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
            $builder->where('ppt.tanggal >=', $addCondition['dateStart']);
        }

        if (!empty($addCondition['dateEnd'])) {
            $builder->where('ppt.tanggal <=', $addCondition['dateEnd']);
        }

        $totalData = $builder->countAllResults(false); // total semua

        $builder->orderBy($sort, $sortType);
        $builder->orderBy('ppt.tanggal', 'DESC');
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
        $id = null,
        $jenis,
        $divisi,
        $paymentMethod,
        $bank_id,
        $bln,
        $thn,
        $last_day,
        $companyID,
        $tanggalPembayaran,
        $divisiId,
        $currentNumber = null,
        $originalDivisi = null
    ) {
        $banksModel = new BanksModel();
        $db = \Config\Database::connect();

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

        $kodeMerah = $kodePutih = '';
        foreach ($divisiMap as $key => $val) {
            if (strpos($divisiUpper, $key) !== false) {
                $kodeMerah = $val[0];
                $kodePutih = $val[1];
                $kodeDivisi = ($jenis === 'MERAH') ? $val[0] : $val[1];
                break;
            }
        }

        // Display prefix
        $displayPrefix = (strtoupper($paymentMethod) === 'CASH') ? $kodeDivisi : ($kodeBank ?: $kodeDivisi);
        $displayPrefix .= "/$targetYear/$targetMonth/";

        // 4. Jika edit mode DAN hanya ganti jenis merah/putih
        if (!empty($id) && (empty($bank_id) || $bank_id === 'undefined')) {

            if ($divisiId == $originalDivisi) {

                if (!empty($currentNumber)) {
                    $parts = explode('/', $currentNumber);
                    if (count($parts) >= 4) {
                        $oldPrefix = $parts[0];
                        $tahun = $parts[1];
                        $bulan = $parts[2];
                        $lastNumber = $parts[3];

                        $newPrefix = strtoupper($paymentMethod) === 'CASH'
                            ? $kodeDivisi
                            : ($kodeBank ?: $kodeDivisi);

                        $finalPrefix = ($oldPrefix !== $newPrefix) ? $newPrefix : $oldPrefix;

                        return "{$finalPrefix}/{$tahun}/{$bulan}/{$lastNumber}";
                    }
                }

                $tablesToCheckForId = [
                    'other_payment' => 'no_pembayaran',
                    'local_po_payments' => 'payment_no',
                    'local_po_payment_bp' => 'payment_no',
                    'panjar_pinjaman_transaction' => 'no_transaction',
                    'import_po_payments' => 'payment_no',
                    'pembayaran_invoice' => 'no_pembayaran',
                ];

                foreach ($tablesToCheckForId as $table => $numberColumn) {
                    try {
                        $fields = $db->getFieldNames($table);
                    } catch (\Exception $e) {
                        continue;
                    }
                    if (!in_array('id', $fields) || !in_array($numberColumn, $fields)) continue;

                    $row = $db->table($table)->select($numberColumn)->where('id', $id)->get()->getRowArray();
                    if ($row && !empty($row[$numberColumn])) {
                        $parts = explode('/', $row[$numberColumn]);
                        if (count($parts) >= 4) {
                            $oldPrefix = $parts[0];
                            $tahun = $parts[1];
                            $bulan = $parts[2];
                            $lastNumber = $parts[3];

                            $newPrefix = strtoupper($paymentMethod) === 'CASH'
                                ? $kodeDivisi
                                : ($kodeBank ?: $kodeDivisi);
                            $finalPrefix = ($oldPrefix !== $newPrefix) ? $newPrefix : $oldPrefix;

                            return "{$finalPrefix}/{$tahun}/{$bulan}/{$lastNumber}";
                        }
                    }
                }
            }
        }

        // Search patterns — cek dua-duanya (FRM & FRK misalnya)
        $searchPatterns = [];
        if ($kodeMerah && $kodePutih) {
            $searchPatterns[] = str_replace($kodeDivisi, $kodeMerah, $displayPrefix);
            $searchPatterns[] = str_replace($kodeDivisi, $kodePutih, $displayPrefix);
        } else {
            $searchPatterns[] = $displayPrefix;
        }

        $tablesToCheck = [
            'other_payment' => ['no_pembayaran', 'tanggal', 'deletedAt'],
            'local_po_payments' => ['payment_no', 'payment_date', 'deletedAt'],
            'local_po_payment_bp' => ['payment_no', 'payment_date', 'deletedAt'],
            'panjar_pinjaman_transaction' => ['no_transaction', 'tanggal', 'deletedAt'],
            'import_po_payments' => ['payment_no', 'payment_date', 'deletedAt'],
            'pembayaran_invoice' => ['no_pembayaran', 'tanggal', 'deletedAt'],
        ];

        $existingNumbers = [];

        foreach ($tablesToCheck as $table => [$numberColumn, $dateColumn, $deleteColumn]) {
            foreach ($searchPatterns as $pattern) {
                $cleanPattern = rtrim($pattern, '/');

                $query = $db->table($table)
                    ->select("$numberColumn, COALESCE($dateColumn, createdAt) AS effective_date")
                    ->where("$numberColumn LIKE", $cleanPattern . '/%')
                    ->where("COALESCE($dateColumn, createdAt) >=", "$targetYear-$targetMonth-01 00:00:00")
                    ->where("COALESCE($dateColumn, createdAt) <=", "$targetYear-$targetMonth-$lastDayOfMonth 23:59:59");

                $tableFields = $db->getFieldNames($table);

                if (in_array('deletedAt', $tableFields)) {
                    $query->where('deletedAt', null);
                } elseif (in_array('deleted_at', $tableFields)) {
                    $query->where('deleted_at', null);
                }

                if (!in_array($companyID, [1, 2])) {
                    $query->where('company_id', $companyID);
                } else {
                    $query->whereIn('company_id', [1, 2]);
                }

                $results = $query->orderBy('effective_date', 'DESC')
                    ->orderBy($numberColumn, 'DESC')
                    ->get()
                    ->getResultArray();

                foreach ($results as $row) {
                    $parts = explode('/', $row[$numberColumn]);
                    if (count($parts) >= 4) {
                        $existingNumbers[] = (int)end($parts);
                    }
                }
            }
        }

        // ======= NEW LOGIC: cari nomor kosong (gap) =======
        sort($existingNumbers);
        $counterNext = null;

        for ($i = 1; $i <= count($existingNumbers) + 1; $i++) {
            if (!in_array($i, $existingNumbers)) {
                $counterNext = str_pad($i, 4, '0', STR_PAD_LEFT);
                break;
            }
        }

        // Fallback kalau semua sudah berurutan
        if (!$counterNext) {
            $lastNumber = end($existingNumbers) ?: 0;
            $counterNext = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $displayPrefix . $counterNext;
    }

}
