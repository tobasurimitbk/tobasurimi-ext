<?php

namespace App\Models;

use CodeIgniter\Model;

class BC41Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_41';
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


    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_order_lain.divisi_id' => 'sales_order_lain.divisi_id',
            'sales_order_lain.warehouse_id' => 'sales_order_lain.warehouse_id',
            'sales_order_lain.no_sales_order' => 'sales_order_lain.no_sales_order',
            'customers.name' => 'customers.name',
            'bc_41.no_aju' => 'bc_41.no_aju',
            'bc_41.createdAt' => 'bc_41.createdAt',
            'bc_41.status_posting' => 'bc_41.status_posting'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'bc_41.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_41.*,
        sales_order_lain.no_sales_order,
        divisis.divisi,
        warehouses.warehouse_name,
        customers.name AS customer_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->join('sales_order_lain', 'sales_order_lain.id = bc_41.sales_order_lain_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusPosting'] || $addCondition['noAju']  && (empty($addCondition['mulaiTanggalBC41']) && empty($addCondition['selesaiTanggalBC41']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusPosting']) {
            if ($addCondition['statusPosting'] == "ALL") {
                $bcDataQry->whereIn('bc_41.status_posting', ['1', '0']);
            } elseif ($addCondition['statusPosting'] == "SUDAH POSTING") {
                $bcDataQry->where('bc_41.status_posting', "1");
            } else if ($addCondition['statusPosting'] == "BELUM POSTING") {
                $bcDataQry->where('bc_41.status_POSTING', "0");
            }
        }

        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju'])->orLike('no_daftar', $addCondition['noAju']);
        }

        if ($addCondition['statusPosting'] || $addCondition['noAju']  && (empty($addCondition['mulaiTanggalBC41']) && empty($addCondition['selesaiTanggalBC41']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC41'] && $addCondition['selesaiTanggalBC41']) {
            $bcDataQry->groupStart();
            $mulaiTanggalBC41Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalBC41']), "Y-m-d");
            $selesaiTanggalBC41Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalBC41']), "Y-m-d");

            if ($addCondition['mulaiTanggalBC41']) {
                $bcDataQry->where('bc_41.createdAt >=', $mulaiTanggalBC41Timestamp);
            }

            if ($addCondition['selesaiTanggalBC41']) {
                $bcDataQry->where('bc_41.createdAt <=', $selesaiTanggalBC41Timestamp);
            }

            $bcDataQry->groupEnd();
        }

        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function detail($id)
    {
        $selectQry = "
            bc_41.*,
            sales_order_lain.no_sales_order,
            customers.name AS nama_customer,
            customers.address AS alamat_customer,
            country.country_name,
            divisis.divisi,
            warehouses.warehouse_name
        ";

        $result = $this->asArray()->select($selectQry)
            ->join('sales_order_lain', 'sales_order_lain.id = bc_41.sales_order_lain_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id')
            ->where('bc_41.id', $id)
            ->where('bc_41.deletedAt', null)
            ->first();

        return $result;
    }

    public function getListSalesOrderLain($salesOrderLainId = null)
    {
        $metaDataModel = new MetadataModel();
        $salesOrderLainModel = new SalesOrderLainModel();

        $bcFirst = $metaDataModel->getBCFirst("BC 4.1");
        $selectQry = "
            sales_order_lain.*,
            customers.name AS nama_customer,
            customers.address AS alamat_customer,
            country.country_name,
            divisis.divisi,
            warehouses.warehouse_name
        ";

        $salesOrderList = $salesOrderLainModel->select($selectQry)
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id')
            ->where('status_posting', '1')
            ->where('sales_order_lain.bc_id', $bcFirst['id'])
            ->where('sales_order_lain.deletedAt', null)
            ->findAll();

        $result = array();

        foreach ($salesOrderList as $s) {
            $bc41 = $this->where('sales_order_lain_id', $s['id'])->first();

            if ($salesOrderLainId != null) {
                if ($bc41 == null || $salesOrderLainId == $s['id']) {
                    array_push($result, $s);
                }
            } else {
                if ($bc41 == null) {
                    array_push($result, $s);
                }
            }
        }

        return $result;
    }
}
