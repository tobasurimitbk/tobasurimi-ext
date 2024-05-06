<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\ParentBarangModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ParentBarang extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        $type = "bahan_baku";

        if (!empty(@$_GET['type'])) {
            $type = $this->request->getGet('type');
        }

        $data = [
            'type' => $type,
        ];
        return view('Warehouse/parentBarang/index', $data);
    }

    public function create()
    {
        $parentBarangModel = new ParentBarangModel();

        $type = $this->request->getVar('type');
        $parentName = $this->request->getVar('parentName');

        if ($parentBarangModel->where('company_id', $this->this_company_id)->where('parent_name', $parentName)->where('parent_type', $type)->where('deletedAt', null)->first() != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Ups Kelompok Barang $parentName Sudah Ada"
            ]);
        }

        $parentBarangModel->insert([
            'parent_type' => ($type == "") ? "bahan_baku" : $type,
            'parent_name' => $parentName,
            'company_id' => $this->this_company_id
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Kelompok Barang $parentName Berhasil Ditambahkan"
        ]);
    }

    public function update()
    {
        $parentBarangModel = new ParentBarangModel();
        $parentName = $this->request->getVar('parentName');
        $id = decrypt($this->request->getVar('id'));

        $first = $parentBarangModel->find($id);

        $parentBarangSameName = $parentBarangModel
            ->where('company_id', $this->this_company_id)
            ->where('parent_name', $parentName)
            ->where('parent_type', $first['parent_type'])
            ->where('id !=', $id)
            ->first();

        if ($parentBarangSameName) {
            return response()->setJSON([
                'status' => false,
                'message' => "Kategori barang sudah digunakan.",
                'token' => csrf_hash()
            ]);
        }

        $parentBarangModel->update($id, [
            'parent_name' => strtoupper($parentName),
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Kelompok Barang $parentName Berhasil Diupdate"
        ]);
    }

    public function delete()
    {
        $parentBarangModel = new ParentBarangModel();
        $id = decrypt($this->request->getVar('id'));

        $rememberName = $parentBarangModel->where('id', $id)->first()['parent_name'];
        $parentBarangModel->delete($id);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Kelompok Barang $rememberName Berhasil Dihapus"
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $parentBarangModel = new ParentBarangModel();
        $res = $parentBarangModel->where('id', $id)->first();
        $res['id'] = encrypt($res['id']);

        return response()->setJSON([
            'data' => $res,
            'token' => csrf_hash(),
            'status' => true,
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
            "company_id"  => $this->this_company_id,
            "parent_type" => $this->request->getGet('parent_type'),
            "deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $parentBarangModel = new ParentBarangModel();

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $parentBarangModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "parent_name"           => strtoupper($data['parent_name']),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }

    public function dropdownKategoriBarang()
    {
        $parentBarangModel = new ParentBarangModel();

        $parent_type = $this->request->getVar('parent_type');
        return response()->setJSON([
            'data' => $parentBarangModel->where('parent_type', $parent_type)->findAll(),
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function exportExcel()
    {

        $search        = $this->request->getVar("search");
        $sort        = $this->request->getVar("sort");
        $sortType      = $this->request->getVar("sortType");
        $parent_type      = $this->request->getVar("parent_type");


        $filename = "EXPORT_KATEGORI_" . strtoupper($parent_type);

        $condition = [
            "company_id"  => $this->this_company_id,
            "parent_type" => $this->request->getGet('parent_type'),
            "deletedAt" => null,
        ];

        $search        = $this->request->getVar("search");
        $parent_type      = $this->request->getVar("parent_type");
        $sort        = $this->request->getVar("sort");
        $sortType      = $this->request->getVar("sortType");

        $parentBarangModel = new ParentBarangModel();

        $selectQry = "parent_barang.*";
        $parentBarangData = $parentBarangModel->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        if ($search) {
            $parentBarangData->groupStart()
                ->like('parent_name', $search)
                ->groupEnd();
        }


        $getAllParentBarangData = $parentBarangData->findAll();


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);


        if (empty($getAllParentBarangData)) {
            $sheet->setCellValue('A1', 'Tidak Ada Data Satuan');
        } else {
            $sheet->setCellValue('A1', 'NO');
            $sheet->setCellValue('B1', 'KATEGORI');
            $sheet->getStyle('A1:B1')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $no = 1;
            $numRow = 2;

            foreach ($getAllParentBarangData as $row) :
                $sheet->setCellValue('A' . $numRow, $no);
                $sheet->setCellValue('B' . $numRow, $row['parent_name']);


                // Auto size columns A-B
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);


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
