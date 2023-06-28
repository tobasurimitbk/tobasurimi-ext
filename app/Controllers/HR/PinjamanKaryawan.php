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
        return view('hr/pinjamanKaryawan/form');
    }

    public function create()
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
            $payload = json_encode([
                "nama_karyawan" => $this->request->getPost("nama_karyawan"),
                "tanggal_peminjaman" => $this->request->getPost("tanggal_peminjaman"),
                "total_pinjaman" => $this->request->getPost("total_pinjaman"),
                "termin_pembayaran" => $this->request->getPost("termin_pembayaran"),
            ]);

            $response = curl_request("POST", "/employeeLoan", $this->token, $payload);

            if ($response["code"] === 201) {
                $data = [
                    "id" => json_decode($response["body"])->createdId,
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
        return;
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
