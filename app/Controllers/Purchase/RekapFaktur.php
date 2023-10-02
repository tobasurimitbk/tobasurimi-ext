<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

use App\Models\SupplierModel;
use App\Models\LocalPOInvSummaryModel;
use App\Models\LocalPOInvSumDetailModel;
use App\Models\PenerimaanBarangModel;

class RekapFaktur extends BaseController
{
    protected $token;
    protected $this_company_id;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        return view('Purchase/rekapFaktur/index');
    }

    public function createRekapFaktur()
    {
        $supplierModel = new SupplierModel();
        $supplierData = $supplierModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('kategori', 'LOKAL')
            ->findAll();

        $data = [
            "supplierList" => $supplierData
        ];

        return view('Purchase/rekapFaktur/form', $data);
    }

    public function saveRekapFaktur()
    {
        try {
            $localPOInvSum = new LocalPOInvSummaryModel();
            $localPOInvSumDet = new LocalPOInvSumDetailModel();
            $penerimaanBarangModel = new PenerimaanBarangModel();

            $rules = [
                "supplier_id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "invoices.*" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "due_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "is_posted" => [
                    "rules" => "permit_empty|numeric|in_list[0,1]"
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

            $totalSummary = 0;
            $detData = [];
            $invoiceIds = $this->request->getPost('invoices');

            // validate here
            foreach ($invoiceIds as $invoice) {
                $penerimaanData = $penerimaanBarangModel->asObject()
                    ->select('penerimaan_barang.*, SUM(penerimaan_barang_detail.harga * penerimaan_barang_detail.qty) AS itemTotal')
                    ->where('status_post', 'FINISH')
                    ->where('status_penerimaan', 'LOKAL')
                    ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
                    ->find($invoice);

                if (empty($penerimaanData)) {
                    $data = [
                        "status"    => false,
                        "message"   => 'Invoice not Found!',
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }

                $totalReceiveAmt = $penerimaanData->shipping_cost + $penerimaanData->biaya_masuk + $penerimaanData->ppnbm + $penerimaanData->itemTotal;
                $totalSummary += $totalReceiveAmt;
                $detData[] = [
                    'penerimaan_barang_id'  => $invoice,
                    'inv_amt'               => $totalReceiveAmt
                ];
            }

            $insertdata = [
                'company_id'    => $this->this_company_id,
                'summary_no'    => $this->generateSummaryNo(),
                'supplier_id'   => $this->request->getPost("supplier_id"),
                'due_date'      => date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("due_date")))),
                'total'         => $totalSummary,
                'is_posted'     => $this->request->getPost('is_posted') ?? 0
            ];

            $localPOInvSum->db->transException(true)->transStart();
            $insertedId = $localPOInvSum->insert($insertdata);

            foreach ($detData as &$detail) {
                $detail['local_po_inv_summary_id'] = $insertedId;
            }

            $localPOInvSumDet->insertBatch($detData);

            $penerimaanBarangModel->set('is_summarized', 1);
            $penerimaanBarangModel->whereIn('id', $invoiceIds)
                ->update();
            $localPOInvSum->db->transComplete();

            $data = [
                "id"        => $insertedId,
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                // "payload"   => $payload,
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

    public function getRekapFakturList()
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

        $localPOInvSumModel = new LocalPOInvSummaryModel();
        $condition = [
            "local_po_inv_summaries.company_id"  => $this->this_company_id,
            // "faktur_type"           => "LOKAL"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $summaryData = $localPOInvSumModel->getSummaryList($condition, $addCondition, $limit, $offset);

        $dataSummary = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($summaryData['data'] as $data) {
            array_push($dataSummary, [
                "no"            => $no++,
                "id"            => $data->id,
                "summary_no"    => $data->summary_no,
                "supplier_name" => $data->supplierName,
                "due_date"      => $data->due_date,
                "total"         => $data->total,
                "summary_status" => $data->summary_status,
                // "is_posted"     => (bool)$data->is_posted
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $summaryData['totalData'],
            "recordsFiltered"   => $summaryData['totalFilteredData'],
            "data"              => $dataSummary,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function getRekapFakturBySupplier($supplierId)
    {

        $localPOInvSumModel = new LocalPOInvSummaryModel();

        $fakturList = $localPOInvSumModel->asObject()
            ->select("local_po_inv_summaries.id AS id, summary_no, total, DATE_FORMAT(due_date, '%d/%m/%Y') AS due_date")
            ->join('local_po_inv_sum_details', 'local_po_inv_sum_details.local_po_inv_summary_id = local_po_inv_summaries.id')
            ->where('company_id', $this->this_company_id)
            ->where('supplier_id', $supplierId)
            ->where('local_po_inv_sum_details.is_paid', 0)
            ->groupBy('local_po_inv_summaries.id')
            ->findAll();

        $data = [
            'data' => $fakturList
        ];

        echo json_encode($data);
        return;
    }

    public function getRekapFakturById($id)
    {
        $localPOInvSum = new LocalPOInvSummaryModel();
        $supplierModel = new SupplierModel();
        $penerimaanBarangModel = new PenerimaanBarangModel();

        $selectQry = "local_po_inv_summaries.*, 
                      GROUP_CONCAT(penerimaan_barang_id) as penerimaan_barang_id,
                      DATE_FORMAT(due_date, '%d/%m/%Y') AS due_date";
        $summaryData = $localPOInvSum->asObject()
            ->select($selectQry)
            ->join('local_po_inv_sum_details', 'local_po_inv_sum_details.local_po_inv_summary_id = local_po_inv_summaries.id')
            ->where('company_id', $this->this_company_id)
            ->groupBy('local_po_inv_summaries.id') // if this line is commented, $summaryData will return object instead of null (because of GROUP_CONCAT)
            ->find($id);

        if (empty($summaryData)) {
            return view('errors/html/error_404', ['message' => 'Not Found!']);
        }

        $summaryData->is_posted = (bool)$summaryData->is_posted;
        $selectedFaktur = explode(',', $summaryData->penerimaan_barang_id);

        // get supplier list
        $supplierList = $supplierModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('kategori', 'LOKAL')
            ->findAll();

        // get faktur list
        $fakturList = $penerimaanBarangModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->where('supplier_id', $summaryData->supplier_id)
            ->findAll();

        $data = [
            "rekapData"     => $summaryData,
            "supplierList"  => $supplierList,
            "fakturList"    => $fakturList,
            "selectedFaktur" => $selectedFaktur
        ];

        return view('Purchase/rekapFaktur/form', $data);
    }

    public function getInvItemsBySummaryId($summaryId)
    {

        $localPOInvSumModel = new LocalPOInvSummaryModel();

        /* $selectQry = "local_po_inv_sum_details.id AS local_po_inv_sum_detail_id,
                      penerimaan_barang.no_penerimaan_barang AS no_lpb,
                      DATE_FORMAT(validation_date, '%d/%m/%Y') AS lpb_date,
                      penerimaan_barang_detail.nama_barang_dok AS item_name,
                      penerimaan_barang_detail.qty AS qty,
                      (penerimaan_barang_detail.qty * penerimaan_barang_detail.harga) AS total,
                      satuans.kode_satuan AS unit"; */

        $selectQry = "local_po_inv_sum_details.id AS local_po_inv_sum_detail_id,
                      penerimaan_barang.no_penerimaan_barang AS no_lpb,
                      DATE_FORMAT(validation_date, '%d/%m/%Y') AS lpb_date,
                      penerimaan_barang_detail.nama_barang_dok AS item_name,
                      penerimaan_barang_detail.qty AS qty,
                      local_po_inv_sum_details.inv_amt AS total,
                      satuans.kode_satuan AS unit";
        $fakturList = $localPOInvSumModel->asObject()
            ->select($selectQry)
            ->join('local_po_inv_sum_details', 'local_po_inv_sum_details.local_po_inv_summary_id = local_po_inv_summaries.id')
            ->join('penerimaan_barang', 'penerimaan_barang.id = local_po_inv_sum_details.penerimaan_barang_id')
            ->join('penerimaan_barang_detail', 'penerimaan_barang_detail.penerimaan_barang_id = penerimaan_barang.id')
            ->join('satuans', 'satuans.id = penerimaan_barang_detail.unit')
            // ->where('company_id', $this->this_company_id)
            ->where('local_po_inv_summaries.id', $summaryId)
            ->where('local_po_inv_sum_details.is_paid', 0)
            ->findAll();

        $data = [
            'data' => $fakturList
        ];

        echo json_encode($data);
        return;
    }

    public function updateRekap()
    {
        try {
            $localPOInvSum = new LocalPOInvSummaryModel();
            $localPOInvSumDet = new LocalPOInvSumDetailModel();
            $penerimaanBarangModel = new PenerimaanBarangModel();

            $rules = [
                "id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "supplier_id" => [
                    "rules" => "is_natural_no_zero"
                ],
                "invoices.*" => [
                    "rules" => "is_natural_no_zero"
                ],
                "due_date" => [
                    "rules" => "valid_date[d/m/Y]"
                ],
                "is_posted" => [
                    "rules" => "permit_empty|numeric|in_list[0,1]"
                ]
            ];

            if (!$this->validate($rules)) {
                $data = [
                    "status"            => false,
                    "message"    => "Data Gagal Diubah",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $detData = [];
            $totalSummary = 0;

            $id = $this->request->getPost("id");
            $supplierId = $this->request->getPost("supplier_id");
            $invoices = $this->request->getPost("invoices");
            $due_date = $this->request->getPost("due_date");
            $isPosted = $this->request->getPost("is_posted");

            $arrData = [
                "supplier_id"   => (int)$supplierId ?: null,
                // "invoices"      => $invoices ?: null,
                "due_date"      => $due_date ? date("Y-m-d", strtotime(str_replace("/", "-", $due_date))) : null,
                "is_posted"     => $isPosted ?? null
            ];
            $filteredData = array_filter($arrData, fn ($value) => !empty($value));
            $detailData = $this->request->getPost('invoices') ?? [];

            foreach ($detailData as $invoice) {
                $penerimaanData = $penerimaanBarangModel->asObject()
                    ->where('status_post', 'FINISH')
                    ->where('status_penerimaan', 'LOKAL')
                    ->find($invoice);

                if (empty($penerimaanData)) {
                    $data = [
                        "status"    => false,
                        "message"   => 'Invoice not Found!',
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }

                $totalSummary += $penerimaanData->shipping_cost;
                $detData[] = [
                    'local_po_inv_summary_id'   => $id,
                    'penerimaan_barang_id'      => $invoice,
                    'inv_amt'                   => $penerimaanData->shipping_cost
                ];
            }

            if (empty($filteredData) && $detailData) {
                $data = [
                    "status"    => true,
                    "message"   => "Tidak ada Data yang diubah",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $localPOInvSum->db->transStart();
            $localPOInvSum->update($id, $filteredData);

            $localPOInvSumDet->where('local_po_inv_summary_id', $id)->delete();
            $localPOInvSumDet->insertBatch($detData);
            $localPOInvSum->db->transComplete();

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil diubah",
                // "payload"   => $payload,
                'token'     => csrf_hash(),
                'id'        => $id
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

    public function deleteRekap()
    {
        try {
            $localPOInvSum = new LocalPOInvSummaryModel();

            $id = $this->request->getPost("id");

            if (empty($id)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $localPOInvSum->delete($id);

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

    private function generateSummaryNo(): string
    {
        $localPOInvSum = new LocalPOInvSummaryModel();

        $month = idate('m');
        $year = date('Y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "SUM/$romanMonth/$year/";

        $lastData = $localPOInvSum->asObject()
            ->like('summary_no', $numberTemplate, 'after')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $summaryNo = "{$numberTemplate}0001";

        if (!empty($lastData)) {
            $exploded = explode('/', $lastData->summary_no);
            $lastIncrement = (int)$exploded[3] + 1;

            $paddedNumber = str_pad($lastIncrement, 4, 0, STR_PAD_LEFT);
            $summaryNo = $numberTemplate . $paddedNumber;
        }

        return $summaryNo;
    }
}
