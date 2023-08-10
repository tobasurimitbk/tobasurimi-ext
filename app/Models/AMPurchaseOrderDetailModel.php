<?php

namespace App\Models;

use CodeIgniter\Model;

class AMPurchaseOrderDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'am_purchase_order_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'am_purchase_order_id',
        'barang_id',
        'item_desc',
        'spec',
        'note',
        'unit',
        'qty',
        'price',
        'disc',
        'additional_cost',
        'ppn',
        'pph',
        'remaining_qty',
        'qty_diterima'
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

    public function getPurchaseOrderDetailByPurchaseOrderId($id)
    {
        $arrCondition = [
            'am_purchase_order_details.deletedAt' => null,
            'am_purchase_order_details.am_purchase_order_id' => $id
        ];

        $selectQry = "am_purchase_order_details.*,
            am_purchase_orders.po_no,
            am_purchase_orders.status_penerimaan,
            FORMAT(CEILING(am_purchase_order_details.qty) * CEILING(am_purchase_order_details.price) + CEILING(am_purchase_order_details.additional_cost), 'N', 'en-us') AS totalPrice,
            FORMAT(CEILING(am_purchase_order_details.qty), 'N', 'en-us') AS qty,
            FORMAT(CEILING(am_purchase_order_details.price), 'N', 'en-us') AS price,
            FORMAT(CEILING(am_purchase_order_details.additional_cost), 'N', 'en-us') AS additional_cost,
            barangs.nama_barang, 
            barangs.kode_barang,
            taxppn.tax_value as ppnValue, 
            taxpph.tax_value as pphValue, 
            satuans.id as id_satuan, 
            satuans.nama_satuan";

        $builder = $this->db->table('am_purchase_order_details')
            ->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('barangs', 'barangs.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('taxes AS taxppn', 'taxppn.id = am_purchase_order_details.ppn', 'left')
            ->join('taxes AS taxpph', 'taxpph.id = am_purchase_order_details.pph', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}
