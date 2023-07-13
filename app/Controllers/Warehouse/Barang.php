<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

use App\Models\BarangModel;
use App\Models\MetadataModel;

class Barang extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $barangModel;
    protected $metadataModel;
    
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangModel = new BarangModel();
        $this->metadataModel = new MetadataModel();
    }

    public function barang()
    {
        // Get Kategori
        $dataKategori = $this->metadataModel->get_by_name('Kategori Barang');
         
        $data = [
            "dataKategori" => $dataKategori
        ];

        return view('Warehouse/barang/index', $data);
    }

    public function allBarang()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "idCategory"    => formatter($this->request->getGet("kategori"), "STR_TO_INT"),
            "status"        => $this->request->getGet("status"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id
        ]; 

        $condition = [
            "company_id"    => $this->this_company_id
        ];
        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "kategori"      => formatter($this->request->getGet("kategori"), "STR_TO_INT"),
            "status"        => $this->request->getGet("status")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $barangData = $this->barangModel->getBarangList($condition, $addCondition, $limit, $offset);

        $dataBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($barangData['data'] as $data) {
            array_push($dataBarang, [
                "no"            => $no++,
                "id"            => $data->id,
                "kode_barang"   => $data->kode_barang,
                "nama_barang"   => $data->nama_barang,
                "harga_barang"  => number_format($data->harga_barang),
                "kode_satuan"   => $data->kode_satuan,
                "kategori"      => $data->kategori,
                "code_hs"       => $data->code_hs,
                "sub_akun_ap"   => $data->sub_akun_ap,
                "sub_akun_ar"   => $data->sub_akun_ar,
                "stok"          => $data->stok,
                "status"        => $data->status,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $barangData['totalData'],
            "recordsFiltered"   => $barangData['totalFilteredData'],
            "data"              => $dataBarang,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveBarang()
    {
        try{
            $rules = [
                "kode_barang" => [
                    "rules" => "required|is_unique[barangs.kode_barang]",
                    'errors' => ['is_unique' => 'kode Barang sudah ada!']
                ],
                "nama_barang" => [
                    "rules" => "required"
                ],
                "harga_barang" => [
                    "rules" => "required"
                ],
                "satuan_id" => [
                    "rules" => "required"
                ],
                "kategori_id" => [
                    "rules" => "required"
                ],
                "hs_id" => [
                    "rules" => "required"
                ],
                "ap_id" => [
                    "rules" => "required"
                ],
                "ar_id" => [
                    "rules" => "required"
                ],
                "stok" => [
                    "rules" => "required"
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

            if ($this->validate($rules)) {
                $payload = [
                    "company_id" => $this->this_company_id,
                    "kode_barang" => $this->request->getPost("kode_barang"),
                    "nama_barang" => $this->request->getPost("nama_barang"),
                    "harga_barang" => formatter($this->request->getPost("harga_barang"), "CURR_TO_INT"),
                    "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                    "kategori_id" => formatter($this->request->getPost("kategori_id"), "STR_TO_INT"),
                    "hs_id" => formatter($this->request->getPost("hs_id"), "STR_TO_INT"),
                    "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    "stok" => $this->request->getPost("stok") ? formatter($this->request->getPost("stok"), "STR_TO_INT") : 0,
                    "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif",
                    "spek" => $this->request->getPost("spek")
                ];

                $response =  $this->barangModel->insert($payload);

                if ($response) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message =  'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
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

    public function updateBarang()
    {
        try{
            $rules = [
                "nama_barang" => [
                    "rules" => "required"
                ],
                "harga_barang" => [
                    "rules" => "required"
                ],
                "satuan_id" => [
                    "rules" => "required"
                ],
                "kategori_id" => [
                    "rules" => "required"
                ],
                "hs_id" => [
                    "rules" => "required"
                ],
                "ap_id" => [
                    "rules" => "required"
                ],
                "ar_id" => [
                    "rules" => "required"
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

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = [
                    "company_id" => $this->this_company_id,
                    "nama_barang" => $this->request->getPost("nama_barang"),
                    "harga_barang" => formatter($this->request->getPost("harga_barang"), "CURR_TO_INT"),
                    "satuan_id" => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                    "kategori_id" => formatter($this->request->getPost("kategori_id"), "STR_TO_INT"),
                    "hs_id" => formatter($this->request->getPost("hs_id"), "STR_TO_INT"),
                    "ap_id" => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id" => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif",
                    "spek" => $this->request->getPost("spek")
                ];

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);

                $condition = [
                    'id' => $id
                ];

                $response = $this->barangModel->where($condition)->set($payload)->update();

                if ($response) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Diubah';
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

    public function updateStatusBarang()
    {
        try{
            $id = $this->request->getPost("id");

            $payload = [
                "status" => !empty($this->request->getPost("status")) ? "Aktif" : "Tidak Aktif"
            ];
            
            $condition = [
                'id' => $id,
                'company_id' => $this->this_company_id
            ];

            $response = $this->barangModel->where($condition)->set($payload)->update();

            if ($response) {
                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Diubah';
                $data = [
                    "status"            => false,
                    "message"    => $message,
                    "payload"   => $payload,
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

    public function getByIdBarang($id = null)
    {
        if (!empty($id)) {
            $response =  $this->barangModel->find($id);
            if ($response) {
                $data = [
                    "status"  => true,
                    "data"  => $response,
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

    public function deleteBarang()
    {
        try{
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $findBarang = $this->barangModel->find($id);
                if ($findBarang) {
                    $response =  $this->barangModel->delete($id);
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

    public function dropdownBarang()
    {
        $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangKategori()
    {
        $kategori = $this->request->getGet("kategori");
        $dataBarang = $this->barangModel->getBarangByKategori($kategori);

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }
}
?>