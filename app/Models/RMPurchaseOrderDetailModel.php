<?php

namespace App\Models;

use CodeIgniter\Model;

class RMPurchaseOrderDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_purchase_order_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rm_purchase_order_id',
        'supplier_harga_id',
        'satuan_id',
        'bagian',
        'peti',
        'quality',
        'note',
        'qty',
        'qty_diterima',
        'remaining_qty',
        'general_price',
        'daily_price',
        'monthly_price',
        'createdAt',
        'updatedAt',
        'deletedAt',
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

    public function getPurchaseOrderDetailByPurchaseOrderId($id)
    {
        $arrCondition = [
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.rm_purchase_order_id' => $id
        ];

        $builder = $this->db->table('rm_purchase_order_details')
            ->select('rm_purchase_orders.po_no,
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, supplier_harga.spesifikasi, bagian.nama_bagian, 
            barang_master.kode_barang, barang_master.barang_name as nama_barang, barang_master.id as barang_id, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('barang_master', 'supplier_harga.bahan_baku_id = barang_master.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left')
            ->join('bagian', 'rm_purchase_order_details.bagian = bagian.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailById($id)
    {
        $arrCondition = [
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.id' => $id
        ];

        $builder = $this->db->table('rm_purchase_order_details')
            ->select('rm_purchase_orders.po_no,
            rm_purchase_orders.status_penerimaan, rm_purchase_order_details.*, supplier_harga.spesifikasi, bagian.nama_bagian, satuans.id as id_satuan, satuans.nama_satuan')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left')
            ->join('bagian', 'rm_purchase_order_details.bagian = bagian.id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getPoBBLokalDetailById($id)
    {
        $selectQry = "rm_purchase_order_details.*, supplier_harga.spesifikasi, bagian.nama_bagian, satuans.id as id_satuan, satuans.nama_satuan";

        $condition = [
            "rm_purchase_order_id" => $id,
        ];

        $poBBLokalDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('supplier_harga', 'rm_purchase_order_details.supplier_harga_id = supplier_harga.id', 'left')
            ->join('satuans', 'rm_purchase_order_details.satuan_id = satuans.id', 'left')
            ->join('bagian', 'rm_purchase_order_details.bagian = bagian.id', 'left')
            ->findAll();

        return $poBBLokalDetailData;
    }

    public function getListLPBBahanBaku($rmPurchaseOrderID, $penerimaanBarangID = null)
    {
        $res = [];
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null
        ];

        $selectQry = "
            rm_purchase_orders.po_no,
            rm_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            satuans.kode_satuan,
            supplier_harga.spesifikasi
        ";

        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $barangs = $rmPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            ->whereIn('rm_purchase_orders.id', $rmPurchaseOrderID)
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->findAll();


        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaHarianTotal = 0;
        $hargaBulananTotal = 0;
        $hargaUmumTotal = 0;
        $hargaSumTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {

            $allLPB = $penerimaanBarangDetailModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->where('purchase_order_id', $b['rm_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('deletedAt', null)
                ->groupBy('purchase_order_id', 'purchase_order_details_id')
                ->findAll();

            $jmlMasukAll = 0;
            foreach ($allLPB as $a) {
                $jmlMasukAll = $a['jmlMasuk'];
            }

            $firstLPB =  $penerimaanBarangDetailModel->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('purchase_order_id', $b['rm_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            $res[] = [
                'rm_purchase_order_details_id' => $b['id'],
                'rm_purchase_order_id' => $b['rm_purchase_order_id'],
                'kode_barang' => $b['kode_barang'],
                'nama_barang' => $b['nama_barang'] . ' (' . $b['spesifikasi'] . ')',
                'po_no' => $b['po_no'],
                'satuan' => $b['kode_satuan'],
                'jml_order' => $b['qty'],
                'jml_diterima_lpb' => $inLPB,
                'jml_diterima_total' => $jmlMasukAll,
                'sisa_total' => $sisaDiterima,
                'harga_umum' => $b['general_price'],
                'harga_harian' => $b['daily_price'],
                'harga_bulanan' => $b['monthly_price'],
                'harga_sum' => ($b['general_price'] + $b['daily_price'] + $b['monthly_price']),
                'sub_total' => ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price'])),
                'keterangan' => $b['note']
            ];

            $jmlOrderTotal += $b['qty'];
            $jmlDiterimaInTotal += $inLPB;
            $jmlDiterimaTotal +=   $jmlMasukAll;
            $sisaDiterimaTotal += $sisaDiterima;
            $hargaHarianTotal += $b['daily_price'];
            $hargaBulananTotal += $b['monthly_price'];
            $hargaUmumTotal += $b['general_price'];
            $hargaSumTotal +=  ($b['general_price'] + $b['daily_price'] + $b['monthly_price']);
            $subTotal += ($inLPB * ($b['general_price'] + $b['daily_price'] + $b['monthly_price']));
        }

        return [
            'result' => $res,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_umum_total' => $hargaUmumTotal,
            'harga_harian_total' => $hargaHarianTotal,
            'harga_bulanan_total' => $hargaBulananTotal,
            'harga_sum_total' => $hargaSumTotal,
            'sub_total' => $subTotal
        ];
    }
}
