<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiPembelianModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'transaksi_pembelian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_local_bb',
        'id_import_bb',
        'id_po_bp',
        'id_transaksi_jurnal',
        'id_supplier',
        'tgl_transaksi',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'supplier_name'              => 'suppliers.name',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'supplier_name'] ?? 'suppliers.name';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "transaksi_pembelian.*, 
        rm_purchase_orders.*,
        rm_import_pos.*,
        am_purchase_orders.*,
        suppliers.*,
        transaksi_pembelian.id AS transaksi_pembelian_id,
        DATE_FORMAT(rm_purchase_orders.po_date, '%d/%m/%Y') AS po_date_lokal_bb,
        DATE_FORMAT(rm_import_pos.po_date, '%d/%m/%Y') AS po_date_import_bb,
        DATE_FORMAT(am_purchase_orders.po_date, '%d/%m/%Y') AS po_date_po_bp,
        transaksi_jurnal.no_transaksi AS evidance_num,
        rm_purchase_orders.id AS id_lokal_bb,
        rm_import_pos.id AS id_import_bb,
        am_purchase_orders.id AS id_po_bp,
        rm_purchase_orders.po_no AS po_no_lokal_bb,
        rm_import_pos.po_no AS po_no_import_bb,
        am_purchase_orders.po_no AS po_no_po_bp,
        rm_import_pos.currency AS currency_import_bb,
        am_purchase_orders.currency AS currency_po_bp,
        suppliers.name AS supplier_name";
        $accountCustomerDataQry = $this->asObject()
            ->select($selectQry)
            ->where("transaksi_pembelian.deletedAt", NULL)
            ->join('rm_purchase_orders', 'transaksi_pembelian.id_local_bb = rm_purchase_orders.id', 'left')
            ->join('rm_import_pos', 'transaksi_pembelian.id_import_bb = rm_import_pos.id', 'left')
            ->join('am_purchase_orders', 'transaksi_pembelian.id_po_bp = am_purchase_orders.id', 'left')
            ->join('transaksi_jurnal', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left')
            ->join('suppliers', 'transaksi_pembelian.id_supplier = suppliers.id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $accountCustomerDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $accountCustomerDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $accountCustomerDataQry->like('suppliers.name', $addCondition['search'])
                ->orLike('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('rm_import_pos.po_no', $addCondition['search'])
                ->orLike('am_purchase_orders.po_no', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $accountCustomerDataQry->where('suppliers.id', $addCondition['filter']);
        }

        if ($addCondition['search']) {
            $accountCustomerDataQry->groupEnd();
        }

        $totalFilteredData = $accountCustomerDataQry->countAllResults(false);
        $data = $accountCustomerDataQry->findAll($limit, $offset);

        // var_dump($data);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }
    public function insertBatchTransaksiPembelian($data)
    {
        return $this->insertBatch($data);
    }
}
