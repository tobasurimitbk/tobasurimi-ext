<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollCustomGajiHarianModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payroll_custom_gaji_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'employees.nip' => 'employees.nip',
            'employees.name' => 'employees.name',
            'employees.division_id' => 'employees.division_id',
            'employees.bagian_id' => 'employees.bagian_id',
            'payroll_custom_gaji_harian.tanggal' => 'payroll_custom_gaji_harian.tanggal',
            'payroll_custom_gaji_harian.nominal' => 'payroll_custom_gaji_harian.nominal',
            'payroll_custom_gaji_harian.keterangan' => 'payroll_custom_gaji_harian.keterangan',
            'payroll_custom_gaji_harian.checkin' => 'payroll_custom_gaji_harian.checkin',
            'payroll_custom_gaji_harian.checkout' => 'payroll_custom_gaji_harian.checkout',

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        // Ensure the sort and sortType values are valid
        $sort = $availableSort[$addCondition['sort'] ?? 'payroll_custom_gaji_harian.id'] ?? 'payroll_custom_gaji_harian.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            payroll_custom_gaji_harian.*,
            employees.name,
            employees.nip,
            divisis.divisi,
            bagian.nama_bagian
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = payroll_custom_gaji_harian.employee_id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->join('bagian', 'bagian.id = employees.bagian_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry->like('employees.nip', $addCondition['search'])
                ->orLike('employees.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bagian.nama_bagian', $addCondition['search'])
                ->orLike('payroll_custom_gaji_harian.keterangan', $addCondition['search']);
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

    public function detail($id)
    {
        $selectQry = "
            employees.nip,
            employees.name AS employee_name,
            payroll_custom_gaji_harian.*
        ";

        $dataResult = $this->asArray()
            ->select($selectQry)
            ->join('employees', 'employees.id = payroll_custom_gaji_harian.employee_id', 'left')
            ->where('payroll_custom_gaji_harian.id', $id)
            ->first();

        return $dataResult;
    }

    public function getMapPayrollCustomGajiHarian(
        $employeeIds,
        $startDate,
        $endDate
    ) {
        $gajiHarianCustom = $this->asArray()
            ->whereIn('employee_id', $employeeIds)
            ->groupStart()
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->groupEnd()
            ->where('deletedAt', null)
            ->findAll();

        $mapCustomGajiHarian = [];
        foreach ($gajiHarianCustom as $g) {
            if (!isset($mapCustomGajiHarian[$g['employee_id']][$g['tanggal']])) {
                $mapCustomGajiHarian[$g['employee_id']][$g['tanggal']] = $g['nominal'];
            }
        }

        return $mapCustomGajiHarian;
    }
}
