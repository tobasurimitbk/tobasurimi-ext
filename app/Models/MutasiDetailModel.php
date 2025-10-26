<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'mutasi_detail';
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

        $mutasiDetail = $this->asArray()
            ->select('mutasi_detail.*,satuans.kode_satuan AS kode_satuan_mutasi')
            ->join('satuans', 'satuans.id = mutasi_detail.unit_id_mutasi', 'left')
            ->where('mutasi_detail.mutasi_id', $id)
            ->where('mutasi_detail.deletedAt', null)
            ->findAll();

        $no = 1;
        foreach ($mutasiDetail as $a) {

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
                    'qty_diterima' => (float)$a['hasil_mutasi'] + $a['qty_mutasi'],
                    'kode_satuan' => $d['kode_satuan'],
                    "unit_id" => $d['unit_id'],
                    "mutasi" => [
                        "qty_mutasi" => $a['qty_mutasi'],
                        'unit_id_mutasi' => $a['unit_id_mutasi'],
                        'unit_name_mutasi' => $a['kode_satuan_mutasi'],
                        'qty_konversi' => $a['qty_konversi'],
                        'unit_id_konversi' => $a['unit_id_konversi'],
                        'unit_name_konversi' => $d['kode_satuan'],
                        'hasil_mutasi' => $a['hasil_mutasi']
                    ]
                ]);
            }
        }

        return $dataResult;
    }
}
