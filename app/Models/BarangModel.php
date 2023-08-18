<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'barangs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'parent_id',
        'company_id',
        'type',
        'warehouse_id',
        'barang_condition',
        'kode_barang',
        'nama_barang',
        'spek',
        'harga_barang',
        'satuan_id',
        'kategori_id',
        'hs_id',
        'ap_id',
        'ar_id',
        'stok',
        'status',
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

    public function getBarangList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'parent_barang'     => 'parent_barangs.nama_barang',
            'kode_barang'       => 'barangs.kode_barang',
            'nama_barang'       => 'barangs.nama_barang',
            'type'              => 'barangs.type',
            'harga_barang'      => 'barangs.harga_barang',
            'kode_satuan'       => 'satuans.kode_satuan',
            'kategori'          => 'metadata.value',
            'code_hs'           => 'hs_codes.code',
            'sub_akun_ap'       => 'ap.nama_sub',
            'sub_akun_ar'       => 'ar.nama_sub',
            'stok'              => 'barangs.stok',
            'status'            => 'barangs.status',
            'createdAt'         => 'barangs.createdAt',
            'updatedAt'         => 'barangs.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'barangs.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "barangs.*, 
                      parent_barangs.nama_barang AS parent_barang,
                      satuans.kode_satuan AS kode_satuan, 
                      metadata.value AS kategori,
                      hs_codes.code AS code_hs,
                      ap.nama_sub AS sub_akun_ap,
                      ar.nama_sub AS sub_akun_ar";
        $barangDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('barangs AS parent_barangs', 'parent_barangs.id = barangs.parent_id', 'left')
            ->join('satuans', 'satuans.id = barangs.satuan_id', 'left')
            ->join('metadata', 'metadata.id = barangs.kategori_id', 'left')
            ->join('hs_codes', 'hs_codes.id = barangs.hs_id', 'left')
            ->join('sub_akuns AS ap', 'ap.id = barangs.ap_id', 'left')
            ->join('sub_akuns AS ar', 'ar.id = barangs.ar_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $barangDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['kategori'] || $addCondition['status']) {
            $barangDataQry->groupStart();
        }

        if ($addCondition['search']) {
            $barangDataQry->like('barangs.nama_barang', $addCondition['search'])->orLike('barangs.kode_barang', $addCondition['search']);
        }

        if ($addCondition['kategori']) {
            $barangDataQry->where('metadata.id', $addCondition['kategori']);
        }

        if ($addCondition['status']) {
            $barangDataQry->where('barangs.status', $addCondition['status']);
        }

        if ($addCondition['search'] || $addCondition['kategori'] || $addCondition['status']) {
            $barangDataQry->groupEnd();
        }

        $totalFilteredData = $barangDataQry->countAllResults(false);
        $data = $barangDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function getBarangByCompanyId($company_id)
    {
        /* $arrCondition = [
            'deletedAt' => null,
            'parent_id !=' => 0,
            'company_id' => $company_id
        ];

        $builder = $this->db->table('barangs');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResult(); */
        
        $data = $this->asObject()
            ->where('company_id', $company_id)
            ->groupStart()
                ->groupStart()
                    ->where('barangs.parent_id !=', 0)
                    ->where('barangs.spec_type', 'multi')
                ->groupEnd()
                ->orGroupStart()
                    ->where('barangs.parent_id', 0)
                    ->where('barangs.spec_type', 'single')
                ->groupEnd()
            ->groupEnd()
            ->findAll();

        return $data;
    }

    public function getBarangByKode($kode, $id = null)
    {
        if ($id) {
            $arrCondition = [
                'deletedAt' => null,
                'parent_id !=' => 0,
                'kode_barang' => $kode,
                'id !=' => $id
            ];
        } else {
            $arrCondition = [
                'parent_id !=' => 0,
                'deletedAt' => null,
                'kode_barang' => $kode
            ];
        }

        $builder = $this->db->table('barangs');
        $builder->where($arrCondition)
            ->orderBy('nama_barang', 'ASC');

        $query = $builder->get();

        return $query->getResult();
    }

    public function getParentBarang($company_id)
    {
        $arrCondition = [
            'deletedAt' => null,
            'company_id' => $company_id,
            'parent_id' => 0
        ];

        $builder = $this->db->table('barangs');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResult();
    }

    public function getBarangByKategori($kategori)
    {
        $arrCondition = [
            'barangs.deletedAt' => null,
            'barangs.parent_id !=' => 0,
            'metadata.deletedAt' => null,
            'metadata.value' => $kategori
        ];

        $selectQry = "barangs.*,
        metadata.value AS value, 
        ";

        $builder = $this->db->table('barangs')
            ->select($selectQry)
            ->join('metadata', 'metadata.id = barangs.kategori_id');
        $builder->where($arrCondition)
            ->orderBy('barangs.nama_barang', 'ASC');
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getBarangByType($type)
    {
        $arrCondition = [
            'barangs.deletedAt' => null,
            'barangs.status' => 'Aktif',
            // 'barangs.parent_id !=' => 0,
            'metadata.deletedAt' => null,
            'barangs.type' => $type
        ];

        /* $selectQry = "barangs.*,
        metadata.value AS value, 
        satuans.kode_satuan AS kode_satuan, 
        ";

        $builder = $this->db->table('barangs')
            ->select($selectQry)
            ->join('metadata', 'metadata.id = barangs.kategori_id')
            ->join('satuans', 'satuans.id = barangs.satuan_id');
        $builder->where($arrCondition)
            ->orderBy('barangs.nama_barang', 'ASC');
        $query = $builder->get();

        return $query->getResultArray(); */

        $selectQry = "barangs.*,
        metadata.value AS value, 
        satuans.nama_satuan AS nama_satuan, 
        ";
        $data = $this->select($selectQry)
            ->join('metadata', 'metadata.id = barangs.kategori_id')
            ->join('satuans', 'satuans.id = barangs.satuan_id')
            ->where($arrCondition)
            ->groupStart()
                ->groupStart()
                    ->where('barangs.parent_id !=', 0)
                    ->where('barangs.spec_type', 'multi')
                ->groupEnd()
                ->orGroupStart()
                    ->where('barangs.parent_id', 0)
                    ->where('barangs.spec_type', 'single')
                ->groupEnd()
            ->groupEnd()
            ->orderBy('barangs.nama_barang', 'ASC')
            ->findAll();

        return $data;
    }
}
