<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Controllers\Warehouse\PenerimaanBarangLokalBP;
use App\Helpers\BeaCukaiApi;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BC23Model;
use App\Models\BC40Model;
use App\Models\BCBarangDokumenModel;
use App\Models\BCBarangModel;
use App\Models\BCBarangTarifModel;
use App\Models\BCDokumenModel;
use App\Models\BCEntitasModel;
use App\Models\BCKemasanModel;
use App\Models\BCKontainerModel;
use App\Models\BCPengangkutModel;
use App\Models\BCPurchaseOrderLPBModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\CeisaSettingModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\NomorIjinTPBModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengusahaTPBModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierHargaModel;
use App\Models\SupplierModel;
use Config\Database;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54
class BC40 extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $ceisaSettingModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $bcPurchaseOrderModel;
    protected $bcBarangModel;
    protected $bc40Model;
    protected $metaDataModel;
    protected $hsCodeModel;
    protected $amPurchaseOrderDetailModel;
    protected $supplierHargaModel;
    protected $barangMasterModel;
    protected $bcBarangTarifModel;
    protected $bcDokumenModel;
    protected $bcBarangDokumenModel;
    protected $kantorBeaCukaiModel;
    protected $nomorIjinTPBModel;
    protected $bcEntitasModel;
    protected $pengusahaTPBModel;
    protected $bcPengangkutModel;
    protected $bcKemasanModel;
    protected $bcKontainerModel;
    protected $supplierModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $amPurchaseOrderModel;
    protected $rmPurchaseOrderModel;
    protected $akunCeisa;
    protected $dompdf;
    protected $divisiModel;
    protected $bcPurchaseOrderLPBModel;
    protected $penerimaanBarangLokalBp;
    protected $bc23Model;

    public function __construct()
    {
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $this->bcBarangModel = new BCBarangModel();
        $this->bc40Model = new BC40Model();
        $this->metaDataModel = new MetadataModel();
        $this->hsCodeModel = new HsCodesModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->bcBarangTarifModel = new BCBarangTarifModel();
        $this->bcDokumenModel = new BCDokumenModel();
        $this->bcBarangDokumenModel = new BCBarangDokumenModel();
        $this->kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $this->nomorIjinTPBModel = new NomorIjinTPBModel();
        $this->bcEntitasModel = new BCEntitasModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->bcPengangkutModel = new BCPengangkutModel();
        $this->bcKemasanModel = new BCKemasanModel();
        $this->bcKontainerModel = new BCKontainerModel();
        $this->supplierModel = new SupplierModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->dompdf = new Dompdf();
        $this->divisiModel = new DivisisModel();
        $this->bcPurchaseOrderLPBModel = new BCPurchaseOrderLPBModel();
        $this->penerimaanBarangLokalBp = new PenerimaanBarangLokalBP();
        $this->bc23Model = new BC23Model();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first()
        ];

        return view('BeaCukai/bc-40/index', $data);
    }

    public function online()
    {
        $data = [
            'baseUrl' => $this->metaDataModel->where('name', "Base Url BC")->first()['value']
        ];

        return view('BeaCukai/bc-40/online', $data);
    }

    public function allOnline()
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);
        $dataOnline = $beacukaiApi->getListStatusResponseAll();

        if ($dataOnline->status == false) {
            return response()->setJSON($dataOnline);
        } else {
            return response()->setJSON([
                'data' => $dataOnline,
                'status' => true
            ]);
        }
    }

    public function downloadResponPdf()
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $path = $this->request->getVar('path');
        $beacukaiApi = new BeaCukaiApi($username, $password);
        $dataOnline = $beacukaiApi->getResponPdf($path);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempFilePath, $dataOnline);

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="download.pdf"')
            ->setBody($dataOnline);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 4.0"
        ];

        $condition = [
            "bc_purchase_order.company_id"  => $this->this_company_id,
            "bc_purchase_order.deletedAt" => null,
            "bc_40.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "searchData" => trim($this->request->getGet('searchData')),
            "statusPosting" => $this->request->getGet('statusPosting'),
            "statusLPB" => $this->request->getGet("statusLPB"),
            "mulaiTanggalBC40" =>  $this->request->getVar("mulaiTanggalBC40") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("mulaiTanggalBC40")))) : "",
            "selesaiTanggalBC40" => $this->request->getVar("selesaiTanggalBC40") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("selesaiTanggalBC40")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc40Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            // $bc40 = $this->bc40Model->where('bc_purchase_order_id', $data->bc_purchase_order_id)->first();
            // $totalBarang = $this->bcPurchaseOrderModel->findDetailBarang($data->bc_purchase_order_id);

            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->bc_purchase_order_id),
                "tanggal_bc_40"         => $data->tanggal_dokumen == null ? '-' : date('d/m/Y', strtotime($data->tanggal_dokumen)),
                "no_aju"                => ($data->no_aju == "" ? "-" : $data->no_aju) . " / " . ($data->no_daftar == "" ? "-" : $data->no_daftar),
                "po_type"               => $data->po_type,
                "lpb_no"                => str_replace(['"', ']', '['], " ",  $data->multiple_lpb_no),
                "po_no"                 => str_replace(['"', ']', '['], " ",  $data->multiple_po_no),
                "supplier_name"         => strtoupper($data->supplier_name),
                "status"                => strtoupper($data->status_dokumen == null ? "BELUM DIBUAT" : $data->status_dokumen),
                // "total_barang"          => count($totalBarang),
                "total_po"              => "",
                "status_posting"        => $data->status_posting,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createPurchaseOrderView()
    {
        return view('BeaCukai/bc-40/form');
    }

    public function createPurchaseOrderAction()
    {
        $poIdArr = [];
        $lpbIdArr = [];
        $lpbNoArr = [];
        $poNoArr = [];

        foreach (json_decode($this->request->getVar('listData')) as $l) {
            array_push($poIdArr, $l->purchase_order_id);
            array_push($lpbIdArr, $l->penerimaan_barang_id);
            array_push($poNoArr, $l->po_no);
            array_push($lpbNoArr, $l->lpb_no);
        }

        $tanggalDokumen = formatDMYtoYMD($this->request->getVar('tanggal_dokumen'));
        // Update Nomor Aju
        $poType = $this->request->getVar('po_type');


        $id = $this->bcPurchaseOrderModel->insert([
            'supplier_id' => $this->request->getVar('supplier_id'),
            'company_id' => $this->this_company_id,
            'po_type' => $poType,
            'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($poIdArr)),
            'multiple_lpb_id' => str_replace(['\\"', '\\', '"'], '', json_encode($lpbIdArr)),
            'multiple_po_no' => str_replace(['\\"', '\\'], '', json_encode($poNoArr)),
            'multiple_lpb_no' => str_replace(['\\"', '\\'], '', json_encode($lpbNoArr)),
            'createdAt' => date('Y-m-d H:i:s', strtotime($tanggalDokumen))
        ]);

        $lpbIdUnique = array_values(array_unique($lpbIdArr));

        foreach ($lpbIdUnique as $l) {
            $this->bcPurchaseOrderLPBModel->insert([
                'bc_purchase_order_id' => $id,
                'penerimaan_barang_id' => $l
            ]);
        }

        if ($poType == "LOKAL BAKU" || $poType == "LOKAL PENOLONG") {
            $noAju = $this->generateNomorAju($tanggalDokumen);

            $this->bc40Model->insert([
                'bc_purchase_order_id' => $id,
                'no_aju' => $noAju
            ]);
        } else {
            $noAju = $this->generateNomorAjuBc23($tanggalDokumen);

            $this->bc23Model->insert([
                'bc_purchase_order_id' => $id,
                'no_aju' => $noAju
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Sukses membuat dokumen bc purchase order",
            'id' => encrypt($id)
        ]);
    }

    public function updatePurchaseOrderView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);
        $bc40 = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $bcPo = $this->bcPurchaseOrderModel
            ->select('bc_purchase_order.*, suppliers.name AS supplier_name')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->where('bc_purchase_order.id', $bcPurchaseOrderID)
            ->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPo['createdAt']));
        $noAju = $this->generateNomorAju($tanggalDokumen);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        if ($bc40 != null) {
            if ($bc40['no_aju'] != null) {
                $noAju = $bc40['no_aju'];
            }
        }

        $data = [
            'noAju' => $noAju,
            'bcPo' => $bcPo,
            'bc40' => $bc40,
            'daftarPoUsed' => $this->bcPurchaseOrderModel->findDetailBarangWithSpek($bcPurchaseOrderID),
            'kodeFasilitasTarif' => $this->metaDataModel->where('name', "Kode Fasilitas Tarif BC")->whereIn('description', ['TIDAK DIPUNGUT', 'DIBEBASKAN', 'DITANGGUHKAN', 'SUDAH DILUNASI'])->findAll(),
            'kodeJenisTarif' => $this->metaDataModel->where('name', "Kode Jenis Tarif BC")->whereIn('description', ['ADVALORUM'])->findAll(),
            'kodeJenisPungutan' => $this->metaDataModel->where('name', "Kode Jenis Pungutan BC")->whereIn('value', ['PPN'])->findAll(),
        ];

        return view('BeaCukai/bc-40/form-po-list', $data);
    }

    public function pungutanPerBarangAll()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $lpbDetail = $this->bcPurchaseOrderModel->findDetailBarang($bcPurchaseOrderID);
        $dataResult = array();

        $no = 1;
        $seriBarang = 1;
        foreach ($lpbDetail as $l) {
            $bcBarangTarif = $this->bcBarangTarifModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('penerimaan_barang_id', $l['penerimaan_barang_id'])
                ->where('barang1_id', $l['barang1_id'])
                ->first();

            $bcBarang = $this->bcBarangModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('penerimaan_barang_id', $l['penerimaan_barang_id'])
                ->where('barang1_id', $l['barang1_id'])
                ->first();

            array_push($dataResult, [
                'no' => $no++,
                'seri_barang' => $seriBarang++,
                'bc_purchase_order_id' => encrypt($bcPurchaseOrderID),
                'penerimaan_barang_id' => encrypt($l['penerimaan_barang_id']),
                'barang1_id' => encrypt($l['barang1_id']),
                'kode_barang' => $l['kode_barang'],
                'barang_name' => str_replace(['"', "'"], [''], $l['barang_name']),
                'qty_po' => number_format($l['qty_po'], 2),
                'qty_lpb' => number_format($l['qty_lpb'], 2),
                'harga' => number_format($l['harga'], 2),
                'status' => $bcBarangTarif == null ? 0 : 1,
                'kode_satuan_barang_id' => $bcBarang == null ? "" : encrypt($bcBarang['kode_satuan_barang']),
                'kode_satuan_barang' => $bcBarang == null ? "" : $bcBarang['kode_satuan_barang'],
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            'recordsTotal' => count($dataResult),
            'recordsFiltered' => 25,
            'data' => $dataResult,
        ];

        return response()->setJSON($data);
    }

    public function pungutanHasilAll()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $dataResult = $this->getPungutanData($bcPurchaseOrderID);

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            'recordsTotal' => count($dataResult),
            'recordsFiltered' => 25,
            'data' => $dataResult,
        ];

        return response()->setJSON($data);
    }

    public function detailBarang($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);
        $bc40 = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $bcPo = $this->bcPurchaseOrderModel
            ->select('bc_purchase_order.*, suppliers.name AS supplier_name')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->where('bc_purchase_order.id', $bcPurchaseOrderID)
            ->first();
        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }
        if ($bc40 != null) {
            if ($bc40['no_aju'] != null) {
                $noAju = $bc40['no_aju'];
            }
        } else {
            $noAju = "";
        }

        $dataResponse = null;
        if ($bc40['status_dokumen'] == "Sudah Kirim") {
            $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
            $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

            $beacukaiApi = new BeaCukaiApi($username, $password);
            $noAju = str_replace('-', '',   $bc40['no_aju']);

            $response = $beacukaiApi->getResponseByNoAju(
                $noAju
            );

            if (is_array($response)) {
                // Gagal Dari Ceisa
                $dataResponse = null;
            } else {
                if ($response->status == "Failed") {
                    $dataResponse = null;
                }
                $dataResponse = $response;
            }
        }

        $data = [
            'daftarPoUsed' => $this->bcPurchaseOrderModel->findDetailBarangWithSpek($bcPurchaseOrderID),
            'bcPo' => $bcPo,
            'noAju' => $noAju,
            'dataResponse' => $dataResponse
        ];
        return view('BeaCukai/bc-40/detail-barang', $data);
    }

    public function updatePurchaseOrderAction()
    {
        $bcPurchaseOrderID = $this->request->getVar('bc_purchase_order_id');
        // HAPUS BC BARANG DAN BC BARANG DOKUMEN
        $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcBarangDokumenModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();

        $poIdArr = [];
        $lpbIdArr = [];
        $lpbNoArr = [];
        $poNoArr = [];

        foreach (json_decode($this->request->getVar('listData')) as $l) {
            array_push($poIdArr, $l->purchase_order_id);
            array_push($lpbIdArr, $l->penerimaan_barang_id);
            array_push($poNoArr, $l->po_no);
            array_push($lpbNoArr, $l->lpb_no);
        }

        $this->bcPurchaseOrderModel->update($bcPurchaseOrderID, [
            'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($poIdArr)),
            'multiple_lpb_id' => str_replace(['\\"', '\\', '"'], '', json_encode($lpbIdArr)),
            'multiple_po_no' => str_replace(['\\"', '\\'], '', json_encode($poNoArr)),
            'multiple_lpb_no' => str_replace(['\\"', '\\'], '', json_encode($lpbNoArr)),
            'no_daftar' => $this->request->getVar('no_daftar'),
            'createdAt' => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))))
        ]);

        $this->bcPurchaseOrderLPBModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->builder()
            ->delete();

        $lpbIdUnique = array_values(array_unique($lpbIdArr));

        foreach ($lpbIdUnique as $l) {
            $this->bcPurchaseOrderLPBModel->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'penerimaan_barang_id' => $l
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Barang purchase order berhasil diupdate",
        ]);
    }

    public function createHeaderView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc40 = $this->bc40Model->get($bcPurchaseOrderID);
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $data = [
            'bc40' => $bc40,
            'noAju' => $bc40 == null ? $this->generateNomorAju($tanggalDokumen) : $bc40['no_aju'],
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'kodeTujuanTpb' => $this->metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTujuanPengiriman' => $this->metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', 40)->where('deletedAt', null)->findAll(),
            'selectedKantor' => $ceisaSetting['kode_kantor_pabean'],
            'bcPo' => $bcPo
        ];

        return view('BeaCukai/bc-40/form-header', $data);
    }

    public function createHeaderAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));

        $lastData = $this->bc40Model->get($bcPurchaseOrderID);
        if ($lastData == null) {
            // insert
            $this->bc40Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju($tanggalDokumen),
                'kode_kantor' => decrypt($this->request->getVar('header_kantor_pabean')),
                'kode_jenis_tpb' => decrypt($this->request->getVar('header_kode_jenis_tpb')),
                'kode_tujuan_pengiriman' => decrypt($this->request->getVar('header_kode_tujuan_pengiriman'))
            ]);
        } else {
            // update
            $this->bc40Model->update($lastData['id'], [
                'kode_kantor' => decrypt($this->request->getVar('header_kantor_pabean')),
                'kode_jenis_tpb' => decrypt($this->request->getVar('header_kode_jenis_tpb')),
                'kode_tujuan_pengiriman' => decrypt($this->request->getVar('header_kode_tujuan_pengiriman'))
            ]);

            if ($lastData['no_aju'] == null) {
                $this->bc40Model->update($lastData['id'], [
                    'no_aju' => $this->generateNomorAju($tanggalDokumen),
                ]);
            }
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Header berhasil diupdate",
        ]);
    }

    public function createEntitasView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc40 = $this->bc40Model->get($bcPurchaseOrderID);
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->findAll();

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $data = [
            'bc40' => $bc40,
            'bcEntitas' => $this->bcEntitasModel->get($bcPurchaseOrderID),
            'pengusahaTPB' => $pengusahaTPB,
            'bcPo' => $bcPo
        ];

        return view('BeaCukai/bc-40/form-entitas', $data);
    }

    public function createEntitasAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $lastData = $this->bcEntitasModel->get($bcPurchaseOrderID);

        if ($lastData == null) {
            // insert
            $this->bcEntitasModel->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_type' => 40,
                'alamat_entitas' => $this->request->getVar('pengusaha_tpb_alamat'),
                'nama_entitas' => $this->request->getVar('pengusaha_tpb_nama'),
                'nib_entitas' => $this->request->getVar('pengusaha_tpb_nib'),
                'nitku_entitas' => $this->request->getVar('pengusaha_tpb_nitku'),
                'nomor_identitas' => $this->request->getVar('pengusaha_tpb_npwp'),
                'nomor_ijin_entitas' => $this->request->getVar('pengusaha_tpb_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('pengirim_nama'),
                'alamat_pemasok' => $this->request->getVar('pengirim_alamat'),
                'nitku_pemasok' => $this->request->getVar('pengirim_nitku'),
                'npwp_pemasok' => $this->request->getVar('pengirim_npwp'),
                'npwp_pemilik_barang' => $this->request->getVar('pemilik_barang_npwp'),
                'nama_pemilik_barang' => $this->request->getVar('pemilik_barang_nama'),
                'alamat_pemilik_barang' => $this->request->getVar('pemilik_barang_alamat'),
                'nitku_pemilik_barang' => $this->request->getVar('pemilik_barang_nitku'),
            ]);
        } else {
            // update
            $this->bcEntitasModel->update($lastData['id'], [
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_type' => 40,
                'alamat_entitas' => $this->request->getVar('pengusaha_tpb_alamat'),
                'nama_entitas' => $this->request->getVar('pengusaha_tpb_nama'),
                'nib_entitas' => $this->request->getVar('pengusaha_tpb_nib'),
                'nitku_entitas' => $this->request->getVar('pengusaha_tpb_nitku'),
                'nomor_identitas' => $this->request->getVar('pengusaha_tpb_npwp'),
                'nomor_ijin_entitas' => $this->request->getVar('pengusaha_tpb_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('pengirim_nama'),
                'alamat_pemasok' => $this->request->getVar('pengirim_alamat'),
                'nitku_pemasok' => $this->request->getVar('pengirim_nitku'),
                'npwp_pemasok' => $this->request->getVar('pengirim_npwp'),
                'npwp_pemilik_barang' => $this->request->getVar('pemilik_barang_npwp'),
                'nama_pemilik_barang' => $this->request->getVar('pemilik_barang_nama'),
                'alamat_pemilik_barang' => $this->request->getVar('pemilik_barang_alamat'),
                'nitku_pemilik_barang' => $this->request->getVar('pemilik_barang_nitku'),
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Entitas berhasil diupdate",
        ]);
    }

    public function createDokumenView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);
        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        // HANYA PO BAHAN BAKU
        $po = $this->rmPurchaseOrderModel->whereIn('id', json_decode($bcPo['multiple_po_id']))->first();

        $data = [
            'kodeDokumen' => $this->metaDataModel
                ->where('name', "Dokumen")
                ->orderBy('description', "ASC")
                ->findAll(),
            'bcPo' => $bcPo,
            'po' => $po,
        ];

        return view('BeaCukai/bc-40/form-dokumen', $data);
    }

    public function allDokumen()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $condition = [
            "bc_dokumen.deletedAt"  => null,
            "bc_dokumen.bc_purchase_order_id" => $bcPurchaseOrderID,
            "bc_dokumen.bc_type" => 40
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiDokumen = $this->bcDokumenModel->getList($condition, $limit, $offset);
        $resDokumen = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiDokumen['data'] as $data) {
            $jenisDokumen = $this->metaDataModel
                ->where('name', "Dokumen")
                ->where('description', $data->kode_dokumen)
                ->orderBy('description', "ASC")
                ->first();

            array_push($resDokumen, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "bc_purchase_order_id"  => encrypt($data->bc_purchase_order_id),
                "kode_dokumen"          => $data->kode_dokumen . ' - ' . $jenisDokumen['value'],
                "nomor_dokumen"         => $data->nomor_dokumen,
                "seri_dokumen"          => $data->seri_dokumen,
                "tanggal_dokumen"       => date('d/m/Y', strtotime($data->tanggal_dokumen))
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiDokumen['totalData'],
            "recordsFiltered"   => $beaCukaiDokumen['totalFilteredData'],
            "data"              => $resDokumen,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createDokumenAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $lastData = $this->bcDokumenModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->orderBy('createdAt', "DESC")
            ->first();

        $seriDokumen = $lastData == null ? 1 : $lastData['seri_dokumen'] + 1;

        $this->bcDokumenModel->insert([
            'bc_type' => 40,
            'bc_purchase_order_id' => $bcPurchaseOrderID,
            'id_dokumen' => generateUniqueCode(5),
            'nomor_dokumen' => $this->request->getVar('dokumen_nomor_dokumen'),
            'seri_dokumen' => $seriDokumen,
            'kode_dokumen' => decrypt($this->request->getVar('dokumen_jenis_dokumen')),
            'tanggal_dokumen' => $this->request->getVar('dokumen_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('dokumen_tanggal')), "Y-m-d") : "",
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Dokumen berhasil ditambah",
        ]);
    }

    public function deleteDokumenAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->bcDokumenModel->delete($id);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Dokumen berhasil dihapus",
        ]);
    }

    public function autoCreateDokumen()
    {
        try {
            $bcPurchaseOrderId = decrypt($this->request->getVar('bc_purchase_order_id'));
            $kodeDokumen = 315;

            $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderId)->first();
            $poIdArr = \json_decode($bcPurchaseOrder['multiple_po_id']);

            if ($bcPurchaseOrder['po_type'] == "LOKAL BAKU") {
                $poDetail = $this->rmPurchaseOrderModel
                    ->whereIn('id', $poIdArr)
                    ->findAll();
            } else {
                $poDetail = $this->amPurchaseOrderModel
                    ->whereIn('id', $poIdArr)
                    ->findAll();
            }

            // Hapus Dulu
            $this->bcDokumenModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderId)
                ->delete();

            $seriDokumen = 1;
            foreach ($poDetail as $p) {
                $this->bcDokumenModel
                    ->insert([
                        'bc_purchase_order_id' => $bcPurchaseOrderId,
                        'bc_type' => 40,
                        'kode_dokumen' => $kodeDokumen,
                        'nomor_dokumen' => $p['po_no'],
                        'seri_dokumen' => $seriDokumen,
                        'tanggal_dokumen' => $p['po_date'],
                        'id_dokumen' => $p['id'],
                        'seri_dokumen' => $seriDokumen
                    ]);

                $seriDokumen++;
            }

            return \response()->setJSON([
                'status' => true,
                'message' => "Dokumen berhasil digenerate",
                'token' => \csrf_hash()
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => true,
                'token' => \csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function createPengangkutView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'bcPengangkut' => $this->bcPengangkutModel->get($bcPurchaseOrderID),
            'bcPo' => $bcPo
        ];

        return view('BeaCukai/bc-40/form-pengangkut', $data);
    }

    public function createPengangkutAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $lastData = $this->bcPengangkutModel->get($bcPurchaseOrderID);

        if ($lastData == null) {
            $this->bcPengangkutModel->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'nama_sarana_pengangkut' => $this->request->getVar('jenis_sarana_pengangkut'),
                'nomor_pengangkut' => $this->request->getVar('nomor_sarana_pengangkut'),
                'seri_pengangkut' => 1,
                'bc_type' => 40
            ]);
        } else {
            $this->bcPengangkutModel->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'nama_sarana_pengangkut' => $this->request->getVar('jenis_sarana_pengangkut'),
                'nomor_pengangkut' => $this->request->getVar('nomor_sarana_pengangkut'),
                'seri_pengangkut' => 1,
                'bc_type' => 40
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pengangkut berhasil diupdate"
        ]);
    }

    public function createKemasanPetiKemas($bcPurchaseOrderID)
    {

        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $kemasanLast = $this->bcKemasanModel->getLast($bcPurchaseOrderID);
        $kontainerLast = $this->bcKontainerModel->getLast($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $this->metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'dropdownKemasan' => $this->bcPurchaseOrderModel->dropdownKemasan($bcPurchaseOrderID),
            'seriKemasan' => $kemasanLast == null ? 1 : $kemasanLast['seri_kemasan'] + 1,
            'seriKontainer' => $kontainerLast == null ? 1 : $kontainerLast['seri_kontainer'] + 1,
            'bcPo' => $bcPo
        ];

        return view('BeaCukai/bc-40/form-kemasan-peti-kemas', $data);
    }

    public function allKemasan()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $condition = [
            "bc_kemasan.deletedAt"  => null,
            "bc_kemasan.bc_purchase_order_id" => $bcPurchaseOrderID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc40Kemasan = $this->bcKemasanModel->getList($condition, $limit, $offset);
        $resKemasan = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc40Kemasan['data'] as $data) {
            $kemasan = $this->metaDataModel
                ->where('name', 'Jenis Kemasan')
                ->where('description', $data->kode_jenis_kemasan)
                ->first();

            array_push($resKemasan, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "bc_purchase_order_id"  => encrypt($data->bc_purchase_order_id),
                "kemasan_name"          => strtoupper($data->kode_kemasan . " - " . $data->kemasan_name),
                "jumlah_kemasan"        => $data->jumlah_kemasan,
                "kode_jenis_kemasan"    => strtoupper($data->kode_jenis_kemasan . ' - ' . $kemasan['value']),
                "merk_kemasan"          => $data->merk_kemasan,
                "seri_kemasan"          => $data->seri_kemasan
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $bc40Kemasan['totalData'],
            "recordsFiltered"   => $bc40Kemasan['totalFilteredData'],
            "data"              => $resKemasan,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function allKontainer()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $condition = [
            "bc_kontainer.deletedAt"  => null,
            "bc_kontainer.bc_purchase_order_id" => $bcPurchaseOrderID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc40Kontainer = $this->bcKontainerModel->getList($condition, $limit, $offset);
        $resKontainer = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc40Kontainer['data'] as $data) {
            $ukuranKontainer = $this->metaDataModel
                ->where('name', "Kode Ukuran Kontainer BC")
                ->where('value', $data->kode_ukuran_kontainer)
                ->first();
            $jenisKontainer = $this->metaDataModel
                ->where('name', "Jenis Kontainer")
                ->where('description', $data->kode_jenis_kontainer)
                ->first();
            $tipeKontainer = $this->metaDataModel
                ->where('name', "Kode Tipe Kontainer BC")
                ->where('value', $data->kode_tipe_kontainer)
                ->first();

            array_push($resKontainer, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "bc_purchase_order_id"  => encrypt($data->bc_purchase_order_id),
                "kode_tipe_kontainer"   => $data->kode_tipe_kontainer . ' - ' . $tipeKontainer['description'],
                "kode_ukuran_kontainer" => $ukuranKontainer['description'],
                "nomor_kontainer"       => $data->nomor_kontainer,
                "seri_kontainer"        => $data->seri_kontainer,
                "kode_jenis_kontainer"  => $data->kode_jenis_kontainer . ' - ' . $jenisKontainer['value']
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $bc40Kontainer['totalData'],
            "recordsFiltered"   => $bc40Kontainer['totalFilteredData'],
            "data"              => $resKontainer,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createKemasanAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $this->bcKemasanModel->insert([
            'bc_type' => 40,
            'bc_purchase_order_id' => $bcPurchaseOrderID,
            'kemasan_id' => $this->request->getVar('kemasan_kemasan_id'),
            'seri_kemasan' => $this->request->getVar('kemasan_seri_kemasan'),
            'jumlah_kemasan' => $this->request->getVar('kemasan_jumlah_kemasan'),
            'kode_jenis_kemasan' => decrypt($this->request->getVar('kemasan_jenis_kemasan')),
            'merk_kemasan' => $this->request->getVar('kemasan_merk_kemasan')
        ]);

        $kemasanLast = $this->bcKemasanModel->getLast($bcPurchaseOrderID);

        return response()->setJSON([
            'status' => true,
            'kemasan_seri_kemasan' => $kemasanLast != null ? $kemasanLast['seri_kemasan'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil ditambahkan"
        ]);
    }

    public function deleteKemasanAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $bcPurchaseOrderID = $this->bcKemasanModel->find($id)['bc_purchase_order_id'];
        $this->bcKemasanModel->delete($id);

        $kemasanLast = $this->bcKemasanModel->getLast($bcPurchaseOrderID);

        return response()->setJSON([
            'status' => true,
            'kemasan_seri_kemasan' => $kemasanLast != null ? $kemasanLast['seri_kemasan'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil dihapus"
        ]);
    }

    public function createKontainerAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $this->bcKontainerModel->insert([
            'bc_type' => 40,
            'bc_purchase_order_id' => $bcPurchaseOrderID,
            'nomor_kontainer' => $this->request->getVar('kontainer_nomor'),
            'kode_ukuran_kontainer' => decrypt($this->request->getVar('kontainer_ukuran')),
            'kode_jenis_kontainer' => decrypt($this->request->getVar('kontainer_jenis')),
            'kode_tipe_kontainer' => decrypt($this->request->getVar('kontainer_tipe')),
            'seri_kontainer' => $this->request->getVar('kontainer_seri')
        ]);

        $kontainerLast = $this->bcKontainerModel->getLast($bcPurchaseOrderID);

        return response()->setJSON([
            'status' => true,
            'kontainer_seri' => $kontainerLast != null ? $kontainerLast['seri_kontainer'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kontainer berhasil ditambahkan"
        ]);
    }

    public function deleteKontainerAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $bcPurchaseOrderID = $this->bcKontainerModel->find($id)['bc_purchase_order_id'];
        $this->bcKontainerModel->delete($id);

        $kontainerLast = $this->bcKontainerModel->getLast($bcPurchaseOrderID);

        return response()->setJSON([
            'status' => true,
            'kontainer_seri' => $kontainerLast != null ? $kontainerLast['seri_kontainer'] + 1 : 1,
            'token' => csrf_hash(),
            'message' => "Kontainer berhasil dihapus"
        ]);
    }


    public function createTransaksiView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $lastData = $this->bc40Model->get($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        if ($lastData != null) {
            $lastData['harga_penyerahan'] = ($lastData['harga_penyerahan']);
            $lastData['nilai_jasa'] = ($lastData['nilai_jasa']);
            $lastData['uang_muka'] = ($lastData['uang_muka']);
            $lastData['harga_perolehan'] = ($lastData['harga_perolehan']);
            $lastData['volume'] = ($lastData['volume']);
            $lastData['bruto'] = ($lastData['bruto']);
            $lastData['netto'] = ($lastData['netto']);
        }

        $data = [
            'bcPo' => $bcPo,
            'bc40' => $lastData,
        ];

        return view('BeaCukai/bc-40/form-transaksi', $data);
    }

    public function createTransaksiAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();

        $lastData = $this->bc40Model->get($bcPurchaseOrderID);
        if ($lastData == null) {
            // insert
            $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));
            $this->bc40Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju(
                    $tanggalDokumen
                ),

                // 'harga_penyerahan' => convertRupiahToNumber($this->request->getVar('harga_nilai_pabean')),
                'nilai_jasa' => ($this->request->getVar('nilai_jasa')),
                'uang_muka' => ($this->request->getVar('nilai_uang_muka')),
                'harga_perolehan' => ($this->request->getVar('harga_perolehan')),
                'volume' => ($this->request->getVar('volume')),
                'bruto' => ($this->request->getVar('berat_kotor')),
                'netto' => ($this->request->getVar('berat_bersih')),
            ]);
        } else {
            $this->bc40Model->update($lastData['id'], [
                // 'harga_penyerahan' => convertRupiahToNumber($this->request->getVar('harga_nilai_pabean')),
                'nilai_jasa' => ($this->request->getVar('nilai_jasa')),
                'uang_muka' => ($this->request->getVar('nilai_uang_muka')),
                'harga_perolehan' => ($this->request->getVar('harga_perolehan')),
                'volume' => ($this->request->getVar('volume')),
                'bruto' => ($this->request->getVar('berat_kotor')),
                'netto' => ($this->request->getVar('berat_bersih')),
            ]);
        }

        return response()->setJSON([
            'message' => "Transaksi berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function createBarangView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPoFirst = $this->bcPurchaseOrderModel->findDetail($bcPurchaseOrderID);

        if ($bcPoFirst == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'bcPo' => $bcPoFirst,
            'lpbDetail' => $this->bcPurchaseOrderModel->findDetailBarang($bcPurchaseOrderID)
        ];

        return view('BeaCukai/bc-40/form-barang', $data);
    }

    public function createBarangDetailView($bcPurchaseOrderID, $penerimaanBarangID, $barang1ID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);
        $penerimaanBarangID = decrypt($penerimaanBarangID);
        $barang1ID = decrypt($barang1ID);

        $lpb = $this->penerimaanBarangModel->getById($penerimaanBarangID);
        $bcPoFirst = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc40DokumenBarang = $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('penerimaan_barang_id', $penerimaanBarangID)->where('barang1_id', $barang1ID)->where('deletedAt', null)->first();
        $seriBarang = $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->orderBy('createdAt', "DESC")->where('deletedAt', null)->first();

        $kodeSatuanBarang = null;

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        if ($lpb == null || $bcPoFirst == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $barangDetail = $this->bcPurchaseOrderModel->findDetailDokumenBarang(
            $bcPurchaseOrderID,
            $penerimaanBarangID,
            $barang1ID
        );

        if ($barangDetail == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        if ($bc40DokumenBarang != null) {
            $kodeSatuanBarang = $this->metaDataModel->where('name', "Kode Satuan BC")->where('value', $bc40DokumenBarang['kode_satuan_barang'])->first();
        }

        // MAtching
        $barangDetail['penerimaan_barang_id'] = $penerimaanBarangID;

        $data = [
            'kodeFasilitasTarif' => $this->metaDataModel->where('name', "Kode Fasilitas Tarif BC")->whereIn('description', ['TIDAK DIPUNGUT', 'DIBEBASKAN', 'DITANGGUHKAN', 'SUDAH DILUNASI'])->findAll(),
            'kodeJenisTarif' => $this->metaDataModel->where('name', "Kode Jenis Tarif BC")->whereIn('description', ['ADVALORUM'])->findAll(),
            'kodeJenisPungutan' => $this->metaDataModel->where('name', "Kode Jenis Pungutan BC")->whereIn('value', ['PPN'])->findAll(),
            'kodeHS' => $this->hsCodeModel->findAll(),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'barangDetail' => $barangDetail,
            'seriBarang' => $seriBarang == null ? 1 : $seriBarang['seri_barang'] + 1,
            'bc40DokumenBarang' => $bc40DokumenBarang,
            'kodeSatuanBarang' => $kodeSatuanBarang,
            'bcPo' => $bcPoFirst
        ];

        return view('BeaCukai/bc-40/form-detail-barang', $data);
    }

    public function createBarangDetailAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $barang1ID = decrypt($this->request->getVar('barang1_id'));
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();

        $bcBarang = $this->bcBarangModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->where('penerimaan_barang_id', $penerimaanBarangID)
            ->where('barang1_id', $barang1ID)
            ->first();

        if ($bcBarang == null) {
            $this->bcBarangModel->insert([
                'bc_purchase_order_id' => decrypt($this->request->getVar('bc_purchase_order_id')),
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'barang1_id' => decrypt($this->request->getVar('barang1_id')),
                'bc_type' => 40,
                'kode_dokumen' => 40,
                'seri_barang' => $this->request->getVar('barang_detail_seri_barang'),
                'pos_tarif' => decrypt($this->request->getVar('barang_detail_kode_hs')),
                'kode_barang' => $this->request->getVar('barang_detail_kode_barang'),
                'uraian' => $this->request->getVar('barang_detail_uraian'),
                'merk_barang' => $this->request->getVar('barang_detail_merk_barang'),
                'tipe_barang' => $this->request->getVar('barang_detail_tipe_barang'),
                'ukuran_barang' => $this->request->getVar('barang_detail_ukuran_barang'),
                'spesifikasi_lain' => $this->request->getVar('barang_detail_spesifikasi_lain'),
                'jumlah_satuan' => $this->request->getVar('barang_detail_jumlah_satuan'),
                'kode_satuan_barang' => decrypt($this->request->getVar('barang_detail_kode_satuan_barang')),
                'jumlah_kemasan' => $this->request->getvar('barang_detail_jumlah_kemasan'),
                'kode_jenis_kemasan' => decrypt($this->request->getVar('barang_detail_kode_jenis_kemasan')),
                'netto' => $this->request->getVar('barang_detail_berat_bersih'),
                'harga_ekspor' => $this->request->getVar('barang_detail_harga_penyerahan'),
                'nilai_tambah' => $this->request->getVar('barang_detail_harga_penggantian'),
                'diskon' => $this->request->getVar('barang_detail_diskon'),
                'volume' => $this->request->getVar('barang_detail_volume')
            ]);
        } else {
            $this->bcBarangModel->update($bcBarang['id'], [
                'bc_purchase_order_id' => decrypt($this->request->getVar('bc_purchase_order_id')),
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'barang1_id' => decrypt($this->request->getVar('barang1_id')),
                'bc_type' => 40,
                'kode_dokumen' => 40,
                'seri_barang' => $this->request->getVar('barang_detail_seri_barang'),
                'pos_tarif' => decrypt($this->request->getVar('barang_detail_kode_hs')),
                'kode_barang' => $this->request->getVar('barang_detail_kode_barang'),
                'uraian' => $this->request->getVar('barang_detail_uraian'),
                'merk_barang' => $this->request->getVar('barang_detail_merk_barang'),
                'tipe_barang' => $this->request->getVar('barang_detail_tipe_barang'),
                'ukuran_barang' => $this->request->getVar('barang_detail_ukuran_barang'),
                'spesifikasi_lain' => $this->request->getVar('barang_detail_spesifikasi_lain'),
                'jumlah_satuan' => $this->request->getVar('barang_detail_jumlah_satuan'),
                'kode_satuan_barang' => decrypt($this->request->getVar('barang_detail_kode_satuan_barang')),
                'jumlah_kemasan' => $this->request->getvar('barang_detail_jumlah_kemasan'),
                'kode_jenis_kemasan' => decrypt($this->request->getVar('barang_detail_kode_jenis_kemasan')),
                'netto' => $this->request->getVar('barang_detail_berat_bersih'),
                'harga_ekspor' => $this->request->getVar('barang_detail_harga_penyerahan'),
                'nilai_tambah' => $this->request->getVar('barang_detail_harga_penggantian'),
                'diskon' => $this->request->getVar('barang_detail_diskon'),
                'volume' => $this->request->getVar('barang_detail_volume')
            ]);
        }

        // UPDATE DATA DI TRANSAKSI
        $bc40 = $this->bc40Model->get($bcPurchaseOrderID);

        if ($bc40 == null) {
            $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));
            $this->bc40Model->insert([
                'no_aju' => $this->generateNomorAju(
                    $tanggalDokumen
                ),
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'netto' => $this->bcBarangModel->totalBeratBersih($bcPurchaseOrderID),
                'harga_penyerahan' => $this->bcBarangModel->totalHargaPenyerahan($bcPurchaseOrderID),
                'volume' => $this->bcBarangModel->totalVolume($bcPurchaseOrderID)
            ]);
        } else {
            $this->bc40Model->update($bc40['id'], [
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'netto' => $this->bcBarangModel->totalBeratBersih($bcPurchaseOrderID),
                'harga_penyerahan' => $this->bcBarangModel->totalHargaPenyerahan($bcPurchaseOrderID),
                'volume' => $this->bcBarangModel->totalVolume($bcPurchaseOrderID)
            ]);
        }

        return response()->setJSON([
            'message' => "Detail Barang Dokumen Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function allPungutan()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $barang1ID = decrypt($this->request->getVar('barang1_id'));

        $condition = [
            "bc_barang_tarif.deletedAt"  => null,
            "bc_barang_tarif.bc_purchase_order_id" => $bcPurchaseOrderID,
            "bc_barang_tarif.penerimaan_barang_id" => $penerimaanBarangID,
            "bc_barang_tarif.barang1_id" => $barang1ID
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $bc40BarangTarif = $this->bcBarangTarifModel->getList($condition, $limit, $offset);
        $res = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc40BarangTarif['data'] as $data) {
            $kodeJenisPungutan = $this->metaDataModel->where('name', "Kode Jenis Pungutan BC")->where('value', $data->kode_jenis_pungutan)->first();
            $kodeJenisTarif = $this->metaDataModel->where('name', "Kode Jenis Tarif BC")->where('value', $data->kode_jenis_tarif)->first();
            $kodeFasilitasTarif =  $this->metaDataModel->where('name', "Kode Fasilitas Tarif BC")->where('value', $data->kode_fasilitas_tarif)->first();

            array_push($res, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "kode_jenis_pungutan"   => $data->kode_jenis_pungutan . ' - ' . $kodeJenisPungutan['description'],
                "kode_jenis_tarif"      => $data->kode_jenis_tarif . ' - ' . $kodeJenisTarif['description'],
                "tarif_bea_masuk"       => ($data->tarif_bea_masuk),
                "kode_fasilitas_tarif"  => $data->kode_fasilitas_tarif . ' - ' . $kodeFasilitasTarif['description'],
                "tarif_fasilitas"       => ($data->tarif_fasilitas)
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $bc40BarangTarif['totalData'],
            "recordsFiltered"   => $bc40BarangTarif['totalFilteredData'],
            "data"              => $res,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createPungutanAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
            $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
            $barang1ID = decrypt($this->request->getVar('barang1_id'));
            $kodeSatuanBarang = decrypt($this->request->getVar('kode_satuan_barang'));
            $seriBarang = $this->request->getVar('seri_barang');
            $harga = $this->request->getVar('harga');

            $bc40Barang = $this->bcBarangModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('barang1_id', $barang1ID)
                ->first();

            if ($bc40Barang == null) {
                if (!$kodeSatuanBarang) {
                    return response()->setJSON([
                        'message' => "Isi dokumen barang detail terlebih dahulu sebelum mengisi pungutan & dokumen barang",
                        'status' => false,
                        'token' => csrf_hash()
                    ]);
                } else {
                    $bc40BarangId = $this->bcBarangModel->insert([
                        'bc_purchase_order_id' => $bcPurchaseOrderID,
                        'penerimaan_barang_id' => $penerimaanBarangID,
                        'barang1_id'  => $barang1ID,
                        'bc_type' => 40,
                        'seri_barang' => $seriBarang,
                        'kode_satuan_barang' => $kodeSatuanBarang,
                        'harga_ekspor' => $harga
                    ]);

                    $bc40Barang = $this->bcBarangModel->where('id', $bc40BarangId)->first();
                }
            }

            $pungutan = $this->bcBarangTarifModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('barang1_id', $barang1ID)
                ->where('kode_jenis_pungutan', decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')))
                ->first();

            if ($pungutan != null) {
                $kodeJenisPungutan = $this->metaDataModel
                    ->where('name', "Kode Jenis Pungutan BC")
                    ->where('value', $pungutan['kode_jenis_pungutan'])
                    ->first();

                return response()->setJSON([
                    'message' => "Jenis pungutan " . $kodeJenisPungutan['description'] . " sudah ada",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            $nilaiTarif =  ($this->request->getVar('barang_detail_nilai_tarif'));
            $tarifFasilitas = ($this->request->getVar('barang_detail_tarif_fasilitas'));
            $tarifFasilitas = ($tarifFasilitas == 0) ? 1 : $tarifFasilitas;

            $nilaiBayar = ($bc40Barang['harga_ekspor'] * (($nilaiTarif / 100) / ($tarifFasilitas / 100)));


            $this->bcBarangTarifModel->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'penerimaan_barang_id' => $penerimaanBarangID,
                'barang1_id' => $barang1ID,
                'bc_type' => 40,
                'kode_jenis_pungutan' => decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')),
                'kode_jenis_tarif' => decrypt($this->request->getVar('barang_detail_kode_jenis_tarif')),
                'tarif_bea_masuk' => ($this->request->getVar('barang_detail_nilai_tarif')),
                'kode_fasilitas_tarif' => decrypt($this->request->getVar('barang_detail_kode_fasilitas_tarif')),
                'tarif_fasilitas' => ($this->request->getVar('barang_detail_tarif_fasilitas')),
                'seri_barang' => $bc40Barang['seri_barang'],
                'kode_satuan_barang' => $bc40Barang['kode_satuan_barang'],
                'nilai_bayar' => $nilaiBayar
            ]);

            $db->transCommit();
            return response()->setJSON([
                'message' => "Pungutan berhasil ditambahkan",
                'status' => true,
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

    public function deletePungutanAction()
    {
        $this->bcBarangTarifModel->delete(decrypt($this->request->getVar('id')));

        return response()->setJSON([
            'message' => "Pungutan berhasil dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function allDokumenBarang()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $barang1ID = decrypt($this->request->getVar('barang1_id'));

        $condition = [
            "bc_dokumen.deletedAt"  => null,
            "bc_dokumen.bc_purchase_order_id" => $bcPurchaseOrderID,
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiDokumen = $this->bcDokumenModel->getList($condition, $limit, $offset);
        $resDokumen = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiDokumen['data'] as $data) {
            $jenisDokumen = $this->metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->where('description', $data->kode_dokumen)->first();
            $barangDokumen = $this->bcBarangDokumenModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('barang1_id', $barang1ID)
                ->where('seri_dokumen', $data->seri_dokumen)
                ->where('deletedAt', null)
                ->first();

            array_push($resDokumen, [
                "no"                    => $no++,
                "bc_dokumen_id"         => encrypt($data->id),
                "bc_purchase_order_id"  => encrypt($data->bc_purchase_order_id),
                "kode_dokumen"          => $data->kode_dokumen . ' - ' . $jenisDokumen['value'],
                "nomor_dokumen"         => $data->nomor_dokumen,
                "seri_dokumen"          => $data->seri_dokumen,
                "tanggal_dokumen"       => date('d/m/Y', strtotime($data->tanggal_dokumen)),
                "is_used"               => $barangDokumen == null ? false : true,
                "barang_dokumen_id"     => $barangDokumen == null ? 0 : encrypt($barangDokumen['id'])
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiDokumen['totalData'],
            "recordsFiltered"   => $beaCukaiDokumen['totalFilteredData'],
            "data"              => $resDokumen,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createBarangDokumenAction()
    {
        $barangDokumenID = decrypt($this->request->getVar('bc_dokumen_id'));
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $barang1ID = decrypt($this->request->getVar('barang1_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $this->bcBarangDokumenModel->insert([
            'barang1_id' => $barang1ID,
            'penerimaan_barang_id' => $penerimaanBarangID,
            'bc_purchase_order_id' => $bcPurchaseOrderID,
            'bc_dokumen_id' => $barangDokumenID,
            'bc_type' => 40,
            'seri_dokumen' => $this->request->getVar('seri_dokumen')
        ]);

        return response()->setJSON([
            'message' => "Dokumen berhasil ditambahkan",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function deleteBarangDokumenAction()
    {
        $this->bcBarangDokumenModel->delete(decrypt($this->request->getVar('id')));

        return response()->setJSON([
            'message' => "Dokumen berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function createBarangDokumenAllAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $barang1ID = decrypt($this->request->getVar('barang1_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));

        $this->bcBarangDokumenModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete(null, false);
        $bcDokumen = $this->bcDokumenModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->where('deletedAt', null)
            ->findAll();

        foreach ($bcDokumen as $b) {
            $this->bcBarangDokumenModel->insert([
                'barang1_id' => $barang1ID,
                'penerimaan_barang_id' => $penerimaanBarangID,
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_dokumen_id' => $b['id'],
                'bc_type' => 40,
                'seri_dokumen' => $b['seri_dokumen']
            ]);
        }

        return response()->setJSON([
            'message' => "Dokumen berhasil ditambahkan semua",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function createPungutanView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $barangTarif = $this->bcBarangTarifModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->findAll();
        $pungutanList = $this->getPungutanData($bcPurchaseOrderID);
        $data = [
            'bcPo' => $bcPo,
            'pungutanList' => $pungutanList,
            'barangTarif' => $barangTarif
        ];

        return view('BeaCukai/bc-40/form-pungutan', $data);
    }

    private function getPungutanData($bcPurchaseOrderID)
    {
        $kodeJenisPungutan = $this
            ->metaDataModel
            ->where('name', "Kode Jenis Pungutan BC")
            ->whereIn('value', ['PPN'])
            ->findAll();

        $barangTarif = $this->bcBarangTarifModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->findAll();

        $pungutanList = [];

        $ppnDitangguhkan = 0;
        $ppnDibebaskan = 0;
        $ppnTidakDipungut = 0;

        foreach ($barangTarif as $b) {
            if ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 3) {
                $ppnDitangguhkan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 5) {
                $ppnDibebaskan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 6) {
                $ppnTidakDipungut += $b['nilai_bayar'];
            }
        }

        $no = 1;
        foreach ($kodeJenisPungutan as $k) {
            if ($k['value'] == "PPN") {
                $pungutanList[] = [
                    'no' => $no++,
                    'pungutan' => $k['value'],
                    'tidak_dipungut' => (float)$ppnTidakDipungut,
                    'dibebaskan' => (float)$ppnDibebaskan,
                    'ditangguhkan' => (float)$ppnDitangguhkan

                ];
            }
        }

        return $pungutanList;
    }

    public function createPernyataanView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc40 = $this->bc40Model->get($bcPurchaseOrderID);
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        if ($bcPo == null || $ceisaSetting == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'bcPo' => $bcPo,
            'bc40' => $bc40,
            'ceisaSetting' => $ceisaSetting
        ];

        return view('BeaCukai/bc-40/form-pernyataan', $data);
    }

    public function createPernyataanAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $lastData = $this->bc40Model->get($bcPurchaseOrderID);

        if ($lastData == null) {
            $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));
            // insert
            $this->bc40Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju(
                    $tanggalDokumen
                ),
                'nama_ttd' => $this->request->getVar('pernyatan_nama'),
                'kota_ttd' => $this->request->getVar('pernyatan_tempat'),
                'tanggal_ttd' => $this->request->getVar('pernyataan_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pernyataan_tanggal')), "Y-m-d") : "",
                'jabatan_ttd' => $this->request->getVar('pernyatan_jabatan'),
            ]);
        } else {
            // update
            $this->bc40Model->update($lastData['id'], [
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'nama_ttd' => $this->request->getVar('pernyatan_nama'),
                'kota_ttd' => $this->request->getVar('pernyatan_tempat'),
                'tanggal_ttd' => $this->request->getVar('pernyataan_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pernyataan_tanggal')), "Y-m-d") : "",
                'jabatan_ttd' => $this->request->getVar('pernyatan_jabatan')
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pernyataan berhasil diupdate",
        ]);
    }

    public function updateNoAju()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $noAju = $this->request->getVar('no_pengajuan');

        $bc40First = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();

        // $bc40 = $this->bc40Model
        //     ->where('no_aju', $noAju)
        //     ->whereNotIn('bc_purchase_order_id', [$bcPurchaseOrderID])
        //     ->first();

        // if ($bc40 != null) {
        //     return response()->setJSON([
        //         'token' => csrf_hash(),
        //         'message' => "Nomor aju sudah ada",
        //         'status' => false,
        //     ]);
        // }

        if ($bc40First == null) {
            $this->bc40Model->insert([
                'no_aju' => $noAju,
                'bc_purchase_order_id' => $bcPurchaseOrderID,
            ]);
        } else {
            $this->bc40Model
                ->set('no_aju', $noAju)
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->update();
        }

        return response()->setJSON([
            'message' => "No Aju berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function delete()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcEntitasModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcKemasanModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcKontainerModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcPengangkutModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcDokumenModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcBarangDokumenModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcBarangTarifModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
        $this->bcPurchaseOrderModel->delete($bcPurchaseOrderID);
        $this->bcPurchaseOrderLPBModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete(null, true);

        return response()->setJSON([
            'message' => "Dokumen BC 4.0 Berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    // KIRIM BC.40 KE CEISA
    public function kirimCeisa($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);

        $payload = $this->generatePayload($bcPurchaseOrderID);

        // return \response()->setJSON($payload);
        // die;

        $res = $beacukaiApi->kirimDokumenBC($payload, false);
        if ($res['status'] == false) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal Kirim Ceisa Karena : " . $res['message'],
            ]);
        }
        // UPDATE STATUS
        //$this->bc40Model->set('status_dokumen', "Sudah Kirim")->where('bc_purchase_order_id', $bcPurchaseOrderID)->update();
        $bc40 = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $this->bc40Model->update($bc40['id'], [
            'status_dokumen' => "Sudah Kirim"
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 4.O Berhasil Online di Ceisa",
            // 'res' => $res,
        ]);
    }

    public function generatePayload($bcPurchaseOrderID)
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);
        $bc40Data = $this->bc40Model->get($bcPurchaseOrderID);

        $bc23Kontainer = $this->bcKontainerModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Barang = $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Entitas = $this->bcEntitasModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Kemasan = $this->bcKemasanModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Dokumen = $this->bcDokumenModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Pengangkut = $this->bcPengangkutModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bcBarangTarif = $this->bcBarangTarifModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();

        $payload = $beacukaiApi->payloadTempleateKirimBC40(
            $bc40Data,
            $bc23Kontainer,
            $bc23Barang,
            $bc23Entitas,
            $bc23Kemasan,
            $bc23Dokumen,
            $bc23Pengangkut,
            $bcBarangTarif
        );

        return $payload;
    }

    // POSTING BEA CUKAI PO
    public function posting()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $bc40 = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();

        if ($bcPurchaseOrder['status_posting'] == 1) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Dokumen sudah di posting user lain silahkan reload halaman anda",
            ]);
        }

        if ($bc40 == null) {
            // insert
            $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));
            $this->bc40Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju(
                    $tanggalDokumen
                ),
            ]);
        }

        $bc40 = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        if ($bcPurchaseOrder['no_daftar'] == null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal Posting, No Daftar Belum Ada",
            ]);
        }

        $this->bcPurchaseOrderModel->update($bcPurchaseOrderID, [
            'status_posting' => '1',
            'no_aju' => $bc40['no_aju'],
            'bc_id' => 53,
            'bc_type' => "BC 4.0"
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 4.O Berhasil Diposting",
        ]);
    }

    // Navigator display
    private function setFlashDataNavigatorSession($bcPurchaseOrderID)
    {
        $isCompleteFormHeader = $this->bc40Model->isCompleteFormHeader($bcPurchaseOrderID);
        $isCompleteFormEntitas = $this->bc40Model->isCompleteFormEntitas($bcPurchaseOrderID);
        $isCompleteFormDokumen = $this->bc40Model->isCompleteFormDokumen($bcPurchaseOrderID);
        $isCompleteFormPengangkut = $this->bc40Model->isCompleteFormPengangkut($bcPurchaseOrderID);
        $isCompleteFormPetiKemas = $this->bc40Model->isCompleteFormPetiKemas($bcPurchaseOrderID);
        $isCompleteFormFormTransaksi = $this->bc40Model->isCompleteFormTransaksi($bcPurchaseOrderID);
        $isCompleteFormBarang = $this->bc40Model->isCompleteFormBarang($bcPurchaseOrderID);
        $isCompleteFormPungutan = $this->bc40Model->isCompleteFormPungutan($bcPurchaseOrderID);
        $isCompleteFormPernyataan = $this->bc40Model->isCompleteFormPernyataan($bcPurchaseOrderID);

        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormPernyataan', $isCompleteFormPernyataan);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormFormTransaksi);
        session()->setFlashdata('isCompleteFormBarang', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPungutan', $isCompleteFormPungutan);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        if ($bcPo['status_posting'] === '0') {
            if (
                $isCompleteFormPernyataan && $isCompleteFormHeader && $isCompleteFormEntitas &&
                $isCompleteFormEntitas && $isCompleteFormDokumen && $isCompleteFormPengangkut &&
                $isCompleteFormPetiKemas && $isCompleteFormFormTransaksi && $isCompleteFormBarang
            ) {
                $this->bc40Model->set('status_dokumen', "Siap Kirim")->where('bc_purchase_order_id', $bcPurchaseOrderID)->update();
            } else {
                $this->bc40Model->set('status_dokumen', "Belum Lengkap")->where('bc_purchase_order_id', $bcPurchaseOrderID)->update();
            }
        }
    }

    public function generateNomorAju($tanggalDokumen)
    {
        if ($this->this_company_id == 1 || $this->this_company_id == 2) {
            $company_id_arr = [1, 2];
        } else {
            $company_id_arr = [$this->this_company_id];
        }
        $ceisaSetting = $this->ceisaSettingModel
            ->where('company_id', $this->this_company_id)
            ->first();

        $kodeDokumenbc40Static = $this->metaDataModel
            ->where('name', "Kode BC40 Static")
            ->first();

        $kodeKantorStatic = $ceisaSetting['kode_unik']
            ? $ceisaSetting['kode_unik']
            : $ceisaSetting['kode_kantor_pabean'];

        $tanggalAju = date('Ymd', strtotime($tanggalDokumen));
        $tahunAjuSekarang = date('Y', strtotime($tanggalDokumen));

        // Ambil BC40 terakhir berdasarkan urutan no_aju terbaru
        $bc40Last = $this->bc40Model
            ->select('bc_40.*')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_40.bc_purchase_order_id', 'left')
            ->whereIn('bc_purchase_order.company_id', $company_id_arr)
            ->whereIn('po_type', ["LOKAL BAKU", "LOKAL PENOLONG"])
            ->orderBy('bc_40.createdAt', "DESC")
            ->limit(1)
            ->first();

        // Default urutan
        $sequenceNoUrutPengajuan = "000001";

        if ($bc40Last && $bc40Last['no_aju']) {
            $arrNo = explode('-', $bc40Last['no_aju']);

            // Pastikan format sesuai: DOC-KANTOR-YYYYMMDD-NOMOR
            if (count($arrNo) === 4) {
                $tanggalTerakhir = $arrNo[2];
                $tahunTerakhir = substr($tanggalTerakhir, 0, 4);
                $lastNomor = $arrNo[3];

                if ($tahunTerakhir === $tahunAjuSekarang) {
                    // Masih tahun yang sama → lanjutkan nomor urut
                    $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);

                    $sequenceNoUrutPengajuan = $nextNomor;
                } else {
                    // Tahun baru → reset ke 000001
                    $sequenceNoUrutPengajuan = "000001";
                }
            }
        }

        return $kodeDokumenbc40Static['value']
            . '-' . $kodeKantorStatic
            . '-' . $tanggalAju
            . '-' . $sequenceNoUrutPengajuan;
    }

    public function generateNomorAjuBc23($tanggalDokumen)
    {
        // ===============================
        // 🔹 Tentukan company yang disatukan
        // ===============================
        if (in_array($this->this_company_id, [1, 2])) {
            $company_id_arr = [1, 2];
        } else {
            $company_id_arr = [$this->this_company_id];
        }

        // ===============================
        // 🔹 Ambil setting dan metadata
        // ===============================
        $ceisaSetting = $this->ceisaSettingModel
            ->where('company_id', $this->this_company_id)
            ->first();

        $kodeDokumenBC23Static = $this->metaDataModel
            ->where('name', "Kode BC23 Static")
            ->first();

        // ===============================
        // 🔹 Tentukan variabel dasar
        // ===============================
        $kodeKantorStatic = $ceisaSetting['kode_kantor_pabean'] ?? 'XXXX';
        $tanggalAju = date('Ymd', strtotime($tanggalDokumen));
        $tahunAjuSekarang = date('Y', strtotime($tanggalDokumen));

        // ===============================
        // 🔹 Ambil data BC23 terakhir
        // ===============================
        $bc23Last = $this->bc23Model
            ->select('bc_23.*')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_23.bc_purchase_order_id', 'left')
            ->whereIn('bc_purchase_order.company_id', $company_id_arr)
            ->whereIn('po_type', ["IMPORT BAKU", "IMPORT PENOLONG"])
            ->orderBy('bc_23.createdAt', "DESC")
            ->limit(1)
            ->first();

        // ===============================
        // 🔹 Tentukan nomor urut baru
        // ===============================
        $sequenceNoUrutPengajuan = "000001"; // default jika kosong

        if ($bc23Last && !empty($bc23Last['no_aju'])) {
            $arrNo = explode('-', $bc23Last['no_aju']);

            // Format seharusnya: KODE-KANTOR-YYYYMMDD-NOMOR
            if (count($arrNo) === 4) {
                $tanggalTerakhir = $arrNo[2];
                $tahunTerakhir = substr($tanggalTerakhir, 0, 4);
                $lastNomor = $arrNo[3];

                if ($tahunTerakhir === $tahunAjuSekarang) {
                    // Tahun sama → lanjut urutan
                    $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
                    $sequenceNoUrutPengajuan = $nextNomor;
                } else {
                    // Tahun berbeda → reset
                    $sequenceNoUrutPengajuan = "000001";
                }
            }
        }

        // ===============================
        // 🔹 Return hasil akhir
        // ===============================
        return $kodeDokumenBC23Static['value']
            . '-' . $kodeKantorStatic
            . '-' . $tanggalAju
            . '-' . $sequenceNoUrutPengajuan;
    }


    // public function dropdownSupplier()
    // {
    //     $poType = $this->request->getVar('po_type');

    //     $supplierResult = [];

    //     if (empty($this->request->getVar('po_type'))) {
    //         return response()->setJSON([
    //             'data' => $supplierResult,
    //             'token' => csrf_hash(),
    //             'status' => true
    //         ]);
    //     }

    //     if (!empty($poType)) {
    //         $poType = str_replace('LOKAL', 'BAHAN', $poType);
    //         $supplier = $this->supplierModel
    //             ->where('deletedAt', null)
    //             ->where('type', $poType)
    //             ->orderBy('name', "ASC")
    //             ->findAll();

    //         foreach ($supplier as $s) {
    //             $result = $this->getListDataPurchaseOrderExport(
    //                 $this->request->getVar('po_type'),
    //                 $s['id'],
    //             );

    //             if (count($result) != 0) {
    //                 array_push($supplierResult, $s);
    //             }
    //         }

    //         return response()->setJSON([
    //             'data' => $supplierResult,
    //             'token' => csrf_hash(),
    //             'status' => true
    //         ]);
    //     }
    // }

    public function dropdownPO()
    {
        $supplierId = $this->request->getVar('supplier_id');
        $poType = $this->request->getVar('po_type');
        $startDate = $this->request->getVar('start_date');
        $endDate = $this->request->getVar('end_date');

        if (empty($endDate) || empty($poType) || empty($supplierId)) {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => true
            ]);
        }

        if (!empty($startDate)) {
            $startDate = formatDMYtoYMD($this->request->getVar('start_date'));
        }
        $endDate =  formatDMYtoYMD($this->request->getVar('end_date'));

        $result = $this->getListDataPurchaseOrderExport($poType, $supplierId, $startDate, $endDate);

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownNoIjinTPB()
    {
        $pengusahaTPBID = $this->request->getVar('pengusaha_tpb_id');
        $data = $this->nomorIjinTPBModel
            ->where('company_id', $this->this_company_id)
            ->where('pengusaha_tpb_id', $pengusahaTPBID)
            ->where('status', '1')
            ->where('deletedAt', null)
            ->findAll();

        $result = array();

        foreach ($data as $d) {
            array_push($result, [
                'no_ijin_tpb' => $d['no_ijin_tpb'],
                'tanggal_skep_tpb' => date('d/m/Y', strtotime($d['tanggal_skep_tpb'])),
                'alamat_pemilik_barang' => $d['alamat_pemilik_barang'],
            ]);
        }

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function exportPdf()
    {
        $supplierId = $this->request->getVar('supplier_id');
        $poType = $this->request->getVar('po_type');
        $startDate = $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "";
        $endDate = $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "";

        $result = $this->getListDataPurchaseOrderExport($poType, $supplierId, $startDate, $endDate);

        $data = [
            'result' => $result,
            'supplier' => $this->supplierModel->find($supplierId)
        ];

        if ($data['supplier'] == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->dompdf->loadHtml(view('BeaCukai/laporan/export-pdf', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("BC 4.0 Purchase Order", array("Attachment" => false));
    }

    public function exportExcel()
    {
        $supplierId = $this->request->getVar('supplier_id');
        $poType = $this->request->getVar('po_type');
        $startDate = $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "";
        $endDate = $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "";

        $result = $this->getListDataPurchaseOrderExport($poType, $supplierId, $startDate, $endDate);

        $filename = "List Purchase Order";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'TGL PO');
        $sheet->setCellValue('C1', 'TGL LPB');
        $sheet->setCellValue('D1', 'NO LPB');
        $sheet->setCellValue('E1', 'NO PO');
        $sheet->setCellValue('F1', 'KODE');
        $sheet->setCellValue('G1', 'BARANG');
        $sheet->setCellValue('H1', 'QTY PO');
        $sheet->setCellValue('I1', 'QTY DITERIMA');
        $sheet->setCellValue('J1', 'HARGA');
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $no = 1;
        $numRow = 2;

        foreach ($result as $row) :
            $sheet->setCellValue('A' . $numRow, $no);
            $sheet->setCellValue('B' . $numRow, $row['po_date']);
            $sheet->setCellValue('C' . $numRow, $row['lpb_date']);
            $sheet->setCellValue('D' . $numRow, $row['lpb_no']);
            $sheet->setCellValue('E' . $numRow, $row['po_no']);
            $sheet->setCellValue('F' . $numRow, $row['kode_barang']);
            $sheet->setCellValue('G' . $numRow, $row['barang_name']);
            $sheet->setCellValue('H' . $numRow, $row['qty_po']);
            $sheet->setCellValue('I' . $numRow, $row['qty_lpb']);
            $sheet->setCellValue('J' . $numRow, $row['harga']);
            // Auto size columns A-E
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getColumnDimension('H')->setAutoSize(true);
            $sheet->getColumnDimension('I')->setAutoSize(true);
            $sheet->getColumnDimension('J')->setAutoSize(true);
            $no++;
            $numRow++;
        endforeach;

        $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow())
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . strlen($excelOutput));

        echo $excelOutput;
        exit();
    }

    public function getListDataPurchaseOrderExport($poType, $supplierId, $startDate, $endDate, $isAll = false)
    {
        // GET PO YANG SUDAH DIGUNAKAN & POSTING
        $lpbUsed = $this->bcPurchaseOrderModel
            ->where('supplier_id', $supplierId)
            ->where('po_type', $poType)
            ->where('deletedAt', null)
            ->findAll();
        $lpbUsedArr = [];

        foreach ($lpbUsed as $p) {
            $lpbArr = json_decode($p['multiple_lpb_id']);
            foreach ($lpbArr as $l) {
                array_push($lpbUsedArr, $l);
            }
        }

        $result = [];

        if ($poType == "LOKAL BAKU") {
            // PO LOKAL BAHAN BAKU
            $poQry = $this->penerimaanBarangModel
                ->select('
                    penerimaan_barang.id,
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang,
                    penerimaan_barang_detail.purchase_order_id,
                    suppliers.name as supplier_name,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    penerimaan_barang_detail.barang_id,
                    rm_purchase_orders.po_no,
                    rm_purchase_orders.po_date,
                    rm_purchase_orders.total_before_pph as sub_total,
                    satuan_lpb.kode_satuan as kode_satuan_lpb,
                    satuan_po.kode_satuan as kode_satuan_po,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ", ") AS spesifikasi
                ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id', 'left')
                ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                ->join('satuans satuan_lpb', 'satuan_lpb.id = penerimaan_barang_detail.unit_konversi', 'left')
                ->join('satuans satuan_po', 'satuan_po.id = penerimaan_barang_detail.unit', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang.company_id', $this->this_company_id);

            if (!empty($supplierId) || $supplierId != "") {
                $poQry->where('penerimaan_barang.supplier_id', $supplierId);
            }

            $poQry->groupBy('penerimaan_barang.id');
            $poQry->groupBy('penerimaan_barang_detail.barang_id');
            $poQry->orderBy('rm_purchase_orders.po_date', "DESC");

            if (!empty($startDate) || $startDate != '') {
                $poQry->having('rm_purchase_orders.po_date >=', $startDate);
            }

            if (!empty($endDate) || $endDate != '') {
                $poQry->having('rm_purchase_orders.po_date <=', $endDate);
            }

            $po = $poQry->findAll();
        } else if ($poType == "LOKAL PENOLONG") {
            // PO LOKAL BAHAN PENOLONG
            $poQry = $this->penerimaanBarangModel
                ->select('
                penerimaan_barang.id,
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.purchase_order_id,
                suppliers.name as supplier_name,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                satuan_lpb.kode_satuan as kode_satuan_lpb,
                satuan_po.kode_satuan as kode_satuan_po,
                purchase_requests.spp_no,
                barang_master.barang_name,
                barang_master.kode_barang,
                barang_master_spesifikasi.spesifikasi
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
                ->join('satuans satuan_lpb', 'satuan_lpb.id = penerimaan_barang_detail.unit_konversi', 'left')
                ->join('satuans satuan_po', 'satuan_po.id = penerimaan_barang_detail.unit', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang.company_id', $this->this_company_id);

            if (!empty($supplierId) || $supplierId != "") {
                $poQry->where('penerimaan_barang.supplier_id', $supplierId);
            }

            $poQry->groupBy('penerimaan_barang.id');
            $poQry->groupBy('penerimaan_barang_detail.spesifikasi_id');
            $poQry->orderBy('am_purchase_orders.po_date', "DESC");

            if (!empty($startDate) || $startDate != '') {
                $poQry->having('am_purchase_orders.po_date >=', $startDate);
            }

            if (!empty($endDate) || $endDate != '') {
                $poQry->having('am_purchase_orders.po_date <=', $endDate);
            }

            $po = $poQry->findAll();
        } elseif ($poType == "IMPORT BAKU") {
            // PO IMPORT BAHAN BAKU
            $poQry = $this->penerimaanBarangModel
                ->select('
                    penerimaan_barang.id,
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang,
                    penerimaan_barang_detail.purchase_order_id,
                    suppliers.name as supplier_name,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                    satuan_lpb.kode_satuan as kode_satuan_lpb,
                    satuan_po.kode_satuan as kode_satuan_po,
                    metadata.value as valas,
                    penerimaan_barang_detail.barang_id,
                    rm_import_pos.po_no,
                    rm_import_pos.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    barang_master_spesifikasi.spesifikasi
                ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_import_pos', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
                ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                ->join('satuans satuan_lpb', 'satuan_lpb.id = penerimaan_barang_detail.unit_konversi', 'left')
                ->join('satuans satuan_po', 'satuan_po.id = penerimaan_barang_detail.unit', 'left')
                ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang.company_id', $this->this_company_id);

            if (!empty($supplierId) || $supplierId != "") {
                $poQry->where('penerimaan_barang.supplier_id', $supplierId);
            }

            $poQry->groupBy('penerimaan_barang.id');
            $poQry->groupBy('penerimaan_barang_detail.spesifikasi_id');
            $poQry->orderBy('rm_import_pos.po_date', "DESC");

            if (!empty($startDate) || $startDate != '') {
                $poQry->having('rm_import_pos.po_date >=', $startDate);
            }

            if (!empty($endDate) || $endDate != '') {
                $poQry->having('rm_import_pos.po_date <=', $endDate);
            }

            $po = $poQry->findAll();
        } elseif ($poType == "IMPORT PENOLONG") {
            // PO IMPORT BAHAN PENOLONG
            $poQry = $this->penerimaanBarangModel
                ->select('
                penerimaan_barang.id,
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.purchase_order_id,
                suppliers.name as supplier_name,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.jml_masuk_konversi) AS qty_lpb_konversi,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                satuan_lpb.kode_satuan as kode_satuan_lpb,
                satuan_po.kode_satuan as kode_satuan_po,
                metadata.value as valas,
                penerimaan_barang_detail.barang_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                barang_master.barang_name,
                barang_master.kode_barang,
                barang_master_spesifikasi.spesifikasi
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                ->join('satuans satuan_lpb', 'satuan_lpb.id = penerimaan_barang_detail.unit_konversi', 'left')
                ->join('satuans satuan_po', 'satuan_po.id = penerimaan_barang_detail.unit', 'left')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang.company_id', $this->this_company_id);

            if (!empty($supplierId) || $supplierId != "") {
                $poQry->where('penerimaan_barang.supplier_id', $supplierId);
            }

            $poQry->groupBy('penerimaan_barang.id');
            $poQry->groupBy('penerimaan_barang_detail.spesifikasi_id');
            $poQry->orderBy('am_purchase_orders.po_date', "DESC");

            if (!empty($startDate) || $startDate != '') {
                $poQry->having('am_purchase_orders.po_date >=', $startDate);
            }

            if (!empty($endDate) || $endDate != '') {
                $poQry->having('am_purchase_orders.po_date <=', $endDate);
            }

            $po = $poQry->findAll();
        }

        foreach ($po as $p) {
            // if ($poType == "LOKAL BAKU") {
            //     // Khusus Bahan Baku Spek nya jadi satu dibuat koma
            //     $penerimaanBarangDetail = $this->penerimaanBarangDetailModel->select('barang_master_spesifikasi.spesifikasi')
            //         ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            //         ->where('penerimaan_barang_detail.penerimaan_barang_id', $p['id'])
            //         ->where('penerimaan_barang_detail.deletedAt', null)
            //         ->findAll();
            //     $p['spesifikasi'] = "";
            //     foreach ($penerimaanBarangDetail as $d) {
            //         $p['spesifikasi'] = $d['spesifikasi'] . ", ";
            //     }
            // }
            if ($isAll) {
                $result[] = [
                    'penerimaan_barang_id' => $p['id'],
                    'lpb_date' => date('d/m/Y', strtotime($p['lpb_date'])),
                    'lpb_no' => $p['no_penerimaan_barang'],
                    'spp_no' => isset($p['spp_no']) ? $p['spp_no'] : "",
                    'purchase_order_id' => $p['purchase_order_id'],
                    'qty_lpb_konversi' => round($p['qty_lpb_konversi'], 2),
                    'qty_lpb' => round($p['qty_lpb'], 2),
                    'qty_po' => round($p['qty_po'], 2),
                    'barang_id' => $p['barang_id'],
                    'po_no' => $p['po_no'],
                    'po_date' => date('d/m/Y', strtotime($p['po_date'])),
                    'barang_name' => $p['barang_name'] . " - " . $p['spesifikasi'],
                    'kode_barang' => $p['kode_barang'],
                    'harga' => number_format($p['sub_total'], 2),
                    'harga_number' => $p['sub_total'],
                    'supplier_name' => $p['supplier_name'],
                    'kode_satuan_lpb' => $p['kode_satuan_lpb'],
                    'kode_satuan_po' => $p['kode_satuan_po'],
                    'valas' => isset($p['valas']) ? $p['valas'] : ""
                ];
            } else {
                if (!in_array($p['id'], $lpbUsedArr)) {
                    $result[] = [
                        'penerimaan_barang_id' => $p['id'],
                        'lpb_date' => date('d/m/Y', strtotime($p['lpb_date'])),
                        'lpb_no' => $p['no_penerimaan_barang'],
                        'spp_no' => isset($p['spp_no']) ? $p['spp_no'] : "",
                        'purchase_order_id' => $p['purchase_order_id'],
                        'qty_lpb_konversi' => round($p['qty_lpb_konversi'], 2),
                        'qty_lpb' => round($p['qty_lpb'], 2),
                        'qty_po' => round($p['qty_po'], 2),
                        'barang_id' => $p['barang_id'],
                        'po_no' => $p['po_no'],
                        'po_date' => date('d/m/Y', strtotime($p['po_date'])),
                        'barang_name' => $p['barang_name'] . " - " . $p['spesifikasi'],
                        'kode_barang' => $p['kode_barang'],
                        'harga' => number_format($p['sub_total'], 2),
                        'harga_number' => $p['sub_total'],
                        'supplier_name' => $p['supplier_name'],
                        'kode_satuan_lpb' => $p['kode_satuan_lpb'],
                        'kode_satuan_po' => $p['kode_satuan_po'],
                        'valas' => isset($p['valas']) ? $p['valas'] : ""
                    ];
                }
            }
        }

        return $result;
    }

    private function insertInventori($bcPurchaseOrderID)
    {
        try {
            $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
            $poIdArr = array_unique(json_decode($bcPo['multiple_po_id']));
            $lpbIdArr = array_unique(json_decode($bcPo['multiple_lpb_id']));
            $typeBahan = $bcPo['po_type'] == "LOKAL BAKU" ? "bahan_baku" : "bahan_penolong";

            foreach ($lpbIdArr as $index => $lpbId) {
                $statusInputStock = true;

                if ($typeBahan == "bahan_baku") {
                    $rmPurchaseOrder = $this->rmPurchaseOrderModel->where('id', $poIdArr[$index])->first();
                    if ($rmPurchaseOrder['status_external'] == "yes") {
                        // JIKA STATUS EKSTERNAL YES GA USAH INSERT KE INVENTORI
                        $statusInputStock = false;
                    }
                }

                if ($statusInputStock) {
                    $this->penerimaanBarangLokalBp->insert_stock_pembelian_revamp(
                        $lpbId
                    );
                }
            }

            return true;
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }

    public function viewOutstanding()
    {
        $data = [
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('BeaCukai/bc-40/bc40outstanding', $data);
    }

    public function allOutstandingServerSide()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $searchValue = $this->request->getGet('search');
        $dateStart = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $dateEnd =  $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";
        $tipeBahan = $this->request->getGet('tipe_bahan');
        $divisiId = $this->request->getGet('divisi_id');

        $db = \Config\Database::connect();

        // --- Cari LPB yang sudah dipakai ---
        $lpbUsed = $this->bcPurchaseOrderModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        $lpbUsedArr = [];
        foreach ($lpbUsed as $used) {
            $usedIds = json_decode($used['multiple_lpb_id']);
            if (is_array($usedIds)) {
                $lpbUsedArr = array_merge($lpbUsedArr, $usedIds);
            }
        }

        // --- Build Filter ---
        $where = [];
        if (!empty($dateStart) && !empty($dateEnd)) {
            $where[] = "pb.tanggal BETWEEN '$dateStart' AND '$dateEnd'";
        }
        if (!empty($tipeBahan)) {
            $where[] = "pb.tipe_bahan = '$tipeBahan'";
        }
        if (!empty($divisiId)) {
            $where[] = "pb.divisi_id = '$divisiId'";
        }
        if (!empty($searchValue)) {
            $search = $db->escapeLikeString($searchValue);
            $where[] = "(s.name LIKE '%$search%' 
                     OR bm.barang_name LIKE '%$search%' 
                     OR pb.no_penerimaan_barang LIKE '%$search%' 
                     OR pod.po_no LIKE '%$search%')";
        }
        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        // --- LPB sudah dipakai ---
        $lpbUsedSubquery = "";
        if (!empty($lpbUsedArr)) {
            $ids = implode(',', array_map('intval', $lpbUsedArr));
            $lpbUsedSubquery = " AND pb.id NOT IN ($ids)";
        }

        // --- Base Query (dua UNION) ---
        $baseQuery = "
            (
                SELECT 
                    pb.id,
                    pb.tanggal AS lpb_date,
                    pb.no_penerimaan_barang,
                    pb.status_penerimaan,
                    pb.tipe_bahan,
                    pb.jumlah_kemasan,
                    divisis.divisi,
                    pod.po_no,
                    pod.po_date,
                    bm.barang_name,
                    bm.kode_barang,
                    s.name AS supplier,
                    satuan_lpb.kode_satuan AS kode_satuan_lpb,
                    satuan_po.kode_satuan AS kode_satuan_po,
                    satuan_kemasan.kode_satuan AS kode_satuan_kemasan,
                    kemasan.name AS nama_kemasan,
                    SUM(pbd.qty) AS qty_po,
                    SUM(pbd.jml_masuk) AS qty_lpb,
                    pod.total_before_pph AS sub_total,
                    pb.updatedAt
                FROM penerimaan_barang pb
                JOIN penerimaan_barang_detail pbd 
                    ON pbd.penerimaan_barang_id = pb.id 
                    AND pbd.deletedAt IS NULL
                LEFT JOIN rm_purchase_orders pod ON pod.id = pbd.purchase_order_id
                LEFT JOIN barang_master bm ON bm.id = pbd.barang_id
                LEFT JOIN suppliers s ON s.id = pb.supplier_id
                LEFT JOIN satuans satuan_lpb ON satuan_lpb.id = pbd.unit_konversi
                LEFT JOIN satuans satuan_po ON satuan_po.id = pbd.unit
                LEFT JOIN kemasan ON kemasan.id = pb.kemasan_id
                LEFT JOIN satuans satuan_kemasan ON satuan_kemasan.id = kemasan.satuan_id
                LEFT JOIN divisis ON divisis.id = pb.divisi_id
                WHERE pb.deletedAt IS NULL
                AND pb.status_penerimaan = 'LOKAL'
                AND pb.status_post = 'FINISH'
                AND pb.bc_type = '53'
                AND pb.company_id = '{$this->this_company_id}'
                AND pb.tipe_bahan = 'BAKU'
                $filterCondition
                $lpbUsedSubquery
                GROUP BY pb.id, pbd.barang_id
            )
            UNION ALL
            (
                SELECT 
                    pb.id,
                    pb.tanggal AS lpb_date,
                    pb.no_penerimaan_barang,
                    pb.status_penerimaan,
                    pb.tipe_bahan,
                    pb.jumlah_kemasan,
                    divisis.divisi,
                    pod.po_no,
                    pod.po_date,
                    bm.barang_name,
                    bm.kode_barang,
                    s.name AS supplier,
                    satuan_lpb.kode_satuan AS kode_satuan_lpb,
                    satuan_po.kode_satuan AS kode_satuan_po,
                    satuan_kemasan.kode_satuan AS kode_satuan_kemasan,
                    kemasan.name AS nama_kemasan,
                    SUM(pbd.qty) AS qty_po,
                    SUM(pbd.jml_masuk) AS qty_lpb,
                    SUM(pbd.sub_total) AS sub_total,
                    pb.updatedAt
                FROM penerimaan_barang pb
                JOIN penerimaan_barang_detail pbd 
                    ON pbd.penerimaan_barang_id = pb.id 
                    AND pbd.deletedAt IS NULL
                LEFT JOIN am_purchase_orders pod ON pod.id = pbd.purchase_order_id
                LEFT JOIN barang_master bm ON bm.id = pbd.barang_id
                LEFT JOIN suppliers s ON s.id = pb.supplier_id
                LEFT JOIN satuans satuan_lpb ON satuan_lpb.id = pbd.unit_konversi
                LEFT JOIN satuans satuan_po ON satuan_po.id = pbd.unit
                LEFT JOIN kemasan ON kemasan.id = pb.kemasan_id
                LEFT JOIN satuans satuan_kemasan ON satuan_kemasan.id = kemasan.satuan_id
                LEFT JOIN divisis ON divisis.id = pb.divisi_id
                WHERE pb.deletedAt IS NULL
                AND pb.status_penerimaan = 'LOKAL'
                AND pb.status_post = 'FINISH'
                AND pb.bc_type = '53'
                AND pb.company_id = '{$this->this_company_id}'
                AND pb.tipe_bahan = 'PENOLONG'
                $filterCondition
                $lpbUsedSubquery
                GROUP BY pb.id, pbd.barang_id
            )
            ";

        // --- Ambil order dari DataTables ---
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';

        // Mapping index kolom DataTables ke nama kolom SQL
        $columns = [
            'id',
            'updatedAt',
            'tipe_bahan',
            'divisi',
            'supplier',
            'po_date',
            'lpb_date',
            'no_penerimaan_barang',
            'po_no',
            'kode_barang',
            'barang_name',
            'qty_po',
            'qty_lpb',
            'nama_kemasan',
            'jumlah_kemasan',
            'sub_total',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        // --- Hitung total records (tanpa LIMIT) ---
        $countQuery = "SELECT COUNT(*) as cnt FROM ($baseQuery) as x";
        $totalFiltered = $db->query($countQuery)->getRow()->cnt;
        $totalRecords = $totalFiltered;



        // --- Data dengan pagination ---
        $mainQuery = $baseQuery . " $orderBy LIMIT $length OFFSET $start";
        $data = $db->query($mainQuery)->getResultArray();

        // --- Formatting untuk DataTables ---
        $formatted = [];
        $no = $start + 1;

        foreach ($data as $row) {
            $formatted[] = [
                'no' => $no++,
                'tipe_bahan' => "LOKAL " . $row['tipe_bahan'],
                'divisi' => $row['divisi'],
                'supplier' => $row['supplier'],
                'po_date' => date('d/m/Y', strtotime($row['po_date'])),
                'lpb_date' => date('d/m/Y', strtotime($row['lpb_date'])),
                'no_penerimaan_barang' => $row['no_penerimaan_barang'],
                'po_no' => $row['po_no'],
                'kode_barang' => $row['kode_barang'],
                'barang' => $row['barang_name'],
                'qty_po' => number_format($row['qty_po'], 2) . " " . $row['kode_satuan_po'],
                'qty_lpb' => number_format($row['qty_lpb'], 2) . " " . $row['kode_satuan_lpb'],
                'kemasan' => $row['nama_kemasan'],
                'qty_kemasan' => number_format($row['jumlah_kemasan'], 2) . " " . $row['kode_satuan_kemasan'],
                'sub_total' => number_format($row['sub_total'], 2),
                'updated_at' => date('d/m/Y H:i:s', strtotime($row['updatedAt']))
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $formatted,
        ]);
    }


    public function allOutstandingExcel()
    {
        $dateStart = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $dateEnd =  $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";
        $tipeBahan = $this->request->getGet('tipe_bahan');
        $divisiId = $this->request->getGet('divisi_id');

        $db = \Config\Database::connect();

        // --- Cari LPB yang sudah dipakai ---
        $lpbUsed = $this->bcPurchaseOrderModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        $lpbUsedArr = [];
        foreach ($lpbUsed as $used) {
            $usedIds = json_decode($used['multiple_lpb_id']);
            if (is_array($usedIds)) {
                $lpbUsedArr = array_merge($lpbUsedArr, $usedIds);
            }
        }

        // --- Build Filter ---
        $where = [];
        if (!empty($dateStart) && !empty($dateEnd)) {
            $where[] = "pb.tanggal BETWEEN '$dateStart' AND '$dateEnd'";
        }
        if (!empty($tipeBahan)) {
            $where[] = "pb.tipe_bahan = '$tipeBahan'";
        }
        if (!empty($divisiId)) {
            $where[] = "pb.divisi_id = '$divisiId'";
        }
        if (!empty($searchValue)) {
            $search = $db->escapeLikeString($searchValue);
            $where[] = "(s.name LIKE '%$search%' 
                     OR bm.barang_name LIKE '%$search%' 
                     OR pb.no_penerimaan_barang LIKE '%$search%' 
                     OR pod.po_no LIKE '%$search%')";
        }
        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        // --- LPB sudah dipakai ---
        $lpbUsedSubquery = "";
        if (!empty($lpbUsedArr)) {
            $ids = implode(',', array_map('intval', $lpbUsedArr));
            $lpbUsedSubquery = " AND pb.id NOT IN ($ids)";
        }

        // --- Base Query (dua UNION) ---
        $baseQuery = "
            (
                SELECT 
                    pb.id,
                    pb.tanggal AS lpb_date,
                    pb.no_penerimaan_barang,
                    pb.status_penerimaan,
                    pb.tipe_bahan,
                    pb.jumlah_kemasan,
                    divisis.divisi,
                    pod.po_no,
                    pod.po_date,
                    bm.barang_name,
                    bm.kode_barang,
                    s.name AS supplier,
                    satuan_lpb.kode_satuan AS kode_satuan_lpb,
                    satuan_po.kode_satuan AS kode_satuan_po,
                    satuan_kemasan.kode_satuan AS kode_satuan_kemasan,
                    kemasan.name AS nama_kemasan,
                    SUM(pbd.qty) AS qty_po,
                    SUM(pbd.jml_masuk) AS qty_lpb,
                    pod.total_before_pph AS sub_total,
                    pb.updatedAt
                FROM penerimaan_barang pb
                JOIN penerimaan_barang_detail pbd 
                    ON pbd.penerimaan_barang_id = pb.id 
                    AND pbd.deletedAt IS NULL
                LEFT JOIN rm_purchase_orders pod ON pod.id = pbd.purchase_order_id
                LEFT JOIN barang_master bm ON bm.id = pbd.barang_id
                LEFT JOIN suppliers s ON s.id = pb.supplier_id
                LEFT JOIN satuans satuan_lpb ON satuan_lpb.id = pbd.unit_konversi
                LEFT JOIN satuans satuan_po ON satuan_po.id = pbd.unit
                LEFT JOIN kemasan ON kemasan.id = pb.kemasan_id
                LEFT JOIN satuans satuan_kemasan ON satuan_kemasan.id = kemasan.satuan_id
                LEFT JOIN divisis ON divisis.id = pb.divisi_id
                WHERE pb.deletedAt IS NULL
                AND pb.status_penerimaan = 'LOKAL'
                AND pb.status_post = 'FINISH'
                AND pb.bc_type = '53'
                AND pb.company_id = '{$this->this_company_id}'
                AND pb.tipe_bahan = 'BAKU'
                $filterCondition
                $lpbUsedSubquery
                GROUP BY pb.id, pbd.barang_id
            )
            UNION ALL
            (
                SELECT 
                    pb.id,
                    pb.tanggal AS lpb_date,
                    pb.no_penerimaan_barang,
                    pb.status_penerimaan,
                    pb.tipe_bahan,
                    pb.jumlah_kemasan,
                    divisis.divisi,
                    pod.po_no,
                    pod.po_date,
                    bm.barang_name,
                    bm.kode_barang,
                    s.name AS supplier,
                    satuan_lpb.kode_satuan AS kode_satuan_lpb,
                    satuan_po.kode_satuan AS kode_satuan_po,
                    satuan_kemasan.kode_satuan AS kode_satuan_kemasan,
                    kemasan.name AS nama_kemasan,
                    SUM(pbd.qty) AS qty_po,
                    SUM(pbd.jml_masuk) AS qty_lpb,
                    SUM(pbd.sub_total) AS sub_total,
                    pb.updatedAt
                FROM penerimaan_barang pb
                JOIN penerimaan_barang_detail pbd 
                    ON pbd.penerimaan_barang_id = pb.id 
                    AND pbd.deletedAt IS NULL
                LEFT JOIN am_purchase_orders pod ON pod.id = pbd.purchase_order_id
                LEFT JOIN barang_master bm ON bm.id = pbd.barang_id
                LEFT JOIN suppliers s ON s.id = pb.supplier_id
                LEFT JOIN satuans satuan_lpb ON satuan_lpb.id = pbd.unit_konversi
                LEFT JOIN satuans satuan_po ON satuan_po.id = pbd.unit
                LEFT JOIN kemasan ON kemasan.id = pb.kemasan_id
                LEFT JOIN satuans satuan_kemasan ON satuan_kemasan.id = kemasan.satuan_id
                LEFT JOIN divisis ON divisis.id = pb.divisi_id
                WHERE pb.deletedAt IS NULL
                AND pb.status_penerimaan = 'LOKAL'
                AND pb.status_post = 'FINISH'
                AND pb.bc_type = '53'
                AND pb.company_id = '{$this->this_company_id}'
                AND pb.tipe_bahan = 'PENOLONG'
                $filterCondition
                $lpbUsedSubquery
                GROUP BY pb.id, pbd.barang_id
            )
            ";

        $data = $db->query($baseQuery)->getResultArray();

        $formatted = [];
        $no = 1;

        // Tambahkan di awal fungsi setelah inisialisasi $formatted
        $totalSubTotal = 0;

        foreach ($data as $row) {
            $totalSubTotal += floatval($row['sub_total']); // Kumpulkan total as angka

            $formatted[] = [
                'no' => $no++,
                'tipe_bahan' => "LOKAL " . $row['tipe_bahan'],
                'divisi' => $row['divisi'],
                'supplier' => $row['supplier'],
                'po_date' => date('d/m/Y', strtotime($row['po_date'])),
                'lpb_date' => date('d/m/Y', strtotime($row['lpb_date'])),
                'no_penerimaan_barang' => $row['no_penerimaan_barang'],
                'po_no' => $row['po_no'],
                'kode_barang' => $row['kode_barang'],
                'barang' => $row['barang_name'],
                'qty_po' => floatval($row['qty_po']),
                'satuan_po' => $row['kode_satuan_po'],
                'qty_lpb' => floatval($row['qty_lpb']),
                'satuan_lpb' => $row['kode_satuan_lpb'],
                'kemasan' => $row['nama_kemasan'],
                'qty_kemasan' => floatval($row['jumlah_kemasan']),
                'satuan_kemasan' => $row['kode_satuan_kemasan'],
                'sub_total' => floatval($row['sub_total']),
                'updated_at' => date('d/m/Y H:i:s', strtotime($row['updatedAt']))
            ];
        }

        // Spreadsheet + headers
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'No',
            'Tipe Bahan',
            'Departemen',
            'Supplier',
            'Tgl PO',
            'Tgl LPB',
            'No LPB',
            'No PO',
            'Kode',
            'Barang',
            'Qty PO',
            'Satuan PO',
            'Qty LPB',
            'Satuan LPB',
            'Kemasan',
            'Qty Kemasan',
            'Satuan Kemasan',
            'Sub Total',
            'Tgl Posting PO'
        ];

        $sheet->fromArray($headers, NULL, 'A1');

        // Bold header
        $sheet->getStyle('A1:S1')->applyFromArray([
            'font' => ['bold' => true],
        ]);

        // Data rows
        $rowNum = 2;
        $no = 1;
        foreach ($formatted as $f) {
            $sheet->setCellValue('A' . $rowNum, $no++)
                ->setCellValue('B' . $rowNum, $f['tipe_bahan'])
                ->setCellValue('C' . $rowNum, $f['divisi'])
                ->setCellValue('D' . $rowNum, $f['supplier'])
                ->setCellValue('E' . $rowNum, $f['po_date'])
                ->setCellValue('F' . $rowNum, $f['lpb_date'])
                ->setCellValue('G' . $rowNum, $f['no_penerimaan_barang'])
                ->setCellValue('H' . $rowNum, $f['po_no'])
                ->setCellValue('I' . $rowNum, $f['kode_barang'])
                ->setCellValue('J' . $rowNum, $f['barang'])
                ->setCellValue('K' . $rowNum, $f['qty_po'])
                ->setCellValue('L' . $rowNum, $f['satuan_po'])
                ->setCellValue('M' . $rowNum, $f['qty_lpb'])
                ->setCellValue('N' . $rowNum, $f['satuan_lpb'])
                ->setCellValue('O' . $rowNum, $f['kemasan'])
                ->setCellValue('P' . $rowNum, $f['qty_kemasan'])
                ->setCellValue('Q' . $rowNum, $f['satuan_kemasan'])
                ->setCellValue('R' . $rowNum, $f['sub_total'])
                ->setCellValue('S' . $rowNum, $f['updated_at']);
            $rowNum++;
        }

        // Grand Total row
        $sheet->setCellValue('Q' . $rowNum, 'GRAND TOTAL');
        $sheet->setCellValue('R' . $rowNum, $totalSubTotal);

        // Bold Grand Total row
        $sheet->getStyle("Q$rowNum:S$rowNum")->applyFromArray([
            'font' => ['bold' => true]
        ]);

        // Border untuk semua
        $lastRow = $rowNum;
        $sheet->getStyle("A1:S$lastRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Auto size
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format kolom Sub Total sebagai Rupiah
        // Format angka di kolom Sub Total (kolom R), tanpa simbol Rp
        $sheet->getStyle("R2:R$lastRow")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');



        // Border
        $sheet->getStyle("A1:S$lastRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);




        // Export
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan Outstanding BC 4.0 ' . $dateStart . " s.d " . $dateEnd;

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }


    public function unPosting()
    {
        try {
            $db = Database::connect();
            $db->transBegin();
            // BC PURCHASE ORDER ID
            $id = decrypt($this->request->getVar('id'));
            $this->bcPurchaseOrderModel->update($id, [
                'status_posting' => '0'
            ]);
            $db->transCommit();
            return response()->setJSON([
                'status' => true,
                'message' => "Dokumen berhasil di unpost ",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => $e->getTrace(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function dropdownSupplier()
    {
        $poType = $this->request->getVar('po_type');
        if (empty($poType)) {
            return response()->setJSON([
                'token' => csrf_token(),
                'data' => [],
                'status' => true
            ]);
        }

        if ($poType == "LOKAL BAKU") {
            // Supplier Lokal Bahan Baku
            $supplierData = $this->supplierModel->getSupplierByType("BAHAN BAKU");
        } else {
            // Supplier Lokal Bahan Penolong
            $supplierData = $this->supplierModel->getSupplierByType("BAHAN PENOLONG");
        }

        return response()->setJSON([
            'token' => csrf_token(),
            'data' => $supplierData,
            'status' => true,
        ]);
    }

    public function updateNoAjuBulk()
    {
        $bcPurchaseOrderID = $this->request->getVar('bc_purchase_order_id');
        $noAjuNew = $this->request->getVar('no_pengajuan');
        $noDaftar = $this->request->getVar('no_daftar');
        $tanggalDokumen = $this->request->getVar("tanggal_dokumen") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_dokumen")))) : "";
        // Get

        // Periksa Nomor Aju
        $bc40First = $this->bc40Model
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->where('no_aju', $noAjuNew)
            ->where('bc_purchase_order_id !=', $bcPurchaseOrderID)
            ->first();

        if ($bc40First != null) {
            return response()->setJSON([
                'token' => csrf_token(),
                'message' => "No Aju Sudah Digunakan",
                'status' => true,
            ]);
        } else {
            $this->bcPurchaseOrderModel->update($bcPurchaseOrderID, [
                'createdAt' => $tanggalDokumen,
                'no_daftar' => $noDaftar,
                'no_aju' => $noAjuNew,
                'bc_id' => 53,
                'bc_type' => "BC 4.0"
            ]);

            $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->set('no_aju', $noAjuNew)
                ->update();

            return response()->setJSON([
                'token' => csrf_token(),
                'message' => "No Aju & No Daftar Berhasil Diupdate",
                'status' => true,
            ]);
        }
    }

    public function syncNoDaftar()
    {
        try {
            $bcPurchaseOrderId = $this->request->getVar('bc_purchase_order_id');
            $bc40 = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderId)->first();

            $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
            $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);
            $beacukaiApi = new BeaCukaiApi($username, $password);
            $noAju = str_replace('-', '',   $bc40['no_aju']);

            $response = $beacukaiApi->getResponseByNoAju(
                $noAju
            );

            if ($response->status == "Failed") {
                return response()->setJSON([
                    'status' => false,
                    'message' => $response->message
                ]);
            }

            $this->bcPurchaseOrderModel->update($bcPurchaseOrderId, [
                'no_daftar' => $response->dataRespon[0]->nomorDaftar
            ]);

            return response()->setJSON([
                'status' => true,
                'message' => "No Daftar berhasil disinkronkan dengan sistem Ceisa"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
