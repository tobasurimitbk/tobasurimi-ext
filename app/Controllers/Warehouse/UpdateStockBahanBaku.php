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
use App\Models\KemasanModel;
use App\Models\TaxModel;
use App\Models\DivisisModel;
use App\Models\RMPurchaseOrderModel;
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

        $this->dompdf = new Dompdf();
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
        $poLokalBB = new RMPurchaseOrderModel();
        
        $data = [
            'tipeBarang' => $this->metadataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'tanggal' => date('Y-m-d'),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'supplier' => $this->supplierModel->getSupplierAll(),
            'po' => $poLokalBB
                    ->select('rm_purchase_orders.*, warehouses.warehouse_name')
                    ->where('rm_purchase_orders.id', decrypt($id))
                    ->join('warehouses', 'warehouses.id = rm_purchase_orders.warehouse_id', 'left')
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

    public function getListStockByPO()
    {
        $po = $this->request->getVar('no_po');
        $poModel = new RMPurchaseOrderModel();

        if (!empty($this->request->getVar('no_po'))) {


            $condition = [
                'stock_details2.no_po' => $this->request->getVar('no_po'),
                'stock_details.sumber' => "LPB"
            ];

            $dataResult = $this->stockDetail2Model->getStockListWithAddConditionByPONew(
                $condition
            );
         
          
            $resultArr = array();

           
                // Khsus Dari Supplier
                for ($i = 0; $i < count($dataResult); $i++) {
                    $bcType = $this->metadataModel->find($dataResult[$i]['bc_id']);

                    $rmPurchaseOrder = $poModel->where('po_no', $dataResult[$i]['stock_dokumen'])
                        // ->where('company_id', $dataResult['company_id'])
                        ->first();

                    $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                    $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                    $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                    $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                    $dataResult[$i]['barang'] = strtoupper($dataResult[$i]['barang']);
                    $dataResult[$i]['stock_date'] = $rmPurchaseOrder == null ? "-" : date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['type_barang'] = $dataResult[$i]['tipe_barang'];
                    $dataResult[$i]['type_barang_text'] = strtoupper(str_replace('_', ' ', $dataResult[$i]['tipe_barang']));
                    $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                    $dataResult[$i]['stok_total_kotor'] = floatval($dataResult[$i]['stok_total_kotor']);
                    $dataResult[$i]['total_penerimaan'] = floatval($dataResult[$i]['total_penerimaan']);

                    if ($dataResult[$i]['stok_total'] > 0) {
                        array_push($resultArr, $dataResult[$i]);
                    }
                }
            

            return response()->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
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
            "stock.company_id"        => $this->this_company_id,
            "stock_details2.qty_kotor !=" => NULL
            // "status_penerimaan" => "IMPORT"
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
        $penerimaanBarangData = $this->stockDetail2Model->getListStokPerPo($condition, $addCondition, $limit, $offset);

        // var_dump($penerimaanBarangData);
        // die;

        $dataPenerimaanBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($penerimaanBarangData['data'] as $data) {

            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_barang"  => $data->stock_dokumen,
                "warehouse_name"        => $data->warehouse_name,
                "divisi"        => $data->divisi,
                // "tipe_bahan"            => $data->tipe_bahan,
                "createdAt"             => $data->stock_date ? date("d/m/Y", strtotime($data->stock_date)) : "",
                // "validation_date"       => $data->validation_date ? date("d/m/Y", strtotime($data->validation_date)) : "",
                "supplier_name"         => $data->supplier_name,
                // "itemCount"             => $data->itemCount,
                "multiple_po_no"        => $data->no_po,
                // "status_post"           => $data->status_post,
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

    public function saveupdateStockBahanBaku()
    {

        $stockDetail = json_decode($this->request->getVar('listBarang'));
        foreach ($stockDetail as $p) {
            $stockRebusDetail = $this->stockDetail2Model
                ->where('id', $p->id)
                ->set('qty_kotor', $p->qty_kotor)
                ->update();
        }

        return response()->setJSON([
            'message' => "Update Stock Kotor berhasil",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function updateupdateStockBahanBaku()
    {
        $stockDetail = json_decode($this->request->getVar('listBarang'));
        foreach ($stockDetail as $p) {
            $stockRebusDetail = $this->stockDetail2Model
                ->where('id', $p->id)
                ->set('qty_kotor', $p->qty_kotor)
                ->update();
        }

        return response()->setJSON([
            'message' => "Update Stock Kotor berhasil",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function updateStatusupdateStockBahanBaku()
    {
        try {
            $id = $this->request->getPost("id");

            $dataPenerimaanBarang = $this->penerimaanBarangModel->getById($id);

            if (empty($dataPenerimaanBarang)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data penerimaan barang tidak ada",
                    "payload"   => "",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $multiple_po_id = json_decode($dataPenerimaanBarang->multiple_po_id);
            $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;

            $detail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "IMPORT");

            $this->penerimaanBarangModel->db->transException(true)->transStart();

            if ($detail) {
                foreach ($detail as $item) {
                    // check po already closed or not
                    if ($item["status_penerimaan"] === "0") {
                        $jml_masuk = $item["jml_masuk"] ? formatter($item["jml_masuk"], "STR_TO_FLOAT") : 0;
                        $qty_diterima = $item["qty_diterima"] ? formatter($item["qty_diterima"], "STR_TO_FLOAT") : 0;
                        $remaining_qty = $item["remaining_qty"] ? formatter($item["remaining_qty"], "STR_TO_FLOAT") : 0;
                        $barang_id = $item["barang_id"] ? formatter($item["barang_id"], "STR_TO_INT") : 0;
                        $purchase_order_details_id = $item["purchase_order_details_id"] ? formatter($item["purchase_order_details_id"], "STR_TO_INT") : 0;

                        // kemasan
                        $packaging = $item["packaging"] ? formatter($item["packaging"], "STR_TO_INT") : 0;
                        $packaging_qty = $item["packaging_qty"] ? formatter($item["packaging_qty"], "STR_TO_FLOAT") : 0;

                        $conditionRemain = [
                            'id' => $purchase_order_details_id
                        ];

                        $payloadRemain = [
                            'qty_diterima' => $qty_diterima + $jml_masuk,
                            'remaining_qty' => $remaining_qty - $jml_masuk
                        ];

                        // UPDATE REMAINING QTY AND JML DITERIMA
                        if ($tipe_bahan === "BAKU") {
                            $responseDet = $this->rmImportPODetailModel->where($conditionRemain)
                                ->set($payloadRemain)
                                ->update();

                            if (!$responseDet) {
                                $message =  'Gagal Ubah Remaining';
                                $data = [
                                    "status"    => false,
                                    "message"   => $message,
                                    "payload"   => "",
                                    'token'     => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        } elseif ($tipe_bahan === "PENOLONG") {
                            $responseDet = $this->amPurchaseOrderDetailModel->where($conditionRemain)
                                ->set($payloadRemain)
                                ->update();

                            if (!$responseDet) {
                                $message =  'Gagal Ubah Remaining';
                                $data = [
                                    "status"    => false,
                                    "message"   => $message,
                                    "payload"   => "",
                                    'token'     => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }

                        // // ADD STOK BARANG
                        // $find = $this->barangModel->find($barang_id);

                        // // ADD STOK KEMASAN
                        // $find_packaging = $this->barangModel->find($packaging);

                        // if($find)
                        // {
                        //     $stok = $find["stok"] ? formatter($find["stok"], "STR_TO_FLOAT") : 0;

                        //     $conditionUpdateStok = [
                        //         'id' => $barang_id
                        //     ];

                        //     $payloadupdateStok = [
                        //         'stok' => $stok + $jml_masuk
                        //     ];

                        //     $responseStok = $this->barangModel->where($conditionUpdateStok)->set($payloadupdateStok)->update();    

                        //     if(!$responseStok) {
                        //         $message =  'Gagal Tambah Stok';
                        //         $data = [
                        //             "status"            => false,
                        //             "message"    => $message,
                        //             "payload"   => "",
                        //             'token' => csrf_hash()
                        //         ];
                        //         echo json_encode($data);
                        //         return;
                        //     }
                        // }

                        // if ($find_packaging) {
                        //     $stok = $find_packaging["stok"] ? formatter($find_packaging["stok"], "STR_TO_FLOAT") : 0;

                        //     $payloadupdateStok = [
                        //         'stok' => $stok + $packaging_qty
                        //     ];

                        //     $responseStok = $this->barangModel->where('id', $packaging)
                        //         ->set($payloadupdateStok)
                        //         ->update();    

                        //     if (!$responseStok) {
                        //         $message =  'Gagal Tambah Stok';
                        //         $data = [
                        //             "status"    => false,
                        //             "message"   => $message,
                        //             "payload"   => "",
                        //             'token'     => csrf_hash()
                        //         ];
                        //         echo json_encode($data);
                        //         return;
                        //     }
                        // }

                        // add stock detail barang
                        $this->stockDetailModel->addOrReduceStock($barang_id, $dataPenerimaanBarang->warehouse_id, 'New', $jml_masuk, 'IN', '');

                        // add stock detail barang kemasan
                        $this->stockDetailModel->addOrReduceStock($packaging, $dataPenerimaanBarang->warehouse_id, 'Scrap', $packaging_qty, 'OUT', '');
                    }
                }
            }

            // automate close po check item by check ech po number
            foreach ($multiple_po_id as $item) {
                $check_close = true;

                if ($tipe_bahan === "BAKU") {
                    $responseDetail = $this->rmImportPODetailModel->getPurchaseOrderDetailByPurchaseOrderId($item);

                    if ($responseDetail) {
                        foreach ($responseDetail as $itemDetail) {
                            // check if each item must 0 remaining qty to close
                            if ($itemDetail["remaining_qty"] !== "0.00") {
                                $check_close = false;
                            }
                        }

                        if ($check_close) {
                            $conditionUpdate = [
                                'id' => $item
                            ];

                            $payloadupdate = [
                                'status_penerimaan' => 1
                            ];

                            $responseStatusPenerimaan = $this->rmImportPOModel->where($conditionUpdate)->set($payloadupdate)->update();

                            if (!$responseStatusPenerimaan) {
                                $message =  'Gagal Ubah Status Penerimaan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => "",
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }
                if ($tipe_bahan === "PENOLONG") {
                    $responseDetail = $this->amPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($item);

                    if ($responseDetail) {
                        foreach ($responseDetail as $itemDetail) {
                            // check if each item must 0 remaining qty to close
                            if ($itemDetail["remaining_qty"] !== "0.00") {
                                $check_close = false;
                            }
                        }

                        if ($check_close) {
                            $conditionUpdate = [
                                'id' => $item
                            ];

                            $payloadupdate = [
                                'status_penerimaan' => 1
                            ];

                            $responseStatusPenerimaan = $this->amPurchaseOrderModel->where($conditionUpdate)->set($payloadupdate)->update();

                            if (!$responseStatusPenerimaan) {
                                $message =  'Gagal Ubah Status Penerimaan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => "",
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }
            }

            $response = $this->penerimaanBarangModel->where('id', $id)
                ->set('status_post', 'FINISH')
                ->update();

            if ($response) {
                $data = [
                    "status"     => true,
                    "message"    => "Data berhasil di posting",
                    "payload"   => "",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"     => false,
                    "message"    => "Data gagal di posting",
                    "payload"   => "",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            }

            $this->penerimaanBarangModel->db->transComplete();
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

    /* public function getReceivedItemsBySupplier($supplierId)
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "kategori"      => "LOKAL",
            "type"          => "BAHAN BAKU"
        ];

        $condition = [
            // "suppliers.company_id"  => $this->this_company_id,
            "penerimaan_barang.status_penerimaan"       => "IMPORT",
            "penerimaan_barang.tipe_bahan"              => "BAKU",
            // "penerimaan_barang_detail.summarized_qty <" => 'penerimaan_barang_detail.qty'

            // "search"                                => $this->request->getGet("search"),
            // "sort"                                  => $this->request->getGet("sort"),
            // "sortType"                              => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $itemData = $this->penerimaanBarangModel
            ->getReceivedItemsBySupplier($supplierId, $condition, $limit, $offset);

        $receivedData = [];

        foreach ($itemData['data'] as $data) {
            array_push($receivedData, [
                "id"                    => $data->id,
                "no_po"                 => "jugijagiju",
                "lpb_date"              => $data->lpb_date,
                "no_lpb"                => $data->no_lpb,
                "item_name"             => $data->item_name,
                "lpb_qty"               => $data->lpb_qty,
                "price"                 => floatval($data->price),
                "return_qty"            => 0, 
                "received_qty"          => 0,
                "qty_will_be_received"  => $data->lpb_qty,
                "unit"                  => $data->unit
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $itemData['totalData'],
            "recordsFiltered"   => $itemData['totalFilteredData'],
            "data"              => $receivedData,
            // "response" => $response,
            // "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    } */

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
