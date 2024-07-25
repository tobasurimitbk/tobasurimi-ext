<?php

namespace App\Models;

use CodeIgniter\Model;

class PengembalianBarangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pengembalian_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function get_no($bln, $thn, $last_day, $warehouseKode, $warehouseID)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('pengembalian_barang');
        $builder->select('pengembalian_barang.no_surat_jalan');
        $builder->orderBy('pengembalian_barang.no_surat_jalan', 'desc');
        $builder->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id');
        $builder->where('penerimaan_barang.warehouse_id', $warehouseID);
        // $builder->where('pengembalian_barang.createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")
        //     ->where('pengembalian_barang.createdAt <=', $last_day . " 23:59:59");
        $builder->like('pengembalian_barang.no_surat_jalan', $lastStr);
        $query = $builder->get();

        $kode = 'SJ/' . $warehouseKode;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['pengembalian_barang.no_surat_jalan']);
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
