<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequestsPenolongModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'material_requests_penolong';
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
            'req_no'             => 'material_requests_penolong.req_no',
            'wo_no'       => 'work_orders.wo_no',
            'nama_barang'       => 'material_request_penolong_details.nama_barang',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_requests_penolong.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "material_requests_penolong.*,
            GROUP_CONCAT(material_request_penolong_details.nama_barang SEPARATOR ',') AS nama_barang,
            material_request_penolong_details.satuan,
            material_request_penolong_details.kimia,
            SUM(material_request_penolong_details.qty) as total,
            work_orders.wo_no,
            work_orders.request_status,
        ";

        $materialRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('material_request_penolong_details', 'material_request_penolong_details.material_request_id = material_requests_penolong.id')
            ->join('work_orders', 'work_orders.id = material_requests_penolong.work_order_id')
            ->join('barang_master', 'barang_master.id = material_request_penolong_details.barang1_id')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id')
            ->where('material_requests_penolong.deletedAt', null)
            ->where('material_request_penolong_details.deletedAt', null)
            ->groupBy('material_request_penolong_details.material_request_id')
            ->orderBy($sort, $sortType);

        $totalData = $materialRequestsDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $materialRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry
                ->like('material_requests_penolong.req_no', $addCondition['search'])
                ->orLike('material_request_penolong_details.nama_barang', $addCondition['search']);
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
            'req_no'             => 'material_requests_penolong.req_no',
            'department'       => 'divisis.divisi',
            'nama_barang'       => 'material_request_penolong_details.nama_barang',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_requests_penolong.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "material_requests_penolong.id, material_requests_penolong.request_date, 
            material_requests_penolong.req_no, 
            material_requests_penolong.is_posted, 
            material_requests_penolong.is_approve, 
            work_orders.wo_no, 
            material_request_penolong_details.nama_barang,
            material_request_penolong_details.satuan,
            material_request_penolong_details.ref_no,
            material_request_penolong_details.stock_date,
            material_request_penolong_details.note,
            SUM(material_request_penolong_details.qty) as total,
            divisis.divisi,
            warehouses.warehouse_name,
            users.name as namaUser,
        ";

        $materialRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = material_requests_penolong.divisi_id')
            ->join('warehouses', 'warehouses.id = material_requests_penolong.warehouse_id')
            ->join('material_request_penolong_details', 'material_request_penolong_details.material_request_id = material_requests_penolong.id')
            ->join('work_orders', 'work_orders.id = material_requests_penolong.work_order_id')
            ->join('users', 'users.id = work_orders.createdBy')
            ->groupBy('material_requests_penolong.id')
            ->orderBy($sort, $sortType);

        $totalData = $materialRequestsDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $materialRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry
                ->like('material_requests_penolong.req_no', $addCondition['search'])
                ->orLike('work_orders.wo_no', $addCondition['search'])
                ->orLike('material_request_penolong_details.nama_barang', $addCondition['search']);
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
            'req_no'             => 'material_requests_penolong.req_no',
            'department'       => 'divisis.divisi',
            'nama_barang'       => 'material_request_penolong_details.nama_barang',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_requests_penolong.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "material_requests_penolong.id, material_requests_penolong.request_date, 
            material_requests_penolong.req_no, 
            work_orders.wo_no, 
            material_request_penolong_details.nama_barang,
            material_request_penolong_details.satuan,
            material_request_penolong_details.ref_no,
            material_request_penolong_details.stock_date,
            material_request_penolong_details.note,
            material_request_penolong_details.qty as total,
            divisis.divisi,
            warehouses.warehouse_name,
            users.name,
        ";

        $materialRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = material_requests_penolong.divisi_id')
            ->join('warehouses', 'warehouses.id = material_requests_penolong.warehouse_id')
            ->join('material_request_penolong_details', 'material_request_penolong_details.material_request_id = material_requests_penolong.id', 'right')
            ->join('work_orders', 'work_orders.id = material_requests_penolong.work_order_id')
            ->join('users', 'users.id = work_orders.createdBy')
            ->orderBy($sort, $sortType);

        $totalData = $materialRequestsDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $materialRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $materialRequestsDataQry
                ->like('material_requests_penolong.req_no', $addCondition['search'])
                ->orLike('work_orders.wo_no', $addCondition['search'])
                ->orLike('material_request_penolong_details.nama_barang', $addCondition['search']);
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

        $builder = $this->db->table('material_requests_penolong');
        $builder->select('req_no');
        $builder->orderBy('req_no', 'desc')
            ->where('deletedAt', null);
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
        $selectQry = "material_requests_penolong.*, work_orders.wo_no, work_orders.standart_production, GROUP_CONCAT(work_order_details.nama_barang SEPARATOR ', ') AS nama_barang, work_orders.is_posted AS posted_wo, work_orders.request_status AS status_wo";

        $data = $this->asObject()
            ->select($selectQry)
            ->join('work_orders', 'work_orders.id = material_requests_penolong.work_order_id')
            ->join('work_order_details', 'work_order_details.work_order_id = material_requests_penolong.work_order_id')
            ->where('material_requests_penolong.id', $id)
            ->groupBy('work_order_details.work_order_id')
            ->get()
            ->getResult();

        // var_dump($data);
        // exit;

        return $data;
    }

    public function getDataProductionResultBahanPenolongWithDetail($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
        barang_master.kode_barang, 
        barang_master.barang_name, 
        barang_master.parent_type_id, 
        barang_master_spesifikasi.spesifikasi,
        material_request_penolong_details.material_request_id,
        material_request_penolong_details.barang1_id,
        material_request_penolong_details.barang2_id,
        material_request_penolong_details.qty2 as qty_produksi,
        stock_details2.stock_dokumen,
        stock_details.no_dokumen,
        parent_barang.parent_name
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('material_request_penolong_details', 'material_request_penolong_details.material_request_id = material_requests_penolong.id', 'left')
            ->join('stock_details2', 'stock_details2.stock_id = material_request_penolong_details.stock_id AND stock_details2.stock_dokumen = material_request_penolong_details.stock_dokumen', 'left')
            ->join('stock_details', 'stock_details.stock_id = material_request_penolong_details.stock_id AND stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.no_penerimaan_barang = stock_details.no_dokumen', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.barang_id = material_request_penolong_details.barang1_id AND penerimaan_barang_detail.spesifikasi_id = material_request_penolong_details.barang2_id', 'left')
            ->join('barang_master', 'barang_master.id = material_request_penolong_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = material_request_penolong_details.barang2_id', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->like('material_requests_penolong.request_date', $where['tanggal_jurnal'])
            // ->where('material_request_penolong_details.divisi_id', $where['divisi_id'])
            ->where('material_request_penolong_details.barang_type', 'bahan_penolong')
            ->where('material_request_penolong_details.deletedAt', $where['deletedAt'])
            ->where('material_requests_penolong.deletedAt', $where['deletedAt'])
            ->groupBy('barang_master.parent_type_id')
            ->findAll();

        return $dataQry;
    }
}
