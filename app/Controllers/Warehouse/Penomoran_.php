<?php

namespace App\Controllers\Warehouse;

use App\Controllers\API\Attendances;
use App\Controllers\API\Employees;
use App\Controllers\BaseController;
use App\Controllers\Purchase\TandaTerimaSupBB;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AttendanceKeteranganModel;
use App\Models\AttendancesModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC23Model;
use App\Models\BC40Model;
use App\Models\BCPurchaseOrderLPBModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\CountryModel;
use App\Models\DivisisModel;
use App\Models\EmployeesFingerModel;
use App\Models\EmployeesModel;
use App\Models\EmployeesUnitsModel;
use App\Models\FormPerijinanModel;
use App\Models\JamKerjaDetailModel;
use App\Models\JamKerjaModel;
use App\Models\JurnalUmumModel;
use App\Models\MetadataModel;
use App\Models\MutasiDetailModel;
use App\Models\PajakTandaTerimaFakturModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SalesContractSizeBreakdownModel;
use App\Models\SalesKontrakModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SppDetailModel;
use App\Models\SppModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampLogModel;
use App\Models\StockRevampModel;
use App\Models\SupplierModel;
use App\Models\TandaTerimaFakturDetailModel;
use App\Models\TandaTerimaFakturModel;
use App\Models\TransaksiJurnalModel;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

// SELECT * 
// FROM penerimaan_barang 
// WHERE tanggal >= '2025-01-01' 
// AND tanggal <= '2025-01-31' 
// AND status_penerimaan = 'LOKAL' 
// AND tipe_bahan = 'BAKU' 
// AND company_id = 2 
// AND deletedAt IS NULL 
// ORDER BY tanggal DESC
// 15 = GLOBAL
// 2 = KIM 2
// 1 = KIM 1
class Penomoran_ extends BaseController
{

