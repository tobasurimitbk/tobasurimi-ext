<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23KontainerModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23_kontainer';
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
            $kodeJenisKontainer = $metaDataModel->bcMetaDataHelper("Jenis Kontainer", null, $r['kode_jenis_kontainer']);
            $kodeTipeKontainer = $metaDataModel->bcMetaDataHelper("Kode Tipe Kontainer BC", $r['kode_tipe_kontainer'], null);
            $kodeUkuranKontainer = $metaDataModel->bcMetaDataHelper("Kode Ukuran Kontainer BC", $r['kode_ukuran_kontainer'], null);
            $result[] = [
                'id' => $r['id'],
                'kontainer_kode_jenis_kontainer' => encrypt($r['kode_jenis_kontainer']),
                'kontainer_kode_jenis_kontainer_text' => $kodeJenisKontainer['description'] . ' (' . $kodeJenisKontainer['value'] . ')',
                'kontainer_kode_tipe_kontainer' => encrypt($r['kode_tipe_kontainer']),
                'kontainer_kode_tipe_kontainer_text' => $kodeTipeKontainer['value'] . ' (' . $kodeTipeKontainer['description'] . ')',
                'kontainer_kode_ukuran_kontainer' => encrypt($r['kode_ukuran_kontainer']),
                'kontainer_kode_ukuran_kontainer_text' => $kodeUkuranKontainer['value'] . ' (' . $kodeUkuranKontainer['description'] . ')',
                'kontainer_nomor_kontainer' => $r['nomor_kontainer'],
                'kontainer_seri_kontainer' => $r['seri_kontainer'],
            ];
        }
        return $result;
    }
}
