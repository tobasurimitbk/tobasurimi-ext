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
        'no_ktp',
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
            'no_ktp'           => 'suppliers.no_ktp',
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
        $dateStart = $addCondition['startdate'] ?? null;
        $dateEnd   = $addCondition['lastdate'] ?? null;
        $result = [];

        $supplierModel = new SupplierModel();
        $suppliers = $supplierModel->asObject()
            ->select("suppliers.id, suppliers.name, suppliers.type")
            ->where($condition)
            ->whereIn('suppliers.company_id', $companyId)
            ->when(!empty($addCondition['filter']), fn($q) => $q->whereIn('suppliers.id', $addCondition['filter']))
            ->findAll();

        foreach ($suppliers as $supplier) {
            $supplierId = $supplier->id;
            $supplierName = $supplier->name;
            $supplierType = $supplier->type;

            // Query total invoice
            $invoiceBuilder = db_connect()->table('penerimaan_barang_detail')
                ->selectSum('penerimaan_barang_detail.sub_total', 'total_invoice')
                ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id')
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->where('penerimaan_barang.deletedAt', null)
                ->where('penerimaan_barang.supplier_id', $supplierId);

            if ($supplierType == 'BAHAN BAKU') {
                $invoiceBuilder->where('penerimaan_barang.tipe_bahan', 'BAKU');
            } elseif ($supplierType == 'BAHAN PENOLONG') {
                $invoiceBuilder->where('penerimaan_barang.tipe_bahan', 'PENOLONG');
            } elseif ($supplierType == 'INTERNASIONAL') {
                $invoiceBuilder->where('penerimaan_barang.tipe_bahan', 'INTERNASIONAL');
            }

            if ($dateStart) $invoiceBuilder->where('penerimaan_barang.tanggal >=', $dateStart);
            if ($dateEnd) $invoiceBuilder->where('penerimaan_barang.tanggal <=', $dateEnd);

            $totalInvoiceRow = $invoiceBuilder->get()->getRow();
            $totalInvoice = floatval($totalInvoiceRow->total_invoice ?? 0);

            // Query total payment
            if ($supplierType == 'INTERNASIONAL') {
                $paymentBuilder = db_connect()->table('import_po_payments')
                    ->selectSum('(payment_amt * current_exchange_rate)', 'total_paid')
                    ->where('supplier_id', $supplierId)
                    ->where('status_posting', 1);
            } else {
                $paymentBuilder = db_connect()->table('local_po_payments')
                    ->selectSum('amount', 'total_paid')
                    ->where("FIND_IN_SET('$supplierId', REPLACE(REPLACE(supplier_id, '[', ''), ']', '')) != ", 0); // atau supplier_id langsung
            }

            $totalPaidRow = $paymentBuilder->get()->getRow();
            $totalPaid = floatval($totalPaidRow->total_paid ?? 0);

            // Query no_penerimaan_barang
            $noPBRows = db_connect()->table('penerimaan_barang')
                ->select("GROUP_CONCAT(DISTINCT no_penerimaan_barang SEPARATOR ', ') AS no_pb")
                ->where('supplier_id', $supplierId)
                ->where('deletedAt', null);

            if ($supplierType == 'BAHAN BAKU') $noPBRows->where('tipe_bahan', 'BAKU');
            elseif ($supplierType == 'BAHAN PENOLONG') $noPBRows->where('tipe_bahan', 'PENOLONG');
            elseif ($supplierType == 'INTERNASIONAL') $noPBRows->where('tipe_bahan', 'INTERNASIONAL');

            if ($dateStart) $noPBRows->where('tanggal >=', $dateStart);
            if ($dateEnd) $noPBRows->where('tanggal <=', $dateEnd);

            $noPB = $noPBRows->get()->getRow()->no_pb ?? '-';

            if ($totalInvoice > 0) {
                $result[] = [
                    'id' => $supplierId,
                    'supplier' => $supplierName,
                    'no_penerimaan_barang' => $noPB,
                    'nominal_idr' => $totalInvoice,
                    'remaining_idr' => $totalInvoice - $totalPaid,
                ];
            }
        }

        $totalData = count($result);
        $paged = array_slice($result, $offset, $limit);

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

    public function getSupplier($type)
    {
        $arrCondition = [
            'deletedAt' => null,
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

    public function getSupplierJasVend()
    {
        $arrCondition = [
            'deletedAt'  => null,
            'company_id' => session()->get('login')->this_company_id,
        ];

        $builder = $this->db->table('suppliers');
        $builder->where($arrCondition);
        $builder->where('type !=', 'BAHAN PENOLONG'); // cara aman di CI4
        $builder->orderBy('suppliers.name', "ASC");

        $results = $builder->get()->getResultArray();

        foreach ($results as &$result) {
            $result['name'] = strtoupper($result['name']);
        }

        return $results;
    }



    public function getSupplierAll()
    {
        $arrCondition = [
            'deletedAt' => null,
            'company_id' => session()->get('login')->this_company_id,
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
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null,
            'rm_purchase_order_details.monthly_price !=' => 0
        ];

        $selectQry = "
        rm_purchase_orders.id AS po_id,
        rm_purchase_orders.pph,
        rm_purchase_orders.po_date,
        rm_purchase_orders.cong_batasan,
        rm_purchase_orders.cong_sebenarnya,
        rm_purchase_orders.subsidi_langsung,
        SUM(rm_purchase_order_details.qty) as qty_total,
        rm_purchase_orders.nilai_total_bulanan as total_bulanan,
        rm_purchase_orders.pph_bulanan as total_pph_bulanan,
        rm_purchase_orders.dpp_bulanan as total_dpp_bulanan,
        barang_master.barang_name
    ";

        $res = $rmPurchaseOrderModel
            ->asObject()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = rm_purchase_orders.barang_id', 'left')
            ->join('rm_purchase_order_details', 'rm_purchase_order_details.rm_purchase_order_id = rm_purchase_orders.id', 'left')
            ->where($condition)
            ->groupBy('rm_purchase_order_details.rm_purchase_order_id')
            ->findAll();

        $finalRes = [];
        $total = 0;

        foreach ($res as $r) {
            $finalRes[] = [
                'nama_barang' => $r->barang_name,
                'kode_satuan' => "",
                'qty'         => $r->qty_total,
                'harga_bulanan' => $r->total_dpp_bulanan,
                'pph'         => $r->total_pph_bulanan,
                'harga_bulanan_pph' => $r->total_bulanan,
            ];

            $total += $r->total_bulanan;
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

    public function getSupplierListKwitansiBulanan($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'name'          => 'suppliers.name',
            'total_bulanan' => 'total_bulanan',
            'createdAt'     => 'suppliers.createdAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort     = $availableSort[$addCondition['sort'] ?? 'createdAt'];
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'];

        $selectQry = "
        suppliers.*,
        COALESCE(SUM(rm_purchase_orders.nilai_total_bulanan), 0) as total_bulanan
    ";

        $builder = $this->asArray()
            ->select($selectQry)
            ->join('rm_purchase_orders', 'rm_purchase_orders.supplier_id = suppliers.id', 'left')
            ->where($condition)
            ->groupBy('suppliers.id');

        // filter by year_month
        if (!empty($addCondition['year_month'])) {
            // Bersihin input, pastikan format YYYY-MM
            $yearMonth = preg_replace('/[^0-9\-]/', '', $addCondition['year_month']);
            $yearMonth = date('Y-m', strtotime($yearMonth . '-01'));

            $dateStart = $yearMonth . "-01";
            $lastDay   = date("t", strtotime($dateStart));
            $dateEnd   = $yearMonth . "-" . $lastDay;

            $builder->where('rm_purchase_orders.po_date >=', $dateStart);
            $builder->where('rm_purchase_orders.po_date <=', $dateEnd);
        }


        // search
        if (!empty($addCondition['search'])) {
            $builder->groupStart()
                ->like('suppliers.name', $addCondition['search'])
                ->groupEnd();
        }

        // filter by tb_search
        if (!empty($addCondition['tb_search'])) {
            if ($addCondition['tb_search'] == "PUNYA TB") {
                $builder->having('total_bulanan >', 0);
            } elseif ($addCondition['tb_search'] == "TIDAK PUNYA TB") {
                $builder->having('total_bulanan <=', 0);
            }
        }

        // clone builder buat total data
        $builderTotal = clone $builder;
        $builderFiltered = clone $builder;

        $totalData = $builderTotal->countAllResults(false);
        $totalFilteredData = $builderFiltered->countAllResults(false);

        // ambil data
        $data = $builder->orderBy($sort, $sortType)
            ->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }
}
