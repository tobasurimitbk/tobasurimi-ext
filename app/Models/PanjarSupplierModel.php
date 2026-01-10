<?php

namespace App\Models;

use CodeIgniter\Model;

class PanjarSupplierModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'panjar_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'company_id',
        'transaction_id',
        'no_panjar',
        'supplier_id',
        'bank_id',
        'divisi_id',
        'payment_date',
        'total_panjar',
        'jenis_panjar',
        'akun_kas',
        'akun_selisih',
        'type_panjar',
        'sisa_panjar',
        'keterangan',
        'is_posted'
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

    public function getPanjarSupplierList($addCondition, $condition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_panjar'     => 'panjar_supplier.no_panjar',
            'supplier_id'   => 'panjar_supplier.supplier_id',
            'payment_date'  => 'panjar_supplier.payment_date',
            'total_panjar'  => 'panjar_supplier.total_panjar',
            'createdAt'     => 'panjar_supplier.createdAt',
            'updatedAt'     => 'panjar_supplier.updatedAt'

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'panjar_supplier.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "panjar_supplier.*,name, akun_kas.id as akun_kas, akun_kas.nama_sub as akun_kas_name, akun_selisih.id as akun_selisih, akun_selisih.nama_sub as akun_selisih_name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'left')
            ->join('sub_akuns AS akun_kas', 'panjar_supplier.akun_kas = akun_kas.id', 'left')
            ->join('sub_akuns AS akun_selisih', 'panjar_supplier.akun_selisih = akun_selisih.id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);
        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['panjar_status'])) {
            $supplierDataQry->groupStart();
        }


        if (!empty($addCondition['panjar_status'])) {
            if ($addCondition['panjar_status'] == "ALL") {
                // JIKA ALL
                $supplierDataQry->whereIn('is_posted', ['1', '0']);
            } else {
                $status = $addCondition['panjar_status'] == "NOT_POSTING" ? '0' : '1';
                $supplierDataQry->where('is_posted', $status);
            }
        }



        // $supplierDataQry->where('is_posted', '0');


        if ($addCondition['search']) {
            $supplierDataQry->like('no_panjar', $addCondition['search'])->orLike('name', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $supplierDataQry->where('payment_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $supplierDataQry->where('payment_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['panjar_status'])) {
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

    //get panjar supplier by id array 
    public function getPanjarSupplierbyIDarray($id)
    {
        $selectQry = "panjar_supplier.*,type,name";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'left')
            ->join('sub_akuns AS akun_kas', 'panjar_supplier.akun_kas = akun_kas.id', 'left')
            ->join('sub_akuns AS akun_selisih', 'panjar_supplier.akun_selisih = akun_selisih.id', 'left')
            ->whereIn('panjar_supplier.id', $id)
            ->findAll();
        return $panjarSupplierData;
    }

    //get panjar id not use array
    public function getPanjarSupplierbyID($id)
    {
        $selectQry = "panjar_supplier.*,type,name, akun_kas.id as akun_kas, akun_kas.nama_sub as akun_kas_name, akun_selisih.id as akun_selisih, akun_selisih.nama_sub as akun_selisih_name";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'left')
            ->join('sub_akuns AS akun_kas', 'panjar_supplier.akun_kas = akun_kas.id', 'left')
            ->join('sub_akuns AS akun_selisih', 'panjar_supplier.akun_selisih = akun_selisih.id', 'left')
            ->find($id);
        return $panjarSupplierData;
    }

    // public function getSisaPembayaranbyID($id){
    //     $condition = [
    //         'panjar_supplier.id' => $id,
    //         'deletedAt' => NULL
    //     ];
    //     $selectQry = "panjar_supplier"
    // }

    public function getPanjarSupplierbySupplierId($id, $companyId)
    {
        $transactionIds = $this->db->table('panjar_pinjaman_transaction')
            ->select('id')
            ->where('supplier_id', $id)
            ->where('company_id', $companyId)
            ->where('deletedAt', null)
            ->where('is_posted', '1')
            ->get()
            ->getResultArray(); // Changed to getResultArray()

        if (empty($transactionIds)) {
            return [];
        }

        // Extract just the ID values
        $transactionIds = array_column($transactionIds, 'id');

        // Then get PANJAR_TB data related to these transactions
        return $this->db->table('panjar_supplier')
            ->select('panjar_supplier.*')
            ->join('panjar_pinjaman_transaction', 'panjar_supplier.transaction_id = panjar_pinjaman_transaction.id')
            ->where('panjar_supplier.supplier_id', $id)
            ->where('panjar_supplier.deletedAt', null)
            ->where('panjar_supplier.jenis_panjar', "PANJAR")
            ->where('panjar_supplier.company_id', $companyId)
            ->whereIn('panjar_pinjaman_transaction.id', $transactionIds) // Changed to whereIn
            ->get()
            ->getResult();
    }


    public function getPanjarTBSupplierbySupplierId($id, $companyId)
    {
        // First get transaction IDs from panjar_pinjaman_transaction
        $transactionIds = $this->db->table('panjar_pinjaman_transaction')
            ->select('id')
            ->where('supplier_id', $id)
            ->where('company_id', $companyId)
            ->where('deletedAt', null)
            ->where('is_posted', '1')
            ->get()
            ->getResultArray(); // Changed to getResultArray()

        if (empty($transactionIds)) {
            return [];
        }

        // Extract just the ID values
        $transactionIds = array_column($transactionIds, 'id');

        // Then get PANJAR_TB data related to these transactions
        return $this->db->table('panjar_supplier')
            ->select('panjar_supplier.*')
            ->join('panjar_pinjaman_transaction', 'panjar_supplier.transaction_id = panjar_pinjaman_transaction.id')
            ->where('panjar_supplier.supplier_id', $id)
            ->where('panjar_supplier.deletedAt', null)
            ->where('panjar_supplier.jenis_panjar', "PANJAR_TB")
            ->where('panjar_supplier.company_id', $companyId)
            ->whereIn('panjar_pinjaman_transaction.id', $transactionIds) // Changed to whereIn
            ->get()
            ->getResult();
    }


    public function getNumber($companyId)
    {
        $month = date('m'); // Bulan saat ini (format: 01-12)
        $year = date('Y'); // Tahun saat ini (format: 2023)
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d'))); // Tanggal terakhir bulan ini
    
        $lastStr = convertBulanToAngkaRomawi($month) . '/' . $year; // Format: III/2023
    
        // Ambil no_panjar terakhir di bulan & tahun ini
        $builder = $this->asArray()->select('no_panjar')
            ->orderBy('no_panjar', "DESC")
            ->where('company_id', $companyId)
            ->where('createdAt >=', $year . "-" . $month . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59")
            ->first();
    
        $kode = 'PJR'; // Kode awal: PJR
        $lastNumber = 1; // Nomor awal: 1
    
        if ($builder != null && isset($builder['no_panjar'])) {
            $explode = explode('/', $builder['no_panjar']); // Pecah no_panjar menjadi array
    
            // Pastikan format no_panjar sesuai: PJR/X/2023/00001
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

    public function getHistoryPembayaranPanjar($id)
    {

        $condition = [
            'panjar_supplier.id' => $id,

        ];

        $selectQry = "no_panjar, bayar_panjar, panjar_supplier.supplier_id, multiple_lpb_no, name, local_po_payments.payment_date";
        $historyPembayaranPanjarData = $this->asObject()
            ->select($selectQry)
            ->join('local_po_payment_panjar', 'panjar_supplier.id = local_po_payment_panjar.panjar_id', 'inner')
            ->join('local_po_payments', 'local_po_payments.id = local_po_payment_panjar.local_po_payment_id', 'inner')
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'inner')
            ->where($condition)
            ->findAll();

        return $historyPembayaranPanjarData;
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
