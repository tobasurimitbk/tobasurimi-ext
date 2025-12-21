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
        'form_id',
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
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->groupBy(('am_purchase_orders.id'))
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['dateStart']) {
            $poDataQry->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $poDataQry->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search']) {
            $poDataQry->groupStart();
            $poDataQry->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search']);
            $poDataQry->groupEnd();
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
            'divisiName'       => 'divisis.divisi',
            'poNo'             => 'am_purchase_orders.po_no',
            'sppNo'            => 'purchase_requests.spp_no',
            'supplierName'     => 'suppliers.name',
            'divisis'          => 'am_purchase_orders.total',
            'createdAt'        => 'am_purchase_orders.createdAt',
            'updatedAt'        => 'am_purchase_orders.updatedAt',
            'statusPenerimaan' => 'am_purchase_orders.status_penerimaan',
            'note'             => 'am_purchase_orders.note'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        // --- Base SELECT ---
        $selectFields = "
        am_purchase_orders.*,
        suppliers.name AS supplierName,
        companies.company AS companyName,
        divisis.divisi,
        purchase_requests.spp_no,
        (SELECT COUNT(*) 
            FROM am_purchase_order_details od
            WHERE od.am_purchase_order_id = am_purchase_orders.id
            AND od.deletedAt IS NULL
        ) AS itemCount
    ";

        // --- Query utama ---
        $poDataQry = $this->asObject()
            ->select($selectFields)
            ->where($condition)
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->join('companies', 'companies.id = am_purchase_orders.company_id', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->orderBy($sort, $sortType);

        // --- Filter tambahan ---
        if (isset($addCondition['is_posted'])) {
            $poDataQry->where('am_purchase_orders.is_posted', $addCondition['is_posted'] === "SUDAH POSTING" ? 1 : 0);
        }

        if (!empty($addCondition['search'])) {
            $poDataQry->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('purchase_requests.spp_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('am_purchase_orders.note', $addCondition['search'])
                ->groupEnd();
        }

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $poDataQry->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $poDataQry->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $poDataQry->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
            }
            $poDataQry->groupEnd();
        }

        // --- Total Data tanpa filter ---
        $totalData = $this->db->table('am_purchase_orders')
            ->select("(SELECT COUNT(*) FROM am_purchase_order_details od WHERE od.am_purchase_order_id = am_purchase_orders.id AND od.deletedAt IS NULL) AS itemCount")
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->where($condition)
            ->countAllResults();

        // --- Total Data dengan filter ---
        $filterDataQry = $this->db->table('am_purchase_orders')
            ->select($selectFields)
            ->where($condition)
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = am_purchase_orders.company_id', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left');

        if (isset($addCondition['is_posted'])) {
            $filterDataQry->where('am_purchase_orders.is_posted', $addCondition['is_posted'] === "SUDAH POSTING" ? 1 : 0);
        }
        if (!empty($addCondition['search'])) {
            $filterDataQry->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('purchase_requests.spp_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('am_purchase_orders.note', $addCondition['search'])
                ->groupEnd();
        }
        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $filterDataQry->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $filterDataQry->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $filterDataQry->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
            }
            $filterDataQry->groupEnd();
        }

        $totalFilteredData = $filterDataQry->countAllResults();
        $data = $poDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType,
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

    public function getSupplierBySpp($sppId) {}

    public function getNoPenerimaanBarangBySPP($po_type, $supplier_id, $multiple_spp_id)
    {
        $arrCondition = [
            'am_purchase_orders.deletedAt' => null,
            'supplier_id' => $supplier_id,
            'am_purchase_orders.is_posted' => 1,
            'status_penerimaan' => 0,
            'po_type' => $po_type,
            // 'purchase_request_id' => $spp_id
        ];

        $builder = $this->db->table('am_purchase_orders');
        $builder->select('am_purchase_orders.*,purchase_requests.spp_no');
        $builder->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left');
        $builder->whereIn('purchase_request_id', $multiple_spp_id);
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

    public function get_new_no_po($tanggal, $companyId, $last_day)
    {
        // Pastikan tanggal valid
        if (empty($tanggal) || !preg_match('/\d{4}-\d{2}-\d{2}/', $tanggal)) {
            throw new \Exception("Format tanggal tidak valid. Gunakan yyyy-mm-dd");
        }

        $tanggalExplode = explode('-', $tanggal);
        $thn = $tanggalExplode[0];
        $bln = $tanggalExplode[1];

        // Counter default (6 digit)
        $counterLength = 6;
        $counterFirst = str_repeat('0', $counterLength - 1) . '1';

        // ======================
        // === FORMAT OCS ===
        // ======================
        if ((int)$companyId === 16) {
            $counterLength = 4;
            $counterFirst = str_repeat('0', $counterLength - 1) . '1';

            $thnShort = substr($thn, -2);
            $romanMonth = romanMonthNumber((int)$bln);
            $head = "/P/{$romanMonth}/{$thnShort}";

            // Cari PO terakhir dengan format OCS
            $lastPO = $this->select('po_no')
                ->like('po_no', $head, 'before') // cari dari awal string
                ->where('am_purchase_orders.po_date >=', "{$thn}-{$bln}-01")
                ->where('am_purchase_orders.po_date <=', $last_day)
                ->where('am_purchase_orders.company_id', $companyId)
                ->orderBy('po_no', 'DESC')
                ->first();

            if ($lastPO) {
                try {
                    // Format contoh: 0004/P/IX/25
                    $parts = explode('/', $lastPO['po_no']);
                    $poLastDigit = (int) preg_replace('/\D/', '', $parts[0]); // ambil angka di depan
                    $newCounter = str_pad($poLastDigit + 1, $counterLength, '0', STR_PAD_LEFT);
                } catch (\Throwable $e) {
                    $newCounter = $counterFirst;
                }
            } else {
                $newCounter = $counterFirst;
            }

            return "{$newCounter}{$head}";
        }

        // ======================
        // === FORMAT NON-OCS ===
        // ======================
        $head = "PO/LBP-{$bln}{$thn}/";

        $lastPO = $this->select('po_no')
            ->like('po_no', $head, 'after') // pastikan format prefix sesuai
            ->where('am_purchase_orders.po_date >=', "{$thn}-{$bln}-01")
            ->where('am_purchase_orders.po_date <=', $last_day)
            ->where('am_purchase_orders.company_id', $companyId)
            ->orderBy('po_no', 'DESC')
            ->first();

        if ($lastPO) {
            try {
                // Format contoh: PO/LBP-102025/000023
                $parts = explode('/', $lastPO['po_no']);
                $poLastDigit = isset($parts[2]) ? (int)$parts[2] : 0;
                $newCounter = str_pad($poLastDigit + 1, $counterLength, '0', STR_PAD_LEFT);
            } catch (\Throwable $e) {
                $newCounter = $counterFirst;
            }
        } else {
            $newCounter = $counterFirst;
        }

        return "{$head}{$newCounter}";
    }


    public function get_new_no_po_import($bln, $thn, $last_day, $companyID)
    {
        $head = "PO/IBP-" . $bln . $thn . '/';
        $lastPO = $this->select('po_no')
            ->like('po_no', "PO/IBP-")
            ->where('am_purchase_orders.po_date >=', $thn . "-" . $bln . "-01")
            ->where('am_purchase_orders.po_date <=', $last_day)
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
            'am_purchase_orders.division_id' => 'am_purchase_orders.division_id',
            'am_purchase_orders.note' => 'am_purchase_orders.note',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang, 
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            am_purchase_order_details.note,
            suppliers.name as nama_supplier,
            am_purchase_order_details.id,
            am_purchase_order_details.price,
            am_purchase_order_details.qty,
            am_purchase_order_details.spesifikasi_id,
            divisis.divisi,
            satuans.kode_satuan,
            purchase_requests.spp_no,
        ";

        $poDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['start_date'] && $addCondition['end_date']) {
            $startDate = date('Y-m-d', strtotime($addCondition['start_date']));
            $endDate = date('Y-m-d', strtotime($addCondition['end_date']));

            $poDataQry->where('am_purchase_orders.po_date >=', $startDate)
                ->where('am_purchase_orders.po_date <=', $endDate);
        }

        if ($addCondition['search']) {
            $poDataQry->like('purchase_requests.spp_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('am_purchase_orders.note', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('am_purchase_order_details.qty', $addCondition['search'])
                ->orLike('satuans.kode_satuan', $addCondition['search'])
                ->orLike('am_purchase_order_details.price', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $poDataQry->groupEnd();
        }

        $totalFilteredData = $poDataQry->countAllResults(false);
        if ($limit == null && $offset == null) {
            $data = $poDataQry->findAll();
        } else {
            $data = $poDataQry->findAll($limit, $offset);
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function historiHargaPOBahanPenolongByLpb($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'am_purchase_orders.po_no' => 'am_purchase_orders.po_no',
            'am_purchase_orders.po_date' => 'am_purchase_orders.po_date',
            'suppliers.name'  => 'suppliers.name',
            'barang_master_spesifikasi.spesifikasi' => 'barang_master_spesifikasi.spesifikasi',
            'am_purchase_order_details.price' => 'am_purchase_order_details.price',
            'am_purchase_orders.division_id' => 'am_purchase_orders.division_id',
            'am_purchase_orders.note' => 'am_purchase_orders.note',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'am_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang, 
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            am_purchase_order_details.note,
            suppliers.name as nama_supplier,
            SUM(penerimaan_barang_detail.harga) as total_harga, 
            SUM(penerimaan_barang_detail.qty) as total_qty,
            SUM(penerimaan_barang_detail.sub_total) as total_sub_total, 
            divisis.divisi,
            satuans.kode_satuan,
            purchase_requests.spp_no,
            penerimaan_barang.no_penerimaan_barang
        ";

        $poDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id AND penerimaan_barang_detail.purchase_order_details_id = am_purchase_order_details.id')
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
            ->groupBy('penerimaan_barang.id, nama_barang, po_no, po_date, note, nama_supplier, divisi, kode_satuan, spp_no, no_penerimaan_barang')
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['start_date'] && $addCondition['end_date']) {
            $startDate = date('Y-m-d', strtotime($addCondition['start_date']));
            $endDate = date('Y-m-d', strtotime($addCondition['end_date']));

            $poDataQry->where('am_purchase_orders.po_date >=', $startDate)
                ->where('am_purchase_orders.po_date <=', $endDate);
        }

        if ($addCondition['search']) {
            $poDataQry->like('purchase_requests.spp_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search'])
                ->orLike('am_purchase_orders.note', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('am_purchase_order_details.qty', $addCondition['search'])
                ->orLike('satuans.kode_satuan', $addCondition['search'])
                ->orLike('am_purchase_order_details.price', $addCondition['search']);
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

    public function historiHargaPOBahanPenolongFirst(
        $barangID,
        $spesifikasiBarangID,
        $poType,
        $companyID,
        $unitId = null
    ) {
        $condition = [
            "am_purchase_orders.company_id"  => $companyID,
            "am_purchase_order_details.barang_id" => $barangID,
            "am_purchase_order_details.spesifikasi_id" => $spesifikasiBarangID,
            "am_purchase_orders.deletedAt" => NULL,
            "am_purchase_order_details.deletedAt" => NULL,
            "am_purchase_orders.po_type" => $poType
        ];

        $selectQry = "
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) as nama_barang, 
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            am_purchase_orders.createdAt,
            suppliers.name as nama_supplier,
            am_purchase_order_details.price
        ";

        $res = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('am_purchase_order_details', 'am_purchase_order_details.am_purchase_order_id = am_purchase_orders.id')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id');

        if ($unitId != null) {
            $res = $res->where('am_purchase_order_details.unit', $unitId);
        }

        $res = $res->orderBy('am_purchase_orders.createdAt', "DESC")->first();

        if ($res == null) {
            return [
                'createdAt' => null,
                'hargaTerakhirNumber' => 0,
                'hargaTerakhir' => '-',
                'supplierTerakhir' => '-'
            ];
        } else {
            return [
                'createdAt' => $res['createdAt'],
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
                'supplierTerakhir' => '-',
                'dataPO' => null
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
        $result = $this->distinct()
            ->select('purchase_requests.*')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->whereIn('am_purchase_orders.id', $multiplePoId)
            ->findAll();

        return $result;
    }

    public function getPOByIdSupplierWithInvoice($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'            => 'am_purchase_orders.po_date',
            'poNo'              => 'am_purchase_orders.po_no',
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

        $selectQry = "am_purchase_orders.id AS id, 
                    am_purchase_orders.po_date AS tanggal_invoice, 
                    am_purchase_orders.po_no AS no_invoice, 
                    am_purchase_orders.company_id, 
                    suppliers.id AS supplier_id, 
                    suppliers.name AS supplier_name,
                    divisis.divisi AS divisi,
                    COUNT(am_purchase_order_details.id) AS itemCount,
                    penerimaan_barang_detail.sub_total AS total, 
                    local_po_payments.amount AS remaining,
                    SUM(penerimaan_barang_detail.sub_total) AS sum_total, 
                    SUM(local_po_payments.amount) AS sum_remaining,
                    penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang,
                    GROUP_CONCAT(penerimaan_barang.no_penerimaan_barang SEPARATOR ', ') AS list_no_penerimaan_barang";

        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->where('am_purchase_orders.is_posted', 1)
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_id = am_purchase_orders.id AND penerimaan_barang_detail.purchase_order_details_id = am_purchase_order_details.id', 'right')
            ->join('penerimaan_barang', "penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id AND penerimaan_barang.tipe_bahan = 'PENOLONG'", 'right')
            ->join('local_po_payments', 'FIND_IN_SET(am_purchase_orders.id, REPLACE(REPLACE(local_po_payments.multiple_po_id, "[", ""), "]", ""))', 'left');
            if (isset($addCondition['summary']) && $addCondition['summary'] == "summary") {
                $poDataQry->groupBy('am_purchase_orders.id, am_purchase_orders.supplier_id');
            } else {
                $poDataQry->groupBy('am_purchase_orders.id');
            }
            $poDataQry->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if (!empty($addCondition['search']) || !empty($addCondition['dateStart']) || !empty($addCondition['dateEnd']) || !empty($addCondition['filter']) || !empty($addCondition['divisi']) || !empty($addCondition['companyId'])) {
            $poDataQry->groupStart();
        }

        if (!empty($addCondition['companyId']) && $addCondition['companyId'] != []) {
            $poDataQry->whereIn('am_purchase_orders.company_id', $addCondition['companyId']);
        }

        if (!empty($addCondition['search'])) {
            $poDataQry->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search']);
        }

        if (!empty($addCondition['dateStart'])) {
            $poDataQry->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if (!empty($addCondition['dateEnd'])) {
            $poDataQry->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['filter'] && $addCondition['divisi']) {
            $poDataQry->groupStart()
                ->where('am_purchase_orders.division_id', $addCondition['divisi'])
                ->groupEnd()
                ->whereIn('suppliers.id', $addCondition['filter']);
        } elseif ($addCondition['divisi']) {
            $poDataQry->groupStart()
                ->where('am_purchase_orders.division_id', $addCondition['divisi'])
                ->groupEnd();
        } elseif ($addCondition['filter']) {
            $poDataQry->whereIn('suppliers.id', $addCondition['filter']);
        }

        if (!empty($addCondition['search']) || !empty($addCondition['dateStart']) || !empty($addCondition['dateEnd']) || !empty($addCondition['filter']) || !empty($addCondition['divisi']) || !empty($addCondition['companyId'])) {
            $poDataQry->groupEnd();
        }

        $totalFilteredData = $poDataQry->countAllResults(false);
        if ($limit != null && $offset != null) {
            $data = $poDataQry->findAll($limit, $offset);
        } else {
            $data = $poDataQry->findAll();
        }

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getListLaporanPurchaseOrder($condition = [], $addCondition = [], $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi'        => 'divisis.divisi',
            'po_date'       => 'am_purchase_orders.po_date',
            'po_no'         => 'am_purchase_orders.po_no',
            'supplier_id'   => 'am_purchase_orders.supplier_id',
            'kode_barang'   => 'barang_master.kode_barang',
            'barang_name'   => 'barang_master.barang_name',
            'satuan_id'     => 'am_purchase_order_details.unit',
            'uraian'        => 'am_purchase_order_details.note',
            'spesifikasi'   => 'barang_master_spesifikasi.spesifikasi',
            'qty_order'     => 'qty_order',
            'qty_diterima'  => 'qty_diterima',
            'qty_sisa'      => 'qty_sisa',
            'total_harga'   => 'am_purchase_order_details.total',
            'spp_no'        => 'purchase_requests.spp_no'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'po_date'] ?? 'am_purchase_orders.po_date';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT utama
        $selectQry = "
            am_purchase_orders.id,
            am_purchase_orders.po_no,
            am_purchase_orders.po_date,
            suppliers.name AS supplier_name,
            divisis.divisi,
            barang_master.kode_barang,
            barang_master.barang_name,
            satuans.kode_satuan,
            am_purchase_order_details.note AS uraian,
            barang_master_spesifikasi.spesifikasi,
            am_purchase_order_details.qty AS qty_order,
            am_purchase_order_details.qty_diterima AS qty_diterima,
            am_purchase_order_details.remaining_qty AS qty_sisa,
            am_purchase_order_details.total AS total_harga,
            metadata.value as valas_name,
            purchase_requests.spp_no
        ";

        $builder = $this->asArray()
            ->select($selectQry, false) // <-- protect(false) agar query tidak di-escape
            ->join('suppliers', 'am_purchase_orders.supplier_id = suppliers.id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('satuans', 'am_purchase_order_details.unit = satuans.id', 'left')
            ->join('barang_master', 'am_purchase_order_details.barang_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        // Hitung total semua data (tanpa filter tambahan)
        $totalData = $builder->countAllResults(false);

        // Filter tambahan
        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] == "SUDAH POSTING") {
                $builder->groupStart();
                $builder->where('am_purchase_orders.is_posted', 1);
                $builder->groupEnd();
            } else if ($addCondition['status_posting'] == "BELUM POSTING") {
                $builder->groupStart();
                $builder->where('am_purchase_orders.is_posted', 0);
                $builder->groupEnd();
            }
        }
        if (!empty($addCondition['dateStart'])) {
            $builder->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $builder->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('am_purchase_orders.division_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $builder->where('am_purchase_orders.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('am_purchase_order_details.note', $addCondition['search'])
                ->orLike('purchase_requests.spp_no', $addCondition['search'])
                ->groupEnd();
        }

        // Hitung total dengan filter → pakai clone supaya SELECT tidak hilang
        $countBuilder = clone $builder;
        $totalFilteredData = $countBuilder->countAllResults(false);

        // Ambil data sesuai limit
        $data = $builder->findAll($limit, $offset);

        // Cari Data Untuk Di Sum Total nya
        $qtySum = $this->db->table('am_purchase_orders')
            ->select('SUM(am_purchase_order_details.total) AS total_harga')
            ->join('suppliers', 'am_purchase_orders.supplier_id = suppliers.id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('satuans', 'am_purchase_order_details.unit = satuans.id', 'left')
            ->join('barang_master', 'am_purchase_order_details.barang_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
            ->join('divisis', 'divisis.id = am_purchase_orders.division_id', 'left')
            ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
            ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
            ->where($condition);

        if (!empty($addCondition['status_posting'])) {
            if ($addCondition['status_posting'] == "SUDAH POSTING") {
                $qtySum->groupStart();
                $qtySum->where('am_purchase_orders.is_posted', 1);
                $qtySum->groupEnd();
            } else if ($addCondition['status_posting'] == "BELUM POSTING") {
                $qtySum->groupStart();
                $qtySum->where('am_purchase_orders.is_posted', 0);
                $qtySum->groupEnd();
            }
        }
        if (!empty($addCondition['dateStart'])) {
            $qtySum->where('am_purchase_orders.po_date >=', $addCondition['dateStart']);
        }
        if (!empty($addCondition['dateEnd'])) {
            $qtySum->where('am_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }
        if (!empty($addCondition['divisi_id'])) {
            $qtySum->where('am_purchase_orders.division_id', $addCondition['divisi_id']);
        }
        if (!empty($addCondition['supplier_id'])) {
            $qtySum->where('am_purchase_orders.supplier_id', $addCondition['supplier_id']);
        }
        if (!empty($addCondition['search'])) {
            $qtySum->groupStart()
                ->like('am_purchase_orders.po_no', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike('barang_master.barang_name', $addCondition['search'])
                ->orLike('barang_master_spesifikasi.spesifikasi', $addCondition['search'])
                ->orLike('am_purchase_order_details.note', $addCondition['search'])
                ->orLike('purchase_requests.spp_no', $addCondition['search'])
                ->groupEnd();
        }

        $grandTotalHarga = $qtySum->get()->getRowArray();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'grandTotalHarga'   => (float)$grandTotalHarga['total_harga']
        ];
    }
}
