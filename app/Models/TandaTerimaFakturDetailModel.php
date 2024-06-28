<?php

namespace App\Models;

use CodeIgniter\Model;

class TandaTerimaFakturDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tanda_terima_faktur_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tanda_terima_faktur_id',
        'penerimaan_barang_detail_id',
        'lpb_date',
        'lpb_no',
        'po_no',
        'item_name',
        'unit',
        'qty',
        'price',
        'price_single'
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

    public function getListTandaTerimaItemFaktur($tandaTerimaFakturID)
    {
        $condition = [
            'tanda_terima_faktur_detail.tanda_terima_faktur_id' => $tandaTerimaFakturID,
            'tanda_terima_faktur_detail.deletedAt' => null
        ];
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $res = $tandaTerimaFakturDetailModel->where($condition)->findAll();
        return $res;
    }

    public function getDetail($tandaTerimaFakturID)
    {
        $condition = [
            'tanda_terima_faktur_detail.tanda_terima_faktur_id' => $tandaTerimaFakturID,
            'tanda_terima_faktur_detail.deletedAt' => null
        ];
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $data = $tandaTerimaFakturDetailModel->where($condition)->findAll();
        $result = [];

        foreach ($data as $d) {
            $result[] = [
                'penerimaan_barang_detail_id' => $d['penerimaan_barang_detail_id'],
                'po_no' => $d['po_no'],
                'tanggal' => date('d/m/Y', strtotime($d['lpb_date'])),
                'no_penerimaan_barang' => $d['lpb_no'],
                'nama_barang_dok' => $d['item_name'],
                'qty_lpb' => 0,
                'qty_retur' => 0,
                'qty_telah_diterima' => 0,
                'qty_akan_diterima' => $d['qty'],
                'kode_satuan' => $d['unit'],
                'harga' => $d['price_single']
            ];
        }


        return $result;
    }
}
