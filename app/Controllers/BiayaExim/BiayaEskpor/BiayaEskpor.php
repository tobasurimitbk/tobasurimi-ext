<?php

namespace App\Controllers\BiayaExim\BiayaEskpor;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\SalesOrderExportModel;
use App\Models\TaxModel;
use App\Models\VendorPelayaranModel;
use Exception;

class BiayaEskpor extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $taxesModel;
    protected $vendorPelayaranModel;
    protected $salesOrderExportModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->taxesModel = new TaxModel();
        $this->vendorPelayaranModel = new VendorPelayaranModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
    }

    public function index()
    {
        return view('BiayaExim/BiayaEskpor/index');
    }

    public function create()
    {
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataPajak = $this->taxesModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('type', "asc")->findAll();
        $dataVendorPelayaran = $this->vendorPelayaranModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('nama_vendor', "asc")->findAll();
        $dataSalesOrderExport = $this->salesOrderExportModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('sales_order_export_id', "desc")->findAll();

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataSalesOrderExport" => $dataSalesOrderExport
        ];

        return view('BiayaExim/BiayaEskpor/form', $data);
    }

    public function getDetailOrderForm()
    {
        try {
            $salesOrderExportId = $this->request->getVar('sales_order_export_id');
            $salesOrderExport = $this->salesOrderExportModel->getById($salesOrderExportId);

            $dataSalesExportDetail =  $this->salesOrderExportModel
                ->getDetailSalesKontrakInOrderForm(
                    $salesOrderExport->sales_contract_id,
                    $salesOrderExportId
                );

            return response()->setJSON([
                'dataSalesExportDetail' => $dataSalesExportDetail,
                'dataSalesOrderExport' => $salesOrderExport,
                'status' => true
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
