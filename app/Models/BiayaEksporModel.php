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
            'biaya_ekspor.destination'               =>  'biaya_ekspor.destination',
            'vendor_pelayaran.nama_vendor'               =>  'vendor_pelayaran.nama_vendor',
            'biaya_ekspor.total_faktur'               => 'biaya_ekspor.total_faktur',
            'biaya_ekspor.status_posting_exim'               => 'biaya_ekspor.status_posting_exim',
            'biaya_ekspor.status_posting_acc'               => 'biaya_ekspor.status_posting_acc',
            'biaya_ekspor.status_posting_audit'               => 'biaya_ekspor.status_posting_audit',
            'biaya_ekspor.status_bayar'               => 'biaya_ekspor.status_bayar',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'tanggal_invoice';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "biaya_ekspor.*,
            customers.name as customer_name,
            vendor_pelayaran.nama_vendor,
            divisis.divisi
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_ekspor.vendor_pelayaran_id', 'left')
            ->join('customers', 'customers.id = biaya_ekspor.customer_id', 'left')
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
            if ($addCondition['status_posting'] == "POSTING EXIM") {
                $dataQry
                    ->where('biaya_ekspor.status_posting_exim', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING EXIM") {
                $dataQry
                    ->where('biaya_ekspor.status_posting_exim', 0);
            } else if ($addCondition['status_posting'] == "POSTING ACC") {
                $dataQry
                    ->where('biaya_ekspor.status_posting_acc', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING ACC") {
                $dataQry
                    ->where('biaya_ekspor.status_posting_acc', 0);
            } else if ($addCondition['status_posting'] == "POSTING AUDIT") {
                $dataQry
                    ->where('biaya_ekspor.status_posting_audit', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING AUDIT") {
                $dataQry
                    ->where('biaya_ekspor.status_posting_audit', 0);
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
            customers.name as customer_name
        ')
            ->join('customers', 'customers.id = biaya_ekspor.customer_id', 'left')
            ->where('biaya_ekspor.id', $id)
            ->first();

        return $resQry;
    }
}
