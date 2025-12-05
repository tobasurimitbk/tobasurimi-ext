<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC41Model;
use App\Models\BcPengeluaranBarangModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\CeisaSettingModel;
use App\Models\CustomerModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengembalianBarangDetailModel;
use App\Models\PengembalianBarangModel;
use App\Models\PengusahaTPBModel;
use App\Models\SalesOrderDetailModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\SalesOrderLainModel;
use App\Models\SalesOrderModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampDetailModel;
use Exception;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 4.1 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54

class BC41 extends BaseController
{

    protected $this_company_id;
    protected $this_user_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $metaDataModel;
    protected $salesOrderLainModel;
    protected $salesOrderLainDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $kantorBeaCukaiModel;
    protected $pengusahaTPBModel;
    protected $barangMasterSpesifikasiModel;
    protected $bc41Model;
    protected $hsCodeModel;
    protected $bcPurchaseOrderModel;
    protected $pengembalianBarangModel;
    protected $penerimaanBarangModel;
    protected $pengembalianBarangDetailModel;
    protected $salesOrderModel;
    protected $salesOrderDetailModel;
    protected $divisiModel;
    protected $satuanModel;
    protected $customerModel;
    protected $bcPengeluaranBarangModel;
    protected $stockRevampDetailModel;

    public function __construct()
    {
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->bc41Model = new BC41Model();
        $this->metaDataModel = new MetadataModel();
        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->hsCodeModel = new HsCodesModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
        $this->salesOrderModel = new SalesOrderModel();
        $this->salesOrderDetailModel = new SalesOrderDetailModel();
        $this->stockRevampDetailModel = new StockRevampDetailModel();
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $this->satuanModel = new SatuansModel();
        $this->bcPengeluaranBarangModel = new BcPengeluaranBarangModel();
        $this->divisiModel = new DivisisModel();
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first(),
        ];

