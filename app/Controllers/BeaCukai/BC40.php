<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BC40Model;
use App\Models\BCBarangDokumenModel;
use App\Models\BCBarangModel;
use App\Models\BCBarangTarifModel;
use App\Models\BCDokumenModel;
use App\Models\BCEntitasModel;
use App\Models\BCKemasanModel;
use App\Models\BCKontainerModel;
use App\Models\BCPengangkutModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\CeisaSettingModel;
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
            "statusBC" => $this->request->getGet("statusBC"),
            "statusLPB" => $this->request->getGet("statusLPB"),
            "supplierName" => $this->request->getGet("supplierName"),
            "mulaiTanggalBC40" => $this->request->getGet("mulaiTanggalBC40"),
            "selesaiTanggalBC40" => $this->request->getGet('selesaiTanggalBC40'),
            "noPenerimaanBarang" => $this->request->getGet('noPenerimaanBarang'),
            "noAju" => $this->request->getGet('noAju')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc40Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $bc40 = $this->bc40Model->where('bc_purchase_order_id', $data->bc_purchase_order_id)->first();
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->bc_purchase_order_id),
                "bc_no_lokal"           => $data->bc_no_lokal,
                "tanggal_bc_40"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "no_aju"                => $data->no_aju,
                "po_type"               => $data->po_type,
                "lpb_no"                => str_replace(['"', ']', '['], " ",  $data->multiple_lpb_no),
                "po_no"                 => str_replace(['"', ']', '['], " ",  $data->multiple_po_no),
                "supplier_name"         => strtoupper($data->supplier_name),
                "status"                => strtoupper($bc40 == null ? "BELUM DIBUAT" : $data->status_dokumen),
                "status_posting"        => $data->status_posting,
                "is_update_no_aju"      => $bc40 == null ? false : ($data->status_posting === "1" ? false : true),
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

        $id = $this->bcPurchaseOrderModel->insert([
            'supplier_id' => $this->request->getVar('supplier_id'),
            'company_id' => $this->this_company_id,
            'po_type' => $this->request->getVar('po_type'),
            'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($poIdArr)),
            'multiple_lpb_id' => str_replace(['\\"', '\\', '"'], '', json_encode($lpbIdArr)),
            'multiple_po_no' => str_replace(['\\"', '\\'], '', json_encode($poNoArr)),
            'multiple_lpb_no' => str_replace(['\\"', '\\'], '', json_encode($lpbNoArr)),
        ]);

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
        $noAju = $this->generateNomorAju();

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
            'daftarPoUsed' => $this->bcPurchaseOrderModel->findDetailBarang($bcPurchaseOrderID),
        ];

        return view('BeaCukai/bc-40/form-po-list', $data);
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
        ]);

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

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $data = [
            'bc40' => $bc40,
            'noAju' => $bc40 == null ? $this->generateNomorAju() : $bc40['no_aju'],
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

        $lastData = $this->bc40Model->get($bcPurchaseOrderID);
        if ($lastData == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $this->bc40Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_no_lokal' => $this->bc40Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

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

            if ($lastData['bc_no_lokal'] == null) {
                $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                $this->bc40Model->update($lastData['id'], [
                    'bc_no_lokal' => $this->bc40Model->getNo(date('m'), date('Y'), $last_day),
                ]);
            }

            if ($lastData['no_aju'] == null) {
                $this->bc40Model->update($lastData['id'], [
                    'no_aju' => $this->generateNomorAju(),
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
                'nomor_identitas' => $this->request->getVar('pengusaha_tpb_npwp'),
                'nomor_ijin_entitas' => $this->request->getVar('pengusaha_tpb_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('pengirim_nama'),
                'alamat_pemasok' => $this->request->getVar('pengirim_alamat'),
                'npwp_pemasok' => $this->request->getVar('pengirim_npwp'),
                'npwp_pemilik_barang' => $this->request->getVar('pemilik_barang_npwp'),
                'nama_pemilik_barang' => $this->request->getVar('pemilik_barang_nama'),
                'alamat_pemilik_barang' => $this->request->getVar('pemilik_barang_alamat')
            ]);
        } else {
            // update
            $this->bcEntitasModel->update($lastData['id'], [
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_type' => 40,
                'alamat_entitas' => $this->request->getVar('pengusaha_tpb_alamat'),
                'nama_entitas' => $this->request->getVar('pengusaha_tpb_nama'),
                'nib_entitas' => $this->request->getVar('pengusaha_tpb_nib'),
                'nomor_identitas' => $this->request->getVar('pengusaha_tpb_npwp'),
                'nomor_ijin_entitas' => $this->request->getVar('pengusaha_tpb_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pengusaha_tpb_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('pengirim_nama'),
                'alamat_pemasok' => $this->request->getVar('pengirim_alamat'),
                'npwp_pemasok' => $this->request->getVar('pengirim_npwp'),
                'npwp_pemilik_barang' => $this->request->getVar('pemilik_barang_npwp'),
                'nama_pemilik_barang' => $this->request->getVar('pemilik_barang_nama'),
                'alamat_pemilik_barang' => $this->request->getVar('pemilik_barang_alamat')
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

        $data = [
            'kodeDokumen' => $this->metaDataModel
                ->where('name', "Dokumen")
                ->orderBy('description', "ASC")
                ->findAll(),
            'bcPo' => $bcPo
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

        $lastData = $this->bc40Model->get($bcPurchaseOrderID);
        if ($lastData == null) {
            // insert
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $this->bc40Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_no_lokal' => $this->bc40Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

                // 'harga_penyerahan' => convertRupiahToNumber($this->request->getVar('harga_nilai_pabean')),
                'nilai_jasa' => convertRupiahToNumber($this->request->getVar('nilai_jasa')),
                'uang_muka' => convertRupiahToNumber($this->request->getVar('nilai_uang_muka')),
                'harga_perolehan' => convertRupiahToNumber($this->request->getVar('harga_perolehan')),
                'volume' => convertRupiahToNumber($this->request->getVar('volume')),
                'bruto' => convertRupiahToNumber($this->request->getVar('berat_kotor')),
                'netto' => convertRupiahToNumber($this->request->getVar('berat_bersih')),
            ]);
        } else {
            $this->bc40Model->update($lastData['id'], [
                // 'harga_penyerahan' => convertRupiahToNumber($this->request->getVar('harga_nilai_pabean')),
                'nilai_jasa' => convertRupiahToNumber($this->request->getVar('nilai_jasa')),
                'uang_muka' => convertRupiahToNumber($this->request->getVar('nilai_uang_muka')),
                'harga_perolehan' => convertRupiahToNumber($this->request->getVar('harga_perolehan')),
                'volume' => convertRupiahToNumber($this->request->getVar('volume')),
                'bruto' => convertRupiahToNumber($this->request->getVar('berat_kotor')),
                'netto' => convertRupiahToNumber($this->request->getVar('berat_bersih')),
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
        $bc40DokumenBarang = $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('penerimaan_barang_id', $penerimaanBarangID)->where('barang1_id', $barang1ID)->first();
        $seriBarang = $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->orderBy('createdAt', "DESC")->first();

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

        $data = [
            'kodeFasilitasTarif' => $this->metaDataModel->where('name', "Kode Fasilitas Tarif BC")->whereIn('description', ['TIDAK DIPUNGUT', 'DIBEBASKAN', 'DITANGGUHKAN'])->findAll(),
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
                'netto' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_berat_bersih'))),
                'harga_ekspor' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_harga_penyerahan'))),
                'nilai_tambah' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_harga_penggantian'))),
                'diskon' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_diskon'))),
                'volume' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_volume')))
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
                'netto' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_berat_bersih'))),
                'harga_ekspor' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_harga_penyerahan'))),
                'nilai_tambah' => trim(convertRupiahToNumber(trim($this->request->getVar('barang_detail_harga_penggantian')))),
                'diskon' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_diskon'))),
                'volume' => trim(convertRupiahToNumber($this->request->getVar('barang_detail_volume')))
            ]);
        }

        // UPDATE DATA DI TRANSAKSI
        $bc40 = $this->bc40Model->get($bcPurchaseOrderID);

        if ($bc40 == null) {
            $this->bc40Model->insert([
                'no_aju' => $this->generateNomorAju(),
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
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $barang1ID = decrypt($this->request->getVar('barang1_id'));

        $bc40Barang = $this->bcBarangModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->where('penerimaan_barang_id', $penerimaanBarangID)
            ->where('barang1_id', $barang1ID)
            ->first();

        if ($bc40Barang == null) {
            return response()->setJSON([
                'message' => "Isi dokumen barang detail terlebih dahulu sebelum mengisi pungutan & dokumen barang",
                'status' => false,
                'token' => csrf_hash()
            ]);
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

        $nilaiBayar100 = $bc40Barang['harga_ekspor'] * ($nilaiTarif / 100);
        $nilaiBayar = $nilaiBayar100 / $tarifFasilitas;


        $this->bcBarangTarifModel->insert([
            'bc_purchase_order_id' => $bcPurchaseOrderID,
            'penerimaan_barang_id' => $penerimaanBarangID,
            'barang1_id' => $barang1ID,
            'bc_type' => 40,
            'kode_jenis_pungutan' => decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')),
            'kode_jenis_tarif' => decrypt($this->request->getVar('barang_detail_kode_jenis_tarif')),
            'tarif_bea_masuk' => convertRupiahToNumber($this->request->getVar('barang_detail_nilai_tarif')),
            'kode_fasilitas_tarif' => decrypt($this->request->getVar('barang_detail_kode_fasilitas_tarif')),
            'tarif_fasilitas' => convertRupiahToNumber($this->request->getVar('barang_detail_tarif_fasilitas')),
            'seri_barang' => $bc40Barang['seri_barang'],
            'kode_satuan_barang' => $bc40Barang['kode_satuan_barang'],
            'nilai_bayar' => $nilaiBayar
        ]);

        return response()->setJSON([
            'message' => "Pungutan berhasil ditambahkan",
            'status' => true,
            'token' => csrf_hash()
        ]);
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

    public function createPungutanView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

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

        foreach ($kodeJenisPungutan as $k) {
            if ($k['value'] == "PPN") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'tidak_dipungut' => $ppnTidakDipungut,
                    'dibebaskan' => $ppnDibebaskan,
                    'ditangguhkan' => $ppnDitangguhkan

                ];
            }
        }

        $data = [
            'bcPo' => $bcPo,
            'pungutanList' => $pungutanList,
            'barangTarif' => $barangTarif
        ];

        return view('BeaCukai/bc-40/form-pungutan', $data);
    }

    public function createPernyataanView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc40 = $this->bc40Model->get($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'bcPo' => $bcPo,
            'bc40' => $bc40
        ];

        return view('BeaCukai/bc-40/form-pernyataan', $data);
    }

    public function createPernyataanAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $lastData = $this->bc40Model->get($bcPurchaseOrderID);

        if ($lastData == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $this->bc40Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_no_lokal' => $this->bc40Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),

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

        $bc40 = $this->bc40Model
            ->where('no_aju', $noAju)
            ->whereNotIn('bc_purchase_order_id', [$bcPurchaseOrderID])
            ->first();

        if ($bc40 != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Nomor aju sudah ada",
                'status' => false,
            ]);
        }

        if ($bc40First == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            $this->bc40Model->insert([
                'no_aju' => $noAju,
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_no_lokal' => $this->bc40Model->getNo(date('m'), date('Y'), $last_day),
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
        $status = $this->insertInventori($bcPurchaseOrderID);

        if (!$status) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Terjadi kesalahan saat menambah stok inventori"
            ]);
        }

        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);

        $bc40Data = $this->bc40Model->get($bcPurchaseOrderID);

        // if ($bc40Data == null) {
        //     return redirect()->to('bea-cukai-bc-40');
        // }

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

        // return response()->setJSON($payload);
        $res = $beacukaiApi->kirimDokumenBC($payload, false);

        // UPDATE STATUS
        $this->bc40Model->set('status_dokumen', "Sudah Kirim")->where('bc_purchase_order_id', $bcPurchaseOrderID)->update();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 4.O Berhasil Dikirim Ke Ceisa",
            'res' => $res,
        ]);
    }

    // POSTING BEA CUKAI PO
    public function posting()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bc40 = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();

        if ($bc40 == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            // insert
            $this->bc40Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_no_lokal' => $this->bc40Model->getNo(date('m'), date('Y'), $last_day),
                'no_aju' => $this->generateNomorAju(),
            ]);
        }

        $status = $this->insertInventori($bcPurchaseOrderID);

        if (!$status) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Terjadi kesalahan saat menambah stok inventori"
            ]);
        }

        $this->bcPurchaseOrderModel->update($bcPurchaseOrderID, [
            'status_posting' => '1'
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

    public function generateNomorAju()
    {
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $kodeDokumenbc40Static = $this->metaDataModel->where('name', "Kode BC40 Static")->first();

        $kodeKantorStatic = $ceisaSetting['kode_kantor_pabean'];
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = "";

        $bc40Last = $this->bc40Model->orderBy('createdAt', "DESC")->limit(1)->first();

        if ($bc40Last == null) {
            $sequenceNoUrutPengajuan = "000001";
        } else {
            if ($bc40Last['no_aju'] == null) {
                $sequenceNoUrutPengajuan = "000001";
            } else {
                // Buatkan auto increment
                $arrNo = explode('-', $bc40Last['no_aju']);
                $lastNomor = $arrNo[3];
                // lakukan increment
                $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
                $sequenceNoUrutPengajuan = $nextNomor;
            }
        }

        return $kodeDokumenbc40Static['value'] . '-' . $kodeKantorStatic . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan;
    }

    public function dropdownSupplier()
    {
        $poType = $this->request->getVar('po_type');

        if (!empty($poType)) {
            $poType = str_replace('LOKAL', 'BAHAN', $poType);
            $supplier = $this->supplierModel->where('deletedAt', null)->where('type', $poType)->orderBy('name', "ASC")->findAll();

            return response()->setJSON([
                'data' => $supplier,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }

    public function dropdownPO()
    {
        $supplierId = $this->request->getVar('supplier_id');
        $poType = $this->request->getVar('po_type');

        if (empty($supplierId) || empty($poType)) {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => true
            ]);
        }

        $result = $this->getListDataPurchaseOrderExport($poType, $supplierId);

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

        $result = $this->getListDataPurchaseOrderExport($poType, $supplierId);

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
        $result = $this->getListDataPurchaseOrderExport($poType, $supplierId);

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

    public function getListDataPurchaseOrderExport($poType, $supplierId, $isAll = false)
    {
        // GET PO YANG SUDAH DIGUNAKAN & POSTING
        $lpbUsed = $this->bcPurchaseOrderModel->where('supplier_id', $supplierId)->where('po_type', $poType)->where('deletedAt', null)->findAll();
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
            $po = $this->penerimaanBarangModel
                ->select('
                    penerimaan_barang.id,
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang,
                    penerimaan_barang_detail.purchase_order_id,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                    penerimaan_barang_detail.barang_id,
                    rm_purchase_orders.po_no,
                    rm_purchase_orders.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang
                ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.supplier_id', $supplierId)
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->orderBy('penerimaan_barang.createdAt', "DESC")
                ->groupBy('barang_id')
                ->groupBy('id')
                ->findAll();
        } else if ($poType == "LOKAL PENOLONG") {
            // PO LOKAL BAHAN PENOLONG
            $po = $this->penerimaanBarangModel
                ->select('
                penerimaan_barang.id,
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.purchase_order_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                barang_master.barang_name,
                barang_master.kode_barang
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "LOKAL")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.supplier_id', $supplierId)
                ->where('penerimaan_barang.bc_type', '53')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->orderBy('penerimaan_barang.createdAt', "DESC")
                ->groupBy('barang_id')
                ->groupBy('id')
                ->findAll();
        } elseif ($poType == "IMPORT BAKU") {
            // PO IMPORT BAHAN BAKU
            $po = $this->penerimaanBarangModel
                ->select('
                    penerimaan_barang.id,
                    penerimaan_barang.tanggal AS lpb_date,
                    penerimaan_barang.no_penerimaan_barang,
                    penerimaan_barang_detail.purchase_order_id,
                    SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                    SUM(penerimaan_barang_detail.qty) AS qty_po,
                    SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                    penerimaan_barang_detail.barang_id,
                    rm_import_pos.po_no,
                    rm_import_pos.po_date,
                    barang_master.barang_name,
                    barang_master.kode_barang
                ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('rm_import_pos', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "BAKU")
                ->where('penerimaan_barang.supplier_id', $supplierId)
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->orderBy('penerimaan_barang.createdAt', "DESC")
                ->groupBy('barang_id')
                ->groupBy('id')
                ->findAll();
        } elseif ($poType == "IMPORT PENOLONG") {
            // PO IMPORT BAHAN PENOLONG
            $po = $this->penerimaanBarangModel
                ->select('
                penerimaan_barang.id,
                penerimaan_barang.tanggal AS lpb_date,
                penerimaan_barang.no_penerimaan_barang,
                penerimaan_barang_detail.purchase_order_id,
                SUM(penerimaan_barang_detail.jml_masuk) AS qty_lpb,
                SUM(penerimaan_barang_detail.qty) AS qty_po,
                SUM(penerimaan_barang_detail.sub_total) AS sub_total,
                penerimaan_barang_detail.barang_id,
                am_purchase_orders.po_no,
                am_purchase_orders.po_date,
                barang_master.barang_name,
                barang_master.kode_barang
            ')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                ->where('penerimaan_barang.status_penerimaan', "IMPORT")
                ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
                ->where('penerimaan_barang.supplier_id', $supplierId)
                ->where('penerimaan_barang.bc_type', '48')
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->orderBy('penerimaan_barang.createdAt', "DESC")
                ->groupBy('barang_id')
                ->groupBy('id')
                ->findAll();
        }

        foreach ($po as $p) {
            if ($isAll) {
                $result[] = [
                    'penerimaan_barang_id' => $p['id'],
                    'lpb_date' => date('d/m/Y', strtotime($p['lpb_date'])),
                    'lpb_no' => $p['no_penerimaan_barang'],
                    'purchase_order_id' => $p['purchase_order_id'],
                    'qty_lpb' => $p['qty_lpb'],
                    'qty_po' => $p['qty_po'],
                    'barang_id' => $p['barang_id'],
                    'po_no' => $p['po_no'],
                    'po_date' => date('d/m/Y', strtotime($p['po_date'])),
                    'barang_name' => $p['barang_name'],
                    'kode_barang' => $p['kode_barang'],
                    'harga' => number_format($p['sub_total'], 2),
                    'harga_number' => $p['sub_total']
                ];
            } else {
                if (!in_array($p['id'], $lpbUsedArr)) {
                    $result[] = [
                        'penerimaan_barang_id' => $p['id'],
                        'lpb_date' => date('d/m/Y', strtotime($p['lpb_date'])),
                        'lpb_no' => $p['no_penerimaan_barang'],
                        'purchase_order_id' => $p['purchase_order_id'],
                        'qty_lpb' => $p['qty_lpb'],
                        'qty_po' => $p['qty_po'],
                        'barang_id' => $p['barang_id'],
                        'po_no' => $p['po_no'],
                        'po_date' => date('d/m/Y', strtotime($p['po_date'])),
                        'barang_name' => $p['barang_name'],
                        'kode_barang' => $p['kode_barang'],
                        'harga' => number_format($p['sub_total'], 2),
                        'harga_number' => $p['sub_total']
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
            $bc40 = $this->bc40Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();

            $poIdArr = json_decode($bcPo['multiple_po_id']);
            $lpbIdArr = json_decode($bcPo['multiple_lpb_id']);
            $typeBahan = $bcPo['po_type'] == "LOKAL BAKU" ? "bahan_baku" : "bahan_penolong";

            foreach ($lpbIdArr as $lpbId) {

                $penerimaanBarang = $this->penerimaanBarangModel->where('id', $lpbId)->first();
                $penerimaanBarangList = $this->penerimaanBarangDetailModel
                    ->where('penerimaan_barang_id', $lpbId)
                    ->whereIn('purchase_order_id', $poIdArr)
                    ->where('deletedAt', null)
                    ->findAll();

                // CHECK STOK APAKAH SUDAH DIINISASI (INISIASI HEADER BARANG)
                foreach ($penerimaanBarangList as $p) {

                    // CHECK STOK BARANG HEADER
                    $stok = $this->stockModel->getStokMaster(
                        $this->this_company_id,
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        $typeBahan,
                        $p['barang_id'],
                        $p['spesifikasi_id'],
                    );

                    if ($stok == null) {
                        $stok = $this->stockModel->insertStok(
                            $this->this_company_id,
                            $penerimaanBarang['warehouse_id'],
                            $penerimaanBarang['divisi_id'],
                            $typeBahan,
                            $p['barang_id'],
                            $p['spesifikasi_id'],
                            0
                        );
                    }
                }

                // CHECK STOK KEMASAN HEADER (INISIASI HEADER KEMASAN)
                $stok = $this->stockModel->getStokMaster(
                    $this->this_company_id,
                    $penerimaanBarang['warehouse_id'],
                    $penerimaanBarang['divisi_id'],
                    "kemasan",
                    0,
                    $penerimaanBarang['kemasan_id'],
                );

                if ($stok == null) {
                    $stok = $this->stockModel->insertStok(
                        $this->this_company_id,
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        "kemasan",
                        0,
                        $penerimaanBarang['kemasan_id'],
                        0
                    );
                }

                // STOK BARANG DIINPUT (INSERT BARANG)
                foreach ($penerimaanBarangList as $p) {
                    // HEADER
                    $stok = $this->stockModel->insertStok(
                        $this->this_company_id,
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        $typeBahan,
                        $p['barang_id'],
                        $p['spesifikasi_id'],
                        $p['jml_masuk']
                    );

                    // DETAIL
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        $p['jml_masuk'],
                        'In',
                        date('Y-m-d'),
                        $this->this_user_id,
                        "LPB",
                        $penerimaanBarang['no_penerimaan_barang'],
                        "-",
                    );

                    // GET PURCHASE ORDER
                    if ($typeBahan == "bahan_penolong") {
                        // PO BAHAN PENOLONG
                        $po = $this->amPurchaseOrderModel->find($p['purchase_order_id']);
                    } else {
                        // PO BAHAN BAKU
                        $po = $this->rmPurchaseOrderModel->find($p['purchase_order_id']);
                    }
                    // SUB DETAIL
                    $this->stockDetail2Model->insertStokDetail2(
                        $penerimaanBarang['bc_type'],
                        $stok,
                        $stokDetail,
                        $p['jml_masuk'],
                        $bc40['no_aju'],
                        $po['po_no'],
                        $po['po_no']
                    );
                }

                // INSERT KEMASAN
                $stok = $this->stockModel->insertStok(
                    $this->this_company_id,
                    $penerimaanBarang['warehouse_id'],
                    $penerimaanBarang['divisi_id'],
                    "kemasan",
                    0,
                    $penerimaanBarang['kemasan_id'],
                    $penerimaanBarang['jumlah_kemasan']
                );

                // DETAIL
                $stokDetail = $this->stockDetailModel->insertStokDetail(
                    $stok,
                    $penerimaanBarang['jumlah_kemasan'],
                    "In",
                    date('Y-m-d'),
                    $this->this_user_id,
                    "LPB",
                    $penerimaanBarang['no_penerimaan_barang'],
                    "-",
                );

                // SUB DETAIL
                $this->stockDetail2Model->insertStokDetail2(
                    $penerimaanBarang['bc_type'],
                    $stok,
                    $stokDetail,
                    $penerimaanBarang['jumlah_kemasan'],
                    $bc40['no_aju'],
                    $penerimaanBarang['no_penerimaan_barang'],
                    $penerimaanBarang['no_penerimaan_barang'],
                );
            }

            return true;
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
    }
}
