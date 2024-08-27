<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;

use App\Models\TaxModel;

class Tax extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $taxModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->taxModel = new TaxModel();
    }

    public function index()
    {
        return view('Master/tax/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $taxData = $this->taxModel->getList($condition, $addCondition, $limit, $offset);

        $dataTax = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($taxData['data'] as $data) {
            array_push($dataTax, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "tax_name"      => $data->name,
                "tax_type"      => strtoupper($data->type),
                "tax_value"     => $data->tax_value,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $taxData['totalData'],
            "recordsFiltered"   => $taxData['totalFilteredData'],
            "data"              => $dataTax,
            // "response" => $response,
            "payload"           => $payload
        ];

        return json_encode($data);
    }

    public function save()
    {
        try {
            $rules = [
                "tax_name" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama tax tidak boleh kosong'
                    ]
                ],
                "tax_type" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Type tax tidak boleh kosong'
                    ]
                ],
                "tax_value" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nilai tax tidak boleh kosong'
                    ]
                ]
            ];

            if ($this->validate($rules)) {
                $tax_name =  $this->request->getPost("tax_name");
                $tax_type =  $this->request->getPost("tax_type");
                $tax_value =  $this->request->getPost("tax_value");

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $tax_name,
                    "type" => $tax_type,
                    "tax_value" => $tax_value,
                ];
                if ($this->taxModel->insert($values)) {
                    $data = [
                        "status"    => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => "",
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Disimpan';
                    $data = [
                        "status"    => false,
                        "message"   => $message,
                        "payload"   => "",
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                }
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

    public function update()
    {
        try {
            $rules = [
                "tax_name" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama tax tidak boleh kosong'
                    ]
                ],
                "tax_type" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Type tax tidak boleh kosong'
                    ]
                ],
                "tax_value" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nilai tax tidak boleh kosong'
                    ]
                ]
            ];

            if ($this->validate($rules)) {
                $id = decrypt($this->request->getPost("id"));
                $tax_name =  $this->request->getPost("tax_name");
                $tax_type =  $this->request->getPost("tax_type");
                $tax_value =  $this->request->getPost("tax_value");

                $values = [
                    "company_id" => $this->this_company_id,
                    "name" => $tax_name,
                    "type" => $tax_type,
                    "tax_value" => $tax_value,
                ];

                if ($this->taxModel->where(['id' => $id])->set($values)->update()) {
                    $data = [
                        "status"    => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => "",
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Diubah';
                    $data = [
                        "status"    => false,
                        "message"   => $message,
                        "payload"   => "",
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                }
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
            $dataTax = $this->taxModel->getById(decrypt($id));

            if ($dataTax) {
                $data = [
                    "status"  => true,
                    "data"  => $dataTax
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

    public function delete()
    {
        try {
            $id = decrypt($this->request->getPost("id"));

            if (!empty($id)) {
                $find = $this->taxModel->find($id);
                if ($find) {
                    $response =  $this->taxModel->delete($id);
                    if ($response) {
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
                        "message"    => "Data Tidak Ditemukan",
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
}
