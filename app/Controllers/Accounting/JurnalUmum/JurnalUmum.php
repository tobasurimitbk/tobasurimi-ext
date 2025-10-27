<?php

namespace App\Controllers\Accounting\JurnalUmum;

use App\Controllers\BaseController;
use App\Models\AccountModuleModel;
use App\Models\Sub_AkunsModel;
use App\Models\JurnalUmumModel;
use App\Models\TransaksiJurnalModel;
use App\Models\TransaksiPembelianModel;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\SupplierModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\AccountSupplierModel;
use App\Models\AccountBarangModel;
use App\Models\AccountDivisisModel;
use App\Models\CompaniesModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\LocalPOPaymentModel;
use App\Models\LocalPOPaymentDetailModel;
use App\Models\ImportPOPaymentModel;
use App\Models\KursModel;
use App\Models\OtherPaymentModel;
use App\Models\OtherPaymentDetailModel;
use App\Models\PembayaranInvoiceDetailModel;
use App\Models\PajakTandaTerimaFakturModel;
use App\Models\PembayaranInvoiceModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\PanjarPinjamanTransactionModel;
use App\Models\SalesOrderLainModel;
use App\Models\TutupBukuModel;
use Config\Database;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\LocalPOPaymentPanjarModel;
use App\Models\LocalPOPaymentPinjamanModel;
use App\Models\PanjarSupplierModel;
use App\Models\PinjamanSupplierModel;
use App\Models\EmployeesModel;
use App\Models\LocalPOPaymentBPModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\TandaTerimaFakturModel;
use App\Models\TaxModel;
use Carbon\Carbon;

