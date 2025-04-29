<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class RMImportPOModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_import_pos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'purchase_request_id',
        'company_id',
        'supplier_id',
        'division_id',
        'po_no',
        'po_date',
        'payment_date',
        'currency',
        'total',
        'payment_term',
        'note',
        'shipper',
        'consigne',
        'port_origin',
        'potongan_harga',
        'direktur',
        'port_destination',
        'location_transaction',
        'shipment',
        'latest_shipment_date',
        'attn',
        'createdBy',
        'status_penerimaan',
        'is_posted',
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
            'poDate'           => 'rm_import_pos.po_date',
            'poNo'             => 'rm_import_pos.po_no',
            'supplierName'      => 'suppliers.name',
            'total'             => 'rm_import_pos.total',
            'currencyName'      => 'metadata.value',
            'createdAt'         => 'rm_import_pos.createdAt',
            'updatedAt'         => 'rm_import_pos.updatedAt',
            'statusPenerimaan'  => 'rm_import_pos.status_penerimaan',
            'divisi'       => 'divisis.divisi'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'rm_import_pos.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rm_import_pos.*, 
                      divisis.divisi,
                      suppliers.name AS supplierName, 
                      metadata.value AS currencyName,
                      companies.company AS companyName,
                      COUNT(rm_import_po_details.id) AS itemCount";
        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'divisis.id = rm_import_pos.division_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
            ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
            ->join('companies', 'companies.id = rm_import_pos.company_id', 'left')
            ->join('rm_import_po_details', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
            ->groupBy(('rm_import_pos.id'))
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $poDataQry->like('rm_import_pos.po_no', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $poDataQry->where('rm_import_pos.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $poDataQry->where('rm_import_pos.po_date <=', $addCondition['dateEnd']);
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
        $selectQry = "rm_import_pos.*,
        divisis.divisi AS divisiName,
        suppliers.name AS supplierName,
        suppliers.address AS supplierAddress,
        suppliers.phone AS supplierPhone,
        suppliers.fax AS supplierFax,
        suppliers.no_npwp AS supplierNPWP,
        users.name AS createdByName,
        companies.company as companyName,
        metadata.value as currencyName,
        purchase_requests.spp_no
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = rm_import_pos.division_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
            ->join('users', 'users.id = rm_import_pos.createdBy', 'left')
            ->join('companies', 'companies.id = rm_import_pos.company_id', 'left')
            ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
            ->join('purchase_requests', 'purchase_requests.id = rm_import_pos.purchase_request_id', 'left')
            ->find($id);

        return $sppData;
    }

    public function getNoPenerimaanBarang($supplier_id, $company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('rm_import_pos');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getNoPOBeaCukai($company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'is_posted' => 1,
            'status_penerimaan' => 1,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('rm_import_pos');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function get_no($tgl, $bln, $thn, $divisi, $thn2, $divisi_id, $last_day)
    {
        $lastStr =  $tgl . $bln . $thn;

        $builder = $this->db->table('rm_import_pos');
        $builder->select('po_no');
        $builder->orderBy('po_no', 'desc')
            ->where('division_id', $divisi_id)
            ->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
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
        $head = "PO/IBB-" . $bln . $thn . '/';
        $lastPO = $this->select('po_no')
            ->like('po_no', "PO/IBB-")
            ->where('rm_import_pos.po_date >=', $thn . "-" . $bln . "-01")
            ->where('rm_import_pos.po_date <=', $last_day)
            ->where('rm_import_pos.company_id', $companyID)
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

    public function getPOByNoPO($noPO, $companyID, $barang1ID, $barang2ID)
    {
        $condition = [
            "rm_import_pos.company_id"  => $companyID,
            "rm_import_pos.po_no"  => $noPO,
            "rm_import_pos.deletedAt" => NULL,
            "rm_import_po_details.deletedAt" => NULL,
            "rm_import_po_details.barang_id" => $barang1ID,
            "rm_import_po_details.spesifikasi_id" => $barang2ID,
        ];

        $selectQry = "
            barang_master.barang_name as nama_barang, 
            rm_import_pos.*,
            suppliers.name as nama_supplier,
            rm_import_po_details.*,
            SUM(rm_import_po_details.price) AS price,
            SUM(rm_import_po_details.total) AS total,
        ";

        $res = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('rm_import_po_details', 'rm_import_po_details.rm_purchase_order_id = rm_import_pos.id')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id')
            ->first();

        if ($res == null) {
            return [
                'hargaTerakhirNumber' => 0,
                'hargaTerakhir' => '-',
                'supplierTerakhir' => '-'
            ];
        } else {
            $totalPrice = $res['total'];
            return [
                'hargaTerakhirNumber' => $totalPrice,
                'hargaTerakhir' => number_format($totalPrice, 2, ',', '.'),
                'supplierTerakhir' => $res['nama_supplier'],
                'dataPO' => $res
            ];
        }
    }

    public function getPOBBCondition($divisi_id, $po_date_awal, $po_date_akhir, $kategori_id, $company_id)
    {
        $selectQry = "
            barang_master.barang_name AS barangName, 
            CONCAT(barang_master_spesifikasi.spesifikasi, ' (IMPORT)') AS spekName, 
            rm_import_pos.potongan_harga AS subsidi,
            rm_import_po_details.price AS price1,
            rm_import_po_details.total AS totalPrice,
            rm_import_po_details.disc AS disc,
            rm_import_po_details.additional_cost AS additional_cost,
            SUM(rm_import_po_details.qty) AS qtyPO,
            rm_import_po_details.barang_id AS barang1_id,
            rm_import_po_details.spesifikasi_id AS barang2_id,
            satuans.kode_satuan AS satuanName, 
            GROUP_CONCAT(rm_import_pos.po_no) AS po_no,
            (SUM(rm_import_po_details.price)) AS avg_price_per_qty
        ";

        $poBBImportData = $this->asObject()
            ->select($selectQry)
            ->join('rm_import_po_details', 'rm_import_po_details.rm_import_po_id = rm_import_pos.id', 'left')
            ->join('barang_master', 'rm_import_po_details.barang_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'rm_import_po_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->join('account_barang', 'rm_import_po_details.barang_id = account_barang.barang_master_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('rm_import_pos.division_id', $divisi_id)
            ->where('rm_import_pos.is_posted', '1')
            ->where('rm_import_pos.po_date >=', date('Y-m-d', strtotime($po_date_awal)))
            ->where('rm_import_pos.po_date <=', date('Y-m-d', strtotime($po_date_akhir)))
            // ->where('account_barang.kategori_id', $kategori_id)
            ->where('account_barang.divisi_id', $divisi_id)
            ->where('rm_import_pos.company_id', $company_id)
            ->where('rm_import_po_details.deletedAt', null)
            ->groupBy('rm_import_po_details.barang_id, rm_import_po_details.spesifikasi_id, rm_import_pos.company_id')
            ->findAll();

        return $poBBImportData;
    }

    public function getPOByIdSupplierWithInvoice($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'            => 'rm_import_pos.po_date',
            'poNo'              => 'rm_import_pos.po_no',
            'divisi'            => 'divisis.divisi',
            'supplierName'      => 'suppliers.name',
            'total'             => 'rm_import_pos.total',
            'currencyName'      => 'metadata.value',
            'createdAt'         => 'rm_import_pos.createdAt',
            'updatedAt'         => 'rm_import_pos.updatedAt',
            'statusPenerimaan'  => 'rm_import_pos.status_penerimaan'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'rm_import_pos.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rm_import_pos.id AS id, 
                    rm_import_pos.po_date AS tanggal_invoice, 
                    rm_import_pos.po_no AS no_invoice, 
                    suppliers.id AS supplier_id, 
                    suppliers.name AS supplier_name,
                    divisis.divisi AS divisi,
                    COUNT(rm_import_po_details.id) AS itemCount,
                    rm_import_pos.total AS total, 
                    local_po_payments.amount AS remaining";

        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id')
            ->join('divisis', 'divisis.id = rm_import_pos.division_id', 'left')
            ->join('rm_import_po_details', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
            ->join('local_po_payments', 'FIND_IN_SET(rm_import_pos.id, REPLACE(REPLACE(local_po_payments.multiple_po_id, "[", ""), "]", ""))', 'left') // Menyesuaikan jika multiple_po_id berbentuk JSON atau array sebagai string
            ->groupBy('rm_import_pos.id')
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if (!empty($addCondition['search']) || !empty($addCondition['dateStart']) || !empty($addCondition['dateEnd']) || !empty($addCondition['filter']) || !empty($addCondition['divisi'])) {
            $poDataQry->groupStart();
        }

        if (!empty($addCondition['search'])) {
            $poDataQry->like('rm_import_pos.po_no', $addCondition['search']);
        }

        if (!empty($addCondition['dateStart'])) {
            $poDataQry->where('rm_import_pos.po_date >=', $addCondition['dateStart']);
        }

        if (!empty($addCondition['dateEnd'])) {
            $poDataQry->where('rm_import_pos.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['filter'] && $addCondition['divisi']) {
            $poDataQry->groupStart()
                ->where('rm_purchase_orders.divisi_id', $addCondition['divisi'])
                ->groupEnd()
                ->whereIn('suppliers.id', $addCondition['filter']);
        } elseif ($addCondition['divisi']) {
            $poDataQry->groupStart()
                ->where('rm_purchase_orders.divisi_id', $addCondition['divisi'])
                ->groupEnd();
        } elseif ($addCondition['filter']) {
            $poDataQry->whereIn('suppliers.id', $addCondition['filter']);
        }

        if (!empty($addCondition['search']) || !empty($addCondition['dateStart']) || !empty($addCondition['dateEnd']) || !empty($addCondition['filter']) || !empty($addCondition['divisi'])) {
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
}
