<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanMutasiGlobalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'penerimaan_mutasi_global';
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

    public function getListNomorMutasi($companyPengirimId)
    {
        $mutasiGlobalModel = new MutasiGlobalModel();
        $penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();

        $listMutasi = $mutasiGlobalModel
            ->select('mutasi_global.id, mutasi_global.no_mutasi, SUM(qty) AS qty_mutasi')
            ->join('mutasi_global_detail', 'mutasi_global_detail.mutasi_global_id = mutasi_global.id', 'left')
            ->where('mutasi_global.company_asal_id', $companyPengirimId)
            ->where('mutasi_global.deletedAt', null)
            ->where('mutasi_global_detail.deletedAt', null)
            ->groupBy('mutasi_global_detail.mutasi_global_id')
            ->orderBy('mutasi_global.no_mutasi', "ASC")
            ->findAll();

        $mutasiResult = [];

        foreach ($listMutasi as $mutasi) {
            $penerimaanTotal = $penerimaanMutasiGlobalDetailModel
                ->select('SUM(qty) AS qty_diterima')
                ->where('mutasi_global_id', $mutasi['id'])
                ->where('deletedAt', null)
                ->groupBy('mutasi_global_id')
                ->findAll();

            if (empty($penerimaanTotal) || $penerimaanTotal[0]['qty_diterima'] < $mutasi['qty_mutasi']) {
                array_push($mutasiResult, $mutasi);
            }
        }

        return $mutasiResult;
    }

    public function get_no($bln, $thn, $last_day, $divisiName, $divisiID)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('penerimaan_mutasi_global');
        $builder->select('penerimaan_mutasi_no');
        $builder->orderBy('penerimaan_mutasi_no', 'desc');
        $builder->where('penerimaan_mutasi_global.divisi_penerima_id', $divisiID);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('penerimaan_mutasi_no', $lastStr);
        $query = $builder->get();

        $kode = 'PMG/' . $divisiName;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['penerimaan_mutasi_no']);
                $number = intval($explode[2]);

                if ($number > $lastPenerimaan) {
                    $lastPenerimaan = $number;
                }
            }
            $lastPenerimaan++;
        }

        $formattedLastPenerimaan = sprintf("%02d", $lastPenerimaan);
        $generatedNo = $kode . '/' . $formattedLastPenerimaan . '/' . $lastStr;

        return $generatedNo;
    }
}
