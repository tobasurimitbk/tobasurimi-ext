<?php

namespace App\Models;

use CodeIgniter\Model;
use PHPUnit\TextUI\XmlConfiguration\Group;

class LocalPOPaymentPinjamanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_payment_pinjaman';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'company_id',
        'local_po_payment_id',
        'type',
        'pinjaman_id',
        'bayar_pinjaman',
        'akun_kas',
        'akun_selisih',
        'keterangan',
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

    public function getPembayaranPinjamanDetails($id)
    {
        $condition = [
            'local_po_payments.id ' => $id,

        ];
        $selectQry = "no_pinjaman, pinjaman_supplier.payment_date, total_pinjaman, bayar_pinjaman, pinjaman_supplier.id as pinjaman_id";

        $list = $this->asObject()
            ->select($selectQry)
            ->join('local_po_payments', 'local_po_payment_pinjaman.local_po_payment_id = local_po_payments.id', 'inner')
            ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id', 'inner')
            ->where($condition)
            ->findAll();



        return $list;
    }

    public function getTotalPembayaranPinjaman($pinjamanId, $type)
    {
        $condition = [
            'deletedAt' => null,
            'pinjaman_id' => $pinjamanId,
            'type'  => $type
        ];
        $selectQry = "sum(bayar_pinjaman) as total_bayar_pinjaman";
        $totalBayarPinjaman = $this
            ->select($selectQry)
            ->where($condition)
            ->first();

        return $totalBayarPinjaman;
    }

    public function getPembayaranPinjamanDetailsbyPinjamanId($id)
    {
        $condition = [
            'pinjaman_id' => $id
        ];
        $pinjamanType = $this->where('pinjaman_id', $id)->first();
        $list = [];

        if (!empty($pinjamanType)) {
            if ($pinjamanType['type'] == "BB") {
                $selectQry = "no_pinjaman,total_pinjaman, bayar_pinjaman, pinjaman_supplier.supplier_id, multiple_lpb_no, name, local_po_payments.payment_date";
                $list = $this->asObject()
                    ->select($selectQry)
                    ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id', 'inner')
                    ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'inner')
                    ->join('local_po_payments', 'local_po_payment_pinjaman.local_po_payment_id = local_po_payments.id', 'inner')
                    ->where($condition)
                    ->findAll();
                foreach ($list as $l) {
                    $l->multiple_lpb_no = json_decode($l->multiple_lpb_no);
                    $l->total_pinjaman = number_format($l->total_pinjaman, 2);
                    $l->bayar_pinjaman = number_format($l->bayar_pinjaman, 2);
                    $l->payment_date = date('d/m/Y', strtotime($l->payment_date));
                }
            } elseif ($pinjamanType['type'] == "BP") {
                $selectQry = "no_pinjaman,total_pinjaman, bayar_pinjaman, pinjaman_supplier.supplier_id, faktur_no, name, local_po_payment_bp.payment_date";
                $list = $this->asObject()
                    ->select($selectQry)
                    ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id', 'inner')
                    ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'inner')
                    ->join('local_po_payment_bp', 'local_po_payment_pinjaman.local_po_payment_id = local_po_payment_bp.id', 'inner')
                    ->join('tanda_terima_faktur', 'local_po_payment_bp.tanda_terima_faktur_id = tanda_terima_faktur.id')
                    ->where($condition)
                    ->findAll();
                foreach ($list as $l) {
                    $l->multiple_lpb_no = $l->faktur_no;
                    $l->total_pinjaman = number_format($l->total_pinjaman, 2);
                    $l->bayar_pinjaman = number_format($l->bayar_pinjaman, 2);
                    $l->payment_date = date('d/m/Y', strtotime($l->payment_date));
                }
            } elseif ($pinjamanType['type'] == "INTERNASIONAL") {
                $selectQry = "no_pinjaman,total_pinjaman, bayar_pinjaman, pinjaman_supplier.supplier_id, po_no, name, import_po_payments.payment_date";
                $list = $this->asObject()
                    ->select($selectQry)
                    ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id')
                    ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id')
                    ->join('import_po_payments', 'local_po_payment_pinjaman.local_po_payment_id = import_po_payments.id')
                    ->join('rm_import_pos', 'import_po_payments.po_id = rm_import_pos.id')
                    ->where($condition)
                    ->findAll();

                foreach ($list as $l) {
                    $l->multiple_lpb_no = $l->po_no;
                    $l->total_pinjaman = number_format($l->total_pinjaman, 2);
                    $l->bayar_pinjaman = number_format($l->bayar_pinjaman, 2);
                    $l->payment_date = date('d/m/Y', strtotime($l->payment_date));
                }
            }
        }



        return $list;
    }

    public function getPembayaranPinjamanDetailsbyIdandType($id, $type)
    {
        $condition = [
            'local_po_payment_pinjaman.local_po_payment_id' => $id,
            'type'  => $type
        ];

        $selectQry = "local_po_payment_pinjaman.id, pinjaman_supplier.no_pinjaman, pinjaman_supplier.payment_date, pinjaman_supplier.total_pinjaman, local_po_payment_pinjaman.pinjaman_id,
        local_po_payment_pinjaman.bayar_pinjaman, sum(local_po_payment_pinjaman.bayar_pinjaman) as total_bayar_pinjaman";

        $result = $this
            ->select($selectQry)
            ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id')
            ->join('local_po_payment_bp', 'local_po_payment_bp.id = local_po_payment_pinjaman.local_po_payment_id')
            ->groupBy('local_po_payment_pinjaman.pinjaman_id')
            ->where($condition)
            ->findAll();

            if (empty($result)) {
                $pinjamanSupplier = new PinjamanSupplierModel();
    
                $dataPinjaman = $pinjamanSupplier->select("local_po_payment_pinjaman.id, pinjaman_supplier.no_pinjaman, pinjaman_supplier.payment_date, pinjaman_supplier.total_pinjaman, local_po_payment_pinjaman.pinjaman_id,
                local_po_payment_pinjaman.bayar_pinjaman, sum(local_po_payment_pinjaman.bayar_pinjaman) as total_bayar_pinjaman")
                            ->where("pinjaman_supplier.type_pinjaman", "BP")
                            ->join('local_po_payment_pinjaman', 'local_po_payment_pinjaman.id = pinjaman_supplier.id', 'left')
                            ->findAll();
                
                $result = $dataPinjaman;
            }

        foreach ($result as $i => $r) {
            $totalPembayaranPinjaman = $this->getTotalPembayaranPinjaman($r['pinjaman_id'], "BP");
            $result[$i]['payment_date'] = date('d/m/Y', strtotime($r['payment_date']));


            $result[$i]['total_pembayaran'] = $totalPembayaranPinjaman;
        }



        return $result;
    }
}
