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
use App\Models\TaxModel;
use App\Models\AllNoModel;

use Config\Services;
use Dompdf\Dompdf;
use ErrorException;
use Exception;

class Invoice extends BaseController
{
    protected $token;
    protected $this_company_id;
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

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userId = session()->get("login")->user_id;

        $this->encrypter = Services::encrypter();

        $this->companyModel = new CompaniesModel();
        $this->CustomerModel = new CustomerModel();
        $this->MetadataModel = new MetadataModel();
        $this->SalesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $this->AllNoModel   = new AllNoModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
        $this->SuratJalanModel = new SuratJalanModel();
        $this->TaxModel = new TaxModel();
    }

    public function index()
    {
        return view('SalesLokal/Invoice/index');
    }

    public function createView()
    {
        //Get Customers
        $customers = $this->CustomerModel->asObject()->select(['id', 'name'])->where('company_id', $this->this_company_id)->findAll();
        $tipeShipping = $this->MetadataModel->asObject()->select(['id', 'value'])->where('name', 'tipe_shipping_via')->findAll();
        $noFaktur = $this->SalesOrderInvoiceModel->generateNoFaktur();
        $data = [
            "noFaktur" => $noFaktur,
            "dataCustomers" => $customers,
            "id_user" => session()->get('login')->user_id,
            "seller_name" => session()->get('login')->name,
            "via" => $tipeShipping
        ];
        //echo json_encode($data);
        return view('SalesLokal/Invoice/form', $data);
    }

    public function all()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $this->request->getGet("length")) + 1;
        $offset = $currentPage - 1;

        $payload = [
            "pageSize"      => $pageSize,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = ['sales_order_invoice.deletedAt' => null, 'sales_order_invoice.tipe_invoice' => 'LOKAL'];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            // "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            //"dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];
        $dataSalesOrderInvoice = $this->SalesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokal($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataAllSalesOrderInvoice = [];

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            array_push($dataAllSalesOrderInvoice, [
                "no"                => $no++,
                "id"                => $data->id,
                "no_faktur"         => $data->no_faktur,
                "tanggal_faktur"    => $data->tanggal_faktur,
                "document_type"     => $data->document_type,
                "document_no"       => $data->document_no,
                "total_invoice"     => number_format(floatval($data->total_invoice)),
                "kode_pelanggan"    => $data->kode_pelanggan,
                "keterangan"        => $data->keterangan,
                "nama_pelanggan"    => $data->nama_pelanggan,
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
            "doc_type" => [
                "rules" => "required|in_list[pesanan,pengiriman]",
                'errors' => [
                    'required' => 'Jenis dokumen tidak boleh kosong',
                ]
            ],
            "doc_id" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Nomor Dokumen tidak boleh kosong',
                ]
            ],
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
            $documentData = null;

            if ($postData['doc_type'] === 'pesanan') {
                $documentData = $this->SalesOrderModel->asObject()
                    ->where('surat_jalan_so_id', null)
                    ->where('sales_order_invoice_id', null)
                    ->find($postData['doc_id']);
            } else {
                $documentData = $this->SuratJalanModel->asObject()
                    ->where('sales_order_invoice_id', null)
                    ->find($postData['doc_id']);
            }

            if (empty($documentData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Dokumen tidak ditemukan',
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }

            // start transaction
            $this->SalesOrderInvoiceModel->db->transException(true)->transStart();

            /* $code = "LKL/INV";
            $currentYear = date('Y');
            $currentMonth = date('m');
            $monthName = date("F", mktime(0, 0, 0, $currentMonth, 10));
            $number = $this->AllNoModel->getNumber($code, $monthName . " " . $currentYear);
            $noFaktur = $code . $number . "/" . $currentYear . "/" . $currentMonth; */
            $noFaktur = $postData['no_faktur'];

            $values = [
                "id_user"           => $this->userId,
                "document_type"     => $postData['doc_type'],
                "document_id"       => $postData['doc_id'],
                "id_customer"       => $documentData->id_customer,
                // "id_surat_jalan"    => $postData['id_surat_jalan'],
                // "no_surat_jalan"    => $postData['no_surat_jalan'],
                "no_faktur"         => $noFaktur,
                "tanggal_faktur"    => date('Y-m-d', strtotime(str_replace('/', '-', $postData['tanggal_faktur']))),
                "terms"             => $postData['terms'] ?? '',
                "ship_via_id"       => $postData['ship_via'],
                "keterangan"        => $postData['keterangan'],
                "dpp"               => str_replace(',', '', $postData['dpp']),
                "ppn"               => str_replace(',', '', $postData['ppn']),
                "total_invoice"     => str_replace(',', '', $postData['total_invoice']),
                "termasuk_pa"       => $this->request->getPost('include_tax') ? 'true' : 'false',
                "status_tax"        => $this->request->getPost('tax_status') ? 'true' : 'false',
                "tipe_invoice"      => 'LOKAL',
                "status_pelunasan"  => 'UNPAID',
            ];

            $dataSalesOrderInvoice =  $this->SalesOrderInvoiceModel->insert($values);

            $updateData = [$documentData->id, ['sales_order_invoice_id' => $dataSalesOrderInvoice]];
            if ($postData['doc_type'] === 'pesanan') {
                $this->SalesOrderModel->update(...$updateData);
            } else {
                /* $soIds = json_decode($documentData->multiple_id_so);
                $this->SalesOrderModel->whereIn('id', $soIds)
                    ->set(['sales_order_invoice_id' => $dataSalesOrderInvoice])
                    ->update(); */
                $this->SuratJalanModel->update(...$updateData);
            }

            $this->SalesOrderInvoiceModel->db->transComplete();

            $data = [
                "id"        => $dataSalesOrderInvoice,
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
        //Get data sales order
        $dataSalesInvoiceOrder = $this->SalesOrderInvoiceModel->getSalesOrderInvoiceLokalById(($id));

        if (empty($dataSalesInvoiceOrder)) {
            return view('errors/html/error_404', ['message' => 'Not Found']);
        }

        $documentList = $this->getDocNumberList($dataSalesInvoiceOrder->document_type, $dataSalesInvoiceOrder->document_id);

        $tipeShipping = $this->MetadataModel->asObject()
            ->select(['id', 'value'])
            ->where('name', 'tipe_shipping_via')
            ->findAll();

        $documentData = $this->getDocDataaaa($dataSalesInvoiceOrder->document_type, $dataSalesInvoiceOrder->document_id);

        $noFaktur = $this->SalesOrderInvoiceModel->generateNoFaktur();

        // dd($documentList);
        $data = [
            "noFaktur"      => $noFaktur,
            "data"          => $dataSalesInvoiceOrder,
            "documentList"  => $documentList,
            "documentData"  => $documentData,
            "id_user"       => $dataSalesInvoiceOrder->id_user,
            "seller_name"   => $dataSalesInvoiceOrder->seller_name,
            "via"           => $tipeShipping,
            // 'dataSuratJalan'=> $dataSuratJalan,
            // 'dataSo'        => $dataSo

        ]; //dd($data);
        return view('SalesLokal/Invoice/form', $data);
    }

    public function update()
    {
        $payload =  $this->request->getVar();
        //echo json_encode($payload);
        //return;

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
            "doc_type" => [
                "rules" => "required|in_list[pesanan,pengiriman]",
                'errors' => [
                    'required' => 'Jenis dokumen tidak boleh kosong',
                ]
            ],
            "doc_id" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Nomor Dokumen tidak boleh kosong',
                ]
            ],
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

            $soInvData = $this->SalesOrderInvoiceModel->asObject()
                ->find($payload['id']);
            if (empty($soInvData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Dokumen tidak ditemukan',
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }

            if ($postData['doc_type'] === 'pesanan') {
                $documentData = $this->SalesOrderModel->asObject()
                    ->where('surat_jalan_so_id', null)
                    ->groupStart()
                    ->where('sales_order_invoice_id', null)
                    ->orWhere('id', $soInvData->document_id)
                    ->groupEnd()
                    ->find($postData['doc_id']);
            } else {
                $documentData = $this->SuratJalanModel->asObject()
                    ->groupStart()
                    ->where('sales_order_invoice_id', null)
                    ->orWhere('id', $soInvData->document_id)
                    ->groupEnd()
                    ->find($postData['doc_id']);
            }

            if (empty($documentData)) {
                $data = [
                    "status"    => false,
                    "message"   => 'Dokumen tidak ditemukan',
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }

            $this->SalesOrderInvoiceModel->db->transException(true)->transStart();
            $values = [
                "document_type"     => $postData['doc_type'],
                "document_id"       => $postData['doc_id'],
                "id_customer"       => $documentData->id_customer,
                "no_faktur"         => $postData['no_faktur'],
                "tanggal_faktur"    => date('Y-m-d', strtotime(str_replace('/', '-', $postData['tanggal_faktur']))),
                "terms"             => $postData['terms'] ?? '',
                "ship_via_id"       => $postData['ship_via'],
                "keterangan"        => $postData['keterangan'],
                "termasuk_pa"       => $this->request->getPost('include_tax') ? 'true' : 'false',
                "status_tax"        => $this->request->getPost('tax_status') ? 'true' : 'false',
            ];
            $dataSalesOrderInvoice =  $this->SalesOrderInvoiceModel->update($payload['id'], $values);

            $updateData = [$documentData->id, ['sales_order_invoice_id' => $payload['id']]];
            if ($postData['doc_type'] === 'pesanan') {
                $this->SalesOrderModel->update(...$updateData);
            } else {
                $this->SuratJalanModel->update(...$updateData);
            }

            $this->SalesOrderInvoiceModel->db->transComplete();

            $data = [
                "id"        => $payload['id'],
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
            $id = $this->request->getPost("id");
            if (!empty($id)) {
                $this->SalesOrderInvoiceModel->delete($id);
                $data = [
                    "status"            => true,
                    "message"    => "Data Success Dihapus",
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
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

    public function getDocNumber($documentType)
    {
        $documentList = $this->getDocNumberList($documentType);

        echo json_encode(['data' => $documentList]);
    }

    public function getDocData($docType, $docId)
    {
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
        $domPdf = new Dompdf();

        $fileName = 'Invoice';
        $soIds = [];

        $companyData = $this->companyModel->asObject()
            ->find($this->this_company_id);

        $invSelectQry = "sales_order_invoice.*,
                         DATE_FORMAT(sales_order_invoice.tanggal_faktur, '%d %b %Y') AS tanggal_faktur, 
                         customers.name AS customerName, 
                         customers.address AS customerAddress";
        $invData = $this->SalesOrderInvoiceModel->asObject()
            ->select($invSelectQry)
            ->join('customers', 'customers.id = sales_order_invoice.id_customer')
            ->find($id);

        if ($invData->document_type == 'pengiriman') {
            $sjData = $this->SuratJalanModel->asObject()
                ->find($invData->document_id);

            $soIds = json_decode($sjData->multiple_id_so);
        } else {
            $soIds = [$invData->document_id];
        }

        $soSelectQry = "sales_order.*,
                        DATE_FORMAT(sales_order.order_date, '%d %b %Y') AS order_date, 
                        DATE_FORMAT(sales_order.shipping_date, '%d %b %Y') AS shipping_date, 
                        customers.name AS customerName, 
                        customers.address AS customerAddress,
                        metadata.value AS termin,
                        barangs.nama_barang AS namaBarang, 
                        barangs.kode_barang AS kodeBarang, 
                        sales_order_detail.qty AS qty, 
                        satuans.kode_satuan AS kodeSatuan,
                        sales_order_detail.discount_percentage AS disc_pct,
                        sales_order_detail.amount AS amt";
        $salesOrderData = $this->SalesOrderModel->asObject()
            ->select($soSelectQry)
            ->join('customers', 'customers.id = sales_order.id_customer', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->join('sales_order_detail', 'sales_order_detail.id_sales_order = sales_order.id', 'left')
            ->join('barangs', 'barangs.id = sales_order_detail.id_barang', 'left')
            ->join('satuans', 'satuans.id = barangs.satuan_id', 'left')
            ->whereIn('sales_order.id', $soIds)
            ->findAll();

        $invTotal = $salesOrderData[0]->total_harga + $salesOrderData[0]->estimated_freight;

        $invData->docNo = ($invData->document_type == 'pengiriman') ? $sjData->no_surat_jalan : $salesOrderData[0]->no_sales_order;

        $data = [
            'companyName'   => $companyData->company,
            'companyAccount' => $companyData->invoice_account,
            'invData'       => $invData,
            'soData'        => $salesOrderData,
            'invTotal'      => $invTotal
        ];

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

    private function getDocNumberList(string $documentType, int $documentId = null): array
    {
        $documentList = [];

        if ($documentType === 'pesanan') {
            $documentList = $this->SalesOrderModel->asObject()
                ->select('id, no_sales_order AS doc_no')
                ->where('surat_jalan_so_id', null);
        } else { // pengiriman
            $documentList = $this->SuratJalanModel->asObject()
                ->select('id, no_surat_jalan AS doc_no');
        }

        $documentList->groupStart();
        $documentList->where('sales_order_invoice_id', null);

        if (!empty($documentId)) {
            $documentList->orWhere('id', $documentId);
        }

        $documentList->groupEnd();

        return $documentList->findAll();
    }

    private function getDocDataaaa(string $docType, int $docId): object
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

        if ($docType == 'pesanan') {
            $soId = $docId;
            $soData = $this->SalesOrderModel->asObject()
                ->select('sales_order.*, customers.name AS customerName, customers.address AS customerAddress, CONCAT(employees.nip , " - ", employees.name) AS salesName, metadata.value AS termin')
                ->join('customers', 'customers.id = sales_order.id_customer')
                ->join('employees', 'employees.id = sales_order.sales_id')
                ->join('metadata', 'metadata.id = customers.termin', 'left')
                ->find($docId);

            $salesName = $soData->salesName;
            $termin = $soData->termin;
            $customerName = $soData->customerName;
            $customerAddress = $soData->customerAddress;
            $taxStatus = filter_var($soData->tax_status, FILTER_VALIDATE_BOOLEAN);
            $includeTax = filter_var($soData->include_pa, FILTER_VALIDATE_BOOLEAN);
        } else {
            $selectQry = "surat_jalan_so.*, 
                          customers.name AS customerName, 
                          customers.address AS customerAddress, 
                          CONCAT(employees.nip , ' - ', employees.name) AS salesName, 
                          IFNULL(metadata.value, '-') AS termin";
            $suratJalanData = $this->SuratJalanModel->asObject()
                ->select($selectQry)
                ->join('customers', 'customers.id = surat_jalan_so.id_customer')
                ->join('employees', 'employees.id = customers.sales_id')
                ->join('metadata', 'metadata.id = customers.termin', 'left')
                ->find($docId);

            $salesName = $suratJalanData->salesName;
            $termin = $suratJalanData->termin;
            $soId = json_decode($suratJalanData->multiple_id_so);
            $customerName = $suratJalanData->customerName;
            $customerAddress = $suratJalanData->customerAddress;
        }

        $itemList = $this->SalesOrderDetailModel->getItemListByIds($soId);
        $itemTax = $this->TaxModel->where('id', '4')->asObject()->findAll();

        $dpp = 0;
        $taxAmt = 0;
        $taxChecked = 0;

        foreach ($itemList as $item) {
        }

        foreach ($itemList as &$item) {
            foreach ($itemTax as $itemT) {
                $item->taxChecked = str_replace(',', '', $itemT->tax_value);
            }
            $hargaBarang = str_replace(',', '', $item->harga_barang);
            $qty = str_replace(',', '', $item->qty);

            $itemTotal = $hargaBarang * $qty;

            $dpp += $itemTotal;
            $taxAmt += $itemTotal * ($item->tax / 100);
        }

        $data = (object)[
            'salesName'         => $salesName,
            'termin'            => $termin,
            'customerName'      => $customerName,
            'customerAddress'   => $customerAddress,
            'taxStatus'         => $taxStatus,
            'includeTax'        => $includeTax,
            'itemList'          => $itemList,
            'dpp'               => number_format($dpp),
            'tax'               => number_format($taxAmt),
            'taxChecked'               => number_format($taxChecked),
            'total'             => number_format($dpp + $taxAmt)
        ];
        return $data;
    }
}
