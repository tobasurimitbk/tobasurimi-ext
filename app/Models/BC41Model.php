<?php

namespace App\Models;

use CodeIgniter\Model;

class BC41Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_41';
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


    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sales_order_lain.divisi_id' => 'sales_order_lain.divisi_id',
            'sales_order_lain.warehouse_id' => 'sales_order_lain.warehouse_id',
            'sales_order_lain.no_sales_order' => 'sales_order_lain.no_sales_order',
            'customers.name' => 'customers.name',
            'bc_41.no_aju' => 'bc_41.no_aju',
            'bc_41.createdAt' => 'bc_41.createdAt',
            'bc_41.status_posting' => 'bc_41.status_posting',
            'bc_41.status_dokumen' => 'bc_41.status_dokumen',
            'pengembalian_barang.no_surat_jalan' => 'pengembalian_barang.no_surat_jalan',
            'suppliers.name' => 'suppliers.name'

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'bc_41.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_41.*,
        sales_order_lain.no_sales_order,
        divisis.divisi,
        warehouses.warehouse_name,
        customers.name AS customer_name,
        suppliers.name as supplier_name,
        pengembalian_barang.no_surat_jalan";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->join('sales_order_lain', 'sales_order_lain.id = bc_41.sales_order_lain_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->join('pengembalian_barang', 'pengembalian_barang.id = bc_41.pengembalian_barang_id', 'left')
            ->join('penerimaan_barang', 'pengembalian_barang.penerimaan_barang_id = penerimaan_barang.id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusPosting'] || $addCondition['noAju']  && (empty($addCondition['mulaiTanggalBC41']) && empty($addCondition['selesaiTanggalBC41']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusPosting']) {
            if ($addCondition['statusPosting'] == "ALL") {
                $bcDataQry->whereIn('bc_41.status_posting', ['1', '0']);
            } elseif ($addCondition['statusPosting'] == "SUDAH POSTING") {
                $bcDataQry->where('bc_41.status_posting', "1");
            } else if ($addCondition['statusPosting'] == "BELUM POSTING") {
                $bcDataQry->where('bc_41.status_POSTING', "0");
            }
        }

        if ($addCondition['asalPengeluaran']) {
            if ($addCondition['asalPengeluaran'] == "RETUR") {
                $bcDataQry->where('pengembalian_barang_id !=', null);
            } else
            if ($addCondition['asalPengeluaran'] == "PENJUALAN") {
                $bcDataQry->where('sales_order_lain_id !=', null);
            }
        }


        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju'])->orLike('no_daftar', $addCondition['noAju']);
        }

        if ($addCondition['statusPosting'] || $addCondition['noAju']  && (empty($addCondition['mulaiTanggalBC41']) && empty($addCondition['selesaiTanggalBC41']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC41'] && $addCondition['selesaiTanggalBC41']) {
            $bcDataQry->groupStart();
            $mulaiTanggalBC41Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalBC41']), "Y-m-d");
            $selesaiTanggalBC41Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalBC41']), "Y-m-d");

            if ($addCondition['mulaiTanggalBC41']) {
                $bcDataQry->where('bc_41.createdAt >=', $mulaiTanggalBC41Timestamp);
            }

            if ($addCondition['selesaiTanggalBC41']) {
                $bcDataQry->where('bc_41.createdAt <=', $selesaiTanggalBC41Timestamp);
            }

            $bcDataQry->groupEnd();
        }

        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function detail($id)
    {
        $bc41 = $this->find($id);

        if ($bc41['sales_order_lain_id'] != null) {
            $selectQry = "
                bc_41.*,
                bc_41.sales_order_lain_id as reference_id,
                sales_order_lain.no_sales_order as no_reference,
                sales_order_lain.tanggal as tanggal_reference,
                sales_order_lain.keterangan,
                customers.name AS nama_penerima,
                customers.address AS alamat_penerima,
                country.country_name,
                divisis.divisi,
                warehouses.warehouse_name
            ";

            $result = $this->asArray()->select($selectQry)
                ->join('sales_order_lain', 'sales_order_lain.id = bc_41.sales_order_lain_id', 'left')
                ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
                ->join('country', 'country.id = customers.country_id', 'left')
                ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
                ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
                ->where('bc_41.id', $id)
                ->where('bc_41.deletedAt', null)
                ->first();
        } else {
            $selectQry = "
                bc_41.*,
                bc_41.pengembalian_barang_id as reference_id,
                pengembalian_barang.no_surat_jalan as no_reference,
                pengembalian_barang.tanggal_surat_jalan as tanggal_reference,
                pengembalian_barang.keterangan,                
                suppliers.name AS nama_penerima,
                suppliers.address AS alamat_penerima,
                divisis.divisi,
                warehouses.warehouse_name
            ";

            $result = $this->asArray()->select($selectQry)
                ->join('pengembalian_barang', 'pengembalian_barang.id = bc_41.pengembalian_barang_id', 'left')
                ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
                ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
                ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
                ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
                ->where('bc_41.id', $id)
                ->where('bc_41.deletedAt', null)
                ->first();
        }

        return $result;
    }

    public function getListSalesOrderLain($salesOrderLainId = null)
    {
        $metaDataModel = new MetadataModel();
        $salesOrderLainModel = new SalesOrderLainModel();

        $bcFirst = $metaDataModel->getBCFirst("BC 4.1");
        $selectQry = "
            sales_order_lain.*,
            customers.name AS nama_penerima,
            customers.address AS alamat_penerima,
            country.country_name,
            divisis.divisi,
            warehouses.warehouse_name
        ";

        $salesOrderList = $salesOrderLainModel->select($selectQry)
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id')
            ->where('status_posting', '1')
            ->where('sales_order_lain.bc_id', $bcFirst['id'])
            ->where('sales_order_lain.deletedAt', null)
            ->findAll();

        $result = array();

        foreach ($salesOrderList as $s) {
            $bc41 = $this->where('sales_order_lain_id', $s['id'])->first();

            if ($salesOrderLainId != null) {
                if ($bc41 == null || $salesOrderLainId == $s['id']) {
                    array_push($result, $s);
                }
            } else {
                if ($bc41 == null) {
                    array_push($result, $s);
                }
            }
        }

        $resultData = array();
        foreach ($result as $r) {
            $resultData[] = [
                'id' => ($r['id']),
                'no_reference' => $r['no_sales_order'],
                'nama_penerima' => $r['nama_penerima'],
                'alamat_penerima' => $r['alamat_penerima'],
                'tanggal_reference' => date('d/m/Y', strtotime('tanggal_sales_order')),
                'divisi' => $r['divisi'],
                'warehouse_name' => $r['warehouse_name'],
                'keterangan' => $r['keterangan']
            ];
        }

        return $resultData;
    }

    public function getListPengembalianBarang($companyId, $pengembalianBarangId = null)
    {
        $metaDataModel = new MetadataModel();
        $pengembalianBarangModel = new PengembalianBarangModel();

        $bcFirst = $metaDataModel->getBCFirst("BC 4.1");
        $selectQry = "
            pengembalian_barang.*,
            suppliers.name AS nama_penerima,
            suppliers.address AS alamat_penerima,
            divisis.divisi,
            warehouses.warehouse_name,
            penerimaan_barang.no_penerimaan_barang
        ";

        $pengeluaranBarang = $pengembalianBarangModel->select($selectQry)
            ->join('penerimaan_barang', 'penerimaan_barang.id = pengembalian_barang.penerimaan_barang_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id')
            ->where('pengembalian_barang.status_post', 'FINISH')
            ->where('pengembalian_barang.bc_pengeluaran_id', $bcFirst['id'])
            ->where('pengembalian_barang.deletedAt', null)
            ->where('pengembalian_barang.company_id', $companyId)
            ->findAll();
        $result = array();

        foreach ($pengeluaranBarang as $s) {
            $bc41 = $this->where('pengembalian_barang_id', $s['id'])->first();
            if ($pengembalianBarangId != null) {
                if ($bc41 == null || $pengembalianBarangId == $s['id']) {
                    array_push($result, $s);
                }
            } else {
                if ($bc41 == null) {
                    array_push($result, $s);
                }
            }
        }

        $resultData = array();
        foreach ($result as $r) {
            $resultData[] = [
                'id' => ($r['id']),
                'no_reference' => $r['no_surat_jalan'] . " (" . $r['no_penerimaan_barang'] . ")",
                'nama_penerima' => $r['nama_penerima'],
                'alamat_penerima' => $r['alamat_penerima'],
                'tanggal_reference' => date('d/m/Y', strtotime($r['tanggal_surat_jalan'])),
                'divisi' => $r['divisi'],
                'warehouse_name' => $r['warehouse_name'],
                'keterangan' => $r['keterangan'],

            ];
        }

        return $resultData;
    }

    public function detailBarang($bcId, $kodeBarang, $salesOrderLainId, $pengembalianBarangId)
    {
        $listBarang = $this->barang($salesOrderLainId, $pengembalianBarangId);
        $payload = json_decode($this->find($bcId)['payload']);
        $result = null;
        // dd($kodeBarang, $bcId, $salesOrderLainId);

        foreach ($listBarang as $l) {
            if ($kodeBarang == $l['kode_barang']) {
                $result = [
                    'barangDetail' => $l,
                    'bcDetail' => null
                ];
            }
        }


        foreach ($payload->barang as $b) {
            if ($b->kodeBarang == $result['barangDetail']['kode_barang']) {
                $result['bcDetail'] = $b;
            }
        }

        return $result;
    }

    public function barang($salesOrderLainId, $pengembalianBarangId)
    {
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $pengembalianBarangDetailModel = new PengembalianBarangModel();

        if ($salesOrderLainId != null) {
            $detailBarang = $salesOrderLainDetailModel->detail($salesOrderLainId);
        } else {
            $detailBarang = $pengembalianBarangDetailModel->getReturBeaCukaiDetail($pengembalianBarangId);
        }

        $result = [];

        foreach ($detailBarang as $item) {
            $key = $item['barang1_id'] . '-' . $item['kemasan_id'];
            if (!isset($result[$key])) {
                $result[$key] = $item;
                $result[$key]['total_harga'] = (int)$item['total_harga'];
                $result[$key]['qty_konversi'] = (int)$item['qty_konversi'];
            } else {
                $result[$key]['total_harga'] += (int)$item['total_harga'];
                $result[$key]['qty_konversi'] += (int)$item['qty_konversi'];
            }
        }

        return array_values($result);
    }

    public function isCompleteFormHeader($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kodeTujuanPengiriman != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormEntitas($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->entitas) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormDokumen($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->dokumen) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPengangkut($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->pengangkut) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPetiKemas($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->kemasan) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormTransaksi($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->bruto != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormBarang($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if (count($payload->barang) != 0) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPungutan($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kodeLokasiBayar != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPernyataan($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->namaTtd != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }
}
