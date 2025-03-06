<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\PinjamanSupplierModel;
use App\Models\SupplierModel;
use App\Models\LocalPOPaymentPinjamanModel;



class PinjamanSupplier extends BaseController
{

    protected $token;
    protected $this_company_id;

    protected $pinjamanSupplierModel;

    protected $supplierModel;

    protected $localPOPaymentPinjamanModel;
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->pinjamanSupplierModel = new PinjamanSupplierModel();
        $this->supplierModel = new SupplierModel();
        // $this->localPOPaymentPinjamanModel = new LocalPOPaymentPinjamanModel();
    }

    public function index()
    {

        $data = [
            // 'noPinjaman' => $this->pinjamanSupplierModel->getNumber($this->this_company_id)
        ];

        return view('Pembayaran/pembayaranPinjamanSupplier/index', $data);
    }


    public function allPinjamanSupplier()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"                 => $this->request->getGet("search"),
            "pinjaman_status"         => $this->request->getVar("pinjaman_status"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),

            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",

        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "pinjaman_status" => $this->request->getVar("pinjaman_status"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            'pinjaman_supplier.company_id' => $this->this_company_id,
            'pinjaman_supplier.deletedAt' => null

        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->pinjamanSupplierModel->getPinjamanSupplierList($addCondition, $condition, $limit, $offset);
        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($supplierData['data'] as $data) {

            // $bayar_pinjaman = $this->localPOPaymentPinjamanModel
            //     ->where('pinjaman_id', $data->id)
            //     ->findAll();

            // $total_bayar_pinjaman = 0;

            // foreach ($bayar_pinjaman as $b) {
            //     $total_bayar_pinjaman += $b['bayar_pinjaman'];
            // }
            array_push($dataSupplier, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "no_pinjaman"     => $data->no_pinjaman,
                "supplier"      => $data->name,
                "payment_date"  => date('d/m/Y', strtotime($data->payment_date)),
                "total_pinjaman"  => number_format($data->total_pinjaman, 2),
                "akun_kas"      => $data->akun_kas,
                "akun_selisih"  => $data->akun_selisih,
                "akun_selisih_nama"  => $data->akun_selisih_name,
                "akun_kas_nama"  => $data->akun_kas_name,
                // "sisa_pinjaman"   => number_format(($data->total_pinjaman) - $total_bayar_pinjaman, 2),
                "is_posted"     => $data->is_posted
            ]);
        }
        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $supplierData['totalData'],
            "recordsFiltered"   => $supplierData['totalFilteredData'],
            "data"              => $dataSupplier,
            "payload"           => $payload
        ];
        echo json_encode($data);
        return;
    }

    public function dropdownSupplierByType()
    {
        $typeSupplier = $this->request->getVar('type_supplier');
        $result = $this->supplierModel->getSupplierByType($typeSupplier);

        return response()->setJSON([
            'data' => $result,
            'status' => true
        ]);
    }

    public function savePinjamanSupplier()
    {
        try {
            $rules = [
                "no_pinjaman" => [
                    "rules" => "required"
                ],
                "payment_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "total_pinjaman" => [
                    "rules" => "required"
                ],
            ];
            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status" => false,
                    "message" => $errorList[array_keys($errorList)[0]],
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
            //check
            $check = $this->pinjamanSupplierModel->where('company_id', $this->this_company_id)->where('no_pinjaman', $this->request->getPost("no_pinjaman"))->first();
            if ($check != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "No Pinjaman sudah digunakan",
                    'status' => false
                ]);
            }

            $insertData = [

                "company_id"    => $this->this_company_id,
                "supplier_id"   => $this->request->getVar('supplier_id'),
                "no_pinjaman"     => $this->request->getPost("no_pinjaman"),
                "payment_date"  => $this->request->getVar("payment_date"),
                "total_pinjaman"  => repairDouble($this->request->getVar("total_pinjaman")),
                "akun_kas"  => repairDouble($this->request->getVar("akun_kas")),
                "akun_selisih"  => repairDouble($this->request->getVar("akun_selisih")),
            ];

            $insert = $this->pinjamanSupplierModel->insert($insertData);

            if (!$insert) {
                $data = [
                    "status" => false,
                    "message" => 'Data Gagal Disimpan!',
                    "payload" => json_encode($insertData),
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "status" => true,
                "message" => "Data Berhasil disimpan",
                "payload" => json_encode($insertData),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status" => false,
                "message" => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function updatePinjamanSupplier()
    {
        try {
            $rules = [
                // "no_pinjaman" => [
                //     "rules" => "required"
                // ],
                "payment_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "total_pinjaman" => [
                    "rules" => "required"
                ],
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status" => false,
                    "message" => $errorList[array_keys($errorList)[0]],
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $id = decrypt($this->request->getPost("id"));

            $check = $this->pinjamanSupplierModel
                            ->where('company_id', $this->this_company_id)
                            ->where('no_pinjaman', $this->request
                            ->where('id !=', $id)
                            ->getPost("no_pinjaman"))
                            ->first();
            if ($check != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "No Pinjaman sudah digunakan",
                    'status' => false
                ]);
            }

            if ($this->validate($rules)) {
                $payload = [

                    "company_id" => $this->this_company_id,
                    "supplier_id"   => $this->request->getVar('supplier_id'),
                    // "no_pinjaman"     => $this->request->getPost("no_pinjaman"),
                    "akun_kas"  => $this->request->getVar("akun_kas"),
                    "akun_selisih"  => $this->request->getVar("akun_selisih"),
                    "payment_date"  => $this->request->getVar("payment_date"),
                    "total_pinjaman"  => repairDouble($this->request->getVar("total_pinjaman")),
                ];
            }

            if ($payload) {
                $this->pinjamanSupplierModel->update($id, $payload);
                $data = [
                    "status" => true,
                    "message" => "Data Berhasil disimpan",
                    "payload" => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);

                return;
            }
        } catch (\Exception $e) {
            $data = [
                "status" => false,
                "message" => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function updateStatusPinjamanSupplier()
    {
        $id = decrypt($this->request->getVar('id'));
        $status = $this->request->getVar('status');

        $this->pinjamanSupplierModel->update($id, [
            'is_posted' => $status
        ]);

        return response()->setJSON([
            "status" => true,
            "message" => "Status Posting Berhasil Diudpdate",
            "token" => csrf_hash()
        ]);
    }

    public function getByIdPanjarSupplier($id)
    {
        if (is_numeric($id)) {
            $id = $id;
        } else {
            $id = decrypt($id);
        }
        $pinjamanSupplierData = $this->pinjamanSupplierModel->getPinjamanSupplierbyID($id);
        if (!$pinjamanSupplierData) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }

        $data = [
            "status"    => true,
            "supplier" => $this->supplierModel->getSupplierByType($pinjamanSupplierData->type), // GET SUPPLIER DETAIL
            "data"      => $pinjamanSupplierData,
        ];
        echo json_encode($data);

        return;
    }


    public function getByIdPinjamanSupplier($id)
    {
        if (is_numeric($id)) {
            $id = $id;
        } else {
            $id = decrypt($id);
        }
        $pinjamanSupplierData = $this->pinjamanSupplierModel->getPinjamanSupplierbyID($id);
        if (!$pinjamanSupplierData) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }

        $data = [
            "status"    => true,
            "supplier" => $this->supplierModel->getSupplierByType($pinjamanSupplierData->type), // GET SUPPLIER DETAIL
            "data"      => $pinjamanSupplierData,
        ];
        echo json_encode($data);

        return;
    }

    public function deletePinjamanSupplier()
    {
        try {
            if (is_numeric($this->request->getPost('id'))) {
                $id = $this->request->getPost("id");
            } else {
                $id = decrypt($this->request->getPost("id"));
            }

            if (empty($id)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $this->pinjamanSupplierModel->delete($id);
            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function generateNoPinjaman()
    {
        $noPinjaman = $this->pinjamanSupplierModel->getNumber($this->this_company_id);
        return json_encode($noPinjaman);
    }

}
