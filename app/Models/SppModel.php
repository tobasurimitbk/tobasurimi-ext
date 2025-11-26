<?php

namespace App\Models;

use CodeIgniter\I18n\Time;

use CodeIgniter\Model;

class SppModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'purchase_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_id',
        'user_id',
        'request_date',
        'spp_no',
        'spp_type',
        'divisi_id',
        'note',
        'is_posted',
        'request_status',
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

    public function getSppList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'sppType'          => 'purchase_requests.spp_type',
            'sppNo'            => 'purchase_requests.spp_no',
            'divisi'            => 'divisis.divisi',
            'company'            => 'companies.company',
            'requestDate'      => 'purchase_requests.request_date',
            'is_posted' => 'purchase_requests.is_posted'
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'purchase_requests.updatedAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "purchase_requests.*,
                    divisis.divisi AS divisiName, 
                    companies.company AS companyName, 
                    COUNT(purchase_request_details.id) AS itemCount";

        $purchaseRequestsDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('divisis', 'purchase_requests.divisi_id = divisis.id', 'left')
            ->join('companies', 'purchase_requests.company_id = companies.id', 'left')
            ->join('purchase_request_details', 'purchase_requests.id = purchase_request_details.purchase_request_id', 'left')
            ->groupBy(('purchase_requests.id'))
            ->orderBy($sort, $sortType);

        $totalData = $purchaseRequestsDataQry->countAllResults(false);

        if ($addCondition['is_posted']) {
            if ($addCondition['is_posted'] == "SUDAH POSTING") {
                $purchaseRequestsDataQry->where('is_posted', 1);
            } else {
                $purchaseRequestsDataQry->where('is_posted', 0);
            }
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $purchaseRequestsDataQry->groupStart();
        }

        if ($addCondition['dateStart']) {
            $purchaseRequestsDataQry->where('purchase_requests.request_date >=',  $addCondition['dateStart']);
        }
        if ($addCondition['dateEnd']) {
            $purchaseRequestsDataQry->where('purchase_requests.request_date <=', $addCondition['dateEnd']);
        }

        if ($addCondition['dateStart'] || $addCondition['dateEnd']) {
            $purchaseRequestsDataQry->groupEnd();
        }

        if ($addCondition['search'] || $addCondition['divisi_id'] || $addCondition['spp_type']) {
            $purchaseRequestsDataQry->groupStart();
        }
        if ($addCondition['divisi_id']) {
            $purchaseRequestsDataQry->where('purchase_requests.divisi_id', $addCondition['divisi_id']);
        }
        if ($addCondition['search']) {
            $purchaseRequestsDataQry
                ->like('spp_no', $addCondition['search'])
                ->orLike('divisis.divisi', $addCondition['search']);
        }

        if ($addCondition['spp_type']) {
            $purchaseRequestsDataQry
                ->like('spp_type', $addCondition['spp_type']);
        }

        if ($addCondition['search'] || $addCondition['divisi_id'] || $addCondition['spp_type']) {
            $purchaseRequestsDataQry->groupEnd();
        }

        $totalFilteredData = $purchaseRequestsDataQry->countAllResults(false);
        $data = $purchaseRequestsDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getSppById($id)
    {
        $selectQry = "purchase_requests.*,
        divisis.divisi AS divisiName,
        companies.company AS companyName,
        users.name AS createdByName
        ";

        $sppData = $this->asObject()
            ->select($selectQry)
            ->join('divisis', 'purchase_requests.divisi_id = divisis.id', 'left')
            ->join('companies', 'purchase_requests.company_id = companies.id', 'left')
            ->join('users', 'purchase_requests.user_id = users.id', 'left')
            ->find($id);

        return $sppData;
    }

    public function getNoSPP($type)
    {
        // is posted 0 artinya spp masih open 
        // request staatus waiting artinya di po belum ada yang menggunakan nomor spp tersebut
        $arrCondition = [
            'deletedAt' => null,
            'spp_type' => $type,
            'is_posted' => 0,
            'request_status' => 'waiting'
        ];

        $builder = $this->db->table('purchase_requests');
        $builder->where($arrCondition);
        $query = $builder->get();

        return $query->getResultArray();
    }

    public function generateNoSpp($divisi, $companyId, $month, $year, $sppType)
    {
        $romanNumb = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        $divisi = str_replace(' ', '', $divisi);
        $monthRoman = $romanNumb[intval($month) - 1];
        $yearShort = substr($year, -2);

        $isImport = in_array($sppType, ['Import BB', 'Import BP']);

        $baseStr = $divisi . '/' . $monthRoman . '/' . $yearShort;
        $searchStr = $isImport ? '/IMP/' . $baseStr : '/' . $baseStr;

        // Ambil semua SPP aktif (belum dihapus) dengan pola yang sama
        $builder = $this->db->table('purchase_requests');
        $builder->select('spp_no');
        $builder->where('company_id', $companyId);
        $builder->where('deletedAt', null);
        $builder->like('spp_no', $searchStr);
        $builder->orderBy('spp_no', 'asc');
        $result = $builder->get()->getResultArray();

        // Kumpulkan semua nomor urut yg sudah dipakai
        $usedNumbers = [];
        foreach ($result as $row) {
            $parts = explode('/', $row['spp_no']);
            $num = intval($parts[0]);
            $usedNumbers[] = $num;
        }

        // Cari nomor terkecil yang belum dipakai
        $nextNumber = 1;
        while (in_array($nextNumber, $usedNumbers)) {
            $nextNumber++;
        }

        // Format nomor jadi 2 digit
        $increment = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        // Format akhir SPP No
        if ($isImport) {
            $generatedSppNo = $increment . '/IMP/' . $baseStr;
        } else {
            $generatedSppNo = $increment . '/' . $baseStr;
        }

        return $generatedSppNo;
    }

    public function getListSPP($company_id)
    {
        $sppResult = $this->asArray()
            ->select('purchase_requests.*,divisis.divisi')
            ->join('divisis', 'divisis.id = purchase_requests.divisi_id', 'left')
            ->where('purchase_requests.deletedAt', null)
            ->where('purchase_requests.company_id', $company_id)
            ->findAll();

        return $sppResult;
    }

    public function getListSPPLPB($company_id)
    {
        $dataSpp = $this->asArray()
            ->select('purchase_requests.*')
            ->join('am_purchase_orders', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->where('purchase_requests.company_id', $company_id)
            ->where('am_purchase_orders.is_posted', 1)
            ->where('am_purchase_orders.status_penerimaan', 0)
            ->where('purchase_requests.deletedAt', null)
            ->groupBy('purchase_requests.id')
            ->having('SUM(am_purchase_order_details.remaining_qty) > 0') // Langsung filter yang masih ada sisa qty
            ->findAll();

        return $dataSpp;
    }

    public function getListSPPLPBByDivisi($company_id, $divisi_id, $supplier_id)
    {
        $dataSpp = $this->asArray()
            ->select('purchase_requests.*')
            ->join('am_purchase_orders', 'am_purchase_orders.purchase_request_id = purchase_requests.id', 'left')
            ->join('am_purchase_order_details', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->where('purchase_requests.company_id', $company_id)
            ->where('purchase_requests.divisi_id', $divisi_id)
            ->where('am_purchase_orders.supplier_id', $supplier_id)
            ->where('am_purchase_orders.status_penerimaan', 0)
            ->where('am_purchase_orders.is_posted', 1)
            ->where('purchase_requests.deletedAt', null)
            ->groupBy('purchase_requests.id')
            ->having('SUM(am_purchase_order_details.remaining_qty) > 0') // Langsung filter yang masih ada sisa qty
            ->findAll();

        return $dataSpp;
    }
    public function getSppNotUsedForPOBp($divisiId, $companyId)
    {
        $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $sppDetailModel = new SppDetailModel();

        $selectQrySppDetail = "
        purchase_requests.id,
        purchase_requests.spp_no,
        purchase_request_details.note,
        purchase_request_details.qty,
        purchase_request_details.barang1_id,
        purchase_request_details.barang2_id
    ";

        $sppDetail = $sppDetailModel
            ->select($selectQrySppDetail)
            ->join('purchase_requests', 'purchase_requests.id = purchase_request_details.purchase_request_id', 'left')
            ->where('purchase_request_details.deletedAt', null)
            ->where('purchase_requests.deletedAt', null)
            ->where('purchase_requests.is_posted', 1)
            ->where('purchase_requests.company_id', $companyId)
            ->where('purchase_requests.spp_type', "Lokal BP")
            ->where('purchase_requests.divisi_id', $divisiId)
            ->findAll();

        $mapSppDetail = [];

        foreach ($sppDetail as $s) {
            $b = $s['barang1_id'];
            $sp = $s['barang2_id'];
            $pr = $s['id'];
            $note = trim($s['note']);

            if (!isset($mapSppDetail[$b][$sp][$pr][$note])) {
                $mapSppDetail[$b][$sp][$pr][$note] = ['qty' => 0, 'id' => $s['id']];
            }

            $mapSppDetail[$b][$sp][$pr][$note]['qty'] += $s['qty'];
        }


        $selectQryPoDetail = "
        SUM(am_purchase_order_details.qty) as total_qty,
        am_purchase_order_details.barang_id,
        am_purchase_order_details.spesifikasi_id,
        am_purchase_order_details.note,
        am_purchase_orders.purchase_request_id
    ";

        $poDetail = $amPurchaseOrderDetailModel
            ->select($selectQryPoDetail)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->where('am_purchase_orders.po_type', "Lokal")
            ->where('am_purchase_orders.company_id', $companyId)
            ->where('am_purchase_orders.deletedAt', null)
            ->where('am_purchase_order_details.deletedAt', null)
            ->groupBy('am_purchase_order_details.barang_id, am_purchase_order_details.spesifikasi_id, am_purchase_orders.purchase_request_id, am_purchase_order_details.note')
            ->findAll();


        $sppIdNotUsedFull = [];

        // --- Step 1: Cek SPP yang sudah ada di PO ---
        foreach ($poDetail as $p) {
            $barangId = $p['barang_id'];
            $spesifikasiId = $p['spesifikasi_id'];
            $purchaseRequestId = $p['purchase_request_id'];
            $note = trim($p['note']);

            if (isset($mapSppDetail[$barangId][$spesifikasiId][$purchaseRequestId][$note])) {
                $mapSppSelected = $mapSppDetail[$barangId][$spesifikasiId][$purchaseRequestId][$note];

                // Kalau qty SPP masih lebih besar dari total qty PO → masih ada sisa
                if ($mapSppSelected['qty'] > $p['total_qty']) {
                    $sppIdNotUsedFull[] = $mapSppSelected['id'];
                }

                // Kalau qty sudah habis → jangan dimasukkan
                unset($mapSppDetail[$barangId][$spesifikasiId][$purchaseRequestId][$note]);
            }
        }

        // --- Step 2: Tambahkan SPP yang belum pernah ada di PO sama sekali ---
        foreach ($mapSppDetail as $barangArr) {
            foreach ($barangArr as $spesifikasiArr) {
                foreach ($spesifikasiArr as $requestArr) {
                    foreach ($requestArr as $spp) {
                        $sppIdNotUsedFull[] = $spp['id'];
                    }
                }
            }
        }

        // --- Step 3: Ambil data final ---
        if (!empty($sppIdNotUsedFull)) {
            $dataFinal = $this->asArray()
                ->whereIn('id', $sppIdNotUsedFull)
                ->where('deletedAt', null)
                ->findAll();
        } else {
            $dataFinal = [];
        }

        return $dataFinal;
    }
}
