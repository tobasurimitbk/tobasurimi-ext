<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrderDetailsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'work_order_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'work_order_id',
        'barang1_id',
        'nama_barang',
        'qty',
        'qty_hasil',
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

    public function getWorkOrderDetailByWorkOrderID($woID)
    {
        $selectQry = '
            work_order_details.*,        
            barang_master.kode_barang,        
            barang_master.barang_name,        
            barang_master.type_barang,        
            satuans.kode_satuan,        
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = work_order_details.barang1_id')
            ->join('satuans', 'satuans.id = work_order_details.unit')
            ->where('work_order_details.work_order_id', $woID)
            ->findAll();

        return $dataQry;
    }
}
