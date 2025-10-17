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
        'price_single',
        'divisi_id'
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
        // Ubah string "309,396,397,472" jadi array
        $ids = array_filter(array_map('trim', explode(',', $tandaTerimaFakturID)));

        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();

        $res = $tandaTerimaFakturDetailModel
            ->select('
                tanda_terima_faktur_detail.*, 
                tanda_terima_faktur.faktur_no
            ')
            ->join(
                'tanda_terima_faktur',
                'tanda_terima_faktur.id = tanda_terima_faktur_detail.tanda_terima_faktur_id',
                'left'
            )
            ->whereIn('tanda_terima_faktur_detail.tanda_terima_faktur_id', $ids)
            ->where('tanda_terima_faktur_detail.deletedAt', null)
            ->findAll();

        return $res;
    }


    public function getDetail($tandaTerimaFakturID)
    {
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();

        $condition = [
            'tanda_terima_faktur_detail.tanda_terima_faktur_id' => $tandaTerimaFakturID,
            'tanda_terima_faktur_detail.deletedAt' => null
        ];

        $selectQry = "
            tanda_terima_faktur_detail.*,
            divisis.divisi,
            suppliers.name AS supplier_name,
            tanda_terima_faktur.supplier_id
        ";

        $data = $tandaTerimaFakturDetailModel
            ->select($selectQry)
            ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = tanda_terima_faktur_detail.tanda_terima_faktur_id', 'left')
            ->join('suppliers', 'suppliers.id = tanda_terima_faktur.supplier_id', 'left')
            ->join('divisis', 'divisis.id = tanda_terima_faktur_detail.divisi_id', 'left')
            ->where($condition)
            ->findAll();

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
                'supplier_name' => $d['supplier_name'],
                'harga' => $d['price_single'],
                'harga_total' => $d['price'],
                'divisi_id' =>  $d['divisi_id'],
                'supplier_id' =>  $d['supplier_id'],
                'divisi_name' => $d['divisi']
            ];
        }


        return $result;
    }
}
