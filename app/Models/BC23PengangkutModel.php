<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23PengangkutModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23_pengangkut';
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
            $kodeCaraAngkut = $metaDataModel->bcMetaDataHelper("Pengangkutan", null, $r['kode_cara_angkut']);

            $result[] = [
                'id' => $r['id'],
                'pengangkut_kode_bendera' => $r['kode_bendera'],
                'pengangkut_kode_cara_angkut' => encrypt($r['kode_cara_angkut']),
                'pengangkut_kode_cara_angkut_text' => $kodeCaraAngkut['description'] . ' (' . $kodeCaraAngkut['value'] . ')',
                'pengangkut_nama_sarana_pengangkut' => $r['nama_sarana_pengangkut'],
                'pengangkut_nomor_pengangkut' => $r['nomor_pengangkut'],
                'pengangkut_seri_pengangkut' => $r['seri_pengangkut'],
            ];
        }
        return $result;
    }
}
