<?php

namespace App\Controllers\InvoiceExim\InvPackingBC;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\InvPackingBcBarangModel;
use App\Models\InvPackingBcBiayaModel;
use App\Models\InvPackingBcModel;
use App\Models\InvPackingBcPackModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use Dompdf\Dompdf;
use Exception;

class InvPackingBC extends BaseController
{

    protected $this_company_id;
    protected $salesOrderExportModel;
    protected $metaDataModel;
    protected $divisiModel;
    protected $bankModel;
    protected $satuanModel;
    protected $companyModel;
    protected $hsCodeModel;
    protected $invPackingBcModel;
    protected $invPackingBcBarangModel;
    protected $invPackingBcBiayaModel;
    protected $invPackingBcPackModel;
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
        $this->invPackingBcModel = new InvPackingBcModel();
        $this->invPackingBcBarangModel = new InvPackingBcBarangModel();
        $this->invPackingBcBiayaModel = new InvPackingBcBiayaModel();
        $this->invPackingBcPackModel = new InvPackingBcPackModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('InvoiceExim/InvPackingBC/index');
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
            $jumlahInvoice = count($this->invPackingBcModel->where('sales_order_export_id', $data->sales_order_export_id)->where('deletedAt', null)->findAll());

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

    public function indexInvPackingBC($id)
    {
        $id = decrypt($id);
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($id);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-bc');
        }

