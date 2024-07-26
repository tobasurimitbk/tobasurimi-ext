<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\BC27Model;
use App\Models\CeisaSettingModel;
use App\Models\CompaniesModel;
use App\Models\MetadataModel;
use App\Models\MutasiGlobalDetailModel;
use App\Models\MutasiGlobalModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\PengusahaTPBModel;
use App\Models\BarangMasterSpesifikasiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PDO;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54
class BC27 extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $companyModel;
    protected $mutasiGlobalModel;
    protected $metaDataModel;
    protected $bc27Model;
    protected $mutasiGlobalDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $kantorBeaCukaiModel;
    protected $pengusahaTPBModel;
    protected $barangMasterSpesifikasiModel;

    public function __construct()
    {
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->companyModel = new CompaniesModel();
        $this->mutasiGlobalModel = new MutasiGlobalModel();
        $this->metaDataModel = new MetadataModel();
        $this->bc27Model = new BC27Model();
        $this->mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first()
        ];

        return view('BeaCukai/bc-27/index', $data);
    }

    public function online()
    {
        $data = [
            'baseUrl' => $this->metaDataModel->where('name', "Base Url BC")->first()['value']
        ];

        return view('BeaCukai/bc-27/online', $data);
    }

    public function allOnline()
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);
        $dataOnline = $beacukaiApi->getListStatusResponseAll();

        $newDataResult = [];
        foreach ($dataOnline->dataRespon as $d) {
            if ($d->kodeDokumen == "27") {
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
            "type"          => "BC 2.7"
        ];

        $condition = [
            "bc_27.company_asal_id"  => $this->this_company_id,
            "bc_27.deletedAt" => null,
            "mutasi_global.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC27" => $this->request->getGet("mulaiTanggalBC27"),
            "selesaiTanggalBC27" => $this->request->getGet('selesaiTanggalBC27'),
            "noBC27" => $this->request->getGet('noBC27'),
            "noAju" => $this->request->getGet('noAju'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc27Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "company_asal_name"     => strtoupper(session()->get('login')->this_company),
                "divisi_asal_name"      => strtoupper($data->divisi),
                "warehouse_asal_name"   => strtoupper($data->warehouse_name),
                "company_tujuan_name"   => strtoupper($data->company),
                "no_mutasi"             => $data->no_mutasi,
                "tanggal_bc_27"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "no_aju"                => $data->no_aju . " / " . ($data->no_daftar == "" ? "-" : $data->no_daftar),
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

    public function create()
    {
        $data = [
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'companyAsalName' => session()->get('login')->this_company,
            'noAju' => $this->generateNomorAju(),
        ];

        return view('BeaCukai/bc-27/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->getBC27($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $data = [
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'companyAsalName' => session()->get('login')->this_company,
            'noAju' => $this->generateNomorAju(),
            'bc27' => $bc27
        ];

        return view('BeaCukai/bc-27/form', $data);
    }

    public function createAction()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));

        $this->bc27Model->insert([
            'company_asal_id' => $this->this_company_id,
            'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
            'mutasi_global_id' => $this->request->getVar('mutasi_global_id'),
            'no_aju' => $this->request->getVar('no_aju'),
            'status_posting' => '0',
            'no_daftar' => $this->request->getVar('no_daftar'),
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 27 Berhasil Disimpan"
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->bc27Model->update($id, [
            'company_asal_id' => $this->this_company_id,
            'company_tujuan_id' => $this->request->getVar('company_tujuan_id'),
            'mutasi_global_id' => $this->request->getVar('mutasi_global_id'),
            'no_aju' => $this->request->getVar('no_aju'),
            'no_daftar' => $this->request->getVar('no_daftar'),
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 27 Berhasil Diupdate"
        ]);
    }

    public function getListMutasiDetail()
    {
        $mutasiGlobalId = $this->request->getVar('mutasi_global_id');
        $dataResultDetail = $this->mutasiGlobalDetailModel->getMutasiDetail($mutasiGlobalId);

        return response()->setJSON([
            'status' => true,
            'data' => $dataResultDetail,
        ]);
    }

    public function dropdownMutasiGlobal()
    {
        $companyTujuanId = $this->request->getVar('company_tujuan_id');
        $result = $this->mutasiGlobalModel->getMutasiGlobalList($companyTujuanId, $this->this_company_id);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $result
        ]);
    }

    public function checkNoAju()
    {
        $id = decrypt($this->request->getVar('id'));
        $noAju = $this->request->getVar('no_aju');
        $isUsed = true;

        if (!empty($this->request->getVar('id'))) {
            // UPDATE
            $first = $this->bc27Model
                ->where('company_asal_id', $this->this_company_id)
                ->where(
                    'no_aju',
                    $noAju
                )
                ->where('id != ', $id)
                ->first();

            if ($first != null) {
                $isUsed = false;
            }
        } else {
            // CREATE
            $first = $this->bc27Model
                ->where('company_asal_id', $this->this_company_id)
                ->where(
                    'no_aju',
                    $noAju
                )
                ->first();

            if ($first != null) {
                $isUsed = false;
            }
        }

        if (!$isUsed) {
            return response()->setJSON([
                'status' => false,
                'message' => "No aju sudah digunakan"
            ]);
        } else {
            return response()->setJSON([
                'status' => true,
                'message' => "No aju tersedia"
            ]);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->bc27Model->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 27 Berhasil Dihapus"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        // BC 27 FIRST
        $bc27 = $this->bc27Model->find($id);
        // KURANGI STOK NYA
        $mutasiGlobal = $this->mutasiGlobalModel->find($bc27['mutasi_global_id']);
        $mutasiGlobalList = $this->mutasiGlobalDetailModel->where('mutasi_global_id', $bc27['mutasi_global_id'])->where('deletedAt', null)->findAll();

        foreach ($mutasiGlobalList as $m) {
            $stock = $this->stockModel->find($m['stock_id']);
            $qty = $m['qty'];

            if ($stock['tipe_barang'] == "kemasan") {
                $barang2_id = $stock['kemasan_id'];
            } else {
                $barang2_id = $stock['barang2_id'];
            }

            // BARANG LAMA
            $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
                $m['stock_id'],
                $m['bc_id'],
                $m['no_aju'],
                $m['stock_dokumen']
            );

            $stok = $this->stockModel->insertStok(
                $mutasiGlobal['company_asal_id'],
                $mutasiGlobal['warehouse_asal_id'],
                $mutasiGlobal['divisi_asal_id'],
                $stock['tipe_barang'],
                $stock['barang1_id'],
                $barang2_id,
                ($qty * -1),
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                "Out",
                date('Y-m-d'),
                $this->this_user_id,
                "MUTASI",
                "-", // NO PENERIMAAN MUTASI
                $mutasiGlobal['keterangan'],
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $m['bc_id'],
                $stok,
                $stokDetail,
                $qty,
                $m['no_aju'],
                $mutasiGlobal['no_mutasi'],
                $m['stock_dokumen'],
                $stockOldDetail['supplier_id'],
                $stockOldDetail['harga_umum'],
                $stockOldDetail['harga_harian'],
                $stockOldDetail['harga_bulanan'],
                $stockOldDetail['no_po']
            );
        }

        $this->bc27Model->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 2.7 Berhasil Diposting"
        ]);
    }

    public function generateNomorAju()
    {
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $kodeDokumenbc40Static = $this->metaDataModel->where('name', "Kode BC27 Static")->first();

        $kodeKantorStatic = $ceisaSetting['kode_kantor_pabean'];
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = "";

        $bc27Last = $this->bc27Model->orderBy('createdAt', "DESC")->limit(1)->first();

        if ($bc27Last == null) {
            $sequenceNoUrutPengajuan = "000001";
        } else {
            if ($bc27Last['no_aju'] == null) {
                $sequenceNoUrutPengajuan = "000001";
            } else {
                // Buatkan auto increment
                $arrNo = explode('-', $bc27Last['no_aju']);
                $lastNomor = $arrNo[3];
                // lakukan increment
                $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
                $sequenceNoUrutPengajuan = $nextNomor;
            }
        }

        return $kodeDokumenbc40Static['value'] . '-' . $kodeKantorStatic . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan;
    }
    public function viewOutstanding()
    {
        return view('BeaCukai/BC-27/bc27outstanding');
    }

    public function allOutstanding()
    {
        $mutasiGlobalUsed = $this->bc27Model
            ->select('mutasi_global_id')
            ->where('company_asal_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $mutasiGlobalAll = $this->mutasiGlobalModel->where('company_asal_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        $allmutasiGlobalIdArr = [];
        $mutasiGlobalIdUsedArr = [];
        $mutasiGlobalIdNotUsedArr = [];

        foreach ($mutasiGlobalUsed as $m) {
            array_push($mutasiGlobalIdUsedArr, $m['mutasi_global_id']);
        }
        foreach ($mutasiGlobalAll as $i) {
            array_push($allmutasiGlobalIdArr, $i['id']);
        }

        $mutasiGlobalIdNotUsedArr = array_diff($allmutasiGlobalIdArr, $mutasiGlobalIdUsedArr);
        $list = [];

        foreach ($mutasiGlobalIdNotUsedArr as $id) {
            $data = $this->mutasiGlobalModel
                ->select('
                    mutasi_global.id as mutasi_id,
                    no_mutasi,
                    company_tujuan_id,
                    company_asal_id,
                    divisi_asal_id,
                    divisi,
                    warehouse_asal_id,
                    warehouse_name,
                    tanggal')
                ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id')
                ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id')
                ->where('mutasi_global.id', $id)
                ->first();
            $company_asal = $this->companyModel
                ->select('company')
                ->where('id', $data['company_asal_id'])
                ->first();
            $company_tujuan = $this->companyModel
                ->select('company')
                ->where('id', $data['company_tujuan_id'])
                ->first();
            $detailCount = $this->mutasiGlobalDetailModel
                ->select('count(*) as jumlah_barang')
                ->where('mutasi_global_id', $id)
                ->first();
            $detailMutasi = $this->mutasiGlobalDetailModel
                ->where('mutasi_global_id', $id)
                ->findAll();

            $nilaiBarang = 0;
            foreach ($detailMutasi as $dm) {
                $stockListDetail = $this->stockDetail2Model
                    ->getStockListDetail($dm['stock_id'], $dm['bc_id'], $dm['no_aju'], $dm['stock_dokumen']);
                $nilaiBarang = intval($stockListDetail['harga_harian']) + intval($stockListDetail['harga_umum']) + intval($stockListDetail['harga_bulanan']);
            }



            if ($data != null) {
                array_push($list, [
                    'id' => $data['mutasi_id'],
                    'no_mutasi' => $data['no_mutasi'],
                    'company_asal' => $company_asal['company'],
                    'company_tujuan' => $company_tujuan['company'],
                    'divisi' => $data['divisi'],
                    'warehouse_name' => $data['warehouse_name'],
                    'tanggal' => date('d/m/Y', strtotime($data['tanggal'])),
                    'jumlah_barang' => $detailCount['jumlah_barang'],
                    'total_harga' => number_format($nilaiBarang, 2)
                ]);
            }
        }
        return json_encode($list);
    }

    public function OutstandingSheet()
    {
        $list = json_decode($this->allOutstanding());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'No Mutasi')
            ->setCellValue('C1', 'Company Asal')
            ->setCellValue('D1', 'Company Tujuan')
            ->setCellValue('E1', 'Divisi / Warehouse Pengeluaran')
            ->setCellValue('F1', 'Tanggal')
            ->setCellValue('G1', 'Jumlah Barang')
            ->setCellValue('H1', 'Nilai Barang');
        $no = 1;
        $column = 2;

        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->no_mutasi)
                ->setCellValue('C' . $column,  $l->company_asal)
                ->setCellValue('D' . $column,  $l->company_tujuan)
                ->setCellValue('E' . $column,  $l->divisi . " / " . $l->warehouse_name)
                ->setCellValue('F' . $column,  $l->tanggal)
                ->setCellValue('G' . $column,  $l->jumlah_barang)
                ->setCellValue('H' . $column,  $l->total_harga);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC27';
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Outstanding-BC-2.7';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
    private function setFlashDataNavigatorSession($id)
    {
        $isCompleteFormHeader = $this->bc27Model->isCompleteFormHeader($id);
        $isCompleteFormEntitas = $this->bc27Model->isCompleteFormEntitas($id);
        $isCompleteFormDokumen = $this->bc27Model->isCompleteFormDokumen($id);
        $isCompleteFormPengangkut = $this->bc27Model->isCompleteFormPengangkut($id);
        $isCompleteFormPetiKemas = $this->bc27Model->isCompleteFormPetiKemas($id);
        $isCompleteFormTransaksi = $this->bc27Model->isCompleteFormTransaksi($id);


        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormTransaksi);

        if (
            $isCompleteFormHeader && $isCompleteFormEntitas &&
            $isCompleteFormEntitas && $isCompleteFormDokumen && $isCompleteFormPengangkut &&
            $isCompleteFormPetiKemas && $isCompleteFormTransaksi
        ) {
            $this->bc27Model->set('status_dokumen', "Siap Kirim")->where('id', $id)->update();
        } else {
            $this->bc27Model->set('status_dokumen', "Belum Lengkap")->where('id', $id)->update();
        }
    }

    //ceisa router
    public function header($id)
    {
        $id = decrypt($id);

        $bc27 = $this->bc27Model->find($id);
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $data = [
            'bc27' => $bc27,
            'noAju' => $bc27 == null ? $this->generateNomorAju() : $bc27['no_aju'],
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'kodeLokasiBayar' => $this->metaDataModel->where('name', "KODE LOKASI BAYAR")->findAll(),
            'kodeTujuanTpb' => $this->metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTujuanPengiriman' => $this->metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', 40)->where('deletedAt', null)->findAll(),
            'kodeCaraBayar' => $this->metaDataModel->where('name', "CARA BAYAR")->findAll(),
            'selectedKantor' => $ceisaSetting['kode_kantor_pabean'],
            'payload' => json_decode($bc27['payload'])
        ];
        return view('BeaCukai/bc-27/form-header', $data);
    }
    public function updateHeader()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);

        $payload->asalData = "S";
        $payload->nomorAju =  str_replace('-', '', $this->request->getVar('nomorAju'));
        $payload->tanggalAju = getDateFromNomorAju($this->request->getVar('header_no_pengajuan'));
        $payload->kodeKantor = $this->request->getVar('kodeKantor');
        $payload->kodeKantorTujuan = $this->request->getVar('kodeKantorTujuan');
        $payload->kodeJenisTpb = $this->request->getVar('kodeJenisTpb');
        $payload->kodeTujuanTpb = $this->request->getVar('kodeTujuanTpb');
        $payload->kodeTujuanPengiriman = $this->request->getVar('kodeTujuanPengiriman');
        $payload->idPengguna = "NPWP";
        $payload->seri = 1;
        $payload->disclaimer = "1";
        $payload->kodeDokumen = "25";
        $payload->volume = 0;

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Header berhasil disimpan"
        ]);
    }

    public function entitas($id)
    {
        $id = decrypt($id);

        $bc27 = $this->bc27Model->find($id);
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->findAll();

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);

        $payload = json_decode($bc27['payload']);
        // JIKA MASIH KOSONG SET DULU BOSQ
        if (!is_array($payload->entitas)) {
            $payload->entitas = [
                [
                    'alamatEntitas' => "",
                    'kodeEntitas' => "3",
                    'kodeJenisApi' => "2",
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
                    'kodeJenisApi' => "2",

                ],
                [
                    'alamatEntitas' => "",
                    'kodeEntitas' => "3",
                    'kodeJenisApi' => "2",
                    'kodeJenisIdentitas' => "5",
                    'kodeStatus' => '3',
                    'namaEntitas' => "",
                    'nomorIdentitas' => "",
                    'seriEntitas' => 3,
                    'nomorIjinEntitas' => "",
                    'tanggalIjinEntitas' => "",
                ]
            ];
            $this->bc27Model->update($id, ['payload' => json_encode($payload)]);
        }

        $data = [
            'bc27' => $bc27,
            'pengusahaTPB' => $pengusahaTPB,
            'payload' => json_decode($this->bc27Model->find($id)['payload'])
        ];
        // var_dump($data['payload']->entitas);
        // die;
        return view('BeaCukai/bc-27/form-entitas', $data);
    }

    public function updateEntitas()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);


        $payload->entitas[0] = [
            'alamatEntitas' => $this->request->getVar('entitas_alamat_pengusaha'),
            'kodeEntitas' => "3",
            'kodeJenisApi' => "2",
            'kodeJenisIdentitas' => "5",
            'kodeStatus' => '3',
            'namaEntitas' => $this->request->getVar('entitas_nama_pengusaha'),
            'nibEntitas' => $this->request->getVar('entitas_nib'),
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_pengusaha'),
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
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_pemilik_barang'),
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
            'nomorIdentitas' => $this->request->getVar('entitas_npwp_penerima_barang'),
            'seriEntitas' => 3,
            'nomorIjinEntitas' => $this->request->getVar('entitas_nomor_ijin_tpb_penerima_barang'),
            'tanggalIjinEntitas' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('entitas_tanggal_skep_tpb_penerima_barang')), "Y-m-d"),

        ];

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);
        return response()->setJSON([
            'status' => true,
            'message' => "Entitas berhasil disimpan"
        ]);
    }

    public function dokumen($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

        if (!is_array($payload->dokumen)) {
            $payload->dokumen = [];
            $this->bc27Model->update($id, ['payload' => json_encode($payload)]);
        }
        $dokumen = [];
        foreach (json_decode($this->bc27Model->find($id)['payload'])->dokumen as $d) {
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
            'bc27' => $bc27,
            'kodeDokumen' => $this->metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'dokumen' => $dokumen,
        ];

        return view('BeaCukai/bc-27/form-dokumen', $data);
    }

    public function updateDokumen()
    {
        // INVOICE = 30
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc27Model->find($id)['payload']);
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

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil disimpan"
        ]);
    }

    public function deleteDokumen()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        unset($payload->dokumen[$indexDelete]);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function pengangkut($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

        $data = [
            'pengangkut' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'bc27' => $bc27,
            'payload' => $payload,
        ];

        return view('BeaCukai/bc-27/form-pengangkut', $data);
    }

    public function pengangkutUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        $payload->pengangkut[0] = [
            'namaPengangkut' => $this->request->getVar('pengangkut_nama_pengangkut'),
            'nomorPengangkut' => $this->request->getVar('pengangkut_nomor_pengangkut'),
            'kodeCaraAngkut' => $this->request->getVar('pengangkut_kode_cara_angkut'),
            'seriPengangkut' => 1
        ];

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengangkut berhasil disimpan"
        ]);
    }
    public function kemasanPetiKemas($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

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
            'bc27' => $bc27,
            'payload' => $payload,
            'seriKemasan' => $seriKemasan,
            'seriKontainer' => $seriKontainer,
            'dropdownKemasan' => $this->bc27Model->dropdownKemasan($bc27['mutasi_global_id']),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $this->metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'dataKemasan' => $dataKemasan,
            'dataKontainer' => $dataKontainer,
        ];

        return view('BeaCukai/bc-27/form-kemasan-peti-kemas', $data);
    }

    public function kemasanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);

        array_push($payload->kemasan, [
            'jumlahKemasan' => (float)$this->request->getVar('kemasan_jumlah_kemasan'),
            'kodeJenisKemasan' => $this->request->getVar('kemasan_jenis_kemasan'),
            'merkKemasan' => $this->request->getVar('kemasan_merk_kemasan'),
            'seriKemasan' => (int)$this->request->getVar('kemasan_seri_kemasan'),
            'kemasanInventoriId' => $this->request->getVar('kemasan_inventori_id'),
        ]);

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kemasan berhasil disimpan"
        ]);
    }

    public function deleteKemasan()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        unset($payload->kemasan[$indexDelete]);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function kontainerUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc27 = $this->bc27Model->find($id);
        $payload = json_decode($bc27['payload']);

        array_push($payload->kontainer, [
            'kodeJenisKontainer' => $this->request->getVar('kontainer_jenis'),
            'kodeTipeKontainer' => $this->request->getVar('kontainer_tipe'),
            'kodeUkuranKontainer' => $this->request->getVar('kontainer_ukuran'),
            'nomorKontainer' => $this->request->getVar('kontainer_nomor'),
            'seriKontainer' => (int)$this->request->getVar('kontainer_seri'),
        ]);

        $payload->jumlahKontainer = count($payload->kontainer);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil disimpan"
        ]);
    }

    public function deleteKontainer()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc27Model->find($id)['payload']);

        unset($payload->kontainer[$indexDelete]);
        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil dihapus"
        ]);
    }

    public function transaksi($id)
    {
        $id = decrypt($id);
        $bc27 = $this->bc27Model->find($id);

        if ($bc27 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc27['payload']);

        $data = [
            'kodeValuta' => $this->metaDataModel->where('name', "Valuta")->findAll(),
            'bc27' => $bc27,
            'payload' => $payload,
        ];

        return view('BeaCukai/bc-27/form-transaksi', $data);
    }

    public function transaksiUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc27Model->find($id)['payload']);

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

        $this->bc27Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Transaksi berhasil diupdate"
        ]);
    }
}
