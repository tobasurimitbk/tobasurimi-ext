<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SatuansModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PoLokalBahanBakuImport extends BaseController
{
    protected $companyModel;
    protected $divisiModel;
    protected $supplierModel;
    protected $satuanModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $rmPurchaseOrderModel;
    protected $rmPurchaseOrderDetailModel;
    protected $warehouseModel;

    public function __construct()
    {
        $this->companyModel = new CompaniesModel();
        $this->divisiModel = new DivisisModel();
        $this->supplierModel = new SupplierModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->satuanModel = new SatuansModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->warehouseModel = new WarehousesModel();
    }


    public function index()
    {
        return view('Purchase/poLokalBahanBaku/form-import');
    }

    // public function importPreview()
    // {
    //     ini_set('memory_limit', '1024M');
    //     try {
    //         $file = $this->request->getFile('file');
    //         $ekstensiFile = $file->getClientExtension();

    //         if (!in_array($ekstensiFile, ['xlsx', 'xls'])) {
    //             return $this->response->setJSON([
    //                 'status'  => false,
    //                 'message' => "File harus berupa Excel (.xlsx atau .xls).",
    //                 'token'   => csrf_hash()
    //             ]);
    //         }

    //         $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //         $worksheet   = $spreadsheet->getActiveSheet();

    //         // =========================
    //         // READ EXCEL
    //         // =========================
    //         $rows = [];
    //         foreach ($worksheet->getRowIterator(3) as $row) {
    //             $cells = [];
    //             foreach ($row->getCellIterator() as $cell) {
    //                 $cells[] = trim((string) $cell->getValue());
    //             }
    //             if (!array_filter($cells)) continue;
    //             $rows[] = $cells;
    //         }

    //         // =========================
    //         // GROUPING (FIX DISINI 🔥)
    //         // =========================
    //         $dataPreview = [];
    //         $dataImported = [];

    //         foreach ($rows as $row) {

    //             [
    //                 $no,
    //                 $supplierName,
    //                 $barangName,
    //                 $poNo,
    //                 $divisi,
    //                 $poDate,
    //                 $qty,
    //                 $kodeSatuan,
    //                 $companyName,
    //                 $dppUmum,
    //                 $pphUmum,
    //                 $nilaiTotalUmum,
    //                 $dppHarian,
    //                 $pphHarian,
    //                 $nilaiTotalHarian,
    //                 $dppBulanan,
    //                 $pphBulanan,
    //                 $nilaiTotalBulanan,
    //                 $dppTambahan,
    //                 $pphTambahan,
    //                 $nilaiTotalTambahan,
    //                 $_
    //             ] = array_pad($row, 22, null);

    //             if (empty($poNo)) continue;

    //             $poNo = trim($poNo);


    //             if (is_numeric($poDate)) {
    //                 $poDate = Date::excelToDateTimeObject($poDate)->format('Y-m-d');
    //             } else {
    //                 $poDate = trim($poDate);
    //             }

    //             $qty = floatval($qty);

    //             $dppUmum =  (float)((float)$dppUmum);
    //             $pphUmum = (float)((float)$pphUmum);
    //             $nilaiTotalUmum = $pphUmum == 0 ? $dppUmum : (float)((float)$nilaiTotalUmum);

    //             $dppHarian = (float)((float)$dppHarian);
    //             $pphHarian = (float)((float)$pphHarian);
    //             $nilaiTotalHarian = $pphHarian == 0 ? $dppHarian : (float)((float)$nilaiTotalHarian);

    //             $dppBulanan = (float)((float)$dppBulanan);
    //             $pphBulanan = (float)((float)$pphBulanan);
    //             $nilaiTotalBulanan = $pphBulanan == 0 ? $dppBulanan : (float)((float)$nilaiTotalBulanan);

    //             $dppTambahan = (float)((float)$dppTambahan);
    //             $pphTambahan = (float)((float)$pphTambahan);
    //             $nilaiTotalTambahan = $pphTambahan == 0 ? $dppTambahan : (float)((float)$nilaiTotalTambahan);

    //             $totalBeforePph = $dppUmum + $dppHarian + $dppBulanan + $dppTambahan;
    //             $totalAfterPph = $nilaiTotalUmum + $nilaiTotalHarian + $nilaiTotalBulanan + $nilaiTotalTambahan;

    //             $dataPreview[] = [
    //                 'no' => $no,
    //                 'supplier_name' => trim($supplierName),
    //                 'barang_name' => trim($barangName),
    //                 'po_no' => trim($poNo),
    //                 'divisi' => trim($divisi),
    //                 'warehouse_name' => null,
    //                 'po_date' => trim($poDate),
    //                 'qty' => (float)$qty,
    //                 'kode_satuan' => trim($kodeSatuan),
    //                 'company_name' => str_replace(['-'], ' ', trim($companyName)),
    //                 'dpp_umum' => $dppUmum,
    //                 'pph_umum' => $pphUmum,
    //                 'nilai_total_umum' => $nilaiTotalUmum,
    //                 'dpp_harian' => $dppHarian,
    //                 'pph_harian' => $pphHarian,
    //                 'nilai_total_harian' => $nilaiTotalHarian,
    //                 'dpp_bulanan' => $dppBulanan,
    //                 'pph_bulanan' => $pphBulanan,
    //                 'nilai_total_bulanan' => $nilaiTotalBulanan,
    //                 'dpp_tambahan' => $dppTambahan,
    //                 'pph_tambahan' => $pphTambahan,
    //                 'nilai_total_tambahan' => $nilaiTotalTambahan,
    //                 'nilai_total' => (float) ($dppUmum) + (float) ($dppHarian) + (float) ($dppBulanan) + (float) ($dppTambahan),
    //                 'status' => true,
    //                 'message' => null,
    //             ];

    //             if ($pphUmum == 0) {
    //                 $pphMode = 'None';
    //                 $generalPrice = ($dppUmum / $qty);
    //                 $dailyPrice = ($dppHarian / $qty);
    //                 $monthlyPrice = ($dppBulanan / $qty);
    //             } else {
    //                 if ($nilaiTotalUmum == ($dppUmum - $pphUmum)) {
    //                     $pphMode = 'Supplier';
    //                     $generalPrice = ($dppUmum / $qty);
    //                     $dailyPrice = ($dppHarian / $qty);
    //                     $monthlyPrice = ($dppBulanan / $qty);
    //                 } else {
    //                     $pphMode = 'Company';
    //                     $generalPrice = ($nilaiTotalUmum / $qty);
    //                     $dailyPrice = ($nilaiTotalHarian / $qty);
    //                     $monthlyPrice = ($nilaiTotalBulanan / $qty);
    //                 }
    //             }

    //             $dataImported[] = [
    //                 'company_id' => null,
    //                 'warehouse_id' => null,
    //                 'purchase_request_id' => 0,
    //                 'supplier_id' => null,
    //                 'barang_id' => null,
    //                 'divisi_id' => null,
    //                 'kemasan_id' => 0,
    //                 'jumlah_kemasan' => 0,
    //                 'kemasan_tambahan' => '',
    //                 'bc_type' => 53,
    //                 'po_no' => $poNo,
    //                 'po_date' => $poDate,
    //                 'pph' => $pphMode,
    //                 'cong_sebenarnya' => 0,
    //                 'cong_batasan' => 0,
    //                 'subsidi_langsung' => $nilaiTotalTambahan,
    //                 'total' => $totalAfterPph,
    //                 'total_before_pph' => $totalBeforePph,
    //                 'total_after_pph' => $totalAfterPph,
    //                 'is_posted' => 1,
    //                 'status_penerimaan' => 1,
    //                 'status_external' => 'no',
    //                 'createdAt' => 119,
    //                 'dpp_harian' => $dppHarian,
    //                 'pph_harian' => $pphHarian,
    //                 'nilai_total_harian' => $nilaiTotalHarian,
    //                 'dpp_bulanan' => $dppBulanan,
    //                 'pph_bulanan' => $pphBulanan,
    //                 'nilai_total_bulanan' => $nilaiTotalBulanan,
    //                 'dpp_umum' => $dppUmum,
    //                 'pph_umum' => $pphUmum,
    //                 'nilai_total_umum' => $nilaiTotalUmum,
    //                 'dpp_tambahan' => $dppTambahan,
    //                 'pph_tambahan' => $pphTambahan,
    //                 'nilai_total_tambahan' => $nilaiTotalTambahan,
    //                 'nilai_total_qty' => $qty,
    //                 'from_import' => 'yes',
    //                 'detail' => [
    //                     'rm_purchase_order_id' => null,
    //                     'supplier_harga_id' => 0,
    //                     'barang1_id' => null,
    //                     'barang2_id' => null,
    //                     'satuan_id' => null,
    //                     'peti' => rand(1, 8) . " FBR",
    //                     'quality' => 'Baik',
    //                     'note' => 'CONG',
    //                     'qty' => $qty,
    //                     'qty_diterima' => $qty,
    //                     'remaining_qty' => 0,
    //                     'general_price' => $generalPrice,
    //                     'daily_price' => $dailyPrice,
    //                     'monthly_price' => $monthlyPrice,
    //                     'dpp_umum' => $dppUmum,
    //                     'pph_umum' => $pphUmum,
    //                     'nilai_total_umum' => $nilaiTotalUmum,
    //                     'dpp_harian' => $dppHarian,
    //                     'pph_harian' => $pphHarian,
    //                     'nilai_total_harian' => $nilaiTotalHarian,
    //                     'dpp_bulanan' => $dppBulanan,
    //                     'pph_bulanan' => $pphBulanan,
    //                     'nilai_total_bulanan' => $nilaiTotalBulanan,
    //                 ]
    //             ];
    //         }


    //         // AMBIL 
    //         // get company all
    //         $companyAll = $this->companyModel->where('deletedAt', null)->findAll();
    //         $mapCompany = [];
    //         foreach ($companyAll as $c) {
    //             $mapCompany[$c['company']] = $c['id'];
    //         }

    //         // get divisi all
    //         $divisiAll = $this->divisiModel
    //             ->select('
    //                 divisis.*,
    //                 w.warehouse_name,
    //                 w.id AS warehouse_id
    //             ')
    //             ->join('(
    //                 SELECT divisi_id, MAX(createdAt) as max_created
    //                 FROM warehouses
    //                 GROUP BY divisi_id
    //             ) wm', 'wm.divisi_id = divisis.id', 'left')
    //             ->join('warehouses w', 'w.divisi_id = wm.divisi_id AND w.createdAt = wm.max_created', 'left')
    //             ->where('divisis.deletedAt', null)
    //             ->findAll();

    //         $mapDivisi = [];
    //         foreach ($divisiAll as $d) {
    //             $mapDivisi[$d['company_id']][$d['divisi']] = [
    //                 'divisi_id' => $d['id'],
    //                 'warehouse_id' => $d['warehouse_id'],
    //                 'warehouse_name' => $d['warehouse_name']
    //             ];
    //         }

    //         // get supplier_id
    //         $supplierAll = $this->supplierModel->where('deletedAt', null)->findAll();
    //         $mapSupplier = [];
    //         foreach ($supplierAll as $s) {
    //             $mapSupplier[$s['company_id']][$s['name']] = $s['id'];
    //         }

    //         // get satuan
    //         $satuanAll = $this->satuanModel->where('deletedAt', null)->findAll();
    //         $mapSatuan = [];
    //         foreach ($satuanAll as $s) {
    //             $mapSatuan[$s['kode_satuan']] = $s['id'];
    //         }

    //         // get barang master spek
    //         $barangSpesifikasiAll = $this->barangMasterSpesifikasiModel
    //             ->select('barang_master_spesifikasi.*,barang_master.barang_name,barang_master.company_id')
    //             ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
    //             ->where('barang_master_spesifikasi.deletedAt', null)
    //             ->where('barang_master_spesifikasi.spesifikasi', "-")
    //             ->findAll();

    //         $mapSpesifikasi = [];
    //         foreach ($barangSpesifikasiAll as $b) {
    //             $mapSpesifikasi[$b['company_id']][$b['barang_name']] = [
    //                 'barang_id' => $b['barang_master_id'],
    //                 'spesifikasi_id' => $b['id']
    //             ];
    //         }

    //         // get barang master
    //         $barangMasterAll = $this->barangMasterModel
    //             ->where('barang_master.deletedAt', null)
    //             ->findAll();

    //         $mapBarangMaster = [];
    //         foreach ($barangMasterAll as $b) {
    //             $mapBarangMaster[$b['company_id']][$b['barang_name']] = $b['id'];
    //         }


    //         // validasi nomor po kembar
    //         $poList = $this->rmPurchaseOrderModel->where('deletedAt', null)->findAll();
    //         $mapPo = [];
    //         foreach ($poList as $p) {
    //             $mapPo[$p['company_id']][$p['po_no']] = $p['id'];
    //         }

    //         $cacheDivisi = [];
    //         $cacheBarang = [];
    //         $cacheSatuan = [];
    //         $cacheSupplier = [];

    //         foreach ($dataPreview as $i => $d) {

    //             $companyId = $mapCompany[$d['company_name']] ?? null;

    //             $divisiId = $warehouseId = $warehouseName = null;

    //             $divisi = $mapDivisi[$companyId][$d['divisi']] ?? null;

    //             $satuanId = $mapSatuan[$d['kode_satuan']] ?? null;

    //             $barangMasterId = $mapBarangMaster[$companyId][$d['barang_name']] ?? null;
    //             $barangSpesifikasi = $mapSpesifikasi[$companyId][$d['barang_name']] ?? null;

    //             $barangId = $spesifikasiId = null;

    //             $supplierId = $mapSupplier[$companyId][$d['supplier_name']] ?? null;

    //             $poCheck = $mapPo[$companyId][$d['po_no']] ?? null;

    //             $status = true;
    //             $message = null;

    //             // =========================
    //             // DIVISI (CACHE)
    //             // =========================
    //             if ($divisi != null) {
    //                 $divisiId = $divisi['divisi_id'];
    //                 $warehouseId = $divisi['warehouse_id'];
    //                 $warehouseName = $divisi['warehouse_name'];
    //             } else {

    //                 $key = $companyId . '|' . $d['divisi'];

    //                 if (!isset($cacheDivisi[$key])) {

    //                     $idDivisi = $this->divisiModel->insert([
    //                         "company_id" => $companyId,
    //                         "divisi" => $d['divisi'],
    //                         "jam_kerja_id" => null,
    //                         "type_divisi" => "UMUM"
    //                     ]);

    //                     $this->warehouseModel->insert([
    //                         'company_id' => $companyId,
    //                         'divisi_id' => $idDivisi,
    //                         'code_warehouse' => $d['divisi'],
    //                         'warehouse_name' => $d['divisi']
    //                     ]);

    //                     $cacheDivisi[$key] = $idDivisi;
    //                 }

    //                 $status = false;
    //                 $message = "divisi dibuat otomatis";
    //             }

    //             // =========================
    //             // BARANG SPESIFIKASI (CACHE)
    //             // =========================
    //             if ($barangSpesifikasi != null) {
    //                 $barangId = $barangSpesifikasi['barang_id'];
    //                 $spesifikasiId = $barangSpesifikasi['spesifikasi_id'];
    //             } else {

    //                 $key = $companyId . '|' . $d['barang_name'];

    //                 if (!isset($cacheBarang[$key])) {

    //                     $idBarangMaster = $this->barangMasterModel->insert([
    //                         'company_id' => $companyId,
    //                         'parent_type_id' => 0,
    //                         'kode_barang' => $d['barang_name'],
    //                         'barang_name' => $d['barang_name'],
    //                         'type_barang' => "bahan_baku"
    //                     ]);

    //                     $this->barangMasterSpesifikasiModel->insert([
    //                         'barang_master_id' => $idBarangMaster,
    //                         'spesifikasi' => '-',
    //                         'satuan_1' => $satuanId
    //                     ]);

    //                     $cacheBarang[$key] = $idBarangMaster;
    //                 }

    //                 $status = false;
    //                 $message = "barang dibuat otomatis";
    //             }

    //             // =========================
    //             // SATUAN (CACHE)
    //             // =========================
    //             if ($satuanId == null) {

    //                 if (!isset($cacheSatuan[$d['kode_satuan']])) {

    //                     $this->satuanModel->insert([
    //                         'kode_satuan' => $d['kode_satuan'],
    //                         'nama_satuan' => $d['kode_satuan']
    //                     ]);

    //                     $cacheSatuan[$d['kode_satuan']] = true;
    //                 }

    //                 $status = false;
    //                 $message = "satuan dibuat otomatis";
    //             }

    //             // =========================
    //             // SUPPLIER (CACHE)
    //             // =========================
    //             if ($supplierId == null) {

    //                 $key = $companyId . '|' . $d['supplier_name'];

    //                 if (!isset($cacheSupplier[$key])) {

    //                     $this->supplierModel->insert([
    //                         'company_id' => $companyId,
    //                         'kode' => $d['supplier_name'],
    //                         'name' => $d['supplier_name'],
    //                         'type' => "BAHAN BAKU"
    //                     ]);

    //                     $cacheSupplier[$key] = true;
    //                 }

    //                 $status = false;
    //                 $message = "supplier dibuat otomatis";
    //             }

    //             // =========================
    //             // PO CHECK
    //             // =========================
    //             if ($poCheck != null) {
    //                 $status = false;
    //                 $message = "no po sudah dipakai";
    //             }

    //             if ($companyId == null) {
    //                 $status = false;
    //                 $message = "company ga ada";
    //             }

    //             // =========================
    //             // SET RESULT
    //             // =========================
    //             $dataPreview[$i]['warehouse_name'] = $warehouseName;
    //             $dataPreview[$i]['message'] = $message;
    //             $dataPreview[$i]['status'] = $status;

    //             $dataImported[$i]['company_id'] = $companyId;
    //             $dataImported[$i]['warehouse_id'] = $warehouseId;
    //             $dataImported[$i]['supplier_id'] = $supplierId;
    //             $dataImported[$i]['barang_id'] = $barangId;
    //             $dataImported[$i]['divisi_id'] = $divisiId;
    //             $dataImported[$i]['detail']['barang1_id'] = $barangId;
    //             $dataImported[$i]['detail']['barang2_id'] = $spesifikasiId;
    //             $dataImported[$i]['detail']['satuan_id'] = $satuanId;
    //         }

    //         return $this->response->setJSON([
    //             'status' => true,
    //             'data' => [
    //                 'dataImported'  => $dataImported,
    //                 'dataPreview' => $dataPreview
    //             ],
    //             'token' => csrf_hash()
    //         ]);
    //     } catch (\Throwable $e) {
    //         return $this->response->setJSON([
    //             'status'  => false,
    //             'message' => "error read file : " . $e->getMessage() . " at file " . $e->getFile() . " in line " . $e->getLine(),
    //             'token'   => csrf_hash()
    //         ]);
    //     }
    // }

    public function importPreview()
    {
        ini_set('memory_limit', '1024M');
        try {
            $file = $this->request->getFile('file');
            $ekstensiFile = $file->getClientExtension();

            if (!in_array($ekstensiFile, ['xlsx', 'xls'])) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => "File harus berupa Excel (.xlsx atau .xls).",
                    'token'   => csrf_hash()
                ]);
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $worksheet   = $spreadsheet->getActiveSheet();

            // =========================
            // READ EXCEL
            // =========================
            // =========================
            // READ EXCEL
            // =========================
            $rows = [];

            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            foreach ($worksheet->getRowIterator(3) as $row) {
                $rowIndex = $row->getRowIndex();

                $cells = [];

                for ($col = 1; $col <= $highestColumnIndex; $col++) {

                    $cell = $worksheet->getCellByColumnAndRow($col, $rowIndex);

                    try {
                        $value = $cell->getCalculatedValue();
                    } catch (\Throwable $e) {
                        $value = $cell->getValue();
                    }

                    if (is_string($value)) {
                        $value = trim($value);
                    }

                    $cells[] = $value;
                }

                // Skip baris kosong
                if (!array_filter($cells, function ($value) {
                    return $value !== null && $value !== '';
                })) {
                    continue;
                }

                $rows[] = $cells;
            }
            // =========================
            // GROUPING (FIX DISINI 🔥)
            // =========================
            $dataPreview = [];
            $dataImported = [];
            $dataImportedGroupped = [];

            foreach ($rows as $row) {

                [
                    $no,
                    $supplierName,
                    $barangName,
                    $petiCong,
                    $size,
                    $poNo,
                    $poDate,
                    $divisi,
                    $warehouseName,
                    $qty,
                    $kodeSatuan,
                    $companyName,
                    $dppUmum,
                    $pphUmum,
                    $nilaiTotalUmum,
                    $dppHarian,
                    $pphHarian,
                    $nilaiTotalHarian,
                    $dppBulanan,
                    $pphBulanan,
                    $nilaiTotalBulanan,
                    $dppTambahan,
                    $pphTambahan,
                    $nilaiTotalTambahan,
                    $_
                ] = array_pad($row, 22, null);

                if (empty($poNo)) continue;

                $poNo = trim($poNo);
                $supplierName = strtoupper($supplierName);

                if (is_numeric($poDate)) {
                    $poDate = Date::excelToDateTimeObject($poDate)->format('Y-m-d');
                } else {
                    $poDate = trim($poDate);
                }

                $qty = floatval($qty);

                $dppUmum =  (float)((float)$dppUmum);
                $pphUmum = (float)((float)$pphUmum);
                $nilaiTotalUmum = $pphUmum == 0 ? $dppUmum : (float)((float)$nilaiTotalUmum);

                $dppHarian = (float)((float)$dppHarian);
                $pphHarian = (float)((float)$pphHarian);
                $nilaiTotalHarian = $pphHarian == 0 ? $dppHarian : (float)((float)$nilaiTotalHarian);

                $dppBulanan = (float)((float)$dppBulanan);
                $pphBulanan = (float)((float)$pphBulanan);
                $nilaiTotalBulanan = $pphBulanan == 0 ? $dppBulanan : (float)((float)$nilaiTotalBulanan);

                $dppTambahan = (float)((float)$dppTambahan);
                $pphTambahan = (float)((float)$pphTambahan);
                $nilaiTotalTambahan = $pphTambahan == 0 ? $dppTambahan : (float)((float)$nilaiTotalTambahan);

                $totalBeforePph = $dppUmum + $dppHarian + $dppBulanan + $dppTambahan;
                $totalAfterPph = $nilaiTotalUmum + $nilaiTotalHarian + $nilaiTotalBulanan + $nilaiTotalTambahan;

                $dataPreview[] = [
                    'no' => $no,
                    'supplier_name' => (trim($supplierName)),
                    'po_no' => trim($poNo),
                    'barang_name' => trim($barangName),
                    'peti' => $petiCong,
                    'size' => $size,
                    'divisi' => trim($divisi),
                    'warehouse_name' => null,
                    'po_date' => trim($poDate),
                    'qty' => (float)$qty,
                    'kode_satuan' => trim($kodeSatuan),
                    'company_name' => str_replace(['-'], ' ', trim($companyName)),
                    'dpp_umum' => $dppUmum,
                    'pph_umum' => $pphUmum,
                    'nilai_total_umum' => $nilaiTotalUmum,
                    'dpp_harian' => $dppHarian,
                    'pph_harian' => $pphHarian,
                    'nilai_total_harian' => $nilaiTotalHarian,
                    'dpp_bulanan' => $dppBulanan,
                    'pph_bulanan' => $pphBulanan,
                    'nilai_total_bulanan' => $nilaiTotalBulanan,
                    'dpp_tambahan' => $dppTambahan,
                    'pph_tambahan' => $pphTambahan,
                    'nilai_total_tambahan' => $nilaiTotalTambahan,
                    'nilai_total' => (float) ($nilaiTotalUmum) + (float) ($nilaiTotalHarian) + (float) ($nilaiTotalBulanan) + (float) ($nilaiTotalTambahan),
                    'status' => true,
                    'message' => null,
                ];



                if ($pphUmum == 0) {
                    $pphMode = 'None';
                    $generalPrice = ($dppUmum / $qty);
                    $dailyPrice = ($dppHarian / $qty);
                    $monthlyPrice = ($dppBulanan / $qty);
                } else {
                    if ($nilaiTotalUmum == ($dppUmum - $pphUmum)) {
                        $pphMode = 'Supplier';
                        $generalPrice = ($dppUmum / $qty);
                        $dailyPrice = ($dppHarian / $qty);
                        $monthlyPrice = ($dppBulanan / $qty);
                    } else {
                        $pphMode = 'Company';
                        $generalPrice = ($nilaiTotalUmum / $qty);
                        $dailyPrice = ($nilaiTotalHarian / $qty);
                        $monthlyPrice = ($nilaiTotalBulanan / $qty);
                    }
                }

                $tanggalCetak = generateTanggalCetakRandom($poDate);

                $dataImported[] = [
                    // parent
                    'company_id' => null,
                    'warehouse_id' => null,
                    'purchase_request_id' => 0,
                    'supplier_id' => null,
                    'barang_id' => null,
                    'divisi_id' => null,
                    'kemasan_id' => 0,
                    'jumlah_kemasan' => 0,
                    'kemasan_tambahan' => '',
                    'bc_type' => 53,
                    'po_no' => $poNo,
                    'po_date' => $poDate,
                    'pph' => $pphMode,
                    'cong_sebenarnya' => 0,
                    'cong_batasan' => 0,
                    'subsidi_langsung' => $nilaiTotalTambahan,
                    'total' => $totalAfterPph,
                    'total_before_pph' => $totalBeforePph,
                    'total_after_pph' => $totalAfterPph,
                    'is_posted' => 1,
                    'status_penerimaan' => 1,
                    'status_external' => 'no',
                    'createdAt' => 119,
                    'dpp_harian' => $dppHarian,
                    'pph_harian' => $pphHarian,
                    'nilai_total_harian' => $nilaiTotalHarian,
                    'dpp_bulanan' => $dppBulanan,
                    'pph_bulanan' => $pphBulanan,
                    'nilai_total_bulanan' => $nilaiTotalBulanan,
                    'dpp_umum' => $dppUmum,
                    'pph_umum' => $pphUmum,
                    'nilai_total_umum' => $nilaiTotalUmum,
                    'dpp_tambahan' => $dppTambahan,
                    'pph_tambahan' => $pphTambahan,
                    'nilai_total_tambahan' => $nilaiTotalTambahan,
                    'nilai_total_qty' => $qty,
                    'from_import' => 'yes',
                    "tanggal_cetak" => $tanggalCetak,
                    "tanggal_cetak_kasbon" => $tanggalCetak,
                    // untuk detail
                    "peti" => $petiCong,
                    "note" => $size,
                    "general_price" => $generalPrice,
                    "daily_price" => $dailyPrice,
                    "monthly_price" => $monthlyPrice,
                    "barang1_id" => null,
                    "barang2_id" => null,
                    "satuan_id" => null
                ];
            }

            // reset key numerik
            $dataImportedGroupped = array_values($dataImportedGroupped);

            // AMBIL 
            // get company all
            $companyAll = $this->companyModel->where('deletedAt', null)->findAll();
            $mapCompany = [];
            foreach ($companyAll as $c) {
                $mapCompany[$c['company']] = $c['id'];
            }

            // get divisi all
            $divisiAll = $this->divisiModel
                ->select('
                    divisis.*,
                    w.warehouse_name,
                    w.id AS warehouse_id
                ')
                ->join('(
                    SELECT divisi_id, MAX(createdAt) as max_created
                    FROM warehouses
                    GROUP BY divisi_id
                ) wm', 'wm.divisi_id = divisis.id', 'left')
                ->join('warehouses w', 'w.divisi_id = wm.divisi_id AND w.createdAt = wm.max_created', 'left')
                ->where('divisis.deletedAt', null)
                ->findAll();

            $mapDivisi = [];
            foreach ($divisiAll as $d) {
                $mapDivisi[$d['company_id']][$d['divisi']] = [
                    'divisi_id' => $d['id'],
                    'warehouse_id' => $d['warehouse_id'],
                    'warehouse_name' => $d['warehouse_name']
                ];
            }

            // get supplier_id
            $supplierAll = $this->supplierModel->where('deletedAt', null)->findAll();
            $mapSupplier = [];
            foreach ($supplierAll as $s) {
                $mapSupplier[$s['company_id']][$s['name']] = $s['id'];
            }

            // get satuan
            $satuanAll = $this->satuanModel->where('deletedAt', null)->findAll();
            $mapSatuan = [];
            foreach ($satuanAll as $s) {
                $mapSatuan[$s['kode_satuan']] = $s['id'];
            }

            // get barang master spek
            $barangSpesifikasiAll = $this->barangMasterSpesifikasiModel
                ->select('barang_master_spesifikasi.*,barang_master.barang_name,barang_master.company_id')
                ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
                ->where('barang_master_spesifikasi.deletedAt', null)
                ->where('barang_master_spesifikasi.spesifikasi', "-")
                ->where('type_barang', "bahan_baku")
                ->findAll();

            $mapSpesifikasi = [];
            foreach ($barangSpesifikasiAll as $b) {
                $mapSpesifikasi[$b['company_id']][$b['barang_name']] = [
                    'barang_id' => $b['barang_master_id'],
                    'spesifikasi_id' => $b['id']
                ];
            }


            // get barang master
            $barangMasterAll = $this->barangMasterModel
                ->where('barang_master.deletedAt', null)
                ->findAll();

            $mapBarangMaster = [];
            foreach ($barangMasterAll as $b) {
                $mapBarangMaster[$b['company_id']][$b['barang_name']] = $b['id'];
            }


            // validasi nomor po kembar
            $poList = $this->rmPurchaseOrderModel->where('deletedAt', null)->findAll();
            $mapPo = [];
            foreach ($poList as $p) {
                $mapPo[$p['company_id']][$p['po_no']] = $p['id'];
            }

            $cacheDivisi = [];
            $cacheBarang = [];
            $cacheSatuan = [];
            $cacheSupplier = [];

            foreach ($dataPreview as $i => $d) {

                $companyId = $mapCompany[$d['company_name']] ?? null;

                $divisiId = $warehouseId = $warehouseName = null;

                $divisi = $mapDivisi[$companyId][$d['divisi']] ?? null;

                $satuanId = $mapSatuan[$d['kode_satuan']] ?? null;

                $barangMasterId = $mapBarangMaster[$companyId][$d['barang_name']] ?? null;
                $barangSpesifikasi = $mapSpesifikasi[$companyId][$d['barang_name']] ?? null;

                $barangId = $spesifikasiId = null;

                $supplierId = $mapSupplier[$companyId][$d['supplier_name']] ?? null;

                $poCheck = $mapPo[$companyId][$d['po_no']] ?? null;

                $status = true;
                $message = null;

                // =========================
                // DIVISI (CACHE)
                // =========================
                if ($divisi != null) {
                    $divisiId = $divisi['divisi_id'];
                    $warehouseId = $divisi['warehouse_id'];
                    $warehouseName = $divisi['warehouse_name'];
                } else {

                    $key = $companyId . '|' . $d['divisi'];

                    if (!isset($cacheDivisi[$key])) {

                        $idDivisi = $this->divisiModel->insert([
                            "company_id" => $companyId,
                            "divisi" => $d['divisi'],
                            "jam_kerja_id" => null,
                            "type_divisi" => "UMUM"
                        ]);

                        $this->warehouseModel->insert([
                            'company_id' => $companyId,
                            'divisi_id' => $idDivisi,
                            'code_warehouse' => $d['divisi'],
                            'warehouse_name' => $d['divisi']
                        ]);

                        $cacheDivisi[$key] = $idDivisi;
                    }

                    $status = false;
                    $message = "divisi dibuat otomatis";
                }

                // =========================
                // BARANG SPESIFIKASI (CACHE)
                // =========================
                if ($barangSpesifikasi != null) {
                    $barangId = $barangSpesifikasi['barang_id'];
                    $spesifikasiId = $barangSpesifikasi['spesifikasi_id'];
                } else {

                    $key = $companyId . '|' . $d['barang_name'];

                    if (!isset($cacheBarang[$key])) {

                        $idBarangMaster = $this->barangMasterModel->insert([
                            'company_id' => $companyId,
                            'parent_type_id' => 0,
                            'kode_barang' => $d['barang_name'],
                            'barang_name' => $d['barang_name'],
                            'type_barang' => "bahan_baku"
                        ]);

                        $this->barangMasterSpesifikasiModel->insert([
                            'barang_master_id' => $idBarangMaster,
                            'spesifikasi' => '-',
                            'satuan_1' => $satuanId
                        ]);

                        $cacheBarang[$key] = $idBarangMaster;
                    }

                    $status = false;
                    $message = "barang dibuat otomatis";
                }

                // =========================
                // SATUAN (CACHE)
                // =========================
                if ($satuanId == null) {

                    if (!isset($cacheSatuan[$d['kode_satuan']])) {

                        $this->satuanModel->insert([
                            'kode_satuan' => $d['kode_satuan'],
                            'nama_satuan' => $d['kode_satuan']
                        ]);

                        $cacheSatuan[$d['kode_satuan']] = true;
                    }

                    $status = false;
                    $message = "satuan dibuat otomatis";
                }

                // =========================
                // SUPPLIER (CACHE)
                // =========================
                if ($supplierId == null) {

                    $key = $companyId . '|' . $d['supplier_name'];

                    if (!isset($cacheSupplier[$key])) {

                        $this->supplierModel->insert([
                            'company_id' => $companyId,
                            'kode' => $d['supplier_name'],
                            'name' => $d['supplier_name'],
                            'type' => "BAHAN BAKU"
                        ]);

                        $cacheSupplier[$key] = true;
                    }

                    $status = false;
                    $message = "supplier dibuat otomatis";
                }

                // =========================
                // PO CHECK
                // =========================
                if ($poCheck != null) {
                    $status = false;
                    $message = "no po sudah dipakai";
                }

                if ($companyId == null) {
                    $status = false;
                    $message = "company ga ada";
                }

                // =========================
                // SET RESULT
                // =========================
                $dataPreview[$i]['warehouse_name'] = $warehouseName;
                $dataPreview[$i]['message'] = $message;
                $dataPreview[$i]['status'] = $status;

                $dataImported[$i]['company_id'] = $companyId;
                $dataImported[$i]['warehouse_id'] = $warehouseId;
                $dataImported[$i]['supplier_id'] = $supplierId;
                $dataImported[$i]['barang_id'] = $barangId;
                $dataImported[$i]['divisi_id'] = $divisiId;
                $dataImported[$i]['barang1_id'] = $barangId;
                $dataImported[$i]['barang2_id'] = $spesifikasiId;
                $dataImported[$i]['satuan_id'] = $satuanId;
            }


            foreach ($dataImported as $d) {
                $poNo = $d['po_no'];

                $detail = [
                    'rm_purchase_order_id' => null,
                    'supplier_harga_id' => 0,
                    'barang1_id' => $d['barang1_id'],
                    'barang2_id' => $d['barang2_id'],
                    'satuan_id' => $d['satuan_id'],
                    'peti' => $d['peti'],
                    'quality' => 'Baik',
                    'note' => $d['note'],
                    'qty' => $d['nilai_total_qty'],
                    'qty_diterima' => 0,
                    'remaining_qty' => $d['nilai_total_qty'],
                    'general_price' => $d['general_price'],
                    'daily_price' => $d['daily_price'],
                    'monthly_price' => $d['monthly_price'],
                    'dpp_umum' => $d['dpp_umum'],
                    'pph_umum' => $d['pph_umum'],
                    'nilai_total_umum' => $d['nilai_total_umum'],
                    'dpp_harian' => $d['dpp_harian'],
                    'pph_harian' => $d['pph_harian'],
                    'nilai_total_harian' => $d['nilai_total_harian'],
                    'dpp_bulanan' => $d['dpp_bulanan'],
                    'pph_bulanan' => $d['pph_bulanan'],
                    'nilai_total_bulanan' => $d['nilai_total_bulanan'],
                ];

                if (!isset($dataImportedGroupped[$poNo])) {

                    $dataImportedGroupped[$poNo] = [
                        'company_id' => $d['company_id'],
                        'warehouse_id' => $d['warehouse_id'],
                        'purchase_request_id' => 0,
                        'supplier_id' => $d['supplier_id'],
                        'barang_id' => $d['barang_id'],
                        'divisi_id' => $d['divisi_id'],
                        'kemasan_id' => $d['kemasan_id'],
                        'jumlah_kemasan' => $d['jumlah_kemasan'],
                        'bc_type' => 53,
                        'po_no' => $poNo,
                        'po_date' => $d['po_date'],
                        'pph' => $d['pph'],
                        'cong_sebenarnya' => $d['cong_sebenarnya'],
                        'cong_batasan' => $d['cong_batasan'],
                        'subsidi_langsung' => 0,
                        'total' => 0,
                        'total_before_pph' => 0,
                        'total_after_pph' => 0,
                        'is_posted' => 0,
                        'status_penerimaan' => 0,
                        'status_external' => 'no',
                        'createdAt' => 119,
                        'dpp_harian' => 0,
                        'pph_harian' => 0,
                        'nilai_total_harian' => 0,
                        'dpp_bulanan' => 0,
                        'pph_bulanan' => 0,
                        'nilai_total_bulanan' => 0,
                        'dpp_umum' => 0,
                        'pph_umum' => 0,
                        'nilai_total_umum' => 0,
                        'dpp_tambahan' => 0,
                        'pph_tambahan' => 0,
                        'nilai_total_tambahan' => 0,
                        'nilai_total_qty' => 0,
                        'from_import' => 'yes',
                        'tanggal_cetak' => $d['tanggal_cetak'],
                        'tanggal_cetak_kasbon' => $d['tanggal_cetak_kasbon'],
                        'detail' => [],
                    ];
                }

                // SUM
                $dataImportedGroupped[$poNo]['subsidi_langsung'] += (float)$d['subsidi_langsung'];
                $dataImportedGroupped[$poNo]['total'] += (float)$d['total'];
                $dataImportedGroupped[$poNo]['total_before_pph'] += (float)$d['total_before_pph'];
                $dataImportedGroupped[$poNo]['total_after_pph'] += (float)$d['total_after_pph'];


                $dataImportedGroupped[$poNo]['dpp_harian'] += (float)$d['dpp_harian'];
                $dataImportedGroupped[$poNo]['pph_harian'] += (float)$d['pph_harian'];
                $dataImportedGroupped[$poNo]['nilai_total_harian'] += (float)$d['nilai_total_harian'];

                $dataImportedGroupped[$poNo]['dpp_bulanan'] += (float)$d['dpp_bulanan'];
                $dataImportedGroupped[$poNo]['pph_bulanan'] += (float)$d['pph_bulanan'];
                $dataImportedGroupped[$poNo]['nilai_total_bulanan'] += (float)$d['nilai_total_bulanan'];

                $dataImportedGroupped[$poNo]['dpp_umum'] += (float)$d['dpp_umum'];
                $dataImportedGroupped[$poNo]['pph_umum'] += (float)$d['pph_umum'];
                $dataImportedGroupped[$poNo]['nilai_total_umum'] += (float)$d['nilai_total_umum'];

                $dataImportedGroupped[$poNo]['dpp_tambahan'] += (float)$d['dpp_tambahan'];
                $dataImportedGroupped[$poNo]['pph_tambahan'] += (float)$d['pph_tambahan'];
                $dataImportedGroupped[$poNo]['nilai_total_tambahan'] += (float)$d['nilai_total_tambahan'];

                $dataImportedGroupped[$poNo]['nilai_total_qty'] += (float)$d['nilai_total_qty'];

                // simpan detail
                $dataImportedGroupped[$poNo]['detail'][] = $detail;
            }
            $dataImportedGroupped = array_values($dataImportedGroupped);

            return $this->response->setJSON([
                'status' => true,
                'data' => [
                    'dataImported'  => $dataImportedGroupped,
                    'dataPreview' => $dataPreview
                ],
                'token' => csrf_hash()
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => "error read file : " . $e->getMessage() . " at file " . $e->getFile() . " in line " . $e->getLine(),
                'token'   => csrf_hash()
            ]);
        }
    }

    public function importData()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {

            $listData = json_decode($this->request->getPost('list_data'), true);

            if (empty($listData)) {
                throw new \Exception('Data import tidak ditemukan');
            }

            foreach ($listData as $dataRmPurchaseOrder) {

                $details = $dataRmPurchaseOrder['detail'] ?? [];

                unset($dataRmPurchaseOrder['detail']);

                // insert header PO
                $rmPurchaseOrderId = $this->rmPurchaseOrderModel->insert($dataRmPurchaseOrder, true);

                // insert detail
                foreach ($details as $detail) {


                    $detail['rm_purchase_order_id'] = $rmPurchaseOrderId;

                    $this->rmPurchaseOrderDetailModel->insert($detail);
                }
            }

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => 'Berhasil import data PO'
            ]);
        } catch (\Throwable $e) {

            $db->transRollback();

            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => sprintf(
                    'Failed import because error : %s at file %s line %s',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            ]);
        }
    }

    // public function importData()
    // {
    //     $db = \Config\Database::connect();
    //     $db->transBegin();
    //     $penerimaanBarangModel = new PenerimaanBarangModel();
    //     try {
    //         var_dump(json_decode($_POST['list_data']));
    //         die;


    //         foreach (json_decode($_POST['list_data']) as $l) {
    //             $dataRmPurchaseOrder = (array)$l;
    //             $dataRmPurchaseOrderDetail = (array)$dataRmPurchaseOrder['detail'];

    //             // unset($dataRmPurchaseOrder['detail']);
    //             // $rmPurchaseOrderId = $this->rmPurchaseOrderModel->insert($dataRmPurchaseOrder);
    //             // $dataRmPurchaseOrderDetail['rm_purchase_order_id'] = $rmPurchaseOrderId;
    //             // $this->rmPurchaseOrderDetailModel->($dataRmPurchaseOrderDetail);

    //             // // import lpb
    //             // $penerimaanBarangModel->generateLpbBB(
    //             //     $rmPurchaseOrderId,
    //             //     $dataRmPurchaseOrder['warehouse_id'],
    //             //     $dataRmPurchaseOrder['bc_type'],
    //             //     $dataRmPurchaseOrder['po_date']
    //             // );
    //         }
    //         $db->transCommit();
    //         return response()->setJSON([
    //             'token' => csrf_hash(),
    //             'message' => "berhasil import data po",
    //             'status' => true
    //         ]);
    //     } catch (Exception $e) {
    //         $db->transRollback();
    //         return  response()->setJSON([
    //             'status' => false,
    //             'token' => csrf_hash(),
    //             'message' => "failed import because error : " . $e->getMessage() . " at file " . $e->getFile() . " in line " . $e->getLine(),
    //         ]);
    //     }
    // }
}
