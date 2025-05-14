<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiJurnalModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'transaksi_jurnal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'penerimaan_barang_id',
        'no_transaksi',
        'tanggal_transaksi',
        'total_debit',
        'total_kredit',
        'metode_input',
        'tipe_barang',
        'kategori_barang',
        'po_id',
        'type_transaksi',
        'no_bukti',
        'valas',
        'exchange_rate',
        'uraian_transaksi',
        'valas_id'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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

    public function insertTransaksiJurnal($data)
    {
        return $this->insert($data);
    }
    public function insertBatchTransaksiJurnal($data)
    {
        return $this->insertBatch($data);
    }
    public function getIdTransaksiLast()
    {
        $transaksi_format = "TRN-";
        $query = $this->select('id')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();
        if ($query->resultID->num_rows > 0) {
            $row = $query->getRow();
            $lastTransaksi = $row->id;

            $newLastTransaksi = $lastTransaksi + 1;

            return $newLastTransaksi;
        } else {
            $newLastTransaksi = 1;
            return $newLastTransaksi; // Handle the case where no transactions are found
        }
    }

    public function getNoTransaksiLast($type)
    {
        if ($type) {
            $transaksi_format = $type . "-";
        }

        $query = $this->select('no_transaksi')
            ->like('no_transaksi', $transaksi_format)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();
        if ($query->resultID->num_rows > 0) {
            $row = $query->getRow();
            $lastTransaksi = $row->no_transaksi;

            // Extract the numerical part and increment it by 1
            $numericPart = (int)substr($lastTransaksi, strlen($transaksi_format));
            $newNumericPart = $numericPart + 1;

            // Format the new transaction number
            $newTransaksi = $transaksi_format . sprintf('%03d', $newNumericPart);

            return $newTransaksi;
        } else {
            return $transaksi_format . "001"; // Handle the case where no transactions are found
        }
    }

    public function searchNoBukti($query)
    {
        return $this->like('no_bukti', $query)->where('deleted_at', null)->findAll();
    }


    // public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    // {
    //     $availableSort = [
    //         'transaksi_jurnal.id' => 'transaksi_jurnal.id',
    //         'transaksi_jurnal.type_transaksi' => 'transaksi_jurnal.type_transaksi',
    //         'transaksi_jurnal.no_transaksi' => 'transaksi_jurnal.no_transaksi',
    //         'transaksi_jurnal.tanggal_transaksi' => 'transaksi_jurnal.tanggal_transaksi',
    //         'transaksi_jurnal.uraian_transaksi' => 'transaksi_jurnal.uraian_transaksi',
    //         'transaksi_jurnal.metode_input' => 'transaksi_jurnal.metode_input',
    //     ];
    //     $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

    //     $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'created_at';
    //     $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

    //     $selectQry = "transaksi_jurnal.*,
    //     metadata.value as transaksi_type_name,

    //     ";

    //     $dataQry = $this->asObject()->select($selectQry);
    //     $dataQry->join('metadata', 'metadata.id = transaksi_jurnal.type_transaksi', 'left');
    //     $dataQry->join('jurnal_umum', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left');
    //     $dataQry->join('transaksi_pembelian', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left');
    //     $dataQry->where($condition);
    //     $dataQry->orderBy($sort, $sortType);
    //     $dataQry->groupBy('jurnal_umum.id_transaksi');

    //     $totalData = $dataQry->countAllResults(false);

    //     if ($addCondition['start_date'] || $addCondition['end_date'] || $addCondition['type_transaksi'] || $addCondition['search']) {
    //         $dataQry->groupStart();
    //     }

    //     if ($addCondition['start_date']) {
    //         $dataQry->where('transaksi_jurnal.tanggal_transaksi >=', $addCondition['start_date']);
    //     }

    //     if ($addCondition['end_date']) {
    //         $dataQry->where('transaksi_jurnal.tanggal_transaksi <=', $addCondition['end_date']);
    //     }

    //     if ($addCondition['type_transaksi']) {
    //         if ($addCondition['type_transaksi'] == "BAHAN BAKU") {
    //             $dataQry->where('transaksi_pembelian.id_local_bb !=', null);
    //             $dataQry->orWhere('transaksi_pembelian.id_import_bb !=', null);
    //         } elseif ($addCondition['type_transaksi'] == "BAHAN PENOLONG") {
    //             $dataQry->where('transaksi_pembelian.id_po_bp !=', null);
    //         } else {
    //             $dataQry->where('transaksi_jurnal.type_transaksi', $addCondition['type_transaksi']);
    //         }
    //     }

    //     if ($addCondition['search']) {
    //         $dataQry->like('no_transaksi', $addCondition['search'])->orLike('uraian_transaksi', $addCondition['search']);
    //     }

    //     if ($addCondition['start_date'] || $addCondition['end_date'] || $addCondition['type_transaksi'] || $addCondition['search']) {
    //         $dataQry->groupEnd();
    //     }

    //     $totalFilteredData = $dataQry->countAllResults(false);
    //     $data = $dataQry->findAll($limit, $offset);

    //     return [
    //         'data'              => $data,
    //         'totalData'         => $totalData,
    //         'totalFilteredData' => $totalFilteredData,
    //     ];
    // }

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'transaksi_jurnal.id' => 'transaksi_jurnal.id',
            'transaksi_jurnal.type_transaksi' => 'transaksi_jurnal.type_transaksi',
            'transaksi_jurnal.penerimaan_barang_id' => 'transaksi_jurnal.penerimaan_barang_id',
            'transaksi_jurnal.no_transaksi' => 'transaksi_jurnal.no_transaksi',
            'transaksi_jurnal.tanggal_transaksi' => 'transaksi_jurnal.tanggal_transaksi',
            'transaksi_jurnal.uraian_transaksi' => 'transaksi_jurnal.uraian_transaksi',
            'transaksi_jurnal.metode_input' => 'transaksi_jurnal.metode_input',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'transaksi_jurnal.created_at'] ?? 'transaksi_jurnal.created_at';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        $selectQry = "
        transaksi_jurnal.id,
        transaksi_jurnal.tanggal_transaksi,
        transaksi_jurnal.no_transaksi,
        transaksi_jurnal.uraian_transaksi,
        transaksi_jurnal.metode_input,
        transaksi_jurnal.valas,
        transaksi_jurnal.exchange_rate,
        transaksi_jurnal.total_debit,
        transaksi_jurnal.no_bukti,
        penerimaan_barang.no_penerimaan_barang,                 
        metadata.value as transaksi_type_name,
        transaksi_pembelian.id_local_bb,
        transaksi_pembelian.id_import_bb,
        transaksi_pembelian.id_po_bp,
        jurnal_umum.supplier_id,
        suppliers.name as supplier_name,
        am_purchase_orders.po_type
    ";

        // === Query Utama ===
        $dataQry = $this->asObject()->select($selectQry)
            ->join('metadata', 'metadata.id = transaksi_jurnal.type_transaksi', 'left')
            ->join('jurnal_umum', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('transaksi_pembelian', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left')
            ->join('suppliers', 'suppliers.id = transaksi_pembelian.id_supplier', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = transaksi_jurnal.penerimaan_barang_id', 'left')
            ->where($condition)
            ->groupBy('jurnal_umum.id_transaksi');

        // === Tambahkan Filter Dinamis ===
        if (!empty($addCondition['start_date'])) {
            $dataQry->where('transaksi_jurnal.tanggal_transaksi >=', $addCondition['start_date']);
        }

        if (!empty($addCondition['end_date'])) {
            $dataQry->where('transaksi_jurnal.tanggal_transaksi <=', $addCondition['end_date']);
        }

        if (!empty($addCondition['type_transaksi'])) {
            if ($addCondition['type_transaksi'] === "BAHAN BAKU") {
                $dataQry->groupStart()
                    ->where('transaksi_pembelian.id_local_bb IS NOT NULL')
                    ->orWhere('transaksi_pembelian.id_import_bb IS NOT NULL')
                    ->groupEnd();
            } elseif ($addCondition['type_transaksi'] === "BAHAN PENOLONG") {
                $dataQry->where('transaksi_pembelian.id_po_bp IS NOT NULL');
            } else {
                $dataQry->where('transaksi_jurnal.type_transaksi', $addCondition['type_transaksi']);
            }
        }

        if (!empty($addCondition['search'])) {
            $dataQry->groupStart()
                ->like('transaksi_jurnal.no_transaksi', $addCondition['search'])
                ->orLike('transaksi_jurnal.uraian_transaksi', $addCondition['search'])
                ->orLike('transaksi_jurnal.total_debit', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->orLike('suppliers.name', $addCondition['search'])
                ->groupEnd();
        }

        // === Total Filtered Data (lebih ringan) ===
        $filteredCountQry = $this->db->table('transaksi_jurnal')
            ->join('jurnal_umum', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('transaksi_pembelian', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = transaksi_jurnal.penerimaan_barang_id', 'left')
            ->where($condition);

        if (!empty($addCondition['start_date'])) {
            $filteredCountQry->where('transaksi_jurnal.tanggal_transaksi >=', $addCondition['start_date']);
        }

        if (!empty($addCondition['end_date'])) {
            $filteredCountQry->where('transaksi_jurnal.tanggal_transaksi <=', $addCondition['end_date']);
        }

        if (!empty($addCondition['type_transaksi'])) {
            if ($addCondition['type_transaksi'] === "BAHAN BAKU") {
                $filteredCountQry->groupStart()
                    ->where('transaksi_pembelian.id_local_bb IS NOT NULL')
                    ->orWhere('transaksi_pembelian.id_import_bb IS NOT NULL')
                    ->groupEnd();
            } elseif ($addCondition['type_transaksi'] === "BAHAN PENOLONG") {
                $filteredCountQry->where('transaksi_pembelian.id_po_bp IS NOT NULL');
            } else {
                $filteredCountQry->where('transaksi_jurnal.type_transaksi', $addCondition['type_transaksi']);
            }
        }

        if (!empty($addCondition['search'])) {
            $filteredCountQry->groupStart()
                ->like('transaksi_jurnal.no_transaksi', $addCondition['search'])
                ->orLike('transaksi_jurnal.uraian_transaksi', $addCondition['search'])
                ->orLike('transaksi_jurnal.total_debit', $addCondition['search'])
                ->orLike('penerimaan_barang.no_penerimaan_barang', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $filteredCountQry->countAllResults();

        // === Total Data Keseluruhan (tanpa filter) ===
        $totalData = $this->db->table('transaksi_jurnal')
            ->join('jurnal_umum', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->where($condition)
            ->countAllResults();

        // === Ambil data dengan limit offset ===
        $dataQry->orderBy($sort, $sortType);
        $data = $dataQry->findAll($limit ?? 10, $offset ?? 0);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }
}
