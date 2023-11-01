<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\SppModel;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\BeaCukaiModel;
use Dompdf\Dompdf;

class POLokalBahanPenolong extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $AMPurchaseOrderModel;
    protected $AMPurchaseOrderDetailModel;
    protected $MetadataModel;
    protected $SppModel;
    protected $SupplierModel;
    protected $DivisisModel;
    protected $BeaCukaiModel;
    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->AMPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->AMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->MetadataModel = new MetadataModel();
        $this->SppModel = new SppModel();
        $this->SupplierModel = new SupplierModel();
        $this->DivisisModel = new DivisisModel();
        $this->BeaCukaiModel = new BeaCukaiModel();
        $this->dompdf = new Dompdf();
    }

    public function poLokalBahanPenolong()
    {
        return view('Purchase/poLokalBahanPenolong/index');
    }

    public function createPOLokalBahanPenolong()
    {
        //Get BC Type By Metadata
        $dataBCType = $this->MetadataModel->get_by_name('Bea Cukai');

        //Get SPP Number
        $dataSPP = $this->SppModel->getNoSPP('Lokal');

        foreach (array_keys($dataSPP) as $key) {
            $dataSPP[$key] = (object)$dataSPP[$key];
        }

        //Get Supplier
        $dataSupplier = $this->SupplierModel->getSupplierByType('BAHAN PENOLONG');

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        //Get Valuta By Metadata
        $dataValuta = $this->MetadataModel->get_by_name('Valuta');

        foreach (array_keys($dataValuta) as $key) {
            $dataValuta[$key] = (object)$dataValuta[$key];
        }

        //Get BC Type By Metadata
        $dataBC = $this->MetadataModel->get_by_name('Bea Cukai');

        foreach (array_keys($dataBC) as $key) {
            $dataBC[$key] = (object)$dataBC[$key];
        }

        $data = [
            "dataBCType" => $dataBCType,
            "today" => date("d/m/Y"),
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta,
            "dataBC" => $dataBC
        ];

        return view('Purchase/poLokalBahanPenolong/form', $data);
    }

    public function getByIdPOLokalBahanPenolong($id = null)
    {
        //Get BC Type By Metadata
        $dataBCType = $this->MetadataModel->get_by_name('Bea Cukai');

        //Get SPP Number
        $dataSPP = $this->SppModel->getNoSPP('Bahan Penolong Lokal');

        foreach (array_keys($dataSPP) as $key) {
            $dataSPP[$key] = (object)$dataSPP[$key];
        }

        //Get Supplier
        $dataSupplier = $this->SupplierModel->getSupplierByType('BAHAN PENOLONG');

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        //Get Valuta By Metadata
        $dataValuta = $this->MetadataModel->get_by_name('Valuta');

        foreach (array_keys($dataValuta) as $key) {
            $dataValuta[$key] = (object)$dataValuta[$key];
        }

        //Get BC Type By Metadata
        $dataBC = $this->MetadataModel->get_by_name('Bea Cukai');

        foreach (array_keys($dataBC) as $key) {
            $dataBC[$key] = (object)$dataBC[$key];
        }

        $data = [
            "dataBCType" => $dataBCType,
            "today" => date("d/m/Y"),
            "dataSPP" => $dataSPP,
            "dataSupplier" => $dataSupplier,
            "dataValuta" => $dataValuta,
            "dataBC" => $dataBC
        ];

        if (!empty($id)) {
            $dataBPLokal = $this->AMPurchaseOrderModel->getPOById($id);
            $dataBPLokalDetail = $this->AMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);
            foreach (array_keys($dataBPLokalDetail) as $key) {
                $dataBPLokalDetail[$key] = (object)$dataBPLokalDetail[$key];
            }
            $data["dataPOLokal"] = $dataBPLokal;
            $data["dataPOLokal"]->am_purchase_order_details = $dataBPLokalDetail;
        }

        return view('Purchase/poLokalBahanPenolong/form', $data);

        return;
    }

    public function allPOLokalBahanPenolong()
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

        $condition = [
            'po_type' => "Lokal"
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $poData = $this->AMPurchaseOrderModel->getPoList($condition, $addCondition, $limit, $offset);

        $dataPOLokal = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poData['data'] as $data) {
            array_push($dataPOLokal, [
                "no"            => $no++,
                "id"            => $data->id,
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "purchase_request_id" => $data->purchase_request_id,
                "po_no"         => $data->po_no,
                "companyName"  => $data->companyName,
                "supplierName"  => $data->supplierName,
                "total"         => "Rp " . number_format(formatter($data->total, "STR_TO_FLOAT"), 2, '.', ','),
                "is_posted"     => $data->is_posted,
                "itemCount"     => $data->itemCount,
                "status_penerimaan" => $data->status_penerimaan === "0" ? "OPEN" : "CLOSED"
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

    public function savePOLokalBahanPenolong()
    {
        try {
            $rules = [
                "purchase_request_id" => [
                    "rules" => "required"
                ],
                "po_no" => [
                    "rules" => "required"
                ],
                "po_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                // "payment_term" => [
                //     "rules" => "required"
                // ],
                "payment_date" => [
                    "rules" => "required"
                ],
                // "dpp" => [
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
                $purchase_request_id = formatter($this->request->getPost("purchase_request_id"), "STR_TO_INT");

                $insertData = [
                    "purchase_request_id"   => $purchase_request_id,
                    "bc_type"               => $this->request->getPost("bc_type"),
                    "po_no"                 => !empty($this->request->getPost("auto_generate")) ? "" : $this->request->getPost("po_no"),
                    "po_date"               => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                    "po_type"               => 'Lokal',
                    "supplier_id"           => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                    //"payment_term"          => $this->request->getPost("payment_term") ? formatter($this->request->getPost("payment_term"), "STR_TO_INT") : 0,
                    "payment_date"          => $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))) : "",
                    //"dpp"                   => formatter($this->request->getPost("dpp"), "CURR_TO_INT"),
                    "note"                  => $this->request->getPost("note"),
                    "isPosted"              => false,
                    "createdBy"             => session()->get("login")->user_id,
                    "items"                 => json_decode($this->request->getPost("items"))
                ];

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    $totalPrice += $value->qty * $value->price;
                };

                $insertData["total"] = $totalPrice;

                if ($insertData["po_no"] === "") {
                    $dataDivisi = $this->DivisisModel->find($this->request->getPost("divisi_id"));
                    $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
                    $insertData["po_no"] = $this->AMPurchaseOrderModel->get_no(date('d'), date('m'), date('Y'), $dataDivisi["divisi"], date('y'), $this->request->getPost("divisi_id"), $last_day);
                };

                $insert = $this->AMPurchaseOrderModel->insert($insertData);

                foreach ($insertData["items"] as $value) {
                    $value->barang_id = $value->item_id;
                    $value->am_purchase_order_id = $insert;
                    $value->remaining_qty = $value->qty;
                }

                $this->AMPurchaseOrderDetailModel->insertBatch($insertData["items"]);

                $payload = json_encode($insertData);

                if ($insert) {
                    // spp number cannot be used again
                    $responsespp = $this->SppModel->where(['id' => $purchase_request_id])->set(['request_status' => 'finished'])->update();

                    if (!$responsespp) {
                        $data = [
                            "status"            => false,
                            "message"    => "No. SPP gagal di close",
                            "payload"   => "",
                            'token' => csrf_hash()
                        ];
                        echo json_encode($data);
                        return;
                    }

                    $data = [
                        "id"        => $insert,
                        "status"    => true,
                        "message"   => "Data Berhasil disimpan",
                        "payload"   => $payload,
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"    => false,
                        "message"   => "Data Gagal Disimpan",
                        "payload"   => $payload,
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                }
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

    public function updatePOLokalBahanPenolong()
    {
        try {
            $rules = [
                "po_no" => [
                    "rules" => "required"
                ],
                "po_date" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                // "payment_term" => [
                //     "rules" => "required"
                // ],
                "payment_date" => [
                    "rules" => "required"
                ],
                // "dpp" => [
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
                $id = $this->request->getPost("id");

                if (!empty($this->request->getPost("auto_generate"))) {
                    $dataDivisi = $this->DivisisModel->find($this->request->getPost("divisi_id"));
                    $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));

                    $insertData = [
                        "po_no"                 => $this->AMPurchaseOrderModel->get_no(date('d'), date('m'), date('Y'), $dataDivisi["divisi"], date('y'), $this->request->getPost("divisi_id"), $last_day),
                        "po_date"               => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                        "po_type"               => 'Lokal',
                        "bc_type"               => $this->request->getPost("bc_type"),
                        "supplier_id"           => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                        //"payment_term"          => $this->request->getPost("payment_term") ? formatter($this->request->getPost("payment_term"), "STR_TO_INT") : 0,
                        "payment_date"          => $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))) : "",
                        //"dpp"                   => formatter($this->request->getPost("dpp"), "CURR_TO_INT"),
                        "note"                  => $this->request->getPost("note"),
                        "isPosted"              => false,
                        "createdBy"             => session()->get("login")->user_id,
                        "items"                 => json_decode($this->request->getPost("items"))
                    ];
                } else {
                    $insertData = [
                        "po_no"                 => $this->request->getPost("po_no"),
                        "po_date"               => $this->request->getPost("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("po_date")))) : "",
                        "po_type"               => 'Lokal',
                        "supplier_id"           => formatter($this->request->getPost("supplier_id"), "STR_TO_INT"),
                        //"payment_term"          => $this->request->getPost("payment_term") ? formatter($this->request->getPost("payment_term"), "STR_TO_INT") : 0,
                        "payment_date"          => $this->request->getPost("payment_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getPost("payment_date")))) : "",
                        //"dpp"                   => formatter($this->request->getPost("dpp"), "CURR_TO_INT"),
                        "note"                  => $this->request->getPost("note"),
                        "isPosted"              => false,
                        "createdBy"             => session()->get("login")->user_id,
                        "items"                 => json_decode($this->request->getPost("items"))
                    ];
                }

                $totalPrice = 0;

                foreach ($insertData["items"] as $value) {
                    if (empty($value->isDeleted)) {
                        $totalPrice += $value->qty * $value->price;
                    }
                };

                $insertData["total"] = $totalPrice;

                if ($insertData) {
                    $this->AMPurchaseOrderModel->update($id, $insertData);

                    foreach ($insertData["items"] as $value) {
                        $value->barang_id = $value->item_id;
                        $value->am_purchase_order_id = $id;

                        if (!empty($value->isDeleted)) {
                            $this->AMPurchaseOrderDetailModel->where('id', $value->id)->delete();
                        }

                        $dataDetail = [
                            "id"                    => $value->id ?? null,
                            "am_purchase_order_id"  => $this->request->getPost("id"),
                            // "barang_id"             => $value->item_id,
                            // "spec"                  => $value->spec,
                            // "note"                  => $value->note,
                            // "unit"                  => $value->unit,
                            // "qty"                   => $value->qty,
                            // "remaining_qty"         => $value->remaining_qty,
                            // "price"                 => $value->price,
                            "disc"                  => $value->disc,
                            "additional_cost"       => $value->additional_cost,
                            "ppn"                   => $value->ppn,
                            "pph"                   => $value->pph,
                        ];

                        $this->AMPurchaseOrderDetailModel->upsert($dataDetail);
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

    public function updateStatusPOLokalBahanPenolong()
    {
        try {
            $id = $this->request->getPost("id");
            $spp = $this->request->getPost("spp");

            // spp close
            $responsespp = $this->SppModel->where(['id' => $spp])->set(['is_posted' => 1])->update();

            if (!$responsespp) {
                $data = [
                    "status"            => false,
                    "message"    => "Gagal close SPP",
                    "payload"   => "",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            // po posting

            $payload = [
                "is_posted" => true
            ];

            if (!empty($id)) {
                $this->AMPurchaseOrderModel->update($id, $payload);

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
                    "message"   => "Data Gagal diposting",
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

    public function closePOLokalBahanPenolong()
    {
        try {
            $id = $this->request->getPost("id");

            $payload = [
                "status_penerimaan" => true
            ];

            if (!empty($id)) {
                $this->AMPurchaseOrderModel->update($id, $payload);

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

    public function deletePOLokalBahanPenolong()
    {
        try {
            $id = $this->request->getPost("id");

            $dataBPLokal = $this->AMPurchaseOrderModel->getPOById($id);

            if (empty($dataBPLokal)) {
                $data = [
                    "status"     => false,
                    "message"    => "Data Gagal Dihapus",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            // spp return to waiting when deleted
            $responsespp = $this->SppModel->where(['id' => $dataBPLokal->purchase_request_id])->set(['request_status' => 'waiting'])->update();

            if (empty($responsespp)) {
                $data = [
                    "status"     => false,
                    "message"    => "Data Gagal Dihapus",
                    'token'      => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $this->AMPurchaseOrderModel->delete($id);
            $this->AMPurchaseOrderDetailModel->where('am_purchase_order_id', $id)->delete();

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

    public function dropdownPOLokalBahanPenolong()
    {
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");

        $dataPOLokal = $this->AMPurchaseOrderModel->getNoPenerimaanBarang("LOKAL", $id);

        $data = [
            "data" => $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOLokalBahanPenolong()
    {
        $id = $this->request->getGet("id");

        $dataPOLokal = $this->AMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function print($id = null)
    {
        if ($id) {
            $filename = "PO Lokal Bahan Penolong";

            if (!empty($id)) {
                $dataBPLokal = $this->AMPurchaseOrderModel->getPOById($id);
                $dataBPLokalDetail = $this->AMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);
                foreach (array_keys($dataBPLokalDetail) as $key) {
                    $dataBPLokalDetail[$key] = (object)$dataBPLokalDetail[$key];
                }

                $no = 0;
                $totalPrice = 0;
                $totalDisc = 0;
                $totalPpn = 0;
                $totalTambahan = 0;
                $keterangan = [];

                foreach ($dataBPLokalDetail as $value) {
                    $no++;
                    $value->no = $no;
                    $totalan = formatter($value->price, "CURR_TO_INT") * formatter($value->qty, "STR_TO_FLOAT") +  formatter($value->additional_cost, "CURR_TO_INT");
                    $value->nilaiPpn = number_format($totalan * (float)$value->ppnValue / 100);
                    $value->nilaiPph = number_format($totalan * (float)$value->pphValue / 100);
                    $totalTambahan += formatter($value->additional_cost, "CURR_TO_INT");
                    $totalPrice += $totalan;
                    $totalDisc += $totalan * (float)$value->disc / 100;
                    $totalPpn += $totalan * (float)$value->ppnValue / 100;
                    $keterangan[] = $value->note;
                }

                $dataBPLokal->totalTambahan = number_format(formatter($totalTambahan, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPrice = number_format(formatter($totalPrice, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalDisc = number_format(formatter($totalDisc, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPpn = number_format(formatter($totalPpn, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPo = number_format(formatter(($totalPrice - $totalDisc + $totalPpn), "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->keterangan = implode(",", array_unique($keterangan));
                $dataBPLokal->jatuhTempoHari = \totalDayInRange($dataBPLokal->po_date, $dataBPLokal->payment_date);
                // $dataBPLokal->totalPo = number_format($totalPrice - $totalDisc + $totalPpn + formatter($dataBPLokal->dpp, "CURR_TO_INT"));

                $data["dataPOLokal"] = $dataBPLokal;
                $data["dataPOLokal"]->am_purchase_order_details = $dataBPLokalDetail;
            }

            // return view('Purchase/poLokalBahanPenolong/print', $data);

            // load HTML content
            $this->dompdf->loadHtml(view('Purchase/poLokalBahanPenolong/print', $data));

            // (optional) setup the paper size and orientation
            $this->dompdf->setPaper('A4', 'landscape');

            // render html as PDF
            $this->dompdf->render();

            // output the generated pdf
            $this->dompdf->stream($filename, array("Attachment" => false));

            exit(0);

            // return view('Purchase/poImportBahanPenolong/print', $data);
        }
    }
}
