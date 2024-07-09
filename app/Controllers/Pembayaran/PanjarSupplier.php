<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\PanjarSupplierModel;
use App\Models\SupplierModel;
use App\Models\LocalPOPaymentPanjarModel;



class PanjarSupplier extends BaseController
{

    protected $token;
    protected $this_company_id;

    protected $panjarSupplierModel;

    protected $supplierModel;

    protected $localPOPaymentPanjarModel;
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->panjarSupplierModel = new PanjarSupplierModel();
        $this->supplierModel = new SupplierModel();
        $this->localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
    }

    public function index()
    {

        $data = [
            'noPanjar' => $this->panjarSupplierModel->getNumber($this->this_company_id)
        ];

        return view('Pembayaran/pembayaranPanjarSupplier/index', $data);
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

    public function savePanjarSupplier()
    {
        try {
            $rules = [
                "no_panjar" => [
                    "rules" => "required"
                ],
                "payment_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "total_panjar" => [
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

            $insertData = [

                "company_id"    => $this->this_company_id,
                "supplier_id"   => $this->request->getVar('supplier_id'),
                "no_panjar"     => $this->request->getPost("no_panjar"),
                "payment_date"  => $this->request->getVar("payment_date"),
                "total_panjar"  => repairDouble($this->request->getVar("total_panjar")),

            ];

            $insert = $this->panjarSupplierModel->insert($insertData);

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



    public function updatePanjarSupplier()
    {
        try {
            $rules = [
                "no_panjar" => [
                    "rules" => "required"
                ],
                "payment_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "total_panjar" => [
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

            if ($this->validate($rules)) {
                $id = decrypt($this->request->getPost("id"));
                $payload = [

                    "company_id" => $this->this_company_id,
                    "supplier_id"   => $this->request->getVar('supplier_id'),
                    "no_panjar"     => $this->request->getPost("no_panjar"),
                    "payment_date"  => $this->request->getVar("payment_date"),
                    "total_panjar"  => repairDouble($this->request->getVar("total_panjar")),
                ];
            }


            if ($payload) {
                $this->panjarSupplierModel->update($id, $payload);
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

    public function updateStatusPanjarSupplier()
    {
        $id = decrypt($this->request->getVar('id'));
        $status = $this->request->getVar('status');

        $this->panjarSupplierModel->update($id, [
            'is_posted' => $status
        ]);

        return response()->setJSON([
            "status" => true,
            "message" => "Status Posting Berhasil Diudpdate",
            "token" => csrf_hash()
        ]);
    }


    public function deletePanjarSupplier()
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

            $this->panjarSupplierModel->delete($id);
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

    public function allPanjarSupplier()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"                 => $this->request->getGet("search"),
            "panjar_status"         => $this->request->getVar("panjar_status"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),

            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",

        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "panjar_status" => $this->request->getVar("panjar_status"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            'panjar_supplier.company_id' => $this->this_company_id,
            'panjar_supplier.deletedAt' => null

        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->panjarSupplierModel->getPanjarSupplierList($addCondition, $condition, $limit, $offset);
        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        foreach ($supplierData['data'] as $data) {

            $bayar_panjar = $this->localPOPaymentPanjarModel
                ->where('panjar_id', $data->id)
                ->findAll();

            $total_bayar_panjar = 0;

            foreach ($bayar_panjar as $b) {
                $total_bayar_panjar += $b['bayar_panjar'];
            }
            array_push($dataSupplier, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "no_panjar"     => $data->no_panjar,
                "supplier"      => $data->name,
                "payment_date"  => date('d/m/Y', strtotime($data->payment_date)),
                "total_panjar"  => number_format($data->total_panjar, 2),
                "sisa_panjar"   => number_format(($data->total_panjar) - $total_bayar_panjar, 2),
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


    public function getByIdPanjarSupplier($id)
    {
        if (is_numeric($id)) {
            $id = $id;
        } else {
            $id = decrypt($id);
        }
        $panjarSupplierData = $this->panjarSupplierModel->getPanjarSupplierbyID($id);
        if (!$panjarSupplierData) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }

        $data = [
            "status"    => true,
            "supplier" => $this->supplierModel->getSupplierByType($panjarSupplierData->type), // GET SUPPLIER DETAIL
            "data"      => $panjarSupplierData,
        ];
        echo json_encode($data);

        return;
    }

    public function dropDownHistoryPembayaranPanjar()
    {
        $id = decrypt($this->request->getVar('id'));

        $historyPembayaranPanjarData = $this->localPOPaymentPanjarModel->getPembayaranPanjarDetailsbyPanjarId($id);
        $panjarDetail = $this->panjarSupplierModel->getPanjarSupplierbyID($id);

        if (!$panjarDetail) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }
        $data = [
            "status"    => true,
            "data"      => $historyPembayaranPanjarData,
            'panjar_detail' => $panjarDetail

        ];


        return response()->setJSON($data);
    }
}
