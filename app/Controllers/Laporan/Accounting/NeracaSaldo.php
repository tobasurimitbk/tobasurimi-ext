<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use App\Models\JurnalUmumModel;
use Dompdf\Dompdf;
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

    public function exportPDF($tglAwal, $tglAkhir)
    {
        $dompdf = new Dompdf();
        $dateStart = $tglAwal;
        $dateEnd = $tglAkhir;

        if ($dateStart != "" && $dateEnd != "") {
            $condition = [
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
        } else {
            $condition = [
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
        }

        $dataMetadata = $this->MetadataModel
            ->asObject()
            ->where('name', 'Kelompok Akun')
            ->findAll();
        $dataKategoriAkun = $this->KategoriAkunsModel->getAPAR($this->this_company_id);
        $dataHeaderAkun = $this->HeaderAkunsModel->getAPAR($this->this_company_id);
        $dataSubAkun = $this->Sub_AkunsModel->getAPAR($this->this_company_id);
        // $dataJurnalUmum = $this->jurnalUmumModel->getDataJurnal($condition);
        $dataJurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->where($condition)
            ->findAll();
        $dataJurnalUmumWithGroup = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->where($condition)
            ->groupBy('kategori_id')
            ->findAll();
        $dataJurnalUmumWithGroupHeader = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->where($condition)
            ->groupBy('id_header')
            ->findAll();

        $data = [
            "dataMetadata" => $dataMetadata,
            "dataKategoriAkun" => $dataKategoriAkun,
            "dataHeaderAkun" => $dataHeaderAkun,
            "dataSubAkuns" => $dataSubAkun,
            "dataJurnalUmum" => $dataJurnalUmum,
            "dataJurnalUmumWithGroup" => $dataJurnalUmumWithGroup,
            "dataJurnalUmumWithGroupHeader" => $dataJurnalUmumWithGroupHeader,
            "dateStart" => $dateStart ? date("d/m/Y", strtotime($dateStart)) : date('d/m/Y'),
            "dateEnd" => $dateEnd ? date("d/m/Y", strtotime($dateEnd)) : date('d/m/Y'),
        ];
        $dompdf->loadHtml(view('Laporan/LaporanNeracaSaldo/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Jurnal Umum ", array("Attachment" => false));

        exit(0);
    }
}