    public function index()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $penerimaan = $db->query("
            SELECT * 
            FROM penerimaan_barang 
            WHERE tanggal >= '2025-01-01' 
            AND tanggal <= '2025-03-31' 
            AND status_penerimaan = 'LOKAL' 
            AND tipe_bahan = 'PENOLONG' 
            AND company_id = 15 
            AND deletedAt IS NULL 
            ORDER BY id ASC
        ");

        try {
            $penerimaanBarangModel = new PenerimaanBarangModel();
            $divisiModel = new DivisisModel();
            $i = 1;
            foreach ($penerimaan->getResult() as $p) {
                $noPenerimaan = $p->no_penerimaan_barang;
                $divisi = $divisiModel->where('id', $p->divisi_id)->first();

                $explode = explode('/', $noPenerimaan);
                $explode0 = "LPB"; // kode
                $explode1 = $divisi['divisi']; // divisi
                $explode2 = $explode[2];
                $explode3 = $explode[3];

                $result = "$explode0/$explode1/$explode2/$explode3/2025";

                $penerimaanBarangModel->update($p->id, [
                    'no_penerimaan_barang' => $result
                ]);

                // echo $result . "<br>";
                $i++;
            }
            $db->transCommit();
            echo "<br>" . "Total Update : " . $i;
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }

    public function repairPPhPoBahanBaku()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $poBahanBaku = $db->query("
            SELECT * 
            FROM rm_purchase_orders 
            WHERE company_id = 16 
            and po_date >= '2025-07-01' 
            AND po_date <= '2025-12-31' 
            AND deletedAt IS NULL 
            ORDER BY id ASC
        ");

        try {
            $rmPurchaseOrderModel = new RMPurchaseOrderModel();
            $i = 1;
            foreach ($poBahanBaku->getResult() as $p) {
                $totalFinal = $rmPurchaseOrderModel->generateTotalBeforeAndAfterPph($p->id);

                $rmPurchaseOrderModel->update($p->id, [
                    'total_before_pph'  => $totalFinal['total_before_pph'],
                    'total_after_pph' => $totalFinal['total_after_pph']
                ]);

                $i++;
            }

            $db->transCommit();
            echo "<br>" . "Total Update PO : " . $i;
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }

    // public function repairNoPurchaseOrdersBahanBaku()
    // {
    //     $db = \Config\Database::connect();
    //     $db->transStart();

    //     $poBahanBaku = $db->query("
    //         SELECT * 
    //         FROM rm_purchase_orders 
    //         WHERE company_id != 16 
    //         and po_date >= '2025-01-01' 
    //         AND po_date <= '2025-01-31' 
    //         AND deletedAt IS NULL 
    //         ORDER BY id ASC
    //     ");

    //     try {
    //         $rmPurchaseOrderModel = new RMPurchaseOrderModel();
    //         $i = 1;
    //         foreach ($poBahanBaku->getResult() as $i => $p) {
    //             $poNo = $p[$i]->po_no;
    //             $explode0 = $poNo[0];
    //             $explode1 = $poNo[1];
    //             $explode2 = $poNo[2];


    //             $formatNumber = $explode0 . "/" . $explode1 . "/" . $explode2;
    //             $rmPurchaseOrderModel->where('id', $p->id, []);
    //         }
    //     } catch (Exception $e) {
    //         $db->transRollback();
    //         echo "Error " . $e->getMessage();
    //     }
    // }

    public function repairPurchaseOrderBahanBakuDuplicate()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $penerimaanBarangDuplicate = $db->query('
            SELECT multiple_po_id, COUNT(*) AS jumlah
            FROM penerimaan_barang
            WHERE deletedAt IS NULL
            AND status_penerimaan="LOKAL" AND
            tipe_bahan="BAKU"
            GROUP BY multiple_po_id
            HAVING COUNT(*) > 1;
        ');

        try {
            $penerimaanBarangModel = new PenerimaanBarangModel();
            $i = 0;
            foreach ($penerimaanBarangDuplicate->getResult() as $d) {
                $penerimaanBarang = $penerimaanBarangModel->where('deletedAt', null)->where('multiple_po_id', $d->multiple_po_id)
                    ->where('status_penerimaan', "LOKAL")
                    ->where('tipe_bahan', "BAKU")
                    ->orderBy('createdAt', 'desc')
                    ->first();

                if ($penerimaanBarang) {
                    $i++;
                    $penerimaanBarangModel->update($penerimaanBarang['id'], [
                        'deletedAt' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $db->transCommit();
            echo "<br>" . "Total Update LPB : " . $i;
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }

    public function generateCountry()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $filePath = FCPATH . 'json\countries.json'; // Path ke file JSON
            if (!file_exists($filePath)) {
                return $this->response->setJSON(['error' => 'File not found']);
            }

            $jsonData = file_get_contents($filePath);
            $countries = json_decode($jsonData, true); // Ubah ke array
            if (!$countries) {
                return $this->response->setJSON(['error' => 'Invalid JSON format']);
            }

            $countryModel = new CountryModel();
            $i = 0;
            foreach ($countries as $country) {
                // Validasi
                $validasi = $countryModel->where('code', $country['code'])->first();
                if ($validasi == null) {
                    $countryModel->insert([
                        'country_name' => $country['name'],
                        'code' => $country['code']
                    ]);
                    $i++;
                }
            }
            $db->transCommit();
            return $this->response->setJSON(['success' => $i . ' countries inserted']);
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }

    public function repairStockDateIncoming()
    {
        $bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $stockDetailModel = new StockDetailModel();

        $bcPurchaseOrder = $bcPurchaseOrderModel
            ->select('bc_purchase_order.createdAt as tanggal_dokumen,bc_40.no_aju')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->where('bc_purchase_order.deletedAt', null)
            ->where('bc_purchase_order.company_id', 16)
            ->findAll();

        $totalUpdate = 0;
        foreach ($bcPurchaseOrder as $b) {
            // Cari Stock Incoming
            $stockDetail2 = $stockDetailModel
                ->select('stock_details.id')
                ->join('stock_details2', 'stock_details2.stock_detail_id = stock_details.id', 'left')
                ->where('stock_details2.no_aju', $b['no_aju'])
                ->where('stock_details.sumber', "LPB")
                ->findAll();

            $stockDetailsId = [];
            foreach ($stockDetail2 as $s) {
                array_push($stockDetailsId, $s['id']);
            }

            if (count($stockDetailsId) != 0) {
                $stockDetailModel->whereIn('id', $stockDetailsId)->set('stock_date', date('Y-m-d', strtotime($b['tanggal_dokumen'])))->update();
            }

            $totalUpdate++;
        }

        \var_dump("Update Stock Date", $totalUpdate);
        die;
    }

    public function repairStockIncomingNonPabean()
    {

        $penerimaanBarangModel = new PenerimaanBarangModel();
        $stockDetailModel = new StockDetailModel();

        $penerimaanBarang = $penerimaanBarangModel
            ->select('penerimaan_barang.tanggal as tanggal_dokumen,penerimaan_barang.no_penerimaan_barang, company_id')
            ->where('bc_type', 0)
            ->where('status_post', "FINISH")
            ->where('deletedAt', null)
            ->where('company_id', 1)
            ->findAll();

        $totalUpdate = 0;
        foreach ($penerimaanBarang as $b) {
            // Cari Stock Incoming
            $stockDetail2 = $stockDetailModel
                ->select('stock_details.id')
                ->join('stock', 'stock.id = stock_details.stock_id', 'left')
                ->join('stock_details2', 'stock_details2.stock_detail_id = stock_details.id', 'left')
                ->where('stock_details.no_dokumen', $b['no_penerimaan_barang'])
                ->where('stock.company_id', $b['company_id'])
                ->where('stock_details2.bc_id', 0)
                ->where('stock_details.sumber', "LPB")
                ->findAll();

            $stockDetailsId = [];
            foreach ($stockDetail2 as $s) {
                array_push($stockDetailsId, $s['id']);
            }

            if (count($stockDetailsId) != 0) {
                $stockDetailModel->whereIn('id', $stockDetailsId)->set('stock_date', date('Y-m-d', strtotime($b['tanggal_dokumen'])))->update();
            }

            $totalUpdate++;
        }

        \var_dump("Update Stock Date", $totalUpdate);
        die;
    }

    public function repairStockView()
    {
        return view('tes');
    }

    public function repairStockExcel()
    {
        $file = $this->request->getFile('file');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid!');
        }

        // Load file Excel
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

        $groupedData = [];

        for ($i = 0; $i < count($data); $i++) {
            $type_barang = trim($data[$i][0]);
            $kategori_barang = trim($data[$i][1]);
            $kode_barang = trim($data[$i][2]);
            $spesifikasi = trim($data[$i][3]);
            $departemen = trim($data[$i][4]);
            $kode_warehouse = trim($data[$i][5]);
            $jenis_dokumen_pabean = trim($data[$i][6]);
            $no_aju = trim($data[$i][7]);
            $qty = (float) trim($data[$i][8]);
            $tanggal_dokumen = "";
            $nama_supplier = trim($data[$i][10]);

            if (!empty($data[$i][9])) {
                // Cek apakah nilai adalah angka (format serial Excel)
                if (is_numeric($data[$i][9])) {
                    $tanggal_dokumen = Date::excelToDateTimeObject($data[$i][9])->format('Y-m-d');
                } else {
                    $tanggal_dokumen = $data[$i][9]; // Jika sudah dalam format string
                }
            } else {
                $tanggal_dokumen = date('Y-m-d'); // Jika kosong, gunakan tanggal sekarang
            }

            // Kunci unik untuk grouping
            $key = $type_barang . '|' . $kategori_barang . '|' . $kode_barang . '|' . $spesifikasi . '|' . $departemen . '|' . $kode_warehouse . '|' . $jenis_dokumen_pabean . '|' . $no_aju;

            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'type_barang' => $type_barang,
                    'kategori_barang' => $kategori_barang,
                    'kode_barang' => $kode_barang,
                    'spesifikasi' => $spesifikasi,
                    'departemen' => $departemen,
                    'kode_warehouse' => $kode_warehouse,
                    'jenis_dokumen_pabean' => $jenis_dokumen_pabean,
                    'no_aju' => $no_aju,
                    'qty' => 0,
                    'tanggal_dokumen' => $tanggal_dokumen,
                    'nama_supplier' => $nama_supplier,
                ];
            }

            // Menjumlahkan qty per grup
            $groupedData[$key]['qty'] += $qty;
        }

        // Membuat Spreadsheet baru untuk hasil
        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();

        // Menambahkan Header
        $newSheet->setCellValue('A1', 'Type Barang')
            ->setCellValue('B1', 'Kategori Barang')
            ->setCellValue('C1', 'Kode Barang')
            ->setCellValue('D1', 'Spesifikasi')
            ->setCellValue('E1', 'Departemen')
            ->setCellValue('F1', 'Kode Warehouse')
            ->setCellValue('G1', 'Jenis Dokumen Pabean')
            ->setCellValue('H1', 'No Aju')
            ->setCellValue('I1', 'Total Qty')
            ->setCellValue('J1', 'Tanggal Dokumen')
            ->setCellValue('K1', 'Nama Supplier');

        // Menuliskan hasil ke dalam file baru
        $rowNumber = 2;
        foreach ($groupedData as $group) {
            $newSheet->setCellValue("A$rowNumber", $group['type_barang'])
                ->setCellValue("B$rowNumber", $group['kategori_barang'])
                ->setCellValue("C$rowNumber", $group['kode_barang'])
                ->setCellValue("D$rowNumber", $group['spesifikasi'])
                ->setCellValue("E$rowNumber", $group['departemen'])
                ->setCellValue("F$rowNumber", $group['kode_warehouse'])
                ->setCellValue("G$rowNumber", $group['jenis_dokumen_pabean'])
                ->setCellValue("H$rowNumber", $group['no_aju'])
                ->setCellValue("I$rowNumber", $group['qty'])
                ->setCellValue("J$rowNumber", $group['tanggal_dokumen'])
                ->setCellValue("K$rowNumber", $group['nama_supplier']);
            $rowNumber++;
        }

        // Mengatur header agar file langsung diunduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Rekap_New_Stock' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        // Output langsung ke browser
        $writer = new Xlsx($newSpreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function bulkNoSalesOrderInvoice()
    {
        $salesOrderInvoiceModel = new SalesOrderInvoiceModel();

        $salesOrderInvoice = $salesOrderInvoiceModel->select('id,no_faktur')
            ->orderBy('id', 'asc')
            ->where('deletedAt', null)
            ->findAll();

        foreach ($salesOrderInvoice as $i => $s) {
            $nomor = str_pad($i + 1, 3, '0', STR_PAD_LEFT); // Menambahkan nol di depan (3 digit)
            $newNumber = "LKL/INV/25/II/" . $nomor;

            $salesOrderInvoiceModel->update($s['id'], [
                'no_faktur' => $newNumber
            ]);
        }
    }

    public function repairJurnalUmum()
    {
        $companyId = 15;
        $condition = [
            'DATE(tanggal_transaksi) >=' => '2025-04-01',
            'DATE(tanggal_transaksi) <=' => '2025-04-31'
        ];

        $transaksiJurnalModel = new TransaksiJurnalModel();
        $jurnalUmumModel = new JurnalUmumModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $rmImportPoDetailModel = new RMImportPODetailModel();
        $total = 0;

        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            // lOKAL BAHAN BAKU
            // $poLokalBahanBaku = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_local_bb,rm_purchase_orders.po_no')
            //     ->join('transaksi_pembelian', 'transaksi_jurnal.id = transaksi_pembelian.id_transaksi_jurnal', 'left')
            //     ->join('rm_purchase_orders', 'rm_purchase_orders.id = transaksi_pembelian.id_local_bb', 'left')
            //     ->where('transaksi_jurnal.deleted_at', null)
            //     ->where('transaksi_pembelian.id_local_bb is not', null)
            //     ->where('rm_purchase_orders.company_id', $companyId)
            //     ->where('rm_purchase_orders.is_posted', 1)
            //     ->where('rm_purchase_orders.deletedAt', null)
            //     ->where($condition)
            //     ->findAll();

            // // dd($poLokalBahanBaku);


            // foreach ($poLokalBahanBaku as $p) {
            //     // Update Transaksi Jurnal
            //     $transaksiJurnalModel->update($p['id'], [
            //         'valas' => "IDR",
            //         'valas_id' => 30,
            //         'exchange_rate' => 1,
            //         'uraian_transaksi' => $p['po_no']
            //     ]);
            //     // Update Jurnal Umum
            //     $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
            //         ->where('company_id', $companyId)
            //         ->where('deletedAt', null)
            //         ->findAll();

            //     $keteranganJurnal = $rmPurchaseOrderDetailModel->getSpesifikasiBarangAsString($p['id_local_bb']);

            //     foreach ($jurnalUmum as $j) {
            //         $jurnalUmumModel->update($j['id'], [
            //             'valas' => 30,
            //             'kurs' => 1,
            //             'keterangan' => $keteranganJurnal
            //         ]);
            //     }

            //     $total++;
            // }

            // LOKAL BAHAN PENOLONG
            $poLokalBahanPenolong = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_po_bp,am_purchase_orders.po_no')
                ->join('transaksi_pembelian', 'transaksi_jurnal.id = transaksi_pembelian.id_transaksi_jurnal', 'left')
                ->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left')
                ->where('transaksi_jurnal.deleted_at', null)
                ->where('transaksi_pembelian.id_po_bp IS NOT NULL')
                ->where('am_purchase_orders.company_id', $companyId)
                ->where('am_purchase_orders.is_posted', 1)
                ->where('am_purchase_orders.po_type', "Lokal")
                ->where($condition)
                ->findAll();

            // dd($poLokalBahanPenolong);

            foreach ($poLokalBahanPenolong as $p) {
                // Update Transaksi Jurnal
                $transaksiJurnalModel->update($p['id'], [
                    'valas' => "IDR",
                    'valas_id' => 30,
                    'exchange_rate' => 1,
                    'uraian_transaksi' => $p['po_no']
                ]);
                // Update Jurnal Umum
                $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
                    ->where('company_id', $companyId)
                    ->where('deletedAt', null)
                    ->findAll();

                $keteranganJurnal = $amPurchaseOrderDetailModel->getSpesifikasiBarangAsString($p['id_po_bp']);

                foreach ($jurnalUmum as $j) {
                    $jurnalUmumModel->update($j['id'], [
                        'valas' => 30,
                        'kurs' => 1,
                        'keterangan' => $keteranganJurnal
                    ]);
                }

                $total++;
            }

            // IMPORT BAHAN PENOLONG
            // $poLokalBahanPenolong = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_po_bp,am_purchase_orders.po_no')
            //     ->join('transaksi_pembelian', 'transaksi_jurnal.id = transaksi_pembelian.id_transaksi_jurnal', 'left')
            //     ->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left')
            //     ->where('transaksi_jurnal.deleted_at', null)
            //     ->where('transaksi_pembelian.id_po_bp IS NOT NULL')
            //     ->where('am_purchase_orders.company_id', $companyId)
            //     ->where('am_purchase_orders.is_posted', 1)
            //     ->where('am_purchase_orders.po_type', "Import")
            //     ->findAll();

            // foreach ($poLokalBahanPenolong as $p) {
            //     // Update Transaksi Jurnal
            //     $transaksiJurnalModel->update($p['id'], [
            //         'uraian_transaksi' => $p['po_no']
            //     ]);
            //     // Update Jurnal Umum
            //     $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
            //         ->where('company_id', $companyId)
            //         ->where('deletedAt', null)
            //         ->findAll();

            //     $keteranganJurnal = $amPurchaseOrderDetailModel->getSpesifikasiBarangAsString($p['id_po_bp']);

            //     foreach ($jurnalUmum as $j) {
            //         $jurnalUmumModel->update($j['id'], [
            //             'keterangan' => $keteranganJurnal
            //         ]);
            //     }

            //     $total++;
            // }
            // IMPORT BAHAN BAKU
            // $poImportBahanBaku = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_import_bb,rm_import_pos.po_no')
            //     ->join('transaksi_pembelian', 'transaksi_jurnal.id = transaksi_pembelian.id_transaksi_jurnal', 'left')
            //     ->join('rm_import_pos', 'rm_import_pos.id = transaksi_pembelian.id_import_bb', 'left')
            //     ->where('transaksi_jurnal.deleted_at', null)
            //     ->where('transaksi_pembelian.id_import_bb IS NOT NULL')
            //     ->where('rm_import_pos.company_id', $companyId)
            //     ->where('rm_import_pos.is_posted', 1)
            //     ->findAll();

            // foreach ($poImportBahanBaku as $p) {
            //     // Update Transaksi Jurnal
            //     $transaksiJurnalModel->update($p['id'], [
            //         'uraian_transaksi' => $p['po_no']
            //     ]);
            //     // Update Jurnal Umum
            //     $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
            //         ->where('company_id', $companyId)
            //         ->where('deletedAt', null)
            //         ->findAll();

            //     $keteranganJurnal = $rmImportPoDetailModel->getSpesifikasiBarangAsString($p['id_import_bb']);

            //     foreach ($jurnalUmum as $j) {
            //         $jurnalUmumModel->update($j['id'], [
            //             'keterangan' => $keteranganJurnal
            //         ]);
            //     }

            //     $total++;
            // }

            $db->transCommit();

            var_dump("Total Updated Journal Bahan pENOLONG", $total);
            die;
        } catch (Exception $e) {
            $db->transRollback();
            \var_dump("Error ", $e->getMessage(), $e->getLine());
        }
    }

    public function repairNominalJurnalUmum()
    {
        $companyId = 1;
        $condition = [
            'DATE(tanggal_transaksi) >=' => '2025-03-01',
            'DATE(tanggal_transaksi) <=' => '2025-03-31',
        ];

        $total = 0;

        $db = \Config\Database::connect();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        try {
            $db->transBegin();

            $penerimaanBarang = $db->query("
                SELECT *FROM
                penerimaan_barang
                WHERE company_id=$companyId
                 
            ");

            $db->transCommit();

            var_dump("Total Updated Journal", $total);
            die;
        } catch (Exception $e) {
            $db->transRollback();
            \var_dump("Error ", $e->getMessage(), $e->getLine());
        }
    }

    public function repairStockBc40()
    {
        $startDate = "2025-02-21";
        $endDate = "2025-02-28";
        $companyId = 16;
        $bcId = 53;

        $bc40Model = new BC40Model();
        $bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $stockDetail2Model = new StockDetail2Model();
        // Pakai Query Update
        $bc40 = $bc40Model->select('
            bc_40.*,
            bc_purchase_order.id as bc_purchase_order_id,
            bc_purchase_order.po_type,
            bc_purchase_order.status_posting,
            bc_purchase_order.supplier_id,
            bc_purchase_order.createdAt as tanggal_dokumen,
        ')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_40.bc_purchase_order_id', 'right')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->where('date(bc_purchase_order.createdAt) >=', $startDate)
            ->where('date(bc_purchase_order.createdAt) <=', $endDate)
            ->where('bc_purchase_order.status_posting', '1')
            ->where('bc_purchase_order.company_id', $companyId)
            ->where('bc_40.deletedAt', null)
            ->where('bc_purchase_order.deletedAt', null)
            ->findAll();

        $totalUpdate = 0;
        $db = \Config\Database::connect();

        foreach ($bc40 as $b) {
            $detailBarang = $bcPurchaseOrderModel->findDetailBarangWithSpek($b['bc_purchase_order_id']);
            // Foreach Detail Barang
            foreach ($detailBarang as $d) {

                $detail = $db->table('stock_details2')
                    ->select('stock_details2.id')
                    ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id')
                    ->join('stock', 'stock.id = stock_details2.stock_id')
                    ->where('stock_details2.supplier_id', $b['supplier_id'])
                    ->where('stock_details2.stock_dokumen', $d['po_no'])
                    ->where('stock_details2.bc_id', $bcId)
                    ->where('stock_details.sumber', 'LPB')
                    ->where('stock.barang1_id', $d['barang1_id'])
                    ->where('stock.barang2_id', $d['spesifikasi_id'])
                    ->get()
                    ->getResult();

                foreach ($detail as $row) {
                    $db->table('stock_details2')
                        ->where('id', $row->id)
                        ->update([
                            'qty' => $d['qty_lpb_konversi'],
                            'updatedAt' => date('Y-m-d H:i:s')
                        ]);
                    $totalUpdate++;
                }


                $totalUpdate++;
            }
        }

        \var_dump("Total Updated", $totalUpdate);
    }

    public function generateJamKerja()
    {
        $jamKerjaModel = new JamKerjaModel();
        $jamKerjaDetailModel = new JamKerjaDetailModel();

        $jamKerjaName = "JAM KERJA CANNING";
        $companyId = 2;
        $divisiId = 6; // Hanya PTS atau Canning (pts 5 canning 6)
        $jamInStart = "05:00";
        $jamOutStart = "13:00";

        $arrDay = [
            1 => "SENIN",
            2 => "SELASA",
            3 => "RABU",
            4 => "KAMIS",
            5 => "JUMAT",
            6 => "SABTU",
            7 => "MINGGU",
        ];

        $arrJamKerja = [];

        for ($i = 1; $i <= 26; $i++) {
            // Buat object waktu masuk dan keluar
            $jamMasuk = new DateTime($jamInStart);
            $jamPulang = new DateTime($jamOutStart);

            // Hitung jam terlambat (+1 menit dari jam pulang)
            $jamTerlambat = (clone $jamMasuk)->modify('+1 minute')->format('H:i');

            $arrJamKerjaDetail = [];

            foreach ($arrDay as $dayNumber => $dayName) {
                $arrJamKerjaDetail[] = [
                    'jam_kerja_id' => 0, // nanti diisi setelah simpan
                    'hari' => $dayName,
                    'jam_masuk' => $jamMasuk->format('H:i'),
                    'jam_pulang' => $jamPulang->format('H:i'),
                ];
            }

            $arrJamKerja[] = [
                'company_id' => $companyId,
                'divisi_id' => $divisiId,
                'shift' => "SHIFT " . $i,
                'jenis' => $jamKerjaName,
                'jam_terlambat' => $jamTerlambat,
                'detail' => $arrJamKerjaDetail,
            ];

            // Tambah 30 menit untuk jam berikutnya
            $jamInStart = (new DateTime($jamInStart))->modify('+30 minutes')->format('H:i');
            $jamOutStart = (new DateTime($jamOutStart))->modify('+30 minutes')->format('H:i');
        }

        // Ok Insert Ke DB
        foreach ($arrJamKerja as $a) {
            $jamKerjaId = $jamKerjaModel->insert([
                'company_id' => $a['company_id'],
                'divisi_id' => $a['divisi_id'],
                'shift' => $a['shift'],
                'jenis' => $a['jenis'],
                'jam_terlambat' => $a['jam_terlambat'],
            ]);

            foreach ($a['detail'] as $d) {
                $jamKerjaDetailModel->insert([
                    'jam_kerja_id' => $jamKerjaId, // nanti diisi setelah simpan
                    'hari' => $d['hari'],
                    'jam_masuk' => $d['jam_masuk'],
                    'jam_pulang' => $d['jam_pulang']
                ]);
            }
        }

        dd("OK", $arrJamKerja);
    }

    public function repairJurnalUmumLpbBp()
    {
        $transaksiJurnalModel = new TransaksiJurnalModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();
        // Loop
        $companyId = 1;
        $startDate = "2025-07-01";
        $endDate = "2025-07-31";
        $tipeTransaksi = "1406"; // PEMBELIAN

        $selectQry = "
            transaksi_jurnal.id,
            transaksi_jurnal.tanggal_transaksi,
            transaksi_jurnal.no_transaksi,
            transaksi_jurnal.uraian_transaksi,
            transaksi_jurnal.metode_input,
            transaksi_jurnal.valas,
            transaksi_jurnal.exchange_rate,
            transaksi_jurnal.total_debit,
            transaksi_jurnal.no_bukti,  
        ";

        $dataQry = $transaksiJurnalModel->asObject()->select($selectQry)
            ->join('jurnal_umum', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->where('transaksi_jurnal.tanggal_transaksi >=', $startDate)
            ->where('transaksi_jurnal.tanggal_transaksi <=', $endDate)
            ->where('transaksi_jurnal.type_transaksi', $tipeTransaksi)
            ->where('jurnal_umum.company_id', $companyId)
            ->groupBy('jurnal_umum.id_transaksi')
            ->findAll();

        $total = 0;

        foreach ($dataQry as $d) {
            $penerimaanBarangId = null;

            $penerimaanBarang = $penerimaanBarangModel
                ->select('penerimaan_barang.*')
                ->where('penerimaan_barang.company_id', $companyId)
                ->groupStart()
                ->like('multiple_po_no', $d->uraian_transaksi)
                ->groupEnd()
                ->first();
            $penerimaanBarangId = $penerimaanBarang == null ? null : $penerimaanBarang['id'];

            if ($penerimaanBarangId != null) {
                $transaksiJurnalModel->update($d->id, [
                    'penerimaan_barang_id' => $penerimaanBarangId
                ]);

                $total++;
            }
        }


        \var_dump("Total Updated", $total);
        die;
    }

    public function repairCustomerLokal()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $customer = $db->query("
    SELECT 
        c1.id AS old_id,
        c2.id AS new_id,
        c1.name AS old_name,
        c2.name AS new_name,
        c1.createdAt AS old_created,
        c2.createdAt AS new_created
    FROM customers c1
    JOIN customers c2 
        ON c1.id < c2.id
        AND c1.deletedAt IS NULL
        AND c2.deletedAt IS NULL
        AND (
            LOWER(c1.name) LIKE CONCAT('%', LOWER(c2.name), '%')
            OR LOWER(c2.name) LIKE CONCAT('%', LOWER(c1.name), '%')
        )
    ORDER BY c1.createdAt DESC, c2.createdAt DESC
");


        try {
            // dd($customer->getResult());
            $salesOrderInvoiceModel = new SalesOrderInvoiceModel();
            $berhasilUpdate = 0;

            foreach ($customer->getResult() as $d) {

                //
                $salesOrderInvoice = $salesOrderInvoiceModel->asObject()->where('id_customer', $d->new_id)
                    ->where('document_type', "import")
                    ->findAll();

                if (\count($salesOrderInvoice) != 0) {
                    foreach ($salesOrderInvoice as $s) {
                        $salesOrderInvoiceModel->update($s->id, [
                            'id_customer' => $d->old_id
                        ]);
                        $berhasilUpdate++;
                    }
                }
            }

            $totalInvoiceImport =  $salesOrderInvoiceModel
                ->where('document_type', "import")
                ->findAll();

            $db->transCommit();
            dd("Puspa Ngaceng", $berhasilUpdate, "Novaldo Ngeblek", \count($totalInvoiceImport));
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }

    public function generateShipmentValueOrderFormEkspor()
    {
        $salesOrderExportModel = new SalesOrderExportModel();
        $list = $salesOrderExportModel->where('deletedAt', null)->findAll();

        foreach ($list as $l) {
            // Update Shipment Value & Shipment Value Net & Valas
            $shipmentDetail = $salesOrderExportModel->generateTotalPrice(
                $l['sales_order_export_id']
            );

            $salesOrderExportModel->update($l['sales_order_export_id'], [
                'shipment_value' => $shipmentDetail['shipment_value'],
                'shipment_value_net' => $shipmentDetail['shipment_value_net'],
                'valas_id' => $shipmentDetail['valas_id']
            ]);
        }

        echo "Oke";
    }

    public function drawExcelSatuanView()
    {
        return view('tes');
    }

    public function drawExcelSatuanAction()
    {

        $db = \Config\Database::connect();
        try {
            $metaDataModel = new MetadataModel();
            $file = $this->request->getFile('file');

            if (!$file->isValid()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'File tidak valid'
                ]);
            }

            // Load spreadsheet
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();

            $data = [];
            foreach ($sheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Hapus ** dari kode satuan
                $kodeSatuan = isset($rowData[0]) ? preg_replace('/\*+/', '', trim($rowData[0])) : null;
                $namaSatuan = isset($rowData[1]) ? trim($rowData[1]) : null;

                if ($kodeSatuan && $namaSatuan) {
                    $data[] = [
                        'kode_satuan' => $kodeSatuan,
                        'nama_satuan' => $namaSatuan,
                    ];
                }
            }

            // Ambil semua kode satuan yang sudah ada di database dalam satu query
            $kodeList = array_column($data, 'kode_satuan');

            $existingRecords = $metaDataModel
                ->where('name', 'Kode Satuan BC')
                ->whereIn('value', $kodeList)
                ->findAll();

            // Buat array untuk lookup cepat
            $existingValues = array_column($existingRecords, 'value');
            $existingMap = array_flip($existingValues);

            $resultData = [];
            foreach ($data as $d) {
                if (!isset($existingMap[$d['kode_satuan']])) {
                    $resultData[] = [
                        'name' => "Kode Satuan BC",
                        'value' => $d['kode_satuan'],
                        'description' => $d['nama_satuan']
                    ];
                }
            }

            $metaDataModel->insertBatch($resultData);
            $db->transCommit();

            \dd("OK");
        } catch (Exception $e) {

            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateNoKtpSupplier()
    {
        $supplierModel = new SupplierModel();
        $supplier = $supplierModel->where('deletedAt', null)
            ->where('type', "BAHAN BAKU")
            ->findAll();

        foreach ($supplier as $s) {
            $alamat = $s['address'];
            $noKtp = null;

            // Tangkap angka berapa pun setelah "NIK:"
            if (preg_match('/NIK\s*:\s*(\d+)/', $alamat, $matches)) {
                $noKtp = $matches[1];

                $supplierModel->update($s['id'], [
                    'no_ktp' => $noKtp
                ]);
            }
        }

        echo "DONE";
        die;
    }

    public function repairFormatNomorLpb()
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $tanggalAwal = "2025-09-01";
        $tanggalAkhir = "2025-09-31";

        // ambil data urut
        $penerimaanBarang = $penerimaanBarangModel
            ->where('tanggal >=', $tanggalAwal)
            ->where('tanggal <=', $tanggalAkhir)
            ->where('deletedAt', null)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->findAll();

        // reset dulu
        $penerimaanBarangModel
            ->where('tanggal >=', $tanggalAwal)
            ->where('tanggal <=', $tanggalAkhir)
            ->where('deletedAt', null)
            ->set('no_penerimaan_barang', null)
            ->update();

        // counter per group
        $counter = [];

        foreach ($penerimaanBarang as $p) {
            $key = $p['company_id'] . '-' . $p['status_penerimaan'] . '-' . $p['tipe_bahan'] . '-' . date('Ym', strtotime($p['tanggal']));

            if (!isset($counter[$key])) {
                $counter[$key] = 1;
            }

            // generate prefix/template
            $noPenerimaan = $penerimaanBarangModel->get_no(
                $p['tanggal'],
                $p['company_id'],
                $p['status_penerimaan'],
                $p['tipe_bahan'],
            );

            // update
            $penerimaanBarangModel->update($p['id'], [
                'no_penerimaan_barang' => $noPenerimaan
            ]);

            $counter[$key]++; // increment
        }

        dd("OK");
    }

    public function repairKomponenHargaPoLokalBahanBaku()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $poBahanBaku = $db->query("
            SELECT * 
            FROM rm_purchase_orders 
            WHERE company_id = 16 
            and po_date >= '2025-09-01' 
            AND po_date <= '2025-12-31' 
            AND deletedAt IS NULL 
            ORDER BY id ASC
        ");

        try {
            $rmPurchaseOrderModel = new RMPurchaseOrderModel();
            $i = 1;
            foreach ($poBahanBaku->getResult() as $p) {
                $totalFinal = $rmPurchaseOrderModel->generateKomponenHarga($p->id);

                $rmPurchaseOrderModel->update($p->id, [
                    'total_before_pph'  => $totalFinal['nilai_before_pph'],
                    'total_after_pph' => $totalFinal['nilai_after_pph'],
                    'dpp_umum' => $totalFinal['dpp_umum'],
                    'dpp_harian' => $totalFinal['dpp_harian'],
                    'dpp_bulanan' => $totalFinal['dpp_bulanan'],
                    'dpp_tambahan' => $totalFinal['dpp_tambahan'],
                    'pph_umum' => $totalFinal['pph_umum'],
                    'pph_harian' => $totalFinal['pph_harian'],
                    'pph_bulanan' => $totalFinal['pph_bulanan'],
                    'pph_tambahan' => $totalFinal['pph_tambahan'],
                    'nilai_total_umum' => $totalFinal['nilai_total_umum'],
                    'nilai_total_harian' => $totalFinal['nilai_total_harian'],
                    'nilai_total_bulanan' => $totalFinal['nilai_total_bulanan'],
                    'nilai_total_tambahan' => $totalFinal['nilai_total_tambahan'],
                    'nilai_total_qty' => $totalFinal['nilai_total_qty']
                ]);

                $i++;
            }

            $db->transCommit();
            echo "<br>" . "Total Update PO : " . $i;
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }

    public function repairHargaTerakhirMasterBarangBackup()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $companyId = 2;
            $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
            $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
            $selectQry = "am_purchase_order_details.*";
            $poDetail = $amPurchaseOrderDetailModel
                ->select($selectQry)
                ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
                ->where('am_purchase_orders.company_id', $companyId)
                ->where('am_purchase_orders.deletedAt', null)
                ->where('am_purchase_order_details.deletedAt', null)
                ->where('po_date >=', "2025-09-01")
                ->orderBy('po_date', "desc")
                ->groupBy('am_purchase_order_details.spesifikasi_id')
                ->findAll();

            $updatedData = [];
            foreach ($poDetail as $b) {
                $hargaTerakhir = $amPurchaseOrderDetailModel->historiHargaPOBahanPenolongFirst(
                    $b['spesifikasi_id'],
                    $companyId,
                    $b['unit']
                );


                if ($hargaTerakhir) {
                    $updatedData[] = [
                        'id' => $hargaTerakhir['spesifikasi_id'],
                        'supplier_terakhir' => $hargaTerakhir['supplier_id'],
                        'harga_terakhir' => $hargaTerakhir['price'],
                        'unit_terakhir' => $hargaTerakhir['unit']
                    ];
                }
            }

            if (!empty($updatedData)) {
                $barangMasterSpesifikasiModel->updateBatch($updatedData, 'id');
            }

            $db->transCommit();
            echo "OOK";
        } catch (Exception $e) {
            $db->transRollback();
        };
    }

    public function repairHargaTerakhirMasterBarang()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $companyId = 16;
            $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
            $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();

            // 1️⃣ Ambil semua data PO detail + join PO (sekali query)
            $poDetails = $amPurchaseOrderDetailModel
                ->select('
                am_purchase_order_details.spesifikasi_id,
                am_purchase_order_details.unit,
                am_purchase_order_details.price,
                am_purchase_orders.supplier_id,
                am_purchase_orders.po_date
            ')
                ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
                ->where('am_purchase_orders.company_id', $companyId)
                ->where('am_purchase_orders.deletedAt', null)
                ->where('am_purchase_order_details.deletedAt', null)
                ->where('am_purchase_orders.po_date >=', '2025-09-01')
                ->orderBy('am_purchase_orders.po_date', 'desc')
                ->findAll();

            // 2️⃣ Mapping per spesifikasi_id saja (abaikan unit)
            $grouped = [];
            foreach ($poDetails as $row) {
                $id = $row['spesifikasi_id'];

                if (!isset($grouped[$id])) {
                    $grouped[$id] = $row;
                } else {
                    // ganti kalau PO lebih baru
                    if (strtotime($row['po_date']) > strtotime($grouped[$id]['po_date'])) {
                        $grouped[$id] = $row;
                    }
                }
            }

            // 3️⃣ Siapkan data update batch
            $updatedData = array_map(fn($row) => [
                'id' => $row['spesifikasi_id'],
                'supplier_terakhir' => $row['supplier_id'],
                'harga_terakhir' => $row['price'],
                'unit_terakhir' => $row['unit'],
            ], array_values($grouped));

            // 4️⃣ Update batch
            if (!empty($updatedData)) {
                $barangMasterSpesifikasiModel->updateBatch($updatedData, 'id');
            }

            $db->transCommit();
            echo "OK";
        } catch (\Throwable $e) {
            $db->transRollback();
            echo "Error: " . $e->getMessage();
        }
    }





    public function generateMultipleSppIdLpb()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $companyId = 1;
            $penerimaanBarangModel = new PenerimaanBarangModel();
            $amPurchaseOrderModel = new AMPurchaseOrderModel();

            $startDate = "2025-09-01";
            $endDate = "2025-09-30";

            $penerimaanBarangList = $penerimaanBarangModel
                ->where('status_penerimaan', "LOKAL")
                ->where('tipe_bahan', "PENOLONG")
                ->where('deletedAt', null)
                ->where('tanggal >= ', $startDate)
                ->where('tanggal <=', $endDate)
                ->where('company_id', $companyId)
                ->findAll();

            $penerimaanBarangUpdateList = [];
            foreach ($penerimaanBarangList as $p) {
                $multiplePoId = json_decode($p['multiple_po_id']);

                if (count($multiplePoId) != 0) {
                    $sppList = $amPurchaseOrderModel->getSPP(
                        $multiplePoId
                    );

                    $multipleSppId = str_replace(['\\"', '\\', '"'], '', json_encode(array_column($sppList, 'id')));
                    $multipleSppNo = str_replace(['\\"', '\\'], '', json_encode(array_column($sppList, 'spp_no')));

                    array_push($penerimaanBarangUpdateList, [
                        'id' => $p['id'],
                        'multiple_spp_id' => $multipleSppId,
                        'multiple_spp_no' => $multipleSppNo
                    ]);
                }
            }
            if (count($penerimaanBarangUpdateList) != 0) {
                $penerimaanBarangModel->updateBatch($penerimaanBarangUpdateList, 'id');
            }
            $db->transCommit();
            echo "DOne";
        } catch (Exception $e) {
            $db->transRollback();
            var_dump($e->getMessage());
        }
    }

    public function generateStokRevampNonPabean()
    {

        $stockRevampModel = new StockRevampModel();
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

            $companyId = 2;
            $bcType = 0;

            $selectQry = "
            penerimaan_barang_detail.*,
            penerimaan_barang.divisi_id,
            penerimaan_barang.warehouse_id,
            penerimaan_barang.status_penerimaan,
            penerimaan_barang.tipe_bahan,
            penerimaan_barang.bc_type,
            metadata.value as type_bc";
            $dataQry = $penerimaanBarangDetailModel
                ->select($selectQry)
                ->join('penerimaan_barang', "penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id", 'left')
                ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
                ->where('penerimaan_barang.id', "8997")
                ->where('penerimaan_barang.company_id', $companyId)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang.status_post', "FINISH")
                ->where('penerimaan_barang.bc_type', $bcType)
                ->findAll();


            $data = [];
            foreach ($dataQry as $d) {
                $poType = $d['status_penerimaan'] . " " . $d['tipe_bahan'];
                array_push($data, [
                    'company_id' => $companyId,
                    'barang_master_id' => $d['barang_id'],
                    'spesifikasi_id' => $d['spesifikasi_id'],
                    'unit_id' => $d['unit_konversi'],
                    'divisi_id' => $d['divisi_id'],
                    'warehouse_id' => $d['warehouse_id'],
                    'qty_bersih' => $d['jml_masuk_konversi'],
                    'qty_diterima' => $d['jml_masuk_konversi'],
                    'bc_id' => $d['bc_type'],
                    'type_bc' => $d['type_bc'] ?? "NON PABEAN",
                    'reference_id'  => $d['penerimaan_barang_id'],
                    'po_type' => $poType,
                    'po_id' => $d['purchase_order_id'],
                    'reference_type' => "LPB",
                    'status' => 'IN',
                    'penerimaan_barang_detail_id' => $d['id']
                ]);
            }

            // var_dump($data);
            // die;

            $db = db_connect();
            foreach ($data as $d) {
                $stockId = $stockRevampModel->insertStockRevamp(
                    $db,
                    $d
                );

                $penerimaanBarangDetailModel->update($d['penerimaan_barang_detail_id'], [
                    'stock_detail_id' => $stockId
                ]);
            }


            $db->transCommit();
            dd("DONE");
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateStokRevampPabean()
    {
        $stockRevampModel = new StockRevampModel();
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

            $companyId = 16;

            $selectQry = "
            penerimaan_barang_detail.*,
            penerimaan_barang.divisi_id,
            penerimaan_barang.warehouse_id,
            penerimaan_barang.status_penerimaan,
            penerimaan_barang.tipe_bahan,
            penerimaan_barang.bc_type,
            metadata.value as type_bc";
            $dataQry = $penerimaanBarangDetailModel
                ->select($selectQry)
                ->join('penerimaan_barang', "penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id", 'left')
                ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
                ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
                ->where('penerimaan_barang.tanggal >=', '2025-09-21')
                ->where('penerimaan_barang.tanggal <=', '2025-09-30')
                ->where('penerimaan_barang.company_id', $companyId)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang.status_post', "FINISH")
                ->where('bc_purchase_order.deletedAt', null)
                ->where('bc_purchase_order.status_posting', 1)
                ->findAll();

            $data = [];
            foreach ($dataQry as $d) {
                $poType = $d['status_penerimaan'] . " " . $d['tipe_bahan'];
                array_push($data, [
                    'company_id' => $companyId,
                    'barang_master_id' => $d['barang_id'],
                    'spesifikasi_id' => $d['spesifikasi_id'],
                    'unit_id' => $d['unit_konversi'],
                    'divisi_id' => $d['divisi_id'],
                    'warehouse_id' => $d['warehouse_id'],
                    'qty_bersih' => $d['jml_masuk_konversi'],
                    'qty_diterima' => $d['jml_masuk_konversi'],
                    'bc_id' => $d['bc_type'],
                    'type_bc' => $d['type_bc'] ?? "NON PABEAN",
                    'reference_id'  => $d['penerimaan_barang_id'],
                    'po_type' => $poType,
                    'po_id' => $d['purchase_order_id'],
                    'reference_type' => "LPB",
                    'status' => 'IN',
                    'penerimaan_barang_detail_id' => $d['id']
                ]);
            }

            // var_dump($data);
            // die;


            $db = db_connect();
            foreach ($data as $d) {
                $stockId = $stockRevampModel->insertStockRevamp(
                    $db,
                    $d
                );

                $penerimaanBarangDetailModel->update($d['penerimaan_barang_detail_id'], [
                    'stock_detail_id' => $stockId
                ]);
            }

            $db->transCommit();
            dd("DONE");
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateNoAjuBcPurcaseOrder()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            $bcPurchaseOrderModel = new BCPurchaseOrderModel();
            $bc40Model = new BC40Model();
            $bc23Model = new BC23Model();

            $bc40 = $bc40Model
                ->where('DATE(createdAt) >=', '2025-09-01')
                ->where('DATE(createdAt) <=', '2025-09-30')
                ->where('deletedAt', null)
                ->findAll();

            foreach ($bc40 as $b) {
                $bcPurchaseOrderModel->update($b['bc_purchase_order_id'], [
                    'no_aju' => $b['no_aju'],
                    'bc_id' => 53,
                    'bc_type' => "BC 4.0"
                ]);
            }

            $bc23 = $bc23Model
                ->where('DATE(createdAt) >=', '2025-09-01')
                ->where('DATE(createdAt) <=', '2025-09-30')
                ->where('deletedAt', null)
                ->findAll();

            foreach ($bc23 as $b) {
                $bcPurchaseOrderModel->update($b['bc_purchase_order_id'], [
                    'no_aju' => $b['no_aju'],
                    'bc_id' => 48,
                    'bc_type' => "BC 2.3"
                ]);
            }


            $db->transCommit();
            dd("OKE");
        } catch (Exception $e) {

            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateStockDetailIdPenerimaanBarangDetailPabean()
    {
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $db = \Config\Database::connect();
        $db->transBegin();
        try {

            $companyId = 16;

            $builder = $penerimaanBarangDetailModel
                ->select("
                    penerimaan_barang_detail.*,
                    penerimaan_barang.divisi_id,
                    penerimaan_barang.warehouse_id,
                    penerimaan_barang.status_penerimaan,
                    penerimaan_barang.tipe_bahan,
                    penerimaan_barang.bc_type,
                    metadata.value as type_bc,
                    stock_revamp_detail.id as stock_detail_id_revamp
                ")
                ->join('penerimaan_barang', "penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id", 'left')
                ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
                ->join('stock_revamp_detail', "stock_revamp_detail.reference_id = penerimaan_barang_detail.penerimaan_barang_id AND stock_revamp_detail.reference_type = 'LPB'", 'left')
                ->join('stock_revamp', "stock_revamp.id = stock_revamp_detail.stock_id 
                             AND stock_revamp.barang_master_id = penerimaan_barang_detail.barang_id 
                             AND stock_revamp.spesifikasi_id = penerimaan_barang_detail.spesifikasi_id", 'left')
                ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
                ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang_detail.stock_detail_id', null)
                ->where('penerimaan_barang.tanggal >=', '2025-09-01')
                ->where('penerimaan_barang.tanggal <=', '2025-09-30')
                ->where('penerimaan_barang.status_post', "FINISH")
                ->where('penerimaan_barang.company_id', $companyId)
                ->where('bc_purchase_order.deletedAt', null)
                ->where('bc_purchase_order.status_posting', 1);

            $dataQry = $builder->findAll();

            $data_updated = [];
            $data_inserted = [];

            foreach ($dataQry as $d) {
                if (!empty($d['stock_detail_id_revamp'])) {
                    $data_updated[] = [
                        'id' => $d['id'],
                        'stock_detail_id' => $d['stock_detail_id_revamp']
                    ];
                } else {
                    $data_inserted[] = [
                        'id' => $d['id'],
                        'penerimaan_barang_id' => $d['penerimaan_barang_id'],
                        'stock_detail_id' => null
                    ];
                }
            }

            //Untuk Update
            $penerimaanBarangDetailModel->updateBatch($data_updated, 'id');
            // Untuk Create
            $db = db_connect();
            foreach ($data_inserted as $d) {
                $this->insert_stock_pembelian_revamp(
                    $d['penerimaan_barang_id']
                );
            }

            $db->transCommit();
            dd("DONE");
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateStockDetailIdPenerimaanBarangDetailNonPabean()
    {
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $db = \Config\Database::connect();
        $db->transBegin();
        try {

            $companyId = 2;
            $bcType = 0;

            $builder = $penerimaanBarangDetailModel
                ->select("
                    penerimaan_barang_detail.*,
                    penerimaan_barang.divisi_id,
                    penerimaan_barang.warehouse_id,
                    penerimaan_barang.status_penerimaan,
                    penerimaan_barang.tipe_bahan,
                    penerimaan_barang.bc_type,
                    metadata.value as type_bc,
                    stock_revamp_detail.id as stock_detail_id_revamp
                ")
                ->join('penerimaan_barang', "penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id", 'left')
                ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
                ->join('stock_revamp_detail', "stock_revamp_detail.reference_id = penerimaan_barang_detail.penerimaan_barang_id AND stock_revamp_detail.reference_type = 'LPB'", 'left')
                ->join('stock_revamp', "stock_revamp.id = stock_revamp_detail.stock_id 
                             AND stock_revamp.barang_master_id = penerimaan_barang_detail.barang_id 
                             AND stock_revamp.spesifikasi_id = penerimaan_barang_detail.spesifikasi_id", 'left')
                ->where('penerimaan_barang.company_id', $companyId)
                ->where('penerimaan_barang.bc_type', $bcType)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang_detail.stock_detail_id', null)
                ->where('penerimaan_barang.tanggal >=', '2025-09-01')
                ->where('penerimaan_barang.tanggal <=', '2025-09-30')
                ->where('penerimaan_barang.status_post', "FINISH");

            $dataQry = $builder->findAll();

            $data_updated = [];
            $data_inserted = [];

            foreach ($dataQry as $d) {
                if (!empty($d['stock_detail_id_revamp'])) {
                    $data_updated[] = [
                        'id' => $d['id'],
                        'stock_detail_id' => $d['stock_detail_id_revamp']
                    ];
                } else {
                    $data_inserted[] = [
                        'id' => $d['id'],
                        'penerimaan_barang_id' => $d['penerimaan_barang_id'],
                        'stock_detail_id' => null
                    ];
                }
            }

            dd($data_updated, $data_inserted);

            // //Untuk Update
            // $penerimaanBarangDetailModel->updateBatch($data_updated, 'id');
            // // Untuk Create
            // $db = db_connect();
            // foreach ($data_inserted as $d) {
            //     $this->insert_stock_pembelian_revamp(
            //         $d['penerimaan_barang_id']
            //     );
            // }

            $db->transCommit();
            dd("DONE");
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    function insert_stock_pembelian_revamp($penerimaanBarangId)
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $penerimaanBarangModel = new PenerimaanBarangModel();
            $penerimaaanBarangDetailModel = new PenerimaanBarangDetailModel();
            $metaDataModel = new MetadataModel();
            $stockRevampModel = new StockRevampModel();

            $penerimaanBarang = $penerimaanBarangModel->where('id', $penerimaanBarangId)->first();
            $penerimaanBarangList = $penerimaaanBarangDetailModel->where('penerimaan_barang_id', $penerimaanBarangId)->where('deletedAt', null)->findAll();
            $poType = $penerimaanBarang['status_penerimaan'] . " " . $penerimaanBarang['tipe_bahan'];
            $typeBc = $metaDataModel->where('id', $penerimaanBarang['bc_type'])->first();

            // STOK BARANG DIINPUT
            foreach ($penerimaanBarangList as $p) {
                $data = [
                    'company_id' => $penerimaanBarang['company_id'],
                    'barang_master_id' => $p['barang_id'],
                    'spesifikasi_id' => $p['spesifikasi_id'],
                    'unit_id' => $p['unit_konversi'],
                    'divisi_id' => $penerimaanBarang['divisi_id'],
                    'warehouse_id' => $penerimaanBarang['warehouse_id'],
                    'qty_bersih' => $p['jml_masuk_konversi'],
                    'qty_diterima' => $p['jml_masuk_konversi'],
                    'bc_id' => $penerimaanBarang['bc_type'],
                    'type_bc' => $typeBc == null ? "NON PABEAN" : $typeBc['value'],
                    'reference_id' => $penerimaanBarang['id'],
                    'po_type' => $poType,
                    'po_id' => $p['purchase_order_id'],
                    'reference_type' => "LPB",
                    'status' => "IN"
                ];
                $stockDetailId = $stockRevampModel->insertStockRevamp(
                    $db,
                    $data
                );

                $penerimaaanBarangDetailModel->update($p['id'], [
                    'stock_detail_id' => $stockDetailId
                ]);
            }

            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            log_message('error', 'Insert Stock Failed: ' . $e->getMessage());
            return false;
        }
    }


    public function syncNoAjuBc40()
    {
        $bc40Model = new BC40Model();
        $bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $selectQry = "bc_purchase_order.no_aju as no_aju_bc_purchaseorder, bc_40.*";

        $dataQry = $bcPurchaseOrderModel
            ->select($selectQry)
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->where('bc_purchase_order.no_aju IS NOT NULL')
            ->where('bc_purchase_order.deletedAt', null)
            ->where('bc_40.deletedAt', null)
            ->findAll();

        $bc40IdArr = [];
        foreach ($dataQry as $d) {
            if ($d['no_aju_bc_purchaseorder'] != $d['no_aju']) {
                array_push($bc40IdArr, [
                    'id' => $d['id'],
                    'no_aju' => $d['no_aju_bc_purchaseorder'],
                    // 'no_aju_old' => $d['no_aju']
                ]);
            }
        }

        // $bc40Model->updateBatch($bc40IdArr, 'id');
        dd($bc40IdArr);
        die;
    }

    public function compareKaryawanNotSyncFinger()
    {
        $employeeModel = new EmployeesModel();
        $employeeUnitModel = new EmployeesUnitsModel();

        $ip = "192.168.6.30";
        // $companyId = 2;
        // $allEmployee = $employeeModel->where('deletedAt', null)
        //     ->where('company_id', $companyId)
        //     ->findAll();

        // $dataFromFinger = $this->get_data_finger($ip);

        // $idsEmployeeSync = array_column($dataFromFinger, 'id');
        // $idsEmployeeAll = array_column($allEmployee, 'id');

        // $idsBelumSync = [];

        // foreach ($idsEmployeeAll as $idAll) {
        //     if (!in_array($idAll, $idsEmployeeSync)) {
        //         array_push($idsBelumSync, $idAll);
        //     }
        // }

        $dataTest = [
            'id' => '125',
            'name' => "IMAM MA'ARID"
        ];

        $this->add_employee($dataTest['id'], $dataTest['name'], $ip);
        echo "OK";
        die;


        // return response()->setJSON($idsBelumSync);

        // $employeeUnitModel->whereIn('employee_id', $idsBelumSync)->delete();
        // echo "DOne";

        // $dataUpdated = [];
        // foreach ($idsBelumSync as $i) {
        //     array_push($dataUpdated, [
        //         'id' => $i,
        //         'attendance_sync' => 0,
        //     ]);
        // }

        // $employeeModel->updateBatch($dataUpdated, 'id');
        // echo "OK";
        // die;

        // $allEmployeeNotSync = $employeeModel->whereIn('id', $idsBelumSync)->findAll();

        // return response()->setJSON($allEmployeeNotSync);
    }

    public function add_employee($id, $nama, $ip)
    {
        $Connect = fsockopen($ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">" . 0 . "</ArgComKey><Arg><PIN>" . $id . "</PIN><Name>" . $nama . "</Name></Arg></SetUserInfo>";
            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);
            $buffer = "";
            while ($Response = fgets($Connect, 1024)) {
                $buffer = $buffer . $Response;
            }
        } else echo "Koneksi Gagal";

        $buffer = $this->Parse_Data($buffer, "<Information>", "</Information>");
        echo "<B>Result:</B><BR>";
        echo $buffer;
    }

    private function get_all_user($ip)
    {
        $Connect = fsockopen($ip, "80", $errno, $errstr, 10);

        if ($Connect) {
            // SOAP request untuk ambil semua user (karyawan)
            $soap_request = "<GetAllUserInfo>
                            <ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey>
                         </GetAllUserInfo>";

            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);

            $buffer = "";
            while ($Response = fgets($Connect, 1024)) {
                $buffer .= $Response;
            }
        } else {
            echo "Koneksi gagal ke mesin fingerprint ($ip)";
            return [];
        }

        // Ambil isi antara tag GetAllUserInfoResponse
        $buffer = $this->Parse_Data($buffer, "<GetAllUserInfoResponse>", "</GetAllUserInfoResponse>");
        $buffer = explode("\r\n", $buffer);

        $users = [];
        foreach ($buffer as $b) {
            $data = $this->Parse_Data($b, "<Row>", "</Row>");
            $PIN   = $this->Parse_Data($data, "<PIN>", "</PIN>");
            $Name  = $this->Parse_Data($data, "<Name>", "</Name>");
            $PWD   = $this->Parse_Data($data, "<Password>", "</Password>");
            $Role  = $this->Parse_Data($data, "<Privilege>", "</Privilege>");
            $Card  = $this->Parse_Data($data, "<Card>", "</Card>");

            if ($PIN != "") {
                $users[] = [
                    "id" => $PIN,
                    "name" => $Name,
                    "password" => $PWD,
                    "role" => $Role,
                    "card" => $Card,
                ];
            }
        }

        return $users;
    }


    private function get_data_finger($ip)
    {
        $Connect = fsockopen($ip, "80", $errno, $errstr, 1);
        if ($Connect) {
            $soap_request = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">" . 0 . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
            $newLine = "\r\n";
            fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($Connect, "Content-Type: text/xml" . $newLine);
            fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($Connect, $soap_request . $newLine);
            $buffer = "";
            while ($Response = fgets($Connect, 1024)) {
                $buffer = $buffer . $Response;
            }
        } else echo "Koneksi Gagal";

        //include("parse.php");
        $buffer = $this->Parse_Data($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
        $buffer = explode("\r\n", $buffer);
        $arr = [];
        for ($a = 0; $a < count($buffer); $a++) {
            $data = $this->Parse_Data($buffer[$a], "<Row>", "</Row>");
            $PIN = $this->Parse_Data($data, "<PIN>", "</PIN>");
            $DateTime = $this->Parse_Data($data, "<DateTime>", "</DateTime>");
            $Verified = $this->Parse_Data($data, "<Verified>", "</Verified>");
            $Name  = $this->Parse_Data($data, "<Name>", "</Name>");
            $Status = $this->Parse_Data($data, "<Status>", "</Status>");
            if ($PIN != "") {
                array_push($arr, array("id" => $PIN, "date" => $DateTime, "name" => $Name));
            }
        }
        return $arr;
    }

    private function Parse_Data($data, $p1, $p2)
    {
        $data = " " . $data;
        $hasil = "";
        $awal = strpos($data, $p1);
        if ($awal != "") {
            $akhir = strpos($data, $p2, $awal);
            if ($akhir != "") {
                $hasil = substr($data, $awal + strlen($p1), $akhir - ($awal + strlen($p1)));
            }
        }
        return $hasil;
    }

    public function repairHargaDetailPoLokalBahanBaku()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $poBahanBaku = $db->query("
            SELECT * 
            FROM rm_purchase_orders 
            WHERE company_id = 16 
            and po_date >= '2025-09-01' 
            AND po_date <= '2025-12-31' 
            AND deletedAt IS NULL 
            ORDER BY id ASC
        ");

        try {
            $rmPurchaseOrderModel = new RMPurchaseOrderModel();
            $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
            $i = 1;
            foreach ($poBahanBaku->getResult() as $p) {
                $totalFinal = $rmPurchaseOrderModel->generateKomponenHarga($p->id);
                $rmPurchaseOrderDetailModel->updateBatch($totalFinal['rm_purchase_order_detail_nilai'], 'id');
                $i++;
            }

            $db->transCommit();
            echo "<br>" . "Total Update PO : " . $i;
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }

    public function repairTandaTerimaFakturNomor()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $tandaTerimaFakturModel = new TandaTerimaFakturModel();

            $companyId = 15;
            $templeate = "G";

            $dataResult = $tandaTerimaFakturModel->where('company_id', $companyId)
                ->where('deletedAt', null)
                ->findAll();

            $dataUpdate = array();
            foreach ($dataResult as $d) {
                $fakturNoExplode = explode('/', $d['faktur_no']);
                $fakturKeluarNo = explode('/', $d['faktur_keluar_no']);

                $newFakturNo = "$fakturNoExplode[0]/$templeate/$fakturNoExplode[1]/$fakturNoExplode[2]/$fakturNoExplode[3]";
                $newFakturKeluarNo = "$fakturKeluarNo[0]/$templeate/$fakturKeluarNo[1]/$fakturKeluarNo[2]/$fakturKeluarNo[3]";

                array_push($dataUpdate, [
                    'id' => $d['id'],
                    'faktur_no' => $newFakturNo,
                    'faktur_keluar_no' => $newFakturKeluarNo
                ]);
            }

            // return response()->setJSON($dataUpdate);
            // die;

            $tandaTerimaFakturModel->updateBatch($dataUpdate, 'id');

            $db->transCommit();
            echo "OK";
            die;
        } catch (Exception $e) {
            $db->transRollback();
            echo "Error " . $e->getMessage();
        }
    }

    // public function generateFingerFromFingerprint()
    // {
    //     $attendance = new Attendances();
    //     $employeeModel = new EmployeesModel();
    //     $employeeFingerModel = new EmployeesFingerModel();

    //     $ip = "192.168.1.203";
    //     $unit_key = 0;


    //     if (icmpPing($ip, 5)) {
    //         $employeeData = $employeeModel->where('id', 88)->findAll();
    //         foreach ($employeeData as $e) {
    //             $data_finger = $attendance->get_registered_finger(
    //                 $ip,
    //                 $unit_key,
    //                 $e['id']
    //             );
    //             $employeeFingerModel->insert([
    //                 'employees_id' => $e['id'],
    //                 'finger' => $data_finger[0]['data']
    //             ]);
    //         }

    //         dd($data_finger);
    //     } else {
    //         dd("Timeout...");
    //     }
    // }

    // public function get_finger()
    // {
    //     $employeesApi = new Employees();
    //     $userId = 903;
    //     $ip = "192.168.1.203";
    //     $key = 0;
    //     if (icmpPing($ip, 2)) {
    //         $data = $employeesApi->get_finger_by_user(
    //             $userId,
    //             $ip,
    //             $key
    //         );

    //         var_dump($data);
    //         die;
    //     } else {
    //         dd("gagal ping ...");
    //     }
    // }


    public function generateTaxIdTt()
    {
        $pajakTandaTerimaSupplierModel = new PajakTandaTerimaFakturModel();
        $companyId = 16;

        $pajakList = $pajakTandaTerimaSupplierModel->select('pajak_tanda_terima_faktur.*')
            ->join('tanda_terima_faktur', 'tanda_terima_faktur.id = pajak_tanda_terima_faktur.tanda_terima_faktur_id', 'left')
            ->where('company_id', $companyId)
            ->where('pajak_tanda_terima_faktur.deletedAt', null)
            ->findAll();

        $dataResult = array();
        foreach ($pajakList as $p) {
            $taxId = $pajakTandaTerimaSupplierModel->getTaxId(
                $p['tax_type'],
                $companyId
            );
            array_push($dataResult, [
                'id' => $p['id'],
                'tax_id' => $taxId
            ]);
        }

        $pajakTandaTerimaSupplierModel->updateBatch($dataResult, 'id');
        echo "done";
        die;
    }

    public function repairDivisiIdTandaTerima()
    {

        $db = \Config\Database::connect();
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $tandaTerimaSupBB = new TandaTerimaSupBB();
        try {
            $db->transBegin();
            $selectQry = "
                tanda_terima_faktur_detail.*,
                penerimaan_barang.divisi_id
            ";

            $tandaTerimaFakturDetail = $tandaTerimaFakturDetailModel
                ->select($selectQry)
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.id = tanda_terima_faktur_detail.penerimaan_barang_detail_id', 'left')
                ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
                ->where('tanda_terima_faktur_detail.deletedAt', null)
                ->findAll();

            $dataUpdatedDetail = [];
            foreach ($tandaTerimaFakturDetail as $t) {
                array_push($dataUpdatedDetail, [
                    'id' => $t['id'],
                    'divisi_id' => $t['divisi_id']
                ]);
            }

            $tandaTerimaFakturDetailModel->updateBatch($dataUpdatedDetail, 'id');

            $tandaTerimaFaktur = $tandaTerimaFakturModel->where('deletedAt', null)->findAll();
            $dataParent = [];
            foreach ($tandaTerimaFaktur as $t) {
                $tandaTerimaSupBB->updateMultipleDivisi($t['id']);
                array_push($dataParent, [
                    'id' => $t['id'],
                    'divisi_id' => null
                ]);
            }

            $tandaTerimaFakturModel->updateBatch($dataParent, 'id');
            $db->transCommit();
            echo "DONE";
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }
    public function getEmployeeImages()
    {
        $employeeModel = new EmployeesModel();
        $folder = '/media/ardhikayoviyanto/E820-0DF5/photo';

        $images = [];

        // Cek folder valid
        if (is_dir($folder)) {
            $files = scandir($folder);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;

                $path = $folder . '/' . $file;
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $id = pathinfo($file, PATHINFO_FILENAME);

                    // Pastikan file bisa dibaca
                    $fileContents = @file_get_contents($path);
                    if ($fileContents === false) continue;

                    $mime = mime_content_type($path);
                    $base64 = base64_encode($fileContents);

                    $images[] = [
                        'id' => $id,
                        'employee_img' => "data:$mime;base64," . $base64
                    ];
                }
            }
        }

        // Ambil ID foto yang sudah ada di folder
        $idEmployeeImages = array_column($images, 'id');

        // Ambil semua employee yang belum punya foto
        $allEmployee = $employeeModel
            ->where('deletedAt', null)
            ->where('employee_img', null)
            ->findAll();

        $imagesToUpdate = [];

        foreach ($allEmployee as $a) {
            if (in_array($a['id'], $idEmployeeImages)) {
                // Cari data base64 yang sesuai ID
                $key = array_search($a['id'], $idEmployeeImages);
                $imagesToUpdate[] = [
                    'id' => $a['id'],
                    'employee_img' => $images[$key]['employee_img']
                ];
            }
        }

        // dd($imagesToUpdate);

        // Update ke database (jika ada yang cocok)
        // if (!empty($imagesToUpdate)) {
        //     $employeeModel->updateBatch($imagesToUpdate, 'id');
        //     return $this->response->setJSON([
        //         'status' => 'success',
        //         'updated' => count($imagesToUpdate),
        //     ]);
        // }

        return $this->response->setJSON([
            'status' => 'no_updates',
            'message' => 'Tidak ada karyawan yang perlu diupdate',
        ]);
    }

    public function generateDestination()
    {
        $db = \Config\Database::connect();
        $salesOrderExportModel = new SalesOrderExportModel();
        $salesKontrakModel = new SalesKontrakModel();

        try {
            $db->transBegin();
            $salesOrderExport = $salesOrderExportModel
                ->where('deletedAt', null)
                ->findAll();

            $dataList = [];
            foreach ($salesOrderExport as $s) {
                $salesKontrak = $salesKontrakModel->where('id', $s['sales_contract_id'])->first();

                array_push($dataList, [
                    'sales_order_export_id' => $s['sales_order_export_id'],
                    'destination' => $salesKontrak == null ? null : $salesKontrak['dicharge_port']
                ]);
            }

            $salesOrderExportModel->updateBatch($dataList, 'sales_order_export_id');
            $db->transCommit();
            dd("OK");
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }
    public function removeDuplicateInStock()
    {
        $stockRevampDetailModel = new StockRevampDetailModel();
        $stockRevampLogModel = new StockRevampLogModel();
        $db = \Config\Database::connect();

        $db->transBegin();

        try {
            $deletedAt = "2025-11-08 11:26:00";
            // $companyId = 2;

            // Step 1: cari kombinasi stock_id + po_id yang duplikat
            $dupQuery = $stockRevampDetailModel
                ->select('stock_id, po_id, COUNT(id) as total_duplicate')
                ->where('deletedAt', null)
                ->where('reference_type', 'LPB')
                ->groupBy('stock_id, po_id')
                ->having('total_duplicate >', 1)
                ->findAll();

            $deletedCount = 0;
            // dd($dupQuery);

            foreach ($dupQuery as $dup) {
                // Step 2: ambil semua ID dari kombinasi duplikat ini
                $ids = $stockRevampDetailModel
                    ->select('id')
                    ->where('deletedAt', null)
                    ->where('stock_id', $dup['stock_id'])
                    ->where('po_id', $dup['po_id'])
                    ->where('reference_type', 'LPB')
                    ->findAll();

                $detailIds = array_column($ids, 'id');

                if (!empty($detailIds)) {
                    // Step 3: ambil yang ada di penerimaan_barang_detail
                    $relatedIds = $db->table('penerimaan_barang_detail')
                        ->select('stock_detail_id')
                        ->whereIn('stock_detail_id', $detailIds)
                        ->get()
                        ->getResultArray();

                    $relatedIds = array_column($relatedIds, 'stock_detail_id');

                    // Step 4: ambil yang tidak berelasi
                    $toDeleteIds = array_diff($detailIds, $relatedIds);


                    if (!empty($toDeleteIds)) {
                        // Step 5: hapus logis (update deletedAt)
                        $stockRevampDetailModel
                            ->whereIn('id', $toDeleteIds)
                            ->set('deletedAt', $deletedAt)
                            ->update();

                        $stockRevampLogModel
                            ->whereIn('stock_detail_id', $toDeleteIds)
                            ->set('deletedAt', $deletedAt)
                            ->update();

                        $deletedCount += count($toDeleteIds);
                    }
                }
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => 'success',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            return response()->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateDeleted()
    {
        $stockRevampDetailModel = new StockRevampDetailModel();
        $deletedAt = "2025-11-08 11:26:00";

        $stockRevampDetailModel->where('deletedAt', $deletedAt)
            ->set('deletedAt', null)
            ->update();

        dd("DONE");
    }

    public function repairBcPurchaseOrderLpb()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        $deletedAt = "2025-11-09 20:57:00";

        try {
            $bcPurchaseOrderLpbModel = new BCPurchaseOrderLPBModel();

            // Cari duplikat penerimaan_barang_id
            $dupQuery = $bcPurchaseOrderLpbModel
                ->select('penerimaan_barang_id, COUNT(id) as total_duplicate')
                ->where('deletedAt', null)
                ->groupBy('penerimaan_barang_id')
                ->having('total_duplicate >', 1)
                ->findAll();

            $deletedIds = [];

            foreach ($dupQuery as $dup) {
                $penerimaanId = $dup['penerimaan_barang_id'];

                // Ambil semua data duplikat untuk penerimaan_barang_id ini
                $dups = $bcPurchaseOrderLpbModel
                    ->where('penerimaan_barang_id', $penerimaanId)
                    ->where('deletedAt', null)
                    ->orderBy('id', 'ASC')
                    ->findAll();

                foreach ($dups as $row) {
                    $bcPurchaseOrder = $db->table('bc_purchase_order')
                        ->where('id', $row['bc_purchase_order_id'])
                        ->where('deletedAt', null)
                        ->get()
                        ->getRowArray();

                    $shouldDelete = false;

                    if (!$bcPurchaseOrder) {
                        // Data bc_purchase_order sudah tidak ada
                        $shouldDelete = true;
                    } else {
                        // Cek apakah penerimaan_barang_id ada di multiple_lpb_id
                        $multipleLpb = json_decode($bcPurchaseOrder['multiple_lpb_id'] ?? '[]', true);

                        if (!is_array($multipleLpb) || !in_array($penerimaanId, $multipleLpb)) {
                            $shouldDelete = true;
                        }
                    }

                    if ($shouldDelete) {
                        // $bcPurchaseOrderLpbModel->delete($row['id'], true);
                        $deletedIds[] = $row['id'];
                    }
                }
            }

            // purge delete
            // $bcPurchaseOrderLpbModel->where('deletedAt', "2025-11-09 20:57:00")->delete(null, true);

            // $db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Repair selesai',
                'deleted_count' => count($deletedIds),
                'deleted_ids' => $deletedIds,
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateStatusAbsensiPg()
    {
        $formPerjinanModel = new FormPerijinanModel();
        $attendanceModel = new AttendancesModel();
        $statusPotongGaji = "POTONG GAJI_PG";

        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            $formPerizinanArr = array();
            $formPerzinan = $formPerjinanModel->where('status', "IJIN_I")->findAll();

            foreach ($formPerzinan as $f) {
                array_push($formPerizinanArr, [
                    'id' => $f['id'],
                    'status' => $statusPotongGaji
                ]);
            }

            $attendanceUpdateArr = array();
            $attendance = $attendanceModel->where('status', "IJIN_I")->findAll();

            foreach ($attendance as $d) {
                array_push($attendanceUpdateArr, [
                    'id' => $d['id'],
                    'status' => $statusPotongGaji
                ]);
            }

            // $attendanceModel->updateBatch($attendanceUpdateArr, 'id');
            // $formPerjinanModel->updateBatch($formPerizinanArr, 'id');

            // $db->transCommit();

            return response()->setJSON([
                'formPerizinanArr' => $formPerizinanArr,
                'attendanceUpdateArr' => $attendanceUpdateArr,
                'totalFormPerizinanArr' => count($formPerizinanArr),
                'totalAttendanceUpdateArr' => count($attendanceUpdateArr)
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateNonPph()
    {

        // $companyId = 2;
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();

        $dataQry = $rmPurchaseOrderModel
            ->select('rm_purchase_order_details.*')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            // ->where('company_id', $companyId)
            ->where('pph', "None")
            ->where('rm_purchase_orders.deletedAt', null)
            ->where('rm_purchase_order_details.deletedAt', null)
            ->findAll();

        $dataResult = array();

        foreach ($dataQry as $d) {

            array_push($dataResult, $d);
            $totalFinal = $rmPurchaseOrderModel->generateKomponenHarga(
                $d['rm_purchase_order_id']
            );
            // $rmPurchaseOrderDetailModel->updateBatch($totalFinal['rm_purchase_order_detail_nilai'], 'id');
        }

        // return response()->setJSON($dataResult);

        dd($dataResult);
    }

    public function generateUpdateStatusJpk()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $companyId = 2;
            $jamKerjaModel = new JamKerjaModel();

            $selectQry = "
            jam_kerja.*
        ";

            $dataQry = $jamKerjaModel->select($selectQry)
                ->where('company_id', $companyId)
                ->where('deletedAt', null)
                ->like('jenis', 'JAM KERJA CANNING')
                ->findAll();

            $dataResult = [];
            foreach ($dataQry as $d) {
                array_push($dataResult, [
                    'id' => $d['id'],
                    'jenis' => $d['jenis'],
                    'lintas_hari' => 'yes'
                ]);
            }

            $jamKerjaModel->updateBatch($dataResult, 'id');
            $db->transCommit();
            return response()->setJSON($dataResult);
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateStockLog()
    {
        $db = \Config\Database::connect();
        $stockRevampLogModel = new StockRevampLogModel();
        try {
            $db->transBegin();

            $deletedAt = "2025-11-18 00:00:01";

            $selectQry = "stock_revamp_log.*";
            $stockLog = $stockRevampLogModel
                ->select($selectQry)
                ->join('stock_revamp_detail', 'stock_revamp_detail.id = stock_revamp_log.stock_detail_id', 'left')
                ->where('stock_revamp_detail.id IS NULL')
                ->findAll();

            $dataResult = array();
            foreach ($stockLog as $s) {
                array_push($dataResult, [
                    'id' => $s['id'],
                    'deletedAt' => $deletedAt
                ]);
            }

            // $stockRevampLogModel->updateBatch($dataResult, 'id');
            $db->transCommit();
            dd($dataResult);
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function repairKwitansiTb()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
            $rmPurchaseOrderModel = new RMPurchaseOrderModel();

            // $companyId = 2;
            $startDate = "2025-10-01";
            $endDate = "2025-10-31";
            $selectQryDetail = "
            rm_purchase_order_details.rm_purchase_order_id,
            SUM(rm_purchase_order_details.dpp_bulanan) AS total_dpp_bulanan,
            SUM(rm_purchase_order_details.pph_bulanan) AS total_pph_bulanan,
            SUM(rm_purchase_order_details.nilai_total_bulanan) AS total_bulanan
        ";

            $detailPo = $rmPurchaseOrderDetailModel->select($selectQryDetail)
                ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
                ->where('rm_purchase_orders.po_date >=', $startDate)
                ->where('rm_purchase_orders.po_date <=', $endDate)
                ->where('rm_purchase_orders.deletedAt', null)
                ->where('rm_purchase_order_details.deletedAt', null)
                // ->where('rm_purchase_orders.company_id', $companyId)
                ->where('rm_purchase_order_details.nilai_total_bulanan >', 0)
                ->groupBy('rm_purchase_order_details.rm_purchase_order_id')
                ->findAll();

            $dataDetail = [];
            foreach ($detailPo as $d) {
                array_push($dataDetail, [
                    'rm_purchase_order_id' => $d['rm_purchase_order_id'],
                    'dpp_bulanan' => (float)$d['total_dpp_bulanan'],
                    'pph_bulanan' => (float)$d['total_pph_bulanan'],
                    'nilai_total_bulanan' => (float)$d['total_bulanan']
                ]);
            }


            $selectQryParent = "
                rm_purchase_orders.id,
                rm_purchase_orders.po_no,
                suppliers.name AS supplier_name,
                rm_purchase_orders.company_id,
                rm_purchase_orders.dpp_bulanan,
                rm_purchase_orders.pph_bulanan,
                rm_purchase_orders.nilai_total_bulanan
            ";

            $parentPo = $rmPurchaseOrderModel->select($selectQryParent)
                // ->where('rm_purchase_orders.company_id', $companyId)
                ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
                ->where('rm_purchase_orders.po_date >=', $startDate)
                ->where('rm_purchase_orders.po_date <=', $endDate)
                ->where('rm_purchase_orders.deletedAt', null)
                ->where('rm_purchase_orders.nilai_total_bulanan >', 0)
                ->findAll();

            $dataParent = [];
            foreach ($parentPo as $d) {
                array_push($dataParent, [
                    'id' => $d['id'],
                    'po_no' => $d['po_no'],
                    'supplier_name' => $d['supplier_name'],
                    'company_id' => $d['company_id'],
                    'dpp_bulanan' => (float)$d['dpp_bulanan'],
                    'pph_bulanan' => (float)$d['pph_bulanan'],
                    'nilai_total_bulanan' => (float)$d['nilai_total_bulanan']
                ]);
            }

            $dataRepair = array();

            foreach ($dataParent as $parent) {

                foreach ($dataDetail as $detail) {

                    if ($parent['nilai_total_bulanan'] != $detail['nilai_total_bulanan'] && $detail['rm_purchase_order_id'] == $parent['id']) {
                        array_push($dataRepair, [
                            'id' => $detail['rm_purchase_order_id'],
                            'po_no' => $parent['po_no'],
                            // 'supplier_name' => $parent['supplier_name'],
                            'company_id' => $parent['company_id'],
                            'dpp_bulanan' => (float)$detail['dpp_bulanan'],
                            'pph_bulanan' => (float)$detail['pph_bulanan'],
                            'nilai_total_bulanan' => (float)$detail['nilai_total_bulanan']
                        ]);
                    }
                }
            }

            // $rmPurchaseOrderModel->updateBatch($dataRepair, 'id');

            // $db->transCommit();

            return response()->setJSON([
                'totalRepair' => count($dataRepair),
                'dataRepair' => $dataRepair,
                // 'totalDetail' => count($dataDetail),
                // 'totalParent' => count($dataParent),
                // 'dataDetail' => $dataDetail,
                // 'dataParent' => $dataParent,
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateRmPurchaseOrderDetailKosong()
    {
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $companyIdArr = [1, 2, 15];

        $selectQry = "
        rm_purchase_order_details.*,
        rm_purchase_orders.po_no,
        rm_purchase_orders.po_date,
        rm_purchase_orders.company_id
    ";

        $dataResult = $rmPurchaseOrderDetailModel
            ->select($selectQry)
            ->join('rm_purchase_orders', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->where('rm_purchase_order_details.deletedAt IS NULL', null, false)
            ->where('rm_purchase_orders.deletedAt IS NULL', null, false)
            ->where('rm_purchase_orders.po_date >=', "2025-09-01")
            ->where('rm_purchase_orders.po_date <=', "2025-12-31")
            ->whereIn('rm_purchase_orders.company_id', $companyIdArr)
            ->where('rm_purchase_order_details.dpp_umum IS NULL', null, false)
            ->findAll();

        $i = 0;

        // foreach ($dataResult as $d) {
        //     $totalFinal = $rmPurchaseOrderModel->generateKomponenHarga($d['rm_purchase_order_id']);
        //     // dd($totalFinal);
        //     $rmPurchaseOrderDetailModel->updateBatch($totalFinal['rm_purchase_order_detail_nilai'], 'id');
        //     $i++;
        // }
        dd($dataResult);
    }

    public function generateKeteranganFromAttendances()
    {
        $attendanceKeteranganModel = new AttendanceKeteranganModel();
        $attendanceModel = new AttendancesModel();
        $selectQry = "attendances.*";

        $dataQry = $attendanceModel->select($selectQry)
            ->where('deletedAt', null)
            ->groupStart()
            ->where('reason !=', '')
            ->where('reason !=', '-')
            ->groupEnd()
            ->findAll();

        $dataResult = array();
        foreach ($dataQry as $d) {
            array_push($dataResult, [
                'employee_id' => $d['employee_id'],
                'tanggal' => $d['periode'],
                'reason' => $d['reason']
            ]);
        }

        $attendanceKeteranganModel->insertBatch($dataResult);
    }

    public function getPoClosedBug()
    {
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $selectQry = "rm_purchase_orders.*";

        $dataRes = $rmPurchaseOrderModel->select($selectQry)
            ->where('deletedAt', null)
            ->where('is_posted', 1)
            ->where('status_penerimaan', 0)
            ->findAll();

        dd($dataRes);
    }

    public function salahSatuanBarang()
    {
        $spesifikasiId = 9087;
        $unitIdSalah = 42;
        $unitIdBenar = 55;

        $sppDetailModel = new SppDetailModel();
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        // Try di spp detail
        $dataResultSppDetail = array();
        $sppDetail = $sppDetailModel
            ->where('barang2_id', $spesifikasiId)
            ->where('deletedAt', null)
            ->where('unit', $unitIdSalah)
            ->findAll();

        foreach ($sppDetail as $s) {
            array_push($dataResultSppDetail, [
                'id' => $s['id'],
                'unit' => $unitIdBenar
            ]);
        }

        // Try di po
        $dataResultAmPurchaseOrderDetail = array();
        $amPurchaseOrderDetail = $amPurchaseOrderDetailModel
            ->where('unit', $unitIdSalah)
            ->where('spesifikasi_id', $spesifikasiId)
            ->where('deletedAt', null)
            ->findAll();

        foreach ($amPurchaseOrderDetail as $a) {
            array_push($dataResultAmPurchaseOrderDetail, [
                'id' => $a['id'],
                'unit' => $unitIdBenar
            ]);
        }

        // Try di penerimaan barang
        $dataResultPenerimaanBarang = array();
        $penerimaanBarangDetail = $penerimaanBarangDetailModel
            ->where('spesifikasi_id', $spesifikasiId)
            ->where('unit', $unitIdSalah)
            ->where('deletedAt', null)
            ->findAll();

        foreach ($penerimaanBarangDetail as $p) {
            array_push($dataResultPenerimaanBarang, [
                'id' => $p['id'],
                'penerimaan_barang_id' => $p['penerimaan_barang_id'],
                'unit' => $unitIdBenar
            ]);
        }

        // $sppDetailModel->updateBatch($dataResultSppDetail, 'id');
        // $amPurchaseOrderDetailModel->updateBatch($dataResultAmPurchaseOrderDetail, 'id');
        // $penerimaanBarangDetailModel->updateBatch($dataResultPenerimaanBarang, 'id');

        return response()->setJSON([
            'po_detail' => $dataResultAmPurchaseOrderDetail,
            'pb_detail' => $dataResultPenerimaanBarang,
            'dataResultSppDetail' => $dataResultSppDetail
        ]);
    }

    public function updateConsigne()
    {
        $salesOrderExportModel = new SalesOrderExportModel();
        $selectQry = "
            sales_order_export.*,
            customers.name AS customer_name
        ";

        $dataQry = $salesOrderExportModel->select($selectQry)
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->findAll();

        $dataUpdated = [];
        foreach ($dataQry as $d) {
            array_push($dataUpdated, [
                'sales_order_export_id' => $d['sales_order_export_id'],
                'consigne' => $d['customer_name']
            ]);
        }

        $salesOrderExportModel->updateBatch($dataUpdated, 'sales_order_export_id');
        dd("ok");
    }

    public function generateSizeBreakdown()
    {
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $salesContractSizeBreakdownModel = new SalesContractSizeBreakdownModel();

        $dataResultSalesOrderDetail = $salesOrderExportDetailModel->where('deletedAt', null)->findAll();
        $dataUpdate = array();
        foreach ($dataResultSalesOrderDetail as $s) {
            $salesContractSize = $salesContractSizeBreakdownModel->where('id', $s['sales_contract_size_breakdown_id'])->first();
            if ($salesContractSize != null) {
                array_push($dataUpdate, [
                    'sales_order_export_detail_id' => $s['sales_order_export_detail_id'],
                    'size' => $salesContractSize['size'],
                    'grade' => $salesContractSize['grade'],
                    'packing' => $salesContractSize['packing'],
                    'can' => $salesContractSize['can'],
                    'case' => $salesContractSize['cased'],
                    'kg' => $salesContractSize['kg'],
                    'lb' => $salesContractSize['lb'],
                    'inner_box' => $salesContractSize['inner_box'],
                    'pc' => $salesContractSize['pc'],
                    'bag' => $salesContractSize['bag'],
                    'persen' => $salesContractSize['persen'],
                    'palet' => $salesContractSize['palet']
                ]);
            }
        }

        $salesOrderExportDetailModel->updateBatch($dataUpdate, 'sales_order_export_detail_id');
        dd("done");
    }

    public function repairStockMutasi()
    {
        $db = \Config\Database::connect();

        $subQuery = $db->table('mutasi_detail')
            ->select('stock_detail_id, MAX(createdAt) as last_created')
            ->where('deletedAt', null)
            ->groupBy('stock_detail_id')
            ->getCompiledSelect();

        $dataResult = $db->table('mutasi_detail md')
            ->select('md.*')
            ->join(
                "($subQuery) last",
                'last.stock_detail_id = md.stock_detail_id 
             AND last.last_created = md.createdAt'
            )
            ->join('mutasi m', 'm.id = md.mutasi_id', 'left')
            ->where('m.status_posting', 1)
            ->where('md.deletedAt', null)
            ->get()
            ->getResultArray();

        $dataUpdated = [];
        foreach ($dataResult as $d) {
            array_push($dataUpdated, [
                'id' => $d['stock_detail_id'],
                'qty_bersih' => (float)$d['hasil_mutasi'],
                'qty_diterima' => (float)$d['hasil_mutasi'],
            ]);
        }

        $stockRevampDetailModel = new StockRevampDetailModel();
        $stockRevampDetailModel->updateBatch($dataUpdated, 'id');
        dd("done");
    }

    public function stockBpView()
    {
        return view('tes');
    }

    public function stockBpAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $barangMasterModel = new BarangMasterModel();
            $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
            $companyId = 2;

            $file = $this->request->getFile('file');
            if (!$file->isValid()) {
                throw new \RuntimeException(
                    $file->getErrorString() . '(' . $file->getError() . ')'
                );
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $worksheet   = $spreadsheet->getActiveSheet();

            // =========================
            // 1. Ambil data non-kosong
            // =========================
            $data = [];
            foreach ($worksheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $value = trim((string) $cell->getValue());
                    if ($value !== '') {
                        $rowData[] = $value;
                    }
                }

                if (!empty($rowData)) {
                    $data[] = $rowData;
                }
            }

            // =========================
            // 2. Mapping data barang
            // =========================
            $dataBarang = [];
            foreach ($data as $row) {
                $dataBarang[] = [
                    'barang_name'       => trim($row[0] ?? ''),
                    'spesifikasi'       => trim($row[1] ?? ''),
                    'barang_master_id'  => null,
                    'spesifikasi_id'    => null,
                    'qty' => $row[3]
                ];
            }

            // =========================
            // 3. Cari relasi master & spesifikasi
            // =========================
            foreach ($dataBarang as $index => $d) {

                if ($d['barang_name'] === '') {
                    continue;
                }

                $barangMaster = $barangMasterModel
                    ->where('company_id', $companyId)
                    ->where('barang_name', $d['barang_name'])
                    ->first();

                if (!$barangMaster) {
                    continue;
                }

                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel
                    ->where('barang_master_id', $barangMaster['id'])
                    ->where('spesifikasi', $d['spesifikasi'])
                    ->first();

                if ($barangMasterSpesifikasi) {
                    $dataBarang[$index]['barang_master_id']
                        = $barangMasterSpesifikasi['barang_master_id'];

                    $dataBarang[$index]['spesifikasi_id']
                        = $barangMasterSpesifikasi['id'];
                }
            }


            // =========================
            // DEBUG
            // =========================
            dd($dataBarang);

            // $db->transCommit();

        } catch (\Throwable $e) {
            $db->transRollback();
            dd($e->getMessage());
        }
    }

    public function generateJamKerjaAll()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $companyIdAsal   = 2;
            $companyIdTujuan = 1;
            //-------------------
            $divisiIdAsal    = 22;
            $divisiIdTujuan  = 45;

            $jamKerjaModel       = new JamKerjaModel();
            $jamKerjaDetailModel = new JamKerjaDetailModel();

            /* ================= AMBIL JAM KERJA ASAL ================= */
            $jamKerjaAsal = $jamKerjaModel
                ->where([
                    'company_id' => $companyIdAsal,
                    'divisi_id'  => $divisiIdAsal,
                    'deletedAt'  => null,
                ])
                ->findAll();

            if (empty($jamKerjaAsal)) {
                throw new \Exception('Data jam kerja asal kosong');
            }

            /* ================= AMBIL SEMUA DETAIL SEKALIGUS ================= */
            $jamKerjaIds = array_column($jamKerjaAsal, 'id');

            $detailAsal = $jamKerjaDetailModel
                ->whereIn('jam_kerja_id', $jamKerjaIds)
                ->where('deletedAt', null)
                ->findAll();

            /* ================= GROUP DETAIL BY JAM_KERJA_ID ================= */
            $detailMap = [];
            foreach ($detailAsal as $d) {
                $detailMap[$d['jam_kerja_id']][] = $d;
            }

            /* ================= COPY DATA ================= */
            foreach ($jamKerjaAsal as $j) {

                // insert jam kerja baru
                $newJamKerjaId = $jamKerjaModel->insert([
                    'company_id'    => $companyIdTujuan,
                    'divisi_id'     => $divisiIdTujuan,
                    'shift'         => $j['shift'],
                    'jenis'         => $j['jenis'],
                    'jam_terlambat' => $j['jam_terlambat'],
                    'lintas_hari'   => $j['lintas_hari'],
                ]);

                if (!$newJamKerjaId) {
                    throw new \Exception('Gagal insert jam kerja');
                }

                // insert detail
                foreach ($detailMap[$j['id']] ?? [] as $det) {
                    $jamKerjaDetailModel->insert([
                        'jam_kerja_id'             => $newJamKerjaId,
                        'hari'                     => $det['hari'],
                        'jam_masuk'                => $det['jam_masuk'],
                        'jam_istirahat_mulai'      => $det['jam_istirahat_mulai'],
                        'jam_istirahat_selesai'    => $det['jam_istirahat_selesai'],
                        'jam_pulang'               => $det['jam_pulang'],
                    ]);
                }
            }

            $db->transCommit();
            return response()->setJSON(['status' => 'success', 'message' => 'Generate jam kerja selesai']);
        } catch (\Throwable $e) {
            $db->transRollback();
            return response()->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }




    // public function getAllUserFinger()
    // {
    //     $ip = "b79e1fc8c155.ngrok-free.app";
    //     $attendance = new Attendances();

    //     $all_user = $attendance->get_all_user_https($ip, 0);
    //     var_dump($all_user);
    //     die;
    // }

    // public function insertFinger()
    // {
    //     $ip = "b79e1fc8c155.ngrok-free.app";
    //     $attendance = new Attendances();

    //     $all_user = $attendance->add_employee_https(2, "test123", $ip, 0);
    // }
}




// [
//     "BAHAN PENOLONG",
//     "PACKAGING",
//     "GP-SLID-LK-002",
//     "-",
//     "PTS",
//     "K7",
//     "BC 4.0",
//     605296,
//     192,
//     "2024-08-02",
//     "PT CAKRA ANUGRAH PERKASA"
// ]