        // dd($dataSalesOrderExport);

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport
        ];

        return view('InvoiceExim/InvPackingBC/indexInv', $data);
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
            "inv_packing_bc.sales_order_export_id"    => $this->request->getVar('sales_order_export_id'),
            "inv_packing_bc.deletedAt" => null,
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
        $invData = $this->invPackingBcModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

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

    public function createPackingBC($id)
    {
        $id = decrypt($id);
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($id);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-bc');
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

        return view('InvoiceExim/InvPackingBC/formInv', $data);
    }

    public function duplicatePackingBC($id)
    {
        $id = decrypt($id);
        $dataInvoice = $this->invPackingBcModel->where('id', $id)->first();
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataInvoice['sales_order_export_id']);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-bc');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataHsCode = $this->hsCodeModel->findAll();
        $dataListBarang = $this->invPackingBcBarangModel->getByInvId($id);
        $dataListPacking = $this->invPackingBcPackModel->getByInvId($id);
        $dataListBiayaTambahan = $this->invPackingBcBiayaModel->getByInvId($id);

        $data = [
            'dataInvoice' => $dataInvoice,
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataHsCode' => $dataHsCode,
            'dataListBarang' => $dataListBarang,
            'dataListPacking' => $dataListPacking,
            'dataListBiayaTambahan' => $dataListBiayaTambahan
        ];

        return view('InvoiceExim/InvPackingBC/formInv_duplicate', $data);
    }

    public function printPackingBC($id)
    {
        $id = decrypt($id);
        $dataInvoice = $this->invPackingBcModel
            ->select('inv_packing_bc.*,metadata.value as valas_name')
            ->join('metadata', 'metadata.id = inv_packing_bc.valas_id', 'left')
            ->where('inv_packing_bc.id', $id)
            ->first();
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataInvoice['sales_order_export_id']);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-bc');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataHsCode = $this->hsCodeModel->findAll();
        $dataListBarang = $this->invPackingBcBarangModel->getByInvId($id);
        $dataListPacking = $this->invPackingBcPackModel->getByInvIdPrint($id);
        $dataListBiayaTambahan = $this->invPackingBcBiayaModel->getByInvId($id);
        $company = $this->companyModel->where('id', $dataInvoice['company_id'])->first();

        $data = [
            'dataInvoice' => $dataInvoice,
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataHsCode' => $dataHsCode,
            'dataListBarang' => $dataListBarang,
            'dataListPacking' => $dataListPacking,
            'dataListBiayaTambahan' => $dataListBiayaTambahan,
            'company'   => $company
        ];

        // dd($data);

        $this->dompdf->loadHtml(view('InvoiceExim/InvPackingBC/print', $data));
        $this->dompdf->setPaper('legal', 'portrait');
        $this->dompdf->render();

        // Output PDF
        $filename = $dataInvoice['no_container'];
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }


    public function updatePackingBC($id)
    {
        $id = decrypt($id);
        $dataInvoice = $this->invPackingBcModel->where('id', $id)->first();
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataInvoice['sales_order_export_id']);
        if ($dataSalesOrderExport == null) {
            return redirect()->to('invoice-packing-bc');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataHsCode = $this->hsCodeModel->findAll();
        $dataListBarang = $this->invPackingBcBarangModel->getByInvId($id);
        $dataListPacking = $this->invPackingBcPackModel->getByInvId($id);
        $dataListBiayaTambahan = $this->invPackingBcBiayaModel->getByInvId($id);

        $data = [
            'dataInvoice' => $dataInvoice,
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataHsCode' => $dataHsCode,
            'dataListBarang' => $dataListBarang,
            'dataListPacking' => $dataListPacking,
            'dataListBiayaTambahan' => $dataListBiayaTambahan
        ];

        return view('InvoiceExim/InvPackingBC/formInv', $data);
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
            $invPCId = $this->invPackingBcModel->insert([
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
                'payment_description' => $this->request->getVar('payment_description'),
                'no_container' => $this->request->getVar('no_container'),
                'no_seal' => $this->request->getVar('no_seal'),
                'country_of_origin' => $this->request->getVar('country_of_origin'),
                'penanda_tangan' => $this->request->getVar('penanda_tangan'),
                'measurement' => $this->request->getVar('measurement'),
                'total_nilai_invoice' => $this->request->getVar('total_nilai_invoice'),
                'total_berat_bersih' => $this->request->getVar('total_berat_bersih'),
                'total_berat_kotor' => $this->request->getVar('total_berat_kotor')
            ]);

            $this->insertDetailPackBC($invPCId);
            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Commercial Invoice BC Berhasil Disimpan"
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

    public function update()
    {
        $db = \Config\Database::connect();
        try {
            $id = decrypt($this->request->getVar('id'));

            $this->invPackingBcModel->update($id, [
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
                'payment_description' => $this->request->getVar('payment_description'),
                'no_container' => $this->request->getVar('no_container'),
                'no_seal' => $this->request->getVar('no_seal'),
                'country_of_origin' => $this->request->getVar('country_of_origin'),
                'penanda_tangan' => $this->request->getVar('penanda_tangan'),
                'measurement' => $this->request->getVar('measurement'),
                'total_nilai_invoice' => $this->request->getVar('total_nilai_invoice'),
                'total_berat_bersih' => $this->request->getVar('total_berat_bersih'),
                'total_berat_kotor' => $this->request->getVar('total_berat_kotor')
            ]);
            // Hapus dulu purge
            $this->invPackingBcBiayaModel->where('inv_packing_bc_id', $id)->delete(null, true);
            $this->invPackingBcPackModel->where('inv_packing_bc_id', $id)->delete(null, true);
            $this->invPackingBcBarangModel->where('inv_packing_bc_id', $id)->delete(null, true);

            $this->insertDetailPackBC($id);
            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Commercial Invoice BC Berhasil Diupdate"
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
        $this->invPackingBcModel->delete($id);
        $this->invPackingBcBiayaModel->where('inv_packing_bc_id', $id)->delete(null, false);
        $this->invPackingBcPackModel->where('inv_packing_bc_id', $id)->delete(null, false);
        $this->invPackingBcBarangModel->where('inv_packing_bc_id', $id)->delete(null, false);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Commercial Invoice BC Berhasil Dihapus"
        ]);
    }


    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->invPackingBcModel->update($id, [
            'status_posting' => 1
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Commercial Invoice BC Berhasil Diposting"
        ]);
    }


    public function unposting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->invPackingBcModel->update($id, [
            'status_posting' => 0
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Commercial Invoice BC Berhasil Diunposting"
        ]);
    }

    private function insertDetailPackBC($id)
    {
        foreach (json_decode($_POST['listBarang']) as $l) {
            $this->invPackingBcBarangModel->insert([
                'inv_packing_bc_id' => $id,
                'nama_barang' => $l->nama_barang,
                'hs_code_id' => $l->hs_code,
                'catatan' => $l->catatan,
                'qty' => $l->qty,
                'satuan_id' => $l->satuan_id,
                'harga_satuan_barang' => $l->harga_satuan_barang,
                'total_harga_barang' => $l->total_harga_barang,
            ]);
        }

        foreach (json_decode($_POST['listPacking']) as $l) {
            $size = $l->size_breakdown;
            $this->invPackingBcPackModel->insert([
                'inv_packing_bc_id' => $id,
                'hs_code_id' => $l->hs_code,
                'qty' => $l->qty,
                'nama_barang' => $l->nama_barang,
                'satuan_id' => $l->satuan_id,
                'harga_satuan_barang' => $l->harga_satuan_barang,
                'total_harga_barang' => $l->total_harga_barang,
                'catatan' => $l->catatan,
                'qty' => $l->qty,
                'berat_bersih' => $size->berat_bersih,
                'berat_kotor' => $size->berat_kotor,
                'can' => $size->can,
                'kg' => $size->kg,
                'lb' => $size->lb,
                'inner_box' => $size->inner_box,
                'pc' => $size->pc,
                'bag' => $size->bag,
                'palet' => $size->palet,
                'persen' => $size->persen,
                'vgm' => $size->vgm,
                'drammed' => $size->drammed,
                'cased' => $size->case,
                'cup' => $size->cup,

            ]);
        }

        foreach (json_decode($_POST['listBiayaTambahan']) as $l) {
            $this->invPackingBcBiayaModel->insert([
                'inv_packing_bc_id' => $id,
                'biaya_tambahan' => $l->biaya_tambahan,
                'tipe_biaya_tambahan' => $l->tipe_biaya_tambahan,
                'nilai_biaya_tambahan' => $l->nilai_biaya_tambahan
            ]);
        }
    }
}
