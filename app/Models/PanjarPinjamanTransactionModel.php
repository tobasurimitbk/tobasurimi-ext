<?php

namespace App\Models;

use CodeIgniter\Model;

class PanjarPinjamanTransactionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'panjar_pinjaman_transaction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'type',
        'supplier_id',
        'company_id',
        'bank_id',
        'divisi_id',
        'no_transaction',
        'is_posted',
        'keterangan'
    ];
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function getPanjarPinjamanSupplierList($addCondition, $condition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_transaction' => 'ppt.no_transaction',
            'createdAt'      => 'ppt.createdAt',
            'type'           => 'ppt.type',
            'updatedAt'      => 'ppt.updatedAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort     = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'ppt.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $builder = $this->db->table('panjar_pinjaman_transaction ppt');
        $builder->select('ppt.*, suppliers.name as supplier_name, 
            COALESCE(ps.total_pinjaman, 0) as total_pinjaman,
            COALESCE(pjs.total_panjar, 0) as total_panjar');

        // Join suppliers
        $builder->join('suppliers', 'ppt.supplier_id = suppliers.id', 'left');

        $builder->join(
            '(SELECT transaction_id, SUM(total_pinjaman) AS total_pinjaman 
              FROM pinjaman_supplier 
              WHERE deletedAt IS NULL 
              GROUP BY transaction_id) ps',
            'ps.transaction_id = ppt.id',
            'left'
        );

        $builder->join(
            '(SELECT transaction_id, SUM(total_panjar) AS total_panjar 
              FROM panjar_supplier 
              WHERE deletedAt IS NULL 
              GROUP BY transaction_id) pjs',
            'pjs.transaction_id = ppt.id',
            'left'
        );

        // Filtering dari parameter $condition
        $builder->where($condition);

        // Filter tambahan
        if (!empty($addCondition['status'])) {
            if ($addCondition['status'] == 'ALL') {
                $builder->whereIn('is_posted', ['0', '1']);
            } else {
                $status = $addCondition['status'] == "NOT_POSTING" ? '0' : '1';
                $builder->where('is_posted', $status);
            }
        }

        if (!empty($addCondition['search'])) {
            $builder->like('ppt.no_transaction', $addCondition['search']);
        }

        if (!empty($addCondition['dateStart'])) {
            $builder->where('ppt.createdAt >=', $addCondition['dateStart']);
        }

        if (!empty($addCondition['dateEnd'])) {
            $builder->where('ppt.createdAt <=', $addCondition['dateEnd']);
        }

        $totalData = $builder->countAllResults(false); // total semua

        $builder->orderBy($sort, $sortType);
        $data = $builder->get($limit, $offset)->getResult();

        $totalFilteredData = $totalData; // jika pakai search lebih kompleks, bisa dihitung ulang

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getPanjarPinjamanSupplierbyID($id)
    {
        $selectQry = "panjar_pinjaman_transaction.*,suppliers.name as supplier_name, suppliers.type as supplier_type";
        $panjarPinjamanSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_pinjaman_transaction.supplier_id = suppliers.id', 'left')
            ->find($id);
        return $panjarPinjamanSupplierData;
    }

    public function getNumber($companyId)
    {
        $month = date('m'); // Bulan saat ini (format: 01-12)
        $year = date('Y'); // Tahun saat ini (format: 2023)
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d'))); // Tanggal terakhir bulan ini

        $lastStr = convertBulanToAngkaRomawi($month) . '/' . $year; // Format: III/2023

        // Ambil no_transaction terakhir di bulan & tahun ini
        $builder = $this->asArray()->select('no_transaction')
            ->orderBy('no_transaction', "DESC")
            ->where('company_id', $companyId)
            ->where('createdAt >=', $year . "-" . $month . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59")
            ->first();

        $kode = 'PJR'; // Kode awal: PJR
        $lastNumber = 1; // Nomor awal: 1

        if ($builder != null && isset($builder['no_transaction'])) {
            $explode = explode('/', $builder['no_transaction']); // Pecah no_transaction menjadi array

            // Pastikan format no_transaction sesuai: PJR/X/2023/00001
            if (count($explode) == 4) {
                $numberStr = $explode[3]; // Ambil bagian nomor (00001)
                $number = intval($numberStr); // Konversi ke integer
                if ($number >= $lastNumber) {
                    $lastNumber = $number + 1; // Increment nomor terakhir
                }
            }
        }

        $formattedlastNumber = sprintf("%05d", $lastNumber); // Format nomor menjadi 5 digit (00001)
        $generatedNo = $kode . '/' . $lastStr . '/' . $formattedlastNumber; // Gabungkan semua bagian

        return $generatedNo;
    }

    public function get_new_no($jenis, $divisi, $bank_id, $bln, $thn, $last_day, $companyID)
    {
        $banksModel = new BanksModel();
        
        // Step 1: Dapatkan kode bank dari database
        $kodeBank = '';
        if (!empty($bank_id)) {
            $bankData = $banksModel->select('name')
                                ->where('id', $bank_id)
                                ->first();
            if ($bankData) {
                $name = strtoupper($bankData['name']);
                if (strpos($name, 'BRI') !== false) {
                    $kodeBank = 'BRI';
                } elseif (strpos($name, 'MANDIRI') !== false) {
                    $kodeBank = 'MND';
                } elseif (strpos($name, 'BNI') !== false) {
                    $kodeBank = 'KBA';
                } elseif (strpos($name, 'BCA') !== false) {
                    $kodeBank = 'BCI';
                }
            }
        }

        // Step 2: Tentukan kode divisi
        $kodeDivisi = '';
        if (!empty($divisi)) {
            $divisiUpper = strtoupper($divisi);
            if (strpos($divisiUpper, 'PTS') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'MKN' : 'KKN';
            } elseif (strpos($divisiUpper, 'CANNING') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'CNM' : 'CNK';
            } elseif (strpos($divisiUpper, 'FROZEN I') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'FRM' : 'FRK';
            } elseif (strpos($divisiUpper, 'FROZEN II') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'FSM' : 'FSK';
            } elseif (strpos($divisiUpper, 'GLOBAL') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'GBM' : 'GBK';
            } elseif (strpos($divisiUpper, 'OCS') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'OCM' : 'OCK';
            }
        }

        // Step 3: Gabungkan kode divisi dan kode bank
        $prefix = '';
        if (!empty($kodeDivisi)) {
            $prefix .= $kodeDivisi . '/';
        }
        if (!empty($kodeBank)) {
            $prefix .= $kodeBank . '/';
        }

        // Step 4: Tambahkan tahun, bulan, dan nomor urut
        $prefix .= $thn . '/' . $bln . '/';

        // Step 5: Query nomor terakhir dan generate nomor baru
        $lastPO = $this->select('no_transaction')
                    ->like('no_transaction', $prefix)
                    ->where('createdAt >=', "{$thn}-{$bln}-01 00:00:00")
                    ->where('createdAt <=', "{$last_day} 23:59:59")
                    ->where('company_id', $companyID)
                    ->where('deletedAt', null)
                    ->orderBy('no_transaction', 'DESC')
                    ->first();

        $counterFirst = '0001';
        if ($lastPO == null) {
            return $prefix . $counterFirst;
        } else {
            try {
                $lastParts = explode('/', $lastPO['no_transaction']);
                $lastNumber = isset($lastParts[4]) ? (int) $lastParts[4] : 0; // Perhatikan index [4] untuk nomor urut
                $counterNext = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                return $prefix . $counterNext;
            } catch (Exception $e) {
                return 'ERROR GENERATE NUMBER ' . date('Y-m-d');
            }
        }
    }
}
