<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CustomerModel;
use App\Models\SupplierLokalModel;
use App\Models\EmployeesModel;
use App\Models\MetadataModel;
use App\Models\ProvincesModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class SupplierLokal extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $ProvincesModel;
    protected $BanksModel;
    protected $soInvModel;
    protected $countryModel;
    protected $CustomerModel;
    protected $SupplierLokalModel;
    protected $employeeModel;
    protected $metadataModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->ProvincesModel = new ProvincesModel();
        $this->BanksModel = new BanksModel();
        $this->CustomerModel = new CustomerModel();
        $this->SupplierLokalModel = new SupplierLokalModel();
        $this->employeeModel = new EmployeesModel();
        $this->metadataModel = new MetadataModel();
    }

    public function index()
    {
        //Get Provinces
        $dataProvinces = $this->ProvincesModel->search_list(array(), 'province_name');
        $dataBanks = $this->BanksModel->search_list(array(), 'name');

        $condition = [
            'jabatan_name' => "MARKETING LOKAL"
        ];

        $sales = $this->employeeModel->getEmployeesComplete($condition);

        $data = [
            "dataProvinces" => $dataProvinces,
            "dataBanks" => $dataBanks,
            "dataSales" => $sales,
        ];

        return view('SalesLokal/SupplierLokal/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        if ($this->is_admin == '1') {
            $condition = [
                'tipe_customer' => $this->request->getGet('customerType'),
                'supplier_lokals.deletedAt' => null,
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                'tipe_customer' => $this->request->getGet('customerType'),
                'supplier_lokals.deletedAt' => null,
                // 'supplier_lokals.user_id' => session()->get('login')->user_id
            ];
        }

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $customerData = $this->SupplierLokalModel->getListCustomerDetail($condition, $addCondition, $limit, $offset);

        $dataCustomer = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        // var_dump($customerData['data']);
        // exit;

        foreach ($customerData['data'] as $data) {
            $termin = "-";
            if ($data->termin == "0" || $data->termin == null) {
                $termin = "-";
            } else {
                $termin = $this->metadataModel->find($data->termin)['value'];
            }

            array_push($dataCustomer, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "kode"          => $data->kode,
                "namaSales"     => $data->namaSales,
                "name"          => $data->name,
                "phone"         => $data->phone,
                "contact_person" => $data->contact_person,
                "saldo"         => number_format(floatval($data->saldo)),
                "currencyName"  => $data->currencyName,
                "countryName"   => $data->countryName,
                "address"       => $data->address,
                "termin"        => $termin,
                "piutang"         => number_format(floatval($data->piutang)),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $customerData['totalData'],
            "recordsFiltered"   => $customerData['totalFilteredData'],
            "data"              => $dataCustomer,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function exportExcel()
    {
        $condition = [
            'tipe_customer' => $this->request->getGet('customerType'),
            'supplier_lokals.deletedAt' => null,
        ];

        if ($this->is_admin == '0') {
            // $condition['supplier_lokals.user_id'] = session()->get('login')->user_id;
        }

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
        ];

        // Ambil semua data tanpa pagination
        $customerData = $this->SupplierLokalModel->getListCustomerDetail($condition, $addCondition, null, null);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode');
        $sheet->setCellValue('C1', 'Nama Sales');
        $sheet->setCellValue('D1', 'Nama SupplierLokal');
        $sheet->setCellValue('E1', 'Phone');
        $sheet->setCellValue('F1', 'Contact Person');
        $sheet->setCellValue('G1', 'Saldo');
        $sheet->setCellValue('H1', 'Currency');
        $sheet->setCellValue('I1', 'Country');
        $sheet->setCellValue('J1', 'Alamat');
        $sheet->setCellValue('K1', 'Termin');
        $sheet->setCellValue('L1', 'Piutang');

        $row = 2;
        $no = 1;

        foreach ($customerData['data'] as $data) {
            $termin = "-";
            if ($data->termin != "0" && $data->termin != null) {
                $termin = $this->metadataModel->find($data->termin)['value'];
            }

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $data->kode);
            $sheet->setCellValue("C{$row}", $data->namaSales);
            $sheet->setCellValue("D{$row}", $data->name);
            $sheet->setCellValue("E{$row}", $data->phone);
            $sheet->setCellValue("F{$row}", $data->contact_person);

            // Saldo as number
            $sheet->setCellValue("G{$row}", floatval($data->saldo));
            $sheet->getStyle("G{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $sheet->setCellValue("H{$row}", $data->currencyName);
            $sheet->setCellValue("I{$row}", $data->countryName);
            $sheet->setCellValue("J{$row}", $data->address);
            $sheet->setCellValue("K{$row}", $termin);

            // Piutang as number
            $sheet->setCellValue("L{$row}", floatval($data->piutang));
            $sheet->getStyle("L{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $row++;
        }

        // Set response untuk download file
        $filename = 'Export-SupplierLokal-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function getByIdCustomer($id = null)
    {
        if (!empty($id)) {
            $id = decrypt($id);
            $res = $this->SupplierLokalModel->get_by_id($id, '1');
            // $country =  $this->countryModel->where('id', $res[0]['country_id'])->first();
            $res[0]['id'] = encrypt($res[0]['id']);
            // $res[0]['country_id'] = $country == null ? 0 : $country['id'];

            if (count($res) > 0) {
                $data = [
                    "status"  => true,
                    "data"    => $res[0]
                ];
                echo json_encode($data);
            } else {
                $message = 'Data Gagal Ditampilkan';
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

    public function generateNo()
    {
        $bln = date('m');
        // $thn = date('Y'); // Tahun awal, full (contoh: 2025)
        $thn2 = date('y'); // Untuk disisipkan dalam kode, biasanya tahun full
        // $last_year = $thn . "-12-31"; // Batas akhir tahun ini

        $no = $this->SupplierLokalModel->get_kode(
            $bln,
            $thn2,
            "LOKAL"
        );

        return response()->setJSON([
            'status' => true,
            'data' => $no
        ]);
    }
    public function saveCustomer()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required|min_length[3]",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong',
                        'min_length' => 'Nama harus memiliki minimal 3 karakter'
                    ]
                ],

                // "email" => [
                //     "rules" => "permit_empty|valid_email",
                //     'errors' => [
                //         'valid_email' => 'Email harus valid'
                //     ]
                // ],
                // "no_npwp" => [
                //     'rules' => 'permit_empty|min_length[15]',
                //     'errors' => [
                //         'min_length' => 'Nomor NPWP harus diisi minimal 15 digit'
                //     ]
                // ],
                // "phone" => [
                //     'rules' => 'permit_empty|min_length[12]',
                //     'errors' => [
                //         'min_length' => 'Nomor HP harus diisi minimal 12 digit'
                //     ]
                // ]
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
                $last_year = date("Y-m-t", strtotime(date('Y') . "-12-31"));
                $kode = $this->request->getPost("kode");
                if (empty($kode)) {
                    $bln = date('m');
                    $thn2 = date('y');
                    $tipeCustomer = $this->request->getPost("tipe_customer");

                    if ($tipeCustomer == "LOKAL") {
                        // KODE CUSTOMER LOKAL
                        $kode = $this->SupplierLokalModel->get_kode(
                            $bln,
                            $thn2,
                            "LOKAL"
                        );
                    } else {
                        // KODE CUSTOMER IMPORT
                        $kode = $this->SupplierLokalModel->get_kode(
                            $bln,
                            $thn2,
                            "INTERNASIONAL"
                        );
                    }
                }
                $values = [
                    "company_id" => $this->this_company_id,
                    "kode" => $kode,
                    "nik" => $this->request->getPost("nik") ?? null,
                    "name" => strtoupper($this->request->getVar("name")) ?? null,
                    "address" => strtoupper($this->request->getVar("address")) ?? null,
                    "no_npwp" => $this->request->getPost("no_npwp") ?? null,
                    "phone" => $this->request->getPost("phone") ?? null,
                    "contact_person" => $this->request->getPost("contact_person") ?? null,
                    "email" => $this->request->getPost("email") ?? null,
                    "postal_code" => $this->request->getPost("parent_postal_code") ?? null,
                    "province_id" => $this->request->getPost("province_parent_id") ?? null,
                    "city_id" => $this->request->getPost("city_parent_id") ?? null,
                    "tipe_pelanggan" => $this->request->getPost("tipe_pelanggan") ?? null,
                    "nik" => $this->request->getPost("nik") ?? null,
                    "termin" => $this->request->getPost("termin") ?? null,
                    "piutang" =>  $this->request->getPost("piutang") ?? null,
                    "currency" => $this->request->getPost("currency") ?? null,
                    "country_id" => $this->request->getPost('country_id') ?? null,
                    "tipe_customer" => $this->request->getPost("tipe_customer") ?? null,
                    "jenis_penjualan" => $this->request->getPost("jenis_penjualan") ?? null,
                    "sales_id" => $this->request->getPost("sales_id") ?? null,
                ];
                // var_dump($values);
                // exit;

                $id = $this->SupplierLokalModel->insert($values);
                if ($id > 0) {
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
    public function updateCustomer()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required|min_length[3]",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong',
                        'min_length' => 'Nama harus memiliki minimal 3 karakter'
                    ]
                ],

                "email" => [
                    "rules" => "permit_empty|valid_email",
                    'errors' => [
                        'valid_email' => 'Email harus valid'
                    ]
                ],
                // "no_npwp" => [
                //     'rules' => 'permit_empty|min_length[15]',
                //     'errors' => [
                //         'min_length' => 'Nomor NPWP harus diisi minimal 15 digit'
                //     ]
                // ],
                // "phone" => [
                //     'rules' => 'permit_empty|min_length[12]',
                //     'errors' => [
                //         'min_length' => 'Nomor HP harus diisi minimal 12 digit'
                //     ]
                // ]
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
                $payload = '';

                $id = decrypt($this->request->getPost("id"));

                $values = [
                    "company_id" => $this->this_company_id,
                    // "user_id" => $this->this_user_id,
                    // "kode" => $this->request->getPost("kode"),
                    "name" => strtoupper($this->request->getVar("name")) ?? null,
                    "address" => strtoupper($this->request->getVar("address")) ?? null,
                    "nik" => $this->request->getPost("nik") ?? null,
                    "no_npwp" => $this->request->getPost("no_npwp") ?? null,
                    "phone" => $this->request->getPost("phone") ?? null,
                    "contact_person" => $this->request->getPost("contact_person") ?? null,
                    "email" => $this->request->getPost("email") ?? null,
                    "postal_code" => $this->request->getPost("parent_postal_code") ?? null,
                    "province_id" => $this->request->getPost("province_parent_id") ?? null,
                    "city_id" => $this->request->getPost("city_parent_id") ?? null,
                    "tipe_pelanggan" => $this->request->getPost("tipe_pelanggan") ?? null,
                    "nik" => $this->request->getPost("nik") ?? null,
                    "termin" => $this->request->getPost("termin") ?? null,
                    "currency" => $this->request->getPost("currency") ?? null,
                    "country_id" => $this->request->getPost('country_id') ?? null,
                    "piutang" =>  $this->request->getPost("piutang") ?? null,
                    "tipe_customer" => $this->request->getPost("tipe_customer") ?? null,
                    "jenis_penjualan" => $this->request->getPost("jenis_penjualan") ?? null,
                    "sales_id" => $this->request->getPost("sales_id") ?? null,
                ];

                if ($values['tipe_customer'] == "LOKAL") {
                    $this->SupplierLokalModel->update($id, [
                        "kode" => $this->request->getPost("kode"),
                    ]);
                }

                if ($this->SupplierLokalModel->update($id, $values)) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
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
    public function deleteCustomer()
    {
        try {
            $id = decrypt($this->request->getPost("id"));

            if (!empty($id)) {
                // $supplier = $this->SupplierLokalModel->find($id);

                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];

                if ($this->SupplierLokalModel->update($id, $values)) {
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
