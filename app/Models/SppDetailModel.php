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
        "barang_id",
        "spec",
        "qty",
        "unit",
        "price",
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
                        FORMAT(CEILING(purchase_request_details.qty) * CEILING(purchase_request_details.price), 'N', 'en-us') AS totalPrice,
                        FORMAT(CEILING(purchase_request_details.qty), 'N', 'en-us') AS qty,
                        FORMAT(CEILING(purchase_request_details.price), 'N', 'en-us') AS price,
                        barangs.kode_barang AS kodeBarang,
                        barangs.nama_barang AS barangName,
                        satuans.nama_satuan AS satuanName
                        ";

        $condition = [
            "purchase_request_id" => $id,
        ];

        $sppDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barangs', 'purchase_request_details.barang_id = barangs.id')
            ->join('satuans', 'barangs.satuan_id = satuans.id')
            ->findAll();

        return $sppDetailData;
    }
}
