<?php

namespace App\Models;

use CodeIgniter\Model;

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

        public function getPembayaranPanjarDetails($id){
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

        public function getTotalPembayaranPanjar($panjarId){
            $condition = [
                'deletedAt' => null,
                'panjar_id' => $panjarId
            ];
            $selectQry = "sum(bayar_panjar) as total_bayar_panjar";
            $totalBayarPanjar = $this
            ->select($selectQry)
            ->where($condition)
            ->first();

            return $totalBayarPanjar;

        }

        public function getPembayaranPanjarDetailsbyPanjarId($id){
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



            foreach($list as $l){
                $l->multiple_lpb_no = json_decode($l->multiple_lpb_no);
                $l->total_panjar = number_format($l->total_panjar, 2);
                $l->bayar_panjar = number_format($l->bayar_panjar, 2);
                $l->payment_date = date('d/m/Y', strtotime($l->payment_date));

            }

           
            return $list;

        }

        
        
}