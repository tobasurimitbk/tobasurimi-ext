<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;

use App\Models\ImportPOPaymentModel;
use App\Models\SupplierModel;
use App\Models\RMImportPOModel;
use App\Models\AMPurchaseOrderModel;

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
        $supplierModel = new SupplierModel();

        $supplierList = $supplierModel->asObject()
            ->where('kategori', 'IMPORT')
            ->findAll();

        $data = [
            // 'supplierList'=> $supplierList
        ];
        
        return view('Pembayaran/pembayaranPOImport/form', $data);
    }

    public function getByIdPembayaranPOImport($id)
    {   
        $importPOPaymentModel = new ImportPOPaymentModel();
        $supplierModel = new SupplierModel();

        $selectQry = "import_po_payments.*, 
                      DATE_FORMAT(import_po_payments.payment_date, '%d/%m/%Y') AS payment_date,
                      metadata.value AS currency";
        $paymentData = $importPOPaymentModel->asObject()
            ->select($selectQry)
            // ->where()
            ->join('metadata', 'metadata.id = import_po_payments.currency')
            ->find($id);

        $poType = $paymentData->po_type == 'BAKU' ? 'BAHAN BAKU' : 'BAHAN PENOLONG';

        $supplierCondition = [
            'kategori'  => 'IMPORT',
            'type'      => $poType,
            'company_id'=> $this->this_company_id
        ];
    
        $supplierList = $supplierModel->asObject()
            ->where($supplierCondition)
            ->findAll();

        $poData = $this->checkPO($paymentData->supplier_id, $paymentData->po_id, $paymentData->po_type);

        $poList = $this->getPOList($paymentData->supplier_id, $paymentData->po_type);

        $data = [
            'paymentData'   => $paymentData,
            'supplierList'  => $supplierList,
            'poData'        => $poData,
            'poList'        => $poList
        ];

        return view('Pembayaran/pembayaranPOImport/form', $data);
    }

    public function allPembayaranPOImport()
    {
        $importPOPaymentModel = new ImportPOPaymentModel();
        
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataPembayaranPOImport = [];

        $condition = [
            "suppliers.company_id"  => $this->this_company_id
        ];
        $addCondition = [
            "startDate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastDate"  => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $paymentList = $importPOPaymentModel->getPaymentList($condition, $addCondition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentList['data'] as $data) {
            array_push($dataPembayaranPOImport, [
                "no"                => $no++,
                "id"                => $data->id,
                "payment_no"        => $data->payment_no,
                "supplier_name"     => $data->supplier_name,
                "currency"          => $data->currency,
                "amount"            => $data->payment_amt,
                "payment_date"      => $data->payment_date
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $paymentList['totalData'],
            "recordsFiltered"   => $paymentList['totalFilteredData'],
            "data"              => $dataPembayaranPOImport,
            // "response"          => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }
    
    public function savePembayaranPOImport()
    {
        try{
            $supplierModel = new SupplierModel();
            $importPOPaymentModel = new ImportPOPaymentModel();

            $rules = [
                "payment_type" => [
                    "rules" => "required|in_list[DP,Pelunasan]"
                ],
                "po_type" => [
                    "rules" => "required|in_list[BAKU,PENOLONG]"
                ],
                "supplier_id" => [
                    "rules" => "required|is_natural"
                ],
                "import_po" => [
                    "rules" => "required|is_natural"
                ],
                "import_po" => [
                    "rules" => "required|is_natural"
                ],
                "payment_amt" => [
                    "rules" => "required|numeric"
                ],
                "current_exchange_rate" => [
                    "rules" => "required|numeric"
                ],
                "payment_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "termin" => [
                    "rules" => "permit_empty|is_natural"
                ],
                "payment_method" => [
                    "rules" => "required"
                ],
                "voucher_no" => [
                    "rules" => "permit_empty"
                ],
                "note" => [
                    "rules" => "permit_empty"
                ],
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

            $supplierId = (int)$this->request->getPost('supplier_id');
            $supplierData = $supplierModel->asObject()
                ->where('id', $supplierId)
                ->find();
            
            if (empty($supplierData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Supplier not Found!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            // validate PO here
            $poType = (string)$this->request->getPost('po_type');
            $poId = (int)$this->request->getPost('import_po');
            $poData = $this->checkPO($supplierId, $poId, $poType);

            if (empty($poData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'PO not Found!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $payload = [
                'company_id'            => $this->this_company_id,
                'payment_no'            => $this->generatePaymentNo(),
                'payment_type'          => $this->request->getPost('payment_type'),
                'po_type'               => $poType,
                'supplier_id'           => $supplierId,
                'po_id'                 => $poId,
                'voucher_no'            => $this->request->getPost('voucher_no'),
                'currency'              => $poData->currency,
                'payment_amt'           => $this->request->getPost('payment_amt'),
                'current_exchange_rate' => $this->request->getPost('current_exchange_rate'),
                'payment_date'          => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))),
                'termin'                => $this->request->getPost('termin'),
                'payment_method'        => $this->request->getPost('payment_method'),
                'voucher_no'            => $this->request->getPost('voucher_no'),
                'note'                  => $this->request->getPost('note')
            ];

            $importPOPaymentModel->db->transException(true)->transStart();
            $importPOPaymentModel->insert($payload);
            // update paid po here

            $importPOPaymentModel->db->transComplete();

            $data = [
                "id"        => "",
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $payload,
                'token'     => csrf_hash(),
                'code'      => 201
            ];
            echo json_encode($data);
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

    private function generatePaymentNo(): string
    {
        $importPOPayModel = new ImportPOPaymentModel();

        $month = idate('m');
        $year = date('Y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "PAY/IM/$romanMonth/$year/";

        $lastData = $importPOPayModel->asObject()
            ->like('payment_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $summaryNo = "{$numberTemplate}0001";
        
        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->summary_no);
            $lastIncrement = (int)$exploded[4] + 1;

            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $summaryNo = $numberTemplate . $paddedNumber;
        }

        return $summaryNo;
    }

    private function checkPO(int $supplierId, int $POId, string $poType): ?object
    {
        if ($poType == 'BAKU') {
            $rmImportPOModel = new RMImportPOModel();
            $poData = $rmImportPOModel->asObject()
                ->where('supplier_id', $supplierId)
                ->find($POId);

            return $poData;

        } else if ($poType == 'PENOLONG') {
            $aMPurchaseOrderModel = new AMPurchaseOrderModel();
            $poData = $aMPurchaseOrderModel->asObject()
                ->where('supplier_id', $supplierId)
                ->find($POId);

            return $poData;
        }

        return null;
    }

    private function getPOList(int $supplierId, string $poType): array
    {
        $condition = [
            'company_id'    => $this->this_company_id,
            'supplier_id'   => $supplierId,
            'is_posted'     => 1
        ];

        if ($poType == 'BAKU') {
            $rmImportPOModel = new RMImportPOModel();
            $selectQry = "rm_import_pos.*,
                      metadata.value AS currency";

            $poData = $rmImportPOModel->asObject()
                ->select($selectQry)
                ->join('metadata', 'metadata.id = rm_import_pos.currency')
                ->where($condition)
                ->findAll();

            return $poData;

        } else if ($poType == 'PENOLONG') {
            $aMPurchaseOrderModel = new AMPurchaseOrderModel();
            $selectQry = "am_purchase_orders.*,
                      metadata.value AS currency";

            $poData = $aMPurchaseOrderModel->asObject()
                ->select($selectQry)
                ->join('metadata', 'metadata.id = am_purchase_orders.currency')
                ->where($condition)
                ->findAll();

            return $poData;
        }

        return null;
    }
}

?>
    