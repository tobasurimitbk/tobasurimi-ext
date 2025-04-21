<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalUmumModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jurnal_umum';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'id_transaksi',
        'divisi_id',
        'supplier_id',
        'company_id',
        'id_coa',
        'tanggal_jurnal',
        'debit',
        'kredit',
        'valas',
        'kurs',
        'keterangan',
        'id_inputer',
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

    public function insertJurnal($data)
    {
        return $this->insert($data);
    }

    public function insertJurnalBatch($data)
    {
        return $this->insertBatch($data);
    }

    public function getDataJurnal($where)
    {
        $where['deletedAt'] = null;
        $builder = $this->db->table('jurnal_umum');

        if (isset($where['tanggal_jurnal'])) {
            $builder->like('tanggal_jurnal', $where['tanggal_jurnal']);
            $builder->where('deletedAt', $where['deletedAt']);
            $builder->where('id_coa', $where['id_coa']);
            var_dump($where);
        } else {
            $builder->where($where);
        }

        $query = $builder->get()->getResult();

        return $query;
    }

    public function getDataJurnalForCosting($where)
    {
        $saldolama = 0;
        $where['deletedAt'] = null;
        $selectQry = 'jurnal_umum.*, metadata.value';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('sub_akuns', 'sub_akuns.id = jurnal_umum.id_coa', 'left')
            ->join('kategori_akuns', 'kategori_akuns.id = sub_akuns.kategori_id', 'left')
            ->join('metadata', 'metadata.id = kategori_akuns.kelompok_id', 'left')
            // ->like('tanggal_jurnal', $where['tanggal_jurnal'])
            ->where('tanggal_jurnal >=', $where['tanggal_awal'])
            ->where('tanggal_jurnal <=', $where['tanggal_akhir'])
            ->where('jurnal_umum.deletedAt', $where['deletedAt'])
            ->where('jurnal_umum.id_coa', $where['id_coa'])
            ->findAll();

        foreach ($dataQry as &$valueJurnal) {
            $debit = floatval($valueJurnal['debit']);
            $kredit = floatval($valueJurnal['kredit']);
            if (stripos($valueJurnal['value'], "Aktiva") !== false) {
                if ($debit == 0) {
                    $saldolama = $saldolama + $debit - $kredit;
                } else {
                    $saldolama = $saldolama + $debit;
                }
            } else {
                if ($kredit == 0) {
                    $saldolama = $saldolama + $kredit - $debit;
                } else {
                    $saldolama = $saldolama + $kredit;
                }
            }
            $valueJurnal['saldoTotal'] = $saldolama;
        }

        return $saldolama;
    }

    public function getTotalSaldoLama($where)
    {
        $totalDebit = 0;
        $totalKredit = 0;
        $dataJurnalUmum = $this
            ->select('jurnal_umum.*')
            ->where('tanggal_jurnal <=', $where['tanggal_awal'])
            ->whereIn('id_coa', $where['id_coa'])
            ->where('jurnal_umum.deletedAt', null)
            ->findAll();

        foreach ($dataJurnalUmum as $d) {
            $totalDebit += ($d['debit'] ?? 0) * ($d['kurs'] ?? 1);
            $totalKredit += ($d['kredit'] ?? 0) * ($d['kurs'] ?? 1);
        }
        $saldoLama = $totalDebit - $totalKredit;
        return $saldoLama;
    }
}
