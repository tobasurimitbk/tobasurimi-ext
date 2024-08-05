<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\BarangMasterSalesModel;
use App\Models\CountryModel;
use App\Models\MetadataModel;
use App\Models\SalesKontrakModel;
use App\Models\SalesKontrakDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use Dompdf\Dompdf;

class SalesKontrak extends BaseController
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
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('SalesInternasional/SalesKontrak/index');
    }

    public function createView()
    {
        $dataCustomer = $this->customerModel->getCustomerEkspor($this->this_user_id, $this->this_company_id);
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->where('type_barang_sales', 'EKSPOR')->orderBy('createdAt', "DESC")->findAll();

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang
        ];

        return view('SalesInternasional/SalesKontrak/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);

        $dataSalesKontrak = $this->salesKontrakModel->find($id);
        $dataSalesKontrakDetail = $this->salesKontrakDetailModel->detail($id);
        $dataCustomer = $this->customerModel->getCustomerEkspor($this->this_user_id, $this->this_company_id);
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        $isClosed = $this->salesOrderExportModel->where('sales_contract_id', $id)->findAll();

        if ($dataSalesKontrak == null) {
            return redirect()->to('sales-kontrak');
        }

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            'dataSalesKontrak' => $dataSalesKontrak,
            'dataSalesKontrakDetail' => $dataSalesKontrakDetail,
            'isClosed' => count($isClosed) == 0 ? '0' : '1',
        ];

        return view('SalesInternasional/SalesKontrak/form', $data);
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
                "sales_contract.company_id"    => $this->this_company_id,
            ];
        } else {
            $condition = [
                "sales_contract.company_id"    => $this->this_company_id,
                "createdBy" => $this->this_user_id
            ];
        }


        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status_posting"  => $this->request->getGet("status_posting")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesKontrakData = $this->salesKontrakModel->getList($condition, $addCondition, $limit, $offset);

        $dataSalesKontrak = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesKontrakData['data'] as $data) {
            $isClosed = $this->salesOrderExportModel->where('sales_contract_id', $data->id)->where('deletedAt', null)->findAll();
            // var_dump($isClosed);
            // exit;

            array_push($dataSalesKontrak, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "sales_contract_no"     => $data->sales_contract_no,
                "customer_po_no"        => $data->customer_po_no,
                "customer_name"         => $data->customer_name,
                "dicharge_port"         => strtoupper($data->dicharge_port),
                "shipment_date"         => date('d/m/Y', strtotime($data->shipment_date)),
                "createdAt"             => date('d/m/Y', strtotime($data->createdAt)),
                "status_posting"        => $data->status_posting,
                "status_closed"         => count($isClosed) > 0 ? '1' : '0',
                "keterangan_unpost"         => $data->keterangan_unpost,
                "jumlah_unpost"             => $data->jumlah_unpost,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesKontrakData['totalData'],
            "recordsFiltered"   => $salesKontrakData['totalFilteredData'],
            "data"              => $dataSalesKontrak,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        $barangs = json_decode($_POST['listBarang']);

        if (count($barangs) == 0) {
            return response()->setJSON([
                'message' => "List barang belum ada",
                'status' => false,
                'token' => csrf_token()
            ]);
        }

        $id = $this->salesKontrakModel->insert([
            'company_id' => $this->this_company_id,
            'customer_id' => $this->request->getVar('customer_id'),
            'sales_contract_no' => $this->request->getVar('sales_contract_no'),
            'currency' => $this->request->getVar('currency'),
            'customer_po_no' => $this->request->getVar('customer_po_no'),
            'loading_port' => $this->request->getVar('loading_port'),
            'dicharge_port' => $this->request->getVar('dicharge_port'),
            'due_date' => $this->request->getVar("due_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("due_date")), "Y-m-d") : "",
            'total_amount' => $this->request->getVar('total_amount'),
            'tolerance' => $this->request->getVar('tolerance'),
            'due_date' => $this->request->getVar("due_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("due_date")), "Y-m-d") : "",
            'total_amount' => $this->request->getVar('total_amount'),
            'tolerance' => $this->request->getVar('tolerance'),
            'shipment_date' => $this->request->getVar("due_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("shipment_date")), "Y-m-d") : "",
            'payment_term' => $this->request->getVar('payment_term'),
            'potongan_harga' => $this->request->getVar('potongan_harga'),
            'documents_required' => $this->request->getVar('documents_required'),
            'special_instructions' => $this->request->getVar('special_instructions'),
            'tipe_harga' => $this->request->getVar('tipe_harga'),
            'broker' => $this->request->getVar('broker'),
            'komisi' => $this->request->getVar('komisi'),
            'print_out_broker' => $this->request->getVar('print_out_broker'),
            'createdBy' => $this->this_user_id,
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0'
        ]);

        foreach ($barangs as $b) {
            $this->salesKontrakDetailModel->insert([
                'sales_contract_id' => $id,
                'barang_master_sales_id' => $b->barang_master_sales_id,
                'satuan_order_id' => $b->satuan_order_id,
                'kemasan' => $b->kemasan,
                'remark' => $b->remark,
                'qty' => $b->qty,
                'harga' => $b->harga,
                'total_harga' => $b->total
            ]);
        }

        return response()->setJSON([
            'id' => encrypt($id),
            'message' => "Sales Kontrak Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $barangs = json_decode($_POST['listBarang']);

        if (count($barangs) == 0) {
            return response()->setJSON([
                'message' => "List barang belum ada",
                'status' => false,
                'token' => csrf_token()
            ]);
        }

        $this->salesKontrakModel->update($id, [
            'customer_id' => $this->request->getVar('customer_id'),
            'customer_po_no' => $this->request->getVar('customer_po_no'),
            'currency' => $this->request->getVar('currency'),
            'loading_port' => $this->request->getVar('loading_port'),
            'dicharge_port' => $this->request->getVar('dicharge_port'),
            'due_date' => $this->request->getVar("due_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("due_date")), "Y-m-d") : "",
            'total_amount' => $this->request->getVar('total_amount'),
            'tolerance' => $this->request->getVar('tolerance'),
            'due_date' => $this->request->getVar("due_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("due_date")), "Y-m-d") : "",
            'total_amount' => $this->request->getVar('total_amount'),
            'tolerance' => $this->request->getVar('tolerance'),
            'shipment_date' => $this->request->getVar("due_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("shipment_date")), "Y-m-d") : "",
            'payment_term' => $this->request->getVar('payment_term'),
            'potongan_harga' => $this->request->getVar('potongan_harga'),
            'documents_required' => $this->request->getVar('documents_required'),
            'special_instructions' => $this->request->getVar('special_instructions'),
            'tipe_harga' => $this->request->getVar('tipe_harga'),
            'broker' => $this->request->getVar('broker'),
            'komisi' => $this->request->getVar('komisi'),
            'print_out_broker' => $this->request->getVar('print_out_broker'),
            'createdBy' => $this->this_user_id,
            'keterangan' => $this->request->getVar('keterangan'),
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach ($barangs as $b) {
            // CHECK
            $check = $this->salesKontrakDetailModel
                ->where('sales_contract_id', $id)
                ->where('barang_master_sales_id', $b->barang_master_sales_id)
                ->first();

            if ($check != null) {
                $this->salesKontrakDetailModel->update($check['id'], [
                    'barang_master_sales_id' => $b->barang_master_sales_id,
                    'satuan_order_id' => $b->satuan_order_id,
                    'kemasan' => $b->kemasan,
                    'remark' => $b->remark,
                    'qty' => $b->qty,
                    'harga' => $b->harga,
                    'total_harga' => $b->total
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                $this->salesKontrakDetailModel
                    ->where('sales_contract_id', $id)
                    ->where('barang_master_sales_id', $b->barang_master_sales_id)
                    ->delete();

                // INSERT
                $id_detail_new = $this->salesKontrakDetailModel->insert([
                    'sales_contract_id' => $id,
                    'barang_master_sales_id' => $b->barang_master_sales_id,
                    'satuan_order_id' => $b->satuan_order_id,
                    'kemasan' => $b->kemasan,
                    'remark' => $b->remark,
                    'qty' => $b->qty,
                    'harga' => $b->harga,
                    'total_harga' => $b->total
                ]);

                array_push($id_detail_all,  $id_detail_new);
            }
        }

        $this->salesKontrakDetailModel->where('sales_contract_id', $id)->whereNotIn('id', $id_detail_all)->delete();

        return response()->setJSON([
            'message' => "Sales Kontrak Berhasil Diupdate",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function updateStatus()
    {
        $id = decrypt($this->request->getVar('id'));
        $statusPosting = $this->request->getVar('status');
        $keterangan = $this->request->getPost("keterangan");
        $checkUnpost = $this->salesKontrakModel->find($id);

        $jmlh = (float) $checkUnpost['jumlah_unpost'];

        $this->salesKontrakModel->update($id, [
            'status_posting' => $statusPosting,
            "keterangan_unpost" => $keterangan,
            "jumlah_unpost" => $statusPosting == "0" ? $jmlh + 1 : $jmlh,
        ]);

        return response()->setJSON([
            'message' => "Status Posting Sales Kontrak Berhasil Diupdate",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->salesKontrakModel->delete($id);
        $this->salesKontrakDetailModel->where('sales_contract_id', $id)->delete();

        return response()->setJSON([
            'message' => "Status Posting Sales Kontrak Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function print($id)
    {
        $filename = "Sales Kontrak";
        $id = decrypt($id);

        $salesKontrak = $this->salesKontrakModel->find($id);
        $salesKontrakDetail = $this->salesKontrakDetailModel->detail($id);
        if ($salesKontrak == null) {
            return redirect()->to('sales-kontrak');
        }

        $data = [
            'salesKontrak' => $salesKontrak,
            'customer' => $this->customerModel->find($salesKontrak['customer_id']),
            'salesKontrakdetail' => $salesKontrakDetail
        ];

        $this->dompdf->loadHtml(view('SalesInternasional/SalesKontrak/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));

        exit(0);
    }


    public function dropdownCustomer()
    {
        $dataCustomer = $this->customerModel->getCustomerEkspor($this->this_user_id, $this->this_company_id);
        return response()->setJSON([
            'data' => $dataCustomer,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownMasterBarang()
    {
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        return response()->setJSON([
            'data' => $dataBarang,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getNo()
    {
        $no = $this->salesKontrakModel->get_no(date('Y'), date('y'), $this->this_company_id);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $no
        ]);
    }
}
