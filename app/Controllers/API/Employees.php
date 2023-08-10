<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\EmployeesModel;
use App\Models\AttendancesUnitModel;
use App\Models\EmployeesFingerModel;

class Employees extends BaseController
{
    use ResponseTrait;
    protected $EmployeesModel;
    protected $EmployeesFingerModel;
    protected $AttendancesUnitModel;
    protected $attendances_id;
    protected $ip;
    protected $unit_key;

    public function __construct()
    {
        $this->EmployeesModel = new EmployeesModel();
        $this->AttendancesUnitModel = new AttendancesUnitModel();
        $this->EmployeesFingerModel = new EmployeesFingerModel();
    }



    public function sync_employee_to_master()
    {
        $res_unit = $this->AttendancesUnitModel->getByMaster(1);

        for ($i = 0; $i < count($res_unit); $i++) {
            $this->attendances_id = $res_unit[$i]["id"];
            $this->ip = $res_unit[$i]["ip"];
            $this->unit_key = $res_unit[$i]["unit_key"];
            $res = $this->EmployeesModel->getEmployeesNotSyncAttendances($res_unit[$i]["company_id"]);

            for ($j = 0; $j < count($res); $j++) {
                $this->add_employee($res[$j]["id"], $res[$j]["name"]);
                $resfinger = $this->get_data_finger($res[$j]["id"]);
                $total_finger = count($resfinger);
                $total = $this->EmployeesFingerModel->getTotalByEmployeesId($res[$j]["id"]);
                echo "TOTAL: " . $total_finger . " = " . $total . "\n";
                if ($total_finger != $total) {
                    //$this->EmployeesFingerModel->delete_by_EmployeesId($res[$j]["id"]);
                    //$this->EmployeesFingerModel->delete("employees_id", $res[$j]["id"]);
                    $this->EmployeesFingerModel->where("employees_id", $res[$j]["id"])->delete();

                    for ($k = 0; $k < count($resfinger); $k++) {
                        $values = [
                            "employees_id"  => $res[$j]["id"],
                            "finger"        => $resfinger[$k]["data"]
                        ];
                        $this->EmployeesFingerModel->insert($values);
                    }
                }
            }
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
    public function get_data_finger($user_id)
    {
        $Connect = fsockopen($this->ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<GetUserTemplate><ArgComKey Xsi:type=\"xsd:integer\">" . $this->unit_key . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">" . $user_id . "</PIN><FingerID xsi:type=\"xsd:integer\">ALL</FingerID></Arg></GetUserTemplate>";
            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);
            $buffer = "";
            //while($Response=fgets($Connect, 1024)){
            while ($Response = fgets($Connect, 4096)) {
                $buffer = $buffer . $Response;
            }
        } else echo "Koneksi Gagal";

        //include("parse.php");
        $buffer = $this->Parse_Data($buffer, "<GetUserTemplateResponse>", "</GetUserTemplateResponse>");
        $buffer = explode("\r\n", $buffer);
        $arr = [];
        for ($a = 0; $a < count($buffer); $a++) {
            $data = $this->Parse_Data($buffer[$a], "<Row>", "</Row>");
            $PIN = $this->Parse_Data($data, "<PIN>", "</PIN>");
            $FingerID = $this->Parse_Data($data, "<FingerID>", "</FingerID>");
            $Template = $this->Parse_Data($data, "<Template>", "</Template>");

            if ($PIN != '') {
                array_push($arr, array("id" => $PIN, "data" => $Template));
            }
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
