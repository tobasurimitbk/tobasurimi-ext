<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamanSupplierModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pinjaman_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'company_id',
        'no_pinjaman',
        'supplier_id',
        'payment_date',
        'total_pinjaman',

        'sisa_pinjaman',
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

    public function getPinjamanSupplierList($addCondition, $condition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_pinjaman'     => 'pinjaman_supplier.no_pinjaman',
            'supplier_id'   => 'pinjaman_supplier.supplier_id',
            'payment_date'  => 'pinjaman_supplier.payment_date',
            'total_pinjaman'  => 'pinjaman_supplier.total_pinjaman',
            'createdAt'     => 'pinjaman_supplier.createdAt',
            'updatedAt'     => 'pinjaman_supplier.updatedAt'

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'pinjaman_supplier.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "pinjaman_supplier.*,name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);
        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['pinjaman_status'])) {
            $supplierDataQry->groupStart();
        }


        if (!empty($addCondition['pinjaman_status'])) {
            if ($addCondition['pinjaman_status'] == "ALL") {
                // JIKA ALL
                $supplierDataQry->whereIn('is_posted', ['1', '0']);
            } else {
                $status = $addCondition['pinjaman_status'] == "NOT_POSTING" ? '0' : '1';
                $supplierDataQry->where('is_posted', $status);
            }
        }



        // $supplierDataQry->where('is_posted', '0');


        if ($addCondition['search']) {
            $supplierDataQry->like('no_pinjaman', $addCondition['search'])->orLike('name', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $supplierDataQry->where('payment_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $supplierDataQry->where('payment_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['pinjaman_status'])) {
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
    public function getPinjamanSupplierbyIDarray($id)
    {
        $selectQry = "pinjaman_supplier.*,type,name";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'left')
            ->whereIn('pinjaman_supplier.id', $id)
            ->findAll();
        return $panjarSupplierData;
    }

    //get panjar id not use array
    public function getPinjamanSupplierbyID($id)
    {
        $selectQry = "pinjaman_supplier.*,type,name";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'left')
            ->find($id);
        return $panjarSupplierData;
    }

    // public function getSisaPembayaranbyID($id){
    //     $condition = [
    //         'pinjaman_supplier.id' => $id,
    //         'deletedAt' => NULL
    //     ];
    //     $selectQry = "pinjaman_supplier"
    // }

    public function getPinjamanSupplierbySupplierId($id, $companyId)
    {

        $condition = [
            'pinjaman_supplier.supplier_id ' => $id,
            'pinjaman_supplier.deletedAt' => null,
            'is_posted' => '1',
            'pinjaman_supplier.company_id' => $companyId
        ];

        $selectQry = "pinjaman_supplier.*";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            // ->join('local_po_payment_pinjaman', 'pinjaman_supplier.id = local_po_payment_panjar.pinjaman_id', 'left')
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
        $builder = $this->asArray()->select('no_pinjaman')
            ->orderBy('no_pinjaman', "DESC")
            ->where('company_id', $companyId)
            ->where('createdAt >=', $year . "-" . $month . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59")
            ->first();

        $kode = 'PJR';  // PJR/X/2023/00001
        $lastNumber = 1;

        if ($builder != null) {
            $explode = explode('/', $builder['no_pinjaman']); // CONVERT TO ARRAY BY (/)
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
            'pinjaman_supplier.id' => $id,

        ];

        $selectQry = "no_panjar, bayar_panjar, pinjaman_supplier.supplier_id, multiple_lpb_no, name, local_po_payments.payment_date";
        $historyPembayaranPanjarData = $this->asObject()
            ->select($selectQry)
            ->join('local_po_payment_pinjaman', 'pinjaman_supplier.id = local_po_payment_panjar.pinjaman_id', 'inner')
            ->join('local_po_payments', 'local_po_payments.id = local_po_payment_panjar.local_po_payment_id', 'inner')
            ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'inner')
            ->where($condition)
            ->findAll();

        return $historyPembayaranPanjarData;
    }
    
}
