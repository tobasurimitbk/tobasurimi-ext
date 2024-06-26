<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesOrderLainModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sales_order_lain';
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

    public function getList($condition, $conditionArr, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'tanggal' => 'tanggal',
            'no_sales_order' => 'no_sales_order',
            'tipe_customer' => 'tipe_customer',
            'customer_name' => 'customer_name',
            'keterangan' => 'keterangan',
            'total_barang' => 'total_barang',
            'total_harga' => 'total_harga',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "sales_order_lain.*,
        customers.tipe_customer,
        customers.name AS customer_name,
        SUM(sales_order_lain_detail.total_harga) AS total_harga,
        divisis.divisi
        ";

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('sales_order_lain_detail', 'sales_order_lain_detail.sales_order_lain_id = sales_order_lain.id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->where($condition)
            ->whereIn('sales_order_lain.divisi_id', $conditionArr)
            ->groupBy('sales_order_lain_detail.sales_order_lain_id')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['status_posting'] || $addCondition['no_sales_order'] || $addCondition['mulai_tanggal'] || $addCondition['selesai_tanggal']) {
            $dataQry->groupStart();
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] == "ALL") {
                $dataQry->whereIn('status_posting', ['1', '0']);
            } elseif ($addCondition['status_posting'] == "SUDAH POSTING") {
                $dataQry->whereIn('status_posting', ['1']);
            } else {
                $dataQry->whereIn('status_posting', ['0']);
            }
        }

        if ($addCondition['no_sales_order']) {
            $dataQry->like('no_sales_order', $addCondition['no_sales_order']);
        }

        if ($addCondition['mulai_tanggal']) {
            $dataQry->where('tanggal >=', $addCondition['mulai_tanggal']);
        }

        if ($addCondition['selesai_tanggal']) {
            $dataQry->where('tanggal <=', $addCondition['selesai_tanggal']);
        }

        if ($addCondition['status_posting'] || $addCondition['no_sales_order'] || $addCondition['mulai_tanggal'] || $addCondition['selesai_tanggal']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function detail($id)
    {
        $result = $this->select('
        sales_order_lain.*,
        warehouses.warehouse_name,
        customers.kode AS kode_customer,
        customers.name AS customer_name,
        customers.tipe_customer
        ')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->where('sales_order_lain.id', $id)
            ->first();

        return $result;
    }

    public function getDokumenPabean()
    {
        $metaDataModel = new MetadataModel();
        $dataAJU = $metaDataModel->getBCUsed("so_lain");
        $result = [];

        array_push($result, [
            'id' => 0,
            'value' => "NON PABEAN"
        ]);

        foreach ($dataAJU as $d) {
            array_push($result, $d);
        }

        return $result;
    }

    public function get_no($bln, $thn, $last_day, $divisiName, $divisiID)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('sales_order_lain');
        $builder->select('no_sales_order');
        $builder->orderBy('no_sales_order', 'desc');
        $builder->where('sales_order_lain.divisi_id', $divisiID);
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59");
        $builder->like('no_sales_order', $lastStr);
        $query = $builder->get();

        $kode = 'SOL/' . $divisiName;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_sales_order']);
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
