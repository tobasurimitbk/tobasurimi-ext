<?php

namespace App\Models;

use CodeIgniter\Model;

class BC40Model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'bc_40';
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
            'bc_40.bc_no_lokal'                      => 'bc_40.bc_no_lokal',
            'bc_40.createdAt'                        => 'bc_40.createdAt',
            'bc_40.no_aju'                           => 'bc_40.no_aju',
            'penerimaan_barang.no_penerimaan_barang' => 'penerimaan_barang.no_penerimaan_barang',
            'penerimaan_barang.warehouse_id'         => 'penerimaan_barang.warehouse_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_40.*,
            penerimaan_barang.no_penerimaan_barang,
            penerimaan_barang.id AS penerimaan_barang_id,
            penerimaan_barang.status_penerimaan,
            penerimaan_barang.tipe_bahan,
            warehouses.warehouse_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('penerimaan_barang', 'penerimaan_barang.id = bc_40.penerimaan_barang_id', 'right')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusBC'] || $addCondition['statusLPB'] || $addCondition['noBC40'] || $addCondition['noPenerimaanBarang'] || $addCondition['noAju'] && (empty($addCondition['mulaiTanggalBC40']) && empty($addCondition['selesaiTanggalBC40']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusBC']) {
            if ($addCondition['statusBC'] == "Belum Dibuat") {
                $bcDataQry->where('bc_40.id IS NULL');
            } elseif ($addCondition['statusBC'] == "Belum Lengkap") {
                $bcDataQry->where('bc_40.status_dokumen', "Belum Lengkap");
            } elseif ($addCondition['statusBC'] == "Siap Kirim") {
                $bcDataQry->where('bc_40.status_dokumen', "Siap Kirim");
            } else {
                $bcDataQry->where('bc_40.status_dokumen', "Sudah Kirim");
            }
        }

        if ($addCondition['statusLPB']) {
            if ($addCondition['statusLPB'] == "LOKAL BAKU") {
                $statusLPB = explode(' ', "LOKAL BAKU");
                $bcDataQry->where('status_penerimaan', $statusLPB[0]);
                $bcDataQry->where('tipe_bahan', $statusLPB[1]);
            } elseif ($addCondition['statusLPB'] == "LOKAL PENOLONG") {
                $statusLPB = explode(' ', "LOKAL PENOLONG");
                $bcDataQry->where('status_penerimaan', $statusLPB[0]);
                $bcDataQry->where('tipe_bahan', $statusLPB[1]);
            } elseif ($addCondition['statusLPB'] == "IMPORT BAKU") {
                $statusLPB = explode(' ', "IMPORT BAKU");
                $bcDataQry->where('status_penerimaan', $statusLPB[0]);
                $bcDataQry->where('tipe_bahan', $statusLPB[1]);
            } elseif ($addCondition['statusLPB'] == "IMPORT PENOLONG") {
                $statusLPB = explode(' ', "IMPORT PENOLONG");
                $bcDataQry->where('status_penerimaan', $statusLPB[0]);
                $bcDataQry->where('tipe_bahan', $statusLPB[1]);
            }
        }

        if ($addCondition['noBC40']) {
            $bcDataQry->like('bc_no_lokal', $addCondition['noBC40']);
        }

        if ($addCondition['noPenerimaanBarang']) {
            $bcDataQry->like('no_penerimaan_barang', $addCondition['noPenerimaanBarang']);
        }

        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju']);
        }

        if ($addCondition['statusBC'] || $addCondition['statusLPB'] || $addCondition['noBC40'] || $addCondition['noPenerimaanBarang'] || $addCondition['noAju'] && (empty($addCondition['mulaiTanggalBC40']) && empty($addCondition['selesaiTanggalBC40']))) {
            $bcDataQry->groupEnd();
        }

        if ($addCondition['mulaiTanggalBC40'] && $addCondition['selesaiTanggalBC40']) {
            $bcDataQry->groupStart();
            $mulaiTanggalBC40Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['mulaiTanggalBC40']), "Y-m-d");
            $selesaiTanggalBC40Timestamp = date_format(date_create_from_format("d/m/Y", $addCondition['selesaiTanggalBC40']), "Y-m-d");

            if ($addCondition['mulaiTanggalBC40']) {
                $bcDataQry->where('bc_40.createdAt >=', $mulaiTanggalBC40Timestamp);
            }

            if ($addCondition['selesaiTanggalBC40']) {
                $bcDataQry->where('bc_40.createdAt <=', $selesaiTanggalBC40Timestamp);
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

    public function get($penerimaanBarangID)
    {
        return $this->asArray()->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->first();
    }

    public function getNo($bln, $thn, $last_day)
    {
        $lastStr =  convertBulanToAngkaRomawi($bln) . '/' . $thn;

        $builder = $this->db->table('bc_40');
        $builder->select('bc_no_lokal');
        $builder->orderBy('bc_no_lokal', 'DESC');
        $builder->where('createdAt >=', $thn . "-" . $bln . "-01" . " 00:00:00")->where('createdAt <=', $last_day . " 23:59:59");
        $builder->where('deletedAt', null);
        $builder->like('bc_no_lokal', $lastStr);
        $query = $builder->get();

        $kode = 'TOBA/BC40';

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

    public function isCompleteFormHeader($penerimaanBarangID)
    {
        $isCompleteForm = false;
        $data = $this->get($penerimaanBarangID);
        if ($data == null) {
            $isCompleteForm = false;
        } else {
            if ($data['no_aju'] != null && $data['kode_kantor'] != null && $data['kode_jenis_tpb'] != null && $data['kode_tujuan_pengiriman'] != null) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormPernyataan($penerimaanBarangID)
    {
        $isCompleteForm = false;
        $data = $this->get($penerimaanBarangID);
        if ($data == null) {
            $isCompleteForm = false;
        } else {
            if ($data['nama_ttd'] != null && $data['kota_ttd'] != null && $data['tanggal_ttd'] != null && $data['jabatan_ttd'] != null) {
                $isCompleteForm = true;
            } else {
                $isCompleteForm = false;
            }
        }
        return $isCompleteForm;
    }

    public function isCompleteFormEntitas($penerimaanBarangID)
    {
        $bcEntitasModel = new BCEntitasModel();
        return $bcEntitasModel->get($penerimaanBarangID) == null ? false : true;
    }

    public function isCompleteFormDokumen($penerimaanBarangID)
    {
        $bcDokumenModel = new BCDokumenModel();
        return $bcDokumenModel->get($penerimaanBarangID) == null ? false : true;
    }

    public function isCompleteFormPengangkut($penerimaanBarangID)
    {
        $bc23PengangkutModel = new BCPengangkutModel();
        return $bc23PengangkutModel->get($penerimaanBarangID) == null ? false : true;
    }

    public function isCompleteFormPetiKemas($penerimaanBarangID)
    {
        $bcKemasanModel = new BCKemasanModel();

        $kemasan = $bcKemasanModel->getLast($penerimaanBarangID) != null ? true : false;

        return $kemasan;
    }

    public function isCompleteFormTransaksi($penerimaanBarangID)
    {
        $data = $this->get($penerimaanBarangID);
        if ($data == null) {
            return false;
        } else {
            return ($data['nilai_jasa'] != null && $data['harga_perolehan'] != null && $data['volume'] != null && $data['bruto'] != null) ? true : false;
        }
    }

    public function isCompleteFormBarang($penerimaanBarangID)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $bcBarangModel = new BCBarangModel();

        $lpb = $penerimaanBarangModel->getById($penerimaanBarangID);
        $lpbDetail = $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($penerimaanBarangID, $lpb->tipe_bahan, $lpb->status_penerimaan);

        $totalPerluDiisi = count($lpbDetail);
        $totalSudahDiisi = 0;
        foreach ($lpbDetail as $l) {
            $bcDokumenBarang =  $bcBarangModel->where('penerimaan_barang_id', $lpb->id)->where('penerimaan_barang_detail_id', $l['penerimaan_barang_detail_id'])->first();
            if ($bcDokumenBarang != null) {
                $totalSudahDiisi++;
            }
        }

        return $totalSudahDiisi == $totalPerluDiisi ? true : false;
    }

    public function isCompleteFormPungutan($penerimaanBarangID)
    {
        $bcBarangTarifModel = new BCBarangTarifModel();
        $barangTarif = $bcBarangTarifModel
            ->where('penerimaan_barang_id', $penerimaanBarangID)
            ->findAll();

        return count($barangTarif) == 0 ? false : true;
    }
}
