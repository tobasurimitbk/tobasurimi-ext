<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Models\BarangMasterSortirModel;
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
    private $departmentIpFile = 'department_ip_data.json';

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
                "payment_date" => $data->payment_date,
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
                'payment_date' => $this->request->getPost('tanggal_pembayaran'),
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
                'payment_date' => $this->request->getPost('tanggal_pembayaran'),
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

    // ============================
    // DEPARTMENT IP CONFIGURATION
    // ============================
    
    // Fungsi untuk mendapatkan path file JSON
    private function getJsonFilePath() {
        return WRITEPATH . 'uploads/' . $this->departmentIpFile;
    }
    
    // Fungsi untuk menyimpan data department dan IP
    public function saveDepartmentIp() {
        $departmentId = $this->request->getPost('department_id');
        $departmentName = $this->request->getPost('department_name');
        $ipAddress = $this->request->getPost('ip_address');
        
        // Validasi input
        if (empty($departmentId) || empty($ipAddress)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department dan IP address harus diisi'
            ]);
        }
        
        // Validasi format IP address
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format IP address tidak valid'
            ]);
        }
        
        // Membaca data yang sudah ada
        $existingData = $this->readDepartmentIpData();
        
        // Mengecek apakah department sudah ada
        $found = false;
        foreach ($existingData as &$item) {
            if ($item['department_id'] == $departmentId) {
                $item['ip_address'] = $ipAddress;
                $found = true;
                break;
            }
        }
        
        // Jika department belum ada, tambahkan data baru
        if (!$found) {
            $existingData[] = [
                'department_id' => $departmentId,
                'department_name' => $departmentName,
                'ip_address' => $ipAddress
            ];
        }
        
        // Menyimpan data ke file JSON
        if ($this->writeDepartmentIpData($existingData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $existingData
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data'
            ]);
        }
    }
    
    // Fungsi untuk membaca data department dan IP
    public function getDepartmentIpData() {
        $data = $this->readDepartmentIpData();
        return $this->response->setJSON($data);
    }
    
    // Fungsi helper untuk membaca data dari file JSON
    private function readDepartmentIpData() {
        $filePath = $this->getJsonFilePath();
        
        if (!file_exists($filePath)) {
            // Jika file tidak ada, buat file dengan array kosong
            $this->writeDepartmentIpData([]);
            return [];
        }
        
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);
        
        return $data ?: [];
    }
    
    // Fungsi helper untuk menulis data ke file JSON
    private function writeDepartmentIpData($data) {
        $filePath = $this->getJsonFilePath();
        
        // Memastikan directory ada
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        return file_put_contents($filePath, $jsonData) !== false;
    }
    
    // Fungsi untuk mendapatkan IP berdasarkan department
    public function getIpByDepartment($departmentId = null) {
        if ($departmentId === null) {
            $departmentId = $this->request->getGet('department_id');
        }
        
        $data = $this->readDepartmentIpData();
        
        foreach ($data as $item) {
            if ($item['department_id'] == $departmentId) {
                return $this->response->setJSON([
                    'success' => true,
                    'ip_address' => $item['ip_address']
                ]);
            }
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'IP address tidak ditemukan untuk department ini'
        ]);
    }

    // Fungsi untuk halaman konfigurasi IP
    public function ipConfig()
    {
        $data = [
            'departement' => $this->divisiModel->where('company_id', $this->this_company_id)->select('id, divisi')->findAll(),
            'ipData' => $this->readDepartmentIpData()
        ];

        return view('HROutsourcing/sallary-payment/ip-config', $data);
    }


    public function push()
    {
        $ipAddress = $this->request->getPost('ip');
        
        $employeeModel = new HrOutsourcingEmployeeModel();
        $barangModel   = new BarangMasterSortirModel();
        $companyModel  = new HrOutsourcingCompanyModel();

        // TEST: Ambil hanya 2 record masing-masing
        $employees = $employeeModel->limit(2)->findAll();
        $barang = $barangModel->limit(2)->findAll();
        $companies = $companyModel->limit(2)->findAll();

        // Debug data
        log_message('debug', 'Sample Employee: ' . json_encode($employees[0] ?? []));
        log_message('debug', 'Sample Barang: ' . json_encode($barang[0] ?? []));
        log_message('debug', 'Sample Company: ' . json_encode($companies[0] ?? []));

        $payload = [
            "employees" => $employees,
            "barang"    => $barang,
            "companies" => $companies,
        ];

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->post("http://{$ipAddress}:8001/api/migrate-to-db", [
                "json" => $payload,
                "timeout" => 30,
                "headers" => [
                    "Content-Type" => "application/json",
                ]
            ]);

            return $this->response->setJSON([
                "status" => true,
                "message" => "Data berhasil dikirim",
                "go_response" => json_decode($response->getBody(), true)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                "status" => false,
                "message" => "Gagal push: " . $e->getMessage()
            ]);
        }
    }


    public function getData()
    {
        $companyId = $this->request->getPost('company_id');
        $tanggal = $this->request->getPost('tanggal');
        $departmentId = $this->request->getPost('department_id');

        // Validasi dasar
        if (!$companyId || !$tanggal || !$departmentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        // ==== STEP 1: Ambil IP berdasarkan department ====
        $ipData = $this->readDepartmentIpData();
        $deptIp = null;

        foreach ($ipData as $item) {
            if ($item['department_id'] == $departmentId) {
                $deptIp = $item['ip_address'];
                break;
            }
        }

        if (!$deptIp) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'IP Address tidak ditemukan untuk departemen ini'
            ]);
        }

        // ==== STEP 2: Request ke scale system ====
        $url = "http://{$deptIp}:8001/api/local-data?company_id={$companyId}&tanggal={$tanggal}";

        try {
            $client = \Config\Services::curlrequest([
                'timeout' => 10,
            ]);

            $response = $client->get($url);
            $json = json_decode($response->getBody(), true);

            return $this->response->setJSON([
                'success' => true,
                'data' => $json
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data dari scale system: ' . $e->getMessage()
            ]);
        }
    }


}