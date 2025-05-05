<?php

namespace App\Models;

use CodeIgniter\I18n\Time;

use CodeIgniter\Model;
use Exception;

class RMPurchaseOrderModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'warehouse_id',
        'company_id',
        'divisi_id',
        'kemasan_id',
        'jumlah_kemasan',
        'kemasan_tambahan',
        'purchase_request_id',
        'bc_type',
        'po_no',
        'po_date',
        'supplier_id',
        'barang_id',
        'pph',
        'cong_sebenarnya',
        'cong_batasan',
        'subsidi_langsung',
        'total',
        'is_posted',
        'createdBy',
        'status_penerimaan',
        'total_before_pph',
        'total_after_pph'
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

    public function getNoPenerimaanBarang($supplier_id, $company_id, $divisi_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'company_id' => $company_id,
            'divisi_id' => $divisi_id
        ];

        $builder = $this->db->table('rm_purchase_orders');
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

        $builder = $this->db->table('rm_purchase_orders');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPoBBList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'            => 'rm_purchase_orders.po_date',
            'poNo'              => 'rm_purchase_orders.po_no',
            'divisi'       => 'divisis.divisi',
            'supplier'          => 'suppliers.name',
            'createdAt'         => 'rm_purchase_orders.createdAt',
            'statusPenerimaan'  => 'rm_purchase_orders.status_penerimaan',
            'total'             => 'rm_purchase_orders.total',
            'total_before_pph' => 'rm_purchase_orders.total_before_pph',
            'total_after_pph' => 'rm_purchase_orders.total_after_pph'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'rm_purchase_orders.po_date'] ?? 'rm_purchase_orders.po_date';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rm_purchase_orders.id,
            rm_purchase_orders.po_date,
            rm_purchase_orders.po_no,
            rm_purchase_orders.pph,
            rm_purchase_orders.cong_batasan,
            rm_purchase_orders.cong_sebenarnya,
            rm_purchase_orders.subsidi_langsung,
            rm_purchase_orders.is_posted,
            rm_purchase_orders.status_penerimaan,
            rm_purchase_orders.total_after_pph,
            rm_purchase_orders.total_before_pph,
            suppliers.name AS supplierName,
            suppliers.no_npwp as supplierNPWP,
            companies.company AS companyName,
            divisis.divisi,
            COUNT(rm_purchase_order_details.id) AS itemCount,
            SUM(rm_purchase_order_details.qty) AS totalQty";

        $bbLokalDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'rm_purchase_orders.supplier_id = suppliers.id', 'left')
            ->join('companies', 'rm_purchase_orders.company_id = companies.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('divisis', 'divisis.id = rm_purchase_orders.divisi_id', 'left')
            ->groupBy(('rm_purchase_orders.id'))
            ->orderBy($sort, $sortType);

        $totalData = $bbLokalDataQry->countAllResults(false);

        if ($addCondition['is_posted']) {
            if ($addCondition['is_posted'] == "SUDAH POSTING") {
                $bbLokalDataQry->where('is_posted', 1);
            } else {
                $bbLokalDataQry->where('is_posted', 0);
            }
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $bbLokalDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $bbLokalDataQry
                ->like('po_no', $addCondition['search']);
            $bbLokalDataQry->orLike('suppliers.name', $addCondition['search']);
            $bbLokalDataQry->orLike('divisis.divisi', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $bbLokalDataQry->where('rm_purchase_orders.po_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $bbLokalDataQry->where('rm_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $bbLokalDataQry->groupEnd();
        }

        $totalFilteredData = $bbLokalDataQry->countAllResults(false);
        $data = $bbLokalDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }


    public function getTotalWithPPH($id)
    {
        // Ambil data PO berdasarkan ID
        $data = $this->asObject()
            ->select("rm_purchase_orders.*, suppliers.no_npwp AS supplierNPWP")
            ->join('suppliers', 'rm_purchase_orders.supplier_id = suppliers.id', 'left')
            ->where('rm_purchase_orders.id', $id)
            ->first();

        if (!$data) {
            return null;
        }

        $detailPurchase = $this->db->table('rm_purchase_order_details')
            ->where('rm_purchase_order_id', $id)
            ->where('deletedAt', null)
            ->get()
            ->getResultArray();

        // Default PPH jika tidak ada
        $nilaiPph = 1.00;
        $nilaiPph2 = 0.00;

        if (!empty($data->supplierNPWP)) {
            $nilaiPph = 1.00 - 0.0025;
            $nilaiPph2 = 0.0025;
        } elseif ($data->supplierNPWP === null) {
            $nilaiPph = 1.00 - 0.005;
            $nilaiPph2 = 0.005;
        }

        $totalQty = 0;
        $nilaiTotalBulanan = 0;
        $nilaiTotalUmum = 0;
        $nilaiTotalHarian = 0;
        $totalTambahan = 0;

        foreach ($detailPurchase as $d) {
            if (empty($data->pph) || $data->pph === "None" || $data->pph === "Supplier") {
                $nilaiTotalHarian += ($d['daily_price'] * $d['qty']);
                $nilaiTotalUmum += ($d['general_price'] * $d['qty']);
                $nilaiTotalBulanan += ($d['monthly_price'] * $d['qty']);
            } else {
                // Jika ada PPH (Company)
                $nilaiTotalHarian += (($d['daily_price'] / $nilaiPph) * $d['qty']);
                $nilaiTotalUmum += (($d['general_price'] / $nilaiPph) * $d['qty']);
                $nilaiTotalBulanan += (($d['monthly_price'] / $nilaiPph) * $d['qty']);
            }
            $totalQty += $d['qty'];
        }

        // Pastikan variabel ini tidak menyebabkan error jika PPH kosong
        $nilaiTotalBulananWithPPH = $nilaiTotalBulanan;
        $nilaiTotalUmumWithPPH = $nilaiTotalUmum;
        $nilaiTotalHarianWithPPH = $nilaiTotalHarian;

        if ($data->pph === "Supplier" || $data->pph === "Company") {
            $nilaiTotalBulananWithPPH -= ($nilaiTotalBulanan * $nilaiPph2);
            $nilaiTotalUmumWithPPH -= ($nilaiTotalUmum * $nilaiPph2);
            $nilaiTotalHarianWithPPH -= ($nilaiTotalHarian * $nilaiPph2);
        }

        $totalTambahanWithPPH = 0;

        if ($data->pph == "Company") {
            $selisih = ($data->cong_batasan - $data->cong_sebenarnya + $data->subsidi_langsung) / $nilaiPph;
            $totalTambahan = $selisih;
            $totalTambahanWithPPH = $totalTambahan - ($totalTambahan * $nilaiPph2);
        } else {
            $selisih = ($data->cong_batasan - $data->cong_sebenarnya + $data->subsidi_langsung);
            $totalTambahan = ($selisih * $totalQty);
            $totalTambahanWithPPH = $totalTambahan - ($totalTambahan * $nilaiPph2);
        }

        $totalBeforePph = $nilaiTotalBulanan + $nilaiTotalHarian + $nilaiTotalUmum + abs($totalTambahan);
        $totalAfterPph = $nilaiTotalBulananWithPPH + $nilaiTotalHarianWithPPH + $nilaiTotalUmumWithPPH + abs($totalTambahanWithPPH);

        return [
            'total_before_pph' => round($totalBeforePph, 2),
            'total_after_pph'  => round($totalAfterPph == 0 ? $totalBeforePph : $totalAfterPph, 2)
        ];
    }

    public function getPoBBLokalById($id)
    {
        $selectQry = "rm_purchase_orders.*,
                            companies.holding_company,
                            companies.company AS companyName,
                            companies.address AS companyAddress,
                            suppliers.name AS supplierName,
                            suppliers.address AS supplierAddress,
                            suppliers.phone AS supplierPhone,
                            suppliers.no_npwp AS supplierNPWP,
                            divisis.divisi,
                            users.name AS createdBy,
                            barang_master.barang_name AS barangName
                            ";

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('divisis', 'divisis.id = rm_purchase_orders.divisi_id', 'left')
            ->find($id);

        return $poBBLokalData;
    }

    public function generateNoPo()
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

        $today = Time::today('America/Chicago', 'en_US');

        $year = $today->getYear();
        $year = substr($year, -2);
        $month = $today->getMonth() - 1;

        $lastStr =  'P/' . $romanNumb[$month] . '/' . $year;

        $builder = $this->db->table('rm_purchase_orders');
        $builder->select('po_no');
        $builder->orderBy('po_no', 'desc');
        $builder->like('po_no', $lastStr);
        $query = $builder->get();

        $increment = '001';

        if ($query->getResultArray()) {
            $lastPo = explode('/', $query->getResultArray()[0]['po_no']);
            $lastPo = intval($lastPo[0]) + 1;

            if ($lastPo < 10) {
                $lastPo = "00" . $lastPo . "";
            } elseif ($lastPo > 9 && $lastPo < 100) {
                $lastPo = "0" . $lastPo . "";
            } else {
                $lastPo = strval($lastPo);
            }

            $increment = $lastPo;
        };

        $generatedPoNo = $increment . '/' . $lastStr;

        return $generatedPoNo;
    }

    public function get_new_no_po($bln, $thn, $companyId)
    {
        $head = "PO/LBB-" . $bln . $thn . '/';

        $first_day = "$thn-$bln-01";
        $last_day = date("Y-m-t", strtotime($first_day));

        if ($companyId == 16) {
            $lastPO = $this->select('po_no')
                ->like('po_no', "PO/LBB-")
                ->where('rm_purchase_orders.po_date >=', $first_day)
                ->where('rm_purchase_orders.po_date <=', $last_day)
                ->where('rm_purchase_orders.company_id', $companyId)
                ->orderBy('po_no', "DESC")
                ->first();
        } else {
            // Kim
            $lastPO = $this->select('po_no')
                ->like('po_no', "PO/LBB-")
                ->where('rm_purchase_orders.po_date >=', $first_day)
                ->where('rm_purchase_orders.po_date <=', $last_day)
                ->where('rm_purchase_orders.company_id !=', 16)
                ->orderBy('po_no', "DESC")
                ->first();
        }
        $counterFirst = '000001';

        if ($lastPO) {
            $last = explode('/', $lastPO['po_no']);
            if (isset($last[2]) && is_numeric($last[2])) {
                $poLastDigit = (int) $last[2];
                $counterFirst = str_pad($poLastDigit + 1, 6, '0', STR_PAD_LEFT);
            }
        }

        return $head . $counterFirst;
    }


    public function getPoBBLokalForSupplierReport($startDate, $finishDate, $supplier, $bahanBaku, $warehouse)
    {
        $selectQry = "
        suppliers.no_npwp AS supplierNpwp,
        suppliers.name AS supplierName, 
        rm_purchase_orders.po_no AS poNum, 
        rm_purchase_orders.po_date AS poDate, 
        barang_master.barang_name AS barangName, 
        warehouses.warehouse_name AS warehouseName, 
        rm_purchase_order_details.qty AS qtyPO, 
        satuans.nama_satuan AS satuanName, 
        companies.company AS companyName, 
        rm_purchase_orders.subsidi_langsung AS subsidi, 
        rm_purchase_order_details.daily_price AS dppHarian,
        rm_purchase_order_details.monthly_price AS dppBulanan,
        rm_purchase_order_details.general_price AS dppUmum,
        rm_purchase_orders.pph AS poPPH,
        supplier_harga.spesifikasi AS spekName
        ";
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            // 'rm_purchase_orders.po_date >=' => $startDate,
            // 'rm_purchase_orders.po_date <=' => $finishDate,
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
        ];
        if (!empty($bahanBaku)) {
            $condition['rm_purchase_orders.barang_id'] = $bahanBaku;
        }
        if (!empty($warehouse)) {
            $condition['penerimaan_barang.warehouse_id'] = $warehouse;
        }
        if (!empty($supplier)) {
            $condition['rm_purchase_orders.supplier_id'] = $supplier;
        }

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->where($condition)
            ->findAll();

        return $poBBLokalData;
    }

    // pendapatan supplier
    //PDF
    public function getPoBBLokalForSupplierReportPdf($dateStart, $dateEnd, $supplier, $bahanBaku, $warehouse, $companyId, $poNo)
    {
        $selectQry = "
        suppliers.no_npwp AS supplierNpwp,
        suppliers.name AS supplierName, 
        rm_purchase_orders.po_no AS poNum, 
        rm_purchase_orders.po_date AS poDate, 
        barang_master.barang_name AS barangName, 
        warehouses.warehouse_name AS warehouseName, 
        rm_purchase_order_details.qty AS qtyPO, 
        satuans.nama_satuan AS satuanName, 
        companies.company AS companyName, 
        rm_purchase_orders.subsidi_langsung AS subsidi, 
        rm_purchase_order_details.daily_price AS dppHarian,
        rm_purchase_order_details.monthly_price AS dppBulanan,
        rm_purchase_order_details.general_price AS dppUmum,
        rm_purchase_orders.pph AS poPPH,
        supplier_harga.spesifikasi AS spekName
        ";
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $companyId
        ];

        if (!empty($dateStart) and !empty($dateEnd)) {
            $condition['rm_purchase_orders.po_date >='] = $dateStart;
            $condition['rm_purchase_orders.po_date <='] = $dateEnd;
        }

        if (!empty($bahanBaku)) {
            $condition['rm_purchase_orders.barang_id'] = $bahanBaku;
        }
        if (!empty($warehouse)) {
            $condition['penerimaan_barang.warehouse_id'] = $warehouse;
        }
        if (!empty($supplier)) {
            $condition['rm_purchase_orders.supplier_id'] = $supplier;
        }

        if (!empty($poNo)) {
            $condition['rm_purchase_orders.po_no'] = $poNo;
        }

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->where($condition)
            ->findAll();

        return $poBBLokalData;
    }


    //TABLE
    public function getPoBBLokalForSupplier($availableSort, $condition, $addCondition, $limit = 10, $offset = 0)
    {

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
        suppliers.no_npwp AS supplierNpwp,
        suppliers.name AS supplierName, 
        rm_purchase_orders.po_no AS poNum, 
        rm_purchase_orders.po_date AS poDate, 
        barang_master.barang_name AS barangName, 
        warehouses.warehouse_name AS warehouseName, 
        rm_purchase_order_details.qty AS qtyPO, 
        satuans.nama_satuan AS satuanName, 
        companies.company AS companyName, 
        rm_purchase_orders.subsidi_langsung AS subsidi, 
        rm_purchase_order_details.daily_price AS dppHarian,
        rm_purchase_order_details.monthly_price AS dppBulanan,
        rm_purchase_order_details.general_price AS dppUmum,
        rm_purchase_orders.pph AS poPPH,
        supplier_harga.spesifikasi AS spekName
        ";

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);


        $totalData = $poBBLokalData->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['supplierId'] || $addCondition['barangId'] || $addCondition['warehouseId'] || $addCondition['poNo']) {
            $poBBLokalData->groupStart();
        }

        if ($addCondition['supplierId']) {
            $poBBLokalData->where('rm_purchase_orders.supplier_id', $addCondition['supplierId']);
        }

        if ($addCondition['barangId']) {
            $poBBLokalData->where('rm_purchase_orders.barang_id', $addCondition['barangId']);
        }

        if ($addCondition['warehouseId']) {
            $poBBLokalData->where('penerimaan_barang.warehouse_id', $addCondition['warehouseId']);
        }

        if ($addCondition['poNo']) {

            $poBBLokalData->where('rm_purchase_orders.po_no', $addCondition['poNo']);
        }

        if ($addCondition['dateStart']) {
            $poBBLokalData->where('rm_purchase_orders.po_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $poBBLokalData->where('rm_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }


        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['supplierId'] || $addCondition['barangId'] || $addCondition['warehouseId'] || $addCondition['poNo']) {
            $poBBLokalData->groupEnd();
        }

        $totalFilteredData = $poBBLokalData->countAllResults(false);
        $data = $poBBLokalData->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
    // pendapatan supplier

    public function getPoBBLokalForAllSupplierReport($startDate, $finishDate)
    {
        $selectQry = "
        suppliers.no_npwp AS supplierNpwp,
        suppliers.name AS supplierName, 
        barang_master.barang_name AS barangName, 
        SUM(rm_purchase_orders.subsidi_langsung) AS subsidi,
        SUM(rm_purchase_order_details.daily_price) AS dppHarian,
        SUM(rm_purchase_order_details.monthly_price) AS dppBulanan,
        SUM(rm_purchase_order_details.general_price) AS dppUmum,
        rm_purchase_orders.pph AS poPPH
        ";
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            // 'rm_purchase_orders.po_date >=' => $startDate,
            // 'rm_purchase_orders.po_date <=' => $finishDate,
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
        ];

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->where($condition)
            ->groupBy(['suppliers.name', 'barang_master.barang_name'])
            ->findAll();

        return $poBBLokalData;
    }

    //laporan rekap all suplier pdf
    public function getPoBBLokalForAllSupplierReportPdf($dateStart, $dateEnd, $supplier, $bahanBaku, $companyId)
    {
        $selectQry = "
        suppliers.no_npwp AS supplierNpwp,
        suppliers.name AS supplierName, 
        barang_master.barang_name AS barangName, 
        rm_purchase_orders.supplier_id,
        rm_purchase_orders.barang_id,
        SUM(rm_purchase_orders.subsidi_langsung) AS subsidi,
        SUM(rm_purchase_order_details.daily_price) AS dppHarian,
        SUM(rm_purchase_order_details.monthly_price) AS dppBulanan,
        SUM(rm_purchase_order_details.general_price) AS dppUmum,
        SUM(rm_purchase_order_details.qty) AS totalQty,
        rm_purchase_orders.pph AS poPPH
        ";
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            // 'rm_purchase_orders.po_date >=' => $startDate,
            // 'rm_purchase_orders.po_date <=' => $finishDate,
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $companyId
        ];

        if (!empty($dateStart) and !empty($dateEnd)) {
            $condition['rm_purchase_orders.po_date >='] = $dateStart;
            $condition['rm_purchase_orders.po_date <='] = $dateEnd;
        }

        if (!empty($bahanBaku)) {
            $condition['rm_purchase_orders.barang_id'] = $bahanBaku;
        }

        if (!empty($supplier)) {
            $condition['rm_purchase_orders.supplier_id'] = $supplier;
        }

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->where($condition)
            ->groupBy(['suppliers.name', 'barang_master.barang_name'])
            ->findAll();

        return $poBBLokalData;
    }

    //laporan rekap all suplier table
    public function getPoBBLokalForAllSupplier($availableSort, $condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
        suppliers.no_npwp AS supplierNpwp,
        suppliers.name AS supplierName, 
        rm_purchase_orders.barang_id,
        rm_purchase_orders.supplier_id,
        barang_master.barang_name AS barangName, 
        SUM(rm_purchase_order_details.qty) AS totalQty,
        SUM(rm_purchase_orders.subsidi_langsung) AS subsidi,
        SUM(rm_purchase_order_details.daily_price) AS dppHarian,
        SUM(rm_purchase_order_details.monthly_price) AS dppBulanan,
        SUM(rm_purchase_order_details.general_price) AS dppUmum,
        rm_purchase_orders.pph AS poPPH
        ";


        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->where($condition)
            ->groupBy(['suppliers.name', 'barang_master.barang_name'])
            ->orderBy($sort, $sortType);

        $totalData = $poBBLokalData->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['supplierId'] || $addCondition['barangId']) {
            $poBBLokalData->groupStart();
        }

        if ($addCondition['supplierId']) {
            $poBBLokalData->where('rm_purchase_orders.supplier_id', $addCondition['supplierId']);
        }

        if ($addCondition['barangId']) {
            $poBBLokalData->where('rm_purchase_orders.barang_id', $addCondition['barangId']);
        }



        if ($addCondition['dateStart']) {
            $poBBLokalData->where('rm_purchase_orders.po_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $poBBLokalData->where('rm_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }


        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['supplierId'] || $addCondition['barangId']) {
            $poBBLokalData->groupEnd();
        }

        $totalFilteredData = $poBBLokalData->countAllResults(false);
        $data = $poBBLokalData->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getPoBBLokalForSupplierReportRekap($startDate, $finishDate, $supplier, $bahanBaku, $warehouse)
    {
        $selectQry = "
        suppliers.name AS supplierName, 
        barang_master.barang_name AS barangName, 
        bagian.nama_bagian AS bagianName, 
        SUM(rm_purchase_orders.subsidi_langsung) AS subsidi,
        SUM(rm_purchase_order_details.daily_price) AS dppHarian,
        SUM(rm_purchase_order_details.monthly_price) AS dppBulanan,
        SUM(rm_purchase_order_details.general_price) AS dppUmum,
        SUM(rm_purchase_order_details.qty) AS qtyPO,
        rm_purchase_orders.pph AS poPPH,
        supplier_harga.spesifikasi AS spekName, 
        satuans.nama_satuan AS satuanName, 
        ";
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            // 'rm_purchase_orders.po_date >=' => $startDate,
            // 'rm_purchase_orders.po_date <=' => $finishDate,
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
        ];

        if (!empty($bahanBaku)) {
            $condition['rm_purchase_orders.barang_id'] = $bahanBaku;
        }
        if (!empty($warehouse)) {
            $condition['penerimaan_barang.warehouse_id'] = $warehouse;
        }
        if (!empty($supplier)) {
            $condition['rm_purchase_orders.supplier_id'] = $supplier;
        }

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('barang_master', 'rm_purchase_orders.barang_id = barang_master.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->join('divisis', 'divisis.id = rm_purchase_orders.divisi_id', 'left')
            ->join('bagian', 'bagian.division_id = divisis.id', 'left')
            ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->where($condition)
            ->groupBy(['bagian.nama_bagian', 'barang_master.barang_name', 'suppliers.name'])
            ->findAll();

        return $poBBLokalData;
    }

    //rekap all barang
    public function getPoBBLokalForSupplierReportRekapPdf($dateStart, $dateEnd, $divisi, $bahanBaku, $companyId)
    {
        $selectQry = "
        suppliers.name AS supplierName, 
        barang_master.barang_name AS barangName, 
        divisis.divisi AS bagianName, 
        SUM(rm_purchase_orders.subsidi_langsung) AS subsidi,
        SUM(rm_purchase_order_details.daily_price) AS dppHarian,
        SUM(rm_purchase_order_details.monthly_price) AS dppBulanan,
        SUM(rm_purchase_order_details.general_price) AS dppUmum,
        SUM(rm_purchase_order_details.qty) AS qtyPO,
        rm_purchase_orders.pph AS poPPH,
        barang_master_spesifikasi.spesifikasi AS spekName, 
        satuans.nama_satuan AS satuanName, 
        ";
        $condition = [
            'rm_purchase_orders.is_posted' => '1',
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.tipe_bahan' => 'BAKU',
            'rm_purchase_orders.company_id'  => $companyId
        ];

        if (!empty($dateStart) and !empty($dateEnd)) {
            $condition['rm_purchase_orders.po_date >='] = $dateStart;
            $condition['rm_purchase_orders.po_date <='] = $dateEnd;
        }

        if (!empty($divisi)) {
            $condition['rm_purchase_orders.divisi_id'] = $divisi;
        }

        if (!empty($bahanBaku)) {
            $condition['rm_purchase_orders.barang_id'] = $bahanBaku;
        }


        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            // ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            // ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'rm_purchase_order_details.barang1_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'rm_purchase_order_details.barang2_id = barang_master_spesifikasi.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->join('divisis', 'divisis.id = rm_purchase_orders.divisi_id', 'left')
            // ->join('bagian', 'bagian.division_id = divisis.id', 'left')
            // ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->where($condition)
            ->groupBy(['divisis.divisi', 'barang_master.barang_name', 'suppliers.name'])
            ->findAll();

        return $poBBLokalData;
    }

    //table rekap
    public function getPoBBLokalForSupplierRekap($availableSort, $condition, $addCondition, $limit = 10, $offset = 0)
    {

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'rm_purchase_orders.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
        suppliers.name AS supplierName, 
        barang_master.barang_name AS barangName, 
        divisis.divisi AS bagianName, 
        SUM(rm_purchase_orders.subsidi_langsung) AS subsidi,
        SUM(rm_purchase_order_details.daily_price) AS dppHarian,
        SUM(rm_purchase_order_details.monthly_price) AS dppBulanan,
        SUM(rm_purchase_order_details.general_price) AS dppUmum,
        SUM(rm_purchase_order_details.qty) AS qtyPO,
        rm_purchase_orders.pph AS poPPH,
        barang_master_spesifikasi.spesifikasi AS spekName, 
        satuans.nama_satuan AS satuanName, 
        ";


        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            // ->join('companies', 'companies.id = rm_purchase_orders.company_id', 'left')
            // ->join('users', 'rm_purchase_orders.createdBy = users.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'rm_purchase_order_details.barang1_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'rm_purchase_order_details.barang2_id = barang_master_spesifikasi.id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('warehouses', 'penerimaan_barang.warehouse_id = warehouses.id', 'left')
            ->join('divisis', 'divisis.id = rm_purchase_orders.divisi_id', 'left')
            // ->join('bagian', 'bagian.division_id = divisis.id', 'left')
            // ->join('supplier_harga', 'supplier_harga.id = rm_purchase_order_details.supplier_harga_id', 'left')
            ->where($condition)
            ->groupBy(['divisis.divisi', 'barang_master_spesifikasi.spesifikasi'])
            ->orderBy($sort, $sortType);

        $totalData = $poBBLokalData->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['barangId'] || $addCondition['divisiId']) {
            $poBBLokalData->groupStart();
        }

        if ($addCondition['barangId']) {
            $poBBLokalData->where('rm_purchase_orders.barang_id', $addCondition['barangId']);
        }

        if ($addCondition['divisiId']) {
            $poBBLokalData->where('rm_purchase_orders.divisi_id', $addCondition['divisiId']);
        }

        if ($addCondition['dateStart']) {
            $poBBLokalData->where('rm_purchase_orders.po_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $poBBLokalData->where('rm_purchase_orders.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd'] || $addCondition['barangId'] || $addCondition['divisiId']) {
            $poBBLokalData->groupEnd();
        }

        $totalFilteredData = $poBBLokalData->countAllResults(false);
        $data = $poBBLokalData->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getPOByNoPO($noPO, $companyID, $barang1ID, $barang2ID)
    {
        $condition = [
            "rm_purchase_orders.company_id"  => $companyID,
            "rm_purchase_orders.po_no"  => $noPO,
            "rm_purchase_orders.deletedAt" => NULL,
            "rm_purchase_order_details.deletedAt" => NULL,
            "rm_purchase_order_details.barang1_id" => $barang1ID,
            "rm_purchase_order_details.barang2_id" => $barang2ID,
        ];

        $selectQry = "
            barang_master.barang_name as nama_barang, 
            rm_purchase_orders.*,
            suppliers.name as nama_supplier,
            rm_purchase_order_details.*,
            SUM(rm_purchase_order_details.general_price) AS general_price,
            SUM(rm_purchase_order_details.daily_price) AS daily_price,
            SUM(rm_purchase_order_details.monthly_price) AS monthly_price,
        ";

        $res = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id')
            ->join('barang_master', 'barang_master.id = rm_purchase_order_details.barang1_id')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id')
            ->first();

        if ($res == null) {
            return [
                'hargaTerakhirNumber' => 0,
                'hargaTerakhir' => '-',
                'supplierTerakhir' => '-',
                'dataPO' => null
            ];
        } else {
            $totalPrice = $res['general_price'] + $res['daily_price'] + $res['monthly_price'];
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
        barang_master_spesifikasi.spesifikasi AS spekName, 
        rm_purchase_orders.subsidi_langsung AS subsidi,
        rm_purchase_order_details.daily_price AS price1,
        rm_purchase_order_details.monthly_price AS price2,
        rm_purchase_order_details.general_price AS price3,
        SUM(rm_purchase_order_details.qty) AS qtyPO,
        rm_purchase_order_details.barang1_id AS barang1_id,
        rm_purchase_order_details.barang2_id AS barang2_id,
        rm_purchase_orders.pph AS poPPH,
        satuans.kode_satuan AS satuanName, 
        CONCAT(rm_purchase_orders.po_no) AS po_no, 
        (SUM(rm_purchase_order_details.daily_price + rm_purchase_order_details.monthly_price + rm_purchase_order_details.general_price)) AS avg_price_per_qty
    ";

        $poBBLokalData = $this->asObject()
            ->select($selectQry)
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'rm_purchase_order_details.barang1_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'rm_purchase_order_details.barang2_id = barang_master_spesifikasi.id', 'left')
            ->join('account_barang', 'rm_purchase_order_details.barang1_id = account_barang.barang_master_id', 'left')
            ->join('satuans', 'satuans.id = rm_purchase_order_details.satuan_id', 'left')
            ->where('rm_purchase_orders.divisi_id', $divisi_id)
            ->where('rm_purchase_orders.is_posted', '1')
            ->where('rm_purchase_orders.po_date >=', date('Y-m-d', strtotime($po_date_awal)))
            ->where('rm_purchase_orders.po_date <=', date('Y-m-d', strtotime($po_date_akhir)))
            // ->where('account_barang.kategori_id', $kategori_id)
            ->where('account_barang.divisi_id', $divisi_id)
            ->where('rm_purchase_orders.company_id', $company_id)
            ->where('rm_purchase_orders.deletedAt', null)
            ->where('rm_purchase_order_details.deletedAt', null)
            ->groupBy('rm_purchase_order_details.barang1_id, rm_purchase_order_details.barang2_id, rm_purchase_orders.company_id')
            ->findAll();

        return $poBBLokalData;
    }

    public function pphPendapatanSupplier($company_id, $supplier_id, $barang1_id, $start_date, $end_date, $type_harga)
    {
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $selectQry = "rm_purchase_orders.*,suppliers.no_npwp";
        $rmPurchaseOrder = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
            ->where('rm_purchase_orders.company_id', $company_id)
            ->where('supplier_id', $supplier_id)
            ->where('barang_id', $barang1_id)
            ->where('rm_purchase_orders.deletedAt', null);
        if ($start_date != '') {
            $rmPurchaseOrder->where('rm_purchase_orders.po_date >=',  $start_date);
        }
        if ($end_date != '') {
            $rmPurchaseOrder->where('rm_purchase_orders.po_date <=', $end_date);
        }

        $resultRmPurchaseOrder = $rmPurchaseOrder->findAll();
        $pphTotal = 0;
        $dibayarkan = 0;

        foreach ($resultRmPurchaseOrder as $r) {
            $dataPODetail = $rmPurchaseOrderDetailModel->getPoBBLokalDetailById($r->id);
            $nilai_pph = !empty($r->no_npwp) ? (1.00 - 0.0025) : (1.00 - 0.005);
            $nilai_pph2 = !empty($r->no_npwp) ? 0.0025 : 0.005;
            $nilai_total = 0;
            // detail 
            foreach ($dataPODetail as $d) {
                if ($type_harga == 'general_price') {
                    if ($r->pph == 'None') {
                        $nilai_total = $nilai_total + (($d->general_price) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                    if ($r->pph == 'Supplier') {
                        $nilai_total = $nilai_total + (($d->general_price) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                    if ($r->pph == 'Company') {
                        $nilai_total = $nilai_total + (($d->general_price / ($nilai_pph)) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                } elseif ($type_harga == 'daily_price') {
                    if ($r->pph == 'None') {
                        $nilai_total = $nilai_total + (($d->daily_price) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                    if ($r->pph == 'Supplier') {
                        $nilai_total = $nilai_total + (($d->daily_price) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                    if ($r->pph == 'Company') {
                        $nilai_total = $nilai_total + (($d->daily_price / ($nilai_pph)) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                } else {
                    if ($r->pph == 'None') {
                        $nilai_total = $nilai_total + (($d->monthly_price) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                    if ($r->pph == 'Supplier') {
                        $nilai_total = $nilai_total + (($d->monthly_price) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                    if ($r->pph == 'Company') {
                        $nilai_total = $nilai_total + (($d->monthly_price / ($nilai_pph)) * formatter($d->qty, "STR_TO_FLOAT"));
                    }
                }
            }

            $pphTotal =  $pphTotal + ($nilai_total * $nilai_pph2);
            $dibayarkan = $dibayarkan + ($nilai_total - $pphTotal);
        }

        return [
            'pphTotal' => $pphTotal,
            'dibayarkan' => $dibayarkan
        ];
    }

    public function generateTotalBeforeAndAfterPph($id)
    {
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();

        $selectQry = "rm_purchase_orders.id,
            rm_purchase_orders.po_date,
            rm_purchase_orders.po_no,
            rm_purchase_orders.pph,
            rm_purchase_orders.cong_batasan,
            rm_purchase_orders.cong_sebenarnya,
            rm_purchase_orders.subsidi_langsung,
            rm_purchase_orders.is_posted,
            rm_purchase_orders.status_penerimaan,
            suppliers.name AS supplierName,
            suppliers.no_npwp as supplierNPWP,
            companies.company AS companyName,
            divisis.divisi,
            COUNT(rm_purchase_order_details.id) AS itemCount";

        $data = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'rm_purchase_orders.supplier_id = suppliers.id', 'left')
            ->join('companies', 'rm_purchase_orders.company_id = companies.id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('divisis', 'divisis.id = rm_purchase_orders.divisi_id', 'left')
            ->where('rm_purchase_orders.id', $id)
            ->first();

        $nilaiPph = !empty($data->supplierNPWP) ? (1.00 - 0.0025) : (1.00 - 0.005);
        $nilaiPph2 = !empty($data->supplierNPWP) ? 0.0025 : 0.005;
        $detailPurchase = $rmPurchaseOrderDetailModel->where('rm_purchase_order_id', $data->id)->where('deletedAt', null)->findAll();

        // PUNYA NPWP 0.25
        // GK PUNYA 0.5
        // 314.54 RUPIAH 
        // sebelum pph 2,635
        // 2,642,105.26 SEBELUM PPH
        // 26,35.500 SESUDAH PPH
        // 325 KTP 

        $totalQty = 0;
        // Tanpa PPH
        $nilaiTotalBulanan = 0;
        $nilaiTotalUmum = 0;
        $nilaiTotalHarian = 0;
        // Dengan PPH
        $nilaiTotalBulananWithPPH = 0;
        $nilaiTotalUmumWithPPH = 0;
        $nilaiTotalHarianWithPPH = 0;
        // Total Tambahan
        $totalTambahan = 0;
        $totalTambahanWithPPH = 0;

        // Nilai PPH
        // $nilaiPPHBulanan = 0;
        // $nilaiPPHumum = 0;
        // $nilaiPPHHarian = 0;

        foreach ($detailPurchase as $d) {

            if ($data->pph === "None" || $data->pph === "Supplier") {
                $nilaiTotalHarian +=  ($d['daily_price'] * $d['qty']);
                $nilaiTotalUmum +=  ($d['general_price'] * $d['qty']);
                $nilaiTotalBulanan += ($d['monthly_price'] * $d['qty']);
            } else {
                // COMPANY
                $nilaiTotalHarian +=  (($d['daily_price'] / $nilaiPph) * $d['qty']);
                $nilaiTotalUmum +=  (($d['general_price'] / $nilaiPph) * $d['qty']);
                $nilaiTotalBulanan += (($d['monthly_price'] / $nilaiPph) * $d['qty']);
            }

            $totalQty += $d['qty'];
        }

        if ($data->pph === "Supplier" || $data->pph === "Company") {
            $nilaiTotalBulananWithPPH = $nilaiTotalBulanan - ($nilaiTotalBulanan * $nilaiPph2);
            $nilaiTotalUmumWithPPH = $nilaiTotalUmum - ($nilaiTotalUmum * $nilaiPph2);
            $nilaiTotalHarianWithPPH = $nilaiTotalHarian - ($nilaiTotalHarian * $nilaiPph2);
        }

        if ($data->pph == "Company") {
            $selisih = ($data->cong_batasan - $data->cong_sebenarnya + $data->subsidi_langsung) / $nilaiPph;
            $totalTambahan = $selisih;
            $totalTambahanWithPPH = $totalTambahan - ($totalTambahan * $nilaiPph2);
        } else {
            $selisih =  ($data->cong_batasan - $data->cong_sebenarnya + $data->subsidi_langsung);
            $totalTambahan = ($selisih * $totalQty);
            $totalTambahanWithPPH = $totalTambahan - ($totalTambahan * $nilaiPph2);
        }

        // NILAI SEBELUM PPH
        $totalBeforePph = $nilaiTotalBulanan + $nilaiTotalHarian + $nilaiTotalUmum +  abs($totalTambahan);
        $totalAfterPph = $nilaiTotalBulananWithPPH + $nilaiTotalHarianWithPPH + $nilaiTotalUmumWithPPH + abs($totalTambahanWithPPH);

        if ($totalAfterPph == 0) {
            $totalAfterPph = $totalBeforePph;
        }

        return [
            'total_before_pph' => (float) number_format($totalBeforePph, 2, '.', ''),
            'total_after_pph' => (float) number_format($totalAfterPph, 2, '.', ''),
        ];
    }

    public function getPOByIdSupplierWithInvoice($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'            => 'rm_purchase_orders.po_date',
            'poNo'              => 'rm_purchase_orders.po_no',
            'divisi'            => 'divisis.divisi',
            'supplierName'      => 'suppliers.name',
            'total'             => 'rm_purchase_orders.total',
            'currencyName'      => 'metadata.value',
            'createdAt'         => 'rm_purchase_orders.createdAt',
            'updatedAt'         => 'rm_purchase_orders.updatedAt',
            'statusPenerimaan'  => 'rm_purchase_orders.status_penerimaan'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'rm_purchase_orders.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rm_purchase_orders.id AS id, 
                    rm_purchase_orders.po_date AS tanggal_invoice, 
                    rm_purchase_orders.po_no AS no_invoice, 
                    suppliers.id AS supplier_id, 
                    suppliers.name AS supplier_name,
                    divisis.divisi AS divisi,
                    COUNT(rm_purchase_order_details.id) AS itemCount,
                    rm_purchase_orders.total AS total, 
                    local_po_payments.amount AS remaining,
                    penerimaan_barang.no_penerimaan_barang AS no_penerimaan_barang";

        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->where('rm_purchase_orders.status_penerimaan', 1)
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id')
            ->join('divisis', 'divisis.id = rm_purchase_orders.divisi_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_orders.id = rm_purchase_order_details.rm_purchase_order_id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_id = rm_purchase_orders.id AND penerimaan_barang_detail.purchase_order_details_id = rm_purchase_order_details.id', 'left')
            ->join('penerimaan_barang', "penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id AND penerimaan_barang.status_penerimaan = 'LOKAL' AND penerimaan_barang.tipe_bahan = 'BAKU'", 'left')
            ->join('local_po_payments', 'FIND_IN_SET(rm_purchase_orders.id, REPLACE(REPLACE(local_po_payments.multiple_po_id, "[", ""), "]", ""))', 'left') // Menyesuaikan jika multiple_po_id berbentuk JSON atau array sebagai string
            ->groupBy('rm_purchase_orders.id')
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if (!empty($addCondition['search']) || !empty($addCondition['dateStart']) || !empty($addCondition['dateEnd']) || !empty($addCondition['filter']) || !empty($addCondition['divisi'])) {
            $poDataQry->groupStart();
        }

        if (!empty($addCondition['search'])) {
            $poDataQry->like('rm_purchase_orders.po_no', $addCondition['search']);
        }

        if (!empty($addCondition['dateStart'])) {
            $poDataQry->where('rm_purchase_orders.po_date >=', $addCondition['dateStart']);
        }

        if (!empty($addCondition['dateEnd'])) {
            $poDataQry->where('rm_purchase_orders.po_date <=', $addCondition['dateEnd']);
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
