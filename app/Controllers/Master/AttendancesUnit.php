<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\AttendancesUnitModel;
use App\Models\EmployeesModel;
use App\Models\EmployeesFingerModel;

class AttendancesUnit extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $AttendancesUnitModel;
    protected $EmployeesModel;
    protected $EmployeesFingerModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->AttendancesUnitModel = new AttendancesUnitModel();
        $this->EmployeesModel = new EmployeesModel();
        $this->EmployeesFingerModel = new EmployeesFingerModel();
    }

    public function ListData()
    {
        return view('Master/AttendancesUnit/index');
    }

    public function AllData()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "company_id"    => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $res = $this->AttendancesUnitModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "ip"                    => $data->ip,
                "unit_key"              => $data->unit_key,
                "name"                  => $data->name,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveData()
    {
        $ip = $this->request->getPost("ip");

        $company_id = $this->this_company_id;

        $getIp = $this->AttendancesUnitModel->select('id')
            ->where('ip', $ip)
            ->where('company_id', $company_id)
            ->where('deletedAt', null)
            ->findAll();


        //cek ip duplikatnya sama yang ada? jika ada is_unique, jika tidak ada lolosin
        if (!empty($getIp)) {
            $rule_is_unique = 'required|is_unique[attendances_unit.ip]';
        } else {
            $rule_is_unique = 'required';
        }

        try {
            $rules = [
                "nama" => [
                    "rules" => "required"
                ],
                "ip" => [
                    "rules" => $rule_is_unique,
                    'errors' => [
                        'required' => 'IP harus diisi',
                        'is_unique' => 'IP sudah ada'
                    ]
                ],
                "unit_key" => [
                    "rules" => "required"
                ]


            ];

            if ($this->validate($rules)) {
                if ($this->request->getPost("master") == 1) {
                    $this->AttendancesUnitModel->set('master', 0)->where('company_id', $this->this_company_id)->update();
                }

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("nama"),
                    "ip" => $this->request->getPost("ip"),
                    "unit_key" => $this->request->getPost("unit_key"),
                    "master" => (empty($this->request->getPost("master"))) ? '0' : '1'
                ];
                if ($this->AttendancesUnitModel->insert($values)) {
                    $data = [
                        "status"    => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $errors = '';
                foreach ($this->validator->getErrors() as $key => $row) {
                    $errors .= $row . '. ';
                }
                $data = [
                    "status"     => false,
                    "message"    => $errors,
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateData()
    {
        $id = $this->request->getPost("id");
        $company_id = $this->this_company_id;
        $ip = $this->request->getPost("ip");

        $getIpNull = $this->AttendancesUnitModel->select('id')
            ->where('ip', $ip)
            ->where('company_id', $company_id)
            ->where('deletedAt', null)
            ->where('id !=', $id)
            ->findAll();

        //cek name duplikatnya sama yang ada? jika ada is_unique, jika tidak ada lolosin
        if (!empty($getIpNull)) {

            //cek name yg diedit masih sama dengan yg di ID?
            $getIpNow = $this->AttendancesUnitModel->select('id')
                ->where('ip', $ip)
                ->where('company_id', $company_id)
                ->where('deletedAt', null)
                ->where('id', $id)
                ->first();

            //jika sama
            if (!empty($getIpNow)) {
                $rule_is_unique = 'required';
            } else {
                $rule_is_unique = 'required|is_unique[attendances_unit.ip]';
            }
        } else {
            $rule_is_unique = 'required';
        }

        try {
            $rules = [
                "nama" => [
                    "rules" => "required"
                ],
                "ip" => [
                    "rules" => $rule_is_unique,
                    'errors' => [
                        'required' => 'IP harus diisi',
                        'is_unique' => 'IP sudah ada'
                    ]
                ],
                "unit_key" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                if ($this->request->getPost("master") == 1) {
                    $this->AttendancesUnitModel->set('master', 0)->where('company_id', $this->this_company_id)->update();
                }

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $this->request->getPost("nama"),
                    "ip" => $this->request->getPost("ip"),
                    "unit_key" => $this->request->getPost("unit_key"),
                    "master" => (empty($this->request->getPost("master"))) ? '0' : '1'
                ];

                if ($this->AttendancesUnitModel->update($id, $values)) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $errors = '';
                foreach ($this->validator->getErrors() as $key => $row) {
                    $errors .= $row . '. ';
                }
                $data = [
                    "status"     => false,
                    "message"    => $errors,
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function getById($id = null)
    {
        if (!empty($id)) {
            $res = $this->AttendancesUnitModel->getById($id);
            if ($res) {
                $data = [
                    "status"  => true,
                    "data"  => $res,
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditemukan';
                $data = [
                    "status" => false,
                    "message"  => $message
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Tidak Ada Id"
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteData()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->AttendancesUnitModel->update($id, $values)) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Dihapus';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function dropdownData()
    {
        $dataDivisi = $this->AttendancesUnitModel->get_by_company_id($this->this_company_id);

        /*
        $responseDivisi = curl_request("GET", "/divisis/all", $this->token);
        $dataDivisi = [];
        if ($responseDivisi["code"] === 200) {
            $dataDivisi = json_decode($responseDivisi["body"])->data;
        }
        */

        $data = [
            "data" => $dataDivisi
        ];

        echo json_encode($data);
        return;
    }

    public function CopyToFinger()
    {
        $res_master = $this->AttendancesUnitModel->getByCompany_id_and_master($this->this_company_id, 1);
        $res_child = $this->AttendancesUnitModel->getByCompany_id_and_master($this->this_company_id, 0);
        $res_employees = $this->EmployeesModel->getEmployeesNotSyncAttendances2($this->this_company_id);

        //        $this->EmployeesModel = new EmployeesModel();
        //      $this->EmployeesFingerModel = new EmployeesFingerModel();
        for ($i = 0; $i < count($res_child); $i++) {
            for ($j = 0; $j < count($res_employees); $j++) {
                $this->delete_finger($res_employees[$j]["id"], $res_child[$i]["ip"], $res_child[$i]["unit_key"]);
                $this->delete_finger_user($res_employees[$j]["id"], $res_child[$i]["ip"], $res_child[$i]["unit_key"]);

                $this->insert_finger_user($res_employees[$j]["id"], $res_child[$i]["ip"], $res_child[$i]["unit_key"], $res_employees[$j]["name"]);

                $res_finger = $this->EmployeesFingerModel->getByEmployeesId($res_employees[$j]["id"]);

                for ($k = 0; $k < count($res_finger); $k++) {
                    $this->insert_finger_data($res_employees[$j]["id"], $res_child[$i]["ip"], $k, $res_child[$i]["unit_key"], $res_finger[$k]["finger"]);
                    //$this->insert_finger($res_employees[$j]["id"], $res_child[$i]["ip"], $res_child[$i]["unit_key"], $res_employees[$j]["name"]);
                }
            }
        }

        $data = [
            "status"            => true,
            "message"   => "Process Berhasil",
            "payload"   => "",
            'token' => csrf_hash()
        ];
        echo json_encode($data);
    }

    public function delete_finger($user_id, $ip, $unit_key)
    {
        $Connect = fsockopen($ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<DeleteTemplate><ArgComKey xsi:type=\"xsd:integer\">" . $unit_key . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">" . $user_id . "</PIN></Arg></DeleteTemplate>";

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
        //	echo $buffer;
        $buffer = $this->Parse_Data($buffer, "<DeleteTemplateResponse>", "</DeleteTemplateResponse>");
        $buffer = $this->Parse_Data($buffer, "<Information>", "</Information>");
        return;
    }

    public function delete_finger_user($user_id, $ip, $unit_key)
    {
        $Connect = fsockopen($ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<DeleteUser><ArgComKey xsi:type=\"xsd:integer\">" . $unit_key . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">" . $user_id . "</PIN></Arg></DeleteUser>";
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
        //echo $buffer;
        $buffer = $this->Parse_Data($buffer, "<DeleteUserResponse>", "</DeleteUserResponse>");
        $buffer = $this->Parse_Data($buffer, "<Information>", "</Information>");
        return;
    }

    public function insert_finger_user($user_id, $ip, $unit_key, $name)
    {
        $Connect = fsockopen($ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">" . $unit_key . "</ArgComKey><Arg><PIN>" . $user_id . "</PIN><Name>" . $name . "</Name></Arg></SetUserInfo>";
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
        //echo "<B>Result:</B><BR>";
        //echo $buffer;
        return;
    }

    public function insert_finger_data($user_id, $ip, $finger_id, $unit_key, $data_finger)
    {
        $Connect = fsockopen($ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<SetUserTemplate><ArgComKey xsi:type=\"xsd:integer\">" . $unit_key . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">" . $user_id . "</PIN><FingerID xsi:type=\"xsd:integer\">" . $finger_id . "</FingerID><Size>" . strlen($data_finger) . "</Size><Valid>1</Valid><Template>" . $data_finger . "</Template></Arg></SetUserTemplate>";
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

        //	echo $buffer;
        $buffer = $this->Parse_Data($buffer, "<SetUserTemplateResponse>", "</SetUserTemplateResponse>");
        $buffer = $this->Parse_Data($buffer, "<Information>", "</Information>");
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
}
