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


    public function getPengembalianBarangList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_surat_jalan'            => 'pengembalian_barang.no_surat_jalan',
            'no_penerimaan_barang'      => 'penerimaan_barang.no_penerimaan_barang',
            'suppliers.name'            => 'suppliers.name',
            'tanggal_surat_jalan'       => 'pengembalian_barang.tanggal_surat_jalan',
            'divisi'                    => 'divisis.divisi',
            'warehouse'                 => 'warehouses.warehouse_name',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'pengembalian_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "pengembalian_barang.*,penerimaan_barang.no_penerimaan_barang,suppliers.name as supplier_name,tanggal_surat_jalan,divisis.divisi as divisi_name,warehouses.warehouse_name";
        $qry = $this->asObject()
            ->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->whereIn('penerimaan_barang.divisi_id', $addCondition['divisi_access_id'])
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $qry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['supplier_id'] || $addCondition['status_post'] || $addCondition['start_date'] || $addCondition['end_date']) {
            $qry->groupStart();
        }

        if ($addCondition['search']) {
            $qry->like('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])->orLike('pengembalian_barang.no_surat_jalan', $addCondition['search']);
        }

        if ($addCondition['divisi_id']) {
            $qry->where('penerimaan_barang.divisi_id', $addCondition['divisi_id']);
        }

        if ($addCondition['warehouse_id']) {
            $qry->where('penerimaan_barang.warehouse_id', $addCondition['warehouse_id']);
        }

        if ($addCondition['supplier_id']) {
            $qry->where('penerimaan_barang.supplier_id', $addCondition['supplier_id']);
        }

        if ($addCondition['status_post'] != "") {
            $qry->where('pengembalian_barang.supplier_id', $addCondition['supplier_id']);
        }

        if ($addCondition['start_date']) {
            $qry->where('penerimaan_barang.createdAt >=', $addCondition['startdate'] . " 00:00:00");
        }

        if ($addCondition['end_date']) {
            $qry->where('penerimaan_barang.createdAt <=', $addCondition['lastdate'] . " 23:59:59");
        }

        if ($addCondition['search'] || $addCondition['divisi_id'] || $addCondition['warehouse_id'] || $addCondition['supplier_id'] || $addCondition['status_post'] || $addCondition['start_date'] || $addCondition['end_date']) {
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

    public function dropdownPenerimaanBarang($companyId, $divisiId, $tipeBahan, $statusPenerimaan)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangList = [];

        $queryPenerimaanBarang = $penerimaanBarangModel->select('penerimaan_barang.*,suppliers.name as supplier_name,warehouses.warehouse_name')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->where('penerimaan_barang.company_id', $companyId)
            ->where('penerimaan_barang.divisi_id', $divisiId)
            ->where('tipe_bahan', $tipeBahan)
            ->where('status_penerimaan', $statusPenerimaan)
            ->where('penerimaan_barang.status_post', "FINISH")
            ->where('penerimaan_barang.deletedAt', null)
            ->findAll();

        for ($i = 0; $i < count($queryPenerimaanBarang); $i++) {
            $retur = $this->where('penerimaan_barang_id', $queryPenerimaanBarang[$i]['id'])->first();
            if ($retur == null) {
                // cari di metadata jenis_dok_aju
                if ($queryPenerimaanBarang[$i]['bc_type'] == 0) {
                    // 0 non pabean
                    $queryPenerimaanBarang[$i]['bc_pengeluaran_id'] = 0;
                } elseif ($queryPenerimaanBarang[$i]['bc_type'] == 48) {
                    // 48 adalah bc 2.3 maka pengeluarannya bc 2.5
                    $queryPenerimaanBarang[$i]['bc_pengeluaran_id'] = 49;
                } else {
                    // 53 adalah bc 4.0 maka pengeluarannya bc 4.1
                    $queryPenerimaanBarang[$i]['bc_pengeluaran_id'] = 54;
                }
                array_push($penerimaanBarangList, $queryPenerimaanBarang[$i]);
            }
        }

        return $penerimaanBarangList;
    }

    public function getReturDetail($id, $penermaanBarangId)
    {
        $result = [];
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $rmImportPoModel = new RMImportPOModel();
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $sppModel = new SppModel();

        $penerimanBarang = $penerimaanBarangModel->where('id', $penermaanBarangId)->first();
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
        $query->where('penerimaan_barang_id', $penermaanBarangId);

        foreach ($query->findAll() as $q) {
            $po = null;
            $spp = null;

            if ($id == null) {
                $pengembalianBarangDetail = null;
            } else {
                $pengembalianBarangDetail = $pengembalianBarangDetailModel->where('pengembalian_barang_id', $id)->where('penerimaan_barang_detail_id', $q['id'])->first();
            }

            if ($penerimanBarang['status_penerimaan'] == "LOKAL" && $penerimanBarang['tipe_bahan'] == "BAKU") {
                // LOKAL BB
                $po = $rmPurchaseOrderModel->where('id', $q['purchase_order_id'])->first();
            } elseif ($penerimanBarang['status_penerimaan'] == "IMPORT" && $penerimanBarang['tipe_bahan'] == "BAKU") {
                // IMPORT BB
                $po = $rmImportPoModel->where('id', $q['purchase_order_id'])->first();
            } else {
                // LOKAL BP DAN IMPORT BP
                $po = $amPurchaseOrderModel->where('id', $q['purchase_order_id'])->first();
                if ($po != null) {
                    $spp = $sppModel->where('id', $po['purchase_request_id'])->first();
                }
            }

            $harga = ($q['harga'] + $q['harga_harian'] + $q['harga_bulanan']);
            $result[] = [
                'id' => $q['id'],
                'kode_barang' => $q['kode_barang'],
                'nama_barang' => $q['barang'] . " - " . $q['spesifikasi'],
                'no_spp' => $spp == null ? "-" : $spp['spp_no'],
                'no_po' => $po == null ? "-" : $po['po_no'],
                'kode_satuan' => $q['kode_satuan'],
                'harga' => $harga,
                'sub_total' => (float)$q['sub_total'],
                'jml_diterima' => (float)$q['jml_masuk'],
                'jml_retur' => $pengembalianBarangDetail == null ? 0 : $pengembalianBarangDetail['jumlah_return'],
                'ket_retur' =>  $pengembalianBarangDetail == null ? "" : $pengembalianBarangDetail['keterangan_return'],
            ];
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
                $po = $rmImportPoModel->where('id', $q['purchase_order_id'])->first();
            } else {
                // LOKAL BP DAN IMPORT BP
                $po = $amPurchaseOrderModel->where('id', $q['purchase_order_id'])->first();
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


                $result[] = [
                    'kode_barang' => $q['kode_barang'],
                    'barang' => $q['barang'] . " - " . $q['spesifikasi'],
                    'barang_master_name' => $q['barang'],
                    'no_surat_jalan' => $pengembalianBarang['no_surat_jalan'],
                    'tanggal' => date('d/m/Y', strtotime('tanggal_surat_jalan')),
                    'divisi' => $penerimanBarang['divisi'],
                    'warehouse_name' => $penerimanBarang['warehouse_name'],
                    'sumber' => $dataStock == null ? "-" : $dataStock['sumber'],
                    'stock_dokumen' => $dataStock == null ? "-" : $dataStock['stock_dokumen'],
                    'bc_type' =>  $dataStock == null ? "-" : $metaDataModel->find($dataStock['bc_id'])['value'],
                    'no_aju' => $dataStock == null ? "-" : $dataStock['no_aju'],
                    'stock_date' => $dataStock == null ? "-" : date('d/m/Y', strtotime($dataStock['stock_date'])),
                    'qty_konversi' => $pengembalianBarangDetail == null ? 0 : $pengembalianBarangDetail['jumlah_return'],
                    'satuan' => $satuanName,
                    'total_harga' => $dataStock == null ? 0 : ($dataStock['harga_umum'] + $dataStock['harga_harian'] + $dataStock['harga_bulanan']),
                    'supplier_name' => $dataStock == null ? "-" : $dataStock['supplier_name'],
                    'barang1_id' => $dataStock == null ? null : $dataStock['barang1_id'],
                    'barang2_id' => $dataStock == null ? null : $dataStock['barang2_id'],
                    'kemasan_id' => $dataStock == null ? null : $dataStock['kemasan_id'],
                    'stock_id' => $dataStock == null ? null : $dataStock['stock_id'],
                    'bc_id' => $dataStock == null ? null : $dataStock['bc_id'],
                    'no_aju' => $dataStock == null ? null : $dataStock['no_aju'],
                    'stock_dokumen' => $dataStock == null ? null : $dataStock['stock_dokumen'],
                ];
            }
        }
        return $result;
    }
}
