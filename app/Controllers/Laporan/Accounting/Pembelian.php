<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Controllers\Master\Kurs;
use App\Models\SupplierModel;
use App\Models\TransaksiPembelianModel;
use App\Models\LocalPOPaymentModel;
use App\Models\MetadataModel;
use App\Models\KursModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderDetailModel;

class Pembelian extends BaseController
{
    protected $this_company_id;
    protected $supplierModel;
    protected $transaksiPembelianModel;
    protected $metadataModel;
    protected $kursModel;
    protected $rMImportPODetailModel;
    protected $rMPurchaseOrderDetailModel;
    protected $aMPurchaseOrderDetailModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
        $this->transaksiPembelianModel = new TransaksiPembelianModel();
        $this->metadataModel = new MetadataModel();
        $this->kursModel = new KursModel();
        $this->rMImportPODetailModel = new RMImportPODetailModel();
        $this->rMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->aMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
    }
    public function index()
    {
        $supplierData = $this->supplierModel->asObject()->findAll();
        $data = [
            'suppliers' => $supplierData
        ];
        return view('Laporan/LaporanPembelian/index', $data);
    }
    public function allTransaksi()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $condition = [
            // "company_id"  => $this->this_company_id,
            "deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $res = $this->transaksiPembelianModel->getList($condition, $addCondition, $limit, $offset);
        $metaValuta = $this->metadataModel->get_by_name('Valuta');

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;


        // var_dump($res);
        foreach ($res['data'] as $data) {
            $tglTransaksi = "";
            $dokumenTransaksi = "";
            $buktiTransaksi = "";
            $invoiceTransaksi = "";
            $tglInvoiceTransaksi = "";
            $taxInvoiceTransaksi = "";
            $poNumberTransaksi = "";
            $supplierTransaksi = "";
            $valasTransaksi = "";
            $exchangeTransaksi = "";
            $nominalTransaksi = "";
            $nominalIdrTransaksi = "";
            $paidIdrTransaksi = "";
            $totalHargaAll = 0.0;
            if ($data->id_local_bb != NULL) {
                $tglTransaksi = $data->po_date_lokal_bb;
                $dokumenTransaksi = "";
                $buktiTransaksi = $data->evidance_num;
                $invoiceTransaksi = "";
                $tglInvoiceTransaksi = "";
                $taxInvoiceTransaksi = "";
                $poNumberTransaksi = $data->po_no_lokal_bb;
                $supplierTransaksi = $data->supplier_name;
                $valasTransaksi = "IDR";
                $exchangeTransaksi = 1.0;
                $detail = $this->rMPurchaseOrderDetailModel->getPoBBLokalDetailById($data->id_lokal_bb);
                foreach ($detail as $value) {
                    $totalHarga = $value->general_price * $value->qty;
                    $totalHargaAll += $totalHarga;
                }
                $nominalTransaksi = $totalHargaAll;
                $nominalIdrTransaksi = $totalHargaAll * $exchangeTransaksi;
                $paidIdrTransaksi = "";
            } else if ($data->id_import_bb != NULL) {
                // var_dump($data);
                $tglTransaksi = $data->po_date_import_bb;
                $dokumenTransaksi = "";
                $buktiTransaksi = $data->evidance_num;
                $invoiceTransaksi = "";
                $tglInvoiceTransaksi = "";
                $taxInvoiceTransaksi = "";
                $poNumberTransaksi = $data->po_no_import_bb;
                $supplierTransaksi = $data->supplier_name;
                // Konversi format tanggal
                $datetime = \DateTime::createFromFormat('d/m/Y', $data->po_date_import_bb);
                $converted_date = $datetime->format('Y-m-d');

                // Gunakan nilai yang telah dikonversi
                $kursData = $this->kursModel->getByMetaId($data->currency_import_bb, $converted_date);
                // var_dump($kursData);
                foreach ($metaValuta as $value) {
                    if ($data->currency_import_bb == $value['id']) {
                        $valasTransaksi = $value['value'];
                        $exchangeTransaksi = $kursData->nilai_kurs;
                    }
                }
                $detail = $this->rMImportPODetailModel->getPoBBImportDetailById($data->id_import_bb);
                foreach ($detail as $value) {
                    $totalHargaAll += $value->total;
                }
                $nominalTransaksi = $totalHargaAll;
                $nominalIdrTransaksi = $totalHargaAll * $exchangeTransaksi;
                $paidIdrTransaksi = "";
            } else if ($data->id_po_bp != NULL) {
                $tglTransaksi = $data->po_date_po_bp;
                $dokumenTransaksi = "";
                $buktiTransaksi = $data->evidance_num;
                $invoiceTransaksi = "";
                $tglInvoiceTransaksi = "";
                $taxInvoiceTransaksi = "";
                $poNumberTransaksi = $data->po_no_po_bp;
                $supplierTransaksi = $data->supplier_name;
                $valasTransaksi = "IDR";
                $exchangeTransaksi = 1.0;
                $datetime = \DateTime::createFromFormat('d/m/Y', $data->po_date_po_bp);
                $converted_date = $datetime->format('Y-m-d');

                // Gunakan nilai yang telah dikonversi
                $kursData = $this->kursModel->getByMetaId($data->currency_po_bp, $converted_date);
                foreach ($metaValuta as $value) {
                    if ($data->currency_po_bp == $value['id']) {
                        $valasTransaksi = $value['value'];
                        $exchangeTransaksi = $kursData->nilai_kurs;
                    }
                }
                $detail = $this->aMPurchaseOrderDetailModel->getPoBPDetailById($data->id_po_bp);
                foreach ($detail as $value) {
                    $totalHargaAll += $value->total;
                }
                $nominalTransaksi = $totalHargaAll;
                $nominalIdrTransaksi = $totalHargaAll * $exchangeTransaksi;
                $paidIdrTransaksi = "";
            }
            // var_dump($poNumberTransaksi);
            // var_dump($supplierTransaksi);
            // var_dump($valasTransaksi);

            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->transaksi_pembelian_id,
                "po_date"               => $tglTransaksi,
                "dokumen_num"           => $dokumenTransaksi,
                "evidance_num"          => $buktiTransaksi,
                "invoice_num"           => $invoiceTransaksi,
                "invoice_date"          => $tglInvoiceTransaksi,
                "tax_invoice"           => $taxInvoiceTransaksi,
                "po_num"                => $poNumberTransaksi,
                "supplier_name"         => $supplierTransaksi,
                "valas"                 => $valasTransaksi,
                "exchange"              => number_format(floatval($exchangeTransaksi), 2, ',', '.'),
                "nominal"               => number_format(floatval($nominalTransaksi), 2, ',', '.'),
                "nominal_idr"           => number_format(floatval($nominalIdrTransaksi), 2, ',', '.'),
                "paid_idr"              => $paidIdrTransaksi,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $rdata,
            "payload"           => $payload,
        ];

        return response()->setJSON($data);
    }
}
