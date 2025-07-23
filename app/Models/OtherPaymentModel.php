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
        $divisiKey = '';
        $divisiUpper = !empty($divisi) ? strtoupper($divisi) : '';

        if (!empty($divisiUpper)) {
            if (strpos($divisiUpper, 'PTS') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'MKN' : 'KKN';
                $divisiKey = 'PTS';
            } elseif (strpos($divisiUpper, 'CANNING') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'CNM' : 'CNK';
                $divisiKey = 'CANNING';
            } elseif (strpos($divisiUpper, 'FROZENI') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'FRM' : 'FRK';
                $divisiKey = 'FROZENI';
            } elseif (strpos($divisiUpper, 'FROZENII') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'FSM' : 'FSK';
                $divisiKey = 'FROZENII';
            } elseif (strpos($divisiUpper, 'FROZEN1') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'FRM' : 'FRK';
                $divisiKey = 'FROZEN1';
            } elseif (strpos($divisiUpper, 'FROZEN2') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'FSM' : 'FSK';
                $divisiKey = 'FROZEN2';
            } elseif (strpos($divisiUpper, 'FRZI') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'FRM' : 'FRK';
                $divisiKey = 'FRZI';
            } elseif (strpos($divisiUpper, 'FRZII') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'FSM' : 'FSK';
                $divisiKey = 'FRZII';
            } elseif (strpos($divisiUpper, 'GLOBAL') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'GBM' : 'GBK';
                $divisiKey = 'GLOBAL';
            } elseif (strpos($divisiUpper, 'OCS') !== false) {
                $kodeDivisi = ($jenis == 'MERAH') ? 'OCM' : 'OCK';
                $divisiKey = 'OCS';
            }
        }

        // Determine the display prefix
        $displayPrefix = '';
        if (strtoupper($paymentMethod) === 'CASH') {
            $displayPrefix = $kodeDivisi . '/';
        } else {
            if (!empty($kodeBank)) {
                $displayPrefix = $kodeBank . '/';
            } elseif (!empty($kodeDivisi)) {
                $displayPrefix = $kodeDivisi . '/';
            }
        }
        $displayPrefix .= $thn . '/' . $bln . '/';

        // Build search patterns for all possible variations
        $searchPatterns = [];
        
        if (strtoupper($paymentMethod) === 'CASH') {
            if (!empty($divisiKey)) {
                switch ($divisiKey) {
                    case 'PTS':
                        $searchPatterns[] = 'MKN/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'KKN/' . $thn . '/' . $bln . '/';
                        break;
                    case 'CANNING':
                        $searchPatterns[] = 'CNM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'CNK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FROZENI':
                        $searchPatterns[] = 'FRM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FRK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FROZENII':
                        $searchPatterns[] = 'FSM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FSK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FROZEN1':
                        $searchPatterns[] = 'FRM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FRK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FROZEN2':
                        $searchPatterns[] = 'FSM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FSK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FRZI':
                        $searchPatterns[] = 'FRM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FRK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FRZII':
                        $searchPatterns[] = 'FSM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FSK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'GLOBAL':
                        $searchPatterns[] = 'GBM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'GBK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'OCS':
                        $searchPatterns[] = 'OCM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'OCK/' . $thn . '/' . $bln . '/';
                        break;
                }
            }
        } else {
            if (!empty($kodeBank)) {
                $searchPatterns[] = $kodeBank . '/' . $thn . '/' . $bln . '/';
            } elseif (!empty($divisiKey)) {
                switch ($divisiKey) {
                    case 'PTS':
                        $searchPatterns[] = 'MKN/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'KKN/' . $thn . '/' . $bln . '/';
                        break;
                    case 'CANNING':
                        $searchPatterns[] = 'CNM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'CNK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FROZENI':
                        $searchPatterns[] = 'FRM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FRK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FROZENII':
                        $searchPatterns[] = 'FSM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FSK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FROZEN1':
                        $searchPatterns[] = 'FRM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FRK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FROZEN2':
                        $searchPatterns[] = 'FSM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FSK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FRZI':
                        $searchPatterns[] = 'FRM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FRK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'FRZII':
                        $searchPatterns[] = 'FSM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'FSK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'GLOBAL':
                        $searchPatterns[] = 'GBM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'GBK/' . $thn . '/' . $bln . '/';
                        break;
                    case 'OCS':
                        $searchPatterns[] = 'OCM/' . $thn . '/' . $bln . '/';
                        $searchPatterns[] = 'OCK/' . $thn . '/' . $bln . '/';
                        break;
                }
            }
        }

        // Check all relevant tables for the highest number
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
            
            foreach ($searchPatterns as $pattern) {
                $lastRecord = $builder->select($column)
                                    ->like($column, $pattern, 'after')
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
        }

        // Generate new number
        $counterNext = str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT);
        
        return $displayPrefix . $counterNext;
    }

}
