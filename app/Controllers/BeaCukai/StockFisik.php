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
            "barang_master.company_id"  => $this->this_company_id,
            "barang_master.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "company_id" => $this->this_company_id,
            "search" => trim($this->request->getVar('search'))
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $stockFisikData = $this->stockRevampModel->allStockFisik(
            $condition,
            $addCondition,
            100000000,
            0,
        );

        $dataPemasukkanTotal = $this->getTotalPemasukkan();
        $dataPemasukkanProduksiTotal = $this->getTotalPemasukkanProduksi();
        $dataPengeluaranProduksiTotal = $this->getTotalPengeluaranProduksi();
        $dataPengeluaranPerDokumenTotal = $this->getTotalPengeluaranPerDokumen();
        $dataStockFisik = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($stockFisikData['data'] as $data) {
            $totalQty = 0;
            $kodeSatuan = "";
            if (isset($dataPemasukkanTotal[$data['id']])) {
                $totalQty += $dataPemasukkanTotal[$data['id']]['qty_diterima'];
                $kodeSatuan =  $dataPemasukkanTotal[$data['id']]['kode_satuan'];
            }

            if (isset($dataPemasukkanProduksiTotal[$data['id']])) {
                $totalQty += $dataPemasukkanProduksiTotal[$data['id']]['qty_diterima'];
            }

            if (isset($dataPengeluaranProduksiTotal[$data['id']])) {
                $totalQty -= $dataPengeluaranProduksiTotal[$data['id']]['qty_diterima'];
            }


            if (isset($dataPengeluaranPerDokumenTotal[$data['id']])) {
                $totalQty -= $dataPengeluaranPerDokumenTotal[$data['id']]['qty_diterima'];
            }

            array_push($dataStockFisik, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "parent_name"           => $data['parent_name'],
                "kode_barang"           => $data['kode_barang'],
                "barang_name"           => $data['barang_name'],
                "total_qty"             => $totalQty,
                "kode_satuan"           => $kodeSatuan
            ]);
        }

        /**
         * ==================================================
         * FILTER: BUANG YANG QTY = 0 SEMUA
         * ==================================================
         */
        $filteredStock = array_values(array_filter($dataStockFisik, function ($row) {
            return !(
                $row['total_qty'] == 0
            );
        }));

        $totalFiltered = count($filteredStock);

        /**
         * ==================================================
         * RE PAGINATION (array_slice)
         * ==================================================
         */
        $paginatedData = array_slice($filteredStock, $offset, $limit);

        // reset nomor urut
        $no = $offset + 1;
        foreach ($paginatedData as &$row) {
            $row['no'] = $no++;
        }
        unset($row);

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $totalFiltered,
            "recordsFiltered"   => $totalFiltered,
            "data"              => $paginatedData,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function exportExcel()
    {
        $limit = 100000000;
        $offset = 0;

        $condition = [
            "barang_master.company_id"  => $this->this_company_id,
            "barang_master.deletedAt" => null,
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

        $dataPemasukkanTotal = $this->getTotalPemasukkan();
        $dataPemasukkanProduksiTotal = $this->getTotalPemasukkanProduksi();
        $dataPengeluaranProduksiTotal = $this->getTotalPengeluaranProduksi();
        $dataPengeluaranPerDokumenTotal = $this->getTotalPengeluaranPerDokumen();

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
            $totalQty = 0;
            $kodeSatuan = "";
            if (isset($dataPemasukkanTotal[$d['id']])) {
                $totalQty = $dataPemasukkanTotal[$d['id']]['qty_diterima'];
                $kodeSatuan =  $dataPemasukkanTotal[$d['id']]['kode_satuan'];
            }

            if (isset($dataPemasukkanProduksiTotal[$d['id']])) {
                $totalQty += $dataPemasukkanProduksiTotal[$d['id']]['qty_diterima'];
            }

            if (isset($dataPengeluaranProduksiTotal[$d['id']])) {
                $totalQty -= $dataPengeluaranProduksiTotal[$d['id']]['qty_diterima'];
            }

            if (isset($dataPengeluaranPerDokumenTotal[$d['id']])) {
                $totalQty -= $dataPengeluaranPerDokumenTotal[$d['id']]['qty_diterima'];
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $d['parent_name']);
            $sheet->setCellValue('C' . $row, $d['kode_barang']);
            $sheet->setCellValue('D' . $row, trim($d['barang_name']));
            $sheet->setCellValue('E' . $row, (float)$totalQty);
            $sheet->setCellValue('F' . $row, $kodeSatuan);
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

        $data = [
            'barangMaster' => $barangMaster,
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
            'dateStartLpb' => "2025-09-01",
            'dateEndLpb' => "2100-12-01",
        ];

        $dataPemasukkan = $this->bcPurchaseOrderModel->getFisikPemasukkanBarang(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        if (empty($condition['search'])) {
            $dataTotal =  $this->bcPurchaseOrderModel->getFisikPemasukkanBarang(
                $condition,
                $orderColumnIndex,
                $orderDir,
                100000000,
                0
            );
        } else {
            $dataTotal =  $this->bcPurchaseOrderModel->getFisikPemasukkanBarang(
                $condition,
                $orderColumnIndex,
                $orderDir,
                $length,
                $start
            );
        }

        $totalMasuk = 0;
        foreach ($dataTotal['data'] as $d) {
            $totalMasuk += (float)$d['qty_diterima'];
        }

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
            'footerTotals' => $totalMasuk
        ]);
    }

    public function allPemasukkanProduksi()
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
            'barang1_id'  => $barangMasterId,
            'search'       => $search,
            'dateStart' => "2025-09-01",
            'dateEnd' => "2100-12-01",
        ];

        $dataPemasukkan = $this->bcPurchaseOrderModel->getFisikPemasukkanProduksi(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        if (empty($condition['search'])) {
            $dataTotal =  $this->bcPurchaseOrderModel->getFisikPemasukkanProduksi(
                $condition,
                $orderColumnIndex,
                $orderDir,
                100000000,
                0
            );
        } else {
            $dataTotal =  $this->bcPurchaseOrderModel->getFisikPemasukkanProduksi(
                $condition,
                $orderColumnIndex,
                $orderDir,
                $length,
                $start
            );
        }

        $totalMasuk = 0;
        foreach ($dataTotal['data'] as $d) {
            $totalMasuk += (float)$d['qty_diterima'];
        }

        $dataResult = [];
        $no = $start + 1;
        foreach ($dataPemasukkan['data'] as $d) {
            $dataResult[] = [
                "no" => $no++,
                "divisi" => $d['divisi'],
                "warehouse_name" => $d['warehouse_name'],
                "receive_date" => date('d/m/Y', strtotime($d['receive_date'])),
                'pr_no' => $d['pr_no'],
                "kode_barang" => $d['kode_barang'],
                "barang_name" => $d['barang_name'],
                "spesifikasi" => $d['spesifikasi'],
                "qty_diterima" => (float)$d['qty_diterima'],
                "kode_satuan" => $d['kode_satuan'],
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($dataPemasukkan['totalData'] ?? 0),
            'recordsFiltered' => intval($dataPemasukkan['totalFilteredData'] ?? 0),
            'data' => $dataResult,
            'footerTotals' => $totalMasuk
        ]);
    }

    public function allPengeluaranProduksi()
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
            'barang1_id'  => $barangMasterId,
            'search'       => $search,
            'dateStart' => "2025-09-01",
            'dateEnd' => "2100-12-01",
        ];

        $dataPengeluaran = $this->bcPurchaseOrderModel->getFisikPengeluaranProduksi(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        if (empty($condition['search'])) {
            $dataTotal =  $this->bcPurchaseOrderModel->getFisikPengeluaranProduksi(
                $condition,
                $orderColumnIndex,
                $orderDir,
                100000000,
                0
            );
        } else {
            $dataTotal =  $this->bcPurchaseOrderModel->getFisikPengeluaranProduksi(
                $condition,
                $orderColumnIndex,
                $orderDir,
                $length,
                $start
            );
        }

        $totalKeluar = 0;
        foreach ($dataTotal['data'] as $d) {
            $totalKeluar += (float)$d['qty_diterima'];
        }

        $dataResult = [];
        $no = $start + 1;
        foreach ($dataPengeluaran['data'] as $d) {
            $dataResult[] = [
                "no" => $no++,
                "divisi" => $d['divisi'],
                "warehouse_name" => $d['warehouse_name'],
                "supplier_name" => $d['supplier_name'],
                "req_no" => $d['req_no'],
                "request_date" => date('d/m/Y', strtotime($d['request_date'])),
                "tanggal_dokumen" => $d['tanggal_dokumen'] == null ? "" : date('d/m/Y', strtotime($d['tanggal_dokumen'])),
                "type_bc" => $d['type_bc'],
                "no_aju" => $d['no_aju'],
                "no_daftar" => $d['no_daftar'],
                'kode_barang' => $d['kode_barang'],
                "barang_name" => $d['barang_name'],
                "spesifikasi" => $d['spesifikasi'],
                "qty_diterima" => (float)$d['qty_diterima'],
                "kode_satuan" => $d['kode_satuan'],
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($dataPemasukkan['totalData'] ?? 0),
            'recordsFiltered' => intval($dataPemasukkan['totalFilteredData'] ?? 0),
            'data' => $dataResult,
            'footerTotals' => $totalKeluar
        ]);
    }

    public function allPengeluaranPerDokumen()
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
            'barang1_id'  => $barangMasterId,
            'search'       => $search,
            'dateStart' => "2025-09-01",
            'dateEnd' => "2100-12-01",
        ];

        $dataPengeluaran = $this->bcPurchaseOrderModel->getFisikPengeluaranBarangPerDokumen(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        if (empty($condition['search'])) {
            $dataTotal =  $this->bcPurchaseOrderModel->getFisikPengeluaranBarangPerDokumen(
                $condition,
                $orderColumnIndex,
                $orderDir,
                100000000,
                0
            );
        } else {
            $dataTotal =  $this->bcPurchaseOrderModel->getFisikPengeluaranBarangPerDokumen(
                $condition,
                $orderColumnIndex,
                $orderDir,
                $length,
                $start
            );
        }

        $totalKeluar = 0;
        foreach ($dataTotal['data'] as $d) {
            $totalKeluar += (float)$d['qty_diterima'];
        }

        $dataResult = [];
        $no = $start + 1;
        foreach ($dataPengeluaran['data'] as $d) {
            $dataResult[] = [
                "no" => $no++,
                "divisi_asal" => $d['divisi_asal'],
                "warehouse_asal" => $d['warehouse_asal'],
                "reference_no" => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $d['reference_no'])),
                'kode_barang' => $d['kode_barang'],
                "barang_name" => $d['barang_name'],
                "spesifikasi" => $d['spesifikasi'],
                "tanggal_dokumen" => $d['tanggal_dokumen'] == null ? "" : date('d/m/Y', strtotime($d['tanggal_dokumen'])),
                "type_bc" => $d['type_bc'],
                "no_aju" => $d['no_aju'],
                "no_daftar" => $d['no_daftar'],
                "qty_diterima" => (float)$d['qty_diterima'],
                "kode_satuan" => $d['kode_satuan'],
                "valas_name" => $d['valas_name'],
                "sub_total" => $d['sub_total']
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($dataPemasukkan['totalData'] ?? 0),
            'recordsFiltered' => intval($dataPemasukkan['totalFilteredData'] ?? 0),
            'data' => $dataResult,
            'footerTotals' => $totalKeluar
        ]);
    }

    private function getTotalPemasukkan()
    {
        $dataPemasukkanMap = array();

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStartLpb' => "2025-09-01",
            'dateEndLpb' => "2100-12-01",
            'search' => ''
        ];

        $dataPemasukkan = $this->bcPurchaseOrderModel->getFisikPemasukkanBarang(
            $condition,
            0,
            "desc",
            1000000000,
            0
        );

        foreach ($dataPemasukkan['data'] as $d) {
            $barangId = $d['barang_id'];
            if (!isset($dataPemasukkanMap[$barangId])) {
                $dataPemasukkanMap[$barangId] = [
                    'qty_diterima' => 0,
                    'kode_satuan' => $d['kode_satuan']
                ];
            }
            $dataPemasukkanMap[$barangId]['qty_diterima'] += floatval($d['qty_diterima']);
        }

        return $dataPemasukkanMap;
    }

    private function getTotalPemasukkanProduksi()
    {
        $dataPemasukkanMap = array();

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart' => "2025-09-01",
            'dateEnd' => "2100-12-01",
            'search' => ''
        ];

        $dataPemasukkan = $this->bcPurchaseOrderModel->getFisikPemasukkanProduksi(
            $condition,
            0,
            "desc",
            1000000000,
            0
        );

        foreach ($dataPemasukkan['data'] as $d) {
            $barangId = $d['barang1_id'];
            if (!isset($dataPemasukkanMap[$barangId])) {
                $dataPemasukkanMap[$barangId] = [
                    'qty_diterima' => 0,
                    'kode_satuan' => $d['kode_satuan']
                ];
            }
            $dataPemasukkanMap[$barangId]['qty_diterima'] += floatval($d['qty_diterima']);
        }

        return $dataPemasukkanMap;
    }

    private function getTotalPengeluaranProduksi()
    {
        $dataPengeluaranMap = array();

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart' => "2025-09-01",
            'dateEnd' => "2100-12-01",
            'search' => ''
        ];

        $dataPengeluaran = $this->bcPurchaseOrderModel->getFisikPengeluaranProduksi(
            $condition,
            0,
            "desc",
            1000000000,
            0
        );

        foreach ($dataPengeluaran['data'] as $d) {
            $barangId = $d['barang1_id'];
            if (!isset($dataPengeluaranMap[$barangId])) {
                $dataPengeluaranMap[$barangId] = [
                    'qty_diterima' => 0,
                    'kode_satuan' => $d['kode_satuan']
                ];
            }
            $dataPengeluaranMap[$barangId]['qty_diterima'] += floatval($d['qty_diterima']);
        }

        return $dataPengeluaranMap;
    }

    private function getTotalPengeluaranPerDokumen()
    {
        $dataPengeluaranMap = array();

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart' => "2025-09-01",
            'dateEnd' => "2100-12-01",
            'search' => ''
        ];

        $dataPengeluaran = $this->bcPurchaseOrderModel->getFisikPengeluaranBarangPerDokumen(
            $condition,
            0,
            "desc",
            1000000000,
            0
        );

        foreach ($dataPengeluaran['data'] as $d) {
            $barangId = $d['barang1_id'];
            if (!isset($dataPengeluaranMap[$barangId])) {
                $dataPengeluaranMap[$barangId] = [
                    'qty_diterima' => 0,
                    'kode_satuan' => $d['kode_satuan']
                ];
            }
            $dataPengeluaranMap[$barangId]['qty_diterima'] += floatval($d['qty_diterima']);
        }

        return $dataPengeluaranMap;
    }
}
