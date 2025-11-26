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


    public function dropdownJasaVendorIn($divisiID, $vendorID)
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
            ->where('jasa_vendor_in.vendor_id', $vendorID)
            ->findAll();

        return $resultBiayaUdang;
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
        $biayaUdangModel = new BiayaUdangModel(); // <-- ini harus model yg bener
        $jasaVendorInModel = new JasaVendorInModel();

        // 🔹 Ambil divisi yang boleh diakses
        $divisiArr = array_column($divisiModel->getDivisiAccess(), 'id');

        // 🔹 Ambil jasa_vendor_in valid
        $result = $jasaVendorInModel
            ->select('divisis.id, divisis.divisi, jasa_vendor_in.id AS jasa_vendor_in_id')
            ->join('divisis', 'divisis.id = jasa_vendor_in.divisi_id', 'left')
            ->where('jasa_vendor_in.status_posting', '1')
            ->where('jasa_vendor_in.vendor_id', $vendorID)
            ->whereIn('jasa_vendor_in.divisi_id', $divisiArr)
            ->findAll();

        $final = [];

        foreach ($result as $r) {

            // 🔹 cek biaya kepiting
            $kepitingExists = $biayaKepitingModel
                ->where('jasa_vendor_in_id', $r['jasa_vendor_in_id'])
                ->first();

            // 🔹 cek biaya udang (multiple)
            $udangExists = $biayaUdangModel
                ->like('multiple_jasa_vendor_in_id', $r['jasa_vendor_in_id'])
                ->first();

            // 🔥 skip jika ada salah satu
            if ($kepitingExists || $udangExists) {
                continue;
            }

            // 🔹 push hanya yang valid
            $final[$r['id']] = $r; // unique by divisi id
        }

        return array_values($final);
    }

    public function dropdownBarang($jasaVendorInArrID, $id = null)
{
    $db = \Config\Database::connect();

    // ==================================================================
    // 1. SANITIZE INPUT IDS
    // ==================================================================
    $ids = [];

    if (is_array($jasaVendorInArrID)) {
        $raw = $jasaVendorInArrID;
    } else {
        $s = trim((string)$jasaVendorInArrID);

        // format JSON-like: "[730]" atau ["730"]
        if ((str_starts_with($s, '[') && str_ends_with($s, ']')) || strpos($s, '[') !== false) {
            preg_match_all('/\d+/', $s, $m);
            $raw = $m[0] ?? [];
        }
        // "1,2,3"
        elseif (strpos($s, ',') !== false) {
            $raw = explode(',', $s);
        }
        // single id "730"
        else {
            $raw = $s !== '' ? [$s] : [];
        }
    }

    // convert semua ke int
    foreach ($raw as $r) {
        $digits = preg_replace('/\D+/', '', (string)$r);
        if ($digits !== '') {
            $ids[] = (int)$digits;
        }
    }

    if (empty($ids)) {
        return [];
    }

    // ==================================================================
    // 2. AMBIL PARENT
    // ==================================================================
    $parents = $db->table('jasa_vendor_in')
        ->select('id, tanggal, multiple_jasa_vendor_out_id')
        ->whereIn('id', $ids)
        ->get()
        ->getResultArray();

    $final = [];

    // ==================================================================
    // 3. LOOP PER-PARENT → ambil detail
    // ==================================================================
    foreach ($parents as $p) {
        $parentId = (int)$p['id'];

        // DETAIL IN (bersih / kotor) + barang & spek
        $detailRows = $db->table('jasa_vendor_in_detail jvid')
            ->select('jvid.qty_bersih, jvid.qty_kotor, jvid.stock_detail_in_id,
                    bm.barang_name, bms.spesifikasi, bm.id as barang_master_id, 
                    bms.id as barang_master_spesifikasi_id, s.barang_master_id, s.spesifikasi_id')
            ->join('stock_revamp_detail srd', 'srd.id = jvid.stock_detail_in_id', 'left')
            ->join('stock_revamp s', 's.id = srd.stock_id', 'left')
            ->join('barang_master bm', 'bm.id = s.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = s.spesifikasi_id', 'left')
            ->where('jvid.jasa_vendor_in_id', $parentId)
            ->get()
            ->getResultArray();

        $sum_bersih = 0;
        $sum_kotor = 0;
        $barang_name = null;
        $spesifikasi = null;
        $barang_master_id = null;
        $barang_master_spesifikasi_id = null;

        foreach ($detailRows as $d) {
            $sum_bersih += floatval($d['qty_bersih'] ?? 0);
            $sum_kotor += floatval($d['qty_kotor'] ?? 0);

            if ($barang_name === null && !empty($d['barang_name'])) {
                $barang_name = $d['barang_name'];
            }
            if ($spesifikasi === null && !empty($d['spesifikasi'])) {
                $spesifikasi = $d['spesifikasi'];
            }
            if ($barang_master_id === null && !empty($d['barang_master_id'])) {
                $barang_master_id = $d['barang_master_id'];
            }
            if ($barang_master_spesifikasi_id === null && !empty($d['barang_master_spesifikasi_id'])) {
                $barang_master_spesifikasi_id = $d['barang_master_spesifikasi_id'];
            }
        }

        // ==================================================================
        // 4. AMBIL OUT DENGAN BARANG & SPESIFIKASI
        // ==================================================================
        $outRows = [];
        $multiple = trim((string)$p['multiple_jasa_vendor_out_id']);

        if ($multiple !== '') {
            preg_match_all('/\d+/', $multiple, $mout);
            $outIds = array_map('intval', $mout[0] ?? []);

            if (!empty($outIds)) {
                $outRows = $db->table('jasa_vendor_out_detail jvod')
                    ->select('jvod.qty AS qty_keluar, jvo.tanggal AS tanggal_keluar,
                             bm_out.barang_name AS barang_name_out, 
                             bms_out.spesifikasi AS spesifikasi_out,
                             bm_out.id AS barang_master_id_out,
                             bms_out.id AS barang_master_spesifikasi_id_out')
                    ->join('jasa_vendor_out jvo', 'jvo.id = jvod.jasa_vendor_out_id', 'left')
                    ->join('stock_revamp_detail srd_out', 'srd_out.id = jvod.stock_out_detail_id', 'left')
                    ->join('stock_revamp s_out', 's_out.id = srd_out.stock_id', 'left')
                    ->join('barang_master bm_out', 'bm_out.id = s_out.barang_master_id', 'left')
                    ->join('barang_master_spesifikasi bms_out', 'bms_out.id = s_out.spesifikasi_id', 'left')
                    ->whereIn('jvod.jasa_vendor_out_id', $outIds)
                    ->get()
                    ->getResultArray();
            }
        }

        $sum_keluar = 0;
        $outData = [];
        foreach ($outRows as $o) {
            $qty_keluar = floatval($o['qty_keluar'] ?? 0);
            $sum_keluar += $qty_keluar;
            
            $outData[] = [
                'tanggal_keluar' => $o['tanggal_keluar'],
                'qty_keluar' => $qty_keluar,
                'barang_name_out' => $o['barang_name_out'],
                'spesifikasi_out' => $o['spesifikasi_out'],
                'barang_master_id_out' => $o['barang_master_id_out'],
                'barang_master_spesifikasi_id_out' => $o['barang_master_spesifikasi_id_out']
            ];
        }

        // ==================================================================
        // 5. HITUNG RATIO
        // ==================================================================
        if ($sum_keluar > 0) {
            $ratio = round(($sum_kotor / $sum_keluar) * 100, 2);
        } else {
            $ratio = 0;
        }

        // ==================================================================
        // 6. BUILD PARENT OUTPUT
        // ==================================================================
        $final[$parentId] = [
            'parent' => [
                'id' => $parentId,
                'jasa_vendor_in_id' => $parentId,
                'tanggal_masuk' => $p['tanggal'],
                'sum_bersih' => round($sum_bersih, 3),
                'sum_kotor' => round($sum_kotor, 3),
                'sum_keluar' => round($sum_keluar, 3),
                'ratio' => $ratio,
                'barang_name' => $barang_name,
                'spesifikasi' => $spesifikasi,
                'barang_master_id' => $barang_master_id,
                'barang_master_spesifikasi_id' => $barang_master_spesifikasi_id,
                'harga_per_kilo' => 0,
                'total_harga' => 0
            ],
            'detail' => $outData // Sekarang detail berisi data out dengan barang & spesifikasi
        ];

        // Jika tidak ada data out, tambahkan baris kosong
        if (empty($outData)) {
            $final[$parentId]['detail'][] = [
                'tanggal_keluar' => null,
                'qty_keluar' => 0,
                'barang_name_out' => null,
                'spesifikasi_out' => null,
                'barang_master_id_out' => null,
                'barang_master_spesifikasi_id_out' => null
            ];
        }
    }

    return $final;
}


  public function getBarangDetail($jasaVendorInArrID, $id)
    {
        // Ambil data dari biaya_udang_detail berdasarkan ID
        $db = \Config\Database::connect();
        
        $existingData = $db->table('biaya_udang_detail bud')
            ->select('bud.*, bm.barang_name, bms.spesifikasi')
            ->join('jasa_vendor_in jvi', 'jvi.id = bud.jasa_vendor_in_id', 'left')
            ->join('barang_master bm', 'bm.id = bud.barang_master_id', 'left')
            ->join('barang_master_spesifikasi bms', 'bms.id = bud.barang_master_spesifikasi_id', 'left')
            ->where('bud.biaya_udang_id', $id)
            ->get()
            ->getResultArray();

        if (empty($existingData)) {
            return [];
        }

        $result = [];
        
        foreach ($existingData as $detail) {
            $jasaVendorInId = $detail['jasa_vendor_in_id'];
            
            // Hitung ratio
            $kg_rebus = floatval($detail['kg_cn'] ?? 0);
            $kg_daging = floatval($detail['kg_daging'] ?? 0);
            $ratio = ($kg_rebus > 0) ? round(($kg_daging / $kg_rebus) * 100, 2) : 0;
            
            // Gunakan harga_per_kilo dari database
            $harga_per_kilo = floatval($detail['harga_per_kilo'] ?? 0);
            $total_harga = floatval($detail['total_harga'] ?? 0);
            
            $result[$jasaVendorInId] = [
                'harga_per_kilo' => $harga_per_kilo,
                'kg_rebus_total' => $kg_rebus,
                'kg_cn_total' => $kg_rebus, // kg_cn sama dengan kg_rebus
                'kg_daging_total' => $kg_daging,
                'total_harga' => $total_harga,
                'ratio' => $ratio,
                'tanggal_po' => $detail['tanggal_po'],
                'barang_name' => $detail['barang_name'],
                'spesifikasi' => $detail['spesifikasi']
            ];
        }

        return $result;
    }

    public function getExistingData($biayaUdangId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('biaya_udang_detail')
            ->where('biaya_udang_id', $biayaUdangId)
            ->get()
            ->getResultArray();
    }

    public function getDataTotalAutoComplete($listBarang)
    {
        $result = [];
        $barang_master_id_last = null;
        $totals = [
            'kg_rebus_total' => 0,
            'kg_fauzy_total' => 0,
            'kg_cn_total' => 0,
            'kg_daging_total' => 0
        ];
        $tb_harga_last = null;

        foreach ($listBarang as $detail) {
            if ($barang_master_id_last !== $detail->barang_master_id) {
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
                    'kg_rebus_total' => (float)$detail->qty_rebus,
                    'kg_fauzy_total' => (float)$detail->kg_fauzy,
                    'kg_cn_total' => (float)$detail->kg_cn,
                    'kg_daging_total' => (float)$detail->kg_daging
                ];
                $barang_master_id_last = $detail->barang_master_id;
                $tb_harga_last = $detail->tb_harga;
            } else {
                // Add to existing totals
                $totals['kg_rebus_total'] += (float)$detail->qty_rebus;
                $totals['kg_fauzy_total'] += (float)$detail->kg_fauzy;
                $totals['kg_cn_total'] += (float)$detail->kg_cn;
                $totals['kg_daging_total'] += (float)$detail->kg_daging;
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
            if ($result[$i]['kg_daging_total'] == 0) {
                $result[$i]['kg_daging_total'] = 1;
            }

            $result[$i]['total_harga'] = (float)($result[$i]['kg_daging_total'] * $result[$i]['tb_harga']);
            $result[$i]['ratio'] = ((float)($result[$i]['kg_daging_total'] / $result[$i]['kg_rebus_total']) * 100);
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
