<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;

use App\Models\SupplierModel;
use App\Models\LocalPOPaymentModel;
use App\Models\LocalPOPaymentDetailModel;
use App\Models\LocalPOInvSummaryModel;
use App\Models\LocalPOInvSumDetailModel;
use App\Models\TandaTerimaFakturDetailModel;
use App\Models\TandaTerimaFakturModel;
use Dompdf\Dompdf;
use Exception;

class PembayaranPOLokal extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function pembayaranPOLokalBP()
    {
        return view('Pembayaran/pembayaranPOLokal/bahanPenolong');
    }

    public function createPembayaranPOLokalBP()
    {
        $supplierModel = new SupplierModel();

        $supplierList = $supplierModel->asObject()
            ->where('deletedAt', null)
            ->where('type', "BAHAN PENOLONG")
            ->orderBy('name', "ASC")
            ->findAll();

        $data = [
            "suppliers" => $supplierList
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanPenolong', $data);
    }

    public function getTandaTerimaFaktur($supplierID)
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $res = $tandaTerimaFakturModel->getListTandaTerimaFakturNotProcessed($supplierID);
        return response()->setJSON([
            'data' => $res,
            'status' => true
        ]);
    }

    public function getItemListByTandaTerimaFaktur($tandaTerimaFakturID)
    {
        $tandaTerimaFakturDetailModel = new TandaTerimaFakturDetailModel();
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();

        return response()->setJSON([
            'detail' => $tandaTerimaFakturModel->getByID($tandaTerimaFakturID),
            'list' => $tandaTerimaFakturDetailModel->getListTandaTerimaItemFaktur($tandaTerimaFakturID)
        ]);
    }

    public function createPembayaranPOLokalBPAction()
    {
        try {
            $localPOPaymentModel = new LocalPOPaymentModel();

            $id = $localPOPaymentModel->insert([
                'payment_no' => $this->request->getVar('no_bukti_pembayaran'),
                'supplier_id' => $this->request->getVar('supplier_id'),
                'tanda_terima_faktur_id' => $this->request->getVar('tanda_terima_faktur_id'),
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('jatuh_tempo')))),
                'amount' => repairDouble($this->request->getVar('nominal_pembayaran')),
                'payment_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('payment_date')))),
                'payment_method' => $this->request->getVar('payment_method'),
                'type_po' => "Bahan Penolong",
                'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh')
            ]);

            return response()->setJSON([
                'message' => "Kwitansi pembayaran lokal bahan penolong berhasil dibuat",
                'status' => true,
                'token' => csrf_hash(),
                'id' => $id
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => "terjadi kesalahan",
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    // PEMBAYARAN BAHAN BAKU ATAU PENOLONG
    public function allPembayaranPOLokal()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $dataPembayaranPOLokal = [];

        $condition = [
            "local_po_payments.type_po"  => $this->request->getGet('type_po'),
            "local_po_payments.deletedAt" => null
        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "dueDate" => $this->request->getGet('dueDate'),
            "paymentDate" => $this->request->getGet('paymentDate')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $paymentData = $localPOPaymentModel->getList($condition, $addCondition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentData['data'] as $data) {
            array_push($dataPembayaranPOLokal, [
                "no"                => $no++,
                "id"                => $data->id,
                "payment_no"        => $data->payment_no,
                "faktur_no"         => $data->faktur_no,
                "supplier"          => $data->supplierName,
                "due_date"          => $data->due_date,
                "payment_date"      => $data->payment_date,
                "payment_method"    => $data->payment_method,
                "amount"            => "Rp " . number_format($data->amount ?? 0, 0, ',', '.')
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $paymentData['totalData'],
            "recordsFiltered"   => $paymentData['totalFilteredData'],
            "data"              => $dataPembayaranPOLokal,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function getPembayaranPOLokalBP($id)
    {
        $supplierModel = new SupplierModel();
        $localPOPaymentModel = new LocalPOPaymentModel();

        $supplierList = $supplierModel->asObject()
            ->where('deletedAt', null)
            ->where('type', "BAHAN PENOLONG")
            ->orderBy('name', "ASC")
            ->findAll();

        $data = [
            "suppliers" => $supplierList,
            "detail" => $localPOPaymentModel->get($id)
        ];

        return view('Pembayaran/pembayaranPOLokal/formBahanPenolong', $data);
    }

    // PRINT PEMBAYARAN PO BP
    public function pembayaranPOLokalBPPrint($id)
    {
        $dompdf = new Dompdf();
        $localPOPaymentModel = new LocalPOPaymentModel();

        $data = [
            "detail" => $localPOPaymentModel->get($id)
        ];

        if ($data['detail'] == null) {
            return redirect()->to('pembayaran-po-lokal-bp');
        }

        $dompdf->loadHtml(view('Pembayaran/pembayaranPOLokal/printBahanPenolong', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Pembayaran PO Lokal Bahan Penolong ", array("Attachment" => false));

        exit(0);
    }

    public function delete()
    {
        $id = $this->request->getVar('id');
        $localPOPaymentModel = new LocalPOPaymentModel();

        $localPOPaymentModel->where('id', $id)->delete();

        return response()->setJSON([
            'message' => "Pembayaran lokal bahan penolong berhasil dihapus",
            'status' => true
        ]);
    }

    public function getByIdPembayaranPOLokal($id)
    {
        $localPOPaymentModel = new LocalPOPaymentModel();
        $localPOPaymentDetModel = new LocalPOPaymentDetailModel();
        $supplierModel = new SupplierModel();
        $localPOInvSummaryModel = new LocalPOInvSummaryModel();

        $selectQry = "local_po_payments.*,
                      DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y') AS payment_date,
                      DATE_FORMAT(local_po_payments.due_date, '%d/%m/%Y') AS due_date
                      ";
        $paymentData = $localPOPaymentModel->asObject()
            ->select($selectQry)
            ->find($id);

        $paymentDetData = $localPOPaymentDetModel->where('local_po_payment_id', $id)
            ->findAll();
        $paidSumDetId = array_column($paymentDetData, 'local_po_inv_sum_detail_id');

        $supplierList = $supplierModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->findAll();

        $summaryList = $localPOInvSummaryModel->asObject()
            ->select("id, summary_no, total, DATE_FORMAT(due_date, '%d/%m/%Y') AS due_date")
            ->where('company_id', $this->this_company_id)
            ->where('supplier_id', $paymentData->supplier_id)
            ->where('is_posted', 1)
            ->findAll();
        $selectQry = "penerimaan_barang.no_penerimaan_barang AS no_lpb,
                      DATE_FORMAT(penerimaan_barang.createdAt, '%d/%m/%Y') AS lpb_date,
                      penerimaan_barang_detail.nama_barang_dok AS item_name,
                      penerimaan_barang_detail.qty AS qty,
                      local_po_inv_sum_details.id AS local_po_inv_sum_detail_id,
                      local_po_inv_sum_details.inv_amt AS total,
                      satuans.kode_satuan AS unit";
        $itemList = $localPOInvSummaryModel->asObject()
            ->select($selectQry)
            ->join('local_po_inv_sum_details', 'local_po_inv_sum_details.local_po_inv_summary_id = local_po_inv_summaries.id')
            ->join('penerimaan_barang', 'penerimaan_barang.id = local_po_inv_sum_details.penerimaan_barang_id')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit')
            // ->where('company_id', $this->this_company_id)
            ->where('local_po_inv_summaries.id', $paymentData->local_po_inv_summary_id)
            ->whereIn('local_po_inv_sum_details.id', $paidSumDetId)
            ->findAll();

        $data = [
            'dataPembayaranPOLokal' => $paymentData,
            'suppliers'             => $supplierList,
            'summaryList'           => $summaryList,
            "itemList"              => $itemList
        ];

        return view('Pembayaran/pembayaranPOLokal/form', $data);
    }

    public function savePembayaranPOLokal()
    {
        try {
            $localPOPaymentModel = new LocalPOPaymentModel();
            $localPOPaymentDetModel = new LocalPOPaymentDetailModel();
            $localPOInvSumModel = new LocalPOInvSummaryModel();
            $localPOInvSumDetModel = new LocalPOInvSumDetailModel();

            $postData = $this->request->getPost();
            $postData["local_po_inv_sum_detail_id"] = json_decode($postData["local_po_inv_sum_detail_id"]);

            $rules = [
                "supplier_id" => [
                    "rules" => "required|is_natural_no_zero",
                    "errors" => [
                        "required" => "Kolom Supplier wajib diisi.",
                        "is_natural_no_zero" => "Kolom Supplier harus berisi angka yang lebih besar dari 0."
                    ]
                ],
                "summary_id" => [
                    "rules" => "required|is_natural_no_zero",
                    "errors" => [
                        "required" => "Kolom Nominal Pembayaran wajib diisi.",
                        "is_natural_no_zero" => "Kolom Nominal Pembayaran harus berisi angka yang lebih besar dari 0."
                    ]
                ],
                "due_date" => [
                    "rules" => "required|valid_date[d/m/Y]",
                    "errors" => [
                        "required" => "Kolom Jatuh Tempo wajib diisi.",
                        "valid_date" => "Kolom Jatuh Tempo harus berisi tanggal dengan format dd/mm/yyyy."
                    ]
                ],
                "payment_date" => [
                    "rules" => "required|valid_date[d/m/Y]",
                    "errors" => [
                        "required" => "Kolom Tanggal Pembayaran wajib diisi.",
                        "valid_date" => "Kolom Tanggal Pembayaran harus berisi tanggal dengan format dd/mm/yyyy."
                    ]
                ],
                "payment_method" => [
                    "rules" => "required",
                    "errors" => [
                        "required" => "Kolom Metode Pembayaran wajib diisi."
                    ]
                ],
                "local_po_inv_sum_detail_id.*" => [
                    "rules" => "required|is_natural_no_zero",
                    "errors" => [
                        "required" => "Checklist item yang ingin dibayar.",
                        "is_natural_no_zero" => "Kolom {field} harus berisi angka yang lebih besar dari 0."
                    ]
                ]
            ];

            if (!$this->validateData($postData, $rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $summaryId = $this->request->getPost("summary_id");
            $paymentTotal = 0;
            $paymentDetData = [];

            // check summary data
            $summaryData = $localPOInvSumModel->asObject()
                ->find($summaryId);

            if (empty($summaryData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Rekap tidak ditemukan!',
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            // check selected items
            foreach ($postData["local_po_inv_sum_detail_id"] as $sumDetId) {

                $sumDetData = $localPOInvSumDetModel->asObject()
                    ->find($sumDetId);
                if (empty($sumDetData)) {
                    $data = [
                        "status"    => false,
                        "message"   => 'Item tidak ditemukan!',
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }

                $paymentTotal += $sumDetData->inv_amt;
                $paymentDetData[] = [
                    'local_po_inv_sum_detail_id'    => $sumDetId,
                    'total'                         => $sumDetData->inv_amt,
                ];
            }

            $sumDetIds = array_column($paymentDetData, 'local_po_inv_sum_detail_id');
            $localPOPaymentModel->db->transException(true)->transStart();
            $data = [
                "supplier_id"               => (int)$this->request->getPost("supplier_id"),
                "local_po_inv_summary_id"   => $summaryId,
                "payment_no"                => $this->generatePaymentNo(),
                "amount"                    => $paymentTotal,
                "payment_date"              => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("payment_date")))),
                "due_date"                  => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("due_date")))),
                "payment_method"            => $this->request->getPost("payment_method")
            ];

            $insertedId = $localPOPaymentModel->insert($data);

            foreach ($paymentDetData as &$detData) {
                $detData['local_po_payment_id'] = $insertedId;
            }

            $localPOPaymentDetModel->insertBatch($paymentDetData);

            $localPOInvSumDetModel->whereIn('id', $sumDetIds)
                ->set('is_paid', 1)
                ->update();

            $localPOPaymentModel->db->transComplete();

            $data = [
                "id"        => $insertedId,
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $data,
                'token'     => csrf_hash(),
                'code'      => 201
            ];
            echo json_encode($data);
            return;
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

    public function updatePembayaranPOLokal()
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
                    "payment_type" => "LOKAL"
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

    public function deletePembayaranPOLokal()
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

    public function print($id)
    {
        $dompdf = new Dompdf();

        $localPOPaymentModel = new LocalPOPaymentModel();
        $localPOPaymentDetModel = new LocalPOPaymentDetailModel();
        $supplierModel = new SupplierModel();
        $localPOInvSummaryModel = new LocalPOInvSummaryModel();

        $selectQry = "local_po_payments.*,
                      DATE_FORMAT(local_po_payments.payment_date, '%d/%m/%Y') AS payment_date,
                      DATE_FORMAT(local_po_payments.due_date, '%d/%m/%Y') AS due_date
                      ";
        $paymentData = $localPOPaymentModel->asObject()
            ->select($selectQry)
            ->find($id);

        if ($paymentData == null) {
            return redirect()->to('pembayaran-po-lokal');
        }

        $paymentDetData = $localPOPaymentDetModel->where('local_po_payment_id', $id)
            ->findAll();
        $paidSumDetId = array_column($paymentDetData, 'local_po_inv_sum_detail_id');

        $supplier = $supplierModel->asObject()
            // ->where('company_id', $this->this_company_id)
            ->where('id', $paymentData->supplier_id)
            ->first();

        $summary = $localPOInvSummaryModel->asObject()
            ->select("id, summary_no, total, DATE_FORMAT(due_date, '%d/%m/%Y') AS due_date")
            ->where('id', 0)
            ->where('is_posted', 1)
            ->first();

        $selectQry = "penerimaan_barang.no_penerimaan_barang AS no_lpb,
                      DATE_FORMAT(local_po_inv_summaries.createdAt, '%d/%m/%Y') AS lpb_date,
                      penerimaan_barang_detail.nama_barang_dok AS item_name,
                      penerimaan_barang_detail.qty AS qty,
                      local_po_inv_sum_details.id AS local_po_inv_sum_detail_id,
                      local_po_inv_sum_details.inv_amt AS total,
                      satuans.kode_satuan AS unit";

        $itemList = $localPOInvSummaryModel->asObject()
            ->select($selectQry)
            ->join('local_po_inv_sum_details', 'local_po_inv_sum_details.local_po_inv_summary_id = local_po_inv_summaries.id')
            ->join('penerimaan_barang', 'penerimaan_barang.id = local_po_inv_sum_details.penerimaan_barang_id')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit')
            ->where('local_po_inv_summaries.id', 0)
            //->whereIn('local_po_inv_sum_details.id', $paidSumDetId)
            ->findAll();

        $data = [
            'dataPembayaranPOLokal' => $paymentData,
            'supplier'             => $supplier,
            'summary'           => $summary,
            "itemList"              => $itemList
        ];

        $dompdf->loadHtml(view('Pembayaran/pembayaranPOLokal/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Pembayaran PO Lokal ", array("Attachment" => false));

        exit(0);
    }

    public function generatePaymentNo()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();

        $month = idate('m');
        $year = date('Y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "PAY/$romanMonth/$year/";

        $lastData = $localPOPaymentModel->asObject()
            ->like('payment_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $paymentNo = "{$numberTemplate}0001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->payment_no);
            $lastIncrement = (int)$exploded[3] + 1;

            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $paymentNo = $numberTemplate . $paddedNumber;
        }

        return response()->setJSON([
            'paymentNo' => $paymentNo,
            'token' => csrf_hash(),
            'success' => true
        ]);
    }
}
