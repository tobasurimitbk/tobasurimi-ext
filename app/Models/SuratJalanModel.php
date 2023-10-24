<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'surat_jalan_so';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user', 
        'id_po', 
        'id_customer', 
        'multiple_id_so', 
        'multiple_no_so', 
        'sales_order_invoice_id',
        'no_po', 
        'shipping_date', 
        'no_surat_jalan', 
        'note'
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

    public function getAllSuratJalan($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_surat_jalan'          => 'surat_jalan_so.no_surat_jalan',
            'no_so'            => 'surat_jalan_so.multiple_no_so',
            'kode_pelanggan'             => 'customers.kode',
            'nama_pelanggan'             => 'customers.name',
            'shipping_date'      => 'surat_jalan_so.shipping_date',
            'createdAt'         => 'surat_jalan_so.createdAt',
            'updatedAt'         => 'surat_jalan_so.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'surat_jalan_so.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "surat_jalan_so.*,customers.name as nama_pelanggan,customers.kode as kode_pelanggan";

        $SuratJalan = $this->asObject()
            ->select($selectQry)
            ->join('customers', 'customers.id = surat_jalan_so.id_customer')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $SuratJalan->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $SuratJalan->groupStart();
        }
        if ($addCondition['search']) {
            $SuratJalan
                ->like('no_surat_jalan', $addCondition['search']);
        }
        if ($addCondition['dateStart']) {
            $SuratJalan->where('surat_jalan_so.shipping_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $SuratJalan->where('surat_jalan_so.shipping_date <=', $addCondition['dateEnd']);
        }
        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd']) {
            $SuratJalan->groupEnd();
        }

        $totalFilteredData = $SuratJalan->countAllResults(false);
        $data = $SuratJalan->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSuratJalanById($id)
    {
        $selectQry = "surat_jalan_so.*,
                      users.name as seller_name,
                      customers.name as customer_name ,
                      customers.address,customers.phone,
                      CONCAT(employees.nip , ' - ', employees.name) AS customerSales,
                      customers.address AS customerAddress,
                      customers.phone AS customerPhone,
                      metadata.value AS customerTermin";

        $dataSuratJalan = $this->asObject()
            ->join('users', 'users.id = surat_jalan_so.id_user')
            ->join('customers', 'customers.id = surat_jalan_so.id_customer ')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->join('employees', 'employees.id = customers.sales_id ')
            ->select($selectQry)
            ->find($id);

        if (empty($dataSuratJalan)) return null; 

        $dataMultpleid = json_decode($dataSuratJalan->multiple_id_so);
        $dataMultpleNo = json_decode($dataSuratJalan->multiple_no_so);

        $dataSuratJalan->multiple_id_so = $dataMultpleid;
        $dataSuratJalan->multiple_no_so = $dataMultpleNo;

        return $dataSuratJalan;
    }

    public function getNumber($periode)
    {

        $no = 0;
        $dummyNum = 0;
        $data = $this->asObject()->where("no_surat_jalan LIKE '%$periode%'")->orderBy('id', 'DESC')->first();
        if ($data) {
            $pecah = explode("/", $data->no_surat_jalan);
            foreach ($pecah as $key => $item) {
                if ($key === 4) {
                    $dummyNum += $item;
                }
            }
        } 

        $number = $dummyNum + 1;
        // $check = $number % 99999;
        // if ($check === 0) {
        //     $this->update($data->id, ['no' => 99999]);
        //     return 99999;
        // } else {
        //     if ($data) {
        //         $this->update($data->id, ['no' => $check]);
        //     } else {
        //         $this->update($idNewCreate, ['no' => $check]);
        //     }
        //     return $check;
        // }
        return $number;
    }
}
