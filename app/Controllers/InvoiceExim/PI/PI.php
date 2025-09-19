<?php

namespace App\Controllers\InvoiceExim\PI;

use App\Controllers\BaseController;
use App\Models\BanksModel;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\ProformaInvoiceBarangModel;
use App\Models\ProformaInvoiceBiayaModel;
use App\Models\ProformaInvoiceModel;
use App\Models\ProformaInvoiceTermModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use Dompdf\Dompdf;
use Exception;

class PI extends BaseController
{
    protected $this_company_id;
    protected $salesOrderExportModel;
    protected $metaDataModel;
    protected $divisiModel;
    protected $bankModel;
    protected $satuanModel;
    protected $proformaInvoiceModel;
    protected $proformaInvoiceTermModel;
    protected $proformaInvoiceBarangModel;
    protected $proformaInvoiceBiayaModel;
    protected $companyModel;
    protected $dompdf;

    public function __construct()
    {
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->bankModel = new BanksModel();
        $this->satuanModel = new SatuansModel();
        $this->proformaInvoiceModel = new ProformaInvoiceModel();
        $this->proformaInvoiceTermModel = new ProformaInvoiceTermModel();
        $this->proformaInvoiceBarangModel = new ProformaInvoiceBarangModel();
        $this->companyModel = new CompaniesModel();
        $this->proformaInvoiceBiayaModel = new ProformaInvoiceBiayaModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $dataValuta = $this->metaDataModel->get_by_name('Valuta');
        $data = [
            "dataValuta" => $dataValuta
        ];
        return view('InvoiceExim/PI/index', $data);
    }

