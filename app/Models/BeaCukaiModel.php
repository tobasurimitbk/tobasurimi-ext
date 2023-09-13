<?php

namespace App\Models;

use CodeIgniter\Model;

class BeaCukaiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bea_cukai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'type',
        'status',
        'status_perbaikan',
        'aju_no',
        'registration_no',
        'registration_date',
        'kppbc_bongkar',
        'kppbc_pengawas',
        'tujuan_tpb',
        'supplier_id',
        'importir_npwp',
        'importir_name',
        'tpb_no',
        'importir_api',
        'importir_address',
        'pemilik_barang',
        'pemilik_barang_npwp',
        'pemilik_barang_name',
        'pemilik_barang_address',
        'pemilik_barang_api',
        'ppjk_npwp',
        'ppjk_name',
        'ppjk_date',
        'ppjk_no',
        'ppjk_address',
        'pengangkutan',
        'pengangkutan_sarana',
        'voy_no',
        'pengangkutan_country',
        'kode_pelabuhan_muat',
        'kode_pelabuhan_transit',
        'kode_pelabuhan_bongkar',
        'invoice_id',
        'invoice_date',
        'fasilitas_import_no',
        'fasilitas_import_date',
        'fasilitas_import_code',
        'lc_no',
        'lc_date',
        'bl_no',
        'bl_date',
        'bc_11_no',
        'bc_11_date',
        'bc_11_zip',
        'penimbunan',
        'valuta',
        'ndpbm',
        'fob',
        'freight',
        'asuransi_type',
        'cif_value',
        'cif_price',
        'bruto',
        'netto',
        'item_count',
        'tempat',
        'tanggal',
        'pemberitahu',
        'jabatan',
        'data_dokumen',
        'data_kontainer',
        'data_kemasan',
        'status_posting'
    ];

    // Dates
    protected $useTimestamps = true;
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
            'ajuNo'            => 'bea_cukai.aju_no',
            'registrationNo'   => 'bea_cukai.registration_no',
            'registrationDate' => 'bea_cukai.registration_date',
            'tujuanTPBName'    => 'metadata.value',
            'statusPosting'    => 'bea_cukai.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'bea_cukai.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bea_cukai.*, 
            metadata.value AS tujuan_tpb_name";

        $bbLokalDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'bea_cukai.tujuan_tpb = metadata.id', 'left')
            ->groupBy(('bea_cukai.id'))
            ->orderBy($sort, $sortType);

        $totalData = $bbLokalDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $bbLokalDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $bbLokalDataQry
                ->like('aju_no', $addCondition['search']);
        }
        if ($addCondition['search']) {
            $bbLokalDataQry->groupEnd();
        }

        $totalFilteredData = $bbLokalDataQry->countAllResults(false);
        $data = $bbLokalDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}