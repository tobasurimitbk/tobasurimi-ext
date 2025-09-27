<?php

namespace App\Models;

use CodeIgniter\Model;
use PDO;

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
        'division_id',
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
        'hadir_final',
        'total_perizinan_not_approved',
        'total_perizinan_approved',
        'nominal_cadangan',
        'nominal_gaji_harian',
        'nominal_pinjaman_karyawan',
        'nominal_uang_gaji',
        'nominal_uang_lembur',
        'nominal_pengurangan_gaji',
        'nominal_penambahan_gaji',
        'nominal_gaji_diterima',
        'start_date',
        'end_date',
        'isPosted',
        'isAmbil'
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
            'employees.nip'    => 'employees.nip',
            'employees.name'   => 'employees.name',
            'employees.divisi_id' => 'employees.divisi_id',
            'employees.bagian_id' => 'employees.bagian_id',
            'payrolls.start_date' => 'payrolls.start_date',
            'payrolls.hadir_final' => 'payrolls.hadir_final',
            'payrolls.nominal_uang_gaji' => 'payrolls.nominal_uang_gaji',
            'payrolls.nominal_uang_lembur' => 'payrolls.nominal_uang_lembur',
            'payrolls.nominal_pengurangan_gaji' => 'payrolls.nominal_pengurangan_gaji',
            'payrolls.nominal_gaji_diterima' => 'payrolls.nominal_gaji_diterima'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        // Ensure the sort and sortType values are valid
        $sort = $availableSort[$addCondition['sort'] ?? 'id'] ?? 'payrolls.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            payrolls.*,
            employees.name,
            employees.nip,
            divisis.divisi,
            bagian.nama_bagian
            "; // Corrected column names and aliases

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = payrolls.employee_id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left')
            ->join('bagian', 'bagian.id = employees.bagian_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['employee_id'] || $addCondition['divisi_id'] || $addCondition['tipe'] ||  $addCondition['bagian_id']) {
            $dataQry->groupStart();
        }

        if ($addCondition['employee_id']) {
            $dataQry->like('employees.id', $addCondition['employee_id']);
        }

        if ($addCondition['bagian_id']) {
            $dataQry->like('employees.bagian_id', $addCondition['bagian_id']);
        }

        if ($addCondition['divisi_id']) {
            $dataQry->like('employees.division_id', $addCondition['divisi_id']);
        }

        if ($addCondition['tipe']) {
            $dataQry->where('employees.tipe', $addCondition['tipe']);
        }

        if ($addCondition['employee_id'] || $addCondition['divisi_id'] || $addCondition['tipe'] ||  $addCondition['bagian_id']) {
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

    public function generate($employeeID, $yearMonth, $payrollID, $startDate, $endDate)
    {
        $result = [
            'nominal_cadangan' => 0,
            'nominal_gaji_harian' => 0,
            'nominal_pinjaman_karyawan' => 0,
            'nominal_uang_gaji' => 0,
            'nominal_uang_lembur' => 0,
            'nominal_pengurangan_gaji' => 0,
            'nominal_gaji_diterima' => 0,
            'nominal_penambahan_gaji' => 0,
        ];

        // declare model
        $formLemburModel = new FormLemburModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();
        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $payrollGajiHarianModel = new PayrollGajiHarianModel();

        // set uang lembur
        $uangLemburTotal = $formLemburModel->select("SUM(total_uang_lembur) as total")
            ->where("LEFT(periode, 7) = '$yearMonth'", null, false)
            ->where('employee_id', $employeeID)
            ->groupStart()
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->groupEnd()
            ->findAll();

        $formLemburModel->where('employee_id', $employeeID)
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->set('is_payroll', '1')
            ->update();

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

        // set nominal cadangan & gaji harian di payroll
        $result['nominal_cadangan'] = $gajiCadangan == null ? 0 : $gajiCadangan['nominal'];
        $result['nominal_gaji_harian'] = $gajiHarian == null ? 0 : $gajiHarian['nominal'];

        // nominal pinjaman karyawan set
        $pinjamanKaryawan = $pinjamanKaryawanModel->getPinjamanKaryawanDiambil($employeeID, $yearMonth);

        if ($pinjamanKaryawan != null) {
            $result['nominal_pinjaman_karyawan'] = $pinjamanKaryawan['nominal'];
        }

        // GENERATE 
        // $result['nominal_uang_gaji'] = ($nominalGajiCadangan + $nominalGajiHarian) * $totalKehadiran;
        $payroll = $this->asArray()->find($payrollID);
        $nominalUangGaji = $payrollGajiHarianModel->generate(
            $payrollID,
            $payroll['company_id'],
            $employeeID,
            $yearMonth,
        );
        $result['nominal_uang_gaji'] = $nominalUangGaji;

        // nominal pengurangan gaji ( keterlambatan absen + komponen gaji minus)
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

        $result['nominal_pengurangan_gaji'] += ($attendanceTerlambat[0]['total'] ?? 0);
        $result['nominal_pengurangan_gaji'] += ($gajiMinus[0]['total'] ?? 0);
        $result['nominal_pengurangan_gaji'] += ($result['nominal_pinjaman_karyawan'] ?? 0);

        // nominal penambahan gaji
        $result['nominal_penambahan_gaji'] = $gajiPlus[0]['total'];
        // gaji diterima
        $result['nominal_gaji_diterima'] = ($result['nominal_uang_gaji'] + $result['nominal_uang_lembur'] + $gajiPlus[0]['total']) - $result['nominal_pengurangan_gaji'];

        return $result;
    }

    public function detailPayroll($payrollID)
    {
        return $this->asArray()->select('payrolls.*, employees.name AS employeeName, employees.nip, divisis.divisi, bagian.nama_bagian')
            ->join('employees', 'employees.id = payrolls.employee_id')
            ->join('divisis', 'divisis.id = employees.division_id')
            ->join('bagian', 'bagian.id = employees.bagian_id', 'left')
            ->where('payrolls.id', $payrollID)
            ->where('employees.deletedAt', null)
            ->first();
    }

    public function generateIfPayrollChanged($payrollID)
    {
        $result = [
            'nominal_cadangan' => 0,
            'nominal_gaji_harian' => 0,
            'nominal_pinjaman_karyawan' => 0,
            'nominal_uang_gaji' => 0,
            'nominal_uang_lembur' => 0,
            'nominal_pengurangan_gaji' => 0,
            'nominal_gaji_diterima' => 0,
            'nominal_penambahan_gaji' => 0,
        ];

        // declare model
        $formLemburModel = new FormLemburModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();
        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $payrollGajiHarianModel = new PayrollGajiHarianModel();

        $payroll = $this->asArray()->where('id', $payrollID)->first();

        // set uang lembur
        $uangLemburTotal = $formLemburModel->select("SUM(total_uang_lembur) as total")
            ->where('employee_id', $payroll['employee_id'])
            ->groupStart()
            ->where('periode >=', $payroll['start_date'])
            ->where('periode <=', $payroll['end_date'])
            ->groupEnd()
            ->findAll();

        $formLemburModel->where('employee_id', $payroll['employee_id'])
            ->where('periode >=', $payroll['start_date'])
            ->where('periode <=', $payroll['end_date'])
            ->set('is_payroll', '1')
            ->update();

        $result['nominal_uang_lembur'] = $uangLemburTotal[0]['total'];

        // set nominal cadangan & gaji harian di payroll riwayat (karena ini update)
        $result['nominal_cadangan'] = $payroll['nominal_cadangan'];
        $result['nominal_gaji_harian'] = $payroll['nominal_gaji_harian'];

        // nominal pinjaman karyawan set
        $pinjamanKaryawan = $pinjamanKaryawanModel->getPinjamanKaryawanDiambil($payroll['employee_id'], $payroll['year_month']);

        if ($pinjamanKaryawan != null) {
            $result['nominal_pinjaman_karyawan'] = $pinjamanKaryawan['nominal'];
        }

        // GENERATE 
        // $result['nominal_uang_gaji'] = ($nominalGajiCadangan + $nominalGajiHarian) * $totalKehadiran;
        $nominalUangGaji = $payrollGajiHarianModel->generate(
            $payrollID,
            $payroll['company_id'],
            $payroll['employee_id'],
            $payroll['year_month'],
        );
        $result['nominal_uang_gaji'] = $nominalUangGaji;

        // nominal pengurangan gaji ( keterlambatan absen + komponen gaji minus)

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

        $result['nominal_pengurangan_gaji'] += ($attendanceTerlambat[0]['total'] ?? 0);
        $result['nominal_pengurangan_gaji'] += ($gajiMinus[0]['total'] ?? 0);
        $result['nominal_pengurangan_gaji'] += ($result['nominal_pinjaman_karyawan'] ?? 0);

        // gaji diterima
        $result['nominal_gaji_diterima'] = ($result['nominal_uang_gaji'] + $result['nominal_uang_lembur'] + $gajiPlus[0]['total']) - $result['nominal_pengurangan_gaji'];

        // update
        $this->update($payrollID, [
            'nominal_cadangan' => $result['nominal_cadangan'],
            'nominal_gaji_harian' => $result['nominal_gaji_harian'],
            'nominal_uang_gaji' => $result['nominal_uang_gaji'],
            'nominal_uang_lembur' => $result['nominal_uang_lembur'],
            'nominal_pengurangan_gaji' => $result['nominal_pengurangan_gaji'],
            'nominal_gaji_diterima' => $result['nominal_gaji_diterima'],
            'nominal_penambahan_gaji' => $gajiPlus[0]['total']
        ]);
    }

    public function getListPrintPayrollByDivision($divisionID, $adminID, $year, $month, $companyID)
    {
        $bagianModel = new BagianModel();

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
        divisis.divisi AS divisiName,
        employees.bagian_id 
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
            $potongan += ($p->nominal_pengurangan_gaji + $p->nominal_pinjaman_karyawan);
            $jumlahUpah += $p->nominal_gaji_diterima;

            $bagian = $bagianModel->where('id', $p->bagian_id)->first();

            array_push($dataPayRolls, [
                "no" => $no++,
                "id" => $p->id,
                "employee_id" => $p->employee_id,
                "nip" => $p->employeesNIP,
                "name"  => $p->employeesName,
                "namaBagian" => $bagian == null ? "-" : $bagian['nama_bagian'],
                "divisi" => $p->divisiName,
                "hariKerja" => $p->hadir_final . "",
                "upahPokok" => number_format($p->nominal_uang_gaji, 2, ',', '.'),
                "upahLembur" => number_format($p->nominal_uang_lembur, 2, ',', '.'),
                "totalUpah" => number_format($p->nominal_uang_gaji + $p->nominal_uang_lembur, 2, ',', '.'),
                "potongan" => number_format($p->nominal_pengurangan_gaji +  $p->nominal_pinjaman_karyawan,  2, ',', '.'),
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


    public function getPayrollDetail($yearMonth, $divisionID, $companyID)
    {

        $payrollModel = new PayrollsModel();
        $employeeModel = new EmployeesModel();
        $formLemburModel = new FormLemburModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();
        $rekapPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $companyModel = new CompaniesModel();
        $divisiModel = new DivisisModel();

        $employeePayroll = $this->asArray()->select('payrolls.*, employees.division_id')
            ->join('employees', 'employees.id = payrolls.employee_id')
            ->where('payrolls.year_month', $yearMonth)
            ->where('employees.company_id', $companyID)
            ->where('employees.division_id', $divisionID)
            ->findAll();

        $data = [];

        foreach ($employeePayroll as $ep) {
            $payrollDetail = $payrollModel->where('id', $ep['id'])->first();
            $employee = $employeeModel->getSingleEmployee($ep['employee_id']);
            $splitJamLembur = $formLemburModel->getTotalLemburJamPertamaKedua($payrollDetail['employee_id'], $payrollDetail['year_month']);

            $data[] = [
                'payroll' => $payrollDetail,
                'employee' => $employee,
                'rekapLembur' => $formLemburModel->rekap($payrollDetail['employee_id'], $payrollDetail['year_month']),
                'totalLemburJamPertama' => $splitJamLembur['jamPertama'],
                'totalLemburJamKedua' => $splitJamLembur['jamKedua'],
                'perhitunganGaji' => $payrollGajiModel->getPerhitunganKomponenGajiPayroll($ep['id']),
                'totalNominalKeterlambatanPresensi' => $attendanceTerlambatModel->getTotalRekap($ep['id']),
                'totalNominalRekapPerizinanNotApproved' => $rekapPerizinanNotApprovedModel->getTotalRekap($ep['id'])
            ];
        }

        return [
            'year' => explode("-", $yearMonth)[0],
            'month' => explode("-", $yearMonth)[1],
            'company'  => $companyModel->where('id', $companyID)->first(),
            'divisi' =>  $divisiModel->where('id', $divisionID)->first(),
            'data' => $data
        ];
    }

    public function getPotonganPayroll($yearMonth, $companyID, $divisionID)
    {
        $divisiModel = new DivisisModel();
        $payrollModel = new PayrollsModel();
        $companyModel = new CompaniesModel();
        $bagianModel = new BagianModel();

        $bagianData = $bagianModel->where('division_id', $divisionID)->where('deletedAt', null)->findAll();

        $res = [];
        $potonganRes = [
            'totPotBonKoperasi' => 0,
            'totPotBpjs' => 0,
            'totPotDenda' => 0,
            'totPotIuranKoperasi' => 0,
            'totPotKantin' => 0,
            'totPerlengkapanKerja' => 0,
            'totPotPinjaman' => 0,
            'totPotSpm' => 0,
            'totPotStm' => 0,
            'totPotAstek' => 0,
            'totPotUangMakan' => 0,
            'totPotongan' => 0
        ];

        foreach ($bagianData as $b) {
            $employeePayroll = $employeePayroll = $payrollModel
                ->select('employees.name, employees.id AS employeeID, payrolls.*')
                ->join('employees', 'payrolls.employee_id = employees.id')
                ->where('employees.bagian_id', $b['id'])
                ->where('employees.division_id', $divisionID)
                ->where('year_month', $yearMonth)
                ->findAll();

            $detail = array();
            $potonganSingle = [
                'totPotBonKoperasi' => 0,
                'totPotBpjs' => 0,
                'totPotDenda' => 0,
                'totPotIuranKoperasi' => 0,
                'totPotKantin' => 0,
                'totPerlengkapanKerja' => 0,
                'totPotPinjaman' => 0,
                'totPotSpm' => 0,
                'totPotStm' => 0,
                'totPotAstek' => 0,
                'totPotUangMakan' => 0,
                'totPotongan' => 0
            ];
            foreach ($employeePayroll as $ep) {
                $detail[] = static::getPotonganByEmployeeID($ep['employeeID'], $yearMonth);
            }
            foreach ($detail as $dp) {
                $potonganSingle['totPotBonKoperasi'] += $dp['potBonKoperasi'];
                $potonganSingle['totPotBpjs'] += $dp['potBpjs'];
                $potonganSingle['totPotDenda'] += $dp['potDenda'];
                $potonganSingle['totPotIuranKoperasi'] += $dp['potIuranKoperasi'];
                $potonganSingle['totPotKantin'] += $dp['potKantin'];
                $potonganSingle['totPerlengkapanKerja'] += $dp['potPerlengkapanKerja'];
                $potonganSingle['totPotPinjaman'] += $dp['potPinjaman'];
                $potonganSingle['totPotSpm'] += $dp['potSpm'];
                $potonganSingle['totPotStm'] += $dp['potStm'];
                $potonganSingle['totPotAstek'] += $dp['potAstek'];
                $potonganSingle['totPotUangMakan'] += $dp['potUangMakan'];
                $potonganSingle['totPotongan'] += $dp['totPotongan'];
            }

            $potonganRes['totPotBonKoperasi'] += $potonganSingle['totPotBonKoperasi'];
            $potonganRes['totPotBpjs'] += $potonganSingle['totPotBpjs'];
            $potonganRes['totPotDenda'] += $potonganSingle['totPotDenda'];
            $potonganRes['totPotIuranKoperasi'] += $potonganSingle['totPotIuranKoperasi'];
            $potonganRes['totPotKantin'] += $potonganSingle['totPotKantin'];
            $potonganRes['totPerlengkapanKerja'] += $potonganSingle['totPerlengkapanKerja'];
            $potonganRes['totPotPinjaman'] += $potonganSingle['totPotPinjaman'];
            $potonganRes['totPotSpm'] += $potonganSingle['totPotSpm'];
            $potonganRes['totPotStm'] += $potonganSingle['totPotStm'];
            $potonganRes['totPotAstek'] += $potonganSingle['totPotAstek'];
            $potonganRes['totPotUangMakan'] += $potonganSingle['totPotUangMakan'];
            $potonganRes['totPotongan'] += $potonganSingle['totPotongan'];

            $res[] = [
                'bagian' => $b['nama_bagian'],
                'detail' => $detail,
                'totPotonganSingle' =>  $potonganSingle,
            ];
        }

        return [
            'res' => $res,
            'potAll' => $potonganRes,
            'unit' => $companyModel->where('id', $companyID)->first(),
            'divisi' => $divisiModel->where('id', $divisionID)->first(),
        ];
    }

    public function getSummaryPayroll($yearMonth, $companyID, $divisionID)
    {
        $divisiModel = new DivisisModel();
        $companyModel = new CompaniesModel();
        $bagianModel = new BagianModel();

        $bagianData = $bagianModel->where('division_id', $divisionID)->where('deletedAt', null)->findAll();

        $res = [];
        $upahPokok = 0;
        $tunjanganPlusCadangan = 0;
        $lemburTotal = 0;
        $totalUpah = 0;
        $potongan = 0;
        $upahBersih = 0;
        $orangTotal = 0;
        $lembur = 0;
        $jamKerja = 0;

        foreach ($bagianData as $b) {
            $selectQry = "
                COUNT(DISTINCT payrolls.employee_id) AS totalEmployee, 
                SUM(nominal_uang_gaji) AS upahBersih, 
                SUM(nominal_penambahan_gaji) AS tunjangan,
                SUM(nominal_cadangan) AS cadangan,
                SUM(nominal_uang_lembur) AS lembur,
                SUM(nominal_gaji_diterima) AS total_upah,
                SUM(nominal_pengurangan_gaji + nominal_pinjaman_karyawan) AS potongan
            ";
            $employeePayrollTotal = $this->asArray()->select($selectQry)
                ->join('employees', 'employees.id = payrolls.employee_id')
                ->where('payrolls.year_month', $yearMonth)
                ->where('employees.company_id', $companyID)
                ->where('employees.bagian_id', $b['id'])
                ->findAll();

            // set total
            $upahPokok += $employeePayrollTotal[0]['upahBersih'];
            $tunjanganPlusCadangan += ($employeePayrollTotal[0]['cadangan'] + $employeePayrollTotal[0]['tunjangan']);
            $lemburTotal += $employeePayrollTotal[0]['lembur'];
            $totalUpah += ($employeePayrollTotal[0]['upahBersih'] + $employeePayrollTotal[0]['cadangan'] + $employeePayrollTotal[0]['tunjangan']);
            $potongan += $employeePayrollTotal[0]['potongan'];
            $upahBersih += ($employeePayrollTotal[0]['upahBersih'] + $employeePayrollTotal[0]['tunjangan'] + $employeePayrollTotal[0]['cadangan'] - $employeePayrollTotal[0]['potongan']);
            $orangTotal += $employeePayrollTotal[0]['totalEmployee'];
            $lembur +=  static::getTotalJamLemburInOnePeriode($divisionID, $b['id'], $yearMonth);
            $jamKerja += static::getTotalJamKerjaInOnePeriode($divisionID, $b['id'], $yearMonth);

            $res[] = [
                'bagian' => $b['nama_bagian'],
                'payrollTotal' => $employeePayrollTotal,
                'totalJamKerja' => static::getTotalJamKerjaInOnePeriode($divisionID, $b['id'], $yearMonth),
                'totalJamLembur' => static::getTotalJamLemburInOnePeriode($divisionID, $b['id'], $yearMonth),
            ];
        }

        return [
            'res' => $res,
            'upahPokokTotal' => $upahPokok,
            'orangTotal' => $orangTotal,
            'tunjanganPlusCadangan' => $tunjanganPlusCadangan,
            'lemburTotal' => $lemburTotal,
            'totalUpah' => $totalUpah,
            'potongan' => $potongan,
            'upahBersih' => $upahBersih,
            'lembur' => $lembur,
            'jamKerja' => $jamKerja,
            'unit' => $companyModel->where('id', $companyID)->first(),
            'divisi' => $divisiModel->where('id', $divisionID)->first(),
        ];
    }

    static function getPotonganByEmployeeID($employeeID, $yearMonth)
    {
        $res = [];
        $payrollModel = new PayrollsModel();

        $employeePayroll = $payrollModel
            ->select('employees.name, employees.id AS employeeID, employees.nip, payrolls.*')
            ->join('employees', 'payrolls.employee_id = employees.id')
            ->where('employee_id', $employeeID)
            ->where('year_month', $yearMonth)
            ->first();

        $res = [
            'employee' => $employeePayroll,
            'potBonKoperasi' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('BON KOPERASI', $employeePayroll['id']),
            'potBpjs' => ($employeePayroll == null) ? 0 : static::getPotonganLikeStr('BPJS', $employeePayroll['id']),
            'potDenda' => ($employeePayroll == null) ? 0 : static::getPotonganLikeStr('DENDA', $employeePayroll['id']),
            'potIuranKoperasi' => ($employeePayroll == null) ? 0 : static::getPotonganLikeStr("IURAN KOPERASI", $employeePayroll['id']),
            'potKantin' => ($employeePayroll == null) ? 0 : static::getPotonganLikeStr("KANTIN", $employeePayroll['id']),
            'potPerlengkapanKerja' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('PERLENGKAPAN KERJA', $employeePayroll['id']),
            'potPinjaman' => ($employeePayroll == null) ? 0 : $employeePayroll['nominal_pinjaman_karyawan'],
            'potSpm' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('SPM', $employeePayroll['id']),
            'potStm' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('STM', $employeePayroll['id']),
            'potAstek' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('ASTEK', $employeePayroll['id']),
            'potUangMakan' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('UANG MAKAN', $employeePayroll['id']),
            // 'potTutupMulut' => ($employeePayroll == null) ? 0 : static::getPotonganLikeStr('TUTUP MULUT', $employeePayroll['id']),
            // 'potBajuSeragam' => ($employeePayroll == null) ? 0 : static::getPotonganLikeStr('Potongan Baju Seragam', $employeePayroll['id']),
            // 'potSepatuCelanaTopi' => ($employeePayroll == null) ? 0 : static::getPotonganLikeStr('Potongan Sepatu Celana Topi', $employeePayroll['id']),
            // 'potDenda' => ($employeePayroll == null) ? 0 : ($employeePayroll['nominal_pengurangan_gaji'] - $employeePayroll['nominal_pinjaman_karyawan']),
            // 'potKartu' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('Potongan Kartu', $employeePayroll['id']),
            // 'potPinjamanKoperasi' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('Potongan Pinjaman Koperasi', $employeePayroll['id']),
            'totPotongan' => 0
        ];

        $res['totPotongan'] = $res['potBonKoperasi'] +
            $res['potBpjs'] +
            $res['potDenda'] +
            $res['potIuranKoperasi'] +
            $res['potKantin'] +
            $res['potPerlengkapanKerja'] +
            $res['potPinjaman'] +
            $res['potSpm'] +
            $res['potStm'] +
            $res['potAstek'] +
            $res['potUangMakan'];
        return $res;
    }

    static function getPotonganLikeStr($str, $payrollID)
    {
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $res = $payrollGajiModel->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('payroll_id', $payrollID)
            ->where('tunjangan.name', $str)
            ->first();
        return $res == null ? 0 : $res['nominal'];
    }

    static function getTotalJamLemburInOnePeriode($divisionID, $bagianID, $yearMonth)
    {
        $formLemburModel = new FormLemburModel();

        $startDate = $yearMonth . '-01';
        $endDate   = date('Y-m-d', strtotime("$startDate +1 month"));

        $result = $formLemburModel
            ->select("SUM(form_lembur.total_jam_lembur) AS totalJamLembur")
            ->join('employees', 'employees.id = form_lembur.employee_id')
            ->where('employees.division_id', $divisionID)
            ->where('employees.bagian_id', $bagianID)
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->first();

        return $result['totalJamLembur'] ?? 0;
    }

    static function getTotalJamKerjaInOnePeriode($divisionID, $bagianID, $yearMonth)
    {
        $db = db_connect();

        $startDate = $yearMonth . '-01';
        $endDate   = date('Y-m-d', strtotime("$startDate +1 month"));

        $builder = $db->table('attendances')
            ->select("FLOOR(SUM(TIME_TO_SEC(TIMEDIFF(attendances.checkout, attendances.checkin)) / 3600)) AS totalJamKerja")
            ->join('employees', 'employees.id = attendances.employee_id')
            ->where('employees.division_id', $divisionID)
            ->where('employees.bagian_id', $bagianID)
            ->where('attendances.checkin IS NOT NULL')
            ->where('attendances.checkout IS NOT NULL')
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate);

        $result = $builder->get()->getRow();

        return $result->totalJamKerja ?? 0;
    }


    static function getRekapGajiKaryawanPerHari($employeeID) {}

    static function convertionIDRMoneyTotal($nilai)
    {
        $pecahan = array(
            '50000' => 'Lembar Rp 50.000',
            '20000' => 'Lembar Rp 20.000',
            '10000' => 'Lembar Rp 10.000',
            '5000' => 'Lembar Rp 5000',
            '2000' => 'Lembar Rp 2000',
            '1000' => 'Lembar Rp 1000',
            '500' => 'Pecahan Rp 500',
            '200' => 'Pecahan Rp 200',
            '100' => 'Pecahan Rp 100',
            '50' => 'Pecahan Rp 50',
            '25' => 'Pecahan Rp 25'
        );

        $result = [];

        foreach ($pecahan as $nilaiPecahan => $namaPecahan) {
            $jumlahPecahan = floor($nilai / $nilaiPecahan);
            $result[] = [
                'lembar' => $namaPecahan,
                'totalLembar' => $jumlahPecahan
            ];
            $nilai %= $nilaiPecahan;
        }

        return $result;
    }
}
