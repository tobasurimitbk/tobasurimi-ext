<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CustomerModel;
use App\Models\SalesKontrakDetailModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportModel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportEkspor extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $customerModel;
    protected $salesOrderExportModel;
    protected $barangMasterSalesModel;
    protected $salesKontrakDetailModel;
    protected $salesOrderExportDetailModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->salesKontrakDetailModel = new SalesKontrakDetailModel();
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
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


    public function exportExcelByCustomer()
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
                "Qty (Kg)"              => (float) $data->total_qty_convertion,
                "Valas"                 => $data->valas_name,
                "Shipment Value"        => (float) $data->shipment_value,
                "Shipment Value Net"    => (float) $data->shipment_value_net
            ];

            $totalQtyConvertion += $data->total_qty_convertion;
            $totalShipmentValue += $data->shipment_value;
            $totalShipmentValueNet += $data->shipment_value_net;
        }

        $dataResult[] = [
            "No"                    => '',
            "Acc Holder"            => '',
            "Order Form No"         => '',
            "Customer"              => '',
            "Container Number"      => '',
            "Actualy Shipment Date" => 'GRAND TOTAL',
            "Qty (Kg)"              => (float) $totalQtyConvertion,
            "Valas"                 => '',
            "Shipment Value"        => (float) $totalShipmentValue,
            "Shipment Value Net"    => (float) $totalShipmentValueNet
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        $headers = array_keys($dataResult[0]);
        $col = 'A';
        $cols = [];

        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $cols[] = $col;
            $col++;
        }

        $lastCol = end($cols);
        $sheet->getStyle("A1:" . $lastCol . "1")->applyFromArray([
            'font' => ['bold' => true],
        ]);

        $row++;

        foreach ($dataResult as $item) {
            foreach ($headers as $i => $header) {
                $cell = $cols[$i] . $row;
                $value = $item[$header] ?? '';

                // Format kolom numerik
                if (in_array($header, ['Qty (Kg)', 'Shipment Value', 'Shipment Value Net']) && is_numeric($value)) {
                    $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_NUMERIC);
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $sheet->setCellValue($cell, $value);
                }
            }
            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle("A1:" . $lastCol . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

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

    public function allByItems()
    {

        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = [
            'sales_contract_detail.deletedAt' => null,
            'sales_order_detail_export.deletedAt' => null,
            'sales_contract.company_id' => $this->this_company_id,
            'sales_order_export.status' => 'POSTED'
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "customer_id"              => $this->request->getGet("customer_id"),
            "barang_master_sales_id"   => $this->request->getGet("barang_master_sales_id"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportDetailModel->getListExportByItems($condition, $addCondition, $limit, $offset);

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
                "dicharge_port"             => $data->dicharge_port,
                "barang_name"               => $data->barang_name,
                "total_qty_convertion"      => $data->total_qty_convertion,
                "valas"                     => $data->valas_name,
                "amount_value"              => $data->total_harga_barang,
                "price_type"                => $data->tipe_harga,
            ]);
        }

        $footerTotals = [
            'totalQtyConvertion' => $salesData['totalQtyConvertion'],
            'amountValue' => $salesData['amountValue'],
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


    public function exportExcelByItems()
    {
        $condition = [
            'sales_order_export.company_id' => $this->this_company_id,
            'sales_order_export.deletedAt' => null,
            'sales_order_export.status' => 'POSTED'
        ];
        $addCondition = [
            "search"                => $this->request->getGet("search"),
            "sort"                  => $this->request->getGet("sort"),
            "sortType"              => $this->request->getGet("sortType"),
            "customer_id"           => $this->request->getGet("customer_id"),
            "barang_master_sales_id" => $this->request->getVar('barang_master_sales_id'),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];
        $filename = "Report Ekspor By Items " . $addCondition['dateStart'] . " - " . $addCondition['dateEnd'];
        $salesOrderExport = $this->salesOrderExportDetailModel->getListExportByItems($condition, $addCondition, 100000000, 0);

        // Group data by product
        $grouped = [];
        foreach ($salesOrderExport['data'] as $d) {
            $grouped[$d->barang_name][] = $d;
        }

        $dataRows = [];
        $no = 1;
        $grandQty = 0;
        $grandAmt = 0;

        foreach ($grouped as $productName => $items) {
            $subQty = 0;
            $subAmt = 0;

            $dataRows[] = ['group_header' => strtoupper($productName)];

            foreach ($items as $d) {
                $dataRows[] = [
                    "No"                    => $no++,
                    "Acc Holder"            => $d->acc_holder,
                    "Order Form No"         => $d->sales_order_export_no,
                    "Customer"              => $d->customer_name,
                    "Container Number"      => $d->container,
                    "Actualy Shipment Date" => date('d/m/Y', strtotime($d->actualy_shipment_date)),
                    "Destination"           => $d->dicharge_port,
                    "Product"               => $d->barang_name,
                    "Qty (Kg)"              => (float) $d->total_qty_convertion,
                    "Valas"                 => $d->valas_name,
                    "Amount Value"          => (float) $d->total_harga_barang,
                    "Price Type"            => $d->tipe_harga,
                ];
                $subQty += $d->total_qty_convertion;
                $subAmt += $d->total_harga_barang;
            }

            $dataRows[] = [
                "subtotal" => true,
                "Product" => 'TOTAL',
                "Qty (Kg)" => $subQty,
                "Amount Value" => $subAmt,
            ];
            $grandQty += $subQty;
            $grandAmt += $subAmt;
        }

        $dataRows[] = [
            "subtotal" => true,
            "Product" => 'GRAND TOTAL',
            "Qty (Kg)" => $grandQty,
            "Amount Value" => $grandAmt,
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            "No",
            "Acc Holder",
            "Order Form No",
            "Customer",
            "Container Number",
            "Actualy Shipment Date",
            "Destination",
            "Product",
            "Qty (Kg)",
            "Valas",
            "Amount Value",
            "Price Type"
        ];
        $cols = range('A', 'L');
        $row = 1;

        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . $row, $h);
            $sheet->getColumnDimension($cols[$i])->setAutoSize(true);
        }

        $sheet->getStyle("A1:L1")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        $row++;
        foreach ($dataRows as $item) {
            if (isset($item['group_header'])) {
                $sheet->mergeCells("A{$row}:L{$row}");
                $sheet->setCellValue("A{$row}", $item['group_header']);
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);
            } elseif (!empty($item['subtotal'])) {
                $sheet->setCellValue("H{$row}", $item['Product']);
                $sheet->setCellValueExplicit("I{$row}", $item['Qty (Kg)'], DataType::TYPE_NUMERIC);
                $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                $sheet->setCellValueExplicit("K{$row}", $item['Amount Value'], DataType::TYPE_NUMERIC);
                $sheet->getStyle("K{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                $sheet->getStyle("H{$row}:K{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
            } else {
                foreach ($headers as $i => $h) {
                    $value = $item[$h] ?? '';
                    if (in_array($h, ['Qty (Kg)', 'Amount Value']) && is_numeric($value)) {
                        $sheet->setCellValueExplicit($cols[$i] . $row, $value, DataType::TYPE_NUMERIC);
                        $sheet->getStyle($cols[$i] . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    } else {
                        $sheet->setCellValue($cols[$i] . $row, $value);
                    }
                }
            }
            $row++;
        }

        $last = $row - 1;
        $sheet->getStyle("A1:L{$last}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
        ]);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
