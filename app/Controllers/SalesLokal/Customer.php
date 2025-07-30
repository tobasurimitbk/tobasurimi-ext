<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CustomerModel;
use App\Models\EmployeesModel;
use App\Models\MetadataModel;
use App\Models\ProvincesModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Customer extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $ProvincesModel;
    protected $BanksModel;
    protected $soInvModel;
    protected $countryModel;
    protected $CustomerModel;
    protected $employeeModel;
    protected $metadataModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->ProvincesModel = new ProvincesModel();
        $this->BanksModel = new BanksModel();
        $this->CustomerModel = new CustomerModel();
        $this->employeeModel = new EmployeesModel();
        $this->metadataModel = new MetadataModel();
    }

    public function index()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataBanks = $this->BanksModel->search_list(array(), 'name');

        $condition = [
            'jabatan_name' => "MARKETING LOKAL"
        ];

        $sales = $this->employeeModel->getEmployeesComplete($condition);

        $data = [
            "dataProvinces" => $dataProvinces,
            "dataBanks" => $dataBanks,
            "dataSales" => $sales,
        ];

        return view('SalesLokal/Customer/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        if ($this->is_admin == '1') {
            $condition = [
                'tipe_customer' => $this->request->getGet('customerType'),
                'customers.deletedAt' => null,
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                'tipe_customer' => $this->request->getGet('customerType'),
                'customers.deletedAt' => null,
                'customers.user_id' => session()->get('login')->user_id
            ];
        }

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $customerData = $this->CustomerModel->getListCustomerDetail($condition, $addCondition, $limit, $offset);

        $dataCustomer = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        // var_dump($customerData['data']);
        // exit;

        foreach ($customerData['data'] as $data) {
            $termin = "-";
            if ($data->termin == "0" || $data->termin == null) {
                $termin = "-";
            } else {
                $termin = $this->metadataModel->find($data->termin)['value'];
            }

            array_push($dataCustomer, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "kode"          => $data->kode,
                "namaSales"     => $data->namaSales,
                "name"          => $data->name,
                "phone"         => $data->phone,
                "contact_person" => $data->contact_person,
                "saldo"         => number_format(floatval($data->saldo)),
                "currencyName"  => $data->currencyName,
                "countryName"   => $data->countryName,
                "address"       => $data->address,
                "termin"        => $termin,
                "piutang"         => number_format(floatval($data->piutang)),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $customerData['totalData'],
            "recordsFiltered"   => $customerData['totalFilteredData'],
            "data"              => $dataCustomer,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function exportExcel()
    {
        $condition = [
            'tipe_customer' => $this->request->getGet('customerType'),
            'customers.deletedAt' => null,
        ];

        if ($this->is_admin == '0') {
            $condition['customers.user_id'] = session()->get('login')->user_id;
        }

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
        ];

        // Ambil semua data tanpa pagination
        $customerData = $this->CustomerModel->getListCustomerDetail($condition, $addCondition, null, null);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode');
        $sheet->setCellValue('C1', 'Nama Sales');
        $sheet->setCellValue('D1', 'Nama Customer');
        $sheet->setCellValue('E1', 'Phone');
        $sheet->setCellValue('F1', 'Contact Person');
        $sheet->setCellValue('G1', 'Saldo');
        $sheet->setCellValue('H1', 'Currency');
        $sheet->setCellValue('I1', 'Country');
        $sheet->setCellValue('J1', 'Alamat');
        $sheet->setCellValue('K1', 'Termin');
        $sheet->setCellValue('L1', 'Piutang');

        $row = 2;
        $no = 1;

        foreach ($customerData['data'] as $data) {
            $termin = "-";
            if ($data->termin != "0" && $data->termin != null) {
                $termin = $this->metadataModel->find($data->termin)['value'];
            }

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $data->kode);
            $sheet->setCellValue("C{$row}", $data->namaSales);
            $sheet->setCellValue("D{$row}", $data->name);
            $sheet->setCellValue("E{$row}", $data->phone);
            $sheet->setCellValue("F{$row}", $data->contact_person);
            $sheet->setCellValue("G{$row}", number_format(floatval($data->saldo)));
            $sheet->setCellValue("H{$row}", $data->currencyName);
            $sheet->setCellValue("I{$row}", $data->countryName);
            $sheet->setCellValue("J{$row}", $data->address);
            $sheet->setCellValue("K{$row}", $termin);
            $sheet->setCellValue("L{$row}", number_format(floatval($data->piutang)));

            $row++;
        }

        // Set response untuk download file
        $filename = 'Export-Customer-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
