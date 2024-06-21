<?php

namespace App\Models;

use CodeIgniter\Model;

class BC30Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_30';
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

    public function getListSalesOrder($companyId, $tipeSalesOrder)
    {
        $salesOrderLokalModel = new SalesOrderModel();

        if ($tipeSalesOrder == "LOKAL") {
            // LOKAL
            $salesOrder = $salesOrderLokalModel
                ->where('used', "USED")
                ->where('company_id', $companyId)
                ->where('deletedAt', null)
                ->findAll();
            $result = array();
            foreach ($salesOrder as $s) {
                $bc23 = $this->where('sales_order_id', $s['id'])->where('tipe_sales_order', "LOKAL")->first();
                if ($bc23 == null) {
                    array_push($result, $s);
                }
            }
        } else {
            // INTERNASIONAL
        }

        return $result;
    }
}
