<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'suppliers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'kode',
        'name',
        'address',
        'no_npwp',
        'phone',
        'fax',
        'type',
        'contact_person',
        'province_id',
        'city_id',
        'postal_code',
        'email',
        'country_code',
        'account_receivable',
        'account_payable',
        'user_id'
        // 'no_rekening',
        // 'supplier_buyer',
        //'kategori',
        // 'ap_id',
        // 'ar_id',
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
    protected $supplierModel;

    public function getSupplierList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'kode'              => 'suppliers.kode',
            'name'              => 'suppliers.name',
            'address'           => 'suppliers.address',
            'no_npwp'           => 'suppliers.no_npwp',
            'phone'             => 'suppliers.phone',
            'contact_person'    => 'suppliers.contact_person',
            'fax'               => 'suppliers.fax',
            'createdAt'         => 'suppliers.createdAt',
            'updatedAt'         => 'suppliers.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "suppliers.*";

        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->orderBy($sort, $sortType);

        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search']) {
            $supplierDataQry->groupStart()
                ->like('name', $addCondition['search'])
                ->orLike('kode', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSupplierHutangList($condition, $addCondition, $limit = 10, $offset = 0, $companyId)
    {
        $dataFinal = [];
        $dateStart = $addCondition['startdate'] ?? null;
        $dateEnd   = $addCondition['lastdate'] ?? null;
        $this->supplierModel = new SupplierModel();

        // Helper for reusability
        $buildQuery = function ($type, $poTable, $poAlias, $paymentTable, $tipeBahan) use ($condition, $addCondition, $companyId, $dateStart, $dateEnd) {
            return $this->supplierModel->asObject()
                ->select("suppliers.id, suppliers.name,
                SUM(penerimaan_barang_detail.sub_total) AS total,
                SUM($paymentTable.amount) AS remaining,
                GROUP_CONCAT(DISTINCT penerimaan_barang.no_penerimaan_barang) AS no_penerimaan_barang")
                ->join("$poTable", "$poTable.supplier_id = suppliers.id AND $poTable.is_posted = 1", 'left')
                ->join("$paymentTable", "FIND_IN_SET($poTable.id, REPLACE(REPLACE($paymentTable.multiple_po_id, '[', ''), ']', ''))", 'left')
                ->join('penerimaan_barang_detail', "penerimaan_barang_detail.purchase_order_id = $poTable.id", 'left')
                ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
                ->where('suppliers.type', $type)
                ->where('penerimaan_barang.tipe_bahan', $tipeBahan)
                ->where($condition)
                ->whereIn('suppliers.company_id', $companyId)
                ->when(!empty($addCondition['filter']), fn($q) => $q->whereIn('suppliers.id', $addCondition['filter']))
                ->when(!empty($addCondition['divisi']), fn($q) => $q->where("$poTable.divisi_id", $addCondition['divisi']))
                ->when($dateStart, fn($q) => $q->where("$poTable.po_date >=", $dateStart))
                ->when($dateEnd, fn($q) => $q->where("$poTable.po_date <=", $dateEnd))
                ->groupBy('suppliers.id');
        };

        // Bahan Baku
        $dataBB = $buildQuery('BAHAN BAKU', 'rm_purchase_orders', 'rm', 'local_po_payments', 'BAKU')->findAll();
        $dataFinal = array_merge($dataFinal, $dataBB);

        // Bahan Penolong
        $dataBP = $buildQuery('BAHAN PENOLONG', 'am_purchase_orders', 'am', 'local_po_payments', 'PENOLONG')->findAll();
        $dataFinal = array_merge($dataFinal, $dataBP);

        // Internasional
        $dataIMP = $this->supplierModel->asObject()
            ->select("suppliers.id, suppliers.name,
            SUM(penerimaan_barang_detail.sub_total) AS total,
            SUM(import_po_payments.payment_amt * import_po_payments.current_exchange_rate) AS remaining,
            GROUP_CONCAT(DISTINCT penerimaan_barang.no_penerimaan_barang) AS no_penerimaan_barang")
            ->join('rm_import_pos', 'rm_import_pos.supplier_id = suppliers.id AND rm_import_pos.is_posted = 1', 'left')
            ->join('import_po_payments', 'import_po_payments.supplier_id = suppliers.id AND import_po_payments.status_posting = 1', 'left')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.purchase_order_id = rm_import_pos.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
            ->where('suppliers.type', 'INTERNASIONAL')
            ->where('penerimaan_barang.tipe_bahan', 'INTERNASIONAL')
            ->where($condition)
            ->whereIn('suppliers.company_id', $companyId)
            ->when(!empty($addCondition['filter']), fn($q) => $q->whereIn('suppliers.id', $addCondition['filter']))
            ->when(!empty($addCondition['divisi']), fn($q) => $q->where('rm_import_pos.division_id', $addCondition['divisi']))
            ->when($dateStart, fn($q) => $q->where('rm_import_pos.po_date >=', $dateStart))
            ->when($dateEnd, fn($q) => $q->where('rm_import_pos.po_date <=', $dateEnd))
            ->groupBy('suppliers.id')
            ->findAll();

        $dataFinal = array_merge($dataFinal, $dataIMP);

        $filtered = [];
        foreach ($dataFinal as $item) {
            if (floatval($item->total) > 0) {
                $filtered[] = [
                    'id' => $item->id,
                    'supplier' => $item->name,
                    'no_penerimaan_barang' => $item->no_penerimaan_barang ?? '-',
                    'nominal_idr' => number_format($item->total, 2, '.', ''),
                    'remaining_idr' => number_format($item->total - $item->remaining, 2, '.', ''),
                ];
            }
        }

        $totalData = count($filtered);
        $paged = array_slice($filtered, $offset, $limit);

        return [
            'data' => $paged,
            'totalData' => $totalData,
            'totalFilteredData' => $totalData
        ];
    }

    public function getSupplierById($id)
    {
        $supplierData = $this->asObject()
            ->select('suppliers.*')
            // ->select('suppliers.*, country.country_name')
            // ->select('suppliers.*, ap.nama_sub AS ap_name, ar.nama_sub AS ar_name, country.country_name')
            // ->join('country', 'country.code = suppliers.country_code', 'left')
            // ->join('sub_akuns AS ap', 'ap.id = suppliers.ap_id', 'left')
            // ->join('sub_akuns AS ar', 'ar.id = suppliers.ar_id', 'left')
            ->find($id);

        return $supplierData;
    }


    public function getSupplierByType($type)
    {
        $arrCondition = [
            'deletedAt' => null,
            'company_id' => session()->get('login')->this_company_id,
            'type' => $type
        ];

        $builder = $this->db->table('suppliers');
        $builder->where($arrCondition);
        $builder->orderBy('suppliers.name', "ASC");
        $query = $builder->get();
        $results = $query->getResultArray();
        foreach ($results as &$result) {
            $result['name'] = strtoupper($result['name']);
        }
        return $results;
    }

    public function generateSupplierCode($type, $company_id): string
    {
        // Template untuk kode berdasarkan tipe
        $month = idate('m');
        $year = date('y');
        $romanMonth = romanMonthNumber($month); // Fungsi romanMonthNumber harus terdefinisi sebelumnya

        // Format khusus untuk BP
        if ($type === "BP") {
            $numberTemplate = "SL-BP";
        } else {
            $numberTemplate = $type;
        }

        // Ambil data terakhir berdasarkan template dan perusahaan
        $lastData = $this->asObject()
            ->where('company_id', $company_id)
            ->like('kode', $numberTemplate . '%')
            ->orderBy('kode', 'DESC')
            ->first();

        // Jika ada data terakhir, ambil angka increment terakhir, tambahkan 1
        if (!empty($lastData)) {
            $asd = explode($type === "BP" ? 'SL-BP' : '-', $lastData->kode);
            $lastIncrement = intval(end($asd)) + 1; // Ambil elemen terakhir sebagai angka
        } else {
            $lastIncrement = 1; // Jika tidak ada data terakhir, mulai dari 1
        }

        // Format angka dengan 3 digit (contoh: 001, 002, ...)
        $paddedNumber = str_pad($lastIncrement, 3, '0', STR_PAD_LEFT);

        // Gabungkan template dengan angka yang sudah diproses
        if ($type === "BP") {
            $invNumber = $numberTemplate . $paddedNumber;
        } else {
            $invNumber = $numberTemplate . '-' . $paddedNumber;
        }

        return $invNumber;
    }


    public function getKwitansiTB($supplierID, $year, $month)
    {
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $supplierModel = new SupplierModel();

        $supplierDet = $supplierModel->where('id', $supplierID)->first();

        $condition = [
            'MONTH(rm_purchase_orders.po_date)' => $month,
            'YEAR(rm_purchase_orders.po_date)' => $year,
            'rm_purchase_orders.supplier_id' => $supplierID,
            'rm_purchase_orders.is_posted' => 1,
            // 'rm_purchase_orders.status_penerimaan' => 1,
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
        ];

        $res = [];
        $selectQry = "
            rm_purchase_order_details.monthly_price,
            barang_master.id AS barang_id,
            satuans.kode_satuan AS kode_satuan,
            rm_purchase_orders.id AS id,
            barang_master.barang_name,
            rm_purchase_orders.supplier_id,
            rm_purchase_orders.pph,
            rm_purchase_order_details.qty_diterima AS qty
        ";

        $allPo = $rmPurchaseOrderModel
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id  = rm_purchase_orders.barang_id')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id')
            ->join('satuans', 'satuans.id = barang_master.satuan_id')
            ->where($condition)
            ->groupBy('rm_purchase_orders.id')
            ->findAll();

        $hargaBulananTotal = 0;
        $hargaBulananWithQtyTotal = 0;
        $hargaBulananWithQtyPphTotal = 0;
        $barangName = "";
        $qtyTotal = 0;
        $satuan = "";

        foreach ($allPo as $ap) {
            if ($ap['pph'] == "None") {
                $pph = 0;
            } else {
                if ($supplierDet['no_npwp'] != "") {
                    // ada npwp
                    $pph = $ap['monthly_price'] * 0.0025;
                } else {
                    // tidak ada npwp
                    $pph = $ap['monthly_price'] * 0.005;
                }
            }

            $barangName = $ap['barang_name'];
            $satuan = $ap['kode_satuan'];

            $hargaBulanan = ($ap['monthly_price'] * $ap['qty']) + $pph;
            $hargaBulananWithQty = $pph;
            $hargaBulananWithQtyPph =  $hargaBulanan - $hargaBulananWithQty;

            $qtyTotal += $ap['qty'];
            $hargaBulananTotal += $hargaBulanan;
            $hargaBulananWithQtyTotal += $hargaBulananWithQty;
            $hargaBulananWithQtyPphTotal += $hargaBulananWithQtyPph;

            $res[] = [
                'id' => $ap['id'],
                'barang' => $ap['barang_name'],
                'barang_id' => $ap['barang_id'],
                'satuan' => $ap['kode_satuan'],
                'pph' => $pph,
                'hargaBulanan' => $hargaBulanan,
                'hargaBulananWithQty' => $hargaBulananWithQty,
                'hargaBulananWithQtyPph' => $hargaBulananWithQtyPph
            ];
        }

        return [
            'supplierID' => $supplierDet['id'],
            'supplierName' => $supplierDet['name'],
            'hargaBulananTotal' => $hargaBulananTotal,
            'hargaBulananWithQtyTotal' => $hargaBulananWithQtyTotal,
            'hargaBulananWithQtyPphTotal' => $hargaBulananWithQtyPphTotal,
            'barangName' => $barangName,
            'qtyTotal' => $qtyTotal,
            'satuan' => $satuan,
            'detailHarga' => $res,
            'allPO' => $allPo,
        ];
    }

    public function getKwitansiTBBySupplier($supplierID, $year, $month)
    {
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $supplierModel = new SupplierModel();

        $supplierDet = $supplierModel->where('id', $supplierID)->first();

        $condition = [
            'MONTH(rm_purchase_orders.po_date)' => $month,
            'YEAR(rm_purchase_orders.po_date)' => $year,
            'rm_purchase_orders.supplier_id' => $supplierID,
            'rm_purchase_orders.is_posted' => 1,
            // 'rm_purchase_orders.status_penerimaan' => 1,
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
        ];

        $selectQry = "
            rm_purchase_orders.pph,
            rm_purchase_orders.po_date,
            rm_purchase_order_details.*,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            SUM(qty) AS qty_total,
            satuans.kode_satuan
        ";

        $res = $rmPurchaseOrderModel
            ->asObject()
            ->select($selectQry)
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->join('barang_master', 'barang_master.id = rm_purchase_order_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_purchase_order_details.barang2_id', 'left')
            ->join('satuans', 'barang_master_spesifikasi.satuan_1 = satuans.id', 'left')
            ->where($condition)
            ->groupBy('rm_purchase_order_details.barang1_id')
            ->groupBy('rm_purchase_order_details.barang2_id')
            ->findAll();

        $finalRes = [];
        $total = 0;

        foreach ($res as $r) {
            if ($r->pph == "None") {
                // tidak ada pph
                $pph = 0;
            } else {
                if ($supplierDet['no_npwp'] != "") {
                    // ada npwp
                    $pph = $r->monthly_price * 0.0025;
                } else {
                    // tidak ada npwp
                    $pph = $r->monthly_price * 0.005;
                }
            }

            if ($r->pph == "Company") {
                $hargaBulananPph = ($r->monthly_price * $r->qty_total) - $pph;
                $hargaBulanan =  ($r->monthly_price * $r->qty_total) + $pph;
            } else {
                $hargaBulananPph = ($r->monthly_price * $r->qty_total) + $pph;
                $hargaBulanan =  ($r->monthly_price * $r->qty_total);
            }


            $total += $hargaBulananPph;

            $finalRes[] = [
                'nama_barang' => $r->barang_name,
                'spesifikasi' => $r->spesifikasi,
                'kode_satuan' => $r->kode_satuan,
                'qty'          => $r->qty_total,
                'harga_bulanan' =>  $hargaBulanan,
                'pph'   => $pph,
                'harga_bulanan_pph' => $hargaBulananPph
            ];
        }

        return [
            'all' => $finalRes,
            'supplier' => $supplierDet,
            'total' => $total
        ];
    }


    public function getSupplierForJurnal($supplierID)
    {
        $select =   "suppliers.*";
        return $this->asObject()
            ->select($select)
            ->where('suppliers.id', $supplierID)
            ->where('suppliers.deletedAt', null)
            ->findAll();
    }
}
