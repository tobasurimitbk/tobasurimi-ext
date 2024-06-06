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
use App\Models\SuratJalanModel;
use App\Models\EmployeesModel;

class SuratJalan extends BaseController
{
    private $token;
    private $this_company_id;
    private $userId;

    private $companyModel;
    private $CustomerModel;
    private $SalesOrderModel;
    private $SalesOrderDetailModel;
    private $encrypter;
    private $SuratJalanModel;
    private $EmployeesModel;
    private $AllNoModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->userId = session()->get("login")->user_id;

        $this->encrypter = Services::encrypter();
        $this->companyModel = new CompaniesModel();
        $this->CustomerModel = new CustomerModel();
        $this->SalesOrderModel = new SalesOrderModel();
        $this->SalesOrderDetailModel = new SalesOrderDetailModel();
        $this->AllNoModel = new AllNoModel();
        $this->SuratJalanModel = new SuratJalanModel();
        $this->EmployeesModel = new EmployeesModel();
    }

    public function index()
    {
        $data = [
            'getCustomers' => $this->CustomerModel->where('deletedAt', NULL)->where('tipe_customer', 'LOKAL')->findAll(),
        ];
        return view('SalesLokal/SuratJalan/index', $data);
    }

    public function createView()
    {
        //Get Customers
        $customers = $this->CustomerModel->getCustomerLokal();




        $data = [
            "dataCustomers" => $customers,
            "id_user" => session()->get('login')->user_id,
            "seller_name" => session()->get('login')->name,

        ];

        return view('SalesLokal/SuratJalan/form', $data);
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

        $condition = [
            "surat_jalan_so.deletedAt" => null,
            "surat_jalan_so.id_company" => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter_invoice"        => $this->request->getGet("filter_invoice"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "filter_customer"        => $this->request->getGet("filter_customer"),
            "dateStart"     => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd"       => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];


        $dataSuratJalan = $this->SuratJalanModel
            ->getAllSuratJalan($condition, $addCondition, $pageSize, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        //dd($dataSuratJalan);
        $dataAllSuratJalan = [];
        foreach ($dataSuratJalan['data'] as $data) {
            $dataNo = json_decode($data->multiple_no_so, true);

            if ($data->sales_id != NULL) {

                $getEmployee = $this->EmployeesModel->select("CONCAT(employees.nip, ' - ', employees.name) AS customerSales")->where('employees.id', $data->sales_id)->first();
                $customerSales = $getEmployee['customerSales'];
            } else {
                $customerSales = "-";
            }
            array_push($dataAllSuratJalan, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "no_surat_jalan"      => $data->no_surat_jalan,
                "tipe_sales_order"      => $data->tipe_sales_order,
                "no_so"      => implode(', ', $dataNo),
                "kode_pelanggan"        => $data->kode_pelanggan,
                "nama_pelanggan" => $data->nama_pelanggan,
                "customerSales" => $customerSales,
                "shipping_date"         => date("d-m-Y", strtotime($data->shipping_date)),
                "sales_order_invoice_id" => $data->sales_order_invoice_id,
                "total_harga" => formatRupiah($data->estimated_freight + $data->total_harga),
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
            "id_so.*" => [
                "rules" => "required|numeric",
                "errors" => [
                    "required" => 'Sales Order tidak boleh kosong!'
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


        // $code = "SJ";
        // $currentYear = date('Y');
        // $currentMonth = date('m');
        // $monthName = date("F", mktime(0, 0, 0, $currentMonth, 10));
        // $number = $this->SuratJalanModel->getNumber($currentMonth . "/" . $currentYear);
        // $noSuratJalan = "TSI/" . $code . "/" . $currentMonth . "/" . $currentYear . "/" . $number;

        $shippingDate = $this->request->getPost('shipping_date');

        try {

            // start transaction
            $this->SuratJalanModel->db->transException(true)->transStart();
            $values = [
                "id_user"       => $this->userId,
                "id_customer"   => $this->request->getPost('id_customer'),
                "shipping_date" =>  $shippingDate ? date("Y-m-d", strtotime(str_replace("/", "-", $shippingDate))) : "",
                "no_surat_jalan" => strtoupper($this->request->getVar('no_surat_jalan')),
                "no_po"         => $this->request->getPost('no_po'),
                "note"          => $this->request->getPost('note'),
                'multiple_id_so' => json_encode($idArray),
                'multiple_no_so' => json_encode($noArray),
                "id_company"     => $this->this_company_id,
            ];
            $checkSJ = $this->SuratJalanModel->where('UPPER(no_surat_jalan)', strtoupper($this->request->getVar('no_surat_jalan')))->findAll();
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

            $this->SalesOrderModel->whereIn('id', $idArray)
                ->set(['surat_jalan_so_id' => $dataSuratJalan])
                ->update();

            // finish transaction
            $this->SuratJalanModel->db->transComplete();

            $data = [
                "id"        => $dataSuratJalan,
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
                "payload"   => $values,
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


        if (empty($dataSuratJalan)) {
            return view('errors/html/error_404', ['message' => 'Not Found']);
        }

        $customers = $this->CustomerModel->asObject()
            ->where('company_id', $this->this_company_id)
            ->findAll();

        $dataSuratJalan->shipping_date = date("m/d/Y", strtotime($dataSuratJalan->shipping_date));
        $dataSo = $this->SalesOrderModel
            ->asObject()
            ->where(['id_customer' => $dataSuratJalan->id_customer, 'tipe_sales_order' => 'LOKAL', 'deletedAt' => null])
            ->select(['id', 'no_sales_order'])
            ->findAll();

        $dataSuratJalan->itemList = $this->SalesOrderDetailModel->getItemListByIds($dataSuratJalan->multiple_id_so);
        $dataSuratJalan->id = encrypt($dataSuratJalan->id);

        // var_dump($dataSuratJalan);
        foreach ($dataSuratJalan->itemList as $value) {
            $value->harga_barang = toRupiah(floatval(str_replace('Rp', '', $value->harga_barang)));
            $value->amount = toRupiah(floatval(str_replace('Rp', '', $value->amount)));
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

        $data = [
            "data" => $dataSuratJalan,
            "dataCustomers" => $customers,
            "id_user" => $dataSuratJalan->id_user,
            "dataSo" => $dataSo,
            "getJenisPenjualan" => $getJenisPenjualan

        ];
        // var_dump($dataSo);
        // exit;
        //echo json_encode($data);
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

        $id = $this->request->getPost('id');
        if (!$validate) {
            // echo json_encode($payload);
            //return;
            return redirect()->to('/surat-jalan/id/' . encrypt($id))->back()->withInput();
        }

        // $dataSo = $this->request->getPost('id_so');

        // $idArray = array();
        // $noArray = array();

        // foreach ($dataSo as $payload) {
        //     $delimiter = ",";
        //     $parts = explode($delimiter, $payload);
        //     array_push($idArray, $parts[0]);
        //     array_push($noArray, $parts[1]);
        // }

        $shippingDate = $this->request->getPost('shipping_date');

        $values = [
            "shipping_date" =>  $shippingDate ? date("Y/m/d", strtotime(str_replace("/", "-", $shippingDate))) : "",
            "no_surat_jalan" => $this->request->getVar('no_surat_jalan'),
            "no_po" => $this->request->getPost('no_po'),
        ];
        try {

            // Create a new validation instance
            $dataSuratJalan =  $this->SuratJalanModel->update($id, $values);

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
        //echo json_encode($id);


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
        $customerData = $this->CustomerModel->asObject()
            ->select('customers.*')
            ->find($idCustomer);

        $condition = [
            'id_customer'               => $idCustomer,
            'tipe_sales_order'          => 'LOKAL',
            'surat_jalan_so_id'         => null,
            'sales_order_invoice_id'    => null
        ];
        $soList = $this->SalesOrderModel->asObject()
            ->where($condition)
            ->select('sales_order.*, metadata.value AS customerTermin, CONCAT(employees.nip , " - ", employees.name) AS salesName')
            ->join('metadata', 'metadata.id = sales_order.payment_terms', 'left')
            ->join('employees', 'employees.id = sales_order.sales_id', 'left')
            ->findAll();

        $data = [
            'customerData'  => $customerData,
            'soList'        => $soList
        ];

        echo json_encode($data);
        return;
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

        $companyData = $this->companyModel->asObject()
            ->find($this->this_company_id);

        $sjData = $this->SuratJalanModel->asObject()
            ->select('surat_jalan_so.*, DATE_FORMAT(surat_jalan_so.shipping_date, "%d %b %Y") AS shipping_date')
            // ->join()
            ->find($id);

        $soIds = json_decode($sjData->multiple_id_so);

        $soSelectQry = "sales_order.*,
                        DATE_FORMAT(sales_order.order_date, '%d %b %Y') AS order_date, 
                        DATE_FORMAT(sales_order.shipping_date, '%d %b %Y') AS shipping_date, 
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
            ->findAll();

        /* $soDetQry = "barangs.nama_barang AS namaBarang, 
                     barangs.kode_barang AS kodeBarang, 
                     sales_order_detail.qty AS qty, 
                     satuans.kode_satuan AS kodeSatuan,
                     sales_order_detail.discount_percentage AS disc_pct,
                     sales_order_detail.amount AS amt";
        $soDet = $this->SalesOrderDetailModel->asObject()
            ->select($soDetQry)
            ->join('barangs', 'barangs.id = sales_order_detail.id_barang')
            ->join('satuans', 'satuans.id = barangs.satuan_id')
            ->whereIn('id_sales_order', $soIds)
            ->findAll(); */
        // dd($soDet);

        $data = [
            'companyName'   => $companyData->company,
            'sjData'        => $sjData,
            'soData'        => $salesOrderData,
            // 'soDet'         => $soDet
        ];

        // return view('SalesLokal/SuratJalan/print', $data);

        // load HTML content
        $domPdf->loadHtml(view('SalesLokal/SuratJalan/print', $data));

        // (optional) setup the paper size and orientation
        $domPdf->setPaper('A4', 'landscape');
        // $domPdf->setPaper([0, 0, 792.96, 528]);


        // render html as PDF
        $domPdf->render();

        // output the generated pdf
        $domPdf->stream($fileName, array("Attachment" => false));

        exit();
    }

    public function generateNomorSuratJalan()
    {
        $code = "SJ";
        $currentYear = date('Y');
        $currentMonth = date('m');
        $number = $this->SuratJalanModel->getNumber($currentMonth . "/" . $currentYear . "/", $this->this_company_id);
        $noSuratJalan = "TSI/" . $code . "/" . $currentMonth . "/" . $currentYear . "/" . $number;

        return response()->setJSON([
            'data' => $noSuratJalan,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
}
