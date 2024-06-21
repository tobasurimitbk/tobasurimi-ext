<?php

namespace App\Controllers\Stuffing;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\JasaVendorOutModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderDetailModel;
use App\Models\SalesOrderModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StuffingLokalDetailModel;
use App\Models\StuffingLokalModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use App\Models\BarangMasterSalesModel;
use Dompdf\Dompdf;

class Lokal extends BaseController
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
    protected $salesOrderModel;
    protected $salesOrderDetailModel;
    protected $stuffingLokalModel;
    protected $stuffingLokalDetailModel;
    protected $BarangMasterSalesModel;
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
        $this->salesOrderModel = new SalesOrderModel();
        $this->salesOrderDetailModel = new SalesOrderDetailModel();
        $this->stuffingLokalModel = new StuffingLokalModel();
        $this->stuffingLokalDetailModel = new StuffingLokalDetailModel();
        $this->BarangMasterSalesModel = new BarangMasterSalesModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Stuffing/Lokal/index', $data);
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

        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');

        $addCondition = [
            "sort"        => $this->request->getVar("sort"),
            "sortType"    => $this->request->getVar("sortType"),
            "status"      => $this->request->getVar("status"),
            "start_date"  => $start_date ? date_format(date_create_from_format('d/m/Y', $start_date), 'Y-m-d') : "",
            "end_date"    => $end_date ? date_format(date_create_from_format('d/m/Y', $end_date), 'Y-m-d') : "",
            "no_stuffing" => $this->request->getVar("no_stuffing"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'stuffing_lokal.company_id' => $this->this_company_id,
            'stuffing_lokal.deletedAt' => null,
        ];

        $dataQry = $this->stuffingLokalModel->getList($condition, $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $stuffingLokalDetailModel = $this->stuffingLokalDetailModel
                ->where('stuffing_lokal_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_stuffing"        => $data->no_stuffing,
                "no_sales_order"     => $data->no_sales_order,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "total_item"            => count($stuffingLokalDetailModel),
                "customer_name"           => $data->customer_name,
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
        $dataAJU = $this->metaDataModel->getBCUsed('so_lokal');
        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->where('description !=', 'kemasan')->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'orderForm' => $this->salesOrderModel
                ->select('sales_order.*, customers.name as customer_name')
                ->join('customers', 'customers.id = sales_order.id_customer')
                ->where('sales_order.deletedAt', null)
                ->where('sales_order.id_company', $this->this_company_id)
                ->where('sales_order.tipe_sales_order', 'LOKAL')
                ->where('sales_order.used', 'NOT USED')
                ->orderBy('sales_order.no_sales_order', "ASC")
                ->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            "dataAJU" => $dataAJU,
        ];
        return view('Stuffing/Lokal/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $stuffingLokalModel = $this->stuffingLokalModel
            ->select('stuffing_lokal.*, sales_order.bc_type, customers.name as customer_name')
            ->join('customers', 'customers.id = stuffing_lokal.customer_id')
            ->join('sales_order', 'sales_order.id = stuffing_lokal.sales_order_id')
            ->find($id);
        $salesOrder = $this->salesOrderDetailModel
            ->select('sales_order_detail.*, sales_order.bc_type, barang_master_sales.id AS id_barang, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang AS kode_barang')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang')
            ->join('sales_order', 'sales_order.id = sales_order_detail.id_sales_order')
            ->where('sales_order_detail.id_sales_order', $stuffingLokalModel['sales_order_id'])
            ->where('sales_order_detail.deletedAt', null)
            ->findAll();

        $dataAJU = $this->metaDataModel->getBCUsed('so_lokal');

        if ($stuffingLokalModel == null) {
            return redirect()->to('pengeluaran-lokal');
        }

        // $getStuffingLokalDetail = $this->stuffingLokalDetailModel->getStuffingDetail($id);
        // var_dump($getStuffingLokalDetail);
        // die();

        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->where('description !=', 'kemasan')->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'orderForm' => $this->salesOrderModel->select('sales_order.*, customers.name as customer_name')->join('customers', 'customers.id = sales_order.id_customer')->where('sales_order.deletedAt', null)->orderBy('sales_order.no_sales_order', "ASC")->findAll(),
            'stuffingLokal' => $stuffingLokalModel,
            'stuffingLokalDetail' =>  $this->stuffingLokalDetailModel->getStuffingDetail($id),
            'salesOrder' => $salesOrder,
            'divisi' => $this->divisiModel->getDivisiAccess(),
            "dataAJU" => $dataAJU,
        ];

        return view('Stuffing/Lokal/form', $data);
    }

    public function createAction()
    {
        $id = $this->stuffingLokalModel->insert([
            'company_id' => $this->this_company_id,
            'customer_id' => $this->request->getVar('customer_id'),
            'sales_order_id' => $this->request->getVar('sales_order_id'),
            'no_stuffing' => $this->request->getVar('no_stuffing'),
            'tanggal' => date('Y-m-d'),
        ]);

        $this->salesOrderModel->update($this->request->getVar('sales_order_id'), [
            'used' => 'USED'
        ]);

        $barang = json_decode($this->request->getVar('listBarang'));



        foreach ($barang as $b) {

            $checkStock = $this->stockModel->where('id', $b->stock_id)->first();
            $this->stuffingLokalDetailModel->insert([
                'divisi_id' => $b->divisi_id,
                'warehouse_id' => $b->warehouse_id,
                'stuffing_lokal_id' => $id,
                'stock_id_warehouse' => $b->stock_id,
                'stock_dokumen' => $b->stock_dokumen,
                'no_dokumen_1' => $b->no_dokumen_1,
                'no_dokumen_2' => $b->no_dokumen_2,
                'bc_id_warehouse' => $b->bc_id,
                'no_aju_warehouse' => $b->no_aju,
                'barang1_id_warehouse' => $checkStock['barang1_id'],
                'barang2_id_warehouse' => $checkStock['barang2_id'],
                'barang_id_order' => $b->output->id_barang,
                'qty' => $b->qty,
                'stok_total' => $b->stok_total
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Lokal berhasil disimpan",
            'token' => csrf_hash(),
            'id' => encrypt($id)
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $barang = json_decode($this->request->getVar('listBarang'));
        // var_dump($barang);
        // die();

        $this->stuffingLokalDetailModel->where('stuffing_lokal_id', $id)->delete();
        // get all id detail
        $id_detail_all = [];
        foreach ($barang as $b) {
            $checkStock = $this->stockModel->where('id', $b->stock_id)->first();
            $this->stuffingLokalDetailModel->insert([
                'divisi_id' => $b->divisi_id,
                'warehouse_id' => $b->warehouse_id,
                'stuffing_lokal_id' => $id,
                'stock_id_warehouse' => $b->stock_id,
                'stock_dokumen' => $b->stock_dokumen,
                'no_dokumen_1' => $b->no_dokumen_1,
                'no_dokumen_2' => $b->no_dokumen_2,
                'bc_id_warehouse' => $b->bc_id,
                'no_aju_warehouse' => $b->no_aju,
                'barang1_id_warehouse' => $checkStock['barang1_id'],
                'barang2_id_warehouse' => $checkStock['barang2_id'],
                'barang_id_order' => $b->output->id_barang,
                'qty' => $b->qty,
                'stok_total' => $b->stok_total
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Lokal berhasil diupdate",
            'token' => csrf_hash(),
        ]);
    }


    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $checkStuffing = $this->stuffingLokalModel->find($id);
        $this->salesOrderModel->update($checkStuffing['sales_order_id'], [
            'used' => 'NOT USED'
        ]);
        $this->stuffingLokalModel->delete($id);
        $this->stuffingLokalDetailModel->where('stuffing_lokal_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Lokal berhasil dihapus",
            'token' => csrf_hash(),
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        // BARANG OUT KE VENDOR
        // Insert To Inventori (-)
        $stuffingLokal = $this->stuffingLokalModel->find($id);
        $salesOrder = $this->salesOrderModel->find($stuffingLokal['sales_order_id']);
        $stuffingLokalDetail = $this->stuffingLokalDetailModel->where('stuffing_lokal_id', $id)->where('deletedAt', null)->findAll();




        foreach ($stuffingLokalDetail as $j) {
            $stock = $this->stockModel->find($j['stock_id_warehouse']);

            $qty = $j['qty'];

            if ($stock['tipe_barang'] == "kemasan") {
                $barang2_id = $stock['kemasan_id'];
            } else {
                $barang2_id = $stock['barang2_id'];
            }

            //ngurangin
            $stok = $this->stockModel->insertStok(
                $stuffingLokal['company_id'],
                $j['warehouse_id'],
                $j['divisi_id'],
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
                "PENJUALAN",
                // $salesOrder['no_sales_order'],
                $j['no_dokumen_1'],
                "-"
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $j['bc_id_warehouse'],
                $j['stock_id_warehouse'],
                $stokDetail,
                $qty,
                $j['no_aju_warehouse'],
                $stuffingLokal['no_stuffing'],
                $j['stock_dokumen']
            );
        }

        $this->stuffingLokalModel->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Lokal berhasil diposting",
            'token' => csrf_hash(),
        ]);
    }

    public function close()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->stuffingLokalModel->update($id, ['status_closed' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Lokal berhasil diclose",
            'token' => csrf_hash(),
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $stuffingLokalModel = $this->stuffingLokalModel
            ->select('stuffing_lokal.*, sales_order.bc_type, customers.name as customer_name, sales_order.no_sales_order')
            ->join('customers', 'customers.id = stuffing_lokal.customer_id')
            ->join('sales_order', 'sales_order.id = stuffing_lokal.sales_order_id')
            ->find($id);
        $salesOrder = $this->salesOrderDetailModel
            ->select('sales_order_detail.*, sales_order.bc_type, barang_master_sales.id AS id_barang, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang AS kode_barang')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang')
            ->join('sales_order', 'sales_order.id = sales_order_detail.id_sales_order')
            ->where('sales_order_detail.id_sales_order', $stuffingLokalModel['sales_order_id'])
            ->where('sales_order_detail.deletedAt', null)
            ->findAll();

        if ($stuffingLokalModel['bc_type'] == 0) {
            $dataAJU = "No Pabean";
        } else {
            $getDataAju = $this->metaDataModel->select('value')->where('id', $stuffingLokalModel['bc_type'])->first();
            $dataAJU = $getDataAju['value'];
        }

        if ($stuffingLokalModel == null) {
            return redirect()->to('pengeluaran-lokal');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->where('description !=', 'kemasan')->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),

            'stuffingLokal' => $stuffingLokalModel,
            'stuffingLokalDetail' => $this->stuffingLokalDetailModel->getStuffingDetail($id),
            'salesOrder' => $salesOrder,
            'divisi' => $this->divisiModel->getDivisiAccess(),
            "dataAJU" => $dataAJU,
        ];

        $this->dompdf->loadHtml(view('Stuffing/Lokal/print', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream("Pengeluaran Lokal", array("Attachment" => false));
    }

    public function dropdownListOrder()
    {
        $data = $this->salesOrderDetailModel
            ->select('sales_order_detail.*, barang_master_sales.id AS id_barang, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang AS kode_barang')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang')
            ->where('sales_order_detail.id_sales_order', $this->request->getVar('sales_order_id'))
            ->where('sales_order_detail.deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $data
        ]);
    }

    public function getStuffingLokalNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $no = $this->stuffingLokalModel->get_no(date('m'), date('Y'), $last_day, "");
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }


    //handel kemasan
    public function getAllKemasan()
    {
        //Get Barang
        /*$responseBarang = curl_request("GET", "/barang/all?idCompany=$this->this_company_id", $this->token);

        $dataBarang = [];
        if ($responseBarang["code"] === 200) {
            $dataBarang = json_decode($responseBarang["body"])->data;
        }*/
        $dataBarang = $this->BarangMasterSalesModel
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'left')
            ->select('barang_master_sales.*')
            ->select('barang_master_sales.id as id_barang')
            ->select('barang_master_sales.kode_barang as kode_barang')
            ->select('barang_master_sales.barang_name as nama_barang')
            ->select('barang_master_sales.harga_pokok as harga_pokok')
            ->select('barang_master_sales.harga_jual as harga_jual')
            ->select('barang_master_sales.status_ppn as statusppn')
            ->select('satuans.nama_satuan as nama_satuan')
            ->where('type_barang_sales', 'LOKAL')
            ->where('type_barang', 'kemasan')
            ->where('barang_master_sales.deletedAt', null)
            ->where('company_id', $this->this_company_id)
            ->groupBy('id_barang')
            ->findAll();



        $data = [
            "dataBarang" => $dataBarang,
        ];

        echo json_encode($data);
        return;
    }

    public function createKemasan()
    {

        $getSalesOrderDetail = $this->salesOrderDetailModel
            ->select('sales_order_detail.id')
            ->where('sales_order_detail.id_sales_order', $this->request->getVar('sales_order_id'))
            ->where('sales_order_detail.id_barang', $this->request->getVar('id_barang'))
            ->where('sales_order_detail.deletedAt', null)
            ->first();

        if ($getSalesOrderDetail) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kemasan sudah ada"
            ]);
        }

        $valueKemasan = [
            "id_sales_order"        => $this->request->getVar('sales_order_id'),
            "id_barang"             => $this->request->getVar('id_barang'),
            "qty"                   => number_format($this->request->getVar('qty'), 2, '.', ''),
            "qty_sekarang"          => number_format($this->request->getVar('qty'), 2, '.', ''),
            "keterangan"            => $this->request->getVar('keterangan'),
            "tax"                   => $this->request->getVar('statusppn'),
            "tipe_input"            => "stuffing",


        ];
        $this->salesOrderDetailModel->save($valueKemasan);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Kemasan berhasil ditambahkan"
        ]);
    }

    public function deleteKemasan()
    {
        try {

            $id = $this->request->getPost("id");
            if (!empty($id)) {
                $this->salesOrderDetailModel->delete($id);
                $data = [
                    "status"     => true,
                    "message"    => "Data Success Dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {

            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }
}
