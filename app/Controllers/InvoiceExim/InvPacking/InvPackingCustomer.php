<?php

namespace App\Controllers\InvoiceExim\InvPacking;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\InvPackingCustomerBarangModel;
use App\Models\InvPackingCustomerBarangSizeModel;
use App\Models\InvPackingCustomerBiayaModel;
use App\Models\InvPackingCustomerModel;
use App\Models\InvPackingCustomerPackModel;
use App\Models\InvPackingCustomerPackSizeModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use Dompdf\Dompdf;
use Exception;

class InvPackingCustomer extends BaseController
{
    protected $this_company_id;
    protected $salesOrderExportModel;
    protected $metaDataModel;
    protected $divisiModel;
    protected $bankModel;
    protected $satuanModel;
    protected $companyModel;
    protected $hsCodeModel;
    protected $invPackingCustomerModel;
    protected $invPackingCustomerBarangModel;
    protected $invPackingCustomerBarangSizeModel;
    protected $invPackingCustomerBiayaModel;
    protected $invPackingCustomerPackModel;
    protected $invPackingCustomerPackSizeModel;
    protected $dompdf;

    public function __construct()
    {
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->bankModel = new BanksModel();
        $this->satuanModel = new SatuansModel();
        $this->companyModel = new CompaniesModel();
        $this->hsCodeModel = new HsCodesModel();
        $this->invPackingCustomerModel = new InvPackingCustomerModel();
        $this->invPackingCustomerBarangModel = new InvPackingCustomerBarangModel();
        $this->invPackingCustomerBarangSizeModel = new InvPackingCustomerBarangSizeModel();
        $this->invPackingCustomerPackModel = new InvPackingCustomerPackModel();
        $this->invPackingCustomerPackSizeModel = new InvPackingCustomerPackSizeModel();
        $this->invPackingCustomerBiayaModel = new InvPackingCustomerBiayaModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('InvoiceExim/InvPackingCustomer/index');
    }

    public function allOrderForm()
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
            "sales_order_export.deletedAt" => null,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportModel->getListPeb($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            $jumlahInvoice = count($this->invPackingCustomerModel->where('sales_order_export_id', $data->sales_order_export_id)->where('deletedAt', null)->findAll());

            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => \encrypt($data->sales_order_export_id),
                "no_invoice"                => $data->no_invoice,
                "tanggal_invoice"           => $data->tanggal_invoice != "" ? date('d/m/Y', strtotime($data->tanggal_invoice)) : "-",
                "customer_name"             => $data->customer_name,
                "sales_order_export_no"     => $data->sales_order_export_no,
                "dicharge_port"             => $data->dicharge_port,
                "status_invoice"            => $jumlahInvoice == 0 ? 0 : 1,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesData['totalData'],
            "recordsFiltered"   => $salesData['totalFilteredData'],
            "data"              => $dataSales,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function allInvoice()
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
            "inv_packing_customer.sales_order_export_id"    => $this->request->getVar('sales_order_export_id'),
            "inv_packing_customer.deletedAt" => null,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "status_posting"    => $this->request->getVar('status_posting')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $invData = $this->invPackingCustomerModel->getList($condition, $addCondition, $limit, $offset);

        $dataInv = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($invData['data'] as $data) {

            array_push($dataInv, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "no_container"      => $data->no_container,
                "vessels_name"           => $data->vessels_name,
                "no_seal"           => $data->no_seal,
                "departure_date"    => $data->departure_date != "" ? date('d/m/Y', strtotime($data->departure_date)) : "-",
                "total_berat_bersih" => (float)$data->total_berat_bersih,
                "total_berat_kotor"  => (float)$data->total_berat_kotor,
                "total_nilai_invoice" => (float)$data->total_nilai_invoice,
                "valas_name"            => $data->valas_name,
                "status_posting"            => $data->status_posting,
                "status_bayar"            => $data->status_bayar,

            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $invData['totalData'],
            "recordsFiltered"   => $invData['totalFilteredData'],
            "data"              => $dataInv,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }



    public function indexInvPackingCustomer($id)
    {
        $id = decrypt($id);
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($id);
        $dataCompany = $this->companyModel->whereIn('id', [1, 2])->findAll();
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-customer');
        }

        // dd($dataSalesOrderExport);

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataCompany' => $dataCompany
        ];

        return view('InvoiceExim/InvPackingCustomer/indexInv', $data);
    }

