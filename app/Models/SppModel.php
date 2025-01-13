<?php

namespace App\Models;

use CodeIgniter\I18n\Time;

use CodeIgniter\Model;

class SppModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'purchase_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'user_id',
        'request_date',
        'spp_no',
        'spp_type',
        'divisi_id',
        'note',
        'is_posted',
        'request_status',
    ];

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

    public function getSppList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sppType'          => 'purchase_requests.spp_type',
            'sppNo'            => 'purchase_requests.spp_no',
            'divisi'            => 'divisis.divisi',
            'company'            => 'companies.company',
            'requestDate'      => 'purchase_requests.request_date',
            'is_posted' => 'purchase_requests.is_posted'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'purchase_requests.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "purchase_requests.*,
                    divisis.divisi AS divisiName, 
                    companies.company AS companyName, 
                    COUNT(purchase_request_details.id) AS itemCount";

        $purchaseRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'purchase_requests.divisi_id = divisis.id', 'left')
            ->join('companies', 'purchase_requests.company_id = companies.id', 'left')
            ->join('purchase_request_details', 'purchase_requests.id = purchase_request_details.purchase_request_id', 'left')
            ->groupBy(('purchase_requests.id'))
            ->orderBy($sort, $sortType);

        $totalData = $purchaseRequestsDataQry->countAllResults(false);

        if ($addCondition['is_posted']) {
            if ($addCondition['is_posted'] == "SUDAH POSTING") {
                $purchaseRequestsDataQry->where('is_posted', 1);
            } else {
                $purchaseRequestsDataQry->where('is_posted', 0);
            }
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $purchaseRequestsDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $purchaseRequestsDataQry
                ->like('spp_no', $addCondition['search']);
        }

        if ($addCondition['spp_type']) {
            $purchaseRequestsDataQry
                ->like('spp_type', $addCondition['spp_type']);
        }

        if ($addCondition['dateStart']) {
            $purchaseRequestsDataQry->where('purchase_requests.request_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $purchaseRequestsDataQry->where('purchase_requests.request_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $purchaseRequestsDataQry->groupEnd();
        }

        $totalFilteredData = $purchaseRequestsDataQry->countAllResults(false);
        $data = $purchaseRequestsDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSppById($id)
    {
        $selectQry = "purchase_requests.*,
        divisis.divisi AS divisiName,
        companies.company AS companyName,
        users.name AS createdByName
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'purchase_requests.divisi_id = divisis.id', 'left')
            ->join('companies', 'purchase_requests.company_id = companies.id', 'left')
            ->join('users', 'purchase_requests.user_id = users.id', 'left')
            ->find($id);

        return $sppData;
    }

    public function getNoSPP($type)
    {
        // is posted 0 artinya spp masih open 
        // request staatus waiting artinya di po belum ada yang menggunakan nomor spp tersebut
        $arrCondition = [
            'deletedAt' => null,
            'spp_type' => $type,
            'is_posted' => 0,
            'request_status' => 'waiting'
        ];

        $builder = $this->db->table('purchase_requests');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function generateNoSpp($divisi, $companyId)
    {
        $romanNumb = [
            'I',
            'II',
            'III',
            'IV',
            'V',
            'VI',
            'VII',
            'VIII',
            'IX',
            'X',
            'XI',
            'XII',
        ];

        $today = Time::today('America/Chicago', 'en_US');

        $year = $today->getYear();
        $month = $today->getMonth() - 1;

        $divisi = str_replace(' ', '', $divisi);

        $lastStr =  $divisi . '/' . $romanNumb[$month] . '/' . $year;

        $builder = $this->db->table('purchase_requests');
        $builder->select('spp_no');
        $builder->orderBy('spp_no', 'desc');
        $builder->where('company_id', $companyId);
        $builder->like('spp_no', $lastStr);
        $query = $builder->get();

        $increment = '01';

        if ($query->getResultArray()) {
            $lastSpp = explode('/', $query->getResultArray()[0]['spp_no']);
            $lastSpp = intval($lastSpp[0]) + 1;

            if ($lastSpp < 10) {
                $lastSpp = "0" . $lastSpp . "";
            } else {
                $lastSpp = strval($lastSpp);
            }

            $increment = $lastSpp;
        };



        $generatedSppNo = $increment . '/' . $lastStr;

        return $generatedSppNo;
    }
}
