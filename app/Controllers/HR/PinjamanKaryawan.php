<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;

class PinjamanKaryawan extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function pinjamanKaryawan()
    {
        return view('hr/pinjamanKaryawan/index');
    }

    public function createView()
    {
        //Get Employee
        $responseEmployee = curl_request("GET", "/employees/selectOption", $this->token);

        $dataEmployee = [];
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

        $data = [
            "dataEmployee" => $dataEmployee,
        ];

        return view('hr/pinjamanKaryawan/form', $data);
    }

    public function allPinjamanKaryawan()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $response = curl_request("GET", "/employeeLoan", $this->token, $payload);
        $dataPinjamanKaryawan = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            foreach ($body as $data) {
                array_push($dataPinjamanKaryawan, [
                    "id" => $data->id,
                    "amount" => $data->amount,
                    "installment_month" => $data->installment_month,
                    "remaining_amount" => $data->remaining_amount,
                    "loan_date" => $data->loan_date,
                    "term" => $data->term,
                    "status" => $data->status,
                    "approve_by" => $data->approve_by,
                    "nip" => $data->nip,
                    "employeeName" => $data->employeeName,
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataPinjamanKaryawan,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function create()
    {
        $rules = [
            "nama_karyawan" => [
                "rules" => "required"
            ],
            "total_pinjaman" => [
                "rules" => "required"
            ],
            "termin_pembayaran" => [
                "rules" => "required"
            ],
        ];


        if ($this->validate($rules)) {
            $payload = json_encode([
                "employee_id" => $this->request->getPost("nama_karyawan"),
                "amount" => $this->request->getPost("total_pinjaman"),
                "term" => $this->request->getPost("termin_pembayaran"),
                "is_posted" => $this->request->getPost("is_posted"),
            ]);

            $response = curl_request("POST", "/employeeLoan", $this->token, $payload);

            if ($response["code"] === 201) {
                $data = [
                    "status"            => true,
                    "message"   => $response["message"],
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
        return;
    }

    public function getById($id = null)
    {
        //Get Employee
        $responseEmployee = curl_request("GET", "/employees/selectOption", $this->token);

        $dataEmployee = [];
        if ($responseEmployee["code"] === 200) {
            $dataEmployee = json_decode($responseEmployee["body"])->data;
        }

        $data = [
            "dataEmployee" => $dataEmployee,
        ];

        if (!empty($id)) {
            $responsePinjamanKaryawan = curl_request("GET", "/employeeLoan/$id", $this->token);
            $dataPinjamanKaryawan = [];
            if ($responsePinjamanKaryawan["code"] === 200) {
                $dataPinjamanKaryawan = json_decode($responsePinjamanKaryawan["body"])->data;
            }

            $data["data"] = $dataPinjamanKaryawan;
        }

        return view('hr/pinjamanKaryawan/form', $data);
    }

    public function update()
    {
        $rules = [
            "nama_karyawan" => [
                "rules" => "required"
            ],
            "tanggal_peminjaman" => [
                "rules" => "required"
            ],
            "total_pinjaman" => [
                "rules" => "required"
            ],
            "termin_pembayaran" => [
                "rules" => "required"
            ],
        ];

        if ($this->validate($rules)) {
            $id = $this->request->getPost("id");

            $payload = json_encode([
                "nama_karyawan" => $this->request->getPost("nama_karyawan"),
                "tanggal_peminjaman" => $this->request->getPost("tanggal_peminjaman"),
                "total_pinjaman" => $this->request->getPost("total_pinjaman"),
                "termin_pembayaran" => $this->request->getPost("termin_pembayaran"),
                "is_posted" => $this->request->getPost("is_posted"),
            ]);

            $response = curl_request("PATCH", "/employeeLoan/$id", $this->token, $payload);

            if ($response["code"] === 201) {
                $data = [
                    "id" => json_decode($response["body"])->createdId,
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash(),
                    'code' => $response["code"]
                ];
                echo json_encode($data);
            } else {
                $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
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
                "message"    => "Data Gagal Diubah",
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }
}
