<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranInvoiceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pembayaran_invoice';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'divisi_id',
        'user_id',
        'invoice_id',
        'customer_id',
        'valas_id',
        'no_pembayaran',
        'keterangan',
        'type_invoice',
        'tanggal',
        'total_invoice',
        'potongan',
        'total_bayar',
        'status_posting',
        'akun_kas',
        'akun_selisih',

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

    public function getList($addCondition, $condition, $limit = 10, $offset = 0)
    {

        $availableSort = [
            'no_pembayaran'     => 'pembayaran_invoice.no_pembayaran',
            'tanggal'   => 'pembayaran_invoice.tanggal',
            'type_invoice'  => 'pembayaran_invoice.type_invoice',
            'createdAt'     => 'pembayaran_invoice.createdAt',
            'updatedAt'     => 'pembayaran_invoice.updatedAt'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'pembayaran_invoice.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';
        $selectQry = "pembayaran_invoice.*, customers.name";
        $dataQry = $this
            ->select($selectQry)
            ->join("customers", "customers.id = pembayaran_invoice.customer_id", 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] != ""  || $addCondition['dateStart'] != "" || $addCondition['dateEnd'] != "" || $addCondition['type_invoice'] != "") {
            $dataQry->groupStart();
        }

        if ($addCondition['search'] != "") {
            $dataQry->like('no_pembayaran', $addCondition['search'])->orLike('name', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['type_invoice']) {
            $dataQry->whereIn('type_invoice', $addCondition['type_invoice']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['type_invoice']) {
            $dataQry->groupEnd();
        }


        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);


        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getTipeInvoice($id)
    {
        $tipe_invoice = $this
            ->select('type_invoice')
            ->where('deletedAt', null)
            ->where('id', $id)
            ->first();

        return $tipe_invoice['type_invoice'];
    }

    public function getPembayaranInvoiceDetail($id)
    {
        $detail = $this
            ->select('pembayaran_invoice.*, customers.name')
            ->join('customers', 'customers.id = pembayaran_invoice.customer_id', 'left')
            ->where('pembayaran_invoice.id', $id)
            ->first();

        return $detail;
    }
}
