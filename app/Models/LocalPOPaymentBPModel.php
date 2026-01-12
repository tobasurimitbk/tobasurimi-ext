<?php

namespace App\Models;

use App\Controllers\Supplier\SupplierHarga;
use CodeIgniter\Model;

class LocalPOPaymentBPModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_payment_bp';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'divisi_id',
        'bank_id',
        'supplier_id',
        'tanda_terima_faktur_id',
        'jenis_pembayaran',
        'payment_no',
        'payment_date',
        'payment_panjar_date',
        'payment_method',
        'status_pph',
        'pembayaran_oleh',
        'supplier',
        'keterangan',
        'amount',
        'amount_pajak',
        'status_posting',
        'akun_kas',
        'akun_selisih',
        'akun_pajak',
        'deletedAt'
    ];

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

    public function getListBP($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'payment_no'        => 'local_po_payment_bp.payment_no',
            // 'suppliers.name'    => 'suppliers.name',
            // 'tanda_terima_faktur.faktur_no' => 'tanda_terima_faktur.faktur_no',
            'payment_date'      => 'local_po_payment_bp.payment_date',
            'payment_method'    => 'local_po_payment_bp.payment_method',
            'amount'            => 'local_po_payment_bp.amount',
            'createdAt'         => 'local_po_payment_bp.createdAt'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];
        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'local_po_payment_bp.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            local_po_payment_bp.id AS id,
            local_po_payment_bp.divisi_id AS divisi_id,
            local_po_payment_bp.status_posting,
            local_po_payment_bp.payment_no, 
            DATE_FORMAT(local_po_payment_bp.payment_date, '%d/%m/%Y') AS payment_date, 
            local_po_payment_bp.amount,
            local_po_payment_bp.amount_pajak,
            local_po_payment_bp.payment_method,
            suppliers.name AS supplierName,
            tanda_terima_faktur.faktur_no,
            DATE_FORMAT(tanda_terima_faktur.jatuh_tempo, '%d/%m/%Y') AS due_date
        ";

        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = local_po_payment_bp.supplier_id')
            ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = local_po_payment_bp.tanda_terima_faktur_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        $hasFilter = !empty($addCondition['search']) || !empty($addCondition['dueDate']) || !empty($addCondition['paymentDate']) || (!empty($addCondition['statusPosting']) && $addCondition['statusPosting'] !== 'ALL');

        if ($hasFilter) {
            $supplierDataQry->groupStart();

            if (!empty($addCondition['dueDate']) && !empty($addCondition['paymentDate'])) {
                $supplierDataQry->where("DATE(local_po_payment_bp.createdAt) BETWEEN '{$addCondition['dueDate']}' AND '{$addCondition['paymentDate']}'");
            } elseif (!empty($addCondition['dueDate'])) {
                $supplierDataQry->where('DATE(local_po_payment_bp.createdAt)', $addCondition['dueDate']);
            } elseif (!empty($addCondition['paymentDate'])) {
                $supplierDataQry->where('DATE(local_po_payment_bp.createdAt)', $addCondition['paymentDate']);
            }

            if (!empty($addCondition['search'])) {
                $search = $addCondition['search'];
                $supplierDataQry->groupStart()
                    ->like('suppliers.name', $search)
                    ->orLike('local_po_payment_bp.payment_no', $search)
                    ->orLike('tanda_terima_faktur.faktur_no', $search)
                    ->orLike('local_po_payment_bp.payment_method', $search)
                    ->orLike('local_po_payment_bp.amount', $search)
                    ->groupEnd();
            }

            if (!empty($addCondition['statusPosting']) && $addCondition['statusPosting'] !== 'ALL') {
                $statusPosting = $addCondition['statusPosting'] === "SUDAH POSTING" ? '1' : '0';
                $supplierDataQry->where('local_po_payment_bp.status_posting', $statusPosting);
            }

            $supplierDataQry->groupEnd();
        }

        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getBahanPenolong($id, $companyId)
    {
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $supplierModel = new SupplierModel();
        $companyModel = new CompaniesModel();
        $localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();

        $condition = [
            'local_po_payment_bp.id'         => $id,
            'local_po_payment_bp.company_id' => $companyId,
            'local_po_payment_bp.deletedAt'  => null
        ];

        // 🔹 Ambil data utama + join ke detail
        $result = $this
            ->select('local_po_payment_bp.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = local_po_payment_bp.supplier_id', 'left')
            ->where($condition)
            ->first();

        if ($result) {
            // 🔹 Ambil semua tanda_terima_faktur_id dari detail
            $db = \Config\Database::connect();
            $builder = $db->table('local_po_payment_details');
            $details = $builder
                ->select('tanda_terima_faktur_id')
                ->where('local_po_payment_id', $id)
                ->where('deletedAt', null)
                ->where('tipe', 'BP')
                ->get()
                ->getResultArray();

            // 🔹 Convert jadi array integer
            $result['tanda_terima_faktur_ids'] = array_map(fn($d) => (int)$d['tanda_terima_faktur_id'], $details);
        } else {
            $result = null;
        }

        return $result;
    }


    public function getPembayaranDetailByid($id)
    {

        $condition = [
            'local_po_payment_bp.id' => $id,
            'local_po_payment_bp.deletedAt' => null,

        ];
        $selectQry = 'local_po_payment_bp.id, local_po_payment_bp.tanda_terima_faktur_id, local_po_payment_bp.payment_no, local_po_payment_bp.payment_date, local_po_payment_bp.status_pph,
                        local_po_payment_bp.amount';
        $res = $this
            ->select($selectQry)
            ->join('tanda_terima_faktur', 'local_po_payment_bp.tanda_terima_faktur_id =  tanda_terima_faktur.id', 'inner')
            ->where($condition)
            ->first();

        return $res;
    }

    public function getPembayaranDetailByidTTS($tandaTerimaFakturID)
    {
        // Ubah string jadi array
        $ids = array_filter(explode(',', $tandaTerimaFakturID));

        $payments = $this
            ->select('amount')
            ->whereIn('local_po_payment_bp.tanda_terima_faktur_id', $ids)
            ->where('local_po_payment_bp.deletedAt', null)
            ->findAll();

        return $payments;
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

        // Get bank code (DINAMIS)
        $kodeBank = '';
        if (!empty($bank_id) && strtoupper($paymentMethod) !== 'CASH') {
            $bankData = $banksModel
                ->select('pay_code')
                ->where('id', $bank_id)
                ->first();

            if (!empty($bankData['pay_code'])) {
                $kodeBank = strtoupper(trim($bankData['pay_code']));
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

        if (!empty($id)) {

            // ===== 1. Ambil nomor existing (prioritas currentNumber) =====
            $existingNumber = $currentNumber;

            if (empty($existingNumber)) {
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

                    if (!in_array('id', $fields) || !in_array($numberColumn, $fields)) {
                        continue;
                    }

                    $row = $db->table($table)
                        ->select($numberColumn)
                        ->where('id', $id)
                        ->get()
                        ->getRowArray();

                    if (!empty($row[$numberColumn])) {
                        $existingNumber = $row[$numberColumn];
                        break;
                    }
                }
            }

            // ===== 2. Kalau ketemu nomor lama → ganti prefix saja =====
            if (!empty($existingNumber)) {

                $parts = explode('/', $existingNumber);
                if (count($parts) >= 4) {

                    $tahun      = $parts[1];
                    $bulan      = $parts[2];
                    $lastNumber = $parts[3];

                    // prefix baru (bank / cash / merah / putih)
                    $newPrefix = (strtoupper($paymentMethod) === 'CASH')
                        ? $kodeDivisi
                        : ($kodeBank ?: $kodeDivisi);

                    return "{$newPrefix}/{$tahun}/{$bulan}/{$lastNumber}";
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
