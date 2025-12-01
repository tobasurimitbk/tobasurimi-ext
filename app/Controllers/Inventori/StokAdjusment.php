<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\AdjusmentDetailModel;
use App\Models\AdjusmentModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use App\Models\WarehousesModel;
use App\Models\BC23Model;
use App\Models\BC27Model;
use App\Models\BC40Model;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PPBKBModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampHistoryModel;
use App\Models\StockRevampLogModel;
use App\Models\StockRevampModel;
use Exception;

class StokAdjusment extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $adjusmentModel;
    protected $adjusmentDetailModel;
    protected $warehouseModel;
    protected $userModel;
    protected $barangMaster;
    protected $barangMasterSpesifikasiModel;
    protected $kemasanModel;
    protected $satuanModel;
    protected $supplierModel;
    protected $bc40Model;
    protected $bc23Model;
    protected $bc27Model;
    protected $ppbkbModel;
    protected $stockRevampModel;
    protected $barangMasterModel;
    protected $stockRevampDetailModel;
    protected $stockRevampLogModel;
    protected $stockRevampHistoryModel;
    protected $penerimaanBarangDetailModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->stockModel = new StockModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->stockDetailModel = new StockDetailModel();
        $this->adjusmentModel = new AdjusmentModel();
        $this->adjusmentDetailModel = new AdjusmentDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->userModel = new UserModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->satuanModel = new SatuansModel();
        $this->kemasanModel = new KemasanModel();
        $this->barangMaster = new BarangMasterModel();
        $this->supplierModel = new SupplierModel();
        $this->bc40Model = new BC40Model();
        $this->bc27Model = new BC27Model();
        $this->bc23Model = new BC23Model();
        $this->ppbkbModel = new PPBKBModel();
        $this->stockRevampModel = new StockRevampModel();
        $this->stockRevampDetailModel = new StockRevampDetailModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->stockRevampLogModel = new StockRevampLogModel();
        $this->stockRevampHistoryModel = new StockRevampHistoryModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
    }

    public function index()
    {
        $tipeBarang = $this->metaDataModel->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('value !=', "kemasan")
            ->findAll();
        $dataTipeAdjusment = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Tipe Adjusment")
            ->findAll();

        $data = [
            'tipeBarang' => $tipeBarang,
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'tipeAdjusment' => $dataTipeAdjusment,

        ];

        return view('Warehouse/stockAdjusment/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "search" => $this->request->getVar("search"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "tipe_adjusment" => $this->request->getVar("tipe_adjusment"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");


        $condition = [
            'adjusment.company_id' => $this->this_company_id,
            'adjusment.deletedAt' => null
        ];


        $dataQry = $this->adjusmentModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );
        $dataResult = array();
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($dataQry['data'] as $data) {
            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data['id']),
                "divisi"                => $data['divisi'],
                "no_adjusment"          => $data['no_adjusment'],
                "tanggal"               => date('d/m/Y', strtotime($data['tanggal'])),
                "keterangan"            => $data['keterangan'],
                "tipe_adjusment"        => $data['tipe_adjusment_text'],
                "created_by"            => $data['user_name'],
                "status_posting"        => $data['status_posting'],
                "divisi"                => $data['divisi'],
                "jenis_adjusment"       => $data['jenis_adjusment']
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create()
    {
        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description !=', "kemasan")
            ->findAll();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataTipeAdjusment = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Tipe Adjusment")
            ->findAll();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();

        $data = [
            'tipeBarang' => $dataTipeBarang,
            'divisi' => $dataDivisi,
            'tipeAdjusment' => $dataTipeAdjusment,
            'dataSatuan' => $dataSatuan,
            'tanggal' => date('Y-m-d'),
        ];

        return view('Warehouse/stockAdjusment/form', $data);
    }

    public function allStockList()
    {
        $draw = $this->request->getGet('draw');
        $start = (int)$this->request->getGet('start');
        $length = (int)$this->request->getGet('length');
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'asc';
        $orderColumnIndex = $this->request->getGet('order')[0]['column'] ?? null;

        $dateStart = $this->request->getVar("dateStart")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
            : null;

        $dateEnd = $this->request->getVar("dateEnd")
            ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
            : null;

        $divisiId = $this->request->getGet('divisi_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        $typeBarang = $this->request->getGet('type_barang');
        $spesifikasiId = $this->request->getGet('spesifikasi_id');
        $barangId = $this->request->getGet('barang_id');
        $search = $this->request->getGet('search');

        $condition = [
            'company_id'        => $this->this_company_id,
            'dateStart'         => $dateStart,
            'dateEnd'           => $dateEnd,
            'type_barang'       => $typeBarang,
            'divisi_id'         => $divisiId,
            'warehouse_id'      => $warehouseId,
            'spesifikasi_id'    => $spesifikasiId,
            'barang_id'         => $barangId,
            'search'            => $search
        ];

        if (empty($condition['divisi_id']) || empty($condition['type_barang'])) {
            return response()->setJSON([
                'draw' => intval($draw),
                'recordsTotal' => intval($data['totalData'] ?? 0),
                'recordsFiltered' => intval($data['totalFilteredData'] ?? 0),
                'data' => [],
            ]);
        }

        $data = $this->stockRevampModel->getStockListAll(
            $condition,
            $orderColumnIndex,
            $orderDir,
            $length,
            $start
        );

        $mapKeterangan = $this->getMapKeteranganPoLokalBp(
            $condition['dateStart'],
            $condition['dateEnd']
        );

        $dataResult = array();
        $no = $start + 1;
        foreach ($data['data'] as $d) {
            $bc_all = "";
            $keterangan = "";

            if ($d['type_bc'] != "NON PABEAN" && $d['no_daftar'] != "") {
                // ADA DOKUMEN BEA CUKAI
                $bc_all = $d['type_bc'] . " / " . $d['no_daftar'] . " / " . $d['no_aju'];
            } else {
                $bc_all = $d['type_bc'];
            }
            if ($d['po_type'] == "LOKAL PENOLONG") {
                $keterangan = $mapKeterangan[$d['po_id']][$d['spesifikasi_id']];
            }
            array_push($dataResult, [
                'no' => $no++,
                'id' => $d['id'],
                'divisi' => $d['divisi'],
                'warehouse_name' => $d['warehouse_name'],
                'spp_no' => $d['spp_no'],
                'reference_type' => $d['reference_type'],
                'supplier_name' => $d['supplier_name'],
                'kode_barang' => $d['kode_barang'],
                'barang_name' => $d['barang_name'],
                'spesifikasi' => $d['spesifikasi'],
                'type_bc' => $d['type_bc'],
                'bc_detail' => $bc_all,
                'po_no' => $d['po_no'],
                'po_date' => !empty($d['po_date']) && $d['po_date'] != null ? date('d/m/Y', strtotime($d['po_date'])) : "",
                'lpb_date' => !empty($d['lpb_date']) && $d['lpb_date'] != null ? date('d/m/Y', strtotime($d['lpb_date'])) : "",
                'reference_no' => $d['reference_no'],
                'qty_diterima' => (float)$d['qty_diterima'],
                'qty_bersih' => (float)$d['qty_bersih'],
                'kode_satuan' => $d['kode_satuan'],
                "unit_id"               => $d['unit_id'],
                "keterangan" => $keterangan,
            ]);
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => intval($data['totalData'] ?? 0),
            'recordsFiltered' => intval($data['totalFilteredData'] ?? 0),
            'data' => $dataResult,
        ]);
    }

    public function dropdownBarangInventori()
    {
        try {
            $typeBarang = $this->request->getVar('type_barang');

            if (empty($typeBarang)) {
                return response()->setJSON(['data' => []]);
            }

            $search = $this->request->getVar('q');
            $data = $this->barangMasterModel->dropdownBarangStock(
                $typeBarang,
                $this->this_company_id,
                $search
            );

            $dataList = array();
            foreach ($data as $d) {
                array_push($dataList, [
                    'id' => $d['spesifikasi_id'],
                    'text' => "(" . $d['kode_barang'] . ") " . trim(
                        str_replace(
                            ["\"", "\t"],
                            "'",
                            $d['barang_name'] . '- ' . $d['spesifikasi']
                        )
                    )
                ]);
            }

            return response()->setJSON([
                'results' => $dataList,
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return  response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function dropdownBarangMaster()
    {
        try {
            $typeBarang = $this->request->getVar('type_barang');
            $search = $this->request->getVar('q');

            if (empty($typeBarang)) {
                return response()->setJSON(['data' => []]);
            }
            $dataBarang = $this->barangMasterModel->dropdownBarangMaster(
                $typeBarang,
                $this->this_company_id,
                $search
            );
            $dataList = array();
            foreach ($dataBarang as $d) {
                array_push($dataList, [
                    'id' => $d['id'],
                    'text' => "(" . $d['kode_barang'] . ") " . trim(
                        str_replace(
                            ["\"", "\t"],
                            "'",
                            $d['barang_name']
                        )
                    ),
                ]);
            }

            return response()->setJSON([
                'results' => $dataList,
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return  response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function dropdownBarangStockList()
    {
        try {
            $divisiId = $this->request->getVar('divisi_id');
            $warehouseId = $this->request->getVar('warehouse_id');

            if (empty($divisiId) || empty($warehouseId)) {
                return response()->setJSON(['results' => []]);
            }

            $search = $this->request->getVar('q');
            $data = $this->stockRevampModel->getStockListSearchBarang(
                $this->this_company_id,
                $divisiId,
                $warehouseId,
                $search
            );

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
                    'unit_id' => $d['unit_id'],
                    'kode_barang' => $d['kode_barang'],
                    'barang_name' => str_replace(["\"", "\t"], "'", $d['barang_name']),
                    'spesifikasi' => str_replace(["\"", "\t"], "'", $d['spesifikasi']),
                ]);
            }

            return response()->setJSON([
                'results' => $dataList,
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return  response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getSatuanKonversi()
    {
        try {
            $stockDetailId = $this->request->getVar('id');
            $dataSatuanIdMap = array();
            $dataSatuanIdArr = array();
            $dataSatuanKonversi = array();
            $dataResult = $this->stockRevampDetailModel->getStockIdentity(
                $stockDetailId
            );

            // Map
            $dataSatuanIdMap[$dataResult['satuan_1']] = 1;
            $dataSatuanIdMap[$dataResult['satuan_2']] = $dataResult['konversi_satuan_2'];
            $dataSatuanIdMap[$dataResult['satuan_3']] = $dataResult['konversi_satuan_3'];
            // Satuan Id Arr
            $dataSatuanIdArr = [
                $dataResult['satuan_1'],
                $dataResult['satuan_2'],
                $dataResult['satuan_3']
            ];

            $dataSatuan = $this->satuanModel->whereIn('id', $dataSatuanIdArr)->findAll();
            foreach ($dataSatuan as $d) {
                array_push($dataSatuanKonversi, [
                    'id' => $d['id'],
                    'kode_satuan' => $d['kode_satuan'],
                    'konversi_satuan' => (float)$dataSatuanIdMap[$d['id']]
                ]);
            }

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'data' => $dataSatuanKonversi
            ]);
        } catch (Exception $e) {
            return  response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $dataTipeBarang = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Kategori Barang")
            ->where('description !=', "kemasan")
            ->findAll();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataTipeAdjusment = $this->metaDataModel
            ->where('deletedAt', null)
            ->where('name', "Tipe Adjusment")
            ->findAll();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataListBarang = $this->adjusmentDetailModel->getDetail($id);
        $adjusment = $this->adjusmentModel->where('id', $id)->first();

        $data = [
            'tipeBarang' => $dataTipeBarang,
            'divisi' => $dataDivisi,
            'tipeAdjusment' => $dataTipeAdjusment,
            'dataSatuan' => $dataSatuan,
            'dataListBarang' => $dataListBarang,
            'adjusment' => $adjusment
        ];

        if ($data['adjusment'] == null) {
            return redirect()->to('stock-adjusment');
        }

        return view('Warehouse/stockAdjusment/form', $data);
    }

    public function createAction()
    {
        // return response()->setJSON([
        //     'listBarang' => json_decode($_POST['listBarang']),
        //     '$_POST' => $_POST,
        //     'token' => csrf_hash()
        // ]);

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $noAdjusment =  $this->request->getVar('no_adjusment');
            $first = $this->adjusmentModel
                ->where('company_id', $this->this_company_id)
                ->where('no_adjusment', $noAdjusment)
                ->first();
            $jenisAdjusment = $this->request->getVar('jenis_adjusment');

            if ($first != null) {
                // sudah ada generate ulang
                $noAdjusment = $this->get_no_str($tanggal);
            }

            $id = $this->adjusmentModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'warehouse_id' => $jenisAdjusment == "UPDATE" ? null : $this->request->getVar('warehouse_id'),
                'no_adjusment' => $this->request->getVar('no_adjusment'),
                'tanggal' => $tanggal,
                'tipe_adjusment' => $this->request->getVar('tipe_adjusment'),
                'keterangan' => $this->request->getVar('keterangan') ?? null,
                'status_posting' => 0,
                'createdBy' => $this->this_user_id,
                'jenis_adjusment' => $jenisAdjusment
            ]);

            foreach (json_decode($_POST['listBarang']) as $l) {

                if ($jenisAdjusment == "UPDATE") {
                    $this->adjusmentDetailModel->insert([
                        'adjusment_id' => $id,
                        'stock_detail_id' => $l->id,
                        'qty_asal' => $l->qty_diterima,
                        'operasi_adjusment_detail' => $l->adjusment->operasi_adjusment_detail,
                        'qty_adjusment' => $l->adjusment->qty_adjusment,
                        'unit_id_adjusment' => $l->adjusment->unit_id_adjusment,
                        'qty_konversi' => $l->adjusment->qty_konversi,
                        'unit_id_konversi' => $l->adjusment->unit_id_konversi,
                        'hasil_adjusment' => $l->adjusment->hasil_adjusment
                    ]);
                } else {
                    $this->adjusmentDetailModel->insert([
                        'adjusment_id' => $id,
                        'stock_id' => $l->stock_id,
                        'operasi_adjusment_detail' => "PLUS",
                        'unit_id_adjusment' => $l->satuan_id,
                        'qty_adjusment' => $l->qty_adjusment,
                    ]);
                }
            }

            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Adjusment berhasil disimpan",
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

    public function updateAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $noAdjusment =  $this->request->getVar('no_adjusment');
            $first = $this->adjusmentModel
                ->where('company_id', $this->this_company_id)
                ->where('no_adjusment', $noAdjusment)
                ->where('id !=', $id)
                ->first();
            $jenisAdjusment = $this->request->getVar('jenis_adjusment');

            if ($first != null) {
                return response()->setJSON([
                    'message' => "No adjusment sudah ada",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }

            $this->adjusmentModel->update($id, [
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'no_adjusment' => $this->request->getVar('no_adjusment'),
                'warehouse_id' => $jenisAdjusment == "UPDATE" ? null : $this->request->getVar('warehouse_id'),
                'tanggal' => $tanggal,
                'tipe_adjusment' => $this->request->getVar('tipe_adjusment'),
                'keterangan' => $this->request->getVar('keterangan') ?? null,
                'status_posting' => 0,
                'jenis_adjusment' => $jenisAdjusment
            ]);

            $this->adjusmentDetailModel->where('adjusment_id', $id)->delete(null, false);

            foreach (json_decode($_POST['listBarang']) as $l) {
                // var_dump($l->adjusment);
                // die;
                if ($jenisAdjusment == "UPDATE") {
                    $this->adjusmentDetailModel->insert([
                        'adjusment_id' => $id,
                        'stock_detail_id' => $l->id,
                        'qty_asal' => $l->qty_diterima,
                        'operasi_adjusment_detail' => $l->adjusment->operasi_adjusment_detail,
                        'qty_adjusment' => $l->adjusment->qty_adjusment,
                        'unit_id_adjusment' => $l->adjusment->unit_id_adjusment,
                        'qty_konversi' => $l->adjusment->qty_konversi,
                        'unit_id_konversi' => $l->adjusment->unit_id_konversi,
                        'hasil_adjusment' => $l->adjusment->hasil_adjusment
                    ]);
                } else {
                    $this->adjusmentDetailModel->insert([
                        'adjusment_id' => $id,
                        'stock_id' => $l->stock_id,
                        'operasi_adjusment_detail' => "PLUS",
                        'unit_id_adjusment' => $l->satuan_id,
                        'qty_adjusment' => $l->qty_adjusment,
                    ]);
                }
            }


            $db->transCommit();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => "Adjusment berhasil diupdate",
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

    public function delete()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $this->adjusmentModel->delete($id);
            $this->adjusmentDetailModel->where('adjusment_id', $id)->delete();
            $db->transCommit();
            return response()->setJSON([
                "status" => true,
                "message" => "Adjusment berhasil dihapus",
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

    public function posting()
    {

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $adjusmentList = $this->adjusmentDetailModel->where('adjusment_id', $id)->where('deletedAt', null)->findAll();
            $adjusment = $this->adjusmentModel->where('id', $id)->first();

            foreach ($adjusmentList as $a) {
                // Update
                $stockDetail = $this->stockRevampDetailModel
                    ->select('stock_revamp_detail.*')
                    ->where('stock_revamp_detail.id', $a['stock_detail_id'])
                    ->first();

                $stock = $this->stockRevampModel->where('id', $stockDetail['stock_id'])->first();
                $qtyTotal = $this->getTotalStockParent($stockDetail['stock_id']);

                if ($a['operasi_adjusment_detail'] == "PLUS") {
                    // PLUS
                    $this->stockRevampModel->update($stock['id'], [
                        'qty_diterima' => $qtyTotal + $a['qty_konversi']
                    ]);

                    $this->stockRevampDetailModel->update($a['stock_detail_id'], [
                        'qty_diterima' => $a['hasil_adjusment']
                    ]);

                    $this->stockRevampLogModel->insert([
                        'stock_detail_id'   => $a['stock_detail_id'],
                        'status'            => 'IN',
                        'qty_bersih'        => $stockDetail['qty_bersih'],
                        'qty_diterima'      => $a['qty_konversi'],
                        'keterangan'     => $adjusment['keterangan'],
                        'reference_tujuan_id' => $adjusment['id'],
                        'reference_tujuan_type' => "ADJUSMENT"
                    ]);

                    // $this->stockRevampHistoryModel->insert([
                    //     'stock_detail_asal' => $a['stock_detail_id'],
                    //     'stock_detail_akhir' => null,
                    //     'status' => "IN",
                    //     'qty_bersih_asal' => $stockDetail['qty_bersih'],
                    //     'qty_bersih_akhir' => $stockDetail['qty_bersih'],
                    //     'qty_diterima_asal' => $a['qty_asal'],
                    //     'qty_diterima_akhir' => $a['hasil_adjusment']
                    // ]);
                } else {
                    // MINUS
                    $this->stockRevampModel->update($stock['id'], [
                        'qty_diterima' => $qtyTotal - $a['qty_konversi']
                    ]);

                    $this->stockRevampDetailModel->update($a['stock_detail_id'], [
                        'qty_diterima' => $a['hasil_adjusment']
                    ]);

                    $this->stockRevampLogModel->insert([
                        'stock_detail_id' => $a['stock_detail_id'],
                        'status'         => 'OUT',
                        'qty_bersih'     => $stockDetail['qty_bersih'],
                        'qty_diterima'      => $a['qty_konversi'],
                        'keterangan'     => $adjusment['keterangan'],
                        'reference_tujuan_id' => $adjusment['id'],
                        'reference_tujuan_type' => "ADJUSMENT"
                    ]);

                    // $this->stockRevampHistoryModel->insert([
                    //     'stock_detail_asal' => $a['stock_detail_id'],
                    //     'stock_detail_akhir' => null,
                    //     'status' => "OUT",
                    //     'qty_bersih_asal' => $stockDetail['qty_bersih'],
                    //     'qty_bersih_akhir' => $stockDetail['qty_bersih'],
                    //     'qty_diterima_asal' => $a['qty_asal'],
                    //     'qty_diterima_akhir' => $a['hasil_adjusment']
                    // ]);
                }
            }

            $this->adjusmentModel->update($id, ['status_posting' => 1]);
            $db->transCommit();
            return response()->setJSON([
                "status" => true,
                "message" => "Adjusment berhasil diposting",
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

    public function getListBarangIsInit()
    {
        $data = $this->stockModel->getBarangAndStock(
            $this->request->getVar('type_barang'),
            $this->request->getVar('divisi_id'),
            $this->request->getVar('warehouse_id')
        );
        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListStockByStockID()
    {
        if (!empty($this->request->getVar('stock_id'))) {
            $isAdjusment = !empty($this->request->getVar('isAdjusment')) ? true : false;

            $dataResult = $this->stockDetail2Model->getStockListWithBCDoc(
                $this->request->getVar('stock_id'),
                $isAdjusment
            );
            $stock = $this->stockModel->find($this->request->getVar('stock_id'));
            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $this->barangMaster->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $this->satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
            } else {
                $kemasan = $this->kemasanModel->find($stock['kemasan_id']);
                $satuan = $this->satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }
            for ($i = 0; $i < count($dataResult); $i++) {
                $bcType = $this->metaDataModel->find($dataResult[$i]['bc_id']);
                $noDaftar = $this->stockDetail2Model->getNomorDaftar($dataResult[$i]['no_aju'], $dataResult[$i]['bc_id']);

                $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                $dataResult[$i]['satuan'] = $satuan['kode_satuan'];
                $dataResult[$i]['barang'] = strtoupper($barangName);
                $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                $dataResult[$i]['type_barang'] = $stock['tipe_barang'];
                $dataResult[$i]['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                $dataResult[$i]['no_daftar'] = $noDaftar;
            }
            return response()->setJSON([
                'data' => $dataResult,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }

    public function getAdjusmentNo()
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

    private function get_no_str($tanggal)
    {
        $tanggalParts = explode('-', $tanggal);
        $month = $tanggalParts[1];
        $year = $tanggalParts[0];

        $no = $this->adjusmentModel->get_no(
            $month,
            $year,
            $this->this_company_id
        );

        return $no;
    }

    public function createAdjTambah()
    {
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();

        $data = [
            'divisi' => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'tanggal' => date('Y-m-d'),
        ];

        return view('Warehouse/stockAdjusment/form_tambah', $data);
    }

    public function detailAdjTambah($id)
    {
        $id = decrypt($id);
        $adjusment = $this->adjusmentModel->where('id', $id)->first();

        if ($adjusment == null) {
            return redirect()->to('stock-adjusment');
        }

        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataListBarang = $this->adjusmentDetailModel->getDetailAdjTambah($id);
        $dataWarehouse = $this->warehouseModel->where('id', $adjusment['warehouse_id'])->findAll();

        $data = [
            'divisi' => $dataDivisi,
            'dataSatuan' => $dataSatuan,
            'dataListBarang' => $dataListBarang,
            'adjusment' => $adjusment,
            'warehouse' => $dataWarehouse
        ];

        return view('Warehouse/stockAdjusment/form_tambah', $data);
    }

    public function importPreview()
    {
        try {
            // ========================
            // 🔍 VALIDASI FILE
            // ========================
            $rules = [
                "file" => [
                    'rules' => 'uploaded[file]|ext_in[file,xlsx,xls]',
                    'errors' => [
                        'uploaded' => 'Tidak ada file yang di-upload.',
                        'ext_in'   => 'File yang di-upload harus berupa file Excel (.xlsx atau .xls).',
                    ],
                ],
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                return $this->response->setJSON([
                    "status"  => false,
                    "message" => $errorList[array_key_first($errorList)],
                    'token'   => csrf_hash()
                ]);
            }

            // ========================
            // 📂 BACA FILE EXCEL
            // ========================
            $file = $this->request->getFile('file');
            $divisiId = $this->request->getVar('divisi_id');
            $warehouseId = $this->request->getVar('warehouse_id');

            $divisi = $this->divisiModel->where('id', $divisiId)->first();
            $warehouse = $this->warehouseModel->where('id', $warehouseId)->first();

            if (!$file->isValid()) {
                throw new \RuntimeException($file->getErrorString() . '(' . $file->getError() . ')');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $worksheet   = $spreadsheet->getActiveSheet();

            $data = [];
            foreach ($worksheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = trim((string) $cell->getValue());
                }
                $data[] = $rowData;
            }

            // ========================
            // 🔍 VALIDASI DATA
            // ========================
            $dataResult  = [];
            $dataPreview = [];

            foreach ($data as $index => $val) {
                $status  = true;
                $message = null;

                $no         = trim($val[0] ?? '');
                $kodeBarang = trim($val[1] ?? '');
                $barangName = trim($val[2] ?? '');
                $spesifikasi = trim($val[3] ?? '');
                $qty        = (float) ($val[4] ?? 0);

                // =========================
                // 🔸 Validasi master barang
                // =========================
                $barangMasterFirst = $this->barangMasterModel
                    ->where('company_id', $this->this_company_id)
                    ->where('kode_barang', $kodeBarang)
                    ->where('barang_name', $barangName)
                    ->where('deletedAt', null)
                    ->first();

                $barangMasterSpesifikasi = null;
                if ($barangMasterFirst) {
                    $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel
                        ->select('barang_master_spesifikasi.*, satuans.kode_satuan')
                        ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1', 'left')
                        ->where('barang_master_id', $barangMasterFirst['id'])
                        ->where('TRIM(spesifikasi)', $spesifikasi)
                        ->where('barang_master_spesifikasi.deletedAt', null)
                        ->first();

                    if (!$barangMasterSpesifikasi) {
                        $status = false;
                        $message = "Spesifikasi tidak ditemukan di master barang.";
                    }
                } else {
                    $status = false;
                    $message = "Kode barang tidak ditemukan di master barang.";
                }

                // =========================
                // 🔸 Validasi stok inisiasi
                // =========================
                if ($status && $barangMasterSpesifikasi) {
                    $validasiStokInisiasi = $this->stockRevampModel->getBarangBelumDiinisiasi(
                        $this->this_company_id,
                        $divisiId,
                        $warehouseId,
                        $barangMasterSpesifikasi['id'],
                        $barangMasterSpesifikasi['satuan_1']
                    );

                    if ($validasiStokInisiasi == null) {
                        $status = false;
                        $message = "Stok belum diinisiasi.";
                    }
                }

                // =========================
                // 💾 Simpan ke array result
                // =========================
                $dataResult[] = [
                    'id' => $validasiStokInisiasi['id'] ?? null,
                    'stock_id' => $validasiStokInisiasi['id'] ?? null,
                    'kode_barang' => $kodeBarang,
                    'barang_name' => $barangName,
                    'spesifikasi' => $spesifikasi,
                    'qty_adjusment' => $qty,
                    'satuan_id' => $barangMasterSpesifikasi['satuan_1'] ?? null,
                    'kode_satuan' => $barangMasterSpesifikasi['kode_satuan'] ?? null,
                    'divisi' => $divisi['divisi'],
                    'warehouse_name' => $warehouse['warehouse_name'],
                    'status'        => $status,
                    'message'       => $message,
                ];
            }

            // =========================
            // ✅ RETURN RESPONSE
            // =========================
            return $this->response->setJSON([
                'data' => [
                    'dataResult'  => $dataResult,
                ],
                'status' => true,
                'token'  => csrf_hash()
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => false,
                'token'   => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function postingAdjTambah()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $adjusment = $this->adjusmentModel->where('id', $id)->first();
            $adjusmentList = $this->adjusmentDetailModel->where('adjusment_id', $id)->where('deletedAt', null)->findAll();

            foreach ($adjusmentList as $a) {
                $stock = $this->stockRevampModel->where('id', $a['stock_id'])->first();

                $payload = [
                    'company_id'        => $this->this_company_id,
                    'barang_master_id'  => $stock['barang_master_id'],
                    'spesifikasi_id'    => $stock['spesifikasi_id'],
                    'unit_id'           => $stock['unit_id'],
                    'divisi_id'         => $stock['divisi_id'],
                    'warehouse_id'      => $stock['warehouse_id'],
                    'qty_bersih'        => $a['qty_adjusment'],
                    'qty_diterima'      => $a['qty_adjusment'],
                    'bc_id'             => 0,
                    'type_bc'           => 'NON PABEAN',
                    'reference_id'      => $adjusment['id'],
                    'po_type'           => null,
                    'po_id'             => null,
                    'reference_type'    => 'ADJUSMENT',
                    'status'            => 'IN',
                    'keterangan'        => $adjusment['no_adjusment']
                ];

                $this->stockRevampModel->insertStockRevamp(
                    $db,
                    $payload
                );
            }

            $this->adjusmentModel->update($id, ['status_posting' => 1]);
            $db->transCommit();
            return response()->setJSON([
                "status" => true,
                "message" => "Adjusment berhasil diposting",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    private function getTotalStockParent($stockId)
    {
        $total = 0;
        $listStock = $this->stockRevampDetailModel->where('stock_id', $stockId)->where('deletedAt', null)->findAll();
        foreach ($listStock as $l) {
            $total += $l['qty_diterima'];
        }

        return $total;
    }

    private function getMapKeteranganPoLokalBp($startDate, $endDate)
    {
        $dataQry = $this->penerimaanBarangDetailModel
            ->select('penerimaan_barang_detail.*')
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
            ->where('penerimaan_barang.company_id', $this->this_company_id)
            ->where('penerimaan_barang_detail.deletedAt', null)
            ->where('penerimaan_barang.status_penerimaan', "LOKAL")
            ->where('penerimaan_barang.tipe_bahan', "PENOLONG")
            ->groupStart()
            ->where('penerimaan_barang.tanggal >=', $startDate)
            ->where('penerimaan_barang.tanggal <=', $endDate)
            ->groupEnd()
            ->findAll();

        $dataMapKeterangan = [];

        foreach ($dataQry as $d) {
            $po = $d['purchase_order_id'];
            $spesifikasi = $d['spesifikasi_id'];

            // buat nested array
            $dataMapKeterangan[$po][$spesifikasi] = $d['keterangan'];
        }

        return $dataMapKeterangan;
    }
}
