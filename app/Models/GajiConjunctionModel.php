<?php

namespace App\Models;

use CodeIgniter\Model;

class GajiConjunctionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'gaji_conjunction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "id",
        "employee_id",
        "tunjangan_id",
        "nominal"
    ];

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

    public function getKomponenByEmployeeId($id)
    {
        $arrCondition = [
            'gaji_conjunction.deletedAt' => null,
            'gaji_conjunction.employee_id' => $id
        ];

        $builder = $this->db->table('gaji_conjunction')
            ->select('gaji_conjunction.*, tunjangan.name AS tunjangan_name')
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getNominalByKomponenName($komponenName, $employeeID)
    {
        $res =  $this->asArray()->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
            ->where('employee_id', $employeeID)
            ->like('tunjangan.name', $komponenName)
            ->first();

        return ($res == null) ? 0 : $res['nominal'];
    }
}
