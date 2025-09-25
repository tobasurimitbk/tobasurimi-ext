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

    public function updateBulkGajiHarian($divisiId)
    {
        $gajiDivisiModel = new GajiDivisiModel();
        $gajiDivisi = $gajiDivisiModel
            ->select('gaji_divisi.*')
            ->join('tunjangan', 'tunjangan.id = gaji_divisi.tunjangan_id', 'left')
            ->where('tunjangan.is_gaji_harian', 1)
            ->where('gaji_divisi.division_id', $divisiId)
            ->where('gaji_divisi.deletedAt', null)
            ->first();

        if ($gajiDivisi != null) {
            $gajiConjunctionList =  $this
                ->select('gaji_conjunction.*')
                ->join('employees', 'employees.id = gaji_conjunction.employee_id', 'left')
                ->where('gaji_conjunction.deletedAt', null)
                ->where('employees.division_id', $divisiId)
                ->where('gaji_conjunction.tunjangan_id', $gajiDivisi['tunjangan_id'])
                ->findAll();

            $updatedData = array();
            foreach ($gajiConjunctionList as $g) {
                array_push($updatedData, [
                    'nominal' => $gajiDivisi['nominal'],
                    'id' => $g['id']
                ]);
            }

            if (count($updatedData) != 0) {
                $this->updateBatch($updatedData, 'id');
            }
        }
    }
}
