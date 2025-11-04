<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanMutasiGlobalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_mutasi_global';
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
            'penerimaan_mutasi_global.tanggal'               => 'penerimaan_mutasi_global.tanggal',
            'penerimaan_mutasi_no'                           => 'penerimaan_mutasi_no',
            'penerimaan_mutasi_global.multiple_mutasi_no'    => 'penerimaan_mutasi_global.multiple_no_mutasi',
            'penerimaan_mutasi_global.divisi_penerima_id'    => 'penerimaan_mutasi_global.divisi_penerima_id',
            'penerimaan_mutasi_global.warehouse_penerima_id' => 'penerimaan_mutasi_global.warehouse_penerima_id',
            'penerimaan_mutasi_global.company_pengirim_id'   => 'penerimaan_mutasi_global.company_pengirim_id'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'penerimaan_mutasi_global.penerimaan_mutasi_no'] ?? 'penerimaan_mutasi_global.penerimaan_mutasi_no';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi_global.*,
        divisis.divisi AS divisi_penerima,
        warehouses.warehouse_name AS warehouse_penerima,
        companies.company AS company_pengirim";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id', 'left')
            ->join('companies', 'companies.id = penerimaan_mutasi_global.company_pengirim_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $dataQry->groupStart();
        }

        if ($addCondition['dateStart']) {
            $dataQry->where('tanggal >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $dataQry->where('tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search']) {
            $dataQry->like('penerimaan_mutasi_no', $addCondition['search'])
                ->orLike('multiple_mutasi_no', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->orLike('companies.company', $addCondition['search']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
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


    public function getListBarangMutasi(
        $mutasiGlobalArrID,
        $penerimaanMutasiGlobalID = null,
        $isEdit = false
    ) {
        $stockRevampModel = new StockRevampModel();
        $mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();

        $selectQry = "
            mutasi_global_detail.*, 
            mutasi_global.no_mutasi,
            divisis.divisi AS divisi_asal,
            warehouses.warehouse_name AS warehouse_asal,
            satuans.kode_satuan AS satuan_konversi,
            bc_27.no_aju,
            bc_27.no_daftar
        ";

        $mutasiDetailList = $mutasiGlobalDetailModel
            ->select($selectQry)
            ->join('mutasi_global', 'mutasi_global.id = mutasi_global_detail.mutasi_global_id', 'left')
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id', 'left')
            ->join('satuans', 'satuans.id = mutasi_global_detail.unit_id_konversi', 'left')
            ->join('bc_27', 'bc_27.mutasi_global_id = mutasi_global.id', 'left')
            ->whereIn('mutasi_global_detail.mutasi_global_id', $mutasiGlobalArrID)
            ->where('mutasi_global_detail.deletedAt', null)
            ->findAll();


        $barangResult = [];
        foreach ($mutasiDetailList as $m) {

            $selectQryDetail = "
                penerimaan_mutasi_global_detail.*,
                barang_master.kode_barang,
                barang_master.barang_name,
                barang_master_spesifikasi.spesifikasi,
                satuans.kode_satuan
            ";

            $penerimaanMutasiGlobalDetail = $penerimaanMutasiGlobalDetailModel
                ->select($selectQryDetail)
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_mutasi_global_detail.spesifikasi_hasil_id', 'left')
                ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
                ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                ->where('penerimaan_mutasi_global_detail.penerimaan_mutasi_global_id', $penerimaanMutasiGlobalID)
                ->where('penerimaan_mutasi_global_detail.mutasi_global_detail_id', $m['id'])
                ->where('penerimaan_mutasi_global_detail.deletedAt', null)
                ->first();

            $fromStock = $stockRevampModel->getStockListAll(
                ["id" => $m['stock_detail_id']],
                0,
                "desc",
                1
            );

            $no = 1;
            foreach ($fromStock['data'] as $d) {

                if ($isEdit && $penerimaanMutasiGlobalDetail != null) {
                    // INI TAMPILAN EDIT
                    array_push($barangResult, [
                        'no' => $no++,
                        'mutasi_global_id' => $m['mutasi_global_id'],
                        'mutasi_global_detail_id' => $m['id'],
                        'divisi_asal' => $m['divisi_asal'],
                        'warehouse_asal' => $m['warehouse_asal'],
                        'no_mutasi' => $m['no_mutasi'],
                        'supplier_name' => $d['supplier_name'],
                        'kode_barang' => $d['kode_barang'],
                        'barang_name' => $d['barang_name'],
                        'spesifikasi' => $d['spesifikasi'],
                        'qty_mutasi' => (float)$m['qty_konversi'],
                        'satuan_mutasi' => $m['satuan_konversi'],
                        'type_bc' => "BC 2.7",
                        'no_aju' => $m['no_aju'],
                        'no_daftar' => $m['no_daftar'],
                        "penerimaan" => [
                            'mutasi_global_id' => $m['mutasi_global_id'],
                            'mutasi_global_detail_id' => $m['id'],
                            'kode_barang' => $penerimaanMutasiGlobalDetail['kode_barang'],
                            'barang_name' => $penerimaanMutasiGlobalDetail['barang_name'],
                            'spesifikasi' => $penerimaanMutasiGlobalDetail['spesifikasi'],
                            'unit_hasil_id' => $penerimaanMutasiGlobalDetail['unit_hasil_id'],
                            'kode_satuan' => $penerimaanMutasiGlobalDetail['kode_satuan'],
                            'spesifikasi_hasil_id' => $penerimaanMutasiGlobalDetail['spesifikasi_hasil_id'],
                            'qty' => (float)$penerimaanMutasiGlobalDetail['qty'],
                        ]
                    ]);
                } else if (!$isEdit) {
                    // INI TAMPILAN CREATE
                    array_push($barangResult, [
                        'no' => $no++,
                        'mutasi_global_id' => $m['mutasi_global_id'],
                        'mutasi_global_detail_id' => $m['id'],
                        'divisi_asal' => $m['divisi_asal'],
                        'warehouse_asal' => $m['warehouse_asal'],
                        'no_mutasi' => $m['no_mutasi'],
                        'supplier_name' => $d['supplier_name'],
                        'kode_barang' => $d['kode_barang'],
                        'barang_name' => $d['barang_name'],
                        'spesifikasi' => $d['spesifikasi'],
                        'qty_mutasi' => (float)$m['qty_konversi'],
                        'satuan_mutasi' => $m['satuan_konversi'],
                        'type_bc' => "BC 2.7",
                        'no_aju' => $m['no_aju'],
                        'no_daftar' => $m['no_daftar'],
                        "penerimaan" => [
                            'mutasi_global_id' => $m['mutasi_global_id'],
                            'mutasi_global_detail_id' => $m['id'],
                            'kode_barang' => null,
                            'barang_name' => null,
                            'spesifikasi' => null,
                            'unit_hasil_id' => null,
                            'kode_satuan' => null,
                            'spesifikasi_hasil_id' => null,
                            'qty' => null,
                        ]
                    ]);
                }
            }
        }

        return $barangResult;
    }

    public function getMutasiNo($mutasiIDArr)
    {
        $mutasiGlobalModel = new MutasiGlobalModel();
        $result = $mutasiGlobalModel->whereIn('id', $mutasiIDArr)->findAll();
        $response = array();
        foreach ($result as $r) {
            array_push($response, $r['no_mutasi']);
        }
        return $response;
    }


    public function getListNomorMutasi($companyPengirimId)
    {
        $mutasiGlobalModel = new MutasiGlobalModel();

        $listMutasi = $mutasiGlobalModel
            ->select("
            mutasi_global.id,
            mutasi_global.no_mutasi,
            SUM(mutasi_global_detail.qty_konversi) AS qty_konversi
        ")
            ->join('mutasi_global_detail', 'mutasi_global_detail.mutasi_global_id = mutasi_global.id', 'left')
            ->join('bc_27', 'bc_27.mutasi_global_id = mutasi_global.id AND bc_27.deletedAt IS NULL', 'left')
            ->where('mutasi_global.company_asal_id', $companyPengirimId)
            ->where('mutasi_global.deletedAt', null)
            ->where('mutasi_global.status_posting', 1)
            ->where('mutasi_global_detail.deletedAt', null)
            ->where('bc_27.penerimaan_otomatis', 1)
            ->groupBy('mutasi_global.id, mutasi_global.no_mutasi')
            ->having('COUNT(bc_27.id) >', 0) // hanya ambil yang sudah ada di bc27
            ->orderBy('mutasi_global.no_mutasi', 'ASC')
            ->findAll();

        return $listMutasi;
    }


    public function get_no(
        $month,
        $year,
        $companyId
    ) {
        $romanMonth = romanMonthNumber((int)$month);
        // Tentukan template berdasarkan company
        switch ($companyId) {
            case 1: // KIM 1 (FRZ)
                $numberTemplate = "/F/PMG/$romanMonth/" . substr($year, -2);
                break;
            case 2: // KIM 2
                $numberTemplate = "/PMG/$romanMonth/" . substr($year, -2);
                break;
            case 15: // GLOBAL
                $numberTemplate = "/G/PMG/$romanMonth/" . substr($year, -2);
                break;
            default: // OCS atau lainnya
                $numberTemplate = "/PMG/$romanMonth/" . substr($year, -2);
                break;
        }

        // Cari nomor terakhir berdasarkan template
        $lastData = $this->asArray()
            ->select('penerimaan_mutasi_no')
            ->where('company_penerima_id', $companyId)
            ->like('penerimaan_mutasi_no', $numberTemplate, 'before')
            ->where('deletedAt', null)
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



    public function getPenerimaanBarangListReportBc27($addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_aju'      => 'bc_27.no_aju'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_mutasi_global.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi_global.penerimaan_mutasi_no,
        penerimaan_mutasi_global.multiple_mutasi_global_id,
        penerimaan_mutasi_global_detail.stock_mutasi_global_id,
        penerimaan_mutasi_global.tanggal,
        penerimaan_mutasi_global_detail.mutasi_global_id,

        mutasi_global_detail.stock_id AS stock_id_asal,
        mutasi_global_detail.bc_id AS bc_id_asal,
        mutasi_global_detail.no_aju AS no_aju_asal,
        mutasi_global_detail.stock_dokumen AS stock_dokumen_asal,
        
        divisis.divisi AS divisi_penerima,
        warehouses.warehouse_name AS warehouse_penerima,    
           
        mutasi_global_detail.qty AS qty,    
        penerimaan_mutasi_global_detail.qty AS jml_masuk,

        mutasi_global.tanggal AS tanggal_bc, 

        bc_27.no_aju AS no_aju,    
        bc_27.no_daftar AS no_daftar,    
          
        stock.tipe_barang,    
        stock.barang1_id,    
        stock.barang2_id,    
        stock.kemasan_id,    
          
        ";

        $penerimaanMutasiGlobal = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id', 'left')
            ->join('penerimaan_mutasi_global_detail', 'penerimaan_mutasi_global_detail.penerimaan_mutasi_global_id = penerimaan_mutasi_global.id', 'left')
            ->join('mutasi_global', 'mutasi_global.id = penerimaan_mutasi_global_detail.mutasi_global_id', 'left')
            ->join('mutasi_global_detail', 'mutasi_global_detail.id = penerimaan_mutasi_global_detail.mutasi_global_detail_id', 'left')
            ->join('bc_27', 'bc_27.mutasi_global_id = penerimaan_mutasi_global_detail.mutasi_global_id', 'left')
            ->join('stock', 'stock.id = mutasi_global_detail.stock_id', 'left')

            ->where('penerimaan_mutasi_global.company_penerima_id', $addCondition['company_id'])
            // ->where('bc_27.company_asal_id', $addCondition['company_id'])
            ->where('penerimaan_mutasi_global.deletedAt', null)
            ->where('penerimaan_mutasi_global_detail.deletedAt', null)
            ->where('penerimaan_mutasi_global.status_posting', '1')

            ->orderBy($sort, $sortType);

        $totalData = $penerimaanMutasiGlobal->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanMutasiGlobal->where('mutasi_global.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->where('mutasi_global.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->groupEnd();
        }

        $totalFilteredData = $penerimaanMutasiGlobal->countAllResults(false);
        $data = $penerimaanMutasiGlobal->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPenerimaanBarangListReportBc27PDF($addCondition)
    {
        $availableSort = [
            'no_aju'      => 'bc_27.no_aju'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_mutasi_global.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_mutasi_global.penerimaan_mutasi_no,
        penerimaan_mutasi_global.multiple_mutasi_global_id,
        penerimaan_mutasi_global_detail.stock_mutasi_global_id,
        penerimaan_mutasi_global.tanggal,
        penerimaan_mutasi_global_detail.mutasi_global_id,

        mutasi_global_detail.stock_id AS stock_id_asal,
        mutasi_global_detail.bc_id AS bc_id_asal,
        mutasi_global_detail.no_aju AS no_aju_asal,
        mutasi_global_detail.stock_dokumen AS stock_dokumen_asal,
        
        divisis.divisi AS divisi_penerima,
        warehouses.warehouse_name AS warehouse_penerima,    
           
        mutasi_global_detail.qty AS qty,    
        penerimaan_mutasi_global_detail.qty AS jml_masuk,

        mutasi_global.tanggal AS tanggal_bc,

        bc_27.no_aju AS no_aju,    
        bc_27.no_daftar AS no_daftar,    
           
        stock.tipe_barang,    
        stock.barang1_id,    
        stock.barang2_id,    
        stock.kemasan_id,    
          
        ";

        $penerimaanMutasiGlobal = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id', 'left')
            ->join('penerimaan_mutasi_global_detail', 'penerimaan_mutasi_global_detail.penerimaan_mutasi_global_id = penerimaan_mutasi_global.id', 'left')
            ->join('mutasi_global', 'mutasi_global.id = penerimaan_mutasi_global_detail.mutasi_global_id', 'left')
            ->join('mutasi_global_detail', 'mutasi_global_detail.id = penerimaan_mutasi_global_detail.mutasi_global_detail_id', 'left')
            ->join('bc_27', 'bc_27.mutasi_global_id = mutasi_global.id', 'left')
            ->join('stock', 'stock.id = mutasi_global_detail.stock_id', 'left')

            ->where('penerimaan_mutasi_global.company_penerima_id', $addCondition['company_id'])
            ->where('penerimaan_mutasi_global.deletedAt', null)
            ->where('penerimaan_mutasi_global_detail.deletedAt', null)
            ->where('penerimaan_mutasi_global.status_posting', '1')

            ->orderBy($sort, $sortType);

        $totalData = $penerimaanMutasiGlobal->countAllResults(false);

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->groupStart();
        }


        if ($addCondition['dateStart']) {
            $penerimaanMutasiGlobal->where('mutasi_global.tanggal >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->where('mutasi_global.tanggal <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $penerimaanMutasiGlobal->groupEnd();
        }

        $totalFilteredData = $penerimaanMutasiGlobal->countAllResults(false);
        $data = $penerimaanMutasiGlobal->findAll();

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getFirstLikeByNoMutasi($noMutasi)
    {
        return $this->asArray()
            ->select('penerimaan_mutasi_global.*,divisis.divisi')
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->like('multiple_no_mutasi', $noMutasi)
            ->first();
    }
}
