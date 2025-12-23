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
        'note_approve',
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
            'req_no'        => 'material_requests.req_no',
            'wo_no'         => 'work_orders.wo_no',
            'barangName'    => 'work_order_details.nama_barang',
            'createdAt'    => 'material_requests.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_requests.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "material_requests.*,
            SUM(material_request_details.qty) as total,
            GROUP_CONCAT(DISTINCT work_orders.wo_no ORDER BY work_orders.wo_no SEPARATOR ', ') AS wo_no,
            GROUP_CONCAT(DISTINCT work_order_details.nama_barang ORDER BY work_order_details.nama_barang SEPARATOR ', ') AS barangName
        ";

        $materialRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id')
            ->join(
                'work_orders',
                'FIND_IN_SET(work_orders.id, material_requests.work_order_id)',
                'left'
            )
            ->join(
                'work_order_details',
                'FIND_IN_SET(work_order_details.work_order_id, material_requests.work_order_id)',
                'left'
            )
            ->join(
                'barang_master',
                'barang_master.id = work_order_details.barang1_id',
                'left'
            )
            ->where('material_requests.deletedAt', null)
            ->where('material_request_details.deletedAt', null)
            ->groupBy('material_request_details.material_request_id')
            ->orderBy($sort, $sortType);

        $totalData = $materialRequestsDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $materialRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry
                ->like('material_requests.req_no', $addCondition['search']);
            // ->orLike('material_request_details.nama_barang', $addCondition['search']);
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
            users.name as namaUser,
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

    public function get_no($tgl, $bln, $thn, $last_day, $company_id)
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
            ->where('deletedAt', null)
            ->where('company_id', $company_id)
            ->where('request_date >=', $thn . "-" . $bln . "-01")
            ->where('request_date <=', $last_day);
        $builder->groupStart();
        $builder->like('req_no', $lastStr);
        $builder->groupEnd();
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
        $selectQry = "material_requests.*, work_orders.wo_no, work_orders.standart_production, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR ', ') AS nama_barang, work_orders.is_posted AS posted_wo, work_orders.request_status AS status_wo";

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

    public function getMaterial($id)
    {
        $selectQry = "material_requests.*";

        $data = $this->asObject()
            ->select($selectQry)
            ->where('material_requests.id', $id)
            ->get()
            ->getResult();

        // var_dump($data);
        // exit;

        return $data;
    }


    public function getAllMaterialRequestReport($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'req_no'             => 'material_requests.req_no',
            'department'       => 'divisis.divisi',
            'nama_barang'       => 'material_request_details.nama_barang',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_requests.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "material_requests.id, 
            material_requests.request_date, 
            material_requests.req_no, 
            work_orders.wo_no,
            material_request_details.nama_barang,
            material_request_details.satuan,
            material_request_details.ref_no,
            material_request_details.qty2,
            divisis.divisi,
            stock.tipe_barang,    
            stock.barang1_id,    
            stock.barang2_id  
              
        ";

        $materialRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where('material_requests.is_posted', '1')
            ->where('material_requests.company_id', $addCondition['company_id'])
            ->join('material_request_details', 'material_request_details.material_request_id = material_requests.id', 'left')
            ->join('work_orders', 'work_orders.id = material_requests.work_order_id', 'left')
            ->join('divisis', 'divisis.id = material_request_details.divisi_tujuan_id', 'left')
            ->join('stock', 'stock.id = material_request_details.stock_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $materialRequestsDataQry->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_tipe_barang']) {
            $materialRequestsDataQry->groupStart();
        }


        if ($addCondition['dateStart']) {
            $materialRequestsDataQry->where('material_requests.request_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $materialRequestsDataQry->where('material_requests.request_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['filter_tipe_barang']) {
            $materialRequestsDataQry->where('material_request_details.barang_type', $addCondition['filter_tipe_barang']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['filter_tipe_barang']) {
            $materialRequestsDataQry->groupEnd();
        }

        $totalFilteredData = $materialRequestsDataQry->countAllResults(false);
        $data = $materialRequestsDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }
}
