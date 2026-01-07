<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalUmumModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'jurnal_umum';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'id_transaksi',
        'divisi_id',
        'supplier_id',
        'company_id',
        'id_coa',
        'tanggal_jurnal',
        'debit',
        'kredit',
        'valas',
        'kurs',
        'keterangan',
        'id_inputer',
        'reference_id',
        'reference_type',
    ];

    // Dates
    protected $useTimestamps = false;
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

    public function insertJurnal($data)
    {
        return $this->insert($data);
    }

    public function insertJurnalBatch($data)
    {
        return $this->insertBatch($data);
    }

    public function getDataJurnal($where)
    {
        $builder = $this->db->table('jurnal_umum');

        // handle company id (array → whereIn)
        if (isset($where['jurnal_umum.company_id']) && is_array($where['jurnal_umum.company_id'])) {
            $builder->whereIn('jurnal_umum.company_id', $where['jurnal_umum.company_id']);
            unset($where['jurnal_umum.company_id']);
        }

        // handle tanggal >= dan <=
        if (isset($where['tanggal_jurnal >='])) {
            $builder->where('tanggal_jurnal >=', $where['tanggal_jurnal >=']);
            unset($where['tanggal_jurnal >=']);
        }

        if (isset($where['tanggal_jurnal <='])) {
            $builder->where('tanggal_jurnal <=', $where['tanggal_jurnal <=']);
            unset($where['tanggal_jurnal <=']);
        }

        // handle deletedAt NULL
        $builder->where('deletedAt', null);

        // handle kondisi lain kalau masih ada
        if (!empty($where)) {
            $builder->where($where);
        }

        return $builder->get()->getResult();
    }

    public function getDataJurnalForCosting($where)
    {
        $saldolama = 0;
        $where['deletedAt'] = null;
        $selectQry = 'jurnal_umum.*, metadata.value';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('sub_akuns', 'sub_akuns.id = jurnal_umum.id_coa', 'left')
            ->join('kategori_akuns', 'kategori_akuns.id = sub_akuns.kategori_id', 'left')
            ->join('metadata', 'metadata.id = kategori_akuns.kelompok_id', 'left')
            // ->like('tanggal_jurnal', $where['tanggal_jurnal'])
            ->where('tanggal_jurnal >=', $where['tanggal_awal'])
            ->where('tanggal_jurnal <=', $where['tanggal_akhir'])
            ->where('jurnal_umum.deletedAt', $where['deletedAt'])
            ->where('jurnal_umum.id_coa', $where['id_coa'])
            ->findAll();

        foreach ($dataQry as &$valueJurnal) {
            $debit = floatval($valueJurnal['debit']);
            $kredit = floatval($valueJurnal['kredit']);
            if (stripos($valueJurnal['value'], "Aktiva") !== false) {
                if ($debit == 0) {
                    $saldolama = $saldolama + $debit - $kredit;
                } else {
                    $saldolama = $saldolama + $debit;
                }
            } else {
                if ($kredit == 0) {
                    $saldolama = $saldolama + $kredit - $debit;
                } else {
                    $saldolama = $saldolama + $kredit;
                }
            }
            $valueJurnal['saldoTotal'] = $saldolama;
        }

        return $saldolama;
    }

    public function getTotalSaldoLama($where)
    {
        if (empty($where['tanggal_awal']) || empty($where['id_coa'])) {
            return 0;
        }

        // Tentukan company scope
        if ($where['company_id'] == 1 || $where['company_id'] == 2) {
            $companyId = [1, 2];
            $companyScope = [1, 2];
        } else if ($where['company_id'] == 15) {
            $companyId = [15];
            $companyScope = [15];
        } else {
            $companyId = [16];
            $companyScope = [16];
        }

        $subModel = new \App\Models\Sub_AkunsModel(); // sesuaikan namespace model lu
        $idCoa = is_array($where['id_coa']) ? $where['id_coa'] : [$where['id_coa']];

        // 1️⃣ Ambil semua no_sub dari id_coa yang dikirim
        $subList = $subModel
            ->select('no_sub')
            ->whereIn('id', $idCoa)
            ->whereIn('company_id', $companyScope)
            ->where('deletedAt', null)
            ->findAll();



        if (empty($subList)) {
            return 0;
        }

        $noSubs = array_column($subList, 'no_sub');

        // 2️⃣ Ambil semua id yang punya no_sub yang sama (karena bisa duplicate antar divisi)
        $relatedSubIds = $subModel
            ->select('id')
            ->whereIn('no_sub', $noSubs)
            ->whereIn('company_id', $companyScope)
            ->where('deletedAt', null)
            ->findColumn('id');

        if (empty($relatedSubIds)) {
            return 0;
        }

        // 3️⃣ Query ke jurnal umum pakai semua id dan scope company
        $row = $this
            ->select('COALESCE(SUM(jurnal_umum.debit),0) AS total_debit, COALESCE(SUM(jurnal_umum.kredit),0) AS total_kredit')
            ->join('transaksi_jurnal', 'transaksi_jurnal.id = jurnal_umum.id_transaksi')
            ->whereIn('jurnal_umum.id_coa', $relatedSubIds)
            ->whereIn('jurnal_umum.company_id', $companyId)
            ->where('transaksi_jurnal.type_transaksi', 1404)
            ->where('jurnal_umum.deletedAt', null)
            ->where('transaksi_jurnal.deleted_at', null)
            ->get()
            ->getRowArray();

        $totalDebit  = (float) ($row['total_debit'] ?? 0);
        $totalKredit = (float) ($row['total_kredit'] ?? 0);


        return $totalDebit - $totalKredit;
    }


    public function getListExport($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'jurnal_umum.id' => 'jurnal_umum.id',
            'transaksi_jurnal.no_transaksi' => 'transaksi_jurnal.no_transaksi',
            'transaksi_jurnal.tanggal_transaksi' => 'transaksi_jurnal.tanggal_transaksi',
            'transaksi_jurnal.uraian_transaksi' => 'transaksi_jurnal.uraian_transaksi',
            'divisis.divisi' => 'divisis.divisi',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'jurnal_umum.id'] ?? 'jurnal_umum.id';
        $sortType = $availableSortType[strtolower($addCondition['sortType'] ?? 'desc')] ?? 'DESC';

        // SELECT
        $selectQry = "
        jurnal_umum.id,
        jurnal_umum.id_transaksi,
        jurnal_umum.keterangan,
        jurnal_umum.divisi_id,
        jurnal_umum.supplier_id,
        jurnal_umum.debit,
        jurnal_umum.kredit,
        jurnal_umum.tanggal_jurnal,
        sub_akuns.no_sub as kode_coa,
        sub_akuns.nama_sub as nama_coa,
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
        suppliers.name as supplier_name,
        am_purchase_orders.po_type,
        divisis.divisi as divisi_name,
        GROUP_CONCAT(DISTINCT jurnal_umum.keterangan SEPARATOR ', ') as keterangan_jurnal
    ";

        // === Get Data ===
        $dataQry = $this->db->table('jurnal_umum')
            ->select($selectQry)
            ->join('transaksi_jurnal', 'transaksi_jurnal.id = jurnal_umum.id_transaksi', 'left')
            ->join('metadata', 'metadata.id = transaksi_jurnal.type_transaksi', 'left')
            ->join('transaksi_pembelian', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left')
            ->join('am_purchase_orders', 'am_purchase_orders.id = transaksi_pembelian.id_po_bp', 'left')
            ->join('suppliers', 'suppliers.id = transaksi_pembelian.id_supplier', 'left')
            ->join('penerimaan_barang', 'penerimaan_barang.id = transaksi_jurnal.penerimaan_barang_id', 'left')
            ->join('divisis', 'divisis.id = jurnal_umum.divisi_id', 'left')
            ->join('sub_akuns', 'sub_akuns.id = jurnal_umum.id_coa', 'left')
            ->where($condition)
            ->groupBy('jurnal_umum.id')
            ->orderBy($sort, $sortType);

        if (!empty($addCondition['start_date'])) {
            $dataQry->where('jurnal_umum.tanggal_jurnal >=', $addCondition['start_date']);
        }

        if (!empty($addCondition['end_date'])) {
            $dataQry->where('jurnal_umum.tanggal_jurnal <=', $addCondition['end_date']);
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

        // === Total Filtered Data ===
        $filteredCountQry = $this->db->table('jurnal_umum')
            ->join('transaksi_jurnal', 'transaksi_jurnal.id = jurnal_umum.id_transaksi', 'left')
            ->join('transaksi_pembelian', 'transaksi_pembelian.id_transaksi_jurnal = transaksi_jurnal.id', 'left')
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
                ->orLike('suppliers.name', $addCondition['search'])
                ->groupEnd();
        }

        $totalFilteredData = $filteredCountQry->countAllResults();

        // === Total Semua Data ===
        $totalData = $this->db->table('jurnal_umum')->countAllResults();

        // === Get Final Data ===
        $data = $dataQry
            ->limit($limit, $offset)
            ->get()
            ->getResult();

        return [
            'data' => $data,
            'totalFilteredData' => $totalFilteredData,
            'totalData' => $totalData
        ];
    }
}
