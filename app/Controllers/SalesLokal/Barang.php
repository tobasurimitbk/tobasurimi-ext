<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\MetadataModel;
use App\Models\SatuansModel;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Barang extends BaseController
{
    protected $this_company_id;
    protected $metaDataModel;
    protected $satuanModel;
    protected $barangMasterSalesModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->satuanModel = new SatuansModel();
        $this->metaDataModel = new MetadataModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
    }

    public function bahanJadiView()
    {
        $data = [
            'satuan' => $this->satuanModel->findAll(),
            'typeBarang' => $this->metaDataModel->where('name', "Tipe Barang Sales Lokal")->where('deletedAt', null)->findAll()
        ];

        return view('SalesLokal/barangMaster/index', $data);
    }

    public function create()
    {
        $kodeBarang = $this->barangMasterSalesModel->where('kode_barang', $this->request->getVar('kode_barang'))->where('company_id', $this->this_company_id)->where('type_barang_sales', "LOKAL")->first();
        if ($kodeBarang != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kode barang " . $kodeBarang['kode_barang'] . " sudah ada"
            ]);
        }

        $namaBarang = $this->barangMasterSalesModel->where('barang_name', strtoupper($this->request->getVar('barang_name')))->where('company_id', $this->this_company_id)->where('type_barang_sales', "LOKAL")->first();
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
            'barang_name' => strtoupper($this->request->getVar('barang_name')),
            'type_barang_sales' => "LOKAL",
            'type_barang' => $this->request->getVar('type_barang'),
            'satuan_id' => $this->request->getVar('satuan_id'),
            'status_ppn' => $this->request->getVar('status_ppn') ?? "0",
            'harga_pokok' => repairDouble($this->request->getVar('harga_pokok')),
            'harga_jual' => repairDouble($this->request->getVar('harga_jual'))
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
            ->where('company_id', $this->this_company_id)->where('type_barang_sales', "LOKAL")
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
            'type_barang_sales' => "LOKAL",
            'satuan_id' => $this->request->getVar('satuan_id'),
            'harga_pokok' => repairDouble($this->request->getVar('harga_pokok')),
            'harga_jual' => repairDouble($this->request->getVar('harga_jual')),
            'status_ppn' => $this->request->getVar('status_ppn') ?? "0",
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
            "barang_master_sales.type_barang_sales" => "LOKAL",
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
                "kode_satuan"       => $data['kode_satuan'],
                "type_barang"       => strtoupper(str_replace('_', ' ', $data['type_barang'])),
                "harga_pokok"       => number_format($data['harga_pokok']),
                "harga_jual"        => number_format($data['harga_jual']),
                "status_ppn"       => $data['status_ppn'],
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

        $codeName = $this->metaDataModel->where('name', "Tipe Barang Sales Lokal")->where('value', $type)->first()['description'];

        $lastBarang = $this->barangMasterSalesModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('type_barang', $type)
            ->where('type_barang_sales', "LOKAL")
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

    public function exportExcel()
    {
        $limit = null;
        $offset = null;

        $condition = [
            'barang_master_sales.company_id' => $this->this_company_id,
            'barang_master_sales.type_barang_sales' => 'LOKAL',
            'barang_master_sales.deletedAt' => null,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "type_barang"   => $this->request->getGet("type_barang"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $dataResult = $this->barangMasterSalesModel->getList($condition, $addCondition, $limit, $offset);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Type Barang Sales');
        $sheet->setCellValue('E1', 'Satuan');
        $sheet->setCellValue('F1', 'Jenis Barang');
        $sheet->setCellValue('G1', 'Harga Pokok');
        $sheet->setCellValue('H1', 'Harga Jual');
        $sheet->setCellValue('I1', 'Status PPN');

        $row = 2;
        $no = 1;

        foreach ($dataResult['data'] as $data) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $data['kode_barang']);
            $sheet->setCellValue("C{$row}", $data['barang_name']);
            $sheet->setCellValue("D{$row}", $data['type_barang_sales']);
            $sheet->setCellValue("E{$row}", $data['kode_satuan']);
            $sheet->setCellValue("F{$row}", strtoupper(str_replace('_', ' ', $data['type_barang'])));

            // Set numeric values without formatting
            $sheet->setCellValue("G{$row}", floatval($data['harga_pokok']));
            $sheet->setCellValue("H{$row}", floatval($data['harga_jual']));
            $sheet->setCellValue("I{$row}", $data['status_ppn']);

            // Apply Excel currency format (IDR style, can be customized)
            $sheet->getStyle("G{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $sheet->getStyle("H{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $row++;
        }

        $filename = 'Export-Barang-Lokal-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