    public function createPackingCustomer($id)
    {
        $id = decrypt($id);
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($id);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-customer');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataHsCode = $this->hsCodeModel->findAll();

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataHsCode' => $dataHsCode
        ];

        //dd($data['dataSalesOrderExport']);

        return view('InvoiceExim/InvPackingCustomer/formInv', $data);
    }

    public function updatePackingCustomer($id)
    {
        $id = decrypt($id);
        $dataInvoice = $this->invPackingCustomerModel->where('id', $id)->first();
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataInvoice['sales_order_export_id']);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-customer');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataHsCode = $this->hsCodeModel->findAll();
        $dataListBarang = $this->invPackingCustomerBarangModel->getByInvId(
            $id
        );
        $dataListPacking = $this->invPackingCustomerPackModel->getByInvId(
            $id
        );
        $dataListBiayaTambahan = $this->invPackingCustomerBiayaModel->getByInvId(
            $id
        );
        $dataCompany = $this->companyModel->whereIn('id', [1, 2])->findAll();

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataHsCode' => $dataHsCode,
            'dataInvoice' => $dataInvoice,
            'dataListBarang' => $dataListBarang,
            'dataListPacking' => $dataListPacking,
            'dataListBiayaTambahan' => $dataListBiayaTambahan,
            'dataCompany' => $dataCompany
        ];

        return view('InvoiceExim/InvPackingCustomer/formInv', $data);
    }

    public function duplicatePackingCustomer($id)
    {
        $id = decrypt($id);
        $dataInvoice = $this->invPackingCustomerModel->where('id', $id)->first();
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataInvoice['sales_order_export_id']);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-customer');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataHsCode = $this->hsCodeModel->findAll();
        $dataListBarang = $this->invPackingCustomerBarangModel->getByInvId(
            $id
        );
        $dataListPacking = $this->invPackingCustomerPackModel->getByInvId(
            $id
        );
        $dataListBiayaTambahan = $this->invPackingCustomerBiayaModel->getByInvId(
            $id
        );

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataHsCode' => $dataHsCode,
            'dataInvoice' => $dataInvoice,
            'dataListBarang' => $dataListBarang,
            'dataListPacking' => $dataListPacking,
            'dataListBiayaTambahan' => $dataListBiayaTambahan
        ];

        return view('InvoiceExim/InvPackingCustomer/formInv_duplicate', $data);
    }

    public function printPackingCustomer($id)
    {
        $id = decrypt($id);
        $companyId = $this->request->getVar('company_id');
        if (empty($companyId)) {
            $companyId = $this->this_company_id;
        }
        $dataInvoice = $this->invPackingCustomerModel
            ->select('inv_packing_customer.*,metadata.value as valas_name')
            ->join('metadata', 'metadata.id = inv_packing_customer.valas_id', 'left')
            ->where('inv_packing_customer.id', $id)
            ->first();

        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataInvoice['sales_order_export_id']);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-customer');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataHsCode = $this->hsCodeModel->findAll();
        $dataListBarang = $this->invPackingCustomerBarangModel->getByInvId(
            $id
        );
        $dataListPacking = $this->invPackingCustomerPackModel->getByInvId(
            $id
        );
        $dataListBiayaTambahan = $this->invPackingCustomerBiayaModel->getByInvId(
            $id
        );
        $company = $this->companyModel->where('id', $companyId)->first();

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataHsCode' => $dataHsCode,
            'dataInvoice' => $dataInvoice,
            'dataListBarang' => $dataListBarang,
            'dataListPacking' => $dataListPacking,
            'dataListBiayaTambahan' => $dataListBiayaTambahan,
            'company'   => $company
        ];

        $this->dompdf->loadHtml(view('InvoiceExim/InvPackingCustomer/print', $data));
        $this->dompdf->setPaper('legal', 'portrait');
        $this->dompdf->render();

        // Output PDF
        $filename = $dataInvoice['no_container'];
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function store()
    {
        // return response()->setJSON([
        //     '_POST' => $_POST,
        //     'listBarang' => json_decode($_POST['listBarang']),
        //     'listPacking' => json_decode($_POST['listPacking']),
        //     'listBiayaTambahan' => json_decode($_POST['listBiayaTambahan'])
        // ]);
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $invPCId = $this->invPackingCustomerModel->insert([
                'company_id' => $this->this_company_id,
                'sales_order_export_id' => $this->request->getVar('sales_order_export_id'),
                'tanggal_invoice' => $this->request->getVar("tanggal_invoice") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : "",
                'nama_customer' => $this->request->getVar('nama_customer'),
                'loading_port' => $this->request->getVar('loading_port'),
                'dicharge_port' => $this->request->getVar('dicharge_port'),
                'departure_date' => $this->request->getVar("departure_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("departure_date")))) : "",
                'vessels_name' => $this->request->getVar('vessels_name'),
                'valas_id' => $this->request->getVar('valas_id'),
                'payment_term' => $this->request->getVar('payment_term'),
                'alamat' => $this->request->getVar('alamat'),
                'notify_party' => $this->request->getVar('notify_party'),
                'notify_party2' => $this->request->getVar('notify_party2'),
                'no_container' => $this->request->getVar('no_container'),
                'no_seal' => $this->request->getVar('no_seal'),
                'country_of_origin' => $this->request->getVar('country_of_origin'),
                'penanda_tangan' => $this->request->getVar('penanda_tangan'),
                'payment_description' => $this->request->getVar('payment_description'),
                'measurement' => $this->request->getVar('measurement'),
                'total_nilai_invoice' => $this->request->getVar('total_nilai_invoice'),
                'total_berat_bersih' => $this->request->getVar('total_berat_bersih'),
                'total_berat_kotor' => $this->request->getVar('total_berat_kotor')
            ]);

            $this->insertDetailPackCustomer($invPCId);
            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Commercial Invoice Customer Berhasil Disimpan"
            ]);

            // return response()->setJSON([
            //     '_POST' => $_POST,
            //     'listBarang' => json_decode($_POST['listBarang']),
            //     'listPacking' => json_decode($_POST['listPacking']),
            //     'listBiayaTambahan' => json_decode($_POST['listBiayaTambahan'])
            // ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        $db = \Config\Database::connect();
        try {
            $id = decrypt($this->request->getVar('id'));
            $db->transBegin();

            $this->invPackingCustomerModel->update($id, [
                'sales_order_export_id' => $this->request->getVar('sales_order_export_id'),
                'tanggal_invoice' => $this->request->getVar("tanggal_invoice") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : "",
                'nama_customer' => $this->request->getVar('nama_customer'),
                'loading_port' => $this->request->getVar('loading_port'),
                'dicharge_port' => $this->request->getVar('dicharge_port'),
                'departure_date' => $this->request->getVar("departure_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("departure_date")))) : "",
                'vessels_name' => $this->request->getVar('vessels_name'),
                'valas_id' => $this->request->getVar('valas_id'),
                'payment_term' => $this->request->getVar('payment_term'),
                'alamat' => $this->request->getVar('alamat'),
                'notify_party' => $this->request->getVar('notify_party'),
                'notify_party2' => $this->request->getVar('notify_party2'),
                'no_container' => $this->request->getVar('no_container'),
                'no_seal' => $this->request->getVar('no_seal'),
                'country_of_origin' => $this->request->getVar('country_of_origin'),
                'penanda_tangan' => $this->request->getVar('penanda_tangan'),
                'payment_description' => $this->request->getVar('payment_description'),
                'measurement' => $this->request->getVar('measurement'),
                'total_nilai_invoice' => $this->request->getVar('total_nilai_invoice'),
                'total_berat_bersih' => $this->request->getVar('total_berat_bersih'),
                'total_berat_kotor' => $this->request->getVar('total_berat_kotor')
            ]);

            // Hapus DUlu Purge
            $this->invPackingCustomerBarangModel->where('inv_packing_customer_id', $id)->delete(null, true);
            $this->invPackingCustomerBarangSizeModel->where('inv_packing_customer_id', $id)->delete(null, true);
            $this->invPackingCustomerPackModel->where('inv_packing_customer_id', $id)->delete(null, true);
            $this->invPackingCustomerPackSizeModel->where('inv_packing_customer_id', $id)->delete(null, true);
            $this->invPackingCustomerBiayaModel->where('inv_packing_customer_id', $id)->delete(null, true);

            $this->insertDetailPackCustomer($id);
            $db->transCommit();

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Commercial Invoice Customer Berhasil Diupdate"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->invPackingCustomerModel->delete($id);
        $this->invPackingCustomerBarangModel->where('inv_packing_customer_id', $id)->delete(null, false);
        $this->invPackingCustomerBarangSizeModel->where('inv_packing_customer_id', $id)->delete(null, false);
        $this->invPackingCustomerPackModel->where('inv_packing_customer_id', $id)->delete(null, false);
        $this->invPackingCustomerPackSizeModel->where('inv_packing_customer_id', $id)->delete(null, false);
        $this->invPackingCustomerBiayaModel->where('inv_packing_customer_id', $id)->delete(null, false);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Commercial Invoice Customer Berhasil Dihapus"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->invPackingCustomerModel->update($id, [
            'status_posting' => 1
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Commercial Invoice Customer Berhasil Diposting"
        ]);
    }

    public function unposting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->invPackingCustomerModel->update($id, [
            'status_posting' => 0
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Commercial Invoice Customer Berhasil Diunposting"
        ]);
    }

    private function insertDetailPackCustomer($id)
    {
        foreach (json_decode($_POST['listBarang']) as $l) {
            $invPCBarangId = $this->invPackingCustomerBarangModel->insert([
                'inv_packing_customer_id' => $id,
                'nama_barang' => $l->nama_barang,
                'catatan' => $l->catatan
            ]);

            foreach ($l->size_breakdown as $s) {
                $this->invPackingCustomerBarangSizeModel->insert([
                    'inv_packing_customer_id' => $id,
                    'inv_packing_customer_barang_id' => $invPCBarangId,
                    'satuan_size_id' => $s->satuan_size_id,
                    'size' => $s->size,
                    'grade' => $s->grade,
                    'qty' => $s->qty,
                    'harga' => $s->harga,
                    'total' => $s->total
                ]);
            }
        }

        foreach (json_decode($_POST['listPacking']) as $l) {
            $invPCPackId = $this->invPackingCustomerPackModel->insert([
                'inv_packing_customer_id' => $id,
                'hs_code_id' => $l->hs_code,
                'nama_barang_packing' => $l->nama_barang_packing,
                'keterangan_packing' => $l->keterangan_packing,
            ]);

            foreach ($l->size_breakdown as $s) {
                $this->invPackingCustomerPackSizeModel->insert([
                    'inv_packing_customer_id' => $id,
                    'inv_packing_customer_pack_id' => $invPCPackId,
                    'satuan_size_id' => $s->satuan_size_id,
                    'size' => $s->size,
                    'grade' => $s->grade,
                    'packing' => $s->packing,
                    'can' => $s->can,
                    'kg' => $s->kg,
                    'lb' => $s->lb,
                    'inner_box' => $s->inner_box,
                    'pc' => $s->pc,
                    'bag' => $s->bag,
                    'palet' => $s->palet,
                    'persen' => $s->persen,
                    'qty' => $s->qty,
                    'harga' => $s->harga,
                    'total' => $s->total,
                    'berat_bersih' => $s->berat_bersih,
                    'berat_kotor' => $s->berat_kotor,
                    'vgm' => $s->vgm,
                    'drammed' => $s->drammed,
                    'cased' => $s->case,
                    'cup' => $s->cup,
                ]);
            }
        }

        foreach (json_decode($_POST['listBiayaTambahan']) as $l) {
            $this->invPackingCustomerBiayaModel->insert([
                'inv_packing_customer_id' => $id,
                'biaya_tambahan' => $l->biaya_tambahan,
                'tipe_biaya_tambahan' => $l->tipe_biaya_tambahan,
                'nilai_biaya_tambahan' => $l->nilai_biaya_tambahan

            ]);
        }
    }
}
