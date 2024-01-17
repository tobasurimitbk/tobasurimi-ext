<?php

namespace App\Models;

use CodeIgniter\Model;

class BCBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_barang';
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

    public function totalHargaPenyerahan($penerimaanBarangID)
    {
        $total = 0;
        foreach ($this->asArray()->where('penerimaan_barang_id', $penerimaanBarangID)->findAll() as $t) {
            $total += $t['harga_ekspor'];
        }
        return $total;
    }

    public function totalBeratBersih($penerimaanBarangID)
    {
        $total = 0;
        foreach ($this->asArray()->where('penerimaan_barang_id', $penerimaanBarangID)->findAll() as $t) {
            $total += $t['netto'];
        }
        return $total;
    }
}
