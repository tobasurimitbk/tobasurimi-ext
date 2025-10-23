<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Controllers\Warehouse\PenerimaanBarangLokalBP;
use App\Helpers\BeaCukaiApi;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BCBarangDokumenModel;
use App\Models\BCBarangModel;
use App\Models\BCBarangTarifModel;
use App\Models\BCDokumenModel;
use App\Models\BCEntitasModel;
use App\Models\BCKemasanModel;
use App\Models\BCKontainerModel;
use App\Models\BC23Model;
use App\Models\BCPengangkutModel;
use App\Models\BCPurchaseOrderLPBModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\CeisaSettingModel;
use App\Models\CountryModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\NomorIjinTPBModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengusahaTPBModel;
use App\Models\RMImportPOModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierHargaModel;
use App\Models\SupplierModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Exception;


// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54
class BC23 extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $ceisaSettingModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $bcPurchaseOrderModel;
    protected $bcBarangModel;
    protected $bc23Model;
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
    protected $countryModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $amPurchaseOrderModel;
    protected $rmImportPoModel;
    protected $akunCeisa;
    protected $dompdf;
    protected $bc40Controller;
    protected $divisiModel;
    protected $penerimaanBarangLokalBp;
    protected $bcPurchaseOrderLPBModel;

    public function __construct()
    {
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $this->bcBarangModel = new BCBarangModel();
        $this->bc23Model = new BC23Model();
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
        $this->countryModel = new CountryModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->rmImportPoModel = new RMImportPOModel();
        $this->dompdf = new Dompdf();
        $this->bc40Controller = new BC40();
        $this->divisiModel = new DivisisModel();
        $this->penerimaanBarangLokalBp = new PenerimaanBarangLokalBP();
        $this->bcPurchaseOrderLPBModel = new BCPurchaseOrderLPBModel();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first()
        ];

        return view('BeaCukai/bc-23/index', $data);
    }

    public function online()
    {
        $data = [
            'baseUrl' => $this->metaDataModel->where('name', "Base Url BC")->first()['value']
        ];

        return view('BeaCukai/bc-23/online', $data);
    }

    public function allOnline()
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);
        $dataOnline = $beacukaiApi->getListStatusResponseAll();

        $newDataResult = [];
        foreach ($dataOnline->dataRespon as $d) {
            if ($d->kodeDokumen == "23") {
                $newDataResult[] = $d;
            }
        }
        $dataOnline->dataRespon = $newDataResult;

        if ($dataOnline->status == false) {
            return response()->setJSON($dataOnline);
        } else {
            return response()->setJSON([
                'data' => $dataOnline,
                'status' => true
            ]);
        }
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
            "type"          => "BC 2.3"
        ];

        $condition = [
            "bc_purchase_order.company_id"  => $this->this_company_id,
            "bc_purchase_order.deletedAt" => null,
            "bc_23.deletedAt" => null,
        ];
        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "searchData" => $this->request->getGet('searchData'),
            "statusPosting" => $this->request->getGet('statusPosting'),
            "statusLPB" => $this->request->getGet("statusLPB"),
            "mulaiTanggalBC23" =>  $this->request->getVar("mulaiTanggalBC23") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("mulaiTanggalBC23")))) : "",
            "selesaiTanggalBC23" => $this->request->getVar("selesaiTanggalBC23") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("selesaiTanggalBC23")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc23Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $bc23 = $this->bc23Model->where('bc_purchase_order_id', $data->bc_purchase_order_id)->first();
            $totalBarang = $this->bcPurchaseOrderModel->findDetailBarang($data->bc_purchase_order_id);

            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->bc_purchase_order_id),
                "tanggal_bc_23"         => $data->tanggal_dokumen == null ? '-' : date('d/m/Y', strtotime($data->tanggal_dokumen)),
                "no_aju"                => ($data->no_aju == "" ? "-" : $data->no_aju) . " / " . ($data->no_daftar == "" ? "-" : $data->no_daftar),
                "po_type"               => $data->po_type,
                "lpb_no"                => str_replace(['"', ']', '['], " ",  $data->multiple_lpb_no),
                "po_no"                 => str_replace(['"', ']', '['], " ",  $data->multiple_po_no),
                "supplier_name"         => strtoupper($data->supplier_name),
                "status"                => strtoupper($bc23 == null ? "BELUM DIBUAT" : $data->status_dokumen),
                "total_barang"          => count($totalBarang),
                "status_posting"        => $data->status_posting,
                "is_update_no_aju"      => $bc23 == null ? false : ($data->status_posting === "1" ? false : true),
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
        return view('BeaCukai/bc-23/form');
    }

    public function updatePurchaseOrderView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);
        $bc40 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $bcPo = $this->bcPurchaseOrderModel
            ->select('bc_purchase_order.*, suppliers.name AS supplier_name')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->where('bc_purchase_order.id', $bcPurchaseOrderID)
            ->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPo['createdAt']));
        $noAju = $this->generateNomorAju($tanggalDokumen);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        if ($bc40 != null) {
            if ($bc40['no_aju'] != null) {
                $noAju = $bc40['no_aju'];
            }
        }

        $data = [
            'noAju' => $noAju,
            'bcPo' => $bcPo,
            'daftarPoUsed' => $this->bcPurchaseOrderModel->findDetailBarangWithSpek($bcPurchaseOrderID),
        ];

        return view('BeaCukai/bc-23/form-po-list', $data);
    }


    public function detailBarang($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);
        $bc23 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $bcPo = $this->bcPurchaseOrderModel
            ->select('bc_purchase_order.*, suppliers.name AS supplier_name')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->where('bc_purchase_order.id', $bcPurchaseOrderID)
            ->first();
        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-23');
        }
        if ($bc23 != null) {
            if ($bc23['no_aju'] != null) {
                $noAju = $bc23['no_aju'];
            }
        } else {
            $noAju = "";
        }
        $data = [
            'daftarPoUsed' => $this->bcPurchaseOrderModel->findDetailBarang($bcPurchaseOrderID),
            'bcPo' => $bcPo,
            'noAju' => $noAju
        ];
        return view('BeaCukai/bc-23/detail-barang', $data);
    }

    public function createHeaderView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc23 = $this->bc23Model->get($bcPurchaseOrderID);
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-40');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'bc23' => $bc23,
            'noAju' => $bc23 == null ? $this->generateNomorAju($tanggalDokumen) : $bc23['no_aju'],
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'kodeTujuanTpb' => $this->metaDataModel->where('name', "Jenis TPB")->findAll(),
            'selectedKantor' => $this->metaDataModel->where('name', "Kode Kantor Pabean Pengawas Static")->first(),
            'bcPo' => $bcPo
        ];

        return view('BeaCukai/bc-23/form-header', $data);
    }

    public function createHeaderAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));

        $lastData = $this->bc23Model->get($bcPurchaseOrderID);
        if ($lastData == null) {
            // insert
            $this->bc23Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju($tanggalDokumen),
                'kode_pelabuhan_bongkar' => $this->request->getVar('header_pelabuhan_bongkar'),
                'kode_kantor_bongkar' => decrypt($this->request->getVar('header_kantor_pabean_bongkar')),
                'kode_kantor' => decrypt($this->request->getVar('header_kantor_pabean_pengawas')),
                'kode_tujuan_tpb' => decrypt($this->request->getVar('header_kode_tujuan_tpb')),
            ]);
        } else {
            // update
            $this->bc23Model->update($lastData['id'], [
                'kode_pelabuhan_bongkar' => $this->request->getVar('header_pelabuhan_bongkar'),
                'kode_kantor_bongkar' => decrypt($this->request->getVar('header_kantor_pabean_bongkar')),
                'kode_kantor' => decrypt($this->request->getVar('header_kantor_pabean_pengawas')),
                'kode_tujuan_tpb' => decrypt($this->request->getVar('header_kode_tujuan_tpb')),
            ]);

            if ($lastData['no_aju'] == null) {
                $this->bc23Model->update($lastData['id'], [
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
        $bc23Entitas = $this->bcEntitasModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->first();
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->findAll();
        $bc23 = $this->bc23Model->get($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        if ($bc23 == null) {
            return \redirect()->to('bea-cukai-bc-23/id/header/' . \encrypt($bcPurchaseOrderID));
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'bc23Entitas' => $bc23Entitas,
            'kodeNegaraAsal' => $this->countryModel->findAll(),
            'pengusahaTPB' => $pengusahaTPB,
            'bcPo' => $bcPo,
            'bc23' => $bc23
        ];

        return view('BeaCukai/bc-23/form-entitas', $data);
    }

    public function createEntitasAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $lastData = $this->bcEntitasModel->get($bcPurchaseOrderID);

        if ($lastData == null) {
            // insert
            $this->bcEntitasModel->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_type' => 23,
                'alamat_entitas' => $this->request->getVar('entitas_alamat_importir'),
                'nama_entitas' => $this->request->getVar('entitas_nama_importir'),
                'nib_entitas' => $this->request->getVar('entitas_nib'),
                'nitku_entitas' => $this->request->getVar('pengusaha_tpb_nitku'),
                'nomor_identitas' => $this->request->getVar('entitas_npwp_importir'),
                'nomor_ijin_entitas' => $this->request->getVar('entitas_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('entitas_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('entitas_nama_pemasok'),
                'alamat_pemasok' => $this->request->getVar('entitas_alamat_pemasok'),
                'kode_negara_pemasok' => decrypt($this->request->getVar('entitas_negara')),
                'npwp_pemilik_barang' => $this->request->getVar('entitas_npwp_pemilik_barang'),
                'nama_pemilik_barang' => $this->request->getVar('entitas_nama_pemilik_barang'),
                'alamat_pemilik_barang' => $this->request->getVar('entitas_alamat_pemilik_barang'),
                'nitku_pemilik_barang' => $this->request->getVar('pemilik_barang_nitku'),
            ]);
        } else {
            // update
            $this->bcEntitasModel->update($lastData['id'], [
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'bc_type' => 23,
                'alamat_entitas' => $this->request->getVar('entitas_alamat_importir'),
                'nama_entitas' => $this->request->getVar('entitas_nama_importir'),
                'nib_entitas' => $this->request->getVar('entitas_nib'),
                'nitku_entitas' => $this->request->getVar('pengusaha_tpb_nitku'),
                'nomor_identitas' => $this->request->getVar('entitas_npwp_importir'),
                'nomor_ijin_entitas' => $this->request->getVar('entitas_nomor_ijin_tpb'),
                'tanggal_ijin_entitas' =>  $this->request->getVar('entitas_tanggal_skep_tpb') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb')), "Y-m-d") : "",
                'nama_pemasok' => $this->request->getVar('entitas_nama_pemasok'),
                'alamat_pemasok' => $this->request->getVar('entitas_alamat_pemasok'),
                'kode_negara_pemasok' => decrypt($this->request->getVar('entitas_negara')),
                'npwp_pemilik_barang' => $this->request->getVar('entitas_npwp_pemilik_barang'),
                'nama_pemilik_barang' => $this->request->getVar('entitas_nama_pemilik_barang'),
                'alamat_pemilik_barang' => $this->request->getVar('entitas_alamat_pemilik_barang'),
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
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'kodeDokumen' => $this->metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'bcPo' => $bcPo
        ];

        return view('BeaCukai/bc-23/form-dokumen', $data);
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
            "bc_dokumen.bc_type" => 23
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
        try {
            $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

            $lastData = $this->bcDokumenModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->orderBy('createdAt', "DESC")
                ->first();

            $seriDokumen = $lastData == null ? 1 : $lastData['seri_dokumen'] + 1;
            $kodeDokumen =  decrypt($this->request->getVar('dokumen_jenis_dokumen'));

            if ($seriDokumen == 1) {
                // HARUS INVOICE 
                if ($kodeDokumen != 705 && $kodeDokumen != 740) {
                    return response()->setJSON([
                        'status' => false,
                        'token' => csrf_hash(),
                        'message' => "Dokumen seri pertama wajib BL / AWB",
                    ]);
                }
            } elseif ($seriDokumen == 2) {
                // HARUS BL/AWB
                if ($kodeDokumen != 380) {
                    return response()->setJSON([
                        'status' => false,
                        'token' => csrf_hash(),
                        'message' => " Dokumen seri kedua wajib invoice",
                    ]);
                }
            }

            $this->bcDokumenModel->insert([
                'bc_type' => 23,
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
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
            ]);
        }
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

    public function getDetailDokumen()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $data = $this->bcDokumenModel->where('id', $id)->first();
            $dokumen = [
                'tanggal_dokumen' => date('d/m/Y', strtotime($data['tanggal_dokumen'])),
                'nomor_dokumen' => $data['nomor_dokumen'],
                'kode_dokumen' => \encrypt($data['kode_dokumen'])
            ];

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => $dokumen
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateDokumenAction()
    {
        try {
            $id = decrypt($this->request->getVar('bc_dokumen_id'));

            $this->bcDokumenModel->update($id, [
                'bc_type' => 23,
                'nomor_dokumen' => $this->request->getVar('dokumen_nomor_dokumen'),
                'kode_dokumen' => decrypt($this->request->getVar('dokumen_jenis_dokumen')),
                'tanggal_dokumen' => $this->request->getVar('dokumen_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('dokumen_tanggal')), "Y-m-d") : "",
            ]);

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Dokumen berhasil diupdate",
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function createPengangkutView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc23DokumenBL = $this->bcDokumenModel
            ->whereIn('kode_dokumen', [705, 740]) // (702 = BL, 740 = AWB)
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->first();

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        $bc23 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $kodeKantorBongkar = $this->kantorBeaCukaiModel->where('kode', $bc23['kode_kantor_bongkar'])->first();

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'kodePengangkutan' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'kodeBendera' => $this->countryModel->findAll(),
            'bc23' => $this->bc23Model->get($bcPurchaseOrderID),
            'bc23Pengangkut' => $this->bcPengangkutModel->get($bcPurchaseOrderID),
            'bc23DokumenBL' => $bc23DokumenBL,
            'bcPo' => $bcPo,
            'kodeKantorBongkar' => $kodeKantorBongkar
        ];

        return view('BeaCukai/bc-23/form-pengangkut', $data);
    }

    public function createPengangkutAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));
        $lastDataBC23 = $this->bc23Model->get($bcPurchaseOrderID);
        $lastPengangkutBC23 = $this->bcPengangkutModel->get($bcPurchaseOrderID);

        if ($lastDataBC23 == null) {
            // insert
            $this->bc23Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju($tanggalDokumen),
                'no_bc_11' => $this->request->getVar('bc_11_no_bc_11'),
                'tanggal_bc_11' => $this->request->getVar('bc_11_tanggal_bc_11') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('bc_11_tanggal_bc_11')), "Y-m-d") : "",
                'pos_bc_11' => $this->request->getVar('bc_11_pos_bc_11'),
                'sub_pos_bc_11' => $this->request->getVar('bc_11_sub_pos_bc_11'),
                'sub_pos_pos_bc_11' => $this->request->getVar('bc_11_sub_sub_pos_bc_11'),
                'kode_pelabuhan_muat' => $this->request->getVar('pengangkutan_pelabuhan_muat'),
                'kode_pelabuhan_transit' => $this->request->getVar('pengangkutan_pelabuhan_transit'),
                'kode_tps' => $this->request->getVar('pengangkutan_tempat_penimbunan')
            ]);
        } else {
            // update
            $this->bc23Model->update($lastDataBC23['id'], [
                'no_bc_11' => $this->request->getVar('bc_11_no_bc_11'),
                'tanggal_bc_11' => $this->request->getVar('bc_11_tanggal_bc_11') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('bc_11_tanggal_bc_11')), "Y-m-d") : "",
                'pos_bc_11' => $this->request->getVar('bc_11_pos_bc_11'),
                'sub_pos_bc_11' => $this->request->getVar('bc_11_sub_pos_bc_11'),
                'sub_pos_pos_bc_11' => $this->request->getVar('bc_11_sub_sub_pos_bc_11'),
                'kode_pelabuhan_muat' => $this->request->getVar('pengangkutan_pelabuhan_muat'),
                'kode_pelabuhan_transit' => $this->request->getVar('pengangkutan_pelabuhan_transit'),
                'kode_tps' => $this->request->getVar('pengangkutan_tempat_penimbunan')
            ]);
        }

        if ($lastPengangkutBC23 == null) {
            $this->bcPengangkutModel->insert([
                'bc_type' => 23,
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'kode_cara_angkut' => decrypt($this->request->getVar('pengangkutan_cara_pengangkutan')),
                'nama_sarana_pengangkut' => $this->request->getVar('pengangkutan_nama_sarana_pengangkut'),
                'nomor_pengangkut' => $this->request->getVar('pengangkutan_nomor_pengangkut'),
                'kode_bendera' => decrypt($this->request->getVar('pengangkutan_kode_bendera')),
            ]);
        } else {
            $this->bcPengangkutModel->update($lastPengangkutBC23['id'], [
                'bc_type' => 23,
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'kode_cara_angkut' => decrypt($this->request->getVar('pengangkutan_cara_pengangkutan')),
                'nama_sarana_pengangkut' => $this->request->getVar('pengangkutan_nama_sarana_pengangkut'),
                'nomor_pengangkut' => $this->request->getVar('pengangkutan_nomor_pengangkut'),
                'kode_bendera' => decrypt($this->request->getVar('pengangkutan_kode_bendera')),
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pengangkut berhasil diupdate",
        ]);
    }

    public function createKemasanPetiKemasView($bcPurchaseOrderID)
    {

        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $kemasanLast = $this->bcKemasanModel->getLast($bcPurchaseOrderID);
        $kontainerLast = $this->bcKontainerModel->getLast($bcPurchaseOrderID);

        $bc23DokumenBL = $this->bcDokumenModel
            ->whereIn('kode_dokumen', [705, 740]) // (702 = BL, 740 = AWB)
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->first();

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $this->metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'seriKemasan' => $kemasanLast == null ? 1 : $kemasanLast['seri_kemasan'] + 1,
            'seriKontainer' => $kontainerLast == null ? 1 : $kontainerLast['seri_kontainer'] + 1,
            'dropdownKemasan' => $this->bcPurchaseOrderModel->dropdownKemasan($bcPurchaseOrderID),
            'bc23DokumenBL' => $bc23DokumenBL,
            'bcPo' => $bcPo
        ];

        return view('BeaCukai/bc-23/form-kemasan-peti-kemas', $data);
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

        $bc23Kemasan = $this->bcKemasanModel->getList($condition, $limit, $offset);
        $resKemasan = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc23Kemasan['data'] as $data) {
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
            "recordsTotal"      => $bc23Kemasan['totalData'],
            "recordsFiltered"   => $bc23Kemasan['totalFilteredData'],
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

        $bc23Kontainer = $this->bcKontainerModel->getList($condition, $limit, $offset);
        $resKontainer = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc23Kontainer['data'] as $data) {
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
            "recordsTotal"      => $bc23Kontainer['totalData'],
            "recordsFiltered"   => $bc23Kontainer['totalFilteredData'],
            "data"              => $resKontainer,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createKemasanAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $this->bcKemasanModel->insert([
            'bc_type' => 23,
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
            'bc_type' => 23,
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

    public function getBLKontainerPetiKemas()
    {
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);

        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $bc23DokumenBL = $this->bcDokumenModel
            ->whereIn('kode_dokumen', [705, 740]) // (702 = BL, 740 = AWB)
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->first();

        $bc23 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $namaImportirDefault = $this->metaDataModel->where('name', "Nama Importir Default BC")->first();

        if ($bc23DokumenBL == null) {
            return response()->setJSON([
                'message' => "Dokumen B/L atau AWB tidak ada",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if ($bc23 == null) {
            return response()->setJSON([
                'message' => "Isi Tab Header terlebih dahulu",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if ($bc23['kode_kantor_bongkar'] == null || $bc23['kode_kantor_bongkar'] == '') {
            return response()->setJSON([
                'message' => "Kode kantor bongkar di Tab Header wajib diisi",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $res = $beacukaiApi->getManifest(
            $bc23DokumenBL['nomor_dokumen'],
            date('d-m-Y', strtotime($bc23DokumenBL['tanggal_dokumen'])),
            $bc23['kode_kantor_bongkar'],
            $namaImportirDefault['value']
        );

        if ($res['status'] == false) {
            return response()->setJSON([
                'message' => $res['message'],
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $listKontainer = $res['data']->listContainer;

        if ($res['data']->respon != "") {
            return response()->setJSON([
                'message' => $res['data']->respon,
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if (\count($listKontainer) == 0) {
            return response()->setJSON([
                'message' => "List Kontainer tidak ditemukan",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        foreach ($listKontainer as $l) {
            $kontainerFirst = $this->bcKontainerModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('nomor_kontainer', $l->noKontainer)
                ->first();

            if ($kontainerFirst == null) {
                $kontainerLast = $this->bcKontainerModel->getLast($bcPurchaseOrderID);
                $seriKontainer =  $kontainerLast != null ? $kontainerLast['seri_kontainer'] + 1 : 1;

                $this->bcKontainerModel->insert([
                    'bc_purchase_order_id' => $bcPurchaseOrderID,
                    'nomor_kontainer' => $l->noKontainer,
                    'kode_ukuran_kontainer' => $l->ukuranKontainer,
                    'kode_jenis_kontainer' => $l->jenisKontainer,
                    'kode_tipe_kontainer' => $l->tipeKontainer,
                    'seri_kontainer' => $seriKontainer
                ]);
            }
        }

        return response()->setJSON([
            'message' => count($listKontainer) . " data kontainer berhasil di ambil dari dokumen B/L atau AWB",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function createTransaksiView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc23 = $this->bc23Model->get($bcPurchaseOrderID);

        if ($bc23 != null) {
            $bc23['ndpbm'] = formatRupiah($bc23['ndpbm']);
            $bc23['nilai_barang'] = formatRupiah($bc23['nilai_barang']);
            $bc23['cif'] = formatRupiah($bc23['cif']);
            $bc23['harga_penyerahan'] = formatRupiah($bc23['harga_penyerahan']);
            $bc23['biaya_tambahan'] = formatRupiah($bc23['biaya_tambahan']);
            $bc23['biaya_pengurang'] = formatRupiah($bc23['biaya_pengurang']);
            $bc23['fob'] = formatRupiah($bc23['fob']);
            $bc23['freight'] = formatRupiah($bc23['freight']);
            $bc23['asuransi'] = formatRupiah($bc23['asuransi']);
            $bc23['bruto'] = formatRupiah($bc23['bruto']);
            $bc23['netto'] = formatRupiah($bc23['netto']);
        }

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'kodeValuta' => $this->metaDataModel->where('name', "Valuta")->findAll(),
            'kodeIncoterm' => $this->metaDataModel->where('name', "Kode Incoterm BC")->findAll(),
            'kodeAsuransi' => $this->metaDataModel->where('name', "Kode Asuransi BC")->findAll(),
            'kodeKenaPajak' => $this->metaDataModel->where('name', "Kode Kena Pajak BC")->findAll(),
            'bc23' => $bc23,
            'bcPo' => $bcPo
        ];

        return view('BeaCukai/bc-23/form-transaksi', $data);
    }

    public function createTransaksiAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));
        $lastDataBC23 = $this->bc23Model->get($bcPurchaseOrderID);
        if ($lastDataBC23 == null) {
            // insert
            $this->bc23Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju($tanggalDokumen),
                'kode_valuta' => decrypt($this->request->getVar('harga_kode_valuta')),
                'ndpbm' => ($this->request->getVar('harga_ndpbm')),
                'kode_incoterm' => decrypt($this->request->getVar('harga_kode_harga_barang')),
                'nilai_barang' => ($this->request->getVar('harga_nilai_barang')),
                'cif' => ($this->request->getVar('harga_cif')),
                'harga_penyerahan' => ($this->request->getVar('harga_nilai_pabean')),
                'biaya_tambahan' => ($this->request->getVar('harga_lainnya_biaya_penambah')),
                'biaya_pengurang' => ($this->request->getVar('harga_lainnya_biaya_pengurang')),
                'fob' => ($this->request->getVar('harga_lainnya_free_on_board')),
                'freight' => ($this->request->getVar('harga_lainnya_freight')),
                'kode_asuransi' => decrypt($this->request->getVar('harga_lainnya_kode_asuransi')),
                'asuransi' => ($this->request->getVar('harga_lainnya_nilai_asuransi')),
                'bruto' => ($this->request->getVar('berat_bruto')),
                'netto' => ($this->request->getVar('berat_netto')),
                'kode_kena_pajak' => decrypt($this->request->getVar('pajak_jasa_kena_pajak'))
            ]);
        } else {
            $this->bc23Model->update($lastDataBC23['id'], [
                'kode_valuta' => decrypt($this->request->getVar('harga_kode_valuta')),
                'ndpbm' => ($this->request->getVar('harga_ndpbm')),
                'kode_incoterm' => decrypt($this->request->getVar('harga_kode_harga_barang')),
                'nilai_barang' => ($this->request->getVar('harga_nilai_barang')),
                'cif' => ($this->request->getVar('harga_cif')),
                'harga_penyerahan' => ($this->request->getVar('harga_nilai_pabean')),
                'biaya_tambahan' => ($this->request->getVar('harga_lainnya_biaya_penambah')),
                'biaya_pengurang' => ($this->request->getVar('harga_lainnya_biaya_pengurang')),
                'fob' => ($this->request->getVar('harga_lainnya_free_on_board')),
                'freight' => ($this->request->getVar('harga_lainnya_freight')),
                'kode_asuransi' => decrypt($this->request->getVar('harga_lainnya_kode_asuransi')),
                'asuransi' => ($this->request->getVar('harga_lainnya_nilai_asuransi')),
                'bruto' => ($this->request->getVar('berat_bruto')),
                'netto' => ($this->request->getVar('berat_netto')),
                'kode_kena_pajak' => decrypt($this->request->getVar('pajak_jasa_kena_pajak'))
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
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'bcPo' => $bcPoFirst,
            'lpbDetail' => $this->bcPurchaseOrderModel->findDetailBarang($bcPurchaseOrderID)
        ];

        return view('BeaCukai/bc-23/form-barang', $data);
    }

    public function createBarangDetailView($bcPurchaseOrderID, $penerimaanBarangID, $barang1ID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);
        $penerimaanBarangID = decrypt($penerimaanBarangID);
        $barang1ID = decrypt($barang1ID);

        $lpb = $this->penerimaanBarangModel->getById($penerimaanBarangID);
        $bcPoFirst = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc23 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $kodeSatuanBarang = null;

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        if ($lpb == null || $bcPoFirst == null || $bc23 == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        $seriBarang = $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->orderBy('createdAt', "DESC")->first();
        $bc23 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $bc23DokumenBarang = $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('penerimaan_barang_id', $penerimaanBarangID)->where('barang1_id', $barang1ID)->first();
        $barangDetail = $this->bcPurchaseOrderModel->findDetailDokumenBarang(
            $bcPurchaseOrderID,
            $penerimaanBarangID,
            $barang1ID
        );

        if ($bc23DokumenBarang != null) {
            $kodeSatuanBarang = $this->metaDataModel
                ->where('name', "Kode Satuan BC")
                ->where('value', $bc23DokumenBarang['kode_satuan_barang'])
                ->first();
        }

        $data = [
            'kodeFasilitasTarif' => $this->metaDataModel
                ->where('name', "Kode Fasilitas Tarif BC")->whereIn('description', ['TIDAK DIPUNGUT', 'DIBEBASKAN', 'DITANGGUHKAN', 'SUDAH DILUNASI'])
                ->findAll(),
            'kodeJenisTarif' => $this->metaDataModel
                ->where('name', "Kode Jenis Tarif BC")
                ->findAll(),
            'kodeJenisPungutan' => $this->metaDataModel
                ->where('name', "Kode Jenis Pungutan BC")
                ->whereIn('value', ['BM', 'PPN', 'PPH', 'PPNBM', 'BMKITE'])
                ->findAll(),
            'kodeNegaraAsal' => $this->countryModel->findAll(),
            'kodeHS' => $this->hsCodeModel->findAll(),
            'kodeKategoriBarang' => $this->metaDataModel->where('name', 'Kategori Barang BC')->orderBy('description', "ASC")->findAll(),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'barangDetail' => $barangDetail,
            'seriBarang' => $seriBarang == null ? 1 : $seriBarang['seri_barang'] + 1,
            'bc23DokumenBarang' => $bc23DokumenBarang,
            'kodeSatuanBarang' => $kodeSatuanBarang,
            'jenisNegara' => $this->metaDataModel->where('name', "Jenis Negara BC")->where('deletedAt', null)->findAll(),
            'ndpbm' => $bc23 == null ? 0 : ($bc23['ndpbm'] == null ? 0 : $bc23['ndpbm']),
            'bcPo' => $bcPoFirst,
            'bc23' => $bc23
        ];

        return view('BeaCukai/bc-23/form-detail-barang', $data);
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

        $bc23BarangTarif = $this->bcBarangTarifModel->getList($condition, $limit, $offset);
        $res = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($bc23BarangTarif['data'] as $data) {
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
            "recordsTotal"      => $bc23BarangTarif['totalData'],
            "recordsFiltered"   => $bc23BarangTarif['totalFilteredData'],
            "data"              => $res,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createPungutanAction()
    {
        try {
            $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
            $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
            $barang1ID = decrypt($this->request->getVar('barang1_id'));

            $bc23Barang = $this->bcBarangModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('barang1_id', $barang1ID)
                ->first();

            if ($bc23Barang == null) {
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

            $kodeJenisPungutanStr = decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan'));
            $nilaiTarif =  ($this->request->getVar('barang_detail_nilai_tarif'));
            $tarifFasilitas = ($this->request->getVar('barang_detail_tarif_fasilitas'));
            $tarifFasilitas = ($tarifFasilitas == 0) ? 1 : $tarifFasilitas;

            if ($kodeJenisPungutanStr == "PPN" || $kodeJenisPungutanStr == "PPH") {
                $beaMasuk = $this->bcBarangTarifModel
                    ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                    ->where('penerimaan_barang_id', $penerimaanBarangID)
                    ->where('barang1_id', $barang1ID)
                    ->where('kode_jenis_pungutan', "BM")
                    ->first();

                if ($beaMasuk == null) {
                    return response()->setJSON([
                        'message' => "Pungutan Bea Masuk wajib ada jikalau ingin input pungutan PPN / PPH",
                        'status' => false,
                        'token' => csrf_hash()
                    ]);
                }

                // $nilaiBayar100 = $bc23Barang['harga_ekspor'] * (($nilaiTarif + $beaMasuk['nilai_bayar']) / 100);
                $nilaiBayar = ($bc23Barang['harga_ekspor'] + $beaMasuk['nilai_bayar']) * ($nilaiTarif / 100);
            } else {
                $nilaiBayar = $bc23Barang['harga_ekspor'] * ($nilaiTarif / 100);
            }

            $this->bcBarangTarifModel->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'penerimaan_barang_id' => $penerimaanBarangID,
                'barang1_id' => $barang1ID,
                'bc_type' => 23,
                'kode_jenis_pungutan' => decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')),
                'kode_jenis_tarif' => decrypt($this->request->getVar('barang_detail_kode_jenis_tarif')),
                'tarif_bea_masuk' => ($this->request->getVar('barang_detail_nilai_tarif')),
                'kode_fasilitas_tarif' => decrypt($this->request->getVar('barang_detail_kode_fasilitas_tarif')),
                'tarif_fasilitas' => ($this->request->getVar('barang_detail_tarif_fasilitas')),
                'seri_barang' => $bc23Barang['seri_barang'],
                'kode_satuan_barang' => $bc23Barang['kode_satuan_barang'],
                'nilai_bayar' => ceil($nilaiBayar / 1000) * 1000,
            ]);

            return response()->setJSON([
                'message' => "Pungutan berhasil ditambahkan",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
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

    public function getDetailPungutan()
    {
        try {
            $data = $this->bcBarangTarifModel->where('id', decrypt($this->request->getVar('id')))->first();
            $pungutan = [
                'kode_jenis_pungutan' => encrypt($data['kode_jenis_pungutan']),
                'kode_jenis_tarif' => encrypt($data['kode_jenis_tarif']),
                'nilai_tarif' => $data['tarif_bea_masuk'],
                'kode_fasilitas_tarif' => encrypt($data['kode_fasilitas_tarif']),
                'tarif_fasilitas' => $data['tarif_fasilitas'],
            ];

            return response()->setJSON([
                'status' => true,
                'data' => $pungutan,
                'token' => csrf_hash(),
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
            ]);
        }
    }

    public function updatePungutanAction()
    {
        try {
            $id = decrypt($this->request->getVar('bc_barang_tarif_id'));
            $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
            $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
            $barang1ID = decrypt($this->request->getVar('barang1_id'));

            $bc23Barang = $this->bcBarangModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('barang1_id', $barang1ID)
                ->first();

            $pungutan = $this->bcBarangTarifModel
                ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                ->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('barang1_id', $barang1ID)
                ->where('kode_jenis_pungutan', decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')))
                ->where('id !=', $id)
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
            $kodeJenisPungutanStr = decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan'));

            if ($kodeJenisPungutanStr == "BM") {
                $nilaiBayar = $bc23Barang['harga_ekspor'] * ($nilaiTarif / 100);
            } else {
                $beaMasuk = $this->bcBarangTarifModel
                    ->where('bc_purchase_order_id', $bcPurchaseOrderID)
                    ->where('penerimaan_barang_id', $penerimaanBarangID)
                    ->where('barang1_id', $barang1ID)
                    ->where('kode_jenis_pungutan', "BM")
                    ->first();

                $nilaiBayar = ($bc23Barang['harga_ekspor'] + $beaMasuk['nilai_bayar']) * ($nilaiTarif / 100);
            }

            $this->bcBarangTarifModel->update($id, [
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'penerimaan_barang_id' => $penerimaanBarangID,
                'barang1_id' => $barang1ID,
                'bc_type' => 23,
                'kode_jenis_pungutan' => decrypt($this->request->getVar('barang_detail_kode_jenis_pungutan')),
                'kode_jenis_tarif' => decrypt($this->request->getVar('barang_detail_kode_jenis_tarif')),
                'tarif_bea_masuk' => ($this->request->getVar('barang_detail_nilai_tarif')),
                'kode_fasilitas_tarif' => decrypt($this->request->getVar('barang_detail_kode_fasilitas_tarif')),
                'tarif_fasilitas' => ($this->request->getVar('barang_detail_tarif_fasilitas')),
                'seri_barang' => $bc23Barang['seri_barang'],
                'kode_satuan_barang' => $bc23Barang['kode_satuan_barang'],
                'nilai_bayar' => ceil($nilaiBayar / 1000) * 1000,
            ]);

            return response()->setJSON([
                'message' => "Pungutan berhasil diupdate",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
            ]);
        }
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
            'bc_type' => 30,
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

    public function createBarangDetailAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $barang1ID = decrypt($this->request->getVar('barang1_id'));

        $bc23Barang = $this->bcBarangModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->where('penerimaan_barang_id', $penerimaanBarangID)
            ->where('barang1_id', $barang1ID)
            ->first();

        if ($bc23Barang == null) {
            $this->bcBarangModel->insert([
                'bc_purchase_order_id' => decrypt($this->request->getVar('bc_purchase_order_id')),
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'barang1_id' => decrypt($this->request->getVar('barang1_id')),
                'bc_type' => 23,
                'kode_dokumen' => 23,
                'seri_barang' => $this->request->getVar('barang_detail_seri_barang'),
                'pos_tarif' => decrypt($this->request->getVar('barang_detail_kode_hs')),
                'kode_barang' => $this->request->getVar('barang_detail_kode_barang'),
                'uraian' => $this->request->getVar('barang_detail_uraian'),
                'merk_barang' => $this->request->getVar('barang_detail_merk_barang'),
                'tipe_barang' => $this->request->getVar('barang_detail_tipe_barang'),
                'spesifikasi_lain' => $this->request->getVar('barang_detail_spesifikasi_lain'),
                'kode_kategori_barang' => decrypt($this->request->getVar('barang_detail_kategori_barang')),
                'kode_negara_asal' => decrypt($this->request->getVar('barang_detail_negara')),
                'harga_perolehan_barang' => ($this->request->getVar('barang_detail_harga')),
                'nilai_tambah' => ($this->request->getVar('barang_detail_biaya_tambahan')),
                'fob' => ($this->request->getVar('barang_detail_fob')),
                'harga_satuan_barang' => ($this->request->getVar('barang_detail_harga_satuan')),
                'freight' => ($this->request->getVar('barang_detail_freight')),
                'asuransi' => ($this->request->getVar('barang_detail_asuransi')),
                'cif_rupiah' => ($this->request->getVar('barang_detail_cif')),
                'harga_ekspor' => ($this->request->getVar('barang_detail_nilai_pabean')),
                'jumlah_satuan' => $this->request->getVar('barang_detail_jumlah_satuan'),
                'kode_satuan_barang' => decrypt($this->request->getVar('barang_detail_kode_satuan_barang')),
                'jumlah_kemasan' => $this->request->getvar('barang_detail_jumlah_kemasan'),
                'kode_jenis_kemasan' => decrypt($this->request->getVar('barang_detail_kode_jenis_kemasan')),
                'netto' => $this->request->getVar('barang_detail_berat_bersih'),
                'persentase_jenis_negara' => $this->request->getVar('persentase_jenis_negara')
            ]);
        } else {
            $this->bcBarangModel->update($bc23Barang['id'], [
                'bc_purchase_order_id' => decrypt($this->request->getVar('bc_purchase_order_id')),
                'penerimaan_barang_id' => decrypt($this->request->getVar('penerimaan_barang_id')),
                'barang1_id' => decrypt($this->request->getVar('barang1_id')),
                'bc_type' => 23,
                'kode_dokumen' => 23,
                'seri_barang' => $this->request->getVar('barang_detail_seri_barang'),
                'pos_tarif' => decrypt($this->request->getVar('barang_detail_kode_hs')),
                'kode_barang' => $this->request->getVar('barang_detail_kode_barang'),
                'uraian' => $this->request->getVar('barang_detail_uraian'),
                'merk_barang' => $this->request->getVar('barang_detail_merk_barang'),
                'tipe_barang' => $this->request->getVar('barang_detail_tipe_barang'),
                'spesifikasi_lain' => $this->request->getVar('barang_detail_spesifikasi_lain'),
                'kode_kategori_barang' => decrypt($this->request->getVar('barang_detail_kategori_barang')),
                'kode_negara_asal' => decrypt($this->request->getVar('barang_detail_negara')),
                'harga_perolehan_barang' => ($this->request->getVar('barang_detail_harga')),
                'nilai_tambah' => ($this->request->getVar('barang_detail_biaya_tambahan')),
                'fob' => ($this->request->getVar('barang_detail_fob')),
                'harga_satuan_barang' => ($this->request->getVar('barang_detail_harga_satuan')),
                'freight' => ($this->request->getVar('barang_detail_freight')),
                'asuransi' => ($this->request->getVar('barang_detail_asuransi')),
                'cif_rupiah' => ($this->request->getVar('barang_detail_cif')),
                'harga_ekspor' => ($this->request->getVar('barang_detail_nilai_pabean')),
                'jumlah_satuan' => $this->request->getVar('barang_detail_jumlah_satuan'),
                'kode_satuan_barang' => decrypt($this->request->getVar('barang_detail_kode_satuan_barang')),
                'jumlah_kemasan' => $this->request->getvar('barang_detail_jumlah_kemasan'),
                'kode_jenis_kemasan' => decrypt($this->request->getVar('barang_detail_kode_jenis_kemasan')),
                'netto' => $this->request->getVar('barang_detail_berat_bersih'),
                'persentase_jenis_negara' => $this->request->getVar('persentase_jenis_negara')
            ]);
        }

        // ADD DATA DI TAB TRANSAKSI
        $bcBarangList = $this->bcBarangModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->where('deletedAt', null)
            ->findAll();
        $hargaTotal = 0;
        foreach ($bcBarangList as $b) {
            $hargaTotal += $b['harga_perolehan_barang'];
        }

        $bc23 = $this->bc23Model->get($bcPurchaseOrderID);
        if ($bc23 == null) {
            $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
            $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));
            $this->bc23Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju($tanggalDokumen),
                'nilai_barang' => $hargaTotal
            ]);
        } else {
            $this->bc23Model->update($bc23['id'], [
                'nilai_barang' => $hargaTotal
            ]);
        }

        return response()->setJSON([
            'message' => "Detail Barang Dokumen Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function createPungutanView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);

        if ($bcPo == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $kodeJenisPungutan = $this->metaDataModel
            ->where('name', "Kode Jenis Pungutan BC")
            ->whereIn('value', ['BM', 'PPN', 'PPH'])
            ->findAll();

        $barangTarif = $this->bcBarangTarifModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->findAll();

        $pungutanList = [];

        $bmDitangguhkan = 0;
        $bmDibebaskan = 0;
        $bmTidakDipungut = 0;

        $pphDitangguhkan = 0;
        $pphDibebaskan = 0;
        $pphTidakDipungut = 0;

        $ppnDitangguhkan = 0;
        $ppnDibebaskan = 0;
        $ppnTidakDipungut = 0;

        foreach ($barangTarif as $b) {
            if ($b['kode_jenis_pungutan'] == "BM" && $b['kode_fasilitas_tarif'] == 3) {
                $bmDitangguhkan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "BM" && $b['kode_fasilitas_tarif'] == 5) {
                $bmDibebaskan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "BM" && $b['kode_fasilitas_tarif'] == 6) {
                $bmTidakDipungut  += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPH" && $b['kode_fasilitas_tarif'] == 3) {
                $pphDitangguhkan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPH" && $b['kode_fasilitas_tarif'] == 5) {
                $pphDibebaskan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPH" && $b['kode_fasilitas_tarif'] == 6) {
                $pphTidakDipungut += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 3) {
                $ppnDitangguhkan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 5) {
                $ppnDibebaskan += $b['nilai_bayar'];
            } elseif ($b['kode_jenis_pungutan'] == "PPN" && $b['kode_fasilitas_tarif'] == 6) {
                $ppnTidakDipungut += $b['nilai_bayar'];
            }
        }

        foreach ($kodeJenisPungutan as $k) {
            if ($k['value'] == "BM") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'tidak_dipungut' => $bmTidakDipungut,
                    'dibebaskan' => $bmDibebaskan,
                    'ditangguhkan' => $bmDitangguhkan

                ];
            } elseif ($k['value'] == "PPH") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'tidak_dipungut' => $pphTidakDipungut,
                    'dibebaskan' => $pphDibebaskan,
                    'ditangguhkan' => $pphDitangguhkan

                ];
            } elseif ($k['value'] == "PPN") {
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

        return view('BeaCukai/bc-23/form-pungutan', $data);
    }

    public function createPernyataanView($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);

        $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
        $bc23 = $this->bc23Model->get($bcPurchaseOrderID);
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        if ($bcPo == null || $ceisaSetting == null) {
            return redirect()->to('bea-cukai-bc-23');
        }

        $this->setFlashDataNavigatorSession($bcPurchaseOrderID);

        $data = [
            'bcPo' => $bcPo,
            'bc23' => $bc23,
            'ceisaSetting' => $ceisaSetting
        ];

        return view('BeaCukai/bc-23/form-pernyataan', $data);
    }

    public function createPernyataanAction()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $lastData = $this->bc23Model->get($bcPurchaseOrderID);

        if ($lastData == null) {
            $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
            $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));            // insert
            $this->bc23Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju($tanggalDokumen),
                'nama_ttd' => $this->request->getVar('pernyatan_nama'),
                'kota_ttd' => $this->request->getVar('pernyatan_tempat'),
                'tanggal_ttd' => $this->request->getVar('pernyataan_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pernyataan_tanggal')), "Y-m-d") : "",
                'jabatan_pengusaha_ttd' => $this->request->getVar('pernyatan_jabatan'),
            ]);
        } else {
            // update
            $this->bc23Model->update($lastData['id'], [
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'nama_ttd' => $this->request->getVar('pernyatan_nama'),
                'kota_ttd' => $this->request->getVar('pernyatan_tempat'),
                'tanggal_ttd' => $this->request->getVar('pernyataan_tanggal') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('pernyataan_tanggal')), "Y-m-d") : "",
                'jabatan_pengusaha_ttd' => $this->request->getVar('pernyatan_jabatan')
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pernyataan berhasil diupdate",
        ]);
    }

    // Navigator display
    private function setFlashDataNavigatorSession($bcPurchaseOrderID)
    {
        $isCompleteFormPernyataan = $this->bc23Model->isCompleteFormPernyataan($bcPurchaseOrderID);
        $isCompleteFormHeader = $this->bc23Model->isCompleteFormHeader($bcPurchaseOrderID);
        $isCompleteFormEntitas = $this->bc23Model->isCompleteFormEntitas($bcPurchaseOrderID);
        $isCompleteFormDokumen = $this->bc23Model->isCompleteFormDokumen($bcPurchaseOrderID);
        $isCompleteFormPengangkut = $this->bc23Model->isCompleteFormPengangkut($bcPurchaseOrderID);
        $isCompleteFormPetiKemas = $this->bc23Model->isCompleteFormPetiKemas($bcPurchaseOrderID);
        $isCompleteFormFormTransaksi = $this->bc23Model->isCompleteFormTransaksi($bcPurchaseOrderID);
        $isCompleteFormBarang = $this->bc23Model->isCompleteFormBarang($bcPurchaseOrderID);
        $isCompleteFormPungutan = $this->bc23Model->isCompleteFormPungutan($bcPurchaseOrderID);

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
                $this->bc23Model->set('status_dokumen', "Siap Kirim")->where('bc_purchase_order_id', $bcPurchaseOrderID)->update();
            } else {
                $this->bc23Model->set('status_dokumen', "Belum Lengkap")->where('bc_purchase_order_id', $bcPurchaseOrderID)->update();
            }
        }
    }

    public function updateNoAju()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $noAju = $this->request->getVar('no_pengajuan');
        $bc23First = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();

        $bc23 = $this->bc23Model
            ->where('no_aju', $noAju)
            ->whereNotIn(
                'bc_purchase_order_id',
                [$bcPurchaseOrderID]
            )->first();

        if ($bc23 != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Nomor aju sudah ada",
                'status' => false,
            ]);
        }

        if ($bc23First == null) {
            $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
            $this->bc23Model->insert([
                'no_aju' => $noAju,
                'bc_purchase_order_id' => $bcPurchaseOrderID,
            ]);
        } else {
            $this->bc23Model->set('no_aju', $noAju)
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

        $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->delete();
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
            'message' => "Dokumen BC 23 Berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    // KIRIM BC.23 KE CEISA
    public function kirimCeisa($bcPurchaseOrderID)
    {
        $bcPurchaseOrderID = decrypt($bcPurchaseOrderID);
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);
        $payload = $this->generatePayload($bcPurchaseOrderID);
        // return response()->setJSON($payload);
        // die;

        $res = $beacukaiApi->kirimDokumenBC($payload, false);


        if ($res['status'] == false) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal Kirim Ceisa Karena : " . $res['message'],
            ]);
        }
        // // UPDATE STATUS
        $this->bc23Model->set('status_dokumen', "Sudah Kirim")->where('bc_purchase_order_id', $bcPurchaseOrderID)->update();
        $this->bcPurchaseOrderModel->update($bcPurchaseOrderID, [
            'status_posting' => '1'
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 2.3 Berhasil Online di Ceisa",
            'res' => $res
        ]);
    }

    public function generatePayload($bcPurchaseOrderID)
    {
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);

        $bc23Data = $this->bc23Model->get($bcPurchaseOrderID);

        $bc23Kontainer = $this->bcKontainerModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Barang = $this->bcBarangModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Entitas = $this->bcEntitasModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Kemasan = $this->bcKemasanModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Dokumen = $this->bcDokumenModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();
        $bc23Pengangkut = $this->bcPengangkutModel->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->findAll();

        $payload = $beacukaiApi->payloadTempleateKirimBC23(
            $bc23Data,
            $bc23Kontainer,
            $bc23Barang,
            $bc23Entitas,
            $bc23Kemasan,
            $bc23Dokumen,
            $bc23Pengangkut
        );

        return $payload;
    }

    // POSTING BEA CUKAI PO
    public function posting()
    {
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));
        $bc40 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $bcPurchaseOrder = $this->bcPurchaseOrderModel->where('id', $bcPurchaseOrderID)->first();
        $tanggalDokumen = date('Y-m-d', strtotime($bcPurchaseOrder['createdAt']));

        if ($bc40 == null) {
            // insert
            $this->bc23Model->insert([
                'bc_purchase_order_id' => $bcPurchaseOrderID,
                'no_aju' => $this->generateNomorAju($tanggalDokumen),
            ]);
        }

        $bc40 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $status = $this->insertInventori($bcPurchaseOrderID);

        if (!$status) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Terjadi kesalahan saat menambah stok inventori"
            ]);
        }

        $this->bcPurchaseOrderModel->update($bcPurchaseOrderID, [
            'status_posting' => '1',
            'no_aju' => $bc40['no_aju'],
            'bc_id' => 48,
            'bc_type' => "BC 2.3"
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 2.3 Berhasil Diposting",
        ]);
    }


    // API GET
    public function getValuta()
    {
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);
        $kodeValuta = decrypt($this->request->getVar('harga_kode_valuta'));
        $res = $beacukaiApi->getNilaiValuta($kodeValuta);

        return response()->setJSON([
            'data' => $res,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getPelabuhan()
    {
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);
        $kodeKantorBongkar = $this->request->getVar('header_kantor_pabean_bongkar');
        $res = $beacukaiApi->getListKodePelabuhan($kodeKantorBongkar);

        return response()->setJSON([
            'data' => $res,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getPelabuhanByKata()
    {
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);
        $kataPelabuhan = $this->request->getVar('search');
        $res = $beacukaiApi->getListPelabuhanByKata($kataPelabuhan);

        return response()->setJSON([
            'data' => $res,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getTpsByKodeKantor()
    {
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);
        $kodeKantor = decrypt($this->request->getVar('kodeKantor'));
        $res = $beacukaiApi->getKodeTpsByKodeKantor($kodeKantor);

        return response()->setJSON([
            'data' => $res,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getManifest()
    {
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);
        $bcPurchaseOrderID = decrypt($this->request->getVar('bc_purchase_order_id'));

        $bc23DokumenBL = $this->bcDokumenModel
            ->whereIn('kode_dokumen', [705, 740]) // (702 = BL, 740 = AWB)
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->first();

        $bc23 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();
        $namaImportirDefault = $this->metaDataModel->where('name', "Nama Importir Default BC")->first();

        if ($bc23DokumenBL == null) {
            return response()->setJSON([
                'message' => "Dokumen B/L atau AWB tidak ada",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if ($bc23 == null) {
            return response()->setJSON([
                'message' => "Isi Tab Header terlebih dahulu",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        if ($bc23['kode_kantor_bongkar'] == null || $bc23['kode_kantor_bongkar'] == '') {
            return response()->setJSON([
                'message' => "Kode kantor bongkar di Tab Header wajib diisi",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $res = $beacukaiApi->getManifest(
            $bc23DokumenBL['nomor_dokumen'],
            date('d-m-Y', strtotime($bc23DokumenBL['tanggal_dokumen'])),
            $bc23['kode_kantor_bongkar'],
            $namaImportirDefault['value']
        );

        $resGudangTps = $beacukaiApi->getKodeTpsByKodeKantor(
            $bc23['kode_kantor_bongkar']
        );

        return response()->setJSON([
            'data' => $res,
            'gudangTps' => $resGudangTps,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getKodeSatuanBarang()
    {
        $metaDataModel = new MetadataModel();
        $search = $this->request->getVar('search');
        $data = $metaDataModel->getKodeSatuanBarang($search);
        return $this->response->setJSON(['results' => $data,]);
    }

    public function generateNomorAju($tanggalDokumen)
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
    //     $poType = "INTERNASIONAL";
    //     $supplier = $this->supplierModel
    //         ->where('deletedAt', null)
    //         ->where('type', $poType)
    //         ->orderBy('name', "ASC")
    //         ->findAll();

    //     $supplierResult = [];

    //     if (empty($this->request->getVar('po_type'))) {
    //         return response()->setJSON([
    //             'data' => $supplierResult,
    //             'token' => csrf_hash(),
    //             'status' => true
    //         ]);
    //     }

    //     foreach ($supplier as $s) {
    //         $result = $this->bc40Controller->getListDataPurchaseOrderExport(
    //             $this->request->getVar('po_type'),
    //             $s['id'],
    //         );

    //         if (count($result) != 0) {
    //             array_push($supplierResult, $s);
    //         }
    //     }

    //     return response()->setJSON([
    //         'data' => $supplierResult,
    //         'token' => csrf_hash(),
    //         'status' => true
    //     ]);
    // }

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
        $supplierData = $this->supplierModel->getSupplierByType("INTERNASIONAL");
        return response()->setJSON([
            'token' => csrf_token(),
            'data' => $supplierData,
            'status' => true,
        ]);
    }

    public function exportPdf()
    {
        $supplierId = $this->request->getVar('supplier_id');
        $poType = $this->request->getVar('po_type');
        $startDate = $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "";
        $endDate = $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "";

        $result = $this->bc40Controller->getListDataPurchaseOrderExport($poType, $supplierId, $startDate, $endDate);

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
        $this->dompdf->stream("BC 2.3 Purchase Order", array("Attachment" => false));
    }

    private function insertInventoriRevamp($bcPurchaseOrderID)
    {
        try {
            $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
            $bc23 = $this->bc23Model->where('bc_purchase_order_id', $bcPurchaseOrderID)->first();

            $poIdArr = array_unique(json_decode($bcPo['multiple_po_id']));
            $lpbIdArr = array_unique(json_decode($bcPo['multiple_lpb_id']));
            $typeBahan = $bcPo['po_type'] == "IMPORT BAKU" ? "bahan_baku" : "bahan_penolong";

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
                        $p['jml_masuk_konversi']
                    );

                    // DETAIL
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        $p['jml_masuk_konversi'],
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
                        $po = $this->rmImportPoModel->find($p['purchase_order_id']);
                    }
                    // SUB DETAIL
                    $this->stockDetail2Model->insertStokDetail2(
                        $penerimaanBarang['bc_type'],
                        $stok,
                        $stokDetail,
                        $p['jml_masuk_konversi'],
                        $bc23['no_aju'],
                        $po['po_no'],
                        $po['po_no'],
                        $penerimaanBarang['supplier_id'],
                        $p['harga'],
                        $p['harga_harian'],
                        $p['harga_bulanan'],
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
                    date('Y-m-d', strtotime($bcPo['createdAt'])),
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
                    $bc23['no_aju'],
                    $penerimaanBarang['no_penerimaan_barang'],
                    $penerimaanBarang['no_penerimaan_barang'],
                    $penerimaanBarang['supplier_id'],
                );
            }

            return true;
        } catch (Exception $e) {
            var_dump($e->getMessage(), $e->getTrace());
            return false;
        }
    }

    private function insertInventori($bcPurchaseOrderID)
    {
        try {
            $bcPo = $this->bcPurchaseOrderModel->find($bcPurchaseOrderID);
            $lpbIdArr = array_unique(json_decode($bcPo['multiple_lpb_id']));

            foreach ($lpbIdArr as $lpbId) {
                $this->penerimaanBarangLokalBp->insert_stock_pembelian_revamp(
                    $lpbId
                );
            }
            return true;
        } catch (Exception $e) {
            var_dump($e->getMessage(), $e->getTrace());
            return false;
        }
    }


    public function viewOutstanding()
    {
        $data = [
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('BeaCukai/bc-23/bc23outstanding', $data);
    }

    public function allOutstandingServerSide()
    {
        $draw = $this->request->getGet('draw');
        $start = is_numeric($this->request->getGet('start')) ? intval($this->request->getGet('start')) : 0;
        $length = is_numeric($this->request->getGet('length')) ? intval($this->request->getGet('length')) : 10;
        $searchValue = $this->request->getGet('search');
        $dateStart = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $dateEnd =  $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";
        $tipeBahan = $this->request->getGet('tipe_bahan');
        $divisiId = $this->request->getGet('divisi_id');

        $lpbUsed = $this->bcPurchaseOrderModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->whereIn('po_type', ["IMPORT BAKU", "IMPORT PENOLONG"])
            ->findAll();

        $lpbUsedArr = [];
        foreach ($lpbUsed as $used) {
            $usedIds = json_decode($used['multiple_lpb_id']);
            if (is_array($usedIds)) {
                $lpbUsedArr = array_merge($lpbUsedArr, $usedIds);
            }
        }

        $db = \Config\Database::connect();
        $where = [];

        if (!empty($dateStart) && !empty($dateEnd)) {
            $where[] = "lpb_date BETWEEN " . $db->escape($dateStart) . " AND " . $db->escape($dateEnd);
        }

        if (!empty($tipeBahan)) {
            $where[] = "tipe_bahan = " . $db->escape($tipeBahan);
        }

        if (!empty($divisiId)) {
            $where[] = "divisis_id = " . $db->escape($divisiId);
        }

        if (!empty($searchValue)) {
            $search = $db->escapeLikeString($searchValue);
            $where[] = "(supplier LIKE '%$search%' OR barang_name LIKE '%$search%' OR no_penerimaan_barang LIKE '%$search%' OR po_no LIKE '%$search%')";
        }

        if (!empty($lpbUsedArr)) {
            $ids = implode(',', array_map('intval', $lpbUsedArr));
            $where[] = "id NOT IN ($ids)";
        }

        $filterCondition = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // MAIN UNION QUERY
        $mainQuery = "
        SELECT * FROM (
            SELECT 
                pb.id,
                pb.tanggal AS lpb_date,
                pb.no_penerimaan_barang,
                pb.status_penerimaan,
                pb.tipe_bahan,
                pb.jumlah_kemasan,
                divisis.id AS divisis_id,
                divisis.divisi,
                po.po_no,
                po.po_date,
                bm.barang_name,
                bm.kode_barang,
                s.name AS supplier,
                satuan_lpb.kode_satuan AS kode_satuan_lpb,
                satuan_po.kode_satuan AS kode_satuan_po,
                satuan_kemasan.kode_satuan AS kode_satuan_kemasan,
                kemasan.name AS nama_kemasan,
                metadata.value AS valas,
                SUM(pbd.qty) AS qty_po,
                SUM(pbd.jml_masuk) AS qty_lpb,
                SUM(pbd.sub_total) AS sub_total
            FROM penerimaan_barang pb
            LEFT JOIN penerimaan_barang_detail pbd ON pbd.penerimaan_barang_id = pb.id
            LEFT JOIN rm_import_pos po ON po.id = pbd.purchase_order_id
            LEFT JOIN metadata ON metadata.id = po.currency
            LEFT JOIN barang_master bm ON bm.id = pbd.barang_id
            LEFT JOIN suppliers s ON s.id = pb.supplier_id
            LEFT JOIN satuans satuan_lpb ON satuan_lpb.id = pbd.unit_konversi
            LEFT JOIN satuans satuan_po ON satuan_po.id = pbd.unit
            LEFT JOIN kemasan ON kemasan.id = pb.kemasan_id
            LEFT JOIN satuans satuan_kemasan ON satuan_kemasan.id = kemasan.satuan_id
            LEFT JOIN divisis ON divisis.id = pb.divisi_id
            WHERE pb.tipe_bahan = 'BAKU'
                AND pb.deletedAt IS NULL
                AND pbd.deletedAt IS NULL
                AND pb.status_penerimaan = 'IMPORT'
                AND pb.status_post = 'FINISH'
                AND pb.bc_type = '48'
                AND pb.company_id = {$this->this_company_id}
            GROUP BY pb.id, pbd.barang_id

            UNION ALL

            SELECT 
                pb.id,
                pb.tanggal AS lpb_date,
                pb.no_penerimaan_barang,
                pb.status_penerimaan,
                pb.tipe_bahan,
                pb.jumlah_kemasan,
                divisis.id AS divisis_id,
                divisis.divisi,
                po.po_no,
                po.po_date,
                bm.barang_name,
                bm.kode_barang,
                s.name AS supplier,
                satuan_lpb.kode_satuan AS kode_satuan_lpb,
                satuan_po.kode_satuan AS kode_satuan_po,
                satuan_kemasan.kode_satuan AS kode_satuan_kemasan,
                kemasan.name AS nama_kemasan,
                metadata.value AS valas,
                SUM(pbd.qty) AS qty_po,
                SUM(pbd.jml_masuk) AS qty_lpb,
                SUM(pbd.sub_total) AS sub_total
            FROM penerimaan_barang pb
            LEFT JOIN penerimaan_barang_detail pbd ON pbd.penerimaan_barang_id = pb.id
            LEFT JOIN am_purchase_orders po ON po.id = pbd.purchase_order_id
            LEFT JOIN metadata ON metadata.id = po.currency
            LEFT JOIN barang_master bm ON bm.id = pbd.barang_id
            LEFT JOIN suppliers s ON s.id = pb.supplier_id
            LEFT JOIN satuans satuan_lpb ON satuan_lpb.id = pbd.unit_konversi
            LEFT JOIN satuans satuan_po ON satuan_po.id = pbd.unit
            LEFT JOIN kemasan ON kemasan.id = pb.kemasan_id
            LEFT JOIN satuans satuan_kemasan ON satuan_kemasan.id = kemasan.satuan_id
            LEFT JOIN divisis ON divisis.id = pb.divisi_id
            WHERE pb.tipe_bahan = 'PENOLONG'
                AND pb.deletedAt IS NULL
                AND pbd.deletedAt IS NULL
                AND pb.status_penerimaan = 'IMPORT'
                AND pb.status_post = 'FINISH'
                AND pb.bc_type = '48'
                AND pb.company_id = {$this->this_company_id}
            GROUP BY pb.id, pbd.barang_id
        ) AS dropdown
        $filterCondition
    ";

        // --- Ambil order dari DataTables ---
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';

        // Mapping index kolom DataTables ke nama kolom SQL
        $columns = [
            'id',
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

        // Hitung total filtered
        $countQuery = $db->query("SELECT COUNT(*) AS total FROM ($mainQuery) AS count_table");
        $totalFiltered = $countQuery->getRow()->total;
        $totalRecords = $totalFiltered;

        // Tambahkan LIMIT OFFSET
        $baseQuery = $mainQuery . " $orderBy LIMIT $length OFFSET $start";
        $data = $db->query($baseQuery)->getResultArray();

        // Format response
        $formatted = [];
        $no = $start + 1;

        foreach ($data as $row) {
            $formatted[] = [
                'no' => $no++,
                'tipe_bahan' => "IMPORT " . $row['tipe_bahan'],
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
                'valas' => $row['valas'],
                'sub_total' => number_format($row['sub_total'], 2),
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $formatted,
        ]);
    }

    public function OutstandingExcel()
    {
        $dateStart = $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "";
        $dateEnd =  $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "";
        $tipeBahan = $this->request->getGet('tipe_bahan');
        $divisiId = $this->request->getGet('divisi_id');

        $lpbUsed = $this->bcPurchaseOrderModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->whereIn('po_type', ["IMPORT BAKU", "IMPORT PENOLONG"])
            ->findAll();

        $lpbUsedArr = [];
        foreach ($lpbUsed as $used) {
            $usedIds = json_decode($used['multiple_lpb_id']);
            if (is_array($usedIds)) {
                $lpbUsedArr = array_merge($lpbUsedArr, $usedIds);
            }
        }

        $db = \Config\Database::connect();
        $where = [];

        if (!empty($dateStart) && !empty($dateEnd)) {
            $where[] = "lpb_date BETWEEN " . $db->escape($dateStart) . " AND " . $db->escape($dateEnd);
        }

        if (!empty($tipeBahan)) {
            $where[] = "tipe_bahan = " . $db->escape($tipeBahan);
        }

        if (!empty($divisiId)) {
            $where[] = "divisis_id = " . $db->escape($divisiId);
        }

        if (!empty($searchValue)) {
            $search = $db->escapeLikeString($searchValue);
            $where[] = "(supplier LIKE '%$search%' OR barang_name LIKE '%$search%' OR no_penerimaan_barang LIKE '%$search%' OR po_no LIKE '%$search%')";
        }

        if (!empty($lpbUsedArr)) {
            $ids = implode(',', array_map('intval', $lpbUsedArr));
            $where[] = "id NOT IN ($ids)";
        }

        $filterCondition = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // MAIN UNION QUERY
        $mainQuery = "
        SELECT * FROM (
            SELECT 
                pb.id,
                pb.tanggal AS lpb_date,
                pb.no_penerimaan_barang,
                pb.status_penerimaan,
                pb.tipe_bahan,
                pb.jumlah_kemasan,
                divisis.id AS divisis_id,
                divisis.divisi,
                po.po_no,
                po.po_date,
                bm.barang_name,
                bm.kode_barang,
                s.name AS supplier,
                satuan_lpb.kode_satuan AS kode_satuan_lpb,
                satuan_po.kode_satuan AS kode_satuan_po,
                satuan_kemasan.kode_satuan AS kode_satuan_kemasan,
                kemasan.name AS nama_kemasan,
                metadata.value AS valas,
                SUM(pbd.qty) AS qty_po,
                SUM(pbd.jml_masuk) AS qty_lpb,
                SUM(pbd.sub_total) AS sub_total
            FROM penerimaan_barang pb
            LEFT JOIN penerimaan_barang_detail pbd ON pbd.penerimaan_barang_id = pb.id
            LEFT JOIN rm_import_pos po ON po.id = pbd.purchase_order_id
            LEFT JOIN metadata ON metadata.id = po.currency
            LEFT JOIN barang_master bm ON bm.id = pbd.barang_id
            LEFT JOIN suppliers s ON s.id = pb.supplier_id
            LEFT JOIN satuans satuan_lpb ON satuan_lpb.id = pbd.unit_konversi
            LEFT JOIN satuans satuan_po ON satuan_po.id = pbd.unit
            LEFT JOIN kemasan ON kemasan.id = pb.kemasan_id
            LEFT JOIN satuans satuan_kemasan ON satuan_kemasan.id = kemasan.satuan_id
            LEFT JOIN divisis ON divisis.id = pb.divisi_id
            WHERE pb.tipe_bahan = 'BAKU'
                AND pb.deletedAt IS NULL
                AND pbd.deletedAt IS NULL
                AND pb.status_penerimaan = 'IMPORT'
                AND pb.status_post = 'FINISH'
                AND pb.bc_type = '48'
                AND pb.company_id = {$this->this_company_id}
            GROUP BY pb.id, pbd.barang_id

            UNION ALL

            SELECT 
                pb.id,
                pb.tanggal AS lpb_date,
                pb.no_penerimaan_barang,
                pb.status_penerimaan,
                pb.tipe_bahan,
                pb.jumlah_kemasan,
                divisis.id AS divisis_id,
                divisis.divisi,
                po.po_no,
                po.po_date,
                bm.barang_name,
                bm.kode_barang,
                s.name AS supplier,
                satuan_lpb.kode_satuan AS kode_satuan_lpb,
                satuan_po.kode_satuan AS kode_satuan_po,
                satuan_kemasan.kode_satuan AS kode_satuan_kemasan,
                kemasan.name AS nama_kemasan,
                metadata.value AS valas,
                SUM(pbd.qty) AS qty_po,
                SUM(pbd.jml_masuk) AS qty_lpb,
                SUM(pbd.sub_total) AS sub_total
            FROM penerimaan_barang pb
            LEFT JOIN penerimaan_barang_detail pbd ON pbd.penerimaan_barang_id = pb.id
            LEFT JOIN am_purchase_orders po ON po.id = pbd.purchase_order_id
            LEFT JOIN metadata ON metadata.id = po.currency
            LEFT JOIN barang_master bm ON bm.id = pbd.barang_id
            LEFT JOIN suppliers s ON s.id = pb.supplier_id
            LEFT JOIN satuans satuan_lpb ON satuan_lpb.id = pbd.unit_konversi
            LEFT JOIN satuans satuan_po ON satuan_po.id = pbd.unit
            LEFT JOIN kemasan ON kemasan.id = pb.kemasan_id
            LEFT JOIN satuans satuan_kemasan ON satuan_kemasan.id = kemasan.satuan_id
            LEFT JOIN divisis ON divisis.id = pb.divisi_id
            WHERE pb.tipe_bahan = 'PENOLONG'
                AND pb.deletedAt IS NULL
                AND pbd.deletedAt IS NULL
                AND pb.status_penerimaan = 'IMPORT'
                AND pb.status_post = 'FINISH'
                AND pb.bc_type = '48'
                AND pb.company_id = {$this->this_company_id}
            GROUP BY pb.id, pbd.barang_id
        ) AS dropdown
        $filterCondition
    ";

        $data = $db->query($mainQuery)->getResultArray();

        $formatted = [];
        $no = 1;

        foreach ($data as $row) {

            $formatted[] = [
                'no' => $no++,
                'tipe_bahan' => "IMPORT " . $row['tipe_bahan'],
                'divisi' => $row['divisi'],
                'supplier' => $row['supplier'],
                'po_date' => date('d/m/Y', strtotime($row['po_date'])),
                'lpb_date' => date('d/m/Y', strtotime($row['lpb_date'])),
                'no_penerimaan_barang' => $row['no_penerimaan_barang'],
                'po_no' => $row['po_no'],
                'kode_barang' => $row['kode_barang'],
                'barang' => $row['barang_name'],
                'qty_po' => number_format($row['qty_po'], 2),
                'satuan_po' =>  $row['kode_satuan_po'],
                'qty_lpb' => number_format($row['qty_lpb'], 2),
                'satuan_lpb' => $row['kode_satuan_lpb'],
                'kemasan' => $row['nama_kemasan'],
                'qty_kemasan' => number_format($row['jumlah_kemasan'], 2),
                'satuan_kemasan' => $row['kode_satuan_kemasan'],
                'valas' => $row['valas'],
                'sub_total' => number_format($row['sub_total'], 2),
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
            'Valas',
            'Sub Total'
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
                ->setCellValue('R' . $rowNum, $f['valas'])
                ->setCellValue('S' . $rowNum, $f['sub_total']);
            $rowNum++;
        }

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
        $filename = 'Laporan Outstanding BC 2.3 ' . $dateStart . " s.d " . $dateEnd;

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
