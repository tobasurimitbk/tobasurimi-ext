<?php

namespace App\Controllers\Warehouse;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Models\AccountBarangModel;
use App\Models\PenerimaanBarangModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierHargaModel;
use App\Models\ReturAmPoDetailModel;
use App\Models\BCPurchaseOrderModel;
use App\Models\JurnalUmumModel;
use App\Models\PengembalianBarangModel;
use App\Models\SppModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampLogModel;
use App\Models\StockRevampModel;
use App\Models\TransaksiJurnalModel;
use App\Models\UserModel;
use Config\Database;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenerimaanBarangLokalBP extends BaseController
{
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $metadataModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $supplierModel;
    protected $warehousesModel;
    protected $satuanModel;
    protected $supplierHargaModel;
    protected $stockDetailModel;
    protected $warehouseModel;
    protected $divisiModel;
    protected $kemasanModel;
    protected $stockModel;
    protected $stockDetail2Model;
    protected $bcPurchaseOrder;
    protected $this_user_id;
    protected $dompdf;
    protected $jurnalUmumController;
    protected $pengembalianBarangModel;
    protected $sppModel;
    protected $transaksiJurnalModel;
    protected $jurnalUmumModel;
    protected $usersModel;
    protected $accountBarangModel;
    protected $stockRevampModel;
    protected $stockRevampDetailModel;
    protected $stockRevampLogModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->warehouseModel = new WarehousesModel();
        $this->divisiModel = new DivisisModel();
        $this->kemasanModel = new KemasanModel();
        $this->stockModel = new StockModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->bcPurchaseOrder = new BCPurchaseOrderModel();
        $this->jurnalUmumController = new JurnalUmum();
        $this->dompdf = new Dompdf();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->sppModel = new SppModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->usersModel = new UserModel();
        $this->accountBarangModel = new AccountBarangModel();
        $this->stockRevampModel = new StockRevampModel();
        $this->stockRevampLogModel = new StockRevampLogModel();
        $this->stockRevampDetailModel = new StockRevampDetailModel();
    }

    public function index()
    {
        return view('Warehouse/penerimaanBarangLokal/bahanPenolong/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
            "statuspenerimaan" => "LOKAL",
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "penerimaan_barang.status_penerimaan" => "LOKAL",
            "penerimaan_barang.deletedAt" => null,
            // "penerimaan_barang_detail.deletedAt" => null,
            "tipe_bahan" => "PENOLONG"
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "note" => strtolower($this->request->getVar('note')),
            "nama_barang" => strtolower($this->request->getVar('nama_barang'))
        ];

        if ($addCondition['status'] == "waiting") {
            // Jika Belum Posting Matikan Filter Start Date End Date
            $addCondition['startdate'] = "";
            $addCondition['lastdate'] = "";
        }

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $penerimaanBarangData = $this->penerimaanBarangModel->getPenerimaanBarangList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $penerimaanBarangIds = array_column($penerimaanBarangData['data'], 'id');
        $akunCoaMap = [];
        if (count($penerimaanBarangIds) != 0) {
            $akunCoaMap = $this->getAkunCoaMap($penerimaanBarangIds);
        }

        foreach ($penerimaanBarangData['data'] as $data) {
            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "tipe_bahan"            => $data->tipe_bahan,
                "createdAt"             => $data->tanggal ? date("d/m/Y", strtotime($data->tanggal)) : "",
                "supplier_name"         => $data->supplier_name,
                "itemCount"             => $data->itemCount,
                "spp_no"                => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_spp_no)),
                "multiple_po_no"        => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no)),
                "status_post"           => $data->status_post,
                "bc_type"               => $data->bc_type,
                "in_bc"                 => $data->bc_purchase_order_id != null ? 'in' : 'out',
                "akun_coa"              => $akunCoaMap[$data->id] ?? false,
                "bc_type_name"          => $data->bc_type_name == null ? "NON PABEAN" : $data->bc_type_name
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $penerimaanBarangData['totalData'],
            "recordsFiltered"   => $penerimaanBarangData['totalFilteredData'],
            "data"              => $dataPenerimaanBarang,
            "payload"           => $payload,

        ];

        echo json_encode($data);
        return;
    }

    public function printTable()
    {
        $filename = "PENERIMAAN BARANG DARI PO LOKAL BAHAN PENOLONG";

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "penerimaan_barang.status_penerimaan" => "LOKAL",
            "penerimaan_barang.deletedAt" => null,
            //"penerimaan_barang_detail.deletedAt" => null,
            "tipe_bahan" => "PENOLONG"
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "note" => strtolower($this->request->getVar('note')),
            "nama_barang" => strtolower($this->request->getVar('nama_barang'))
        ];

        $penerimaanBarangData = $this->penerimaanBarangModel->getPenerimaanBarangList($condition, $addCondition, 100000000, 0);

        $dataPenerimaanBarang = [];

        $no = 1;

        $penerimaanBarangIds = array_column($penerimaanBarangData['data'], 'id');
        $akunCoaMap = [];
        if (count($penerimaanBarangIds) != 0) {
            $akunCoaMap = $this->getAkunCoaMap($penerimaanBarangIds);
        }

        foreach ($penerimaanBarangData['data'] as $data) {


            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "tipe_bahan"            => $data->tipe_bahan,
                "createdAt"             => $data->tanggal ? date("d/m/Y", strtotime($data->tanggal)) : "",
                "supplier_name"         => $data->supplier_name,
                "itemCount"             => $data->itemCount,
                "spp_no"                => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_spp_no)),
                "multiple_po_no"        => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no)),
                "status_post"           => $data->status_post,
                "bc_type"               => $data->bc_type,
                "in_bc"                 => $data->bc_purchase_order_id != null ? 'in' : 'out',
                "akun_coa"              => $akunCoaMap[$data->id] ?? false,
                "bc_type_name"          => $data->bc_type_name == null ? "NON PABEAN" : $data->bc_type_name
            ]);
        }

        $data = [
            "data"  => $dataPenerimaanBarang,
        ];
        $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangLokal/bahanPenolong/print-table', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function exportExcel()
    {

        $filename = "EXPORT_LPB_LOKAL_BP";

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "penerimaan_barang.status_penerimaan" => "LOKAL",
            "penerimaan_barang.deletedAt" => null,
            //"penerimaan_barang_detail.deletedAt" => null,
            "tipe_bahan" => "PENOLONG"
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "note" => strtolower($this->request->getVar('note')),
            "nama_barang" => strtolower($this->request->getVar('nama_barang'))
        ];

        $penerimaanBarangData = $this->penerimaanBarangModel->getPenerimaanBarangList($condition, $addCondition, 100000000, 0);

        $dataPenerimaanBarang = [];

        $no = 1;

        foreach ($penerimaanBarangData['data'] as $data) {
            array_push($dataPenerimaanBarang, [
                "NO"                    => $no++,
                "DEPARTEMEN"            => $data->divisi,
                "NO PENERIMAAN"         => $data->no_penerimaan_barang,
                "NO SPP"                => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_spp_no)),
                "GUDANG"                => $data->warehouse_name,
                "TANGGAL"               => $data->tanggal ? date("d/m/Y", strtotime($data->tanggal)) : "",
                "SUPPLIER"              => $data->supplier_name,
                "DOKUMEN"               => $data->bc_type_name == null ? "NON PABEAN" : $data->bc_type_name,
                "JUMLAH ITEM"           => $data->itemCount,
            ]);
        }

        $data = [
            "data"  => $dataPenerimaanBarang,
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);


        if (empty($dataPenerimaanBarang)) {
            $sheet->setCellValue('A1', 'Tidak Ada Data Penerimaan Barang');
        } else {
            $header = array_keys($dataPenerimaanBarang[0]);
            $sheet->fromArray($header, null, 'A1');
            $sheet->getStyle('A1:I1')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);

            $rowData = array_map('array_values', $dataPenerimaanBarang);
            $sheet->fromArray($rowData, null, 'A2');

            foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow())
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Content-Length: ' . strlen($excelOutput));

        echo $excelOutput;
        exit();
    }

    public function create()
    {
        $dataAJU = $this->metadataModel->getBCUsed("po_lokal_bp");
        $dataSupplier = $this->supplierModel->getSupplierByType('BAHAN PENOLONG');
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);
        $dataSatuan = $this->satuanModel->asObject()->find();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataKemasan = $this->kemasanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', 'asc')->findAll();

        $data = [
            "dataSatuan" => $dataSatuan,
            "dataWarehouse" => $dataWarehouse,
            "dataSupplier" => $dataSupplier,
            "dataAJU" => $dataAJU,
            "dataDivisi" => $dataDivisi,
            "dataKemasan" => $dataKemasan
        ];

        return view('Warehouse/penerimaanBarangLokal/bahanPenolong/form', $data);
    }

    public function createAction()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            $barangs = $this->request->getVar('barangs');

            // Cek Kekosongan
            $jml_diterima_lpb = 0;
            foreach (json_decode($barangs) as $b) {
                $jml_diterima_lpb += $b->jml_diterima_lpb;
            }

            if ($jml_diterima_lpb == 0) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Isikan minimal satu item barang yang akan diterima",
                    'status' => false
                ]);
            }

            $noPenerimaanBarang = $this->request->getVar('no_penerimaan_barang');
            $tanggal = $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "";
            $checkNoLpb = $this->checkLpbNo($noPenerimaanBarang);
            if (!$checkNoLpb) {
                $noPenerimaanBarang = $this->penerimaanBarangModel->get_no(
                    $tanggal,
                    $this->this_company_id,
                    "LOKAL",
                    "PENOLONG"
                );
            }

            // $first = $this->penerimaanBarangModel->where('company_id', $this->this_company_id)
            //     ->where('no_penerimaan_barang', $this->request->getVar('no_penerimaan_barang'))
            //     ->where('status_penerimaan', "LOKAL")
            //     ->where('tipe_bahan', "PENOLONG")
            //     ->first();

            // if ($first != null) {
            //     return response()->setJSON([
            //         'token' => csrf_hash(),
            //         'message' => "No Penerimaan Barang Sudah Ada",
            //         'status' => false
            //     ]);
            // }

            $penerimaanBarangID = $this->penerimaanBarangModel->insert([
                'company_id' => $this->this_company_id,
                'bc_type' => $this->request->getVar('aju_document_type'),
                'supplier_id' => $this->request->getVar('supplier_id'),
                'divisi_id' => $this->request->getVar('divisi_id'),
                'kemasan_id' => $this->request->getVar('kemasan_id'),
                'warehouse_id' => $this->request->getVar('warehouse_id'),
                'no_penerimaan_barang' => $noPenerimaanBarang,
                "ongkos_kirim" => $this->request->getVar('ongkos_kirim'),
                'acceptance_type' => $this->request->getVar('acceptance_type'),
                'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_po_id'))),
                'multiple_po_no' => $this->request->getVar('multiple_po_no'),
                'kemasan' => $this->request->getVar('kemasan'),
                'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
                'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
                'no_invoice' => $this->request->getVar('no_invoice'),
                "tipe_bahan" => "PENOLONG",
                "status_post" => "WAITING",
                "status_penerimaan" => "LOKAL",
                "tanggal" => $tanggal,
            ]);

            foreach (json_decode($barangs) as $b) {
                $poDetail = $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->first();
                $barang = null;
                if ($poDetail != null) {
                    $barang = $this->barangMasterModel->where('id', $poDetail['barang_id'])->first();
                }
                if ($b->jml_diterima_lpb != 0) {
                    $this->penerimaanBarangDetailModel->insert([
                        'purchase_order_id' => $b->am_purchase_order_id,
                        'purchase_order_details_id' => $b->am_purchase_order_details_id,
                        'penerimaan_barang_id' => $penerimaanBarangID,
                        'barang_id' => $barang == null ? 0 : $barang['id'],
                        'spesifikasi_id' => $poDetail == null ? 0 : $poDetail['spesifikasi_id'],
                        'unit' => $poDetail == null ? 0 : $poDetail['unit'],
                        'harga' => $b->harga,
                        'sub_total' => $b->sub_total,
                        'qty' => $b->jml_order,
                        'nama_barang_dok' => $barang == null ? 0 : $barang['barang_name'],
                        'jml_masuk' => $b->jml_diterima_lpb,
                        'jml_masuk_konversi' => $b->jml_diterima_lpb_konversi,
                        'unit_konversi' => $b->satuan_konversi_id,
                        'keterangan' => $b->keterangan
                    ]);
                    // update remeaning di detail po
                    $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->where('am_purchase_order_id', $b->am_purchase_order_id)
                        ->set('remaining_qty', $b->sisa_total)
                        ->set('qty_diterima', $b->jml_diterima_total)
                        ->update();
                }
                $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisi_id'), $barang['id'], $poDetail['spesifikasi_id']);
            }

            // Update Multiple Spp Id
            $this->updateMultipleSppColumn($penerimaanBarangID);
            $db->transCommit();

            return response()->setJSON([
                'message' => "Penerimaan barang Lokal BP berhasil disimpan",
                'token' => csrf_hash(),
                'status' => true,
                'id' => encrypt($penerimaanBarangID)
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function update($id)
    {
        $id = decrypt($id);

        if ($this->penerimaanBarangModel->find($id) == null) {
            return redirect()->to('penerimaan-barang-lokal-bp');
        }

        $dataPenerimaanBarang =  $this->penerimaanBarangModel->where('id', $id)->first();
        // $dataAJU = $this->metadataModel->getBCUsed("po_lokal_bp");
        // $dataSupplier = $this->supplierModel->getSupplierByType('BAHAN PENOLONG');
        // $dataWarehouse = $this->warehousesModel->where('divisi_id', $dataPenerimaanBarang['divisi_id'])->where('deletedAt', null)->findAll();
        // $dataSatuan = $this->satuanModel->asObject()->find();
        // $dataDivisi = $this->divisiModel->getDivisiAccess();
        // $dataKemasan = $this->kemasanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', 'asc')->findAll();

        $data = [
            // "dataSatuan" => $dataSatuan,
            // "dataWarehouse" => $dataWarehouse,
            // "dataSupplier" => $dataSupplier,
            // "dataAJU" => $dataAJU,
            "dataPenerimaanBarang" => $dataPenerimaanBarang,
            // "dataKemasan"   => $dataKemasan,
            // "dataDivisi" => $dataDivisi,
            // "dataSPP" => [],
            // 'dataSPPSelected' => []
        ];

        // $dataSPPSelected = $this->amPurchaseOrderModel->getSPP(json_decode($data['dataPenerimaanBarang']['multiple_po_id']));
        // $dataSppAll = $this->sppModel->getListSPPLPBByDivisi($this->this_company_id, $dataPenerimaanBarang['divisi_id'], $dataPenerimaanBarang['supplier_id']);


        // $data['dataSPPSelected'] = $dataSPPSelected;
        // $data['dataSPP'] = $dataSppAll;


        return view('Warehouse/penerimaanBarangLokal/bahanPenolong/form', $data);
    }

    public function updateAction()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();

            $id = decrypt($this->request->getVar('id'));
            $barangs = $this->request->getVar('barangs');

            if (count(json_decode($barangs)) == 0) {
                return response()->setJSON([
                    'message' => "Gagal Update: List barang tidak ditemukan",
                    'token' => csrf_hash(),
                    'status' => false
                ]);
            }

            $noPenerimaanBarang = $this->request->getVar('no_penerimaan_barang');
            $first = $this->penerimaanBarangModel->where('company_id', $this->this_company_id)
                ->where('no_penerimaan_barang', $this->request->getVar('no_penerimaan_barang'))
                ->where('status_penerimaan', "LOKAL")
                ->where('tipe_bahan', "PENOLONG")
                ->where('id !=', $id)
                ->first();

            if ($first != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "No Penerimaan Barang Sudah Ada",
                    'status' => false
                ]);
            }

            $this->penerimaanBarangModel->update($id, [
                'company_id' => $this->this_company_id,
                'bc_type' => $this->request->getVar('aju_document_type'),
                'supplier_id' => $this->request->getVar('supplier_id'),
                'divisi_id' => $this->request->getVar('divisi_id'),
                'kemasan_id' => $this->request->getVar('kemasan_id'),
                "ongkos_kirim" => $this->request->getVar('ongkos_kirim'),
                'warehouse_id' => $this->request->getVar('warehouse_id'),
                'no_penerimaan_barang' => $noPenerimaanBarang,
                'acceptance_type' => $this->request->getVar('acceptance_type'),
                'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_po_id'))),
                'multiple_po_no' => $this->request->getVar('multiple_po_no'),
                'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
                'no_invoice' => $this->request->getVar('no_invoice'),
                'kemasan' => $this->request->getVar('kemasan'),
                'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
                'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
                "tanggal" => $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "",
            ]);

            // delete first in penerimaan_barang_detail
            // $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->delete();
            $penerimaanBarangDetailId = array();
            foreach (json_decode($barangs) as $b) {

                $poDetail = $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->first();
                $barang = null;
                if ($poDetail != null) {
                    $barang = $this->barangMasterModel->where('id', $poDetail['barang_id'])->first();
                }

                if ($b->jml_diterima_lpb != 0) {
                    $penerimaanBarangDetailFirst = $this->penerimaanBarangDetailModel
                        ->select('penerimaan_barang_detail.*')
                        ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
                        ->where('penerimaan_barang_id', $id)
                        ->where('purchase_order_id', $b->am_purchase_order_id)
                        ->where('purchase_order_details_id', $b->am_purchase_order_details_id)
                        ->where('status_penerimaan', "LOKAL")
                        ->where('tipe_bahan', "PENOLONG")
                        ->first();

                    if ($penerimaanBarangDetailFirst == null) {
                        // \var_dump($b);
                        // die;
                        // Insert
                        $id_new_detail = $this->penerimaanBarangDetailModel->insert([
                            'purchase_order_id' => $b->am_purchase_order_id,
                            'purchase_order_details_id' => $b->am_purchase_order_details_id,
                            'penerimaan_barang_id' => $id,
                            'barang_id' => $barang == null ? 0 : $barang['id'],
                            'spesifikasi_id' => $poDetail == null ? 0 : $poDetail['spesifikasi_id'],
                            'unit' => $poDetail == null ? 0 : $poDetail['unit'],
                            'harga' => $b->harga,
                            'sub_total' => $b->sub_total,
                            'qty' => $b->jml_order,
                            'nama_barang_dok' => $barang == null ? 0 : $barang['barang_name'],
                            'jml_masuk' => $b->jml_diterima_lpb,
                            'jml_masuk_konversi' => $b->jml_diterima_lpb_konversi,
                            'unit_konversi' => $b->satuan_konversi_id,
                            'keterangan' => $b->keterangan
                        ]);

                        // update remeaning di detail po
                        $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)
                            ->where('am_purchase_order_id', $b->am_purchase_order_id)
                            ->set('remaining_qty', $b->sisa_total)
                            ->set('qty_diterima', $b->jml_diterima_total)
                            ->update();
                        // \var_dump($id);
                        // die;
                        array_push($penerimaanBarangDetailId, $id_new_detail);
                        $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisi_id'), $barang['id'], $poDetail['spesifikasi_id']);
                    } else {
                        // Update
                        $this->penerimaanBarangDetailModel->update($penerimaanBarangDetailFirst['id'], [
                            'purchase_order_id' => $b->am_purchase_order_id,
                            'purchase_order_details_id' => $b->am_purchase_order_details_id,
                            // 'penerimaan_barang_id' => $id,
                            'barang_id' => $barang == null ? 0 : $barang['id'],
                            'spesifikasi_id' => $poDetail == null ? 0 : $poDetail['spesifikasi_id'],
                            'unit' => $poDetail == null ? 0 : $poDetail['unit'],
                            'harga' => $b->harga,
                            'sub_total' => $b->sub_total,
                            'qty' => $b->jml_order,
                            'nama_barang_dok' => $barang == null ? 0 : $barang['barang_name'],
                            'jml_masuk' => $b->jml_diterima_lpb,
                            'jml_masuk_konversi' => $b->jml_diterima_lpb_konversi,
                            'unit_konversi' => $b->satuan_konversi_id,
                            'keterangan' => $b->keterangan
                        ]);
                        // update remeaning di detail po
                        $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->where('am_purchase_order_id', $b->am_purchase_order_id)
                            ->set('remaining_qty', $b->sisa_total)
                            ->set('qty_diterima', $b->jml_diterima_total)
                            ->update();

                        $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisi_id'), $barang['id'], $poDetail['spesifikasi_id']);
                        array_push($penerimaanBarangDetailId, $penerimaanBarangDetailFirst['id']);
                    }
                } else {
                    $last = $this->amPurchaseOrderDetailModel
                        ->where('id',  $b->am_purchase_order_details_id)
                        ->where('am_purchase_order_id', $b->am_purchase_order_id)
                        ->first();

                    $this->amPurchaseOrderDetailModel
                        ->where('id', $b->am_purchase_order_details_id)
                        ->where('am_purchase_order_id', $b->am_purchase_order_id)
                        ->set('remaining_qty', $last['remaining_qty'] +  $b->jml_diterima_lpb)
                        ->set('qty_diterima', $last['qty_diterima'] - $b->jml_diterima_lpb)
                        ->update();

                    $this->penerimaanBarangDetailModel
                        ->where('penerimaan_barang_id', $id)
                        ->where('purchase_order_id', $b->am_purchase_order_id)
                        ->where('purchase_order_details_id', $b->am_purchase_order_details_id)
                        ->delete();
                }
            }

            // REMOVE BARANG
            $penerimaanBarangDetailRemovedList = $this->penerimaanBarangDetailModel
                ->whereNotIn('id', $penerimaanBarangDetailId)
                ->where('penerimaan_barang_id', $id)
                ->where('deletedAt', null)
                ->findAll();

            foreach ($penerimaanBarangDetailRemovedList as $p) {
                $last = $this->amPurchaseOrderDetailModel
                    ->where('id',  $p['purchase_order_details_id'])
                    ->where('am_purchase_order_id', $p['purchase_order_id'])
                    ->first();

                $this->amPurchaseOrderDetailModel
                    ->where('id', $p['purchase_order_details_id'])
                    ->where('am_purchase_order_id', $p['purchase_order_id'])
                    ->set('remaining_qty', $last['remaining_qty'] +  $p['jml_masuk_konversi'])
                    ->set('qty_diterima', $last['qty_diterima'] - $p['jml_masuk_konversi'])
                    ->update();

                $this->penerimaanBarangDetailModel
                    ->delete($p['id']);
            }

            $multiplePoIdArr = [];
            $multiplePoNoArr = [];

            $penerimaanBarangDetails = $this->penerimaanBarangDetailModel
                ->where('penerimaan_barang_id', $id)
                ->findAll();


            foreach ($penerimaanBarangDetails as $detail) {
                $amPurchaseOrder = $this->amPurchaseOrderModel->where('id', $detail['purchase_order_id'])->first();
                if ($amPurchaseOrder) {
                    $multiplePoIdArr[] = (int) $amPurchaseOrder['id']; // ← penting: cast ke int
                    $multiplePoNoArr[] = $amPurchaseOrder['po_no'];
                }
            }

            // Hapus duplikat
            $multiplePoIdArr = array_unique($multiplePoIdArr);
            $multiplePoNoArr = array_unique($multiplePoNoArr);

            // Simpan
            $this->penerimaanBarangModel->update($id, [
                'multiple_po_id' => json_encode(array_values($multiplePoIdArr), JSON_UNESCAPED_SLASHES),
                'multiple_po_no' => json_encode(array_values($multiplePoNoArr), JSON_UNESCAPED_SLASHES)
            ]);

            $this->updateMultipleSppColumn($id);
            $db->transCommit();

            return response()->setJSON([
                'message' => "Berhasil update penerimaan barang lokal BP",
                'token' => csrf_hash(),
                'status' => true
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage() . " " . $e->getLine() . " " . $e->getFile(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function print($id)
    {
        $id = decrypt($id);
        if ($id) {
            $filename = "Penerimaan Barang Lokal";

            $data = [
                'dataPenerimaanBarang' => $this->penerimaanBarangModel->getById($id),
                'dataPenerimaanBarangDetail' => $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail2($id),
                'dataUser' => $this->usersModel->where('id', $this->this_user_id)->first()
            ];
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangLokal/bahanPenolong/print', $data));
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        // GET PENERIMAAN BARANG
        $penerimaanBarang = $this->penerimaanBarangModel->where('id', $id)->first();
        $penerimaanBarangList = $this->penerimaanBarangDetailModel->asObject()->where('penerimaan_barang_id', $id)->where('deletedAt', null)->findAll();
        // UPDATE STATUS PENERIMAAN PO MENJADI 0
        foreach (json_decode($penerimaanBarang['multiple_po_id']) as $p) {
            $this->amPurchaseOrderModel->update($p, ['status_penerimaan' => 0]);
        }
        // DELETE PENERIMAAN BARANG
        $this->penerimaanBarangModel->where('id', $id)->delete();
        foreach ($penerimaanBarangList as $b) {
            // update remeaning di detail po
            $last = $this->amPurchaseOrderDetailModel->where('id', $b->purchase_order_details_id)
                ->where('am_purchase_order_id', $b->purchase_order_id)
                ->first();

            $this->amPurchaseOrderDetailModel->where('id', $b->purchase_order_details_id)
                ->where('am_purchase_order_id', $b->purchase_order_id)
                ->set('remaining_qty', $last['remaining_qty'] + $b->jml_masuk)
                ->set('qty_diterima', $last['qty_diterima'] - $b->jml_masuk)
                ->update();
        }
        $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "LPB berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function postingBackup()
    {
        $id = decrypt($this->request->getVar('id'));

        try {
            $penerimaanBarang = $this->penerimaanBarangModel->where('id', $id)->first();
            $penerimaanBarangList = $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->where('deletedAt', null)->findAll();

            foreach (json_decode($penerimaanBarang['multiple_po_id']) as $p) {
                $amPurchaseOrder = $this->amPurchaseOrderModel
                    ->select('am_purchase_orders.*,purchase_requests.spp_no')
                    ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
                    ->where('am_purchase_orders.id', $p)
                    ->first();
                if ($amPurchaseOrder['is_posted'] == 0) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Purchase Order dengan nomor SPP : " . $amPurchaseOrder['spp_no'] . ", dengan nomor PO : " . $amPurchaseOrder['po_no'] . " Belum Diposting",
                        'token' => csrf_hash()
                    ]);
                    break;
                }
            }

            $result = $this->jurnalUmumController->insertDataPembelian($id, "BAHAN " . $penerimaanBarang['tipe_bahan'], $penerimaanBarang['status_penerimaan'], "pembelian", $id);
            if ($result) {
                $responseBody = json_decode($result->getBody(), true);
                if ($responseBody && isset($responseBody['status'])) {
                    $data = [
                        "status"    => false,
                        "message"   => $responseBody['message'],
                        "payload"   => "",
                        'token'     => csrf_hash()
                    ];
                    // echo json_encode($data);
                    return json_encode($data);
                }
            }

            // MASUKKAN STOK BARANG DAN KEMASAN JIKA NON PABEAN 
            // (JIKA ADA BC MASUK KE INVENTORI DI MODUL BEA CUKAI)
            if ($penerimaanBarang['bc_type'] == 0) {

                $res = $this->insert_stock_pembelian_revamp($id);
                if (!$res) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
                        'token' => csrf_hash()
                    ]);
                }

                // CHECK STOK APAKAH SUDAH DIINISASI
                foreach ($penerimaanBarangList as $p) {
                    // CHECK STOK BARANG HEADER
                    $stok = $this->stockModel->getStokMaster(
                        $this->this_company_id,
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        "bahan_penolong",
                        $p['barang_id'],
                        $p['spesifikasi_id'],
                    );

                    if ($stok == null) {
                        $stok = $this->stockModel->insertStok(
                            $this->this_company_id,
                            $penerimaanBarang['warehouse_id'],
                            $penerimaanBarang['divisi_id'],
                            "bahan_penolong",
                            $p['barang_id'],
                            $p['spesifikasi_id'],
                            0
                        );
                    }
                }

                // CHECK STOK KEMASAN HEADER
                $stok = $this->stockModel->getStokMaster(
                    $this->this_company_id,
                    $penerimaanBarang['warehouse_id'],
                    $penerimaanBarang['divisi_id'],
                    "kemasan",
                    0,
                    $penerimaanBarang['kemasan_id'],
                );

                if ($stok == null) {
                    $stok = $this->stockModel->insertStok(
                        $this->this_company_id,
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        "kemasan",
                        0,
                        $penerimaanBarang['kemasan_id'],
                        0
                    );
                }


                // STOK BARANG DIINPUT
                foreach ($penerimaanBarangList as $p) {
                    // HEADER
                    $stok = $this->stockModel->insertStok(
                        $this->this_company_id,
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        "bahan_penolong",
                        $p['barang_id'],
                        $p['spesifikasi_id'],
                        $p['jml_masuk_konversi']
                    );

                    // DETAIL
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        $p['jml_masuk_konversi'],
                        'In',
                        $penerimaanBarang['tanggal'],
                        $this->this_user_id,
                        "LPB",
                        $penerimaanBarang['no_penerimaan_barang'],
                        "-",
                    );

                    // GET PURCHASE ORDER
                    $po = $this->amPurchaseOrderModel->find($p['purchase_order_id']);
                    // SUB DETAIL
                    $this->stockDetail2Model->insertStokDetail2(
                        $penerimaanBarang['bc_type'],
                        $stok,
                        $stokDetail,
                        $p['jml_masuk_konversi'],
                        "-",
                        $po['po_no'],
                        $po['po_no'],
                        $penerimaanBarang['supplier_id'],
                        $p['harga'],
                        $p['harga_harian'],
                        $p['harga_bulanan'],
                        $po['po_no']
                    );
                }

                // KEMASAN
                // HEADER
                $stok = $this->stockModel->insertStok(
                    $this->this_company_id,
                    $penerimaanBarang['warehouse_id'],
                    $penerimaanBarang['divisi_id'],
                    "kemasan",
                    0,
                    $penerimaanBarang['kemasan_id'],
                    $penerimaanBarang['jumlah_kemasan']
                );


                // DETAIL
                $stokDetail = $this->stockDetailModel->insertStokDetail(
                    $stok,
                    $penerimaanBarang['jumlah_kemasan'],
                    "In",
                    date('Y-m-d'),
                    $this->this_user_id,
                    "LPB",
                    $penerimaanBarang['no_penerimaan_barang'],
                    "-",
                );

                // SUB DETAIL
                $this->stockDetail2Model->insertStokDetail2(
                    $penerimaanBarang['bc_type'],
                    $stok,
                    $stokDetail,
                    $penerimaanBarang['jumlah_kemasan'],
                    "-",
                    $penerimaanBarang['no_penerimaan_barang'],
                    $penerimaanBarang['no_penerimaan_barang'],
                    $penerimaanBarang['supplier_id'],
                );
            }
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
                'error' => $e->getTrace(),
                'token' => csrf_hash()
            ]);
        }

        $this->penerimaanBarangModel
            ->where(['id' => $id])
            ->set(['status_post' => 'FINISH'])
            ->update();

        $this->penerimaanBarangModel->autoClosePO($id);

        return response()->setJSON([
            'status' => true,
            'message' => "LPB berhasil diposting",
            'token' => csrf_hash()
        ]);
    }

    public function unpostingBackup()
    {
        try {
            $db = Database::connect();
            $db->transBegin();

            $id = decrypt($this->request->getVar('id'));

            // Unpost
            $res = $this->unposting_stock_pembelian_revamp(
                $id
            );

            if (!$res) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal UnPosting : Stock Barang Sudah Digunakan",
                    'token' => csrf_hash()
                ]);
            }

            $penerimaanBarang = $this->penerimaanBarangModel->where('id', $id)->first();
            $penerimaanBarangList = $this->penerimaanBarangDetailModel
                ->select('penerimaan_barang_detail.*,barang_master.id as barang1_id, barang_master_spesifikasi.id as barang2_id, barang_master.type_barang')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = barang_master.id')
                ->where('penerimaan_barang_id', $id)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->findAll();

            $retur_am_po_detail_list = $this->pengembalianBarangModel->where('penerimaan_barang_id', $id)->where('deletedAt', null)->findAll();
            $bc_purchase_order_detail_list = $this->bcPurchaseOrder->where('company_id', $this->this_company_id)->like('multiple_lpb_id', $id)->where('deletedAt', null)->findAll();

            if ($penerimaanBarang['bc_type'] == 0) {
                $cekStockLpbUsed = $this->stockModel->checkStockLpbUsed(
                    $penerimaanBarang['id']
                );

                if ($cekStockLpbUsed != null) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Gagal UnPosting : " . $cekStockLpbUsed,
                        'token' => csrf_hash()
                    ]);
                }
            }

            if (!empty($retur_am_po_detail_list)) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal UnPosting : Stok sudah di returkan",
                    'token' => csrf_hash()
                ]);
            }

            if (!empty($bc_purchase_order_detail_list)) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal UnPosting : Terdapat barang dalam BC",
                    'token' => csrf_hash()
                ]);
            }

            // Hapus Di Jurnal
            $transaksiJurnal = $this->transaksiJurnalModel->where('penerimaan_barang_id', $id)->first();
            if ($transaksiJurnal) {
                $this->transaksiJurnalModel->where('penerimaan_barang_id', $id)->delete();
                $this->jurnalUmumModel->where('id_transaksi', $transaksiJurnal['id']);
            }

            // else {
            //     return response()->setJSON([
            //         'status' => false,
            //         'message' => "Gagal UnPosting : Jurnal Pembelian Tidak Ditemukan",
            //         'token' => csrf_hash()
            //     ]);
            // }

            // foreach ($penerimaanBarangList as $p) {

            //     $statusOUT = $this->jurnalUmumController->TransaksiJurnalStockBarang($this->this_company_id, $penerimaanBarang['divisi_id'], $p['barang1_id'], $p['barang2_id'], $p['type_barang'], $penerimaanBarang['no_penerimaan_barang'], 'OUT');

            //     if ($statusOUT) {
            //         $responseBody = json_decode($statusOUT->getBody(), true);
            //         $data = [
            //             "status"    => false,
            //             "id"    => $this->request->getVar('id'),
            //             "message"   => $responseBody['message'],
            //             'token'     => csrf_hash()
            //         ];
            //         echo json_encode($data);
            //         return;
            //     }
            // }

            // UNPOST KHUSUS NON PABEAN
            if ($penerimaanBarang['bc_type'] == 0) {
                $this->stockModel->unPostingStockLPB(
                    $penerimaanBarang['id']
                );
            }

            $this->penerimaanBarangModel
                ->where(['id' => $id])
                ->set(['status_post' => 'WAITING'])
                ->update();

            $db->transCommit();
            return response()->setJSON([
                'status' => true,
                'message' => "LPB berhasil diunpost ",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'message' => "Gagal Posting : Terjadi kesalahan saat unposting lpb",
                'error' => $e->getTrace(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        try {
            $penerimaanBarang = $this->penerimaanBarangModel->where('id', $id)->first();

            if ($penerimaanBarang['status_post'] == "FINISH") {
                return response()->setJSON([
                    'status' => false,
                    'message' => "LPB Sudah diposting user lain",
                    'token' => csrf_hash()
                ]);
            }

            foreach (json_decode($penerimaanBarang['multiple_po_id']) as $p) {
                $amPurchaseOrder = $this->amPurchaseOrderModel
                    ->select('am_purchase_orders.*,purchase_requests.spp_no')
                    ->join('purchase_requests', 'purchase_requests.id = am_purchase_orders.purchase_request_id', 'left')
                    ->where('am_purchase_orders.id', $p)
                    ->first();
                if ($amPurchaseOrder['is_posted'] == 0) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Purchase Order dengan nomor SPP : " . $amPurchaseOrder['spp_no'] . ", dengan nomor PO : " . $amPurchaseOrder['po_no'] . " Belum Diposting",
                        'token' => csrf_hash()
                    ]);
                    break;
                }
            }

            $result = $this->jurnalUmumController->insertDataPembelian($id, "BAHAN " . $penerimaanBarang['tipe_bahan'], $penerimaanBarang['status_penerimaan'], "pembelian", $id);
            if ($result) {
                $responseBody = json_decode($result->getBody(), true);
                if ($responseBody && isset($responseBody['status'])) {
                    $data = [
                        "status"    => false,
                        "message"   => $responseBody['message'],
                        "payload"   => "",
                        'token'     => csrf_hash()
                    ];
                    // echo json_encode($data);
                    return json_encode($data);
                }
            }

            // MASUKKAN STOK BARANG DAN KEMASAN JIKA NON PABEAN 
            // (JIKA ADA BC MASUK KE INVENTORI DI MODUL BEA CUKAI)
            if ($penerimaanBarang['bc_type'] == 0) {

                $res = $this->insert_stock_pembelian_revamp($id);
                if (!$res) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
                        'token' => csrf_hash()
                    ]);
                }
            }

            $this->penerimaanBarangModel
                ->where(['id' => $id])
                ->set(['status_post' => 'FINISH'])
                ->update();

            $this->penerimaanBarangModel->autoClosePO($id);

            return response()->setJSON([
                'status' => true,
                'message' => "LPB berhasil diposting",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
                'error' => $e->getTrace(),
                'token' => csrf_hash()
            ]);
        }
    }
    public function unposting()
    {
        try {
            $db = Database::connect();
            $db->transBegin();

            $id = decrypt($this->request->getVar('id'));

            $penerimaanBarang = $this->penerimaanBarangModel->where('id', $id)->first();

            if ($penerimaanBarang['bc_type'] == 0) {
                $res = $this->unposting_stock_pembelian_revamp(
                    $id
                );
                if (!$res) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Gagal UnPosting : Stock Barang Sudah Digunakan",
                        'token' => csrf_hash()
                    ]);
                }
            }


            // Hapus Di Jurnal
            $transaksiJurnal = $this->transaksiJurnalModel->where('penerimaan_barang_id', $id)->first();
            if ($transaksiJurnal) {
                $this->transaksiJurnalModel->where('penerimaan_barang_id', $id)->delete();
                $this->jurnalUmumModel->where('id_transaksi', $transaksiJurnal['id']);
            }

            $this->penerimaanBarangModel
                ->where(['id' => $id])
                ->set(['status_post' => 'WAITING'])
                ->update();

            $this->penerimaanBarangModel->autoOpenPO($id);


            $db->transCommit();
            return response()->setJSON([
                'status' => true,
                'message' => "LPB berhasil diunpost ",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'message' => "Gagal Posting : Terjadi kesalahan saat unposting lpb",
                'error' => $e->getTrace(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function listBarangLPB()
    {
        $penerimaanBarangID = empty($this->request->getVar('penerimaan_barang_id')) ? null : decrypt($this->request->getVar('penerimaan_barang_id'));
        $amPurchaseOrderID = json_decode($this->request->getVar('am_purchase_order_id'));
        $isInitEdit = $this->request->getVar('is_init_edit');
        if (count($amPurchaseOrderID) == 0) {
            return response()->setJSON([
                'result' => []
            ]);
        }
        return response()->setJSON($this->amPurchaseOrderDetailModel->getListLPBBahanPenolong($amPurchaseOrderID, "LOKAL", "PENOLONG", $isInitEdit, $penerimaanBarangID));
    }

    public function generateLPBNo()
    {
        try {
            $tanggalReq = $this->request->getVar('tanggal');
            if (empty($tanggalReq)) {
                return response()->setJSON([
                    'status' => true,
                    'token' => csrf_hash(),
                    'data' => "LPB/" . date('m') . "" . date('y')
                ]);
            }
            $tanggal = $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $tanggalReq), "Y-m-d") : "";
            $noPenerimaanBarang = $this->penerimaanBarangModel->get_no(
                $tanggal,
                $this->this_company_id,
                "LOKAL",
                "PENOLONG"
            );

            return response()->setJSON([
                'status' => true,
                'data' => $noPenerimaanBarang,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function dropdownDivisiPOLokalBP()
    {
        // Dapatkan divisi yang ada nomor PO nya
        $supplierID = $this->request->getVar('id');
        $condition = [
            'am_purchase_orders.deletedAt' => null,
            'am_purchase_orders.supplier_id' => $supplierID,
            'am_purchase_orders.is_posted' => '1',
            'am_purchase_orders.status_penerimaan' => '0',
            'am_purchase_orders.po_type' => 'Lokal',
            'am_purchase_orders.company_id' => $this->this_company_id,
        ];

        $selectQry = "divisis.*";
        $result = $this->divisiModel->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.division_id = divisis.id', 'left')
            // ->whereIn('divisis.id', session()->get('login')->this_access_divisi_id)
            ->where($condition)
            ->groupBy('divisis.id')
            ->findAll();

        return response()->setJSON([
            'data' => $result,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    // Load Component
    public function loadComponent()
    {
        $id = $this->request->getVar('id');
        $form = $this->request->getVar('form');
        $dataAJU = $this->metadataModel->getBCUsed("po_lokal_bp");
        $dataWarehouse = [];
        $dataSatuan = $this->satuanModel->asObject()->find();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataKemasan = $this->kemasanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', 'asc')->findAll();

        $data = [
            "dataSatuan" => $dataSatuan,
            "dataWarehouse" => $dataWarehouse,
            "dataSupplier" => [],
            "dataAJU" => $dataAJU,
            "dataDivisi" => $dataDivisi,
            "dataKemasan" => $dataKemasan,
            "dataSPP" => [],
            "dataDivisi" => [],
            "dataPenerimaanBarang" => null,
            "dataSPP" => [],
            'dataSPPSelected' => []
        ];

        if (!empty($id)) {
            $data['dataPenerimaanBarang'] =  $this->penerimaanBarangModel->where('id', $id)->first();
            $dataSupplier = $this->supplierModel->where('id', $data['dataPenerimaanBarang']['supplier_id'])->findAll();
            $data['dataSupplier'] = $dataSupplier;
            $data['dataWarehouse'] =  $this->warehousesModel->where('divisi_id', $data['dataPenerimaanBarang']['divisi_id'])->where('deletedAt', null)->findAll();

            $dataSPPSelected = $this->amPurchaseOrderModel->getSPP(json_decode($data['dataPenerimaanBarang']['multiple_po_id']));
            $dataSppAll = $this->sppModel->getListSPPLPBByDivisi(
                $this->this_company_id,
                $data['dataPenerimaanBarang']['divisi_id'],
                $data['dataPenerimaanBarang']['supplier_id']
            );

            // Buat array berisi ID dari yang sudah selected
            $selectedIds = array_column($dataSPPSelected, 'id');

            // Filter hanya yang belum dipilih
            $dataSppAllUnique = [];

            foreach ($dataSppAll as $spp) {
                if (!in_array($spp['id'], $selectedIds)) {
                    $dataSppAllUnique[] = $spp;
                }
            }

            $data['dataSPPSelected'] = $dataSPPSelected;
            $data['dataSPP'] = $dataSppAllUnique;
        }

        if ($form == 'single') {
            if (empty($id)) {
                // ambil data spp yang sudah dibuatkan PO dan sudah diposting
                $data['dataSPP'] = $this->sppModel->getListSPPLPB($this->this_company_id);
            }
            $data['dataDivisi'] = $this->divisiModel->getDivisiAccess();

            return view('Warehouse/penerimaanBarangLokal/bahanPenolong/formSingle', $data);
        } else {
            $data['dataSupplier'] = $this->supplierModel->getSupplierByType("BAHAN PENOLONG");
            $data['dataDivisi'] = $this->divisiModel->getDivisiAccess();
            return view('Warehouse/penerimaanBarangLokal/bahanPenolong/formMultiple', $data);
        }
    }

    public function dropdownSupplierBySPPBackup()
    {
        $sppId = $this->request->getVar('spp_id');
        $poList = $this->amPurchaseOrderModel->where('po_type', "Lokal")->where('purchase_request_id', $sppId)->where('is_posted', 1)->where('status_penerimaan', 0)->where('deletedAt', null)->findAll();
        $supplierId = [];

        foreach ($poList as $p) {
            array_push($supplierId, $p['supplier_id']);
        }

        if (count($supplierId) == 0) {
            return response()->setJSON([
                'status' => true,
                'data' => []
            ]);
        }

        $suplierData = $this->supplierModel->whereIn('id', $supplierId)->where('deletedAt', null)->findAll();
        return response()->setJSON([
            'status' => true,
            'data' => $suplierData
        ]);
    }

    public function dropdownSupplierBySPP()
    {
        $sppId = $this->request->getVar('spp_id');
        $poList = $this->amPurchaseOrderModel->where('po_type', "Lokal")
            ->where('purchase_request_id', $sppId)
            ->where('is_posted', 1)
            ->where('status_penerimaan', 0)
            ->where('deletedAt', null)
            ->findAll();

        $supplierId = [];

        foreach ($poList as $p) {
            $poDetail = $this->amPurchaseOrderDetailModel
                ->where('am_purchase_order_id', $p['id'])
                ->where('deletedAt', null)
                ->findAll();

            foreach ($poDetail as $d) {
                if ($d['remaining_qty'] != 0) {
                    array_push($supplierId, $p['supplier_id']);
                }
            }
        }

        if (count($supplierId) == 0) {
            return response()->setJSON([
                'status' => true,
                'data' => []
            ]);
        }

        $suplierData = $this->supplierModel->whereIn('id', $supplierId)->where('deletedAt', null)->findAll();
        return response()->setJSON([
            'status' => true,
            'data' => $suplierData
        ]);
    }

    private function checkLpbNo($noLpb)
    {
        $penerimaanBarang = $this->penerimaanBarangModel
            ->where('no_penerimaan_barang', $noLpb)
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->first();

        return $penerimaanBarang == null ? true : false;
    }

    private function updateMultipleSppColumn($id)
    {
        $penerimaanBarang = $this->penerimaanBarangModel->where('id', $id)->first();
        $multiplePoId = json_decode($penerimaanBarang['multiple_po_id']);

        $sppList = $this->amPurchaseOrderModel->getSPP(
            $multiplePoId
        );

        $multipleSppId = str_replace(['\\"', '\\', '"'], '', json_encode(array_column($sppList, 'id')));
        $multipleSppNo = str_replace(['\\"', '\\'], '', json_encode(array_column($sppList, 'spp_no')));

        $this->penerimaanBarangModel->update($id, [
            'multiple_spp_id' => $multipleSppId,
            'multiple_spp_no' => $multipleSppNo
        ]);
    }

    public function getAkunCoaMap($penerimaanBarangIds)
    {
        $details = $this->penerimaanBarangDetailModel
            ->select('penerimaan_barang_detail.penerimaan_barang_id, 
              penerimaan_barang_detail.barang_id, 
              penerimaan_barang_detail.spesifikasi_id,
              account_barang.ap_id,
              account_barang.ar_id')
            ->join('penerimaan_barang', 'penerimaan_barang.id = penerimaan_barang_detail.penerimaan_barang_id', 'left')
            ->join('account_barang', 'account_barang.barang_master_id = penerimaan_barang_detail.barang_id 
                             AND account_barang.barang_master_spesifikasi_id = penerimaan_barang_detail.spesifikasi_id
                             AND account_barang.divisi_id = penerimaan_barang.divisi_id', 'left')
            ->whereIn('penerimaan_barang_detail.penerimaan_barang_id', $penerimaanBarangIds)
            ->where('penerimaan_barang_detail.deletedAt', null)
            ->where('account_barang.deleted_at', null)
            ->findAll();

        $akunCoaMap = [];
        foreach ($details as $d) {
            $pid = $d['penerimaan_barang_id'];
            if (!isset($akunCoaMap[$pid])) {
                $akunCoaMap[$pid] = true; // default true
            }

            if ($d['ap_id'] === null && $d['ar_id'] === null) {
                $akunCoaMap[$pid] = false;
            }
        }

        return $akunCoaMap;
    }


    public function insert_stock_pembelian_revamp($penerimaanBarangId)
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {

            $penerimaanBarang = $this->penerimaanBarangModel->where('id', $penerimaanBarangId)->first();
            $penerimaanBarangList = $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $penerimaanBarangId)->where('deletedAt', null)->findAll();
            $poType = $penerimaanBarang['status_penerimaan'] . " " . $penerimaanBarang['tipe_bahan'];
            $typeBc = $this->metadataModel->where('id', $penerimaanBarang['bc_type'])->first();

            // STOK BARANG DIINPUT
            foreach ($penerimaanBarangList as $p) {
                $data = [
                    'company_id' => $this->this_company_id,
                    'barang_master_id' => $p['barang_id'],
                    'spesifikasi_id' => $p['spesifikasi_id'],
                    'unit_id' => $p['unit_konversi'],
                    'divisi_id' => $penerimaanBarang['divisi_id'],
                    'warehouse_id' => $penerimaanBarang['warehouse_id'],
                    'qty_bersih' => $p['jml_masuk_konversi'],
                    'qty_diterima' => $p['jml_masuk_konversi'],
                    'bc_id' => $penerimaanBarang['bc_type'],
                    'type_bc' => $typeBc == null ? "NON PABEAN" : $typeBc['value'],
                    'reference_id' => $penerimaanBarang['id'],
                    'po_type' => $poType,
                    'po_id' => $p['purchase_order_id'],
                    'reference_type' => "LPB",
                    'status' => "IN"
                ];
                $stockDetailId = $this->stockRevampModel->insertStockRevamp(
                    $db,
                    $data
                );

                $this->penerimaanBarangDetailModel->update($p['id'], [
                    'stock_detail_id' => $stockDetailId
                ]);
            }

            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            log_message('error', 'Insert Stock Failed: ' . $e->getMessage());
            return false;
        }
    }

    public function unposting_stock_pembelian_revamp($penerimaanBarangId)
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {

            $penerimaanBarangList = $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $penerimaanBarangId)->where('deletedAt', null)->findAll();

            $isFailed = false;
            foreach ($penerimaanBarangList as $p) {

                // Jika Qty Masih Sama Dengan LPB Maka Belum Digunakan
                // Aman Jika Dihapus
                $stockDetail = $this->stockRevampDetailModel
                    ->where('id', $p['stock_detail_id'])
                    ->first();

                if ($stockDetail['qty_bersih'] != $p['jml_masuk_konversi']) {
                    $isFailed = true;
                    break;
                }
            }

            if ($isFailed) {
                $db->transRollback();
                return false;
            }

            // Aman Stock Belum Digunakan
            foreach ($penerimaanBarangList as $p) {
                $stockDetail = $this->stockRevampDetailModel
                    ->where('id', $p['stock_detail_id'])
                    ->first();

                $stock = $this->stockRevampModel->where('id', $stockDetail['stock_id'])->first();
                if ($stock) {
                    $qtyNow = $stock['qty_bersih'] - $p['jml_masuk_konversi'];
                    $this->stockRevampModel->update($stock['id'], ['qty_bersih' => $qtyNow, 'qty_diterima' => $qtyNow]);
                }

                $this->stockRevampDetailModel->delete($stockDetail['id'], true);
                $this->stockRevampLogModel->where('stock_detail_id', $stockDetail['id'])->delete(null, true);
            }

            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            log_message('error', 'Unposting Stock Failed: ' . $e->getMessage());
            return false;
        }
    }

    public function cariBarang()
    {
        return view('Warehouse/penerimaanBarangLokal/bahanPenolong/cariBarang');
    }

    public function allCariBarang()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $condition = [
            "am_purchase_orders.company_id" => $this->this_company_id,
            "am_purchase_orders.po_type" => "Lokal",
            "am_purchase_orders.is_posted" => 1,
            "am_purchase_orders.deletedAt" => null,
            "am_purchase_order_details.deletedAt" => null,
            "am_purchase_order_details.remaining_qty !=" => 0,
            "am_purchase_orders.supplier_id" => $this->request->getVar('supplier_id')
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $data = $this->amPurchaseOrderDetailModel->getBarangBelumDiterima(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($data['data'] as $d) {
            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($d['id']),
                "divisi"                => $d['divisi'],
                "spp_no"                => $d['spp_no'],
                "request_date"          => date('d/m/Y', strtotime($d['request_date'])),
                "supplier_name"         => $d['supplier_name'],
                "po_date"               => date('d/m/Y', strtotime($d['po_date'])),
                "po_no"                 => $d['po_no'],
                "barang_name"           => $d['barang_name'],
                "spesifikasi"           => $d['spesifikasi'],
                "note"                  => $d['note'],
                "qty"                   => (float)$d['qty'],
                "qty_diterima"          => (float)$d['qty_diterima'],
                "remaining_qty"         => (float)$d['remaining_qty'],
                "kode_satuan"           => $d['kode_satuan']
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $data['totalData'],
            "recordsFiltered"   => $data['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload,

        ];
        echo json_encode($data);
        return;
    }
}
