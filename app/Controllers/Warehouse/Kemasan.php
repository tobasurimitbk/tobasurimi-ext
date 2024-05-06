<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\KemasanModel;
use App\Models\ParentBarangModel;
use App\Models\SatuansModel;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Kemasan extends BaseController
{
    protected $this_company_id;
    protected $kemasanModel;
    protected $parentBarangModel;
    protected $satuanModel;

    public function __construct()
    {
        $this->satuanModel = new SatuansModel();
        $this->parentBarangModel = new ParentBarangModel();
        $this->kemasanModel = new KemasanModel();
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        $data = [
            'kelompokBarang' => $this->parentBarangModel
                ->where('parent_type', "kemasan")
                ->where('deletedAt', null)
                ->where('company_id', $this->this_company_id)
                ->orderBy('parent_name', 'asc')
                ->findAll(),
            'satuan' => $this->satuanModel->where('deletedAt', null)
                ->findAll()
        ];

        return view('Warehouse/kemasan/index', $data);
    }

    public function create()
    {
        $first = $this->kemasanModel
            ->where('company_id', $this->this_company_id)
            ->where('name', strtoupper($this->request->getVar('name')))
            ->where('satuan_id', $this->request->getVar('satuan_id'))
            ->where('parent_type_id', $this->request->getVar('parent_type_id'))
            ->where('deletedAt', null)
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kemasan " . strtoupper($this->request->getVar('name')) . " sudah ada"
            ]);
        }

        $kodeKemasan = $this->kemasanModel
            ->where('company_id', $this->this_company_id)
            ->where('kode', $this->request->getVar('kode'))
            ->first();

        if ($kodeKemasan != null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kode kemasan sudah ada",
            ]);
        }

        $this->kemasanModel->insert([
            'company_id' => $this->this_company_id,
            'kode' => $this->request->getVar('kode'),
            'name' => strtoupper($this->request->getVar('name')),
            'satuan_id' => $this->request->getVar('satuan_id'),
            'parent_type_id' => $this->request->getVar('parent_type_id')
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil ditambah"
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));

        $kemasanSameName = $this->kemasanModel
            ->where('company_id', $this->this_company_id)
            ->where('name', strtoupper($this->request->getVar('name')))
            ->where('satuan_id', $this->request->getVar('satuan_id'))
            ->where('parent_type_id', $this->request->getVar('parent_type_id'))
            ->where('id !=', $id)
            ->first();

        if ($kemasanSameName) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nama Kemasan sudah digunakan.",
                'token' => csrf_hash()
            ]);
        }

        $this->kemasanModel->update($id, [
            'company_id' => $this->this_company_id,
            'kode' => $this->request->getVar('kode'),
            'name' => strtoupper($this->request->getVar('name')),
            'satuan_id' => $this->request->getVar('satuan_id'),
            'parent_type_id' => $this->request->getVar('parent_type_id')
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil diupdate"
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->kemasanModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil dihapus"
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $res = $this->kemasanModel->find($id);
        if ($res != null) {
            $res['id'] = encrypt($res['id']);
        }
        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'data' => $res
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
            "kemasan.company_id"  => $this->this_company_id,
            "kemasan.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "parent_type_id" => $this->request->getGet('parent_type_id'),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->kemasanModel->getList($condition, $addCondition, $limit, $offset);

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "kode"                  => $data['kode'],
                "name"                  => $data['name'],
                "kode_satuan"                => $data['kode_satuan'],
                "parent_name"           => $data['parent_name']
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

    public function generateNewKode()
    {
        $codeName = "KS";

        $last = $this->kemasanModel
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->like('kode', $codeName . '-____')
            ->orderBy('kode', 'DESC')
            ->first();


        if (empty($last)) {
            return response()->setJSON([
                'codeNew' => "$codeName-0001",
                'token' => csrf_hash(),
            ]);
        }
        try {

            $lastCode = $last['kode'];
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

        if ($this->validate($rules)) {

            $file = $this->request->getFile('file');

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

            $gagalArr = [];
            $berhasilTotal = 0;

            // INSERT MASTER KEMASAN
            for ($i = 0; $i < count($data); $i++) {

                // VALIDASI KODE KEMASAN
                $kodeKemasan = $this->kemasanModel->where('kode', trim($data[$i][1]))
                    ->where('company_id', $this->this_company_id)
                    ->where('deletedAt', null)
                    ->first();

                // VALIDASI NAMA KEMASAN
                $kemasanName = $this->kemasanModel->where('name', trim($data[$i][2]))
                    ->where('company_id', $this->this_company_id)
                    ->where('deletedAt', null)
                    ->first();

                // VALIDASI KATEGORI BARANG
                $satuan = $this->satuanModel->where('kode_satuan', trim($data[$i][3]))->first();

                // PARENT BARANG 
                $parentBarang = $this->parentBarangModel->where('company_id', $this->this_company_id)->where('parent_name', trim($data[$i][0]))->where('parent_type', "kemasan")->first();

                // if excel null
                if ($data[$i][1] != null) {
                    if ($kodeKemasan == null && $kemasanName == null && $satuan != null && $parentBarang != null) {
                        // KEMASAN INSERTED
                        $this->kemasanModel->insert([
                            'company_id' => $this->this_company_id,
                            'kode' => trim($data[$i][1]),
                            'name' => strtoupper(trim($data[$i][2])),
                            'satuan_id' => $satuan['id'],
                            'parent_type_id' => $parentBarang['id'],
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

    public function exportExcel()
    {

        $filename = "EXPORT_KEMASAN";

        $condition = [
            "kemasan.company_id"  => $this->this_company_id,
            "kemasan.deletedAt" => null,
        ];

        $search        = $this->request->getVar("search");
        $parent_type_id      = $this->request->getVar("parent_type_id");
        $sort        = $this->request->getVar("sort");
        $sortType      = $this->request->getVar("sortType");



        $selectQry = "kemasan.*,
        parent_barang.parent_name,
        satuans.kode_satuan";
        $kemasanDataQry = $this->kemasanModel->select($selectQry)
            ->join('satuans', 'kemasan.satuan_id = satuans.id', 'left')
            ->join('parent_barang', 'parent_barang.id = kemasan.parent_type_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);


        if ($search || $parent_type_id) {
            $kemasanDataQry->groupStart();
        }

        if ($parent_type_id) {
            $kemasanDataQry->where('kemasan.parent_type_id', $parent_type_id);
        }

        if ($search) {
            $kemasanDataQry->like('satuans.kode_satuan', $search)
                ->orLike('kemasan.name', 'search')
                ->orLike('kemasan.kode', 'search')
                ->orLike('parent_name', 'search');
            $kemasanDataQry->Orlike('satuans.kode_satuan', $search);
        }

        if ($search || $parent_type_id) {
            $kemasanDataQry->groupEnd();
        }

        $getAllKemasanData = $kemasanDataQry->findAll();


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);


        if (empty($getAllKemasanData)) {
            $sheet->setCellValue('A1', 'Tidak Ada Data Satuan');
        } else {
            $sheet->setCellValue('A1', 'NO');
            $sheet->setCellValue('B1', 'KODE');
            $sheet->setCellValue('C1', 'KEMASAN');
            $sheet->setCellValue('D1', 'KATEGORI');
            $sheet->setCellValue('E1', 'SATUAN');
            $sheet->getStyle('A1:E1')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $no = 1;
            $numRow = 2;

            foreach ($getAllKemasanData as $row) :
                $sheet->setCellValue('A' . $numRow, $no);
                $sheet->setCellValue('B' . $numRow, $row['kode']);
                $sheet->setCellValue('C' . $numRow, $row['name']);
                $sheet->setCellValue('D' . $numRow, $row['parent_name']);
                $sheet->setCellValue('E' . $numRow, $row['kode_satuan']);


                // Auto size columns A-E
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);

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
