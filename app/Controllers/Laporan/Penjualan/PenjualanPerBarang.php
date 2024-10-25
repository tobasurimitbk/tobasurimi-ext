<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderInvoiceModel;
use Dompdf\Dompdf;

class PenjualanPerBarang extends BaseController
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
        return view('Laporan/LaporanSales/LaporanPerBarang/index', $data);
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
            ->getAllSalesOrderInvoiceLokalBarang($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderInvoice = [];
        $currentBarang = null;
        $totalPerBarang = 0;
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentBarang !== $data->id_barang_invoice) {
                if ($currentBarang !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no" => '',
                        "id" => '',
                        "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerBarang)),
                        "tanggal_faktur" => '',
                        "keterangan" => '',
                        "total_invoice" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                        "id_barang" => '',
                        "kode_barang" => '',
                        "barang_name" => '',
                        "qty_invoice" => '',
                        "kode_satuan" => '',
                    ]);
                }

                $currentBarang = $data->id_barang_invoice;
                $totalPerBarang = 0;

                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "no_faktur" => $data->kode_barang . ' ' . $data->barang_name,
                    "tanggal_faktur" => '',
                    "keterangan" => '',
                    "total_invoice" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                    "id_barang" => '',
                    "kode_barang" => '',
                    "barang_name" => '',
                    "qty_invoice" => '',
                    "kode_satuan" => '',
                ]);
            }

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "total_invoice" => number_format(floatval($data->total_invoice)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "id_barang" => $data->id_barang_invoice,
                "kode_barang" => $data->kode_barang,
                "barang_name" => $data->barang_name,
                "qty_invoice" => $data->qty_invoice,
                "kode_satuan" => $data->kode_satuan,
            ]);

            $totalPerBarang += floatval($data->total_invoice);
        }

        if ($currentBarang !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => '',
                "id" => '',
                "no_faktur" => 'Total Invoice: Rp ' . number_format(floatval($totalPerBarang)),
                "tanggal_faktur" => '',
                "keterangan" => '',
                "total_invoice" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
                "id_barang" => '',
                "kode_barang" => '',
                "barang_name" => '',
                "qty_invoice" => '',
                "kode_satuan" => '',
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
            ->getAllSalesOrderInvoiceLokalBarangWithoutLimit($condition, $addCondition);

        $dataAllSalesOrderInvoice = [];
        $currentBarang = null;
        $totalPerBarang = 0;
        $no = 1;

        foreach ($dataSalesOrderInvoice['data'] as $data) {
            if ($currentBarang !== $data->id_barang_invoice) {
                if ($currentBarang !== null) {
                    array_push($dataAllSalesOrderInvoice, [
                        "no" => '',
                        "id" => '',
                        "no_faktur" => 'Rp ' . number_format(floatval($totalPerBarang)),
                        "tanggal_faktur" => '',
                        "keterangan" => '',
                        "total_invoice" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                        "id_barang" => '',
                        "kode_barang" => '',
                        "barang_name" => '',
                        "qty_invoice" => '',
                        "kode_satuan" => '',
                    ]);
                }

                $currentBarang = $data->id_barang_invoice;
                $totalPerBarang = 0;

                array_push($dataAllSalesOrderInvoice, [
                    "no" => '',
                    "id" => '',
                    "no_faktur" => $data->kode_barang . ' ' . $data->barang_name,
                    "tanggal_faktur" => '',
                    "keterangan" => '',
                    "total_invoice" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                    "id_barang" => '',
                    "kode_barang" => '',
                    "barang_name" => '',
                    "qty_invoice" => '',
                    "kode_satuan" => '',
                ]);
            }

            array_push($dataAllSalesOrderInvoice, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_faktur" => $data->no_faktur,
                "tanggal_faktur" => $data->tanggal_faktur,
                "keterangan" => $data->keterangan,
                "total_invoice" => number_format(floatval($data->total_invoice)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->salesName,
                "id_barang" => $data->id_barang_invoice,
                "kode_barang" => $data->kode_barang,
                "barang_name" => $data->barang_name,
                "qty_invoice" => $data->qty_invoice,
                "kode_satuan" => $data->kode_satuan,
            ]);

            $totalPerBarang += floatval($data->total_invoice);
        }

        if ($currentBarang !== null) {
            array_push($dataAllSalesOrderInvoice, [
                "no" => '',
                "id" => '',
                "no_faktur" => 'Rp ' . number_format(floatval($totalPerBarang)),
                "tanggal_faktur" => '',
                "keterangan" => '',
                "total_invoice" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
                "id_barang" => '',
                "kode_barang" => '',
                "barang_name" => '',
                "qty_invoice" => '',
                "kode_satuan" => '',
            ]);
        }

        $data = [
            "data" => $dataAllSalesOrderInvoice,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        // return view('Laporan/LaporanSales/LaporanPerBarang/print', $data);

        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanPerBarang/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Penjualan Per Barang ", array("Attachment" => false));

        exit(0);
    }
}
