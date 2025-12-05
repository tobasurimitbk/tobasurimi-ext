<?php

namespace App\Controllers\Accounting\JurnalUmum;

use App\Controllers\BaseController;
use App\Models\AccountBarangModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\JurnalUmumModel;
use App\Models\MetadataModel;
use App\Models\SaldoAwalBarangModel;
use App\Models\Sub_AkunsModel;
use App\Models\TransaksiJurnalModel;
use App\Models\WarehousesModel;

use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class insertNilaiBarang extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $this_company;
    protected $this_user_id;

    protected $divisiModel;
    protected $warehouseModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $saldoAwalBarangModel;
    protected $transaksiJurnalModel;
    protected $jurnalUmumModel;
    protected $metadataModel;
    protected $subAkunModel;
    protected $accountBarangModel;

    protected $db;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_company = session()->get("login")->this_company;
        $this->this_user_id = session()->get("login")->user_id;

        $this->divisiModel = new DivisisModel;
        $this->warehouseModel = new WarehousesModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->saldoAwalBarangModel = new SaldoAwalBarangModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->metadataModel = new MetadataModel();
        $this->subAkunModel = new Sub_AkunsModel();
        $this->accountBarangModel = new AccountBarangModel();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $metaDataModel = new MetadataModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $subAkunsModel = $Sub_AkunsModel->getAPAR($this->this_company_id);

        $data = [
            'kategoriBarangAkun' => $metaDataModel->asObject()->where('name', 'kategori_barang_akun')->findAll(),
            "subAkuns" => $subAkunsModel,
        ];
        return view('Accounting/jurnalUmum/indexInsert', $data);
    }

    public function dropdownDivisi()
    {
        $dataDivisi = $this->divisiModel->get_by_company_id($this->this_company_id);

        $data = [
            "data" => $dataDivisi
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownWarehouse()
    {
        $divisiID = $this->request->getVar('department_id');
        $dataWarehouse = $this->warehouseModel->get_by_divisi_id($this->this_company_id, $divisiID);

        $data = [
            "data" => $dataWarehouse
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarang()
    {
        $typeBarang = $this->request->getVar('type_barang');
        $dataBarang = $this->barangMasterModel->getBarangByTypeCondition($this->this_company_id, $typeBarang);

        $data = [
            "data" => $dataBarang
        ];

        echo json_encode($data);
        return;
    }

    public function getData()
    {
        $payload = [
            "pageSize"          => $this->request->getVar("length"),
            "currentPage"       => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort"              => $this->request->getVar("sort"),
            "sorttype"          => $this->request->getVar("sortType"),
            "search"            => $this->request->getVar("search"),
        ];

        $addCondition = [
            "sort"              => $this->request->getVar("sort"),
            "sortType"          => $this->request->getVar("sortType"),
            "kode_department"   => $this->request->getVar("kode_department"),
            "type_barang"       => $this->request->getVar('type_barang'),
            "kode_barang"       => $this->request->getVar("kode_barang"),
            "transaksi_date"    => $this->request->getVar("transaksi_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("transaksi_date")))) : date("Y-m-d"),
            "company_id"        => $this->this_company_id,
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            'barang_master_spesifikasi.deletedAt' => null,
        ];

        $dataQry = $this->barangMasterSpesifikasiModel->getListBarangSpesifikasiWithAccount($condition, $addCondition, $limit, $offset);

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataQry['data'],
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        $db->transBegin(); // === START TRANSACTION ===

        try {

            $companyId = $this->this_company_id;
            $tanggalTransaksi = $this->request->getVar('tanggal_transaksi')
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_transaksi"))))
                : date("Y-m-d");

            $divisiId = $this->request->getVar('divisi_id_saldo_barang');
            $barangMasterSpesifikasiId = $this->request->getVar('barang_id_saldo_barang');
            $barangMasterId = $this->barangMasterSpesifikasiModel
                ->where('id', $barangMasterSpesifikasiId)
                ->first()['barang_master_id'];

            $akunPembelianId = $this->request->getVar('akun_ap_id_saldo_barang');
            $saldoPembelian   = $this->request->getVar('saldo_akun_pembelian');

            $akunPenjualanId = $this->request->getVar('akun_ar_id_saldo_barang');
            $saldoPenjualan   = $this->request->getVar('saldo_akun_penjualan');

            $akunPemakaianId = $this->request->getVar('akun_pemakaian_id_saldo_barang');
            $saldoPemakaian   = $this->request->getVar('saldo_akun_pemakaian');

            // === INSERT / UPDATE: PEMBELIAN ===
            $idPembelian = $this->_insertOrUpdateSaldo(
                $companyId,
                $divisiId,
                $barangMasterId,
                $barangMasterSpesifikasiId,
                $tanggalTransaksi,
                $akunPembelianId,
                $saldoPembelian
            );

            // === INSERT / UPDATE: PENJUALAN ===
            $idPenjualan = $this->_insertOrUpdateSaldo(
                $companyId,
                $divisiId,
                $barangMasterId,
                $barangMasterSpesifikasiId,
                $tanggalTransaksi,
                $akunPenjualanId,
                $saldoPenjualan
            );

            // === INSERT / UPDATE: PEMAKAIAN ===
            $idPemakaian = $this->_insertOrUpdateSaldo(
                $companyId,
                $divisiId,
                $barangMasterId,
                $barangMasterSpesifikasiId,
                $tanggalTransaksi,
                $akunPemakaianId,
                $saldoPemakaian
            );

            // === INSERT / UPDATE: PEMBELIAN ===
            $this->_insertTransaksiJurnal(
                $companyId,
                $divisiId,
                $barangMasterId,
                $barangMasterSpesifikasiId,
                $tanggalTransaksi,
                $idPembelian,
                $akunPembelianId,
                $saldoPembelian,
                $idPenjualan,
                $akunPenjualanId,
                $saldoPenjualan,
                $idPemakaian,
                $akunPemakaianId,
                $saldoPemakaian
            );

            // === COMMIT ===
            if ($db->transStatus() === false) {
                $db->transRollback();
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false,
                    'message' => "Gagal menyimpan saldo barang (DB error)"
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Saldo Barang Berhasil Ditambahkan"
            ]);
        } catch (\Throwable $th) {

            // === FORCE ROLLBACK JIKA ADA ERROR PHP ===
            $db->transRollback();

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Terjadi error: " . $th->getMessage()
            ]);
        }
    }

    private function _insertOrUpdateSaldo(
        $companyId,
        $divisiId,
        $barangMasterId,
        $barangMasterSpesifikasiId,
        $tanggalTransaksi,
        $coaId,
        $saldo
    ) {
        $existing = $this->saldoAwalBarangModel
            ->where('company_id', $companyId)
            ->where('divisi_id', $divisiId)
            ->where('barang_master_spesifikasi_id', $barangMasterSpesifikasiId)
            ->where('tanggal_transaksi', $tanggalTransaksi)
            ->where('coa_id', $coaId)
            ->where('deletedAt', NULL)
            ->first();

        if ($existing) {
            return $this->saldoAwalBarangModel->update($existing['id'], [
                'saldo_awal' => $saldo
            ]);
        }

        return $this->saldoAwalBarangModel->insert([
            'tanggal_transaksi' => $tanggalTransaksi,
            'company_id'        => $companyId,
            'divisi_id'         => $divisiId,
            'barang_master_id'  => $barangMasterId,
            'barang_master_spesifikasi_id' => $barangMasterSpesifikasiId,
            'coa_id'            => $coaId,
            'saldo_awal'        => $saldo,
        ]);
    }

    private function _insertTransaksiJurnal(
        $companyId,
        $divisiId,
        $barangMasterId,
        $barangMasterSpesifikasiId,
        $tanggalTransaksi,
        $idPembelian,
        $coaIdPembelian,
        $saldoPembelian,
        $idPenjualan,
        $coaIdPenjualan,
        $saldoPenjualan,
        $idPemakaian,
        $coaIdPemakaian,
        $saldoPemakaian
    ) {
        $dataMetadataTipeTransaksi = $this->metadataModel
            ->asObject()
            ->where('value', 'SALDO AWAL')
            ->where('name', 'tipe_transaksi')
            ->first();

        $no_transaksi_jurnal = $this->transaksiJurnalModel->getNoTransaksiLast($dataMetadataTipeTransaksi->description);
        $totalSaldo = $saldoPembelian + $saldoPenjualan + $saldoPemakaian;

        $id = $this->transaksiJurnalModel->insert([
            'no_transaksi' => $no_transaksi_jurnal,
            'tanggal_transaksi' => $tanggalTransaksi,
            'type_transaksi' => $dataMetadataTipeTransaksi->id,
            'uraian_transaksi' => 'SALDO AWAL BARANG ' . $tanggalTransaksi,
            'total_debit' => $totalSaldo,
            'total_kredit' => $totalSaldo,
            'metode_input' => "manual",
            'no_bukti' => $no_transaksi_jurnal,
            'valas' => 'IDR',
            'valas_id' => 30,
            'exchange_rate' => 1.00
        ]);

        $this->jurnalUmumModel->insert([
            'id_transaksi' => $id,
            'id_coa' => $coaIdPembelian,
            'company_id' => $companyId,
            'divisi_id' => $divisiId,
            'tanggal_jurnal' => $tanggalTransaksi,
            'debit' => $saldoPembelian,
            'kredit' => 0,
            'valas' => 30,
            'kurs' => 1.00,
            'keterangan' => 'SALDO AWAL BARANG ' . $tanggalTransaksi,
            'id_inputer' => $this->this_user_id,
            'reference_id' => $idPembelian,
            'reference_type' => 'SALDO AWAL',
        ]);

        $this->jurnalUmumModel->insert([
            'id_transaksi' => $id,
            'id_coa' => $coaIdPenjualan,
            'company_id' => $companyId,
            'divisi_id' => $divisiId,
            'tanggal_jurnal' => $tanggalTransaksi,
            'debit' => $saldoPenjualan,
            'kredit' => 0,
            'valas' => 30,
            'kurs' => 1.00,
            'keterangan' => 'SALDO AWAL BARANG ' . $tanggalTransaksi,
            'id_inputer' => $this->this_user_id,
            'reference_id' => $idPenjualan,
            'reference_type' => 'SALDO AWAL',
        ]);

        $this->jurnalUmumModel->insert([
            'id_transaksi' => $id,
            'id_coa' => $coaIdPemakaian,
            'company_id' => $companyId,
            'divisi_id' => $divisiId,
            'tanggal_jurnal' => $tanggalTransaksi,
            'debit' => $saldoPemakaian,
            'kredit' => 0,
            'valas' => 30,
            'kurs' => 1.00,
            'keterangan' => 'SALDO AWAL BARANG ' . $tanggalTransaksi,
            'id_inputer' => $this->this_user_id,
            'reference_id' => $idPemakaian,
            'reference_type' => 'SALDO AWAL',
        ]);
    }

    public function templateNilaiBarang()
    {
        $companyId = $this->this_company_id;

        // Ambil data divisi sesuai company
        $divisi = $this->divisiModel
            ->where('company_id', $companyId)
            ->findAll();

        // Ambil barang master spesifikasi (pakai query yang sudah Anda punya)
        $barangSpesifikasi = $this->barangMasterSpesifikasiModel
            ->select('barang_master_spesifikasi.id, barang_master.type_barang, barang_master.kode_barang, barang_master.barang_name, barang_master_spesifikasi.spesifikasi')
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
            ->where('company_id', $companyId)
            ->findAll();

        // Ambil data sub akun
        $subAkun = $this->subAkunModel
            ->where('company_id', $companyId)
            ->findAll();

        // Mulai Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        /* =============================
        1. SHEET TEMPLATE
        ============================= */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('TEMPLATE');

        // HEADER
        $sheet->setCellValue('A1', 'Tanggal Transaksi');
        $sheet->setCellValue('B1', 'ID Divisi');
        $sheet->setCellValue('C1', 'ID Spesifikasi Barang');
        $sheet->setCellValue('D1', 'ID Coa Pembelian/Persediaan');
        $sheet->setCellValue('E1', 'Saldo Awal Barang Pembelian/Persediaan');
        $sheet->setCellValue('F1', 'ID Coa Penjualan');
        $sheet->setCellValue('G1', 'Saldo Awal Barang Penjualan');
        $sheet->setCellValue('H1', 'ID Coa Pemakaian');
        $sheet->setCellValue('I1', 'Saldo Awal Barang Pemakaian');

        // Bold header
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', date('d/m/Y'));
        $sheet->setCellValue('B2', '1');
        $sheet->setCellValue('C2', '1');
        $sheet->setCellValue('D2', '1');
        $sheet->setCellValue('E2', '2000000');
        $sheet->setCellValue('F2', '2');
        $sheet->setCellValue('G2', '3000000');
        $sheet->setCellValue('H2', '3');
        $sheet->setCellValue('I2', '4000000');

        /* =============================
        2. SHEET DIVISI
        ============================= */
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('DEPARTMENT');

        $sheet2->setCellValue('A1', 'ID');
        $sheet2->setCellValue('B1', 'Nama Department');
        $sheet2->getStyle('A1:C1')->getFont()->setBold(true);

        $row = 2;
        foreach ($divisi as $d) {
            $sheet2->setCellValue("A{$row}", $d['id']);
            $sheet2->setCellValue("B{$row}", $d['divisi']);
            $row++;
        }

        /* =============================
        3. SHEET BARANG SPESIFIKASI
        ============================= */
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('SPESIFIKASI BARANG');

        $sheet3->setCellValue('A1', 'ID');
        $sheet3->setCellValue('B1', 'Type Barang');
        $sheet3->setCellValue('C1', 'Kode Barang');
        $sheet3->setCellValue('D1', 'Nama Barang');
        $sheet3->setCellValue('E1', 'Spesifikasi');
        $sheet3->getStyle('A1:E1')->getFont()->setBold(true);

        $row = 2;
        foreach ($barangSpesifikasi as $bs) {
            $sheet3->setCellValue("A{$row}", $bs['id']);
            $sheet3->setCellValue("B{$row}", $bs['type_barang']);
            $sheet3->setCellValue("C{$row}", $bs['kode_barang']);
            $sheet3->setCellValue("D{$row}", $bs['barang_name']);
            $sheet3->setCellValue("E{$row}", $bs['spesifikasi']);
            $row++;
        }

        /* =============================
        4. SHEET AKUN COA
        ============================= */
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Akun COA');

        $sheet4->setCellValue('A1', 'ID');
        $sheet4->setCellValue('B1', 'Kode Akun');
        $sheet4->setCellValue('C1', 'Nama Akun');
        $sheet4->getStyle('A1:C1')->getFont()->setBold(true);

        $row = 2;
        foreach ($subAkun as $bs) {
            $sheet4->setCellValue("A{$row}", $bs['id']);
            $sheet4->setCellValue("B{$row}", $bs['no_sub']);
            $sheet4->setCellValue("C{$row}", $bs['nama_sub']);
            $row++;
        }

        /* =============================
        OUTPUT FILE
        ============================= */
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'TEMPLATE_NILAI_BARANG_' . date('Ymd_His') . '.xlsx';

        return response()
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment;filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody($writer->save('php://output'));
    }

    public function importExcel()
    {
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return response()->setJSON([
                'status' => false,
                'message' => 'File tidak valid'
            ]);
        }

        if ($file->getClientExtension() !== 'xlsx') {
            return response()->setJSON([
                'status' => false,
                'message' => 'Format file harus .xlsx'
            ]);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getSheetByName('TEMPLATE');

        if (!$sheet) {
            return response()->setJSON([
                'status' => false,
                'message' => 'Sheet TEMPLATE tidak ditemukan'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $companyId = $this->this_company_id;
            $highestRow = $sheet->getHighestRow();

            // === kumpulkan jurnal detail di sini (untuk batch insert) ===
            $journalItems = [];

            for ($row = 2; $row <= $highestRow; $row++) {

                $tanggalXls     = $sheet->getCell("A{$row}")->getValue();
                $divisiId       = $sheet->getCell("B{$row}")->getValue();
                $spesifikasiId  = $sheet->getCell("C{$row}")->getValue();

                $coaPembelian   = $sheet->getCell("D{$row}")->getValue();
                $saldoPembelian = floatval($sheet->getCell("E{$row}")->getValue());

                $coaPenjualan   = $sheet->getCell("F{$row}")->getValue();
                $saldoPenjualan = floatval($sheet->getCell("G{$row}")->getValue());

                $coaPemakaian   = $sheet->getCell("H{$row}")->getValue();
                $saldoPemakaian = floatval($sheet->getCell("I{$row}")->getValue());

                if (!$tanggalXls || !$divisiId || !$spesifikasiId) {
                    continue;
                }

                // =======================
                //  PARSE TANGGAL
                // =======================
                $tanggalRaw = trim((string)$tanggalXls);

                if (is_numeric($tanggalRaw)) {
                    $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$tanggalRaw);
                    $tanggal = $dt->format('Y-m-d');
                } else {
                    $parsed = false;
                    $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'm/d/Y', 'm-d-Y'];

                    foreach ($formats as $fmt) {
                        $d = \DateTime::createFromFormat($fmt, $tanggalRaw);
                        if ($d && $d->format($fmt) === $tanggalRaw) {
                            $tanggal = $d->format('Y-m-d');
                            $parsed = true;
                            break;
                        }
                    }

                    if (!$parsed) {
                        $ts = @strtotime($tanggalRaw);
                        if ($ts !== false && $ts > 0) {
                            $tanggal = date('Y-m-d', $ts);
                            $parsed = true;
                        }
                    }

                    if (!$parsed) {
                        throw new \Exception("Format tanggal tidak dikenali di baris {$row}: {$tanggalRaw}");
                    }
                }

                // =======================
                //  Ambil barang_master_id
                // =======================
                $barangSpesifikasi = $this->barangMasterSpesifikasiModel
                    ->where('id', $spesifikasiId)
                    ->first();

                if (!$barangSpesifikasi) {
                    throw new \Exception("ID Spesifikasi {$spesifikasiId} tidak valid (baris {$row})");
                }

                $barangMasterId = $barangSpesifikasi['barang_master_id'];

                // =======================
                //  Cek data mapping COA
                // =======================
                $this->_ensureAccountBarangCoa(
                    $companyId,
                    $divisiId,
                    $barangMasterId,
                    $spesifikasiId,
                    $coaPembelian,
                    $coaPenjualan,
                    $coaPemakaian
                );

                // =======================
                //  Insert/Update saldo
                // =======================
                $idPembelian = $this->_insertOrUpdateSaldo(
                    $companyId,
                    $divisiId,
                    $barangMasterId,
                    $spesifikasiId,
                    $tanggal,
                    $coaPembelian,
                    $saldoPembelian
                );

                $idPenjualan = $this->_insertOrUpdateSaldo(
                    $companyId,
                    $divisiId,
                    $barangMasterId,
                    $spesifikasiId,
                    $tanggal,
                    $coaPenjualan,
                    $saldoPenjualan
                );

                $idPemakaian = $this->_insertOrUpdateSaldo(
                    $companyId,
                    $divisiId,
                    $barangMasterId,
                    $spesifikasiId,
                    $tanggal,
                    $coaPemakaian,
                    $saldoPemakaian
                );

                // =======================
                //  SIMPAN DETAIL JURNAL (batch)
                // =======================
                if ($saldoPembelian > 0) {
                    $journalItems[] = [
                        "tanggal"        => $tanggal,
                        "divisi_id"      => $divisiId,
                        "spesifikasi_id" => $spesifikasiId,
                        "barang_id"      => $barangMasterId,
                        "coa"            => $coaPembelian,
                        "debit"          => $saldoPembelian,
                        "kredit"         => 0,
                        "ref_id"         => $idPembelian,
                        "type"           => "PEMBELIAN"
                    ];
                }

                if ($saldoPenjualan > 0) {
                    $journalItems[] = [
                        "tanggal"        => $tanggal,
                        "divisi_id"      => $divisiId,
                        "spesifikasi_id" => $spesifikasiId,
                        "barang_id"      => $barangMasterId,
                        "coa"            => $coaPenjualan,
                        "debit"          => 0,
                        "kredit"         => $saldoPenjualan,
                        "ref_id"         => $idPenjualan,
                        "type"           => "PENJUALAN"
                    ];
                }

                if ($saldoPemakaian > 0) {
                    $journalItems[] = [
                        "tanggal"        => $tanggal,
                        "divisi_id"      => $divisiId,
                        "spesifikasi_id" => $spesifikasiId,
                        "barang_id"      => $barangMasterId,
                        "coa"            => $coaPemakaian,
                        "debit"          => 0,
                        "kredit"         => $saldoPemakaian,
                        "ref_id"         => $idPemakaian,
                        "type"           => "PEMAKAIAN"
                    ];
                }
            }

            // ==========================================================
            // INSERT SEMUA JURNAL DALAM 1 TRANSAKSI
            // ==========================================================
            $this->_insertTransaksiJurnalBatch($companyId, $journalItems);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return response()->setJSON([
                    'status' => false,
                    'message' => 'DB Error — gagal import'
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'message' => 'Import Excel Berhasil'
            ]);
        } catch (\Throwable $e) {

            $db->transRollback();

            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function _ensureAccountBarangCoa(
        $companyId,
        $divisiId,
        $barangMasterId,
        $spesifikasiId,
        $coaPembelian,
        $coaPenjualan,
        $coaPemakaian
    ) {
        $model = $this->accountBarangModel;

        // Cek existing
        $data = $model->where('company_id', $companyId)
            ->where('divisi_id', $divisiId)
            ->where('barang_master_id', $barangMasterId)
            ->where('barang_master_spesifikasi_id', $spesifikasiId)
            ->where('deleted_at', null)
            ->first();

        // Jika belum ada → Insert baru langsung lengkap
        if (!$data) {
            return $model->insert([
                'company_id' => $companyId,
                'divisi_id' => $divisiId,
                'barang_master_id' => $barangMasterId,
                'barang_master_spesifikasi_id' => $spesifikasiId,
                'ap_id' => $coaPembelian ?: null,
                'ar_id' => $coaPenjualan ?: null,
                'pemakaian_id' => $coaPemakaian ?: null,
                'kategori_id' => null,
            ]);
        }

        // Jika sudah ada → Update COA yang masih kosong
        $updateData = [];

        if (empty($data['ap_id']) && !empty($coaPembelian)) {
            $updateData['ap_id'] = $coaPembelian;
        }
        if (empty($data['ar_id']) && !empty($coaPenjualan)) {
            $updateData['ar_id'] = $coaPenjualan;
        }
        if (empty($data['pemakaian_id']) && !empty($coaPemakaian)) {
            $updateData['pemakaian_id'] = $coaPemakaian;
        }

        if (!empty($updateData)) {
            $model->update($data['id'], $updateData);
        }

        return $data['id'];
    }

    private function _insertTransaksiJurnalBatch($companyId, $journalItems)
    {
        if (empty($journalItems)) {
            return; // tidak ada jurnal, skip
        }

        // ============================
        // Ambil metadata SALDO AWAL
        // ============================
        $dataMetadataTipeTransaksi = $this->metadataModel
            ->asObject()
            ->where('value', 'SALDO AWAL')
            ->where('name', 'tipe_transaksi')
            ->first();

        $no_transaksi_jurnal = $this->transaksiJurnalModel
            ->getNoTransaksiLast($dataMetadataTipeTransaksi->description);

        // ============================
        // Hitung total debit & kredit
        // ============================
        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($journalItems as $j) {
            $totalDebit  += floatval($j["debit"]);
            $totalKredit += floatval($j["kredit"]);
        }

        // ============================
        // Buat transaksi jurnal parent
        // ============================
        $idTransaksi = $this->transaksiJurnalModel->insert([
            'no_transaksi'      => $no_transaksi_jurnal,
            'tanggal_transaksi' => $journalItems[0]["tanggal"], // tanggal pertama
            'type_transaksi'    => $dataMetadataTipeTransaksi->id,
            'uraian_transaksi'  => 'SALDO AWAL BARANG ' . $journalItems[0]["tanggal"],
            'total_debit'       => $totalDebit,
            'total_kredit'      => $totalKredit,
            'metode_input'      => "manual",
            'no_bukti'          => $no_transaksi_jurnal,
            'valas'             => 'IDR',
            'valas_id'          => 30,
            'exchange_rate'     => 1.00
        ]);

        // ============================
        // Siapkan batch insert anak (jurnal_umum)
        // ============================
        $batch = [];
        foreach ($journalItems as $j) {
            $batch[] = [
                'id_transaksi'  => $idTransaksi,
                'id_coa'        => $j["coa"],
                'company_id'    => $companyId,
                'divisi_id'     => $j["divisi_id"],
                'tanggal_jurnal' => $j["tanggal"],
                'debit'         => $j["debit"],
                'kredit'        => $j["kredit"],
                'valas'         => 30,
                'kurs'          => 1.00,
                'keterangan'    => 'SALDO AWAL BARANG ' . $j["tanggal"],
                'id_inputer'    => $this->this_user_id,
                'reference_id'  => $j["ref_id"],
                'reference_type' => 'SALDO AWAL',
            ];
        }

        // ============================
        // INSERT BATCH KE jurnal_umum
        // ============================
        if (!empty($batch)) {
            $this->jurnalUmumModel->insertBatch($batch);
        }

        return $idTransaksi;
    }
}
