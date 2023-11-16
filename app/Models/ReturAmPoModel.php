<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class ReturAmPoModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'retur_am_po';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'user_id',
        'retur_no',
        'retur_date',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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

    public function generateNoRetur()
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
        $year = substr($year, -2);
        $month = $today->getMonth() - 1;

        $lastStr =  'RETUR/' . $romanNumb[$month] . '/' . $year;

        $builder = $this->db->table('retur_am_po');
        $builder->select('retur_no');
        $builder->orderBy('retur_no', 'desc');
        $builder->like('retur_no', $lastStr);
        $query = $builder->get();

        $increment = '001';

        if ($query->getResultArray()) {
            $lastPo = explode('/', $query->getResultArray()[0]['retur_no']);
            $lastPo = intval($lastPo[0]) + 1;

            if ($lastPo < 10) {
                $lastPo = "00" . $lastPo . "";
            } elseif ($lastPo > 9 && $lastPo < 100) {
                $lastPo = "0" . $lastPo . "";
            } else {
                $lastPo = strval($lastPo);
            }

            $increment = $lastPo;
        };

        $generatedPoNo = $increment . '/' . $lastStr;

        return $generatedPoNo;
    }
}
