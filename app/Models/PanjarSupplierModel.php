<?php

namespace App\Models;

use CodeIgniter\Model;

class PanjarSupplierModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'panjar_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'company_id',
        'no_panjar',
        'supplier_id',
        'payment_date',
        'total_panjar',
        'jenis_panjar',
        'akun_kas',
        'akun_selisih',
        'type_panjar',
        'sisa_panjar',
        'is_posted'
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

    public function getPanjarSupplierList($addCondition, $condition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_panjar'     => 'panjar_supplier.no_panjar',
            'supplier_id'   => 'panjar_supplier.supplier_id',
            'payment_date'  => 'panjar_supplier.payment_date',
            'total_panjar'  => 'panjar_supplier.total_panjar',
            'createdAt'     => 'panjar_supplier.createdAt',
            'updatedAt'     => 'panjar_supplier.updatedAt'

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'panjar_supplier.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "panjar_supplier.*,name, akun_kas.id as akun_kas, akun_kas.nama_sub as akun_kas_name, akun_selisih.id as akun_selisih, akun_selisih.nama_sub as akun_selisih_name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'left')
            ->join('sub_akuns AS akun_kas', 'panjar_supplier.akun_kas = akun_kas.id', 'left')
            ->join('sub_akuns AS akun_selisih', 'panjar_supplier.akun_selisih = akun_selisih.id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);
        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['panjar_status'])) {
            $supplierDataQry->groupStart();
        }


        if (!empty($addCondition['panjar_status'])) {
            if ($addCondition['panjar_status'] == "ALL") {
                // JIKA ALL
                $supplierDataQry->whereIn('is_posted', ['1', '0']);
            } else {
                $status = $addCondition['panjar_status'] == "NOT_POSTING" ? '0' : '1';
                $supplierDataQry->where('is_posted', $status);
            }
        }



        // $supplierDataQry->where('is_posted', '0');


        if ($addCondition['search']) {
            $supplierDataQry->like('no_panjar', $addCondition['search'])->orLike('name', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $supplierDataQry->where('payment_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $supplierDataQry->where('payment_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['panjar_status'])) {
            $supplierDataQry->groupEnd();
        }



        $totalFilteredData = $supplierDataQry->countAllResults(false);
        $data = $supplierDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    //get panjar supplier by id array 
    public function getPanjarSupplierbyIDarray($id)
    {
        $selectQry = "panjar_supplier.*,type,name";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'left')
            ->join('sub_akuns AS akun_kas', 'panjar_supplier.akun_kas = akun_kas.id', 'left')
            ->join('sub_akuns AS akun_selisih', 'panjar_supplier.akun_selisih = akun_selisih.id', 'left')
            ->whereIn('panjar_supplier.id', $id)
            ->findAll();
        return $panjarSupplierData;
    }

    //get panjar id not use array
    public function getPanjarSupplierbyID($id)
    {
        $selectQry = "panjar_supplier.*,type,name, akun_kas.id as akun_kas, akun_kas.nama_sub as akun_kas_name, akun_selisih.id as akun_selisih, akun_selisih.nama_sub as akun_selisih_name";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'left')
            ->join('sub_akuns AS akun_kas', 'panjar_supplier.akun_kas = akun_kas.id', 'left')
            ->join('sub_akuns AS akun_selisih', 'panjar_supplier.akun_selisih = akun_selisih.id', 'left')
            ->find($id);
        return $panjarSupplierData;
    }

    // public function getSisaPembayaranbyID($id){
    //     $condition = [
    //         'panjar_supplier.id' => $id,
    //         'deletedAt' => NULL
    //     ];
    //     $selectQry = "panjar_supplier"
    // }

    public function getPanjarSupplierbySupplierId($id, $companyId)
    {

        $condition = [
            'panjar_supplier.supplier_id ' => $id,
            'panjar_supplier.deletedAt' => null,
            'panjar_supplier.jenis_panjar' => "PANJAR",
            'is_posted' => '1',
            'panjar_supplier.company_id' => $companyId
        ];

        $selectQry = "panjar_supplier.*";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            // ->join('local_po_payment_panjar', 'panjar_supplier.id = local_po_payment_panjar.panjar_id', 'left')
            ->where($condition)
            ->findAll();

        return $panjarSupplierData;
    }


    public function getPanjarTBSupplierbySupplierId($id, $companyId)
    {

        $condition = [
            'panjar_supplier.supplier_id ' => $id,
            'panjar_supplier.deletedAt' => null,
            'panjar_supplier.jenis_panjar' => "PANJAR_TB",
            'is_posted' => '1',
            'panjar_supplier.company_id' => $companyId
        ];

        $selectQry = "panjar_supplier.*";
        $panjarSupplierData = $this->asObject()
            ->select($selectQry)
            // ->join('local_po_payment_panjar', 'panjar_supplier.id = local_po_payment_panjar.panjar_id', 'left')
            ->where($condition)
            ->findAll();

        return $panjarSupplierData;
    }


    public function getNumber($companyId)
    {
        $month = date('m'); // Bulan saat ini (format: 01-12)
        $year = date('Y'); // Tahun saat ini (format: 2023)
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d'))); // Tanggal terakhir bulan ini
    
        $lastStr = convertBulanToAngkaRomawi($month) . '/' . $year; // Format: III/2023
    
        // Ambil no_panjar terakhir di bulan & tahun ini
        $builder = $this->asArray()->select('no_panjar')
            ->orderBy('no_panjar', "DESC")
            ->where('company_id', $companyId)
            ->where('createdAt >=', $year . "-" . $month . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59")
            ->first();
    
        $kode = 'PJR'; // Kode awal: PJR
        $lastNumber = 1; // Nomor awal: 1
    
        if ($builder != null && isset($builder['no_panjar'])) {
            $explode = explode('/', $builder['no_panjar']); // Pecah no_panjar menjadi array
    
            // Pastikan format no_panjar sesuai: PJR/X/2023/00001
            if (count($explode) == 4) {
                $numberStr = $explode[3]; // Ambil bagian nomor (00001)
                $number = intval($numberStr); // Konversi ke integer
                if ($number >= $lastNumber) {
                    $lastNumber = $number + 1; // Increment nomor terakhir
                }
            }
        }
    
        $formattedlastNumber = sprintf("%05d", $lastNumber); // Format nomor menjadi 5 digit (00001)
        $generatedNo = $kode . '/' . $lastStr . '/' . $formattedlastNumber; // Gabungkan semua bagian
    
        return $generatedNo;
    }

    public function getHistoryPembayaranPanjar($id)
    {

        $condition = [
            'panjar_supplier.id' => $id,

        ];

        $selectQry = "no_panjar, bayar_panjar, panjar_supplier.supplier_id, multiple_lpb_no, name, local_po_payments.payment_date";
        $historyPembayaranPanjarData = $this->asObject()
            ->select($selectQry)
            ->join('local_po_payment_panjar', 'panjar_supplier.id = local_po_payment_panjar.panjar_id', 'inner')
            ->join('local_po_payments', 'local_po_payments.id = local_po_payment_panjar.local_po_payment_id', 'inner')
            ->join('suppliers', 'panjar_supplier.supplier_id = suppliers.id', 'inner')
            ->where($condition)
            ->findAll();

        return $historyPembayaranPanjarData;
    }
}
