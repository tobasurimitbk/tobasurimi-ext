<?php

namespace App\Models;

use CodeIgniter\Model;

class PPBKBModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'ppbkb';
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


    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'ppbkb.tanggal' => 'ppbkb.tanggal',
            'mutasi.divisi_asal_id'           => 'mutasi.divisi_asal_id',
            'mutasi.warehouse_asal_id'        => 'mutasi.warehouse_asal_id',
            'mutasi.divisi_tujuan_id'           => 'mutasi.divisi_tujuan_id',
            'mutasi.warehouse_tujuan_id'        => 'mutasi.warehouse_tujuan_id',
            'mutasi.no_mutasi'                => 'mutasi.no_mutasi',
            'ppbkb.no_ppbkb'                  => 'ppbkb.no_ppbkb',
            'ppbkb.tanggal'                   => 'ppbkb.tanggal',
            'ppbkb.no_daftar'                   => 'ppbkb.no_daftar',
            'ppbkb.status_posting'            => 'ppbkb.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'ppbkb.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "ppbkb.*,
            tb_divisi_asal.divisi AS divisi_asal,
            tb_divisi_tujuan.divisi AS divisi_tujuan,
            tb_warehouse_asal.warehouse_name AS warehouse_asal,
            tb_warehouse_tujuan.warehouse_name AS warehouse_tujuan,
            mutasi.no_mutasi,
            ppbkb.no_ppbkb,
            ppbkb.no_daftar";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('mutasi', 'mutasi.id = ppbkb.mutasi_id', 'left')
            ->join('divisis tb_divisi_asal', 'tb_divisi_asal.id = mutasi.divisi_asal_id', 'left')
            ->join('divisis tb_divisi_tujuan', 'tb_divisi_tujuan.id = mutasi.divisi_tujuan_id', 'left')
            ->join('warehouses tb_warehouse_asal', 'tb_warehouse_asal.id = mutasi.warehouse_asal_id', 'left')
            ->join('warehouses tb_warehouse_tujuan', 'tb_warehouse_tujuan.id = mutasi.warehouse_tujuan_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if (
            $addCondition['search'] ||
            $addCondition['mulaiTanggalPPBKB'] ||
            $addCondition['selesaiTanggalPPBKB'] ||
            $addCondition['statusPosting']
        ) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusPosting']) {
            if ($addCondition['statusPosting'] == "ALL") {
                $bcDataQry->whereIn('ppbkb.status_posting', ['1', '0']);
            } elseif ($addCondition['statusPosting'] == "SUDAH POSTING") {
                $bcDataQry->where('ppbkb.status_posting', "1");
            } else if ($addCondition['statusPosting'] == "BELUM POSTING") {
                $bcDataQry->where('ppbkb.status_posting', "0");
            }
        }

        if ($addCondition['mulaiTanggalPPBKB']) {
            $bcDataQry->where('ppbkb.tanggal >=', $addCondition['mulaiTanggalPPBKB']);
        }

        if ($addCondition['selesaiTanggalPPBKB']) {
            $bcDataQry->where('ppbkb.tanggal <=', $addCondition['selesaiTanggalPPBKB']);
        }

        if ($addCondition['search']) {
            $bcDataQry->like('no_ppbkb', $addCondition['search'])
                ->orLike('no_daftar', $addCondition['search'])
                ->orLike('no_mutasi', $addCondition['search']);
        }

        if (
            $addCondition['search'] ||
            $addCondition['mulaiTanggalPPBKB'] ||
            $addCondition['selesaiTanggalPPBKB'] ||
            $addCondition['statusPosting']
        ) {
            $bcDataQry->groupEnd();
        }

        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListOutstanding($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $mutasiDetailModel = new MutasiDetailModel();

        $availableSort = [
            'mutasi.no_mutasi' => 'mutasi.no_mutasi',
            'mutasi.divisi_asal_id' => 'mutasi.divisi_asal_id',
            'mutasi.warehouse_asal_id' => 'mutasi.warehouse_asal_id',
            'mutasi.divisi_tujuan_id' => 'mutasi.divisi_tujuan_id',
            'mutasi.warehouse_tujuan_id' => 'mutasi.warehouse_tujuan_id',
            'mutasi.tanggal' => 'mutasi.tanggal',
            'barang_master.kode_barang' => 'barang_master.kode_barang',
            'barang_master.barang_name' => 'barang_master.barang_name',
            'barang_master_spesifikasi.spesifikasi' => 'barang_master_spesifikasi.spesifikasi',
            'mutasi_detail.qty_konversi' => 'mutasi_detail.qty_konversi',
            'mutasi_detail.unit_id_konversi' => 'mutasi_detail.unit_id_konversi',
            'stock_revamp_detail.type_bc' => 'stock_revamp_detail.type_bc',
            'bc_purchase_order.no_aju' => 'bc_purchase_order.no_aju',
            'bc_purchase_order.no_daftar' => 'bc_purchase_order.no_daftar'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'mutasi.tanggal'] ?? 'mutasi.tanggal';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "mutasi_detail.*,
            tb_divisi_asal.divisi AS divisi_asal,
            tb_divisi_tujuan.divisi AS divisi_tujuan,
            tb_warehouse_asal.warehouse_name AS warehouse_asal,
            tb_warehouse_tujuan.warehouse_name AS warehouse_tujuan,
            mutasi.tanggal,
            mutasi.no_mutasi,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan,
            stock_revamp_detail.type_bc,
            bc_purchase_order.no_aju AS no_aju,
            bc_purchase_order.no_daftar AS no_daftar,
            ppbkb_detail.hs_code_id";

        $bcDataQry = $mutasiDetailModel
            ->asObject()
            ->select($selectQry)
            ->where($condition)
            ->groupStart()
            ->where('ppbkb_detail.deletedAt IS NOT NULL')
            ->orWhere('ppbkb_detail.hs_code_id IS NULL')
            ->groupEnd()
            ->join('mutasi', 'mutasi.id = mutasi_detail.mutasi_id', 'left')
            ->join('divisis tb_divisi_asal', 'tb_divisi_asal.id = mutasi.divisi_asal_id', 'left')
            ->join('divisis tb_divisi_tujuan', 'tb_divisi_tujuan.id = mutasi.divisi_tujuan_id', 'left')
            ->join('warehouses tb_warehouse_asal', 'tb_warehouse_asal.id = mutasi.warehouse_asal_id', 'left')
            ->join('warehouses tb_warehouse_tujuan', 'tb_warehouse_tujuan.id = mutasi.warehouse_tujuan_id', 'left')
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = mutasi_detail.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            // asumsikan dari bc 40 dan 23 dulu aja yha pemasukkannnya
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('ppbkb_detail', 'ppbkb_detail.mutasi_detail_id = mutasi_detail.id', 'left')
            ->join('satuans', 'satuans.id = mutasi_detail.unit_id_konversi', 'left')
            ->groupBy('mutasi_detail.id')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $bcDataQry->like('bc_purchase_order.no_aju', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('mutasi.no_mutasi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search']);
        }

        if ($addCondition['search']) {
            $bcDataQry->groupEnd();
        }

        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getDetail($id)
    {

        $selectQry = "ppbkb.*,
        mutasi.divisi_asal_id,
        mutasi.divisi_tujuan_id,
        mutasi.divisi_tujuan_id,
        mutasi.warehouse_tujuan_id,
        mutasi.no_mutasi,
        divisis.divisi AS divisi_asal_name,
        warehouses.warehouse_name AS warehouse_asal_name";

        $result = $this->asArray()
            ->select($selectQry)
            ->join('mutasi', 'mutasi.id = ppbkb.mutasi_id', 'left')
            ->join('divisis', 'divisis.id = mutasi.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi.warehouse_asal_id', 'left')
            ->where('ppbkb.id', $id)
            ->first();

        return $result;
    }

    public function getNo($companyId)
    {
        $builder = $this->db->table('ppbkb');
        $builder->select('no_ppbkb');
        $builder->orderBy('no_ppbkb', 'desc');
        $builder->where('ppbkb.company_id', $companyId);
        $query = $builder->get();
        $lastPenerimaan = 0;

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $row) {
                $number = $row['no_ppbkb'];
                if ($number > $lastPenerimaan) {
                    $lastPenerimaan = $number;
                }
            }
            $lastPenerimaan++;
        } else {
            $lastPenerimaan = 1;
        }
        $formattedLastPenerimaan = sprintf("%05d", $lastPenerimaan);
        return $formattedLastPenerimaan;
    }
}
