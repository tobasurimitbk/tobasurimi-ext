<?php

namespace App\Models;

use CodeIgniter\Model;

class FormLemburModel extends Model
{
    protected $DBGroup           = 'default';
    protected $table            = 'form_lembur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'employee_id',
        'division_id',
        'periode',
        'total_jam_lembur',
        'total_uang_lembur',
        'kurangi_jam_istirahat',
        'jam_mulai_lembur',
        'jam_selesai_lembur',
        'gaji_pokok_per_hari',
        'is_payroll'
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'employees.nip' => 'employees.nip',
            'employees.name' => 'employees.name',
            'divisis.divisi' => 'divisis.divisi',
            'form_lembur.periode' => 'form_lembur.periode',
            'form_lembur.total_jam_lembur' => 'form_lembur.total_jam_lembur',
            'form_lembur.total_uang_lembur' => 'form_lembur.total_uang_lembur',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        // Ensure the sort and sortType values are valid
        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'form_lembur.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            form_lembur.*,
            employees.name AS employeesName,
            employees.nip AS employeesNIP,
            divisis.divisi AS divisiName
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = form_lembur.employee_id', 'INNER')
            ->join('divisis', 'divisis.id = employees.division_id', 'LEFT')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry->like('employees.nip', $addCondition['search'])
                ->orLike('employees.name', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function rekap($employeeID, $yearMonth)
    {
        return $this->asArray()->where('employee_id', $employeeID)
            ->where('LEFT(periode, 7)', $yearMonth)
            ->findAll();
    }

    public function getTotalLemburJamPertamaKedua($employeeID, $yearMonth)
    {
        $formLemburModel = new FormLemburModel();
        $lembur = $formLemburModel->rekap($employeeID, $yearMonth);
        $totalLembur = count($lembur);
        $totalJam = 0;
        $lemburJamPertama = 0;
        $lemburJamKedua = 0;

        foreach ($lembur as $l) {
            $totalJam += $l['total_jam_lembur'];
        }

        for ($i = 0; $i < $totalLembur; $i++) {
            $totalJam--;
            $lemburJamPertama++;
            $lemburJamKedua = $totalJam;
        }

        return [
            'jamPertama' => $lemburJamPertama,
            'jamKedua' => $lemburJamKedua
        ];
    }
}
