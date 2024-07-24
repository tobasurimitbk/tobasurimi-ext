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
        'no_panjar',
        'supplier_id',
        'payment_date',
        'total_panjar',

        'sisa_panjar',
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

        $selectQry = "panjar_supplier.*,name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'left')
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
            ->whereIn('panjar_supplier.id', $id)
            ->findAll();
        return $panjarSupplierData;
    }

    //get panjar id not use array
    public function getPanjarSupplierbyID($id)
    {
        $selectQry = "panjar_supplier.*,type,name";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'left')
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

        $condition = [
            'panjar_supplier.supplier_id ' => $id,
            'panjar_supplier.deletedAt' => null,
            'is_posted' => '1',
            'panjar_supplier.company_id' => $companyId
        ];

        $selectQry = "panjar_supplier.*";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            // ->join('local_po_payment_panjar', 'panjar_supplier.id = local_po_payment_panjar.panjar_id', 'left')
            ->where($condition)
            ->findAll();

        return $panjarSupplierData;
    }




    public function getNumber($companyId)
    {
        $month = date('m');
        $year = date('Y');
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));

        $lastStr =  convertBulanToAngkaRomawi($month) . '/' . $year;

        // AMBIL NO PANJAR TERAKHIR DI BULAN & TAHUN INI
        $builder = $this->asArray()->select('no_panjar')
            ->orderBy('no_panjar', "DESC")
            ->where('company_id', $companyId)
            ->where('createdAt >=', $year . "-" . $month . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59")
            ->first();

        $kode = 'PJR';  // PJR/X/2023/00001
        $lastNumber = 1;

        if ($builder != null) {
            $explode = explode('/', $builder['no_panjar']); // CONVERT TO ARRAY BY (/)
            $number = intval($explode[3]); // CARI DIGIT ANGKA
            if ($number > $lastNumber) {
                $lastNumber = $number;
            }
            $lastNumber++;
        }

        $formattedlastNumber = sprintf("%02d", $lastNumber); // 00001
        $generatedNo = $kode . '/' . $lastStr . '/' . $formattedlastNumber;

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
}
