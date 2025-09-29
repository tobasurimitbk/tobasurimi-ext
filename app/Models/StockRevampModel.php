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
}
