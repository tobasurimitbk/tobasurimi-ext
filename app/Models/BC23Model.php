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
            'penerimaan_barang.no_penerimaan_barang' => 'penerimaan_barang.no_penerimaan_barang',
            'penerimaan_barang.warehouse_id'         => 'penerimaan_barang.warehouse_id',
        ];
        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'updatedAt'] ?? 'penerimaan_barang.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = "bc_23.*,
            penerimaan_barang.no_penerimaan_barang,
            penerimaan_barang.id AS penerimaan_barang_id,
            penerimaan_barang.status_penerimaan,
            penerimaan_barang.tipe_bahan,
            warehouses.warehouse_name";

        $bcDataQry = $this->asObject()
            ->select($selectQry)
            ->where($condition)
            ->join('penerimaan_barang', 'penerimaan_barang.id = bc_23.penerimaan_barang_id', 'right')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->orderBy($sort, $sortType);

        $totalData = $bcDataQry->countAllResults(false);

        if ($addCondition['statusBC'] || $addCondition['statusLPB'] || $addCondition['noBC23'] || $addCondition['noPenerimaanBarang'] || $addCondition['noAju'] && (empty($addCondition['mulaiTanggalBC23']) && empty($addCondition['selesaiTanggalBC23']))) {
            $bcDataQry->groupStart();
        }

        if ($addCondition['statusBC']) {
            if ($addCondition['statusBC'] == "Belum Dibuat") {
                $bcDataQry->where('bc_23.id IS NULL');
            } elseif ($addCondition['statusBC'] == "Belum Posting") {
                $bcDataQry->where('bc_23.status_posting', "Belum Posting");
            } else {
                $bcDataQry->where('bc_23.status_posting', "Sudah Posting");
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

        if ($addCondition['noBC23']) {
            $bcDataQry->like('bc_no_lokal', $addCondition['noBC23']);
        }

        if ($addCondition['noPenerimaanBarang']) {
            $bcDataQry->like('no_penerimaan_barang', $addCondition['noPenerimaanBarang']);
        }

        if ($addCondition['noAju']) {
            $bcDataQry->like('no_aju', $addCondition['noAju']);
        }

        if ($addCondition['statusBC'] || $addCondition['statusLPB'] || $addCondition['noBC23'] || $addCondition['noPenerimaanBarang'] || $addCondition['noAju'] && (empty($addCondition['mulaiTanggalBC23']) && empty($addCondition['selesaiTanggalBC23']))) {
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

    public function writeJsonUpdate($id)
    {
        $bc23BarangModel = new BC23BarangModel();
        $bc23BarangDokumenModel = new BC23BarangDokumenModel();
        $bc23BarangTarifModel = new BC23BarangTarifModel();
        $bc23DokumenModel = new BC23DokumenModel();
        $bc23EntitasModel = new BC23EntitasModel();
        $bc23KemasanModel = new BC23KemasanModel();
        $bc23KontainerModel = new BC23KontainerModel();
        $bc23PengangkutModel = new BC23PengangkutModel();
        // helper
        $barangMasterModel = new BarangMasterModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        // Get Barang Dok
        $detailBarangDok = [];
        foreach ($bc23BarangModel->get($id) as $b) {
            // Get Kode Barang
            $penerimaanBarangDetail = $penerimaanBarangDetailModel
                ->where('id', $b['penerimaan_barang_detail_id'])
                ->first();
            $barangDetail = $barangMasterModel
                ->where('id', $penerimaanBarangDetail == null ? 0 : $penerimaanBarangDetail['barang_id'])
                ->first();
            // Res
            $detailBarangDok[] = [
                'penerimaan_barang_detail_id' => encrypt($b['penerimaan_barang_detail_id']),
                'asuransi' => $b['asuransi'],
                'cif_rupiah' => $b['cif_rupiah'],
                'diskon' => $b['diskon'],
                'fob' => $b['fob'],
                'freight' => $b['freight'],
                'harga_cif' => $b['harga_cif'],
                'harga_ekspor' => $b['harga_ekspor'],
                'harga_penyerahan_barang' => $b['harga_penyerahan_barang'],
                'harga_perolehan_barang' => $b['harga_perolehan_barang'],
                'harga_satuan_barang' => $b['harga_satuan_barang'],
                'isi_per_kemasan' => $b['isi_per_kemasan'],
                'jumlah_kemasan' => $b['jumlah_kemasan'],
                'jumlah_satuan' => $b['jumlah_satuan'],
                'kode_asal_bahan_baku' => encrypt($b['kode_asal_bahan_baku']),
                'kode_barang' => $barangDetail == null ? "-" : $barangDetail['kode_barang'],
                'kode_dokumen' => encrypt($b['kode_dokumen']),
                'kode_jenis_kemasan' => encrypt($b['kode_jenis_kemasan']),
                'kode_kategori_barang' => encrypt($b['kode_kategori_barang']),
                'kode_negara_asal' => encrypt($b['kode_negara_asal']),
                'kode_perhitungan' => encrypt($b['kode_perhitungan']),
                'kode_satuan_barang' => encrypt($b['kode_satuan_barang']),
                'merk_barang' => $b['merk_barang'],
                'ndpm' => $b['ndpm'],
                'netto' => $b['netto'],
                'nilai_barang' => $b['nilai_barang'],
                'nilai_tambah' => $b['nilai_tambah'],
                'pos_tarif' => $b['pos_tarif'],
                'seri_barang' => $b['seri_barang'],
                'spesifikasi_lain' => $b['spesifikasi_lain'],
                'tipe_barang' => $b['tipe_barang'],
                'ukuran_barang' => $b['ukuran_barang'],
                'uraian' => $b['uraian'],
                'barangDokumen' => $bc23BarangDokumenModel->get($b['id']),
                'barangTarif' => $bc23BarangTarifModel->get($b['id']),
            ];
        }

        return [
            'detailBarangDok' => $detailBarangDok,
            'dokumen' => $bc23DokumenModel->get($id),
            'entitas' => $bc23EntitasModel->get($id),
            'kemasan' => $bc23KemasanModel->get($id),
            'kontainer' => $bc23KontainerModel->get($id),
            'pengangkut' => $bc23PengangkutModel->get($id)
        ];
    }

    // BARU
    public function get($penerimaanBarangID)
    {
        return $this->asArray()->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->first();
    }

    public function isCompleteFormHeader($penerimaanBarangID)
    {
        $isCompleteForm = false;
        $data = $this->get($penerimaanBarangID);
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

    public function isCompleteFormPernyataan($penerimaanBarangID)
    {
        $isCompleteForm = false;
        $data = $this->get($penerimaanBarangID);
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

    public function isCompleteFormEntitas($penerimaanBarangID)
    {
        $bc23EntitasModel = new BC23EntitasModel();
        $isCompleteForm = false;
        $data = $bc23EntitasModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->first();
        if ($data == null) {
            $isCompleteForm = false;
        } else {
            $isCompleteForm = true;
        }
        return $isCompleteForm;
    }

    public function isCompleteFormDokumen($penerimaanBarangID)
    {
        $bc23DokumenModel = new BC23DokumenModel();
        $isCompleteForm = false;
        $data = $bc23DokumenModel->where('penerimaan_barang_id', $penerimaanBarangID)->where('deletedAt', null)->first();
        if ($data == null) {
            $isCompleteForm = false;
        } else {
            $isCompleteForm = true;
        }
        return $isCompleteForm;
    }
}
