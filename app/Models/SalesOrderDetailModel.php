<?php

namespace App\Models;

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
        'qty_sekarang',
        'keterangan',
        'discount_percentage',
        'tax',
        'harga_barang',
        'amount',
        'id_warehouse',
        'dept',
        'tipe_input',
        'status_ppn',
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
                    sales_order_detail.id_barang AS id_barang,
                    barang_master_sales.kode_barang AS kode_barang,
                      barang_master_sales.barang_name AS nama_barang,
                      sales_order_detail.qty AS qty,
                      sales_order_detail.qty_sekarang AS qty_sekarang,
                      satuans.kode_satuan AS satuan,
                      sales_order_detail.discount_percentage AS disc,
                      sales_order_detail.tax AS tax,
                      sales_order_detail.amount AS amount,
                      sales_order_detail.id_sales_order,
                      sales_order_detail.harga_barang AS harga_barang";

        $datas = $this->asObject()
            ->select($selectQry)
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang AND barang_master_sales.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            ->where('qty_sekarang !=', 0)
            ->where('tipe_input', 'order_form')
            ->whereIn('id_sales_order', $ids)
            ->orderBy('sales_order_detail.id_sales_order', 'ASC')
            ->orderBy('barang_master_sales.barang_name', 'ASC')
            ->findAll();

        foreach ($datas as &$data) {
            $amount = ($data->amount);
            // $basePrice = ($amount / $data->qty) / ((100 - $data->disc) / 100);
            $totalPrice = ($amount / $data->qty) / ((100 - $data->disc) / 100);

            // $data->harga_barang = number_format($basePrice);
            $data->total_harga_barang = number_format($totalPrice);
        }

        // $totalPrice = 0;
        // $totalQty = 0;
        // foreach ($datas as &$data) {
        //     $amount = floatval($data->amount);
        //     $basePrice = ($amount / $data->qty) / ((100 - $data->disc) / 100);

        //     $amounts = $data->amount;
        //     $basePrices = ($amounts / $data->qty) / ((100 - $data->disc) / 100);

        //     $totalQty += floatval($data->qty);

        //     $data->amount = number_format($amount);
        //     $data->qty = number_format($data->qty);
        //     $data->harga_barang = number_format($basePrice);
        //     $totalPrice += $basePrices;
        // }
        // $datas['total_qty'] = $totalQty;
        // $datas['total_amount'] = $totalPrice;

        // var_dump($datas);

        return $datas;
    }


    public function getItemListPostingByIds($ids): array
    {

        if (!is_array($ids)) $ids = [$ids];

        $selectQry = "sales_order_detail.id AS id,
                    sales_order_detail.id_barang AS id_barang,
                    barang_master_sales.kode_barang AS kode_barang,
                      barang_master_sales.barang_name AS nama_barang,
                      sales_order_detail.qty AS qty,
                      sales_order_detail.qty_sekarang AS qty_sekarang,
                      satuans.kode_satuan AS satuan,
                      sales_order_detail.discount_percentage AS disc,
                      sales_order_detail.tax AS tax,
                      sales_order_detail.amount AS amount,
                      sales_order_detail.id_sales_order,
                      sales_order_detail.harga_barang AS harga_barang";

        $datas = $this->asObject()
            ->select($selectQry)
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang AND barang_master_sales.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            // ->where('qty_sekarang !=', 0)
            ->where('tipe_input', 'order_form')
            ->whereIn('id_sales_order', $ids)
            ->orderBy('sales_order_detail.id_sales_order', 'ASC')
            ->orderBy('barang_master_sales.barang_name', 'ASC')
            ->findAll();

        foreach ($datas as &$data) {
            $amount = ($data->amount);
            // $basePrice = ($amount / $data->qty) / ((100 - $data->disc) / 100);
            $totalPrice = ($amount / $data->qty) / ((100 - $data->disc) / 100);

            // $data->harga_barang = number_format($basePrice);
            $data->total_harga_barang = number_format($totalPrice);
        }

        // $totalPrice = 0;
        // $totalQty = 0;
        // foreach ($datas as &$data) {
        //     $amount = floatval($data->amount);
        //     $basePrice = ($amount / $data->qty) / ((100 - $data->disc) / 100);

        //     $amounts = $data->amount;
        //     $basePrices = ($amounts / $data->qty) / ((100 - $data->disc) / 100);

        //     $totalQty += floatval($data->qty);

        //     $data->amount = number_format($amount);
        //     $data->qty = number_format($data->qty);
        //     $data->harga_barang = number_format($basePrice);
        //     $totalPrice += $basePrices;
        // }
        // $datas['total_qty'] = $totalQty;
        // $datas['total_amount'] = $totalPrice;

        // var_dump($datas);

        return $datas;
    }
}
