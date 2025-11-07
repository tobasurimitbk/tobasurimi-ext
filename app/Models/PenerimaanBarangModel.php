<?php

namespace App\Models;

use App\Controllers\Warehouse\PenerimaanBarangLokalBP;
use CodeIgniter\Model;
use Doctrine\Instantiator\Exception\InvalidArgumentException;

class PenerimaanBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'supplier_id',
        'warehouse_id',
        'divisi_id',
        'tanggal',
        'bc_type',
        'no_penerimaan_barang',
        'acceptance_type',
        'multiple_po_id',
        'multiple_po_no',
        'multiple_spp_id',
        'multiple_spp_no',
        'status_penerimaan',
        'status_post',
        'tipe_bahan',
        'kemasan_id',
        'kemasan',
        'jumlah_kemasan',
        'no_surat_jalan',
        'no_invoice',
        'ongkos_kirim',
        'createdAt',
        'updatedAt',
        'deletedAt',
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

    public function getPenerimaanBarangList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_penerimaan_barang' => 'penerimaan_barang.no_penerimaan_barang',
            'warehouse_name'       => 'warehouses.warehouse_name',
            'tipe_bahan'           => 'penerimaan_barang.tipe_bahan',
            'supplier_name'        => 'suppliers.name',
            'createdAt'            => 'penerimaan_barang.createdAt',
            'updatedAt'            => 'penerimaan_barang.updatedAt',
            'divisi'               => 'divisis.divisi',
            'metadata.value'       => 'metadata.value'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort     = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        // --- Build base query (tanpa COUNT dulu) ---
        $baseQuery = $this->db->table('penerimaan_barang')
            ->select('penerimaan_barang.*,
                    suppliers.name AS supplier_name,
                    warehouses.warehouse_name,
                    divisis.divisi,
                    metadata.value AS bc_type_name,
                    bc_purchase_order_lpb.bc_purchase_order_id
                  ')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang.id');

        // --- Filter search ---
        if (!empty($addCondition['search'])) {
            $baseQuery->groupStart()
                ->like('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('penerimaan_barang.multiple_po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.multiple_spp_no', $addCondition['search'])
                ->orLike('metadata.value', $addCondition['search'])
                ->groupEnd();
        }

        if (!empty($addCondition['status'])) {
            $baseQuery->where('penerimaan_barang.status_post', $addCondition['status']);
        }

        if (!empty($addCondition['startdate'])) {
            $baseQuery->where('penerimaan_barang.tanggal >=', $addCondition['startdate']);
        }
        if (!empty($addCondition['lastdate'])) {
            $baseQuery->where('penerimaan_barang.tanggal <=', $addCondition['lastdate']);
        }

        // --- Hitung totalFilteredData dengan query clone (tanpa limit) ---
        $totalFilteredData = clone $baseQuery;
        $totalFilteredData = $totalFilteredData->countAllResults(false);

        // --- Hitung totalData (tanpa filter search/status/date) ---
        $totalDataQuery = $this->db->table('penerimaan_barang')
            ->where($condition);
        $totalData = $totalDataQuery->countAllResults();

        // --- Ambil data utama dengan limit/offset ---
        $dataList = $baseQuery
            ->orderBy($sort, $sortType)
            ->limit($limit, $offset)
            ->get()
            ->getResultObject();

        // --- Ambil itemCount sekaligus untuk semua ID di page ini ---
        $penerimaanBarangIds = array_column($dataList, 'id');
        $itemCounts = [];
        if (!empty($penerimaanBarangIds)) {
            $itemCountQuery = $this->db->table('penerimaan_barang_detail')
                ->select('penerimaan_barang_id, COUNT(*) AS item_count')
                ->whereIn('penerimaan_barang_id', $penerimaanBarangIds)
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

    public function getPenerimaanBarangListBackup($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_penerimaan_barang'      => 'penerimaan_barang.no_penerimaan_barang',
            'warehouse_name'            => 'warehouses.warehouse_name',
            'tipe_bahan'                => 'penerimaan_barang.tipe_bahan',
            'supplier_name'             => 'suppliers.name',
            'createdAt'                 => 'penerimaan_barang.createdAt',
            'updatedAt'                 => 'penerimaan_barang.updatedAt',
            'divisi'                    => 'divisis.divisi',
            'metadata.value'            => 'metadata.value'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
        DISTINCT(penerimaan_barang.id), 
        penerimaan_barang.no_penerimaan_barang,
        penerimaan_barang.multiple_po_id,
        penerimaan_barang.tipe_bahan,
        penerimaan_barang.tanggal,
        penerimaan_barang.multiple_po_no,
        penerimaan_barang.multiple_spp_no,
        penerimaan_barang.status_post,
        penerimaan_barang.bc_type,
        penerimaan_barang.divisi_id,
        bc_purchase_order_lpb.bc_purchase_order_id,
        warehouses.warehouse_name, suppliers.name as supplier_name, 
        COUNT(penerimaan_barang_detail.id) AS itemCount, 
        divisis.divisi,
        metadata.value as bc_type_name";
        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'right')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->groupBy('penerimaan_barang.id');


        if (in_array($condition['penerimaan_barang.status_penerimaan'], ['LOKAL', 'IMPORT']) && $condition['tipe_bahan'] == "PENOLONG") {
            // KHSUSUS LPB LOKAL BP & LOKAL BB
            $penerimaanBarangDataQry->join('am_purchase_orders', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id', 'left');
            $penerimaanBarangDataQry->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left');
            $penerimaanBarangDataQry->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left');
        } elseif ($condition['penerimaan_barang.status_penerimaan'] == "LOKAL" && $condition['tipe_bahan'] == "BAKU") {
            // KHUSUS LPB LOKAL BB
            $penerimaanBarangDataQry->join('rm_purchase_order_details', 'rm_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left');
        } elseif ($condition['penerimaan_barang.status_penerimaan'] == "IMPOR" && $condition['tipe_bahan'] == "BAKU") {
            // KHUSUS LPB IMPORT BB
            $penerimaanBarangDataQry->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left');
        }


        if (isset($addCondition['status'])) {
            $penerimaanBarangDataQry->where('penerimaan_barang.status_post', $addCondition['status']);
        }

        if ($addCondition['nama_barang']) {
            $penerimaanBarangDataQry->groupStart();
            $penerimaanBarangDataQry->like('LOWER(CONCAT(barang_master.barang_name," ",barang_master_spesifikasi.spesifikasi))', $addCondition['nama_barang'])
                ->orLike('LOWER(barang_master.kode_barang)', $addCondition['nama_barang']);
            $penerimaanBarangDataQry->groupEnd();
        }

        if ($addCondition['note']) {
            $penerimaanBarangDataQry->groupStart();
            if (in_array($condition['penerimaan_barang.status_penerimaan'], ['LOKAL', 'IMPORT']) && $condition['tipe_bahan'] == "PENOLONG") {
                // KHSUSUS LPB LOKAL BP & LOKAL BB
                $penerimaanBarangDataQry->like('LOWER(am_purchase_order_details.note)', $addCondition['note']);
            } elseif ($condition['penerimaan_barang.status_penerimaan'] == "LOKAL" && $condition['tipe_bahan'] == "BAKU") {
                // KHSUSUS LPB LOKAL LOKAL BB
                $penerimaanBarangDataQry->like('LOWER(rm_purchase_order_details.note)', $addCondition['note']);
            } elseif ($condition['penerimaan_barang.status_penerimaan'] == "IMPOR" && $condition['tipe_bahan'] == "BAKU") {
                // KHUSUS LPB IMPORT BB
                $penerimaanBarangDataQry->like('LOWER(rm_import_po_details.note)', $addCondition['note']);
            }
            $penerimaanBarangDataQry->groupEnd();
        }

        if ($addCondition['search']) {
            $penerimaanBarangDataQry->groupStart();
            $penerimaanBarangDataQry->like('penerimaan_barang.no_penerimaan_barang', $addCondition['search']);
            $penerimaanBarangDataQry->orLike('suppliers.name', $addCondition['search']);
            $penerimaanBarangDataQry->orLike('warehouses.warehouse_name', $addCondition['search']);
            $penerimaanBarangDataQry->orLike('penerimaan_barang.multiple_po_no', $addCondition['search']);
            $penerimaanBarangDataQry->orLike('divisis.divisi', $addCondition['search']);
            $penerimaanBarangDataQry->orLike('metadata.value', $addCondition['search']);

            if ($condition['penerimaan_barang.status_penerimaan'] == "LOKAL" && $condition['tipe_bahan'] == "PENOLONG") {
                $penerimaanBarangDataQry->orLike('purchase_requests.spp_no', $addCondition['search']);
            }

            $penerimaanBarangDataQry->groupEnd();
        }

        if ($addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal <=', $addCondition['lastdate']);
        }

        if ($addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupEnd();
        }


        $penerimaanBarangDataQry->groupBy(('penerimaan_barang.id'))
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);


        $totalFilteredData = $penerimaanBarangDataQry->countAllResults(false);
        $data = $penerimaanBarangDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListForAccounting($condition, $addCondition, $limit = 10, $offset = 0, $companyId)
    {
        $availableSort = [
            'no_penerimaan_barang'      => 'penerimaan_barang.no_penerimaan_barang',
            'warehouse_name'            => 'warehouses.warehouse_name',
            'tipe_bahan'                => 'penerimaan_barang.tipe_bahan',
            'supplier_name'             => 'suppliers.name',
            'createdAt'                 => 'penerimaan_barang.createdAt',
            'updatedAt'                 => 'penerimaan_barang.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_barang.*, 
                    DATE_FORMAT(penerimaan_barang.tanggal, '%d/%m/%Y') AS tanggal_penerimaan,
                    warehouses.warehouse_name, 
                    suppliers.name as supplier_name, 
                    COUNT(penerimaan_barang_detail.id) AS itemCount, 
                    penerimaan_barang_detail.harga,
                    SUM(penerimaan_barang_detail.sub_total) as harga_sub_total,
                    bc_23.no_aju AS BC23_AJU,
                    bc_40.no_aju AS BC40_AJU,
                    divisis.divisi as divisi";
        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'right')
            ->join('bc_purchase_order bc_23_po', 'bc_23_po.id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order bc_40_po', 'bc_40_po.id = penerimaan_barang.id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_23_po.id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_40_po.id', 'left')
            ->join('divisis', 'divisis.divisi = penerimaan_barang.divisi_id', 'left')
            ->groupBy(('penerimaan_barang.id'))
            ->where($condition)
            ->whereIn('penerimaan_barang.company_id', $companyId)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangDataQry->like('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('penerimaan_barang.multiple_po_no', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $penerimaanBarangDataQry->whereIn('suppliers.id', $addCondition['filter']);
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal <=', $addCondition['lastdate']);
        }

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupEnd();
        }

        $totalFilteredData = $penerimaanBarangDataQry->countAllResults(false);
        $data = $penerimaanBarangDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListForPrintAccounting($condition, $addCondition, $companyId)
    {
        $availableSort = [
            'no_penerimaan_barang'      => 'penerimaan_barang.no_penerimaan_barang',
            'warehouse_name'            => 'warehouses.warehouse_name',
            'tipe_bahan'                => 'penerimaan_barang.tipe_bahan',
            'supplier_name'             => 'suppliers.name',
            'createdAt'                 => 'penerimaan_barang.createdAt',
            'updatedAt'                 => 'penerimaan_barang.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_barang.*, 
                DATE_FORMAT(penerimaan_barang.tanggal, '%d/%m/%Y') AS tanggal_penerimaan,
                warehouses.warehouse_name, 
                suppliers.name as supplier_name, 
                COUNT(penerimaan_barang_detail.id) AS itemCount, 
                penerimaan_barang_detail.harga,
                SUM(penerimaan_barang_detail.sub_total) as harga_sub_total,
                bc_23.no_aju AS BC23_AJU,
                bc_40.no_aju AS BC40_AJU,
                divisis.divisi as divisi";

        // dd($condition, $addCondition, $companyId);

        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'right')
            ->join('bc_purchase_order bc_23_po', 'bc_23_po.id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order bc_40_po', 'bc_40_po.id = penerimaan_barang.id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_23_po.id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_40_po.id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->groupBy('penerimaan_barang.id')
            ->where($condition)
            ->whereIn('penerimaan_barang.company_id', $companyId)
            ->orderBy($sort, $sortType);

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangDataQry->like('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('penerimaan_barang.multiple_po_no', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $penerimaanBarangDataQry->whereIn('suppliers.id', $addCondition['filter']);
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.tanggal <=', $addCondition['lastdate']);
        }

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupEnd();
        }

        $totalData = $penerimaanBarangDataQry->countAllResults(false);
        $totalFilteredData = $totalData;
        $data = $penerimaanBarangDataQry->findAll();
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
        $selectQry = "penerimaan_barang.*, 
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
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('companies', 'companies.id = penerimaan_barang.company_id', 'left')
            ->find($id);

        return $sppData;
    }

    public function getByIdPrintBahanBaku($id)
    {
        $selectQry = "penerimaan_barang.*, 
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
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join(
                'rm_purchase_orders',
                'FIND_IN_SET(rm_purchase_orders.id, REPLACE(REPLACE(REPLACE(penerimaan_barang.multiple_po_id, "[", ""), "]", ""), " ", ""))',
                'left'
            )
            ->where('penerimaan_barang.id', $id)
            ->first();

        return $sppData;
    }

    public function get_no($tanggal, $companyId, $statusPenerimaan, $tipeBahan)
    {
        // Validasi input
        if (empty($tanggal) || empty($companyId) || empty($statusPenerimaan) || empty($tipeBahan)) {
            throw new InvalidArgumentException("Semua parameter harus diisi");
        }

        // Validasi format tanggal
        if (!strtotime($tanggal)) {
            throw new InvalidArgumentException("Format tanggal tidak valid");
        }

        // Ambil Tanggal
        $tanggalArr = explode('-', $tanggal);
        if (count($tanggalArr) < 3) {
            throw new InvalidArgumentException("Format tanggal harus YYYY-MM-DD");
        }

        $bulanF = $tanggalArr[1];
        $tahunF = date('y', strtotime($tanggal)); // Output contoh: 25

        // Template dasar
        $template = '';

        // Mapping berdasarkan companyId
        if ($companyId == 1) {
            $prefix = ($statusPenerimaan == "LOKAL") ? "" : "IMP/";
            if ($tipeBahan == "BAKU") {
                $template = "LBB/F/" . $prefix . $bulanF . $tahunF;
            } elseif ($tipeBahan == "PENOLONG") {
                $template = "LPB/F/" . $prefix . $bulanF . $tahunF;
            }
        } elseif ($companyId == 2) {
            $prefix = ($statusPenerimaan == "LOKAL") ? "" : "IMP/";
            if ($tipeBahan == "BAKU") {
                $template = "LBB/" . $prefix . $bulanF . $tahunF;
            } elseif ($tipeBahan == "PENOLONG") {
                $template = "LPB/" . $prefix . $bulanF . $tahunF;
            }
        } elseif ($companyId == 15) {
            $prefix = ($statusPenerimaan == "LOKAL") ? "" : "IMP/";
            if ($tipeBahan == "BAKU") {
                $template = "LBB/" . $prefix . "G/" . $bulanF . $tahunF;
            } elseif ($tipeBahan == "PENOLONG") {
                $template = "LPB/" . $prefix . "G/" . $bulanF . $tahunF;
            }
        } elseif ($companyId == 16) {
            $prefix = ($statusPenerimaan == "LOKAL") ? "" : "IMP/";
            if ($tipeBahan == "BAKU") {
                $template = "LBB/" . $prefix . "O/" . $bulanF . $tahunF;
            } elseif ($tipeBahan == "PENOLONG") {
                $template = "LPB/" . $prefix . "O/" . $bulanF . $tahunF;
            }
        }

        // Jika template masih kosong, berarti kombinasi parameter tidak valid
        if (empty($template)) {
            throw new InvalidArgumentException("Kombinasi parameter tidak valid");
        }

        // Hitung tanggal akhir bulan
        $lastDayOfMonth = date('Y-m-t', strtotime($tanggal));
        $startDayOfMonth = date('Y-m', strtotime($lastDayOfMonth)) . "-01";

        // Query cari nomor terakhir
        $builder = $this->db->table('penerimaan_barang');
        $builder->select('no_penerimaan_barang');
        $builder->orderBy('no_penerimaan_barang', 'desc');
        $builder->where([
            'company_id' => $companyId,
            'status_penerimaan' => $statusPenerimaan,
            'tipe_bahan' => $tipeBahan,
            'deletedAt' => null
        ]);
        $builder->where('tanggal >=', $startDayOfMonth);
        $builder->where('tanggal <=', $lastDayOfMonth);
        $builder->like('no_penerimaan_barang', $template, 'after');
        $query = $builder->get();

        // Ambil semua existing number
        $existingNumbers = [];
        foreach ($query->getResultArray() as $row) {
            $string = $row['no_penerimaan_barang'];
            $explode = explode('/', $string);
            $last = end($explode);

            // Pastikan bagian terakhir adalah angka
            if (is_numeric($last)) {
                $existingNumbers[] = intval($last);
            }
        }

        // Jika tidak ada nomor yang ada, mulai dari 1
        if (empty($existingNumbers)) {
            $nextNumber = 1;
        } else {
            // Urutkan dan cari celah
            sort($existingNumbers);
            $nextNumber = 1;

            foreach ($existingNumbers as $num) {
                if ($num > $nextNumber) {
                    // Ditemukan celah, gunakan celah ini
                    break;
                }
                $nextNumber = $num + 1;
            }
        }

        // Format nomor
        if ($statusPenerimaan == "LOKAL" && $tipeBahan == "BAKU") {
            // Lima digit
            $formattedNumber = sprintf("%05d", $nextNumber);
        } else {
            // Empat digit
            $formattedNumber = sprintf("%04d", $nextNumber);
        }

        // Hasil akhir
        return $template . '/' . $formattedNumber;
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
            'createdAt'         => 'penerimaan_barang.createdAt',
            'updatedAt'         => 'penerimaan_barang.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$condition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$condition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_barang_detail.id AS id,
                      DATE_FORMAT(penerimaan_barang.createdAt, '%d/%m/%Y') AS lpb_date,
                      penerimaan_barang.no_penerimaan_barang AS no_lpb,
                      penerimaan_barang.multiple_po_no,
                      penerimaan_barang_detail.nama_barang_dok AS item_name,
                      (`penerimaan_barang_detail`.`qty` - `penerimaan_barang_detail`.`summarized_qty`) AS lpb_qty,
                      (penerimaan_barang_detail.harga + penerimaan_barang_detail.harga_harian + penerimaan_barang_detail.harga_bulanan) AS price,
                      satuans.kode_satuan AS unit";
        $receiveDataQry = $this->asObject()
            ->select($selectQry)
            ->where('penerimaan_barang.supplier_id', $supplierId)
            ->where($condition)
            ->where('(`penerimaan_barang_detail`.`qty` - `penerimaan_barang_detail`.`summarized_qty`) > 0')
            ->where("penerimaan_barang_detail.summarized_qty <", 'penerimaan_barang_detail.qty', false)
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit AND satuans.deletedAt IS NULL')
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
            ->where('penerimaan_barang.supplier_id', $supplierId)
            ->where($condition)
            // ->orderBy($sort, $sortType)
            ->findAll();

        return $receiveDataQry;
    }

    public function generateLpbBBBackup($poID, $warehouseID, $dokumenBC, $tanggalPenerimaanLPB)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $rmPurchaseOrder = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $stockDetailModel = new StockDetailModel();
        $stockModel = new StockModel();
        $stockDetail2Model = new StockDetail2Model();
        $barangMasterModel = new BarangMasterModel();
        $penerimaaanBarangLokalBp = new PenerimaanBarangLokalBP();

        $rmDetail =  $rmPurchaseOrder->where('id', $poID)->first();
        $rmBarangDetail = $rmPurchaseOrderDetailModel->where('rm_purchase_order_id', $poID)->findAll();

        // MASUKKAN STOK BARANG DAN KEMASAN JIKA NON PABEAN 
        // (JIKA ADA BC MASUK KE INVENTORI DI MODUL BEA CUKAI)
        if ($rmDetail['bc_type'] == 0 && $rmDetail['status_external'] == "no") {

            foreach ($rmBarangDetail as $r) {
                // HANDLE STOK BARANG

                // CHECK STOK BARANG HEADER
                $stok = $stockModel->getStokMaster(
                    $rmDetail['company_id'],
                    $rmDetail['warehouse_id'],
                    $rmDetail['divisi_id'],
                    "bahan_baku",
                    $r['barang1_id'],
                    $r['barang2_id'],
                );

                if ($stok == null) {
                    $stok = $stockModel->insertStok(
                        $rmDetail['company_id'],
                        $rmDetail['warehouse_id'],
                        $rmDetail['divisi_id'],
                        "bahan_baku",
                        $r['barang1_id'],
                        $r['barang2_id'],
                        0
                    );
                }
            }

            // CHECK STOK KEMASAN HEADER
            $stok = $stockModel->getStokMaster(
                $rmDetail['company_id'],
                $rmDetail['warehouse_id'],
                $rmDetail['divisi_id'],
                "kemasan",
                $rmDetail['bc_type'],
                $rmDetail['kemasan_id'],
            );

            if ($stok == null) {
                $stok = $stockModel->insertStok(
                    $rmDetail['company_id'],
                    $rmDetail['warehouse_id'],
                    $rmDetail['divisi_id'],
                    "kemasan",
                    0,
                    $rmDetail['kemasan_id'],
                    0
                );
            }
        }


        // no lpb
        $warehouseModel = new WarehousesModel();
        $warehouse = $warehouseModel->where('id', $warehouseID)->first();
        $no = $penerimaanBarangModel->get_no(
            $rmDetail['po_date'],
            $rmDetail['company_id'],
            "LOKAL",
            "BAKU",
        );


        $payloadPenerimaanBarang = [
            "company_id" => $rmDetail['company_id'],
            "no_penerimaan_barang" => $no,
            "supplier_id" => $rmDetail['supplier_id'],
            "warehouse_id" => $warehouseID,
            "divisi_id" => $warehouse['divisi_id'],
            "acceptance_type" => "SINGLE ORDER",
            "multiple_po_id" => '[' . $rmDetail['id'] . ']',
            "multiple_po_no" => '["' . $rmDetail['po_no'] . '"]',
            "kemasan_id" => $rmDetail['kemasan_id'],
            "kemasan" => $rmDetail['kemasan_tambahan'],
            "jumlah_kemasan" => $rmDetail['jumlah_kemasan'],
            "tipe_bahan" => "BAKU",
            "bc_type" => $dokumenBC,
            "status_post" => "FINISH",
            "status_penerimaan" => "LOKAL",
            "tanggal" => $tanggalPenerimaanLPB
        ];

        $lpbID = $penerimaanBarangModel->insert($payloadPenerimaanBarang);

        foreach ($rmBarangDetail as $r) {
            $selectQry = "
                barang_master.id as bahan_baku_id, 
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                barang_master_spesifikasi.satuan_1,
                barang_master_spesifikasi.satuan_2,
                barang_master_spesifikasi.satuan_3,
                barang_master_spesifikasi.konversi_satuan_2,
                barang_master_spesifikasi.konversi_satuan_3,
            ";

            $barang = $barangMasterModel->select($selectQry)
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                ->where('barang_master.id', $r['barang1_id'])
                ->where('barang_master_spesifikasi.id', $r['barang2_id'])
                ->first();

            $nilaiKonversi = 1;
            $satuanKonversiId = $barang == null ? null : $barang['satuan_1'];
            if ($r['satuan_id'] == $barang['satuan_1']) {
                $nilaiKonversi = 1;
            } elseif ($r['satuan_id'] == $barang['satuan_2']) {
                $nilaiKonversi = $barang['konversi_satuan_2'];
            } elseif ($r['satuan_id'] == $barang['satuan_3']) {
                $nilaiKonversi = $barang['konversi_satuan_3'];
            }

            $penerimaanBarangDetailModel->insert([
                'purchase_order_id' => $r['rm_purchase_order_id'],
                'purchase_order_details_id' => $r['id'],
                'penerimaan_barang_id' => $lpbID,
                'spesifikasi_id' => $r['barang2_id'],
                'harga' => $r['general_price'],
                'harga_harian' => $r['daily_price'],
                'harga_bulanan' => $r['monthly_price'],
                'sub_total' => ($r['general_price'] +  $r['daily_price'] + $r['monthly_price']) * $r['qty'],
                'keterangan' => $r['note'],
                'barang_id' => $barang['bahan_baku_id'],
                'qty' => $r['qty'],
                'unit' => $r['satuan_id'],
                'nama_barang_dok' => $barang['barang_name'] . " (" . $barang['spesifikasi'] . ")",
                'jml_masuk' => $r['qty'],
                'jml_masuk_konversi' => ($r['qty'] * $nilaiKonversi),
                'unit_konversi' => $satuanKonversiId,
                // 'packaging' => "-",
                // 'packaging_qty' => $r['qty']
            ]);

            // UPDATE QTY DITERIMA
            $rmPurchaseOrderDetailModel->update($r['id'], [
                'qty_diterima' => $r['qty'],
                'remaining_qty' => 0
            ]);
        }

        // TAMBAJKAN STOK DISINI
        $penerimaanBarang = $penerimaanBarangModel->where('id', $lpbID)->first();
        $penerimaanBarangList = $penerimaanBarangDetailModel->where('penerimaan_barang_id', $lpbID)->where('deletedAt', null)->findAll();

        // NON PABEAN LANGSUNG INPUTKAN STOK NYA
        if ($penerimaanBarang['bc_type'] == 0 && $rmDetail['status_external'] == "no") {

            $res = $penerimaaanBarangLokalBp->insert_stock_pembelian_revamp(
                $penerimaanBarang['id']
            );
            if (!$res) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
                    'token' => csrf_hash()
                ]);
            }

            // STOK BARANG DIINPUT
            // KHUSUS INTERNAL
            foreach ($penerimaanBarangList as $p) {
                // HEADER
                $stok = $stockModel->insertStok(
                    $penerimaanBarang['company_id'],
                    $penerimaanBarang['warehouse_id'],
                    $penerimaanBarang['divisi_id'],
                    "bahan_baku",
                    $p['barang_id'],
                    $p['spesifikasi_id'],
                    $p['jml_masuk_konversi']
                );

                // DETAIL
                $stokDetail = $stockDetailModel->insertStokDetail(
                    $stok,
                    $p['jml_masuk_konversi'],
                    'In',
                    $penerimaanBarang['tanggal'],
                    $rmDetail['createdBy'],
                    "LPB",
                    $penerimaanBarang['no_penerimaan_barang'],
                    "-",
                );

                // GET PURCHASE ORDER
                $po = $rmPurchaseOrder->find($p['purchase_order_id']);
                // SUB DETAIL
                $stockDetail2Model->insertStokDetail2(
                    $penerimaanBarang['bc_type'],
                    $stok,
                    $stokDetail,
                    $p['jml_masuk_konversi'],
                    "-",
                    $po['po_no'],
                    $po['po_no'],
                    $penerimaanBarang['supplier_id'],
                    $p['harga'],
                    $p['harga_harian'],
                    $p['harga_bulanan'],
                    $po['po_no'],
                );
            }

            // KEMASAN
            // HEADER
            $stok = $stockModel->insertStok(
                $penerimaanBarang['company_id'],
                $penerimaanBarang['warehouse_id'],
                $penerimaanBarang['divisi_id'],
                "kemasan",
                0,
                $penerimaanBarang['kemasan_id'],
                $penerimaanBarang['jumlah_kemasan']
            );


            // DETAIL
            $stokDetail = $stockDetailModel->insertStokDetail(
                $stok,
                $penerimaanBarang['jumlah_kemasan'],
                "In",
                date('Y-m-d'),
                $rmDetail['createdBy'],
                "LPB",
                $penerimaanBarang['no_penerimaan_barang'],
                "-",
            );

            // SUB DETAIL
            $stockDetail2Model->insertStokDetail2(
                $penerimaanBarang['bc_type'],
                $stok,
                $stokDetail,
                $penerimaanBarang['jumlah_kemasan'],
                "-",
                $penerimaanBarang['no_penerimaan_barang'],
                $penerimaanBarang['no_penerimaan_barang'],
                $penerimaanBarang['supplier_id'],
            );
        }

        $this->autoClosePO($lpbID);

        return $lpbID;
    }

    public function generateLpbBB($poID, $warehouseID, $dokumenBC, $tanggalPenerimaanLPB)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $rmPurchaseOrder = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $barangMasterModel = new BarangMasterModel();
        $penerimaaanBarangLokalBp = new PenerimaanBarangLokalBP();
        $warehouseModel = new WarehousesModel();

        $rmDetail =  $rmPurchaseOrder->where('id', $poID)->first();
        $rmBarangDetail = $rmPurchaseOrderDetailModel->where('rm_purchase_order_id', $poID)->findAll();


        // no lpb
        $warehouse = $warehouseModel->where('id', $warehouseID)->first();
        $no = $penerimaanBarangModel->get_no(
            $rmDetail['po_date'],
            $rmDetail['company_id'],
            "LOKAL",
            "BAKU",
        );


        $payloadPenerimaanBarang = [
            "company_id" => $rmDetail['company_id'],
            "no_penerimaan_barang" => $no,
            "supplier_id" => $rmDetail['supplier_id'],
            "warehouse_id" => $warehouseID,
            "divisi_id" => $warehouse['divisi_id'],
            "acceptance_type" => "SINGLE ORDER",
            "multiple_po_id" => '[' . $rmDetail['id'] . ']',
            "multiple_po_no" => '["' . $rmDetail['po_no'] . '"]',
            "kemasan_id" => $rmDetail['kemasan_id'],
            "kemasan" => $rmDetail['kemasan_tambahan'],
            "jumlah_kemasan" => $rmDetail['jumlah_kemasan'],
            "tipe_bahan" => "BAKU",
            "bc_type" => $dokumenBC,
            "status_post" => "FINISH",
            "status_penerimaan" => "LOKAL",
            "tanggal" => $tanggalPenerimaanLPB
        ];

        $lpbID = $penerimaanBarangModel->insert($payloadPenerimaanBarang);

        foreach ($rmBarangDetail as $r) {
            $selectQry = "
                barang_master.id as bahan_baku_id, 
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                barang_master_spesifikasi.satuan_1,
                barang_master_spesifikasi.satuan_2,
                barang_master_spesifikasi.satuan_3,
                barang_master_spesifikasi.konversi_satuan_2,
                barang_master_spesifikasi.konversi_satuan_3,
            ";

            $barang = $barangMasterModel->select($selectQry)
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                ->where('barang_master.id', $r['barang1_id'])
                ->where('barang_master_spesifikasi.id', $r['barang2_id'])
                ->first();

            $nilaiKonversi = 1;
            $satuanKonversiId = $barang == null ? null : $barang['satuan_1'];
            if ($r['satuan_id'] == $barang['satuan_1']) {
                $nilaiKonversi = 1;
            } elseif ($r['satuan_id'] == $barang['satuan_2']) {
                $nilaiKonversi = $barang['konversi_satuan_2'];
            } elseif ($r['satuan_id'] == $barang['satuan_3']) {
                $nilaiKonversi = $barang['konversi_satuan_3'];
            }

            $penerimaanBarangDetailModel->insert([
                'purchase_order_id' => $r['rm_purchase_order_id'],
                'purchase_order_details_id' => $r['id'],
                'penerimaan_barang_id' => $lpbID,
                'spesifikasi_id' => $r['barang2_id'],
                'harga' => $r['general_price'],
                'harga_harian' => $r['daily_price'],
                'harga_bulanan' => $r['monthly_price'],
                'sub_total' => ($r['general_price'] +  $r['daily_price'] + $r['monthly_price']) * $r['qty'],
                'keterangan' => $r['note'],
                'barang_id' => $barang['bahan_baku_id'],
                'qty' => $r['qty'],
                'unit' => $r['satuan_id'],
                'nama_barang_dok' => $barang['barang_name'] . " (" . $barang['spesifikasi'] . ")",
                'jml_masuk' => $r['qty'],
                'jml_masuk_konversi' => ($r['qty'] * $nilaiKonversi),
                'unit_konversi' => $satuanKonversiId,
                // 'packaging' => "-",
                // 'packaging_qty' => $r['qty']
            ]);

            // UPDATE QTY DITERIMA
            $rmPurchaseOrderDetailModel->update($r['id'], [
                'qty_diterima' => $r['qty'],
                'remaining_qty' => 0
            ]);
        }

        // TAMBAJKAN STOK DISINI
        $penerimaanBarang = $penerimaanBarangModel->where('id', $lpbID)->first();
        if ($penerimaanBarang['bc_type'] == 0 && $rmDetail['status_external'] == "no") {

            $res = $penerimaaanBarangLokalBp->insert_stock_pembelian_revamp(
                $penerimaanBarang['id']
            );
            if (!$res) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
                    'token' => csrf_hash()
                ]);
            }
        }

        $this->autoClosePO($lpbID);

        return $lpbID;
    }

    public function autoClosePO($penerimaanBarangID)
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel(); // bp lokal or import
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel(); // bp lokal or import
        $rmPurchaseOrderModel = new RMPurchaseOrderModel(); // bb lokal
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel(); // bb lokal
        $rmImportPoModel = new RMImportPOModel(); // bb import
        $rmImportPoDetailModel = new RMImportPODetailModel(); // bb import

        // get type penerimaan dan tipe bahan
        $penerimaanFirst = $this->asArray()->where('id', $penerimaanBarangID)->first();

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

    public function autoOpenPO($penerimaanBarangID)
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel(); // bp lokal or import
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel(); // bp lokal or import
        $rmPurchaseOrderModel = new RMPurchaseOrderModel(); // bb lokal
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel(); // bb lokal
        $rmImportPoModel = new RMImportPOModel(); // bb import
        $rmImportPoDetailModel = new RMImportPODetailModel(); // bb import

        // get type penerimaan dan tipe bahan
        $penerimaanFirst = $this->asArray()->where('id', $penerimaanBarangID)->first();

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
                        'status_penerimaan' => 0
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
                        'status_penerimaan' => 0
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
                        'status_penerimaan' => 0
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
                        'status_penerimaan' => 0
                    ]);
                }
            }
        }
    }

    public function getListLaporanPenerimaanBarangBahanBakuLokal($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'          => 'divisis.divisi',
            'supplier_id'     => 'penerimaan_barang.supplier_id',
            'bc_type'         => 'penerimaan_barang.bc_type',
            'tanggal_dokumen' => 'bc_purchase_order.createdAt',
            'no_daftar'       => 'bc_purchase_order.no_daftar',
            'no_aju'          => 'bc_40.no_aju',
            'tanggal_lpb'     => 'penerimaan_barang.tanggal',
            'no_lpb'          => 'penerimaan_barang.no_penerimaan_barang',
            'po_date'         => 'rm_purchase_orders.po_date',
            'po_no'           => 'rm_purchase_orders.po_no',
            'kode_barang'     => 'barang_master.kode_barang',
            'barang_name'     => 'barang_master.barang_name',
            'spesifikasi'     => 'barang_master_spesifikasi.spesifikasi',
            'satuan_id'       => 'penerimaan_barang_detail.unit',
            'keterangan'      => 'penerimaan_barang_detail.keterangan',
            'qty_order'       => 'qty_order',
            'qty_diterima'    => 'qty_diterima',
            'total_harga'     => 'rm_purchase_orders.total_before_pph',
            'harga_satuan'    => 'penerimaan_barang_detail.harga'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'penerimaan_barang.tanggal';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            penerimaan_barang_detail.id,
            penerimaan_barang.tanggal AS tanggal_lpb,
            penerimaan_barang.no_penerimaan_barang AS no_lpb,
            divisis.divisi,
            suppliers.name AS supplier_name,
            metadata.value AS bc_name,
            bc_purchase_order.createdAt AS tanggal_dokumen,
            bc_purchase_order.no_daftar,
            bc_40.no_aju,
            barang_master.kode_barang,
            barang_master.barang_name,
            satuans.kode_satuan,
            IFNULL(GROUP_CONCAT(DISTINCT penerimaan_barang_detail.keterangan SEPARATOR ', '), '') AS keterangan,
            IFNULL(GROUP_CONCAT(DISTINCT barang_master_spesifikasi.spesifikasi SEPARATOR ', '), '') AS spesifikasi,
            SUM(penerimaan_barang_detail.qty) AS qty_order,
            SUM(penerimaan_barang_detail.jml_masuk) AS qty_diterima,
            rm_purchase_orders.po_date,
            rm_purchase_orders.po_no,
            rm_purchase_orders.total_before_pph AS total_harga,
            parent_barang.parent_name AS kategori_barang
        ";
        $builder = $this->asArray()
            ->select($selectQry)
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang.id, penerimaan_barang_detail.purchase_order_id')
            ->orderBy($sort, $sortType);
        $totalData = $builder->countAllResults(false);
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
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
        $sub = $this->db->table('penerimaan_barang')
            ->select('rm_purchase_orders.id, rm_purchase_orders.total_before_pph')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang.id, penerimaan_barang_detail.purchase_order_id');

        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $sub->where('penerimaan_barang.bc_type', 0);
            } else {
                $sub->where('penerimaan_barang.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $sub->where('penerimaan_barang.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $sub->where('penerimaan_barang.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $sub->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $sub->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $sub->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $sub->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $sub->groupStart()
                ->like('rm_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
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

    public function getListLaporanPenerimaanBarangBahanPenolongLokal($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'          => 'divisis.divisi',
            'supplier_id'     => 'penerimaan_barang.supplier_id',
            'bc_type'         => 'penerimaan_barang.bc_type',
            'tanggal_dokumen' => 'bc_purchase_order.createdAt',
            'no_daftar'       => 'bc_purchase_order.no_daftar',
            'no_aju'          => 'bc_40.no_aju',
            'tanggal_lpb'     => 'penerimaan_barang.tanggal',
            'no_lpb'          => 'penerimaan_barang.no_penerimaan_barang',
            'po_date'         => 'am_purchase_orders.po_date',
            'po_no'           => 'am_purchase_orders.po_no',
            'kode_barang'     => 'barang_master.kode_barang',
            'barang_name'     => 'barang_master.barang_name',
            'spesifikasi'     => 'barang_master_spesifikasi.spesifikasi',
            'satuan_id'       => 'penerimaan_barang_detail.unit',
            'keterangan'      => 'penerimaan_barang_detail.keterangan',
            'qty_order'       => 'qty_order',
            'qty_diterima'    => 'qty_diterima',
            'total_harga'     => 'penerimaan_barang_detail.sub_total',
            'harga_satuan'    => 'penerimaan_barang_detail.harga'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'penerimaan_barang.tanggal';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            penerimaan_barang_detail.id,
            penerimaan_barang.tanggal AS tanggal_lpb,
            penerimaan_barang.no_penerimaan_barang AS no_lpb,
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
            penerimaan_barang_detail.qty AS qty_order,
            penerimaan_barang_detail.jml_masuk AS qty_diterima,
            am_purchase_orders.po_date,
            am_purchase_orders.po_no,
            penerimaan_barang_detail.sub_total AS total_harga,
            penerimaan_barang_detail.harga AS harga_satuan,
            tb_valas.value AS valas_name,
            parent_barang.parent_name AS kategori_barang
        ";
        $builder = $this->db->table('penerimaan_barang_detail')
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = am_purchase_orders.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_detail.id')
            ->orderBy($sort, $sortType);
        $totalData = $builder->countAllResults(false);
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
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

        $sub = $this->db->table('penerimaan_barang_detail')
            ->select('SUM(penerimaan_barang_detail.sub_total) AS total_harga')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = am_purchase_orders.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->where($condition);
        // Filter
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $sub->where('penerimaan_barang.bc_type', 0);
            } else {
                $sub->where('penerimaan_barang.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $sub->where('penerimaan_barang.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $sub->where('penerimaan_barang.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $sub->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $sub->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $sub->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $sub->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $sub->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
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

    public function getListLaporanPenerimaanBarangBahanPenolongImpor($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'          => 'divisis.divisi',
            'supplier_id'     => 'penerimaan_barang.supplier_id',
            'bc_type'         => 'penerimaan_barang.bc_type',
            'tanggal_dokumen' => 'bc_purchase_order.createdAt',
            'no_daftar'       => 'bc_purchase_order.no_daftar',
            'no_aju'          => 'bc_23.no_aju',
            'tanggal_lpb'     => 'penerimaan_barang.tanggal',
            'no_lpb'          => 'penerimaan_barang.no_penerimaan_barang',
            'po_date'         => 'am_purchase_orders.po_date',
            'po_no'           => 'am_purchase_orders.po_no',
            'kode_barang'     => 'barang_master.kode_barang',
            'barang_name'     => 'barang_master.barang_name',
            'spesifikasi'     => 'barang_master_spesifikasi.spesifikasi',
            'satuan_id'       => 'penerimaan_barang_detail.unit',
            'keterangan'      => 'penerimaan_barang_detail.keterangan',
            'qty_order'       => 'qty_order',
            'qty_diterima'    => 'qty_diterima',
            'total_harga'     => 'penerimaan_barang_detail.sub_total',
            'harga_satuan'    => 'penerimaan_barang_detail.harga'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'penerimaan_barang.tanggal';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            penerimaan_barang_detail.id,
            penerimaan_barang.tanggal AS tanggal_lpb,
            penerimaan_barang.no_penerimaan_barang AS no_lpb,
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
            penerimaan_barang_detail.qty AS qty_order,
            penerimaan_barang_detail.jml_masuk AS qty_diterima,
            am_purchase_orders.po_date,
            am_purchase_orders.po_no,
            penerimaan_barang_detail.sub_total AS total_harga,
            penerimaan_barang_detail.harga AS harga_satuan,
            tb_valas.value AS valas_name,
            parent_barang.parent_name AS kategori_barang
        ";
        $builder = $this->db->table('penerimaan_barang_detail')
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = am_purchase_orders.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_detail.id')
            ->orderBy($sort, $sortType);
        $totalData = $builder->countAllResults(false);
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
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

        $sub = $this->db->table('penerimaan_barang_detail')
            ->select('SUM(penerimaan_barang_detail.sub_total) AS total_harga')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = am_purchase_orders.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->where($condition);
        // Filter
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $sub->where('penerimaan_barang.bc_type', 0);
            } else {
                $sub->where('penerimaan_barang.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $sub->where('penerimaan_barang.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $sub->where('penerimaan_barang.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $sub->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $sub->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $sub->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $sub->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $sub->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
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

    public function getListLaporanPenerimaanBarangBahanBakuImport($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'          => 'divisis.divisi',
            'supplier_id'     => 'penerimaan_barang.supplier_id',
            'bc_type'         => 'penerimaan_barang.bc_type',
            'tanggal_dokumen' => 'bc_purchase_order.createdAt',
            'no_daftar'       => 'bc_purchase_order.no_daftar',
            'no_aju'          => 'bc_23.no_aju',
            'tanggal_lpb'     => 'penerimaan_barang.tanggal',
            'no_lpb'          => 'penerimaan_barang.no_penerimaan_barang',
            'po_date'         => 'rm_import_pos.po_date',
            'po_no'           => 'rm_import_pos.po_no',
            'kode_barang'     => 'barang_master.kode_barang',
            'barang_name'     => 'barang_master.barang_name',
            'spesifikasi'     => 'barang_master_spesifikasi.spesifikasi',
            'satuan_id'       => 'penerimaan_barang_detail.unit',
            'keterangan'      => 'penerimaan_barang_detail.keterangan',
            'qty_order'       => 'qty_order',
            'qty_diterima'    => 'qty_diterima',
            'total_harga'     => 'penerimaan_barang_detail.total',
            'harga_satuan'    => 'penerimaan_barang_detail.harga'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'penerimaan_barang.tanggal';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
            penerimaan_barang_detail.id,
            penerimaan_barang.tanggal AS tanggal_lpb,
            penerimaan_barang.no_penerimaan_barang AS no_lpb,
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
            penerimaan_barang_detail.qty AS qty_order,
            penerimaan_barang_detail.jml_masuk AS qty_diterima,
            rm_import_pos.po_date,
            rm_import_pos.po_no,
            penerimaan_barang_detail.sub_total AS total_harga,
            penerimaan_barang_detail.harga AS harga_satuan,
            tb_valas.value AS valas_name,
            parent_barang.parent_name AS kategori_barang
        ";
        $builder = $this->db->table('penerimaan_barang_detail')
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = rm_import_pos.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->join('parent_barang', 'parent_barang.id = barang_master.parent_type_id', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_detail.id')
            ->orderBy($sort, $sortType);
        $totalData = $builder->countAllResults(false);
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('rm_import_pos.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
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

        $sub = $this->db->table('penerimaan_barang_detail')
            ->select('SUM(penerimaan_barang_detail.sub_total) AS total_harga')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id', 'left')
            ->join('rm_import_pos', 'rm_import_pos.id = penerimaan_barang_detail.purchase_order_id', 'left')
            ->join('rm_import_po_details', 'rm_import_po_details.id = penerimaan_barang_detail.purchase_order_details_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('metadata tb_bc', 'tb_bc.id = penerimaan_barang.bc_type', 'left')
            ->join('metadata tb_valas', 'tb_valas.id = rm_import_pos.currency', 'left')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left')
            ->where($condition)
            ->groupBy('penerimaan_barang_detail.id');

        // Filter
        // filter tambahan
        if (!empty($addCondition['bc_type'])) {
            if ($addCondition['bc_type'] == "NON PABEAN") {
                $builder->where('penerimaan_barang.bc_type', 0);
            } else {
                $builder->where('penerimaan_barang.bc_type', $addCondition['bc_type']);
            }
        }
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] === "SUDAH POSTING") {
                $builder->where('penerimaan_barang.status_post', "FINISH");
            } elseif ($addCondition['status_posting'] === "BELUM POSTING") {
                $builder->where('penerimaan_barang.status_post', "WAITING");
            }
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('penerimaan_barang.tanggal >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('penerimaan_barang.tanggal <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('rm_import_pos.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
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
