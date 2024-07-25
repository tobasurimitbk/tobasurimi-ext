<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengembalianBarangModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierHargaModel;
use App\Models\SupplierModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReturnBarangPO extends BaseController
{
    protected $this_company_id;
    protected $penerimaanBarangModel;
    protected $penerimaanBarangDetailModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $metadataModel;
    protected $supplierModel;
    protected $warehousesModel;
    protected $satuanModel;
    protected $rmPurchaseOrderModel;
    protected $rmPurchaseOrderDetailModel;
    protected $supplierHargaModel;
    protected $stockDetailModel;
    protected $divisiModel;
    protected $kemasanModel;
    protected $stockModel;
    protected $stockDetail2Model;
    protected $this_user_id;
    protected $dompdf;
    protected $pengembalianBarangModel;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->warehousesModel = new WarehousesModel();
        $this->satuanModel = new SatuansModel();
        $this->metadataModel = new MetadataModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->rmPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->supplierHargaModel = new SupplierHargaModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->divisiModel = new DivisisModel();
        $this->kemasanModel = new KemasanModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->dompdf = new Dompdf();

        $this->pengembalianBarangModel = new PengembalianBarangModel();
        // $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
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

        $first = $this->penerimaanBarangModel->where('company_id', $this->this_company_id)->where('no_penerimaan_barang', $this->request->getVar('no_penerimaan_barang'))->first();
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
            'divisi_id' => $this->request->getVar('divisi_id'),
            'kemasan_id' => $this->request->getVar('kemasan_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_penerimaan_barang' => $this->request->getVar('no_penerimaan_barang'),
            'acceptance_type' => $this->request->getVar('acceptance_type'),
            'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_po_id'))),
            'multiple_po_no' => $this->request->getVar('multiple_po_no'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'kemasan' => $this->request->getVar('kemasan'),
            'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
            'no_invoice' => $this->request->getVar('no_invoice'),
            "tanggal" => $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "",
            "ongkos_kirim" => $this->request->getVar('ongkos_kirim'),
            "tipe_bahan" => "BAKU",
            "status_post" => "WAITING",
            "status_penerimaan" => "LOKAL",
        ]);

        foreach (json_decode($barangs) as $b) {
            $poDetail = $this->rmPurchaseOrderDetailModel->where('id', $b->rm_purchase_order_details_id)->first();
            $supplierHarga = null;
            if ($poDetail != null) {
                $supplierHarga = $this->supplierHargaModel
                    ->select('supplier_harga.spesifikasi, barang_master.barang_name, barang_master.id, supplier_harga.spesifikasi_id')
                    ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id')
                    ->where('supplier_harga.id', $poDetail['supplier_harga_id'])
                    ->first();
            }
            if ($b->jml_diterima_lpb != 0) {
                $this->penerimaanBarangDetailModel->insert([
                    'purchase_order_id' => $b->rm_purchase_order_id,
                    'purchase_order_details_id' => $b->rm_purchase_order_details_id,
                    'penerimaan_barang_id' => $penerimaanBarangID,
                    'barang_id' => $supplierHarga == null ? 0 : $supplierHarga['id'],
                    'spesifikasi_id' => $supplierHarga == null ? 0 : $supplierHarga['spesifikasi_id'],
                    'unit' => $poDetail == null ? 0 : $poDetail['satuan_id'],
                    'harga' => $b->harga_umum,
                    'harga_harian' => $b->harga_harian,
                    'harga_bulanan' => $b->harga_bulanan,
                    'sub_total' => $b->sub_total,
                    'qty' => $b->jml_order,
                    'nama_barang_dok' => $supplierHarga == null ? 0 : $supplierHarga['barang_name'] . ' (' . $supplierHarga['spesifikasi'] . ')',
                    'jml_masuk' => $b->jml_diterima_lpb,
                ]);
                // update remeaning di detail po
                $this->rmPurchaseOrderDetailModel->where('id', $b->rm_purchase_order_details_id)->where('rm_purchase_order_id', $b->rm_purchase_order_id)
                    ->set('remaining_qty', $b->sisa_total)
                    ->set('qty_diterima', $b->jml_diterima_total)
                    ->update();
            }
        }

        return response()->setJSON([
            'message' => "Penerimaan barang lokal BB berhasil disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($penerimaanBarangID)
        ]);
    }

    public function update($id)
    {
        $id = decrypt($id);

        if ($this->penerimaanBarangModel->find($id) == null) {
            return redirect()->to('penerimaan-barang-lokal-bb');
        }

        $dataPenerimaanBarang =  $this->penerimaanBarangModel->where('id', $id)->first();
        $dataPengembalianBarang =  $this->pengembalianBarangModel->where('penerimaan_barang_id', $id)->first();
        $dataAJU = $this->metadataModel->where('name', 'jenis_dok_aju')->findAll();
        $dataSupplier = $this->supplierModel->where('deletedAt', null)->findAll();
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);
        $dataSatuan = $this->satuanModel->asObject()->find();
        $dataDivisi = $this->divisiModel->where('id', $dataPenerimaanBarang['divisi_id'])->findAll();
        $dataKemasan = $this->kemasanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', 'asc')->findAll();

        $data = [
            "dataSatuan" => $dataSatuan,
            "dataWarehouse" => $dataWarehouse,
            "dataSupplier" => $dataSupplier,
            "dataAJU" => $dataAJU,
            "dataDivisi" => $dataDivisi,
            "dataPenerimaanBarang" => $dataPenerimaanBarang,
            "dataPengembalianBarang" => $dataPengembalianBarang,
            "dataKemasan"   => $dataKemasan
        ];

        return view('Warehouse/returnBarang/form', $data);
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

        $this->penerimaanBarangModel->update($id, [
            'company_id' => $this->this_company_id,
            'bc_type' => $this->request->getVar('aju_document_type'),
            'supplier_id' => $this->request->getVar('supplier_id'),
            'kemasan_id' => $this->request->getVar('kemasan_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_penerimaan_barang' => $this->request->getVar('no_penerimaan_barang'),
            'acceptance_type' => $this->request->getVar('acceptance_type'),
            'multiple_po_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_po_id'))),
            'multiple_po_no' => $this->request->getVar('multiple_po_no'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'kemasan' => $this->request->getVar('kemasan'),
            'jumlah_kemasan' => $this->request->getVar('jumlah_kemasan'),
            'no_invoice' => $this->request->getVar('no_invoice'),
            "ongkos_kirim" => $this->request->getVar('ongkos_kirim'),
            "tanggal" => $this->request->getVar("tanggal_penerimaan_lpb") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal_penerimaan_lpb")), "Y-m-d") : "",
        ]);

        // delete first in penerimaan_barang_detail
        // $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->delete();

        foreach (json_decode($barangs) as $b) {
            $poDetail = $this->rmPurchaseOrderDetailModel->where('id', $b->rm_purchase_order_details_id)->first();
            $supplierHarga = null;
            if ($poDetail != null) {
                $supplierHarga = $this->supplierHargaModel
                    ->select('supplier_harga.*, supplier_harga.spesifikasi, barang_master.barang_name, barang_master.id')
                    ->join('barang_master', 'barang_master.id = supplier_harga.bahan_baku_id')
                    ->where('supplier_harga.id', $poDetail['supplier_harga_id'])
                    ->first();
            }

            if ($b->jml_diterima_lpb != 0) {
                // UPDATE
                $penerimaanBarangDetailFirst = $this->penerimaanBarangDetailModel
                    ->where('penerimaan_barang_id', $id)
                    ->where('purchase_order_id', $b->rm_purchase_order_id)
                    ->where('purchase_order_details_id', $b->rm_purchase_order_details_id)
                    ->first();

                $this->penerimaanBarangDetailModel
                    ->update($penerimaanBarangDetailFirst['id'], [
                        'purchase_order_id' => $b->rm_purchase_order_id,
                        'purchase_order_details_id' => $b->rm_purchase_order_details_id,
                        'penerimaan_barang_id' => $id,
                        'barang_id' => $supplierHarga == null ? 0 : $supplierHarga['id'],
                        'spesifikasi_id' => $supplierHarga == null ? 0 : $supplierHarga['spesifikasi_id'],
                        'unit' => $poDetail == null ? 0 : $poDetail['satuan_id'],
                        'harga' => $b->harga_umum,
                        'harga_harian' => $b->harga_harian,
                        'harga_bulanan' => $b->harga_bulanan,
                        'sub_total' => $b->sub_total,
                        'qty' => $b->jml_order,
                        'nama_barang_dok' => $supplierHarga == null ? 0 : $supplierHarga['barang_name'],
                        'jml_masuk' => $b->jml_diterima_lpb,
                    ]);
                // update remeaning di detail po
                $this->rmPurchaseOrderDetailModel->where('id', $b->rm_purchase_order_details_id)->where('rm_purchase_order_id', $b->rm_purchase_order_id)
                    ->set('remaining_qty', $b->sisa_total)
                    ->set('qty_diterima', $b->jml_diterima_total)
                    ->update();
            } else {
                // DELETE
                $last = $this->rmPurchaseOrderDetailModel
                    ->where('id', $b->rm_purchase_order_details_id)
                    ->where('rm_purchase_order_id', $b->rm_purchase_order_id)
                    ->first();

                $this->rmPurchaseOrderDetailModel->where('id', $b->rm_purchase_order_details_id)
                    ->where('rm_purchase_order_id', $b->rm_purchase_order_id)
                    ->set('remaining_qty', $last['remaining_qty'] + $b->jml_diterima_lpb)
                    ->set('qty_diterima', $last['qty_diterima'] - $b->jml_diterima_lpb)
                    ->update();

                $this->penerimaanBarangDetailModel
                    ->where('penerimaan_barang_id', $id)
                    ->where('purchase_order_id', $b->rm_purchase_order_id)
                    ->where('purchase_order_details_id', $b->rm_purchase_order_details_id)
                    ->delete();
            }
        }

        return response()->setJSON([
            'message' => "Berhasil update penerimaan barang lokal BB",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        if ($id) {
            $filename = "Penerimaan Barang Lokal";

            $data = [
                'dataPenerimaanBarang' => $this->penerimaanBarangModel->getById($id),
                'dataPenerimaanBarangDetail' => $this->penerimaanBarangDetailModel->getPenerimaanBarangBakuDetail($id)
            ];
            $this->dompdf->loadHtml(view('Warehouse/penerimaanBarangLokal/bahanBaku/print', $data));
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->penerimaanBarangModel->where('id', $id)->delete();
        $penerimaanBarangList = $this->penerimaanBarangDetailModel->asObject()->where('penerimaan_barang_id', $id)->where('deletedAt', null)->findAll();
        foreach ($penerimaanBarangList as $b) {
            // update remeaning di detail po
            $last = $this->rmPurchaseOrderDetailModel->where('id', $b->purchase_order_details_id)
                ->where('rm_purchase_order_id', $b->purchase_order_id)
                ->first();

            $this->rmPurchaseOrderDetailModel->where('id', $b->purchase_order_details_id)
                ->where('rm_purchase_order_id', $b->purchase_order_id)
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

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        try {
            $penerimaanBarang = $this->penerimaanBarangModel->where('id', $id)->first();
            $penerimaanBarangList = $this->penerimaanBarangDetailModel->where('penerimaan_barang_id', $id)->where('deletedAt', null)->findAll();

            // MASUKKAN STOK BARANG DAN KEMASAN JIKA NON PABEAN 
            // (JIKA ADA BC MASUK KE INVENTORI DI MODUL BEA CUKAI)
            if ($penerimaanBarang['bc_type'] == 0) {
                // CHECK STOK APAKAH SUDAH DIINISASI
                foreach ($penerimaanBarangList as $p) {

                    // CHECK STOK BARANG HEADER
                    $stok = $this->stockModel->getStokMaster(
                        $this->this_company_id,
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        "bahan_baku",
                        $p['barang_id'],
                        $p['spesifikasi_id'],
                    );

                    if ($stok == null) {
                        $stok = $this->stockModel->insertStok(
                            $this->this_company_id,
                            $penerimaanBarang['warehouse_id'],
                            $penerimaanBarang['divisi_id'],
                            "bahan_baku",
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
                        "bahan_baku",
                        $p['barang_id'],
                        $p['spesifikasi_id'],
                        $p['jml_masuk']
                    );

                    // DETAIL
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        $p['jml_masuk'],
                        'In',
                        date('Y-m-d'),
                        $this->this_user_id,
                        "LPB",
                        $penerimaanBarang['no_penerimaan_barang'],
                        "-",
                    );

                    // GET PURCHASE ORDER
                    $po = $this->rmPurchaseOrderModel->find($p['purchase_order_id']);
                    // SUB DETAIL
                    $this->stockDetail2Model->insertStokDetail2(
                        $penerimaanBarang['bc_type'],
                        $stok,
                        $stokDetail,
                        $p['jml_masuk'],
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

    public function listBarangLPB()
    {
        $penerimaanBarangID = empty($this->request->getVar('penerimaan_barang_id')) ? null : decrypt($this->request->getVar('penerimaan_barang_id'));

        $rmPurchaseOrderID = json_decode($this->request->getVar('rm_purchase_order_id'));

        if (count($rmPurchaseOrderID) == 0) {
            return response()->setJSON([
                'result' => []
            ]);
        }

        return response()->setJSON($this->rmPurchaseOrderDetailModel->getListLPBBahanBaku($rmPurchaseOrderID, "LOKAL", "BAKU", $penerimaanBarangID));
    }

    public function generatePONo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouseID = $this->request->getVar('warehouseID');

        if (empty($warehouseID)) {
            $no = $this->pengembalianBarangModel->get_no(date('m'), date('Y'), $last_day, "", $warehouseID);
        } else {
            $warehouse = $this->warehousesModel->where('id', $warehouseID)->first();
            $no = $this->pengembalianBarangModel->get_no(date('m'), date('Y'), $last_day, $warehouse['code_warehouse'], $warehouseID);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no
        ]);
    }
}
