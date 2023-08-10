<?php

namespace App\Models;

use CodeIgniter\Model;

class RMImportPODetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_import_po_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'rm_import_po_id', 'barang_id', 'item_desc', 'spec', 'note', 'unit', 'qty', 'price',
    'disc', 'additional_cost', 'ppn', 'pph', 'remaining_qty', 'qty_diterima'];

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

    public function getPurchaseOrderDetailByPurchaseOrderId($id)
    {
        $arrCondition = [
            'rm_import_po_details.deletedAt' => null,
            'rm_import_po_details.rm_import_po_id' => $id
        ];

        $builder = $this->db->table('rm_import_po_details')
        ->select('rm_import_po_details.*, rm_import_pos.po_no,
        rm_import_pos.status_penerimaan, barangs.nama_barang, barangs.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
        ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
        ->join('barangs', 'barangs.id = rm_import_po_details.barang_id', 'left')
        ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();
        
        return $query->getResultArray();
    }
}