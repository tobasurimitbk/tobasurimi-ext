<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

use App\Models\SupplierModel;
use App\Models\TandaTerimaFakturLokalModel;

class TerimaFakturLokal extends BaseController
{
    protected $token;
    protected $user_id;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function terimaFakturLokal()
    {
        return view('Purchase/terimaFakturLokal/index');
    }

    public function createTerimaFakturLokal()
    {
        $supplierModel = new SupplierModel();

        //Get Supplier
        $supplierList = $supplierModel->asObject()
            ->where('kategori', 'LOKAL')
            ->findAll();

        $data = [
            "dataSupplier" => $supplierList
        ];

        return view('Purchase/terimaFakturLokal/form', $data);
    }

    public function getByIdTerimaFakturLokal($id)
    {
        $tandaTerimaFakturLokalModel = new TandaTerimaFakturLokalModel();
        $supplierModel = new SupplierModel();

        $selectQry = "tanda_terima_faktur_lokal.id,
                      tanda_terima_faktur_lokal.inv_no, 
                      tanda_terima_faktur_lokal.supplier_id, 
                      tanda_terima_faktur_lokal.inv_total, 
                      DATE_FORMAT(tanda_terima_faktur_lokal.receive_date, '%d/%m/%Y') AS receive_date, 
                      DATE_FORMAT(tanda_terima_faktur_lokal.due_date, '%d/%m/%Y') AS due_date, 
                      tanda_terima_faktur_lokal.information, 
                      users.name AS createdBy";
        $dataTerimaFaktur = $tandaTerimaFakturLokalModel->asObject()
            ->select($selectQry)
            ->join('users', 'users.id = tanda_terima_faktur_lokal.createdBy')
            ->find($id);

        $supplierList = $supplierModel->asObject()
            ->where('kategori', 'LOKAL')
            ->findAll();

        $data = [
            'dataTerimaFaktur'  => $dataTerimaFaktur,
            'dataSupplier'      => $supplierList
        ];

        return view('Purchase/terimaFakturLokal/form', $data);
    }

    public function getBySupplierId($supplierId = null)
    {
        //Get Faktur
        $responseFaktur = curl_request("GET", "/tandaTerimaFaktur/getBySupplier/$supplierId", $this->token);

        $dataFaktur = [];
        if ($responseFaktur["code"] === 200) {
            $dataFaktur = json_decode($responseFaktur["body"])->data;
        }

        $data = [
            "data" => $dataFaktur
        ];

        echo json_encode($data);
        return;
    }

    public function allTerimaFakturLokal()
    {
        $tandaTerimaFakturLokalModel = new TandaTerimaFakturLokalModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "fakturtype" => "LOKAL",
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            "suppliers.company_id"  => $this->this_company_id,
            "kategori"              => "LOKAL",
            "suppliers.type"        => "BAHAN BAKU"
        ];
        $addCondition = [
            "dateStart" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"   => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $invData = $tandaTerimaFakturLokalModel->getInvList($condition, $addCondition, $limit, $offset);

        $dataTerimaFakturImport = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($invData['data'] as $data) {
            array_push($dataTerimaFakturImport, [
                "no"                => $no++,
                "id"                => $data->id,
                "inv_no"            => $data->inv_no,
                "supplier"          => $data->supplierName,
                "nominal_faktur"    => $data->inv_total,
                "due_date"          => $data->due_date,
                "receive_date"      => $data->receive_date,
                "recipient"         => $data->createdBy
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $invData['totalData'],
            "recordsFiltered"   => $invData['totalFilteredData'],
            "data"              => $dataTerimaFakturImport,
            // "response"          => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveTerimaFakturLokal()
    {
        try {
            $tandaTerimaFakturLokalModel = new TandaTerimaFakturLokalModel();

            $rules = [
                "supplier_id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "inv_total" => [
                    "rules" => "required|numeric"
                ],
                "receive_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "due_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "information" => [
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

            $payload = [
                'receive_date'  => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("receive_date")))),
                'inv_no'        => $this->generateInvNo(),
                'supplier_id'   => $this->request->getPost("supplier_id"),
                'inv_total'     => $this->request->getPost('inv_total'),
                'due_date'      => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))),
                'information'   => $this->request->getPost("information"),
                'createdBy'     => $this->user_id
            ];

            // insert here
            $insertedId = $tandaTerimaFakturLokalModel->insert($payload);

            $data = [
                "id"        => $insertedId,
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $payload,
                'token'     => csrf_hash(),
                'code'      => 201
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateTerimaFakturLokal()
    {
        try {
            $tandaTerimaFakturLokalModel = new TandaTerimaFakturLokalModel();

            $id = $this->request->getPost("id");

            $rules = [
                "supplier_id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "inv_total" => [
                    "rules" => "required|numeric"
                ],
                "receive_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "due_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "information" => [
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

            $invData = $tandaTerimaFakturLokalModel->find($id);
            if (empty($invData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data not Found!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $payload = [
                'receive_date'  => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("receive_date")))),
                'inv_no'        => $this->generateInvNo(),
                'supplier_id'   => $this->request->getPost("supplier_id"),
                'inv_total'     => $this->request->getPost('inv_total'),
                'due_date'      => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))),
                'information'   => $this->request->getPost("information"),
                'createdBy'     => $this->user_id
            ];

            // update here
            $tandaTerimaFakturLokalModel->update($id, $payload);

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $payload,
                'token'     => csrf_hash(),
                'code'      => 200
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteTerimafakturLokal()
    {
        try {
            $tandaTerimaFakturLokalModel = new TandaTerimaFakturLokalModel();

            $id = $this->request->getPost("id");

            if (empty($id)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $invData = $tandaTerimaFakturLokalModel->find($id);
            if (empty($invData)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $tandaTerimaFakturLokalModel->delete($id);
            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
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

    private function generateInvNo()
    {
        $tandaTerimaFakturLokalModel = new TandaTerimaFakturLokalModel();

        $month = idate('m');
        $year = date('Y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "TTL/$romanMonth/$year/";

        $lastData = $tandaTerimaFakturLokalModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->like('inv_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $invNo = "{$numberTemplate}0001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->inv_no);
            $lastIncrement = (int)$exploded[3] + 1;

            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $invNo = $numberTemplate . $paddedNumber;
        }

        return $invNo;
    }
}
