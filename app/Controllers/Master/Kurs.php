<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\KursModel;
use App\Models\MetadataModel;

class Kurs extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $KursModel;
    protected $MetadataModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->KursModel = new KursModel();
        $this->MetadataModel = new MetadataModel();
    }

    public function index()
    {
        return view('Master/kurs/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            // "requestStatus" => $this->request->getGet("status"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $kursData = $this->KursModel->getList($condition, $addCondition, $limit, $offset);

        $dataKurs = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($kursData['data'] as $data) {
            array_push($dataKurs, [
                "no"            => $no++,
                "id"            => $data->id,
                "valas"         => $data->valas,
                "nilai_kurs"    => $data->nilai_kurs ? number_format(formatter($data->nilai_kurs, "STR_TO_INT")) : 0,
                "start_date"    => $data->start_date,
                "end_date"      => $data->end_date,
                "createdAt"     => $data->createdAt,
                "updatedAt"     => $data->updatedAt
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $kursData['totalData'],
            "recordsFiltered"   => $kursData['totalFilteredData'],
            "data"              => $dataKurs,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
        print_r($addCondition);
    }

    public function save()
    {
        try {
            $rules = [
                "valas" => [
                    "rules" => "is_unique[metadata.value]",
                    'errors' => ['is_unique' => 'Valas sudah ada']
                ],
                "metadata_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Valas tidak boleh kosong'
                    ]
                ],
                "nilai_kurs" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nilai Kurs tidak boleh kosong'
                    ]
                ],
                "start_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal Awal tidak boleh kosong'
                    ]
                ],
                "end_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal Akhir tidak boleh kosong'
                    ]
                ]
            ];

            if ($this->validate($rules)) {
                $valas =  $this->request->getPost("valas");

                // VALIDATION DATE STRAT AND END
                $startDate = $this->request->getPost("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("start_date")))) : "";
                $endDate = $this->request->getPost("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("end_date")))) : "";
                if (strtotime($startDate) > strtotime($endDate)) {
                    $data = [
                        "status"            => false,
                        "message"    => "Tanggal Mulai dan Tanggal Selesai Tidak Sesuai",
                        'token' => csrf_hash()
                    ];
                    return response()->setJSON($data);
                }

                // CREATE CURRENCY
                $id_metadata = $this->request->getPost("metadata_id");
                if ($valas) {
                    $payload = [
                        "name" => "Valuta",
                        "description" => NULL,
                        "value" => $valas
                    ];

                    $id_metadata = $this->MetadataModel->insert($payload);
                }

                $values = [
                    "metadata_id" => $id_metadata,
                    "nilai_kurs" => formatter($this->request->getPost("nilai_kurs"), "CURR_TO_INT"),
                    "start_date" => $this->request->getPost("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("start_date")))) : "",
                    "end_date" => $this->request->getPost("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("end_date")))) : "",
                ];

                // CHECK CURRENT KURS
                $check = $this->KursModel->check_current("", $id_metadata, $this->request->getPost("end_date"));

                if ($check > 0) {
                    $message = 'Kurs Sedang Berjalan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    if ($this->KursModel->insert($values)) {
                        $data = [
                            "status"            => true,
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
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Disimpan",
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

    public function update()
    {
        try {
            $rules = [
                "valas" => [
                    "rules" => "is_unique[metadata.value]",
                    'errors' => ['is_unique' => 'Valas sudah ada']
                ],
                "metadata_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Valas tidak boleh kosong'
                    ]
                ],
                "nilai_kurs" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nilai Kurs tidak boleh kosong'
                    ]
                ],
                "start_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal Awal tidak boleh kosong'
                    ]
                ],
                "end_date" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tanggal Akhir tidak boleh kosong'
                    ]
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $valas =  $this->request->getPost("valas");

                // VALIDATION DATE STRAT AND END
                $startDate = $this->request->getPost("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("start_date")))) : "";
                $endDate = $this->request->getPost("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("end_date")))) : "";
                if (strtotime($startDate) > strtotime($endDate)) {
                    $data = [
                        "status"            => false,
                        "message"    => "Tanggal Mulai dan Tanggal Selesai Tidak Sesuai",
                        'token' => csrf_hash()
                    ];
                    return response()->setJSON($data);
                }

                // CREATE CURRENCY
                $id_metadata = $this->request->getPost("metadata_id");
                if ($valas) {
                    $payload = [
                        "name" => "Valuta",
                        "description" => NULL,
                        "value" => $valas
                    ];

                    $id_metadata = $this->MetadataModel->insert($payload);
                }

                $values = [
                    "metadata_id" => $id_metadata,
                    "nilai_kurs" => formatter($this->request->getPost("nilai_kurs"), "CURR_TO_INT"),
                    "start_date" => $this->request->getPost("start_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("start_date")))) : "",
                    "end_date" => $this->request->getPost("end_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("end_date")))) : "",
                ];

                // CHECK CURRENT KURS
                $check = $this->KursModel->check_current($id, $id_metadata, $this->request->getPost("end_date"));

                if ($check > 0) {
                    $message = 'Kurs Sedang Berjalan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => "",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    if ($this->KursModel->where(['id' => $id])->set($values)->update()) {
                        $data = [
                            "status"            => true,
                            "message"   => "Data Berhasil disimpan",
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
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Diubah",
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

    public function getById($id = null)
    {
        if (!empty($id)) {
            $dataKurs = $this->KursModel->getById($id);

            if ($dataKurs) {
                $data = [
                    "status"  => true,
                    "data"  => $dataKurs
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

    public function getKurs($id = null)
    {
        if (!empty($id)) {
            $dataKurs = $this->KursModel->getKurs($id);

            if ($dataKurs) {
                $data = [
                    "status"  => true,
                    "data"  => $dataKurs
                ];
                echo json_encode($data);
            } else {
                $message = 'Kurs dengan valas tersebut belum terdefinisikan';
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
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $find = $this->KursModel->find($id);
                if ($find) {
                    $response =  $this->KursModel->delete($id);
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
