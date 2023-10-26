<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

use App\Models\CompaniesModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\BeaCukaiModel;
use App\Models\BarangMasterModel;
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
    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->SupplierModel = new SupplierModel();
        $this->BeaCukaiModel = new BeaCukaiModel();
        $this->barangModel = new BarangMasterModel();
        $this->CompaniesModel = new CompaniesModel();
        $this->dompdf = new Dompdf();
    }

    public function poLokalBahanBaku()
    {
        return view('Purchase/poLokalBahanBaku/index');
    }

    public function createPOLokalBahanBaku()
    {
        //Get Company
        $dataCompany =  $this->CompaniesModel->getCompanies();

        //Get Supplier
        $dataSupplier = $this->SupplierModel->getSupplierByType('BAHAN BAKU');

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        $data = [
            "today"         => date("d/m/Y"),
            "dataSupplier"  => $dataSupplier,
            "dataCompany"   => $dataCompany
        ];

        return view('Purchase/poLokalBahanBaku/form', $data);
    }

    public function getByIdPOLokalBahanBaku($id = null)
    {
        //Get Company
        $dataCompany =  $this->CompaniesModel->getCompanies();

        //Get Supplier
        $dataSupplier = $this->SupplierModel->getSupplierByType('BAHAN BAKU');

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        $data = [
            "today" => date("d/m/Y"),
            "dataCompany" => $dataCompany,
            "dataSupplier" => $dataSupplier
        ];

        if (!empty($id)) {
            $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalById($id);
            $dataBBLokalDetail = $this->RMPurchaseOrderDetailModel->getPoBBLokalDetailById($id);
            $dataBarang = $this->barangModel->getBySupplier($id);
            $data["dataBarang"] = $dataBarang;
            $data["dataPOLokal"] = $dataBBLokal;
            $data["dataPOLokal"]->rm_purchase_order_details = $dataBBLokalDetail;
        }

        return view('Purchase/poLokalBahanBaku/form', $data);
        return;
    }

    public function allPOLokalBahanBaku()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];
        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
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
            "draw"              => intval($this->request->getGet("draw")),
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
                "subsidi_langsung" => [
                    "rules" => "required"
                ],
                "cong_sebenarnya" => [
                    "rules" => "required"
                ],
                "cong_batasan" => [
                    "rules" => "required"
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
                $insertData = [
                    "company_id" => $this->request->getPost("company_id"),
                    "po_no" => !empty($this->request->getPost("auto_generate")) ? $this->RMPurchaseOrderModel->generateNoPo() : $this->request->getPost("po_no"),
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "supplier_id" => $this->request->getPost("supplier_id"),
                    "barang_id" => $this->request->getPost("barang_id"),
                    "pph" => $this->request->getPost("pph"),
                    "is_posted" => false,
                    "cong_sebenarnya" => $this->request->getPost("cong_sebenarnya") ? formatter($this->request->getPost("cong_sebenarnya"), "STR_TO_INT") : 0,
                    "cong_batasan" => $this->request->getPost("cong_batasan") ? formatter($this->request->getPost("cong_batasan"), "STR_TO_INT") : 0,
                    "subsidi_langsung" => $this->request->getPost("subsidi_langsung") ? formatter($this->request->getPost("subsidi_langsung"), "STR_TO_INT") : 0,
                    "createdBy" => session()->get("login")->user_id,
                    "items" =>  json_decode($this->request->getPost("items"))
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    $totalPrice += $value->qty * $value->general_price;
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
                "subsidi_langsung" => [
                    "rules" => "required"
                ],
                "cong_sebenarnya" => [
                    "rules" => "required"
                ],
                "cong_batasan" => [
                    "rules" => "required"
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
                $id = $this->request->getPost("id");

                $insertData = [
                    "company_id" => $this->request->getPost("company_id"),
                    "po_no" => !empty($this->request->getPost("auto_generate")) ? $this->RMPurchaseOrderModel->generateNoPo() : $this->request->getPost("po_no"),
                    "po_date" => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "supplier_id" => $this->request->getPost("supplier_id"),
                    "barang_id" => $this->request->getPost("barang_id"),
                    "pph" => $this->request->getPost("pph"),
                    "is_posted" => false,
                    "cong_sebenarnya" => $this->request->getPost("cong_sebenarnya") ? formatter($this->request->getPost("cong_sebenarnya"), "STR_TO_INT") : 0,
                    "cong_batasan" => $this->request->getPost("cong_batasan") ? formatter($this->request->getPost("cong_batasan"), "STR_TO_INT") : 0,
                    "subsidi_langsung" => $this->request->getPost("subsidi_langsung") ? formatter($this->request->getPost("subsidi_langsung"), "STR_TO_INT") : 0,
                    "createdBy" => session()->get("login")->user_id,
                    "items" =>  json_decode($this->request->getPost("items"))
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    if (empty($value->isDeleted)) {
                        $totalPrice += $value->qty * $value->general_price;
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
                            "rm_purchase_order_id" => $this->request->getPost("id"),
                            "supplier_harga_id" => $value->supplier_harga_id,
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
            $id = $this->request->getPost("id");

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
            $id = $this->request->getPost("id");

            // po posting
            $payload = [
                "is_posted" => "1"
            ];

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
            $id = $this->request->getPost("id");

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
                $dataPO->pphTambahan = $dataPO->totalTambahan * $pphTax;

                if ($dataPODetail) {
                    $data["dataPO"] = $dataPO;
                    $data["dataPODetail"] = $dataPODetail;
                }
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
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");

        $dataPOLokal = $this->RMPurchaseOrderModel->getNoPenerimaanBarang($id, $this->this_company_id);

        $data = [
            "data" => $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOLokalBahanBaku()
    {
        $id = $this->request->getGet("id");

        $dataPOLokal = $this->RMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }
}
