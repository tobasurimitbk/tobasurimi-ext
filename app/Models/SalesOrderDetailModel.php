<?php

namespace App\models;

use CodeIgniter\Model;

class SalesOrderDetailModel extends Model
{

    protected $table      = 'sales_order_detail';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;

    protected $allowedFields = [
        'id_sales_order',
        'id_barang',
        'qty',
        'keterangan',
        'discount_percentage',
        'tax',
        'harga_barang',
        'amount',
        'id_warehouse',
        'dept',
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

    public function getItemListByIds($ids): array
    {
        if (!is_array($ids)) $ids = [$ids];

        $selectQry = "sales_order_detail.id AS id,
                      barangs.kode_barang AS kode_barang,
                      barangs.nama_barang AS nama_barang,
                      sales_order_detail.qty AS qty,
                      satuans.kode_satuan AS satuan,
                      sales_order_detail.discount_percentage AS disc,
                      sales_order_detail.tax AS tax,
                      sales_order_detail.amount AS amount";

        $datas = $this->asObject()
            ->select($selectQry)
            ->join('barangs', 'barangs.id = sales_order_detail.id_barang AND barangs.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = barangs.satuan_id', 'LEFT')
            ->whereIn('id_sales_order', $ids)
            ->orderBy('sales_order_detail.id_sales_order', 'ASC')
            ->orderBy('barangs.nama_barang', 'ASC')
            ->findAll();

        foreach ($datas as &$data) {
            $amount = floatval($data->amount);
            $basePrice = ($amount / $data->qty) / ((100 - $data->disc) / 100);

            $data->amount = number_format($amount);
            $data->qty = number_format($data->qty);
            $data->harga_barang = number_format($basePrice);
        }

        return $datas;
    }
}