    public function allOrderForm()
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->salesOrderExportModel->getListPeb($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            $totalInvPI = \count($this->proformaInvoiceModel
                ->where('sales_order_export_id', $data->sales_order_export_id)
                ->where('deletedAt', null)
                ->findAll());

            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => \encrypt($data->sales_order_export_id),
                "no_invoice"                => $data->no_invoice,
                "tanggal_invoice"           => $data->tanggal_invoice != "" ? date('d/m/Y', strtotime($data->tanggal_invoice)) : "-",
                "customer_name"             => $data->customer_name,
                "sales_order_export_no"     => $data->sales_order_export_no,
                "dicharge_port"             => $data->dicharge_port,
                "status_invoice"            => $data->status_invoice,
                "nilai_peb"                 =>  "(" . $data->valas_peb_name . ") " . \number_format($data->shipment_value, 2),
                "total_inv_pi"              => $totalInvPI != 0 ? $totalInvPI . " Invoice" : ""
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

    public function allPI()
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
            "proforma_invoice.company_id"    => $this->this_company_id,
            "proforma_invoice.deletedAt" => null,
            "proforma_invoice.sales_order_export_id" => $this->request->getVar('sales_order_export_id')
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status_posting" => $this->request->getGet('status_posting'),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $piData = $this->proformaInvoiceModel->getList($condition, $addCondition, $limit, $offset);

        $dataPI = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($piData['data'] as $data) {
            $PIterm = $this->proformaInvoiceTermModel
                ->where('proforma_invoice_id', $data->id)
                ->where('is_penagihan', 1)
                ->where('deletedAt', null)
                ->findAll();

            $paymentTerm = "";
            foreach ($PIterm as $p) {
                $paymentTerm .= " " . $p['payment_term'];
            }

            array_push($dataPI, [
                "no"                        => $no++,
                "id"                        => \encrypt($data->id),
                "no_pi"                     => $data->no_pi,
                "tanggal_pi"                =>  date('d/m/Y', strtotime($data->tanggal_pi)),
                "total_pi"                  => "(" . $data->valas_name . ") " . \number_format($data->total_pi, 2),
                "payment_term"              => $paymentTerm,
                "status_posting"            => (int)$data->status_posting,
                "status_bayar"              => (int)$data->status_bayar,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $piData['totalData'],
            "recordsFiltered"   => $piData['totalFilteredData'],
            "data"              => $dataPI,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function indexPI($id)
    {
        $id = \decrypt($id);
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($id);
        $dataCompany = $this->companyModel->whereIn('id', [1, 2])->findAll();
        if ($dataSalesOrderExport == null) {
            return \redirect()->to('proforma-invoice');
        }

        // dd($dataSalesOrderExport);

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataCompany' => $dataCompany
        ];

        return view('InvoiceExim/PI/indexPI', $data);
    }

    public function createPI($id)
    {
        $id = \decrypt($id);
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($id);
        if ($dataSalesOrderExport == null) {
            return \redirect()->to('proforma-invoice');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan
        ];

        return view('InvoiceExim/PI/formPI', $data);
    }

    public function duplicatePI($id)
    {
        $id = \decrypt($id);
        $dataPI = $this->proformaInvoiceModel->where('id', $id)->first();
        if ($dataPI == null) {
            return \redirect()->to('proforma-invoice');
        }
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataPI['sales_order_export_id']);
        if ($dataSalesOrderExport == null) {
            return \redirect()->to('proforma-invoice');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataPIBarang = $this->proformaInvoiceBarangModel->getBarang($id);
        $dataPIPaymentTerm = $this->proformaInvoiceTermModel->where('proforma_invoice_id', $id)->where('deletedAt', null)->findAll();
        $dataPIBiaya = $this->proformaInvoiceBiayaModel->where('proforma_invoice_id', $id)->where('deletedAt', null)->findAll();

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataPI' => $dataPI,
            'dataPIBarang' => $dataPIBarang,
            'dataPIPaymentTerm' => $dataPIPaymentTerm,
            'dataPIBiaya' => $dataPIBiaya
        ];

        return view('InvoiceExim/PI/formPI_duplicate', $data);
    }

    public function editPI($id)
    {
        $id = \decrypt($id);
        $dataPI = $this->proformaInvoiceModel->where('id', $id)->first();
        if ($dataPI == null) {
            return \redirect()->to('proforma-invoice');
        }
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataPI['sales_order_export_id']);
        if ($dataSalesOrderExport == null) {
            return \redirect()->to('proforma-invoice');
        }
        $dataValuta = $this->metaDataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBank = $this->bankModel->where('company_id', $this->this_company_id)->findAll();
        $dataSatuan = $this->satuanModel->findAll();
        $dataPIBarang = $this->proformaInvoiceBarangModel->getBarang($id);
        $dataPIPaymentTerm = $this->proformaInvoiceTermModel->where('proforma_invoice_id', $id)->where('deletedAt', null)->findAll();
        $dataPIBiaya = $this->proformaInvoiceBiayaModel->where('proforma_invoice_id', $id)->where('deletedAt', null)->findAll();
        $dataCompany = $this->companyModel->whereIn('id', [1, 2])->findAll();

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataValuta' => $dataValuta,
            'dataBank' => $dataBank,
            'dataSatuan' => $dataSatuan,
            'dataPI' => $dataPI,
            'dataPIBarang' => $dataPIBarang,
            'dataPIPaymentTerm' => $dataPIPaymentTerm,
            'dataPIBiaya' => $dataPIBiaya,
            'dataCompany' => $dataCompany
        ];

        return view('InvoiceExim/PI/formPI', $data);
    }

