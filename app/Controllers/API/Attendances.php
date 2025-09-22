<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\AttendancesLogModel;
use App\Models\AttendancesUnitModel;
use App\Models\CompaniesModel;

class Attendances extends BaseController
{
    use ResponseTrait;
    protected $AttendancesLogModel;
    protected $AttendancesUnitModel;
    protected $CompaniesModel;
    protected $attendances_id;
    protected $ip;
    protected $unit_key;

    public function __construct()
    {
        $this->AttendancesLogModel = new AttendancesLogModel();
        $this->AttendancesUnitModel = new AttendancesUnitModel();
        $this->CompaniesModel = new CompaniesModel();
    }

    public function sync_attendance()
    {
        $res_company = $this->CompaniesModel->search_list(array('deletedAt' => NULL));
        for ($k = 0; $k < count($res_company); $k++) {
            $res_unit = $this->AttendancesUnitModel->getByCompany_id($res_company[$k]["id"]);
            for ($i = 0; $i < count($res_unit); $i++) {
                $this->attendances_id = $res_unit[$i]["id"];
                $this->ip = $res_unit[$i]["ip"];
                $this->unit_key = $res_unit[$i]["unit_key"];
                if (icmpPing($this->ip, 1)) {
                    // Ping Berhasil
                    $res = $this->get_data_finger();
                    for ($j = 0; $j < count($res); $j++) {
                        $res_log = $this->AttendancesLogModel->get_by_company_employee_unit_date($res_company[$k]["id"], $res[$j]["id"], $res_unit[$i]["id"], $res[$j]["date"]);
                        if (count($res_log) == 0) {
                            $values = array(
                                "company_id"    => $res_company[$k]["id"],
                                "employees_id"  => $res[$j]["id"],
                                "attendances_unit_id"   => $res_unit[$i]["id"],
                                "date_create"   => $res[$j]["date"]
                            );
                            $this->AttendancesLogModel->insert($values);
                        }
                    }
                } else {
                    //
                    echo "Ping Gagal IP : " . $this->ip;
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
