<?php

namespace App\Models;

use CodeIgniter\Model;

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
            'no_penerimaan_barang'      => 'penerimaan_barang.no_penerimaan_barang',
            'warehouse_name'            => 'warehouses.warehouse_name',
            'tipe_bahan'                => 'penerimaan_barang.tipe_bahan',
            'supplier_name'             => 'suppliers.name',
            'createdAt'                 => 'penerimaan_barang.createdAt',
            'updatedAt'                 => 'penerimaan_barang.updatedAt',
            'divisi'                    => 'divisis.divisi'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_barang.*, warehouses.warehouse_name, suppliers.name as supplier_name, COUNT(penerimaan_barang_detail.id) AS itemCount, divisis.divisi";
        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'right')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->groupBy(('penerimaan_barang.id'))
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['status'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangDataQry->like('penerimaan_barang.no_penerimaan_barang', $addCondition['search']);
        }

        if ($addCondition['status']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.status_post', $addCondition['status']);
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.createdAt >=', $addCondition['startdate'] . " 00:00:00");
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.createdAt <=', $addCondition['lastdate'] . " 23:59:59");
        }

        if ($addCondition['search'] || $addCondition['status'] || $addCondition['startdate'] || $addCondition['lastdate']) {
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

    public function getPenerimaanBarangListForAccounting($condition, $addCondition, $limit = 10, $offset = 0)
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
                    bc_23.no_aju AS BC23_AJU,
                    bc_40.no_aju AS BC40_AJU,";
        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'right')
            ->join('bc_purchase_order bc_23_po', 'bc_23_po.id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order bc_40_po', 'bc_40_po.id = penerimaan_barang.id', 'left')
            ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_23_po.id', 'left')
            ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_40_po.id', 'left')
            ->groupBy(('penerimaan_barang.id'))
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangDataQry->like('penerimaan_barang.no_penerimaan_barang', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $penerimaanBarangDataQry->where('suppliers.id', $addCondition['filter']);
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

    public function getPenerimaanBarangListForPrintAccounting($condition, $addCondition)
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
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'asc'] ?? 'ASC';

        $selectQry = "penerimaan_barang.*, 
                    DATE_FORMAT(penerimaan_barang.tanggal, '%d/%m/%Y') AS tanggal_penerimaan,
                    warehouses.warehouse_name, 
                    suppliers.name as supplier_name, 
                    COUNT(penerimaan_barang_detail.id) AS itemCount, 
                    penerimaan_barang_detail.harga";
        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id', 'right')
            ->groupBy(('penerimaan_barang.id'))
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['filter'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangDataQry->like('penerimaan_barang.no_penerimaan_barang', $addCondition['search']);
        }

        if ($addCondition['filter']) {
            $penerimaanBarangDataQry->where('suppliers.id', $addCondition['filter']);
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
        $data = $penerimaanBarangDataQry->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getById($id)
    {
        $selectQry = "penerimaan_barang.*, suppliers.name as supplier_name,
        suppliers.address as supplier_address, suppliers.phone as supplier_phone, warehouses.warehouse_name,
        metadata.value as bc_type,divisis.divisi as divisi
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('metadata', 'metadata.id = penerimaan_barang.bc_type', 'left')
            ->find($id);

        return $sppData;
    }

    public function get_no($bln, $thn, $last_day, $warehouseKode, $warehouseID)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('penerimaan_barang');
        $builder->select('no_penerimaan_barang');
        $builder->orderBy('no_penerimaan_barang', 'desc');
        $builder->where('penerimaan_barang.warehouse_id', $warehouseID);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_penerimaan_barang', $lastStr);
        $query = $builder->get();

        $kode = 'LPB/' . $warehouseKode;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_penerimaan_barang']);
                $number = intval($explode[2]);

                if ($number > $lastPenerimaan) {
                    $lastPenerimaan = $number;
                }
            }
            $lastPenerimaan++;
        }

        $formattedLastPenerimaan = sprintf("%02d", $lastPenerimaan);
        $generatedNo = $kode . '/' . $formattedLastPenerimaan . '/' . $lastStr;

        return $generatedNo;
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

    public function generateLpbBB($poID, $warehouseID, $dokumenBC, $tanggalPenerimaanLPB)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $rmPurchaseOrder = new RMPurchaseOrderModel();
        $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $supplierHargaModel = new SupplierHargaModel();
        $stockDetailModel = new StockDetailModel();
        $stockModel = new StockModel();
        $stockDetail2Model = new StockDetail2Model();

        $rmDetail =  $rmPurchaseOrder->where('id', $poID)->first();
        $rmBarangDetail = $rmPurchaseOrderDetailModel->where('rm_purchase_order_id', $poID)->findAll();

        // MASUKKAN STOK BARANG DAN KEMASAN JIKA NON PABEAN 
        // (JIKA ADA BC MASUK KE INVENTORI DI MODUL BEA CUKAI)
        if ($rmDetail['bc_type'] == 0) {

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
        $lastDay = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $no = $penerimaanBarangModel->get_no(date('m'), date('Y'), $lastDay, $warehouse['code_warehouse'], $warehouseID);

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
                supplier_harga.bahan_baku_id, 
                supplier_harga.spesifikasi, 
                barang_master.barang_name,
                barang_master_spesifikasi.satuan_1,
                barang_master_spesifikasi.satuan_2,
                barang_master_spesifikasi.satuan_3,
                barang_master_spesifikasi.konversi_satuan_2,
                barang_master_spesifikasi.konversi_satuan_3,
            ";

            $barang = $supplierHargaModel->select($selectQry)
                ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = supplier_harga.spesifikasi_id', 'left')
                ->where('supplier_harga.id', $r['supplier_harga_id'])
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
        if ($penerimaanBarang['bc_type'] == 0) {
            // STOK BARANG DIINPUT
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
                    date('Y-m-d'),
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
}
