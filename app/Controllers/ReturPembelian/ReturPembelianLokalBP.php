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

class ReturPembelianLokalBP extends BaseController
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
        $this->bc25Model = new BC25Model();
        $this->bc30Model = new BC30Model();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataSupplier' => $this->supplierModel->getSupplierByType('BAHAN PENOLONG')
        ];

        return view('Warehouse/returnBarang/indexLokalBP', $data);
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
            "penerimaan_barang.status_penerimaan" => "LOKAL",
            "penerimaan_barang.tipe_bahan" => "PENOLONG",
        ];

        $addCondition = [
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "search" => $this->request->getVar('search'),
            "status" => $this->request->getVar("status"),
            "divisi_id" => $this->request->getVar('divisi_id'),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
            "supplier_id" => $this->request->getVar("supplier_id"),
            "status_post" => $this->request->getVar('status_post'),
            "start_date" => $this->request->getVar("start_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "",
            "end_date" => $this->request->getVar("end_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "",
            "divisi_access_id" => $this->this_divisi_access,
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $penerimaanBarangData = $this->pengembalianBarangModel->getPengembalianBarangList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarang = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($penerimaanBarangData['data'] as $data) {
            $status_bc = 0;
            $dokumen_pengeluaran = $this->metaDataModel->where('id', $data->bc_pengeluaran_id)->first();

            if ($data->bc_pengeluaran_id == '0') {
                // TIDAK ADA
                if ($data->status_post == "FINISH") {
                    $status_bc = 1;
                } else {
                    $status_bc = 0;
                }
            } else {
                // ADA DOKUMEN BC   
                // 54 -> 4.1
                // 49 -> 2.5
                $bc25 = $this->bc25Model->where('pengembalian_barang_id', $data->id)->first();
                $bc41 = $this->bc41Model->where('pengembalian_barang_id', $data->id)->first();
                $bc30 = $this->bc30Model->where('pengembalian_barang_id', $data->id)->first();

                if ($bc25 != null || $bc41 != null || $bc30 != null) {
                    $status_bc = 1;
                } else {
                    $status_bc = 0;
                }
            }

            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_surat_jalan"        => $data->no_surat_jalan,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "supplier_name"         => $data->supplier_name,
                "tanggal_surat_jalan"   => $data->tanggal_surat_jalan ? date("d/m/Y", strtotime($data->tanggal_surat_jalan)) : "",
                "divisi_name"           => $data->divisi_name,
                "warehouse_name"        => $data->warehouse_name,
                "status_post"           => $data->status_post,
                "status_bc"             => $status_bc,
                "dokumen_pengeluaran" =>
                $data->bc_pengeluaran_id == '0'
                    ? "NON PABEAN"
                    : ($data->bc_pengeluaran_id == null
                        ? "-"
                        : $dokumen_pengeluaran['value']),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $penerimaanBarangData['totalData'],
            "recordsFiltered"   => $penerimaanBarangData['totalFilteredData'],
            "data"              => $dataPenerimaanBarang,
            "payload"           => $payload,
        ];

        echo json_encode($data);
        return;
    }

    public function create()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('Warehouse/returnBarang/formLokalBP', $data);
    }

    public function update($id)
    {
        $id = decrypt($id);

        if ($this->pengembalianBarangModel->find($id) == null) {
            return redirect()->to('retur-po-lokal-bp');
        }

        $dataPengembalianBarang = $this->pengembalianBarangModel->find($id);
        $dataPengembalianBarangDetail = $this->pengembalianBarangModel->getReturDetail(
            $id,
            $dataPengembalianBarang['penerimaan_barang_id']
        );
        $dataPenerimaanBarang = $this->penerimaanBarangModel
            ->select('penerimaan_barang.*,warehouses.warehouse_name,suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->where('penerimaan_barang.id', $dataPengembalianBarang['penerimaan_barang_id'])
            ->findAll();

        $resultPengembalianDetail = array();
        foreach ($dataPengembalianBarangDetail as $d) {
            if ($d['jml_retur'] != 0) {
                array_push($resultPengembalianDetail, $d);
            }
        }

        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataPengembalianBarang' => $dataPengembalianBarang,
            'dataPengembalianBarangDetail' => $resultPengembalianDetail,
            'dataPenerimaanBarang' => $dataPenerimaanBarang
        ];

        return view('Warehouse/returnBarang/formLokalBP', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);

        if ($this->pengembalianBarangModel->find($id) == null) {
            return redirect()->to('retur-po-lokal-bp');
        }

        $dataPengembalianBarang = $this->pengembalianBarangModel->find($id);
        $dataPengembalianBarangDetail = $this->pengembalianBarangModel->getReturDetail(
            $id,
            $dataPengembalianBarang['penerimaan_barang_id']
        );
        $dataPenerimaanBarang = $this->penerimaanBarangModel
            ->select('penerimaan_barang.*,warehouses.warehouse_name,suppliers.name as supplier_name,divisis.divisi')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->where('penerimaan_barang.id', $dataPengembalianBarang['penerimaan_barang_id'])
            ->findAll();

        $resultPengembalianDetail = array();
        foreach ($dataPengembalianBarangDetail as $d) {
            if ($d['jml_retur'] != 0) {
                array_push($resultPengembalianDetail, $d);
            }
        }

        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataDokumenPabean' => $this->metaDataModel->where('name', "jenis_dok_aju")->where('value', "BC 4.1")->findAll(),
            'dataPengembalianBarang' => $dataPengembalianBarang,
            'dataPengembalianBarangDetail' => $resultPengembalianDetail,
            'dataPenerimaanBarang' => $dataPenerimaanBarang,
            'title' => "Retur Pembelian Lokal Bahan Penolong"
        ];
        $this->dompdf->loadHtml(view('Warehouse/returnBarang/printLokalBP', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Retur Barang", array("Attachment" => false));
        exit(0);
    }

    public function dropdownPenerimaanBarang()
    {
        $divisiId = $this->request->getVar('divisi_id');
        $dataList = $this->pengembalianBarangModel->dropdownPenerimaanBarang(
            $this->this_company_id,
            $divisiId,
            "PENOLONG",
            "LOKAL"
        );

        return response()->setJSON([
            'data' => $dataList,
            'status' => true
        ]);
    }
}
