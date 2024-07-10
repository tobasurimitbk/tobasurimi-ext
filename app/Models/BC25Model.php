<?php

namespace App\Models;

use CodeIgniter\Model;

class BC25Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_25';
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
            'bc_25.no_aju' => 'bc_25.no_aju',
            'bc_25.createdAt' => 'bc_25.createdAt',
            'bc_25.status_posting' => 'bc_25.status_posting',
            'bc_25.status_dokumen' => 'bc_25.status_dokumen'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'bc_25.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_25.*,
        sales_order_lain.no_sales_order,
        divisis.divisi,
        warehouses.warehouse_name,
        customers.name AS customer_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->join('sales_order_lain', 'sales_order_lain.id = bc_25.sales_order_lain_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusPosting'] || $addCondition['noAju']  && (empty($addCondition['mulaiTanggalBC25']) && empty($addCondition['selesaiTanggalBC25']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusPosting']) {
            if ($addCondition['statusPosting'] == "ALL") {
                $bcDataQry->whereIn('bc_25.status_posting', ['1', '0']);
            } elseif ($addCondition['statusPosting'] == "SUDAH POSTING") {
                $bcDataQry->where('bc_25.status_posting', "1");
            } else if ($addCondition['statusPosting'] == "BELUM POSTING") {
                $bcDataQry->where('bc_25.status_POSTING', "0");
            }
        }

        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju'])->orLike('no_daftar', $addCondition['noAju']);
        }

        if ($addCondition['statusPosting'] || $addCondition['noAju']  && (empty($addCondition['mulaiTanggalBC25']) && empty($addCondition['selesaiTanggalBC25']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC25'] && $addCondition['selesaiTanggalBC25']) {
            $bcDataQry->groupStart();
            $mulaiTanggalBC25Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalBC25']), "Y-m-d");
            $selesaiTanggalBC25Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalBC25']), "Y-m-d");

            if ($addCondition['mulaiTanggalBC25']) {
                $bcDataQry->where('bc_25.createdAt >=', $mulaiTanggalBC25Timestamp);
            }

            if ($addCondition['selesaiTanggalBC25']) {
                $bcDataQry->where('bc_25.createdAt <=', $selesaiTanggalBC25Timestamp);
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
        $selectQry = "
            bc_25.*,
            sales_order_lain.no_sales_order,
            customers.name AS nama_customer,
            customers.address AS alamat_customer,
            country.country_name,
            divisis.divisi,
            warehouses.warehouse_name
        ";

        $result = $this->asArray()->select($selectQry)
            ->join('sales_order_lain', 'sales_order_lain.id = bc_25.sales_order_lain_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->join('country', 'country.id = customers.country_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id')
            ->where('bc_25.id', $id)
            ->where('bc_25.deletedAt', null)
            ->first();

        return $result;
    }

    public function getListSalesOrderLain($salesOrderLainId = null)
    {
        $metaDataModel = new MetadataModel();
        $salesOrderLainModel = new SalesOrderLainModel();

        $bcFirst = $metaDataModel->getBCFirst("BC 2.5");
        $selectQry = "
            sales_order_lain.*,
            customers.name AS nama_customer,
            customers.address AS alamat_customer,
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
            $bc25 = $this->where('sales_order_lain_id', $s['id'])->first();

            if ($salesOrderLainId != null) {
                if ($bc25 == null || $salesOrderLainId == $s['id']) {
                    array_push($result, $s);
                }
            } else {
                if ($bc25 == null) {
                    array_push($result, $s);
                }
            }
        }

        return $result;
    }

    public function dropdownKemasan($salesOrderLainId)
    {
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $selectQry = "
            sales_order_lain_detail.*, 
            stock.tipe_barang, 
            stock.barang2_id,
            barang_master.kode_barang,
            CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang, 
        ";

        $dataList = $salesOrderLainDetailModel
            ->select($selectQry)
            ->join('stock', 'stock.id = sales_order_lain_detail.stock_id', 'left')
            ->join('barang_master', 'barang_master.id = stock.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id', 'left')
            ->where('sales_order_lain_id', $salesOrderLainId)
            ->where('stock.tipe_barang', "bahan_penolong") // KEMASAN AMBIL DARI BAHAN PENOLONG 
            ->findAll();

        return $dataList;
    }

    // BEA CUKAI FUNCTION
    public function detailBarang($bcId, $kodeBarang, $salesOrderLainId)
    {
        $listBarang = $this->barang($salesOrderLainId);
        $payload = json_decode($this->find($bcId)['payload']);
        $result = null;

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

    public function barang($salesOrderLainId)
    {
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $detailBarang = $salesOrderLainDetailModel->detail($salesOrderLainId);
        $result = [];

        foreach ($detailBarang as $item) {
            $key = $item['barang1_id'] . '-' . $item['kemasan_id'];
            if (!isset($result[$key])) {
                $result[$key] = $item;
                $result[$key]['total_harga'] = (int)$item['total_harga'];
                $result[$key]['qty_konversi'] = (int)$item['qty_konversi'];
            } else {
                $result[$key]['total_harga'] = (int)$item['total_harga'];
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
            if (count($payload->kontainer) != 0 && count($payload->kemasan) != 0) {
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
            if ($payload->kodeValuta) {
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

    public function isCompleteFormPernyataan($id)
    {
        $isCompleteForm = false;
        $payload = json_decode($this->find($id)['payload']);
        if ($payload == null) {
            $isCompleteForm = false;
        } else {
            if ($payload->kotaTtd != "") {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }
}
