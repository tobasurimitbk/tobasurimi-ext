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

    public function getTotalPembayaranPinjaman($pinjamanId)
    {
        $condition = [
            'deletedAt' => null,
            'pinjaman_id' => $pinjamanId,
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

    public function getPembayaranPinjamanDetailsbyIdandType($id = null, $type, $supllierId)
    {
        $dataPinjaman = []; // Inisialisasi dengan nilai default
        $paidData = [];   // Variabel baru untuk menyimpan data yang sudah terbayar

        if ($id) {
            $condition = [
                'local_po_payment_pinjaman.local_po_payment_id' => $id,
                'local_po_payment_pinjaman.type'  => $type,
            ];

            $selectQry = "local_po_payment_pinjaman.id, pinjaman_supplier.no_pinjaman, pinjaman_supplier.payment_date, pinjaman_supplier.total_pinjaman, local_po_payment_pinjaman.pinjaman_id,
            local_po_payment_pinjaman.bayar_pinjaman, sum(local_po_payment_pinjaman.bayar_pinjaman) as total_bayar_pinjaman, local_po_payment_pinjaman.akun_kas, local_po_payment_pinjaman.akun_selisih, local_po_payment_pinjaman.keterangan,
            akun_kas.nama_sub as akun_kas_name,  akun_selisih.nama_sub as akun_selisih_name";

            $resultPay = $this
                ->select($selectQry)
                ->join('pinjaman_supplier', 'local_po_payment_pinjaman.pinjaman_id = pinjaman_supplier.id')
                ->join('sub_akuns as akun_kas', 'local_po_payment_pinjaman.akun_kas = akun_kas.id', 'left')
                ->join('sub_akuns as akun_selisih', 'local_po_payment_pinjaman.akun_selisih = akun_selisih.id', 'left')
                ->groupBy('local_po_payment_pinjaman.pinjaman_id')
                ->where($condition)
                ->findAll();
        } else {
            // Jika $id null, ambil data hanya dari pinjaman_supplier tanpa join
            $pinjamanSupplier = new PinjamanSupplierModel();

            $result = $pinjamanSupplier
                ->select("id, no_pinjaman, payment_date, total_pinjaman")
                ->where('supplier_id', $supllierId)
                ->findAll();
        }

        // Jika hasil query kosong, ambil data alternatif
        if (empty($result)) {
            $pinjamanSupplier = new PinjamanSupplierModel();

            $dataPinjaman = $pinjamanSupplier->select("id, no_pinjaman, payment_date, total_pinjaman")
                                        ->where('supplier_id', $supllierId)
                                        ->findAll();
        }

        // Jika $dataPinjaman tidak kosong, gunakan $dataPinjaman sebagai hasil
        if (!empty($dataPinjaman)) {
            $result = $dataPinjaman;
        }

        if (!empty($resultPay)) {
            foreach ($resultPay as $i => $r) {
                $totalPembayaranPinjaman = $this->getTotalPembayaranPinjaman($r['pinjaman_id'], "BP");

                // Simpan data yang sudah terbayar ke variabel $paidData
                $paidData[$i] = [
                    'payment_date' => date('d/m/Y', strtotime($r['payment_date'])),
                    'akun_kas' => $r['akun_kas'],
                    'akun_selisih' => $r['akun_selisih'],
                    'akun_kas_name' => $r['akun_kas_name'],
                    'akun_selisih_name' => $r['akun_selisih_name'],
                    'keterangan' => $r['keterangan'],
                    'total_pembayaran' => $totalPembayaranPinjaman,
                ];

                // Gabungkan $paidData ke $result
                $result[$i] = array_merge($r, $paidData[$i]);
            }
        }

        return $result;
    }
}
