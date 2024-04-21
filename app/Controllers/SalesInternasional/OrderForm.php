<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CountryModel;
use App\Models\CustomerModel;
use App\Models\MetadataModel;
use App\Models\SalesKontrakDetailModel;
use App\Models\SalesKontrakModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use Dompdf\Dompdf;

class OrderForm extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_user_id;
    protected $customerModel;
    protected $salesKontrakModel;
    protected $salesKontrakDetailModel;
    protected $barangMasterModel;
    protected $stokDetailModel;
    protected $countryModel;
    protected $metaDataModel;
    protected $satuanModel;
    protected $barangMasterSalesModel;
    protected $salesOrderExportModel;
    protected $salesOrderExportDetailModel;
    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->customerModel = new CustomerModel();
        $this->salesKontrakModel = new SalesKontrakModel();
        $this->salesKontrakDetailModel = new SalesKontrakDetailModel();
        $this->stokDetailModel = new StockDetailModel();
        $this->countryModel = new CountryModel();
        $this->metaDataModel = new MetadataModel();
        $this->satuanModel = new SatuansModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('SalesInternasional/OrderForm/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "status"      => $this->request->getGet("status")
        ];

        $condition = [
            "sales_order_export.company_id"    => $this->this_company_id,
            "status"      => $this->request->getGet("status"),
            "sales_order_export.deletedAt" => null
        ];
        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status"      => $this->request->getGet("status")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportModel->getList($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => encrypt($data->sales_order_export_id),
                "sales_order_export_no"     => $data->sales_order_export_no,
                "customer_po_no"            => $data->customer_po_no,
                "customer_name"             => $data->customer_name,
                "dicharge_port"             => $data->dicharge_port,
                "shipment_date"             => $data->shipment_date,
                "createdAt"                 => date('Y-m-d', strtotime($data->createdAt)),
                "status"                    => $data->status,
                "used"                      => $data->used,
                "keterangan_unpost"         => $data->keterangan_unpost,
                "jumlah_unpost"             => $data->jumlah_unpost,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesData['totalData'],
            "recordsFiltered"   => $salesData['totalFilteredData'],
            "data"              => $dataSales,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function createView()
    {
        $dataCustomer = $this->customerModel->getCustomerEkspor($this->this_user_id);
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        $dataAJU = $this->metaDataModel->getBCUsed('so_internasional');

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            "dataAJU" => $dataAJU,
        ];

        return view('SalesInternasional/OrderForm/form', $data);
    }

    public function saveOrder()
    {
        $items = json_decode($this->request->getVar("items"));

        $postData = $this->request->getPost();
        $postData["items"] = json_decode($postData["items"], true);

        $rules = [
            "sales_kontrak" => [
                "rules" => "required|is_natural_no_zero",
                'errors' => [
                    'required' => 'Sales Kontrak tidak boleh kosong',
                ]
            ],
            "items" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Barang tidak boleh kosong',
                ],
            ],
        ];

        if (!$this->validateData($postData, $rules)) {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        }

        try {
            $this->salesOrderExportModel->db->transException(true)->transStart();

            $values = [
                "sales_order_export_no"        => $postData['no_sales_order'],
                "sales_contract_id"           => $postData['sales_kontrak'],
                "bc_type"                     => $postData['aju_document_type'],
                "company_id"                  => $this->this_company_id,
                "status"                      => "NEW",
                "used"                        => "NOT USED",
            ];

            // Create a new validation instance
            $dataSalesOrder =  $this->salesOrderExportModel->insert($values);

            $totalQty = 0;
            foreach ($items as $row) {
                $valueBarang = [
                    "sales_order_export_id"         => $dataSalesOrder,
                    "barang_id"                     => $row->barang_master_sales_id,
                    "barang_name"                   => $row->barang_name,
                    "barang_kode"                   => $row->kode_barang,
                    "sales_contract_detail_id"                   => $row->id_detail,
                    "qty"                           => isset($row->qtyOrder) ? number_format($row->qtyOrder, 2, '.', '') : number_format($row->qty, 2, '.', ''),
                    "satuan_id"                     => $row->satuan_order_id,
                    "remark"                        => $row->remark,
                    "kemasan"                       => $row->kemasan,
                    "harga_barang"                  => isset($row->hargaOrder) ? number_format($row->hargaOrder, 2, '.', '') : number_format($row->harga, 2, '.', ''),
                    "total_harga_barang"            => isset($row->totalHargaOrder) ? number_format($row->totalHargaOrder, 2, '.', '') : number_format($row->total, 2, '.', ''),
                ];
                $this->salesOrderExportDetailModel->save($valueBarang);
            }

            $this->salesOrderExportModel->db->transComplete();

            $data = [
                "id"        => encrypt($dataSalesOrder),
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                // "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function dropdownSalesKontrak()
    {
        $dataSalesKontrakFilter = [];
        $dataSalesKontrak = $this->salesKontrakModel->getSalesKontrakForOrderForm($this->this_user_id, '1');
        foreach ($dataSalesKontrak as $value) {
            $totalQtyDetail = 0;
            $dataDetailExport = $this->salesOrderExportDetailModel
                ->select('sales_order_export.*, sales_order_detail_export.*, SUM(sales_order_detail_export.qty) AS qtyOrder')
                ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
                ->where('sales_order_detail_export.sales_contract_detail_id', $value['idContractDetail'])
                ->where('sales_order_export.status', "POSTED")
                ->groupBy('sales_order_export.sales_order_export_id') // Ubah ke sales order export ID
                ->findAll();
            if (count($dataDetailExport) > 0) { // Periksa apakah ada hasil query
                foreach ($dataDetailExport as $valueExportDetail) {
                    $totalQtyDetail += $valueExportDetail['qtyOrder'];
                }
                if ($value['qtyContract'] > $totalQtyDetail) {
                    $dataSalesKontrakFilter[] = $value; // Tambahkan ke array jika kondisi terpenuhi
                }
            } else {
                $dataSalesKontrakFilter[] = $value; // Tambahkan ke array jika tidak ada hasil query
            }
        }
        return response()->setJSON([
            'data' => $dataSalesKontrakFilter,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }


    public function getDetailSalesKontrak()
    {
        $dataSalesKontrakFilter = [];
        $id = $this->request->getGet("id");
        // $dataSalesKontrak = $this->salesKontrakModel->getSalesKontrak($this->this_user_id);
        $dataSalesKontrakDetail = $this->salesKontrakDetailModel->detail($id);
        // var_dump($id);
        // var_dump($dataSalesKontrakDetail);
        foreach ($dataSalesKontrakDetail as $value) {
            $totalQtyDetail = 0;
            $dataDetailExport = $this->salesOrderExportDetailModel
                ->select('sales_order_export.*, sales_order_detail_export.*, SUM(sales_order_detail_export.qty) AS qtyOrder')
                ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
                ->where('sales_order_detail_export.sales_contract_detail_id', $value['id_detail'])
                ->where('sales_order_export.status', "POSTED")
                ->groupBy('sales_order_export.sales_order_export_id') // Ubah ke sales order export ID
                ->findAll();
            if (count($dataDetailExport) > 0) { // Periksa apakah ada hasil query
                foreach ($dataDetailExport as $valueExportDetail) {
                    $totalQtyDetail += $valueExportDetail['qtyOrder'];
                }
                if ($value['qty'] > $totalQtyDetail) {
                    $value['qty'] = $value['qty'] - $totalQtyDetail;
                    $dataSalesKontrakFilter[] = $value; // Tambahkan ke array jika kondisi terpenuhi
                }
            } else {
                $dataSalesKontrakFilter[] = $value; // Tambahkan ke array jika tidak ada hasil query
            }
        }
        return response()->setJSON([
            'data' => $dataSalesKontrakFilter,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function generateNomorSalesOrderInternasional()
    {
        $code = "SI";
        $currentYear = date('Y');
        $currentMonth = date('m');
        $numberTemplate = $code . "/" . $currentMonth . "/" . $currentYear . "/";
        $lastData = $this->salesOrderExportModel->asObject()
            ->like('sales_order_export_no', $numberTemplate)
            ->orderBy('createdAt', 'DESC')
            ->first();

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->sales_order_export_no);
            $lastIncrement = intval($asd[3]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $numberTemplate . $paddedNumber;
        } else {
            $invNumber = $numberTemplate . '001';
        }

        return response()->setJSON([
            'data' => $invNumber,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getById($id = null)
    {
        $id = decrypt($id);
        $dataCustomer = $this->customerModel->getCustomerEkspor($this->this_user_id);
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        $dataSalesExport = $this->salesOrderExportModel->asObject()
            ->select('sales_order_export.*, sales_contract.*, customers.name AS customer_name, CONCAT(metadata.value, " - ", metadata.description) AS currencyName')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
            ->where('sales_order_export.sales_order_export_id', $id)
            ->orderBy('sales_order_export.createdAt', "DESC")
            ->first();
        $dataSalesExportDetail = $this->salesOrderExportDetailModel->asObject()
            ->select('sales_order_detail_export.*, satuans.kode_satuan, sales_contract_detail.qty as qtyContract, sales_contract_detail.harga as hargaContract, sales_contract_detail.total_harga as totalHargaContract')
            ->join('satuans', 'satuans.id = sales_order_detail_export.satuan_id', 'left')
            ->join('sales_contract_detail', 'sales_contract_detail.id = sales_order_detail_export.sales_contract_detail_id', 'left')
            ->where('sales_order_export_id', $id)
            ->orderBy('createdAt', "DESC")
            ->findAll();
        $dataAJU = $this->metaDataModel->getBCUsed('so_internasional');

        $data = [
            "id" => encrypt($id),
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            'dataSalesExport' => $dataSalesExport,
            'dataSalesExportDetail' => $dataSalesExportDetail,
            "dataAJU" => $dataAJU,
        ];

        return view('SalesInternasional/OrderForm/form', $data);
    }

    public function update()
    {
        $items = json_decode($this->request->getVar("items"));
        $id = decrypt($this->request->getPost("id"));

        $postData = $this->request->getPost();
        $postData["items"] = json_decode($postData["items"], true);

        try {

            $totalQty = 0;
            foreach ($items as $row) {
                $valueBarang = [
                    "qty"                           => isset($row->qtyOrder) ? number_format($row->qtyOrder, 2, '.', '') : number_format($row->qty, 2, '.', ''),
                    "harga_barang"                  => isset($row->hargaOrder) ? number_format($row->hargaOrder, 2, '.', '') : number_format($row->harga, 2, '.', ''),
                    "total_harga_barang"            => isset($row->totalHargaOrder) ? number_format($row->totalHargaOrder, 2, '.', '') : number_format($row->total, 2, '.', ''),
                ];
                $this->salesOrderExportDetailModel->update($row->id_detail_sales_order, $valueBarang);
            }

            $this->salesOrderExportModel->db->transComplete();

            $data = [
                "id"        => encrypt($id),
                "status"    => true,
                "message"   => "Data Berhasil diperbaharui",
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                // "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function updateStatus()
    {
        try {
            $id = decrypt($this->request->getPost("id"));
            $status = $this->request->getPost("status");
            $keterangan = $this->request->getPost("keterangan");

            $checkUnpost = $this->salesOrderExportModel->find($id);

            $jmlh = (float) $checkUnpost['jumlah_unpost'];

            $payload = [
                "status" => $status,
                "keterangan_unpost" => $keterangan,
                "jumlah_unpost" => $status == "NEW" ? $jmlh + 1 : $jmlh,
            ];

            $response = $this->salesOrderExportModel->update($id, $payload);

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => $status === "POSTED" ? "Data Berhasil diposting" : "Data Berhasil diunposting",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = $status === "POSTED" ? "Data Gagal diposting" : "Data Gagal diunposting";
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    // public function print($id = null)
    // {
    //     if ($id) {
    //         $filename = "ORDER FORM";

    //         $data = [];
    //         $dataSO = $this->salesOrderExportModel->getById($id);

    //         if ($dataSO) {
    //             $dataSODetail = $this->salesOrderExportDetailModel->getSalesOrderExportDetailBySalesOrderExportId($id);

    //             // var_dump($dataSO);
    //             // die;

    //             if ($dataSODetail) {
    //                 $data["dataSO"] = $dataSO;
    //                 $data["dataSODetail"] = $dataSODetail;
    //             }
    //         }

    //         // load HTML content
    //         $this->dompdf->loadHtml(view('SalesInternasional/OrderForm/print', $data));

    //         // (optional) setup the paper size and orientation
    //         $this->dompdf->setPaper('A4', 'portrait');

    //         // render html as PDF
    //         $this->dompdf->render();

    //         // output the generated pdf
    //         $this->dompdf->stream($filename, array("Attachment" => false));

    //         exit(0);

    //         // return view('Purchase/poImportBahanPenolong/print', $data);
    //     }
    // }
}
