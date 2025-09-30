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
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
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
use App\Models\TransaksiJurnalModel;
use Config\Database;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenerimaanBarangImportBP extends BaseController
{
    protected $this_company_id;
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $metadataModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $rmPurchaseOrderModel;
    protected $rmPurchaseOrderDetailModel;
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
    protected $returnAmPoDetailModel;
    protected $bcPurchaseOrder;
    protected $jurnalUmumController;
    protected $this_user_id;
    protected $dompdf;
    protected $pengembalianBarangModel;
    protected $transaksiJurnalModel;
    protected $jurnalUmumModel;
    protected $accountBarangModel;
    protected $penerimaanBarangLokalBp;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->warehouseModel = new WarehousesModel();
        $this->divisiModel = new DivisisModel();
        $this->stockModel = new StockModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->kemasanModel = new KemasanModel();
        $this->returnAmPoDetailModel = new ReturAmPoDetailModel();
        $this->bcPurchaseOrder = new BCPurchaseOrderModel();
        $this->jurnalUmumController = new JurnalUmum();
        $this->dompdf = new Dompdf();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->accountBarangModel = new AccountBarangModel();
        $this->penerimaanBarangLokalBp = new PenerimaanBarangLokalBP();
    }

    public function index()
    {
        return view('Warehouse/penerimaanBarangImport/bahanPenolong/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
            "statuspenerimaan" => "IMPORT",
            "status" => $this->request->getVar("status"),
            "startdate" => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "penerimaan_barang.status_penerimaan" => "IMPORT",
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

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $penerimaanBarangData = $this->penerimaanBarangModel->getPenerimaanBarangList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarang = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $penerimaanBarangIds = array_column($penerimaanBarangData['data'], 'id');
        $akunCoaMap = [];
        if (count($penerimaanBarangIds) != 0) {
            $akunCoaMap = $this->penerimaanBarangLokalBp->getAkunCoaMap($penerimaanBarangIds);
        }

        foreach ($penerimaanBarangData['data'] as $data) {

            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "warehouse_name"        => $data->warehouse_name,
                "tipe_bahan"            => $data->tipe_bahan,
                "divisi"            => $data->divisi,
                "createdAt"             => $data->tanggal ? date("d/m/Y", strtotime($data->tanggal)) : "",
                "supplier_name"         => $data->supplier_name,
                "itemCount"             => $data->itemCount,
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
        $filename = "PENERIMAAN BARANG DARI PO IMPORT BAHAN PENOLONG";

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "penerimaan_barang.status_penerimaan" => "IMPORT",
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
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "divisi"                => $data->divisi,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "warehouse_name"        => $data->warehouse_name,
                "tipe_bahan"            => $data->tipe_bahan,
                "createdAt"             => $data->tanggal ? date("d/m/Y", strtotime($data->tanggal)) : "",
                "supplier_name"         => $data->supplier_name,
                "itemCount"             => $data->itemCount,
                "multiple_po_no"        => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no)),
                "status_post"           => $data->status_post,
            ]);
        }

        $data = [
            "data"  => $dataPenerimaanBarang,
        ];
        $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangImport/bahanPenolong/print-table', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function exportExcel()
    {

        $filename = "EXPORT_LPB_IMPORT_BP";

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "penerimaan_barang.status_penerimaan" => "IMPORT",
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
                "DEPARTEMEN"                => $data->divisi,
                "NO LPB"  => $data->no_penerimaan_barang,
                "WAREHOUSE"        => $data->warehouse_name,
                "TANGGAL"             => $data->tanggal ? date("d/m/Y", strtotime($data->tanggal)) : "",
                "SUPPLIER"         => $data->supplier_name,
                "TOTAL BARANG"             => $data->itemCount,
                "NO PO"        => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no)),
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
            $sheet->getStyle('A1:H1')->applyFromArray([
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
        $dataAJU = $this->metadataModel->getBCUsed("po_import_bp");
        $dataSupplier = $this->supplierModel->getSupplierByType('INTERNASIONAL');
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

        return view('Warehouse/penerimaanBarangImport/bahanPenolong/form', $data);
    }

    public function createAction()
    {
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
        if ($noPenerimaanBarang == "AUTO GENERATE") {
            $noPenerimaanBarang = $this->penerimaanBarangModel->get_no(
                $tanggal,
                $this->this_company_id,
                "IMPORT",
                "PENOLONG"
            );
        }

        $first = $this->penerimaanBarangModel->where('company_id', $this->this_company_id)
            ->where('no_penerimaan_barang', $this->request->getVar('no_penerimaan_barang'))
            ->where('status_penerimaan', "IMPORT")
            ->where('tipe_bahan', "PENOLONG")
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "No Penerimaan Barang Sudah Ada",
                'status' => false
            ]);
        }

        $penerimaanBarangID = $this->penerimaanBarangModel->insert([
            'company_id' => $this->this_company_id,
            'bc_type' => $this->request->getVar('aju_document_type'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            "ongkos_kirim" => $this->request->getVar('ongkos_kirim'),
            'kemasan_id' => $this->request->getVar('kemasan_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_penerimaan_barang' => $noPenerimaanBarang,
            'acceptance_type' => $this->request->getVar('acceptance_type'),
            'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_po_id'))),
            'multiple_po_no' => $this->request->getVar('multiple_po_no'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'kemasan' => $this->request->getVar('kemasan'),
            'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
            'no_invoice' => $this->request->getVar('no_invoice'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            "tipe_bahan" => "PENOLONG",
            "status_post" => "WAITING",
            "status_penerimaan" => "IMPORT",
            "tanggal" => $tanggal,
        ]);

        foreach (json_decode($barangs) as $b) {
            $poDetail = $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->first();
            $barang = null;
            if ($poDetail != null) {
                $barang = $this->barangMasterModel->where('id', $poDetail['barang_id'])->first();
            }
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
                'unit_konversi' => $b->satuan_konversi_id
            ]);
            // update remeaning di detail po
            $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->where('am_purchase_order_id', $b->am_purchase_order_id)
                ->set('remaining_qty', $b->sisa_total)
                ->set('qty_diterima', $b->jml_diterima_total)
                ->update();

            $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisi_id'), $barang['id'], $poDetail['spesifikasi_id']);
        }

        return response()->setJSON([
            'message' => "Penerimaan barang Import BP berhasil disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($penerimaanBarangID)
        ]);
    }

    public function update($id)
    {
        $id = decrypt($id);

        if ($this->penerimaanBarangModel->find($id) == null) {
            return redirect()->to('penerimaan-barang-import-bp');
        }
        $dataPenerimaanBarang =  $this->penerimaanBarangModel->where('id', $id)->first();

        $dataAJU = $this->metadataModel->getBCUsed("po_import_bp");
        $dataSupplier = $this->supplierModel->getSupplierByType('INTERNASIONAL');
        $dataWarehouse = $this->warehousesModel->where('divisi_id', $dataPenerimaanBarang['divisi_id'])->where('deletedAt', null)->findAll();
        $dataSatuan = $this->satuanModel->asObject()->find();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataKemasan = $this->kemasanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', 'asc')->findAll();

        $data = [
            "dataSatuan" => $dataSatuan,
            "dataWarehouse" => $dataWarehouse,
            "dataSupplier" => $dataSupplier,
            "dataAJU" => $dataAJU,
            "dataPenerimaanBarang" => $dataPenerimaanBarang,
            "dataKemasan"   => $dataKemasan,
            "dataDivisi" => $dataDivisi,
        ];

        return view('Warehouse/penerimaanBarangImport/bahanPenolong/form', $data);
    }

    public function updateAction()
    {
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
        $tanggal = $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "";
        if ($noPenerimaanBarang == "AUTO GENERATE") {
            $noPenerimaanBarang = $this->penerimaanBarangModel->get_no(
                $tanggal,
                $this->this_company_id,
                "IMPORT",
                "PENOLONG"
            );
        }

        $first = $this->penerimaanBarangModel
            ->where('company_id', $this->this_company_id)
            ->where('no_penerimaan_barang', $noPenerimaanBarang)
            ->where('status_penerimaan', "IMPORT")
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
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            "ongkos_kirim" => $this->request->getVar('ongkos_kirim'),
            'kemasan_id' => $this->request->getVar('kemasan_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'no_penerimaan_barang' => $noPenerimaanBarang,
            'acceptance_type' => $this->request->getVar('acceptance_type'),
            'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_po_id'))),
            'multiple_po_no' => json_encode($this->request->getVar('multiple_po_no')),
            'multiple_po_no' => $this->request->getVar('multiple_po_no'),
            'no_invoice' => $this->request->getVar('no_invoice'),
            'kemasan' => $this->request->getVar('kemasan'),
            'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            "tanggal" => $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "",
        ]);

        // delete first in penerimaan_barang_detail
        // $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->delete();

        foreach (json_decode($barangs) as $b) {
            $poDetail = $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->first();
            $barang = null;
            if ($poDetail != null) {
                $barang = $this->barangMasterModel->where('id', $poDetail['barang_id'])->first();
            }

            if ($b->jml_diterima_lpb != 0) {
                $penerimaanBarangDetailFirst = $this->penerimaanBarangDetailModel
                    ->where('penerimaan_barang_id', $id)
                    ->where('purchase_order_id', $b->am_purchase_order_id)
                    ->where('purchase_order_details_id', $b->am_purchase_order_details_id)
                    ->first();

                $this->penerimaanBarangDetailModel->update($penerimaanBarangDetailFirst['id'], [
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
                    'unit_konversi' => $b->satuan_konversi_id
                ]);
                // update remeaning di detail po
                $this->amPurchaseOrderDetailModel->where('id', $b->am_purchase_order_details_id)->where('am_purchase_order_id', $b->am_purchase_order_id)
                    ->set('remaining_qty', $b->sisa_total)
                    ->set('qty_diterima', $b->jml_diterima_total)
                    ->update();
                $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisi_id'), $barang == null ? 0 : $barang['id'], $poDetail == null ? 0 : $poDetail['spesifikasi_id']);
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

        return response()->setJSON([
            'message' => "Berhasil update penerimaan barang import BP",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);

        if ($id) {
            $filename = "Penerimaan Barang Import";

            $data = [
                'dataPenerimaanBarang' => $this->penerimaanBarangModel->getById($id),
                'dataPenerimaanBarangDetail' => $this->penerimaanBarangDetailModel->getPenerimaanBarangPenolongDetail2($id)
            ];
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangImport/bahanPenolong/print', $data));
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

            $multiple_po_id = json_decode($penerimaanBarang['multiple_po_id']);
            foreach ($multiple_po_id as $key => $value) {
                $result = $this->jurnalUmumController->insertDataPembelian($value, "BAHAN " . $penerimaanBarang['tipe_bahan'], $penerimaanBarang['status_penerimaan'], "pembelian", $id);
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
            }
            // MASUKKAN STOK BARANG DAN KEMASAN JIKA NON PABEAN 
            // (JIKA ADA BC MASUK KE INVENTORI DI MODUL BEA CUKAI)
            if ($penerimaanBarang['bc_type'] == 0) {

                $res = $this->penerimaanBarangLokalBp->insert_stock_pembelian_revamp($id);
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
                        date('Y-m-d'),
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

            foreach ($penerimaanBarangList as $p) {
                $this->barangMasterSpesifikasiModel
                    ->update($p['spesifikasi_id'], [
                        'harga_terakhir' => $p['harga'],
                        'supplier_terakhir' => $penerimaanBarang['supplier_id']
                    ]);
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
                    'message' => "Gagal UnPosting : Terdapat barang dalam return",
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
            } else {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal UnPosting : Jurnal Pembelian Tidak Ditemukan",
                    'token' => csrf_hash()
                ]);
            }

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

            // UPDATE HARGA
            foreach ($penerimaanBarangList as $p) {
                $hargaTerakhir = $this->penerimaanBarangDetailModel
                    ->historiHargaPOBahanPenolongByLpbFirst(
                        "IMPORT",
                        "PENOLONG",
                        $p['spesifikasi_id'],
                        $this->this_company_id
                    );

                if ($hargaTerakhir) {
                    $this->barangMasterSpesifikasiModel
                        ->update($p['spesifikasi_id'], [
                            'harga_terakhir' => $hargaTerakhir['harga'],
                            'supplier_terakhir' => $hargaTerakhir['supplier_id']
                        ]);
                } else {
                    $this->barangMasterSpesifikasiModel
                        ->update($p['spesifikasi_id'], [
                            'harga_terakhir' => null,
                            'supplier_terakhir' => null
                        ]);
                }
            }

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
            $penerimaanBarangList = $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->where('deletedAt', null)->findAll();

            $multiple_po_id = json_decode($penerimaanBarang['multiple_po_id']);
            foreach ($multiple_po_id as $key => $value) {
                $result = $this->jurnalUmumController->insertDataPembelian($value, "BAHAN " . $penerimaanBarang['tipe_bahan'], $penerimaanBarang['status_penerimaan'], "pembelian", $id);
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
            }
            // MASUKKAN STOK BARANG DAN KEMASAN JIKA NON PABEAN 
            // (JIKA ADA BC MASUK KE INVENTORI DI MODUL BEA CUKAI)
            if ($penerimaanBarang['bc_type'] == 0) {
                $res = $this->penerimaanBarangLokalBp->insert_stock_pembelian_revamp($id);
                if (!$res) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
                        'token' => csrf_hash()
                    ]);
                }
            }

            foreach ($penerimaanBarangList as $p) {
                $this->barangMasterSpesifikasiModel
                    ->update($p['spesifikasi_id'], [
                        'harga_terakhir' => $p['harga'],
                        'supplier_terakhir' => $penerimaanBarang['supplier_id']
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
            $penerimaanBarangList = $this->penerimaanBarangDetailModel
                ->select('penerimaan_barang_detail.*,barang_master.id as barang1_id, barang_master_spesifikasi.id as barang2_id, barang_master.type_barang')
                ->join('barang_master', 'barang_master.id = penerimaan_barang_detail.barang_id')
                ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = barang_master.id')
                ->where('penerimaan_barang_id', $id)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->findAll();

            // Hapus Di Jurnal
            $transaksiJurnal = $this->transaksiJurnalModel->where('penerimaan_barang_id', $id)->first();
            if ($transaksiJurnal) {
                $this->transaksiJurnalModel->where('penerimaan_barang_id', $id)->delete();
                $this->jurnalUmumModel->where('id_transaksi', $transaksiJurnal['id']);
            } else {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal UnPosting : Jurnal Pembelian Tidak Ditemukan",
                    'token' => csrf_hash()
                ]);
            }


            if ($penerimaanBarang['bc_type'] == 0) {
                $res = $this->penerimaanBarangLokalBp->unposting_stock_pembelian_revamp(
                    $id
                );

                if (!$res) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => "Gagal Unposting : Stok sudah digunakan",
                        'token' => csrf_hash()
                    ]);
                }
            }

            $this->penerimaanBarangModel
                ->where(['id' => $id])
                ->set(['status_post' => 'WAITING'])
                ->update();

            // UPDATE HARGA
            foreach ($penerimaanBarangList as $p) {
                $hargaTerakhir = $this->penerimaanBarangDetailModel
                    ->historiHargaPOBahanPenolongByLpbFirst(
                        "IMPORT",
                        "PENOLONG",
                        $p['spesifikasi_id'],
                        $this->this_company_id
                    );

                if ($hargaTerakhir) {
                    $this->barangMasterSpesifikasiModel
                        ->update($p['spesifikasi_id'], [
                            'harga_terakhir' => $hargaTerakhir['harga'],
                            'supplier_terakhir' => $hargaTerakhir['supplier_id']
                        ]);
                } else {
                    $this->barangMasterSpesifikasiModel
                        ->update($p['spesifikasi_id'], [
                            'harga_terakhir' => null,
                            'supplier_terakhir' => null
                        ]);
                }
            }

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

        return response()->setJSON($this->amPurchaseOrderDetailModel->getListLPBBahanPenolong($amPurchaseOrderID, "IMPORT", "PENOLONG", $isInitEdit, $penerimaanBarangID));
    }

    public function dropdownDivisiPOImportBP()
    {
        // Dapatkan divisi yang ada nomor PO nya
        $supplierID = $this->request->getVar('id');
        $condition = [
            'am_purchase_orders.deletedAt' => null,
            'am_purchase_orders.supplier_id' => $supplierID,
            'am_purchase_orders.is_posted' => '1',
            'am_purchase_orders.status_penerimaan' => '0',
            'am_purchase_orders.po_type' => 'Import',
            'divisis.company_id' => $this->this_company_id,
            'divisis.deletedAt' => null
        ];

        $selectQry = "divisis.*";
        $result = $this->divisiModel->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.division_id = divisis.id', 'left')
            ->whereIn('divisis.id', session()->get('login')->this_access_divisi_id)
            ->where($condition)
            ->groupBy('divisis.id')
            ->findAll();

        return response()->setJSON([
            'data' => $result,
            'status' => true,
            'token' => csrf_hash()
        ]);
    }
}
