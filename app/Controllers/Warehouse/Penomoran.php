<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\CountryModel;
use App\Models\DivisisModel;
use App\Models\JurnalUmumModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\StockDetailModel;
use App\Models\TransaksiJurnalModel;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
class Penomoran extends BaseController
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
            WHERE company_id = 1 
            and po_date >= '2025-03-01' 
            AND po_date <= '2025-03-31' 
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
        $companyId = 1;
        $condition = [
            'DATE(tanggal_transaksi) >=' => '2025-03-21',
            'DATE(tanggal_transaksi) <=' => '2025-03-31'
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
            // $poLokalBahanPenolong = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_po_bp,am_purchase_orders.po_no')
            //     ->join('transaksi_pembelian', 'transaksi_jurnal.id = transaksi_pembelian.id_transaksi_jurnal', 'left')
            //     ->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left')
            //     ->where('transaksi_jurnal.deleted_at', null)
            //     ->where('transaksi_pembelian.id_po_bp IS NOT NULL')
            //     ->where('am_purchase_orders.company_id', $companyId)
            //     ->where('am_purchase_orders.is_posted', 1)
            //     ->where('am_purchase_orders.po_type', "Lokal")
            //     ->where($condition)
            //     ->findAll();

            // foreach ($poLokalBahanPenolong as $p) {
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

            //     $keteranganJurnal = $amPurchaseOrderDetailModel->getSpesifikasiBarangAsString($p['id_po_bp']);

            //     foreach ($jurnalUmum as $j) {
            //         $jurnalUmumModel->update($j['id'], [
            //             'valas' => 30,
            //             'kurs' => 1,
            //             'keterangan' => $keteranganJurnal
            //         ]);
            //     }

            //     $total++;
            // }

            // IMPORT BAHAN PENOLONG
            $poLokalBahanPenolong = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_po_bp,am_purchase_orders.po_no')
                ->join('transaksi_pembelian', 'transaksi_jurnal.id = transaksi_pembelian.id_transaksi_jurnal', 'left')
                ->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left')
                ->where('transaksi_jurnal.deleted_at', null)
                ->where('transaksi_pembelian.id_po_bp IS NOT NULL')
                ->where('am_purchase_orders.company_id', $companyId)
                ->where('am_purchase_orders.is_posted', 1)
                ->where('am_purchase_orders.po_type', "Import")
                ->findAll();

            foreach ($poLokalBahanPenolong as $p) {
                // Update Transaksi Jurnal
                $transaksiJurnalModel->update($p['id'], [
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
                        'keterangan' => $keteranganJurnal
                    ]);
                }

                $total++;
            }
            // IMPORT BAHAN BAKU
            $poImportBahanBaku = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_import_bb,rm_import_pos.po_no')
                ->join('transaksi_pembelian', 'transaksi_jurnal.id = transaksi_pembelian.id_transaksi_jurnal', 'left')
                ->join('rm_import_pos', 'rm_import_pos.id = transaksi_pembelian.id_import_bb', 'left')
                ->where('transaksi_jurnal.deleted_at', null)
                ->where('transaksi_pembelian.id_import_bb IS NOT NULL')
                ->where('rm_import_pos.company_id', $companyId)
                ->where('rm_import_pos.is_posted', 1)
                ->findAll();

            foreach ($poImportBahanBaku as $p) {
                // Update Transaksi Jurnal
                $transaksiJurnalModel->update($p['id'], [
                    'uraian_transaksi' => $p['po_no']
                ]);
                // Update Jurnal Umum
                $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
                    ->where('company_id', $companyId)
                    ->where('deletedAt', null)
                    ->findAll();

                $keteranganJurnal = $rmImportPoDetailModel->getSpesifikasiBarangAsString($p['id_import_bb']);

                foreach ($jurnalUmum as $j) {
                    $jurnalUmumModel->update($j['id'], [
                        'keterangan' => $keteranganJurnal
                    ]);
                }

                $total++;
            }

            $db->transCommit();

            var_dump("Total Updated Journal", $total);
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
            // $poLokalBahanBaku = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_local_bb,rm_purchase_orders.po_no,rm_purchase_orders.total')
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
            //         'uraian_transaksi' => $p['po_no'],
            //         'total_debit' => $p['total'],
            //         'total_kredit' => $p['total']
            //     ]);
            //     // Update Jurnal Umum
            //     $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
            //         ->where('company_id', $companyId)
            //         ->where('deletedAt', null)
            //         ->findAll();

            //     $keteranganJurnal = $rmPurchaseOrderDetailModel->getSpesifikasiBarangAsString($p['id_local_bb']);
            //     $totalAkun = count($jurnalUmum) - 1;

            //     foreach ($jurnalUmum as $j) {
            //         $lastJurnalUmum = $jurnalUmumModel->find($j['id']);
            //         $jurnalUmumModel->update($j['id'], [
            //             'valas' => 30,
            //             'kurs' => 1,
            //             'keterangan' => $keteranganJurnal,
            //             'debit' => $lastJurnalUmum['debit'] == 0 ? 0 : round($p['total'] / $totalAkun, 0),
            //             'kredit' => $p['total']
            //         ]);
            //     }

            //     $total++;
            // }

            // // LOKAL BAHAN PENOLONG
            $poLokalBahanPenolong = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_po_bp,am_purchase_orders.po_no,am_purchase_orders.total')
                ->join('transaksi_pembelian', 'transaksi_jurnal.id = transaksi_pembelian.id_transaksi_jurnal', 'left')
                ->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left')
                ->where('transaksi_jurnal.deleted_at', null)
                ->where('transaksi_pembelian.id_po_bp IS NOT NULL')
                ->where('am_purchase_orders.company_id', $companyId)
                ->where('am_purchase_orders.is_posted', 1)
                ->where('am_purchase_orders.po_type', "Lokal")
                ->where($condition)
                ->findAll();

            foreach ($poLokalBahanPenolong as $p) {
                // Update Transaksi Jurnal
                $transaksiJurnalModel->update($p['id'], [
                    'valas' => "IDR",
                    'valas_id' => 30,
                    'exchange_rate' => 1,
                    'uraian_transaksi' => $p['po_no'],
                    'total_debit' => $p['total'],
                    'total_kredit' => $p['total']
                ]);
                // Update Jurnal Umum
                $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
                    ->where('company_id', $companyId)
                    ->where('deletedAt', null)
                    ->findAll();

                $keteranganJurnal = $amPurchaseOrderDetailModel->getSpesifikasiBarangAsString($p['id_po_bp']);
                $totalAkun = count($jurnalUmum) - 1;

                foreach ($jurnalUmum as $j) {
                    $lastJurnalUmum = $jurnalUmumModel->find($j['id']);

                    $jurnalUmumModel->update($j['id'], [
                        'valas' => 30,
                        'kurs' => 1,
                        'keterangan' => $keteranganJurnal,
                        'debit' => $lastJurnalUmum['debit'] == 0 ? 0 : round($p['total'] / $totalAkun, 0),
                        'kredit' => $p['total']
                    ]);
                }

                $total++;
            }

            // // IMPORT BAHAN PENOLONG
            // $poLokalBahanPenolong = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_po_bp,am_purchase_orders.po_no,am_purchase_orders.total')
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
            //         'uraian_transaksi' => $p['po_no'],
            //         'total_debit' => $p['total'],
            //         'total_kredit' => $p['total']
            //     ]);
            //     // Update Jurnal Umum
            //     $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
            //         ->where('company_id', $companyId)
            //         ->where('deletedAt', null)
            //         ->findAll();
            //     $totalAkun = count($jurnalUmum) - 1;

            //     $keteranganJurnal = $amPurchaseOrderDetailModel->getSpesifikasiBarangAsString($p['id_po_bp']);

            //     foreach ($jurnalUmum as $j) {
            //         $lastJurnalUmum = $jurnalUmumModel->find($j['id']);

            //         $jurnalUmumModel->update($j['id'], [
            //             'keterangan' => $keteranganJurnal,
            //             'debit' => $lastJurnalUmum['debit'] == 0 ? 0 : round($p['total'] / $totalAkun, 0),
            //             'kredit' => $p['total']
            //         ]);
            //     }

            //     $total++;
            // }
            // IMPORT BAHAN BAKU
            // $poImportBahanBaku = $transaksiJurnalModel->select('transaksi_jurnal.*,transaksi_pembelian.id_import_bb,rm_import_pos.po_no,rm_import_pos.total')
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
            //         'uraian_transaksi' => $p['po_no'],
            //         'total_debit' => $p['total'],
            //         'total_kredit' => $p['total']
            //     ]);
            //     // Update Jurnal Umum
            //     $jurnalUmum = $jurnalUmumModel->where('id_transaksi', $p['id'])
            //         ->where('company_id', $companyId)
            //         ->where('deletedAt', null)
            //         ->findAll();

            //     $keteranganJurnal = $rmImportPoDetailModel->getSpesifikasiBarangAsString($p['id_import_bb']);
            //     $totalAkun = count($jurnalUmum) - 1;

            //     foreach ($jurnalUmum as $j) {
            //         $jurnalUmumModel->update($j['id'], [
            //             'keterangan' => $keteranganJurnal,
            //             'debit' => $lastJurnalUmum['debit'] == 0 ? 0 : round($p['total'] / $totalAkun, 0),
            //             'kredit' => $p['total']
            //         ]);
            //     }

            //     $total++;
            // }

            $db->transCommit();

            var_dump("Total Updated Journal", $total);
            die;
        } catch (Exception $e) {
            $db->transRollback();
            \var_dump("Error ", $e->getMessage(), $e->getLine());
        }
    }
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