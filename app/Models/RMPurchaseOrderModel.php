<?php

namespace App\Models;

use CodeIgniter\I18n\Time;

use CodeIgniter\Model;

class RMPurchaseOrderModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'bc_type',
        'po_no',
        'po_date',
        'supplier_id',
        'barang_id',
        'pph',
        'cong_sebenarnya',
        'cong_batasan',
        'subsidi_langsung',
        'total',
        'is_posted',
        'createdBy',
        'status_penerimaan'
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

    public function getNoPenerimaanBarang($supplier_id, $company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('rm_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getNoPOBeaCukai($company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'is_posted' => 1,
            'status_penerimaan' => 1,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('rm_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();
        
        return $query->getResultArray();
    }

    public function getPoBBList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'            => 'rm_purchase_orders.po_date',
            'poNo'              => 'rm_purchase_orders.po_no',
            'companyName'       => 'companies.company',
            'supplier'          => 'suppliers.supplier_name',
            'createdAt'         => 'rm_purchase_orders.createdAt',
            'statusPenerimaan'  => 'rm_purchase_orders.status_penerimaan',
            'total'             => 'rm_purchase_orders.total'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rm_purchase_orders.*, 
            suppliers.name AS supplierName,
            companies.company AS companyName,
            COUNT(rm_purchase_order_details.id) AS itemCount";

        $bbLokalDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'rm_purchase_orders.supplier_id = suppliers.id', 'left')
            ->join('companies', 'rm_purchase_orders.company_id = companies.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->groupBy(('rm_purchase_orders.id'))
            ->orderBy($sort, $sortType);

        $totalData = $bbLokalDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $bbLokalDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $bbLokalDataQry
                ->like('po_no', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $bbLokalDataQry->where('rm_purchase_orders.request_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $bbLokalDataQry->where('rm_purchase_orders.request_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $bbLokalDataQry->groupEnd();
        }

        $totalFilteredData = $bbLokalDataQry->countAllResults(false);
        $data = $bbLokalDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getPoBBLokalById($id)
    {
        $selectQry = "rm_purchase_orders.*,
                            companies.company AS companyName,
                            companies.address AS companyAddress,
                            suppliers.name AS supplierName,
                            suppliers.address AS supplierAddress,
                            suppliers.phone AS supplierPhone,
                            suppliers.no_npwp AS supplierNPWP,
                            users.name AS createdBy,
                            barang_master.barang_name AS barangName
                            ";

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->find($id);

        return $poBBLokalData;
    }

    public function generateNoPo()
    {
        $romanNumb = [
            'I',
            'II',
            'III',
            'IV',
            'V',
            'VI',
            'VII',
            'VIII',
            'IX',
            'X',
            'XI',
            'XII',
        ];

        $today = Time::today('America/Chicago', 'en_US');

        $year = $today->getYear();
        $year = substr($year, -2);
        $month = $today->getMonth() - 1;

        $lastStr =  'P/' . $romanNumb[$month] . '/' . $year;

        $builder = $this->db->table('rm_purchase_orders');
        $builder->select('po_no');
        $builder->orderBy('po_no', 'desc');
        $builder->like('po_no', $lastStr);
        $query = $builder->get();

        $increment = '001';

        if ($query->getResultArray()) {
            $lastPo = explode('/', $query->getResultArray()[0]['po_no']);
            $lastPo = intval($lastPo[0]) + 1;

            if ($lastPo < 10) {
                $lastPo = "00" . $lastPo . "";
            } elseif ($lastPo > 9 && $lastPo < 100) {
                $lastPo = "0" . $lastPo . "";
            } else {
                $lastPo = strval($lastPo);
            }

            $increment = $lastPo;
        };

        $generatedPoNo = $increment . '/' . $lastStr;

        return $generatedPoNo;
    }

    public function getPoBBLokalForSupplierReport($startDate,$finishDate,$supplier,$bahanBaku,$warehouse)
    {
        $selectQry = "
        suppliers.no_npwp AS supplierNpwp,
        suppliers.name AS supplierName, 
        rm_purchase_orders.po_no AS poNum, 
        rm_purchase_orders.po_date AS poDate, 
        barang_master.barang_name AS barangName, 
        warehouses.warehouse_name AS warehouseName, 
        rm_purchase_order_details.qty AS qtyPO, 
        satuans.nama_satuan AS satuanName, 
        companies.company AS companyName, 
        rm_purchase_orders.subsidi_langsung AS subsidi, 
        rm_purchase_order_details.daily_price AS dppHarian,
        rm_purchase_order_details.monthly_price AS dppBulanan,
        rm_purchase_order_details.general_price AS dppUmum,
        rm_purchase_orders.pph AS poPPH
        ";
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'rm_purchase_orders.po_date >=' => $startDate,
            'rm_purchase_orders.po_date <=' => $finishDate,
        ];
        if (!empty($bahanBaku)) {
            $condition['rm_purchase_orders.barang_id'] = $bahanBaku;
        }                  
        if (!empty($warehouse)) {
            $condition['penerimaan_barang.warehouse_id'] = $warehouse;
        }                  
        if (!empty($supplier)) {
            $condition['rm_purchase_orders.supplier_id'] = $supplier;
        }                  

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->where($condition)
            ->findAll();

        return $poBBLokalData;
    }
}
