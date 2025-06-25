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
use App\Models\BCPurchaseOrderModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

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
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $bcPurchaseOrderModel;

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
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->bcPurchaseOrderModel = new BCPurchaseOrderModel();
    }

    public function index()
    {
        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $supplierData = $this->supplierModel
            ->select('GROUP_CONCAT(suppliers.id) AS id, suppliers.name, companies.company')
            ->join('companies', 'companies.id = suppliers.company_id')
            ->whereIn('company_id', $companyId)
            ->asObject()
            ->groupBy('suppliers.name')
            ->findAll();
        $data = [
            'suppliers' => $supplierData
        ];
        return view('Laporan/LaporanPembelian/index', $data);
    }

    public function allTransaksi()
    {
        $rawFilter = $this->request->getGet("filter");
        $filter = [];

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        }

        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $condition = [
            // "penerimaan_barang.company_id"  => $this->this_company_id,
            "penerimaan_barang.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $filter,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : date("Y-m-d"),
            "lastdate"      => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : date("Y-m-d"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        // $res = $this->transaksiPembelianModel->getList($condition, $addCondition, $limit, $offset);
        $res = $this->penerimaanBarangModel->getPenerimaanBarangListForAccounting($condition, $addCondition, $limit, $offset, $companyId);
        $metaValuta = $this->metadataModel->get_by_name('Valuta');

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $grandTotal = [
            'nominal'       => 0.0,
            'nominal_idr'   => 0.0,
            'paid_idr'      => 0.0,
        ];

        foreach ($res['data'] as $data) {
            // CARI BC NYA DI BC_PURCHASE ORDER
            $bc23PurchaseOrder = $this->bcPurchaseOrderModel->select('
                        bc_purchase_order.no_daftar,
                        bc_23.no_aju
                    ')
                ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id')
                ->like('multiple_lpb_id', $data->id)
                ->where('bc_purchase_order.company_id', $this->this_company_id)
                ->first();

            $bc40PurchaseOrder = $this->bcPurchaseOrderModel->select('
                        bc_purchase_order.no_daftar,
                        bc_40.no_aju
                    ')
                ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id')
                ->like('multiple_lpb_id', $data->id)
                ->where('bc_purchase_order.company_id', $this->this_company_id)
                ->first();

            if ($bc23PurchaseOrder != null) {
                $dokumenTransaksi = "BC 2.3 / " . $bc23PurchaseOrder['no_aju'] . " / " . $bc23PurchaseOrder['no_daftar'];
            } elseif ($bc40PurchaseOrder != null) {
                $dokumenTransaksi = "BC 4.0 / " . $bc40PurchaseOrder['no_aju'] . " / " . $bc40PurchaseOrder['no_daftar'];
            } else {
                $dokumenTransaksi = "-";
            }

            $tglTransaksi = $data->tanggal_penerimaan;
            $buktiTransaksi = $data->no_penerimaan_barang;
            $invoiceTransaksi = $data->no_invoice;
            $tglInvoiceTransaksi = $data->tanggal_penerimaan;
            $taxInvoiceTransaksi = "";
            $poNumberTransaksi = str_replace(',', ", ", str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no));
            $supplierTransaksi = $data->supplier_name;
            $valasTransaksi = "IDR";
            $exchangeTransaksi = 1.0;
            $nominalTransaksi = 0.0;
            $nominalIdrTransaksi = 0.0;
            $paidIdrTransaksi = 0.0;
            $totalHargaAll = 0.0;
            $lokalbb = "";
            $importbb = "";
            $bp = "";
            if ($data->status_penerimaan == "LOKAL" && $data->tipe_bahan == "BAKU") {
                $lokalbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangBakuDetail($data->id);
                // var_dump($lokalbb);
                foreach ($lokalbb as $value) {
                    // $totalxqty = $value['qty_barang_po'] * $value['total_barang_po'];
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->status_penerimaan == "IMPORT" && $data->tipe_bahan == "BAKU") {
                $importbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangImportBakuDetail($data->id);
                // var_dump($importbb);
                foreach ($importbb as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    } else {
                        $valasTransaksi = $value['currencyValue'];
                    }
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->tipe_bahan == "PENOLONG") {
                $bp = $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail($data->id);
                // var_dump($bp);
                foreach ($bp as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    } else {
                        $valasTransaksi = $value['currencyValue'] ? $value['currencyValue'] : "IDR";
                    }
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            }
            // exit;

            $grandTotal['nominal'] += floatval($nominalTransaksi) ?? 0;
            $grandTotal['nominal_idr'] += floatval($nominalIdrTransaksi) ?? 0;
            $grandTotal['paid_idr'] += floatval($paidIdrTransaksi) ?? 0;

            // var_dump($data);
            // var_dump(($nominalTransaksi));
            // var_dump(($nominalIdrTransaksi));
            // var_dump(($paidIdrTransaksi));
            // exit;

            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "po_date"               => $tglTransaksi,
                "dokumen_num"           => $dokumenTransaksi,
                "evidance_num"          => $buktiTransaksi,
                "invoice_num"           => $invoiceTransaksi,
                "invoice_date"          => $tglInvoiceTransaksi,
                "tax_invoice"           => $taxInvoiceTransaksi,
                "po_num"                => $poNumberTransaksi,
                "supplier_name"         => $supplierTransaksi,
                "valas"                 => $valasTransaksi,
                "exchange"              => number_format(floatval($exchangeTransaksi), 2, '.', ','),
                "nominal"               => number_format(floatval($nominalTransaksi), 2, '.', ','),
                "nominal_idr"           => number_format(floatval($nominalIdrTransaksi), 2, '.', ','),
                "paid_idr"              => number_format(floatval($paidIdrTransaksi), 2, '.', ','),
            ]);
        }
        // exit;

        $grandTotalFormatted = [
            'nominal'      => number_format($grandTotal['nominal'], 2, '.', ','),
            'nominal_idr'  => number_format($grandTotal['nominal_idr'], 2, '.', ','),
            'paid_idr'     => number_format($grandTotal['paid_idr'], 2, '.', ','),
        ];

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $res['totalData'],
            "recordsFiltered"   => $res['totalFilteredData'],
            "data"              => $addCondition['startdate'] != "" && $addCondition['lastdate'] != "" ? $rdata : [],
            "payload"           => $payload,
            "grandTotal"        => $grandTotalFormatted,
        ];

        return response()->setJSON($data);
    }

    public function LaporanPembelianPrint($tglAwal, $tglAkhir, $rawFilter, $search)
    {
        $dompdf = new Dompdf();
        $filter = [];

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        }

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $condition = [
            // "penerimaan_barang.company_id"  => $this->this_company_id,
            "penerimaan_barang.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $search,
            "filter"        => $filter,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $tglAwal ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal))) : date("Y-m-d"),
            "lastdate"      => $tglAkhir ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir))) : date("Y-m-d"),
        ];

        // $res = $this->transaksiPembelianModel->getList($condition, $addCondition, $limit, $offset);
        $res = $this->penerimaanBarangModel->getPenerimaanBarangListForPrintAccounting($condition, $addCondition, $companyId);
        $metaValuta = $this->metadataModel->get_by_name('Valuta');
        // var_dump($res);
        // exit;

        $rdata = [];

        $no = 1;
        foreach ($res['data'] as $data) {
            // CARI BC NYA DI BC_PURCHASE ORDER
            $bc23PurchaseOrder = $this->bcPurchaseOrderModel->select('
                        bc_purchase_order.no_daftar,
                        bc_23.no_aju
                    ')
                ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id')
                ->like('multiple_lpb_id', $data->id)
                ->where('bc_purchase_order.company_id', $this->this_company_id)
                ->first();

            $bc40PurchaseOrder = $this->bcPurchaseOrderModel->select('
                        bc_purchase_order.no_daftar,
                        bc_40.no_aju
                    ')
                ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id')
                ->like('multiple_lpb_id', $data->id)
                ->where('bc_purchase_order.company_id', $this->this_company_id)
                ->first();

            if ($bc23PurchaseOrder != null) {
                $dokumenTransaksi = "BC 2.3 / " . $bc23PurchaseOrder['no_aju'] . " / " . $bc23PurchaseOrder['no_daftar'];
            } elseif ($bc40PurchaseOrder != null) {
                $dokumenTransaksi = "BC 4.0 / " . $bc40PurchaseOrder['no_aju'] . " / " . $bc40PurchaseOrder['no_daftar'];
            } else {
                $dokumenTransaksi = "-";
            }

            $tglTransaksi = $data->tanggal_penerimaan;
            $buktiTransaksi = $data->no_penerimaan_barang;
            $invoiceTransaksi = $data->no_invoice;
            $tglInvoiceTransaksi = $data->tanggal_penerimaan;
            $taxInvoiceTransaksi = "";
            $poNumberTransaksi = str_replace(',', ", ", str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no));
            $supplierTransaksi = $data->supplier_name;
            $valasTransaksi = "IDR";
            $exchangeTransaksi = 1.0;
            $nominalTransaksi = 0.0;
            $nominalIdrTransaksi = 0.0;
            $paidIdrTransaksi = 0.0;
            $totalHargaAll = 0.0;
            $lokalbb = "";
            $importbb = "";
            $bp = "";
            if ($data->status_penerimaan == "LOKAL" && $data->tipe_bahan == "BAKU") {
                $lokalbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangBakuDetail($data->id);
                // var_dump($lokalbb);
                foreach ($lokalbb as $value) {
                    // $totalxqty = $value['qty_barang_po'] * $value['total_barang_po'];
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->status_penerimaan == "IMPORT" && $data->tipe_bahan == "BAKU") {
                $importbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangImportBakuDetail($data->id);
                // var_dump($importbb);
                foreach ($importbb as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    } else {
                        $valasTransaksi = $value['currencyValue'];
                    }
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->tipe_bahan == "PENOLONG") {
                $bp = $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail($data->id);
                // var_dump($bp);
                foreach ($bp as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    } else {
                        $valasTransaksi = $value['currencyValue'] ? $value['currencyValue'] : "IDR";
                    }
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            }

            array_push($rdata, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "po_date"               => $tglTransaksi,
                "dokumen_num"           => $dokumenTransaksi,
                "evidance_num"          => $buktiTransaksi,
                "invoice_num"           => $invoiceTransaksi,
                "invoice_date"          => $tglInvoiceTransaksi,
                "tax_invoice"           => $taxInvoiceTransaksi,
                "po_num"                => $poNumberTransaksi,
                "supplier_name"         => $supplierTransaksi,
                "valas"                 => $valasTransaksi,
                "exchange"              => number_format(floatval($exchangeTransaksi)),
                "nominal"               => number_format(floatval($nominalTransaksi)),
                "nominal_idr"           => number_format(floatval($nominalIdrTransaksi)),
                "paid_idr"              => number_format(floatval($paidIdrTransaksi)),
            ]);
        }

        $data = [
            "data"              => $rdata,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" =>  $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        // return view('Laporan/LaporanPembelian/print', $data);

        // var_dump($data);
        // exit;
        $filename = 'Laporan Pembelian PT. TOBA SURIMI INDUSTRIES, Tbk (' . session()->get("login")->this_company . ')';
        $encodedFilename = rawurlencode($filename . '.xlsx');

        $dompdf->loadHtml(view('Laporan/LaporanPembelian/print', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($encodedFilename, array("Attachment" => false));

        exit(0);
    }

    public function exportExcel($tglAwal, $tglAkhir, $rawFilter, $search)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $spreadsheet = new Spreadsheet();
        $filter = [];

        if ($rawFilter) {
            $filter = explode(',', $rawFilter);
        }

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $condition = [
            // "penerimaan_barang.company_id"  => $this->this_company_id,
            // "penerimaan_barang.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $search == "all" ? "" : $search,
            "filter"        => $rawFilter == "all" ? [] : $filter,
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate"     => $tglAwal ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAwal))) : date("Y-m-d"),
            "lastdate"      => $tglAkhir ? date("Y-m-d", strtotime(str_replace("/", "-", $tglAkhir))) : date("Y-m-d"),
        ];

        // Menggabungkan sel dari A1 hingga N1 dan mengisi dengan teks "Purchase Order"
        $spreadsheet->setActiveSheetIndex(0)
            ->mergeCells('A1:N1')
            ->setCellValue('A1', 'Purchase Order')
            ->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Menggabungkan sel dari A1 hingga N1 dan mengisi dengan teks "Purchase Order"
        $spreadsheet->setActiveSheetIndex(0)
            ->mergeCells('A2:N2')
            ->setCellValue('A2', 'Periode : ' . ($tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All") . " - " . ($tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now"))
            ->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A3', 'No.')
            ->setCellValue('B3', 'Transaction Date')
            ->setCellValue('C3', 'Department')
            ->setCellValue('D3', 'Document')
            ->setCellValue('E3', 'Evidance Num')
            ->setCellValue('F3', 'Invoice')
            ->setCellValue('G3', 'Invoice Date')
            ->setCellValue('H3', 'Tax Invoice')
            ->setCellValue('I3', 'PO Num')
            ->setCellValue('J3', 'Supplier')
            ->setCellValue('K3', 'Valas')
            ->setCellValue('L3', 'Exchange Rate')
            ->setCellValue('M3', 'Nominal Value')
            ->setCellValue('N3', 'Nominal Value(IDR)')
            ->setCellValue('O3', 'Paid Value(IDR)');

        $res = $this->penerimaanBarangModel->getPenerimaanBarangListForPrintAccounting($condition, $addCondition, $companyId);
        $metaValuta = $this->metadataModel->get_by_name('Valuta');

        $rdata = [];

        $no = 1;
        $column = 4;
        foreach ($res['data'] as $data) {
            // CARI BC NYA DI BC_PURCHASE ORDER
            $bc23PurchaseOrder = $this->bcPurchaseOrderModel->select('
                        bc_purchase_order.no_daftar,
                        bc_23.no_aju
                    ')
                ->join('bc_23', 'bc_23.bc_purchase_order_id = bc_purchase_order.id')
                ->like('multiple_lpb_id', $data->id)
                ->where('bc_purchase_order.company_id', $this->this_company_id)
                ->first();

            $bc40PurchaseOrder = $this->bcPurchaseOrderModel->select('
                        bc_purchase_order.no_daftar,
                        bc_40.no_aju
                    ')
                ->join('bc_40', 'bc_40.bc_purchase_order_id = bc_purchase_order.id')
                ->like('multiple_lpb_id', $data->id)
                ->where('bc_purchase_order.company_id', $this->this_company_id)
                ->first();

            if ($bc23PurchaseOrder != null) {
                $dokumenTransaksi = "BC 2.3 / " . $bc23PurchaseOrder['no_aju'] . " / " . $bc23PurchaseOrder['no_daftar'];
            } elseif ($bc40PurchaseOrder != null) {
                $dokumenTransaksi = "BC 4.0 / " . $bc40PurchaseOrder['no_aju'] . " / " . $bc40PurchaseOrder['no_daftar'];
            } else {
                $dokumenTransaksi = "-";
            }

            $tglTransaksi = $data->tanggal_penerimaan;
            $buktiTransaksi = $data->no_penerimaan_barang;
            $invoiceTransaksi = $data->no_invoice;
            $tglInvoiceTransaksi = $data->tanggal_penerimaan;
            $taxInvoiceTransaksi = "";
            $poNumberTransaksi = str_replace(',', ", ", str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no));
            $supplierTransaksi = $data->supplier_name;
            $valasTransaksi = "IDR";
            $exchangeTransaksi = 1.0;
            $nominalTransaksi = 0.0;
            $nominalIdrTransaksi = 0.0;
            $paidIdrTransaksi = 0.0;
            $totalHargaAll = 0.0;
            $lokalbb = "";
            $importbb = "";
            $bp = "";
            if ($data->status_penerimaan == "LOKAL" && $data->tipe_bahan == "BAKU") {
                $lokalbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangBakuDetail($data->id);
                // var_dump($lokalbb);
                foreach ($lokalbb as $value) {
                    // $totalxqty = $value['qty_barang_po'] * $value['total_barang_po'];
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->status_penerimaan == "IMPORT" && $data->tipe_bahan == "BAKU") {
                $importbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangImportBakuDetail($data->id);
                // var_dump($importbb);
                foreach ($importbb as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    } else {
                        $valasTransaksi = $value['currencyValue'];
                    }
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->tipe_bahan == "PENOLONG") {
                $bp = $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail($data->id);
                // var_dump($bp);
                foreach ($bp as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    } else {
                        $valasTransaksi = $value['currencyValue'] ? $value['currencyValue'] : "IDR";
                    }
                    $nominalTransaksi += floatval($value['sub_total']);
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            }
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column, $tglTransaksi)
                ->setCellValue('C' . $column, strtoupper($data->divisi))
                ->setCellValue('D' . $column, $dokumenTransaksi)
                ->setCellValue('E' . $column, $buktiTransaksi)
                ->setCellValue('F' . $column, $invoiceTransaksi)
                ->setCellValue('G' . $column, $tglInvoiceTransaksi)
                ->setCellValue('H' . $column, $taxInvoiceTransaksi)
                ->setCellValue('I' . $column, $poNumberTransaksi)
                ->setCellValue('J' . $column, $supplierTransaksi)
                ->setCellValue('K' . $column, $valasTransaksi)
                ->setCellValue('L' . $column, floatval($exchangeTransaksi))
                ->setCellValue('M' . $column, floatval($nominalTransaksi))
                ->setCellValue('N' . $column, floatval($nominalIdrTransaksi))
                ->setCellValue('O' . $column, floatval($paidIdrTransaksi));

            $spreadsheet->getActiveSheet()->getStyle('K' . $column)
                ->getNumberFormat()->setFormatCode('#,##0.00');
            $spreadsheet->getActiveSheet()->getStyle('L' . $column)
                ->getNumberFormat()->setFormatCode('#,##0.00');
            $spreadsheet->getActiveSheet()->getStyle('M' . $column)
                ->getNumberFormat()->setFormatCode('#,##0.00');
            $spreadsheet->getActiveSheet()->getStyle('N' . $column)
                ->getNumberFormat()->setFormatCode('#,##0.00');

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan Pembelian PT. TOBA SURIMI INDUSTRIES, Tbk (' . session()->get("login")->this_company . ')';
        $encodedFilename = rawurlencode($filename . '.xlsx');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $encodedFilename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
