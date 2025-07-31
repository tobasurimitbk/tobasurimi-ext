<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CustomerModel;
use App\Models\SalesOrderExportModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportEkspor extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $customerModel;
    protected $salesOrderExportModel;
    protected $barangMasterSalesModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
    }

    public function index()
    {
        return view('SalesInternasional/Report/index');
    }

    public function indexByCustomer()
    {
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );

        $data = [
            'dataCustomer' => $dataCustomer
        ];

        return view('SalesInternasional/Report/ReportCustomer/index', $data);
    }

    public function allByCustomer()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = [
            'sales_order_export.company_id' => $this->this_company_id,
            'sales_order_export.deletedAt' => null,
            'sales_order_export.status' => 'POSTED'
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "customer_id"   => $this->request->getGet("customer_id"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportModel->getListExportByCustomer($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => encrypt($data->sales_order_export_id),
                "acc_holder"                => $data->acc_holder,
                "sales_order_export_no"     => $data->sales_order_export_no,
                "customer_name"             => $data->customer_name,
                "container"                 => $data->container,
                "actualy_shipment_date"     => date('d/m/Y', strtotime($data->actualy_shipment_date)),
                "total_qty"                 => number_format(floatval($data->total_qty), 2) . " " . $data->kode_satuan,
                "total_qty_convertion"      => $data->total_qty_convertion,
                "valas"                     => $data->valas_name,
                "shipment_value"            => $data->shipment_value,
                "shipment_value_net"        => $data->shipment_value_net,
            ]);
        }

        $footerTotals = [
            'totalQtyConvertion' => $salesData['totalQtyConvertion'],
            'totalShipmentValue' => $salesData['totalShipmentValue'],
            'totalShipmentValueNet' => $salesData['totalShipmentValueNet'],
        ];

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesData['totalData'],
            "recordsFiltered"   => $salesData['totalFilteredData'],
            "data"              => $dataSales,
            "payload"           => $payload,
            "footerTotals"      => $footerTotals
        ];

        echo json_encode($data);
        return;
    }


    public function exportExcel()
    {
        $condition = [
            'sales_order_export.company_id' => $this->this_company_id,
            'sales_order_export.deletedAt' => null,
            'sales_order_export.status' => 'POSTED'
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "customer_id"   => $this->request->getGet("customer_id"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];


        $filename = "Report Ekspor By Customer " . $addCondition['dateStart'] . " - " . $addCondition['dateEnd'];

        $salesOrderExport = $this->salesOrderExportModel->getListExportByCustomer($condition, $addCondition, 100000000, 0);
        $dataResult = [];
        $no = 1;

        // Penampung total
        $totalQtyConvertion = 0;
        $totalShipmentValue = 0;
        $totalShipmentValueNet = 0;

        foreach ($salesOrderExport['data'] as $data) {
            $dataResult[] = [
                "No"                    => $no++,
                "Acc Holder"            => $data->acc_holder,
                "Order Form No"         => $data->sales_order_export_no,
                "Customer"              => $data->customer_name,
                "Container Number"      => $data->container,
                "Actualy Shipment Date" => date('d/m/Y', strtotime($data->actualy_shipment_date)),
                "Qty (Kg)"                => number_format($data->total_qty_convertion, 2),
                "Valas"                 => $data->valas_name,
                "Shipment Value"        => number_format($data->shipment_value, 2),
                "Shipment Value Net"    => number_format($data->shipment_value_net, 2)
            ];

            // Akumulasi total
            $totalQtyConvertion += $data->total_qty_convertion;
            $totalShipmentValue += $data->shipment_value;
            $totalShipmentValueNet += $data->shipment_value_net;
        }

        // Tambahkan baris GRAND TOTAL
        $dataResult[] = [
            "No"                    => '',
            "Acc Holder"            => '',
            "Order Form No"         => '',
            "Customer"              => '',
            "Container Number"      => '',
            "Actualy Shipment Date" => 'GRAND TOTAL',
            "Qty (Kg)"                => number_format($totalQtyConvertion, 2),
            "Valas"                 => '',
            "Shipment Value"        => number_format($totalShipmentValue, 2),
            "Shipment Value Net"    => number_format($totalShipmentValueNet, 2)
        ];

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tulis header
        $row = 1;
        $headers = array_keys($dataResult[0]);
        $col = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            // Auto size kolom
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Hitung kolom terakhir
        $lastCol = chr(ord('A') + count($headers) - 1);

        // Bold header
        $sheet->getStyle("A1:" . $lastCol . "1")->applyFromArray([
            'font' => ['bold' => true],
        ]);

        // Tulis data
        $row++;
        foreach ($dataResult as $item) {
            $col = 'A';
            foreach ($item as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Tambahkan border untuk semua data termasuk header
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        // Hitung baris terakhir
        $lastRow = $row - 1;
        $sheet->getStyle("A1:" . $lastCol . $lastRow)->applyFromArray($styleArray);

        // Export file
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function indexByItems()
    {
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );

        $dataBarangSales = $this->barangMasterSalesModel
            ->where('company_id', $this->this_company_id)
            ->where('type_barang_sales', "EKSPOR")
            ->where('deletedAt', null)
            ->orderBy('barang_name', "asc")
            ->findAll();

        $data = [
            'dataCustomer' => $dataCustomer,
            'dataBarangSales' => $dataBarangSales

        ];

        return view('SalesInternasional/Report/ReportItems/index', $data);
    }
}
