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
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PPBKBModel;
use App\Models\RMImportPOModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\SupplierModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    }

    public function index()
    {

        return view('Laporan/LaporanBeaCukai/index/index');
    }

    public function laporanPemasukanBarang()
    {
        $data = [
            'tipeBarang' => $this->metadataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataSupplier' => $this->supplierModel->where('company_id', $this->this_company_id)->findAll(),
            'dataDokumen' => $this->metadataModel->where('name', 'jenis_dok_aju')->whereIn('value', ['BC 2.3', 'BC 2.7', 'BC 4.0', 'PPB-KB'])->findAll(),
            'dataPemasukan' => ["LPB", "JASA VENDOR", "MUTASI", "REBUS"],
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('Laporan/LaporanBeaCukai/pemasukanBarang/index', $data);
    }

    public function exportPDFLaporanMasukBarang()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "tipe_barang"   => $this->request->getVar('tipe_barang') != 'null' ? $this->request->getVar('tipe_barang') : "",
            "date_start"    => $this->request->getVar("date_start") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_start")))) : "",
            "date_end"      => $this->request->getVar("date_end") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_end")))) : "",
            "supplier_id"   => $this->request->getVar("supplier_id") != 'null' ? $this->request->getVar("supplier_id") : "",
            "bc_id"         => $this->request->getVar("bc_id") != 'null' ? $this->request->getVar("bc_id") : "",
            "sumber"        => $this->request->getVar("sumber") != 'null' ? [$this->request->getVar("sumber")] : ["LPB", "JASA VENDOR", "MUTASI", "REBUS"],
            "divisi_id"     => $this->request->getVar("divisi_id") != 'null' ? $this->request->getVar("divisi_id") : "",
            "warehouse_id"  => $this->request->getVar("warehouse_id") != 'null' ? $this->request->getVar("warehouse_id") : "",
            "nama_barang"   => $this->request->getVar("nama_barang") != 'null' ? $this->request->getVar("nama_barang") : "",
            "no_aju"        => $this->request->getVar("no_aju") != 'null' ? $this->request->getVar("no_aju") : "",
            "no_daftar"     => $this->request->getVar("no_daftar") != 'null' ? $this->request->getVar("no_daftar") : "",
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType")
        ];

        $condition = [
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
            "stock.deletedAt" => null,
            "stock_details.status" => "In"
        ];

        $dataBarang = $this->stockDetail2Model->getListStokMasukKeluar($condition, $addCondition, 10000000, 0);

        $jsonData = $this->getListMasukKeluarBarang($dataBarang, $payload, $addCondition);
        $domPdf = new Dompdf();

        $fileName = 'Laporan Pemasukan Barang';
        $domPdf->loadHtml(view('Laporan/LaporanBeaCukai/pemasukanBarang/print', [
            'jsonData' => $jsonData,
            'condition' => $addCondition
        ]));
        $domPdf->setPaper('legal', 'landscape');
        $domPdf->render();
        $domPdf->stream($fileName, array("Attachment" => false));
    }

    public function exportExcelLaporanMasukBarang()
    {
        $payload = [
            "pageSize" => 10000000,
            "currentPage" => 1,
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "tipe_barang"   => $this->request->getVar('tipe_barang') != 'null' ? $this->request->getVar('tipe_barang') : "",
            "date_start"    => $this->request->getVar("date_start") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_start")))) : "",
            "date_end"      => $this->request->getVar("date_end") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_end")))) : "",
            "supplier_id"   => $this->request->getVar("supplier_id") != 'null' ? $this->request->getVar("supplier_id") : "",
            "bc_id"         => $this->request->getVar("bc_id") != 'null' ? $this->request->getVar("bc_id") : "",
            "sumber"        => $this->request->getVar("sumber") != 'null' ? [$this->request->getVar("sumber")] : ["LPB", "JASA VENDOR", "MUTASI", "REBUS"],
            "divisi_id"     => $this->request->getVar("divisi_id") != 'null' ? $this->request->getVar("divisi_id") : "",
            "warehouse_id"  => $this->request->getVar("warehouse_id") != 'null' ? $this->request->getVar("warehouse_id") : "",
            "nama_barang"   => $this->request->getVar("nama_barang") != 'null' ? $this->request->getVar("nama_barang") : "",
            "no_aju"        => $this->request->getVar("no_aju") != 'null' ? $this->request->getVar("no_aju") : "",
            "no_daftar"     => $this->request->getVar("no_daftar") != 'null' ? $this->request->getVar("no_daftar") : "",
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType")
        ];

        $condition = [
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
            "stock.deletedAt" => null,
            "stock_details.status" => "In"
        ];

        $dataBarang = $this->stockDetail2Model->getListStokMasukKeluar($condition, $addCondition, 10000000, 0);

        $jsonData = $this->getListMasukKeluarBarang($dataBarang, $payload, $addCondition);

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
            ->setCellValue('B1', 'Tipe Barang')
            ->setCellValue('C1', 'Jenis Dokumen')
            ->setCellValue('D1', 'Nomor Aju')
            ->setCellValue('E1', 'No Daftar')
            ->setCellValue('F1', 'Tgl Daftar')
            ->setCellValue('G1', 'No Penerimaan')
            ->setCellValue('H1', 'Tgl Penerimaan')
            ->setCellValue('I1', 'Surat Jalan')
            ->setCellValue('J1', 'Jenis Order')
            ->setCellValue('K1', 'No Order')
            ->setCellValue('L1', 'No Invoice')
            ->setCellValue('M1', 'Departemen')
            ->setCellValue('N1', 'Warehouse')
            ->setCellValue('O1', 'Supplier')
            ->setCellValue('P1', 'Kode Barang')
            ->setCellValue('Q1', 'Barang')
            ->setCellValue('R1', 'Spesifikasi')
            ->setCellValue('S1', 'Jumlah Barang')
            ->setCellValue('T1', 'Satuan')
            ->setCellValue('U1', 'Valas')
            ->setCellValue('V1', 'Harga Barang / Jasa')
            ->setCellValue('W1', 'Nilai Penyerahan')
            ->setCellValue('X1', 'Jumlah Penerimaan')
            ->setCellValue('Y1', 'Selisih')
            ->setCellValue('Z1', 'Keterangan');


        $sheet->getStyle('A1:Z1')->applyFromArray($headerStyleArray);

        // Fill data
        $column = 2; // Start from the second row
        foreach ($jsonData['data'] as $row) {
            $sheet->setCellValue('A' . $column, $row['no'])
                ->setCellValue('B' . $column, $row['tipeBarang'])
                ->setCellValue('C' . $column, $row['jenisDokumen'])
                ->setCellValue('D' . $column, $row['noAju'])
                ->setCellValue('E' . $column, $row['noDaftar'])
                ->setCellValue('F' . $column, $row['tglDaftar'])
                ->setCellValue('G' . $column, $row['noPenerimaan'])
                ->setCellValue('H' . $column, $row['tglPenerimaan'])
                ->setCellValue('I' . $column, $row['suratJalan'])
                ->setCellValue('J' . $column, $row['jenisSumber'])
                ->setCellValue('K' . $column, $row['noOrder'])
                ->setCellValue('L' . $column, $row['noInvoice'])
                ->setCellValue('M' . $column, $row['divisi'])
                ->setCellValue('N' . $column, $row['warehouse'])
                ->setCellValue('O' . $column, $row['supplier'])
                ->setCellValue('P' . $column, $row['kodeBarang'])
                ->setCellValue('Q' . $column, $row['barang'])
                ->setCellValue('R' . $column, $row['spesifikasi'])
                ->setCellValue('S' . $column, $row['jumlahBarang'])
                ->setCellValue('T' . $column, $row['satuanName'])
                ->setCellValue('U' . $column, $row['valas'])
                ->setCellValue('V' . $column, $row['hargaBarang'])
                ->setCellValue('W' . $column, $row['nilaiPenyerahan'])
                ->setCellValue('X' . $column, $row['jumlahPenerimaan'])
                ->setCellValue('Y' . $column, $row['selisih'])
                ->setCellValue('Z' . $column, $row['keterangan']);

            $sheet->getStyle('A' . $column . ':Z' . $column)->applyFromArray($dataStyleArray);
            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        foreach (range('A', 'Z') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Penerimaan_Barang';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }

    public function allMasukKeluarBarang()
    {
        $payload = [
            "pageSize" => $this->request->getVar("length"),
            "currentPage" => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sortType" => $this->request->getVar("sortType"),
            "date_start" => $this->request->getVar("date_start") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("date_start")))) : "",
            "date_end" => $this->request->getVar("date_end") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("date_end")))) : "",
        ];

        $addCondition = [
            "tipe_barang"   => $this->request->getVar('tipe_barang'),
            "date_start"    => $this->request->getVar("date_start") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_start")))) : "",
            "date_end"      => $this->request->getVar("date_end") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("date_end")))) : "",
            "supplier_id"   => $this->request->getVar("supplier_id"),
            "bc_id"         => $this->request->getVar("bc_id"),
            "sumber"        => $this->request->getVar("sumber") ? [$this->request->getVar("sumber")] : ["LPB", "JASA VENDOR", "MUTASI", "REBUS"],
            "divisi_id"     => $this->request->getVar("divisi_id"),
            "warehouse_id"  => $this->request->getVar("warehouse_id"),
            "nama_barang"   => $this->request->getVar("nama_barang"),
            "no_aju"        => $this->request->getVar("no_aju"),
            "no_daftar"     => $this->request->getVar("no_daftar"),
            "status"        => $this->request->getVar("status"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType")
        ];

        $condition = [
            "stock_details2.deletedAt" => null,
            "stock_details.deletedAt" => null,
            "stock.deletedAt" => null,
            "stock_details.status" => $addCondition['status']
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $dataBarang = $this->stockDetail2Model->getListStokMasukKeluar($condition, $addCondition, $limit, $offset);

        $jsonData = $this->getListMasukKeluarBarang($dataBarang, $payload, $addCondition);
        return response()->setJSON($jsonData);
    }


    private function getListMasukKeluarBarang($dataBarang, $payload, $addCondition)
    {
        $dataBarangList = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataBarang['data'] as $data) {
            $jenisDokumen = ($data->bc_id == 0) ? "NON PABEAN" : $this->metadataModel->find($data->bc_id)['value'];
            // GET BC DETAIL
            if ($jenisDokumen == "NON PABEAN") {
                $noDaftar = "-";
            } elseif ($data->bc_id == 48) {
                $bcDetail = $this->bc23Model->select('no_daftar')
                    ->join('bc_purchase_order', 'bc_purchase_order.id = bc_23.bc_purchase_order_id', 'left')
                    ->where('no_aju', $data->no_aju)
                    ->first();
            } elseif ($data->bc_id == 49) {
                $bcDetail = $this->bc25Model->where('no_aju', $data->no_aju)->first();
            } elseif ($data->bc_id == 52) {
                $bcDetail = $this->bc27Model->where('no_aju', $data->no_aju)->first();
            } elseif ($data->bc_id == 53) {
                $bcDetail = $this->bc40Model->select('no_daftar')
                    ->join('bc_purchase_order', 'bc_purchase_order.id = bc_40.bc_purchase_order_id', 'left')
                    ->first();
            } elseif ($data->bc_id == 54) {
                $bcDetail = $this->bc41Model->where('no_aju', $data->no_aju)->first();
            } elseif ($data->bc_id == 1426) {
                $bcDetail = $this->ppbkbModel->where('no_ppbkb', $data->no_aju)->first();
            } else {
                $bcDetail = $this->bc30Model->where('no_aju', $data->no_aju)->first();
            }


            if ($data->sumber === "LPB") {
                $data->sumber = "PEMBELIAN";
                $penerimaanBarang = $this->penerimaanBarangModel->where('no_penerimaan_barang', $data->no_dokumen)->first();
            }

            if ($data->kemasan_id == 0) {
                // INI BARANG
                $barangMaster = $this->barangMasterModel->find($data->barang1_id);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($data->barang2_id);
                $satuan = $this->satuanModel->find($barangMasterSpesifikasi['satuan_1']);

                $kodeBarang = $barangMaster == null ? "-" : $barangMaster['kode_barang'];
                $barangName = $barangMaster == null ? "-" : $barangMaster['barang_name'];
                $spesifikasiName = $barangMasterSpesifikasi == null ? "-" : $barangMasterSpesifikasi['spesifikasi'];
                $satuanName = $satuan == null ? "-" : $satuan['kode_satuan'];
            } else {
                // INI KEMASAN
                $kemasan = $this->kemasanModel->find($data->kemasan_id);
                $satuan = $this->satuanModel->find($kemasan['satuan_id']);

                $kodeBarang = $kemasan == null ? "-" : $kemasan['kode'];
                $barangName = $kemasan['name'];
                $spesifikasiName = "-";
                $satuanName = $satuan == null ? "-" : $satuan['kode_satuan'];
            }

            if ($data->bc_id == 48) {
                // IMPROT BB ATAU BP
                $poImportBB = $this->rmImportPosModel->select('metadata.value AS valas')
                    ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                    ->where('po_no', $data->no_po)
                    ->first();
                $poImportBP = $this->amPurchaseOrderModel->select('metadata.value AS valas')
                    ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                    ->where('po_no', $data->no_po)
                    ->first();

                if ($poImportBB != null) {
                    $valas = $poImportBB['valas'];
                } elseif ($poImportBP != null) {
                    $valas = $poImportBP['valas'];
                } else {
                    $valas = "IDR";
                }
            } else {
                $valas = "IDR";
            }

            if ($data->supplier_id != null) {
                $supplier = $this->supplierModel->find($data->supplier_id);
            }
            $hargaBarang = ($data->harga_umum + $data->harga_harian + $data->harga_bulanan) * $data->qty;

            // FILTERAN
            if ($addCondition['nama_barang'] != "" || $addCondition['no_daftar'] != "") {
                if ($addCondition['nama_barang'] == $barangName || $addCondition['nama_barang'] == $kodeBarang) {
                    array_push($dataBarangList, [
                        "no"            => $no++,
                        "id"            => encrypt($data->id),
                        "tipeBarang"    => strtoupper(str_replace("_", " ", $data->tipe_barang)),
                        "jenisDokumen"  => $jenisDokumen,
                        "noAju"         => $data->no_aju,
                        "noDaftar"      => isset($noDaftar) ? $noDaftar : ($bcDetail == null ? "-" : $bcDetail['no_daftar']),
                        "tglDaftar"     => date('d/m/Y', strtotime($data->stock_date)),
                        "noPenerimaan"  => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_penerimaan_barang'] : '') : '',
                        "tglPenerimaan" => isset($penerimaanBarang) ? ($penerimaanBarang != null ? date('d/m/Y', strtotime($penerimaanBarang['tanggal'])) : '') : '',
                        "suratJalan"    => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_surat_jalan'] : '') : '',
                        "jenisSumber"   => $data->sumber,
                        "noOrder"       => $data->no_po,
                        "noInvoice"     => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_invoice'] : '') : '',
                        "divisi"        => $data->divisiName,
                        "warehouse"     => $data->warehouseName,
                        "supplier"      => isset($supplier) ? ($supplier != null ? $supplier['name'] : "-") : "-",
                        "kodeBarang"    => $kodeBarang,
                        "barang"        => $barangName,
                        "spesifikasi"   => $spesifikasiName,
                        "jumlahBarang"  => number_format($data->qty),
                        "satuanName"    => $satuanName,
                        "valas"         => $valas,
                        "hargaBarang"   => number_format($hargaBarang, 2),
                        "nilaiPenyerahan"  => number_format($hargaBarang, 2),
                        "jumlahPenerimaan"  => number_format($data->qty),
                        "selisih"       => 0,
                        "keterangan"    => $data->keterangan
                    ]);
                } elseif ($data->bc_id != 0 && $bcDetail != null) {
                    if ($bcDetail['no_daftar'] == $addCondition['no_daftar'] && $bcDetail['no_daftar'] != "") {
                        array_push($dataBarangList, [
                            "no"            => $no++,
                            "id"            => encrypt($data->id),
                            "tipeBarang"    => strtoupper(str_replace("_", " ", $data->tipe_barang)),
                            "jenisDokumen"  => $jenisDokumen,
                            "noAju"         => $data->no_aju,
                            "noDaftar"      => isset($noDaftar) ? $noDaftar : ($bcDetail == null ? "-" : $bcDetail['no_daftar']),
                            "tglDaftar"     => date('d/m/Y', strtotime($data->stock_date)),
                            "noPenerimaan"  => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_penerimaan_barang'] : '') : '',
                            "tglPenerimaan" => isset($penerimaanBarang) ? ($penerimaanBarang != null ? date('d/m/Y', strtotime($penerimaanBarang['tanggal'])) : '') : '',
                            "suratJalan"    => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_surat_jalan'] : '') : '',
                            "jenisSumber"   => $data->sumber,
                            "noOrder"       => $data->no_po,
                            "noInvoice"     => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_invoice'] : '') : '',
                            "divisi"        => $data->divisiName,
                            "warehouse"     => $data->warehouseName,
                            "supplier"      => isset($supplier) ? ($supplier != null ? $supplier['name'] : "-") : "-",
                            "kodeBarang"    => $kodeBarang,
                            "barang"        => $barangName,
                            "spesifikasi"   => $spesifikasiName,
                            "jumlahBarang"  => number_format($data->qty),
                            "satuanName"    => $satuanName,
                            "valas"         => $valas,
                            "hargaBarang"   => number_format($hargaBarang, 2),
                            "nilaiPenyerahan"  => number_format($hargaBarang, 2),
                            "jumlahPenerimaan"  => number_format($data->qty),
                            "selisih"       => 0,
                            "keterangan"    => $data->keterangan
                        ]);
                    }
                }
            } else {
                array_push($dataBarangList, [
                    "no"            => $no++,
                    "id"            => encrypt($data->id),
                    "tipeBarang"    => strtoupper(str_replace("_", " ", $data->tipe_barang)),
                    "jenisDokumen"  => $jenisDokumen,
                    "noAju"         => $data->no_aju,
                    "noDaftar"      => isset($noDaftar) ? $noDaftar : ($bcDetail == null ? "-" : $bcDetail['no_daftar']),
                    "tglDaftar"     => date('d/m/Y', strtotime($data->stock_date)),
                    "noPenerimaan"  => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_penerimaan_barang'] : '') : '',
                    "tglPenerimaan" => isset($penerimaanBarang) ? ($penerimaanBarang != null ? date('d/m/Y', strtotime($penerimaanBarang['tanggal'])) : '') : '',
                    "suratJalan"    => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_surat_jalan'] : '') : '',
                    "jenisSumber"   => $data->sumber,
                    "noOrder"       => $data->no_po,
                    "noInvoice"     => isset($penerimaanBarang) ? ($penerimaanBarang != null ? $penerimaanBarang['no_invoice'] : '') : '',
                    "divisi"        => $data->divisiName,
                    "warehouse"     => $data->warehouseName,
                    "supplier"      => isset($supplier) ? ($supplier != null ? $supplier['name'] : "-") : "-",
                    "kodeBarang"    => $kodeBarang,
                    "barang"        => $barangName,
                    "spesifikasi"   => $spesifikasiName,
                    "jumlahBarang"  => number_format($data->qty),
                    "satuanName"    => $satuanName,
                    "valas"         => $valas,
                    "hargaBarang"   => number_format($hargaBarang, 2),
                    "nilaiPenyerahan"  => number_format($hargaBarang, 2),
                    "jumlahPenerimaan"  => number_format($data->qty),
                    "selisih"       => 0,
                    "keterangan"    => $data->keterangan
                ]);
            }
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataBarang['totalData'],
            "recordsFiltered"   => $dataBarang['totalFilteredData'],
            "data"              => $dataBarangList,
            "payload"           => $payload
        ];

        return $data;
    }
}
