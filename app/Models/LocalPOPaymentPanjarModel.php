<?php

namespace App\Models;

use CodeIgniter\Model;
use PHPUnit\TextUI\XmlConfiguration\Group;

class LocalPOPaymentPanjarModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_payment_panjar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'company_id',
        'local_po_payment_id',
        'type',
        'panjar_id',
        'bayar_panjar'
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

    public function getPembayaranPanjarDetails($id)
    {
        $condition = [
            'local_po_payments.id ' => $id,

        ];
        $selectQry = "no_panjar, panjar_supplier.payment_date, total_panjar, bayar_panjar, panjar_supplier.id as panjar_id";

        $list = $this->asObject()
            ->select($selectQry)
            ->join('local_po_payments', 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id', 'inner')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id', 'inner')
            ->where($condition)
            ->findAll();



        return $list;
    }

    public function getTotalPembayaranPanjar($panjarId, $type)
    {
        $condition = [
            'deletedAt' => null,
            'panjar_id' => $panjarId,
            'type'  => $type
        ];
        $selectQry = "sum(bayar_panjar) as total_bayar_panjar";
        $totalBayarPanjar = $this
            ->select($selectQry)
            ->where($condition)
            ->first();

        return $totalBayarPanjar;
    }

    public function getPembayaranPanjarDetailsbyPanjarId($id)
    {
        $condition = [
            'panjar_id' => $id
        ];

        $selectQry = "no_panjar,total_panjar, bayar_panjar, panjar_supplier.supplier_id, multiple_lpb_no, name, local_po_payments.payment_date";
        $list = $this->asObject()
            ->select($selectQry)
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id', 'inner')
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'inner')
            ->join('local_po_payments', 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id', 'inner')
            ->where($condition)
            ->findAll();



        foreach ($list as $l) {
            $l->multiple_lpb_no = json_decode($l->multiple_lpb_no);
            $l->total_panjar = number_format($l->total_panjar, 2);
            $l->bayar_panjar = number_format($l->bayar_panjar, 2);
            $l->payment_date = date('d/m/Y', strtotime($l->payment_date));
        }


        return $list;
    }

    public function getPembayaranPanjarDetailsbyIdandType($id, $type)
    {
        $condition = [
            'local_po_payment_panjar.local_po_payment_id' => $id,
            'type'  => $type
        ];

        $selectQry = "local_po_payment_panjar.id, panjar_supplier.no_panjar, panjar_supplier.payment_date, panjar_supplier.total_panjar, local_po_payment_panjar.panjar_id,
        local_po_payment_panjar.bayar_panjar, sum(local_po_payment_panjar.bayar_panjar) as total_bayar_panjar";

        $result = $this
            ->select($selectQry)
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
            ->join('local_po_payment_bp', 'local_po_payment_bp.id = local_po_payment_panjar.local_po_payment_id')
            ->groupBy('local_po_payment_panjar.panjar_id')
            ->where($condition)
            ->findAll();

        foreach ($result as $i => $r) {
            $totalPembayaranPanjar = $this->getTotalPembayaranPanjar($r['panjar_id'], "BP");
            $result[$i]['payment_date'] = date('d/m/Y', strtotime($r['payment_date']));


            $result[$i]['total_pembayaran'] = $totalPembayaranPanjar;
        }



        return $result;
    }
}
