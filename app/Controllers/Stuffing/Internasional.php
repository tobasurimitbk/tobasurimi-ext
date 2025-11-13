<?php

namespace App\Controllers\Stuffing;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\JasaVendorOutModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderDetailModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StuffingInternasionalDetailModel;
use App\Models\StuffingInternasionalModel;
use App\Models\StuffingLokalDetailModel;
use App\Models\StuffingLokalModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use App\Models\BarangMasterSalesModel;
use App\Models\SalesOrderExportSpecsModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampModel;
use Dompdf\Dompdf;

class Internasional extends BaseController
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
    protected $stuffingInternasionalModel;
    protected $stuffingInternasionalDetailModel;
    protected $BarangMasterSalesModel;
    protected $dompdf;
    protected $salesOrderExportSpecsModel;

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
        $this->salesOrderModel = new SalesOrderExportModel();
        $this->salesOrderDetailModel = new SalesOrderExportDetailModel();
        $this->stuffingInternasionalModel = new StuffingInternasionalModel();
        $this->stuffingInternasionalDetailModel = new StuffingInternasionalDetailModel();
        $this->BarangMasterSalesModel = new BarangMasterSalesModel();
        $this->salesOrderExportSpecsModel = new SalesOrderExportSpecsModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Stuffing/Internasional/index', $data);
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
            'stuffing_internasional.company_id' => $this->this_company_id,
            'stuffing_internasional.deletedAt' => null,
        ];

        $dataQry = $this->stuffingInternasionalModel->getList($condition, $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $stuffingInternasionalDetailModel = $this->stuffingInternasionalDetailModel
                ->where('stuffing_internasional_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_stuffing"        => $data->no_stuffing,
                "no_sales_order"     => $data->no_sales_order,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "total_item"            => count($stuffingInternasionalDetailModel),
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
        $dataAJU = $this->metaDataModel->getBCUsed('so_internasional');
        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->where('description !=', 'kemasan')->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'orderForm' => $this->salesOrderModel
                ->select('sales_order_export.*, customers.name as customer_name, sales_contract.customer_id')
                ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                ->join('customers', 'customers.id = sales_contract.customer_id')
                ->where('sales_order_export.deletedAt', null)
                // ->where('sales_order_export.status', 'POSTED')
                ->where('sales_order_export.used', 'NOT USED')
                // ->where('sales_order_export.company_id', $this->this_company_id)
                ->orderBy('sales_order_export.sales_order_export_no', "ASC")
                ->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            "dataAJU" => $dataAJU,
        ];
        return view('Stuffing/Internasional/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $stuffingInternasionalModel = $this->stuffingInternasionalModel
            ->select('stuffing_internasional.*, sales_order_export.bc_type, sales_order_export.sales_order_export_id, customers.name as customer_name')
            ->join('customers', 'customers.id = stuffing_internasional.customer_id')
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = stuffing_internasional.sales_order_export_id')
            ->find($id);
        $dataSalesExport = $this->salesOrderModel
            ->where('sales_order_export.sales_order_export_id', $stuffingInternasionalModel['sales_order_export_id'])
            ->first();
        $salesOrder =  $this->salesOrderModel
            ->getDetailSalesKontrakInOrderForm(
                $dataSalesExport['sales_contract_id'],
                $stuffingInternasionalModel['sales_order_export_id']
            );
        // $salesOrder = $this->salesOrderDetailModel
        //     ->select('sales_order_detail_export.*, barang_master_sales.id AS id_barang, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang AS kode_barang')
        //     ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail_export.barang_id')
        //     ->where('sales_order_detail_export.sales_order_export_id', $stuffingInternasionalModel['sales_order_export_id'])
        //     ->where('sales_order_detail_export.deletedAt', null)
        //     ->findAll();
      
        $dataAJU = $this->metaDataModel->getBCUsed('so_internasional');

        if ($stuffingInternasionalModel == null) {
            return redirect()->to('pengeluaran-internasional');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->where('description !=', 'kemasan')->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'orderForm' => $this->salesOrderModel
                ->select('sales_order_export.*, customers.name as customer_name, sales_contract.customer_id')
                ->join('sales_contract', 'sales_contract.id = sales_order_export.sales_contract_id')
                ->join('customers', 'customers.id = sales_contract.customer_id')
                ->where('sales_order_export.deletedAt', null)
                ->where('sales_order_export.company_id', $this->this_company_id)
                ->orderBy('sales_order_export.sales_order_export_no', "ASC")
                ->findAll(),
            'stuffingInternasional' => $stuffingInternasionalModel,
            'stuffingInternasionalDetail' => $this->stuffingInternasionalDetailModel->getStuffingDetail($id),
            'salesOrder' => $salesOrder,
            'divisi' => $this->divisiModel->getDivisiAccess(),
            "dataAJU" => $dataAJU,
        ];

        return view('Stuffing/Internasional/form', $data);
    }

    public function createAction()
    {
        $id = $this->stuffingInternasionalModel->insert([
            'company_id' => $this->this_company_id,
            'customer_id' => $this->request->getVar('customer_id'),
            'sales_order_export_id' => $this->request->getVar('sales_order_id'),
            'no_stuffing' => $this->request->getVar('no_stuffing'),
            'tanggal' => date('Y-m-d'),
        ]);

        $this->salesOrderModel->update($this->request->getVar('sales_order_id'), [
            'used' => 'USED'
        ]);

        $barang = json_decode($this->request->getVar('listBarang'));

        foreach ($barang as $b) {
            $this->stuffingInternasionalDetailModel->insert([
                'divisi_id' => $b->divisi_id,
                'warehouse_id' => $b->warehouse_id,
                'stuffing_internasional_id' => $id,
                'stock_id_warehouse' => $b->stock_id,
                'stock_detail_id' => $b->id,
                'barang_id_order' => $b->output->id_barang,
                'qty' => $b->qty,
                'stok_total' => $b->stok_total
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Internasional berhasil disimpan",
            'token' => csrf_hash(),
            'id' => encrypt($id)
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $barang = json_decode($this->request->getVar('listBarang'));

        $this->stuffingInternasionalDetailModel->where('stuffing_internasional_id', $id)->delete();

        // get all id detail
        $id_detail_all = [];
        foreach ($barang as $b) {

            $checkStock = $this->stockModel->where('id', $b->stock_id)->first();
            $this->stuffingInternasionalDetailModel->insert([
                'divisi_id' => $b->divisi_id,
                'warehouse_id' => $b->warehouse_id,
                'stuffing_internasional_id' => $id,
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
                'stok_total' => $b->stok_total,

            ]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Internasional berhasil diupdate",
            'token' => csrf_hash(),
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $checkStuffing = $this->stuffingInternasionalModel->find($id);
        $this->salesOrderModel->update($checkStuffing['sales_order_export_id'], [
            'used' => 'NOT USED'
        ]);
        $this->stuffingInternasionalModel->delete($id);
        $this->stuffingInternasionalDetailModel->where('stuffing_internasional_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Internasional berhasil dihapus",
            'token' => csrf_hash(),
        ]);
    }

    public function posting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        $id = decrypt($this->request->getVar('id'));

        $stockRevampModel = new StockRevampModel();

        // BARANG OUT KE VENDOR
        // Insert To Inventori (-)
        $stuffingInternasional = $this->stuffingInternasionalModel->find($id);
        $salesOrder = $this->salesOrderModel->find($stuffingInternasional['sales_order_export_id']);
        $stuffingInternasionalDetail = $this->stuffingInternasionalDetailModel->where('stuffing_internasional_id', $id)->where('deletedAt', null)->findAll();

        foreach ($stuffingInternasionalDetail as $j) {
            $data = [
                    "stock_detail_id" => $j['stock_detail_id'],
                    "qty_digunakan" => $j['qty'],
                    "no_dokumen" => $salesOrder['sales_order_export_no'],
                ];

                // Panggil model - jika gagal akan throw exception
                $result = $stockRevampModel->outStockRevamp($db, $data);
                
                if (!$result) {
                    throw new \Exception("Gagal memproses stock untuk detail ID: {$j['stock_out_detail_id']}");
                }
        }

        $this->stuffingInternasionalModel->update($id, ['status_posting' => '1']);
        $db->transCommit();
        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Internasional berhasil diposting",
            'token' => csrf_hash(),
        ]);
    }

    public function close()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->stuffingInternasionalModel->update($id, ['status_closed' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengeluaran Internasional berhasil diclose",
            'token' => csrf_hash(),
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $stuffingInternasionalModel = $this->stuffingInternasionalModel
            ->select('stuffing_internasional.*, sales_order_export.bc_type, customers.name as customer_name, sales_order_export.sales_order_export_no')
            ->join('sales_order_export', 'sales_order_export.sales_order_export_id = stuffing_internasional.sales_order_export_id')
            ->join('customers', 'customers.id = stuffing_internasional.customer_id')
            ->find($id);


        $salesOrder = $this->salesOrderDetailModel
            ->select('sales_order_detail_export.*, barang_master_sales.id AS id_barang, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang AS kode_barang')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail_export.barang_id')
            ->where('sales_order_detail_export.sales_order_export_id', $stuffingInternasionalModel['sales_order_export_id'])
            ->where('sales_order_detail_export.deletedAt', null)
            ->findAll();
        if ($stuffingInternasionalModel['bc_type'] == 0) {
            $dataAJU = "No Pabean";
        } else {
            $getDataAju = $this->metaDataModel->select('value')->where('id', $stuffingInternasionalModel['bc_type'])->first();
            $dataAJU = $getDataAju['value'];
        }



        if ($stuffingInternasionalModel == null) {
            return redirect()->to('pengeluaran-internasional');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->where('description !=', 'kemasan')->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'stuffingInternasional' => $stuffingInternasionalModel,
            'stuffingInternasionalDetail' => $this->stuffingInternasionalDetailModel->getStuffingDetail($id),
            'salesOrder' => $salesOrder,
            'divisi' => $this->divisiModel->getDivisiAccess(),
            "dataAJU" => $dataAJU,
        ];

        $this->dompdf->loadHtml(view('Stuffing/Internasional/print', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream("Pengeluaran Internasional", array("Attachment" => false));
    }

    public function dropdownListOrder()
    {
        // $data = $this->salesOrderDetailModel
        //     ->select('sales_order_detail_export.*, barang_master_sales.id AS id_barang, barang_master_sales.barang_name AS nama_barang, barang_master_sales.kode_barang AS kode_barang')
        //     ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail_export.barang_id')
        //     ->where('sales_order_detail_export.sales_order_export_id', $this->request->getVar('sales_order_id'))
        //     ->where('sales_order_detail_export.deletedAt', null)
        //     ->findAll();

        $dataSalesExport = $this->salesOrderModel
            ->where('sales_order_export.sales_order_export_id', $this->request->getVar('sales_order_id'))
            ->first();
        $dataSalesExportDetail =  $this->salesOrderModel
            ->getDetailSalesKontrakInOrderForm(
                $dataSalesExport['sales_contract_id'],
                $this->request->getVar('sales_order_id')
            );

        $dataSalesExportSpecs = $this->salesOrderModel->getSalesOrderSpecs($this->request->getVar('sales_order_id'));

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $dataSalesExportDetail,
        ]);
    }

    public function getStuffingLokalNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $no = $this->stuffingInternasionalModel->get_no(date('m'), date('Y'), $last_day, "");
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
            ->where('type_barang_sales', 'EKSPOR')
            ->where('type_barang', 'kemasan')
            ->where('barang_master_sales.deletedAt', null)
            ->where('barang_master_sales.company_id', $this->this_company_id)
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
            ->select('sales_order_detail_export.sales_order_export_id')
            ->where('sales_order_detail_export.sales_order_export_id', $this->request->getVar('sales_order_id'))
            ->where('sales_order_detail_export.barang_id', $this->request->getVar('id_barang'))
            ->where('sales_order_detail_export.deletedAt', null)
            ->first();

        if ($getSalesOrderDetail) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Kemasan sudah ada"
            ]);
        }

        $getBarang = $this->BarangMasterSalesModel
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'left')
            ->select('barang_master_sales.*')
            ->select('barang_master_sales.id as id_barang')
            ->select('barang_master_sales.kode_barang as kode_barang')
            ->select('barang_master_sales.barang_name as nama_barang')
            ->select('barang_master_sales.harga_pokok as harga_pokok')
            ->select('barang_master_sales.harga_jual as harga_jual')
            ->select('barang_master_sales.status_ppn as statusppn')
            ->select('satuans.nama_satuan as nama_satuan')
            ->where('barang_master_sales.id', $this->request->getVar('id_barang'))
            ->where('barang_master_sales.deletedAt', null)
            ->groupBy('id_barang')
            ->first();



        $valueKemasan = [
            "sales_order_export_id"  => $this->request->getVar('sales_order_id'),
            "barang_id"             => $this->request->getVar('id_barang'),
            "barang_name"             => $getBarang['barang_name'],
            "barang_kode"             => $getBarang['kode_barang'],
            "satuan_id"             => $getBarang['satuan_id'],
            "qty"                   => number_format($this->request->getVar('qty'), 2, '.', ''),


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



    public function getListStockByStockID()
    {
        $stockRevampDetailModel = new StockRevampDetailModel();

        if (!empty($this->request->getVar('stock_id'))) {

            $condition = [
                'stock_revamp.id' => $this->request->getVar('stock_id'),
            ];

            $dataResult = $stockRevampDetailModel->getStockListWithAddConditionForStuffing(
                $condition,
            );

            
            $resultArr = array();

            $metaDataModel = new MetadataModel();

            for ($i = 0; $i < count($dataResult); $i++) {
                            $bcType = $metaDataModel->find($dataResult[$i]['bc_id']);
                            $dataResult[$i]['type_barang'] = ucwords(str_replace('_', ' ', $dataResult[$i]['type_barang']));
                            $dataResult[$i]['po_no'] = $dataResult[$i]['po_no'];
                            $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                            $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                            $dataResult[$i]['barang'] = strtoupper($dataResult[$i]['barang']);
                            $dataResult[$i]['stock_date'] = $dataResult == null ? "-" : date('d/m/Y', strtotime($dataResult[$i]['po_date']));
                            $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                            $dataResult[$i]['stok_total_bersih']          = round((float)$dataResult[$i]['stok_total_bersih'], 2);
                            $dataResult[$i]['stok_total_diterima'] = round((float)$dataResult[$i]['stok_total_diterima'], 2);
                            $dataResult[$i]['total_penerimaan']    = round((float)$dataResult[$i]['total_penerimaan'], 2);
                            $dataResult[$i]['stok_total_kotor']    = round((float)$dataResult[$i]['stok_total_diterima'] - (float)$dataResult[$i]['stok_total_bersih'], 2);


                            array_push($resultArr, $dataResult[$i]);
                            
            }


            return response()->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }

}
