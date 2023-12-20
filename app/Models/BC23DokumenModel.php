<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23DokumenModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23_dokumen';
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
        $result = [];
        foreach ($this->where('bc_23_id', $bc23ID)->where('deletedAt', null)->findAll() as $r) {
            $result[] = [
                'id' => $r['id'],
                'dokumen_pelengkap_kode_dokumen' => "Dokumen Invoice (3)",
                'dokumen_pelengkap_nomor_dokumen' => $r['kode_dokumen'],
                'dokumen_pelengkap_seri_dokumen' => $r['seri_dokumen'],
                'dokumen_pelengkap_tanggal_dokumen' => date('d/m/Y', strtotime($r['tanggal_dokumen']))
            ];
        }
        return $result;
    }
}
