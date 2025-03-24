<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use App\Models\JurnalUmumModel;
use Dompdf\Dompdf;

class BukuBesar extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $MetadataModel;
    protected $divisiModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_role_id = session()->get("login")->this_role_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->KategoriAkunsModel = new KategoriAkunsModel();
        $this->HeaderAkunsModel = new HeaderAkunsModel();
        $this->MetadataModel = new MetadataModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->divisiModel = new DivisisModel();
    }
    public function index()
    {
        $divisi = $this->divisiModel->getDivisiAccess();
        $account = [];

        if (@$_POST['jenis_account'] == "header_account") {
            $dataHeaderAccount = $this->HeaderAkunsModel->where('company_id', $this->this_company_id)->findAll();
            foreach ($dataHeaderAccount as $d) {
                array_push($account, [
                    'id' => $d['id'],
                    'number' => $d['no_header'],
                    'name' => $d['nama_header']
                ]);
            }
        } else {
            $dataSubAccount =  $this->Sub_AkunsModel->where('company_id', $this->this_company_id)->findAll();
            foreach ($dataSubAccount as $d) {
                array_push($account, [
                    'id' => $d['id'],
                    'number' => $d['no_sub'],
                    'name' => $d['nama_sub']
                ]);
            }
        }


        if (!empty($this->request->getPost('dateStart')) && !empty($this->request->getPost('jenis_account')) && ($this->request->getPost('account_id') || $this->request->getPost('range_account_start_id'))) {
            $dataJurnalUmum = $this->getDataBukuBesar();
        }

        $data = [
            "account" => $account,
            "divisi" => $divisi,
            "jurnalUmum" => isset($dataJurnalUmum) ? $dataJurnalUmum : []
        ];

        return view('Laporan/LaporanBukuBesar/index', $data);
    }

    private function getDataBukuBesar()
    {
        // Ambil nilai input dan lakukan validasi awal
        $dateStart = $this->request->getPost('dateStart') ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("dateStart")))) : null;
        $dateEnd = $this->request->getPost('dateEnd') ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("dateEnd")))) : null;
        $divisiId = $this->request->getPost('divisi_id');
        $accountId = isset($_POST['account_id']) ? array_filter($_POST['account_id'], function ($value) {
            return $value !== "";
        }) : [];
        $rangeAccountStartId = $this->request->getPost('range_account_start_id');
        $rangeAccountFinishId = $this->request->getPost('range_account_finish_id');
        $jenisAccount = $this->request->getPost('jenis_account');


        $result = [];
        
        if ($this->this_company_id == 1 || $this->this_company_id == 2) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $condition = [];
        if ($this->this_role_id != '7') {
            $condition['jurnal_umum.id_transaksi !='] = '1404';
            $condition['transaksi_jurnal.type_transaksi !='] = '1404';
        }

        // Fungsi umum untuk mendapatkan saldo lama dan data jurnal
        $fetchJurnalData = function ($coaIds) use ($dateStart, $dateEnd, $divisiId, $companyId, $condition) {
            // Query dasar
            $dataJurnalUmum = $this->jurnalUmumModel
                ->select('
                    jurnal_umum.*, 
                    transaksi_jurnal.no_transaksi, 
                    transaksi_jurnal.id AS transaksi_jurnal_id, 
                    m_valas.value AS valas, 
                    m_jenis_transaksi.value AS jenis_transaksi
                ')
                ->join('transaksi_jurnal', 'transaksi_jurnal.id = jurnal_umum.id_transaksi', 'left')
                ->join('metadata AS m_valas', 'm_valas.id = jurnal_umum.valas', 'left')
                ->join('metadata AS m_jenis_transaksi', 'm_jenis_transaksi.id = transaksi_jurnal.type_transaksi', 'left')
                ->where('transaksi_jurnal.deleted_at', null)
                ->where('jurnal_umum.deletedAt', null)
                ->where($condition)
                ->whereIn('jurnal_umum.company_id', $companyId)
                ->orderBy('jurnal_umum.tanggal_jurnal', "ASC")
                ->orderBy('jurnal_umum.debit', "DESC");


            // Filter berdasarkan tanggal
            if ($dateStart) {
                $dataJurnalUmum->where('tanggal_jurnal >=', $dateStart);
            }
            if ($dateEnd) {
                $dataJurnalUmum->where('tanggal_jurnal <=', $dateEnd);
            }

            // Filter berdasarkan divisi
            if (!empty($divisiId)) {
                $dataJurnalUmum->where('divisi_id', $divisiId);
            }


            $resultJurnalUmum = $dataJurnalUmum->whereIn('id_coa', $coaIds)->findAll();
            $saldoLama = $this->jurnalUmumModel->getTotalSaldoLama([
                'tanggal_awal' => $dateStart,
                'id_coa' => $coaIds
            ]);
            return [$resultJurnalUmum, $saldoLama];
        };

        // Proses berdasarkan account ID atau range account
        if (!empty($accountId)) {
            foreach ($accountId as $a) {
                if ($jenisAccount == "sub_account") {
                    $subAccount = $this->Sub_AkunsModel->find($a);
                    [$resultJurnalUmum, $saldoLama] = $fetchJurnalData([$a]);
                    $result[] = [
                        'id' => $subAccount['id'],
                        'number' => $subAccount['no_sub'],
                        'name' => $subAccount['nama_sub'],
                        'saldo_lama' => $saldoLama,
                        'result' => $resultJurnalUmum,
                    ];
                } else {
                    $headerAccount = $this->HeaderAkunsModel->find($a);
                    $subAccountIds = $this->Sub_AkunsModel->where('header_id', $headerAccount['id'])->findColumn('id');
                    [$resultJurnalUmum, $saldoLama] = $fetchJurnalData($subAccountIds);
                    $result[] = [
                        'id' => $headerAccount['id'],
                        'number' => $headerAccount['no_header'],
                        'name' => $headerAccount['nama_header'],
                        'saldo_lama' => $saldoLama,
                        'result' => $resultJurnalUmum,
                    ];
                }
            }
        } elseif (!empty($rangeAccountStartId) && !empty($rangeAccountFinishId)) {
            $model = $jenisAccount == "sub_account" ? $this->Sub_AkunsModel : $this->HeaderAkunsModel;
            $accounts = $model->where('id >=', $rangeAccountStartId)
                ->where('id <=', $rangeAccountFinishId)
                ->findAll();
            foreach ($accounts as $account) {
                if ($jenisAccount == "sub_account") {
                    [$resultJurnalUmum, $saldoLama] = $fetchJurnalData([$account['id']]);
                    $result[] = [
                        'id' => $account['id'],
                        'number' => $account['no_sub'],
                        'name' => $account['nama_sub'],
                        'saldo_lama' => $saldoLama,
                        'result' => $resultJurnalUmum,
                    ];
                } else {
                    $subAccountIds = $this->Sub_AkunsModel->where('header_id', $account['id'])->findColumn('id');
                    [$resultJurnalUmum, $saldoLama] = $fetchJurnalData($subAccountIds);
                    $result[] = [
                        'id' => $account['id'],
                        'number' => $account['no_header'],
                        'name' => $account['nama_header'],
                        'saldo_lama' => $saldoLama,
                        'result' => $resultJurnalUmum,
                    ];
                }
            }
        }

        return $result;
    }


    public function dropdownAccount()
    {
        $jenisAccount = $this->request->getVar('jenis_account');
        $account = [];

        if ($jenisAccount == "sub_account") {
            // Sub Account
            $dataSubAccount = $this->Sub_AkunsModel->where('company_id', $this->this_company_id)->findAll();
            foreach ($dataSubAccount as $d) {
                array_push($account, [
                    'id' => $d['id'],
                    'number' => $d['no_sub'],
                    'name' => $d['nama_sub']
                ]);
            }
        } else {
            // Header Account
            $dataHeaderAccount = $this->HeaderAkunsModel->where('company_id', $this->this_company_id)->findAll();
            foreach ($dataHeaderAccount as $d) {
                array_push($account, [
                    'id' => $d['id'],
                    'number' => $d['no_header'],
                    'name' => $d['nama_header']
                ]);
            }
        }

        return response()->setJSON([
            'data' => $account,
            'status' => true
        ]);
    }

    public function indexBackup()
    {
        $dateStart = $this->request->getPost('dateStart');
        $dateEnd = $this->request->getPost('dateEnd');

        if ($dateStart != "" && $dateEnd != "") {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
        } else {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
        }

        $dataMetadata = $this->MetadataModel
            ->asObject()
            ->where('name', 'Kelompok Akun')
            ->groupStart()
            ->like('value', 'Pendapatan')
            ->orLike('value', 'Beban')
            ->groupEnd()
            ->findAll();
        $dataKategoriAkun = $this->KategoriAkunsModel->getAPAR($this->this_company_id);
        $dataHeaderAkun = $this->HeaderAkunsModel->getAPAR($this->this_company_id);
        $dataSubAkun = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $dataJurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition);

        if (isset($_POST['id_sub_akun'])) {
            foreach ($_POST['id_sub_akun'] as $i) {
                $dataJurnalUmum->orWhere('id_coa', decrypt($i));
            }
        }

        if (isset($_POST['id_header']) && @$_POST['id_header'] != "") {
            $dataJurnalUmum->where('sub_akuns.header_id', decrypt($_POST['id_header']));
        }

        $dataJurnalUmumResult =  $dataJurnalUmum->findAll();

        $dataJurnalUmumWithGroup = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition);

        if (isset($_POST['id_sub_akun'])) {
            foreach ($_POST['id_sub_akun'] as $i) {
                $dataJurnalUmumWithGroup->where('id_coa', decrypt($i));
            }
        }

        if (isset($_POST['id_header']) && @$_POST['id_header'] != "") {
            $dataJurnalUmumWithGroup->where('sub_akuns.header_id', decrypt($_POST['id_header']));
        }

        $dataJurnalUmumWithGroupResult = $dataJurnalUmumWithGroup->groupBy('id_header')->findAll();

        $data = [
            "dataMetadata" => $dataMetadata,
            "dataKategoriAkun" => $dataKategoriAkun,
            "dataHeaderAkun" => $dataHeaderAkun,
            "dataSubAkuns" => $dataSubAkun,
            "dataJurnalUmum" => $dataJurnalUmumResult,
            "dataJurnalUmumWithGroup" => $dataJurnalUmumWithGroupResult,
            "dateEnd" => $dateEnd ? $dateEnd : date('d/m/Y'),
        ];
        return view('Laporan/LaporanBukuBesar/index', $data);
    }

    public function exportPDFBackup()
    {
        $dompdf = new Dompdf();
        $dateStart = $this->request->getPost('dateStart');
        $dateEnd = $this->request->getPost('dateEnd');

        if ($dateStart != "" && $dateEnd != "") {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
        } else {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
        }

        $dataMetadata = $this->MetadataModel
            ->asObject()
            ->where('name', 'Kelompok Akun')
            ->groupStart()
            ->like('value', 'Pendapatan')
            ->orLike('value', 'Beban')
            ->groupEnd()
            ->findAll();
        $dataKategoriAkun = $this->KategoriAkunsModel->getAPAR($this->this_company_id);
        $dataHeaderAkun = $this->HeaderAkunsModel->getAPAR($this->this_company_id);
        $dataSubAkun = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $dataJurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition);

        if (isset($_POST['id_sub_akun'])) {
            foreach ($_POST['id_sub_akun'] as $i) {
                $dataJurnalUmum->orWhere('id_coa', decrypt($i));
            }
        }

        if (isset($_POST['id_header']) && @$_POST['id_header'] != "") {
            $dataJurnalUmum->where('sub_akuns.header_id', decrypt($_POST['id_header']));
        }

        $dataJurnalUmumResult =  $dataJurnalUmum->findAll();

        $dataJurnalUmumWithGroup = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition);

        if (isset($_POST['id_sub_akun'])) {
            foreach ($_POST['id_sub_akun'] as $i) {
                $dataJurnalUmumWithGroup->where('id_coa', decrypt($i));
            }
        }

        if (isset($_POST['id_header']) && @$_POST['id_header'] != "") {
            $dataJurnalUmumWithGroup->where('sub_akuns.header_id', decrypt($_POST['id_header']));
        }

        $dataJurnalUmumWithGroupResult = $dataJurnalUmumWithGroup->groupBy('id_header')->findAll();

        $data = [
            "dataMetadata" => $dataMetadata,
            "dataKategoriAkun" => $dataKategoriAkun,
            "dataHeaderAkun" => $dataHeaderAkun,
            "dataSubAkuns" => $dataSubAkun,
            "dataJurnalUmum" => $dataJurnalUmumResult,
            "dataJurnalUmumWithGroup" => $dataJurnalUmumWithGroupResult,
            "dateStart" => $dateStart ? date("d/m/Y", strtotime($dateStart)) : date('d/m/Y'),
            "dateEnd" => $dateEnd ? date("d/m/Y", strtotime($dateEnd)) : date('d/m/Y'),
        ];
        $dompdf->loadHtml(view('Laporan/LaporanBukuBesar/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Jurnal Umum ", array("Attachment" => false));

        exit(0);
    }

    public function exportPDF()
    {
        if (!empty($this->request->getPost('dateStart')) && !empty($this->request->getPost('jenis_account')) && ($this->request->getPost('account_id') || $this->request->getPost('range_account_start_id'))) {
            $dataJurnalUmum = $this->getDataBukuBesar();
        }

        $data = [
            "jurnalUmum" => isset($dataJurnalUmum) ? $dataJurnalUmum : []
        ];

        $dompdf = new Dompdf();

        $dompdf->loadHtml(view('Laporan/LaporanBukuBesar/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Jurnal Umum ", array("Attachment" => false));
    }
}
