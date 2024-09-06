<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequestDetailsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'material_request_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields = [];

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

    public function getMaterialRequestDetailByMaterialRequestID($mrID)
    {
        $selectQry = '
            material_request_details.*,        
            material_request_details.qty_now,        
            barang_master.kode_barang,        
            barang_master.barang_name,        
            barang_master.type_barang,    
            satuans.id AS satuan_id,
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = material_request_details.barang1_id', 'left')
            ->join('satuans', 'satuans.kode_satuan = material_request_details.satuan', 'left')
            ->whereIn('material_request_details.material_request_id', $mrID)
            ->where('material_request_details.qty_now >', 0)
            ->where('material_request_details.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            ->where('satuans.deletedAt', null)
            // ->groupBy('material_request_details.barang1_id, material_request_details.barang2_id')
            ->findAll();

        return $dataQry;
    }

    public function getListBarangWorkInProgres($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'material_request_details.barang_type' => 'material_request_details.barang_type',
            'barang_master.kode_barang' => 'barang_master.kode_barang',
            'barang_master.barang_name' => 'barang_master.barang_name',
            'material_request_details.satuan' => 'material_request_details.satuan',
            'material_request_details.qty' => 'material_request_details.qty',
            'material_request_details.note' => 'material_request_details.note',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_request_details.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = '
            barang_master.kode_barang,
            barang_master.barang_name,
            material_request_details.*,
            SUM(qty_now) as total_qty_now
        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = material_request_details.barang1_id', 'left')
            ->join('material_requests', 'material_requests.id = material_request_details.material_request_id', 'left')
            ->join('work_orders', 'work_orders.id = material_requests.work_order_id', 'left')
            ->join('production_results', 'production_results.work_order_id = work_orders.id', 'left')
            ->where($condition)
            ->groupBy('material_request_details.barang1_id')
            ->groupBy('work_orders.divisi_id')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['status_produksi'] != "ALL" ||  $addCondition['date_start'] != "" || $addCondition['date_end'] != "" || $addCondition['divisi_id'] || $addCondition['nama_barang'] != "" || $addCondition['kode_produksi'] != "") {
            $dataQry->groupStart();
        }

        if (isset($addCondition['date_start']) && $addCondition['date_start'] !== "") {
            $dataQry->where('production_results.receive_date >=', $addCondition['date_start']);
        }

        if (isset($addCondition['date_end']) && $addCondition['date_end'] !== "") {
            $dataQry->where('production_results.receive_date <=', $addCondition['date_end']);
        }

        if (isset($addCondition['divisi_id']) && $addCondition['divisi_id'] !== "") {
            $dataQry->where('work_orders.divisi_id', $addCondition['divisi_id']);
        }

        if (isset($addCondition['kode_produksi']) && $addCondition['kode_produksi'] !== "") {
            $dataQry->where('production_results.pr_no', $addCondition['kode_produksi']);
        }

        if (isset($addCondition['status_produksi']) && $addCondition['status_produksi'] != "ALL") {
            // MASIH AKTIF = BELUM DIPOSTING
            $dataQry->where('production_results.is_posted', 0);
        }

        if (isset($addCondition['nama_barang']) && $addCondition['nama_barang'] !== "") {
            $dataQry->like('barang_name', $addCondition['nama_barang'])->orLike('kode_barang', $addCondition['nama_barang']);
        }

        if ($addCondition['status_produksi'] != "ALL" ||  $addCondition['date_start'] != "" || $addCondition['date_end'] != "" || $addCondition['divisi_id'] || $addCondition['nama_barang'] != "" || $addCondition['kode_produksi'] != "") {
            $dataQry->groupEnd();
        }


        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
