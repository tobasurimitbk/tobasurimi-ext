<?php

namespace App\Models;

use CodeIgniter\Model;

class BC27Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_27';
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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'bc_27.createdAt'                        => 'bc_27.createdAt',
            'mutasi_global.company_asal_id' => 'mutasi_global.company_asal_id',
            'mutasi_global.divisi_asal_id' => 'mutasi_global.divisi_asal_id',
            'mutasi_global.warehouse_asal_id' => 'mutasi_global.warehouse_asal_id',
            'mutasi_global.company_tujuan_id' => 'mutasi_global.company_tujuan_id',
            'bc_27.no_aju' => 'bc_27.no_aju',
            'bc_27.mutasi_global_id' => 'bc_27.mutasi_global_id',
            'bc_27.status_posting'                   => 'bc_27.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'bc_27.createdAt'] ?? 'bc_27.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_27.*,
            mutasi_global.no_mutasi,
            tb_company_asal.company AS company_asal,
            tb_company_tujuan.company AS company_tujuan,
            divisis.divisi AS divisi_asal,
            warehouses.warehouse_name AS warehouse_asal";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('mutasi_global', 'mutasi_global.id = bc_27.mutasi_global_id', 'left')
            ->join('companies tb_company_asal', 'tb_company_asal.id = bc_27.company_asal_id', 'left')
            ->join('companies tb_company_tujuan', 'tb_company_tujuan.id = bc_27.company_tujuan_id', 'left')
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if (
            $addCondition['statusPosting'] ||
            $addCondition['search'] ||
            $addCondition['mulaiTanggalBC27'] ||
            $addCondition['selesaiTanggalBC27']
        ) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusPosting']) {
            if ($addCondition['statusPosting'] == "ALL") {
                $bcDataQry->whereIn('bc_27.status_posting', ['1', '0']);
            } elseif ($addCondition['statusPosting'] == "SUDAH POSTING") {
                $bcDataQry->where('bc_27.status_posting', "1");
            } else if ($addCondition['statusPosting'] == "BELUM POSTING") {
                $bcDataQry->where('bc_27.status_POSTING', "0");
            }
        }

        if ($addCondition['search']) {
            $bcDataQry->like('bc_27.no_aju', $addCondition['search'])
                ->orLike('bc_27.no_daftar', $addCondition['search'])
                ->orLike('mutasi_global.no_mutasi', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->orLike('warehouses.warehouse_name', $addCondition['search'])
                ->orLike('tb_company_tujuan.company', $addCondition['search']);
        }

        if ($addCondition['mulaiTanggalBC27']) {
            $bcDataQry->where('bc_27.createdAt >=', $addCondition['mulaiTanggalBC27']);
        }

        if ($addCondition['selesaiTanggalBC27']) {
            $bcDataQry->where('bc_27.createdAt <=', $addCondition['selesaiTanggalBC27']);
        }


        if (
            $addCondition['statusPosting'] ||
            $addCondition['search'] ||
            $addCondition['mulaiTanggalBC27'] ||
            $addCondition['selesaiTanggalBC27']
        ) {
            $bcDataQry->groupEnd();
        }


        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }


    public function getListOutstanding($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $mutasiGlobalDetailModel = new MutasiGlobalDetailModel();

        $availableSort = [
            'mutasi_global.no_mutasi' => 'mutasi_global.no_mutasi',
            'mutasi_global.company_asal_id' => 'mutasi_global.company_asal_id',
            'mutasi_global.divisi_asal_id' => 'mutasi_global.divisi_asal_id',
            'mutasi_global.warehouse_asal_id' => 'mutasi_global.warehouse_asal_id',
            'mutasi_global.company_tujuan_id' => 'mutasi_global.company_tujuan_id',
            'mutasi_global.tanggal' => 'mutasi_global.tanggal',
            'barang_master.kode_barang' => 'barang_master.kode_barang',
            'barang_master.barang_name' => 'barang_master.barang_name',
            'barang_master_spesifikasi.spesifikasi' => 'barang_master_spesifikasi.spesifikasi',
            'mutasi_global_detail.qty_konversi' => 'mutasi_global_detail.qty_konversi',
            'mutasi_global_detail.unit_id_konversi' => 'mutasi_global_detail.unit_id_konversi',
            'stock_revamp_detail.type_bc' => 'stock_revamp_detail.type_bc',
            'bc_purchase_order.no_aju' => 'bc_purchase_order.no_aju',
            'bc_purchase_order.no_daftar' => 'bc_purchase_order.no_daftar'
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'mutasi_global.tanggal'] ?? 'mutasi_global.tanggal';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "mutasi_global_detail.*,
            mutasi_global.no_mutasi,
            tb_company_asal.company AS company_asal,
            divisis.divisi AS divisi_asal,
            warehouses.warehouse_name AS warehouse_asal,
            tb_company_tujuan.company AS company_tujuan,
            mutasi_global.tanggal,
            barang_master.kode_barang,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan,
            stock_revamp_detail.type_bc,
            bc_purchase_order.no_aju AS no_aju,
            bc_purchase_order.no_daftar AS no_daftar";

        $bcDataQry = $mutasiGlobalDetailModel
            ->asObject()
            ->select($selectQry)
            ->where($condition)
            ->groupStart()
            ->where('bc_27.deletedAt IS NOT NULL')
            ->orWhere('bc_27.mutasi_global_id IS NULL')
            ->groupEnd()
            ->join('mutasi_global', 'mutasi_global.id = mutasi_global_detail.mutasi_global_id', 'left')
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id', 'left')
            ->join('companies tb_company_asal', 'tb_company_asal.id = mutasi_global.company_asal_id', 'left')
            ->join('companies tb_company_tujuan', 'tb_company_tujuan.id = mutasi_global.company_tujuan_id', 'left')
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = mutasi_global_detail.stock_detail_id', 'left')
            ->join('stock_revamp', 'stock_revamp.id = stock_revamp_detail.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
            // asumsikan dari bc 40 dan 23 dulu aja yha pemasukkannnya
            ->join('penerimaan_barang', 'penerimaan_barang.id = stock_revamp_detail.reference_id', 'left')
            ->join('bc_purchase_order_lpb', 'bc_purchase_order_lpb.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_purchase_order_lpb.bc_purchase_order_id', 'left')
            ->join('bc_27', 'bc_27.mutasi_global_id = mutasi_global.id', 'left')
            ->join('satuans', 'satuans.id = mutasi_global_detail.unit_id_konversi', 'left')
            ->groupBy('mutasi_global_detail.id')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $bcDataQry->like('bc_purchase_order.no_aju', $addCondition['search'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['search'])
                ->orLike('mutasi_global.no_mutasi', $addCondition['search'])
                ->orLike('barang_master.kode_barang', $addCondition['search'])
                ->orLike("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi)", $addCondition['search']);
        }

        if ($addCondition['search']) {
            $bcDataQry->groupEnd();
        }

        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getBC27($id)
    {
        $result = $this->asArray()
            ->select('bc_27.*, mutasi_global.no_mutasi, divisis.divisi, warehouses.warehouse_name')
            ->join('mutasi_global', 'mutasi_global.id = bc_27.mutasi_global_id', 'left')
            ->join('divisis', 'mutasi_global.divisi_asal_id = divisis.id', 'left')
            ->join('warehouses', 'mutasi_global.warehouse_asal_id = warehouses.id', 'left')
            ->where('bc_27.id', $id)
            ->first();
        return $result;
    }

    public function isCompleteFormHeader($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kodeTujuanPengiriman != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }
    public function isCompleteFormEntitas($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->entitas) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormDokumen($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->dokumen) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPengangkut($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->pengangkut) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPetiKemas($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->kemasan) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormTransaksi($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kodeValuta) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormBarang($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->barang) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPernyataan($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kotaTtd != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }


    public function dropdownKemasan($mutasiGlobalId)
    {
        $mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $selectQry = "
            mutasi_global_detail.*, 
            stock.tipe_barang, 
            stock.barang2_id,
            barang_master.kode_barang,
            CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang, 
        ";

        $dataList = $mutasiGlobalDetailModel
            ->select($selectQry)
            ->join('stock', 'stock.id = mutasi_global_detail.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->where('mutasi_global_id', $mutasiGlobalId)
            ->where('stock.tipe_barang', "bahan_penolong") // KEMASAN AMBIL DARI BAHAN PENOLONG 
            ->findAll();

        return $dataList;
    }

    public function barang($mutasiGlobalId)
    {
        $mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $detailBarang = $mutasiGlobalDetailModel->getMutasiDetail($mutasiGlobalId);
        $result = [];
        foreach ($detailBarang as $item) {
            $key = $item['barang1_id'] . '-' . $item['kemasan_id'];
            if (!isset($result[$key])) {
                $result[$key] = $item;
                $result[$key]['total_harga'] = (int)$item['total_harga'];
                $result[$key]['qty'] = (int)$item['qty'];
            } else {
                $result[$key]['total_harga'] += (int)$item['total_harga'];
                $result[$key]['qty'] += (int)$item['qty'];
            }
        }

        return array_values($result);
    }
    public function detailBarang($bcId, $kodeBarang, $mutasiGlobalId)
    {
        $listBarang = $this->barang($mutasiGlobalId);
        $payload = json_decode($this->find($bcId)['payload']);
        $result = null;

        foreach ($listBarang as $l) {
            if ($kodeBarang == $l['kode_barang']) {
                $result = [
                    'barangDetail' => $l,
                    'bcDetail' => null
                ];
            }
        }

        foreach ($payload->barang as $b) {
            if ($b->kodeBarang == $result['barangDetail']['kode_barang']) {
                $result['bcDetail'] = $b;
            }
        }

        return $result;
    }
}
