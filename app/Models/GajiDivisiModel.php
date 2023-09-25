<?php

namespace App\Models;

use CodeIgniter\Model;

class GajiDivisiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'gaji_divisi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tunjangan_id',
        'division_id',
        'company_id',
        'nominal'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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

    public function getGajiByDivision($divisionID, $companyID)
    {
        return $this->asArray()->select('tunjangan.*')
            ->join('tunjangan', 'tunjangan.id = gaji_divisi.tunjangan_id')
            ->where('tunjangan.deletedAt', null)
            ->where('gaji_divisi.division_id', $divisionID)
            ->where('gaji_divisi.company_id', $companyID)
            ->findAll();
    }

    public function getGajiByDivisionReturnIDOnArray($divisionID, $companyID)
    {
        $id = [];
        $data = $this->asArray()->select('tunjangan.*')
            ->join('tunjangan', 'tunjangan.id = gaji_divisi.tunjangan_id')
            ->where('tunjangan.deletedAt', null)
            ->where('gaji_divisi.division_id', $divisionID)
            ->where('gaji_divisi.company_id', $companyID)
            ->findAll();

        foreach ($data as $d) {
            \array_push($id, $d['id']);
        }

        return $id;
    }

    public function getGajiByDivisionAndEmployee($divisionID, $companyID, $employeeID)
    {
        $data = $this->asArray()->select('tunjangan.*')
            ->join('tunjangan', 'tunjangan.id = gaji_divisi.tunjangan_id')
            ->where('tunjangan.deletedAt', null)
            ->where('gaji_divisi.division_id', $divisionID)
            ->where('gaji_divisi.company_id', $companyID)
            ->findAll();

        $res = [];

        $gajiConjunctionModel = new GajiConjunctionModel();

        foreach ($data as $d) {
            $gajiConjunction = $gajiConjunctionModel
                ->where('tunjangan_id', $d['id'])
                ->where('employee_id', $employeeID)
                ->first();

            $res[] = [
                'id' => $d['id'],
                'nominal' => $gajiConjunction == null ? '0' : $gajiConjunction['nominal'],
                'name' => $d['name'],
                'tipe' => $d['tipe']
            ];
        }

        return $res;
    }
}
