<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkOrdersModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'work_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'wo_no',
        'company_id',
        'divisi_id',
        'warehouse_id',
        'request_date',
        'standart_production',
        'note',
        'is_posted',
        'request_status',
        'createdBy',
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

    public function getWorkOrderList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'request_date'           => 'request_date',
            'wo_no'                  => 'work_orders.wo_no',
            'department'             => 'divisis.divisi',
            'barang1_id'             => 'barang1_id',
            'warehouse_id'           => 'work_orders.warehouse_id',
            'work_order_details.qty' => 'work_order_details.qty',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'work_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "work_orders.*,
            work_order_details.qty,
            divisis.divisi,
            warehouses.warehouse_name,
            barang_master.barang_name
        ";

        $workOrdersDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = work_orders.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = work_orders.warehouse_id', 'left')
            ->join('work_order_details', 'work_order_details.work_order_id = work_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = work_order_details.barang1_id', 'left')
            ->groupBy('work_orders.id')
            ->orderBy($sort, $sortType);

        $totalData = $workOrdersDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $workOrdersDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $workOrdersDataQry
                ->like('work_orders.wo_no', $addCondition['search'])
                ->orLike('work_order_details.nama_barang', $addCondition['search']);
        }
        if ($addCondition['search']) {
            $workOrdersDataQry->groupEnd();
        }

        // Pisahkan filter tanggal dari pencarian agar tidak terkena efek `LIKE`
        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $workOrdersDataQry->groupStart(); // Pastikan tanggal hanya masuk dalam satu blok kondisi
            if (!empty($addCondition['dateStart'])) {
                $workOrdersDataQry->where('request_date >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $workOrdersDataQry->where('request_date <=', $addCondition['dateEnd']);
            }
            $workOrdersDataQry->groupEnd();
        }

        $totalFilteredData = $workOrdersDataQry->countAllResults(false);
        $data = $workOrdersDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function get_no($bln, $thn, $department)
    {
        $romanNumb = [
            'I', 'II', 'III', 'IV', 'V', 'VI',
            'VII', 'VIII', 'IX', 'X', 'XI', 'XII',
        ];

        $first_day = "$thn-$bln-01";
        $last_day = date("Y-m-t", strtotime($first_day));
        $monthRoman = $romanNumb[$bln - 1];
        $lastStr = "{$monthRoman}/{$thn}";

        // Bersihkan spasi: hilangkan semua spasi, bukan hanya trim
        $department = str_replace(' ', '', trim($department));

        $builder = $this->db->table('work_orders');
        $builder->select('wo_no');
        $builder->where('deletedAt IS NULL');
        $builder->where('company_id', session()->get("login")->this_company_id);
        $builder->where('request_date >=', $first_day);
        $builder->where('request_date <=', $last_day);
        $builder->like('wo_no', $lastStr);
        $builder->orderBy('id', "desc");
        $query = $builder->get();

        $kode = 'WO';
        $lastWO = '0001';

        $result = $query->getRowArray();

        if ($result && isset($result['wo_no'])) {
            $lastWO = explode('/', $result['wo_no']);
            $lastWO = intval(end($lastWO)) + 1;
            $lastWO = sprintf("%04d", $lastWO);
        }

        return "{$kode}/{$department}/{$lastStr}/{$lastWO}";
    }

}
