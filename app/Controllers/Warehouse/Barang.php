<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

use App\Models\BarangModel;
use App\Models\BarangSupplierModel;
use App\Models\HSCodeModel;
use App\Models\MetadataModel;
use App\Models\SatuansModel;
use App\Models\Sub_AkunsModel;

class Barang extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $barangModel;
    protected $barangSupplierModel;
    protected $HSCodeModel;
    protected $metadataModel;
    protected $SatuansModel;
    protected $Sub_AkunsModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangModel = new BarangModel();
        $this->barangSupplierModel = new BarangSupplierModel();
        $this->HSCodeModel = new HSCodeModel();
        $this->metadataModel = new MetadataModel();
        $this->SatuansModel = new SatuansModel();
        $this->Sub_AkunsModel = new Sub_AkunsModel();
    }

    public function barang()
    {
        // Get Kategori
        $dataKategori = $this->metadataModel->get_by_name('Kategori Barang');

        // get parent barang
        $dataBarangParent = $this->barangModel->getParentBarang($this->this_company_id);

        // get kategori barang
        $kategoriBarangData = $this->metadataModel->getByName('kategori barang');

        // get ap ar account
        $dataAPAR = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        // get satuan data
        $satuanData = $this->SatuansModel->asObject()->findAll();

        // get data HS
        $dataKodeHS = $this->HSCodeModel->asObject()->findAll();

        $data = [
            "dataKategori"      => $dataKategori,
            "dataBarangParent"  => $dataBarangParent,
            "kategoriBarangData"=> $kategoriBarangData,
            "aparData"          => $dataAPAR,
            "satuanData"        => $satuanData,
            "dataKodeHS"        => $dataKodeHS
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
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id
        ];

        $condition = [
            "barangs.company_id"    => $this->this_company_id
        ];
        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "kategori"      => formatter($this->request->getGet("kategori"), "STR_TO_INT")
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
                "parent_barang"   => $data->parent_barang,
                "kode_barang"   => $data->kode_barang,
                "nama_barang"   => $data->nama_barang,
                "type"          => $data->type,
                "harga_barang"  => number_format($data->harga_barang),
                "kode_satuan"   => $data->kode_satuan,
                "kategori"      => $data->kategori,
                "code_hs"       => $data->code_hs,
                "sub_akun_ap"   => $data->sub_akun_ap,
                "sub_akun_ar"   => $data->sub_akun_ar,
                "stok"          => $data->stok
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
        try {
            $rules = [
                "productSpec" => [
                    "rules" => "required|in_list[single,multi]",
                    'errors' => [
                        'required' => 'Product Spec tidak boleh kosong'
                    ]
                ],
                "nama_barang" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama Barang tidak boleh kosong'
                    ]
                ],
                "spek" => [
                    "rules" => "permit_empty",
                ],
                "satuan_id" => [
                    "rules" => "required|is_natural",
                    'errors' => [
                        'required' => 'Satuan tidak boleh kosong'
                    ]
                ],
                "harga_barang" => [
                    "rules" => "required|regex_match[/[0-9]|\,/]",
                    'errors' => [
                        'required' => 'Harga Barang tidak boleh kosong',
                        "regex_match" => "Harga Barang harus numerik!"
                    ]
                ],
                "type" => [
                    "rules" => "permit_empty",
                ],
                "kategori_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "hs_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "ap_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "ar_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "tax" => [
                    "rules" => "permit_empty|numeric",
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

            $parent_id = formatter($this->request->getPost("parent_id"), "STR_TO_INT");
            $kodeBarang = $this->generateNewCode();
            $productSpec = $this->request->getPost('productSpec');
            if ($parent_id || $productSpec == 'single') {
                $payload = [
                    "spec_type"         => $productSpec,
                    "company_id"        => $this->this_company_id,
                    "parent_id"         => formatter($this->request->getPost("parent_id"), "STR_TO_INT"),
                    "kode_barang"       => $kodeBarang,
                    "nama_barang"       => $this->request->getPost("nama_barang"),
                    "type"              => $this->request->getPost("type"),
                    "supplier_id"       => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "harga_barang"      => formatter($this->request->getPost("harga_barang"), "CURR_TO_INT"),
                    "satuan_id"         => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                    "kategori_id"       => formatter($this->request->getPost("kategori_id"), "STR_TO_INT"),
                    "hs_id"             => formatter($this->request->getPost("hs_id"), "STR_TO_INT"),
                    "ap_id"             => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id"             => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    // "stok"              => $this->request->getPost("stok") ? formatter($this->request->getPost("stok"), "STR_TO_INT") : 0,
                    "stok"              => 0,
                    "tax"               => $this->request->getPost('tax') ?? 0,
                    "spek"              => $this->request->getPost("spek")
                ];
            } else {
                $payload = [
                    "spec_type"     => $productSpec,
                    "company_id"    => $this->this_company_id,
                    "parent_id"     => 0,
                    "kode_barang"   => $kodeBarang,
                    "nama_barang"   => $this->request->getPost("nama_barang"),
                ];
            }

            $supplier_id = json_decode($this->request->getPost("supplier_id"));
            $payload_supplier = [];

            $this->barangModel->db->transException(true)->transStart();
            $insertedId =  $this->barangModel->insert($payload);

            foreach ($supplier_id as $item) {
                $payload_supplier[] = [
                    "barang_id"     => $insertedId,
                    "supplier_id"   => $item
                ];
            }

            if (count($payload_supplier)) {
                $this->barangSupplierModel->insertBatch($payload_supplier);
            }
            
            $this->barangModel->db->transComplete();

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $payload,
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            
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

    public function updateBarang()
    {
        try {
            $rules = [
                "productSpec" => [
                    "rules" => "permit_empty|in_list[single,multi]",
                ],
                "nama_barang" => [
                    "rules" => "permit_empty",
                ],
                "spek" => [
                    "rules" => "permit_empty",
                ],
                "satuan_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "harga_barang" => [
                    "rules" => "permit_empty|regex_match[/[0-9]|\,/]",
                    "errors" => [
                        "regex_match" => "Harga Barang harus numerik!"
                    ]
                ],
                "type" => [
                    "rules" => "permit_empty",
                ],
                "kategori_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "hs_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "ap_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "ar_id" => [
                    "rules" => "permit_empty|is_natural",
                ],
                "tax" => [
                    "rules" => "permit_empty|numeric",
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
die('Ok');
            $id = $this->request->getPost("id");
            $productSpec = $this->request->getPost('productSpec');
            $parent = formatter($this->request->getPost("parent"), "STR_TO_INT");

            $barangData = $this->barangModel->asObject()
                ->find($id);

            if (empty($barangData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Barang Tidak ditemukan',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($barangData->spec_type == 'single' && $productSpec == 'multi' && empty($parent)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Not Allowed',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($parent || $productSpec == 'single') {
                $payload = [
                    "spec_type"         => $productSpec,
                    "parent_id"         => $parent,
                    "type"              => $this->request->getPost("type"),
                    "supplier_id"       => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    "company_id"        => $this->this_company_id,
                    "nama_barang"       => $this->request->getPost("nama_barang"),
                    "harga_barang"      => formatter($this->request->getPost("harga_barang"), "CURR_TO_INT"),
                    "satuan_id"         => formatter($this->request->getPost("satuan_id"), "STR_TO_INT"),
                    "kategori_id"       => formatter($this->request->getPost("kategori_id"), "STR_TO_INT"),
                    "hs_id"             => formatter($this->request->getPost("hs_id"), "STR_TO_INT"),
                    "ap_id"             => formatter($this->request->getPost("ap_id"), "STR_TO_INT"),
                    "ar_id"             => formatter($this->request->getPost("ar_id"), "STR_TO_INT"),
                    "stok"              => $this->request->getPost("stok") ? formatter($this->request->getPost("stok"), "STR_TO_INT") : 0,
                    "tax"               => $this->request->getPost('tax') ?? 0,
                    "spek"              => $this->request->getPost("spek")
                ];
            } else {
                $payload = [
                    "spec_type"     => $productSpec,
                    "parent_id"     => 0,
                    "company_id"    => $this->this_company_id,
                    "nama_barang"   => $this->request->getPost("nama_barang"),
                ];
            }

            $condition = [
                'id' => $id
            ];

            $this->barangModel->db->transException(true)->transStart();
            
            $this->barangModel->where($condition)->set($payload)
                ->update();

            $supplier_id = json_decode($this->request->getPost("supplier_id"));

            $this->barangSupplierModel->deleteByBarangId($id);

            foreach ($supplier_id as $item) {
                $payload_supplier = [
                    "barang_id" => $id,
                    "supplier_id" => $item
                ];
                $this->barangSupplierModel->insert($payload_supplier);
            }

            $this->barangModel->db->transComplete();

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil diubah",
                "payload"   => $payload,
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            
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

    public function getByIdBarang($id = null)
    {
        if (!empty($id)) {
            $response =  $this->barangModel->find($id);

            if ($response) {
                $responseSupplier =  $this->barangSupplierModel->getByBarangId($id);
                if ($responseSupplier) {
                    $data = [
                        "status"  => true,
                        "data"  => $response,
                        "dataSupplier"  => $responseSupplier
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"  => true,
                        "data"  => $response,
                        "dataSupplier"  => []
                    ];
                    echo json_encode($data);
                }
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
        try {
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

    public function dropdownBarang()
    {
        $dataBarang = $this->barangModel->getBarangByCompanyId($this->this_company_id);

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownParentBarang()
    {
        $dataBarang = $this->barangModel->getParentBarang($this->this_company_id);

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

    public function dropdownBarangType()
    {
        $type = $this->request->getGet("type");
        $dataBarang = $this->barangModel->getBarangByType($type);

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }

    private function generateNewCode()
    {
        // PR-0001
        $lastBarang = $this->barangModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->orderBy('createdAt', 'DESC')
            ->first();

        if (empty($lastBarang)) {
            return "PR-0001";
        }

        $lastCode = $lastBarang->kode_barang;
        $lastCodeExp = explode('-', $lastCode);
        $lastIncrement = (int)$lastCodeExp[1];
        $newIncrement = str_pad(($lastIncrement + 1), 4, '0', STR_PAD_LEFT);

        return "PR-$newIncrement";
    }
}
