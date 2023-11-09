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
        'account_payable'
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
            // 'contact_person'    => 'suppliers.contact_person',
            // 'no_rekening'       => 'suppliers.no_rekening',
            // 'supplier_buyer'    => 'suppliers.supplier_buyer',
            // 'province'          => 'provinces.province_name',
            // 'city'              => 'cities.city_name',
            // 'postal_code'       => 'suppliers.postal_code',
            'createdAt'         => 'suppliers.createdAt',
            'updatedAt'         => 'suppliers.updatedAt',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'suppliers.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "suppliers.*";
        //   cities.city_name AS city_name, 
        //   provinces.province_name AS province_name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            // ->join('cities', 'suppliers.city_id = cities.id', 'left')
            // ->join('provinces', 'suppliers.province_id = provinces.id', 'left')
            ->orderBy($sort, $sortType);

        // $selectQry = "suppliers.*, 
        //               cities.city_name AS city_name, 
        //               provinces.province_name AS province_name,
        //               ap.nama_sub AS ap_name,
        //               ar.nama_sub AS ar_name";
        // $supplierDataQry = $this->asObject()
        //     ->select($selectQry)
        //     ->where($condition)
        //     ->join('cities', 'suppliers.city_id = cities.id', 'left')
        //     ->join('provinces', 'suppliers.province_id = provinces.id', 'left')
        //     ->join('sub_akuns AS ap', 'suppliers.ap_id = ap.id', 'left')
        //     ->join('sub_akuns AS ar', 'suppliers.ar_id = ar.id', 'left')
        //     ->orderBy($sort, $sortType);

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
            'type' => $type
        ];

        $builder = $this->db->table('suppliers');
        $builder->where($arrCondition);
        $builder->orderBy('suppliers.name', "ASC");
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function generateSupplierCode($type): string
    {
        $month = idate('m');
        $year = date('y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "$type";

        $lastData = $this->asObject()
            ->like('kode', $numberTemplate, 'before')
            ->orderBy('createdAt', 'DESC')
            ->first();


        if (!empty($lastData)) {
            $asd = explode('/', $lastData->kode);
            $lastIncrement = intval($asd[0]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        } else {

            $invNumber = '001' . $numberTemplate;
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
            'rm_purchase_orders.status_penerimaan' => 1,
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
}
