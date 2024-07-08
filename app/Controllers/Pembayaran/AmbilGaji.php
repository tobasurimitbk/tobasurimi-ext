<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\PayrollsModel;
use App\Models\EmployeesModel;
use App\Models\DivisisModel;
use App\Models\GolonganModel;
use App\Models\BagianModel;
use Exception;

class AmbilGaji extends BaseController
{
    protected $token;
    protected $this_company_id, $userID;
    protected $payrollsModel;



    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userID = session()->get('login')->user_id;
        $this->payrollsModel = new PayrollsModel();
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

        return view('Pembayaran/ambilGaji/index', $data);
    }

    public function getAllAmbilGaji()
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
                "isAmbil" => $p->isAmbil
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

    public function checkIsAmbil()
    {
        $checkList = json_decode($this->request->getVar('checkList'));


        try {
            foreach ($checkList as $p) {

                $this->payrollsModel->update(decrypt($p->id), [
                    'isAmbil' => intval($p->isChecked)
                ]);
            }
            return response()->setJSON([
                'message' => "ambil gaji sukses",
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
