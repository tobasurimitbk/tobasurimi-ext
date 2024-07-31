<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengembalianBarangDetailModel;
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

class ReturnBarangPOBP extends BaseController
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
    protected $amPurchaseOrderModel;
    protected $amPurchaseOrderDetailModel;
    protected $supplierHargaModel;
    protected $stockDetailModel;
    protected $divisiModel;
    protected $kemasanModel;
    protected $stockModel;
    protected $stockDetail2Model;
    protected $this_user_id;
    protected $dompdf;
    protected $pengembalianBarangModel;
    protected $pengembalianBarangDetailModel;

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
        $this->amPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
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
        $this->pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
    }

    public function createAction()
    {
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $pengembalianBarangID = $this->request->getVar('pengembalian_barang_id');
        $noReturnBarang = $this->request->getVar('no_return_barang');
        $tanggalReturnBarang = $this->request->getVar('tanggal_return_barang');
        $barangs = $this->request->getVar('barangs');

        // var_dump($penerimaanBarangID);
        // var_dump($pengembalianBarangID);
        // var_dump($noReturnBarang);
        // var_dump($tanggalReturnBarang);
        // var_dump($barangs);
        // exit;

        $first = $this->pengembalianBarangModel->where('company_id', $this->this_company_id)->where('no_surat_jalan', $noReturnBarang)->first();
        if ($first != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "No Surat Jalan Sudah Ada",
                'status' => false
            ]);
        }

        // Cek Kekosongan
        $qtyReturn = 0;
        foreach (json_decode($barangs) as $b) {
            $qtyReturn += $b->qtyReturn;
        }

        if ($qtyReturn == 0) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Isikan minimal satu item barang yang akan direturn",
                'status' => false
            ]);
        }

        $pengembalianBarangID = $this->pengembalianBarangModel->insert([
            'company_id' => $this->this_company_id,
            'penerimaan_barang_id' => $penerimaanBarangID,
            'no_surat_jalan' => $this->request->getVar('no_return_barang'),
            "status_post" => "WAITING",
            "tanggal_surat_jalan" => $tanggalReturnBarang ? date_format(date_create_from_format("d/m/Y", $tanggalReturnBarang), "Y-m-d") : "",
        ]);

        foreach (json_decode($barangs) as $b) {
            if (isset($b->qtyReturn)) {
                if ($b->qtyReturn != 0 || $b->qtyReturn != "") {
                    $this->pengembalianBarangDetailModel->insert([
                        'pengembalian_barang_id' => $pengembalianBarangID,
                        'penerimaan_barang_detail_id' => $b->penerimaan_barang_detail_id,
                        'jumlah_return' => $b->qtyReturn,
                        'keterangan_return' => isset($b->ketReturn) ? $b->ketReturn : "",
                    ]);
                }
            }
        }

        return response()->setJSON([
            'message' => "Pengembalian Barang berhasil disimpan",
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
            "dataPenerimaanBarang" => $dataPenerimaanBarang,
            "dataPengembalianBarang" => $dataPengembalianBarang,
            "dataKemasan"   => $dataKemasan
        ];

        return view('Warehouse/returnBarang/formLokalBP', $data);
    }

    public function updateAction()
    {
        $penerimaanBarangID = decrypt($this->request->getVar('penerimaan_barang_id'));
        $pengembalianBarangID = $this->request->getVar('pengembalian_barang_id');
        $tanggalReturnBarang = $this->request->getVar('tanggal_return_barang');
        $barangs = $this->request->getVar('barangs');

        if (count(json_decode($barangs)) == 0) {
            return response()->setJSON([
                'message' => "Gagal Update: List barang tidak ditemukan",
                'token' => csrf_hash(),
                'status' => false
            ]);
        }

        $this->pengembalianBarangModel->update($pengembalianBarangID, [
            'company_id' => $this->this_company_id,
            "tanggal_surat_jalan" => $tanggalReturnBarang ? date_format(date_create_from_format("d/m/Y", $tanggalReturnBarang), "Y-m-d") : "",
        ]);

        foreach (json_decode($barangs) as $b) {
            $qtyReturn = isset($b->qtyReturn) ? $b->ketReturn : $b->jumlah_return;
            if ($qtyReturn != 0) {
                // UPDATE
                $this->pengembalianBarangDetailModel
                    ->update($b->pengembalian_barang_detail_id, [
                        'jumlah_return' => $qtyReturn,
                        'keterangan_return' => isset($b->ketReturn) ? $b->ketReturn : $b->keterangan_return,
                    ]);
            }
        }

        return response()->setJSON([
            'message' => "Berhasil update pengembalian barang",
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
        $id = decrypt($this->request->getVar('pengembalian_barang_id'));

        $this->pengembalianBarangModel->where('id', $id)->delete();
        $this->pengembalianBarangDetailModel->where('pengembalian_barang_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Pengembalian barang berhasil dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function posting()
    {
        $pengembalian_barang_id = decrypt($this->request->getVar('pengembalian_barang_id'));
        $penerimaan_barang_id = decrypt($this->request->getVar('penerimaan_barang_id'));

        try {
            $penerimaanBarang = $this->penerimaanBarangModel
                ->join('pengembalian_barang', 'pengembalian_barang.penerimaan_barang_id = penerimaan_barang.id')
                ->where('penerimaan_barang.id', $penerimaan_barang_id)
                ->where('pengembalian_barang.id', $pengembalian_barang_id)
                ->first();
            $penerimaanBarangList = $this->penerimaanBarangDetailModel
                ->join('pengembalian_barang_detail', 'pengembalian_barang_detail.penerimaan_barang_detail_id = penerimaan_barang_detail.id')
                ->where('pengembalian_barang_id', $pengembalian_barang_id)
                ->where('pengembalian_barang_detail.deletedAt', null)
                ->where('penerimaan_barang_detail.deletedAt', null)
                ->findAll();
            // var_dump($penerimaanBarang);
            // var_dump($penerimaanBarangList);
            // exit;

            // STOK BARANG DIINPUT
            foreach ($penerimaanBarangList as $p) {
                $stok = $this->stockModel->insertStok(
                    $penerimaanBarang['company_id'],
                    $penerimaanBarang['warehouse_id'],
                    $penerimaanBarang['divisi_id'],
                    $penerimaanBarang['tipe_bahan'] == "BAKU" ? "bahan_baku" : "bahan_penolong",
                    $p['barang_id'],
                    $p['spesifikasi_id'],
                    ($p['jumlah_return'] * -1)
                );

                // DETAIL
                $stokDetail = $this->stockDetailModel->insertStokDetail(
                    $stok,
                    $p['jumlah_return'],
                    "Out",
                    date('Y-m-d'),
                    $this->this_user_id,
                    "LPB",
                    $penerimaanBarang['no_surat_jalan'],
                    $p['keterangan_return'] ? $p['keterangan_return'] : "-"
                );

                // SUB DETAIL
                $this->stockDetail2Model->insertStokDetail2(
                    $penerimaanBarang['bc_type'],
                    $stok,
                    $stokDetail,
                    $p['jumlah_return'],
                    "-",
                    $penerimaanBarang['no_surat_jalan'],
                    $penerimaanBarang['no_penerimaan_barang'],
                    $penerimaanBarang['supplier_id'],
                    $p['harga'],
                    $p['harga_harian'],
                    $p['harga_bulanan'],
                );
            }
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => "Gagal Posting : Terjadi kesalahan saat mengeluarkan stok",
                'error' => $e->getTrace(),
                'token' => csrf_hash()
            ]);
        }

        $this->pengembalianBarangModel
            ->where(['id' => $pengembalian_barang_id])
            ->set(['status_post' => 'FINISH'])
            ->update();

        return response()->setJSON([
            'status' => true,
            'message' => "Return barang berhasil diposting",
            'token' => csrf_hash()
        ]);
    }


    public function listBarangLPB()
    {
        $penerimaanBarangID = empty($this->request->getVar('penerimaan_barang_id')) ? null : decrypt($this->request->getVar('penerimaan_barang_id'));
        $amPurchaseOrderID = json_decode($this->request->getVar('am_purchase_order_id'));
        if (count($amPurchaseOrderID) == 0) {
            return response()->setJSON([
                'result' => []
            ]);
        }
        return response()->setJSON($this->amPurchaseOrderDetailModel->getListLPBBahanPenolong($amPurchaseOrderID, "LOKAL", "PENOLONG", $penerimaanBarangID));
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
