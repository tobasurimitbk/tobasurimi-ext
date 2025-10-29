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

    public function get_no($bln, $thn, $divisiName, $divisiId)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('pengembalian_barang');
        $builder->select('pengembalian_barang.no_surat_jalan');
        $builder->orderBy('pengembalian_barang.no_surat_jalan', 'desc');
        $builder->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id');
        $builder->where('penerimaan_barang.divisi_id', $divisiId);
        $builder->like('pengembalian_barang.no_surat_jalan', $lastStr);
        $query = $builder->get();

        $kode = 'SJR/' . $divisiName;

        $lastPenerimaan = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['no_surat_jalan']);
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


    public function getPengembalianBarangList(
        $condition,
        $addCondition,
        $limit = 10,
        $offset = 0
    ) {
        $availableSort = [
            'tanggal_surat_jalan'       => 'pengembalian_barang.tanggal_surat_jalan',
            'no_surat_jalan'            => 'pengembalian_barang.no_surat_jalan',
            'multiple_spp_id'           => 'pengembalian_barang.multiple_spp_id',
            'multiple_lpb_id'           => 'pengembalian_barang.multiple_lpb_id',
            'supplier_id'               => 'pengembalian_barang.supplier_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'pengembalian_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "
            pengembalian_barang.*,
            suppliers.name as supplier_name";

        $qry = $this->asArray()
            ->select($selectQry)
            ->join('suppliers', 'suppliers.id = pengembalian_barang.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $qry->countAllResults(false);

        if (
            $addCondition['search'] ||
            $addCondition['start_date'] ||
            $addCondition['end_date']
        ) {
            $qry->groupStart();
        }

        if ($addCondition['start_date']) {
            $qry->where('pengembalian_barang.tanggal_surat_jalan >=', $addCondition['start_date']);
        }

        if ($addCondition['end_date']) {
            $qry->where('pengembalian_barang.tanggal_surat_jalan <=', $addCondition['end_date']);
        }

        if ($addCondition['search']) {
            $qry->groupStart();
            $qry->like('pengembalian_barang.no_surat_jalan', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search']);
            $qry->groupEnd();
        }

        if (
            $addCondition['search'] ||
            $addCondition['start_date'] ||
            $addCondition['end_date']
        ) {
            $qry->groupEnd();
        }

        $totalFilteredData = $qry->countAllResults(false);
        $data = $qry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'  => $sort,
            'sortType'  => $sortType
        ];
    }

    public function dropdownPenerimaanBarang(
        $companyId,
        $supplierId,
        $tipeBahan,
        $statusPenerimaan
    ) {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $queryPenerimaanBarang = $penerimaanBarangModel
            ->asArray()
            ->where('penerimaan_barang.company_id', $companyId)
            ->where('penerimaan_barang.supplier_id', $supplierId)
            ->where('tipe_bahan', $tipeBahan)
            ->where('status_penerimaan', $statusPenerimaan)
            ->where('penerimaan_barang.status_post', "FINISH")
            ->where('penerimaan_barang.deletedAt', null)
            ->where('penerimaan_barang.tanggal >=', '2025-09-01') // diatas bulan 9
            ->orderBy('penerimaan_barang.no_penerimaan_barang', "asc")
            ->findAll();

        return $queryPenerimaanBarang;

        //  for ($i = 0; $i < count($queryPenerimaanBarang); $i++) {
        //     $retur = $this->where('penerimaan_barang_id', $queryPenerimaanBarang[$i]['id'])->first();
        //     if ($retur == null) {
        //         // cari di metadata jenis_dok_aju
        //         if ($queryPenerimaanBarang[$i]['bc_type'] == 0) {
        //             // 0 non pabean
        //             $queryPenerimaanBarang[$i]['bc_pengeluaran_id'] = 0;
        //         } elseif ($queryPenerimaanBarang[$i]['bc_type'] == 48) {
        //             // 48 adalah bc 2.3 maka pengeluarannya bc 2.5
        //             $queryPenerimaanBarang[$i]['bc_pengeluaran_id'] = 49;
        //         } else {
        //             // 53 adalah bc 4.0 maka pengeluarannya bc 4.1
        //             $queryPenerimaanBarang[$i]['bc_pengeluaran_id'] = 54;
        //         }
        //         array_push($penerimaanBarangList, $queryPenerimaanBarang[$i]);
        //     }
        // }


    }

    public function getReturDetail(
        $id,
        $penermaanBarangIdArr,
        $preview_edit = false
    ) {
        $result = [];
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmImportPoModel = new RMImportPOModel();
        $amPurchaseOrderModel = new AMPurchaseOrderModel();

        if (count($penermaanBarangIdArr) == 0) {
            return [];
        }

        $selectQry = "
            penerimaan_barang_detail.*,
            penerimaan_barang.status_penerimaan,
            penerimaan_barang.tipe_bahan,
            penerimaan_barang.no_penerimaan_barang,
            penerimaan_barang.bc_type,
            barang_master.barang_name as barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan
        ";
        $query = $penerimaanBarangDetailModel->asArray()->select($selectQry);
        $query->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left');
        $query->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left');
        $query->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left');
        $query->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left');
        $query->whereIn('penerimaan_barang.id', $penermaanBarangIdArr);

        foreach ($query->findAll() as $q) {
            $po = null;
            $bc_pengeluaran_id = null;

            if ($q['bc_type'] == 0) {
                // 0 non pabean
                $bc_pengeluaran_id = 0;
            } elseif ($q['bc_type'] == 48) {
                // 48 adalah bc 2.3 maka pengeluarannya bc 2.5
                $bc_pengeluaran_id = 49;
            } else {
                // 53 adalah bc 4.0 maka pengeluarannya bc 4.1
                $bc_pengeluaran_id = 54;
            }

            if ($id == null) {
                $pengembalianBarangDetail = null;
            } else {
                $pengembalianBarangDetail = $pengembalianBarangDetailModel
                    ->where('pengembalian_barang_id', $id)
                    ->where('penerimaan_barang_detail_id', $q['id'])
                    ->first();
            }

            if ($q['status_penerimaan'] == "LOKAL" && $q['tipe_bahan'] == "BAKU") {
                // LOKAL BB
                $po = $rmPurchaseOrderModel->where('id', $q['purchase_order_id'])->first();
            } elseif ($q['status_penerimaan'] == "IMPORT" && $q['tipe_bahan'] == "BAKU") {
                // IMPORT BB
                $po = $rmImportPoModel->where('id', $q['purchase_order_id'])->first();
            } else {
                // LOKAL BP DAN IMPORT BP
                $po = $amPurchaseOrderModel
                    ->select('am_purchase_orders.*,purchase_requests.spp_no')
                    ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
                    ->where('am_purchase_orders.id', $q['purchase_order_id'])
                    ->first();
            }

            $harga = (float)$q['harga'] + $q['harga_harian'] + $q['harga_bulanan'];

            if ($preview_edit) {
                if ($pengembalianBarangDetail != null) {
                    $result[] = [
                        'id' => $q['id'],
                        'penerimaan_barang_id' => $q['penerimaan_barang_id'],
                        'spp_id' => $po == null ? "" : $po['purchase_request_id'],
                        'spp_no' => $po == null ? "" : (isset($po['spp_no']) ? $po['spp_no'] : ''),
                        'no_penerimaan_barang' => $q['no_penerimaan_barang'],
                        'kode_barang' => $q['kode_barang'],
                        'barang_name' => $q['barang'],
                        'spesifikasi' => $q['spesifikasi'],
                        'jml_masuk' => (float)$q['jml_masuk'],
                        'kode_satuan' => $q['kode_satuan'],
                        'harga_lpb' => (float)$harga,
                        'sub_total_lpb' => (float)$q['sub_total'],
                        'jumlah_return' => $pengembalianBarangDetail == null ? 0 : (float)$pengembalianBarangDetail['jumlah_return'],
                        'harga_satuan_return' => $pengembalianBarangDetail == null ? $harga :  (float)$pengembalianBarangDetail['harga_satuan_return'],
                        'total_harga_return' => $pengembalianBarangDetail == null ? 0 : (float)$pengembalianBarangDetail['total_harga_return'],
                        'keterangan_return' =>  $pengembalianBarangDetail == null ? "" : $pengembalianBarangDetail['keterangan_return'],
                        'bc_pengeluaran_id' => $bc_pengeluaran_id
                    ];
                }
            } else {
                $result[] = [
                    'id' => $q['id'],
                    'penerimaan_barang_id' => $q['penerimaan_barang_id'],
                    'spp_id' => $po == null ? "" : $po['purchase_request_id'],
                    'spp_no' => $po == null ? "" : (isset($po['spp_no']) ? $po['spp_no'] : ''),
                    'no_penerimaan_barang' => $q['no_penerimaan_barang'],
                    'kode_barang' => $q['kode_barang'],
                    'barang_name' => $q['barang'],
                    'spesifikasi' => $q['spesifikasi'],
                    'jml_masuk' => (float)$q['jml_masuk'],
                    'kode_satuan' => $q['kode_satuan'],
                    'harga_lpb' => (float)$harga,
                    'sub_total_lpb' => (float)$q['sub_total'],
                    'jumlah_return' => $pengembalianBarangDetail == null ? 0 : (float)$pengembalianBarangDetail['jumlah_return'],
                    'harga_satuan_return' => $pengembalianBarangDetail == null ? $harga :  (float)$pengembalianBarangDetail['harga_satuan_return'],
                    'total_harga_return' => $pengembalianBarangDetail == null ? 0 : (float)$pengembalianBarangDetail['total_harga_return'],
                    'keterangan_return' =>  $pengembalianBarangDetail == null ? "" : $pengembalianBarangDetail['keterangan_return'],
                    'bc_pengeluaran_id' => $bc_pengeluaran_id
                ];
            }
        }
        return $result;
    }

    public function getReturBeaCukaiDetail($id)
    {
        $result = [];
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmImportPoModel = new RMImportPOModel();
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $stockDetail2Model = new StockDetail2Model();
        $metaDataModel = new MetadataModel();
        $satuanModel = new SatuansModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();

        $pengembalianBarang = $this->where('id', $id)->first();
        if ($pengembalianBarang == null) {
            return [];
        }
        $penerimaanBarangId = $pengembalianBarang['penerimaan_barang_id'];
        $penerimanBarang = $penerimaanBarangModel
            ->select('penerimaan_barang.*,divisis.divisi,warehouses.warehouse_name')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->where('penerimaan_barang.id', $penerimaanBarangId)
            ->first();
        if ($penerimanBarang == null) {
            return [];
        }

        $selectQry = "
            penerimaan_barang_detail.*,
            barang_master.barang_name as barang,
            barang_master.kode_barang,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan
        ";
        $query = $penerimaanBarangDetailModel->select($selectQry);
        $query->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left');
        $query->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left');
        $query->join('satuans', 'satuans.id = penerimaan_barang_detail.unit', 'left');
        $query->where('penerimaan_barang_id', $penerimaanBarangId);

        foreach ($query->findAll() as $q) {

            $pengembalianBarangDetail = $pengembalianBarangDetailModel->where('pengembalian_barang_id', $id)->where('penerimaan_barang_detail_id', $q['id'])->first();

            if ($penerimanBarang['status_penerimaan'] == "LOKAL" && $penerimanBarang['tipe_bahan'] == "BAKU") {
                // LOKAL BB
                $po = $rmPurchaseOrderModel->where('id', $q['purchase_order_id'])->first();
            } elseif ($penerimanBarang['status_penerimaan'] == "IMPORT" && $penerimanBarang['tipe_bahan'] == "BAKU") {
                // IMPORT BB
                $po = $rmImportPoModel->select('rm_import_pos.*,metadata.value as valas_name')
                    ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                    ->where('rm_import_pos.id', $q['purchase_order_id'])
                    ->first();

                $valasName = $po['valas_name'];
            } else {
                // LOKAL BP DAN IMPORT BP
                $po = $amPurchaseOrderModel->select('am_purchase_orders.*,metadata.value as valas_name')
                    ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                    ->where('am_purchase_orders.id', $q['purchase_order_id'])
                    ->first();

                $valasName = $po['valas_name'];
            }

            if ($pengembalianBarangDetail != null) {
                $dataStock = $stockDetail2Model->getStockDetailByStockDokumen(
                    $po['po_no'],
                    "LPB",
                    $penerimanBarang['company_id'],
                    $penerimanBarang['divisi_id'],
                    $penerimanBarang['warehouse_id'],
                    $q['barang_id'],
                    $q['spesifikasi_id'],
                    null
                );

                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($q['spesifikasi_id']);
                $satuan = $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $satuanName = $satuan == null ? "-" : $satuan['kode_satuan'];
                $bcType =  $metaDataModel->find($dataStock['bc_id']);

                $result[] = [
                    'kode_barang' => $q['kode_barang'],
                    'kode_barang_internal' => $q['kode_barang'],
                    'barang' => $q['barang'] . " - " . $q['spesifikasi'],
                    'barang_master_name' => $q['barang'],
                    'no_surat_jalan' => $pengembalianBarang['no_surat_jalan'],
                    'tanggal' => date('d/m/Y', strtotime('tanggal_surat_jalan')),
                    'divisi' => $penerimanBarang['divisi'],
                    'warehouse_name' => $penerimanBarang['warehouse_name'],
                    'sumber' => $dataStock == null ? "-" : $dataStock['sumber'],
                    'stock_dokumen' => $dataStock == null ? "-" : $dataStock['stock_dokumen'],
                    'bc_type' =>  $dataStock == null ? "-" : ($bcType == null ? "NON PABEAN" : $bcType['value']),
                    'no_aju' => $dataStock == null ? "-" : $dataStock['no_aju'],
                    'stock_date' => $dataStock == null ? "-" : date('d/m/Y', strtotime($dataStock['stock_date'])),
                    'qty_konversi' => $pengembalianBarangDetail == null ? 0 : $pengembalianBarangDetail['jumlah_return'],
                    'satuan' => $satuanName,
                    'total_harga' => $dataStock == null ? 0 : ($dataStock['harga_umum'] + $dataStock['harga_harian'] + $dataStock['harga_bulanan']) *  $pengembalianBarangDetail['jumlah_return'],
                    'supplier_name' => $dataStock == null ? "-" : $dataStock['supplier_name'],
                    'barang1_id' => $dataStock == null ? null : $dataStock['barang1_id'],
                    'barang2_id' => $dataStock == null ? null : $dataStock['barang2_id'],
                    'kemasan_id' => $dataStock == null ? null : $dataStock['kemasan_id'],
                    'stock_id' => $dataStock == null ? null : $dataStock['stock_id'],
                    'bc_id' => $dataStock == null ? null : $dataStock['bc_id'],
                    'stock_dokumen' => $dataStock == null ? null : $dataStock['stock_dokumen'],
                    'tipe_barang' => $dataStock == null ? null : strtoupper(str_replace('_', ' ', $dataStock['tipe_barang'])),
                    'valas_name' => isset($valasName) ? $valasName : '',
                    'type_barang_text' =>  $dataStock == null ? null : strtoupper(str_replace('_', ' ', $dataStock['tipe_barang'])),
                ];
            }
        }
        return $result;
    }
}
