<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\AMPurchaseOrderModel;
use App\Models\BC30Model;
use App\Models\CeisaSettingModel;
use App\Models\MetadataModel;
use App\Models\StockDetail2Model;
use App\Models\StuffingInternasionalModel;
use App\Models\StuffingLokalModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\PengusahaTPBModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BcPengeluaranBarangModel;
use App\Models\CountryModel;
use App\Models\CustomerModel;
use App\Models\DivisisModel;
use App\Models\HsCodesModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengembalianBarangDetailModel;
use App\Models\PengembalianBarangModel;
use App\Models\RMImportPOModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\SalesOrderLainModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampDetailModel;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54

class BC30 extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $bc30Model;
    protected $metaDataModel;
    protected $stuffingLokalModel;
    protected $stuffingInternasionalModel;
    protected $stockDetail2Model;
    protected $salesOrderModel;
    protected $salesOrderDetailModel;
    protected $salesOrderExportModel;
    protected $salesOrderExportDetailModel;
    protected $kantorBeaCukaiModel;
    protected $pengusahaTPBModel;
    protected $barangMasterSpesifikasiModel;
    protected $countryModel;
    protected $hsCodeModel;
    protected $pengembalianBarangModel;
    protected $pengembalianBarangDetailModel;
    protected $salesOrderLainModel;
    protected $rmImportPosModel;
    protected $amPurchaseOrderModel;
    protected $salesOrderLainDetailModel;
    protected $penerimaanBarangModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $customerModel;
    protected $divisiModel;
    protected $satuanModel;
    protected $bcPengeluaranBarangModel;
    protected $stockRevampDetailModel;

    public function __construct()
    {
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->bc30Model = new BC30Model();
        $this->metaDataModel = new MetadataModel();
        $this->stuffingLokalModel = new StuffingLokalModel();
        $this->stuffingInternasionalModel = new StuffingInternasionalModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->salesOrderModel = new SalesOrderModel();
        $this->salesOrderDetailModel = new SalesOrderDetailModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $this->kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->countryModel = new CountryModel();
        $this->hsCodeModel = new HsCodesModel();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
        $this->rmImportPosModel = new RMImportPOModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->customerModel = new CustomerModel();
        $this->divisiModel = new DivisisModel();
        $this->satuanModel = new SatuansModel();
        $this->bcPengeluaranBarangModel = new BcPengeluaranBarangModel();
        $this->stockRevampDetailModel = new StockRevampDetailModel();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        $akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $customer = $this->customerModel->getCustomerByCompany($this->this_company_id, "INTERNASIONAL");
        $data = [
            'akunCeisa' => $akunCeisa,
            'customer' => $customer
        ];

        return view('BeaCukai/bc-30/index', $data);
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

        $data = $this->bc30Model->getList(
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
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $jenisPengeluaran = $this->request->getVar('jenis_pengeluaran');
            $referencePenerimaId = $this->request->getVar('reference_penerima_id');

            $noAju = $this->generateNomorAju($tanggal);
            $payload = $this->get_payload();

            $payload = json_decode($payload);
            $payload->nomorAju = $noAju;
            $payload->tanggalAju = $tanggal;
            $payload->idPengguna = "NPWP";

            $payloadUpdate = json_encode($payload);

            $id = $this->bc30Model->insert([
                'company_id' => $this->this_company_id,
                'reference_penerima_id' => $referencePenerimaId,
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

            $db->transCommit();
            return response()->setJSON([
                'message' => 'Dokumen berhasil dibuat',
                'id' => encrypt($id),
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function updateNoAju()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $noAju = $this->request->getVar('no_pengajuan');
            $this->bc30Model->update($id, ['no_aju' => $noAju]);
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
            $noAju = $this->request->getVar('no_pengajuan');
            $noDaftar = $this->request->getVar('no_daftar');
            $multipleReferenceIdArr = $this->request->getVar('multiple_reference_id');
            $bc30 = $this->bc30Model->where('id', $id)->first();
            $multipleReferenceNo = null;
            $multipleReferenceId = null;

            if (!empty($multipleReferenceIdArr) && count($multipleReferenceIdArr) != 0) {
                if ($bc30['jenis_pengeluaran'] == "ORDER FORM EKSPOR") {
                    // ORDER FORM EKSPOR
                    $multipleReferenceNo = $this->bcPengeluaranBarangModel->getReferensiNoPengeluaranOrderFormEkspor(
                        $multipleReferenceIdArr
                    );
                } else {
                    // SAMPLE
                    $multipleReferenceNo = $this->bcPengeluaranBarangModel->getReferensiNoPengeluaranSample(
                        $multipleReferenceIdArr
                    );
                }
                $multipleReferenceId = "[" . implode(",", $multipleReferenceIdArr) . "]";
            }


            $this->bc30Model->update($id, [
                'tanggal' => $tanggal,
                'no_aju' => $noAju,
                'no_daftar' => $noDaftar,
                'multiple_reference_id' => $multipleReferenceId,
                'multiple_reference_no' => $multipleReferenceNo,
            ]);

            $listStock = json_decode($this->request->getVar('listStock'));
            $this->bcPengeluaranBarangModel->where('bc_pengeluaran_id', $id)->where('tipe_bc', "BC 3.0")->delete(null, true);
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
                    'tipe_bc'            => "BC 3.0",
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
        $bc30 = $this->bc30Model->detail($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description !=', "kemasan")
            ->findAll();

        $divisi = $this->divisiModel->getDivisiAccess();
        $satuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->where('deletedAt', null)->findAll();
        $dataCustomer = $this->customerModel->where('id', $bc30['reference_penerima_id'])->findAll();
        $dataBarang = $this->bcPengeluaranBarangModel->getDetailBarang(
            $id,
            "BC 3.0"
        );
        $dataReferencePengeluaran = [];
        $dataBarangSalesEkspor = [];
        $multipleReferenceIds = json_decode($bc30['multiple_reference_id'], true) ?? [];

        if ($bc30['jenis_pengeluaran'] == "ORDER FORM EKSPOR") {
            // TARIK DARI ORDER FORM
            $dataReferencePengeluaranNotUsed = $this->bc30Model->getReferencePengeluaranOrderForm(
                $bc30['reference_penerima_id'],
                $bc30['company_id']
            );
            $dataReferencePengeluaranUsed = $this->bc30Model->getReferencePengeluaranOrderFormSelected(
                $bc30['id'],
                $bc30['company_id']
            );
            $dataReferencePengeluaran = array_merge(
                $dataReferencePengeluaranNotUsed,
                $dataReferencePengeluaranUsed
            );

            if (count($multipleReferenceIds) != 0) {
                $dataBarangSalesEkspor = $this->bc30Model->getListBarangSalesOrderEkspor(
                    $multipleReferenceIds
                );
            }
        } else {
            // TARIK DARI SAMPLE
            $dataReferencePengeluaranNotUsed = $this->bc30Model->getReferencePengeluaranSample(
                $bc30['reference_penerima_id'],
                $bc30['company_id']
            );
            $dataReferencePengeluaranUsed = $this->bc30Model->getReferencePengeluaranSampleSelected(
                $bc30['id'],
                $bc30['company_id']
            );
            $dataReferencePengeluaran = array_merge(
                $dataReferencePengeluaranNotUsed,
                $dataReferencePengeluaranUsed
            );

            if (count($multipleReferenceIds) != 0) {
                $dataBarangSalesEkspor = $this->bc30Model->getListBarangSample(
                    $multipleReferenceIds
                );
            }
        }

        $data = [
            'bc30' => $bc30,
            'dataCustomer' => $dataCustomer,
            'tipeBarang' => $dataTipeBarang,
            'dataReferencePengeluaran' => $dataReferencePengeluaran,
            'divisi' => $divisi,
            'dataSatuan' => $satuan,
            'dataValuta' => $dataValuta,
            'noAju' => $bc30 == null ? $this->generateNomorAju($bc30['tanggal']) : $bc30['no_aju'],
            'dataBarang' => $dataBarang,
            'dataBarangSalesEkspor' => $dataBarangSalesEkspor,
            'multipleReferenceIds' => $multipleReferenceIds
        ];

        return view('BeaCukai/bc-30/form', $data);
    }

    public function delete()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $this->bc30Model->delete($id);
            $this->bcPengeluaranBarangModel->where('bc_pengeluaran_id', $id)->where('tipe_bc', "2.5")->delete(null, false);
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Dokumen BC 3.0 Berhasil Dihapus"
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
        $this->bc30Model->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 3.0 Berhasil Diposting",
            'token' => csrf_hash()
        ]);
    }

    public function unposting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->bc30Model->update($id, ['status_posting' => '0']);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 3.0 Berhasil Diunposting",
            'token' => csrf_hash()
        ]);
    }

    public function dropdownSalesOrder()
    {
        $typeReference = $this->request->getVar('type_reference');
        $referenceId = $this->request->getVar('reference_id');

        if ($typeReference == "ORDER FORM EKSPOR") {
            $result = $this->bc30Model->getListSalesOrder(
                $this->this_company_id,
            );
        } elseif ($typeReference == "RETUR PEMBELIAN") {
            $result = $this->bc30Model->getListReturPembelian(
                $this->this_company_id,
                $referenceId
            );
        } else if ($typeReference == "ORDER FORM LAIN") {
            $result = $this->bc30Model->getListSalesOrderLain(
                $this->this_company_id,
                $referenceId
            );
        } else {
            $result = [];
        }

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['reference_id'] = $result[$i]['sales_order_id'];
            $result[$i]['no_reference'] = $result[$i]['no_sales_order'];
        }

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListBarangSalesEkspor()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $bc30 = $this->bc30Model->where('id', $id)->first();
            $multipleReferenceId = $this->request->getVar('multiple_reference_id');
            $multipleReferenceIdArr = json_decode($multipleReferenceId);
            if (count($multipleReferenceIdArr) == 0) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => true,
                    'data' => []
                ]);
            }

            if ($bc30['jenis_pengeluaran'] == "ORDER FORM EKSPOR") {
                $dataResult = $this->bc30Model->getListBarangSalesOrderEkspor(
                    $multipleReferenceIdArr
                );
            } else {
                $dataResult = $this->bc30Model->getListBarangSample(
                    $multipleReferenceIdArr
                );
            }

            return response()->setJSON([
                'data' => $dataResult,
                'token' => csrf_hash(),
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
        $typeReference = $this->request->getVar('type_reference');
        $referenceId = $this->request->getVar('reference_id');

        if (empty($typeReference) || empty($referenceId)) {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => true
            ]);
        }

        if ($typeReference == "ORDER FORM EKSPOR") {
            $result = $this->bc30Model->getListBarang(
                $referenceId,
                $typeReference
            );
        } elseif ($typeReference == "RETUR PEMBELIAN") {
            $result = $this->bc30Model->getListBarangReturEkspor(
                $referenceId
            );
        } elseif ($typeReference == "ORDER FORM LAIN") {
            $result = $this->bc30Model->getListBarangOrderFormLain(
                $referenceId
            );
        }

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
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

        $kodeDokumenbc30Static = $this->metaDataModel
            ->where('name', "Kode BC30 Static")
            ->first();

        $kodeKantorStatic = $ceisaSetting['kode_unik']
            ? $ceisaSetting['kode_unik']
            : $ceisaSetting['kode_kantor_pabean'];

        $tanggalAju = date('Ymd', strtotime($tanggalDokumen));
        $tahunAjuSekarang = date('Y', strtotime($tanggalDokumen));

        // Ambil BC40 terakhir berdasarkan urutan no_aju terbaru
        $bc30Last = $this->bc30Model
            ->whereIn('company_id', $company_id_arr)
            ->orderBy('tanggal', "DESC")
            ->limit(1)
            ->first();

        // Default urutan
        $sequenceNoUrutPengajuan = "000001";

        if ($bc30Last && $bc30Last['no_aju']) {
            $arrNo = explode('-', $bc30Last['no_aju']);

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

        return $kodeDokumenbc30Static['value']
            . '-' . $kodeKantorStatic
            . '-' . $tanggalAju
            . '-' . $sequenceNoUrutPengajuan;
    }

    public function viewOutstanding()
    {
        return view('BeaCukai/bc-30/bc30outstanding');
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

        $dataQry = $this->bc30Model->getListOutstanding(
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
                'sales_order_export_id' => encrypt($d['sales_order_export_id']),
                'tujuan_pengeluaran' => $d['tujuan_pengeluaran'],
                'tanggal' => date('d/m/Y', strtotime($d['tanggal'])),
                'reference_no' => $d['reference_no'],
                'customer_name' => $d['customer_name'],
                'kode_barang' => $d['kode_barang'],
                'barang_name' => $d['barang_name'],
                'qty' => (float)$d['qty'],
                'kode_satuan' => $d['kode_satuan'],
                'valas_name' => $d['valas_name'],
                'total_harga_barang' => (float)$d['total_harga_barang'],
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

        $dataQry = $this->bc30Model->getListOutstanding(
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
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // ================================
        // Header kolom
        $headers = ['No', 'Tujuan Pengeluaran', 'Tanggal', 'Reference No', 'Customer', 'Kode Barang', 'Barang', 'Qty', 'Satuan', 'Valas', 'Nilai Barang'];
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
            $sheet->setCellValue('J' . $row, $d['valas_name']);
            $sheet->setCellValue('K' . $row, (float)$d['total_harga_barang']);

            // Rata kanan & format angka
            $sheet->getStyle('K' . $row)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('K' . $row)
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            // Border untuk setiap sel
            foreach (range('A', 'K') as $c) {
                $sheet->getStyle($c . $row)
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            $row++;
        }

        // ================================
        // Auto width kolom
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ================================
        // Export
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Outstanding_Ekspor_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    }

    //kirim ceisa
    public function kirimCeisa($id)
    {
        $id = decrypt($id);
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);

        // return response()->setJSON(
        //     $payload
        // );

        $res = $beacukaiApi->kirimDokumenBC($payload, false);
        if ($res['status'] == false) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal Kirim Ceisa Karena : " . $res['message'],
            ]);
        }
        // // UPDATE STATUS
        // $this->bc30Model->set('status_dokumen', "Sudah Kirim")->where('id', $id)->update();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 3.0 Berhasil Diposting",
            'res' => $res
        ]);
    }
    private function setFlashDataNavigatorSession($id)
    {
        $isCompleteFormHeader = $this->bc30Model->isCompleteFormHeader($id);
        $isCompleteFormEntitas = $this->bc30Model->isCompleteFormEntitas($id);
        $isCompleteFormDokumen = $this->bc30Model->isCompleteFormDokumen($id);
        $isCompleteFormPengangkut = $this->bc30Model->isCompleteFormPengangkut($id);
        $isCompleteFormPetiKemas = $this->bc30Model->isCompleteFormPetiKemas($id);
        $isCompleteFormTransaksi = $this->bc30Model->isCompleteFormTransaksi($id);
        $isCompleteFormBarang = $this->bc30Model->isCompleteFormBarang($id);
        $iscompleteFormKesiapanBarang = $this->bc30Model->iscompleteFormKesiapanBarang($id);
        $isCompleteFormPernyataan = $this->bc30Model->isCompleteFormPernyataan($id);


        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormTransaksi);
        session()->setFlashdata('isCompleteFormBarang', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPungutan', $isCompleteFormBarang);
        session()->setFlashdata('iscompleteFormKesiapanBarang', $iscompleteFormKesiapanBarang);
        session()->setFlashdata('isCompleteFormPernyataan', $isCompleteFormPernyataan);

        if (
            $isCompleteFormHeader && $isCompleteFormEntitas &&
            $isCompleteFormEntitas && $isCompleteFormDokumen && $isCompleteFormPengangkut &&
            $isCompleteFormPetiKemas && $isCompleteFormTransaksi && $iscompleteFormKesiapanBarang
        ) {
            $this->bc30Model->set('status_dokumen', "Siap Kirim")->where('id', $id)->update();
        } else {
            $this->bc30Model->set('status_dokumen', "Belum Lengkap")->where('id', $id)->update();
        }
    }

    //CEISA ROUTER
    public function header($id)
    {
        $id = decrypt($id);

        $bc30 = $this->bc30Model->find($id);
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();


        $this->setFlashDataNavigatorSession($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $data = [
            'bc30' => $bc30,
            'noAju' => $bc30 == null ? $this->generateNomorAju($bc30['tanggal']) : $bc30['no_aju'],
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'kodeLokasiBayar' => $this->metaDataModel->where('name', "KODE LOKASI BAYAR")->findAll(),
            'kodeTujuanTpb' => $this->metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTujuanPengiriman' => $this->metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', 40)->where('deletedAt', null)->findAll(),
            'kodeCaraBayar' => $this->metaDataModel->where('name', "CARA BAYAR")->findAll(),
            'selectedKantor' => $ceisaSetting['kode_kantor_pabean'],
            'payload' => json_decode($bc30['payload'])
        ];


        return view('BeaCukai/bc-30/form-header', $data);
    }

    public function updateHeader()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);

        $payload->asalData = "S";
        $payload->nomorAju =  str_replace('-', '', $this->request->getVar('nomorAju'));
        $payload->tanggalAju = getDateFromNomorAju($this->request->getVar('nomorAju'));
        $payload->kodeKantor = $this->request->getVar('kodeKantorMuat');
        $payload->kodeKantorMuat = $this->request->getVar('kodeKantorMuat');
        $payload->kodeJenisEkspor = $this->request->getVar('jenisEkspor');
        $payload->kodeKategoriEkspor = $this->request->getVar('kategoriEkspor');
        $payload->kodeKantorEkspor = $this->request->getVar('kodeKantorEkspor');
        $payload->kodePelEkspor = $this->request->getVar('kodePelabuhan');
        $payload->kodeCaraDagang = $this->request->getVar('caraDagang');
        $payload->kodeCaraBayar = $this->request->getVar('caraBayar');
        $payload->flagMigas = $this->request->getVar('komoditi');
        $payload->flagCurah = $this->request->getVar('curah');
        $payload->seri = 1;


        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Header berhasil disimpan"
        ]);
    }

    public function entitas($id)
    {
        $id = decrypt($id);

        $bc30 = $this->bc30Model->find($id);
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->findAll();


        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);

        $payload = json_decode($bc30['payload']);
        // JIKA MASIH KOSONG SET DULU BOSQ
        if (count($payload->entitas) == 0) {
            $payload->entitas = [
                [
                    //eksportir
                    "alamatEntitas" => "",
                    "kodeEntitas" => "2",
                    "nibEntitas" => "",
                    "kodeJenisIdentitas" => "",
                    "namaEntitas" => "",
                    "nomorIdentitas" => "",
                    "seriEntitas" => "1",
                ],

                [
                    //penerima

                    "alamatEntitas" => "",
                    "kodeEntitas" => '8',
                    "kodeNegara" => "",
                    "namaEntitas" => "",
                    "seriEntitas" => '2',

                ],
                [
                    //pembeli
                    "alamatEntitas" => "",
                    "kodeEntitas" => '6',
                    "kodeNegara" => "",
                    "namaEntitas" => "",
                    "seriEntitas" => '3',
                ],

            ];
            $this->bc30Model->update($id, ['payload' => json_encode($payload)]);
        }
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $pemilikBarang = [];
        $nomor = 1;
        $indexPembeli = 0;
        $indexPenerima = 0;

        $indexEntitas = count($payload->entitas);
        foreach ($payload->entitas as $i => $e) {
            if ($payload->entitas[$i]->kodeEntitas == 6) {
                $indexPembeli = $i;
            }
            if ($payload->entitas[$i]->kodeEntitas == 8) {
                $indexPenerima = $i;
            }
            if ($payload->entitas[$i]->kodeEntitas == 7) {
                array_push($pemilikBarang, $e);
            }
        }

        // var_dump($indexPembeli);
        // die;
        $data = [
            'bc30' => $bc30,
            'pengusahaTPB' => $pengusahaTPB,
            'payload' => $payload,
            'kodeNegaraAsal' => $this->countryModel->findAll(),
            'pemilik' => $pemilikBarang,
            'indexPembeli' => $indexPembeli,
            'indexPenerima' => $indexPenerima,
            'indexEntitas' => $indexEntitas
        ];


        return view('BeaCukai/bc-30/form-entitas', $data);
    }

    public function updateEntitas()
    {
        $id = decrypt($this->request->getVar('id'));

        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);


        $payload->entitas[0] = [
            //eksportir
            "alamatEntitas" => $this->request->getVar('entitas_alamat_eksportir'),
            "kodeEntitas" => "2",
            "kodeJenisIdentitas" => $this->request->getVar('entitas_kode_jenis_identitas_eksportir'),
            "namaEntitas" => $this->request->getVar('entitas_nama_eksportir'),
            "nomorIdentitas" => $this->request->getVar('entitas_nomor_eksportir'),
            "seriEntitas" => 1,
        ];


        $payload->entitas[1] = [
            //penerima

            "alamatEntitas" => $this->request->getVar('entitas_alamat_penerima'),
            "kodeEntitas" => '8',
            "kodeNegara" => decrypt($this->request->getVar('entitas_kode_negara_penerima')),
            "namaEntitas" => $this->request->getVar('entitas_nama_penerima'),
            "seriEntitas" => 2,
        ];

        $payload->entitas[2] = [
            //pembeli
            "alamatEntitas" => $this->request->getVar('entitas_alamat_pembeli'),
            "kodeEntitas" => '6',
            "kodeNegara" => decrypt($this->request->getVar('entitas_kode_negara_pembeli')),
            "namaEntitas" => $this->request->getVar('entitas_nama_pembeli'),
            "seriEntitas" => 3,
        ];




        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);
        return response()->setJSON([
            'status' => true,
            'message' => "Entitas berhasil disimpan"
        ]);
    }

    public function updateEntitasPemilik()
    {
        $id = decrypt($this->request->getVar('id'));

        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);
        $lastIndex = count($payload->entitas) - 2;
        $arrayEntitasPemilik = [
            //pemilik
            "alamatEntitas" => $this->request->getVar('tambah_alamat_pemilik_barang'),
            "kodeEntitas" => "7",
            "kodeJenisIdentitas" => $this->request->getVar('tambah_pemilik_kode_jenis_entitas'),
            "namaEntitas" => $this->request->getVar('tambah_nama_pemilik_barang'),
            "nomorIdentitas" => $this->request->getVar('tambah_nomor_pemilik_barang'),
            "nibEntitas" => $this->request->getVar('tambah_nib_pemilik_barang') ? $this->request->getVar('tambah_nib_pemilik_barang') : "",
            "seriEntitas" => $lastIndex + 3,
        ];
        array_splice($payload->entitas, $lastIndex, 0, [$arrayEntitasPemilik]);

        // $payload->entitas[$lastIndex] = [
        //     //pemilik
        //     "alamatEntitas" => $this->request->getVar('tambah_alamat_pemilik_barang'),
        //     "kodeEntitas" => "7",
        //     "kodeJenisIdentitas" => $this->request->getVar('tambah_pemilik_kode_jenis_entitas'),
        //     "namaEntitas" => $this->request->getVar('tambah_nama_pemilik_barang'),
        //     "nomorIdentitas" => $this->request->getVar('tambah_nomor_pemilik_barang'),
        //     "nibEntitas" => $this->request->getVar('tambah_nib_pemilik_barang'),
        //     "seriEntitas" => $lastIndex,
        // ];

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Entitas Pemilik berhasil disimpan"
        ]);
    }

    public function deleteEntitasPemilik()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete') + 3;

        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->entitas[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function dokumen($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        if (!is_array($payload->dokumen)) {
            $payload->dokumen = [];
            $this->bc30Model->update($id, ['payload' => json_encode($payload)]);
        }
        $dokumen = [];
        foreach (json_decode($this->bc30Model->find($id)['payload'])->dokumen as $d) {
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
            'bc30' => $bc30,
            'kodeDokumen' => $this->metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'dokumen' => $dokumen,
        ];

        return view('BeaCukai/bc-30/form-dokumen', $data);
    }

    public function updateDokumen()
    {
        // INVOICE = 30
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);
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
        if ($seriDokumen == 2) {
            // HARUS PACKIGN LIST
            if ($kodeDokumen != 217) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Dokumen seri kedua wajib packing list ",
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

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil disimpan"
        ]);
    }

    public function deleteDokumen()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->dokumen[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }
    public function pengangkut($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);
        $pengangkutData = [];

        foreach (json_decode($this->bc30Model->find($id)['payload'])->pengangkut as $p) {
            $pengangkutDetail = $this->metaDataModel->where('name', "Pengangkutan")->where('description', $p->kodeCaraAngkut)->first();
            $negaraDetail = $this->countryModel->where('code', $p->kodeBendera)->first();
            $valuePengangkut = ($pengangkutDetail == null) ? "" : $pengangkutDetail['value'];
            $countryName = ($negaraDetail == null) ? "" : $negaraDetail['country_name'];
            array_push($pengangkutData, [
                'seriPengangkut' => $p->seriPengangkut,
                'kodeBendera' => $p->kodeBendera . " - " . $countryName,
                'nomorPengangkut' => $p->nomorPengangkut,
                'kodeCaraAngkut' => $p->kodeCaraAngkut . " - " . $valuePengangkut,
                'namaPengangkut' => $p->namaPengangkut
            ]);
        }

        $data = [
            'pengangkut' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'bc30' => $bc30,
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'selectedKantor' => $this->metaDataModel->where('name', "Kode Kantor Pabean Pengawas Static")->first(),
            'kodeNegaraAsal' => $this->countryModel->findAll(),
            'kodePengangkutan' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'payload' => $payload,
            'pengangkutData' => $pengangkutData
        ];



        return view('BeaCukai/bc-30/form-pengangkut', $data);
    }
    //table pengangkutan
    public function createPengangkutanAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $indexLast = count($payload->pengangkut) == 0 ? 0 : count($payload->pengangkut) - 1;
        $seriPengangkut = count($payload->pengangkut) == 0 ? 1 : $payload->pengangkut[$indexLast]->seriPengangkut + 1;
        $kodeNegara = decrypt($this->request->getVar('pengangkutan_negara'));

        array_push($payload->pengangkut, [
            "kodeBendera" => $kodeNegara,
            "namaPengangkut" => $this->request->getVar('nama_sarana_angkut'),
            'nomorPengangkut' => $this->request->getVar('nomor_pengangkutan'),
            'kodeCaraAngkut' => $this->request->getVar('cara_pengangkutan'),
            'seriPengangkut' => $seriPengangkut
        ]);


        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil disimpan"
        ]);
    }

    public function deletePengangkutAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->pengangkut[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengangkut berhasil dihapus"
        ]);
    }


    public function pengangkutUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);


        $payload->kodeTps = "" . decrypt($this->request->getVar('pengangkut_tempat_penimbuhan'));
        $payload->kodePelMuat = $this->request->getVar('pengangkut_muat_asal');
        $payload->kodePelEkspor = $this->request->getVar('pengangkut_muat_ekspor');
        $payload->kodePelBongkar = $this->request->getVar('pengangkut_bongkar');
        $payload->kodePelTujuan = $this->request->getVar('pengangkut_tujuan');
        $payload->kodeNegaraTujuan = decrypt($this->request->getVar('negara_tujuan_ekspor'));
        $payload->tanggalEkspor = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('pengangkutan_tanggal_perkiraan_ekspor'))));
        $payload->kodeLokasi = $this->request->getVar('pengangkutan_lokasi_pemeriksaan');
        $payload->tanggalPeriksa = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('pengangkutan_tanggal_pemeriksa'))));
        $payload->kodeKantorPeriksa = "" . decrypt($this->request->getVar('pengangkutan_kantor_pemeriksa'));
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);


        return response()->setJSON([
            'status' => true,
            'message' => "Pengangkut berhasil disimpan"
        ]);
    }

    public function kemasanPetiKemas($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $indexLastKemasan = count($payload->kemasan) == 0 ? 0 : count($payload->kemasan) - 1;
        $indexLastKontainer = count($payload->kontainer) == 0 ? 0 : count($payload->kontainer) - 1;
        $seriKemasan = count($payload->kemasan) == 0 ? 1 : $payload->kemasan[$indexLastKemasan]->seriKemasan + 1;
        $seriKontainer = count($payload->kontainer) == 0 ? 1 : $payload->kontainer[$indexLastKontainer]->seriKontainer + 1;

        $dataKemasan = [];
        $dataKontainer = [];

        // KEMASAN
        foreach ($payload->kemasan as $k) {
            $jenisKemasanDetail = $this->metaDataModel->where('name', 'Jenis Kemasan')->where('description', $k->kodeJenisKemasan)->first();


            array_push($dataKemasan, [
                'jumlahKemasan' => $k->jumlahKemasan,
                'kodeJenisKemasan' => $k->kodeJenisKemasan . " - " . strtoupper($jenisKemasanDetail['value']),
                'merkKemasan' => $k->merkKemasan,
                'seriKemasan' => $k->seriKemasan,
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
            'bc30' => $bc30,
            'payload' => $payload,
            'seriKemasan' => $seriKemasan,
            'seriKontainer' => $seriKontainer,
            // 'dropdownKemasan' => $this->bc30Model->dropdownKemasan($bc30['sales_order_id']),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $this->metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'dataKemasan' => $dataKemasan,
            'dataKontainer' => $dataKontainer,
        ];

        return view('BeaCukai/bc-30/form-kemasan-peti-kemas', $data);
    }

    public function kemasanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);

        array_push($payload->kemasan, [
            'jumlahKemasan' => (float)$this->request->getVar('kemasan_jumlah_kemasan'),
            'kodeJenisKemasan' => $this->request->getVar('kemasan_jenis_kemasan'),
            'merkKemasan' => $this->request->getVar('kemasan_merk_kemasan'),
            'seriKemasan' => (int)$this->request->getVar('kemasan_seri_kemasan'),
            // 'kemasanInventoriId' => $this->request->getVar('kemasan_inventori_id'),
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kemasan berhasil disimpan"
        ]);
    }

    public function deleteKemasan()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->kemasan[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function kontainerUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);

        array_push($payload->kontainer, [
            'kodeJenisKontainer' => $this->request->getVar('kontainer_jenis'),
            'kodeTipeKontainer' => $this->request->getVar('kontainer_tipe'),
            'kodeUkuranKontainer' => $this->request->getVar('kontainer_ukuran'),
            'nomorKontainer' => $this->request->getVar('kontainer_nomor'),
            'seriKontainer' => (int)$this->request->getVar('kontainer_seri'),
        ]);

        $payload->jumlahKontainer = count($payload->kontainer);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil disimpan"
        ]);
    }

    public function deleteKontainer()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->kontainer[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil dihapus"
        ]);
    }

    public function transaksi($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $data = [
            'kodeValuta' => $this->metaDataModel->where('name', "Valuta")->findAll(),
            'kodeAsuransi' => $this->metaDataModel->where('name', "Asuransi")->findAll(),
            'kodeIncoterm' => $this->metaDataModel->where('name', "Kode Incoterm BC")->findAll(),
            'bc30' => $bc30,
            'payload' => $payload,
        ];

        return view('BeaCukai/bc-30/form-transaksi', $data);
    }

    public function transaksiUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);

        if ((float)$this->request->getVar('berat_bruto') < (float) $this->request->getVar('berat_netto')) {
            return response()->setJSON([
                'status' => false,
                'message' => "berat bruto Harus Lebih besar daripada berat netto"
            ]);
        }
        $payload->kodeValuta = $this->request->getVar('harga_kode_valuta');
        $payload->ndpbm = (float)($this->request->getVar('harga_ndpbm'));
        $payload->kodeIncoterm = $this->request->getVar('transaksi_kode_incoterm');
        $payload->cif = intval($this->request->getVar('harga_cif'));
        $payload->freight = (float) $this->request->getVar('freight');
        $payload->kodeAsuransi = $this->request->getVar('transaksi_kode_asuransi');
        $payload->asuransi = (float) $this->request->getVar('tambah_nomor_pemilik_barang');
        $payload->bruto = (float)$this->request->getVar('berat_bruto');
        $payload->netto = (float) $this->request->getVar('berat_netto');
        $payload->nilaiMaklon = (float) $this->request->getVar('nilai_maklon');
        $payload->totalDanaSawit = (float)$this->request->getVar('nilai_pungutan_sawit');

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);


        return response()->setJSON([
            'status' => true,
            'message' => "Transaksi berhasil ditambah"
        ]);
    }

    public function saveBankDevisa()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);


        $kodeBank = $this->request->getVar('tambah-kode-bank-devisa');
        $bankData = $this->metaDataModel->where('name', 'Kode Bank')->where('value', $kodeBank)->first();
        $seriBankLast = count($payload->bankDevisa) == 0 ? 0 : count($payload->bankDevisa);

        if ($bankData == null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Tidak ada Bank",
                'status' => false
            ]);
        }

        array_push($payload->bankDevisa, [
            'kodeBank' => $kodeBank,
            'namaBank' => $bankData['description'],
            'seriBank' => $seriBankLast + 1,
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bank Devisa berhasil ditambah"
        ]);
    }

    public function deleteBankDevisa()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->bankDevisa[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bank Devisa berhasil dihapus"
        ]);
    }


    public function barang($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        if ($bc30['tipe_sales_order'] == "ORDER FORM EKSPOR") {
            $referenceId = $bc30['sales_order_id'];
        } elseif ($bc30['tipe_sales_order']  == "ORDER FORM LAIN") {
            $referenceId = $bc30['sales_order_lain_id'];
        } else {
            $referenceId = $bc30['pengembalian_barang_id'];
        }

        $data = [
            'bc30' => $bc30,
            'payload' => $payload,
            'barang' =>  $this->bc30Model->barang(
                $referenceId,
                $bc30['tipe_sales_order']
            )
        ];
        // var_dump($data['barang']);
        // die;

        return view('BeaCukai/bc-30/form-barang', $data);
    }


    public function barangDetail($id, $kodeBarang)
    {
        $id = decrypt($id);
        $kodeBarang = decrypt($kodeBarang);
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $detailBarang = $this->bc30Model->detailBarang($bc30['id'], $kodeBarang, $bc30['sales_order_id']);
        $totalBarang = count($this->bc30Model->barang($bc30['sales_order_id'], $bc30['tipe_sales_order']));
        $this->setFlashDataNavigatorSession($id);

        if ($detailBarang['bcDetail'] == null) {
            // MASIH KOSONG
            $indexLast = count($payload->barang) == 0 ? 0 : count($payload->barang) - 1;
            $seriBarang = count($payload->barang) == 0 ? 1 : $payload->barang[$indexLast]->seriBarang + 1;

            // $ndpbm = $payload->ndpbm / $totalBarang;
            // $cif = $payload->cif / $totalBarang;
            // $bruto = $payload->bruto / $totalBarang;

            array_push($payload->barang, [
                'cif' => 0,
                'cifRupiah' => 0,
                'fob' => 0,
                'hargaEkspor' => 0,
                'hargaPatokan' => 0,
                'hargaPerolehan' => 0,
                'hargaSatuan' => 0,
                'jumlahKemasan' => 0,
                'jumlahSatuan' =>  (float)$detailBarang['barangDetail']['qty_keluar'],
                'kodeAsalBahanBaku' => "",
                'kodeBarang' => $kodeBarang,
                'kodeDaerahAsal' => "",
                'kodeDokumen' => "30",
                'kodeJenisKemasan' => "",
                'kodeNegaraAsal' => "",
                'kodeSatuanBarang' => $detailBarang['barangDetail']['kode_satuan_internal'],
                'merk' => "",
                'ndpbm' => 0,
                'netto' => 0,
                'nilaiBarang' => 0,
                'nilaiDanaSawit' => 0,
                'posTarif' => "",
                'seriBarang' => $seriBarang,
                'spesifikasiLain' => "",
                'tipe' => "",
                'ukuran' => "",
                'uraian' => "",
                'volume' => "",
                'barangTarif' => [],
                'barangDokumen' => [],
                'barangPemilik' => []
            ]);


            $this->bc30Model->update($id, ['payload' => json_encode($payload)]);
        }
        $data = [
            'bc30' => $bc30,
            'barang' => $this->bc30Model->detailBarang($bc30['id'], $kodeBarang),
            'kodeHS' => $this->hsCodeModel->findAll(),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', "Jenis Kemasan")->findAll(),
            'kodeFasilitasTarif' => $this->metaDataModel
                ->where('name', "Kode Fasilitas Tarif BC")->whereIn('description', ['TIDAK DIPUNGUT', 'DIBEBASKAN', 'DITANGGUHKAN'])
                ->findAll(),
            'kodeJenisTarif' => $this->metaDataModel
                ->where('name', "Kode Jenis Tarif BC")
                ->findAll(),
            'kodeJenisPungutan' => $this->metaDataModel
                ->where('name', "Kode Jenis Pungutan BC")
                ->whereIn('value', ['BM', 'PPN', 'PPH'])
                ->findAll(),
            'kodeNegaraAsal' => $this->countryModel->findAll(),
            'kodeSatuanBarang' => null,
            'dokumen' => [],
            'dokumenSelected' => [], // SERI DOKUMEN YANG DI CHEKLIST
            'entitas' => [],
            'entitasSelected' => [],
        ];
        $data['kodeSatuanBarang'] = $this->metaDataModel->where('name', "Kode Satuan BC")->where('value', $data['barang']['bcDetail']->kodeSatuanBarang)->findAll();
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
        foreach ($data['barang']['bcDetail']->barangDokumen as $b) {
            array_push($data['dokumenSelected'], $b->seriDokumen);
        }
        //APPEND JENIS ENTITAS
        $indexEntitas = count($payload->entitas);
        foreach ($payload->entitas as $i => $e) {
            if ($i > 0 && $i < $indexEntitas - 2) {
                array_push($data['entitas'], [
                    'seriEntitas' => $e->seriEntitas,
                    'noIdentitas' => $e->nomorIdentitas,
                    'namaEntitas' => $e->namaEntitas,
                    'alamatEntitas' => $e->alamatEntitas
                ]);
            }
            $i++;
        }
        foreach ($data['barang']['bcDetail']->barangPemilik as $e) {
            array_push($data['entitasSelected'], $e->seriEntitas);
        }

        return view('BeaCukai/bc-30/form-detail-barang', $data);
    }

    public function barangDetailUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $seriBarang = $this->request->getVar('seriBarang');

        $nettoTotal = 0;
        $hargaPenyerahanTotal = 0;
        for ($i = 0; $i < count($payload->barang); $i++) {
            if ($payload->barang[$i]->seriBarang == $seriBarang) {
                $payload->barang[$i]->fob = (float) $this->request->getVar('fob');
                //     $payload->barang[$i]->hargaEkspor = $this->request->getVar('kodeBarang');
                //  $payload->barang[$i]->hargaPatokan = $this->request->getVar('uraian');
                //   $payload->barang[$i]->hargaPerolehan = $this->request->getVar('merk');
                //     $payload->barang[$i]->hargaSatuan = $this->request->getVar('tipe');
                $payload->barang[$i]->jumlahKemasan = intval($this->request->getVar('jumlahKemasan'));
                $payload->barang[$i]->jumlahSatuan = intval($this->request->getVar('jumlahSatuan'));
                $payload->barang[$i]->kodeAsalBahanBaku = $this->request->getVar('barang_detail_kode_asal_bahan_baku');
                $payload->barang[$i]->kodeBarang = $this->request->getVar('kodeBarang');
                $payload->barang[$i]->kodeDaerahAsal = $this->request->getVar('daerahAsalBarang');
                //  $payload->barang[$i]->kodeDokumen = $this->request->getVar('kodePerhitungan');
                $payload->barang[$i]->kodeJenisKemasan = $this->request->getVar('kodeJenisKemasan');
                $payload->barang[$i]->kodeNegaraAsal = (string)decrypt($this->request->getVar('negaraAsalBarang'));
                $payload->barang[$i]->kodeSatuanBarang = "" . decrypt($this->request->getVar('kodeSatuanBarang'));
                $payload->barang[$i]->merk = $this->request->getVar('merk');
                //                $payload->barang[$i]->ndpbm = (float)$this->request->getVar('netto');
                $payload->barang[$i]->netto = (float) $this->request->getVar('netto');
                // $payload->barang[$i]->nilaiBarang = (float)convertRupiahToNumber($this->request->getVar('hargaEkspor'));
                // $payload->barang[$i]->nilaiDanaSawit = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
                $payload->barang[$i]->posTarif = $this->request->getVar('posTarif');
                $payload->barang[$i]->seriBarang = intval($this->request->getVar('seriBarang'));
                //$payload->barang[$i]->spesifikasiLain = $this->request->getVar('hargaPenyerahan');
                $payload->barang[$i]->tipe = $this->request->getVar('tipe');
                $payload->barang[$i]->ukuran = $this->request->getVar('ukuran');
                $payload->barang[$i]->uraian = $this->request->getVar('uraian');
                $payload->barang[$i]->volume = (float) $this->request->getVar('volume');
                //below keknya tuk table
                // $payload->barang[$i]->barangTarif = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
                // $payload->barang[$i]->barangDokumen = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
                // $payload->barang[$i]->barangPemilik = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
            }
            $nettoTotal += $payload->barang[$i]->netto;
        }
        $payload->netto = $nettoTotal;


        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil update detail barang"
        ]);
    }


    public function createDokumenBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriDokumen = $this->request->getVar('seriDokumen');
        $seriBarang = $this->request->getVar('seriBarang');
        $indexBarang = 0;

        $payload = json_decode($this->bc30Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        array_push($payload->barang[$indexBarang]->barangDokumen, [
            'seriDokumen' => $seriDokumen,
            'seriIjin' => $seriDokumen
        ]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Dokumen berhasil disimpan",
            'status' => true,
        ]);
    }
    public function deleteDokumenBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $seriDokumen = $this->request->getVar('seriDokumen');
        $indexBarang = 0;
        $indexDelete = 0;

        $payload = json_decode($this->bc30Model->find($id)['payload']);
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
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Dokumen berhasil dihapus",
            'status' => true,
        ]);
    }

    public function createEntitasBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriEntitas = $this->request->getVar('seriEntitas');
        $seriBarang = $this->request->getVar('seriBarang');
        $indexBarang = 0;
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        array_push($payload->barang[$indexBarang]->barangPemilik, [
            'seriEntitas' => intval($seriEntitas),
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Entitas Pemilik Barang berhasil disimpan",
            'status' => true,
        ]);
    }
    public function deleteEntitasBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $seriEntitas = $this->request->getVar('seriEntitas');
        $indexBarang = 0;
        $indexDelete = 0;

        $payload = json_decode($this->bc30Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        foreach ($payload->barang[$indexBarang]->barangDokumen as $i => $b) {
            if ($b->seriEntitas == $seriEntitas) {
                $indexDelete = $i;
            }
        }

        unset($payload->barang[$indexBarang]->barangDokumen[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Entitas Pemilik Barang berhasil dihapus",
            'status' => true,
        ]);
    }
    public function pungutan($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);
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
            'bc30' => $bc30,
            'payload' => $payload,
            'pungutanList' => $pungutanList,
        ];

        return view('BeaCukai/bc-30/form-pungutan', $data);
    }

    public function pernyataan($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $data = [
            'bc30' => $bc30,
            'payload' => $payload,
            'ceisaSetting' => $ceisaSetting
        ];

        return view('BeaCukai/bc-30/form-pernyataan', $data);
    }

    public function pernyataanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        $payload->kotaTtd = $this->request->getVar('kotaTtd');
        $payload->tanggalTtd = date('Y-m-d', strtotime($this->request->getVar('tanggalTtd')));
        $payload->namaTtd = $this->request->getVar('namaTtd');
        $payload->jabatanTtd = $this->request->getVar('jabatanTtd');

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pernyataan berhasil diupdate"
        ]);
    }

    public function kesiapanBarang($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        $this->setFlashDataNavigatorSession($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        // $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $tanggalSiapPeriksa = "";
        $waktuPeriksa = "";


        if (count($payload->kesiapanBarang) == 0) {
            $payload->kesiapanBarang[0] = [
                "kodeJenisBarang" => "",
                "kodeJenisGudang" => "",
                "namaPic" => "",
                "alamat" => "",
                "nomorTelpPic" => "",
                "jumlahContainer20" => 0,
                "jumlahContainer40" => 0,
                "lokasiSiapPeriksa" => "",
                "kodeCaraStuffing" => "",
                "kodeJenisPartOf" => "",
                "tanggalPkb" => "",
                "waktuSiapPeriksa" => ""
            ];
        } else {
            $waktuSiapPeriksa = $payload->kesiapanBarang[0]->waktuSiapPeriksa;
            if ($waktuSiapPeriksa) {
                $tanggalSiapPeriksa = date('d/m/Y', strtotime(substr($waktuSiapPeriksa, 0, 10)));
                $waktuPeriksa = date("h:i A", strtotime(substr($waktuSiapPeriksa, 11, 5)));
            }
        }
        $data = [
            'bc30' => $bc30,
            'payload' => $payload,
            'tanggalSiapPeriksa' => $tanggalSiapPeriksa,
            'waktuPeriksa' => $waktuPeriksa

        ];


        return view('BeaCukai/bc-30/form-kesiapan-barang', $data);
    }

    public function kesiapanBarangUpdate()
    {
        $id = decrypt($this->request->getVar('id'));

        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        $payload->kesiapanBarang = [];

        $waktuSiapPeriksa =  date('Y-m-d', strtotime(str_replace('/', '-',  $this->request->getVar('tanggalSiapPeriksa'))))  . "T" .  date('H:i:s', strtotime($this->request->getVar('waktuSiapPeriksa'))) . '' . date('P', strtotime($this->request->getVar('timezoneOffset')));

        array_push($payload->kesiapanBarang, [
            'kodeJenisBarang' => $this->request->getVar('jenisBarang'),
            'kodeJenisGudang' => $this->request->getVar('jenisGudang'),
            'namaPic' => $this->request->getVar('namaPic'),
            'alamat' => $this->request->getVar('alamat'),
            'nomorTelpPic' => $this->request->getVar('nomorTelponPic'),
            'jumlahContainer20' => (float) $this->request->getVar('jumlahContainer20'),
            'jumlahContainer40' => (float)$this->request->getVar('jumlahContainer40'),
            'lokasiSiapPeriksa' => $this->request->getVar('lokasiSiapPeriksa'),
            'kodeCaraStuffing' => $this->request->getVar('caraStuffing'),
            'kodeJenisPartOf' => $this->request->getVar('jenisPartOf'),
            'tanggalPkb' => date("Y-m-d", strtotime(str_replace('/', '-', $this->request->getVar('tanggalPkb')))),
            'waktuSiapPeriksa' => $waktuSiapPeriksa,
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "kesiapan Barang berhasil diupdate"
        ]);
    }

    public function get_payload()
    {
        $payloadArr = [
            "asalData" => "S",
            "asuransi" => 0,
            "bruto" => 0,
            "cif" => 0,
            "disclaimer" => "1",
            "flagCurah" => "",
            "flagMigas" => "",
            "fob" => 0,
            "freight" => 0,
            "idPengguna" => "",
            "jabatanTtd" => "",
            "jumlahKontainer" => 0,
            "kodeAsuransi" => "",
            "kodeCaraBayar" => "",
            "kodeCaraDagang" => "",
            "kodeDokumen" => "30",
            "kodeIncoterm" => "",
            "kodeJenisEkspor" => "",
            "kodeJenisNilai" => "",
            "kodeJenisProsedur" => "",
            "kodeKantor" => "",
            "kodeKantorEkspor" => "",
            "kodeKantorMuat" => "",
            "kodeKantorPeriksa" => "",
            "kodeKategoriEkspor" => "",
            "kodeLokasi" => "",
            "kodeNegaraTujuan" => "",
            "kodePelBongkar" => "",
            "kodePelEkspor" => "",
            "kodePelMuat" => "",
            "kodePelTujuan" => "",
            "kodePembayar" => "",
            "kodeTps" => "",
            "kodeValuta" => "",
            "kotaTtd" => "",
            "namaTtd" => "",
            "ndpbm" => 0,
            "netto" => 0,
            "nilaiMaklon" => 0,
            "nomorAju" => "",
            "seri" => 1,
            "tanggalAju" => "",
            "tanggalEkspor" => "",
            "tanggalPeriksa" => "",
            "tanggalTtd" => "",
            "totalDanaSawit" => 0,
            "barang" => [],
            "entitas" => [],
            "kemasan" => [],
            "kontainer" => [],
            "dokumen" => [],
            "pengangkut" => [],
            "bankDevisa" => [],
            "kesiapanBarang" => []
        ];

        return json_encode($payloadArr, JSON_UNESCAPED_SLASHES);
    }
}
