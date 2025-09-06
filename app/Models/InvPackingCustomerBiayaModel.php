<?php

namespace App\Models;

use CodeIgniter\Model;

class InvPackingCustomerBiayaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inv_packing_customer_biaya';
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


    public function getByInvId($id)
    {
        $resultFinal = [];
        $invPCPack = $this->where('inv_packing_customer_id', $id)->where('deletedAt', null)->findAll();
        foreach ($invPCPack as $i) {
            array_push($resultFinal, [
                'id_biaya_tambahan' => $i['id'],
                'biaya_tambahan' => $i['biaya_tambahan'],
                'tipe_biaya_tambahan' => $i['tipe_biaya_tambahan'],
                'nilai_biaya_tambahan' => (float)$i['nilai_biaya_tambahan']
            ]);
        }

        return $resultFinal;
    }
}
