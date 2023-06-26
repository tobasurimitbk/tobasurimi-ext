<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;

class Attendance extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function attendance()
    {
        $payload = [
            "idCompany" => $this->this_company_id
        ];

        $res_employee = curl_request("GET", "/employees", $this->token, $payload);
        $dataEmployee = [];
        if ($res_employee["code"] === 200) {
            $dataEmployee = json_decode($res_employee["body"])->data;
        }

        $data = [
            "dataEmployee" => $dataEmployee,
        ];

        //return view('employee/index');
        return view('hr/attendance/index', $data);

        /*
        // Get Kategori
        $responseKategori = curl_request("GET", "/metadata/all?name=kategori_barang", $this->token);

        $dataKategori = [];

        $data = [
            "dataKategori" => $dataKategori
        ];

        return view('barang/index', $data);
        */
    }

    public function ListAttendance()
    {
        return view('hr/attendance/list-attendance');
    }

    public function SaveAttendance()
    {
        $img = $this->request->getVar("pic");
        //$img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $payload = json_encode([
            "company_id" => formatter($this->this_company_id, "STR_TO_INT"),
            "employee_id"   => formatter($this->request->getVar("employee_id"), "STR_TO_INT"),
            "checkin"   => date("Y-m-d H:i:s"),
            "status"       => "HADIR",
            "checkin_image" => $img,
        ]);
        $res_attendance = curl_request("POST", "/attendance", $this->token, $payload);
        $dataAttendance = [];
        print_r($res_attendance);
        if ($res_attendance["code"] === 200) {
            $dataAttendance = json_decode($res_attendance["body"])->data;
        }

        $data1 = [
            "res_attendance" => $res_attendance,
        ];



        $file = uniqid() . '.png';
        $success = file_put_contents($file, $data);

        $message = 'Data Berhasil Disimpan';
        $data = [
            "status"            => false,
            "message"    => $message,
            "payload"   => "",
            'token' => csrf_hash()
        ];
        echo json_encode($data1);
        return;
    }

    public function get_employee_by_company($employee_id)
    {
        $dataEmployee = [];

        $responseEmployee = curl_request("GET", "/attendance/$employee_id", $this->token);
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

        $data = [
            "data" => $dataEmployee
        ];

        echo json_encode($data);
        return;
    }
}
