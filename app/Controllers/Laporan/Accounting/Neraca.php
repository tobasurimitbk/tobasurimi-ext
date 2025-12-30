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

class Neraca extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $MetadataModel;
    protected $encrypter;

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
    }
    
    public function index()
    {
        return view('Laporan/LaporanNeraca/index', [
            "dateStart" => $this->request->getGet('dateStart') ?: date('01/m/Y'),
            "dateEnd"   => $this->request->getGet('dateEnd')   ?: date('d/m/Y'),
        ]);
    }

    /**
     * Ambil data neraca server-side tanpa limit
     */
    public function getData()
    {
        $dateStart = $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "";
        $dateEnd = $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "";
        
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

        if (!empty($dateStart)) {
            $condition = [
                'jurnal_umum.company_id' => $companyId,
                'tanggal_jurnal >=' => $dateStart,
                'tanggal_jurnal <=' => $dateEnd,
            ];
        } else {
            $condition = [
                'jurnal_umum.company_id' => $companyId,
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
        }

        $dataMetadata     = $this->MetadataModel
            ->asObject()
            ->where('name', 'Kelompok Akun')
            ->groupStart()
                ->like('value', 'Aktiva')
                ->orLike('value', 'Kewajiban')
                ->orLike('value', 'Modal')
            ->groupEnd()
            ->findAll();

        $dataKategoriAkun = $this->KategoriAkunsModel->getAPAR($companyId);
        $dataSubAkuns     = $this->Sub_AkunsModel->getAPAR($companyId);
        $dataJurnal       = $this->jurnalUmumModel->getDataJurnal($condition);

        // var_dump($dataMetadata, $dataKategoriAkun, $dataSubAkuns, $dataJurnal);
        // exit;
        
        /** ==== HITUNG SALDO ==== **/
        $output = [];
        $total_kelompok = [];

        foreach ($dataMetadata as $kelompok) {
            $output[] = ["type"=>"kelompok","name"=>$kelompok->value,"nominal"=>""];

            $total_kelompok[$kelompok->value] = 0;

            foreach ($dataKategoriAkun as $kategori) {
                // cek apakah kategori ini termasuk kelompok
                if (!in_array($kelompok->id, $kategori['kelompok_ids'])) continue;

                $total_kategori = 0;
                $sub_rows = []; // tampung row sub terlebih dahulu

                foreach ($dataSubAkuns as $sub) {
                    // cek apakah sub-akun ini termasuk kategori saat ini
                    if (!array_intersect($sub['kategori_ids'], $kategori['ids'])) continue;

                    $saldo = 0;
                    foreach ($dataJurnal as $j) {
                        if (!empty($sub['ids']) && in_array($j->id_coa, $sub['ids'])) {
                            // hitung saldo
                            $saldo += stripos($kelompok->value, "Aktiva") !== false
                                ? $j->debit - $j->kredit
                                : $j->kredit - $j->debit;
                        }
                    }

                    $saldo = max(0, $saldo);
                    if ($saldo != 0) {
                        $sub_rows[] = [
                            "type"   => "sub",
                            "name"   => $sub['no_sub']." - ".$sub['nama_sub'],
                            "nominal"=> $saldo
                        ];
                        $total_kategori += $saldo;
                    }
                }

                // === JANGAN OUTPUT KATEGORI KALAU TOTAL KOSONG ===
                if ($total_kategori == 0) continue;

                // tampilkan kategori
                $output[] = [
                    "type"   => "kategori",
                    "name"   => $kategori['no_kategori']." - ".$kategori['nama_kategori'],
                    "nominal"=> ""
                ];

                // tampilkan semua sub yg ada saldonya
                foreach ($sub_rows as $sr) {
                    $output[] = $sr;
                }

                // tampilkan total kategori
                $output[] = [
                    "type"   => "total_kategori",
                    "name"   => "Total ".$kategori['nama_kategori'],
                    "nominal"=> $total_kategori
                ];

                $total_kelompok[$kelompok->value] += $total_kategori;
            }

            $output[] = ["type"=>"total_kelompok","name"=>"Total ".$kelompok->value,"nominal"=>$total_kelompok[$kelompok->value]];
        }

        return $this->response->setJSON([
            "data" => $output
        ]);
    }

    /**
     * ===== EXPORT EXCEL =====
     */
    public function exportExcel()
    {
        // Ambil parameter tanggal dari GET
        $dateStart = $this->request->getGet("dateStart") ?: date('d/m/Y', strtotime('first day of this month'));
        $dateEnd   = $this->request->getGet("dateEnd") ?: date('d/m/Y');

        // Set $_GET agar getData() bisa pakai
        $_GET['dateStart'] = $dateStart;
        $_GET['dateEnd']   = $dateEnd;

        // Ambil data
        $response = $this->getData();
        $data = json_decode($response->getBody(), true)['data'];

        // Buat spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue("A1", "LAPORAN NERACA");
        $sheet->mergeCells("A1:B1");

        $row = 3;
        foreach ($data as $d) {
            // Hilangkan HTML tags
            $sheet->setCellValue("A$row", strip_tags($d['name']));

            // Untuk sub/total kategori/total kelompok masukkan angka saja
            if (in_array($d['type'], ['sub', 'total_kategori', 'total_kelompok'])) {
                $sheet->setCellValue("B$row", $d['nominal']);

                // Set format currency di Excel (tanpa Rp, bisa dihitung)
                $sheet->getStyle("B$row")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');
            }

            $row++;
        }

        // Auto width
        foreach(range('A','B') as $col){
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Laporan-Neraca-" . date("YmdHis") . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save("php://output");
        exit;
    }
}
