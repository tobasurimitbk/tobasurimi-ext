<?php

namespace App\Controllers\ReturPembelian;

use App\Controllers\BaseController;
use App\Models\BC25Model;
use App\Models\BC30Model;
use App\Models\BC41Model;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengembalianBarangDetailModel;
use App\Models\PengembalianBarangModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use Dompdf\Dompdf;
use Exception;

class ReturPembelianLokalBB extends BaseController
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
    protected $penerimaanBarangDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
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
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->metaDataModel = new MetadataModel();
        $this->bc41Model = new BC41Model();
        $this->bc25Model = new BC25Model();
        $this->bc30Model = new BC30Model();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('Warehouse/returnBarang/indexLokalBB');
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
            "pengembalian_barang.type_return" => "LOKAL BAKU",
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
            ->where('type', "BAHAN BAKU")
            ->orderBy('name', "ASC")
            ->findAll();

        $data = [
            'dataSupplier' => $dataSupplier
        ];

        return view('Warehouse/returnBarang/formLokalBB', $data);
    }

    public function createAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // return response()->setJSON([
            //     '$_POST' => $_POST,
            //     'listBarang' => json_decode($_POST['listBarang']),
            //     'token' => csrf_hash()
            // ]);
            $tanggalSuratJalan = formatDMYtoYMD($this->request->getVar('tanggal_retur_barang'));
            $supplierId = $this->request->getVar('supplier_id');
            $noSuratJalan = $this->request->getVar('no_surat_jalan');
            $keterangan = $this->request->getVar('keterangan');
            $typeReturn = $this->request->getVar('type_return');

            $multipleLpbId = array();
            $multipleSppId = array();
            $multipleSppNo = array();
            $multipleLpbNo = array();

            foreach (json_decode($_POST['listBarang']) as $l) {
                array_push($multipleLpbId, $l->penerimaan_barang_id);
                array_push($multipleSppId, $l->spp_id);
                array_push($multipleSppNo, $l->spp_no);
                array_push($multipleLpbNo, $l->no_penerimaan_barang);
            }

            $multipleLpbId =  array_unique($multipleLpbId);
            $multipleSppId =  array_unique($multipleSppId);
            $multipleSppNo = array_unique($multipleSppNo);
            $multipleLpbNo =  array_unique($multipleLpbNo);

            $multipleLpbIdStr =   str_replace(['\\"', '\\', '"'], '', json_encode($multipleLpbId));
            $multipleSppIdStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleSppId));
            $multipleSppNoStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleSppNo));
            $multipleLpbNoStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleLpbNo));


            $id = $this->pengembalianBarangModel->insert([
                'company_id' => $this->this_company_id,
                'tanggal_surat_jalan' => $tanggalSuratJalan,
                'type_return' => $typeReturn,
                'supplier_id' => $supplierId,
                'multiple_lpb_id' => $multipleLpbIdStr,
                'multiple_spp_id' => $multipleSppIdStr,
                'multiple_spp_no' => $multipleSppNoStr,
                'multiple_lpb_no' => $multipleLpbNoStr,
                'no_surat_jalan'  => $noSuratJalan,
                'status_post' => "WAITING",
                'keterangan' => $keterangan
            ]);

            foreach (json_decode($_POST['listBarang']) as $l) {
                $this->pengembalianBarangDetailModel->insert([
                    'pengembalian_barang_id' => $id,
                    'penerimaan_barang_detail_id' => $l->id,
                    'bc_pengeluaran_id' => $l->bc_pengeluaran_id,
                    'purchase_request_id' => $l->spp_id,
                    'jumlah_return' => $l->jumlah_return,
                    'harga_satuan_return' => $l->harga_satuan_return,
                    'total_harga_return' => $l->total_harga_return,
                    'keterangan_return' => $l->keterangan_return
                ]);
            }

            $db->transCommit();
            return response()->setJSON([
                'status' => true,
                'message' => "Data disimpan",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function updateAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $id = decrypt($this->request->getVar('id'));
            $tanggalSuratJalan = formatDMYtoYMD($this->request->getVar('tanggal_retur_barang'));
            $supplierId = $this->request->getVar('supplier_id');
            $noSuratJalan = $this->request->getVar('no_surat_jalan');
            $keterangan = $this->request->getVar('keterangan');
            $typeReturn = $this->request->getVar('type_return');

            $multipleLpbId = array();
            $multipleSppId = array();
            $multipleSppNo = array();
            $multipleLpbNo = array();

            foreach (json_decode($_POST['listBarang']) as $l) {
                array_push($multipleLpbId, $l->penerimaan_barang_id);
                array_push($multipleSppId, $l->spp_id);
                array_push($multipleSppNo, $l->spp_no);
                array_push($multipleLpbNo, $l->no_penerimaan_barang);
            }

            $multipleLpbId =  array_unique($multipleLpbId);
            $multipleSppId =  array_unique($multipleSppId);
            $multipleSppNo = array_unique($multipleSppNo);
            $multipleLpbNo =  array_unique($multipleLpbNo);

            $multipleLpbIdStr =   str_replace(['\\"', '\\', '"'], '', json_encode($multipleLpbId));
            $multipleSppIdStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleSppId));
            $multipleSppNoStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleSppNo));
            $multipleLpbNoStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleLpbNo));


            $this->pengembalianBarangModel->update($id, [
                'company_id' => $this->this_company_id,
                'tanggal_surat_jalan' => $tanggalSuratJalan,
                'type_return' => $typeReturn,
                'supplier_id' => $supplierId,
                'multiple_lpb_id' => $multipleLpbIdStr,
                'multiple_spp_id' => $multipleSppIdStr,
                'multiple_spp_no' => $multipleSppNoStr,
                'multiple_lpb_no' => $multipleLpbNoStr,
                'no_surat_jalan'  => $noSuratJalan,
                'status_post' => "WAITING",
                'keterangan' => $keterangan
            ]);

            $this->pengembalianBarangDetailModel->where('pengembalian_barang_id', $id)->delete();

            foreach (json_decode($_POST['listBarang']) as $l) {
                $this->pengembalianBarangDetailModel->insert([
                    'pengembalian_barang_id' => $id,
                    'penerimaan_barang_detail_id' => $l->id,
                    'bc_pengeluaran_id' => $l->bc_pengeluaran_id,
                    'purchase_request_id' => $l->spp_id,
                    'jumlah_return' => $l->jumlah_return,
                    'harga_satuan_return' => $l->harga_satuan_return,
                    'total_harga_return' => $l->total_harga_return,
                    'keterangan_return' => $l->keterangan_return
                ]);
            }

            $db->transCommit();
            return response()->setJSON([
                'status' => true,
                'message' => "Data diupdate",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }

    public function update($id)
    {
        $id = decrypt($id);
        $dataPengembalianBarang = $this->pengembalianBarangModel->where('id', $id)->first();

        if ($dataPengembalianBarang == null) {
            return redirect()->to('retur-po-lokal-bb');
        }

        $dataPengembalianBarangDetail = $this->pengembalianBarangModel->getReturDetail(
            $id,
            json_decode($dataPengembalianBarang['multiple_lpb_id']),
            true
        );
        $dataPenerimaanBarang = $this->pengembalianBarangModel->dropdownPenerimaanBarang(
            $dataPengembalianBarang['company_id'],
            $dataPengembalianBarang['supplier_id'],
            "BAKU",
            "LOKAL"
        );
        $dataSupplier = $this->supplierModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->where('type', "BAHAN BAKU")
            ->orderBy('name', "ASC")
            ->findAll();

        $data = [
            'dataSupplier' => $dataSupplier,
            'dataPengembalianBarang' => $dataPengembalianBarang,
            'dataPengembalianBarangDetail' => $dataPengembalianBarangDetail,
            'dataPenerimaanBarang' => $dataPenerimaanBarang
        ];

        return view('Warehouse/returnBarang/formLokalBB', $data);
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
            return redirect()->to('retur-po-lokal-bb');
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
            'title' => "Retur Pembelian Lokal Bahan Baku"
        ];
        $this->dompdf->loadHtml(view('Warehouse/returnBarang/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Retur Barang", array("Attachment" => false));
        exit(0);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->pengembalianBarangModel->delete($id);
        $this->pengembalianBarangDetailModel->where('pengembalian_barang_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil Hapus Retur Pembelian",
            'token' => csrf_hash()
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->pengembalianBarangModel->update($id, [
            'status_post' => "FINISH"
        ]);
        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil Posting Retur Pembelian",
            'token' => csrf_hash()
        ]);
    }


    public function unposting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->pengembalianBarangModel->update($id, [
            'status_post' => "WAITING"
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil Unposting Retur Pembelian",
            'token' => csrf_hash()
        ]);
    }

    public function dropdownPenerimaanBarang()
    {
        $supplierId = $this->request->getVar('supplier_id');
        $dataList = $this->pengembalianBarangModel->dropdownPenerimaanBarang(
            $this->this_company_id,
            $supplierId,
            "BAKU",
            "LOKAL"
        );

        return response()->setJSON([
            'data' => $dataList,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function detailBarang()
    {
        try {
            $id = $this->request->getVar('id');
            $penerimaanBarangId = $this->request->getVar('penerimaan_barang_id');

            if ($penerimaanBarangId == "") {
                return response()->setJSON([
                    'status' => true,
                    'token' => csrf_hash(),
                    'data' => []
                ]);
            }
            $penerimaanBarangIds = json_decode($penerimaanBarangId);
            $id = $id == "" ? null : decrypt($id);

            $dataList = $this->pengembalianBarangModel->getReturDetail(
                $id,
                $penerimaanBarangIds
            );

            return response()->setJSON([
                'data' => $dataList,
                'status' => true,
                'token' => csrf_hash(),
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status'  => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }
}
