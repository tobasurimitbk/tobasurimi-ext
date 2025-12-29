<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use App\Models\JurnalUmumModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Config\Database;

class NeracaSaldo extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $MetadataModel;
    protected $encrypter;
    protected $db;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->KategoriAkunsModel = new KategoriAkunsModel();
        $this->HeaderAkunsModel = new HeaderAkunsModel();
        $this->MetadataModel = new MetadataModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->encrypter = \Config\Services::encrypter();
        $this->db = Database::connect();
    }

    public function index()
    {
        return view('Laporan/LaporanNeracaSaldo/index', [
            "dateStart" => $this->request->getGet('dateStart') ?: date('01/m/Y'),
            "dateEnd"   => $this->request->getGet('dateEnd')   ?: date('d/m/Y'),
        ]);
    }

    public function getData()
    {
        $dateStart = $this->request->getGet('dateStart');
        $dateEnd   = $this->request->getGet('dateEnd');

        if ($dateStart && $dateEnd) {
            $start = date('Y-m-d', strtotime(str_replace('/', '-', $dateStart)));
            $end   = date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd)));
        } else {
            $start = date('Y-m-01');
            $end   = date('Y-m-d');
        }

        // === Company ID ===
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
                break;
        }

        $rows = $this->db->table('sub_akuns sa')
            ->select("
                CONCAT(sa.no_sub, ' ', sa.nama_sub) AS akun,

                SUM( CASE WHEN tj.type_transaksi = '1404' THEN ju.debit  ELSE 0 END ) AS saldo_awal_debit,
                SUM( CASE WHEN tj.type_transaksi = '1404' THEN ju.kredit ELSE 0 END ) AS saldo_awal_kredit,

                SUM( CASE WHEN tj.type_transaksi != '1404' THEN ju.debit  ELSE 0 END ) AS pergerakan_debit,
                SUM( CASE WHEN tj.type_transaksi != '1404' THEN ju.kredit ELSE 0 END ) AS pergerakan_kredit,

                SUM(ju.debit)  AS total_debit,
                SUM(ju.kredit) AS total_kredit
            ")
            ->join('jurnal_umum ju', 'ju.id_coa = sa.id AND sa.is_header IS NULL', 'left')
            ->join('transaksi_jurnal tj', 'tj.id = ju.id_transaksi', 'left')
            ->whereIn('sa.company_id', $companyId)
            ->whereIn('ju.company_id', $companyId)
            ->where('ju.tanggal_jurnal >=', $start)
            ->where('ju.tanggal_jurnal <=', $end)
            ->where('sa.is_header', null)
            ->where('tj.deleted_at', null)
            ->where('ju.deletedAt', null)
            ->groupBy('sa.no_sub')
            ->orderBy('sa.no_sub')
            ->get()
            ->getResult();

        // format ke tampilan datatable
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r->akun,
                "",
                number_format($r->saldo_awal_debit, 2, ',', '.'),
                number_format($r->saldo_awal_kredit, 2, ',', '.'),
                number_format($r->pergerakan_debit, 2, ',', '.'),
                number_format($r->pergerakan_kredit, 2, ',', '.'),
                number_format($r->total_debit, 2, ',', '.'),
                number_format($r->total_kredit, 2, ',', '.'),
            ];
        }

        return $this->response->setJSON(["data" => $data]);
    }

    public function exportExcel($dateStart = null, $dateEnd = null)
    {
        // ====== HANDLE TANGGAL ======
        if ($dateStart !== "all" && $dateEnd !== "now") {
            $start = $dateStart;
            $end   = $dateEnd;
        } else {
            $start = date('Y-m-01');
            $end   = date('Y-m-d');
        }

        // ====== COMPANY ID ======
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
                break;
        }

        // ====== QUERY DATA SAMA PERSIS DENGAN DATATABLE ======
        $rows = $this->db->table('sub_akuns sa')
            ->select("
                CONCAT(sa.no_sub, ' ', sa.nama_sub) AS akun,
                SUM( CASE WHEN tj.type_transaksi = '1404' THEN ju.debit  ELSE 0 END ) AS saldo_awal_debit,
                SUM( CASE WHEN tj.type_transaksi = '1404' THEN ju.kredit ELSE 0 END ) AS saldo_awal_kredit,
                SUM( CASE WHEN tj.type_transaksi != '1404' THEN ju.debit  ELSE 0 END ) AS pergerakan_debit,
                SUM( CASE WHEN tj.type_transaksi != '1404' THEN ju.kredit ELSE 0 END ) AS pergerakan_kredit,
                SUM(ju.debit)  AS total_debit,
                SUM(ju.kredit) AS total_kredit
            ")
            ->join('jurnal_umum ju', 'ju.id_coa = sa.id AND sa.is_header IS NULL', 'left')
            ->join('transaksi_jurnal tj', 'tj.id = ju.id_transaksi', 'left')
            ->whereIn('sa.company_id', $companyId)
            ->whereIn('ju.company_id', $companyId)
            ->where('ju.tanggal_jurnal >=', $start)
            ->where('ju.tanggal_jurnal <=', $end)
            ->where('sa.is_header', null)
            ->where('tj.deleted_at', null)
            ->where('ju.deletedAt', null)
            ->groupBy('sa.no_sub')
            ->orderBy('sa.no_sub')
            ->get()
            ->getResult();

        // ====== SPREADSHEET ======
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Neraca Saldo");

        // ====== HEADER ======
        $header = [
            'Daftar Akun', '', 'Saldo Awal Debit', 'Saldo Awal Kredit',
            'Pergerakan Debit', 'Pergerakan Kredit', 'Saldo Akhir Debit', 'Saldo Akhir Kredit'
        ];
        $sheet->fromArray($header, null, 'A1');

        // ====== ISI DATA ======
        $rowNum = 2;
        $sum = array_fill(0, 6, 0);

        foreach ($rows as $r) {
            $sheet->fromArray([
                $r->akun, "",
                number_format($r->saldo_awal_debit, 2, ',', '.'),
                number_format($r->saldo_awal_kredit, 2, ',', '.'),
                number_format($r->pergerakan_debit, 2, ',', '.'),
                number_format($r->pergerakan_kredit, 2, ',', '.'),
                number_format($r->total_debit, 2, ',', '.'),
                number_format($r->total_kredit, 2, ',', '.'),
            ], null, "A{$rowNum}");

            // SUM total tahap akhir
            $sum[0] += $r->saldo_awal_debit;
            $sum[1] += $r->saldo_awal_kredit;
            $sum[2] += $r->pergerakan_debit;
            $sum[3] += $r->pergerakan_kredit;
            $sum[4] += $r->total_debit;
            $sum[5] += $r->total_kredit;

            $rowNum++;
        }

        // ====== FOOTER TOTAL ======
        $sheet->fromArray([
            "TOTAL", "",
            number_format($sum[0],2,',','.'),
            number_format($sum[1],2,',','.'),
            number_format($sum[2],2,',','.'),
            number_format($sum[3],2,',','.'),
            number_format($sum[4],2,',','.'),
            number_format($sum[5],2,',','.'),
        ], null, "A{$rowNum}");

        // ====== AUTO SIZE ======
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ====== EXPORT ======
        $filename = "neraca_saldo_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
