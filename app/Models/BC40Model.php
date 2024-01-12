<?php

namespace App\Models;

use CodeIgniter\Model;

class BC40Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_40';
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
            'bc_40.bc_no_lokal'                      => 'bc_40.bc_no_lokal',
            'bc_40.createdAt'                        => 'bc_40.createdAt',
            'bc_40.no_aju'                           => 'bc_40.no_aju',
            'penerimaan_barang.no_penerimaan_barang' => 'penerimaan_barang.no_penerimaan_barang',
            'penerimaan_barang.warehouse_id'         => 'penerimaan_barang.warehouse_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_40.*,
            penerimaan_barang.no_penerimaan_barang,
            penerimaan_barang.id AS penerimaan_barang_id,
            penerimaan_barang.status_penerimaan,
            penerimaan_barang.tipe_bahan,
            warehouses.warehouse_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('penerimaan_barang', 'penerimaan_barang.id = bc_40.penerimaan_barang_id', 'right')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusBC'] || $addCondition['statusLPB'] || $addCondition['noBC40'] || $addCondition['noPenerimaanBarang'] || $addCondition['noAju'] && (empty($addCondition['mulaiTanggalBC40']) && empty($addCondition['selesaiTanggalBC40']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusBC']) {
            if ($addCondition['statusBC'] == "Belum Dibuat") {
                $bcDataQry->where('bc_40.id IS NULL');
            } elseif ($addCondition['statusBC'] == "Belum Lengkap") {
                $bcDataQry->where('bc_40.status_dokumen', "Belum Lengkap");
            } elseif ($addCondition['statusBC'] == "Siap Kirim") {
                $bcDataQry->where('bc_40.status_dokumen', "Siap Kirim");
            } else {
                $bcDataQry->where('bc_40.status_dokumen', "Sudah Kirim");
            }
        }

        if ($addCondition['statusLPB']) {
            if ($addCondition['statusLPB'] == "LOKAL BAKU") {
                $statusLPB = explode(' ', "LOKAL BAKU");
                $bcDataQry->where('status_penerimaan', $statusLPB[0]);
                $bcDataQry->where('tipe_bahan', $statusLPB[1]);
            } elseif ($addCondition['statusLPB'] == "LOKAL PENOLONG") {
                $statusLPB = explode(' ', "LOKAL PENOLONG");
                $bcDataQry->where('status_penerimaan', $statusLPB[0]);
                $bcDataQry->where('tipe_bahan', $statusLPB[1]);
            } elseif ($addCondition['statusLPB'] == "IMPORT BAKU") {
                $statusLPB = explode(' ', "IMPORT BAKU");
                $bcDataQry->where('status_penerimaan', $statusLPB[0]);
                $bcDataQry->where('tipe_bahan', $statusLPB[1]);
            } elseif ($addCondition['statusLPB'] == "IMPORT PENOLONG") {
                $statusLPB = explode(' ', "IMPORT PENOLONG");
                $bcDataQry->where('status_penerimaan', $statusLPB[0]);
                $bcDataQry->where('tipe_bahan', $statusLPB[1]);
            }
        }

        if ($addCondition['noBC40']) {
            $bcDataQry->like('bc_no_lokal', $addCondition['noBC40']);
        }

        if ($addCondition['noPenerimaanBarang']) {
            $bcDataQry->like('no_penerimaan_barang', $addCondition['noPenerimaanBarang']);
        }

        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju']);
        }

        if ($addCondition['statusBC'] || $addCondition['statusLPB'] || $addCondition['noBC40'] || $addCondition['noPenerimaanBarang'] || $addCondition['noAju'] && (empty($addCondition['mulaiTanggalBC40']) && empty($addCondition['selesaiTanggalBC40']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC40'] && $addCondition['selesaiTanggalBC40']) {
            $bcDataQry->groupStart();
            $mulaiTanggalBC40Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalBC40']), "Y-m-d");
            $selesaiTanggalBC40Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalBC40']), "Y-m-d");

            if ($addCondition['mulaiTanggalBC40']) {
                $bcDataQry->where('bc_40.createdAt >=', $mulaiTanggalBC40Timestamp);
            }

            if ($addCondition['selesaiTanggalBC40']) {
                $bcDataQry->where('bc_40.createdAt <=', $selesaiTanggalBC40Timestamp);
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
}
