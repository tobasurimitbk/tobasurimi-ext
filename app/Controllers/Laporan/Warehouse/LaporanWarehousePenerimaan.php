<?php

namespace App\Controllers\Laporan\Warehouse;

use App\Controllers\BaseController;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SupplierModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanWarehousePenerimaan extends BaseController
{
    protected $this_company_id;
    protected $supplierModel;
    protected $divisiModel;
    protected $penerimaanBarangModel;
    protected $companyModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->divisiModel = new DivisisModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->companyModel = new CompaniesModel();
    }

    public function index()
    {
        $data = [
            'divisis' => $this->divisiModel->getDivisiAccess(),
            'suppliers' => $this->supplierModel->where('company_id', $this->this_company_id)->findAll(),
        ];

        return view('Laporan/Warehouse/LaporanPenerimaanBarang/index', $data);
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
            "bc_type"    => $this->request->getVar("bc_type"),
            "search"         => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "po_type"       => ""
        ];

        $lpb_type = $this->request->getVar("lpb_type");

        $dataResult = array();
        if ($lpb_type == "LOKAL BB") {
            $condition = [
                'penerimaan_barang.status_penerimaan' => "LOKAL",
                'penerimaan_barang.tipe_bahan' => 'BAKU',
                'penerimaan_barang.deletedAt' => null,
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.company_id' => $this->this_company_id,
            ];

            $addCondition['po_type'] = "LOKAL BAKU";

            $dataLpb = $this->penerimaanBarangModel->getListLaporanPenerimaanBarangBahanBakuLokal(
                $condition,
                $addCondition,
                $pageSize,
                $offset
            );
        } elseif ($lpb_type == "LOKAL BP") {
            $condition = [
                'penerimaan_barang.status_penerimaan' => "LOKAL",
                'penerimaan_barang.tipe_bahan' => 'PENOLONG',
                'penerimaan_barang.deletedAt' => null,
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.company_id' => $this->this_company_id,
            ];

            $addCondition['po_type'] = "LOKAL PENOLONG";

            $dataLpb = $this->penerimaanBarangModel->getListLaporanPenerimaanBarangBahanPenolong(
                $condition,
                $addCondition,
                $pageSize,
                $offset
            );
        } elseif ($lpb_type == "IMPORT BP") {
            $condition = [
                'penerimaan_barang.status_penerimaan' => "IMPORT",
                'penerimaan_barang.tipe_bahan' => 'PENOLONG',
                'penerimaan_barang.deletedAt' => null,
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.company_id' => $this->this_company_id,
            ];

            $addCondition['po_type'] = "IMPORT PENOLONG";

            $dataLpb = $this->penerimaanBarangModel->getListLaporanPenerimaanBarangBahanPenolong(
                $condition,
                $addCondition,
                $pageSize,
                $offset
            );
        } elseif ($lpb_type == "IMPORT BB") {
            $condition = [
                'penerimaan_barang.status_penerimaan' => "IMPORT",
                'penerimaan_barang.tipe_bahan' => 'BAKU',
                'penerimaan_barang.deletedAt' => null,
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.company_id' => $this->this_company_id,
            ];

            $addCondition['po_type'] = "IMPORT BAKU";

            $dataLpb = $this->penerimaanBarangModel->getListLaporanPenerimaanBarangBahanBakuImport(
                $condition,
                $addCondition,
                $pageSize,
                $offset
            );
        }

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($dataLpb['data'] as $data) {
            $valasName = isset($data['valas_name']) ? $data['valas_name'] : "";
            array_push($dataResult, [
                "no"                => $no++,
                "divisi"            => $data['divisi'],
                "supplier_name"     => $data['supplier_name'],
                "bc_name"           => $data['bc_name'] == null ? "Non Pabean" : $data['bc_name'],
                "tanggal_dokumen"   => $data['tanggal_dokumen'] == null ? '' : date('d/m/Y', strtotime($data['tanggal_dokumen'])),
                "no_daftar"         => $data['no_daftar'],
                "no_aju"            => $data['no_aju'],
                "tanggal_lpb"       => date('d/m/Y', strtotime($data['tanggal_lpb'])),
                "no_lpb"            => $data['no_lpb'],
                "po_date"           => date('d/m/Y', strtotime($data['po_date'])),
                "po_no"             => $data['po_no'],
                "kode_barang"       => $data['kode_barang'],
                "barang_name"       => $data['barang_name'],
                "spesifikasi"      => $data['spesifikasi'],
                "kode_satuan"        => $data['kode_satuan'],
                "keterangan"      => $data['keterangan'],
                "qty_order"       => (float)$data['qty_order'],
                "qty_diterima"       => (float)$data['qty_diterima'],
                "total_harga"       => number_format($data['total_harga'], 2) . " " . $valasName,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataLpb['totalData'],
            "recordsFiltered"   => $dataLpb['totalFilteredData'],
            'data'              => $dataResult,
            "payload"           => $payload,
            "grandTotalHarga"   => (float)$dataLpb['grandTotalHarga']
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
            "bc_type"    => $this->request->getVar("bc_type"),
            "search"         => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "po_type"       => ""
        ];

        $lpb_type = $this->request->getVar("lpb_type");

        $dataResult = array();
        if ($lpb_type == "LOKAL BB") {
            $condition = [
                'penerimaan_barang.status_penerimaan' => "LOKAL",
                'penerimaan_barang.tipe_bahan' => 'BAKU',
                'penerimaan_barang.deletedAt' => null,
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.company_id' => $this->this_company_id,
            ];

            $addCondition['po_type'] = "LOKAL BAKU";

            $dataLpb = $this->penerimaanBarangModel->getListLaporanPenerimaanBarangBahanBakuLokal(
                $condition,
                $addCondition,
                100000000,
                0
            );
        } elseif ($lpb_type == "LOKAL BP") {
            $condition = [
                'penerimaan_barang.status_penerimaan' => "LOKAL",
                'penerimaan_barang.tipe_bahan' => 'PENOLONG',
                'penerimaan_barang.deletedAt' => null,
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.company_id' => $this->this_company_id,
            ];

            $addCondition['po_type'] = "LOKAL PENOLONG";

            $dataLpb = $this->penerimaanBarangModel->getListLaporanPenerimaanBarangBahanPenolong(
                $condition,
                $addCondition,
                100000000,
                0
            );
        } elseif ($lpb_type == "IMPORT BP") {
            $condition = [
                'penerimaan_barang.status_penerimaan' => "IMPORT",
                'penerimaan_barang.tipe_bahan' => 'PENOLONG',
                'penerimaan_barang.deletedAt' => null,
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.company_id' => $this->this_company_id,
            ];

            $addCondition['po_type'] = "IMPORT PENOLONG";

            $dataLpb = $this->penerimaanBarangModel->getListLaporanPenerimaanBarangBahanPenolong(
                $condition,
                $addCondition,
                100000000,
                0
            );
        } elseif ($lpb_type == "IMPORT BB") {
            $condition = [
                'penerimaan_barang.status_penerimaan' => "IMPORT",
                'penerimaan_barang.tipe_bahan' => 'BAKU',
                'penerimaan_barang.deletedAt' => null,
                'penerimaan_barang_detail.deletedAt' => null,
                'penerimaan_barang.company_id' => $this->this_company_id,
            ];

            $addCondition['po_type'] = "IMPORT BAKU";

            $dataLpb = $this->penerimaanBarangModel->getListLaporanPenerimaanBarangBahanBakuImport(
                $condition,
                $addCondition,
                100000000,
                0
            );
        }

        $no =  1;
        foreach ($dataLpb['data'] as $data) {
            $valasName = isset($data['valas_name']) ? $data['valas_name'] : "IDR";
            array_push($dataResult, [
                "no"                => $no++,
                "divisi"            => $data['divisi'],
                "supplier_name"     => $data['supplier_name'],
                "bc_name"           => $data['bc_name'] == null ? "Non Pabean" : $data['bc_name'],
                "tanggal_dokumen"   => $data['tanggal_dokumen'] == null ? '' : date('d/m/Y', strtotime($data['tanggal_dokumen'])),
                "no_daftar"         => $data['no_daftar'],
                "no_aju"            => $data['no_aju'],
                "tanggal_lpb"       => date('d/m/Y', strtotime($data['tanggal_lpb'])),
                "no_lpb"            => $data['no_lpb'],
                "po_date"           => date('d/m/Y', strtotime($data['po_date'])),
                "po_no"             => $data['po_no'],
                "kode_barang"       => $data['kode_barang'],
                "barang_name"       => $data['barang_name'],
                "spesifikasi"      => $data['spesifikasi'],
                "kode_satuan"        => $data['kode_satuan'],
                "keterangan"      => $data['keterangan'],
                "qty_order"       => (float)$data['qty_order'],
                "qty_diterima"       => (float)$data['qty_diterima'],
                "total_harga"       => (float)$data['total_harga'],
                "valas_name" => $valasName
            ]);
        }

        $header = [
            'No',
            'Departemen',
            'Supplier',
            'Jenis Doc',
            'Tgl Doc',
            'No Daftar',
            'No Aju',
            'Tgl LPB',
            'No LPB',
            'Tgl PO',
            'No PO',
            'Kode Barang',
            'Nama Barang',
            'Spesifikasi',
            'Satuan',
            'Keterangan',
            'Qty Order',
            'Qty Diterima',
            'Total Harga',
            'Valas'

        ];
        // Ambil periode
        $dateStart = $addCondition['dateStart'] ? date("d/m/Y", strtotime($addCondition['dateStart'])) : "-";
        $dateEnd   = $addCondition['dateEnd'] ? date("d/m/Y", strtotime($addCondition['dateEnd'])) : "-";
        $company = $this->companyModel->where('id', $this->this_company_id)->first();

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // === Periode di paling atas ===
        $lastCol = chr(64 + count($header)); // Hitung kolom terakhir berdasarkan header
        $sheet->mergeCells("A1:" . $lastCol . "1");
        $sheet->setCellValue("A1", "Periode : {$dateStart} s/d {$dateEnd} {$company['company']} ");
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // === Header di baris ke-3 ===
        $col = 'A';
        foreach ($header as $h) {
            $sheet->setCellValue($col . '3', $h);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $col++;
        }

        // === Data mulai baris ke-4 ===
        $row = 4;
        $grandTotal = 0;
        foreach ($dataResult as $d) {
            $col = 'A';
            foreach ($d as $key => $val) {
                $sheet->setCellValue($col . $row, $val);

                if ($key === "total_harga") {
                    $sheet->getStyle($col . $row)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    if ($lpb_type == "LOKAL BB" || $lpb_type == "LOKAL BP") {
                        $grandTotal += $val;
                    }
                }

                $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $col++;
            }
            $row++;
        }

        // === Grand Total ===
        if ($lpb_type == "LOKAL BB" || $lpb_type == "LOKAL BP") {
            $sheet->setCellValue("R" . $row, "Grand Total");
            $sheet->setCellValue("S" . $row, $grandTotal);

            $sheet->mergeCells("A" . $row . ":R" . $row);
            $sheet->getStyle("A" . $row . ":S" . $row)->getFont()->setBold(true);
            $sheet->getStyle("A" . $row . ":S" . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle("S" . $row)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }


        // Auto size kolom
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $writer = new Xlsx($spreadsheet);
        $filename = "Laporan_LPB_" . $lpb_type . "_" . date("Ymd_His") . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
