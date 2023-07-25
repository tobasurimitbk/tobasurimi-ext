<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;

use App\Models\SupplierModel;
use App\Models\LocalPOPaymentModel;
use App\Models\LocalPOInvSummaryModel;

class PembayaranPOLokal extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function pembayaranPOLokal()
    {
        return view('Pembayaran/pembayaranPOLokal/index');
    }

    public function createPembayaranPOLokal()
    {   
        $supplierModel = new SupplierModel();

        $supplierList = $supplierModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('kategori', 'LOKAL')
            ->findAll();

        $data = [
            "suppliers"=> $supplierList
        ];
        
        return view('Pembayaran/pembayaranPOLokal/form', $data);
    }

    public function getByIdPembayaranPOLokal($id)
    {   
        $data = [];

        $responsePembayaranPOLokal = curl_request("GET", "/localPOPayment/$id", $this->token);
        $dataPembayaranPOLokal = [];
        if ($responsePembayaranPOLokal["code"] === 200) {
            $dataPembayaranPOLokal = json_decode($responsePembayaranPOLokal["body"])->data;
        }
        $selectedFaktur = array_column($dataPembayaranPOLokal->local_po_payment_details, 'local_po_inv_summary_id');

        $responseSupplier = curl_request("GET", "/suppliers/all?idCompany=$this->this_company_id&kategori=lokal", $this->token);
        $supplierList = [];
        if ($responseSupplier["code"] === 200) {
            $supplierList = json_decode($responseSupplier["body"])->data;
        }

        $summaryResponse = curl_request("GET", "/localPOInvSummary/supplier/$dataPembayaranPOLokal->supplier_id", $this->token);
        $summaryList = [];
        if ($summaryResponse["code"] === 200) {
            $summaryList = json_decode($summaryResponse["body"])->data;
        }

        $data = [
            'dataPembayaranPOLokal' => $dataPembayaranPOLokal,
            'suppliers'             => $supplierList,
            'summaryList'           => $summaryList,
            "selectedFaktur"        => $selectedFaktur
        ];

        return view('Pembayaran/pembayaranPOLokal/form', $data);
    }

    public function allPembayaranPOLokal()
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

        $response = curl_request("GET", "/localPOPayment", $this->token, $payload);
        $dataPembayaranPOLokal = [];
        $totalRecords = 0;

        if ($response["code"] === 200) {
            $body = json_decode($response["body"])->data;
            $totalRecords = json_decode($response["body"])->meta->totalData;

            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($body as $data) {
                array_push($dataPembayaranPOLokal, [
                    "no"                => $no++,
                    "id"                => $data->id,
                    "payment_no"        => $data->payment_no,
                    "due_date"          => $data->due_date,
                    "payment_date"      => $data->payment_date,
                    "payment_method"    => $data->payment_method,
                    "payment_status"    => $data->payment_status,
                    "amount"            => $data->amount
                ]);
            }
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $dataPembayaranPOLokal,
            "response" => $response,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }
    
    public function savePembayaranPOLokal()
    {
        try {
            $localPOPaymentModel = new LocalPOPaymentModel();
            $localPOInvSumModel = new LocalPOInvSummaryModel();
            
            $rules = [
                "supplier_id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "summary_id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "due_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "payment_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "payment_method" => [
                    "rules" => "required"
                ],
                "payment_status" => [
                    "rules" => "required|in_list[Unpaid,Paid]"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $summaryId = $this->request->getPost("summary_id");
            $summaryData = $localPOInvSumModel->asObject()
                ->find($summaryId);

            if (empty($summaryData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Rekap tidak ditemukan!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "supplier_id"               => (int)$this->request->getPost("supplier_id"),
                "local_po_inv_summary_id"   => $summaryId,
                "payment_no"                => $this->generatePaymentNo(),
                "amount"                    => $summaryData->total,
                "payment_date"              => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))),
                "due_date"                  => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))),
                "payment_method"            => $this->request->getPost("payment_method"),
                "payment_status"            => $this->request->getPost("payment_status"),
            ];

            $insertedId = $localPOPaymentModel->insert($data);

            $data = [
                "id"        => $insertedId,
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $data,
                'token'     => csrf_hash(),
                'code'      => 201
            ];
            echo json_encode($data);
            return;
        }
        catch(\Exception $e)
        {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updatePembayaranPOLokal()
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
                "payment_type" => "LOKAL"
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

    public function deletePembayaranPOLokal()
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

    private function generatePaymentNo()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();

        $month = idate('m');
        $year = date('Y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "PAY/$romanMonth/$year/";

        $lastData = $localPOPaymentModel->asObject()
            ->like('payment_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $paymentNo = "{$numberTemplate}0001";
        
        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->payment_no);
            $lastIncrement = (int)$exploded[3] + 1;

            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $paymentNo = $numberTemplate . $paddedNumber;
        }

        return $paymentNo;
    }
}

?>
    