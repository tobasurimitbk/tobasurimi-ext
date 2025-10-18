<?php

namespace App\Models;

use CodeIgniter\Model;
use PHPUnit\TextUI\XmlConfiguration\Group;

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
        'divisi_id',
        'multiple_divisi_id',
        'multiple_divisi_name',
        'faktur_no',
        'faktur_keluar_no',
        'jatuh_tempo',
        'total_paid',
        'nominal_faktur',
        'invoice_date',
        'receive_date',
        'potongan',
        'recipient',
        'tambahan',
        'faktur_type',
        'information_tambahan',
        'information_potongan',
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
            'tanda_terima_faktur.id'  => 'tanda_terima_faktur.id',
            'receive_date'  => 'tanda_terima_faktur.receive_date',
            'faktur_no'     => 'tanda_terima_faktur.faktur_no',
            'suppliers.name' => 'suppliers.name',
            'nominal_faktur' => 'nominal_faktur',
            'recipient'     => 'recipient',
            "divisi" => "multiple_divisi_name"
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'tanda_terima_faktur.id';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "tanda_terima_faktur.*, 
                      suppliers.name AS supplierName,
                      users.name AS userName,
                      COALESCE(SUM(local_po_payment_bp.amount), 0) AS total_dibayar,
                      COUNT(tanda_terima_faktur_detail.id) AS jumlah_item";

        $tandaTerimaQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('suppliers', 'suppliers.id = tanda_terima_faktur.supplier_id', 'left')
            ->join('users', 'users.id = tanda_terima_faktur.user_id', 'left')
            ->join('local_po_payment_bp', 'local_po_payment_bp.tanda_terima_faktur_id = tanda_terima_faktur.id', 'left')
            ->join('tanda_terima_faktur_detail', 'tanda_terima_faktur_detail.tanda_terima_faktur_id = tanda_terima_faktur.id', 'left');

        // ✅ Pencarian berdasarkan input `search`
        if (!empty($addCondition['search'])) {
            $tandaTerimaQry->groupStart()
                ->like('tanda_terima_faktur.faktur_no', $addCondition['search'])
                ->orLike('multiple_divisi_name', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->orLike('users.name', $addCondition['search'])
                ->groupEnd();
        }

        // ✅ Filter status lunas menggunakan HAVING
        if (!empty($addCondition['status_lunas'])) {
            if ($addCondition['status_lunas'] === "LUNAS") {
                $tandaTerimaQry->having('total_dibayar >= tanda_terima_faktur.nominal_faktur');
            } else {
                $tandaTerimaQry->having('total_dibayar < tanda_terima_faktur.nominal_faktur');
            }
        }

        // ✅ Filter berdasarkan tanggal
        if (!empty($addCondition['start']) || !empty($addCondition['finish'])) {
            $tandaTerimaQry->groupStart();

            if (!empty($addCondition['start'])) {
                $tandaTerimaQry->where('tanda_terima_faktur.receive_date >=', $addCondition['start']);
            }

            if (!empty($addCondition['finish'])) {
                $tandaTerimaQry->where('tanda_terima_faktur.receive_date <=', $addCondition['finish']);
            }

            $tandaTerimaQry->groupEnd();
        }

        $tandaTerimaQry->groupBy('tanda_terima_faktur.id');

        // ✅ Hitung total data
        $totalData = $tandaTerimaQry->countAllResults(false);
        $totalFilteredData = $tandaTerimaQry->countAllResults(false);
        $data = $tandaTerimaQry->orderBy($sort, $sortType)->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getListTandaTerimaFakturNotProcessed($supplier)
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $supplierModel = new SupplierModel();
        $divisiModel = new DivisisModel();

        $supplierName = $supplierModel
            ->select('name')
            ->where('id', $supplier)
            ->where('deletedAt', null)
            ->findColumn('name') ?? [];

        $idSupplierArr = $supplierModel
            ->select('id')
            ->where('name', $supplierName)
            ->where('deletedAt', null)
            ->findColumn('id') ?? [];

        $res = $tandaTerimaFakturModel
            ->whereIn('supplier_id', $idSupplierArr)
            ->where('deletedAt', null)
            ->select('id, faktur_no')
            ->findAll();

        return $res;
    }

    public function getTandaTerimaFakturInPembayaran($tandaTerimaFakturID)
    {
        $condition = [
            'local_po_payment_bp.tanda_terima_faktur_id' => $tandaTerimaFakturID,
            'local_po_payment_bp.deletedAt' => null
        ];
        $localPoPaymentBPModel = new LocalPOPaymentBPModel();
        $res = $localPoPaymentBPModel
            ->where($condition)
            ->first();


        return $res;
    }

    public function getAllTandaTerimaFakturInPembayaran($tandaTerimaFakturID)
    {
        $condition = [
            'local_po_payment_bp.tanda_terima_faktur_id' => $tandaTerimaFakturID,
            'local_po_payment_bp.deletedAt' => null
        ];
        $localPoPaymentBPModel = new LocalPOPaymentBPModel();
        $res = $localPoPaymentBPModel
            ->where($condition)
            ->findAll();


        return $res;
    }

    public function getListPenerimaanBarangLokalBPNotProcessed(
        $supplierID,
        $divisiIds,
        $companyID
    ) {
        // Tampilkan diatas bulan 01-09-2025
        $condition = [
            'penerimaan_barang.status_post' => 'FINISH',
            'penerimaan_barang.company_id' => $companyID,
            'penerimaan_barang.tipe_bahan' => 'PENOLONG',
            'penerimaan_barang.status_penerimaan' => 'LOKAL',
            'penerimaan_barang.deletedAt' => null,
            'penerimaan_barang_detail.deletedAt' => null,
            'penerimaan_barang_detail.jml_masuk !=' => 0,
            'penerimaan_barang.tanggal >=' => "2025-09-01"
        ];

        // Tambahkan filter jika $supplierID bukan "all"
        if ($supplierID !== 'all') {
            $condition['penerimaan_barang.supplier_id'] = $supplierID;
        }

        $penerimaanBarangModel = new PenerimaanBarangModel();
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();

        $selectQry = "am_purchase_orders.po_no, penerimaan_barang.tanggal, 
            penerimaan_barang.no_penerimaan_barang, penerimaan_barang_detail.nama_barang_dok,
            penerimaan_barang_detail.jml_masuk AS qty_lpb, 
            penerimaan_barang_detail.id AS penerimaan_barang_detail_id, 
            penerimaan_barang_detail.harga,
            penerimaan_barang_detail.sub_total as harga_total,
            suppliers.id as supplier_id, 
            suppliers.name as supplier_name,
            divisis.id as divisi_id, 
            divisis.divisi as divisi_name,
            satuans.kode_satuan,
            CONCAT(barang_master.barang_name, ' - ', barang_master_spesifikasi.spesifikasi) AS barang
        ";

        $dataQry = $penerimaanBarangModel->select($selectQry)
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
            ->join('am_purchase_orders', 'am_purchase_orders.id = penerimaan_barang_detail.purchase_order_id')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit')
            ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id', 'left')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_detail.spesifikasi_id', 'left')
            ->where($condition);

        if (!in_array("all", $divisiIds)) {
            // Jika bukan all tampilkan wherein nya 
            $dataQry->whereIn('penerimaan_barang.divisi_id', $divisiIds);
        }

        $penerimaanList = $dataQry->orderBy('penerimaan_barang.tanggal', "ASC")->findAll();

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
                    'nama_barang_dok' => strtoupper($penerimaan['barang']),
                    'qty_lpb' => $qtyLpb,
                    'qty_retur' => $qtyRetur,
                    'qty_telah_diterima' => $qtyTelahDiterima == null ? 0 : $qtyTelahDiterima,
                    'qty_akan_diterima' => $qtyAkanDiterima,
                    'kode_satuan' => $penerimaan['kode_satuan'],
                    'harga' => $penerimaan['harga'],
                    'harga_total' => $penerimaan['harga_total'],
                    'supplier_id' => $penerimaan['supplier_id'],
                    'supplier_name' => $penerimaan['supplier_name'],
                    'divisi_id' => $penerimaan['divisi_id'],
                    'divisi_name' => $penerimaan['divisi_name']
                ];
            }
        }, $penerimaanList);

        $filteredResults = array_filter($filteredResults);

        return $filteredResults;
    }



    public function getByID($tandaTerimaFakturID)
    {   
      $res = $this->select("
            tanda_terima_faktur.*,
            GROUP_CONCAT(DISTINCT tanda_terima_faktur_detail.lpb_no ORDER BY tanda_terima_faktur_detail.lpb_no SEPARATOR ', ') AS list_lpb,
            (tanda_terima_faktur.nominal_faktur - COALESCE(tanda_terima_faktur.total_paid, 0)) AS sisa_bayar
        ")
        ->join('tanda_terima_faktur_detail', 'tanda_terima_faktur_detail.tanda_terima_faktur_id = tanda_terima_faktur.id', 'left')
        ->where('tanda_terima_faktur.id', $tandaTerimaFakturID)
        ->where('tanda_terima_faktur.deletedAt', null)
        ->groupBy('tanda_terima_faktur.id')
        ->first();

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
            ->where('company_id', session()->get("login")->this_company_id)
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


    public function getNoKeluar()
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();

        $month = idate('m');
        $year = date('y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "/TT/$romanMonth/$year";

        $lastData = $tandaTerimaFakturModel->asObject()
            ->where('company_id', session()->get("login")->this_company_id)
            ->like('faktur_keluar_no', $numberTemplate, 'before')
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
