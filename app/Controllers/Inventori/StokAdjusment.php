<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\AdjusmentDetailModel;
use App\Models\AdjusmentModel;
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
    }

    public function index()
    {
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
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
            "status" => $this->request->getVar("status"),
            "no_adjusment" => $this->request->getVar("no_adjusment"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'adjusment.company_id' => $this->this_company_id,
            'divisis.deletedAt' => null,
            'adjusment.deletedAt' => null
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->adjusmentModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            $listItem = $this->adjusmentDetailModel->where('adjusment_id', $data->id)->findAll();
            $totalItem = count($listItem);
            $userName = $this->userModel->find($data->createdBy);

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_adjusment"          => $data->no_adjusment,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "keterangan"            => $data->keterangan,
                "divisi"                => $data->divisi,
                "total_item"            => $totalItem,
                "status_posting"        => $data->status_posting,
                "created_by"            => $userName == null ? "-" : $userName['name']
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
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'tipeAdjusment' => $this->metaDataModel->where('deletedAt', null)->where('name', "Tipe Adjusment")->findAll(),
            'tanggal' => date('Y-m-d'),
        ];

        return view('Warehouse/stockAdjusment/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'adjusment' => $this->adjusmentModel->find($id),
            'tipeAdjusment' => $this->metaDataModel->where('deletedAt', null)->where('name', "Tipe Adjusment")->findAll(),
            'listBarang' => $this->adjusmentDetailModel->getDetail($id)
        ];

        if ($data['adjusment'] == null) {
            return redirect()->to('stock-adjusment');
        }

        $data['warehouse'] = $this->warehouseModel->where('divisi_id', $data['adjusment']['divisi_id'])->where('deletedAt', null)->findAll();

        return view('Warehouse/stockAdjusment/form', $data);
    }

    public function createAction()
    {

        $first = $this->adjusmentModel->where('company_id', $this->this_company_id)->where('no_adjusment', $this->request->getVar('no_adjusment'))->first();

        if ($first != null) {
            return response()->setJSON([
                'message' => "Nomor Adjusment Sudah Ada",
                'token' => csrf_hash(),
                'status' => false,
            ]);
        }

        $id = $this->adjusmentModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'no_adjusment' => $this->request->getVar('no_adjusment'),
            'tanggal' => date('Y-m-d'),
            'keterangan' => $this->request->getVar('keterangan'),
            'tipe_adjusment' => $this->request->getVar('tipe_adjusment'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'status_posting' => '0',
            'createdBy' => $this->this_user_id
        ]);

        foreach (json_decode($_POST['listBarang']) as $l) {
            $stock = $this->stockModel->find($l->stock_id);
            $this->adjusmentDetailModel->insert([
                'adjusment_id' => $id,
                'bc_id' => $l->bc_id,
                'warehouse_id' => $l->warehouse_id,
                'barang1_id' => $stock['barang1_id'],
                'barang2_id' => $stock['barang2_id'],
                'kemasan_id' => $stock['kemasan_id'],
                'no_aju' => $l->no_aju,
                'stock_dokumen' => $l->stock_dokumen,
                'tipe_barang' => $l->type_barang,
                'operasi' => $l->type_adjusment,
                'qty' => $l->qty_adjusment
            ]);
        }

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Adjusment berhasil disimpan",
            'id' => encrypt($id)
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $first = $this->adjusmentModel
            ->where('company_id', $this->this_company_id)
            ->where('no_adjusment', $this->request->getVar('no_adjusment'))
            ->where('id !=', $id)
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'message' => "Nomor Adjusment Sudah Ada",
                'token' => csrf_hash(),
                'status' => false,
            ]);
        }

        $this->adjusmentModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'keterangan' => $this->request->getVar('keterangan'),
            'tipe_adjusment' => $this->request->getVar('tipe_adjusment'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'status_posting' => '0',
            'createdBy' => $this->this_user_id
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach (json_decode($_POST['listBarang']) as $l) {
            $stock = $this->stockModel->find($l->stock_id);
            // CHECK
            $check = $this->adjusmentDetailModel
                ->where('adjusment_id', $id)
                ->where('warehouse_id', $l->warehouse_id)
                ->where('barang1_id', $stock['barang1_id'])
                ->where('barang2_id', $stock['barang2_id'])
                ->where('kemasan_id', $stock['kemasan_id'])
                ->where('bc_id', $l->bc_id)
                ->where('no_aju', $l->no_aju)
                ->where('stock_dokumen', $l->stock_dokumen)
                ->first();

            if ($check != null) {
                $this->adjusmentDetailModel->update($check['id'], [
                    'adjusment_id' => $id,
                    'bc_id' => $l->bc_id,
                    'warehouse_id' => $l->warehouse_id,
                    'barang1_id' => $stock['barang1_id'],
                    'barang2_id' => $stock['barang2_id'],
                    'kemasan_id' => $stock['kemasan_id'],
                    'stock_dokumen' => $l->stock_dokumen,
                    'no_aju' => $l->no_aju,
                    'operasi' => $l->type_adjusment,
                    'tipe_barang' => $l->type_barang,
                    'qty' => $l->qty_adjusment
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // NEW BARANG
                // DELETE
                $this->adjusmentDetailModel
                    ->where('adjusment_id', $id)
                    ->where('warehouse_id', $l->warehouse_id)
                    ->where('barang1_id', $stock['barang1_id'])
                    ->where('barang2_id', $stock['barang2_id'])
                    ->where('kemasan_id', $stock['kemasan_id'])
                    ->where('bc_id', $l->bc_id)
                    ->where('no_aju', $l->no_aju)
                    ->where('stock_dokumen', $l->stock_dokumen)
                    ->delete();

                // INSERT NEW
                $id_detail_new = $this->adjusmentDetailModel->insert([
                    'adjusment_id' => $id,
                    'bc_id' => $l->bc_id,
                    'warehouse_id' => $l->warehouse_id,
                    'barang1_id' => $stock['barang1_id'],
                    'barang2_id' => $stock['barang2_id'],
                    'kemasan_id' => $stock['kemasan_id'],
                    'tipe_barang' => $l->type_barang,
                    'no_aju' => $l->no_aju,
                    'operasi' => $l->type_adjusment,
                    'stock_dokumen' => $l->stock_dokumen,
                    'qty' => $l->qty_adjusment
                ]);

                array_push($id_detail_all,  $id_detail_new);
            }
        }

        $this->adjusmentDetailModel->where('adjusment_id', $id)->whereNotIn('id', $id_detail_all)->delete();
        return response()->setJSON([
            "status" => true,
            "message" => "Adjusment berhasil diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->adjusmentModel->delete($id);
        $this->adjusmentDetailModel->where('adjusment_id', $id)->delete();

        return response()->setJSON([
            "status" => true,
            "message" => "Adjusment berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $adjusmentList = $this->adjusmentDetailModel->where('adjusment_id', $id)->where('deletedAt', null)->findAll();
        $adjusment = $this->adjusmentModel->find($id);

        // INSERT TO STOCK
        foreach ($adjusmentList as $a) {
            // HEADER
            $spesifikasiID =  $a['kemasan_id'] != 0 ? $a['kemasan_id'] : $a['barang2_id'];
            $qtyTotal =  $a['operasi'] == "PLUS" ? $a['qty'] : (-$a['qty']);
            $qty = $a['qty'];
            $status =  $a['operasi'] == "PLUS" ? "In" : "Out";
            $no_aju = $a['no_aju'] == "" ? "-" : $a['no_aju'];

            $stok = $this->stockModel->insertStok(
                $adjusment['company_id'],
                $a['warehouse_id'],
                $adjusment['divisi_id'],
                $a['tipe_barang'],
                $a['barang1_id'],
                $spesifikasiID,
                $qtyTotal,
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                $status,
                date('Y-m-d'),
                $this->this_user_id,
                "ADJUSMENT",
                $adjusment['no_adjusment'],
                $adjusment['keterangan'],
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $a['bc_id'],
                $stok,
                $stokDetail,
                $qty,
                $no_aju,
                "-",
                $a['stock_dokumen']
            );
        }

        $this->adjusmentModel->update($id, [
            'status_posting' => '1'
        ]);

        return response()->setJSON([
            'message' => "Adjusment berhasil diposting",
            'token' => csrf_hash(),
            'status' => true
        ]);
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
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisi_id = $this->request->getVar('divisi_id');

        if (empty($divisi_id)) {
            $no = $this->adjusmentModel->get_no(date('m'), date('Y'), $last_day, "", $divisi_id);
        } else {
            $divisi = $this->divisiModel->where('id', $divisi_id)->first();
            $no = $this->adjusmentModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisi_id);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
