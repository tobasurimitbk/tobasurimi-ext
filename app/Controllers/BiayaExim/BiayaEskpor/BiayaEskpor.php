<?php

namespace App\Controllers\BiayaExim\BiayaEskpor;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\BiayaEksporBarangModel;
use App\Models\BiayaEksporDetailModel;
use App\Models\BiayaEksporModel;
use App\Models\BiayaEksporPajakModel;
use App\Models\CustomerModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderExportModel;
use App\Models\SatuansModel;
use App\Models\TaxModel;
use App\Models\UserModel;
use App\Models\VendorPelayaranModel;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BiayaEskpor extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $taxesModel;
    protected $vendorPelayaranModel;
    protected $salesOrderExportModel;
    protected $biayaEksporModel;
    protected $biayaEksporDetailModel;
    protected $biayaEksporPajakModel;
    protected $customerModel;
    protected $barangMasterSalesModel;
    protected $metadataModel;
    protected $satuanModel;
    protected $biayaEksporBarangModel;
    protected $usersModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->taxesModel = new TaxModel();
        $this->vendorPelayaranModel = new VendorPelayaranModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->biayaEksporModel = new BiayaEksporModel();
        $this->biayaEksporDetailModel = new BiayaEksporDetailModel();
        $this->biayaEksporPajakModel = new BiayaEksporPajakModel();
        $this->customerModel = new CustomerModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->metadataModel = new MetadataModel();
        $this->satuanModel = new SatuansModel();
        $this->biayaEksporBarangModel = new BiayaEksporBarangModel();
        $this->usersModel = new UserModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $dataUser = $this->usersModel->orderBy('name', 'asc')->where('deletedAt', null)->findAll();
        $data = [
            'dataUser' => $dataUser
        ];
        return view('BiayaExim/BiayaEskpor/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "biaya_ekspor.company_id"  => $this->this_company_id,
            "biaya_ekspor.deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "status_posting" => $this->request->getGet("status_posting"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataResult = $this->biayaEksporModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $vendorResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataResult['data'] as $data) {
            array_push($vendorResult, [
                "no"                => $no++,
                "id"                => \encrypt($data['id']),
                "divisi"        => $data['divisi'],
                "tanggal_invoice"   => \date('d/m/Y', \strtotime($data['tanggal_invoice'])),
                "no_invoice"        => $data['no_invoice'],
                "no_container"        => $data['no_container'],
                "customer_name"        => $data['customer_name'],
                "destination"        => $data['destination'],
                "nama_vendor"        => $data['nama_vendor'],
                "total_faktur"        => (float)$data['total_faktur'],
                "status_posting_exim"        => $data['status_posting_exim'],
                "status_posting_acc"        => $data['status_posting_acc'],
                "status_posting_audit"        => $data['status_posting_audit'],
                "status_bayar" => $data['status_bayar']
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $dataResult['totalData'],
            "recordsFiltered"   => $dataResult['totalFilteredData'],
            "data"              => $vendorResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create()
    {
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataPajak = $this->taxesModel
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%', 'PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('type', "asc")
            ->findAll();
        $dataVendorPelayaran = $this->vendorPelayaranModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('nama_vendor', "asc")->findAll();
        $dataCustomer = $this->customerModel->getCustomerList("INTERNASIONAL");
        $dataBarang = $this->barangMasterSalesModel->where('type_barang', "bahan_jadi")->where('type_barang_sales', "EKSPOR")->where('deletedAt', null)->orderBy('barang_name', "ASC")->findAll();
        $dataValuta = $this->metadataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataBiayaEksporList = $this->biayaEksporModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('id', "desc")->findAll();

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataCustomer" => $dataCustomer,
            "dataBarang" => $dataBarang,
            "dataValuta" => $dataValuta,
            "dataSatuan" => $dataSatuan,
            "dataBiayaEksporList" => $dataBiayaEksporList
        ];

        return view('BiayaExim/BiayaEskpor/form', $data);
    }

    public function edit($id)
    {
        $id = \decrypt($id);
        $dataBiayaEskpor = $this->biayaEksporModel->getById($id);
        if ($dataBiayaEskpor == null) {
            return \redirect()->to('biaya-ekspor');
        }
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataPajak = $this->taxesModel
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%', 'PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('type', "asc")
            ->findAll();
        $dataVendorPelayaran = $this->vendorPelayaranModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('nama_vendor', "asc")->findAll();
        $dataSalesOrderExport = $this->salesOrderExportModel->where('sales_order_export_id', $dataBiayaEskpor['sales_order_export_id'])->findAll();
        $dataBiayaEksporPajak = $this->biayaEksporPajakModel
            ->select('biaya_ekspor_pajak.*,taxes.type as type_tax, taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_ekspor_pajak.tax_id', 'left')
            ->where('biaya_ekspor_pajak.biaya_ekspor_id', $id)
            ->where('biaya_ekspor_pajak.deletedAt', null)
            ->findAll();
        $dataBiayaEksporDetail = $this->biayaEksporDetailModel
            ->where('biaya_ekspor_id', $id)
            ->where('deletedAt', null)
            ->findAll();
        $dataCustomer = $this->customerModel->getCustomerList("INTERNASIONAL");
        $dataBarang = $this->barangMasterSalesModel->where('type_barang', "bahan_jadi")->where('type_barang_sales', "EKSPOR")->where('deletedAt', null)->orderBy('barang_name', "ASC")->findAll();
        $dataValuta = $this->metadataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataDetailBarang = $this->biayaEksporBarangModel->getListBarang($id);

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataSalesOrderExport" => $dataSalesOrderExport,
            "dataBiayaEskpor" => $dataBiayaEskpor,
            "dataBiayaEksporPajak" => $dataBiayaEksporPajak,
            "dataBiayaEksporDetail" => $dataBiayaEksporDetail,
            "dataCustomer" => $dataCustomer,
            "dataBarang" => $dataBarang,
            "dataValuta" => $dataValuta,
            "dataSatuan" => $dataSatuan,
            "dataDetailBarang" => $dataDetailBarang
        ];

        return view('BiayaExim/BiayaEskpor/form', $data);
    }

    public function print($id)
    {

        $id = decrypt($id);
        $dataBiayaEkspor = $this->biayaEksporModel->where('id', $id)->first();
        if ($dataBiayaEkspor == null) {
            return redirect()->to('biaya-eskpor');
        }

        $filename = $dataBiayaEkspor['no_invoice'];

        $data = [];
        $detailBiayaList = [];
        $taxList = [];
        $taxReturnList = [];
        $biayaTotal = 0;
        $taxTotal = 0;
        $taxReturnTotal = 0;

        $dataBiayaEkspor = $this->biayaEksporModel
            ->select("biaya_ekspor.*, vendor_pelayaran.nama_vendor")
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_ekspor.vendor_pelayaran_id', 'left')
            ->where('biaya_ekspor.id', $id)
            ->first();

        $dataBiayaEksporDetail = $this->biayaEksporDetailModel
            ->where('biaya_ekspor_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        // Pajak Bukti Pengeluaran (Bon Putih)
        $taxData = $this->biayaEksporPajakModel
            ->select('biaya_ekspor_pajak.*,taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_ekspor_pajak.tax_id', 'left')
            ->where('biaya_ekspor_id', $id)
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%'])
            ->where('biaya_ekspor_pajak.deletedAt', null)
            ->findAll();

        // Pajak Bukti Pemasukkan (Bon Merah)
        $taxReturnData = $this->biayaEksporPajakModel
            ->select('biaya_ekspor_pajak.*,taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_ekspor_pajak.tax_id', 'left')
            ->where('biaya_ekspor_id', $id)
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('biaya_ekspor_pajak.deletedAt', null)
            ->findAll();

        foreach ($dataBiayaEksporDetail as $det) {
            $detailBiayaList[] = $det['uraian_biaya'];
            $biayaTotal += $det['nilai_biaya'];
        }

        foreach ($taxData as $tax) {
            $taxList[] = "{$tax['tax_name']}: {$tax['no_faktur_pajak']}";
            $taxTotal += $tax['nilai_pajak'];
        }

        foreach ($taxReturnData as $tax) {
            $taxReturnList[] = "{$tax['tax_name']}: {$tax['no_faktur_pajak']}";
            $taxReturnTotal += $tax['nilai_pajak'];
        }

        $total = $biayaTotal  + $taxReturnTotal;

        $data["uraian"] = implode(", ", $detailBiayaList);
        $data["biayaTotal"] = $biayaTotal;
        $data["dataBiayaEkspor"] = $dataBiayaEkspor;
        $data['taxList'] = implode(', ', $taxList);
        $data['taxReturnList'] = implode(', ', $taxReturnList);
        $data['total'] = $total;
        $data['taxTotal'] = $taxTotal;
        $data['taxReturnTotal'] = $taxReturnTotal;
        $data['terbilang'] = penyebut($total < 0 ? $total * -1 : $total);
        $data['taxReturnTerbilang'] = $taxReturnTotal > 0 ? penyebut($taxReturnTotal) : 'nol';
        $data['taxReturnData'] = $taxReturnData;
        $data['taxData'] = $taxData;

        // Bon Merah Bukti Penerimaan
        $totalDikembalikan = 0;
        foreach ($taxReturnData as $t) :
            $totalDikembalikan += $t['nilai_pajak'];
        endforeach;
        $data['totalDikembalikan'] = $totalDikembalikan;

        $this->dompdf->loadHtml(view('BiayaExim/BiayaEskpor/print', $data));
        $this->dompdf->setPaper('legal', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function getDetailOrderForm()
    {
        try {
            $salesOrderExportId = $this->request->getVar('sales_order_export_id');
            if (empty($salesOrderExportId)) {
                return \response()->setJSON([
                    'dataSalesExportDetail' => null,
                    'dataSalesOrderExport' => null,
                    'status' => true
                ]);
            }
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

    public function store()
    {
        // return response()->setJSON([
        //     'POST' => $_POST,
        //     'listPajak' => \json_decode($_POST['listPajak']),
        //     'listBiayaEkspor' => \json_decode($_POST['listBiayaEkspor'])
        // ]);

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $noInvoice = $this->request->getVar('no_invoice');
            $tanggalInvoice = $this->request->getVar('tanggal_invoice');
            if ($noInvoice == "AUTO GENERATE") {
                $noInvoice = $this->biayaEksporModel->generateNumber(
                    $tanggalInvoice
                );
            }

            // if ($this->checkNumber($noInvoice, null) == false) {
            //     return response()->setJSON([
            //         'status' => false,
            //         'message' => "No invoice sudah digunakan"
            //     ]);
            // }

            $biayaEksporId = $this->biayaEksporModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'vendor_pelayaran_id' => $this->request->getVar('vendor_pelayaran_id'),
                'sales_order_export_id' => !empty($this->request->getVar('sales_order_export_id')) ? $this->request->getVar('sales_order_export_id') : null,
                'no_invoice' => $noInvoice,
                'tanggal_invoice' =>  $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : null,
                'no_container' => $this->request->getVar('no_container'),
                'no_seal' => $this->request->getVar('no_seal'),
                'nama_kapal' => $this->request->getVar('nama_kapal'),
                'keberangkatan_kapal' =>  $this->request->getVar("keberangkatan_kapal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("keberangkatan_kapal")))) : null,
                'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
                'tanggal_surat_jalan' =>  $this->request->getVar("tanggal_surat_jalan") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_surat_jalan")))) : null,
                'no_kendaraan' => $this->request->getVar('no_kendaraan'),
                'detail_kendaraan' => $this->request->getVar('detail_kendaraan'),
                'payment_term' => $this->request->getVar('payment_term'),
                'destination' => $this->request->getVar('destination'),
                'no_container_order_form' => $this->request->getVar('no_container_order_form'),
                'po_no' => $this->request->getVar('po_no'),
                'total_faktur_before_tax' => $this->request->getVar('total_faktur_before_tax'),
                'total_faktur' => $this->request->getVar('total_faktur'),
                'customer_id' => $this->request->getVar('customer_id'),
            ]);

            foreach (\json_decode($_POST['listBarang']) as $l) {
                $this->biayaEksporBarangModel->insert([
                    'biaya_ekspor_id' => $biayaEksporId,
                    'barang_master_sales_id' => $l->barang_id,
                    'valas_id' => $l->valas_id,
                    'satuan_id' => $l->satuan_id,
                    'qty_barang' => $l->qty_barang,
                    'harga_satuan' => $l->harga_satuan,
                    'total_harga' => $l->total_harga
                ]);
            }

            foreach (\json_decode($_POST['listBiayaEkspor']) as $l) {
                $this->biayaEksporDetailModel->insert([
                    'biaya_ekspor_id' => $biayaEksporId,
                    'nilai_biaya' => $l->nilai_biaya,
                    'uraian_biaya' => $l->uraian_biaya
                ]);
            }

            foreach (\json_decode($_POST['listPajak']) as $l) {
                $this->biayaEksporPajakModel->insert([
                    'biaya_ekspor_id' => $biayaEksporId,
                    'tax_id' => $l->tax_id,
                    'no_faktur_pajak' => $l->no_faktur_pajak,
                    'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                    'status_pajak' => $l->tax_status,
                    'nilai_pajak' => $l->nilai_pajak,
                    'keterangan_pajak' => $l->keterangan_pajak
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data berhasil disimpan",
                'token' => csrf_hash(),
                'status' => true,
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return \response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage() . " " . $e->getFile() . " at " . $e->getLine()
            ]);
        }
    }

    public function update()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $id = \decrypt($this->request->getVar('id'));
            $noInvoice = $this->request->getVar('no_invoice');

            // if ($this->checkNumber($noInvoice, $id) == false) {
            //     return response()->setJSON([
            //         'status' => false,
            //         'message' => "No invoice sudah digunakan"
            //     ]);
            // }

            $this->biayaEksporModel->update($id, [
                'divisi_id' => $this->request->getVar('divisi_id'),
                'vendor_pelayaran_id' => $this->request->getVar('vendor_pelayaran_id'),
                'sales_order_export_id' => !empty($this->request->getVar('sales_order_export_id')) ? $this->request->getVar('sales_order_export_id') : null,
                'no_invoice' => $noInvoice,
                'tanggal_invoice' =>  $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : null,
                'no_container' => $this->request->getVar('no_container'),
                'no_seal' => $this->request->getVar('no_seal'),
                'nama_kapal' => $this->request->getVar('nama_kapal'),
                'keberangkatan_kapal' =>  $this->request->getVar("keberangkatan_kapal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("keberangkatan_kapal")))) : null,
                'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
                'tanggal_surat_jalan' =>  $this->request->getVar("tanggal_surat_jalan") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_surat_jalan")))) : null,
                'no_kendaraan' => $this->request->getVar('no_kendaraan'),
                'detail_kendaraan' => $this->request->getVar('detail_kendaraan'),
                'payment_term' => $this->request->getVar('payment_term'),
                'destination' => $this->request->getVar('destination'),
                'no_container_order_form' => $this->request->getVar('no_container_order_form'),
                'po_no' => $this->request->getVar('po_no'),
                'total_faktur_before_tax' => $this->request->getVar('total_faktur_before_tax'),
                'total_faktur' => $this->request->getVar('total_faktur'),
                'customer_id' => $this->request->getVar('customer_id'),
            ]);

            $idBiayaEksporDetailUsedArr = array();
            $idBiayaEksporPajakUsedArr = array();
            foreach (\json_decode($_POST['listBiayaEkspor']) as $l) {
                $check = $this->biayaEksporDetailModel
                    ->where('id', $l->id_biaya_ekspor_detail)
                    ->first();

                if ($check != null) {
                    // Update
                    $this->biayaEksporDetailModel->update($check['id'], [
                        'nilai_biaya' => $l->nilai_biaya,
                        'uraian_biaya' => $l->uraian_biaya
                    ]);
                    \array_push($idBiayaEksporDetailUsedArr, $check['id']);
                } else {
                    // Create
                    $biayaEksporDetailId =   $this->biayaEksporDetailModel->insert([
                        'biaya_ekspor_id' => $id,
                        'nilai_biaya' => $l->nilai_biaya,
                        'uraian_biaya' => $l->uraian_biaya
                    ]);
                    \array_push($idBiayaEksporDetailUsedArr, $biayaEksporDetailId);
                }
            }

            foreach (\json_decode($_POST['listPajak']) as $l) {
                $check = $this->biayaEksporPajakModel
                    ->where('id', $l->id_biaya_ekspor_pajak)
                    ->first();


                if ($check != null) {
                    // Update
                    $this->biayaEksporPajakModel->update($check['id'], [
                        'tax_id' => $l->tax_id,
                        'no_faktur_pajak' => $l->no_faktur_pajak,
                        'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                        'status_pajak' => $l->tax_status,
                        'nilai_pajak' => $l->nilai_pajak,
                        'keterangan_pajak' => $l->keterangan_pajak
                    ]);
                    \array_push($idBiayaEksporPajakUsedArr, $check['id']);
                } else {
                    // Create
                    $biayaEksporPajakId =  $this->biayaEksporPajakModel->insert([
                        'biaya_ekspor_id' => $id,
                        'tax_id' => $l->tax_id,
                        'no_faktur_pajak' => $l->no_faktur_pajak,
                        'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                        'status_pajak' => $l->tax_status,
                        'nilai_pajak' => $l->nilai_pajak,
                        'keterangan_pajak' => $l->keterangan_pajak
                    ]);
                    \array_push($idBiayaEksporPajakUsedArr, $biayaEksporPajakId);
                }
            }

            $this->biayaEksporDetailModel->whereNotIn('id', $idBiayaEksporDetailUsedArr)
                ->where('biaya_ekspor_id', $id)
                ->delete();

            $this->biayaEksporPajakModel->whereNotIn('id', $idBiayaEksporPajakUsedArr)
                ->where('biaya_ekspor_id', $id)
                ->delete();

            $this->biayaEksporBarangModel->where('biaya_ekspor_id', $id)->delete();
            foreach (\json_decode($_POST['listBarang']) as $l) {
                $this->biayaEksporBarangModel->insert([
                    'biaya_ekspor_id' => $id,
                    'barang_master_sales_id' => $l->barang_id,
                    'valas_id' => $l->valas_id,
                    'satuan_id' => $l->satuan_id,
                    'qty_barang' => $l->qty_barang,
                    'harga_satuan' => $l->harga_satuan,
                    'total_harga' => $l->total_harga
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data berhasil diupdate",
                'token' => csrf_hash(),
                'status' => true,
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return \response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy()
    {
        try {
            $id = \decrypt($this->request->getVar('id'));
            $this->biayaEksporModel->delete($id);
            $this->biayaEksporDetailModel->where('biaya_ekspor_id', $id)->delete();
            $this->biayaEksporPajakModel->where('biaya_ekspor_id', $id)->delete();
            $this->biayaEksporBarangModel->where('biaya_ekspor_id', $id)->delete();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Data berhasil dihapus"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function posting()
    {
        $id = \decrypt($this->request->getVar('id'));
        $this->biayaEksporModel->update($id, [
            'status_posting' => 1
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Data berhasil diposting"
        ]);
    }

    public function unposting()
    {
        $id = \decrypt($this->request->getVar('id'));
        $this->biayaEksporModel->update($id, [
            'status_posting' => 0
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Data berhasil diunposting"
        ]);
    }

    private function checkNumber($noInvoice, $id)
    {
        $checkQry = $this->biayaEksporModel
            ->where('company_id', $this->this_company_id)
            ->where('no_invoice', $noInvoice)
            ->where('deletedAt', null);
        if ($id != null) {
            $checkQry->where('id !=', $id);
        }

        $resultCheck = $checkQry->first();
        if ($resultCheck == null) {
            return true;
        } else {
            return false;
        }
    }

    public function getSalesOrderExportByCustomerId()
    {
        $customerId = $this->request->getVar('customer_id');
        if (!empty($customerId)) {
            $dataResult = $this->salesOrderExportModel->getSalesOrderExportByCustomerId(
                $customerId
            );
            return response()->setJSON([
                'data' => $dataResult,
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    public function exportExcel()
    {
        $condition = [
            "biaya_ekspor.company_id" => $this->this_company_id,
            "biaya_ekspor.deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "status_posting" => $this->request->getGet("status_posting"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $biayaEkspor = $this->biayaEksporModel->getList($condition, $addCondition, 100000000, 0);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // === Header utama ===
        $headers = [
            'NO',
            'NO INVOICE',
            'TANGGAL INVOICE',
            'DEPARTEMEN',
            'CUSTOMER',
            'PAYMENT TERM',
            'DESTINATION',
            'NO PO',
            'NO CONTAINER',
            'NO SEAL',
            'NAMA KAPAL',
            'KEBERANGKATAN KAPAL',
            'NO SURAT JALAN',
            'TANGGAL SURAT JALAN',
            'NO KENDARAAN',
            'DETAIL KENDARAAN',
            'VENDOR / PELAYARAN',
            'TOTAL INVOICE'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', strtoupper($header));
            $col++;
        }

        // Style header utama
        $sheet->getStyle('A1:R1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1:R1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDCE6F1'); // biru lembut
        $sheet->getStyle('A1:R1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        foreach (range('A', 'R') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $row = 2;
        $no = 1;

        foreach ($biayaEkspor['data'] as $data) {

            // Baris utama
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $data['no_invoice']);
            $sheet->setCellValue("C{$row}", $data['tanggal_invoice']);
            $sheet->setCellValue("D{$row}", $data['divisi']);
            $sheet->setCellValue("E{$row}", $data['customer_name']);
            $sheet->setCellValue("F{$row}", $data['payment_term']);
            $sheet->setCellValue("G{$row}", $data['destination']);
            $sheet->setCellValue("H{$row}", $data['po_no']);
            $sheet->setCellValue("I{$row}", $data['no_container']);
            $sheet->setCellValue("J{$row}", $data['no_seal']);
            $sheet->setCellValue("K{$row}", $data['nama_kapal']);
            $sheet->setCellValue("L{$row}", $data['keberangkatan_kapal']);
            $sheet->setCellValue("M{$row}", $data['no_surat_jalan']);
            $sheet->setCellValue("N{$row}", $data['tanggal_surat_jalan']);
            $sheet->setCellValue("O{$row}", $data['no_kendaraan']);
            $sheet->setCellValue("P{$row}", $data['detail_kendaraan']);
            $sheet->setCellValue("Q{$row}", $data['nama_vendor']);
            $sheet->setCellValue("R{$row}", $data['total_faktur']);

            // Format angka rupiah (tanpa Rp)
            $sheet->getStyle("R{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $row++;

            // === Detail Pajak ===
            $pajakHeader = ['TGL FAKTUR PAJAK', 'NO FAKTUR PAJAK', 'PAJAK', 'NILAI PAJAK', 'STATUS', 'KETERANGAN'];
            $col = 'B';
            foreach ($pajakHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailPajak = $this->biayaEksporPajakModel
                ->select('biaya_ekspor_pajak.*, taxes.type as type_tax, taxes.name as tax_name')
                ->join('taxes', 'taxes.id = biaya_ekspor_pajak.tax_id', 'left')
                ->where('biaya_ekspor_pajak.biaya_ekspor_id', $data['id'])
                ->where('biaya_ekspor_pajak.deletedAt', null)
                ->findAll();

            if ($detailPajak) {
                foreach ($detailPajak as $pjk) {
                    $sheet->setCellValue("B{$row}", $pjk['tanggal_faktur_pajak']);
                    $sheet->setCellValue("C{$row}", $pjk['no_faktur_pajak']);
                    $sheet->setCellValue("D{$row}", $pjk['tax_name']);
                    $sheet->setCellValue("E{$row}", $pjk['nilai_pajak']);
                    $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->setCellValue("F{$row}", $pjk['status_pajak']);
                    $sheet->setCellValue("G{$row}", $pjk['keterangan_pajak']);
                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada pajak -");
                $row++;
            }

            // === Detail Biaya ===
            $biayaHeader = ['DETAIL BIAYA', 'NILAI BIAYA'];
            $col = 'B';
            foreach ($biayaHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailBiaya = $this->biayaEksporDetailModel
                ->where('biaya_ekspor_id', $data['id'])
                ->where('deletedAt', null)
                ->findAll();

            if ($detailBiaya) {
                foreach ($detailBiaya as $by) {
                    $sheet->setCellValue("B{$row}", $by['uraian_biaya']);
                    $sheet->setCellValue("C{$row}", $by['nilai_biaya']);
                    $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada biaya -");
                $row++;
            }

            // === Detail Barang ===
            $barangHeader = ['KODE BARANG', 'BARANG', 'QTY', 'SATUAN', 'HARGA SATUAN', 'TOTAL HARGA'];
            $col = 'B';
            foreach ($barangHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailBarang = $this->biayaEksporBarangModel
                ->getListBarang($data['id']);

            if ($detailBarang) {
                foreach ($detailBarang as $by) {
                    $sheet->setCellValue("B{$row}", $by['kode_barang']);
                    $sheet->setCellValue("C{$row}", $by['barang_name']);
                    $sheet->setCellValue("D{$row}", $by['qty_barang']);
                    $sheet->setCellValue("E{$row}", $by['kode_satuan']);
                    $sheet->setCellValue("F{$row}", $by['harga_satuan']);
                    $sheet->setCellValue("G{$row}", $by['total_harga']);

                    $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada barang -");
                $row++;
            }

            // Baris kosong sebelum invoice berikutnya
            $row++;
        }

        $fileName = 'Export_Biaya_Ekspor_' . time() . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function getStatusPosting()
    {
        try {
            $id = \decrypt($this->request->getVar('id'));
            $biayaEkspor = $this->biayaEksporModel
                ->where('id', $id)
                ->first();

            return response()->setJSON([
                'status' => true,
                'token' => \csrf_hash(),
                'data' => $biayaEkspor
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'token' => \csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateStatusPosting()
    {
        try {
            $id = $this->request->getVar('id');
            $statusPostingExim = $this->request->getVar('status_posting_exim');
            $userEximPosted = $this->request->getVar('user_exim_posted');
            $statusPostingAcc = $this->request->getVar('status_posting_acc');
            $userAccPosted = $this->request->getVar('user_acc_posted');
            $statusPostingAudit = $this->request->getVar('status_posting_audit');
            $userAuditPosted = $this->request->getVar('user_audit_posted');

            $this->biayaEksporModel->update($id, [
                'status_posting_exim' => $statusPostingExim,
                'user_exim_posted' => $userEximPosted,
                'status_posting_acc' => $statusPostingAcc,
                'user_acc_posted' => $userAccPosted,
                'status_posting_audit' => $statusPostingAudit,
                'user_audit_posted' => $userAuditPosted
            ]);

            return \response()->setJSON([
                'status' => true,
                'token' => \csrf_hash(),
                'message' => "Status posting berhasil diperbaruhi"
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'token' => \csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function detail($id)
    {
        $id = \decrypt($id);
        $dataBiayaEskpor = $this->biayaEksporModel->getById($id);
        $dataDetailBarang = $this->biayaEksporBarangModel->getListBarang($id);
        $dataBiayaEksporPajak = $this->biayaEksporPajakModel
            ->select('biaya_ekspor_pajak.*,taxes.type as type_tax, taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_ekspor_pajak.tax_id', 'left')
            ->where('biaya_ekspor_pajak.biaya_ekspor_id', $id)
            ->where('biaya_ekspor_pajak.deletedAt', null)
            ->findAll();
        $dataBiayaEksporDetail = $this->biayaEksporDetailModel
            ->where('biaya_ekspor_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        $data = [
            'dataBiayaEskpor' => $dataBiayaEskpor,
            'dataDetailBarang' => $dataDetailBarang,
            'dataBiayaEksporPajak' => $dataBiayaEksporPajak,
            'dataBiayaEksporDetail' => $dataBiayaEksporDetail
        ];

        return \view('BiayaExim/BiayaEskpor/detail', $data);
    }

    public function getDataBiayaEkspor()
    {
        try {
            $id = $this->request->getVar('id');

            if (empty($id)) {
                return \response()->setJSON([
                    'data' => null,
                    'status' => true,
                    'token' => \csrf_hash()
                ]);
            }

            $dataBiayaEkspor = $this->biayaEksporModel->where('id', $id)->first();
            $dataDetailBarang = $this->biayaEksporBarangModel->getListBarang($id);
            $dataBiayaEkspor['tanggal_invoice'] = \date('d/m/Y', \strtotime($dataBiayaEkspor['tanggal_invoice']));
            $dataBiayaEkspor['keberangkatan_kapal'] = $dataBiayaEkspor['keberangkatan_kapal'] != null ? \date('d/m/Y', \strtotime($dataBiayaEkspor['keberangkatan_kapal'])) : "";
            $dataBiayaEkspor['tanggal_surat_jalan'] = $dataBiayaEkspor['tanggal_surat_jalan'] != null ? \date('d/m/Y', \strtotime($dataBiayaEkspor['tanggal_surat_jalan'])) : "";

            return \response()->setJSON([
                'data' => [
                    'dataBiayaEkspor' => $dataBiayaEkspor,
                    'dataDetailBarang' => $dataDetailBarang
                ],
                'status' => true,
                'token' => \csrf_hash()
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => \csrf_hash()
            ]);
        }
    }
}
