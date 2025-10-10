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
        'tanggal',
        'payment_method',
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
                $builder->whereIn('ppt.is_posted', ['0', '1']);
            } else {
                $status = $addCondition['status'] == "NOT_POSTING" ? '0' : '1';
                $builder->where('ppt.is_posted', $status);
            }
        }

        if (!empty($addCondition['search'])) {
            $builder->groupStart() // Start a group for OR conditions
                ->like('ppt.no_transaction', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->groupEnd(); // End the OR group
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

    public function getPanjarPinjamanSupplierbyID($id, $company_id)
    {
        $selectQry = "panjar_pinjaman_transaction.*,suppliers.name as supplier_name, suppliers.type as supplier_type";
        $panjarPinjamanSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_pinjaman_transaction.supplier_id = suppliers.id', 'left')
            ->where('suppliers.company_id', $company_id)
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
            'pembayaran_invoice' => ['no_pembayaran', 'tanggal'],
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
