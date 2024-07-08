<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendanceKeterlambatanModel;
use App\Models\AttendancesModel;
use App\Models\BagianModel;
use App\Models\CompaniesModel;
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
use Dompdf\Dompdf;

class Payroll extends BaseController
{
    protected $token;
    protected $this_company_id, $userID;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userID = session()->get('login')->user_id;
    }

    public function payroll()
    {
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        $payrollModel = new PayrollsModel();
        $divisiModel = new DivisisModel();
        $golonganModel = new GolonganModel();
        $bagianModel = new BagianModel();

        $startDate = date('d/m/Y', strtotime("{$year}-{$month}-01 -1 month +22 days"));
        $endDate = date('d/m/Y', strtotime("{$year}-{$month}-01  +20 days"));

        $isGenerate = $payrollModel->where('company_id', $this->this_company_id)
            ->where('year_month', $year . "-" . $month)
            ->countAllResults();

        $data = [
            'year' => $year,
            'month' => $month,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'divisi' => $divisiModel->get_by_company_id($this->this_company_id),
            'bagian' => $bagianModel->get_by_company_id($this->this_company_id),
            'isGenerate' => ($isGenerate == 0) ? false : true,
            'golongan' => $golonganModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
        ];

        return view('hr/payroll/index', $data);
    }

    public function getAllPayRoll()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "divisi_id"          => $this->request->getGet("divisi_id"),
            "bagian_id"     => $this->request->getGet("bagian_id"),
            "employee_id"        => $this->request->getGet("employee_id"),
        ];
        // Bulan, Tahun
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');

        $condition = [
            'employees.company_id' => $this->this_company_id,
            "employees.deletedAt" => null,
            "employees.company_id" => $this->this_company_id,
            "employees.id != " => $this->userID, // kecualikan admin yg akses
            "year_month" => $year . "-" . $month,
        ];

        $addCondition = [
            "divisi_id"          => $this->request->getGet("divisi_id"),
            "bagian_id"          => $this->request->getGet("bagian_id"),
            "employee_id"        => $this->request->getGet("employee_id"),
            "tipe"               => $this->request->getGet("golongan")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $payrollModel = new PayrollsModel();

        $payrollData = $payrollModel->getList($condition, $addCondition, $limit, $offset);
        $dataPayRolls = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $bagianModel = new BagianModel();

        foreach ($payrollData['data'] as $p) {
            $bagian = $bagianModel->where('id', $p->bagianID)->first();
            array_push($dataPayRolls, [
                "no" => $no++,
                "id" => encrypt($p->id),
                "employee_id" => $p->employee_id,
                "nip" => $p->employeesNIP,
                "namaBagian" => ($bagian == null) ? "-" : $bagian['nama_bagian'],
                "name"  => $p->employeesName,
                "divisi" => $p->divisiName,
                "hariKerja" => $p->hadir_final . " Hari",
                "startDate" => date('d/m/Y', strtotime($p->start_date)),
                "endDate" => date('d/m/Y', strtotime($p->end_date)),
                "upahBersih" => "Rp " . number_format($p->nominal_uang_gaji, 2, ',', '.'),
                "totalGajiLembur" => "Rp " . number_format($p->nominal_uang_gaji + $p->nominal_uang_lembur, 2, ',', '.'),
                "totalPenguranganGaji" => "Rp " . number_format($p->nominal_pengurangan_gaji, 2, ',', '.'),
                "sisaGaji" => "Rp " . number_format($p->nominal_gaji_diterima, 2, ',', '.'),
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

        echo json_encode($data);
        return;
    }

    public function generateGlobalPayroll()
    {
        $yearMonth = $this->request->getVar('yearMonth');
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
        $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));
        $divisionGlobalID = $this->request->getVar('divisionGlobalID');

        $attendanceModel = new AttendancesModel();
        $payrollModel = new PayrollsModel();
        $employeesModel = new EmployeesModel();
        $FormPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $AttendanceKeterlambatanModel = new AttendanceKeterlambatanModel();
        $PayrollGajiModel = new PayrollGajiConjunctionModel();

        if ($divisionGlobalID == 'ALL') {
            $dataAbsensiGenerated = $attendanceModel
                ->where('year_month', $yearMonth)
                ->where('company_id', $this->this_company_id)
                ->countAllResults();
        } else {
            $dataAbsensiGenerated = $attendanceModel
                ->where('year_month', $yearMonth)
                ->where('division_id', $divisionGlobalID)
                ->where('company_id', $this->this_company_id)
                ->countAllResults();
        }

        if ($dataAbsensiGenerated != 0) {

            // get employees
            if ($divisionGlobalID == 'ALL') {
                $employeesData = $employeesModel->getEmployees($this->this_company_id);
                // delete firts if ada
                $payrollModel->where('company_id', $this->this_company_id)
                    ->where('year_month', $yearMonth)
                    ->delete();
            } else {
                $employeesData = $employeesModel->getEmployeesByDivisionID($this->this_company_id, $this->request->getVar('divisionGlobalID'));
                // delete firts if ada
                $payrollModel->where('company_id', $this->this_company_id)
                    ->where('year_month', $yearMonth)
                    ->where('division_id', $divisionGlobalID)
                    ->delete();
            }
            $test = [];
            // Insert Again
            foreach ($employeesData as $e) {
                $status = $attendanceModel->getStatusAttendancesInRange($startDate, $endDate, $e['id']);

                // payroll insert
                $payrollID = $payrollModel->insert([
                    "company_id" => $e['company_id'],
                    "employee_id" => $e['id'],
                    "division_id" => $e['division_id'],
                    "year_month" => $yearMonth,
                    "cuti_tahunan" => $status['CUTI TAHUNAN_CT'],
                    "cuti_haid" => $status['CUTI HAID_CHD'],
                    "cuti_hamil" => $status['CUTI HAMIL_CHL'],
                    "cuti_melahirkan" => $status['CUTI MELAHIRKAN_CM'],
                    "izin" => $status['IJIN_I'],
                    "sakit" => $status['SAKIT_S'],
                    "rl" => $status['RL_RL'],
                    "hadir" => $status['HADIR_H'],
                    "libur" => $status['LIBUR_L'],
                    "alpha" => $status['ALPHA_A'],
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
                $AttendanceKeterlambatanModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $yearMonth,
                    $startDate,
                    $endDate
                );

                // generate payroll gaji 
                $PayrollGajiModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $yearMonth
                );

                // generate form perijinan not approved
                $res = $FormPerizinanNotApprovedModel->generate(
                    $payrollID,
                    $e['id'],
                    $this->this_company_id,
                    $yearMonth,
                    $startDate,
                    $endDate
                );

                // update payroll
                $payrollFinal = $payrollModel->generate(
                    $e['id'],
                    $yearMonth,
                    $payrollID,
                    $startDate,
                    $endDate
                );

                $payrollModel
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

            return \response()->setJSON([
                'message' => "Data Payroll global berhasil digenerate ",
                'status' => true,
                'test' => $test
            ]);
        } else {
            return \response()->setJSON([
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

        $attendanceModel = new AttendancesModel();
        $payrollModel = new PayrollsModel();
        $employeesModel = new EmployeesModel();
        $FormPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $AttendanceKeterlambatanModel = new AttendanceKeterlambatanModel();
        $PayrollGajiModel = new PayrollGajiConjunctionModel();

        // delete first
        $payrollModel->where('company_id', $this->this_company_id)
            ->where('employee_id', $employeeID)
            ->delete();

        // get employee
        $employeesData = $employeesModel->where('id', $employeeID)->first();

        if ($employeesData['status'] != "Aktif") {
            return $this->response->setJSON([
                'message' => "Status karyawan " . $employeesData['name'] . " adalah " . $employeesData['status'],
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $status = $attendanceModel->getStatusAttendancesInRange($startDate, $endDate, $employeesData['id']);
        // payroll insert
        $payrollID = $payrollModel->insert([
            "company_id" => $employeesData['company_id'],
            "employee_id" => $employeesData['id'],
            "division_id" => $employeesData['division_id'],
            "year_month" => $yearMonth,
            "cuti_tahunan" => $status['CUTI TAHUNAN_CT'],
            "cuti_haid" => $status['CUTI HAID_CHD'],
            "cuti_hamil" => $status['CUTI HAMIL_CHL'],
            "cuti_melahirkan" => $status['CUTI MELAHIRKAN_CM'],
            "izin" => $status['IJIN_I'],
            "sakit" => $status['SAKIT_S'],
            "rl" => $status['RL_RL'],
            "hadir" => $status['HADIR_H'],
            "libur" => $status['LIBUR_L'],
            "alpha" => $status['ALPHA_A'],
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
        $AttendanceKeterlambatanModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $yearMonth,
            $startDate,
            $endDate
        );

        // generate payroll gaji 
        $PayrollGajiModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $yearMonth
        );

        // generate form perijinan not approved
        $res = $FormPerizinanNotApprovedModel->generate(
            $payrollID,
            $employeesData['id'],
            $this->this_company_id,
            $yearMonth,
            $startDate,
            $endDate
        );

        // update payroll
        $payrollFinal = $payrollModel->generate(
            $employeesData['id'],
            $yearMonth,
            $payrollID,
            $startDate,
            $endDate
        );

        $payrollModel
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


        return \response()->setJSON([
            'message' => "Data Payroll atas nama " . $employeesData['name'] . " berhasil digenerate",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function detailPayrollView($id)
    {
        $id = decrypt($id);

        $payrollModel = new PayrollsModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();
        $formLemburModel = new FormLemburModel();
        $rekapPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();
        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $payrollGajiHarian = new PayrollGajiHarianModel();

        $payroll = $payrollModel->where('id', $id)->first();

        // validation
        if ($payroll == null) {
            return redirect()->to('payroll');
        }

        $data = [
            'payrollDetail' => $payrollModel->detailPayroll($id),
            'perhitunganGaji' => $payrollGajiModel->getPerhitunganKomponenGajiPayroll($id),
            'totalPerhitunganGaji' => $payrollGajiModel->getTotalKomponenGajiPayroll($id),
            'rekapKeterlambatanPresensi' => $attendanceTerlambatModel->rekap($id),
            'totalNominalKeterlambatanPresensi' => $attendanceTerlambatModel->getTotalRekap($id),
            'rekapLembur' => $formLemburModel->rekap($payroll['employee_id'], $payroll['year_month']),
            'rekapPerizinanNotApproved' => $rekapPerizinanNotApprovedModel->rekap($id),
            'totalNominalRekapPerizinanNotApproved' => $rekapPerizinanNotApprovedModel->getTotalRekap($id),
            'rekapPinjaman' => $pinjamanKaryawanModel->getPinjamanKaryawanDiambil($payroll['employee_id'], $payroll['year_month']),
            'rekapGajiHarian' => $payrollGajiHarian->getList($id)
        ];

        return view('hr/payroll/form', $data);
    }

    public function updateNominalKomponenGaji()
    {
        $id = $this->request->getVar('komponenGajiID');
        $payrollID = $this->request->getVar('payrollID');

        $angka = preg_replace("/[^0-9,]/", "", $this->request->getVar('nominal'));
        $angka = str_replace(",", ".", $angka);
        $angkaDesimal = number_format((float) $angka, 3, '.', '');

        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $payrollModel = new PayrollsModel();

        $payrollGajiModel->update($id, [
            'nominal' => $angkaDesimal
        ]);

        $payrollModel->generateIfPayrollChanged($payrollID);

        return \response()->setJSON([
            'message' => "Nominal komponen tunjangan berhasil diperbaruhi",
            'location' => "nilaiKomponenGaji",
            'id' => $payrollID
        ]);
    }

    public function updateNominalKeterlambatanPresensi()
    {
        $id = $this->request->getVar('rekapKeterlambatanPresensiID');
        $payrollID = $this->request->getVar('payrollID');

        $angka = preg_replace("/[^0-9,]/", "", $this->request->getVar('nominal'));
        $angka = str_replace(",", ".", $angka);
        $angkaDesimal = number_format((float) $angka, 3, '.', '');

        $payrollModel = new PayrollsModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();

        $attendanceTerlambatModel->update($id, [
            'nominal_pengurangan' => $angkaDesimal,
        ]);

        $payrollModel->generateIfPayrollChanged($payrollID);

        return \response()->setJSON([
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

        $nominalGajiPerHari = preg_replace("/[^0-9,]/", "", $this->request->getVar('nominalGajiPerHari'));
        $nominalGajiPerHari = str_replace(",", ".", $nominalGajiPerHari);
        $angkaDesimalGajiPerHari = number_format((float) $nominalGajiPerHari, 3, '.', '');

        $nominalCadangan = preg_replace("/[^0-9,]/", "", $this->request->getVar('nominalCadangan'));
        $nominalCadangan = str_replace(",", ".", $nominalCadangan);
        $angkaDesimalCadangan = number_format((float) $nominalCadangan, 3, '.', '');

        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $payrollModel = new PayrollsModel();

        $payrollGajiModel->update($gajiPerHariID, [
            'nominal' => $angkaDesimalGajiPerHari
        ]);

        $payrollGajiModel->update($cadanganID, [
            'nominal' => $angkaDesimalCadangan
        ]);

        $payrollModel->generateIfPayrollChanged($payrollID);

        return \response()->setJSON([
            'message' => "Nominal Gaji Per Hari dan Nominal Cadangan Berhasil Diperbaruhi",
            'payrollDetail' => $payrollModel->detailPayroll($payrollID),
            'token' => \csrf_hash(),
            'test' => $_POST
        ]);
    }

    public function getEmployeeByDivision()
    {
        $EmployeesModel = new EmployeesModel();
        return \response()->setJSON([
            'data' => $EmployeesModel->where('deletedAt', null)
                ->where('division_id', $this->request->getVar('divisionID'))
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

        // set model
        $payrollModel = new PayrollsModel();
        $employeeModel = new EmployeesModel();
        $payrollGajiModel = new PayrollGajiConjunctionModel();
        $formLemburModel = new FormLemburModel();
        $companyModel = new CompaniesModel();
        $attendanceTerlambatModel = new AttendanceKeterlambatanModel();
        $rekapPerizinanNotApprovedModel = new FormPerizinanNotApprovedModel();

        // set payroll detail
        $payrollDetail = $payrollModel->where('id', $payrollID)->first();
        // validation
        if ($payrollDetail == null) {
            return redirect()->to('payroll');
        }
        // set variable
        $employee = $employeeModel->getSingleEmployee($payrollDetail['employee_id']);
        $splitJamLembur = $formLemburModel->getTotalLemburJamPertamaKedua($payrollDetail['employee_id'], $payrollDetail['year_month']);
        $company =  $companyModel->where('id', $this->this_company_id)->first();

        $data = [
            'payroll' => $payrollDetail,
            'employee' => $employee,
            'year' => explode("-", $payrollDetail['year_month'])[0],
            'month' => explode("-", $payrollDetail['year_month'])[1],
            'rekapLembur' => $formLemburModel->rekap($payrollDetail['employee_id'], $payrollDetail['year_month']),
            'totalLemburJamPertama' => $splitJamLembur['jamPertama'],
            'totalLemburJamKedua' => $splitJamLembur['jamKedua'],
            'perhitunganGaji' => $payrollGajiModel->getPerhitunganKomponenGajiPayroll($payrollID),
            'company' => $company,
            'totalNominalKeterlambatanPresensi' => $attendanceTerlambatModel->getTotalRekap($payrollID),
            'totalNominalRekapPerizinanNotApproved' => $rekapPerizinanNotApprovedModel->getTotalRekap($payrollID)
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_single_print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Tanda Terima Upah Karyawan ", array("Attachment" => false));

        exit(0);
    }

    public function exportPdfPayrollDivision($yearMonth, $divisionID)
    {
        $dompdf = new Dompdf();

        // set model
        $divisiModel = new DivisisModel();
        $payrollModel = new PayrollsModel();
        $gajiDivisi = new GajiDivisiModel();

        // set variable
        $divisi = $divisiModel->where('id', $divisionID)->first();
        $year = explode("-", $yearMonth)[0];
        $month = explode("-", $yearMonth)[1];
        $payrollData = $payrollModel->getListPrintPayrollByDivision($divisionID, $this->userID, $year, $month, $this->this_company_id);

        // get limit 1 untuk label periode
        $payrollLimit = $payrollModel->where('year_month', $yearMonth)->where('division_id', $divisionID)->first();

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
            'komponenGaji' => $gajiDivisi->getGajiByDivision($divisionID, $this->this_company_id),
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_division_print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Daftar Upah Karyawan ", array("Attachment" => false));

        exit(0);
    }

    public function exportPdfPayrollDivisionDetail($yearMonth, $divisionID)
    {
        $dompdf = new Dompdf();
        $payrollModel = new PayrollsModel();

        $data = [
            'payrollData' => $payrollModel->getPayrollDetail($yearMonth, $divisionID, $this->this_company_id)
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_detail_division', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Detail Payroll Berdasarkan Divisi ", array("Attachment" => false));

        exit(0);
    }

    public function exportPdfSummary($yearMonth, $divisionID)
    {
        $dompdf = new Dompdf();

        $payrollModel = new PayrollsModel();

        $year = explode("-", $yearMonth)[0];
        $month = explode("-", $yearMonth)[1];

        // get limit 1 untuk label periode
        $payrollLimit = $payrollModel->where('year_month', $yearMonth)->where('division_id', $divisionID)->first();

        $startDate = date('d/m/Y', strtotime($payrollLimit['start_date']));
        $endDate = date('d/m/Y', strtotime($payrollLimit['end_date']));

        $data = [
            'year' => $year,
            'month' => $month,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $payrollModel->getSummaryPayroll($yearMonth, $this->this_company_id, $divisionID)
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_summary_print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Sumarry Jumlah Upah dan Jam Kerja ", array("Attachment" => false));

        exit(0);
    }

    public function exportPdfPotongan($yearMonth, $divisionID)
    {
        $dompdf = new Dompdf();
        $payrollModel = new PayrollsModel();

        $year = explode("-", $yearMonth)[0];
        $month = explode("-", $yearMonth)[1];

        $data = [
            'year' => $year,
            'month' => $month,
            'payrollData' => $payrollModel->getPotonganPayroll($yearMonth, $this->this_company_id, $divisionID)
        ];

        $dompdf->loadHtml(view('hr/payroll/payroll_potongan_print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Daftar Potongan ", array("Attachment" => false));

        exit(0);
    }
}
