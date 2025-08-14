<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaEksporModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_ekspor';
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
            'biaya_ekspor.id'     => 'biaya_ekspor.id',
            'biaya_ekspor.divisi_id'     => 'biaya_ekspor.divisi_id',
            'biaya_ekspor.tanggal_invoice'         => 'biaya_ekspor.tanggal_invoice',
            'biaya_ekspor.no_invoice'               => 'biaya_ekspor.no_invoice',
            'biaya_ekspor.no_container'               => 'biaya_ekspor.no_container',
            'customers.name'               =>     'customers.name',
            'sales_contract.dicharge_port'               =>  'sales_contract.dicharge_port',
            'vendor_pelayaran.nama_vendor'               =>  'vendor_pelayaran.nama_vendor',
            'biaya_ekspor.total_faktur'               => 'biaya_ekspor.total_faktur',
            'biaya_ekspor.status_posting'               => 'biaya_ekspor.status_posting',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'tanggal_invoice';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "biaya_ekspor.*,
            customers.name as customer_name,
            vendor_pelayaran.nama_vendor,
            sales_contract.dicharge_port,
            divisis.divisi
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_ekspor.vendor_pelayaran_id', 'left')
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = biaya_ekspor.sales_order_export_id', 'left')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->join('divisis', 'divisis.id = biaya_ekspor.divisi_id', 'left')
            ->where($condition);

        $totalData = $dataQry->countAllResults(false);

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $dataQry->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $dataQry->where('tanggal_invoice >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $dataQry->where('tanggal_invoice <=', $addCondition['dateEnd']);
            }
            $dataQry->groupEnd();
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] == "SUDAH POSTING") {
                $dataQry
                    ->where('biaya_ekspor.status_posting', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING") {
                $dataQry
                    ->where('biaya_ekspor.status_posting', 0);
            }
        }

        if (isset($addCondition['search']) && !empty($addCondition['search'])) {
            $dataQry->groupStart()
                ->like('no_invoice', $addCondition['search'])
                ->orLike('biaya_ekspor.no_container', $addCondition['search'])
                ->orLike('customers.name', $addCondition['search'])
                ->orLike('sales_contract.dicharge_port', $addCondition['search'])
                ->orLike('vendor_pelayaran.nama_vendor', $addCondition['search'])
                ->orLike('biaya_ekspor.total_faktur', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);

        $data = $dataQry->orderBy($sort, $sortType)
            ->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }


    public function generateNumber($tanggalInvoice)
    {
        $tanggalTerimaExplode = explode('/', $tanggalInvoice);

        $month = $tanggalTerimaExplode[1];
        $year = substr($tanggalTerimaExplode[2], -2);
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "/INV/$romanMonth/$year";

        $lastData = $this->asObject()
            ->where('company_id', session()->get("login")->this_company_id)
            ->like('no_invoice', $numberTemplate, 'before')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $invNumber = '001' . $numberTemplate;

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->no_invoice);
            $lastIncrement = intval($asd[0]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }

        return $invNumber;
    }

    public function getById($id)
    {
        $resQry = $this->select('
            biaya_ekspor.*,
            sales_contract.dicharge_port,
            sales_contract.payment_term,
            sales_contract.tipe_harga,
            sales_order_export.container as container_orderform,
            metadata.value as valas,
            customers.name as customer_name
        ')
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = biaya_ekspor.sales_order_export_id', 'left')
            ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id', 'left')
            ->join('metadata', 'sales_order_export.valas_id = metadata.id', 'left')
            ->join('customers', 'customers.id = sales_contract.customer_id', 'left')
            ->where('biaya_ekspor.id', $id)
            ->first();

        return $resQry;
    }
}
