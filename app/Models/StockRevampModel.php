<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Model;

class StockRevampModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stock_revamp';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    //insertStock($db, [
    //     'company_id'        => 1,
    //     'barang_master_id'  => 10,
    //     'spesifikasi_id'    => 5,
    //     'unit_id'           => 2,
    //     'divisi_id'         => 3,
    //     'warehouse_id'      => 1,
    //     'qty_bersih'        => 50,
    //     'qty_diterima'      => 50,
    //     'bc_id'             => 100,
    //     'type_bc'           => 'BC 40',
    //     'reference_id'      => 200,
    //     'po_type'           => 'IMPORT BB',
    //     'po_id'             => 300,
    //     'reference_type'    => 'LPB',
    //     'status'            => 'IN',
    //     'stock_detail_asal' => 100,
    //     'keterangan'        => 'keterangan value'
    // ]);
    public function insertStockRevamp(BaseConnection $db, array $data)
    {
        $db->transBegin();

        try {
            // ==============================
            // 1. Cek stock_revamp (master stok)
            // ==============================
            $builder = $db->table('stock_revamp');
            $exist = $builder->where([
                'company_id'      => $data['company_id'],
                'barang_master_id' => $data['barang_master_id'],
                'spesifikasi_id'  => $data['spesifikasi_id'],
                'unit_id'         => $data['unit_id'],
                'divisi_id'       => $data['divisi_id'],
                'warehouse_id'    => $data['warehouse_id'],
                'deletedAt'       => null,
            ])->get()->getRow();

            if ($exist) {
                // update stok
                $builder->where('id', $exist->id)->update([
                    'qty_bersih'   => $exist->qty_bersih + $data['qty_bersih'],
                    'qty_diterima' => $exist->qty_diterima + $data['qty_diterima'],
                    'updatedAt'    => date('Y-m-d H:i:s'),
                ]);
                $stockId = $exist->id;
            } else {
                // insert stok baru
                $builder->insert([
                    'company_id'      => $data['company_id'],
                    'barang_master_id' => $data['barang_master_id'],
                    'spesifikasi_id'  => $data['spesifikasi_id'],
                    'unit_id'         => $data['unit_id'],
                    'divisi_id'       => $data['divisi_id'],
                    'warehouse_id'    => $data['warehouse_id'],
                    'qty_bersih'      => $data['qty_bersih'],
                    'qty_diterima'    => $data['qty_diterima'],
                    'createdAt'       => date('Y-m-d H:i:s'),
                    'updatedAt'       => date('Y-m-d H:i:s'),
                ]);
                $stockId = $db->insertID();
            }

            if (!empty($data['stock_detail_result_id'])) {
                // ✅ UPDATE jika stock_detail_result_id sudah ada
                $db->table('stock_revamp_detail')
                    ->where('id', $data['stock_detail_result_id'])
                    ->where('deletedAt', null)
                    ->update([
                        'stock_id'       => $stockId,
                        'bc_id'          => $data['bc_id'] ?? null,
                        'type_bc'        => $data['type_bc'] ?? null,
                        'reference_id'   => $data['reference_id'] ?? null,
                        'po_type'        => $data['po_type'] ?? null,
                        'po_id'          => $data['po_id'] ?? null,
                        'reference_type' => $data['reference_type'] ?? null,
                        'qty_bersih'     => $data['qty_bersih'],
                        'qty_diterima'   => $data['qty_diterima'],
                        'updatedAt'      => date('Y-m-d H:i:s'),
                    ]);

                $stockDetailId = $data['stock_detail_result_id'];
            } else {
                // ✅ INSERT baru jika belum ada stock_detail_result_id
                $db->table('stock_revamp_detail')->insert([
                    'stock_id'       => $stockId,
                    'bc_id'          => $data['bc_id'] ?? null,
                    'type_bc'        => $data['type_bc'] ?? null,
                    'reference_id'   => $data['reference_id'] ?? null,
                    'po_type'        => $data['po_type'] ?? null,
                    'po_id'          => $data['po_id'] ?? null,
                    'reference_type' => $data['reference_type'] ?? null,
                    'qty_bersih'     => $data['qty_bersih'],
                    'qty_diterima'   => $data['qty_diterima'],
                    'createdAt'      => date('Y-m-d H:i:s'),
                    'updatedAt'      => date('Y-m-d H:i:s'),
                ]);

                $stockDetailId = $db->insertID();
            }


            // ==============================
            // 3. Insert ke stock_revamp_log
            // ==============================
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id' => $stockDetailId,
                'status'         => $data['status'] ?? 'IN',
                'qty_diterima'   => $data['qty_diterima'],
                'qty_bersih'     => $data['qty_bersih'],
                'keterangan'     => $data['keterangan'] ?? null,
                'createdAt'      => date('Y-m-d H:i:s'),
                'updatedAt'      => date('Y-m-d H:i:s'),
            ]);

            // =========================================================
            // 4. Insert ke stock_revamp_history (ga usah pas pembelian)
            // =========================================================
            if (!empty($data['stock_detail_asal'])) {
                $db->table('stock_revamp_history')->insert([
                    'stock_detail_asal'    => $data['stock_detail_asal'], // bisa diisi kalau ada asal
                    'stock_detail_akhir'   => $stockDetailId,
                    'qty_bersih_asal'      => $data['qty_bersih_asal'],
                    'qty_diterima_asal'    => $data['qty_diterima_asal'],
                    'qty_bersih_akhir'     => $data['qty_bersih'],
                    'qty_diterima_akhir'   => $data['qty_diterima'],
                    'createdAt'            => date('Y-m-d H:i:s'),
                    'updatedAt'            => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transCommit();

            return $stockDetailId;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Insert Stock Failed: ' . $e->getMessage());
            return false;
        }
    }

    public function insertStockRevampJasaVendorIn(BaseConnection $db, array $data)
    {
        $db->transBegin();

        try {
            // ==============================
            // 1. Cek stock_revamp (master stok)
            // ==============================
            $builder = $db->table('stock_revamp');
            $exist = $builder->where([
                'company_id'      => $data['company_id'],
                'barang_master_id' => $data['barang_master_id'],
                'spesifikasi_id'  => $data['spesifikasi_id'],
                'unit_id'         => $data['unit_id'],
                'divisi_id'       => $data['divisi_id'],
                'warehouse_id'    => $data['warehouse_id'],
                'deletedAt'       => null,
            ])->get()->getRow();

            if ($exist) {
                // update stok
                $builder->where('id', $exist->id)->update([
                    'qty_bersih'   => $exist->qty_bersih + $data['qty_bersih'],
                    'qty_diterima' => $exist->qty_diterima + $data['qty_diterima'],
                    'updatedAt'    => date('Y-m-d H:i:s'),
                ]);
                $stockId = $exist->id;
            } else {
                // insert stok baru
                $builder->insert([
                    'company_id'      => $data['company_id'],
                    'barang_master_id' => $data['barang_master_id'],
                    'spesifikasi_id'  => $data['spesifikasi_id'],
                    'unit_id'         => $data['unit_id'],
                    'divisi_id'       => $data['divisi_id'],
                    'warehouse_id'    => $data['warehouse_id'],
                    'qty_bersih'      => $data['qty_bersih'],
                    'qty_diterima'    => $data['qty_diterima'],
                    'createdAt'       => date('Y-m-d H:i:s'),
                    'updatedAt'       => date('Y-m-d H:i:s'),
                ]);
                $stockId = $db->insertID();
            }

            // ==============================
            // 2. Insert ke stock_revamp_detail
            // ==============================
            $db->table('stock_revamp_detail')->insert([
                'stock_id'       => $stockId,
                'bc_id'          => $data['bc_id'] ?? null,
                'type_bc'        => $data['type_bc'] ?? null,
                'reference_id'   => $data['reference_id'] ?? null,
                'po_type'        => $data['po_type'] ?? null,
                'reference_type' => $data['reference_type'] ?? null,
                'keterangan'      => $data['keterangan'],
                'qty_bersih'     => $data['qty_bersih'],
                'qty_diterima'   => $data['qty_diterima'],
                'createdAt'      => date('Y-m-d H:i:s'),
                'updatedAt'      => date('Y-m-d H:i:s'),
            ]);
            $stockDetailId = $db->insertID();

            // ==============================
            // 3. Insert ke stock_revamp_log
            // ==============================
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id' => $stockDetailId,
                'status'         => 'IN',
                'qty_diterima'   => $data['qty_diterima'],
                'qty_bersih'     => $data['qty_bersih'],
                'createdAt'      => date('Y-m-d H:i:s'),
                'updatedAt'      => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();

            return $stockDetailId;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Insert Stock Failed: ' . $e->getMessage());
            return false;
        }
    }

    //outStockRevamp($db, [
    //     'stock_detail_id'   => 1,
    //     'qty_digunakan'     => 10,
    //     'keterangan'        => 5,
    //     'reference_tujuan_id' => 2,
    //     'reference_tujuan_type' => enum,
    // ]);

    public function outStockRevamp(BaseConnection $db, array $data)
    {
        // HAPUS transBegin() dari model, karena sudah dihandle controller
        try {
            // ==============================
            // 1. Ambil data detail dulu
            // ==============================
            $stockDetail = $db->table('stock_revamp_detail')
                ->where('id', $data['stock_detail_id'])
                ->get()
                ->getRowArray();

            if (!$stockDetail) {
                throw new \Exception("Stock detail tidak ditemukan");
            }

            // ==============================
            // 2. Hitung qty detail baru (dikurangi)
            // ==============================
            $newQtyDetail       = $stockDetail['qty_diterima'] - $data['qty_digunakan'];
            $newQtyDetailBersih = max(0, $stockDetail['qty_bersih'] - $data['qty_digunakan']);

            $db->table('stock_revamp_detail')
                ->where('id', $data['stock_detail_id'])
                ->update([
                    'qty_diterima' => $newQtyDetail,
                    'qty_bersih'   => $newQtyDetailBersih,
                ]);

            // ==============================
            // 3. Ambil data parent stock_revamp
            // ==============================
            $stock = $db->table('stock_revamp')
                ->where('id', $stockDetail['stock_id'])
                ->get()
                ->getRowArray();

            if (!$stock) {
                throw new \Exception("Stock parent tidak ditemukan");
            }

            // ==============================
            // 4. Hitung qty parent baru (dikurangi)
            // ==============================
            $newQtyParent       = $stock['qty_diterima'] - $data['qty_digunakan'];
            $newQtyParentBersih = $stock['qty_bersih'] - $data['qty_digunakan'];

            $db->table('stock_revamp')
                ->where('id', $stockDetail['stock_id'])
                ->update([
                    'qty_diterima' => $newQtyParent,
                    'qty_bersih'   => $newQtyParentBersih,
                ]);

            // $db->table('stock_revamp_history')->insert([
            //     'stock_detail_asal'    => $data['stock_detail_id'],
            //     'stock_detail_akhir'   => $data['stock_detail_id'],
            //     'qty_bersih_asal'      => $data['qty_digunakan'],
            //     'qty_diterima_asal'    => $data['qty_digunakan'],
            //     'qty_bersih_akhir'     => $data['qty_digunakan'], // hasil rumus
            //     'qty_diterima_akhir'   => $data['qty_digunakan'], // bisa disamakan kalau proporsional
            //     'tanggal'         => date('Y-m-d'),
            //     'createdAt'            => date('Y-m-d H:i:s'),
            //     'updatedAt'            => date('Y-m-d H:i:s'),
            // ]);

            // ==============================
            // 5. Insert ke log
            // ==============================
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id'     => $stockDetail['id'],
                'status'              => 'OUT',
                'qty_diterima'        => $data['qty_digunakan'],
                'qty_bersih'          => $data['qty_digunakan'],
                'keterangan'          => $data['keterangan'] ?? null,
                'reference_tujuan_id' => $data['reference_tujuan_id'] ?? null,
                'reference_tujuan_type' => $data['reference_tujuan_type'] ?? null,
                'createdAt'           => date('Y-m-d H:i:s'),
                'updatedAt'           => date('Y-m-d H:i:s'),
            ]);

            return $stockDetail['id'];
        } catch (\Throwable $e) {
            // HAPUS transRollback() dari model
            log_message('error', 'Out Stock Failed: ' . $e->getMessage());
            throw $e; // Lempar exception ke controller
        }
    }

    public function outStockRevampWithoutQtyBersih(BaseConnection $db, array $data)
    {
        try {
            // ==============================
            // 1. Ambil data detail
            // ==============================
            $stockDetail = $db->table('stock_revamp_detail')
                ->where('id', $data['stock_detail_id'])
                ->get()
                ->getRowArray();

            if (!$stockDetail) {
                throw new \Exception("Stock detail tidak ditemukan");
            }

            // ==============================
            // 2. Hitung qty_diterima baru (ONLY THIS)
            // ==============================
            $newQtyDetail = $stockDetail['qty_diterima'] - $data['qty_digunakan'];

            // Tidak menyentuh qty_bersih

            $db->table('stock_revamp_detail')
                ->where('id', $data['stock_detail_id'])
                ->update([
                    'qty_diterima' => $newQtyDetail,
                    // 'qty_bersih' tidak di-update!
                ]);

            // ==============================
            // 3. Ambil data parent stock_revamp
            // ==============================
            $stock = $db->table('stock_revamp')
                ->where('id', $stockDetail['stock_id'])
                ->get()
                ->getRowArray();

            if (!$stock) {
                throw new \Exception("Stock parent tidak ditemukan");
            }

            // ==============================
            // 4. Hitung qty_diterima parent baru
            // ==============================
            $newQtyParent = $stock['qty_diterima'] - $data['qty_digunakan'];

            // Tidak menyentuh qty_bersih

            $db->table('stock_revamp')
                ->where('id', $stockDetail['stock_id'])
                ->update([
                    'qty_diterima' => $newQtyParent,
                    // 'qty_bersih' tidak di-update!
                ]);

            // ==============================
            // 5. Insert LOG
            // ==============================
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id'       => $stockDetail['id'],
                'status'                => 'OUT',
                'qty_diterima'          => $data['qty_digunakan'],
                'qty_bersih'            => $data['qty_digunakan'], // log boleh isi tapi tidak memengaruhi stock
                'keterangan'            => $data['keterangan'] ?? null,
                'reference_tujuan_id'   => $data['reference_tujuan_id'] ?? null,
                'reference_tujuan_type' => $data['reference_tujuan_type'] ?? null,
                'createdAt'             => date('Y-m-d H:i:s'),
                'updatedAt'             => date('Y-m-d H:i:s'),
            ]);

            return $stockDetail['id'];
        } catch (\Throwable $e) {
            log_message('error', 'Out Stock Failed: ' . $e->getMessage());
            throw $e;
        }
    }


    public function unpostStockRevamp(BaseConnection $db, array $data)
    {
        try {
            $db->transStart();

            $asalId   = $data['stock_detail_asal'];
            $akhirId  = $data['stock_detail_akhir'];
            $qtyAsal  = (float)($data['qty_diterima_asal'] ?? 0);
            $qtyAkhir = (float)($data['qty_diterima_akhir'] ?? 0);

            // ==============================
            // 1. Ambil data detail asal & akhir
            // ==============================
            $detailAsal  = $db->table('stock_revamp_detail')->where('id', $asalId)->get()->getRowArray();
            $detailAkhir = $db->table('stock_revamp_detail')->where('id', $akhirId)->get()->getRowArray();

            if (!$detailAsal || !$detailAkhir)
                throw new \Exception("Stock detail asal/akhir tidak ditemukan");

            // ==============================
            // 2. Ambil parent stock
            // ==============================
            $parentAsal  = $db->table('stock_revamp')->where('id', $detailAsal['stock_id'])->get()->getRowArray();
            $parentAkhir = $db->table('stock_revamp')->where('id', $detailAkhir['stock_id'])->get()->getRowArray();

            if (!$parentAsal || !$parentAkhir)
                throw new \Exception("Parent stock tidak ditemukan");

            // ==============================
            // 3. Rollback parent
            // ==============================
            $db->table('stock_revamp')->where('id', $parentAsal['id'])->update([
                'qty_diterima' => $parentAsal['qty_diterima'] + $qtyAsal,
                'qty_bersih'   => $parentAsal['qty_bersih'] + $qtyAsal,
            ]);

            $db->table('stock_revamp')->where('id', $parentAkhir['id'])->update([
                'qty_diterima' => $parentAkhir['qty_diterima'] - $qtyAkhir,
                'qty_bersih'   => $parentAkhir['qty_bersih'] - $qtyAkhir,
            ]);

            // ==============================
            // 4. Update detail asal & akhir
            // ==============================
            $db->table('stock_revamp_detail')->where('id', $asalId)->update([
                'qty_diterima' => $detailAsal['qty_diterima'] + $qtyAsal,
                'qty_bersih'   => $detailAsal['qty_bersih'] + $qtyAsal,
            ]);

            // 🔹 Hitung nilai baru dulu sebelum update
            $newQtyDiterimaAkhir = $detailAkhir['qty_diterima'] - $qtyAkhir;
            $newQtyBersihAkhir   = $detailAkhir['qty_bersih'] - $qtyAkhir;

            // 🔹 Lalu update menggunakan variable
            $db->table('stock_revamp_detail')
                ->where('id', $akhirId)
                ->update([
                    'qty_diterima' => $newQtyDiterimaAkhir,
                    'qty_bersih'   => $newQtyBersihAkhir,
                ]);


            // ==============================
            // 5. Hapus history akhir
            // ==============================
            $db->table('stock_revamp_history')->where('stock_detail_akhir', $akhirId)->delete();

            // ==============================
            // 6. Catat log
            // ==============================
            // Barang hasil revamp keluar
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id' => $akhirId,
                'status'          => 'OUT',
                'qty_diterima'    => $qtyAkhir,
                'qty_bersih'      => $qtyAkhir,
                'keterangan'      => $data['keterangan'] ?? 'UNPOST STOCK REVAMP (OUT)',
                'no_dokumen'      => $data['no_dokumen'] ?? null,
                'createdAt'       => date('Y-m-d H:i:s'),
                'updatedAt'       => date('Y-m-d H:i:s'),
            ]);

            // Barang asal dikembalikan
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id' => $asalId,
                'status'          => 'IN',
                'qty_diterima'    => $qtyAsal,
                'qty_bersih'      => $qtyAsal,
                'keterangan'      => 'ROLLBACK dari UNPOST STOCK REVAMP',
                'no_dokumen'      => $data['no_dokumen'] ?? null,
                'createdAt'       => date('Y-m-d H:i:s'),
                'updatedAt'       => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();
            return true;
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Unpost Stock Revamp Failed: ' . $e->getMessage());
            throw $e;
        }
    }




    //            CAUTIONNN!!!!
    // 2 FUNGSI DI BAWAH INI KHUSUS JASA VENDOR

    public function unpostStockKeluar(BaseConnection $db, array $data)
    {
        try {
            $asalId  = $data['stock_detail_asal'];
            $qtyAsal = $data['qty_diterima_asal'];

            // ==============================
            // 1. Ambil stock detail asal
            // ==============================
            $detailAsal = $db->table('stock_revamp_detail')
                ->where('id', $asalId)
                ->get()
                ->getRowArray();

            // ==============================
            // 3. Ambil parent stock
            // ==============================
            $parentAsal = $db->table('stock_revamp')
                ->where('id', $detailAsal['stock_id'])
                ->get()
                ->getRowArray();

            if (!$parentAsal) {
                throw new \Exception("Parent stock tidak ditemukan");
            }

            // ==============================
            // 4. Kembalikan qty ke parent & detail asal
            // ==============================
            $db->table('stock_revamp')
                ->where('id', $parentAsal['id'])
                ->update([
                    'qty_diterima' => $parentAsal['qty_diterima'] + $qtyAsal,
                    'qty_bersih'   => $parentAsal['qty_bersih'] + $qtyAsal,
                ]);


            // 🔹 Hapus history lama berdasarkan stock_detail_asal
            $db->table('stock_revamp_history')
                ->where('stock_detail_asal', $asalId)
                ->delete();

            $db->table('stock_revamp_detail')
                ->where('id', $asalId)
                ->update([
                    'qty_diterima' => $detailAsal['qty_diterima'] + $qtyAsal,
                    'qty_bersih'   => $detailAsal['qty_bersih'] + $qtyAsal,
                ]);

            // ==============================
            // 5. Insert log UNPOST
            // ==============================
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id' => $asalId,
                'status'          => 'IN',
                'keterangan'      => $data['keterangan'] ?? 'UNPOST STOCK KELUAR',
                'no_dokumen'      => $data['no_dokumen'] ?? null,
                'qty_diterima'    => $qtyAsal,
                'qty_bersih'      => $qtyAsal,
                'createdAt'       => date('Y-m-d H:i:s'),
                'updatedAt'       => date('Y-m-d H:i:s'),
            ]);

            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Unpost Stock Keluar Failed: ' . $e->getMessage());
            throw $e; // Lempar ke controller
        }
    }

    public function unpostStockMasuk(BaseConnection $db, array $data)
    {
        try {
            $akhirId = $data['stock_detail_akhir']; // id stock detail IN yang mau di-unpost
            $qtyIn   = $data['qty_diterima_akhir'] ?? 0;
            $keterangan = $data['keterangan'] ?? 'UNPOST STOCK IN';
            $noDokumen  = $data['no_dokumen'] ?? null;

            // ==============================
            // 1. Ambil stock detail akhir
            // ==============================
            $detailAkhir = $db->table('stock_revamp_detail')
                ->where('id', $akhirId)
                ->get()
                ->getRowArray();

            if (!$detailAkhir) {
                throw new \Exception("Stock detail IN tidak ditemukan");
            }

            // ==============================
            // 2. Ambil parent stock
            // ==============================
            $parentAkhir = $db->table('stock_revamp')
                ->where('id', $detailAkhir['stock_id'])
                ->get()
                ->getRowArray();

            if (!$parentAkhir) {
                throw new \Exception("Parent stock IN tidak ditemukan");
            }

            // ==============================
            // 3. Kurangi qty di detail dan parent
            // ==============================
            $newQtyDetail = max(0, $detailAkhir['qty_diterima'] - $qtyIn);
            $newQtyBersih = max(0, $detailAkhir['qty_bersih'] - $qtyIn);

            $db->table('stock_revamp_detail')
                ->where('id', $akhirId)
                ->update([
                    'qty_diterima' => $newQtyDetail,
                    'qty_bersih'   => $newQtyBersih,
                    'updatedAt'    => date('Y-m-d H:i:s'),
                ]);

            $newQtyParentDiterima = max(0, $parentAkhir['qty_diterima'] - $qtyIn);
            $newQtyParentBersih   = max(0, $parentAkhir['qty_bersih'] - $qtyIn);

            $db->table('stock_revamp')
                ->where('id', $parentAkhir['id'])
                ->update([
                    'qty_diterima' => $newQtyParentDiterima,
                    'qty_bersih'   => $newQtyParentBersih,
                    'updatedAt'    => date('Y-m-d H:i:s'),
                ]);

            // ==============================
            // 4. Hapus history terkait detail IN ini
            // ==============================
            $db->table('stock_revamp_history')
                ->where('stock_detail_akhir', $akhirId)
                ->delete();

            // ==============================
            // 5. Insert log UNPOST (status OUT)
            // ==============================
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id' => $akhirId,
                'status'          => 'OUT',
                'keterangan'      => $keterangan,
                'no_dokumen'      => $noDokumen,
                'qty_diterima'    => $qtyIn,
                'qty_bersih'      => $qtyIn,
                'createdAt'       => date('Y-m-d H:i:s'),
                'updatedAt'       => date('Y-m-d H:i:s'),
            ]);

            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Unpost Stock IN Failed: ' . $e->getMessage());
            throw $e;
        }
    }



    // END OF 2 FUNGSI KHUSUS JASA VENDOR


    public function getBarangRebusAndStock($type_barang, $divisi_id, $warehouse_id)
    {
        $selectQry = "
        stock_revamp.id AS stock_id,
        stock_revamp.spesifikasi_id AS spesifikasi_id,
        CONCAT(UPPER(barang_master.barang_name), '-', UPPER(barang_master_spesifikasi.spesifikasi)) AS barang,
        barang_master.kode_barang,
        barang_master.id,
        satuans.kode_satuan
    ";

        $dataResult1 = $this->asArray()->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('stock_revamp.deletedAt', null)
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            ->where('barang_master.type_barang', $type_barang)
            ->where('stock_revamp.divisi_id', $divisi_id)
            ->where('stock_revamp.warehouse_id', $warehouse_id)
            // ->like('barang_master.barang_name', '%' . "UDANG" . '%')
            ->orderBy('barang_master.kode_barang', "ASC")
            ->findAll();
        return $dataResult1;
    }


    public function getBarangAndStock($type_barang, $divisi_id, $warehouse_id)
    {
        // var_dump($type_barang, $divisi_id, $warehouse_id);
        // die;
        $selectQry = "
        stock_revamp.id AS stock_id,
        stock_revamp.spesifikasi_id AS spesifikasi_id,
        CONCAT(UPPER(barang_master.barang_name), '-', UPPER(barang_master_spesifikasi.spesifikasi)) AS barang,
        barang_master.kode_barang,
        barang_master.id,
        satuans.kode_satuan
    ";

        $dataResult1 = $this->asArray()->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('stock_revamp.deletedAt', null)
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            ->where('barang_master.type_barang', $type_barang)
            ->where('stock_revamp.divisi_id', $divisi_id)
            ->where('stock_revamp.warehouse_id', $warehouse_id)
            // ->like('barang_master.barang_name', '%' . "UDANG" . '%')
            ->orderBy('barang_master.kode_barang', "ASC")
            ->findAll();
        return $dataResult1;
    }


    public function getListMasterBarang($company_id, $type_barang)
    {
        $kemasanModel = new KemasanModel();
        $barangMasterModel = new BarangMasterModel();
        $result = [];

        if ($type_barang == "kemasan") {
            $condition = [
                'kemasan.deletedAt' => null,
                'kemasan.company_id' => $company_id,
            ];

            $qryKemasanRes = $kemasanModel
                ->select('kemasan.*, satuans.kode_satuan, satuans.nama_satuan, satuans.kode_satuan')
                ->join('satuans', 'satuans.id = kemasan.satuan_id', 'left')
                ->where($condition)
                ->findAll();

            foreach ($qryKemasanRes as $k) {

                $result[] = [
                    'barang_id'  => 0,
                    'spesifikasi_id' => $k['id'], // spesifikasi_id = kemasan_id (jika kemasan)
                    'type_barang' => $type_barang,
                    'barang' => strtoupper($k['name']),
                    'spesifikasi_name' => strtoupper($k['name']),
                    'kode_barang' => $k['kode'],
                    'nama_satuan' => $k['nama_satuan'],
                    'kode_satuan' => $k['kode_satuan']
                ];
            }
        } else {
            $condition = [
                'barang_master.company_id' => $company_id,
                'barang_master.type_barang' => $type_barang,
                'barang_master.deletedAt' => null,
                'barang_master_spesifikasi.deletedAt' => null
            ];

            $selectQryBarang = "
                barang_master.id,
                barang_master.barang_name,
                barang_master.kode_barang,
                barang_master_spesifikasi.id AS spesifikasi_id,
                barang_master_spesifikasi.spesifikasi,
                satuans.nama_satuan,
                satuans.kode_satuan
            ";

            $qryBarangMaster = $barangMasterModel
                ->select($selectQryBarang)
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->where($condition)
                ->findAll();

            foreach ($qryBarangMaster as $b) {
                $result[] = [
                    'barang_id'  => $b['id'],
                    'spesifikasi_id' => $b['spesifikasi_id'], // spesifikasi_id = kemasan_id (jika kemasan)
                    'type_barang' => $type_barang,
                    'barang' => strtoupper($b['barang_name'] . "-" . $b['spesifikasi']),
                    'spesifikasi_name' => strtoupper($b['spesifikasi']),
                    'kode_barang' => $b['kode_barang'],
                    'nama_satuan' => $b['nama_satuan'],
                    'kode_satuan' => $b['kode_satuan']
                ];
            }
        }

        return $result;
    }

    public function getListStock($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'parent_type_id'    => 'barang_master.parent_type_id',
            'kode_barang'       => 'barang_master.kode_barang',
            'barang_name'       => 'barang_master.barang_name',
            'spesifikasi'       => 'barang_master_spesifikasi.spesifikasi',
            'divisi_id'         => 'divisis.divisi',
            'warehouse_id'      => 'warehouses.warehouse_name',
            'qty_diterima'      => 'stock_revamp.qty_diterima',
            'unit_id'           => 'stock_revamp.unit_id',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'stock_revamp.barang_master_id'] ?? 'stock_revamp.barang_master_id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp.*,
            parent_barang.parent_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi,
            warehouses.warehouse_name,
            satuans.kode_satuan
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['type_barang'])) {
            $builder->where('barang_master.type_barang', $addCondition['type_barang']);
        }
        if (!empty($addCondition['parent_type_id'])) {
            $builder->where('barang_master.parent_type_id', $addCondition['parent_type_id']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getStockIdentity($id)
    {
        $selectQry = "
            stock_revamp.*,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            barang_master.type_barang,
            parent_barang.parent_name,
            satuans.kode_satuan,
            warehouses.warehouse_name,
            divisis.divisi
        ";

        $dataResult = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->where('stock_revamp.id', $id)
            ->first();

        return $dataResult;
    }

    public function getStockListAll(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {

        $db = \Config\Database::connect();

        // ============================
        // 🔍 FILTER KONDISI
        // ============================

        $where = [];
        $whereDatePenerimaanBarang = "";
        $whereDateProsesRebus = "";
        $whereDateJasaVendor = "";
        $whereDateHasilProduksi = "";
        $whereDateMaterialRequest = "";
        $whereDateMaterialRequestPenolong = "";
        $whereDateAdjusment = "";
        $whereDatePenerimaanMutasi = "";
        $whereDatePenerimaanMutasiGlobal = "";

        $searchPoLokalBb = "";
        $searchPoBp = "";
        $searchPoImportBb = "";
        $searchProsesRebus = "";
        $searchJasaVendor = "";
        $searchHasilProduksi = "";
        $searchMaterialRequest = "";
        $searchMaterialRequestPenolong = "";
        $searchAdjusment = "";
        $searchPenerimaanMutasi = "";
        $searchPenerimaanMutasiGlobal = "";

        if (!empty($condition['id'])) {
            $where[] = "stock_revamp_detail.id = '$condition[id]'";
        }

        if (!empty($condition['company_id'])) {
            $where[] = "stock_revamp.company_id = '$condition[company_id]'";
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDatePenerimaanBarang = "AND penerimaan_barang.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateProsesRebus = "AND proses_rebus.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateJasaVendor = "AND jasa_vendor_in.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateHasilProduksi = "AND production_results.receive_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequest = "AND material_requests.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateMaterialRequestPenolong = "AND material_requests_penolong.request_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDateAdjusment = "AND adjusment.tanggal  BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDatePenerimaanMutasi = "AND penerimaan_mutasi.tanggal  BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
            $whereDatePenerimaanMutasiGlobal = "AND penerimaan_mutasi_global.tanggal  BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['type_barang'])) {
            $where[] = "barang_master.type_barang = '$condition[type_barang]'";
        }

        if (!empty($condition['divisi_id'])) {
            $where[] = "stock_revamp.divisi_id = '$condition[divisi_id]'";
        }

        if (!empty($condition['warehouse_id'])) {
            $where[] = "stock_revamp.warehouse_id = '$condition[warehouse_id]'";
        }

        if (!empty($condition['spesifikasi_id'])) {
            $where[] = "stock_revamp.spesifikasi_id = '$condition[spesifikasi_id]'";
        }

        if (!empty($condition['barang_id'])) {
            $where[] = "stock_revamp.barang_master_id = '$condition[barang_id]'";
        }
        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));
            $searchPoLokalBb = "
                AND
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR rm_purchase_orders.po_no LIKE '%{$search}%'
                        OR suppliers.name LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                    )

            ";
            $searchPoBp = "
                AND 
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR am_purchase_orders.po_no LIKE '%{$search}%'
                        OR suppliers.name LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                        OR purchase_requests.spp_no LIKE '%{$search}%'
                    )
            ";
            $searchPoImportBb = "
                AND 
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR rm_import_pos.po_no LIKE '%{$search}%'
                        OR suppliers.name LIKE '%{$search}%'
                        OR penerimaan_barang.no_penerimaan_barang LIKE '%{$search}%'
                    )
            ";
            $searchProsesRebus = "
                AND 
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR proses_rebus.no_rebus LIKE '%{$search}%'
                    )
            ";
            $searchJasaVendor = "
                AND
                    ( 
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR jasa_vendor_in.no_penerimaan_surat_jalan LIKE '%{$search}%'
                    )
            ";
            $searchHasilProduksi = "
                AND 
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR production_results.pr_no LIKE '%{$search}%'
                    )
            ";
            $searchMaterialRequest = "
                AND
                    ( 
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR material_requests.req_no LIKE '%{$search}%'
                    )
            ";
            $searchMaterialRequestPenolong = "
                AND 
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR material_requests_penolong.req_no LIKE '%{$search}%'
                    )
            ";
            $searchAdjusment = "
                AND 
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR adjusment.no_adjusment LIKE '%{$search}%'
                    )
            ";

            $searchPenerimaanMutasi = "
                AND 
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR penerimaan_mutasi.penerimaan_mutasi_no LIKE '%{$search}%'
                    )
            ";


            $searchPenerimaanMutasiGlobal = "
                AND 
                    (
                        CONCAT(barang_master.barang_name, ' ',barang_master_spesifikasi.spesifikasi) LIKE '%{$search}%'
                        OR barang_master.kode_barang LIKE '%{$search}%'
                        OR penerimaan_mutasi_global.penerimaan_mutasi_no LIKE '%{$search}%'
                    )
            ";
        }


        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'id',
            'id',
            'divisi',
            'warehouse_name',
            'spp_no',
            'reference_type',
            'supplier_name',
            'kode_barang',
            'barang_name',
            'spesifikasi',
            'type_bc',
            'po_no',
            'po_date',
            'lpb_date',
            'reference_no',
            'qty_diterima',
            'kode_satuan',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- STOK DARI PO LOKAL BAKU
            SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name AS barang_name,
                barang_master_spesifikasi.spesifikasi AS spesifikasi,
                rm_purchase_orders.po_no AS po_no,
                rm_purchase_orders.po_date,
                penerimaan_barang.tanggal AS lpb_date,
                suppliers.name AS supplier_name,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                bc_purchase_order.no_aju AS no_aju,
                bc_purchase_order.no_daftar AS no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_dokumen,
                -- HELPER UNTUK OUT DAN IN STOK GLOBAL
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_bersih
            FROM 
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_purchase_orders ON rm_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = rm_purchase_orders.supplier_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='BAKU'
            AND penerimaan_barang.status_penerimaan='LOKAL'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoLokalBb
        )
        UNION ALL
        (
            -- STOK DARI PO LOKAL BAHAN PENOLONG
            SELECT
                stock_revamp_detail.id,
                purchase_requests.spp_no AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                am_purchase_orders.po_no AS po_no,
                am_purchase_orders.po_date,
                penerimaan_barang.tanggal AS lpb_date,
                suppliers.name AS supplier_name,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                bc_purchase_order.no_aju AS no_aju,
                bc_purchase_order.no_daftar AS no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_dokumen,
                -- HELPER UNTUK OUT DAN IN STOK GLOBAL
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_bersih
            FROM 
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN am_purchase_orders ON am_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = am_purchase_orders.supplier_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            LEFT JOIN purchase_requests ON purchase_requests.id = am_purchase_orders.purchase_request_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND penerimaan_barang.status_penerimaan='LOKAL'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoBp
        )
        UNION ALL
        (
            -- STOK DARI PO IMPORT BAHAN PENOLONG
            SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                am_purchase_orders.po_no AS po_no,
                am_purchase_orders.po_date,
                penerimaan_barang.tanggal AS lpb_date,
                suppliers.name AS supplier_name,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                bc_purchase_order.no_aju AS no_aju,
                bc_purchase_order.no_daftar AS no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_dokumen,
                -- HELPER UNTUK OUT DAN IN STOK GLOBAL
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM 
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN am_purchase_orders ON am_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = am_purchase_orders.supplier_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            LEFT JOIN purchase_requests ON purchase_requests.id = am_purchase_orders.purchase_request_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='PENOLONG'
            AND penerimaan_barang.status_penerimaan='IMPORT'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoBp
        )
        UNION ALL
        (
            -- STOK DARI PO IMPORT BAHAN BAKU
            SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                rm_import_pos.po_no AS po_no,
                rm_import_pos.po_date,
                penerimaan_barang.tanggal AS lpb_date,
                suppliers.name AS supplier_name,
                penerimaan_barang.no_penerimaan_barang AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                bc_purchase_order.no_aju AS no_aju,
                bc_purchase_order.no_daftar AS no_daftar,
                DATE(bc_purchase_order.createdAt) AS tanggal_dokumen,
                -- HELPER UNTUK OUT DAN IN STOK GLOBAL
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM 
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_import_pos ON rm_import_pos.id = stock_revamp_detail.po_id
            LEFT JOIN penerimaan_barang ON penerimaan_barang.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = rm_import_pos.supplier_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            LEFT JOIN bc_purchase_order_lpb ON bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id
            LEFT JOIN bc_purchase_order ON bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='LPB'
            AND penerimaan_barang.tipe_bahan='BAKU'
            AND penerimaan_barang.status_penerimaan='IMPORT'
            $filterCondition
            $whereDatePenerimaanBarang
            $searchPoImportBb
        )
        UNION ALL
        (
            -- STOK DARI PROSES REBUS
            SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                rm_purchase_orders.po_no AS po_no,
                rm_purchase_orders.po_date,
                proses_rebus.tanggal AS lpb_date,
                suppliers.name AS supplier_name,
                proses_rebus.no_rebus AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                '' AS no_aju,
                '' AS no_daftar,
                '' AS tanggal_dokumen,
                -- HELPER UNTUK OUT DAN IN STOK GLOBAL
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN rm_purchase_orders ON rm_purchase_orders.id = stock_revamp_detail.po_id
            LEFT JOIN proses_rebus ON proses_rebus.id = stock_revamp_detail.reference_id
            LEFT JOIN suppliers ON suppliers.id = rm_purchase_orders.supplier_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PROSES REBUS'
            $filterCondition
            $whereDateProsesRebus
            $searchProsesRebus
        )
        UNION ALL
        (
            -- STOK DARI JASA VENDOR
             SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS po_no,
                '' AS po_date,
                jasa_vendor_in.tanggal AS lpb_date,
                vendors.name AS supplier_name,
                jasa_vendor_in.no_penerimaan_surat_jalan AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                '' AS no_aju,
                '' AS no_daftar,
                '' AS tanggal_dokumen,
                -- HELPER UNTUK STOK
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN jasa_vendor_in ON jasa_vendor_in.id = stock_revamp_detail.reference_id
            LEFT JOIN vendors ON vendors.id = jasa_vendor_in.vendor_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='JASA VENDOR'
            $filterCondition
            $whereDateJasaVendor
            $searchJasaVendor
        )
        UNION ALL
        (
            -- STOK DARI HASIL PRODUKSI
             SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS po_no,
                '' AS po_date,
                production_results.receive_date AS lpb_date,
                '' AS supplier_name,
                production_results.pr_no AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                '' AS no_aju,
                '' AS no_daftar,
                '' AS tanggal_dokumen,
                -- HELPER UNTUK STOK
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN production_results ON production_results.id = stock_revamp_detail.reference_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='HASIL PRODUKSI'
            $filterCondition
            $whereDateHasilProduksi
            $searchHasilProduksi
        )
        UNION ALL
        (
            -- STOK DARI MATERIAL REQUEST BAKU
             SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS po_no,
                '' AS po_date,
                material_requests.request_date AS lpb_date,
                '' AS supplier_name,
                material_requests.req_no AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                '' AS no_aju,
                '' AS no_daftar,
                '' AS tanggal_dokumen,
                -- HELPER UNTUK STOK
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests ON material_requests.id = stock_revamp_detail.reference_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='MATERIAL REQUEST BAKU'
            $filterCondition
            $whereDateMaterialRequest
            $searchMaterialRequest
        )
        UNION ALL
        (
            -- STOK DARI MATERIAL REQUEST PENOLONG
             SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS po_no,
                '' AS po_date,
                material_requests_penolong.request_date AS lpb_date,
                '' AS supplier_name,
                material_requests_penolong.req_no AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                '' AS no_aju,
                '' AS no_daftar,
                '' AS tanggal_dokumen,
                -- HELPER UNTUK STOK
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN material_requests_penolong ON material_requests_penolong.id = stock_revamp_detail.reference_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='MATERIAL REQUEST PENOLONG'
            $filterCondition
            $whereDateMaterialRequestPenolong
            $searchMaterialRequestPenolong
        )
        UNION ALL
        (
            -- STOK DARI INISIASI
             SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS po_no,
                '' AS po_date,
                '' AS lpb_date,
                '' AS supplier_name,
                '' AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                '' AS no_aju,
                '' AS no_daftar,
                '' AS tanggal_dokumen,
                -- HELPER UNTUK STOK
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='INISIASI'
            $filterCondition
        )
        UNION ALL
        (
            -- STOK DARI ADJUSMENT
             SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS po_no,
                '' AS po_date,
                adjusment.tanggal AS lpb_date,
                '' AS supplier_name,
                adjusment.no_adjusment AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                '' AS no_aju,
                '' AS no_daftar,
                '' AS tanggal_dokumen,
                -- HELPER UNTUK STOK
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            LEFT JOIN adjusment ON adjusment.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='ADJUSMENT'
            $filterCondition
            $whereDateAdjusment
            $searchAdjusment
        )
        UNION ALL
        (
            -- STOK DARI PENERIMAAN MUTASI
             SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS po_no,
                '' AS po_date,
                penerimaan_mutasi.tanggal AS lpb_date,
                '' AS supplier_name,
                penerimaan_mutasi.penerimaan_mutasi_no AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                ppbkb.no_ppbkb AS no_aju,
                ppbkb.no_daftar AS no_daftar,
                ppbkb.tanggal AS tanggal_dokumen,
                -- HELPER UNTUK STOK
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            LEFT JOIN penerimaan_mutasi ON penerimaan_mutasi.id = stock_revamp_detail.reference_id
            LEFT JOIN ppbkb ON ppbkb.id = penerimaan_mutasi.ppbkb_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PENERIMAAN MUTASI'
            $filterCondition
            $whereDatePenerimaanMutasi
            $searchPenerimaanMutasi
        )
        UNION ALL
        (
            -- STOK DARI PENERIMAAN MUTASI GLOBAL
             SELECT
                stock_revamp_detail.id,
                '' AS spp_no,
                stock_revamp_detail.reference_type,
                stock_revamp_detail.qty_diterima,
                stock_revamp_detail.type_bc,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                '' AS po_no,
                '' AS po_date,
                penerimaan_mutasi_global.tanggal AS lpb_date,
                '' AS supplier_name,
                penerimaan_mutasi_global.penerimaan_mutasi_no AS reference_no,
                satuans.kode_satuan,
                divisis.divisi,
                warehouses.warehouse_name,
                stock_revamp.unit_id,
                '' AS no_aju,
                '' AS no_daftar,
                '' AS tanggal_dokumen,
                -- HELPER UNTUK STOK
                stock_revamp.barang_master_id,
                stock_revamp.spesifikasi_id,
                stock_revamp.divisi_id,
                stock_revamp.warehouse_id,
                stock_revamp_detail.bc_id,
                stock_revamp_detail.reference_id,
                stock_revamp_detail.po_type,
                stock_revamp_detail.po_id,
                stock_revamp_detail.qty_diterima
            FROM
                stock_revamp_detail
            LEFT JOIN stock_revamp ON stock_revamp.id = stock_revamp_detail.stock_id
            LEFT JOIN barang_master ON stock_revamp.barang_master_id = barang_master.id
            LEFT JOIN barang_master_spesifikasi ON stock_revamp.spesifikasi_id = barang_master_spesifikasi.id
            LEFT JOIN satuans ON satuans.id = stock_revamp.unit_id
            LEFT JOIN divisis ON divisis.id = stock_revamp.divisi_id
            LEFT JOIN warehouses ON warehouses.id = stock_revamp.warehouse_id
            LEFT JOIN penerimaan_mutasi_global ON penerimaan_mutasi_global.id = stock_revamp_detail.reference_id
            WHERE stock_revamp_detail.deletedAt IS NULL
            AND stock_revamp_detail.reference_type='PENERIMAAN MUTASI GLOBAL'
            $filterCondition
            $whereDatePenerimaanMutasiGlobal
            $searchPenerimaanMutasiGlobal
        )
        
        ";


        // ============================
        // 📊 COUNT + PAGINATION
        // ============================

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = "
            SELECT * FROM ($baseQuery) AS x
            $orderBy
            LIMIT $limit OFFSET $offset
        ";



        // var_dump($mainQuery);
        // die;
        $data = $db->query($mainQuery)->getResultArray();

        // ============================
        // 📦 RETURN RESULT
        // ============================

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function getBarangBelumInisiasi(
        $type_barang,
        $divisi_id,
        $warehouse_id,
        $company_id,
        $search
    ) {
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();

        $stockSudahInisiasi = $this->asArray()
            ->select('stock_revamp.*')
            ->where('stock_revamp.deletedAt', null)
            ->where('stock_revamp.company_id', $company_id)
            ->where('stock_revamp.divisi_id', $divisi_id)
            ->where('stock_revamp.warehouse_id', $warehouse_id)
            ->findAll();

        $spesifikasiIdSudahInisiasi = array_column($stockSudahInisiasi, 'spesifikasi_id');
        $dataQry = $barangMasterSpesifikasiModel
            ->select(
                '
                barang_master_spesifikasi.*,
                barang_master.barang_name,
                barang_master.kode_barang
                '
            )
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            ->where('barang_master.company_id', $company_id)
            ->where('barang_master.type_barang', $type_barang);

        if (count($spesifikasiIdSudahInisiasi) > 0) {
            $dataQry->whereNotIn('barang_master_spesifikasi.id', $spesifikasiIdSudahInisiasi);
        }

        $dataQry->groupStart()
            ->like('CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi)', $search)
            ->orLike('barang_master.kode_barang', $search)
            ->groupEnd();

        return $dataQry->findAll(100);
    }

    public function getBarangBelumDiinisiasi(
        $company_id,
        $divisi_id,
        $warehouse_id,
        $spesifikasi_id,
        $unit_id
    ) {
        $stockSudahInisiasi = $this->asArray()
            ->select('stock_revamp.*')
            ->where('stock_revamp.company_id', $company_id)
            ->where('stock_revamp.divisi_id', $divisi_id)
            ->where('stock_revamp.warehouse_id', $warehouse_id)
            ->where('stock_revamp.spesifikasi_id', $spesifikasi_id)
            ->where('stock_revamp.unit_id', $unit_id)
            ->where('stock_revamp.deletedAt', null)
            ->first();
        return $stockSudahInisiasi;
    }

    public function getListKartuStock($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi_id'          => 'stock_revamp.divisi_id',
            'warehouse_id'       => 'stock_revamp.warehouse_id',
            'barang_master_id'   => 'stock_revamp.barang_master_id',
            'spesifikasi_id'     => 'stock_revamp.spesifikasi_id',
            'unit_id'            => 'stock_revamp.unit_id',
            'kode_barang'       => 'barang_master.kode_barang'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'barang_master_id'] ?? 'stock_revamp.barang_master_id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            stock_revamp.*,
            divisis.divisi,
            warehouses.warehouse_name,
            satuans.kode_satuan,
            barang_master.barang_name,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi
        ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'stock_revamp.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->join('divisis', 'divisis.id = stock_revamp.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = stock_revamp.warehouse_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $builder->countAllResults(false);

        if (!empty($addCondition['divisi_id'])) {
            $builder->where('stock_revamp.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('stock_revamp.warehouse_id', $addCondition['warehouse_id']);
        }
        if (!empty($addCondition['barang_master_id'])) {
            $builder->where('stock_revamp.barang_master_id', $addCondition['barang_master_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getStockListSearchBarang(
        $companyId,
        $divisiId,
        $warehouseId,
        $search
    ) {

        $condition = [
            'stock_revamp.company_id' => $companyId,
            'stock_revamp.divisi_id' => $divisiId,
            'stock_revamp.warehouse_id' => $warehouseId
        ];

        $selectQry = "
            stock_revamp.id,
            barang_master.kode_barang, 
            barang_master.barang_name, 
            barang_master_spesifikasi.spesifikasi,
            stock_revamp.unit_id,
            satuans.kode_satuan
        ";

        $dataQry = $this->asArray()->select($selectQry)
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->where('stock_revamp.deletedAt', null)
            ->where($condition);

        $dataQry
            ->groupStart()
            ->like('CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi)', $search)
            ->orLike('barang_master.kode_barang', $search)
            ->groupEnd();


        $dataBarang = $dataQry->findAll(100);
        return $dataBarang;
    }

    public function allStockFisik($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'parent_type_id' => 'barang_master.parent_type_id',
            'kode_barang'    => 'barang_master.kode_barang',
            'barang_name'    => 'barang_master.barang_name',
            'qty_diterima'   => 'stock_revamp.qty_diterima',
            'unit_id'        => 'stock_revamp.unit_id',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'barang_master.barang_name'] ?? 'barang_master.barang_name';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'asc')] ?? 'asc';

        $selectQry = "
            stock_revamp.barang_master_id,
            stock_revamp.unit_id,
            SUM(stock_revamp.qty_bersih) AS total_qty_bersih,
            parent_barang.parent_name,
            barang_master.kode_barang,
            barang_master.barang_name,
            satuans.kode_satuan
        ";

        // Builder dasar (tanpa search)
        $baseBuilder = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'stock_revamp.barang_master_id = barang_master.id', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('satuans', 'satuans.id = stock_revamp.unit_id', 'left')
            ->where($condition)
            ->groupBy('stock_revamp.barang_master_id')
            ->orderBy($sort, $sortType);

        // Total data (tanpa search)
        $totalData = $baseBuilder->countAllResults(false);

        if (!empty($addCondition['barang_master_id'])) {
            $baseBuilder->where('stock_revamp.barang_master_id', $addCondition['barang_master_id']);
        }

        // Filter pencarian
        if (!empty($addCondition['search'])) {
            $search = $addCondition['search'];
            $baseBuilder->groupStart()
                ->like('barang_master.barang_name', $search)
                ->orLike('barang_master.kode_barang', $search)
                ->orLike('parent_barang.parent_name', $search)
                ->groupEnd();
        }

        $countBuilder = clone $baseBuilder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $baseBuilder->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }
}
