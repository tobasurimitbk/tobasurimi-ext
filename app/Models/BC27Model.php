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
            'mutasi_global.divisi_asal_id'           => 'mutasi_global.divisi_asal_id',
            'mutasi_global.warehouse_asal_id'        => 'mutasi_global.warehouse_asal_id',
            'bc_27.no_daftar'                        => 'bc_27.no_daftar',
            'bc_27.company_tujuan_id'                => 'bc_27.company_tujuan_id',
            'mutasi_global.no_mutasi'                => 'mutasi_global.no_mutasi',
            'bc_27.createdAt'                        => 'bc_27.createdAt',
            'bc_27.no_aju'                           => 'bc_27.no_aju',
            'bc_27.status_posting'                   => 'bc_27.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'bc_27.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_27.*,
            mutasi_global.no_mutasi,
            companies.company,
            divisis.divisi,
            warehouses.warehouse_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('mutasi_global', 'mutasi_global.id = bc_27.mutasi_global_id', 'left')
            ->join('companies', 'companies.id = bc_27.company_tujuan_id', 'left')
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusPosting'] || $addCondition['noAju'] || $addCondition['noBC27'] && (empty($addCondition['mulaiTanggalBC27']) && empty($addCondition['selesaiTanggalBC27']))) {
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

        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju'])->orLike('no_daftar', $addCondition['noAju']);
        }

        if ($addCondition['statusPosting'] || $addCondition['noAju'] || $addCondition['noBC27'] && (empty($addCondition['mulaiTanggalBC27']) && empty($addCondition['selesaiTanggalBC27']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC27'] && $addCondition['selesaiTanggalBC27']) {
            $bcDataQry->groupStart();
            $mulaiTanggalBC27Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalBC27']), "Y-m-d");
            $selesaiTanggalBC27Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalBC27']), "Y-m-d");

            if ($addCondition['mulaiTanggalBC27']) {
                $bcDataQry->where('bc_27.createdAt >=', $mulaiTanggalBC27Timestamp);
            }

            if ($addCondition['selesaiTanggalBC27']) {
                $bcDataQry->where('bc_27.createdAt <=', $selesaiTanggalBC27Timestamp);
            }

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
            if (count($payload->kontainer) != 0 && count($payload->kemasan) != 0) {
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
}
