<?php

namespace App\Models;

use CodeIgniter\Model;

class BCBarangTarifModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_barang_tarif';
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

    public function getList($condition, $limit = 10, $offset = 0)
    {

        $sort = 'bc_barang_tarif.createdAt';
        $sortType = 'ASC';

        $selectQry = "bc_barang_tarif.*";

        $pinjamanQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $pinjamanQry->countAllResults(false);

        $totalFilteredData = $pinjamanQry->countAllResults(false);
        $data = $pinjamanQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getByBcPurchaseOrder($id)
    {
        $selectQry = "kode_jenis_pungutan, nilai_bayar, kode_fasilitas_tarif";

        $result = $this->asArray()
            ->select($selectQry)
            ->where('bc_purchase_order_id', $id)
            ->findAll();

        return $result;
    }
}
