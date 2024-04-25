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
            'vendor_id' => 'vendor_id',
            'multiple_jasa_vendor_in_no' => 'multiple_jasa_vendor_in_no',
            'no_pembayaran' => 'no_pembayaran',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "biaya_udang.*,
        divisis.divisi,
        vendors.name as vendor_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = biaya_udang.divisi_id', 'left')
            ->join('vendors', 'vendors.id = biaya_udang.vendor_id', 'left')
            ->where($condition)
            ->whereIn('biaya_udang.divisi_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['status'] || $addCondition['no_pembayaran'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('biaya_udang.divisi_id', $addCondition['divisi_id']);
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

        if ($addCondition['divisi_id'] || $addCondition['status'] || $addCondition['no_pembayaran'] || $addCondition['start_date'] || $addCondition['end_date']) {
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


    public function dropdownJasaVendorIn($divisiID)
    {
        $jasaVendorInModel = new JasaVendorInModel();
        $biayaKepitingModel = new BiayaKepitingModel();
        $result = array();

        $resultBiayaUdang = $jasaVendorInModel
            ->select('jasa_vendor_in.id, jasa_vendor_in.no_penerimaan_surat_jalan')
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where('jasa_vendor_in.status_posting', '1')
            ->where('jasa_vendor_in.divisi_id', $divisiID)
            ->findAll();

        foreach ($resultBiayaUdang as $r) {
            $checkBiayaKepiting = $biayaKepitingModel->where('jasa_vendor_in_id', $r['id'])->first();
            $checkBiayaUdang = $this->like('multiple_jasa_vendor_in_id', $r['id'])->first();
            if ($checkBiayaKepiting == null && $checkBiayaUdang == null) {
                array_push($result, $r);
            }
        }

        return $result;
    }

    public function getJasaVendorInNo($jasaVendorInArrID)
    {
        $jasaVendorInModel = new JasaVendorInModel();
        $result = array();
        $dataQry = $jasaVendorInModel->whereIn('id', $jasaVendorInArrID)->where('deletedAt', null)->findAll();

        foreach ($dataQry as $d) {
            array_push($result, $d['no_penerimaan_surat_jalan']);
        }

        return $result;
    }

    public function dropdownDivisi($vendorID)
    {
        $divisiModel = new DivisisModel();
        $biayaKepitingModel = new BiayaKepitingModel();
        $jasaVendorInModel = new JasaVendorInModel();

        $divisiArr = array();
        $result = array();

        foreach ($divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $resultBiayaUdang = $jasaVendorInModel
            ->select('DISTINCT(divisis.id), divisis.divisi, jasa_vendor_in.id AS jasa_vendor_in_id')
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
            ->where('jasa_vendor_in.status_posting', '1')
            ->where('jasa_vendor_in.vendor_id', $vendorID)
            ->whereIn('jasa_vendor_in.divisi_id', $divisiArr)
            ->findAll();

        foreach ($resultBiayaUdang as $r) {
            $checkBiayaKepiting = $biayaKepitingModel->where('jasa_vendor_in_id', $r['jasa_vendor_in_id'])->first();
            $checkBiayaUdang = $this->like('multiple_jasa_vendor_in_id', $r['jasa_vendor_in_id'])->first();
            if ($checkBiayaKepiting == null && $checkBiayaUdang == null) {
                array_push($result, $r);
            }
        }

        if (count($result) != 0) {
            $idArr = array_column($result, 'id');
            $uniqueID = array_unique($idArr);
            $uniqueArr =  array_intersect_key($result, $uniqueID);
            $result = $uniqueArr;
        }

        return $result;
    }


    public function dropdownBarang($jasaVendorInArrID, $id = null)
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
            barang_master_spesifikasi.spesifikasi,
            jasa_vendor_in.id AS jasa_vendor_in_id
        ";

        $jasaVendorInDetail = $jasaVendorInDetailModel
            ->select($selectQryJasaVendorDetail)
            ->join('jasa_vendor_out_detail', 'jasa_vendor_out_detail.id = jasa_vendor_in_detail.jasa_vendor_out_detail_id')
            ->join('jasa_vendor_in', 'jasa_vendor_in.id = jasa_vendor_in_detail.jasa_vendor_in_id')
            ->join('jasa_vendor_out', 'jasa_vendor_out.id = jasa_vendor_out_detail.jasa_vendor_out_id')
            ->join('stock', 'stock.id = jasa_vendor_in_detail.stock_in_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->whereIn('jasa_vendor_in_id', $jasaVendorInArrID)
            ->groupBy('jasa_vendor_in_detail.stock_in_id')
            ->findAll();

        for ($i = 0; $i < count($jasaVendorInDetail); $i++) {
            $jasaVendorInDetail[$i]['tanggal_masuk'] = date('Y-m-d', strtotime($jasaVendorInDetail[$i]['tanggal_masuk']));
            $jasaVendorInDetail[$i]['tanggal_keluar'] = date('d/m/Y', strtotime($jasaVendorInDetail[$i]['tanggal_keluar']));

            if ($id != null) {
                $biayaUdangDetail = $biayaUdangDetailModel
                    ->where('biaya_udang_id', $id)
                    ->where('jasa_vendor_in_id', $jasaVendorInDetail[$i]['jasa_vendor_in_id'])
                    ->where('barang_master_id', $jasaVendorInDetail[$i]['barang_master_id'])
                    ->where('barang_master_spesifikasi_id', $jasaVendorInDetail[$i]['barang_master_spesifikasi_id'])
                    ->first();

                $jasaVendorInDetail[$i]['tanggal_po'] = $biayaUdangDetail['tanggal_po'];
                $jasaVendorInDetail[$i]['kg_fauzy'] = $biayaUdangDetail['kg_fauzy'];
                $jasaVendorInDetail[$i]['kg_cn'] = $biayaUdangDetail['kg_cn'];
                $jasaVendorInDetail[$i]['kg_daging'] = $biayaUdangDetail['kg_daging'];
                $jasaVendorInDetail[$i]['tb_harga'] = $biayaUdangDetail['tb_harga'];
            } else {
                $jasaVendorInDetail[$i]['tanggal_po'] = $jasaVendorInDetail[$i]['tanggal_masuk'];
                $jasaVendorInDetail[$i]['kg_fauzy'] = 0;
                $jasaVendorInDetail[$i]['kg_cn'] = 0;
                $jasaVendorInDetail[$i]['kg_daging'] = 0;
                $jasaVendorInDetail[$i]['tb_harga'] = 0;
            }
        }

        return $jasaVendorInDetail;
    }

    public function getBarangDetail($jasaVendorInArrID, $id)
    {
        $biayaUdangDetail = $this->dropdownBarang($jasaVendorInArrID, $id);

        if (empty($biayaUdangDetail)) {
            return [];
        }

        $result = [];
        $barang_master_id_last = null;
        $totals = [
            'kg_rebus_total' => 0,
            'kg_fauzy_total' => 0,
            'kg_cn_total' => 0,
            'kg_daging_total' => 0
        ];
        $tb_harga_last = null;

        foreach ($biayaUdangDetail as $detail) {
            if ($barang_master_id_last !== $detail['barang_master_id']) {
                if ($barang_master_id_last !== null) {
                    $result[] = [
                        'barang_master_id' => $barang_master_id_last,
                        'tb_harga' => $tb_harga_last,
                        'kg_rebus_total' => $totals['kg_rebus_total'],
                        'kg_fauzy_total' => $totals['kg_fauzy_total'],
                        'kg_cn_total' => $totals['kg_cn_total'],
                        'kg_daging_total' => $totals['kg_daging_total']
                    ];
                }
                // Reset totals and tb_harga for the new barang_master_id
                $totals = [
                    'kg_rebus_total' => (float)$detail['qty_rebus'],
                    'kg_fauzy_total' => (float)$detail['kg_fauzy'],
                    'kg_cn_total' => (float)$detail['kg_cn'],
                    'kg_daging_total' => (float)$detail['kg_daging']
                ];
                $barang_master_id_last = $detail['barang_master_id'];
                $tb_harga_last = $detail['tb_harga'];
            } else {
                // Add to existing totals
                $totals['kg_rebus_total'] += (float)$detail['qty_rebus'];
                $totals['kg_fauzy_total'] += (float)$detail['kg_fauzy'];
                $totals['kg_cn_total'] += (float)$detail['kg_cn'];
                $totals['kg_daging_total'] += (float)$detail['kg_daging'];
            }
        }

        // Add the last barang_master_id to the result
        $result[] = [
            'barang_master_id' => $barang_master_id_last,
            'tb_harga' => $tb_harga_last,
            'kg_rebus_total' => $totals['kg_rebus_total'],
            'kg_fauzy_total' => $totals['kg_fauzy_total'],
            'kg_cn_total' => $totals['kg_cn_total'],
            'kg_daging_total' => $totals['kg_daging_total']
        ];

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['total_harga'] = (float)($result[$i]['kg_daging_total'] * $result[$i]['tb_harga']);
            $result[$i]['ratio'] = ((float)($result[$i]['kg_rebus_total'] / $result[$i]['kg_daging_total']));
        }

        return $result;
    }

    public function get_no($bln, $thn, $last_day, $divisi, $divisi_id)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('biaya_udang');
        $builder->select('no_pembayaran');
        $builder->orderBy('no_pembayaran', 'desc');
        $builder->where('biaya_udang.divisi_id', $divisi_id);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_pembayaran', $lastStr);
        $query = $builder->get();

        $kode = 'PAY-UDG/' . $divisi;

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
