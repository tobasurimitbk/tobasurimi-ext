<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;

class PenjualanPerPelanggan extends BaseController
{
    protected $this_company_id;
    protected $customerModel;
    protected $salesOrderInvoiceModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->customerModel = new CustomerModel();
        $this->salesOrderInvoiceModel = new SalesOrderInvoiceModel();
    }

    public function index()
    {
        $customerData = $this->customerModel->asObject()->findAll();
        $data = [
            'customer' => $customerData
        ];
        return view('Laporan/LaporanSales/LaporanPerPelanggan/index', $data);
    }

    public function allTransaksi()
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
            "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer" => $this->request->getGet("filter"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokal($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderInvoice = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentCustomer !== $data->nama_pelanggan) {
                // Push the total row for the previous customer, if applicable
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no" => '',
                        "id" => '',
                        "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerCustomer)),
                        "tanggal_faktur" => '',
                        "keterangan" => '',
                        "total_invoice" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                    ]);
                }

                // Reset for the new customer
                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;

                // Add a row for the customer's name
                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "no_faktur" => $data->nama_pelanggan,
                    "tanggal_faktur" => '',
                    "keterangan" => '',
                    "total_invoice" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                ]);
            }

            // Add the regular invoice data
            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "total_invoice" => number_format(floatval($data->total_invoice)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
            ]);

            // Accumulate the total invoice per customer
            $totalPerCustomer += floatval($data->total_invoice);
        }

        // Add the total for the last customer
        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => '',
                "id" => '',
                "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerCustomer)),
                "tanggal_faktur" => '',
                "keterangan" => '',
                "total_invoice" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
            ]);
        }

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $dataSalesOrderInvoice['totalData'],
            "recordsFiltered" => $dataSalesOrderInvoice['totalFilteredData'],
            "data" => $dataAllSalesOrderInvoice,
            "payload" => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function LaporanPenjualanPrint($tglAwal, $tglAkhir, $filter, $search)
    {
        $dompdf = new Dompdf();

        $condition = [
            "sales_order_invoice.id_company" => $this->this_company_id,
            "sales_order_invoice.deletedAt" => null,
            "sales_order_invoice.tipe_invoice" => 'LOKAL'
        ];

        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_jenis_dokumen" => $this->request->getGet("filter_jenis_dokumen"),
            "filter_customer" => $this->request->getGet("filter"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        // Fetch sales order invoice data
        $dataSalesOrderInvoice = $this->salesOrderInvoiceModel
            ->getAllSalesOrderInvoiceLokalWithoutLimit($condition, $addCondition);

        $dataAllSalesOrderInvoice = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            // Check if the customer has changed
            if ($currentCustomer !== $data->nama_pelanggan) {
                // If there's a previous customer, push their total row
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no" => '',
                        "id" => '',
                        "no_faktur" => number_format(floatval($totalPerCustomer)),
                        "tanggal_faktur" => '',
                        "keterangan" => '',
                        "total_invoice" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                    ]);
                }

                // Reset total for the new customer
                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;

                // Add a row for the new customer's name
                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "no_faktur" => $data->nama_pelanggan,
                    "tanggal_faktur" => '',
                    "keterangan" => '',
                    "total_invoice" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                ]);
            }

            // Add the regular invoice data for this customer
            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "total_invoice" => number_format(floatval($data->total_invoice)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
            ]);

            // Accumulate the total invoice for this customer
            $totalPerCustomer += floatval($data->total_invoice);
        }

        // After looping through all data, push the total row for the last customer
        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => '',
                "id" => '',
                "no_faktur" => number_format(floatval($totalPerCustomer)),
                "tanggal_faktur" => '',
                "keterangan" => '',
                "total_invoice" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
            ]);
        }

        $data = [
            "data" => $dataAllSalesOrderInvoice,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        // return view('Laporan/LaporanSales/LaporanPerPelanggan/print', $data);

        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPerPelanggan/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Penjualan Per Pelanggan ", array("Attachment" => false));

        exit(0);
    }
}
