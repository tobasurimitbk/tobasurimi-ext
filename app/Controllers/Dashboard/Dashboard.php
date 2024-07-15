<?php

namespace App\Controllers\Dashboard;


use App\Controllers\BaseController;
use App\Controllers\BeaCukai\BC23;
use App\Controllers\BeaCukai\BC27;

use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\CustomerModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderLainModel;
use App\Models\UserModel;
use App\Models\WarehousesModel;
use App\Models\WorkOrdersModel;
use App\Models\BC23Model;
use App\Models\BC25Model;
use App\Models\BC27Model;
use App\Models\BC30Model;
use App\Models\BC40Model;
use App\Models\BC41Model;
use App\Models\BCPurchaseOrderModel;
use App\Models\PPBKBModel;
use PDO;

class Dashboard extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }
    public function dashboard()
    {

        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $rmImportPOModel = new RMImportPOModel();
        $salesOrderModel = new SalesOrderModel();
        $salesOrderExportModel = new SalesOrderExportModel();
        $salesOrderLainModel = new SalesOrderLainModel();
        $warehouseModel = new WarehousesModel();
        $workOrdersModel = new WorkOrdersModel();
        $userModel = new UserModel();
        $customerModel = new CustomerModel();

        $jumlah_pembayaran_belum_posting = 0;
        $jumlah_sales_bulan_ini = 0;
        $jumlah_warehouse_bulan_ini = 0;

        $condition = [
            'company_id' => $this->this_company_id,
            'deletedAt'  => null
        ];
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
            ->where('id_company', $this->this_company_id)
            ->where('deletedAt', null)
            ->first();

        $jumlah_sales_order_export_bulan_ini = $salesOrderExportModel
            ->select("count(*) as total_bulan_ini")
            ->where("DATE_FORMAT(createdAt, '%m')", $month)
            ->where($condition)
            ->first();
        $jumlah_sales_order_lain_bulan_ini = $salesOrderLainModel
            ->select("count(*) as total_bulan_ini")
            ->where("DATE_FORMAT(createdAt, '%m')", $month)
            ->where($condition)
            ->first();

        $jumlah_sales_bulan_ini =  $jumlah_sales_order_bulan_ini['total_bulan_ini'] +  $jumlah_sales_order_export_bulan_ini['total_bulan_ini']
            + $jumlah_sales_order_lain_bulan_ini['total_bulan_ini'];

        $jumlah_warehouse_bulan_ini = $warehouseModel
            ->select("count(*) as total_bulan_ini")
            ->where("DATE_FORMAT(createdAt, '%m')", $month)
            ->where($condition)
            ->first();

        $jumlah_work_orders = $workOrdersModel
            ->select("count(*) as total_work_orders")
            ->where($condition)
            ->first();

        $jumlah_users = $userModel
            ->select("count(*) as total_user")
            ->where('current_company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->first();

        $jumlah_customer = $customerModel
            ->select("count(*) as total_customer")
            ->where($condition)
            ->first();

        $data = [
            'jumlah_pembayaran' => $jumlah_pembayaran_belum_posting,
            'jumlah_sales_bulan_ini' => $jumlah_sales_bulan_ini,
            'jumlah_warehouse_bulan_ini' => $jumlah_warehouse_bulan_ini['total_bulan_ini'],
            'jumlah_work_orders' => $jumlah_work_orders['total_work_orders'],
            'jumlah_users' => $jumlah_users['total_user'],
            'jumlah_customer' => $jumlah_customer['total_customer']
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

    public function getNumberBC()
    {
        $date = $this->request->getVar("dateBC");

        $bc23Model = new BC23Model();
        $bc25Model = new BC25Model();
        $bc27Model = new BC27Model();
        $bc30Model = new BC30Model();
        $bc40Model = new BC40Model();
        $bc41Model = new BC41Model();
        $ppbkbModel = new PPBKBModel();



        $selectQry = "count(*) as total_bc";



        $condition = [
            'company_id' => $this->this_company_id
        ];
        $countBC23 = $bc23Model
            ->select("count(bc_23.id) as total_bc")
            ->join('bc_purchase_order', 'bc_23.bc_purchase_order_id = bc_purchase_order.id')
            ->where($condition)
            ->where("DATE_FORMAT(bc_23.createdAt, '%m/%Y')", $date)
            ->where('bc_23.deletedAt', null)
            ->first();

        $countBC25 = $bc25Model
            ->select($selectQry)
            ->where($condition)
            ->where("DATE_FORMAT(createdAt, '%m/%Y')", $date)
            ->where('deletedAt', null)
            ->first();
        $countBC27 = $bc27Model
            ->select($selectQry)
            ->where('company_asal_id', $this->this_company_id)
            ->where("DATE_FORMAT(bc_27.createdAt, '%m/%Y')", $date)
            ->where('deletedAt', null)
            ->first();
        $countBC30 = $bc30Model
            ->select($selectQry)
            ->where($condition)
            ->where("DATE_FORMAT(createdAt, '%m/%Y')", $date)
            ->where('deletedAt', null)
            ->first();
        $countBC40 = $bc40Model
            ->select($selectQry)
            ->join('bc_purchase_order', 'bc_40.bc_purchase_order_id = bc_purchase_order.id')
            ->where($condition)
            ->where("DATE_FORMAT(bc_40.createdAt, '%m/%Y')", $date)
            ->where('bc_40.deletedAt', null)
            ->first();
        $countBC41 = $bc41Model
            ->select($selectQry)
            ->where($condition)
            ->where("DATE_FORMAT(createdAt, '%m/%Y')", $date)
            ->where('deletedAt', null)
            ->first();

        $countPPBKB = $ppbkbModel
            ->select($selectQry)
            ->where($condition)
            ->where('deletedAt', null)
            ->where("DATE_FORMAT(createdAt, '%m/%Y')", $date)
            ->first();

        $countDataBC = [
            "bc23" => $countBC23['total_bc'],
            "bc25" => $countBC25['total_bc'],
            "bc27" => $countBC27['total_bc'],
            "bc30" => $countBC30['total_bc'],
            "bc40" => $countBC40['total_bc'],
            "bc41" => $countBC41['total_bc'],
            "ppbkb" => $countPPBKB['total_bc']
        ];


        echo json_encode($countDataBC);
        return;
    }
}
