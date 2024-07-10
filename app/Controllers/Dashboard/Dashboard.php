<?php

namespace App\Controllers\Dashboard;


use App\Controllers\BaseController;

use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderLainModel;
use PDO;

class Dashboard extends BaseController
{
    public function dashboard()
    {
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $rmImportPOModel = new RMImportPOModel();
        $salesOrderModel = new SalesOrderModel();
        $salesOrderExportModel = new SalesOrderExportModel();
        $salesOrderLainModel = new SalesOrderLainModel();

        $jumlah_pembayaran_belum_posting = 0;
        $jumlah_sales_bulan_ini = 0;

        $selectQry = "sum(is_posted = 0) as total_belum_post";

        $jumlah_is_not_posted_rm_purchase = $rmPurchaseOrderModel->select($selectQry)->first();
        $jumlah_is_not_posted_am_purchase = $amPurchaseOrderModel->select($selectQry)->first();
        $jumlah_is_not_posted_rm_import = $rmImportPOModel->select($selectQry)->first();

        $jumlah_pembayaran_belum_posting = intval($jumlah_is_not_posted_rm_purchase['total_belum_post']) + intval($jumlah_is_not_posted_am_purchase['total_belum_post'])
            + intval($jumlah_is_not_posted_rm_import['total_belum_post']);

        $month = date('m');
        $jumlah_sales_order_bulan_ini = $salesOrderModel
            ->select("count(*) as total_bulan_ini")
            ->where("DATE_FORMAT(createdAt, '%m')", $month)
            ->first();

        $jumlah_sales_order_export_bulan_ini = $salesOrderExportModel
            ->select("count(*) as total_bulan_ini")
            ->where("DATE_FORMAT(createdAt, '%m')", $month)
            ->first();
        $jumlah_sales_order_lain_bulan_ini = $salesOrderLainModel
            ->select("count(*) as total_bulan_ini")
            ->where("DATE_FORMAT(createdAt, '%m')", $month)
            ->first();

        $jumlah_sales_bulan_ini =  $jumlah_sales_order_bulan_ini['total_bulan_ini'] +  $jumlah_sales_order_export_bulan_ini['total_bulan_ini']
            + $jumlah_sales_order_lain_bulan_ini['total_bulan_ini'];

        $data = [
            'jumlah_pembayaran' => $jumlah_pembayaran_belum_posting,
            'jumlah_sales_bulan_ini' => $jumlah_sales_bulan_ini
        ];
        return view('Dashboard/dashboard/index', $data);
    }

    public function toggleSidebar()
    {
        $session = session();
        $toggle = $session->get('toggle');
        if (empty($toggle)) {
            $session->set('toggle', "sidebar-mini");
        } elseif ($toggle == "sidebar-mini") {
            $session->set('toggle', "");
        }
    }
}
