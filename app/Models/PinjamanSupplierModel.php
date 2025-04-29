<?php

namespace App\Models;

use CodeIgniter\Model;

class PinjamanSupplierModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'pinjaman_supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'company_id',
        'transaction_id',
        'no_pinjaman',
        'supplier_id',
        'keterangan',
        'payment_date',
        'total_pinjaman',
        'akun_kas',
        'akun_selisih',
        'sisa_pinjaman',
        'type_pinjaman',
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

    public function getPinjamanSupplierList($addCondition, $condition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'no_pinjaman'     => 'pinjaman_supplier.no_pinjaman',
            'supplier_id'   => 'pinjaman_supplier.supplier_id',
            'payment_date'  => 'pinjaman_supplier.payment_date',
            'total_pinjaman'  => 'pinjaman_supplier.total_pinjaman',
            'createdAt'     => 'pinjaman_supplier.createdAt',
            'updatedAt'     => 'pinjaman_supplier.updatedAt'

        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'pinjaman_supplier.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "pinjaman_supplier.*,name, akun_kas.id as akun_kas, akun_kas.nama_sub as akun_kas_name, akun_selisih.id as akun_selisih, akun_selisih.nama_sub as akun_selisih_name";
        $supplierDataQry = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'left')
            ->join('sub_akuns AS akun_kas', 'pinjaman_supplier.akun_kas = akun_kas.id', 'left')
            ->join('sub_akuns AS akun_selisih', 'pinjaman_supplier.akun_selisih = akun_selisih.id', 'left')
            ->where($condition)
            ->orderBy($sort, $sortType);
        $totalData = $supplierDataQry->countAllResults(false);

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['pinjaman_status'])) {
            $supplierDataQry->groupStart();
        }


        if (!empty($addCondition['pinjaman_status'])) {
            if ($addCondition['pinjaman_status'] == "ALL") {
                // JIKA ALL
                $supplierDataQry->whereIn('is_posted', ['1', '0']);
            } else {
                $status = $addCondition['pinjaman_status'] == "NOT_POSTING" ? '0' : '1';
                $supplierDataQry->where('is_posted', $status);
            }
        }



        // $supplierDataQry->where('is_posted', '0');


        if ($addCondition['search']) {
            $supplierDataQry->like('no_pinjaman', $addCondition['search'])->orLike('name', $addCondition['search']);
        }

        if ($addCondition['dateStart']) {
            $supplierDataQry->where('payment_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $supplierDataQry->where('payment_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['search'] || $addCondition['dateStart'] || $addCondition['dateEnd'] || !empty($addCondition['pinjaman_status'])) {
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

    //get pinjaman supplier by id array 
    public function getPinjamanSupplierbyIDarray($id)
    {
        $selectQry = "pinjaman_supplier.*,type,name";
        $pinjamanSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'left')
            ->whereIn('pinjaman_supplier.id', $id)
            ->findAll();
        return $pinjamanSupplierData;
    }

    //get pinjaman id not use array
    public function getPinjamanSupplierbyID($id)
    {
        $selectQry = "pinjaman_supplier.*, type, name, akun_kas.id as akun_kas, akun_kas.nama_sub as akun_kas_name, akun_selisih.id as akun_selisih, akun_selisih.nama_sub as akun_selisih_name";
        $pinjamanSupplierData = $this->asObject()
            ->select($selectQry)
            ->join('sub_akuns AS akun_kas', 'pinjaman_supplier.akun_kas = akun_kas.id', 'left')
            ->join('sub_akuns AS akun_selisih', 'pinjaman_supplier.akun_selisih = akun_selisih.id', 'left')
            ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'left')
            ->find($id);
        return $pinjamanSupplierData;
    }

    // public function getSisaPembayaranbyID($id){
    //     $condition = [
    //         'pinjaman_supplier.id' => $id,
    //         'deletedAt' => NULL
    //     ];
    //     $selectQry = "pinjaman_supplier"
    // }

    public function getPinjamanSupplierbySupplierId($id, $companyId)
    {

        $condition = [
            'pinjaman_supplier.supplier_id ' => $id,
            'pinjaman_supplier.deletedAt' => null,
            'is_posted' => '1',
            'pinjaman_supplier.company_id' => $companyId
        ];

        $selectQry = "pinjaman_supplier.*";
        $pinjamanSupplierData = $this->asObject()
            ->select($selectQry)
            // ->join('local_po_payment_pinjaman', 'pinjaman_supplier.id = local_po_payment_pinjaman.pinjaman_id', 'left')
            ->where($condition)
            ->findAll();

        return $pinjamanSupplierData;
    }




    public function getNumber($companyId)
    {
        $month = date('m'); // Bulan saat ini (format: 01-12)
        $year = date('Y'); // Tahun saat ini (format: 2023)
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d'))); // Tanggal terakhir bulan ini
    
        $lastStr = convertBulanToAngkaRomawi($month) . '/' . $year; // Format: III/2023
    
        // Ambil no_pinjaman terakhir di bulan & tahun ini
        $builder = $this->asArray()->select('no_pinjaman')
            ->orderBy('no_pinjaman', "DESC")
            ->where('company_id', $companyId)
            ->where('createdAt >=', $year . "-" . $month . "-01" . " 00:00:00")
            ->where('createdAt <=', $last_day . " 23:59:59")
            ->first();
    
        $kode = 'PJMN'; // Kode awal: PJMN
        $lastNumber = 1; // Nomor awal: 1
    
        if ($builder != null && isset($builder['no_pinjaman'])) {
            $explode = explode('/', $builder['no_pinjaman']); // Pecah no_pinjaman menjadi array
    
            // Pastikan format no_pinjaman sesuai: PJR/X/2023/00001
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

    public function getHistoryPembayaranpinjaman($id)
    {

        $condition = [
            'pinjaman_supplier.id' => $id,

        ];

        $selectQry = "no_pinjaman, bayar_pinjaman, pinjaman_supplier.supplier_id, multiple_lpb_no, name, local_po_payments.payment_date";
        $historyPembayaranpinjamanData = $this->asObject()
            ->select($selectQry)
            ->join('local_po_payment_pinjaman', 'pinjaman_supplier.id = local_po_payment_pinjaman.pinjaman_id', 'inner')
            ->join('local_po_payments', 'local_po_payments.id = local_po_payment_pinjaman.local_po_payment_id', 'inner')
            ->join('suppliers', 'pinjaman_supplier.supplier_id = suppliers.id', 'inner')
            ->where($condition)
            ->findAll();

        return $historyPembayaranpinjamanData;
    }
    
}
