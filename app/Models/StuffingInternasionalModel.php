<?php

namespace App\Models;

use CodeIgniter\Model;

class StuffingInternasionalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'stuffing_internasional';
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
            'no_stuffing' => 'stuffing_internasional.no_stuffing',
            'no_sales_order' => 'sales_order_export.sales_order_export_no',
            'createdAt' => 'stuffing_internasional.createdAt',
            'customer_name' => 'stuffing_internasional.customer_name',
            'status_closed' => 'status_closed'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "stuffing_internasional.*,
        customers.name as customer_name,
        sales_order_export.sales_order_export_no AS no_sales_order
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = stuffing_internasional.customer_id', 'left')
            ->join('sales_order_export', 'stuffing_internasional.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['status'] || $addCondition['no_stuffing'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupStart();
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['no_stuffing']) {
            $dataQry->like('no_stuffing', $addCondition['no_stuffing'])->orLike('no_sales_order', $addCondition['no_stuffing']);
        }

        if ($addCondition['start_date']) {
            $dataQry->where('tanggal >=', $addCondition['start_date']);
        }

        if ($addCondition['end_date']) {
            $dataQry->where('tanggal <=', $addCondition['end_date']);
        }

        if ($addCondition['status'] || $addCondition['no_stuffing'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function get_no($bln, $thn, $last_day)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('stuffing_internasional');
        $builder->select('no_stuffing');
        $builder->orderBy('no_stuffing', 'desc');
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_stuffing', $lastStr);
        $query = $builder->get();

        $kode = 'STUFFING-INT';

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_stuffing']);
                $number = intval($explode[2]);

                if ($number > $lastPenerimaan) {
                    $lastPenerimaan = $number;
                }
            }
            $lastPenerimaan++;
        }

        $formattedLastPenerimaan = sprintf("%02d", $lastPenerimaan);
        $generatedNo = $kode . '/' . $formattedLastPenerimaan . '/' . $lastStr;

        return $generatedNo;
    }
}
