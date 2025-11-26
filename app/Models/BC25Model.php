<?php

namespace App\Models;

use CodeIgniter\Model;

class BC25Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_25';
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

    public function getList(
        $condition,
        $orderColumnIndex,
        $orderDir,
        $limit = 10,
        $offset = 0

    ) {
        $db = \Config\Database::connect();
        $where = [];
        $whereDate = "";
        $searchOrderForm = "";

        if (!empty($condition['company_id'])) {
            $where[] = "bc_25.company_id ='$condition[company_id]'";
        }

        if (!empty($condition['dateStart']) && !empty($condition['dateEnd'])) {
            $whereDate = "AND bc_25.tanggal BETWEEN '$condition[dateStart]' AND '$condition[dateEnd]'";
        }

        if (!empty($condition['status_posting'])) {
            if ($condition['status_posting'] == "ALL") {
                $where[] = "(bc_25.status_posting='1' OR bc_25.status_posting='0')";
            } elseif ($condition['status_posting'] == "SUDAH POSTING") {
                $where[] = "bc_25.status_posting='1'";
            } else {
                $where[] = "bc_25.status_posting='0'";
            }
        }

        if (!empty($condition['search'])) {
            $search = $db->escapeLikeString(trim($condition['search']));
            $searchOrderForm = "
              AND (
                    customers.name LIKE '%{$search}%'
                    OR bc_25.no_daftar LIKE '%{$search}%'
                    OR bc_25.no_aju LIKE '%{$search}%'     
                    OR bc_25.multiple_reference_no LIKE '%{$search}%'   
                )
            ";
        }


        $filterCondition = !empty($where) ? " AND " . implode(" AND ", $where) : "";

        $columns = [
            'id',
            'jenis_pengeluaran',
            'reference_penerima',
            'reference_pengeluaran_id',
            'multiple_reference_no',
            'no_aju',
            'tanggal',
            'status_posting',
        ];

        $orderBy = "";
        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $col = $columns[$orderColumnIndex];
            $dir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = " ORDER BY $col $dir ";
        }

        $baseQuery = "
        (
            -- BC 2.5
            SELECT
                bc_25.*,
                customers.name AS reference_penerima
            FROM bc_25
            LEFT JOIN customers ON customers.id = bc_25.reference_penerima_id
            WHERE bc_25.deletedAt IS NULL
            $filterCondition
            $whereDate
            $searchOrderForm
        )
        ";
        // ============================
        // 📊 COUNT + PAGINATION
        // ============================
        $countQuery = "SELECT COUNT(*) AS cnt FROM ($baseQuery) AS x";
        $totalFiltered = (int) $db->query($countQuery)->getRow()->cnt;

        $mainQuery = "
            SELECT * FROM ($baseQuery) AS x
            $orderBy
            LIMIT $limit OFFSET $offset
        ";

        $data = $db->query($mainQuery)->getResultArray();

        // ============================
        // 📦 RETURN RESULT
        // ============================

        return [
            'data'              => $data,
            'totalData'         => $totalFiltered,
            'totalFilteredData' => $totalFiltered,
            'sort'              => $orderColumnIndex,
            'sortType'          => $orderDir,
        ];
    }

    public function detail($id)
    {
        $bc25 = $this->find($id);

        if ($bc25['sales_order_lain_id'] != null) {
            $selectQry = "
                bc_25.*,
                bc_25.sales_order_lain_id as reference_id,
                sales_order_lain.no_sales_order as no_reference,
                sales_order_lain.tanggal as tanggal_reference,
                sales_order_lain.keterangan,
                customers.name AS nama_penerima,
                customers.address AS alamat_penerima,
                country.country_name,
            ";

            $result = $this->asArray()->select($selectQry)
                ->join('sales_order_lain', 'sales_order_lain.id = bc_25.sales_order_lain_id', 'left')
                ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
                ->join('country', 'country.id = customers.country_id', 'left')
                ->where('bc_25.id', $id)
                ->where('bc_25.deletedAt', null)
                ->first();
        } elseif ($bc25['pengembalian_barang_id'] != null) {
            $selectQry = "
                bc_25.*,
                bc_25.pengembalian_barang_id as reference_id,
                pengembalian_barang.no_surat_jalan as no_reference,
                pengembalian_barang.tanggal_surat_jalan as tanggal_reference,
                pengembalian_barang.keterangan,                
                suppliers.name AS nama_penerima,
                suppliers.address AS alamat_penerima
            ";

            $result = $this->asArray()->select($selectQry)
                ->join('pengembalian_barang', 'pengembalian_barang.id = bc_25.pengembalian_barang_id', 'left')
                ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
                ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                ->where('bc_25.id', $id)
                ->where('bc_25.deletedAt', null)
                ->first();
        } elseif ($bc25['sales_order_id'] != null) {
            $selectQry = "
                bc_25.*,
                bc_25.sales_order_id as reference_id,
                sales_order.no_sales_order as no_reference,
                sales_order.keterangan,
                stuffing_lokal.tanggal as tanggal_reference,
                customers.name AS nama_penerima,
                customers.address AS alamat_penerima,
            ";

            $result = $this->asArray()->select($selectQry)
                ->join('sales_order', 'sales_order.id = bc_25.sales_order_id', 'left')
                ->join('stuffing_lokal', 'stuffing_lokal.sales_order_id = sales_order.id', 'left')
                ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                ->where('bc_25.id', $id)
                ->where('bc_25.deletedAt', null)
                ->first();
        }

        return $result;
    }

    public function getListSalesOrderLain($companyId, $salesOrderLainId = null)
    {
        $metaDataModel = new MetadataModel();
        $salesOrderLainModel = new SalesOrderLainModel();

        $bcFirst = $metaDataModel->getBCFirst("BC 2.5");
        $selectQry = "
            sales_order_lain.*,
            customers.name AS nama_penerima,
            customers.address AS alamat_penerima,
            country.country_name,
            divisis.divisi,
            warehouses.warehouse_name
        ";

        $salesOrderList = $salesOrderLainModel->select($selectQry)
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id')
            ->where('status_posting', '1')
            ->where('sales_order_lain.bc_id', $bcFirst['id'])
            ->where('sales_order_lain.deletedAt', null)
            ->where('sales_order_lain.company_id', $companyId)
            ->findAll();

        $result = array();

        foreach ($salesOrderList as $s) {
            $bc25 = $this->where('sales_order_lain_id', $s['id'])->first();

            if ($salesOrderLainId != null) {
                if ($bc25 == null || $salesOrderLainId == $s['id']) {
                    array_push($result, $s);
                }
            } else {
                if ($bc25 == null) {
                    array_push($result, $s);
                }
            }
        }

        $resultData = array();
        foreach ($result as $r) {
            $resultData[] = [
                'id' => ($r['id']),
                'no_reference' => $r['no_sales_order'],
                'nama_penerima' => $r['nama_penerima'],
                'alamat_penerima' => $r['alamat_penerima'],
                'tanggal_reference' => date('d/m/Y', strtotime($r['tanggal'])),
                'keterangan' => $r['keterangan']
            ];
        }

        return $resultData;
    }

    public function getListPengembalianBarang($companyId, $pengembalianBarangId = null)
    {
        $bc41Model = new BC41Model();
        $pengembalianBarangModel = new PengembalianBarangModel();

        $selectQry = "
            pengembalian_barang.*,
            suppliers.name AS nama_penerima,
            suppliers.address AS alamat_penerima,
            divisis.divisi,
            warehouses.warehouse_name,
            penerimaan_barang.no_penerimaan_barang
        ";

        $pengeluaranBarang = $pengembalianBarangModel->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id')
            ->where('pengembalian_barang.bc_pengeluaran_id', null)
            ->where('pengembalian_barang.deletedAt', null)
            ->where('pengembalian_barang.status_post', 'FINISH')
            ->where('pengembalian_barang.company_id', $companyId)
            ->where('penerimaan_barang.status_penerimaan', "LOKAL")
            ->findAll();
        $result = array();

        foreach ($pengeluaranBarang as $s) {
            $bc25 = $this->where('pengembalian_barang_id', $s['id'])->first();
            $bc41 = $bc41Model->where('pengembalian_barang_id', $s['id'])->first();

            if ($pengembalianBarangId != null) {
                if (($bc25 == null && $bc41 == null) || $pengembalianBarangId == $s['id']) {
                    array_push($result, $s);
                }
            } else {
                if ($bc25 == null && $bc41 == null) {
                    array_push($result, $s);
                }
            }
        }

        $resultData = array();
        foreach ($result as $r) {
            $resultData[] = [
                'id' => ($r['id']),
                'no_reference' => $r['no_surat_jalan'] . " (" . $r['no_penerimaan_barang'] . ")",
                'nama_penerima' => $r['nama_penerima'],
                'alamat_penerima' => $r['alamat_penerima'],
                'tanggal_reference' => date('d/m/Y', strtotime($r['tanggal_surat_jalan'])),
                'keterangan' => $r['keterangan'],

            ];
        }

        return $resultData;
    }

    public function getListSalesOrderLokal($companyId, $salesOrderId = null)
    {
        $salesOrderLokalModel = new SalesOrderModel();
        $bc41Model = new BC41Model();
        // LOKAL
        $selectQry = "
            sales_order.id AS sales_order_id,
            sales_order.no_sales_order,
            sales_order.keterangan,
            customers.name AS nama_customer,
            customers.address AS alamat_customer,
            country.country_name,
            stuffing_lokal.tanggal,

        ";
        $salesOrder = $salesOrderLokalModel
            ->select($selectQry)
            ->join('customers', 'customers.id = sales_order.id_customer', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('stuffing_lokal', 'stuffing_lokal.sales_order_id = sales_order.id', 'left')
            ->where('used', "USED")
            ->where('sales_order.id_company', $companyId)
            ->where('sales_order.deletedAt', null)
            ->findAll();

        $result = array();
        foreach ($salesOrder as $s) {
            $bc25 = $this->where('sales_order_id', $s['sales_order_id'])->first();
            $bc41 = $bc41Model->where('sales_order_id', $s['sales_order_id'])->first();
            if ($salesOrderId != null) {
                if (($bc25 == null && $bc41 == null) || $salesOrderId == $s['sales_order_id']) {
                    array_push($result, $s);
                }
            } else {
                if ($bc25 == null && $bc41 == null) {
                    array_push($result, $s);
                }
            }
        }

        $resultData = array();
        foreach ($result as $s) {
            $resultData[] = [
                'id' => $s['sales_order_id'],
                'no_reference' => $s['no_sales_order'],
                'nama_penerima' => $s['nama_customer'],
                'alamat_penerima' => $s['alamat_customer'],
                'tanggal_reference' => date('d/m/Y', strtotime($s['tanggal'])),
                'keterangan' => $s['keterangan'],
            ];
        }

        return $resultData;
    }

    public function getListBarangSalesOrderLokal($referenceId, $typeReference)
    {
        $bc30Model = new BC30Model();
        $result = $bc30Model->getListBarang($referenceId, $typeReference);

        for ($i = 0; $i < count($result); $i++) {
            // Tulis yang diperlukan 
            $result[$i]['type_barang_text'] = $result[$i]['tipe_barang'];
            $result[$i]['tipe_barang'] = $result[$i]['tipe_barang'];
            $result[$i]['bc_type'] = $result[$i]['dokumen_asal'];
            $result[$i]['no_aju'] = $result[$i]['no_aju_warehouse'];
            $result[$i]['kode_barang'] = $result[$i]['kode_barang_internal'];
            $result[$i]['barang'] = $result[$i]['nama_barang_internal'];
            $result[$i]['divisi'] = $result[$i]['divisi'];
            $result[$i]['warehouse_name'] = $result[$i]['warehouse_name'];
            $result[$i]['satuan'] = $result[$i]['kode_satuan_internal'];
            $result[$i]['qty_konversi'] = $result[$i]['qty_keluar'];
            $result[$i]['total_harga'] = $result[$i]['harga_number'];
        }

        return $result;
    }

    public function dropdownKemasan($salesOrderLainId)
    {
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $selectQry = "
            sales_order_lain_detail.*, 
            stock.tipe_barang, 
            stock.barang2_id,
            barang_master.kode_barang,
            CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang, 
        ";

        $dataList = $salesOrderLainDetailModel
            ->select($selectQry)
            ->join('stock', 'stock.id = sales_order_lain_detail.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->where('sales_order_lain_id', $salesOrderLainId)
            ->where('stock.tipe_barang', "bahan_penolong") // KEMASAN AMBIL DARI BAHAN PENOLONG 
            ->findAll();

        return $dataList;
    }

    // BEA CUKAI FUNCTION
    public function detailBarang($bcId, $kodeBarang, $salesOrderLainId, $pengembalianBarangId, $salesOrderId)
    {
        $listBarang = $this->barang($salesOrderLainId, $pengembalianBarangId, $salesOrderId);

        $payload = json_decode($this->find($bcId)['payload']);
        $result = null;

        foreach ($listBarang as $l) {
            if ($kodeBarang == $l['kode_barang']) {
                $result = [
                    'barangDetail' => $l,
                    'bcDetail' => null
                ];
            }
        }

        foreach ($payload->barang as $b) {
            if ($b->kodeBarang == $result['barangDetail']['kode_barang']) {
                $result['bcDetail'] = $b;
            }
        }

        return $result;
    }

    public function barang($salesOrderLainId, $pengembalianBarangId, $salesOrderId)
    {
        $pengembalianBarangDetailModel = new PengembalianBarangModel();
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();


        if ($salesOrderLainId != null) {
            $detailBarang = $salesOrderLainDetailModel->detail($salesOrderLainId);
        } elseif ($pengembalianBarangId != null) {
            $detailBarang = $pengembalianBarangDetailModel->getReturBeaCukaiDetail($pengembalianBarangId);
        } else {
            $detailBarang = $this->getListBarangSalesOrderLokal($salesOrderId, "ORDER FORM LOKAL");
        }

        $result = [];

        foreach ($detailBarang as $item) {
            $key = $item['barang1_id'] . '-' . $item['kemasan_id'];
            if (!isset($result[$key])) {
                $result[$key] = $item;
                $result[$key]['total_harga'] = (int)$item['total_harga'];
                $result[$key]['qty_konversi'] = (int)$item['qty_konversi'];
            } else {
                $result[$key]['total_harga'] += (int)$item['total_harga'];
                $result[$key]['qty_konversi'] += (int)$item['qty_konversi'];
            }
        }

        return array_values($result);
    }

    public function isCompleteFormHeader($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kodeTujuanPengiriman != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormEntitas($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->entitas) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormDokumen($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->dokumen) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPengangkut($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->pengangkut) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPetiKemas($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->kemasan) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormTransaksi($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kodeValuta) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormBarang($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->barang) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPernyataan($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kotaTtd != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }
}
