<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use App\Models\JurnalUmumModel;
use App\Models\TransaksiJurnalModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JurnalUmum extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_role_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $MetadataModel;
    protected $transaksiJurnalModel;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_role_id = session()->get("login")->this_role_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->MetadataModel = new MetadataModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->encrypter = \Config\Services::encrypter();
    }

    public function index()
    {
        $dateStart = $this->request->getPost('dateStart');
        $dateEnd = $this->request->getPost('dateEnd');

        if ($dateEnd) {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
            $condition2 = [
                'tanggal_transaksi >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_transaksi <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
        } else {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
            $condition2 = [
                'tanggal_transaksi >=' => date('Y-m-01'),
                'tanggal_transaksi <=' => date('Y-m-d')
            ];
        }

        if ($this->this_company_id == "1" || $this->this_company_id == "2") {
            $companyId = [1, 2];
        } else if ($this->this_company_id == "15") {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        if ($this->this_role_id != '7') {
            $condition['jurnal_umum.id_transaksi !='] = '1404';
            $condition['transaksi_jurnal.type_transaksi !='] = '1404';
            $condition2['transaksi_jurnal.type_transaksi !='] = '1404';
            $condition3 = '1404';
        }

        $dataSubAkun = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $dataMetadataTipeTransaksi = $this->MetadataModel
            ->asObject()
            ->where('name', 'tipe_transaksi')
            ->where('id !=', $condition3 ?? '')
            ->findAll();
        foreach ($dataMetadataTipeTransaksi as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }

        $data = [
            "dataSubAkuns" => $dataSubAkun,
            "dataMetadataTipeTransaksi" => $dataMetadataTipeTransaksi,
            "dateStart" => $dateStart ? $dateStart : date('01/m/Y'),
            "dateEnd" => $dateEnd ? $dateEnd : date('d/m/Y'),
        ];
        return view('Laporan/LaporanJurnalUmum/index', $data);
    }

    public function getData()
    {
        $request = $this->request->getVar();

        $dateStart     = $request['dateStart'] ?? date('Y-m-01');
        $dateEnd       = $request['dateEnd'] ?? date('Y-m-d');
        $typeTransaksi = $request['type_transaksi'] ?? null;
        $noBukti       = $request['no_bukti'] ?? null;
        $subsAkun      = $request['subs_akun'] ?? null;

        // === Company ID ===
        switch ($this->this_company_id) {
            case "1":
                $companyId = [1, 2];
                break;
            case "2":
                $companyId = [1, 2];
                break;

            case "15":
                $companyId = [15];
                break;

            default:
                $companyId = [16];
                break;
        }

        $builder = $this->jurnalUmumModel
            ->asObject()
            ->select("
                transaksi_jurnal.id as id_transaksi,
                transaksi_jurnal.no_bukti,
                jurnal_umum.tanggal_jurnal,
                sub_akuns.no_sub,
                sub_akuns.nama_sub,
                SUM(jurnal_umum.debit) AS sum_debit,
                SUM(jurnal_umum.kredit) AS sum_kredit,
                transaksi_jurnal.valas,
                transaksi_jurnal.exchange_rate,
                suppliers.name as supplier,
                jurnal_umum.keterangan,
                CASE 
                    WHEN jurnal_umum.divisi_id = 0 OR jurnal_umum.divisi_id IS NULL THEN 'ALL'
                    ELSE divisis.divisi 
                END as nama_divisi
            ")
            ->join('sub_akuns','jurnal_umum.id_coa = sub_akuns.id','left')
            ->join('transaksi_jurnal','jurnal_umum.id_transaksi=transaksi_jurnal.id','left')
            ->join('divisis','jurnal_umum.divisi_id=divisis.id','left')
            ->join('penerimaan_barang','penerimaan_barang.id=transaksi_jurnal.penerimaan_barang_id','left')
            ->join('suppliers','suppliers.id=penerimaan_barang.supplier_id','left')
            ->whereIn('jurnal_umum.company_id', $companyId)
            ->whereIn('sub_akuns.company_id', $companyId)
            ->where('sub_akuns.deletedAt', null)
            ->where('transaksi_jurnal.deleted_at', null)
            ->where('jurnal_umum.deletedAt', null)
            ->where('tanggal_jurnal >=', date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))))
            ->where('tanggal_jurnal <=', date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))));

        if ($typeTransaksi) {
            $builder->where('transaksi_jurnal.type_transaksi', $this->encrypter->decrypt(hex2bin($typeTransaksi)));
        }
        if ($noBukti) {
            $builder->where('transaksi_jurnal.id', $this->encrypter->decrypt(hex2bin($noBukti)));
        }
        if ($subsAkun) {
            $builder->where('sub_akuns.id', $subsAkun);
        }

        $data = $builder
        ->groupBy([
            'transaksi_jurnal.id',
            'sub_akuns.no_sub'
        ])
        ->orderBy('tanggal_jurnal', 'asc')
        ->findAll();

        $formatted = [];
        $lastGroup = null;

        foreach ($data as $row) {

            // IDENTIKAN KEY PEMBAGI GROUP
            $currentGroup = $row->no_bukti . "-" . $row->keterangan;

            if ($lastGroup !== $currentGroup) {
                // HEADER ROW
                $formatted[] = (object)[
                    "is_header"     => true,
                    "tanggal_jurnal"=> date('d/m/Y', strtotime($row->tanggal_jurnal)),
                    "nama_divisi"   => $row->nama_divisi,
                    "desc"          => $row->no_bukti . " - " . $row->keterangan,
                    "reference"     => "",
                    "supplier"      => "",
                    "currency"      => "",
                    "exchange_rate" => "",
                    "debit"         => "",
                    "kredit"        => ""
                ];
                $lastGroup = $currentGroup;
            }

            // DETAIL ROW
            $formatted[] = (object)[
                "is_header"     => false,
                "tanggal_jurnal"=> '',
                "nama_divisi"   => $row->nama_divisi,
                "desc"          => $row->no_sub . " - " . $row->nama_sub,
                "reference"     => '',
                "supplier"      => $row->supplier ?? '',
                "currency"      => $row->valas ?? 'IDR',
                "exchange_rate" => $row->exchange_rate ?? '1.00',
                "debit"         => (float)$row->sum_debit,
                "kredit"        => (float)$row->sum_kredit,
            ];
        }

        return $this->response->setJSON([
            "data" => $formatted
        ]);
    }

    public function exportExcel($tglAwal, $tglAkhir, $filter)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // =============================
        // PARAMETER
        // =============================
        $dateStart  = $tglAwal;
        $dateEnd    = $tglAkhir;
        $Filter     = $filter != "all"
            ? $this->encrypter->decrypt(hex2bin($filter))
            : "";

        if ($dateStart && $dateEnd) {
            $conditionMain = [
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
        } else {
            $conditionMain = [
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d'),
            ];
        }

        if ($Filter) {
            $conditionMain['transaksi_jurnal.type_transaksi'] = $Filter;
        }

        // =============================
        // COMPANY ID
        // =============================
        switch ($this->this_company_id) {
            case "1":
            case "2":
                $companyId = [1, 2];
                break;
            case "15":
                $companyId = [15];
                break;
            default:
                $companyId = [16];
        }

        // =============================
        // QUERY DATA
        // =============================
        $data = $this->jurnalUmumModel
            ->asObject()
            ->select("
                jurnal_umum.tanggal_jurnal,
                transaksi_jurnal.no_bukti,
                sub_akuns.no_sub,
                sub_akuns.nama_sub,
                SUM(jurnal_umum.debit) AS debit,
                SUM(jurnal_umum.kredit) AS kredit,
                transaksi_jurnal.valas,
                transaksi_jurnal.exchange_rate,
                suppliers.name as supplier,
                jurnal_umum.keterangan,
                CASE 
                    WHEN jurnal_umum.divisi_id = 0 OR jurnal_umum.divisi_id IS NULL THEN 'ALL'
                    ELSE divisis.divisi 
                END as department
            ")
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('divisis', 'jurnal_umum.divisi_id = divisis.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = transaksi_jurnal.penerimaan_barang_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->where($conditionMain)
            ->whereIn('jurnal_umum.company_id', $companyId)
            ->whereIn('sub_akuns.company_id', $companyId)
            ->where('jurnal_umum.deletedAt', null)
            ->where('transaksi_jurnal.deleted_at', null)
            ->groupBy([
                'transaksi_jurnal.id',
                'sub_akuns.no_sub'
            ])
            ->orderBy('tanggal_jurnal', 'asc')
            ->findAll();

        // =============================
        // HELPER FORMAT
        // =============================
        $fmtRp = function ($n) {
            return number_format((float)$n, 2, ',', '.');
        };

        $fmtCur = function ($n, $cur) {
            return number_format((float)$n, 2, ',', '.') . ' ' . $cur;
        };

        // =============================
        // TITLE
        // =============================
        $sheet->setCellValue('A1', 'PT. TOBA SURIMI INDUSTRIES, Tbk (KIM 2)');
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A2', 'Journal Form Report');
        $sheet->mergeCells('A2:M2');

        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // =============================
        // HEADER
        // =============================
        $row = 4;
        $sheet->fromArray([
            'Date', 'Department', 'Transaction Num', 'Information',
            'Invoice', 'Estimate Num', 'Estimate Name', 'Desc',
            'Reference', 'Currency', 'Exchange Rate', 'Debit', 'Credit'
        ], null, "A{$row}");

        $sheet->getStyle("A{$row}:M{$row}")->getFont()->setBold(true);
        $sheet->getStyle('A:M')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // =============================
        // DATA
        // =============================
        $row++;
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($data as $d) {
            $sheet->setCellValue("A{$row}", date('d/m/Y', strtotime($d->tanggal_jurnal)));
            $sheet->setCellValue("B{$row}", $d->department);
            $sheet->setCellValue("C{$row}", $d->no_bukti);
            $sheet->setCellValue("D{$row}", $d->supplier ?? '');
            $sheet->setCellValue("E{$row}", '');
            $sheet->setCellValue("F{$row}", $d->no_sub);
            $sheet->setCellValue("G{$row}", $d->nama_sub);
            $sheet->setCellValue("H{$row}", $d->keterangan);
            $sheet->setCellValue("I{$row}", '');
            $sheet->setCellValue("J{$row}", $fmtCur($d->debit + $d->kredit, $d->valas ?? 'IDR'));
            $sheet->setCellValue("K{$row}", number_format((float)$d->exchange_rate, 2, ',', '.'));
            $sheet->setCellValue("L{$row}", $fmtRp($d->debit));
            $sheet->setCellValue("M{$row}", $fmtRp($d->kredit));

            $totalDebit  += $d->debit;
            $totalCredit += $d->kredit;
            $row++;
        }

        // Debit
        $sheet->getStyle("A5:J{$row}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
            // Exchange Rate
        $sheet->getStyle("K5:M{$row}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // =============================
        // FOOTER TOTAL
        // =============================
        $sheet->mergeCells("J{$row}:K{$row}");
        $sheet->setCellValue("J{$row}", 'Total');
        $sheet->setCellValue("L{$row}", $fmtRp($totalDebit));
        $sheet->setCellValue("M{$row}", $fmtRp($totalCredit));

        $sheet->getStyle("A{$row}:M{$row}")->getFont()->setBold(true);
        $sheet->getStyle("L{$row}:M{$row}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


        // =============================
        // AUTO WIDTH
        // =============================
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // =============================
        // OUTPUT
        // =============================
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Journal_Form_Report';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // public function exportExcel($tglAwal, $tglAkhir, $filter)
    // {
    //     set_time_limit(0);
    //     ini_set('memory_limit', '512M');
    //     $spreadsheet = new Spreadsheet();

    //     $dateStart  = $tglAwal;
    //     $dateEnd    = $tglAkhir;
    //     $Filter     = $filter != "all" ? $this->encrypter->decrypt(hex2bin($filter)) : "";

    //     if ($dateStart && $dateEnd) {
    //         $conditionMain = [
    //             'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
    //             'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
    //         ];
    //     } else {
    //         $conditionMain = [
    //             'tanggal_jurnal >=' => date('Y-m-01'),
    //             'tanggal_jurnal <=' => date('Y-m-d'),
    //         ];
    //     }

    //     if ($Filter) {
    //         $conditionMain['transaksi_jurnal.type_transaksi'] = $Filter;
    //     }

    //     // === Company ID ===
    //     switch ($this->this_company_id) {
    //         case "1":
    //             $companyId = [1, 2];
    //             break;
    //         case "2":
    //             $companyId = [1, 2];
    //             break;

    //         case "15":
    //             $companyId = [15];
    //             break;

    //         default:
    //             $companyId = [16];
    //             break;
    //     }

    //     // === Header Excel ===
    //     $spreadsheet->setActiveSheetIndex(0)
    //         ->setCellValue('A1', 'Tanggal')
    //         ->setCellValue('B1', 'Department')
    //         ->setCellValue('C1', 'Description')
    //         ->setCellValue('E1', 'Reference')
    //         ->setCellValue('F1', 'Supplier')
    //         ->setCellValue('G1', 'Currency')
    //         ->setCellValue('H1', 'Exchange Rate')
    //         ->setCellValue('I1', 'Debit')
    //         ->setCellValue('J1', 'Kredit');
    //     $spreadsheet->getActiveSheet()->mergeCells('C1:D1');

    //     $spreadsheet->getActiveSheet()
    //         ->getStyle("A1:J1")
    //         ->applyFromArray([
    //             'font' => ['bold' => true],
    //         ]);

    //     // === QUERY MAIN DATA FOLLOW DATA TABLE ===
    //     $dataJurnal = $this->jurnalUmumModel
    //         ->asObject()
    //         ->select("
    //             transaksi_jurnal.id as id_transaksi,
    //             transaksi_jurnal.no_bukti,
    //             jurnal_umum.tanggal_jurnal,
    //             sub_akuns.no_sub,
    //             sub_akuns.nama_sub,
    //             SUM(jurnal_umum.debit) AS sum_debit,
    //             SUM(jurnal_umum.kredit) AS sum_kredit,
    //             transaksi_jurnal.valas,
    //             transaksi_jurnal.exchange_rate,
    //             suppliers.name as supplier,
    //             jurnal_umum.keterangan,
    //             CASE 
    //                 WHEN jurnal_umum.divisi_id = 0 OR jurnal_umum.divisi_id IS NULL THEN 'ALL'
    //                 ELSE divisis.divisi 
    //             END as nama_divisi
    //         ")
    //         ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
    //         ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
    //         ->join('divisis', 'jurnal_umum.divisi_id = divisis.id', 'left')
    //         ->join('penerimaan_barang', 'penerimaan_barang.id = transaksi_jurnal.penerimaan_barang_id', 'left')
    //         ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
    //         ->where($conditionMain)
    //         ->whereIn('jurnal_umum.company_id', $companyId)
    //         ->whereIn('sub_akuns.company_id', $companyId)
    //         ->where('sub_akuns.deletedAt', null)
    //         ->where('transaksi_jurnal.deleted_at', null)
    //         ->where('jurnal_umum.deletedAt', null)
    //         ->groupBy([
    //             'transaksi_jurnal.id',
    //             'sub_akuns.no_sub'
    //         ])
    //         ->orderBy('tanggal_jurnal', 'asc')
    //         ->findAll();

    //     // === MATCH FORMAT DATATABLE ===
    //     function format_currency($nilai)
    //     {
    //         return "Rp " . number_format((float)$nilai, 2, ',', '.');
    //     }

    //     $formatted = [];
    //     $lastGroup = null;

    //     foreach ($dataJurnal as $row) {
    //         $currentGroup = $row->no_bukti . "-" . $row->keterangan;

    //         if ($lastGroup !== $currentGroup) {
    //             $formatted[] = (object)[
    //                 "is_header"      => true,
    //                 "tanggal_jurnal" => date('d/m/Y', strtotime($row->tanggal_jurnal)),
    //                 "nama_divisi"    => $row->nama_divisi,
    //                 "desc"           => $row->no_bukti . " - " . $row->keterangan,
    //                 "reference"      => '',
    //                 "supplier"       => '',
    //                 "currency"       => '',
    //                 "exchange_rate"  => '',
    //                 "debit"          => '',
    //                 "kredit"         => ''
    //             ];
    //             $lastGroup = $currentGroup;
    //         }

    //         $formatted[] = (object)[
    //             "is_header"     => false,
    //             "tanggal_jurnal"=> '',
    //             "nama_divisi"   => $row->nama_divisi,
    //             "desc"          => $row->no_sub . " - " . $row->nama_sub,
    //             "reference"     => '',
    //             "supplier"      => $row->supplier ?? '',
    //             "currency"      => $row->valas ?? 'IDR',
    //             "exchange_rate" => $row->exchange_rate ?? '1.00',
    //             "debit"         => (float)$row->sum_debit,
    //             "kredit"        => (float)$row->sum_kredit,
    //         ];
    //     }

    //     // === WRITE INTO EXCEL ===
    //     $column = 2;
    //     $totalDebit = 0;
    //     $totalKredit = 0;

    //     foreach ($formatted as $row) {

    //         if ($row->is_header) {
    //             $spreadsheet->setActiveSheetIndex(0)
    //                 ->setCellValue('A' . $column, $row->tanggal_jurnal)
    //                 ->setCellValue('C' . $column, $row->desc);
    //             $spreadsheet->getActiveSheet()->mergeCells('C' . $column . ':J' . $column);

    //             $spreadsheet->getActiveSheet()->getStyle("A{$column}:J{$column}")
    //                 ->applyFromArray([
    //                     'font' => ['bold' => true],
    //                 ]);
    //         } else {
    //             $spreadsheet->setActiveSheetIndex(0)
    //                 ->setCellValue('A' . $column, $row->tanggal_jurnal)
    //                 ->setCellValue('B' . $column, $row->nama_divisi)
    //                 ->setCellValue('C' . $column, $row->desc)
    //                 ->setCellValue('E' . $column, $row->reference)
    //                 ->setCellValue('F' . $column, $row->supplier)
    //                 ->setCellValue('G' . $column, $row->currency)
    //                 ->setCellValue('H' . $column, $row->exchange_rate)
    //                 ->setCellValue('I' . $column, format_currency($row->debit))
    //                 ->setCellValue('J' . $column, format_currency($row->kredit));

    //             $spreadsheet->getActiveSheet()->mergeCells('C' . $column . ':D' . $column);

    //             $totalDebit  += $row->debit;
    //             $totalKredit += $row->kredit;
    //         }

    //         $column++;
    //     }

    //     // === FOOTER TOTAL ===
    //     $spreadsheet->setActiveSheetIndex(0)
    //         ->setCellValue('A' . $column, "Total Transaksi")
    //         ->mergeCells("A{$column}:H{$column}")
    //         ->setCellValue("I{$column}", format_currency($totalDebit))
    //         ->setCellValue("J{$column}", format_currency($totalKredit));

    //     $spreadsheet->getActiveSheet()
    //         ->getStyle("A{$column}:J{$column}")
    //         ->applyFromArray([
    //             'font' => ['bold' => true],
    //         ]);

    //     $writer = new Xlsx($spreadsheet);
    //     $filename = 'Laporan-Jurnal';

    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
    //     header('Cache-Control: max-age=0');

    //     $writer->save('php://output');
    //     exit;
    // }
}
