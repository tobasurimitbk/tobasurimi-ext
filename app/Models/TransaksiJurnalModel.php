<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiJurnalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'transaksi_jurnal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'no_transaksi',
        'tanggal_transaksi',
        'total_debit',
        'total_kredit',
        'metode_input',
        'type_transaksi',
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

    public function insertTransaksiJurnal($data)
    {
        return $this->insert($data);
    }
    public function getIdTransaksiLast()
    {
        $transaksi_format = "TRN-";
        $query = $this->select('id')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();
        if ($query->resultID->num_rows > 0) {
            $row = $query->getRow();
            $lastTransaksi = $row->id;

            $newLastTransaksi = $lastTransaksi + 1;

            return $newLastTransaksi;
        } else {
            $newLastTransaksi = 1;
            return $newLastTransaksi; // Handle the case where no transactions are found
        }
    }

    public function getNoTransaksiLast($type)
    {
        if ($type == "penjualan") {
            $transaksi_format = "SI-";
        } else if ($type == "pembelian") {
            $transaksi_format = "PI-";
        } else if ($type == "penerimaan") {
            $transaksi_format = "RP-";
        } else {
            $transaksi_format = "EXP-";
        }

        $query = $this->select('no_transaksi')
            ->like('no_transaksi', $transaksi_format)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();
        if ($query->resultID->num_rows > 0) {
            $row = $query->getRow();
            $lastTransaksi = $row->no_transaksi;

            // Extract the numerical part and increment it by 1
            $numericPart = (int)substr($lastTransaksi, strlen($transaksi_format));
            $newNumericPart = $numericPart + 1;

            // Format the new transaction number
            $newTransaksi = $transaksi_format . sprintf('%03d', $newNumericPart);

            return $newTransaksi;
        } else {
            return $transaksi_format . "001"; // Handle the case where no transactions are found
        }
    }
}
