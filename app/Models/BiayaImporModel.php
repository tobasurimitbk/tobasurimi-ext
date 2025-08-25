<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayaImporModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'biaya_impor';
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
            'biaya_impor.id'     => 'biaya_impor.id',
            'biaya_impor.divisi_id'     => 'biaya_impor.divisi_id',
            'biaya_impor.tanggal_invoice'         => 'biaya_impor.tanggal_invoice',
            'biaya_impor.no_invoice'               => 'biaya_impor.no_invoice',
            'suppliers.name'               => 'suppliers.name',
            'biaya_impor.port_of_origin'               =>  'biaya_impor.port_of_origin',
            'biaya_impor.port_of_destination'               =>  'biaya_impor.port_of_destination',
            'biaya_impor.vendor_pelayaran_id'               => 'biaya_impor.vendor_pelayaran_id',
            'biaya_impor.total_faktur' => 'biaya_impor.total_faktur',
            'biaya_impor.status_posting_exim'               => 'biaya_impor.status_posting_exim',
            'biaya_impor.status_posting_acc'               => 'biaya_impor.status_posting_acc',
            'biaya_impor.status_posting_audit'               => 'biaya_impor.status_posting_audit',
            'biaya_impor.status_bayar'               => 'biaya_impor.status_bayar',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'biaya_impor.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "biaya_impor.*,
            suppliers.name as supplier_name,
            vendor_pelayaran.nama_vendor,
            divisis.divisi
        ";

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_impor.vendor_pelayaran_id', 'left')
            ->join('suppliers', 'suppliers.id = biaya_impor.supplier_id', 'left')
            ->join('divisis', 'divisis.id = biaya_impor.divisi_id', 'left')
            ->where($condition);

        $totalData = $dataQry->countAllResults(false);

        if (!empty($addCondition['dateStart']) || !empty($addCondition['dateEnd'])) {
            $dataQry->groupStart();
            if (!empty($addCondition['dateStart'])) {
                $dataQry->where('tanggal_invoice >=', $addCondition['dateStart']);
            }
            if (!empty($addCondition['dateEnd'])) {
                $dataQry->where('tanggal_invoice <=', $addCondition['dateEnd']);
            }
            $dataQry->groupEnd();
        }

        if ($addCondition['status_posting']) {
            if ($addCondition['status_posting'] == "POSTING EXIM") {
                $dataQry
                    ->where('biaya_impor.status_posting_exim', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING EXIM") {
                $dataQry
                    ->where('biaya_impor.status_posting_exim', 0);
            } else if ($addCondition['status_posting'] == "POSTING ACC") {
                $dataQry
                    ->where('biaya_impor.status_posting_acc', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING ACC") {
                $dataQry
                    ->where('biaya_impor.status_posting_acc', 0);
            } else if ($addCondition['status_posting'] == "POSTING AUDIT") {
                $dataQry
                    ->where('biaya_impor.status_posting_audit', 1);
            } else if ($addCondition['status_posting'] == "BELUM POSTING AUDIT") {
                $dataQry
                    ->where('biaya_impor.status_posting_audit', 0);
            }
        }

        if (isset($addCondition['search']) && !empty($addCondition['search'])) {
            $dataQry->groupStart()
                ->like('no_invoice', $addCondition['search'])
                ->orLike('biaya_impor.port_of_origin', $addCondition['search'])
                ->orLike('biaya_impor.port_of_destination', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('vendor_pelayaran.nama_vendor', $addCondition['search'])
                ->orLike('biaya_impor.total_faktur', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);

        $data = $dataQry->orderBy($sort, $sortType)
            ->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
            'sort'              => $sort,
            'sortType'          => $sortType
        ];
    }

    public function getDropdownPo($supplierId, $tipePo)
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $rmImportPosModel = new RMImportPOModel();


        if ($tipePo == "IMPORT BAKU") {
            // PO IMPORT BAHAN BAKU
            $poList = $rmImportPosModel
                ->select('rm_import_pos.*,suppliers.name as supplier_name, metadata.value as valas_name')
                ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
                ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                ->where('supplier_id', $supplierId)
                ->where('rm_import_pos.deletedAt', null)
                ->orderBy('rm_import_pos.id', "desc")
                ->findAll();
        } else {
            // PO IMPORT BAHAN PENOLONG
            $poList = $amPurchaseOrderModel
                ->select('am_purchase_orders.*,suppliers.name as supplier_name, metadata.value as valas_name')
                ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                ->where('supplier_id', $supplierId)
                ->where('am_purchase_orders.deletedAt', null)
                ->orderBy('am_purchase_orders.id', "desc")
                ->findAll();
        }

        // List
        $resultArr = array();
        foreach ($poList as $p) {
            array_push($resultArr, [
                'id' => $p['id'],
                'po_no' => $p['po_no'],
                'consigne' => $p['consigne'],
                'port_origin' => $p['port_origin'],
                'port_destination' => $p['port_destination'],
                'valas_name' => $p['valas_name']
            ]);
        }

        return $resultArr;
    }

    public function getDetailBarangPo($poId, $tipePo)
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $rmImportPosModel = new RMImportPOModel();
        $rmImportPosDetailModel = new RMImportPODetailModel();

        $tipePo = "";
        $poListBb = $rmImportPosModel->where('id', $poId)->first();

        if ($tipePo == "IMPORT BAKU" && $poListBb != null) {
            // PO IMPORT BAHAN BAKU
            $poList = $rmImportPosModel
                ->select('rm_import_pos.*,suppliers.name as supplier_name, metadata.value as valas_name')
                ->join('suppliers', 'suppliers.id = rm_import_pos.supplier_id', 'left')
                ->join('metadata', 'metadata.id = rm_import_pos.currency', 'left')
                ->where('rm_import_pos.id', $poId)
                ->first();

            $poBarang = $rmImportPosDetailModel
                ->select('
                    rm_import_po_details.*,
                    barang_master_spesifikasi.spesifikasi,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan
                ')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_import_po_details.spesifikasi_id', 'left')
                ->join('barang_master', 'barang_master.id = rm_import_po_details.barang_id', 'left')
                ->join('satuans', 'satuans.id = rm_import_po_details.unit', 'left')
                ->where('rm_import_po_details.deletedAt', null)
                ->where('rm_import_po_details.rm_import_po_id', $poId)
                ->findAll();

            $tipePo = "IMPORT BAKU";
        } else {
            // PO IMPORT BAHAN PENOLONG
            $poList = $amPurchaseOrderModel
                ->select('am_purchase_orders.*,suppliers.name as supplier_name, metadata.value as valas_name')
                ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id', 'left')
                ->join('metadata', 'metadata.id = am_purchase_orders.currency', 'left')
                ->where('am_purchase_orders.id', $poId)
                ->first();

            $poBarang = $amPurchaseOrderDetailModel
                ->select('
                    am_purchase_order_details.*,
                    barang_master_spesifikasi.spesifikasi,
                    barang_master.barang_name,
                    barang_master.kode_barang,
                    satuans.kode_satuan
                ')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = am_purchase_order_details.spesifikasi_id', 'left')
                ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id', 'left')
                ->join('satuans', 'satuans.id = am_purchase_order_details.unit', 'left')
                ->where('am_purchase_order_details.deletedAt', null)
                ->where('am_purchase_order_details.am_purchase_order_id', $poId)
                ->findAll();

            $tipePo = "IMPORT PENOLONG";
        }

        // PO LIST
        $poDetail = [
            'id' => $poList['id'],
            'po_no' => $poList['po_no'],
            'consigne' => $poList['consigne'],
            'port_origin' => $poList['port_origin'],
            'port_destination' => $poList['port_destination'],
            'valas_name' => $poList['valas_name'],
            'valas_id' => $poList['currency'],
            'shipper' => $poList['shipper'],
            "tipe_po" => $tipePo
        ];

        $poBarangList = array();
        foreach ($poBarang as $b) {
            array_push($poBarangList, [
                'id' => $b['id'],
                'barang_id' => $b['barang_id'],
                'spesifikasi_id' => $b['spesifikasi_id'],
                'kode_barang' => $b['kode_barang'],
                'barang_name' => $b['barang_name'],
                'spesifikasi' => $b['spesifikasi'],
                'qty' => (float)$b['qty'],
                'kode_satuan' => $b['kode_satuan'],
                'harga_satuan' => (float)$b['price'],
                'total_harga' => (float)$b['total'],
                'unit' => $b['unit'],
                'kode_satuan' => $b['kode_satuan']
            ]);
        }


        return [
            'po_detail' => $poDetail,
            'po_barang' => $poBarangList
        ];
    }

    public function generateNumber($tanggalInvoice)
    {
        $tanggalTerimaExplode = explode('/', $tanggalInvoice);

        $month = $tanggalTerimaExplode[1];
        $year = substr($tanggalTerimaExplode[2], -2);
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "/INV/$romanMonth/$year";

        $lastData = $this->asObject()
            ->where('company_id', session()->get("login")->this_company_id)
            ->like('no_invoice', $numberTemplate, 'before')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $invNumber = '001' . $numberTemplate;

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->no_invoice);
            $lastIncrement = intval($asd[0]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }

        return $invNumber;
    }
}
