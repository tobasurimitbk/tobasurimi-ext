<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CustomerModel;
use App\Models\BarangMasterSalesModel;
use App\Models\CountryModel;
use App\Models\EmployeesModel;
use App\Models\DivisisModel;
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
    protected $employessModel;
    protected $divisiModel;
    protected $bankModel;

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
        $this->employessModel = new EmployeesModel();
        $this->dompdf = new Dompdf();
        $this->divisiModel = new DivisisModel();
        $this->bankModel = new BanksModel();
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
        $dataDivisi = $this->divisiModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->where('type_barang_sales', 'EKSPOR')->orderBy('createdAt', "DESC")->findAll();
        $condition = [
            'jabatan_name' => "SALES INTERNASIONAL"
        ];
        $sales = $this->employessModel->getEmployeesComplete($this->this_company_id, $condition);
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataDivisi" => $dataDivisi,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            "dataSales" => $sales,
            "dataBank" => $dataBank
        ];


        return view('SalesInternasional/SalesKontrak/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);

        $dataSalesKontrak = $this->salesKontrakModel->find($id);
        $dataSalesKontrakDetail = $this->salesKontrakDetailModel->detail($id);
        $dataCustomer = $this->customerModel->getCustomerEkspor($this->this_user_id, $this->this_company_id);
        $dataDivisi = $this->divisiModel->where('company_id', $this->this_company_id)->findAll();
        $dataCountry = $this->countryModel->findAll();
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $dataTipeHarga = $this->metaDataModel->get_by_name('Tipe Harga Sales Ekspor');
        $dataSatuan = $this->satuanModel->findAll();
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->orderBy('createdAt', "DESC")->findAll();
        $isClosed = $this->salesOrderExportModel->where('sales_contract_id', $id)->findAll();
        $condition = [
            'jabatan_name' => "SALES INTERNASIONAL"
        ];
        $sales = $this->employessModel->getEmployeesComplete($this->this_company_id, $condition);
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();

        if ($dataSalesKontrak == null) {
            return redirect()->to('sales-kontrak');
        }

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            "dataDivisi" => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            'dataSalesKontrak' => $dataSalesKontrak,
            'dataSalesKontrakDetail' => $dataSalesKontrakDetail,
            'isClosed' => count($isClosed) == 0 ? '0' : '1',
            "dataSales" => $sales,
            "dataBank" => $dataBank
        ];

        return view('SalesInternasional/SalesKontrak/form', $data);
    }


    public function duplicate($id)
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
        $dataDivisi = $this->divisiModel->where('company_id', $this->this_company_id)->findAll();
        $condition = [
            'jabatan_name' => "SALES"
        ];
        $sales = $this->employessModel->getEmployeesComplete($this->this_company_id, $condition);
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();

        if ($dataSalesKontrak == null) {
            return redirect()->to('sales-kontrak');
        }

        $data = [
            "dataCustomer" => $dataCustomer,
            "dataCountry" => $dataCountry,
            "dataValuta" => $dataValuta,
            "dataTipeHarga" => $dataTipeHarga,
            "dataDivisi" => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'dataBarang' => $dataBarang,
            'dataSalesKontrak' => $dataSalesKontrak,
            'dataSalesKontrakDetail' => $dataSalesKontrakDetail,
            'isClosed' => count($isClosed) == 0 ? '0' : '1',
            "dataSales" => $sales,
            "dataBank" => $dataBank
        ];

        return view('SalesInternasional/SalesKontrak/form_duplicate', $data);
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
                "sales_contract.deletedAt" => null
            ];
        } else {
            $condition = [
                "sales_contract.company_id"    => $this->this_company_id,
                "createdBy" => $this->this_user_id,
                "sales_contract.deletedAt" => null
            ];
        }


        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status_posting"  => $this->request->getGet("status_posting"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
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

        // Validasi Nomor Sales Kontrak
        $salesContractNo = $this->request->getVar('sales_contract_no');
        $salesContract = $this->salesKontrakModel->where('sales_contract_no', $salesContractNo)
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->first();

        if ($salesContract != null) {
            return response()->setJSON([
                'message' => "Nomor sales kontrak sudah digunakan oleh anda atau sales lain",
                'status' => false,
                'token' => csrf_token()
            ]);
        }

        $id = $this->salesKontrakModel->insert([
            'company_id' => $this->this_company_id,
            'customer_id' => $this->request->getVar('customer_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
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
            'shipment_date' => $this->request->getVar("shipment_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("shipment_date")), "Y-m-d") : "",
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
            'status_posting' => '0',
            'bank_id' => $this->request->getVar('bank_id'),
            'spesifikasi' => $this->request->getVar('spesifikasi'),
            'no_container' => $this->request->getVar('no_container')
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
                'total_harga' => $b->total,
                'size' => $b->size
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


        // Validasi Nomor Sales Kontrak
        $salesContractNo = $this->request->getVar('sales_contract_no');
        $salesContract = $this->salesKontrakModel->where('sales_contract_no', $salesContractNo)
            ->where('company_id', $this->this_company_id)
            ->where('id !=', $id)
            ->where('deletedAt', null)
            ->first();

        if ($salesContract != null) {
            return response()->setJSON([
                'message' => "Nomor sales kontrak sudah digunakan oleh anda atau sales lain",
                'status' => false,
                'token' => csrf_token()
            ]);
        }

        $this->salesKontrakModel->update($id, [
            'sales_contract_no' => $this->request->getVar('sales_contract_no'),
            'customer_id' => $this->request->getVar('customer_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
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
            'shipment_date' => $this->request->getVar("shipment_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("shipment_date")), "Y-m-d") : "",
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
            'bank_id' => $this->request->getVar('bank_id'),
            'spesifikasi' => $this->request->getVar('spesifikasi'),
            'no_container' => $this->request->getVar('no_container')
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
                    'total_harga' => $b->total,
                    'size' => $b->size
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
                    'total_harga' => $b->total,
                    'size' => $b->size
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

        $salesKontrak = $this->salesKontrakModel
            ->select(
                '
                sales_contract.*,
                customers.name as customer_name,
                banks.kode_bank,
                banks.name as nama_bank,
                banks.atas_nama,
                banks.no_rekening,
                metadata.value as mata_uang
                '
            )
            ->join('customers', 'sales_contract.customer_id = customers.id', 'left')
            ->join('banks', 'banks.id = sales_contract.bank_id', 'left')
            ->join('metadata', 'metadata.id = sales_contract.currency', 'left')
            ->find($id);
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
        $dataBarang = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)
            ->where('type_barang_sales', 'EKSPOR')
            ->orderBy('createdAt', "DESC")
            ->findAll();
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
