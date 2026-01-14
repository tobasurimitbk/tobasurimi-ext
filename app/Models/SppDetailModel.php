<?php

namespace App\Models;

use CodeIgniter\Model;

class SppDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'purchase_request_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "purchase_request_id",
        "barang1_id",
        "barang2_id",
        "nama_barang",
        "qty",
        "unit",
        "note"
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

    public function getSppDetailById($id)
    {
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();

        $selectQry = "purchase_request_details.*,
                    barang_master.kode_barang,
                    satuans.nama_satuan AS nama_satuan,
                    satuans.kode_satuan AS kode_satuan,
                    barang_master.barang_name,
                    barang_master_spesifikasi.spesifikasi";

        $condition = [
            "purchase_request_details.purchase_request_id" => $id,
        ];

        $sppDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barang_master', 'purchase_request_details.barang1_id = barang_master.id', 'left')
            ->join('satuans', 'purchase_request_details.unit = satuans.id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = purchase_request_details.barang2_id', 'left')
            ->where('purchase_request_details.deletedAt', null)
            ->findAll();

        foreach ($sppDetailData as $i => $d) {
            $poRes = $amPurchaseOrderDetailModel
                ->select('am_purchase_orders.po_no,am_purchase_order_details.*')
                ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
                ->where('am_purchase_order_details.purchase_request_detail_id', $d->id)
                ->where('am_purchase_orders.purchase_request_id', $id)
                ->where('am_purchase_order_details.deletedAt', null)
                ->where('am_purchase_orders.is_posted', 1)
                ->first();
            $sppDetailData[$i]->status_po = $poRes == null ? false : true;
        }

        return $sppDetailData;
    }

    public function getDetailByPo($sppId, $barang1Id, $barang2Id)
    {
        $result = $this->asArray()->where('purchase_request_id', $sppId)
            ->where('barang1_id', $barang1Id)
            ->where('barang2_id', $barang2Id)
            ->where('deletedAt', null)
            ->first();

        return $result;
    }

    public function updateNoteDetailSpp($amPurchaseOrderId)
    {
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        // get po detail
        $dataPoDetail = $amPurchaseOrderDetailModel
            ->where('am_purchase_order_id', $amPurchaseOrderId)
            ->where('deletedAt', null)
            ->findAll();

        $dataSppDetailUpdate = [];
        foreach ($dataPoDetail as $d) {
            array_push($dataSppDetailUpdate, [
                'id' => $d['purchase_request_detail_id'],
                'note' => $d['note']
            ]);
        }

        if (count($dataSppDetailUpdate) > 0) {
            $this->updateBatch($dataSppDetailUpdate, 'id');
        }
    }
}
