<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesLogModel;
use App\Models\BigDaysModel;
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

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        return \view('hr/lembur/index');
    }

    public function createView()
    {
        $employeesModel = new EmployeesModel();

        $data = [
            'employees' => $employeesModel->getEmployeesAndDivisi($this->this_company_id),
            'lemburDetail' => null
        ];

        return \view('hr/lembur/form', $data);
    }

    public function getById($id)
    {
        $employeesModel = new EmployeesModel();
        $formLemburModel = new FormLemburModel();

        $data = [
            'employees' => $employeesModel->getEmployeesAndDivisi($this->this_company_id),
            'lemburDetail' => $formLemburModel->where('id', $id)->first()
        ];

        return \view('hr/lembur/form', $data);
    }

    public function delete()
    {
        $id = $this->request->getPost("id");

        $formLemburModel = new FormLemburModel();
        $formLemburModel->where('id', $id)->delete();

        return \response()->setJSON([
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

        $condition = [
            "form_lembur.company_id" => $this->this_company_id,
            "employees.deletedAt" => null,
        ];

        $addCondition = [
            "nip"           => $this->request->getGet("nip"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $modelFormLembur = new FormLemburModel();

        $result = $modelFormLembur->getList($condition, $addCondition, $limit, $offset);
        $dataFormLembur = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($result['data'] as $p) {
            $tanggalObj = DateTime::createFromFormat('Y-m-d', $p->periode);
            $splitJamMenit = \explode('.', $p->total_jam_lembur);

            array_push($dataFormLembur, [
                "no" => $no++,
                "id" => $p->id,
                "nip" => $p->employeesNIP,
                "name"  => $p->employeesName,
                "divisi" => $p->divisiName,
                "periode" => $tanggalObj->format('d/m/Y'),
                "jam_lembur" => (\count($splitJamMenit) == 2) ? $splitJamMenit[0] . " Jam " . $splitJamMenit[1] . " Menit" : $splitJamMenit[0] . " Jam ",
                "uang_lembur" => "Rp. " . number_format($p->total_uang_lembur, 0, ',', '.')
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $result['totalData'],
            "recordsFiltered"   => $result['totalFilteredData'],
            "data"              => $dataFormLembur,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function generateLemburPay()
    {
        $employeeID = $this->request->getVar('employeeID');
        $tanggal = $this->request->getVar('tanggalLembur');
        $kurangiJamIstirahat = $this->request->getVar('kurangiJamIstirahat');
        $jamSelesaiLembur = $this->request->getVar('jamSelesaiLembur');

        $modelJamKerja = new JamKerjaModel();
        $modelGaji = new GajiConjunctionModel();
        $modelLogAttendance = new AttendancesLogModel();
        $modelEmployee = new EmployeesModel();
        $modelBigDays = new BigDaysModel();

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
            return \response()->setJSON([
                'message' => "Inputan Nama Karyawan dan Tanggal lembur wajib diisi !",
                'status' => false,
                'code' => 422
            ]);
        }

        $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));

        // Check data di fingerprint
        $logAttendance = $modelLogAttendance
            ->where('company_id', $this->this_company_id)
            ->where('employees_id', $employeeID)
            ->where("DATE_FORMAT(date_create, '%Y-%m-%d')",  $tanggal)
            ->limit(2)
            ->orderBy('id', "ASC") // urutkan (masuk, pulang)
            ->get()
            ->getResult();

        // insert data log to variable
        foreach ($logAttendance as $i => $v) {
            if ($i == 1) {
                // ambil jam pulang
                $checkOutLog = \date('H:i', \strtotime($v->date_create));
            } elseif ($i == 0) {
                // jika ga ada (jam masuk)
                $checkOutLog = \date('H:i', \strtotime($v->date_create));
            }
        }

        // cek hari besar
        $hariBesar = $modelBigDays->where('date', $tanggal)->where('company_id', $this->this_company_id)->first();
        // jika hari besar yha libur gak ada lembur
        if ($hariBesar != null) {
            return \response()->setJSON([
                'message' => "$tanggal adalah hari besar " . $hariBesar['name'] . ". jadi ga bisa ambil lembur di hari tersebut",
                'status' => false,
                'code' => 400
            ]);
        }

        // check apakah sudah presensi pulang di log
        if ($checkOutLog == "") {
            // belum ada presensi pulang di log
            return \response()->setJSON([
                'message' => "Karyawan belum melakukan presensi pulang pada tanggal $tanggal",
                'status' => false,
                'code' => 400
            ]);
        }

        // update checkout (jika dia input manual)
        if (!empty($jamSelesaiLembur)) {
            $checkOutLog = $jamSelesaiLembur;
        }

        // employee get first
        $employee = $modelEmployee->where('id', $employeeID)->first();
        // get jam kerja
        $hariInIndonesia = static::getDayIndonesia(date('l', strtotime($tanggal)));
        $jamKerjaDetail = $modelJamKerja
            ->select('jam_kerja_detail.*')
            ->join('jam_kerja_detail', 'jam_kerja.id = jam_kerja_detail.jam_kerja_id')
            ->join('divisis', 'divisis.jam_kerja_id = jam_kerja.id')
            ->where('jam_kerja.company_id', $this->this_company_id)
            ->where('divisis.id', $employee['division_id'])
            ->where('jam_kerja_detail.hari', $hariInIndonesia)
            ->first();

        // cek jam kerja detail apakah kosong
        if ($jamKerjaDetail == null) {
            return \response()->setJSON([
                'message' => "Terjadi kesalahan, jam kerja belum diset untuk divisi ini",
                'status' => \false,
                'code' => 400
            ]);
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
        $gaji = $modelGaji->select("tunjangan.name, gaji_conjunction.nominal, tunjangan.is_gaji_harian")
            ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id')
            ->where('gaji_conjunction.employee_id', $employeeID)
            ->where('tunjangan.tipe', "PLUS")
            ->where('tunjangan.is_gaji_harian', 1)
            ->findAll();

        // get gaji pokok
        foreach ($gaji as $g) {
            if ($g['is_gaji_harian'] == 1) {
                $gajiPokok = $g['nominal'];
            }
        }

        // hitung total jam lembur
        $waktuSelisihPulangLembur = static::selisihWaktu(
            $jamKerjaDetail['jam_pulang'],
            $checkOutLog
        );

        $totalJamLembur = (float)$waktuSelisihPulangLembur['jam'] . "." . $waktuSelisihPulangLembur['menit'];

        if ($totalJamLembur <= 0.9) {
            return \response()->setJSON([
                'message' => "Minimal pegawai dapat mengambil lembur adalah satu jam",
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
            return \response()->setJSON([
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

        echo \json_encode($result);
    }

    public function create()
    {
        $modelFormLembur = new FormLemburModel();
        $periode = $this->request->getVar('tanggalLembur');
        $tanggalObj = DateTime::createFromFormat('d/m/Y', $periode);

        $check = $modelFormLembur->where('employee_id', $this->request->getVar('employeeID'))
            ->where('periode', $tanggalObj->format('Y-m-d'))
            ->first();

        if ($check != null) {
            return \response()->setJSON([
                'message' => "Karyawan sudah mengambil lembur tanggal " . $this->request->getVar('tanggalLembur'),
                'status' => \false
            ]);
        }

        $modelFormLembur->insert([
            'company_id' => $this->this_company_id,
            'employee_id' => $this->request->getVar('employeeID'),
            'periode' => $tanggalObj->format('Y-m-d'),
            'total_jam_lembur' => $this->request->getVar('totalJamLembur'),
            'total_uang_lembur' => $this->request->getVar('totalUangLembur'),
            'kurangi_jam_istirahat' => $this->request->getVar('kurangiJamIstirahat'),
            'jam_mulai_lembur' => $this->request->getVar('jamMulaiLembur'),
            'jam_selesai_lembur' => $this->request->getVar('jamSelesaiLembur'),
            'gaji_pokok_per_hari' => $this->request->getVar('gajiPokokPerHari')
        ]);

        return \response()->setJSON([
            'message' => "Form lembur berhasil disimpan",
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