    public function storePI()
    {
        // return \response()->setJSON([
        //     '$_POST' => $_POST,
        //     'listPaymentTerm' => \json_decode($_POST['listPaymentTerm']),
        //     'listBarang' => \json_decode($_POST['listBarang'])
        // ]);
        try {
            $noInvoicePI = $this->request->getVar('no_invoice_pi');
            if ($noInvoicePI == "AUTO GENERATE") {
                $noInvoicePI = $this->proformaInvoiceModel->generateNo(
                    $this->this_company_id
                );
            }

            $checkNumber = $this->checkNumber(
                $noInvoicePI
            );

            if (!$checkNumber) {
                return \response()->setJSON([
                    'status' => false,
                    'message' => "Nomor PI, sudah digunakan",
                    'token' => \csrf_hash()
                ]);
            }

            $id = $this->proformaInvoiceModel->insert([
                'company_id' => $this->this_company_id,
                'sales_order_export_id' => $this->request->getVar('sales_order_export_id'),
                'valas_id' => $this->request->getVar('valas_id'),
                'bank_id' => $this->request->getVar('bank_id'),
                'no_pi' => $noInvoicePI,
                'tanggal_pi' => $this->request->getVar("tanggal_pi") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_pi")))) : "",
                'payment_term' => $this->request->getVar('payment_term_parent'),
                'payment_instruction' => $this->request->getVar('payment_instruction'),
                'packing' => $this->request->getVar('packing'),
                'total_pi' => $this->request->getVar('total_pi'),
                'status_posting' => 0,
                'status_bayar' => 0,
                'penanda_tangan' => $this->request->getVar('penanda_tangan'),
                'alamat_customer' => $this->request->getVar('alamat_customer')
            ]);

            foreach (\json_decode($_POST['listPaymentTerm']) as $l) {
                $this->proformaInvoiceTermModel->insert([
                    'proforma_invoice_id' => $id,
                    'payment_term' => $l->payment_term,
                    'nilai_payment_term' => $l->nilai_payment_term,
                    'is_penagihan' => $l->is_penagihan,
                    'presentase' => $l->presentase
                ]);
            }

            foreach (\json_decode($_POST['listBarang']) as $l) {
                $this->proformaInvoiceBarangModel->insert([
                    'proforma_invoice_id' => $id,
                    'satuan_id' => $l->satuan_id,
                    'nama_barang' => $l->nama_barang,
                    'qty_barang' => $l->qty_barang,
                    'harga_satuan' => $l->harga_satuan,
                    'total_harga' => $l->total_harga,
                ]);
            }

            foreach (json_decode($_POST['listBiayaTambahan']) as $l) {
                $this->proformaInvoiceBiayaModel->insert([
                    'proforma_invoice_id' => $id,
                    'biaya_tambahan' => $l->biaya_tambahan,
                    'tipe_biaya_tambahan' => $l->tipe_biaya_tambahan,
                    'nilai_biaya_tambahan' => $l->nilai_biaya_tambahan
                ]);
            }

            return \response()->setJSON([
                'status' => true,
                'message' => "PI berhasil dibuat",
                'token' => \csrf_hash()
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => \false,
                'message' => $e->getMessage(),
                'token' => \csrf_hash()
            ]);
        }
    }

