<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;

use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\RMImportPOModel;
use App\Models\RMImportPODetailModel;
use App\Models\SupplierModel;
use App\Models\StockDetailModel;
use App\Models\WarehousesModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\UpdateStockPurchase;
use App\Models\UpdateStockPurchaseDetail;
use App\Models\KemasanModel;
use App\Models\TaxModel;
use App\Models\DivisisModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampHistoryModel;
use App\Models\StockRevampLogModel;
use App\Models\StockRevampModel;
use DateTime;
use Dompdf\Dompdf;

class UpdateStockBahanBaku extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $barangModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $kemasanModel;
    protected $metadataModel;
    protected $divisiModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $rmImportPOModel;
    protected $rmImportPODetailModel;
    protected $supplierModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $warehousesModel;
    protected $satuanModel;
    protected $taxModel;
    protected $beaCukaiModel;
    protected $updateStockPurchase;
    protected $updateStockPurchaseDetail;
    protected $rmPurchaseOrder;
    protected $stockRevampModel;
    protected $stockRevampDetailModel;
    protected $stockRevampHistoryModel;
    protected $stockRevampLogModel;

    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;

        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->barangModel = new BarangModel();
        $this->metadataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->rmImportPOModel = new RMImportPOModel();
        $this->rmPurchaseOrder = new RMPurchaseOrderModel();
        $this->rmImportPODetailModel = new RMImportPODetailModel();
        $this->supplierModel = new SupplierModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->taxModel = new TaxModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->kemasanModel = new KemasanModel();
        $this->updateStockPurchase = new UpdateStockPurchase();
        $this->updateStockPurchaseDetail = new UpdateStockPurchaseDetail();
        $this->stockRevampDetailModel = new StockRevampDetailModel();
        $this->stockRevampModel = new StockRevampModel();
        $this->stockRevampLogModel = new StockRevampLogModel();
        $this->stockRevampHistoryModel = new StockRevampHistoryModel();

        $this->dompdf = new Dompdf();
    }


    public function allupdateStockBahanBaku()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sorttype" => $this->request->getGet("sortType"),
            // "statuspenerimaan" => "IMPORT",
            "status" => $this->request->getGet("status"),
            // "status_bc" => $this->request->getGet("status_bc"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $condition = [
            "update_stock_purchase.company_id"        => $this->this_company_id,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status" => $this->request->getGet("status"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $penerimaanBarangData = $this->updateStockPurchase->getList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($penerimaanBarangData['data'] as $data) {

            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "warehouse_name"        => $data->warehouse_name,
                "divisi"                => $data->divisi,
                // "tipe_bahan"            => $data->tipe_bahan,
                "createdAt"             => $data->createdAt ? date("d/m/Y", strtotime($data->createdAt)) : "",
                // "validation_date"       => $data->validation_date ? date("d/m/Y", strtotime($data->validation_date)) : "",
                "supplier_name"         => $data->supplier_name,
                "warehouse_name"        => $data->warehouse_name,
                // "itemCount"             => $data->itemCount,
                // "multiple_po_no"        => $data->no_po,
                "status_posting"           => $data->status_posting,
                // "status_bc"             => $status_bc
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $penerimaanBarangData['totalData'],
            "recordsFiltered"   => $penerimaanBarangData['totalFilteredData'],
            "data"              => $dataPenerimaanBarang,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function updateStockBahanBaku()
    {
        return view('Warehouse/updateStockBahanBaku/bahanBaku/index');
    }

    public function createupdateStockBahanBaku()
    {
        $data = [
            'tipeBarang' => $this->metadataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'tanggal' => date('Y-m-d'),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'supplier' => $this->supplierModel->getSupplierAll()
        ];

        return view('Warehouse/updateStockBahanBaku/bahanBaku/form', $data);
    }

    public function getByIdupdateStockBahanBaku($id = null)
    {
        $data = [
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'supplier' => $this->supplierModel->getSupplierAll(),
            'updateStockData' => $this->updateStockPurchase
                    ->where('update_stock_purchase.id', decrypt($id))
                    ->select('update_stock_purchase.*, barang_master.barang_name')
                    ->join('barang_master', 'barang_master.id = update_stock_purchase.master_barang_id', 'left')
                    ->first(),
        ];
 
        return view('Warehouse/updateStockBahanBaku/bahanBaku/form', $data);
    }


    public function getListPO()
    {
        $divisi_id = $this->request->getVar('divisi_id');
        $warehouse_id = $this->request->getVar('warehouse_id');
        $supplier_id = $this->request->getVar('supplier_id');
        $poModel = new RMPurchaseOrderModel();
       

            $dataResult = $poModel->getPOList(
                $divisi_id,
                $warehouse_id,
                $supplier_id,
            );

            return response()->setJSON([
                'data' => $dataResult,
            ]);
        
    }

    public function getListStock()
    {
        $tanggal_po_awal   = $this->request->getVar('tanggal_po_awal');
        $tanggal_po_akhir  = $this->request->getVar('tanggal_po_akhir');
        $supplier_id       = $this->request->getVar('supplier_id');
        $warehouse_id      = $this->request->getVar('warehouse_id');
        $divisi_id         = $this->request->getVar('divisi_id');
        $barang_id         = $this->request->getVar('barang_id');
        $spesifikasi_id         = $this->request->getVar('spesifikasi_id');

        // siapkan condition kosong dulu
        $condition = [
            'rm_purchase_orders.warehouse_id' => $warehouse_id,
            'rm_purchase_orders.divisi_id' => $divisi_id,
            'rm_purchase_orders.supplier_id' => $supplier_id,
            'stock_revamp.barang_master_id' => $barang_id,
            'rm_purchase_orders.po_date >=' => DateTime::createFromFormat('d/m/Y', $tanggal_po_awal)->format('Y-m-d'),
            'rm_purchase_orders.po_date <=' => DateTime::createFromFormat('d/m/Y', $tanggal_po_akhir)->format('Y-m-d'),
            'stock_revamp_detail.reference_type' => 'LPB',
            'stock_revamp_detail.deletedAt' => NULL,
        ];

        $dataResult = $this->stockRevampDetailModel->getStockListWithAddConditionForUpdateStock($condition, $spesifikasi_id);    
          
        $resultArr = array();
           
        // Khsus Dari Supplier
        for ($i = 0; $i < count($dataResult); $i++) {
                    $bcType = $this->metadataModel->find($dataResult[$i]['bc_id']);
                    $dataResult[$i]['po_no'] = $dataResult[$i]['po_no'];
                    $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                    $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                    $dataResult[$i]['barang'] = strtoupper($dataResult[$i]['barang']);
                    $dataResult[$i]['stock_date'] = $dataResult == null ? "-" : date('d/m/Y', strtotime($dataResult[$i]['po_date']));
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                    $dataResult[$i]['stok_total_kotor'] = floatval($dataResult[$i]['stok_total_diterima'] -$dataResult[$i]['stok_total']);
                    $dataResult[$i]['stok_total_diterima'] = floatval($dataResult[$i]['stok_total_diterima']);
                    $dataResult[$i]['total_penerimaan'] = floatval($dataResult[$i]['total_penerimaan']);

                    array_push($resultArr, $dataResult[$i]);
                    
        }
            

        return response()->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
        ]);
        
    }


     public function getListStockById()
    {
        $id   = decrypt($this->request->getVar('id'));
        $tanggal_po_awal  = $this->request->getVar('tanggal_po_awal');
        $tanggal_po_akhir  = $this->request->getVar('tanggal_po_akhir');
        $supplier_id       = $this->request->getVar('supplier_id');
        $warehouse_id      = $this->request->getVar('warehouse_id');
        $divisi_id         = $this->request->getVar('divisi_id');
        $barang_id         = $this->request->getVar('barang_id');
        $spesifikasi_id         = $this->request->getVar('spesifikasi_id');

      
        $dataResult = $this->stockRevampDetailModel->getStockListWithAddConditionForUpdateStocWithId($id);    
        $resultArr = array();
           
        // Khsus Dari Supplier
        for ($i = 0; $i < count($dataResult); $i++) {
                    $bcType = $this->metadataModel->find($dataResult[$i]['bc_id']);
                    $dataResult[$i]['po_no'] = $dataResult[$i]['po_no'];
                    $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                    $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                    $dataResult[$i]['barang'] = strtoupper($dataResult[$i]['barang']);
                    $dataResult[$i]['stock_date'] = $dataResult == null ? "-" : date('d/m/Y', strtotime($dataResult[$i]['po_date']));
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                    $dataResult[$i]['stok_total_kotor'] = $dataResult[$i]['stok_total_kotor'] = number_format(
                                                            $dataResult[$i]['stok_total_diterima'] - $dataResult[$i]['stok_total'], 
                                                            2, '.', ''
                                                        );
                    $dataResult[$i]['stok_total_diterima'] = floatval($dataResult[$i]['stok_total_diterima']);
                    $dataResult[$i]['total_penerimaan'] = floatval($dataResult[$i]['total_penerimaan']);

                    array_push($resultArr, $dataResult[$i]);
                    
        }
            

        return response()->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
        ]);
        
    }

    public function saveStockBahanBakuAction()
    {
        $stockDetail = json_decode($this->request->getVar('listBarang'));
        $tanggal_po_awal   = $this->request->getVar('tanggal_po_awal');
        $tanggal_po_akhir  = $this->request->getVar('tanggal_po_akhir');
        $supplier_id       = $this->request->getVar('supplier_id');
        $warehouse_id      = $this->request->getVar('warehouse_id');
        $divisi_id         = $this->request->getVar('divisi_id');
        $barang_id         = $this->request->getVar('barang_id');
        $spesifikasi_id         = $this->request->getVar('spesifikasi_id');
        

        $poID = $this->updateStockPurchase->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' =>  $divisi_id,
            'supplier_id' => $supplier_id,
            'warehouse_id' => $warehouse_id,
            'tanggal_awal' => DateTime::createFromFormat('d/m/Y', $tanggal_po_awal)->format('Y-m-d'),
            'tanggal_akhir' => DateTime::createFromFormat('d/m/Y', $tanggal_po_akhir)->format('Y-m-d'),
            'master_barang_id' => $barang_id,
        ]);


        foreach ($stockDetail as $p) {
            $selisih = $p->qty_diterima - $p->total_penerimaan ;

            $this->updateStockPurchaseDetail->insert([
                'update_stock_purchase_id' => $poID,
                'rm_purchase_order_id' => $p->rm_purchase_order_id,
                'rm_purchase_order_detail_id' => $p->rm_purchase_order_detail_id,
                'stock_id' => $p->stock_id,
                'spesifikasi_id' => $p->spesifikasi_id,
                'stock_detail_id' => $p->id,
                'qty_po' => $p->total_penerimaan,
                'qty_diterima' => $p->qty_diterima,
                'qty_kotor' => $selisih,
            ]);
        }

        return response()->setJSON([
            'message' => "Update Stock Kotor berhasil",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function updateStockBahanBakuAction()
    {
        $stockDetail = json_decode($this->request->getVar('listBarang'));
        $id   = $this->request->getVar('id');
        $tanggal_po_awal   = $this->request->getVar('tanggal_po_awal');
        $tanggal_po_akhir  = $this->request->getVar('tanggal_po_akhir');
        $supplier_id       = $this->request->getVar('supplier_id');
        $warehouse_id      = $this->request->getVar('warehouse_id');
        $divisi_id         = $this->request->getVar('divisi_id');
        $barang_id         = $this->request->getVar('barang_id');
        $spesifikasi_id    = $this->request->getVar('spesifikasi_id');

        $this->updateStockPurchase->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' =>  $divisi_id,
            'supplier_id' => $supplier_id,
            'warehouse_id' => $warehouse_id,
            'tanggal_awal' => DateTime::createFromFormat('d/m/Y', $tanggal_po_awal)->format('Y-m-d'),
            'tanggal_akhir' => DateTime::createFromFormat('d/m/Y', $tanggal_po_akhir)->format('Y-m-d'),
            'master_barang_id' => $barang_id,
        ]);

        $this->updateStockPurchaseDetail
            ->where('update_stock_purchase_id', $id)
            ->delete();

        foreach ($stockDetail as $p) {
            $selisih = $p->qty_diterima - $p->total_penerimaan;

            $this->updateStockPurchaseDetail->insert([
                'update_stock_purchase_id' => $id,
                'rm_purchase_order_id' => $p->rm_purchase_order_id,
                'rm_purchase_order_detail_id' => $p->rm_purchase_order_detail_id,
                'stock_id' => $p->stock_id,
                'spesifikasi_id' => $p->spesifikasi_id,
                'stock_detail_id' => $p->id,
                'qty_po' => $p->total_penerimaan,
                'qty_diterima' => $p->qty_diterima,
                'qty_kotor' => $selisih,
            ]);
        }

        return $this->response->setJSON([
            'message' => "Update Stock Kotor berhasil diubah",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        // Cek data utama
        $purchase = $this->updateStockPurchase->find($id);
        if (!$purchase) {
            return $this->response->setJSON([
                'message' => "Data tidak ditemukan",
                'status'  => false,
                'token'   => csrf_hash()
            ]);
        }

        try {
            // Ambil detail
            $stockDetail = $this->updateStockPurchaseDetail
                ->where('deletedAt', null)
                ->where('update_stock_purchase_id', $id)
                ->findAll();

            $qty_diterima_total = 0;

            // ambil parent_id dari salah satu detail
            $parentId = !empty($stockDetail[0]['stock_id']) ? $stockDetail[0]['stock_id'] : null;

            foreach ($stockDetail as $p) {
                // ambil qty lama detail
                $oldDetail = $this->stockRevampDetailModel
                    ->select('qty_diterima')
                    ->where('id', $p['stock_detail_id'])
                    ->first();

                $oldQty = $oldDetail ? $oldDetail['qty_diterima'] : 0;
                $newQty = $p['qty_diterima'];

                // hitung selisih
                $selisih = $newQty - $oldQty;
                $qty_diterima_total += $selisih;

                // update detail
                $this->stockRevampDetailModel
                    ->where('id', $p['stock_detail_id'])
                    ->set(['qty_diterima' => $newQty])
                    ->update();

                // insert ke log kalau ada perubahan qty
               
                    $this->stockRevampLogModel->insert([
                        'stock_detail_id' => $p['stock_detail_id'],
                        'status'          => 'IN',
                        'qty_diterima'    => $selisih,
                    ]);
            }

            // update parent (tambah qty_diterima dengan total selisih)
            if ($parentId) {
                $this->stockRevampModel
                    ->where('id', $parentId)
                    ->set('qty_diterima', 'qty_diterima + ' . $qty_diterima_total, false)
                    ->update();
            }

            // Update status posting
            $this->updateStockPurchase
                ->update($id, ['status_posting' => "1"]);

            return $this->response->setJSON([
                'message' => "Posting Stock Kotor berhasil",
                'status'  => true,
                'token'   => csrf_hash()
            ]);
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'message' => "Gagal posting: " . $th->getMessage(),
                'status'  => false,
                'token'   => csrf_hash()
            ]);
        }
    }


    

    public function print($id = null)
    {
        if ($id) {
            $filename = "Penerimaan Barang Import";

            $data = [];
            $dataPenerimaanBarang = $this->penerimaanBarangModel->getById($id);

            if ($dataPenerimaanBarang) {
                $status_penerimaan = $dataPenerimaanBarang->status_penerimaan;
                $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;

                if ($status_penerimaan === "IMPORT") {
                    $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "IMPORT");

                    // var_dump($dataPenerimaanBarang);
                    // die;

                    if ($dataPenerimaanBarangDetail) {
                        $data["dataPenerimaanBarang"] = $dataPenerimaanBarang;
                        $data["dataPenerimaanBarangDetail"] = $dataPenerimaanBarangDetail;

                        // var_dump(json_decode($dataPenerimaanBarang->multiple_po_no));
                        // die;
                    }
                }
            }

            // load HTML content
            $this->dompdf->loadHtml(view('Warehouse/updateStockBahanBaku/bahanBaku/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'portrait');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Warehouse/penerimaanBarangLokal/print', $data);
        }
    }



    public function deleteupdateStockBahanBaku()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $findPenerimaanBarang = $this->penerimaanBarangModel->find($id);
                if ($findPenerimaanBarang) {
                    $response =  $this->penerimaanBarangModel->delete($id);
                    if ($response) {
                        $data = [
                            "status"            => true,
                            "message"   => "Data Berhasil dihapus",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    } else {
                        $message = 'Data Gagal Dihapus';
                        $data = [
                            "status"            => false,
                            "message"    => $message,
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                    }
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Data Tidak Ditemukan",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Dihapus",
                    'token' => csrf_hash()
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

    public function dropdownupdateStockBahanBaku()
    {
        $payload = [
            "idsupplier" => $this->request->getGet("id"),
            "statuspenerimaan" => "IMPORT",
            "tipebahan" => $this->request->getGet("tipe")
        ];

        $dataPenerimaanBarang = [];
        $responsePenerimaanBarang = curl_request("GET", "/penerimaanBarang/dropdown-tandaTerimaFaktur", $this->token, $payload);
        if ($responsePenerimaanBarang["code"] === 200) {
            $dataPenerimaanBarang = json_decode($responsePenerimaanBarang["body"])->data;
        }

        $data = [
            "data" =>  $dataPenerimaanBarang,
            "response" => $responsePenerimaanBarang,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function searchBarang()
    {
        $term = $this->request->getGet('q');
        $barang = $this->request->getGet('barang_id');

        $builder = $this->barangMasterSpesifikasiModel
            ->select('barang_master_spesifikasi.id as spesifikasi_id, barang_master.barang_name as master_barang, barang_master_spesifikasi.spesifikasi as spesifikasi, satuans.kode_satuan')
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            // ->where('barang_master.id', $barang)
            ->where('barang_master.company_id', $this->this_company_id)
            ->where('barang_master.type_barang', 'bahan_baku')
            ->groupStart()
                ->like('barang_master_spesifikasi.spesifikasi', "%{$term}%")
            ->groupEnd();

        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'data'   => $data,
            'status' => true,
            'token'  => csrf_hash()
        ]);
    }

    public function generatePenerimaanBarang()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouseID = $this->request->getVar('warehouseID');

        if (empty($warehouseID)) {
            $no = $this->penerimaanBarangModel->get_no(date('m'), date('Y'), $last_day, "", $warehouseID);
            return response()->setJSON([
                'status' => true,
                'data' => $no
            ]);
        } else {
            $warehouseModel = new WarehousesModel();
            $warehouse = $warehouseModel->where('id', $warehouseID)->first();

            $no = $this->penerimaanBarangModel->get_no(date('m'), date('Y'), $last_day, $warehouse['code_warehouse'], $warehouseID);

            if ($no) {
                $data = [
                    "status"  => true,
                    "data"  => $no
                ];
                echo json_encode($data);
            } else {
                $message = 'Gagal Auto Generate';
                $data = [
                    "status" => false,
                    "message"  => $message
                ];
                echo json_encode($data);
            }

            return;
        }
    }

    public function getReceivedItemsBySupplier($supplierId)
    {
        $selectQry = "penerimaan_barang.id AS id,
                      no_penerimaan_barang AS lpb_no, 
                      'USD' AS currency,
                      SUM(penerimaan_barang_detail.harga * penerimaan_barang_detail.qty) AS total";

        $receiveDataQry = $this->penerimaanBarangModel->asObject()
            ->select($selectQry)
            ->where('penerimaan_barang.supplier_id', $supplierId)
            // ->where('(`penerimaan_barang_detail`.`qty` - `penerimaan_barang_detail`.`summarized_qty`) > 0')
            // ->where("penerimaan_barang_detail.summarized_qty <", 'penerimaan_barang_detail.qty', false)
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL')
            ->groupBy('penerimaan_barang_id')
            ->findAll();

        echo json_encode(['data' => $receiveDataQry]);
        return;
    }
}
