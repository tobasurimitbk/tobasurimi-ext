<?php

namespace App\Models;

use CodeIgniter\Model;

class OtherPaymentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'other_payment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi_id' => 'divisi_id',
            'no_pembayaran' => 'no_pembayaran',
            'tanggal' => 'tanggal',
            'valas' => 'valas',
            'metode_pembayaran' => 'metode_pembayaran',
            'nominal_pembayaran' => 'nominal_pembayaran',
            'bayar_ke' => "bayar_ke"
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            other_payment.*,
            divisis.divisi,
            SUM(other_payment_detail.nominal) AS nominal_all
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = other_payment.divisi_id')
            ->join('other_payment_detail', 'other_payment_detail.other_payment_id = other_payment.id', 'left')
            ->where($condition)
            ->where('other_payment_detail.deletedAt', null)
            ->groupBy('other_payment.id, divisis.divisi')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry
                ->like('tanggal', $addCondition['search'])
                ->orLike('divisi', $addCondition['search'])
                ->orLike('no_pembayaran', $addCondition['search'])
                ->orLike('tanggal', $addCondition['search'])
                ->orLike('bayar_ke', $addCondition['search']);
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] != "ALL") {
                $addCondition['status_posting'] = $addCondition['status_posting'] == "SUDAH POSTING" ? '1' : '0';
                $dataQry->where('other_payment.status_posting', $addCondition['status_posting']);
            }
        }

        // date filter start
        if ($addCondition['startDate']) {
            $dataQry->where('other_payment.tanggal >=', $addCondition['startDate']);
        }

        if ($addCondition['lastDate']) {
            $dataQry->where('other_payment.tanggal <=', $addCondition['lastDate']);
        }

        if ($addCondition['search']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function get_new_no(
        $jenis,
        $divisi,
        $bank_id,
        $bln,
        $thn,
        $last_day,
        $companyID
    ) {
        $banksModel = new BanksModel();
        
        // Step 1: Get bank code
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

        // Step 3: Build search pattern based on whether bank code exists
        $searchPattern = $kodeDivisi . '/';
        if (!empty($kodeBank)) {
            $searchPattern .= $kodeBank . '/';
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
        
        // Build final number
        $prefix = $kodeDivisi . '/';
        if (!empty($kodeBank)) {
            $prefix .= $kodeBank . '/';
        }
        $prefix .= $thn . '/' . $bln . '/';
        
        return $prefix . $counterNext;
    }

}
