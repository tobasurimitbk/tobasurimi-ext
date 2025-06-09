<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\MetadataModel;
use App\Models\SatuansModel;
use App\Models\DivisisModel;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Barang extends BaseController
{
    protected $this_company_id;
    protected $satuanModel;
    protected $barangMasterSalesModel;
    protected $metaDataModel;
    protected $divisiModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->satuanModel = new SatuansModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
    }

    public function bahanJadiView()
    {
        $data = [
            'satuan' => $this->satuanModel->findAll(),
            'divisi' => $this->divisiModel->where('company_id', $this->this_company_id)->findAll(),
        ];

        return view('SalesInternasional/barangMaster/index', $data);
    }

    public function create()
    {
        $kodeBarang = $this->barangMasterSalesModel->where('kode_barang', $this->request->getVar('kode_barang'))->where('company_id', $this->this_company_id)->where('type_barang_sales', "EKSPOR")->first();
        if ($kodeBarang != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kode barang " . $kodeBarang['kode_barang'] . " sudah ada"
            ]);
        }

        $namaBarang = $this->barangMasterSalesModel->where('barang_name', strtoupper($this->request->getVar('barang_name')))->where('company_id', $this->this_company_id)->where('type_barang_sales', "EKSPOR")->first();
        if ($namaBarang != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Nama barang " . $namaBarang['barang_name'] . " sudah ada"
            ]);
        }

        $this->barangMasterSalesModel->insert([
            'company_id' => $this->this_company_id,
            'kode_barang' => $this->request->getVar('kode_barang'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'barang_name' => strtoupper($this->request->getVar('barang_name')),
            'type_barang_sales' => "EKSPOR",
            'type_barang' => $this->request->getVar('type_barang'),
            'satuan_id' => $this->request->getVar('satuan_id'),
            // 'harga_pokok' => repairDouble($this->request->getVar('harga_pokok')),
            'harga_jual' => $this->request->getVar('harga_jual')
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Barang berhasil ditambahkan"
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));

        $check = $this->barangMasterSalesModel
            ->where('barang_name', strtoupper($this->request->getVar('barang_name')))
            ->where('company_id', $this->this_company_id)->where('type_barang_sales', "EKSPOR")
            ->where('id !=', $id)
            ->first();


        if ($check) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nama barang sudah ada.",
                'token' => csrf_hash()
            ]);
        }


        $this->barangMasterSalesModel->update($id, [
            'company_id' => $this->this_company_id,
            'kode_barang' => $this->request->getVar('kode_barang'),
            'barang_name' => strtoupper($this->request->getVar('barang_name')),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'type_barang_sales' => "EKSPOR",
            'satuan_id' => $this->request->getVar('satuan_id'),
            // 'harga_pokok' => repairDouble($this->request->getVar('harga_pokok')),
            'harga_jual' => $this->request->getVar('harga_jual')
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Barang berhasil diupdate"
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->barangMasterSalesModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Barang berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $data = $this->barangMasterSalesModel->find($id);
        $data['id'] = encrypt($data['id']);
        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $data
        ]);
    }

    public function getBarangJadi()
    {
        $id = decrypt($this->request->getVar('id'));
        $data = $this->barangMasterSalesModel->find($id);
        $data['id'] = encrypt($data['id']);
        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $data
        ]);
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

        $condition = [
            "barang_master_sales.company_id"  => $this->this_company_id,
            "barang_master_sales.type_barang_sales" => "EKSPOR",
            "barang_master_sales.deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "type_barang"   => $this->request->getGet("type_barang"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataResult = $this->barangMasterSalesModel->getList($condition, $addCondition, $limit, $offset);

        $barangResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataResult['data'] as $data) {
            array_push($barangResult, [
                "no"                => $no++,
                "id"                => encrypt($data['id']),
                "kode_barang"       => $data['kode_barang'],
                "barang_name"       => $data['barang_name'],
                "type_barang_sales" => $data['type_barang_sales'],
                'kode_satuan'       => $data['kode_satuan'] . " (" . $data['nama_satuan'] . ")",
                "type_barang"       => strtoupper(str_replace('_', ' ', $data['type_barang'] == "bahan_jadi" ? "barang_jadi" : "kemasan")),
                "harga_pokok"       => floatval($data['harga_pokok']),
                "harga_jual"        => floatval($data['harga_jual']),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $dataResult['totalData'],
            "recordsFiltered"   => $dataResult['totalFilteredData'],
            "data"              => $barangResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function generateNewCode()
    {
        $type = $this->request->getVar('type_barang');
        if (empty($type)) {
            return response()->setJSON([
                'codeNew' => "",
                'token' => csrf_hash(),
            ]);
        }

        $codeName = $this->metaDataModel->where('name', "Tipe Barang Sales Ekspor")->where('value', $type)->first()['description'];

        $lastBarang = $this->barangMasterSalesModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', $type)
            ->where('type_barang_sales', "EKSPOR")
            ->where('deletedAt', null)
            ->like('kode_barang', $codeName . '-____')
            ->orderBy('kode_barang', 'DESC')
            ->first();

        if (empty($lastBarang)) {
            return response()->setJSON([
                'codeNew' => "$codeName-0001",
                'token' => csrf_hash(),
            ]);
        }
        try {

            $lastCode = $lastBarang->kode_barang;
            $lastCodeExp = explode('-', $lastCode);
            $lastIncrement = (int)$lastCodeExp[1];
            $newIncrement = str_pad(($lastIncrement + 1), 4, '0', STR_PAD_LEFT);

            return response()->setJSON([
                'codeNew' => $codeName . "-" . $newIncrement,
                'token' => csrf_hash(),
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'codeNew' => $codeName . "-????",
                'token' => csrf_hash()
            ]);
        }
    }

    public function importExcel()
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
            $tipeBarangSales = $this->request->getVar('type_barang_sales');

            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

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

            for ($i = 0; $i < count($data); $i++) {

                $kodeBarang = trim($data[$i][0]);
                $namaBarang = trim($data[$i][1]);
                $tipeBarang = trim($data[$i][2]);
                $kodeSatuan = trim($data[$i][3]);
                $hargaJual = trim($data[$i][4]);

                $satuan =  $this->satuanModel->where('kode_satuan', $kodeSatuan)->first();
                $kodeBarangCheck = $this->barangMasterSalesModel->where('company_id', $this->this_company_id)->where('kode_barang', $kodeBarang)->first();

                if ($satuan != null && $kodeBarangCheck == null) {
                    $this->barangMasterSalesModel->insert([
                        'company_id' => $this->this_company_id,
                        'kode_barang' => $kodeBarang,
                        'type_barang' => $tipeBarang == "BARANG JADI" ? "bahan_jadi" : "kemasan",
                        'barang_name' => $namaBarang,
                        'type_barang_sales' => $tipeBarangSales,
                        'satuan_id' => $satuan['id'],
                        'harga_jual' => $hargaJual
                    ]);

                    $berhasilTotal++;
                }
            }

            return response()->setJSON([
                'message' => "Berhasil Import : $berhasilTotal Data",
                'status' => true,
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

    public function exportExcel()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
        ];

        $condition = [
            "barang_master_sales.company_id"  => $this->this_company_id,
            "barang_master_sales.type_barang_sales" => "EKSPOR",
            "barang_master_sales.deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            'type_barang'   => ''
        ];

        $dataResult = $this->barangMasterSalesModel->getList($condition, $addCondition, 10000000, 0);

        $list = [];

        foreach ($dataResult['data'] as $data) {
            array_push($list, [
                "kode_barang"       => $data['kode_barang'],
                "barang_name"       => $data['barang_name'],
                "type_barang_sales" => $data['type_barang_sales'],
                'kode_satuan'       => $data['kode_satuan'],
                "type_barang"       => strtoupper(str_replace('_', ' ', $data['type_barang'] == "bahan_jadi" ? "barang_jadi" : "kemasan")),
                "harga_jual"        => floatval($data['harga_jual']),
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $column = 2;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'KODE BARANG')
            ->setCellValue('B1', 'NAMA BARANG')
            ->setCellValue('C1', 'TIPE BARANG')
            ->setCellValue('D1', 'KODE SATUAN')
            ->setCellValue('E1', 'HARGA JUAL');

        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $l['kode_barang'])
                ->setCellValue('B' . $column, $l['barang_name'])
                ->setCellValue('C' . $column, $l['type_barang'])
                ->setCellValue('D' . $column, $l['kode_satuan'])
                ->setCellValue('E' . $column, $l['harga_jual']);

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Export_Data_Master_Barang_Sales';
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
