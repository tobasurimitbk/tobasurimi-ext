<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'payrolls';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'employee_id',
        'year_month',
        'cuti_tahunan',
        'cuti_haid',
        'cuti_hamil',
        'cuti_melahirkan',
        'izin',
        'sakit',
        'rl',
        'hadir',
        'libur',
        'alpha',
        'nominal_uang_gaji',
        'nominal_uang_lembur',
        'nominal_pengurangan_gaji',
        'nominal_penambahan_gaji',
        'nominal_gaji_diterima',
        'isPosted'
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
            'nip'    => 'employees.nip',
            'name'   => 'employees.name',
            'divisi' => 'divisis.divisi',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        // Ensure the sort and sortType values are valid
        $sort = $availableSort[$addCondition['sort'] ?? 'id'] ?? 'payrolls.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            payrolls.*,
            employees.name AS employeesName,
            employees.nip AS employeesNIP,
            divisis.divisi AS divisiName
            "; // Corrected column names and aliases

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = payrolls.employee_id', 'INNER')
            ->join('divisis', 'divisis.id = employees.division_id', 'LEFT') // Corrected the join condition
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['employee_id'] || $addCondition['divisi_id']) {
            $dataQry->groupStart();
        }

        if ($addCondition['employee_id']) {
            $dataQry->like('employees.id', $addCondition['employee_id']);
        }

        if ($addCondition['divisi_id']) {
            $dataQry->like('employees.division_id', $addCondition['divisi_id']);
        }

        if ($addCondition['employee_id'] || $addCondition['divisi_id']) {
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

    public function generate($employeeID, $yearMonth, $payrollID, $totalKehadiran)
    {
        $result = [
            'nominal_uang_gaji' => 0,
            'nominal_uang_lembur' => 0,
            'nominal_pengurangan_gaji' => 0,
            'nominal_gaji_diterima' => 0,
            'nominal_penambahan_gaji' => 0
        ];

        // declare model
        $formLemburModel = new FormLemburModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $formPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();

        // set uang lembur
        $uangLemburTotal = $formLemburModel->select("SUM(total_uang_lembur) as total")
            ->where("LEFT(periode, 7) = '$yearMonth'", null, false)
            ->where('employee_id', $employeeID)
            ->findAll();

        $result['nominal_uang_lembur'] = $uangLemburTotal[0]['total'];

        // set uang gaji (jmlh hadir x (gaji harian + uang cadangan))
        $gajiHarian = $payrollGajiModel->select('payroll_gaji_conjunction.nominal')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_gaji_harian', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->first();

        // get nominal uang cadangan
        $gajiCadangan = $payrollGajiModel->select('payroll_gaji_conjunction.nominal')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_cadangan', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->first();

        $nominalGajiHarian = ($gajiHarian != null) ? $gajiHarian['nominal'] : 0;
        $nominalGajiCadangan = ($gajiCadangan != null) ? $gajiCadangan['nominal'] : 0;

        $result['nominal_uang_gaji'] = ($nominalGajiCadangan + $nominalGajiHarian) * $totalKehadiran;

        // nominal pengurangan gaji (perizinan not approved + keterlambatan absen + komponen gaji minus)
        // perizinan not approved
        $perizinanNotApproved = $formPerizinanNotApprovedModel->select("SUM(nominal_pengurangan) AS total")
            ->where('employee_id', $employeeID)
            ->where('payroll_id', $payrollID)
            ->findAll();

        // keterlambatan kehadiran
        $attendanceTerlambat = $attendanceTerlambatModel->select("SUM(nominal_pengurangan) AS total")
            ->where('payroll_id', $payrollID)
            ->findAll();

        // tunjangan minus
        $gajiMinus = $payrollGajiModel->select("SUM(payroll_gaji_conjunction.nominal) AS total")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.tipe', 'MINUS')
            ->where('tunjangan.is_cadangan != ', '1')
            ->where('tunjangan.is_gaji_harian != ', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->findAll();

        // tunjangan plus
        $gajiPlus = $payrollGajiModel->select("SUM(payroll_gaji_conjunction.nominal) AS total")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.tipe', 'PLUS')
            ->where('tunjangan.is_cadangan != ', '1')
            ->where('tunjangan.is_gaji_harian != ', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->findAll();

        $result['nominal_pengurangan_gaji'] += ($perizinanNotApproved[0]['total'] ?? 0);
        $result['nominal_pengurangan_gaji'] += ($attendanceTerlambat[0]['total'] ?? 0);
        $result['nominal_pengurangan_gaji'] += ($gajiMinus[0]['total'] ?? 0);

        // nominal penambahan gaji
        $result['nominal_penambahan_gaji'] = $gajiPlus[0]['total'];
        // gaji diterima
        $result['nominal_gaji_diterima'] = ($result['nominal_uang_gaji'] + $result['nominal_uang_lembur'] + $gajiPlus[0]['total']) - $result['nominal_pengurangan_gaji'];

        return $result;
    }

    public function detailPayroll($payrollID)
    {
        return $this->asArray()->select('payrolls.*, employees.name AS employeeName, employees.nip, divisis.divisi')
            ->join('employees', 'employees.id = payrolls.employee_id')
            ->join('divisis', 'divisis.id = employees.division_id')
            ->where('payrolls.id', $payrollID)
            ->where('employees.deletedAt', null)
            ->first();
    }

    public function generateIfPayrollChanged($payrollID)
    {
        $result = [
            'nominal_uang_gaji' => 0,
            'nominal_uang_lembur' => 0,
            'nominal_pengurangan_gaji' => 0,
            'nominal_gaji_diterima' => 0,
            'nominal_penambahan_gaji' => 0
        ];

        // declare model
        $formLemburModel = new FormLemburModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $formPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();

        $payroll = $this->asArray()->where('id', $payrollID)->first();

        // set uang lembur
        $uangLemburTotal = $formLemburModel->select("SUM(total_uang_lembur) as total")
            ->where("LEFT(periode, 7) = '$payroll[year_month]'", null, false)
            ->where('employee_id', $payroll['employee_id'])
            ->findAll();

        $result['nominal_uang_lembur'] = $uangLemburTotal[0]['total'];

        // set uang gaji (jmlh hadir x (gaji harian + uang cadangan))
        $gajiHarian = $payrollGajiModel->select('payroll_gaji_conjunction.nominal')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_gaji_harian', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->first();

        // get nominal uang cadangan
        $gajiCadangan = $payrollGajiModel->select('payroll_gaji_conjunction.nominal')
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.is_cadangan', '1')
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->first();

        $nominalGajiHarian = ($gajiHarian != null) ? $gajiHarian['nominal'] : 0;
        $nominalGajiCadangan = ($gajiCadangan != null) ? $gajiCadangan['nominal'] : 0;

        $result['nominal_uang_gaji'] = ($nominalGajiCadangan + $nominalGajiHarian) * $payroll['hadir'];

        // nominal pengurangan gaji (perizinan not approved + keterlambatan absen + komponen gaji minus)
        // perizinan not approved
        $perizinanNotApproved = $formPerizinanNotApprovedModel->select("SUM(nominal_pengurangan) AS total")
            ->where('employee_id', $payroll['employee_id'])
            ->where('payroll_id', $payrollID)
            ->findAll();

        // keterlambatan kehadiran
        $attendanceTerlambat = $attendanceTerlambatModel->select("SUM(nominal_pengurangan) AS total")
            ->where('payroll_id', $payrollID)
            ->findAll();

        // tunjangan minus
        $gajiMinus = $payrollGajiModel->select("SUM(payroll_gaji_conjunction.nominal) AS total")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.tipe', 'MINUS')
            ->where('tunjangan.is_cadangan != ', '1')
            ->where('tunjangan.is_gaji_harian != ', '1')
            ->where('payroll_gaji_conjunction.employee_id', $payroll['employee_id'])
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->findAll();

        // tunjangan plus
        $gajiPlus = $payrollGajiModel->select("SUM(payroll_gaji_conjunction.nominal) AS total")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.tipe', 'PLUS')
            ->where('tunjangan.is_cadangan != ', '1')
            ->where('tunjangan.is_gaji_harian != ', '1')
            ->where('payroll_gaji_conjunction.employee_id', $payroll['employee_id'])
            ->where('payroll_gaji_conjunction.payroll_id', $payrollID)
            ->findAll();

        $result['nominal_pengurangan_gaji'] += ($perizinanNotApproved[0]['total'] ?? 0);
        $result['nominal_pengurangan_gaji'] += ($attendanceTerlambat[0]['total'] ?? 0);
        $result['nominal_pengurangan_gaji'] += ($gajiMinus[0]['total'] ?? 0);

        // gaji diterima
        $result['nominal_gaji_diterima'] = ($result['nominal_uang_gaji'] + $result['nominal_uang_lembur'] + $gajiPlus[0]['total']) - $result['nominal_pengurangan_gaji'];

        // update
        $this->update($payrollID, [
            'nominal_uang_gaji' => $result['nominal_uang_gaji'],
            'nominal_uang_lembur' => $result['nominal_uang_lembur'],
            'nominal_pengurangan_gaji' => $result['nominal_pengurangan_gaji'],
            'nominal_gaji_diterima' => $result['nominal_gaji_diterima'],
            'nominal_penambahan_gaji' => $gajiPlus[0]['total']
        ]);
    }

    public function getListPrintPayrollByDivision($divisionID, $adminID, $year, $month, $companyID)
    {
        $condition = [
            'employees.company_id' => $companyID,
            "employees.deletedAt" => null,
            "employees.company_id" => $companyID,
            "employees.division_id" => $divisionID,
            "employees.id != " => $adminID, // kecualikan admin yg akses
            "year_month" => $year . "-" . $month,
        ];

        $selectQry = "
        payrolls.*,
        employees.name AS employeesName,
        employees.nip AS employeesNIP,
        divisis.divisi AS divisiName
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = payrolls.employee_id', 'INNER')
            ->join('divisis', 'divisis.id = employees.division_id', 'LEFT') // Corrected the join condition
            ->findAll();

        $no = 1;
        // set variable
        $dataPayRolls = [];
        $upahPokok = 0;
        $upahLembur = 0;
        $totalUpah = 0;
        $potongan = 0;
        $jumlahUpah = 0;

        foreach ($dataQry as $p) {
            $upahPokok += $p->nominal_uang_gaji;
            $upahLembur += $p->nominal_uang_lembur;
            $totalUpah += ($p->nominal_uang_gaji + $p->nominal_uang_lembur);
            $potongan += $p->nominal_pengurangan_gaji;
            $jumlahUpah += $p->nominal_gaji_diterima;

            array_push($dataPayRolls, [
                "no" => $no++,
                "id" => $p->id,
                "employee_id" => $p->employee_id,
                "nip" => $p->employeesNIP,
                "name"  => $p->employeesName,
                "divisi" => $p->divisiName,
                "hariKerja" => $p->hadir . "",
                "upahPokok" => number_format($p->nominal_uang_gaji, 2, ',', '.'),
                "upahLembur" => number_format($p->nominal_uang_lembur, 2, ',', '.'),
                "totalUpah" => number_format($p->nominal_uang_gaji + $p->nominal_uang_lembur, 2, ',', '.'),
                "potongan" => number_format($p->nominal_pengurangan_gaji,  2, ',', '.'),
                "jumlahUpah" => number_format($p->nominal_gaji_diterima,  2, ',', '.'),
            ]);
        }

        return [
            'dataPayroll' => $dataPayRolls,
            'total' => [
                'upahPokok' => $upahPokok,
                'upahLembur' => $upahLembur,
                'totalUpah' => $totalUpah,
                'potongan' => $potongan,
                'jumlahUpah' => $jumlahUpah,
            ]
        ];
    }

    static function convertionIDRMoneyTotal($nilai)
    {
        $pecahan = array(
            '100000' => 'Lembar 100000',
            '50000' => 'Lembar 50000',
            '20000' => 'Lembar 20000',
            '10000' => 'Lembar 10000',
            '5000' => 'Lembar 5000',
            '2000' => 'Lembar 2000',
            '1000' => 'Lembar 1000',
            '500' => 'Pecahan 500',
            '200' => 'Pecahan 200',
            '100' => 'Pecahan 100',
            '50' => 'Pecahan 50',
            '25' => 'Pecahan 25'
        );

        $result = [];

        foreach ($pecahan as $nilaiPecahan => $namaPecahan) {
            $jumlahPecahan = floor($nilai / $nilaiPecahan);
            if ($jumlahPecahan > 0) {
                $result[] = [
                    'lembar' => $namaPecahan,
                    'totalLembar' => $jumlahPecahan
                ];
                $nilai %= $nilaiPecahan;
            }
        }

        return $result;
    }
}
