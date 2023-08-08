<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\AttendancesLogModel;
use App\Models\AttendancesUnitModel;

class Attendances extends BaseController
{
    use ResponseTrait;
    protected $AttendancesLogModel;
    protected $this_company_id;
    protected $AttendancesUnitModel;
    protected $attendances_id;
    protected $ip;
    protected $unit_key;

    public function __construct()
    {
        $this->AttendancesLogModel = new AttendancesLogModel();
        $this->AttendancesUnitModel = new AttendancesUnitModel();
        $this->this_company_id = session()->get("login")->this_company_id;
    }



    public function sync_attendance()
    {
        $res_unit = $this->AttendancesUnitModel->getByCompany_id($this->this_company_id);

        for ($i = 0; $i < count($res_unit); $i++) {
            $this->attendances_id = $res_unit[0]["id"];
            $this->ip = $res_unit[0]["ip"];
            $this->unit_key = $res_unit[0]["unit_key"];

            $res = $this->get_data_finger();

            for ($j = 0; $j < count($res); $j++) {
                $res_log = $this->AttendancesLogModel->get_by_company_employee_unit_date($this->this_company_id, $res[$j]["id"], $res_unit[$i]["id"], $res[$j]["date"]);

                if (count($res_log) == 0) {
                    $values = array(
                        "company_id"    => $this->this_company_id,
                        "employees_id"  => $res[$j]["id"],
                        "attendances_unit_id"   => $res_unit[$i]["id"],
                        "date_create"   => $res[$j]["date"]
                    );
                    $this->AttendancesLogModel->insert($values);
                }
            }
        }

        echo "done";
        exit;
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
            $soap_request = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">" . $this->unit_key . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
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
        $buffer = $this->Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
        $buffer = explode("\r\n", $buffer);
        $arr = [];
        for ($a = 0; $a < count($buffer); $a++) {
            $data = $this->Parse_Data($buffer[$a], "<Row>", "</Row>");
            $PIN = $this->Parse_Data($data, "<PIN>", "</PIN>");
            $DateTime = $this->Parse_Data($data, "<DateTime>", "</DateTime>");
            $Verified = $this->Parse_Data($data, "<Verified>", "</Verified>");
            $Status = $this->Parse_Data($data, "<Status>", "</Status>");
            if ($PIN != "") {
                array_push($arr, array("id" => $PIN, "date" => $DateTime));
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
