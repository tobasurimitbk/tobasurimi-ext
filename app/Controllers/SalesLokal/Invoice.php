<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;

use App\Models\CompaniesModel;
use App\Models\CustomerModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderDetailModel;
use App\Models\SuratJalanModel;
use App\Models\SuratJalanDetailModel;
use App\Models\TaxModel;
use App\Models\AllNoModel;
use App\Models\BarangMasterSalesModel;
use App\Models\EmployeesModel;
use App\Models\PembayaranInvoiceModel;
use App\Models\SalesOrderInvoiceDetailModel;
use App\Models\SalesOrderPaymentDetailModel;
use App\Models\SalesOrderPaymentModel;
use App\Models\StockModel;
use Config\Services;
use Dompdf\Dompdf;
use ErrorException;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Invoice extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $is_admin;
    private $companyModel;
    protected $CustomerModel;
    private $userId;
    protected $encrypter;
    protected $MetadataModel;
    protected $SalesOrderInvoiceModel;
    protected $AllNoModel;
    protected $SalesOrderModel;
    protected $SalesOrderDetailModel;
    protected $SuratJalanModel;
    protected $SuratJalanDetailModel;
    protected $SalesOrderInvoiceDetailModel;
    protected $taxModel;
    protected $pembayaranInvoiceModel;
    protected $BarangMasterSalesModel;
    protected $stockModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userId = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;

        $this->encrypter = Services::encrypter();

        $this->companyModel = new CompaniesModel();
        $this->CustomerModel = new CustomerModel();
        $this->MetadataModel = new MetadataModel();
        $this->SalesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $this->AllNoModel   = new AllNoModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
        $this->SuratJalanModel = new SuratJalanModel();
        $this->SuratJalanDetailModel = new SuratJalanDetailModel();
        $this->SalesOrderInvoiceDetailModel = new SalesOrderInvoiceDetailModel();
        $this->taxModel = new TaxModel();
        $this->pembayaranInvoiceModel = new PembayaranInvoiceModel();
        $this->BarangMasterSalesModel = new BarangMasterSalesModel();
        $this->stockModel = new StockModel();
        $this->employeeModel = new EmployeesModel();
    }

    public function index()
    {
        $data = [
            'getCustomers' => $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin),
        ];
        return view('SalesLokal/Invoice/index', $data);
    }

    public function createView()
    {
        //Get Customers
        // $customers = $this->CustomerModel->asObject()->select(['id', 'name'])->where('company_id', $this->this_company_id)->findAll();
        $customers = $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin);
        $tipeShipping = $this->MetadataModel->asObject()->select(['id', 'value'])->where('name', 'tipe_shipping_via')->findAll();
        // $noFaktur = $this->getNomorFaktur();
        $taxData = $this->taxModel->getTaxByType('ppn');
        $condition = [
            'jabatan_name' => "MARKETING LOKAL"
        ];

        $sales = $this->employeeModel->getEmployeesComplete($condition);
        $data = [
            "sales" => $sales,
            "dataCustomers" => $customers,
            "id_user" => session()->get('login')->user_id,
            "seller_name" => session()->get('login')->name,
            "via" => $tipeShipping,
            "termin"        => "",
            "taxData"       => $taxData
        ];
        // echo json_encode($data);
        return view('SalesLokal/Invoice/form', $data);
    }

    public function all()
    {
        $pageSize = (int)$this->request->getGet('length');
        $start    = (int)$this->request->getGet('start');
        $currentPage = $pageSize ? (int)($start / $pageSize) + 1 : 1;
        $offset   = $start;   // gunakan ini!

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        if ($this->is_admin == '1') {
            $condition = [
                // "sales_order_invoice.id_company"    => $this->this_company_id,
                "sales_order_invoice.deletedAt" => null,
                "sales_order_invoice.tipe_invoice" => 'LOKAL',
            ];
        } else {
            $condition = [
                // "sales_order_invoice.id_company"    => $this->this_company_id,
                "sales_order_invoice.deletedAt" => null,
                "sales_order_invoice.tipe_invoice" => 'LOKAL',
                "sales_order_invoice.id_user" => $this->userId
            ];
        }

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "filter_jenis_dokumen"        => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer"        => $this->request->getGet("filter_customer"),
            "filter_paid"        => $this->request->getGet("filter_paid"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderInvoice = $this->SalesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokal($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataAllSalesOrderInvoice = [];

        foreach ($dataSalesOrderInvoice['data'] as $data) {

            // Karakter yang akan dihapus
            $unwanted_characters = array('[', '"', ']');

            // Gantikan karakter tidak diinginkan dengan string kosong
            $cleaned_string_document_no = str_replace($unwanted_characters, ' ', $data->doc_no);

            $pembayaranInvoice = $this->pembayaranInvoiceModel->where('invoice_id', $data->id)->where('status_posting', "1")->findAll();
            $statusPembayaranInvoice = "";

            if ($data->status_pelunasan == "UNPAID") {
                $statusPembayaranInvoice = "BELUM LUNAS";
            } elseif ($data->status_pelunasan == "PAID") {
                $statusPembayaranInvoice = "LUNAS";
            }

            if ($data->id) {
                $dataSumAmount = $this->SalesOrderInvoiceDetailModel->getSumAmount($data->id);
            }

            array_push($dataAllSalesOrderInvoice, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "no_faktur"         => $data->no_faktur,
                "tanggal_faktur"    => $data->tanggal_faktur,
                "document_type"     => strtoupper($data->doc_type),
                "document_no"       => $cleaned_string_document_no,
                "total_invoice"     => number_format(floatval($data->total_invoice)),
                "kode_pelanggan"    => $data->kode_pelanggan,
                "keterangan"        => $data->keterangan,
                "nama_pelanggan"    => $data->nama_pelanggan,
                "nama_sales"        => $data->salesName,
                "tipe_invoice"      => $data->tipe_invoice,
                "status"            => ($data->status_posting == 0) ? 'WAITING' : 'POSTING',
                "counter_print"     => $data->counter_print,
                "status_pembayaran"     => $statusPembayaranInvoice,
            ]);
        }

        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataSalesOrderInvoice['totalData'],
            "recordsFiltered" => $dataSalesOrderInvoice['totalFilteredData'],
            "data" => $dataAllSalesOrderInvoice,
            "payload" => $payload
        ];
        //dd($dataAllSalesOrderInvoice);

        echo json_encode($data);
        return;
    }

    public function save()
    {
        $payload =  $this->request->getVar();
        //echo json_encode($payload);
        //return;
        $rules = [
            "no_faktur" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Nomor Faktur tidak boleh kosong',
                ]
            ],
            "tanggal_faktur" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'Tanggal Faktur tidak boleh kosong',
                ]
            ],
            // "doc_type" => [
            //     "rules" => "required|in_list[pesanan,pengiriman,penjualan]",
            //     'errors' => [
            //         'required' => 'Jenis dokumen tidak boleh kosong',
            //     ]
            // ],
            "terms" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'Term tidak boleh kosong',
                ]
            ],
            "keterangan" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errorList = $this->validator->getErrors();
            $data = [
                "status"    => false,
                "message"   => $errorList[array_keys($errorList)[0]],
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        }

        try {
            $postData = $this->request->getPost();
            $postItemsData = json_decode($this->request->getPost('items'), true);
            $documentData = null;

            $checkINV = $this->SalesOrderInvoiceModel->where('UPPER(no_faktur)', strtoupper($postData['no_faktur']))->findAll();
            // var_dump($postItemsData);
            // exit;
            if ($checkINV) {
                $data = [
                    "status"    => false,
                    "message"   => "No Faktur Sudah Digunakan",
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }

            // start transaction
            $this->SalesOrderInvoiceModel->db->transException(true)->transStart();
            $noFaktur = $postData['no_faktur'];

            $values = [
                "id_user"           => $this->userId,
                "document_type"     => $postData['doc_type'],
                "document_id"       => isset($postData['doc_id']) ? str_replace(['\\"', '\\', '"'], '', json_encode($postData['doc_id'])) : "",
                "id_customer"       => $postData['id_customer'],
                "document_no"       => $postData['noDocument'],
                // "id_surat_jalan"    => $postData['id_surat_jalan'],
                "jenis_penjualan"    => $postData['jenis_penjualan'],
                "no_faktur"         => $noFaktur,
                "tanggal_faktur"    => date('Y-m-d', strtotime(str_replace('/', '-', $postData['tanggal_faktur']))),
                "terms"             => $postData['termin'] ?? '',
                "ship_via_id"       => $postData['ship_via'],
                "keterangan"        => $postData['keterangan'],
                "dpp"               => str_replace('.', '', $postData['dpp']),
                "ppn"               => str_replace('.', '', $postData['ppn']),
                "total_invoice"     => str_replace('.', '', $postData['total_invoice']),
                "termasuk_pa"       => $this->request->getPost('include_tax') ? 'true' : 'false',
                "status_tax"        => $this->request->getPost('tax_status') ? 'true' : 'false',
                "tipe_invoice"      => 'LOKAL',
                "status_pelunasan"  => 'UNPAID',
                "id_company"        => ($this->this_company_id != 16)
                    ? $this->request->getPost('company_ids')
                    : $this->this_company_id,
                "tax_id"            => $this->request->getPost('taxes'),
                "tax_value"         => ($tax = $this->taxModel->find($this->request->getPost('taxes'))) ? $tax['tax_value'] : null,
            ];

            $dataSalesOrderInvoice =  $this->SalesOrderInvoiceModel->insert($values);

            foreach ($postItemsData as $value) {
                if ($value['qty_input'] != 0) {
                    $valuesDetail = [
                        "id_sales_order_invoice"        => $dataSalesOrderInvoice,
                        "id_sales_order"                => $value['id_sales_order'] ?? null,
                        "id_surat_jalan"                => $value['id_sj'] ?? null,
                        "id_barang_invoice"             => $value['id_barang'],
                        "qty_invoice"                   => $value['qty_input'],
                        "keterangan_invoice"            => "-",
                        "discount_percentage_invoice"   => $value['disc'],
                        "discount_unit_invoice"         => $value['discUnit'],
                        "harga_barang_invoice"          => str_replace(',', '', $value['harga_barang']),
                        "tax_invoice"                   => str_replace(',', '', ($value['tax'] ?? ($value['taxAmt'] ?? 0))),
                        "amount_invoice"                => str_replace(',', '', $value['amount']),
                    ];
                    $this->SalesOrderInvoiceDetailModel->insert($valuesDetail);
                }
            }

            $this->SalesOrderInvoiceModel->db->transComplete();

            if (isset($postData['doc_id'])) {
                foreach ($postData['doc_id'] as $id) {
                    if ($postData['doc_type'] === 'pesanan') {
                        $this->SalesOrderModel->where('id', $id)->set(['sales_order_invoice_id' => $dataSalesOrderInvoice])->update();
                    } else {

                        $this->SuratJalanModel->where('id', $id)->set(['sales_order_invoice_id' => $dataSalesOrderInvoice])->update();
                    }
                }
            }

            $data = [
                "id"        => encrypt($dataSalesOrderInvoice),
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                "payload"   => $payload,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    private function dataSuratJalanDetail($id_surat_jalan)
    {
        $data = $this->SuratJalanModel
            ->asObject()
            ->select(['id', 'no_surat_jalan', 'multiple_id_so'])
            ->find($id_surat_jalan);

        $dataIdSo = json_decode($data->multiple_id_so);

        $detailSo = array();
        $detailBarang = array();

        foreach ($dataIdSo as $id) {
            $dataSo = $this->SalesOrderModel->getSalesOrderLokalById($id);
            $detailSo[] = ["id" => $dataSo->id, "no_so" => $dataSo->no_sales_order];
            foreach ($dataSo->detail as $detail) {
                $detail['no_so'] = $dataSo->no_sales_order;
                $detail['no_surat_jalan'] = $data->no_surat_jalan;
                $detailBarang[] = $detail;
            }
        }
        $allData = [
            "detail_barang" => $detailBarang,
            "detail_so" => $detailSo
        ];
        return $allData;
    }
    private function dropDownSuratJalan($id_customer)
    {
        $data = $this->SuratJalanModel
            ->asObject()
            ->where(['id_customer' => $id_customer])
            ->select(['id', 'no_surat_jalan'])
            ->findAll();



        return $data;
    }

    public function getById($id = null)
    {
        $invoice_id = $id;
        $id = decrypt($id);
        //Get data sales order
        $dataSalesInvoiceOrder = $this->SalesOrderInvoiceModel->getSalesOrderInvoiceLokalById(($id));
        // dd($dataSalesInvoiceOrder);
        $dataSalesInvoiceOrderDetail = $this->SalesOrderInvoiceDetailModel
            ->select('sales_order_invoice_detail.*, 
                  sales_order_invoice_detail.id as id_detail_invoice, 
                  sales_order_invoice_detail.id_barang_invoice as id_barang,
                  barang_master_sales.barang_name as nama_barang,
                  barang_master_sales.kode_barang,
                  sales_order_invoice_detail.qty_invoice as qty,
                  sales_order_invoice_detail.qty_invoice as qty_sekarang,
                  satuans.kode_satuan as satuan,
                  sales_order_invoice_detail.keterangan_invoice as keterangan,
                  sales_order_invoice_detail.discount_percentage_invoice as disc,
                  sales_order_invoice_detail.discount_percentage_invoice as discAmt,
                  sales_order_invoice_detail.discount_unit_invoice as discUnit,
                  sales_order_invoice_detail.harga_barang_invoice as harga_barang,
                  sales_order_invoice_detail.tax_invoice as tax,
                  sales_order_invoice_detail.amount_invoice as amount,')
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice', 'left')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'left')
            ->where('id_sales_order_invoice', $id)
            ->findAll();

        $taxData = $this->taxModel->getTaxByType('ppn');
        if (empty($dataSalesInvoiceOrder)) {
            return view('errors/html/error_404', ['message' => 'Not Found']);
        }

        $currentDocumentIds = json_decode($dataSalesInvoiceOrder->document_id, true) ?? [];
        $documentList = $this->getDocNumberList(
            $dataSalesInvoiceOrder->document_type,
            $dataSalesInvoiceOrder->id_customer,
            'edit',
            $currentDocumentIds
        );

        $tipeShipping = $this->MetadataModel->asObject()
            ->select(['id', 'value'])
            ->where('name', 'tipe_shipping_via')
            ->findAll();

        if ($dataSalesInvoiceOrder->document_type != "penjualan") {
            $documentIds = json_decode($dataSalesInvoiceOrder->document_id, true) ?? [];
            $documentResults = [];

            foreach ($documentIds as $docId) {
                $documentGetData = $this->getDocDataaaa($dataSalesInvoiceOrder->document_type, (int) $docId);
                $documentResults[] = $documentGetData;
            }
            $documentData = $documentResults;
        } else {
            $documentData = $this->getDocDataaaa($dataSalesInvoiceOrder->document_type, $id);
        }

        // var_dump($documentData);
        // exit;

        foreach ($documentData as $doc) {
            foreach ($doc->itemList as $key => &$value) {
                foreach ($dataSalesInvoiceOrderDetail as &$valueDetail) {
                    // var_dump($value);
                    // var_dump($valueDetail);

                    $valueDetail['qty_input'] = number_format(floatval($valueDetail['qty']), 2);
                    $valueDetail['harga_barang'] = number_format(floatval($valueDetail['harga_barang']), 0);
                    $valueDetail['amount'] = number_format(floatval($valueDetail['amount']), 0);

                    // if (isset($valueDetail['id_sales_order']) ? ($value->id_sales_order == $valueDetail['id_sales_order'] && $value->id_barang == $valueDetail['id_barang_invoice']) : ($value->id_sj == $valueDetail['id_surat_jalan'] && $value->id_barang == $valueDetail['id_barang_invoice'])) {

                    //     // Check if deletedAt is not empty
                    //     if (!empty($valueDetail['deletedAt'])) {
                    //         unset($documentData->itemList[$key]);
                    //         break; // Break out of the inner loop since the item has been removed
                    //     }

                    //     $value->id_detail_invoice = $valueDetail['id'];
                    //     $value->qty_input = number_format(floatval($valueDetail['qty_invoice']), 2);
                    //     $value->harga_barang = number_format(floatval($valueDetail['harga_barang_invoice']), 0);
                    //     $value->amount = number_format(floatval($valueDetail['amount_invoice']), 0);
                    //     // } else {
                    //     //     unset($documentData->itemList[$key]);
                    //     //     break; // Break out of the inner loop since the item has been removed
                    // }
                }
            }
        }

        // exit();
        //untuk yang sudah di posting

        foreach ($documentData as $doc) {
            foreach ($doc->itemListPosting as $key => &$value) {
                // var_dump($value);
                foreach ($dataSalesInvoiceOrderDetail as $valueDetail) {

                    // if (isset($valueDetail['id_sales_order']) ? ($value->id_sales_order == $valueDetail['id_sales_order'] && $value->id_barang == $valueDetail['id_barang_invoice']) : ($value->id_sj == $valueDetail['id_surat_jalan'] && $value->id_barang == $valueDetail['id_barang_invoice'])) {

                    //     // Check if deletedAt is not empty
                    //     if (!empty($valueDetail['deletedAt'])) {
                    //         unset($documentData->itemListPosting[$key]);
                    //         break; // Break out of the inner loop since the item has been removed
                    //     }

                    //     $value->id_detail_invoice = $valueDetail['id'];
                    //     $value->qty_input = number_format(floatval($valueDetail['qty_invoice']), 2);
                    //     $value->harga_barang = number_format(floatval($valueDetail['harga_barang_invoice']), 0);
                    //     $value->amount = number_format(floatval($valueDetail['amount_invoice']), 0);
                    //     // } else {
                    //     //     unset($documentData->itemListPosting[$key]);
                    //     //     break; // Break out of the inner loop since the item has been removed
                    // }
                }
            }
        }


        $noFaktur = $this->SalesOrderInvoiceModel->generateNoFaktur();

        // $customers = $this->CustomerModel->asObject()->select(['id', 'name'])->where('company_id', $this->this_company_id)->findAll();
        $customers = $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin);

        foreach ($dataSalesInvoiceOrderDetail as &$valueDetail) {
            $valueDetail['id_detail_invoice'] = $valueDetail['id'];
            $valueDetail['qty_input'] = number_format(floatval($valueDetail['qty_invoice']), 2);
            $valueDetail['harga_barang'] = floatval($valueDetail['harga_barang_invoice']);
            $valueDetail['amount'] = floatval($valueDetail['amount_invoice']);
        }

        // echo "<pre>";
        // // var_dump($documentData);
        // var_dump($dataSalesInvoiceOrderDetail);
        // echo "</pre>";
        // exit;

        $data = [
            "noFaktur"      => $noFaktur,
            "data"          => $dataSalesInvoiceOrder,
            "dataDetail"    => $dataSalesInvoiceOrderDetail,
            "dataCustomers" => $customers,
            "documentList"  => $documentList,
            "documentData"  => $documentData,
            "termin"        => $this->MetadataModel->where('name', 'termin')->findAll(),
            "id_user"       => $dataSalesInvoiceOrder->id_user,
            "seller_name"   => $dataSalesInvoiceOrder->seller_name,
            "via"           => $tipeShipping,
            'invoice_id' => $invoice_id,
            "taxData"       => $taxData
            // 'dataSo'        => $dataSo

        ];
        return view('SalesLokal/Invoice/form', $data);
    }

    public function update()
    {
        $payload =  $this->request->getVar();

        $validate = $this->validate([
            "no_faktur" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Nomor Faktur tidak boleh kosong',
                ]
            ],
            "tanggal_faktur" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'Tanggal Faktur tidak boleh kosong',
                ]
            ],
            "terms" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'Term tidak boleh kosong',
                ]
            ]
        ]);
        try {
            if (!$validate) {
                $errorList = $this->validator->getErrors();
                // $error = validation_errors();
                //echo json_encode($error);
                throw new ErrorException($errorList[array_keys($errorList)[0]]);
                //return;
                // return redirect()->to('/invoice-penjualan-lokal/create')->back()->withInput();
            }

            $postData = $this->request->getPost();
            $postItemsData = json_decode($this->request->getPost('items'), true);

            $soInvData = $this->SalesOrderInvoiceModel->asObject()
                ->find(decrypt($payload['id']));

            if (empty($soInvData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Dokumen tidak ditemukan',
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }

            if (empty($postItemsData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Item tidak boleh kosong',
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }

            $checkINV = $this->SalesOrderInvoiceModel->where('UPPER(no_faktur)', strtoupper($postData['no_faktur']))->first();

            if ($checkINV) {
                if ($checkINV['id'] != $soInvData->id) {
                    $data = [
                        "status"    => false,
                        "message"   => "No Faktur Sudah Digunakan",
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                    return;
                }
            }
            $noFaktur = $postData['no_faktur'];

            $totalInvoice = 0;
            foreach ($postItemsData as $value) {
                if ($value['qty_input'] != 0) {
                    $amount = str_replace(',', '', $value['amount']);
                    $totalInvoice += (int) $amount;
                }
            }

            $values = [
                "id_user"           => $this->userId,
                // "document_type"     => $postData['doc_type'],
                "document_id"       => isset($postData['doc_id']) ? str_replace(['\\"', '\\', '"'], '', json_encode($postData['doc_id'])) : "",
                "id_customer"       => $postData['id_customer'],
                "document_no" => json_encode(
                    array_map(
                        fn($d) => trim(preg_replace('/\s+/', '', $d)),
                        json_decode($postData['noDocument'], true) ?? []
                    ),
                    JSON_UNESCAPED_SLASHES
                ),
                "no_faktur"         => $noFaktur,
                "tanggal_faktur"    => date('Y-m-d', strtotime(str_replace('/', '-', $postData['tanggal_faktur']))),
                "terms"             => $postData['termin'] ?? '',
                "ship_via_id"       => $postData['ship_via'],
                "keterangan"        => $postData['keterangan'],
                "jenis_penjualan"   => $postData['jenis_penjualan'],
                "dpp"               => str_replace('.', '', $postData['dpp']),
                "ppn"               => str_replace('.', '', $postData['ppn']),
                "total_invoice"     => $totalInvoice,
                "termasuk_pa"       => $this->request->getPost('include_tax') ? 'true' : 'false',
                "status_tax"        => $this->request->getPost('tax_status') ? 'true' : 'false',
                "id_company"        => ($this->this_company_id != 16)
                    ? $this->request->getPost('company_ids')
                    : $this->this_company_id,

            ];

            $dataSalesOrderInvoice =  $this->SalesOrderInvoiceModel->update(decrypt($payload['id']), $values);

            $this->SalesOrderInvoiceModel->db->transComplete();

            //jika document tidak berubah
            if ($soInvData->document_no == $postData['noDocument']) {
                // Periksa apakah $postItemsData tidak kosong sebelum melakukan iterasi
                foreach ($postItemsData as $value) {
                    if (isset($value['id_detail_invoice'])) {
                        if ($value['qty_input'] != 0) {
                            $valuesDetail = [
                                "id_barang_invoice"             => $value['id_barang'],
                                "qty_invoice"                   => $value['qty_input'],
                                "id_sales_order"                => $value['id_sales_order'] ?? null,
                                "id_surat_jalan"                => $value['id_sj'] ?? null,
                                "keterangan_invoice"            => "-",
                                "discount_percentage_invoice"   => $value['disc'],
                                "harga_barang_invoice"          => str_replace(',', '', $value['harga_barang']),
                                "tax_invoice"                   => str_replace(',', '', $value['tax']),
                                "amount_invoice"                => str_replace(',', '', $value['amount']),
                            ];
                            $this->SalesOrderInvoiceDetailModel->update($value['id_detail_invoice'], $valuesDetail);
                        } else {
                            $this->SalesOrderInvoiceDetailModel->where('id', $value['id_detail_invoice'])->delete();
                        }
                    }
                }

                foreach ($postData['doc_id'] as $id) {
                    if ($soInvData->document_type === 'pesanan') {
                        $this->SalesOrderModel->where('id', $id)->set(['sales_order_invoice_id' => decrypt($payload['id'])])->update();
                    } else {
                        $this->SuratJalanModel->where('id', $id)->set(['sales_order_invoice_id' => decrypt($payload['id'])])->update();
                    }
                }
            } else {
                // jika ada perubahan
                $this->SalesOrderInvoiceDetailModel->where('id_sales_order_invoice', decrypt($payload['id']))->delete();
                //setelah hapus kembalikan kondisi sales_order_invoice_id pada sales order detail semula mejadi null
                foreach (json_decode($soInvData->document_id) as $id) {
                    if ($soInvData->document_type === 'pesanan') {
                        $this->SalesOrderModel->where('id', $id)->set(['sales_order_invoice_id' => NULL])->update();
                    } else {
                        $this->SuratJalanModel->where('id', $id)->set(['sales_order_invoice_id' => NULL])->update();
                    }
                }
                foreach ($postItemsData as $value) {
                    if ($value['qty_input'] != 0) {
                        $valuesDetail = [
                            "id_sales_order_invoice"        => decrypt($payload['id']),
                            "id_barang_invoice"             => $value['id_barang'],
                            "qty_invoice"                   => $value['qty_input'],
                            "id_sales_order"                => $value['id_sales_order'] ?? null,
                            "id_surat_jalan"                => $value['id_sj'] ?? null,
                            "keterangan_invoice"            => "-",
                            "discount_percentage_invoice"   => $value['disc'],
                            "harga_barang_invoice"          => str_replace(',', '', $value['harga_barang']),
                            "tax_invoice"                   => str_replace(',', '', $value['tax']),
                            "amount_invoice"                => str_replace(',', '', $value['amount']),
                        ];
                        $this->SalesOrderInvoiceDetailModel->insert($valuesDetail);
                    }
                }

                foreach ($postData['doc_id'] as $id) {
                    if ($soInvData->document_type === 'pesanan') {
                        $this->SalesOrderModel->where('id', $id)->set(['sales_order_invoice_id' => decrypt($payload['id'])])->update();
                    } else {
                        $this->SuratJalanModel->where('id', $id)->set(['sales_order_invoice_id' => decrypt($payload['id'])])->update();
                    }
                }
            }

            $data = [
                "id"        => decrypt($payload['id']),
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $dataSalesOrderInvoice,
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage(),
                "payload"   => $payload,
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function delete()
    {
        try {

            $id = decrypt($this->request->getPost("id"));

            if (!empty($id)) {
                $soInvData = $this->SalesOrderInvoiceModel->asObject()
                    ->find($id);


                //setelah hapus kembalikan kondisi sales_order_invoice_id pada sales order detail semula mejadi null
                foreach (json_decode($soInvData->document_id) as $id_doc) {


                    if ($soInvData->document_type === 'pesanan') {

                        $this->SalesOrderModel->where('id', $id_doc)->set(['sales_order_invoice_id' => NULL])->update();
                    } else {

                        $this->SuratJalanModel->where('id', $id_doc)->set(['sales_order_invoice_id' => NULL])->update();
                    }
                }

                $this->SalesOrderInvoiceDetailModel->where('id_sales_order_invoice', $id)->delete();

                $this->SalesOrderInvoiceModel->delete($id);
                $data = [
                    "status"     => true,
                    "message"    => "Data Success Dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
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

    public function deleteDetail()
    {
        $id = ($this->request->getVar('id'));

        $getBarangSalesOrderDetail = $this->SalesOrderInvoiceDetailModel
            ->select('id_sales_order_invoice, id_barang_invoice')
            ->where('id', $id)
            ->first();

        if ($getBarangSalesOrderDetail['id_barang_invoice'] == "85") {
            return response()->setJSON([
                'message' => "Kemasan Default Tidak Boleh Dihapus",
                'token' => csrf_hash(),
                'status' => false
            ]);
        }

        $this->SalesOrderInvoiceDetailModel->delete($id);

        $getAllBarangSalesOrderDetail = $this->SalesOrderInvoiceDetailModel
            ->select('qty_invoice, harga_barang_invoice, discount_percentage_invoice, amount_invoice, discount_unit_invoice')
            ->where('id_sales_order_invoice', $getBarangSalesOrderDetail['id_sales_order_invoice'])
            ->where('id_barang_invoice !=', 85)->findAll();

        $totalQty = 0;
        $total_harga = 0;
        foreach ($getAllBarangSalesOrderDetail as $row) {
            $totalQty = $totalQty + $row['qty_invoice'];
            $amountValue = $row['amount_invoice'] ? (float) str_replace(",", "", $row['amount_invoice']) : 0;
            $total_harga +=  $row['discount_unit_invoice'] == "percent" ? ($amountValue - ($amountValue * ($row['discount_percentage_invoice'] / 100))) : ($amountValue - $row['discount_percentage_invoice']);
        }
        //qty barang ditambah jumlah kemasan
        $this->SalesOrderInvoiceModel
            ->update(
                $getBarangSalesOrderDetail['id_sales_order_invoice'],
                [
                    'total_invoice' => $total_harga
                ]
            );

        return response()->setJSON([
            'message' => "Detail Barang Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getDocNumber($documentType, $id_customer)
    {
        $documentList = $this->getDocNumberList($documentType, $id_customer);

        echo json_encode(['data' => $documentList]);
    }

    public function getDocNumberEdit($documentType, $id_customer)
    {
        $documentList = $this->getDocNumberList($documentType, $id_customer, 'edit');

        echo json_encode(['data' => $documentList]);
    }

    public function getDocData($docType, $docId)
    {

        $docId = [$docId];

        $data = $this->getDocDataaaa($docType, $docId);
        echo json_encode($data);
    }

    public function getItemList($id)
    {
        $invData = $this->SalesOrderInvoiceModel->asObject()
            ->find($id);

        $documentData = $this->getDocDataaaa($invData->document_type, $invData->document_id);
        echo json_encode($documentData);
    }

    public function printInvoice($id)
    {

        $id = decrypt($id);
        $domPdf = new Dompdf();

        $fileName = 'Invoice';
        $soIds = [];

        $companyData = $this->companyModel->asObject()
            ->find($this->this_company_id);

        $invSelectQry = "sales_order_invoice.*,
        DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d/%m/%Y') AS tanggal_faktur,
        users.name AS seller_name,
        customers.name AS customer_name,
        customers.phone AS customer_phone,
        customers.address AS customer_address,
        sales_order_invoice.status_tax AS status_tax,
        sales_order_invoice.termasuk_pa AS termasuk_pa,
        sales_order.jenis_penjualan,
        sales_order.no_po, 
        sales_order.nama_ecommerce,
        metadata.value as terms";
        $invData = $this->SalesOrderInvoiceModel->asObject()
            ->select($invSelectQry)
            ->join('users', 'users.id = sales_order_invoice.id_user', 'left')
            ->join('customers', 'customers.id = sales_order_invoice.id_customer', 'left')
            ->join('sales_order', 'sales_order.id = sales_order_invoice.document_id', 'left')
            ->join('employees', 'employees.id = sales_order.sales_id', 'left')
            ->join('metadata', 'metadata.id = sales_order_invoice.terms', 'left')
            ->find($id);

        // if ($invData->document_type == 'pengiriman') {
        //     $sjData = $this->SuratJalanModel->asObject()
        //         ->find($invData->document_id);

        //     $soIds = json_decode($sjData->multiple_id_so);
        // } else {
        //     $soIds = json_decode($invData->document_id);
        // }

        $soSelectQry = "sales_order_invoice_detail.id AS id,
        sales_order_invoice_detail.id_barang_invoice AS id_barang,
        barang_master_sales.kode_barang AS kode_barang,
          barang_master_sales.barang_name AS nama_barang,
          sales_order_invoice_detail.qty_invoice AS qty_invoice,
          satuans.kode_satuan AS satuan,
          sales_order_invoice_detail.discount_percentage_invoice AS disc,
          sales_order_invoice_detail.tax_invoice AS tax,
          sales_order_invoice_detail.amount_invoice AS amount,
          sales_order_invoice_detail.id_sales_order,
          sales_order_invoice_detail.harga_barang_invoice AS harga_barang";
        $salesOrderDetailData = $this->SalesOrderInvoiceDetailModel->asObject()
            ->select($soSelectQry)
            ->join('barang_master_sales', 'barang_master_sales.id = sales_order_invoice_detail.id_barang_invoice AND barang_master_sales.deletedAt IS NULL')
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'LEFT')
            // ->where('qty_sekarang !=', 0)
            ->where('id_sales_order_invoice', $id)
            ->orderBy('barang_master_sales.barang_name', 'ASC')
            ->findAll();

        // $invTotal = $salesOrderDetailData[0]->total_harga + $salesOrderDetailData[0]->estimated_freight;

        // $invData->docNo = ($invData->document_type == 'pengiriman') ? $sjData->no_surat_jalan : $salesOrderData[0]->no_sales_order;

        $data = [
            'companyName'   => $companyData->company,
            'companyAccount' => $companyData->invoice_account,
            'invData'       => $invData,
            'soData'        => $salesOrderDetailData,
            // 'invTotal'      => $invTotal
        ];

        $this->SalesOrderInvoiceModel->update($id, ['counter_print' => $invData->counter_print + 1]);

        // return view('SalesLokal/Invoice/print', $data);

        // load HTML content
        $domPdf->loadHtml(view('SalesLokal/Invoice/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper([0, 0, 792.96, 528]);

        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
    }

    private function getDocNumberList(string $documentType, $customer_id, $condition = null, array $currentDocumentIds = []): array
    {
        $usedDocs = $this->SalesOrderInvoiceModel
            ->select('document_id')
            ->where('id_customer', $customer_id)
            ->findAll();

        $usedIds = [];

        foreach ($usedDocs as $u) {
            if (!empty($u['document_id'])) {
                $ids = is_array($u['document_id'])
                    ? $u['document_id']
                    : json_decode($u['document_id'], true);

                if (is_array($ids)) {
                    $usedIds = array_merge($usedIds, $ids);
                }
            }
        }

        $usedIds = array_unique($usedIds);

        // jika edit, keluarkan document_id milik invoice saat ini dari daftar yang di-exclude
        if ($condition === 'edit' && !empty($currentDocumentIds)) {
            $usedIds = array_values(array_diff($usedIds, $currentDocumentIds));
        }

        // ---------- ambil dokumen ----------
        if ($documentType === 'pesanan') {
            $documentList = $this->SalesOrderModel->asObject()
                ->select('
                sales_order.id,
                sales_order.no_sales_order AS doc_no,
                sales_order.id_company,
                sales_order.no_po,
                sales_order.keterangan,
                sales_order.payment_terms AS termin,
                sales_order.jenis_penjualan AS jenis_penjualan,
                sales_order.sales_id,
                employees.name AS salesName
            ')
                ->join('sales_order_detail', 'sales_order_detail.id_sales_order = sales_order.id')
                ->join('employees', 'employees.id = sales_order.sales_id', 'left')
                ->where('sales_order.id_customer', $customer_id)
                ->where('sales_order.surat_jalan_so_id', null)
                ->where('sales_order_detail.qty_sekarang !=', 0)
                ->groupBy('sales_order.no_sales_order')
                ->findAll();

            // ---------- filter agar id yg sudah dipakai invoice tidak muncul ----------
            if (!empty($usedIds)) {
                $documentList = array_values(array_filter(
                    $documentList,
                    fn($d) => !in_array($d->id, $usedIds)
                ));
            }
        } else { // pengiriman
            $documentList1 = $this->SuratJalanModel->asObject()
                ->select('
                    surat_jalan_so.id,
                    surat_jalan_so.no_surat_jalan AS doc_no,
                    surat_jalan_so.id_company,
                    surat_jalan_so.no_po,
                    surat_jalan_so.note AS keterangan,
                    COALESCE(surat_jalan_so.terms, customers.termin) AS termin,
                    COALESCE(sales_order.jenis_penjualan, customers.jenis_penjualan) AS jenis_penjualan,
                    sales_order.sales_id,
                    employees.name AS salesName
                ')
                ->join('sales_order', 'sales_order.surat_jalan_so_id = surat_jalan_so.id', 'left')
                ->join('sales_order_detail', 'sales_order_detail.id_sales_order = sales_order.id')
                ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                ->join('employees', 'employees.id = sales_order.sales_id', 'left')
                ->where('sales_order.id_customer', $customer_id)
                ->where('sales_order_detail.qty_sekarang !=', 0)
                ->groupBy('surat_jalan_so.no_surat_jalan')
                ->findAll();

            $documentList2 = $this->SuratJalanModel->asObject()
                ->select('
                    surat_jalan_so.id,
                    surat_jalan_so.no_surat_jalan AS doc_no,
                    surat_jalan_so.id_company,
                    surat_jalan_so.no_po,
                    surat_jalan_so.note AS keterangan,
                    COALESCE(surat_jalan_so.terms, customers.termin) AS termin,
                    COALESCE(sales_order.jenis_penjualan, customers.jenis_penjualan) AS jenis_penjualan,
                    sales_order.sales_id,
                    employees.name AS salesName
                ')
                ->join('surat_jalan_so_detail', 'surat_jalan_so_detail.id_surat_jalan = surat_jalan_so.id', 'left')
                ->join('sales_order', 'sales_order.surat_jalan_so_id = surat_jalan_so_detail.id_sales_order', 'left')
                ->join('customers', 'customers.id = surat_jalan_so.id_customer', 'left')
                ->join('employees', 'employees.id = sales_order.sales_id', 'left')
                ->where('surat_jalan_so.id_customer', $customer_id)
                ->where('surat_jalan_so_detail.qty !=', 0)
                ->groupBy('surat_jalan_so.no_surat_jalan')
                ->findAll();

            $merged = [];

            foreach (array_merge($documentList1, $documentList2) as $row) {
                if (!isset($merged[$row->id])) {
                    // Jika belum ada, langsung simpan
                    $merged[$row->id] = $row;
                } else {
                    // Jika sudah ada dari list1, jangan timpa field yang sudah terisi
                    foreach ($row as $key => $value) {
                        if (empty($merged[$row->id]->{$key}) && !empty($value)) {
                            $merged[$row->id]->{$key} = $value;
                        }
                    }
                }
            }

            $documentList = array_values($merged);

            // ---------- filter agar id yg sudah dipakai invoice tidak muncul ----------
            if (!empty($usedIds)) {
                $documentList = array_values(array_filter(
                    $documentList,
                    fn($d) => !in_array($d->id, $usedIds)
                ));
            }
        }

        return $documentList;
    }

    private function  getDocDataaaa(string $docType, $docId): object
    {
        $soId = 0;
        $salesName = '';
        $customerName = '';
        $customerAddress = '';
        $salesName = '';
        $termin = '';
        $taxStatus = false;
        $includeTax = false;
        $itemList = [];
        $itemTax = [];

        $soId = $docId;

        if ($docType == 'pesanan') {
            $soData = $this->SalesOrderModel->asObject()
                ->select("sales_order.*,
                sales_order.no_po, 
                sales_order.nama_ecommerce, 
                customers.name AS customerName, 
                customers.address AS customerAddress, 
                CONCAT(employees.nip , ' - ', employees.name) AS salesName, 
                metadata.id AS termin,
                customers.termin as termin_id")
                ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                ->join('employees', 'employees.id = sales_order.sales_id', 'left')
                ->join('metadata', 'metadata.id = customers.termin', 'left')
                ->where('sales_order.id', $docId)
                // ->where('sales_order.posting', 1)
                ->first();

            $salesName = $soData->salesName ?? "-";
            $termin = $soData->termin ?? "-";
            $jenis_penjualan = $soData->jenis_penjualan ?? "-";
            $no_po = $soData->no_po ?? "-";
            $nama_ecommerce = $soData->nama_ecommerce ?? "-";
            $customerName = $soData->customerName ?? "-";
            $customerAddress = $soData->customerAddress ?? "-";
        } else if ($docType == 'pengiriman') {
            $selectQry = "surat_jalan_so.*, 
                          sales_order.jenis_penjualan,
                          sales_order.no_po,
                          sales_order.nama_ecommerce,
                          customers.name AS customerName, 
                          customers.address AS customerAddress,  
                          CONCAT(employees.nip , ' - ', employees.name) AS salesName, 
                          metadata.id AS termin,
                          customers.termin as termin_id";
            $suratJalanData = $this->SuratJalanModel->asObject()
                ->select($selectQry)
                ->join('customers', 'customers.id = surat_jalan_so.id_customer', 'left')
                ->join('sales_order', 'sales_order.surat_jalan_so_id = surat_jalan_so.id', 'left')
                ->join('employees', 'employees.id = sales_order.sales_id', 'left')
                ->join('metadata', 'metadata.id = customers.termin', 'left')
                ->where('surat_jalan_so.id', $docId)
                // ->where('surat_jalan_so.posting', 1)
                ->first();

            $salesName = $suratJalanData->salesName ?? "-";
            $termin = $suratJalanData->termin ?? "-";
            $jenis_penjualan = $suratJalanData->jenis_penjualan ?? "-";
            $no_po = $suratJalanData->no_po ?? "-";
            $nama_ecommerce = $suratJalanData->nama_ecommerce ?? "-";
            $soId = $suratJalanData == NULL ? [] : json_decode($suratJalanData->multiple_id_so);
            $customerName = $suratJalanData->customerName ?? "-";
            $customerAddress = $suratJalanData->customerAddress ?? "-";
        } else {
            $selectQry = "  sales_order_invoice.*,
                            sales_order_invoice.no_faktur, 
                            sales_order_invoice.nama_ecommerce, 
                            customers.name AS customerName, 
                            customers.address AS customerAddress, 
                            metadata.id AS termin,
                            customers.termin as termin_id";
            $soData = $this->SalesOrderInvoiceModel->asObject()
                ->select($selectQry)
                ->join('customers', 'customers.id = sales_order_invoice.id_customer', 'left')
                ->join('metadata', 'metadata.id = customers.termin', 'left')
                ->where('sales_order_invoice.id', $docId)
                ->first();

            $salesName =  "-";
            $termin = $soData->termin ?? "-";
            $jenis_penjualan = "-";
            $no_po = $soData->no_po ?? "-";
            $nama_ecommerce = $soData->nama_ecommerce ?? "-";
            $customerName = $soData->customerName ?? "-";
            $customerAddress = $soData->customerAddress ?? "-";
        }


        if ($docType == 'pengiriman') {
            if (!is_array($docId)) $docId = [$docId];
            $checkSuratJalanDetail = $this->SuratJalanDetailModel->whereIn('id_surat_jalan', $docId)->first();

            if ($checkSuratJalanDetail) {
                $itemList = $this->SuratJalanDetailModel->getItemListByIds($docId);
                $itemListPosting = $this->SuratJalanDetailModel->getItemListPostingByIds($docId);
            } else {
                $itemList = $this->SalesOrderDetailModel->getItemListByIds($soId);
                $itemListPosting = $this->SalesOrderDetailModel->getItemListPostingByIds($soId);
            }
        } else {
            $itemList = $this->SalesOrderDetailModel->getItemListByIds($soId);
            $itemListPosting = $this->SalesOrderDetailModel->getItemListPostingByIds($soId);
        }

        $itemTax = $this->taxModel->where('id', '4')->asObject()->findAll();

        $dpp = 0;
        $taxAmt = 0;
        $taxChecked = 0;
        $no = 1;
        foreach ($itemList as &$item) {
            foreach ($itemTax as $itemT) {
                $item->taxChecked = str_replace(',', '', $itemT->tax_value);
            }
            $hargaBarang = str_replace(',', '', $item->harga_barang);
            $qty = str_replace(',', '', $item->qty);
            $discUnit = $item->discUnit;
            $getDiscount = str_replace(',', '', $item->disc);
            $discount = $discUnit == "percent" ? floatval($getDiscount) / 100 : floatval($getDiscount);

            if ($discUnit == "percent") {
                if ($getDiscount == 0) {
                    $itemTotal = $hargaBarang * $qty;
                } else {
                    $itemTotal = ($hargaBarang - ($hargaBarang * $discount)) * $qty;
                }
            } else {
                if ($getDiscount == 0) {
                    $itemTotal = $hargaBarang * $qty;
                } else {
                    $itemTotal = ($hargaBarang * $qty) - $discount;
                }
            }

            $dpp += $itemTotal;
            $taxAmt += $itemTotal * ($item->tax / 100);
            $item->no = $no++;
            $item->amount = number_format($itemTotal);
            $item->harga_barang = number_format($item->harga_barang);
        }

        $data = (object)[
            'salesName'         => $salesName,
            'termin'            => $termin,
            'jenis_penjualan'   => $jenis_penjualan,
            'no_po'             => $no_po,
            'nama_ecommerce'   => $nama_ecommerce,
            'customerName'      => $customerName,
            'customerAddress'   => $customerAddress,
            'taxStatus'         => $taxStatus,
            'includeTax'        => $includeTax,
            'itemList'          => $itemList,
            'itemListPosting'   => $itemListPosting,
            'dpp'               => number_format($dpp),
            'tax'               => number_format($taxAmt),
            'taxChecked'               => number_format($taxChecked),
            'total'             => number_format($dpp + $taxAmt)
        ];
        return $data;
    }

    public function posting()
    {
        $postData = $this->request->getPost();

        $this->SalesOrderInvoiceModel->update(decrypt($postData['id']), ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Invoice Berhasil Diposting",
            'token' => csrf_hash(),
        ]);
    }

    public function unposting()
    {
        $postData = $this->request->getPost();

        $this->SalesOrderInvoiceModel->update(decrypt($postData['id']), ['status_posting' => '0']);

        return response()->setJSON([
            'status' => true,
            'message' => "Invoice Berhasil Diunposting",
            'token' => csrf_hash(),
        ]);
    }

    // public function getNomorFaktur()
    // {
    //     $code = "LKL/INV";
    //     $currentYear = date('y'); // 2 digit
    //     $currentMonth = date('n'); // 1-12 tanpa nol depan
    //     $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    //     $numberTemplate = $code . "/" . $currentYear . "/" . $romawi[$currentMonth] . "/";

    //     $listData = $this->SalesOrderInvoiceModel->asObject()
    //         ->like('no_faktur', $numberTemplate)
    //         ->orderBy('no_faktur', 'ASC')
    //         ->findAll();

    //     $existingNumbers = [];

    //     foreach ($listData as $data) {
    //         $parts = explode('/', $data->no_faktur);
    //         if (isset($parts[4]) && is_numeric($parts[4])) {
    //             $existingNumbers[] = intval($parts[4]);
    //         }
    //     }

    //     sort($existingNumbers);

    //     $nextNumber = 1;
    //     foreach ($existingNumbers as $num) {
    //         if ($num != $nextNumber) {
    //             break; // ketemu celah
    //         }
    //         $nextNumber++;
    //     }

    //     $paddedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    //     $invNumber = $numberTemplate . $paddedNumber;

    //     return response()->setJSON([
    //         'data' => $invNumber,
    //         'token' => csrf_hash(),
    //         'status' => true
    //     ]);
    // }

    public function getNomorFaktur()
    {
        $code   = "LKL/INV";
        $year   = date('y'); // 2 digit
        $month  = date('n'); // 1–12 tanpa nol depan
        $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $template = "{$code}/{$year}/{$romawi[$month]}/";

        // Ambil record terakhir berdasarkan createdAt DESC
        $last = $this->SalesOrderInvoiceModel
            ->select('no_faktur')
            ->orderBy('createdAt', 'DESC')
            ->first();

        if ($last && !empty($last['no_faktur'])) {
            // Pisahkan nomor terakhir
            $parts = explode('/', $last['no_faktur']);
            // Ambil index terakhir sebagai nomor (pastikan numerik)
            $lastNumber = is_numeric(end($parts)) ? (int)end($parts) : 0;

            $nextNumber = $lastNumber + 1;
            // Format tetap 3 digit (001, 002, ...)
            $newNumber  = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Pakai prefix dari faktur terakhir (bukan template baru)
            $prefix = implode('/', array_slice($parts, 0, -1));
            $invNumber = "{$prefix}/{$newNumber}";
        } else {
            // Tidak ada data, gunakan template default
            $invNumber = $template . '001';
        }

        return $this->response->setJSON([
            'data'   => $invNumber,
            'token'  => csrf_hash(),
            'status' => true
        ]);
    }

    public function getAllBarang()
    {
        $dataBarang = $this->BarangMasterSalesModel
            ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'left')
            ->join('sales_order_return_detail', 'sales_order_return_detail.id_barang_return = barang_master_sales.id', 'left')
            ->join('sales_order_return', 'sales_order_return.id = sales_order_return_detail.id_sales_order_return', 'left')
            ->select('barang_master_sales.*')
            ->select('barang_master_sales.id as id_barang')
            ->select('barang_master_sales.kode_barang as kode_barang')
            ->select('barang_master_sales.barang_name as nama_barang')
            ->select('barang_master_sales.harga_pokok as harga_pokok')
            ->select('barang_master_sales.harga_jual as harga_jual')
            ->select('barang_master_sales.status_ppn as statusppn')
            ->select('satuans.kode_satuan as kode_satuan')
            ->select('satuans.nama_satuan as nama_satuan')
            ->select('sales_order_return_detail.stock_id as stock_id')
            ->select('sales_order_return_detail.bc_id as bc_id')
            ->select('sales_order_return_detail.no_aju as no_aju')
            ->select('sales_order_return_detail.stock_dokumen as stock_dokumen')
            ->where('type_barang_sales', 'LOKAL')
            ->where('barang_master_sales.deletedAt', null)
            // ->where('barang_master_sales.company_id', $this->this_company_id)
            ->where('sales_order_return.is_approved', 1)
            ->groupBy('id_barang')
            ->findAll();

        // Map to get stock quantities and filter
        $dataBarang = array_filter(array_map(function ($value) {
            $currentStock = 0;

            if ($value['stock_id'] || $value['stock_id'] != "0") {
                $stockDetail = $this->stockModel->detailStock($value['stock_id']);
                $currentStock = $stockDetail['barang']['qty'];
            }

            if ($currentStock > 0) {
                $value['qty_stock'] = $currentStock;
                return $value;
            }
            return null; // Filter out items with stock <= 0
        }, $dataBarang));

        $data = [
            "dataBarang" => array_values($dataBarang), // Reindex the array
        ];

        echo json_encode($data);
        return;
    }

    public function exportExcel()
    {
        $condition = [
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL',
        ];

        if ($this->is_admin == '0') {
            $condition["sales_order_invoice.id_user"] = $this->userId;
        }

        $addCondition = [
            "search"                => $this->request->getGet("search"),
            "sort"                  => $this->request->getGet("sort"),
            "sortType"              => $this->request->getGet("sortType"),
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer"       => $this->request->getGet("filter_customer"),
            "filter_paid"       => $this->request->getGet("filter_paid"),
            "dateStart"             => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"               => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderInvoice = $this->SalesOrderInvoiceModel->getAllSalesOrderInvoiceLokal($condition, $addCondition, null, null);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No Faktur');
        $sheet->setCellValue('C1', 'Tanggal Faktur');
        $sheet->setCellValue('D1', 'Jenis Dokumen');
        $sheet->setCellValue('E1', 'No Dokumen');
        $sheet->setCellValue('F1', 'Kode Pelanggan');
        $sheet->setCellValue('G1', 'Nama Pelanggan');
        $sheet->setCellValue('H1', 'Nama Sales');
        $sheet->setCellValue('I1', 'Tipe Invoice');
        $sheet->setCellValue('J1', 'Total Invoice');
        $sheet->setCellValue('K1', 'Keterangan');
        $sheet->setCellValue('L1', 'Status Posting');
        $sheet->setCellValue('M1', 'Counter Print');
        $sheet->setCellValue('N1', 'Status Pembayaran');

        $row = 2;
        $no = 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            // Bersihkan string No Dokumen
            $unwanted_characters = ['[', '"', ']'];
            $cleaned_doc_no = str_replace($unwanted_characters, ' ', $data->doc_no);

            // Status pembayaran
            $statusPembayaran = ($data->status_pelunasan == "UNPAID") ? "BELUM LUNAS" : "LUNAS";

            // Format angka: hanya ribuan (tanpa "Rp")
            $total_invoice = floatval($data->total_invoice);

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $data->no_faktur);
            $sheet->setCellValue("C{$row}", $data->tanggal_faktur);
            $sheet->setCellValue("D{$row}", strtoupper($data->doc_type));
            $sheet->setCellValue("E{$row}", $cleaned_doc_no);
            $sheet->setCellValue("F{$row}", $data->kode_pelanggan);
            $sheet->setCellValue("G{$row}", $data->nama_pelanggan);
            $sheet->setCellValue("H{$row}", $data->salesName);
            $sheet->setCellValue("I{$row}", $data->tipe_invoice);
            $sheet->setCellValue("J{$row}", $total_invoice);
            $sheet->setCellValue("K{$row}", $data->keterangan);
            $sheet->setCellValue("L{$row}", $data->status_posting == 0 ? 'WAITING' : 'POSTING');
            $sheet->setCellValue("M{$row}", $data->counter_print);
            $sheet->setCellValue("N{$row}", $statusPembayaran);

            // Format angka ribuan
            $sheet->getStyle("J{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $row++;
        }

        $filename = 'Export-Sales-Order-Invoice-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function importStatic()
    {
        ini_set('max_execution_time', 300);
        // $filePath = '/Users/' . getenv('USER') . '/Downloads/INVOICE CLEAN FINAL.xlsx';
        $filePath = '';
        if (!file_exists($filePath)) {
            return $this->response->setJSON(['error' => 'File tidak ditemukan di folder Downloads.']);
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet()->toArray();

        // 🔹 ambil semua customer & metadata
        $customers = $this->CustomerModel
            ->select('id, name')
            ->where('deletedAt', null)
            ->findAll();

        $customerMap = [];
        foreach ($customers as $c) {
            $normalized = strtolower(preg_replace('/\s+/', '', $c['name']));
            $customerMap[$normalized] = $c['id'];
        }

        $inserted = 0;
        $skipped = 0;
        $failed = [];

        foreach ($sheet as $i => $row) {
            if ($i < 2) continue; // skip header
            $rowNumber = $i + 1;

            if (empty($row[0]) || empty($row[1]) || empty($row[3])) {
                $failed[] = [
                    'row' => $rowNumber,
                    'reason' => 'Data wajib (tanggal faktur / no faktur / nama customer) kosong',
                    'raw' => $row
                ];
                continue;
            }

            try {
                $tanggalFaktur = date('Y-m-d', strtotime($row[0]));
                $noFaktur = trim($row[1]);

                // 🔹 handle tanggal jatuh tempo (bisa kosong)
                $tanggalJthTempo = null;
                if (!empty($row[2])) {
                    $time = strtotime($row[2]);
                    if ($time) {
                        $tanggalJthTempo = date('Y-m-d', $time);
                    } else {
                        $failed[] = [
                            'row' => $rowNumber,
                            'no_faktur' => $noFaktur,
                            'reason' => 'Format tanggal jatuh tempo tidak valid',
                            'raw' => $row[2]
                        ];
                        continue;
                    }
                }

                // 🔹 normalize nama customer
                $customerExcel = trim($row[3]);
                $normalized = strtolower(preg_replace('/\s+/', '', $customerExcel));

                // 🔹 cari ID customer
                $customerId = $customerMap[$normalized] ?? null;
                if (!$customerId) {
                    $failed[] = [
                        'row' => $rowNumber,
                        'no_faktur' => $noFaktur,
                        'customer_excel' => $customerExcel,
                        'reason' => 'Customer tidak ditemukan di tabel customers'
                    ];
                    continue;
                }

                // 🔹 bersihkan total invoice
                $rawTotal = preg_replace('/[^\d.-]/', '', $row[4]); // hapus semua selain angka & titik
                $totalInvoice = is_numeric($rawTotal) ? (float)$rawTotal : 0;

                if ($totalInvoice <= 0) {
                    $failed[] = [
                        'row' => $rowNumber,
                        'no_faktur' => $noFaktur,
                        'reason' => 'Total invoice tidak valid (0 atau bukan angka)',
                        'raw' => $row[4]
                    ];
                    continue;
                }

                // 🔹 cek duplikat faktur
                $exists = $this->SalesOrderInvoiceModel
                    ->where('no_faktur', $noFaktur)
                    ->where('deletedAt', null)
                    ->first();

                if ($exists) {
                    $skipped++;
                    $failed[] = [
                        'row' => $rowNumber,
                        'no_faktur' => $noFaktur,
                        'reason' => 'No faktur sudah ada di database (duplikat)',
                    ];
                    continue;
                }

                // 🔹 kalkulasi termin
                $termId = 1446; // default COD
                if ($tanggalJthTempo) {
                    $daysDiff = (strtotime($tanggalJthTempo) - strtotime($tanggalFaktur)) / 86400;
                    $term = $this->MetadataModel
                        ->where('name', 'Termin')
                        ->where('value', $daysDiff)
                        ->where('deletedAt', null)
                        ->first();

                    if ($term) {
                        $termId = $term['id'];
                    } else {
                        // fallback ke COD (1446)
                        $failed[] = [
                            'row' => $rowNumber,
                            'no_faktur' => $noFaktur,
                            'reason' => "Tidak ditemukan metadata termin untuk selisih $daysDiff hari → fallback ke COD (1446)"
                        ];
                    }
                } else {
                    $failed[] = [
                        'row' => $rowNumber,
                        'no_faktur' => $noFaktur,
                        'reason' => "Tanggal jatuh tempo kosong → fallback ke COD (1446)"
                    ];
                }

                // 🔹 insert data
                $this->SalesOrderInvoiceModel->insert([
                    'tanggal_faktur' => $tanggalFaktur,
                    'no_faktur' => $noFaktur,
                    'tanggal_jatuh_tempo' => $tanggalJthTempo,
                    'id_customer' => $customerId,
                    'total_invoice' => $totalInvoice,
                    'document_type' => 'import',
                    'status_pelunasan' => 'UNPAID',
                    'status_posting' => '1',
                    'terms' => $termId,
                    'id_company' => 2,
                    'createdAt' => date('Y-m-d H:i:s'),
                ]);

                $inserted++;
            } catch (\Throwable $th) {
                $failed[] = [
                    'row' => $rowNumber,
                    'no_faktur' => $row[1] ?? '(unknown)',
                    'reason' => 'Exception: ' . $th->getMessage()
                ];
            }
        }

        return $this->response->setJSON([
            'status' => 'done',
            'inserted' => $inserted,
            'skipped' => $skipped,
            'failed_count' => count($failed),
            'failed_details' => $failed,
        ]);
    }
}
