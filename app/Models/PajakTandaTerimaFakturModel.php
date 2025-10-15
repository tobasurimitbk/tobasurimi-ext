<?php

namespace App\Models;

use CodeIgniter\Model;

class PajakTandaTerimaFakturModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pajak_tanda_terima_faktur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tanda_terima_faktur_id',
        'tax_inv_date',
        'tax_inv_no',
        'tax_type',
        'tax_amt',
        'tax_status',
        'tax_note',
        'tax_id'
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

    public function getTaxDetail($taxStatus, $tandaTerimaFakturID)
    {
        $taxRes = $this->asArray()->where('tanda_terima_faktur_id', $tandaTerimaFakturID)->where('tax_status', $taxStatus)->where('deletedAt', null)->findAll();
        $taxType = [];
        $taxAmt = 0;
        foreach ($taxRes as $t) {
            $taxType[] = $t['tax_type'];
            $taxAmt += $t['tax_amt'];
        }
        return [
            'taxType' => count($taxType) == 0 ? '-' : implode(',', $taxType),
            'taxAmt' => $taxAmt
        ];
    }

    public function getTaxId($taxName, $companyId)
    {
        $taxModel = new TaxModel();
        if ($taxName == "PPN Masukan") {
            $taxName = "PPN Masukan 0%";
        }
        $tax = $taxModel->where('name', $taxName)->where('company_id', $companyId)->first();
        if ($tax) {
            return $tax['id'];
        } else {
            return null;
        }
    }
}
