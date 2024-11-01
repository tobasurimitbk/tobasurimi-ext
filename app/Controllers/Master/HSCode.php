<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\HsCodesModel;
use App\Models\SatuansModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HSCode extends BaseController
{
    protected $token;
    protected $HsCodesModel;
    protected $satuanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->HsCodesModel = new HsCodesModel();
        $this->satuanModel = new SatuansModel();
    }

    public function hsCode()
    {
        return view('Master/hsCode/index');
    }

    public function allHSCode()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $hsData = $this->HsCodesModel->getList($condition, $addCondition, $limit, $offset);

        $dataHS = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($hsData['data'] as $data) {
            array_push($dataHS, [
                "no"                => $no++,
                "id"                => $data->id,
                "komoditi"          => $data->komoditi,
                "code"              => $data->code,
                "uraian_barang"     => $data->uraian_barang,
                "kode_satuan"       => $data->kode_satuan,
                "nama_satuan"       => $data->nama_satuan
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $hsData['totalData'],
            "recordsFiltered"   => $hsData['totalFilteredData'],
            "data"              => $dataHS,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }


    public function import()
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

        if (!$this->validate($rules)) {
            return response()->setJSON([
                'message' => $this->validator->getError('file'),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $file = $this->request->getFile('file');

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();

            // Cek apakah ada data selain header
            if ($worksheet->getHighestRow() <= 1) {
                return response()->setJSON([
                    'message' => 'File kosong atau tidak ada data yang dapat diimpor.',
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            $data = [];
            $rowIterator = $worksheet->getRowIterator(2);
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            $berhasilTotal = 0;
            $gagalTotal = 0;
            $totalData = count($data);

            foreach ($data as $row) {
                $komoditi = isset($row[0]) ? trim($row[0]) : null;
                $code = isset($row[1]) ? trim($row[1]) : null;
                $uraianBarang = isset($row[2]) ? trim($row[2]) : null;
                $unit = isset($row[3]) ? trim($row[3]) : null;
                $nilaiTarif = is_numeric($row[4]) ? (float)$row[4] : null;
                if ($komoditi && $code && $uraianBarang && $unit) {
                    $satuan = $this->satuanModel->where('kode_satuan', $unit)->first();
                    $hsCode = $this->HsCodesModel->where('code', $code)->first();

                    if ($satuan && !$hsCode) {
                        $this->HsCodesModel->insert([
                            'komoditi' => $komoditi,
                            'code' => $code,
                            'uraian_barang' => $uraianBarang,
                            'unit' => $satuan['id'],
                            'nilai_tarif' => $nilaiTarif
                        ]);
                        $berhasilTotal++;
                    } else {
                        $gagalTotal++;
                    }
                } else {
                    $gagalTotal++;
                }
            }

            return response()->setJSON([
                'message' => "Berhasil Import: $berhasilTotal Data, Gagal Import: $gagalTotal",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            return response()->setJSON([
                'message' => "Terjadi kesalahan saat memproses file: " . $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
    }



    public function export()
    {
        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $hsData = $this->HsCodesModel->getList([], $addCondition, 10000000, 0);

        $dataHS = [];

        foreach ($hsData['data'] as $data) {
            array_push($dataHS, [
                "komoditi"          => $data->komoditi,
                "code"              => $data->code,
                "uraian_barang"     => $data->uraian_barang,
                "kode_satuan"       => $data->kode_satuan,
                "nilai_tarif"       => $data->nilai_tarif
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);


        if (empty($dataHS)) {
            $sheet->setCellValue('A1', 'Tidak Ada Data HS Code');
        } else {
            $filename = "EXPORT_HS_CODE" . date('d/m/Y');

            $sheet->setCellValue('A1', 'KOMODITI');
            $sheet->setCellValue('B1', 'KODE');
            $sheet->setCellValue('C1', 'URAIAN BARANG');
            $sheet->setCellValue('D1', 'KODE SATUAN');
            $sheet->setCellValue('E1', 'NILAI TARIF');
            $sheet->getStyle('A1:E1')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $numRow = 2;
            foreach ($dataHS as $d) {
                $sheet->setCellValue('A' . $numRow, $d['komoditi']);
                $sheet->setCellValue('B' . $numRow, $d['code']);
                $sheet->setCellValue('C' . $numRow, $d['uraian_barang']);
                $sheet->setCellValue('D' . $numRow, $d['kode_satuan']);
                $sheet->setCellValue('E' . $numRow, $d['nilai_tarif']);
                $numRow++;
            }

            $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow())
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            ob_start();
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $excelOutput = ob_get_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');
            header('Content-Length: ' . strlen($excelOutput));

            echo $excelOutput;
            exit();
        }
    }

    public function saveHSCode()
    {
        try {
            $rules = [
                "komoditi" => [
                    "rules" => "required"
                ],
                "code" => [
                    "rules" => "required|is_unique[hs_codes.code]",
                    'errors' => [
                        'required' => 'HS Codes wajib diisi',
                        'is_unique' => 'HS Codes sudah ada'
                    ]
                ],
                "uraian_barang" => [
                    "rules" => "required"
                ],
                "unit" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "komoditi" => $this->request->getPost("komoditi"),
                    "code" => $this->request->getPost("code"),
                    "uraian_barang" => $this->request->getPost("uraian_barang"),
                    "unit" => $this->request->getPost("unit"),
                    "nilai_tarif" => $this->request->getVar('nilai_tarif')
                ];

                if ($this->HsCodesModel->insert($values)) {
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
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
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

    public function updateHSCode()
    {
        try {
            $rules = [
                "komoditi" => [
                    "rules" => "required"
                ],
                "code" => [
                    "rules" => "required"
                ],
                "uraian_barang" => [
                    "rules" => "required"
                ],
                "unit" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $values = [
                    "komoditi" => $this->request->getPost("komoditi"),
                    "code" => $this->request->getPost("code"),
                    "uraian_barang" => $this->request->getPost("uraian_barang"),
                    "unit" => $this->request->getPost("unit"),
                    "nilai_tarif" => $this->request->getVar('nilai_tarif')
                ];

                $hsCodeSameName = $this->HsCodesModel
                    ->where('code', $values['code'])
                    ->where('id !=', $id)
                    ->first();

                if ($hsCodeSameName) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "HS Code sudah digunakan.",
                        'token' => csrf_hash()
                    ]);
                }

                if ($this->HsCodesModel->update($id, $values)) {
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

    public function getByIdHSCode($id = null)
    {
        if (!empty($id)) {
            $res = $this->HsCodesModel->get_by_id($id);

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

    public function deleteHSCode()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->HsCodesModel->update($id, $values)) {
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

    public function dropdownHSCode()
    {
        $dataKodeHS = $this->HsCodesModel->asObject()->find();

        $data = [
            "data" => $dataKodeHS
        ];

        echo json_encode($data);
        return;
    }
}
