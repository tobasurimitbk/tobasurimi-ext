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


    public function getList($condition, $divisiArr, $addCondition = [], $limit = 0, $offset = 0)
    {
        $builder = $this->db->table('jasa_vendor_in_kepiting_kukus')
            ->select('jasa_vendor_in_kepiting_kukus.*, divisis.divisi, warehouses.warehouse_name, vendors.name as vendor_name')
            ->join('divisis', 'divisis.id = jasa_vendor_in_kepiting_kukus.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = jasa_vendor_in_kepiting_kukus.warehouse_id', 'left')
            ->join('vendors', 'vendors.id = jasa_vendor_in_kepiting_kukus.vendor_id', 'left')
            ->where($condition);
        
        // Filter divisi access
        if (!empty($divisiArr)) {
            $builder->whereIn('jasa_vendor_in_kepiting_kukus.divisi_id', $divisiArr);
        }
        
        // Filter tambahan
        if (!empty($addCondition['divisi_id'])) {
            $builder->where('jasa_vendor_in_kepiting_kukus.divisi_id', $addCondition['divisi_id']);
        }
        
        if (!empty($addCondition['warehouse_id'])) {
            $builder->where('jasa_vendor_in_kepiting_kukus.warehouse_id', $addCondition['warehouse_id']);
        }
        
        if ($addCondition['status'] !== '' && $addCondition['status'] !== null) {
            $builder->where('jasa_vendor_in_kepiting_kukus.status_posting', $addCondition['status']);
        }
        
        if (!empty($addCondition['start_date'])) {
            $startDate = date('Y-m-d', strtotime(str_replace('/', '-', $addCondition['start_date'])));
            $builder->where('DATE(jasa_vendor_in_kepiting_kukus.tanggal) >=', $startDate);
        }
        
        if (!empty($addCondition['end_date'])) {
            $endDate = date('Y-m-d', strtotime(str_replace('/', '-', $addCondition['end_date'])));
            $builder->where('DATE(jasa_vendor_in_kepiting_kukus.tanggal) <=', $endDate);
        }
        
        if (!empty($addCondition['no_penerimaan_surat_jalan'])) {
            $builder->like('jasa_vendor_in_kepiting_kukus.no_penerimaan_surat_jalan', $addCondition['no_penerimaan_surat_jalan']);
        }
        
        // Sorting
        if (!empty($addCondition['sort'])) {
            $sortType = !empty($addCondition['sortType']) ? $addCondition['sortType'] : 'asc';
            $builder->orderBy($addCondition['sort'], $sortType);
        } else {
            $builder->orderBy('jasa_vendor_in_kepiting_kukus.createdAt', 'desc');
        }
        
        // Clone untuk total data
        $totalBuilder = clone $builder;
        $totalData = $totalBuilder->countAllResults();
        
        // Clone untuk filtered data
        $filteredBuilder = clone $builder;
        $totalFilteredData = $filteredBuilder->countAllResults();
        
        // Limit dan offset untuk pagination
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }
        
        $query = $builder->get();
        
        return [
            'data' => $query->getResult(),
            'totalData' => $totalData,
            'totalFilteredData' => $totalFilteredData
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
                barang_master.id as barang_master_id,
                barang_master.kode_barang AS kode_barang,
                satuans.kode_satuan AS satuan,
                suppliers.name AS supplier_name,
                suppliers.id AS supplier_id
            ')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = jasa_vendor_out_kepiting_kukus_detail.spesifikasi_id', 'left')
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
            ->join('jasa_vendor_out_kepiting_kukus', 'jasa_vendor_out_kepiting_kukus.id = jasa_vendor_out_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_id', 'left')
            ->join('suppliers', 'suppliers.id = jasa_vendor_out_kepiting_kukus_detail.supplier_id', 'left')
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
                        barang_master_spesifikasi.spesifikasi AS spesifikasi_name,
                        barang_master.barang_name AS barang_name,
                        barang_master.id as barang_master_id,
                        barang_master.kode_barang AS kode_barang,
                        satuans.kode_satuan AS satuan,
                        suppliers.name AS supplier_name,
                        suppliers.id AS supplier_id
                    ')
                    ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = jasa_vendor_in_kepiting_kukus_detail.spesifikasi_in_id', 'left')
                    ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
                    ->join('jasa_vendor_out_kepiting_kukus_detail', 'jasa_vendor_out_kepiting_kukus_detail.id = jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_detail_id', 'left')
                    ->join('suppliers', 'suppliers.id = jasa_vendor_out_kepiting_kukus_detail.supplier_id', 'left')
                    ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                    ->where('jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_in_kepiting_kukus_id', $jasaVendorInID)
                    ->where('jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_id', $j['jasa_vendor_out_kepiting_kukus_id'])
                    ->where('jasa_vendor_in_kepiting_kukus_detail.jasa_vendor_out_kepiting_kukus_detail_id', $j['id'])
                    ->findAll();
            }

            // data out
            $row = [
                'jasa_vendor_out_kepiting_kukus_detail_id' => $j['id'],
                "no_surat_jalan" => $j['no_surat_jalan'] ?? '',
                'jasa_vendor_out_kepiting_kukus_id' => $j['jasa_vendor_out_kepiting_kukus_id'],
                'supplier_name'    => $j['supplier_name'],
                'supplier_id'       => $j['supplier_id'],
                'kode_barang_out'           => $j['kode_barang'],
                'barang_master_out_id'          => $j['barang_master_id'],
                'spesifikasi_out_id'        => $j['spesifikasi_id'],
                'barang_out'                => strtoupper($j['barang_name']),
                'spesifikasi_out'           => strtoupper($j['spesifikasi_name']),
                'keterangan'                => $j['keterangan'] ?? '',
                'satuan_out'                => $j['satuan'],
                'qty_out'                   => $j['qty'],
                'list_barang_masuk'         => []
            ];

            // tambahin data in kalau ada
            foreach ($jasaVendorInDetail as $k) {
                $row['list_barang_masuk'][] = [
                    'jasa_vendor_out_kepiting_kukus_detail_id' => $k['jasa_vendor_out_kepiting_kukus_detail_id'],
                    'barang_master_in_id'       => $k['barang_master_id'],
                    'spesifikasi_in_id'         => $k['spesifikasi_in_id'],
                    'kode_barang_in'               => $k['kode_barang'],
                    'barang_name_in'                 => strtoupper($k['barang_name'] . ($k['spesifikasi_name'] ? ' - ' . $k['spesifikasi_name'] : '')),
                    // 'spesifikasi_in'            => strtoupper($k['spesifikasi_name']),
                    'kode_satuan_in'                 => $k['satuan'],
                    'qty_kotor'                 => $k['qty_kotor'],
                ];
            }

            $result[] = $row;
        }

        $grouped = [];
        foreach ($result as $item) {
            // bikin key unik dari supplier + keterangan
            $groupKey = $item['supplier_id'] . '|' . ($item['keterangan'] ?? '');

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    "jasa_vendor_out_kepiting_kukus_detail_id" => $item['jasa_vendor_out_kepiting_kukus_detail_id'],
                    "jasa_vendor_out_kepiting_kukus_id" => $item['jasa_vendor_out_kepiting_kukus_id'],
                    "no_surat_jalan"   => $item['no_surat_jalan'] ?? '',
                    "spesifikasi_out_id" => $item['spesifikasi_out_id'] ?? null,
                    "supplier_name"    => $item['supplier_name'] ?? '',
                    "supplier_id"      => $item['supplier_id'],
                    "keterangan"       => $item['keterangan'] ?? '',
                    "kode_barang_out"  => $item['kode_barang_out'],
                    "barang_master_out_id" => $item['barang_master_out_id'],
                    "barang_out"       => $item['barang_out'],
                    "spesifikasi_out"  => $item['spesifikasi_out'],
                    "satuan_out"       => $item['satuan_out'],
                    "qty_out"          => 0,
                    "list_barang_masuk"=> []
                ];
            }

            $grouped[$groupKey]["qty_out"] += $item['qty_out'];

            foreach ($item['list_barang_masuk'] as $masuk) {
                $grouped[$groupKey]['list_barang_masuk'][] = $masuk;
            }
        }

        // kalau mau hasilnya berupa array reindex
        // $grouped = array_values($grouped);


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
        $jasaVendorOutKepitingKukusModel = new JasaVendorOutKepitingKukusModel();
        $result = array();
        $dataQry = $jasaVendorOutKepitingKukusModel->whereIn('id', $jasaVendorOutID)->where('deletedAt', null)->findAll();

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
