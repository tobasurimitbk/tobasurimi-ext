<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\MetadataModel;
use App\Models\ProsesRebusDetailModel;
use App\Models\ProsesRebusModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;

class ProsesRebus extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $divisiModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $metaDataModel;
    protected $prosesRebusModel;
    protected $prosesRebusDetailModel;
    protected $jasaVendorOutDetailModel;
    protected $warehouseModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->metaDataModel = new MetadataModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->prosesRebusModel = new ProsesRebusModel();
        $this->prosesRebusDetailModel = new ProsesRebusDetailModel();
        $this->jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
        ];
        return view('jasaVendor/prosesRebus/index', $data);
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
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
            "status" => $this->request->getVar("status"),
            "start_date" =>  $this->request->getVar("start_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("start_date")), "Y-m-d") : "",
            "end_date" =>  $this->request->getVar("end_date") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("end_date")), "Y-m-d") : "",
            "no_rebus" => $this->request->getVar("no_rebus"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'proses_rebus.company_id' => $this->this_company_id,
            'proses_rebus.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->prosesRebusModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $prosesRebusDetail = $this->prosesRebusDetailModel
                ->where('proses_rebus_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            if ($prosesRebusDetail[0]) {
                $stockDetail = $this->stockDetail2Model->getStockListDetail(
                    $prosesRebusDetail[0]['stock_rebus_id'],
                    $prosesRebusDetail[0]['bc_rebus_id'],
                    $prosesRebusDetail[0]['no_aju_rebus'],
                    $prosesRebusDetail[0]['stock_dokumen']
                );

                $barangDiRebus = $stockDetail['barang_master'];

                if ($prosesRebusDetail[0]['stock_hasil_rebus_id']) {
                    $stockDetailKeluar = $this->stockDetail2Model->getStockListDetailForRebusAllNew(
                        $prosesRebusDetail[0]['stock_hasil_rebus_id'],
                    );

                    $barangHasilRebus = $stockDetailKeluar['barang_master'] ?? null;
                } else {
                    $barangMasterModel = new BarangMasterModel();
                    $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();

                   
                    $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($prosesRebusDetail[0]['barang_out_id']);
                    $barangMaster = $barangMasterModel->find($barangMasterSpesifikasi['barang_master_id']);

                    $barangHasilRebus =  $barangMaster['barang_name'] ?? null;          // amanin kalau null
                   
                }
            }
            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel->where('deletedAt', null)->like('stock_dokumen', $data->no_rebus)->first();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_rebus"              => $data->no_rebus,
                'status_used'           => $jasaVendorOutDetail == null ? 0 : 1,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "barang_rebus"          => $barangDiRebus,
                "barang_hasi_rebus"     => $barangHasilRebus,
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($prosesRebusDetail),
                "status_posting"        => $data->status_posting
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
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'tanggal' => date('Y-m-d'),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'supplier' => $this->supplierModel->getSupplierAll()
        ];

        return view('jasaVendor/prosesRebus/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $prosesRebus = $this->prosesRebusModel->find($id);
        if ($prosesRebus == null) {
            return redirect()->to('proses-rebus');
        }

        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'prosesRebus' => $prosesRebus,
            'prosesRebusDetail' => $this->prosesRebusDetailModel->getProsesRebusDetail2($id),
            'divisi' => $this->divisiModel->where('id', $prosesRebus['divisi_id'])->findAll(),
            'warehouse' => $this->warehouseModel->where('id', $prosesRebus['warehouse_id'])->findAll(),
            'supplier' => $this->supplierModel->getSupplierByType("BAHAN BAKU")
        ];


        return view('jasaVendor/prosesRebus/form', $data);
    }

    public function createActionNew()
    {
        $barangs = json_decode($_POST['listBarang']);
        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $check = $this->prosesRebusModel
                ->where('no_rebus', $this->request->getVar('no_rebus'))
                ->where('deletedAt', null)
                ->first();

        if ($check != null) {
            return response()->setJSON([
                'message' => "Nomor Rebus Sudah Ada",
                'token' => csrf_hash(),
                'status' => false,
            ]);
        }

        $id = $this->prosesRebusModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'no_rebus' => $this->request->getVar('no_rebus'),
            "tanggal" => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
            "tanggal_selesai" => $this->request->getVar("tanggal_selesai") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_selesai")), "Y-m-d") : "",
            'keterangan' => $this->request->getVar('keterangan'),
            'type_barang' => "bahan_baku",
            'status_posting' => '0'
        ]);

        foreach ($barangs as $b) {
            // Get current stock quantity
            $stockDetail = $this->stockDetail2Model->getStockListDetail(
                $b->stock_id,
                $b->bc_id,
                $b->no_aju,
                $b->stock_dokumen
            );
            
            $qty_stok_sistem = $stockDetail['stok_total'] ?? 0;
            $qty_input_user = $b->qty;
            
            // Hitung qty bersih dan kotor
            $qty_bersih = min($qty_input_user, $qty_stok_sistem); // Ambil yang terkecil
            $qty_kotor = max($qty_input_user - $qty_stok_sistem, 0); // Selisihnya (kalau ada)

            $this->prosesRebusDetailModel->insert([
                'proses_rebus_id' => $id,
                'stock_rebus_id' => $b->stock_id,
                'bc_rebus_id' => $b->bc_id,
                'no_aju_rebus' => $b->no_aju,
                'qty_rebus' => $qty_bersih, // <-- INI YANG DIUBAH, pakai qty bersih
                'stock_hasil_rebus_id' => $b->output->stock_id,
                'barang_out_id' => $b->output->barang_id,
                'stock_dokumen' => $b->stock_dokumen,
                'qty_hasil_rebus' => $b->output->qty,
                'qty_kotor' => $qty_kotor
            ]);
        }

        return response()->setJSON([
            'message' => "Proses Rebus Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($id)
        ]);
    }


    public function updateActionNew()
    {
        $barangs = json_decode($_POST['listBarang']);

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $id = decrypt($this->request->getVar('id'));

        // Update header data
        $this->prosesRebusModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            "tanggal" => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
            "tanggal_selesai" => $this->request->getVar("tanggal_selesai") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_selesai")), "Y-m-d") : "",
            'keterangan' => $this->request->getVar('keterangan'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'type_barang' => "bahan_baku",
            'status_posting' => '0' // Reset posting status when updated
        ]);

        $id_detail_all = [];

        foreach ($barangs as $b) {
            // Get current stock quantity
            $stockDetail = $this->stockDetail2Model->getStockListDetail(
                $b->stock_id,
                $b->bc_id,
                $b->no_aju,
                $b->stock_dokumen
            );
            
            $qty_stok_sistem = $stockDetail['qty'] ?? 0;
            $qty_input_user = $b->qty;
            
            // Calculate clean and excess quantities
            $qty_bersih = min($qty_input_user, $qty_stok_sistem);
            $qty_kotor = max($qty_input_user - $qty_stok_sistem, 0);

            // Check if record exists
            $check = $this->prosesRebusDetailModel
                ->where('proses_rebus_id', $id)
                ->where('stock_rebus_id', $b->stock_id)
                ->where('bc_rebus_id', $b->bc_id)
                ->where('no_aju_rebus', $b->no_aju)
                ->where('stock_hasil_rebus_id', $b->output->stock_id)
                ->where('stock_dokumen', $b->stock_dokumen)
                ->first();

            if ($check != null) {
                // UPDATE existing record with calculated quantities
                $this->prosesRebusDetailModel->update($check['id'], [
                    'proses_rebus_id' => $id,
                    'stock_rebus_id' => $b->stock_id,
                    'bc_rebus_id' => $b->bc_id,
                    'no_aju_rebus' => $b->no_aju,
                    'qty_rebus' => $qty_bersih, // Use calculated clean quantity
                    'stock_hasil_rebus_id' => $b->output->stock_id,
                    'barang_out_id' => $b->output->barang_id,
                    'stock_dokumen' => $b->stock_dokumen,
                    'qty_hasil_rebus' => $b->output->qty,
                    'qty_kotor' => $qty_kotor // Store excess quantity
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // For new records, delete any existing conflicting records first
                $this->prosesRebusDetailModel
                    ->where('proses_rebus_id', $id)
                    ->where('stock_rebus_id', $b->stock_id)
                    ->where('bc_rebus_id', $b->bc_id)
                    ->where('no_aju_rebus', $b->no_aju)
                    ->where('stock_hasil_rebus_id', $b->output->stock_id)
                    ->where('stock_dokumen', $b->stock_dokumen)
                    ->delete();

                // INSERT new record with calculated quantities
                $id_detail_new = $this->prosesRebusDetailModel->insert([
                    'proses_rebus_id' => $id,
                    'stock_rebus_id' => $b->stock_id,
                    'bc_rebus_id' => $b->bc_id,
                    'no_aju_rebus' => $b->no_aju,
                    'qty_rebus' => $qty_bersih, // Use calculated clean quantity
                    'stock_hasil_rebus_id' => $b->output->stock_id,
                    'barang_out_id' => $b->output->barang_id,
                    'stock_dokumen' => $b->stock_dokumen,
                    'qty_hasil_rebus' => $b->output->qty,
                    'qty_kotor' => $qty_kotor // Store excess quantity
                ]);
                array_push($id_detail_all, $id_detail_new);
            }
        }

        // Delete any detail records not included in the update
        $this->prosesRebusDetailModel->where('proses_rebus_id', $id)->whereNotIn('id', $id_detail_all)->delete();

        return response()->setJSON([
            'message' => "Proses Rebus Berhasil Diupdate",
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function createAction()
    {
        $barangs = json_decode($_POST['listBarang']);

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $check = $this->prosesRebusModel->where('no_rebus', $this->request->getVar('no_rebus'))->first();

        if ($check != null) {
            return response()->setJSON([
                'message' => "Nomor Rebus Sudah Ada",
                'token' => csrf_hash(),
                'status' => false,
            ]);
        }

        $id = $this->prosesRebusModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'no_rebus' => $this->request->getVar('no_rebus'),
            "tanggal" => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
            "tanggal_selesai" => $this->request->getVar("tanggal_selesai") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_selesai")), "Y-m-d") : "",
            'keterangan' => $this->request->getVar('keterangan'),
            'type_barang' => "bahan_baku",
            'status_posting' => '0'
        ]);

        foreach ($barangs as $b) {
            $this->prosesRebusDetailModel->insert([
                'proses_rebus_id' => $id,
                'stock_rebus_id' => $b->stock_id,
                'bc_rebus_id' => $b->bc_id,
                'no_aju_rebus' => $b->no_aju,
                'qty_rebus' => $b->qty,
                'stock_hasil_rebus_id' => $b->output->stock_id,
                'stock_dokumen' => $b->stock_dokumen,
                'qty_hasil_rebus' => $b->output->qty,
            ]);
        }

        return response()->setJSON([
            'message' => "Proses Rebus Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($id)
        ]);
    }

    public function updateAction()
    {
        $barangs = json_decode($_POST['listBarang']);

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $id = decrypt($this->request->getVar('id'));

        $this->prosesRebusModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            "tanggal" => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
            "tanggal_selesai" => $this->request->getVar("tanggal_selesai") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_selesai")), "Y-m-d") : "",
            'keterangan' => $this->request->getVar('keterangan'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'type_barang' => "bahan_baku",
            'status_posting' => '0'
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach ($barangs as $b) {
            $check = $this->prosesRebusDetailModel
                ->where('proses_rebus_id', $id)
                ->where('stock_rebus_id', $b->stock_id)
                ->where('bc_rebus_id', $b->bc_id)
                ->where('no_aju_rebus', $b->no_aju)
                ->where('stock_hasil_rebus_id',  $b->output->stock_id)
                ->where('stock_dokumen', $b->stock_dokumen)
                ->first();

            if ($check != null) {
                $this->prosesRebusDetailModel->update($check['id'], [
                    'proses_rebus_id' => $id,
                    'stock_rebus_id' => $b->stock_id,
                    'bc_rebus_id' => $b->bc_id,
                    'no_aju_rebus' => $b->no_aju,
                    'qty_rebus' => $b->qty,
                    'stock_hasil_rebus_id' => $b->output->stock_id,
                    'stock_dokumen' => $b->stock_dokumen,
                    'qty_hasil_rebus' => $b->output->qty,
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // NEW
                // DELETE
                $this->prosesRebusDetailModel
                    ->where('proses_rebus_id', $id)
                    ->where('stock_rebus_id', $b->stock_id)
                    ->where('bc_rebus_id', $b->bc_id)
                    ->where('no_aju_rebus', $b->no_aju)
                    ->where('stock_hasil_rebus_id',  $b->output->stock_id)
                    ->where('stock_dokumen', $b->stock_dokumen)
                    ->delete();

                // INSERT
                $id_detail_new =   $this->prosesRebusDetailModel->insert([
                    'proses_rebus_id' => $id,
                    'stock_rebus_id' => $b->stock_id,
                    'bc_rebus_id' => $b->bc_id,
                    'no_aju_rebus' => $b->no_aju,
                    'qty_rebus' => $b->qty,
                    'stock_hasil_rebus_id' => $b->output->stock_id,
                    'stock_dokumen' => $b->stock_dokumen,
                    'qty_hasil_rebus' => $b->output->qty,
                ]);
                array_push($id_detail_all,  $id_detail_new);
            }
        }

        $this->prosesRebusDetailModel->where('proses_rebus_id', $id)->whereNotIn('id', $id_detail_all)->delete();

        return response()->setJSON([
            'message' => "Proses Rebus Berhasil Diupdate",
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->prosesRebusModel->delete($id);
        $this->prosesRebusDetailModel->where('proses_rebus_id', $id)->delete();
        return response()->setJSON([
            'message' => "Proses Rebus Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();

        $prosesRebus = $this->prosesRebusModel->find($id);
        $prosesRebusDetail = $this->prosesRebusDetailModel->where('proses_rebus_id', $id)->findAll();

        foreach ($prosesRebusDetail as $p) {
            // BARANG OUT DARI INVENTORI
            $stockRebus = $this->stockModel->find($p['stock_rebus_id']);
            $stockHasilRebus = $this->stockModel->find($p['stock_hasil_rebus_id']);

            if ($stockHasilRebus) {
                // Kalau stok hasil rebus ada, langsung pakai datanya
                $barang1Id = $stockHasilRebus['barang1_id'];
                $barang2Id = $stockHasilRebus['barang2_id'];
            } else {
                // Kalau stok hasil rebus tidak ada, fallback dari barang_out_id
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($p['barang_out_id']);
                $barangMaster = $barangMasterModel->find($barangMasterSpesifikasi['barang_master_id']);

                $barang1Id = $barangMaster['id'] ?? null;               // amanin kalau null
                $barang2Id = $barangMasterSpesifikasi['id'] ?? null;    // amanin kalau null
            }
           
            // BARANG LAMA
            $stockRebusDetail = $this->stockDetail2Model->getStockListDetail(
                $p['stock_rebus_id'],
                $p['bc_rebus_id'],
                $p['no_aju_rebus'],
                $p['stock_dokumen']
            );

            $stok = $this->stockModel->insertStok(
                $prosesRebus['company_id'],
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockRebus['barang1_id'],
                $stockRebus['barang2_id'],
                ($p['qty_rebus'] * -1),
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $p['qty_rebus'],
                "Out",
                date('Y-m-d'),
                $this->this_user_id,
                "REBUS",
                $prosesRebus['no_rebus'],
                $prosesRebus['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_rebus_id'],
                $p['stock_rebus_id'],
                $stokDetail,
                $p['qty_rebus'],
                $p['no_aju_rebus'],
                $prosesRebus['no_rebus'],
                $p['stock_dokumen'],
                $stockRebusDetail['supplier_id'],
                $stockRebusDetail['harga_umum'],
                $stockRebusDetail['harga_harian'],
                $stockRebusDetail['harga_bulanan'],
                $stockRebusDetail['no_po']

            );

            // -----
            // BARANG IN KE INVENTORI
            $stok = $this->stockModel->insertStok(
                $prosesRebus['company_id'],
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $barang1Id,
                $barang2Id,
                $p['qty_hasil_rebus']
            );


            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $p['qty_hasil_rebus'],
                "In",
                date('Y-m-d'),
                $this->this_user_id,
                "REBUS",
                $prosesRebus['no_rebus'],
                $prosesRebus['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_rebus_id'],
                $stok,
                $stokDetail,
                $p['qty_hasil_rebus'],
                $p['no_aju_rebus'],
                $prosesRebus['no_rebus'],
                $prosesRebus['no_rebus'] . " ( " . $p['stock_dokumen'] . " )",
                $stockRebusDetail['supplier_id'],
                $stockRebusDetail['harga_umum'],
                $stockRebusDetail['harga_harian'],
                $stockRebusDetail['harga_bulanan'],
                $stockRebusDetail['no_po']
            );
        }

        $this->prosesRebusModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'message' => "Proses rebus berhasil diposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }


    public function postingNew()
    {
        $id = decrypt($this->request->getVar('id'));

        $prosesRebus = $this->prosesRebusModel->find($id);
        $prosesRebusDetail = $this->prosesRebusDetailModel->where('proses_rebus_id', $id)->findAll();

        foreach ($prosesRebusDetail as $p) {
            // BARANG OUT DARI INVENTORI
            $stockRebus = $this->stockModel->find($p['stock_rebus_id']);
            $stockHasilRebus = $this->stockModel->find($p['stock_hasil_rebus_id']);

            // BARANG LAMA
            $stockRebusDetail = $this->stockDetail2Model->getStockListDetail(
                $p['stock_rebus_id'],
                $p['bc_rebus_id'],
                $p['no_aju_rebus'],
                $p['stock_dokumen']
            );

            // Hitung qty yang akan diproses
            $qty_stok_sistem = $stockRebusDetail['qty'] ?? 0;
            $qty_rebus_input = $p['qty_rebus'];
            $qty_kotor = 0;

            if ($qty_rebus_input > $qty_stok_sistem) {
                $qty_kotor = $qty_rebus_input - $qty_stok_sistem;
                $qty_rebus_input = $qty_stok_sistem;
            }

            // Update qty kotor di database
            $this->prosesRebusDetailModel->update($p['id'], ['qty_kotor' => $qty_kotor]);

            // Proses stok keluar (hanya untuk qty yang ada di sistem)
            if ($qty_rebus_input > 0) {
                $stok = $this->stockModel->insertStok(
                    $prosesRebus['company_id'],
                    $prosesRebus['warehouse_id'],
                    $prosesRebus['divisi_id'],
                    "bahan_baku",
                    $stockRebus['barang1_id'],
                    $stockRebus['barang2_id'],
                    ($qty_rebus_input * -1)
                );

                // DETAIL
                $stokDetail = $this->stockDetailModel->insertStokDetail(
                    $stok,
                    $qty_rebus_input,
                    "Out",
                    date('Y-m-d'),
                    $this->this_user_id,
                    "REBUS",
                    $prosesRebus['no_rebus'],
                    $prosesRebus['keterangan']
                );

                // SUB DETAIL
                $this->stockDetail2Model->insertStokDetail2(
                    $p['bc_rebus_id'],
                    $p['stock_rebus_id'],
                    $stokDetail,
                    $qty_rebus_input,
                    $p['no_aju_rebus'],
                    $prosesRebus['no_rebus'],
                    $p['stock_dokumen'],
                    $stockRebusDetail['supplier_id'],
                    $stockRebusDetail['harga_umum'],
                    $stockRebusDetail['harga_harian'],
                    $stockRebusDetail['harga_bulanan'],
                    $stockRebusDetail['no_po']
                );
            }

            // -----
            // BARANG IN KE INVENTORI (proses seperti biasa)
            $stok = $this->stockModel->insertStok(
                $prosesRebus['company_id'],
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockHasilRebus['barang1_id'],
                $stockHasilRebus['barang2_id'],
                $p['qty_hasil_rebus']
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $p['qty_hasil_rebus'],
                "In",
                date('Y-m-d'),
                $this->this_user_id,
                "REBUS",
                $prosesRebus['no_rebus'],
                $prosesRebus['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_rebus_id'],
                $p['stock_hasil_rebus_id'],
                $stokDetail,
                $p['qty_hasil_rebus'],
                $p['no_aju_rebus'],
                $prosesRebus['no_rebus'],
                $prosesRebus['no_rebus'] . " ( " . $p['stock_dokumen'] . " )",
                $stockRebusDetail['supplier_id'],
                $stockRebusDetail['harga_umum'],
                $stockRebusDetail['harga_harian'],
                $stockRebusDetail['harga_bulanan'],
                $stockRebusDetail['no_po']
            );
        }

        $this->prosesRebusModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'message' => "Proses rebus berhasil diposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }


    public function unPosting()
    {
        $id = decrypt($this->request->getVar('id'));

        $prosesRebus = $this->prosesRebusModel->find($id);
        $prosesRebusDetail = $this->prosesRebusDetailModel->where('proses_rebus_id', $id)->findAll();

        foreach ($prosesRebusDetail as $p) {
            // BARANG OUT DARI INVENTORI
            $stockRebus = $this->stockModel->find($p['stock_rebus_id']);
            $stockHasilRebus = $this->stockModel->find($p['stock_hasil_rebus_id']);

            // BARANG IN YANG DIREBUS
            $stockRebusDetail = $this->stockDetail2Model->getStockListDetail(
                $p['stock_rebus_id'],
                $p['bc_rebus_id'],
                $p['no_aju_rebus'],
                $p['stock_dokumen']
            );

            $stok = $this->stockModel->insertStok(
                $prosesRebus['company_id'],
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockRebus['barang1_id'],
                $stockRebus['barang2_id'],
                ($p['qty_rebus']),
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $p['qty_rebus'],
                "In",
                date('Y-m-d'),
                $this->this_user_id,
                "REBUS",
                $prosesRebus['no_rebus'],
                $prosesRebus['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_rebus_id'],
                $p['stock_rebus_id'],
                $stokDetail,
                $p['qty_rebus'],
                $p['no_aju_rebus'],
                $prosesRebus['no_rebus'],
                $p['stock_dokumen'],
                $stockRebusDetail['supplier_id'],
                $stockRebusDetail['harga_umum'],
                $stockRebusDetail['harga_harian'],
                $stockRebusDetail['harga_bulanan'],
                $stockRebusDetail['no_po']
            );

            // -----
            // BARANG OUT HASIL REBUS
            $stok = $this->stockModel->insertStok(
                $prosesRebus['company_id'],
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockHasilRebus['barang1_id'],
                $stockHasilRebus['barang2_id'],
                $p['qty_hasil_rebus']
            );

            $stockRebusDetail = $this->stockDetail2Model->getStockListDetail(
                $p['stock_rebus_id'],
                $p['bc_rebus_id'],
                $p['no_aju_rebus'],
                $p['stock_dokumen']
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $p['qty_hasil_rebus'],
                "Out",
                date('Y-m-d'),
                $this->this_user_id,
                "REBUS",
                $prosesRebus['no_rebus'],
                $prosesRebus['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_rebus_id'],
                $p['stock_hasil_rebus_id'],
                $stokDetail,
                $p['qty_hasil_rebus'],
                $p['no_aju_rebus'],
                $prosesRebus['no_rebus'],
                $prosesRebus['no_rebus'] . " ( " . $p['stock_dokumen'] . " )",
                $stockRebusDetail['supplier_id'],
                $stockRebusDetail['harga_umum'],
                $stockRebusDetail['harga_harian'],
                $stockRebusDetail['harga_bulanan'],
                $stockRebusDetail['no_po']
            );
        }

        $this->prosesRebusModel->update($id, ['status_posting' => '0']);

        return response()->setJSON([
            'message' => "Proses rebus berhasil diunposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }


    public function unPostingNew()
    {
        $id = decrypt($this->request->getVar('id'));

        $prosesRebus = $this->prosesRebusModel->find($id);
        $prosesRebusDetail = $this->prosesRebusDetailModel->where('proses_rebus_id', $id)->findAll();

        foreach ($prosesRebusDetail as $p) {
            // BARANG OUT DARI INVENTORI
            $stockRebus = $this->stockModel->find($p['stock_rebus_id']);
            $stockHasilRebus = $this->stockModel->find($p['stock_hasil_rebus_id']);

            // BARANG LAMA
            $stockRebusDetail = $this->stockDetail2Model->getStockListDetail(
                $p['stock_rebus_id'],
                $p['bc_rebus_id'],
                $p['no_aju_rebus'],
                $p['stock_dokumen']
            );

            // Hitung qty yang akan diproses (konsisten dengan posting)
            $qty_stok_sistem = $stockRebusDetail['qty'] ?? 0;
            $qty_rebus_input = $p['qty_rebus'];
            $qty_kotor = $p['qty_kotor'] ?? 0; // Ambil nilai qty kotor dari database
            
            // Jika ada qty kotor, kita hanya mengembalikan qty yang benar-benar dikurangi dari stok sistem
            $qty_to_return = ($qty_rebus_input > $qty_stok_sistem) ? $qty_stok_sistem : $qty_rebus_input;

            // BARANG IN YANG DIREBUS (kembalikan stok)
            if ($qty_to_return > 0) {
                $stok = $this->stockModel->insertStok(
                    $prosesRebus['company_id'],
                    $prosesRebus['warehouse_id'],
                    $prosesRebus['divisi_id'],
                    "bahan_baku",
                    $stockRebus['barang1_id'],
                    $stockRebus['barang2_id'],
                    $qty_to_return // Kembalikan qty yang sebenarnya dikurangi dari sistem
                );

                // DETAIL
                $stokDetail = $this->stockDetailModel->insertStokDetail(
                    $stok,
                    $qty_to_return,
                    "In",
                    date('Y-m-d'),
                    $this->this_user_id,
                    "REBUS",
                    $prosesRebus['no_rebus'],
                    $prosesRebus['keterangan']
                );

                // SUB DETAIL
                $this->stockDetail2Model->insertStokDetail2(
                    $p['bc_rebus_id'],
                    $p['stock_rebus_id'],
                    $stokDetail,
                    $qty_to_return,
                    $p['no_aju_rebus'],
                    $prosesRebus['no_rebus'],
                    $p['stock_dokumen'],
                    $stockRebusDetail['supplier_id'],
                    $stockRebusDetail['harga_umum'],
                    $stockRebusDetail['harga_harian'],
                    $stockRebusDetail['harga_bulanan'],
                    $stockRebusDetail['no_po']
                );
            }

            // -----
            // BARANG OUT HASIL REBUS (proses seperti biasa)
            $stok = $this->stockModel->insertStok(
                $prosesRebus['company_id'],
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockHasilRebus['barang1_id'],
                $stockHasilRebus['barang2_id'],
                ($p['qty_hasil_rebus'] * -1) // dikembalikan jadi minus
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $p['qty_hasil_rebus'],
                "Out",
                date('Y-m-d'),
                $this->this_user_id,
                "REBUS",
                $prosesRebus['no_rebus'],
                $prosesRebus['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_rebus_id'],
                $p['stock_hasil_rebus_id'],
                $stokDetail,
                $p['qty_hasil_rebus'],
                $p['no_aju_rebus'],
                $prosesRebus['no_rebus'],
                $prosesRebus['no_rebus'] . " ( " . $p['stock_dokumen'] . " )",
                $stockRebusDetail['supplier_id'],
                $stockRebusDetail['harga_umum'],
                $stockRebusDetail['harga_harian'],
                $stockRebusDetail['harga_bulanan'],
                $stockRebusDetail['no_po']
            );
        }

        $this->prosesRebusModel->update($id, ['status_posting' => '0']);

        return response()->setJSON([
            'message' => "Proses rebus berhasil diunposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function getProsesRebusNo()
    {
        $tanggal = date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))));
        $warehouse_id = $this->request->getVar('warehouse_id');


        if (empty($tanggal) || empty($warehouse_id)) {
            $no = $this->prosesRebusModel->get_no(
                date('m'),
                date('Y'),
                ""
            );
        } else {

            $tanggalExplode = explode('-', $tanggal);
            $year = $tanggalExplode[0];
            $month = $tanggalExplode[1];

            $warehouse = $this->warehouseModel->where('id', $warehouse_id)->first();
            $divisi = $this->divisiModel->where('id', $warehouse['divisi_id'])->first();
            $no = $this->prosesRebusModel->get_no(
                $month,
                $year,
                $divisi['divisi']
            );
        }

        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownListBarangIsInit()
    {
        $stockRevampModel = new StockRevampModel();
        $data =   $data = $stockRevampModel->getBarangRebusAndStock(
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

    public function dropdownListHasilRebus()
    {
        $stockID = $this->request->getVar('stock_id');

        $response = array();
        if (!empty($stockID)) {
            $data = $this->stockModel->getBarangRebusAndStock(
                $this->request->getVar('type_barang'),
                $this->request->getVar('divisi_id'),
                $this->request->getVar('warehouse_id')
            );

            foreach ($data as $d) {
                if ($d['stock_id'] != $stockID) {
                    array_push($response, $d);
                }
            }
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $response
        ]);
    }
}
