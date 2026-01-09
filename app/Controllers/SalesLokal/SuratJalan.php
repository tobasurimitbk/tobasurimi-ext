<?php

namespace App\Controllers\SalesLokal;

use App\Controllers\BaseController;

use Config\Services;
use Dompdf\Dompdf;

use App\Models\CompaniesModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderDetailModel;
use App\Models\CustomerModel;
use App\Models\AllNoModel;
use App\Models\BarangMasterSalesModel;
use App\Models\SuratJalanModel;
use App\Models\SuratJalanDetailModel;
use App\Models\EmployeesModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderInvoiceDetailModel;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SuratJalan extends BaseController
{
    private $token;
    private $this_company_id;
    protected $is_admin;
    private $userId;

    private $companyModel;
    private $CustomerModel;
    private $SalesOrderModel;
    private $SalesOrderDetailModel;
    private $SalesOrderInvoiceDetailModel;
    private $encrypter;
    private $SuratJalanModel;
    private $SuratJalanDetailModel;
    private $EmployeesModel;
    private $AllNoModel;
    private $metaDataModel;
    private $barangMasterSalesModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userId = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;

        $this->encrypter = Services::encrypter();
        $this->companyModel = new CompaniesModel();
        $this->CustomerModel = new CustomerModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
        $this->SalesOrderInvoiceDetailModel = new SalesOrderInvoiceDetailModel();
        $this->AllNoModel = new AllNoModel();
        $this->SuratJalanModel = new SuratJalanModel();
        $this->SuratJalanDetailModel = new SuratJalanDetailModel();
        $this->EmployeesModel = new EmployeesModel();
        $this->metaDataModel = new MetadataModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
    }

    public function index()
    {
        $data = [
            'getCustomers' => $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin),
            'getCompany' => $this->companyModel->where('deletedAt', NULL)->findAll(),
        ];
        return view('SalesLokal/SuratJalan/index', $data);
    }

    public function createView()
    {
        //Get Customers
        $customers = $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin);


        $data = [
            "dataCustomers" => $customers,
            "id_user" => session()->get('login')->user_id,
            "seller_name" => session()->get('login')->name,

        ];

        return view('SalesLokal/SuratJalan/form', $data);
    }

    public function all()
    {
        $pageSize = (int) $this->request->getGet("length");
        $start = (int) $this->request->getGet("start");
        $offset = $start; // DataTables sudah kirim offset dalam bentuk jumlah data
        $currentPage = ($start / $pageSize) + 1;

        $payload = [
            "pageSize" => $pageSize,
            "currentPage" => $currentPage,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        if ($this->is_admin == '1') {
            $condition = [
                "surat_jalan_so.deletedAt" => null,
                // "surat_jalan_so.id_company" => $this->this_company_id,
            ];
        } else {
            $condition = [
                "surat_jalan_so.deletedAt" => null,
                // "surat_jalan_so.id_company" => $this->this_company_id,
                'surat_jalan_so.id_user' => $this->userId
            ];
        }


        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter_invoice"        => $this->request->getGet("filter_invoice"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "filter_customer"        => $this->request->getGet("filter_customer"),
            "filter_company"        => $this->request->getGet("filter_company"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSuratJalan = $this->SuratJalanModel
            ->getAllSuratJalan($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $customerSales = "-";
        // var_dump($dataSuratJalan['data']);
        // exit;
        $dataAllSuratJalan = [];
        foreach ($dataSuratJalan['data'] as $data) {
            $dataNo = json_decode($data->multiple_no_so, true);
            if ($data->sales_order_invoice_id) {
                $dataSumAmount = $this->SalesOrderInvoiceDetailModel->getSumAmount($data->sales_order_invoice_id);
            }
            // if ($data->sales_id == NULL || $data->sales_id == "0") {
            //     $customerSales = "-";
            // } else {
            //     $getEmployee = $this->EmployeesModel->select("CONCAT(employees.nip, ' - ', employees.name) AS customerSales")->where('employees.id', $data->sales_id)->first();
            //     if ($getEmployee && isset($getEmployee['customerSales'])) {
            //         $customerSales = $getEmployee['customerSales'];
            //     } else {
            //         $customerSales = '-'; // atau null, atau string kosong, sesuai kebutuhan
            //     }
            // }
            array_push($dataAllSuratJalan, [
                "no"                => $no++,
                "id"                => encrypt($data->id),
                "no_surat_jalan"    => $data->no_surat_jalan,
                "tipe_sales_order"  => $data->tipe_sales_order ?? 'LOKAL',
                "no_so"             => implode(', ', $dataNo),
                "kode_pelanggan"    => $data->kode_pelanggan,
                "nama_pelanggan"    => $data->nama_pelanggan,
                "customerSales"     => $data->customerSales ?? "-",
                "shipping_date"     => date("d-m-Y", strtotime($data->shipping_date)),
                "sales_order_invoice_id" => $data->sales_order_invoice_id,
                "print" => $data->counter_print,
                "total_harga" => $data->sum_amount_sj_detail ?? ($data->estimated_freight + $data->total_harga),
                "posting" => $data->posting,
                "company_name"      => $data->company_name,
            ]);
        }
        //dd($dataAllSuratJalan);


        $data = [
            "draw"            => intval($this->request->getGet("draw")),
            "recordsTotal"    => $dataSuratJalan['totalData'],
            "recordsFiltered" => $dataSuratJalan['totalFilteredData'],
            "data"              => $dataAllSuratJalan,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function save()
    {
        $rules = [
            "id_customer" => [
                "rules" => "required",
                'errors' => [
                    'required' => 'Customer tidak boleh kosong',
                ]
            ],
            "shipping_date" => [
                "rules" => "required|valid_date[d/m/Y]",
                'errors' => [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "no_po" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
            "note" => [
                "rules" => "permit_empty",
                'errors' => [
                    // 'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
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

        $dataSo = $this->request->getPost('id_so');

        $idArray = array();
        $noArray = array();
        if ($dataSo) {
            foreach ($dataSo as $soId) {
                $soData = $this->SalesOrderModel->asObject()->find($soId);

                if (empty($soData)) {
                    $data = [
                        "status"    => false,
                        "message"   => "Sales Order tidak ditemukan!",
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                    return;
                }

                $idArray[] = $soId;
                $noArray[] = $soData->no_sales_order;
            }
        }
        try {
            $shippingDate = $this->request->getPost('shipping_date');
            $listItems = json_decode($this->request->getPost('list_items'), true);

            // start transaction
            $this->SuratJalanModel->db->transException(true)->transStart();
            $values = [
                "id_user"           => $this->userId,
                "id_customer"       => $this->request->getPost('id_customer'),
                "shipping_date"     =>  $shippingDate ? date("Y-m-d", strtotime(str_replace("/", "-", $shippingDate))) : "",
                "no_surat_jalan"    => strtoupper($this->request->getVar('no_surat_jalan')),
                "no_po"             => $this->request->getPost('no_po') ?? null,
                "note"              => $this->request->getPost('note'),
                "terms"             => $this->request->getPost('termin'),
                'multiple_id_so'    => json_encode($idArray) ?? null,
                'multiple_no_so'    => json_encode($noArray) ?? null,
                "id_company"        => ($this->this_company_id != 16)
                    ? $this->request->getPost('company_id')
                    : $this->this_company_id,
            ];

            $checkSJ = $this->SuratJalanModel->where('deletedAt', NULL)->where('UPPER(no_surat_jalan)', strtoupper($this->request->getVar('no_surat_jalan')))->findAll();
            if ($checkSJ) {
                $data = [
                    "status"    => false,
                    "message"   => "No Surat Jalan Sudah Digunakan",
                    "payload"   => $values,
                    'token'     => csrf_hash(),
                ];
                echo json_encode($data);
                return;
            }
            $dataSuratJalan =  $this->SuratJalanModel->insert($values);

            if ($idArray != []) {
                $this->SalesOrderModel->whereIn('id', $idArray)
                    ->set(['surat_jalan_so_id' => $dataSuratJalan])
                    ->update();
            }

            foreach ($listItems as $key => $value) {
                $dataBarang = $this->barangMasterSalesModel->find($value['id_barang']);
                $valueBarang = [
                    "id_surat_jalan"        => $dataSuratJalan,
                    "id_barang"             => $value['id_barang'],
                    "id_sales_order"        => $value['id_sales_order'] ?? null,
                    "id_sales_order_detail" => $value['id_sales_order_detail'] ?? null,
                    "qty"                   => number_format($value['qty'], 2, '.', ''),
                    "qty_sekarang"          => number_format($value['qty'], 2, '.', ''),
                    "harga_barang"          => number_format($value['harga_barang'], 2, '.', ''),
                    "hpp"                   => number_format($dataBarang['harga_pokok'], 2, '.', ''),
                    "amount"                => number_format($value['total_harga_barang'], 2, '.', ''),
                    "keterangan"            => $value['keterangan'],
                    "discount_percentage"   => number_format($value['disc'], 2, '.', ''),
                    "tipe_input"            => $value['tipe_input'],
                    "status_ppn"            => $value['statusppn'],
                    "discount_unit"         => $value['discUnit'],
                ];
                $this->SuratJalanDetailModel->save($valueBarang);
            }

            // finish transaction
            $this->SuratJalanModel->db->transComplete();

            $data = [
                "id"        => encrypt($dataSuratJalan),
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                "payload"   => [],
                'token'     => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function getById($id = null)
    {
        $id = decrypt($id);
        $dataSuratJalan = $this->SuratJalanModel->getSuratJalanById(($id));
        $dataSuratJalanDetail = $this->SuratJalanDetailModel->getItemListByIds($id);

        if (empty($dataSuratJalan)) {
            return view('errors/html/error_404', ['message' => 'Not Found']);
        }

        $customers = $this->CustomerModel->getCustomerLokal($this->userId, $this->is_admin);

        $dataSuratJalan->shipping_date = date("d/m/Y", strtotime($dataSuratJalan->shipping_date));
        $dataSo = $this->SalesOrderModel
            ->asObject()
            ->where(['id_customer' => $dataSuratJalan->id_customer, 'tipe_sales_order' => 'LOKAL', 'deletedAt' => null])
            ->select(['id', 'no_sales_order'])
            ->findAll();

        $dataSuratJalan->itemList = $this->SalesOrderDetailModel->getItemListByIds($dataSuratJalan->multiple_id_so);
        $dataSuratJalan->id = encrypt($dataSuratJalan->id);

        foreach ($dataSuratJalan->itemList as $value) {
            $value->harga_barang = $value->harga_barang;
            $value->amount = $value->total_harga_barang;
        }

        if ($dataSuratJalan->jenis_penjualan == 1) {
            $getJenisPenjualan = "By Sales";
        } elseif ($dataSuratJalan->jenis_penjualan == 2) {
            $getJenisPenjualan = "By Office";
        } elseif ($dataSuratJalan->jenis_penjualan == 3) {
            $getJenisPenjualan = "By Ecommerce";
        } else {
            $getJenisPenjualan = "";
        }

        $dataTermin = $this->metaDataModel
            ->where('metadata.deletedAt', null)
            ->where('metadata.name', 'Termin')
            ->orderBy('CAST(metadata.value AS DECIMAL)', 'ASC')
            ->findAll();

        $dataSuratJalan->posting = 0;

        $data = [
            "data" => $dataSuratJalan,
            "dataCustomers" => $customers,
            "dataDetail" => $dataSuratJalanDetail,
            "id_user" => $dataSuratJalan->id_user,
            "dataSo" => $dataSo,
            "getJenisPenjualan" => $getJenisPenjualan,
            "termin" => $dataTermin,
        ];
        return view('SalesLokal/SuratJalan/form', $data);
    }

    public function update()
    {
        $payload = $this->request->getVar();

        $data = [
            "payload" => $payload,
            //"items" => $items,
            'token'   => csrf_hash()
        ];
        //echo json_encode($data);

        $validate = $this->validate([
            "shipping_date" => [
                "rules" => "required",
                'errors' =>
                [
                    'required' => 'tanggal pengiriman tidak boleh kosong',
                ]
            ],
        ]);

        $id = decrypt($this->request->getPost('id'));
        if (!$validate) {
            // echo json_encode($payload);
            //return;
            return redirect()->to('/surat-jalan/id/' . encrypt($id))->back()->withInput();
        }

        $dataSo = $this->request->getPost('id_so');

        $idArray = array();
        $noArray = array();

        if ($dataSo) {
            foreach ($dataSo as $soId) {
                $soData = $this->SalesOrderModel->asObject()->find($soId);

                if (empty($soData)) {
                    $data = [
                        "status"    => false,
                        "message"   => "Sales Order tidak ditemukan!",
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                    return;
                }

                $idArray[] = $soId;
                $noArray[] = $soData->no_sales_order;
            }
        }

        $shippingDate = $this->request->getPost('shipping_date');
        $listItems = json_decode($this->request->getPost('list_items'), true);

        $values = [
            "id_customer"       => $this->request->getPost('id_customer'),
            "shipping_date"     =>  $shippingDate ? date("Y-m-d", strtotime(str_replace("/", "-", $shippingDate))) : "",
            "no_surat_jalan"    => strtoupper($this->request->getVar('no_surat_jalan')),
            "no_po"             => $this->request->getPost('no_po'),
            "note"              => $this->request->getPost('note'),
            "terms"             => $this->request->getPost('termin'),
            'multiple_id_so'    => json_encode($idArray),
            'multiple_no_so'    => json_encode($noArray),
            "id_company"        => ($this->this_company_id != 16)
                ? $this->request->getPost('company_id')
                : $this->this_company_id,
        ];

        try {
            $dataSJ = $this->SuratJalanModel->find($id);
            $checkSJ = $this->SuratJalanModel->where('deletedAt', NULL)->where('UPPER(no_surat_jalan)', strtoupper($this->request->getVar('no_surat_jalan')))->first();
            if ($checkSJ) {
                if ($checkSJ['id'] != $dataSJ['id']) {
                    $data = [
                        "status"    => false,
                        "message"   => "No Surat Jalan Sudah Digunakan",
                        "payload"   => $values,
                        'token'     => csrf_hash(),
                    ];
                    echo json_encode($data);
                    return;
                }
            }

            // Create a new validation instance
            $dataSuratJalan =  $this->SuratJalanModel->update($id, $values);

            foreach ($listItems as $key => $value) {
                $dataBarang = $this->barangMasterSalesModel->find($value['id_barang']);
                if (isset($value['id_detail_sj'])) {
                    $valueBarang = [
                        "id_barang"             => $value['id_barang'],
                        "id_sales_order"        => $value['id_sales_order'] ?? null,
                        "id_sales_order_detail" => $value['id_sales_order_detail'] ?? null,
                        "qty"                   => number_format($value['qty'], 2, '.', ''),
                        "qty_sekarang"          => number_format($value['qty'], 2, '.', ''),
                        "harga_barang"          => number_format($value['harga_barang'], 2, '.', ''),
                        "hpp"                   => number_format($dataBarang['harga_pokok'], 2, '.', ''),
                        "amount"                => number_format($value['total_harga_barang'], 2, '.', ''),
                        "keterangan"            => $value['keterangan'],
                        "discount_percentage"   => number_format($value['disc'], 2, '.', ''),
                        "tipe_input"            => $value['tipe_input'],
                        "status_ppn"            => $value['statusppn'],
                        "discount_unit"         => $value['discUnit'],
                    ];
                    $this->SuratJalanDetailModel->update($value['id_detail_sj'], $valueBarang);
                } else {
                    $valueBarang = [
                        "id_surat_jalan"        => $id,
                        "id_barang"             => $value['id_barang'],
                        "id_sales_order"        => $value['id_sales_order'] ?? null,
                        "id_sales_order_detail" => $value['id_sales_order_detail'] ?? null,
                        "qty"                   => number_format($value['qty'], 2, '.', ''),
                        "qty_sekarang"          => number_format($value['qty'], 2, '.', ''),
                        "harga_barang"          => number_format($value['harga_barang'], 2, '.', ''),
                        "hpp"                   => number_format($dataBarang['harga_pokok'], 2, '.', ''),
                        "amount"                => number_format($value['total_harga_barang'], 2, '.', ''),
                        "keterangan"            => $value['keterangan'],
                        "discount_percentage"   => number_format($value['disc'], 2, '.', ''),
                        "tipe_input"            => $value['tipe_input'],
                        "status_ppn"            => $value['statusppn'],
                        "discount_unit"         => $value['discUnit'],
                    ];
                    $this->SuratJalanDetailModel->save($valueBarang);
                }
            }
            // exit;
            $data = [
                "id" => $dataSuratJalan,
                "status"            => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            //echo "Transaction failed: " . $e->getMessage();
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                "payload"   => $values,
                'token' => csrf_hash(),
            ];
            echo json_encode($data);
            return;
        };
    }

    public function delete()
    {
        $id = decrypt($this->request->getPost("id"));
        try {
            if (!empty($id)) {

                $checkSJ = $this->SuratJalanModel
                    ->where('id', $id)
                    ->where('sales_order_invoice_id !=', null)
                    ->first();

                if ($checkSJ) {
                    $data = [
                        "status"     => false,
                        "message"    => "Data Surat Jalan sudah digunakan tidak dapat dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }

                $sJData = $this->SuratJalanModel->asObject()
                    ->find($id);
                foreach (json_decode($sJData->multiple_id_so) as $id_doc) {

                    $this->SalesOrderModel->where('id', $id_doc)->set(['surat_jalan_so_id' => NULL])->update();
                }

                $this->SuratJalanModel->delete($id);
                $this->SuratJalanDetailModel->where('id_surat_jalan', $id)->delete();
                $data = [
                    "status"            => true,
                    "message"    => "Data success Dihapus",
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

    public function deleteDetail()
    {
        $id = $this->request->getPost("id");
        try {
            $dataSuratJalanDetail = $this->SuratJalanDetailModel->find($id);
            if (!empty($id)) {

                $checkSJ = $this->SuratJalanModel
                    ->where('id', $dataSuratJalanDetail['id_surat_jalan'])
                    ->where('sales_order_invoice_id !=', null)
                    ->first();

                if ($checkSJ) {
                    $data = [
                        "status"     => false,
                        "message"    => "Data Surat Jalan sudah digunakan tidak dapat dihapus",
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }

                $this->SuratJalanDetailModel->delete($id);
                $data = [
                    "status"            => true,
                    "message"    => "Data success Dihapus",
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

    public function dropDownSalesOrder($idCustomer)
    {
        // --- Ambil data customer ---
        $customerData = $this->CustomerModel->asObject()
            ->select('customers.*, employees.name as salesName, metadata.value AS customerTermin')
            ->join('employees', 'employees.id = customers.sales_id', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->find($idCustomer);

        // --- Ambil semua Sales Order aktif milik customer ini ---
        $condition = [
            'sales_order.id_customer'      => $idCustomer,
            'sales_order.tipe_sales_order' => 'LOKAL',
            'sales_order.deletedAt'        => null
        ];

        $soList = $this->SalesOrderModel->asObject()
            ->where($condition)
            ->select('sales_order.*, 
            metadata.value AS customerTermin, 
            CONCAT(employees.nip , " - ", employees.name) AS salesName')
            ->join('metadata', 'metadata.id = sales_order.payment_terms', 'left')
            ->join('employees', 'employees.id = sales_order.sales_id', 'left')
            ->findAll();

        // --- Ambil semua total qty dari surat jalan detail sekaligus (lebih efisien) ---
        $sjDetails = $this->SuratJalanDetailModel
            ->select('id_sales_order_detail, SUM(qty) as total_qty')
            ->where('deletedAt', null)
            ->groupBy('id_sales_order_detail')
            ->findAll();

        // Buat array lookup [id_sales_order_detail => total_qty]
        $sjQtyMap = [];
        foreach ($sjDetails as $row) {
            $sjQtyMap[$row['id_sales_order_detail']] = (float)$row['total_qty'];
        }

        // --- Proses per Sales Order ---
        $finalSoList = [];

        foreach ($soList as $so) {
            $detailSO = $this->SalesOrderDetailModel
                ->where('id_sales_order', $so->id)
                ->where('deletedAt', null)
                ->findAll();

            $filteredDetails = [];

            foreach ($detailSO as &$d) {
                $qtySekarang = (float)$d['qty_sekarang'];

                // Cek apakah detail ini sudah pernah masuk surat jalan
                $totalQtySJ = $sjQtyMap[$d['id']] ?? 0;


                // Hitung sisa qty
                $qtySekarang -= $totalQtySJ;

                // Hanya tambahkan kalau masih ada sisa qty
                if ($qtySekarang > 0) {
                    $d['qty_sekarang'] = $qtySekarang;
                    $filteredDetails[] = $d;
                }
            }

            // Hanya tambahkan SO yang punya detail dengan qty > 0
            if (!empty($filteredDetails)) {
                $so->details = $filteredDetails;
                $finalSoList[] = $so;
            }
        }

        $data = [
            'customerData' => $customerData,
            'soList'       => $finalSoList,
        ];

        echo json_encode($data);
    }

    public function dropDownSuratJalan($id_customer)
    {
        $data = $this->SuratJalanModel
            ->asObject()
            ->where(['id_customer' => $id_customer])
            ->select(['id', 'no_surat_jalan'])
            ->findAll();


        echo json_encode($data);
        return;
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
            "detail_so" => $detailSo,
            "detail_barang" => $detailBarang
        ];
        echo json_encode($allData);
        return;
    }

    public function printSJ($id)
    {
        $id = decrypt($id);
        $domPdf = new Dompdf();
        $fileName = 'Order Form';

        // Ambil data perusahaan
        $companyData = $this->companyModel->asObject()
            ->find($this->this_company_id);

        // Data Surat Jalan + Customer
        $sjData = $this->SuratJalanModel->asObject()
            ->select('surat_jalan_so.*,
                  customers.kode AS customerCode,
                  customers.phone AS phone,
                  customers.name AS customerName,
                  customers.address AS customerAddress,
                  metadata.value AS termin,
                  DATE_FORMAT(surat_jalan_so.shipping_date, "%d %b %Y") AS shipping_date')
            ->join('customers', 'customers.id = surat_jalan_so.id_customer', 'left')
            ->join('metadata', 'metadata.id = customers.termin', 'left')
            ->find($id);

        if (!$sjData) {
            throw new \RuntimeException("Surat Jalan dengan ID {$id} tidak ditemukan.");
        }

        // Detail Surat Jalan
        $dataSuratJalanDetail = $this->SuratJalanDetailModel->getItemListByIds($id);

        // Update counter_print
        $this->SuratJalanModel->update($id, [
            'counter_print' => (int)($sjData->counter_print ?? 0) + 1
        ]);

        // Ambil Sales Order terkait (untuk info header dan fallback)
        $soIds = json_decode($sjData->multiple_id_so, true) ?: [];
        $soIds = array_values(array_filter($soIds, fn($v) => $v !== null && $v !== ''));

        $salesOrderData = [];
        if (!empty($soIds)) {
            $soSelectQry = "sales_order.*,
            DATE_FORMAT(sales_order.order_date, '%d %b %Y') AS order_date, 
            DATE_FORMAT(sales_order.shipping_date, '%d %b %Y') AS shipping_date, 
            customers.kode AS customerCode, 
            customers.phone AS phone, 
            customers.name AS customerName, 
            customers.address AS customerAddress,
            metadata.value AS termin,
            barang_master_sales.barang_name AS namaBarang, 
            barang_master_sales.kode_barang AS kodeBarang, 
            sales_order_detail.qty AS qty, 
            satuans.kode_satuan AS kodeSatuan,
            sales_order_detail.discount_percentage AS disc_pct,
            sales_order_detail.amount AS amt";

            $salesOrderData = $this->SalesOrderModel->asObject()
                ->select($soSelectQry)
                ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                ->join('metadata', 'metadata.id = customers.termin', 'left')
                ->join('sales_order_detail', 'sales_order_detail.id_sales_order = sales_order.id', 'left')
                ->join('barang_master_sales', 'barang_master_sales.id = sales_order_detail.id_barang', 'left')
                ->join('satuans', 'satuans.id = barang_master_sales.satuan_id', 'left')
                ->whereIn('sales_order.id', $soIds)
                ->where('tipe_input', "order_form")
                ->findAll();
        }

        $data = [
            'companyName'   => $companyData->company,
            'sjData'        => $sjData,
            'soData'        => $salesOrderData,
            'sjDetailData'  => $dataSuratJalanDetail
        ];

        // return view('SalesLokal/SuratJalan/print', $data);

        // --- jika mau PDF, pindahkan return view dan pakai Dompdf ---
        $domPdf->loadHtml(view('SalesLokal/SuratJalan/print', $data));
        $domPdf->setPaper('A4', 'landscape');
        $domPdf->set_option('defaultFont', 'DejaVu Sans Mono');
        $domPdf->render();
        $domPdf->stream($fileName, ["Attachment" => false]);
        exit();
    }

    public function generateNomorSuratJalan()
    {
        $code   = "TSI/SJ";
        $year   = date('y'); // 2 digit tahun
        $month  = date('n'); // 1–12 tanpa nol depan
        $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $template = "{$code}/{$romawi[$month]}/{$year}/";

        // Ambil record terakhir
        $last = $this->SuratJalanModel
            ->select('no_surat_jalan')
            ->orderBy('createdAt', 'DESC')
            ->first();

        if ($last && !empty($last['no_surat_jalan'])) {
            // Pecah nomor terakhir
            $parts = explode('/', $last['no_surat_jalan']);
            // Ambil elemen terakhir sebagai nomor urut
            $lastNumber = is_numeric(end($parts)) ? (int)end($parts) : 0;
            $nextNumber = $lastNumber + 1;

            // Gunakan prefix lama supaya format tetap konsisten
            $prefix = implode('/', array_slice($parts, 0, -1));
            $newNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $suratJalanNumber = "{$prefix}/{$newNumber}";
        } else {
            // Jika belum ada data sama sekali
            $suratJalanNumber = $template . '001';
        }

        return $this->response->setJSON([
            'data'   => $suratJalanNumber,
            'token'  => csrf_hash(),
            'status' => true
        ]);
    }

    public function posting()
    {
        try {

            $id = $this->request->getVar('id');
            if (is_numeric($id)) {
                $id = $id;
            } else {
                $id = decrypt($id);
            }

            // po posting
            $payload = [
                "posting" => $this->request->getVar('status_posting'),
            ];

            $this->SuratJalanModel->update($id, $payload);

            $data = [
                "status"    => true,
                "message"   => "Surat Jalan Berhasil Diperbaruhi",
                "payload"   => json_encode($payload),
                'token'     => csrf_hash()
            ];

            echo json_encode($data);
        } catch (Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function exportExcel()
    {
        $limit = null;
        $offset = null;

        $condition = [
            "surat_jalan_so.deletedAt" => null,
        ];

        if ($this->is_admin == '0') {
            $condition['surat_jalan_so.id_user'] = $this->userId;
        }

        $addCondition = [
            "search"              => $this->request->getGet("search"),
            "filter_invoice"      => $this->request->getGet("filter_invoice"),
            "filter_customer"     => $this->request->getGet("filter_customer"),
            "sort"                => $this->request->getGet("sort"),
            "sortType"            => $this->request->getGet("sortType"),
            "dateStart"           => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"             => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSuratJalan = $this->SuratJalanModel->getAllSuratJalan($condition, $addCondition, $limit, $offset);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'No Surat Jalan');
        $sheet->setCellValue('C1', 'Tipe Sales Order');
        $sheet->setCellValue('D1', 'No Sales Order');
        $sheet->setCellValue('E1', 'Kode Pelanggan');
        $sheet->setCellValue('F1', 'Nama Pelanggan');
        $sheet->setCellValue('G1', 'Sales');
        $sheet->setCellValue('H1', 'Tanggal Kirim');
        $sheet->setCellValue('I1', 'Total Harga');

        $row = 2;
        $no = 1;

        foreach ($dataSuratJalan['data'] as $data) {
            $dataNo = json_decode($data->multiple_no_so, true);
            $noSO = is_array($dataNo) ? implode(', ', $dataNo) : '';

            if ($data->sales_id == NULL || $data->sales_id == "0") {
                $customerSales = "-";
            } else {
                $getEmployee = $this->EmployeesModel->select("CONCAT(employees.nip, ' - ', employees.name) AS customerSales")->where('employees.id', $data->sales_id)->first();
                $customerSales = $getEmployee['customerSales'] ?? '-';
            }

            $totalHarga = $data->estimated_freight + $data->total_harga;

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $data->no_surat_jalan);
            $sheet->setCellValue("C{$row}", $data->tipe_sales_order);
            $sheet->setCellValue("D{$row}", $noSO);
            $sheet->setCellValue("E{$row}", $data->kode_pelanggan);
            $sheet->setCellValue("F{$row}", $data->nama_pelanggan);
            $sheet->setCellValue("G{$row}", $customerSales);
            $sheet->setCellValue("H{$row}", date("d/m/Y", strtotime($data->shipping_date)));
            $sheet->setCellValue("I{$row}", floatval($totalHarga));

            // Format total harga
            $sheet->getStyle("I{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            $row++;
        }

        $filename = 'Export-Surat-Jalan-' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function getItemListByIds()
    {
        $ids = $this->request->getGet('ids');

        if (empty($ids)) {
            echo json_encode([]);
            return;
        }

        $datas = $this->SalesOrderDetailModel->getItemListByIds($ids);
        $result = [];

        foreach ($datas as $value) {

            // Kalau ADA surat jalan, cek apakah ada detail untuk sales order detail ini
            $dataSuratJalanDetail = $this->SuratJalanDetailModel
                ->select('sum(qty) as sum_qty')
                ->where('id_sales_order_detail', $value->id)
                ->where('deletedAt', null)
                ->groupBy('id_sales_order_detail')
                ->findAll();

            if ($dataSuratJalanDetail) {
                // Ada detail → kurangi qty sekarang
                $qty = (float)$value->qty - (float)$dataSuratJalanDetail[0]['sum_qty'];
                $value->qty = $qty;
                if ($value->discUnit == "percent") {
                    $value->amount = ($value->harga_barang - ($value->harga_barang * $value->discAmt / 100)) * $qty;
                } else {
                    $value->amount = ($value->harga_barang - $value->discAmt) * $qty;
                }
            }

            // Hanya masukkan jika qty masih > 0
            if ((float)$value->qty > 0) {
                $result[] = $value;
            }
        }

        echo json_encode($result);
    }

    // public function generateSuratJalanDetail()
    // {
    //     $insertedList = []; // array untuk menyimpan hasil insert
    //     $dataSJ = $this->SuratJalanModel->asObject()
    //         ->where('deletedAt', null)
    //         ->findAll();

    //     foreach ($dataSJ as $sj) {
    //         $id = $sj->id;
    //         $dataDetail = $this->SuratJalanDetailModel->where('id_surat_jalan', $id)->findAll();

    //         if ($dataDetail) {
    //             // jika sudah ada detail, lanjut ke SJ berikutnya
    //             continue;
    //         } else {
    //             $dataSuratJalan = $this->SuratJalanModel->getSuratJalanById($id);
    //             $itemList = $this->SalesOrderDetailModel->getItemListByIds($dataSuratJalan->multiple_id_so);

    //             foreach ($itemList as $item) {
    //                 if (isset($item->id_detail_sj)) {
    //                     continue;
    //                 } else {
    //                     $valueBarang = [
    //                         "id_surat_jalan"        => $id,
    //                         "id_barang"             => $item->id_barang,
    //                         "id_sales_order"        => $item->id_sales_order ?? null,
    //                         "id_sales_order_detail" => $item->id_sales_order_detail ?? null,
    //                         "qty"                   => number_format($item->qty, 2, '.', ''),
    //                         "qty_sekarang"          => number_format($item->qty, 2, '.', ''),
    //                         "harga_barang"          => number_format($item->harga_barang, 2, '.', ''),
    //                         "amount"                => number_format($item->total_harga_barang, 2, '.', ''),
    //                         "keterangan"            => $item->keterangan,
    //                         "discount_percentage"   => number_format($item->disc, 2, '.', ''),
    //                         "tipe_input"            => $item->tipe_input,
    //                         "status_ppn"            => $item->statusppn,
    //                         "discount_unit"         => $item->discUnit,
    //                     ];
    //                     $this->SuratJalanDetailModel->save($valueBarang);
    //                 }
    //             }

    //             // setelah berhasil insert, simpan info SJ
    //             $insertedList[] = [
    //                 'id_surat_jalan' => $id,
    //                 'no_surat_jalan' => $dataSuratJalan->no_surat_jalan
    //             ];
    //         }
    //     }

    //     // tampilkan hasil insert
    //     if (!empty($insertedList)) {
    //         echo "Berhasil insert Surat Jalan Detail untuk:\n";
    //         foreach ($insertedList as $sj) {
    //             echo "ID: {$sj['id_surat_jalan']}, No Surat Jalan: {$sj['no_surat_jalan']}\n";
    //         }
    //     } else {
    //         echo "Tidak ada Surat Jalan yang baru diinsert.\n";
    //     }

    //     return;
    // }
}
