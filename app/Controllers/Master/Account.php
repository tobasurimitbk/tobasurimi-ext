<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Account extends BaseController
{
    protected $token;
    protected $Sub_AkunsModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $metaDataModel;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->KategoriAkunsModel = new KategoriAkunsModel();
        $this->HeaderAkunsModel = new HeaderAkunsModel();
        $this->metaDataModel = new MetadataModel();
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function account()
    {
        return view('Master/account/index');
    }

    public function dropdownKategoriAccount()
    {
        $search = $this->request->getGet('search');
        $page = $this->request->getGet('page') ?? 1;
        $limit = 10;
        $offset = ($page - 1) * 10;

        $dataQry = $this->KategoriAkunsModel;

        if (!empty($search)) {
            $dataQry->like('nama_kategori', $search);
        }

        $totalData = $dataQry->countAllResults(false);
        $subAccData = $dataQry->select('id, nama_kategori AS text')
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt is null')
            ->orderBy('nama_kategori', 'asc')
            ->findAll($limit, $offset);


        $data = [
            "results"   => $subAccData,
            "pagination" => [
                "more"  => $offset < $totalData
            ]
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownHeaderAccount()
    {
        $search = $this->request->getGet('search');

        $builder = $this->HeaderAkunsModel
            ->where('company_id', $this->this_company_id);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama_header', $search)
                ->orLike('no_header', $search)
                ->groupEnd();
        }

        $subAccData = $builder
            ->select("id, CONCAT(no_header, ' - ', nama_header) AS text")
            ->orderBy('nama_header', 'asc')
            ->findAll();

        return $this->response->setJSON([
            'results' => $subAccData
        ]);
    }

    public function dropdownSubAccount()
    {
        $search = $this->request->getGet('search');
        $page = $this->request->getGet('page') ?? 1;
        $limit = 10;
        $offset = ($page - 1) * 10;

        $dataQry = $this->Sub_AkunsModel;

        if (!empty($search)) {
            $dataQry->like('nama_sub', $search);
        }

        $totalData = $dataQry->countAllResults(false);
        $subAccData = $dataQry->select('id, nama_sub AS text')
            ->where('company_id', $this->this_company_id)
            ->orderBy('nama_sub', 'asc')
            ->findAll($limit, $offset);


        $data = [
            "results"   => $subAccData,
            "pagination" => [
                "more"  => $offset < $totalData
            ]
        ];

        echo json_encode($data);
        return;
    }

    public function allKategoriAccount()
    {
        $draw = $this->request->getVar('draw');
        $row = $this->request->getVar('start');
        $rowperpage = $this->request->getVar('length');
        $temp = $this->request->getVar('order');
        $columnIndex = $temp[0]['column']; // Column index

        $temp = $this->request->getVar('columns');
        $columnName = $temp[$columnIndex]['data']; // Column index

        $temp = $this->request->getVar('order');
        $columnSortOrder = $temp[0]['dir']; // Column index

        $search = $this->request->getVar('search');
        //$searchValue = $temp['value']; // Column index

        $values = [
            "company_id"    => $this->this_company_id,
            "search"        => $search
        ];

        $totalRecords = $this->KategoriAkunsModel->total_list(array());
        $totalRecordwithFilter = $this->KategoriAkunsModel->total_list($values);

        $res = $this->KategoriAkunsModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);
        //        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "kelompok_akun" => $res[$i]["kelompok_akun"],
                "no_kategori" => $res[$i]["no_kategori"],
                "nama_kategori" => $res[$i]["nama_kategori"],
            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $this->response->setJSON($response);
    }

    public function saveKategoriAccount()
    {
        try {
            $rules = [
                "kelompok_akun_id_kategori" => [
                    "rules" => "required"
                ],
                "kode_akun_kategori" => [
                    "rules" => "required"
                ],
                "nama_akun_kategori" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "company_id"    => $this->this_company_id,
                    "kelompok_id" => formatter($this->request->getPost("kelompok_akun_id_kategori"), "STR_TO_INT"),
                    "no_kategori" => $this->request->getPost("kode_akun_kategori"),
                    "nama_kategori" => $this->request->getPost("nama_akun_kategori"),
                ];
                
                $checkKodeAkun = $this->KategoriAkunsModel
                ->where('no_kategori', $this->request->getPost("kode_akun_kategori"))
                ->where('company_id', $this->this_company_id)
                ->first();

                if ($checkKodeAkun) {
                    $data = [
                        "status"    => false,
                        "message"   => "Kode Kategori Sudah Digunakan",
                        "payload"   => $values,
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                    return;
                }

                if ($this->KategoriAkunsModel->insert($values)) {
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

    public function updateKategoriAccount()
    {
        try {
            $rules = [
                "kelompok_akun_id_kategori" => [
                    "rules" => "required"
                ],
                "kode_akun_kategori" => [
                    "rules" => "required"
                ],
                "nama_akun_kategori" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id_kategori");

                $values = [
                    "kelompok_id" => formatter($this->request->getPost("kelompok_akun_id_kategori"), "STR_TO_INT"),
                    "no_kategori" => $this->request->getPost("kode_akun_kategori"),
                    "nama_kategori" => $this->request->getPost("nama_akun_kategori"),
                ];

                if ($this->KategoriAkunsModel->update($id, $values)) {
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

    public function getByIdKategoriAccount($id = null)
    {
        if (!empty($id)) {
            $res = $this->KategoriAkunsModel->get_by_id($id);
            $response = curl_request("GET", "/kategoriAkun/$id", $this->token);
            if (count($res)) {
                $data = [
                    "status"  => true,
                    "data"  => (object) $res[0],
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

    public function deleteKategoriAccount()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->KategoriAkunsModel->update($id, $values)) {
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

    public function sheetKategoriAccount()
    {

        $list = $this->KategoriAkunsModel
            ->select('kategori_akuns.*, metadata.value as kelompok_akun')
            ->join('metadata', 'kategori_akuns.kelompok_id = metadata.id', 'left')
            ->where('kategori_akuns.deletedAt', null)
            ->where('company_id', $this->this_company_id)
            ->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $dataStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Kelompok Akun')
            ->setCellValue('C1', 'No. Kategori Akun')
            ->setCellValue('D1', 'Nama Kategori Akun');

        $sheet->getStyle('A1:D1')->applyFromArray($headerStyleArray);

        $no = 1;
        $column = 2;
        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l['kelompok_akun'])
                ->setCellValue('C' . $column,  $l['no_kategori'])
                ->setCellValue('D' . $column,  $l['nama_kategori']);

            $sheet->getStyle('A' . $column . ':D' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Sheet Kategori Akun';
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Kategori Akun';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
    public function importKategoriAccount()
    {
        $rules = [
            "file" => [
                'rules' => 'uploaded[file]|ext_in[file,xlsx]',
                'errors' => [
                    'uploaded' => 'Tidak ada file yang di-upload.',
                    'ext_in' => 'File yang di-upload harus berupa file Excel (.xlsx).',
                ],

            ],
        ];

        if ($this->validate($rules)) {

            $file = $this->request->getFile('file');

            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();


            $data = [];
            $gagalArr = [];
            $berhasilTotal = 0;
            $rowIterator = $worksheet->getRowIterator(2);
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }
            for ($i = 0; $i < count($data); $i++) {
                $kelompok = $this->metaDataModel->where('value', $data[$i][0])
                    ->where('deletedAt', null)
                    ->first();
                $noKategori = $this->KategoriAkunsModel->where('no_kategori', $data[$i][1])->where('company_id', $this->this_company_id)->first();
                if ($data[$i][1] != null) {
                    if ($noKategori == null) {
                        // Kategori Akun INSERTED
                        $this->KategoriAkunsModel->insert([
                            'company_id' => $this->this_company_id,
                            'kelompok_id' => $kelompok['id'],
                            'no_kategori' => $data[$i][1],
                            'nama_kategori' => $data[$i][2],
                        ]);
                        $berhasilTotal++;
                    } else {
                        array_push($gagalArr, $data[$i]);
                    }
                }
            }

            $gagalTotal = count($gagalArr);

            return response()->setJSON([
                'message' => "Berhasil Import : $berhasilTotal Data, Gagal Import : $gagalTotal",
                'status' => true,
                'gagal' => $gagalArr,
                'token' => csrf_hash()
            ]);
        } else {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJSON($data);
        }
    }

    public function allHeaderAccount()
    {
        $draw = $this->request->getVar('draw');
        $row = $this->request->getVar('start');
        $rowperpage = $this->request->getVar('length');
        $temp = $this->request->getVar('order');
        $columnIndex = $temp[0]['column']; // Column index

        $temp = $this->request->getVar('columns');
        $columnName = $temp[$columnIndex]['data']; // Column index

        $temp = $this->request->getVar('order');
        $columnSortOrder = $temp[0]['dir']; // Column index

        $search = $this->request->getVar('search');
        //$searchValue = $temp['value']; // Column index

        $values = [
            "company_id"    => $this->this_company_id,
            "search"        => $search
        ];

        $totalRecords = $this->HeaderAkunsModel->total_list(array());
        $totalRecordwithFilter = $this->HeaderAkunsModel->total_list($values);

        $res = $this->HeaderAkunsModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);
        //        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "nama_kategori" => $res[$i]["nama_kategori"],
                "no_header" => $res[$i]["no_header"],
                "nama_header" => $res[$i]["nama_header"],
            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $this->response->setJSON($response);
    }

    public function saveHeaderAccount()
    {
        try {
            $rules = [
                "category_id_header" => [
                    "rules" => "required"
                ],
                "kode_akun_header" => [
                    "rules" => "required"
                ],
                "nama_akun_header" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "company_id"    => $this->this_company_id,
                    "kategori_id" => formatter($this->request->getPost("category_id_header"), "STR_TO_INT"),
                    "no_header" => $this->request->getPost("kode_akun_header"),
                    "nama_header" => $this->request->getPost("nama_akun_header"),
                ];

                $checkKodeAkun = $this->HeaderAkunsModel
                ->where('no_header', $this->request->getPost("kode_akun_header"))
                ->where('company_id', $this->this_company_id)
                ->first();

                if ($checkKodeAkun) {
                    $data = [
                        "status"    => false,
                        "message"   => "Kode Header Sudah Digunakan",
                        "payload"   => $values,
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                    return;
                }

                if ($this->HeaderAkunsModel->insert($values)) {
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

    public function updateHeaderAccount()
    {
        try {
            $rules = [
                "category_id_header" => [
                    "rules" => "required"
                ],
                "kode_akun_header" => [
                    "rules" => "required"
                ],
                "nama_akun_header" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id_header");

                $values = [
                    "kategori_id" => formatter($this->request->getPost("category_id_header"), "STR_TO_INT"),
                    "no_header" => $this->request->getPost("kode_akun_header"),
                    "nama_header" => $this->request->getPost("nama_akun_header"),
                ];

                if ($this->HeaderAkunsModel->update($id, $values)) {
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

    public function getByIdHeaderAccount($id = null)
    {
        if (!empty($id)) {
            $res = $this->HeaderAkunsModel->get_by_id($id);
            if (count($res)) {
                $data = [
                    "status"  => true,
                    "data"  => (object) $res[0],
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

    public function deleteHeaderAccount()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->HeaderAkunsModel->update($id, $values)) {
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

    public function sheetHeaderAccount()
    {

        $list = $this->HeaderAkunsModel
            ->select('header_akuns.*,kategori_akuns.nama_kategori')
            ->join('kategori_akuns', 'kategori_akuns.id = header_akuns.kategori_id', 'left')
            ->where('header_akuns.deletedAt', null)
            ->where('header_akuns.company_id', $this->this_company_id)
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $dataStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Kategori Akun')
            ->setCellValue('C1', 'No. Header Akun')
            ->setCellValue('D1', 'Nama Header Akun');

        $sheet->getStyle('A1:D1')->applyFromArray($headerStyleArray);

        $no = 1;
        $column = 2;
        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  empty($l['nama_kategori']) ? "-" : $l['nama_kategori'])
                ->setCellValue('C' . $column,  $l['no_header'])
                ->setCellValue('D' . $column,  $l['nama_header']);

            $sheet->getStyle('A' . $column . ':D' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Sheet Header Akun';
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Header Akun';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
    public function importHeaderAccount()
    {
        $rules = [
            "file" => [
                'rules' => 'uploaded[file]|ext_in[file,xlsx]',
                'errors' => [
                    'uploaded' => 'Tidak ada file yang di-upload.',
                    'ext_in' => 'File yang di-upload harus berupa file Excel (.xlsx).',
                ],

            ],
        ];

        if ($this->validate($rules)) {

            $file = $this->request->getFile('file');

            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();


            $data = [];
            $gagalArr = [];
            $berhasilTotal = 0;
            $rowIterator = $worksheet->getRowIterator(2);
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            for ($i = 0; $i < count($data); $i++) {
                $kategoriAkun = $this->KategoriAkunsModel->where('nama_kategori', $data[$i][0])
                    ->where('company_id', $this->this_company_id)
                    ->where('deletedAt', null)
                    ->first();
                $noHeader = $this->HeaderAkunsModel->where('no_header', $data[$i][1])->where('company_id', $this->this_company_id)->first();
                if ($data[$i][1] != null) {
                    if ($noHeader == null && $kategoriAkun != null) {
                        // Header Akun INSERTED
                        $this->HeaderAkunsModel->insert([
                            'company_id' => $this->this_company_id,
                            'kategori_id' => $kategoriAkun['id'],
                            'no_header' => $data[$i][1],
                            'nama_header' => $data[$i][2],
                        ]);
                        $berhasilTotal++;
                    } else {
                        array_push($gagalArr, $data[$i]);
                    }
                }
            }

            $gagalTotal = count($gagalArr);

            return response()->setJSON([
                'message' => "Berhasil Import : $berhasilTotal Data, Gagal Import : $gagalTotal",
                'status' => true,
                'gagal' => $gagalArr,
                'token' => csrf_hash()
            ]);
        } else {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJSON($data);
        }
    }

    public function allSubAccount()
    {

        $draw = $this->request->getVar('draw');
        $row = $this->request->getVar('start');
        $rowperpage = $this->request->getVar('length');
        $temp = $this->request->getVar('order');
        $columnIndex = $temp[0]['column']; // Column index

        $temp = $this->request->getVar('columns');
        $columnName = $temp[$columnIndex]['data']; // Column index

        $temp = $this->request->getVar('order');
        $columnSortOrder = $temp[0]['dir']; // Column index

        $search = $this->request->getVar('search');
        $status_sub = $this->request->getVar('status_sub');
        //$searchValue = $temp['value']; // Column index

        $values = [
            "company_id"    => $this->this_company_id,
            "search"        => $search,
            "status_sub"    => $status_sub
        ];

        $totalRecords = $this->Sub_AkunsModel->total_list(array());
        $totalRecordwithFilter = $this->Sub_AkunsModel->total_list($values);

        $res = $this->Sub_AkunsModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => $res[$i]["id"],
                "kategori_id" => $res[$i]["kategori_id"],
                "header_id" => $res[$i]["header_id"],
                "coa_id" => $res[$i]["coa_id"],
                "nama_kategori" => $res[$i]["nama_kategori"],
                "no_header" => $res[$i]["no_header"],
                "no_sub" => $res[$i]["no_sub"],
                "nama_sub" => $res[$i]["nama_sub"],
                "nama_header" => $res[$i]["nama_header"],
                "akun_coa" => $res[$i]["akun_coa"],
                "status" => $res[$i]["status"],
            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $this->response->setJSON($response);
    }

    public function saveSubAccount()
    {
        try {
            $rules = [
                "header_id_sub" => [
                    "rules" => "required"
                ],
                "kode_akun_sub" => [
                    "rules" => "required"
                ],
                "nama_akun_sub" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $res_header = $this->HeaderAkunsModel->get_by_id($this->request->getPost("header_id_sub"));

                $values = [
                    "company_id"    => $this->this_company_id,
                    "header_id" => formatter($this->request->getPost("header_id_sub"), "STR_TO_INT"),
                    "kategori_id" => $res_header[0]["kategori_id"],
                    "coa_id" => formatter($this->request->getPost("coa_id_sub"), "STR_TO_INT"),
                    "no_sub" => $this->request->getPost("kode_akun_sub"),
                    "nama_sub" => $this->request->getPost("nama_akun_sub"),
                    "status" => !empty($this->request->getPost("status_sub")) ? "Aktif" : "Void"
                ];

                $checkKodeAkun = $this->Sub_AkunsModel
                ->where('no_sub', $this->request->getPost("kode_akun_sub"))
                ->where('company_id', $this->this_company_id)
                ->first();

                if ($checkKodeAkun) {
                    $data = [
                        "status"    => false,
                        "message"   => "Kode Akun Sudah Digunakan",
                        "payload"   => $values,
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                    return;
                }

                if ($this->Sub_AkunsModel->insert($values)) {
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

    public function updateSubAccount()
    {
        try {
            $rules = [
                "header_id_sub" => [
                    "rules" => "required"
                ],
                "kode_akun_sub" => [
                    "rules" => "required"
                ],
                "nama_akun_sub" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id_sub");
                $res_header = $this->HeaderAkunsModel->get_by_id($this->request->getPost("header_id_sub"));
                $values = [
                    "header_id" => formatter($this->request->getPost("header_id_sub"), "STR_TO_INT"),
                    "kategori_id" => $res_header[0]["kategori_id"],
                    "coa_id" => formatter($this->request->getPost("coa_id_sub"), "STR_TO_INT"),
                    "no_sub" => $this->request->getPost("kode_akun_sub"),
                    "nama_sub" => $this->request->getPost("nama_akun_sub"),
                    "status" => !empty($this->request->getPost("status_sub")) ? "Aktif" : "Void"
                ];

                if ($this->Sub_AkunsModel->update($id, $values)) {
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

    public function updateStatusSubAccount()
    {
        try {
            $id = $this->request->getPost("id");

            $status = !empty($this->request->getPost("status")) ? "Aktif" : "Void";

            if ($this->Sub_AkunsModel->update_status_by_id($id, $status)) {
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

    public function getByIdSubAccount($id = null)
    {
        if (!empty($id)) {
            $res = $this->Sub_AkunsModel->get_by_id($id);

            if (count($res)) {

                $data = [
                    "status"  => true,
                    "data"  => (object) $res[0],
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

    public function deleteSubAccount()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->Sub_AkunsModel->update($id, $values)) {
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

    public function sheetSubAccount()
    {

        $list = $this->Sub_AkunsModel
            ->select('sub_akuns.*, kategori_akuns.nama_kategori, m1.value as kelompok_akun, m2.value as akun_coa,
            header_akuns.no_header, header_akuns.nama_header')
            ->join('kategori_akuns', 'kategori_akuns.id = sub_akuns.kategori_id', 'left')
            ->join('header_akuns', 'sub_akuns.header_id = header_akuns.id', 'left')
            ->join('metadata m1', 'kategori_akuns.kelompok_id = m1.id', 'left')
            ->join('metadata m2', 'sub_akuns.coa_id = m2.id', 'left')
            ->where('sub_akuns.deletedAt', null)
            ->where('sub_akuns.company_id', $this->this_company_id)
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $dataStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Kategori Akun')
            ->setCellValue('C1', 'No. Header Akun')
            ->setCellValue('D1', 'Header Akun')
            ->setCellValue('E1', 'No. Sub Akun')
            ->setCellValue('F1', 'Nama Sub Akun')
            ->setCellValue('G1', 'COA')
            ->setCellValue('H1', 'Status');

        $sheet->getStyle('A1:H1')->applyFromArray($headerStyleArray);

        $no = 1;
        $column = 2;
        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l['nama_kategori'])
                ->setCellValue('C' . $column,  $l['no_header'])
                ->setCellValue('D' . $column,  $l['nama_header'])
                ->setCellValue('E' . $column,  $l['no_sub'])
                ->setCellValue('F' . $column,  $l['nama_sub'])
                ->setCellValue('G' . $column,  $l['akun_coa'])
                ->setCellValue('H' . $column,  $l['status'] == 'Void' ? 'Tidak Aktif' : 'Aktif');

            $sheet->getStyle('A' . $column . ':H' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Sheet Sub Akun';
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Sub Akun';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
    public function importSubAccount()
    {
        $rules = [
            "file" => [
                'rules' => 'uploaded[file]|ext_in[file,xlsx]',
                'errors' => [
                    'uploaded' => 'Tidak ada file yang di-upload.',
                    'ext_in' => 'File yang di-upload harus berupa file Excel (.xlsx).',
                ],

            ],
        ];

        if ($this->validate($rules)) {

            $file = $this->request->getFile('file');

            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            $data = [];
            $gagalArr = [];
            $berhasilTotal = 0;
            $rowIterator = $worksheet->getRowIterator(2);
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            for ($i = 0; $i < count($data); $i++) {
                $headerAkun = $this->HeaderAkunsModel
                    ->where('no_header',  $data[$i][0])
                    ->where('company_id', $this->this_company_id)
                    ->where('deletedAt', null)
                    ->first();
                $noSub = $this->Sub_AkunsModel
                    ->where('no_sub', $data[$i][1])
                    ->where('company_id', $this->this_company_id)
                    ->where('deletedAt', null)
                    ->first();

                $res_header = $this->HeaderAkunsModel->get_by_id($headerAkun['id']);
                if ($data[$i][1] != null) {
                    if ($noSub == null && $headerAkun != null) {
                        // sub Akun INSERTED
                        $this->Sub_AkunsModel->insert([
                            "company_id"    => $this->this_company_id,
                            "header_id" => $headerAkun['id'],
                            "kategori_id" => $res_header[0]["kategori_id"],
                            // "coa_id" => $coa['id'],
                            "no_sub" => $data[$i][1],
                            "nama_sub" => $data[$i][2],
                            "status" => $data[$i][4] == 'Aktif' ? "Aktif" : "Void"
                        ]);
                        $berhasilTotal++;
                    } else {
                        array_push($gagalArr, $data[$i]);
                    }
                }
            }

            $gagalTotal = count($gagalArr);

            return response()->setJSON([
                'message' => "Berhasil Import : $berhasilTotal Data, Gagal Import : $gagalTotal",
                'status' => true,
                'gagal' => $gagalArr,
                'token' => csrf_hash()
            ]);
        } else {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJSON($data);
        }
    }

    public function dropdownAPAR()
    {

        $dataAPAR = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $data = [
            "data" => $dataAPAR
        ];

        echo json_encode($data);
        return;
    }

    public function getSubAkun() { 
        $Sub_AkunsModel = new Sub_AkunsModel();
        $search = trim($this->request->getGet('search')); // Ambil & bersihkan input pencarian
    
        $subAkun = $Sub_AkunsModel
            ->select('id, nama_sub')
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->like('nama_sub', $search)
            ->findAll(10); // Batasi hasil max 10 biar efisien

        return $this->response->setJSON($subAkun);
    }    
	
}
