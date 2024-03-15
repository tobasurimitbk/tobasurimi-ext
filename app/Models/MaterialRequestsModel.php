<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequestsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'material_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'work_order_id',
        'company_id',
        'divisi_id',
        'warehouse_id',
        'production_date',
        'request_date',
        'req_no',
        'is_posted',
        'createdBy',
        'is_approve',
        'approveBy',
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

    public function getMaterialRequestList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'req_no'             => 'material_requests.req_no',
            'department'       => 'divisis.divisi',
            'nama_barang'       => 'material_request_details.nama_barang',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_requests.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "material_requests.*,
            material_request_details.nama_barang,
            material_request_details.satuan,
            SUM(material_request_details.qty) as total,
            divisis.divisi,
            warehouses.warehouse_name,
        ";

        $materialRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = material_requests.divisi_id')
            ->join('warehouses', 'warehouses.id = material_requests.warehouse_id')
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id')
            ->groupBy('material_requests.id')
            ->orderBy($sort, $sortType);

        $totalData = $materialRequestsDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $materialRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry
                ->like('material_requests.req_no', $addCondition['search'])
                ->orLike('material_request_details.kode_barang', $addCondition['search'])
                ->orLike('material_request_details.nama_barang', $addCondition['search']);
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry->groupEnd();
        }

        $totalFilteredData = $materialRequestsDataQry->countAllResults(false);
        $data = $materialRequestsDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getMaterialRequestListForMaterialWarehouse($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'req_no'             => 'material_requests.req_no',
            'department'       => 'divisis.divisi',
            'nama_barang'       => 'material_request_details.nama_barang',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_requests.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "material_requests.id, material_requests.request_date, 
            material_requests.req_no, 
            material_requests.is_posted, 
            material_requests.is_approve, 
            work_orders.wo_no, 
            material_request_details.nama_barang,
            material_request_details.satuan,
            material_request_details.ref_no,
            material_request_details.stock_date,
            material_request_details.note,
            SUM(material_request_details.qty) as total,
            divisis.divisi,
            warehouses.warehouse_name,
            users.name,
        ";

        $materialRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = material_requests.divisi_id')
            ->join('warehouses', 'warehouses.id = material_requests.warehouse_id')
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id')
            ->join('work_orders', 'work_orders.id = material_requests.work_order_id')
            ->join('users', 'users.id = work_orders.createdBy')
            ->groupBy('material_requests.id')
            ->orderBy($sort, $sortType);

        $totalData = $materialRequestsDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $materialRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry
                ->like('material_requests.req_no', $addCondition['search'])
                ->orLike('work_orders.wo_no', $addCondition['search'])
                ->orLike('material_request_details.nama_barang', $addCondition['search']);
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry->groupEnd();
        }

        $totalFilteredData = $materialRequestsDataQry->countAllResults(false);
        $data = $materialRequestsDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getMaterialRequestListForMaterialWarehouseDetail($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'req_no'             => 'material_requests.req_no',
            'department'       => 'divisis.divisi',
            'nama_barang'       => 'material_request_details.nama_barang',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_requests.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "material_requests.id, material_requests.request_date, 
            material_requests.req_no, 
            work_orders.wo_no, 
            material_request_details.nama_barang,
            material_request_details.satuan,
            material_request_details.ref_no,
            material_request_details.stock_date,
            material_request_details.note,
            material_request_details.qty as total,
            divisis.divisi,
            warehouses.warehouse_name,
            users.name,
        ";

        $materialRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = material_requests.divisi_id')
            ->join('warehouses', 'warehouses.id = material_requests.warehouse_id')
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'right')
            ->join('work_orders', 'work_orders.id = material_requests.work_order_id')
            ->join('users', 'users.id = work_orders.createdBy')
            ->orderBy($sort, $sortType);

        $totalData = $materialRequestsDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $materialRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry
                ->like('material_requests.req_no', $addCondition['search'])
                ->orLike('work_orders.wo_no', $addCondition['search'])
                ->orLike('material_request_details.nama_barang', $addCondition['search']);
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry->groupEnd();
        }

        $totalFilteredData = $materialRequestsDataQry->countAllResults(false);
        $data = $materialRequestsDataQry->findAll($limit, $offset);

        // var_dump($data);
        // exit;

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function get_no($tgl, $bln, $thn, $last_day)
    {
        $romanNumb = [
            'I',
            'II',
            'III',
            'IV',
            'V',
            'VI',
            'VII',
            'VIII',
            'IX',
            'X',
            'XI',
            'XII',
        ];

        $lastStr =  $romanNumb[$bln - 1] . '/' . $thn;

        $builder = $this->db->table('material_requests');
        $builder->select('req_no');
        $builder->orderBy('req_no', 'desc')
            ->where('createdAt >=', $thn . "-" . $bln . "-" . $tgl . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('req_no', $lastStr);
        $query = $builder->get();

        $kode = 'MR';

        $lastWO = '0001';
        if ($query->getResultArray()) {
            $lastWO = explode('/', $query->getResultArray()[0]['req_no']);
            $lastWO = intval($lastWO[3]) + 1;

            $lastWO = sprintf("%04d", $lastWO);
        };

        $generatedNo = $kode . '/' . $lastStr . '/' . $lastWO;

        return $generatedNo;
    }

    public function getMaterialWithWorkOrder($id)
    {
        $selectQry = "material_requests.*, work_orders.wo_no, work_orders.standart_production, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR ', ') AS nama_barang";

        $data = $this->asObject()
            ->select($selectQry)
            ->join('work_orders', 'work_orders.id = material_requests.work_order_id')
            ->join('work_order_details', 'work_order_details.work_order_id = material_requests.work_order_id')
            ->where('material_requests.id', $id)
            ->groupBy('work_order_details.work_order_id')
            ->get()
            ->getResult();

        // var_dump($data);
        // exit;

        return $data;
    }
}
