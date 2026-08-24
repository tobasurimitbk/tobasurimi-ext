<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class FormRequestActionModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'form_request_action';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = false;
    protected $allowedFields = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';
    protected $deletedField = 'deletedAt';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getList(
        $condition,
        $addCondition,
        $limit = 10,
        $offset = 0
    ) {
        $availableSort = [
            'id' => 'id',
            'nomor' => 'nomor',
            'judul_form' => 'judul_form',
            'dibuat_oleh' => 'dibuat_oleh',
            'status_print' => 'status_print',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];
        $sort = $availableSort[$addCondition['sort'] ?? 'id'] ?? 'id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            form_request_action.*,
            users.name AS last_update
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('users', 'users.id = form_request_action.last_update_by', 'left')
            ->where($condition);

        $totalData = $dataQry->countAllResults(false);

        if (!empty($addCondition['search'])) {
            $search = $this->db->escapeLikeString(trim($addCondition['search']));
            $dataQry->groupStart()
                ->like('form_request_action.nomor', $search)
                ->orLike('users.name', $search)
                ->orLike('judul_form', $search)
                ->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->orderBy($sort, $sortType)->findAll($limit, $offset);

        return [
            'data' => $data,
            'totalData' => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort' => $sort,
            'sortType' => $sortType
        ];
    }

    public function getNo($companyId, $yearMonth)
    {
        // Validasi format
        if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            throw new Exception("Format yearMonth harus YYYY-MM");
        }

        $yearMonthArr = explode('-', $yearMonth);
        $year = $yearMonthArr[0];
        $month = $yearMonthArr[1];

        // Cek company
        $companyModel = new CompaniesModel();
        $company = $companyModel->asArray()->where('id', $companyId)->first();
        if (!$company) {
            throw new Exception("Company ID $companyId not found");
        }

        // Tentukan prefix
        $prefixKode = in_array($companyId, [1, 2]) ? "TSI" : $company['company'];

        // Ambil data untuk tahun DAN bulan tertentu
        $existingData = $this->asArray()
            ->where('year', $year)
            ->where('deletedAt', null)
            ->where('company_id', $companyId)
            ->orderBy('nomor', 'asc')
            ->findAll();

        // Ambil array nomor
        $nomorArr = array_column($existingData, 'nomor');

        // Cari nomor terbesar yang sudah ada
        $maxNomor = 0;
        if (!empty($nomorArr)) {
            // Ambil 3 digit pertama dari nomor (contoh: 003/RA/TSI/08/2026 -> 003)
            foreach ($nomorArr as $nomor) {
                $parts = explode('/', $nomor);
                $urutanAngka = (int) $parts[0]; // Convert ke integer
                if ($urutanAngka > $maxNomor) {
                    $maxNomor = $urutanAngka;
                }
            }
            $urutan = $maxNomor + 1;
        } else {
            $urutan = 1;
        }

        $nomorUrut = str_pad($urutan, 3, '0', STR_PAD_LEFT);

        // Format: 003/RA/TSI/08/2026
        $nomorBaru = "{$nomorUrut}/RA/{$prefixKode}/{$month}/{$year}";

        return $nomorBaru;
    }
}
