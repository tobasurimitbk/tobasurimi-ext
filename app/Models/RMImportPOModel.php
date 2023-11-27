<?php

namespace App\Models;

use CodeIgniter\Model;

class RMImportPOModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_import_pos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'company_id', 'supplier_id', 'division_id', 'po_no', 'po_date', 'payment_date',
        'currency',  'total', 'payment_term', 'note', 'shipper', 'consigne', 'port_origin',
        'port_destination', 'location_transaction', 'shipment', 'latest_shipment_date', 'attn', 'createdBy', 'status_penerimaan', 'is_posted',
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

    public function getPOList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'poDate'           => 'rm_import_pos.po_date',
            'poNo'             => 'rm_import_pos.po_no',
            'supplierName'      => 'suppliers.name',
            'total'             => 'rm_import_pos.total',
            'currencyName'      => 'metadata.value',
            'createdAt'         => 'rm_import_pos.createdAt',
            'updatedAt'         => 'rm_import_pos.updatedAt',
            'statusPenerimaan'  => 'rm_import_pos.status_penerimaan',
            'companyName'       => 'companies.company'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'rm_import_pos.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "rm_import_pos.*, 
                      suppliers.name AS supplierName, 
                      metadata.value AS currencyName,
                      companies.company AS companyName,
                      COUNT(rm_import_po_details.id) AS itemCount";
        $poDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
            ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
            ->join('companies', 'companies.id = rm_import_pos.company_id', 'left')
            ->join('rm_import_po_details', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
            ->groupBy(('rm_import_pos.id'))
            ->orderBy($sort, $sortType);

        $totalData = $poDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $poDataQry->like('rm_import_pos.po_no', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $poDataQry->where('rm_import_pos.po_date >=', $addCondition['dateStart']);
        }

        if ($addCondition['dateEnd']) {
            $poDataQry->where('rm_import_pos.po_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $poDataQry->groupEnd();
        }

        $totalFilteredData = $poDataQry->countAllResults(false);
        $data = $poDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getPOById($id)
    {
        $selectQry = "rm_import_pos.*,
        divisis.divisi AS divisiName,
        suppliers.name AS supplierName,
        suppliers.address AS supplierAddress,
        suppliers.phone AS supplierPhone,
        suppliers.fax AS supplierFax,
        suppliers.no_npwp AS supplierNPWP,
        users.name AS createdByName,
        companies.company as companyName,
        metadata.value as currencyName
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = rm_import_pos.division_id', 'left')
            ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
            ->join('users', 'users.id = rm_import_pos.createdBy', 'left')
            ->join('companies', 'companies.id = rm_import_pos.company_id', 'left')
            ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
            ->find($id);

        return $sppData;
    }

    public function getNoPenerimaanBarang($supplier_id, $company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'supplier_id' => $supplier_id,
            'is_posted' => 1,
            'status_penerimaan' => 0,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('rm_import_pos');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getNoPOBeaCukai($company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'is_posted' => 1,
            'status_penerimaan' => 1,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('rm_import_pos');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function get_no($tgl, $bln, $thn, $divisi, $thn2, $divisi_id, $last_day)
    {
        $lastStr =  $tgl . $bln . $thn;

        $builder = $this->db->table('rm_import_pos');
        $builder->select('po_no');
        $builder->orderBy('po_no', 'desc')
            ->where('division_id', $divisi_id)
            ->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('po_no', $lastStr);
        $query = $builder->get();

        $lastPO = '01';
        if ($query->getResultArray()) {
            $lastFirst = explode('/', $query->getResultArray()[0]['po_no']);
            $lastPO = explode('-', $lastFirst[0]);
            $lastPO = intval($lastPO[1]) + 1;
            $lastPO = sprintf("%02d", $lastPO);
        };

        $generatedNo =  $lastStr . '-' . $lastPO . '/' . $divisi . '/TOBA/' . $thn2;

        return $generatedNo;
    }
}
