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

    public function getDetail(
        $id
    ) {
        $stockRevampModel = new StockRevampModel();
        $pbbkbDetailModel = new PPBKBDetailModel();
        $dataResult = array();

        $selectQry = "
            mutasi_detail.*,
            satuans.kode_satuan AS kode_satuan_mutasi
        ";

        $mutasiDetail = $this->asArray()
            ->select($selectQry)
            ->join('satuans', 'satuans.id = mutasi_detail.unit_id_mutasi', 'left')
            ->where('mutasi_detail.mutasi_id', $id)
            ->where('mutasi_detail.deletedAt', null)
            ->findAll();

        $no = 1;
        foreach ($mutasiDetail as $a) {

            $fromStock = $stockRevampModel->getStockListAll(
                ["id" => $a['stock_detail_id']],
                0,
                "desc",
                1
            );

            $ppbkbDetail = $pbbkbDetailModel
                ->select('ppbkb_detail.*,hs_codes.code')
                ->join('hs_codes', 'hs_codes.id = ppbkb_detail.hs_code_id', 'left')
                ->where('mutasi_detail_id', $a['id'])
                ->where('ppbkb_detail.deletedAt', null)
                ->first();

            foreach ($fromStock['data'] as $d) {
                // Stock
                array_push($dataResult, [
                    'no' => $no++,
                    'id_detail' => $a['id'],
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
                    "hs_code" => $ppbkbDetail == null ? "" : $ppbkbDetail['code'],
                    "hs_code_id" => $ppbkbDetail == null ? "" : $ppbkbDetail['hs_code_id'],
                    "mutasi" => [
                        "mutasi_id" => $a['mutasi_id'],
                        "mutasi_detail_id" => $a['id'],
                        "qty_mutasi" => $a['qty_mutasi'],
                        'unit_id_mutasi' => $a['unit_id_mutasi'],
                        'unit_name_mutasi' => $a['kode_satuan_mutasi'],
                        'qty_konversi' => $a['qty_konversi'],
                        'unit_id_konversi' => $a['unit_id_konversi'],
                        'unit_name_konversi' => $d['kode_satuan'],
                        'hasil_mutasi' => $a['hasil_mutasi']
                    ],
                    "dokumen_asal" => [
                        'no_aju' => $d['no_aju'],
                        'no_daftar' => $d['no_daftar'],
                        'tanggal_dokumen' => $d['tanggal_dokumen']
                    ],
                    "keterangan_mutasi" => $a['keterangan'],
                    "spp_no" => $d['spp_no']
                ]);
            }
        }

        return $dataResult;
    }
}
