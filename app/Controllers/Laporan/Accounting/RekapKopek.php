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
use App\Models\PenerimaanBarangModel;
use App\Models\PenerimaanBarangDetailModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class RekapKopek extends BaseController
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
    }
    public function index()
    {
        $supplierData = $this->supplierModel->asObject()->findAll();
        $data = [
            'suppliers' => $supplierData
        ];
        return view('Laporan/LaporanKopek/index', $data);
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
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            // "company_id"  => $this->this_company_id,
            "penerimaan_barang.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "filter"        => $this->request->getGet("filter"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        // $res = $this->transaksiPembelianModel->getList($condition, $addCondition, $limit, $offset);
        $res = $this->penerimaanBarangModel->getPenerimaanBarangListForAccounting($condition, $addCondition, $limit, $offset, $companyId);
        $metaValuta = $this->metadataModel->get_by_name('Valuta');

        $rdata = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($res['data'] as $data) {
            $tglTransaksi = $data->tanggal_penerimaan;
            $dokumenTransaksi = $data->BC23_AJU ? "BC 2.3/" . $data->BC23_AJU : ($data->BC40_AJU ? "BC 4.0/" . $data->BC40_AJU : "-");
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
                foreach ($lokalbb as $value) {
                    $totalxqty = $value['qty_barang_po'] * $value['total_barang_po'];
                    $nominalTransaksi += $totalxqty;
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->status_penerimaan == "IMPORT" && $data->tipe_bahan == "BAKU") {
                $importbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangImportBakuDetail($data->id);
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
                    $nominalTransaksi += $value['total_po'];
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else {
                $bp = $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail($data->id);
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
                    $nominalTransaksi += $value['total_po'];
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
                "exchange"              => number_format(floatval($exchangeTransaksi), 2, ',', '.'),
                "nominal"               => number_format(floatval($nominalTransaksi), 2, ',', '.'),
                "nominal_idr"           => number_format(floatval($nominalIdrTransaksi), 2, ',', '.'),
                "paid_idr"              => $paidIdrTransaksi,
            ]);

            // var_dump($rdata);
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

    public function LaporanKopekPrint($tglAwal, $tglAkhir, $filter, $search)
    {
        $dompdf = new Dompdf();
        $condition = [
            // "company_id"  => $this->this_company_id,
            "penerimaan_barang.deletedAt" => NULL
        ];

        // var_dump($tglAwal);
        // var_dump($tglAkhir);
        // var_dump($filterData);
        // exit;

        $addCondition = [
            "search"        => $search != "all" ? $search : "",
            "filter"        => $filter != "all" ? $filter : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate" => $tglAwal != "all" ? $tglAwal : "",
            "lastdate" => $tglAkhir != "now" ? $tglAkhir : "",
        ];

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        // $res = $this->transaksiPembelianModel->getList($condition, $addCondition, $limit, $offset);
        $res = $this->penerimaanBarangModel->getPenerimaanBarangListForAccounting($condition, $addCondition, null, null, $companyId);
        $metaValuta = $this->metadataModel->get_by_name('Valuta');
        // var_dump($res);
        // exit;

        $rdata = [];

        $no = 1;
        foreach ($res['data'] as $data) {
            $tglTransaksi = $data->tanggal_penerimaan;
            $dokumenTransaksi = $data->BC23_AJU ? "BC 2.3/" . $data->BC23_AJU : ($data->BC40_AJU ? "BC 4.0/" . $data->BC40_AJU : "-");
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
                foreach ($lokalbb as $value) {
                    $totalxqty = $value['qty_barang_po'] * $value['total_barang_po'];
                    $nominalTransaksi += $totalxqty;
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->status_penerimaan == "IMPORT" && $data->tipe_bahan == "BAKU") {
                $importbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangImportBakuDetail($data->id);
                foreach ($importbb as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    }
                    $nominalTransaksi += $value['total_po'];
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->tipe_bahan == "PENOLONG") {
                $bp = $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail($data->id);
                foreach ($bp as $value) {
                    // var_dump($valasTransaksi);
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    // var_dump($kursData);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    }
                    // var_dump($valasTransaksi);
                    // var_dump($exchangeTransaksi);
                    $nominalTransaksi += $value['total_po'];
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            }
            // var_dump($lokalbb);
            // var_dump($importbb);
            // var_dump($bp);

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
                "exchange"              => number_format(floatval($exchangeTransaksi), 2, ',', '.'),
                "nominal"               => number_format(floatval($nominalTransaksi), 2, ',', '.'),
                "nominal_idr"           => number_format(floatval($nominalIdrTransaksi), 2, ',', '.'),
                "paid_idr"              => $paidIdrTransaksi,
            ]);
        }

        $data = [
            "data"              => $rdata,
            "dateStart" => $tglAwal != "all" ? date("d/m/Y", strtotime($tglAwal)) : "All",
            "dateEnd" =>  $tglAkhir != "now" ? date("d/m/Y", strtotime($tglAkhir)) : "Now",
        ];

        // return view('Laporan/LaporanKopek/print', $data);

        // var_dump($data);
        // exit;
        $dompdf->loadHtml(view('Laporan/LaporanKopek/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Pembelian ", array("Attachment" => false));

        exit(0);
    }

    public function exportExcel($tglAwal, $tglAkhir, $filter, $search)
    {

        $spreadsheet = new Spreadsheet();


        $condition = [
            "penerimaan_barang.deletedAt" => NULL
        ];

        $addCondition = [
            "search"        => $search != "all" ? $search : "",
            "filter"        => $filter != "all" ? $filter : "",
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "startdate" => $tglAwal != "all" ? $tglAwal : "",
            "lastdate" => $tglAkhir != "now" ? $tglAkhir : "",
        ];

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Transaction Date')
            ->setCellValue('C1', 'Document')
            ->setCellValue('D1', 'Evidance Num')
            ->setCellValue('E1', 'Invoice')
            ->setCellValue('F1', 'Invoice Date')
            ->setCellValue('G1', 'Tax Invoice')
            ->setCellValue('H1', 'PO Num')
            ->setCellValue('I1', 'Supplier')
            ->setCellValue('J1', 'Valas')
            ->setCellValue('K1', 'Exchange Rate')
            ->setCellValue('L1', 'Nominal Value')
            ->setCellValue('M1', 'Nominal Value(IDR)')
            ->setCellValue('N1', 'Paid Value(IDR)');

        if ($this->this_company_id != 16 && $this->this_company_id != 15) {
            $companyId = [1, 2];
        } else if ($this->this_company_id == 15) {
            $companyId = [15];
        } else {
            $companyId = [16];
        }

        $res = $this->penerimaanBarangModel->getPenerimaanBarangListForAccounting($condition, $addCondition, null, null, $companyId);
        $metaValuta = $this->metadataModel->get_by_name('Valuta');

        $rdata = [];

        $no = 1;
        $column = 2;
        foreach ($res['data'] as $data) {
            $tglTransaksi = $data->tanggal_penerimaan;
            $dokumenTransaksi = $data->BC23_AJU ? "BC 2.3/" . $data->BC23_AJU : ($data->BC40_AJU ? "BC 4.0/" . $data->BC40_AJU : "-");
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
                foreach ($lokalbb as $value) {
                    $totalxqty = $value['qty_barang_po'] * $value['total_barang_po'];
                    $nominalTransaksi += $totalxqty;
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->status_penerimaan == "IMPORT" && $data->tipe_bahan == "BAKU") {
                $importbb = $this->penerimaanBarangDetailModel->getPenerimaanBarangImportBakuDetail($data->id);
                foreach ($importbb as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    }
                    $nominalTransaksi += $value['total_po'];
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            } else if ($data->tipe_bahan == "PENOLONG") {
                $bp = $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail($data->id);
                foreach ($bp as $value) {
                    $kursData = $this->kursModel->getByMetaId($value['currency'], $data->tanggal);
                    if ($kursData) {
                        foreach ($metaValuta as $valueValuta) {
                            if ($value['currency'] == $valueValuta['id']) {
                                $valasTransaksi = $valueValuta['value'];
                                $exchangeTransaksi = $kursData->nilai_kurs;
                            }
                        }
                    }
                    $nominalTransaksi += $value['total_po'];
                }
                $totalHargaAll = $nominalTransaksi * $exchangeTransaksi;
                $nominalIdrTransaksi += $totalHargaAll;
            }
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column, $tglTransaksi)
                ->setCellValue('C' . $column, $dokumenTransaksi)
                ->setCellValue('D' . $column, $buktiTransaksi)
                ->setCellValue('E' . $column, $invoiceTransaksi)
                ->setCellValue('F' . $column, $tglInvoiceTransaksi)
                ->setCellValue('G' . $column, $taxInvoiceTransaksi)
                ->setCellValue('H' . $column, $poNumberTransaksi)
                ->setCellValue('I' . $column, $supplierTransaksi)
                ->setCellValue('J' . $column, $valasTransaksi)
                ->setCellValue('K' . $column, number_format(floatval($exchangeTransaksi), 2, ',', '.'))
                ->setCellValue('L' . $column, number_format(floatval($nominalTransaksi), 2, ',', '.'))
                ->setCellValue('M' . $column, number_format(floatval($nominalIdrTransaksi), 2, ',', '.'))
                ->setCellValue('N' . $column, $paidIdrTransaksi);

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Pembelian';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
