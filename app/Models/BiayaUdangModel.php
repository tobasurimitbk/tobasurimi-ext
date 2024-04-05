<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaUdangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_udang';
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

        $selectQry = "biaya_udang.*,
        divisis.divisi,
        warehouses.warehouse_name,
        vendors.name as vendor_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = biaya_udang.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = biaya_udang.warehouse_id', 'left')
            ->join('vendors', 'vendors.id = biaya_udang.vendor_id', 'left')
            ->where($condition)
            ->whereIn('biaya_udang.divisi_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_pembayaran'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('biaya_udang.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->like('biaya_udang.warehouse_id', $addCondition['warehouse_id']);
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


    public function dropdownPenerimaanSuratJalan()
    {
        $divisiModel = new DivisisModel();
        $divisiArr = array();

        foreach ($divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $result = $this
            ->select('jasa_vendor_in.*,divisis.divisi,vendors.name')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = biaya_udang.jasa_vendor_in_id', 'right')
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where('biaya_udang.jasa_vendor_in_id', null)
            ->where('jasa_vendor_in.status_posting', '1')
            ->whereIn('jasa_vendor_in.divisi_id', $divisiArr)
            ->findAll();

        return $result;
    }

    public function getPenerimaanSuratJalanDetail($id)
    {
        $result = $this
            ->select('jasa_vendor_in.*,divisis.divisi,vendors.name')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = biaya_udang.jasa_vendor_in_id', 'left')
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where('biaya_udang.id', $id)
            ->first();

        return $result;
    }

    public function dropdownBarang($jasaVendorInID, $id = null)
    {
        $jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $biayaUdangDetailModel = new BiayaUdangDetailModel();

        // CREATE
        $selectQryJasaVendorDetail = "
            barang_master.id AS barang_master_id,
            barang_master_spesifikasi.id AS barang_master_spesifikasi_id,
            jasa_vendor_in.tanggal AS tanggal_masuk,
            jasa_vendor_out.tanggal AS tanggal_keluar,
            SUM(jasa_vendor_in_detail.qty_bersih) as qty_bersih,
            SUM(jasa_vendor_out_detail.qty) as qty_rebus,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi
        ";

        $jasaVendorInDetail = $jasaVendorInDetailModel
            ->select($selectQryJasaVendorDetail)
            ->join('jasa_vendor_out_detail', 'jasa_vendor_out_detail.id = jasa_vendor_in_detail.jasa_vendor_out_detail_id')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = jasa_vendor_in_detail.jasa_vendor_in_id')
            ->join('jasa_vendor_out', 'jasa_vendor_out.id = jasa_vendor_out_detail.jasa_vendor_out_id')
            ->join('stock', 'stock.id = jasa_vendor_in_detail.stock_in_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->where('jasa_vendor_in_id', $jasaVendorInID)
            ->groupBy('jasa_vendor_in_detail.stock_in_id')
            ->findAll();

        for ($i = 0; $i < count($jasaVendorInDetail); $i++) {
            $jasaVendorInDetail[$i]['tanggal_masuk'] = date('d/m/Y', strtotime($jasaVendorInDetail[$i]['tanggal_masuk']));
            $jasaVendorInDetail[$i]['tanggal_keluar'] = date('d/m/Y', strtotime($jasaVendorInDetail[$i]['tanggal_keluar']));

            if ($id != null) {
                $biayaUdangDetail = $biayaUdangDetailModel
                    ->where('biaya_udang_id', $id)
                    ->where('jasa_vendor_in_id', $jasaVendorInID)
                    ->where('barang_master_id', $jasaVendorInDetail[$i]['barang_master_id'])
                    ->where('barang_master_spesifikasi_id', $jasaVendorInDetail[$i]['barang_master_spesifikasi_id'])
                    ->first();

                $jasaVendorInDetail[$i]['kg_fauzy'] = $biayaUdangDetail['kg_fauzy'];
                $jasaVendorInDetail[$i]['kg_cn'] = $biayaUdangDetail['kg_cn'];
                $jasaVendorInDetail[$i]['kg_daging'] = $biayaUdangDetail['kg_daging'];
                $jasaVendorInDetail[$i]['tb_harga'] = $biayaUdangDetail['tb_harga'];
            } else {
                $jasaVendorInDetail[$i]['kg_fauzy'] = 0;
                $jasaVendorInDetail[$i]['kg_cn'] = 0;
                $jasaVendorInDetail[$i]['kg_daging'] = 0;
                $jasaVendorInDetail[$i]['tb_harga'] = 0;
            }
        }

        return $jasaVendorInDetail;
    }

    public function get_no($bln, $thn, $last_day, $warehouseKode, $warehouse_id)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('biaya_udang');
        $builder->select('no_pembayaran');
        $builder->orderBy('no_pembayaran', 'desc');
        $builder->where('biaya_udang.warehouse_id', $warehouse_id);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_pembayaran', $lastStr);
        $query = $builder->get();

        $kode = 'PAY-UDG/' . $warehouseKode;

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
