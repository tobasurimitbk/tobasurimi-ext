<?php

namespace App\Models;

use CodeIgniter\Model;

class AdjusmentDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'adjusment_detail';
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

    public function getDetail($id)
    {
        $stockRevampModel = new StockRevampModel();
        $dataResult = array();
        $adjusmentDetail = $this->asArray()
            ->select('adjusment_detail.*,satuans.kode_satuan AS kode_satuan_adjusment')
            ->join('satuans', 'satuans.id = adjusment_detail.unit_id_adjusment', 'left')
            ->where('adjusment_detail.adjusment_id', $id)
            ->where('adjusment_detail.deletedAt', null)
            ->findAll();

        $no = 1;
        foreach ($adjusmentDetail as $a) {

            $fromStock = $stockRevampModel->getStockListAll(
                ["id" => $a['stock_detail_id']],
                0,
                "desc"
            );

            foreach ($fromStock['data'] as $d) {
                // Stock
                array_push($dataResult, [
                    'no' => $no++,
                    'id' => $d['id'],
                    'divisi' => $d['divisi'],
                    'warehouse_name' => $d['warehouse_name'],
                    'reference_type' => $d['reference_type'],
                    'supplier_name' => $d['supplier_name'],
                    'kode_barang' => $d['kode_barang'],
                    'barang_name' => $d['barang_name'],
                    'spesifikasi' => $d['spesifikasi'],
                    'type_bc' => $d['type_bc'],
                    'po_no' => $d['po_no'],
                    'po_date' => !empty($d['po_date']) ? date('d/m/Y', strtotime($d['po_date'])) : "",
                    'lpb_date' => !empty($d['lpb_date']) ? date('d/m/Y', strtotime($d['lpb_date'])) : "",
                    'reference_no' => $d['reference_no'],
                    'qty_diterima' => (float)$a['qty_asal'], // pakai asal sebelum di adjusment
                    'kode_satuan' => $d['kode_satuan'],
                    "unit_id" => $d['unit_id'],
                    "adjusment" => [
                        "operasi_adjusment_detail" => $a['operasi_adjusment_detail'],
                        "qty_adjusment" => $a['qty_adjusment'],
                        'unit_id_adjusment' => $a['unit_id_adjusment'],
                        'unit_name_adjusment' => $a['kode_satuan_adjusment'],
                        'qty_konversi' => $a['qty_konversi'],
                        'unit_id_konversi' => $a['unit_id_konversi'],
                        'unit_name_konversi' => $d['kode_satuan'],
                        'hasil_adjusment' => $a['hasil_adjusment']
                    ]
                ]);
            }
        }

        return $dataResult;
    }
}
