<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BC27Model;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\MutasiGlobalDetailModel;
use App\Models\MutasiGlobalModel;
use App\Models\PenerimaanMutasiGlobalDetailModel;
use App\Models\PenerimaanMutasiGlobalModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use Exception;
use Throwable;

class PenerimaanMutasiGlobal extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $companyModel;
    protected $divisiModel;
    protected $penerimaanMutasiGlobalModel;
    protected $penerimaanMutasiGlobalDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $mutasiGlobalModel;
    protected $mutasiGlobalDetailModel;
    protected $warehouseModel;
    protected $bc27Model;
    protected $stockRevampModel;
    protected $satuansModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->companyModel = new CompaniesModel();
        $this->divisiModel = new DivisisModel();
        $this->penerimaanMutasiGlobalModel = new PenerimaanMutasiGlobalModel();
        $this->penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->mutasiGlobalModel = new MutasiGlobalModel();
        $this->mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $this->bc27Model = new BC27Model();
        $this->warehouseModel = new WarehousesModel();
        $this->stockRevampModel = new StockRevampModel();
        $this->satuansModel = new SatuansModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
        ];

        return view('Warehouse/penerimaanMutasi/index_global', $data);
    }

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'satuan' => $this->satuansModel->where('deletedAt', null)->findAll()
        ];
        return view('Warehouse/penerimaanMutasi/form_global', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $penerimaanMutasiGlobal = $this->penerimaanMutasiGlobalModel->find($id);

        if ($penerimaanMutasiGlobal == null) {
            return redirect()->to('penerimaan-mutasi/global');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'warehouse' => $this->warehouseModel->where('divisi_id', $penerimaanMutasiGlobal['divisi_penerima_id'])->where('deletedAt', null)->findAll(),
            'penerimaanMutasiGlobal' => $penerimaanMutasiGlobal,
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'satuan' => $this->satuansModel->where('deletedAt', null)->findAll()
        ];
        return view('Warehouse/penerimaanMutasi/form_global', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"    => $this->request->getVar("length"),
            "currentPage" => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search"      => $this->request->getVar("search"),
            "sort"        => $this->request->getVar("sort"),
            "sorttype"    => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "sort"      => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "search"    => $this->request->getVar("search"),
            "dateStart" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"   => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit  = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            'penerimaan_mutasi_global.company_penerima_id' => $this->this_company_id,
            'penerimaan_mutasi_global.deletedAt' => null,
        ];

        // 1️⃣ Ambil data utama
        $dataQry = $this->penerimaanMutasiGlobalModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        if (empty($dataQry['data'])) {
            return response()->setJSON([
                "draw" => intval($this->request->getVar("draw")),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ]);
        }

        // Ambil semua ID untuk dipakai di IN clause
        $globalIds = array_column($dataQry['data'], 'id');

        // 2️⃣ Ambil semua detail sekaligus
        $details = $this->penerimaanMutasiGlobalDetailModel
            ->whereIn('penerimaan_mutasi_global_id', $globalIds)
            ->findAll();

        // Group by penerimaan_mutasi_global_id
        $detailGrouped = [];
        $mutasiIds = [];
        foreach ($details as $d) {
            $detailGrouped[$d['penerimaan_mutasi_global_id']][] = $d;
            if (!empty($d['mutasi_global_id'])) {
                $mutasiIds[] = $d['mutasi_global_id'];
            }
        }

        // 3️⃣ Ambil semua data BC27 sekaligus
        $mutasiIds = array_unique($mutasiIds);
        $bc27Data = [];
        if (!empty($mutasiIds)) {
            $bc27Rows = $this->bc27Model
                ->whereIn('mutasi_global_id', $mutasiIds)
                ->findAll();

            foreach ($bc27Rows as $bc) {
                $bc27Data[$bc['mutasi_global_id']] = $bc['no_aju'];
            }
        }

        // 4️⃣ Gabungkan hasil jadi array akhir
        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            $mutasiNameArr = [];
            if (!empty($detailGrouped[$data->id])) {
                foreach ($detailGrouped[$data->id] as $det) {
                    if (isset($bc27Data[$det['mutasi_global_id']])) {
                        $mutasiNameArr[] = $bc27Data[$det['mutasi_global_id']];
                    }
                }
            }

            $mutasiNameArr = array_unique($mutasiNameArr);

            $dataResult[] = [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_mutasi_no"  => $data->penerimaan_mutasi_no,
                "multiple_no_mutasi"    => str_replace(['"', ']', '['], " ", $data->multiple_no_mutasi),
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi_penerima"       => $data->divisi_penerima,
                "warehouse_penerima"    => $data->warehouse_penerima,
                "company_pengirim"      => $data->company_pengirim,
                "dokumen_mutasi_barang" => "BC 2.7 / " . implode(', ', $mutasiNameArr),
                "status_posting"        => $data->status_posting,
            ];
        }

        // 5️⃣ Return ke DataTables
        $data = [
            "draw"            => intval($this->request->getVar("draw")),
            "recordsTotal"    => $dataQry['totalData'],
            "recordsFiltered" => $dataQry['totalFilteredData'],
            "data"            => $dataResult,
            "payload"         => $payload,
        ];

        return response()->setJSON($data);
    }


    public function createAction()
    {
        // return response()->setJSON([
        //     '$_POST' => $_POST,
        //     'listBarang' => json_decode($_POST['listBarang'])
        // ]);

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $penerimaanMutasiNo = $this->request->getVar('penerimaan_mutasi_no');
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $barang = json_decode($_POST['listBarang']);

            $penerimaanMutasiFirst = $this->penerimaanMutasiGlobalModel
                ->where('company_penerima_id', $this->this_company_id)
                ->where('penerimaan_mutasi_no', $penerimaanMutasiNo)
                ->first();

            if ($penerimaanMutasiFirst != null) {
                $penerimaanMutasiNo = $this->get_no_str(
                    $tanggal
                );
            }

            $multipleMutasiId = $this->request->getVar('multiple_mutasi_id');
            $multipleMutasiNo = array();
            foreach (json_decode($_POST['listBarang']) as $l) {
                array_push($multipleMutasiNo, $l->no_mutasi);
            }
            $multipleMutasiId = array_values(array_unique($multipleMutasiId));
            $multipleMutasiNo = array_values(array_unique($multipleMutasiNo));

            $multipleMutasiIdStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleMutasiId));
            $multipleMutasiNoStr = json_encode($multipleMutasiNo, JSON_UNESCAPED_SLASHES);

            $id = $this->penerimaanMutasiGlobalModel->insert([
                'company_penerima_id' => $this->this_company_id,
                'company_pengirim_id' => $this->request->getVar('company_pengirim_id'),
                'divisi_penerima_id' => $this->request->getVar('divisi_penerima_id'),
                'warehouse_penerima_id' => $this->request->getVar('warehouse_penerima_id'),
                'penerimaan_mutasi_no' => $penerimaanMutasiNo,
                'multiple_mutasi_id' => $multipleMutasiIdStr,
                'multiple_no_mutasi' => $multipleMutasiNoStr,
                'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
                'keterangan' => $this->request->getVar('keterangan'),
                'status_posting' => '0',
                'createdBy' => $this->this_user_id
            ]);

            foreach ($barang as $b) {
                $this->penerimaanMutasiGlobalDetailModel->insert([
                    'penerimaan_mutasi_global_id' => $id,
                    'mutasi_global_id' => $b->mutasi_global_id,
                    'mutasi_global_detail_id' => $b->mutasi_global_detail_id,
                    'stock_detail_id' => null,
                    'spesifikasi_hasil_id' => $b->penerimaan->spesifikasi_hasil_id,
                    'unit_hasil_id' => $b->penerimaan->unit_hasil_id,
                    'qty' => $b->penerimaan->qty,
                ]);
            }
            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Penerimaan mutasi berhasil disimpan"
            ]);
        } catch (Throwable $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function updateAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $penerimaanMutasiNo = $this->request->getVar('penerimaan_mutasi_no');
            $barang = json_decode($_POST['listBarang']);

            $penerimaanMutasiFirst = $this->penerimaanMutasiGlobalModel
                ->where('company_penerima_id', $this->this_company_id)
                ->where('penerimaan_mutasi_no', $penerimaanMutasiNo)
                ->where('id !=', $id)
                ->first();

            if ($penerimaanMutasiFirst != null) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Nomor penerimaan mutasi sudah ada"
                ]);
            }

            $multipleMutasiId = $this->request->getVar('multiple_mutasi_id');
            $multipleMutasiNo = array();
            foreach (json_decode($_POST['listBarang']) as $l) {
                array_push($multipleMutasiNo, $l->no_mutasi);
            }
            $multipleMutasiId = array_values(array_unique($multipleMutasiId));
            $multipleMutasiNo = array_values(array_unique($multipleMutasiNo));

            $multipleMutasiIdStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleMutasiId));
            $multipleMutasiNoStr = json_encode($multipleMutasiNo, JSON_UNESCAPED_SLASHES);

            $this->penerimaanMutasiGlobalModel->update($id, [
                'company_penerima_id' => $this->this_company_id,
                'company_pengirim_id' => $this->request->getVar('company_pengirim_id'),
                'divisi_penerima_id' => $this->request->getVar('divisi_penerima_id'),
                'warehouse_penerima_id' => $this->request->getVar('warehouse_penerima_id'),
                'penerimaan_mutasi_no' => $penerimaanMutasiNo,
                'multiple_mutasi_id' => $multipleMutasiIdStr,
                'multiple_no_mutasi' => $multipleMutasiNoStr,
                'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
                'keterangan' => $this->request->getVar('keterangan'),
                'status_posting' => '0',
            ]);

            $this->penerimaanMutasiGlobalDetailModel->where('penerimaan_mutasi_global_id', $id)->delete();

            foreach ($barang as $b) {
                $this->penerimaanMutasiGlobalDetailModel->insert([
                    'penerimaan_mutasi_global_id' => $id,
                    'mutasi_global_id' => $b->mutasi_global_id,
                    'mutasi_global_detail_id' => $b->mutasi_global_detail_id,
                    'stock_detail_id' => null,
                    'spesifikasi_hasil_id' => $b->penerimaan->spesifikasi_hasil_id,
                    'unit_hasil_id' => $b->penerimaan->unit_hasil_id,
                    'qty' => $b->penerimaan->qty,
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Penerimaan mutasi berhasil diupdate"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->penerimaanMutasiGlobalModel->delete($id);
        $this->penerimaanMutasiGlobalDetailModel->where('penerimaan_mutasi_global_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Penerimaan mutasi berhasil dihapus"
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $selectQry = "penerimaan_mutasi_global.*, divisis.divisi AS divisi_penerima, warehouses.warehouse_name AS warehouse_penerima, companies.company AS company_pengirim";

        $penerimaanMutasiGlobal = $this->penerimaanMutasiGlobalModel->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id', 'left')
            ->join('companies', 'companies.id = penerimaan_mutasi_global.company_pengirim_id', 'left')
            ->where('penerimaan_mutasi_global.id', $id)
            ->first();

        if ($penerimaanMutasiGlobal == null) {
            return redirect()->to('penerimaan-mutasi/global');
        }

        $data = [
            'penerimaanMutasiGlobal' => $penerimaanMutasiGlobal,
            'penerimaanMutasiDetailGlobal' => $this->penerimaanMutasiGlobalModel->getListBarangMutasi(
                json_decode($penerimaanMutasiGlobal->multiple_mutasi_id),
                $id,
                true
            )
        ];

        // dd($data);

        $this->dompdf->loadHtml(view('Warehouse/penerimaanMutasi/print_global', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Penerimaan Mutasi BC 2.7", array("Attachment" => false));
    }

    public function posting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $this->penerimaanMutasiGlobalModel->posting($id, $db);
            $this->penerimaanMutasiGlobalModel->update($id, ['status_posting' => '1']);
            return response()->setJSON([
                'message' => "Penerimaan Mutasi berhasil diposting",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function dropdownListNomorMutasi()
    {
        $companyPengirimId = $this->request->getVar('company_pengirim_id');

        if (empty($companyPengirimId)) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => [],
                'status' => true
            ]);
        }

        $data = $this->penerimaanMutasiGlobalModel->getListNomorMutasi(
            $companyPengirimId
        );

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $data,
            'status' => true
        ]);
    }


    public function dropdownListBarang()
    {
        $mutasiGlobalID = json_decode($this->request->getVar('mutasi_global_id'));
        $penerimaanMutasiGlobalID = decrypt($this->request->getVar('penerimaan_mutasi_global_id'));
        $isEdit = $this->request->getVar('is_edit');

        if (empty($isEdit)) {
            $isEdit = false;
        } else {
            $isEdit = true;
        }

        if (empty($mutasiGlobalID)) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => [],
                'status' => true
            ]);
        } else {

            $data = $this->penerimaanMutasiGlobalModel->getListBarangMutasi(
                $mutasiGlobalID,
                $penerimaanMutasiGlobalID,
                $isEdit
            );

            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => $data,
                'status' => true
            ]);
        }
    }

    public function dropdownListBarangMasuk()
    {
        try {
            $search = $this->request->getVar('q');
            $companyTujuanId = $this->request->getVar('company_tujuan_id');

            $selectQry = "
                barang_master_spesifikasi.id,
                barang_master_spesifikasi.satuan_1,
                barang_master.kode_barang,
                barang_master.barang_name,
                REPLACE(REPLACE(barang_master_spesifikasi.spesifikasi, '\"', ''), \"'\", '') AS spesifikasi
            ";

            $data = $this->stockRevampModel
                ->select($selectQry)
                ->join('barang_master', 'barang_master.id = stock_revamp.barang_master_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock_revamp.spesifikasi_id', 'left')
                ->where('barang_master_spesifikasi.deletedAt', null)
                ->where('barang_master.company_id', $companyTujuanId)
                ->groupStart()
                ->like('CONCAT(barang_master.barang_name, " ", barang_master_spesifikasi.spesifikasi)', $search)
                ->orLike('barang_master.kode_barang', $search)
                ->groupEnd()
                ->orderBy('barang_master.barang_name', 'asc')
                ->findAll();

            $dataList = array();
            foreach ($data as $d) {
                array_push($dataList, [
                    'id' => $d['id'],
                    'text' => "(" . $d['kode_barang'] . ") " . trim(
                        str_replace(
                            ["\"", "\t"],
                            "'",
                            $d['barang_name'] . '- ' . $d['spesifikasi']
                        )
                    ),
                    // helper
                    'satuan_1' => $d['satuan_1'],
                    'kode_barang' => $d['kode_barang'],
                    'barang_name' => trim(
                        str_replace(
                            ["\"", "\t"],
                            "'",
                            $d['barang_name']
                        )
                    ),
                    'spesifikasi' => trim(
                        str_replace(
                            ["\"", "\t"],
                            "'",
                            $d['spesifikasi']
                        )
                    )
                ]);
            }

            return response()->setJSON([
                'status' => true,
                'data' => $dataList,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getPenerimaanMutasiNo()
    {
        $tanggal = $this->request->getVar('tanggal');

        if (empty($tanggal)) {
            return response()->setJSON([
                'status' => true,
                'data' => '',
                'token' => csrf_hash()
            ]);
        }

        $tanggal = formatDMYtoYMD($tanggal); // 2025-09-21
        $tanggalParts = explode('-', $tanggal);
        if (count($tanggalParts) !== 3) {
            return $this->response->setJSON([
                'data' => '',
                'status' => false,
                'message' => 'Format tanggal tidak valid. Gunakan dd/mm/yyyy.'
            ]);
        }

        $no = $this->get_no_str($tanggal);
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    public function get_no_str($tanggal)
    {
        $tanggalParts = explode('-', $tanggal);
        $month = $tanggalParts[1];
        $year = $tanggalParts[0];

        $no = $this->penerimaanMutasiGlobalModel->get_no(
            $month,
            $year,
            $this->this_company_id,
        );

        return $no;
    }
}
