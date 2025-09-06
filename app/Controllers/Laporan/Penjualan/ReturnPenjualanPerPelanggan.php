<?php

namespace App\Controllers\Laporan\Penjualan;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SalesOrderReturnModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReturnPenjualanPerPelanggan extends BaseController
{
    protected $this_company_id;
    protected $customerModel;
    protected $salesOrderReturnModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->customerModel = new CustomerModel();
        $this->salesOrderReturnModel = new SalesOrderReturnModel();
    }

    public function index()
    {
        $customerData = $this->customerModel->asObject()->where([
            'deletedAt' => null,
            'tipe_customer' => 'LOKAL'
        ])->orderBy('name', 'ASC')->findAll();
        $data = [
            'customer' => $customerData
        ];
        return view('Laporan/LaporanSales/LaporanReturnPerPelanggan/index', $data);
    }

    public function allTransaksi()
    {
        $pageSize = $this->request->getGet("length");
        $currentPage = ($this->request->getGet("start") / $pageSize) + 1;
        $offset = ($currentPage - 1) * $pageSize;

        $condition = [
            "sales_order_return.deletedAt" => null,
            "sales_order_return_detail.deletedAt" => null
        ];

        $addCondition = [
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "filter_customer" => $this->request->getGet("filter"),
            "dateStart" => $this->request->getGet("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateStart")))) : "",
            "dateEnd" => $this->request->getGet("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getGet("dateEnd")))) : "",
        ];

        $dataSalesOrderReturn = $this->salesOrderReturnModel
            ->getAllSalesOrderReturnLokal($condition, $addCondition, $pageSize, $offset);

        $dataAllSalesOrderReturn = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = ($pageSize * ($currentPage - 1)) + 1;

        foreach ($dataSalesOrderReturn['data'] as $data) {
            if ($currentCustomer !== $data->nama_pelanggan) {
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderReturn, [
                        "no" => '',
                        "id" => '',
                        "no_return" => 'Total Return: Rp ' . number_format(floatval($totalPerCustomer)),
                        "no_dokumen" => '',
                        "tanggal_return" => '',
                        "note" => '',
                        "sum_amount_return" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                    ]);
                }

                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;

                array_push($dataAllSalesOrderReturn, [
                    "no" => '',
                    "id" => '',
                    "no_return" => $data->nama_pelanggan,
                    "no_dokumen" => '',
                    "tanggal_return" => '',
                    "note" => '',
                    "sum_amount_return" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                ]);
            }

            array_push($dataAllSalesOrderReturn, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "no_return" => $data->no_return,
                "no_dokumen" => $data->no_dokumen,
                "tanggal_return" => $data->tanggal_return,
                "note" => $data->note,
                "sum_amount_return" => number_format(floatval($data->sum_amount_return)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->nama_sales ?? '-',
            ]);

            $totalPerCustomer += floatval($data->sum_amount_return);
        }

        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderReturn, [
                "no" => '',
                "id" => '',
                "no_return" => 'Total Return: Rp ' . number_format(floatval($totalPerCustomer)),
                "no_dokumen" => '',
                "tanggal_return" => '',
                "note" => '',
                "sum_amount_return" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
            ]);
        }

        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $dataSalesOrderReturn['totalData'],
            "recordsFiltered" => $dataSalesOrderReturn['totalFilteredData'],
            "data" => $dataAllSalesOrderReturn,
        ];

        echo json_encode($data);
        return;
    }

    public function printPDF($tglAwal, $tglAkhir, $filter, $search)
    {
        $dompdf = new Dompdf();

        $condition = [
            "sales_order_return.deletedAt" => null,
            "sales_order_return_detail.deletedAt" => null
        ];

        $addCondition = [
            "search" => $search == "all" ? null : $search,
            "filter_customer" => $filter == "all" ? null : $filter,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        $dataSalesOrderReturn = $this->salesOrderReturnModel
            ->getAllSalesOrderReturnLokal($condition, $addCondition, null, null);

        $dataAllSalesOrderReturn = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = 1;

        foreach ($dataSalesOrderReturn['data'] as $data) {
            if ($currentCustomer !== $data->nama_pelanggan) {
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderReturn, [
                        "no" => '',
                        "no_return" => 'Total Return: Rp ' . number_format(floatval($totalPerCustomer)),
                        "no_dokumen" => '',
                        "tanggal_return" => '',
                        "note" => '',
                        "sum_amount_return" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                    ]);
                }

                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;

                array_push($dataAllSalesOrderReturn, [
                    "no" => '',
                    "no_return" => $data->nama_pelanggan,
                    "no_dokumen" => '',
                    "tanggal_return" => '',
                    "note" => '',
                    "sum_amount_return" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                ]);
            }

            array_push($dataAllSalesOrderReturn, [
                "no" => $no++,
                "no_return" => $data->no_return,
                "no_dokumen" => $data->no_dokumen,
                "tanggal_return" => $data->tanggal_return,
                "note" => $data->note,
                "sum_amount_return" => number_format(floatval($data->sum_amount_return)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->nama_sales ?? '-',
            ]);

            $totalPerCustomer += floatval($data->sum_amount_return);
        }

        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderReturn, [
                "no" => '',
                "no_return" => 'Total Return: Rp ' . number_format(floatval($totalPerCustomer)),
                "no_dokumen" => '',
                "tanggal_return" => '',
                "note" => '',
                "sum_amount_return" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
            ]);
        }

        $data = [
            "data" => $dataAllSalesOrderReturn,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" => $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        $dompdf->loadHtml(view('Laporan/LaporanSales/LaporanReturnPerPelanggan/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan Return Penjualan Per Pelanggan", array("Attachment" => false));
        exit(0);
    }

    public function printExcel($tglAwal, $tglAkhir, $filter, $search)
    {
        $condition = [
            "sales_order_return.deletedAt" => null,
            "sales_order_return_detail.deletedAt" => null
        ];

        $addCondition = [
            "search" => $search == "all" ? null : $search,
            "filter_customer" => $filter == "all" ? "" : $filter,
            "dateStart" => $tglAwal != "all" ? date("Y-m-d", strtotime($tglAwal)) : "",
            "dateEnd" => $tglAkhir != "now" ? date("Y-m-d", strtotime($tglAkhir)) : "",
        ];

        $dataSalesOrderReturn = $this->salesOrderReturnModel
            ->getAllSalesOrderReturnLokal($condition, $addCondition, null, null);

        $dataAllSalesOrderReturn = [];
        $currentCustomer = null;
        $totalPerCustomer = 0;
        $no = 1;

        foreach ($dataSalesOrderReturn['data'] as $data) {
            if ($currentCustomer !== $data->nama_pelanggan) {
                if ($currentCustomer !== null) {
                    array_push($dataAllSalesOrderReturn, [
                        "no" => '',
                        "no_return" => 'Total Return: Rp ' . number_format(floatval($totalPerCustomer)),
                        "no_dokumen" => '',
                        "tanggal_return" => '',
                        "note" => '',
                        "sum_amount_return" => '',
                        "nama_pelanggan" => '',
                        "nama_sales" => '',
                        "is_total" => true,
                    ]);
                }

                $currentCustomer = $data->nama_pelanggan;
                $totalPerCustomer = 0;

                array_push($dataAllSalesOrderReturn, [
                    "no" => '',
                    "no_return" => $data->nama_pelanggan,
                    "no_dokumen" => '',
                    "tanggal_return" => '',
                    "note" => '',
                    "sum_amount_return" => '',
                    "nama_pelanggan" => '',
                    "nama_sales" => '',
                    "is_customer" => true,
                ]);
            }

            array_push($dataAllSalesOrderReturn, [
                "no" => $no++,
                "no_return" => $data->no_return,
                "no_dokumen" => $data->no_dokumen,
                "tanggal_return" => $data->tanggal_return,
                "note" => $data->note,
                "sum_amount_return" => number_format(floatval($data->sum_amount_return)),
                "nama_pelanggan" => $data->nama_pelanggan,
                "nama_sales" => $data->nama_sales ?? '-',
            ]);

            $totalPerCustomer += floatval($data->sum_amount_return);
        }

        if ($currentCustomer !== null) {
            array_push($dataAllSalesOrderReturn, [
                "no" => '',
                "no_return" => 'Total Return: Rp ' . number_format(floatval($totalPerCustomer)),
                "no_dokumen" => '',
                "tanggal_return" => '',
                "note" => '',
                "sum_amount_return" => '',
                "nama_pelanggan" => '',
                "nama_sales" => '',
                "is_total" => true,
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getProperties()
            ->setCreator("Your System")
            ->setLastModifiedBy("Your System")
            ->setTitle("Return Penjualan Per Pelanggan")
            ->setSubject("Return Penjualan Per Pelanggan")
            ->setDescription("Return Penjualan Per Pelanggan")
            ->setKeywords("laporan return penjualan pelanggan")
            ->setCategory("Laporan");

        $sheet->setCellValue('A1', 'TOBA FISH');
        $sheet->setCellValue('A2', 'LAPORAN RETURN PENJUALAN PER PELANGGAN');
        $sheet->setCellValue('A3', 'Periode: ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . ' - ' . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"));
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');

        $sheet->getStyle('A1:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:G3')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'No. Return');
        $sheet->setCellValue('C5', 'No. Dokumen');
        $sheet->setCellValue('D5', 'Tanggal Return');
        $sheet->setCellValue('E5', 'Keterangan');
        $sheet->setCellValue('F5', 'Jumlah');
        $sheet->setCellValue('G5', 'Nama Pelanggan');
        $sheet->setCellValue('H5', 'Nama Penjual');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']]
        ];
        $sheet->getStyle('A5:H5')->applyFromArray($headerStyle);

        $row = 6;
        foreach ($dataAllSalesOrderReturn as $item) {
            if (isset($item['is_customer']) && $item['is_customer']) {
                $sheet->setCellValue('A' . $row, $item['no_return']);
                $sheet->mergeCells('A' . $row . ':H' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE6E6E6');
            } elseif (isset($item['is_total']) && $item['is_total']) {
                $sheet->setCellValue('A' . $row, $item['no_return']);
                $sheet->mergeCells('A' . $row . ':H' . $row);
                $sheet->getStyle('A' . $row . ':H' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':H' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
            } else {
                $sheet->setCellValue('A' . $row, $item['no']);
                $sheet->setCellValue('B' . $row, $item['no_return']);
                $sheet->setCellValue('C' . $row, $item['no_dokumen']);
                $sheet->setCellValue('D' . $row, $item['tanggal_return']);
                $sheet->setCellValue('E' . $row, $item['note']);
                $sheet->setCellValue('F' . $row, $item['sum_amount_return']);
                $sheet->setCellValue('G' . $row, $item['nama_pelanggan']);
                $sheet->setCellValue('H' . $row, $item['nama_sales']);
            }
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(20);

        $lastRow = $row - 1;
        $sheet->getStyle('A5:H' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getStyle('F6:F' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $filename = "Laporan_Return_Penjualan_Per_Pelanggan_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
