<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderReturnModel extends Model
{

    protected $table      = 'sales_order_return';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;

    protected $allowedFields = [
        'id_user',
        'id_invoice',
        'no_return',
        'tanggal_return',
        'note',
        'id_company',
        'is_approved',
        'id_warehouse',
        'already_paid',
        'sumber',
        'id_customer'
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

    public function getAllReturn($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_return'         => 'sales_order_return.no_return',
            'kode_customer'    => 'customers.kode',
            'nama_customer'    => 'customers.name',
            'shipping_date'     => 'sales_order_return.tanggal_return',
            'createdAt'         => 'sales_order_return.createdAt'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'sales_order_return.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_return.id AS id,
                      sales_order_return.no_return AS returnNo,
                      sales_order_return.tanggal_return AS returnDate,
                      sales_order_return.already_paid AS already_paid,
                      sales_order_return.is_approved AS is_approved,
                        CASE 
                            WHEN sales_order_return.sumber = 'invoice' THEN sales_order_invoice.no_faktur
                            WHEN sales_order_return.sumber = 'surat_jalan' THEN surat_jalan_so.no_surat_jalan
                            WHEN sales_order_return.sumber = 'order_form' THEN sales_order.no_sales_order
                            ELSE '-'
                        END AS refNo,
                        CASE 
                            WHEN sales_order_return.sumber = 'invoice' THEN csoi.name
                            WHEN sales_order_return.sumber = 'surat_jalan' THEN csj.name
                            WHEN sales_order_return.sumber = 'order_form' THEN cso.name
                            ELSE '-'
                        END AS customerName";

        $soReturn = $this->asObject()
            ->select($selectQry)
            ->join('sales_order_invoice', 'sales_order_invoice.id = sales_order_return.id_invoice', 'left')
            ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order_return.id_invoice', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_return.id_invoice', 'left')
            ->join('customers cso', 'cso.id = sales_order.id_customer', 'left')
            ->join('customers csj', 'csj.id = surat_jalan_so.id_customer', 'left')
            ->join('customers csoi', 'csoi.id = sales_order_invoice.id_customer', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $soReturn->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $soReturn->groupStart();
        }
        if ($addCondition['search']) {
            $soReturn
                ->like('sales_order_return.no_return', $addCondition['search'])
                ->orLike('sales_order_invoice.no_faktur', $addCondition['search']);
        }
        if ($addCondition['dateStart']) {
            $soReturn->where('sales_order_return.tanggal_return >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $soReturn->where('sales_order_return.tanggal_return <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $soReturn->groupEnd();
        }

        $totalFilteredData = $soReturn->countAllResults(false);

        if ($limit && $offset) {
            $data = $soReturn->findAll($limit, $offset);
        } else {
            $data = $soReturn->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function generateNoReturn(): string
    {
        $format = "RETURN";
        $month = idate('m');
        $year = date('Y');
        $formatMonth = str_pad($month, 2, 0, STR_PAD_LEFT);
        $numberTemplate = "/$year/$formatMonth/";

        $lastData = $this->asObject()
            ->where("no_return LIKE '%$numberTemplate%'")
            ->orderBy('createdAt', 'DESC')
            ->first();

        $dummyNum = 0;
        if (!empty($lastData)) {
            $asd = explode('/', $lastData->no_return);
            foreach ($asd as $key => $item) {
                if ($key === 3) {
                    if (preg_match('/^(.*?)(\d+)$/', $item, $matches)) {
                        $prefix = $matches[1]; // "inv"
                        $number = $matches[2]; // "nomer invoice"
                    }
                }
            }
            $numbers = $number + 1; // increment nomer invoice

            $invNumber = $format . $numberTemplate . $numbers;
        } else {
            $numbers = 1; // nomer invoice awal jika tidak ada data

            $invNumber = $format . $numberTemplate . $numbers;
        }

        return $invNumber;
    }
}
