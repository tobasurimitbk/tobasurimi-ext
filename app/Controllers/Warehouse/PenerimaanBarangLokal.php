<?php

namespace App\Controllers\Warehouse;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;

use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use App\Models\SupplierHargaModel;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\IOFactory;

use Dompdf\Dompdf;

class PenerimaanBarangLokal extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $barangModel;
    protected $metadataModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $rmPurchaseOrderModel;
    protected $rmPurchaseOrderDetailModel;
    protected $supplierModel;
    protected $warehousesModel;
    protected $satuanModel;
    private $stockDetailModel;
    protected $beaCukaiModel;
    protected $supplierHargaModel;
    protected $jurnalController;

    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->barangModel = new BarangMasterModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->jurnalController = new JurnalUmum();

        $this->dompdf = new Dompdf();
    }

    public function penerimaanBarangLokal()
    {
        return view('Warehouse/penerimaanBarangLokal/index');
    }

    public function createPenerimaanBarangLokal()
    {
        //Get AJU
        $dataAJU = $this->metadataModel->get_by_name('jenis_dok_aju');

        //Get Supplier
        $dataSupplier = $this->supplierModel->getSupplierByType('BAHAN BAKU');

        //Get Warehouse
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $data = [
            "dataSatuan" => $dataSatuan,
            "dataWarehouse" => $dataWarehouse,
            "dataSupplier" => $dataSupplier,
            "dataAJU" => $dataAJU
        ];

        return view('Warehouse/penerimaanBarangLokal/form', $data);
    }

    public function getByIdPenerimaanBarangLokal($id = null)
    {
        //Get AJU
        $dataAJU = $this->metadataModel->get_by_name('jenis_dok_aju');

        //Get Warehouse
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);

        //Get Satuan
        $dataSatuan = $this->satuanModel->asObject()->find();

        $data["dataAJU"] = $dataAJU;

        $data["dataWarehouse"] = $dataWarehouse;

        $data["dataSatuan"] = $dataSatuan;

        if (!empty($id)) {
            $dataPenerimaanBarang = $this->penerimaanBarangModel->asObject()->find($id);

            if ($dataPenerimaanBarang) {
                $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;
                $status_penerimaan = $dataPenerimaanBarang->status_penerimaan;

                if ($status_penerimaan === "LOKAL") {
                    $data["dataPenerimaanBarang"] = $dataPenerimaanBarang;
                    $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "LOKAL");

                    // var_dump($dataPenerimaanBarangDetail);
                    // die;

                    if ($dataPenerimaanBarangDetail) {
                        $data["dataPenerimaanBarangDetail"] = $dataPenerimaanBarangDetail;
                    }

                    if ($tipe_bahan === "BAKU") {
                        $supplier_id = $dataPenerimaanBarang->supplier_id;

                        $dataNo = $this->rmPurchaseOrderModel->getNoPenerimaanBarang($supplier_id, $this->this_company_id, $dataPenerimaanBarang->divisi_id);

                        //Get Supplier
                        $dataSupplier = $this->supplierModel->getSupplierByType('BAHAN BAKU');

                        $data["dataNo"] = $dataNo;
                        $data["dataSupplier"] = $dataSupplier;
                    }
                    if ($tipe_bahan === "PENOLONG") {
                        $supplier_id = $dataPenerimaanBarang->supplier_id;

                        $dataNo = $this->amPurchaseOrderModel->getNoPenerimaanBarang("Lokal", $supplier_id, $this->this_company_id);

                        //Get Supplier
                        $dataSupplier = $this->supplierModel->getSupplierByType('BAHAN PENOLONG');

                        $data["dataNo"] = $dataNo;
                        $data["dataSupplier"] = $dataSupplier;
                    }
                }
            }
        }


        return view('Warehouse/penerimaanBarangLokal/form', $data);
    }

    public function allPenerimaanBarangLokal()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
            "statuspenerimaan" => "LOKAL",
            "status" => $this->request->getVar("status"),
            // "status_bc" => $this->request->getVar("status_bc"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "status_penerimaan" => "LOKAL",
            "penerimaan_barang.deletedAt" => null,
            "penerimaan_barang_detail.deletedAt" => null
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $penerimaanBarangData = $this->penerimaanBarangModel->getPenerimaanBarangList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($penerimaanBarangData['data'] as $data) {
            $multiple_po_no = json_decode($data->multiple_po_no);

            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "warehouse_name"        => $data->warehouse_name,
                "tipe_bahan"            => $data->tipe_bahan,
                "createdAt"             => $data->createdAt ? date("d/m/Y", strtotime($data->createdAt)) : "",
                "supplier_name"         => $data->supplier_name,
                "itemCount"             => $data->itemCount,
                "multiple_po_no"        => json_decode($data->multiple_po_no),
                "status_post"           => $data->status_post,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $penerimaanBarangData['totalData'],
            "recordsFiltered"   => $penerimaanBarangData['totalFilteredData'],
            "data"              => $dataPenerimaanBarang,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePenerimaanBarangLokal()
    {
        try {
            $rules = [
                "no_penerimaan_barang" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'No. Penerimaan Barang tidak boleh kosong'
                    ]
                ],
                "supplier_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Supplier tidak boleh kosong'
                    ]
                ],
                "warehouse_id" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Warehouse tidak boleh kosong'
                    ]
                ],
                "tipe_bahan" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Tipe Bahan tidak boleh kosong'
                    ]
                ]
            ];


            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                $tipe_bahan = $this->request->getVar("tipe_bahan");
                $multiple_po_id = formatter(json_decode($this->request->getVar("multiple_po_id")), "ARR_TO_INT");

                $payload = [
                    "company_id" => $this->this_company_id,
                    "no_penerimaan_barang" => $this->request->getVar("no_penerimaan_barang"),
                    "supplier_id" => formatter($this->request->getVar("supplier_id"), "STR_TO_INT"),
                    "warehouse_id" => formatter($this->request->getVar("warehouse_id"), "STR_TO_INT"),
                    "acceptance_type" => $this->request->getVar("acceptance_type"),
                    "multiple_po_id" => json_encode($multiple_po_id),
                    "multiple_po_no" => $this->request->getVar("multiple_po_no"),
                    "tipe_bahan" => $tipe_bahan,
                    "bc_type" => $this->request->getVar('aju_document_type'),
                    "no_surat_jalan" => $this->request->getVar('no_surat_jalan'),
                    "status_post" => "WAITING",
                    "status_penerimaan" => "LOKAL",
                ];

                $items = json_decode($this->request->getVar("items"));

                $detailPayload = [];

                $this->penerimaanBarangModel->db->transException(true)->transStart();
                $response =  $this->penerimaanBarangModel->insert($payload);

                foreach ($items as $data) {

                    $detailPayload[] = [
                        'purchase_order_details_id' => $data->purchase_order_details_id,
                        'penerimaan_barang_id' => $response,
                        'harga' => $data->harga,
                        'harga_harian' => $data->harga_harian,
                        'harga_bulanan' => $data->harga_bulanan,
                        'sub_total' => $data->sub_total,
                        'keterangan' => $data->keterangan,
                        'barang_id' => $data->barang_id,
                        'qty' => $data->qty,
                        'unit' => $data->unit,
                        'nama_barang_dok' => $data->nama_barang_dok,
                        'jml_masuk' => $data->jml_masuk,
                        'packaging' => $data->packaging,
                        'packaging_qty' => $data->packaging_qty
                    ];

                    if ($tipe_bahan == "PENOLONG") {
                        // UPDATE remaining_qty (sisa stok) di table am_purchase_order_detail
                        $amDetail =  $this->amPurchaseOrderDetailModel->where('id', $data->purchase_order_details_id)->first();
                        $qtyDiterimaLast = ($amDetail == null) ? 0 : $amDetail['qty_diterima'];
                        $qtyRemainingLast = ($amDetail == null) ? 0 : $amDetail['remaining_qty'];

                        $this->amPurchaseOrderDetailModel->update($data->purchase_order_details_id, [
                            'remaining_qty' => ($data->qty - $data->jml_masuk - $qtyDiterimaLast),
                            'qty_diterima' => $data->jml_masuk + $qtyDiterimaLast
                        ]);
                    } else {
                        // BAKU
                        $rmDetail = $this->rmPurchaseOrderDetailModel->where('id', $data->purchase_order_details_id)->first();
                        $qtyDiterimaLast = ($rmDetail == null) ? 0 : $rmDetail['qty_diterima'];
                        $qtyRemainingLast = ($rmDetail == null) ? 0 : $rmDetail['remaining_qty'];

                        $this->rmPurchaseOrderDetailModel->update($data->purchase_order_details_id, [
                            'remaining_qty' => ($data->qty - $data->jml_masuk - $qtyDiterimaLast),
                            'qty_diterima' => $data->jml_masuk + $qtyDiterimaLast
                        ]);
                    }
                }

                $this->penerimaanBarangDetailModel->insertBatch($detailPayload);

                $this->penerimaanBarangModel->db->transComplete();

                $data = [
                    "id"        => $response,
                    "status"    => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => $payload,
                    "response"  => $response,
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

    public function updatePenerimaanBarangLokal()
    {
        $rules = [
            "no_penerimaan_barang" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'No. Penerimaan Barang tidak boleh kosong'
                ]
            ],
            "supplier_id" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Supplier tidak boleh kosong'
                ]
            ],
            "warehouse_id" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Warehouse tidak boleh kosong'
                ]
            ],
            "tipe_bahan" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Tipe Bahan tidak boleh kosong'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash()
            ];
            return response()->setJson($data);
        }

        $id = $this->request->getVar("id");
        $tipe_bahan = $this->request->getVar("tipe_bahan");
        $multiple_po_id = formatter(json_decode($this->request->getVar("multiple_po_id")), "ARR_TO_INT");

        $payload = [
            "company_id" => $this->this_company_id,
            "no_penerimaan_barang" => $this->request->getVar("no_penerimaan_barang"),
            "supplier_id" => formatter($this->request->getVar("supplier_id"), "STR_TO_INT"),
            "warehouse_id" => formatter($this->request->getVar("warehouse_id"), "STR_TO_INT"),
            "acceptance_type" => $this->request->getVar("acceptance_type"),
            "multiple_po_id" => json_encode($multiple_po_id),
            "multiple_po_no" => $this->request->getVar("multiple_po_no"),
            "bc_type" => $this->request->getVar('aju_document_type'),
            "no_surat_jalan" => $this->request->getVar('no_surat_jalan'),
            "tipe_bahan" => $tipe_bahan
        ];

        $items = json_decode($this->request->getVar("items"));
        $response = $this->penerimaanBarangModel->update($id, $payload);

        foreach ($items as $data) {
            $detailPayload = [
                'purchase_order_details_id' => $data->purchase_order_details_id,
                'penerimaan_barang_id' => $id,
                'harga' => $data->harga,
                'harga_harian' => $data->harga_harian,
                'harga_bulanan' => $data->harga_bulanan,
                'sub_total' => $data->sub_total,
                'keterangan' => $data->keterangan,
                'barang_id' => $data->barang_id,
                'qty' => $data->qty,
                'unit' => $data->unit,
                'nama_barang_dok' => $data->nama_barang_dok,
                'jml_masuk' => $data->qty_diterima - $data->jml_masuk,
                'packaging' => $data->packaging,
                'packaging_qty' => $data->packaging_qty
            ];

            $conditionDetail = [
                'purchase_order_details_id' => $data->purchase_order_details_id,
                'penerimaan_barang_id' => $id,
                'penerimaan_barang_detail.deletedAt' => null
            ];

            $this->penerimaanBarangDetailModel->set($detailPayload)->where($conditionDetail)->update();

            if ($tipe_bahan == "PENOLONG") {
                $conditionDetail = [
                    'purchase_order_details_id' => $data->purchase_order_details_id,
                    'penerimaan_barang_id' => $id,
                    'penerimaan_barang_detail.deletedAt' => null
                ];

                $selectQry = "
                            SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk
                        ";
                $amDetail =  $this->penerimaanBarangDetailModel
                    ->select($selectQry)
                    ->where($conditionDetail)
                    ->groupBy(['penerimaan_barang_id', 'purchase_order_details_id'])
                    ->first();

                $this->amPurchaseOrderDetailModel->update($data->purchase_order_details_id, [
                    'remaining_qty' => $data->qty -  $amDetail['jmlMasuk'],
                    'qty_diterima' => $amDetail['jmlMasuk'],
                ]);
            } else {
                $conditionDetail = [
                    'purchase_order_details_id' => $data->purchase_order_details_id,
                    'penerimaan_barang_id' => $id,
                    'penerimaan_barang_detail.deletedAt' => null
                ];

                $selectQry = "
                            SUM(penerimaan_barang_detail.jml_masuk) AS jmlMasuk
                        ";
                $rmDetail =  $this->penerimaanBarangDetailModel
                    ->select($selectQry)
                    ->where($conditionDetail)
                    ->groupBy(['penerimaan_barang_id', 'purchase_order_details_id'])
                    ->first();

                // return response()->setJson([
                //     'emDetail' => $rmDetail,
                //     'list' => $items
                // ]);

                $this->rmPurchaseOrderDetailModel->update($data->purchase_order_details_id, [
                    'remaining_qty' =>  $data->qty - $rmDetail['jmlMasuk'],
                    'qty_diterima' =>  $rmDetail['jmlMasuk']
                ]);
            }
        }


        return response()->setJson([
            "status" => true,
            "message" => "Penerimaan barang berhasil diupdate",
            "payload" => $payload,
            "response" => $response,
            'token' => csrf_hash()
        ]);
    }

    public function updateStatusPenerimaanBarangLokal()
    {
        try {
            $id = $this->request->getVar("id");

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
            foreach ($multiple_po_id as $key => $value) {
                $result = $this->jurnalController->insertDataPembelian($value, "BAHAN " . $dataPenerimaanBarang->tipe_bahan, $dataPenerimaanBarang->status_penerimaan, "pembelian", $id);
                if ($result) {
                    $responseBody = json_decode($result->getBody(), true);
                    if ($responseBody && isset($responseBody['status'])) {
                        $data = [
                            "status"    => false,
                            "message"   => $responseBody['message'],
                            "payload"   => "",
                            'token'     => csrf_hash()
                        ];
                        echo json_encode($data);
                        return;
                    }
                }
            }
            $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;
            $detail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "LOKAL");
            $this->penerimaanBarangModel->db->transException(true)->transStart();

            foreach ($detail as $item) {
                // check po already closed or not
                if ($item["status_penerimaan"] === "0") {
                    $jml_masuk = $item["jml_masuk"] ? formatter($item["jml_masuk"], "STR_TO_FLOAT") : 0;
                    $barang_id = $item["barang_id"] ? formatter($item["barang_id"], "STR_TO_INT") : 0;
                    $purchase_order_details_id = $item["purchase_order_details_id"] ? formatter($item["purchase_order_details_id"], "STR_TO_INT") : 0;

                    // kemasan
                    $packaging = $item["packaging"] ? formatter($item["packaging"], "STR_TO_INT") : 0;
                    $packaging_qty = $item["packaging_qty"] ? formatter($item["packaging_qty"], "STR_TO_FLOAT") : 0;

                    $spesifikasi = "";
                    if ($tipe_bahan === "BAKU") {
                        $find_baku = $this->rmPurchaseOrderDetailModel->getPurchaseOrderDetailById($purchase_order_details_id);
                        if ($find_baku) {
                            $spesifikasi = $find_baku->spesifikasi;
                        }
                    }
                    $this->stockDetailModel->addOrReduceStock($barang_id, $dataPenerimaanBarang->warehouse_id, 'New', $jml_masuk, 'IN', $spesifikasi);
                    $this->stockDetailModel->addOrReduceStock($packaging, $dataPenerimaanBarang->warehouse_id, 'Scrap', $packaging_qty, 'OUT', '');
                }
            }

            $response = $this->penerimaanBarangModel
                ->where(['id' => $id])
                ->set(['status_post' => 'FINISH'])
                ->update();

            $this->penerimaanBarangModel->db->transComplete();

            if ($response) {
                $data = [
                    "status"    => true,
                    "message"   => "Data berhasil di posting",
                    "payload"   => "",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data gagal di posting",
                    "payload"   => "",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deletePenerimaanBarangLokal()
    {
        $id = $this->request->getVar("id");

        $penerimaanBarangFirst = $this->penerimaanBarangModel->where('id', $id)->first();
        if ($penerimaanBarangFirst) {
            $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
            $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
            $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();

            $poArr = json_decode($penerimaanBarangFirst['multiple_po_id']);

            foreach ($poArr as $p) {

                if ($penerimaanBarangFirst['tipe_bahan'] == "PENOLONG") {
                    // PENOLONG
                    $amList = $amPurchaseOrderDetailModel->where('am_purchase_order_id', $p)->where('deletedAt', null)->findAll();
                    // GET QTY TOTAL IS DELETED
                    foreach ($amList as $a) {
                        $conditionDetail = [
                            'purchase_order_details_id' => $a['id'],
                            'penerimaan_barang_id' => $id,
                            'penerimaan_barang_detail.deletedAt' => null
                        ];
                        // get qty masuk di lpb
                        $penerimaanBarangDetailFirst = $penerimaanBarangDetailModel->where($conditionDetail)->first();
                        if ($penerimaanBarangDetailFirst != null) {
                            // update remaining
                            $this->amPurchaseOrderDetailModel
                                ->where('id', $penerimaanBarangDetailFirst['purchase_order_details_id'])
                                ->set('remaining_qty', $a['remaining_qty'] + $penerimaanBarangDetailFirst['jml_masuk'])
                                ->set('qty_diterima', $a['qty_diterima'] - $penerimaanBarangDetailFirst['jml_masuk'])
                                ->update();
                        }
                    }
                } else {
                    // BAKU
                    $rmList = $rmPurchaseOrderDetailModel->where('rm_purchase_order_id', $p)->where('deletedAt', null)->findAll();
                    // GET QTY TOTAL IS DELETED
                    foreach ($rmList as $r) {
                        $conditionDetail = [
                            'purchase_order_details_id' => $r['id'],
                            'penerimaan_barang_id' => $id,
                            'penerimaan_barang_detail.deletedAt' => null
                        ];
                        // get qty masuk di lpb
                        $penerimaanBarangDetailFirst = $penerimaanBarangDetailModel->where($conditionDetail)->first();

                        if ($penerimaanBarangDetailFirst != null) {
                            // update remaining
                            $this->rmPurchaseOrderDetailModel
                                ->where('id', $penerimaanBarangDetailFirst['purchase_order_details_id'])
                                ->set('remaining_qty', $r['remaining_qty'] + $penerimaanBarangDetailFirst['jml_masuk'])
                                ->set('qty_diterima', $r['qty_diterima'] - $penerimaanBarangDetailFirst['jml_masuk'])
                                ->update();
                        }
                    }
                }
            }
        }
        $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->delete();
        $this->penerimaanBarangModel->delete($id);
        $data = [
            "status" => true,
            "message" => "Data Berhasil dihapus",
            'token' => csrf_hash()
        ];

        return response()->setJSON($data);
    }


    public function print($id = null)
    {
        if ($id) {
            $filename = "Penerimaan Barang Lokal";

            $data = [];
            $dataPenerimaanBarang = $this->penerimaanBarangModel->getById($id);

            if ($dataPenerimaanBarang) {
                $status_penerimaan = $dataPenerimaanBarang->status_penerimaan;
                $tipe_bahan = $dataPenerimaanBarang->tipe_bahan;

                if ($status_penerimaan === "LOKAL") {
                    $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $tipe_bahan, "LOKAL");

                    if ($dataPenerimaanBarangDetail) {
                        $data["dataPenerimaanBarang"] = $dataPenerimaanBarang;
                        $data["dataPenerimaanBarangDetail"] = $dataPenerimaanBarangDetail;
                        $kemasan = [];
                        foreach ($dataPenerimaanBarangDetail as $d) {
                            $kemasan[] = $d['packaging'];
                        }
                        $data["dataKemasan"] = array_unique($kemasan);
                    }
                }
            }
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangLokal/print', $data));
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function dropdownPenerimaanBarangLokal()
    {
        $payload = [
            "idsupplier" => $this->request->getVar("id"),
            "statuspenerimaan" => "LOKAL",
            "tipebahan" => $this->request->getVar("tipe")
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

    public function getReceivedItemsBySupplier($supplierId)
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "idCompany"     => $this->this_company_id,
            "kategori"      => "LOKAL",
            // "type"          => "BAHAN BAKU"
        ];

        $condition = [
            // "suppliers.company_id"  => $this->this_company_id,
            "penerimaan_barang.status_penerimaan"       => "LOKAL",
            // "penerimaan_barang.tipe_bahan"              => "BAKU",
            // "penerimaan_barang_detail.summarized_qty <" => 'penerimaan_barang_detail.qty'

            // "search"                                => $this->request->getVar("search"),
            // "sort"                                  => $this->request->getVar("sort"),
            // "sortType"                              => $this->request->getVar("sortType")
        ];
        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $itemData = $this->penerimaanBarangModel
            ->getReceivedItemsBySupplier($supplierId, $condition, $limit, $offset);

        $receivedData = [];

        foreach ($itemData['data'] as $data) {
            array_push($receivedData, [
                "id"                    => $data->id,
                "no_po"                 => json_decode(json_decode($data->multiple_po_no)),
                "lpb_date"              => $data->lpb_date,
                "no_lpb"                => $data->no_lpb,
                "item_name"             => $data->item_name,
                "lpb_qty"               => formatter($data->lpb_qty, "STR_TO_FLOAT"),
                "price"                 => number_format($data->price),
                "return_qty"            => 0,
                "received_qty"          => 0,
                "qty_will_be_received"  => formatter($data->lpb_qty, "STR_TO_FLOAT"),
                "unit"                  => $data->unit
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $itemData['totalData'],
            "recordsFiltered"   => $itemData['totalFilteredData'],
            "data"              => $receivedData,
            // "response" => $response,
            // "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function generatePenerimaanBarang()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouseID = $this->request->getVar('warehouseID');

        if (empty($warehouseID)) {
            return response()->setJSON([
                'status' => false,
                'message' => "Pilih lokasi warehouse dahulu"
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

    public function getReceivedNoBySupplier($supplierId)
    {
        $condition = [
            "penerimaan_barang.company_id"          => $this->this_company_id,
            "penerimaan_barang.status_post"         => "FINISH",
            "penerimaan_barang.status_penerimaan"   => "LOKAL",
            "penerimaan_barang.is_summarized"       => 0
        ];

        $itemData = $this->penerimaanBarangModel
            ->getReceivedNoBySupplier($supplierId, $condition);

        $data = [
            "data"  => $itemData
        ];

        echo json_encode($data);
        return;
    }
}
