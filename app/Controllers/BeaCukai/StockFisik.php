<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\StockRevampModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockFisik extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $stockRevampModel;
    protected $barangMasterModel;
    protected $bcPurchaseOrderModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->stockRevampModel = new StockRevampModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
    }

    public function index()
    {
        return view('BeaCukai/stockFisik/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = [
            "stock_revamp.company_id"  => $this->this_company_id,
            "stock_revamp.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "company_id" => $this->this_company_id,
            "search" => $this->request->getVar('search')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->stockRevampModel->allStockFisik(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "barang_master_id"      => encrypt($data['barang_master_id']),
                "parent_name"           => $data['parent_name'],
                "kode_barang"           => $data['kode_barang'],
                "barang_name"           => $data['barang_name'],
                "total_qty_bersih"            => (float)$data['total_qty_bersih'],
                "kode_satuan"           => $data['kode_satuan']
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function exportExcel()
    {
        $limit = 100000000;
        $offset = 0;

        $condition = [
            "stock_revamp.company_id"  => $this->this_company_id,
            "stock_revamp.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => "asc",
            "sortType" => "barang_name",
            "company_id" => $this->this_company_id,
        ];

        $dataQry = $this->stockRevampModel->allStockFisik(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kategori');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Barang');
        $sheet->setCellValue('E1', 'Qty');
        $sheet->setCellValue('F1', 'Unit');

        $row = 2;
        $no = 1;

        foreach ($dataQry['data'] as $d) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $d['parent_name']);
            $sheet->setCellValue('C' . $row, $d['kode_barang']);
            $sheet->setCellValue('D' . $row, $d['barang_name']);
            $sheet->setCellValue('E' . $row, (float)$d['total_qty_bersih']);
            $sheet->setCellValue('F' . $row, $d['kode_satuan']);
            $row++;
        }

        // Format kolom Qty (rata kanan + format ribuan)
        $lastRow = $row - 1;
        $sheet->getStyle('E2:E' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00'); // tampil seperti 25,000.23

        $sheet->getStyle('E2:E' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Auto width untuk semua kolom
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Bold header + rata tengah
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Output
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Stock_Fisik_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $barangMaster = $this->barangMasterModel
            ->select('barang_master.*,parent_barang.parent_name')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where('barang_master.id', $id)
            ->first();

        $condition = [
            "stock_revamp.company_id"  => $this->this_company_id,
            "stock_revamp.barang_master_id" => $id,
        ];

        $stock = $this->stockRevampModel->allStockFisik(
            $condition,
            [],
            1,
            0
        );

        if (count($stock['data']) == 0) {
            var_dump("data stock tidak ditemukan");
            die;
        }

        $data = [
            'barangMaster' => $barangMaster,
            'stock' => $stock['data'][0]
        ];

        return view('BeaCukai/stockFisik/detail', $data);
    }

    public function allPemasukkan()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;

        $barangMasterId = ($this->request->getGet('barang_master_id'));
        $search = $this->request->getGet('search') ?? '';

        $condition = [
            'company_id'   => $this->this_company_id,
            'barang_master_id'  => $barangMasterId,
            'search'       => $search,
            'bc_id' => ''
        ];

        $dataPemasukkan = $this->bcPurchaseOrderModel->getListLapPemasukanBarang(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $dataResult = [];
        $no = $start + 1;
        foreach ($dataPemasukkan['data'] as $d) {
            $dataResult[] = [
                "no" => $no++,
                "divisi" => $d['divisi'],
                "warehouse_name" => $d['warehouse_name'],
                "supplier_name" => $d['supplier_name'],
                "kode_barang" => $d['kode_barang'],
                "barang_name" => $d['barang_name'],
                "spesifikasi" => $d['spesifikasi'],
                "jenis_doc" => !empty($d['jenis_doc']) ? $d['jenis_doc'] : "NON PABEAN",
                "po_no" => $d['no_order'],
                "ref_no" => $d['no_penerimaan_barang'],
                "tanggal_lpb" => date('d/m/Y', strtotime($d['tanggal_lpb'])),
                "no_daftar" => $d['no_daftar'],
                "no_aju"    => $d['no_aju'],
                "qty_diterima" => (float)$d['qty_diterima'],
                "kode_satuan" => $d['kode_satuan'],
                "valas" => $d['valas'],
                "total_harga" => (float)$d['total_harga'],
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($dataPemasukkan['totalData'] ?? 0),
            'recordsFiltered' => intval($dataPemasukkan['totalFilteredData'] ?? 0),
            'data' => $dataResult,
        ]);
    }
}
