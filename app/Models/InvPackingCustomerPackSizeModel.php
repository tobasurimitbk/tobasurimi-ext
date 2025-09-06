<?php

namespace App\Models;

use CodeIgniter\Model;

class InvPackingCustomerPackSizeModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inv_packing_customer_pack_size';
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

    public function getFirstByInvId($id)
    {
        $selectQry = "
            inv_packing_customer_pack_size.*,
            satuans.kode_satuan
        ";

        $result = $this->asArray()
            ->select($selectQry)
            ->join('satuans', 'satuans.id = inv_packing_customer_pack_size.satuan_size_id', 'left')
            ->where('inv_packing_customer_id', $id)
            ->first();

        return $result;
    }
}
