<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\HROutsourcingCompanyModel;
use App\Models\HROutsourcingEmployeeModel;
use App\Models\HROutsourcingSallaryPaymentModel;

class SallaryPayment extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $hrOutsourcingCompanyModel;
    protected $hrOutsourcingEmployeeModel;
    protected $hrOutsourcingSallaryPaymentModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->hrOutsourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->hrOutsourcingEmployeeModel = new HROutsourcingEmployeeModel();
        $this->hrOutsourcingSallaryPaymentModel = new HROutsourcingSallaryPaymentModel();
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

    public function all()
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
            'hr_outsourcing_company.company_id' => $this->this_company_id,
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $employee = $this->hrOutsourcingSallaryPaymentModel->getList($condition, $addCondition, $limit, $offset);
        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($employee['data'] as $data) {
            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "department" => $data->department,
                "company" => $data->company,
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

    public function detail($id)
    {
        $id = decrypt($id);
        $data = [
            'departement' => $this->divisiModel->where('company_id', $this->this_company_id)->select('id, divisi')->findAll(),
            'data' => $this->hrOutsourcingSallaryPaymentModel->find($id)
        ];
        return view('HROutsourcing/sallary-payment/form', $data);
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
        if (!$this->validate([
            'departemen' => 'required',
            'company' => 'required',
            'employee_data' => 'required'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
                csrf_token() => csrf_hash()
            ]);
        }

        try {
            // Get the JSON data from the request
            $employeeData = json_decode($this->request->getPost('employee_data'), true);
            
            // Validate the JSON data
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON data format');
            }

            $data = [
                'divisi_id' => $this->request->getPost('departemen'),
                'company_id' => $this->request->getPost('company'),
                'payment_data' => $this->request->getPost('employee_data'), // Store the JSON string directly
            ];

            // Insert the data
            $insertId =  $this->hrOutsourcingSallaryPaymentModel->insert($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'id' => $insertId, // Return the ID of the newly created record
                csrf_token() => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                csrf_token() => csrf_hash()
            ]);
        }
    }

    public function update($id)
    {
        if (!$this->validate([
            'departemen' => 'required',
            'company' => 'required',
            'employee_data' => 'required'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
                csrf_token() => csrf_hash()
            ]);
        }

        // $id = $this->request->getPost('id');
        
        try {
            // Get the JSON data from the request
            $employeeData = json_decode($this->request->getPost('employee_data'), true);
            
            // Validate the JSON data
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON data format');
            }

            // Prepare update data
            $updateData = [
                'divisi_id' => $this->request->getPost('departemen'),
                'company_id' => $this->request->getPost('company'),
                'payment_data' => $this->request->getPost('employee_data'),
                'updatedAt' => date('Y-m-d H:i:s') // Update timestamp
            ];

            // Update the record
            $this->hrOutsourcingSallaryPaymentModel->update($id, $updateData);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil diperbarui',
                'id' => $id,
                csrf_token() => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
                csrf_token() => csrf_hash()
            ]);
        }
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $this->hrOutsourcingSallaryPaymentModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Sallary Payment Outsourcing Berhasil Dihapus",
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
        $employee = $this->hrOutsourcingEmployeeModel->where('company_id', $company_id)->select('id, nama, badge, tanggal_masuk_kerja')->findAll();

        return response()->setJSON([
            'status' => true,
            'data' => $employee,
            'message' => "Employee Outsourcing Berhasil Di GET",
            'token' => csrf_hash()
        ]);
    }

}
