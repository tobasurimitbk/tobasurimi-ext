<?php

namespace App\Controllers\ReturPembelian;

use App\Controllers\BaseController;
use App\Models\BC25Model;
use App\Models\BC30Model;
use App\Models\BC41Model;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengembalianBarangDetailModel;
use App\Models\PengembalianBarangModel;
use App\Models\SupplierModel;
use Dompdf\Dompdf;

class ReturPembelianImportBP extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $this_divisi_access;
    protected $divisiModel;
    protected $supplierModel;
    protected $pengembalianBarangModel;
    protected $pengembalianBarangDetailModel;
    protected $penerimaanBarangModel;
    protected $metaDataModel;
    protected $bc41Model;
    protected $bc25Model;
    protected $bc30Model;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_divisi_access = session()->get('login')->this_access_divisi_id;
        $this->divisiModel = new DivisisModel();
        $this->supplierModel = new SupplierModel();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->metaDataModel = new MetadataModel();
        $this->bc41Model = new BC41Model();
        $this->bc30Model = new BC30Model();
        $this->dompdf = new Dompdf();
        $this->bc25Model = new BC25Model();
    }

    public function index()
    {
        return view('Warehouse/returnBarang/indexImportBP');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $condition = [
            "pengembalian_barang.company_id" => $this->this_company_id,
            "pengembalian_barang.deletedAt" => null,
            "pengembalian_barang.type_return" => "IMPORT PENOLONG",
        ];

        $addCondition = [
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "search" => $this->request->getVar('search'),
            "start_date" => $this->request->getVar("start_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "",
            "end_date" => $this->request->getVar("end_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $penerimaanBarangData = $this->pengembalianBarangModel->getPengembalianBarangList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataPenerimaanBarang = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($penerimaanBarangData['data'] as $data) {
            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "status_post"           => $data['status_post'],
                "tanggal_surat_jalan"   => $data['tanggal_surat_jalan'] ? date("d/m/Y", strtotime($data['tanggal_surat_jalan'])) : "",
                "supplier_name"         => $data['supplier_name'],
                "no_surat_jalan"        => $data['no_surat_jalan'],
                "multiple_spp_no"       => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data['multiple_spp_no'])),
                "multiple_lpb_no"       => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data['multiple_lpb_no'])),
                "status_bc"             => 0
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $penerimaanBarangData['totalData'],
            "recordsFiltered"   => $penerimaanBarangData['totalFilteredData'],
            "data"              => $dataPenerimaanBarang,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }
    public function create()
    {
        $dataSupplier = $this->supplierModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->where('type', "INTERNASIONAL")
            ->orderBy('name', "ASC")
            ->findAll();

        $data = [
            'dataSupplier' => $dataSupplier
        ];

        return view('Warehouse/returnBarang/formImportBP', $data);
    }

    public function update($id)
    {
        $id = decrypt($id);
        $dataPengembalianBarang = $this->pengembalianBarangModel->where('id', $id)->first();

        if ($dataPengembalianBarang == null) {
            return redirect()->to('retur-po-import-bb');
        }

        $dataPengembalianBarangDetail = $this->pengembalianBarangModel->getReturDetail(
            $id,
            json_decode($dataPengembalianBarang['multiple_lpb_id']),
            true
        );
        $dataPenerimaanBarang = $this->pengembalianBarangModel->dropdownPenerimaanBarang(
            $dataPengembalianBarang['company_id'],
            $dataPengembalianBarang['supplier_id'],
            "PENOLONG",
            "IMPORT"
        );
        $dataSupplier = $this->supplierModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->where('type', "INTERNASIONAL")
            ->orderBy('name', "ASC")
            ->findAll();

        $data = [
            'dataSupplier' => $dataSupplier,
            'dataPengembalianBarang' => $dataPengembalianBarang,
            'dataPengembalianBarangDetail' => $dataPengembalianBarangDetail,
            'dataPenerimaanBarang' => $dataPenerimaanBarang
        ];

        return view('Warehouse/returnBarang/formImportBP', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);

        $dataPengembalianBarang = $this->pengembalianBarangModel
            ->select('pengembalian_barang.*,suppliers.name AS supplier_name')
            ->join('suppliers', 'suppliers.id = pengembalian_barang.supplier_id', 'left')
            ->where('pengembalian_barang.id', $id)
            ->first();

        if ($dataPengembalianBarang == null) {
            return redirect()->to('retur-po-import-bp');
        }
        $dataPengembalianBarangDetail = $this->pengembalianBarangModel->getReturDetail(
            $id,
            json_decode($dataPengembalianBarang['multiple_lpb_id']),
            true
        );

        // dd($dataPengembalianBarangDetail);

        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataPengembalianBarang' => $dataPengembalianBarang,
            'dataPengembalianBarangDetail' => $dataPengembalianBarangDetail,
            'title' => "Retur Pembelian Import Bahan Penolong"
        ];
        $this->dompdf->loadHtml(view('Warehouse/returnBarang/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Retur Barang", array("Attachment" => false));
        exit(0);
    }

    public function dropdownPenerimaanBarang()
    {
        $supplierId = $this->request->getVar('supplier_id');
        $dataList = $this->pengembalianBarangModel->dropdownPenerimaanBarang(
            $this->this_company_id,
            $supplierId,
            "PENOLONG",
            "IMPORT"
        );

        return response()->setJSON([
            'data' => $dataList,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }
}
