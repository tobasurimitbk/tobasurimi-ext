<?php

namespace App\Models;

use CodeIgniter\Model;

class OtherPaymentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'other_payment';
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
            'divisi_id' => 'divisi_id',
            'no_pembayaran' => 'no_pembayaran',
            'tanggal' => 'tanggal',
            'valas' => 'valas',
            'metode_pembayaran' => 'metode_pembayaran',
            'nominal_pembayaran' => 'nominal_pembayaran',
            'bayar_ke' => "bayar_ke"
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            other_payment.*,
            divisis.divisi,
            metadata.value AS valas_name,
            SUM(other_payment_detail.nominal) AS nominal_all
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = other_payment.divisi_id')
            ->join('metadata', 'metadata.id = other_payment.valas')
            ->join('other_payment_detail', 'other_payment_detail.other_payment_id = other_payment.id', 'left')
            ->where($condition)
            ->where('other_payment_detail.deletedAt', null)
            ->groupBy('other_payment.id, divisis.divisi, metadata.value')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry
                ->like('tanggal', $addCondition['search'])
                ->orLike('divisi', $addCondition['search'])
                ->orLike('no_pembayaran', $addCondition['search'])
                ->orLike('tanggal', $addCondition['search'])
                ->orLike('bayar_ke', $addCondition['search']);
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] != "ALL") {
                $addCondition['status_posting'] = $addCondition['status_posting'] == "SUDAH POSTING" ? '1' : '0';
                $dataQry->where('other_payment.status_posting', $addCondition['status_posting']);
            }
        }

        // date filter start
        if ($addCondition['startDate']) {
            $dataQry->where('other_payment.tanggal >=', $addCondition['startDate']);
        }

        if ($addCondition['lastDate']) {
            $dataQry->where('other_payment.tanggal <=', $addCondition['lastDate']);
        }

        if ($addCondition['search']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
