<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierHargaModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'supplier_harga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'supplier_id',
        'bahan_baku_id',
        'spesifikasi_id',
        'divisi_id',
        'spesifikasi',
        'nama_barang',
        'harga_umum',
        'harga_harian',
        'harga_bulanan',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    // Dates
    protected $useTimestamps = true;
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
            'divisis.divisi'   => 'divisis.divisi',
            'bahan_baku_name'   => 'barang_master.barang_name',
            'spesifikasi'       => 'supplier_harga.spesifikasi',
            'harga_umum'        => 'supplier_harga.harga_umum',
            'harga_harian'      => 'supplier_harga.harga_harian',
            'harga_bulanan'     => 'supplier_harga.harga_bulanan',
            'createdAt'         => 'supplier_harga.createdAt',
            'updatedAt'         => 'supplier_harga.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'supplier_harga.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "supplier_harga.*, 
            barang_master.barang_name AS bahan_baku_name, 
            barang_master_spesifikasi.spesifikasi,
            divisis.divisi as divisi";

        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barang_master', 'supplier_harga.bahan_baku_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = supplier_harga.spesifikasi_id', 'left')
            ->join('divisis', 'divisis.id = supplier_harga.divisi_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $supplierDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $supplierDataQry->like('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $supplierDataQry->groupEnd();
        }

        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getDetailSpesifikasi($barang_id, $supplier_id, $spesifikasi_id)
    {
        $condition = [
            'supplier_harga.deletedAt' => null,
            'supplier_harga.supplier_id' => $supplier_id,
            'supplier_harga.bahan_baku_id' => $barang_id,
            'supplier_harga.spesifikasi_id' => $spesifikasi_id,
        ];
        return $this->asArray()->where($condition)->first();
    }

    public function getSupplierHarga($supplier_id, $bahan_baku_id, $divisi_id)
    {
        $satuanModel = new SatuansModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();

        $condition = [
            'supplier_harga.deletedAt' => null,
            'supplier_id' => $supplier_id,
            'bahan_baku_id' => $bahan_baku_id,
            'supplier_harga.divisi_id' => $divisi_id
        ];

        $selectQry = "
            supplier_harga.id,
            supplier_harga.supplier_id,
            supplier_harga.bahan_baku_id,
            supplier_harga.spesifikasi_id,
            barang_master_spesifikasi.spesifikasi,
            barang_master.barang_name,
            supplier_harga.harga_umum,
            supplier_harga.harga_harian,
            supplier_harga.harga_bulanan
        ";

        $result = $this
            ->asArray()
            ->select($selectQry)
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = supplier_harga.spesifikasi_id')
            ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id', 'left')
            ->where($condition)
            ->findAll();

        for ($i = 0; $i < count($result); $i++) {
            $spesifikaiDetail = $satuanModel->select('satuans.*,satuan_2,satuan_3')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.satuan_1 = satuans.id')
                ->where('barang_master_spesifikasi.id', $result[0]['spesifikasi_id'])
                ->first();

            $poLast = $rmPurchaseOrderDetailModel
                ->select('rm_purchase_order_details.*')
                ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id')
                ->where('rm_purchase_orders.supplier_id', $supplier_id)
                ->where('rm_purchase_order_details.barang2_id', $result[$i]['spesifikasi_id'])
                ->orderBy('rm_purchase_orders.createdAt', "DESC")
                ->first();

            if ($poLast != null) {
                $result[$i]['harga_umum'] = $poLast['general_price'];
                $result[$i]['harga_harian'] = $poLast['daily_price'];
                $result[$i]['harga_bulanan'] = $poLast['monthly_price'];
            }

            $result[$i]['nama_barang'] = $result[$i]['barang_name'] . " " . $result[$i]['spesifikasi'];
            $result[$i]['satuan_id'] = ($spesifikaiDetail != null) ? $spesifikaiDetail['id'] : '';
            $result[$i]['satuan_2'] = ($spesifikaiDetail != null) ? $spesifikaiDetail['satuan_2'] : '';
            $result[$i]['satuan_3'] = ($spesifikaiDetail != null) ? $spesifikaiDetail['satuan_3'] : '';
            $result[$i]['nama_satuan'] = ($spesifikaiDetail != null) ? $spesifikaiDetail['nama_satuan'] : '';
            $result[$i]['kode_satuan'] = ($spesifikaiDetail != null) ? $spesifikaiDetail['kode_satuan'] : '';
        }

        return $result;
    }
}