    public function updatePI()
    {
        try {
            $id = \decrypt($this->request->getVar('id'));
            $noInvoicePI = $this->request->getVar('no_invoice_pi');

            $checkNumber = $this->checkNumber(
                $noInvoicePI,
                $id
            );

            if (!$checkNumber) {
                return \response()->setJSON([
                    'status' => false,
                    'message' => "Nomor PI, sudah digunakan",
                    'token' => \csrf_hash()
                ]);
            }

            $this->proformaInvoiceModel->update($id, [
                'company_id' => $this->this_company_id,
                'sales_order_export_id' => $this->request->getVar('sales_order_export_id'),
                'valas_id' => $this->request->getVar('valas_id'),
                'bank_id' => $this->request->getVar('bank_id'),
                'no_pi' => $noInvoicePI,
                'tanggal_pi' => $this->request->getVar("tanggal_pi") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_pi")))) : "",
                'payment_term' => $this->request->getVar('payment_term_parent'),
                'payment_instruction' => $this->request->getVar('payment_instruction'),
                'packing' => $this->request->getVar('packing'),
                'total_pi' => $this->request->getVar('total_pi'),
                'penanda_tangan' => $this->request->getVar('penanda_tangan'),
                'alamat_customer' => $this->request->getVar('alamat_customer')
            ]);

            $id_proforma_invoice_payment_term = [];
            $id_proforma_invoice_barang = [];
            $id_proforma_invoice_biaya = [];

            foreach (\json_decode($_POST['listPaymentTerm']) as $l) {
                $check = $this->proformaInvoiceTermModel
                    ->where('id', $l->id_payment_term)
                    ->first();
                if ($check != null) {
                    $this->proformaInvoiceTermModel->update($check['id'], [
                        'proforma_invoice_id' => $id,
                        'payment_term' => $l->payment_term,
                        'nilai_payment_term' => $l->nilai_payment_term,
                        'is_penagihan' => $l->is_penagihan,
                        'presentase' => $l->presentase
                    ]);
                    \array_push($id_proforma_invoice_payment_term, $check['id']);
                } else {
                    $id_new = $this->proformaInvoiceTermModel->insert([
                        'proforma_invoice_id' => $id,
                        'payment_term' => $l->payment_term,
                        'nilai_payment_term' => $l->nilai_payment_term,
                        'is_penagihan' => $l->is_penagihan,
                        'presentase' => $l->presentase
                    ]);
                    \array_push($id_proforma_invoice_payment_term, $id_new);
                }
            }

            foreach (\json_decode($_POST['listBarang']) as $l) {
                $check = $this->proformaInvoiceBarangModel
                    ->where('id', $l->id_barang)
                    ->first();
                if ($check != null) {
                    $this->proformaInvoiceBarangModel->update($check['id'], [
                        'proforma_invoice_id' => $id,
                        'satuan_id' => $l->satuan_id,
                        'nama_barang' => $l->nama_barang,
                        'qty_barang' => $l->qty_barang,
                        'harga_satuan' => $l->harga_satuan,
                        'total_harga' => $l->total_harga,
                    ]);

                    \array_push($id_proforma_invoice_barang, $check['id']);
                } else {
                    $id_new =  $this->proformaInvoiceBarangModel->insert([
                        'proforma_invoice_id' => $id,
                        'satuan_id' => $l->satuan_id,
                        'nama_barang' => $l->nama_barang,
                        'qty_barang' => $l->qty_barang,
                        'harga_satuan' => $l->harga_satuan,
                        'total_harga' => $l->total_harga,
                    ]);
                    \array_push($id_proforma_invoice_barang, $id_new);
                }
            }

            foreach (json_decode($_POST['listBiayaTambahan']) as $l) {
                $check = $this->proformaInvoiceBiayaModel
                    ->where('id', $l->id_biaya_tambahan)
                    ->first();

                if ($check != null) {
                    $this->proformaInvoiceBiayaModel->update($check['id'], [
                        'biaya_tambahan' => $l->biaya_tambahan,
                        'tipe_biaya_tambahan' => $l->tipe_biaya_tambahan,
                        'nilai_biaya_tambahan' => $l->nilai_biaya_tambahan
                    ]);

                    array_push($id_proforma_invoice_biaya, $check['id']);
                } else {
                    $id_new = $this->proformaInvoiceBiayaModel->insert([
                        'proforma_invoice_id' => $id,
                        'biaya_tambahan' => $l->biaya_tambahan,
                        'tipe_biaya_tambahan' => $l->tipe_biaya_tambahan,
                        'nilai_biaya_tambahan' => $l->nilai_biaya_tambahan
                    ]);
                    array_push($id_proforma_invoice_biaya, $id_new);
                }
            }

            $this->proformaInvoiceBarangModel->whereNotIn('id', $id_proforma_invoice_barang)->where('proforma_invoice_id', $id)->delete();
            $this->proformaInvoiceTermModel->whereNotIn('id', $id_proforma_invoice_payment_term)->where('proforma_invoice_id', $id)->delete();
            $this->proformaInvoiceBiayaModel->whereNotIn('id', $id_proforma_invoice_biaya)->where('proforma_invoice_id', $id)->delete();

            return \response()->setJSON([
                'status' => true,
                'message' => "PI berhasil diupdate",
                'token' => \csrf_hash()
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => \false,
                'message' => $e->getMessage(),
                'token' => \csrf_hash()
            ]);
        }
    }

