<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderExportDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_order_detail_export';
    protected $primaryKey       = 'sales_order_export_detail_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sales_order_export_detail_id',
        'sales_order_export_id',
        'barang_id',
        'barang_name',
        'barang_kode',
        'sales_contract_detail_id',
        'qty',
        'satuan_id',
        'remark',
        'kemasan',
        'harga_barang',
        'tipe_input',
        'total_harga_barang',
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

    public function getSalesOrderExportDetailBySalesOrderExportId($id)
    {
        $arrCondition = [
            'sales_order_detail_export.deletedAt' => null,
            'sales_order_export_id' => $id
        ];

        $builder = $this->db->table('sales_order_detail_export')
            ->select('sales_order_detail_export.*, barang_master.barang_name as nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('barang_master', 'barang_master.id = sales_order_detail_export.barang_id', 'left')
            ->join('satuans', 'satuans.id = sales_order_detail_export.unit', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }
}
