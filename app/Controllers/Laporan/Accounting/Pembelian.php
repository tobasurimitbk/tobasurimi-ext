<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\SupplierModel;
use App\Models\TransaksiPembelianModel;
use App\Models\LocalPOPaymentModel;

class Pembelian extends BaseController
{
    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
    }
    public function index()
    {
        $supplierData = $this->supplierModel->asObject()->findAll();
        $data = [
            'suppliers' => $supplierData
        ];
        return view('Laporan/LaporanPembelian/index',$data);
    }
    public function allTransaksi()
    {
        $localPOPaymentModel = new LocalPOPaymentModel();
        $transaksiPembelianModel = new TransaksiPembelianModel();

        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $dataPembayaranPOLokal = [];

        $condition = [
            "suppliers.company_id"  => $this->this_company_id
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "dateStart" => $this->request->getGet('dateStart'),
            "dateEnd" => $this->request->getGet('dateEnd')
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $paymentData = $transaksiPembelianModel->getPaymentList($condition, $addCondition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentData['data'] as $data) {
            array_push($dataPembayaranPOLokal, [
                "no"                => $no++,
                "id"                => $data->id,
                "payment_date"      => $data->payment_date,
                "dokumen"           => "Dokumen",
                "ev_num"            => $data->ev_num,
                "due_date"          => $data->due_date,
                "payment_no"        => $data->payment_no,
                "supplier"          => $data->supplierName,
                "payment_method"    => $data->payment_method,
                "amount"            => "Rp " . number_format($data->amount ?? 0, 0, ',', '.')
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $paymentData['totalData'],
            "recordsFiltered"   => $paymentData['totalFilteredData'],
            "data"              => $dataPembayaranPOLokal,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }
}
