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
    protected $is_admin;
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
        $this->is_admin = session()->get("login")->is_admin;
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
        if ($this->is_admin == '1') {

            $condition = [
                "sales_order_export.company_id"    => $this->this_company_id,
                "sales_order_export.deletedAt" => null,
            ];
        } else {
            $condition = [
                "sales_order_export.company_id"    => $this->this_company_id,
                "sales_order_export.deletedAt" => null,
                'sales_order_export.user_id' => $this->this_user_id
            ];
        }



        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status"      => $this->request->getGet("status"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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
                "shipment_date"             => date('d/m/Y', strtotime($data->shipment_date)),
                "createdAt"                 => date('d/m/Y', strtotime($data->createdAt)),
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
        $dataCustomer = $this->customerModel->getCustomerEkspor($this->this_user_id, $this->this_company_id);
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
                'user_id'                     => $this->this_user_id,
                'documents_required'        => $postData['documents_required'],
                'special_instructions' => $postData['special_instructions']
            ];

            // Create a new validation instance
            $dataSalesOrder =  $this->salesOrderExportModel->insert($values);

            // var_dump($items);
            // die();

            $totalQty = 0;
            foreach ($items as $row) {
                $qty_sisa = $row->qty - $row->qtyOrder;
                $valueBarang = [
                    "sales_order_export_id"         => $dataSalesOrder,
                    "barang_id"                     => $row->barang_master_sales_id,
                    "barang_name"                   => $row->barang_name,
                    "barang_kode"                   => $row->kode_barang,
                    "sales_contract_detail_id"                   => $row->id_detail,
                    "qty"                           => isset($row->qtyOrder) ? number_format($row->qtyOrder, 2, '.', '') : number_format($row->qty, 2, '.', ''),
                    "qty_awal"                           => number_format($row->qty_awal, 2, '.', ''),
                    "qty_sisa"                           => number_format($qty_sisa, 2, '.', ''),
                    "satuan_id"                     => $row->satuan_order_id,
                    "remark"                        => $row->remark,
                    "kemasan"                       => $row->kemasan,
                    "harga_barang"                  => isset($row->hargaOrder) ? number_format($row->hargaOrder, 2, '.', '') : number_format($row->harga, 2, '.', ''),
                    "total_harga_barang"            => isset($row->totalHargaOrder) ? number_format($row->totalHargaOrder, 2, '.', '') : number_format($row->total, 2, '.', ''),
                    "tipe_input"            => "order_form",
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
        $dataSalesKontrak = $this->salesKontrakModel->getSalesKontrakForOrderForm($this->this_user_id, '1', $this->this_company_id);

        foreach ($dataSalesKontrak as $value) {
            $totalQtyDetail = 0;
            $dataDetailExport = $this->salesOrderExportDetailModel
                ->select('sales_order_export.*, sales_order_detail_export.*, SUM(sales_order_detail_export.qty) AS qtyOrder')
                ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
                ->where('sales_order_detail_export.sales_contract_detail_id', $value['idContractDetail'])
                ->where('sales_order_export.status', "POSTED")
                ->groupBy('sales_order_export.sales_order_export_id')
                ->findAll();

            if (count($dataDetailExport) > 0) {
                foreach ($dataDetailExport as $valueExportDetail) {
                    $totalQtyDetail += $valueExportDetail['qtyOrder'];
                }
                if ($value['qtyContract'] > $totalQtyDetail) {
                    $dataSalesKontrakFilter[] = $value;
                }
            } else {
                $dataSalesKontrakFilter[] = $value;
            }
        }

        // Hapus duplikat dari $dataSalesKontrakFilter
        $temp_array = [];
        $key_array = [];

        foreach ($dataSalesKontrakFilter as $val) {
            if (!in_array($val['id'], $key_array)) {
                $key_array[] = $val['id'];
                $temp_array[] = $val;
            }
        }

        for ($i = 0; $i < count($temp_array); $i++) {
            $temp_array[$i]['due_date'] = date('d/m/Y', strtotime($temp_array[$i]['due_date']));
            $temp_array[$i]['shipment_date'] = date('d/m/Y', strtotime($temp_array[$i]['shipment_date']));
        }
        return response()->setJSON([
            'data' => $temp_array,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getDetailSalesKontrak()
    {
        $dataSalesKontrakFilter = [];
        $id = $this->request->getGet("id");
        if (empty($id)) {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
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
                    $value['qty_awal'] = $value['qty'];
                    $value['qty'] = $value['qty'] - $totalQtyDetail;
                    $dataSalesKontrakFilter[] = $value; // Tambahkan ke array jika kondisi terpenuhi
                }
            } else {
                $value['qty_awal'] = $value['qty'];
                $dataSalesKontrakFilter[] = $value; // Tambahkan ke array jika tidak ada hasil query
            }
        }
        return response()->setJSON([
            'data' => $dataSalesKontrakFilter,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getDetailInfoSalesKontrak()
    {
        $id = $this->request->getGet("id");
        $selectQry = "
            sales_contract.*,
            customers.name as customer_name,
            metadata.value as valuta,
            metadata.description as valuta_description
        ";
        $dataSalesKontak = $this->salesKontrakModel->select($selectQry)
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency')
            ->where('sales_contract.id', $id)
            ->first();

        if ($dataSalesKontak != null) {
            $dataSalesKontak['potongan_harga'] = floatval($dataSalesKontak['potongan_harga']);
            $dataSalesKontak['shipment_date'] = $dataSalesKontak['shipment_date'] != null ? date('d/m/Y', strtotime($dataSalesKontak['shipment_date'])) : "";
            $dataSalesKontak['due_date'] = $dataSalesKontak['due_date'] != null ? date('d/m/Y', strtotime($dataSalesKontak['due_date'])) : "";

            return response()->setJSON([
                'data' => $dataSalesKontak,
                'token' => csrf_hash(),
                'status' => true
            ]);
        } else {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function generateNomorSalesOrderInternasional()
    {
        $salesContractId = $this->request->getVar('sales_contract_id');

        if (empty($salesContractId)) {
            return response()->setJSON([
                'data' => '',
                'token' => csrf_hash(),
                'status' => true
            ]);
        }

        // Ambil semua nomor yang sudah ada untuk sales contract tertentu
        $existingNumbers = $this->salesOrderExportModel
            ->where('sales_contract_id', $salesContractId)
            ->where('deletedAt', null)
            ->orderBy('sales_order_export_no', 'ASC')
            ->findAll();

        // Simpan daftar nomor yang sudah ada dalam bentuk angka
        $usedNumbers = [];

        foreach ($existingNumbers as $row) {
            $usedNumbers[] = (int) $row['sales_order_export_no'];
        }

        // Cari nomor yang hilang
        $nextNumber = 1;
        for ($i = 1; $i <= count($usedNumbers) + 1; $i++) {
            if (!in_array($i, $usedNumbers)) {
                $nextNumber = $i;
                break;
            }
        }

        // Format nomor menjadi 4 digit dengan leading zero
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $salesContract = $this->salesKontrakModel->where('id', $salesContractId)->first();
        $number = $salesContract['sales_contract_no'] . " - " . $formattedNumber;

        return response()->setJSON([
            'data' => $number,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }


    public function getById($id = null)
    {
        $id = decrypt($id);
        $dataCustomer = $this->customerModel->getCustomerEkspor($this->this_user_id, $this->this_company_id);
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        $dataSalesExport = $this->salesOrderExportModel->asObject()
            ->select('sales_order_export.*, sales_contract.sales_contract_no,sales_contract.due_date,sales_contract.shipment_date, customers.name AS customer_name, CONCAT(metadata.value, " - ", metadata.description) AS currencyName')
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
            ->where('tipe_input', "order_form")
            ->orderBy('createdAt', "DESC")
            ->findAll();

        foreach ($dataSalesExportDetail as $row) {

            $dataDetailExport = $this->salesOrderExportDetailModel
                ->select('SUM(sales_order_detail_export.qty) AS qtyOrder')
                ->join('sales_order_export', 'sales_order_export.sales_order_export_id = sales_order_detail_export.sales_order_export_id', 'left')
                ->where('sales_order_detail_export.sales_contract_detail_id', $row->sales_contract_detail_id)
                ->where('sales_order_export.status', "POSTED")
                ->groupBy('sales_order_export.sales_order_export_id') // Ubah ke sales order export ID
                ->findAll();



            if ($dataDetailExport) {
                $row->qtyContract = $row->qtyContract - $dataDetailExport[0]['qtyOrder'];
            } else {
                $row->qtyContract = $row->qtyContract;
            }
        }

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
        $this->salesOrderExportModel->update($id, [
            'documents_required'        => $postData['documents_required'],
            'special_instructions' => $postData['special_instructions']
        ]);

        $postData["items"] = json_decode($postData["items"], true);

        try {


            $totalQty = 0;
            foreach ($items as $row) {


                $qty_sisa = $row->qty - $row->qtyOrder;

                $valueBarang = [
                    "qty"                           => isset($row->qtyOrder) ? number_format($row->qtyOrder, 2, '.', '') : number_format($row->qty, 2, '.', ''),
                    "qty_sisa"                           =>  number_format($qty_sisa, 2, '.', ''),
                    "harga_barang"                  => isset($row->hargaOrder) ? number_format($row->hargaOrder, 2, '.', '') : number_format($row->harga, 2, '.', ''),
                    "total_harga_barang"            => isset($row->totalHargaOrder) ? number_format($row->totalHargaOrder, 2, '.', '') : number_format($row->total, 2, '.', ''),
                    "remark" => $row->remark,
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

    public function updateRemark()
    {
        try {
            $id = $this->request->getPost("id");
            $remark = $this->request->getPost("remark");


            $payload = [
                "remark" => $remark,
            ];

            $response = $this->salesOrderExportDetailModel->update($id, $payload);

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => "Berhasil Ubah Remark",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = "Gagal Ubah Remark";
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

    public function print($id = null)
    {
        $id = decrypt($id);
        if ($id) {
            $filename = "ORDER FORM";

            $data = [];
            $dataSO = $this->salesOrderExportModel->getById($id);

            if ($dataSO) {
                $dataSODetail = $this->salesOrderExportDetailModel->getSalesOrderExportDetailBySalesOrderExportId($id);

                // var_dump($dataSO);
                // die;

                if ($dataSODetail) {
                    $data["dataSO"] = $dataSO;
                    $data["dataSODetail"] = $dataSODetail;
                }
            }

            // load HTML content
            $this->dompdf->loadHtml(view('SalesInternasional/OrderForm/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'portrait');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Purchase/poImportBahanPenolong/print', $data);
        }
    }

    public function destroy()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->salesOrderExportModel->update($id, [
            'deletedAt' => date('Y-m-d H:i:s')
        ]);
        $this->salesOrderExportDetailModel->where('sales_order_export_id', $id)->delete();
        return response()->setJSON([
            'message' => "Order Form Berhasil Dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }
}
