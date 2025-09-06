<?php

namespace App\Models;

use CodeIgniter\Model;

class JasaVendorInKepitingKukusModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jasa_vendor_in_kepiting_kukus';
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
            'no_penerimaan_surat_jalan' => 'no_penerimaan_surat_jalan',
            'jasa_vendor_in_kepiting_kukus.createdAt' => 'jasa_vendor_in_kepiting_kukus.createdAt',
            'jasa_vendor_in_kepiting_kukus.divisi_id' => 'jasa_vendor_in_kepiting_kukus.divisi_id',
            'jasa_vendor_in_kepiting_kukus.warehouse_id' => 'jasa_vendor_in_kepiting_kukus.warehouse_id',
            'vendor_id' => 'vendor_id',
            'no_surat_jalan_vendor' => 'no_surat_jalan_vendor',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "jasa_vendor_in_kepiting_kukus.*,
        divisis.divisi,
        warehouses.warehouse_name,
        vendors.name as vendor_name
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = jasa_vendor_in_kepiting_kukus.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = jasa_vendor_in_kepiting_kukus.warehouse_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in_kepiting_kukus.vendor_id', 'left')
            ->where($condition)
            ->whereIn('jasa_vendor_in_kepiting_kukus.divisi_id', $conditionArr)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_penerimaan_surat_jalan'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $dataQry->groupStart();
        }

        if ($addCondition['divisi_id']) {
            $dataQry->where('jasa_vendor_in_kepiting_kukus.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $dataQry->like('jasa_vendor_in_kepiting_kukus.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['status'] || $addCondition['status'] == '0') {
            $dataQry->where('status_posting', $addCondition['status']);
        }

        if ($addCondition['no_penerimaan_surat_jalan']) {
            $dataQry->like('no_penerimaan_surat_jalan', $addCondition['no_penerimaan_surat_jalan']);
        }

        if ($addCondition['start_date']) {
            $dataQry->where('tanggal >=', $addCondition['start_date']);
        }

        if ($addCondition['end_date']) {
            $dataQry->where('tanggal <=', $addCondition['end_date']);
        }

        if ($addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['status'] || $addCondition['no_penerimaan_surat_jalan'] || $addCondition['start_date'] || $addCondition['end_date']) {
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

    public function listBarang($jasaVendorOutArr, $jasaVendorInID)
    {
        $jasaVendorOutKepitingKukusDetailModel = new JasaVendorOutKepitingKukusDetailModel();
        $jasaVendorInKepitingKukusDetailModel  = new JasaVendorInKepitingKukusDetailModel();

        // ambil detail out + join ke spesifikasi & master_barang
        $jasaVendorOutData = $jasaVendorOutKepitingKukusDetailModel
            ->select('
                jasa_vendor_out_kepiting_kukus_detail.*,
                jasa_vendor_out_kepiting_kukus.no_surat_jalan,
                barang_master_spesifikasi.spesifikasi AS spesifikasi_name,
                barang_master.barang_name,
                satuans.kode_satuan AS satuan
            ')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = jasa_vendor_out_kepiting_kukus_detail.spesifikasi_id', 'left')
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('jasa_vendor_out_kepiting_kukus', 'jasa_vendor_out_kepiting_kukus.id = jasa_vendor_out_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_id', 'left')
            ->whereIn('jasa_vendor_out_kepiting_kukus_id', $jasaVendorOutArr)
            ->where('jasa_vendor_out_kepiting_kukus_detail.deletedAt', null)
            ->findAll();

        $result = [];

        foreach ($jasaVendorOutData as $j) {
            // ambil detail in (kalau ada)
            $jasaVendorInDetail = [];
            if ($jasaVendorInID) {
                $jasaVendorInDetail = $jasaVendorInKepitingKukusDetailModel
                    ->select('
                        jasa_vendor_in_kepiting_kukus_detail.*,
                        barang_master_spesifikasi.nama AS spesifikasi_name,
                        barang_master.nama AS barang_name,
                        satuans.kode_satuan AS satuan
                    ')
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = jasa_vendor_in_kepiting_kukus_detail.spesifikasi_in_id')
                    ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
                    ->join('satuans', 'satuans.id = barang_master.satuan_id')
                    ->where('jasa_vendor_in_kepiting_kukus_id', $jasaVendorInID)
                    ->where('jasa_vendor_out_kepiting_kukus_id', $j['jasa_vendor_out_kepiting_kukus_id'])
                    ->where('jasa_vendor_out_detail_id', $j['id'])
                    ->findAll();
            }

            // data out
            $row = [
                'jasa_vendor_out_detail_id' => $j['id'],
                "no_surat_jalan" => $j['no_surat_jalan'] ?? '',
                'jasa_vendor_out_kepiting_kukus_id' => $j['jasa_vendor_out_kepiting_kukus_id'],
                'spesifikasi_out_id'        => $j['spesifikasi_id'],
                'barang_out'                => strtoupper($j['barang_name']),
                'spesifikasi_out'           => strtoupper($j['spesifikasi_name']),
                'satuan_out'                => $j['satuan'],
                'qty_out'                   => $j['qty'],
                'list_barang_masuk'         => []
            ];

            // tambahin data in kalau ada
            foreach ($jasaVendorInDetail as $k) {
                $row['list_barang_masuk'][] = [
                    'jasa_vendor_out_detail_id' => $k['jasa_vendor_out_detail_id'],
                    'spesifikasi_in_id'         => $k['spesifikasi_in_id'],
                    'barang_in'                 => strtoupper($k['barang_name']),
                    'spesifikasi_in'            => strtoupper($k['spesifikasi_name']),
                    'satuan_in'                 => $k['satuan'],
                    'qty'                       => $k['qty'],
                ];
            }

            $result[] = $row;
        }

        // group by jasa_vendor_out_kepiting_kukus_id
        $grouped = [];
        foreach ($result as $item) {
            $barangId = $item['jasa_vendor_out_kepiting_kukus_id'];

            if (!isset($grouped[$barangId])) {
                $grouped[$barangId] = [
                    "no_surat_jalan" => $item['no_surat_jalan'] ?? '',
                    "spesifikasi_out_id" => $barangId,
                    "barang_out"         => $item['barang_out'],
                    "spesifikasi_out"    => $item['spesifikasi_out'],
                    "satuan_out"         => $item['satuan_out'],
                    "qty_out"            => 0,
                    "list_barang_masuk"  => []
                ];
            }

            $grouped[$barangId]["qty_out"] += $item['qty_out'];

            foreach ($item['list_barang_masuk'] as $masuk) {
                $grouped[$barangId]['list_barang_masuk'][] = $masuk;
            }
        }

        return [
            'dataDetail' => $result,
            'dataGroup'  => array_values($grouped)
        ];
    }

    static function getDetailBarang($stock)
    {
        $barangMasterModel = new BarangMasterModel();

        $barang = $barangMasterModel
            ->select("CONCAT(barang_master.barang_name, '-', barang_master_spesifikasi.spesifikasi) AS barang, satuans.kode_satuan, barang_master.kode_barang, barang_master_spesifikasi.barang_master_id AS barang1_id")
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->where('barang_master_spesifikasi.id', $stock['barang2_id'])
            ->where('barang_master_spesifikasi.barang_master_id', $stock['barang1_id'])
            ->first();

        return $barang;
    }

    public function getJasaVendorOutNo($jasaVendorOutID)
    {
        $jasaVendorOutModel = new JasaVendorOutModel();
        $result = array();
        $dataQry = $jasaVendorOutModel->whereIn('id', $jasaVendorOutID)->where('deletedAt', null)->findAll();

        foreach ($dataQry as $d) {
            array_push($result, $d['no_surat_jalan']);
        }

        return $result;
    }

    public function get_no($bln, $thn, $divisi)
    {
        $lastStr = convertBulanToAngkaRomawi($bln) . '/' . $thn;
        $first_day = "$thn-$bln-01";
        $last_day = date("Y-m-t", strtotime($first_day));

        $builder = $this->db->table('jasa_vendor_in_kepiting_kukus');
        $builder->select('no_penerimaan_surat_jalan');
        $builder->orderBy('id', 'desc');
        $builder->where('company_id', session()->get("login")->this_company_id);
        $builder->where('tanggal >=', $first_day);
        $builder->where('tanggal <=', $last_day);
        $builder->like('no_penerimaan_surat_jalan', $lastStr);
        $query = $builder->get();

        $kode = 'TOBA-VBM/' . $divisi;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_penerimaan_surat_jalan']);
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
