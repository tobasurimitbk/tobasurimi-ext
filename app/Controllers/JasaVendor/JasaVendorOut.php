<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\JasaVendorOutModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\ProsesRebusModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampModel;
use App\Models\SupplierModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use Exception;

class JasaVendorOut extends BaseController
{
    protected $vendorModel;
    protected $this_user_id;
    protected $this_company_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $jasaVendorOutModel;
    protected $jasaVendorOutDetailModel;
    protected $jasaVendorInModel;
    protected $warehouseModel;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $kemasanModel;
    protected $satuanModel;
    protected $supplierModel;
    protected $prosesRebusModel;
    protected $rmPurchaseOrderModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->vendorModel = new VendorModel();
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->jasaVendorOutModel = new JasaVendorOutModel();
        $this->jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->kemasanModel = new KemasanModel();
        $this->satuanModel = new SatuansModel();
        $this->supplierModel = new SupplierModel();
        $this->prosesRebusModel = new ProsesRebusModel();
        $this->rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
        ];
        return view('jasaVendor/out/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
            "status" => $this->request->getVar("status"),
            "start_date" => $this->request->getVar('start_date'),
            "end_date" => $this->request->getVar('end_date'),
            "no_surat_jalan" => $this->request->getVar("no_surat_jalan"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'jasa_vendor_out.company_id' => $this->this_company_id,
            'jasa_vendor_out.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->jasaVendorOutModel->getListNew($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $jasaVendorIn = $this->jasaVendorInModel->where('company_id', $this->this_company_id)->like('multiple_jasa_vendor_out_id', $data->id)->where('deletedAt', null)->first();

            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel
                ->getJasaVendorOutDetailForIndexNew($data->id);

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_surat_jalan"        => $data->no_surat_jalan,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "barang_name"           => $jasaVendorOutDetail[0] ?? '',
                "total_item"            => count($jasaVendorOutDetail),
                "vendor_name"           => $data->vendor_name,
                "status_posting"        => $data->status_posting,
                "status_closed"         => $data->status_closed == "1" ? "CLOSED" : "OPEN",
                "un_posting"            => $jasaVendorIn == null ? 1 : 0,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'supplier' => $this->supplierModel->getSupplierJasVend()
        ];
        return view('jasaVendor/out/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $jasaVendorOut = $this->jasaVendorOutModel->find($id);

        if ($jasaVendorOut == null) {
            return redirect()->to('jasa-vendor-out');
        }

        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'jasaVendorOut' => $jasaVendorOut,
            'warehouse' => $this->warehouseModel->where('deletedAt', null)->where('divisi_id', $jasaVendorOut['divisi_id'])->orderBy('warehouse_name', "ASC")->findAll(),
            'jasaVendorOutDetail' => $this->jasaVendorOutDetailModel->getJasaVendorOutDetailNew($id),
            'supplier' => $this->supplierModel->getSupplierByType("BAHAN BAKU")
        ];
        return view('jasaVendor/out/form', $data);
    }

    public function createAction()
    {
        $check = $this->jasaVendorOutModel->where('no_surat_jalan', $this->request->getVar('no_surat_jalan'))->first();

        if ($check != null) {
            return response()->setJSON([
                'message' => "Nomor Surat Jalan Sudah Ada",
                'token' => csrf_hash(),
                'status' => false,
            ]);
        }

        $barang = json_decode($this->request->getVar('listBarang'));      

        $id = $this->jasaVendorOutModel->insert([
            'company_id' => $this->this_company_id,
            'vendor_id' => $this->request->getVar('vendor_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            "tanggal" => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
            'tipe_barang' => "bahan_baku",
            'no_kontainer' => $this->request->getVar('no_kontainer'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'keterangan' => $this->request->getVar('keterangan'),
        ]);

        foreach ($barang as $b) {
            // CEK STOK DARI PROSES REBUS


                    if ($b->reference_type == "PROSES REBUS") {
                        $doc = $this->prosesRebusModel
                            ->select("no_rebus")
                            ->where("id", $b->reference_id)
                            ->first();
                        $stock_dokumen = $doc ? $doc['no_rebus'] : null;
                    } else {
                        $doc = $this->rmPurchaseOrderModel
                            ->select("po_no")
                            ->where("id", $b->po_id)
                            ->first();
                        $stock_dokumen = $doc ? $doc['po_no'] : null;
                    }

            if ($this->request->getVar('type_asal_barang') == "SUPPLIER") {
                // STOK DARI SUPPLIER

                    $this->jasaVendorOutDetailModel->insert([
                        'proses_rebus_id'     => $b->reference_type == "PROSES REBUS" ? $b->reference_id : null,
                        'jasa_vendor_out_id'  => $id,
                        'stock_out_detail_id' => $b->id,
                        'bc_out_id'           => $b->bc_id,
                        'stock_dokumen'       => $stock_dokumen, // ✅ masukin hasil query
                        'satuan_id'           => $b->satuan_id,
                        'po_id'               => $b->po_id,
                        'keterangan'          => $b->keterangan,
                        'qty'                 => $b->qty,
                        'qty_kotor'           => $b->qty,
                    ]);


            } else {
                    // STOK DARI JASA VENDOR
                    $this->jasaVendorOutDetailModel->insert([
                        'proses_rebus_id'     => $b->reference_type == "PROSES REBUS" ? $b->reference_id : null,
                        'jasa_vendor_out_id'  => $id,
                        'stock_out_detail_id' => $b->id,
                        'bc_out_id'           => $b->bc_id,
                        'stock_dokumen'       => $stock_dokumen, // ✅ masukin hasil query
                        'satuan_id'           => $b->satuan_id,
                        'po_id'               => $b->po_id,
                        'keterangan'          => $b->keterangan,
                        'qty'                 => $b->qty,
                        'qty_kotor'           => $b->qty,
                    ]);
                }
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil disimpan",
            'token' => csrf_hash(),
            'id' => encrypt($id)
        ]);
    }

    public function updateAction()
    {
        $decodedId = decrypt($this->request->getVar('id'));
        // 🔹 Cek apakah data utama masih ada
        $jasaVendorOut = $this->jasaVendorOutModel->find($decodedId);
        if (!$jasaVendorOut) {
            return response()->setJSON([
                'message' => "Data tidak ditemukan",
                'token'   => csrf_hash(),
                'status'  => false,
            ]);
        }

        // 🔹 Cek duplikasi no_surat_jalan selain current ID
        $check = $this->jasaVendorOutModel
            ->where('no_surat_jalan', $this->request->getVar('no_surat_jalan'))
            ->where('id !=', $decodedId)
            ->first();

        if ($check != null) {
            return response()->setJSON([
                'message' => "Nomor Surat Jalan sudah digunakan oleh data lain",
                'token'   => csrf_hash(),
                'status'  => false,
            ]);
        }

        // 🔹 Update data utama
        $this->jasaVendorOutModel->update($decodedId, [
            'vendor_id'             => $this->request->getVar('vendor_id'),
            'divisi_id'             => $this->request->getVar('divisi_id'),
            'warehouse_id'          => $this->request->getVar('warehouse_id'),
            'no_surat_jalan'        => $this->request->getVar('no_surat_jalan'),
            "tanggal"               => $this->request->getVar("tanggal")
                ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d")
                : "",
            'no_kontainer'          => $this->request->getVar('no_kontainer'),
            'tipe_pengambilan_stock'=> $this->request->getVar('type_pengambilan_stock'),
            'keterangan'            => $this->request->getVar('keterangan'),
            'updatedAt'             => date('Y-m-d H:i:s'),
        ]);

        // 🔹 Hapus semua detail lama dulu
        $this->jasaVendorOutDetailModel
            ->where('jasa_vendor_out_id', $decodedId)
            ->delete();

        // 🔹 Ambil list barang baru dari request
        $barang = json_decode($this->request->getVar('listBarang'));

        // 🔹 Insert ulang detail
        foreach ($barang as $b) {

            // Cek dokumen asal (Rebus atau PO)
            if ($b->reference_type == "PROSES REBUS") {
                $doc = $this->prosesRebusModel
                    ->select("no_rebus")
                    ->where("id", $b->reference_id)
                    ->first();
                $stock_dokumen = $doc ? $doc['no_rebus'] : null;
            } else {
                $doc = $this->rmPurchaseOrderModel
                    ->select("po_no")
                    ->where("id", $b->po_id)
                    ->first();
                $stock_dokumen = $doc ? $doc['po_no'] : null;
            }

            $this->jasaVendorOutDetailModel->insert([
                'proses_rebus_id'     => $b->reference_type == "PROSES REBUS" ? $b->reference_id : null,
                'jasa_vendor_out_id'  => $decodedId,
                'stock_out_detail_id' => $b->id,
                'bc_out_id'           => $b->bc_id,
                'stock_dokumen'       => $stock_dokumen,
                'satuan_id'           => $b->satuan_id,
                'po_id'               => $b->po_id,
                'keterangan'          => $b->keterangan,
                'qty'                 => $b->qty,
                'qty_kotor'           => $b->qty,
            ]);
        }

        return response()->setJSON([
            'status'  => true,
            'message' => "Data Jasa Vendor Out berhasil diperbarui",
            'token'   => csrf_hash(),
            'id'      => encrypt($decodedId),
        ]);
    }

    public function updateActionNew()
    {
        $id = decrypt($this->request->getVar('id'));

        // Update header transaksi
        $this->jasaVendorOutModel->update($id, [
            'vendor_id'             => $this->request->getVar('vendor_id'),
            'divisi_id'             => $this->request->getVar('divisi_id'),
            'warehouse_id'          => $this->request->getVar('warehouse_id'),
            'no_kontainer'          => $this->request->getVar('no_kontainer'),
            'tipe_pengambilan_stock'=> $this->request->getVar('type_pengambilan_stock'),
            'keterangan'            => $this->request->getVar('keterangan'),
            'no_surat_jalan'        => $this->request->getVar('no_surat_jalan'),
            'tanggal'               => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
            'tipe_barang'           => "bahan_baku",

        ]);

        $barang = json_decode($this->request->getVar('listBarang'));

        // Hapus dulu semua detail lama
        $this->jasaVendorOutDetailModel->where('jasa_vendor_out_id', $id)->delete();

        foreach ($barang as $b) {
            $stockId = $b->id;

            $stockDetail = $this->stockDetail2Model->getStockListDetailNew($stockId);

            $qty_stok_sistem = $stockDetail['stok_total'] ?? 0;
            $qty_input_user  = $b->qty;

            // Hitung qty bersih & kotor
            $qty_bersih = min($qty_input_user, $qty_stok_sistem);
            $qty_kotor  = max($qty_input_user - $qty_stok_sistem, 0);

            if ($stockDetail) {
                // Validasi stok
                if (floatval($stockDetail['stok_total']) < floatval($b->qty)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => "Stok tidak mencukupi untuk dokumen {$b->stock_dokumen}. Sisa: {$stockDetail['stok_total']}, diminta: {$b->qty}"
                    ]);
                }

                $stockRebus = $this->prosesRebusModel
                    ->where('no_rebus', $stockDetail['no_dokumen_1'])
                    ->first();

                // Cek apakah detail sudah ada
                $existing = $this->jasaVendorOutDetailModel
                    ->where('jasa_vendor_out_id', $id)
                    ->where('stock_out_id', $stockId)
                    ->first();

                $dataDetail = [
                    'proses_rebus_id'    => $stockRebus == null ? null : $stockRebus['id'],
                    'jasa_vendor_out_id' => $id,
                    'stock_out_id'       => $stockId,
                    'bc_out_id'          => !empty($b->bc_id) ? $b->bc_id : 0,
                    'no_aju_out'         => !empty($b->no_aju) ? $b->no_aju : '-',
                    'stock_dokumen'      => !empty($b->stock_dokumen) ? $b->stock_dokumen : '-',
                    'qty'                => $qty_bersih,
                    'qty_kotor'          => $qty_kotor,
                ];

                if ($existing) {
                    // Update kalau sudah ada
                    $this->jasaVendorOutDetailModel->update($existing['id'], $dataDetail);
                } else {
                    // Insert kalau belum ada
                    $this->jasaVendorOutDetailModel->insert($dataDetail);
                }
            }
        }

        return response()->setJSON([
            'status'  => true,
            'message' => "Jasa vendor pengeluaran barang berhasil diupdate",
            'token'   => csrf_hash(),
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->jasaVendorOutModel->delete($id);
        $this->jasaVendorOutDetailModel->where('jasa_vendor_out_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil dihapus",
            'token' => csrf_hash(),
        ]);
    }

    public function posting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $stockRevampModel = new StockRevampModel();
            $id = decrypt($this->request->getVar('id'));

            // Validasi data exists
            $jasaVendorOut = $this->jasaVendorOutModel->where('id', $id)->where('deletedAt', NULL)->first();
            if (!$jasaVendorOut) {
                throw new \Exception("Data jasa vendor tidak ditemukan");
            }

            // Cek jika sudah diposting
            if ($jasaVendorOut['status_posting'] == '1') {
                throw new \Exception("Data sudah diposting sebelumnya");
            }

            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel
                ->where('jasa_vendor_out_id', $id)
                ->where('deletedAt', null)
                ->findAll();

            if (empty($jasaVendorOutDetail)) {
                throw new \Exception("Detail jasa vendor tidak ditemukan");
            }

            foreach ($jasaVendorOutDetail as $j) {
                $data = [
                    "stock_detail_id" => $j['stock_out_detail_id'],
                    "qty_digunakan" => $j['qty'],
                    "no_dokumen" => $jasaVendorOut['no_surat_jalan']
                ];

                // Panggil model - jika gagal akan throw exception
                $result = $stockRevampModel->outStockRevamp($db, $data);
                
                if (!$result) {
                    throw new \Exception("Gagal memproses stock untuk detail ID: {$j['stock_out_detail_id']}");
                }
            }

            // Update status posting
            $this->jasaVendorOutModel->update($id, ['status_posting' => '1']);

            // Commit transaksi
            $db->transCommit();

            return $this->response->setJSON([
                'status' => true,
                'message' => "Jasa vendor pengeluaran barang berhasil diposting",
                'token' => csrf_hash(),
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'status' => false,
                'message' => "Gagal posting jasa vendor: " . $e->getMessage(),
                'token' => csrf_hash(),
            ]);
            
        } catch (\Throwable $th) {
            $db->transRollback();
            
            return $this->response->setJSON([
                'status' => false,
                'message' => "Terjadi kesalahan sistem: " . $th->getMessage(),
                'token' => csrf_hash(),
            ]);
        }
    }

    public function unposting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        $stockRevampModel = new StockRevampModel();

        try {
            $id = decrypt($this->request->getVar('id'));
            
            // Validasi apakah data exists
            $jasaVendorOut = $this->jasaVendorOutModel
                ->where('id', $id)
                ->first();
            if (!$jasaVendorOut) {
                throw new \Exception("Data Jasa Vendor Out tidak ditemukan");
            }


            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel
                ->where('jasa_vendor_out_id', $id)
                ->findAll();

            if (empty($jasaVendorOutDetail)) {
                throw new \Exception("Detail Jasa Vendor Out tidak ditemukan");
            }

            foreach ($jasaVendorOutDetail as $j) {
                $data = [
                    "stock_detail_asal"     => $j["stock_out_detail_id"],    
                    "qty_diterima_asal"     => $j["qty"],
                    "no_dokumen"            => $jasaVendorOut["no_surat_jalan"],
                    "keterangan"            => "UNPOST JASA VENDOR KELUAR",
                ];

                // Panggil model - jika gagal akan throw exception
                $stockRevampModel->unpostStockKeluar($db, $data);
            }

            // update status Jasa Vendor Out
            $this->jasaVendorOutModel->update($id, [
                'status_posting' => '0'
            ]);

            // commit transaksi
            $db->transCommit();

            return $this->response->setJSON([
                'message' => "Jasa Vendor Out berhasil di-unpost",
                'status'  => true,
                'token'   => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();

            return $this->response->setJSON([
                'message' => "Gagal unpost Jasa Vendor Out: " . $e->getMessage(),
                'status'  => false,
                'token'   => csrf_hash()
            ]);
            
        } catch (\Throwable $th) {
            $db->transRollback();

            return $this->response->setJSON([
                'message' => "Terjadi kesalahan sistem: " . $th->getMessage(),
                'status'  => false,
                'token'   => csrf_hash()
            ]);
        }
    }

    public function close()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->jasaVendorOutModel->update($id, ['status_closed' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil diclose",
            'token' => csrf_hash(),
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $selectQryJasaVendor = "
            jasa_vendor_out.*,
            vendors.name as vendor_name,
            vendors.address
        ";

        $jasaVendorOut = $this->jasaVendorOutModel->select($selectQryJasaVendor)
            ->join('vendors', 'vendors.id = jasa_vendor_out.vendor_id')
            ->where('jasa_vendor_out.id', $id)
            ->first();

        if ($jasaVendorOut == null) {
            return redirect()->to('jasa-vendor-out');
        }

        $selectQryJasaVendorDetail = "
            SUM(jasa_vendor_out_detail.qty) as qty,
            satuans.kode_satuan,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi
        ";

        // $jasaVendorOutDetail = $this->jasaVendorOutDetailModel->select($selectQryJasaVendorDetail)
        //     ->join('stock', 'stock.id = jasa_vendor_out_detail.stock_out_id')
        //     ->join('barang_master', 'barang_master.id = stock.barang1_id')
        //     ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
        //     ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
        //     ->where('jasa_vendor_out_id', $jasaVendorOut['id'])
        //     ->groupBy('jasa_vendor_out_detail.stock_out_id')
        //     ->findAll();

        $jasaVendorOutDetail = $this->jasaVendorOutDetailModel->getJasaVendorOutDetail2New($id);

        $data = [
            'jasaVendorOut' => $jasaVendorOut,
            'jasaVendorDetail' => $jasaVendorOutDetail
        ];

        $this->dompdf->loadHtml(view('jasaVendor/out/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Jasa Vendor Barang Keluar", array("Attachment" => false));
    }

    public function dropdownListBarangIsInit()
    {
        $stockRevampModel = new StockRevampModel();
        $asalBarang = $this->request->getVar('asal_barang');

        if ($asalBarang == "SUPPLIER") {
            // Stok Dengan Master Barang Saja
            $data = $this->barangMasterModel->getListBarangmaster(
                $this->request->getVar('type_barang')
            );
        } else {
            // Stok Dengan Master Barang & Spesifikasi
            $data = $stockRevampModel->getBarangRebusAndStock(
                $this->request->getVar('type_barang'),
                $this->request->getVar('divisi_id'),
                $this->request->getVar('warehouse_id')
            );
        }

        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListStockByStockID()
    {
        $supplierId = $this->request->getVar('supplier_id');
        $vendorId = $this->request->getVar('vendor_id');
        $stockRevampDetailModel = new StockRevampDetailModel();
        $stockRevampModel = new StockRevampModel();

        if (!empty($this->request->getVar('stock_id')) && (!empty($supplierId) || !empty($vendorId))) {

            if (!empty($supplierId)) {
                    // Untuk Dari Po & Supplier
                $condition = [
                        'rm_purchase_orders.supplier_id' => $this->request->getVar('supplier_id'),
                    ];

                $dataResult = $stockRevampDetailModel->getStockListWithAddConditionForProsesRebus(
                    $condition,
                    $this->request->getVar('spesifikasi_id'),
                );


                // $stock = $stockRevampModel->where('id', $this->request->getVar('stock_id'))->first();
                $resultArr = array();

                $metaDataModel = new MetadataModel();

            
                    // Khsus Dari Supplier
                    for ($i = 0; $i < count($dataResult); $i++) {
                                $bcType = $metaDataModel->find($dataResult[$i]['bc_id']);
                                $dataResult[$i]['po_no'] = $dataResult[$i]['po_no'];
                                $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                                $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                                $dataResult[$i]['barang'] = strtoupper($dataResult[$i]['barang']);
                                $dataResult[$i]['stock_date'] = $dataResult == null ? "-" : date('d/m/Y', strtotime($dataResult[$i]['po_date']));
                                $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                                $dataResult[$i]['stok_total_bersih']          = round((float)$dataResult[$i]['stok_total_bersih'], 2);
                                $dataResult[$i]['stok_total_diterima'] = round((float)$dataResult[$i]['stok_total_diterima'], 2);
                                $dataResult[$i]['total_penerimaan']    = round((float)$dataResult[$i]['total_penerimaan'], 2);
                                $dataResult[$i]['stok_total_kotor']    = round((float)$dataResult[$i]['stok_total_diterima'] - (float)$dataResult[$i]['stok_total_bersih'], 2);


                                array_push($resultArr, $dataResult[$i]);
                    }  
            
            } else {

                $condition = [
                    'stock_revamp_detail.reference_type' => "JASA VENDOR",
                    'jasa_vendor_in.vendor_id' => $vendorId,
                ];


                $dataResult = $stockRevampDetailModel->getStockListWithAddConditionForProsesRebusFromVendor(
                    $condition,
                    $this->request->getVar('spesifikasi_id'),
                );


                // $stock = $stockRevampModel->where('id', $this->request->getVar('stock_id'))->first();
                $resultArr = array();

                $metaDataModel = new MetadataModel();

                // Khsus Dari Vendor
                for ($i = 0; $i < count($dataResult); $i++) {
                                $bcType = $metaDataModel->find($dataResult[$i]['bc_id']);
                                $dataResult[$i]['no_penerimaan_surat_jalan'] = $dataResult[$i]['no_penerimaan_surat_jalan'];
                                $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                                $dataResult[$i]['satuan'] = $dataResult[$i]['kode_satuan'];
                                $dataResult[$i]['barang'] = strtoupper($dataResult[$i]['barang']);
                                $dataResult[$i]['stock_date'] = $dataResult == null ? "-" : date('d/m/Y', strtotime($dataResult[$i]['tanggal']));
                                $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                                $dataResult[$i]['stok_total_bersih']          = round((float)$dataResult[$i]['stok_total_bersih'], 2);
                                $dataResult[$i]['stok_total_diterima'] = round((float)$dataResult[$i]['stok_total_diterima'], 2);
                                $dataResult[$i]['total_penerimaan']    = round((float)$dataResult[$i]['total_penerimaan'], 2);
                                $dataResult[$i]['stok_total_kotor']    = round((float)$dataResult[$i]['stok_total_diterima'] - (float)$dataResult[$i]['stok_total_bersih'], 2);


                                array_push($resultArr, $dataResult[$i]);
                }  
            }


            return response()->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }

    public function getListStockJasaVendorOut()
    {

        $barangMasterId = $this->request->getVar('barang_master_id');
        $supplierId = $this->request->getVar('supplier_id');
        $vendorId = $this->request->getVar('vendor_id');
        $stockRevampDetailModel = new StockRevampDetailModel();

            if (!empty($supplierId) && !empty($barangMasterId)) {
                // Untuk Dari Po & Supplier
                $condition = [
                    'rm_purchase_orders.supplier_id' => $supplierId,
                    'stock_revamp.barang_master_id' => $barangMasterId,
                    'stock_revamp_detail.qty_diterima >' => 0,
                ];
                $dataResult = $stockRevampDetailModel->getStockListWithAddConditionForJasaVendorOut($condition);

                
                $resultArr = [];
                $stock_dokumen = "";
                foreach ($dataResult as $item) {

                     if ($item['reference_type'] == "PROSES REBUS") {
                        $doc = $this->prosesRebusModel
                            ->select("no_rebus")
                            ->where("id", $item['reference_id'])
                            ->first();
                        $stock_dokumen = $doc ? $doc['no_rebus'] : null;
                    } else {
                        $doc = $this->rmPurchaseOrderModel
                            ->select("po_no")
                            ->where("id", $item['rm_purchase_order_id'])
                            ->first();
                        $stock_dokumen = $doc ? $doc['po_no'] : null;
                    }

                    
                    $resultArr[] = [
                        'id' => $item['id'],
                        'sumber' => $item['reference_type'],
                        'bc_id' => $item['bc_id'],
                        'supplier_name' => $item['supplier_name'],
                        'bc_type' => $item['type_bc'],
                        'stock_dokumen' => $stock_dokumen ?? '-',
                        'stock_date' => $item['po_date'],
                        'barang' => $item['barang'],
                        'reference_id' => $item['reference_id'],
                        'po_id' => $item['rm_purchase_order_id'],
                        'reference_type' => $item['reference_type'],
                        'satuan' => $item['kode_satuan'],
                        'satuan_id' => $item['satuan_id'],
                        'stok_total' => floatval($item['stok_total_diterima'])
                    ];
                }
            } else {
                // Untuk Dari Jasa Vendor
                $condition = [
                    'stock_revamp_detail.reference_type' => "JASA VENDOR",
                    'stock_revamp.barang_master_id' => $barangMasterId,
                    'stock_revamp_detail.qty_diterima >' => 0,
                    'jasa_vendor_in.vendor_id' => $vendorId,
                ];

                $dataResult = $stockRevampDetailModel->getStockListWithAddConditionForJasaVendorOutByVendor($condition);


                $resultArr = [];
                foreach ($dataResult as $item) {
                    if (floatval($item['stok_total_diterima']) <= 0) continue;
                    $noJasaVendorIn = $item['no_penerimaan_vendor'];
                   
                    $jasaVendorInQuery = $this->jasaVendorInModel
                        ->select('jasa_vendor_in.*, vendors.name as nama_vendor')
                        ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                        ->where('jasa_vendor_in.no_penerimaan_surat_jalan', $noJasaVendorIn)
                        ->where('jasa_vendor_in.company_id', $this->this_company_id);

                    // Tambahin filter vendor_id cuma kalau ada nilai
                    if (!empty($vendorId)) {
                        $jasaVendorInQuery->where('vendor_id', $vendorId);
                    }

                    $jasaVendorIn = $jasaVendorInQuery->first();

                    if (!$jasaVendorIn) continue;

                    $resultArr[] = [
                        'id' => $item['id'],
                        'sumber' => $item['reference_type'],
                        'bc_id' => $item['bc_id'],
                        'supplier_name' => $jasaVendorIn['nama_vendor'],
                        'bc_type' => $item['type_bc'],
                        'stock_dokumen' => $item['no_penerimaan_vendor'] ?? '-',
                        'stock_date' => $jasaVendorIn['tanggal'],
                        'barang' => $item['barang'],
                        'reference_id' => $item['reference_id'],
                        'po_id' => $item['rm_purchase_order_id'],
                        'reference_type' => $item['reference_type'],
                        'satuan' => $item['kode_satuan'],
                        'satuan_id' => $item['satuan_id'],
                        'stok_total' => floatval($item['stok_total_diterima'])
                    ];
                }
            }

            return $this->response->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
            ]);
        
    }


    public function getJasaVendorOutNo()
    {
        $tanggal = date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))));
        $warehouse_id = $this->request->getVar('warehouse_id');

        if (empty($tanggal) || empty($warehouse_id)) {
            $no = $this->jasaVendorOutModel->get_no(
                date('m'),
                date('Y'),
                ""
            );
        } else {

            $tanggalExplode = explode('-', $tanggal);
            $year = $tanggalExplode[0];
            $month = $tanggalExplode[1];

            $warehouse = $this->warehouseModel->where('id', $warehouse_id)->first();
            $divisi = $this->divisiModel->where('id', $warehouse['divisi_id'])->first();
            $no = $this->jasaVendorOutModel->get_no(
                $month,
                $year,
                $divisi['divisi']
            );
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
