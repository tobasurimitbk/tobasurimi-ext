<?php

namespace App\Models;

use CodeIgniter\Model;

class BC23Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_23';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

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

    public function getList($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'bc_23.bc_no_lokal'                      => 'bc_23.bc_no_lokal',
            'bc_23.createdAt'                        => 'bc_23.createdAt',
            'bc_23.no_aju'                           => 'bc_23.no_aju',
            'bc_purchase_order.po_type'              => 'bc_purchase_order.po_type',
            'bc_purchase_order.multiple_lpb_no'      => 'bc_purchase_order.multiple_lpb_no',
            'bc_purchase_order.multiple_po_no'       => 'bc_purchase_order.multiple_po_no',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'bc_purchase_order.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_23.*,
            bc_purchase_order.multiple_po_no,
            bc_purchase_order.id AS bc_purchase_order_id,
            bc_purchase_order.multiple_lpb_no,
            bc_purchase_order.po_type,
            suppliers.name AS supplier_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->whereIn('po_type', ["IMPORT BAKU", "IMPORT PENOLONG"])
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_23.bc_purchase_order_id', 'right')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusBC'] || $addCondition['statusLPB'] || $addCondition['supplierName'] || $addCondition['noPenerimaanBarang'] || $addCondition['noAju'] && (empty($addCondition['mulaiTanggalBC23']) && empty($addCondition['selesaiTanggalBC23']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusBC']) {
            if ($addCondition['statusBC'] == "Belum Lengkap") {
                $bcDataQry->where('bc_23.status_dokumen', "Belum Lengkap");
            } elseif ($addCondition['statusBC'] == "Siap Kirim") {
                $bcDataQry->where('bc_23.status_dokumen', "Siap Kirim");
            } else if ($addCondition['statusBC'] == "Sudah Kirim") {
                $bcDataQry->where('bc_23.status_dokumen', "Sudah Kirim");
            }
        }

        if ($addCondition['statusLPB']) {
            if ($addCondition['statusLPB'] != "SEMUA") {
                $bcDataQry->where('bc_purchase_order.po_type', $addCondition['statusLPB']);
            } else {
                $bcDataQry->whereIn('bc_purchase_order.po_type', ["IMPORT BAKU", "IMPORT PENOLONG"]);
            }
        }

        if ($addCondition['supplierName']) {
            $bcDataQry->like('suppliers.name', $addCondition['supplierName']);
        }

        if ($addCondition['noPenerimaanBarang']) {
            $bcDataQry->like('multiple_lpb_no', $addCondition['noPenerimaanBarang']);
        }

        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju']);
        }

        if ($addCondition['statusBC'] || $addCondition['statusLPB'] || $addCondition['supplierName'] || $addCondition['noPenerimaanBarang'] || $addCondition['noAju'] && (empty($addCondition['mulaiTanggalBC23']) && empty($addCondition['selesaiTanggalBC23']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC23'] && $addCondition['selesaiTanggalBC23']) {
            $bcDataQry->groupStart();
            $mulaiTanggalBC23Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalBC23']), "Y-m-d");
            $selesaiTanggalBC23Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalBC23']), "Y-m-d");

            if ($addCondition['mulaiTanggalBC23']) {
                $bcDataQry->where('bc_23.createdAt >=', $mulaiTanggalBC23Timestamp);
            }

            if ($addCondition['selesaiTanggalBC23']) {
                $bcDataQry->where('bc_23.createdAt <=', $selesaiTanggalBC23Timestamp);
            }

            $bcDataQry->groupEnd();
        }

        $totalFilteredData = $bcDataQry->countAllResults(false);
        $data = $bcDataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData,
        ];
    }

    public function getNo($bln, $thn, $last_day)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('bc_23');
        $builder->select('bc_no_lokal');
        $builder->orderBy('bc_no_lokal', 'DESC');
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")->where('createdAt <=', $last_day . " 23:59:59");
        $builder->where('deletedAt', null);
        $builder->like('bc_no_lokal', $lastStr);
        $query = $builder->get();

        $kode = 'TOBA/BC23';

        $lastNumber = '1';

        if (!empty($query->getResultArray())) {
            foreach ($query->getResultArray() as $string) {
                $explode = explode('/', $string['bc_no_lokal']);
                $number = intval($explode[2]);

                if ($number > $lastNumber) {
                    $lastNumber = $number;
                }
            }
            $lastNumber++;
        }

        $formattedlastNumber = sprintf("%02d", $lastNumber);
        $generatedNo = $kode . '/' . $formattedlastNumber . '/' . $lastStr;

        return $generatedNo;
    }

    // BARU
    public function get($bcPurchaseOrderID)
    {
        return $this->asArray()->where('bc_purchase_order_id', $bcPurchaseOrderID)->where('deletedAt', null)->first();
    }

    public function isCompleteFormHeader($bcPurchaseOrderID)
    {
        $isCompleteForm = false;
        $data = $this->get($bcPurchaseOrderID);
        if ($data == null) {
            $isCompleteForm = false;
        } else {
            if ($data['no_aju'] != null && $data['kode_pelabuhan_bongkar'] != null && $data['kode_kantor_bongkar'] != null && $data['kode_kantor'] != null && $data['kode_tujuan_tpb'] != null) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPernyataan($bcPurchaseOrderID)
    {
        $isCompleteForm = false;
        $data = $this->get($bcPurchaseOrderID);
        if ($data == null) {
            $isCompleteForm = false;
        } else {
            if ($data['nama_ttd'] != null && $data['kota_ttd'] != null && $data['tanggal_ttd'] != null && $data['jabatan_pengusaha_ttd'] != null) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormEntitas($bcPurchaseOrderID)
    {
        $bcEntitasModel = new BCEntitasModel();
        return $bcEntitasModel->get($bcPurchaseOrderID) == null ? false : true;
    }

    public function isCompleteFormDokumen($bcPurchaseOrderID)
    {
        $bcDokumenModel = new BCDokumenModel();
        return $bcDokumenModel->get($bcPurchaseOrderID) == null ? false : true;
    }

    public function isCompleteFormPengangkut($bcPurchaseOrderID)
    {
        $bc23PengangkutModel = new BCPengangkutModel();
        return $bc23PengangkutModel->get($bcPurchaseOrderID) == null ? false : true;
    }

    public function isCompleteFormPetiKemas($bcPurchaseOrderID)
    {
        $bcKontainerModel = new BCKontainerModel();
        $bcKemasanModel = new BCKemasanModel();

        $kontainer = $bcKontainerModel->getLast($bcPurchaseOrderID) != null ? true : false;
        $kemasan = $bcKemasanModel->getLast($bcPurchaseOrderID) != null ? true : false;

        return $kontainer && $kemasan;
    }

    public function isCompleteFormTransaksi($bcPurchaseOrderID)
    {
        $data = $this->get($bcPurchaseOrderID);
        if ($data == null) {
            return false;
        } else {
            return ($data['kode_valuta'] != null && $data['kode_incoterm'] != null && $data['kode_asuransi'] != null && $data['kode_kena_pajak'] != null) ? true : false;
        }
    }

    public function isCompleteFormBarang($bcPurchaseOrderID)
    {
        $bcPurchaseOrderModel = new BCPurchaseOrderModel();
        $bcBarangModel = new BCBarangModel();

        $lpbDetail = $bcPurchaseOrderModel->findDetailBarang($bcPurchaseOrderID);

        $totalPerluDiisi = count($lpbDetail);
        $totalSudahDiisi = 0;
        foreach ($lpbDetail as $l) {
            $bcDokumenBarang =  $bcBarangModel->where('penerimaan_barang_id', $l['penerimaan_barang_id'])->where('barang1_id', $l['barang1_id'])->first();
            if ($bcDokumenBarang != null) {
                $totalSudahDiisi++;
            }
        }

        return $totalSudahDiisi == $totalPerluDiisi ? true : false;
    }

    public function isCompleteFormPungutan($bcPurchaseOrderID)
    {
        $bcBarangTarifModel = new BCBarangTarifModel();
        $barangTarif = $bcBarangTarifModel
            ->where('bc_purchase_order_id', $bcPurchaseOrderID)
            ->findAll();

        return count($barangTarif) == 0 ? false : true;
    }
}
