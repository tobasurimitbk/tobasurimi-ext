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
        'payment_method',
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

    public function get_new_no(
    $jenis,
    $divisi,
    $paymentMethod,
    $bank_id,
    $bln,
    $thn,
    $last_day,
    $companyID
) {
    $banksModel = new BanksModel();
    
    // Step 1: Get bank code (only if payment method is BANK)
    $kodeBank = '';
    if (!empty($bank_id) && strtoupper($paymentMethod) !== 'CASH') {
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

    $kodeDivisi = '';
    // Only get division code if payment method is CASH or if bank code is empty (fallback)
    if (!empty($divisi) && (strtoupper($paymentMethod) === 'CASH' || empty($kodeBank))) {
        $divisiUpper = strtoupper($divisi);
        if (strpos($divisiUpper, 'PTS') !== false) {
            $kodeDivisi = 'PTS'; // Simplified division code without jenis
        } elseif (strpos($divisiUpper, 'CANNING') !== false) {
            $kodeDivisi = 'CAN';
        } elseif (strpos($divisiUpper, 'FROZEN I') !== false) {
            $kodeDivisi = 'FR1';
        } elseif (strpos($divisiUpper, 'FROZEN II') !== false) {
            $kodeDivisi = 'FR2';
        } elseif (strpos($divisiUpper, 'GLOBAL') !== false) {
            $kodeDivisi = 'GBL';
        } elseif (strpos($divisiUpper, 'OCS') !== false) {
            $kodeDivisi = 'OCS';
        }
    }

    // Step 3: Build search pattern
    $searchPattern = '';
    
    // Use division code for CASH, bank code for BANK
    if (strtoupper($paymentMethod) === 'CASH') {
        $searchPattern = $kodeDivisi . '/';
    } else {
        if (!empty($kodeBank)) {
            $searchPattern = $kodeBank . '/';
        } elseif (!empty($kodeDivisi)) { // Fallback to division code if no bank code
            $searchPattern = $kodeDivisi . '/';
        }
    }
    
    $searchPattern .= $thn . '/' . $bln . '/';

    // Step 4: Check all relevant tables for the highest number
    $db = \Config\Database::connect();
    
    $tablesToCheck = [
        'other_payment' => 'no_pembayaran',
        'local_po_payments' => 'payment_no',
        'local_po_payment_bp' => 'payment_no',
        'panjar_pinjaman_transaction' => 'no_transaction',
    ];
    
    $maxNumber = 0;
    
    foreach ($tablesToCheck as $table => $column) {
        $builder = $db->table($table);
        
        $lastRecord = $builder->select($column)
                            ->like($column, $searchPattern, 'after')
                            ->where('createdAt >=', "{$thn}-{$bln}-01 00:00:00")
                            ->where('createdAt <=', "{$last_day} 23:59:59")
                            ->where('company_id', $companyID)
                            ->where('deletedAt', null)
                            ->orderBy($column, 'DESC')
                            ->get(1)
                            ->getRowArray();

        if ($lastRecord) {
            try {
                $lastParts = explode('/', $lastRecord[$column]);
                $currentNumber = (int)end($lastParts);
                $maxNumber = max($maxNumber, $currentNumber);
            } catch (Exception $e) {
                log_message('error', "Failed to parse number from {$table}.{$column}: " . $e->getMessage());
            }
        }
    }

    // Step 5: Generate new number
    $counterNext = str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT);
    
    // Build final number based on payment method
    $prefix = '';
    if (strtoupper($paymentMethod) === 'CASH') {
        $prefix = $kodeDivisi . '/';
    } else {
        if (!empty($kodeBank)) {
            $prefix = $kodeBank . '/';
        } elseif (!empty($kodeDivisi)) { // Fallback to division code if no bank code
            $prefix = $kodeDivisi . '/';
        }
    }
    $prefix .= $thn . '/' . $bln . '/';
    
    return $prefix . $counterNext;
}
 
}
