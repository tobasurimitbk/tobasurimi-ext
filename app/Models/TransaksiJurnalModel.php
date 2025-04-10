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


    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'transaksi_jurnal.id' => 'transaksi_jurnal.id',
            'transaksi_jurnal.type_transaksi' => 'transaksi_jurnal.type_transaksi',
            'transaksi_jurnal.no_transaksi' => 'transaksi_jurnal.no_transaksi',
            'transaksi_jurnal.tanggal_transaksi' => 'transaksi_jurnal.tanggal_transaksi',
            'transaksi_jurnal.uraian_transaksi' => 'transaksi_jurnal.uraian_transaksi',
            'transaksi_jurnal.metode_input' => 'transaksi_jurnal.metode_input',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'created_at';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "transaksi_jurnal.*,
        metadata.value as transaksi_type_name,

        ";

        $dataQry = $this->asObject()->select($selectQry);
        $dataQry->join('metadata', 'metadata.id = transaksi_jurnal.type_transaksi', 'left');
        $dataQry->join('jurnal_umum', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left');
        $dataQry->join('transaksi_pembelian', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left');
        $dataQry->where($condition);
        $dataQry->orderBy($sort, $sortType);
        $dataQry->groupBy('jurnal_umum.id_transaksi');

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['start_date'] || $addCondition['end_date'] || $addCondition['type_transaksi'] || $addCondition['search']) {
            $dataQry->groupStart();
        }

        if ($addCondition['start_date']) {
            $dataQry->where('transaksi_jurnal.tanggal_transaksi >=', $addCondition['start_date']);
        }

        if ($addCondition['end_date']) {
            $dataQry->where('transaksi_jurnal.tanggal_transaksi <=', $addCondition['end_date']);
        }

        if ($addCondition['type_transaksi']) {
            if ($addCondition['type_transaksi'] == "BAHAN BAKU") {
                $dataQry->where('transaksi_pembelian.id_local_bb !=', null);
                $dataQry->orWhere('transaksi_pembelian.id_import_bb !=', null);
            } elseif ($addCondition['type_transaksi'] == "BAHAN PENOLONG") {
                $dataQry->where('transaksi_pembelian.id_po_bp !=', null);
            } else {
                $dataQry->where('transaksi_jurnal.type_transaksi', $addCondition['type_transaksi']);
            }
        }

        if ($addCondition['search']) {
            $dataQry->like('no_transaksi', $addCondition['search'])->orLike('uraian_transaksi', $addCondition['search']);
        }

        if ($addCondition['start_date'] || $addCondition['end_date'] || $addCondition['type_transaksi'] || $addCondition['search']) {
            $dataQry->groupEnd();
        }

        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
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

    //     $selectQry = "
    //         transaksi_jurnal.*,
    //         metadata.value as transaksi_type_name,
    //         transaksi_pembelian.id_local_bb,
    //         transaksi_pembelian.id_import_bb,
    //         transaksi_pembelian.id_po_bp,
    //         pb_lokal.no_penerimaan_barang as no_penerimaan_lokal,
    //         pb_import.no_penerimaan_barang as no_penerimaan_import,
    //         pb_bp.no_penerimaan_barang as no_penerimaan_bp,
    //         s_lokal.name as supplier_lokal,
    //         s_import.name as supplier_import,
    //         s_bp.name as supplier_bp,
    //         am_purchase_orders.po_type
    //     ";

    //     // Base Query dengan semua JOIN
    //     $baseQry = $this->asObject()->select($selectQry);
    //     $baseQry->join('metadata', 'metadata.id = transaksi_jurnal.type_transaksi', 'left');
    //     $baseQry->join('jurnal_umum', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left');
    //     $baseQry->join('transaksi_pembelian', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left');

    //     // Gunakan FIND_IN_SET untuk multiple_po_id
    //     $baseQry->join(
    //         'penerimaan_barang pb_lokal',
    //         'pb_lokal.status_penerimaan = "LOKAL" AND pb_lokal.tipe_bahan = "BAKU" AND FIND_IN_SET(transaksi_pembelian.id_local_bb, pb_lokal.multiple_po_id)',
    //         'left'
    //     );
    //     $baseQry->join(
    //         'penerimaan_barang pb_import',
    //         'pb_import.status_penerimaan = "IMPORT" AND pb_import.tipe_bahan = "BAKU" AND FIND_IN_SET(transaksi_pembelian.id_import_bb, pb_import.multiple_po_id)',
    //         'left'
    //     );
    //     $baseQry->join(
    //         'penerimaan_barang pb_bp',
    //         'pb_bp.tipe_bahan = "PENOLONG" AND FIND_IN_SET(transaksi_pembelian.id_po_bp, pb_bp.multiple_po_id)',
    //         'left'
    //     );

    //     $baseQry->join('suppliers s_lokal', 's_lokal.id = pb_lokal.supplier_id', 'left');
    //     $baseQry->join('suppliers s_import', 's_import.id = pb_import.supplier_id', 'left');
    //     $baseQry->join('suppliers s_bp', 's_bp.id = pb_bp.supplier_id', 'left');
    //     $baseQry->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left');

    //     // Total data awal (tanpa filter pencarian/dinamis)
    //     $totalDataQry = clone $baseQry;
    //     $totalDataQry->where($condition);
    //     $totalData = $totalDataQry->countAllResults(false);

    //     // Query data utama
    //     $dataQry = clone $baseQry;
    //     $dataQry->where($condition);
    //     $dataQry->groupBy('jurnal_umum.id_transaksi');

    //     // Filter dinamis
    //     if (
    //         !empty($addCondition['start_date']) ||
    //         !empty($addCondition['end_date']) ||
    //         !empty($addCondition['type_transaksi']) ||
    //         !empty($addCondition['search'])
    //     ) {
    //         $dataQry->groupStart();

    //         if (!empty($addCondition['start_date'])) {
    //             $dataQry->where('transaksi_jurnal.tanggal_transaksi >=', $addCondition['start_date']);
    //         }

    //         if (!empty($addCondition['end_date'])) {
    //             $dataQry->where('transaksi_jurnal.tanggal_transaksi <=', $addCondition['end_date']);
    //         }

    //         if (!empty($addCondition['type_transaksi'])) {
    //             if ($addCondition['type_transaksi'] === "BAHAN BAKU") {
    //                 $dataQry->groupStart();
    //                 $dataQry->where('transaksi_pembelian.id_local_bb IS NOT NULL');
    //                 $dataQry->orWhere('transaksi_pembelian.id_import_bb IS NOT NULL');
    //                 $dataQry->groupEnd();
    //             } elseif ($addCondition['type_transaksi'] === "BAHAN PENOLONG") {
    //                 $dataQry->where('transaksi_pembelian.id_po_bp IS NOT NULL');
    //             } else {
    //                 $dataQry->where('transaksi_jurnal.type_transaksi', $addCondition['type_transaksi']);
    //             }
    //         }

    //         if (!empty($addCondition['search'])) {
    //             $dataQry->groupStart();
    //             $dataQry->like('transaksi_jurnal.no_transaksi', $addCondition['search']);
    //             $dataQry->orLike('transaksi_jurnal.uraian_transaksi', $addCondition['search']);
    //             $dataQry->orLike('pb_lokal.no_penerimaan_barang', $addCondition['search']);
    //             $dataQry->orLike('pb_import.no_penerimaan_barang', $addCondition['search']);
    //             $dataQry->orLike('pb_bp.no_penerimaan_barang', $addCondition['search']);
    //             $dataQry->orLike('s_lokal.name', $addCondition['search']);
    //             $dataQry->orLike('s_import.name', $addCondition['search']);
    //             $dataQry->orLike('s_bp.name', $addCondition['search']);
    //             $dataQry->groupEnd();
    //         }

    //         $dataQry->groupEnd();
    //     }

    //     // Hitung total data setelah filter
    //     $filteredQry = clone $dataQry;
    //     $totalFilteredData = $filteredQry->countAllResults(false);

    //     // Ambil data akhir
    //     $data = $dataQry->orderBy($sort, $sortType)->findAll($limit, $offset);

    //     return [
    //         'data'              => $data,
    //         'totalData'         => $totalData,
    //         'totalFilteredData' => $totalFilteredData,
    //     ];
    // }
}
