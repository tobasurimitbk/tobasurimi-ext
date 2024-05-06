<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\SatuansModel;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Satuan extends BaseController
{
    protected $token;
    protected $SatuansModel;
    protected $barangModel;
    protected $barangSpesifikasiModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->SatuansModel = new SatuansModel();
        $this->barangModel = new BarangMasterModel();
        $this->barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
    }

    public function satuan()
    {
        return view('Master/satuan/index');
    }

    public function allSatuan()
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
            "search"        => $search
        ];

        $totalRecords = $this->SatuansModel->total_list(array());
        $totalRecordwithFilter = $this->SatuansModel->total_list($values);

        $res = $this->SatuansModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $number = $row * $rowperpage;

        $data = [];

        for ($i = 0; $i < count($res); $i++) {

            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => encrypt($res[$i]["id"]),
                "kode_satuan" => $res[$i]["kode_satuan"],
                "nama_satuan" => $res[$i]["nama_satuan"],
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

    public function saveSatuan()
    {
        try {
            $rules = [
                "kode_satuan" => [
                    "rules" => "required"
                ],
                "nama_satuan" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $values = [
                    "kode_satuan" => strtoupper($this->request->getVar("kode_satuan")),
                    "nama_satuan" => strtoupper($this->request->getVar("nama_satuan"))
                ];

                $firstData = $this->SatuansModel->where('kode_satuan', strtoupper($values['kode_satuan']))->first();

                if ($firstData != null) {
                    return response()->setJSON([
                        'message' => "Kode satuan sudah ada",
                        'status' => false,
                        'token' => csrf_hash()
                    ]);
                }

                $doubleData = $this->SatuansModel->where('nama_satuan', strtoupper($values['nama_satuan']))->first();

                if ($doubleData != null) {
                    return response()->setJSON([
                        'message' => "Nama satuan sudah ada",
                        'status' => false,
                        'token' => csrf_hash()
                    ]);
                }

                if ($this->SatuansModel->insert($values)) {
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

    public function updateSatuan()
    {
        try {
            $rules = [
                "kode_satuan" => [
                    "rules" => "required"
                ],
                "nama_satuan" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = decrypt($this->request->getPost("id"));

                $values = [
                    "kode_satuan" => $this->request->getPost("kode_satuan"),
                    "nama_satuan" => $this->request->getPost("nama_satuan")
                ];

                if ($this->SatuansModel->update($id, $values)) {
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
        } catch (Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function getByIdSatuan($id = null)
    {
        if (!empty($id)) {
            $id = decrypt($id);
            $res = $this->SatuansModel->get_by_id($id);
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

    public function deleteSatuan()
    {
        try {
            $id = decrypt($this->request->getPost("id"));

            if (!empty($id)) {

                $checkSudahDigunakan = $this->barangSpesifikasiModel
                    ->where('satuan_1', $id)
                    ->first();

                if ($checkSudahDigunakan != null) {
                    return response()->setJSON([
                        'message' => "Satuan sudah digunakan",
                        'status' => false,
                        'token' => csrf_hash()
                    ]);
                }

                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->SatuansModel->update($id, $values)) {
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

    public function dropdownSatuan()
    {

        $dataSatuan = $this->SatuansModel->asObject()->find();

        $data = [
            "data" => $dataSatuan
        ];

        echo json_encode($data);
        return;
    }

    public function exportExcel()
    {

        $filename = "EXPORT_SATUAN";

        $condition = [
            "satuans.deletedAt" => null,
        ];

        $search        = $this->request->getVar("search");
        $sort        = $this->request->getVar("sort");
        $sortType      = $this->request->getVar("sortType");



        $selectQry = "satuans.*";
        $satuanDataQry = $this->SatuansModel->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);



        if ($search) {
            $satuanDataQry->groupStart();
        }

        if ($search) {
            $satuanDataQry->like('satuans.nama_satuan', $search);
        }

        if ($search) {
            $satuanDataQry->Orlike('satuans.kode_satuan', $search);
        }

        if ($search) {
            $satuanDataQry->groupEnd();
        }

        $getAllSatuanData = $satuanDataQry->findAll();




        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();



        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);


        if (empty($getAllSatuanData)) {
            $sheet->setCellValue('A1', 'Tidak Ada Data Satuan');
        } else {
            $sheet->setCellValue('A1', 'NO');
            $sheet->setCellValue('B1', 'KODE SATUAN');
            $sheet->setCellValue('C1', 'NAMA SATUAN');
            $sheet->getStyle('A1:C1')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $no = 1;
            $numRow = 2;

            foreach ($getAllSatuanData as $row) :
                $sheet->setCellValue('A' . $numRow, $no);
                $sheet->setCellValue('B' . $numRow, $row['kode_satuan']);
                $sheet->setCellValue('C' . $numRow, $row['nama_satuan']);


                // Auto size columns A, B, and C
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);

                $no++;
                $numRow++;
            endforeach;


            $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow())
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

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
