<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

use App\Models\CompaniesModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\BeaCukaiModel;
use App\Models\BarangMasterModel;
use App\Models\BagianModel;
use App\Models\SatuansModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SupplierHargaModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class POLokalBahanBaku extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $RMPurchaseOrderModel;
    protected $RMPurchaseOrderDetailModel;
    protected $CompaniesModel;
    protected $SupplierModel;
    protected $BeaCukaiModel;
    private $barangModel;
    protected $metadataModel;
    protected $BagianModel;
    protected $SatuansModel;
    protected $SupplierHargaModel;
    protected $warehousesModel;
    protected $penerimaanBarangModel;
    protected $dompdf;
    protected $penerimaanBarangDetailModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->SupplierModel = new SupplierModel();
        $this->BeaCukaiModel = new BeaCukaiModel();
        $this->barangModel = new BarangMasterModel();
        $this->metadataModel = new MetadataModel();
        $this->BagianModel = new BagianModel();
        $this->CompaniesModel = new CompaniesModel();
        $this->SatuansModel = new SatuansModel();
        $this->SupplierHargaModel = new SupplierHargaModel();
        $this->warehousesModel = new WarehousesModel();
        $this->dompdf = new Dompdf();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
    }

    public function poLokalBahanBaku()
    {
        return view('Purchase/poLokalBahanBaku/index');
    }

    public function createPOLokalBahanBaku()
    {
        //Get BC Type By Metadata
        $dataBCType = $this->metadataModel->get_by_name('jenis_dok_aju');

        //Get Company
        $dataCompany =  $this->CompaniesModel->getCompanies();

        //Get Supplier
        $dataSupplier = $this->SupplierModel->getSupplierByType('BAHAN BAKU');

        // Get Bagian
        $dataBagian = $this->BagianModel->where('deletedAt', null)->findAll();

        // Get Satuan
        $dataSatuan = $this->SatuansModel->where('deletedAt', null)->findAll();

        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        $data = [
            "dataBCType"    => $dataBCType,
            "today"         => date("d/m/Y"),
            "dataSatuan"    => $dataSatuan,
            "dataBagian"    => $dataBagian,
            "dataSupplier"  => $dataSupplier,
            "dataCompany"   => $dataCompany,
            "dataWarehouse" => $dataWarehouse,

        ];

        return view('Purchase/poLokalBahanBaku/form', $data);
    }

    public function getByIdPOLokalBahanBaku($id = null)
    {
        //Get Company
        $dataCompany =  $this->CompaniesModel->getCompanies();

        //Get Supplier
        $dataSupplier = $this->SupplierModel->getSupplierByType('BAHAN BAKU');

        // Get Bagian
        $dataBagian = $this->BagianModel->where('deletedAt', null)->findAll();

        // Get Satuan
        $dataSatuan = $this->SatuansModel->where('deletedAt', null)->findAll();
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);
        $dataBCType = $this->metadataModel->get_by_name('jenis_dok_aju');

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        $data = [
            "today" => date("d/m/Y"),
            "dataSatuan"    => $dataSatuan,
            "dataBagian"    => $dataBagian,
            "dataCompany" => $dataCompany,
            "dataSupplier" => $dataSupplier,
            "dataWarehouse" => $dataWarehouse,
            "dataBCType"    => $dataBCType,

        ];

        $spesifikasi = [];

        if (!empty($id)) {
            $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalById($id);
            $dataBBLokalDetail = $this->RMPurchaseOrderDetailModel->getPoBBLokalDetailById($id);
            $dataBarang = $this->barangModel->getBySupplier($dataBBLokal->supplier_id);
            $spesifikasi = $this->SupplierHargaModel->getByBarangandSupplier($dataBBLokal->barang_id, $dataBBLokal->supplier_id);
            $data["dataSpesifikasi"] = $spesifikasi;
            $data["dataBarang"] = $dataBarang;
            $data["dataPOLokal"] = $dataBBLokal;
            $data["dataPOLokal"]->rm_purchase_order_details = $dataBBLokalDetail;
        }

        // dd($data["dataPOLokal"]);

        return view('Purchase/poLokalBahanBaku/form', $data);
    }

    public function allPOLokalBahanBaku()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];
        $condition = [
            'rm_purchase_orders.deletedAt' => null,
            'rm_purchase_order_details.deletedAt' => null
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];
        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $poData = $this->RMPurchaseOrderModel->getPoBBList($condition, $addCondition, $limit, $offset);

        $dataPOLokal = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poData['data'] as $data) {
            array_push($dataPOLokal, [
                "no"            => $no++,
                "id"            => $data->id,
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "po_no"         => $data->po_no,
                "companyName"   => $data->companyName,
                "supplierName"  => $data->supplierName,
                "itemCount"     => $data->itemCount,
                "total"         => "Rp " . number_format(formatter($data->total, "STR_TO_FLOAT"), 2, '.', ','),
                "is_posted"     => $data->is_posted,
                "status_penerimaan" => $data->status_penerimaan === "0" ? "OPEN" : "CLOSED",
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $poData['totalData'],
            "recordsFiltered"   => $poData['totalFilteredData'],
            "data"              => $dataPOLokal,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePOLokalBahanBaku()
    {
        try {
            $rules = [
                "po_date" => [
                    "rules" => "required"
                ],
                "po_no" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "barang_id" => [
                    "rules" => "required"
                ],
                "company_id" => [
                    "rules" => "required"
                ],
                "pph" => [
                    "rules" => "required"
                ],
                // "subsidi_langsung" => [
                //     "rules" => "required"
                // ],
                // "cong_sebenarnya" => [
                //     "rules" => "required"
                // ],
                // "cong_batasan" => [
                //     "rules" => "required"
                // ]
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
                $insertData = [
                    "warehouse_id" => $this->request->getVar("warehouse_id"),
                    "company_id" => $this->request->getVar("company_id"),
                    "bc_type" => $this->request->getVar("bc_type"),
                    "po_no" => !empty($this->request->getVar("auto_generate")) ? $this->RMPurchaseOrderModel->generateNoPo() : $this->request->getVar("po_no"),
                    "po_date" => $this->request->getVar("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("po_date")))) : "",
                    "supplier_id" => $this->request->getVar("supplier_id"),
                    "barang_id" => $this->request->getVar("barang_id"),
                    "pph" => $this->request->getVar("pph"),
                    "is_posted" => false,
                    "cong_sebenarnya" => $this->request->getVar("cong_sebenarnya") ? formatter($this->request->getVar("cong_sebenarnya"), "STR_TO_INT") : 0,
                    "cong_batasan" => $this->request->getVar("cong_batasan") ? formatter($this->request->getVar("cong_batasan"), "STR_TO_INT") : 0,
                    "subsidi_langsung" => $this->request->getVar("subsidi_langsung") ? formatter($this->request->getVar("subsidi_langsung"), "STR_TO_INT") : 0,
                    "createdBy" => session()->get("login")->user_id,
                    "items" =>  json_decode($this->request->getVar("items"))
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    $totalPrice += $value->qty * ($value->general_price + $value->daily_price + $value->monthly_price);
                };

                $insertData["total"] = $totalPrice;

                $payload = json_encode($insertData);

                $insert = $this->RMPurchaseOrderModel->insert($insertData);

                foreach ($insertData["items"] as $value) {
                    $value->rm_purchase_order_id = $insert;
                    $value->remaining_qty = $value->qty;
                }

                $this->RMPurchaseOrderDetailModel->insertBatch($insertData["items"]);

                if ($insert) {
                    $data = [
                        "id" => $insert,
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Data Gagal Disimpan",
                        "payload"   => $payload,
                        'token' => csrf_hash(),
                    ];
                    echo json_encode($data);
                }
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

    public function updatePOLokalBahanBaku()
    {
        try {
            $rules = [
                "po_date" => [
                    "rules" => "required"
                ],
                "po_no" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "barang_id" => [
                    "rules" => "required"
                ],
                "company_id" => [
                    "rules" => "required"
                ],
                "pph" => [
                    "rules" => "required"
                ],
                // "subsidi_langsung" => [
                //     "rules" => "required"
                // ],
                // "cong_sebenarnya" => [
                //     "rules" => "required"
                // ],
                // "cong_batasan" => [
                //     "rules" => "required"
                // ]
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
                $id = $this->request->getVar("id");

                $insertData = [
                    "warehouse_id" => $this->request->getVar("warehouse_id"),
                    "company_id" => $this->request->getVar("company_id"),
                    "bc_type" => $this->request->getVar("bc_type"),
                    "po_no" => !empty($this->request->getVar("auto_generate")) ? $this->RMPurchaseOrderModel->generateNoPo() : $this->request->getVar("po_no"),
                    "po_date" => $this->request->getVar("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("po_date")))) : "",
                    "supplier_id" => $this->request->getVar("supplier_id"),
                    "barang_id" => $this->request->getVar("barang_id"),
                    "pph" => $this->request->getVar("pph"),
                    "is_posted" => false,
                    "cong_sebenarnya" => $this->request->getVar("cong_sebenarnya") ? formatter($this->request->getVar("cong_sebenarnya"), "STR_TO_INT") : 0,
                    "cong_batasan" => $this->request->getVar("cong_batasan") ? formatter($this->request->getVar("cong_batasan"), "STR_TO_INT") : 0,
                    "subsidi_langsung" => $this->request->getVar("subsidi_langsung") ? formatter($this->request->getVar("subsidi_langsung"), "STR_TO_INT") : 0,
                    "createdBy" => session()->get("login")->user_id,
                    "items" =>  json_decode($this->request->getVar("items"))
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    if (empty($value->isDeleted)) {
                        $totalPrice += $value->qty * ($value->general_price + $value->daily_price + $value->monthly_price);
                    }
                };

                $insertData["total"] = $totalPrice;

                if ($insertData) {
                    $this->RMPurchaseOrderModel->update($id, $insertData);

                    foreach ($insertData["items"] as $value) {
                        $value->rm_purchase_order_id = $id;

                        if (!empty($value->isDeleted)) {
                            $this->RMPurchaseOrderDetailModel->where('id', $value->id)->delete();
                        }

                        $dataDetail = [
                            "id" => $value->id ?? null,
                            "rm_purchase_order_id" => $this->request->getVar("id"),
                            "supplier_harga_id" => $value->supplier_harga_id,
                            "satuan_id" => $value->satuan_id,
                            "bagian" => $value->bagian,
                            "peti" => $value->peti,
                            "quality" => $value->quality,
                            "note" => $value->note,
                            "qty" => $value->qty,
                            // "remaining_qty" => $value->remaining_qty,
                            "general_price" => $value->general_price,
                            "daily_price" => $value->daily_price,
                            "monthly_price" => $value->monthly_price,
                        ];

                        $this->RMPurchaseOrderDetailModel->upsert($dataDetail);
                    }

                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   =>  json_encode($insertData),
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => 'Data Gagal Diubah',
                        "payload"   =>  json_encode($insertData),
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
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

    public function closePOLokalBahanBaku()
    {
        try {
            $id = $this->request->getVar("id");

            $payload = [
                "status_penerimaan" => true
            ];

            if (!empty($id)) {
                $this->RMPurchaseOrderModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "message"   => "PO Berhasil di Close",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "PO Gagal di Close",
                    "payload"   => json_encode($payload),
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

    public function updateStatusPOLokalBahanBaku()
    {
        try {
            $id = $this->request->getVar("id");

            // po posting
            $payload = [
                "is_posted" => "1"
            ];

            $detail = $this->RMPurchaseOrderModel->where('id', $id)->first();

            // cek if warehouse_id != null
            if ($detail['warehouse_id'] != null && $detail['warehouse_id'] != 0) {
                $this->penerimaanBarangModel->generateLpbBB($detail['id'], $detail['warehouse_id'], $detail['bc_type']);
            }

            if (!empty($id)) {
                $this->RMPurchaseOrderModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "message"   => "Data Berhasil diposting",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Disimpan",
                    "payload"   => json_encode($payload),
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

    public function deletePOLokalBahanBaku()
    {
        try {
            $id = $this->request->getVar("id");

            if (empty($id)) {
                $data = [
                    "status"     => false,
                    "message"    => "Data Gagal Dihapus",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $this->RMPurchaseOrderModel->delete($id);
            $this->RMPurchaseOrderDetailModel->where('rm_purchase_order_id', $id)->delete();

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
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
            $filename = "PO LOKAL Bahan Baku";

            $data = [];
            $dataPO = $this->RMPurchaseOrderModel->getPoBBLokalById($id);
            $dataPO->itemName = $dataPO->barangName;
            $dataPO->lpb = null;
            $dataPO->lpbDetail = null;

            if ($dataPO) {
                $dataPODetail = $this->RMPurchaseOrderDetailModel->getPoBBLokalDetailById($id);

                $totalPrice = 0;
                $totalDailyPrice = 0;
                $totalQty = 0;
                $pphTax = !empty($dataPO->supplierNPWP) ? 0.0025 : 0.005;

                $dataPO->nilai_pph = !empty($dataPO->supplierNPWP) ? 0.0025 : 0.005;

                $objPph = [
                    "None" => 0,
                    "Supplier" => 1,
                    "Company" => -1,
                ];

                $pphTax *= $objPph[$dataPO->pph];

                foreach ($dataPODetail as $value) {
                    $totalPrice += formatter($value->general_price, "CURR_TO_FLOAT") * formatter($value->qty, "CURR_TO_FLOAT");
                    $totalDailyPrice += formatter($value->daily_price, "CURR_TO_FLOAT") * formatter($value->qty, "CURR_TO_FLOAT");
                    $totalQty += formatter($value->qty, "STR_TO_FLOAT");
                }
                $dataPO->totalPrice = number_format($totalPrice, 2, '.', ',');
                $dataPO->totalDailyPrice = number_format($totalDailyPrice, 2, '.', ',');
                $dataPO->totalQty = number_format($totalQty, 2, '.', ',');
                $dataPO->totalPph = number_format($totalPrice * $pphTax, 2, '.', ',');
                $dataPO->totalDailyPph = number_format($totalDailyPrice * $pphTax, 2, '.', ',');
                $dataPO->totalPaid = number_format($totalPrice + $totalPrice * $pphTax, 2, '.', ',');
                $dataPO->totalDailyPaid = number_format(($totalDailyPrice + $totalDailyPrice * $pphTax), 2, '.', ',');
                $dataPO->amount = terbilang($totalPrice);
                $dataPO->amountDaily = terbilang($totalDailyPrice);
                $dataPO->selisih = ($dataPO->cong_batasan ? formatter($dataPO->cong_batasan, "STR_TO_FLOAT") : 0) - ($dataPO->cong_sebenarnya ? formatter($dataPO->cong_sebenarnya, "STR_TO_FLOAT") : 0);
                $dataPO->totalTambahan = $dataPO->selisih * $totalQty;
                $dataPO->pphTambahan = $dataPO->totalTambahan;

                if ($dataPODetail) {
                    $data["dataPO"] = $dataPO;
                    $data["dataPODetail"] = $dataPODetail;
                }
            }

            $lpb = $this->penerimaanBarangModel->where('status_penerimaan', "LOKAL")->where('tipe_bahan', "BAKU")->like('multiple_po_id', $id)->first();

            if ($lpb != null) {
                $lpbDetail = $this->penerimaanBarangModel->getById($lpb['id']);
                $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($lpb['id'], "BAKU", "LOKAL");
                $dataPO->lpb = $lpbDetail;
                $dataPO->lpbDetail = $dataPenerimaanBarangDetail;
            }

            // dd($data);

            // load HTML content
            $this->dompdf->loadHtml(view('Purchase/poLokalBahanBaku/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'portrait');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Purchase/poImportBahanPenolong/print', $data);
        }
    }

    public function dropdownPOLokalBahanBaku()
    {
        $id = formatter($this->request->getVar("id"), "STR_TO_INT");

        $dataPOLokal = $this->RMPurchaseOrderModel->getNoPenerimaanBarang($id, $this->this_company_id);

        $data = [
            "data" => $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOLokalBahanBaku()
    {
        $id = $this->request->getVar("id");

        $dataPOLokal = $this->RMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownWarehouse()
    {
        $res = $this->warehousesModel->get_by_company_id($this->request->getVar('company_id'));
        return response()->setJSON([
            'data' => $res
        ]);
    }
}
