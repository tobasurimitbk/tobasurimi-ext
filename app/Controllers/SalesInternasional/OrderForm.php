<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CountryModel;
use App\Models\CustomerModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\SalesKontrakDetailModel;
use App\Models\SalesKontrakModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderExportSpecsModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use Dompdf\Dompdf;
use Exception;

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
    protected $divisiModel;
    protected $salesOrderExportSpecsModel;
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
        $this->divisiModel = new DivisisModel();
        $this->salesOrderExportSpecsModel = new SalesOrderExportSpecsModel();
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
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        $dataAJU = $this->metaDataModel->getBCUsed('so_internasional');
        $dataDivisi = $this->divisiModel->getDivisiAccess();

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            "dataAJU" => $dataAJU,
            "dataDivisi" => $dataDivisi
        ];

        return view('SalesInternasional/OrderForm/form', $data);
    }

    public function saveOrder()
    {

        // return \response()->setJSON([
        //     '$_POST' => $_POST,
        //     'listDataSalesKontrak' => \json_decode($_POST['listDataSalesKontrak']),
        //     'listDetailSpecs' => \json_decode($_POST['listDetailSpecs'])
        // ]);

        $salesOrderNo = $this->request->getVar('sales_order_export_no');

        if ($salesOrderNo == "AUTO GENERATE") {
            // JIKA AUTO GENERATE MAKA GENERATE NOMOR BARU
            $salesOrderNo = $this->generateNomorSalesOrderInternasional(
                $this->request->getVar('sales_contract_id')
            );
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $salesOrderExportId = $this->salesOrderExportModel->insert([
                'sales_order_export_no' => $salesOrderNo,
                'sales_contract_id' => $this->request->getVar('sales_contract_id'),
                'company_id' => $this->this_company_id,
                'user_id' => $this->this_user_id,
                'bc_type' => $this->request->getVar('aju_document_type'),
                "tanggal" => $this->request->getVar("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
                'divisi_id' => $this->request->getVar('divisi_id'),
                'tax_id' => $this->request->getVar('tax_id'),
                'royalty_price' => $this->request->getVar('royalty_price'),
                'rebate_price' => $this->request->getVar('rebate_price'),
                'can_deduction_price' => $this->request->getVar('can_deduction_price'),
                'estimated_freight_price' => $this->request->getVar('estimated_freight_price'),
                'others_type' => $this->request->getVar('others_type'),
                'others_price' => $this->request->getVar('others_price'),
                'commision' => $this->request->getVar('commision'),
                'palet_fumigation' => $this->request->getVar('palet_fumigation'),
                'palet_fumigation_price' => $this->request->getVar('palet_fumigation_price'),
                'additional_detail' => $this->request->getVar('additional_detail'),
                'additional_detail_type' => $this->request->getVar('additional_detail_type'),
                'additional_detail_price' => $this->request->getVar('additional_detail_price'),
                'payment_term' => $this->request->getVar('payment_term'),
                'shipment_an' => $this->request->getVar('shipment_an'),
                'consigne_docs' => $this->request->getVar('consigne_docs'),
                'notify_party' => $this->request->getVar('notify_party'),
                'additional_detail_docs' => $this->request->getVar('additional_detail_docs'),
                'product_specs' => $this->request->getVar('product_specs'),
                'processing_method' => $this->request->getVar('processing_method'),
                'packaging' => $this->request->getVar('packaging'),
                'code_stamping' => $this->request->getVar('code_stamping'),
                'loading' => $this->request->getVar('loading'),
                'foto_loading' => $this->request->getVar('foto_loading'),
                'stuffing' => $this->request->getVar('stuffing'),
                'status' => "NEW",
                'used' => "NOT USED",
            ]);

            // Insert Detail Specs
            foreach (json_decode($_POST['listDetailSpecs']) as $l) {
                $this->salesOrderExportSpecsModel->insert([
                    'sales_order_export_id' => $salesOrderExportId,
                    'grade' => $l->grade,
                    'specification' => $l->specification
                ]);
            }

            // Insert Sales Order Detail Export
            $listDataSalesKontrak = json_decode($_POST['listDataSalesKontrak']);

            foreach ($listDataSalesKontrak->salesContractDetailList as $s) {
                foreach ($s->size_breakdown as $sb) {
                    $this->salesOrderExportDetailModel->insert([
                        'sales_order_export_id' => $salesOrderExportId,
                        'sales_contract_size_breakdown_id' => $sb->id_detail_breakdown,
                        'sales_contract_detail_id' => $s->id,
                        'satuan_id' => $sb->satuan_size_id,
                        'qty' => $sb->qty_input,
                        'harga_barang' => $sb->harga,
                        'total_harga_barang' => $sb->total_input
                    ]);
                }
            }

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data saved",
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => "Internal server error " . $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function dropdownSalesKontrak()
    {
        $divisiId = $this->request->getVar('divisi_id');
        $dataSalesKontrak = $this->salesKontrakModel->getSalesKontrakList(
            $divisiId
        );

        return response()->setJSON([
            'data' => $dataSalesKontrak,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getDetailSalesKontrak()
    {
        $salesContractId = $this->request->getVar("sales_contract_id");
        $id = $this->request->getVar('id');

        if (empty($salesContractId)) {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => true
            ]);
        }

        if (empty($id)) {
            $id = null;
        }

        $data = $this->salesOrderExportModel->getDetailSalesKontrakInOrderForm(
            $salesContractId,
            $id
        );

        return response()->setJSON([
            'data' => $data,
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

    private function generateNomorSalesOrderInternasional($salesContractId)
    {
        $existingNumbers = $this->salesOrderExportModel
            ->where('sales_contract_id', $salesContractId)
            ->where('deletedAt', null)
            ->orderBy('sales_order_export_no', 'ASC')
            ->findAll();
        $usedNumbers = [];

        foreach ($existingNumbers as $row) {
            $usedNumbers[] = (int) $row['sales_order_export_no'];
        }
        $nextNumber = 1;
        for ($i = 1; $i <= count($usedNumbers) + 1; $i++) {
            if (!in_array($i, $usedNumbers)) {
                $nextNumber = $i;
                break;
            }
        }

        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $salesContract = $this->salesKontrakModel->where('id', $salesContractId)->first();
        $number = $salesContract['sales_contract_no'] . " - " . $formattedNumber;
        return $number;
    }


    public function getById($id = null)
    {
        $id = decrypt($id);
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );
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
        $id = decrypt($this->request->getPost("id"));

        $salesOrderNo = $this->request->getVar('sales_order_export_no');

        $checkNoSalesOrder = $this->salesOrderExportModel
            ->where('sales_order_export_no', $salesOrderNo)
            ->where('company_id', $this->this_company_id)
            ->where('id !=', $id)
            ->first();

        if ($checkNoSalesOrder != null) {
            // NO Sales Order Already Exists
            return response()->setJSON([
                'message' => "Sales order no already exists",
                'status' => false
            ]);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $this->salesOrderExportModel->update($id, [
                'sales_order_export_no' => $salesOrderNo,
                'sales_contract_id' => $this->request->getVar('sales_contract_id'),
                'company_id' => $this->this_company_id,
                'user_id' => $this->this_user_id,
                'bc_type' => $this->request->getVar('aju_document_type'),
                "tanggal" => $this->request->getVar("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
                'divisi_id' => $this->request->getVar('divisi_id'),
                'tax_id' => $this->request->getVar('tax_id'),
                'royalty_price' => $this->request->getVar('royalty_price'),
                'rebate_price' => $this->request->getVar('rebate_price'),
                'can_deduction_price' => $this->request->getVar('can_deduction_price'),
                'estimated_freight_price' => $this->request->getVar('estimated_freight_price'),
                'others_type' => $this->request->getVar('others_type'),
                'others_price' => $this->request->getVar('others_price'),
                'commision' => $this->request->getVar('commision'),
                'palet_fumigation' => $this->request->getVar('palet_fumigation'),
                'palet_fumigation_price' => $this->request->getVar('palet_fumigation_price'),
                'additional_detail' => $this->request->getVar('additional_detail'),
                'additional_detail_type' => $this->request->getVar('additional_detail_type'),
                'additional_detail_price' => $this->request->getVar('additional_detail_price'),
                'payment_term' => $this->request->getVar('payment_term'),
                'shipment_an' => $this->request->getVar('shipment_an'),
                'consigne_docs' => $this->request->getVar('consigne_docs'),
                'notify_party' => $this->request->getVar('notify_party'),
                'additional_detail_docs' => $this->request->getVar('additional_detail_docs'),
                'product_specs' => $this->request->getVar('product_specs'),
                'processing_method' => $this->request->getVar('processing_method'),
                'packaging' => $this->request->getVar('packaging'),
                'code_stamping' => $this->request->getVar('code_stamping'),
                'loading' => $this->request->getVar('loading'),
                'foto_loading' => $this->request->getVar('foto_loading'),
                'stuffing' => $this->request->getVar('stuffing'),
            ]);

            // Insert Detail Specs
            $idSalesOrderExportSpecsNotDeleted = [];
            foreach (json_decode($_POST['listDetailSpecs']) as $l) {
                $check = $this->salesOrderExportSpecsModel
                    ->where('id', $l->id)
                    ->first();

                if ($check != null) {
                    $this->salesOrderExportSpecsModel->update($check['id'], [
                        'grade' => $l->grade,
                        'specification' => $l->specification
                    ]);

                    array_push($idSalesOrderExportSpecsNotDeleted, $check['id']);
                } else {

                    $idSalesOrderExportSpecs = $this->salesOrderExportSpecsModel->insert([
                        'sales_order_export_id' => $id,
                        'grade' => $l->grade,
                        'specification' => $l->specification
                    ]);

                    array_push($idSalesOrderExportSpecsNotDeleted, $idSalesOrderExportSpecs);
                }
            }

            $this->salesOrderExportSpecsModel->whereNotIn('id', $idSalesOrderExportSpecsNotDeleted)
                ->where('sales_order_export_id', $id)
                ->delete();

            return response()->setJSON([
                'message' => "Data updated",
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => "Internal server error " . $e->getMessage(),
                'status' => false
            ]);
        }
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
