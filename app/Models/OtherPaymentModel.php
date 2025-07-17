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
        $lastPO = $this->select('no_pembayaran')
                    ->like('no_pembayaran', $prefix)
                    ->where('createdAt >=', "{$thn}-{$bln}-01 00:00:00")
                    ->where('createdAt <=', "{$last_day} 23:59:59")
                    ->where('company_id', $companyID)
                    ->where('deletedAt', null)
                    ->orderBy('no_pembayaran', 'DESC')
                    ->first();

        $counterFirst = '0001';
        if ($lastPO == null) {
            return $prefix . $counterFirst;
        } else {
            try {
                $lastParts = explode('/', $lastPO['no_pembayaran']);
                $lastNumber = isset($lastParts[4]) ? (int) $lastParts[4] : 0; // Perhatikan index [4] untuk nomor urut
                $counterNext = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                return $prefix . $counterNext;
            } catch (Exception $e) {
                return 'ERROR GENERATE NUMBER ' . date('Y-m-d');
            }
        }
    }
}
