<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesLogModel;
use App\Models\AttendancesModel;
use App\Models\BagianModel;
use App\Models\BigDaysModel;
use App\Models\DivisisModel;
use App\Models\EmployeeJamKerjaModel;
use App\Models\EmployeesModel;
use App\Models\FormLemburModel;
use App\Models\GajiConjunctionModel;
use App\Models\GolonganModel;
use App\Models\JamKerjaModel;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FormLembur extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $DivisiModel;
    protected $EmployeeModel;
    protected $FormLemburModel;
    protected $JamKerjaModel;
    protected $GajiConjunctionModel;
    protected $AttendancesLogModel;
    protected $BigdaysModel;
    protected $EmployeeJamKerjaModel;
    protected $GolonganModel;
    protected $AttendancesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->DivisiModel = new DivisisModel();
        $this->EmployeeModel = new EmployeesModel();
        $this->FormLemburModel = new FormLemburModel();
        $this->JamKerjaModel = new JamKerjaModel();
        $this->GajiConjunctionModel = new GajiConjunctionModel();
        $this->AttendancesLogModel = new AttendancesLogModel();
        $this->BigdaysModel = new BigDaysModel();
        $this->EmployeeJamKerjaModel = new EmployeeJamKerjaModel();
        $this->GolonganModel = new GolonganModel();
        $this->AttendancesModel = new AttendancesModel();
    }

    public function index()
    {
        $dataGolongan = $this->GolonganModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->findAll();
        $dataDivisi = $this->DivisiModel->getDivisiAccess();
        $data = [
            'divisi' => $dataDivisi,
            'golongan' => $dataGolongan

        ];

        return view('hr/lembur/index', $data);
    }

    public function createView()
    {
        $divisi = $this->DivisiModel->get_by_company_id($this->this_company_id);
        $data = [
            "divisi" => $divisi,
            'lemburDetail' => null
        ];

        return view('hr/lembur/form', $data);
    }

    public function getEmployeeByDivision()
    {
        $divisiId = $this->request->getVar('divisionID');
        $bagianId = $this->request->getVar('bagianID');

        $employee = $this->EmployeeModel->where('deletedAt', null)
            ->where('division_id', $divisiId)
            ->where('bagian_id', $bagianId)
            ->orderBy('name', "ASC")
            ->findAll();

        return response()->setJSON([
            'data' => $employee,
            'status' => true,
            'token' => csrf_hash(),
        ]);
    }

    public function getById($id)
    {
        $employeesModel = new EmployeesModel();
        $formLemburModel = new FormLemburModel();
        $DivisiModel = new DivisisModel();
        $BagianModel = new BagianModel();

        $id = decrypt($id);
        $lemburDetail = $formLemburModel->where('id', $id)->first();

        if ($lemburDetail == null) {
            return redirect()->to('lembur');
        }

        $employeeFirst = $employeesModel->where('id', $lemburDetail['employee_id'])->first();
        $divisi = $DivisiModel->get_by_company_id($this->this_company_id);
        $employees = $employeesModel->getEmployeesAndDivisi($this->this_company_id);
        $bagian = $BagianModel->where('id', $employeeFirst['bagian_id'])->findAll();

        $data = [
            "divisi" => $divisi,
            'employees' => $employees,
            'lemburDetail' => $lemburDetail,
            'bagian' => $bagian
        ];

        return view('hr/lembur/form', $data);
    }

    public function delete()
    {
        $id = decrypt($this->request->getPost("id"));
        $this->FormLemburModel->delete($id);

        return response()->setJSON([
            'status' => true,
            'message' => "Form data lembur berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $monthYear = explode('-', $this->request->getVar('month'));
        $condition = [
            "form_lembur.company_id" => $this->this_company_id,
            "form_lembur.deletedAt" => null,
            'MONTH(form_lembur.periode)' => $monthYear[1],
            'YEAR(form_lembur.periode)' => $monthYear[0]
        ];

        $addCondition = [
            "search"           => $this->request->getGet("search"),
            "tipe" => $this->request->getGet('tipe'),
            "divisi_id" => $this->request->getGet('divisi_id'),
            "bagian_id" => $this->request->getGet("bagian_id")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");


        $result = $this->FormLemburModel->getList($condition, $addCondition, $limit, $offset);
        $dataFormLembur = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($result['data'] as $p) {
            $splitJamMenit = explode('.', $p->total_jam_lembur);

            if (count($splitJamMenit) == 2) {
                $jam = $splitJamMenit[0];
                if (count(str_split($splitJamMenit[1])) == 2) {
                    $menit = str_pad($splitJamMenit[1], 2, '0', STR_PAD_LEFT);
                } else {
                    $menit = $splitJamMenit[1] . "0";
                }
            } else {
                $jam = $p->total_jam_lembur;
                $menit = '00';
            }

            array_push($dataFormLembur, [
                "no" => $no++,
                "id" => encrypt($p->id),
                "nip" => $p->nip,
                "name" => $p->name,
                "divisi" => $p->divisi,
                "nama_bagian" => $p->nama_bagian,
                "periode" => date('d/m/Y', strtotime($p->periode)),
                "jam_mulai_lembur" => $p->jam_mulai_lembur,
                "jam_selesai_lembur" => $p->jam_selesai_lembur,
                "total_jam_lembur" => $jam . " Jam " . $menit . " Menit",
                "total_uang_lembur" => number_format($p->total_uang_lembur, 2)
            ]);
        }
        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $result['totalData'],
            "recordsFiltered"   => $result['totalFilteredData'],
            "data"              => $dataFormLembur,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function generateLemburPay()
    {
        try {
            $employeeID = $this->request->getVar('employeeID');
            $tanggal = $this->request->getVar('tanggalLembur');
            $jamSelesaiLembur = $this->request->getVar('jamSelesaiLembur');
            $gajiPokokPerHari = $this->request->getVar('gajiPokokPerHari');

            // declare Variable
            $checkOutLog = ""; // di set sebagai selesai lembur
            $jumlahJamIstirahat = 0;
            $jumlahJamKerjaBersih = 0;
            $gajiPokok = 0;
            $totalJamLembur = 0;
            $totalLemburJamPertama = 0;
            $totalLemburJamBerikutnya = 0;
            $bayaranLemburJamPertama = 0;
            $bayaranLemburJamBerikutnya = 0;

            // Check
            if (empty($employeeID) || empty($tanggal)) {
                return response()->setJSON([
                    'message' => "Inputan Nama Karyawan dan Tanggal lembur wajib diisi !",
                    'status' => false,
                    'code' => 422
                ]);
            }

            // GET JAM KERJA USED
            $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));
            $jamKerja = $this->EmployeeJamKerjaModel->getJamKerjaUsedByEmployeeId($tanggal, $employeeID);
            $lintas_hari = $jamKerja['lintas_hari'] == "yes" ? true : false;
            $dayName = date('D', strtotime($tanggal)); // get nama hari

            if ($jamKerja['lintas_hari'] == "yes") {
                // jam kerja lintas hari
                // Check data di data absensi
                $selectQry = "attendances.checkin,attendances.checkout";

                $logAttendance = $this->AttendancesModel
                    ->select($selectQry)
                    ->where('company_id', $this->this_company_id)
                    ->where('employee_id', $employeeID)
                    ->where("periode",  $tanggal)
                    ->get()
                    ->getResult();
            } else {
                // jam kerja normal
                // Check data di fingerprint
                //     $selectQry = "DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
                // DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout";

                //     $logAttendance = $this->AttendancesLogModel
                //         ->select($selectQry)
                //         ->where('company_id', $this->this_company_id)
                //         ->where('employees_id', $employeeID)
                //         ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $tanggal)
                //         ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
                //         ->limit(2)
                //         ->get()
                //         ->getResult();
                $selectQry = "attendances.checkin,attendances.checkout";

                $logAttendance = $this->AttendancesModel
                    ->select($selectQry)
                    ->where('company_id', $this->this_company_id)
                    ->where('employee_id', $employeeID)
                    ->where("periode",  $tanggal)
                    ->get()
                    ->getResult();
            }


            if (count($logAttendance) == 0) {
                return response()->setJSON([
                    'message' => "Karyawan belum melakukan presensi fingerprint pada tanggal $tanggal",
                    'status' => false,
                    'code' => 400
                ]);
            }
            // asign to max date create
            if ($logAttendance[0]->checkout != $logAttendance[0]->checkin) {
                // ada in and out
                $checkOutLog = date('H:i', \strtotime($logAttendance[0]->checkout));
            } else {
                // in
                $checkOutLog = date('H:i', \strtotime($logAttendance[0]->checkin));
            }

            // cek hari besar
            $hariBesar = $this->BigdaysModel->where('date', $tanggal)->where('company_id', $this->this_company_id)->first();
            // jika hari besar yha libur gak ada lembur
            // if ($hariBesar != null) {
            //     return response()->setJSON([
            //         'message' => "$tanggal adalah hari besar " . $hariBesar['name'] . ". jadi ga bisa ambil lembur di hari tersebut",
            //         'status' => false,
            //         'code' => 400
            //     ]);
            // }

            // check apakah sudah presensi pulang di log
            if ($logAttendance[0]->checkout == $logAttendance[0]->checkin) {
                // belum ada presensi pulang di log
                return response()->setJSON([
                    'message' => "Karyawan belum melakukan presensi pulang pada tanggal $tanggal",
                    'status' => false,
                    'code' => 400
                ]);
            }

            // update checkout (jika dia input manual)
            if (!empty($jamSelesaiLembur)) {
                $checkOutLog = $jamSelesaiLembur;
            }

            // get jam kerja
            $hariInIndonesia = static::getDayIndonesia(date('l', strtotime($tanggal)));

            $jamKerjaDetail = $this->JamKerjaModel
                ->select('jam_kerja_detail.*')
                ->join('jam_kerja_detail', 'jam_kerja.id = jam_kerja_detail.jam_kerja_id')
                ->where('jam_kerja.company_id', $this->this_company_id)
                ->where('jam_kerja.id', $jamKerja['id'])
                ->where('jam_kerja_detail.hari', $hariInIndonesia)
                ->where('jam_kerja_detail.deletedAt', null)
                ->first();

            // cek jam kerja detail apakah kosong
            if ($jamKerjaDetail == null) {
                return response()->setJSON([
                    'message' => "Terjadi kesalahan, jam kerja belum diset untuk divisi ini",
                    'status' => false,
                    'code' => 400
                ]);
            }

            // Default Jam Istirahat
            if ($jamKerjaDetail['jam_istirahat_mulai'] == null) {
                $jamKerjaDetail['jam_istirahat_mulai'] = "12:01";
            }
            if ($jamKerjaDetail['jam_istirahat_selesai'] == null) {
                $jamKerjaDetail['jam_istirahat_selesai'] = "13:00";
            }


            // cari selisih waktu jam masuk dan checkout (bersih) => jam masuk -> checkout
            $waktuSelisihMasukPulang = static::selisihWaktu($jamKerjaDetail['jam_masuk'], $checkOutLog, $lintas_hari);
            $jumlahJamKerjaBersih = \abs($waktuSelisihMasukPulang['jam']) . " Jam , " . \abs($waktuSelisihMasukPulang['menit']) . " Menit";

            // gaji
            $gaji = $this->GajiConjunctionModel->select("tunjangan.name, gaji_conjunction.nominal, tunjangan.is_gaji_harian")
                ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
                ->where('gaji_conjunction.employee_id', $employeeID)
                ->where('tunjangan.tipe', "PLUS")
                ->where('tunjangan.is_gaji_harian', 1)
                ->findAll();

            $cadangan = $this->GajiConjunctionModel->select("tunjangan.name, gaji_conjunction.nominal, tunjangan.is_cadangan")
                ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
                ->where('gaji_conjunction.employee_id', $employeeID)
                ->where('tunjangan.tipe', "PLUS")
                ->where('tunjangan.is_cadangan', 1)
                ->findAll();

            // get gaji pokok
            foreach ($gaji as $g) {
                if ($g['is_gaji_harian'] == 1) {
                    $gajiPokok = $gajiPokokPerHari == "-" ? $g['nominal'] : $gajiPokokPerHari;
                }
            }

            // get cadangan
            $gajiCadangan = 0;
            foreach ($cadangan as $c) {
                if ($c['is_cadangan'] == 1) {
                    $gajiCadangan = $c['nominal'];
                }
            }

            // var_dump($gaji, $cadangan);
            // die;

            $gajiPokok = $gajiPokok + $gajiCadangan;

            // hitung total jam lembur
            $waktuSelisihPulangLembur = static::selisihWaktu(
                $jamKerjaDetail['jam_pulang'],
                $checkOutLog,
                $lintas_hari
            );


            $totalJamLembur = (float)$waktuSelisihPulangLembur['jam'] . "." . $waktuSelisihPulangLembur['menit'];

            if ($totalJamLembur <= 0.29) {
                return response()->setJSON([
                    'message' => "Minimal pegawai dapat mengambil lembur adalah setengah jam. Tanggal " . date('d/m/Y', strtotime($tanggal)) . " hanya menghasilkan total jam lembur sebesar " . $waktuSelisihPulangLembur['menit'] . " Menit. Pegawai Checkout Jam " . $checkOutLog . " dan Waktu Pulang di Jam Kerja Adalah Jam " . $jamKerjaDetail['jam_pulang'] . ". Sehingga tidak memenuhi persyaratan :)",
                    'status' => \false,
                    'code' => 400
                ]);
            }


            // var_dump($tanggal);
            // die;

            if ($hariBesar || $dayName == 'Sun') {
                // Libur
                // chek apakah lembur lebih dari satu jam
                // jam pertama
                $totalLemburJamPertama = 0;
                $bayaranLemburJamPertama = 0;
                // sisanya
                $sisaWaktu = static::kurangiWaktu(
                    $waktuSelisihPulangLembur['jam'] . ":" . $waktuSelisihPulangLembur['menit'],
                    60.00 // satu jam
                ) + 1;

                // var_dump($sisaWaktu, $gajiPokok, $waktuSelisihPulangLembur['jam'] . ":" . $waktuSelisihPulangLembur['menit']);
                // die;

                // lebih satu jam
                $totalLemburJamBerikutnya = $sisaWaktu + 1;
                $bayaranLemburJamBerikutnya = ((1 / 173) * 25 * 2) * $sisaWaktu * $gajiPokok;
            } else {
                // Normal
                // chek apakah lembur lebih dari satu jam
                if ($totalJamLembur >= 1) {
                    // jam pertama
                    $totalLemburJamPertama = 1;
                    $bayaranLemburJamPertama = ((1 / 173) * 25 * 1.5) * 1 * $gajiPokok;
                    // sisanya
                    $sisaWaktu = static::kurangiWaktu(
                        $waktuSelisihPulangLembur['jam'] . ":" . $waktuSelisihPulangLembur['menit'],
                        60.00 // satu jam
                    );

                    // lebih satu jam
                    $totalLemburJamBerikutnya = $sisaWaktu;
                    $bayaranLemburJamBerikutnya = ((1 / 173) * 25 * 2) * $sisaWaktu * $gajiPokok;
                } else if ($totalJamLembur > 1 && $totalJamLembur < 2) {
                    // cuma satu jam
                    $totalLemburJamPertama = $totalJamLembur;
                    $bayaranLemburJamPertama = ((1 / 173) * 25 * 1.5) * 1 * $gajiPokok;
                } else {
                    $totalLemburJamPertama = $totalJamLembur;
                    $bayaranLemburJamPertama = ((1 / 173) * 25 * 1.5) * 0.5 * $gajiPokok;
                }
            }

            if (($totalLemburJamPertama + $totalLemburJamBerikutnya) <= 0) {
                return response()->setJSON([
                    'message' => "Tidak memenuhi syarat melakukan lembur karena pegawai checkout sebelum jam pulang, silahkan cek menu log absensi",
                    'status' => \false,
                    'code' => 400

                ]);
            }

            $finalBayaranLemburJamPertama = \number_format($bayaranLemburJamPertama, 2, '.', '');
            $finalBayaranLemburJamBerikutnya = \number_format($bayaranLemburJamBerikutnya, 2, '.', '');

            $result = [
                'jamKerja' => [
                    'jamKerjaMasuk' => $jamKerjaDetail['jam_masuk'],
                    'jamKerjaKeluar' => $jamKerjaDetail['jam_pulang'],
                    'jamMulaiLembur' => $jamKerjaDetail['jam_pulang'],
                    'jamSelesaiLembur' => $checkOutLog,
                    'jumlahJamIstirahat' => $jumlahJamIstirahat,
                    'jumlahJamKerjaBersih' => $jumlahJamKerjaBersih
                ],
                'komponenGaji' => array_merge($gaji, $cadangan),
                'upah' => $gajiPokok,
                'lembur' => [
                    'lemburJamPertama' => [
                        'totalLemburJamPertama' => \number_format(\abs($totalLemburJamPertama), 2, '.', ''),
                        'bayaran' => $finalBayaranLemburJamPertama
                    ],
                    'lemburJamBerikutnya' => [
                        'totalLemburJamKedua' => \number_format(\abs($totalLemburJamBerikutnya), 2, '.', ''),
                        'bayaran' => $finalBayaranLemburJamBerikutnya
                    ],
                    'totalBayaran' => \number_format($finalBayaranLemburJamPertama + $finalBayaranLemburJamBerikutnya, 2, '.', ''),
                    'totalJamLembur' => $totalJamLembur
                ],
                'status' => true,
            ];

            return response()->setJSON($result);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        $periode = $this->request->getVar('tanggalLembur');
        $tanggalObj = DateTime::createFromFormat('d/m/Y', $periode);
        $employeeId =  $this->request->getVar('employeeID');
        $totalJamLembur = (float)$this->request->getVar('totalJamLembur');
        $totalUangLembur = (float)$this->request->getVar('totalUangLembur');
        $kurangiJamIstirahat = $this->request->getVar('kurangiJamIstirahat');
        $jamMulaiLembur = $this->request->getVar('jamMulaiLembur');
        $jamSelesaiLembur = $this->request->getVar('jamSelesaiLembur');
        $gajiPokokPerHari = (float)$this->request->getVar('gajiPokokPerHari');

        $check = $this->FormLemburModel
            ->where('employee_id', $employeeId)
            ->where('periode', $tanggalObj->format('Y-m-d'))
            ->first();

        if ($check != null) {
            return response()->setJSON([
                'message' => "Karyawan sudah mengambil lembur tanggal " . $this->request->getVar('tanggalLembur'),
                'status' => \false,
                'token' => csrf_hash()
            ]);
        }

        $employee = $this->EmployeeModel->where('id', $employeeId)->first();

        $this->FormLemburModel->insert([
            'company_id' => $this->this_company_id,
            'division_id' => $employee['division_id'],
            'employee_id' => $employeeId,
            'periode' => $tanggalObj->format('Y-m-d'),
            'total_jam_lembur' => $totalJamLembur,
            'total_uang_lembur' => $totalUangLembur,
            'kurangi_jam_istirahat' => $kurangiJamIstirahat,
            'jam_mulai_lembur' => $jamMulaiLembur,
            'jam_selesai_lembur' => $jamSelesaiLembur,
            'gaji_pokok_per_hari' => $gajiPokokPerHari
        ]);

        return response()->setJSON([
            'message' => "Form lembur berhasil disimpan",
            'token' => csrf_hash(),
            'status' => \true
        ]);
    }

    public function exportBulananLembur()
    {
        // ================= Input & Periode =================
        $monthReq = $this->request->getVar('month') ?: date('Y-m');
        [$year, $month] = explode('-', $monthReq);

        $monthName = strtoupper(date('F Y', strtotime("$year-$month-01"))); // contoh: SEPTEMBER 2025
        $startDate = date('Y-m-01', strtotime($monthReq));
        $endDate   = date('Y-m-t', strtotime($monthReq));
        $totalDaysInMonth = date('t', strtotime($monthReq)); // lebih aman daripada cal_days_in_month

        // ================= Data Employee =================
        $condition = [
            "employees.company_id" => $this->this_company_id,
            "employees.deletedAt"  => null,
        ];
        $addCondition = [
            "divisi_id"   => $this->request->getVar('divisi_id'),
            "tipe"        => $this->request->getVar('tipe'),
            "employee_id" => $this->request->getVar("employee_id"),
        ];

        $employees    = $this->EmployeeModel->getEmployeeListAttendances($condition, $addCondition, 0, 10000000);
        $employeeData = $employees['data'];
        $employeeIds  = array_column($employeeData, 'id');

        // ================= Data Lembur =================
        $lembur = !empty($employeeIds)
            ? $this->FormLemburModel->getFormLemburRangeAmt($employeeIds, $startDate, $endDate)
            : [];

        $mapLog = [];
        foreach ($lembur as $l) {
            $selisih = static::selisihWaktu($l['jam_mulai_lembur'], $l['jam_selesai_lembur']);
            $durasi  = (float) $selisih['jam'] . " Jam, " . $selisih['menit'] . " Menit";

            $mapLog[$l['employee_id']][$l['periode']] = [
                'in'                => $l['jam_mulai_lembur'],
                'out'               => $l['jam_selesai_lembur'],
                'durasi_lembur'     => $durasi,
                'total_uang_lembur' => $l['total']
            ];
        }

        // ================== Excel ==================
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle("Detail Lembur Bulanan");

        // Judul
        $sheet1->mergeCells('A1:Z1');
        $sheet1->setCellValue('A1', "DETIL LEMBUR BULAN $monthName");
        $sheet1->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // ================= Header =================
        $headers   = ['No', 'NIP', 'Nama', 'Divisi', 'Bagian'];
        $rowHeader = 3;
        $colIndex  = 1;

        // Header utama
        foreach ($headers as $h) {
            $sheet1->mergeCellsByColumnAndRow($colIndex, $rowHeader, $colIndex, $rowHeader + 1);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowHeader, $h);
        }

        // Header tanggal
        for ($d = 1; $d <= $totalDaysInMonth; $d++) {
            $startCol = $colIndex;

            $sheet1->mergeCellsByColumnAndRow($startCol, $rowHeader, $startCol + 3, $rowHeader);
            $sheet1->setCellValueByColumnAndRow($startCol, $rowHeader, $d);

            $sheet1->setCellValueByColumnAndRow($startCol,     $rowHeader + 1, "IN");
            $sheet1->setCellValueByColumnAndRow($startCol + 1, $rowHeader + 1, "OUT");
            $sheet1->setCellValueByColumnAndRow($startCol + 2, $rowHeader + 1, "Durasi");
            $sheet1->setCellValueByColumnAndRow($startCol + 3, $rowHeader + 1, "Uang");

            $colIndex += 4;
        }

        $lastCol = $colIndex - 1;

        // Style header
        $sheet1->getStyle("A{$rowHeader}:" . $sheet1->getCellByColumnAndRow($lastCol, $rowHeader + 1)->getCoordinate())
            ->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        // ================= Isi Data =================
        $rowIndex = $rowHeader + 2;
        $no = 1;
        foreach ($employeeData as $e) {
            $colIndex = 1;

            // Info karyawan
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $no++);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['nip']);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['name']);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['divisi']);
            $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $e['bagian']);

            // Data harian
            for ($d = 1; $d <= $totalDaysInMonth; $d++) {
                $tanggal = sprintf("%04d-%02d-%02d", $year, $month, $d);
                $dayLog  = $mapLog[$e['id']][$tanggal] ?? null;

                $in     = $dayLog['in'] ?? '';
                $out    = $dayLog['out'] ?? '';
                $durasi = $dayLog['durasi_lembur'] ?? '';
                $uang   = $dayLog['total_uang_lembur'] ?? null; // biar null kalau kosong

                $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $in);
                $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $out);
                $sheet1->setCellValueByColumnAndRow($colIndex++, $rowIndex, $durasi);

                // uang (rata kanan + format ribuan)
                $cell = $sheet1->getCellByColumnAndRow($colIndex, $rowIndex);
                if ($uang !== null && $uang !== '') {
                    $cell->setValueExplicit((float) $uang, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                    $sheet1->getStyleByColumnAndRow($colIndex, $rowIndex)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                    $sheet1->getStyleByColumnAndRow($colIndex, $rowIndex)
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                }
                $colIndex++;
            }

            $rowIndex++;
        }

        // ================= Style & Border =================
        foreach (range('A', $sheet1->getCellByColumnAndRow($lastCol, 1)->getColumn()) as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }
        // force kalkulasi ulang lebar
        $sheet1->calculateColumnWidths();

        $sheet1->getStyle("A{$rowHeader}:" . $sheet1->getCellByColumnAndRow($lastCol, $rowIndex - 1)->getCoordinate())
            ->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);

        // ================= Download =================
        $filename = "Laporan_Lembur_Bulan_{$monthReq}.xlsx";
        $writer   = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    // helper
    static function selisihWaktu($start, $finish, $lintas_hari = false)
    {
        // normalisasi lintas_hari (terima 'yes', '1', true, dll)
        $isLintas = false;
        if (is_string($lintas_hari)) {
            $isLintas = strtolower($lintas_hari) === 'yes' || strtolower($lintas_hari) === 'true' || $lintas_hari === '1';
        } else {
            $isLintas = (bool) $lintas_hari;
        }

        // helper: ambil jam dan menit dari string H:i atau H:i:s
        $parse = function ($time) {
            if ($time === null || $time === '') return false;
            $parts = explode(':', $time);
            if (count($parts) < 2) return false;
            $h = (int) $parts[0];
            $m = (int) $parts[1];
            // normalisasi range
            $h = max(0, min(23, $h));
            $m = max(0, min(59, $m));
            return [$h, $m];
        };

        $a = $parse($start);
        $b = $parse($finish);

        if ($a === false || $b === false) {
            // kembalikan 00:00 jika parsing gagal
            return ['jam' => '00', 'menit' => '00'];
        }

        list($jamMasuk, $menitMasuk) = $a;
        list($jamLain, $menitLain) = $b;

        // konversi ke total menit dari awal hari
        $totalStart = $jamMasuk * 60 + $menitMasuk;
        $totalFinish = $jamLain * 60 + $menitLain;

        // jika lintas hari dan finish <= start, tambahkan 24 jam ke finish
        if ($isLintas && $totalFinish <= $totalStart) {
            $totalFinish += 24 * 60;
        }

        $diffMinutes = $totalFinish - $totalStart;

        // jika hasil negatif (tidak lintas) set 0 agar tidak bikin kacau logika
        if ($diffMinutes < 0) {
            $diffMinutes = 0;
        }

        $diffHours = intdiv($diffMinutes, 60);
        $diffRemainMinutes = $diffMinutes % 60;

        $selisihJamFormatted = str_pad($diffHours, 2, '0', STR_PAD_LEFT);
        $selisihMenitFormatted = str_pad($diffRemainMinutes, 2, '0', STR_PAD_LEFT);

        return [
            'jam' => $selisihJamFormatted,
            'menit' => $selisihMenitFormatted
        ];
    }

    static function kurangiWaktu($waktu, $menitDikurangkan)
    {
        if (empty($waktu)) {
            throw new \Exception("Format waktu tidak valid atau kosong (kurangiWaktu)");
        }

        // pastikan formatnya H:i
        if (strlen($waktu) == 8) { // misal 05:30:00
            $waktu = substr($waktu, 0, 5); // jadi 05:30
        }

        $waktuObj = DateTime::createFromFormat('H:i', $waktu);
        if (!$waktuObj) {
            throw new \Exception("Gagal parsing waktu: {$waktu}");
        }

        $jam = (int) $waktuObj->format('H');
        $menit = (int) $waktuObj->format('i');

        $totalMenit = ($jam * 60) + $menit - $menitDikurangkan;
        return ($totalMenit / 60);
    }

    static function kurangiWaktus($waktu, $menitDikurangkan)
    {
        if (empty($waktu)) {
            throw new \Exception("Format waktu tidak valid atau kosong (kurangiWaktus)");
        }

        if (strlen($waktu) == 8) { // misal 05:30:00
            $waktu = substr($waktu, 0, 5);
        }

        $waktuObj = DateTime::createFromFormat('H:i', $waktu);
        if (!$waktuObj) {
            throw new \Exception("Gagal parsing waktu: {$waktu}");
        }

        $jam = (int) $waktuObj->format('H');
        $menit = (int) $waktuObj->format('i');

        $totalMenit = ($jam * 60) + $menit - $menitDikurangkan;

        $jamBaru = floor($totalMenit / 60);
        $menitBaru = $totalMenit % 60;

        $hasil = "";
        if ($jamBaru > 0) {
            $hasil .= $jamBaru . " jam ";
        }
        if ($menitBaru > 0) {
            $hasil .= $menitBaru . " menit";
        }
        return $hasil;
    }

    // helper
    static function getDayIndonesia($day)
    {
        $translations = [
            'Sunday'    => 'MINGGU',
            'Monday'    => 'SENIN',
            'Tuesday'   => 'SELASA',
            'Wednesday' => 'RABU',
            'Thursday'  => 'KAMIS',
            'Friday'    => 'JUMAT',
            'Saturday'  => 'SABTU',
        ];

        return isset($translations[$day]) ? $translations[$day] : $day;
    }
}
