<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CompaniesModel;
use App\Models\CustomerModel;
use App\Models\SalesKontrakDetailModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\UserModel;
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
    protected $userModel;
    protected $companyModel;

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
        $this->companyModel = new CompaniesModel();
        $this->userModel = new UserModel();
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

        $dataAccHolder = $this->salesOrderExportModel->getAccHolder();
        $dataCompany = $this->companyModel->where('deletedAt', null)->findAll();

        $data = [
            'dataCustomer' => $dataCustomer,
            "dataAccHolder" => $dataAccHolder,
            "dataCompany" => $dataCompany
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
            "company_id"    => $this->request->getGet('company_id'),
            "user_id"       => $this->request->getGet('user_id'),
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
                "sales_contract_id"         => encrypt($data->sales_contract_id),
                "acc_holder"                => $data->acc_holder,
                "sales_order_export_no"     => $data->sales_order_export_no,
                "customer_name"             => $data->customer_name,
                "container"                 => $data->container,
                "actualy_shipment_date"     => !empty($data->actualy_shipment_date) ? date('d/m/Y', strtotime($data->actualy_shipment_date)) : "",
                "deadline"                  => $data->deadline,
                "total_qty_convertion"      => $data->total_qty_convertion,
                "company"                   => $data->company,
                "valas"                     => $data->valas_name,
                "shipment_value"            => $data->shipment_value,
                "shipment_value_net"        => $data->shipment_value_net,
                "total_qty"                 => number_format(floatval($data->total_qty), 2) . " " . $data->kode_satuan,
                "tipe_harga"                => $data->tipe_harga,
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
            "company_id"    => $this->request->getGet('company_id'),
            "user_id"       => $this->request->getGet('user_id'),
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
                "Container"             => $data->container,
                "Actualy Shipment Date" =>  !empty($data->actualy_shipment_date) ? date('d/m/Y', strtotime($data->actualy_shipment_date)) : "",
                "Deadline"              => $data->deadline,
                "Qty (Kg)"              => (float) $data->total_qty_convertion,
                "Plant"                 => $data->company,
                "Valas"                 => $data->valas_name,
                "Amount"                => (float) $data->shipment_value,
                "Amount Net"            => (float) $data->shipment_value_net,
                "Price Type"            => $data->tipe_harga,
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
            "Container"             => '',
            "Actualy Shipment Date" => 'GRAND TOTAL',
            "Qty (Kg)"              => (float) $totalQtyConvertion,
            "Plant"                 => '',
            "Valas"                 => '',
            "Amount"                => (float) $totalShipmentValue,
            "Amount Net"            => (float) $totalShipmentValueNet,
            "Price Type"            => ''
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
                if (in_array($header, ['Qty (Kg)', 'Amount', 'Amount Net']) && is_numeric($value)) {
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

        $dataAccHolder = $this->salesOrderExportModel->getAccHolder();
        $dataCompany = $this->companyModel->where('deletedAt', null)->findAll();

        $data = [
            'dataCustomer' => $dataCustomer,
            'dataBarangSales' => $dataBarangSales,
            "dataAccHolder" => $dataAccHolder,
            "dataCompany" => $dataCompany
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
            "user_id"       => $this->request->getVar('user_id'),
            "company_id"    => $this->request->getVar('company_id'),
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
                "sales_contract_id"         => encrypt($data->sales_contract_id),
                "acc_holder"                => $data->acc_holder,
                "sales_order_export_no"     => $data->sales_order_export_no,
                "customer_name"             => $data->customer_name,
                "container"                 => $data->container,
                "actualy_shipment_date"     => !empty($data->actualy_shipment_date) ? date('d/m/Y', strtotime($data->actualy_shipment_date)) : "",
                "deadline"                  => $data->deadline,
                "dicharge_port"             => $data->dicharge_port,
                "company"                   => $data->company,
                "barang_name"               => $data->barang_name,
                "total_qty" => (fmod($data->total_qty, 1) != 0
                    ? number_format($data->total_qty, 2) . " " . $data->kode_satuan
                    : number_format($data->total_qty, 0) . " " . $data->kode_satuan),
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
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "customer_id" => $this->request->getGet("customer_id"),
            "barang_master_sales_id" => $this->request->getVar('barang_master_sales_id'),
            "user_id" => $this->request->getVar('user_id'),
            "company_id" => $this->request->getVar('company_id'),
            "dateStart" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $filename = "Report Ekspor By Items " . $addCondition['dateStart'] . " - " . $addCondition['dateEnd'];
        $salesOrderExport = $this->salesOrderExportDetailModel->getListExportByItems($condition, $addCondition, 100000000, 0);

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
                    "No" => $no++,
                    "Acc Holder" => $d->acc_holder,
                    "Order Form No" => $d->sales_order_export_no,
                    "Customer" => $d->customer_name,
                    "Container Number" => $d->container,
                    "Actualy Shipment Date" => !empty($d->actualy_shipment_date) ? date('d/m/Y', strtotime($d->actualy_shipment_date)) : "",
                    "Deadline" => $d->deadline,
                    "Destination" => $d->dicharge_port,
                    "Plant" => $d->company,
                    "Product" => $d->barang_name,
                    "Qty OF" => $d->total_qty,
                    "Unit OF" => $d->kode_satuan,
                    "Qty (Kg)" => (float)$d->total_qty_convertion,
                    "Valas" => $d->valas_name,
                    "Amount" => (float)$d->total_harga_barang,
                    "Price Type" => $d->tipe_harga,
                ];
                $subQty += $d->total_qty_convertion;
                $subAmt += $d->total_harga_barang;
            }

            $dataRows[] = [
                "subtotal" => true,
                "Product" => 'TOTAL',
                "Qty (Kg)" => $subQty,
                "Amount" => $subAmt,
            ];

            $grandQty += $subQty;
            $grandAmt += $subAmt;
        }

        $dataRows[] = [
            "subtotal" => true,
            "Product" => 'GRAND TOTAL',
            "Qty (Kg)" => $grandQty,
            "Amount" => $grandAmt,
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
            "Deadline",
            "Destination",
            "Plant",
            "Product",
            "Qty OF",
            "Unit OF",
            "Qty (Kg)",
            "Valas",
            "Amount",
            "Price Type"
        ];

        $cols = range('A', chr(ord('A') + count($headers) - 1));
        $row = 1;

        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . $row, $h);
            $sheet->getColumnDimension($cols[$i])->setAutoSize(true);
        }

        $sheet->getStyle("A1:" . end($cols) . "1")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        $row++;
        foreach ($dataRows as $item) {
            if (isset($item['group_header'])) {
                $sheet->mergeCells("A{$row}:" . end($cols) . "{$row}");
                $sheet->setCellValue("A{$row}", $item['group_header']);
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);
            } elseif (!empty($item['subtotal'])) {
                // Merge kolom Product (J) sampai kolom sebelum Qty (Kg) (L)
                $sheet->mergeCells("J{$row}:L{$row}");
                $sheet->setCellValue("J{$row}", $item['Product']);
                $sheet->getStyle("J{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Qty (Kg) di M
                $sheet->setCellValueExplicit("M{$row}", $item['Qty (Kg)'], DataType::TYPE_NUMERIC);
                $sheet->getStyle("M{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                // Amount di O
                $sheet->setCellValueExplicit("O{$row}", $item['Amount'], DataType::TYPE_NUMERIC);
                $sheet->getStyle("O{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                // Style Qty & Amount
                $sheet->getStyle("M{$row}:O{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
            } else {
                foreach ($headers as $i => $h) {
                    $value = $item[$h] ?? '';
                    $cell = $cols[$i] . $row;

                    if (in_array($h, ['Qty (Kg)', 'Amount', 'Qty OF']) && is_numeric($value)) {
                        $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_NUMERIC);
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0.00');
                    } else {
                        $sheet->setCellValue($cell, $value);
                    }
                }
            }
            $row++;
        }

        $last = $row - 1;
        $sheet->getStyle("A1:" . end($cols) . "{$last}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
        ]);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function indexAccountHolder()
    {
        return view('SalesInternasional/Report/ReportAccountHolder/index');
    }

    public function allByAccountHolder()
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
            "year"       => $this->request->getGet('year'),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportDetailModel->getListExportByAccountHolder($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            array_push($dataSales, [
                "no"                                => $no++,
                "id"                                => encrypt($data->user_id),
                "acc_holder"                        => $data->acc_holder,
                "total_qty_convertion"              => $data->total_qty_convertion,
                "total_harga_barang"                => $data->total_harga_barang,
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

    public function detailByAccountHolder($userId)
    {
        $userId = decrypt($userId);
        $user = $this->userModel->where('id', $userId)->first();

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

        $dataAccHolder = $this->salesOrderExportModel->getAccHolder();
        $dataCompany = $this->companyModel->where('deletedAt', null)->findAll();

        $data = [
            'dataCustomer' => $dataCustomer,
            'dataBarangSales' => $dataBarangSales,
            "dataAccHolder" => $dataAccHolder,
            "dataCompany" => $dataCompany,
            'user' => $user
        ];

        return view('SalesInternasional/Report/ReportAccountHolder/detail_popup', $data);
    }

    public function exportExcelByAccountHolder()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
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
            "year"       => $this->request->getGet('year'),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $salesData = $this->salesOrderExportDetailModel->getListExportByAccountHolder($condition, $addCondition, 100000000, 0);

        $dataSales = [];

        $no =  1;

        foreach ($salesData['data'] as $data) {
            array_push($dataSales, [
                "no"                                => $no++,
                "id"                                => encrypt($data->user_id),
                "acc_holder"                        => $data->acc_holder,
                "total_qty_convertion"              => $data->total_qty_convertion,
                "total_harga_barang"                => $data->total_harga_barang,
            ]);
        }

        $footerTotals = [
            'totalQtyConvertion' => $salesData['totalQtyConvertion'],
            'amountValue' => $salesData['amountValue'],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $headers = ['No', 'Account Holder', 'Total Qty (Kg)', 'Total Amount'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Isi data
        $row = 2;
        foreach ($dataSales as $entry) {
            $sheet->setCellValue('A' . $row, $entry['no']);
            $sheet->setCellValue('B' . $row, $entry['acc_holder']);
            $sheet->setCellValue('C' . $row, $entry['total_qty_convertion']);
            $sheet->setCellValue('D' . $row, $entry['total_harga_barang']);
            $row++;
        }

        // Footer total
        $sheet->setCellValue('B' . $row, 'Total');
        $sheet->setCellValue('C' . $row, $footerTotals['totalQtyConvertion']);
        $sheet->setCellValue('D' . $row, $footerTotals['amountValue']);

        // Bold dan border total row
        $styleArray = [
            'font' => ['bold' => true],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($styleArray);

        // Tambahkan border ke semua data
        $dataRange = 'A1:D' . $row;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto width
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format number (jika ingin ribuan pakai ,)
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        // Download
        $filename = 'Export_Account_Holder_' . $addCondition['year'] . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
