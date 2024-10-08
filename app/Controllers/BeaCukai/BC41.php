<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC25Model;
use App\Models\BC41Model;
use App\Models\BCPurchaseOrderModel;
use App\Models\CeisaSettingModel;
use App\Models\HsCodesModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengembalianBarangDetailModel;
use App\Models\PengembalianBarangModel;
use App\Models\PengusahaTPBModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\SalesOrderLainModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    protected $bc25Model;
    protected $hsCodeModel;
    protected $bcPurchaseOrderModel;
    protected $pengembalianBarangModel;
    protected $penerimaanBarangModel;
    protected $pengembalianBarangDetailModel;

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
        $this->bc25Model = new BC25Model();
        $this->hsCodeModel = new HsCodesModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->pengembalianBarangDetailModel = new PengembalianBarangDetailModel();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first(),
        ];

        return view('BeaCukai/BC-41/index', $data);
    }

    public function online()
    {
        $data = [
            'baseUrl' => $this->metaDataModel->where('name', "Base Url BC")->first()['value']
        ];

        return view('BeaCukai/BC-41/online', $data);
    }

    public function allOnline()
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);
        $dataOnline = $beacukaiApi->getListStatusResponseAll();

        $newDataResult = [];
        foreach ($dataOnline->dataRespon as $d) {
            if ($d->kodeDokumen == "41") {
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
            "type"          => "BC 4.1"
        ];

        $condition = [
            "bc_41.company_id"  => $this->this_company_id,
            "bc_41.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC41" => $this->request->getGet("mulaiTanggalBC41"),
            "selesaiTanggalBC41" => $this->request->getGet('selesaiTanggalBC41'),
            "noAju" => $this->request->getGet('noAju'),
            'asalPengeluaran' => $this->request->getGet('asalPengeluaran'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc41Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            if ($data->pengembalian_barang_id != null) {
                $penerimaanBarang = $this->pengembalianBarangModel
                    ->select('divisis.divisi,warehouses.warehouse_name')
                    ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
                    ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                    ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                    ->where('pengembalian_barang.id', $data->pengembalian_barang_id)
                    ->first();

                if ($penerimaanBarang != null) {
                    $data->divisi = $penerimaanBarang['divisi'];
                    $data->warehouse_name = $penerimaanBarang['warehouse_name'];
                }
            }

            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "asal_pengeluaran"      => ($data->sales_order_lain_id != null) ? "PENJUALAN" : "RETUR",
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "no_sales_order"        => $data->no_sales_order,
                "no_surat_jalan"        => $data->no_surat_jalan,
                "customer_name"         => $data->customer_name,
                "supplier_name"         => $data->supplier_name,
                "no_aju"                => $data->no_aju . " / " . $data->no_daftar,
                "tanggal_bc_41"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "status_posting"        => $data->status_posting,
                "status_dokumen"        => strtoupper($data->status_dokumen),
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

    public function getReference()
    {
        $asalPengeluaran = $this->request->getVar('asal_pengeluaran');
        if ($asalPengeluaran == "RETUR") {
            $reference = $this->bc41Model->getListPengembalianBarang($this->this_company_id);
        } else {
            $reference = $this->bc41Model->getListSalesOrderLain($this->this_company_id);
        }

        return response()->setJSON([
            'status' => true,
            'data' => $reference,
        ]);
    }

    public function getDetailReference()
    {
        $asalPengeluaran = $this->request->getVar('asal_pengeluaran');
        $referenceId = ($this->request->getVar('reference_id'));

        if ($asalPengeluaran == "RETUR") {
            $referenceDetail = $this->pengembalianBarangModel->getReturBeaCukaiDetail($referenceId);
        } else {
            $referenceDetail = $this->salesOrderLainDetailModel->detail($referenceId);
        }

        return response()->setJSON([
            'data' => $referenceDetail,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function create()
    {
        $data = [
            'noAju' => $this->generateNomorAju(),
            'salesOrderLain' => $this->bc41Model->getListSalesOrderLain()
        ];

        return view('BeaCukai/BC-41/form', $data);
    }

    public function createAction()
    {
        $this->bc41Model->insert([
            'company_id' => $this->this_company_id,
            'sales_order_lain_id' => $this->request->getVar('asal_pengeluaran') == "RETUR" ? null : $this->request->getVar('reference_id'),
            'pengembalian_barang_id' => $this->request->getVar('asal_pengeluaran') == "PENJUALAN" ? null : $this->request->getVar('reference_id'),
            'no_aju' => $this->request->getVar('no_aju'),
            'no_daftar' => $this->request->getVar('no_daftar'),
            'status_posting' => '0',
            'createdAt' => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))))
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Disimpan"
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->bc41Model->update($id, [
            'company_id' => $this->this_company_id,
            'sales_order_lain_id' => $this->request->getVar('asal_pengeluaran') == "RETUR" ? null : $this->request->getVar('reference_id'),
            'pengembalian_barang_id' => $this->request->getVar('asal_pengeluaran') == "PENJUALAN" ? null : $this->request->getVar('reference_id'),
            'no_aju' => $this->request->getVar('no_aju'),
            'no_daftar' => $this->request->getVar('no_daftar'),
            'status_posting' => '0',
            'createdAt' => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))))
        ]);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Diupdate"
        ]);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->detail($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        if ($bc41['sales_order_lain_id'] != null) {
            $reference = $this->bc41Model->getListSalesOrderLain($this->this_company_id, $bc41['sales_order_lain_id']);
        } else {
            $reference = $this->bc41Model->getListPengembalianBarang($this->this_company_id, $bc41['pengembalian_barang_id']);
        }

        $data = [
            'noAju' => $this->generateNomorAju(),
            'reference' => $reference,
            'bc41' => $bc41
        ];

        return view('BeaCukai/BC-41/form', $data);
    }

    public function checkNoAju()
    {
        $id = decrypt($this->request->getVar('id'));
        $noAju = $this->request->getVar('no_aju');
        $isUsed = true;

        if (!empty($this->request->getVar('id'))) {
            // UPDATE
            $first = $this->bc41Model
                ->where('company_id', $this->this_company_id)
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
            $first = $this->bc41Model
                ->where('company_id', $this->this_company_id)
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
        $this->bc41Model->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Dihapus"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc41 = $this->bc41Model->find($id);

        if ($bc41['sales_order_lain_id'] != null) {
            // KURANGIN STOK NYA
            $salesOrderLain = $this->salesOrderLainModel->find($bc41['sales_order_lain_id']);
            $salesOrderLainList = $this->salesOrderLainDetailModel->where('sales_order_lain_id', $bc41['sales_order_lain_id'])->findAll();

            foreach ($salesOrderLainList as $s) {
                $stock = $this->stockModel->find($s['stock_id']);
                $qty = $s['qty_konversi'];

                if ($stock['tipe_barang'] == "kemasan") {
                    $barang2_id = $stock['kemasan_id'];
                } else {
                    $barang2_id = $stock['barang2_id'];
                }

                // BARANG LAMA
                $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
                    $s['stock_id'],
                    $s['bc_id'],
                    $s['no_aju'],
                    $s['stock_dokumen']
                );

                $stok = $this->stockModel->insertStok(
                    $salesOrderLain['company_id'],
                    $salesOrderLain['warehouse_id'],
                    $salesOrderLain['divisi_id'],
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
                    "PENJUALAN",
                    $salesOrderLain['no_sales_order'],
                    $salesOrderLain['keterangan'],
                );

                // SUB DETAIL
                $this->stockDetail2Model->insertStokDetail2(
                    $s['bc_id'],
                    $stok,
                    $stokDetail,
                    $qty,
                    $s['no_aju'],
                    $salesOrderLain['no_sales_order'],
                    $s['stock_dokumen'],
                    $stockOldDetail['supplier_id'],
                    $s['total_harga'],
                    null,
                    null,
                    $stockOldDetail['no_po']
                );
            }
        } else {

            $pengembalianBarang = $this->pengembalianBarangModel->find($bc41['pengembalian_barang_id']);
            $penerimaanBarang = $this->penerimaanBarangModel->find($pengembalianBarang['penerimaan_barang_id']);

            if ($penerimaanBarang == null) {
                return response()->setJSON([
                    'status' => true,
                    'message' => "Gagal Posting, Terjadi Kesalahan Saat Menambahkan Stok"
                ]);
            } else {
                $pengembalianBarangDetail =  $this->pengembalianBarangModel->getReturBeaCukaiDetail($bc41['pengembalian_barang_id']);

                foreach ($pengembalianBarangDetail as $p) {

                    $stock = $this->stockModel->find($p['stock_id']);
                    $qty = $p['qty_konversi'];

                    if ($stock['tipe_barang'] == "kemasan") {
                        $barang2_id = $stock['kemasan_id'];
                    } else {
                        $barang2_id = $stock['barang2_id'];
                    }

                    // BARANG LAMA
                    $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
                        $p['stock_id'],
                        $p['bc_id'],
                        $p['no_aju'],
                        $p['stock_dokumen']
                    );

                    $stok = $this->stockModel->insertStok(
                        $penerimaanBarang['company_id'],
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
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
                        "RETUR",
                        $pengembalianBarang['no_surat_jalan'],
                        $pengembalianBarang['keterangan'],
                    );

                    // SUB DETAIL
                    $this->stockDetail2Model->insertStokDetail2(
                        $p['bc_id'],
                        $stok,
                        $stokDetail,
                        $qty,
                        $p['no_aju'],
                        $pengembalianBarang['no_surat_jalan'],
                        $p['stock_dokumen'],
                        $stockOldDetail['supplier_id'],
                        $p['total_harga'],
                        null,
                        null,
                        $stockOldDetail['no_po']
                    );
                }
            }
        }

        $this->bc41Model->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Diposting"
        ]);
    }


    public function generateNomorAju()
    {
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $kodeDokumenbc41Static = $this->metaDataModel->where('name', "Kode BC41 Static")->first();

        $kodeKantorStatic = $ceisaSetting['kode_kantor_pabean'];
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = "";

        $bc41Last = $this->bc41Model->orderBy('createdAt', "DESC")->limit(1)->first();

        if ($bc41Last == null) {
            $sequenceNoUrutPengajuan = "000001";
        } else {
            if ($bc41Last['no_aju'] == null) {
                $sequenceNoUrutPengajuan = "000001";
            } else {
                // Buatkan auto increment
                $arrNo = explode('-', $bc41Last['no_aju']);
                $lastNomor = $arrNo[3];
                // lakukan increment
                $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
                $sequenceNoUrutPengajuan = $nextNomor;
            }
        }

        return $kodeDokumenbc41Static['value'] . '-' . $kodeKantorStatic . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan;
    }

    public function header($id)
    {
        $id = decrypt($id);

        $bc41 = $this->bc41Model->find($id);
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $data = [
            'bc41' => $bc41,
            'noAju' => $bc41 == null ? $this->generateNomorAju() : $bc41['no_aju'],
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
            'dropdownKemasan' => $this->bc25Model->dropdownKemasan($bc41['sales_order_lain_id']),
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
        $totalBarang = count($this->bc41Model->barang($bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id']));

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
            'barang' => $this->bc41Model->barang($bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id'])
        ];

        return view('BeaCukai/BC-41/form-barang', $data);
    }

    public function barangDetail($id, $kodeBarang)
    {
        $id = decrypt($id);
        $kodeBarang = decrypt($kodeBarang);
        $bc41 = $this->bc41Model->find($id);
        $payload = json_decode($this->bc41Model->find($id)['payload']);
        $detailBarang = $this->bc41Model->detailBarang($bc41['id'], $kodeBarang, $bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id']);
        $totalBarang = count($this->bc41Model->barang($bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id']));

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
            'barang' => $this->bc41Model->detailBarang($bc41['id'], $kodeBarang, $bc41['sales_order_lain_id'], $bc41['pengembalian_barang_id']),
            'kodeHS' => $this->hsCodeModel->findAll(),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', "Jenis Kemasan")->findAll(),
            'kodeSatuanBarang' => null,
            'bahanBakuLokal' => [],
        ];

        $data['kodeSatuanBarang'] = $this->metaDataModel->where('name', "Kode Satuan BC")->where('value', $data['barang']['bcDetail']->kodeSatuanBarang)->findAll();

        // APPEND BAHAN BAKU LOKAL & EKSPOR
        foreach ($data['barang']['bcDetail']->bahanBaku as $i => $b) {
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
    public function viewOutstanding()
    {
        return view('BeaCukai/BC-41/bc41outstanding');
    }

    public function allOutstanding()
    {
        $salesOrderLainUsed = $this->bc41Model
            ->select('sales_order_lain_id')
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $salesOrderLainAll = $this->salesOrderLainModel->where('company_id', $this->this_company_id)->where('deletedAt', null)
            ->where('bc_id', '54')
            ->findAll();


        $allSalesOrderLainIdArr = [];
        $salesOrderLainIdUsedArr = [];
        $salesOrderLainIdNotUsedArr = [];

        foreach ($salesOrderLainUsed as $s) {
            array_push($salesOrderLainIdUsedArr, $s['sales_order_lain_id']);
        }
        foreach ($salesOrderLainAll as $p) {
            array_push($allSalesOrderLainIdArr, $p['id']);
        }
        $salesOrderLainIdNotUsedArr = array_diff($allSalesOrderLainIdArr, $salesOrderLainIdUsedArr);
        $listSalesOrderLain = [];
        foreach ($salesOrderLainIdNotUsedArr as $id) {
            $sol = $this->salesOrderLainModel
                ->select('
                    sales_order_lain.id,
                    customers.name,
                    sales_order_lain.no_sales_order,
                    divisis.divisi,
                    warehouses.warehouse_name,
                    sales_order_lain.tanggal
                ')
                ->join('customers', 'customers.id = sales_order_lain.customer_id')
                ->join('divisis', 'divisis.id = sales_order_lain.divisi_id')
                ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id')
                ->where('sales_order_lain.bc_id', '54')
                ->where('sales_order_lain.id', $id)
                ->first();

            $sold = $this->salesOrderLainDetailModel
                ->select('count(*) as jumlah_barang, sum(total_harga) as harga_barang')
                ->where('sales_order_lain_detail.sales_order_lain_id', $id)
                ->first();
            if ($sol != null) {
                array_push($listSalesOrderLain, [
                    'asal_pengeluaran' => "PENJUALAN",
                    'id' => encrypt($sol['id']),
                    'customer' => $sol['name'],
                    'no_sales_order' => $sol['no_sales_order'],
                    'divisi' => $sol['divisi'],
                    'warehouse_name' => $sol['warehouse_name'],
                    'tanggal' => date("d/m/Y", strtotime($sol['tanggal'])),
                    'jumlah_barang' => $sold['jumlah_barang'],
                    'harga_barang' => number_format($sold['harga_barang'], 2)
                ]);
            }
        }

        // RETUR
        $pengembalianBarangUsed = $this->bc41Model
            ->select('pengembalian_barang_id')
            ->where('pengembalian_barang_id !=', null)
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        $pengembalianBarangAll = $this->pengembalianBarangModel
            ->where('company_id', $this->this_company_id)
            ->where('status_post', 'FINISH')
            ->where('deletedAt', null)
            ->where('bc_pengeluaran_id', '54')
            ->findAll();

        $allPengembalianBarangIdArr = [];
        $pengembalianBarangUsedArr = [];
        $pengembalianBarangNotUsedArr = [];

        foreach ($pengembalianBarangUsed as $p) {
            array_push($pengembalianBarangUsedArr, $p['pengembalian_barang_id']);
        }
        foreach ($pengembalianBarangAll as $p) {
            array_push($allPengembalianBarangIdArr, $p['id']);
        }

        $pengembalianBarangNotUsedArr = array_diff($allPengembalianBarangIdArr, $pengembalianBarangUsedArr);

        $listPengembalianBarang = [];

        foreach ($pengembalianBarangNotUsedArr as $p) {
            $pengembalianBarang = $this->pengembalianBarangModel
                ->select('pengembalian_barang.*,divisis.divisi,warehouses.warehouse_name,suppliers.name as supplier_name')
                ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
                ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                ->where('pengembalian_barang.id', $p)
                ->first();

            $pengembalianBarangDetail = $this->pengembalianBarangDetailModel
                ->select('harga,harga_harian,harga_bulanan,jumlah_return')
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.id = pengembalian_barang_detail.penerimaan_barang_detail_id')
                ->where('pengembalian_barang_detail.pengembalian_barang_id', $p)
                ->where('pengembalian_barang_detail.deletedAt', null)
                ->findAll();
            if ($pengembalianBarang) {
                $jumlahBarang = count($pengembalianBarangDetail);
                $hargaBarang = 0;

                foreach ($pengembalianBarangDetail as $d) {
                    $hargaBarang += $d['harga'] + $d['harga_harian'] + $d['harga_bulanan'];
                }

                array_push($listPengembalianBarang, [
                    'asal_pengeluaran' => "RETUR",
                    'id' => ($pengembalianBarang['id']),
                    'customer' => $pengembalianBarang['supplier_name'],
                    'no_sales_order' => $pengembalianBarang['no_surat_jalan'],
                    'divisi' => $pengembalianBarang['divisi'],
                    'warehouse_name' => $pengembalianBarang['warehouse_name'],
                    'tanggal' => date("d/m/Y", strtotime($pengembalianBarang['tanggal_surat_jalan'])),
                    'jumlah_barang' => $jumlahBarang,
                    'harga_barang' => number_format(($hargaBarang * $jumlahBarang), 2)
                ]);
            }
        }


        return json_encode(array_merge($listPengembalianBarang, $listSalesOrderLain));
    }

    public function OutstandingSheet()
    {
        $list = json_decode($this->allOutstanding());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Asal Pengeluaran')
            ->setCellValue('C1', 'Nama Customer / Supplier')
            ->setCellValue('D1', 'No Sales Order / No Surat Jalan')
            ->setCellValue('E1', 'Department / Warehouse Pengeluaran')
            ->setCellValue('F1', 'Tanggal')
            ->setCellValue('G1', 'Jumlah Barang')
            ->setCellValue('H1', 'Nilai Barang');
        $no = 1;
        $column = 2;

        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->asal_pengeluaran)
                ->setCellValue('C' . $column,  $l->customer)
                ->setCellValue('D' . $column,  $l->no_sales_order)
                ->setCellValue('E' . $column,  $l->divisi . " / " . $l->warehouse_name)
                ->setCellValue('F' . $column,  $l->tanggal)
                ->setCellValue('G' . $column,  $l->jumlah_barang)
                ->setCellValue('H' . $column,  $l->harga_barang);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC25';
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Outstanding-BC-4.1';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
