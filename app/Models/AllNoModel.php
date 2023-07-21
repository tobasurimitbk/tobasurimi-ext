<?php

namespace App\models;

use CodeIgniter\Model;
use App\Models\SalesOrderDetailModel;

class AllNoMOdel extends Model
{
    protected $SalesOrderDetailModel;

    protected $table      = 'all_no';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $protectedField = true;




    protected $allowedFields = [
        'name', 'no', 'periode'
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

    public function getNumber($name, $periode)
    {
        $condition = [
            'name' => $name,
            'periode' => $periode,
        ];

        $no = 0;
        $data = $this->asObject()->where($condition)->first();
        if (!$data) {
            $value = [
                'name' => $name,
                'periode' => $periode,
                'no' => 0
            ];
            $idNewCreate = $this->insert($value);
        } else {
            $no = $data->no;
        }

        $number = $no + 1;
        $check = $number % 99999;
        if ($check === 0) {
            $this->update($data->id, ['no' => 99999]);
            return 99999;
        } else {
            if ($data) {
                $this->update($data->id, ['no' => $check]);
            } else {
                $this->update($idNewCreate, ['no' => $check]);
            }
            return $check;
        }
    }
}
