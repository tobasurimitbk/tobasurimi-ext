<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'supplier_id',
        'no_penerimaan_barang',
        'acceptance_type',
        'multiple_po_id',
        'multiple_po_no',
        'aju_document_type',
        'aju_no',
        'validation_date',
        'no_registration',
        'letter_no',
        'invoice_no',
        'packaging',
        'total_weight',
        'shipping_cost',
        'createdAt',
        'updatedAt',
        'deletedAt',
        'ppnbm',
        'tipe_bahan',
        'biaya_masuk',
        'status_post',
        'status_penerimaan'
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

    public function getPenerimaanBarangList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_penerimaan_barang'      => 'penerimaan_barang.no_penerimaan_barang',
            'no_po'                     => 'penerimaan_barang.multiple_po_no',
            'acceptance_type'           => 'penerimaan_barang.acceptance_type',
            'aju_type'                  => 'metadata.value',
            'aju_no'                    => 'penerimaan_barang.aju_no',
            'validation_date'           => 'penerimaan_barang.validation_date',
            'sender_name'               => 'suppliers.name',
            'createdAt'                 => 'penerimaan_barang.createdAt',
            'updatedAt'                 => 'penerimaan_barang.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "penerimaan_barang.*, metadata.value as aju_type, suppliers.name as sender_name";
        $penerimaanBarangDataQry = $this->asObject()
            ->select($selectQry)
            ->join('metadata', 'metadata.id = penerimaan_barang.aju_document_type')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $penerimaanBarangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['status'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $penerimaanBarangDataQry->like('penerimaan_barang.no_penerimaan', $addCondition['search']);
        }

        if ($addCondition['status']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.status_post', $addCondition['status']);
        }

        if ($addCondition['startdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.validation_date >=', $addCondition['startdate']);
        }

        if ($addCondition['lastdate']) {
            $penerimaanBarangDataQry->where('penerimaan_barang.validation_date <=', $addCondition['lastdate']);
        }

        if ($addCondition['search'] || $addCondition['status'] || $addCondition['startdate'] || $addCondition['lastdate']) {
            $penerimaanBarangDataQry->groupEnd();
        }
        
        $totalFilteredData = $penerimaanBarangDataQry->countAllResults(false);
        $data = $penerimaanBarangDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function get_no($tgl, $bln, $thn, $last_day)
    {
        $filt_no = "LPB/1/" . $thn . "/" . $bln;

        $conditions = [
            'deletedAt' => null
        ];

        $no = $this->db->table('penerimaan_barang')
        ->where('createdAt >=', $thn . "-" . $bln . "-" . $tgl . " 00:00:00")
        ->where('createdAt <=', $last_day . " 23:59:59")
        ->where($conditions)
        ->countAllResults(false) + 1;

        if ($no != '') {
            $filt_no = "LPB/". $no . "/" . $thn . "/" . $bln;
        }
        return $filt_no;
    }
}