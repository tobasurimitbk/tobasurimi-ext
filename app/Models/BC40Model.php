<?php

namespace App\Models;

use CodeIgniter\Model;

class BC40Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_40';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
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
        // $db = \Config\Database::connect();
        $availableSort = [
            'bc_40.createdAt'                        => 'bc_40.createdAt',
            'bc_40.no_aju'                           => 'bc_40.no_aju',
            'bc_purchase_order.po_type'              => 'bc_purchase_order.po_type',
            'bc_purchase_order.multiple_lpb_no'      => 'bc_purchase_order.multiple_lpb_no',
            'bc_purchase_order.multiple_po_no'       => 'bc_purchase_order.multiple_po_no',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'bc_purchase_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_40.*,
            bc_purchase_order.multiple_po_no,
            bc_purchase_order.id AS bc_purchase_order_id,
            bc_purchase_order.multiple_lpb_no,
            bc_purchase_order.po_type,
            bc_purchase_order.status_posting,
            bc_purchase_order.no_daftar,
            bc_purchase_order.createdAt as tanggal_dokumen,
            suppliers.name AS supplier_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->whereIn('po_type', ["LOKAL BAKU", "LOKAL PENOLONG"])
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_40.bc_purchase_order_id', 'right')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusLPB']) {
            $bcDataQry->groupStart();
            if ($addCondition['statusLPB'] != "SEMUA") {
                $bcDataQry->where('bc_purchase_order.po_type', $addCondition['statusLPB']);
            } else {
                $bcDataQry->whereIn('bc_purchase_order.po_type', ["LOKAL BAKU", "LOKAL PENOLONG"]);
            }
            $bcDataQry->groupEnd();
        }

        if ($addCondition['statusPosting'] && $addCondition['statusPosting'] != "SEMUA") {
            $bcDataQry->groupStart();
            if ($addCondition['statusPosting'] == "SUDAH POSTING") {
                $bcDataQry->where('bc_purchase_order.status_posting', '1');
            } else {
                $bcDataQry->where('bc_purchase_order.status_posting', '0');
            }
            $bcDataQry->groupEnd();
        }

        if ($addCondition['searchData']) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['searchData']) {
            $bcDataQry->like('suppliers.name', $addCondition['searchData'])
                ->orLike('multiple_lpb_no', $addCondition['searchData'])
                ->orLike('no_aju', $addCondition['searchData'])
                ->orLike('bc_purchase_order.no_daftar', $addCondition['searchData']);
        }

        if ($addCondition['searchData']) {
            $bcDataQry->groupEnd();
        }

        if (!empty($addCondition['search'])) {
            $searchTerm = $addCondition['search'];
            $bcDataQry->groupStart();
            $bcDataQry->like('suppliers.name', $searchTerm)
                ->orLike('bc_23.no_aju', $searchTerm)
                ->orLike('bc_purchase_order.multiple_lpb_no', $searchTerm)
                ->orLike('bc_purchase_order.no_daftar', $searchTerm);
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC40'] || $addCondition['selesaiTanggalBC40']) {
            $bcDataQry->groupStart();

            if ($addCondition['mulaiTanggalBC40']) {
                $bcDataQry->where('date(bc_purchase_order.createdAt) >=', $addCondition['mulaiTanggalBC40']);
            }

            if ($addCondition['selesaiTanggalBC40']) {
                $bcDataQry->where('date(bc_purchase_order.createdAt) <=',  $addCondition['selesaiTanggalBC40']);
            }

            $bcDataQry->groupEnd();
        }



        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);
        // var_dump($db->getLastQuery());
        // die;
        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function get($bcPurchaseOrderID)
    {
        return $this->asArray()->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->first();
    }

    public function isCompleteFormHeader($bcPurchaseOrderID)
    {
        $isCompleteForm = false;
        $data = $this->get($bcPurchaseOrderID);
        if ($data == null) {
            $isCompleteForm = false;
        } else {
            if ($data['no_aju'] != null && $data['kode_kantor'] != null && $data['kode_jenis_tpb'] != null && $data['kode_tujuan_pengiriman'] != null) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPernyataan($bcPurchaseOrderID)
    {
        $isCompleteForm = false;
        $data = $this->get($bcPurchaseOrderID);
        if ($data == null) {
            $isCompleteForm = false;
        } else {
            if ($data['nama_ttd'] != null && $data['kota_ttd'] != null && $data['tanggal_ttd'] != null && $data['jabatan_ttd'] != null) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormEntitas($bcPurchaseOrderID)
    {
        $bcEntitasModel = new BCEntitasModel();
        return $bcEntitasModel->get($bcPurchaseOrderID) == null ? false : true;
    }

    public function isCompleteFormDokumen($bcPurchaseOrderID)
    {
        $bcDokumenModel = new BCDokumenModel();
        return $bcDokumenModel->get($bcPurchaseOrderID) == null ? false : true;
    }

    public function isCompleteFormPengangkut($bcPurchaseOrderID)
    {
        $bc23PengangkutModel = new BCPengangkutModel();
        return $bc23PengangkutModel->get($bcPurchaseOrderID) == null ? false : true;
    }

    public function isCompleteFormPetiKemas($bcPurchaseOrderID)
    {
        $bcKemasanModel = new BCKemasanModel();

        $kemasan = $bcKemasanModel->getLast($bcPurchaseOrderID) != null ? true : false;

        return $kemasan;
    }

    public function isCompleteFormTransaksi($bcPurchaseOrderID)
    {
        $data = $this->get($bcPurchaseOrderID);
        if ($data == null) {
            return false;
        } else {
            return ($data['nilai_jasa'] != null && $data['harga_perolehan'] != null && $data['volume'] != null && $data['bruto'] != null) ? true : false;
        }
    }

    public function isCompleteFormBarang($bcPurchaseOrderID)
    {
        $bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $bcBarangModel = new BCBarangModel();

        $lpbDetail = $bcPurchaseOrderModel->findDetailBarang($bcPurchaseOrderID);

        $totalPerluDiisi = count($lpbDetail);
        $totalSudahDiisi = 0;
        foreach ($lpbDetail as $l) {
            $bcDokumenBarang =  $bcBarangModel->where('penerimaan_barang_id', $l['penerimaan_barang_id'])->where('barang1_id', $l['barang1_id'])->first();
            if ($bcDokumenBarang != null) {
                $totalSudahDiisi++;
            }
        }

        return $totalSudahDiisi == $totalPerluDiisi ? true : false;
    }

    public function isCompleteFormPungutan($bcPurchaseOrderID)
    {
        $bcBarangTarifModel = new BCBarangTarifModel();
        $barangTarif = $bcBarangTarifModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->findAll();

        return count($barangTarif) == 0 ? false : true;
    }
}
