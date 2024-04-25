<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\JasaVendorInModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\JasaVendorOutModel;
use App\Models\MetadataModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class JasaVendorIn extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $vendorModel;
    protected $jasaVendorInModel;
    protected $jasaVendorInDetailModel;
    protected $jasaVendorOutModel;
    protected $jasaVendorOutDetailModel;
    protected $warehouseModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->vendorModel = new VendorModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $this->jasaVendorOutModel = new JasaVendorOutModel();
        $this->jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('jasaVendor/in/index', $data);
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
            "start_date" => $this->request->getVar('start_date'),
            "end_date" => $this->request->getVar('end_date'),
            "no_penerimaan_surat_jalan" => $this->request->getVar("no_penerimaan_surat_jalan"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'jasa_vendor_in.company_id' => $this->this_company_id,
            'jasa_vendor_in.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->jasaVendorInModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $jasaVendorInDetail = $this->jasaVendorInDetailModel
                ->where('jasa_vendor_in_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_surat_jalan"        => $data->no_penerimaan_surat_jalan,
                "no_surat_jalan"        => str_replace(['"', ']', '['], "",  $data->multiple_jasa_vendor_out_no),
                "no_surat_jalan_vendor" => $data->no_surat_jalan_vendor == "" ? "-" : $data->no_surat_jalan_vendor,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($jasaVendorInDetail),
                "vendor_name"           => $data->vendor_name,
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
            'tanggal' => date('Y-m-d'),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
        ];
        return view('jasaVendor/in/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $jasaVendorIn = $this->jasaVendorInModel->find($id);
        if ($jasaVendorIn == null) {
            return redirect()->to('jasa-vendor-in');
        }

        $data = [
            'jasaVendorIn' => $jasaVendorIn,
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'divisi' => $this->divisiModel->where('id', $jasaVendorIn['divisi_id'])->findAll(),
            'warehouse' => $this->warehouseModel->where('id', $jasaVendorIn['warehouse_id'])->findAll()
        ];

        return view('jasaVendor/in/form', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $jasaVendorIn = $this->jasaVendorInModel->find($id);
        if ($jasaVendorIn == null) {
            return redirect()->to('jasa-vendor-in');
        }

        $selectQryJasaVendorDetail = "
            SUM(jasa_vendor_in_detail.qty_bersih) as qty_bersih,
            SUM(jasa_vendor_in_detail.qty_kotor) as qty_kotor,
            satuans.kode_satuan,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi
        ";

        $jasaVendorInDetail = $this->jasaVendorInDetailModel->select($selectQryJasaVendorDetail)
            ->join('stock', 'stock.id = jasa_vendor_in_detail.stock_in_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
            ->where('jasa_vendor_in_id', $id)
            ->groupBy('jasa_vendor_in_detail.stock_in_id')
            ->findAll();

        $data = [
            'jasaVendorIn' => $jasaVendorIn,
            'vendor' => $this->vendorModel->find($jasaVendorIn['vendor_id']),
            'jasaVendorInDetail' => $jasaVendorInDetail
        ];

        $this->dompdf->loadHtml(view('jasaVendor/in/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Jasa Vendor Barang Masuk", array("Attachment" => false));
    }

    public function createAction()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );
        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $check = $this->jasaVendorInModel->where('no_penerimaan_surat_jalan', $this->request->getVar('no_penerimaan_surat_jalan'))->first();

        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor penerimaan surat jalan sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $id = $this->jasaVendorInModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'vendor_id' => $this->request->getVar('vendor_id'),
            'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
            "no_surat_jalan_vendor" => $this->request->getVar('no_surat_jalan_vendor'),
            'no_penerimaan_surat_jalan' => $this->request->getVar('no_penerimaan_surat_jalan'),
            'multiple_jasa_vendor_out_id' =>  str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_jasa_vendor_out_id'))),
            'multiple_jasa_vendor_out_no' =>  str_replace(['\\"', '\\'], '', json_encode($jasaVendorOutNo)),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        foreach ($barangs as $b) {
            // LIST BARANG MASUK
            foreach ($b->list_barang_masuk as $c) {
                if ($c->qty_bersih != 0) {
                    $this->jasaVendorInDetailModel->insert([
                        'jasa_vendor_in_id' => $id,
                        'jasa_vendor_out_id' => $b->jasa_vendor_out_id,
                        'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id,
                        'stock_in_id' => $c->stock_in_id,
                        'bc_in_id' => $b->bc_id,
                        'no_aju_in' => $b->no_aju,
                        'qty_kotor' => $c->qty_kotor,
                        'qty_bersih' => $c->qty_bersih
                    ]);
                }
            }
        }

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($id)
        ]);
    }

    public function updateAction()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );
        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $id = decrypt($this->request->getVar('id'));

        $this->jasaVendorInModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            "no_surat_jalan_vendor" => $this->request->getVar('no_surat_jalan_vendor'),
            'multiple_jasa_vendor_out_id' =>  str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_jasa_vendor_out_id'))),
            'multiple_jasa_vendor_out_no' =>  str_replace(['\\"', '\\'], '', json_encode($jasaVendorOutNo)),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach ($barangs as $b) {
            // LIST BARANG MASUK
            foreach ($b->list_barang_masuk as $c) {
                // CHECK
                $check = $this->jasaVendorInDetailModel
                    ->where('jasa_vendor_in_id', $id)
                    ->where('jasa_vendor_out_id', $b->jasa_vendor_out_id)
                    ->where('jasa_vendor_out_detail_id', $b->jasa_vendor_out_detail_id)
                    ->where('stock_in_id', $c->stock_in_id)
                    ->first();

                if ($check != null) {
                    $this->jasaVendorInDetailModel->update($check['id'], [
                        'jasa_vendor_in_id' => $id,
                        'jasa_vendor_out_id' => $b->jasa_vendor_out_id,
                        'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id,
                        'stock_in_id' => $c->stock_in_id,
                        'bc_in_id' => $b->bc_id,
                        'no_aju_in' => $b->no_aju,
                        'qty_kotor' => $c->qty_kotor,
                        'qty_bersih' => $c->qty_bersih
                    ]);
                    array_push($id_detail_all, $check['id']);
                } else {
                    // NEW
                    // DELETE
                    $this->jasaVendorInDetailModel
                        ->where('jasa_vendor_in_id', $id)
                        ->where('jasa_vendor_out_id', $b->jasa_vendor_out_id)
                        ->where('jasa_vendor_out_detail_id', $b->jasa_vendor_out_detail_id)
                        ->where('stock_in_id', $c->stock_in_id)
                        ->delete();

                    // INSERT
                    $id_detail_new = $this->jasaVendorInDetailModel->insert([
                        'jasa_vendor_in_id' => $id,
                        'jasa_vendor_out_id' => $b->jasa_vendor_out_id,
                        'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id,
                        'stock_in_id' => $c->stock_in_id,
                        'bc_in_id' => $b->bc_id,
                        'no_aju_in' => $b->no_aju,
                        'qty_kotor' => $c->qty_kotor,
                        'qty_bersih' => $c->qty_bersih
                    ]);
                    array_push($id_detail_all,  $id_detail_new);
                }
            }
        }

        $this->jasaVendorInDetailModel->where('jasa_vendor_in_id', $id)->whereNotIn('id', $id_detail_all)->delete();

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Diupdate",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->jasaVendorInModel->delete($id);
        $this->jasaVendorInDetailModel->where('id', $id)->delete();
        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        // BARANG IN KE INVENTORI DARI VENDOR
        // INSERT INVENTORI (+)
        $jasaVendorIn = $this->jasaVendorInModel->find($id);
        $jasaVendorInDetail = $this->jasaVendorInDetailModel->where('jasa_vendor_in_id', $id)->findAll();

        foreach ($jasaVendorInDetail as $j) {
            $stock = $this->stockModel->find($j['stock_in_id']);
            $qty = $j['qty_bersih'];

            $jasaVendorOut = $this->jasaVendorOutModel->find($j['jasa_vendor_out_id']);

            $stok = $this->stockModel->insertStok(
                $jasaVendorIn['company_id'],
                $jasaVendorIn['warehouse_id'],
                $jasaVendorIn['divisi_id'],
                "bahan_baku",
                $stock['barang1_id'],
                $stock['barang2_id'],
                $qty
            );

            $checkStokDetail =  $this->stockModel->isDefinedStockSubDetail(
                $this->this_company_id,
                $jasaVendorIn['warehouse_id'],
                $jasaVendorIn['divisi_id'],
                "bahan_baku",
                $stock['barang1_id'],
                $stock['barang2_id'],
                $j['bc_in_id'],
                $j['no_aju_in'],
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
                    $j['bc_in_id'],
                    $j['stock_in_id'],
                    $stokDetail,
                    0,
                    $j['no_aju_in'],
                    "-"
                );
            }

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                "In",
                date('Y-m-d'),
                $this->this_user_id,
                "JASA VENDOR",
                $jasaVendorIn['no_penerimaan_surat_jalan'],
                $jasaVendorIn['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $j['bc_in_id'],
                $j['stock_in_id'],
                $stokDetail,
                $qty,
                $j['no_aju_in'],
                $jasaVendorOut == null ? "-" : $jasaVendorOut['no_surat_jalan']
            );
        }

        $this->jasaVendorInModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk berhasil diposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }


    public function dropdownListBarangKeluar()
    {
        $id = decrypt($this->request->getVar('id'));
        $jasaVendorOutID = json_decode($this->request->getVar('multiple_jasa_vendor_out_id'));
        if (count($jasaVendorOutID) == 0) {
            return response()->setJSON([
                'data' => [],
                'status' => true,
                'token' => csrf_hash()
            ]);
        } else {
            $id = ($id == false) ? null : $id;
            $data = $this->jasaVendorInModel->listBarang(
                $jasaVendorOutID,
                $id
            );
            return response()->setJSON([
                'data' => $data,
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    public function dropdownListBarangMasuk()
    {
        $stockID = $this->request->getVar('stock_out_id');
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

    public function dropdownDivisi()
    {
        $vendorID = $this->request->getVar('vendor_id');
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $divisiResult = [];

        foreach ($dataDivisi as $d) {
            array_push($divisiResult, $d['id']);
        }

        $result = $this->jasaVendorOutModel
            ->select('DISTINCT(divisis.id), divisis.divisi')
            ->join('divisis', 'divisis.id = jasa_vendor_out.divisi_id', 'left')
            ->whereIn('jasa_vendor_out.divisi_id', $divisiResult)
            ->where('vendor_id', $vendorID)
            ->where('status_posting', '1')
            ->where('status_closed', '0')
            ->where('jasa_vendor_out.deletedAt', null)
            ->where('divisis.deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownWarehouse()
    {
        $vendorID = $this->request->getVar('vendor_id');
        $divisiID = $this->request->getVar('divisi_id');

        $result = $this->jasaVendorOutModel
            ->select('DISTINCT(warehouses.id), warehouses.warehouse_name')
            ->join('warehouses', 'warehouses.id = jasa_vendor_out.warehouse_id', 'left')
            ->where('jasa_vendor_out.divisi_id', $divisiID)
            ->where('vendor_id', $vendorID)
            ->where('status_posting', '1')
            ->where('status_closed', '0')
            ->where('jasa_vendor_out.deletedAt', null)
            ->where('warehouses.deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownNoJasaVendorOut()
    {
        $vendorID = $this->request->getVar('vendor_id');
        $divisiID = $this->request->getVar('divisi_id');
        $warehouseID = $this->request->getVar('warehouse_id');

        $result = $this->jasaVendorOutModel
            ->where('divisi_id', $divisiID)
            ->where('warehouse_id', $warehouseID)
            ->where('vendor_id', $vendorID)
            ->where('status_posting', '1')
            ->where('status_closed', '0')
            ->where('deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getJasaVendorInNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouse_id = $this->request->getVar('warehouse_id');

        if (empty($warehouse_id)) {
            $no = $this->jasaVendorInModel->get_no(date('m'), date('Y'), $last_day, "", $warehouse_id);
        } else {
            $warehouse = $this->warehouseModel->where('id', $warehouse_id)->first();
            $no = $this->jasaVendorInModel->get_no(date('m'), date('Y'), $last_day, strtoupper($warehouse['code_warehouse']), $warehouse_id);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
