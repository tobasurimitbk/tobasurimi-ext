<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use App\Models\JurnalUmumModel;
use App\Models\SupplierModel;
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
    protected $this_role_id;
    protected $supplierModel;

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
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        $divisi = $this->divisiModel->getDivisiAccess();
        $supplier = $this->supplierModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();

        if (!empty($this->request->getPost('dateStart')) && !empty($this->request->getPost('jenis_account')) && ($this->request->getPost('account_id') || $this->request->getPost('range_account_start_id'))) {
            $dataJurnalUmum = $this->getDataBukuBesar();
        }
        
        $data = [
            "supplier" => $supplier,
            "divisi" => $divisi,
            "jurnalUmum" => isset($dataJurnalUmum) ? $dataJurnalUmum : []
        ];

        return view('Laporan/LaporanBukuBesar/index', $data);
    }

    public function searchAccounts() {
        $search = $this->request->getVar('search');
        $jenisAccount = $this->request->getVar('jenis_account');
        $ids = $this->request->getVar('ids'); // For handling selected options
        
        $results = [];
        
        if ($jenisAccount == "header_account") {
            $headerBuilder = $this->HeaderAkunsModel
                ->select('header_akuns.id, header_akuns.no_header as number, header_akuns.nama_header as name, companies.company')
                ->where('header_akuns.deletedAt', null)
                ->join('companies', 'companies.id = header_akuns.company_id', 'left')
                ->where('header_akuns.company_id', $this->this_company_id);
            
            // If IDs are provided (for selected options)
            if (!empty($ids)) {
                $ids = is_array($ids) ? $ids : [$ids];
                $headerBuilder->whereIn('header_akuns.id', $ids);
                $results = $headerBuilder->orderBy('header_akuns.no_header', 'ASC')->findAll();
            } 
            // If searching
            else if (!empty($search)) {
                $headerBuilder->groupStart()
                    ->like('header_akuns.no_header', $search)
                    ->orLike('header_akuns.nama_header', $search)
                    ->orLike('companies.company', $search)
                    ->groupEnd();
                $results = $headerBuilder->orderBy('header_akuns.no_header', 'ASC')->findAll(10);
            }
        } else {
            $subBuilder = $this->Sub_AkunsModel
                ->select('sub_akuns.id, sub_akuns.no_sub as number, sub_akuns.nama_sub as name, companies.company')
                 ->where('sub_akuns.deletedAt', null)
                ->join('companies', 'companies.id = sub_akuns.company_id', 'left')
                ->where('sub_akuns.company_id', $this->this_company_id);
            
            // If IDs are provided (for selected options)
            if (!empty($ids)) {
                $ids = is_array($ids) ? $ids : [$ids];
                $subBuilder->whereIn('sub_akuns.id', $ids);
                $results = $subBuilder->orderBy('sub_akuns.no_sub', 'ASC')->findAll();
            } 
            // If searching
            else if (!empty($search)) {
                $subBuilder->groupStart()
                    ->like('sub_akuns.no_sub', $search)
                    ->orLike('sub_akuns.nama_sub', $search)
                    ->orLike('companies.company', $search)
                    ->groupEnd();
                $results = $subBuilder->orderBy('sub_akuns.no_sub', 'ASC')->findAll(10);
            }
        }
        
        return $this->response->setJSON($results);
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
        $supplierId = $this->request->getPost('supplier_id');

        $result = [];

        // company scope / company filter (sesuai logika awal)
        if ($this->this_company_id == 1 || $this->this_company_id == 2) {
            $companyId = [1, 2];
            $companyScope = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
            $companyScope = [15];
        } else {
            $companyId = [16];
            $companyScope = [16];
        }

        // Kondisi dasar untuk query jurnal
        $condition = [];
        if ($this->this_role_id != '7') {
            $condition['jurnal_umum.id_transaksi !='] = '1404';
            $condition['transaksi_jurnal.type_transaksi !='] = '1404';
        }
        if (!empty($supplierId)) {
            $condition['jurnal_umum.supplier_id'] = $supplierId;
        }

        // Helper: merge array hasil jurnal unik berdasarkan jurnal_umum.id, lalu sort by no_transaksi
        $mergeUniqueJurnal = function (array $existing, array $additional) {
            $map = [];
            foreach ($existing as $r) {
                if (isset($r['id'])) $map[$r['id']] = $r;
            }
            foreach ($additional as $r) {
                if (isset($r['id']) && !isset($map[$r['id']])) $map[$r['id']] = $r;
            }
            $out = array_values($map);
            usort($out, function ($a, $b) {
                return strcmp($a['no_transaksi'] ?? '', $b['no_transaksi'] ?? '');
            });
            return $out;
        };

        // Fungsi umum untuk mendapatkan saldo lama dan data jurnal untuk daftar id_coa tertentu
        $fetchJurnalData = function (array $coaIds) use ($dateStart, $dateEnd, $divisiId, $companyId, $condition) {
            if (empty($coaIds)) {
                return [[], 0];
            }

            // Query dasar
            $dataJurnalUmum = $this->jurnalUmumModel
                ->select('
                    jurnal_umum.*, 
                    companies.company,
                    transaksi_jurnal.no_transaksi, 
                    suppliers.name as supplier_name, 
                    transaksi_jurnal.id AS transaksi_jurnal_id, 
                    m_valas.value AS valas, 
                    m_jenis_transaksi.value AS jenis_transaksi
                ')
                ->join('transaksi_jurnal', 'transaksi_jurnal.id = jurnal_umum.id_transaksi', 'left')
                ->join('suppliers', 'suppliers.id = jurnal_umum.supplier_id', 'left')
                ->join('metadata AS m_valas', 'm_valas.id = jurnal_umum.valas', 'left')
                ->join('metadata AS m_jenis_transaksi', 'm_jenis_transaksi.id = transaksi_jurnal.type_transaksi', 'left')
                ->join('companies', 'companies.id = jurnal_umum.company_id', 'left')
                ->where('transaksi_jurnal.deleted_at', null)
                ->where('jurnal_umum.deletedAt', null)
                ->where($condition)
                ->whereIn('jurnal_umum.company_id', $companyId)
                ->orderBy('transaksi_jurnal.no_transaksi', "ASC");

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

            $resultJurnalUmum = $dataJurnalUmum->whereIn('id_coa', $coaIds)->findAll() ?: [];

            // Hitung saldo lama (fungsi model tetap dipanggil dengan array id_coa)
            $saldoLama = $this->jurnalUmumModel->getTotalSaldoLama([
                'tanggal_awal' => $dateStart,
                'id_coa' => $coaIds
            ]);

            return [$resultJurnalUmum, $saldoLama];
        };

        // Proses berdasarkan account ID (sub_account atau header)
        if (!empty($accountId)) {
            foreach ($accountId as $a) {
                if ($jenisAccount == "sub_account") {
                    // Ambil semua sub akun dengan no_sub sesuai dan dalam company scope
                    $subAccounts = $this->Sub_AkunsModel
                                        ->where('sub_akuns.no_sub', $a)
                                        ->where('sub_akuns.deletedAt', null)
                                        ->whereIn('sub_akuns.company_id', $companyScope)
                                        ->join('companies', 'companies.id = sub_akuns.company_id', 'left')
                                        ->select('sub_akuns.*, companies.company')
                                        ->findAll();

                    if (empty($subAccounts)) {
                        continue;
                    }

                    // IMPORTANT: ambil jurnal per sub-account (jangan reuse jurnal gabungan untuk semua sub account)
                    foreach ($subAccounts as $subAccount) {
                        $subId = $subAccount['id'];
                        [$resultJurnalUmum, $saldoLama] = $fetchJurnalData([$subId]);

                        // Gunakan key komposit supaya akun serupa di company berbeda tidak tercampur
                        $key = ($subAccount['company'] ?? 'company') . '|' . ($subAccount['nama_sub'] ?? '') . '|' . ($subAccount['no_sub'] ?? '');

                        if (!isset($result[$key])) {
                            $result[$key] = [
                                'id' => $subAccount['id'],
                                'number' => $subAccount['no_sub'],
                                'company' => $subAccount['company'] ?? null,
                                'name' => $subAccount['nama_sub'],
                                'saldo_lama' => $saldoLama,
                                'result' => $resultJurnalUmum,
                            ];
                        } else {
                            // tambahkan saldo dan merge hasil jurnal unik
                            $result[$key]['saldo_lama'] += $saldoLama;
                            $result[$key]['result'] = $mergeUniqueJurnal($result[$key]['result'], $resultJurnalUmum);
                        }
                    }
                } else {
                    // Header account
                    $headerAccount = $this->HeaderAkunsModel
                                        ->where('header_akuns.id', $a)
                                        ->where('header_akuns.deletedAt', null)
                                        ->join('companies', 'companies.id = header_akuns.company_id', 'left')
                                        ->select('header_akuns.*, companies.company')
                                        ->first();
                    if ($headerAccount === null) {
                        continue;
                    }

                    // Ambil sub account id yang terkait dengan header ini tetapi **hanya dalam company scope**
                    $subAccountIds = $this->Sub_AkunsModel
                                        ->where('header_id', $headerAccount['id'])
                                        ->where('sub_akuns.deletedAt', null)
                                        ->whereIn('sub_akuns.company_id', $companyScope)
                                        ->findColumn('id') ?: [];

                    if (empty($subAccountIds)) {
                        // tidak ada sub account untuk header tersebut dalam company scope
                        continue;
                    }

                    // Ambil jurnal sekaligus untuk semua subAccount di header ini (agregat per header)
                    [$resultJurnalUmum, $saldoLama] = $fetchJurnalData($subAccountIds);

                    $key = ($headerAccount['company'] ?? 'company') . '|' . ($headerAccount['nama_header'] ?? '') . '|' . ($headerAccount['no_header'] ?? '');

                    if (!isset($result[$key])) {
                        $result[$key] = [
                            'id' => $headerAccount['id'],
                            'number' => $headerAccount['no_header'],
                            'company' => $headerAccount['company'] ?? null,
                            'name' => $headerAccount['nama_header'],
                            'saldo_lama' => $saldoLama,
                            'result' => $resultJurnalUmum,
                        ];
                    } else {
                        $result[$key]['saldo_lama'] += $saldoLama;
                        $result[$key]['result'] = $mergeUniqueJurnal($result[$key]['result'], $resultJurnalUmum);
                    }
                }
            }
        }

        // var_dump($result);
        // die;
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
