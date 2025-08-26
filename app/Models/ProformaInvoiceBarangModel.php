<?php

namespace App\Models;

use CodeIgniter\Model;

class ProformaInvoiceBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'proforma_invoice_barang';
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

    public function getBarang($PIId)
    {
        $selectQry = '
            proforma_invoice_barang.*,
            satuans.kode_satuan
        ';
        $dataQry = $this->select($selectQry)
            ->join('satuans', 'satuans.id = proforma_invoice_barang.satuan_id', 'left')
            ->where('proforma_invoice_barang.deletedAt', null)
            ->findAll();

        return $dataQry;
    }
}
