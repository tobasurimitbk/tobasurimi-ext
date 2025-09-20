<?php

namespace App\Controllers\Pembayaran;

use Illuminate\Support\Collection;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Models\PayrollsModel;
use App\Models\EmployeesModel;
use App\Models\DivisisModel;
use App\Models\GolonganModel;
use App\Models\BagianModel;
use App\Models\SupplierModel;
use App\Models\Sub_AkunsModel;
use App\Models\PembayaranInvoiceModel;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderInvoiceDetailModel;
use App\Models\SalesOrderLainModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\ProformaInvoiceBarangModel;
use App\Models\ProformaInvoiceModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportAdditionalModel;
use App\Models\MetadataModel;
use App\Models\PembayaranInvoiceDetailModel;
use App\Models\SalesOrderReturnDetailModel;
use App\Models\SalesOrderReturnModel;
use DateTime;
use Exception;

class PembayaranInvoice extends BaseController
{
    protected $token;
    protected $this_company_id, $user_id;
    protected $is_admin;
    protected $divisiModel;
    protected $supplierList;
    protected $Sub_AkunsModel;
    protected $pembayaranInvoiceModel;
    protected $customerModel;
    protected $salesOrderLainModel;
    protected $salesOrderLainDetailModel;
    protected $salesOrderExportModel;
    protected $salesOrderExportDetailModel;
    protected $salesOrderExportAdditionalModel;
    protected $salesOrderInvoiceModel;
    protected $salesOrderInvoiceDetailModel;
    protected $salesOrderReturnModel;
    protected $salesOrderReturnDetailModel;
    protected $proformaInvoiceModel;
    protected $proformaInvoiceBarangModel;
    protected $pembayaranInvoiceDetailModel;
    protected $metaDataModel;
    protected $jurnalController;


    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->user_id = session()->get('login')->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->divisiModel = new DivisisModel();
        $this->supplierList = new SupplierModel();
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->pembayaranInvoiceModel = new PembayaranInvoiceModel();
        $this->pembayaranInvoiceDetailModel = new PembayaranInvoiceDetailModel();
        $this->customerModel = new CustomerModel();
        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->salesOrderExportAdditionalModel = new SalesOrderExportAdditionalModel();
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $this->proformaInvoiceModel = new ProformaInvoiceModel();
        $this->proformaInvoiceBarangModel = new ProformaInvoiceBarangModel();
        $this->salesOrderInvoiceDetailModel = new SalesOrderInvoiceDetailModel();
        $this->salesOrderReturnModel = new SalesOrderReturnModel();
        $this->salesOrderReturnDetailModel = new SalesOrderReturnDetailModel();
        $this->metaDataModel = new MetadataModel();
        $this->jurnalController = new JurnalUmum();
    }

    public function index()
    {
        return view('Pembayaran/pembayaranInvoice/index');
    }

    public function createPembayaranInvoiceEkspor()
    {
        $subAkunsModel = $this->Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $dokumenList = [];
        $customers = $this->customerModel->getCustomerLokal($this->user_id, $this->this_company_id);
        $divisi = $this->divisiModel->getDivisiAccess();
        $salesOrderExportData = $this->salesOrderExportModel
            ->select('sales_order_export.*, SUM(sales_order_detail_export.total_harga_barang) AS total_invoice')
            ->join('sales_order_detail_export', 'sales_order_detail_export.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
            ->where('sales_order_export.deletedAt', null)
            ->where('sales_order_detail_export.deletedAt', null)
            ->where('sales_order_export.status', "POSTED")
            ->where('sales_order_export.company_id', $this->this_company_id)
            ->groupBy('sales_order_detail_export.sales_order_export_id')
            ->findAll();

        foreach ($salesOrderExportData as $s) {
            $totalPembayaran = 0;
            $pembayaranInvoiceData = $this->pembayaranInvoiceModel
                ->where('pembayaran_invoice.company_id', $this->this_company_id)
                ->where('pembayaran_invoice.invoice_id', $s['sales_order_export_id'])
                ->where('pembayaran_invoice.deletedAt', null)
                ->where('pembayaran_invoice.type_invoice', "EKSPOR")
                ->findAll();
            foreach ($pembayaranInvoiceData as $ss) {
                $totalPembayaran += $ss['total_bayar'];
            }
            if (floatval($s['total_invoice']) > floatval($totalPembayaran)) {
                array_push($dokumenList, $s);
            }
        }
        $data = [
            "customers" => $customers,
            "divisi" => $divisi,
            "subsAkuns" => $subAkunsModel,
            "dokumenList" => $dokumenList,
            "detail" => "",
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Pembayaran/pembayaranInvoice/formEkspor', $data);
    }

    public function createPembayaranInvoiceProformaInvoice()
    {
        $subAkunsModel = $this->Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $dokumenList = [];
        $customers = $this->customerModel->getCustomerLokal($this->user_id, $this->this_company_id);
        $divisi = $this->divisiModel->getDivisiAccess();

        $proformaInvoiceData = $this->proformaInvoiceModel
            ->select('proforma_invoice.*, SUM(proforma_invoice_barang.total_harga) AS total_invoice')
            ->join('proforma_invoice_barang', 'proforma_invoice_barang.proforma_invoice_id = proforma_invoice.id', 'left')
            ->where('proforma_invoice.deletedAt', null)
            ->where('proforma_invoice_barang.deletedAt', null)
            ->where('proforma_invoice.status_posting', 0)
            ->where('proforma_invoice.company_id', $this->this_company_id)
            ->groupBy('proforma_invoice_barang.proforma_invoice_id')
            ->findAll();
            

        foreach ($proformaInvoiceData as $s) {
            $totalPembayaran = 0;
            $pembayaranInvoiceData = $this->pembayaranInvoiceModel
                ->where('pembayaran_invoice.company_id', $this->this_company_id)
                ->where('pembayaran_invoice.invoice_id', $s['id'])
                ->where('pembayaran_invoice.deletedAt', null)
                ->where('pembayaran_invoice.type_invoice', "PROFORMA-INVOICE")
                ->findAll();
            foreach ($pembayaranInvoiceData as $ss) {
                $totalPembayaran += $ss['total_bayar'];
            }
            if (floatval($s['total_pi']) > floatval($totalPembayaran)) {
                array_push($dokumenList, $s);
            }
        }

        $data = [
            "customers" => $customers,
            "divisi" => $divisi,
            "subsAkuns" => $subAkunsModel,
            "dokumenList" => $dokumenList,
            "detail" => "",
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Pembayaran/pembayaranInvoice/formPI', $data);
    }

    public function createPembayaranInvoiceLain()
    {
        $subAkunsModel = $this->Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $dokumenList = [];
        $customers = $this->customerModel->getCustomerLokal($this->user_id, $this->this_company_id);
        $divisi = $this->divisiModel->getDivisiAccess();
        $salesOrderLainData = $this->salesOrderLainModel
            ->select('sales_order_lain.*, SUM(sales_order_lain_detail.total_harga) AS total_invoice')
            ->join('sales_order_lain_detail', 'sales_order_lain_detail.sales_order_lain_id = sales_order_lain.id', 'left')
            ->where('sales_order_lain.deletedAt', null)
            ->where('sales_order_lain_detail.deletedAt', null)
            ->where('sales_order_lain.company_id', $this->this_company_id)
            ->groupBy('sales_order_lain_detail.sales_order_lain_id')
            ->findAll();
        foreach ($salesOrderLainData as $s) {
            $totalPembayaran = 0;
            $pembayaranInvoiceData = $this->pembayaranInvoiceModel
                ->where('pembayaran_invoice.company_id', $this->this_company_id)
                ->where('pembayaran_invoice.invoice_id', $s['id'])
                ->where('pembayaran_invoice.deletedAt', null)
                ->where('pembayaran_invoice.type_invoice', "LAIN-LAIN")
                ->findAll();
            foreach ($pembayaranInvoiceData as $ss) {
                $totalPembayaran += floatval($ss['total_bayar']);
            }
            // var_dump(floatval($s['total_invoice']));
            // var_dump(floatval($totalPembayaran));
            if (floatval($s['total_invoice']) > floatval($totalPembayaran)) {
                array_push($dokumenList, $s);
            }
        }
        // exit;

        $data = [
            "customers" => $customers,
            "divisi" => $divisi,
            "subsAkuns" => $subAkunsModel,
            "dokumenList" => $dokumenList,
            "detail" => ""
        ];
        return view('Pembayaran/pembayaranInvoice/formLain', $data);
    }

    public function createPembayaranInvoiceReturn()
    {
        $subAkunsModel = $this->Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $customers = $this->customerModel->getCustomerLokal($this->user_id, $this->this_company_id);
        $divisi = $this->divisiModel->getDivisiAccess();

        $data = [
            "customers" => $customers,
            "divisi" => $divisi,
            "subsAkuns" => $subAkunsModel,
            "detail" => ""
        ];
        return view('Pembayaran/pembayaranInvoice/formReturn', $data);
    }

    public function dropdownInvoiceReturn()
    {
        $customerId = $this->request->getVar('customer_id');

        if (empty($customerId)) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => []
            ]);
        }

        $selectQry = "
            sales_order_return.*,
            sales_order_return_detail.id as sales_order_return_detail_id,
            sales_order_return_detail.amount_return
        ";

        $salesOrderInvoiceReturn = $this->salesOrderInvoiceModel->select($selectQry)
            ->join('sales_order_return', 'sales_order_return.id_invoice = sales_order_invoice.id', 'left')
            ->join('sales_order_return_detail', 'sales_order_return_detail.id_sales_order_return = sales_order_return.id', 'left')
            ->where('sales_order_invoice.id_customer', $customerId)
            ->where('sales_order_return.deletedAt', null)
            ->where('sales_order_return_detail.deletedAt', null)
            ->where('sales_order_return.is_approved', 1)
            ->findAll();


        $invoiceReturnId = [];
        foreach ($salesOrderInvoiceReturn as $s) {
            $selectQry = "
                SUM(harga_total) as total_invoice_dibayar
            ";

            $pembayaranInvoiceData = $this->pembayaranInvoiceDetailModel
                ->select($selectQry)
                ->where('sales_order_invoice_id', $s['id'])
                ->where('sales_order_invoice_detail_id', $s['sales_order_return_detail_id'])
                ->groupBy('sales_order_invoice_id')
                ->findAll();


            if (count($pembayaranInvoiceData) > 0) {
                if ($s['amount_return'] > $pembayaranInvoiceData[0]['total_invoice_dibayar']) {
                    array_push($invoiceReturnId, $s['id']);
                }
            } else {
                array_push($invoiceReturnId, $s['id']);
            }
        }

        if (count($invoiceReturnId) > 0) {
            $salesOrderInvoiceReturn = $this->salesOrderReturnModel->whereIn('id', $invoiceReturnId)->findAll();
        } else {
            $salesOrderInvoiceReturn = [];
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $salesOrderInvoiceReturn
        ]);
    }

    public function createPembayaranInvoiceLokal()
    {
        $subAkunsModel = $this->Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $dokumenList = [];
        $customers = $this->customerModel
            ->select('customers.id, customers.name') // Pilih kolom yang diperlukan
            ->join('sales_order_invoice', 'sales_order_invoice.id_customer = customers.id', 'left') // Perbaiki kondisi join
            ->where('sales_order_invoice.deletedAt', null)
            ->where('sales_order_invoice.status_pelunasan', 'UNPAID')
            ->where('customers.company_id', $this->this_company_id)
            ->where('customers.deletedAt', null)
            ->groupBy('customers.id')
            ->findAll();

        $divisi = $this->divisiModel->getDivisiAccess();
        $salesOrderLokalInvoiceData = $this->salesOrderInvoiceModel->where('deletedAt', null)->where('id_company', $this->this_company_id)->findAll();
        foreach ($salesOrderLokalInvoiceData as $s) {
            $totalPembayaran = 0;
            $pembayaranInvoiceData = $this->pembayaranInvoiceModel
                ->where('pembayaran_invoice.company_id', $this->this_company_id)
                ->where('pembayaran_invoice.invoice_id', $s['id'])
                ->where('pembayaran_invoice.deletedAt', null)
                ->where('pembayaran_invoice.type_invoice', "LOKAL")
                ->findAll();
            foreach ($pembayaranInvoiceData as $ss) {
                $totalPembayaran += $ss['total_bayar'];
            }
            if (floatval($s['total_invoice']) > floatval($totalPembayaran)) {
                array_push($dokumenList, $s);
            }
        }

        $data = [
            "customers" => $customers,
            "divisi" => $divisi,
            "subsAkuns" => $subAkunsModel,
            "dokumenList" => $dokumenList,
        ];
        return view('Pembayaran/pembayaranInvoice/formLokal', $data);
    }

    public function getDataDokumenInvoiceLokal($customer_id, $pembayaran_invoice_id = null)
    {
        if ($customer_id == "import") {
            $query = $this->salesOrderInvoiceModel
                ->select('sales_order_invoice.no_faktur, sales_order_invoice.id') // Pilih kolom yang dibutuhkan
                ->where('sales_order_invoice.document_type', "import");
        } else {
            $customer_id_decrypt = decrypt($customer_id);
            $pembayaran_invoice_id_decrypt = $this->request->getVar('pembayaran_invoice_id') ? decrypt($this->request->getVar('pembayaran_invoice_id')) : null;
            $query = $this->salesOrderInvoiceModel
                ->select('sales_order_invoice.no_faktur, sales_order_invoice.id') // Pilih kolom yang dibutuhkan
                ->join('pembayaran_invoice_detail', 'pembayaran_invoice_detail.sales_order_invoice_id = sales_order_invoice.id', 'left') // Relasi ke pembayaran_invoice_detail
                ->where('sales_order_invoice.id_customer', $customer_id_decrypt)
                ->where('sales_order_invoice.deletedAt', null);
                

            // Jika dalam mode edit
            if ($pembayaran_invoice_id_decrypt) {
                // Tampilkan semua data, termasuk yang terkait dengan 
              
                   
                $query->where('pembayaran_invoice_detail.type_invoice', 'LOKAL')
                    ->where('pembayaran_invoice_detail.deletedAt', null)
                    ->where('pembayaran_invoice_detail.pembayaran_invoice_id', $pembayaran_invoice_id_decrypt);
            } else {
                // Non-edit mode: hanya data yang belum dibayar
                $query->where('pembayaran_invoice_detail.deletedAt', null)
                    ->where('pembayaran_invoice_detail.id', null);
            }
        }

        $salesOrderLokalInvoiceData = $query->findAll();

        return response()->setJSON([
            'data' => $salesOrderLokalInvoiceData,
            'token' => csrf_hash(),
            'success' => true,
        ]);
    }

    public function getDataDokumenInvoiceReturn($customer_id, $pembayaran_invoice_id = null)
    {
        $customer_id_decrypt = decrypt($customer_id);
        $pembayaran_invoice_id_decrypt = $this->request->getVar('pembayaran_invoice_id') ? decrypt($this->request->getVar('pembayaran_invoice_id')) : null;

        $this->salesOrderReturnModel
            ->select('sales_order_return.no_return, sales_order_return.id') // Pilih kolom yang dibutuhkan
            ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice', 'inner') // Relasi ke invoice
            ->join('pembayaran_invoice_detail', 'pembayaran_invoice_detail.sales_order_invoice_id = sales_order_return.id', 'left') // Relasi ke pembayaran_invoice_detail
            ->where('sales_order_return.id_company', $this->this_company_id) // Perusahaan yang relevan
            ->where('sales_order_invoice.id_customer', $customer_id_decrypt) // Filter berdasarkan customer
            ->where('sales_order_return.deletedAt', null); // Hanya return yang aktif

        // Jika dalam mode edit
        if ($pembayaran_invoice_id_decrypt) {
            // Tampilkan semua data, termasuk yang terkait dengan pembayaran_invoice_id
            $this->salesOrderReturnModel->groupStart()
                ->where('pembayaran_invoice_detail.id', null)
                ->where('pembayaran_invoice_detail.type_invoice', 'RETURN')
                ->orWhere('pembayaran_invoice_detail.pembayaran_invoice_id', $pembayaran_invoice_id_decrypt)
                ->groupEnd();
        } else {
            // Non-edit mode: hanya data yang belum dibayar
            $this->salesOrderReturnModel->where('pembayaran_invoice_detail.id', null);
        }

        $salesOrderReturnData = $this->salesOrderReturnModel->findAll();

        return response()->setJSON([
            'data' => $salesOrderReturnData,
            'token' => csrf_hash(),
            'success' => true,
        ]);
    }


    public function generateNoPembayaranInvoice()
    {
        $paymentNo = "BNL/";
        $month = date('m');
        $year = date('y');

        $numberTemplate = $paymentNo . "$year/$month/";
        $lastData = $this->pembayaranInvoiceModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->like('no_pembayaran', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        // default mulai dari 0001
        $paymentNo = "{$numberTemplate}0001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->no_pembayaran);
            $lastIncrement = (int)$exploded[3] + 1;

            // ubah jadi 4 digit
            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $paymentNo = $numberTemplate . $paddedNumber;
        }

        return response()->setJSON([
            'paymentNo' => $paymentNo,
            'token' => csrf_hash(),
            'success' => true,
        ]);
    }

    public function getAllPembayaranInvoice()
    {

        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),

        ];

        if ($this->is_admin == '1') {
            $condition = [
                'pembayaran_invoice.company_id' => $this->this_company_id,
                "pembayaran_invoice.deletedAt" => null,
            ];
        } else {
            $condition = [
                'pembayaran_invoice.company_id' => $this->this_company_id,
                "pembayaran_invoice.deletedAt" => null,
                "pembayaran_invoice.user_id" => $this->user_id, // kecualikan admin yg akses
            ];
        }

        $typeInvoice = [];
        $type = $this->request->getGet('type_invoice');
        if ($type == "ALL") {
            $typeInvoice = ['EKSPOR', 'LOKAL', 'LAIN-LAIN', 'RETURN', 'PROFORMA INVOICE'];
        } else {
            $typeInvoice = [$type];
        }

        $addCondition = [
            "search"    => $this->request->getVar("search"),
            "sort"      => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "dateStart" =>  $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd" =>  $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "type_invoice" => $typeInvoice,
            "status_posting" => $this->request->getVar('status_posting'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $pembayaranInvoiceData = $this->pembayaranInvoiceModel->getList($addCondition, $condition, $limit, $offset);
        $dataPembayaran = [];


        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;



        foreach ($pembayaranInvoiceData['data'] as $p) {
            $nomor_invoice = "";
            $customer_name = "";
            if ($p['type_invoice'] == "LOKAL") {
                // Ambil array invoice_id
                $invoiceIds = is_array($p['invoice_id']) ? $p['invoice_id'] : explode(',', $p['invoice_id']);  // Mengonversi ke array jika dalam format string yang dipisah koma

                // Query untuk mengambil data berdasarkan array invoice_id
                $salesOrderLokalInvoiceData = $this->salesOrderInvoiceModel
                    ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                    ->whereIn('sales_order_invoice.id', $invoiceIds)  // Menggunakan whereIn untuk memilih beberapa ID
                    ->findAll();  // Mengambil semua data yang cocok

                // Ambil nomor faktur dan nama pelanggan dan gabungkan dengan koma
                $nomor_invoice = implode(', ', array_map(function ($item) {
                    return $item['no_faktur'];  // Mengambil no_faktur dari setiap hasil query
                }, $salesOrderLokalInvoiceData));

                $customerName = $this->customerModel->where('id', $p['customer_id'])->select('name')->first();
                if (empty($customerName)) {
                    $customer_name = implode(', ', array_map(function ($item) {
                        return $item['name'];  // Mengambil name dari setiap hasil query
                    }, $salesOrderLokalInvoiceData));
                } else {
                    $customer_name = $customerName['name'];
                }
            } elseif ($p['type_invoice'] == "EKSPOR") {
                $salesOrderExportData = $this->salesOrderExportModel
                    ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                    ->join('customers', 'customers.id = sales_contract.customer_id')
                    ->where('sales_order_export_id', $p['invoice_id'])
                    ->first();
                $nomor_invoice = $salesOrderExportData['sales_order_export_no'];
                $customer_name = $salesOrderExportData['name'];
            } elseif ($p['type_invoice'] == "PROFORMA INVOICE") {
                $salesOrderExportData = $this->proformaInvoiceModel
                    ->join('sales_order_export', 'sales_order_export.sales_contract_id = proforma_invoice.sales_order_export_id')
                    ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                    ->join('customers', 'customers.id = sales_contract.customer_id')
                    ->where('proforma_invoice.id', $p['invoice_id'])
                    ->first();
                $nomor_invoice = $salesOrderExportData['sales_order_export_no'];
                $customer_name = $salesOrderExportData['name'];
            } elseif ($p['type_invoice'] == "LAIN-LAIN") {
                $salesOrderLainData = $this->salesOrderLainModel
                    ->join('customers', 'customers.id = sales_order_lain.customer_id')
                    ->where('sales_order_lain.id', $p['invoice_id'])
                    ->first();
                $nomor_invoice = $salesOrderLainData['no_sales_order'];
                $customer_name = $salesOrderLainData['name'];
            } elseif ($p['type_invoice'] == "RETURN") {
                $invoiceIds = is_array($p['invoice_id']) ? $p['invoice_id'] : explode(',', $p['invoice_id']);  // Mengonversi ke array jika dalam format string yang dipisah koma

                // Query untuk mengambil data berdasarkan array invoice_id
                $salesOrderReturnData = $this->salesOrderReturnModel
                    ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice')
                    ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                    ->whereIn('sales_order_return.id', $invoiceIds)  // Menggunakan whereIn untuk memilih beberapa ID
                    ->findAll();  // Mengambil semua data yang cocok

                // Ambil nomor faktur dan nama pelanggan dan gabungkan dengan koma
                $nomor_invoice = implode(', ', array_map(function ($item) {
                    return $item['no_return'];  // Mengambil no_faktur dari setiap hasil query
                }, $salesOrderReturnData));

                $customerName = $this->customerModel->where('id', $p['customer_id'])->select('name')->first();
                if (empty($customerName)) {
                    $customer_name = implode(', ', array_map(function ($item) {
                        return $item['name'];  // Mengambil name dari setiap hasil query
                    }, $salesOrderReturnData));
                } else {
                    $customer_name = $customerName['name'];
                }
            }

            array_push($dataPembayaran, [
                "no" => $no++,
                "id" => encrypt($p['id']),
                "no_pembayaran" => $p['no_pembayaran'],
                "customer_name" => $customer_name,
                "payment_date" => date("d/m/Y", strtotime($p['tanggal'])),
                "tipe_invoice" => $p['type_invoice'],
                "amount" => number_format($p['total_bayar'], 2),
                "currency" =>  $p['valas_id'],
                "nomor_invoice" => $nomor_invoice,
                "status_posting" => $p['status_posting']
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $pembayaranInvoiceData['totalData'],
            "recordsFiltered"   => $pembayaranInvoiceData['totalFilteredData'],
            "data"              => $dataPembayaran,
            "payload"           => $payload,
            'test' => $addCondition
        ];

        echo json_encode($data);
        return;
    }


    public function getAllDataInvoice()
    {
        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokalForPembayaran($this->this_company_id);

        $dataAllSalesOrderInvoice = [];

        $no = 1;
        foreach ($dataSalesOrderInvoice['data'] as $data) {
            // Karakter yang akan dihapus
            $unwanted_characters = array('[', '"', ']');

            // Gantikan karakter tidak diinginkan dengan string kosong
            $cleaned_string_document_no = str_replace($unwanted_characters, ' ', $data->doc_no);

            $statusPembayaranInvoice = "";
            if ($data->status_pelunasan = "UNPAID") {
                $statusPembayaranInvoice = "BELUM LUNAS";
            } else {
                $statusPembayaranInvoice = "LUNAS";
            }
            // Calculate due date
            $tanggalJatuhTempo = "Tidak ada terms";
            if (isset($data->terms_customer) && $data->terms_customer !== "" && ctype_digit($data->terms_customer)) {  // Pengecekan lebih akurat untuk string angka
                try {
                    // Ubah format tanggal dari d/m/Y ke d-m-Y untuk pemrosesan
                    $tanggalFaktur = DateTime::createFromFormat('d/m/Y', $data->tanggal_faktur);
                    if ($tanggalFaktur) {
                        $terms_customer = (int)$data->terms_customer;  // Konversi ke integer
                        $tanggalFaktur->modify('+' . $terms_customer . ' days');
                        $tanggalJatuhTempo = $tanggalFaktur->format('d/m/Y');  // Kembalikan ke format asal
                    }
                } catch (Exception $e) {
                    $tanggalJatuhTempo = "Format tanggal tidak valid";
                }
            }

            array_push($dataAllSalesOrderInvoice, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "no_faktur"         => $data->no_faktur,
                "tanggal_faktur"    => $data->tanggal_faktur,
                "document_type"     => strtoupper($data->doc_type),
                "document_no"       => $cleaned_string_document_no,
                "total_invoice"     => number_format(floatval($data->total_invoice)),
                "kode_pelanggan"    => $data->kode_pelanggan,
                "nama_pelanggan"    => $data->nama_pelanggan,
                "nama_sales"        => $data->salesName,
                "tipe_invoice"      => $data->tipe_invoice,
                "counter_print"     => $data->counter_print,
                "terms" => $data->terms_customer ?? 'termin belum dibuat',
                "status_pembayaran" => $statusPembayaranInvoice,
                "tanggal_jatuh_tempo" => $tanggalJatuhTempo,
                "dpp" => $data->dpp,
                "ppn" => $data->ppn,
            ]);
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataSalesOrderInvoice['totalData'],
            "recordsFiltered" => $dataSalesOrderInvoice['totalFilteredData'],
            "data" => $dataAllSalesOrderInvoice,
        ];

        echo json_encode($data);
        return;
    }

    public function getDokumenList()
    {
        $jenis_dokumen = $this->request->getVar('jenis_dokumen');
        $dokumenList = [];
        if ($jenis_dokumen == "lokal") {
            $salesOrderLokalInvoiceData = $this->salesOrderInvoiceModel->where('deletedAt', null)->where('id_company', $this->this_company_id)->findAll();
            foreach ($salesOrderLokalInvoiceData as $s) {
                array_push($dokumenList, $s['document_no']);
            }
        } elseif ($jenis_dokumen == "ekspor") {
            $salesOrderExportData = $this->salesOrderExportModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll();
            foreach ($salesOrderExportData as $s) {
                array_push($dokumenList, $s['sales_order_export_no']);
            }
        } elseif ($jenis_dokumen == "lain") {
            $salesOrderLainData = $this->salesOrderLainModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll();
            foreach ($salesOrderLainData as $s) {
                array_push($dokumenList, $s['no_sales_order']);
            }
        }

        return response()->setJSON([
            'data' => $dokumenList,
            'status' => true
        ]);
    }

    public function getBarangSalesLokal()
    {
        $idArray = json_decode($this->request->getVar('id'), true); // Decode array dari JSON
        $pembayaranInvoiceId = decrypt($this->request->getVar('pembayaran_invoice_id'));

        $dataBarang = [];
        $isImport = false;
        $totalPembayaran = 0;

        $idArray = array_map('intval', $idArray);

        if (!empty($idArray)) {
            // Ambil data invoice berdasarkan ID jika tidak ada pembayaran_invoice_id
            if (empty($pembayaranInvoiceId)) {
                $salesOrderInvoiceDetailData = $this->salesOrderInvoiceDetailModel
                    ->select('sales_order_invoice.no_faktur, sales_order_invoice_detail.id as sales_order_invoice_detail_id, sales_order_invoice_detail.id_sales_order_invoice as sales_order_invoice_id, qty_invoice, harga_barang_invoice, amount_invoice, kode_barang, barang_name')
                    ->join('barang_master_sales', 'sales_order_invoice_detail.id_barang_invoice = barang_master_sales.id', 'left')
                    ->join('sales_order_invoice', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'left')
                    ->whereIn('id_sales_order_invoice', $idArray)
                    ->where('sales_order_invoice_detail.deletedAt', null)
                    ->findAll();
                foreach ($salesOrderInvoiceDetailData as $s) {
                    array_push($dataBarang, $s);
                }
            }

            // Ambil data pembayaran jika pembayaran_invoice_id tersedia
            if (!empty($pembayaranInvoiceId)) {
                $pembayaranInvoiceDetail = $this->pembayaranInvoiceDetailModel
                    ->join('sub_akuns as akun_kas', 'pembayaran_invoice_detail.akun_kredit = akun_kas.id', 'left')
                    ->join('sub_akuns as akun_selisih', 'pembayaran_invoice_detail.akun_debit = akun_selisih.id', 'left')
                    ->join('sales_order_invoice_detail', 'pembayaran_invoice_detail.sales_order_invoice_detail_id = sales_order_invoice_detail.id', 'left')
                    ->join('sales_order_invoice', 'pembayaran_invoice_detail.sales_order_invoice_id = sales_order_invoice.id', 'left')
                    ->join('barang_master_sales', 'sales_order_invoice_detail.id_barang_invoice = barang_master_sales.id', 'left')
                    ->select('barang_master_sales.barang_name, barang_master_sales.kode_barang, sales_order_invoice.no_faktur, sales_order_invoice_detail.id as sales_order_invoice_detail_id, sales_order_invoice_detail.id_sales_order_invoice as sales_order_invoice_id, akun_kas.id as id_akun_kas, akun_selisih.id as id_akun_selisih, akun_kas.nama_sub as nama_sub_kas, akun_kas.no_sub as no_sub_kas, akun_selisih.nama_sub as nama_sub_selisih, akun_selisih.no_sub as no_sub_selisih, pembayaran_invoice_detail.*')
                    ->where('pembayaran_invoice_id', $pembayaranInvoiceId)
                    ->findAll();

                foreach ($pembayaranInvoiceDetail as $p) {
                    array_push($dataBarang, [
                        "sales_order_invoice_detail_id" => $p['sales_order_invoice_detail_id'],
                        "sales_order_invoice_id" => $p['sales_order_invoice_id'],
                        "no_faktur" => $p['no_faktur'],
                        "qty_invoice" => $p['qty'],
                        "harga_barang_invoice" => $p['harga_satuan'],
                        "amount_invoice" => $p['harga_total'],
                        "kode_barang" => $p['kode_barang'],
                        "barang_name" => $p['barang_name'],
                        "keterangan" => $p['keterangan'],
                        "keterangan_pajak" => $p['keterangan_pajak'],
                        "nominal_pajak" => $p['nominal_pajak'],
                        "id_akun_kredit" => $p['id_akun_kas'],
                        "id_akun_debit" => $p['id_akun_selisih'],
                        "akun_kredit" => $p['no_sub_kas'] . ' - ' . $p['nama_sub_kas'],
                        "akun_debit" => $p['no_sub_selisih'] . ' - ' . $p['nama_sub_selisih'],
                    ]);
                }

                $pembayaranInvoiceData = $this->pembayaranInvoiceModel
                    ->select('total_bayar, jenis_data')
                    ->where('id', $pembayaranInvoiceId)
                    ->first();


                if ($pembayaranInvoiceData["jenis_data"] == "import") {
                    $isImport = true;
                }
            }

            if (empty($dataBarang)) {

                $dataBarang = $this->salesOrderInvoiceModel->whereIn("id", $idArray)
                    ->select('id as sales_order_invoice_id, no_faktur, tanggal_faktur, total_invoice as amount_invoice, document_type')
                    ->findAll();

                $dataBarang = $dataBarang;

                $isImport = true;
            }
        }


        return $this->response->setJSON([
            'data' => $dataBarang,
            'totalPembayaran' => $pembayaranInvoiceData['total_bayar'] ?? 0,
            'status' => true,
            'isImport' => $isImport,
        ]);
    }

    public function getBarangSalesProformaInvoice()
    {
        $idArray = json_decode(decrypt($this->request->getVar('id')), true); // Decode array dari JSON
        $pembayaranInvoiceId = decrypt($this->request->getVar('pembayaran_invoice_id'));

        $dataBarang = [];
        $isImport = false;
        $totalPembayaran = 0;

        // var_dump($idArray);
        // die;

        // $idArray = array_map('intval', $idArray);

        if (!empty($idArray)) {
            // Ambil data invoice berdasarkan ID jika tidak ada pembayaran_invoice_id
            if (empty($pembayaranInvoiceId)) {
                $proformaInvoiceBarangData = $this->proformaInvoiceBarangModel
                    ->select('proforma_invoice.no_pi, proforma_invoice_barang.id as proforma_invoice_barang_id, proforma_invoice_barang.id as proforma_invoice_id, qty_barang, harga_satuan, total_harga, nama_barang')
                    ->join('proforma_invoice', 'proforma_invoice_barang.proforma_invoice_id = proforma_invoice.id', 'left')
                    ->where('proforma_invoice_id', $idArray)
                    ->where('proforma_invoice_barang.deletedAt', null)
                    ->findAll();
                foreach ($proformaInvoiceBarangData as $s) {
                    array_push($dataBarang, $s);
                }

                // var_dump($proformaInvoiceBarangData);
                // die;
            }

            // Ambil data pembayaran jika pembayaran_invoice_id tersedia
            if (!empty($pembayaranInvoiceId)) {
                $pembayaranInvoiceDetail = $this->pembayaranInvoiceDetailModel
                    ->join('sub_akuns as akun_kas', 'pembayaran_invoice_detail.akun_kredit = akun_kas.id', 'left')
                    ->join('sub_akuns as akun_selisih', 'pembayaran_invoice_detail.akun_debit = akun_selisih.id', 'left')
                    ->join('proforma_invoice_barang', 'pembayaran_invoice_detail.proforma_invoice_barang_id = proforma_invoice_barang.id', 'left')
                    ->join('proforma_invoice', 'proforma_invoice_barang.proforma_invoice_id = proforma_invoice.id', 'left')
                    ->select('barang_master_sales.nama_barang, barang_master_sales.kode_barang, proforma_invoice.no_faktur, proforma_invoice_barang.id as proforma_invoice_barang_id, proforma_invoice_barang.id as proforma_invoice_id, akun_kas.id as id_akun_kas, akun_selisih.id as id_akun_selisih, akun_kas.nama_sub as nama_sub_kas, akun_kas.no_sub as no_sub_kas, akun_selisih.nama_sub as nama_sub_selisih, akun_selisih.no_sub as no_sub_selisih, pembayaran_invoice_detail.*')
                    ->where('pembayaran_invoice_id', $pembayaranInvoiceId)
                    ->findAll();

                foreach ($pembayaranInvoiceDetail as $p) {
                    array_push($dataBarang, [
                        "proforma_invoice_barang_id" => $p['proforma_invoice_barang_id'],
                        "proforma_invoice_id" => $p['proforma_invoice_id'],
                        "no_faktur" => $p['no_pi'],
                        "qty_invoice" => $p['qty_barang'],
                        "harga_barang_invoice" => $p['harga_satuan'],
                        "amount_invoice" => $p['total_harga'],
                        "barang_name" => $p['nama_barang'],
                        "keterangan" => $p['keterangan'],
                        // "keterangan_pajak" => $p['keterangan_pajak'],
                        // "nominal_pajak" => $p['nominal_pajak'],
                        "id_akun_kredit" => $p['id_akun_kas'],
                        "id_akun_debit" => $p['id_akun_selisih'],
                        "akun_kredit" => $p['no_sub_kas'] . ' - ' . $p['nama_sub_kas'],
                        "akun_debit" => $p['no_sub_selisih'] . ' - ' . $p['nama_sub_selisih'],
                    ]);
                }

                $pembayaranInvoiceData = $this->pembayaranInvoiceModel
                    ->select('total_bayar, jenis_data')
                    ->where('id', $pembayaranInvoiceId)
                    ->first();


                if ($pembayaranInvoiceData["jenis_data"] == "import") {
                    $isImport = true;
                }
            }

            if (empty($dataBarang)) {

                $dataBarang = $this->salesOrderInvoiceModel->whereIn("id", $idArray)
                    ->select('id as sales_order_invoice_id, no_faktur, tanggal_faktur, total_invoice as amount_invoice, document_type')
                    ->findAll();

                $dataBarang = $dataBarang;

                $isImport = true;
            }
        }


        return $this->response->setJSON([
            'data' => $dataBarang,
            'totalPembayaran' => $pembayaranInvoiceData['total_bayar'] ?? 0,
            'status' => true,
            'isImport' => $isImport,
        ]);
    }

    public function getBarangSalesReturn()
    {
        $idArray = json_decode($this->request->getVar('id'), true); // Decode array dari JSON
        $pembayaranreturnId = decrypt($this->request->getVar('pembayaran_invoice_id'));

        $dataBarang = [];
        $totalPembayaran = 0;

        if (!empty($idArray)) {
            // Ambil data return berdasarkan ID jika tidak ada pembayaran_invoice_id
            if (empty($pembayaranreturnId)) {
                $salesOrderReturnDetailData = $this->salesOrderReturnDetailModel
                    ->select('sales_order_return.no_return as no_faktur, sales_order_return_detail.id as sales_order_return_detail_id, sales_order_return_detail.id_sales_order_return as sales_order_return_id, qty_return, harga_barang_return, amount_return, kode_barang, barang_name')
                    ->join('barang_master_sales', 'sales_order_return_detail.id_barang_return = barang_master_sales.id', 'left')
                    ->join('sales_order_return', 'sales_order_return_detail.id_sales_order_return = sales_order_return.id', 'left')
                    ->whereIn('id_sales_order_return', $idArray)
                    ->where('sales_order_return_detail.deletedAt', null)
                    ->findAll();

                foreach ($salesOrderReturnDetailData as $s) {
                    array_push($dataBarang, $s);
                }
            }

            // Ambil data pembayaran jika pembayaran_invoice_id tersedia
            if (!empty($pembayaranreturnId)) {
                $pembayaranInvoiceDatail = $this->pembayaranInvoiceDetailModel
                    ->join('sub_akuns as akun_kas', 'pembayaran_invoice_detail.akun_kredit = akun_kas.id')
                    ->join('sub_akuns as akun_selisih', 'pembayaran_invoice_detail.akun_debit = akun_selisih.id')
                    ->join('sales_order_return_detail', 'pembayaran_invoice_detail.sales_order_invoice_detail_id = sales_order_return_detail.id', 'left')
                    ->join('sales_order_return', 'pembayaran_invoice_detail.sales_order_invoice_id = sales_order_return.id', 'left')
                    ->join('barang_master_sales', 'sales_order_return_detail.id_barang_return = barang_master_sales.id', 'left')
                    ->select('barang_master_sales.barang_name, barang_master_sales.kode_barang, sales_order_return.no_return as no_faktur, sales_order_return_detail.id as sales_order_return_detail_id, sales_order_return_detail.id_sales_order_return as sales_order_return_id, akun_kas.id as id_akun_kas, akun_selisih.id as id_akun_selisih, akun_kas.nama_sub as nama_sub_kas, akun_kas.no_sub as no_sub_kas, akun_selisih.nama_sub as nama_sub_selisih, akun_selisih.no_sub as no_sub_selisih, pembayaran_invoice_detail.*')
                    ->where('pembayaran_invoice_id', $pembayaranreturnId)
                    ->findAll();

                foreach ($pembayaranInvoiceDatail as $p) {
                    array_push($dataBarang, [
                        "sales_order_return_detail_id" => $p['sales_order_invoice_detail_id'],
                        "sales_order_return_id" => $p['sales_order_invoice_id'],
                        "no_faktur" => $p['no_faktur'],
                        "qty_return" => $p['qty'],
                        "harga_barang_return" => $p['harga_satuan'],
                        "amount_return" => $p['harga_total'],
                        "kode_barang" => $p['kode_barang'],
                        "barang_name" => $p['barang_name'],
                        "keterangan" => $p['keterangan'],
                        "keterangan_pajak" => $p['keterangan_pajak'],
                        "nominal_pajak" => $p['nominal_pajak'],
                        "id_akun_kredit" => $p['id_akun_kas'],
                        "id_akun_debit" => $p['id_akun_selisih'],
                        "akun_kredit" => $p['no_sub_kas'] . ' - ' . $p['nama_sub_kas'],
                        "akun_debit" => $p['no_sub_selisih'] . ' - ' . $p['nama_sub_selisih'],
                    ]);
                }

                $pembayaranreturnData = $this->pembayaranInvoiceModel
                    ->select('total_bayar')
                    ->where('id', $pembayaranreturnId)
                    ->first();
            }
        }

        return $this->response->setJSON([
            'data' => $dataBarang,
            'totalPembayaran' => $pembayaranreturnData['total_bayar'] ?? 0,
            'status' => true,
        ]);
    }

    public function getBarangSalesEkspor()
    {
        $id = decrypt($this->request->getVar('id'));
        $dataBarang = [];
        $totalPembayaran = 0;
        $totalAmountInvoice = 0;
        $salesOrderExportData = $this->salesOrderExportModel
            ->select('
                sales_order_export_id,
                commision,
                palet_fumigation,
                palet_fumigation_price,
                freight,
                additional,
                additional_2,
                rebate_price,
                royalty_price,
                can_deduction_price,
                estimated_freight_price,
                others_type,
                others_price,
            ')
            ->where('sales_order_export_id', $id)
            ->first();
        $salesOrderExportAdditionalData = $this->salesOrderExportAdditionalModel
            ->where('sales_order_export_id', $id)
            ->findAll();
        $salesOrderExportDetailData = $this->salesOrderExportDetailModel
            ->select('
                barang_master_sales.kode_barang,
                barang_master_sales.barang_name,
                SUM(sales_order_detail_export.qty) as total_qty,
                SUM(sales_order_detail_export.total_harga_barang) as total_harga,
                sales_order_detail_export.harga_barang,
                sales_contract_detail.specs,
            ')
            ->join('sales_contract_detail', 'sales_order_detail_export.sales_contract_detail_id = sales_contract_detail.id', 'left')
            ->join('barang_master_sales', 'sales_contract_detail.barang_master_sales_id = barang_master_sales.id', 'left')
            ->where('sales_order_detail_export.sales_order_export_id', $id)
            ->where('sales_order_detail_export.deletedAt', null)
            ->groupBy('barang_master_sales.kode_barang, barang_master_sales.barang_name, sales_order_detail_export.harga_barang')
            ->findAll();

            foreach ($salesOrderExportDetailData as $s) {
                $totalAmountInvoice += $s['total_harga'];
                array_push($dataBarang, $s);
            }

        $pembayaranInvoiceData = $this->pembayaranInvoiceModel
            ->where('pembayaran_invoice.company_id', $this->this_company_id)
            ->where('pembayaran_invoice.invoice_id', $id)
            ->where('pembayaran_invoice.deletedAt', null)
            ->where('pembayaran_invoice.type_invoice', "EKSPOR")
            ->findAll();
        foreach ($pembayaranInvoiceData as $s) {
            $totalPembayaran += $s['total_bayar'];
        }

        // if ($totalAmountInvoice >= $totalPembayaran) {
        $data = [
            'data' => $dataBarang,
            'salesOrderExportData'  => $salesOrderExportData,
            'salesOrderExportAdditionalData'  => $salesOrderExportAdditionalData,
            'totalPembayaran' => $totalPembayaran,
            'status' => true
        ];
        // } else {
        //     $data = [
        //         'data' => [],
        //         'totalPembayaran' => $totalPembayaran,
        //         'status' => false
        //     ];
        // }
        return response()->setJSON($data);
    }

    public function getBarangSalesLain()
    {
        $id = decrypt($this->request->getVar('id'));
        $dataBarang = [];
        $totalPembayaran = 0;
        $totalAmountInvoice = 0;
        $salesOrderLainDetailData = $this->salesOrderLainDetailModel
            ->select('kode_barang, barang_name, qty_konversi, harga_satuan, total_harga')
            ->join('stock', 'stock.id = sales_order_lain_detail.stock_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->where('sales_order_lain_detail.deletedAt', null)
            ->where('sales_order_lain_detail.sales_order_lain_id', $id)
            ->findAll();
        foreach ($salesOrderLainDetailData as $s) {
            $totalAmountInvoice += $s['total_harga'];
            array_push($dataBarang, $s);
        }
        $pembayaranInvoiceData = $this->pembayaranInvoiceModel
            ->where('pembayaran_invoice.company_id', $this->this_company_id)
            ->where('pembayaran_invoice.invoice_id', $id)
            ->where('pembayaran_invoice.deletedAt', null)
            ->where('pembayaran_invoice.type_invoice', "LAIN-LAIN")
            ->findAll();
        foreach ($pembayaranInvoiceData as $s) {
            $totalPembayaran += $s['total_bayar'];
        }

        // if ($totalAmountInvoice >= $totalPembayaran) {
        $data = [
            'data' => $dataBarang,
            'totalPembayaran' => $totalPembayaran,
            'status' => true
        ];
        // } else {
        //     $data = [
        //         'data' => [],
        //         'totalPembayaran' => $totalPembayaran,
        //         'status' => false
        //     ];
        // }


        return response()->setJSON($data);
    }



    public function saveLokalInvoice()
    {


        try {
            $check = $this->pembayaranInvoiceModel->where('company_id', $this->this_company_id)->where('no_pembayaran', $this->request->getVar('no_bukti_pembayaran'))->first();
            if ($check != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "No pembayaran sudah digunakan",
                    'status' => false
                ]);
            }
            if ($this->request->getVar('total_bayar') == null || repairDouble($this->request->getVar('total_bayar'))  <= 0) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Pembayaran Tidak Boleh Kosong",
                    'status' => false
                ]);
            }

            $tipe_invoice = $this->request->getVar('tipe_invoice');

            if ($tipe_invoice == "LOKAL") {
                $no_dokumen_req = $this->request->getVar("no_dokumen");

                // Validasi bahwa data adalah array
                if (!is_array($no_dokumen_req)) {
                    // Konversi ke array jika data bukan array
                    $no_dokumen_req = $no_dokumen_req ? [$no_dokumen_req] : [];
                }

                // Gunakan implode jika array tidak kosong
                if (!empty($no_dokumen_req)) {
                    $no_dokumen_implode = "[" . implode("','", $no_dokumen_req) . "]";
                } else {
                    $no_dokumen_implode = "[]"; // Default jika array kosong
                }

                $listBarang = json_decode($_POST['list_barang']);

                $isImport = array_reduce($listBarang, function ($carry, $item) {
                    return $carry && isset($item->document_type) && stripos($item->document_type, 'import') !== false;
                }, true) ? 'import' : null;

                $id = $this->pembayaranInvoiceModel->insert([
                    'company_id' => $this->this_company_id,
                    'user_id' => $this->user_id,
                    'divisi_id' => $this->request->getVar('divisi_id'),
                    'payment_method' => $this->request->getVar('payment_methods'),
                    'customer_id' => decrypt($this->request->getVar('customer')),
                    'invoice_id' => $no_dokumen_implode,
                    'no_pembayaran' => $this->request->getVar('no_bukti_pembayaran'),
                    'keterangan' =>  $this->request->getVar('keterangan'),
                    'type_invoice' => "LOKAL",
                    'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                    'total_invoice' => repairDouble($this->request->getVar('total_amount_invoice')),
                    'potongan' => $this->request->getVar('potongan') ? repairDouble($this->request->getVar('potongan')) : 0,
                    'total_bayar' => repairDouble($this->request->getVar('total_bayar')),
                    'akun_kas' => $this->request->getVar('akun_kas'),
                    'akun_selisih' => $this->request->getVar('akun_selisih'),
                    'status_posting' => '0',
                    'pembayaran_dari' => $this->request->getVar('pembayaran_dari'),
                    'jenis_data' => $isImport
                ]);

                foreach (json_decode($_POST['list_barang']) as $l) {
                    $this->pembayaranInvoiceDetailModel->insert([
                        'pembayaran_invoice_id' => $id,
                        'sales_order_invoice_id' => $l->sales_order_invoice_id,
                        'sales_order_invoice_detail_id' => $l->sales_order_invoice_detail_id,
                        'type_invoice' => "LOKAL",
                        'nama_barang' => $l->barang_name,
                        'qty' => $l->qty_invoice,
                        'harga_satuan' => $l->harga_barang_invoice,
                        'harga_total' => $l->amount_invoice,
                    ]);

                    $this->salesOrderInvoiceModel->update($l->sales_order_invoice_id, [
                        "status_pelunasan" => "PAID"
                    ]);
                }

                return response()->setJSON([
                    'id' => encrypt($id),
                    'status' => true,
                    'message' => "Pembayaran Invoice Lokal berhasil disimpan",
                    'token' => csrf_hash()
                ]);
            } elseif ($tipe_invoice == "LAIN-LAIN") {
                $id = $this->pembayaranInvoiceModel->insert([
                    'company_id' => $this->this_company_id,
                    'user_id' => $this->user_id,
                    'payment_method' => $this->request->getVar('payment_methods'),
                    'invoice_id' => decrypt($this->request->getVar('no_dokumen')),
                    'valas_id' => 30,
                    'no_pembayaran' => $this->request->getVar('no_bukti_pembayaran'),
                    'keterangan' =>  $this->request->getVar('keterangan'),
                    'type_invoice' => "LAIN-LAIN",
                    'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                    'total_invoice' => repairDouble($this->request->getVar('total_amount_invoice')),
                    'potongan' => $this->request->getVar('potongan') ? repairDouble($this->request->getVar('potongan')) : 0,
                    'total_bayar' => repairDouble($this->request->getVar('total_bayar')),
                    'akun_kas' => $this->request->getVar('akun_kas'),
                    'akun_selisih' => $this->request->getVar('akun_selisih'),
                    'status_posting' => '0'
                ]);

                return response()->setJSON([
                    'id' => encrypt($id),
                    'status' => true,
                    'message' => "Pembayaran Invoice Lain Lain berhasil disimpan",
                    'token' => csrf_hash()
                ]);
            } elseif ($tipe_invoice == "EKSPOR") {

                $currency = $this->salesOrderExportModel
                    ->select('currency')
                    ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                    ->where('sales_order_export.id', decrypt($this->request->getVar('no_dokumen')))
                    ->first();
                
                $valas = $this->metaDataModel->where('id', $currency)->where('deletedAt', null)->first();

                $id = $this->pembayaranInvoiceModel->insert([
                    'company_id' => $this->this_company_id,
                    'user_id' => $this->user_id,
                    'payment_method' => $this->request->getVar('payment_methods'),
                    'invoice_id' => decrypt($this->request->getVar('no_dokumen')),
                    'divisi_id' => $this->request->getVar('divisi_id'),
                    'valas_id' => $valas['id'],
                    'no_pembayaran' => $this->request->getVar('no_bukti_pembayaran'),
                    'keterangan' =>  $this->request->getVar('keterangan'),
                    'type_invoice' => "EKSPOR",
                    'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                    'total_invoice' => $this->request->getVar('total_amount_invoice'),
                    'potongan' => $this->request->getVar('potongan') ? $this->request->getVar('potongan'): 0,
                    'total_bayar' => $this->request->getVar('total_bayar'),
                    'akun_kas' => $this->request->getVar('akun_kas'),
                    'akun_selisih' => $this->request->getVar('akun_selisih'),
                    'status_posting' => '0'
                ]);

                $salesOrderExportData = $this->salesOrderExportModel
                    ->select('
                        sales_order_export_id,
                        commision,
                        palet_fumigation,
                        palet_fumigation_price,
                        freight,
                        additional,
                        additional_2,
                        rebate_price,
                        royalty_price,
                        can_deduction_price,
                        estimated_freight_price,
                        others_type,
                        others_price,
                    ')
                    ->where('sales_order_export_id', $id)
                    ->first();

                return response()->setJSON([
                    'id' => encrypt($id),
                    'status' => true,
                    'message' => "Pembayaran Invoice Ekspor berhasil disimpan",
                    'token' => csrf_hash()
                ]);
            } elseif ($tipe_invoice == "RETURN") {
                $no_dokumen_req = $this->request->getVar("no_dokumen");
                $no_dokumen_implode = '[' . implode(",", $no_dokumen_req) . ']';

                $id = $this->pembayaranInvoiceModel->insert([
                    'company_id' => $this->this_company_id,
                    'user_id' => $this->user_id,
                    'divisi_id' => $this->request->getVar('divisi_id'),
                    'payment_method' => $this->request->getVar('payment_methods'),
                    'customer_id' => decrypt($this->request->getVar('customer')),
                    'valas_id' => 30,
                    'invoice_id' => $no_dokumen_implode,
                    'no_pembayaran' => $this->request->getVar('no_bukti_pembayaran'),
                    'keterangan' =>  $this->request->getVar('keterangan'),
                    'type_invoice' => "RETURN",
                    'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                    'total_invoice' => repairDouble($this->request->getVar('total_amount_invoice')),
                    'potongan' => $this->request->getVar('potongan') ? repairDouble($this->request->getVar('potongan')) : 0,
                    'total_bayar' => repairDouble($this->request->getVar('total_bayar')),
                    'akun_kas' => $this->request->getVar('akun_kas'),
                    'akun_selisih' => $this->request->getVar('akun_selisih'),
                    'status_posting' => '0'
                ]);

                foreach (json_decode($_POST['list_barang']) as $l) {
                    $this->pembayaranInvoiceDetailModel->insert([
                        'pembayaran_invoice_id' => $id,
                        'sales_order_invoice_id' => $l->sales_order_invoice_id,
                        'sales_order_invoice_detail_id' => $l->sales_order_invoice_detail_id,
                        'type_invoice' => "RETURN",
                        'nama_barang' => $l->barang_name,
                        'qty' => $l->qty_invoice,
                        'harga_satuan' =>  $l->harga_barang_invoice,
                        'harga_total' =>  $l->harga_barang_invoice * $l->qty_invoice,
                        'keterangan' => $l->keterangan,
                        'akun_kredit' => $l->akun_kredit,
                        'akun_debit' => $l->akun_debit,
                    ]);

                    $this->salesOrderReturnModel->update($l->sales_order_invoice_id, [
                        "already_paid" => 1
                    ]);
                }

                return response()->setJSON([
                    'id' => encrypt($id),
                    'status' => true,
                    'message' => "Pembayaran Invoice Retur berhasil disimpan",
                    'token' => csrf_hash()
                ]);
            } elseif ($tipe_invoice == "PROFORMA INVOICE") {
                $customer = $this->proformaInvoiceModel
                    ->select('customers.*')
                    ->join('sales_order_export', 'sales_order_export.sales_contract_id = proforma_invoice.sales_order_export_id')
                    ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                    ->join('customers', 'customers.id = sales_contract.customer_id')
                    ->where('proforma_invoice.id', decrypt($this->request->getVar('no_dokumen')))
                    ->first();

                $currency = $this->proformaInvoiceModel
                    ->select('currency')
                    ->join('sales_order_export', 'sales_order_export.sales_contract_id = proforma_invoice.sales_order_export_id')
                    ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                    ->where('proforma_invoice.id', decrypt($this->request->getVar('no_dokumen')))
                    ->first();
                
                $valas = $this->metaDataModel->where('id', $currency)->where('deletedAt', null)->first();


                $id = $this->pembayaranInvoiceModel->insert([
                    'company_id' => $this->this_company_id,
                    'user_id' => $this->user_id,
                    'payment_method' => $this->request->getVar('payment_methods'),
                    'divisi_id' => $this->request->getVar('divisi_id'),
                    'invoice_id' => decrypt($this->request->getVar('no_dokumen')),
                    'customer_id' => $customer['id'],
                    'valas_id' => $valas['id'],
                    'no_pembayaran' => $this->request->getVar('no_bukti_pembayaran'),
                    'keterangan' =>  $this->request->getVar('keterangan'),
                    'type_invoice' => "PROFORMA INVOICE",
                    'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                    'total_invoice' => $this->request->getVar('total_amount_invoice'),
                    'potongan' => $this->request->getVar('potongan') ? $this->request->getVar('potongan'): 0,
                    'total_bayar' => $this->request->getVar('total_bayar'),
                    'akun_kas' => $this->request->getVar('akun_kas'),
                    'akun_selisih' => $this->request->getVar('akun_selisih'),
                    'status_posting' => '0'
                ]);

                // $salesOrderExportData = $this->salesOrderExportModel
                //     ->select('
                //         sales_order_export_id,
                //         commision,
                //         palet_fumigation,
                //         palet_fumigation_price,
                //         freight,
                //         additional,
                //         additional_2,
                //         rebate_price,
                //         royalty_price,
                //         can_deduction_price,
                //         estimated_freight_price,
                //         others_type,
                //         others_price,
                //     ')
                //     ->where('sales_order_export_id', $id)
                //     ->first();

                return response()->setJSON([
                    'id' => encrypt($id),
                    'status' => true,
                    'message' => "Pembayaran Proforma Invoice berhasil disimpan",
                    'token' => csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan " . $e->getMessage(),
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    public function updateInvoice()
    {
        // var_dump($this->request->getVar("keterangan"));
        // die;
        $no_dokumen_req = $this->request->getVar("no_dokumen");

        // Validasi bahwa data adalah array
        if (!is_array($no_dokumen_req)) {
            // Konversi ke array jika data bukan array
            $no_dokumen_req = $no_dokumen_req ? [$no_dokumen_req] : [];
        }

        // Gunakan implode jika array tidak kosong
        if (!empty($no_dokumen_req)) {
            $no_dokumen_implode = "[" . implode("','", $no_dokumen_req) . "]";
        } else {
            $no_dokumen_implode = "[]"; // Default jika array kosong
        }

        try {
            $id = decrypt($this->request->getVar('id'));
            // if ($this->request->getVar('total_bayar') == null || repairDouble($this->request->getVar('total_bayar'))  <= 0) {
            //     return response()->setJSON([
            //         'token' => csrf_hash(),
            //         'message' => "Pembayaran Tidak Boleh Kosong",
            //         'status' => false
            //     ]);
            // }   

            // Ambil nilai total bayar sebelumnya
            $lastPay = $this->pembayaranInvoiceModel
                ->where('id', $id)
                ->select('total_bayar')
                ->first();

            // Pastikan nilai $lastPay['total_bayar'] valid
            $previousTotalBayar = $lastPay['total_bayar'] ?? 0; // Default ke 0 jika null atau tidak ditemukan

            // Ambil nilai pembayaran baru dari request
            $newPayment = repairDouble($this->request->getVar('total_bayar'));

            // Hitung total pembayaran kumulatif
            $updatedTotalBayar = $previousTotalBayar + $newPayment;

            $listBarang = json_decode($_POST['list_barang']);

            $isImport = array_reduce($listBarang, function ($carry, $item) {
                return $carry && isset($item->document_type) && stripos($item->document_type, 'import') !== false;
            }, true) ? 'import' : null;

            $this->pembayaranInvoiceModel->update($id, [
                'company_id' => $this->this_company_id,
                'user_id' => $this->user_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'customer_id' => decrypt($this->request->getVar('customer')),
                'invoice_id' => $no_dokumen_implode,
                'valas_id' => 30,
                'keterangan' =>  $this->request->getVar('keterangan'),
                'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'potongan' => $this->request->getVar('potongan') ? repairDouble($this->request->getVar('potongan')) : 0,
                'total_bayar' =>  $updatedTotalBayar,
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih'),
                'status_posting' => '0',
                'payment_method' => $this->request->getVar('payment_methods'),
                'jenis_data' => $isImport,
                'pembayaran_dari' => $this->request->getVar('pembayaran_dari'),
            ]);

            $pembayaranInvoiceFirst = $this->pembayaranInvoiceModel->find($id);
            $this->pembayaranInvoiceDetailModel->where('pembayaran_invoice_id', $id)->delete();
            foreach (json_decode($_POST['list_barang']) as $l) {
                $this->pembayaranInvoiceDetailModel->insert([
                    'pembayaran_invoice_id' => $id,
                    'sales_order_invoice_id' => $l->sales_order_invoice_id,
                    'sales_order_invoice_detail_id' => $l->sales_order_invoice_detail_id,
                    'type_invoice' => $pembayaranInvoiceFirst['type_invoice'],
                    'nama_barang' => $l->barang_name,
                    'qty' => $l->qty_invoice,
                    'harga_satuan' => $l->harga_barang_invoice,
                    'harga_total' => $l->amount_invoice,
                ]);
            }


            return response()->setJSON([
                'id' => encrypt($id),
                'status' => true,
                'message' => "Pembayaran Invoice berhasil diupdate",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan " . $e->getMessage(),
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }


    public function updateReturn()
    {
        $no_dokumen_req = $this->request->getVar("no_dokumen");
        $no_dokumen_implode = "['" . implode("','", $no_dokumen_req) . "']";

        try {
            $id = decrypt($this->request->getVar('id'));
            if ($this->request->getVar('total_bayar') == null || repairDouble($this->request->getVar('total_bayar'))  <= 0) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Pembayaran Tidak Boleh Kosong",
                    'status' => false
                ]);
            }

            // Ambil nilai total bayar sebelumnya
            $lastPay = $this->pembayaranInvoiceModel
                ->where('id', $id)
                ->select('total_bayar')
                ->first();

            // Pastikan nilai $lastPay['total_bayar'] valid
            $previousTotalBayar = $lastPay['total_bayar'] ?? 0; // Default ke 0 jika null atau tidak ditemukan

            // Ambil nilai pembayaran baru dari request
            $newPayment = repairDouble($this->request->getVar('total_bayar'));

            // Hitung total pembayaran kumulatif
            $updatedTotalBayar = $previousTotalBayar + $newPayment;


            $this->pembayaranInvoiceModel->update($id, [
                'company_id' => $this->this_company_id,
                'user_id' => $this->user_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'customer_id' => decrypt($this->request->getVar('customer')),
                'invoice_id' => $no_dokumen_implode,
                'valas_id' => $this->request->getVar('valas'),
                'keterangan' =>  $this->request->getVar('keterangan'),
                'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'total_invoice' => repairDouble($this->request->getVar('total_amount_invoice')),
                'potongan' => $this->request->getVar('potongan') ? repairDouble($this->request->getVar('potongan')) : 0,
                'total_bayar' =>  $updatedTotalBayar,
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih'),
                'status_posting' => '0',
                'payment_method' => $this->request->getVar('payment_methods')
            ]);

            $pembayaranInvoiceFirst = $this->pembayaranInvoiceModel->find($id);
            // Delete Detail Lalu Insert Again
            $this->pembayaranInvoiceDetailModel->where('pembayaran_invoice_id', $id)->delete();
            foreach (json_decode($_POST['list_barang']) as $l) {
                $this->pembayaranInvoiceDetailModel->insert([
                    'pembayaran_invoice_id' => $id,
                    'sales_order_invoice_id' => $l->sales_order_return_id,
                    'sales_order_invoice_detail_id' => $l->sales_order_return_detail_id,
                    'type_invoice' => $pembayaranInvoiceFirst['type_invoice'],
                    'nama_barang' => $l->barang_name,
                    'qty' => $l->qty_return,
                    'harga_satuan' => $l->harga_barang_return,
                    'harga_total' => $l->amount_return,
                    'keterangan' => $l->keterangan,
                    'akun_kredit' => $l->akun_kredit,
                    'akun_debit' => $l->akun_debit,
                    'keterangan' => $l->keterangan,
                    'keterangan_pajak' => $l->keterangan_pajak,
                    'nominal_pajak' => $l->nominal_pajak
                ]);
            }


            return response()->setJSON([
                'id' => encrypt($id),
                'status' => true,
                'message' => "Pembayaran Return berhasil diupdate",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan " . $e->getMessage(),
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }


    public function getValas()
    {
        $id = decrypt($this->request->getVar('id'));
        $selectQry = "currency";
        $currency = $this->salesOrderExportModel
            ->select($selectQry)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
            ->where('sales_order_export.deletedAt', null)
            ->where('sales_order_export.sales_order_export_id', $id)
            ->first();

        $valas = $this->metaDataModel->where('id', $currency)->where('deletedAt', null)->first();

        return json_encode($valas['value']);
    }


    public function getValasPI()
    {
        $id = decrypt($this->request->getVar('id'));
        $selectQry = "currency";
        $currency = $this->proformaInvoiceModel
                ->select($selectQry)
                ->join('sales_order_export', 'sales_order_export.sales_contract_id = proforma_invoice.sales_order_export_id')
                ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                ->where('proforma_invoice.id', $id)
                ->first();

        $valas = $this->metaDataModel->where('id', $currency)->where('deletedAt', null)->first();

        return json_encode($valas['value']);
    }


    public function getById($id)
    {
        $id = decrypt($id);
        $tipe_invoice = $this->pembayaranInvoiceModel->getTipeInvoice($id);
        $subAkunsModel = $this->Sub_AkunsModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $customers = $this->customerModel->getCustomerLokal($this->user_id, $this->this_company_id);
        $divisi = $this->divisiModel->getDivisiAccess();
        $dokumenList = [];
        $detail = [];

        if ($tipe_invoice == "LOKAL") {
            $customers_lokal = $this->customerModel
                ->select('customers.id, customers.name') // Pilih kolom yang diperlukan
                ->join('sales_order_invoice', 'sales_order_invoice.id_customer = customers.id', 'left') // Perbaiki kondisi join
                ->where('sales_order_invoice.deletedAt', null)
                ->where('customers.company_id', $this->this_company_id)
                ->where('customers.deletedAt', null)
                ->groupBy('customers.id')
                ->findAll();
            $salesOrderLokalInvoiceData = $this->salesOrderInvoiceModel->where('deletedAt', null)->where('id_company', $this->this_company_id)->findAll();
            foreach ($salesOrderLokalInvoiceData as $s) {
                $s['document_no'] = str_replace(['[', ']', '"'], '', $s['document_no']);
                array_push($dokumenList, $s);
            }
            $data = [
                "customers" => $customers_lokal,
                "divisi" => $divisi,
                "subsAkuns" => $subAkunsModel,
                "dokumenList" => $dokumenList,
                "detail" => $this->pembayaranInvoiceModel->getPembayaranInvoiceDetail($id),
            ];

            return view('Pembayaran/pembayaranInvoice/formLokal', $data);
        } elseif ($tipe_invoice == "EKSPOR") {
            $salesOrderExportData = $this->salesOrderExportModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll();
            foreach ($salesOrderExportData as $s) {
                array_push($dokumenList, $s);
            }
            $data = [
                "customers" => $customers,
                "divisi" => $divisi,
                "subsAkuns" => $subAkunsModel,
                "dokumenList" => $dokumenList,
                "detail" => $this->pembayaranInvoiceModel->getPembayaranInvoiceDetail($id),
            ];
            return view('Pembayaran/pembayaranInvoice/formEkspor', $data);
        } elseif ($tipe_invoice == "PROFORMA INVOICE") {
            $salesOrderExportData = $this->proformaInvoiceModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll();
            foreach ($salesOrderExportData as $s) {
                array_push($dokumenList, $s);
            }
            $data = [
                "customers" => $customers,
                "divisi" => $divisi,
                "subsAkuns" => $subAkunsModel,
                "dokumenList" => $dokumenList,
                "detail" => $this->pembayaranInvoiceModel->getPembayaranInvoiceDetail($id),
            ];
            return view('Pembayaran/pembayaranInvoice/formPI', $data);
        } elseif ($tipe_invoice == "LAIN-LAIN") {

            $salesOrderLainData = $this->salesOrderLainModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->findAll();
            foreach ($salesOrderLainData as $s) {
                array_push($dokumenList, $s);
            }
            $data = [
                "customers" => $customers,
                "divisi" => $divisi,
                "subsAkuns" => $subAkunsModel,
                "dokumenList" => $dokumenList,
                "detail" => $this->pembayaranInvoiceModel->getPembayaranInvoiceDetail($id),
            ];

            return view('Pembayaran/pembayaranInvoice/formLain', $data);
        } elseif ($tipe_invoice == "RETURN") {

            $detail =  $this->pembayaranInvoiceModel->getPembayaranInvoiceDetail($id);
            // $salesOrderReturnData = $this->salesOrderReturnModel
            //     ->where('deletedAt', null)
            //     ->where('id_company', $this->this_company_id)
            //     ->whereIn('id', json_decode($detail['invoice_id']))
            //     ->findAll();
            $customers_return = $this->customerModel
                ->select('customers.id, customers.name') // Kolom yang dibutuhkan
                ->join('sales_order_invoice', 'sales_order_invoice.id_customer = customers.id', 'inner') // Pastikan hanya customer dengan invoice
                ->join('sales_order_return', 'sales_order_return.id_invoice = sales_order_invoice.id', 'inner') // Pastikan hanya invoice yang ada di return
                ->where('sales_order_return.deletedAt', null) // Hanya return yang aktif
                ->where('sales_order_invoice.status_pelunasan', 'UNPAID') // Status pelunasan UNPAID
                ->where('sales_order_return.id_company', $this->this_company_id) // Perusahaan dari return
                ->where('customers.company_id', $this->this_company_id) // Perusahaan dari customer
                ->where('customers.deletedAt', null) // Hanya customer aktif
                ->groupBy('customers.id') // Hindari duplikasi data customer
                ->findAll();

            $data = [
                "customers" => $customers_return,
                "divisi" => $divisi,
                "subsAkuns" => $subAkunsModel,
                "dokumenList" => NULL,
                "detail" => $this->pembayaranInvoiceModel->getPembayaranInvoiceDetail($id),
            ];

            return view('Pembayaran/pembayaranInvoice/formReturn', $data);
        }
    }

    public function getCustomer()
    {

        $invoice_id =  decrypt($this->request->getVar('invoice_id'));
        $tipe_invoice = $this->request->getVar('type_invoice');

        $customer = "";
        if ($tipe_invoice == "LOKAL") {
            $salesOrderLokalInvoiceData = $this->salesOrderInvoiceModel
                ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                ->where('sales_order_invoice.id', $invoice_id)
                ->first();
            $customer = $salesOrderLokalInvoiceData['name'];
        } elseif ($tipe_invoice == "EKSPOR") {
            $salesOrderExportData = $this->salesOrderExportModel
                ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                ->join('customers', 'customers.id = sales_contract.customer_id')
                ->where('sales_order_export_id', $invoice_id)
                ->first();
            $customer = $salesOrderExportData['name'];
        } elseif ($tipe_invoice == "PROFORMA INVOICE") {
            $salesOrderExportData = $this->proformaInvoiceModel
                ->select('customers.*')
                ->join('sales_order_export', 'sales_order_export.sales_order_export_id = proforma_invoice.sales_order_export_id')
                ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                ->join('customers', 'customers.id = sales_contract.customer_id')
                ->where('proforma_invoice.id', $invoice_id)
                ->first();
            $customer = $salesOrderExportData['name'];
        } elseif ($tipe_invoice == "LAIN-LAIN") {
            $salesOrderLainData = $this->salesOrderLainModel
                ->join('customers', 'customers.id = sales_order_lain.customer_id')
                ->where('sales_order_lain.id', $invoice_id)
                ->first();
            $customer = $salesOrderLainData['name'];
        } elseif ($tipe_invoice == "RETURN") {
            $salesOrderReturnData = $this->salesOrderReturnModel
                ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice')
                ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                ->where('sales_order_return.id', $invoice_id)
                ->first();
            $customer = $salesOrderReturnData['name'];
        }

        return json_encode($customer);
    }

    public function deletePembayaranInvoice()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->pembayaranInvoiceModel->where('id', $id)->delete();

        return response()->setJSON([
            'message' => "Pembayaran Invoice berhasil dihapus",
            'status' => true
        ]);
    }

    public function posting()
    {

        $id = decrypt($this->request->getVar('id'));
        $result = $this->jurnalController->insertDataPembayaranInvoice($id);

        if ($result) {
            $this->pembayaranInvoiceModel->update($id, ['status_posting' => 1]);
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => $result,
            'message' => $result ? "Pembayaran berhasil diposting" : "Terjadi Kesalahan Saat Input Data Transaksi Ke Jurnal Umum"
        ]);
    }
}
