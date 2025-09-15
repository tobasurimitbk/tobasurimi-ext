<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC40Model;
use App\Models\BCPurchaseOrderModel;
use App\Models\CountryModel;
use App\Models\DivisisModel;
use App\Models\JamKerjaDetailModel;
use App\Models\JamKerjaModel;
use App\Models\JurnalUmumModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\SupplierModel;
use App\Models\TransaksiJurnalModel;
use DateTime;
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

    public function repairHargaTerakhirMasterBarang()
    {

        $companyId = 16;
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
                $companyId
            );


            if ($hargaTerakhir) {
                $updatedData[] = [
                    'id' => $hargaTerakhir['spesifikasi_id'],
                    'supplier_terakhir' => $hargaTerakhir['supplier_id'],
                    'harga_terakhir' => $hargaTerakhir['price']
                ];
            }
        }

        if (!empty($updatedData)) {
            $barangMasterSpesifikasiModel->updateBatch($updatedData, 'id');
        }

        echo json_encode($updatedData, JSON_PRETTY_PRINT);
        die;
        echo "OOK";
        die;
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