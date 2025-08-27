<?php

namespace App\Controllers\BiayaExim\BiayaImpor;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BiayaImporBarangModel;
use App\Models\BiayaImporContainerModel;
use App\Models\BiayaImporDetailModel;
use App\Models\BiayaImporModel;
use App\Models\BiayaImporPajakModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\SatuansModel;
use App\Models\SupplierModel;
use App\Models\TaxModel;
use App\Models\UserModel;
use App\Models\VendorPelayaranModel;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BiayaImpor extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $taxesModel;
    protected $vendorPelayaranModel;
    protected $dompdf;
    protected $supplierModel;
    protected $metadataModel;
    protected $biayaImporModel;
    protected $biayaImporDetailModel;
    protected $biayaImporPajakModel;
    protected $biayaImporContainerModel;
    protected $satuanModel;
    protected $barangMasterModel;
    protected $biayaImporBarangModel;
    protected $usersModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->taxesModel = new TaxModel();
        $this->vendorPelayaranModel = new VendorPelayaranModel();
        $this->supplierModel = new SupplierModel();
        $this->dompdf = new Dompdf();
        $this->metadataModel = new MetadataModel();
        $this->biayaImporModel = new BiayaImporModel();
        $this->biayaImporDetailModel = new BiayaImporDetailModel();
        $this->biayaImporPajakModel = new BiayaImporPajakModel();
        $this->biayaImporContainerModel = new BiayaImporContainerModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->satuanModel = new SatuansModel();
        $this->usersModel = new UserModel();
        $this->biayaImporBarangModel = new BiayaImporBarangModel();
    }

    public function index()
    {
        $dataUser = $this->usersModel->orderBy('name', 'asc')->where('deletedAt', null)->findAll();
        $data = [
            'dataUser' => $dataUser,
        ];
        return view('BiayaExim/BiayaImpor/index', $data);
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
            "biaya_impor.company_id"  => $this->this_company_id,
            "biaya_impor.deletedAt" => NULL,
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

        $dataResult = $this->biayaImporModel->getList(
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
                "supplier_name"        => $data['supplier_name'],
                "port_of_origin"        => $data['port_of_origin'],
                "port_of_destination"        => $data['port_of_destination'],
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
        $dataSupplier = $this->supplierModel->getSupplierByType("INTERNASIONAL");
        $dataValas = $this->metadataModel->where('name', "Valuta")->where('deletedAt', null)->orderBy('value', "ASC")->findAll();
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataBarang = $this->barangMasterModel->getBarangByTypeWithSpec([
            'barang_master.company_id' => $this->this_company_id,
            'barang_master.deletedAt' => null,
            'barang_master_spesifikasi.deletedAt' => null
        ]);
        $dataBiayaImporList = $this->biayaImporModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('id', "desc")->findAll();

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataSupplier" => $dataSupplier,
            "dataValas" => $dataValas,
            "dataSatuan" => $dataSatuan,
            "dataBarang" => $dataBarang,
            "dataBiayaImporList" => $dataBiayaImporList
        ];


        return view('BiayaExim/BiayaImpor/form', $data);
    }

    public function edit($id)
    {
        $id = \decrypt($id);
        $dataBiayaImpor = $this->biayaImporModel->where('id', $id)->first();
        if ($dataBiayaImpor == null) {
            return \redirect()->to('biaya-impor');
        }

        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataPajak = $this->taxesModel
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%', 'PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('type', "asc")
            ->findAll();
        $dataVendorPelayaran = $this->vendorPelayaranModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('nama_vendor', "asc")->findAll();
        $dataSupplier = $this->supplierModel->getSupplierByType("INTERNASIONAL");
        $dataValas = $this->metadataModel->where('name', "Valuta")->where('deletedAt', null)->orderBy('value', "ASC")->findAll();
        $dataBiayaImporPajak = $this->biayaImporPajakModel
            ->select(
                '
                biaya_impor_pajak.*,
                taxes.type as type_tax, 
                taxes.name as tax_name
            '
            )
            ->join('taxes', 'taxes.id = biaya_impor_pajak.tax_id', 'left')
            ->where('biaya_impor_id', $id)
            ->where('biaya_impor_pajak.deletedAt', null)
            ->findAll();
        $dataBiayaImporDetail = $this->biayaImporDetailModel
            ->select('biaya_impor_detail.*,metadata.value as valas_name')
            ->join('metadata', 'metadata.id = biaya_impor_detail.valas_id', 'left')
            ->where('biaya_impor_id', $id)
            ->where('biaya_impor_detail.deletedAt', null)
            ->findAll();

        $dataContainer = $this->biayaImporContainerModel
            ->where('biaya_impor_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        $dataDetailBarang = $this->biayaImporBarangModel->getListBarang(
            $id
        );

        $dataPoDetail = null;
        if ($dataBiayaImpor['po_id'] != null && !empty($dataBiayaImpor['po_id'])) {
            $dataPoDetail = $this->biayaImporModel->getDetailBarangPo(
                $dataBiayaImpor['po_id'],
                $dataBiayaImpor['tipe_po']
            )['po_detail'];
        }
        $dataSatuan = $this->satuanModel->where('deletedAt', null)->findAll();
        $dataBarang = $this->barangMasterModel->getBarangByTypeWithSpec([
            'barang_master.company_id' => $this->this_company_id,
            'barang_master.deletedAt' => null,
            'barang_master_spesifikasi.deletedAt' => null
        ]);

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataSupplier" => $dataSupplier,
            "dataValas" => $dataValas,
            "dataBiayaImporPajak" => $dataBiayaImporPajak,
            "dataBiayaImporDetail" => $dataBiayaImporDetail,
            "dataDetailBarang" => $dataDetailBarang,
            "dataBiayaImpor" => $dataBiayaImpor,
            "dataContainer" => $dataContainer,
            "dataSatuan" => $dataSatuan,
            "dataBarang" => $dataBarang,
            "dataPoDetail" => $dataPoDetail
        ];

        return view('BiayaExim/BiayaImpor/form', $data);
    }
    public function print($id)
    {

        $id = decrypt($id);
        $dataBiayaImpor = $this->biayaImporModel->where('id', $id)->first();
        if ($dataBiayaImpor == null) {
            return redirect()->to('biaya-impor');
        }

        $filename = $dataBiayaImpor['no_invoice'];

        $data = [];
        $detailBiayaList = [];
        $taxList = [];
        $taxReturnList = [];
        $biayaTotal = 0;
        $taxTotal = 0;
        $taxReturnTotal = 0;

        $dataBiayaImpor = $this->biayaImporModel
            ->select("biaya_impor.*, vendor_pelayaran.nama_vendor")
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_impor.vendor_pelayaran_id', 'left')
            ->where('biaya_impor.id', $id)
            ->first();

        $dataBiayaImporDetail = $this->biayaImporDetailModel
            ->where('biaya_impor_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        // Pajak Bukti Pengeluaran (Bon Putih)
        $taxData = $this->biayaImporPajakModel
            ->select('biaya_impor_pajak.*,taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_impor_pajak.tax_id', 'left')
            ->where('biaya_impor_id', $id)
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%'])
            ->where('biaya_impor_pajak.deletedAt', null)
            ->findAll();

        // Pajak Bukti Pemasukkan (Bon Merah)
        $taxReturnData = $this->biayaImporPajakModel
            ->select('biaya_impor_pajak.*,taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_impor_pajak.tax_id', 'left')
            ->where('biaya_impor_id', $id)
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('biaya_impor_pajak.deletedAt', null)
            ->findAll();

        foreach ($dataBiayaImporDetail as $det) {
            $detailBiayaList[] = $det['uraian_biaya'];
            $biayaTotal += $det['nilai_biaya_idr'];
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
        $data["dataBiayaImpor"] = $dataBiayaImpor;
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

        $this->dompdf->loadHtml(view('BiayaExim/BiayaImpor/print', $data));
        $this->dompdf->setPaper('legal', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function store()
    {
        // return response()->setJSON([
        //     'POST' => $_POST,
        //     'listPajak' => \json_decode($_POST['listPajak']),
        //     'listBiayaEkspor' => \json_decode($_POST['listBiayaImpor']),
        //     'listContainer' => \json_decode($_POST['listContainer']),
        //     'listBarang' => \json_decode($_POST['listBarang'])
        // ]);

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $noInvoice = $this->request->getVar('no_invoice');
            $tanggalInvoice = $this->request->getVar('tanggal_invoice');
            if ($noInvoice == "AUTO GENERATE") {
                $noInvoice = $this->biayaImporModel->generateNumber(
                    $tanggalInvoice
                );
            }

            // if ($this->checkNumber($noInvoice, null) == false) {
            //     return response()->setJSON([
            //         'status' => false,
            //         'message' => "No invoice sudah digunakan"
            //     ]);
            // }
            $biayaImporId = $this->biayaImporModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'vendor_pelayaran_id' => $this->request->getVar('vendor_pelayaran_id'),
                'po_id' => !empty($this->request->getVar('po_id')) ? $this->request->getVar('po_id') : null,
                'supplier_id' => $this->request->getVar('supplier_id'),
                'tanggal_invoice' =>  $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : null,
                'no_invoice' => $noInvoice,
                'tipe_po' => !empty($this->request->getVar('tipe_po')) ? $this->request->getVar('tipe_po') : null,
                'no_bl' => $this->request->getVar('no_bl'),
                'shipper' => !empty($this->request->getVar('shipper_prev')) ? $this->request->getVar('shipper_prev') : null,
                'consigne' => !empty($this->request->getVar('consigne_prev')) ? $this->request->getVar('consigne_prev') : null,
                'port_of_origin' => !empty($this->request->getVar('port_of_origin_prev')) ? $this->request->getVar('port_of_origin_prev') : null,
                'port_of_destination' => !empty($this->request->getVar('port_of_destination_prev')) ? $this->request->getVar('port_of_destination_prev') : null,
                'total_faktur_before_tax' => $this->request->getVar('total_faktur_before_tax'),
                'total_faktur' => $this->request->getVar('total_faktur'),
            ]);

            foreach (\json_decode($_POST['listBiayaImpor']) as $l) {
                $this->biayaImporDetailModel->insert([
                    'biaya_impor_id' => $biayaImporId,
                    'valas_id' => $l->valas_id,
                    'uraian_biaya' => $l->uraian_biaya,
                    'nilai_biaya' => $l->nilai_biaya,
                    'nilai_exchange_rate' => $l->nilai_exchange_rate,
                    'nilai_biaya_idr' => $l->nilai_biaya_idr
                ]);
            }

            foreach (\json_decode($_POST['listPajak']) as $l) {
                $this->biayaImporPajakModel->insert([
                    'biaya_impor_id' => $biayaImporId,
                    'tax_id' => $l->tax_id,
                    'no_faktur_pajak' => $l->no_faktur_pajak,
                    'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                    'status_pajak' => $l->tax_status,
                    'nilai_pajak' => $l->nilai_pajak,
                    'keterangan_pajak' => $l->keterangan_pajak
                ]);
            }

            foreach (\json_decode($_POST['listContainer']) as $l) {
                $this->biayaImporContainerModel->insert([
                    'biaya_impor_id' => $biayaImporId,
                    'no_container' => $l->no_container,
                    'detail_container' => $l->detail_container
                ]);
            }

            foreach (\json_decode($_POST['listBarang']) as $l) {
                $this->biayaImporBarangModel->insert([
                    'biaya_impor_id' => $biayaImporId,
                    'spesifikasi_id' => $l->spesifikasi_id,
                    'valas_id' => $l->valas_id,
                    'satuan_id' => $l->satuan_id,
                    'qty_barang' => $l->qty_barang,
                    'harga_satuan' => $l->harga_satuan,
                    'total_harga' => $l->total_harga
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data berhasil disimpan",
                'token' => csrf_hash(),
                'status' => true,
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        // return response()->setJSON([
        //     'POST' => $_POST,
        //     'listPajak' => \json_decode($_POST['listPajak']),
        //     'listBiayaEkspor' => \json_decode($_POST['listBiayaImpor']),
        //     'listContainer' => \json_decode($_POST['listContainer'])
        // ]);

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = \decrypt($this->request->getVar('id'));
            // dd($id);
            $noInvoice = $this->request->getVar('no_invoice');
            // if ($this->checkNumber($noInvoice, $id) == false) {
            //     return response()->setJSON([
            //         'status' => false,
            //         'message' => "No invoice sudah digunakan"
            //     ]);
            // }
            $this->biayaImporModel->update($id, [
                'divisi_id' => $this->request->getVar('divisi_id'),
                'vendor_pelayaran_id' => $this->request->getVar('vendor_pelayaran_id'),
                'po_id' => !empty($this->request->getVar('po_id')) ? $this->request->getVar('po_id') : null,
                'supplier_id' => $this->request->getVar('supplier_id'),
                'tanggal_invoice' =>  $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : null,
                'no_invoice' => $noInvoice,
                'tipe_po' => !empty($this->request->getVar('tipe_po')) ? $this->request->getVar('tipe_po') : null,
                'no_bl' => $this->request->getVar('no_bl'),
                'shipper' => !empty($this->request->getVar('shipper_prev')) ? $this->request->getVar('shipper_prev') : null,
                'consigne' => !empty($this->request->getVar('consigne_prev')) ? $this->request->getVar('consigne_prev') : null,
                'port_of_origin' => !empty($this->request->getVar('port_of_origin_prev')) ? $this->request->getVar('port_of_origin_prev') : null,
                'port_of_destination' => !empty($this->request->getVar('port_of_destination_prev')) ? $this->request->getVar('port_of_destination_prev') : null,
                'total_faktur_before_tax' => $this->request->getVar('total_faktur_before_tax'),
                'total_faktur' => $this->request->getVar('total_faktur'),
            ]);

            $idBiayaImporUsed = array();
            $idBiayaImporPajakUsed = array();
            $idBiayaImporContainerUsed = array();

            foreach (\json_decode($_POST['listBiayaImpor']) as $l) {
                $check = $this->biayaImporModel
                    ->where('id', $l->id_biaya_impor_detail)
                    ->first();

                if ($check != null) {
                    $this->biayaImporDetailModel->update($check['id'], [
                        'valas_id' => $l->valas_id,
                        'uraian_biaya' => $l->uraian_biaya,
                        'nilai_biaya' => $l->nilai_biaya,
                        'nilai_exchange_rate' => $l->nilai_exchange_rate,
                        'nilai_biaya_idr' => $l->nilai_biaya_idr
                    ]);

                    \array_push($idBiayaImporUsed, $check['id']);
                } else {
                    $biayaImporDetailId = $this->biayaImporDetailModel->insert([
                        'biaya_impor_id' => $id,
                        'valas_id' => $l->valas_id,
                        'uraian_biaya' => $l->uraian_biaya,
                        'nilai_biaya' => $l->nilai_biaya,
                        'nilai_exchange_rate' => $l->nilai_exchange_rate,
                        'nilai_biaya_idr' => $l->nilai_biaya_idr
                    ]);
                    \array_push($idBiayaImporUsed, $biayaImporDetailId);
                }
            }

            foreach (\json_decode($_POST['listPajak']) as $l) {
                $check = $this->biayaImporPajakModel
                    ->where('id', $l->id_biaya_impor_pajak)
                    ->first();

                if ($check != null) {
                    $this->biayaImporPajakModel->update($check['id'], [
                        'biaya_impor_id' => $id,
                        'tax_id' => $l->tax_id,
                        'no_faktur_pajak' => $l->no_faktur_pajak,
                        'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                        'status_pajak' => $l->tax_status,
                        'nilai_pajak' => $l->nilai_pajak,
                        'keterangan_pajak' => $l->keterangan_pajak
                    ]);
                    \array_push($idBiayaImporPajakUsed, $check['id']);
                } else {
                    $biayaImporPajakId = $this->biayaImporPajakModel->insert([
                        'biaya_impor_id' => $id,
                        'tax_id' => $l->tax_id,
                        'no_faktur_pajak' => $l->no_faktur_pajak,
                        'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                        'status_pajak' => $l->tax_status,
                        'nilai_pajak' => $l->nilai_pajak,
                        'keterangan_pajak' => $l->keterangan_pajak
                    ]);
                    \array_push($idBiayaImporPajakUsed, $biayaImporPajakId);
                }
            }

            foreach (\json_decode($_POST['listContainer']) as $l) {
                $check = $this->biayaImporContainerModel
                    ->where('id', $l->id_container)
                    ->first();

                if ($check != null) {
                    $this->biayaImporModel->update($check['id'], [
                        'biaya_impor_id' => $id,
                        'no_container' => $l->no_container,
                        'detail_container' => $l->detail_container
                    ]);
                    \array_push($idBiayaImporContainerUsed, $check['id']);
                } else {
                    $biayaImporContainerId = $this->biayaImporContainerModel->insert([
                        'biaya_impor_id' => $id,
                        'no_container' => $l->no_container,
                        'detail_container' => $l->detail_container
                    ]);
                    \array_push($idBiayaImporContainerUsed, $biayaImporContainerId);
                }
            }

            $this->biayaImporBarangModel->where('biaya_impor_id', $id)->delete();
            foreach (\json_decode($_POST['listBarang']) as $l) {
                $this->biayaImporBarangModel->insert([
                    'biaya_impor_id' => $id,
                    'spesifikasi_id' => $l->spesifikasi_id,
                    'valas_id' => $l->valas_id,
                    'satuan_id' => $l->satuan_id,
                    'qty_barang' => $l->qty_barang,
                    'harga_satuan' => $l->harga_satuan,
                    'total_harga' => $l->total_harga
                ]);
            }

            $this->biayaImporContainerModel->whereNotIn('id', $idBiayaImporContainerUsed)
                ->where('biaya_impor_id', $id)
                ->delete();
            $this->biayaImporDetailModel->whereNotIn('id', $idBiayaImporUsed)
                ->where('biaya_impor_id', $id)
                ->delete();
            $this->biayaImporPajakModel->whereNotIn('id', $idBiayaImporPajakUsed)
                ->where('biaya_impor_id', $id)
                ->delete();

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data berhasil diupdate",
                'token' => csrf_hash(),
                'status' => true,
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy()
    {
        try {
            $id = \decrypt($this->request->getVar('id'));
            $this->biayaImporModel->delete($id);
            $this->biayaImporDetailModel->where('biaya_impor_id', $id)->delete();
            $this->biayaImporPajakModel->where('biaya_impor_id', $id)->delete();
            $this->biayaImporContainerModel->where('biaya_impor_id', $id)->delete();
            $this->biayaImporBarangModel->where('biaya_impor_id', $id)->delete();

            return \response()->setJSON([
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



    public function dropdownPo()
    {
        $tipePo = trim($this->request->getVar('tipe_po'));
        $supplierId = $this->request->getVar('supplier_id');

        $resultArray = $this->biayaImporModel->getDropdownPo(
            $supplierId,
            $tipePo
        );

        return response()->setJSON([
            'data' => $resultArray,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getDetailBarangPo()
    {
        try {
            $tipePo = trim($this->request->getVar('tipe_po'));
            $poId = $this->request->getVar('po_id');

            if (!empty($tipePo) && !empty($poId)) {
                $resultArray = $this->biayaImporModel->getDetailBarangPo(
                    $poId,
                    $tipePo
                );

                return response()->setJSON([
                    'data' => $resultArray,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'data' => null,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            }
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'message' => $e->getMessage() . " at " . $e->getFile() . " line " . $e->getLine()
            ]);
        }
    }

    private function checkNumber($noInvoice, $id)
    {
        $checkQry = $this->biayaImporModel
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

    public function exportExcel()
    {
        $condition = [
            "biaya_impor.company_id" => $this->this_company_id,
            "biaya_impor.deletedAt"  => null,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "dateStart"     => $this->request->getVar("dateStart")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
                : "",
            "dateEnd"       => $this->request->getVar("dateEnd")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
                : "",
            "status_posting" => $this->request->getGet("status_posting"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $biayaEkspor = $this->biayaImporModel->getList($condition, $addCondition, 100000000, 0);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        /**
         * ========================
         * Header Utama
         * ========================
         */
        $headers = [
            'NO',
            'NO INVOICE',
            'TANGGAL INVOICE',
            'DEPARTEMEN',
            'SUPPLIER',
            'PORT OF ORIGIN',
            'PORT OF DESTINATION',
            'SHIPPER',
            'CONSIGNE',
            'TOTAL INVOICE (IDR)',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', strtoupper($header));
            $col++;
        }

        // Styling header
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1:J1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDCE6F1'); // biru lembut
        $sheet->getStyle('A1:J1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        /**
         * ========================
         * Isi Data
         * ========================
         */
        $row = 2;
        $no  = 1;

        foreach ($biayaEkspor['data'] as $data) {
            // Data utama
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $data['no_invoice']);
            $sheet->setCellValue("C{$row}", $data['tanggal_invoice']);
            $sheet->setCellValue("D{$row}", $data['divisi']);
            $sheet->setCellValue("E{$row}", $data['supplier_name']);
            $sheet->setCellValue("F{$row}", $data['port_of_origin']);
            $sheet->setCellValue("G{$row}", $data['port_of_destination']);
            $sheet->setCellValue("H{$row}", $data['shipper']);
            $sheet->setCellValue("I{$row}", $data['consigne']);
            $sheet->setCellValue("J{$row}", $data['total_faktur']);

            // Format angka rupiah (tanpa Rp)
            $sheet->getStyle("J{$row}")->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $row++;

            /**
             * ========================
             * Detail Pajak
             * ========================
             */
            $pajakHeader = [
                'TGL FAKTUR PAJAK',
                'NO FAKTUR PAJAK',
                'PAJAK',
                'NILAI PAJAK',
                'STATUS',
                'KETERANGAN'
            ];

            $col = 'B';
            foreach ($pajakHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailPajak = $this->biayaImporPajakModel
                ->select('biaya_impor_pajak.*, taxes.type as type_tax, taxes.name as tax_name')
                ->join('taxes', 'taxes.id = biaya_impor_pajak.tax_id', 'left')
                ->where('biaya_impor_pajak.biaya_impor_id', $data['id'])
                ->where('biaya_impor_pajak.deletedAt', null)
                ->findAll();

            if ($detailPajak) {
                foreach ($detailPajak as $pjk) {
                    $sheet->setCellValue("B{$row}", $pjk['tanggal_faktur_pajak']);
                    $sheet->setCellValue("C{$row}", $pjk['no_faktur_pajak']);
                    $sheet->setCellValue("D{$row}", $pjk['tax_name']);
                    $sheet->setCellValue("E{$row}", $pjk['nilai_pajak']);
                    $sheet->getStyle("E{$row}")->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->setCellValue("F{$row}", $pjk['status_pajak']);
                    $sheet->setCellValue("G{$row}", $pjk['keterangan_pajak']);
                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada pajak -");
                $row++;
            }

            /**
             * ========================
             * Detail Biaya
             * ========================
             */
            $biayaHeader = [
                'DETAIL BIAYA',
                'CURRENCY',
                'NILAI',
                'EXCHANGE RATE',
                'NILAI (IDR)'
            ];

            $col = 'B';
            foreach ($biayaHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailBiaya = $this->biayaImporDetailModel
                ->select('biaya_impor_detail.*, metadata.value as valas_name')
                ->join('metadata', 'metadata.id = biaya_impor_detail.valas_id', 'left')
                ->where('biaya_impor_id', $data['id'])
                ->where('biaya_impor_detail.deletedAt', null)
                ->findAll();

            if ($detailBiaya) {
                foreach ($detailBiaya as $by) {
                    $sheet->setCellValue("B{$row}", $by['uraian_biaya']);
                    $sheet->setCellValue("C{$row}", $by['valas_name']);
                    $sheet->setCellValue("D{$row}", $by['nilai_biaya']);
                    $sheet->setCellValue("E{$row}", $by['nilai_exchange_rate']);
                    $sheet->setCellValue("F{$row}", $by['nilai_biaya_idr']);

                    // Format angka
                    $sheet->getStyle("D{$row}")->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle("E{$row}")->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle("F{$row}")->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada biaya -");
                $row++;
            }


            /**
             * ========================
             * Detail Container
             * ========================
             */
            $containerHeader = [
                'NO CONTAINER',
                'DETAIL CONTAINER',
            ];

            $col = 'B';
            foreach ($containerHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailContainer = $this->biayaImporContainerModel
                ->where('biaya_impor_id', $data['id'])
                ->where('deletedAt', null)
                ->findAll();

            if ($detailContainer) {
                foreach ($detailContainer as $c) {
                    $sheet->setCellValue("B{$row}", $c['no_container']);
                    $sheet->setCellValue("C{$row}", $c['detail_container']);
                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada container -");
                $row++;
            }

            // === Detail Barang ===
            $barangHeader = ['KODE BARANG', 'BARANG', 'QTY', 'SATUAN', 'TOTAL HARGA'];
            $col = 'B';
            foreach ($barangHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailBarang = $this->biayaImporBarangModel
                ->getListBarang($data['id']);

            if ($detailBarang) {
                foreach ($detailBarang as $by) {
                    $sheet->setCellValue("B{$row}", $by['kode_barang']);
                    $sheet->setCellValue("C{$row}", $by['barang_name'] . " " . $by['spesifikasi']);
                    $sheet->setCellValue("D{$row}", $by['qty_barang']);
                    $sheet->setCellValue("E{$row}", $by['kode_satuan']);
                    $sheet->setCellValue("F{$row}", $by['total_harga']);

                    $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada barang -");
                $row++;
            }

            // Spasi antar invoice
            $row++;
        }

        /**
         * ========================
         * Output
         * ========================
         */
        $fileName = 'Export_Biaya_Impor_' . time() . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody((function () use ($spreadsheet) {
                ob_start();
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                return ob_get_clean();
            })());
    }

    public function getStatusPosting()
    {
        try {
            $id = \decrypt($this->request->getVar('id'));
            $biayaImpor = $this->biayaImporModel
                ->where('id', $id)
                ->first();

            return response()->setJSON([
                'status' => true,
                'token' => \csrf_hash(),
                'data' => $biayaImpor
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

            $this->biayaImporModel->update($id, [
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
        $dataBiayaImpor = $this->biayaImporModel
            ->select(
                'biaya_impor.*,
                vendor_pelayaran.nama_vendor,
                suppliers.name as supplier_name,
                divisis.divisi
                '
            )
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_impor.vendor_pelayaran_id', 'left')
            ->join('suppliers', 'suppliers.id = biaya_impor.supplier_id', 'left')
            ->join('divisis', 'divisis.id = biaya_impor.divisi_id', 'left')
            ->where('biaya_impor.id', $id)
            ->first();

        $dataDetailBarang = $this->biayaImporBarangModel->getListBarang(
            $id
        );
        $dataBiayaImporPajak = $this->biayaImporPajakModel
            ->select(
                '
                biaya_impor_pajak.*,
                taxes.type as type_tax, 
                taxes.name as tax_name
            '
            )
            ->join('taxes', 'taxes.id = biaya_impor_pajak.tax_id', 'left')
            ->where('biaya_impor_id', $id)
            ->where('biaya_impor_pajak.deletedAt', null)
            ->findAll();
        $dataBiayaImporDetail = $this->biayaImporDetailModel
            ->select('biaya_impor_detail.*,metadata.value as valas_name')
            ->join('metadata', 'metadata.id = biaya_impor_detail.valas_id', 'left')
            ->where('biaya_impor_id', $id)
            ->where('biaya_impor_detail.deletedAt', null)
            ->findAll();

        $dataContainer = $this->biayaImporContainerModel
            ->where('biaya_impor_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        $data = [
            'dataBiayaImpor' => $dataBiayaImpor,
            'dataDetailBarang' => $dataDetailBarang,
            'dataBiayaImporPajak' => $dataBiayaImporPajak,
            'dataBiayaImporDetail' => $dataBiayaImporDetail,
            'dataContainer' => $dataContainer
        ];

        return \view('BiayaExim/BiayaImpor/detail', $data);
    }

    public function getDataBiayaImpor()
    {
        try {
            $id = $this->request->getVar('id');

            if (empty($id)) {
                return \response()->setJSON([
                    'data' =>  null,
                    'status' => true,
                    'token' => \csrf_hash()
                ]);
            }

            $dataBiayaImpor = $this->biayaImporModel->where('id', $id)->first();
            $dataContainer = $this->biayaImporContainerModel
                ->where('biaya_impor_id', $id)
                ->where('deletedAt', null)

                ->findAll();
            $dataDetailBarang = $this->biayaImporBarangModel->getListBarang(
                $id
            );

            $dataBiayaImpor['tanggal_invoice'] = \date('d/m/Y', \strtotime($dataBiayaImpor['tanggal_invoice']));

            return \response()->setJSON([
                'data' => [
                    'dataBiayaImpor' => $dataBiayaImpor,
                    'dataContainer' => $dataContainer,
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
