<?php

namespace App\Controllers\Stuffing;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\JasaVendorOutModel;
use App\Models\MetadataModel;
use App\models\SalesOrderDetailModel;
use App\models\SalesOrderModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StuffingLokalDetailModel;
use App\Models\StuffingLokalModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
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

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "status" => $this->request->getVar("status"),
            "start_date" => $this->request->getVar('start_date'),
            "end_date" => $this->request->getVar('end_date'),
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
        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'orderForm' => $this->salesOrderModel->select('sales_order.*, customers.name as customer_name')->join('customers', 'customers.id = sales_order.id_customer')->where('sales_order.deletedAt', null)->orderBy('sales_order.no_sales_order', "ASC")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),

        ];
        return view('Stuffing/Lokal/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $stuffingLokalModel = $this->stuffingLokalModel->select('stuffing_lokal.*, customers.name as customer_name')->join('customers', 'customers.id = stuffing_lokal.customer_id')->find($id);
        $salesOrder = $this->salesOrderDetailModel
            ->select('sales_order_detail.*, barang_master_sales.id AS id_barang, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang AS kode_barang')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang')
            ->where('sales_order_detail.id_sales_order', $stuffingLokalModel['sales_order_id'])
            ->where('sales_order_detail.deletedAt', null)
            ->findAll();

        if ($stuffingLokalModel == null) {
            return redirect()->to('pengeluaran-lokal');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'orderForm' => $this->salesOrderModel->select('sales_order.*, customers.name as customer_name')->join('customers', 'customers.id = sales_order.id_customer')->where('sales_order.deletedAt', null)->orderBy('sales_order.no_sales_order', "ASC")->findAll(),
            'stuffingLokal' => $stuffingLokalModel,
            'stuffingLokalDetail' => $this->stuffingLokalDetailModel->getStuffingDetail($id),
            'salesOrder' => $salesOrder,
            'divisi' => $this->divisiModel->getDivisiAccess(),

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

        $barang = json_decode($this->request->getVar('listBarang'));

        foreach ($barang as $b) {
            $checkStock = $this->stockModel->where('id', $b->stock_id)->first();
            $this->stuffingLokalDetailModel->insert([
                'divisi_id' => $b->divisi_id,
                'warehouse_id' => $b->warehouse_id,
                'stuffing_lokal_id' => $id,
                'stock_id_warehouse' => $b->stock_id,
                'bc_id_warehouse' => $b->bc_id,
                'no_aju_warehouse' => $b->no_aju,
                'barang1_id_warehouse' => $checkStock['barang1_id'],
                'barang2_id_warehouse' => $checkStock['barang2_id'],
                'barang_id_order' => $b->output->id_barang,
                'qty' => $b->output->qty
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

        // get all id detail
        $id_detail_all = [];
        foreach ($barang as $b) {
            if (isset($b->id_stuffing_detail)) {
                $checkStock = $this->stockModel->where('id', $b->stock_id)->first();
                $this->stuffingLokalDetailModel->update($b->id_stuffing_detail, [
                    'divisi_id' => $b->divisi_id,
                    'warehouse_id' => $b->warehouse_id,
                    'stuffing_lokal_id' => $id,
                    'stock_id_warehouse' => $b->stock_id,
                    'bc_id_warehouse' => $b->bc_id,
                    'no_aju_warehouse' => $b->no_aju,
                    'barang1_id_warehouse' => $checkStock['barang1_id'],
                    'barang2_id_warehouse' => $checkStock['barang2_id'],
                    'barang_id_order' => $b->output->id_barang,
                    'qty' => $b->output->qty
                ]);
            } else {
                $checkStock = $this->stockModel->where('id', $b->stock_id)->first();
                $this->stuffingLokalDetailModel->insert([
                    'divisi_id' => $b->divisi_id,
                    'warehouse_id' => $b->warehouse_id,
                    'stuffing_lokal_id' => $id,
                    'stock_id_warehouse' => $b->stock_id,
                    'bc_id_warehouse' => $b->bc_id,
                    'no_aju_warehouse' => $b->no_aju,
                    'barang1_id_warehouse' => $checkStock['barang1_id'],
                    'barang2_id_warehouse' => $checkStock['barang2_id'],
                    'barang_id_order' => $b->output->id_barang,
                    'qty' => $b->output->qty
                ]);
            }
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
                $salesOrder['no_sales_order'],
                "-"
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $j['bc_id_warehouse'],
                $j['stock_id_warehouse'],
                $stokDetail,
                $qty,
                $j['no_aju_warehouse'],
                $stuffingLokal['no_stuffing']
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
        $this->jasaVendorOutModel->update($id, ['status_closed' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil diclose",
            'token' => csrf_hash(),
        ]);
    }

    // public function print($id)
    // {
    //     $id = decrypt($id);
    //     $selectQryJasaVendor = "
    //         jasa_vendor_out.*,
    //         vendors.name as vendor_name,
    //         vendors.address
    //     ";

    //     $jasaVendorOut = $this->jasaVendorOutModel->select($selectQryJasaVendor)
    //         ->join('vendors', 'vendors.id = jasa_vendor_out.vendor_id')
    //         ->where('jasa_vendor_out.id', $id)
    //         ->first();

    //     if ($jasaVendorOut == null) {
    //         return redirect()->to('jasa-vendor-out');
    //     }

    //     $selectQryJasaVendorDetail = "
    //         SUM(jasa_vendor_out_detail.qty) as qty,
    //         satuans.kode_satuan,
    //         barang_master.barang_name,
    //         barang_master_spesifikasi.spesifikasi
    //     ";

    //     $jasaVendorOutDetail = $this->jasaVendorOutDetailModel->select($selectQryJasaVendorDetail)
    //         ->join('stock', 'stock.id = jasa_vendor_out_detail.stock_out_id')
    //         ->join('barang_master', 'barang_master.id = stock.barang1_id')
    //         ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
    //         ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
    //         ->where('jasa_vendor_out_id', $jasaVendorOut['id'])
    //         ->groupBy('jasa_vendor_out_detail.stock_out_id')
    //         ->findAll();

    //     $data = [
    //         'jasaVendorOut' => $jasaVendorOut,
    //         'jasaVendorDetail' => $jasaVendorOutDetail
    //     ];

    //     $this->dompdf->loadHtml(view('Stuffing/Lokal/print', $data));
    //     $this->dompdf->setPaper('A4', 'portrait');
    //     $this->dompdf->render();
    //     $this->dompdf->stream("Jasa Vendor Barang Keluar", array("Attachment" => false));
    // }

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
}
