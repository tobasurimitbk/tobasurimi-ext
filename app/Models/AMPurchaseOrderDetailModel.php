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
        'purchase_request_detail_id',
        'barang_id',
        'item_desc',
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
            (am_purchase_order_details.qty * am_purchase_order_details.price) AS totalPriceWithoutAdditional,
            (am_purchase_order_details.qty * am_purchase_order_details.price + am_purchase_order_details.additional_cost) AS totalPrice,
            am_purchase_order_details.price AS price,
            am_purchase_order_details.additional_cost AS additional_cost,
            barang_master.barang_name as nama_barang, 
            am_purchase_order_details.note AS spp_note,
            barang_master.kode_barang,
            taxppn.tax_value as ppnValue, 
            taxpph.tax_value as pphValue, 
            satuans.id as id_satuan, 
            satuans.nama_satuan";

        $builder = $this->db->table('am_purchase_order_details')
            ->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('taxes AS taxppn', 'taxppn.id = am_purchase_order_details.ppn', 'left')
            ->join('taxes AS taxpph', 'taxpph.id = am_purchase_order_details.pph', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailById($id)
    {
        $arrCondition = [
            'am_purchase_order_details.deletedAt' => null,
            'am_purchase_order_details.id' => $id
        ];

        $selectQry = "am_purchase_order_details.*,
            am_purchase_orders.po_no,
            purchase_requests.spp_no,
            am_purchase_orders.status_penerimaan,
            (am_purchase_order_details.qty * am_purchase_order_details.price) AS totalPriceWithoutAdditional,
            (am_purchase_order_details.qty * am_purchase_order_details.price + am_purchase_order_details.additional_cost) AS totalPrice,
            (am_purchase_order_details.price) AS price,
            (am_purchase_order_details.additional_cost) AS additional_cost,
            barang_master.barang_name as nama_barang, 
            barang_master.kode_barang,
            am_purchase_order_details.note AS spp_note,
            taxppn.tax_value as ppnValue, 
            taxpph.tax_value as pphValue, 
            satuans.id as id_satuan, 
            satuans.nama_satuan";

        $builder = $this->db->table('am_purchase_order_details')
            ->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('taxes AS taxppn', 'taxppn.id = am_purchase_order_details.ppn', 'left')
            ->join('taxes AS taxpph', 'taxpph.id = am_purchase_order_details.pph', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getPurchaseOrderDetailByPurchaseRequestDetailId($id)
    {
        $arrCondition = [
            'am_purchase_order_details.deletedAt' => null,
            'am_purchase_order_details.purchase_request_detail_id' => $id
        ];

        $selectQry = "am_purchase_order_details.*,
            am_purchase_orders.po_no,
            am_purchase_orders.status_penerimaan,
            FORMAT(CEILING(am_purchase_order_details.qty) * CEILING(am_purchase_order_details.price), 'N', 'en-us') AS totalPriceWithoutAdditional,
            FORMAT(CEILING(am_purchase_order_details.qty) * CEILING(am_purchase_order_details.price) + CEILING(am_purchase_order_details.additional_cost), 'N', 'en-us') AS totalPrice,
            FORMAT(CEILING(am_purchase_order_details.price), 'N', 'en-us') AS price,
            FORMAT(CEILING(am_purchase_order_details.additional_cost), 'N', 'en-us') AS additional_cost,
            barang_master.barang_name as nama_barang, 
            barang_master.kode_barang,
            taxppn.tax_value as ppnValue, 
            taxpph.tax_value as pphValue, 
            am_purchase_order_details.note AS spp_note,
            satuans.id as id_satuan, 
            satuans.nama_satuan";

        $builder = $this->db->table('am_purchase_order_details')
            ->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('taxes AS taxppn', 'taxppn.id = am_purchase_order_details.ppn', 'left')
            ->join('taxes AS taxpph', 'taxpph.id = am_purchase_order_details.pph', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }
}
