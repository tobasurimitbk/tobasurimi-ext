<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23KemasanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23_kemasan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
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
            $kodeJenisKemasan = $metaDataModel->bcMetaDataHelper("Jenis Kemasan", null, $r['kode_jenis_kemasan']);
            $result[] = [
                'id' => $r['id'],
                'kemasan_jumlah_kemasan' => $r['jumlah_kemasan'],
                'kemasan_kode_jenis_kemasan' => encrypt($r['kode_jenis_kemasan']),
                'kemasan_kode_jenis_kemasan_text' => $kodeJenisKemasan['description'] . ' (' . $kodeJenisKemasan['value'] . ')',
                'kemasan_merk_kemasan' => $r['merk_kemasan'],
                'kemasan_seri_kemasan' => $r['seri_kemasan'],
            ];
        }
        return $result;
    }
}
