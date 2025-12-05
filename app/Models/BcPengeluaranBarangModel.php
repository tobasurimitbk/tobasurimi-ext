<?php

namespace App\Models;

use CodeIgniter\Model;

class BcPengeluaranBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_pengeluaran_barang';
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


    public function getListOutstansingPengeluaran(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0
    ) {
        $bc25Model = new BC25Model();
        $bc41Model = new BC41Model();

        $bc25All = $bc25Model->where('jenis_pengeluaran', "ORDER FORM LOKAL")->where('deletedAt', null)->findAll();
        $bc41All = $bc41Model->where('jenis_pengeluaran', "ORDER FORM LOKAL")->where('deletedAt', null)->findAll();

        $multipleRefBc25 = array_column($bc25All, 'multiple_reference_id');
        $multipleRefBc41 = array_column($bc41All, 'multiple_reference_id');

        $multipleIdMerged = array_merge($multipleRefBc25, $multipleRefBc41);
        $idUsed = array();
        foreach ($multipleIdMerged as $m) {
            foreach (json_decode($m) as $mx) {
                array_push($idUsed, $mx);
            }
        }

        $notIn = "";
        if (!empty($idUsed)) {
            $idUsedEscaped = implode(",", array_map('intval', $idUsed));
            $notIn = " AND sales_order_detail.id_sales_order NOT IN ($idUsedEscaped) ";
        }

        $db = \Config\Database::connect();
        $where = [];
        $whereDateSalesOrder = "";
        $searchSalesOrder = "";

        // DARI BC 25 & BC 41

        if (!empty($condition['company_id'])) {
            $where[] = "sales_order.id_company = '$condition[company_id]'";
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDateSalesOrder = "AND sales_order.order_date BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));
            $searchSalesOrder = "
                AND(
                    barang_master_sales.kode_barang LIKE '%{$search}%'
                    OR barang_master_sales.barang_name LIKE '%{$search}%'
                    OR customers.name LIKE '%{$search}%'
                    OR sales_order.no_sales_order LIKE '%{$search}%'
                )
            ";
        }

        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'id',
            'tujuan_pengeluaran',
            'tanggal',
            'reference_no',
            'customer_name',
            'kode_barang',
            'barang_name',
            'qty',
            'kode_satuan',
            'amount'
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
            SELECT
                sales_order_detail.id,
                'ORDER FORM LOKAL' AS tujuan_pengeluaran,
                sales_order.order_date AS tanggal,
                sales_order.no_sales_order AS reference_no,
                customers.name AS customer_name,
                barang_master_sales.kode_barang,
                barang_master_sales.barang_name,
                sales_order_detail.qty,
                satuans.kode_satuan,
                sales_order_detail.amount
            FROM
                sales_order_detail
            LEFT JOIN sales_order ON sales_order.id = sales_order_detail.id_sales_order
            LEFT JOIN barang_master_sales ON barang_master_sales.id = sales_order_detail.id_barang
            LEFT JOIN satuans ON satuans.id = barang_master_sales.satuan_id 
            LEFT JOIN customers ON customers.id = sales_order.id_customer
            WHERE sales_order_detail.deletedAt IS NULL
            $notIn
            $filterCondition
            $whereDateSalesOrder
            $searchSalesOrder
        ";

        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = "
            SELECT * FROM ($baseQuery) AS x
            $orderBy
            LIMIT $limit OFFSET $offset
        ";

        $data = $db->query($mainQuery)->getResultArray();

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function referensiPengeluaranOrderFormLokal(
        $companyId,
        $customerId
    ) {
        $bc25Model = new BC25Model();
        $bc41Model = new BC41Model();
        $bc25List = $bc25Model->where('company_id', $companyId)->where('jenis_pengeluaran', "ORDER FORM LOKAL")->where('deletedAt', null)->findAll();
        $bc41List = $bc41Model->where('company_id', $companyId)->where('jenis_pengeluaran', "ORDER FORM LOKAL")->where('deletedAt', null)->findAll();

        $build = $this->db->table('sales_order')
            ->where('id_company', $companyId)
            ->where('id_customer', $customerId)
            ->where('deletedAt', null);

        if (count($bc25List) != 0) {
            $orderFormIdArr = array_column($bc25List, 'multiple_reference_id');
            $build->whereNotIn('id', $orderFormIdArr);
        }

        if (count($bc41List) != 0) {
            $orderFormIdArr = array_column($bc41List, 'multiple_reference_id');
            $build->whereNotIn('id', $orderFormIdArr);
        }

        $orderForm = $build->orderBy('id', "desc")->get()->getResultArray();
        $dataResult = array();

        foreach ($orderForm as $o) {
            array_push($dataResult, [
                'id' => $o['id'],
                'reference_no' => $o['no_sales_order']
            ]);
        }

        return $dataResult;
    }

    public function getReferensiNoPengeluaranOrderFormLokal($orderFormIdArr)
    {
        $salesOrderModel = new SalesOrderModel();
        $result = $salesOrderModel->whereIn('id', $orderFormIdArr)
            ->where('deletedAt', null)
            ->findAll();

        $salesOrderNoArr = array_column($result, 'no_sales_order');

        return json_encode($salesOrderNoArr, JSON_UNESCAPED_SLASHES);
    }

    public function getDetailBarang(
        $bcPengeluaranId,
        $tipeBc
    ) {
        $stockRevampModel = new StockRevampModel();
        $dataResult = array();

        $selectQry = "
            bc_pengeluaran_barang.*,
            tb_satuan_konversi.kode_satuan AS unit_name_konversi,
            tb_satuan_keluar.kode_satuan AS unit_name_keluar,
            metadata.value AS valas_name
        ";

        $detail = $this->asArray()
            ->select($selectQry)
            ->join('satuans tb_satuan_konversi', 'tb_satuan_konversi.id = bc_pengeluaran_barang.unit_id_konversi', 'left')
            ->join('satuans tb_satuan_keluar', 'tb_satuan_keluar.id = bc_pengeluaran_barang.unit_id_keluar', 'left')
            ->join('metadata', 'metadata.id = bc_pengeluaran_barang.valas_id', 'left')
            ->where('bc_pengeluaran_barang.bc_pengeluaran_id', $bcPengeluaranId)
            ->where('bc_pengeluaran_barang.tipe_bc', $tipeBc)
            ->where('bc_pengeluaran_barang.deletedAt', null)
            ->findAll();

        $no = 1;
        foreach ($detail as $a) {

            $fromStock = $stockRevampModel->getStockListAll(
                ["id" => $a['stock_detail_id']],
                0,
                "desc",
                1
            );

            foreach ($fromStock['data'] as $d) {
                // Stock
                array_push($dataResult, [
                    'no' => $no++,
                    'id' => $d['id'],
                    'divisi' => $d['divisi'],
                    'warehouse_name' => $d['warehouse_name'],
                    'reference_type' => $d['reference_type'],
                    'supplier_name' => $d['supplier_name'],
                    'kode_barang' => $d['kode_barang'],
                    'barang_name' => $d['barang_name'],
                    'spesifikasi' => $d['spesifikasi'],
                    'type_bc' => $d['type_bc'],
                    'po_no' => $d['po_no'],
                    'po_date' => !empty($d['po_date']) ? date('d/m/Y', strtotime($d['po_date'])) : "",
                    'lpb_date' => !empty($d['lpb_date']) ? date('d/m/Y', strtotime($d['lpb_date'])) : "",
                    'reference_no' => $d['reference_no'],
                    'qty_diterima' => (float)$d['qty_diterima'],
                    'qty_bersih' => (float)$d['qty_bersih'],
                    'kode_satuan' => $d['kode_satuan'],
                    "unit_id" => $d['unit_id'],
                    "keluar" => [
                        "qty_keluar" => $a['qty_keluar'],
                        "unit_id_keluar" => $a['unit_id_keluar'],
                        "unit_name_keluar" => $a['unit_name_keluar'],
                        "qty_konversi" => (float)$a['qty_konversi'],
                        "unit_id_konversi" => $a['unit_id_konversi'],
                        "unit_name_konversi" => $a['unit_name_konversi'],
                        "valas_id" => $a['valas_id'],
                        "valas_name" => $a['valas_name'],
                        "nilai_tukar" => (float)$a['nilai_tukar'],
                        "harga_satuan" => (float)$a['harga_satuan'],
                        "sub_total" => (float)$a['sub_total']
                    ],
                ]);
            }
        }

        return $dataResult;
    }
}
