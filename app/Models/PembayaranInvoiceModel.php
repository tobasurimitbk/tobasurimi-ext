<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranInvoiceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pembayaran_invoice';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'divisi_id',
        'user_id',
        'invoice_id',
        'customer_id',
        'valas_id',
        'no_pembayaran',
        'keterangan',
        'type_invoice',
        'tanggal',
        'total_invoice',
        'potongan',
        'total_bayar',
        'status_posting',
        'akun_kas',
        'akun_selisih',
        'akun_kas_lain',
        'akun_selisih_lain',
        'payment_method',
        'jenis_data',
        'pembayaran_dari',
        'bank_id',

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

    public function getList($addCondition, $condition, $limit = 10, $offset = 0)
    {

        $availableSort = [
            'no_pembayaran' => 'pembayaran_invoice.no_pembayaran',
            'tanggal' => 'pembayaran_invoice.tanggal',
            'type_invoice' => 'pembayaran_invoice.type_invoice',
            'createdAt' => 'pembayaran_invoice.createdAt',
            'updatedAt' => 'pembayaran_invoice.updatedAt'
        ];


        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'no_pembayaran'] ?? 'pembayaran_invoice.no_pembayaran';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'asc'] ?? 'ASC';
        $selectQry = "pembayaran_invoice.*";
        $dataQry = $this
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] != ""  || $addCondition['dateStart'] != "" || $addCondition['dateEnd'] != "" || $addCondition['type_invoice'] != "" || $addCondition['status_posting']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search'] != "") {
            $dataQry->like('no_pembayaran', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['type_invoice']) {
            $dataQry->whereIn('type_invoice', $addCondition['type_invoice']);
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] != "ALL") {
                $addCondition['status_posting'] = $addCondition['status_posting'] == "SUDAH POSTING" ? '1' : '0';
                $dataQry->where('pembayaran_invoice.status_posting', $addCondition['status_posting']);
            }
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['type_invoice']) {
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

    public function getTipeInvoice($id)
    {
        $tipe_invoice = $this
            ->select('type_invoice')
            ->where('deletedAt', null)
            ->where('id', $id)
            ->first();

        return $tipe_invoice['type_invoice'];
    }

    public function getPembayaranInvoiceDetail($id)
    {
        $salesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $salesOrderExportModel = new SalesOrderExportModel();
        $salesOrderLainModel = new SalesOrderLainModel();
        $salesOrderReturnModel = new SalesOrderReturnModel();
        $proformaInvoiceModel = new ProformaInvoiceModel();

        $customer_name = "";

        $data = [];
        $detail = $this
            ->select('pembayaran_invoice.*')
            ->where('pembayaran_invoice.id', $id)
            ->first();

        if ($detail['type_invoice'] == "LOKAL") {
            // Pastikan invoice_id adalah array (jika tidak, ubah ke array)
            $invoiceIds = is_array($detail['invoice_id']) ? $detail['invoice_id'] : explode(',', $detail['invoice_id']);  // Jika string, pisahkan berdasarkan koma

            // Ambil nama pelanggan berdasarkan invoice_id
            $namaCustomer = $salesOrderInvoiceModel
                ->select("customers.name")
                ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                ->whereIn('sales_order_invoice.id', $invoiceIds)  // Gunakan whereIn untuk beberapa ID invoice
                ->findAll();  // Ambil semua hasil yang sesuai

            // Gabungkan semua nama pelanggan yang ditemukan (jika ada lebih dari satu)
            $customerNames = array_map(function ($item) {
                return $item['name'];  // Ambil nama pelanggan dari setiap hasil query
            }, $namaCustomer);

            // Gabungkan nama pelanggan dengan koma jika ada lebih dari satu
            $detail['customer_name'] = implode(', ', $customerNames);  // Gabungkan nama-nama pelanggan

        } elseif ($detail['type_invoice'] == "EKSPOR") {
            $namaCustomer = $salesOrderExportModel
                ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                ->join('customers', 'customers.id = sales_contract.customer_id')
                ->where('sales_order_export_id', $detail['invoice_id'])
                ->first();
            $detail['customer_name'] = $namaCustomer['name'];
        } elseif ($detail['type_invoice'] == "PROFORMA INVOICE") {
            $namaCustomer =  $proformaInvoiceModel
                ->select('customers.*')
                ->join('sales_contract', 'sales_contract.id = proforma_invoice.sales_contract_id')
                ->join('sales_order_export', 'sales_order_export.sales_contract_id = sales_contract.id')
                ->join('customers', 'customers.id = sales_contract.customer_id')
                ->where('proforma_invoice.id', $detail['invoice_id'])
                ->first();
            $detail['customer_name'] = $namaCustomer['name'];
        } elseif ($detail['type_invoice'] == "LAIN-LAIN") {
            $namaCustomer = $salesOrderLainModel
                ->select("name")
                ->join('customers', 'customers.id = sales_order_lain.customer_id')
                ->where('sales_order_lain.id', $detail['invoice_id'])
                ->first();
            $detail['customer_name'] = $namaCustomer['name'];
        } elseif ($detail['type_invoice'] == "RETURN") {
            // Pastikan invoice_id adalah array (jika tidak, ubah ke array)
            $invoiceIds = is_array($detail['invoice_id']) ? $detail['invoice_id'] : explode(',', $detail['invoice_id']);  // Jika string, pisahkan berdasarkan koma

            // Ambil nama pelanggan berdasarkan invoice_id
            $namaCustomer = $salesOrderReturnModel
                ->select("customers.name")
                ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice')
                ->join('customers', 'customers.id = sales_order_invoice.id_customer')
                ->whereIn('sales_order_invoice.id', $invoiceIds)
                ->findAll();

            $customerNames = array_map(function ($item) {
                return $item['name'];
            }, $namaCustomer);
            $detail['customer_name'] = implode(', ', $customerNames);
        }
        return $detail;
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
        // ================= MODE EDIT: JANGAN LANJUT NOMOR =================
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
