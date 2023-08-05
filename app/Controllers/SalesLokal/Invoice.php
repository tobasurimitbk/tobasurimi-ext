<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderInvoiceModel;
use App\Models\SalesOrderModel;
use App\Models\SuratJalanModel;
use App\Models\AllNoModel;
use Config\Services;
use ErrorException;

class Invoice extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $CustomerModel;
    protected $encrypter;
    protected $MetadataModel;
    protected $SalesOrderInvoiceModel;
    protected $AllNoModel;
    protected $SalesOrderModel;
    protected $SuratJalanModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->encrypter = Services::encrypter();
        $this->CustomerModel = new CustomerModel();
        $this->MetadataModel = new MetadataModel();
        $this->SalesOrderInvoiceModel = new SalesOrderInvoiceModel();
        $this->AllNoModel   = new AllNoModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->SuratJalanModel = new SuratJalanModel();
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
        $data = [
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
            "pageSize" => $pageSize,
            "currentPage" => $currentPage,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
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
                "no"            => $no++,
                "id"            => $data->id,
                "no_faktur"      => $data->no_faktur,
                "total_invoice"      => number_format(floatval($data->total_invoice)),
                "kode_pelanggan"        => $data->kode_pelanggan,
                "keterangan"        => $data->keterangan,
                "nama_pelanggan" => $data->nama_pelanggan,
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
        $validate = $this->validate([
            "id_user" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'User tidak boleh kosong',
                ]
            ],
            "id_customer" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "id_surat_jalan" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'surat jalan tidak boleh kosong',
                ]
            ],

            "tipe_invoice" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pemesanan ID tidak boleh kosong',
                ]
            ],
            "tanggal_faktur" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "no_surat_jalan" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'no surat jalan tidak boleh kosong',
                ]
            ],
            "terms" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'terms tidak boleh kosong',
                ]
            ],
            "total_invoice" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'total harga tidak boleh kosong',
                ]
            ],
            "ppn" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'ppn tidak boleh kosong',
                ],
            ],
            "dpp" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'dpp tidak boleh kosong',
                ],
            ],


        ]);
        if (!$validate) {
            //$error = validation_errors();
            //echo json_encode($error);
            //throw new ErrorException(json_encode($error));
            //return;
            return redirect()->to('/invoice-penjualan-lokal/create')->back()->withInput();
        }
        try {

            $code = "LKL/INV";
            $currentYear = date('Y');
            $currentMonth = date('m');
            $monthName = date("F", mktime(0, 0, 0, $currentMonth, 10));
            $number = $this->AllNoModel->getNumber($code, $monthName . " " . $currentYear);
            $noFaktur = $code . $number . "/" . $currentYear . "/" . $currentMonth;


            $values = [
                "id_user" => $this->request->getPost('id_user'),
                "id_customer" => $this->request->getPost('id_customer'),
                "id_surat_jalan" => $this->request->getPost('id_surat_jalan'),
                "no_surat_jalan" => $this->request->getPost('no_surat_jalan'),
                "no_faktur" => $noFaktur,
                "tanggal_faktur" => $this->request->getPost('tanggal_faktur'),
                "terms" => $this->request->getPost('terms'),
                "ship_via_id" => $this->request->getPost('ship_via'),
                "keterangan" => $this->request->getPost('keterangan'),
                "dpp" => $this->request->getPost('dpp'),
                "ppn" => $this->request->getPost('ppn'),
                "total_invoice" => $this->request->getPost('total_invoice'),
                "termasuk_pa" => $this->request->getPost('include_pa') ? 'true' : 'false',
                "status_tax" => $this->request->getPost('tax_status') ? 'true' : 'false',
                "tipe_invoice" => $this->request->getPost('tipe_invoice'),
                "status_pelunasan" => 'UNPAID',

            ];

            $dataSalesOrderInvoice =  $this->SalesOrderInvoiceModel->insert($values);
            $data = [
                "id" => $dataSalesOrderInvoice,
                "status"            => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token' => csrf_hash(),
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

    public function dataSuratJalanDetail($id_surat_jalan)
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
    public function dropDownSuratJalan($id_customer)
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
        $customers = $this->CustomerModel->asObject()->where('company_id', $this->this_company_id)->select(['id', 'name'])->findAll();
        $dataSalesInvoiceOrder->tanggal_faktur = date("d-m-Y", strtotime($dataSalesInvoiceOrder->tanggal_faktur));
        $tipeShipping = $this->MetadataModel->asObject()->select(['id', 'value'])->where('name', 'tipe_shipping_via')->findAll();
        $detailSoBarang = $this->dataSuratJalanDetail($dataSalesInvoiceOrder->id_surat_jalan);
        $dataSalesInvoiceOrder->detail = $detailSoBarang['detail_barang'];
        $dataSo = $detailSoBarang['detail_so'];
        $dataSuratJalan = $this->dropDownSuratJalan($dataSalesInvoiceOrder->id_customer);

        $data = [
            "data" => $dataSalesInvoiceOrder,
            "dataCustomers" => $customers,
            "id_user" => $dataSalesInvoiceOrder->id_user,
            "seller_name" => $dataSalesInvoiceOrder->seller_name,
            "via" => $tipeShipping,
            'dataSuratJalan' => $dataSuratJalan,
            'dataSo' => $dataSo

        ];
        echo json_encode($data);

        //return view('SalesLokal/Invoice/form', $data);
    }

    public function update()
    {
        $payload =  $this->request->getVar();
        //echo json_encode($payload);
        //return;

        $validate = $this->validate([
            "id" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'User tidak boleh kosong',
                ]
            ],
            "id_user" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'User tidak boleh kosong',
                ]
            ],
            "id_customer" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "id_surat_jalan" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'surat jalan tidak boleh kosong',
                ]
            ],

            "tipe_invoice" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pemesanan ID tidak boleh kosong',
                ]
            ],
            /*
            "tanggal_faktur" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],*/
            "no_surat_jalan" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'no surat jalan tidak boleh kosong',
                ]
            ],
            "terms" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'terms tidak boleh kosong',
                ]
            ],
            "total_invoice" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'total harga tidak boleh kosong',
                ]
            ],
            "ppn" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'ppn tidak boleh kosong',
                ],
            ],
            "dpp" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'dpp tidak boleh kosong',
                ],
            ],


        ]);
        try {
            if (!$validate) {
                $error = validation_errors();
                //echo json_encode($error);
                throw new ErrorException(json_encode($error));
                //return;
                // return redirect()->to('/invoice-penjualan-lokal/create')->back()->withInput();
            }

            $values = [
                "id_user" => $this->request->getPost('id_user'),
                "id_customer" => $this->request->getPost('id_customer'),
                "id_surat_jalan" => $this->request->getPost('id_surat_jalan'),
                "no_surat_jalan" => $this->request->getPost('no_surat_jalan'),
                "tanggal_faktur" => $this->request->getPost('tanggal_faktur'),
                "terms" => $this->request->getPost('terms'),
                "ship_via_id" => $this->request->getPost('ship_via'),
                "keterangan" => $this->request->getPost('keterangan'),
                "dpp" => $this->request->getPost('dpp'),
                "ppn" => $this->request->getPost('ppn'),
                "total_invoice" => $this->request->getPost('total_invoice'),
                "termasuk_pa" => $this->request->getPost('include_pa') ? 'true' : 'false',
                "status_tax" => $this->request->getPost('tax_status') ? 'true' : 'false',
                "tipe_invoice" => $this->request->getPost('tipe_invoice'),
            ];

            $dataSalesOrderInvoice =  $this->SalesOrderInvoiceModel->update($payload['id'], $values);
            $data = [
                "id" => $payload['id'],
                "status"            => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $dataSalesOrderInvoice,
                'token' => csrf_hash(),
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
}
