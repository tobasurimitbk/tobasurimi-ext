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
    //     'stock_detail_asal' => 100
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

            // ==============================
            // 2. Insert ke stock_revamp_detail
            // ==============================
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

            // ==============================
            // 3. Insert ke stock_revamp_log
            // ==============================
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id' => $stockDetailId,
                'status'         => $data['status'] ?? 'IN',
                'qty_diterima'   => $data['qty_diterima'],
                'qty_bersih'     => $data['qty_bersih'],
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
            $newQtyDetailBersih = $stockDetail['qty_bersih'] - $data['qty_digunakan'];

            // if ($newQtyDetail < 0 || $newQtyDetailBersih < 0) {
            //     throw new \Exception("Qty detail tidak mencukupi. 
            //         Stok tersedia: {$stockDetail['qty_diterima']}/{$stockDetail['qty_bersih']}, 
            //         Qty diminta: {$data['qty_digunakan']}");
            // }

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

            // if ($newQtyParent < 0 || $newQtyParentBersih < 0) {
            //     throw new \Exception("Qty parent tidak mencukupi. 
            //         Stok tersedia: {$stock['qty_diterima']}/{$stock['qty_bersih']}, 
            //         Qty diminta: {$data['qty_digunakan']}");
            // }

            $db->table('stock_revamp')
                ->where('id', $stockDetail['stock_id'])
                ->update([
                    'qty_diterima' => $newQtyParent,
                    'qty_bersih'   => $newQtyParentBersih,
                ]);

            // ==============================
            // 5. Insert ke log
            // ==============================
            $db->table('stock_revamp_log')->insert([
                'stock_detail_id' => $stockDetail['id'],
                'status'          => 'OUT',
                'qty_diterima'    => $data['qty_digunakan'],
                'qty_bersih'      => $data['qty_digunakan'],
                'createdAt'       => date('Y-m-d H:i:s'),
                'updatedAt'       => date('Y-m-d H:i:s'),
            ]);

            return $stockDetail['id'];

        } catch (\Throwable $e) {
            // HAPUS transRollback() dari model
            log_message('error', 'Out Stock Failed: ' . $e->getMessage());
            throw $e; // Lempar exception ke controller
        }
    }


    public function unpostStockRevamp(BaseConnection $db, array $data)
    {
        // HAPUS transBegin() dan transRollback/Commit dari model
        // Karena transaksi sudah dihandle oleh controller

        try {
            $asalId   = $data['stock_detail_asal'];
            $akhirId  = $data['stock_detail_akhir'];
            $qtyAsal  = $data['qty_diterima_asal'];
            $qtyAkhir = $data['qty_diterima_akhir'];

            // ==============================
            // 1. Ambil stock detail asal
            // ==============================
            $detailAsal = $db->table('stock_revamp_detail')
                ->where('id', $asalId)
                ->get()
                ->getRowArray();

            if (!$detailAsal) {
                throw new \Exception("Stock detail asal tidak ditemukan");
            }

            // 2. Ambil stock detail akhir
            $detailAkhir = $db->table('stock_revamp_detail')
                ->where('id', $akhirId)
                ->get()
                ->getRowArray();

            if (!$detailAkhir) {
                throw new \Exception("Stock detail akhir tidak ditemukan");
            }

            // ==============================
            // 3. Validasi qty - PERBAIKAN: Gunakan != bukan !==
            // ==============================
            if ((int)$detailAsal['qty_diterima'] != (int)$qtyAsal) {
                throw new \Exception("Qty asal tidak sesuai. Database: {$detailAsal['qty_diterima']}, Request: {$qtyAsal}. Unpost dibatalkan");
            }

            if ((int)$detailAkhir['qty_diterima'] != (int)$qtyAkhir) {
                throw new \Exception("Qty akhir tidak sesuai. Database: {$detailAkhir['qty_diterima']}, Request: {$qtyAkhir}. Unpost dibatalkan");
            }

            // ==============================
            // 4. Rollback ke parent
            // ==============================
            $parentAsal = $db->table('stock_revamp')
                ->where('id', $detailAsal['stock_id'])
                ->get()
                ->getRowArray();

            $parentAkhir = $db->table('stock_revamp')
                ->where('id', $detailAkhir['stock_id'])
                ->get()
                ->getRowArray();

            if (!$parentAsal || !$parentAkhir) {
                throw new \Exception("Parent stock tidak ditemukan");
            }

            // Kembalikan qty ke parent asal
            $db->table('stock_revamp')
                ->where('id', $parentAsal['id'])
                ->update([
                    'qty_diterima' => $parentAsal['qty_diterima'] + $qtyAsal
                ]);

            // Kurangi qty dari parent akhir
            $db->table('stock_revamp')
                ->where('id', $parentAkhir['id'])
                ->update([
                    'qty_diterima' => $parentAkhir['qty_diterima'] - $qtyAkhir // PERBAIKAN: seharusnya dikurangi
                ]);

            // ==============================
            // 5. Update detail asal (kembalikan qty)
            // ==============================
            $db->table('stock_revamp_detail')
                ->where('id', $asalId)
                ->update([
                    'qty_diterima' => $detailAsal['qty_diterima'] + $qtyAsal
                ]);

            // ==============================
            // 6. Hapus detail akhir
            // ==============================
            $db->table('stock_revamp_detail')->where('id', $akhirId)->delete();

            return true;

        } catch (\Throwable $e) {
            // HAPUS transRollback() di model
            log_message('error', 'Unpost Stock Failed: ' . $e->getMessage());
            throw $e; // Lempar exception ke controller
        }
    }


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

            if (!$detailAsal) {
                throw new \Exception("Stock detail asal tidak ditemukan");
            }

            // ==============================
            // 2. Validasi qty
            // ==============================
            if ((int)$detailAsal['qty_diterima'] != (int)$qtyAsal) {
                throw new \Exception("Qty asal tidak sesuai. Database: {$detailAsal['qty_diterima']}, Request: {$qtyAsal}. Unpost dibatalkan");
            }


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
                'status'          => 'UNPOST',
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

}
