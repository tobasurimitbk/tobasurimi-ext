<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaLokalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_lokal';
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
            'biaya_lokal.id'     => 'biaya_lokal.id',
            'biaya_lokal.divisi_id'     => 'biaya_lokal.divisi_id',
            'biaya_lokal.tanggal_invoice'         => 'biaya_lokal.tanggal_invoice',
            'biaya_lokal.no_invoice'               => 'biaya_lokal.no_invoice',
            'biaya_lokal.vendor_pelayaran_id'               => 'biaya_lokal.vendor_pelayaran_id',
            'biaya_lokal.total_faktur' => 'biaya_lokal.total_faktur',
            'biaya_lokal.status_posting_exim'               => 'biaya_lokal.status_posting_exim',
            'biaya_lokal.status_posting_acc'               => 'biaya_lokal.status_posting_acc',
            'biaya_lokal.status_posting_audit'               => 'biaya_lokal.status_posting_audit',
            'biaya_lokal.status_bayar'               => 'biaya_lokal.status_bayar',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'biaya_lokal.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "biaya_lokal.*,
            vendor_pelayaran.nama_vendor,
            divisis.divisi
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_lokal.vendor_pelayaran_id', 'left')
            ->join('divisis', 'divisis.id = biaya_lokal.divisi_id', 'left')
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
                    ->where('biaya_lokal.status_posting_exim', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING EXIM") {
                $dataQry
                    ->where('biaya_lokal.status_posting_exim', 0);
            } else if ($addCondition['status_posting'] == "POSTING ACC") {
                $dataQry
                    ->where('biaya_lokal.status_posting_acc', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING ACC") {
                $dataQry
                    ->where('biaya_lokal.status_posting_acc', 0);
            } else if ($addCondition['status_posting'] == "POSTING AUDIT") {
                $dataQry
                    ->where('biaya_lokal.status_posting_audit', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING AUDIT") {
                $dataQry
                    ->where('biaya_lokal.status_posting_audit', 0);
            }
        }

        if (isset($addCondition['search']) && !empty($addCondition['search'])) {
            $dataQry->groupStart()
                ->like('no_invoice', $addCondition['search'])
                ->orLike('vendor_pelayaran.nama_vendor', $addCondition['search'])
                ->orLike('biaya_lokal.total_faktur', $addCondition['search'])
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
}
