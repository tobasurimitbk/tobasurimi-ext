<?php

namespace App\Controllers\Pembayaran;

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
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\MetadataModel;
use App\Models\PembayaranInvoiceDetailModel;
use App\Models\SalesOrderReturnDetailModel;
use App\Models\SalesOrderReturnModel;
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
    protected $salesOrderInvoiceModel;
    protected $salesOrderInvoiceDetailModel;
    protected $salesOrderReturnModel;
    protected $salesOrderReturnDetailModel;
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
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
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
        $dokumenList = [];
        $customers = $this->customerModel->getCustomerLokal($this->user_id, $this->this_company_id);
        $divisi = $this->divisiModel->getDivisiAccess();
        $salesOrderReturnData = $this->salesOrderReturnModel
            ->where('deletedAt', null)
            ->where('id_company', $this->this_company_id)
            ->findAll();
        foreach ($salesOrderReturnData as $s) {
            array_push($dokumenList, $s);
        }

        $data = [
            "customers" => $customers,
            "divisi" => $divisi,
            "subsAkuns" => $subAkunsModel,
            "dokumenList" => $dokumenList,
            "detail" => ""
        ];
        return view('Pembayaran/pembayaranInvoice/formReturn', $data);
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
            ->where('sales_order_invoice.id_company', $this->this_company_id)
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

    public function getDataDokumenInvoiceLokal($customer_id)
    {
        $customer_id_decrypt = decrypt($customer_id);
        $dokumenList = [];
        $salesOrderLokalInvoiceData = $this->salesOrderInvoiceModel->where('deletedAt', null)
            ->where('id_company', $this->this_company_id)
            ->where('id_customer', $customer_id_decrypt)
            ->where('pay_amount < total_invoice') // Menambahkan kondisi pay_amount lebih kecil dari total_invoice
            ->findAll();
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
        return response()->setJSON([
            'data' => $dokumenList,
            'token' => csrf_hash(),
            'success' => true,
        ]);
    }

    public function generateNoPembayaranInvoice()
    {

        $paymentNo = "PI/";
        $month = date('m');
        $year = date('Y');

        $numberTemplate = $paymentNo . "$year/$month/";
        $lastData = $this->pembayaranInvoiceModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->like('no_pembayaran', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $paymentNo = "{$numberTemplate}001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->no_pembayaran);
            $lastIncrement = (int)$exploded[3] + 1;

            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);
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
            $typeInvoice = ['EKSPOR', 'LOKAL', 'LAIN-LAIN', 'RETURN'];
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
               $nomor_invoice = implode(', ', array_map(function($item) {
                   return $item['no_faktur'];  // Mengambil no_faktur dari setiap hasil query
               }, $salesOrderLokalInvoiceData));

               $customerName = $this->customerModel->where('id', $p['customer_id'])->select('name')->first();
               if (empty($customerName)) {
                   $customer_name = implode(', ', array_map(function($item) {
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
            } elseif ($p['type_invoice'] == "LAIN-LAIN") {
                $salesOrderLainData = $this->salesOrderLainModel
                    ->join('customers', 'customers.id = sales_order_lain.customer_id')
                    ->where('sales_order_lain.id', $p['invoice_id'])
                    ->first();
                $nomor_invoice = $salesOrderLainData['no_sales_order'];
                $customer_name = $salesOrderLainData['name'];
            } elseif ($p['type_invoice'] == "RETURN") {
                $salesOrderReturnData = $this->salesOrderReturnModel
                    ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice')
                    ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                    ->where('sales_order_return.id', $p['invoice_id'])
                    ->first();
                $nomor_invoice = $salesOrderReturnData['no_return'];
                $customer_name = $salesOrderReturnData['name'];
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
        $totalPembayaran = 0;
        $totalAmountInvoice = 0;

       if(!empty($idArray)) {
         // Ambil data invoice berdasarkan semua ID
         $salesOrderInvoiceData = $this->salesOrderInvoiceModel->select('pay_amount')->whereIn('id', $idArray)->first();
        // Gunakan ternary untuk memeriksa apakah pay_amount null, jika ya beri nilai 0
        $payAmount = $salesOrderInvoiceData['pay_amount'] ?? 0;


         $salesOrderInvoiceDetailData = $this->salesOrderInvoiceDetailModel
             ->select('sales_order_invoice.no_faktur, sales_order_invoice_detail.id as sales_order_invoice_detail_id, sales_order_invoice_detail.id_sales_order_invoice as sales_order_invoice_id, qty_invoice, harga_barang_invoice, qty_invoice, amount_invoice, kode_barang, barang_name')
             ->join('barang_master_sales', 'sales_order_invoice_detail.id_barang_invoice = barang_master_sales.id')
             ->join('sales_order_invoice', 'sales_order_invoice_detail.id_sales_order_invoice = sales_order_invoice.id', 'left')
             ->whereIn('id_sales_order_invoice', $idArray)
             ->where('sales_order_invoice_detail.deletedAt', null)
             ->findAll();
 
         // Jika ada ID pembayaran, tambahkan detailnya
         if (!empty($pembayaranInvoiceId)) {
             $pembayaranInvoiceDatail = $this->pembayaranInvoiceDetailModel
                 ->where('pembayaran_invoice_id', $pembayaranInvoiceId)
                 ->where('sales_order_invoice_id', null)
                 ->findAll();
         }
 
         // Proses data dari detail invoice
         foreach ($salesOrderInvoiceDetailData as $s) {
             $totalAmountInvoice += $s['amount_invoice'];
             array_push($dataBarang, $s);
         }
 
         // Tambahkan data lain-lain jika ada
         if (isset($pembayaranInvoiceDatail)) {
             foreach ($pembayaranInvoiceDatail as $p) {
                 array_push($dataBarang, [
                     "sales_order_invoice_detail_id" => $p['sales_order_invoice_detail_id'],
                     "sales_order_invoice_id" => $p['sales_order_invoice_id'],
                     "qty_invoice" => $p['qty'],
                     "harga_barang_invoice" => $p['harga_satuan'],
                     "amount_invoice" => $p['harga_total'],
                     "kode_barang" => "LAIN-LAIN",
                     "barang_name" => $p['nama_barang']
                 ]);
             }
         }
 
         // Hitung total pembayaran
         if(!empty($pembayaranInvoiceId)) {
             $pembayaranInvoiceData = $this->pembayaranInvoiceModel
             ->select('total_bayar') // Hanya memilih kolom 'total_bayar'
             ->where('id', $pembayaranInvoiceId) // Filter berdasarkan ID
             ->first(); // Ambil data pertama
         } else {
             $pembayaranInvoiceData = NULL;
         }
       }

        // Return data
        return $this->response->setJSON([
            'data' => $dataBarang,
            'totalPembayaran' => $pembayaranInvoiceData['total_bayar'] ?? 0,
            'totalSudahDiBayar' => $payAmount,
            'status' => true
        ]);
    }


    public function getBarangSalesEkspor()
    {
        $id = decrypt($this->request->getVar('id'));
        $dataBarang = [];
        $totalPembayaran = 0;
        $totalAmountInvoice = 0;
        $salesOrderInvoiceData = $this->salesOrderInvoiceModel->where('id', $id)->first();
        $salesOrderExportDetailData = $this->salesOrderExportDetailModel
            ->select('harga_barang, qty, total_harga_barang, barang_kode, barang_name')
            ->where('sales_order_detail_export.sales_order_export_id', $id)
            ->where('sales_order_detail_export.deletedAt', null)
            ->findAll();
        foreach ($salesOrderExportDetailData as $s) {
            $totalAmountInvoice += $s['total_harga_barang'];
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

    public function getBarangSalesReturn()
    {
        $id = decrypt($this->request->getVar('id'));
        $pembayaranreturnId = decrypt($this->request->getVar('pembayaran_invoice_id'));

        $dataBarang = [];
        $totalPembayaran = 0;
        $totalAmountreturn = 0;
        $salesOrderreturnData = $this->salesOrderReturnModel->where('id', $id)->first();
        $salesOrderreturnDetailData = $this->salesOrderReturnDetailModel
            ->select('sales_order_return_detail.id as sales_order_return_detail_id, sales_order_return_detail.id_sales_order_return as sales_order_return_id, qty_return, harga_barang_return, qty_return, amount_return, kode_barang, barang_name')
            ->join('barang_master_sales', 'sales_order_return_detail.id_barang_return = barang_master_sales.id')
            ->where('id_sales_order_return', $id)
            ->where('sales_order_return_detail.deletedAt', null)
            ->findAll();

        if ($pembayaranreturnId) {
            $pembayaranInvoiceDetail = $this->pembayaranInvoiceDetailModel->where('pembayaran_invoice_id', $pembayaranreturnId)->where('sales_order_invoice_id', null)->findAll();
        }

        foreach ($salesOrderreturnDetailData as $s) {
            $totalAmountreturn += $s['amount_return'];
            array_push($dataBarang, $s);
        }

        if (isset($pembayaranInvoiceDetail)) {
            foreach ($pembayaranInvoiceDetail as $p) {
                array_push($dataBarang, [
                    "sales_order_return_detail_id" => $p['sales_order_invoice_detail_id'],
                    "sales_order_return_id" => $p['sales_order_invoice_id'],
                    "qty_return" => $p['qty'],
                    "harga_barang_return" => $p['harga_satuan'],
                    "amount_return" => $p['harga_total'],
                    "kode_barang" => "LAIN-LAIN",
                    "barang_name" => $p['nama_barang']
                ]);
            }
        }

        $pembayaranreturnData = $this->pembayaranInvoiceModel
            ->where('pembayaran_invoice.company_id', $this->this_company_id)
            ->where('pembayaran_invoice.invoice_id', $id)
            ->where('pembayaran_invoice.deletedAt', null)
            ->where('pembayaran_invoice.type_invoice', "RETURN")
            ->findAll();
        foreach ($pembayaranreturnData as $s) {
            $totalPembayaran += $s['total_bayar'];
        }

        // if ($totalAmountreturn >= $totalPembayaran) {
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

        $no_dokumen_req = $this->request->getVar("no_dokumen");
        $no_dokumen_implode = "[" . implode("','", $no_dokumen_req) . "]";

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

                //update py_amount untuk metode partials
                foreach ($no_dokumen_req as $inv_id) {
                    $this->salesOrderInvoiceModel->update($inv_id, [
                        'pay_amount' => repairDouble($this->request->getVar('total_bayar')),
                    ]);
                }

                $id = $this->pembayaranInvoiceModel->insert([
                    'company_id' => $this->this_company_id,
                    'user_id' => $this->user_id,
                    'divisi_id' => $this->request->getVar('divisi_id'),
                    'payment_method' => $this->request->getVar('payment_methods'),
                    'customer_id' => decrypt($this->request->getVar('customer')),
                    'invoice_id' => $no_dokumen_implode,
                    'valas_id' => "-",
                    'no_pembayaran' => $this->request->getVar('no_bukti_pembayaran'),
                    'keterangan' =>  $this->request->getVar('keterangan'),
                    'type_invoice' => "LOKAL",
                    'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                    'total_invoice' => repairDouble($this->request->getVar('total_amount_invoice')),
                    'potongan' => $this->request->getVar('potongan') ? repairDouble($this->request->getVar('potongan')) : 0,
                    'total_bayar' => repairDouble($this->request->getVar('total_bayar')),
                    'akun_kas' => $this->request->getVar('akun_kas'),
                    'akun_selisih' => $this->request->getVar('akun_selisih'),
                    'akun_kas_lain' => $this->request->getVar('akun_kas_lain'),
                    'akun_selisih_lain' => $this->request->getVar('akun_selisih_lain'),
                    'status_posting' => '0'
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
                        'harga_total' => $l->amount_invoice
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
                    'valas_id' => "-",
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
                $id = $this->pembayaranInvoiceModel->insert([
                    'company_id' => $this->this_company_id,
                    'user_id' => $this->user_id,
                    'payment_method' => $this->request->getVar('payment_methods'),
                    'invoice_id' => decrypt($this->request->getVar('no_dokumen')),
                    'valas_id' => $this->request->getVar('valas'),
                    'no_pembayaran' => $this->request->getVar('no_bukti_pembayaran'),
                    'keterangan' =>  $this->request->getVar('keterangan'),
                    'type_invoice' => "EKSPOR",
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
                    'message' => "Pembayaran Invoice Ekspor berhasil disimpan",
                    'token' => csrf_hash()
                ]);
            } elseif ($tipe_invoice == "RETURN") {
                $id = $this->pembayaranInvoiceModel->insert([
                    'company_id' => $this->this_company_id,
                    'user_id' => $this->user_id,
                    'payment_method' => $this->request->getVar('payment_methods'),
                    'invoice_id' => decrypt($this->request->getVar('no_dokumen')),
                    'valas_id' => $this->request->getVar('valas'),
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


                foreach (json_decode($_POST['tableData']) as $l) {
                    $this->pembayaranInvoiceDetailModel->insert([
                        'pembayaran_invoice_id' => $id,
                        'sales_order_invoice_id' => (!isset($l->sales_order_return_id) || $l->sales_order_return_id == 0) ? null : $l->sales_order_return_id,
                        'sales_order_invoice_detail_id' => (!isset($l->sales_order_return_detail_id) || $l->sales_order_return_detail_id == 0) ? null : $l->sales_order_return_detail_id,
                        'type_invoice' => "RETUR",
                        'nama_barang' => $l->nama_barang,
                        'qty' => $l->qty,
                        'harga_satuan' => $l->harga_satuan,
                        'harga_total' => $l->amount
                    ]);
                }

                return response()->setJSON([
                    'id' => encrypt($id),
                    'status' => true,
                    'message' => "Pembayaran Invoice Return berhasil disimpan",
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
                'total_bayar' => repairDouble($this->request->getVar('total_bayar')),
                'akun_kas' => $this->request->getVar('akun_kas'),
                'akun_selisih' => $this->request->getVar('akun_selisih'),
                'akun_kas_lain' => $this->request->getVar('akun_kas_lain'),
                'akun_selisih_lain' => $this->request->getVar('akun_selisih_lain'),
                'status_posting' => '0',
                'payment_method' => $this->request->getVar('payment_methods')
            ]);

            $pembayaranInvoiceFirst = $this->pembayaranInvoiceModel->find($id);
            // Delete Detail Lalu Insert Again
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
                    'harga_total' => $l->amount_invoice
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
                ->where('sales_order_invoice.status_pelunasan', 'UNPAID')
                ->where('sales_order_invoice.id_company', $this->this_company_id)
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

            $salesOrderReturnData = $this->salesOrderReturnModel
                ->where('deletedAt', null)
                ->where('id_company', $this->this_company_id)
                ->findAll();
            foreach ($salesOrderReturnData as $s) {
                array_push($dokumenList, $s);
            }
            $data = [
                "customers" => $customers,
                "divisi" => $divisi,
                "subsAkuns" => $subAkunsModel,
                "dokumenList" => $dokumenList,
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
