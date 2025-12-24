<?php

namespace App\Controllers\Laporan\BeaCukai;

use App\Controllers\BaseController;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC23Model;
use App\Models\BC25Model;
use App\Models\BC27Model;
use App\Models\BC30Model;
use App\Models\BC40Model;
use App\Models\BC41Model;
use App\Models\BCBarangTarifModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInModel;
use App\Models\JasaVendorOutModel;
use App\Models\KemasanModel;
use App\Models\MaterialRequestDetailsModel;
use App\Models\MetadataModel;
use App\Models\MutasiGlobalModel;
use App\Models\MutasiModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanMutasiGlobalModel;
use App\Models\PPBKBModel;
use App\Models\ProductionResultDetailModel;
use App\Models\ProductionResultModel;
use App\Models\RMImportPOModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockRevampModel;
use App\Models\SupplierModel;
use App\Models\WorkOrdersModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// META DATA -> jenis_dok_aju
// BC 2.5 -> 49
// BC 2.7 -> 52
// BC 3.0 -> 1445
// BC 4.1 -> 54
// PPBKB -> 1426

class LaporanBeaCukai extends BaseController
{
    protected $this_company_id;
    protected $stockDetail2Model;
    protected $metadataModel;
    protected $bc23Model;
    protected $bc25Model;
    protected $bc27Model;
    protected $bc30Model;
    protected $bc40Model;
    protected $bc41Model;
    protected $bcTarifModel;
    protected $ppbkbModel;
    protected $penerimaanBarangModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $kemasanModel;
    protected $rmImportPosModel;
    protected $amPurchaseOrderModel;
    protected $satuanModel;
    protected $divisiModel;
    protected $supplierModel;
    protected $mutasiModel;
    protected $mutasiGlobalModel;
    protected $jasaVendorInModel;
    protected $jasaVendorOutModel;
    protected $salesOrderInvoiceModel;
    protected $penerimaanMutasiGlobalModel;
    protected $materialRequestDetailsModel;
    protected $productionResultModel;
    protected $productionResultDetailModel;
    protected $companiesModel;
    protected $bcPurchaseOrderModel;
    protected $workOrdersModel;
    protected $stockRevampModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->stockDetail2Model = new StockDetail2Model();
        $this->metadataModel = new MetadataModel();
        $this->bc23Model = new BC23Model();
        $this->bc25Model = new BC25Model();
        $this->bc27Model = new BC27Model();
        $this->bc30Model = new BC30Model();
        $this->bc40Model = new BC40Model();
        $this->bc41Model = new BC41Model();
        $this->bcTarifModel = new BCBarangTarifModel();
        $this->ppbkbModel = new PPBKBModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->supplierModel = new SupplierModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->kemasanModel = new KemasanModel();
        $this->rmImportPosModel = new RMImportPOModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->satuanModel = new SatuansModel();
        $this->divisiModel = new DivisisModel();
        $this->supplierModel = new SupplierModel();
        $this->mutasiModel = new MutasiModel();
        $this->mutasiGlobalModel = new MutasiGlobalModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->jasaVendorOutModel = new JasaVendorOutModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $this->penerimaanMutasiGlobalModel = new PenerimaanMutasiGlobalModel();
        $this->materialRequestDetailsModel = new MaterialRequestDetailsModel();
        $this->productionResultModel = new ProductionResultModel();
        $this->productionResultDetailModel = new ProductionResultDetailModel();
        $this->companiesModel = new CompaniesModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $this->workOrdersModel = new WorkOrdersModel();
        $this->stockRevampModel = new StockRevampModel();
    }

    public function index()
    {
        return view('Laporan/LaporanBeaCukai/index/index');
    }

    public function laporanPemasukanBarang()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataDokumen' => $this->metadataModel->where('name', 'jenis_dok_aju')->whereIn('value', ['BC 2.3', 'BC 2.7', 'BC 4.0'])->findAll(),
            'dataPemasukan' => ["LOKAL BAKU", "LOKAL PENOLONG", "IMPORT BAKU", "IMPORT PENOLONG"],
        ];

        return view('Laporan/LaporanBeaCukai/pemasukanBarang/index', $data);
    }

    public function exportExcelPemasukkan()
    {
        $start = 0;
        $length = 100000000000000;
        $orderDir = 'desc';
        $orderColumnIndex = 2;

        // --- Ambil filter & condition ---
        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart'    => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : null,
            'dateEnd'      => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : null,
            'bc_id'        => $this->request->getGet('bc_id'),
            'sumber'       => $this->request->getGet('sumber'),
            'divisi_id'    => $this->request->getGet('divisi_id'),
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'search'       => $this->request->getGet('search') ?? '',
        ];

        // --- Ambil data ---
        $dataPemasukkan = $this->bcPurchaseOrderModel->getListLapPemasukanBarang(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        // --- Ambil nama perusahaan ---
        $company = $this->companiesModel
            ->select('holding_company, company')
            ->where('id', $this->this_company_id)
            ->first();

        // --- Mapping total per no_aju ---
        $mapNoAju = [];
        $mapTotal = [];
        $no = 1;

        if (!empty($dataPemasukkan['data'])) {
            foreach ($dataPemasukkan['data'] as $d) {
                $noAju = $d['no_aju'];
                if (!isset($mapNoAju[$noAju])) $mapNoAju[$noAju] = $no++;
                if (!isset($mapTotal[$noAju])) $mapTotal[$noAju] = ['qty_order' => 0, 'qty_diterima' => 0, 'total_harga' => 0];

                $mapTotal[$noAju]['qty_order'] += (float)($d['qty_order'] ?? 0);
                $mapTotal[$noAju]['qty_diterima'] += (float)($d['qty_diterima'] ?? 0);
                $mapTotal[$noAju]['total_harga'] += (float)($d['total_harga'] ?? 0);
            }
        }

        // --- Excel ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pemasukan Barang');

        // --- HEADER ---
        $periodeText = (!empty($condition['dateStart']) && !empty($condition['dateEnd']))
            ? 'Periode ' . date('d/m/Y', strtotime($condition['dateStart'])) . ' s.d ' . date('d/m/Y', strtotime($condition['dateEnd']))
            : '';
        $companyName = ($company['holding_company'] ?? '') . ' - ' . ($company['company'] ?? '');

        $sheet->mergeCells('A1:T1');
        $sheet->mergeCells('A2:T2');
        $sheet->mergeCells('A3:T3');
        $sheet->setCellValue('A1', 'Laporan Pemasukkan Barang');
        $sheet->setCellValue('A2', $periodeText);
        $sheet->setCellValue('A3', $companyName);

        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(12);

        // --- HEADER KOLOM ---
        $headers = [
            'No',
            'Jenis Doc',
            'Nomor Aju',
            'No Daftar',
            'Tgl Daftar',
            'No Lpb',
            'Tgl Lpb',
            'No Order',
            'Dept',
            'Warehouse',
            'Supplier',
            'Kode Barang',
            'Barang',
            'Spesifikasi',
            'Jml Order',
            'Jml Diterima',
            'Satuan',
            'Valas',
            'Harga Barang / Jasa',
            'Keterangan'
        ];
        $sheet->fromArray($headers, null, 'A5');
        $sheet->getStyle('A5:T5')->getFont()->setBold(true);
        $sheet->getStyle('A5:T5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- DATA + TOTAL per no_aju ---
        $row = 6;
        $lastNoAju = null;
        if (!empty($dataPemasukkan['data'])) {
            foreach ($dataPemasukkan['data'] as $index => $d) {
                $noAju = $d['no_aju'];
                $displayNo = ($lastNoAju !== $noAju) ? $mapNoAju[$noAju] : '';

                $sheet->setCellValue("A{$row}", $displayNo);
                $sheet->setCellValue("B{$row}", $d['jenis_doc'] ?? 'NON PABEAN');
                $sheet->setCellValue("C{$row}", $noAju);
                $sheet->setCellValue("D{$row}", $d['no_daftar'] ?? '');
                $sheet->setCellValue("E{$row}", !empty($d['tanggal_daftar']) ? date('d/m/Y', strtotime($d['tanggal_daftar'])) : '');
                $sheet->setCellValue("F{$row}", $d['no_penerimaan_barang'] ?? '');
                $sheet->setCellValue("G{$row}", !empty($d['tanggal_lpb']) ? date('d/m/Y', strtotime($d['tanggal_lpb'])) : '');
                $sheet->setCellValue("H{$row}", $d['no_order'] ?? '');
                $sheet->setCellValue("I{$row}", $d['divisi'] ?? '');
                $sheet->setCellValue("J{$row}", $d['warehouse_name'] ?? '');
                $sheet->setCellValue("K{$row}", $d['supplier_name'] ?? '');
                $sheet->setCellValue("L{$row}", $d['kode_barang'] ?? '');
                $sheet->setCellValue("M{$row}", $d['barang_name'] ?? '');
                $sheet->setCellValue("N{$row}", $d['spesifikasi'] ?? '');
                $sheet->setCellValue("O{$row}", (float)$d['qty_order']);
                $sheet->setCellValue("P{$row}", (float)$d['qty_diterima']);
                $sheet->setCellValue("Q{$row}", $d['kode_satuan'] ?? '');
                $sheet->setCellValue("R{$row}", $d['valas'] ?? '');
                $sheet->setCellValue("S{$row}", (float)$d['total_harga']);
                $sheet->setCellValue("T{$row}", $d['keterangan'] ?? '');

                $sheet->getStyle("S{$row}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("S{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $row++;


                // Tambahkan TOTAL jika row berikutnya beda no_aju atau akhir data
                $nextNoAju = $dataPemasukkan['data'][$index + 1]['no_aju'] ?? null;
                if ($nextNoAju !== $noAju) {
                    // Merge kolom N (Spesifikasi) + O (Jml Order) untuk TOTAL
                    $sheet->mergeCells("N{$row}:O{$row}");
                    $sheet->setCellValue("N{$row}", 'TOTAL');

                    // Isi total qty_diterima dan total_harga
                    $sheet->setCellValue("P{$row}", $mapTotal[$noAju]['qty_diterima']);
                    $sheet->setCellValue("S{$row}", $mapTotal[$noAju]['total_harga']);

                    // Format angka 2,000.00 tanpa Rp
                    $sheet->getStyle("P{$row}:S{$row}")
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    // Bold + rata kanan
                    $sheet->getStyle("N{$row}:S{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("N{$row}:S{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $row++;
                }


                $lastNoAju = $noAju;
            }
        }

        // --- BORDERS & AUTO SIZE ---
        $sheet->getStyle("A5:T" . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- OUTPUT ---
        $filename = 'Laporan_Pemasukan_Barang_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function allPemasukkan()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;

        $dateStart = $this->request->getVar("dateStart")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
            : null;

        $dateEnd = $this->request->getVar("dateEnd")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
            : null;

        $bcId = $this->request->getGet('bc_id');
        $sumber = $this->request->getGet('sumber');
        $divisiId = $this->request->getGet('divisi_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        $search = $this->request->getGet('search') ?? '';

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart'    => $dateStart,
            'dateEnd'      => $dateEnd,
            'bc_id'        => $bcId,
            'sumber'       => $sumber,
            'divisi_id'    => $divisiId,
            'warehouse_id' => $warehouseId,
            'search'       => $search,
        ];

        // ambil semua data buat total
        $allData = $this->bcPurchaseOrderModel->getListLapPemasukanBarang(
            $condition,
            $orderColumnIndex,
            $orderDir,
            100000000000000,
            0
        );

        $mapNoAju = [];
        $mapTotal = [];
        $no = 1;

        if (!empty($allData['data'])) {
            foreach ($allData['data'] as $d) {
                $noAju = $d['no_aju'];

                // nomor unik per no_aju
                if (!isset($mapNoAju[$noAju])) {
                    $mapNoAju[$noAju] = $no++;
                }

                // inisialisasi total per no_aju
                if (!isset($mapTotal[$noAju])) {
                    $mapTotal[$noAju] = [
                        'total_qty_diterima' => 0,
                        'total_harga' => 0,
                    ];
                }

                // akumulasi total
                $mapTotal[$noAju]['total_qty_diterima'] += (float) ($d['qty_diterima'] ?? 0);
                $mapTotal[$noAju]['total_harga'] += (float) ($d['total_harga'] ?? 0);
            }
        }

        // ambil data sesuai pagination
        $dataPemasukkan = $this->bcPurchaseOrderModel->getListLapPemasukanBarang(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $dataResult = [];
        $lastNoAju = null;

        if (!empty($dataPemasukkan['data'])) {
            foreach ($dataPemasukkan['data'] as $index => $d) {
                $noAju = $d['no_aju'];

                // tampilkan nomor hanya di baris pertama grup
                if ($lastNoAju !== $noAju) {
                    $d['no'] = $mapNoAju[$noAju] ?? '';
                } else {
                    $d['no'] = '';
                }

                // format tanggal
                $d['tanggal_daftar'] = !empty($d['tanggal_daftar'])
                    ? date('d/m/Y', strtotime($d['tanggal_daftar'])) : "";
                $d['tanggal_lpb'] = !empty($d['tanggal_lpb'])
                    ? date('d/m/Y', strtotime($d['tanggal_lpb'])) : "";
                $d['jenis_doc'] = !empty($d['jenis_doc']) ? $d['jenis_doc'] : "NON PABEAN";


                $dataResult[] = $d;

                // kalau next no_aju beda, tambahkan baris total
                $nextNoAju = $dataPemasukkan['data'][$index + 1]['no_aju'] ?? null;
                if ($nextNoAju !== $noAju) {
                    $dataResult[] = [
                        "no" => "",
                        "jenis_doc" => "",
                        "no_aju" => "",
                        "no_daftar" => "",
                        "tanggal_daftar" => "",
                        "no_penerimaan_barang" => "",
                        "tanggal_lpb" => "",
                        "no_order" => "",
                        "divisi" => "",
                        "warehouse_name" => "",
                        "supplier_name" => "",
                        "kode_barang" => "",
                        "barang_name" => "",
                        "spesifikasi" => "TOTAL",
                        "qty_order" => "",
                        "qty_diterima" => (float)$mapTotal[$noAju]['total_qty_diterima'],
                        "kode_satuan" => "",
                        "valas" => "",
                        "total_harga" => (float)$mapTotal[$noAju]['total_harga'],
                        "keterangan" => "",
                        "is_total_row" => true
                    ];
                }

                $lastNoAju = $noAju;
            }
        }


        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($dataPemasukkan['totalData'] ?? 0),
            'recordsFiltered' => intval($dataPemasukkan['totalFilteredData'] ?? 0),
            'data' => $dataResult,
        ]);
    }

    public function laporanPengeluaranBarang()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataDokumen' => $this->metadataModel->where('name', 'jenis_dok_aju')->whereIn('value', ['BC 2.5', 'BC 2.7', 'BC 4.1', 'BC 3.0'])->findAll(),
        ];

        return view('Laporan/LaporanBeaCukai/pengeluaranBarang/index', $data);
    }

    public function allKeluarBarang()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;

        $dateStart = $this->request->getVar("dateStart")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
            : null;

        $dateEnd = $this->request->getVar("dateEnd")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
            : null;

        $bcId = $this->request->getGet('bc_id');
        $divisiId = $this->request->getGet('divisi_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        $search = $this->request->getGet('search') ?? '';

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart'    => $dateStart,
            'dateEnd'      => $dateEnd,
            'bc_id'        => $bcId,
            'divisi_id'    => $divisiId,
            'warehouse_id' => $warehouseId,
            'search'       => $search,
        ];

        // ambil semua data buat total
        $allData = $this->bcPurchaseOrderModel->getListLapPengeluaranBarang(
            $condition,
            $orderColumnIndex,
            $orderDir,
            100000000000000,
            0
        );

        $mapNoAju = [];
        $mapTotal = [];
        $no = 1;

        if (!empty($allData['data'])) {
            foreach ($allData['data'] as $d) {
                $noAju = $d['no_aju'];

                // nomor unik per no_aju
                if (!isset($mapNoAju[$noAju])) {
                    $mapNoAju[$noAju] = $no++;
                }

                // inisialisasi total per no_aju
                if (!isset($mapTotal[$noAju])) {
                    $mapTotal[$noAju] = [
                        'jml_pengeluaran' => 0,
                        'sub_total' => 0,
                    ];
                }

                // akumulasi total
                $mapTotal[$noAju]['jml_pengeluaran'] += (float) ($d['jml_pengeluaran'] ?? 0);
                $mapTotal[$noAju]['sub_total'] += (float) ($d['sub_total'] ?? 0);
            }
        }

        // ambil data sesuai pagination
        $dataPemasukkan = $this->bcPurchaseOrderModel->getListLapPengeluaranBarang(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $dataResult = [];
        $lastNoAju = null;

        if (!empty($dataPemasukkan['data'])) {
            foreach ($dataPemasukkan['data'] as $index => $d) {
                $noAju = $d['no_aju'];

                // tampilkan nomor hanya di baris pertama grup
                if ($lastNoAju !== $noAju) {
                    $d['no'] = $mapNoAju[$noAju] ?? '';
                } else {
                    $d['no'] = '';
                }

                // format tanggal
                $d['tanggal_dokumen'] = !empty($d['tanggal_dokumen'])
                    ? date('d/m/Y', strtotime($d['tanggal_dokumen'])) : "";
                $d['tanggal_keluar'] = !empty($d['tanggal_keluar'])
                    ? date('d/m/Y', strtotime($d['tanggal_keluar'])) : "";
                $d['jenis_doc'] = !empty($d['jenis_doc']) ? $d['jenis_doc'] : "NON PABEAN";
                $d['no_keluar'] = str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $d['no_keluar']));

                $dataResult[] = $d;

                // kalau next no_aju beda, tambahkan baris total
                $nextNoAju = $dataPemasukkan['data'][$index + 1]['no_aju'] ?? null;
                if ($nextNoAju !== $noAju) {
                    $dataResult[] = [
                        "no" => "",
                        "jenis_doc" => "",
                        "no_aju" => "",
                        "no_daftar" => "",
                        "tanggal_dokumen" => "",
                        "no_keluar" => "",
                        "tanggal_keluar" => "",
                        "divisi" => "",
                        "warehouse_name" => "",
                        "customer_name" => "",
                        "kode_barang" => "",
                        "barang_name" => "",
                        "spesifikasi" => "TOTAL",
                        "jml_pengeluaran" => (float)$mapTotal[$noAju]['jml_pengeluaran'],
                        "kode_satuan" => "",
                        "valas_name" => "",
                        "sub_total" => (float)$mapTotal[$noAju]['sub_total'],
                        "is_total_row" => true
                    ];
                }

                $lastNoAju = $noAju;
            }
        }


        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($dataPemasukkan['totalData'] ?? 0),
            'recordsFiltered' => intval($dataPemasukkan['totalFilteredData'] ?? 0),
            'data' => $dataResult,
        ]);
    }

    public function exportExcelLaporanKeluarBarang()
    {
        $start = 0;
        $length = 100000000000000;
        $orderDir = 'desc';
        $orderColumnIndex = 2;

        // --- Ambil filter & condition ---
        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart'    => $this->request->getVar("dateStart")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
                : null,
            'dateEnd'      => $this->request->getVar("dateEnd")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
                : null,
            'bc_id'        => $this->request->getGet('bc_id'),
            'divisi_id'    => $this->request->getGet('divisi_id'),
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'search'       => $this->request->getGet('search') ?? '',
        ];

        // --- Ambil data ---
        $dataPengeluaran = $this->bcPurchaseOrderModel->getListLapPengeluaranBarang(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        // --- Ambil nama perusahaan ---
        $company = $this->companiesModel
            ->select('holding_company, company')
            ->where('id', $this->this_company_id)
            ->first();

        // --- Mapping nomor & total per no_aju ---
        $mapNoAju = [];
        $mapTotal = [];
        $no = 1;

        foreach ($dataPengeluaran['data'] ?? [] as $d) {
            $noAju = $d['no_aju'];

            if (!isset($mapNoAju[$noAju])) {
                $mapNoAju[$noAju] = $no++;
            }

            if (!isset($mapTotal[$noAju])) {
                $mapTotal[$noAju] = [
                    'jml_pengeluaran' => 0,
                    'sub_total'       => 0,
                ];
            }

            $mapTotal[$noAju]['jml_pengeluaran'] += (float)($d['jml_pengeluaran'] ?? 0);
            $mapTotal[$noAju]['sub_total']       += (float)($d['sub_total'] ?? 0);
        }

        // --- Excel ---
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengeluaran Barang');

        // --- HEADER ---
        $periodeText = (!empty($condition['dateStart']) && !empty($condition['dateEnd']))
            ? 'Periode ' . date('d/m/Y', strtotime($condition['dateStart'])) . ' s.d ' . date('d/m/Y', strtotime($condition['dateEnd']))
            : '';

        $companyName = ($company['holding_company'] ?? '') . ' - ' . ($company['company'] ?? '');

        $sheet->mergeCells('A1:Q1');
        $sheet->mergeCells('A2:Q2');
        $sheet->mergeCells('A3:Q3');

        $sheet->setCellValue('A1', 'LAPORAN PENGELUARAN BARANG');
        $sheet->setCellValue('A2', $periodeText);
        $sheet->setCellValue('A3', $companyName);

        $sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- HEADER KOLOM ---
        $headers = [
            'No',
            'Jenis Doc',
            'Nomor Aju',
            'No Daftar',
            'Tgl Dokumen',
            'No Keluar',
            'Tgl Keluar',
            'Dept',
            'Warehouse',
            'Penerima',
            'Kode Barang',
            'Barang',
            'Spesifikasi',
            'Jml Keluar',
            'Satuan',
            'Valas',
            'Sub Total',
        ];

        $sheet->fromArray($headers, null, 'A5');
        $sheet->getStyle('A5:Q5')->getFont()->setBold(true);
        $sheet->getStyle('A5:Q5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- DATA ---
        $row = 6;
        $lastNoAju = null;

        foreach ($dataPengeluaran['data'] ?? [] as $index => $d) {
            $noAju = $d['no_aju'];
            $displayNo = ($lastNoAju !== $noAju) ? $mapNoAju[$noAju] : '';

            $sheet->setCellValue("A{$row}", $displayNo);
            $sheet->setCellValue("B{$row}", $d['jenis_doc'] ?? '');
            $sheet->setCellValue("C{$row}", $noAju);
            $sheet->setCellValue("D{$row}", $d['no_daftar'] ?? '');
            $sheet->setCellValue("E{$row}", !empty($d['tanggal_dokumen']) ? date('d/m/Y', strtotime($d['tanggal_dokumen'])) : '');
            $sheet->setCellValue("F{$row}", $d['no_keluar'] ?? '');
            $sheet->setCellValue("G{$row}", !empty($d['tanggal_keluar']) ? date('d/m/Y', strtotime($d['tanggal_keluar'])) : '');
            $sheet->setCellValue("H{$row}", $d['divisi'] ?? '');
            $sheet->setCellValue("I{$row}", $d['warehouse_name'] ?? '');
            $sheet->setCellValue("J{$row}", $d['customer_name'] ?? '');
            $sheet->setCellValue("K{$row}", $d['kode_barang'] ?? '');
            $sheet->setCellValue("L{$row}", $d['barang_name'] ?? '');
            $sheet->setCellValue("M{$row}", $d['spesifikasi'] ?? '');
            $sheet->setCellValue("N{$row}", (float)($d['jml_pengeluaran'] ?? 0));
            $sheet->setCellValue("O{$row}", $d['kode_satuan'] ?? '');
            $sheet->setCellValue("P{$row}", $d['valas_name'] ?? '');
            $sheet->setCellValue("Q{$row}", (float)($d['sub_total'] ?? 0));

            $sheet->getStyle("N{$row}:Q{$row}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $row++;

            // --- TOTAL PER NO AJU ---
            $nextNoAju = $dataPengeluaran['data'][$index + 1]['no_aju'] ?? null;

            if ($nextNoAju !== $noAju) {
                $sheet->mergeCells("K{$row}:M{$row}");
                $sheet->setCellValue("K{$row}", 'TOTAL');

                $sheet->setCellValue("N{$row}", $mapTotal[$noAju]['jml_pengeluaran']);
                $sheet->setCellValue("Q{$row}", $mapTotal[$noAju]['sub_total']);

                $sheet->getStyle("N{$row}:Q{$row}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $sheet->getStyle("K{$row}:Q{$row}")
                    ->getFont()->setBold(true);

                $sheet->getStyle("K{$row}:Q{$row}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $row++;
            }

            $lastNoAju = $noAju;
        }

        // --- BORDER & AUTOSIZE ---
        $sheet->getStyle("A5:Q" . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- OUTPUT ---
        $filename = 'Laporan_Pengeluaran_Barang_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    public function laporanWip()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('Laporan/LaporanBeaCukai/wip/index', $data);
    }

    public function allWip()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;

        $dateStart = $this->request->getVar("dateStart")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
            : null;

        $dateEnd = $this->request->getVar("dateEnd")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
            : null;

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart'    => $dateStart,
            'dateEnd'      => $dateEnd,
            'divisi_id'    => $this->request->getGet('divisi_id'),
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'search'       => $this->request->getGet('search') ?? '',
        ];

        $dataWip = $this->bcPurchaseOrderModel->getListLapWip(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $totalQtyWip = $this->getTotalWip($condition);

        // 🔑 ambil mapping WO sekali
        $mapWorkOrder = $this->mapWorkOrder();
        $dataResult = [];

        $no = $start + 1;
        foreach ($dataWip['data'] as $d) {

            $woNoArr = [];
            $barangJadiArr = [];

            if (!empty($d['work_order_id'])) {
                $woIds = explode(',', $d['work_order_id']);

                foreach ($woIds as $woId) {
                    $woId = trim($woId);
                    if (isset($mapWorkOrder[$woId])) {
                        $woNoArr[] = $mapWorkOrder[$woId]['wo_no'];
                        if (!empty($mapWorkOrder[$woId]['barang_name'])) {
                            $barangJadiArr[] = $mapWorkOrder[$woId]['barang_name'];
                        }
                    }
                }
            }

            $dataResult[] = [
                'no'              => $no++,
                'production_date' => date('d/m/Y', strtotime($d['production_date'])),
                'wo_no'       => array_values(array_unique($woNoArr)),
                'barang_jadi' => array_values(array_unique($barangJadiArr)),
                'divisi'          => $d['divisi'],
                'warehouse_name'  => $d['warehouse_name'],
                'req_no'          => $d['req_no'],
                'no_aju'          => $d['no_aju'],
                'no_daftar'       => $d['no_daftar'],
                'tanggal_daftar'  => $d['tanggal_daftar']
                    ? date('d/m/Y', strtotime($d['tanggal_daftar']))
                    : '',
                'kode_barang'     => $d['kode_barang'],
                'barang_name'     => $d['barang_name'],
                'spesifikasi'     => $d['spesifikasi'],
                'qty'             => (float)$d['qty'],
                'kode_satuan'     => $d['kode_satuan']
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($dataWip['totalData'] ?? 0),
            'recordsFiltered' => intval($dataWip['totalFilteredData'] ?? 0),
            'data' => $dataResult,
            'footerTotals' => $totalQtyWip
        ]);
    }

    private function getTotalWip($condition)
    {
        $start = 0;
        $length = 100000000000000;
        $orderDir = 'desc';
        $orderColumnIndex = 2;
        $dataWip = $this->bcPurchaseOrderModel->getListLapWip(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $totalQty = 0;
        foreach ($dataWip['data'] as $d) {
            $totalQty += (float)$d['qty'];
        }

        return $totalQty;
    }

    private function mapWorkOrder()
    {
        $selectQry = "
            work_orders.id,
            work_orders.wo_no,
            barang_master.barang_name
        ";

        $workOrders = $this->workOrdersModel
            ->select($selectQry)
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = work_order_details.barang1_id', 'left')
            ->where('work_orders.company_id', $this->this_company_id)
            ->where('work_orders.deletedAt', null)
            ->groupBy('work_orders.id, barang_master.barang_name')
            ->findAll();

        $map = [];

        foreach ($workOrders as $wo) {
            if (!isset($map[$wo['id']])) {
                $map[$wo['id']] = [
                    'wo_no' => $wo['wo_no'],
                    'barang_name' => $wo['barang_name']
                ];
            }
        }

        return $map;
    }

    public function exportExcelWip()
    {
        // ===== ambil data (punyamu, tidak diubah) =====
        $start = 0;
        $length = 100000000000000;
        $orderDir = 'desc';
        $orderColumnIndex = 2;

        $dateStart = $this->request->getVar("dateStart")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
            : null;

        $dateEnd = $this->request->getVar("dateEnd")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
            : null;

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart'    => $dateStart,
            'dateEnd'      => $dateEnd,
            'divisi_id'    => $this->request->getGet('divisi_id'),
            'warehouse_id' => $this->request->getGet('warehouse_id'),
            'search'       => $this->request->getGet('search') ?? '',
        ];

        $dataWip = $this->bcPurchaseOrderModel->getListLapWip(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $mapWorkOrder = $this->mapWorkOrder();

        // ===== olah data =====
        $rows = [];
        $no = 1;

        foreach ($dataWip['data'] as $d) {

            $woNoArr = [];
            $barangJadiArr = [];

            if (!empty($d['work_order_id'])) {
                foreach (explode(',', $d['work_order_id']) as $woId) {
                    $woId = trim($woId);
                    if (isset($mapWorkOrder[$woId])) {
                        $woNoArr[] = $mapWorkOrder[$woId]['wo_no'];
                        if (!empty($mapWorkOrder[$woId]['barang_name'])) {
                            $barangJadiArr[] = $mapWorkOrder[$woId]['barang_name'];
                        }
                    }
                }
            }

            $rows[] = [
                $no++,
                date('d/m/Y', strtotime($d['production_date'])),
                implode(', ', array_unique($woNoArr)),        // WO NO
                implode(', ', array_unique($barangJadiArr)), // BARANG JADI
                $d['divisi'],
                $d['warehouse_name'],
                $d['req_no'],
                $d['tanggal_daftar']
                    ? date('d/m/Y', strtotime($d['tanggal_daftar']))
                    : '',
                $d['no_aju'],
                $d['no_daftar'],
                $d['kode_barang'],
                $d['barang_name'],
                $d['spesifikasi'],
                (float)$d['qty'],
                $d['kode_satuan'],
            ];
        }

        // ===== buat excel =====
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /**
         * =========================
         * JUDUL & PERIODE
         * =========================
         */
        $periode = 'Periode : ';
        if ($dateStart && $dateEnd) {
            $periode .= date('d/m/Y', strtotime($dateStart)) . ' s.d ' . date('d/m/Y', strtotime($dateEnd));
        } elseif ($dateStart) {
            $periode .= 'Mulai ' . date('d/m/Y', strtotime($dateStart));
        } elseif ($dateEnd) {
            $periode .= 'Sampai ' . date('d/m/Y', strtotime($dateEnd));
        } else {
            $periode .= '-';
        }

        // Judul
        $sheet->setCellValue('A1', 'LAPORAN WORK IN PROCESS (WIP)');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Periode
        $sheet->setCellValue('A2', $periode);
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        /**
         * =========================
         * HEADER TABEL (mulai baris 4)
         * =========================
         */
        $headers = [
            'No',
            'Tgl Prod',
            'Wo No',
            'Barang Jadi',
            'Dept',
            'Warehouse',
            'Mr No',
            'Tgl Doc',
            'No Aju',
            'No Daftar',
            'Kode Barang',
            'Barang',
            'Spesifikasi',
            'Qty',
            'Satuan'
        ];

        $headerRow = 4;
        $dataStartRow = 5;

        $sheet->fromArray($headers, null, "A{$headerRow}");
        $sheet->fromArray($rows, null, "A{$dataStartRow}");

        $lastColumn = $sheet->getHighestColumn();
        $lastDataRow = $sheet->getHighestRow();

        /**
         * =========================
         * TOTAL QTY
         * =========================
         */
        $totalRow = $lastDataRow + 1;

        // Merge kolom A - M untuk label TOTAL
        $sheet->mergeCells("A{$totalRow}:M{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');

        // SUM Qty (kolom N)
        $sheet->setCellValue(
            "N{$totalRow}",
            "=SUM(N{$dataStartRow}:N{$lastDataRow})"
        );

        // Style TOTAL
        $sheet->getStyle("A{$totalRow}:O{$totalRow}")->getFont()->setBold(true);

        /**
         * =========================
         * STYLING
         * =========================
         */

        // Header bold & center
        $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")
            ->getFont()->setBold(true);

        $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Border semua cell
        $sheet->getStyle("A{$headerRow}:{$lastColumn}{$totalRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Autosize kolom
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Align Qty kanan
        $sheet->getStyle("N{$dataStartRow}:N{$totalRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        /**
         * =========================
         * OUTPUT
         * =========================
         */
        $filename = 'Laporan_WIP_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    public function pungutanBc23()
    {
        return view('Laporan/LaporanBeaCukai/2.3/index');
    }

    public function allPungutanBc23()
    {
        $payload = [
            "pageSize"    => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"      => $this->request->getGet("search"),
            "sort"        => $this->request->getGet("sort"),
            "sortType"    => $this->request->getGet("sortType"),
            "company_id"  => $this->this_company_id,
            "type"        => "BC 2.3"
        ];

        $addCondition = [
            "sort"               => $this->request->getGet("sort"),
            "sortType"           => $this->request->getGet("sortType"),
            "statusBC"           => $this->request->getGet("statusBC"),
            "statusLPB"          => $this->request->getGet("statusLPB"),
            "supplierName"       => $this->request->getGet("supplierName"),
            "mulaiTanggalBC23"   => !empty($this->request->getGet("mulaiTanggalBC23"))
                ? formatDMYtoYMD($this->request->getGet("mulaiTanggalBC23")) : '',
            "selesaiTanggalBC23" => !empty($this->request->getGet("selesaiTanggalBC23"))
                ? formatDMYtoYMD($this->request->getGet("selesaiTanggalBC23")) : '',
            "noPenerimaanBarang" => $this->request->getGet("noPenerimaanBarang"),
            "statusPosting"      => "SUDAH POSTING",
            "noAju"              => $this->request->getGet("noAju"),
            "searchData"         => trim($this->request->getGet("search"))
        ];

        $condition = [
            "bc_purchase_order.deletedAt" => null,
            "bc_purchase_order.company_id" => $this->this_company_id,
            "bc_23.deletedAt" => null,
        ];

        $limit  = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $dataBC23 = $this->bc23Model->getList($condition, $addCondition, $limit, $offset);

        if (empty($dataBC23['data'])) {
            return response()->setJSON([
                "draw" => intval($this->request->getVar("draw")),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "payload" => $payload
            ]);
        }

        $dataBC23Result = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataBC23['data'] as $data) {

            $entry = [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "supplier_name" => $data->supplier_name,
                "no_aju"        => $data->no_aju,
                "no_daftar"     => $data->no_daftar,
                "po_type"       => $data->po_type,
                "date"          => $data->createdAt
                    ? date('d/m/Y', strtotime($data->createdAt))
                    : '-',
                "dataBCTarif" => [
                    'PPN' => ['nilai' => 0, 'status' => ''],
                    'PPH' => ['nilai' => 0, 'status' => ''],
                    'BM'  => ['nilai' => 0, 'status' => ''],
                ]
            ];

            $dataBCTarif = $this->bcTarifModel
                ->getByBcPurchaseOrder($data->bc_purchase_order_id);

            foreach ($dataBCTarif as $tarif) {

                switch ($tarif['kode_jenis_pungutan']) {
                    case '1':
                    case 'PPN':
                        $jenis = 'PPN';
                        break;
                    case '2':
                    case 'PPH':
                        $jenis = 'PPH';
                        break;
                    case '3':
                    case 'BM':
                        $jenis = 'BM';
                        break;
                    default:
                        continue 2;
                }

                switch ($tarif['kode_fasilitas_tarif']) {
                    case '6':
                        $entry['dataBCTarif'][$jenis]['nilai'] += (float)$tarif['nilai_bayar'];
                        $entry['dataBCTarif'][$jenis]['status'] = $tarif['nilai_bayar'] == 0 ? '' : 'Tidak Dipungut';
                        break;

                    case '5':
                        $entry['dataBCTarif'][$jenis]['nilai'] += (float)$tarif['nilai_bayar'];
                        $entry['dataBCTarif'][$jenis]['status'] = $tarif['nilai_bayar'] == 0 ? '' : 'Dibebaskan';
                        break;

                    case '3':
                        $entry['dataBCTarif'][$jenis]['nilai'] += (float)$tarif['nilai_bayar'];
                        $entry['dataBCTarif'][$jenis]['status'] = $tarif['nilai_bayar'] == 0 ? '' : 'Ditangguhkan';
                        break;
                }
            }

            $dataBC23Result[] = $entry;
        }

        return response()->setJSON([
            "draw"            => intval($this->request->getVar("draw")),
            "recordsTotal"    => $dataBC23['totalData'],
            "recordsFiltered" => $dataBC23['totalFilteredData'],
            "data"            => $dataBC23Result,
            "payload"         => $payload
        ]);
    }


    public function exportExcelPungutanBc23()
    {
        /* ================= FILTER ================= */
        $addCondition = [
            "statusBC" => $this->request->getVar('statusBC'),
            "statusLPB" => $this->request->getVar('statusLPB'),
            "mulaiTanggalBC23" => !empty($this->request->getGet("mulaiTanggalBC23"))
                ? formatDMYtoYMD($this->request->getGet("mulaiTanggalBC23")) : '',
            "selesaiTanggalBC23" => !empty($this->request->getGet('selesaiTanggalBC23'))
                ? formatDMYtoYMD($this->request->getGet('selesaiTanggalBC23')) : '',
            "supplierName" => $this->request->getVar("supplierName"),
            "noAju" => $this->request->getVar("noAju"),
            "noPenerimaanBarang" => $this->request->getVar("noPenerimaanBarang"),
            "searchData" => trim($this->request->getGet('search')),
            "statusPosting" => "SUDAH POSTING",
        ];

        $condition = [
            "bc_purchase_order.deletedAt" => null,
            "bc_purchase_order.company_id" => $this->this_company_id,
            "bc_23.deletedAt" => null,
        ];

        $dataBC23 = $this->bc23Model->getList($condition, $addCondition, 1000000, 0);

        /* ================= OLAH DATA ================= */
        $rows = [];
        $no = 1;

        foreach ($dataBC23['data'] as $data) {

            $row = [
                'no' => $no++,
                'supplier' => $data->supplier_name,
                'date' => date('d/m/Y', strtotime($data->createdAt)),
                'no_aju' => $data->no_aju,
                'no_daftar' => $data->no_daftar,
                'po_type' => $data->po_type,

                'PPN_nilai' => 0,
                'PPN_status' => '',
                'PPH_nilai' => 0,
                'PPH_status' => '',
                'BM_nilai'  => 0,
                'BM_status'  => '',
            ];

            $tarifs = $this->bcTarifModel->getByBcPurchaseOrder($data->bc_purchase_order_id) ?? [];

            foreach ($tarifs as $t) {

                /* ===== JENIS PUNGUTAN ===== */
                $jenis = null;
                switch ($t['kode_jenis_pungutan']) {
                    case '1':
                    case 'PPN':
                        $jenis = 'PPN';
                        break;

                    case '2':
                    case 'PPH':
                        $jenis = 'PPH';
                        break;

                    case '3':
                    case 'BM':
                        $jenis = 'BM';
                        break;
                }

                if ($jenis === null) {
                    continue;
                }

                $row[$jenis . '_nilai'] += (float) $t['nilai_bayar'];

                /* ===== STATUS FASILITAS ===== */
                switch ($t['kode_fasilitas_tarif']) {
                    case '6':
                        $row[$jenis . '_status'] = $row[$jenis . '_nilai']  == 0 ? '' : 'Tidak Dipungut';
                        break;

                    case '5':
                        $row[$jenis . '_status'] =  $row[$jenis . '_nilai']  == 0 ? '' :  'Dibebaskan';
                        break;

                    case '3':
                        $row[$jenis . '_status'] =  $row[$jenis . '_nilai']  == 0 ? '' :  'Ditangguhkan';
                        break;
                }
            }

            $rows[] = $row;
        }

        /* ================= EXCEL ================= */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ===== JUDUL ===== */
        $periode = ($addCondition['mulaiTanggalBC23'] && $addCondition['selesaiTanggalBC23'])
            ? date('d/m/Y', strtotime($addCondition['mulaiTanggalBC23'])) . ' s/d ' .
            date('d/m/Y', strtotime($addCondition['selesaiTanggalBC23']))
            : 'SEMUA PERIODE';

        $sheet->mergeCells('A1:L1')->setCellValue('A1', 'LAPORAN PUNGUTAN BC 2.3');
        $sheet->mergeCells('A2:L2')->setCellValue('A2', 'PERIODE : ' . $periode);

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        /* ===== HEADER ===== */
        $sheet->setCellValue('A3', 'No')->mergeCells('A3:A4');
        $sheet->setCellValue('B3', 'Supplier')->mergeCells('B3:B4');
        $sheet->setCellValue('C3', 'Tanggal')->mergeCells('C3:C4');
        $sheet->setCellValue('D3', 'No Aju')->mergeCells('D3:D4');
        $sheet->setCellValue('E3', 'No Daftar')->mergeCells('E3:E4');
        $sheet->setCellValue('F3', 'Tipe PO')->mergeCells('F3:F4');

        $sheet->setCellValue('G3', 'PPN')->mergeCells('G3:H3');
        $sheet->setCellValue('I3', 'PPH')->mergeCells('I3:J3');
        $sheet->setCellValue('K3', 'BM')->mergeCells('K3:L3');

        $sheet->setCellValue('G4', 'Nilai')->setCellValue('H4', 'Status');
        $sheet->setCellValue('I4', 'Nilai')->setCellValue('J4', 'Status');
        $sheet->setCellValue('K4', 'Nilai')->setCellValue('L4', 'Status');

        $sheet->getStyle('A3:L4')->getFont()->setBold(true);
        $sheet->getStyle('A3:L4')->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        /* ===== DATA ===== */
        $rowNum = 5;
        $totalPPN = $totalPPH = $totalBM = 0;

        foreach ($rows as $r) {
            $sheet->fromArray([
                $r['no'],
                $r['supplier'],
                $r['date'],
                $r['no_aju'],
                $r['no_daftar'],
                $r['po_type'],
                $r['PPN_nilai'],
                $r['PPN_status'] ?: '',
                $r['PPH_nilai'],
                $r['PPH_status'] ?: '',
                $r['BM_nilai'],
                $r['BM_status'] ?: '',
            ], null, "A{$rowNum}");

            $totalPPN += $r['PPN_nilai'];
            $totalPPH += $r['PPH_nilai'];
            $totalBM  += $r['BM_nilai'];

            $rowNum++;
        }



        /* ===== TOTAL ===== */
        $sheet->mergeCells("A{$rowNum}:F{$rowNum}")
            ->setCellValue("A{$rowNum}", "TOTAL PPN");
        $sheet->setCellValue("G{$rowNum}", $totalPPN);
        $rowNum++;

        $sheet->mergeCells("A{$rowNum}:F{$rowNum}")
            ->setCellValue("A{$rowNum}", "TOTAL PPH");
        $sheet->setCellValue("G{$rowNum}", $totalPPH);
        $rowNum++;

        $sheet->mergeCells("A{$rowNum}:F{$rowNum}")
            ->setCellValue("A{$rowNum}", "TOTAL BM");
        $sheet->setCellValue("G{$rowNum}", $totalBM);
        $rowNum++;

        $sheet->mergeCells("A{$rowNum}:F{$rowNum}")
            ->setCellValue("A{$rowNum}", "GRAND TOTAL PUNGUTAN");
        $sheet->setCellValue("G{$rowNum}", $totalPPN + $totalPPH + $totalBM);

        /* ===== FORMAT ANGKA (30,000 TANPA RP) ===== */
        foreach (['G', 'I', 'K'] as $col) {
            $sheet->getStyle("{$col}5:{$col}{$rowNum}")
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        $sheet->getStyle("A{$rowNum}:L{$rowNum}")
            ->getFont()->setBold(true);

        foreach (range('A', 'L') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        /* ===== OUTPUT ===== */
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Pungutan_BC23.xlsx"');
        $writer->save('php://output');
        exit;
    }

    public function exportPdfPungutanBc23()
    {
        $addCondition = [
            "statusBC" => $this->request->getVar('statusBC'),
            "statusLPB" => $this->request->getVar('statusLPB'),
            "mulaiTanggalBC23" => !empty($this->request->getGet("mulaiTanggalBC23"))
                ? formatDMYtoYMD($this->request->getGet("mulaiTanggalBC23")) : '',
            "selesaiTanggalBC23" => !empty($this->request->getGet('selesaiTanggalBC23'))
                ? formatDMYtoYMD($this->request->getGet('selesaiTanggalBC23')) : '',
            "supplierName" => $this->request->getVar("supplierName"),
            "noAju" => $this->request->getVar("noAju"),
            "noPenerimaanBarang" => $this->request->getVar("noPenerimaanBarang"),
            "searchData" => trim($this->request->getGet('search')),
            "statusPosting" => "SUDAH POSTING",
        ];

        $condition = [
            "bc_purchase_order.deletedAt" => null,
            "bc_purchase_order.company_id" => $this->this_company_id,
            "bc_23.deletedAt" => null,
        ];

        $dataBC23 = $this->bc23Model->getList($condition, $addCondition, 10000000, 0);

        $result = [];
        $no = 1;

        foreach ($dataBC23['data'] as $data) {
            $row = [
                "no" => $no++,
                "supplier_name" => $data->supplier_name,
                "date" => date('d/m/Y', strtotime($data->createdAt)),
                "no_aju" => $data->no_aju,
                "no_daftar" => $data->no_daftar,
                "po_type" => $data->po_type,
                "pungutan" => [
                    'PPN' => ['nilai' => 0, 'status' => '-'],
                    'PPH' => ['nilai' => 0, 'status' => '-'],
                    'BM'  => ['nilai' => 0, 'status' => '-'],
                ]
            ];

            $tarifs = $this->bcTarifModel->getByBcPurchaseOrder($data->bc_purchase_order_id) ?? [];

            foreach ($tarifs as $tarif) {
                switch ($tarif['kode_jenis_pungutan']) {
                    case '1':
                    case 'PPN':
                        $jenis = 'PPN';
                        break;
                    case '2':
                    case 'PPH':
                        $jenis = 'PPH';
                        break;
                    case '3':
                    case 'BM':
                        $jenis = 'BM';
                        break;
                    default:
                        continue 2;
                }

                $row['pungutan'][$jenis]['nilai'] += (int)$tarif['nilai_bayar'];

                switch ($tarif['kode_fasilitas_tarif']) {
                    case '3':
                        $row['pungutan'][$jenis]['status'] =  $row['pungutan'][$jenis]['nilai'] == 0 ? '' : 'Ditangguhkan';
                        break;
                    case '5':
                        $row['pungutan'][$jenis]['status'] = $row['pungutan'][$jenis]['nilai'] == 0 ? '' :  'Dibebaskan';
                        break;
                    case '6':
                        $row['pungutan'][$jenis]['status'] = $row['pungutan'][$jenis]['nilai'] == 0 ? '' :  'Tidak Dipungut';
                        break;
                }
            }

            $result[] = $row;
        }

        $periode = ($addCondition['mulaiTanggalBC23'] && $addCondition['selesaiTanggalBC23'])
            ? date('d/m/Y', strtotime($addCondition['mulaiTanggalBC23'])) .
            ' s/d ' .
            date('d/m/Y', strtotime($addCondition['selesaiTanggalBC23']))
            : 'SEMUA PERIODE';

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('Laporan/LaporanBeaCukai/2.3/print', [
            'data' => $result,
            'periode' => $periode,
            'company' => $this->companiesModel->find($this->this_company_id),
        ]));

        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan_Pungutan_BC23.pdf", ["Attachment" => false]);
    }


    //---------------------------------------------------

    public function pungutanBc40()
    {
        return view('Laporan/LaporanBeaCukai/4.0/index');
    }

    public function allPungutanBc40()
    {
        $payload = [
            "pageSize"    => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"      => $this->request->getGet("search"),
            "sort"        => $this->request->getGet("sort"),
            "sortType"    => $this->request->getGet("sortType"),
            "company_id"  => $this->this_company_id,
            "type"        => "BC 4.0"
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusBC" => $this->request->getGet("statusBC"),
            "statusLPB" => $this->request->getGet("statusLPB"),
            "supplierName" => $this->request->getGet("supplierName"),
            "mulaiTanggalBC40" => $this->request->getGet("mulaiTanggalBC40")
                ? formatDMYtoYMD($this->request->getGet("mulaiTanggalBC40"))
                : '',
            "selesaiTanggalBC40" => $this->request->getGet("selesaiTanggalBC40")
                ? formatDMYtoYMD($this->request->getGet("selesaiTanggalBC40"))
                : '',
            "noPenerimaanBarang" => $this->request->getGet('noPenerimaanBarang'),
            "statusPosting" => "SUDAH POSTING",
            "noAju" => $this->request->getGet('noAju'),
            "searchData" => trim($this->request->getGet('search'))
        ];

        $condition = [
            "bc_purchase_order.deletedAt" => null,
            "bc_purchase_order.company_id" => $this->this_company_id,
            "bc_40.deletedAt" => null,
        ];

        $limit  = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $dataBC40 = $this->bc40Model->getList($condition, $addCondition, $limit, $offset);

        $result = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataBC40['data'] ?? [] as $row) {

            $ppnNilai  = 0;
            $ppnStatus = '';

            $tarifs = $this->bcTarifModel->getByBcPurchaseOrder($row->bc_purchase_order_id);

            foreach ($tarifs as $tarif) {
                if ($tarif['kode_jenis_pungutan'] !== '1' && $tarif['kode_jenis_pungutan'] !== 'PPN') {
                    continue;
                }

                $ppnNilai += (float) $tarif['nilai_bayar'];

                switch ($tarif['kode_fasilitas_tarif']) {
                    case '3':
                        $ppnStatus =  $tarif['nilai_bayar'] == 0 ? '' : 'Ditangguhkan';
                        break 2;
                    case '5':
                        $ppnStatus =  $tarif['nilai_bayar'] == 0 ? '' : 'Dibebaskan';
                        break;
                    case '6':
                        $ppnStatus = $tarif['nilai_bayar'] == 0 ? '' : 'Tidak Dipungut';
                        break;
                }
            }

            $result[] = [
                "no" => $no++,
                "id" => encrypt($row->id),
                "supplier_name" => $row->supplier_name,
                "date" => $row->createdAt
                    ? date('d/m/Y', strtotime($row->createdAt))
                    : '-',
                "no_aju" => $row->no_aju,
                "no_daftar" => $row->no_daftar,
                "po_type" => $row->po_type,
                "ppn_nilai" => $ppnNilai,
                "ppn_status" => $ppnStatus,
            ];
        }

        return response()->setJSON([
            "draw" => intval($this->request->getVar("draw")),
            "recordsTotal" => $dataBC40['totalData'] ?? 0,
            "recordsFiltered" => $dataBC40['totalFilteredData'] ?? 0,
            "data" => $result,
            "payload" => $payload
        ]);
    }


    public function exportExcelPungutanBc40()
    {
        $addCondition = [
            "statusBC" => $this->request->getVar('statusBC'),
            "statusLPB" => $this->request->getVar('statusLPB'),
            "mulaiTanggalBC40" => $this->request->getGet("mulaiTanggalBC40")
                ? formatDMYtoYMD($this->request->getGet("mulaiTanggalBC40"))
                : '',
            "selesaiTanggalBC40" => $this->request->getGet("selesaiTanggalBC40")
                ? formatDMYtoYMD($this->request->getGet("selesaiTanggalBC40"))
                : '',
            "supplierName" => $this->request->getVar("supplierName"),
            "noAju" => $this->request->getVar("noAju"),
            "noPenerimaanBarang" => $this->request->getVar("noPenerimaanBarang"),
            "searchData" => trim($this->request->getGet('search')),
            "statusPosting" => "SUDAH POSTING",
        ];

        $condition = [
            "bc_purchase_order.deletedAt" => null,
            "bc_purchase_order.company_id" => $this->this_company_id,
            "bc_40.deletedAt" => null,
        ];

        $dataBC40 = $this->bc40Model->getList($condition, $addCondition, 10000000, 0);

        $rows = [];
        $no = 1;
        $totalPPN = 0;

        foreach ($dataBC40['data'] ?? [] as $data) {

            $ppnNilai  = 0;
            $ppnStatus = '';

            $tarifs = $this->bcTarifModel->getByBcPurchaseOrder($data->bc_purchase_order_id);

            foreach ($tarifs as $tarif) {
                if ($tarif['kode_jenis_pungutan'] !== '1' && $tarif['kode_jenis_pungutan'] !== 'PPN') {
                    continue;
                }

                $ppnNilai += (float) $tarif['nilai_bayar'];

                switch ($tarif['kode_fasilitas_tarif']) {
                    case '3':
                        $ppnStatus = $tarif['nilai_bayar'] == 0 ? '' : 'Ditangguhkan';
                        break 2;
                    case '5':
                        $ppnStatus = $tarif['nilai_bayar'] == 0 ? '' : 'Dibebaskan';
                        break;
                    case '6':
                        $ppnStatus = $tarif['nilai_bayar'] == 0 ? '' : 'Tidak Dipungut';
                        break;
                }
            }

            $totalPPN += $ppnNilai;

            $rows[] = [
                'no' => $no++,
                'supplier' => $data->supplier_name,
                'tanggal' => $data->createdAt ? date('d/m/Y', strtotime($data->createdAt)) : '-',
                'no_aju' => $data->no_aju,
                'no_daftar' => $data->no_daftar,
                'po_type' => $data->po_type,
                'ppn_nilai' => $ppnNilai,
                'ppn_status' => $ppnStatus,
            ];
        }

        /* ================= EXCEL ================= */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* ===== HEADER LAPORAN ===== */
        $periode = 'ALL';
        if ($addCondition['mulaiTanggalBC40'] && $addCondition['selesaiTanggalBC40']) {
            $periode =
                date('d/m/Y', strtotime($addCondition['mulaiTanggalBC40'])) .
                ' s.d ' .
                date('d/m/Y', strtotime($addCondition['selesaiTanggalBC40']));
        }

        $sheet->setCellValue('A1', 'LAPORAN PUNGUTAN BC 4.0');
        $sheet->setCellValue('A2', 'Periode : ' . $periode);
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');

        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        /* ===== HEADER TABLE ===== */
        $sheet->setCellValue('A4', 'No')
            ->setCellValue('B4', 'Nama Supplier')
            ->setCellValue('C4', 'Tanggal')
            ->setCellValue('D4', 'No Aju')
            ->setCellValue('E4', 'No Daftar')
            ->setCellValue('F4', 'Tipe PO')
            ->setCellValue('G4', 'PPN Nilai')
            ->setCellValue('H4', 'PPN Status');

        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        /* ===== DATA ===== */
        $rowExcel = 5;
        foreach ($rows as $r) {
            $sheet->setCellValue('A' . $rowExcel, $r['no'])
                ->setCellValue('B' . $rowExcel, $r['supplier'])
                ->setCellValue('C' . $rowExcel, $r['tanggal'])
                ->setCellValue('D' . $rowExcel, $r['no_aju'])
                ->setCellValue('E' . $rowExcel, $r['no_daftar'])
                ->setCellValue('F' . $rowExcel, $r['po_type'])
                ->setCellValue('G' . $rowExcel, $r['ppn_nilai'])
                ->setCellValue('H' . $rowExcel, $r['ppn_status']);
            $rowExcel++;
        }

        /* ===== TOTAL ===== */
        $sheet->setCellValue('F' . $rowExcel, 'TOTAL PPN');
        $sheet->setCellValue('G' . $rowExcel, $totalPPN);
        $sheet->getStyle('F' . $rowExcel . ':G' . $rowExcel)->getFont()->setBold(true);

        /* ===== FORMAT ANGKA ===== */
        $sheet->getStyle('G5:G' . $rowExcel)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle('G5:G' . $rowExcel)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        /* ===== AUTOSIZE ===== */
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        /* ===== OUTPUT ===== */
        $filename = 'Laporan_Pungutan_BC_4_0_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdfPungutanBc40()
    {
        $addCondition = [
            "statusBC" => $this->request->getVar('statusBC'),
            "statusLPB" => $this->request->getVar('statusLPB'),
            "mulaiTanggalBC40" => $this->request->getGet("mulaiTanggalBC40")
                ? formatDMYtoYMD($this->request->getGet("mulaiTanggalBC40"))
                : '',
            "selesaiTanggalBC40" => $this->request->getGet("selesaiTanggalBC40")
                ? formatDMYtoYMD($this->request->getGet("selesaiTanggalBC40"))
                : '',
            "supplierName" => $this->request->getVar("supplierName"),
            "noAju" => $this->request->getVar("noAju"),
            "noPenerimaanBarang" => $this->request->getVar("noPenerimaanBarang"),
            "searchData" => trim($this->request->getGet('search')),
            "statusPosting" => "SUDAH POSTING",
        ];

        $condition = [
            "bc_purchase_order.deletedAt" => null,
            "bc_purchase_order.company_id" => $this->this_company_id,
            "bc_40.deletedAt" => null,
        ];

        $dataBC40 = $this->bc40Model->getList($condition, $addCondition, 10000000, 0);

        $rows = [];
        $no = 1;
        $totalPPN = 0;

        foreach ($dataBC40['data'] ?? [] as $data) {

            $ppnNilai  = 0;
            $ppnStatus = '';

            $tarifs = $this->bcTarifModel->getByBcPurchaseOrder($data->bc_purchase_order_id);

            foreach ($tarifs as $tarif) {
                if ($tarif['kode_jenis_pungutan'] !== '1' && $tarif['kode_jenis_pungutan'] !== 'PPN') {
                    continue;
                }

                $ppnNilai += (float) $tarif['nilai_bayar'];

                switch ($tarif['kode_fasilitas_tarif']) {
                    case '3':
                        $ppnStatus = $tarif['nilai_bayar'] == 0 ? '' : 'Ditangguhkan';
                        break 2;
                    case '5':
                        $ppnStatus = $tarif['nilai_bayar'] == 0 ? '' : 'Dibebaskan';
                        break;
                    case '6':
                        $ppnStatus = $tarif['nilai_bayar'] == 0 ? '' : 'Tidak Dipungut';
                        break;
                }
            }

            $totalPPN += $ppnNilai;

            $rows[] = [
                "no" => $no++,
                "supplier_name" => $data->supplier_name,
                "date" => $data->createdAt
                    ? date('d/m/Y', strtotime($data->createdAt))
                    : '-',
                "no_aju" => $data->no_aju,
                "no_daftar" => $data->no_daftar,
                "po_type" => $data->po_type,
                "ppn_nilai" => $ppnNilai,
                "ppn_status" => $ppnStatus,
            ];
        }

        $label = "Laporan Pungutan BC 4.0";

        $domPdf = new Dompdf();
        $domPdf->loadHtml(view(
            'Laporan/LaporanBeaCukai/4.0/print',
            [
                'data' => $rows,
                'condition' => $addCondition,
                'company' => $this->companiesModel->find($_SESSION['login']->this_company_id),
                'label' => $label,
                'totalPPN' => $totalPPN
            ]
        ));

        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream($label, ["Attachment" => false]);
    }


    //-----------------------------------------------------

    public function laporanDuaLima()
    {

        return view('Laporan/LaporanBeaCukai/2.5/index');
    }

    public function allBCDuaLima()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 2.5"
        ];

        $condition = [
            "bc_25.company_id"  => $this->this_company_id,
            "bc_25.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC25" => $this->request->getGet("mulaiTanggalBC25"),
            "selesaiTanggalBC25" => $this->request->getGet('selesaiTanggalBC25'),
            "noAju" => $this->request->getGet('noAju'),
            'asalPengeluaran' => $this->request->getGet('asalPengeluaran'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataBC25 = $this->bc25Model->getList($condition, $addCondition, $limit, $offset);

        // Pastikan datanya ada sebelum lanjut
        if (!empty($dataBC25['data'])) {
            $dataBC25Result = [];
            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
            foreach ($dataBC25['data'] as $data) {
                // Decode payload JSON
                $payloadData = json_decode($data->payload, true);

                if (empty($payloadData)) {
                    continue;
                }

                if (isset($payloadData['barang']) && !empty($payloadData['barang'])) {
                    foreach ($payloadData['barang'] as $barang) {

                        // Jika id sudah ada, tambahkan tarifnya
                        $dataBC25Result[$data->id] = [
                            "no" => $no++,  // Nomor akan selalu bertambah
                            "id" => encrypt($data->id),
                            "asal_pengeluaran"      => ($data->sales_order_lain_id != null) ? "PENJUALAN" : "RETUR",
                            "supplier_name" => isset($data->supplier_name) ? $data->supplier_name : $data->customer_name,
                            "no_aju" => $data->no_aju,
                            "no_daftar" => $data->no_daftar,
                            "date" => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                            "dataBCTarif" => [
                                'PPN' => [
                                    'di_bebaskan' => 0,
                                    'di_bayar' => 0,
                                    'di_tanggung_pemerintah' => 0,
                                    'di_lunasi' => 0
                                ],
                                'PPH' => [
                                    'di_bebaskan' => 0,
                                    'di_bayar' => 0,
                                    'di_tanggung_pemerintah' => 0,
                                    'di_lunasi' => 0
                                ],
                                'BM' => [
                                    'di_bebaskan' => 0,
                                    'di_bayar' => 0,
                                    'di_tanggung_pemerintah' => 0,
                                    'di_lunasi' => 0
                                ],
                            ]
                        ];

                        // Loop data barangTarif dan tambahkan nilai ke entri yang sudah ada
                        foreach ($barang['barangTarif'] as $tarif) {
                            $jenisPungutan = '';

                            switch ($tarif['kodeJenisPungutan']) {
                                case 'PPN':
                                    $jenisPungutan = 'PPN';
                                    break;
                                case 'PPH':
                                    $jenisPungutan = 'PPH';
                                    break;
                                case 'BM':
                                    $jenisPungutan = 'BM';
                                    break;
                            }

                            if (!empty($jenisPungutan)) {
                                switch ($tarif['kodeFasilitasTarif']) {
                                    case '1':
                                        $dataBC25Result[$data->id]['dataBCTarif'][$jenisPungutan]['di_bayar'] += (int)$tarif['nilaiBayar'];
                                        break;
                                    case '5':
                                        $dataBC25Result[$data->id]['dataBCTarif'][$jenisPungutan]['di_bebaskan'] += (int)$tarif['nilaiBayar'];
                                        break;
                                    case '2':
                                        $dataBC25Result[$data->id]['dataBCTarif'][$jenisPungutan]['di_tanggung_pemerintah'] += (int)$tarif['nilaiBayar'];
                                        break;
                                    case '7':
                                        $dataBC25Result[$data->id]['dataBCTarif'][$jenisPungutan]['di_lunasi'] += (int)$tarif['nilaiBayar'];
                                        break;
                                }
                            }
                        }
                    }
                }
            }

            // Ubah array asosiatif menjadi array numerik untuk respons DataTables
            $dataBC25Result = array_values($dataBC25Result);

            // Format response untuk DataTables
            $data = [
                "draw" => intval($this->request->getVar("draw")),
                "recordsTotal" => $dataBC25['totalData'],
                "recordsFiltered" => $dataBC25['totalFilteredData'],
                "data" => $dataBC25Result,
                "payload" => $payload
            ];

            return response()->setJSON($data);
        } else {
            // Jika data kosong, kirim response kosong juga
            return response()->setJSON([
                "draw" => intval($this->request->getVar("draw")),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "payload" => $payload
            ]);
        }
    }

    public function exportExcelLaporanBCDuaLima()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
            "type" => "BC 2.5" // Ensure correct type is used here
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC25" => $this->request->getGet("mulaiTanggalBC25"),
            "selesaiTanggalBC25" => $this->request->getGet('selesaiTanggalBC25'),
            "noAju" => $this->request->getGet('noAju'),
            'asalPengeluaran' => $this->request->getGet('asalPengeluaran'),
        ];

        $condition = [
            "bc_25.company_id"  => $this->this_company_id,
            "bc_25.deletedAt" => null, // Adjust for BC 25
        ];

        // Retrieve all data based on the conditions without pagination
        $dataBC25 = $this->bc25Model->getList($condition, $addCondition, 10000000, 0);

        // Prepare data for Excel
        $dataBC25Result = [];
        $no = 1; // Resetting no for Excel export

        // Loop data utama dari $dataBC25
        foreach ($dataBC25['data'] as $data) {
            $entry = [
                "no" => $no++,
                "id" => encrypt($data->id),
                "asal_pengeluaran"      => ($data->sales_order_lain_id != null) ? "PENJUALAN" : "RETUR",
                "supplier_name" => isset($data->supplier_name) ? $data->supplier_name : $data->customer_name,
                "asal_pengeluaran"      => ($data->sales_order_lain_id != null) ? "PENJUALAN" : "RETUR",
                "no_aju" => $data->no_aju,
                "no_daftar" => $data->no_daftar,
                "date" => $data->createdAt,
                "dataBCTarif" => [
                    'PPN' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                    'PPH' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                    'BM' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                ]
            ];

            // Looping dataBCTarif untuk mengisi nilai
            $payloadData = json_decode($data->payload, true);

            if (isset($payloadData['barang']) && !empty($payloadData['barang'])) {
                foreach ($payloadData['barang'] as $barang) {

                    foreach ($barang['barangTarif'] as $tarif) {
                        $jenisPungutan = '';

                        switch ($tarif['kodeJenisPungutan']) {
                            case 'PPN':
                                $jenisPungutan = 'PPN';
                                break;
                            case 'PPH':
                                $jenisPungutan = 'PPH';
                                break;
                            case 'BM':
                                $jenisPungutan = 'BM';
                                break;
                        }

                        if (!empty($jenisPungutan)) {
                            switch ($tarif['kodeFasilitasTarif']) {
                                case '1':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_bayar'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '5':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_bebaskan'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '2':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_tanggung_pemerintah'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '7':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_lunasi'] += (int)$tarif['nilaiBayar'];
                                    break;
                            }
                        }
                    }
                }
            }

            $dataBC25Result[] = $entry;
        }

        // Generate the spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header style
        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        // Set the headers
        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama Supplier/Customer')
            ->setCellValue('C1', 'Tanggal')
            ->setCellValue('D1', 'No Aju')
            ->setCellValue('E1', 'No Daftar');

        // Merging cells for PPN, PPH, and BM
        $sheet->setCellValue('F1', 'PPN')->mergeCells('F1:I1');
        $sheet->setCellValue('J1', 'PPH')->mergeCells('J1:M1');
        $sheet->setCellValue('N1', 'BM')->mergeCells('N1:Q1');

        // Second row headers for PPN, PPH, and BM
        $sheet->setCellValue('F2', 'Di Bayar')
            ->setCellValue('G2', 'Di Bebaskan')
            ->setCellValue('H2', 'Di Tanggung Pemerintah')
            ->setCellValue('I2', 'Di Lunasi')
            ->setCellValue('J2', 'Di Bayar')
            ->setCellValue('K2', 'Di Bebaskan')
            ->setCellValue('L2', 'Di Tanggung Pemerintah')
            ->setCellValue('M2', 'Di Lunasi')
            ->setCellValue('N2', 'Di Bayar')
            ->setCellValue('O2', 'Di Bebaskan')
            ->setCellValue('P2', 'Di Tanggung Pemerintah')
            ->setCellValue('Q2', 'Di Lunasi');

        // Applying header style
        $sheet->getStyle('A1:Q2')->applyFromArray($headerStyleArray);

        // Fill the data rows
        $row = 3; // Starting from row 3 after the headers
        foreach ($dataBC25Result as $result) {
            $sheet->setCellValue('A' . $row, $result['no'])
                ->setCellValue('B' . $row, $result['supplier_name'])
                ->setCellValue('C' . $row, $result['date'])
                ->setCellValue('D' . $row, $result['no_aju'])
                ->setCellValue('E' . $row, $result['no_daftar'])
                ->setCellValue('F' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPN']['di_bayar']))
                ->setCellValue('G' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPN']['di_bebaskan']))
                ->setCellValue('H' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPN']['di_tanggung_pemerintah']))
                ->setCellValue('I' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPN']['di_lunasi']))
                ->setCellValue('J' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPH']['di_bayar']))
                ->setCellValue('K' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPH']['di_bebaskan']))
                ->setCellValue('L' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPH']['di_tanggung_pemerintah']))
                ->setCellValue('M' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPH']['di_lunasi']))
                ->setCellValue('N' . $row, formatRupiahPdfExcel($result['dataBCTarif']['BM']['di_bayar']))
                ->setCellValue('O' . $row, formatRupiahPdfExcel($result['dataBCTarif']['BM']['di_bebaskan']))
                ->setCellValue('P' . $row, formatRupiahPdfExcel($result['dataBCTarif']['BM']['di_tanggung_pemerintah']))
                ->setCellValue('Q' . $row, formatRupiahPdfExcel($result['dataBCTarif']['BM']['di_lunasi']));

            $row++;
        }

        // Set auto column width
        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Export the file
        $filename = "Laporan_BC_25_" . date('Y-m-d-His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }


    public function exportPDFLaporanBCDuaLima()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
            "type" => "BC 2.5" // Ensure correct type is used here
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC25" => $this->request->getGet("mulaiTanggalBC25"),
            "selesaiTanggalBC25" => $this->request->getGet('selesaiTanggalBC25'),
            "noAju" => $this->request->getGet('noAju'),
            'asalPengeluaran' => $this->request->getGet('asalPengeluaran'),
        ];

        $condition = [
            "bc_25.company_id"  => $this->this_company_id,
            "bc_25.deletedAt" => null, // Adjust for BC 25
        ];

        // Retrieve all data based on the conditions without pagination
        $dataBC25 = $this->bc25Model->getList($condition, $addCondition, 10000000, 0);

        // Prepare data for Excel
        $dataBC25Result = [];
        $no = 1; // Resetting no for Excel export

        // Loop data utama dari $dataBC25
        foreach ($dataBC25['data'] as $data) {
            $entry = [
                "no" => $no++,
                "id" => encrypt($data->id),
                "supplier_name" => isset($data->supplier_name) ? $data->supplier_name : $data->customer_name,
                "asal_pengeluaran"      => ($data->sales_order_lain_id != null) ? "PENJUALAN" : "RETUR",
                "no_aju" => $data->no_aju,
                "no_daftar" => $data->no_daftar,
                "date" => $data->createdAt,
                "dataBCTarif" => [
                    'PPN' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                    'PPH' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                    'BM' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                ]
            ];

            // Looping dataBCTarif untuk mengisi nilai
            $payloadData = json_decode($data->payload, true);

            if (isset($payloadData['barang']) && !empty($payloadData['barang'])) {
                foreach ($payloadData['barang'] as $barang) {

                    foreach ($barang['barangTarif'] as $tarif) {
                        $jenisPungutan = '';

                        switch ($tarif['kodeJenisPungutan']) {
                            case 'PPN':
                                $jenisPungutan = 'PPN';
                                break;
                            case 'PPH':
                                $jenisPungutan = 'PPH';
                                break;
                            case 'BM':
                                $jenisPungutan = 'BM';
                                break;
                        }

                        if (!empty($jenisPungutan)) {
                            switch ($tarif['kodeFasilitasTarif']) {
                                case '1':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_bayar'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '5':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_bebaskan'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '2':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_tanggung_pemerintah'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '7':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_lunasi'] += (int)$tarif['nilaiBayar'];
                                    break;
                            }
                        }
                    }
                }
            }

            $dataBC25Result[] = $entry;
        }

        // Inisialisasi Dompdf
        $domPdf = new Dompdf();

        // Label untuk laporan
        $label = "Laporan Bea Cukai BC 2.5 : " . $this->request->getVar('dateStart') . " / " . $this->request->getVar('dateEnd');

        // Load view untuk PDF
        $domPdf->loadHtml(view('Laporan/LaporanBeaCukai/2.5/print', [
            'data' => $dataBC25Result,
            'condition' => $addCondition,
            'company' => $this->companiesModel->find($_SESSION['login']->this_company_id),
            'label' => $label
        ]));

        // Set kertas untuk PDF
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream($label, ["Attachment" => false]);
    }

    public function laporanTigaKosong()
    {

        return view('Laporan/LaporanBeaCukai/3.0/index');
    }

    public function allBCTigaKosong()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 3.0"
        ];

        $condition = [
            "bc_30.company_id"  => $this->this_company_id,
            "bc_30.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC30" => $this->request->getGet("mulaiTanggalBC30"),
            "selesaiTanggalBC30" => $this->request->getGet('selesaiTanggalBC30'),
            "noAju" => $this->request->getGet('noAju'),
            "tipeSalesOrder" => $this->request->getGet('tipeSalesOrder')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataBC30 = $this->bc30Model->getList($condition, $addCondition, $limit, $offset);

        // Pastikan datanya ada sebelum lanjut
        if (!empty($dataBC30['data'])) {
            $dataBC30Result = [];
            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($dataBC30['data'] as $data) {
                // Decode payload JSON
                $payloadData = json_decode($data->payload, true);

                // Tetap buat entry, meskipun payloadData kosong
                $dataBC30Result[$data->id] = [
                    "no" => $no++,
                    "id"                    => encrypt($data->id),
                    "no_aju"                => $data->no_aju . " / " . $data->no_daftar,
                    "tanggal_bc_30"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                    "no_daftar"             => $data->no_daftar,
                    "no_stuffing"           => $data->no_stuffing,
                    "dataBCTarif" => [
                        'PPN' => [
                            'di_bebaskan' => 0,
                            'di_bayar' => 0,
                            'di_tanggung_pemerintah' => 0,
                            'di_lunasi' => 0
                        ],
                        'PPH' => [
                            'di_bebaskan' => 0,
                            'di_bayar' => 0,
                            'di_tanggung_pemerintah' => 0,
                            'di_lunasi' => 0
                        ],
                        'BM' => [
                            'di_bebaskan' => 0,
                            'di_bayar' => 0,
                            'di_tanggung_pemerintah' => 0,
                            'di_lunasi' => 0
                        ],
                    ]
                ];

                // Lanjutkan jika payloadData memiliki barang
                if (isset($payloadData['barang']) && !empty($payloadData['barang'])) {
                    foreach ($payloadData['barang'] as $barang) {
                        // Lanjutkan jika barang memiliki barangTarif
                        if (isset($barang['barangTarif']) && !empty($barang['barangTarif'])) {
                            // Loop data barangTarif dan tambahkan nilai ke entri yang sudah ada
                            foreach ($barang['barangTarif'] as $tarif) {
                                $jenisPungutan = '';

                                // Tentukan jenis pungutan
                                switch ($tarif['kodeJenisPungutan']) {
                                    case 'PPN':
                                        $jenisPungutan = 'PPN';
                                        break;
                                    case 'PPH':
                                        $jenisPungutan = 'PPH';
                                        break;
                                    case 'BM':
                                        $jenisPungutan = 'BM';
                                        break;
                                }

                                if (!empty($jenisPungutan)) {
                                    // Gunakan ternary untuk nilaiBayar, jika tidak ada, gunakan 0
                                    $nilaiBayar = isset($tarif['nilaiBayar']) ? (int)$tarif['nilaiBayar'] : 0;

                                    switch ($tarif['kodeFasilitasTarif']) {
                                        case '1':
                                            $dataBC30Result[$data->id]['dataBCTarif'][$jenisPungutan]['di_bayar'] += $nilaiBayar;
                                            break;
                                        case '5':
                                            $dataBC30Result[$data->id]['dataBCTarif'][$jenisPungutan]['di_bebaskan'] += $nilaiBayar;
                                            break;
                                        case '2':
                                            $dataBC30Result[$data->id]['dataBCTarif'][$jenisPungutan]['di_tanggung_pemerintah'] += $nilaiBayar;
                                            break;
                                        case '7':
                                            $dataBC30Result[$data->id]['dataBCTarif'][$jenisPungutan]['di_lunasi'] += $nilaiBayar;
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Ubah array asosiatif menjadi array numerik untuk respons DataTables
            $dataBC30Result = array_values($dataBC30Result);


            // Format response untuk DataTables
            $data = [
                "draw" => intval($this->request->getVar("draw")),
                "recordsTotal" => $dataBC30['totalData'],
                "recordsFiltered" => $dataBC30['totalFilteredData'],
                "data" => $dataBC30Result,
                "payload" => $payload
            ];

            return response()->setJSON($data);
        } else {
            // Jika data kosong, kirim response kosong juga
            return response()->setJSON([
                "draw" => intval($this->request->getVar("draw")),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "payload" => $payload
            ]);
        }
    }

    public function exportExcelLaporanBCTigaKosong()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
            "type" => "BC 3.0" // Ensure correct type is used here
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC30" => $this->request->getGet("mulaiTanggalBC30"),
            "selesaiTanggalBC30" => $this->request->getGet('selesaiTanggalBC30'),
            "noAju" => $this->request->getGet('noAju'),
            "tipeSalesOrder" => $this->request->getGet('tipeSalesOrder')
        ];

        $condition = [
            "bc_30.company_id" => $this->this_company_id,
            "bc_30.deletedAt" => null, // Adjust for BC 30
        ];

        // Retrieve all data based on the conditions without pagination
        $dataBC30 = $this->bc30Model->getList($condition, $addCondition, 10000000, 0);

        // Prepare data for Excel
        $dataBC30Result = [];
        $no = 1; // Resetting no for Excel export

        // Loop data utama dari $dataBC30
        foreach ($dataBC30['data'] as $data) {
            $entry = [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_aju" => $data->no_aju,
                "no_daftar" =>  $data->no_daftar,
                "no_stuffing" =>  $data->no_stuffing,
                "date"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),

                "dataBCTarif" => [
                    'PPN' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                    'PPH' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                    'BM' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                ]
            ];

            // Looping dataBCTarif untuk mengisi nilai
            $payloadData = json_decode($data->payload, true);

            if (isset($payloadData['barang']) && !empty($payloadData['barang'])) {
                foreach ($payloadData['barang'] as $barang) {
                    if (isset($barang['barangTarif']) && !empty($barang['barangTarif'])) {
                        foreach ($barang['barangTarif'] as $tarif) {
                            $jenisPungutan = '';

                            switch ($tarif['kodeJenisPungutan']) {
                                case 'PPN':
                                    $jenisPungutan = 'PPN';
                                    break;
                                case 'PPH':
                                    $jenisPungutan = 'PPH';
                                    break;
                                case 'BM':
                                    $jenisPungutan = 'BM';
                                    break;
                            }

                            if (!empty($jenisPungutan)) {
                                switch ($tarif['kodeFasilitasTarif']) {
                                    case '1':
                                        $entry['dataBCTarif'][$jenisPungutan]['di_bayar'] += (int)$tarif['nilaiBayar'];
                                        break;
                                    case '5':
                                        $entry['dataBCTarif'][$jenisPungutan]['di_bebaskan'] += (int)$tarif['nilaiBayar'];
                                        break;
                                    case '2':
                                        $entry['dataBCTarif'][$jenisPungutan]['di_tanggung_pemerintah'] += (int)$tarif['nilaiBayar'];
                                        break;
                                    case '7':
                                        $entry['dataBCTarif'][$jenisPungutan]['di_lunasi'] += (int)$tarif['nilaiBayar'];
                                        break;
                                }
                            }
                        }
                    }
                }
            }

            $dataBC30Result[] = $entry;
        }

        // Generate the spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header style
        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        // Set the headers
        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Tanggal')
            ->setCellValue('C1', 'No Aju')
            ->setCellValue('D1', 'No Daftar');


        // Merging cells for PPN, PPH, and BM
        $sheet->setCellValue('F1', 'PPN')->mergeCells('F1:I1');
        $sheet->setCellValue('J1', 'PPH')->mergeCells('J1:M1');
        $sheet->setCellValue('N1', 'BM')->mergeCells('N1:Q1');

        // Second row headers for PPN, PPH, and BM
        $sheet->setCellValue('F2', 'Di Bayar')
            ->setCellValue('G2', 'Di Bebaskan')
            ->setCellValue('H2', 'Di Tanggung Pemerintah')
            ->setCellValue('I2', 'Di Lunasi')
            ->setCellValue('J2', 'Di Bayar')
            ->setCellValue('K2', 'Di Bebaskan')
            ->setCellValue('L2', 'Di Tanggung Pemerintah')
            ->setCellValue('M2', 'Di Lunasi')
            ->setCellValue('N2', 'Di Bayar')
            ->setCellValue('O2', 'Di Bebaskan')
            ->setCellValue('P2', 'Di Tanggung Pemerintah')
            ->setCellValue('Q2', 'Di Lunasi');

        // Applying header style
        $sheet->getStyle('A1:Q2')->applyFromArray($headerStyleArray);

        // Fill the data rows
        $row = 3; // Starting from row 3 after the headers
        foreach ($dataBC30Result as $result) {
            $sheet->setCellValue('A' . $row, $result['no'])
                ->setCellValue('B' . $row, $result['date'])
                ->setCellValue('C' . $row, $result['no_aju'])
                ->setCellValue('D' . $row, $result['no_daftar'])
                ->setCellValue('E' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPN']['di_bayar']))
                ->setCellValue('F' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPN']['di_bebaskan']))
                ->setCellValue('G' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPN']['di_tanggung_pemerintah']))
                ->setCellValue('H' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPN']['di_lunasi']))
                ->setCellValue('I' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPH']['di_bayar']))
                ->setCellValue('J' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPH']['di_bebaskan']))
                ->setCellValue('K' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPH']['di_tanggung_pemerintah']))
                ->setCellValue('L' . $row, formatRupiahPdfExcel($result['dataBCTarif']['PPH']['di_lunasi']))
                ->setCellValue('M' . $row, formatRupiahPdfExcel($result['dataBCTarif']['BM']['di_bayar']))
                ->setCellValue('N' . $row, formatRupiahPdfExcel($result['dataBCTarif']['BM']['di_bebaskan']))
                ->setCellValue('O' . $row, formatRupiahPdfExcel($result['dataBCTarif']['BM']['di_tanggung_pemerintah']))
                ->setCellValue('P' . $row, formatRupiahPdfExcel($result['dataBCTarif']['BM']['di_lunasi']));

            $row++;
        }

        // Set auto column width
        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Export the file
        $filename = "Laporan_BC_30_" . date('Y-m-d-His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function exportPDFLaporanBCTigaKosong()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
            "type" => "BC 3.0" // Ensure correct type is used here
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC30" => $this->request->getGet("mulaiTanggalBC30"),
            "selesaiTanggalBC30" => $this->request->getGet('selesaiTanggalBC30'),
            "noAju" => $this->request->getGet('noAju'),
            "tipeSalesOrder" => $this->request->getGet('tipeSalesOrder')
        ];

        $condition = [
            "bc_30.company_id"  => $this->this_company_id,
            "bc_30.deletedAt" => null, // Adjust for BC 30
        ];

        // Retrieve all data based on the conditions without pagination
        $dataBC30 = $this->bc30Model->getList($condition, $addCondition, 10000000, 0);

        // Prepare data for Excel
        $dataBC30Result = [];
        $no = 1; // Resetting no for Excel export

        // Loop data utama dari $dataBC30
        foreach ($dataBC30['data'] as $data) {
            $entry = [
                "no" => $no++,
                "id"                    => encrypt($data->id),
                "date"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "no_aju"                => $data->no_aju,
                "no_daftar"                => $data->no_daftar,
                "no_stuffing"                => $data->no_stuffing,

                "dataBCTarif" => [
                    'PPN' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                    'PPH' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                    'BM' => [
                        'di_bebaskan' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0
                    ],
                ]
            ];

            // Looping dataBCTarif untuk mengisi nilai
            $payloadData = json_decode($data->payload, true);

            if (isset($payloadData['barang']) && !empty($payloadData['barang'])) {
                foreach ($payloadData['barang'] as $barang) {

                    foreach ($barang['barangTarif'] as $tarif) {
                        $jenisPungutan = '';

                        switch ($tarif['kodeJenisPungutan']) {
                            case 'PPN':
                                $jenisPungutan = 'PPN';
                                break;
                            case 'PPH':
                                $jenisPungutan = 'PPH';
                                break;
                            case 'BM':
                                $jenisPungutan = 'BM';
                                break;
                        }

                        if (!empty($jenisPungutan)) {
                            switch ($tarif['kodeFasilitasTarif']) {
                                case '1':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_bayar'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '5':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_bebaskan'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '2':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_tanggung_pemerintah'] += (int)$tarif['nilaiBayar'];
                                    break;
                                case '7':
                                    $entry['dataBCTarif'][$jenisPungutan]['di_lunasi'] += (int)$tarif['nilaiBayar'];
                                    break;
                            }
                        }
                    }
                }
            }

            $dataBC30Result[] = $entry;
        }

        // Inisialisasi Dompdf
        $domPdf = new Dompdf();

        // Label untuk laporan
        $label = "Laporan Bea Cukai BC 3.0 : " . $this->request->getVar('dateStart') . " / " . $this->request->getVar('dateEnd');

        // Load view untuk PDF
        $domPdf->loadHtml(view('Laporan/LaporanBeaCukai/3.0/print', [
            'data' => $dataBC30Result,
            'condition' => $addCondition,
            'company' => $this->companiesModel->find($_SESSION['login']->this_company_id),
            'label' => $label
        ]));

        // Set kertas untuk PDF
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream($label, ["Attachment" => false]);
    }

    public function laporanDuaTujuh()
    {

        return view('Laporan/LaporanBeaCukai/2.7/index');
    }

    public function allBCDuaTujuh()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 2.7"
        ];

        $condition = [
            "bc_27.company_tujuan_id"  => $this->this_company_id,
            "bc_27.deletedAt" => null,
            "mutasi_global.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC27" => $this->request->getGet("mulaiTanggalBC27"),
            "selesaiTanggalBC27" => $this->request->getGet('selesaiTanggalBC27'),
            "noAju" => $this->request->getGet('noAju'),
            "noBC27" => $this->request->getGet('noBC27'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataBC27 = $this->bc27Model->getList($condition, $addCondition, $limit, $offset);

        // Pastikan datanya ada sebelum lanjut
        if (!empty($dataBC27['data'])) {
            $dataBC27Result = [];
            $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

            foreach ($dataBC27['data'] as $data) {
                // Decode payload JSON
                $payloadData = json_decode($data->payload, true);

                // Tetap buat entry, meskipun payloadData kosong
                $dataBC27Result[$data->id] = [
                    "no" => $no++,
                    "id" => encrypt($data->id),
                    "no_aju" => $data->no_aju . " / " . $data->no_daftar,
                    "tanggal_bc_27" => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                    "no_daftar" => $data->no_daftar,
                    "pungutan" => [
                        'PPN' => [
                            'di_bebaskan' => 0,
                            'tidak_dipungunt' => 0,
                            'di_tunda' => 0,
                            'di_bayar' => 0,
                            'di_tanggung_pemerintah' => 0,
                            'di_lunasi' => 0,
                            'di_tangguhkan' => 0
                        ],
                        'PPH' => [
                            'di_bebaskan' => 0,
                            'tidak_dipungunt' => 0,
                            'di_tunda' => 0,
                            'di_bayar' => 0,
                            'di_tanggung_pemerintah' => 0,
                            'di_lunasi' => 0,
                            'di_tangguhkan' => 0
                        ],
                        'BM' => [
                            'di_bebaskan' => 0,
                            'tidak_dipungunt' => 0,
                            'di_tunda' => 0,
                            'di_bayar' => 0,
                            'di_tanggung_pemerintah' => 0,
                            'di_lunasi' => 0,
                            'di_tangguhkan' => 0
                        ],
                    ]
                ];

                // Lanjutkan hanya jika payloadData tidak kosong dan mengandung pungutan
                if (!empty($payloadData) && isset($payloadData['pungutan'])) {
                    foreach ($payloadData['pungutan'] as $pungutan) {
                        // Process each pungutan item individually
                        if (is_array($pungutan) && isset($pungutan['kodeJenisPungutan'])) {
                            $jenisPungutan = '';

                            switch ($pungutan['kodeJenisPungutan']) {
                                case 'PPN':
                                    $jenisPungutan = 'PPN';
                                    break;
                                case 'PPH':
                                    $jenisPungutan = 'PPH';
                                    break;
                                case 'BM':
                                    $jenisPungutan = 'BM';
                                    break;
                            }

                            if (!empty($jenisPungutan)) {
                                // Gunakan ternary untuk mengecek apakah 'nilaiPungutan' ada
                                $nilaiPungutan = isset($pungutan['nilaiPungutan']) ? (int)$pungutan['nilaiPungutan'] : 0;

                                switch ($pungutan['kodeFasilitasTarif']) {
                                    case '1':
                                        $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_bayar'] += $nilaiPungutan;
                                        break;
                                    case '5':
                                        $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_bebaskan'] += $nilaiPungutan;
                                        break;
                                    case '2':
                                        $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tanggung_pemerintah'] += $nilaiPungutan;
                                        break;
                                    case '7':
                                        $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_lunasi'] += $nilaiPungutan;
                                        break;
                                    case '6':
                                        $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tunda'] += $nilaiPungutan;
                                        break;
                                    case '9':
                                        $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['tidak_dipungut'] += $nilaiPungutan;
                                        break;
                                    case '3':
                                        $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tangguhkan'] += $nilaiPungutan;
                                        break;
                                }
                            }
                        }
                    }
                }
            }


            // Ubah array asosiatif menjadi array numerik untuk respons DataTables
            $dataBC27Result = array_values($dataBC27Result);

            // Format response untuk DataTables
            $data = [
                "draw" => intval($this->request->getVar("draw")),
                "recordsTotal" => $dataBC27['totalData'],
                "recordsFiltered" => $dataBC27['totalFilteredData'],
                "data" => $dataBC27Result,
                "payload" => $payload
            ];

            return response()->setJSON($data);
        } else {
            // Jika data kosong, kirim response kosong juga
            return response()->setJSON([
                "draw" => intval($this->request->getVar("draw")),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "payload" => $payload
            ]);
        }
    }

    public function exportExcelLaporanBCDuaTujuh()
    {
        $payload = [
            "pageSize"      => 100000,
            "currentPage"   => 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 2.7"
        ];

        $condition = [
            "bc_27.company_tujuan_id"  => $this->this_company_id,
            "bc_27.deletedAt" => null,
            "mutasi_global.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC27" => $this->request->getGet("mulaiTanggalBC27"),
            "selesaiTanggalBC27" => $this->request->getGet('selesaiTanggalBC27'),
            "noAju" => $this->request->getGet('noAju'),
            "noBC27" => $this->request->getGet('noBC27'),
        ];

        // Retrieve all data based on the conditions without pagination
        $dataBC27 = $this->bc27Model->getList($condition, $addCondition, 10000000, 0);
        $dataBC27Result = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataBC27['data'] as $data) {
            // Decode payload JSON
            $payloadData = json_decode($data->payload, true);

            // Tetap buat entry, meskipun payloadData kosong
            $dataBC27Result[$data->id] = [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_aju" => $data->no_aju . " / " . $data->no_daftar,
                "tanggal_bc_27" => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "no_daftar" => $data->no_daftar,
                "pungutan" => [
                    'PPN' => [
                        'di_bebaskan' => 0,
                        'tidak_dipungunt' => 0,
                        'di_tunda' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0,
                        'di_tangguhkan' => 0
                    ],
                    'PPH' => [
                        'di_bebaskan' => 0,
                        'tidak_dipungunt' => 0,
                        'di_tunda' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0,
                        'di_tangguhkan' => 0
                    ],
                    'BM' => [
                        'di_bebaskan' => 0,
                        'tidak_dipungunt' => 0,
                        'di_tunda' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0,
                        'di_tangguhkan' => 0
                    ],
                ]
            ];

            // Lanjutkan hanya jika payloadData tidak kosong dan mengandung pungutan
            if (!empty($payloadData) && isset($payloadData['pungutan'])) {
                foreach ($payloadData['pungutan'] as $pungutan) {
                    // Process each pungutan item individually
                    if (is_array($pungutan) && isset($pungutan['kodeJenisPungutan'])) {
                        $jenisPungutan = '';

                        switch ($pungutan['kodeJenisPungutan']) {
                            case 'PPN':
                                $jenisPungutan = 'PPN';
                                break;
                            case 'PPH':
                                $jenisPungutan = 'PPH';
                                break;
                            case 'BM':
                                $jenisPungutan = 'BM';
                                break;
                        }

                        if (!empty($jenisPungutan)) {
                            // Gunakan ternary untuk mengecek apakah 'nilaiPungutan' ada
                            $nilaiPungutan = isset($pungutan['nilaiPungutan']) ? (int)$pungutan['nilaiPungutan'] : 0;

                            switch ($pungutan['kodeFasilitasTarif']) {
                                case '1':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_bayar'] += $nilaiPungutan;
                                    break;
                                case '5':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_bebaskan'] += $nilaiPungutan;
                                    break;
                                case '2':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tanggung_pemerintah'] += $nilaiPungutan;
                                    break;
                                case '7':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_lunasi'] += $nilaiPungutan;
                                    break;
                                case '6':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tunda'] += $nilaiPungutan;
                                    break;
                                case '9':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['tidak_dipungut'] += $nilaiPungutan;
                                    break;
                                case '3':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tangguhkan'] += $nilaiPungutan;
                                    break;
                            }
                        }
                    }
                }
            }
        }

        // Generate the spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header style
        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        // Set the headers
        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Tanggal')
            ->setCellValue('C1', 'No Aju')
            ->setCellValue('D1', 'No Daftar');

        // Merging cells for PPN, PPH, and BM
        $sheet->setCellValue('E1', 'PPN')->mergeCells('E1:K1'); // PPN
        $sheet->setCellValue('L1', 'PPH')->mergeCells('L1:R1'); // PPH
        $sheet->setCellValue('S1', 'BM')->mergeCells('S1:Y1'); // BM

        // Second row headers for PPN
        $sheet->setCellValue('E2', 'Di Bayar')
            ->setCellValue('F2', 'Di Bebaskan')
            ->setCellValue('G2', 'Di Tanggung Pemerintah')
            ->setCellValue('H2', 'Di Lunasi')
            ->setCellValue('I2', 'Di Tunda')
            ->setCellValue('J2', 'Di Tangguhkan')
            ->setCellValue('K2', 'Tidak Dipungut');

        // Second row headers for PPH
        $sheet->setCellValue('L2', 'Di Bayar')
            ->setCellValue('M2', 'Di Bebaskan')
            ->setCellValue('N2', 'Di Tanggung Pemerintah')
            ->setCellValue('O2', 'Di Lunasi')
            ->setCellValue('P2', 'Di Tunda')
            ->setCellValue('Q2', 'Di Tangguhkan')
            ->setCellValue('R2', 'Tidak Dipungut');

        // Second row headers for BM
        $sheet->setCellValue('S2', 'Di Bayar')
            ->setCellValue('T2', 'Di Bebaskan')
            ->setCellValue('U2', 'Di Tanggung Pemerintah')
            ->setCellValue('V2', 'Di Lunasi')
            ->setCellValue('W2', 'Di Tunda')
            ->setCellValue('X2', 'Di Tangguhkan')
            ->setCellValue('Y2', 'Tidak Dipungut');

        // Applying header style
        $sheet->getStyle('A1:Y2')->applyFromArray($headerStyleArray);

        // Fill the data rows
        $row = 3; // Starting from row 3 after the headers
        foreach ($dataBC27Result as $result) {
            $sheet->setCellValue('A' . $row, $result['no'])
                ->setCellValue('B' . $row, $result['tanggal_bc_27']) // Use correct field name
                ->setCellValue('C' . $row, $result['no_aju'])
                ->setCellValue('D' . $row, $result['no_daftar'])
                //ppn
                ->setCellValue('E' . $row, formatRupiahPdfExcel($result['pungutan']['PPN']['di_bayar'])) // Di Bayar
                ->setCellValue('F' . $row, formatRupiahPdfExcel($result['pungutan']['PPN']['di_bebaskan'])) // Di Bebaskan
                ->setCellValue('G' . $row, formatRupiahPdfExcel($result['pungutan']['PPN']['di_tanggung_pemerintah'])) // Di Tanggung Pemerintah
                ->setCellValue('H' . $row, formatRupiahPdfExcel($result['pungutan']['PPN']['di_lunasi'])) // Di Lunasi
                ->setCellValue('I' . $row, formatRupiahPdfExcel($result['pungutan']['PPN']['di_tunda'])) // Di Tunda
                ->setCellValue('J' . $row, formatRupiahPdfExcel($result['pungutan']['PPN']['di_tangguhkan'])) // Di Tangguhkan
                ->setCellValue('K' . $row, formatRupiahPdfExcel($result['pungutan']['PPN']['tidak_dipungut'])) // Tidak Dipungut
                // PPH
                ->setCellValue('L' . $row, formatRupiahPdfExcel($result['pungutan']['PPH']['di_bayar'])) // Di Bayar
                ->setCellValue('M' . $row, formatRupiahPdfExcel($result['pungutan']['PPH']['di_bebaskan'])) // Di Bebaskan
                ->setCellValue('N' . $row, formatRupiahPdfExcel($result['pungutan']['PPH']['di_tanggung_pemerintah'])) // Di Tanggung Pemerintah
                ->setCellValue('O' . $row, formatRupiahPdfExcel($result['pungutan']['PPH']['di_lunasi'])) // Di Lunasi
                ->setCellValue('P' . $row, formatRupiahPdfExcel($result['pungutan']['PPH']['di_tunda'])) // Di Tunda
                ->setCellValue('Q' . $row, formatRupiahPdfExcel($result['pungutan']['PPH']['di_tangguhkan'])) // Di Tangguhkan
                ->setCellValue('R' . $row, formatRupiahPdfExcel($result['pungutan']['PPH']['tidak_dipungut'])) // Tidak Dipungut
                // BM
                ->setCellValue('S' . $row, formatRupiahPdfExcel($result['pungutan']['BM']['di_bayar'])) // Di Bayar
                ->setCellValue('T' . $row, formatRupiahPdfExcel($result['pungutan']['BM']['di_bebaskan'])) // Di Bebaskan
                ->setCellValue('U' . $row, formatRupiahPdfExcel($result['pungutan']['BM']['di_tanggung_pemerintah'])) // Di Tanggung Pemerintah
                ->setCellValue('V' . $row, formatRupiahPdfExcel($result['pungutan']['BM']['di_lunasi'])) // Di Lunasi
                ->setCellValue('W' . $row, formatRupiahPdfExcel($result['pungutan']['BM']['di_tunda'])) // Di Tunda
                ->setCellValue('X' . $row, formatRupiahPdfExcel($result['pungutan']['BM']['di_tangguhkan'])) // Di Tangguhkan
                ->setCellValue('Y' . $row, formatRupiahPdfExcel($result['pungutan']['BM']['tidak_dipungut'])); // Tidak Dipungut

            $row++;
        }

        // Set auto column width
        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Export the file
        $filename = "Laporan_BC_27_" . date('Y-m-d-His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }


    public function exportPDFLaporanBCDuaTujuh()
    {
        $payload = [
            "pageSize"      => 100000,
            "currentPage"   => 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 2.7"
        ];

        $condition = [
            "bc_27.company_tujuan_id"  => $this->this_company_id,
            "bc_27.deletedAt" => null,
            "mutasi_global.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC27" => $this->request->getGet("mulaiTanggalBC27"),
            "selesaiTanggalBC27" => $this->request->getGet('selesaiTanggalBC27'),
            "noAju" => $this->request->getGet('noAju'),
            "noBC27" => $this->request->getGet('noBC27'),
        ];

        // Retrieve all data based on the conditions without pagination
        $dataBC27 = $this->bc27Model->getList($condition, $addCondition, 10000000, 1);

        $no = 1; // Resetting no for Excel export
        $dataBC27Result = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataBC27['data'] as $data) {
            // Decode payload JSON
            $payloadData = json_decode($data->payload, true);

            // Tetap buat entry, meskipun payloadData kosong
            $dataBC27Result[$data->id] = [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_aju" => $data->no_aju . " / " . $data->no_daftar,
                "tanggal_bc_27" => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "no_daftar" => $data->no_daftar,
                "pungutan" => [
                    'PPN' => [
                        'di_bebaskan' => 0,
                        'tidak_dipungunt' => 0,
                        'di_tunda' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0,
                        'di_tangguhkan' => 0
                    ],
                    'PPH' => [
                        'di_bebaskan' => 0,
                        'tidak_dipungunt' => 0,
                        'di_tunda' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0,
                        'di_tangguhkan' => 0
                    ],
                    'BM' => [
                        'di_bebaskan' => 0,
                        'tidak_dipungunt' => 0,
                        'di_tunda' => 0,
                        'di_bayar' => 0,
                        'di_tanggung_pemerintah' => 0,
                        'di_lunasi' => 0,
                        'di_tangguhkan' => 0
                    ],
                ]
            ];

            // Lanjutkan hanya jika payloadData tidak kosong dan mengandung pungutan
            if (!empty($payloadData) && isset($payloadData['pungutan'])) {
                foreach ($payloadData['pungutan'] as $pungutan) {
                    // Process each pungutan item individually
                    if (is_array($pungutan) && isset($pungutan['kodeJenisPungutan'])) {
                        $jenisPungutan = '';

                        switch ($pungutan['kodeJenisPungutan']) {
                            case 'PPN':
                                $jenisPungutan = 'PPN';
                                break;
                            case 'PPH':
                                $jenisPungutan = 'PPH';
                                break;
                            case 'BM':
                                $jenisPungutan = 'BM';
                                break;
                        }

                        if (!empty($jenisPungutan)) {
                            // Gunakan ternary untuk mengecek apakah 'nilaiPungutan' ada
                            $nilaiPungutan = isset($pungutan['nilaiPungutan']) ? (int)$pungutan['nilaiPungutan'] : 0;

                            switch ($pungutan['kodeFasilitasTarif']) {
                                case '1':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_bayar'] += $nilaiPungutan;
                                    break;
                                case '5':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_bebaskan'] += $nilaiPungutan;
                                    break;
                                case '2':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tanggung_pemerintah'] += $nilaiPungutan;
                                    break;
                                case '7':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_lunasi'] += $nilaiPungutan;
                                    break;
                                case '6':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tunda'] += $nilaiPungutan;
                                    break;
                                case '9':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['tidak_dipungut'] += $nilaiPungutan;
                                    break;
                                case '3':
                                    $dataBC27Result[$data->id]['pungutan'][$jenisPungutan]['di_tangguhkan'] += $nilaiPungutan;
                                    break;
                            }
                        }
                    }
                }
            }
        }
        // Inisialisasi Dompdf
        $domPdf = new Dompdf();

        // Label untuk laporan
        $label = "Laporan Bea Cukai BC 2.7 : " . $this->request->getVar('dateStart') . " / " . $this->request->getVar('dateEnd');

        // Load view untuk PDF
        $domPdf->loadHtml(view('Laporan/LaporanBeaCukai/2.7/print', [
            'data' => $dataBC27Result,
            'condition' => $addCondition,
            'company' => $this->companiesModel->find($_SESSION['login']->this_company_id),
            'label' => $label
        ]));

        // Set kertas untuk PDF
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream($label, ["Attachment" => false]);
    }



    public function laporanEmpatSatu()
    {
        return view('Laporan/LaporanBeaCukai/4.1/index');
    }

    public function allBCEmpatSatu()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 4.1"
        ];

        $condition = [
            "bc_41.company_id"  => $this->this_company_id,
            "bc_41.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC41" => $this->request->getGet("mulaiTanggalBC41"),
            "selesaiTanggalBC41" => $this->request->getGet('selesaiTanggalBC41'),
            "noAju" => $this->request->getGet('noAju'),
            'asalPengeluaran' => $this->request->getGet('asalPengeluaran'),
        ];


        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $beaCukaiData = $this->bc41Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $payloadData = json_decode($data->payload, true);

            array_push($dataBeaCukai, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "no_aju"    => $data->no_aju,
                "no_daftar"    => $data->no_daftar,
                "date"    => $data->createdAt,
                "tanggal_bayar"    => $payloadData["tanggalBuktiBayar"],
                "no_bayar"    => $payloadData["nomorBuktiBayar"],
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            "payload"           => $payload
        ];
        return response()->setJSON($data);
    }

    public function exportPDFLaporanBCEmpatSatu()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 4.1"
        ];

        $condition = [
            // "bc_41.company_id"  => $this->this_company_id,
            "bc_41.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC41" => $this->request->getGet("mulaiTanggalBC41"),
            "selesaiTanggalBC41" => $this->request->getGet('selesaiTanggalBC41'),
            "noAju" => $this->request->getGet('noAju'),
            'asalPengeluaran' => $this->request->getGet('asalPengeluaran'),
        ];


        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $beaCukaiData = $this->bc41Model->getList($condition, $addCondition, 1000000, 0);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $payloadData = json_decode($data->payload, true);

            array_push($dataBeaCukai, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "no_aju"    => $data->no_aju,
                "no_daftar"    => $data->no_daftar,
                "date"    => $data->createdAt,
                "tanggal_bayar"    => $payloadData["tanggalBuktiBayar"],
                "no_bayar"    => $payloadData["nomorBuktiBayar"],
            ]);
        }

        $domPdf = new Dompdf();

        $fileName = 'Laporan Pemasukan Barang';
        $domPdf->loadHtml(view('Laporan/LaporanBeaCukai/4.1/print', [
            'dataBeaCukai' => $dataBeaCukai,
            'condition' => $addCondition
        ]));
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream($fileName, array("Attachment" => false));
    }

    public function exportExcelLaporanBCEmpatSatu()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 4.1"
        ];

        $condition = [
            "bc_41.company_id"  => $this->this_company_id,
            "bc_41.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC41" => $this->request->getGet("mulaiTanggalBC41"),
            "selesaiTanggalBC41" => $this->request->getGet('selesaiTanggalBC41'),
            "noAju" => $this->request->getGet('noAju'),
            'asalPengeluaran' => $this->request->getGet('asalPengeluaran'),
        ];

        $beaCukaiData = $this->bc41Model->getList($condition, $addCondition, 1000000, 0);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $payloadData = json_decode($data->payload, true);

            array_push($dataBeaCukai, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "no_aju"    => $data->no_aju,
                "no_daftar"    => $data->no_daftar,
                "date"    => $data->createdAt,
                "tanggal_bayar"    => $payloadData["tanggalBuktiBayar"],
                "no_bayar"    => $payloadData["nomorBuktiBayar"],
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $dataStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $column = 2;

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Tanggal')
            ->setCellValue('C1', 'No Aju')
            ->setCellValue('D1', 'No Daftar')
            ->setCellValue('E1', 'No Bukti Bayar')
            ->setCellValue('F1', 'Tanggal Bukti Bayar');

        $sheet->getStyle('A1:F1')->applyFromArray($headerStyleArray);

        // Fill data
        $column = 2; // Start from the second row

        foreach ($dataBeaCukai as $row) {

            $sheet->setCellValue('A' . $column, $row['no'])
                ->setCellValue('B' . $column, $row['date'])
                ->setCellValue('C' . $column, $row['no_aju'])
                ->setCellValue('D' . $column, $row['no_daftar'])
                ->setCellValue('E' . $column, $row['no_bayar'])
                ->setCellValue('F' . $column, $row['tanggal_bayar']);

            $sheet->getStyle('A' . $column . ':F' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }

        $sheet->getStyle('A' . $column . ':F' . $column)->applyFromArray($headerStyleArray);

        $writer = new Xlsx($spreadsheet);
        foreach (range('A', 'Z') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Pungutan_BC_41';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }

    public function allWipProduksiDashboard()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = [
            "production_results.company_id"  => $this->this_company_id,
        ];
        $addCondition = [
            "month"     => $this->request->getGet('month'),
            "search"    => $this->request->getGet("kodeProduksi"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "dateStart"    =>  "",
            "dateEnd"    =>   "",
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $productionResultData = $this->productionResultModel->getProductResultList($condition, $addCondition, $limit, $offset);

        $dataProductionResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($productionResultData['data'] as &$data) {
            $qtyHasilProduksi = 0;
            $productionResDetailData = $this->productionResultDetailModel->where('production_result_id', $data->id)->where('type', 'JADI')->findAll();
            foreach ($productionResDetailData as $value) {
                $qtyHasilProduksi += (float) $value['qty_isi'];
            }
            $data->qtyHasilProduksi = $qtyHasilProduksi;
            array_push($dataProductionResult, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "productionDate" => date('d/m/Y', strtotime($data->receive_date)),
                "productionCode" => $data->wo_no,
                "barangCode"    => $data->barangCode,
                "barangName"    => $data->barangName,
                "hasilProduksi"  => number_format($data->qtyHasilProduksi, 2),
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $productionResultData['totalData'],
            "recordsFiltered"   => $productionResultData['totalFilteredData'],
            "data"              => $dataProductionResult,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function allWipProduksiDashboardExcel()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
        ];

        $condition = [
            "production_results.company_id"  => $this->this_company_id,
        ];
        $addCondition = [
            "month"     => $this->request->getGet('month'),
            "search"    => $this->request->getGet('kodeProduksi') == 'null' ? '' : $this->request->getGet("kodeProduksi"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "dateStart" =>  "",
            "dateEnd"   =>   "",
        ];
        $productionResultData = $this->productionResultModel->getProductResultList($condition, $addCondition, 10000000, 0);

        $dataProductionResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($productionResultData['data'] as &$data) {
            $qtyHasilProduksi = 0;
            $productionResDetailData = $this->productionResultDetailModel->where('production_result_id', $data->id)->where('type', 'JADI')->findAll();
            foreach ($productionResDetailData as $value) {
                $qtyHasilProduksi += (float) $value['qty_isi'];
            }
            $data->qtyHasilProduksi = $qtyHasilProduksi;
            array_push($dataProductionResult, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "productionDate" => date('d/m/Y', strtotime($data->receive_date)),
                "productionCode" => $data->wo_no,
                "barangCode"    => $data->barangCode,
                "barangName"    => $data->barangName,
                "hasilProduksi"  => number_format($data->qtyHasilProduksi, 2),
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $dataStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $column = 2;

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B1', 'Tanggal Produksi')
            ->setCellValue('C1', 'Kode Produksi')
            ->setCellValue('D1', 'Kode Barang')
            ->setCellValue('E1', 'Nama Barang')
            ->setCellValue('F1', 'Hasil Produksi');

        $sheet->getStyle('A1:F1')->applyFromArray($headerStyleArray);

        $column = 2;
        foreach ($dataProductionResult as $row) {

            $sheet->setCellValue('A' . $column, $row['no'])
                ->setCellValue('B' . $column, $row['productionDate'])
                ->setCellValue('C' . $column, $row['productionCode'])
                ->setCellValue('D' . $column, $row['barangCode'])
                ->setCellValue('E' . $column, $row['barangName'])
                ->setCellValue('F' . $column, $row['hasilProduksi']);

            $sheet->getStyle('A' . $column . ':F' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        foreach (range('A', 'Z') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Hasil_Produksi';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }

    public function detailMutasi($id)
    {
        $id = decrypt($id);
        $dateStart = formatDMYtoYMD($this->request->getVar('dateStart'));
        $dateEnd   = formatDMYtoYMD($this->request->getVar('dateEnd'));

        $barangMaster = $this->barangMasterModel
            ->select('barang_master.*, parent_barang.parent_name')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where('barang_master.id', $id)
            ->first();

        // ===============================
        // Helper aman ambil qty
        // ===============================
        $qty = fn($arr, $id) => $arr[$id]['qty_diterima'] ?? 0;

        $awal = $masuk = $keluar = 0;
        $kodeSatuan = '';

        // ===============================
        // PRELOAD DATA STOK
        // ===============================
        $stok = [
            'awal' => [
                'masuk'    => $this->getTotalPemasukkan("2025-09-01", $dateStart),
                'masuk_p'  => $this->getTotalPemasukkanProduksi("2025-09-01", $dateStart),
                'keluar'   => $this->getTotalPengeluaranPerDokumen("2025-09-01", $dateStart),
                'keluar_p' => $this->getTotalPengeluaranProduksi("2025-09-01", $dateStart),
            ],
            'jalan' => [
                'masuk'    => $this->getTotalPemasukkan($dateStart, $dateEnd),
                'masuk_p'  => $this->getTotalPemasukkanProduksi($dateStart, $dateEnd),
                'keluar'   => $this->getTotalPengeluaranPerDokumen($dateStart, $dateEnd),
                'keluar_p' => $this->getTotalPengeluaranProduksi($dateStart, $dateEnd),
            ]
        ];

        // ===============================
        // HITUNG SALDO AWAL
        // ===============================
        $awal += $qty($stok['awal']['masuk'], $id);
        $awal += $qty($stok['awal']['masuk_p'], $id);
        $awal -= $qty($stok['awal']['keluar'], $id);
        $awal -= $qty($stok['awal']['keluar_p'], $id);

        // kode satuan (ambil dari salah satu sumber)
        $kodeSatuan = $stok['awal']['masuk'][$id]['kode_satuan'] ?? '';

        // ===============================
        // HITUNG STOK BERJALAN
        // ===============================
        $masuk += $qty($stok['jalan']['masuk'], $id);
        $masuk += $qty($stok['jalan']['masuk_p'], $id);

        $keluar += $qty($stok['jalan']['keluar'], $id);
        $keluar += $qty($stok['jalan']['keluar_p'], $id);

        $akhir = $awal + $masuk - $keluar;

        return view('Laporan/LaporanBeaCukai/mutasi/detail', [
            'barangMaster' => $barangMaster,
            'mutasi' => [
                'awal'        => $awal,
                'masuk'       => $masuk,
                'keluar'      => $keluar,
                'akhir'       => $akhir,
                'kode_satuan' => $kodeSatuan,
            ]
        ]);
    }

    public function allMutasi()
    {
        $limit  = (int) $this->request->getGet("length");
        $offset = (int) $this->request->getGet("start");

        $payload = [
            "pageSize"    => $limit,
            "currentPage" => ($offset / $limit) + 1,
            "search"      => $this->request->getGet("search"),
            "sort"        => $this->request->getGet("sort"),
            "sortType"    => $this->request->getGet("sortType"),
        ];

        $condition = [
            "barang_master.company_id" => $this->this_company_id,
            "barang_master.deletedAt"  => null,
        ];

        $addCondition = [
            "sort"       => $this->request->getGet("sort"),
            "sortType"   => $this->request->getGet("sortType"),
            "company_id" => $this->this_company_id,
            "search"     => trim($this->request->getVar('search'))
        ];

        $dateStart = $this->request->getVar("dateStart")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
            : null;

        $dateEnd = $this->request->getVar("dateEnd")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
            : null;

        $tipeBarang = explode(',', $this->request->getVar('type_barang'));

        /**
         * ==================================================
         * FETCH SEMUA BARANG (TANPA PAGINATION DB)
         * ==================================================
         */
        $stockMutasi = $this->stockRevampModel->allStockFisik(
            $condition,
            $addCondition,
            100000000,
            0,
            $tipeBarang
        );

        /**
         * ==================================================
         * PRELOAD DATA STOK
         * ==================================================
         */
        // Saldo awal masuk
        $dataStokMasukAwal = $this->getTotalPemasukkan("2025-09-01", $dateStart);
        $dataStokProduksiAwal = $this->getTotalPemasukkanProduksi("2025-09-01", $dateStart);

        // Saldo awal keluar
        $dataStokKeluarAwal = $this->getTotalPengeluaranPerDokumen("2025-09-01", $dateStart);
        $dataStokKeluarProduksiAwal = $this->getTotalPengeluaranProduksi("2025-09-01", $dateStart);

        // Stok berjalan masuk
        $dataStokMasuk = $this->getTotalPemasukkan($dateStart, $dateEnd);
        $dataStokProduksiMasuk = $this->getTotalPemasukkanProduksi($dateStart, $dateEnd);

        // Stok berjalan keluar
        $dataStokKeluar = $this->getTotalPengeluaranPerDokumen($dateStart, $dateEnd);
        $dataStokProduksiKeluar = $this->getTotalPengeluaranProduksi($dateStart, $dateEnd);

        /**
         * ==================================================
         * HITUNG MUTASI (TANPA PAGINATION)
         * ==================================================
         */
        $dataMutasi = [];

        foreach ($stockMutasi['data'] as $data) {

            $awal = $masuk = $keluar = 0;
            $kodeSatuan = "";

            // === SALDO AWAL ===
            if (isset($dataStokMasukAwal[$data['id']])) {
                $awal += $dataStokMasukAwal[$data['id']]['qty_diterima'];
                $kodeSatuan = $dataStokMasukAwal[$data['id']]['kode_satuan'];
            }
            if (isset($dataStokProduksiAwal[$data['id']])) {
                $awal += $dataStokProduksiAwal[$data['id']]['qty_diterima'];
            }
            if (isset($dataStokKeluarAwal[$data['id']])) {
                $awal -= $dataStokKeluarAwal[$data['id']]['qty_diterima'];
            }
            if (isset($dataStokKeluarProduksiAwal[$data['id']])) {
                $awal -= $dataStokKeluarProduksiAwal[$data['id']]['qty_diterima'];
            }

            // === STOK MASUK ===
            if (isset($dataStokMasuk[$data['id']])) {
                $masuk += $dataStokMasuk[$data['id']]['qty_diterima'];
            }
            if (isset($dataStokProduksiMasuk[$data['id']])) {
                $masuk += $dataStokProduksiMasuk[$data['id']]['qty_diterima'];
            }

            // === STOK KELUAR ===
            if (isset($dataStokKeluar[$data['id']])) {
                $keluar += $dataStokKeluar[$data['id']]['qty_diterima'];
            }
            if (isset($dataStokProduksiKeluar[$data['id']])) {
                $keluar += $dataStokProduksiKeluar[$data['id']]['qty_diterima'];
            }

            $akhir = $awal + $masuk - $keluar;

            // simpan dulu, filter nanti
            $dataMutasi[] = [
                "id"          => encrypt($data['id']),
                "parent_name" => $data['parent_name'],
                "kode_barang" => $data['kode_barang'],
                "barang_name" => $data['barang_name'],
                "kode_satuan" => $kodeSatuan,
                "awal"        => $awal,
                "masuk"       => $masuk,
                "keluar"      => $keluar,
                "akhir"       => $akhir,
            ];
        }

        /**
         * ==================================================
         * FILTER: BUANG YANG QTY = 0 SEMUA
         * ==================================================
         */
        $filteredMutasi = array_values(array_filter($dataMutasi, function ($row) {
            return !(
                $row['awal'] == 0 &&
                $row['masuk'] == 0 &&
                $row['keluar'] == 0 &&
                $row['akhir'] == 0
            );
        }));

        $totalFiltered = count($filteredMutasi);

        /**
         * ==================================================
         * RE PAGINATION (array_slice)
         * ==================================================
         */
        $paginatedData = array_slice($filteredMutasi, $offset, $limit);

        // reset nomor urut
        $no = $offset + 1;
        foreach ($paginatedData as &$row) {
            $row['no'] = $no++;
        }
        unset($row);

        /**
         * ==================================================
         * RESPONSE DATATABLES
         * ==================================================
         */
        return response()->setJSON([
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $totalFiltered,
            "recordsFiltered" => $totalFiltered,
            "data"            => $paginatedData,
            "payload"         => $payload
        ]);
    }

    public function exportExcelMutasi()
    {
        $condition = [
            "barang_master.company_id" => $this->this_company_id,
            "barang_master.deletedAt"  => null,
        ];

        $addCondition = [
            "sort"       => "barang_master.barang_name",
            "sortType"   => "asc",
            "company_id" => $this->this_company_id,
            "search"     => trim($this->request->getVar('search'))
        ];

        $dateStart = $this->request->getVar("dateStart")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
            : null;

        $dateEnd = $this->request->getVar("dateEnd")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
            : null;

        $tipeBarang = explode(',', $this->request->getVar('type_barang'));

        /**
         * ==================================================
         * FETCH & HITUNG MUTASI (SAMA PERSIS SEPERTI KAMU)
         * ==================================================
         */
        $stockMutasi = $this->stockRevampModel->allStockFisik(
            $condition,
            $addCondition,
            100000000,
            0,
            $tipeBarang
        );

        // preload stok
        $dataStokMasukAwal = $this->getTotalPemasukkan("2025-09-01", $dateStart);
        $dataStokProduksiAwal = $this->getTotalPemasukkanProduksi("2025-09-01", $dateStart);
        $dataStokKeluarAwal = $this->getTotalPengeluaranPerDokumen("2025-09-01", $dateStart);
        $dataStokKeluarProduksiAwal = $this->getTotalPengeluaranProduksi("2025-09-01", $dateStart);

        $dataStokMasuk = $this->getTotalPemasukkan($dateStart, $dateEnd);
        $dataStokProduksiMasuk = $this->getTotalPemasukkanProduksi($dateStart, $dateEnd);
        $dataStokKeluar = $this->getTotalPengeluaranPerDokumen($dateStart, $dateEnd);
        $dataStokProduksiKeluar = $this->getTotalPengeluaranProduksi($dateStart, $dateEnd);

        $dataMutasi = [];

        foreach ($stockMutasi['data'] as $data) {
            $awal = $masuk = $keluar = 0;
            $kodeSatuan = "";

            if (isset($dataStokMasukAwal[$data['id']])) {
                $awal += $dataStokMasukAwal[$data['id']]['qty_diterima'];
                $kodeSatuan = $dataStokMasukAwal[$data['id']]['kode_satuan'];
            }
            if (isset($dataStokProduksiAwal[$data['id']])) {
                $awal += $dataStokProduksiAwal[$data['id']]['qty_diterima'];
            }
            if (isset($dataStokKeluarAwal[$data['id']])) {
                $awal -= $dataStokKeluarAwal[$data['id']]['qty_diterima'];
            }
            if (isset($dataStokKeluarProduksiAwal[$data['id']])) {
                $awal -= $dataStokKeluarProduksiAwal[$data['id']]['qty_diterima'];
            }

            if (isset($dataStokMasuk[$data['id']])) {
                $masuk += $dataStokMasuk[$data['id']]['qty_diterima'];
            }
            if (isset($dataStokProduksiMasuk[$data['id']])) {
                $masuk += $dataStokProduksiMasuk[$data['id']]['qty_diterima'];
            }

            if (isset($dataStokKeluar[$data['id']])) {
                $keluar += $dataStokKeluar[$data['id']]['qty_diterima'];
            }
            if (isset($dataStokProduksiKeluar[$data['id']])) {
                $keluar += $dataStokProduksiKeluar[$data['id']]['qty_diterima'];
            }

            $akhir = $awal + $masuk - $keluar;

            // skip kosong
            if ($awal == 0 && $masuk == 0 && $keluar == 0 && $akhir == 0) {
                continue;
            }

            $dataMutasi[] = [
                $data['parent_name'],
                $data['kode_barang'],
                $data['barang_name'],
                $kodeSatuan,
                $awal,
                $masuk,
                $keluar,
                $akhir,
            ];
        }

        /**
         * ==================================================
         * EXCEL GENERATION
         * ==================================================
         */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ===== HEADER =====
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'LAPORAN MUTASI BARANG');

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue(
            'A2',
            'Periode : ' . date('d-m-Y', strtotime($dateStart)) . ' s/d ' . date('d-m-Y', strtotime($dateEnd))
        );

        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue(
            'A3',
            'Tipe Barang : ' . implode(', ', $tipeBarang)
        );

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ===== TABLE HEADER =====
        $headerRow = 5;
        $headers = ['No', 'Parent', 'Kode Barang', 'Nama Barang', 'Satuan', 'Awal', 'Masuk', 'Keluar', 'Akhir'];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $headerRow, $header);
            $col++;
        }

        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:I{$headerRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // ===== TABLE BODY =====
        $row = $headerRow + 1;
        $no = 1;
        foreach ($dataMutasi as $item) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->fromArray($item, null, "B{$row}");
            $row++;
        }

        $sheet->getStyle("A{$headerRow}:I" . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // autosize
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        /**
         * ==================================================
         * OUTPUT
         * ==================================================
         */
        $filename = 'Laporan_Mutasi_Barang_' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    private function getTotalPemasukkan(
        $dateStart,
        $dateEnd
    ) {
        $dataPemasukkanMap = array();

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStartLpb' => $dateStart,
            'dateEndLpb' => $dateEnd,
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

    private function getTotalPemasukkanProduksi(
        $dateStart,
        $dateEnd
    ) {
        $dataPemasukkanMap = array();

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
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

    private function getTotalPengeluaranProduksi(
        $dateStart,
        $dateEnd
    ) {
        $dataPengeluaranMap = array();

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
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

    private function getTotalPengeluaranPerDokumen(
        $dateStart,
        $dateEnd
    ) {
        $dataPengeluaranMap = array();

        $condition = [
            'company_id'   => $this->this_company_id,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
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

    public function laporanMutasiBahanBakuPenolong()
    {
        return view('Laporan/LaporanBeaCukai/mutasi/bahanBakuPenolong');
    }

    public function laporanMutasiBarangJadi()
    {
        return view('Laporan/LaporanBeaCukai/mutasi/barangJadi');
    }

    public function laporanMutasiBarangScrap()
    {
        return view('Laporan/LaporanBeaCukai/mutasi/barangScrap');
    }

    public function laporanMutasiBarangModal()
    {

        return view('Laporan/LaporanBeaCukai/mutasi/barangModal');
    }
}
