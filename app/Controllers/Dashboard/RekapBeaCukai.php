<?php

namespace App\Controllers\Dashboard;


use App\Controllers\BaseController;
use App\Controllers\PenjualanLain\SalesOrderLain;
use App\Models\CompaniesModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\CustomerModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderLainModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\UserModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrdersModel;
use App\Models\BC23Model;
use App\Models\BC25Model;
use App\Models\BC27Model;
use App\Models\BC30Model;
use App\Models\BC40Model;
use App\Models\BC41Model;

use App\Models\PPBKBModel;
use App\Models\PPBKBDetailModel;
use App\Models\MutasiGlobalModel;
use App\Models\MutasiGlobalDetailModel;
use App\Models\MutasiModel;
use App\Models\MutasiDetailModel;
use App\Models\SalesOrderExportDetailModel;
use PDO;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RekapBeaCukai extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function rekapBC23()
    {
        $companiesModel = new CompaniesModel();

        $companyName = $companiesModel
            ->asObject()
            ->select("company")
            ->where('id', $this->this_company_id)
            ->first();

        $data = [
            'company_name' => $companyName->company
        ];
        return view("Dashboard/rekapBeaCukai/rekapBC23", $data);
    }

    public function RekapBC23all()
    {
        $month = $this->request->getVar('date');
        $bc23Model = new BC23Model();
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $condition = [
            "bc_purchase_order.company_id"  => $this->this_company_id,
            "bc_purchase_order.deletedAt" => null,
            "bc_23.deletedAt" => null,
            "DATE_FORMAT(bc_23.createdAt, '%m/%Y')" => $month
        ];

        $selectQry = "bc_23.*,
        bc_purchase_order.multiple_po_no,
        bc_purchase_order.id AS bc_purchase_order_id,
        bc_purchase_order.multiple_lpb_no,
        bc_purchase_order.multiple_lpb_id,
        bc_purchase_order.po_type,
        bc_purchase_order.status_posting,
        bc_purchase_order.no_daftar,
        bc_purchase_order.po_type,
        suppliers.name AS supplier_name";

        $bcDataQry = $bc23Model
            ->select($selectQry)
            ->where($condition)
            ->whereIn('po_type', ["IMPORT BAKU", "IMPORT PENOLONG"])
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_23.bc_purchase_order_id', 'right')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->findAll();

        $list = [];

        foreach ($bcDataQry as $d) {
            $lpbidArr = json_decode($d['multiple_lpb_id']);
            $lpbnoArr = json_decode($d['multiple_lpb_no']);
            $poNoArr = json_decode($d['multiple_po_no']);
            $lpbString = implode(',', $lpbnoArr);
            $poString = implode(',', $poNoArr);


            $dataLPB = $penerimaanBarangDetailModel
                ->select('count(id) as jumlah_barang, sum(jml_masuk) as total_barang')
                ->whereIn('penerimaan_barang_id', $lpbidArr)
                ->first();

            array_push($list, [
                'no_order' => $poString,
                'no_aju' => $d['no_aju'],
                'no_dokumen' => $d['no_daftar'],
                'tgl_dokumen' => date('d/m/Y', strtotime($d['createdAt'])),
                'po_type' => $d['po_type'],
                'no_bukti' => $lpbString,
                'penerima' => $d['supplier_name'],
                'jumlah_barang' => $dataLPB['jumlah_barang'],
                'total_barang' => number_format($dataLPB['total_barang'], 2)
            ]);
        }

        return json_encode($list);
    }

    public function rekapBC23Sheet()
    {

        $list = json_decode($this->RekapBC23all());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Tipe PO')
            ->setCellValue('C1', 'No Purchase Order')
            ->setCellValue('D1', 'No Aju')
            ->setCellValue('E1', 'No Daftar')
            ->setCellValue('F1', 'Tgl Dokumen')
            ->setCellValue('G1', 'No Penerimaan Barang')
            ->setCellValue('H1', 'Supplier')
            ->setCellValue('I1', 'Jumlah Barang (Termasuk Spek)')
            ->setCellValue('J1', 'Total Barang');

        $no = 1;
        $column = 2;

        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->po_type)
                ->setCellValue('C' . $column,  $l->no_order)
                ->setCellValue('D' . $column,  $l->no_aju)
                ->setCellValue('E' . $column,  $l->no_dokumen)
                ->setCellValue('F' . $column,  $l->tgl_dokumen)
                ->setCellValue('G' . $column,  $l->no_bukti)
                ->setCellValue('H' . $column,  $l->penerima)
                ->setCellValue('I' . $column,  $l->jumlah_barang)
                ->setCellValue('J' . $column,  $l->total_barang);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC23';
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        ob_start();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer->save('php://output');

        // Clear the output buffer
        ob_end_flush();
        die;
    }

    public function rekapBC25()
    {
        $companiesModel = new CompaniesModel();

        $companyName = $companiesModel
            ->asObject()
            ->select("company")
            ->where('id', $this->this_company_id)
            ->first();

        $data = [
            'company_name' => $companyName->company
        ];
        return view("Dashboard/rekapBeaCukai/rekapBC25", $data);
    }
    public function RekapBC25all()
    {
        $month = $this->request->getVar('date');
        $bc25Model = new BC25Model();
        $salesOrderLainModel = new SalesOrderLainModel();
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();

        $condition = [
            "bc_25.company_id"  => $this->this_company_id,
            "bc_25.deletedAt" => null,
            "DATE_FORMAT(bc_25.createdAt, '%m/%Y')" => $month
        ];

        $selectQry = "bc_25.*,
        sales_order_lain.no_sales_order,
        divisis.divisi,
        warehouses.warehouse_name,
        customers.name AS customer_name";

        $bcDataQry = $bc25Model
            ->select($selectQry)
            ->join('sales_order_lain', 'sales_order_lain.id = bc_25.sales_order_lain_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->where($condition)
            ->findAll();



        $list = [];

        foreach ($bcDataQry as $d) {
            $dataSalesOrderLainDetail = $salesOrderLainDetailModel
                ->select('count(id) as jumlah_barang, sum(qty_order) as total_barang')
                ->where('sales_order_lain_id', $d['sales_order_lain_id'])
                ->first();
            $dataSalesOrderLain = $salesOrderLainModel
                ->select('no_sales_order')
                ->where('id', $d['sales_order_lain_id'])
                ->first();





            array_push($list, [
                'no_sales_order' => $dataSalesOrderLain['no_sales_order'],
                'no_aju' => $d['no_aju'],
                'no_dokumen' => $d['no_daftar'],
                'tgl_dokumen' => date('d/m/Y', strtotime($d['createdAt'])),
                'customer_nama' => $d['customer_name'],
                'jumlah_barang' => $dataSalesOrderLainDetail['jumlah_barang'],
                'total_barang' => number_format($dataSalesOrderLainDetail['total_barang'], 2)



            ]);
        }

        return json_encode($list);
    }
    public function rekapBC25Sheet()
    {

        $list = json_decode($this->RekapBC25all());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'No Sales Order')
            ->setCellValue('C1', 'No Aju')
            ->setCellValue('D1', 'No Daftar')
            ->setCellValue('E1', 'Tgl Dokumen')
            ->setCellValue('F1', 'Customer')
            ->setCellValue('G1', 'Jumlah Barang')
            ->setCellValue('H1', 'Total Barang');


        $no = 1;
        $column = 2;


        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->no_sales_order)
                ->setCellValue('C' . $column,  $l->no_aju)
                ->setCellValue('D' . $column,  $l->no_dokumen)
                ->setCellValue('E' . $column,  $l->tgl_dokumen)
                ->setCellValue('F' . $column,  $l->customer_nama)
                ->setCellValue('G' . $column,  $l->jumlah_barang)
                ->setCellValue('H' . $column,  $l->total_barang);

            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC23';
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        ob_start();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer->save('php://output');

        // Clear the output buffer
        ob_end_flush();
        die;
    }

    public function rekapBC27()
    {
        $companiesModel = new CompaniesModel();

        $companyName = $companiesModel
            ->asObject()
            ->select("company")
            ->where('id', $this->this_company_id)
            ->first();

        $data = [
            'company_name' => $companyName->company
        ];
        return view("Dashboard/rekapBeaCukai/rekapBC27", $data);
    }
    public function RekapBC27all()
    {
        $month = $this->request->getVar('date');
        $bc27Model = new BC27Model();
        $mutasiGlobalModel = new MutasiGlobalModel();
        $mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $companiesModel = new CompaniesModel();

        $condition = [
            "bc_27.company_asal_id"  => $this->this_company_id,
            "bc_27.deletedAt" => null,
            "mutasi_global.deletedAt" => null,
            "DATE_FORMAT(bc_27.createdAt, '%m/%Y')" => $month
        ];

        $selectQry = "bc_27.*,
            mutasi_global.no_mutasi,
            companies.company,
            divisis.divisi,
            warehouses.warehouse_name";

        $bcDataQry = $bc27Model
            ->select($selectQry)
            ->where($condition)
            ->join('mutasi_global', 'mutasi_global.id = bc_27.mutasi_global_id', 'left')
            ->join('companies', 'companies.id = bc_27.company_tujuan_id', 'left')
            ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi_global.warehouse_asal_id', 'left')
            ->findAll();

        $list = [];

        foreach ($bcDataQry as $d) {
            $dataMutasiGlobal = $mutasiGlobalModel
                ->select('no_mutasi')
                ->where('id', $d['mutasi_global_id'])
                ->first();
            $dataMutasiGlobalDetail = $mutasiGlobalDetailModel
                ->select('count(id) as jumlah_barang, sum(qty) as total_barang')
                ->where('mutasi_global_id', $d['mutasi_global_id'])
                ->first();
            $dataDokumen = $mutasiGlobalDetailModel
                ->select('stock_dokumen')
                ->where('mutasi_global_id', $d['mutasi_global_id'])
                ->findAll();

            $dataPengirim = $companiesModel
                ->select('company as pengirim')
                ->where('id', $d['company_asal_id'])
                ->first();
            $dataPenerima = $companiesModel
                ->select('company as penerima')
                ->where('id', $d['company_tujuan_id'])
                ->first();


            $stockDokumenArr = [];
            foreach ($dataDokumen as $data) {
                array_push($stockDokumenArr, $data['stock_dokumen']);
            }


            $stockDokumenArr = array_unique($stockDokumenArr);
            $stringStockDokumen = implode(" ", $stockDokumenArr);

            array_push($list, [
                'no_mutasi' => $dataMutasiGlobal['no_mutasi'],
                'no_aju' => $d['no_aju'],
                'no_stock_dokumen' => $stringStockDokumen,
                'no_dokumen' => $d['no_daftar'],
                'tgl_dokumen' => date('d/m/Y', strtotime($d['createdAt'])),
                'pengirim' => $dataPengirim['pengirim'],
                'penerima' => $dataPenerima['penerima'],
                'jumlah_barang' => $dataMutasiGlobalDetail['jumlah_barang'],
                'total_barang' => number_format($dataMutasiGlobalDetail['total_barang'], 2)

            ]);
        }

        return json_encode($list);
    }

    public function rekapBC27Sheet()
    {

        $list = json_decode($this->RekapBC27all());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'No Mutasi')
            ->setCellValue('C1', 'No Dokumen')
            ->setCellValue('D1', 'No Aju')
            ->setCellValue('E1', 'No Daftar')
            ->setCellValue('F1', 'Tgl Dokumen')
            ->setCellValue('G1', 'Pengirim')
            ->setCellValue('H1', 'Penerima')
            ->setCellValue('I1', 'Jumlah Barang')
            ->setCellValue('J1', 'Total Barang');



        $no = 1;
        $column = 2;


        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->no_mutasi)
                ->setCellValue('C' . $column,  $l->no_stock_dokumen)
                ->setCellValue('D' . $column,  $l->no_aju)
                ->setCellValue('E' . $column,  $l->no_dokumen)
                ->setCellValue('F' . $column,  $l->tgl_dokumen)
                ->setCellValue('G' . $column,  $l->pengirim)
                ->setCellValue('H' . $column,  $l->penerima)
                ->setCellValue('I' . $column,  $l->jumlah_barang)
                ->setCellValue('J' . $column,  $l->total_barang);

            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC27';
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        ob_start();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer->save('php://output');

        // Clear the output buffer
        ob_end_flush();
        die;
    }

    public function rekapBC30()
    {
        $companiesModel = new CompaniesModel();

        $companyName = $companiesModel
            ->asObject()
            ->select("company")
            ->where('id', $this->this_company_id)
            ->first();

        $data = [
            'company_name' => $companyName->company
        ];
        return view("Dashboard/rekapBeaCukai/rekapBC30", $data);
    }
    public function RekapBC30all()
    {
        $month = $this->request->getVar('date');
        $bc30Model = new BC30Model();
        $salesOrderModel = new SalesOrderModel();
        $salesOrderDetailModel = new SalesOrderDetailModel();
        $salesOrderExportModel = new SalesOrderExportModel();
        $salesOrderExportDetailModel = new SalesOrderExportDetailModel();


        $condition = [
            "bc_30.company_id"  => $this->this_company_id,
            "bc_30.deletedAt" => null,
            "DATE_FORMAT(bc_30.createdAt, '%m/%Y')" => $month
        ];

        $selectQry = "bc_30.*";

        $bcDataQry = $bc30Model
            ->select($selectQry)
            ->where($condition)
            ->findAll();


        $list = [];

        foreach ($bcDataQry as $d) {
            $noSalesOrder = "";
            $noStuffing = "";
            $jumlahBarang = "";
            $totalBarang = "";
            $penerima = "";
            if ($d['tipe_sales_order'] == "INTERNASIONAL") {
                $dataSalesOrderExport = $salesOrderExportModel
                    ->select('sales_order_export_no, no_stuffing, company')
                    ->join('stuffing_internasional', 'sales_order_export.sales_order_export_id = stuffing_internasional.sales_order_export_id')
                    ->join('companies', 'companies.id = sales_order_export.company_id')
                    ->where('sales_order_export.sales_order_export_id', $d['sales_order_id'])
                    ->first();
                $dataSalesOrderExportDetail = $salesOrderExportDetailModel
                    ->select('count(*) as jumlah_barang, sum(qty) as total_barang')
                    ->where('sales_order_export_id', $d['sales_order_id'])
                    ->first();
                $noSalesOrder = $dataSalesOrderExport['sales_order_export_no'];
                $noStuffing = $dataSalesOrderExport['no_stuffing'];
                $penerima = $dataSalesOrderExport['company'];
                $jumlahBarang = $dataSalesOrderExportDetail['jumlah_barang'];
                $totalBarang = $dataSalesOrderExportDetail['total_barang'];
            } elseif ($d['tipe_sales_order'] == "LOKAL") {
                $dataSalesOrder = $salesOrderModel
                    ->select('no_sales_order, customers.name, no_stuffing')
                    ->join('stuffing_lokal', 'stuffing_lokal.sales_order_id = sales_order.id')
                    ->join('customers', 'customers.id = sales_order.id_customer')
                    ->where('sales_order.id', $d['sales_order_id'])
                    ->first();
                $dataSalesOrderDetail = $salesOrderDetailModel
                    ->select('count(*) as jumlah_barang, sum(qty) as total_barang')
                    ->where('id_sales_order', $d['sales_order_id'])
                    ->first();
                $noSalesOrder = $dataSalesOrder['no_sales_order'];
                $noStuffing = $dataSalesOrder['no_stuffing'];
                $penerima = $dataSalesOrder['name'];
                $jumlahBarang = $dataSalesOrderDetail['jumlah_barang'];
                $totalBarang = $dataSalesOrderDetail['total_barang'];
            }

            array_push($list, [
                'tipe_sales_order' => $d['tipe_sales_order'],
                'no_sales_order' => $noSalesOrder,
                'no_aju' => $d['no_aju'],
                'no_dokumen' => $d['no_daftar'],
                'tgl_dokumen' => date('d/m/Y', strtotime($d['createdAt'])),
                'no_stuffing' => $noStuffing,
                'penerima' => $penerima,
                'jumlah_barang' => $jumlahBarang,
                'total_barang' => number_format($totalBarang, 2)
            ]);
        }

        return json_encode($list);
    }
    public function rekapBC30Sheet()
    {

        $list = json_decode($this->RekapBC30all());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Tipe Sales Order')
            ->setCellValue('C1', 'No Sales Order')
            ->setCellValue('D1', 'No Aju')
            ->setCellValue('E1', 'No Daftar')
            ->setCellValue('F1', 'Tgl Dokumen')
            ->setCellValue('G1', 'No Stuffing')
            ->setCellValue('H1', 'Penerima')
            ->setCellValue('I1', 'Jumlah Barang')
            ->setCellValue('J1', 'Total Barang');

        $no = 1;
        $column = 2;


        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->tipe_sales_order)
                ->setCellValue('C' . $column,  $l->no_sales_order)
                ->setCellValue('D' . $column,  $l->no_aju)
                ->setCellValue('E' . $column,  $l->no_dokumen)
                ->setCellValue('F' . $column,  $l->tgl_dokumen)
                ->setCellValue('G' . $column,  $l->no_stuffing)
                ->setCellValue('H' . $column,  $l->penerima)
                ->setCellValue('I' . $column,  $l->jumlah_barang)
                ->setCellValue('J' . $column,  $l->total_barang);

            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC23';
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        ob_start();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer->save('php://output');

        // Clear the output buffer
        ob_end_flush();
        die;
    }

    public function rekapBC40()
    {
        $companiesModel = new CompaniesModel();

        $companyName = $companiesModel
            ->asObject()
            ->select("company")
            ->where('id', $this->this_company_id)
            ->first();

        $data = [
            'company_name' => $companyName->company
        ];
        return view("Dashboard/rekapBeaCukai/rekapBC40", $data);
    }
    public function RekapBC40all()
    {
        $month = $this->request->getVar('date');
        $bc40Model = new BC40Model();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();

        $condition = [
            "bc_purchase_order.company_id"  => $this->this_company_id,
            "bc_purchase_order.deletedAt" => null,
            "bc_40.deletedAt" => null,
            "DATE_FORMAT(bc_40.createdAt, '%m/%Y')" => $month
        ];

        $selectQry = "bc_40.*,
        bc_purchase_order.multiple_po_no,
        bc_purchase_order.id AS bc_purchase_order_id,
        bc_purchase_order.multiple_lpb_no,
        bc_purchase_order.multiple_lpb_id,
        bc_purchase_order.po_type,
        bc_purchase_order.status_posting,
        bc_purchase_order.no_daftar,
        suppliers.name AS supplier_name";

        $bcDataQry = $bc40Model
            ->select($selectQry)
            ->where($condition)
            ->whereIn('po_type', ["LOKAL BAKU", "LOKAL PENOLONG"])
            ->join('bc_purchase_order', 'bc_purchase_order.id = bc_40.bc_purchase_order_id', 'right')
            ->join('suppliers', 'suppliers.id = bc_purchase_order.supplier_id', 'left')
            ->findAll();
        $list = [];

        foreach ($bcDataQry as $d) {
            $lpbidArr = json_decode($d['multiple_lpb_id']);
            $lpbnoArr = json_decode($d['multiple_lpb_no']);
            $poNoArr = json_decode($d['multiple_po_no']);
            $lpbString = implode(',', $lpbnoArr);
            $poString = implode(',', $poNoArr);


            $dataLPB = $penerimaanBarangDetailModel
                ->select('count(id) as jumlah_barang, sum(jml_masuk) as total_barang')
                ->whereIn('penerimaan_barang_id', $lpbidArr)
                ->first();

            array_push($list, [
                'no_order' => $poString,
                'no_aju' => $d['no_aju'],
                'no_dokumen' => $d['no_daftar'],
                'tgl_dokumen' => date('d/m/Y', strtotime($d['createdAt'])),
                'po_type' => $d['po_type'],
                'no_bukti' => $lpbString,
                'penerima' => $d['supplier_name'],
                'jumlah_barang' => $dataLPB['jumlah_barang'],
                'total_barang' => number_format($dataLPB['total_barang'], 2)
            ]);
        }

        return json_encode($list);
    }

    public function rekapBC40Sheet()
    {

        $list = json_decode($this->RekapBC40all());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Tipe PO')
            ->setCellValue('C1', 'No Purchase Order')
            ->setCellValue('D1', 'No Aju')
            ->setCellValue('E1', 'No Daftar')
            ->setCellValue('F1', 'Tgl Dokumen')
            ->setCellValue('G1', 'No Penerimaan Barang')
            ->setCellValue('H1', 'Penerima')
            ->setCellValue('I1', 'Jumlah Barang')
            ->setCellValue('J1', 'Total Barang');

        $no = 1;
        $column = 2;


        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->po_type)
                ->setCellValue('C' . $column,  $l->no_order)
                ->setCellValue('D' . $column,  $l->no_aju)
                ->setCellValue('E' . $column,  $l->no_dokumen)
                ->setCellValue('F' . $column,  $l->tgl_dokumen)
                ->setCellValue('G' . $column,  $l->no_bukti)
                ->setCellValue('H' . $column,  $l->penerima)
                ->setCellValue('I' . $column,  $l->jumlah_barang)
                ->setCellValue('J' . $column,  $l->total_barang);

            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC23';
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        ob_start();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer->save('php://output');

        // Clear the output buffer
        ob_end_flush();
        die;
    }

    public function rekapBC41()
    {
        $companiesModel = new CompaniesModel();

        $companyName = $companiesModel
            ->asObject()
            ->select("company")
            ->where('id', $this->this_company_id)
            ->first();

        $data = [
            'company_name' => $companyName->company
        ];
        return view("Dashboard/rekapBeaCukai/rekapBC41", $data);
    }
    public function RekapBC41all()
    {
        $month = $this->request->getVar('date');
        $bc41Model = new BC41Model();
        $salesOrderLainModel = new SalesOrderLainModel();
        $salesOrderLainDetailModel = new SalesOrderLainDetailModel();

        $condition = [
            "bc_41.company_id"  => $this->this_company_id,
            "bc_41.deletedAt" => null,
            "DATE_FORMAT(bc_41.createdAt, '%m/%Y')" => $month
        ];

        $selectQry = "bc_41.*,
        sales_order_lain.no_sales_order,
        divisis.divisi,
        warehouses.warehouse_name,
        customers.name AS customer_name";

        $bcDataQry = $bc41Model
            ->select($selectQry)
            ->join('sales_order_lain', 'sales_order_lain.id = bc_41.sales_order_lain_id', 'left')
            ->join('divisis', 'divisis.id = sales_order_lain.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = sales_order_lain.warehouse_id', 'left')
            ->join('customers', 'customers.id = sales_order_lain.customer_id', 'left')
            ->where($condition)
            ->findAll();



        $list = [];

        foreach ($bcDataQry as $d) {
            $dataSalesOrderLainDetail = $salesOrderLainDetailModel
                ->select('count(id) as jumlah_barang, sum(qty_order) as total_barang')
                ->where('sales_order_lain_id', $d['sales_order_lain_id'])
                ->first();
            $dataSalesOrderLain = $salesOrderLainModel
                ->select('no_sales_order')
                ->where('id', $d['sales_order_lain_id'])
                ->first();
            array_push($list, [
                'no_sales_order' => $dataSalesOrderLain['no_sales_order'],
                'no_aju' => $d['no_aju'],
                'no_dokumen' => $d['no_daftar'],
                'tgl_dokumen' => date('d/m/Y', strtotime($d['createdAt'])),
                'customer_nama' => $d['customer_name'],
                'jumlah_barang' => $dataSalesOrderLainDetail['jumlah_barang'],
                'total_barang' => number_format($dataSalesOrderLainDetail['total_barang'], 2)
            ]);
        }


        return json_encode($list);
    }
    public function rekapBC41Sheet()
    {

        $list = json_decode($this->RekapBC41all());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'No Sales Order')
            ->setCellValue('C1', 'No Aju')
            ->setCellValue('D1', 'No Daftar')
            ->setCellValue('E1', 'Tgl Dokumen')
            ->setCellValue('F1', 'Customer')
            ->setCellValue('G1', 'Jumlah Barang')
            ->setCellValue('H1', 'Total Barang');


        $no = 1;
        $column = 2;


        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->no_sales_order)
                ->setCellValue('C' . $column,  $l->no_aju)
                ->setCellValue('D' . $column,  $l->no_dokumen)
                ->setCellValue('E' . $column,  $l->tgl_dokumen)
                ->setCellValue('F' . $column,  $l->customer_nama)
                ->setCellValue('G' . $column,  $l->jumlah_barang)
                ->setCellValue('H' . $column,  $l->total_barang);

            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC23';
        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        ob_start();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer->save('php://output');

        // Clear the output buffer
        ob_end_flush();
        die;
    }

    public function rekapPPBKB()
    {
        $companiesModel = new CompaniesModel();

        $companyName = $companiesModel
            ->asObject()
            ->select("company")
            ->where('id', $this->this_company_id)
            ->first();

        $data = [
            'company_name' => $companyName->company
        ];
        return view("Dashboard/rekapBeaCukai/rekapPPBKB", $data);
    }
    public function RekapPPBKBall()
    {
        $month = $this->request->getVar('date');
        $ppbkbModel = new PPBKBModel();
        $mutasiModel = new MutasiModel();
        $mutasiDetailModel = new MutasiDetailModel();

        $condition = [
            "ppbkb.company_id"  => $this->this_company_id,
            "ppbkb.deletedAt" => null,
            "mutasi.deletedAt" => null,
            "DATE_FORMAT(ppbkb.createdAt, '%m/%Y')" => $month
        ];
        $selectQry = "ppbkb.*,
        mutasi.divisi_tujuan_id,
        mutasi.warehouse_tujuan_id,
        mutasi.no_mutasi,
        divisis.divisi AS divisi_asal_name,
        warehouses.warehouse_name AS warehouse_asal_name";

        $bcDataQry = $ppbkbModel
            ->select($selectQry)
            ->where($condition)
            ->join('mutasi', 'mutasi.id = ppbkb.mutasi_id', 'left')
            ->join('divisis', 'divisis.id = mutasi.divisi_asal_id', 'left')
            ->join('warehouses', 'warehouses.id = mutasi.warehouse_asal_id', 'left')
            ->findAll();



        $list = [];

        foreach ($bcDataQry as $d) {
            $dataMutasi = $mutasiModel
                ->select('no_mutasi')
                ->where('id', $d['mutasi_id'])
                ->first();
            $dataAsal = $mutasiModel
                ->select('divisi, warehouse_name')
                ->join('divisis', 'divisis.id = mutasi.divisi_asal_id')
                ->join('warehouses', 'warehouses.id = mutasi.warehouse_asal_id')
                ->where('mutasi.id', $d['mutasi_id'])
                ->first();
            $dataTujuan = $mutasiModel
                ->select('divisi, warehouse_name')
                ->join('divisis', 'divisis.id = mutasi.divisi_tujuan_id')
                ->join('warehouses', 'warehouses.id = mutasi.warehouse_tujuan_id')
                ->where('mutasi.id', $d['mutasi_id'])
                ->first();


            $dataMutasiDetail = $mutasiDetailModel
                ->select('count(id) as jumlah_barang, sum(qty) as total_barang')
                ->where('mutasi_id', $d['mutasi_id'])
                ->first();
            $dataDokumen = $mutasiDetailModel
                ->select('stock_dokumen')
                ->where('mutasi_id', $d['mutasi_id'])
                ->findAll();
            $stockDokumenArr = [];
            foreach ($dataDokumen as $data) {
                array_push($stockDokumenArr, $data['stock_dokumen']);
            }
            $stockDokumenArr = array_unique($stockDokumenArr);
            $stringStockDokumen = implode(" ", $stockDokumenArr);

            array_push($list, [
                'no_mutasi' => $dataMutasi['no_mutasi'],
                'no_aju' => $d['no_ppbkb'],
                'no_stock_dokumen' => $stringStockDokumen,
                'no_dokumen' => $d['no_daftar'],
                'tgl_dokumen' => date('d/m/Y', strtotime($d['createdAt'])),
                'jumlah_barang' => $dataMutasiDetail['jumlah_barang'],
                'asal' => $dataAsal['divisi'] . " / " . $dataAsal['warehouse_name'],
                'tujuan' => $dataTujuan['divisi'] . " / " . $dataAsal['warehouse_name'],
                'total_barang' => number_format($dataMutasiDetail['total_barang'], 2)
            ]);
        }

        return json_encode($list);
    }
    public function rekapPPBKBSheet()
    {

        $list = json_decode($this->RekapPPBKBall());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'No Mutasi')
            ->setCellValue('C1', 'No Dokumen')
            ->setCellValue('D1', 'No Aju')
            ->setCellValue('E1', 'No Daftar')
            ->setCellValue('F1', 'Tgl Dokumen')
            ->setCellValue('G1', 'Asal Divisi/Warehouse')
            ->setCellValue('H1', 'Tujuan Divisi/Warehouse')
            ->setCellValue('I1', 'Jumlah Barang')
            ->setCellValue('J1', 'Total Barang');

        $no = 1;
        $column = 2;


        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->no_mutasi)
                ->setCellValue('C' . $column,  $l->no_stock_dokumen)
                ->setCellValue('D' . $column,  $l->no_aju)
                ->setCellValue('E' . $column,  $l->no_dokumen)
                ->setCellValue('F' . $column,  $l->tgl_dokumen)
                ->setCellValue('G' . $column,  $l->asal)
                ->setCellValue('H' . $column,  $l->tujuan)
                ->setCellValue('I' . $column,  $l->jumlah_barang)
                ->setCellValue('J' . $column,  $l->total_barang);

            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC23';
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        ob_start();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output the file
        $writer->save('php://output');

        // Clear the output buffer
        ob_end_flush();
        die;
    }
}
