<?php

namespace App\Models;

use CodeIgniter\Model;

class BeaCukaiModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bea_cukai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'company_id',
        'type',
        'status',
        'status_perbaikan',

        'jenis_dokumen',
        'no_dokumen',
        'dokumen_type',

        'aju_no',
        'registration_no',
        'registration_date',
        'kppbc_bongkar',
        'kppbc_pengawas',
        'tujuan_tpb',
        'supplier_id',

        'importir_jenis_identitas', //baru
        'importir_identitas', //baru
        'importir_jenis_api', //baru

        'importir_npwp', // hapus
        'importir_name',
        'tpb_no',
        'tpb_date',
        'importir_api',
        'importir_address',
        'pemilik_barang',

        'pemilik_barang_jenis_identitas', //baru
        'pemilik_barang_identitas', //baru
        'pemilik_barang_jenis_api', //baru

        'pemilik_barang_npwp', //hapus
        'pemilik_barang_name',
        'pemilik_barang_address',
        'pemilik_barang_api',
        'ppjk_npwp',
        'ppjk_name',
        'ppjk_date',
        'ppjk_no',
        'ppjk_address',
        'pengangkutan',
        'pengangkutan_sarana',
        'voy_no',
        'pengangkutan_country',
        'kode_pelabuhan_muat',
        'kode_pelabuhan_transit',
        'kode_pelabuhan_bongkar',
        'invoice_id',
        'invoice_date',
        'fasilitas_import_no',
        'fasilitas_import_date',
        'fasilitas_import_code',
        'lc_no',
        'lc_date',
        'bl_no',
        'bl_date',
        'bc_11_no',
        'bc_11_date',
        'bc_11_zip',
        'penimbunan',
        'valuta',
        'ndpbm',
        'fob',
        'freight',
        'asuransi_type',
        'cif_value',
        'cif_price',
        'bruto',
        'netto',
        'item_count',
        'tempat',
        'tanggal',
        'pemberitahu',
        'jabatan',
        'data_dokumen',
        'data_kontainer',
        // 'data_kemasan',
        'status_posting',

        // bc 2.5
        'kantor_pabean',
        'penerima_barang_npwp',
        'penerima_barang_name',
        'penerima_barang_api',
        'penerima_barang_niper',
        'penerima_barang_address',
        'no_packing_list',
        'tanggal_packing_list',
        'no_kontrak',
        'tanggal_kontrak',
        'harga_penyerahan',
        'pembayaran',
        'wajib_bayar',

        // bc 2.6.1 
        'data_jaminan'
    ];

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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'ajuNo'            => 'bea_cukai.aju_no',
            'registrationNo'   => 'bea_cukai.registration_no',
            'registrationDate' => 'bea_cukai.registration_date',
            'tujuanTPBName'    => 'metadata.value',
            'statusPosting'    => 'bea_cukai.status_posting',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'bea_cukai.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bea_cukai.*, 
            metadata.value AS tujuan_tpb_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('metadata', 'bea_cukai.tujuan_tpb = metadata.id', 'left')
            ->groupBy(('bea_cukai.id'))
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $bcDataQry->groupStart();
        }
        if ($addCondition['search']) {
            $bcDataQry
                ->like('aju_no', $addCondition['search']);
        }
        if ($addCondition['search']) {
            $bcDataQry->groupEnd();
        }

        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getById($id)
    {
        $bcData = $this->asObject()
            ->select('bea_cukai.*, country.code as country_code, country.country_name, suppliers.address as supplier_address, sales_order_invoice.createdAt as tanggal_invoice')
            ->join('suppliers', 'bea_cukai.supplier_id = suppliers.id', 'left')
            ->join('country', 'country.code = suppliers.country_code', 'left')
            ->join('sales_order_invoice', 'bea_cukai.invoice_id = sales_order_invoice.id', 'left')
            ->find($id);

        return $bcData;
    }
}