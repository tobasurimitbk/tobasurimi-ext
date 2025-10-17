<?php

namespace App\Models;

use CodeIgniter\Model;

class DivisisModel extends Model
{
    protected $table = 'divisis';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $DBGroup          = 'default';
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'jam_kerja_id',
        'divisi',
        'type_divisi',
        'createdAt',
        'updatedAt',
        'deletedAt'
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

    public function get_by_id($id)
    {
        $requete = "SELECT * FROM divisis WHERE id='" . $id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function get_by_company_id($company_id)
    {
        $requete = "SELECT * FROM divisis WHERE divisis.deletedAt is null and company_id='" . $company_id . "'";
        //echo $requete;
        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT divisis.*, jam_kerja.jenis FROM divisis ";
        $requete .= "LEFT JOIN jam_kerja ON jam_kerja.id = divisis.jam_kerja_id ";
        $requete .= "WHERE divisis.deletedAt IS NULL AND jam_kerja.deletedAt IS NULL ";

        if (isset($values["company_id"]) && $values["company_id"] !== "") {
            $requete .= "AND divisis.company_id = '" . $values["company_id"] . "' ";
        }

        if (isset($values["type_divisi"]) && $values["type_divisi"] !== "") {
            $requete .= "AND (divisis.type_divisi = '" . $values["type_divisi"] . "' OR divisis.type_divisi = 'GABUNGAN') ";
        }

        if (isset($values["divisi"]) && $values["divisi"] !== "") {
            $requete .= "AND UPPER(divisis.divisi) LIKE '%" . strtoupper($values["divisi"]) . "%' ";
        }

        if (isset($values["search"]) && $values["search"] !== "") {
            $requete .= "AND (UPPER(divisis.divisi) LIKE '%" . strtoupper($values["search"]) . "%') ";
        }

        if ($sortby !== '') {
            $requete .= "ORDER BY $sortby ";
        }

        if ($limit >= 0) {
            $requete .= "LIMIT $limit OFFSET $offset";
        }

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT COUNT(*) AS total FROM divisis ";
        $requete .= "LEFT JOIN jam_kerja ON jam_kerja.id = divisis.jam_kerja_id ";
        $requete .= "WHERE divisis.deletedAt IS NULL AND jam_kerja.deletedAt IS NULL ";

        // Menyimpan parameter agar aman dari SQL Injection
        $params = [];

        if (isset($values["company_id"]) && $values["company_id"] !== "") {
            $requete .= "AND divisis.company_id = ? ";
            $params[] = $values["company_id"];
        }

        if (isset($values["type_divisi"]) && $values["type_divisi"] !== "") {
            $requete .= "AND (divisis.type_divisi = ? OR divisis.type_divisi = 'GABUNGAN') ";
            $params[] = $values["type_divisi"];
        }

        if (isset($values["divisi"]) && $values["divisi"] !== "") {
            $requete .= "AND UPPER(divisis.divisi) LIKE ? ";
            $params[] = '%' . strtoupper($values["divisi"]) . '%';
        }

        if (isset($values["search"]) && $values["search"] !== "") {
            $requete .= "AND (UPPER(divisis.divisi) LIKE ?) ";
            $params[] = '%' . strtoupper($values["search"]) . '%';
        }

        $result = $this->db->query($requete, $params)->getResultArray();
        return $result[0]["total"] ?? 0;
    }

    public function getTunjanganByDivisi($divisionID)
    {
        $select = "
            tunjangan.*, gaji_divisi.nominal
        ";
        $data = $this->asObject()
            ->select($select)
            ->join('gaji_divisi', 'gaji_divisi.division_id = divisis.id', 'LEFT')
            ->join('tunjangan', 'tunjangan.id = gaji_divisi.tunjangan_id', 'LEFT')
            ->where('gaji_divisi.division_id', $divisionID)
            ->where('tunjangan.deletedAt', null)
            ->findAll();


        $res = [];
        foreach ($data as $d) {
            $res[] = [
                'id' => $d->id,
                'nominal' => (float)$d->nominal,
                'name' => $d->name,
                'is_cadangan' => $d->is_cadangan,
                'is_gaji_harian' => $d->is_gaji_harian,
                'tipe' => $d->tipe
            ];
        }

        return $res;
    }

    public function getListForAccount($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisi_name'       => 'divisis.divisi',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'divisis.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "divisis.*,
                    account_divisis.coa_kas_id,
                    account_divisis.coa_piutang_id,
                    account_divisis.coa_gaji_id,
                    account_divisis.coa_hpp_id,";

        $divisisDataQry = $this->asArray()
            ->select($selectQry)
            ->where($condition)
            ->join('account_divisis', 'divisis.id = account_divisis.divisis_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $divisisDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $divisisDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $divisisDataQry->like('divisis.divisi', $addCondition['search']);
        }

        if ($addCondition['search']) {
            $divisisDataQry->groupEnd();
        }

        $totalFilteredData = $divisisDataQry->countAllResults(false);
        $data = $divisisDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getAccountKasForJurnal($divisionID, $companyID)
    {
        $select =   "divisis.*,
                    account_divisis.coa_kas_id,
                    account_divisis.coa_piutang_id,
                    account_divisis.coa_gaji_id,
                    account_divisis.coa_hpp_id,";
        return $this->asObject()
            ->select($select)
            ->join('account_divisis', 'divisis.id = account_divisis.divisis_id', 'left')
            ->where('divisis.id', $divisionID)
            ->where('account_divisis.company_id', $companyID)
            ->where('divisis.deletedAt', null)
            ->where('account_divisis.deleted_at', null)
            ->findAll();
    }

    public function getDivisiAccess()
    {
        if (empty(session()->get('login')->this_access_divisi_id)) {
            return [];
        } else {
            return $this->asArray()->where('deletedAt', null)
                ->whereIn('id', session()->get('login')->this_access_divisi_id)
                ->where('company_id', session()->get('login')->this_company_id)
                ->orderBy('divisi', "ASC")
                ->findAll();
        }
    }

    public function getDivisiAccessAllCompany()
    {
        $session = session()->get('login');
        if (empty($session->this_access_divisi_id)) {
            return [];
        }

        $builder = $this->db->table('divisis');
        $builder->select('divisis.*, companies.company as company_name');
        $builder->join('companies', 'companies.id = divisis.company_id', 'left');

        // 🔹 Filter company sesuai kondisi
        if (in_array($session->this_company_id, [1, 2])) {
            $builder->whereIn('divisis.company_id', [1, 2]);
        } else {
            $builder->where('divisis.company_id', $session->this_company_id);
        }

        $builder->where('divisis.deletedAt', null);
        $builder->groupBy('divisis.divisi');
        $builder->orderBy('divisis.divisi', 'ASC');

        $results = $builder->get()->getResultArray();

        foreach ($results as &$result) {
            $divisiName  = strtoupper($result['divisi']);
            // $companyName = strtoupper($result['company_name'] ?? '');
            $result['divisi'] = $divisiName;
        }

        return $results;
    }



    public function getDivisiExcept($divisi_id)
    {
        if (empty(session()->get('login')->this_access_divisi_id)) {
            return [];
        } else {
            return $this->asArray()->where('deletedAt', null)
                ->whereNotIn('id', [$divisi_id])
                ->whereIn('id', session()->get('login')->this_access_divisi_id)
                ->where('company_id', session()->get('login')->this_company_id)
                ->orderBy('divisi', "ASC")
                ->findAll();
        }
    }

    public function getDivisiJamKerja($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'divisis.id' => 'divisis.id',
            'divisis.jam_kerja_id' => 'divisis.jam_kerja_id',
            'jam_kerja.jenis' => 'jam_kerja.jenis',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'divisis.divisi'] ?? 'divisis.divisi';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $dataQry = $this->asObject()->select("
                DISTINCT(divisis.id),
                divisis.divisi,
                divisis.jam_kerja_id,
                COUNT(jam_kerja.id) AS total_jam_kerja,
                jam_kerja.jenis,
                jam_kerja.shift,
                CONCAT(jam_kerja.jenis, ' ', jam_kerja.shift) AS jenis_shift
            ")
            ->join('jam_kerja', 'jam_kerja.id = divisis.jam_kerja_id', 'left')
            ->whereIn('divisis.type_divisi', ["GABUNGAN", "PERSONALIA"])
            ->where($condition)
            ->groupBy('divisis.id')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['search']) {
            $dataQry->groupStart();
        }

        if ($addCondition['search']) {
            $dataQry->like("CONCAT(jam_kerja.jenis, ' ', jam_kerja.shift)", $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search']);
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
