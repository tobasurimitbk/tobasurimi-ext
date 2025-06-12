<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\HROutsourcingCompanyModel;
use App\Models\HROutsourcingEmployeeModel;

class SallaryPayment extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $hrOutsourcingCompanyModel;
    protected $hrOutsourcingEmployeeModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->hrOutsourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->hrOutsourcingEmployeeModel = new HROutsourcingEmployeeModel();
    }


    public function index() {

         return view('HROutsourcing/sallary-payment/index');
    }

    public function create()
    {
        $data = [
            'departement' => $this->divisiModel->where('company_id', $this->this_company_id)->select('id, divisi')->findAll(),
        ];

        return view('HROutsourcing/sallary-payment/form', $data);
    }

    public function getAllEmployeeByCompany()
    {
        $payload = [
            "pageSize"         => $this->request->getVar("length"),
            "currentPage"      => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort"             => $this->request->getVar("sort"),
            "sortType"         => $this->request->getVar("sortType"),
            "company_id"       => $this->this_company_id,
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
        ];

        $condition = [
            'hr_outsourcing_employee.company_id' => $this->request->getVar('company_id'),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $employee = $this->hrOutsourcingEmployeeModel->getList($condition, $addCondition, $limit, $offset);
        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($employee['data'] as $data) {
            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "nama" => $data->nama,
                "tanggal_masuk_kerja" => $data->tanggal_masuk_kerja,
                "badge" => $data->badge,
                "total" => 0,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $employee['totalData'],
            "recordsFiltered"   => $employee['totalFilteredData'],
            "data"              => $dataSPP,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function generateKode()
    {
        $companyId = $this->request->getVar('company_id');
        $month = date('m'); // Bulan saat ini (format: 01-12)
        $year = date('Y'); // Tahun saat ini (format: 2023)
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d'))); // Tanggal terakhir bulan ini

        $lastStr = convertBulanToAngkaRomawi($month) . '/' . $year; // Format: III/2023

        // Ambil hr_outsourcing_employee terakhir di bulan & tahun ini
        $builder = $this->hrOutsourcingEmployeeModel->asArray()->select('badge')
            ->orderBy('badge', "DESC")
            ->where('company_id', $companyId)
            ->where('createdAt >=', $year . "-" . $month . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59")
            ->first();

        $badge = 'KAROSRC'; // badge awal: PJR
        $lastNumber = 1; // Nomor awal: 1

        if ($builder != null && isset($builder['badge'])) {
            $explode = explode('/', $builder['badge']); // Pecah hr_outsourcing_employee menjadi array

            // Pastikan format hr_outsourcing_employee sesuai: PJR/X/2023/00001
            if (count($explode) == 4) {
                $numberStr = $explode[3]; // Ambil bagian nomor (00001)
                $number = intval($numberStr); // Konversi ke integer
                if ($number >= $lastNumber) {
                    $lastNumber = $number + 1; // Increment nomor terakhir
                }
            }
        }

        $formattedlastNumber = sprintf("%05d", $lastNumber); // Format nomor menjadi 5 digit (00001)
        $generatedNo = $badge . '/' . $lastStr . '/' . $formattedlastNumber; // Gabungkan semua bagian

        return json_encode($generatedNo);
    }

    public function store()
    {
        $companyId = $this->request->getVar('company_id');
        $badge = $this->request->getVar('badge_karyawan');
        $nama = $this->request->getVar('nama');
        $tanggal_masuk_kerja = $this->request->getVar('tanggal_masuk_kerja');

        $this->hrOutsourcingEmployeeModel->insert([
            'company_id' => $companyId,
            'badge' => $badge,
            'nama' => $nama,
            'tanggal_masuk_kerja' => $tanggal_masuk_kerja
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Employee Outsourcing Berhasil Disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $companyId = $this->request->getVar('company_id');
        $badge = $this->request->getVar('badge_karyawan');
        $nama = $this->request->getVar('nama');
        $tanggal_masuk_kerja = $this->request->getVar('tanggal_masuk_kerja');

        $this->hrOutsourcingEmployeeModel->update($id, [
            'company_id' => $companyId,
            'badge' => $badge,
            'nama' => $nama,
            'tanggal_masuk_kerja' => $tanggal_masuk_kerja
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Employee Outsourcing Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function destroy()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->hrOutsourcingEmployeeModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Employee Outsourcing Berhasil Dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function getHrCompanyOutSourcing() {
        $departemen_id = $this->request->getVar('departemen_id');
        $company = $this->hrOutsourcingCompanyModel->where('divisi_id', $departemen_id)->select('id, name')->findAll();

        return response()->setJSON([
            'status' => true,
            'data' => $company,
            'message' => "Company Outsourcing Berhasil Di GET",
            'token' => csrf_hash()
        ]);
    }

    public function getHrEmployeeOutSourcing() {
        $company_id = $this->request->getVar('company_id');
        $employee = $this->hrOutsourcingEmployeeModel->where('company_id', $company_id)->select('id, nama')->findAll();

        return response()->setJSON([
            'status' => true,
            'data' => $employee,
            'message' => "Employee Outsourcing Berhasil Di GET",
            'token' => csrf_hash()
        ]);
    }

}
