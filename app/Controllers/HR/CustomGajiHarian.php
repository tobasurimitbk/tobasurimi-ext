<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesModel;
use App\Models\EmployeeJamKerjaModel;
use App\Models\GajiConjunctionModel;
use App\Models\PayrollCustomGajiHarianModel;
use CodeIgniter\HTTP\Request;
use Exception;

class CustomGajiHarian extends BaseController
{
    protected $this_company_id;
    protected $payrollCustomGajiHarianModel;
    protected $attendanceModel;
    protected $gajiConjunctionModel;
    protected $employeeJamKerjaModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->payrollCustomGajiHarianModel = new PayrollCustomGajiHarianModel();
        $this->attendanceModel = new AttendancesModel();
        $this->gajiConjunctionModel = new GajiConjunctionModel();
        $this->employeeJamKerjaModel = new EmployeeJamKerjaModel();
    }

    public function index()
    {
        return view('hr/customGajiHarian/index');
    }

    public function create()
    {
        return view('hr/customGajiHarian/form');
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $customGajiHarian = $this->payrollCustomGajiHarianModel->detail($id);
        $data = [
            'customGajiHarian' => $customGajiHarian,
        ];

        return view('hr/customGajiHarian/form', $data);
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
            "payroll_custom_gaji_harian.company_id" => $this->this_company_id,
            "payroll_custom_gaji_harian.deletedAt" => null,
            'MONTH(payroll_custom_gaji_harian.tanggal)' => $monthYear[1],
            'YEAR(payroll_custom_gaji_harian.tanggal)' => $monthYear[0]
        ];

        $addCondition = [
            "search" => $this->request->getGet("search"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");


        $result = $this->payrollCustomGajiHarianModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );
        $dataResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($result['data'] as $p) {
            array_push($dataResult, [
                "no" => $no++,
                "id" => encrypt($p->id),
                "nip" => $p->nip,
                "name" => $p->name,
                "divisi" => $p->divisi,
                "nama_bagian" => $p->nama_bagian,
                "tanggal" => date('d/m/Y', strtotime($p->tanggal)),
                "nominal_gaji_harian" => (float)$p->nominal_gaji_harian,
                "nominal_cadangan" => (float)$p->nominal_cadangan,
                "nominal" => (float)$p->nominal,
                "keterangan" => $p->keterangan,
            ]);
        }
        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $result['totalData'],
            "recordsFiltered"   => $result['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $employeeId = $this->request->getVar('employee_id');
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $keterangan = $this->request->getVar('keterangan');
            $nominal = $this->request->getVar('nominal');
            $nominalGajiHarian = $this->request->getVar('nominal_gaji_harian');
            $nominalCadangan = $this->request->getVar('nominal_cadangan');
            $checkIn = $this->request->getVar('checkin');
            $checkOut = $this->request->getVar('checkout');
            $totalJam = $this->request->getVar('total_jam');

            $first = $this->payrollCustomGajiHarianModel
                ->where('employee_id', $employeeId)
                ->where('tanggal', $tanggal)
                ->where('deletedAt', null)
                ->first();

            if ($first != null) {
                return response()->setJSON([
                    'message' => "Data sudah pernah terinput",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            $this->payrollCustomGajiHarianModel->insert([
                'company_id' => $this->this_company_id,
                'employee_id' => $employeeId,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'nominal' => $nominal,
                'nominal_gaji_harian' => $nominalGajiHarian,
                'nominal_cadangan' => $nominalCadangan,
                'checkin' => $checkIn,
                'checkout' => $checkOut,
                'total_jam' => $totalJam

            ]);
            $db->transCommit();
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Data tersimpan"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $employeeId = $this->request->getVar('employee_id');
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $keterangan = $this->request->getVar('keterangan');
            $nominal = $this->request->getVar('nominal');
            $nominalGajiHarian = $this->request->getVar('nominal_gaji_harian');
            $nominalCadangan = $this->request->getVar('nominal_cadangan');
            $checkIn = $this->request->getVar('checkin');
            $checkOut = $this->request->getVar('checkout');
            $totalJam = $this->request->getVar('total_jam');

            $first = $this->payrollCustomGajiHarianModel
                ->where('employee_id', $employeeId)
                ->where('tanggal', $tanggal)
                ->where('deletedAt', null)
                ->where('id !=', $id)
                ->first();

            if ($first != null) {
                return response()->setJSON([
                    'message' => "Data sudah pernah terinput",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            $this->payrollCustomGajiHarianModel->update($id, [
                'company_id' => $this->this_company_id,
                'employee_id' => $employeeId,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
                'nominal' => $nominal,
                'nominal_gaji_harian' => $nominalGajiHarian,
                'nominal_cadangan' => $nominalCadangan,
                'checkin' => $checkIn,
                'checkout' => $checkOut,
                'total_jam' => $totalJam

            ]);
            $db->transCommit();
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Data terupdate"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->payrollCustomGajiHarianModel->delete($id);
        return response()->setJSON([
            'message' => "Data terhapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getAttendance()
    {
        try {
            $employeeId = $this->request->getVar('employee_id');
            $tanggal    = formatDMYtoYMD($this->request->getVar('tanggal'));

            /* =======================
         * Ambil detail absensi
         * ======================= */
            $attendanceDetail = $this->attendanceModel
                ->where('periode', $tanggal)
                ->where('employee_id', $employeeId)
                ->first();

            if ($attendanceDetail === null) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data absensi belum digenerate / tidak ada, silakan generate data personal karyawan tersebut',
                    'token'   => csrf_hash()
                ]);
            }

            /* =======================
         * Cek jam kerja karyawan
         * ======================= */
            $jamKerja = $this->employeeJamKerjaModel
                ->getJamKerjaDetailByEmployeeId($tanggal, $employeeId);

            if ($jamKerja === null) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Jam kerja belum dibuat untuk departemen karyawan ini',
                    'token'   => csrf_hash()
                ]);
            }

            /* =======================
         * Ambil gaji harian & cadangan
         * ======================= */
            $gajiList = $this->gajiConjunctionModel
                ->select('gaji_conjunction.nominal, tunjangan.is_cadangan, tunjangan.is_gaji_harian')
                ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id', 'left')
                ->where('gaji_conjunction.employee_id', $employeeId)
                ->groupStart()
                ->where('tunjangan.is_cadangan', 1)
                ->orWhere('tunjangan.is_gaji_harian', 1)
                ->groupEnd()
                ->findAll();

            $gajiHarian = 0.0;
            $cadangan   = 0.0;

            foreach ($gajiList as $row) {
                if ((int) $row['is_gaji_harian'] === 1) {
                    $gajiHarian = (float) $row['nominal'];
                }

                if ((int) $row['is_cadangan'] === 1) {
                    $cadangan = (float) $row['nominal'];
                }
            }

            /* =======================
         * Hitung jam kerja (support lintas hari)
         * ======================= */
            $checkIn  = $attendanceDetail['checkin'];   // contoh: 23:00
            $checkOut = $attendanceDetail['checkout'];  // contoh: 03:00

            if ($checkIn == null && $checkOut == null) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Pegawai tidak scan finger di tanggal tersebut",
                    'token' => csrf_hash()
                ]);
            }

            $totalJamKerja = $this->hitungTotalJam($checkIn, $checkOut);

            /* =======================
         * Hitung nominal gaji
         * ======================= */
            $totalGajiCadangan = $gajiHarian + $cadangan;

            // Potong 1 jam istirahat
            $jamEfektif = max(0, $totalJamKerja - 1);

            $nominal = ($totalGajiCadangan / 7) * $jamEfektif;

            /* =======================
         * Response
         * ======================= */
            return $this->response->setJSON([
                'status' => true,
                'data'   => [
                    'checkin'   => $checkIn,
                    'checkout'  => $checkOut,
                    'total_jam' => $totalJamKerja,
                    'nominal_gaji_harian' => (float)$gajiHarian,
                    'nominal_cadangan' => (float)$cadangan,
                    'nominal'   => (float)round($nominal, 2),
                ],
                'token'  => csrf_hash()
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => $e->getMessage(),
                'token'   => csrf_hash()
            ]);
        }
    }

    /**
     * Helper hitung jam kerja (hari sama & lintas hari)
     * Contoh:
     *  - 08:00 → 17:30 = 9.5 jam
     *  - 23:00 → 03:00 = 4 jam
     */
    private function hitungTotalJam(string $checkIn, string $checkOut): float
    {
        [$inH, $inM]   = explode(':', $checkIn);
        [$outH, $outM] = explode(':', $checkOut);

        $jamMasuk  = ((int) $inH) + ((int) $inM / 60);
        $jamKeluar = ((int) $outH) + ((int) $outM / 60);

        // Lintas hari
        if ($jamKeluar < $jamMasuk) {
            return (24 - $jamMasuk) + $jamKeluar;
        }

        // Hari yang sama
        return $jamKeluar - $jamMasuk;
    }
}
