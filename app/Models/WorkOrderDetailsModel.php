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
            work_order_details.id as id,        
            work_order_details.barang1_id as barang1_id,        
            barang_master_spesifikasi.id as barang2_id,        
            barang_master.barang_name as barang_name,        
            barang_master.kode_barang as kode_barang,        
            satuans.kode_satuan as kode_satuan,        
            work_order_details.nama_barang as nama_barang,        
            work_order_details.note as note,        
            work_order_details.qty as qty,        
            barang_master.type_barang as type_barang,        
            barang_master_spesifikasi.spesifikasi as spesifikasi,        
            barang_master_spesifikasi.satuan_1 as unit,        
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = work_order_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('work_order_details.work_order_id', $woID)
            ->where('work_order_details.deletedAt', null)
            ->findAll();

        return $dataQry;
    }
}
