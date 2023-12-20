<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23EntitasModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23_entitas';
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

    public function get($bc23ID)
    {
        $metaDataModel = new MetadataModel();

        $result = [];
        foreach ($this->where('bc_23_id', $bc23ID)->where('deletedAt', null)->findAll() as $r) {
            $kodeJenisIdentitas = $metaDataModel->bcMetaDataHelper("Jenis Identitas", null, $r['kode_jenis_entitas']);
            $result[] = [
                'id' => $r['id'],
                'entitas_alamat_entitas' => $r['alamat_entitas'],
                'entitas_kode_entitas' => "Pengusaha (3)",
                'entitas_kode_jenis_identitas' => encrypt($r['kode_jenis_entitas']),
                'entitas_kode_jenis_identitas_text' => $kodeJenisIdentitas['description'] . ' (' . $kodeJenisIdentitas['value'] . ')',
                'entitas_nama_entitas' => $r['nama_entitas'],
                'entitas_nib_entitas' => $r['nib_entitas'],
                'entitas_nomor_identitas' => $r['nomor_identitas'],
                'entitas_nomor_ijin_entitas' => $r['nomor_ijin_entitas'],
                'entitas_seri_entitas' => $r['seri_entitas'],
                'entitas_tanggal_ijin_entitas' => date('d/m/Y', strtotime($r['tanggal_ijin_entitas']))
            ];
        }
        return $result;
    }
}
