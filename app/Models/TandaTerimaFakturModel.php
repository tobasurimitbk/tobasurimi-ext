<?php

namespace App\Models;

use CodeIgniter\Model;

class TandaTerimaFakturModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tanda_terima_faktur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'supplier_id',
        'faktur_no',
        'jatuh_tempo',
        'nominal_faktur',
        'invoice_date',
        'receive_date',
        'potongan',
        'recipient',
        'tambahan',
        'faktur_type',
        'information',
        'tipe_bahan',
        'user_id',
        'status_update'
    ];

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

    public function getInvoiceList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'receive_date'  => 'tanda_terima_faktur.receive_date',
            'faktur_no'     => 'tanda_terima_faktur.faktur_no',
            'suppliers.name' => 'suppliers.name',
            'nominal_faktur' => 'nominal_faktur',
            'recipient'      => 'recipient',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'tanda_terima_faktur.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "tanda_terima_faktur.*, 
                      suppliers.name AS supplierName,
                      users.name AS userName";

        $tandaTerimaQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = tanda_terima_faktur.supplier_id')
            ->join('users', 'users.id = tanda_terima_faktur.user_id');

        if ($addCondition['search']) {
            $tandaTerimaQry->groupStart();
            $tandaTerimaQry->like('faktur_no', $addCondition['search']);
            $tandaTerimaQry->groupEnd();
        }

        if ($addCondition['start'] || $addCondition['finish'] && $addCondition['search'] != "") {
            $tandaTerimaQry->groupStart();

            if ($addCondition['start']) {
                $tandaTerimaQry->where('receive_date >=', $addCondition['start']);
            }

            if ($addCondition['finish']) {
                $tandaTerimaQry->where('receive_date <=', $addCondition['finish']);
            }

            $tandaTerimaQry->groupEnd();
        }

        $totalData = $tandaTerimaQry->countAllResults(false);
        $totalFilteredData = $tandaTerimaQry->countAllResults(false);
        $data = $tandaTerimaQry->orderBy($sort, $sortType)->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getTandaTerimaFakturInPembayaran($tandaTerimaFakturID)
    {
        $condition = [
            'local_po_payments.tanda_terima_faktur_id' => $tandaTerimaFakturID,
            'local_po_payments.deletedAt' => null
        ];
        $localPoPaymentModel = new LocalPOPaymentModel();
        $res = $localPoPaymentModel
            ->where($condition)
            ->first();

        return $res;
    }

    public function getListTandaTerimaFakturNotProcessed($supplierID)
    {
        $condition = [
            'tanda_terima_faktur.supplier_id' => $supplierID,
            'local_po_payments.tanda_terima_faktur_id' => null,
            'tanda_terima_faktur.deletedAt' => null
        ];
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $res = $tandaTerimaFakturModel
            ->select('tanda_terima_faktur.id, tanda_terima_faktur.faktur_no')
            ->join('local_po_payments', 'local_po_payments.tanda_terima_faktur_id = tanda_terima_faktur.id',  'LEFT')
            ->where($condition)
            ->orderBy('tanda_terima_faktur.id', "ASC")
            ->findAll();

        return $res;
    }

    public function getListPenerimaanBarangLokalBPNotProcessed($supplierID)
    {
        $condition = [
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.deletedAt' => null,
            'penerimaan_barang_detail.deletedAt' => null,
            'penerimaan_barang_detail.jml_masuk !=' => 0,
            'penerimaan_barang.supplier_id' => $supplierID
        ];

        $penerimaanBarangModel = new PenerimaanBarangModel();
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();

        $selectQry = "am_purchase_orders.po_no, penerimaan_barang.tanggal, 
            penerimaan_barang.no_penerimaan_barang, penerimaan_barang_detail.nama_barang_dok,
            penerimaan_barang_detail.jml_masuk AS qty_lpb, 
            penerimaan_barang_detail.id AS penerimaan_barang_detail_id, 
            penerimaan_barang_detail.harga,
            satuans.kode_satuan";

        $penerimaanList = $penerimaanBarangModel->select($selectQry)
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit')
            ->where($condition)
            ->orderBy('penerimaan_barang.tanggal', "ASC")
            ->findAll();

        $filteredResults = array_map(function ($penerimaan) use ($tandaTerimaFakturDetailModel) {
            $selectQry = "SUM(qty) AS qty_diterima";

            $tandaTerimaFaktur = $tandaTerimaFakturDetailModel->select($selectQry)
                ->where('penerimaan_barang_detail_id', $penerimaan['penerimaan_barang_detail_id'])
                ->where('deletedAt', null)
                ->findAll();

            $qtyLpb = $penerimaan['qty_lpb'];
            $qtyRetur = 0;
            $qtyTelahDiterima = empty($tandaTerimaFaktur) ? 0 : $tandaTerimaFaktur[0]['qty_diterima'];
            $qtyAkanDiterima = $qtyLpb - $qtyTelahDiterima;

            if ($qtyAkanDiterima != 0) {
                return [
                    'penerimaan_barang_detail_id' => $penerimaan['penerimaan_barang_detail_id'],
                    'po_no' => $penerimaan['po_no'],
                    'tanggal' => date('d/m/Y', strtotime($penerimaan['tanggal'])),
                    'no_penerimaan_barang' => $penerimaan['no_penerimaan_barang'],
                    'nama_barang_dok' => strtoupper($penerimaan['nama_barang_dok']),
                    'qty_lpb' => $qtyLpb,
                    'qty_retur' => $qtyRetur,
                    'qty_telah_diterima' => $qtyTelahDiterima == null ? 0 : $qtyTelahDiterima,
                    'qty_akan_diterima' => $qtyAkanDiterima,
                    'kode_satuan' => $penerimaan['kode_satuan'],
                    'harga' => $penerimaan['harga']
                ];
            }
        }, $penerimaanList);

        $filteredResults = array_filter($filteredResults);

        return $filteredResults;
    }


    public function getByID($tandaTerimaFakturID)
    {
        $res = $this->where('id', $tandaTerimaFakturID)->first();
        return $res;
    }

    public function getNo()
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();

        $month = idate('m');
        $year = date('y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "/TT/$romanMonth/$year";

        $lastData = $tandaTerimaFakturModel->asObject()
            ->like('faktur_no', $numberTemplate, 'before')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $invNumber = '001' . $numberTemplate;

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->faktur_no);
            $lastIncrement = intval($asd[0]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }

        return $invNumber;
    }
}
