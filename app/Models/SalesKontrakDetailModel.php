<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesKontrakDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_contract_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function getSalesContractDetailBySalesContractId($id)
    {
        $arrCondition = [
            'sales_contract_detail.deletedAt' => null,
            'sales_contract_id' => $id
        ];

        $builder = $this->db->table('sales_contract_detail')
            ->select('sales_contract_detail.*, barang_master.barang_name AS nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('barang_master', 'barang_master.id = sales_contract_detail.barang_id', 'left')
            ->join('satuans', 'satuans.id = sales_contract_detail.unit', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function detail($salesContractID)
    {
        $qryResult = $this->asArray()
            ->select('barang_master_sales.kode_barang,barang_master_sales.barang_name,sales_contract_detail.*,satuans.kode_satuan')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_contract_detail.barang_master_sales_id', 'left')
            ->join('satuans', 'satuans.id = sales_contract_detail.satuan_order_id', 'left')
            ->where('sales_contract_id', $salesContractID)->findAll();

        $dataResult = [];

        foreach ($qryResult as $q) {
            $dataResult[] = [
                'id_detail' => $q['id'],
                'barang_master_sales_id' => $q['barang_master_sales_id'],
                'kode_barang' => $q['kode_barang'],
                'barang_name' => $q['barang_name'],
                'satuan_order_id' => $q['satuan_order_id'],
                'satuan_order_name' => $q['kode_satuan'],
                'kemasan' => $q['kemasan'],
                'qty' => $q['qty'],
                'harga' => $q['harga'],
                'remark' => $q['remark'],
                'total' => $q['total_harga'],
                // print
                'total_harga' => $q['total_harga'],
                'nama_barang' => $q['barang_name'],
                'size' => $q['size']
            ];
        }

        return $dataResult;
    }
}
