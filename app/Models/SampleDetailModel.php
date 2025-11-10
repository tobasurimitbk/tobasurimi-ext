<?php

namespace App\Models;

use CodeIgniter\Model;

class SampleDetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sample_detail';
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

    public function getSampleDetail($sampleId)
    {
        $sampleAdditionalModel = new SampleAdditionalModel();

        $selectQry = "
            sample_detail.*,
            barang_master_sales.barang_name AS barang,
            satuans.kode_satuan,
            divisis.divisi AS divisi_barang
        ";

        $sampleDetail = $this->asArray()
            ->select($selectQry)
            ->join('barang_master_sales', 'barang_master_sales.id = sample_detail.barang_master_sales_id', 'left')
            ->join('satuans', 'satuans.id = sample_detail.satuan_id', 'left')
            ->join('divisis', 'divisis.id = sample_detail.divisi_barang_id', 'left')
            ->where('sample_detail.sample_id', $sampleId)
            ->where('sample_detail.deletedAt', null)
            ->findAll();

        $resultArr = [];

        foreach ($sampleDetail as $s) {

            $sampleAdditional = $sampleAdditionalModel
                ->select('sample_additional.*,satuans.kode_satuan AS satuan_additional_kode')
                ->join('satuans', 'satuans.id = sample_additional.satuan_additional', 'left')
                ->where('sample_detail_id', $s['id'])
                ->where('sample_additional.deletedAt', null)
                ->findAll();

            $listAdditional = [];
            foreach ($sampleAdditional as $d) {
                array_push($listAdditional, [
                    'id_additional_item' => $d['id'],
                    'additional_item' => $d['additional_item'],
                    'qty_additional' => (float)$d['qty_additional'],
                    'satuan_additional' => $d['satuan_additional'],
                    'satuan_additional_kode' => $d['satuan_additional_kode']
                ]);
            }

            $resultArr[] = [
                'id_barang' => $s['id'],
                'barang_master_sales_id' => $s['barang_master_sales_id'],
                'barang' => $s['barang'],
                'grade' => $s['grade'],
                'an' => $s['an'],
                'pickup_date' => $s['pickup_date'],
                'via' => $s['via'],
                'qty' => (float)$s['qty'],
                'kode_satuan' => $s['kode_satuan'],
                'satuan_id' => $s['satuan_id'],
                'berat_kotor' => (float)$s['berat_kotor'],
                'berat_bersih' => (float)$s['berat_bersih'],
                'note' => $s['note'],
                'list_additional' => $listAdditional,
                'divisi_barang_id' => $s['divisi_barang_id'],
                'divisi_barang_text' => $s['divisi_barang']
            ];
        }

        return $resultArr;
    }
}
