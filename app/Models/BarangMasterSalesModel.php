<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasterSalesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'barang_master_sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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
            'type_barang'       => 'type_barang',
            'kode_barang'       => 'kode_barang',
            'barang_name'       => 'barang_name',
            'satuan_id'         => 'satuan_id',
            'harga_jual'        => 'harga_jual',
            'harga_pokok'       => 'harga_pokok',
            'status_ppn'         => 'status_ppn'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'barang_master_sales.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "barang_master_sales.*,satuans.kode_satuan,satuans.nama_satuan";

        $barangDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $barangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['type_barang']) {
            $barangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $barangDataQry->like('barang_master_sales.barang_name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $barangDataQry->orLike('barang_master_sales.kode_barang', $addCondition['search']);
        }

        if ($addCondition['type_barang']) {
            $barangDataQry->where('type_barang', $addCondition['type_barang']);
        }

        if ($addCondition['search'] || $addCondition['type_barang']) {
            $barangDataQry->groupEnd();
        }

        $totalFilteredData = $barangDataQry->countAllResults(false);
        $data = $barangDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }
}
