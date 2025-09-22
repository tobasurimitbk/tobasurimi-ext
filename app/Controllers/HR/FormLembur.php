<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesLogModel;
use App\Models\AttendancesModel;
use App\Models\BigDaysModel;
use App\Models\DivisisModel;
use App\Models\EmployeeJamKerjaModel;
use App\Models\EmployeesModel;
use App\Models\FormLemburModel;
use App\Models\GajiConjunctionModel;
use App\Models\JamKerjaModel;
use DateInterval;
use DateTime;

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
    }

    public function index()
    {
        return view('hr/lembur/index');
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
        $employee = $this->EmployeeModel->where('deletedAt', null)
            ->where('division_id', $divisiId)
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

        $id = decrypt($id);

        $data = [
            "divisi" => $DivisiModel->get_by_company_id($this->this_company_id),
            'employees' => $employeesModel->getEmployeesAndDivisi($this->this_company_id),
            'lemburDetail' => $formLemburModel->where('id', $id)->first()
        ];

        return view('hr/lembur/form', $data);
    }

    public function delete()
    {
        $id = decrypt($this->request->getPost("id"));
        $this->FormLemburModel->delete($id);

        return response()->setJSON([
            'status' => true,
            'message' => "Form data lembur berhasil dihapus"
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
        $employeeID = $this->request->getVar('employeeID');
        $tanggal = $this->request->getVar('tanggalLembur');
        $kurangiJamIstirahat = $this->request->getVar('kurangiJamIstirahat');
        $jamSelesaiLembur = $this->request->getVar('jamSelesaiLembur');
        $gajiPokokPerHari = $this->request->getVar('gajiPokokPerHari');

        // $modelJamKerja = new JamKerjaModel();
        // $modelGaji = new GajiConjunctionModel();
        // $modelLogAttendance = new AttendancesLogModel();
        // $modelBigDays = new BigDaysModel();
        // $employeeJamKerjaModel = new EmployeeJamKerjaModel();

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

        $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));

        // Check data di fingerprint
        $selectQry = "DATE_FORMAT(MIN(date_create), '%H:%i:%s') AS checkin,
        DATE_FORMAT(MAX(date_create), '%H:%i:%s') AS checkout";

        $logAttendance = $this->AttendancesLogModel
            ->select($selectQry)
            ->where('company_id', $this->this_company_id)
            ->where('employees_id', $employeeID)
            ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $tanggal)
            ->groupBy('DATE_FORMAT(date_create, \'%Y-%m-%d\')')
            ->limit(2)
            ->get()
            ->getResult();

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
        if ($hariBesar != null) {
            return response()->setJSON([
                'message' => "$tanggal adalah hari besar " . $hariBesar['name'] . ". jadi ga bisa ambil lembur di hari tersebut",
                'status' => false,
                'code' => 400
            ]);
        }

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
        // GET JAM KERJA USED
        $jamKerja = $this->EmployeeJamKerjaModel->getJamKerjaUsedByEmployeeId($tanggal, $employeeID);

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
        $waktuSelisihMasukPulang = static::selisihWaktu($jamKerjaDetail['jam_masuk'], $checkOutLog);
        // cari selisih waktu istirahat
        $waktuSelisihIstirahat = static::selisihWaktu($jamKerjaDetail['jam_istirahat_mulai'], $jamKerjaDetail['jam_istirahat_selesai']);
        // get selisih waktu istirahat
        $jumlahJamIstirahat = $waktuSelisihIstirahat['jam'] . " Jam , " . $waktuSelisihIstirahat['menit'] . " Menit";

        // jam istirahat
        if ($kurangiJamIstirahat) {
            // get selisih waktu jam masuk dan jam pulang
            $waktuSelisihMasukPulangIstirahat = static::kurangiWaktus(
                $waktuSelisihMasukPulang['jam'] . ":" . $waktuSelisihMasukPulang['menit'],
                60
            );
            $jumlahJamKerjaBersih = $waktuSelisihMasukPulangIstirahat;
        } else {
            // jangan kurangi jam kerja bersih dengan jam istirahat
            $jumlahJamKerjaBersih = \abs($waktuSelisihMasukPulang['jam']) . " Jam , " . \abs($waktuSelisihMasukPulang['menit']) . " Menit";
        }

        // gaji
        $gaji = $this->GajiConjunctionModel->select("tunjangan.name, gaji_conjunction.nominal, tunjangan.is_gaji_harian")
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
            ->where('gaji_conjunction.employee_id', $employeeID)
            ->where('tunjangan.tipe', "PLUS")
            ->where('tunjangan.is_gaji_harian', 1)
            ->findAll();

        // get gaji pokok
        foreach ($gaji as $g) {
            if ($g['is_gaji_harian'] == 1) {
                $gajiPokok = $gajiPokokPerHari == "-" ? $g['nominal'] : $gajiPokokPerHari;
            }
        }

        // hitung total jam lembur
        $waktuSelisihPulangLembur = static::selisihWaktu(
            $jamKerjaDetail['jam_pulang'],
            $checkOutLog
        );

        $totalJamLembur = (float)$waktuSelisihPulangLembur['jam'] . "." . $waktuSelisihPulangLembur['menit'];

        if ($totalJamLembur <= 0.9) {
            return response()->setJSON([
                'message' => "Minimal pegawai dapat mengambil lembur adalah satu jam. Tanggal " . date('d/m/Y', strtotime($tanggal)) . " hanya menghasilkan total jam lembur sebesar " . $waktuSelisihPulangLembur['menit'] . " Menit. Pegawai Checkout Jam " . $checkOutLog . " dan Waktu Pulang di Jam Kerja Adalah Jam " . $jamKerjaDetail['jam_pulang'] . ". Sehingga tidak memenuhi persyaratan :)",
                'status' => \false,
                'code' => 400
            ]);
        }

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
        } else {
            // cuma satu jam
            $totalLemburJamPertama = $totalJamLembur;
            $bayaranLemburJamPertama = ((1 / 173) * 25 * 1.5) * 1 * $gajiPokok;
        }

        if ($totalLemburJamPertama <= 0) {
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
            'komponenGaji' => $gaji,
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

    // helper
    static function selisihWaktu($start, $finish)
    {
        list($jamMasuk, $menitMasuk) = explode(":", $start);
        list($jamLain, $menitLain) = explode(":", $finish);
        $selisihJam = $jamLain - $jamMasuk;
        $selisihMenit = $menitLain - $menitMasuk;

        if ($selisihMenit < 0) {
            $selisihJam--;
            $selisihMenit += 60;
        }
        $selisihJamFormatted = str_pad($selisihJam, 2, '0', STR_PAD_LEFT);
        $selisihMenitFormatted = str_pad($selisihMenit, 2, '0', STR_PAD_LEFT);

        return [
            'jam' => $selisihJamFormatted,
            'menit' => $selisihMenitFormatted
        ];
    }


    // helper
    static function kurangiWaktu($waktu, $menitDikurangkan)
    {
        $waktuObj = DateTime::createFromFormat('H:i', $waktu);
        $jam = $waktuObj->format('H');
        $menit = $waktuObj->format('i');
        $totalMenit = ($jam * 60) + $menit;
        $totalMenit -= $menitDikurangkan;
        return ($totalMenit / 60);
    }

    static function kurangiWaktus($waktu, $menitDikurangkan)
    {
        $waktuObj = DateTime::createFromFormat('H:i', $waktu);
        $jam = $waktuObj->format('H');
        $menit = $waktuObj->format('i');
        $totalMenit = ($jam * 60) + $menit;
        $totalMenit -= $menitDikurangkan;

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
