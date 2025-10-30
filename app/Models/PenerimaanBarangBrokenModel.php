<?php

namespace App\Models;

use App\Controllers\Warehouse\PenerimaanBarangBrokenLokalBP;
use CodeIgniter\Model;
use Doctrine\Instantiator\Exception\InvalidArgumentException;

class PenerimaanBarangBrokenModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_barang_broken';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function getPenerimaanBarangBrokenList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_penerimaan_barang' => 'penerimaan_barang_broken.no_penerimaan_barang',
            'warehouse_name'       => 'warehouses.warehouse_name',
            'tipe_bahan'           => 'penerimaan_barang_broken.tipe_bahan',
            'supplier_name'        => 'suppliers.name',
            'createdAt'            => 'penerimaan_barang_broken.createdAt',
            'updatedAt'            => 'penerimaan_barang_broken.updatedAt',
            'divisi'               => 'divisis.divisi',
            'metadata.value'       => 'metadata.value'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort     = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang_broken.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        // --- Build base query (tanpa COUNT dulu) ---
        $baseQuery = $this->db->table('penerimaan_barang_broken')
            ->select('penerimaan_barang_broken.*,
                    suppliers.name AS supplier_name,
                    warehouses.warehouse_name,
                    divisis.divisi
                  ')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang_broken.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_broken.id');

        // --- Filter search ---
        if (!empty($addCondition['search'])) {
            $baseQuery->groupStart()
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->groupEnd();
        }

        if (!empty($addCondition['status'])) {
            $baseQuery->where('penerimaan_barang_broken.status_posting', $addCondition['status']);
        }

        if (!empty($addCondition['startdate'])) {
            $baseQuery->where('penerimaan_barang_broken.tanggal >=', $addCondition['startdate']);
        }
        if (!empty($addCondition['lastdate'])) {
            $baseQuery->where('penerimaan_barang_broken.tanggal <=', $addCondition['lastdate']);
        }

        // --- Hitung totalFilteredData dengan query clone (tanpa limit) ---
        $totalFilteredData = clone $baseQuery;
        $totalFilteredData = $totalFilteredData->countAllResults(false);

        // --- Hitung totalData (tanpa filter search/status/date) ---
        $totalDataQuery = $this->db->table('penerimaan_barang_broken')
            ->where($condition);
        $totalData = $totalDataQuery->countAllResults();

        // --- Ambil data utama dengan limit/offset ---
        $dataList = $baseQuery
            ->orderBy($sort, $sortType)
            ->limit($limit, $offset)
            ->get()
            ->getResultObject();

        // --- Ambil itemCount sekaligus untuk semua ID di page ini ---
        $penerimaanBarangBrokenIds = array_column($dataList, 'id');
        $itemCounts = [];
        if (!empty($penerimaanBarangBrokenIds)) {
            $itemCountQuery = $this->db->table('penerimaan_barang_broken_detail')
                ->select('penerimaan_barang_id, COUNT(*) AS item_count')
                ->whereIn('penerimaan_barang_id', $penerimaanBarangBrokenIds)
                ->where('deletedAt IS NULL', null, false)
                ->groupBy('penerimaan_barang_id')
                ->get()
                ->getResultObject();

            foreach ($itemCountQuery as $row) {
                $itemCounts[$row->penerimaan_barang_id] = $row->item_count;
            }
        }

        // --- Merge itemCount ke data utama ---
        foreach ($dataList as $key => $row) {
            $row->itemCount = $itemCounts[$row->id] ?? 0;
            $dataList[$key] = $row;
        }

        return [
            'data'              => $dataList,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getPenerimaanBarangBrokenListBackup($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_penerimaan_barang'      => 'penerimaan_barang_broken.no_penerimaan_barang',
            'warehouse_name'            => 'warehouses.warehouse_name',
            'tipe_bahan'                => 'penerimaan_barang_broken.tipe_bahan',
            'supplier_name'             => 'suppliers.name',
            'createdAt'                 => 'penerimaan_barang_broken.createdAt',
            'updatedAt'                 => 'penerimaan_barang_broken.updatedAt',
            'divisi'                    => 'divisis.divisi',
            'metadata.value'            => 'metadata.value'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang_broken.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
        DISTINCT(penerimaan_barang_broken.id), 
        penerimaan_barang_broken.no_penerimaan_barang,
        penerimaan_barang_broken.multiple_po_id,
        penerimaan_barang_broken.tipe_bahan,
        penerimaan_barang_broken.tanggal,
        penerimaan_barang_broken.multiple_po_no,
        penerimaan_barang_broken.multiple_spp_no,
        penerimaan_barang_broken.status_post,
        penerimaan_barang_broken.bc_type,
        penerimaan_barang_broken.divisi_id,
        bc_purchase_order_lpb.bc_purchase_order_id,
        warehouses.warehouse_name, suppliers.name as supplier_name, 
        COUNT(penerimaan_barang_broken_detail.id) AS itemCount, 
        divisis.divisi,
        metadata.value as bc_type_name";
        $penerimaanBarangBrokenDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang_broken.warehouse_id', 'left')
            ->join('penerimaan_barang_broken_detail', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'right')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->groupBy('penerimaan_barang_broken.id');


        if (in_array($condition['penerimaan_barang_broken.status_penerimaan'], ['LOKAL', 'IMPORT']) && $condition['tipe_bahan'] == "PENOLONG") {
            // KHSUSUS LPB LOKAL BP & LOKAL BB
            $penerimaanBarangBrokenDataQry->join('am_purchase_orders', 'penerimaan_barang_broken_detail.purchase_order_id = am_purchase_orders.id', 'left');
            $penerimaanBarangBrokenDataQry->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left');
            $penerimaanBarangBrokenDataQry->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left');
        } elseif ($condition['penerimaan_barang_broken.status_penerimaan'] == "LOKAL" && $condition['tipe_bahan'] == "BAKU") {
            // KHUSUS LPB LOKAL BB
            $penerimaanBarangBrokenDataQry->join('rm_purchase_order_details', 'rm_purchase_order_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left');
        } elseif ($condition['penerimaan_barang_broken.status_penerimaan'] == "IMPOR" && $condition['tipe_bahan'] == "BAKU") {
            // KHUSUS LPB IMPORT BB
            $penerimaanBarangBrokenDataQry->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left');
        }


        if (isset($addCondition['status'])) {
            $penerimaanBarangBrokenDataQry->where('penerimaan_barang_broken.status_post', $addCondition['status']);
        }

        if ($addCondition['nama_barang']) {
            $penerimaanBarangBrokenDataQry->groupStart();
            $penerimaanBarangBrokenDataQry->like('LOWER(CONCAT(barang_master.barang_name," ",barang_master_spesifikasi.spesifikasi))', $addCondition['nama_barang'])
                ->orLike('LOWER(barang_master.kode_barang)', $addCondition['nama_barang']);
            $penerimaanBarangBrokenDataQry->groupEnd();
        }

        if ($addCondition['note']) {
            $penerimaanBarangBrokenDataQry->groupStart();
            if (in_array($condition['penerimaan_barang_broken.status_penerimaan'], ['LOKAL', 'IMPORT']) && $condition['tipe_bahan'] == "PENOLONG") {
                // KHSUSUS LPB LOKAL BP & LOKAL BB
                $penerimaanBarangBrokenDataQry->like('LOWER(am_purchase_order_details.note)', $addCondition['note']);
            } elseif ($condition['penerimaan_barang_broken.status_penerimaan'] == "LOKAL" && $condition['tipe_bahan'] == "BAKU") {
                // KHSUSUS LPB LOKAL LOKAL BB
                $penerimaanBarangBrokenDataQry->like('LOWER(rm_purchase_order_details.note)', $addCondition['note']);
            } elseif ($condition['penerimaan_barang_broken.status_penerimaan'] == "IMPOR" && $condition['tipe_bahan'] == "BAKU") {
                // KHUSUS LPB IMPORT BB
                $penerimaanBarangBrokenDataQry->like('LOWER(rm_import_po_details.note)', $addCondition['note']);
            }
            $penerimaanBarangBrokenDataQry->groupEnd();
        }

        if ($addCondition['search']) {
            $penerimaanBarangBrokenDataQry->groupStart();
            $penerimaanBarangBrokenDataQry->like('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search']);
            $penerimaanBarangBrokenDataQry->orLike('suppliers.name', $addCondition['search']);
            $penerimaanBarangBrokenDataQry->orLike('warehouses.warehouse_name', $addCondition['search']);
            $penerimaanBarangBrokenDataQry->orLike('penerimaan_barang_broken.multiple_po_no', $addCondition['search']);
            $penerimaanBarangBrokenDataQry->orLike('divisis.divisi', $addCondition['search']);
            $penerimaanBarangBrokenDataQry->orLike('metadata.value', $addCondition['search']);

            if ($condition['penerimaan_barang_broken.status_penerimaan'] == "LOKAL" && $condition['tipe_bahan'] == "PENOLONG") {
                $penerimaanBarangBrokenDataQry->orLike('purchase_requests.spp_no', $addCondition['search']);
            }

            $penerimaanBarangBrokenDataQry->groupEnd();
        }

        if ($addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->groupStart();
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangBrokenDataQry->where('penerimaan_barang_broken.tanggal >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->where('penerimaan_barang_broken.tanggal <=', $addCondition['lastdate']);
        }

        if ($addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->groupEnd();
        }


        $penerimaanBarangBrokenDataQry->groupBy(('penerimaan_barang_broken.id'))
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangBrokenDataQry->countAllResults(false);


        $totalFilteredData = $penerimaanBarangBrokenDataQry->countAllResults(false);
        $data = $penerimaanBarangBrokenDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangBrokenListForAccounting($condition, $addCondition, $limit = 10, $offset = 0, $companyId)
    {
        $availableSort = [
            'no_penerimaan_barang'      => 'penerimaan_barang_broken.no_penerimaan_barang',
            'warehouse_name'            => 'warehouses.warehouse_name',
            'tipe_bahan'                => 'penerimaan_barang_broken.tipe_bahan',
            'supplier_name'             => 'suppliers.name',
            'createdAt'                 => 'penerimaan_barang_broken.createdAt',
            'updatedAt'                 => 'penerimaan_barang_broken.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang_broken.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_barang_broken.*, 
                    DATE_FORMAT(penerimaan_barang_broken.tanggal, '%d/%m/%Y') AS tanggal_penerimaan,
                    warehouses.warehouse_name, 
                    suppliers.name as supplier_name, 
                    COUNT(penerimaan_barang_broken_detail.id) AS itemCount, 
                    penerimaan_barang_broken_detail.harga,
                    SUM(penerimaan_barang_broken_detail.sub_total) as harga_sub_total,
                    bc_23.no_aju AS BC23_AJU,
                    bc_40.no_aju AS BC40_AJU,
                    divisis.divisi as divisi";
        $penerimaanBarangBrokenDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang_broken.warehouse_id', 'left')
            ->join('penerimaan_barang_broken_detail', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'right')
            ->join('bc_purchase_order bc_23_po', 'bc_23_po.id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order bc_40_po', 'bc_40_po.id = penerimaan_barang_broken.id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_23_po.id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_40_po.id', 'left')
            ->join('divisis', 'divisis.divisi = penerimaan_barang_broken.divisi_id', 'left')
            ->groupBy(('penerimaan_barang_broken.id'))
            ->where($condition)
            ->whereIn('penerimaan_barang_broken.company_id', $companyId)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangBrokenDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangBrokenDataQry->like('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.multiple_po_no', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $penerimaanBarangBrokenDataQry->whereIn('suppliers.id', $addCondition['filter']);
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangBrokenDataQry->where('penerimaan_barang_broken.tanggal >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->where('penerimaan_barang_broken.tanggal <=', $addCondition['lastdate']);
        }

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->groupEnd();
        }

        $totalFilteredData = $penerimaanBarangBrokenDataQry->countAllResults(false);
        $data = $penerimaanBarangBrokenDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangBrokenListForPrintAccounting($condition, $addCondition, $companyId)
    {
        $availableSort = [
            'no_penerimaan_barang'      => 'penerimaan_barang_broken.no_penerimaan_barang',
            'warehouse_name'            => 'warehouses.warehouse_name',
            'tipe_bahan'                => 'penerimaan_barang_broken.tipe_bahan',
            'supplier_name'             => 'suppliers.name',
            'createdAt'                 => 'penerimaan_barang_broken.createdAt',
            'updatedAt'                 => 'penerimaan_barang_broken.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang_broken.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_barang_broken.*, 
                DATE_FORMAT(penerimaan_barang_broken.tanggal, '%d/%m/%Y') AS tanggal_penerimaan,
                warehouses.warehouse_name, 
                suppliers.name as supplier_name, 
                COUNT(penerimaan_barang_broken_detail.id) AS itemCount, 
                penerimaan_barang_broken_detail.harga,
                SUM(penerimaan_barang_broken_detail.sub_total) as harga_sub_total,
                bc_23.no_aju AS BC23_AJU,
                bc_40.no_aju AS BC40_AJU,
                divisis.divisi as divisi";

        // dd($condition, $addCondition, $companyId);

        $penerimaanBarangBrokenDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang_broken.warehouse_id', 'left')
            ->join('penerimaan_barang_broken_detail', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'right')
            ->join('bc_purchase_order bc_23_po', 'bc_23_po.id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order bc_40_po', 'bc_40_po.id = penerimaan_barang_broken.id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_23_po.id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_40_po.id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->groupBy('penerimaan_barang_broken.id')
            ->where($condition)
            ->whereIn('penerimaan_barang_broken.company_id', $companyId)
            ->orderBy($sort, $sortType);

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangBrokenDataQry->like('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.multiple_po_no', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $penerimaanBarangBrokenDataQry->whereIn('suppliers.id', $addCondition['filter']);
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangBrokenDataQry->where('penerimaan_barang_broken.tanggal >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->where('penerimaan_barang_broken.tanggal <=', $addCondition['lastdate']);
        }

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangBrokenDataQry->groupEnd();
        }

        $totalData = $penerimaanBarangBrokenDataQry->countAllResults(false);
        $totalFilteredData = $totalData;
        $data = $penerimaanBarangBrokenDataQry->findAll();
        // $db = \Config\Database::connect();
        // dd($db->getLastQuery()->getQuery());


        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getById($id)
    {
        $selectQry = "penerimaan_barang_broken.*, 
        suppliers.name as supplier_name,
        suppliers.address as supplier_address, 
        suppliers.phone as supplier_phone, 
        warehouses.warehouse_name,
        metadata.value as bc_type,divisis.divisi as divisi,
        barang_master.barang_name as barang_name,
        companies.holding_company,
        companies.company as companyName,
        companies.address as companyAddress
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang_broken.warehouse_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('penerimaan_barang_broken_detail', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('companies', 'companies.id = penerimaan_barang_broken.company_id', 'left')
            ->find($id);

        return $sppData;
    }

    public function getByIdPrintBahanBaku($id)
    {
        $selectQry = "penerimaan_barang_broken.*, 
        suppliers.name as supplier_name,
        suppliers.address as supplier_address, 
        suppliers.phone as supplier_phone, 
        warehouses.warehouse_name,
        metadata.value as bc_type,
        divisis.divisi as divisi,
        barang_master.barang_name as barang_name,
        rm_purchase_orders.po_no as po_no,
        rm_purchase_orders.total_after_pph as total_after_pph,
        rm_purchase_orders.total_before_pph as total_before_pph";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang_broken.warehouse_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('penerimaan_barang_broken_detail', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join(
                'rm_purchase_orders',
                'FIND_IN_SET(rm_purchase_orders.id, REPLACE(REPLACE(REPLACE(penerimaan_barang_broken.multiple_po_id, "[", ""), "]", ""), " ", ""))',
                'left'
            )
            ->where('penerimaan_barang_broken.id', $id)
            ->first();

        return $sppData;
    }

    public function get_no($tanggal, $companyId)
    {
        $bulanF = date('m', strtotime($tanggal));
        $tahunF = date('y', strtotime($tanggal));

        // Prefix tetap biar tahu LPB Broken
        $unixPrefix = 'BRK';
        $tipeKode   = 'LBB'; // Karena cuma bahan baku
        $kodeTambahan = [
            1  => 'F',
            2  => '',
            15 => 'G',
            16 => 'O'
        ];

        $kode = $kodeTambahan[$companyId] ?? '';

        // Template dasar: BRK/LBB/F/1025
        $template = "{$unixPrefix}/{$tipeKode}/{$kode}" . ($kode ? '/' : '') . "{$bulanF}{$tahunF}";

        // Ambil nomor terakhir bulan ini
        $builder = $this->db->table('penerimaan_barang_broken');
        $builder->select('no_penerimaan_barang')
            ->where('company_id', $companyId)
            ->where('deletedAt', null)
            ->like('no_penerimaan_barang', $template, 'after')
            ->where('tanggal >=', date('Y-m-01', strtotime($tanggal)))
            ->where('tanggal <=', date('Y-m-t', strtotime($tanggal)))
            ->orderBy('no_penerimaan_barang', 'desc');

        $results = $builder->get()->getResultArray();

        // Cari next number
        $existingNumbers = [];
        foreach ($results as $r) {
            $last = end(explode('/', $r['no_penerimaan_barang']));
            if (is_numeric($last)) $existingNumbers[] = (int)$last;
        }

        $nextNumber = empty($existingNumbers) ? 1 : (max($existingNumbers) + 1);
        $formattedNumber = sprintf("%05d", $nextNumber); // selalu 5 digit

        return "{$template}/{$formattedNumber}";
    }

    public function getReceivedItemsBySupplier($supplierId, $condition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode'              => 'suppliers.kode',
            'name'              => 'suppliers.name',
            'address'           => 'suppliers.address',
            'no_npwp'           => 'suppliers.no_npwp',
            'phone'             => 'suppliers.phone',
            'contact_person'    => 'suppliers.contact_person',
            'no_rekening'       => 'suppliers.no_rekening',
            'supplier_buyer'    => 'suppliers.supplier_buyer',
            'province'          => 'provinces.province_name',
            'city'              => 'cities.city_name',
            'postal_code'       => 'cities.postal_code',
            'createdAt'         => 'penerimaan_barang_broken.createdAt',
            'updatedAt'         => 'penerimaan_barang_broken.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$condition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$condition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_barang_broken_detail.id AS id,
                      DATE_FORMAT(penerimaan_barang_broken.createdAt, '%d/%m/%Y') AS lpb_date,
                      penerimaan_barang_broken.no_penerimaan_barang AS no_lpb,
                      penerimaan_barang_broken.multiple_po_no,
                      penerimaan_barang_broken_detail.nama_barang_dok AS item_name,
                      (`penerimaan_barang_broken_detail`.`qty` - `penerimaan_barang_broken_detail`.`summarized_qty`) AS lpb_qty,
                      (penerimaan_barang_broken_detail.harga + penerimaan_barang_broken_detail.harga_harian + penerimaan_barang_broken_detail.harga_bulanan) AS price,
                      satuans.kode_satuan AS unit";
        $receiveDataQry = $this->asObject()
            ->select($selectQry)
            ->where('penerimaan_barang_broken.supplier_id', $supplierId)
            ->where($condition)
            ->where('(`penerimaan_barang_broken_detail`.`qty` - `penerimaan_barang_broken_detail`.`summarized_qty`) > 0')
            ->where("penerimaan_barang_broken_detail.summarized_qty <", 'penerimaan_barang_broken_detail.qty', false)
            ->join('penerimaan_barang_broken_detail', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id AND penerimaan_barang_broken_detail.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit AND satuans.deletedAt IS NULL')
            ->orderBy($sort, $sortType);

        $totalData = $receiveDataQry->countAllResults(false);

        /* if ($condition['search']) {
            $receiveDataQry->groupStart()
                ->like('name', $condition['search'])
                ->orLike('kode', $condition['search'])
            ->groupEnd();
        } */

        $totalFilteredData = $receiveDataQry->countAllResults(false);
        $data = $receiveDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getReceivedNoBySupplier($supplierId, $condition): array
    {
        $receiveDataQry = $this->asObject()
            ->select('id, no_penerimaan_barang')
            ->where('penerimaan_barang_broken.supplier_id', $supplierId)
            ->where($condition)
            // ->orderBy($sort, $sortType)
            ->findAll();

        return $receiveDataQry;
    }

    public function autoClosePO($penerimaanBarangBrokenID)
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel(); // bp lokal or import
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel(); // bp lokal or import
        $rmPurchaseOrderModel = new RMPurchaseOrderModel(); // bb lokal
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel(); // bb lokal
        $rmImportPoModel = new RMImportPOModel(); // bb import
        $rmImportPoDetailModel = new RMImportPODetailModel(); // bb import

        // get type penerimaan dan tipe bahan
        $penerimaanFirst = $this->asArray()->where('id', $penerimaanBarangBrokenID)->first();

        if ($penerimaanFirst == null) {
            return;
        }

        $poIDArr = json_decode(($penerimaanFirst['multiple_po_id']));

        if ($penerimaanFirst['status_penerimaan'] == "LOKAL" && $penerimaanFirst['tipe_bahan'] == "PENOLONG") {
            // PO LOKAL BAHAN PENOLONG
            foreach ($poIDArr as $p) {
                $poList = $amPurchaseOrderDetailModel->select('SUM(remaining_qty) AS remaining_qty_sum')
                    ->where('deletedAt', null)
                    ->where('am_purchase_order_id', $p)
                    ->findAll();

                if (count($poList) == 0) {
                    return;
                }
                if ($poList[0]['remaining_qty_sum'] == 0) {
                    $amPurchaseOrderModel->update($p, [
                        'status_penerimaan' => 1
                    ]);
                }
            }
        } elseif ($penerimaanFirst['status_penerimaan'] == "LOKAL" && $penerimaanFirst['tipe_bahan'] == "BAKU") {
            // PO LOKAL BAHAN BAKU
            foreach ($poIDArr as $p) {
                $poList = $rmPurchaseOrderDetailModel->select('SUM(remaining_qty) AS remaining_qty_sum')
                    ->where('deletedAt', null)
                    ->where('rm_purchase_order_id', $p)
                    ->findAll();

                if (count($poList) == 0) {
                    return;
                }
                if ($poList[0]['remaining_qty_sum'] == 0) {
                    $rmPurchaseOrderModel->update($p, [
                        'status_penerimaan' => 1
                    ]);
                }
            }
        } elseif ($penerimaanFirst['status_penerimaan'] == "IMPORT" && $penerimaanFirst['tipe_bahan'] == "PENOLONG") {
            // PO IMPORT BAHAN PENOLONG
            foreach ($poIDArr as $p) {
                $poList = $amPurchaseOrderDetailModel->select('SUM(remaining_qty) AS remaining_qty_sum')
                    ->where('deletedAt', null)
                    ->where('am_purchase_order_id', $p)
                    ->findAll();

                if (count($poList) == 0) {
                    return;
                }
                if ($poList[0]['remaining_qty_sum'] == 0) {
                    $amPurchaseOrderModel->update($p, [
                        'status_penerimaan' => 1
                    ]);
                }
            }
        } elseif ($penerimaanFirst['status_penerimaan'] == "IMPORT" && $penerimaanFirst['tipe_bahan'] == "BAKU") {
            // PO IMPORT BAHAN BAKU
            foreach ($poIDArr as $p) {
                $poList = $rmImportPoDetailModel->select('SUM(remaining_qty) AS remaining_qty_sum')
                    ->where('deletedAt', null)
                    ->where('rm_import_po_id', $p)
                    ->findAll();

                if (count($poList) == 0) {
                    return;
                }
                if ($poList[0]['remaining_qty_sum'] == 0) {
                    $rmImportPoModel->update($p, [
                        'status_penerimaan' => 1
                    ]);
                }
            }
        }
    }

    public function getListLaporanPenerimaanBarangBrokenBahanBakuLokal($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'          => 'divisis.divisi',
            'supplier_id'     => 'penerimaan_barang_broken.supplier_id',
            'bc_type'         => 'penerimaan_barang_broken.bc_type',
            'tanggal_dokumen' => 'bc_purchase_order.createdAt',
            'no_daftar'       => 'bc_purchase_order.no_daftar',
            'no_aju'          => 'bc_40.no_aju',
            'tanggal_lpb'     => 'penerimaan_barang_broken.tanggal',
            'no_lpb'          => 'penerimaan_barang_broken.no_penerimaan_barang',
            'po_date'         => 'rm_purchase_orders.po_date',
            'po_no'           => 'rm_purchase_orders.po_no',
            'kode_barang'     => 'barang_master.kode_barang',
            'barang_name'     => 'barang_master.barang_name',
            'spesifikasi'     => 'barang_master_spesifikasi.spesifikasi',
            'satuan_id'       => 'penerimaan_barang_broken_detail.unit',
            'keterangan'      => 'penerimaan_barang_broken_detail.keterangan',
            'qty_order'       => 'qty_order',
            'qty_diterima'    => 'qty_diterima',
            'total_harga'     => 'rm_purchase_orders.total_before_pph',
            'harga_satuan'    => 'penerimaan_barang_broken_detail.harga'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'penerimaan_barang_broken.tanggal';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            penerimaan_barang_broken_detail.id,
            penerimaan_barang_broken.tanggal AS tanggal_lpb,
            penerimaan_barang_broken.no_penerimaan_barang AS no_lpb,
            divisis.divisi,
            suppliers.name AS supplier_name,
            metadata.value AS bc_name,
            bc_purchase_order.createdAt AS tanggal_dokumen,
            bc_purchase_order.no_daftar,
            bc_40.no_aju,
            barang_master.kode_barang,
            barang_master.barang_name,
            satuans.kode_satuan,
            IFNULL(GROUP_CONCAT(DISTINCT penerimaan_barang_broken_detail.keterangan SEPARATOR ', '), '') AS keterangan,
            IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
            SUM(penerimaan_barang_broken_detail.qty) AS qty_order,
            SUM(penerimaan_barang_broken_detail.jml_masuk) AS qty_diterima,
            rm_purchase_orders.po_date,
            rm_purchase_orders.po_no,
            rm_purchase_orders.total_before_pph AS total_harga,
            parent_barang.parent_name AS kategori_barang
        ";
        $builder = $this->asArray()
            ->select($selectQry)
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('penerimaan_barang_broken_detail', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang_broken_detail.purchase_order_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_broken.id, penerimaan_barang_broken_detail.purchase_order_id')
            ->orderBy($sort, $sortType);
        $totalData = $builder->countAllResults(false);
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang_broken.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang_broken.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang_broken.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang_broken.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang_broken.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang_broken.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('bc_40.no_aju', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->groupEnd();
        }

        // hitung total & filtered
        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->findAll($limit, $offset);

        // HITUNG TOTAL YAH
        $sub = $this->db->table('penerimaan_barang_broken')
            ->select('rm_purchase_orders.id, rm_purchase_orders.total_before_pph')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('penerimaan_barang_broken_detail', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang_broken_detail.purchase_order_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_broken.id, penerimaan_barang_broken_detail.purchase_order_id');

        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $sub->where('penerimaan_barang_broken.bc_type', 0);
            } else {
                $sub->where('penerimaan_barang_broken.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $sub->where('penerimaan_barang_broken.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $sub->where('penerimaan_barang_broken.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $sub->where('penerimaan_barang_broken.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $sub->where('penerimaan_barang_broken.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $sub->where('penerimaan_barang_broken.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $sub->where('penerimaan_barang_broken.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $sub->groupStart()
                ->like('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('bc_40.no_aju', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->groupEnd();
        }

        // wrap ke subquery lalu SUM
        $qtySum = $this->db->table("({$sub->getCompiledSelect()}) as po")
            ->select('SUM(po.total_before_pph) as total_harga');

        $grandTotalHarga = $qtySum->get()->getRowArray();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'grandTotalHarga'   => (float)$grandTotalHarga['total_harga']
        ];
    }

    public function getListLaporanPenerimaanBarangBrokenBahanPenolongLokal($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'          => 'divisis.divisi',
            'supplier_id'     => 'penerimaan_barang_broken.supplier_id',
            'bc_type'         => 'penerimaan_barang_broken.bc_type',
            'tanggal_dokumen' => 'bc_purchase_order.createdAt',
            'no_daftar'       => 'bc_purchase_order.no_daftar',
            'no_aju'          => 'bc_40.no_aju',
            'tanggal_lpb'     => 'penerimaan_barang_broken.tanggal',
            'no_lpb'          => 'penerimaan_barang_broken.no_penerimaan_barang',
            'po_date'         => 'am_purchase_orders.po_date',
            'po_no'           => 'am_purchase_orders.po_no',
            'kode_barang'     => 'barang_master.kode_barang',
            'barang_name'     => 'barang_master.barang_name',
            'spesifikasi'     => 'barang_master_spesifikasi.spesifikasi',
            'satuan_id'       => 'penerimaan_barang_broken_detail.unit',
            'keterangan'      => 'penerimaan_barang_broken_detail.keterangan',
            'qty_order'       => 'qty_order',
            'qty_diterima'    => 'qty_diterima',
            'total_harga'     => 'penerimaan_barang_broken_detail.sub_total',
            'harga_satuan'    => 'penerimaan_barang_broken_detail.harga'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'penerimaan_barang_broken.tanggal';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            penerimaan_barang_broken_detail.id,
            penerimaan_barang_broken.tanggal AS tanggal_lpb,
            penerimaan_barang_broken.no_penerimaan_barang AS no_lpb,
            divisis.divisi,
            suppliers.name AS supplier_name,
            tb_bc.value AS bc_name,
            bc_purchase_order.createdAt AS tanggal_dokumen,
            bc_purchase_order.no_daftar,
            bc_40.no_aju,
            barang_master.kode_barang,
            barang_master.barang_name,
            satuans.kode_satuan,
            am_purchase_order_details.note AS keterangan,
            barang_master_spesifikasi.spesifikasi AS spesifikasi,
            penerimaan_barang_broken_detail.qty AS qty_order,
            penerimaan_barang_broken_detail.jml_masuk AS qty_diterima,
            am_purchase_orders.po_date,
            am_purchase_orders.po_no,
            penerimaan_barang_broken_detail.sub_total AS total_harga,
            penerimaan_barang_broken_detail.harga AS harga_satuan,
            tb_valas.value AS valas_name,
            parent_barang.parent_name AS kategori_barang
        ";
        $builder = $this->db->table('penerimaan_barang_broken_detail')
            ->select($selectQry)
            ->join('penerimaan_barang_broken', 'penerimaan_barang_broken.id = penerimaan_barang_broken_detail.penerimaan_barang_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_broken_detail.purchase_order_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = am_purchase_orders.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_broken_detail.id')
            ->orderBy($sort, $sortType);
        $totalData = $builder->countAllResults(false);
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang_broken.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang_broken.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang_broken.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang_broken.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang_broken.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang_broken.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('bc_40.no_aju', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('am_purchase_order_details.note', $addCondition['search'])
                ->groupEnd();
        }

        // hitung total & filtered
        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->get($limit, $offset)->getResultArray();

        $sub = $this->db->table('penerimaan_barang_broken_detail')
            ->select('SUM(penerimaan_barang_broken_detail.sub_total) AS total_harga')
            ->join('penerimaan_barang_broken', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_broken_detail.purchase_order_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = am_purchase_orders.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit', 'left')
            ->where($condition);
        // Filter
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $sub->where('penerimaan_barang_broken.bc_type', 0);
            } else {
                $sub->where('penerimaan_barang_broken.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $sub->where('penerimaan_barang_broken.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $sub->where('penerimaan_barang_broken.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $sub->where('penerimaan_barang_broken.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $sub->where('penerimaan_barang_broken.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $sub->where('penerimaan_barang_broken.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $sub->where('penerimaan_barang_broken.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $sub->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('bc_40.no_aju', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('am_purchase_order_details.note', $addCondition['search'])
                ->groupEnd();
        }

        // wrap ke subquery lalu SUM

        $grandTotalHarga = $sub->get()->getRowArray();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'grandTotalHarga'   => (float)$grandTotalHarga['total_harga']
        ];
    }

    public function getListLaporanPenerimaanBarangBrokenBahanPenolongImpor($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'          => 'divisis.divisi',
            'supplier_id'     => 'penerimaan_barang_broken.supplier_id',
            'bc_type'         => 'penerimaan_barang_broken.bc_type',
            'tanggal_dokumen' => 'bc_purchase_order.createdAt',
            'no_daftar'       => 'bc_purchase_order.no_daftar',
            'no_aju'          => 'bc_23.no_aju',
            'tanggal_lpb'     => 'penerimaan_barang_broken.tanggal',
            'no_lpb'          => 'penerimaan_barang_broken.no_penerimaan_barang',
            'po_date'         => 'am_purchase_orders.po_date',
            'po_no'           => 'am_purchase_orders.po_no',
            'kode_barang'     => 'barang_master.kode_barang',
            'barang_name'     => 'barang_master.barang_name',
            'spesifikasi'     => 'barang_master_spesifikasi.spesifikasi',
            'satuan_id'       => 'penerimaan_barang_broken_detail.unit',
            'keterangan'      => 'penerimaan_barang_broken_detail.keterangan',
            'qty_order'       => 'qty_order',
            'qty_diterima'    => 'qty_diterima',
            'total_harga'     => 'penerimaan_barang_broken_detail.sub_total',
            'harga_satuan'    => 'penerimaan_barang_broken_detail.harga'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'penerimaan_barang_broken.tanggal';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            penerimaan_barang_broken_detail.id,
            penerimaan_barang_broken.tanggal AS tanggal_lpb,
            penerimaan_barang_broken.no_penerimaan_barang AS no_lpb,
            divisis.divisi,
            suppliers.name AS supplier_name,
            tb_bc.value AS bc_name,
            bc_purchase_order.createdAt AS tanggal_dokumen,
            bc_purchase_order.no_daftar,
            bc_23.no_aju,
            barang_master.kode_barang,
            barang_master.barang_name,
            satuans.kode_satuan,
            am_purchase_order_details.note AS keterangan,
            barang_master_spesifikasi.spesifikasi AS spesifikasi,
            penerimaan_barang_broken_detail.qty AS qty_order,
            penerimaan_barang_broken_detail.jml_masuk AS qty_diterima,
            am_purchase_orders.po_date,
            am_purchase_orders.po_no,
            penerimaan_barang_broken_detail.sub_total AS total_harga,
            penerimaan_barang_broken_detail.harga AS harga_satuan,
            tb_valas.value AS valas_name,
            parent_barang.parent_name AS kategori_barang
        ";
        $builder = $this->db->table('penerimaan_barang_broken_detail')
            ->select($selectQry)
            ->join('penerimaan_barang_broken', 'penerimaan_barang_broken.id = penerimaan_barang_broken_detail.penerimaan_barang_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_broken_detail.purchase_order_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = am_purchase_orders.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_broken_detail.id')
            ->orderBy($sort, $sortType);
        $totalData = $builder->countAllResults(false);
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang_broken.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang_broken.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang_broken.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang_broken.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang_broken.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang_broken.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('bc_23.no_aju', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('am_purchase_order_details.note', $addCondition['search'])
                ->groupEnd();
        }

        // hitung total & filtered
        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->get($limit, $offset)->getResultArray();

        $sub = $this->db->table('penerimaan_barang_broken_detail')
            ->select('SUM(penerimaan_barang_broken_detail.sub_total) AS total_harga')
            ->join('penerimaan_barang_broken', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_broken_detail.purchase_order_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = am_purchase_orders.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit', 'left')
            ->where($condition);
        // Filter
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $sub->where('penerimaan_barang_broken.bc_type', 0);
            } else {
                $sub->where('penerimaan_barang_broken.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $sub->where('penerimaan_barang_broken.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $sub->where('penerimaan_barang_broken.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $sub->where('penerimaan_barang_broken.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $sub->where('penerimaan_barang_broken.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $sub->where('penerimaan_barang_broken.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $sub->where('penerimaan_barang_broken.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $sub->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('bc_23.no_aju', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('am_purchase_order_details.note', $addCondition['search'])
                ->groupEnd();
        }

        // wrap ke subquery lalu SUM

        $grandTotalHarga = $sub->get()->getRowArray();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'grandTotalHarga'   => (float)$grandTotalHarga['total_harga']
        ];
    }

    public function getListLaporanPenerimaanBarangBrokenBahanBakuImport($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'          => 'divisis.divisi',
            'supplier_id'     => 'penerimaan_barang_broken.supplier_id',
            'bc_type'         => 'penerimaan_barang_broken.bc_type',
            'tanggal_dokumen' => 'bc_purchase_order.createdAt',
            'no_daftar'       => 'bc_purchase_order.no_daftar',
            'no_aju'          => 'bc_23.no_aju',
            'tanggal_lpb'     => 'penerimaan_barang_broken.tanggal',
            'no_lpb'          => 'penerimaan_barang_broken.no_penerimaan_barang',
            'po_date'         => 'rm_import_pos.po_date',
            'po_no'           => 'rm_import_pos.po_no',
            'kode_barang'     => 'barang_master.kode_barang',
            'barang_name'     => 'barang_master.barang_name',
            'spesifikasi'     => 'barang_master_spesifikasi.spesifikasi',
            'satuan_id'       => 'penerimaan_barang_broken_detail.unit',
            'keterangan'      => 'penerimaan_barang_broken_detail.keterangan',
            'qty_order'       => 'qty_order',
            'qty_diterima'    => 'qty_diterima',
            'total_harga'     => 'penerimaan_barang_broken_detail.total',
            'harga_satuan'    => 'penerimaan_barang_broken_detail.harga'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'penerimaan_barang_broken.tanggal';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            penerimaan_barang_broken_detail.id,
            penerimaan_barang_broken.tanggal AS tanggal_lpb,
            penerimaan_barang_broken.no_penerimaan_barang AS no_lpb,
            divisis.divisi,
            suppliers.name AS supplier_name,
            tb_bc.value AS bc_name,
            bc_purchase_order.createdAt AS tanggal_dokumen,
            bc_purchase_order.no_daftar,
            bc_23.no_aju,
            barang_master.kode_barang,
            barang_master.barang_name,
            satuans.kode_satuan,
            rm_import_po_details.note AS keterangan,
            barang_master_spesifikasi.spesifikasi AS spesifikasi,
            penerimaan_barang_broken_detail.qty AS qty_order,
            penerimaan_barang_broken_detail.jml_masuk AS qty_diterima,
            rm_import_pos.po_date,
            rm_import_pos.po_no,
            penerimaan_barang_broken_detail.sub_total AS total_harga,
            penerimaan_barang_broken_detail.harga AS harga_satuan,
            tb_valas.value AS valas_name,
            parent_barang.parent_name AS kategori_barang
        ";
        $builder = $this->db->table('penerimaan_barang_broken_detail')
            ->select($selectQry)
            ->join('penerimaan_barang_broken', 'penerimaan_barang_broken_detail.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang_broken_detail.purchase_order_id', 'left')
            ->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = rm_import_pos.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_broken_detail.id')
            ->orderBy($sort, $sortType);
        $totalData = $builder->countAllResults(false);
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang_broken.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang_broken.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang_broken.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang_broken.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang_broken.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang_broken.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('rm_import_pos.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('bc_23.no_aju', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('rm_import_po_details.note', $addCondition['search'])
                ->groupEnd();
        }

        // hitung total & filtered
        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);
        $data = $builder->get($limit, $offset)->getResultArray();

        $sub = $this->db->table('penerimaan_barang_broken_detail')
            ->select('SUM(penerimaan_barang_broken_detail.sub_total) AS total_harga')
            ->join('penerimaan_barang_broken', 'penerimaan_barang_broken_detail.id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang_broken.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang_broken_detail.purchase_order_id', 'left')
            ->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_broken_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang_broken.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang_broken.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang_broken.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = rm_import_pos.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_broken_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_broken_detail.unit', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_broken_detail.id');

        // Filter
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang_broken.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang_broken.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang_broken.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang_broken.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang_broken.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang_broken.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang_broken.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('rm_import_pos.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang_broken.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('bc_23.no_aju', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('rm_import_po_details.note', $addCondition['search'])
                ->groupEnd();
        }

        $grandTotalHarga = $sub->get()->getRowArray();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'grandTotalHarga'   => (float)$grandTotalHarga['total_harga']
        ];
    }
}
