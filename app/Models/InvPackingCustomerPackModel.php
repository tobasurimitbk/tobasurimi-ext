<?php

namespace App\Models;

use CodeIgniter\Model;

class InvPackingCustomerPackModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inv_packing_customer_pack';
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
        $invPackingCustomerPackSizeModel = new InvPackingCustomerPackSizeModel();

        $resultFinal = [];
        $invPCPack = $this->select('inv_packing_customer_pack.*,hs_codes.code,hs_codes.uraian_barang')
            ->join('hs_codes', 'hs_codes.id = inv_packing_customer_pack.hs_code_id', 'left')
            ->where('inv_packing_customer_id', $id)
            ->findAll();
        foreach ($invPCPack as $i) {

            $invPCPackSize = $invPackingCustomerPackSizeModel
                ->select('inv_packing_customer_pack_size.*,satuans.kode_satuan as satuan_size_code')
                ->join('satuans', 'satuans.id = inv_packing_customer_pack_size.satuan_size_id', 'left')
                ->where('inv_packing_customer_pack_id', $i['id'])
                ->where('inv_packing_customer_pack_size.deletedAt', null)
                ->findAll();

            $resultPack = [
                'id_packing' => $i['id'],
                'nama_barang_packing' => $i['nama_barang_packing'],
                'hs_code' => $i['hs_code_id'],
                'hs_code_name' => $i['code'] . " - " . $i['uraian_barang'],
                'keterangan_packing' => $i['keterangan_packing'],
                'size_breakdown' => [],
            ];

            foreach ($invPCPackSize as $s) {
                array_push($resultPack['size_breakdown'], [
                    'id_detail_breakdown_packing' => $s['id'],
                    'size' => $s['size'],
                    'grade' => $s['grade'],
                    'packing' => (float)$s['packing'],
                    'can' => (float)$s['can'],
                    'kg' => (float)$s['kg'],
                    'lb' => (float)$s['lb'],
                    'inner_box' => (float)$s['inner_box'],
                    'pc' => (float)$s['pc'],
                    'bag' => (float) $s['bag'],
                    'palet' => (float)$s['palet'],
                    'persen' => (float)$s['persen'],
                    'qty' => (float)$s['qty'],
                    'harga' => (float)$s['harga'],
                    'total' => (float)$s['total'],
                    'satuan_size_id' => $s['satuan_size_id'],
                    'satuan_size_code' => $s['satuan_size_code'],
                    'berat_bersih' => (float)$s['berat_bersih'],
                    'berat_kotor' => (float)$s['berat_kotor'],
                    'vgm' => (float)$s['vgm'],
                    'drammed' => (float)$s['drammed'],
                    'case' => (float)$s['cased'],
                    'cup' => (float)$s['cup']


                ]);
            }

            array_push($resultFinal, $resultPack);
        }

        return $resultFinal;
    }
}
