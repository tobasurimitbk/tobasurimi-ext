<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequestPenolongDetailsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'material_request_penolong_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields = [];

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

    public function getMaterialRequestDetailByMaterialRequestID($mrID)
    {
        $selectQry = '
            material_request_penolong_details.*,        
            barang_master.kode_barang,        
            barang_master.barang_name,        
            barang_master.type_barang      
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = material_request_penolong_details.barang1_id')
            ->where('material_request_penolong_details.material_request_id', $mrID)
            ->findAll();

        return $dataQry;
    }
}
