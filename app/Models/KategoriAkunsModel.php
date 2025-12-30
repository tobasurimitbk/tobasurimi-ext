<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriAkunsModel extends Model
{
    protected $table = 'kategori_akuns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id',
        'company_id',
        'kelompok_id',
        'no_kategori',
        'nama_kategori',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function get_by_id($id)
    {
        $requete = "SELECT kategori_akuns.*,metadata.value as meta_name FROM kategori_akuns ";
        $requete .= "LEFT JOIN metadata ON (metadata.id=kategori_akuns.kelompok_id) ";
        $requete .= "WHERE kategori_akuns.id='" . $id . "'";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function search_list($values, $sortby = '', $offset = 0, $limit = -1)
    {
        $requete = "SELECT " . $this->table . ".*,metadata.value as kelompok_akun FROM " . $this->table . " ";
        $requete .= "LEFT JOIN metadata ON (kategori_akuns.kelompok_id=metadata.id) ";
        $requete .= "WHERE kategori_akuns.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND " . $this->table . ".company_id ='" . $values["company_id"] . "' ");
        if (isset($values["search"]))
            $requete .= ($values["search"] == "") ? "" : ("AND (UPPER(no_kategori) like '%" . strtoupper($values["search"]) . "%' OR UPPER(nama_kategori) like '%" . strtoupper($values["search"]) . "%')");

        if ($sortby != '')
            $requete .= "ORDER BY $sortby ";
        if ($limit >= 0)
            $requete .= "LIMIT $limit OFFSET $offset";

        $query = $this->db->query($requete);
        return $query->getResultArray();
    }

    public function total_list($values)
    {
        $requete  = "SELECT count(*) as total FROM " . $this->table . " ";
        $requete .= "WHERE kategori_akuns.deletedAt is null ";
        if (isset($values["company_id"]))
            $requete .= ($values["company_id"] == "") ? "" : ("AND " . $this->table . ".company_id ='" . $values["company_id"] . "' ");
        if (isset($values["nama_sub"]))
            $requete .= ($values["nama_sub"] == "") ? "" : ("AND UPPER(nama_sub) like '%" . strtoupper($values["nama_sub"]) . "%' ");

        $result = $this->db->query($requete)->getResultArray();
        return ($result[0]["total"]) ? $result[0]["total"] : 0;
    }

    public function getAPAR($company_id)
    {
        if ($company_id != "" && !is_array($company_id)) {
            $arrCondition = [
                'deletedAt' => null,
                'company_id' => $company_id
            ];
        } else {
            $arrCondition = [
                'deletedAt' => null
            ];
        }

        $builder = $this->db->table('kategori_akuns');
        $builder->where($arrCondition);
        if (is_array($company_id)) {
            $builder->whereIn('company_id', $company_id);
        }
        $query = $builder
        ->get();

        $rows = $query->getResult();

        /** ======== GROUP BY no_sub ========= **/
        $grouped = [];
        foreach ($rows as $row) {
            if (!isset($grouped[$row->no_kategori])) {
                $grouped[$row->no_kategori] = [
                    'no_kategori'        => $row->no_kategori,
                    'nama_kategori'      => $row->nama_kategori,
                    'kelompok_id'        => $row->kelompok_id,
                    'ids'                => [],
                    'kelompok_ids'       => []
                ];
            }
            $grouped[$row->no_kategori]['ids'][] = $row->id;
            $grouped[$row->no_kategori]['kelompok_ids'][] = $row->kelompok_id;
        }

        return array_values($grouped); // reset index
    }
}
