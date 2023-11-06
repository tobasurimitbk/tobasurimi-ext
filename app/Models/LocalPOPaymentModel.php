<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalPOPaymentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payment_no',
        'supplier_id',
        'tanda_terima_faktur_id',
        'due_date',
        'amount',
        'payment_date',
        'payment_method',
        'pembayaran_oleh',
        'type_po',
        'type_bayar',
        'multiple_po_no',
        'multiple_po_id',
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'payment_no'        => 'local_po_payments.payment_no',
            'suppliers.name'    => 'suppliers.name',
            'tanda_terima_faktur.faktur_no' => 'tanda_terima_faktur.faktur_no',
            'due_date'          => 'local_po_payments.due_date',
            'payment_date'      => 'local_po_payments.payment_date',
            'payment_method'    => 'local_po_payments.payment_method',
            'amount'            => 'local_po_payments.amount',
            'createdAt'         => 'local_po_payments.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "local_po_payments.id AS id,
                      local_po_payments.payment_no AS payment_no, 
                      DATE_FORMAT(local_po_payments.due_date, '%d/%m/%Y') AS due_date, 
                      DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y') AS payment_date, 
                      local_po_payments.amount AS amount,
                      local_po_payments.payment_method AS payment_method,
                      suppliers.name AS supplierName,
                      tanda_terima_faktur.faktur_no
                      ";

        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = local_po_payments.supplier_id')
            ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = local_po_payments.tanda_terima_faktur_id')
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] != "" || $addCondition['dueDate'] != "" || $addCondition['paymentDate'] != "") {
            $supplierDataQry->groupStart();
        }

        if ($addCondition['search'] != "") {
            $supplierDataQry
                ->like('payment_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('local_po_payments.payment_no', $addCondition['search'])
                ->orLike('tanda_terima_faktur.faktur_no', $addCondition['search'])
                ->orLike('local_po_payments.payment_method', $addCondition['search'])
                ->orLike('local_po_payments.amount', $addCondition['search']);
        }

        if ($addCondition['dueDate'] != "") {
            $supplierDataQry->where("DATE_FORMAT(local_po_payments.due_date, '%d/%m/%Y')", $addCondition['dueDate']);
        }

        if ($addCondition['paymentDate'] != "") {
            $supplierDataQry->where("DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y')", $addCondition['paymentDate']);
        }

        if ($addCondition['search'] != "" || $addCondition['dueDate'] != "" || $addCondition['paymentDate'] != "") {
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

    public function get($pembayaranID)
    {
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $supplierModel = new SupplierModel();
        $companyModel = new CompaniesModel();

        $result = [
            'pembayaranDetail' => null,
            'tandaTerimaSupplier' => null,
            'itemLpbList' => null,
            'supplierDetail' => null,
            'company' => null
        ];
        $result['pembayaranDetail'] = $this->where('id', $pembayaranID)->first();
        $result['itemLpbList'] = $tandaTerimaFakturDetailModel->getListTandaTerimaItemFaktur($result['pembayaranDetail']['tanda_terima_faktur_id']);
        $result['tandaTerimaSupplier'] = $tandaTerimaFakturModel->getByID($result['pembayaranDetail']['tanda_terima_faktur_id']);
        $result['supplierDetail'] = $supplierModel->where('id', $result['pembayaranDetail']['supplier_id'])->first();
        $result['company'] = $companyModel->select('companies.company')
            ->join('users', 'users.current_company_id = companies.id')
            ->join('tanda_terima_faktur', 'tanda_terima_faktur.user_id = users.id')
            ->where('tanda_terima_faktur.user_id', $result['tandaTerimaSupplier']['user_id'])
            ->first();
        return $result;
    }

    public function getListLPBNotPaid($supplierID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $resLPB = [];

        $conditionPenerimaanBarang = [
            'deletedAt' => null,
            'status_post' => 'FINISH',
            'tipe_bahan' => 'BAKU',
            'status_penerimaan' => 'LOKAL',
            'supplier_id' => $supplierID
        ];

        $lpbList = $penerimaanBarangModel->where($conditionPenerimaanBarang)->findAll();
        $poPayed = static::summaryArrPOIsPayed($supplierID, "Bahan Baku");

        foreach ($lpbList as $l) {
            $poID = array_diff(json_decode($l['multiple_po_id']), $poPayed['po_id']);
            $poNo = array_diff(json_decode($l['multiple_po_no']), $poPayed['po_no']);
            if (count($poID) != 0 && count($poNo) != 0) {
                $resLPB[] = [
                    'lpbID' => $l['id'],
                    'lpbNO' => $l['no_penerimaan_barang'],
                    'poNO' => $poNo,
                    'poID' => $poID
                ];
            }
        }

        return $resLPB;
    }

    static function summaryArrPOIsPayed($supplierID, $typePO)
    {
        $localPaymentModel = new LocalPOPaymentModel();

        $conditionLocalPayment = [
            'deletedAt' => null,
            'type_po' => $typePO,
            'supplier_id' => $supplierID
        ];

        $paymentList = $localPaymentModel->where($conditionLocalPayment)->findAll();
        $lpbIDArr = [];
        $noPoArr = [];

        foreach ($paymentList as $pl) {
            foreach (json_decode($pl['multiple_po_id']) as $id) {
                $lpbIDArr[] = $id;
            }
            foreach (json_decode($pl['multiple_po_no']) as $po) {
                $noPoArr[] = $po;
            }
        }

        return [
            'po_id' => $lpbIDArr,
            'po_no' => $noPoArr
        ];
    }
}
