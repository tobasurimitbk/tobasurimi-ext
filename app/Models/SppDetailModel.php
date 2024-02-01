<?php

namespace App\Models;

use CodeIgniter\Model;

class SppDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'purchase_request_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "purchase_request_id",
        "barang1_id",
        "barang2_id",
        "nama_barang",
        "qty",
        "unit",
        "note"
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

    public function getSppDetailById($id)
    {
        $selectQry = "purchase_request_details.*,
                    barang_master.kode_barang,
                    satuans.nama_satuan AS nama_satuan";

        $condition = [
            "purchase_request_id" => $id,
        ];

        $sppDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barang_master', 'purchase_request_details.barang1_id = barang_master.id', 'left')
            ->join('satuans', 'purchase_request_details.unit = satuans.id', 'left')
            ->findAll();

        return $sppDetailData;
    }
}
