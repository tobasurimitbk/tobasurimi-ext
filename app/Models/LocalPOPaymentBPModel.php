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
            'local_po_payment_bp.id'            => $id,
            'local_po_payment_bp.company_id'    => $companyId,
            'local_po_payment_bp.deletedAt'     => null
        ];


        $result = [
            'pembayaranDetail' => null,
            'itemList' => null,
            'company' => null,
            'supplierDetail' => null,
        ];


        $resultPaymentBP = $this
            ->select('local_po_payment_bp.*, tanda_terima_faktur.jatuh_tempo')
            ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = local_po_payment_bp.tanda_terima_faktur_id')
            ->where($condition)
            ->first();


        $result['pembayaranDetail'] = $resultPaymentBP;

        $result['tandaTerimaSupplier'] = $tandaTerimaFakturModel->getByID($result['pembayaranDetail']['tanda_terima_faktur_id']);


        $conditionListBarang = [
            'tanda_terima_faktur_detail.deletedAt' => null,
            'tanda_terima_faktur_detail.tanda_terima_faktur_id' => $resultPaymentBP['tanda_terima_faktur_id']
        ];

        $selectQry = "lpb_date, lpb_no, item_name, qty, price, unit";
        $listBarang = $this
            ->select($selectQry)
            ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = local_po_payment_bp.tanda_terima_faktur_id', 'left')
            ->join('tanda_terima_faktur_detail', 'tanda_terima_faktur_detail.tanda_terima_faktur_id = tanda_terima_faktur.id')
            ->where($conditionListBarang)
            ->distinct()
            ->findAll();

        $result['itemLpbList'] = $listBarang;

        $result['company'] = $companyModel->select('companies.company')
            ->where('id', $companyId)
            ->first();

        $panjarList = $localPOPaymentPanjarModel
            ->select('*, no_panjar, type, panjar_supplier.id as panjar_id')
            ->join("local_po_payment_bp", 'local_po_payment_panjar.local_po_payment_id = local_po_payment_bp.id')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
            ->where('local_po_payment_bp.deletedAt', null)
            ->where('local_po_payment_bp.id', $id)
            ->where('type', 'BP')
            ->findAll();


        $total_bayar_panjar = 0;
        $total_panjar = 0;
        $dataPembayaranPanjar = [];
        foreach ($panjarList as $p) {
            $total_bayar_panjar = $localPOPaymentPanjarModel->getTotalPembayaranPanjar($p['panjar_id'], "BP");
            array_push($dataPembayaranPanjar, [
                'bayar_panjar'  => number_format($p['bayar_panjar'], 2),
                'no_panjar'     => $p['no_panjar'],
                'payment_date'  => date('d/m/Y', strtotime($p['payment_date'])),
                'total_panjar'  => number_format($p['total_panjar'], 2),
                'sisa_panjar'   => number_format(intval($p['total_panjar']) - intval($total_bayar_panjar['total_bayar_panjar']), 2)
            ]);
            $total_panjar = $p['bayar_panjar'];
        }



        $result['totalpanjar'] = $total_panjar;
        $result['panjar'] = $dataPembayaranPanjar;
        $result['supplierDetail'] = $supplierModel->where('id', $result['pembayaranDetail']['supplier_id'])->first();
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
        $condition = [
            'local_po_payment_bp.tanda_terima_faktur_id' => $tandaTerimaFakturID,
            'local_po_payment_bp.deletedAt' => null,
        ];
        
        $payments = $this
            ->select('amount')
            ->where($condition)
            ->findAll();

        return $payments;
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
