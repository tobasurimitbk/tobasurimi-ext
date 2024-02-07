<?php

namespace App\Models;

use CodeIgniter\Model;

class AMPurchaseOrderDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'am_purchase_order_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'am_purchase_order_id',
        'spesifikasi_id',
        'barang_id',
        'item_desc',
        'note',
        'unit',
        'qty',
        'price',
        'disc',
        'additional_cost',
        'ppn',
        'pph',
        'remaining_qty',
        'qty_diterima',
        'total'
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
            'am_purchase_order_details.deletedAt' => null,
            'am_purchase_order_details.am_purchase_order_id' => $id
        ];

        $selectQry = "am_purchase_order_details.*,
            am_purchase_orders.po_no,
            am_purchase_orders.status_penerimaan,
            (am_purchase_order_details.qty * am_purchase_order_details.price) AS totalPriceWithoutAdditional,
            (am_purchase_order_details.qty * am_purchase_order_details.price + am_purchase_order_details.additional_cost) AS totalPrice,
            am_purchase_order_details.price AS price,
            am_purchase_order_details.additional_cost AS additional_cost,
            barang_master.barang_name as nama_barang, 
            am_purchase_order_details.note AS spp_note,
            barang_master.kode_barang,
            taxppn.tax_value as ppnValue, 
            taxpph.tax_value as pphValue, 
            satuans.id as id_satuan, 
            satuans.kode_satuan,
            satuans.nama_satuan";

        $builder = $this->db->table('am_purchase_order_details')
            ->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('taxes AS taxppn', 'taxppn.id = am_purchase_order_details.ppn', 'left')
            ->join('taxes AS taxpph', 'taxpph.id = am_purchase_order_details.pph', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailById($id)
    {
        $arrCondition = [
            'am_purchase_order_details.deletedAt' => null,
            'am_purchase_order_details.id' => $id
        ];

        $selectQry = "am_purchase_order_details.*,
            am_purchase_orders.po_no,
            purchase_requests.spp_no,
            am_purchase_orders.status_penerimaan,
            (am_purchase_order_details.qty * am_purchase_order_details.price) AS totalPriceWithoutAdditional,
            (am_purchase_order_details.qty * am_purchase_order_details.price + am_purchase_order_details.additional_cost) AS totalPrice,
            (am_purchase_order_details.price) AS price,
            (am_purchase_order_details.additional_cost) AS additional_cost,
            barang_master.barang_name as nama_barang, 
            barang_master.kode_barang,
            am_purchase_order_details.note AS spp_note,
            taxppn.tax_value as ppnValue, 
            taxpph.tax_value as pphValue, 
            satuans.id as id_satuan, 
            satuans.nama_satuan";

        $builder = $this->db->table('am_purchase_order_details')
            ->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->join('taxes AS taxppn', 'taxppn.id = am_purchase_order_details.ppn', 'left')
            ->join('taxes AS taxpph', 'taxpph.id = am_purchase_order_details.pph', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getListLPBBahanPenolong($amPurchaseOrderID, $penerimaanBarangID = null)
    {
        $res = [];
        $condition = [
            'am_purchase_orders.deletedAt' => null,
            'am_purchase_order_details.deletedAt' => null
        ];

        $selectQry = "
            am_purchase_orders.po_no,
            am_purchase_order_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            satuans.kode_satuan
        ";

        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $barangs = $amPurchaseOrderModel
            ->select($selectQry)
            ->where($condition)
            ->whereIn('am_purchase_orders.id', $amPurchaseOrderID)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->findAll();


        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaPerBarangTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {

            $allLPB = $penerimaanBarangDetailModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->where('purchase_order_id', $b['am_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('deletedAt', null)
                ->groupBy('purchase_order_id', 'purchase_order_details_id')
                ->findAll();

            $jmlMasukAll = 0;
            foreach ($allLPB as $a) {
                $jmlMasukAll = $a['jmlMasuk'];
            }

            $firstLPB =  $penerimaanBarangDetailModel->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('purchase_order_id', $b['am_purchase_order_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            $diskonHarga = ($b['disc'] / 100) * ($b['price']);
            $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

            $res[] = [
                'am_purchase_order_details_id' => $b['id'],
                'am_purchase_order_id' => $b['am_purchase_order_id'],
                'kode_barang' => $b['kode_barang'],
                'nama_barang' => $b['nama_barang'],
                'po_no' => $b['po_no'],
                'satuan' => $b['kode_satuan'],
                'jml_order' => $b['qty'],
                'jml_diterima_lpb' => $inLPB,
                'jml_diterima_total' => $jmlMasukAll,
                'sisa_total' => $sisaDiterima,
                'harga' => $harga,
                'sub_total' => ($inLPB * $harga),
                'keterangan' => $b['note']
            ];

            $jmlOrderTotal += $b['qty'];
            $jmlDiterimaInTotal += $inLPB;
            $jmlDiterimaTotal +=   $jmlMasukAll;
            $sisaDiterimaTotal += $sisaDiterima;
            $hargaPerBarangTotal += $harga;
            $subTotal += ($inLPB * $harga);
        }

        return [
            'result' => $res,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_per_barang_total' => $hargaPerBarangTotal,
            'sub_total' => $subTotal
        ];
    }

    public function getPoBPDetailById($id)
    {
        $selectQry = "am_purchase_order_details.*";

        $condition = [
            "am_purchase_order_id" => $id,
        ];

        $poBPDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->findAll();

        return $poBPDetailData;
    }
}
