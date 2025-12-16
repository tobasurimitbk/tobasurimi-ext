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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

        // var_dump($this->request->getGet());
        // die;

        if (!empty($this->request->getGet('dateStart')) && !empty($this->request->getGet('jenis_account')) && ($this->request->getGet('account_id') || $this->request->getGet('range_account_start_id'))) {
            $dataJurnalUmum = $this->getDataBukuBesar();
        }

        // var_dump($dataJurnalUmum);
        // die;
        
        $data = [
            "supplier" => $supplier,
            "divisi" => $divisi,
            "jurnalUmum" => isset($dataJurnalUmum) ? $dataJurnalUmum : []
        ];

        return view('Laporan/LaporanBukuBesar/index', $data);
    }

    public function searchAccounts() {
        $search = $this->request->getVar('search');
        $no_subs = $this->request->getVar('no_subs');
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

    public function searchAccountsBukuBesar()
    {
        $search = $this->request->getVar('search');
        $noSubs = $this->request->getVar('no_subs');
        $jenisAccount = $this->request->getVar('jenis_account');
        $ids = $this->request->getVar('ids'); // for preselected options

        $results = [];

        if ($jenisAccount === "header_account") {
            $builder = $this->HeaderAkunsModel
                ->select('header_akuns.id, header_akuns.no_header AS number, header_akuns.nama_header AS name, companies.company')
                ->where('header_akuns.deletedAt', null)
                ->where('header_akuns.company_id', $this->this_company_id)
                ->join('companies', 'companies.id = header_akuns.company_id', 'left');

            // 🔹 Selected (IDs)
            if (!empty($ids)) {
                $ids = is_array($ids) ? $ids : [$ids];
                $builder->whereIn('header_akuns.id', $ids);
            }

            // 🔹 Searching
            elseif (!empty($search)) {
                $builder->groupStart()
                    ->like('header_akuns.no_header', $search)
                    ->orLike('header_akuns.nama_header', $search)
                    ->orLike('companies.company', $search)
                    ->groupEnd();
            }

            $results = $builder->orderBy('header_akuns.no_header', 'ASC')->findAll(10);
        } else {
            // 🔹 SUB ACCOUNT
            $builder = $this->Sub_AkunsModel
                ->select('sub_akuns.id, sub_akuns.no_sub AS number, sub_akuns.nama_sub AS name, companies.company')
                ->where('sub_akuns.deletedAt', null)
                ->where('sub_akuns.company_id', $this->this_company_id)
                ->join('companies', 'companies.id = sub_akuns.company_id', 'left');

            // 🔹 Handle multiple IDs (preselected)
            if (!empty($ids)) {
                $ids = is_array($ids) ? $ids : [$ids];
                $builder->whereIn('sub_akuns.id', $ids);
            }

            // 🔹 Handle multiple no_subs (for reload selected values)
            elseif (!empty($noSubs)) {
                $noSubs = is_array($noSubs) ? $noSubs : [$noSubs];
                $builder->whereIn('sub_akuns.no_sub', $noSubs);
            }

            // 🔹 Searching (autocomplete)
            elseif (!empty($search)) {
                $builder->groupStart()
                    ->like('sub_akuns.no_sub', $search)
                    ->orLike('sub_akuns.nama_sub', $search)
                    ->orLike('companies.company', $search)
                    ->groupEnd();
            }

            $results = $builder->orderBy('sub_akuns.no_sub', 'ASC')->findAll(10);
        }

        return $this->response->setJSON($results);
    }


    private function getDataBukuBesar()
    {
        // Ambil nilai input dan lakukan validasi awal
        $dateStart = $this->request->getGet('dateStart') ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : null;
        $dateEnd = $this->request->getGet('dateEnd') ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : null;
        $divisiId = $this->request->getGet('divisi_id');
        $accountId = isset($_GET['account_id']) ? array_filter($_GET['account_id'], function ($value) {
            return $value !== "";
        }) : [];
        $rangeAccountStartId = $this->request->getGet('range_account_start_id');
        $rangeAccountFinishId = $this->request->getGet('range_account_finish_id');
        $jenisAccount = $this->request->getGet('jenis_account');
        $supplierId = $this->request->getGet('supplier_id');

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
                'company_id' => $this->this_company_id,
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
                        $key = $subAccount['no_sub'] ?? '';
                        if (!isset($result[$key])) {
                            $result[$key] = [
                                'id' => $subAccount['id'],
                                'number' => $subAccount['no_sub'],
                                'name' => $subAccount['nama_sub'],
                                'saldo_lama' => $saldoLama,
                                'result' => $resultJurnalUmum,
                            ];
                        } else {
                            $result[$key]['saldo_lama'] = $saldoLama;
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

                    $key = $headerAccount['no_header'] ?? '';
                    if (!isset($result[$key])) {
                        $result[$key] = [
                            'id' => $headerAccount['id'],
                            'number' => $headerAccount['no_header'],
                            'name' => $headerAccount['nama_header'],
                            'saldo_lama' => $saldoLama,
                            'result' => $resultJurnalUmum,
                        ];
                    } else {
                        $result[$key]['saldo_lama'] = $saldoLama;
                        $result[$key]['result'] = $mergeUniqueJurnal($result[$key]['result'], $resultJurnalUmum);
                    }
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
        // Ambil semua parameter GET
        $getParams = $this->request->getGet();
        
        if (empty($getParams)) {
            session()->setFlashdata('error', 'Silakan filter data terlebih dahulu');
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }
        
        if (empty($this->request->getGet('dateStart')) || 
            empty($this->request->getGet('jenis_account'))) {
            session()->setFlashdata('error', 'Parameter tanggal dan jenis account wajib diisi');
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }
        
        // Cek apakah ada account_id atau range_account_start_id
        $account_id = $this->request->getGet('account_id');
        $range_account_start_id = $this->request->getGet('range_account_start_id');
        
        $hasAccountId = false;
        if (is_array($account_id)) {
            $filtered = array_filter($account_id, function($val) {
                return !empty($val);
            });
            $hasAccountId = !empty($filtered);
        } else {
            $hasAccountId = !empty($account_id);
        }
        
        $hasRange = !empty($range_account_start_id);
        
        if (!$hasAccountId && !$hasRange) {
            session()->setFlashdata('error', 'Pilih minimal satu akun atau range akun');
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }
        
        // Get data
        $dataJurnalUmum = $this->getDataBukuBesar();
        
        if (empty($dataJurnalUmum)) {
            session()->setFlashdata('error', 'Tidak ada data untuk di-export');
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }

        $data = [
            "jurnalUmum" => $dataJurnalUmum,
            "dateStart" => $this->request->getGet('dateStart'),
            "dateEnd" => $this->request->getGet('dateEnd')
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanBukuBesar/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = "Laporan_Buku_Besar_" . date('Ymd_His') . ".pdf";
        $dompdf->stream($filename, array("Attachment" => false));
        exit();
    }

    public function exportExcel()
    {
        // Ambil semua parameter GET
        $getParams = $this->request->getGet();
        
        // Jika tidak ada parameter, redirect back
        if (empty($getParams)) {
            session()->setFlashdata('error', 'Silakan filter data terlebih dahulu');
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }
        
        // Validasi parameter wajib
        if (empty($this->request->getGet('dateStart')) || 
            empty($this->request->getGet('jenis_account'))) {
            session()->setFlashdata('error', 'Parameter tanggal dan jenis account wajib diisi');
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }
        
        // Cek apakah ada account_id atau range_account_start_id
        $account_id = $this->request->getGet('account_id');
        $range_account_start_id = $this->request->getGet('range_account_start_id');
        
        $hasAccountId = false;
        if (is_array($account_id)) {
            $filtered = array_filter($account_id, function($val) {
                return !empty($val);
            });
            $hasAccountId = !empty($filtered);
        } else {
            $hasAccountId = !empty($account_id);
        }
        
        $hasRange = !empty($range_account_start_id);
        
        if (!$hasAccountId && !$hasRange) {
            session()->setFlashdata('error', 'Pilih minimal satu akun atau range akun');
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }
        
        // Get data dari function yang sudah ada
        $dataJurnalUmum = $this->getDataBukuBesar();
        
        if (empty($dataJurnalUmum)) {
            session()->setFlashdata('error', 'Tidak ada data untuk di-export');
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }

        try {
            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set judul dan informasi header
            $title = "LAPORAN BUKU BESAR";
            $sheet->setCellValue('A1', $title);
            $sheet->mergeCells('A1:K1');
            
            $sheet->setCellValue('A2', 'PT. TOBA SURIMI INDUSTRIES, Tbk');
            $sheet->mergeCells('A2:K2');
            
            $periode = "Periode: " . $this->request->getGet('dateStart');
            if ($this->request->getGet('dateEnd')) {
                $periode .= " s/d " . $this->request->getGet('dateEnd');
            }
            $sheet->setCellValue('A3', $periode);
            $sheet->mergeCells('A3:K3');
            
            // Header tabel (sesuaikan dengan struktur data Anda)
            $headers = [
                'No',
                'Tanggal',
                'No. Transaksi', 
                'Company',
                'Jenis Transaksi',
                'Supplier',
                'Keterangan',
                'Currency',
                'Exchange Rate',
                'Debit',
                'Kredit',
                'Saldo'
            ];
            
            $sheet->fromArray($headers, NULL, 'A5');
            
            // Styling header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            
            $sheet->getStyle('A5:L5')->applyFromArray($headerStyle);
            
            $row = 6;
            $no = 1;
            
            // Loop melalui setiap account (sesuaikan dengan struktur data)
            foreach ($dataJurnalUmum as $j) {
                // Header untuk setiap account
                $sheet->setCellValue('A' . $row, 'Account: ' . $j['number'] . ' - ' . $j['name']);
                $sheet->mergeCells('A' . $row . ':L' . $row);
                $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F4FD']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);
                $row++;
                
                // Tampilkan saldo awal
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, '');
                $sheet->setCellValue('C' . $row, '');
                $sheet->setCellValue('D' . $row, '');
                $sheet->setCellValue('E' . $row, '');
                $sheet->setCellValue('F' . $row, '');
                $sheet->setCellValue('G' . $row, 'SALDO AWAL');
                $sheet->setCellValue('H' . $row, '');
                $sheet->setCellValue('I' . $row, '');
                $sheet->setCellValue('J' . $row, '');
                $sheet->setCellValue('K' . $row, '');
                $sheet->setCellValue('L' . $row, $j['saldo_lama']);
                
                $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']]
                ]);
                $row++;
                
                $runningBalance = $j['saldo_lama'];
                
                // Loop melalui setiap transaksi
                if (!empty($j['result'])) {
                    foreach ($j['result'] as $r) {
                        $sheet->setCellValue('A' . $row, $no++);
                        $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($r['tanggal_jurnal'])));
                        $sheet->setCellValue('C' . $row, $r['no_transaksi']);
                        $sheet->setCellValue('D' . $row, $r['company'] ?? '');
                        $sheet->setCellValue('E' . $row, $r['jenis_transaksi'] ?? '');
                        $sheet->setCellValue('F' . $row, $r['supplier_name'] ?? '');
                        $sheet->setCellValue('G' . $row, $r['keterangan']);
                        
                        // Currency dan Exchange Rate
                        $amount = (float)$r['kurs'] != 1 ? ((float)$r['kredit'] != 0 ? $r['kredit'] : $r['debit']) : 0;
                        $sheet->setCellValue('H' . $row, $r['valas'] . ' ' . $amount);
                        $sheet->setCellValue('I' . $row, $r['kurs'] == "1" ? "" : $r['kurs']);
                        
                        // Debit dan Kredit
                        $debitAmount = (float)$r['debit'] * (float)$r['kurs'];
                        $kreditAmount = (float)$r['kredit'] * (float)$r['kurs'];
                        $sheet->setCellValue('J' . $row, $debitAmount);
                        $sheet->setCellValue('K' . $row, $kreditAmount);
                        
                        // Hitung running balance
                        $runningBalance += $debitAmount - $kreditAmount;
                        $sheet->setCellValue('L' . $row, $runningBalance);
                        
                        $row++;
                    }
                }
                
                // Tambahkan baris untuk subtotal
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, '');
                $sheet->setCellValue('C' . $row, '');
                $sheet->setCellValue('D' . $row, '');
                $sheet->setCellValue('E' . $row, '');
                $sheet->setCellValue('F' . $row, '');
                $sheet->setCellValue('G' . $row, 'SUB TOTAL');
                $sheet->setCellValue('H' . $row, '');
                $sheet->setCellValue('I' . $row, '');
                $sheet->setCellValue('J' . $row, '=SUM(J' . ($row - count($j['result'])) . ':J' . ($row - 1) . ')');
                $sheet->setCellValue('K' . $row, '=SUM(K' . ($row - count($j['result'])) . ':K' . ($row - 1) . ')');
                $sheet->setCellValue('L' . $row, '');
                
                $row++;
                
                // Baris untuk total akhir
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, '');
                $sheet->setCellValue('C' . $row, '');
                $sheet->setCellValue('D' . $row, '');
                $sheet->setCellValue('E' . $row, '');
                $sheet->setCellValue('F' . $row, '');
                $sheet->setCellValue('G' . $row, 'TOTAL');
                $sheet->setCellValue('H' . $row, '');
                $sheet->setCellValue('I' . $row, '');
                $sheet->setCellValue('J' . $row, '');
                $sheet->setCellValue('K' . $row, '');
                $sheet->setCellValue('L' . $row, $runningBalance);
                
                $sheet->getStyle('G' . ($row-1) . ':G' . $row)->getFont()->setBold(true);
                
                $row += 2; // Spasi antar account
            }
            
            // Apply styling untuk data
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
            ];
            
            $lastRow = $row - 1;
            $sheet->getStyle('A5:L' . $lastRow)->applyFromArray($dataStyle);
            
            // Format number untuk kolom numeric
            $sheet->getStyle('J5:L' . $lastRow)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
            
            // Auto size columns
            foreach (range('A', 'L') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Center alignment untuk beberapa kolom
            $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J:L')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Set judul utama
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Prepare download
            $filename = 'Buku_Besar_' . date('Y_m_d_His') . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit();
            
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Excel Export Error: ' . $e->getMessage());
            
            session()->setFlashdata('error', 'Terjadi kesalahan saat generate Excel: ' . $e->getMessage());
            return redirect()->to(base_url('laporan-accounting/bukubesar'));
        }
    }
}
