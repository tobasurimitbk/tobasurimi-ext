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
use App\Models\PembayaranInvoiceDetailModel;
use App\Models\PembayaranInvoiceModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\SalesOrderLainModel;
use App\Models\TutupBukuModel;
use Config\Database;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JurnalUmum extends BaseController
{
    protected $token;
    protected $this_company_id;
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
    protected $penerimaanBarangModel;
    protected $metadataModel;
    protected $kursModel;
    protected $tutupBukuModel;

    protected $salesOrderLainModel;
    protected $salesOrderLainDetailModel;
    protected $pembayaranInvoiceModel;
    protected $pembayaranInvoiceDetailModel;
    protected $companiesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
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
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->metadataModel = new MetadataModel();
        $this->kursModel = new KursModel();
        $this->tutupBukuModel = new TutupBukuModel();

        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $this->pembayaranInvoiceModel = new PembayaranInvoiceModel();
        $this->pembayaranInvoiceDetailModel = new PembayaranInvoiceDetailModel();
        $this->companiesModel = new CompaniesModel();
    }

    public function index()
    {
        $tipeTransaksi = $this->MetadataModel
            ->where('name', 'tipe_transaksi')
            ->findAll();

        $data = [
            'tipeTransaksi' => $tipeTransaksi
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
        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "start_date" => $this->request->getVar('start_date'),
            "end_date" => $this->request->getVar("end_date"),
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
            if ($data->po_id != null) {
                // Transaksi Pembelian
                $transaksiPembelian = $this->transaksiPembelianModel
                    ->select('suppliers.name as supplier')
                    ->join('suppliers', 'suppliers.id = transaksi_pembelian.id_supplier', 'left')
                    ->where('id_transaksi_jurnal', $data->id)
                    ->first();

                $supplierName = $transaksiPembelian != null ? "0 : " . $transaksiPembelian['supplier'] : "0 : ";
            }

            // Cek Tutup Buku Per Transaksi
            $tutupBuku = $this->tutupBukuModel
                ->where('company_id', $this->this_company_id)
                ->where('bulan', date('Y-m', strtotime($data->tanggal_transaksi)))
                ->first();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "transaksi_type_name"   => $data->transaksi_type_name,
                "no_transaksi"          => $data->no_transaksi,
                "tanggal_transaksi"     => date('d/m/Y', strtotime($data->tanggal_transaksi)),
                "uraian_transaksi"      => $data->uraian_transaksi,
                "invoice"               => isset($supplierName) ? $supplierName : "0 : ",
                "metode_input"          => strtoupper($data->metode_input),
                "valas"                 => $data->valas,
                "nilai"                 => $data->total_debit,
                "nilai_idr"             => ($data->total_debit * $data->exchange_rate),
                "tutup_buku"            => $tutupBuku == null ? 0 : 1,
            ]);
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
                    'debit' => $l->jenis_transaksi == "debit" ? $l->jumlah : 0,
                    'kredit' => $l->jenis_transaksi == "kredit" ? $l->jumlah : 0,
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
                'total_debit' => $this->request->getVar('totalDebit'),
                'total_kredit' => $this->request->getVar('totalKredit'),
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
                    'debit' => $l->jenis_transaksi == "debit" ? $l->jumlah : 0,
                    'kredit' => $l->jenis_transaksi == "kredit" ? $l->jumlah : 0,
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
                'jumlah' => floatval($jumlah),
                'kurs' => floatval($j['kurs']),
                'jumlah_idr' => ($jumlah * $j['kurs']),
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

    public function delete($id)
    {
        $id = decrypt($id);
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
                'jumlah' => $jumlah,
                'jumlah_idr' => ($jumlah * $j['kurs']),
                'nama_sub' => $j['nama_sub'],
                'no_sub' => $j['no_sub'],
                'kurs' => $j['kurs']
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
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
            "currentPage"   => 1,

        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "start_date" => $this->request->getVar('start_date'),
            "end_date" => $this->request->getVar("end_date"),
            "type_transaksi" => $this->request->getVar('type_transaksi'),
            "search" => $this->request->getVar("search"),
        ];

        $condition = [
            'jurnal_umum.company_id' => $this->this_company_id,
            'transaksi_jurnal.deleted_at' => null,
        ];

        $dataQry = $this->transaksiJurnalModel->getList($condition, $addCondition, 10000000, 0);
        $dataJurnal = $this->getData($dataQry['data'], $payload);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $dataStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $column = 2;

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B1', 'Transaksi')
            ->setCellValue('C1', 'Nomor')
            ->setCellValue('D1', 'Tanggal')
            ->setCellValue('E1', 'Invoice')
            ->setCellValue('F1', 'Keterangan')
            ->setCellValue('G1', 'Nilai')
            ->setCellValue('H1', 'Valas')
            ->setCellValue('I1', 'Nilai (IDR)');


        $sheet->getStyle('A1:I1')->applyFromArray($headerStyleArray);

        foreach ($dataJurnal as $row) {
            $sheet->setCellValue('A' . $column, $row['no'])
                ->setCellValue('B' . $column, $row['transaksi_type_name'])
                ->setCellValue('C' . $column, $row['no_transaksi'])
                ->setCellValue('D' . $column, $row['tanggal_transaksi'])
                ->setCellValue('E' . $column, $row['invoice'])
                ->setCellValue('F' . $column, $row['uraian_transaksi'])
                ->setCellValue('G' . $column, $row['nilai'])
                ->setCellValue('H' . $column, $row['valas'])
                ->setCellValue('I' . $column, $row['nilai_idr']);

            $sheet->getStyle('A' . $column . ':I' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Transaksi_Jurnal';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
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
            "start_date" => $this->request->getVar('start_date'),
            "end_date" => $this->request->getVar("end_date"),
            "type_transaksi" => $this->request->getVar('type_transaksi'),
            "search" => $this->request->getVar("search"),
        ];

        $condition = [
            'jurnal_umum.company_id' => $this->this_company_id,
            'transaksi_jurnal.deleted_at' => null,
        ];

        $dataQry = $this->transaksiJurnalModel->getList($condition, $addCondition, 10000000, 0);
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

    public function insertDataPembelian($poID, $type, $kategori, $module)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";

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
                            'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'total_debit' => $totalPO,
                            'total_kredit' => $totalPO,
                            'metode_input' => 'system',
                            'type_transaksi' => $idTransaksi,
                        );

                        // ambil id dari transaksi jurnal untuk jurnal umum
                        $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                        // start input jurnal dari banyak detail barang

                        foreach ($dataPOBBDetail as $dataBBDetail) {
                            $totalPOqty = (($dataBBDetail->general_price * $dataBBDetail->qty) + ($dataBBDetail->daily_price * $dataBBDetail->qty) + ($dataBBDetail->monthly_price * $dataBBDetail->qty));
                            $totalPO += $totalPOqty;
                            $barangAPFound = false;
                            foreach ($dataAccountBarang as $value) {
                                if ($dataBB->barang_id == $value->barang_master_id && $dataBB->divisi_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                    $barangAP = $value->ap_id;
                                    $barangAR = $value->ar_id;
                                    $barangAPFound = true;
                                }
                            }
                            if (!$barangAPFound) {
                                // Collect errors
                                $errors[] = "Barang Tidak Memiliki Akun COA";
                            } else {
                                $result[] = array(
                                    'id_transaksi' => $id_transaksi_jurnal,
                                    'divisi_id' => $dataBB->divisi_id,
                                    'company_id' => $this->this_company_id,
                                    'id_coa' =>  $barangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => (repairDouble($totalPOqty)),
                                    'kredit' => 0,
                                    'valas' => $dataMetadataValutaIDR->id,
                                    'kurs' => 1,
                                    'keterangan' => $dataBB->po_no,
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
                            'divisi_id' => $dataBB->divisi_id,
                            'company_id' => $this->this_company_id,
                            'id_coa' =>  $UtangAP,
                            'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'debit' => 0,
                            'kredit' => repairDouble($totalPO),
                            'valas' => $dataMetadataValutaIDR->id,
                            'kurs' => 1,
                            'keterangan' => $dataBB->po_no,
                            'id_inputer' => session()->get("login")->user_id
                        );

                        $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                        $resultTransaksiJurnal[] = array(
                            'no_transaksi' => $no_transaksi_jurnal,
                            'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                            'total_debit' => $totalPO,
                            'total_kredit' => $totalPO,
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

                        $dataPOBBDetail = $this->rmImportPODetailModel->asObject()->where('deletedAt', null)->where('rm_import_po_id', $dataBB->id)->findAll();
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
                            'exchange_rate' => $exchangeTransaksi,
                        );

                        // ambil id dari transaksi jurnal untuk jurnal umum
                        $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                        // end inisialisasi kode transaksi
                        // start input jurnal dari banyak detail barang
                        foreach ($dataPOBBDetail as $dataBBDetail) {
                            $totalPO += repairDouble($dataBBDetail->total);
                            $barangAPFound = false;
                            foreach ($dataAccountBarang as $value) {
                                if ($dataBBDetail->barang_id == $value->barang_master_id && $dataBB->division_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                    $barangAP = $value->ap_id;
                                    $barangAR = $value->ar_id;
                                    $barangAPFound = true;
                                }
                            }
                            if (!$barangAPFound) {
                                // Collect errors
                                $errors[] = "Barang Tidak Memiliki Akun COA";
                            } else {
                                $result[] = array(
                                    'id_transaksi' => $id_transaksi_jurnal,
                                    'divisi_id' => $dataBB->division_id,
                                    'company_id' => $this->this_company_id,
                                    'id_coa' =>  $barangAP,
                                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBB->po_date))),
                                    'debit' => repairDouble($dataBBDetail->total) * $exchangeTransaksi,
                                    'kredit' => 0,
                                    'valas' => $valasTransaksi,
                                    'kurs' => $exchangeTransaksi,
                                    'keterangan' => $dataBB->po_no,
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
                            'valas' => $valasTransaksi,
                            'kurs' => $exchangeTransaksi,
                            'keterangan' => $dataBB->po_no,
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
            $dataPOBP = $this->aMPurchaseOrderModel->asObject()->where('deletedAt', null)->where('id', $poID)->findAll();
            if ($dataPOBP) {
                $result = array();
                $resultTransaksiJurnal = "";
                $resultTransaksiPembelian = array();
                $valasTransaksi = "IDR";
                $exchangeTransaksi = 1;
                foreach ($dataPOBP as $dataBP) {
                    $totalPO = 0;
                    $kodeTransaksi = "";
                    $idTransaksi = "";

                    $dataDepartment = $this->divisionModel->getAccountKasForJurnal($dataBP->division_id, $dataBP->company_id);
                    $dataSupplier = $this->supplierModel->getSupplierForJurnal($dataBP->supplier_id);
                    $dataAccountSupplier = $this->accountSupplierModel->getAccountSupplierForJurnal();
                    $dataAccountModule = $this->accountModuleModel->getAccountModuleForJurnal();
                    $dataAccountBarang = $this->accountBarangModel->getAccountBarangForJurnal();
                    $kursData = $this->kursModel->getByMetaId($dataBP->currency, $dataBP->po_date);
                    $metaValuta = $this->metadataModel->get_by_name('Valuta');
                    foreach ($metaValuta as $valueValuta) {
                        if ($dataBP->currency == $valueValuta['id']) {
                            $valasTransaksi = $valueValuta['value'];
                            if ($kursData) {
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            } else {
                                $exchangeTransaksi = 1;
                            }
                        }
                    }

                    $dataPOBPDetail = $this->aMPurchaseOrderDetailModel->asObject()->where('deletedAt', null)->where('am_purchase_order_id', $dataBP->id)->findAll();
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
                    // end inisialisasi kode transaksi
                    // input ke transaksi jurnal
                    $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                    $resultTransaksiJurnal = array(
                        'no_transaksi' => $no_transaksi_jurnal,
                        'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                        'total_debit' => $totalPO * $exchangeTransaksi,
                        'total_kredit' => $totalPO * $exchangeTransaksi,
                        'metode_input' => 'system',
                        'tipe_barang' => $type,
                        'kategori_barang' => $kategori,
                        'po_id' => $poID,
                        'type_transaksi' => $idTransaksi,
                        'no_bukti' => $no_transaksi_jurnal,
                        'valas' => $valasTransaksi,
                        'exchange_rate' => $exchangeTransaksi,
                    );

                    // ambil id dari transaksi jurnal untuk jurnal umum
                    $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);

                    // start input jurnal dari banyak detail barang
                    foreach ($dataPOBPDetail as $dataBPDetail) {
                        $totalPO += repairDouble($dataBPDetail->total);
                        $barangAPFound = false;
                        foreach ($dataAccountBarang as $value) {
                            if ($dataBPDetail->barang_id == $value->barang_master_id && $dataBP->division_id == $value->divisi_id && $value->ap_id != null && $value->ar_id != null) {
                                $barangAP = $value->ap_id;
                                $barangAR = $value->ar_id;
                                $barangAPFound = true;
                            }
                        }
                        if (!$barangAPFound) {
                            // Collect errors
                            $errors[] = "Barang Tidak Memiliki Akun COA";
                        } else {
                            $result[] = array(
                                'id_transaksi' => $id_transaksi_jurnal,
                                'divisi_id' => $dataBP->division_id,
                                'company_id' => $this->this_company_id,
                                'id_coa' =>  $barangAP,
                                'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                                'debit' => repairDouble($dataBPDetail->total) * $exchangeTransaksi,
                                'kredit' => 0,
                                'valas' => $valasTransaksi,
                                'kurs' => $exchangeTransaksi,
                                'keterangan' => $dataBP->po_no,
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
                        'divisi_id' => $dataBP->division_id,
                        'company_id' => $this->this_company_id,
                        'id_coa' =>  $UtangAP,
                        'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                        'debit' => 0,
                        'kredit' => $totalPO * $exchangeTransaksi,
                        'valas' => $valasTransaksi,
                        'kurs' => $exchangeTransaksi,
                        'keterangan' => $dataBP->po_no,
                        'id_inputer' => session()->get("login")->user_id
                    );

                    $resultTransaksiPembelian[] = array(
                        'id_po_bp' => $poID,
                        'id_supplier' => $dataBP->supplier_id,
                        'id_transaksi_jurnal' => $id_transaksi_jurnal,
                        'tgl_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $dataBP->po_date))),
                    );
                }

                $this->jurnalUmumModel->insertJurnalBatch($result);
                $this->transaksiPembelianModel->insertBatchTransaksiPembelian($resultTransaksiPembelian);
            }
        }
    }

    public function insertDataPembayaran($payID, $module)
    {
        $KasAP = "";
        $KasAR = "";
        $UtangAP = "";
        $UtangAR = "";
        $barangAP = "";
        $barangAR = "";
        $dataPO = "";
        if ($module == "LOKAL") {
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
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                    'total_debit' => $POlocal->amount,
                    'total_kredit' => $POlocal->amount,
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => 'IDR',
                    'exchange_rate' => 1,
                );

                // ambil id dari transaksi jurnal untuk jurnal umum
                $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
                //untuk insert ke jurnal umum

                $multipleLpbIds = str_replace(['[', ']'], '', $POlocal->multiple_lpb_id); // Remove brackets
                $lpbIdsArray = explode(',', $multipleLpbIds); // Split the string into an array by comma

                foreach ($lpbIdsArray as $lpbId) {
                    $sumValue = 0;
                    $dataPenerimaan = $this->penerimaanBarangModel->asObject()
                        ->select('penerimaan_barang.*, penerimaan_barang_detail.*, local_po_payment_details.*')
                        ->join('penerimaan_barang_detail', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
                        ->join('local_po_payment_details', 'local_po_payment_details.penerimaan_barang_id = penerimaan_barang.id', 'left')
                        ->where('penerimaan_barang.id', $lpbId)
                        ->where('penerimaan_barang.deletedAt', null)
                        ->where('penerimaan_barang_detail.deletedAt', null)
                        ->where('local_po_payment_details.deletedAt', null)
                        ->where('local_po_payment_details.local_po_payment_id', $payID)
                        ->groupBy('local_po_payment_details.penerimaan_barang_id, local_po_payment_details.penerimaan_barang_detail_id')
                        ->findAll();

                    // var_dump($dataPenerimaan);
                    foreach ($dataPenerimaan as $value) {
                        $dataPO = str_replace(['[', ']', '"', "\\"], '', $value->multiple_po_no);
                        $sumValue = $value->total;
                        $result[] = array(
                            'id_transaksi'      => $id_transaksi_jurnal,
                            'id_coa'            => $POlocal->akun_kas == 0 || $POlocal->akun_kas == NULL ? $UtangAR : $POlocal->akun_kas,
                            'company_id'            => $POlocal->company_id,
                            'divisi_id'            => $POlocal->divisi_id,
                            'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                            'debit'             => ($sumValue),
                            'kredit'            => 0,
                            'valas'             => 'IDR',
                            'kurs'              => 1,
                            'keterangan'        => "Pembayaran PO " . $dataPO,
                            'id_inputer'        => session()->get("login")->user_id
                        );
                        $result[] = array(
                            'id_transaksi'      => $id_transaksi_jurnal,
                            'id_coa'            => $POlocal->akun_selisih,
                            'company_id'            => $POlocal->company_id,
                            'divisi_id'            => $POlocal->divisi_id,
                            'tanggal_jurnal'    => date('Y-m-d', strtotime(str_replace('/', '-', $POlocal->payment_date))),
                            'debit'             => 0,
                            'kredit'            => ($sumValue),
                            'valas'             => 'IDR',
                            'kurs'              => 1,
                            'keterangan'        => "Pembayaran PO " . $dataPO,
                            'id_inputer'        => session()->get("login")->user_id
                        );
                    }
                }
                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
        } else {
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
                $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($kodeTransaksi);
                $resultTransaksiJurnal = array(
                    'no_transaksi' => $no_transaksi_jurnal,
                    'tanggal_transaksi' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'total_debit' => repairDouble($POimport->payment_amt)  * repairDouble($POimport->current_exchange_rate),
                    'total_kredit' => repairDouble($POimport->payment_amt)  * repairDouble($POimport->current_exchange_rate),
                    'metode_input' => 'system',
                    'type_transaksi' => $idTransaksi,
                    'no_bukti' => $no_transaksi_jurnal,
                    'valas' => $POimport->currency,
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
                    'kredit' => repairDouble($POimport->payment_amt) * repairDouble($POimport->current_exchange_rate),
                    'valas' => $POimport->currency,
                    'kurs' => $POimport->current_exchange_rate,
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $result[] = array(
                    'id_transaksi' => $id_transaksi_jurnal,
                    'id_coa' =>  $POimport->akun_selisih == 0 || $POimport->akun_selisih == NULL ? $UtangAR : $POimport->akun_selisih,
                    'tanggal_jurnal' => date('Y-m-d', strtotime(str_replace('/', '-', $POimport->payment_date))),
                    'debit' => repairDouble($POimport->payment_amt) * repairDouble($POimport->current_exchange_rate),
                    'kredit' => 0,
                    'valas' => $POimport->currency,
                    'kurs' => $POimport->current_exchange_rate,
                    'keterangan' => "Pembayaran PO " . $dataPO,
                    'id_inputer' => session()->get("login")->user_id
                );
                $this->jurnalUmumModel->insertJurnalBatch($result);
            }
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
                'kredit' => repairDouble($dataPO['hargaTerakhirNumber']),
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
                'debit' => repairDouble($dataPO['hargaTerakhirNumber']),
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
                'debit' => repairDouble($dataPO['hargaTerakhirNumber']),
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
                'kredit' => repairDouble($dataPO['hargaTerakhirNumber']),
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

        var_dump($kasDepartment);
        var_dump($piutangDepartment);
        var_dump($gajiDepartment);
        var_dump($hppDepartment);
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
                'type_transaksi' => $metaDataTypeTransaksi['id'],
                'valas' => $metaDataValuta['value']
            );

            $id_transaksi_jurnal = $this->transaksiJurnalModel->insertTransaksiJurnal($resultTransaksiJurnal);
            $hargaTotalBarangInvoice = 0;
            $result = array();
            $totalDebit = 0;
            $totalKredit = 0;

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
                            'keterangan' => "Pembayaran Invoice (Invoice Lain), Nomor : " . $p['nama_barang'],
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
                            'keterangan' => "Pembayaran Invoice (Barang Lain), Nomor : " . $p['nama_barang'],
                            'id_inputer' => session()->get("login")->user_id
                        );
                        $totalKredit += $p['harga_total'];
                    }
                } else {
                    // PASTI BARANG
                    $hargaTotalBarangInvoice += $p['harga_total'];
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
                    'keterangan' => "Pembayaran Invoice (Barang Invoice), Nomor : " . $pembayaranInvoice['no_pembayaran'],
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
                    'keterangan' => "Pembayaran Invoice (Barang Invoice), Nomor : " . $pembayaranInvoice['no_pembayaran'],
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
}
