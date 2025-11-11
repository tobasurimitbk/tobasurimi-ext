<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendanceKeterlambatanModel;
use App\Models\AttendancesModel;
use App\Models\BagianModel;
use App\Models\CompaniesModel;
use App\Models\DendaAbsenHarianModel;
use App\Models\DivisisModel;
use App\Models\EmployeesModel;
use App\Models\FormLemburModel;
use App\Models\FormPerizinanNotApprovedModel;
use App\Models\GajiDivisiModel;
use App\Models\GolonganModel;
use App\Models\PayrollGajiConjunctionModel;
use App\Models\PayrollGajiHarianModel;
use App\Models\PayrollsModel;
use App\Models\PinjamanKaryawanModel;
use App\Models\TunjanganModel;
use App\Models\UangMakanHarianModel;
use Dompdf\Dompdf;
use Exception;

class Payroll extends BaseController
{
    protected $token;
    protected $this_company_id, $userID;
    protected $payrollModel;
    protected $divisiModel;
    protected $golonganModel;
    protected $bagianModel;
    protected $attendanceModel;
    protected $formPerizinanNotApprovedModel;
    protected $employeeModel;
    protected $attendanceKeterlambatanModel;
    protected $payrollGajiModel;
    protected $formLemburModel;
    protected $rekapPerizinanNotApprovedModel;
    protected $pinjamanKaryawanModel;
    protected $payrollGajiHarianModel;
    protected $companyModel;
    protected $gajiDivisiModel;
    protected $uangMakanHarianModel;
    protected $tunjanganModel;
    protected $dendaAbsenHarianModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userID = session()->get('login')->user_id;
        $this->payrollModel = new PayrollsModel();
        $this->divisiModel = new DivisisModel();
        $this->golonganModel = new GolonganModel();
        $this->bagianModel = new BagianModel();
        $this->attendanceModel = new AttendancesModel();
        $this->employeeModel = new EmployeesModel();
        $this->formPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $this->attendanceKeterlambatanModel = new AttendanceKeterlambatanModel();
        $this->payrollGajiModel = new PayrollGajiConjunctionModel();
        $this->formLemburModel = new FormLemburModel();
        $this->rekapPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $this->pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $this->payrollGajiHarianModel = new PayrollGajiHarianModel();
        $this->companyModel = new CompaniesModel();
        $this->gajiDivisiModel = new GajiDivisiModel();
        $this->uangMakanHarianModel = new UangMakanHarianModel();
        $this->tunjanganModel = new TunjanganModel();
        $this->dendaAbsenHarianModel = new DendaAbsenHarianModel();
    }

    public function index()
    {
        $data = [
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'bagian' => [],
            'golongan' => $this->golonganModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
        ];

        return view('hr/payroll/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = [
            'employees.company_id' => $this->this_company_id,
            "employees.deletedAt" => null,
            "year_month" => $this->request->getVar('month'),
        ];

        $addCondition = [
            "divisi_id"          => $this->request->getGet("divisi_id"),
            "bagian_id"          => $this->request->getGet("bagian_id"),
            "employee_id"        => $this->request->getGet("employee_id"),
            "tipe"               => $this->request->getGet("golongan"),
            "sort"               => $this->request->getGet("sort"),
            "sortType"           => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $payrollData = $this->payrollModel->getList($condition, $addCondition, $limit, $offset);
        $dataPayRolls = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($payrollData['data'] as $p) {
            array_push($dataPayRolls, [
                "no" => $no++,
                "id" => encrypt($p->id),
                "employee_id" => $p->employee_id,
                "nip" => $p->nip,
                "namaBagian" => $p->nama_bagian,
                "name"  => $p->name,
                "divisi" => $p->divisi,
                "hariKerja" => $p->hadir_final . " Hari",
                "startDate" => date('d/m/Y', strtotime($p->start_date)),
                "endDate" => date('d/m/Y', strtotime($p->end_date)),
                "upahBersih" => $p->nominal_uang_gaji,
                "totalLembur" =>  $p->nominal_uang_lembur,
                "totalGajiLembur" => $p->nominal_uang_gaji + $p->nominal_uang_lembur,
                "totalPenguranganGaji" => $p->nominal_pengurangan_gaji,
                "sisaGaji" => $p->nominal_gaji_diterima
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $payrollData['totalData'],
            "recordsFiltered"   => $payrollData['totalFilteredData'],
            "data"              => $dataPayRolls,
            "payload"           => $payload,
            'test' => $addCondition
        ];

        return response()->setJSON($data);
    }

    public function generateGlobalPayroll()
    {
        $yearMonth = $this->request->getVar('yearMonth');
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
        $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));
        $divisionGlobalID = $this->request->getVar('divisionGlobalID');

        if ($divisionGlobalID == 'ALL') {
            $dataAbsensiGenerated = $this->attendanceModel
                ->where('year_month', $yearMonth)
                ->where('company_id', $this->this_company_id)
                ->countAllResults();
        } else {
            $dataAbsensiGenerated = $this->attendanceModel
                ->where('year_month', $yearMonth)
                ->where('division_id', $divisionGlobalID)
                ->where('company_id', $this->this_company_id)
                ->countAllResults();
        }

        if ($dataAbsensiGenerated != 0) {

            // get employees
            if ($divisionGlobalID == 'ALL') {
                $employeesData = $this->employeeModel->getEmployees($this->this_company_id);
                // delete firts if ada
                $this->payrollModel->where('company_id', $this->this_company_id)
                    ->where('year_month', $yearMonth)
                    ->delete();
            } else {
                $employeesData = $this->employeeModel->getEmployeesByDivisionID($this->this_company_id, $this->request->getVar('divisionGlobalID'));
                // delete firts if ada
                $this->payrollModel->where('company_id', $this->this_company_id)
                    ->where('year_month', $yearMonth)
                    ->where('division_id', $divisionGlobalID)
                    ->delete();
            }
            $test = [];
            // Insert Again
            foreach ($employeesData as $e) {
                $status = $this->attendanceModel->getStatusAttendancesInRange($startDate, $endDate, $e['id']);

                // payroll insert
                $payrollID = $this->payrollModel->insert([
                    "company_id" => $e['company_id'],
                    "employee_id" => $e['id'],
                    "division_id" => $e['division_id'],
                    "year_month" => $yearMonth,
                    "cuti_tahunan" => $status['CUTI TAHUNAN_CT'],
                    "cuti_haid" => $status['CUTI HAID_CHD'],
                    "cuti_hamil" => $status['CUTI HAMIL_CHL'],
                    "cuti_melahirkan" => $status['CUTI MELAHIRKAN_CM'],
                    "izin" => $status['POTONG GAJI_PG'],
                    "sakit" => $status['SAKIT_S'],
                    "rl" => $status['RL_RL'],
                    "hadir" => $status['HADIR_H'],
                    "libur" => $status['LIBUR_L'],
                    "alpha" => $status['ALPHA_A'],
                    "dinas" => $status['DINAS_D'],
                    "cuti_keguguran" => $status['CUTI KEGUGURAN_CKG'],
                    "hadir_final" => 0,
                    "total_perizinan_not_approved" => 0,
                    "total_perizinan_approved" => 0,
                    "nominal_cadangan" => 0,
                    "nominal_gaji_harian" => 0,
                    "nominal_pinjaman_karyawan" => 0,
                    "nominal_uang_gaji" => 0,
                    "nominal_uang_lembur" => 0,
                    "nominal_pengurangan_gaji" => 0,
                    "nominal_gaji_diterima" => 0,
                    "nominal_penambahan_gaji" => 0,
                    "start_date" => $startDate,
                    "end_date" => $endDate
                ]);

                // generate keterlambatan
                $this->attendanceKeterlambatanModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $yearMonth,
                    $startDate,
                    $endDate
                );

                // generate payroll gaji 
                $this->payrollGajiModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $yearMonth
                );

                // generate form perijinan not approved
                $res = $this->formPerizinanNotApprovedModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $yearMonth,
                    $startDate,
                    $endDate
                );

                // update payroll
                $payrollFinal = $this->payrollModel->generate(
                    $e['id'],
                    $yearMonth,
                    $payrollID,
                    $startDate,
                    $endDate
                );

                $this->payrollModel
                    ->set('hadir_final', ($status['HADIR_H'] + $res['total_perizinan_approved']))
                    ->set('total_perizinan_not_approved', $res['total_perizinan_not_approved'])
                    ->set('total_perizinan_approved', $res['total_perizinan_approved'])
                    ->set('nominal_cadangan', $payrollFinal['nominal_cadangan'])
                    ->set('nominal_gaji_harian', $payrollFinal['nominal_gaji_harian'])
                    ->set('nominal_pinjaman_karyawan', $payrollFinal['nominal_pinjaman_karyawan'])
                    ->set('nominal_uang_gaji', $payrollFinal['nominal_uang_gaji'])
                    ->set('nominal_uang_lembur', $payrollFinal['nominal_uang_lembur'])
                    ->set('nominal_pengurangan_gaji', $payrollFinal['nominal_pengurangan_gaji'])
                    ->set('nominal_gaji_diterima', $payrollFinal['nominal_gaji_diterima'])
                    ->set('nominal_penambahan_gaji', $payrollFinal['nominal_penambahan_gaji'])
                    ->where('id', $payrollID)
                    ->update();
            }

            return response()->setJSON([
                'message' => "Data Payroll global berhasil digenerate ",
                'status' => true,
                'test' => $test
            ]);
        } else {
            return response()->setJSON([
                'message' => "Data absensi bulan " . $yearMonth . " tidak ada",
                'status' => false
            ]);
        }
    }

    public function generateSinglePayroll()
    {
        $yearMonth = $this->request->getVar('yearMonth');
        $employeeID = $this->request->getVar("employeeID");
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
        $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));

        // $attendanceModel = new AttendancesModel();
        // $payrollModel = new PayrollsModel();
        // $employeesModel = new EmployeesModel();
        // $FormPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        // $AttendanceKeterlambatanModel = new AttendanceKeterlambatanModel();
        // $PayrollGajiModel = new PayrollGajiConjunctionModel();

        // delete first
        $this->payrollModel->where('company_id', $this->this_company_id)
            ->where('employee_id', $employeeID)
            ->delete();

        // get employee
        $employeesData = $this->employeeModel->where('id', $employeeID)->first();

        if ($employeesData['status'] != "Aktif") {
            return $this->response->setJSON([
                'message' => "Status karyawan " . $employeesData['name'] . " adalah " . $employeesData['status'],
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $status = $this->attendanceModel->getStatusAttendancesInRange($startDate, $endDate, $employeesData['id']);
        // payroll insert
        $payrollID = $this->payrollModel->insert([
            "company_id" => $employeesData['company_id'],
            "employee_id" => $employeesData['id'],
            "division_id" => $employeesData['division_id'],
            "year_month" => $yearMonth,
            "cuti_tahunan" => $status['CUTI TAHUNAN_CT'],
            "cuti_haid" => $status['CUTI HAID_CHD'],
            "cuti_hamil" => $status['CUTI HAMIL_CHL'],
            "cuti_melahirkan" => $status['CUTI MELAHIRKAN_CM'],
            "izin" => $status['POTONG GAJI_PG'],
            "sakit" => $status['SAKIT_S'],
            "rl" => $status['RL_RL'],
            "hadir" => $status['HADIR_H'],
            "libur" => $status['LIBUR_L'],
            "alpha" => $status['ALPHA_A'],
            "dinas" => $status['DINAS_D'],
            "cuti_keguguran" => $status['CUTI KEGUGURAN_CKG'],
            "hadir_final" => 0,
            "total_perizinan_not_approved" => 0,
            "total_perizinan_approved" => 0,
            "nominal_cadangan" => 0,
            "nominal_gaji_harian" => 0,
            "nominal_pinjaman_karyawan" => 0,
            "nominal_uang_gaji" => 0,
            "nominal_uang_lembur" => 0,
            "nominal_pengurangan_gaji" => 0,
            "nominal_gaji_diterima" => 0,
            "nominal_penambahan_gaji" => 0,
            "start_date" => $startDate,
            "end_date" => $endDate
        ]);

        // generate keterlambatan
        $this->attendanceKeterlambatanModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $yearMonth,
            $startDate,
            $endDate
        );

        // generate payroll gaji 
        $this->payrollGajiModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $yearMonth
        );

        // generate form perijinan not approved
        $res = $this->formPerizinanNotApprovedModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $yearMonth,
            $startDate,
            $endDate
        );

        // update payroll
        $payrollFinal = $this->payrollModel->generate(
            $employeesData['id'],
            $yearMonth,
            $payrollID,
            $startDate,
            $endDate
        );

        $this->payrollModel
            ->set('hadir_final', ($status['HADIR_H'] + $res['total_perizinan_approved']))
            ->set('total_perizinan_not_approved', $res['total_perizinan_not_approved'])
            ->set('total_perizinan_approved', $res['total_perizinan_approved'])
            ->set('nominal_cadangan', $payrollFinal['nominal_cadangan'])
            ->set('nominal_gaji_harian', $payrollFinal['nominal_gaji_harian'])
            ->set('nominal_pinjaman_karyawan', $payrollFinal['nominal_pinjaman_karyawan'])
            ->set('nominal_uang_gaji', $payrollFinal['nominal_uang_gaji'])
            ->set('nominal_uang_lembur', $payrollFinal['nominal_uang_lembur'])
            ->set('nominal_pengurangan_gaji', $payrollFinal['nominal_pengurangan_gaji'])
            ->set('nominal_gaji_diterima', $payrollFinal['nominal_gaji_diterima'])
            ->set('nominal_penambahan_gaji', $payrollFinal['nominal_penambahan_gaji'])
            ->where('id', $payrollID)
            ->update();


        return response()->setJSON([
            'message' => "Data Payroll atas nama " . $employeesData['name'] . " berhasil digenerate",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function generateSinglePayrollRevamp()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $yearMonth = $this->request->getVar('yearMonth');
            $employeeID = $this->request->getVar("employeeID");
            $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
            $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));


            // Cek absensi
            $dataAbsensiGeneratedQry = $this->attendanceModel->where('year_month', $yearMonth);
            $dataAbsensiGeneratedQry->where('company_id', $this->this_company_id);
            $totalAbsensi = $dataAbsensiGeneratedQry->where('employee_id', $employeeID)->countAllResults();

            if ($totalAbsensi == 0) {
                return response()->setJSON([
                    'message' => "Data absensi bulan " . $yearMonth . " tidak ada",
                    'status'  => false,
                    'token'   => csrf_hash()
                ]);
            }

            // Ambil employee + hapus payroll lama
            // delete first
            $this->payrollModel->where('company_id', $this->this_company_id)
                ->where('employee_id', $employeeID)
                ->delete();

            $employeeIds = [$employeeID];

            if (count($employeeIds) == 0) {
                $db->transRollback();
                return response()->setJSON([
                    'message' => "Data karyawan tidak ditemukan",
                    'status'  => false,
                    'token'   => csrf_hash()
                ]);
            }

            // Total Karyawan Masuk di Hari Libur
            // $mapTotalEmployeeMasukLibur = $this->attendanceModel->getTotalHariLiburEmployeeHadir(
            //     $startDate,
            //     $endDate,
            //     $this->this_company_id,
            //     $employeeIds,
            // );

            // get karyawan masuk tapi ga di approved
            $mapTotalMasukNotApprove = $this->attendanceModel->getTotalEmployeeHadirNotApproved(
                $startDate,
                $endDate,
                $employeeIds,
            );

            // Mapping divisi
            $divisiList = $this->employeeModel->getDivisiByEmployeeAmt($employeeIds);
            $mapDivisi  = [];
            foreach ($divisiList as $d) {
                $mapDivisi[$d['id']] = $d['division_id'];
            }

            // Status attendance
            $statusAttendance = $this->attendanceModel->getStatusAttendancesInRangeAmt(
                $startDate,
                $endDate,
                $employeeIds
            );

            $mapStatusAttendance = [];
            foreach ($statusAttendance as $s) {
                $empId = $s['employee_id'];
                if (!isset($mapStatusAttendance[$empId])) {
                    $mapStatusAttendance[$empId] = [
                        'CUTI TAHUNAN_CT'   => 0,
                        'CUTI HAID_CHD'     => 0,
                        'CUTI HAMIL_CHL'    => 0,
                        'CUTI MELAHIRKAN_CM' => 0,
                        'POTONG GAJI_PG'            => 0,
                        'SAKIT_S'           => 0,
                        'RL_RL'             => 0,
                        'HADIR_H'           => 0,
                        'LIBUR_L'           => 0,
                        'ALPHA_A'           => 0,
                        'DINAS_D'           => 0,
                        'CUTI KEGUGURAN_CKG' => 0
                    ];
                }
                $mapStatusAttendance[$empId][$s['status']] = $s['total'];
            }

            // Insert payroll batch awal
            $dataPayroll = [];
            foreach ($employeeIds as $e) {
                if (isset($mapStatusAttendance[$e])) {
                    //$mapStatusAttendance[$e]['HADIR_H'] = $mapStatusAttendance[$e]['HADIR_H'] - $mapTotalEmployeeMasukLibur[$e];
                    $mapStatusAttendance[$e]['HADIR_H'] = $mapStatusAttendance[$e]['HADIR_H'] - $mapTotalMasukNotApprove[$e];
                }

                $att = $mapStatusAttendance[$e] ?? [];

                $dataPayroll[] = [
                    "company_id"                   => $this->this_company_id,
                    "employee_id"                  => $e,
                    "division_id"                  => $mapDivisi[$e],
                    "year_month"                   => $yearMonth,
                    "cuti_tahunan"                 => $att["CUTI TAHUNAN_CT"] ?? 0,
                    "cuti_haid"                    => $att["CUTI HAID_CHD"] ?? 0,
                    "cuti_hamil"                   => $att["CUTI HAMIL_CHL"] ?? 0,
                    "cuti_melahirkan"              => $att["CUTI MELAHIRKAN_CM"] ?? 0,
                    "izin"                         => $att["POTONG GAJI_PG"] ?? 0,
                    "sakit"                        => $att["SAKIT_S"] ?? 0,
                    "rl"                           => $att["RL_RL"] ?? 0,
                    "hadir"                        => $att["HADIR_H"] ?? 0,
                    "libur"                        => $att["LIBUR_L"] ?? 0,
                    "alpha"                        => $att["ALPHA_A"] ?? 0,
                    "dinas"                        => $att["DINAS_D"] ?? 0,
                    "cuti_keguguran"               => $att["CUTI KEGUGURAN_CKG"] ?? 0,
                    "hadir_final"                  => 0,
                    "total_perizinan_not_approved" => 0,
                    "total_perizinan_approved"     => 0,
                    "nominal_cadangan"             => 0,
                    "nominal_gaji_harian"          => 0,
                    "nominal_pinjaman_karyawan"    => 0,
                    "nominal_uang_gaji"            => 0,
                    "nominal_uang_lembur"          => 0,
                    "nominal_pengurangan_gaji"     => 0,
                    "nominal_gaji_diterima"        => 0,
                    "nominal_penambahan_gaji"      => 0,
                    "start_date"                   => $startDate,
                    "end_date"                     => $endDate
                ];
            }

            $this->payrollModel->insertBatch($dataPayroll);

            // Ambil payroll id hasil insert
            $emmployeePayroll = $this->payrollModel
                ->select('id, employee_id')
                ->where('company_id', $this->this_company_id)
                ->where('year_month', $yearMonth)
                ->whereIn('employee_id', $employeeIds)
                ->findAll();

            $mapEmployeePayroll = [];
            $payrollIds = [];
            foreach ($emmployeePayroll as $e) {
                $mapEmployeePayroll[$e['employee_id']] = $e['id'];
                $payrollIds[] = $e['id'];
            }



            //--------------------------------------
            // Generate Keterlambatan
            //---------------------------------------
            $dataAttendanceKeterlambatan = $this->attendanceKeterlambatanModel->generateAmt(
                $this->this_company_id,
                $startDate,
                $endDate,
                $yearMonth,
                $employeeIds,
                $mapEmployeePayroll
            );
            if (count($dataAttendanceKeterlambatan) != 0) {
                $this->attendanceKeterlambatanModel->insertBatch($dataAttendanceKeterlambatan);
            }

            //--------------------------------------
            // Gaji Conjunction
            //---------------------------------------
            $uangMakanHarian = $this->uangMakanHarianModel->generateUangMakanAmt(
                $employeeIds,
                $startDate,
                $endDate
            );
            $mapUangMakanHarian = [];
            foreach ($uangMakanHarian as $u) {
                $mapUangMakanHarian[$u['employee_id']] = $u['total_nominal'];
            }

            $dendaAbsenHarian = $this->dendaAbsenHarianModel->generateDendaAmt(
                $employeeIds,
                $startDate,
                $endDate
            );
            $mapDendaAbsenHarian = [];
            foreach ($dendaAbsenHarian as $d) {
                $mapDendaAbsenHarian[$d['employee_id']] = $d['total_nominal'];
            }

            $dataPayrollGajiConjunction = $this->payrollGajiModel->generateAmt(
                $mapEmployeePayroll,
                $mapUangMakanHarian,
                $mapDendaAbsenHarian,
                $employeeIds,
                $this->this_company_id,
                $yearMonth
            );
            if (count($dataPayrollGajiConjunction) != 0) {
                $this->payrollGajiModel->insertBatch($dataPayrollGajiConjunction);
            }

            //--------------------------------------
            // Perizinan Not Approved
            //---------------------------------------
            $gajiHarian = $this->payrollGajiModel->getGajiHarianGajiCadanganAmt($payrollIds);

            $mapGajiHarian = [];
            foreach ($gajiHarian['gajiHarian'] as $g) {
                $mapGajiHarian[$g['employee_id']] = $g['nominal'];
            }

            $mapGajiCadangan = [];
            foreach ($gajiHarian['gajiCadangan'] as $g) {
                $mapGajiCadangan[$g['employee_id']] = $g['nominal'];
            }

            $dataFormPerizinanNotApproved = $this->formPerizinanNotApprovedModel->generateAmt(
                $mapStatusAttendance,
                $mapEmployeePayroll,
                $mapGajiHarian,
                $mapGajiCadangan,
                $employeeIds,
                $this->this_company_id,
                $yearMonth,
                $startDate,
                $endDate
            );
            if (count($dataFormPerizinanNotApproved['dataFormPerizinan']) != 0) {
                $this->formPerizinanNotApprovedModel->insertBatch($dataFormPerizinanNotApproved['dataFormPerizinan']);
            }

            if (count($dataFormPerizinanNotApproved['dataFormPerizinanTotal']) != 0) {
                $this->payrollModel->updateBatch($dataFormPerizinanNotApproved['dataFormPerizinanTotal'], 'id');
            }


            //-----------------------------------------
            // Payroll Gaji Harian
            //-----------------------------------------
            $dataPayrollGajiHarian = $this->payrollGajiHarianModel->generateAmt(
                $mapEmployeePayroll,
                $mapGajiHarian,
                $mapGajiCadangan,
                $this->this_company_id,
                $employeeIds,
                $yearMonth,
                $startDate,
                $endDate
            );
            if (count($dataPayrollGajiHarian['rows']) != 0) {
                $this->payrollGajiHarianModel->insertBatch($dataPayrollGajiHarian['rows']);
            }
            $mapTotalGajiHarian = $dataPayrollGajiHarian['totals_per_payroll'];

            //--------------------------------------
            // Lembur
            //---------------------------------------
            $formLembur = $this->formLemburModel->getFormLemburAmt(
                $employeeIds,
                $startDate,
                $endDate
            );
            $mapFormLembur = [];
            foreach ($formLembur as $f) {
                $mapFormLembur[$f['employee_id']] = $f['total'];
            }

            //--------------------------------------
            // Pinjaman Karyawan
            //---------------------------------------
            $pinjamanKaryawan = $this->pinjamanKaryawanModel->getPinjamanKaryawanDiambilAmt(
                $employeeIds,
                $yearMonth
            );
            $mapPinjaman = [];
            foreach ($pinjamanKaryawan as $p) {
                $mapPinjaman[$p['employee_id']] = $p['nominal'];
            }

            //--------------------------------------
            // Generate Payroll Final
            //---------------------------------------
            $dataPayrollLast = $this->payrollModel->generateAmt(
                $mapFormLembur,
                $mapEmployeePayroll,
                $mapGajiHarian,
                $mapGajiCadangan,
                $mapPinjaman,
                $mapTotalGajiHarian,
                $employeeIds,
                $yearMonth,
                $payrollIds,
                $startDate,
                $endDate
            );

            if (count($dataPayrollLast) != 0) {
                $this->payrollModel->updateBatch($dataPayrollLast, 'id');
            }


            // Commit transaksi
            $db->transCommit();

            return response()->setJSON([
                'token'   => csrf_hash(),
                'message' => "Generate payroll sukses",
                'status'  => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();

            return response()->setJSON([
                'message' => $e->getMessage() . " at " . $e->getFile() . " in line " . $e->getLine(),
                'status'  => false,
                'token'   => csrf_hash()
            ]);
        }
    }

    public function generateGlobalPayrollRevamp()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $yearMonth       = $this->request->getVar('yearMonth');
            $startDate       = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
            $endDate         = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));
            $divisionGlobalID = $this->request->getVar('divisionGlobalID');

            // Cek absensi
            $dataAbsensiGeneratedQry = $this->attendanceModel->where('year_month', $yearMonth);
            $dataAbsensiGeneratedQry->where('company_id', $this->this_company_id);
            if ($divisionGlobalID != 'ALL') {
                $dataAbsensiGeneratedQry->where('division_id', $divisionGlobalID);
            }

            $totalAbsensi = $dataAbsensiGeneratedQry->countAllResults();

            if ($totalAbsensi == 0) {
                $db->transRollback();
                return response()->setJSON([
                    'message' => "Data absensi bulan " . $yearMonth . " tidak ada",
                    'status'  => false,
                    'token'   => csrf_hash()
                ]);
            }

            // Ambil employee + hapus payroll lama
            if ($divisionGlobalID == "ALL") {
                $employeeData = $this->employeeModel->getEmployees($this->this_company_id);

                $this->payrollModel->where('company_id', $this->this_company_id)
                    ->where('year_month', $yearMonth)
                    ->delete();
            } else {
                $employeeData = $this->employeeModel->getEmployeesByDivisionID(
                    $this->this_company_id,
                    $divisionGlobalID
                );

                $this->payrollModel->where('company_id', $this->this_company_id)
                    ->where('year_month', $yearMonth)
                    ->where('division_id', $divisionGlobalID)
                    ->delete();
            }

            $employeeIds = array_column($employeeData, 'id');
            if (count($employeeIds) == 0) {
                $db->transRollback();
                return response()->setJSON([
                    'message' => "Data karyawan tidak ditemukan",
                    'status'  => false,
                    'token'   => csrf_hash()
                ]);
            }

            // Total Karyawan Masuk di Hari Libur
            // $mapTotalEmployeeMasukLibur = $this->attendanceModel->getTotalHariLiburEmployeeHadir(
            //     $startDate,
            //     $endDate,
            //     $this->this_company_id,
            //     $employeeIds,
            // );

            $mapTotalMasukNotApprove = $this->attendanceModel->getTotalEmployeeHadirNotApproved(
                $startDate,
                $endDate,
                $employeeIds,
            );

            // Mapping divisi
            $divisiList = $this->employeeModel->getDivisiByEmployeeAmt($employeeIds);
            $mapDivisi  = [];
            foreach ($divisiList as $d) {
                $mapDivisi[$d['id']] = $d['division_id'];
            }

            // Status attendance
            $statusAttendance = $this->attendanceModel->getStatusAttendancesInRangeAmt(
                $startDate,
                $endDate,
                $employeeIds
            );

            $mapStatusAttendance = [];
            foreach ($statusAttendance as $s) {
                $empId = $s['employee_id'];
                if (!isset($mapStatusAttendance[$empId])) {
                    $mapStatusAttendance[$empId] = [
                        'CUTI TAHUNAN_CT'   => 0,
                        'CUTI HAID_CHD'     => 0,
                        'CUTI HAMIL_CHL'    => 0,
                        'CUTI MELAHIRKAN_CM' => 0,
                        'POTONG GAJI_PG'            => 0,
                        'SAKIT_S'           => 0,
                        'RL_RL'             => 0,
                        'HADIR_H'           => 0,
                        'LIBUR_L'           => 0,
                        'ALPHA_A'           => 0,
                        'DINAS_D'           => 0,
                        'CUTI KEGUGURAN_CKG' => 0
                    ];
                }
                $mapStatusAttendance[$empId][$s['status']] = $s['total'];
            }

            // Insert payroll batch awal
            $dataPayroll = [];
            foreach ($employeeIds as $e) {
                if (isset($mapStatusAttendance[$e])) {
                    //$mapStatusAttendance[$e]['HADIR_H'] = $mapStatusAttendance[$e]['HADIR_H'] - $mapTotalEmployeeMasukLibur[$e];
                    $mapStatusAttendance[$e]['HADIR_H'] = $mapStatusAttendance[$e]['HADIR_H'] - $mapTotalMasukNotApprove[$e];
                }
                $att = $mapStatusAttendance[$e] ?? [];

                $dataPayroll[] = [
                    "company_id"                   => $this->this_company_id,
                    "employee_id"                  => $e,
                    "division_id"                  => $mapDivisi[$e],
                    "year_month"                   => $yearMonth,
                    "cuti_tahunan"                 => $att["CUTI TAHUNAN_CT"] ?? 0,
                    "cuti_haid"                    => $att["CUTI HAID_CHD"] ?? 0,
                    "cuti_hamil"                   => $att["CUTI HAMIL_CHL"] ?? 0,
                    "cuti_melahirkan"              => $att["CUTI MELAHIRKAN_CM"] ?? 0,
                    "izin"                         => $att["POTONG GAJI_PG"] ?? 0,
                    "sakit"                        => $att["SAKIT_S"] ?? 0,
                    "rl"                           => $att["RL_RL"] ?? 0,
                    "hadir"                        => $att["HADIR_H"] ?? 0,
                    "libur"                        => $att["LIBUR_L"] ?? 0,
                    "alpha"                        => $att["ALPHA_A"] ?? 0,
                    "dinas"                        => $att["DINAS_D"] ?? 0,
                    "cuti_keguguran"               => $att["CUTI KEGUGURAN_CKG"] ?? 0,
                    "hadir_final"                  => 0,
                    "total_perizinan_not_approved" => 0,
                    "total_perizinan_approved"     => 0,
                    "nominal_cadangan"             => 0,
                    "nominal_gaji_harian"          => 0,
                    "nominal_pinjaman_karyawan"    => 0,
                    "nominal_uang_gaji"            => 0,
                    "nominal_uang_lembur"          => 0,
                    "nominal_pengurangan_gaji"     => 0,
                    "nominal_gaji_diterima"        => 0,
                    "nominal_penambahan_gaji"      => 0,
                    "start_date"                   => $startDate,
                    "end_date"                     => $endDate
                ];
            }

            $this->payrollModel->insertBatch($dataPayroll);

            // Ambil payroll id hasil insert
            $emmployeePayroll = $this->payrollModel
                ->select('id, employee_id')
                ->where('company_id', $this->this_company_id)
                ->where('year_month', $yearMonth)
                ->whereIn('employee_id', $employeeIds)
                ->findAll();

            $mapEmployeePayroll = [];
            $payrollIds = [];
            foreach ($emmployeePayroll as $e) {
                $mapEmployeePayroll[$e['employee_id']] = $e['id'];
                $payrollIds[] = $e['id'];
            }

            //--------------------------------------
            // Generate Keterlambatan
            //---------------------------------------
            $dataAttendanceKeterlambatan = $this->attendanceKeterlambatanModel->generateAmt(
                $this->this_company_id,
                $startDate,
                $endDate,
                $yearMonth,
                $employeeIds,
                $mapEmployeePayroll
            );
            if (count($dataAttendanceKeterlambatan) != 0) {
                $this->attendanceKeterlambatanModel->insertBatch($dataAttendanceKeterlambatan);
            }

            //--------------------------------------
            // Gaji Conjunction
            //---------------------------------------
            $uangMakanHarian = $this->uangMakanHarianModel->generateUangMakanAmt(
                $employeeIds,
                $startDate,
                $endDate
            );
            $mapUangMakanHarian = [];
            foreach ($uangMakanHarian as $u) {
                $mapUangMakanHarian[$u['employee_id']] = $u['total_nominal'];
            }

            $dendaAbsenHarian = $this->dendaAbsenHarianModel->generateDendaAmt(
                $employeeIds,
                $startDate,
                $endDate
            );
            $mapDendaAbsenHarian = [];
            foreach ($dendaAbsenHarian as $d) {
                $mapDendaAbsenHarian[$d['employee_id']] = $d['total_nominal'];
            }


            $dataPayrollGajiConjunction = $this->payrollGajiModel->generateAmt(
                $mapEmployeePayroll,
                $mapUangMakanHarian,
                $mapDendaAbsenHarian,
                $employeeIds,
                $this->this_company_id,
                $yearMonth
            );
            if (count($dataPayrollGajiConjunction) != 0) {
                $this->payrollGajiModel->insertBatch($dataPayrollGajiConjunction);
            }

            //--------------------------------------
            // Perizinan Not Approved
            //---------------------------------------
            $gajiHarian = $this->payrollGajiModel->getGajiHarianGajiCadanganAmt($payrollIds);

            $mapGajiHarian = [];
            foreach ($gajiHarian['gajiHarian'] as $g) {
                $mapGajiHarian[$g['employee_id']] = $g['nominal'];
            }

            $mapGajiCadangan = [];
            foreach ($gajiHarian['gajiCadangan'] as $g) {
                $mapGajiCadangan[$g['employee_id']] = $g['nominal'];
            }

            $dataFormPerizinanNotApproved = $this->formPerizinanNotApprovedModel->generateAmt(
                $mapStatusAttendance,
                $mapEmployeePayroll,
                $mapGajiHarian,
                $mapGajiCadangan,
                $employeeIds,
                $this->this_company_id,
                $yearMonth,
                $startDate,
                $endDate
            );

            if (count($dataFormPerizinanNotApproved['dataFormPerizinan']) != 0) {
                $this->formPerizinanNotApprovedModel->insertBatch($dataFormPerizinanNotApproved['dataFormPerizinan']);
            }

            if (count($dataFormPerizinanNotApproved['dataFormPerizinanTotal']) != 0) {

                $this->payrollModel->updateBatch($dataFormPerizinanNotApproved['dataFormPerizinanTotal'], 'id');
            }

            //-----------------------------------------
            // Payroll Gaji Harian
            //-----------------------------------------
            $dataPayrollGajiHarian = $this->payrollGajiHarianModel->generateAmt(
                $mapEmployeePayroll,
                $mapGajiHarian,
                $mapGajiCadangan,
                $this->this_company_id,
                $employeeIds,
                $yearMonth,
                $startDate,
                $endDate
            );
            if (count($dataPayrollGajiHarian['rows']) != 0) {
                $this->payrollGajiHarianModel->insertBatch($dataPayrollGajiHarian['rows']);
            }
            $mapTotalGajiHarian = $dataPayrollGajiHarian['totals_per_payroll'];

            //--------------------------------------
            // Lembur
            //---------------------------------------
            $formLembur = $this->formLemburModel->getFormLemburAmt(
                $employeeIds,
                $startDate,
                $endDate
            );
            $mapFormLembur = [];
            foreach ($formLembur as $f) {
                $mapFormLembur[$f['employee_id']] = $f['total'];
            }

            //--------------------------------------
            // Pinjaman Karyawan
            //---------------------------------------
            $pinjamanKaryawan = $this->pinjamanKaryawanModel->getPinjamanKaryawanDiambilAmt(
                $employeeIds,
                $yearMonth
            );
            $mapPinjaman = [];
            foreach ($pinjamanKaryawan as $p) {
                $mapPinjaman[$p['employee_id']] = $p['nominal'];
            }

            //--------------------------------------
            // Generate Payroll Final
            //---------------------------------------
            $dataPayrollLast = $this->payrollModel->generateAmt(
                $mapFormLembur,
                $mapEmployeePayroll,
                $mapGajiHarian,
                $mapGajiCadangan,
                $mapPinjaman,
                $mapTotalGajiHarian,
                $employeeIds,
                $yearMonth,
                $payrollIds,
                $startDate,
                $endDate
            );

            if (count($dataPayrollLast) != 0) {
                $this->payrollModel->updateBatch($dataPayrollLast, 'id');
            }

            // Commit transaksi
            $db->transCommit();

            return response()->setJSON([
                'token'   => csrf_hash(),
                'message' => "Generate payroll sukses",
                'status'  => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();

            return response()->setJSON([
                'message' => $e->getMessage() . " at " . $e->getFile() . " in line " . $e->getLine(),
                'status'  => false,
                'token'   => csrf_hash()
            ]);
        }
    }


    public function detailPayrollView($id)
    {
        $id = decrypt($id);

        $payroll = $this->payrollModel->where('id', $id)->first();

        // validation
        if ($payroll == null) {
            return redirect()->to('payroll');
        }

        $data = [
            'payrollDetail' => $this->payrollModel->detailPayroll($id),
            'perhitunganGaji' => $this->payrollGajiModel->getPerhitunganKomponenGajiPayroll($id),
            'totalPerhitunganGaji' => $this->payrollGajiModel->getTotalKomponenGajiPayroll($id),
            'rekapKeterlambatanPresensi' => $this->attendanceKeterlambatanModel->rekap($id),
            'totalNominalKeterlambatanPresensi' => $this->attendanceKeterlambatanModel->getTotalRekap($id),
            'rekapLembur' => $this->formLemburModel->rekapLemburDateRange($payroll['employee_id'], $payroll['start_date'], $payroll['end_date']),
            'rekapPerizinanNotApproved' => $this->rekapPerizinanNotApprovedModel->rekap($id),
            'totalNominalRekapPerizinanNotApproved' => $this->rekapPerizinanNotApprovedModel->getTotalRekap($id),
            'rekapPinjaman' => $this->pinjamanKaryawanModel->getPinjamanKaryawanDiambil($payroll['employee_id'], $payroll['year_month']),
            'rekapGajiHarian' => $this->payrollGajiHarianModel->getList($id)
        ];

        return view('hr/payroll/form', $data);
    }

    public function updateNominalKomponenGaji()
    {
        $id = $this->request->getVar('komponenGajiID');
        $payrollID = $this->request->getVar('payrollID');
        $nominal =  $this->request->getVar('nominal');

        $this->payrollGajiModel->update($id, [
            'nominal' => $nominal
        ]);

        $this->payrollModel->generateIfPayrollChanged($payrollID);

        return response()->setJSON([
            'message' => "Nominal komponen tunjangan berhasil diperbaruhi",
            'location' => "nilaiKomponenGaji",
            'id' => encrypt($payrollID)
        ]);
    }

    public function updateNominalKeterlambatanPresensi()
    {
        $id = $this->request->getVar('rekapKeterlambatanPresensiID');
        $payrollID = $this->request->getVar('payrollID');
        $nominal =  $this->request->getVar('nominal');

        $this->attendanceKeterlambatanModel->update($id, [
            'nominal_pengurangan' => $nominal,
        ]);

        $this->payrollModel->generateIfPayrollChanged($payrollID);

        return response()->setJSON([
            'message' => "Nominal pengurangan keterlambatan presensi berhasil diperbaruhi",
            'location' => "rekapKeterlambatanPresensi",
            'id' => encrypt($payrollID)
        ]);
    }

    public function updateNominalGajiPerHariAndCadangan()
    {
        $payrollID = $this->request->getVar('payrollID');
        $gajiPerHariID = $this->request->getVar('gajiPerHariID');
        $cadanganID = $this->request->getVar('cadanganID');
        $nominalGajiPerHari = $this->request->getVar('nominalGajiPerHari');
        $nominalCadangan = $this->request->getVar('nominalCadangan');

        $this->payrollGajiModel->update($gajiPerHariID, [
            'nominal' => $nominalGajiPerHari
        ]);

        $this->payrollGajiModel->update($cadanganID, [
            'nominal' => $nominalCadangan
        ]);

        $this->payrollModel->generateIfPayrollChanged($payrollID);

        return response()->setJSON([
            'message' => "Nominal Gaji Per Hari dan Nominal Cadangan Berhasil Diperbaruhi",
            'payrollDetail' => $this->payrollModel->detailPayroll($payrollID),
            'token' => csrf_hash(),
            'test' => $_POST
        ]);
    }

    public function getEmployeeByDivision()
    {
        return response()->setJSON([
            'data' => $this->employeeModel->where('deletedAt', null)
                ->where('division_id', $this->request->getVar('divisionID'))
                ->orderBy('name', "ASC")
                ->findAll(),
            'token' => \csrf_hash(),
        ]);
    }

    public function getEmployeeByBagian()
    {
        return response()->setJSON([
            'data' => $this->employeeModel->where('deletedAt', null)
                ->where('bagian_id', $this->request->getVar('bagianId'))
                ->orderBy('name', "ASC")
                ->findAll(),
            'token' => \csrf_hash(),
        ]);
    }


    public function exportPdfPayrollSingle($payrollID)
    {
        $dompdf = new Dompdf();

        if (is_numeric($payrollID)) {
            $payrollID = $payrollID;
        } else {
            $payrollID = decrypt($payrollID);
        }

        $payrollDetail = $this->payrollModel->where('id', $payrollID)->first();
        if ($payrollDetail == null) {
            return redirect()->to('payroll');
        }
        $employee = $this->employeeModel->getSingleEmployee($payrollDetail['employee_id']);
        $splitJamLembur = $this->formLemburModel->getTotalLemburJamPertamaKeduaByDateRange(
            $payrollDetail['employee_id'],
            $payrollDetail['start_date'],
            $payrollDetail['end_date']
        );
        $company =  $this->companyModel->where('id', $this->this_company_id)->first();
        $tunjanganGajiPokok = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_gaji_harian', 1)->where('deletedAt', null)->first();
        $tunjanganCadangan = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_cadangan', 1)->where('deletedAt', null)->first();
        $payrollDetail['total_gaji_harian_plus_cadangan'] = $payrollDetail['nominal_gaji_harian'] + $payrollDetail['nominal_cadangan'];

        $data = [
            'payroll' => $payrollDetail,
            'employee' => $employee,
            'year' => explode("-", $payrollDetail['year_month'])[0],
            'month' => explode("-", $payrollDetail['year_month'])[1],
            'rekapLembur' => $this->formLemburModel->rekapLemburDateRange($payrollDetail['employee_id'], $payrollDetail['start_date'], $payrollDetail['end_date']),
            'totalLemburJamPertama' => $splitJamLembur['jamPertama'],
            'totalLemburJamKedua' => $splitJamLembur['jamKedua'],
            'perhitunganGaji' => $this->payrollGajiModel->getPerhitunganKomponenGajiPayroll($payrollID),
            'company' => $company,
            'totalNominalKeterlambatanPresensi' => $this->attendanceKeterlambatanModel->getTotalRekap($payrollID),
            'totalNominalRekapPerizinanNotApproved' => $this->attendanceKeterlambatanModel->getTotalRekap($payrollID),
            'tunjanganGajiPokok' => $tunjanganGajiPokok,
            'tunjanganCadangan' => $tunjanganCadangan
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_single_print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($data['payroll']['year_month'] . " " . $data['employee']['name'], array("Attachment" => false));

        exit(0);
    }

    public function exportPdfPayrollDivision()
    {
        $dompdf = new Dompdf();
        $yearMonth = $this->request->getVar('month');
        $divisionID = $this->request->getVar('divisi_id');

        // set variable
        $divisi = $this->divisiModel->where('id', $divisionID)->first();
        $year = explode("-", $yearMonth)[0];
        $month = explode("-", $yearMonth)[1];
        $payrollData = $this->payrollModel->getListPrintPayrollByDivision($divisionID, $this->userID, $year, $month, $this->this_company_id);

        // get limit 1 untuk label periode
        $payrollLimit = $this->payrollModel->where('year_month', $yearMonth)->where('division_id', $divisionID)->first();

        $startDate = date('d/m/Y', strtotime($payrollLimit['start_date']));
        $endDate = date('d/m/Y', strtotime($payrollLimit['end_date']));

        // validation
        if ($payrollData == null) {
            return redirect()->to('payroll');
        }

        $data = [
            'year' => $year,
            'month' => $month,
            'divisi' => $divisi,
            'payrollData' => $payrollData,
            'komponenGaji' => $this->gajiDivisiModel->getGajiByDivision($divisionID, $this->this_company_id),
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_division_print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Daftar Upah Karyawan ", array("Attachment" => false));

        exit(0);
    }

    public function exportPdfPayrollDivisionDetail()
    {
        $dompdf = new Dompdf();
        $yearMonth = $this->request->getVar('month');
        $divisionID = $this->request->getVar('divisi_id');
        $bagianID = $this->request->getVar('bagian_id');
        $tunjanganGajiPokok = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_gaji_harian', 1)->where('deletedAt', null)->first();
        $tunjanganCadangan = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_cadangan', 1)->where('deletedAt', null)->first();

        $data = [
            'payrollData' => $this->payrollModel->getPayrollDetail(
                $yearMonth,
                $divisionID,
                $this->this_company_id,
                $bagianID
            ),
            'tunjanganGajiPokok' => $tunjanganGajiPokok,
            'tunjanganCadangan' => $tunjanganCadangan
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_detail_division', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Detail Payroll Berdasarkan Divisi ", array("Attachment" => false));

        exit(0);
    }

    public function exportPdfSummary()
    {
        $dompdf = new Dompdf();
        $yearMonth = $this->request->getVar('month');
        $divisionID = $this->request->getVar('divisi_id');

        $year = explode("-", $yearMonth)[0];
        $month = explode("-", $yearMonth)[1];

        // get limit 1 untuk label periode
        $payrollLimit = $this->payrollModel->where('year_month', $yearMonth)->where('division_id', $divisionID)->first();

        $startDate = date('d/m/Y', strtotime($payrollLimit['start_date']));
        $endDate = date('d/m/Y', strtotime($payrollLimit['end_date']));

        $data = [
            'year' => $year,
            'month' => $month,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $this->payrollModel->getSummaryPayroll($yearMonth, $this->this_company_id, $divisionID)
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_summary_print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Sumarry Jumlah Upah dan Jam Kerja ", array("Attachment" => false));

        exit(0);
    }

    public function exportPdfPotongan()
    {
        $dompdf = new Dompdf();
        $yearMonth = $this->request->getVar('month');
        $divisionID = $this->request->getVar('divisi_id');

        $year = explode("-", $yearMonth)[0];
        $month = explode("-", $yearMonth)[1];

        $data = [
            'year' => $year,
            'month' => $month,
            'payrollData' => $this->payrollModel->getPotonganPayroll($yearMonth, $this->this_company_id, $divisionID)
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_potongan_print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Daftar Potongan ", array("Attachment" => false));

        exit(0);
    }

    public function getBagian()
    {
        $divisi = $this->request->getVar('divisi');
        $data = $this->bagianModel
            ->select("*")
            ->where("division_id", $divisi)
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'data' => $data,
            'status' => true
        ]);
    }
}
