<?php

namespace App\Controllers\Warehouse;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;

use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangBrokenModel;
use App\Models\PenerimaanBarangBrokenDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use App\Models\SatuansModel;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampModel;
use App\Models\SupplierHargaModel;
use DateTime;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\IOFactory;

use Dompdf\Dompdf;

class PenerimaanBarangBroken extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $barangModel;
    protected $metadataModel;
    protected $penerimaanBarangBrokenModel;
    protected $penerimaanBarangBrokenDetailModel;
    protected $rmPurchaseOrderModel;
    protected $rmPurchaseOrderDetailModel;
    protected $supplierModel;
    protected $warehousesModel;
    protected $satuanModel;
    private   $stockDetailModel;
    protected $beaCukaiModel;
    protected $supplierHargaModel;
    protected $jurnalController;
    protected $divisiModel;

    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->barangModel = new BarangMasterModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangBrokenModel = new PenerimaanBarangBrokenModel();
        $this->penerimaanBarangBrokenDetailModel = new PenerimaanBarangBrokenDetailModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->jurnalController = new JurnalUmum();
        $this->divisiModel = new DivisisModel();

        $this->dompdf = new Dompdf();
    }

    public function penerimaanBarangBroken()
    {
        return view('Warehouse/penerimaanBarangBroken/bahanBaku/index');
    }

    public function createPenerimaanBarangBroken()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'supplier' => $this->supplierModel->getSupplierByType('BAHAN BAKU')
        ];

        return view('Warehouse/penerimaanBarangBroken/bahanBaku/form', $data);
    }

    public function getByIdPenerimaanBarangBroken($id = null)
    {
        $data = [
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'supplier' => $this->supplierModel->getSupplierByType('BAHAN BAKU'),
            'penerimaanBarangBroken' => $this->penerimaanBarangBrokenModel
                    ->where('penerimaan_barang_broken.id', decrypt($id))
                    ->first(),
            'penerimaanBarangBrokenDetail' => $this->penerimaanBarangBrokenDetailModel
                ->select('
                    penerimaan_barang_broken_detail.*,
                    rm_purchase_orders.id AS po_id,
                    rm_purchase_orders.po_no AS po_no,
                    rm_purchase_orders.po_date,
                    suppliers.name AS supplier_name,
                    barang_master.id AS barang_id,
                    barang_master_spesifikasi.id AS spesifikasi_id,
                    barang_master.barang_name,
                    CONCAT(barang_master.barang_name, " - ", barang_master_spesifikasi.spesifikasi) AS spesifikasi_name
                ')
                ->join('penerimaan_barang_broken', 'penerimaan_barang_broken.id = penerimaan_barang_broken_detail.penerimaan_barang_id', 'left')
                ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang_broken_detail.rm_purchase_order_id', 'left')
                ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
                ->join('barang_master', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                ->where('penerimaan_barang_broken_detail.penerimaan_barang_id', decrypt($id))
                ->where('penerimaan_barang_broken_detail.deletedAt', NULL)
                ->findAll(),

        ];
 
        return view('Warehouse/penerimaanBarangBroken/bahanBaku/form', $data);
   }

    public function allPenerimaanBarangBroken()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
            "statuspenerimaan" => "Broken",
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            "penerimaan_barang_broken.company_id" => $this->this_company_id,
            // "status_penerimaan" => "Broken",
            "penerimaan_barang_broken.deletedAt" => null,
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
        $penerimaanBarangBrokenData = $this->penerimaanBarangBrokenModel->getPenerimaanBarangBrokenList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarangBroken = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($penerimaanBarangBrokenData['data'] as $data) {


            array_push($dataPenerimaanBarangBroken, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "createdAt"             => $data->createdAt ? date("d/m/Y", strtotime($data->createdAt)) : "",
                "supplier_name"         => $data->supplier_name,
                "status_posting"        => $data->status_posting,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $penerimaanBarangBrokenData['totalData'],
            "recordsFiltered"   => $penerimaanBarangBrokenData['totalFilteredData'],
            "data"              => $dataPenerimaanBarangBroken,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePenerimaanBarangBroken()
    {

        try {
            // === VALIDASI DASAR ===
            $rules = [
                "no_penerimaan_barang" => "required",
                "supplier_id" => "required",
                "warehouse_id" => "required",
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                return response()->setJSON([
                    "status"  => false,
                    "message" => reset($errorList),
                    "token"   => csrf_hash()
                ]);
            }

            $tanggal_po_awal   = $this->request->getVar('tanggal_po_awal');
            $tanggal_po_akhir  = $this->request->getVar('tanggal_po_akhir');
            
            $payload = [
                "company_id"           => $this->this_company_id,
                "no_penerimaan_barang" => $this->request->getVar("no_penerimaan_barang"),
                "divisi_id"            => formatter($this->request->getVar("divisi_id"), "STR_TO_INT"),
                "supplier_id"          => formatter($this->request->getVar("supplier_id"), "STR_TO_INT"),
                "warehouse_id"         => formatter($this->request->getVar("warehouse_id"), "STR_TO_INT"),
                "tanggal_awal"         => DateTime::createFromFormat("d/m/Y", $tanggal_po_awal)->format("Y-m-d"),
                "tanggal_akhir"        => DateTime::createFromFormat("d/m/Y", $tanggal_po_akhir)->format("Y-m-d"),
                "status_posting"          => "0",
            ];

            // === INSERT HEADER ===
            $this->penerimaanBarangBrokenModel->db->transException(true)->transStart();
            $headerId = $this->penerimaanBarangBrokenModel->insert($payload);

            // === DETAIL PAYLOAD ===
            $items = json_decode($this->request->getVar("listBarang"));
            $detailPayload = [];

            foreach ($items as $data) {

                $detailPayload[] = [
                    'penerimaan_barang_id'     => $headerId,
                    'rm_purchase_order_id'     => $data->po_id ?? null,
                    'spesifikasi_id'           => $data->spesifikasi_id ?? null,
                    'qty_diterima'             => $data->qty_diterima ?? 0,
                    'qty_po'                   => $data->qty_diterima ?? 0,
                ];
            }

            $this->penerimaanBarangBrokenDetailModel->insertBatch($detailPayload);
            $this->penerimaanBarangBrokenModel->db->transComplete();

            return response()->setJSON([
                "status"   => true,
                "message"  => "Penerimaan barang broken berhasil disimpan",
                "id"       => $headerId,
                "token"    => csrf_hash()
            ]);

        } catch (\Exception $e) {
            return response()->setJSON([
                "status"  => false,
                "message" => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                "token"   => csrf_hash()
            ]);
        }
    }


    public function updatePenerimaanBarangBroken()
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
        $response = $this->penerimaanBarangBrokenModel->update($id, $payload);

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
                'penerimaan_barang_broken_detail.deletedAt' => null
            ];

            $this->penerimaanBarangBrokenDetailModel->set($detailPayload)->where($conditionDetail)->update();

            if ($tipe_bahan == "PENOLONG") {
                $conditionDetail = [
                    'purchase_order_details_id' => $data->purchase_order_details_id,
                    'penerimaan_barang_id' => $id,
                    'penerimaan_barang_broken_detail.deletedAt' => null
                ];

                $selectQry = "
                            SUM(penerimaan_barang_broken_detail.jml_masuk) AS jmlMasuk
                        ";
                $amDetail =  $this->penerimaanBarangBrokenDetailModel
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
                    'penerimaan_barang_broken_detail.deletedAt' => null
                ];

                $selectQry = "
                            SUM(penerimaan_barang_broken_detail.jml_masuk) AS jmlMasuk
                        ";
                $rmDetail =  $this->penerimaanBarangBrokenDetailModel
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

    public function posting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $stockRevampModel = new StockRevampModel();
            $id = decrypt($this->request->getVar('id'));

            // Validasi data exists
            $penerimaanBarangBroken = $this->penerimaanBarangBrokenModel->where('id', $id)->where('deletedAt', NULL)->first();
            if (!$penerimaanBarangBroken) {
                throw new \Exception("Data jasa vendor tidak ditemukan");
            }

            // Cek jika sudah diposting
            if ($penerimaanBarangBroken['status_posting'] == '1') {
                throw new \Exception("Data sudah diposting sebelumnya");
            }

            $penerimaanBarangBrokenDetail = $this->penerimaanBarangBrokenDetailModel
                ->select('
                    penerimaan_barang_broken_detail.*,
                    rm_purchase_orders.id AS po_id,
                    rm_purchase_orders.po_no AS po_no,
                    rm_purchase_orders.po_date,
                    suppliers.name AS supplier_name,
                    barang_master.id AS barang_id,
                    barang_master.barang_name,
                    barang_master_spesifikasi.id AS spesifikasi_id,
                    barang_master_spesifikasi.satuan_1 AS unit_id,
                    CONCAT(barang_master.barang_name, " - ", barang_master_spesifikasi.spesifikasi) AS spesifikasi_name
                ')
                ->join('penerimaan_barang_broken', 'penerimaan_barang_broken.id = penerimaan_barang_broken_detail.penerimaan_barang_id', 'left')
                ->join('rm_purchase_orders', 'rm_purchase_orders.id = penerimaan_barang_broken_detail.rm_purchase_order_id', 'left')
                ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id', 'left')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = penerimaan_barang_broken_detail.spesifikasi_id', 'left')
                ->join('barang_master', 'barang_master_spesifikasi.barang_master_id = barang_master.id', 'left')
                ->where('penerimaan_barang_broken_detail.penerimaan_barang_id', $id)
                ->where('penerimaan_barang_broken_detail.deletedAt', NULL)
                ->findAll();

            if (empty($penerimaanBarangBrokenDetail)) {
                throw new \Exception("Detail jasa vendor tidak ditemukan");
            }

            foreach ($penerimaanBarangBrokenDetail as $p) {

                $data = [
                    "company_id"       => $this->this_company_id,
                    "spesifikasi_id"   => $p["spesifikasi_id"],
                    "barang_master_id" => $p["barang_id"],
                    "unit_id"          => $p["unit_id"],
                    "divisi_id"        => $penerimaanBarangBroken["divisi_id"],
                    "warehouse_id"     => $penerimaanBarangBroken["warehouse_id"],
                    "no_dokumen"       => $penerimaanBarangBroken["no_penerimaan_barang"],
                    "po_id"            => $p["po_id"],
                    "qty_diterima"     => round($p["qty_po"], 2),
                    "qty_bersih"       => round($p["qty_diterima"], 2),
                    'reference_id'     => $id,
                    'po_type'          => "LOKAL BAKU",
                    'reference_type'   => "LPB",
                    'status'           => "IN"
                ];

                $stockDetail = $stockRevampModel->insertStockRevamp($db, $data);

                $this->penerimaanBarangBrokenDetailModel->update($p['id'], [
                    'stock_detail_id' => $stockDetail,
                ]);
            }

            // Update status posting
            $this->penerimaanBarangBrokenModel->update($id, ['status_posting' => '1']);

            // Commit transaksi
            $db->transCommit();

            return $this->response->setJSON([
                'status' => true,
                'message' => "Penerimaan Barang Broken berhasil diposting",
                'token' => csrf_hash(),
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'status' => false,
                'message' => "Gagal posting Penerimaan Barang Broken: " . $e->getMessage(),
                'token' => csrf_hash(),
            ]);
            
        } catch (\Throwable $th) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'status' => false,
                'message' => "Terjadi kesalahan sistem: " . $th->getMessage(),
                'token' => csrf_hash(),
            ]);
        }
    }

    public function unposting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        $stockRevampModel = new StockRevampModel();

        try {
            $id = decrypt($this->request->getVar('id'));
            
            // Validasi apakah data exists
            $penerimaanBarangBroken = $this->penerimaanBarangBrokenModel
                ->where('id', $id)
                ->first();
            if (!$penerimaanBarangBroken) {
                throw new \Exception("Data Jasa Vendor Out tidak ditemukan");
            }


            $penerimaanBarangBrokenDetail = $this->penerimaanBarangBrokenDetailModel
                ->where('jasa_vendor_out_id', $id)
                ->findAll();

            if (empty($penerimaanBarangBrokenDetail)) {
                throw new \Exception("Detail Jasa Vendor Out tidak ditemukan");
            }

            foreach ($penerimaanBarangBrokenDetail as $j) {
                $data = [
                    "stock_detail_asal"     => $j["stock_out_detail_id"],    
                    "qty_diterima_asal"     => $j["qty"],
                    "no_dokumen"            => $penerimaanBarangBroken["no_surat_jalan"],
                    "keterangan"            => "UNPOST JASA VENDOR KELUAR",
                ];

                // Panggil model - jika gagal akan throw exception
                $stockRevampModel->unpostStockKeluar($db, $data);
            }

            // update status Jasa Vendor Out
            $this->penerimaanBarangBrokenModel->update($id, [
                'status_posting' => '0'
            ]);

            // commit transaksi
            $db->transCommit();

            return $this->response->setJSON([
                'message' => "Jasa Vendor Out berhasil di-unpost",
                'status'  => true,
                'token'   => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();

            return $this->response->setJSON([
                'message' => "Gagal unpost Jasa Vendor Out: " . $e->getMessage(),
                'status'  => false,
                'token'   => csrf_hash()
            ]);
            
        } catch (\Throwable $th) {
            $db->transRollback();

            return $this->response->setJSON([
                'message' => "Terjadi kesalahan sistem: " . $th->getMessage(),
                'status'  => false,
                'token'   => csrf_hash()
            ]);
        }
    }

    public function deletePenerimaanBarangBroken()
    {
        $id = $this->request->getVar("id");

        $penerimaanBarangBrokenFirst = $this->penerimaanBarangBrokenModel->where('id', $id)->first();
        if ($penerimaanBarangBrokenFirst) {
            $penerimaanBarangBrokenDetailModel = new PenerimaanBarangBrokenDetailModel();
            $rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
            $amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();

            $poArr = json_decode($penerimaanBarangBrokenFirst['multiple_po_id']);

            foreach ($poArr as $p) {

                if ($penerimaanBarangBrokenFirst['tipe_bahan'] == "PENOLONG") {
                    // PENOLONG
                    $amList = $amPurchaseOrderDetailModel->where('am_purchase_order_id', $p)->where('deletedAt', null)->findAll();
                    // GET QTY TOTAL IS DELETED
                    foreach ($amList as $a) {
                        $conditionDetail = [
                            'purchase_order_details_id' => $a['id'],
                            'penerimaan_barang_id' => $id,
                            'penerimaan_barang_broken_detail.deletedAt' => null
                        ];
                        // get qty masuk di lpb
                        $penerimaanBarangBrokenDetailFirst = $penerimaanBarangBrokenDetailModel->where($conditionDetail)->first();
                        if ($penerimaanBarangBrokenDetailFirst != null) {
                            // update remaining
                            $this->amPurchaseOrderDetailModel
                                ->where('id', $penerimaanBarangBrokenDetailFirst['purchase_order_details_id'])
                                ->set('remaining_qty', $a['remaining_qty'] + $penerimaanBarangBrokenDetailFirst['jml_masuk'])
                                ->set('qty_diterima', $a['qty_diterima'] - $penerimaanBarangBrokenDetailFirst['jml_masuk'])
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
                            'penerimaan_barang_broken_detail.deletedAt' => null
                        ];
                        // get qty masuk di lpb
                        $penerimaanBarangBrokenDetailFirst = $penerimaanBarangBrokenDetailModel->where($conditionDetail)->first();

                        if ($penerimaanBarangBrokenDetailFirst != null) {
                            // update remaining
                            $this->rmPurchaseOrderDetailModel
                                ->where('id', $penerimaanBarangBrokenDetailFirst['purchase_order_details_id'])
                                ->set('remaining_qty', $r['remaining_qty'] + $penerimaanBarangBrokenDetailFirst['jml_masuk'])
                                ->set('qty_diterima', $r['qty_diterima'] - $penerimaanBarangBrokenDetailFirst['jml_masuk'])
                                ->update();
                        }
                    }
                }
            }
        }
        $this->penerimaanBarangBrokenDetailModel->where('penerimaan_barang_id', $id)->delete();
        $this->penerimaanBarangBrokenModel->delete($id);
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
            $filename = "Penerimaan Barang Broken";

            $data = [];
            $dataPenerimaanBarangBroken = $this->penerimaanBarangBrokenModel->getById($id);

            if ($dataPenerimaanBarangBroken) {
                $status_penerimaan = $dataPenerimaanBarangBroken->status_penerimaan;
                $tipe_bahan = $dataPenerimaanBarangBroken->tipe_bahan;

                if ($status_penerimaan === "Broken") {
                    $dataPenerimaanBarangBrokenDetail = $this->penerimaanBarangBrokenDetailModel->getPenerimaanBarangBrokenDetailByPenerimaanBarangBrokenId($id, $tipe_bahan, "Broken");

                    if ($dataPenerimaanBarangBrokenDetail) {
                        $data["dataPenerimaanBarangBroken"] = $dataPenerimaanBarangBroken;
                        $data["dataPenerimaanBarangBrokenDetail"] = $dataPenerimaanBarangBrokenDetail;
                        $kemasan = [];
                        foreach ($dataPenerimaanBarangBrokenDetail as $d) {
                            $kemasan[] = $d['packaging'];
                        }
                        $data["dataKemasan"] = array_unique($kemasan);
                    }
                }
            }
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangBroken/bahanBaku/print', $data));
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function dropdownPenerimaanBarangBroken()
    {
        $payload = [
            "idsupplier" => $this->request->getVar("id"),
            "statuspenerimaan" => "Broken",
            "tipebahan" => $this->request->getVar("tipe")
        ];

        $dataPenerimaanBarangBroken = [];
        $responsePenerimaanBarangBroken = curl_request("GET", "/penerimaanBarangBroken/dropdown-tandaTerimaFaktur", $this->token, $payload);
        if ($responsePenerimaanBarangBroken["code"] === 200) {
            $dataPenerimaanBarangBroken = json_decode($responsePenerimaanBarangBroken["body"])->data;
        }

        $data = [
            "data" =>  $dataPenerimaanBarangBroken,
            "response" => $responsePenerimaanBarangBroken,
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
            "kategori"      => "Broken",
            // "type"          => "BAHAN BAKU"
        ];

        $condition = [
            // "suppliers.company_id"  => $this->this_company_id,
            "penerimaan_barang_broken.status_penerimaan"       => "Broken",
            // "penerimaan_barang_broken.tipe_bahan"              => "BAKU",
            // "penerimaan_barang_broken_detail.summarized_qty <" => 'penerimaan_barang_broken_detail.qty'

            // "search"                                => $this->request->getVar("search"),
            // "sort"                                  => $this->request->getVar("sort"),
            // "sortType"                              => $this->request->getVar("sortType")
        ];
        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $itemData = $this->penerimaanBarangBrokenModel
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

    public function generatePenerimaanBarangBroken()
    {
        $warehouseID = $this->request->getVar('warehouse_id');

        if (empty($warehouseID)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => "Pilih lokasi warehouse dahulu"
            ]);
        }

        $warehouseModel = new WarehousesModel();
        $warehouse = $warehouseModel->find($warehouseID);

        $tanggal = date('Y-m-d');
        $companyId = $warehouse['company_id'];

        $no = $this->penerimaanBarangBrokenModel->get_no($tanggal, $companyId);

        return $this->response->setJSON([
            'status' => (bool)$no,
            'data'   => $no ?: null,
            'message' => $no ? null : 'Gagal Auto Generate'
        ]);
    }

    public function getReceivedNoBySupplier($supplierId)
    {
        $condition = [
            "penerimaan_barang_broken.company_id"          => $this->this_company_id,
            "penerimaan_barang_broken.status_post"         => "FINISH",
            "penerimaan_barang_broken.status_penerimaan"   => "Broken",
            "penerimaan_barang_broken.is_summarized"       => 0
        ];

        $itemData = $this->penerimaanBarangBrokenModel
            ->getReceivedNoBySupplier($supplierId, $condition);

        $data = [
            "data"  => $itemData
        ];

        echo json_encode($data);
        return;
    }

    public function getListPO()
    {
        $divisi_id = $this->request->getVar('divisi_id');
        $warehouse_id = $this->request->getVar('warehouse_id');
        $supplier_id = $this->request->getVar('supplier_id');
        $poModel = new RMPurchaseOrderModel();
       

            $dataResult = $poModel->getDataPOList(
                $divisi_id,
                $warehouse_id,
                $supplier_id,
            );

            return response()->setJSON([
                'data' => $dataResult,
            ]);
        
    }
}
