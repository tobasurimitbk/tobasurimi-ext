<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;

class PembayaranPOImport extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function pembayaranPOImport()
    {
        return view('Pembayaran/pembayaranPOImport/index');
    }

    public function createPembayaranPOImport()
    {   
        $data = [];
        
        return view('Pembayaran/pembayaranPOImport/form', $data);
    }

    public function getByIdPembayaranPOImport($id = null)
    {   
        $data = [];

        if (!empty($id)) {
            $responsePembayaranPOImport = curl_request("GET", "/buktiPembayaran/$id", $this->token);
            $dataPembayaranPOImport = [];
            if ($responsePembayaranPOImport["code"] === 200) {
                $dataPembayaranPOImport = json_decode($responsePembayaranPOImport["body"])->data;
            }
            $data["dataPembayaranPOImport"] = $dataPembayaranPOImport;
        }

        return view('Purchase/terimaFakturImport/form', $data);
    }

    public function allPembayaranPOImport()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $response = curl_request("GET", "/buktiPembayaran", $this->token, $payload);
        $dataPembayaranPOImport = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataPembayaranPOImport, [
                    "no" => $no++,
                    "id" => $data->id,
                    "payment_no" => $data->payment_no,
                    "multiple_faktur_no" => $data->multiple_faktur_no,
                    "nominal_faktur" => $data->nominal_faktur,
                    "payment_date" => $data->payment_date,
                    "createdBy" => ""
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataPembayaranPOImport,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }
    
    public function savePembayaranPOImport()
    {
        try{
        $rules = [
            "nominal_faktur" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $payload = json_encode([
                "multiple_faktur_id" => json_decode($this->request->getPost("multiple_faktur_id")),
                "multiple_faktur_no" => json_decode($this->request->getPost("multiple_faktur_no")),
                "nominal_faktur" => formatter($this->request->getPost("nominal_faktur"), "CURR_TO_INT"),
                "payment_type" => "IMPORT"
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);
            
            $response = curl_request("POST", "/buktiPembayaran", $this->token, $payload);

            if ($response["code"] === 201) {
                $data = [
                    "id" => "",
                    "status"            => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Disimpan';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Data Gagal Disimpan",
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updatePembayaranPOImport()
    {
        try{
        $rules = [
            "nominal_faktur" => [
                "rules" => "required"
            ]
        ];

        if ($this->validate($rules)) {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "multiple_faktur_id" => json_decode($this->request->getPost("multiple_faktur_id")),
                "multiple_faktur_no" => json_decode($this->request->getPost("multiple_faktur_no")),
                "nominal_faktur" => formatter($this->request->getPost("nominal_faktur"), "CURR_TO_INT"),
                "payment_type" => "IMPORT"
            ]);

            // $data = [
            //     "status"            => false,
            //     "message"    => $payload,
            //     "payload"   => $payload,
            //     'token' => csrf_hash()
            // ];
            // echo json_encode($data);

            $response = curl_request("PATCH", "/buktiPembayaran/$id", $this->token, $payload);

            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }
        } else {
            $data = [
                "status"            => false,
                "message"    => "Data Gagal Diubah",
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deletePembayaranPOImport()
    {
        try{
        $id = $this->request->getPost("id");

        if (!empty($id)) {
            $response = curl_request("DELETE", "/buktiPembayaran/$id", $this->token);
            if ($response["code"] === 200) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Dihapus';
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
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }
}

?>
    