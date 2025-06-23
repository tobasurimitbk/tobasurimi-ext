<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\BagianModel;
use App\Models\DivisisModel;
use App\Models\GajiDivisiModel;
use App\Models\JamKerjaModel;
use App\Models\TunjanganModel;

class Divisi extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $DivisisModel;
    protected $gajiDivisiModel;
    protected $tunjanganModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->DivisisModel = new DivisisModel();
        $this->gajiDivisiModel = new GajiDivisiModel();
        $this->tunjanganModel = new TunjanganModel();
    }

    public function divisi()
    {
        $modelJamKerja = new JamKerjaModel();
        $modelTunjangan = new TunjanganModel();

        $dataDivisi = $this->DivisisModel->search_list(array(), 'divisi');
        $isGajiPokok = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_gaji_harian', '1')->first();
        $isCadangan = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_cadangan', '1')->first();
        $dataTypeDivisi = array("PERSONALIA", "UMUM", "GABUNGAN");

        $data = [
            "dataDivisi" => $dataDivisi,
            "jamKerja" => $modelJamKerja->where('company_id', $this->this_company_id)->findAll(),
            "tunjangan" => $modelTunjangan->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('is_gaji_harian', "DESC")->findAll(),
            "isGajiPokok" => $isGajiPokok,
            "isCadangan" => $isCadangan,
            "dataTypeDivisi" => $dataTypeDivisi
        ];

        return view('Master/divisi/index', $data);
    }

    public function allDivisi()
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

        if ($columnName == "jamKerja") {
            $columnName = "jam_kerja.jenis";
        }

        if ($columnName == "totalBagian") {
            $columnName = "jam_kerja.id";
        }

        $values = [
            "company_id"    => $this->this_company_id,
            "search"        => $search,
            "type_divisi"   => $this->request->getVar("divisiType")
        ];

        $totalRecords = $this->DivisisModel->total_list($values);
        $totalRecordwithFilter = $this->DivisisModel->total_list($values);

        $res = $this->DivisisModel->search_list($values, $columnName . " " . $columnSortOrder, $row, $rowperpage);

        $data = [];

        $bagianModel = new BagianModel();
        $gajiDivisiModel = new GajiDivisiModel();

        for ($i = 0; $i < count($res); $i++) {
            $totalBagian = $bagianModel->where('division_id', $res[$i]['id'])->where('deletedAt', null)->findAll();
            $totalKomponenGaji = $gajiDivisiModel->where('division_id', $res[$i]['id'])->where('deletedAt', null)->findAll();
            $data[] = array(
                "no" => ($row + $i + 1),
                "id" => encrypt($res[$i]["id"]),
                "divisi" => strtoupper($res[$i]["divisi"]),
                "totalBagian" => count($totalBagian) == 0 ? '-' : count($totalBagian) . " BAGIAN",
                "komponenGaji" => count($totalKomponenGaji) == 0 ? 'BELUM DIATUR' : 'SUDAH DIATUR',
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

    public function saveDivisi()
    {
        try {
            $rules = [
                "divisi" => [
                    "rules" => "required"
                ],
                "type_divisi" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {

                $first = $this->DivisisModel->where('company_id', $this->this_company_id)->where('divisi', strtoupper($this->request->getVar('divisi')))->first();
                if ($first != null) {
                    return response()->setJSON([
                        'status' => false,
                        'token' => csrf_hash(),
                        'message' => "Departemen " . strtoupper($this->request->getVar('divisi')) . " sudah ada"
                    ]);
                }

                $komponenGaji = $this->request->getPost('komponenGaji');

                if (empty($komponenGaji)) {
                    return response()->setJSON([
                        "status"    => false,
                        "token" => csrf_hash(),
                        "message"   => "Checklist minimal satu komponen gaji !",
                    ]);
                }

                $isGajiPokok = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_gaji_harian', '1')->first();
                $isCadangan = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_cadangan', '1')->first();

                if ($isGajiPokok == null || $isCadangan == null) {
                    return response()->setJSON([
                        "status"    => false,
                        "token" => csrf_hash(),
                        "message"   => "Komponen Gaji Pokok dan Komponen Tunjangan Wajib Ada !",
                    ]);
                }

                $divisiInserted = $this->DivisisModel->insert([
                    "company_id" => $this->this_company_id,
                    "divisi" => strtoupper($this->request->getVar("divisi")),
                    "jam_kerja_id" => $this->request->getPost('jam_kerja_id'),
                    "type_divisi"   => $this->request->getVar("type_divisi")
                ]);

                foreach ($komponenGaji as $k) {
                    if (in_array($k, array_keys($_POST))) {
                        $angka = preg_replace("/[^0-9,]/", "", $this->request->getVar($k));
                        $angka = str_replace(",", ".", $angka);
                        $angkaDesimal = number_format((float) $angka, 3, '.', '');

                        $this->gajiDivisiModel->insert([
                            "tunjangan_id" => $k,
                            "division_id" => $divisiInserted,
                            "company_id" => $this->this_company_id,
                            "nominal" => $angkaDesimal
                        ]);
                    }
                }

                return \response()->setJSON([
                    "status"    => true,
                    "message"   => "Data Berhasil disimpan",
                    'token' => csrf_hash()
                ]);
            } else {
                return \response()->setJSON([
                    "status"    => \false,
                    "message"   => "Terjadi kesalahan saat validasi data",
                    'token' => csrf_hash()
                ]);
            }
        } catch (\Exception $e) {
            return \response()->setJSON([
                "status"    => \false,
                "message"   => $e->getMessage(),
            ]);
        }
    }

    public function updateDivisi()
    {
        try {
            $rules = [
                "divisi" => [
                    "rules" => "required"
                ],
                "type_divisi" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {

                $id = decrypt($this->request->getPost("id"));

                $first = $this->DivisisModel->where('company_id', $this->this_company_id)->where('divisi', strtoupper($this->request->getVar('divisi')))->where('id !=', $id)->first();
                if ($first != null) {
                    return response()->setJSON([
                        'status' => false,
                        'token' => csrf_hash(),
                        'message' => "Departemen " . strtoupper($this->request->getVar('divisi')) . " sudah digunakan"
                    ]);
                }

                $komponenGaji = $this->request->getPost('komponenGaji');

                if (empty($komponenGaji)) {
                    return \response()->setJSON([
                        "status"    => \false,
                        "message"   => "Checklist minimal satu komponen gaji",
                    ]);
                }

                $isGajiPokok = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_gaji_harian', '1')->first();
                $isCadangan = $this->tunjanganModel->where('company_id', $this->this_company_id)->where('is_cadangan', '1')->first();

                if ($isGajiPokok == null || $isCadangan == null) {
                    return response()->setJSON([
                        "status"    => false,
                        "token" => csrf_hash(),
                        "message"   => "Komponen Gaji Pokok dan Komponen Tunjangan Wajib Ada !",
                    ]);
                }

                $this->DivisisModel->update($id, [
                    "company_id" => $this->this_company_id,
                    "divisi" => strtoupper($this->request->getVar("divisi")),
                    "jam_kerja_id" => $this->request->getPost('jam_kerja_id'),
                    "type_divisi"   => $this->request->getVar("type_divisi")
                ]);

                $this->gajiDivisiModel->where('division_id', $id)->delete();

                foreach ($komponenGaji as $k) {
                    if (in_array($k, array_keys($_POST))) {
                        $angka = preg_replace("/[^0-9,]/", "", $this->request->getVar($k));
                        $angka = str_replace(",", ".", $angka);
                        $angkaDesimal = number_format((float) $angka, 3, '.', '');

                        $this->gajiDivisiModel->insert([
                            "tunjangan_id" => $k,
                            "division_id" => $id,
                            "company_id" => $this->this_company_id,
                            "nominal" => $angkaDesimal
                        ]);
                    }
                }

                return \response()->setJSON([
                    "status"    => true,
                    "message"   => "Data Berhasil diupdate",
                    'token' => csrf_hash()
                ]);
            } else {
                return \response()->setJSON([
                    "status"    => \false,
                    "message"   => "Terjadi kesalahan saat validasi data",
                    'token' => csrf_hash()
                ]);
            }
        } catch (\Exception $e) {
            return \response()->setJSON([
                "status"    => \false,
                "message"   => $e->getMessage(),
            ]);
        }
    }

    public function getByIdDivisi($id = null)
    {
        $id = decrypt($id);
        $data = $this->DivisisModel->get_by_id($id);

        return \response()->setJSON([
            'status' => true,
            'data' => (\count($data) == 0) ? null : (object)$this->DivisisModel->get_by_id($id)[0],
            'komponenGaji' => $this->DivisisModel->getTunjanganByDivisi($id),
        ]);
    }

    public function deleteDivisi()
    {
        try {
            $id = decrypt($this->request->getPost("id"));

            if (!empty($id)) {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];
                if ($this->DivisisModel->update($id, $values)) {
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

    public function dropdownDivisi()
    {
        $dataDivisi = $this->DivisisModel->get_by_company_id($this->this_company_id);

        /*
        $responseDivisi = curl_request("GET", "/divisis/all", $this->token);
        $dataDivisi = [];
        if ($responseDivisi["code"] === 200) {
            $dataDivisi = json_decode($responseDivisi["body"])->data;
        }
        */

        $data = [
            "data" => $dataDivisi
        ];

        echo json_encode($data);
        return;
    }
}
