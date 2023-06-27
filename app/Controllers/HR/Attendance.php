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
            "checkStatus"   => $this->request->getVar("state"),
            "checkin_image" => $img,
        ]);
        $response = curl_request("POST", "/attendance", $this->token, $payload);
        $dataAttendance = [];
        if ($response["code"] === 200) {
            $message = json_decode($response["body"]);
            $data = [
                "status"    => true,
                "message"   => $message->message,
                "payload"   => $payload,
                'token' => csrf_hash()
            ];
        } else {
            $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
            $data = [
                "status"            => false,
                "message"    => $message,
                "payload"   => $payload,
                'token' => csrf_hash()
            ];
        }
        echo json_encode($data);


        /*
        $file = uniqid() . '.png';
        $success = file_put_contents($file, $data);
*/
        return;
    }

    public function CheckPinEmployee()
    {
        $employee_id = $this->request->getVar("employee_id");
        $pin = $this->request->getVar("pin");

        $payload = json_encode([
            "employee_id"   => formatter($this->request->getVar("employee_id"), "STR_TO_INT"),
            "pin"   => $this->request->getVar("pin"),
        ]);

        $response = curl_request("POST", "/attendance/pinValidation", $this->token, $payload);

        if ($response["code"] === 200) {
            $message = json_decode($response["body"]);
            $data = [
                "status"    => true,
                "message"   => $message->message,
                "payload"   => $payload,
                'token' => csrf_hash()
            ];
        } else {
            $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
            $data = [
                "status"            => false,
                "message"    => $message,
                "payload"   => $payload,
                'token' => csrf_hash()
            ];
        }
        echo json_encode($data);


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
