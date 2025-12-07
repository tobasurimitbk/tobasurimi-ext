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
            'employees.nip'                   => 'employees.nip',
            'employees.tipe'                  => 'employees.tipe',
            'employees.name'                  => 'employees.name',
            'employees.division_id'           => 'employees.division_id',
            'pinjaman_karyawan.start_date'    => 'pinjaman_karyawan.start_date',
            'pinjaman_karyawan.end_date'      => 'pinjaman_karyawan.end_date',
            'pinjaman_karyawan.hadir'         => 'pinjaman_karyawan.hadir',
            'pinjaman_karyawan.tidak_hadir'   => 'pinjaman_karyawan.tidak_hadir',
            'pinjaman_karyawan.tanggal_ambil' => 'pinjaman_karyawan.tanggal_ambil'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort     = $availableSort[$addCondition['sort'] ?? 'employees.nip'] ?? 'employees.nip';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'asc'] ?? 'ASC';

        $selectQry = "pinjaman_karyawan.*,
                  employees.nip, employees.name AS employeeName, 
                  employees.division_id, employees.tipe,
                  divisis.divisi";

        $pinjamanQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = pinjaman_karyawan.employee_id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->orderBy($sort, $sortType);

        // Total Data (sebelum filter tambahan)
        $totalData = $pinjamanQry->countAllResults(false);

        // Apakah ada filter tambahan?
        $hasFilter =
            !empty($addCondition['employee_id']) ||
            !empty($addCondition['divisi_id']) ||
            !empty($addCondition['employees.tipe']) ||
            !empty($addCondition['status_pinjaman']) ||
            !empty($addCondition['employees.bagian_id']);

        // ========== FILTER ==========

        if (!empty($addCondition['status_pinjaman'])) {
            if ($addCondition['status_pinjaman'] === "AMBIL") {
                $pinjamanQry->where('pinjaman_karyawan.status_pinjaman', 1);
            } elseif ($addCondition['status_pinjaman'] === "TIDAK AMBIL") {
                $pinjamanQry->where('pinjaman_karyawan.status_pinjaman', 0);
            }
        }

        if (!empty($addCondition['employee_id'])) {
            $pinjamanQry->where('pinjaman_karyawan.employee_id', $addCondition['employee_id']);
        }

        if (!empty($addCondition['employees.bagian_id'])) {
            $pinjamanQry->where('employees.bagian_id', $addCondition['employees.bagian_id']);
        }

        if (!empty($addCondition['divisi_id'])) {
            $pinjamanQry->where('pinjaman_karyawan.division_id', $addCondition['divisi_id']);
        }

        if (!empty($addCondition['employees.tipe'])) {
            $pinjamanQry->where('employees.tipe', $addCondition['employees.tipe']);
        }

        // Total data setelah filter
        $totalFilteredData = $pinjamanQry->countAllResults(false);

        // Data final
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

    public function getTotalPinjamanKaryawanDiambil($employeeID, $monthYear)
    {
        $dataRes = $this->asArray()->where('month_year', $monthYear)->where('status_pinjaman', '1')->where('employee_id', $employeeID)->first();
        return $dataRes == null ? 0 : (float)$dataRes['nominal'];
    }

    public function getPinjamanKaryawanDiambilAmt(
        $employeeIds,
        $monthYear
    ) {
        return $this->asArray()
            ->where('month_year', $monthYear)
            ->where('status_pinjaman', '1')
            ->whereIn('employee_id', $employeeIds)
            ->where('deletedAt', null)
            ->findAll();
    }
}
