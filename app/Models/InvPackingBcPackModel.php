<?php

namespace App\Models;

use CodeIgniter\Model;

class InvPackingBcPackModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inv_packing_bc_pack';
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
    protected $ikipValidation       = false;
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
        $invPCPack = $this->getInvPack($id);

        foreach ($invPCPack as $i) {
            $resultFinal[] = [
                'id_packing' => $i['id'],
                'nama_barang' => $i['nama_barang'],
                'hs_code' => $i['hs_code_id'],
                'hs_code_name' => $i['code'] . " - " . $i['uraian_barang'],
                'qty' => (float)$i['qty'],
                'satuan_id' => $i['satuan_id'],
                'kode_satuan' => $i['kode_satuan'],
                'harga_satuan_barang' => (float)$i['harga_satuan_barang'],
                'total_harga_barang' => (float)$i['total_harga_barang'],
                'catatan' => $i['catatan'],
                'size_breakdown' => [
                    'can' => (float)$i['can'],
                    'case' => (float)$i['cased'],
                    'kg' => (float)$i['kg'],
                    'lb' => (float)$i['lb'],
                    'inner_box' => (float)$i['inner_box'],
                    'pc' => (float)$i['pc'],
                    'bag' => (float) $i['bag'],
                    'persen' => (float)$i['persen'],
                    'cup' => (float)$i['cup'],
                    'palet' => (float)$i['palet'],
                    'packing' => (float)$i['packing'],
                    'berat_bersih' => (float)$i['berat_bersih'],
                    'berat_kotor' => (float)$i['berat_kotor'],
                    'vgm' => (float)$i['vgm'],
                    'drammed' => (float)$i['drammed'],
                ]
            ];
        }

        return $resultFinal;
    }

    public function getByInvIdPrint($id)
    {
        $resultFinal = [];
        $invPCPack = $this->getInvPack($id);
        foreach ($invPCPack as $i) {
            $sizeBreakdown = [];
            $sizeBreakdown[] = [
                'can' => (float)$i['can'],
                'case' => (float)$i['cased'],
                'kg' => (float)$i['kg'],
                'lb' => (float)$i['lb'],
                'inner_box' => (float)$i['inner_box'],
                'pc' => (float)$i['pc'],
                'bag' => (float) $i['bag'],
                'persen' => (float)$i['persen'],
                'cup' => (float)$i['cup'],
                'palet' => (float)$i['palet'],
                'packing' => (float)$i['packing'],
                'berat_bersih' => (float)$i['berat_bersih'],
                'berat_kotor' => (float)$i['berat_kotor'],
                'vgm' => (float)$i['vgm'],
                'drammed' => (float)$i['drammed'],
            ];

            $resultFinal[] = [
                'id_packing' => $i['id'],
                'nama_barang' => $i['nama_barang'],
                'hs_code' => $i['hs_code_id'],
                'hs_code_name' => $i['code'] . " - " . $i['uraian_barang'],
                'qty' => (float)$i['qty'],
                'satuan_id' => $i['satuan_id'],
                'kode_satuan' => $i['kode_satuan'],
                'harga_satuan_barang' => (float)$i['harga_satuan_barang'],
                'total_harga_barang' => (float)$i['total_harga_barang'],
                'catatan' => $i['catatan'],
                'size_breakdown' => $sizeBreakdown
            ];
        }
        return $resultFinal;
    }

    private function getInvPack($id)
    {
        $invPCPackList = $this
            ->asArray()
            ->select(
                '
                inv_packing_bc_pack.*,
                hs_codes.code,
                hs_codes.uraian_barang,
                satuans.kode_satuan'
            )
            ->join('satuans', 'satuans.id = inv_packing_bc_pack.satuan_id', 'left')
            ->join('hs_codes', 'hs_codes.id = inv_packing_bc_pack.hs_code_id', 'left')
            ->where('inv_packing_bc_id', $id)
            ->findAll();

        return $invPCPackList;
    }
}
