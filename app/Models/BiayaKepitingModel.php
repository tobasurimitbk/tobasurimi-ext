<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaKepitingModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_kepiting';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
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

    public function getList($condition, $conditionArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_pembayaran' => 'no_pembayaran',
            'tanggal' => 'tanggal',
            'divisi_id' => 'divisi_id',
            'warehouse_id' => 'warehouse_id',
            'vendor_id' => 'vendor_id',
            'no_pembayaran' => 'no_pembayaran',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "biaya_kepiting.*,
        divisis.divisi,
        warehouses.warehouse_name,
        vendors.name as vendor_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = biaya_kepiting.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = biaya_kepiting.warehouse_id', 'left')
            ->join('vendors', 'vendors.id = biaya_kepiting.vendor_id', 'left')
            ->where($condition)
            ->whereIn('biaya_kepiting.divisi_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_pembayaran'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('biaya_kepiting.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->like('biaya_kepiting.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['no_pembayaran']) {
            $dataQry->like('no_pembayaran', $addCondition['no_pembayaran']);
        }

        if ($addCondition['start_date']) {
            $dataQry->where('tanggal >=', $addCondition['start_date']);
        }

        if ($addCondition['end_date']) {
            $dataQry->where('tanggal <=', $addCondition['end_date']);
        }

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_pembayaran'] || $addCondition['start_date'] || $addCondition['end_date']) {
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

    public function dropdownJasaVendorIn()
    {
        $divisiModel = new DivisisModel();
        $biayaUdangModel = new BiayaUdangModel();
        $divisiArr = array();
        $result = array();

        foreach ($divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $resultBiayaKepiting = $this
            ->select('jasa_vendor_in.*,divisis.divisi,vendors.name')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = biaya_kepiting.jasa_vendor_in_id', 'right')
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where('biaya_kepiting.jasa_vendor_in_id', null)
            ->where('jasa_vendor_in.status_bayar', '1')
            ->whereIn('jasa_vendor_in.divisi_id', $divisiArr)
            ->findAll();


        foreach ($resultBiayaKepiting as $r) {
            $checkBiayaUdang = $biayaUdangModel->like('multiple_jasa_vendor_in_id', $r['id'])->first();
            if ($checkBiayaUdang == null) {
                array_push($result, $r);
            }
        }

        return $result;
    }

    public function dropdownJasaVendorKepitingKukusIn()
    {
        $divisiModel = new DivisisModel();
        $biayaUdangModel = new BiayaUdangModel();
        $divisiArr = array();
        $result = array();

        foreach ($divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $resultBiayaKepiting = $this
            ->select('jasa_vendor_in_kepiting_kukus.*,divisis.divisi,vendors.name')
            ->join('jasa_vendor_in_kepiting_kukus', 'jasa_vendor_in_kepiting_kukus.id = biaya_kepiting.jasa_vendor_in_kepiting_kukus_id', 'right')
            ->join('divisis', 'divisis.id = jasa_vendor_in_kepiting_kukus.divisi_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in_kepiting_kukus.vendor_id', 'left')
            ->where('biaya_kepiting.jasa_vendor_in_kepiting_kukus_id', null)
            ->where('jasa_vendor_in_kepiting_kukus.status_posting', '1')
            ->where('jasa_vendor_in_kepiting_kukus.deletedAt', null)
            ->whereIn('jasa_vendor_in_kepiting_kukus.divisi_id', $divisiArr)
            ->findAll();

        foreach ($resultBiayaKepiting as $r) {
            $checkBiayaUdang = $biayaUdangModel->like('multiple_jasa_vendor_in_id', $r['id'])->first();
            if ($checkBiayaUdang == null) {
                array_push($result, $r);
            }
        }

        return $result;
    }

    public function dropdownBarang($jasaVendorInID, $id = null)
    {
        $jasaVendorInModel = new JasaVendorInModel();
        $jasaVendorOutModel = new JasaVendorOutModel();
        $biayaKepitingDetailModel = new BiayaKepitingDetailModel();

        // CARI JASA VENDOR OUT ID
        $jasaVendorIn = $jasaVendorInModel->find($jasaVendorInID);

        $jasaVendorOutIdArr = json_decode($jasaVendorIn['multiple_jasa_vendor_out_id']);
        // var_dump($jasaVendorOutIdArr);
        // die;
        // CREATE
        $selectQryJasaVendorOut = "
            barang_master.id AS barang_master_id,
            barang_master_spesifikasi.id AS barang_master_spesifikasi_id,
            jasa_vendor_out.tanggal AS tanggal_keluar,
            SUM(jasa_vendor_out_detail.qty) as qty_kopek,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS nama_barang
        ";

        $jasaVendorOutDetail = $jasaVendorOutModel
            ->select($selectQryJasaVendorOut)
            ->join('jasa_vendor_out_detail', 'jasa_vendor_out_detail.jasa_vendor_out_id = jasa_vendor_out.id')
            ->join('stock_details2', 'stock_details2.id = jasa_vendor_out_detail.stock_out_id', 'left')
            ->join('stock_details', 'stock_details.id = stock_details2.stock_detail_id', 'left')
            ->join('stock', 'stock.id = stock_details.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->whereIn('jasa_vendor_out.id', $jasaVendorOutIdArr)
            ->where('jasa_vendor_out_detail.deletedAt', null)
            ->groupBy('jasa_vendor_out_detail.stock_out_id')
            ->findAll();

        for ($i = 0; $i < count($jasaVendorOutDetail); $i++) {
            $jasaVendorOutDetail[$i]['tanggal_masuk'] = date('d/m/Y', strtotime($jasaVendorIn['tanggal']));
            $jasaVendorOutDetail[$i]['tanggal_keluar'] = date('d/m/Y', strtotime($jasaVendorOutDetail[$i]['tanggal_keluar']));

            if ($id != null) {
                $biayaKepitingDetail = $biayaKepitingDetailModel
                    ->where('biaya_kepiting_id', $id)
                    ->where('jasa_vendor_in_id', $jasaVendorInID)
                    ->where('barang_master_id', $jasaVendorOutDetail[$i]['barang_master_id'])
                    ->where('barang_master_spesifikasi_id', $jasaVendorOutDetail[$i]['barang_master_spesifikasi_id'])
                    ->first();

                $jasaVendorOutDetail[$i]['jumbo'] = $biayaKepitingDetail['jumbo'];
                $jasaVendorOutDetail[$i]['ex_lump'] = $biayaKepitingDetail['ex_lump'];
                $jasaVendorOutDetail[$i]['lump'] = $biayaKepitingDetail['lump'];
                $jasaVendorOutDetail[$i]['special'] = $biayaKepitingDetail['special'];
                $jasaVendorOutDetail[$i]['claw'] = $biayaKepitingDetail['claw'];
                $jasaVendorOutDetail[$i]['mh'] = $biayaKepitingDetail['mh'];
                $jasaVendorOutDetail[$i]['cf'] = $biayaKepitingDetail['cf'];
            } else {

                $jasaVendorOutDetail[$i]['jumbo'] = 0;
                $jasaVendorOutDetail[$i]['ex_lump'] = 0;
                $jasaVendorOutDetail[$i]['lump'] = 0;
                $jasaVendorOutDetail[$i]['special'] = 0;
                $jasaVendorOutDetail[$i]['claw'] = 0;
                $jasaVendorOutDetail[$i]['mh'] = 0;
                $jasaVendorOutDetail[$i]['cf'] = 0;
            }
        }

        return $jasaVendorOutDetail;
    }

    public function dropdownBarangKepitingKukus($jasaVendorInID, $id = null)
    {
        $jasaVendorInKepitingKukusModel   = new JasaVendorInKepitingKukusModel();
        $jasaVendorInDetailModel          = new JasaVendorInKepitingKukusDetailModel();
        $jasaVendorOutDetailModel         = new JasaVendorOutKepitingKukusDetailModel(); 
        $biayaKepitingDetailModel         = new BiayaKepitingDetailModel();

        // Pastikan array (bisa single atau multiple)
        if (!is_array($jasaVendorInID)) {
            $jasaVendorInID = [$jasaVendorInID];
        }

        // 🔹 Ambil semua header IN (buat tanggal masuk)
        $jasaVendorInList = $jasaVendorInKepitingKukusModel
            ->whereIn('id', $jasaVendorInID)
            ->findAll();

        // 🔹 Ambil semua OUT detail yang punya relasi ke salah satu IN
        $outDetails = $jasaVendorOutDetailModel
            ->select('
                jasa_vendor_out_kepiting_kukus_detail.id,
                jasa_vendor_out_kepiting_kukus_detail.supplier_id,
                jasa_vendor_out_kepiting_kukus_detail.keterangan,
                suppliers.name AS supplier_name,
                MIN(jasa_vendor_out_kepiting_kukus.tanggal) AS tanggal_keluar,
                SUM(jasa_vendor_out_kepiting_kukus_detail.qty) AS qty_kopek,
                barang_master.id AS barang_master_id,
                barang_master_spesifikasi.id AS spesifikasi_id,
                jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_in_kepiting_kukus_id AS jasa_vendor_in_id
            ')
            ->join('jasa_vendor_out_kepiting_kukus', 'jasa_vendor_out_kepiting_kukus.id = jasa_vendor_out_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_id')
            ->join('suppliers', 'suppliers.id = jasa_vendor_out_kepiting_kukus_detail.supplier_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = jasa_vendor_out_kepiting_kukus_detail.spesifikasi_id', 'left')
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
            ->join('jasa_vendor_in_kepiting_kukus_detail', 'jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_detail_id = jasa_vendor_out_kepiting_kukus_detail.id', 'inner')
            ->whereIn('jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_in_kepiting_kukus_id', $jasaVendorInID)
            ->where('jasa_vendor_out_kepiting_kukus_detail.deletedAt', null)
            ->groupBy('
                jasa_vendor_out_kepiting_kukus_detail.supplier_id,
                jasa_vendor_out_kepiting_kukus_detail.keterangan,
                suppliers.name,
                jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_in_kepiting_kukus_id
            ')
            ->findAll();

        $grouped = [];
        $allSpek = [];

        foreach ($outDetails as $outRow) {
            $groupKey = $outRow['supplier_id'].'-'.$outRow['keterangan'].'-'.$outRow['jasa_vendor_in_id'];

            // Ambil tanggal masuk dari IN yang sesuai
            $tanggalMasuk = '-';
            foreach ($jasaVendorInList as $in) {
                if ($in['id'] == $outRow['jasa_vendor_in_id']) {
                    $tanggalMasuk = date('d/m/Y', strtotime($in['tanggal']));
                    break;
                }
            }

            // 🔹 ambil IN detail hanya untuk kombinasi supplier + keterangan + IN ini
            $inDetails = $jasaVendorInDetailModel
                ->select('
                    jasa_vendor_in_kepiting_kukus_detail.qty_kotor AS qty,
                    barang_master_spesifikasi.spesifikasi
                ')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = jasa_vendor_in_kepiting_kukus_detail.spesifikasi_in_id')
                ->where('jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_detail_id', $outRow['id'])
                ->where('jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_in_kepiting_kukus_id', $outRow['jasa_vendor_in_id'])
                ->where('jasa_vendor_in_kepiting_kukus_detail.deletedAt', null)
                ->findAll();

            $grouped[$groupKey] = [
                'supplier'         => $outRow['supplier_name'],
                'supplier_id'      => $outRow['supplier_id'],
                'barang_master_id' => $outRow['barang_master_id'],
                'keterangan'       => $outRow['keterangan'],
                'jasa_vendor_in_id'=> $outRow['jasa_vendor_in_id'],
                'qty_sebelum_kopek'=> $outRow['qty_kopek'] ?? 0,
                'tanggal_masuk'    => $tanggalMasuk,
                'tanggal_keluar'   => date('d/m/Y', strtotime($outRow['tanggal_keluar'])),
                'spek'             => []
            ];

            foreach ($inDetails as $inRow) {
                $spekName = strtoupper($inRow['spesifikasi']);
                $allSpek[$spekName] = $spekName;

                if (!isset($grouped[$groupKey]['spek'][$spekName])) {
                    $grouped[$groupKey]['spek'][$spekName] = 0;
                }

                $grouped[$groupKey]['spek'][$spekName] += $inRow['qty'];
            }

            // 🔹 Kalau edit mode → overwrite isi spek
            if (!empty($id)) {
                $idList = is_array($id) ? $id : [$id];

                $biayaDetail = $biayaKepitingDetailModel
                    ->whereIn('biaya_kepiting_id', $idList)
                    ->where('jasa_vendor_in_id', $outRow['jasa_vendor_in_id'])
                    ->where('supplier_id', $outRow['supplier_id'])
                    ->where('keterangan', $outRow['keterangan'])
                    ->first();

                if ($biayaDetail) {
                    foreach ($allSpek as $spekName) {
                        $grouped[$groupKey]['spek'][$spekName] =
                            $biayaDetail[strtolower($spekName)] ?? ($grouped[$groupKey]['spek'][$spekName] ?? 0);
                    }
                }
            }
        }

        return [
            'thead' => array_values($allSpek),
            'data'  => array_values($grouped)
        ];
    }


    public function dropdownBarangPrint($jasaVendorInID, $id = null)
    {
        $jasaVendorInModel = new JasaVendorInModel();
        $jasaVendorOutModel = new JasaVendorOutModel();
        $biayaKepitingDetailModel = new BiayaKepitingDetailModel();
        $stockDetail2Model = new StockDetail2Model();

        // CARI JASA VENDOR OUT ID
        $jasaVendorIn = $jasaVendorInModel->find($jasaVendorInID);

        $jasaVendorOutIdArr = json_decode($jasaVendorIn['multiple_jasa_vendor_out_id']);

        // CREATE
        $selectQryJasaVendorOut = "
            barang_master.id AS barang_master_id,
            barang_master_spesifikasi.id AS barang_master_spesifikasi_id,
            jasa_vendor_out.tanggal AS tanggal_keluar,
            SUM(jasa_vendor_out_detail.qty) as qty_kopek,
            jasa_vendor_out_detail.jasa_vendor_out_id,
            jasa_vendor_out_detail.stock_out_id,
            jasa_vendor_out_detail.bc_out_id,
            jasa_vendor_out_detail.no_aju_out,
            jasa_vendor_out_detail.stock_dokumen,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS nama_barang
        ";

        $jasaVendorOutDetail = $jasaVendorOutModel
            ->select($selectQryJasaVendorOut)
            ->join('jasa_vendor_out_detail', 'jasa_vendor_out_detail.jasa_vendor_out_id = jasa_vendor_out.id')
            ->join('stock', 'stock.id = jasa_vendor_out_detail.stock_out_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->whereIn('jasa_vendor_out.id', $jasaVendorOutIdArr)
            ->where('jasa_vendor_out_detail.deletedAt', null)
            ->groupBy('jasa_vendor_out_detail.stock_out_id')
            ->findAll();

        for ($i = 0; $i < count($jasaVendorOutDetail); $i++) {
            $jasaVendorOutDetail[$i]['tanggal_masuk'] = date('d/m/Y', strtotime($jasaVendorIn['tanggal']));
            $jasaVendorOutDetail[$i]['tanggal_keluar'] = date('d/m/Y', strtotime($jasaVendorOutDetail[$i]['tanggal_keluar']));
            $jasaVendorOutDetail[$i]['supplier_name'] = "";

            $stockDetailList = $stockDetail2Model->getStockListDetail(
                $jasaVendorOutDetail[$i]['stock_out_id'],
                $jasaVendorOutDetail[$i]['bc_out_id'],
                $jasaVendorOutDetail[$i]['no_aju_out'],
                $jasaVendorOutDetail[$i]['stock_dokumen'],
            );

            $jasaVendorOutDetail[$i]['supplier_name'] = $stockDetailList == null ? "-" : $stockDetailList['supplier_name'];

            if ($id != null) {
                $biayaKepitingDetail = $biayaKepitingDetailModel
                    ->where('biaya_kepiting_id', $id)
                    // ->where('jasa_vendor_in_id', $jasaVendorInID)
                    // ->where('barang_master_id', $jasaVendorOutDetail[$i]['barang_master_id'])
                    // ->where('barang_master_spesifikasi_id', $jasaVendorOutDetail[$i]['barang_master_spesifikasi_id'])
                    ->first();

                $jasaVendorOutDetail[$i]['jumbo'] = $biayaKepitingDetail['jumbo'];
                $jasaVendorOutDetail[$i]['ex_lump'] = $biayaKepitingDetail['ex_lump'];
                $jasaVendorOutDetail[$i]['lump'] = $biayaKepitingDetail['lump'];
                $jasaVendorOutDetail[$i]['special'] = $biayaKepitingDetail['special'];
                $jasaVendorOutDetail[$i]['claw'] = $biayaKepitingDetail['claw'];
                $jasaVendorOutDetail[$i]['mh'] = $biayaKepitingDetail['mh'];
                $jasaVendorOutDetail[$i]['cf'] = $biayaKepitingDetail['cf'];
            } else {

                $jasaVendorOutDetail[$i]['jumbo'] = 0;
                $jasaVendorOutDetail[$i]['ex_lump'] = 0;
                $jasaVendorOutDetail[$i]['lump'] = 0;
                $jasaVendorOutDetail[$i]['special'] = 0;
                $jasaVendorOutDetail[$i]['claw'] = 0;
                $jasaVendorOutDetail[$i]['mh'] = 0;
                $jasaVendorOutDetail[$i]['cf'] = 0;
            }
        }

        $result = [];

        foreach ($jasaVendorOutDetail as $item) {
            $key = $item['tanggal_masuk'] . '|' . $item['supplier_name'];

            if (!isset($result[$key])) {
                $result[$key] = [
                    'tanggal_masuk' => $item['tanggal_masuk'],
                    'supplier_name' => $item['supplier_name'],
                    'jumbo' => 0,
                    'ex_lump' => 0,
                    'lump' => 0,
                    'special' => 0,
                    'claw' => 0,
                    'mh' => 0,
                    'cf' => 0,
                    'qty_kopek' => 0
                ];
            }

            $result[$key]['jumbo'] += (float)$item['jumbo'];
            $result[$key]['ex_lump'] += (float)$item['ex_lump'];
            $result[$key]['lump'] += (float)$item['lump'];
            $result[$key]['special'] += (float)$item['special'];
            $result[$key]['claw'] += (float)$item['claw'];
            $result[$key]['mh'] += (float)$item['mh'];
            $result[$key]['cf'] += (float)$item['cf'];
            $result[$key]['qty_kopek'] += (float)$item['qty_kopek'];
        }

        $result = array_values($result);


        return $result;
    }   


    public function dropdownBarangKepitingKukusPrint($jasaVendorInID, $id = null)
    {
        $biayaKepitingDetailModel = new BiayaKepitingDetailModel();
        $jasaVendorInModel = new JasaVendorInModel();

        // Validasi input
        if (!$jasaVendorInID) {
            return [];
        }

        try {
            // cari data jasa vendor in buat tanggal masuk
            $jasaVendorIn = $jasaVendorInModel->find($jasaVendorInID);
            if (!$jasaVendorIn) {
                return [];
            }
            
            $tanggalMasuk = date('d/m/Y', strtotime($jasaVendorIn['tanggal']));

            // ambil semua detail kepiting berdasarkan jasa_vendor_in_id
            $biayaKepiting = $biayaKepitingDetailModel
                ->select("
                    biaya_kepiting_detail.*,
                    barang_master.id AS barang_master_id,
                    barang_master.barang_name,
                    suppliers.name as supplier_name
                ")
                ->join('barang_master', 'barang_master.id = biaya_kepiting_detail.barang_master_id', 'left')
                ->join('suppliers', 'suppliers.id = biaya_kepiting_detail.supplier_id', 'left')
                ->where('biaya_kepiting_detail.jasa_vendor_in_id', $jasaVendorInID)
                ->where('biaya_kepiting_detail.deletedAt', null)
                ->findAll();

            $result = [];

            foreach ($biayaKepiting as $item) {
                $key = $tanggalMasuk . '|' . ($item['supplier_name'] ?? '-');

                if (!isset($result[$key])) {
                    $result[$key] = [
                        'tanggal_masuk' => $tanggalMasuk,
                        'supplier_name' => $item['supplier_name'] ?? '-',
                        'jumbo' => 0,
                        'ex_lump' => 0,
                        'lump' => 0,
                        'special' => 0,
                        'claw' => 0,
                        'mh' => 0,
                        'cf' => 0,
                        'qty_kopek' => 0,
                    ];
                }

                $result[$key]['jumbo']   += (float)($item['jumbo'] ?? 0);
                $result[$key]['ex_lump'] += (float)($item['ex_lump'] ?? 0);
                $result[$key]['lump']    += (float)($item['lump'] ?? 0);
                $result[$key]['special'] += (float)($item['special'] ?? 0);
                $result[$key]['claw']    += (float)($item['claw'] ?? 0);
                $result[$key]['mh']      += (float)($item['mh'] ?? 0);
                $result[$key]['cf']      += (float)($item['cf'] ?? 0);
                $result[$key]['qty_kopek'] += (float)($item['qty_kopek'] ?? 0);
            }
            
            return array_values($result);
        } catch (\Exception $e) {
            log_message('error', 'Error in dropdownBarangKepitingKukusPrint: ' . $e->getMessage());
            return [];
        }
    }

    public function dropdownPerolehanGaji($id = null)
    {
        try {
            $metaDataModel = new MetadataModel();
            $biayaKepitingGajiModel = new BiayaKepitingGajiModel();
            
            $jenisBiayaKepiting = $metaDataModel->where('name', "Jenis Biaya Kepiting")->findAll();

            // Validasi jika tidak ada data metadata
            if (empty($jenisBiayaKepiting)) {
                return [];
            }

            foreach ($jenisBiayaKepiting as $i => $j) {
                // Set default values
                $defaultValues = [
                    'jumbo' => 0,
                    'ex_lump' => 0,
                    'lump' => 0,
                    'special' => 0,
                    'claw' => 0,
                    'mh' => 0,
                    'cf' => 0
                ];

                // Cari data gaji jika ada ID
                if ($id != null) {
                    $biayaKepitingGaji = $biayaKepitingGajiModel
                        ->where('biaya_kepiting_id', $id)
                        ->where('jenis', $j['description'] ?? '')
                        ->first();

                    if ($biayaKepitingGaji) {
                        $jenisBiayaKepiting[$i]['jumbo'] = (float)($biayaKepitingGaji['jumbo'] ?? 0);
                        $jenisBiayaKepiting[$i]['ex_lump'] = (float)($biayaKepitingGaji['ex_lump'] ?? 0);
                        $jenisBiayaKepiting[$i]['lump'] = (float)($biayaKepitingGaji['lump'] ?? 0);
                        $jenisBiayaKepiting[$i]['special'] = (float)($biayaKepitingGaji['special'] ?? 0);
                        $jenisBiayaKepiting[$i]['claw'] = (float)($biayaKepitingGaji['claw'] ?? 0);
                        $jenisBiayaKepiting[$i]['mh'] = (float)($biayaKepitingGaji['mh'] ?? 0);
                        $jenisBiayaKepiting[$i]['cf'] = (float)($biayaKepitingGaji['cf'] ?? 0);
                    } else {
                        // Jika tidak ditemukan, set default values
                        $jenisBiayaKepiting[$i] = array_merge($jenisBiayaKepiting[$i], $defaultValues);
                    }
                } else {
                    // Jika tidak ada ID, set default values
                    $jenisBiayaKepiting[$i] = array_merge($jenisBiayaKepiting[$i], $defaultValues);
                }
            }

            return $jenisBiayaKepiting;
            
        } catch (\Exception $e) {
            log_message('error', 'Error in dropdownPerolehanGaji: ' . $e->getMessage());
            return [];
        }
    }

    public function getPenerimaanSuratJalanDetail($id)
    {
        $result = $this
            ->select('jasa_vendor_in.*,divisis.divisi,vendors.name')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = biaya_kepiting.jasa_vendor_in_id', 'left')
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where('biaya_kepiting.id', $id)
            ->first();

        return $result;
    }

    public function get_no($bln, $thn, $last_day, $warehouseKode, $warehouse_id)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('biaya_kepiting');
        $builder->select('no_pembayaran');
        $builder->orderBy('no_pembayaran', 'desc');
        $builder->where('biaya_kepiting.warehouse_id', $warehouse_id);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_pembayaran', $lastStr);
        $query = $builder->get();

        $kode = 'PAY-KPT/' . $warehouseKode;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_pembayaran']);
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
}
