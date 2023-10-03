<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;

use App\Models\ImportPOPaymentModel;
use App\Models\SupplierModel;
use App\Models\RMImportPOModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\PenerimaanBarangModel;

class PembayaranPOImport extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function pembayaranPOImport()
    {
        return view('Pembayaran/pembayaranPOImport/index');
    }

    public function createPembayaranPOImport()
    {
        return view('Pembayaran/pembayaranPOImport/form');
    }

    public function getByIdPembayaranPOImport($id)
    {
        $importPOPaymentModel = new ImportPOPaymentModel();
        $supplierModel = new SupplierModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $selectQry = "import_po_payments.*, 
                      DATE_FORMAT(import_po_payments.payment_date, '%d/%m/%Y') AS payment_date,
                      metadata.value AS currency";
        $paymentData = $importPOPaymentModel->asObject()
            ->select($selectQry)
            ->join('metadata', 'metadata.id = import_po_payments.currency')
            ->find($id);

        $poType = $paymentData->po_type == 'BAKU' ? 'BAHAN BAKU' : 'BAHAN PENOLONG';

        $supplierCondition = [
            'kategori'  => 'IMPORT',
            'type'      => $poType,
            'company_id' => $this->this_company_id
        ];

        $supplierList = $supplierModel->asObject()
            ->where($supplierCondition)
            ->findAll();

        $poData = null;
        $poList = null;
        $lpbList = null;

        if ($paymentData->payment_type == 'DP') {
            $poData = $this->checkPO($paymentData->supplier_id, $paymentData->po_id, $paymentData->po_type);
            $poList = $this->getPOList($paymentData->supplier_id, $paymentData->po_type);
        } else {
            $poData = $penerimaanBarangModel->asObject()
                ->select("SUM(penerimaan_barang_detail.harga * penerimaan_barang_detail.qty) AS total")
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL')
                ->groupBy('penerimaan_barang_id')
                ->find($paymentData->penerimaan_barang_id);

            $selectQry = "penerimaan_barang.id AS id,
                          no_penerimaan_barang AS lpb_no, 
                          'USD' AS currency,
                          SUM(penerimaan_barang_detail.harga * penerimaan_barang_detail.qty) AS total";

            $lpbList = $penerimaanBarangModel->asObject()
                ->select($selectQry)
                ->where('penerimaan_barang.supplier_id', $paymentData->supplier_id)
                ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id AND penerimaan_barang_detail.deletedAt IS NULL')
                ->groupBy('penerimaan_barang_id')
                ->findAll();
        }

        $data = [
            'paymentData'   => $paymentData,
            'supplierList'  => $supplierList,
            'poData'        => $poData,
            'poList'        => $poList,
            'lpbList'        => $lpbList
        ];
        return view('Pembayaran/pembayaranPOImport/form', $data);
    }

    public function allPembayaranPOImport()
    {
        $importPOPaymentModel = new ImportPOPaymentModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $dataPembayaranPOImport = [];

        $condition = [
            "suppliers.company_id"  => $this->this_company_id
        ];
        $addCondition = [
            "startDate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastDate"  => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $paymentList = $importPOPaymentModel->getPaymentList($condition, $addCondition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentList['data'] as $data) {
            array_push($dataPembayaranPOImport, [
                "no"                => $no++,
                "id"                => $data->id,
                "payment_no"        => $data->payment_no,
                "supplier_name"     => $data->supplier_name,
                "currency"          => $data->currency,
                "amount"            => "Rp " . number_format($data->payment_amt ?? 0, 0, ',', '.'),
                "payment_date"      => $data->payment_date
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $paymentList['totalData'],
            "recordsFiltered"   => $paymentList['totalFilteredData'],
            "data"              => $dataPembayaranPOImport,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePembayaranPOImport()
    {
        try {
            $supplierModel = new SupplierModel();
            $importPOPaymentModel = new ImportPOPaymentModel();

            $rules = [
                "payment_type" => [
                    "rules" => "required|in_list[DP,Pelunasan]",
                    "messages" => [
                        "required" => "Jenis pembayaran harus diisi.",
                        "in_list" => "Jenis pembayaran harus salah satu dari DP atau Pelunasan."
                    ]
                ],
                "po_type" => [
                    "rules" => "required|in_list[BAKU,PENOLONG]",
                    "messages" => [
                        "required" => "Tipe PO harus diisi.",
                        "in_list" => "Tipe PO harus salah satu dari BAKU atau PENOLONG."
                    ]
                ],
                "supplier_id" => [
                    "rules" => "required|is_natural",
                    "messages" => [
                        "required" => "ID Supplier harus diisi.",
                        "is_natural" => "ID Supplier harus berupa angka."
                    ]
                ],
                "import_po" => [
                    "rules" => "permit_empty|is_natural",
                    "messages" => [
                        "is_natural" => "Import PO harus berupa angka."
                    ]
                ],
                "import_lpb" => [
                    "rules" => "permit_empty|is_natural",
                    "messages" => [
                        "is_natural" => "Import LPB harus berupa angka."
                    ]
                ],
                "payment_amt" => [
                    "rules" => "required",
                    "messages" => [
                        "required" => "Jumlah pembayaran harus diisi."
                    ]
                ],
                "current_exchange_rate" => [
                    "rules" => "required",
                    "messages" => [
                        "required" => "Kurs saat ini harus diisi."
                    ]
                ],
                "payment_date" => [
                    "rules" => "required|valid_date[d/m/Y]",
                    "messages" => [
                        "required" => "Tanggal pembayaran harus diisi.",
                        "valid_date" => "Format tanggal tidak valid. Gunakan format dd/mm/yyyy."
                    ]
                ],
                "termin" => [
                    "rules" => "permit_empty|is_natural",
                    "messages" => [
                        "is_natural" => "Termin harus berupa angka."
                    ]
                ],
                "payment_method" => [
                    "rules" => "required",
                    "messages" => [
                        "required" => "Metode pembayaran harus diisi."
                    ]
                ],
                "voucher_no" => [
                    "rules" => "permit_empty",
                ],
                "note" => [
                    "rules" => "permit_empty",
                ],
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

            $poId = (int)$this->request->getPost('import_po');
            $penerimaanBarangId = (int)$this->request->getPost('import_lpb');

            if (empty($poId) && empty($penerimaanBarangId)) {
                $data = [
                    "status"    => false,
                    "message"   => 'PO or LPB is required!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $supplierId = (int)$this->request->getPost('supplier_id');
            $supplierData = $supplierModel->asObject()
                ->where('id', $supplierId)
                ->find();

            if (empty($supplierData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Supplier not Found!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $poType = (string)$this->request->getPost('po_type');
            $poData = null;

            // validate PO here
            if ($poId) {
                $poData = $this->checkPO($supplierId, $poId, $poType);

                if (empty($poData)) {
                    $data = [
                        "status"    => false,
                        "message"   => 'PO not Found!',
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }
            }

            $paymentAmtNatural = preg_replace("/[^0-9,]/", "", $this->request->getPost('payment_amt'));
            $paymentAmtNatural = str_replace(",", ".", $paymentAmtNatural);
            $paymentAmt = number_format((float) $paymentAmtNatural, 3, '.', '');

            $currentExchangeRateNatural = preg_replace("/[^0-9,]/", "", $this->request->getPost('current_exchange_rate'));
            $currentExchangeRateNatural = str_replace(",", ".", $currentExchangeRateNatural);
            $currentExchangeRate = number_format((float) $paymentAmtNatural, 3, '.', '');

            $payload = [
                'company_id'            => $this->this_company_id,
                'payment_no'            => $this->generatePaymentNo(),
                'payment_type'          => $this->request->getPost('payment_type'),
                'po_type'               => $poType,
                'supplier_id'           => $supplierId,
                'po_id'                 => $poId,
                'penerimaan_barang_id'  => $penerimaanBarangId,
                'voucher_no'            => $this->request->getPost('voucher_no'),
                'currency'              => $poData->currency ?? 32,
                'payment_amt'           => $paymentAmt,
                'current_exchange_rate' => $currentExchangeRate,
                'payment_date'          => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("payment_date")))),
                'termin'                => $this->request->getPost('termin'),
                'payment_method'        => $this->request->getPost('payment_method'),
                'voucher_no'            => $this->request->getPost('voucher_no'),
                'note'                  => $this->request->getPost('note')
            ];

            $importPOPaymentModel->db->transException(true)->transStart();
            $importPOPaymentModel->insert($payload);
            // update paid po here

            $importPOPaymentModel->db->transComplete();

            $data = [
                "id"        => "",
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $payload,
                'token'     => csrf_hash(),
                'code'      => 201
            ];
            echo json_encode($data);
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

    public function updatePembayaranPOImport()
    {
        try {
            $rules = [
                "nominal_faktur" => [
                    "rules" => "required"
                ]
            ];

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = json_encode([
                    "multiple_faktur_id" => json_decode($this->request->getVar("multiple_faktur_id")),
                    "multiple_faktur_no" => json_decode($this->request->getVar("multiple_faktur_no")),
                    "nominal_faktur" => formatter($this->request->getPost("nominal_faktur"), "CURR_TO_INT"),
                    "payment_type" => "IMPORT"
                ]);

                // $data = [
                //     "status"            => false,
                //     "message"    => $payload,
                //     "payload"   => $payload,
                //     'token' => csrf_hash()
                // ];
                // echo json_encode($data);

                $response = curl_request("PATCH", "/buktiPembayaran/$id", $this->token, $payload);

                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil diubah",
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Diubah';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                }
            } else {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Diubah",
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

    public function deletePembayaranPOImport()
    {
        try {
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $response = curl_request("DELETE", "/buktiPembayaran/$id", $this->token);
                if ($response["code"] === 200) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                } else {
                    $message = is_object(json_decode($response["body"])) ? json_decode($response["body"])->message : 'Data Gagal Dihapus';
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

    private function generatePaymentNo(): string
    {
        $importPOPayModel = new ImportPOPaymentModel();

        $month = idate('m');
        $year = date('Y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "PAY/IM/$romanMonth/$year/";

        $lastData = $importPOPayModel->asObject()
            ->like('payment_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $summaryNo = "{$numberTemplate}0001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->payment_no);
            $lastIncrement = (int)$exploded[4] + 1;

            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $summaryNo = $numberTemplate . $paddedNumber;
        }

        return $summaryNo;
    }

    private function checkPO(int $supplierId, int $POId, string $poType): ?object
    {
        if ($poType == 'BAKU') {
            $rmImportPOModel = new RMImportPOModel();
            $poData = $rmImportPOModel->asObject()
                ->where('supplier_id', $supplierId)
                ->find($POId);

            return $poData;
        } else if ($poType == 'PENOLONG') {
            $aMPurchaseOrderModel = new AMPurchaseOrderModel();
            $poData = $aMPurchaseOrderModel->asObject()
                ->where('supplier_id', $supplierId)
                ->find($POId);

            return $poData;
        }

        return null;
    }

    private function getPOList(int $supplierId, string $poType): array
    {
        $condition = [
            'company_id'    => $this->this_company_id,
            'supplier_id'   => $supplierId,
            'is_posted'     => 1
        ];

        if ($poType == 'BAKU') {
            $rmImportPOModel = new RMImportPOModel();
            $selectQry = "rm_import_pos.*,
                      metadata.value AS currency";

            $poData = $rmImportPOModel->asObject()
                ->select($selectQry)
                ->join('metadata', 'metadata.id = rm_import_pos.currency')
                ->where($condition)
                ->findAll();

            return $poData;
        } else if ($poType == 'PENOLONG') {
            $aMPurchaseOrderModel = new AMPurchaseOrderModel();
            $selectQry = "am_purchase_orders.*,
                      metadata.value AS currency";

            $poData = $aMPurchaseOrderModel->asObject()
                ->select($selectQry)
                ->join('metadata', 'metadata.id = am_purchase_orders.currency')
                ->where($condition)
                ->findAll();

            return $poData;
        }

        return null;
    }
}