class JurnalUmum extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_company;
    protected $this_user_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $transaksiJurnalModel;
    protected $transaksiPembelianModel;
    protected $encrypter;
    protected $MetadataModel;
    protected $supplierModel;
    protected $divisionModel;
    protected $aMPurchaseOrderModel;
    protected $aMPurchaseOrderDetailModel;
    protected $rMPurchaseOrderModel;
    protected $rMPurchaseOrderDetailModel;
    protected $rmImportPOModel;
    protected $rmImportPODetailModel;
    protected $accountSupplierModel;
    protected $accountModuleModel;
    protected $accountBarangModel;
    protected $accountDivisisModel;
    protected $localPOPaymentModel;
    protected $localPOPaymentDetailModel;
    protected $importPOPaymentModel;
    protected $otherPaymentModel;
    protected $otherPaymentDetailModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $metadataModel;
    protected $kursModel;
    protected $tutupBukuModel;
    protected $localPOPaymentPanjarModel;
    protected $localPOPaymentPinjamanModel;
    protected $PanjarSupplierModel;
    protected $PinjamanSupplierModel;
    protected $EmployeeModel;
    protected $panjarPinjamanTransactionModel;
    protected $localPoPaymentBpModel;

    protected $salesOrderLainModel;
    protected $salesOrderLainDetailModel;
    protected $pembayaranInvoiceModel;
    protected $pembayaranInvoiceDetailModel;
    protected $companiesModel;
    protected $db;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_company = session()->get("login")->this_company;
        $this->this_user_id = session()->get("login")->user_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
        $this->transaksiPembelianModel = new TransaksiPembelianModel();
        $this->MetadataModel = new MetadataModel();
        $this->encrypter = \Config\Services::encrypter();
        $this->supplierModel = new SupplierModel();
        $this->divisionModel = new DivisisModel();
        $this->aMPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->aMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->rMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();
        $this->accountSupplierModel = new AccountSupplierModel();
        $this->accountModuleModel = new AccountModuleModel();
        $this->accountBarangModel = new AccountBarangModel();
        $this->accountDivisisModel = new AccountDivisisModel();
        $this->localPOPaymentModel = new LocalPOPaymentModel();
        $this->localPOPaymentDetailModel = new LocalPOPaymentDetailModel();
        $this->importPOPaymentModel = new ImportPOPaymentModel();
        $this->otherPaymentModel = new OtherPaymentModel();
        $this->otherPaymentDetailModel = new OtherPaymentDetailModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->metadataModel = new MetadataModel();
        $this->kursModel = new KursModel();
        $this->tutupBukuModel = new TutupBukuModel();
        $this->localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
        $this->localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();
        $this->PinjamanSupplierModel = new PinjamanSupplierModel();
        $this->PanjarSupplierModel = new PanjarSupplierModel();
        $this->EmployeeModel = new EmployeesModel();
        $this->localPoPaymentBpModel = new LocalPOPaymentBPModel();

        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $this->pembayaranInvoiceModel = new PembayaranInvoiceModel();
        $this->pembayaranInvoiceDetailModel = new PembayaranInvoiceDetailModel();
        $this->panjarPinjamanTransactionModel = new PanjarPinjamanTransactionModel();
        $this->companiesModel = new CompaniesModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $tipeTransaksi = $this->MetadataModel
            ->where('name', 'tipe_transaksi')
            ->where('name !=', "PEMBELIAN")
            ->findAll();
        // Tambahkan Transaksi Pembelian Bahan Baku dan Bahan Penolong
        $tipeTransaksiArr = array(
            [
                'id' => "BAHAN BAKU",
                'value' => "PEMBELIAN BAHAN BAKU"
            ],
            [
                'id' => "BAHAN PENOLONG",
                'value' => "PEMBELIAN BAHAN PENOLONG"
            ]
        );
        foreach ($tipeTransaksi as $t) {
            array_push($tipeTransaksiArr, [
                'id' => $t['id'],
                'value' => $t['value']
            ]);
        }

        $data = [
            'tipeTransaksi' => $tipeTransaksiArr
        ];

        return view('Accounting/jurnalUmum/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
            "search" => $this->request->getVar("search"),
        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "start_date" =>  $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "",
            "end_date" =>  $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "",
            "type_transaksi" => $this->request->getVar('type_transaksi'),
            "search" => $this->request->getVar("search"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            'jurnal_umum.company_id' => $this->this_company_id,
            'transaksi_jurnal.deleted_at' => null,
        ];

        $dataQry = $this->transaksiJurnalModel->getList($condition, $addCondition, $limit, $offset);
        $dataJurnal = $this->getData($dataQry['data'], $payload);

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataJurnal,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    private function getData($dataJurnal, $payload)
    {
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataResult = [];

        foreach ($dataJurnal as $data) {
            // Cek Tutup Buku per transaksi
            // $tutupBuku = $this->tutupBukuModel
            //     ->where('company_id', $this->this_company_id)
            //     ->where('bulan', date('Y-m', strtotime($data->tanggal_transaksi)))
            //     ->first();

            // Penentuan tipe pembelian
            $noLpb = $data->no_penerimaan_barang;
            $tipePembelian = "";
            if ($data->id_local_bb != null) {
                $tipePembelian = "LOKAL BB";
            } elseif ($data->id_import_bb != null) {
                $tipePembelian = "IMPORT BB";
            } elseif ($data->id_po_bp != null) {
                $tipePembelian = ($data->po_type === "Lokal") ? "LOKAL BP" : "IMPORT BP";
            }

            $dataResult[] = [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "transaksi_type_name"   => trim($data->transaksi_type_name . " " . $tipePembelian),
                "no_transaksi"          => $data->metode_input === 'system' ? $data->no_transaksi : $data->no_bukti,
                "tanggal_transaksi"     => date('d/m/Y', strtotime($data->tanggal_transaksi)),
                "uraian_transaksi"      => $data->uraian_transaksi,
                "keterangan_jurnal"      => $data->keterangan_jurnal,
                "supplier"              => "0 : " . $data->supplier_name, // tidak tersedia setelah relasi dihapus
                "no_lpb"                => $noLpb, // tidak tersedia setelah relasi dihapus
                "metode_input"          => strtoupper($data->metode_input),
                "valas"                 => $data->valas,
                "exchange_rate"         => $data->exchange_rate == 1 ? "" : $data->exchange_rate,
                "nilai"                 => $data->exchange_rate == 1 ? "" : $data->exchange_rate,
                "nilai_idr"             => $data->total_debit,
                "tutup_buku"            => 0,
                "divisi_name"           => $data->divisi_name ?? 'ALL',
            ];
        }

        return $dataResult;
    }

    private function getDataExport($dataJurnal, $payload)
    {
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataResult = [];

        foreach ($dataJurnal as $data) {
            // Cek Tutup Buku per transaksi
            // $tutupBuku = $this->tutupBukuModel
            //     ->where('company_id', $this->this_company_id)
            //     ->where('bulan', date('Y-m', strtotime($data->tanggal_transaksi)))
            //     ->first();

            // Penentuan tipe pembelian
            $noLpb = $data->no_penerimaan_barang;
            $tipePembelian = "";
            if ($data->id_local_bb != null) {
                $tipePembelian = "LOKAL BB";
            } elseif ($data->id_import_bb != null) {
                $tipePembelian = "IMPORT BB";
            } elseif ($data->id_po_bp != null) {
                $tipePembelian = ($data->po_type === "Lokal") ? "LOKAL BP" : "IMPORT BP";
            }

            $dataResult[] = [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "transaksi_type_name"   => trim($data->transaksi_type_name . " " . $tipePembelian),
                "no_transaksi"          => $data->metode_input === 'system' ? $data->no_transaksi : $data->no_bukti,
                "tanggal_transaksi"     => date('d/m/Y', strtotime($data->tanggal_jurnal)),
                "uraian_transaksi"      => $data->uraian_transaksi,
                "keterangan_jurnal"      => $data->keterangan_jurnal,
                "supplier"              => "0 : " . $data->supplier_name, // tidak tersedia setelah relasi dihapus
                "no_lpb"                => $noLpb, // tidak tersedia setelah relasi dihapus
                "metode_input"          => strtoupper($data->metode_input),
                "valas"                 => $data->valas,
                "exchange_rate"         => $data->exchange_rate == 1 ? 1 : $data->exchange_rate,
                "nilai"                 => $data->exchange_rate == 1 ? 1 : $data->exchange_rate,
                "nilai_idr"             => $data->total_debit,
                "tutup_buku"            => 0,
                "divisi_name"           => $data->divisi_name ?? 'ALL',
                "debit"                 => $data->debit,
                "kredit"                => $data->kredit,
                "kode_coa"              => $data->kode_coa,
                "nama_coa"              => $data->nama_coa,
            ];
        }

        return $dataResult;
    }

    public function create()
    {
        $tipeTransaksi = $this->MetadataModel
            ->where('name', 'tipe_transaksi')
            ->findAll();


        $data = [
            'tipeTransaksi' => $tipeTransaksi,
            'subAkun' =>  $this->Sub_AkunsModel->getAPAR($this->this_company_id),
            'valuta' => $this->metadataModel->get_by_name('Valuta'),
            'divisi' => $this->divisionModel->getDivisiAccess()
        ];

        return view('Accounting/jurnalUmum/form', $data);
    }

    public function store()
    {
        // $check = $this->transaksiJurnalModel->where('no_bukti', $this->request->getVar('no_bukti'))->first();
        // if ($check == null) {
        //     return response()->setJSON([
        //         'status' => false,
        //         'message' => "Nomor bukti sudah ada",
        //         'token' => csrf_hash()
        //     ]);
        // }

        $valas = null;
        $valasId = null;
        $exchangeRate = null;

        foreach (json_decode($this->request->getVar('listJurnal')) as $l) {
            $valas = $l->valas;
            $valasId = $l->valas_id;
            $exchangeRate = $l->kurs;
        }
        $db = Database::connect();

        try {
            $db->transBegin();

            $id = $this->transaksiJurnalModel->insert([
                'no_transaksi' => $this->request->getVar('no_bukti'),
                'tanggal_transaksi' =>  $this->request->getVar("tanggal_transaksi") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_transaksi")))) : "",
                'type_transaksi' => $this->request->getVar('type_transaksi'),
                'uraian_transaksi' => $this->request->getVar('uraian_transaksi'),
                'total_debit' => $this->request->getVar('total_debit'),
                'total_kredit' => $this->request->getVar('total_kredit'),
                'metode_input' => "manual",
                'no_bukti' => $this->request->getVar('no_bukti'),
                'uraian_transaksi' => $this->request->getVar('uraian_transaksi'),
                'valas' => $valas,
                'valas_id' => $valasId,
                'exchange_rate' => $exchangeRate,
                'total_debit' => $this->request->getVar('totalDebit'),
                'total_kredit' => $this->request->getVar('totalKredit'),
            ]);

            foreach (json_decode($this->request->getVar('listJurnal')) as $l) {
                $this->jurnalUmumModel->insert([
                    'id_transaksi' => $id,
                    'id_coa' => $l->id_coa,
                    'company_id' => $this->this_company_id,
                    'divisi_id' => $this->request->getVar('divisi_id') == "ALL" ? null : $this->request->getVar('divisi_id'),
                    'tanggal_jurnal' => $this->request->getVar("tanggal_transaksi") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_transaksi")))) : "",
                    'debit' => $l->jenis_transaksi == "debit" ? $l->jumlah_idr : 0,
                    'kredit' => $l->jenis_transaksi == "kredit" ? $l->jumlah_idr : 0,
                    'valas' => $l->valas_id,
                    'kurs' => $l->kurs,
                    'keterangan' => $l->keterangan,
                    'id_inputer' => $this->this_user_id
                ]);
            }
            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'message' => "Jurnal Berhasil Disimpan",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            var_dump($e->getMessage(), $e->getLine());
            return response()->setJSON([
                'status' => false,
                'message' => "Terjadi Kesalahan",
                'token' => csrf_hash()
            ]);
        }
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        // $check = $this->transaksiJurnalModel->where('no_bukti', $this->request->getVar('no_bukti'))->where('id !=', $id)->first();
        // if ($check == null) {
        //     return response()->setJSON([
        //         'status' => false,
        //         'message' => "Nomor bukti sudah ada",
        //         'token' => csrf_hash()
        //     ]);
        // }

        $valas = null;
        $valasId = null;
        $exchangeRate = null;

        foreach (json_decode($this->request->getVar('listJurnal')) as $l) {
            $valas = $l->valas;
            $valasId = $l->valas_id;
            $exchangeRate = $l->kurs;
        }
        $db = Database::connect();

        try {
            $db->transBegin();
            $this->transaksiJurnalModel->update($id, [
                'no_transaksi' => $this->request->getVar('no_bukti'),
                'tanggal_transaksi' =>  $this->request->getVar("tanggal_transaksi") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_transaksi")))) : "",
                'type_transaksi' => $this->request->getVar('type_transaksi'),
                'uraian_transaksi' => $this->request->getVar('uraian_transaksi'),
                'total_debit' => $this->request->getVar('total_debit'),
                'total_kredit' => $this->request->getVar('total_kredit'),
                'metode_input' => "manual",
                'no_bukti' => $this->request->getVar('no_bukti'),
                'uraian_transaksi' => $this->request->getVar('uraian_transaksi'),
                'valas' => $valas,
                'valas_id' => $valasId,
                'exchange_rate' => $exchangeRate,
                'total_debit' => floatval($this->request->getVar('totalDebit')),
                'total_kredit' => floatval($this->request->getVar('totalKredit')),
            ]);

            // Delete All
            $this->jurnalUmumModel->where('id_transaksi', $id)->delete();
            foreach (json_decode($this->request->getVar('listJurnal')) as $l) {
                $this->jurnalUmumModel->insert([
                    'id_transaksi' => $id,
                    'id_coa' => $l->id_coa,
                    'company_id' => $this->this_company_id,
                    'divisi_id' => $this->request->getVar('divisi_id') == "ALL" ? null : $this->request->getVar('divisi_id'),
                    'tanggal_jurnal' => $this->request->getVar("tanggal_transaksi") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_transaksi")))) : "",
                    'debit' => $l->jenis_transaksi == "debit" ? $l->jumlah_idr : 0,
                    'kredit' => $l->jenis_transaksi == "kredit" ? $l->jumlah_idr : 0,
                    'valas' => $l->valas_id,
                    'kurs' => $l->kurs,
                    'keterangan' => $l->keterangan,
                    'id_inputer' => $this->this_user_id
                ]);
            }
            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'message' => "Jurnal Berhasil Diupdate",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();

            var_dump($e->getMessage(), $e->getLine());
            return response()->setJSON([
                'status' => false,
                'message' => "Terjadi Kesalahan",
                'token' => csrf_hash()
            ]);
        }
    }

    public function detail($id)
    {

        $id = decrypt($id);
        $transaksiJurnal = $this->transaksiJurnalModel->where('id', $id)->first();

        if ($transaksiJurnal == null) {
            return redirect()->to('jurnal');
        }

        $jurnalUmum = $this->jurnalUmumModel->select('
            jurnal_umum.*,
            sub_akuns.no_sub,
            sub_akuns.nama_sub,
            metadata.value as valas_name,    
        ')
            ->join('sub_akuns', 'sub_akuns.id = jurnal_umum.id_coa', 'left')
            ->join('metadata', 'metadata.id = jurnal_umum.valas', 'left')
            ->where('id_transaksi', $id)
            ->where('jurnal_umum.deletedAt', null)
            ->findAll();

        $divisiId = "ALL";

        $jurnalUmumList = [];
        foreach ($jurnalUmum as $j) {
            $divisiId = $j['divisi_id'];

            $jumlah =  $j['debit'] == 0.00 ? $j['kredit'] : $j['debit'];
            array_push($jurnalUmumList, [
                'id' => $j['id'],
                'jenis_transaksi' => $j['debit'] == 0.00 ? "kredit" : "debit",
                'keterangan' => $j['keterangan'],
                'id_coa' => $j['id_coa'],
                'valas_id' => $j['valas'],
                'valas' => $j['valas_name'],
                'jumlah' => floatval($jumlah / $j['kurs']),
                'kurs' => floatval($j['kurs']),
                'jumlah_idr' => floatval($jumlah),
                'nama_sub' => $j['nama_sub'],
                'no_sub' => $j['no_sub'],
            ]);
        }

        $tipeTransaksi = $this->MetadataModel
            ->where('name', 'tipe_transaksi')
            ->findAll();

        $tutupBuku = $this->tutupBukuModel
            ->where('company_id', $this->this_company_id)
            ->where('bulan', date('Y-m', \strtotime($transaksiJurnal['tanggal_transaksi'])))
            ->first();

        $data = [
            'transaksiJurnal' => $transaksiJurnal,
            'jurnalUmumList' => $jurnalUmumList,
            'tipeTransaksi' => $tipeTransaksi,
            'tutupBuku' => $tutupBuku == null ? 0 : 1,
            'divisiId' => $divisiId,
            'divisi' => $this->divisionModel->getDivisiAccess(),
            'subAkun' =>  $this->Sub_AkunsModel->getAPAR($this->this_company_id),
            'valuta' => $this->metadataModel->get_by_name('Valuta'),

        ];

        return view('Accounting/jurnalUmum/form', $data);
    }

    public function detailView($id)
    {
        $id = decrypt($id);
        $transaksiJurnal = $this->transaksiJurnalModel->where('id', $id)->first();

        if ($transaksiJurnal == null) {
            return redirect()->to('jurnal');
        }

        $jurnalUmum = $this->jurnalUmumModel->select('
            jurnal_umum.*,
            sub_akuns.no_sub,
            sub_akuns.nama_sub,
            metadata.value as valas_name,    
        ')
            ->join('sub_akuns', 'sub_akuns.id = jurnal_umum.id_coa', 'left')
            ->join('metadata', 'metadata.id = jurnal_umum.valas', 'left')
            ->where('id_transaksi', $id)
            ->where('jurnal_umum.deletedAt', null)
            ->findAll();

        $divisiId = "ALL";

        $jurnalUmumList = [];
        foreach ($jurnalUmum as $j) {
            $divisiId = $j['divisi_id'];

            $jumlah =  $j['debit'] == 0.00 ? $j['kredit'] : $j['debit'];
            array_push($jurnalUmumList, [
                'id' => $j['id'],
                'jenis_transaksi' => $j['debit'] == 0.00 ? "kredit" : "debit",
                'keterangan' => $j['keterangan'],
                'id_coa' => $j['id_coa'],
                'valas_id' => $j['valas'],
                'valas' => $j['valas_name'],
                'jumlah' => floatval($jumlah / $j['kurs']),
                'kurs' => floatval($j['kurs']),
                'jumlah_idr' => floatval($jumlah),
                'nama_sub' => $j['nama_sub'],
                'no_sub' => $j['no_sub'],
            ]);
        }

        $tipeTransaksi = $this->MetadataModel
            ->where('name', 'tipe_transaksi')
            ->findAll();

        $tutupBuku = $this->tutupBukuModel
            ->where('company_id', $this->this_company_id)
            ->where('bulan', date('Y-m', \strtotime($transaksiJurnal['tanggal_transaksi'])))
            ->first();

        $data = [
            'transaksiJurnal' => $transaksiJurnal,
            'jurnalUmumList' => $jurnalUmumList,
            'tipeTransaksi' => $tipeTransaksi,
            'tutupBuku' => $tutupBuku == null ? 0 : 1,
            'divisiId' => $divisiId,
            'divisi' => $this->divisionModel->getDivisiAccess(),
            'subAkun' =>  $this->Sub_AkunsModel->getAPAR($this->this_company_id),
            'valuta' => $this->metadataModel->get_by_name('Valuta'),

        ];

        return view('Accounting/jurnalUmum/form_view', $data);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->transaksiJurnalModel->where('id', $id)->delete();
        $this->jurnalUmumModel->where('id_transaksi', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Jurnal Berhasil Dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $transaksiJurnal = $this->transaksiJurnalModel->where('id', $id)->first();

        if ($transaksiJurnal == null) {
            return redirect()->to('jurnal');
        }

        $jurnalUmum = $this->jurnalUmumModel->select('
            jurnal_umum.*,
            sub_akuns.no_sub,
            sub_akuns.nama_sub,
            metadata.value as valas,    
        ')
            ->join('sub_akuns', 'sub_akuns.id = jurnal_umum.id_coa', 'left')
            ->join('metadata', 'metadata.id = jurnal_umum.valas', 'left')
            ->where('id_transaksi', $id)
            ->where('jurnal_umum.deletedAt', null)
            ->findAll();

        $jurnalUmumList = [];
        foreach ($jurnalUmum as $j) {
            $jumlah = $j['debit'] == 0.00 ? $j['kredit'] : $j['debit'];
            array_push($jurnalUmumList, [
                'id' => $j['id'],
                'jenis_transaksi' => $j['debit'] == 0.00 ? "kredit" : "debit",
                'id_coa' => $j['id_coa'],
                'valas_id' => $j['valas'],
                'valas' => $j['valas'],
                'jumlah' => floatval($jumlah / $j['kurs']),
                'kurs' => floatval($j['kurs']),
                'jumlah_idr' => floatval($jumlah),
                'nama_sub' => $j['nama_sub'],
                'no_sub' => $j['no_sub'],
            ]);
        }

        $data = [
            'transaksiJurnal' => $transaksiJurnal,
            'jurnalUmumList' => $jurnalUmumList,
            'company' => $this->companiesModel->where('id', $this->this_company_id)->first()
        ];

        $html = view('Accounting/jurnalUmum/print', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('voucher-accounting.pdf', ["Attachment" => false]);
    }

    public function exportExcel()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "sort"          => $this->request->getVar("sort"),
            "sorttype"      => $this->request->getVar("sortType"),
            "currentPage"   => 1,
        ];

        $startDate = $this->request->getVar("start_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "";
        $endDate = $this->request->getVar("end_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "";

        $addCondition = [
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "start_date"    => $startDate,
            "end_date"      => $endDate,
            "type_transaksi" => $this->request->getVar('type_transaksi'),
            "search"        => $this->request->getVar("search"),
        ];

        $condition = [
            'jurnal_umum.company_id'        => $this->this_company_id,
            'transaksi_jurnal.deleted_at'   => null,
        ];

        $dataQry = $this->jurnalUmumModel->getListExport($condition, $addCondition, 0, 0);
        $dataJurnal = $this->getDataExport($dataQry['data'], $payload);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyleArray = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $dataStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        // Nama PT di baris pertama
        $sheet->mergeCells('A1:O1')->setCellValue('A1', 'PT. Toba Surimi Industries');
        $sheet->getStyle('A1')->applyFromArray($headerStyleArray);

        // Tanggal di baris kedua
        $periode = ($startDate && $endDate) ? "Periode: $startDate s.d $endDate" : "Tanggal: -";
        $sheet->mergeCells('A2:O2')->setCellValue('A2', $periode);
        $sheet->getStyle('A2')->applyFromArray($headerStyleArray);

        // Judul laporan di baris ketiga
        $sheet->mergeCells('A3:O3')->setCellValue('A3', 'Laporan Jurnal Umum');
        $sheet->getStyle('A3')->applyFromArray($headerStyleArray);

        // Header kolom mulai dari baris ke-4
        $sheet->setCellValue('A4', 'No')
            ->setCellValue('B4', 'Date')
            ->setCellValue('C4', 'Department')
            ->setCellValue('D4', 'Transaction Num')
            ->setCellValue('E4', 'LPB Num')
            ->setCellValue('F4', 'Information')
            ->setCellValue('G4', 'Invoice')
            ->setCellValue('H4', 'Estimate Num')
            ->setCellValue('I4', 'Estimate Name')
            ->setCellValue('J4', 'Desc')
            ->setCellValue('K4', 'Reference')
            ->setCellValue('L4', 'Currency')
            ->setCellValue('M4', 'Exchange Rate')
            ->setCellValue('N4', 'Debit')
            ->setCellValue('O4', 'Credit');

        $sheet->getStyle('A4:O4')->applyFromArray($headerStyleArray);

        $column = 5;

        foreach ($dataJurnal as $index => $row) {
            $sheet->setCellValue('A' . $column, $index + 1)
                ->setCellValue('B' . $column, $row['tanggal_transaksi'])
                ->setCellValue('C' . $column, $row['divisi_name'])
                ->setCellValue('D' . $column, $row['no_transaksi'])
                ->setCellValue('E' . $column, $row['no_lpb'])
                ->setCellValue('F' . $column, $row['transaksi_type_name'])
                ->setCellValue('G' . $column, $row['supplier'])
                ->setCellValue('H' . $column, $row['kode_coa'])
                ->setCellValue('I' . $column, $row['nama_coa'])
                ->setCellValue('J' . $column, $row['keterangan_jurnal'])
                ->setCellValue('K' . $column, $row['uraian_transaksi'])
                ->setCellValue('L' . $column, number_format($row['nilai_idr'], 2, ',', '.') . " " . $row['valas'])
                ->setCellValue('M' . $column, $row['exchange_rate'])
                ->setCellValue('N' . $column, $row['debit'])
                ->setCellValue('O' . $column, $row['kredit']);

            $sheet->getStyle('N' . $column)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

            $sheet->getStyle('O' . $column)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

            $sheet->getStyle('A' . $column . ':O' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }

        // Auto size kolom
        foreach (range('A', 'O') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Transaksi_Jurnal';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
            "currentPage"   => 1,

        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "start_date" =>  $this->request->getVar("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "",
            "end_date" =>  $this->request->getVar("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "",
            "type_transaksi" => $this->request->getVar('type_transaksi'),
            "search" => $this->request->getVar("search"),
        ];

        $condition = [
            'jurnal_umum.company_id' => $this->this_company_id,
            'transaksi_jurnal.deleted_at' => null,
        ];

        $dataQry = $this->transaksiJurnalModel->getListExport($condition, $addCondition, 10000000, 0);
        $dataJurnal = $this->getData($dataQry['data'], $payload);

        $data = [
            'dataJurnal' => $dataJurnal,
            'company' => $this->companiesModel->where('id', $this->this_company_id)->first(),
            'startDate' => $addCondition['start_date'],
            'endDate' => $addCondition['end_date']
        ];

        $html = view('Accounting/jurnalUmum/printRange', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Laporan_Transaksi_Jurnal.pdf', ["Attachment" => false]);
    }

    public function import()
    {
        $rules = [
            "file" => [
                'rules' => 'uploaded[file]|ext_in[file,xlsx]',
                'errors' => [
                    'uploaded' => 'Tidak ada file yang di-upload.',
                    'ext_in' => 'File yang di-upload harus berupa file Excel (.xlsx).',
                ],

            ],
        ];

        if ($this->validate($rules)) {
            try {
                $file = $this->request->getFile('file');
                // Load the spreadsheet
                $spreadsheet = IOFactory::load($file->getTempName());
                $sheet = $spreadsheet->getActiveSheet();
                // Start a database transaction
                $this->db->transBegin();

                // GET ALL VALUTA
                $valuta = $this->MetadataModel->where('name', 'Valuta')->findAll();
                $valutaLookup = [];
                foreach ($valuta as $meta) {
                    $valutaLookup[$meta['value']] = $meta['id'];
                }

                // GET ALL TRANSAKSI
                $tipeTransaksi =  $this->MetadataModel->where('name', 'tipe_transaksi')->findAll();
                $tipeTransaksiLookup = [];
                foreach ($tipeTransaksi as $meta) {
                    $tipeTransaksiLookup[$meta['value']] = $meta['id'];
                }

                // GET ALL COA
                $coa = $this->Sub_AkunsModel->findAll();
                $coaLookup = [];
                foreach ($coa as $c) {
                    $coaLookup[$c['no_sub']] = $c['id'];
                }

                // GET ALL DEPARTEMEN
                $divisi = $this->divisionModel->where('company_id', $this->this_company_id)->findAll();
                $divisiLookup = [];
                foreach ($divisi as $d) {
                    $divisiLookup[$d['divisi']] = $d['id'];
                }
                $divisiLookup["ALL"] = null; // JIKA UNTUK SEMUA DEPARTEMEN MAKA SET NULL
                // TRANSAKSI JURNAL
                $transaksiJurnalData = [];
                $row = 3;
                while (trim($sheet->getCell("A" . $row)->getValue()) != '') {
                    $tipeTransaksiValue = trim($sheet->getCell('A' . $row)->getValue());
                    $tglTransaksiValue = $sheet->getCell('B' . $row)->getValue();
                    $noBuktiValue = $sheet->getCell('C' . $row)->getValue();
                    $keteranganValue = $sheet->getCell('D' . $row)->getValue();
                    $valasValue = trim($sheet->getCell('E' . $row)->getValue());
                    $exchangeRate = $sheet->getCell('F' . $row)->getValue();
                    $total = $sheet->getCell('G' . $row)->getValue();
                    $transaksiJurnal = $this->transaksiJurnalModel
                        ->join('jurnal_umum', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
                        ->where('no_bukti', $noBuktiValue)
                        ->where('jurnal_umum.company_id', $this->this_company_id)
                        ->first();

                    if (Date::isDateTime($sheet->getCell('B' . $row))) {
                        $date = Date::excelToDateTimeObject($tglTransaksiValue);
                        $formattedDate = $date->format('Y-m-d');
                    } else {
                        $formattedDate = date('Y-m-d', strtotime($tglTransaksiValue));
                    }

                    if ($noBuktiValue != "" && $transaksiJurnal == null) {
                        array_push($transaksiJurnalData, [
                            'no_transaksi' => $noBuktiValue,
                            'tanggal_transaksi' => $formattedDate,
                            'total_debit' => $total,
                            'total_kredit' => $total,
                            'metode_input' => "system",
                            'type_transaksi' => $tipeTransaksiLookup[$tipeTransaksiValue] ?? null,
                            'no_bukti' => $noBuktiValue,
                            'valas' => $valasValue,
                            'valas_id' => $valutaLookup[$valasValue] ?? null,
                            'exchange_rate' => $exchangeRate,
                            'uraian_transaksi' => $keteranganValue,
                        ]);
                    }
                    $row++;
                }

                // INSERTKAN TRANSAKSI JURNAL
                $this->transaksiJurnalModel->insertBatch($transaksiJurnalData);
                // JURNAL UMUM
                $jurnalUmumData = [];
                $row = 3;
                while (trim($sheet->getCell("J" . $row)->getValue()) != '') {
                    $noBuktiValue = trim($sheet->getCell('J' . $row)->getValue());
                    $divisiValue = trim($sheet->getCell('K' . $row)->getValue());
                    $subAccountValue = trim($sheet->getCell('L' . $row)->getValue());
                    $uraianValue = $sheet->getCell('M' . $row)->getValue();
                    $jumlahValue = $sheet->getCell('N' . $row)->getValue();
                    $debetValue = $sheet->getCell('O' . $row)->getValue();
                    $kreditValue = $sheet->getCell('p' . $row)->getValue();

                    $transaksiJurnal = $this->transaksiJurnalModel->where('no_bukti', $noBuktiValue)->first();
                    if ($transaksiJurnal != null) {
                        array_push($jurnalUmumData, [
                            'id_transaksi' => $transaksiJurnal['id'],
                            'id_coa' => $coaLookup[$subAccountValue] ?? null,
                            'company_id' => $this->this_company_id,
                            'divisi_id' => $divisiLookup[$divisiValue] ?? null,
                            'tanggal_jurnal' => date('Y-m-d', strtotime($transaksiJurnal['tanggal_transaksi'])),
                            'debit' => floatval($debetValue),
                            'kredit' => floatval($kreditValue),
                            'valas' => $transaksiJurnal['valas_id'],
                            'kurs' => $transaksiJurnal['exchange_rate'],
                            'keterangan' => $uraianValue ?? "",
                            'id_inputer' => $this->this_user_id,
                            'id_company_inputer' => $this->this_company_id
                        ]);
                    }
                    $row++;
                }

                // Insert "jurnal_umum" data into database
                $this->jurnalUmumModel->insertBatch($jurnalUmumData);
                $this->db->transCommit();
                return $this->response->setJSON(['success' => true, 'message' => 'Import berhasil']);
            } catch (Exception $e) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Import gagal: ' . $e->getMessage(),
                    'error' => $e->getTraceAsString(),
                ]);
            }
        } else {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJSON($data);
        }
    }

    public function indexBackup()
    {
        $accountModuleModel = new AccountModuleModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $accountModuleData = $accountModuleModel->asObject()->findAll();
        $subAkunsModel = $Sub_AkunsModel->getAPAR($this->this_company_id);
        foreach ($subAkunsModel as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }

        $dataMetadataTipeTransaksi = $this->MetadataModel
            ->asObject()
            ->where('name', 'tipe_transaksi')
            ->findAll();
        foreach ($dataMetadataTipeTransaksi as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }

        $data = [
            "dataAccountModule" => $accountModuleData,
            "dataMetadataTipeTransaksi" => $dataMetadataTipeTransaksi,
            "dataValuta" => $this->metadataModel->get_by_name('Valuta'),
            "subAkuns" => $subAkunsModel
        ];

        return view('Accounting/jurnalUmum/index', $data);
    }

    public function save()
    {
        try {
            $nm = $this->request->getPost('cari');
            $total_debit = 0;
            $total_credit = 0;
            $result = array();

            $id_transaksi_jurnal = $this->transaksiJurnalModel->getIdTransaksiLast();
            foreach ($nm as $key => $val) {
                if (isset($_POST['debit'][$key])) {
                    $debitValue = ($_POST['debit'][$key] != "") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['debit'][$key])) : 0;
                } else {
                    $debitValue = 0;
                }

                if (isset($_POST['kredit'][$key])) {
                    $kreditValue = ($_POST['kredit'][$key] != "") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['kredit'][$key])) : 0;
                } else {
                    $kreditValue = 0;
                }

                if (isset($_POST['kurs'][$key])) {
                    $kursValue = ($_POST['kurs'][$key] != "") ? (float) str_replace(",", ".", str_replace(["Rp. ", "."], "", $_POST['kurs'][$key])) : 0;
                } else {
                    $kursValue = 0;
                }

                if ($debitValue == 0) {
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' => $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tgl_transaksi'][$key]))),
                        'debit' => $debitValue,
                        'kredit' => $kreditValue,
                        'valas' => $_POST['valas'][$key],
                        'kurs' => $kursValue,
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id
                    );
                    $total_credit += $kreditValue;
                } elseif ($kreditValue == 0) {
                    $result[] = array(
                        'id_transaksi' => $id_transaksi_jurnal,
                        'id_coa' =>  $this->encrypter->decrypt(hex2bin($_POST['cari'][$key])),
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tgl_transaksi'][$key]))),
                        'debit' => $debitValue,
                        'kredit' => $kreditValue,
                        'valas' => $_POST['valas'][$key],
                        'kurs' => $kursValue,
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id
                    );
                    $total_debit += $debitValue;
                }
            }
            $kodeTransaksi = "";
            $dataMetadataTipeTransaksi = $this->MetadataModel
                ->asObject()
                ->where('id', $this->encrypter->decrypt(hex2bin($this->request->getPost('type_transaksi'))))
                ->findAll();
            foreach ($dataMetadataTipeTransaksi as $val) {
                $kodeTransaksi = $val->description;
            }

            $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
            $dataTransaksiJurnal = [
                'no_transaksi' => $no_transaksi_jurnal,
                'tanggal_transaksi' => date('Y-m-d'),
                'total_debit' => $total_debit,
                'total_kredit' => $total_credit,
                'metode_input' => 'manual',
                'type_transaksi' => $this->encrypter->decrypt(hex2bin($this->request->getPost('type_transaksi'))),
                'no_bukti' => $this->request->getPost('no_bukti') ? $this->request->getPost('no_bukti') : $no_transaksi_jurnal,
                'valas' => 'IDR',
                'exchange_rate' => 1,
            ];
            $this->jurnalUmumModel->insertJurnalBatch($result);
            $this->transaksiJurnalModel->insertTransaksiJurnal($dataTransaksiJurnal);

            session()->setFlashdata('success_message', 'Data Berhasil disimpan');
        } catch (\Exception $e) {
            session()->setFlashdata('error_message', 'Gagal Coba Cek Kembali Semua Field');
        }
        return redirect()->to('jurnal');
    }

    public function searchSubAkun()
    {
        $query = strtolower(str_replace(' ', '', $this->request->getPost('query')));

        $subAkunModel = new Sub_AkunsModel();
        $subAkuns = $subAkunModel->searchSubAkun($query);

        $output = array(); // Menggunakan array untuk menyimpan data
        foreach ($subAkuns as $sub_akun) {
            $sub_akun['hexid'] = bin2hex($this->encrypter->encrypt($sub_akun['id'])); // Menyimpan nilai yang dienkripsi dengan kunci 'hexid'
            $output[] = $sub_akun; // Menambahkan $sub_akun ke dalam array $output
        }

        // Mengembalikan output dalam format JSON
        echo json_encode($output);
        return;
    }

    public function searchSubAkunExact()
    {
        $query = strtolower(str_replace(' ', '', $this->request->getPost('query')));

        $subAkunModel = new Sub_AkunsModel();
        $subAkuns = $subAkunModel->searchSubAkunExact($query);

        $output = array(); // Menggunakan array untuk menyimpan data
        foreach ($subAkuns as $sub_akun) {
            $sub_akun['hexid'] = bin2hex($this->encrypter->encrypt($sub_akun['id'])); // Menyimpan nilai yang dienkripsi dengan kunci 'hexid'
            $output[] = $sub_akun; // Menambahkan $sub_akun ke dalam array $output
        }

        // Mengembalikan output dalam format JSON
        echo json_encode($output);
        return;
    }

    public function generateNoBukti()
    {
        $kodeTransaksi = "";
        $no_transaksi_jurnal = "";
        try {
            $dataMetadataTipeTransaksi = $this->MetadataModel
                ->asObject()
                ->where('id', $this->request->getPost('transaksi'))
                ->findAll();
            foreach ($dataMetadataTipeTransaksi as $val) {
                $kodeTransaksi = $val->description;
            }

            $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);

            return response()->setJSON([
                'codeNew' => $no_transaksi_jurnal,
                'token' => csrf_hash(),

            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $no_transaksi_jurnal . "-????",
                'token' => csrf_hash()
            ]);
        }
    }

    public function insertDataPembelian($poID, $type, $kategori, $module, $penerimaanBarangId)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";

        $db = \Config\Database::connect();
        try {

            if ($type == "BAHAN BAKU") {
                if ($kategori == "LOKAL") {
                    $dataPOBB = $this->rMPurchaseOrderModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
                    if ($dataPOBB) {
                        $result = array();
                        $resultTransaksiJurnal = array();
                        $resultTransaksiPembelian = array();
                        foreach ($dataPOBB as $dataBB) {
                            $totalPO = 0;
                            $kodeTransaksi = "";
                            $idTransaksi = "";
                            $keteranganJurnal = $this->rMPurchaseOrderDetailModel->getSpesifikasiBarangAsString($dataBB->id);

                            // $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBB->division_id);
                            $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBB->supplier_id);
                            $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                            $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                            $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();

                            $dataPOBBDetail = $this->rMPurchaseOrderDetailModel->asObject()->where('deletedAt', null)->where('rm_purchase_order_id', $dataBB->id)->findAll();
                            $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembelian')->findAll();
                            $dataMetadataValutaIDR = $this->MetadataModel->asObject()->where('name', 'Valuta')->where('value', 'IDR')->first();

                            foreach ($dataSupplier as $value) {
                                foreach ($dataAccountSupplier as $valueAccount) {
                                    if ($value->id == $valueAccount->supplier_id) {
                                        $UtangAP = $valueAccount->ap_id;
                                        $UtangAR = $valueAccount->ar_id;
                                    }
                                }
                                foreach ($dataAccountModule as $valueModule) {
                                    if ($valueModule->type == $type && $valueModule->kategori == $kategori && $valueModule->module == $module) {
                                        $UtangAP = $valueModule->ap_id;
                                        $UtangAR = $valueModule->ar_id;
                                    }
                                }
                            }
                            // end inisialisasi account

                            // start inisialisasi kode transaksi
                            foreach ($dataMetadataTipeTransaksi as $val) {
                                $kodeTransaksi = $val->description;
                                $idTransaksi = $val->id;
                            }
                            // end inisialisasi kode transaksi
                            $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                            $resultTransaksiJurnal = array(
                                'no_transaksi' => $no_transaksi_jurnal,
                                'no_bukti' => $no_transaksi_jurnal,
                                'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                'total_debit' => $totalPO,
                                'total_kredit' => $totalPO,
                                'metode_input' => 'system',
                                'type_transaksi' => $idTransaksi,
                                'valas' => 'IDR',
                                'valas_id' => $dataMetadataValutaIDR->id,
                                'uraian_transaksi' => $dataBB->po_no, // Untuk transaksi_jurnal memakai po_no
                                'penerimaan_barang_id' => $penerimaanBarangId,
                            );

                            // ambil id dari transaksi jurnal untuk jurnal umum
                            $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                            // start input jurnal dari banyak detail barang

                            try {
                                $barangAP = "";
                                $barangAR = "";
                                $barangAPFound = "";
                                foreach ($dataAccountBarang as $value) {
                                    if ($dataPOBBDetail[0]->barang1_id == $value->barang_master_id && $dataBB->company_id == $value->company_id && $dataPOBBDetail[0]->barang2_id == $value->barang_master_spesifikasi_id && $dataPOBBDetail[0]->note == $value->keterangan && $dataBB->divisi_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                        $barangAP = $value->ap_id;
                                        $barangAR = $value->ar_id;
                                        $barangAPFound = true;
                                    }
                                    if ($dataPOBBDetail[0]->barang1_id == $value->barang_master_id && $dataBB->company_id == $value->company_id && $dataBB->divisi_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                        $barangAP = $value->ap_id;
                                        $barangAR = $value->ar_id;
                                        $barangAPFound = true;
                                    }
                                }

                                $result[] = array(
                                    'id_transaksi' => $id_transaksi_jurnal,
                                    'divisi_id' => $dataBB->divisi_id,
                                    'company_id' => $this->this_company_id,
                                    'id_coa' =>  $barangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => $dataBB->total_before_pph,
                                    'kredit' => 0,
                                    'valas' => $dataMetadataValutaIDR->id,
                                    'kurs' => 1,
                                    'keterangan' => $keteranganJurnal,
                                    'id_inputer' => session()->get("login")->user_id
                                );

                                // Untuk insert ke jurnal umum
                                $result[] = array(
                                    'id_transaksi' => $id_transaksi_jurnal,
                                    'divisi_id' => $dataBB->divisi_id,
                                    'company_id' => $this->this_company_id,
                                    'id_coa' =>  $barangAR ? $barangAR : $UtangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => 0,
                                    'kredit' => $dataBB->total_before_pph,
                                    'valas' => $dataMetadataValutaIDR->id,
                                    'kurs' => 1,
                                    'keterangan' => $keteranganJurnal,
                                    'id_inputer' => session()->get("login")->user_id
                                );
                            } catch (Exception $e) {
                                // Log the error or handle it appropriately
                                log_message('error', 'Error in journal entry processing: ' . $e->getMessage());
                                // You might want to return an error response or re-throw the exception
                                throw new Exception('Failed to process journal entries: ' . $e->getMessage());
                            }

                            // end input jurnal dari banyak detail barang

                            // update total debit dan kredit dari total nilai pada jurnal umum
                            $this->transaksiJurnalModel->update(
                                $id_transaksi_jurnal,
                                [
                                    'total_debit' => $dataBB->total_before_pph,
                                    'total_kredit' => $dataBB->total_before_pph,
                                ]
                            );

                            $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                            $resultTransaksiJurnal[] = array(
                                'no_transaksi' => $no_transaksi_jurnal,
                                'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                'total_debit' => $dataBB->total_before_pph,
                                'total_kredit' => $dataBB->total_before_pph,
                                'metode_input' => 'system',
                                'tipe_barang' => $type,
                                'kategori_barang' => $kategori,
                                'po_id' => $poID,
                                'type_transaksi' => $idTransaksi,
                                'no_bukti' => $no_transaksi_jurnal,
                                'valas' => 'IDR',
                                'exchange_rate' => 1,
                            );

                            $resultTransaksiPembelian[] = array(
                                'id_local_bb' => $poID,
                                'id_supplier' => $dataBB->supplier_id,
                                'id_transaksi_jurnal' => $id_transaksi_jurnal,
                                'tgl_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            );
                        }
                        $this->jurnalUmumModel->insertJurnalBatch($result);
                        $this->transaksiPembelianModel->insertBatchTransaksiPembelian($resultTransaksiPembelian);
                    }
                } else {
                    $dataPOBB = $this->rmImportPOModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
                    if ($dataPOBB) {
                        $result = array();
                        $resultTransaksiJurnal = array();
                        $resultTransaksiPembelian = array();
                        foreach ($dataPOBB as $dataBB) {
                            $totalPO = 0;
                            $kodeTransaksi = "";
                            $idTransaksi = "";
                            $keteranganJurnal = $this->rmImportPODetailModel->getSpesifikasiBarangAsString($dataBB->id);

                            $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBB->division_id, $dataBB->company_id);
                            $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBB->supplier_id);
                            $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                            $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                            $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                            $kursData = $this->kursModel->getByMetaId($dataBB->currency, $dataBB->po_date);
                            $metaValuta = $this->metadataModel->get_by_name('Valuta');
                            foreach ($metaValuta as $valueValuta) {
                                if ($dataBB->currency == $valueValuta['id']) {
                                    $valasTransaksi = $valueValuta['value'];
                                    if ($kursData) {
                                        $exchangeTransaksi = $kursData->nilai_kurs;
                                    } else {
                                        $exchangeTransaksi = 1;
                                    }
                                }
                            }

                            $dataPOBBDetail = $this->rmImportPODetailModel
                                ->asObject()
                                ->select('rm_import_po_details.*,barang_master.barang_name,barang_master_spesifikasi.spesifikasi')
                                ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
                                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_import_po_details.spesifikasi_id', 'left')
                                ->where('rm_import_po_details.deletedAt', null)
                                ->where('rm_import_po_id', $dataBB->id)
                                ->findAll();

                            $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembelian')->findAll();

                            // $id_transaksi_jurnal = $this->transaksiJurnalModel->getIdTransaksiLast();

                            // start inisialisasi account
                            foreach ($dataDepartment as $value) {
                                $KasAP = $value->coa_kas_id;
                                $KasAR = $value->coa_piutang_id;
                            }
                            foreach ($dataSupplier as $value) {
                                foreach ($dataAccountSupplier as $valueAccount) {
                                    if ($value->id == $valueAccount->supplier_id) {
                                        $UtangAP = $valueAccount->ap_id;
                                        $UtangAR = $valueAccount->ar_id;
                                    }
                                }
                                foreach ($dataAccountModule as $valueModule) {
                                    if ($valueModule->type == $type && $valueModule->kategori == $kategori && $valueModule->module == $module) {
                                        $UtangAP = $valueModule->ap_id;
                                        $UtangAR = $valueModule->ar_id;
                                    }
                                }
                            }
                            // end inisialisasi account
                            // start inisialisasi kode transaksi
                            foreach ($dataMetadataTipeTransaksi as $val) {
                                $kodeTransaksi = $val->description;
                                $idTransaksi = $val->id;
                            }
                            // input ke transaksi jurnal
                            $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                            $resultTransaksiJurnal = array(
                                'no_transaksi' => $no_transaksi_jurnal,
                                'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                'total_debit' => $totalPO * $exchangeTransaksi,
                                'total_kredit' => $totalPO * $exchangeTransaksi,
                                'metode_input' => 'system',
                                'tipe_barang' => $type,
                                'kategori_barang' => $kategori,
                                'po_id' => $poID,
                                'type_transaksi' => $idTransaksi,
                                'no_bukti' => $no_transaksi_jurnal,
                                'valas' => $valasTransaksi,
                                'valas_id' => $dataBB->currency,
                                'exchange_rate' => $exchangeTransaksi,
                                'uraian_transaksi' => $dataBB->po_no, // Untuk transaksi_jurnal memakai po_no
                                'penerimaan_barang_id' => $penerimaanBarangId,
                            );

                            // ambil id dari transaksi jurnal untuk jurnal umum
                            $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                            // end inisialisasi kode transaksi
                            // start input jurnal dari banyak detail barang
                            foreach ($dataPOBBDetail as $dataBBDetail) {
                                $totalPO += ($dataBBDetail->total);
                                $barangAPFound = false;
                                foreach ($dataAccountBarang as $value) {
                                    if ($dataBBDetail->barang_id == $value->barang_master_id && $dataBB->company_id == $value->company_id && $dataBBDetail->spesifikasi_id == $value->barang_master_spesifikasi_id && $dataBBDetail->note == $value->keterangan && $dataBB->division_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                        $barangAP = $value->ap_id;
                                        $barangAR = $value->ar_id;
                                        $barangAPFound = true;
                                    }
                                    if ($dataBBDetail->barang_id == $value->barang_master_id && $dataBB->company_id == $value->company_id && $dataBB->division_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                        $barangAP = $value->ap_id;
                                        $barangAR = $value->ar_id;
                                        $barangAPFound = true;
                                    }
                                }
                                if (!$barangAPFound) {
                                    // Collect errors
                                    $errors[] = "Barang {$dataBBDetail->barang_name} {$dataBBDetail->spesifikasi} Tidak Memiliki Akun COA";
                                } else {
                                    $result[] = array(
                                        'id_transaksi' => $id_transaksi_jurnal,
                                        'divisi_id' => $dataBB->division_id,
                                        'company_id' => $this->this_company_id,
                                        'id_coa' =>  $barangAP,
                                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                        'debit' => $dataBBDetail->total * $exchangeTransaksi,
                                        'kredit' => 0,
                                        'valas' =>  $dataBB->currency,
                                        'kurs' => $exchangeTransaksi,
                                        'keterangan' => $keteranganJurnal,
                                        'id_inputer' => session()->get("login")->user_id
                                    );
                                }
                                if (!empty($errors)) {
                                    return response()->setJSON([
                                        "status" => false,
                                        "message" => implode(', ', $errors),
                                        'token' => csrf_hash()
                                    ]);
                                }
                            }
                            // end input jurnal dari banyak detail barang
                            // update total debit dan kredit dari total nilai pada jurnal umum
                            $this->transaksiJurnalModel->update(
                                $id_transaksi_jurnal,
                                [
                                    'total_debit' => $totalPO,
                                    'total_kredit' => $totalPO,
                                ]
                            );
                            //untuk insert ke jurnal umum
                            $result[] = array(
                                'id_transaksi' => $id_transaksi_jurnal,
                                'divisi_id' => $dataBB->division_id,
                                'company_id' => $this->this_company_id,
                                'id_coa' =>  $UtangAP,
                                'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                'debit' => 0,
                                'kredit' => $totalPO * $exchangeTransaksi,
                                'valas' =>  $dataBB->currency,
                                'kurs' => $exchangeTransaksi,
                                'keterangan' => $keteranganJurnal,
                                'id_inputer' => session()->get("login")->user_id
                            );

                            $resultTransaksiPembelian[] = array(
                                'id_import_bb' => $poID,
                                'id_supplier' => $dataBB->supplier_id,
                                'id_transaksi_jurnal' => $id_transaksi_jurnal,
                                'tgl_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            );
                        }
                        $this->jurnalUmumModel->insertJurnalBatch($result);
                        $this->transaksiPembelianModel->insertBatchTransaksiPembelian($resultTransaksiPembelian);
                    }
                }
            } else {
                $dataPB = $this->penerimaanBarangModel->asObject()
                    ->where('deletedAt', null)
                    ->where('id', $penerimaanBarangId)
                    ->first();

                $dataPBD = $this->penerimaanBarangDetailModel
                    ->select('penerimaan_barang_detail.*, 
                        am_purchase_orders.po_no, 
                        am_purchase_orders.po_date, 
                        am_purchase_orders.currency,
                        am_purchase_order_details.note,
                        barang_master.barang_name as nama_barang,
                        barang_master_spesifikasi.spesifikasi as nama_spesifikasi')
                    ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
                    ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
                    ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
                    ->where('penerimaan_barang_detail.deletedAt', null)
                    ->where('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangId)
                    ->asObject()
                    ->findAll();

                if (!$dataPB || empty($dataPBD)) {
                    return response()->setJSON([
                        "status" => false,
                        "message" => "Data LPB atau detail tidak ditemukan",
                        'token' => csrf_hash()
                    ]);
                }

                $result = [];
                $errors = [];

                $valasTransaksi = "IDR";
                $exchangeTransaksi = 1;
                $totalDebit = 0;

                // ambil COA dan data referensi dari LPB
                $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataPB->divisi_id, $dataPB->company_id);
                $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataPB->supplier_id);
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                $metaValuta = $this->metadataModel->get_by_name('Valuta');

                // ambil kurs dari PO pertama dalam multiple_po_id
                $kursData = null;
                $valasID = "30";
                $poIDs = [];
                $poNosString = '';

                if (!empty($dataPB->multiple_po_id)) {
                    if (is_string($dataPB->multiple_po_id) && str_contains($dataPB->multiple_po_id, '[')) {
                        $poIDs = json_decode($dataPB->multiple_po_id, true);
                    } else {
                        $poIDs = explode(',', $dataPB->multiple_po_id);
                    }

                    if (!empty($poIDs)) {
                        $firstPO = $this->aMPurchaseOrderModel->asObject()
                            ->whereIn('id', $poIDs)
                            ->where('deletedAt', null)
                            ->first();

                        if ($firstPO) {
                            $valasID = $firstPO->currency == "0" ? "30" : $firstPO->currency;
                            $kursData = $this->kursModel->getByMetaId($valasID, $firstPO->po_date);
                        }
                    }
                }

                // mapping valas
                foreach ($metaValuta as $valueValuta) {
                    if ($valasID == $valueValuta['id']) {
                        $valasTransaksi = $valueValuta['value'];
                        $exchangeTransaksi = $kursData ? $kursData->nilai_kurs : 1;
                    }
                }

                // ambil akun supplier
                $UtangAP = null;
                foreach ($dataSupplier as $sup) {
                    foreach ($dataAccountSupplier as $accSup) {
                        if ($sup->id == $accSup->supplier_id) {
                            $UtangAP = $accSup->ap_id;
                        }
                    }
                    foreach ($dataAccountModule as $accMod) {
                        if ($accMod->type == $type && $accMod->kategori == $kategori && $accMod->module == $module) {
                            $UtangAP = $accMod->ap_id;
                        }
                    }
                }

                if (!$UtangAP) {
                    return response()->setJSON([
                        "status" => false,
                        "message" => "Akun utang tidak ditemukan",
                        'token' => csrf_hash()
                    ]);
                }

                // buat transaksi jurnal utama
                $dataMetadata = $this->MetadataModel->asObject()
                    ->where('name', 'tipe_transaksi')
                    ->where('value', 'Pembelian')
                    ->first();

                $kodeTransaksi = $dataMetadata->description ?? 'Pembelian';
                $idTransaksi = $dataMetadata->id ?? null;
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                $tanggalJurnal = $dataPB->tanggal;

                $resultTransaksiJurnal = [
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => $tanggalJurnal,
                    'total_debit' => 0,
                    'total_kredit' => 0,
                    'metode_input' => 'system',
                    'tipe_barang' => $type,
                    'kategori_barang' => $kategori,
                    'po_id' => $dataPB->multiple_po_id,
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => $valasTransaksi,
                    'valas_id' => $valasID,
                    'exchange_rate' => $exchangeTransaksi,
                    'uraian_transaksi' => $dataPB->no_penerimaan_barang,
                    'penerimaan_barang_id' => $penerimaanBarangId,
                ];

                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

                // isi jurnal umum berdasarkan detail
                foreach ($dataPBD as $detail) {
                    $barangAP = null;
                    $poNos = array_unique(array_filter(array_map(function ($d) {
                        return $d->po_no ?? null;
                    }, $dataPBD)));

                    $poNosString = implode(', ', $poNos);

                    foreach ($dataAccountBarang as $accBarang) {
                        if (
                            $detail->barang_id == $accBarang->barang_master_id &&
                            $dataPB->company_id == $accBarang->company_id &&
                            $detail->spesifikasi_id == $accBarang->barang_master_spesifikasi_id &&
                            $dataPB->divisi_id == $accBarang->divisi_id &&
                            $accBarang->ap_id
                        ) {
                            $barangAP = $accBarang->ap_id;
                            break;
                        }
                    }

                    if (!$barangAP) {
                        $errors[] = "Barang {$detail->nama_barang} {$detail->nama_spesifikasi} tidak memiliki akun COA";
                    } else {
                        $jumlah = $detail->sub_total;
                        $totalDebit += $jumlah;

                        $result[] = [
                            'id_transaksi' => $id_transaksi_jurnal,
                            'divisi_id' => $dataPB->divisi_id,
                            'company_id' => $this->this_company_id,
                            'id_coa' => $barangAP,
                            'tanggal_jurnal' => $tanggalJurnal,
                            'debit' => $jumlah * $exchangeTransaksi,
                            'kredit' => 0,
                            'valas' => $valasID,
                            'kurs' => $exchangeTransaksi,
                            'keterangan' => $detail->po_no,
                            'id_inputer' => session()->get("login")->user_id
                        ];
                    }
                }

                if (!empty($errors)) {
                    return response()->setJSON([
                        "status" => false,
                        "message" => implode(', ', $errors),
                        'token' => csrf_hash()
                    ]);
                }

                // jurnal utang (kredit)
                $result[] = [
                    'id_transaksi' => $id_transaksi_jurnal,
                    'divisi_id' => $dataPB->divisi_id,
                    'company_id' => $this->this_company_id,
                    'id_coa' => $UtangAP,
                    'tanggal_jurnal' => $tanggalJurnal,
                    'debit' => 0,
                    'kredit' => $totalDebit * $exchangeTransaksi,
                    'valas' => $valasID,
                    'kurs' => $exchangeTransaksi,
                    'keterangan' => $poNosString,
                    'id_inputer' => session()->get("login")->user_id
                ];

                // simpan semua jurnal umum
                $this->jurnalUmumModel->insertJurnalBatch($result);

                // update total debit/kredit di header
                $this->transaksiJurnalModel->update($id_transaksi_jurnal, [
                    'total_debit' => $totalDebit * $exchangeTransaksi,
                    'total_kredit' => $totalDebit * $exchangeTransaksi,
                ]);

                $purchaseId = json_decode($dataPB->multiple_po_id, true);
                if (empty($purchaseId)) {
                    // simpan ke transaksi pembelian
                    $this->transaksiPembelianModel->insertBatchTransaksiPembelian([[
                        'id_po_bp' => $purchaseId,
                        'id_supplier' => $dataPB->supplier_id,
                        'id_transaksi_jurnal' => $id_transaksi_jurnal,
                        'tgl_transaksi' => $tanggalJurnal,
                    ]]);
                }
            }
            $db->transCommit();
        } catch (Exception $e) {
            $db->transRollback();
            var_dump($e->getMessage(), $e->getFile(), $e->getLine());
            die;
        }
    }

    public function insertDataPembelianBB($poID, $type, $kategori, $module, $idTransaksiJurnal, $companyIDs)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";

        $db = \Config\Database::connect();
        try {

            if ($type == "BAHAN BAKU") {
                if ($kategori == "LOKAL") {
                    $dataPOBB = $this->rMPurchaseOrderModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
                    if ($dataPOBB) {
                        $result = array();
                        $resultTransaksiJurnal = array();
                        $resultTransaksiPembelian = array();
                        foreach ($dataPOBB as $dataBB) {
                            $totalPO = 0;
                            $kodeTransaksi = "";
                            $idTransaksi = "";
                            $keteranganJurnal = $this->rMPurchaseOrderDetailModel->getSpesifikasiBarangAsString($dataBB->id);

                            // $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBB->division_id);
                            $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBB->supplier_id);
                            $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                            $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                            $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();

                            $dataPOBBDetail = $this->rMPurchaseOrderDetailModel->asObject()->where('deletedAt', null)->where('rm_purchase_order_id', $dataBB->id)->findAll();
                            $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'Pembelian')->findAll();
                            $dataMetadataValutaIDR = $this->MetadataModel->asObject()->where('name', 'Valuta')->where('value', 'IDR')->first();

                            foreach ($dataSupplier as $value) {
                                foreach ($dataAccountSupplier as $valueAccount) {
                                    if ($value->id == $valueAccount->supplier_id) {
                                        $UtangAP = $valueAccount->ap_id;
                                        $UtangAR = $valueAccount->ar_id;
                                    }
                                }
                                foreach ($dataAccountModule as $valueModule) {
                                    if ($valueModule->type == $type && $valueModule->kategori == $kategori && $valueModule->module == $module) {
                                        $UtangAP = $valueModule->ap_id;
                                        $UtangAR = $valueModule->ar_id;
                                    }
                                }
                            }
                            // end inisialisasi account

                            // start inisialisasi kode transaksi
                            foreach ($dataMetadataTipeTransaksi as $val) {
                                $kodeTransaksi = $val->description;
                                $idTransaksi = $val->id;
                            }

                            try {
                                $barangAP = "";
                                $barangAR = "";
                                $barangAPFound = "";
                                foreach ($dataAccountBarang as $value) {
                                    if ($dataPOBBDetail[0]->barang1_id == $value->barang_master_id && $dataBB->company_id == $value->company_id && $dataPOBBDetail[0]->barang2_id == $value->barang_master_spesifikasi_id && $dataPOBBDetail[0]->note == $value->keterangan && $dataBB->divisi_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                        $barangAP = $value->ap_id;
                                        $barangAR = $value->ar_id;
                                        $barangAPFound = true;
                                    }
                                    if ($dataPOBBDetail[0]->barang1_id == $value->barang_master_id && $dataBB->company_id == $value->company_id && $dataBB->divisi_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                        $barangAP = $value->ap_id;
                                        $barangAR = $value->ar_id;
                                        $barangAPFound = true;
                                    }
                                }

                                $result[] = array(
                                    'id_transaksi' => $idTransaksiJurnal,
                                    'divisi_id' => $dataBB->divisi_id,
                                    'company_id' => $companyIDs,
                                    'id_coa' =>  $barangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => $dataBB->total_before_pph,
                                    'kredit' => 0,
                                    'valas' => $dataMetadataValutaIDR->id,
                                    'kurs' => 1,
                                    'keterangan' => $keteranganJurnal,
                                    'id_inputer' => session()->get("login")->user_id
                                );

                                // Untuk insert ke jurnal umum
                                $result[] = array(
                                    'id_transaksi' => $idTransaksiJurnal,
                                    'divisi_id' => $dataBB->divisi_id,
                                    'company_id' => $companyIDs,
                                    'id_coa' =>  $barangAR ? $barangAR : $UtangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => 0,
                                    'kredit' => $dataBB->total_before_pph,
                                    'valas' => $dataMetadataValutaIDR->id,
                                    'kurs' => 1,
                                    'keterangan' => $keteranganJurnal,
                                    'id_inputer' => session()->get("login")->user_id
                                );
                            } catch (Exception $e) {
                                // Log the error or handle it appropriately
                                log_message('error', 'Error in journal entry processing: ' . $e->getMessage());
                                // You might want to return an error response or re-throw the exception
                                throw new Exception('Failed to process journal entries: ' . $e->getMessage());
                            }

                            // end input jurnal dari banyak detail barang

                            // update total debit dan kredit dari total nilai pada jurnal umum
                            $this->transaksiJurnalModel->update($idTransaksiJurnal, [
                                'total_debit' => $dataBB->total_before_pph,
                                'total_kredit' => $dataBB->total_before_pph,
                            ]);
                        }
                        $this->jurnalUmumModel->insertJurnalBatch($result);
                    }
                }
            }

            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            var_dump($e->getMessage(), $e->getFile(), $e->getLine());
            die;
        }
    }

    public function insertDataPembayaran($payID, $module, $divisi)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";
        $dataPO = "";
        if ($module == "LOKAL BB") {
            $result = array();
            $POlocal = $this->localPOPaymentModel->asObject()->find($payID);
            if ($POlocal) {
                $dataSupplier = $this->supplierModel->getSupplierForJurnal($POlocal->supplier_id);
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'PEMBAYARAN')->findAll();

                foreach ($dataSupplier as $value) {
                    foreach ($dataAccountSupplier as $valueAccount) {
                        if ($value->id == $valueAccount->supplier_id) {
                            $UtangAP = $valueAccount->ap_id;
                            $UtangAR = $valueAccount->ar_id;
                        }
                    }
                    foreach ($dataAccountModule as $valueModule) {
                        if ($valueModule->type == strtoupper($value->type) && $valueModule->kategori == $module && $valueModule->module == "pembelian") {
                            $UtangAP = $valueModule->ap_id;
                            $UtangAR = $valueModule->ar_id;
                        }
                    }
                }
                // start inisialisasi kode transaksi
                foreach ($dataMetadataTipeTransaksi as $val) {
                    $kodeTransaksi = $val->description;
                    $idTransaksi = $val->id;
                }
                // end inisialisasi kode transaksi
                // input ke transaksi jurnal
                $no_transaksi_jurnal = $POlocal->payment_no;
                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                    'total_debit' => $POlocal->amount +  $POlocal->amount_pajak,
                    'total_kredit' => $POlocal->amount +  $POlocal->amount_pajak,
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $POlocal->payment_no,
                    'valas' => 'IDR',
                    'valas_id' => '30',
                    'exchange_rate' => 1,
                );

                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

                //untuk insert ke jurnal umum
                $multiplePoIds = str_replace(['[', ']'], '', $POlocal->multiple_po_id);
                $PoIdsArray = explode(',', $multiplePoIds);

                $totalNominal = 0;
                $dataPOdesc = [];
                $firstDesc = true; // ✅ penanda hanya ambil sekali

                foreach ($PoIdsArray as $poId) {
                    $dataPO = $this->localPOPaymentModel->asObject()
                        ->select('local_po_payments.*, local_po_payment_details.*, suppliers.name as supplier_name')
                        ->join('local_po_payment_details', 'local_po_payment_details.local_po_payment_id = local_po_payments.id', 'left')
                        ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id', 'left')
                        ->where('local_po_payment_details.tipe', 'BB')
                        ->where('local_po_payments.id', $payID)
                        ->where('local_po_payments.deletedAt', null)
                        ->where('local_po_payment_details.deletedAt', null)
                        ->where('local_po_payment_details.rm_purchase_order_id', $poId)
                        ->findAll();

                    // Hitung total semua PO dari detail
                    foreach ($dataPO as $value) {
                        $totalNominal += $value->total; 
                    }

                    // ✅ Deskripsi cukup sekali aja (pas loop pertama yg ada datanya)
                    if ($firstDesc && !empty($dataPO)) {
                        $cleanedPO = str_replace(['[', ']', '"', "\\"], '', $dataPO[0]->multiple_po_no);
                        $dataPOdesc[] = $dataPO[0]->supplier_name . ' - ' . $cleanedPO;
                        $firstDesc = false; // setelah sekali, nggak ambil lagi
                    }
                }

                // foreach ($PoIdsArray as $poId) {

                //     $sumValue = 0;
                //     $dataPO = $this->localPOPaymentModel->asObject()
                //         ->select('local_po_payments.*, local_po_payment_details.*, suppliers.name as supplier_name')
                //         ->join('local_po_payment_details', 'local_po_payment_details.local_po_payment_id = local_po_payments.id', 'left')
                //         ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id', 'left')
                //         ->where('local_po_payments.id', $payID)
                //         ->where('local_po_payments.deletedAt', null)
                //         ->where('local_po_payment_details.deletedAt', null)
                //         ->where('local_po_payment_details.rm_purchase_order_id', $poId)
                //         ->groupBy('local_po_payment_details.local_po_payment_id')
                //         ->findAll();

                //     foreach ($dataPO as $value) {

                //         $cleanedPO = str_replace(['[', ']', '"', "\\"], '', $value->multiple_po_no);
                //         $dataPO = $value->supplier_name . ' - ' . $cleanedPO;

                //         $sumValue = $value->total;
                //         $result[] = array(
                //             'id_transaksi'      => $id_transaksi_jurnal,
                //             'id_coa'            => $POlocal->akun_kas == 0 || $POlocal->akun_kas == NULL ? $UtangAR : $POlocal->akun_kas,
                //             'company_id'        => $POlocal->company_id,
                //             'divisi_id'         => $divisi,
                //             'supplier_id'       => $POlocal->supplier_id,
                //             'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                //             'debit'             => ($sumValue),
                //             'kredit'            => 0,
                //             'valas'             => '30',
                //             'kurs'              => 1,
                //             'keterangan'        => $POlocal->keterangan,
                //             'id_inputer'        => session()->get("login")->user_id
                //         );
                //         $result[] = array(
                //             'id_transaksi'      => $id_transaksi_jurnal,
                //             'id_coa'            => $POlocal->akun_selisih,
                //             'company_id'        => $POlocal->company_id,
                //             'divisi_id'         => $divisi,
                //             'supplier_id'       => $POlocal->supplier_id,
                //             'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                //             'debit'             => 0,
                //             'kredit'            => ($sumValue),
                //             'valas'             => '30',
                //             'kurs'              => 1,
                //             'keterangan'        => $POlocal->keterangan,
                //             'id_inputer'        => session()->get("login")->user_id
                //         );

                //         if ($value->total_pay_pph) {

                //             $cleanedPO = str_replace(['[', ']', '"', "\\"], '', $value->multiple_po_no);
                //             $dataPO = $value->supplier_name . ' - ' . $cleanedPO;

                //             $sumValue = $value->total_pay_pph;
                //             $result[] = array(
                //                 'id_transaksi'      => $id_transaksi_jurnal,
                //                 'id_coa'            => $POlocal->akun_pajak,
                //                 'company_id'        => $POlocal->company_id,
                //                 'divisi_id'         => $divisi,
                //                 'supplier_id'       => $POlocal->supplier_id,
                //                 'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                //                 'debit'             => ($sumValue),
                //                 'kredit'            => 0,
                //                 'valas'             => '30',
                //                 'kurs'              => 1,
                //                 'keterangan'        => "PAJAK " . $POlocal->keterangan,
                //                 'id_inputer'        => session()->get("login")->user_id
                //             );
                //             $result[] = array(
                //                 'id_transaksi'      => $id_transaksi_jurnal,
                //                 'id_coa'            => $POlocal->akun_pajak,
                //                 'company_id'        => $POlocal->company_id,
                //                 'divisi_id'         => $divisi,
                //                 'supplier_id'       => $POlocal->supplier_id,
                //                 'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                //                 'debit'             => 0,
                //                 'kredit'            => ($sumValue),
                //                 'valas'             => '30',
                //                 'kurs'              => 1,
                //                 'keterangan'        => "PAJAK " . $POlocal->keterangan,
                //                 'id_inputer'        => session()->get("login")->user_id
                //             );
                //         }
                //     }
                // }

                $result = [];

                // Debit
                $result[] = [
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $POlocal->akun_kas == 0 || $POlocal->akun_kas == NULL ? $UtangAR : $POlocal->akun_kas,
                    'company_id'        => $POlocal->company_id,
                    'divisi_id'         => $divisi,
                    'supplier_id'       => $POlocal->supplier_id,
                    'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                    'debit'             => $totalNominal - ($POlocal->potongan_harga ?? 0 ?? 0),
                    'kredit'            => 0,
                    'valas'             => '30',
                    'kurs'              => 1,
                    'keterangan'        => implode(', ', $dataPOdesc), // semua PO disatukan
                    'id_inputer'        => session()->get("login")->user_id
                ];

                // Kredit
                $result[] = [
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $POlocal->akun_selisih,
                    'company_id'        => $POlocal->company_id,
                    'divisi_id'         => $divisi,
                    'supplier_id'       => $POlocal->supplier_id,
                    'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                    'debit'             => 0,
                    'kredit'            => $totalNominal - ($POlocal->potongan_harga ?? 0 ?? 0),
                    'valas'             => '30',
                    'kurs'              => 1,
                    'keterangan'        => implode(', ', $dataPOdesc),
                    'id_inputer'        => session()->get("login")->user_id
                ];

                return $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        } elseif ($module == "LOKAL BP") {
            $result = [];

            // ✅ 1. Ambil data pembayaran utama
            $POlocal = $this->localPoPaymentBpModel->where('id', $payID)->first();

            if (!$POlocal) {
                throw new Exception("❌ Tidak ada data pembayaran Lokal BP dengan ID: {$payID}");
            }

            // ✅ 2. Ambil detail pembayaran
            $detailModel = new LocalPOPaymentDetailModel();
            $tandaTerimaFakturModel = new TandaTerimaFakturModel();

            $details = $detailModel
                ->where('local_po_payment_id', $payID)
                ->where('tipe', 'BP')
                ->where('deletedAt', null)
                ->findAll();

            if (empty($details)) {
                throw new Exception("❌ Tidak ada detail tanda terima untuk pembayaran ini.");
            }

            // ✅ 3. Ambil ID tipe transaksi (PEMBAYARAN)
            $dataMetadataTipeTransaksi = $this->MetadataModel
                ->asObject()
                ->where('name', 'tipe_transaksi')
                ->where('value', 'PEMBAYARAN')
                ->first();

            if (!$dataMetadataTipeTransaksi) {
                throw new Exception("❌ Metadata tipe_transaksi 'PEMBAYARAN' tidak ditemukan.");
            }

            $idTransaksi = $dataMetadataTipeTransaksi->id;

            // ✅ 4. Cek data penting
            $divisi = $POlocal['divisi_id'] ?? null;
            if (!$divisi) {
                throw new Exception("❌ Divisi belum diisi pada data pembayaran lokal.");
            }

            $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $POlocal['payment_date'])));

            // ✅ 5. Insert ke transaksi_jurnal
            $resultTransaksiJurnal = [
                'no_transaksi'      => $POlocal['payment_no'],
                'tanggal_transaksi' => $tanggal,
                'total_debit'       => $POlocal['amount'],
                'total_kredit'      => $POlocal['amount'],
                'metode_input'      => 'system',
                'type_transaksi'    => $idTransaksi,
                'no_bukti'          => $POlocal['payment_no'],
                'valas'             => 'IDR',
                'valas_id'          => '30',
                'exchange_rate'     => 1,
                'uraian_transaksi'  => $POlocal['supplier']
            ];

            $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

            if (!$id_transaksi_jurnal || !is_numeric($id_transaksi_jurnal)) {
                throw new Exception("❌ Insert transaksi jurnal gagal.");
            }

            // ✅ 6. Loop setiap detail tanda terima
            foreach ($details as $det) {
                $tandaTerimaID = $det['tanda_terima_faktur_id'];

                // Ambil data tanda terima untuk keterangan tambahan
                $ttf = $tandaTerimaFakturModel->where('id', $tandaTerimaID)->first();
                $keteranganTTF = $ttf['no_tanda_terima'] ?? $POlocal['keterangan'];

                // 🔹 Debit utama (kas)
                $result[] = [
                    'id_transaksi'   => $id_transaksi_jurnal,
                    'id_coa'         => $POlocal['akun_kas'],
                    'company_id'     => $POlocal['company_id'],
                    'divisi_id'      => $divisi,
                    'supplier_id'    => $POlocal['supplier_id'],
                    'tanggal_jurnal' => $tanggal,
                    'debit'          => $det['total'],
                    'kredit'         => 0,
                    'valas'          => '30',
                    'kurs'           => 1,
                    'keterangan'     => "Pembayaran: {$keteranganTTF}",
                    'id_inputer'     => session()->get("login")->user_id
                ];

                // 🔹 Kredit utama (akun selisih / hutang)
                $result[] = [
                    'id_transaksi'   => $id_transaksi_jurnal,
                    'id_coa'         => $POlocal['akun_selisih'],
                    'company_id'     => $POlocal['company_id'],
                    'divisi_id'      => $divisi,
                    'supplier_id'    => $POlocal['supplier_id'],
                    'tanggal_jurnal' => $tanggal,
                    'debit'          => 0,
                    'kredit'         => $det['total'],
                    'valas'          => '30',
                    'kurs'           => 1,
                    'keterangan'     => "Pembayaran: {$keteranganTTF}",
                    'id_inputer'     => session()->get("login")->user_id
                ];

                // 🔹 Tambahan (kalau ada)
                if (!empty($ttf['tambahan']) && $ttf['tambahan'] > 0) {
                    $result[] = [
                        'id_transaksi'   => $id_transaksi_jurnal,
                        'id_coa'         => $POlocal['akun_kas'],
                        'company_id'     => $POlocal['company_id'],
                        'divisi_id'      => $divisi,
                        'supplier_id'    => $POlocal['supplier_id'],
                        'tanggal_jurnal' => $tanggal,
                        'debit'          => $ttf['tambahan'],
                        'kredit'         => 0,
                        'valas'          => '30',
                        'kurs'           => 1,
                        'keterangan'     => "Tambahan dari {$keteranganTTF}",
                        'id_inputer'     => session()->get("login")->user_id
                    ];
                }

                // 🔹 Potongan (kalau ada)
                if (!empty($ttf['potongan']) && $ttf['potongan'] > 0) {
                    $result[] = [
                        'id_transaksi'   => $id_transaksi_jurnal,
                        'id_coa'         => $POlocal['akun_selisih'],
                        'company_id'     => $POlocal['company_id'],
                        'divisi_id'      => $divisi,
                        'supplier_id'    => $POlocal['supplier_id'],
                        'tanggal_jurnal' => $tanggal,
                        'debit'          => 0,
                        'kredit'         => $ttf['potongan'],
                        'valas'          => '30',
                        'kurs'           => 1,
                        'keterangan'     => "Potongan dari {$keteranganTTF}",
                        'id_inputer'     => session()->get("login")->user_id
                    ];
                }

                // 🔹 Pajak per tanda terima (kalau ada)
                $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();
                $pajakTandaTerimaPutih = $pajakTandaTerimaFakturModel
                    ->where("tanda_terima_faktur_id", $tandaTerimaID)
                    ->whereIn('tax_type', ['PPN Masukan', 'PPN Masukan 11%'])
                    ->where("deletedAt", null)
                    ->findAll();

                $pajakTandaTerimaMerah = $pajakTandaTerimaFakturModel
                    ->where("tanda_terima_faktur_id", $tandaTerimaID)
                    ->whereIn('tax_type', ['PPN Masukan', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
                    ->where("deletedAt", null)
                    ->findAll();

                if ($pajakTandaTerimaPutih) {
                    $taxesModel = new TaxModel();
                    foreach ($pajakTandaTerimaPutih as $ptt) {
                        $tax = $taxesModel->where('id', $ptt['tax_id'])->first();

                        $taxStatus = $ptt['tax_status'] ?? '-';
                        $taxNote   = $ptt['tax_note'] ?? '';

                        // Debit pajak
                        $result[] = [
                            'id_transaksi'   => $id_transaksi_jurnal,
                            'id_coa'         => $tax['akun_debit'],
                            'company_id'     => $POlocal['company_id'],
                            'divisi_id'      => $divisi,
                            'supplier_id'    => $POlocal['supplier_id'],
                            'tanggal_jurnal' => $tanggal,
                            'debit'          => $ptt['tax_amt'],
                            'kredit'         => 0,
                            'valas'          => '30',
                            'kurs'           => 1,
                            'keterangan'     => "Pajak {$keteranganTTF} | Status: {$taxStatus}" . (!empty($taxNote) ? " | Catatan: {$taxNote}" : ''),
                            'id_inputer'     => session()->get("login")->user_id
                        ];

                        // Kredit pajak
                        $result[] = [
                            'id_transaksi'   => $id_transaksi_jurnal,
                            'id_coa'         => $tax['akun_kredit'],
                            'company_id'     => $POlocal['company_id'],
                            'divisi_id'      => $divisi,
                            'supplier_id'    => $POlocal['supplier_id'],
                            'tanggal_jurnal' => $tanggal,
                            'debit'          => 0,
                            'kredit'         => $ptt['tax_amt'],
                            'valas'          => '30',
                            'kurs'           => 1,
                            'keterangan'     => "Pajak {$keteranganTTF} | Status: {$taxStatus}" . (!empty($taxNote) ? " | Catatan: {$taxNote}" : ''),
                            'id_inputer'     => session()->get("login")->user_id
                        ];
                    }
                }

                if ($pajakTandaTerimaMerah) {
                    $taxesModel = new TaxModel();
                    foreach ($pajakTandaTerimaMerah as $ptt) {
                        $tax = $taxesModel->where('id', $ptt['tax_id'])->first();

                        $taxStatus = $ptt['tax_status'] ?? '-';
                        $taxNote   = $ptt['tax_note'] ?? '';

                        // Debit pajak
                        $result[] = [
                            'id_transaksi'   => $id_transaksi_jurnal,
                            'id_coa'         => $tax['akun_debit'],
                            'company_id'     => $POlocal['company_id'],
                            'divisi_id'      => $divisi,
                            'supplier_id'    => $POlocal['supplier_id'],
                            'tanggal_jurnal' => $tanggal,
                            'debit'          => $ptt['tax_amt'],
                            'kredit'         => 0,
                            'valas'          => '30',
                            'kurs'           => 1,
                            'keterangan'     => "Pajak {$keteranganTTF} | Status: {$taxStatus}" . (!empty($taxNote) ? " | Catatan: {$taxNote}" : ''),
                            'id_inputer'     => session()->get("login")->user_id
                        ];

                        // Kredit pajak
                        $result[] = [
                            'id_transaksi'   => $id_transaksi_jurnal,
                            'id_coa'         => $tax['akun_kredit'],
                            'company_id'     => $POlocal['company_id'],
                            'divisi_id'      => $divisi,
                            'supplier_id'    => $POlocal['supplier_id'],
                            'tanggal_jurnal' => $tanggal,
                            'debit'          => 0,
                            'kredit'         => $ptt['tax_amt'],
                            'valas'          => '30',
                            'kurs'           => 1,
                            'keterangan'     => "Pajak {$keteranganTTF} | Status: {$taxStatus}" . (!empty($taxNote) ? " | Catatan: {$taxNote}" : ''),
                            'id_inputer'     => session()->get("login")->user_id
                        ];
                    }
                }
            }

            // ✅ 7. Insert batch ke jurnal umum
            if (empty($result)) {
                throw new Exception("❌ Tidak ada data jurnal yang terbentuk dari detail pembayaran ini.");
            }

            $insertResult = $this->jurnalUmumModel->insertJurnalBatch($result);

            if (!$insertResult) {
                throw new Exception("❌ Gagal insert ke jurnal umum.");
            }

            log_message('info', "✅ Jurnal berhasil dibuat untuk payment_no {$POlocal['payment_no']}");
            return $insertResult;
        } else if ($module == "IMPORT") {
            $POimport = $this->importPOPaymentModel->asObject()->where('deletedAt', null)->where('id', $payID)->first();
            if ($POimport) {
                $totalPO = 0;
                $dataSupplier = $this->supplierModel->getSupplierForJurnal($POimport->supplier_id);
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'PEMBAYARAN')->findAll();

                $rmImportPO = $this->rmImportPOModel->asObject()
                    ->where('rm_import_pos.id', $POimport->po_id)
                    ->where('rm_import_pos.deletedAt', null)
                    ->findAll();
                $rmImportPODetail = $this->rmImportPODetailModel->asObject()
                    ->where('rm_import_po_details.rm_import_po_id', $POimport->po_id)
                    ->where('rm_import_po_details.deletedAt', null)
                    ->findAll();
                foreach ($dataSupplier as $value) {
                    foreach ($dataAccountSupplier as $valueAccount) {
                        if ($value->id == $valueAccount->supplier_id) {
                            $UtangAP = $valueAccount->ap_id;
                            $UtangAR = $valueAccount->ar_id;
                        }
                    }
                    foreach ($dataAccountModule as $valueModule) {
                        if ($valueModule->type == strtoupper($POimport->po_type) && $valueModule->kategori == $module && $valueModule->module == "pembelian") {
                            $UtangAP = $valueModule->ap_id;
                            $UtangAR = $valueModule->ar_id;
                        }
                    }
                }
                // start inisialisasi kode transaksi
                foreach ($dataMetadataTipeTransaksi as $val) {
                    $kodeTransaksi = $val->description;
                    $idTransaksi = $val->id;
                }
                // end inisialisasi kode transaksi
                // input ke transaksi jurnal
                $no_transaksi_jurnal = $POimport->payment_no;
                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'total_debit' => ($POimport->payment_amt),
                    'total_kredit' => ($POimport->payment_amt),
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => $POimport->currency,
                    'valas_id' => $POimport->valas_id,
                    'exchange_rate' => $POimport->current_exchange_rate,
                );

                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                //untuk insert ke jurnal umum

                foreach ($rmImportPO as $value) {
                    $dataPO = str_replace(['[', ']', '"', "\\"], '', $value->po_no);
                }

                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' =>  $POimport->akun_kas,
                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'debit' => 0,
                    'kredit' => ($POimport->payment_amt),
                    'valas' => $POimport->valas_id,
                    'kurs' => $POimport->current_exchange_rate,
                    'company_id' => $POimport->company_id,
                    'divisi_id' => $POimport->divisi_id,
                    'supplier_id' => $POimport->supplier_id,
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' =>  $POimport->akun_selisih == 0 || $POimport->akun_selisih == NULL ? $UtangAR : $POimport->akun_selisih,
                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'debit' => ($POimport->payment_amt),
                    'kredit' => 0,
                    'valas' => $POimport->valas_id,
                    'kurs' => $POimport->current_exchange_rate,
                    'company_id' => $POimport->company_id,
                    'divisi_id' => $POimport->divisi_id,
                    'supplier_id' => $POimport->supplier_id,
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                return $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        } else if ($module == "LAIN-LAIN") {
            $otherPayment = $this->otherPaymentModel
                ->asObject()
                ->where('deletedAt', null)
                ->where('id', $payID)
                ->first();

            if ($otherPayment) {

                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()
                    ->where('name', 'tipe_transaksi')
                    ->where('value', 'PEMBAYARAN')
                    ->first();

                // start inisialisasi kode transaksi
                $idTransaksi = $dataMetadataTipeTransaksi->id;

                $detailPembayaran = $this->otherPaymentDetailModel
                    ->where('other_payment_id', $otherPayment->id)
                    ->where('deletedAt', null)
                    ->findAll();

                $totalNominal = array_reduce($detailPembayaran, function ($carry, $item) {
                    return $carry + floatval(str_replace([',', '.'], '', $item['nominal']));
                }, 0);

                $isFirstTransaction = true;

                // 1. Buat transaksi jurnal per detail
                $tanggal = "";
                $exchangeRate = 1;
                $valasId = 30;
                foreach ($detailPembayaran as $detail) {
                    $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $detail['tanggal_pembayaran'])));
                    $exchangeRate = $detail['kurs'];
                    $valasId = $detail['valas_id'];
                }

                $valas = $this->metadataModel->find($valasId);
                $no_transaksi_jurnal = $otherPayment->no_pembayaran;

                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => $tanggal,
                    'total_debit' => $totalNominal,
                    'total_kredit' => $totalNominal,
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas_id' => $valasId,
                    'valas' => $valas['value'],
                    'exchange_rate' => $exchangeRate,
                    'uraian_transaksi' => $otherPayment->bayar_ke,
                );

                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

                foreach ($detailPembayaran as $detail) {
                    $nominal = floatval(str_replace([',', '.'], '', $detail['nominal']));
                    $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $detail['tanggal_pembayaran'])));

                    // 2. Buat jurnal umum: kredit akun kas, debit akun selisih
                    $result = [];

                    if ($otherPayment->jenis_pembayaran == 'PUTIH') {
                        // PUTIH Logic: Parent Kredit, Child Debit
                        if ($isFirstTransaction) {
                            // Parent Entry (Kredit)
                            $result[] = [
                                'id_transaksi' => $id_transaksi_jurnal,
                                'id_coa' => $otherPayment->akun_selisih, // Parent COA for PUTIH
                                'tanggal_jurnal' => $tanggal,
                                'debit' => 0,
                                'kredit' => $totalNominal,
                                'valas' => $detail['valas_id'],
                                'kurs' => $detail['kurs'],
                                'company_id' => $otherPayment->company_id,
                                'divisi_id' => $otherPayment->divisi_id,
                                'keterangan' => $otherPayment->keterangan,
                                'id_inputer' => session()->get("login")->user_id
                            ];
                        }

                        // Child Entry (Debit)
                        $result[] = [
                            'id_transaksi' => $id_transaksi_jurnal,
                            'id_coa' => $detail['akun_selisih'], // Debit from detail
                            'tanggal_jurnal' => $tanggal,
                            'debit' => $nominal,
                            'kredit' => 0,
                            'valas' => $detail['valas_id'],
                            'kurs' => $detail['kurs'],
                            'company_id' => $otherPayment->company_id,
                            'divisi_id' => $otherPayment->divisi_id,
                            'keterangan' => $detail['keterangan'],
                            'id_inputer' => session()->get("login")->user_id
                        ];
                    } else {
                        // MERAH Logic: Parent Debit, Child Kredit
                        if ($isFirstTransaction) {
                            // Parent Entry (Debit)
                            $result[] = [
                                'id_transaksi' => $id_transaksi_jurnal,
                                'id_coa' => $otherPayment->akun_kas, // Parent COA for MERAH
                                'tanggal_jurnal' => $tanggal,
                                'debit' => $totalNominal,
                                'kredit' => 0,
                                'valas' => $detail['valas_id'],
                                'kurs' => $detail['kurs'],
                                'company_id' => $otherPayment->company_id,
                                'divisi_id' => $otherPayment->divisi_id,
                                'keterangan' => $otherPayment->keterangan,
                                'id_inputer' => session()->get("login")->user_id
                            ];
                        }

                        // Child Entry (Kredit)
                        $result[] = [
                            'id_transaksi' => $id_transaksi_jurnal,
                            'id_coa' => $detail['akun_kas'], // Kredit from detail (special case for MERAH)
                            'tanggal_jurnal' => $tanggal,
                            'debit' => 0,
                            'kredit' => $nominal,
                            'valas' => $detail['valas_id'],
                            'kurs' => $detail['kurs'],
                            'company_id' => $otherPayment->company_id,
                            'divisi_id' => $otherPayment->divisi_id,
                            'keterangan' => $detail['keterangan'],
                            'id_inputer' => session()->get("login")->user_id
                        ];
                    }

                    $this->jurnalUmumModel->insertJurnalBatch($result);
                    $isFirstTransaction = false;
                }
            }
        }
    }

    public function unpostDataPembayaran($payID, $module, $divisi)
    {
        try {
            // Cari transaksi berdasarkan module dan payID
            $transaction = null;
            $paymentNo = '';

            switch ($module) {
                case "LOKAL BB":
                    $payment = $this->localPOPaymentModel->asObject()->find($payID);
                    if ($payment) {
                        $paymentNo = $payment->payment_no;
                    }
                    break;

                case "LOKAL BP":
                    $payment = $this->localPoPaymentBpModel->where('id', $payID)->first();
                    if ($payment) {
                        $paymentNo = $payment['payment_no'];
                    }
                    break;

                case "IMPORT":
                    $payment = $this->importPOPaymentModel->asObject()->where('deletedAt', null)->where('id', $payID)->first();
                    if ($payment) {
                        $paymentNo = $payment->payment_no;
                    }
                    break;

                case "LAIN-LAIN":
                    $payment = $this->otherPaymentModel->asObject()->where('deletedAt', null)->where('id', $payID)->first();
                    if ($payment) {
                        $paymentNo = $payment->no_pembayaran;
                    }
                    break;
            }

            if (empty($paymentNo)) {
                throw new Exception("Data pembayaran tidak ditemukan");
            }

            // 1. Cari transaksi jurnal berdasarkan no_transaksi
            $transaksiJurnal = $this->transaksiJurnalModel
                ->where('no_transaksi', $paymentNo)
                ->where('deleted_at', null)
                ->first();

            if (!$transaksiJurnal) {
                throw new Exception("Transaksi jurnal tidak ditemukan");
            }

            // 2. Hapus jurnal umum terkait
            $this->jurnalUmumModel
                ->where('id_transaksi', $transaksiJurnal['id'])
                ->delete();

            // 3. Hapus transaksi jurnal
            $this->transaksiJurnalModel
                ->where('id', $transaksiJurnal['id'])
                ->delete();

            return [
                'status' => true,
                'message' => 'Data jurnal berhasil diunpost'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }


    public function insertDataPanjarPinjamanTransaction($payID)
    {
        try {

            // Get parent transaction data
            $transaction = $this->panjarPinjamanTransactionModel
                ->asObject()
                ->find($payID);

            if (!$transaction) {
                throw new \Exception("Transaction not found");
            }

            // Get all transaction details
            $panjarDetails = $this->PanjarSupplierModel
                ->where('transaction_id', $payID)
                ->where('deletedAt', null)
                ->where('company_id', $this->this_company_id)
                ->findAll();

            $pinjamanDetails = $this->PinjamanSupplierModel
                ->where('transaction_id', $payID)
                ->where('deletedAt', null)
                ->where('company_id', $this->this_company_id)
                ->findAll();

            // Validate at least one detail exists
            if (empty($panjarDetails) && empty($pinjamanDetails)) {
                throw new \Exception("No panjar or pinjaman details found for this transaction");
            }

            // Combine all details
            $allDetails = array_merge($panjarDetails, $pinjamanDetails);

            // Initialize metadata
            $metadataTipeTransaksi = $this->MetadataModel
                ->asObject()
                ->where('name', 'tipe_transaksi')
                ->whereIn('value', ['PANJAR', 'PINJAMAN', 'PANJAR & PINJAMAN'])
                ->findAll();

            $metadataMap = [];
            foreach ($metadataTipeTransaksi as $meta) {
                $metadataMap[$meta->value] = [
                    'id' => $meta->id,
                    'kode' => $meta->description
                ];
            }

            // Calculate totals with validation
            $tanggal = "";
            $totalPanjar = 0;
            $totalPinjaman = 0;

            foreach ($allDetails as $detail) {
                if (!isset($detail['payment_date'])) {
                    throw new \Exception("Payment date is missing in one of the details");
                }

                $tanggal = $detail['payment_date'];

                if (isset($detail['total_panjar'])) {
                    if (!is_numeric($detail['total_panjar'])) {
                        throw new \Exception("Invalid panjar amount");
                    }
                    $totalPanjar += $detail['total_panjar'];
                }

                if (isset($detail['total_pinjaman'])) {
                    if (!is_numeric($detail['total_pinjaman'])) {
                        throw new \Exception("Invalid pinjaman amount");
                    }
                    $totalPinjaman += $detail['total_pinjaman'];
                }
            }

            // Validate totals
            if ($totalPanjar <= 0 && $totalPinjaman <= 0) {
                throw new \Exception("Total amounts must be greater than 0");
            }

            // Generate journal number and determine transaction type
            $no_transaksi_jurnal = $transaction->no_transaction;
            $jenisTransaksi = '';

            if ($totalPanjar > 0 && $totalPinjaman > 0) {
                $jenisTransaksi = 'PANJAR & PINJAMAN';
            } elseif ($totalPanjar > 0) {
                $jenisTransaksi = 'PANJAR';
            } elseif ($totalPinjaman > 0) {
                $jenisTransaksi = 'PINJAMAN';
            } else {
                throw new \Exception("Cannot determine transaction type");
            }

            // Create journal header
            $jurnalHeader = [
                'no_transaksi' => $no_transaksi_jurnal,
                'tanggal_transaksi' => date('Y-m-d', strtotime($tanggal)),
                'total_debit' => $totalPanjar + $totalPinjaman,
                'total_kredit' => $totalPanjar + $totalPinjaman,
                'metode_input' => 'system',
                'type_transaksi' => $metadataMap[$jenisTransaksi]['id'],
                'no_bukti' => $transaction->no_transaction,
                'valas_id' => '30',
                'valas' => "IDR",
                'uraian_transaksi' => $transaction->keterangan,
                'exchange_rate' => 1,
                'createdAt' => date('Y-m-d H:i:s'),
                'createdBy' => session()->get("login")->user_id
            ];

            // Insert journal header and get ID
            $id_transaksi_jurnal = $this->transaksiJurnalModel
                ->insertTransaksiJurnal($jurnalHeader);

            $jurnalEntries = [];

            // Process each detail with validation
            foreach ($allDetails as $detail) {
                if (!isset($detail['akun_kas']) || empty($detail['akun_kas'])) {
                    throw new \Exception("Cash account is missing in one of the details");
                }

                if (!isset($detail['akun_selisih']) || empty($detail['akun_selisih'])) {
                    throw new \Exception("Difference account is missing in one of the details");
                }

                $nominal = isset($detail['total_pinjaman']) ? $detail['total_pinjaman'] : $detail['total_panjar'];

                if (!is_numeric($nominal) || $nominal <= 0) {
                    throw new \Exception("Invalid amount in one of the details");
                }

                // Prepare journal entries
                $jurnalEntries[] = [
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' => $detail['akun_kas'],
                    'company_id' => $transaction->company_id,
                    'divisi_id' => $transaction->divisi_id,
                    'tanggal_jurnal' => $transaction->tanggal,
                    'debit' => $nominal,
                    'kredit' => 0,
                    'valas' => '30',
                    'kurs' => 1,
                    'keterangan' => $detail['keterangan'] ?? '',
                    'supplier_id' => $detail['supplier_id'] ?? null,
                    'id_inputer' => session()->get("login")->user_id,
                    'createdAt' => date('Y-m-d H:i:s')
                ];

                $jurnalEntries[] = [
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' => $detail['akun_selisih'],
                    'company_id' => $transaction->company_id,
                    'divisi_id' => $transaction->divisi_id,
                    'tanggal_jurnal' => $transaction->tanggal,
                    'debit' => 0,
                    'kredit' => $nominal,
                    'valas' => '30',
                    'kurs' => 1,
                    'keterangan' => $detail['keterangan'] ?? '',
                    'supplier_id' => $detail['supplier_id'] ?? null,
                    'id_inputer' => session()->get("login")->user_id,
                    'createdAt' => date('Y-m-d H:i:s')
                ];
            }

            // Insert journal entries if any
            if (!empty($jurnalEntries)) {
                $this->jurnalUmumModel->insertJurnalBatch($jurnalEntries);
            } else {
                throw new \Exception("No valid journal entries to process");
            }

            return [
                'status' => true,
                'message' => 'Journal entries created successfully'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function unpostDataPanjarPinjamanTransaction($payID)
    {
        try {
            // Get transaction data
            $transaction = $this->panjarPinjamanTransactionModel
                ->asObject()
                ->find($payID);

            if (!$transaction) {
                throw new \Exception("Transaction not found");
            }

            // 1. Find journal transaction by transaction number
            $transaksiJurnal = $this->transaksiJurnalModel
                ->where('no_bukti', $transaction->no_transaction)
                ->first();

            if (!$transaksiJurnal) {
                // If journal doesn't exist, consider it already unposted
                return [
                    'status' => true,
                    'message' => 'No journal entries found to unpost'
                ];
            }

            // 2. Delete all related journal entries
            $deletedEntries = $this->jurnalUmumModel
                ->where('id_transaksi', $transaksiJurnal['id'])
                ->delete();

            // 3. Delete journal header
            $deletedHeader = $this->transaksiJurnalModel
                ->where('id', $transaksiJurnal['id'])
                ->delete();

            if ($deletedHeader === false || $deletedEntries === false) {
                throw new \Exception("Failed to delete journal entries");
            }

            return [
                'status' => true,
                'message' => 'Journal transaction successfully unposted'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function insertDataPanjar($payID, $module)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";
        $dataPO = "";
        $divisi = $this->EmployeeModel->select('division_id')->asObject()->where('id', session()->get("login")->employee_id)->first();

        if ($module == "PANJAR") {
            $result = array();
            $dataPanjar = $this->PanjarSupplierModel->asObject()->find($payID);


            if ($dataPanjar) {
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'PANJAR')->findAll();

                // start inisialisasi kode transaksi
                foreach ($dataMetadataTipeTransaksi as $val) {
                    $kodeTransaksi = $val->description;
                    $idTransaksi = $val->id;
                }
                // end inisialisasi kode transaksi

                // input ke transaksi jurnal
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);

                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => Carbon::parse($dataPanjar->createdAt)->format('Y-m-d'),
                    'total_debit' => $dataPanjar->total_panjar,
                    'total_kredit' => $dataPanjar->total_panjar,
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => '30',
                    'exchange_rate' => 1,
                );


                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

                $result[] = array(
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $dataPanjar->akun_kas == 0 || $dataPanjar->akun_kas == NULL ? $UtangAR : $dataPanjar->akun_kas,
                    'company_id'        => $dataPanjar->company_id,
                    'divisi_id'            => $divisi->division_id,
                    'tanggal_jurnal' => Carbon::parse($dataPanjar->createdAt)->format('Y-m-d'),
                    'debit'             => $dataPanjar->total_panjar,
                    'kredit'            => 0,
                    'valas'             => '20',
                    'kurs'              => 1,
                    'keterangan' => "Pembuatan Panjar " . $dataPanjar->no_panjar . " " . $dataPanjar->keterangan,
                    'id_inputer'        => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $dataPanjar->akun_selisih,
                    'company_id'        => $dataPanjar->company_id,
                    'divisi_id'            => $divisi->division_id,
                    'tanggal_jurnal'    => Carbon::parse($dataPanjar->createdAt)->format('Y-m-d'),
                    'debit'             => 0,
                    'kredit'            => $dataPanjar->total_panjar,
                    'valas'             => '20',
                    'kurs'              => 1,
                    'keterangan' => "Pembuatan Panjar " . $dataPanjar->no_panjar . " " . $dataPanjar->keterangan,
                    'id_inputer'        => session()->get("login")->user_id
                );

                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        }
    }


    public function insertDataPinjaman($payID, $module)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";
        $dataPO = "";

        if ($module == "PINJAMAN") {
            $result = array();
            $dataPinjaman = $this->PinjamanSupplierModel->asObject()->find($payID);


            if ($dataPinjaman) {
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'PANJAR')->findAll();

                // start inisialisasi kode transaksi
                foreach ($dataMetadataTipeTransaksi as $val) {
                    $kodeTransaksi = $val->description;
                    $idTransaksi = $val->id;
                }
                // end inisialisasi kode transaksi

                // input ke transaksi jurnal
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);

                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => Carbon::parse($dataPinjaman->createdAt)->format('Y-m-d'),
                    'total_debit' => $dataPinjaman->total_pinjaman,
                    'total_kredit' => $dataPinjaman->total_pinjaman,
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => '30',
                    'exchange_rate' => 1,
                );


                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

                $result[] = array(
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $dataPinjaman->akun_kas == 0 || $dataPinjaman->akun_kas == NULL ? $UtangAR : $dataPinjaman->akun_kas,
                    'company_id'        => $dataPinjaman->company_id,
                    // 'divisi_id'            => $this->divisi,
                    'tanggal_jurnal' => Carbon::parse($dataPinjaman->createdAt)->format('Y-m-d'),
                    'debit'             => $dataPinjaman->total_pinjaman,
                    'kredit'            => 0,
                    'valas'             => '20',
                    'kurs'              => 1,
                    'keterangan'        => "Pembuatan Pinjaman" . $dataPinjaman->no_panjar,
                    'id_inputer'        => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $dataPinjaman->akun_selisih,
                    'company_id'        => $dataPinjaman->company_id,
                    // 'divisi_id'            => $this->divisi,
                    'tanggal_jurnal'    => Carbon::parse($dataPinjaman->createdAt)->format('Y-m-d'),
                    'debit'             => 0,
                    'kredit'            => $dataPinjaman->total_pinjaman,
                    'valas'             => '20',
                    'kurs'              => 1,
                    'keterangan'        => "Pembuatan Pinjaman" . $dataPinjaman->no_panjar,
                    'id_inputer'        => session()->get("login")->user_id
                );

                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        }
    }


    public function inserDataPembayaranPanjar($payID, $module, $divisi)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";
        $dataPO = "";

        if ($module == "PANJAR") {
            $result = array();
            $dataPanjar = $this->localPOPaymentPanjarModel->where('local_po_payment_panjar.id', $payID)
                ->join('panjar_supplier', 'panjar_supplier.id = local_po_payment_panjar.panjar_id', 'left')
                ->asObject()
                ->select('local_po_payment_panjar.*, panjar_supplier.no_panjar')
                ->first();


            if ($dataPanjar) {
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'PANJAR')->findAll();

                // start inisialisasi kode transaksi
                foreach ($dataMetadataTipeTransaksi as $val) {
                    $kodeTransaksi = $val->description;
                    $idTransaksi = $val->id;
                }
                // end inisialisasi kode transaksi

                // input ke transaksi jurnal
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);

                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataPanjar->createdAt))),
                    'total_debit' => $dataPanjar->bayar_panjar,
                    'total_kredit' => $dataPanjar->bayar_panjar,
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => '30',
                    'exchange_rate' => 1,
                );


                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

                $result[] = array(
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $dataPanjar->akun_kas == 0 || $dataPanjar->akun_kas == NULL ? $UtangAR : $dataPanjar->akun_kas,
                    'company_id'            => $dataPanjar->company_id,
                    // 'divisi_id'            => $this->divisi,
                    'divisi_id'         => $divisi,
                    'tanggal_jurnal' => Carbon::parse($dataPanjar->createdAt)->format('Y-m-d'),
                    'debit'             => $dataPanjar->bayar_panjar,
                    'kredit'            => 0,
                    'valas'             => '20',
                    'kurs'              => 1,
                    'keterangan'        => "Pembayaran Panjar " . $dataPanjar->no_panjar,
                    'id_inputer'        => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $dataPanjar->akun_selisih,
                    'company_id'            => $dataPanjar->company_id,
                    // 'divisi_id'            => $this->divisi,
                    'divisi_id'         => $divisi,
                    'tanggal_jurnal' => Carbon::parse($dataPanjar->createdAt)->format('Y-m-d'),
                    'debit'             => 0,
                    'kredit'            => $dataPanjar->bayar_panjar,
                    'valas'             => '20',
                    'kurs'              => 1,
                    'keterangan'        => "Pembayaran Panjar " . $dataPanjar->no_panjar,
                    'id_inputer'        => session()->get("login")->user_id
                );

                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        }
    }


    public function unpostDataPembayaranPanjar($payID, $module, $divisi)
    {
        try {
            // Validasi module harus PANJAR
            if ($module != "PANJAR") {
                throw new Exception("Module harus PANJAR");
            }

            // Cari data panjar berdasarkan ID
            $dataPanjar = $this->localPOPaymentPanjarModel
                ->where('local_po_payment_panjar.id', $payID)
                ->join('panjar_supplier', 'panjar_supplier.id = local_po_payment_panjar.panjar_id', 'left')
                ->asObject()
                ->select('local_po_payment_panjar.*, panjar_supplier.no_panjar')
                ->first();

            if (!$dataPanjar) {
                throw new Exception("Data panjar tidak ditemukan");
            }

            // 1. Cari transaksi jurnal berdasarkan keterangan yang unik
            $transaksiJurnal = $this->transaksiJurnalModel
                ->where('no_bukti', $dataPanjar->no_panjar)
                ->orWhere('keterangan', 'LIKE', '%Pembayaran Panjar ' . $dataPanjar->no_panjar . '%')
                ->first();

            if (!$transaksiJurnal) {
                throw new Exception("Transaksi jurnal untuk panjar ini tidak ditemukan");
            }

            // 2. Hapus semua jurnal umum terkait
            $this->jurnalUmumModel
                ->where('id_transaksi', $transaksiJurnal['id'])
                ->delete();

            // 3. Hapus transaksi jurnal
            $this->transaksiJurnalModel
                ->where('id', $transaksiJurnal['id'])
                ->delete();

            return [
                'status' => true,
                'message' => 'Data jurnal panjar berhasil diunpost'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function inserDataPembayaranPinjaman($payID, $module, $divisi)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";
        $dataPO = "";

        if ($module == "PINJAMAN") {
            $result = array();
            $payPinjaman = $this->localPOPaymentPinjamanModel->where('local_po_payment_pinjaman.id', $payID)
                ->join('pinjaman_supplier', 'pinjaman_supplier.id = local_po_payment_pinjaman.pinjaman_id', 'left')
                ->asObject()
                ->select('local_po_payment_pinjaman.*, pinjaman_supplier.no_pinjaman')
                ->first();

            if ($payPinjaman) {
                $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'PINJAMAN')->findAll();

                // start inisialisasi kode transaksi
                foreach ($dataMetadataTipeTransaksi as $val) {
                    $kodeTransaksi = $val->description;
                    $idTransaksi = $val->id;
                }
                // end inisialisasi kode transaksi

                // input ke transaksi jurnal
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);

                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $payPinjaman->createdAt))),
                    'total_debit' => $payPinjaman->bayar_pinjaman,
                    'total_kredit' => $payPinjaman->bayar_pinjaman,
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => '30',
                    'exchange_rate' => 1,
                );


                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);



                $result[] = array(
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $payPinjaman->akun_kas == 0 || $payPinjaman->akun_kas == NULL ? $UtangAR : $payPinjaman->akun_kas,
                    'company_id'            => $payPinjaman->company_id,
                    'tanggal_jurnal' => Carbon::parse($payPinjaman->createdAt)->format('Y-m-d'),
                    'debit'             => $payPinjaman->bayar_pinjaman,
                    'kredit'            => 0,
                    'valas'             => '20',
                    'kurs'              => 1,
                    'divisi_id'         => $divisi,
                    'keterangan'        => "Pembayaran Pinjaman " . $payPinjaman->no_pinjaman,
                    'id_inputer'        => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi'      => $id_transaksi_jurnal,
                    'id_coa'            => $payPinjaman->akun_selisih,
                    'company_id'            => $payPinjaman->company_id,
                    'tanggal_jurnal' => Carbon::parse($payPinjaman->createdAt)->format('Y-m-d'),
                    'debit'             => 0,
                    'kredit'            => $payPinjaman->bayar_pinjaman,
                    'valas'             => '20',
                    'kurs'              => 1,
                    'divisi_id'         => $divisi,
                    'keterangan'        => "Pembayaran Pinjaman" . $payPinjaman->no_pinjaman,
                    'id_inputer'        => session()->get("login")->user_id
                );

                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        }
    }

    public function unpostDataPembayaranPinjaman($payID, $module, $divisi)
    {
        try {
            // Validasi module harus PINJAMAN
            if ($module != "PINJAMAN") {
                throw new Exception("Module harus PINJAMAN");
            }

            // Cari data pinjaman berdasarkan ID
            $dataPinjaman = $this->localPOPaymentPinjamanModel
                ->where('local_po_payment_pinjaman.id', $payID)
                ->join('pinjaman_supplier', 'pinjaman_supplier.id = local_po_payment_pinjaman.pinjaman_id', 'left')
                ->asObject()
                ->select('local_po_payment_pinjaman.*, pinjaman_supplier.no_pinjaman')
                ->first();

            if (!$dataPinjaman) {
                throw new Exception("Data pinjaman tidak ditemukan");
            }

            // 1. Cari transaksi jurnal berdasarkan no_pinjaman atau keterangan
            $transaksiJurnal = $this->transaksiJurnalModel
                ->where('no_bukti', $dataPinjaman->no_pinjaman)
                ->orWhere('keterangan', 'LIKE', '%Pembayaran Pinjaman' . $dataPinjaman->no_pinjaman . '%')
                ->first();

            if (!$transaksiJurnal) {
                throw new Exception("Transaksi jurnal untuk pinjaman ini tidak ditemukan");
            }

            // 2. Hapus semua jurnal umum terkait
            $this->jurnalUmumModel
                ->where('id_transaksi', $transaksiJurnal['id'])
                ->delete();

            // 3. Hapus transaksi jurnal
            $this->transaksiJurnalModel
                ->where('id', $transaksiJurnal['id'])
                ->delete();

            return [
                'status' => true,
                'message' => 'Data jurnal pinjaman berhasil diunpost'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function TransaksiJurnalStockBarang($companyID, $divisiID, $barang1ID, $barang2ID, $typeBarang, $noPO, $operasi)
    {
        $kategori = "";
        $valas = "";
        $kurs = "";
        $barangAPFound = false;

        $conditionAccountBarang = [
            'company_id' => $companyID,
            'divisi_id' => $divisiID,
            'barang_master_id' => $barang1ID,
        ];

        // definisi data
        $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal($conditionAccountBarang);
        $dataMetadataTipeTransaksi = $this->MetadataModel->asObject()->where('name', 'tipe_transaksi')->where('value', 'MUTASI')->findAll();
        $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
        $dataAccountDivisi = $this->accountDivisisModel->getAccountDivisiForJurnal();
        $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
        $dataMetadataValutaIDR = $this->MetadataModel->asObject()->where('name', 'Valuta')->where('value', 'IDR')->first();

        if ($typeBarang == "bahan_penolong") {
            $dataPO = $this->aMPurchaseOrderModel->getPOByNoPO($noPO, $this->this_company_id, $barang1ID, $barang2ID);
            if ($dataPO['dataPO'] == null) {
                return;
            }
            $kategori = $dataPO["dataPO"]["po_type"];
            $kursData = $this->kursModel->getByMetaId($dataPO["dataPO"]["currency"], $dataPO["dataPO"]["po_date"]);
            if ($kursData) {
                $kurs = $kursData->nilai_kurs;
            } else {
                $kurs = 1;
            }
            $valas = $dataPO["dataPO"]["currency"];
            $valasText = $this->MetadataModel->asObject()->find($dataPO["dataPO"]["currency"]);
        } else if ($typeBarang == "bahan_baku") {
            $dataPO = $this->rMPurchaseOrderModel->getPOByNoPO($noPO, $this->this_company_id, $barang1ID, $barang2ID);
            if ($dataPO['dataPO'] == null) {
                return;
            }
            if ($dataPO) {
                $kategori = "LOKAL";
                $kurs = 1;
                $valas = $dataMetadataValutaIDR->id;
                $valasText = "IDR";
            } else {
                $dataPO = $this->rmImportPOModel->getPOByNoPO($noPO, $this->this_company_id, $barang1ID, $barang2ID);
                $kursData = $this->kursModel->getByMetaId($dataPO["dataPO"]["currency"], $dataPO["dataPO"]["po_date"]);
                if ($kursData) {
                    $kurs = $kursData->nilai_kurs;
                } else {
                    $kurs = 1;
                }
                $kategori = "IMPORT";
                $valas = $dataPO["dataPO"]["currency"];
                $valasText = $this->MetadataModel->asObject()->find($dataPO["dataPO"]["currency"]);
            }
        } else {
            $kategori = "LOKAL";
            $kurs = 1;
            $valas = $dataMetadataValutaIDR->id;
            $valasText = "IDR";
        }

        foreach ($dataMetadataTipeTransaksi as $val) {
            $kodeTransaksi = $val->description;
            $idTransaksi = $val->id;
        }

        if ($typeBarang != "bahan_jadi" || $typeBarang != "bahan_setengah_jadi") {
            if ($dataAccountSupplier) {
                foreach ($dataAccountSupplier as $valueAccount) {
                    if ($dataPO["dataPO"]["supplier_id"] == $valueAccount->supplier_id) {
                        $UtangAP = $valueAccount->ap_id;
                        $UtangAR = $valueAccount->ar_id;
                    }
                }
            }

            foreach ($dataAccountModule as $valueModule) {
                if ($valueModule->type == strtoupper(str_replace("_", " ", $typeBarang)) && $valueModule->kategori == strtoupper($kategori) && $valueModule->module == "pembelian") {
                    $UtangAP = $valueModule->ap_id;
                    $UtangAR = $valueModule->ar_id;
                }
            }

            foreach ($dataAccountBarang as $value) {
                if ($barang1ID == $value->barang_master_id && $divisiID == $value->divisi_id && $companyID == $value->company_id) {
                    $barangAP = $value->ap_id;
                    $barangAR = $value->ar_id;
                    $barangAPFound = true;
                }
            }
        } else {
            foreach ($dataAccountDivisi as $valueAccount) {
                if ($divisiID == $valueAccount->divisis_id) {
                    $UtangAP = $valueAccount->ap_id;
                    $UtangAR = $valueAccount->ar_id;
                }
            }

            foreach ($dataAccountBarang as $value) {
                if ($barang1ID == $value->barang_master_id && $divisiID == $value->divisi_id && $companyID == $value->company_id) {
                    $barangAP = $value->ap_id;
                    $barangAR = $value->ar_id;
                    $barangAPFound = true;
                }
            }
        }

        if (!$barangAPFound) {
            return response()->setJSON([
                "status" => false,
                "message" => "Barang Tidak Memiliki Akun COA",
                'token' => csrf_hash()
            ]);
        }

        $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);

        $resultTransaksiJurnal = array(
            'no_transaksi' => $no_transaksi_jurnal,
            'tanggal_transaksi' => date('Y-m-d'),
            'total_debit' => $dataPO['hargaTerakhirNumber'],
            'total_kredit' => $dataPO['hargaTerakhirNumber'],
            'metode_input' => 'system',
            'type_transaksi' => $idTransaksi,
            'no_bukti' => $no_transaksi_jurnal,
            'valas' => $valasText,
            'exchange_rate' => $kurs,
        );

        $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

        if ($operasi == "IN") {
            $result[] = array(
                'id_transaksi' => $id_transaksi_jurnal,
                'id_coa' =>  $barangAP,
                'company_id' =>  $companyID,
                'divisi_id' =>  $divisiID,
                'tanggal_jurnal' => date('Y-m-d'),
                'debit' => 0,
                'kredit' => ($dataPO['hargaTerakhirNumber']),
                'valas' => $valas,
                'kurs' => $kurs,
                'keterangan' => "Pindah Saldo ",
                'id_inputer' => session()->get("login")->user_id
            );

            $result[] = array(
                'id_transaksi' => $id_transaksi_jurnal,
                'id_coa' =>  $UtangAR,
                'company_id' =>  $companyID,
                'divisi_id' =>  $divisiID,
                'tanggal_jurnal' => date('Y-m-d'),
                'debit' => ($dataPO['hargaTerakhirNumber']),
                'kredit' => 0,
                'valas' => $valas,
                'kurs' => $kurs,
                'keterangan' => "Pindah Saldo ",
                'id_inputer' => session()->get("login")->user_id
            );
        } else {
            $result[] = array(
                'id_transaksi' => $id_transaksi_jurnal,
                'id_coa' =>  $barangAP,
                'company_id' =>  $companyID,
                'divisi_id' =>  $divisiID,
                'tanggal_jurnal' => date('Y-m-d'),
                'debit' => ($dataPO['hargaTerakhirNumber']),
                'kredit' => 0,
                'valas' => $valas,
                'kurs' => $kurs,
                'keterangan' => "Pindah Saldo ",
                'id_inputer' => session()->get("login")->user_id
            );

            $result[] = array(
                'id_transaksi' => $id_transaksi_jurnal,
                'id_coa' =>  $UtangAR,
                'company_id' =>  $companyID,
                'divisi_id' =>  $divisiID,
                'tanggal_jurnal' => date('Y-m-d'),
                'debit' => 0,
                'kredit' => ($dataPO['hargaTerakhirNumber']),
                'valas' => $valas,
                'kurs' => $kurs,
                'keterangan' => "Pindah Saldo ",
                'id_inputer' => session()->get("login")->user_id
            );
        }
        $this->jurnalUmumModel->insertJurnalBatch($result);
    }


    public function insertDataPenjualan($poID, $typePenjualan = ['LAIN', 'LOKAL', 'INTERNASIONAL'])
    {
        $kasDepartment = "";
        $piutangDepartment = "";
        $gajiDepartment = "";
        $hppDepartment = "";
        // $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBP->supplier_id);
        // $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
        // $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
        // $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();

        if ($typePenjualan == 'LAIN') {
            $salesOrderLain = $this->salesOrderLainModel->find($poID);
            if ($salesOrderLain) {
                $dataDepartment = $this->divisionModel->getAccountKasForJurnal($salesOrderLain['divisi_id'], $salesOrderLain['company_id']);
                $salesOrderLainDetail = $this->salesOrderLainDetailModel->where('sales_order_lain_id', $poID)->findAll();

                foreach ($dataDepartment as $value) {
                    $kasDepartment = $value->coa_kas_id;
                    $piutangDepartment = $value->coa_piutang_id;
                    $gajiDepartment = $value->coa_gaji_id;
                    $hppDepartment = $value->coa_hpp_id;
                }

                foreach ($salesOrderLainDetail as $value) {
                    # code...
                }
            }
        } elseif ($typePenjualan == "LOKAL") {
            # code...
        } elseif ($typePenjualan == "INTERNASIONAL") {
            # code...
        }
    }

    public function insertDataPembayaranInvoice($pembayaranInvoiceId)
    {
        $pembayaranInvoice = $this->pembayaranInvoiceModel->where('id', $pembayaranInvoiceId)->first();
        if ($pembayaranInvoice == null) {
            return false;
        }
        $pembayaranInvoiceDetail = $this->pembayaranInvoiceDetailModel->where('pembayaran_invoice_id', $pembayaranInvoice['id'])->findAll();
        $metaDataTypeTransaksi = $this->MetadataModel->where('name', 'tipe_transaksi')->where('value', 'PEMBAYARAN')->first();
        $metaDataValuta = $this->MetadataModel->where('id', $pembayaranInvoice['valas_id'])->first();

        try {
            $db = Database::connect();
            $db->transBegin();

            $resultTransaksiJurnal = array(
                'no_transaksi' => $pembayaranInvoice['no_pembayaran'],
                'tanggal_transaksi' => $pembayaranInvoice['tanggal'],
                'total_debit' => $pembayaranInvoice['total_bayar'],
                'total_kredit' =>  $pembayaranInvoice['total_bayar'],
                'metode_input' => 'system',
                'type_transaksi' => 1407,
                'valas' => $metaDataValuta['value'],
                'uraian_transaksi' => $pembayaranInvoice['pembayaran_dari']
            );

            $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
            $hargaTotalBarangInvoice = 0;
            $result = array();
            $totalDebit = 0;
            $totalKredit = 0;

            if ($pembayaranInvoiceDetail) {
                foreach ($pembayaranInvoiceDetail as $p) {
                    if ($p['sales_order_invoice_id'] == null) {
                        // PASTI LAIN LAIN
                        // AKUN KAS
                        if ($p['akun_kas_lain'] != null) {
                            $result[] = array(
                                'id_transaksi' => $id_transaksi_jurnal,
                                'company_id' => $this->this_company_id,
                                'divisi_id' => $pembayaranInvoice['divisi_id'],
                                'id_coa' =>  $p['akun_kas_lain'],
                                'tanggal_jurnal' => $resultTransaksiJurnal['tanggal_transaksi'],
                                'debit' =>  $p['harga_total'],
                                'kredit' => 0,
                                'valas' => $metaDataValuta['id'],
                                'kurs' => $pembayaranInvoice['kurs_sekarang'],
                                'keterangan' =>  $p['nama_barang'],
                                'id_inputer' => session()->get("login")->user_id
                            );
                            $totalDebit += $p['harga_total'];
                        }

                        if ($p['akun_selisih_lain'] != null) {
                            $result[] = array(
                                'id_transaksi' => $id_transaksi_jurnal,
                                'company_id' => $this->this_company_id,
                                'divisi_id' => $pembayaranInvoice['divisi_id'],
                                'id_coa' =>  $p['akun_selisih_lain'],
                                'tanggal_jurnal' => $resultTransaksiJurnal['tanggal_transaksi'],
                                'debit' => 0,
                                'kredit' =>  $p['harga_total'],
                                'valas' => $metaDataValuta['id'],
                                'kurs' => $pembayaranInvoice['kurs_sekarang'],
                                'keterangan' =>  $p['nama_barang'],
                                'id_inputer' => session()->get("login")->user_id
                            );
                            $totalKredit += $p['harga_total'];
                        }
                    } else {
                        // PASTI BARANG
                        $hargaTotalBarangInvoice += $p['harga_total'];
                    }
                }
            }

            // AKUN KAS
            if ($pembayaranInvoice['akun_kas'] != null) {
                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'company_id' => $this->this_company_id,
                    'divisi_id' => $pembayaranInvoice['divisi_id'],
                    'id_coa' =>  $pembayaranInvoice['akun_kas'],
                    'tanggal_jurnal' => $resultTransaksiJurnal['tanggal_transaksi'],
                    'debit' => $pembayaranInvoice['total_bayar'],
                    'kredit' => 0,
                    'valas' => $metaDataValuta['id'],
                    'kurs' => 1,
                    'keterangan' => $pembayaranInvoice['keterangan'],
                    'id_inputer' => session()->get("login")->user_id
                );
            }

            // AKUN SELISIH
            if ($pembayaranInvoice['akun_selisih'] != null) {
                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'company_id' => $this->this_company_id,
                    'divisi_id' => $pembayaranInvoice['divisi_id'],
                    'id_coa' =>  $pembayaranInvoice['akun_selisih'],
                    'tanggal_jurnal' => $resultTransaksiJurnal['tanggal_transaksi'],
                    'debit' => 0,
                    'kredit' => $pembayaranInvoice['total_bayar'],
                    'valas' => $metaDataValuta['id'],
                    'kurs' => 1,
                    'keterangan' => $pembayaranInvoice['keterangan'],
                    'id_inputer' => session()->get("login")->user_id
                );
            }

            // Update di Jurnal
            $this->transaksiJurnalModel->update($id_transaksi_jurnal, [
                'total_debit' => $pembayaranInvoice['total_bayar'] + $totalDebit,
                'total_kredit' =>  $pembayaranInvoice['total_bayar'] + $totalKredit,
            ]);

            if (count($result) > 0) {
                $this->jurnalUmumModel->insertJurnalBatch($result);
                $db->transCommit();
                return true;
            } else {
                $db->transRollback();
                return false;
            }
        } catch (Exception $e) {
            $db->transRollback();
            \var_dump($e->getMessage(), $e->getLine());
            return false;
        }
    }

    public function unpostDataPembayaranInvoice($pembayaranInvoiceId)
    {
        $pembayaranInvoice = $this->pembayaranInvoiceModel->where('id', $pembayaranInvoiceId)->first();
        if ($pembayaranInvoice == null) {
            return false;
        }

        try {
            $db = Database::connect();
            $db->transBegin();

            // Ambil data transaksi jurnal berdasarkan no_pembayaran
            $transaksiJurnal = $this->transaksiJurnalModel
                ->where('no_transaksi', $pembayaranInvoice['no_pembayaran'])
                ->first();

            if ($transaksiJurnal) {
                $idTransaksiJurnal = $transaksiJurnal['id'];

                // Hapus jurnal umum terkait transaksi ini
                $this->jurnalUmumModel
                    ->where('id_transaksi', $idTransaksiJurnal)
                    ->delete();

                // Hapus transaksi jurnalnya
                $this->transaksiJurnalModel
                    ->where('id', $idTransaksiJurnal)
                    ->delete();
            }

            // Update status posting pembayaran_invoice jadi 0 (belum posting)
            $this->pembayaranInvoiceModel
                ->where('id', $pembayaranInvoiceId)
                ->set(['status_posting' => 0])
                ->update();

            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            \var_dump('Error unpost:', $e->getMessage(), 'Line:', $e->getLine());
            return false;
        }
    }



    public function insertDataPembayaranInvoiceInternasional($pembayaranInvoiceId)
    {
        $pembayaranInvoice = $this->pembayaranInvoiceModel->where('id', $pembayaranInvoiceId)->first();
        if ($pembayaranInvoice == null) {
            return false;
        }

        $metaDataTypeTransaksi = $this->MetadataModel
            ->where('name', 'tipe_transaksi')
            ->where('value', 'PEMBAYARAN')
            ->first();

        $metaDataValuta = $this->MetadataModel->where('id', $pembayaranInvoice['valas_id'])->first();

        try {
            $db = Database::connect();
            $db->transBegin();

            // Data header transaksi jurnal
            $resultTransaksiJurnal = array(
                'no_transaksi'      => $pembayaranInvoice['no_pembayaran'],
                'tanggal_transaksi' => $pembayaranInvoice['tanggal'],
                'total_debit'       => $pembayaranInvoice['total_bayar'],
                'total_kredit'      => $pembayaranInvoice['total_bayar'],
                'metode_input'      => 'system',
                'type_transaksi'    => 1407,
                'valas'             => $metaDataValuta['value'],
                'uraian_transaksi'  => $pembayaranInvoice['pembayaran_dari']
            );

            $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

            $result = array();

            // --- Debit : AKUN KAS ---
            if ($pembayaranInvoice['akun_kas'] != null) {
                $result[] = array(
                    'id_transaksi'    => $id_transaksi_jurnal,
                    'company_id'      => $this->this_company_id,
                    'divisi_id'       => $pembayaranInvoice['divisi_id'],
                    'id_coa'          => $pembayaranInvoice['akun_kas'],
                    'tanggal_jurnal'  => $resultTransaksiJurnal['tanggal_transaksi'],
                    'debit'           => $pembayaranInvoice['total_bayar'],
                    'kredit'          => 0,
                    'valas'           => $metaDataValuta['id'],
                    'kurs'            => $pembayaranInvoice['kurs_sekarang'],
                    'keterangan'      => $pembayaranInvoice['keterangan'],
                    'id_inputer'      => session()->get("login")->user_id
                );
            }

            // --- Kredit : AKUN SELISIH (jika ada) ---
            if ($pembayaranInvoice['akun_selisih'] != null) {
                $result[] = array(
                    'id_transaksi'    => $id_transaksi_jurnal,
                    'company_id'      => $this->this_company_id,
                    'divisi_id'       => $pembayaranInvoice['divisi_id'],
                    'id_coa'          => $pembayaranInvoice['akun_selisih'],
                    'tanggal_jurnal'  => $resultTransaksiJurnal['tanggal_transaksi'],
                    'debit'           => 0,
                    'kredit'          => $pembayaranInvoice['total_bayar'],
                    'valas'           => $metaDataValuta['id'],
                    'kurs'            => $pembayaranInvoice['kurs_sekarang'],
                    'keterangan'      => $pembayaranInvoice['keterangan'],
                    'id_inputer'      => session()->get("login")->user_id
                );
            }

            // Update total di Jurnal
            $this->transaksiJurnalModel->update($id_transaksi_jurnal, [
                'total_debit'  => $pembayaranInvoice['total_bayar'],
                'total_kredit' => $pembayaranInvoice['total_bayar'],
            ]);

            if (count($result) > 0) {
                $this->jurnalUmumModel->insertJurnalBatch($result);
                $db->transCommit();
                return true;
            } else {
                $db->transRollback();
                return false;
            }
        } catch (Exception $e) {
            $db->transRollback();
            \var_dump($e->getMessage(), $e->getLine());
            return false;
        }
    }


    public function fix()
    {
        $data = $this->transaksiJurnalModel->select('
            transaksi_jurnal.id as transaksi_jurnal_id,
            transaksi_jurnal.total_debit as transaksi_jurnal_total_debit,
            transaksi_jurnal.total_kredit as transaksi_jurnal_total_kredit,
            rm_purchase_orders.id as rm_purchase_orders_id,
            rm_purchase_orders.total_before_pph as rm_purchase_orders_total,
            am_purchase_orders.id as am_purchase_orders_id,
            am_purchase_orders.total as am_purchase_orders_total,
            rm_import_pos.id as rm_import_pos_id,
            rm_import_pos.total as rm_import_pos_total,
        ')
            // ->join('transaksi_jurnal', 'transaksi_jurnal.id = jurnal_umum.id_transaksi', 'left')
            ->join('transaksi_pembelian', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = transaksi_pembelian.id_local_bb', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left')
            ->join('rm_import_pos', 'rm_import_pos.id = transaksi_pembelian.id_import_bb', 'left')
            ->findAll();
        // dd($data);

        $datas = [];
        foreach ($data as $key => $value) {
            $expected = null;

            if ($value['rm_purchase_orders_id']) {
                $expected = $value['rm_purchase_orders_total'];
            } elseif ($value['am_purchase_orders_id']) {
                $expected = $value['am_purchase_orders_total'];
            } elseif ($value['rm_import_pos_id']) {
                $expected = $value['rm_import_pos_total'];
            }

            if ($expected !== null) {
                // $needsUpdate = ($value['transaksi_jurnal_total_debit'] != $expected || $value['transaksi_jurnal_total_kredit'] != $expected);

                if (($value['transaksi_jurnal_total_debit'] != $expected || $value['transaksi_jurnal_total_kredit'] != $expected)) {
                    // Update jurnal_umum
                    // $this->jurnalUmumModel->update($value['jurnal_id'], [
                    //     'debit'  => $expected,
                    //     'kredit' => $expected,
                    // ]);
                    $datas[] = [
                        'id'  => $value['transaksi_jurnal_id'],
                        'expected'  => $expected
                    ];
                }
            }
        }
        dd($datas);
    }
}
