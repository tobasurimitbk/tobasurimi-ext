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
        $proformaInvoiceSizeBreakdownModel = new ProformaInvoiceSizeBreakdownModel();

        $resultFinal = [];
        $barang = $this->where('proforma_invoice_id', $PIId)->findAll();
        foreach ($barang as $b) {

            $sizeBreakdown = $proformaInvoiceSizeBreakdownModel
                ->select('proforma_invoice_size_breakdown.*,satuans.kode_satuan AS satuan_size_code')
                ->join('satuans', 'satuans.id = proforma_invoice_size_breakdown.satuan_id', 'left')
                ->where('proforma_invoice_barang_id', $b['id'])
                ->where('proforma_invoice_size_breakdown.deletedAt')
                ->findAll();

            $resultBarang = [
                'id_barang' => $b['id'],
                'nama_barang' => $b['nama_barang'],
                'keterangan' => $b['keterangan'],
                'size_breakdown' => []
            ];

            foreach ($sizeBreakdown as $s) {
                array_push($resultBarang['size_breakdown'], [
                    'id_detail_breakdown' => $s['id'],
                    'size' => $s['size'],
                    'grade' => $s['grade'],
                    'packing_size' => $s['packing'],
                    'qty' => (float)$s['qty'],
                    'harga' => (float)$s['harga'],
                    'total' => (float)$s['total'],
                    'satuan_size_id' => $s['satuan_id'],
                    'satuan_size_code' => $s['satuan_size_code']
                ]);
            }
            array_push($resultFinal, $resultBarang);
        }

        return $resultFinal;
    }
}
