<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasterModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'barang_master';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'satuan_id',
        'parent_type_id',
        'divisi_id',
        'barang_name',
        'kode_barang',
        'type_barang',
        'minimum_stock',
        'harga_pokok',
        'harga_jual',
        'createdAt',
        'updatedAt',
        'deletedAt',
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
            'kelompok_barang'   => 'parent_barang.parent_name',
            'kode_barang'       => 'barang_master.kode_barang',
            'barang_name'       => 'barang_master.barang_name',
            'satuan'            => 'satuans.nama_satuan',
            'stok'              => 'barang_master.stok',
            'createdAt'         => 'barang_master.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'barang_master.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "barang_master.*,
                    parent_barang.parent_name AS kelompok_barang,
                    satuans.kode_satuan AS satuan";

        $barangDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('satuans', 'satuans.id = barang_master.satuan_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $barangDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $barangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $barangDataQry->like('barang_master.barang_name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $barangDataQry->orLike('barang_master.kode_barang', $addCondition['search']);
        }

        if ($addCondition['search']) {
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

    public function getBarangByType($type)
    {
        $arrCondition = [
            'barang_master.deletedAt' => null,
            'barang_master.type_barang' => $type
        ];

        $selectQry = "barang_master.*, satuans.nama_satuan";
        $data = $this->select($selectQry)
            ->join('satuans', 'barang_master.satuan_id = satuans.id', 'left')
            ->where($arrCondition)
            ->findAll();

        return $data;
    }

    public function getBySupplier($id)
    {
        $arrCondition = [
            'barang_master.deletedAt' => null,
            'supplier_harga.deletedAt' => null,
            'supplier_harga.supplier_id' => $id
        ];

        $selectQry = "barang_master.*";
        $data = $this->select($selectQry)
            ->join('supplier_harga', 'barang_master.id = supplier_harga.bahan_baku_id', 'left')
            ->where($arrCondition)
            ->groupBy('id')
            ->orderBy('barang_name', 'asc')
            ->findAll();

        return $data;
    }

    public function getBarang($barangID)
    {
        return $this->where('id', $barangID)->first();
    }
}
