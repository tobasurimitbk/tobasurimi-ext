<?php

namespace App\Models;

use CodeIgniter\Model;

class InvPackingBcBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inv_packing_bc_barang';
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
        $resultFinal = [];
        $invPCBarang = $this
            ->asArray()
            ->select(
                '
                inv_packing_bc_barang.*,
                hs_codes.code,
                hs_codes.uraian_barang,
                satuans.kode_satuan'
            )
            ->join('satuans', 'satuans.id = inv_packing_bc_barang.satuan_id', 'left')
            ->join('hs_codes', 'hs_codes.id = inv_packing_bc_barang.hs_code_id', 'left')
            ->where('inv_packing_bc_id', $id)
            ->findAll();

        foreach ($invPCBarang as $i) {
            $resultFinal[] = [
                'id_barang' => $i['id'],
                'nama_barang' => $i['nama_barang'],
                'hs_code' => $i['hs_code_id'],
                'hs_code_name' => $i['code'] . " - " . $i['uraian_barang'],
                'qty' => (float)$i['qty'],
                'satuan_id' => $i['satuan_id'],
                'kode_satuan' => $i['kode_satuan'],
                'harga_satuan_barang' => (float)$i['harga_satuan_barang'],
                'total_harga_barang' => (float)$i['total_harga_barang'],
                'catatan' => $i['catatan']
            ];
        }

        return $resultFinal;
    }
}
