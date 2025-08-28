<?php

namespace App\Controllers\Laporan\Warehouse;

use App\Controllers\BaseController;
use App\Models\AMPurchaseOrderModel;
use App\Models\DivisisModel;
use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SupplierModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanWarehousePembelian extends BaseController
{
    protected $this_company_id;
    protected $supplierModel;
    protected $divisiModel;
    protected $rmPurchaseOrderModel;
    protected $amPurchaseOrderModel;
    protected $rmImportPoModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
        $this->divisiModel = new DivisisModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->rmImportPoModel = new RMImportPOModel();
    }

    public function index()
    {
        $data = [
            'divisis' => $this->divisiModel->getDivisiAccess(),
            'suppliers' => $this->supplierModel->where('company_id', $this->this_company_id)->findAll(),
        ];

        return view('Laporan/Warehouse/LaporanPurchaseOrder/index', $data);
    }

    public function all()
    {
        $pageSize = $this->request->getVar("length");
        $currentPage = ($this->request->getVar("start") / $this->request->getVar("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "status_posting" => $this->request->getVar("status_posting"),
            "dateStart"      => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"        => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "divisi_id"      => $this->request->getVar("divisi_id"),
            "supplier_id"    => $this->request->getVar("supplier_id"),
            "search"         => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
        ];

        $po_type = $this->request->getVar("po_type");

        $dataResult = array();

        if ($po_type == "LOKAL BB") {
            $condition = [
                'rm_purchase_orders.deletedAt' => null,
                'rm_purchase_order_details.deletedAt' => null,
                'rm_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->rmPurchaseOrderModel->getListLaporanPurchaseOrder(
                $condition,
                $addCondition,
                $pageSize,
                $offset
            );
        } elseif ($po_type == "LOKAL BP") {
            $condition = [
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_orders.company_id' => $this->this_company_id,
                'am_purchase_orders.po_type' => "Lokal"
            ];

            $dataPurchaseOrder = $this->amPurchaseOrderModel->getListLaporanPurchaseOrder(
                $condition,
                $addCondition,
                $pageSize,
                $offset
            );
        } elseif ($po_type == "IMPORT BB") {
            $condition = [
                'rm_import_pos.deletedAt' => null,
                'rm_import_po_details.deletedAt' => null,
                'rm_import_pos.company_id' => $this->this_company_id,
            ];

            $dataPurchaseOrder = $this->rmImportPoModel->getListLaporanPurchaseOrder(
                $condition,
                $addCondition,
                $pageSize,
                $offset
            );
        } elseif ($po_type == "IMPORT BP") {
            $condition = [
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_orders.company_id' => $this->this_company_id,
                'am_purchase_orders.po_type' => "Import"
            ];

            $dataPurchaseOrder = $this->amPurchaseOrderModel->getListLaporanPurchaseOrder(
                $condition,
                $addCondition,
                $pageSize,
                $offset
            );
        }

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($dataPurchaseOrder['data'] as $data) {
            $valasName = isset($data['valas_name']) ? $data['valas_name'] : "IDR";
            array_push($dataResult, [
                "no"                => $no++,
                "divisi"            => $data['divisi'],
                "po_date"           => date('d/m/Y', strtotime($data['po_date'])),
                "po_no"             => $data['po_no'],
                "supplier_name"     => $data['supplier_name'],
                "kode_barang"       => $data['kode_barang'],
                "barang_name"       => $data['barang_name'],
                "kode_satuan"       => $data['kode_satuan'],
                "uraian"            => $data['uraian'],
                "spesifikasi"       => $data['spesifikasi'],
                "qty_order"       => (float)$data['qty_order'],
                "qty_diterima"       => (float)$data['qty_diterima'],
                "qty_sisa"       => (float)$data['qty_sisa'],
                "total_harga"       => number_format($data['total_harga'], 2) . " " . $valasName,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataPurchaseOrder['totalData'],
            "recordsFiltered"   => $dataPurchaseOrder['totalFilteredData'],
            'data'              => $dataResult,
            "payload"           => $payload,
        ];

        echo json_encode($data);
        return;
    }

    public function exportExcel()
    {
        $addCondition = [
            "status_posting" => $this->request->getVar("status_posting"),
            "dateStart"      => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"        => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "divisi_id"      => $this->request->getVar("divisi_id"),
            "supplier_id"    => $this->request->getVar("supplier_id"),
            "search"         => $this->request->getVar("search"),
        ];

        $po_type = $this->request->getVar("po_type");

        if ($po_type == "LOKAL BB") {
            $condition = [
                'rm_purchase_orders.deletedAt' => null,
                'rm_purchase_order_details.deletedAt' => null,
                'rm_purchase_orders.company_id' => $this->this_company_id
            ];

            $dataPurchaseOrder = $this->rmPurchaseOrderModel->getListLaporanPurchaseOrder(
                $condition,
                $addCondition,
                100000000,
                0
            );
        } elseif ($po_type == "LOKAL BP") {
            $condition = [
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_orders.company_id' => $this->this_company_id,
                'am_purchase_orders.po_type' => "Lokal"
            ];

            $dataPurchaseOrder = $this->amPurchaseOrderModel->getListLaporanPurchaseOrder(
                $condition,
                $addCondition,
                100000000,
                0
            );
        } elseif ($po_type == "IMPORT BB") {
            $condition = [
                'rm_import_pos.deletedAt' => null,
                'rm_import_po_details.deletedAt' => null,
                'rm_import_pos.company_id' => $this->this_company_id,
            ];

            $dataPurchaseOrder = $this->rmImportPoModel->getListLaporanPurchaseOrder(
                $condition,
                $addCondition,
                100000000,
                0
            );
        } elseif ($po_type == "IMPORT BP") {
            $condition = [
                'am_purchase_orders.deletedAt' => null,
                'am_purchase_order_details.deletedAt' => null,
                'am_purchase_orders.company_id' => $this->this_company_id,
                'am_purchase_orders.po_type' => "Import"
            ];

            $dataPurchaseOrder = $this->amPurchaseOrderModel->getListLaporanPurchaseOrder(
                $condition,
                $addCondition,
                100000000,
                0
            );
        }

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ============================
        // Tambahin Periode di atas
        // ============================
        $periodeText = "Periode: " . date('d/m/Y', strtotime($addCondition['dateStart'])) . " s.d. " . date('d/m/Y', strtotime($addCondition['dateEnd']));

        // Merge cell biar text center di atas tabel (misal header tabel ada 15 kolom = A sampai O)
        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', $periodeText);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ============================
        // Header tabel (turun ke baris 2)
        // ============================
        $header = [
            'No',
            'Departemen',
            'PO Date',
            'PO No',
            'Supplier',
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Uraian',
            'Spesifikasi',
            'Qty Order',
            'Qty Diterima',
            'Qty Sisa',
            'Total Harga',
            'Valas'
        ];

        $sheet->fromArray($header, null, 'A2');

        // Isi data mulai dari baris ke-3
        $row = 3;
        $no = 1;
        foreach ($dataPurchaseOrder['data'] as $data) {
            $valasName = $data['valas_name'] ?? "IDR";
            $sheet->fromArray([
                $no++,
                $data['divisi'],
                date('d/m/Y', strtotime($data['po_date'])),
                $data['po_no'],
                $data['supplier_name'],
                $data['kode_barang'],
                $data['barang_name'],
                $data['kode_satuan'],
                $data['uraian'],
                $data['spesifikasi'],
                (float) $data['qty_order'],
                (float) $data['qty_diterima'],
                (float) $data['qty_sisa'],
                (float) $data['total_harga'],
                $valasName
            ], null, 'A' . $row);

            $row++;
        }

        // Styling header (baris 2)
        $sheet->getStyle('A2:O2')->getFont()->setBold(true);
        $sheet->getStyle('A2:O2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:O' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto size kolom
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }


        // Output file Excel
        $filename = 'laporan_purchase_order_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Supaya langsung download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
