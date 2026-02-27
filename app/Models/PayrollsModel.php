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
        'pg',
        'sakit',
        'rl',
        'hadir',
        'libur',
        'alpha',
        'dinas',
        'off',
        'cuti_keguguran',
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
        $sort = $availableSort[$addCondition['sort'] ?? 'employees.nip'] ?? 'employees.nip';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'asc'] ?? 'asc';

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

        if (count($addCondition['tipe']) > 0) {
            $dataQry->whereIn('employees.tipe', $addCondition['tipe']);
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
        $payroll = $this->asArray()->where('id', $payrollID)->first();
        $result['nominal_uang_gaji'] = $payroll['nominal_uang_gaji'];

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
    public function generateAmt(
        $mapFormLembur,
        $mapEmployeePayroll,
        $mapGajiHarian,
        $mapGajiCadangan,
        $mapPinjaman,
        $mapTotalGajiHarian,
        $employeeIds,
        $yearMonth,
        $payrollIDs,
        $startDate,
        $endDate
    ) {
        $formLemburModel = new FormLemburModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();

        // ✅ sekali update lembur
        $formLemburModel
            ->whereIn('employee_id', $employeeIds)
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->set('is_payroll', '1')
            ->update();

        // ✅ sekali query keterlambatan
        $attLambat = $attendanceTerlambatModel
            ->select("payroll_id, SUM(nominal_pengurangan) AS total")
            ->whereIn('payroll_id', $payrollIDs)
            ->groupBy('payroll_id')
            ->findAll();

        $mapTerlambat = [];
        foreach ($attLambat as $row) {
            $mapTerlambat[$row['payroll_id']] = (float) $row['total'];
        }

        // ✅ sekali query tunjangan MINUS
        $gajiMinus = $payrollGajiModel
            ->select("payroll_gaji_conjunction.payroll_id, SUM(payroll_gaji_conjunction.nominal) AS total")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.tipe', 'MINUS')
            ->where('tunjangan.is_cadangan !=', '1')
            ->where('tunjangan.is_gaji_harian !=', '1')
            ->whereIn('payroll_gaji_conjunction.payroll_id', $payrollIDs)
            ->groupBy('payroll_gaji_conjunction.payroll_id')
            ->findAll();

        $mapGajiMinus = [];
        foreach ($gajiMinus as $row) {
            $mapGajiMinus[$row['payroll_id']] = (float) $row['total'];
        }

        // ✅ sekali query tunjangan PLUS
        $gajiPlus = $payrollGajiModel
            ->select("payroll_gaji_conjunction.payroll_id, SUM(payroll_gaji_conjunction.nominal) AS total")
            ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id')
            ->where('tunjangan.tipe', 'PLUS')
            ->where('tunjangan.is_cadangan !=', '1')
            ->where('tunjangan.is_gaji_harian !=', '1')
            ->whereIn('payroll_gaji_conjunction.payroll_id', $payrollIDs)
            ->groupBy('payroll_gaji_conjunction.payroll_id')
            ->findAll();

        $mapGajiPlus = [];
        foreach ($gajiPlus as $row) {
            $mapGajiPlus[$row['payroll_id']] = (float) $row['total'];
        }

        // ✅ build hasil batch
        $dataResult = [];
        foreach ($employeeIds as $e) {
            $payrollId = $mapEmployeePayroll[$e] ?? null;
            if (!$payrollId) {
                continue;
            }

            $gajiLembur       = $mapFormLembur[$e] ?? 0;
            $gajiHarian       = $mapGajiHarian[$e] ?? 0;
            $gajiCadangan     = $mapGajiCadangan[$e] ?? 0;
            $pinjamanKaryawan = $mapPinjaman[$e] ?? 0;
            $totalGajiHarian  = $mapTotalGajiHarian[$e] ?? 0;

            $totalMinus = ($mapTerlambat[$payrollId] ?? 0)
                + ($mapGajiMinus[$payrollId] ?? 0)
                + $pinjamanKaryawan;

            $totalPlus  = ($mapGajiPlus[$payrollId] ?? 0);

            $dataResult[] = [
                'id'                       => $payrollId,
                'nominal_cadangan'         => $gajiCadangan,
                'nominal_gaji_harian'      => $gajiHarian,
                'nominal_pinjaman_karyawan' => $pinjamanKaryawan,
                'nominal_uang_gaji'        => $totalGajiHarian,
                'nominal_uang_lembur'      => $gajiLembur,
                'nominal_pengurangan_gaji' => $totalMinus,
                'nominal_penambahan_gaji'  => $totalPlus,
                'nominal_gaji_diterima'    => ($totalGajiHarian + $totalPlus + $gajiLembur) - $totalMinus,
            ];
        }

        return $dataResult; // tinggal updateBatch($dataResult, 'id')
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

        $result['nominal_uang_gaji'] = $payroll['nominal_uang_gaji'];

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
    public function getListPrintPayrollByDivision(
        $divisionID,
        $adminID,
        $year,
        $month,
        $companyID,
        $tipes,
        $bagianId
    ) {
        $payrollGajiConjunctionModel = new PayrollGajiConjunctionModel();

        // Kondisi dasar
        $condition = [
            'employees.company_id' => $companyID,
            "employees.deletedAt" => null,
            "employees.division_id" => $divisionID,
            // "employees.id !=" => $adminID, // kecualikan admin yg akses
            "year_month" => $year . "-" . $month,
        ];

        // Query utama payroll
        $selectQry = "
            payrolls.*,
            employees.name AS employeesName,
            employees.nip AS employeesNIP,
            divisis.divisi AS divisiName,
            employees.bagian_id,
            bagian.nama_bagian
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('employees', 'employees.id = payrolls.employee_id', 'left')
            ->join('bagian', 'bagian.id = employees.bagian_id', 'left')
            ->join('divisis', 'divisis.id = employees.division_id', 'left');

        if (!empty($tipes)) {
            $dataQry->whereIn('tipe', $tipes);
        }

        if (!empty($bagianId)) {
            $dataQry->where('employees.bagian_id', $bagianId);
        }

        $dataQry->where('payrolls.deletedAt', null);

        // Ambil semua payroll dulu
        $payrolls = $dataQry->orderBy('employees.nip', 'asc')->findAll();

        // Ambil semua payroll_id untuk map uang makan sekaligus
        $payrollIds = array_map(fn($p) => $p->id, $payrolls);

        $uangMakanList = [];
        if (!empty($payrollIds)) {
            $map = $payrollGajiConjunctionModel
                ->select('payroll_id, nominal')
                ->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id', 'left')
                ->where('tunjangan.name', 'UANG MAKAN')
                ->whereIn('payroll_id', $payrollIds)
                ->findAll();

            foreach ($map as $u) {
                $uangMakanList[$u['payroll_id']] = $u['nominal'];
            }
        }

        // Proses data & total
        $no = 1;
        $dataPayRolls = [];
        $subTotalUpah = $subTotalUangMakan = 0;
        $subTotalUpahPokok = $subTotalLemburKerja = 0;
        $subTotalTunjanganKesejahteraan = $subTotalPotongan = 0;
        $subTotalJumlahUpah = 0;

        foreach ($payrolls as $p) {
            $totalUpah = $p->nominal_gaji_diterima;
            $uangMakan = $uangMakanList[$p->id] ?? 0;
            // $upahPokok = ($p->nominal_gaji_harian + $p->nominal_cadangan) * $p->hadir_final;
            $upahPokok = $p->nominal_uang_gaji;
            $lemburKerja = $p->nominal_uang_lembur;
            $tunjanganKesejahteraan = 0;
            $potongan = $p->nominal_pengurangan_gaji;
            $jumlahUpah = $upahPokok + $lemburKerja - $potongan;

            // sum
            $subTotalUpah += $totalUpah;
            $subTotalUangMakan += $uangMakan;
            $subTotalUpahPokok += $upahPokok;
            $subTotalLemburKerja += $lemburKerja;
            $subTotalTunjanganKesejahteraan += $tunjanganKesejahteraan;
            $subTotalPotongan += $potongan;
            $subTotalJumlahUpah += $jumlahUpah;

            $dataPayRolls[] = [
                "id" => $p->id,
                "employee_id" => $p->employee_id,
                "nip" => $p->employeesNIP,
                "name"  => $p->employeesName,
                "namaBagian" => $p->nama_bagian,
                "divisi" => $p->divisiName,
                "hariKerja" => $p->hadir_final,
                "totalUpah" => $totalUpah,
                "uangMakan" => $uangMakan,
                "upahPokok" => $upahPokok,
                "lemburKerja" => $lemburKerja,
                "tunjanganKesejahteraan" => $tunjanganKesejahteraan,
                "potongan" => $potongan,
                "jumlahUpah" => $jumlahUpah,
            ];
        }


        $grouped = [];

        foreach ($dataPayRolls as $d) {
            $bagian = $d['namaBagian'];

            if (!isset($grouped[$bagian])) {
                // Inisialisasi
                $grouped[$bagian] = [
                    "namaBagian" => $bagian,
                    "totalTotalUpah" => 0,
                    "totalUangMakan" => 0,
                    "totalUpahPokok" => 0,
                    "totalLemburKerja" => 0,
                    "totalTunjanganKesejahteraan" => 0,
                    "totalPotongan" => 0,
                    "totalJumlahUpah" => 0,
                    "employees" => [],
                ];
            }

            // Sum per field
            $grouped[$bagian]['totalTotalUpah'] += $d['totalUpah'];
            $grouped[$bagian]['totalUangMakan'] += $d['uangMakan'];
            $grouped[$bagian]['totalUpahPokok'] += $d['upahPokok'];
            $grouped[$bagian]['totalLemburKerja'] += $d['lemburKerja'];
            $grouped[$bagian]['totalTunjanganKesejahteraan'] += $d['tunjanganKesejahteraan'];
            $grouped[$bagian]['totalPotongan'] += $d['potongan'];
            $grouped[$bagian]['totalJumlahUpah'] += $d['jumlahUpah'];

            // Simpan detail pegawai (opsional)
            $grouped[$bagian]['employees'][] = $d;
        }

        // Jika mau hasil sebagai array numerik
        $groupedResult = array_values($grouped);

        return [
            'dataPayroll' => $groupedResult,
            'total' => [
                'subTotalUpah' => $subTotalUpah,
                'subTotalUangMakan' => $subTotalUangMakan,
                'subTotalUpahPokok' => $subTotalUpahPokok,
                'subTotalLemburKerja' => $subTotalLemburKerja,
                'subTotalTunjanganKesejahteraan' => $subTotalTunjanganKesejahteraan,
                'subTotalPotongan' => $subTotalPotongan,
                'subTotalJumlahUpah' => $subTotalJumlahUpah
            ]
        ];
    }



    public function getPayrollDetail(
        $yearMonth,
        $divisionID,
        $companyID,
        $bagianID,
        $tipes
    ) {
        $payrollModel = new PayrollsModel();
        $employeeModel = new EmployeesModel();
        $formLemburModel = new FormLemburModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $companyModel = new CompaniesModel();
        $divisiModel = new DivisisModel();
        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $payrollGajiConjunctionModel = new PayrollGajiConjunctionModel();
        $tunjanganModel = new TunjanganModel();

        $employeePayroll = $this->asArray()->select('payrolls.*, employees.division_id')
            ->join('employees', 'employees.id = payrolls.employee_id', 'left')
            ->where('payrolls.year_month', $yearMonth)
            ->where('employees.company_id', $companyID)
            ->where('employees.division_id', $divisionID);

        if ($bagianID != '' && !empty($bagianID)) {
            $employeePayroll->where('employees.bagian_id', $bagianID);
        }

        if (count($tipes) > 0) {
            $employeePayroll->whereIn('employees.tipe', $tipes);
        }
        $employeePayroll->orderBy('employees.nip', "asc");
        $data = [];

        $tunjanganGajiPokok = $tunjanganModel->where('company_id', $companyID)->where('is_gaji_harian', 1)->where('deletedAt', null)->first();
        $tunjanganCadangan = $tunjanganModel->where('company_id', $companyID)->where('is_cadangan', 1)->where('deletedAt', null)->first();

        foreach ($employeePayroll->findAll() as $ep) {
            $payrollDetail = $payrollModel->where('id', $ep['id'])->first();
            $employee = $employeeModel->getSingleEmployee($ep['employee_id']);
            $splitJamLembur = $formLemburModel->getTotalLemburJamPertamaKeduaByDateRange(
                $payrollDetail['employee_id'],
                $payrollDetail['start_date'],
                $payrollDetail['end_date']
            );
            $payrollDetail['total_gaji_harian_plus_cadangan'] = $payrollDetail['nominal_gaji_harian'] + $payrollDetail['nominal_cadangan'];
            $totalPinjamanDiambil = $pinjamanKaryawanModel->getTotalPinjamanKaryawanDiambil($payrollDetail['employee_id'], $payrollDetail['year_month']);
            $perhitunganGaji = $payrollGajiModel->getPerhitunganKomponenGajiPayrollPrint($ep['id']);
            // $uangMakan = $payrollGajiConjunctionModel->getPayrollUangMakan($payrollDetail['id']);
            $tunjanganTidakTetap = $payrollGajiConjunctionModel->getPayrollTunjanganTidakTetap($payrollDetail['id']);


            $data[] = [
                'payroll' => $payrollDetail,
                'employee' => $employee,
                'totalLemburJamPertama' => number_format($splitJamLembur['jamPertama'], 1),
                'totalLemburJamKedua' => number_format($splitJamLembur['jamKedua'], 1),
                'perhitunganGaji' => $perhitunganGaji,
                'uangMakan' => 0,
                'tunjanganGajiPokok' => $tunjanganGajiPokok,
                'tunjanganCadangan' => $tunjanganCadangan,
                'totalPinjamanDiambil' => $totalPinjamanDiambil,
                'tunjanganTidakTetap' => $tunjanganTidakTetap
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

    public function getPotonganPayrollRevamp(
        $yearMonth,
        $companyID,
        $divisionID,
        $tipes,
        $bagianId
    ) {
        $db = \Config\Database::connect();

        /* =====================================================
        * MASTER DATA
        * ===================================================== */
        $bagianQry = $db->table('bagian')->where('division_id', $divisionID)->where('deletedAt', null);

        if (!empty($bagianId) && $bagianId != '') {
            $bagianQry->where('id', $bagianId);
        }
        $bagianData =  $bagianQry->orderBy('nama_bagian', "ASC")->get()->getResultArray();

        /* =====================================================
        * PAYROLL + EMPLOYEE (1 QUERY)
        * ===================================================== */
        $builder = $db->table('payrolls');
        $builder->select("
            payrolls.id AS payroll_id,
            payrolls.*,
            employees.id AS employeeID,
            employees.name,
            employees.nip,
            employees.bagian_id
       ");
        $builder->join('employees', 'employees.id = payrolls.employee_id', 'left');
        $builder->where('employees.division_id', $divisionID);
        $builder->where('payrolls.year_month', $yearMonth);

        if (!empty($tipes)) {
            $builder->whereIn('employees.tipe', $tipes);
        }

        if (!empty($bagianId)) {
            $builder->where('employees.bagian_id', $bagianId);
        }

        $payrollRows = $builder->orderBy('employees.nip', 'asc')->get()->getResultArray();

        /* =====================================================
        * POTONGAN AGGREGATE (1 QUERY)
        * ===================================================== */
        $payrollIds = array_column($payrollRows, 'payroll_id');

        $potonganRows = [];
        if (!empty($payrollIds)) {
            $potonganRows = $db->table('payroll_gaji_conjunction pgc')
                ->select('
                pgc.payroll_id,
                tunjangan.name,
                SUM(pgc.nominal) AS total
            ')
                ->join('tunjangan', 'tunjangan.id = pgc.tunjangan_id')
                ->whereIn('pgc.payroll_id', $payrollIds)
                ->groupBy('pgc.payroll_id, tunjangan.name')
                ->get()->getResultArray();
        }

        /* =====================================================
        * MAP POTONGAN
        * ===================================================== */
        $potMap = [];
        foreach ($potonganRows as $p) {
            $potMap[$p['payroll_id']][$p['name']] = (float)$p['total'];
        }

        /* =====================================================
        * INDEX PAYROLL BY BAGIAN
        * ===================================================== */
        $payrollByBagian = [];
        foreach ($payrollRows as $p) {
            $payrollByBagian[$p['bagian_id']][] = $p;
        }

        /* =====================================================
        * BUILD RESULT (SAMA PLEK)
        * ===================================================== */
        $res = [];
        $potonganRes = [
            'totPotBonKoperasi' => 0,
            'totPotBpjs' => 0,
            'totPotDenda' => 0,
            'totPotIuranKoperasi' => 0,
            'totPotKantin' => 0,
            'totPinjamanKoperasi' => 0,
            'totPotPinjamanLainLain' => 0,
            'totPotBajuSeragam' => 0,
            'totPotSepatuCelanaTopi' => 0,
            'totPotTutupMulut' => 0,
            'totPotSpm' => 0,
            'totPotStm' => 0,
            'totPotAstek' => 0,
            // 'totPotUangMakan' => 0,
            'totPotPinjaman' => 0,
            'totPotongan' => 0
        ];

        foreach ($bagianData as $b) {

            $detail = [];
            $potonganSingle = [
                'totPotBonKoperasi' => 0,
                'totPotBpjs' => 0,
                'totPotDenda' => 0,
                'totPotIuranKoperasi' => 0,
                'totPotKantin' => 0,
                'totPinjamanKoperasi' => 0,
                'totPotPinjamanLainLain' => 0,
                'totPotBajuSeragam' => 0,
                'totPotSepatuCelanaTopi' => 0,
                'totPotTutupMulut' => 0,
                'totPotSpm' => 0,
                'totPotStm' => 0,
                'totPotAstek' => 0,
                // 'totPotUangMakan' => 0,
                'totPotPinjaman' => 0,
                'totPotongan' => 0
            ];

            foreach ($payrollByBagian[$b['id']] ?? [] as $ep) {
                $pid = $ep['payroll_id'];
                $pots = $potMap[$pid] ?? [];

                $dp = [
                    'employee' => $ep,
                    'potBonKoperasi' => $pots['BON KOPERASI'] ?? 0,
                    'potBpjs' => $pots['BPJS'] ?? 0,
                    'potDenda' => $pots['DENDA'] ?? 0,
                    'potIuranKoperasi' => $pots['IURAN KOPERASI'] ?? 0,
                    'potKantin' => $pots['KANTIN'] ?? 0,
                    'potPinjamanKoperasi' => $pots['PINJAMAN KOPERASI'] ?? 0,
                    'potPinjamanLainLain' => $pots['PINJAMAN LAIN LAIN'] ?? 0,
                    'potBajuSeragam' => $pots['POTONGAN BAJU SERAGAM'] ?? 0,
                    'potSepatuCelanaTopi' => $pots['POTONGAN SEPATU, CELANA, TOPI'] ?? 0,
                    'potTutupMulut' => $pots['POTONGAN TUTUP MULUT'] ?? 0,
                    'potSpm' => $pots['SPM'] ?? 0,
                    'potStm' => $pots['STM'] ?? 0,
                    // 'potUangMakan' => $pots['UANG MAKAN'] ?? 0,
                    'potPinjaman' => (float)$ep['nominal_pinjaman_karyawan'],
                    'totPotongan' => 0
                ];

                $dp['totPotongan'] =
                    $dp['potBonKoperasi'] +
                    $dp['potBpjs'] +
                    $dp['potDenda'] +
                    $dp['potIuranKoperasi'] +
                    $dp['potKantin'] +
                    $dp['potPinjamanKoperasi'] +
                    $dp['potPinjamanLainLain'] +
                    $dp['potBajuSeragam'] +
                    $dp['potSepatuCelanaTopi'] +
                    $dp['potTutupMulut'] +
                    $dp['potSpm'] +
                    $dp['potStm'] +
                    // $dp['potUangMakan'] +
                    $dp['potPinjaman'];

                foreach ($potonganSingle as $k => $v) {
                    if ($k !== 'totPotongan') {
                        $potonganSingle[$k] += $dp[lcfirst(str_replace('tot', '', $k))] ?? 0;
                    }
                }
                $potonganSingle['totPotongan'] += $dp['totPotongan'];

                $detail[] = $dp;
            }

            foreach ($potonganSingle as $k => $v) {
                $potonganRes[$k] += $v;
            }

            $res[] = [
                'bagian' => $b['nama_bagian'],
                'detail' => $detail,
                'totPotonganSingle' => $potonganSingle
            ];
        }

        return [
            'res' => $res,
            'potAll' => $potonganRes,
            'unit' => $db->table('companies')->where('id', $companyID)->get()->getRowArray(),
            'divisi' => $db->table('divisis')->where('id', $divisionID)->get()->getRowArray()
        ];
    }

    public function getPotonganPayroll(
        $yearMonth,
        $companyID,
        $divisionID,
        $tipes
    ) {
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
            'totPinjamanKoperasi' => 0,
            'totPotPinjamanLainLain' => 0,
            'totPotBajuSeragam' => 0,
            'totPotSepatuCelanaTopi' => 0,
            'totPotTutupMulut' => 0,
            'totPotSpm' => 0,
            'totPotStm' => 0,
            'totPotAstek' => 0,
            'totPotUangMakan' => 0,
            'totPotPinjaman' => 0,
            'totPotongan' => 0

        ];

        foreach ($bagianData as $b) {
            $employeePayrollQry = $employeePayroll = $payrollModel
                ->select('employees.name, employees.id AS employeeID, payrolls.*')
                ->join('employees', 'payrolls.employee_id = employees.id', 'left')
                ->where('employees.bagian_id', $b['id'])
                ->where('employees.division_id', $divisionID)
                ->where('year_month', $yearMonth);

            if (count($tipes) > 0) {
                $employeePayrollQry->whereIn('employees.tipe', $tipes);
            }

            $employeePayroll =  $employeePayrollQry->findAll();

            $detail = array();
            $potonganSingle = [
                'totPotBonKoperasi' => 0,
                'totPotBpjs' => 0,
                'totPotDenda' => 0,
                'totPotIuranKoperasi' => 0,
                'totPotKantin' => 0,
                'totPinjamanKoperasi' => 0,
                'totPotPinjamanLainLain' => 0,
                'totPotBajuSeragam' => 0,
                'totPotSepatuCelanaTopi' => 0,
                'totPotTutupMulut' => 0,
                'totPotSpm' => 0,
                'totPotStm' => 0,
                'totPotAstek' => 0,
                'totPotUangMakan' => 0,
                'totPotPinjaman' => 0,
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
                $potonganSingle['totPinjamanKoperasi'] += $dp['potPinjamanKoperasi'];
                $potonganSingle['totPotPinjamanLainLain'] += $dp['potPinjamanLainLain'];
                $potonganSingle['totPotBajuSeragam'] += $dp['potBajuSeragam'];
                $potonganSingle['totPotSepatuCelanaTopi'] += $dp['potSepatuCelanaTopi'];
                $potonganSingle['totPotTutupMulut'] += $dp['potTutupMulut'];
                $potonganSingle['totPotSpm'] += $dp['potSpm'];
                $potonganSingle['totPotStm'] += $dp['potStm'];
                $potonganSingle['totPotUangMakan'] += $dp['potUangMakan'];
                $potonganSingle['totPotPinjaman'] += $dp['potPinjaman'];
                $potonganSingle['totPotongan'] += $dp['totPotongan'];
            }

            $potonganRes['totPotBonKoperasi'] += $potonganSingle['totPotBonKoperasi'];
            $potonganRes['totPotBpjs'] += $potonganSingle['totPotBpjs'];
            $potonganRes['totPotDenda'] += $potonganSingle['totPotDenda'];
            $potonganRes['totPotIuranKoperasi'] += $potonganSingle['totPotIuranKoperasi'];
            $potonganRes['totPotKantin'] += $potonganSingle['totPotKantin'];
            $potonganRes['totPinjamanKoperasi'] += $potonganSingle['totPinjamanKoperasi'];
            $potonganRes['totPotPinjamanLainLain'] += $potonganSingle['totPotPinjamanLainLain'];
            $potonganRes['totPotBajuSeragam'] += $potonganSingle['totPotBajuSeragam'];
            $potonganRes['totPotSepatuCelanaTopi'] += $potonganSingle['totPotSepatuCelanaTopi'];
            $potonganRes['totPotTutupMulut'] += $potonganSingle['totPotTutupMulut'];
            $potonganRes['totPotSpm'] += $potonganSingle['totPotSpm'];
            $potonganRes['totPotStm'] += $potonganSingle['totPotStm'];
            $potonganRes['totPotUangMakan'] += $potonganSingle['totPotUangMakan'];
            $potonganRes['totPotPinjaman'] += $potonganSingle['totPotPinjaman'];
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

    public function getSummaryPayroll(
        $yearMonth,
        $companyID,
        $divisionID,
        $bagianID,
        $tipes,
        $payrollId
    ) {
        $divisiModel = new DivisisModel();
        $companyModel = new CompaniesModel();
        $bagianModel = new BagianModel();
        $payrollGajiHarianModel = new PayrollGajiHarianModel();
        $payrollGajiConjunctionModel = new PayrollGajiConjunctionModel();

        $bagianQry = $bagianModel->where('division_id', $divisionID)->orderBy('nama_bagian', "ASC")->where('deletedAt', null);
        if (!empty($bagianID) && $bagianID != '') {
            $bagianQry->where('id', $bagianID);
        }
        $bagianData = $bagianQry->findAll();

        $res = [];
        $upahPokok = 0;
        $tunjanganPlusSkalaUpah = 0;
        $lemburTotal = 0;
        $totalUpah = 0;
        $potongan = 0;
        $upahBersih = 0;
        $orangTotal = 0;
        $lembur = 0;
        $jamKerja = 0;


        foreach ($bagianData as $i => $b) {
            //----------------------------------------------------
            $selectQry = "
                SUM(nominal) AS uangMakan
            ";

            $uangMakanQry = $payrollGajiConjunctionModel
                ->select($selectQry)
                ->join('tunjangan', 'payroll_gaji_conjunction.tunjangan_id = tunjangan.id', 'left')
                ->join('payrolls', 'payrolls.id = payroll_gaji_conjunction.payroll_id', 'left')
                ->join('employees', 'employees.id = payrolls.employee_id', 'left')
                ->where('payroll_gaji_conjunction.year_month', $yearMonth)
                ->where('employees.company_id', $companyID)
                ->where('employees.bagian_id', $b['id'])
                ->where('tunjangan.name', "UANG MAKAN");

            if (count($tipes) > 0) {
                $uangMakanQry->whereIn('employees.tipe', $tipes);
            }

            $employeeUangMakan = $uangMakanQry->findAll();

            $selectQry = "
                SUM(payroll_gaji_harian.nominal_gaji_harian) AS upahPokok,
                SUM(payroll_gaji_harian.nominal_cadangan) AS skala_upah,
                SUM(payroll_gaji_harian.total_jam) AS total_jam
            ";

            $upahPokokSkalaQry = $payrollGajiHarianModel
                ->select($selectQry)
                ->join('payrolls', 'payrolls.id = payroll_gaji_harian.payroll_id', 'left')
                ->join('employees', 'employees.id = payrolls.employee_id', 'left')
                ->where('payrolls.year_month', $yearMonth)
                ->where('employees.company_id', $companyID)
                ->where('employees.bagian_id', $b['id']);

            if (count($tipes) > 0) {
                $upahPokokSkalaQry->whereIn('employees.tipe', $tipes);
            }

            $employeeUpahPokokSkalaTotal = $upahPokokSkalaQry->findAll();

            //---------------------------------------------------
            $selectQry = "
                COUNT(DISTINCT payrolls.employee_id) AS totalEmployee, 
                '0' AS upahPokok, 
                SUM(nominal_penambahan_gaji) AS tunjangan,
                '0' AS skala_upah,
                SUM(nominal_uang_lembur) AS lembur,
                SUM(nominal_gaji_diterima) AS total_upah,
                SUM(nominal_pengurangan_gaji) AS potongan
            ";
            $employeePayrolQry = $this->asArray()->select($selectQry)
                ->join('employees', 'employees.id = payrolls.employee_id', 'left')
                ->where('payrolls.year_month', $yearMonth)
                ->where('employees.company_id', $companyID)
                ->where('employees.bagian_id', $b['id']);

            if (count($tipes) > 0) {
                $employeePayrolQry->whereIn('employees.tipe', $tipes);
            }
            $employeePayrollTotal =  $employeePayrolQry->groupBy('employees.bagian_id')->findAll();

            if (count($employeePayrollTotal) > 0) {
                if ($employeePayrollTotal[0]['totalEmployee'] != 0) {

                    // update value
                    $employeePayrollTotal[0]['upahPokok'] = $employeeUpahPokokSkalaTotal[0]['upahPokok'];
                    $employeePayrollTotal[0]['skala_upah'] = $employeeUpahPokokSkalaTotal[0]['skala_upah'];
                    $employeePayrollTotal[0]['lembur'] = $employeePayrollTotal[0]['lembur'] + $employeeUangMakan[0]['uangMakan'];
                    // end update
                    $totalUpahSingle =  $employeePayrollTotal[0]['upahPokok'] + $employeePayrollTotal[0]['skala_upah'] + $employeePayrollTotal[0]['lembur']; // Total Upah = Upah Pokok + skala + lembur (udah include uang makan)
                    $totalUpahBersihSingle = $totalUpahSingle - $employeePayrollTotal[0]['potongan']; // uPAH BERSIH = total upah - potongan
                    $totalJam = $employeeUpahPokokSkalaTotal[0]['total_jam'];

                    $employeePayrollTotal[0]['total_upah'] = $totalUpahSingle;
                    $employeePayrollTotal[0]['upahBersih'] = $totalUpahBersihSingle;

                    // set total
                    $upahPokok += $employeePayrollTotal[0]['upahPokok'];
                    $tunjanganPlusSkalaUpah += ($employeePayrollTotal[0]['skala_upah']);
                    $lemburTotal += $employeePayrollTotal[0]['lembur'];
                    $totalUpah += $totalUpahSingle;
                    $potongan += $employeePayrollTotal[0]['potongan'];
                    $upahBersih += $totalUpahBersihSingle;
                    $orangTotal += $employeePayrollTotal[0]['totalEmployee'];
                    $lembur +=  static::getTotalJamLemburInOnePeriode($divisionID, $b['id'], $payrollId);
                    $jamKerja += $totalJam;

                    $res[] = [
                        'bagian' => $b['nama_bagian'],
                        'payrollTotal' => $employeePayrollTotal,
                        'totalJamKerja' => $totalJam,
                        'totalJamLembur' => static::getTotalJamLemburInOnePeriode($divisionID, $b['id'], $payrollId),
                    ];
                }
            }
        }

        // dd($res);

        return [
            'res' => $res,
            'upahPokokTotal' => $upahPokok,
            'orangTotal' => $orangTotal,
            'tunjanganPlusSkalaUpah' => $tunjanganPlusSkalaUpah,
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

    public function getEmployeeIdByPayrollAmt($payrollIds)
    {
        $dataQry = $this->asArray()->whereIn('id', $payrollIds)->where('deletedAt', null)->findAll();
        return $dataQry;
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
            'potPinjamanKoperasi' => ($employeePayroll == null) ? 0 : static::getPotonganLikeStr("PINJAMAN KOPERASI", $employeePayroll['id']),
            'potPinjamanLainLain' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('PINJAMAN LAIN LAIN', $employeePayroll['id']),
            'potBajuSeragam' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('POTONGAN BAJU SERAGAM', $employeePayroll['id']),
            'potSepatuCelanaTopi' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('POTONGAN SEPATU, CELANA, TOPI', $employeePayroll['id']),
            'potTutupMulut' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('POTONGAN TUTUP MULUT', $employeePayroll['id']),
            'potSpm' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('SPM', $employeePayroll['id']),
            'potStm' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('STM', $employeePayroll['id']),
            'potUangMakan' => ($employeePayroll == null) ? 0 :  static::getPotonganLikeStr('UANG MAKAN', $employeePayroll['id']),
            'potPinjaman' => ($employeePayroll == null) ? 0 : $employeePayroll['nominal_pinjaman_karyawan'],
            'totPotongan' => 0
        ];

        $res['totPotongan'] = $res['potBonKoperasi'] +
            $res['potBpjs'] +
            $res['potDenda'] +
            $res['potIuranKoperasi'] +
            $res['potKantin'] +
            $res['potPinjamanKoperasi'] +
            $res['potPinjamanLainLain'] +
            $res['potBajuSeragam'] +
            $res['potSepatuCelanaTopi'] +
            $res['potTutupMulut'] +
            $res['potSpm'] +
            $res['potStm'] +
            $res['potUangMakan'] +
            $res['potPinjaman'];
        return $res;
    }

    static function getPotonganLikeStr($str, $payrollID)
    {
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $res = $payrollGajiModel->join('tunjangan', 'tunjangan.id = payroll_gaji_conjunction.tunjangan_id', 'left')
            ->where('payroll_id', $payrollID)
            ->where('tunjangan.name', $str)
            ->first();
        return $res == null ? 0 : $res['nominal'];
    }

    static function getTotalJamLemburInOnePeriode(
        $divisionID,
        $bagianID,
        $payrollId
    ) {
        $formLemburModel = new FormLemburModel();
        $payrollModel = new PayrollsModel();
        $payroll = $payrollModel->where('id', $payrollId)->first();

        $startDate = $payroll['start_date'];
        $endDate   = $payroll['end_date'];

        $result = $formLemburModel
            ->select("SUM(form_lembur.total_jam_lembur) AS totalJamLembur")
            ->join('employees', 'employees.id = form_lembur.employee_id', 'left')
            ->where('employees.division_id', $divisionID)
            ->where('employees.bagian_id', $bagianID)
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->first();

        return $result['totalJamLembur'] ?? 0;
    }

    static function getTotalJamKerjaInOnePeriode(
        $divisionID,
        $bagianID,
        $payrollId
    ) {
        $db = db_connect();
        $payrollModel = new PayrollsModel();
        $payroll = $payrollModel->where('id', $payrollId)->first();

        $startDate = $payroll['start_date'];
        $endDate   = $payroll['end_date'];

        $builder = $db->table('attendances')
            ->select("FLOOR(SUM(TIME_TO_SEC(TIMEDIFF(attendances.checkout, attendances.checkin)) / 3600)) AS totalJamKerja")
            ->join('employees', 'employees.id = attendances.employee_id', 'left')
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

    public function getPayrollId()
    {
        $result = $this->asArray()->where('deletedAt', null)->findAll();
        $payrollIds = array_column($result, 'id');
        return $payrollIds;
    }

    public function getListRiwayatPayroll(
        $condition,
        $addCondition,
        $year,
        $limit = 10,
        $offset = 0
    ) {
        $availableSort = [
            'payrolls.year_month' => 'payrolls.year_month',
            'payrolls.nominal_gaji_diterima' => 'payrolls.nominal_gaji_diterima',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];
        $sort = $availableSort[$addCondition['sort'] ?? 'payrolls.year_month'] ?? 'payrolls.year_month';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'desc';

        $selectQry = "payrolls.*";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->like('payrolls.year_month', $year, 'after')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);
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
}