    public function destroyPI()
    {
        $id = \decrypt($this->request->getVar('id'));
        $this->proformaInvoiceModel->delete($id);
        $this->proformaInvoiceBarangModel->where('proforma_invoice_id', $id)->delete();
        $this->proformaInvoiceTermModel->where('proforma_invoice_id', $id)->delete();

        return \response()->setJSON([
            'status' => \true,
            'token' => \csrf_hash(),
            'message' => "PI berhasil dihapus"
        ]);
    }

    public function postingPI()
    {
        $id = \decrypt($this->request->getVar('id'));
        $this->proformaInvoiceModel->update($id, ['status_posting' => 1]);

        return \response()->setJSON([
            'status' => \true,
            'token' => \csrf_hash(),
            'message' => "PI berhasil diposting"
        ]);
    }

    public function unpostingPI()
    {
        $id = \decrypt($this->request->getVar('id'));
        $this->proformaInvoiceModel->update($id, ['status_posting' => 0]);

        return \response()->setJSON([
            'status' => \true,
            'token' => \csrf_hash(),
            'message' => "PI berhasil diunposting"
        ]);
    }

    public function printPI($id)
    {
        $id = \decrypt($id);
        $companyId = $this->request->getVar('company_id');
        if (empty($companyId)) {
            $companyId = $this->this_company_id;
        }

        $selectQry = "
            proforma_invoice.*,
            metadata.value as valas_name,
            banks.kode_bank,
            banks.name as nama_bank,
            banks.atas_nama,
            banks.no_rekening
        ";
        $dataPI = $this->proformaInvoiceModel
            ->select($selectQry)
            ->join('metadata', 'metadata.id = proforma_invoice.valas_id', 'left')
            ->join('banks', 'banks.id = proforma_invoice.bank_id', 'left')
            ->where('proforma_invoice.id', $id)
            ->first();
        $dataSalesOrderExport = $this->salesOrderExportModel->getById($dataPI['sales_order_export_id']);
        $dataPIBarang = $this->proformaInvoiceBarangModel->getBarang($id);
        $dataPIPaymentTerm = $this->proformaInvoiceTermModel->where('proforma_invoice_id', $id)->where('deletedAt', null)->findAll();
        $dataPIBiaya = $this->proformaInvoiceBiayaModel->where('proforma_invoice_id', $id)->where('deletedAt', null)->findAll();
        $company = $this->companyModel->where('id', $companyId)->first();

        $data = [
            'dataSalesOrderExport' => $dataSalesOrderExport,
            'dataPI' => $dataPI,
            'dataPIBarang' => $dataPIBarang,
            'dataPIPaymentTerm' => $dataPIPaymentTerm,
            'company' => $company,
            'dataPIBiaya' => $dataPIBiaya
        ];

        $this->dompdf->loadHtml(view('InvoiceExim/PI/print', $data));
        $this->dompdf->setPaper('legal', 'portrait');
        $this->dompdf->render();

        $canvas = $this->dompdf->getCanvas();
        $font = $this->dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
        $fontSize = 9;

        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($font, $fontSize) {
            if ($pageCount > 1) {
                $text = "Page $pageNumber of $pageCount";
                $textWidth = $fontMetrics->getTextWidth($text, $font, $fontSize);
                $x = $canvas->get_width() - $textWidth - 20;
                $y = $canvas->get_height() - 20;
                $canvas->text($x, $y, $text, $font, $fontSize);
            }
        });

        // Output PDF
        $filename = $dataPI['no_pi'];
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    private function checkNumber($noInvoicePI, $id = null)
    {
        $companyId = $this->this_company_id;
        if ($companyId == 1 || $companyId == 2) {
            $companyIdArr = [1, 2];
        } elseif ($companyId == 15) {
            $companyIdArr = [15];
        } else {
            $companyIdArr = [16];
        }

        $dataQry = $this->proformaInvoiceModel->whereIn('company_id', $companyIdArr);
        $dataQry->where('no_pi', $noInvoicePI);
        if ($id != null) {
            $dataQry->where('id !=', $id);
        }

        if ($dataQry->first() == null) {
            return true;
        }

        return false;
    }
}
