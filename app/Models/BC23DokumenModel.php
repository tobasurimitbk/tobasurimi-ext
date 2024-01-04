<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23DokumenModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23_dokumen';
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
            'bc_23_dokumen.nomor_dokumen' => 'bc_23_dokumen.nomor_dokumen',
            'bc_23_dokumen.seri_dokumen' => 'bc_23_dokumen.seri_dokumen',
            'bc_23_dokumen.tanggal_dokumen' => 'bc_23_dokumen.tanggal_dokumen',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'bc_23_dokumen.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_23_dokumen.*";

        $pinjamanQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $pinjamanQry->countAllResults(false);

        $totalFilteredData = $pinjamanQry->countAllResults(false);
        $data = $pinjamanQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function get($bc23ID)
    {
        $result = [];
        foreach ($this->where('bc_23_id', $bc23ID)->where('deletedAt', null)->findAll() as $r) {
            $result[] = [
                'id' => $r['id'],
                'dokumen_pelengkap_kode_dokumen' => "Dokumen Invoice (3)",
                'dokumen_pelengkap_nomor_dokumen' => $r['kode_dokumen'],
                'dokumen_pelengkap_seri_dokumen' => $r['seri_dokumen'],
                'dokumen_pelengkap_tanggal_dokumen' => date('d/m/Y', strtotime($r['tanggal_dokumen']))
            ];
        }
        return $result;
    }
}
