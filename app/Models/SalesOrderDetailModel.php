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
        'id_surat_jalan',
        'id_sales_order',
        'id_sales_order_detail',
        'id_barang',
        'qty',
        'qty_sekarang',
        'keterangan',
        'discount_percentage',
        'discount_unit',
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
        // pastikan selalu array
        if (!is_array($ids)) $ids = [$ids];

        // jika array kosong, langsung kembalikan array kosong
        if (empty($ids)) {
            return [];
        }

        $selectQry = "sales_order_detail.id AS id,
                  sales_order_detail.id AS id_sales_order_detail,
                  sales_order_detail.id_sales_order AS id_sales_order,
                  sales_order_detail.id_barang AS id_barang,
                  barang_master_sales.kode_barang AS kode_barang,
                  barang_master_sales.barang_name AS nama_barang,
                  sales_order_detail.qty AS qty,
                  sales_order_detail.qty_sekarang AS qty_sekarang,
                  satuans.kode_satuan AS satuan,
                  sales_order_detail.discount_percentage AS disc,
                  sales_order_detail.discount_percentage AS discAmt,
                  sales_order_detail.discount_unit AS discUnit,
                  sales_order_detail.tax AS tax,
                  sales_order_detail.amount AS amount,
                  sales_order_detail.id_sales_order,
                  sales_order_detail.harga_barang AS harga_barang,
                  sales_order_detail.status_ppn AS statusppn,
                  sales_order_detail.keterangan AS keterangan,
                  sales_order_detail.tipe_input AS tipe_input,
                  sales_order.id_company";

        $datas = $this->asObject()
            ->select($selectQry)
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang AND barang_master_sales.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            ->join('sales_order', 'sales_order.id = sales_order_detail.id_sales_order', 'LEFT')
            ->where('tipe_input', 'order_form')
            ->whereIn('id_sales_order', $ids)
            ->orderBy('sales_order_detail.id_sales_order', 'ASC')
            ->orderBy('barang_master_sales.barang_name', 'ASC')
            ->findAll();

        foreach ($datas as &$data) {
            $amount = $data->amount;
            $data->total_harga_barang = $amount; // atau totalPrice jika mau hitung diskon
        }

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
                      sales_order_detail.discount_unit AS discUnit,
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
            $totalPrice = ($amount / $data->qty) / ((100 - $data->disc) / 100);
            $data->total_harga_barang = number_format($totalPrice);
        }

        return $datas;
    }
}
