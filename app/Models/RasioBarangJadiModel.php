<?php

namespace App\Models;

use CodeIgniter\Model;

class RasioBarangJadiModel extends Model
{
    protected $table = 'rasio_barang_jadi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $DBGroup          = 'default';
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields = [];

    // Dates
    protected $useTimestamps = true;
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

    public function getDataRasioMaterialI($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
        rasio_barang_jadi.*
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('rasio', 'rasio.id = rasio_barang_jadi.rasio_id', 'left')
            ->like('rasio.bulan', $where['tanggal_jurnal'])
            ->where('rasio.company_id', $where['company_id'])
            ->where('rasio.department_id', $where['divisi_id'])
            ->where('rasio.deletedAt', $where['deletedAt'])
            ->where('rasio_barang_jadi.deletedAt', $where['deletedAt'])
            ->findAll();

        return $dataQry;
    }
}
