<?php

namespace App\Models;

use CodeIgniter\Model;

class FormPerijinanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'form_perijinan';
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

    public function getPerijinanList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'employees.nip' => 'employees.nip',
            'employees.name' => 'employees.name',
            'employees.division_id' => 'employees.division_id',
            'form_perijinan.status' => 'form_perijinan.status',
            'form_perijinan.reason' => 'form_perijinan.reason',
            'form_perijinan.is_approval' => 'form_perijinan.is_approval'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'form_perijinan.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $formPerijinanQry = $this->asObject()
            ->select("DISTINCT (form_perijinan.kode), employees.*, divisis.divisi, form_perijinan.status, form_perijinan.is_approval, form_perijinan.reason")
            ->join('employees', 'employees.id = form_perijinan.employee_id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $formPerijinanQry->countAllResults(false);

        if ($addCondition['search']) {
            $formPerijinanQry->groupStart();
        }

        if ($addCondition['search']) {
            $formPerijinanQry
                ->like('employees.nip', $addCondition['search'])
                ->orLike('employees.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $formPerijinanQry->groupEnd();
        }

        $totalFilteredData = $formPerijinanQry->countAllResults(false);
        $data = $formPerijinanQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getPerijinanById($id)
    {

        $selectQry = "form_perijinan.* ,
            employees.name AS employeeName,
            employees.nip AS employeeNip,
            divisis.divisi AS divisionName
            ";

        $formPerijinanQry = $this->asObject()
            ->select($selectQry)
            ->join('employees', 'form_perijinan.employee_id = employees.id')
            ->join('divisis', 'employees.division_id = divisis.id')
            ->find($id);

        return $formPerijinanQry;
    }

    public function getPerijinanAmt($id, $year, $month)
    {
        $date = $year . "-" . $month;
        $selectQry = "form_perijinan.periode,
            form_perijinan.status,
            employees.name AS employeeName,
            employees.nip AS employeeNip,
            divisis.divisi AS divisionName
            ";

        $formPerijinanQry = $this->asObject()
            ->select($selectQry)
            ->join('employees', 'form_perijinan.employee_id = employees.id')
            ->join('divisis', 'employees.division_id = divisis.id')
            ->where('employee_id', $id)
            ->like('periode', $date)
            ->orderBy('periode', 'DESC');


        $data = $formPerijinanQry->findAll();

        return $data;
    }

    public function getTotalPerijinanByStatus($employeeID, $status, $year, $month)
    {
        $formPerijinanQry = $this->asObject()
            ->where('LEFT(periode, 7)', $year . "-" . $month)
            ->where('status', $status)
            ->where('employee_id', $employeeID)
            ->countAllResults();

        return $formPerijinanQry;
    }
}
