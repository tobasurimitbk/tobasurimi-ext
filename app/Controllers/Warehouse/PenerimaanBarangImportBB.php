<?php

namespace App\Controllers\Warehouse;

use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMImportPODetailModel;
use App\Models\RMImportPOModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
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

class PenerimaanBarangImportBB extends BaseController
{
    protected $this_company_id;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $metadataModel;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $rmImportPo;
    protected $rmImportPoDetail;
    protected $supplierModel;
    protected $warehousesModel;
    protected $satuanModel;
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
    protected $penerimaanBarangLokalBp;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->rmImportPo = new RMImportPOModel();
        $this->rmImportPoDetail = new RMImportPODetailModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->metadataModel = new MetadataModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->divisiModel = new DivisisModel();
        $this->kemasanModel = new KemasanModel();
        $this->stockModel = new StockModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->returnAmPoDetailModel = new ReturAmPoDetailModel();
        $this->bcPurchaseOrder = new BCPurchaseOrderModel();
        $this->jurnalUmumController = new JurnalUmum();
        $this->dompdf = new Dompdf();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->penerimaanBarangLokalBp = new PenerimaanBarangLokalBP();
    }

    public function index()
    {
        return view('Warehouse/penerimaanBarangImport/bahanBaku/index');
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
            "tipe_bahan" => "BAKU"
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
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "tipe_bahan"            => $data->tipe_bahan,
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
        $filename = "PENERIMAAN BARANG DARI PO IMPORT BAHAN BAKU";

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "penerimaan_barang.status_penerimaan" => "IMPORT",
            "penerimaan_barang.deletedAt" => null,
            //"penerimaan_barang_detail.deletedAt" => null,
            "tipe_bahan" => "BAKU"
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
        $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangImport/bahanBaku/print-table', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function exportExcel()
    {

        $filename = "EXPORT_LPB_IMPORT_BB";

        $condition = [
            "penerimaan_barang.company_id" => $this->this_company_id,
            "penerimaan_barang.status_penerimaan" => "IMPORT",
            "penerimaan_barang.deletedAt" => null,
            //"penerimaan_barang_detail.deletedAt" => null,
            "tipe_bahan" => "BAKU"
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
                "NO PENERIMAAN BARANG"  => $data->no_penerimaan_barang,
                "NO PO"                 => str_replace(',', ', ', str_replace(['[', ']', '"', "\\"], '', $data->multiple_po_no)),
                "GUDANG"                => $data->warehouse_name,
                "TANGGAL"               => $data->tanggal ? date("d/m/Y", strtotime($data->tanggal)) : "",
                "SUPPLIER"              => $data->supplier_name,
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
        $dataAJU = $this->metadataModel->getBCUsed("po_import_bb");
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

        return view('Warehouse/penerimaanBarangImport/bahanBaku/form', $data);
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
                "BAKU"
            );
        }

        $first = $this->penerimaanBarangModel
            ->where('company_id', $this->this_company_id)
            ->where('no_penerimaan_barang', $this->request->getVar('no_penerimaan_barang'))
            ->where('status_penerimaan', "IMPORT")
            ->where('tipe_bahan', "BAKU")
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
            "tipe_bahan" => "BAKU",
            "status_post" => "WAITING",
            "status_penerimaan" => "IMPORT",
            "tanggal" => $tanggal,
        ]);

        foreach (json_decode($barangs) as $b) {
            $poDetail = $this->rmImportPoDetail->where('id', $b->rm_import_po_details_id)->first();
            $barang = null;
            if ($poDetail != null) {
                $barang = $this->barangMasterModel->where('id', $poDetail['barang_id'])->first();
            }
            if ($b->jml_diterima_lpb != 0) {
                $this->penerimaanBarangDetailModel->insert([
                    'purchase_order_id' => $b->rm_import_po_id,
                    'purchase_order_details_id' => $b->rm_import_po_details_id,
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
                $this->rmImportPoDetail->where('id', $b->rm_import_po_details_id)->where('rm_import_po_id', $b->rm_import_po_id)
                    ->set('remaining_qty', $b->sisa_total)
                    ->set('qty_diterima', $b->jml_diterima_total)
                    ->update();
            }
        }

        return response()->setJSON([
            'message' => "Penerimaan barang Import BB berhasil disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($penerimaanBarangID)
        ]);
    }

    public function update($id)
    {
        $id = decrypt($id);

        if ($this->penerimaanBarangModel->find($id) == null) {
            return redirect()->to('penerimaan-barang-import-bb');
        }

        $dataPenerimaanBarang =  $this->penerimaanBarangModel->where('id', $id)->first();
        $dataAJU = $this->metadataModel->getBCUsed("po_import_bb");
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

        return view('Warehouse/penerimaanBarangImport/bahanBaku/form', $data);
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
        $first = $this->penerimaanBarangModel->where('company_id', $this->this_company_id)
            ->where('no_penerimaan_barang', $this->request->getVar('no_penerimaan_barang'))
            ->where('status_penerimaan', "IMPORT")
            ->where('tipe_bahan', "BAKU")
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
            'multiple_po_no' => $this->request->getVar('multiple_po_no'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'kemasan' => $this->request->getVar('kemasan'),
            'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
            'no_invoice' => $this->request->getVar('no_invoice'),
            "tanggal" => $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "",
        ]);

        // delete first in penerimaan_barang_detail
        // $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->delete();

        foreach (json_decode($barangs) as $b) {
            $poDetail = $this->rmImportPoDetail->where('id', $b->rm_import_po_details_id)->first();
            $barang = null;
            if ($poDetail != null) {
                $barang = $this->barangMasterModel->where('id', $poDetail['barang_id'])->first();
            }

            if ($b->jml_diterima_lpb != 0) {
                $penerimaanBarangDetailFirst = $this->penerimaanBarangDetailModel
                    ->where('penerimaan_barang_id', $id)
                    ->where('purchase_order_id', $b->rm_import_po_id)
                    ->where('purchase_order_details_id', $b->rm_import_po_details_id)
                    ->first();

                $this->penerimaanBarangDetailModel->update($penerimaanBarangDetailFirst['id'], [
                    'purchase_order_id' => $b->rm_import_po_id,
                    'purchase_order_details_id' => $b->rm_import_po_details_id,
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
                $this->rmImportPoDetail->where('id', $b->rm_import_po_details_id)->where('rm_import_po_id', $b->rm_import_po_id)
                    ->set('remaining_qty', $b->sisa_total)
                    ->set('qty_diterima', $b->jml_diterima_total)
                    ->update();
            } else {
                $last = $this->rmImportPoDetail
                    ->where('id',  $b->rm_import_po_details_id)
                    ->where('rm_import_po_id', $b->rm_import_po_id)
                    ->first();

                $this->rmImportPoDetail
                    ->where('id', $b->rm_import_po_details_id)
                    ->where('rm_import_po_id', $b->rm_import_po_id)
                    ->set('remaining_qty', $last['remaining_qty'] +  $b->jml_diterima_lpb)
                    ->set('qty_diterima', $last['qty_diterima'] - $b->jml_diterima_lpb)
                    ->update();

                $this->penerimaanBarangDetailModel
                    ->where('penerimaan_barang_id', $id)
                    ->where('purchase_order_id', $b->rm_import_po_id)
                    ->where('purchase_order_details_id', $b->rm_import_po_details_id)
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
                'dataPenerimaanBarangDetail' => $this->penerimaanBarangDetailModel->getPenerimaanBarangImportBakuDetailPrint($id)
            ];
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangImport/bahanBaku/print', $data));
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
            $this->rmImportPo->update($p, ['status_penerimaan' => 0]);
        }
        // DELETE PENERIMAAN BARANG
        $this->penerimaanBarangModel->where('id', $id)->delete();
        foreach ($penerimaanBarangList as $b) {
            // update remeaning di detail po
            $last = $this->rmImportPoDetail->where('id', $b->purchase_order_details_id)
                ->where('rm_import_po_id', $b->purchase_order_id)
                ->first();

            $this->rmImportPoDetail->where('id', $b->purchase_order_details_id)
                ->where('rm_import_po_id', $b->purchase_order_id)
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

    public function unposting()
    {
        try {
            $db = Database::connect();
            $db->transBegin();
            $id = decrypt($this->request->getVar('id'));
            $penerimaanBarang = $this->penerimaanBarangModel->where('id', $id)->first();

            if ($penerimaanBarang['status_post'] == "WAITING") {
                return response()->setJSON([
                    'status' => false,
                    'message' => "LPB sudah diunposting user lain",
                    'token' => csrf_hash()
                ]);
            }

            // Hapus Di Jurnal
            $transaksiJurnal = $this->transaksiJurnalModel->where('penerimaan_barang_id', $id)->first();
            if ($transaksiJurnal) {
                $this->transaksiJurnalModel->delete($transaksiJurnal['id']);
                $this->jurnalUmumModel->where('id_transaksi', $transaksiJurnal['id'])->delete();
            }

            $res = $this->penerimaanBarangLokalBp->unposting_stock_pembelian_revamp(
                $id
            );

            if (!$res) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal UnPosting : Stock Barang Sudah Digunakan",
                    'token' => csrf_hash()
                ]);
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

            $res = $this->penerimaanBarangLokalBp->insert_stock_pembelian_revamp($id);
            if (!$res) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
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
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => "Gagal Posting : Terjadi kesalahan saat menambah stok",
                'error' => $e->getTrace(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function listBarangLPB()
    {
        $penerimaanBarangID = empty($this->request->getVar('penerimaan_barang_id')) ? null : decrypt($this->request->getVar('penerimaan_barang_id'));
        $rmPurchaseOrderID = json_decode($this->request->getVar('rm_import_po_id'));

        if (count($rmPurchaseOrderID) == 0) {
            return response()->setJSON([
                'result' => []
            ]);
        }

        return response()->setJSON($this->rmImportPoDetail->getListLPBBahanBaku($rmPurchaseOrderID, "IMPORT", "BAKU", $penerimaanBarangID));
    }

    public function dropdownDivisiPOImportBB()
    {
        // Dapatkan divisi yang ada nomor PO nya
        $supplierID = $this->request->getVar('id');
        $condition = [
            'rm_import_pos.deletedAt' => null,
            'rm_import_pos.supplier_id' => $supplierID,
            'rm_import_pos.is_posted' => '1',
            'rm_import_pos.status_penerimaan' => '0',
            'divisis.company_id' => $this->this_company_id,
            'divisis.deletedAt' => null
        ];

        $selectQry = "divisis.*";
        $result = $this->divisiModel->select($selectQry)
            ->join('rm_import_pos', 'rm_import_pos.division_id = divisis.id', 'left')
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
}
