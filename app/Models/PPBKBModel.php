<?php

namespace App\Models;

use CodeIgniter\Model;

class PPBKBModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'ppbkb';
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
            'mutasi.divisi_asal_id'           => 'mutasi.divisi_asal_id',
            'mutasi.warehouse_asal_id'        => 'mutasi.warehouse_asal_id',
            'mutasi.no_mutasi'                => 'mutasi.no_mutasi',
            'ppbkb.no_ppbkb'                  => 'ppbkb.no_ppbkb',
            'ppbkb.tanggal'                   => 'ppbkb.tanggal',
            'ppbkb.status_posting'            => 'ppbkb.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'ppbkb.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "ppbkb.*,
            mutasi.divisi_tujuan_id,
            mutasi.warehouse_tujuan_id,
            mutasi.no_mutasi,
            divisis.divisi AS divisi_asal_name,
            warehouses.warehouse_name AS warehouse_asal_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('mutasi', 'mutasi.id = ppbkb.mutasi_id', 'left')
            ->join('divisis', 'divisis.id = mutasi.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi.warehouse_asal_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusPosting'] || $addCondition['noPPBKB'] && (empty($addCondition['mulaiTanggalPPBKB']) && empty($addCondition['selesaiTanggalPPBKB']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusPosting']) {
            if ($addCondition['statusPosting'] == "ALL") {
                $bcDataQry->whereIn('ppbkb.status_posting', ['1', '0']);
            } elseif ($addCondition['statusPosting'] == "SUDAH POSTING") {
                $bcDataQry->where('ppbkb.status_posting', "1");
            } else if ($addCondition['statusPosting'] == "BELUM POSTING") {
                $bcDataQry->where('ppbkb.status_posting', "0");
            }
        }

        if ($addCondition['noPPBKB']) {
            $bcDataQry->like('no_ppbkb', $addCondition['noPPBKB'])->orLike('no_daftar', $addCondition['no_daftar'])->orLike('no_mutasi', $addCondition['noPPBKB']);
        }


        if ($addCondition['statusPosting'] || $addCondition['noPPBKB'] && (empty($addCondition['mulaiTanggalPPBKB']) && empty($addCondition['selesaiTanggalPPBKB']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalPPBKB'] && $addCondition['selesaiTanggalPPBKB']) {
            $bcDataQry->groupStart();
            $mulaiTanggalPPBKBTimestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalPPBKB']), "Y-m-d");
            $selesaiTanggalPPBKBTimestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalPPBKB']), "Y-m-d");

            if ($addCondition['mulaiTanggalPPBKB']) {
                $bcDataQry->where('ppbkb.createdAt >=', $mulaiTanggalPPBKBTimestamp);
            }

            if ($addCondition['selesaiTanggalPPBKB']) {
                $bcDataQry->where('ppbkb.createdAt <=', $selesaiTanggalPPBKBTimestamp);
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

    public function getDetail($id)
    {

        $selectQry = "ppbkb.*,
        mutasi.divisi_asal_id,
        mutasi.divisi_tujuan_id,
        mutasi.divisi_tujuan_id,
        mutasi.warehouse_tujuan_id,
        mutasi.no_mutasi,
        divisis.divisi AS divisi_asal_name,
        warehouses.warehouse_name AS warehouse_asal_name";

        $result = $this->asArray()
            ->select($selectQry)
            ->join('mutasi', 'mutasi.id = ppbkb.mutasi_id', 'left')
            ->join('divisis', 'divisis.id = mutasi.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi.warehouse_asal_id', 'left')
            ->where('ppbkb.id', $id)
            ->first();

        return $result;
    }

    public function getNo($companyId)
    {
        $builder = $this->db->table('ppbkb');
        $builder->select('no_ppbkb');
        $builder->orderBy('no_ppbkb', 'desc');
        $builder->where('ppbkb.company_id', $companyId);
        $query = $builder->get();
        $lastPenerimaan = 0;

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $row) {
                $number = $row['no_ppbkb'];
                if ($number > $lastPenerimaan) {
                    $lastPenerimaan = $number;
                }
            }
            $lastPenerimaan++;
        } else {
            $lastPenerimaan = 1;
        }
        $formattedLastPenerimaan = sprintf("%05d", $lastPenerimaan);
        return $formattedLastPenerimaan;
    }
}
