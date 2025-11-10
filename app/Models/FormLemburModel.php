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
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
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
            'form_lembur.periode' => 'form_lembur.periode',
            'form_lembur.jam_mulai_lembur' => 'form_lembur.jam_mulai_lembur',
            'form_lembur.jam_selesai_lembur' => 'form_lembur.jam_selesai_lembur',
            'form_lembur.total_jam_lembur' => 'form_lembur.total_jam_lembur',
            'form_lembur.total_uang_lembur' => 'form_lembur.total_uang_lembur',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        // Ensure the sort and sortType values are valid
        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'form_lembur.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            form_lembur.*,
            employees.name,
            employees.nip,
            divisis.divisi,
            bagian.nama_bagian
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = form_lembur.employee_id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->join('bagian', 'bagian.id = employees.bagian_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['divisi_id'] || $addCondition['tipe'] || $addCondition['bagian_id']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('employees.division_id', $addCondition['divisi_id']);
        }

        if ($addCondition['tipe']) {
            $dataQry->where('employees.tipe', $addCondition['tipe']);
        }

        if ($addCondition['bagian_id']) {
            $dataQry->where('employees.bagian_id', $addCondition['bagian_id']);
        }

        if ($addCondition['search']) {
            $dataQry->like('employees.nip', $addCondition['search'])
                ->orLike('employees.name', $addCondition['search'])
                ->orLike('bagian.nama_bagian', $addCondition['search']);
        }

        if ($addCondition['search'] || $addCondition['divisi_id'] || $addCondition['tipe'] || $addCondition['bagian_id']) {
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

    public function rekapLemburDateRange($employeeID, $startDate, $endDate)
    {
        return $this->asArray()->where('employee_id', $employeeID)
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
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
            'jamKedua' => number_format((($lemburJamKedua / 60) * 100), 2)
        ];
    }

    public function getTotalLemburJamPertamaKeduaByDateRange($employeeID, $startDate, $endDate)
    {
        $formLemburModel = new FormLemburModel();
        $lemburList = $formLemburModel->rekapLemburDateRange($employeeID, $startDate, $endDate);

        $lemburJamPertama = 0;
        $lemburJamKedua = 0;

        foreach ($lemburList as $lembur) {
            $totalJamHariIni = (float) $lembur['total_jam_lembur'];

            if ($totalJamHariIni <= 1) {
                // semua masuk ke jam pertama
                $lemburJamPertama += $totalJamHariIni;
            } else {
                // 1 jam pertama, sisanya ke jam kedua
                $lemburJamPertama += 1;
                $lemburJamKedua += ($totalJamHariIni - 1);
            }
        }

        return [
            'jamPertama' => $lemburJamPertama,
            'jamKedua' => $lemburJamKedua,
        ];
    }


    public function getFormLemburAmt(
        $employeeIds,
        $startDate,
        $endDate
    ) {
        $uangLemburQry = $this->asArray()
            ->select("SUM(total_uang_lembur) as total, form_lembur.employee_id")
            ->whereIn('employee_id', $employeeIds)
            ->groupStart()
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupEnd()
            ->where('deletedAt', null)
            ->findAll();

        return $uangLemburQry;
    }

    public function getFormLemburRangeAmt(
        $employeeIds,
        $startDate,
        $endDate
    ) {
        $uangLemburQry = $this->asArray()
            ->select("total_uang_lembur as total, form_lembur.*")
            ->whereIn('employee_id', $employeeIds)
            ->groupStart()
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupEnd()
            ->where('deletedAt', null)
            ->findAll();

        return $uangLemburQry;
    }
}
