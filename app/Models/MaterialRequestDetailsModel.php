<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequestDetailsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'material_request_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'material_request_id',
        'divisi_id',
        'warehouse_id',
        'barang1_id',
        'barang2_id',
        'nama_barang',
        'satuan',
        'stock_id',
        'bc_id',
        'no_aju',
        'ref_no',
        'stock_date',
        'barang_type',
        'qty',
        'qty2',
        'qty_isi',
        'note',
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

    public function getMaterialRequestDetailByMaterialRequestID($mrID)
    {
        $selectQry = '
            material_request_details.*,        
            barang_master.kode_barang,        
            barang_master.barang_name,        
            barang_master.type_barang      
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = material_request_details.barang1_id')
            ->where('material_request_details.material_request_id', $mrID)
            ->findAll();

        return $dataQry;
    }
}
