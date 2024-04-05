<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\JasaVendorOutModel;
use App\Models\MetadataModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class JasaVendorOut extends BaseController
{
    protected $vendorModel;
    protected $this_user_id;
    protected $this_company_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $jasaVendorOutModel;
    protected $jasaVendorOutDetailModel;
    protected $warehouseModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->vendorModel = new VendorModel();
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->jasaVendorOutModel = new JasaVendorOutModel();
        $this->jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('jasaVendor/out/index', $data);
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
            "no_surat_jalan" => $this->request->getVar("no_surat_jalan"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'jasa_vendor_out.company_id' => $this->this_company_id,
            'jasa_vendor_out.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->jasaVendorOutModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel
                ->where('jasa_vendor_out_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_surat_jalan"        => $data->no_surat_jalan,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($jasaVendorOutDetail),
                "vendor_name"           => $data->vendor_name,
                "status_posting"        => $data->status_posting,
                "status_closed"         => $data->status_closed == "1" ? "CLOSED" : "OPEN",
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
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),

        ];
        return view('jasaVendor/out/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $jasaVendorOut = $this->jasaVendorOutModel->find($id);

        if ($jasaVendorOut == null) {
            return redirect()->to('jasa-vendor-out');
        }

        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'jasaVendorOut' => $jasaVendorOut,
            'warehouse' => $this->warehouseModel->where('deletedAt', null)->where('divisi_id', $jasaVendorOut['divisi_id'])->orderBy('warehouse_name', "ASC")->findAll(),
            'jasaVendorOutDetail' => $this->jasaVendorOutDetailModel->getJasaVendorOutDetail($id)

        ];

        return view('jasaVendor/out/form', $data);
    }

    public function createAction()
    {
        $id = $this->jasaVendorOutModel->insert([
            'company_id' => $this->this_company_id,
            'vendor_id' => $this->request->getVar('vendor_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'tanggal' => date('Y-m-d'),
            'tipe_barang' => "bahan_baku",
            'no_kontainer' => $this->request->getVar('no_kontainer'),
            'keterangan' => $this->request->getVar('keterangan'),
        ]);

        $barang = json_decode($this->request->getVar('listBarang'));

        foreach ($barang as $b) {
            $this->jasaVendorOutDetailModel->insert([
                'jasa_vendor_out_id' => $id,
                'stock_out_id' => $b->stock_id,
                'bc_out_id' => $b->bc_id,
                'no_aju_out' => $b->no_aju,
                'stock_in_id' => $b->output->stock_id,
                'qty' => $b->qty
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil disimpan",
            'token' => csrf_hash(),
            'id' => encrypt($id)
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->jasaVendorOutModel->update($id, [
            'vendor_id' => $this->request->getVar('vendor_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_kontainer' => $this->request->getVar('no_kontainer'),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        $barang = json_decode($this->request->getVar('listBarang'));

        // get all id detail
        $id_detail_all = [];
        foreach ($barang as $b) {
            $check = $this->jasaVendorOutDetailModel
                ->where('jasa_vendor_out_id', $id)
                ->where('stock_out_id', $b->stock_id)
                ->where('bc_out_id', $b->bc_id)
                ->where('no_aju_out', $b->no_aju)
                ->first();

            if ($check == null) {
                // Belum Ada
                $this->jasaVendorOutDetailModel
                    ->where('jasa_vendor_out_id', $id)
                    ->where('stock_out_id', $b->stock_id)
                    ->where('bc_out_id', $b->bc_id)
                    ->where('no_aju_out', $b->no_aju)
                    ->delete();

                $id_detail_new = $this->jasaVendorOutDetailModel->insert([
                    'jasa_vendor_out_id' => $id,
                    'stock_out_id' => $b->stock_id,
                    'bc_out_id' => $b->bc_id,
                    'no_aju_out' => $b->no_aju,
                    'stock_in_id' => $b->output->stock_id,
                    'qty' => $b->qty
                ]);
                array_push($id_detail_all,  $id_detail_new);
            } else {
                // ada
                $this->jasaVendorOutDetailModel->update($check['id'], [
                    'jasa_vendor_out_id' => $id,
                    'stock_out_id' => $b->stock_id,
                    'bc_out_id' => $b->bc_id,
                    'no_aju_out' => $b->no_aju,
                    'stock_in_id' => $b->output->stock_id,
                    'qty' => $b->qty
                ]);
                array_push($id_detail_all, $check['id']);
            }
        }
        $this->jasaVendorOutDetailModel->where('jasa_vendor_out_id', $id)->whereNotIn('id', $id_detail_all)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil diupdate",
            'token' => csrf_hash(),
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->jasaVendorOutModel->delete($id);
        $this->jasaVendorOutDetailModel->where('jasa_vendor_out_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil dihapus",
            'token' => csrf_hash(),
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        // BARANG OUT KE VENDOR
        // Insert To Inventori (-)
        $jasaVendorOut = $this->jasaVendorOutModel->find($id);
        $jasaVendorOutDetail = $this->jasaVendorOutDetailModel->where('jasa_vendor_out_id', $id)->where('deletedAt', null)->findAll();

        foreach ($jasaVendorOutDetail as $j) {
            $stock = $this->stockModel->find($j['stock_out_id']);
            $qty = $j['qty'];

            if ($stock['tipe_barang'] == "kemasan") {
                $barang2_id = $stock['kemasan_id'];
            } else {
                $barang2_id = $stock['barang2_id'];
            }

            $stok = $this->stockModel->insertStok(
                $jasaVendorOut['company_id'],
                $jasaVendorOut['warehouse_id'],
                $jasaVendorOut['divisi_id'],
                $stock['tipe_barang'],
                $stock['barang1_id'],
                $barang2_id,
                ($qty * -1)
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                "Out",
                date('Y-m-d'),
                $this->this_user_id,
                "JASA VENDOR",
                "-",
                $jasaVendorOut['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $j['bc_out_id'],
                $j['stock_out_id'],
                $stokDetail,
                $qty,
                $j['no_aju_out'],
                $jasaVendorOut['no_surat_jalan']
            );
        }

        $this->jasaVendorOutModel->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil diposting",
            'token' => csrf_hash(),
        ]);
    }

    public function close()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->jasaVendorOutModel->update($id, ['status_closed' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil diclose",
            'token' => csrf_hash(),
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $selectQryJasaVendor = "
            jasa_vendor_out.*,
            vendors.name as vendor_name,
            vendors.address
        ";

        $jasaVendorOut = $this->jasaVendorOutModel->select($selectQryJasaVendor)
            ->join('vendors', 'vendors.id = jasa_vendor_out.vendor_id')
            ->where('jasa_vendor_out.id', $id)
            ->first();

        if ($jasaVendorOut == null) {
            return redirect()->to('jasa-vendor-out');
        }

        $selectQryJasaVendorDetail = "
            SUM(jasa_vendor_out_detail.qty) as qty,
            satuans.kode_satuan,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi
        ";

        $jasaVendorOutDetail = $this->jasaVendorOutDetailModel->select($selectQryJasaVendorDetail)
            ->join('stock', 'stock.id = jasa_vendor_out_detail.stock_out_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
            ->where('jasa_vendor_out_id', $jasaVendorOut['id'])
            ->groupBy('jasa_vendor_out_detail.stock_out_id')
            ->findAll();

        $data = [
            'jasaVendorOut' => $jasaVendorOut,
            'jasaVendorDetail' => $jasaVendorOutDetail
        ];

        $this->dompdf->loadHtml(view('jasaVendor/out/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Jasa Vendor Barang Keluar", array("Attachment" => false));
    }

    public function dropdownListOutputVendor()
    {
        $stockID = $this->request->getVar('stock_id');

        $response = array();
        if (!empty($stockID)) {
            $data = $this->stockModel->getBarangAndStock(
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

    public function getJasaVendorOutNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouse_id = $this->request->getVar('warehouse_id');

        if (empty($warehouse_id)) {
            $no = $this->jasaVendorOutModel->get_no(date('m'), date('Y'), $last_day, "", $warehouse_id);
        } else {
            $warehouse = $this->warehouseModel->where('id', $warehouse_id)->first();
            $no = $this->jasaVendorOutModel->get_no(date('m'), date('Y'), $last_day, strtoupper($warehouse['code_warehouse']), $warehouse_id);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
