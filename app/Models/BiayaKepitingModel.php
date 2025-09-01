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

    public function dropdownPerolehanGaji($id = null)
    {
        $metaDataModel = new MetadataModel();
        $biayaKepitingGajiModel = new BiayaKepitingGajiModel();
        $jenisBiayaKepiting = $metaDataModel->where('name', "Jenis Biaya Kepiting")->findAll();

        foreach ($jenisBiayaKepiting as $i => $j) {
            if ($id != null) {
                $biayaKepitingGaji = $biayaKepitingGajiModel->where('biaya_kepiting_id', $id)->where('jenis', $j['description'])->first();
                $jenisBiayaKepiting[$i]['jumbo'] = $biayaKepitingGaji['jumbo'] ?? 0;
                $jenisBiayaKepiting[$i]['ex_lump'] = $biayaKepitingGaji['ex_lump'] ?? 0;
                $jenisBiayaKepiting[$i]['lump'] = $biayaKepitingGaji['lump'] ?? 0;
                $jenisBiayaKepiting[$i]['special'] = $biayaKepitingGaji['special'] ?? 0;
                $jenisBiayaKepiting[$i]['claw'] = $biayaKepitingGaji['claw'] ?? 0;
                $jenisBiayaKepiting[$i]['mh'] = $biayaKepitingGaji['mh'] ?? 0;
                $jenisBiayaKepiting[$i]['cf'] = $biayaKepitingGaji['cf'] ?? 0;
            } else {
                $biayaKepitingGaji = $biayaKepitingGajiModel->where('biaya_kepiting_id', $id)->where('jenis', $j['description'])->first();
                $jenisBiayaKepiting[$i]['jumbo'] = 0;
                $jenisBiayaKepiting[$i]['ex_lump'] = 0;
                $jenisBiayaKepiting[$i]['lump'] = 0;
                $jenisBiayaKepiting[$i]['special'] = 0;
                $jenisBiayaKepiting[$i]['claw'] = 0;
                $jenisBiayaKepiting[$i]['mh'] = 0;
                $jenisBiayaKepiting[$i]['cf'] = 0;
            }
        }

        return $jenisBiayaKepiting;
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
