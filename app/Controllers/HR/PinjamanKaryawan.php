<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesLogModel;
use App\Models\AttendancesModel;
use App\Models\BagianModel;
use App\Models\BigDaysModel;
use App\Models\DivisisModel;
use App\Models\EmployeesModel;
use App\Models\FormPerijinanModel;
use App\Models\GolonganModel;
use App\Models\MetadataModel;
use App\Models\PinjamanKaryawanModel;
use Dompdf\Dompdf;
use Exception;

use function PHPUnit\Framework\returnSelf;

class PinjamanKaryawan extends BaseController
{
    protected $this_company_id, $userID;
    protected $divisiModel;
    protected $pinjamanKaryawanModel;
    protected $golonganModel;
    protected $metadataModel;
    protected $formPerijinanModel;
    protected $employeeModel;
    protected $bigDaysModel;
    protected $attendancesLogModel;
    protected $bagianModel;
    protected $attendancesModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userID = session()->get('login')->user_id;
        $this->divisiModel = new DivisisModel();
        $this->pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $this->golonganModel = new GolonganModel();
        $this->metadataModel = new MetadataModel();
        $this->formPerijinanModel = new FormPerijinanModel();
        $this->employeeModel = new EmployeesModel();
        $this->bigDaysModel = new BigDaysModel();
        $this->attendancesLogModel = new AttendancesLogModel();
        $this->bagianModel = new BagianModel();
        $this->attendancesModel = new AttendancesModel();
    }

    public function index()
    {
        $data = [
            'divisi' => $this->divisiModel->get_by_company_id($this->this_company_id),
            'golongan' => $this->golonganModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll(),
        ];

        return view('hr/pinjamanKaryawan/index', $data);
    }

    public function generateAllPinjaman()
    {
        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            $divisiId = $this->request->getVar('divisiId_generate');
            $bagianId = $this->request->getVar('bagianId_generate');
            $tipe = $this->request->getVar('golongan_generate');
            $yearMonth  = $this->request->getVar('monthYear');
            $startDate  = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
            $endDate    = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));
            $tanggalAmbil    = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggalAmbil_Global'))));

            $addCondition = [
                'divisi_id' => $divisiId,
                'bagian_id' => $bagianId,
                'tipe' => $tipe,
                'status' => "Aktif"
            ];

            $addConditionAll = [
                'divisi_id' => $divisiId,
                'bagian_id' => $bagianId,
                'tipe' => $tipe,
            ];

            // Validasi tanggal
            if (strtotime($startDate) > strtotime($endDate)) {
                return response()->setJSON([
                    "status" => false,
                    "message" => "Tanggal mulai absen dan tanggal selesai absen tidak sesuai",
                    'token' => csrf_hash()
                ]);
            }

            $selisihHari = (strtotime($endDate) - strtotime($startDate)) / 86400;
            if ($selisihHari > 15) {
                return response()->setJSON([
                    "status" => false,
                    "message" => "Periode tidak boleh melebihi 15 hari kerja. (Range: $selisihHari hari)",
                    'token' => csrf_hash()
                ]);
            }

            // list tanggal
            $dateList = [];
            $tmp = strtotime($startDate);
            while ($tmp <= strtotime($endDate)) {
                $dateList[] = date('Y-m-d', $tmp);
                $tmp += 86400;
            }

            // preload semua employee
            $employeeData = $this->employeeModel->getEmployeesPinjaman(
                $addCondition,
                $this->this_company_id
            );

            $employeeDataAll = $this->employeeModel->getEmployeesPinjaman(
                $addConditionAll,
                $this->this_company_id
            );

            $employeeIds  = array_column($employeeData, 'id');
            $employeeIdsAll = array_column($employeeDataAll, 'id');

            if (count($employeeIds) == 0) {
                return response()->setJSON([
                    "status" => false,
                    "message" => "Data karyawan tidak ditemukan",
                    'token' => csrf_hash()
                ]);
            }

            // hapus data lama
            $this->pinjamanKaryawanModel
                ->where('company_id', $this->this_company_id)
                ->where('month_year', $yearMonth)
                ->whereIn('employee_id', $employeeIdsAll)
                ->delete();

            // ✅ preload izin sekali saja
            $izinData = $this->formPerijinanModel
                ->whereIn('employee_id', $employeeIds)
                ->whereIn('periode', $dateList)
                ->findAll();
            $izinMap = [];
            foreach ($izinData as $i) {
                $izinMap[$i['employee_id'] . '_' . $i['periode']] = $i;
            }

            // ✅ preload hari libur
            $liburData = $this->bigDaysModel->whereIn('date', $dateList)->findAll();
            $liburMap = array_column($liburData, null, 'date'); // key = tanggal

            // ✅ preload attendance log
            $logs = $this->attendancesModel
                ->select("employee_id, periode AS tgl, checkin, checkout")
                ->whereIn('employee_id', $employeeIds)
                ->where("periode >=", $startDate)
                ->where("periode <=", $endDate)
                ->groupBy("employee_id, periode")
                ->findAll();

            $logMap = [];
            foreach ($logs as $l) {
                $logMap[$l['employee_id'] . '_' . $l['tgl']] = $l;
            }

            // ✅ preload golongan
            $golonganData = (new GolonganModel())
                ->where('company_id', $this->this_company_id)
                ->findAll();
            $golonganMap = [];
            foreach ($golonganData as $g) {
                $golonganMap[$g['golongan_name']] = $g['nominal_pinjaman'];
            }

            // siapkan batch insert
            $insertData = [];

            foreach ($employeeData as $e) {
                $hadir = 0;
                $tidakHadir = 0;

                foreach ($dateList as $tgl) {
                    $izin    = $izinMap[$e['id'] . '_' . $tgl] ?? null;
                    $libur   = $liburMap[$tgl] ?? null;
                    $logAbsen = $logMap[$e['id'] . '_' . $tgl] ?? null;

                    // if (($libur != null || date('l', strtotime($tgl)) == "Sunday") && $izin == null && $logAbsen == null) {

                    if ($libur != null || date('l', strtotime($tgl)) == "Sunday") {
                        $tidakHadir++;
                    } elseif ($izin != null) {
                        if (in_array($izin['status'], ["ALPHA_A", "POTONG GAJI_PG", "CUTI HAMIL_CHL", "CUTI MELAHIRKAN_CM", "0FF_OFF"])) {
                            $tidakHadir++;
                        } else {
                            $hadir++;
                        }
                    } else {
                        if ($logAbsen == null) {
                            $tidakHadir++;
                        } else {
                            $hadir++;
                        }
                    }
                }

                $nominalPinjaman = $golonganMap[$e['tipe']] ?? 0;

                $insertData[] = [
                    'company_id'      => $this->this_company_id,
                    'employee_id'     => $e['id'],
                    'division_id'     => $e['division_id'],
                    'month_year'      => $yearMonth,
                    'start_date'      => $startDate,
                    'end_date'        => $endDate,
                    'tidak_hadir'     => $tidakHadir,
                    'hadir'           => $hadir,
                    'is_boleh_minjam' => ($hadir >= 8) ? '1' : '0',
                    'status_pinjaman' => ($hadir >= 8) ? '1' : '0',
                    'is_ambil'        => ($hadir >= 8) ? '1' : '0',
                    'nominal'         => ($hadir >= 8) ? $nominalPinjaman : null,
                    'tanggal_ambil'   => $tanggalAmbil
                ];
            }

            // ✅ batch insert sekali jalan
            $this->pinjamanKaryawanModel->insertBatch($insertData, 200);

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'message' => 'Generate Data Karyawan yang Berhak Meminjam Berhasil',
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }


    public function generateSinglePinjaman()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $yearMonth  = $this->request->getVar('monthYearSingle');
            $startDate  = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('startDate'))));
            $endDate    = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('finishDate'))));
            $tanggalAmbil    = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('tanggalAmbil_Personal'))));
            $employeeID = $this->request->getVar('employeeID');
            $id         = $this->request->getVar('id');

            // validasi tanggal
            if (strtotime($startDate) > strtotime($endDate)) {
                return response()->setJSON([
                    "status"  => false,
                    "message" => "Tanggal mulai absen dan tanggal selesai absen tidak sesuai",
                    'token'   => csrf_hash()
                ]);
            }

            $startDateTimestamp = strtotime($startDate);
            $endDateTimestamp   = strtotime($endDate);
            $selisihHari        = ($endDateTimestamp - $startDateTimestamp) / (60 * 60 * 24);

            if ($selisihHari > 15) {
                return response()->setJSON([
                    "status"  => false,
                    "message" => "Periode tidak boleh melebihi 15 hari kerja. Pinjaman maksimal 15 hari kerja. (Total $selisihHari hari)",
                    'token'   => csrf_hash()
                ]);
            }

            // hapus data lama
            $this->pinjamanKaryawanModel->delete($id);

            // generate range tanggal
            $dateList = [];
            while ($startDateTimestamp <= $endDateTimestamp) {
                $dateList[] = date('Y-m-d', $startDateTimestamp);
                $startDateTimestamp += 86400;
            }

            // ambil data karyawan
            $employee = $this->employeeModel
                ->where('id', $employeeID)
                ->where('deletedAt', null)
                // ->where('status', "Aktif")
                ->first();

            if (!$employee) {
                return response()->setJSON([
                    "status"  => false,
                    "message" => "Karyawan tidak ditemukan",
                    'token'   => csrf_hash()
                ]);
            }

            // preload izin, hari libur, dan attendance log supaya ga query berulang
            $izinList = $this->formPerijinanModel
                ->where('employee_id', $employee['id'])
                ->whereIn('periode', $dateList)
                ->findAll();
            $izinMap = [];
            foreach ($izinList as $izin) {
                $izinMap[$izin['periode']] = $izin;
            }

            $hariLiburList = $this->bigDaysModel
                ->whereIn('date', $dateList)
                ->findAll();
            $hariLiburMap = array_column($hariLiburList, null, 'date');

            $attLogs = $this->attendancesModel
                ->select("periode as tanggal, checkin, checkout")
                ->where('employee_id', $employee['id'])
                ->whereIn("periode", $dateList)
                ->groupBy('periode')
                ->findAll();
            $logMap = [];
            foreach ($attLogs as $log) {
                $logMap[$log['tanggal']] = $log;
            }

            $hadir = 0;
            $tidakHadir = 0;

            foreach ($dateList as $dates) {
                $izin = $izinMap[$dates] ?? null;
                $hariLibur = $hariLiburMap[$dates] ?? null;
                $log = $logMap[$dates] ?? null;

                // if ($hariLibur != null || (date('l', strtotime($dates)) == "Sunday" && !$izin && !$log)) {

                if ($hariLibur != null || date('l', strtotime($dates)) == "Sunday") {
                    $tidakHadir++;
                } elseif ($izin != null) {
                    if (in_array($izin['status'], ["ALPHA_A", "POTONG GAJI_PG", "CUTI HAMIL_CHL", "CUTI MELAHIRKAN_CM", "OFF_OFF"])) {
                        $tidakHadir++;
                    } else {
                        $hadir++;
                    }
                } elseif (!$izin && !$log) {
                    $tidakHadir++;
                } else {
                    $hadir++;
                }
            }

            // ambil golongan sekali aja
            $golonganDetail = $this->golonganModel
                ->where('golongan_name', $employee['tipe'])
                ->first();

            $nominalPinjaman = $golonganDetail ? $golonganDetail['nominal_pinjaman'] : 0;

            $this->pinjamanKaryawanModel->insert([
                'company_id'      => $this->this_company_id,
                'employee_id'     => $employee['id'],
                'division_id'     => $employee['division_id'],
                'month_year'      => $yearMonth,
                'start_date'      => $startDate,
                'end_date'        => $endDate,
                'tidak_hadir'     => $tidakHadir,
                'hadir'           => $hadir,
                'is_boleh_minjam' => ($hadir >= 8) ? '1' : '0',
                'status_pinjaman' => ($hadir >= 8) ? '1' : '0',
                'nominal'         => ($hadir >= 8) ? $nominalPinjaman : null,
                'tanggal_ambil'   => $tanggalAmbil
            ]);

            $db->transCommit();

            return response()->setJSON([
                'status'  => true,
                'message' => 'Generate Single Data Karyawan yang Berhak Meminjam Berhasil',
                'token'   => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
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

        $condition = [
            "employees.deletedAt" => null,
            "employees.company_id" => $this->this_company_id,
            "pinjaman_karyawan.month_year" => $month,
        ];

        $addCondition = [
            "divisi_id"          => $this->request->getGet("divisi_id"),
            "employee_id"        => $this->request->getGet("employee_id"),
            "employees.tipe"     => $this->request->getGet("tipe"),
            "employees.bagian_id" => $this->request->getGet("bagian_id"),
            "status_pinjaman" => $this->request->getGet('status_pinjaman'),
            "sort" => $this->request->getGet('sort'),
            "sortType" => $this->request->getGet('sortType')
        ];

        $pinjamanKaryawanData = $this->pinjamanKaryawanModel->getList($condition, $addCondition, $limit, $offset);
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
                "isAmbil" => $p->is_ambil,
                "nominalPinjaman" => (float)$p->nominal,
                // helper
                "monthYear" => $p->month_year,
                "employeeID" => $p->employee_id,
                "tipeGol" => $p->tipe == null ? "-" : $p->tipe,
                "tanggal_ambil" => $p->tanggal_ambil == null ? "" : date('d/m/Y', strtotime($p->tanggal_ambil)),
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

    public function update()
    {
        try {
            $id = $this->request->getVar('id');
            $statusPinjaman = $this->request->getVar('status_pinjaman');
            $nominal = $this->request->getVar('nominal');
            $this->pinjamanKaryawanModel->update($id, [
                'status_pinjaman' => $statusPinjaman,
                'is_ambil' => $statusPinjaman,
                'nominal' => $nominal
            ]);
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Data berhasil diupdate",
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function delete()
    {
        try {
            $id = $this->request->getVar('id');
            $this->pinjamanKaryawanModel->delete($id);
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Data terhapus",
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function exportPDF()
    {
        $yearMonth = $this->request->getVar('year_month');
        $divisiId = $this->request->getVar('divisi_id');
        $bagianId = $this->request->getVar('bagian_id');

        $selectQry = "pinjaman_karyawan.*,employees.name,employees.nip";

        $data = [
            'divisi' => $this->divisiModel->where('id', $divisiId)->first(),
            'bagian' => $this->bagianModel->where('id', $bagianId)->first(),
            'pinjaman' => $this->pinjamanKaryawanModel->select($selectQry)
                ->join('employees', 'employees.id = pinjaman_karyawan.employee_id', 'left')
                ->where('pinjaman_karyawan.month_year', $yearMonth)
                ->where('pinjaman_karyawan.division_id', $divisiId)
                ->where('employees.bagian_id', $bagianId)
                ->where('pinjaman_karyawan.is_boleh_minjam', '1')
                ->where('pinjaman_karyawan.is_ambil', '1')
                ->orderBy('employees.nip', "asc")
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
