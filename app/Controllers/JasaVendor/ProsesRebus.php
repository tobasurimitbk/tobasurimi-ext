<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\MetadataModel;
use App\Models\ProsesRebusDetailModel;
use App\Models\ProsesRebusModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
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
    protected $jasaVedorInDetailModel;
    protected $warehouseModel;

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
        $this->jasaVedorInDetailModel = new JasaVendorInDetailModel();
        $this->warehouseModel = new WarehousesModel();
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

            $jasaVendorInDetail = $this->jasaVedorInDetailModel->where('deletedAt', null)->like('stock_dokumen', $data->no_rebus)->first();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_rebus"              => $data->no_rebus,
                'status_used'           => $jasaVendorInDetail == null ? 0 : 1,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
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
            'prosesRebusDetail' => $this->prosesRebusDetailModel->getProsesRebusDetail($id),
            'divisi' => $this->divisiModel->where('id', $prosesRebus['divisi_id'])->findAll(),
            'warehouse' => $this->warehouseModel->where('id', $prosesRebus['warehouse_id'])->findAll()
        ];

        return view('jasaVendor/prosesRebus/form', $data);
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

        $prosesRebus = $this->prosesRebusModel->find($id);
        $prosesRebusDetail = $this->prosesRebusDetailModel->where('proses_rebus_id', $id)->findAll();

        foreach ($prosesRebusDetail as $p) {
            // BARANG OUT DARI INVENTORI
            $stockRebus = $this->stockModel->find($p['stock_rebus_id']);
            $stockHasilRebus = $this->stockModel->find($p['stock_hasil_rebus_id']);

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
                "-",
                $p['stock_dokumen']
            );

            // -----
            // BARANG IN KE INVENTORI
            $stok = $this->stockModel->insertStok(
                $prosesRebus['company_id'],
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockHasilRebus['barang1_id'],
                $stockHasilRebus['barang2_id'],
                $p['qty_hasil_rebus']
            );

            $checkStokDetail =  $this->stockModel->isDefinedStockSubDetail(
                $this->this_company_id,
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockHasilRebus['barang1_id'],
                $stockHasilRebus['barang2_id'],
                $p['bc_rebus_id'],
                $p['no_aju_rebus'],
                $stok
            );

            if ($checkStokDetail == null) {
                // INSERT STOK INISIASI
                $stokDetail = $this->stockDetailModel->insertStokDetail(
                    $stok,
                    0,
                    "In",
                    date('Y-m-d'),
                    $this->this_user_id,
                    "INISIASI",
                    "-",
                    "-"
                );
                $this->stockDetail2Model->insertStokDetail2(
                    $p['bc_rebus_id'],
                    $p['stock_hasil_rebus_id'],
                    $stokDetail,
                    0,
                    $p['no_aju_rebus'],
                    "-"
                );
            }

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
                "In",
                date('Y-m-d'),
                $this->this_user_id,
                "REBUS",
                $stockRebusDetail == null ? "-" : $stockRebusDetail['no_dokumen_1'], // AMBIL NOMOR LPB NYA (GET SUPPLIER NYA)
                $prosesRebus['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_rebus_id'],
                $p['stock_hasil_rebus_id'],
                $stokDetail,
                $p['qty_hasil_rebus'],
                $p['no_aju_rebus'],
                $p['stock_dokumen'],
                $prosesRebus['no_rebus'] . " ( " . $p['stock_dokumen'] . " )"
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
                "-",
                $p['stock_dokumen']
            );

            // -----
            // BARANG IN KE INVENTORI
            $stok = $this->stockModel->insertStok(
                $prosesRebus['company_id'],
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockHasilRebus['barang1_id'],
                $stockHasilRebus['barang2_id'],
                $p['qty_hasil_rebus']
            );

            $checkStokDetail =  $this->stockModel->isDefinedStockSubDetail(
                $this->this_company_id,
                $prosesRebus['warehouse_id'],
                $prosesRebus['divisi_id'],
                "bahan_baku",
                $stockHasilRebus['barang1_id'],
                $stockHasilRebus['barang2_id'],
                $p['bc_rebus_id'],
                $p['no_aju_rebus'],
                $stok
            );

            if ($checkStokDetail == null) {
                // INSERT STOK INISIASI
                $stokDetail = $this->stockDetailModel->insertStokDetail(
                    $stok,
                    0,
                    "In",
                    date('Y-m-d'),
                    $this->this_user_id,
                    "INISIASI",
                    "-",
                    "-"
                );
                $this->stockDetail2Model->insertStokDetail2(
                    $p['bc_rebus_id'],
                    $p['stock_hasil_rebus_id'],
                    $stokDetail,
                    0,
                    $p['no_aju_rebus'],
                    "-"
                );
            }

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
                $stockRebusDetail == null ? "-" : $stockRebusDetail['no_dokumen_1'], // AMBIL NOMOR LPB NYA (GET SUPPLIER NYA)
                $prosesRebus['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_rebus_id'],
                $p['stock_hasil_rebus_id'],
                $stokDetail,
                $p['qty_hasil_rebus'],
                $p['no_aju_rebus'],
                $p['stock_dokumen'],
                $prosesRebus['no_rebus'] . " ( " . $p['stock_dokumen'] . " )"
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
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouse_id = $this->request->getVar('warehouse_id');

        if (empty($warehouse_id)) {
            $no = $this->prosesRebusModel->get_no(date('m'), date('Y'), $last_day, "", $warehouse_id);
        } else {
            $warehouse = $this->warehouseModel->where('id', $warehouse_id)->first();
            $no = $this->prosesRebusModel->get_no(date('m'), date('Y'), $last_day, strtoupper($warehouse['code_warehouse']), $warehouse_id);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownListBarangIsInit()
    {
        $data = $this->stockModel->getBarangRebusAndStock(
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
