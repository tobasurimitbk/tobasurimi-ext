<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23BarangTarifModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23_barang_tarif';
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

    public function get($bc23BarangID)
    {
        $metaDataModel = new MetadataModel();

        $result = [];
        foreach ($$this->where('bc_23_barang_id', $bc23BarangID)->where('deletedAt', null)->findAll() as $r) {
            $kodeFasilitasTarif = $metaDataModel->bcMetaDataHelper("Kode Fasilitas Tarif BC", $r['kode_fasilitas_tarif'], null);
            $kodeJenisTarif = $metaDataModel->bcMetaDataHelper("Kode Jenis Tarif BC", $r['kode_jenis_tarif'], null);
            $kodeSatuanBarang = $metaDataModel->bcMetaDataHelper("Kode Satuan BC", $r['kode_satuan_barang'], null);

            $result[] = [
                'barang_tarif_kode_fasilitas_tarif' => encrypt($r['kode_fasilitas_tarif']),
                'barang_tarif_kode_fasilitas_tarif_text' => '(' . $kodeFasilitasTarif['value'] . ') ' . $kodeFasilitasTarif['description'],
                'barang_tarif_kode_jenis_tarif' => encrypt($r['kode_jenis_tarif']),
                'barang_tarif_kode_jenis_tarif_text' => "($kodeJenisTarif[value]) kodeJenisTarif[description]",
                'barang_tarif_kode_satuan_barang' => encrypt($r['kode_satuan_barang']),
                'barang_tarif_kode_satuan_barang_text' => "($kodeSatuanBarang[value]) kodeSatuanBarang[description]",
                'barang_tarif_seri_barang' => $r['seri_barang'],
                'id' => $r['id'],
                'jumlah_satuan_bm'  => $r['jumlah_satuan_bea_masuk'],
                'kode_jenis_pungutan' => "BM (BEA MASUK)",
                'nilai_bayar' => $r['nilai_bayar'],
                'nilai_fasilitas' => $r['nilai_fasilitas'],
                'nilai_sudah_dilunasi' => $r['nilai_sudah_dilunasi'],
                'tarif_bm' => $r['tarif_bea_masuk'],
                'tarif_fasilitas' => $r['tarif_fasilitas']
            ];
        }
        return $result;
    }
}
