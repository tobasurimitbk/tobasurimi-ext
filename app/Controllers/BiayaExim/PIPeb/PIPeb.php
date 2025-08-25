<?php

namespace App\Controllers\BiayaExim\PIPeb;

use App\Controllers\BaseController;
use App\Models\MetadataModel;
use App\Models\SalesOrderExportModel;
use Exception;

class PIPeb extends BaseController
{
    protected $this_company_id;
    protected $salesOrderExportModel;
    protected $metaDataModel;

    public function __construct()
    {
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
    }

    public function index()
    {
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $data = [
            "dataValuta" => $dataValuta
        ];
        return view('BiayaExim/PIPeb/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "status"      => $this->request->getGet("status")
        ];

        $condition = [
            "sales_order_export.company_id"    => $this->this_company_id,
            "sales_order_export.deletedAt" => null,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status_invoice" => $this->request->getGet("status_invoice"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportModel->getListPiPeb($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => $data->sales_order_export_id,
                "no_invoice"                => $data->no_invoice,
                "tanggal_invoice"           => $data->tanggal_invoice != "" ? date('d/m/Y', strtotime($data->tanggal_invoice)) : "-",
                "customer_name"             => $data->customer_name,
                "sales_order_export_no"     => $data->sales_order_export_no,
                "dicharge_port"             => $data->dicharge_port,
                "status_invoice"            => $data->status_invoice,
                "nilai_pi"                  => $data->valas_pi_name == null ? "" :  "(" . $data->valas_pi_name . ") " . \number_format($data->shipment_value, 2),
                "nilai_peb"                 =>  $data->valas_peb_name == null ? "" : "(" . $data->valas_peb_name . ") " . \number_format($data->nilai_peb, 2),
                "nilai_peb_idr"                 => $data->nilai_peb_idr == null ? "" :  (float)$data->nilai_peb_idr,
                "exchange_rate_peb"                 => $data->exchange_rate_peb == null ? "" :  (float)$data->exchange_rate_peb,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesData['totalData'],
            "recordsFiltered"   => $salesData['totalFilteredData'],
            "data"              => $dataSales,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function updatePiPeb()
    {
        try {
            $id = $this->request->getVar('id');
            $tanggalInvoice = $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : "";
            $noInvoice = $this->request->getVar('no_invoice');
            $valasIdPeb = $this->request->getVar('valas_id_peb');
            $nilaiPeb = $this->request->getVar('nilai_peb');
            $exchangeRatePeb = $this->request->getVar('exchange_rate_peb');
            $nilaiPebIdr = $this->request->getVar('nilai_peb_idr');

            $checkNumber = $this->salesOrderExportModel
                ->where('no_invoice', $noInvoice)
                ->where('company_id', $this->this_company_id)
                ->where('sales_order_export_id !=', $id)
                ->first();

            if ($checkNumber != null) {
                return \response()->setJSON([
                    'status' => false,
                    'message' => "Nomor invoice sudah diterbitkan"
                ]);
            }

            $this->salesOrderExportModel->update($id, [
                'no_invoice' => $this->request->getVar('no_invoice'),
                'tanggal_invoice' => $tanggalInvoice,
                'nilai_peb' => $nilaiPeb,
                'status_invoice' => "TERBIT",
                'valas_id_peb' => $valasIdPeb,
                'exchange_rate_peb' => $exchangeRatePeb,
                'nilai_peb_idr' => $nilaiPebIdr
            ]);

            return \response()->setJSON([
                'status' => true,
                'message' => "Invoice berhasil diterbitkan"
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }

    public function getPiPeb()
    {
        $id = $this->request->getVar('id');
        $salesOrderExport = $this->salesOrderExportModel->where('sales_order_export_id', $id)->first();
        $resultData = [
            'id' => $id,
            'no_invoice' => $salesOrderExport['no_invoice'],
            'tanggal_invoice' => $salesOrderExport['tanggal_invoice'] != null ? date('d/m/Y', \strtotime($salesOrderExport['tanggal_invoice'])) : "",
            'valas_id_peb' => $salesOrderExport['valas_id_peb'],
            'nilai_peb' => (float)$salesOrderExport['nilai_peb'],
            'exchange_rate_peb' => (float)$salesOrderExport['exchange_rate_peb'],
            'nilai_peb_idr' => (float)$salesOrderExport['nilai_peb_idr'],
            'valas_id_pi' => $salesOrderExport['valas_id'],
            'nilai_pi' => (float)$salesOrderExport['shipment_value'],
        ];
        return \response()->setJSON([
            'status' => true,
            'data' => $resultData
        ]);
    }
}
