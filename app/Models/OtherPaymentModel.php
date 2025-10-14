<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

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
            SUM(other_payment_detail.jumlah) AS nominal_all
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
        $companyID,
        $tanggalPembayaran
    ) {
        $banksModel = new BanksModel();

        // Parse tanggalPembayaran
        $tanggalObj = new \DateTime(str_replace("/", "-", $tanggalPembayaran));
        $targetYear = $tanggalObj->format('Y');
        $targetMonth = $tanggalObj->format('m');
        $lastDayOfMonth = $tanggalObj->format('t');

        // Get bank code
        $kodeBank = '';
        if (!empty($bank_id) && strtoupper($paymentMethod) !== 'CASH') {
            $bankData = $banksModel->select('kode_bank')->where('id', $bank_id)->first();
            if ($bankData) {
                $kode = strtoupper($bankData['kode_bank']);
                if (strpos($kode, 'BBRI') !== false) $kodeBank = 'BRI';
                elseif (strpos($kode, 'BMRIIDJA') !== false) $kodeBank = 'MND';
                elseif (strpos($kode, 'BBNI') !== false) $kodeBank = 'KBA';
                elseif (strpos($kode, 'BBCA') !== false) $kodeBank = 'BCI';
                elseif (strpos($kode, 'BBNL') !== false) $kodeBank = 'BNL';
            }
        }

        // Get divisi code
        $kodeDivisi = '';
        $divisiKey = '';
        $divisiUpper = strtoupper($divisi);

        $divisiMap = [
            'PTS' => ['MKN', 'KKN'],
            'CANNING' => ['CNM', 'CNK'],
            'FROZENI' => ['FRM', 'FRK'],
            'FROZENII' => ['FSM', 'FSK'],
            'FROZEN1' => ['FRM', 'FRK'],
            'FROZEN2' => ['FSM', 'FSK'],
            'FRZI' => ['FRM', 'FRK'],
            'FRZII' => ['FSM', 'FSK'],
            'GLOBAL' => ['GBM', 'GBK'],
            'OCS' => ['OCM', 'OCK']
        ];

        foreach ($divisiMap as $key => $val) {
            if (strpos($divisiUpper, $key) !== false) {
                $kodeDivisi = ($jenis === 'MERAH') ? $val[0] : $val[1];
                $divisiKey = $key;
                break;
            }
        }

        // Display prefix
        $displayPrefix = '';
        $displayPrefix = (strtoupper($paymentMethod) === 'CASH') ? $kodeDivisi : ($kodeBank ?: $kodeDivisi);
        $displayPrefix .= "/$targetYear/$targetMonth/";

        // Search patterns
        $searchPatterns = [];
        if (strtoupper($paymentMethod) === 'CASH') {
            if (!empty($divisiKey) && isset($divisiMap[$divisiKey])) {
                $searchPatterns[] = $divisiMap[$divisiKey][0] . "/$targetYear/$targetMonth/";
                $searchPatterns[] = $divisiMap[$divisiKey][1] . "/$targetYear/$targetMonth/";
            }
        } else {
            if ($kodeBank) {
                $searchPatterns[] = "$kodeBank/$targetYear/$targetMonth/";
            } elseif (!empty($divisiKey) && isset($divisiMap[$divisiKey])) {
                $searchPatterns[] = $divisiMap[$divisiKey][0] . "/$targetYear/$targetMonth/";
                $searchPatterns[] = $divisiMap[$divisiKey][1] . "/$targetYear/$targetMonth/";
            }
        }

        // Check all relevant tables
        $db = \Config\Database::connect();
        $tablesToCheck = [
            'other_payment' => ['no_pembayaran', 'tanggal'],
            'local_po_payments' => ['payment_no', 'payment_date'],
            'local_po_payment_bp' => ['payment_no', 'payment_date'],
            'panjar_pinjaman_transaction' => ['no_transaction', 'tanggal'],
            'pembayaran_invoice' => ['no_pembayaran', 'tanggal']
        ];

        $maxNumber = 0;
        foreach ($tablesToCheck as $table => [$numberColumn, $dateColumn]) {
            $builder = $db->table($table);
            foreach ($searchPatterns as $pattern) {
                $query = $builder->select("$numberColumn, COALESCE($dateColumn, createdAt) AS effective_date")
                    ->like($numberColumn, $pattern, 'after')
                    ->where("COALESCE($dateColumn, createdAt) >=", "$targetYear-$targetMonth-01 00:00:00")
                    ->where("COALESCE($dateColumn, createdAt) <=", "$targetYear-$targetMonth-$lastDayOfMonth 23:59:59")
                    ->where('deletedAt', null);

                if (!in_array($companyID, [1, 2])) {
                    $query->where('company_id', $companyID);
                } else {
                    $query->whereIn('company_id', [1, 2]);
                }

                $lastRecord = $query->orderBy('effective_date', 'DESC')
                    ->orderBy($numberColumn, 'DESC')
                    ->get(1)
                    ->getRowArray();

                if ($lastRecord) {
                    $parts = explode('/', $lastRecord[$numberColumn]);
                    $currentNumber = (int)end($parts);
                    $maxNumber = max($maxNumber, $currentNumber);
                }
            }
        }

        $counterNext = str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT);
        return $displayPrefix . $counterNext;
    }

}
