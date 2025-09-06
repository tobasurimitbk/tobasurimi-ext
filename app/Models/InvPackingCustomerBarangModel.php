<?php

namespace App\Models;

use CodeIgniter\Model;

class InvPackingCustomerBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inv_packing_customer_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function getByInvId($id)
    {
        $invPackingCustomerBarangSizeModel = new InvPackingCustomerBarangSizeModel();

        $resultFinal = [];
        $invPCBarang = $this->where('inv_packing_customer_id', $id)->findAll();
        foreach ($invPCBarang as $i) {

            $invPCBarangSize = $invPackingCustomerBarangSizeModel
                ->select('inv_packing_customer_barang_size.*,satuans.kode_satuan as satuan_size_code')
                ->join('satuans', 'satuans.id = inv_packing_customer_barang_size.satuan_size_id', 'left')
                ->where('inv_packing_customer_barang_id', $i['id'])
                ->where('inv_packing_customer_barang_size.deletedAt', null)
                ->findAll();

            $resultBarang = [
                'id_barang' => $i['id'],
                'nama_barang' => $i['nama_barang'],
                'catatan' => $i['catatan'],
                'size_breakdown' => []
            ];

            foreach ($invPCBarangSize as $s) {
                array_push($resultBarang['size_breakdown'], [
                    'id_detail_breakdown' => $s['id'],
                    'size' => $s['size'],
                    'grade' => $s['grade'],
                    'packing' => $s['packing'],
                    'can' => $s['can'],
                    'cased' => $s['cased'],
                    'kg' => $s['kg'],
                    'lb' => $s['lb'],
                    'inner_box' => $s['inner_box'],
                    'pc' => $s['pc'],
                    'bag' => $s['bag'],
                    'palet' => $s['palet'],
                    'persen' => $s['persen'],
                    'qty' => (float)$s['qty'],
                    'harga' => (float)$s['harga'],
                    'total' => (float)$s['total'],
                    'remark' => $s['remark'],
                    'satuan_size_id' => $s['satuan_size_id'],
                    'satuan_size_code' => $s['satuan_size_code']
                ]);
            }

            array_push($resultFinal, $resultBarang);
        }

        return $resultFinal;
    }
}
