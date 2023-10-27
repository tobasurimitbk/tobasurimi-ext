<?php

namespace App\Models;

use CodeIgniter\Model;

class StockDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'barang_id',
        'warehouse_id',
        'stock_type',
        'qty',
        'status',
        'spesifikasi',
        'stock_date',
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

    public function addOrReduceStock($barangID, $warehouseID, $stockType = 'New', $qty, $status = 'IN', $spesifikasi)
    {
        $this->insert([
            'barang_id' => $barangID,
            'warehouse_id' => $warehouseID,
            'stock_type' => $stockType,
            'qty' => $qty,
            'status' => $status,
            'spesifikasi' => $spesifikasi,
            'stock_date' => date('Y-m-d')
        ]);
    }

    public function checkStock($barangID, $warehouseID)
    {
        $condition = [
            'stock_details.barang_id' => $barangID,
            'stock_details.warehouse_id' => $warehouseID,
        ];

        $selectQry = '
            (SUM(CASE WHEN status = "In" THEN qty ELSE 0 END) - 
            SUM(CASE WHEN status = "Out" THEN qty ELSE 0 END)) 
            AS 
            totalStock
        ';

        $query = $this->select($selectQry)
            ->where($condition)
            ->groupBy('barang_id')
            ->findAll();

        if (!empty($query)) {
            return $query[0]['totalStock'];
        } else {
            return 0;
        }
    }

    public function getListHistori($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'stock_details.barang_id' => 'barang_id',
            'stock_details.warehouse_id' => 'warehouse_id',
            'stock_details.stock_type' => 'stock_type',
            'stock_details.qty' => 'qty',
            'stock_details.status' => 'status',
            'stock_details.spesifikasi' => 'spesifikasi',
            'stock_details.stock_date' => 'stock_date',
            'companies.company' => 'companies.company'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "stock_details.*, 
            barang_master.barang_name,
            satuans.nama_satuan, 
            warehouses.warehouse_name AS warehousesName,
            companies.company AS companyName";

        $DataQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock_details.barang_id')
            ->join('satuans', 'satuans.id = barang_master.satuan_id')
            ->join('warehouses', 'warehouses.id = stock_details.warehouse_id')
            ->join('companies', 'companies.id = warehouses.company_id')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $DataQry->countAllResults(false);

        if ($addCondition['search']) {
            $DataQry->groupStart()
                ->like('stock_details.stock_date', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('satuans.nama_satuan', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->orLike('companies.company', $addCondition['search'])
                ->orLike('stock_details.stock_date', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $DataQry->countAllResults(false);
        $data = $DataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getListStock($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'companies.company' => 'companies.company',
            'stock_details.warehouse_id' => 'stock_details.warehouse_id',
            'barang_master.parent_type_id' => 'barang_master.parent_type_id',
            'barang_master.barang_name' => 'barang_master.barang_name',
            'satuans.nama_satuan' => 'satuans.nama_satuan',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'stock_details.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = '
            (SUM(CASE WHEN stock_details.status = "In" 
            THEN stock_details.qty ELSE 0 END) - 
            SUM(CASE WHEN stock_details.status = "Out" 
            THEN stock_details.qty ELSE 0 END)) 
            AS totalStock, 
            barang_master.id,
            barang_master.barang_name,
            satuans.nama_satuan, 
            warehouses.warehouse_name AS warehousesName,
            companies.company AS companyName,
            parent_barang.parent_name AS kelompok';

        $DataQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock_details.barang_id')
            ->join('satuans', 'satuans.id = barang_master.satuan_id')
            ->join('warehouses', 'warehouses.id = stock_details.warehouse_id')
            ->join('companies', 'companies.id = warehouses.company_id')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'LEFT')
            ->where($condition)
            ->groupBy('stock_details.barang_id')
            ->orderBy($sort, $sortType);

        $totalData = $DataQry->countAllResults(false);

        if ($addCondition['search']) {
            $DataQry->groupStart()
                ->like('barang_master.barang_name', $addCondition['search'])
                ->orLike('companies.company', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->orLike('parent_barang.parent_name', $addCondition['search'])
                ->orLike('satuans.nama_satuan', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $DataQry->countAllResults(false);
        $data = $DataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
