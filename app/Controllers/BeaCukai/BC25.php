<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC25Model;
use App\Models\BC41Model;
use App\Models\BcPengeluaranBarangModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\CeisaSettingModel;
use App\Models\CountryModel;
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
use App\Models\StockDetail2Model;
use App\Models\SalesOrderLainDetailModel;
use App\Models\SalesOrderLainModel;
use App\Models\SalesOrderModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampDetailModel;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54

class BC25 extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $bc25Model;
    protected $metaDataModel;
    protected $salesOrderLainModel;
    protected $salesOrderLainDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $kantorBeaCukaiModel;
    protected $countryModel;
    protected $pengusahaTPBModel;
    protected $barangMasterSpesifikasiModel;
    protected $hsCodeModel;
    protected $bcPurchaseOrderModel;
    protected $bc40Controller;
    protected $bc23Controller;
    protected $pengembalianBarangModel;
    protected $penerimaanBarangModel;
    protected $pengembalianBarangDetailModel;
    protected $salesOrderModel;
    protected $salesOrderDetailModel;
    protected $bc41Model;
    protected $bcPengeluaranBarangModel;
    protected $customerModel;
    protected $divisiModel;
    protected $satuanModel;
    protected $stockRevampDetailModel;

    public function __construct()
    {
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->bc25Model = new BC25Model();
        $this->metaDataModel = new MetadataModel();
        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $this->countryModel = new CountryModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->hsCodeModel = new HsCodesModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $this->bc23Controller = new BC23();
        $this->bc40Controller = new BC40();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
        $this->salesOrderModel = new SalesOrderModel();
        $this->salesOrderDetailModel = new SalesOrderDetailModel();
        $this->bc41Model = new BC41Model();
        $this->bcPengeluaranBarangModel = new BcPengeluaranBarangModel();
        $this->divisiModel = new DivisisModel();
        $this->customerModel = new CustomerModel();
        $this->satuanModel = new SatuansModel();
        $this->stockRevampDetailModel = new StockRevampDetailModel();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first(),
        ];

        return view('BeaCukai/bc-25/index', $data);
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

        $data = $this->bc25Model->getList(
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


    public function detail($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->where('id', $id)->first();
        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description !=', "kemasan")
            ->findAll();

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $divisi = $this->divisiModel->getDivisiAccess();
        $satuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->where('deletedAt', null)->findAll();
        $dataCustomer = [];
        $dataReferencePengeluaran = [];

        if ($bc25['reference_penerima_id'] != null) {
            $dataCustomer = $this->customerModel->where('id', $bc25['reference_penerima_id'])->findAll();
        }

        if ($bc25['multiple_reference_id'] != null) {
            $dataReferencePengeluaran = $this->salesOrderModel->whereIn('id', json_decode($bc25['multiple_reference_id']))->where('deletedAt', null)->findAll();
        }

        $dataBarang = $this->bcPengeluaranBarangModel->getDetailBarang(
            $id,
            "BC 2.5"
        );

        $data = [
            'bc25' => $bc25,
            'tipeBarang' => $dataTipeBarang,
            'divisi' => $divisi,
            'dataSatuan' => $satuan,
            'noAju' => $bc25 == null ? $this->generateNomorAju($bc25['tanggal']) : $bc25['no_aju'],
            'dataValuta' => $dataValuta,
            'dataCustomer' => $dataCustomer,
            'dataReferencePengeluaran' => $dataReferencePengeluaran,
            'dataBarang' => $dataBarang
        ];

        return view('BeaCukai/bc-25/form', $data);
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


            $this->bc25Model->update($id, [
                'tanggal' => $tanggal,
                'reference_penerima_id' => $referencePenerimaId,
                'no_aju' => $noAju,
                'no_daftar' => $noDaftar,
                'multiple_reference_id' => $multipleReferenceId,
                'multiple_reference_no' => $multipleReferenceNo,
                'jenis_pengeluaran' => $jenisPengeluaran,
            ]);

            $listStock = json_decode($this->request->getVar('listStock'));
            $this->bcPengeluaranBarangModel->where('bc_pengeluaran_id', $id)->where('tipe_bc', "BC 2.5")->delete(null, true);
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
                    'tipe_bc'            => "BC 2.5",
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

    public function getReferensiPengeluaran()
    {
        try {
            $jenisPengeluaran = $this->request->getVar('jenis_pengeluaran');
            $referencePenerimaId = $this->request->getVar('reference_penerima_id');
            $dataResult = [];
            if ($jenisPengeluaran == "ORDER FORM LOKAL") {
                $dataResult = $this->bcPengeluaranBarangModel->referensiPengeluaranOrderFormLokal(
                    $this->this_company_id,
                    $referencePenerimaId
                );
            }
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => $dataResult
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getReferensiPenerima()
    {
        try {
            $jenisPengeluaran = $this->request->getVar('jenis_pengeluaran');
            $dataPenerima = [];
            if ($jenisPengeluaran == "ORDER FORM LOKAL") {

                $id_company_arr = [];
                if (in_array($this->this_company_id, [1, 2, 15])) {
                    $id_company_arr = [1, 2, 15];
                } else {
                    $id_company_arr = [16];
                }

                $dataQry = $this->customerModel
                    ->select('customers.*,companies.company')
                    ->join('companies', 'companies.id = customers.company_id', 'left')
                    ->where('tipe_customer', "LOKAL")
                    ->where('customers.deletedAt', null)
                    ->whereIn('customers.company_id', $id_company_arr)
                    ->orderBy('customers.name', "asc")
                    ->findAll();

                foreach ($dataQry as $d) {
                    array_push($dataPenerima, [
                        'id' => $d['id'],
                        'name' => $d['name'] . " (" . $d['company'] . ")"
                    ]);
                }
            }

            return response()->setJSON([
                'status' => true,
                'data' => $dataPenerima,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
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

            $id = $this->bc25Model->insert([
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

    public function updateNoAju()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $noAju = $this->request->getVar('no_pengajuan');
            $this->bc25Model->update($id, ['no_aju' => $noAju]);
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "no aju berhasil diupdate"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }


    public function delete()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $this->bc25Model->delete($id);
            $this->bcPengeluaranBarangModel->where('bc_pengeluaran_id', $id)->where('tipe_bc', "2.5")->delete(null, false);
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Dokumen BC 2.5 Berhasil Dihapus"
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
        $this->bc25Model->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 2.5 Berhasil Diposting",
            'token' => csrf_hash()
        ]);
    }

    public function unposting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->bc25Model->update($id, ['status_posting' => '0']);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 2.5 Berhasil Diunposting",
            'token' => csrf_hash()
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

        $kodeDokumenbc25Static = $this->metaDataModel
            ->where('name', "Kode BC25 Static")
            ->first();

        $kodeKantorStatic = $ceisaSetting['kode_unik']
            ? $ceisaSetting['kode_unik']
            : $ceisaSetting['kode_kantor_pabean'];

        $tanggalAju = date('Ymd', strtotime($tanggalDokumen));
        $tahunAjuSekarang = date('Y', strtotime($tanggalDokumen));

        // Ambil BC40 terakhir berdasarkan urutan no_aju terbaru
        $bc25Last = $this->bc25Model
            ->whereIn('company_id', $company_id_arr)
            ->orderBy('tanggal', "DESC")
            ->limit(1)
            ->first();

        // Default urutan
        $sequenceNoUrutPengajuan = "000001";

        if ($bc25Last && $bc25Last['no_aju']) {
            $arrNo = explode('-', $bc25Last['no_aju']);

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

        return $kodeDokumenbc25Static['value']
            . '-' . $kodeKantorStatic
            . '-' . $tanggalAju
            . '-' . $sequenceNoUrutPengajuan;
    }

    // CEISA ROUTER
    public function header($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->where('id', $id)->first();
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $this->setFlashDataNavigatorSession($id);

        $data = [
            'bc25' => $bc25,
            'noAju' => $bc25 == null ? $this->generateNomorAju($bc25['tanggal']) : $bc25['no_aju'],
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'kodeLokasiBayar' => $this->metaDataModel->where('name', "KODE LOKASI BAYAR")->findAll(),
            'kodeTujuanTpb' => $this->metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTujuanPengiriman' => $this->metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', 40)->where('deletedAt', null)->findAll(),
            'kodeCaraBayar' => $this->metaDataModel->where('name', "CARA BAYAR")->findAll(),
            'selectedKantor' => $ceisaSetting['kode_kantor_pabean'],
            'payload' => json_decode($bc25['payload'])
        ];

        return view('BeaCukai/bc-25/form-header', $data);
    }

    public function updateHeader()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $bc25 = $this->bc25Model->where('id', $id)->first();
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $noDaftar = $this->request->getVar('no_daftar');

            $payload = json_decode($bc25['payload']);
            $payload->nomorAju =  $this->request->getVar('header_no_pengajuan');
            $payload->tanggalAju = $tanggal;
            $payload->kodeCaraBayar = $this->request->getVar('header_kode_cara_bayar');
            $payload->kodeKantor = $this->request->getVar('header_kantor_pabean');
            $payload->kodeTujuanPengiriman = $this->request->getVar('header_kode_tujuan_pengiriman');
            $payload->kodeJenisTpb = $this->request->getVar('header_kode_jenis_tpb');
            $payload->kodeLokasiBayar = $this->request->getVar('header_kode_lokasi_bayar');

            $payloadUpdate = json_encode($payload);
            $this->bc25Model->update($id, [
                'tanggal' => $tanggal,
                'payload' => $payloadUpdate,
                'no_daftar' => $noDaftar
            ]);
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "header diupdate"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Header berhasil disimpan"
        ]);
    }

    public function entitas($id)
    {
        $id = decrypt($id);

        $bc25 = $this->bc25Model->find($id);
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->findAll();

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $this->setFlashDataNavigatorSession($id);

        $payload = json_decode($bc25['payload']);
        // JIKA MASIH KOSONG SET DULU BOSQ
        if (!is_object($payload->entitas)) {
            if (count($payload->entitas) == 0) {
                $payload->entitas = [
                    [
                        'alamatEntitas' => "",
                        'kodeEntitas' => "3",
                        'kodeJenisApi' => "2",
                        'kodeJenisIdentitas' => "5",
                        'kodeStatus' => '3',
                        'namaEntitas' => "",
                        'nibEntitas' => "",
                        'nomorIdentitas' => "", // nitku (22 digit) npwp 16 digit
                        'nomorIjinEntitas' => "",
                        'tanggalIjinEntitas' => "",
                        'seriEntitas' => 1,
                    ],
                    [
                        'alamatEntitas' => "",
                        'kodeEntitas' => "7",
                        'kodeJenisIdentitas' => "5",
                        'kodeStatus' => '3',
                        'namaEntitas' => "", // nitku
                        'nomorIdentitas' => "",
                        'seriEntitas' => 2,
                    ],
                    [
                        'alamatEntitas' => "",
                        'kodeEntitas' => "3",
                        'kodeJenisApi' => "2",
                        'kodeJenisIdentitas' => "5",
                        'kodeStatus' => '3',
                        'namaEntitas' => "", // nitku
                        'niperEntitas' => "",
                        'nomorIdentitas' => "",
                        'seriEntitas' => 3,
                    ]
                ];

                $this->bc25Model->update($id, ['payload' => json_encode($payload)]);
            }
        }

        $data = [
            'bc25' => $bc25,
            'pengusahaTPB' => $pengusahaTPB,
            'payload' => json_decode($this->bc25Model->find($id)['payload'])
        ];

        return view('BeaCukai/bc-25/form-entitas', $data);
    }

    public function updateEntitas()
    {
        try {

            $id = decrypt($this->request->getVar('id'));
            $bc25 = $this->bc25Model->find($id);
            $payload = json_decode($bc25['payload']);

            $payload->entitas[0] = [
                'alamatEntitas' => $this->request->getVar('entitas_alamat_pengusaha'),
                'kodeEntitas' => "3",
                'kodeJenisApi' => "2",
                'kodeJenisIdentitas' => "5",
                'kodeStatus' => '3',
                'namaEntitas' => $this->request->getVar('entitas_nama_pengusaha'),
                'nibEntitas' => $this->request->getVar('entitas_nib'),
                'nomorIdentitas' => $this->request->getVar('entitas_nitku'),
                'nomorIjinEntitas' => $this->request->getVar('entitas_nomor_ijin_tpb'),
                'tanggalIjinEntitas' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb')), "Y-m-d"),
                'seriEntitas' => 1,
            ];

            $payload->entitas[1] = [
                'alamatEntitas' => $this->request->getVar('entitas_alamat_pemilik_barang'),
                'kodeEntitas' => "7",
                'kodeJenisIdentitas' => "5",
                'kodeStatus' => '3',
                'namaEntitas' => $this->request->getVar('entitas_nama_pemilik_barang'),
                'nomorIdentitas' => $this->request->getVar('entitas_nitku_pemilik_barang'),
                'seriEntitas' => 2,
            ];

            $payload->entitas[2] = [
                'alamatEntitas' => $this->request->getVar('entitas_alamat_penerima_barang'),
                'kodeEntitas' => "8",
                'kodeJenisApi' => "2",
                'kodeJenisIdentitas' => "5",
                'kodeStatus' => '3',
                'namaEntitas' => $this->request->getVar('entitas_nama_penerima_barang'),
                'niperEntitas' => $this->request->getVar('entitas_niper_penerima_barang'),
                'nomorIdentitas' => $this->request->getVar('entitas_nitku_penerima_barang'),
                'seriEntitas' => 3,
            ];

            $this->bc25Model->update($id, ['payload' => json_encode($payload)]);
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Entitas berhasil disimpan"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function dokumen($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->find($id);

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc25['payload']);

        if (!is_array($payload->dokumen)) {
            $payload->dokumen = [];
            $this->bc25Model->update($id, ['payload' => json_encode($payload)]);
        }
        $dokumen = [];
        foreach (json_decode($this->bc25Model->find($id)['payload'])->dokumen as $d) {
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
            'bc25' => $bc25,
            'kodeDokumen' => $this->metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'dokumen' => $dokumen,
        ];

        return view('BeaCukai/bc-25/form-dokumen', $data);
    }

    public function updateDokumen()
    {
        // INVOICE = 30
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc25Model->find($id)['payload']);
        $indexLast = count($payload->dokumen) == 0 ? 0 : count($payload->dokumen) - 1;
        $seriDokumen = count($payload->dokumen) == 0 ? 1 : $payload->dokumen[$indexLast]->seriDokumen + 1;

        $kodeDokumen = $this->request->getVar('dokumen_jenis_dokumen');
        if ($seriDokumen == 1) {
            // HARUS INVOICE
            if ($kodeDokumen != 380) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Dokumen seri pertama wajib invoice ",
                ]);
            }
        }

        array_push($payload->dokumen, [
            'idDokumen' =>  generateUniqueCode(5),
            'kodeDokumen' => $kodeDokumen,
            'nomorDokumen' => $this->request->getVar('dokumen_nomor_dokumen'),
            'seriDokumen' => $seriDokumen,
            'tanggalDokumen' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('dokumen_tanggal')), "Y-m-d"),
        ]);

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil disimpan"
        ]);
    }

    public function deleteDokumen()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc25Model->find($id)['payload']);

        unset($payload->dokumen[$indexDelete]);
        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function pengangkut($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->find($id);

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc25['payload']);

        $data = [
            'pengangkut' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'bc25' => $bc25,
            'payload' => $payload,
        ];

        return view('BeaCukai/bc-25/form-pengangkut', $data);
    }

    public function pengangkutUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc25Model->find($id)['payload']);

        $payload->pengangkut[0] = [
            'namaPengangkut' => $this->request->getVar('pengangkut_nama_pengangkut'),
            'nomorPengangkut' => $this->request->getVar('pengangkut_nomor_pengangkut'),
            'kodeCaraAngkut' => $this->request->getVar('pengangkut_kode_cara_angkut'),
            'seriPengangkut' => 1
        ];

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengangkut berhasil disimpan"
        ]);
    }

    public function kemasanPetiKemas($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->find($id);

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc25['payload']);

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
            'bc25' => $bc25,
            'payload' => $payload,
            'seriKemasan' => $seriKemasan,
            'seriKontainer' => $seriKontainer,
            'dropdownKemasan' => $this->bc25Model->dropdownKemasan($bc25['sales_order_lain_id']),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $this->metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'dataKemasan' => $dataKemasan,
            'dataKontainer' => $dataKontainer,
        ];

        return view('BeaCukai/bc-25/form-kemasan-peti-kemas', $data);
    }

    public function kemasanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc25 = $this->bc25Model->find($id);
        $payload = json_decode($bc25['payload']);

        array_push($payload->kemasan, [
            'jumlahKemasan' => (float)$this->request->getVar('kemasan_jumlah_kemasan'),
            'kodeJenisKemasan' => $this->request->getVar('kemasan_jenis_kemasan'),
            'merkKemasan' => $this->request->getVar('kemasan_merk_kemasan'),
            'seriKemasan' => (int)$this->request->getVar('kemasan_seri_kemasan'),
            'kemasanInventoriId' => $this->request->getVar('kemasan_inventori_id'),
        ]);

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kemasan berhasil disimpan"
        ]);
    }

    public function deleteKemasan()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc25Model->find($id)['payload']);

        unset($payload->kemasan[$indexDelete]);
        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function kontainerUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc25 = $this->bc25Model->find($id);
        $payload = json_decode($bc25['payload']);

        array_push($payload->kontainer, [
            'kodeJenisKontainer' => $this->request->getVar('kontainer_jenis'),
            'kodeTipeKontainer' => $this->request->getVar('kontainer_tipe'),
            'kodeUkuranKontainer' => $this->request->getVar('kontainer_ukuran'),
            'nomorKontainer' => $this->request->getVar('kontainer_nomor'),
            'seriKontainer' => (int)$this->request->getVar('kontainer_seri'),
        ]);

        $payload->jumlahKontainer = count($payload->kontainer);
        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil disimpan"
        ]);
    }

    public function deleteKontainer()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc25Model->find($id)['payload']);

        unset($payload->kontainer[$indexDelete]);
        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil dihapus"
        ]);
    }

    public function transaksi($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->find($id);

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc25['payload']);

        if ($payload->ndpbm == "") {
            $payload->ndpbm = 1;
        }

        $data = [
            'kodeValuta' => $this->metaDataModel->where('name', "Valuta")->findAll(),
            'bc25' => $bc25,
            'payload' => $payload,
        ];

        return view('BeaCukai/bc-25/form-transaksi', $data);
    }

    public function transaksiUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc25Model->find($id)['payload']);

        $payload->kodeValuta = $this->request->getVar('harga_kode_valuta');
        $payload->ndpbm = (float)convertRupiahToNumber($this->request->getVar('harga_ndpbm'));
        $payload->cif = (float)convertRupiahToNumber($this->request->getVar('harga_cif'));
        $payload->hargaPenyerahan = (float)convertRupiahToNumber($this->request->getVar('harga_nilai_penyerahan'));
        $payload->dasarPengenaanPajak = (float)convertRupiahToNumber($this->request->getVar('pajak_dasar_pengenaan_pajak'));
        $payload->ppnPajak = (float)($this->request->getVar('pajak_ppn_pajak'));
        $payload->tarifPpnPajak = (float)convertRupiahToNumber($this->request->getVar('pajak_tarif_ppn_pajak'));
        $payload->ppnbmPajak = (float)($this->request->getVar('pajak_ppnbm_pajak'));
        $payload->tarifPpnbmPajak = (float)convertRupiahToNumber($this->request->getVar('pajak_tarif_ppnbm_pajak'));
        $payload->bruto = (float)convertRupiahToNumber($this->request->getVar('berat_bruto'));
        $payload->netto = (float)convertRupiahToNumber($this->request->getVar('berat_netto'));

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Transaksi berhasil diupdate"
        ]);
    }

    public function barang($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->find($id);

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc25['payload']);

        $data = [
            'bc25' => $bc25,
            'payload' => $payload,
            'barang' => $this->bc25Model->barang($bc25['sales_order_lain_id'], $bc25['pengembalian_barang_id'], $bc25['sales_order_id'])
        ];

        return view('BeaCukai/bc-25/form-barang', $data);
    }

    public function barangDetail($id, $kodeBarang)
    {
        $id = decrypt($id);
        $kodeBarang = decrypt($kodeBarang);
        $bc25 = $this->bc25Model->find($id);
        $payload = json_decode($this->bc25Model->find($id)['payload']);
        $detailBarang = $this->bc25Model->detailBarang($bc25['id'], $kodeBarang, $bc25['sales_order_lain_id'], $bc25['pengembalian_barang_id'], $bc25['sales_order_id']);
        $totalBarang = count($this->bc25Model->barang($bc25['sales_order_lain_id'], $bc25['pengembalian_barang_id'], $bc25['sales_order_id']));

        $this->setFlashDataNavigatorSession($id);

        if ($detailBarang['bcDetail'] == null) {
            // MASIH KOSONG
            $indexLast = count($payload->barang) == 0 ? 0 : count($payload->barang) - 1;
            $seriBarang = count($payload->barang) == 0 ? 1 : $payload->barang[$indexLast]->seriBarang + 1;

            $ndpbm = $payload->ndpbm ?? 0 / $totalBarang;
            $cif = $payload->cif  ?? 0 / $totalBarang;
            $bruto = $payload->bruto ?? 0 / $totalBarang;

            array_push($payload->barang, [
                'bruto' => $bruto,
                'cif' => $cif,
                'diskon' => 0,
                'fob' => 0,
                'freight' => 0,
                'hargaEkspor' => 0,
                'hargaPenyerahan' => 0,
                'isiPerKemasan' => 0,
                'jumlahKemasan' => 0,
                'jumlahSatuan' => (float)$detailBarang['barangDetail']['qty_konversi'],
                'kodeBarang' => $detailBarang['barangDetail']['kode_barang'],
                'kodeGunaBarang' => "",
                'kodeKategoriBarang' => "",
                'kodeJenisKemasan' => "",
                'kodeKondisiBarang' => "",
                'kodePerhitungan' => "",
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
                'ndpbm' => $ndpbm,
                'cifRupiah' => $cif, // GK TAU
                'hargaPerolehan' => 0,
                'kodeDokAsal' => "25",
                'flag4tahun' => "",
                "barangTarif" => [],
                "barangDokumen" => [],
                "bahanBaku" => []
            ]);

            $this->bc25Model->update($id, ['payload' => json_encode($payload)]);
        }
        $data = [
            'bc25' => $bc25,
            'barang' => $this->bc25Model->detailBarang($bc25['id'], $kodeBarang, $bc25['sales_order_lain_id'], $bc25['pengembalian_barang_id'], $bc25['sales_order_id']),
            'kodeHS' => $this->hsCodeModel->findAll(),
            'kodeGunaBarang' => $this->metaDataModel->where('name', "KODE GUNA BARANG")->findAll(),
            'kodeKategoriBarang' => $this->metaDataModel->where('name', "Kategori Barang BC")->like('description', '25')->findAll(),
            'kodeKondisiBarang' => $this->metaDataModel->where('name', "KONDISI BARANG")->findAll(),
            'kodePerhitungan' =>  $this->metaDataModel->where('name', "KODE PERHITUNGAN")->findAll(),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', "Jenis Kemasan")->findAll(),
            'kodeJenisPungutan' => $this->metaDataModel->where('name', "Kode Jenis Pungutan BC")->whereIn('value', ['BM', 'PPN', 'PPH'])->findAll(),
            'kodeJenisTarif' => $this->metaDataModel->where('name', "Kode Jenis Tarif BC")->findAll(),
            'kodeFasilitasTarif' => $this->metaDataModel->where('name', "Kode Fasilitas Tarif BC")->whereIn('value', ['1', '2', '5', '7'])->findAll(),
            'kodeSatuanBarang' => null,
            'dokumen' => [],
            'dokumenSelected' => [], // SERI DOKUMEN YANG DI CHEKLIST
            'pungutan' => [],
            'bahanBakuLokal' => [],
            'bahanBakuImpor' => []
        ];

        $data['kodeSatuanBarang'] = $this->metaDataModel->where('name', "Kode Satuan BC")->where('value', $data['barang']['bcDetail']->kodeSatuanBarang ?? "")->findAll();
        // APPEND JENIS DOKUMEN
        foreach ($payload->dokumen as $d) {
            $dokumenDetail = $this->metaDataModel->where('name', "Dokumen")->where('description', $d->kodeDokumen)->first();
            $value = ($dokumenDetail == null) ? "" : $dokumenDetail['value'];
            array_push($data['dokumen'], [
                'seriDokumen' => $d->seriDokumen,
                'kodeDokumen' => $d->kodeDokumen . " - " . $value,
                'nomorDokumen' => $d->nomorDokumen,
                'tanggalDokumen' => date('d/m/Y', strtotime($d->tanggalDokumen))
            ]);
        }
        // APPEND DOKUMEN YANG TER CHECKLIST
        foreach ($data['barang']['bcDetail']->barangDokumen ?? [] as $b) {
            array_push($data['dokumenSelected'], $b->seriDokumen);
        }
        // APPEND PUNGUTAN
        foreach ($data['barang']['bcDetail']->barangTarif ?? [] as $b) {
            $kodeJenisTarifDetail = $this->metaDataModel->where('name', "Kode Jenis Tarif BC")->where('value', $b->kodeJenisTarif)->first();
            $kodeJenisPungutanDetail =  $this->metaDataModel->where('name', "Kode Jenis Pungutan BC")->where('value', $b->kodeJenisPungutan)->first();
            $kodeFasilitasTarifDetail = $this->metaDataModel->where('name', "Kode Fasilitas Tarif BC")->where('value', $b->kodeFasilitasTarif)->first();

            array_push($data['pungutan'], [
                'kodeJenisPungutan' => $b->kodeJenisPungutan,
                'kodeJenisPungutanText' => $b->kodeJenisPungutan . " - " . strtoupper($kodeJenisPungutanDetail['description']),
                'kodeJenisTarifText' => $b->kodeJenisTarif . " - " . $kodeJenisTarifDetail['description'],
                'tarif' => number_format($b->tarif, 2),
                'kodeFasilitasTarifText' => $b->kodeFasilitasTarif . " - " . $kodeFasilitasTarifDetail['description'],
                'tarifFasilitas' => number_format($b->tarifFasilitas, 2)
            ]);
        }
        // APPEND BAHAN BAKU LOKAL & EKSPOR
        foreach ($data['barang']['bcDetail']->bahanBaku ?? [] as $i => $b) {
            if ($b->kodeDokAsal == "23") {
                // EKSPOR
                array_push($data['bahanBakuImpor'], [
                    'indexDelete' => $i,
                    'seriBarang' => $b->seriBarang,
                    'posTarif' => $b->posTarif,
                    'uraianBarang' => $b->uraianBarang,
                    'hargaPenyerahan' => $b->hargaPenyerahan,
                    'kodeSatuanBarang' => $b->kodeSatuanBarang
                ]);
            } else {
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
        }

        return view('BeaCukai/bc-25/form-detail-barang', $data);
    }

    public function barangDetailUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc25Model->find($id)['payload']);
        $seriBarang = $this->request->getVar('seriBarang');

        $nettoTotal = 0;
        $hargaPenyerahanTotal = 0;
        for ($i = 0; $i < count($payload->barang); $i++) {
            if ($payload->barang[$i]->seriBarang == $seriBarang) {
                $payload->barang[$i]->posTarif = $this->request->getVar('posTarif');
                $payload->barang[$i]->kodeBarang = $this->request->getVar('kodeBarang');
                $payload->barang[$i]->uraian = $this->request->getVar('uraian');
                $payload->barang[$i]->merk = $this->request->getVar('merk');
                $payload->barang[$i]->tipe = $this->request->getVar('tipe');
                $payload->barang[$i]->spesifikasiLain = $this->request->getVar('spesifikasiLain');
                $payload->barang[$i]->kodeGunaBarang = $this->request->getVar('kodeGunaBarang');
                $payload->barang[$i]->kodeKategoriBarang = $this->request->getVar('kodeKategoriBarang');
                $payload->barang[$i]->kodeKondisiBarang = $this->request->getVar('kodeKondisiBarang');
                $payload->barang[$i]->flag4tahun = $this->request->getVar('flag4tahun');
                $payload->barang[$i]->kodePerhitungan = $this->request->getVar('kodePerhitungan');
                $payload->barang[$i]->jumlahSatuan = (float)$this->request->getVar('jumlahSatuan');
                $payload->barang[$i]->kodeSatuanBarang = (string)decrypt($this->request->getVar('kodeSatuanBarang'));
                $payload->barang[$i]->jumlahKemasan = (float)$this->request->getVar('jumlahKemasan');
                $payload->barang[$i]->kodeJenisKemasan = $this->request->getVar('kodeJenisKemasan');
                $payload->barang[$i]->netto = (float)$this->request->getVar('netto');
                $payload->barang[$i]->cif = (float)convertRupiahToNumber($this->request->getVar('cif'));
                $payload->barang[$i]->hargaEkspor = (float)convertRupiahToNumber($this->request->getVar('hargaEkspor'));
                $payload->barang[$i]->hargaPenyerahan = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
            }
            $nettoTotal += $payload->barang[$i]->netto;
            $hargaPenyerahanTotal += $payload->barang[$i]->hargaPenyerahan;
        }

        $payload->netto = $nettoTotal;
        $payload->hargaPenyerahan = $hargaPenyerahanTotal;

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil update detail barang"
        ]);
    }

    // CHECKLIST DOKUMEN
    public function createDokumenBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriDokumen = $this->request->getVar('seriDokumen');
        $seriBarang = $this->request->getVar('seriBarang');
        $indexBarang = 0;

        $payload = json_decode($this->bc25Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        array_push($payload->barang[$indexBarang]->barangDokumen, [
            'seriDokumen' => (float)$seriDokumen,
            'seriIjin' => (float)$seriDokumen
        ]);
        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Dokumen berhasil disimpan",
            'status' => true,
        ]);
    }

    // REMOVE CHECKLIST DOKUMEN
    public function deleteDokumenBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $seriDokumen = $this->request->getVar('seriDokumen');
        $indexBarang = 0;
        $indexDelete = 0;

        $payload = json_decode($this->bc25Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        foreach ($payload->barang[$indexBarang]->barangDokumen as $i => $b) {
            if ($b->seriDokumen == $seriDokumen) {
                $indexDelete = $i;
            }
        }

        unset($payload->barang[$indexBarang]->barangDokumen[$indexDelete]);
        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Dokumen berhasil dihapus",
            'status' => true,
        ]);
    }

    public function createPungutanDetailBarang()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $payload = json_decode($this->bc25Model->find($id)['payload']);
        $indexBarang = 0;

        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        // CEK BEA MASUK
        if (count($payload->barang[$indexBarang]->barangTarif) == 0) {
            if ($this->request->getVar('kodeJenisPungutan') != "BM") {
                return response()->setJSON([
                    'message' => "Kode pungutan seri pertama wajib BM (Bea Masuk)",
                    'token' => csrf_hash(),
                    'status' => false,
                ]);
            }
        }

        foreach ($payload->barang[$indexBarang]->barangTarif as $b) {
            if ($b->kodeJenisPungutan == $this->request->getVar('kodeJenisPungutan')) {
                return response()->setJSON([
                    'message' => "Kode pungutan " . $b->kodeJenisPungutan . " sudah ada",
                    'token' => csrf_hash(),
                    'status' => false,
                ]);
            }
        }

        // PUNGUTAN PPN ATAU PPH    
        if ($this->request->getVar('kodeJenisPungutan') == "PPN" || $this->request->getVar('kodeJenisPungutan') == "PPH") {
            $nilaiBayar100 = $payload->barang[$indexBarang]->hargaEkspor * (($this->request->getVar('tarif') + $payload->barang[$indexBarang]->barangTarif[0]->nilaiBayar) / 100);
            $nilaiBayar = $nilaiBayar100 / $this->request->getVar('tarifFasilitas');
        } else {
            $nilaiBayar100 = $payload->barang[$indexBarang]->hargaEkspor * (($this->request->getVar('tarif')) / 100);
            $nilaiBayar = $nilaiBayar100 / $this->request->getVar('tarifFasilitas');
        }

        array_push($payload->barang[$indexBarang]->barangTarif, [
            'seriBarang' => (float)$seriBarang,
            'kodeJenisTarif' => $this->request->getVar('kodeJenisTarif'),
            'jumlahSatuan' => 0,
            'kodeFasilitasTarif' => $this->request->getVar('kodeFasilitasTarif'),
            'kodeSatuanBarang' => (string)$payload->barang[$indexBarang]->kodeSatuanBarang,
            'kodeJenisPungutan' => $this->request->getVar('kodeJenisPungutan'),
            'nilaiBayar' => (int)$nilaiBayar,
            'nilaiFasilitas' => 0,
            'nilaiSudahDilunasi' => 0,
            'tarif' => (float)$this->request->getVar('tarif'),
            'tarifFasilitas' => (float)$this->request->getVar('tarifFasilitas')
        ]);

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Pungutan berhasil disimpan",
            'status' => true,
        ]);
    }

    public function deletePungutanDetailBarang()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $payload = json_decode($this->bc25Model->find($id)['payload']);
        $indexBarang = 0;
        $indexDelete = 0;

        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        foreach ($payload->barang[$indexBarang]->barangTarif as $i => $b) {
            if ($b->kodeJenisPungutan == $this->request->getVar('kodeJenisPungutan')) {
                $indexDelete = $i;
            }
        }

        unset($payload->barang[$indexBarang]->barangTarif[$indexDelete]);
        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Pungutan berhasil dihapus",
            'status' => true,
        ]);
    }

    public function bahanBakuUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $bcPurchaseOrderId = $this->request->getVar('bcPurchaseOrderId');
        $bahanBakuBCList = json_decode($this->request->getVar('bahanBakuBCList'));
        $payload = json_decode($this->bc25Model->find($id)['payload']);
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
                'bahanBakuDokumen' => [
                    'seriDokumen' => $b->seriBarang
                ],
                'cif' => $b->cif,
                'cifRupiah' => $b->cifRupiah,
                'hargaPenyerahan' => $b->hargaPenyerahan,
                'hargaPerolehan' => 0,
                'jumlahSatuan' => $b->jumlahSatuan,
                'kodeSatuanBarang' => $b->kodeSatuanBarang,
                'kodeAsalBahanBaku' => $b->kodeAsalBahanBaku,
                'kodeBarang' => $b->kodeBarang,
                'kodeDokAsal' => $bahanBakuBCList->kodeDokumen,
                'kodeKantor' => $bahanBakuBCList->kodeKantor,
                'merkBarang' => $b->merk,
                'ndpbm' => $b->ndpbm,
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
                'bahanBakuTarif' => $b->barangTarif
            ]);

            $seriBahanBakuLast++;
        }

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

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
        $payload = json_decode($this->bc25Model->find($id)['payload']);

        $indexBarang = 0;

        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        unset($payload->barang[$indexBarang]->bahanBaku[$indexDelete]);
        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Bahan baku berhasil dihapus",
            'status' => true,
        ]);
    }

    public function pungutan($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->find($id);

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc25['payload']);
        $kodeJenisPungutan = $this->metaDataModel
            ->where('name', "Kode Jenis Pungutan BC")
            ->whereIn('value', ['BM', 'PPN', 'PPH'])
            ->findAll();

        $pungutanList = [];

        $bmDibayar = 0;
        $bmDitanggung = 0;
        $bmDibebaskan = 0;
        $bmSudahDilunasi = 0;

        $pphDibayar = 0;
        $pphDitanggung = 0;
        $pphDibebaskan = 0;
        $pphSudahDilunasi = 0;

        $ppnDibayar = 0;
        $ppnDitanggung = 0;
        $ppnDibebaskan = 0;
        $ppnSudahDilunasi = 0;

        foreach ($payload->barang as $b) {
            foreach ($b->barangTarif as $p) {
                if ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 1) {
                    $bmDibayar += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 2) {
                    $bmDitanggung += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 5) {
                    $bmDibebaskan += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 7) {
                    $bmSudahDilunasi += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 1) {
                    $ppnDibayar += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 2) {
                    $ppnDitanggung  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 5) {
                    $ppnDibebaskan  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 7) {
                    $ppnSudahDilunasi  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 1) {
                    $pphDibayar  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 2) {
                    $pphDitanggung  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 5) {
                    $pphDibebaskan  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 7) {
                    $pphSudahDilunasi  += $p->nilaiBayar;
                }
            }
        }
        foreach ($kodeJenisPungutan as $k) {
            if ($k['value'] == "BM") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $bmDibayar,
                    'ditanggung' => $bmDitanggung,
                    'dibebaskan' => $bmDibebaskan,
                    'sudahDilunasi' => $bmSudahDilunasi

                ];
            } elseif ($k['value'] == "PPH") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $pphDibayar,
                    'ditanggung' => $pphDitanggung,
                    'dibebaskan' => $pphDibebaskan,
                    'sudahDilunasi' => $pphSudahDilunasi

                ];
            } elseif ($k['value'] == "PPN") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $ppnDibayar,
                    'ditanggung' => $ppnDitanggung,
                    'dibebaskan' => $ppnDibebaskan,
                    'sudahDilunasi' => $ppnSudahDilunasi
                ];
            }
        }


        $data = [
            'bc25' => $bc25,
            'payload' => $payload,
            'pungutanList' => $pungutanList,
        ];

        return view('BeaCukai/bc-25/form-pungutan', $data);
    }

    public function pernyataan($id)
    {
        $id = decrypt($id);
        $bc25 = $this->bc25Model->find($id);

        if ($bc25 == null) {
            return redirect()->to('bea-cukai-bc-25');
        }

        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc25['payload']);

        $data = [
            'bc25' => $bc25,
            'payload' => $payload,
            'ceisaSetting' => $ceisaSetting
        ];

        return view('BeaCukai/bc-25/form-pernyataan', $data);
    }

    public function pernyataanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc25Model->find($id)['payload']);

        $payload->kotaTtd = $this->request->getVar('kotaTtd');
        $payload->tanggalTtd = date('Y-m-d', strtotime($this->request->getVar('tanggalTtd')));
        $payload->namaTtd = $this->request->getVar('namaTtd');
        $payload->jabatanTtd = $this->request->getVar('jabatanTtd');

        $this->bc25Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pernyataan berhasil diupdate"
        ]);
    }

    public function dropdownBahanBakuAsal()
    {
        $result = $this->bcPurchaseOrderModel->findByNoAjuOrDaftar(
            $this->request->getVar('tipe'),
            $this->request->getVar('search'),
            $this->this_company_id
        );

        return $this->response->setJSON(['results' => $result]);
    }

    public function dropdownDetailPayload()
    {
        $bcPurchaseOrderId = $this->request->getVar('bc_purchase_order_id');
        $tipe = $this->request->getVar('tipe');

        if (empty($bcPurchaseOrderId)) {
            return response()->setJSON([
                'success' => true,
                'token' => csrf_hash(),
                'data' => []
            ]);
        }

        if ($tipe == "IMPORT") {
            $result = $this->bc23Controller->generatePayload($bcPurchaseOrderId);
        } else {
            $result = $this->bc40Controller->generatePayload($bcPurchaseOrderId);
        }

        return response()->setJSON([
            'success' => true,
            'token' => csrf_hash(),
            'data' => $result
        ]);
    }

    // KIRIM BC.23 KE CEISA
    public function kirimCeisa($id)
    {
        $id = decrypt($id);
        $payload = json_decode($this->bc25Model->find($id)['payload']);
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);

        for ($i = 0; $i < count($payload->kemasan); $i++) {
            unset($payload->kemasan[$i]->kemasanInventoriId);
        }

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
        $this->bc25Model->set('status_dokumen', "Sudah Kirim")->where('id', $id)->update();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 2.5 Berhasil Diposting",
            'res' => $res
        ]);
    }

    private function setFlashDataNavigatorSession($id)
    {
        $isCompleteFormHeader = $this->bc25Model->isCompleteFormHeader($id);
        $isCompleteFormEntitas = $this->bc25Model->isCompleteFormEntitas($id);
        $isCompleteFormDokumen = $this->bc25Model->isCompleteFormDokumen($id);
        $isCompleteFormPengangkut = $this->bc25Model->isCompleteFormPengangkut($id);
        $isCompleteFormPetiKemas = $this->bc25Model->isCompleteFormPetiKemas($id);
        $isCompleteFormTransaksi = $this->bc25Model->isCompleteFormTransaksi($id);
        $isCompleteFormBarang = $this->bc25Model->isCompleteFormBarang($id);
        $isCompleteFormPernyataan = $this->bc25Model->isCompleteFormPernyataan($id);

        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormTransaksi);
        session()->setFlashdata('isCompleteFormBarang', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPungutan', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPernyataan', $isCompleteFormPernyataan);

        if (
            $isCompleteFormPernyataan && $isCompleteFormHeader && $isCompleteFormEntitas &&
            $isCompleteFormEntitas && $isCompleteFormDokumen && $isCompleteFormPengangkut &&
            $isCompleteFormPetiKemas && $isCompleteFormTransaksi && $isCompleteFormBarang
        ) {
            $this->bc25Model->set('status_dokumen', "Siap Kirim")->where('id', $id)->update();
        } else {
            $this->bc25Model->set('status_dokumen', "Belum Lengkap")->where('id', $id)->update();
        }
    }

    public function viewOutstanding()
    {
        return view('BeaCukai/bc-25/bc25outstanding');
    }

    public function allOutstanding()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;

        $condition = [
            'company_id' => $this->this_company_id,
            "search" => $this->request->getVar("search"),
            "dateStart" => $this->request->getVar("dateStart")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
                : null,
            "dateEnd" => $this->request->getVar("dateEnd")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
                : null,
        ];

        $dataQry = $this->bcPengeluaranBarangModel->getListOutstansingPengeluaran(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $dataResult = [];
        $no = $start + 1;
        foreach ($dataQry['data'] as $d) {
            array_push($dataResult, [
                'no' => $no++,
                'id' => encrypt($d['id']),
                'tujuan_pengeluaran' => $d['tujuan_pengeluaran'],
                'tanggal' => date('d/m/Y', strtotime($d['tanggal'])),
                'reference_no' => $d['reference_no'],
                'customer_name' => $d['customer_name'],
                'kode_barang' => $d['kode_barang'],
                'barang_name' => $d['barang_name'],
                'qty' => (float)$d['qty'],
                'kode_satuan' => $d['kode_satuan'],
                'amount' => (float)$d['amount'],
            ]);
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($dataQry['totalData'] ?? 0),
            'recordsFiltered' => intval($dataQry['totalFilteredData'] ?? 0),
            'data' => $dataResult,
        ]);
    }


    public function OutstandingExcel()
    {
        $condition = [
            'company_id' => $this->this_company_id,
            "dateStart" => $this->request->getVar("dateStart")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
                : null,
            "dateEnd" => $this->request->getVar("dateEnd")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
                : null,
        ];

        $dataQry = $this->bcPengeluaranBarangModel->getListOutstansingPengeluaran(
            $condition,
            2,
            "desc",
            100000000000,
            0
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================================
        // Rentang tanggal di atas
        $sheet->setCellValue('A1', 'Tanggal: ' .
            ($condition['dateStart'] ?? '-') . ' s/d ' . ($condition['dateEnd'] ?? '-'));
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // ================================
        // Header kolom
        $headers = ['No', 'Tujuan Pengeluaran', 'Tanggal', 'Reference No', 'Customer', 'Kode Barang', 'Barang', 'Qty', 'Satuan', 'Nilai Barang'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '2', $header);
            $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $col++;
        }

        // ================================
        // Isi data
        $row = 3;
        $no = 1;
        foreach ($dataQry['data'] as $d) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $d['tujuan_pengeluaran']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($d['tanggal'])));
            $sheet->setCellValue('D' . $row, $d['reference_no']);
            $sheet->setCellValue('E' . $row, $d['customer_name']);
            $sheet->setCellValue('F' . $row, $d['kode_barang']);
            $sheet->setCellValue('G' . $row, $d['barang_name']);
            $sheet->setCellValue('H' . $row, (float)$d['qty']);
            $sheet->setCellValue('I' . $row, $d['kode_satuan']);
            $sheet->setCellValue('J' . $row, (float)$d['amount']);

            // Rata kanan & format angka
            $sheet->getStyle('J' . $row)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J' . $row)
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // Border untuk setiap sel
            foreach (range('A', 'J') as $c) {
                $sheet->getStyle($c . $row)
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            $row++;
        }

        // ================================
        // Auto width kolom
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ================================
        // Export
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Outstanding_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    public function get_payload()
    {
        $payloadArr = [
            "asalData" => "S",
            "bruto" => 0,
            "cif" => 0,
            "dasarPengenaanPajak" => 0,
            "disclaimer" => "1",
            "kodeJenisTpb" => "",
            "hargaPenyerahan" => "",
            "idPengguna" => "",
            "jabatanTtd" => "",
            "jumlahKontainer" => 0,
            "kodeCaraBayar" => "",
            "kodeDokumen" => "25",
            "kodeKantor" => "",
            "kodeLokasiBayar" => "",
            "kodeTujuanPengiriman" => "",
            "kodeValuta" => "",
            "kotaTtd" => "",
            "namaTtd" => "",
            "ndpbm" => 0,
            "netto" => 0,
            "nomorAju" => "",
            "seri" => 1,
            "tanggalAju" => "",
            "tanggalTtd" => "",
            "volume" => 0,
            "ppnPajak" => 0,
            "ppnbmPajak" => 0,
            "tarifPpnPajak" => 0,
            "tarifPpnbmPajak" => 0,
            "barang" => [],
            "entitas" => [],
            "kemasan" => [],
            "kontainer" => [],
            "dokumen" => [],
            "pengangkut" => []
        ];

        return json_encode($payloadArr, JSON_UNESCAPED_SLASHES);
    }

    public function debug_payload($id)
    {
        $data = $this->bc25Model->where('id', decrypt($id))->first();
        $payload = json_decode($data['payload']);
        return response()->setJSON($payload);
    }
}
