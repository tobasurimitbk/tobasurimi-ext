<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Controllers\HR\PinjamanKaryawan;
use App\Models\PayrollsModel;
use App\Models\EmployeesModel;
use App\Models\DivisisModel;
use App\Models\GolonganModel;
use App\Models\BagianModel;
use App\Models\PinjamanKaryawanModel;
use Exception;

class AmbilPinjamanKaryawan extends BaseController
{
    protected $token;
    protected $this_company_id, $userID;
    protected $payrollsModel;
    protected $pinjamanKaryawanModel;



    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userID = session()->get('login')->user_id;
        $this->payrollsModel = new PayrollsModel();
        $this->pinjamanKaryawanModel = new PinjamanKaryawanModel();
    }


    public function index()
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

        return view('Pembayaran/ambilPinjamanKaryawan/index', $data);
    }

    public function getAllAmbilPinjamanKaryawan()
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
            // "bagian_id"          => $this->request->getGet("bagian_id"),
            "employees.tipe"     => $this->request->getGet("tipe")
        ];

        $pinjamanKaryawanModel = new PinjamanKaryawanModel();
        $pinjamanKaryawanData = $pinjamanKaryawanModel->getList($condition, $addCondition, $limit, $offset);
        $dataPinjaman = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($pinjamanKaryawanData['data'] as $p) {
            if ($p->status_pinjaman == '1') {
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
                    "is_ambil" => $p->is_ambil,
                    // helper
                    "monthYear" => $p->month_year,
                    "employeeID" => $p->employee_id,
                    "tipeGol" => $p->tipe == null ? "-" : $p->tipe,
                    "status_pengambilan" => $p->is_ambil == 1 ? 'Sudah' : 'Belum'
                ]);
            }
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

    public function checkIsAmbil()
    {
        $checkList = json_decode($this->request->getVar('checkList'));


        try {
            foreach ($checkList as $p) {

                $this->pinjamanKaryawanModel->update(intval($p->id), [
                    'is_ambil' => intval($p->isChecked)
                ]);
            }
            return response()->setJSON([
                'message' => "Pengambilan Pinjaman Karyawan Sukses",
                'status' => true,
                'token' => csrf_hash(),

            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan " . $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }
}
