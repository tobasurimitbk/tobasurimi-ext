<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamanKaryawanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pinjaman_karyawan';
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
        'month_year',
        'start_date',
        'end_date',
        'hadir',
        'tidak_hadir',
        'is_boleh_minjam',
        'status_pinjaman',
        'nominal',
        'is_ambil',
        'tanggal_ambil',
        'deletedAt'
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'employees.tipe' => 'employees.tipe',
            'employees.name' => 'employees.name',
            'employees.division_id' => 'employees.division_id',
            'pinjaman_karyawan.start_date' => 'pinjaman_karyawan.start_date',
            'pinjaman_karyawan.end_date' => 'pinjaman_karyawan.end_date',
            'pinjaman_karyawan.hadir' => 'pinjaman_karyawan.hadir',
            'pinjaman_karyawan.tidak_hadir' => 'pinjaman_karyawan.tidak_hadir',
            'pinjaman_karyawan.tanggal_ambil' => 'pinjaman_karyawan.tanggal_ambil'

        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'employees.name';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';


        $selectQry = "pinjaman_karyawan.*,
                    employees.nip, employees.name AS employeeName, employees.division_id, 
                    employees.tipe,
                    divisis.divisi";

        $pinjamanQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = pinjaman_karyawan.employee_id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $pinjamanQry->countAllResults(false);

        if ($addCondition['employee_id'] || $addCondition['divisi_id'] || $addCondition['employees.tipe']) {
            $pinjamanQry->groupStart();
        }

        if ($addCondition['employee_id']) {
            $pinjamanQry->like('pinjaman_karyawan.employee_id', $addCondition['employee_id']);
        }

        if ($addCondition['divisi_id']) {
            $pinjamanQry->like('pinjaman_karyawan.division_id', $addCondition['divisi_id']);
        }

        if ($addCondition['employees.tipe']) {
            $pinjamanQry->like('employees.tipe', $addCondition['employees.tipe']);
        }


        if ($addCondition['employee_id'] || $addCondition['divisi_id'] || $addCondition['employees.tipe']) {
            $pinjamanQry->groupEnd();
        }

        $totalFilteredData = $pinjamanQry->countAllResults(false);
        $data = $pinjamanQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getPinjamanKaryawanDiambil($employeeID, $monthYear)
    {
        return $this->asArray()->where('month_year', $monthYear)->where('status_pinjaman', '1')->where('employee_id', $employeeID)->first();
    }
}
