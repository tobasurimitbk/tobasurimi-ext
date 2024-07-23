<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class AMPurchaseOrderModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'am_purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'purchase_request_id',
        'division_id',
        'bc_type',
        'po_no',
        'po_date',
        'po_type',
        'company_id',
        'currency',
        'supplier_id',
        'total',
        'payment_term',
        'payment_date',
        'dpp',
        'note',
        'is_posted',
        'createdBy',
        'status_penerimaan',
        'status_closed_spp',

        // import field
        'shipper',
        'consigne',
        'port_origin',
        'port_destination',
        'location_transaction',
        'shipment',
        'latest_shipment_date',
        'attn',
        'potongan_harga',
        'direktur'
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

    public function getPOList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'           => 'am_purchase_orders.po_date',
            'poNo'             => 'am_purchase_orders.po_no',
            'divisi'            => 'divisis.divisi',
            'supplierName'      => 'suppliers.name',
            'total'             => 'am_purchase_orders.total',
            'currencyName'      => 'metadata.value',
            'createdAt'         => 'am_purchase_orders.createdAt',
            'updatedAt'         => 'am_purchase_orders.updatedAt',
            'statusPenerimaan'  => 'am_purchase_orders.status_penerimaan'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "am_purchase_orders.*, 
                      suppliers.name AS supplierName, 
                      metadata.value AS currencyName,
                      companies.company AS companyName,
                      divisis.divisi,
                      COUNT(am_purchase_order_details.id) AS itemCount";
        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('companies', 'companies.id = am_purchase_orders.company_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->groupBy(('am_purchase_orders.id'))
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $poDataQry->like('am_purchase_orders.po_no', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $poDataQry->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $poDataQry->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupEnd();
        }

        $totalFilteredData = $poDataQry->countAllResults(false);
        $data = $poDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }


    // ALL PO BAHAN PENOLONG
    public function getPOLokalBPList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'           => 'am_purchase_orders.po_date',
            'divisiName'      => 'divisis.divisi',
            'poNo'             => 'am_purchase_orders.po_no',
            'sppNo'             => 'purchase_requests.spp_no',
            'supplierName'      => 'suppliers.name',
            'divisis'             => 'am_purchase_orders.total',
            'createdAt'         => 'am_purchase_orders.createdAt',
            'updatedAt'         => 'am_purchase_orders.updatedAt',
            'statusPenerimaan'  => 'am_purchase_orders.status_penerimaan'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "am_purchase_orders.*, 
                      purchase_requests.spp_no,
                      suppliers.name AS supplierName, 
                      companies.company AS companyName,
                      divisis.divisi,
                      COUNT(am_purchase_order_details.id) AS itemCount";

        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->join('companies', 'companies.id = am_purchase_orders.company_id', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->groupBy(('am_purchase_orders.id'))
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $poDataQry->like('am_purchase_orders.po_no', $addCondition['search'])->orLike('purchase_requests.spp_no', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $poDataQry->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $poDataQry->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupEnd();
        }

        $totalFilteredData = $poDataQry->countAllResults(false);
        $data = $poDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }


    public function getPOById($id)
    {
        $selectQry = "am_purchase_orders.*,
        divisis.id AS divisi_id,
        divisis.divisi AS divisiName,
        suppliers.name AS supplierName,
        suppliers.address AS supplierAddress,
        suppliers.phone AS supplierPhone,
        suppliers.no_npwp AS supplierNPWP,
        suppliers.fax AS supplierFax,
        users.name AS createdByName,
        companies.company as companyName,
        metadata.value as currencyName,
        purchase_requests.spp_no
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('users', 'users.id = am_purchase_orders.createdBy', 'left')
            ->join('companies', 'companies.id = am_purchase_orders.company_id', 'left')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->find($id);

        return $sppData;
    }

    public function getByPurchaseRequestId($id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'purchase_request_id' => $id,
        ];

        $builder = $this->db->table('am_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getNoPenerimaanBarang($po_type, $supplier_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'po_type' => $po_type
        ];

        $builder = $this->db->table('am_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getNoPenerimaanBarangBySPP($po_type, $supplier_id, $spp_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'po_type' => $po_type,
            'purchase_request_id' => $spp_id
        ];

        $builder = $this->db->table('am_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getNoPOBeaCukai($po_type, $company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'po_type' => $po_type
        ];

        $builder = $this->db->table('am_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function get_no($tgl, $bln, $thn, $divisi, $thn2, $divisi_id, $last_day)
    {
        $lastStr =  $tgl . $bln . $thn;

        $builder = $this->db->table('am_purchase_orders');
        $builder->select('po_no')
            ->join('purchase_requests', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left');
        $builder->orderBy('po_no', 'desc')
            ->like('am_purchase_orders.po_no', "TOBA")
            ->where('am_purchase_orders.division_id', $divisi_id)
            ->orWhere('purchase_requests.divisi_id', $divisi_id)
            ->where('am_purchase_orders.createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('am_purchase_orders.createdAt <=', $last_day . " 23:59:59");
        $builder->like('po_no', $lastStr);
        $query = $builder->get();

        $lastPO = '01';
        if ($query->getResultArray()) {
            $lastFirst = explode('/', $query->getResultArray()[0]['po_no']);
            $lastPO = explode('-', $lastFirst[0]);
            $lastPO = intval($lastPO[1]) + 1;
            $lastPO = sprintf("%02d", $lastPO);
        };

        $generatedNo =  $lastStr . '-' . $lastPO . '/' . $divisi . '/TOBA/' . $thn2;

        return $generatedNo;
    }

    public function get_new_no_po($bln, $thn, $last_day, $companyID)
    {
        $head = "PO/LBP-" . $bln . $thn . '/';
        $lastPO = $this->select('po_no')
            ->like('po_no', "PO/LBP-")
            ->where('am_purchase_orders.createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('am_purchase_orders.createdAt <=', $last_day . " 23:59:59")
            ->where('am_purchase_orders.company_id', $companyID)
            ->orderBy('po_no', "DESC")
            ->first();

        $counterFirst = '000001';
        if ($lastPO == null) {
            return $head . '' . $counterFirst;
        } else {
            try {
                $last = explode('/', $lastPO['po_no']);
                $poLastDigit = $last[2];
                $counterFirst = str_pad((int) $poLastDigit + 1, strlen($counterFirst), '0', STR_PAD_LEFT);
                return $head . '' . $counterFirst;
            } catch (Exception $e) {
                return 'ERROR GENERATE NUMBER ' . date('Y-m-d');
            }
        }
    }

    public function get_new_no_po_import($bln, $thn, $last_day, $companyID)
    {
        $head = "PO/IBP-" . $bln . $thn . '/';
        $lastPO = $this->select('po_no')
            ->like('po_no', "PO/IBP-")
            ->where('am_purchase_orders.createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('am_purchase_orders.createdAt <=', $last_day . " 23:59:59")
            ->where('am_purchase_orders.company_id', $companyID)
            ->orderBy('po_no', "DESC")
            ->first();

        $counterFirst = '000001';
        if ($lastPO == null) {
            return $head . '' . $counterFirst;
        } else {
            try {
                $last = explode('/', $lastPO['po_no']);
                $poLastDigit = $last[2];
                $counterFirst = str_pad((int) $poLastDigit + 1, strlen($counterFirst), '0', STR_PAD_LEFT);
                return $head . '' . $counterFirst;
            } catch (Exception $e) {
                return 'ERROR GENERATE NUMBER ' . date('Y-m-d');
            }
        }
    }


    public function historiHargaPOBahanPenolong($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'am_purchase_orders.po_no' => 'am_purchase_orders.po_no',
            'am_purchase_orders.po_date' => 'am_purchase_orders.po_date',
            'suppliers.name'  => 'suppliers.name',
            'barang_master_spesifikasi.spesifikasi' => 'barang_master_spesifikasi.spesifikasi',
            'am_purchase_order_details.price' => 'am_purchase_order_details.price',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang, 
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            suppliers.name as nama_supplier,
            am_purchase_order_details.price
        ";

        $poDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $poDataQry->like('am_purchase_orders.po_no', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $poDataQry->groupEnd();
        }

        $totalFilteredData = $poDataQry->countAllResults(false);
        $data = $poDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function historiHargaPOBahanPenolongFirst($barangID, $spesifikasiBarangID, $poType, $companyID)
    {
        $condition = [
            "am_purchase_orders.company_id"  => $companyID,
            "am_purchase_order_details.barang_id" => $barangID,
            "am_purchase_order_details.spesifikasi_id" => $spesifikasiBarangID,
            "am_purchase_orders.deletedAt" => NULL,
            "am_purchase_order_details.deletedAt" => NULL,
            "am_purchase_orders.po_type" => $poType
        ];

        $selectQry = "
            barang_master.barang_name as nama_barang, 
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            suppliers.name as nama_supplier,
            am_purchase_order_details.price
        ";

        $res = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->first();

        if ($res == null) {
            return [
                'hargaTerakhirNumber' => 0,
                'hargaTerakhir' => '-',
                'supplierTerakhir' => '-'
            ];
        } else {
            return [
                'hargaTerakhirNumber' => $res['price'],
                'hargaTerakhir' => number_format($res['price'], 2, ',', '.'),
                'supplierTerakhir' => $res['nama_supplier']
            ];
        }
    }

    public function getPOByNoPO($noPO, $companyID, $barang1ID, $barang2ID)
    {
        $condition = [
            "am_purchase_orders.company_id"  => $companyID,
            "am_purchase_orders.po_no"  => $noPO,
            "am_purchase_orders.deletedAt" => NULL,
            "am_purchase_order_details.deletedAt" => NULL,
            "am_purchase_order_details.barang_id" => $barang1ID,
            "am_purchase_order_details.spesifikasi_id" => $barang2ID,
        ];

        $selectQry = "
            barang_master.barang_name as nama_barang, 
            am_purchase_orders.*,
            suppliers.name as nama_supplier,
            am_purchase_order_details.*
        ";

        $res = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->first();

        if ($res == null) {
            return [
                'hargaTerakhirNumber' => 0,
                'hargaTerakhir' => '-',
                'supplierTerakhir' => '-'
            ];
        } else {
            return [
                'hargaTerakhirNumber' => $res['price'],
                'hargaTerakhir' => number_format($res['price'], 2, ',', '.'),
                'supplierTerakhir' => $res['nama_supplier'],
                'dataPO' => $res
            ];
        }
    }

    public function getSPP($multiplePoId)
    {
        $result = $this->select('purchase_requests.*')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->where('am_purchase_orders.id', $multiplePoId[0])
            ->findAll();

        return $result;
    }
}