        return view('BeaCukai/BC-41/index', $data);
    }

    public function all()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;

        $dateStart = $this->request->getVar("dateStart")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
            : null;

        $dateEnd = $this->request->getVar("dateEnd")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
            : null;

        $statusPosting = $this->request->getGet('status_posting');
        $search = $this->request->getGet('search');

        $condition = [
            'company_id'        => $this->this_company_id,
            'dateStart'         => $dateStart,
            'dateEnd'           => $dateEnd,
            'status_posting'    => $statusPosting,
            'search'            => $search
        ];

        $data = $this->bc41Model->getList(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $dataResult = array();
        $no = $start + 1;
        foreach ($data['data'] as $d) {
            array_push($dataResult, [
                'no' => $no++,
                'id' => encrypt($d['id']),
                'jenis_pengeluaran' => $d['jenis_pengeluaran'],
                'reference_penerima' => $d['reference_penerima'],
                'multiple_reference_no' => str_replace(['"', ']', '['], " ",  $d['multiple_reference_no']),
                "no_aju" => ($d['no_aju'] == "" ? "-" : $d['no_aju']) . " / " . ($d['no_daftar'] == "" ? "-" : $d['no_daftar']),
                'tanggal' => !empty($d['tanggal']) && $d['tanggal'] != null ? date('d/m/Y', strtotime($d['tanggal'])) : "",
                'status_posting' => $d['status_posting'],
            ]);
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($data['totalData'] ?? 0),
            'recordsFiltered' => intval($data['totalFilteredData'] ?? 0),
            'data' => $dataResult,
        ]);
    }

    public function createAction()
    {
        try {
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $jenisPengeluaran = $this->request->getVar('jenis_pengeluaran');
            $noAju = $this->generateNomorAju($tanggal);
            $payload = $this->get_payload();

            $payload = json_decode($payload);
            $payload->nomorAju = $noAju;
            $payload->tanggalAju = $tanggal;
            $payload->idPengguna = "NPWP";

            $payloadUpdate = json_encode($payload);

            $id = $this->bc41Model->insert([
                'company_id' => $this->this_company_id,
                'tanggal' => $tanggal,
                'jenis_pengeluaran' => $jenisPengeluaran,
                'no_aju' => $noAju,
                'no_daftar' => null,
                'multiple_reference_id' => null,
                'multiple_reference_no' => null,
                'status_dokumen' => "Belum Lengkap",
                'payload' => $payloadUpdate,
                'status_posting' => 0
            ]);

            return response()->setJSON([
                'message' => 'Dokumen berhasil dibuat',
                'id' => encrypt($id),
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }
    public function updateDetail()
    {
        // return response()->setJSON([
        //     'token' => csrf_hash(),
        //     '$_POST' => $_POST,
        //     'listStock' => json_decode($_POST['listStock']),
        //     'status' => false
        // ]);
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $jenisPengeluaran = $this->request->getVar('jenis_pengeluaran');
            $noAju = $this->request->getVar('no_pengajuan');
            $noDaftar = $this->request->getVar('no_daftar');
            $referencePenerimaId = $this->request->getVar('reference_penerima_id');
            $multipleReferenceIdArr = $this->request->getVar('multiple_reference_id');
            $multipleReferenceNo = null;
            $multipleReferenceId = null;

            if (!empty($multipleReferenceIdArr) && count($multipleReferenceIdArr) != 0) {
                $multipleReferenceNo = $this->bcPengeluaranBarangModel->getReferensiNoPengeluaranOrderFormLokal($multipleReferenceIdArr);
                $multipleReferenceId = "[" . implode(",", $multipleReferenceIdArr) . "]";
            }


            $this->bc41Model->update($id, [
                'tanggal' => $tanggal,
                'reference_penerima_id' => $referencePenerimaId,
                'no_aju' => $noAju,
                'no_daftar' => $noDaftar,
                'multiple_reference_id' => $multipleReferenceId,
                'multiple_reference_no' => $multipleReferenceNo,
                'jenis_pengeluaran' => $jenisPengeluaran,
            ]);

            $listStock = json_decode($this->request->getVar('listStock'));
            $this->bcPengeluaranBarangModel->where('bc_pengeluaran_id', $id)->where('tipe_bc', "BC 4.1")->delete(null, true);
            foreach ($listStock as $l) {
                $stock = $this->stockRevampDetailModel
                    ->select('stock_revamp_detail.*,stock_revamp.barang_master_id')
                    ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
                    ->where('stock_revamp_detail.id', $l->id)
                    ->first();

                $this->bcPengeluaranBarangModel->insert([
                    'stock_detail_id'    => $l->id,
                    'barang_master_id'   => $stock['barang_master_id'],
                    'bc_pengeluaran_id'  => $id,
                    'unit_id_konversi'   => $l->keluar->unit_id_konversi,
                    'unit_id_keluar'     => $l->keluar->unit_id_keluar,
                    'valas_id'           => $l->keluar->valas_id,
                    'tipe_bc'            => "BC 4.1",
                    'harga_satuan'       => $l->keluar->harga_satuan,
                    'nilai_tukar'        => $l->keluar->nilai_tukar,
                    'sub_total'          => $l->keluar->sub_total,
                    'qty_keluar'         => $l->keluar->qty_keluar,
                    'qty_konversi'       => $l->keluar->qty_konversi,
                ]);
            }

            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Berhasil update",
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }


    public function detail($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->where('id', $id)->first();
        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description !=', "kemasan")
            ->findAll();

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $satuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->where('deletedAt', null)->findAll();
        $divisi = $this->divisiModel->getDivisiAccess();
        $dataCustomer = [];
        $dataReferencePengeluaran = [];

        if ($bc41['reference_penerima_id'] != null) {
            $dataCustomer = $this->customerModel->where('id', $bc41['reference_penerima_id'])->findAll();
        }

        if ($bc41['multiple_reference_id'] != null) {
            $dataReferencePengeluaran = $this->salesOrderModel->whereIn('id', json_decode($bc41['multiple_reference_id']))->where('deletedAt', null)->findAll();
        }

        $dataBarang = $this->bcPengeluaranBarangModel->getDetailBarang(
            $id,
            "BC 4.1"
        );

        $data = [
            'bc41' => $bc41,
            'tipeBarang' => $dataTipeBarang,
            'dataSatuan' => $satuan,
            'noAju' => $bc41 == null ? $this->generateNomorAju($bc41['tanggal']) : $bc41['no_aju'],
            'dataValuta' => $dataValuta,
            'dataCustomer' => $dataCustomer,
            'dataReferencePengeluaran' => $dataReferencePengeluaran,
            'dataBarang' => $dataBarang,
            'divisi' => $divisi
        ];

        return view('BeaCukai/BC-41/form', $data);
    }


    public function delete()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $this->bc41Model->delete($id);
            $this->bcPengeluaranBarangModel->where('bc_pengeluaran_id', $id)->where('tipe_bc', "4.1")->delete(null, false);
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Dokumen BC 4.1 Berhasil Dihapus"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $db = \Config\Database::connect();
        $db->transStart();

        $this->bc41Model->update($id, ['status_posting' => '1']);
        $db->transComplete();

        if ($db->transStatus() == false) {
            $db->transRollback();
        }

        $db->transCommit();

        return response()->setJSON([
            'token'  => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Diposting"
        ]);
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

        $kodeDokumenbc41Static = $this->metaDataModel
            ->where('name', "Kode BC41 Static")
            ->first();

        $kodeKantorStatic = $ceisaSetting['kode_unik']
            ? $ceisaSetting['kode_unik']
            : $ceisaSetting['kode_kantor_pabean'];

        $tanggalAju = date('Ymd', strtotime($tanggalDokumen));
        $tahunAjuSekarang = date('Y', strtotime($tanggalDokumen));

        // Ambil BC40 terakhir berdasarkan urutan no_aju terbaru
        $bc41Last = $this->bc41Model
            ->whereIn('company_id', $company_id_arr)
            ->orderBy('tanggal', "DESC")
            ->limit(1)
            ->first();

        // Default urutan
        $sequenceNoUrutPengajuan = "000001";

        if ($bc41Last && $bc41Last['no_aju']) {
            $arrNo = explode('-', $bc41Last['no_aju']);

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

        return $kodeDokumenbc41Static['value']
            . '-' . $kodeKantorStatic
            . '-' . $tanggalAju
            . '-' . $sequenceNoUrutPengajuan;
    }

    public function header($id)
    {
        $id = decrypt($id);

        $bc41 = $this->bc41Model->where('id', $id)->first();
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $data = [
            'bc41' => $bc41,
            'noAju' => $bc41 == null ? $this->generateNomorAju($bc41['tanggal']) : $bc41['no_aju'],
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'kodeTujuanTpb' => $this->metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTujuanPengiriman' => $this->metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', 40)->where('deletedAt', null)->findAll(),
            'selectedKantor' => $ceisaSetting['kode_kantor_pabean'],
            'payload' => json_decode($bc41['payload'])
        ];

        return view('BeaCukai/BC-41/form-header', $data);
    }

    public function updateHeader()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc41 = $this->bc41Model->find($id);
        $payload = json_decode($bc41['payload']);

        $payload->asalData = "S";
        $payload->nomorAju =  str_replace('-', '', $this->request->getVar('nomorAju'));
        $payload->tanggalAju = getDateFromNomorAju($this->request->getVar('nomorAju'));
        $payload->kodeKantor = $this->request->getVar('kodeKantor');
        $payload->kodeTujuanPengiriman =  $this->request->getVar('kodeTujuanPengiriman');
        $payload->kodeJenisTpb = $this->request->getVar('kodeJenisTpb');

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Header berhasil disimpan"
        ]);
    }

    public function entitas($id)
    {
        $id = decrypt($id);

        $bc41 = $this->bc41Model->find($id);
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->findAll();

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $this->setFlashDataNavigatorSession($id);

        $payload = json_decode($bc41['payload']);
        // JIKA MASIH KOSONG SET DULU BOSQ
        if (!is_array($payload->entitas)) {
            $payload->entitas = [
                [
                    'alamatEntitas' => "",
                    'kodeEntitas' => "3",
                    'kodeJenisApi' => "02",
                    'kodeJenisIdentitas' => "5",
                    'kodeStatus' => '3',
                    'namaEntitas' => "",
                    'nibEntitas' => "",
                    'nomorIdentitas' => "",
                    'nomorIjinEntitas' => "",
                    'tanggalIjinEntitas' => "",
                    'seriEntitas' => 1,
                ],
                [
                    'alamatEntitas' => "",
                    'kodeEntitas' => "7",
                    'kodeJenisIdentitas' => "5",
                    'kodeStatus' => '3',
                    'namaEntitas' => "",
                    'nomorIdentitas' => "",
                    'seriEntitas' => 2,
                ],
                [
                    'alamatEntitas' => "",
                    'kodeEntitas' => "3",
                    'kodeJenisApi' => "02",
                    'kodeJenisIdentitas' => "5",
                    'kodeStatus' => '3',
                    'namaEntitas' => "",
                    'niperEntitas' => "",
                    'nomorIdentitas' => "",
                    'seriEntitas' => 3,
                ]
            ];

            $this->bc41Model->update($id, ['payload' => json_encode($payload)]);
        }

        $data = [
            'bc41' => $bc41,
            'pengusahaTPB' => $pengusahaTPB,
            'payload' => json_decode($this->bc41Model->find($id)['payload'])
        ];

        return view('BeaCukai/BC-41/form-entitas', $data);
    }

    public function updateEntitas()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc41 = $this->bc41Model->find($id);
        $payload = json_decode($bc41['payload']);

        $payload->entitas[0] = [
            'alamatEntitas' => $this->request->getVar('entitas_alamat_pengusaha'),
            'kodeEntitas' => "3",
            'kodeJenisIdentitas' => "5",
            'namaEntitas' => $this->request->getVar('entitas_nama_pengusaha'),
            'nibEntitas' => $this->request->getVar('entitas_nib'),
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_pengusaha'),
            'nomorIjinEntitas' => $this->request->getVar('entitas_nomor_ijin_tpb'),
            'seriEntitas' => 1,
            'tanggalIjinEntitas' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb')), "Y-m-d"),
        ];

        $payload->entitas[1] = [
            'alamatEntitas' => $this->request->getVar('entitas_alamat_pemilik_barang'),
            'kodeEntitas' => "7",
            'kodeJenisApi' => "02",
            'kodeJenisIdentitas' => "5",
            'kodeStatus' => '3',
            'namaEntitas' => $this->request->getVar('entitas_nama_pemilik_barang'),
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_pemilik_barang'),
            'nomorIjinEntitas' => $this->request->getVar('entitas_nomor_ijin_tpb'),
            'seriEntitas' => 2,
            'tanggalIjinEntitas' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb')), "Y-m-d"),
        ];

        $payload->entitas[2] = [
            'alamatEntitas' => $this->request->getVar('entitas_alamat_penerima_barang'),
            'kodeEntitas' => "8",
            'kodeJenisApi' => "02",
            'kodeJenisIdentitas' => "5",
            'kodeStatus' => '3',
            'namaEntitas' => $this->request->getVar('entitas_nama_penerima_barang'),
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_penerima_barang'),
            'seriEntitas' => 3,
        ];

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);
        return response()->setJSON([
            'status' => true,
            'message' => "Entitas berhasil disimpan"
        ]);
    }

    public function dokumen($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->find($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc41['payload']);

        if (!is_array($payload->dokumen)) {
            $payload->dokumen = [];
            $this->bc41Model->update($id, ['payload' => json_encode($payload)]);
        }
        $dokumen = [];
        foreach (json_decode($this->bc41Model->find($id)['payload'])->dokumen as $d) {
            $dokumenDetail = $this->metaDataModel->where('name', "Dokumen")->where('description', $d->kodeDokumen)->first();
            $value = ($dokumenDetail == null) ? "" : $dokumenDetail['value'];
            array_push($dokumen, [
                'seriDokumen' => $d->seriDokumen,
                'kodeDokumen' => $d->kodeDokumen . " - " . $value,
                'nomorDokumen' => $d->nomorDokumen,
                'tanggalDokumen' => date('d/m/Y', strtotime($d->tanggalDokumen))
            ]);
        }

        $data = [
            'bc41' => $bc41,
            'kodeDokumen' => $this->metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'dokumen' => $dokumen,
        ];

        return view('BeaCukai/BC-41/form-dokumen', $data);
    }

    public function updateDokumen()
    {
        // INVOICE = 30
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc41Model->find($id)['payload']);
        $indexLast = count($payload->dokumen) == 0 ? 0 : count($payload->dokumen) - 1;
        $seriDokumen = count($payload->dokumen) == 0 ? 1 : $payload->dokumen[$indexLast]->seriDokumen + 1;

        $kodeDokumen = $this->request->getVar('dokumen_jenis_dokumen');

        array_push($payload->dokumen, [
            'idDokumen' =>  generateUniqueCode(5),
            'kodeDokumen' => $kodeDokumen,
            'nomorDokumen' => $this->request->getVar('dokumen_nomor_dokumen'),
            'seriDokumen' => $seriDokumen,
            'tanggalDokumen' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('dokumen_tanggal')), "Y-m-d"),
        ]);

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil disimpan"
        ]);
    }

    public function deleteDokumen()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc41Model->find($id)['payload']);

        unset($payload->dokumen[$indexDelete]);
        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function pengangkut($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->find($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc41['payload']);

        $data = [
            'pengangkut' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'bc41' => $bc41,
            'payload' => $payload,
        ];

        return view('BeaCukai/BC-41/form-pengangkut', $data);
    }

    public function pengangkutUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc41Model->find($id)['payload']);

        $payload->pengangkut[0] = [
            'idPengangkut' => random_int(1, 10),
            'namaPengangkut' => $this->request->getVar('pengangkut_nama_pengangkut'),
            'nomorPengangkut' => $this->request->getVar('pengangkut_nomor_pengangkut'),
            'seriPengangkut' => (string)1
        ];

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengangkut berhasil disimpan"
        ]);
    }

    public function kemasanPetiKemas($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->find($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc41['payload']);

        $indexLastKemasan = count($payload->kemasan) == 0 ? 0 : count($payload->kemasan) - 1;
        $indexLastKontainer = count($payload->kontainer) == 0 ? 0 : count($payload->kontainer) - 1;
        $seriKemasan = count($payload->kemasan) == 0 ? 1 : $payload->kemasan[$indexLastKemasan]->seriKemasan + 1;
        $seriKontainer = count($payload->kontainer) == 0 ? 1 : $payload->kontainer[$indexLastKontainer]->seriKontainer + 1;

        $dataKemasan = [];
        $dataKontainer = [];

        // KEMASAN
        foreach ($payload->kemasan as $k) {
            $jenisKemasanDetail = $this->metaDataModel->where('name', 'Jenis Kemasan')->where('description', $k->kodeJenisKemasan)->first();

            $kemasanDetail = $this->barangMasterSpesifikasiModel->select("
                CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang, 

            ")
                ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
                ->where('barang_master_spesifikasi.id', $k->kemasanInventoriId)
                ->first();

            array_push($dataKemasan, [
                'jumlahKemasan' => $k->jumlahKemasan,
                'kodeJenisKemasan' => $k->kodeJenisKemasan . " - " . strtoupper($jenisKemasanDetail['value']),
                'merkKemasan' => $k->merkKemasan,
                'seriKemasan' => $k->seriKemasan,
                'namaKemasanInventori' => $kemasanDetail == null ? "-" : $kemasanDetail['barang']
            ]);
        }

        // KONTAINER
        foreach ($payload->kontainer as $k) {
            $jenisKontainerDetail = $this->metaDataModel->where('name', "Jenis Kontainer")->where('description', $k->kodeJenisKontainer)->first();
            $tipeKontainerDetail = $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->where('value', $k->kodeTipeKontainer)->first();
            $ukuranKontainerDetail = $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->where('value', $k->kodeUkuranKontainer)->first();

            array_push($dataKontainer, [
                'kodeJenisKontainer' => $k->kodeJenisKontainer . " - " .  $jenisKontainerDetail['value'],
                'kodeTipeKontainer' => $k->kodeTipeKontainer . " - " .  $tipeKontainerDetail['description'],
                'kodeUkuranKontainer' => $k->kodeUkuranKontainer . " - " . $ukuranKontainerDetail['description'],
                'nomorKontainer' => $k->nomorKontainer,
                'seriKontainer' => $k->seriKontainer
            ]);
        }

        $data = [
            'bc41' => $bc41,
            'payload' => $payload,
            'seriKemasan' => $seriKemasan,
            'seriKontainer' => $seriKontainer,
            // 'dropdownKemasan' => $this->bc25Model->dropdownKemasan($bc41['sales_order_lain_id']),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $this->metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'dataKemasan' => $dataKemasan,
            'dataKontainer' => $dataKontainer,
        ];

        return view('BeaCukai/BC-41/form-kemasan-peti-kemas', $data);
    }

    public function kemasanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc41 = $this->bc41Model->find($id);
        $payload = json_decode($bc41['payload']);

        array_push($payload->kemasan, [
            'jumlahKemasan' => (float)$this->request->getVar('kemasan_jumlah_kemasan'),
            'kodeJenisKemasan' => $this->request->getVar('kemasan_jenis_kemasan'),
            'merkKemasan' => $this->request->getVar('kemasan_merk_kemasan'),
            'seriKemasan' => (int)$this->request->getVar('kemasan_seri_kemasan'),
            'kemasanInventoriId' => $this->request->getVar('kemasan_inventori_id'),
        ]);

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kemasan berhasil disimpan"
        ]);
    }

    public function deleteKemasan()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc41Model->find($id)['payload']);

        unset($payload->kemasan[$indexDelete]);
        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function kontainerUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc41 = $this->bc41Model->find($id);
        $payload = json_decode($bc41['payload']);

        array_push($payload->kontainer, [
            'kodeJenisKontainer' => $this->request->getVar('kontainer_jenis'),
            'kodeTipeKontainer' => $this->request->getVar('kontainer_tipe'),
            'kodeUkuranKontainer' => $this->request->getVar('kontainer_ukuran'),
            'nomorKontainer' => $this->request->getVar('kontainer_nomor'),
            'seriKontainer' => (int)$this->request->getVar('kontainer_seri'),
        ]);

        $payload->jumlahKontainer = count($payload->kontainer);
        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil disimpan"
        ]);
    }

    public function deleteKontainer()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc41Model->find($id)['payload']);

        unset($payload->kontainer[$indexDelete]);
        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil dihapus"
        ]);
    }

    public function transaksi($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->find($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc41['payload']);

        $nilaiJasaTotal = 0;
        $hargaPerolehanTotal = 0;

        for ($i = 0; $i < count($payload->barang); $i++) {
            $nilaiJasaTotal += $payload->barang[$i]->nilaiJasa;
            $hargaPerolehanTotal += $payload->barang[$i]->hargaPerolehan;
        }

        $data = [
            'bc41' => $bc41,
            'payload' => $payload,
            'nilaiJasaTotal' => $nilaiJasaTotal,
            'hargaPerolehanTotal' => $hargaPerolehanTotal,
        ];

        return view('BeaCukai/BC-41/form-transaksi', $data);
    }

    public function transaksiUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc41 = $this->bc41Model->find($id);
        $payload = json_decode($bc41['payload']);
        $totalBarang = count($this->bc41Model->barang($bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id'], $bc41['sales_order_id']));

        if ($totalBarang != count($payload->barang)) {
            return response()->setJSON([
                'message' => "Lengkapi data pada tab barang terlebih dahulu",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $hargaPerolehan = (float)convertRupiahToNumber($this->request->getVar('hargaPerolehan')) / $totalBarang;
        $uangMuka = (float)convertRupiahToNumber($this->request->getVar('uangMuka')) / $totalBarang;

        $payload->hargaPenyerahan = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
        $payload->volume = (float)convertRupiahToNumber($this->request->getVar('volume'));
        $payload->bruto = (float)convertRupiahToNumber($this->request->getVar('bruto'));
        $payload->netto = (float)convertRupiahToNumber($this->request->getVar('netto'));
        $payload->uangMuka = (float)convertRupiahToNumber($this->request->getVar('uangMuka'));

        // UPDATE NILAI JASA DAN HARGA PEROLEHAN
        for ($i = 0; $i < count($payload->barang); $i++) {
            $payload->barang[$i]->uangMuka = $uangMuka;
            $payload->barang[$i]->hargaPerolehan = $hargaPerolehan;
        }

        // return \response()->setJSON($payload);

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Transaksi berhasil diupdate"
        ]);
    }

    public function barang($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->find($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc41['payload']);

        $data = [
            'bc41' => $bc41,
            'payload' => $payload,
            'barang' => $this->bc41Model->barang($bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id'], $bc41['sales_order_id'])
        ];

        return view('BeaCukai/BC-41/form-barang', $data);
    }

    public function barangDetail($id, $kodeBarang)
    {
        $id = decrypt($id);
        $kodeBarang = decrypt($kodeBarang);
        $bc41 = $this->bc41Model->find($id);
        $payload = json_decode($this->bc41Model->find($id)['payload']);
        $detailBarang = $this->bc41Model->detailBarang($bc41['id'], $kodeBarang, $bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id'], $bc41['sales_order_id']);
        $totalBarang = count($this->bc41Model->barang($bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id'], $bc41['sales_order_id']));

        $this->setFlashDataNavigatorSession($id);

        if ($detailBarang['bcDetail'] == null) {
            // MASIH KOSONG
            $indexLast = count($payload->barang) == 0 ? 0 : count($payload->barang) - 1;
            $seriBarang = count($payload->barang) == 0 ? 1 : $payload->barang[$indexLast]->seriBarang + 1;

            $ndpbm = $payload->ndpbm / $totalBarang;
            $cif = $payload->cif / $totalBarang;

            array_push($payload->barang, [
                'cif' => $cif,
                'hargaEkspor' => 0,
                'hargaPenyerahan' => 0,
                'isiPerKemasan' => 0,
                'jumlahKemasan' => 0,
                'jumlahSatuan' => (float)$detailBarang['barangDetail']['qty_konversi'],
                'kodeBarang' => $detailBarang['barangDetail']['kode_barang'],
                'kodeDokumen' => "41",
                'kodeJenisKemasan' => "",
                'kodeSatuanBarang' => "",
                'merk' => "",
                'netto' => 0,
                'nilaiBarang' => 0,
                'posTarif' => "",
                'seriBarang' => $seriBarang,
                'spesifikasiLain' => "",
                'tipe' => "",
                'ukuran' => "-",
                'uraian' => "",
                'volume' => 0,
                'cifRupiah' => $cif,
                'hargaPerolehan' => 0,
                'kodeAsalBahanBaku' => "1",
                'ndpbm' => $ndpbm,
                'uangMuka' => 0,
                'nilaiJasa' => 0,
                "bahanBaku" => []
            ]);

            $this->bc41Model->update($id, ['payload' => json_encode($payload)]);
        }
        $data = [
            'bc41' => $bc41,
            'barang' => $this->bc41Model->detailBarang($bc41['id'], $kodeBarang, $bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id'], $bc41['sales_order_id']),
            'kodeHS' => $this->hsCodeModel->findAll(),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', "Jenis Kemasan")->findAll(),
            'kodeSatuanBarang' => null,
            'bahanBakuLokal' => [],
        ];

        $data['kodeSatuanBarang'] = $this->metaDataModel->where('name', "Kode Satuan BC")->where('value', $data['barang']['bcDetail']->kodeSatuanBarang ?? "")->findAll();

        // APPEND BAHAN BAKU LOKAL & EKSPOR
        foreach ($data['barang']['bcDetail']->bahanBaku ?? [] as $i => $b) {
            // LOKAL
            array_push($data['bahanBakuLokal'], [
                'indexDelete' => $i,
                'seriBarang' => $b->seriBarang,
                'posTarif' => $b->posTarif,
                'uraianBarang' => $b->uraianBarang,
                'hargaPenyerahan' => $b->hargaPenyerahan,
                'kodeSatuanBarang' => $b->kodeSatuanBarang
            ]);
        }

        return view('BeaCukai/BC-41/form-detail-barang', $data);
    }

    public function barangDetailUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc41Model->find($id)['payload']);
        $seriBarang = $this->request->getVar('seriBarang');

        $hargaPenyerahanTotal = 0;
        $volumeTotal = 0;
        $nettoTotal = 0;

        for ($i = 0; $i < count($payload->barang); $i++) {
            if ($payload->barang[$i]->seriBarang == $seriBarang) {
                $payload->barang[$i]->posTarif = $this->request->getVar('posTarif');
                $payload->barang[$i]->kodeBarang = $this->request->getVar('kodeBarang');
                $payload->barang[$i]->uraian = $this->request->getVar('uraian');
                $payload->barang[$i]->merk = $this->request->getVar('merk');
                $payload->barang[$i]->tipe = $this->request->getVar('tipe');
                $payload->barang[$i]->spesifikasiLain = $this->request->getVar('spesifikasiLain');
                $payload->barang[$i]->kodeSatuanBarang = (string)decrypt($this->request->getVar('kodeSatuanBarang'));
                $payload->barang[$i]->jumlahKemasan = (float)$this->request->getVar('jumlahKemasan');
                $payload->barang[$i]->kodeJenisKemasan = $this->request->getVar('kodeJenisKemasan');
                $payload->barang[$i]->netto = (float)$this->request->getVar('netto');
                $payload->barang[$i]->volume = (float)$this->request->getVar('volume');
                $payload->barang[$i]->hargaPenyerahan = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
                $payload->barang[$i]->nilaiJasa = (float)convertRupiahToNumber($this->request->getVar('nilaiJasa'));
            }
            $hargaPenyerahanTotal += $payload->barang[$i]->hargaPenyerahan;
            $volumeTotal += $payload->barang[$i]->volume;
            $nettoTotal += $payload->barang[$i]->netto;
        }

        $payload->hargaPenyerahan = $hargaPenyerahanTotal;
        $payload->volume = $volumeTotal;
        $payload->netto = $nettoTotal;

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil update detail barang"
        ]);
    }

    public function bahanBakuUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $bcPurchaseOrderId = $this->request->getVar('bcPurchaseOrderId');
        $bahanBakuBCList = json_decode($this->request->getVar('bahanBakuBCList'));
        $payload = json_decode($this->bc41Model->find($id)['payload']);
        $indexBarang = 0;
        $seriBahanBakuLast = 1;

        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        if (count($payload->barang[$indexBarang]->bahanBaku) != 0) {
            $seriBahanBakuLast = count($payload->barang[$indexBarang]->bahanBaku) + 1;
        }

        $bcPurchaseOrder = $this->bcPurchaseOrderModel->find($bcPurchaseOrderId);

        foreach ($bahanBakuBCList->barang as $b) {
            array_push($payload->barang[$indexBarang]->bahanBaku, [
                'cif' => $b->cif,
                'cifRupiah' => $b->cifRupiah,
                'hargaPenyerahan' => $b->hargaPenyerahan,
                'hargaPerolehan' => 0,
                'jumlahSatuan' => $b->jumlahSatuan,
                'kodeSatuanBarang' => $b->kodeSatuanBarang,
                'kodeAsalBahanBaku' => $b->kodeAsalBahanBaku,
                'kodeBarang' => $b->kodeBarang,
                'kodeDokAsal' => $bahanBakuBCList->kodeDokumen,
                'kodeDokumen' => $bahanBakuBCList->kodeDokumen,
                'kodeKantor' => $bahanBakuBCList->kodeKantor,
                'merkBarang' => $b->merk,
                'ndpbm' => $b->ndpbm,
                'netto' => $b->netto,
                'nomorAjuDokAsal' => $bahanBakuBCList->nomorAju,
                'nomorDaftarDokAsal' => $bcPurchaseOrder['no_daftar'],
                'posTarif' => $b->posTarif,
                'seriBahanBaku' => $seriBahanBakuLast,
                'seriBarang' => $seriBahanBakuLast,
                'seriBarangDokAsal' => $b->seriBarang,
                'seriIjin' => 0,
                'spesifikasiLainBarang' => $b->spesifikasiLain,
                'tanggalDaftarDokAsal' => $bahanBakuBCList->tanggalTtd,
                'tipeBarang' => $b->tipe,
                'ukuranBarang' => $b->ukuran,
                'uraianBarang' => $b->uraian,
                'nilaiJasa' => $b->nilaiJasa
            ]);

            $seriBahanBakuLast++;
        }

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Bahan baku berhasil disimpan",
            'status' => true,
        ]);
    }

    public function bahanBakuDelete()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $indexDelete = $this->request->getVar('indexDelete');
        $payload = json_decode($this->bc41Model->find($id)['payload']);

        $indexBarang = 0;

        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        unset($payload->barang[$indexBarang]->bahanBaku[$indexDelete]);
        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Bahan baku berhasil dihapus",
            'status' => true,
        ]);
    }


    public function pungutan($id)
    {
        $id = decrypt($id);

        $bc41 = $this->bc41Model->find($id);
        $this->setFlashDataNavigatorSession($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $data = [
            'bc41' => $bc41,
            'kodeLokasiBayar' => $this->metaDataModel->where('name', "KODE LOKASI BAYAR")->findAll(),
            'kodePembayar' => $this->metaDataModel->where('name', "ENTITAS")->findAll(),
            'payload' => json_decode($bc41['payload'])
        ];

        return view('BeaCukai/BC-41/form-pungutan', $data);
    }

    public function pungutanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc41Model->find($id)['payload']);

        $payload->kodeLokasiBayar = $this->request->getVar('kodeLokasiBayar');
        $payload->kodePembayar = $this->request->getVar('kodePembayar');
        $payload->nomorBuktiBayar = $this->request->getVar('nomorBuktiBayar');
        $payload->tanggalBuktiBayar = date_format(date_create_from_format("d/m/Y", $this->request->getVar('tanggalBuktiBayar')), "Y-m-d");

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Pungutan berhasil disimpan",
            'status' => true,
        ]);
    }

    public function pernyataan($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->find($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc41['payload']);

        $data = [
            'bc41' => $bc41,
            'payload' => $payload,
            'ceisaSetting' => $ceisaSetting
        ];

        return view('BeaCukai/BC-41/form-pernyataan', $data);
    }

    public function pernyataanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc41Model->find($id)['payload']);

        $payload->kotaTtd = $this->request->getVar('kotaTtd');
        $payload->tanggalTtd = date('Y-m-d', strtotime($this->request->getVar('tanggalTtd')));
        $payload->namaTtd = $this->request->getVar('namaTtd');
        $payload->jabatanTtd = $this->request->getVar('jabatanTtd');

        $this->bc41Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pernyataan berhasil diupdate"
        ]);
    }

    // KIRIM BC.23 KE CEISA
    public function kirimCeisa($id)
    {
        $id = decrypt($id);
        $payload = json_decode($this->bc41Model->find($id)['payload']);
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);

        for ($i = 0; $i < count($payload->kemasan); $i++) {
            unset($payload->kemasan[$i]->kemasanInventoriId);
        }

        unset($payload->nomorBuktiBayar);
        unset($payload->tanggalBuktiBayar);

        // TES HAPUS BAHAN BAKU 
        for ($i = 0; $i < count($payload->barang); $i++) {
            $payload->barang[$i]->bahanBaku = [];
        }
        $res = $beacukaiApi->kirimDokumenBC($payload, false);

        // return \response()->setJSON($payload);
        // die;
        if ($res['status'] == false) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal Kirim Ceisa Karena : " . $res['message'],
            ]);
        }
        // // UPDATE STATUS
        $this->bc41Model->set('status_dokumen', "Sudah Kirim")->where('id', $id)->update();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Diposting",
            'res' => $res
        ]);
    }

    private function setFlashDataNavigatorSession($id)
    {
        $isCompleteFormHeader = $this->bc41Model->isCompleteFormHeader($id);
        $isCompleteFormEntitas = $this->bc41Model->isCompleteFormEntitas($id);
        $isCompleteFormDokumen = $this->bc41Model->isCompleteFormDokumen($id);
        $isCompleteFormPengangkut = $this->bc41Model->isCompleteFormPengangkut($id);
        $isCompleteFormPetiKemas = $this->bc41Model->isCompleteFormPetiKemas($id);
        $isCompleteFormTransaksi = $this->bc41Model->isCompleteFormTransaksi($id);
        $isCompleteFormBarang = $this->bc41Model->isCompleteFormBarang($id);
        $isCompleteFormPungutan = $this->bc41Model->isCompleteFormPungutan($id);
        $isCompleteFormPernyataan = $this->bc41Model->isCompleteFormPernyataan($id);

        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormTransaksi);
        session()->setFlashdata('isCompleteFormBarang', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPungutan', $isCompleteFormPungutan);
        session()->setFlashdata('isCompleteFormPernyataan', $isCompleteFormPernyataan);

        if (
            $isCompleteFormPernyataan && $isCompleteFormHeader && $isCompleteFormEntitas &&
            $isCompleteFormEntitas && $isCompleteFormDokumen && $isCompleteFormPengangkut &&
            $isCompleteFormPetiKemas && $isCompleteFormTransaksi && $isCompleteFormBarang
        ) {
            $this->bc41Model->set('status_dokumen', "Siap Kirim")->where('id', $id)->update();
        } else {
            $this->bc41Model->set('status_dokumen', "Belum Lengkap")->where('id', $id)->update();
        }
    }

    public function get_payload()
    {
        $payloadArr = [
            "asalData"               => "S",
            "asuransi"               => 0,
            "bruto"                  => 0,
            "cif"                    => 0,
            "dasarPengenaanPajak"    => 0,
            "kodeJenisTpb"           => "",
            "kodeJenisImpor"         => "",
            "kodeJenisEkspor"        => "",
            "freight"                => 0,
            "hargaPenyerahan"        => 0,
            "jabatanTtd"             => "",
            "jumlahKontainer"        => 0,
            "kodeDokumen"            => "41",
            "kodeKantor"             => "",
            "kodeLokasiBayar"        => "",
            "kodePembayar"           => "",
            "kodeTujuanPengiriman"   => "",
            "kotaTtd"                => "",
            "namaTtd"                => "",
            "ndpbm"                  => 0,
            "netto"                  => 0,
            "nik"                    => "",
            "nilaiBarang"            => 0,
            "nomorAju"               => "",
            "seri"                   => 1,
            "tanggalAju"             => "",
            "tanggalTtd"             => "",
            "volume"                 => 0,
            "biayaTambahan"          => 0,
            "biayaPengurang"         => 0,
            "vd"                     => 0,
            "uangMuka"               => 0,
            "ppnPajak"               => 0,
            "ppnbmPajak"            => 0,
            "tarifPpnPajak"          => 0,
            "tarifPpnbmPajak"        => 0,
            "entitas"                => [],
            "dokumen"                => [],
            "pengangkut"             => [],
            "kontainer"              => [],
            "kemasan"                => [],
            "barang"                 => []
        ];

        return json_encode($payloadArr, JSON_UNESCAPED_SLASHES);
    }
}
