<?php

namespace App\Models;

use CodeIgniter\Model;

class RMImportPODetailModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rm_import_po_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'rm_import_po_id', 'barang_id', 'note', 'unit', 'qty', 'price',
        'disc', 'additional_cost', 'remaining_qty', 'qty_diterima', 'total', 'spesifikasi_id'
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

    public function getPurchaseOrderDetailByPurchaseOrderId($id)
    {
        $arrCondition = [
            'rm_import_po_details.deletedAt' => null,
            'rm_import_po_details.rm_import_po_id' => $id
        ];

        $builder = $this->db->table('rm_import_po_details')
            ->select("rm_import_po_details.*, rm_import_pos.po_no,
        FORMAT(CEILING(rm_import_po_details.qty) * CEILING(rm_import_po_details.price) + CEILING(rm_import_po_details.additional_cost), 'N', 'en-us') AS totalPrice,
        rm_import_pos.status_penerimaan, barang_master.barang_name as nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan, satuans.kode_satuan,barang_master_spesifikasi.spesifikasi")
            ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_import_po_details.spesifikasi_id', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function getPurchaseOrderDetailById($id)
    {
        $arrCondition = [
            'rm_import_po_details.deletedAt' => null,
            'rm_import_po_details.id' => $id
        ];

        $builder = $this->db->table('rm_import_po_details')
            ->select("rm_import_po_details.*, rm_import_pos.po_no,
        FORMAT(CEILING(rm_import_po_details.qty) * CEILING(rm_import_po_details.price) + CEILING(rm_import_po_details.additional_cost), 'N', 'en-us') AS totalPrice,
        rm_import_pos.status_penerimaan, barang_master.barang_name as nama_barang, barang_master.kode_barang, satuans.id as id_satuan, satuans.nama_satuan")
            ->join('rm_import_pos', 'rm_import_pos.id = rm_import_po_details.rm_import_po_id', 'left')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getRow();
    }

    public function getListLPBBahanBaku($rmImportPoID, $penerimaanBarangID = null)
    {
        $res = [];
        $condition = [
            'rm_import_pos.deletedAt' => null,
            'rm_import_po_details.deletedAt' => null
        ];

        $selectQry = "
            rm_import_pos.po_no,
            rm_import_po_details.*,
            barang_master.barang_name AS nama_barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            barang_master_spesifikasi.id AS spesifikasi_id,
            satuans.kode_satuan
        ";

        $rmImportPoModel = new RMImportPOModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $barangs = $rmImportPoModel
            ->select($selectQry)
            ->where($condition)
            ->whereIn('rm_import_pos.id', $rmImportPoID)
            ->join('rm_import_po_details', 'rm_import_po_details.rm_import_po_id = rm_import_pos.id', 'left')
            ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
            ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
            ->join('barang_master_spesifikasi', 'rm_import_po_details.spesifikasi_id = barang_master_spesifikasi.id', 'left')
            ->findAll();


        $jmlOrderTotal = 0;
        $jmlDiterimaInTotal = 0;
        $jmlDiterimaTotal = 0;
        $sisaDiterimaTotal = 0;
        $hargaPerBarangTotal = 0;
        $subTotal = 0;

        foreach ($barangs as $b) {

            $allLPB = $penerimaanBarangDetailModel
                ->select('SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk')
                ->where('purchase_order_id', $b['rm_import_po_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('deletedAt', null)
                ->groupBy('purchase_order_id', 'purchase_order_details_id')
                ->findAll();

            $jmlMasukAll = 0;
            foreach ($allLPB as $a) {
                $jmlMasukAll = $a['jmlMasuk'];
            }

            $firstLPB =  $penerimaanBarangDetailModel->where('penerimaan_barang_id', $penerimaanBarangID)
                ->where('purchase_order_id', $b['rm_import_po_id'])
                ->where('purchase_order_details_id', $b['id'])
                ->where('deletedAt', null)
                ->first();

            $inLPB = ($firstLPB == null) ? 0 : $firstLPB['jml_masuk'];
            $sisaDiterima = $b['qty'] - $jmlMasukAll;

            if ($penerimaanBarangID == null) {
                // CREATE
                if ($sisaDiterima != 0) {
                    $diskonHarga = ($b['disc'] / 100) * ($b['price']);
                    $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

                    $res[] = [
                        'rm_import_po_details_id' => $b['id'],
                        'rm_import_po_id' => $b['rm_import_po_id'],
                        'kode_barang' => $b['kode_barang'],
                        'nama_barang' => $b['nama_barang'] . ' (' . $b['spesifikasi'] . ')',
                        'spesifikasi_name' => $b['spesifikasi'],
                        'nama_barang_master' => $b['nama_barang'],
                        'po_no' => $b['po_no'],
                        'satuan' => $b['kode_satuan'],
                        'jml_order' => $b['qty'],
                        'jml_diterima_lpb' => $inLPB,
                        'jml_diterima_total' => $jmlMasukAll,
                        'sisa_total' => $sisaDiterima,
                        'harga' => $harga,
                        'sub_total' => ($inLPB * $harga),
                        'keterangan' => $b['note']
                    ];

                    $jmlOrderTotal += $b['qty'];
                    $jmlDiterimaInTotal += $inLPB;
                    $jmlDiterimaTotal +=   $jmlMasukAll;
                    $sisaDiterimaTotal += $sisaDiterima;
                    $hargaPerBarangTotal += $harga;
                    $subTotal += ($inLPB * $harga);
                }
            } else {
                // UPDATE
                if ($inLPB != 0) {
                    // TAMPILKAN YANG MASIH ADA SISA AJA
                    $diskonHarga = ($b['disc'] / 100) * ($b['price']);
                    $harga = ($b['price'] - $diskonHarga) + $b['additional_cost'];

                    $res[] = [
                        'rm_import_po_details_id' => $b['id'],
                        'rm_import_po_id' => $b['rm_import_po_id'],
                        'kode_barang' => $b['kode_barang'],
                        'nama_barang' => $b['nama_barang'] . ' (' . $b['spesifikasi'] . ')',
                        'spesifikasi_name' => $b['spesifikasi'],
                        'nama_barang_master' => $b['nama_barang'],
                        'po_no' => $b['po_no'],
                        'satuan' => $b['kode_satuan'],
                        'jml_order' => $b['qty'],
                        'jml_diterima_lpb' => $inLPB,
                        'jml_diterima_total' => $jmlMasukAll,
                        'sisa_total' => $sisaDiterima,
                        'harga' => $harga,
                        'sub_total' => ($inLPB * $harga),
                        'keterangan' => $b['note']
                    ];

                    $jmlOrderTotal += $b['qty'];
                    $jmlDiterimaInTotal += $inLPB;
                    $jmlDiterimaTotal +=   $jmlMasukAll;
                    $sisaDiterimaTotal += $sisaDiterima;
                    $hargaPerBarangTotal += $harga;
                    $subTotal += ($inLPB * $harga);
                }
            }
        }

        return [
            'result' => $res,
            'jml_order_total' => $jmlOrderTotal,
            'jml_diterima_in_total' => $jmlDiterimaInTotal,
            'jml_diterima_total' => $jmlDiterimaTotal,
            'sisa_diterima_total' => $sisaDiterimaTotal,
            'harga_per_barang_total' => $hargaPerBarangTotal,
            'sub_total' => $subTotal
        ];
    }

    public function getPoBBImportDetailById($id)
    {
        $selectQry = "rm_import_po_details.*";

        $condition = [
            "rm_import_po_id" => $id,
        ];

        $poBBImportDetailData = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->findAll();

        return $poBBImportDetailData;
    }
}
