<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesLogModel;
use App\Models\BigDaysModel;
use App\Models\DivisisModel;
use App\Models\EmployeesModel;
use App\Models\FormPerijinanModel;
use App\Models\PinjamanKaryawanModel;
use Dompdf\Dompdf;
use Exception;

class PinjamanKaryawan extends BaseController
{
    protected $this_company_id, $userID;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userID = session()->get('login')->user_id;
    }

    public function pinjamanKaryawan()
    {
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getVar("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getVar("month");

        $divisiModel = new DivisisModel();
        $pinjamanKaryawanModel = new PinjamanKaryawanModel();

        $data = [
            'year' => $year,
            'month' => $month,
            'divisi' => $divisiModel->get_by_company_id($this->this_company_id),
            'pinjamanCheck' => $pinjamanKaryawanModel->where('month_year', $year . "-" . $month)->where('company_id', $this->this_company_id)->findAll()
        ];

        return view('hr/pinjamanKaryawan/index', $data);
    }

    public function generateAllPinjaman()
    {
        $yearMonth = $this->request->getVar('monthYear');
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
        $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));

        // init model
        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $formPerijinanModel = new FormPerijinanModel();
        $employeesModel = new EmployeesModel();
        $hariLiburModel = new BigDaysModel();
        $attendancesLogModel = new AttendancesLogModel();

        // date start end validation
        if (strtotime($startDate) > strtotime($endDate)) {
            $data = [
                "status" => false,
                "message" => "Tanggal mulai absen dan tanggal selesai absen tidak sesuai",
                'token' => csrf_hash()
            ];
            return response()->setJSON($data);
        }

        // check range $startDate dan $endDate harus <= 15 hari
        $startDateTimestamp = strtotime($startDate);
        $endDateTimestamp = strtotime($endDate);
        $selisihHari = ($endDateTimestamp - $startDateTimestamp) / (60 * 60 * 24);

        if ($selisihHari > 15) {
            // pinjaman yang diberikan harus dibawah 15 hari kerja
            $data = [
                "status" => false,
                "message" => "Periode tidak boleh melebihi 15 hari kerja. karena pinjaman itu diberikan maksimal 15 hari kerja sesuai dengan periode pinjaman (Total hari kerja berdasarkan range yang anda masukkan sebanyak $selisihHari hari)",
                'token' => csrf_hash()
            ];
            return response()->setJSON($data);
        }

        // remove all if exist and insert again
        $pinjamanKaryawanModel
            ->where('company_id', $this->this_company_id)
            ->where('month_year', $yearMonth)
            ->delete();

        // init date untuk menyimpan range hari
        $dateList = [];
        while ($startDateTimestamp <= $endDateTimestamp) {
            $currentDate = date('Y-m-d', $startDateTimestamp);
            $dateList[] = $currentDate;
            $startDateTimestamp += 86400;
        }

        $employeeData = $employeesModel->getEmployees($this->this_company_id);

        foreach ($employeeData as $e) {
            $hadir = 0;
            $tidakHadir = 0;
            foreach ($dateList as $dates) {
                $formPerizinan = $formPerijinanModel->where('periode', $dates)
                    ->where('employee_id', $e['id'])
                    ->first();

                $hariLibur = $hariLiburModel->where('date', $dates)->first();
                $selectQry = "DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
                    DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout";

                $logAttandance = $attendancesLogModel
                    ->select($selectQry)
                    ->where('employees_id', $e['id'])
                    ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $dates)
                    ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
                    ->limit(2)
                    ->get()
                    ->getResult();

                if ($hariLibur != null || date('l', strtotime($dates)) == "Sunday" && $formPerizinan == null && \count($logAttandance) == 0) {
                    // ada hari libur
                    $tidakHadir++;
                } elseif ($formPerizinan != null) {
                    // ada perizinan 
                    $tidakHadir++;
                } elseif ($formPerizinan == null) {
                    if (count($logAttandance) == 0) {
                        // alpha
                        $tidakHadir++;
                    } else {
                        // data absen ada di log
                        $hadir++;
                    }
                }
            }
            $pinjamanKaryawanModel->insert([
                'company_id' => $this->this_company_id,
                'employee_id' => $e['id'],
                'division_id' => $e['division_id'],
                'month_year' => $yearMonth,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tidak_hadir' => $tidakHadir,
                'hadir' => $hadir,
                'is_boleh_minjam' => ($tidakHadir <= 6) ? '1' : '0',
                'status_pinjaman' => ($tidakHadir <= 6) ? '0' : null,
                'nominal' => ($tidakHadir <= 6) ? 100000 : null // default 100k
            ]);
        }

        return \response()->setJSON([
            'status' => true,
            'message' => 'Generate Data Karyawan yang Berhak Meminjam Berhasil',
            'token' => csrf_hash()
        ]);
    }

    public function generateSinglePinjaman()
    {
        $yearMonth = $this->request->getVar('monthYear');
        $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
        $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));
        $employeeID = $this->request->getVar('employeeID');
        $id = $this->request->getVar('id');

        // init model
        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $formPerijinanModel = new FormPerijinanModel();
        $employeesModel = new EmployeesModel();
        $hariLiburModel = new BigDaysModel();
        $attendancesLogModel = new AttendancesLogModel();

        // date start end validation
        if (strtotime($startDate) > strtotime($endDate)) {
            $data = [
                "status" => false,
                "message" => "Tanggal mulai absen dan tanggal selesai absen tidak sesuai",
                'token' => csrf_hash()
            ];
            return response()->setJSON($data);
        }

        // check range $startDate dan $endDate harus <= 15 hari
        $startDateTimestamp = strtotime($startDate);
        $endDateTimestamp = strtotime($endDate);
        $selisihHari = ($endDateTimestamp - $startDateTimestamp) / (60 * 60 * 24);

        if ($selisihHari > 15) {
            // pinjaman yang diberikan harus dibawah 15 hari kerja
            $data = [
                "status" => false,
                "message" => "Periode tidak boleh melebihi 15 hari kerja. karena pinjaman itu diberikan maksimal 15 hari kerja sesuai dengan periode pinjaman (Total hari kerja berdasarkan range yang anda masukkan sebanyak $selisihHari hari)",
                'token' => csrf_hash()
            ];
            return response()->setJSON($data);
        }

        // remove all if exist and insert again
        $pinjamanKaryawanModel->delete($id);

        // init date untuk menyimpan range hari
        $dateList = [];
        while ($startDateTimestamp <= $endDateTimestamp) {
            $currentDate = date('Y-m-d', $startDateTimestamp);
            $dateList[] = $currentDate;
            $startDateTimestamp += 86400;
        }

        $employeeData = $employeesModel->where('id', $employeeID)->findAll();

        foreach ($employeeData as $e) {
            $hadir = 0;
            $tidakHadir = 0;
            foreach ($dateList as $dates) {
                $formPerizinan = $formPerijinanModel->where('periode', $dates)
                    ->where('employee_id', $e['id'])
                    ->first();

                $hariLibur = $hariLiburModel->where('date', $dates)->first();
                $selectQry = "DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
                    DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout";

                $logAttandance = $attendancesLogModel
                    ->select($selectQry)
                    ->where('employees_id', $e['id'])
                    ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $dates)
                    ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
                    ->limit(2)
                    ->get()
                    ->getResult();

                if ($hariLibur != null || date('l', strtotime($dates)) == "Sunday" && $formPerizinan == null && \count($logAttandance) == 0) {
                    // ada hari libur
                    $tidakHadir++;
                } elseif ($formPerizinan != null) {
                    // ada perizinan 
                    $tidakHadir++;
                } elseif ($formPerizinan == null) {
                    if (count($logAttandance) == 0) {
                        // alpha
                        $tidakHadir++;
                    } else {
                        // data absen ada di log
                        $hadir++;
                    }
                }
            }
            $pinjamanKaryawanModel->insert([
                'company_id' => $this->this_company_id,
                'employee_id' => $e['id'],
                'division_id' => $e['division_id'],
                'month_year' => $yearMonth,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'tidak_hadir' => $tidakHadir,
                'hadir' => $hadir,
                'is_boleh_minjam' => ($tidakHadir <= 6) ? '1' : '0',
                'status_pinjaman' => ($tidakHadir <= 6) ? '0' : null,
                'nominal' => ($tidakHadir <= 6) ? 100000 : null // default 100k
            ]);
        }

        return \response()->setJSON([
            'status' => true,
            'message' => 'Generate Single Data Karyawan yang Berhak Meminjam Berhasil',
            'token' => csrf_hash()
        ]);
    }

    public function updateNominalPinjaman()
    {
        $id = $this->request->getVar('id');

        $angka = preg_replace("/[^0-9,]/", "", $this->request->getVar('nominal'));
        $angka = str_replace(",", ".", $angka);
        $angkaDesimal = number_format((float) $angka, 3, '.', '');

        $pinjamanKaryawanModel = new PinjamanKaryawanModel();

        $pinjamanKaryawanModel->where('id', $id)->update($id, [
            'nominal' => $angkaDesimal
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Nominal pinjaman berhasil diperbaruhi"
        ]);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "divisi_id"     => $this->request->getGet("divisi_id"),
            "employee_id"   => $this->request->getGet("employee_id"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        // Bulan, Tahun
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');

        $condition = [
            'employees.company_id' => $this->this_company_id,
            "employees.deletedAt" => null,
            "employees.company_id" => $this->this_company_id,
            "employees.id != " => $this->userID,
            "pinjaman_karyawan.month_year" => $year . "-" . $month,
        ];

        $addCondition = [
            "divisi_id"          => $this->request->getGet("divisi_id"),
            "employee_id"        => $this->request->getGet("employee_id"),
        ];

        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $pinjamanKaryawanData = $pinjamanKaryawanModel->getList($condition, $addCondition, $limit, $offset);
        $dataPinjaman = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($pinjamanKaryawanData['data'] as $p) {
            array_push($dataPinjaman, [
                "no" => $no++,
                "id" => $p->id,
                "nip" => $p->nip,
                "name"  => $p->employeeName,
                "divisi" => $p->divisi,
                "mulaiAbsen" => date('d/m/Y', strtotime($p->start_date)),
                "selesaiAbsen" => date('d/m/Y', strtotime($p->end_date)),
                "hadir" => $p->hadir . " Kali",
                "tidakHadir" => $p->tidak_hadir . " Kali",
                "statusPinjaman" => $p->status_pinjaman,
                "isBolehMinjam" => $p->is_boleh_minjam,
                "nominalPinjaman" => "Rp " . number_format($p->nominal, 2, ',', '.'),
                // helper
                "monthYear" => $p->month_year,
                "employeeID" => $p->employee_id,
                "tipeGol" => $p->tipe == null ? "-" : $p->tipe,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $pinjamanKaryawanData['totalData'],
            "recordsFiltered"   => $pinjamanKaryawanData['totalFilteredData'],
            "data"              => $dataPinjaman,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }

    public function changeStatusPinjaman()
    {
        try {
            $id = $this->request->getVar('pinjamanID');
            $statusPinjaman = $this->request->getVar('statusPinjaman');

            $pinjamanKaryawanModel = new PinjamanKaryawanModel();

            foreach (explode(',', $id) as $rId) {
                $pinjamanKaryawanModel->update($rId, [
                    'status_pinjaman' => $statusPinjaman
                ]);
            }

            return response()->setJSON([
                'token'  => csrf_hash(),
                'status' => true,
                'message' => "Status pinjaman karyawan berhasil diperbaruhi"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token'  => csrf_hash(),
                'status' => false,
                'message' => "Error in " . $e->getMessage()
            ]);
        }
    }

    public function exportPDF($yearMonth, $divisionID)
    {
        $divisiModel = new DivisisModel();
        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $selectQry = "pinjaman_karyawan.*,employees.name";

        $data = [
            'divisi' => $divisiModel->where('id', $divisionID)->first(),
            'pinjaman' => $pinjamanKaryawanModel->select($selectQry)
                ->join('employees', 'employees.id = pinjaman_karyawan.employee_id', 'left')
                ->where('pinjaman_karyawan.month_year', $yearMonth)
                ->where('pinjaman_karyawan.division_id', $divisionID)
                ->where('pinjaman_karyawan.status_pinjaman', '1')
                ->where('pinjaman_karyawan.is_boleh_minjam', '1')
                ->findAll(),
            'yearMonth' => $yearMonth
        ];

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('hr/pinjamanKaryawan/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Daftar Potongan Pinjaman Divisi " . $data['divisi']['divisi'], array("Attachment" => false));
    }
}
