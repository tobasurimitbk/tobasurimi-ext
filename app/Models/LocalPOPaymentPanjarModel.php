<?php

namespace App\Models;

use App\Models\PanjarSupplierModel;
use CodeIgniter\Model;
use PHPUnit\TextUI\XmlConfiguration\Group;

class LocalPOPaymentPanjarModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'local_po_payment_panjar';
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
        'panjar_id',
        'bayar_panjar',
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

    public function getPembayaranPanjarDetails($id)
    {
        $condition = [
            'local_po_payments.id ' => $id,

        ];
        $selectQry = "no_panjar, panjar_supplier.payment_date, total_panjar, bayar_panjar, panjar_supplier.id as panjar_id";

        $list = $this->asObject()
            ->select($selectQry)
            ->join('local_po_payments', 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id', 'inner')
            ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id', 'inner')
            ->where($condition)
            ->findAll();



        return $list;
    }

    public function getTotalPembayaranPanjar($panjarId, $type)
    {
        $condition = [
            'deletedAt' => null,
            'panjar_id' => $panjarId,
            'type'  => $type
        ];
        $selectQry = "sum(bayar_panjar) as total_bayar_panjar";
        $totalBayarPanjar = $this
            ->select($selectQry)
            ->where($condition)
            ->first();

        return $totalBayarPanjar;
    }

    public function getPembayaranPanjarDetailsbyPanjarId($id)
    {
        $condition = [
            'panjar_id' => $id
        ];
        $panjarType = $this->where('panjar_id', $id)->first();
        $list = [];

        if (!empty($panjarType)) {
            if ($panjarType['type'] == "BB") {
                $selectQry = "no_panjar,total_panjar, bayar_panjar, panjar_supplier.supplier_id, panjar_supplier.jenis_panjar, name, local_po_payments.payment_date, rm_purchase_orders.po_no";
                $list = $this->asObject()
                    ->select($selectQry)
                    ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id', 'inner')
                    ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'inner')
                    ->join('local_po_payments', 'local_po_payment_panjar.local_po_payment_id = local_po_payments.id', 'inner')
                    ->join('local_po_payment_details', 'local_po_payments.id = local_po_payment_details.local_po_payment_id', 'left')
                    ->join('rm_purchase_orders', 'local_po_payment_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
                    ->where($condition)
                    ->findAll();
                foreach ($list as $l) {
                    $l->total_panjar = number_format($l->total_panjar, 2);
                    $l->bayar_panjar = number_format($l->bayar_panjar, 2);
                    $l->payment_date = date('d/m/Y', strtotime($l->payment_date));
                }
            } elseif ($panjarType['type'] == "BP") {
                $selectQry = "no_panjar,total_panjar, bayar_panjar, panjar_supplier.supplier_id, faktur_no, name, local_po_payment_bp.payment_date";
                $list = $this->asObject()
                    ->select($selectQry)
                    ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id', 'inner')
                    ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'inner')
                    ->join('local_po_payment_bp', 'local_po_payment_panjar.local_po_payment_id = local_po_payment_bp.id', 'inner')
                    ->join('tanda_terima_faktur', 'local_po_payment_bp.tanda_terima_faktur_id = tanda_terima_faktur.id')
                    ->where($condition)
                    ->findAll();
                foreach ($list as $l) {
                    $l->multiple_lpb_no = $l->faktur_no;
                    $l->total_panjar = number_format($l->total_panjar, 2);
                    $l->bayar_panjar = number_format($l->bayar_panjar, 2);
                    $l->payment_date = date('d/m/Y', strtotime($l->payment_date));
                }
            } elseif ($panjarType['type'] == "INTERNASIONAL") {
                $selectQry = "no_panjar,total_panjar, bayar_panjar, panjar_supplier.supplier_id, po_no, name, import_po_payments.payment_date";
                $list = $this->asObject()
                    ->select($selectQry)
                    ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
                    ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id')
                    ->join('import_po_payments', 'local_po_payment_panjar.local_po_payment_id = import_po_payments.id')
                    ->join('rm_import_pos', 'import_po_payments.po_id = rm_import_pos.id')
                    ->where($condition)
                    ->findAll();

                foreach ($list as $l) {
                    $l->multiple_lpb_no = $l->po_no;
                    $l->total_panjar = number_format($l->total_panjar, 2);
                    $l->bayar_panjar = number_format($l->bayar_panjar, 2);
                    $l->payment_date = date('d/m/Y', strtotime($l->payment_date));
                }
            }
        }



        return $list;
    }

    public function getPembayaranPanjarDetailsbyIdandType($id = null, $type, $supllierId)
    {
        $dataPanjar = []; // Inisialisasi dengan nilai default
        $paidData = [];   // Variabel baru untuk menyimpan data yang sudah terbayar

        if ($id) {
            $condition = [
                'local_po_payment_panjar.local_po_payment_id' => $id,
                'local_po_payment_panjar.type'  => $type,
                'panjar_supplier.jenis_panjar'  => "PANJAR",
            ];

            $selectQry = "local_po_payment_panjar.id, panjar_supplier.no_panjar, panjar_supplier.payment_date, panjar_supplier.total_panjar, local_po_payment_panjar.panjar_id,
            local_po_payment_panjar.bayar_panjar, sum(local_po_payment_panjar.bayar_panjar) as total_bayar_panjar, local_po_payment_panjar.akun_kas, local_po_payment_panjar.akun_selisih, local_po_payment_panjar.keterangan,
            akun_kas.nama_sub as akun_kas_name,  akun_selisih.nama_sub as akun_selisih_name";

            $resultPay = $this
                ->select($selectQry)
                ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
                ->join('sub_akuns as akun_kas', 'local_po_payment_panjar.akun_kas = akun_kas.id', 'left')
                ->join('sub_akuns as akun_selisih', 'local_po_payment_panjar.akun_selisih = akun_selisih.id', 'left')
                ->groupBy('local_po_payment_panjar.panjar_id')
                ->where('panjar_supplier.jenis_panjar', "PANJAR")
                ->where($condition)
                ->findAll();
        } else {
            // Jika $id null, ambil data hanya dari panjar_supplier tanpa join
            $panjarSupplier = new PanjarSupplierModel();

            $result = $panjarSupplier
                ->select("id, no_panjar, payment_date, total_panjar, jenis_panjar")
                ->where('jenis_panjar', "PANJAR")
                ->where('supplier_id', $supllierId)
                ->findAll();
        }

        // Jika hasil query kosong, ambil data alternatif
        if (empty($result)) {
            $panjarSupplier = new PanjarSupplierModel();

            $dataPanjar = $panjarSupplier->select("id, no_panjar, payment_date, total_panjar, jenis_panjar")
                                        ->where('jenis_panjar', "PANJAR_TB")
                                        ->where('supplier_id', $supllierId)
                                        ->findAll();
        }

        // Jika $dataPanjar tidak kosong, gunakan $dataPanjar sebagai hasil
        if (!empty($dataPanjar)) {
            $result = $dataPanjar;
        }

        // Proses hasil query
        if (!empty($resultPay)) {
            foreach ($result as $i => $r) {
                $totalPembayaranPanjar = $this->getTotalPembayaranPanjar($r['panjar_id'], "BP");

                // Simpan data yang sudah terbayar ke variabel $paidData
                $paidData[$i] = [
                    'payment_date' => date('d/m/Y', strtotime($r['payment_date'])),
                    'akun_kas' => $r['akun_kas'],
                    'akun_selisih' => $r['akun_selisih'],
                    'akun_kas_name' => $r['akun_kas_name'],
                    'akun_selisih_name' => $r['akun_selisih_name'],
                    'keterangan' => $r['keterangan'],
                    'total_pembayaran' => $totalPembayaranPanjar,
                ];

                // Gabungkan $paidData ke $result
                $result[$i] = array_merge($r, $paidData[$i]);
            }
        }

        return $result;
    }


    public function getPembayaranPanjarTBDetailsbyIdandType($id = null, $type, $supllierId)
    {
        // Jika $id ada, lakukan query dengan join ke tabel terkait
        if ($id) {
            $condition = [
                'local_po_payment_panjar.local_po_payment_id' => $id,
                'local_po_payment_panjar.type'  => $type,
                'panjar_supplier.jenis_panjar'  => "PANJAR_TB",
            ];

            $selectQry = "local_po_payment_panjar.id, panjar_supplier.no_panjar, panjar_supplier.payment_date, panjar_supplier.total_panjar, local_po_payment_panjar.panjar_id,
            local_po_payment_panjar.bayar_panjar, sum(local_po_payment_panjar.bayar_panjar) as total_bayar_panjar, local_po_payment_panjar.akun_kas, local_po_payment_panjar.akun_selisih, local_po_payment_panjar.keterangan,
            akun_kas.nama_sub as akun_kas_name,  akun_selisih.nama_sub as akun_selisih_name";

            $result = $this
                ->select($selectQry)
                ->join('panjar_supplier', 'local_po_payment_panjar.panjar_id = panjar_supplier.id')
                ->join('sub_akuns as akun_kas', 'local_po_payment_panjar.akun_kas = akun_kas.id', 'left')
                ->join('sub_akuns as akun_selisih', 'local_po_payment_panjar.akun_selisih = akun_selisih.id', 'left')
                ->groupBy('local_po_payment_panjar.panjar_id')
                ->where('panjar_supplier.jenis_panjar', "PANJAR_TB")
                ->where($condition)
                ->findAll();
        } else {
            // Jika $id null, ambil data hanya dari panjar_supplier tanpa join
            $panjarSupplier = new PanjarSupplierModel();

            $result = $panjarSupplier
                ->select("id, no_panjar, payment_date, total_panjar, jenis_panjar")
                ->where('jenis_panjar', "PANJAR_TB")
                ->where('supplier_id', $supllierId)
                ->findAll();
        }

        // Jika hasil query kosong, ambil data alternatif
        if (empty($result)) {
            $panjarSupplier = new PanjarSupplierModel();

            $dataPanjar = $panjarSupplier->select("id, no_panjar, payment_date, total_panjar, jenis_panjar")
                                        ->where('jenis_panjar', "PANJAR_TB")
                                        ->where('supplier_id', $supllierId)
                                        ->findAll();
        }

        if (empty($dataPanjar)) {
            foreach ($result as $i => $r) {
                $totalPembayaranPanjar = $this->getTotalPembayaranPanjar($r['panjar_id'], "BP");
                $result[$i]['payment_date'] = date('d/m/Y', strtotime($r['payment_date']));
                $result[$i]['akun_kas'] = $r['akun_kas'];
                $result[$i]['akun_selisih'] = $r['akun_selisih'];
                $result[$i]['akun_kas_name'] = $r['akun_kas_name'];
                $result[$i]['akun_selisih_name'] = $r['akun_selisih_name'];
                $result[$i]['keterangan'] = $r['keterangan'];
                $result[$i]['total_pembayaran'] = $totalPembayaranPanjar;
            }
        } else {
            $result = $dataPanjar;
        }

        return $result;
    } 
}
