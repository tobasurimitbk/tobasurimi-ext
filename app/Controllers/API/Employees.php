<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\EmployeesModel;
use App\Models\AttendancesUnitModel;

class Employees extends BaseController
{
    use ResponseTrait;
    protected $EmployeesModel;
    protected $this_company_id;
    protected $AttendancesUnitModel;
    protected $attendances_id;
    protected $ip;
    protected $unit_key;

    public function __construct()
    {
        $this->EmployeesModel = new EmployeesModel();
        $this->AttendancesUnitModel = new AttendancesUnitModel();
        $this->this_company_id = session()->get("login")->this_company_id;
    }



    public function sync_employee_to_master()
    {
        $res_attendances = $this->AttendancesUnitModel->getByCompany_id_and_master($this->this_company_id);

        $this->attendances_id = $res_attendances[0]["id"];
        $this->ip = $res_attendances[0]["ip"];
        $this->unit_key = $res_attendances[0]["unit_key"];

        //$rfinger = $this->get_data_finger();

        $res = $this->EmployeesModel->getEmployeesNotSyncAttendances($this->this_company_id);
        for ($i = 0; $i < count($res); $i++) {
            $this->add_employee($res[$i]["id"], $res[$i]["name"]);
        }
    }

    public function Parse_Data($data, $p1, $p2)
    {
        $data = " " . $data;
        $hasil = "";
        $awal = strpos($data, $p1);
        if ($awal != "") {
            $akhir = strpos(strstr($data, $p1), $p2);
            if ($akhir != "") {
                $hasil = substr($data, $awal + strlen($p1), $akhir - strlen($p1));
            }
        }
        return $hasil;
    }
    public function get_data_finger()
    {
        $Connect = fsockopen($this->ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<GetAllUserInfo><ArgComKey xsi:type=\"xsd:integer\">" . $this->unit_key . "</ArgComKey></GetAllUserInfo>";
            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);
            $buffer = "";
            while ($Response = fgets($Connect, 1024)) {
                $buffer = $buffer . $Response;
            }
        } else echo "Koneksi Gagal";

        //include("parse.php");
        $buffer = $this->Parse_Data($buffer, "<GetAllUserInfoResponse>", "</GetAllUserInfoResponse>");
        $buffer = explode("\r\n", $buffer);
        $arr = [];
        for ($a = 0; $a < count($buffer); $a++) {
            $data = $this->Parse_Data($buffer[$a], "<Row>", "</Row>");
            $PIN = $this->Parse_Data($data, "<PIN2>", "</PIN2>");
            $DateTime = $this->Parse_Data($data, "<Name>", "</Name>");
            $Verified = $this->Parse_Data($data, "<Password>", "</Password>");
            $Group = $this->Parse_Data($data, "<Group>", "</Group>");
            $Status = $this->Parse_Data($data, "<Privilege>", "</Privilege>");

            array_push($arr, array("id" => $PIN, "name" => $DateTime));
        }
        return $arr;
    }

    public function add_employee($id, $nama)
    {
        $Connect = fsockopen($this->ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">" . $this->unit_key . "</ArgComKey><Arg><PIN>" . $id . "</PIN><Name>" . $nama . "</Name></Arg></SetUserInfo>";
            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);
            $buffer = "";
            while ($Response = fgets($Connect, 1024)) {
                $buffer = $buffer . $Response;
            }
        } else echo "Koneksi Gagal";

        $buffer = $this->Parse_Data($buffer, "<Information>", "</Information>");
        echo "<B>Result:</B><BR>";
        echo $buffer;
    }
}
