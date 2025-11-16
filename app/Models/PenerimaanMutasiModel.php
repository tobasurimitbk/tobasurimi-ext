<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanMutasiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'penerimaan_mutasi.penerimaan_mutasi_no' => 'penerimaan_mutasi.penerimaan_mutasi_no',
            'penerimaan_mutasi.multiple_no_mutasi' => 'penerimaan_mutasi.multiple_no_mutasi',
            'penerimaan_mutasi.tanggal'            => 'penerimaan_mutasi.tanggal',
            'penerimaan_mutasi.divisi_id'          => 'penerimaan_mutasi.divisi_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'penerimaan_mutasi_no'] ?? 'penerimaan_mutasi_no';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi.*,
        divisis.divisi AS divisi,
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if (
            $addCondition['search'] ||
            $addCondition['dateStart'] ||
            $addCondition['dateEnd']
        ) {
            $dataQry->groupStart();
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search']) {
            $dataQry->like('multiple_no_mutasi', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('penerimaan_mutasi_no', $addCondition['search']);
        }

        if (
            $addCondition['search'] ||
            $addCondition['dateStart'] ||
            $addCondition['dateEnd']
        ) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getListWarehouse($bcID, $divisiTujuanID)
    {
        $mutasiModel = new MutasiModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $warehouseModel = new WarehousesModel();

        $listMutasi = $mutasiModel
            ->select('mutasi.id, divisi_tujuan_id, warehouse_tujuan_id, SUM(qty) AS qty_mutasi')
            ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
            ->whereIn('divisi_tujuan_id', $divisiTujuanID)
            ->where('mutasi.deletedAt', null)
            ->where('mutasi_detail.deletedAt', null)
            ->where('mutasi.bc_id', $bcID)
            ->groupBy('mutasi_detail.mutasi_id')
            ->findAll();

        $warehouseResult = [];

        foreach ($listMutasi as $mutasi) {
            $penerimaanTotal = $penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $mutasi['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_id')
                ->findAll();

            if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $mutasi['qty_mutasi']) {
                $warehouseResult[] = $mutasi['warehouse_tujuan_id'];
            }
        }

        if (count($warehouseResult) == 0) {
            return [];
        } else {
            $warehouseList = $warehouseModel
                ->select('warehouses.id, warehouses.divisi_id, warehouses.warehouse_name, divisis.divisi')
                ->join('divisis', 'divisis.id = warehouses.divisi_id')
                ->whereIn('warehouses.id', $warehouseResult)
                ->where('warehouses.deletedAt', null)
                ->where('divisis.deletedAt', null)
                ->orderBy('divisis.divisi', "ASC")
                ->findAll();

            return $warehouseList;
        }
    }

    public function getListNomorMutasi(
        $divisiId,
        $tipeMutasi
    ) {
        $mutasiModel = new MutasiModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $ppbkbModel = new PPBKBModel();


        $listMutasi = $mutasiModel
            ->select('mutasi.id, mutasi.no_mutasi, SUM(qty_konversi) AS qty_konversi')
            ->join('mutasi_detail', 'mutasi_detail.mutasi_id = mutasi.id', 'left')
            ->where('mutasi.tipe_mutasi', $tipeMutasi)
            ->where('mutasi.divisi_tujuan_id', $divisiId)
            ->where('mutasi.deletedAt', null)
            ->where('mutasi_detail.deletedAt', null)
            ->groupBy('mutasi_detail.mutasi_id')
            ->orderBy('mutasi.no_mutasi', "ASC")
            ->findAll();

        $mutasiResult = [];

        foreach ($listMutasi as $mutasi) {

            // Cek Total Diterima
            $penerimaanTotal = $penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $mutasi['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_id')
                ->findAll();

            if ($tipeMutasi == "PPBKB") {
                // PPBKB
                $checkPPBKB = $ppbkbModel->where('mutasi_id', $mutasi['id'])->first();
                if ($checkPPBKB) {
                    if (count($penerimaanTotal) == 0) {
                        array_push($mutasiResult, $mutasi);
                    } else {
                        if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $mutasi['qty_konversi']) {
                            array_push($mutasiResult, $mutasi);
                        }
                    }
                }
            } else {
                // LOKAL
                if (count($penerimaanTotal) == 0) {
                    array_push($mutasiResult, $mutasi);
                } else {
                    if ($penerimaanTotal[0]['qty_diterima'] < $mutasi['qty_konversi']) {
                        array_push($mutasiResult, $mutasi);
                    }
                }
            }
        }

        return $mutasiResult;
    }

    public function getListBarangMutasi(
        $mutasiArrID,
        $penerimaanMutasiID = null,
        $isEdit = false
    ) {
        $mutasiDetailModel = new MutasiDetailModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $stockRevampModel = new StockRevampModel();

        $selectQry = "
            mutasi_detail.*, 
            mutasi.no_mutasi,
            tb_divisi_asal.divisi AS divisi_asal,
            tb_divisi_tujuan.divisi AS divisi_tujuan,
            tb_warehouses_asal.warehouse_name AS warehouse_asal,
            tb_warehouses_tujuan.warehouse_name AS warehouse_tujuan,
            satuans.kode_satuan AS satuan_konversi
        ";

        $mutasiDetailList = $mutasiDetailModel
            ->select($selectQry)
            ->join('mutasi', 'mutasi.id = mutasi_detail.mutasi_id', 'left')
            ->join('divisis tb_divisi_asal', 'tb_divisi_asal.id = mutasi.divisi_asal_id', 'left')
            ->join('divisis tb_divisi_tujuan', 'tb_divisi_tujuan.id = mutasi.divisi_tujuan_id', 'left')
            ->join('warehouses tb_warehouses_asal', 'tb_warehouses_asal.id = mutasi.warehouse_asal_id', 'left')
            ->join('warehouses tb_warehouses_tujuan', 'tb_warehouses_tujuan.id = mutasi.warehouse_tujuan_id', 'left')
            ->join('satuans', 'satuans.id = mutasi_detail.unit_id_konversi', 'left')
            ->whereIn('mutasi_id', $mutasiArrID)
            ->where('mutasi_detail.deletedAt', null)
            ->findAll();

        $barangResult = [];
        $no = 1;
        foreach ($mutasiDetailList as $m) {

            $penerimaanTotalQty = $penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $m['mutasi_id'])
                ->where('mutasi_detail_id', $m['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_detail_id')
                ->first();

            $penerimaanTotalCurrentQty = $penerimaanMutasiDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_id', $m['mutasi_id'])
                ->where('mutasi_detail_id', $m['id'])
                ->where('penerimaan_mutasi_id', $penerimaanMutasiID)
                ->where('deletedAt', null)
                ->groupBy('mutasi_detail_id')
                ->first();

            $qtyDiterimaTotal = $penerimaanTotalQty == null ? 0 : $penerimaanTotalQty['qty_diterima'];
            $qtySisaMutasi = $m['qty_konversi'] - $qtyDiterimaTotal;
            $qtyDiterimaSekarang =  $penerimaanTotalCurrentQty == null ? 0 : $penerimaanTotalCurrentQty['qty_diterima'];


            $fromStock = $stockRevampModel->getStockListAll(
                ["id" => $m['stock_detail_id']],
                0,
                "desc",
                1
            );

            foreach ($fromStock['data'] as $d) {

                if ($isEdit && $penerimaanTotalCurrentQty != null) {
                    // INI TAMPILAN EDIT
                    array_push($barangResult, [
                        'no' => $no++,
                        'mutasi_id' => $m['mutasi_id'],
                        'mutasi_detail_id' => $m['id'],
                        'divisi_asal' => $m['divisi_asal'],
                        'divisi_tujuan' => $m['divisi_tujuan'],
                        'warehouse_asal' => $m['warehouse_asal'],
                        'warehouse_tujuan' => $m['warehouse_tujuan'],
                        'no_mutasi' => $m['no_mutasi'],
                        'supplier_name' => $d['supplier_name'],
                        'kode_barang' => $d['kode_barang'],
                        'barang_name' => $d['barang_name'],
                        'spesifikasi' => $d['spesifikasi'],
                        'qty_sisa_mutasi' => (float)$qtySisaMutasi,
                        'qty_diterima_sekarang' => (float)$qtyDiterimaSekarang,
                        'satuan_mutasi' => $m['satuan_konversi'],
                        "qty_diterima_total" => (float)$qtyDiterimaTotal,
                        'satuan_diterima' => $m['satuan_konversi']
                    ]);
                } else if (!$isEdit && $qtySisaMutasi != 0) {
                    // INI TAMPILAN CREATE
                    array_push($barangResult, [
                        'no' => $no++,
                        'mutasi_id' => $m['mutasi_id'],
                        'mutasi_detail_id' => $m['id'],
                        'divisi_asal' => $m['divisi_asal'],
                        'divisi_tujuan' => $m['divisi_tujuan'],
                        'warehouse_asal' => $m['warehouse_asal'],
                        'warehouse_tujuan' => $m['warehouse_tujuan'],
                        'no_mutasi' => $m['no_mutasi'],
                        'supplier_name' => $d['supplier_name'],
                        'kode_barang' => $d['kode_barang'],
                        'barang_name' => $d['barang_name'],
                        'spesifikasi' => $d['spesifikasi'],
                        'qty_sisa_mutasi' => (float)$qtySisaMutasi,
                        'qty_diterima_sekarang' => (float)$qtyDiterimaSekarang,
                        'satuan_mutasi' => $m['satuan_konversi'],
                        "qty_diterima_total" => (float)$qtyDiterimaTotal,
                        'satuan_diterima' => $m['satuan_konversi']
                    ]);
                }
            }
        }

        return $barangResult;
    }

    public function getMutasiNo($mutasiIDArr)
    {
        $mutasiModel = new MutasiModel();
        $result = $mutasiModel->whereIn('id', $mutasiIDArr)->findAll();
        $response = array();
        foreach ($result as $r) {
            array_push($response, $r['no_mutasi']);
        }
        return $response;
    }

    public function get_no(
        $month,
        $year,
        $companyId,
        $tipe_mutasi
    ) {
        $romanMonth = romanMonthNumber((int)$month);
        // Tentukan template berdasarkan company
        if ($tipe_mutasi == "PPBKB") {
            // PENERIMAAN MUTASI PPBKB
            switch ($companyId) {
                case 1: // KIM 1 (FRZ)
                    $numberTemplate = "/F/PM/PPBKB/$romanMonth/" . substr($year, -2);
                    break;
                case 2: // KIM 2
                    $numberTemplate = "/PM/PPBKB/$romanMonth/" . substr($year, -2);
                    break;
                case 15: // GLOBAL
                    $numberTemplate = "/G/PM/PPBKB/$romanMonth/" . substr($year, -2);
                    break;
                default: // OCS atau lainnya
                    $numberTemplate = "/PM/PPBKB/$romanMonth/" . substr($year, -2);
                    break;
            }
        } else {
            // PENERIMAAN MUTASI LOKAL
            switch ($companyId) {
                case 1: // KIM 1 (FRZ)
                    $numberTemplate = "/F/PM/$romanMonth/" . substr($year, -2);
                    break;
                case 2: // KIM 2
                    $numberTemplate = "/PM/$romanMonth/" . substr($year, -2);
                    break;
                case 15: // GLOBAL
                    $numberTemplate = "/G/PM/$romanMonth/" . substr($year, -2);
                    break;
                default: // OCS atau lainnya
                    $numberTemplate = "/PM/$romanMonth/" . substr($year, -2);
                    break;
            }
        }


        // Cari nomor terakhir berdasarkan template
        $lastData = $this->asArray()
            ->select('penerimaan_mutasi_no')
            ->where('company_id', $companyId)
            ->like('penerimaan_mutasi_no', $numberTemplate, 'before')
            ->where('deletedAt', null)
            ->where('tipe_mutasi', $tipe_mutasi)
            ->orderBy('penerimaan_mutasi_no', 'DESC')
            ->first();

        // Nomor awal default
        $invNumber = '001' . $numberTemplate;

        if ($lastData && !empty($lastData['penerimaan_mutasi_no'])) {
            // Ambil angka urutan terakhir
            $parts = explode('/', $lastData['penerimaan_mutasi_no']);
            $lastIncrement = isset($parts[0]) ? (int)$parts[0] : 0;
            $newIncrement = $lastIncrement + 1;
            $paddedNumber = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }
        return $invNumber;
    }

    public function getPenerimaanBarangListReportPPBKB($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_ppbkb'      => 'ppbkb.no_ppbkb'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_mutasi.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi.penerimaan_mutasi_no,
        penerimaan_mutasi.multiple_mutasi_id,
        penerimaan_mutasi_detail.stock_asal_id as stock_id,
        penerimaan_mutasi.tanggal,
        penerimaan_mutasi_detail.mutasi_id,

        mutasi_detail.stock_id AS stock_id_asal,
        mutasi_detail.bc_id AS bc_id_asal,
        mutasi_detail.no_aju AS no_aju_asal,
        mutasi_detail.stock_dokumen AS stock_dokumen_asal,
        
        divisis.divisi AS divisi_penerima,
           
        
        mutasi_detail.qty AS qty,    
        penerimaan_mutasi_detail.qty AS jml_masuk,

        mutasi.tanggal AS tanggal_bc, 

        ppbkb.no_ppbkb AS no_ppbkb,    
        ppbkb.no_daftar AS no_daftar,    
          
        stock.tipe_barang,    
        stock.barang1_id,    
        stock.barang2_id,    
        stock.kemasan_id,    
          
        ";

        $penerimaanMutasi = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->join('penerimaan_mutasi_detail', 'penerimaan_mutasi_detail.penerimaan_mutasi_id = penerimaan_mutasi.id', 'left')
            ->join('mutasi', 'mutasi.id = penerimaan_mutasi_detail.mutasi_id', 'left')
            ->join('mutasi_detail', 'mutasi_detail.id = penerimaan_mutasi_detail.mutasi_detail_id', 'left')
            ->join('ppbkb', 'ppbkb.mutasi_id = penerimaan_mutasi_detail.mutasi_id', 'left')
            ->join('stock', 'stock.id = mutasi_detail.stock_id', 'left')

            ->where('penerimaan_mutasi.company_id', $addCondition['company_id'])

            ->where('penerimaan_mutasi.deletedAt', null)
            ->where('penerimaan_mutasi_detail.deletedAt', null)
            ->where('penerimaan_mutasi.status_posting', '1')

            ->orderBy($sort, $sortType);

        $totalData = $penerimaanMutasi->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasi->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanMutasi->where('mutasi.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanMutasi->where('mutasi.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasi->groupEnd();
        }

        $totalFilteredData = $penerimaanMutasi->countAllResults(false);
        $data = $penerimaanMutasi->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportPPBKBPDF($addCondition)
    {
        $availableSort = [
            'no_ppbkb'      => 'ppbkb.no_ppbkb'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_mutasi.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi.penerimaan_mutasi_no,
        penerimaan_mutasi.multiple_mutasi_id,
        penerimaan_mutasi_detail.stock_asal_id as stock_id,
        penerimaan_mutasi.tanggal,
        penerimaan_mutasi_detail.mutasi_id,

        mutasi_detail.stock_id AS stock_id_asal,
        mutasi_detail.bc_id AS bc_id_asal,
        mutasi_detail.no_aju AS no_aju_asal,
        mutasi_detail.stock_dokumen AS stock_dokumen_asal,
        
        divisis.divisi AS divisi_penerima,
           
        
        mutasi_detail.qty AS qty,    
        penerimaan_mutasi_detail.qty AS jml_masuk,

        mutasi.tanggal AS tanggal_bc, 

        ppbkb.no_ppbkb AS no_ppbkb,    
        ppbkb.no_daftar AS no_daftar,    
          
        stock.tipe_barang,    
        stock.barang1_id,    
        stock.barang2_id,    
        stock.kemasan_id,    
          
        ";

        $penerimaanMutasi = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->join('penerimaan_mutasi_detail', 'penerimaan_mutasi_detail.penerimaan_mutasi_id = penerimaan_mutasi.id', 'left')
            ->join('mutasi', 'mutasi.id = penerimaan_mutasi_detail.mutasi_id', 'left')
            ->join('mutasi_detail', 'mutasi_detail.id = penerimaan_mutasi_detail.mutasi_detail_id', 'left')
            ->join('ppbkb', 'ppbkb.mutasi_id = penerimaan_mutasi_detail.mutasi_id', 'left')
            ->join('stock', 'stock.id = mutasi_detail.stock_id', 'left')

            ->where('penerimaan_mutasi.company_id', $addCondition['company_id'])

            ->where('penerimaan_mutasi.deletedAt', null)
            ->where('penerimaan_mutasi_detail.deletedAt', null)
            ->where('penerimaan_mutasi.status_posting', '1')

            ->orderBy($sort, $sortType);

        $totalData = $penerimaanMutasi->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasi->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanMutasi->where('mutasi.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanMutasi->where('mutasi.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasi->groupEnd();
        }

        $totalFilteredData = $penerimaanMutasi->countAllResults(false);
        $data = $penerimaanMutasi->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function posting($id, $db)
    {
        $stockRevampModel = new StockRevampModel();
        $stockRevampDetailModel = new StockRevampDetailModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();

        $penerimaanMutasi = $this->asArray()->where('id', $id)->first();
        $penerimaanMutasiDetail = $penerimaanMutasiDetailModel
            ->select('penerimaan_mutasi_detail.*,mutasi_detail.stock_detail_id')
            ->join('mutasi_detail', 'mutasi_detail.id = penerimaan_mutasi_detail.mutasi_detail_id', 'left')
            ->where('penerimaan_mutasi_detail.penerimaan_mutasi_id', $id)
            ->where('penerimaan_mutasi_detail.deletedAt', null)
            ->findAll();

        foreach ($penerimaanMutasiDetail as $p) {
            $stockDetail = $stockRevampDetailModel->where('id', $p['stock_detail_id'])->first();
            $stock = $stockRevampModel->where('id', $stockDetail['stock_id'])->first();

            $payload = [
                'company_id'        => $penerimaanMutasi['company_id'],
                'barang_master_id'  => $stock['barang_master_id'],
                'spesifikasi_id'    => $stock['spesifikasi_id'],
                'unit_id'           => $stock['unit_id'],
                'divisi_id'         => $stock['divisi_id'],
                'warehouse_id'      => $stock['warehouse_id'],
                'qty_bersih'        => $p['qty'],
                'qty_diterima'      => $p['qty'],
                'bc_id'             => $penerimaanMutasi['tipe_mutasi'] == "LOKAL" ? 0 : 1426,
                'type_bc'           => $penerimaanMutasi['tipe_mutasi'] == "LOKAL" ? "NON PABEAN" : "PPBKB",
                'reference_id'      => $penerimaanMutasi['id'],
                'po_type'           => null,
                'po_id'             => null,
                'reference_type'    => 'PENERIMAAN MUTASI',
                'status'            => 'IN',
                'keterangan'        => $penerimaanMutasi['penerimaan_mutasi_no']
            ];

            $stockDetailId =  $stockRevampModel->insertStockRevamp(
                $db,
                $payload
            );

            $penerimaanMutasiDetailModel->update($p['id'], ['stock_detail_id' => $stockDetailId]);
        }
    }

    public function unposting($id)
    {
        $stockRevampDetailModel = new StockRevampDetailModel();
        $penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $stockRevampModel = new StockRevampModel();
        $stockRevampLogModel = new StockRevampLogModel();

        $penerimaanMutasiDetail = $penerimaanMutasiDetailModel
            ->where('penerimaan_mutasi_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        $isFailed = false;

        foreach ($penerimaanMutasiDetail as $p) {
            $stockDetail = $stockRevampDetailModel->where('id', $p['stock_detail_id'])->first();

            if ($stockDetail['qty_diterima'] != $p['qty']) {
                $isFailed = true;
                break;
            }
        }

        if ($isFailed) {
            return false;
        }

        foreach ($penerimaanMutasiDetail as $p) {
            $stockDetail = $stockRevampDetailModel->where('id', $p['stock_detail_id'])->first();

            if ($stockDetail['qty_diterima'] != $p['qty']) {
                $isFailed = true;
                break;
            }

            $stock = $stockRevampModel->where('id', $stockDetail['stock_id'])->first();
            if ($stock) {
                $qtyNow = $stock['qty_bersih'] - $p['qty'];
                $stockRevampModel->update($stock['id'], ['qty_bersih' => $qtyNow, 'qty_diterima' => $qtyNow]);
            }

            $stockRevampDetailModel->delete($stockDetail['id'], true);
            $stockRevampLogModel->where('stock_detail_id', $stockDetail['id'])->delete(null, true);
        }
        return true;
    }
}
