<?php

namespace App\Models;

use CodeIgniter\Model;

class ProformaInvoiceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'proforma_invoice';
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
            'proforma_invoice.id'     => 'proforma_invoice.id',
            'proforma_invoice.no_pi'     => 'proforma_invoice.no_pi',
            'proforma_invoice.tanggal_pi'     => 'proforma_invoice.tanggal_pi',
            'proforma_invoice.total_pi'               => 'proforma_invoice.total_pi',
            'proforma_invoice.status_exim'               => 'proforma_invoice.status_exim',
            'proforma_invoice.status_bayar'               =>  'proforma_invoice.status_bayar',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'proforma_invoice.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "proforma_invoice.*,
        metadata.value as valas_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('metadata', 'metadata.id = proforma_invoice.valas_id', 'left')
            ->where($condition);

        $totalData = $dataQry->countAllResults(false);

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $dataQry->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $dataQry->where('tanggal_pi >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $dataQry->where('tanggal_pi <=', $addCondition['dateEnd']);
            }
            $dataQry->groupEnd();
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] == "SUDAH POSTING") {
                $dataQry
                    ->where('proforma_invoice.status_posting', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING") {
                $dataQry
                    ->where('proforma_invoice.status_posting', 0);
            }
        }

        if (isset($addCondition['search']) && !empty($addCondition['search'])) {
            $dataQry->groupStart()
                ->like('proforma_invoice.no_pi', $addCondition['search'])
                ->orLike('proforma_invoice.total_pi', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);

        $data = $dataQry->orderBy($sort, $sortType)
            ->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }
    public function generateNo($companyId)
    {
        $template = "";
        $companyIdArr = [];

        if ($companyId == 1 || $companyId == 2) {
            $template = "TSI-PI";
            $companyIdArr = [1, 2];
        } elseif ($companyId == 15) {
            $template = "GPS-PI";
            $companyIdArr = [15];
        } else {
            $template = "OCS-PI";
            $companyIdArr = [16];
        }

        $lastStr = "/" . $template . "/" . date('y'); // contoh: /TSI-PI/25
        $year = date('Y');

        $dataQry = $this->select('no_pi')
            ->orderBy('no_pi', "desc")
            ->where('createdAt >=', $year . "-01-01 00:00:00")
            ->where('createdAt <=', $year . "-12-31 23:59:59")
            ->whereIn('company_id', $companyIdArr)
            ->first();

        if ($dataQry != null) {
            $lastFirst = explode('/', $dataQry['no_pi']);
            $lastCounter = $lastFirst[0]; // ambil 3 digit pertama
            $newCounter = intval($lastCounter) + 1;
            $newCounter = sprintf("%03d", $newCounter); // format jadi 3 digit
        } else {
            $newCounter = sprintf("%03d", 1); // format juga ke 3 digit
        }

        $number = $newCounter . $lastStr;
        return $number;
    }

    public function getAllSalesOrderProformaInvoice($company_id)
    {
        $selectQry = "proforma_invoice.no_pi as no_faktur, 
                        proforma_invoice.total_pi as total_invoice,
                        proforma_invoice.tanggal_pi as tanggal_faktur,
                        companies.company,
                        metadata.value AS valas_name,
                        users.name AS acc_holder,
                        customers.name AS customer_name";
        $salesDataQry = $this->asObject()
            ->select($selectQry)
            ->join('companies', 'proforma_invoice.company_id = companies.id', 'left')
            ->join('sales_contract', 'sales_contract.id = proforma_invoice.sales_contract_id', 'left')
            ->join('sales_order_export', 'sales_order_export.sales_contract_id = sales_contract.id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('metadata', 'proforma_invoice.valas_id = metadata.id', 'left')
            ->join('users', 'users.id = sales_order_export.user_id', 'left')
            ->where('proforma_invoice.status_posting', 1)
            ->where('proforma_invoice.status_bayar', 0)
            ->where('proforma_invoice.company_id', $company_id)
            ->groupBy('proforma_invoice.id');

        $totalData = $salesDataQry->countAllResults(false);

        $totalFilteredData = $salesDataQry->countAllResults(false);
        $data = $salesDataQry->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
