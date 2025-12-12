<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\AttendancesModel;
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

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->payrollCustomGajiHarianModel = new PayrollCustomGajiHarianModel();
        $this->attendanceModel = new AttendancesModel();
        $this->gajiConjunctionModel = new GajiConjunctionModel();
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
                "checkin" => $p->checkin,
                "checkout" => $p->checkout,
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
            $checkIn = $this->request->getVar('checkin');
            $checkOut = $this->request->getVar('checkout');

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
                'checkin' => $checkIn,
                'checkout' => $checkOut

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
            $checkIn = $this->request->getVar('checkin');
            $checkOut = $this->request->getVar('checkout');

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
                'checkin' => $checkIn,
                'checkout' => $checkOut

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

            // Ambil detail attendance
            $attendanceDetail = $this->attendanceModel
                ->where('periode', $tanggal)
                ->where('employee_id', $employeeId)
                ->first();

            if ($attendanceDetail === null) {
                return response()->setJSON([
                    'message' => "Data absensi belum digenerate / tidak ada, silahkan generate data personal karyawan tersebut",
                    'status'  => false,
                    'token'   => csrf_hash()
                ]);
            }

            // Ambil gaji harian & cadangan (gunakan grouping utk orWhere)
            $gajiCadangan = $this->gajiConjunctionModel
                ->select('gaji_conjunction.nominal, tunjangan.is_cadangan, tunjangan.is_gaji_harian')
                ->join('tunjangan', 'tunjangan.id = gaji_conjunction.tunjangan_id', 'left')
                ->where('gaji_conjunction.employee_id', $employeeId)
                ->groupStart()
                ->where('tunjangan.is_cadangan', 1)
                ->orWhere('tunjangan.is_gaji_harian', 1)
                ->groupEnd()
                ->findAll();

            $gajiHarian = 0;
            $cadangan   = 0;

            foreach ($gajiCadangan as $g) {
                if ((int)$g['is_cadangan'] === 1) {
                    $cadangan = (float)$g['nominal'];
                }
                if ((int)$g['is_gaji_harian'] === 1) {
                    $gajiHarian = (float)$g['nominal'];
                }
            }

            // Nominal yang dikembalikan
            $nominal = $gajiHarian + $cadangan;

            $data = [
                'checkin'  => $attendanceDetail['checkin'],
                'checkout' => $attendanceDetail['checkout'],
                'nominal'  => $nominal
            ];

            return response()->setJSON([
                'data'   => $data,
                'status' => true,
                'token'  => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token'   => csrf_hash(),
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
