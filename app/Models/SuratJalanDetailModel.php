<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanDetailModel extends Model
{

    protected $table      = 'surat_jalan_so_detail';
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
        if (!is_array($ids)) $ids = [$ids];

        $selectQry = "surat_jalan_so_detail.id AS id,
                      surat_jalan_so_detail.id AS id_detail_sj,
                      surat_jalan_so.id AS id_sj,
                      surat_jalan_so_detail.id_barang AS id_barang,
                      barang_master_sales.kode_barang AS kode_barang,
                      barang_master_sales.barang_name AS nama_barang,
                      surat_jalan_so_detail.qty AS qty,
                      surat_jalan_so_detail.qty_sekarang AS qty_sekarang,
                      satuans.kode_satuan AS satuan,
                      surat_jalan_so_detail.discount_percentage AS disc,
                      surat_jalan_so_detail.discount_unit AS discUnit,
                      surat_jalan_so_detail.tax AS tax,
                      surat_jalan_so_detail.amount AS amount,
                      surat_jalan_so_detail.id_surat_jalan,
                      surat_jalan_so_detail.id_sales_order,
                      surat_jalan_so_detail.id_sales_order_detail,
                      surat_jalan_so_detail.harga_barang AS harga_barang,
                      surat_jalan_so_detail.status_ppn AS statusppn,
                      surat_jalan_so_detail.tipe_input AS tipe_input,
                      surat_jalan_so_detail.keterangan AS keterangan,
                      surat_jalan_so_detail.*,
                      surat_jalan_so.id_company,
                      sales_order.no_sales_order,
                      surat_jalan_so.no_surat_jalan AS no_surat_jalan";

        $datas = $this->asObject()
            ->select($selectQry)
            ->join('barang_master_sales', 'barang_master_sales.id = surat_jalan_so_detail.id_barang AND barang_master_sales.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = surat_jalan_so_detail.id_surat_jalan', 'LEFT')
            ->join('sales_order', 'sales_order.id = surat_jalan_so_detail.id_sales_order', 'LEFT')
            ->whereIn('id_surat_jalan', $ids)
            ->orderBy('surat_jalan_so_detail.id_surat_jalan', 'ASC')
            ->orderBy('barang_master_sales.barang_name', 'ASC')
            ->findAll();

        foreach ($datas as &$data) {
            $amount = ($data->amount);
            $totalPrice = (($amount) * (100 - $data->disc)) / 100;
            $data->total_harga_barang = $amount;
        }
        return $datas;
    }


    public function getItemListPostingByIds($ids): array
    {

        if (!is_array($ids)) $ids = [$ids];

        $selectQry = "surat_jalan_so_detail.id AS id,
                      surat_jalan_so_detail.id AS id_detail_sj,
                      surat_jalan_so.id AS id_sj,
                    surat_jalan_so_detail.id_barang AS id_barang,
                    barang_master_sales.kode_barang AS kode_barang,
                      barang_master_sales.barang_name AS nama_barang,
                      surat_jalan_so_detail.qty AS qty,
                      surat_jalan_so_detail.qty_sekarang AS qty_sekarang,
                      satuans.kode_satuan AS satuan,
                      surat_jalan_so_detail.discount_percentage AS disc,
                      surat_jalan_so_detail.discount_unit AS discUnit,
                      surat_jalan_so_detail.tax AS tax,
                      surat_jalan_so_detail.amount AS amount,
                      surat_jalan_so_detail.id_surat_jalan,
                      surat_jalan_so_detail.harga_barang AS harga_barang";

        $datas = $this->asObject()
            ->select($selectQry)
            ->join('barang_master_sales', 'barang_master_sales.id = surat_jalan_so_detail.id_barang AND barang_master_sales.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            ->join('surat_jalan_so', 'surat_jalan_so.id = surat_jalan_so_detail.id_surat_jalan', 'LEFT')
            // ->where('qty_sekarang !=', 0)
            // ->where('tipe_input', 'order_form')
            ->whereIn('id_surat_jalan', $ids)
            ->orderBy('surat_jalan_so_detail.id_surat_jalan', 'ASC')
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
