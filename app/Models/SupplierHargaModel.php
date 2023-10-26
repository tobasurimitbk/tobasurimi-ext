<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierHargaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'supplier_harga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'supplier_id',
        'bahan_baku_id',
        'bagian_id',
        'spesifikasi',
        'harga_umum',
        'harga_harian',
        'harga_bulanan',
        'createdAt',
        'updatedAt',
        'deletedAt'
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'bahan_baku_name'   => 'barang_master.barang_name',
            'spesifikasi'       => 'supplier_harga.spesifikasi',
            'harga_umum'        => 'supplier_harga.harga_umum',
            'harga_harian'      => 'supplier_harga.harga_harian',
            'harga_bulanan'     => 'supplier_harga.harga_bulanan',
            'createdAt'         => 'supplier_harga.createdAt',
            'updatedAt'         => 'supplier_harga.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'supplier_harga.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "supplier_harga.*, barang_master.barang_name AS bahan_baku_name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barang_master', 'supplier_harga.bahan_baku_id = barang_master.id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $supplierDataQry->groupStart()
                ->like('barang_master.barang_name', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getBySupplierId($id)
    {
        $arrCondition = [
            'supplier_harga.deletedAt' => null,
            'supplier_harga.supplier_id' => $id
        ];

        $builder = $this->db->table('supplier_harga')->select('supplier_harga.*, bagian.id as bagian_ids, bagian.nama_bagian as nama_bagian, supplier_harga.id as supplier_harga_id, barang_master.barang_name, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan');
        $builder->join('barang_master', 'supplier_harga.bahan_baku_id = barang_master.id', 'left')
        ->join('satuans', 'barang_master.satuan_id = satuans.id', 'left')
        ->join('bagian', 'bagian.id = supplier_harga.bagian_id', 'left')
        ->where($arrCondition)
        ->orderBy('supplier_harga.updatedAt', 'desc');
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getByBarangandSupplier($barang_id, $supplier_id)
    {
        $arrCondition = [
            'supplier_harga.deletedAt' => null,
            'supplier_harga.supplier_id' => $supplier_id,
            'supplier_harga.bahan_baku_id' => $barang_id
        ];

        $builder = $this->db->table('supplier_harga')->select('supplier_harga.*, bagian.id as bagian_ids, bagian.nama_bagian as nama_bagian, supplier_harga.id as supplier_harga_id, barang_master.barang_name, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan');
        $builder->join('barang_master', 'supplier_harga.bahan_baku_id = barang_master.id', 'left')
        ->join('satuans', 'barang_master.satuan_id = satuans.id', 'left')
        ->join('bagian', 'bagian.id = supplier_harga.bagian_id', 'left')
        ->where($arrCondition)
        ->orderBy('supplier_harga.updatedAt', 'desc');
        $query = $builder->get();

        return $query->getResultArray();
    }
}