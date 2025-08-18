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

        $dataQry = $this->jasaVendorOutModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $jasaVendorIn = $this->jasaVendorInModel->where('company_id', $this->this_company_id)->like('multiple_jasa_vendor_out_id', $data->id)->where('deletedAt', null)->first();

            $jasaVendorOutDetail = $this->jasaVendorOutDetailModel
                ->getJasaVendorOutDetail2New($data->id);

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_surat_jalan"        => $data->no_surat_jalan,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
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
            'jasaVendorOutDetail' => $this->jasaVendorOutDetailModel->getJasaVendorOutDetail2New($id),
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

        $barang = json_decode($this->request->getVar('listBarang'));

        // var_dump($barang);
        // die;

        foreach ($barang as $b) {
            // CEK STOK DARI PROSES REBUS
            $stockId = $b->id;
            if ($this->request->getVar('type_asal_barang') == "SUPPLIER") {
                // STOK DARI SUPPLIER
                $stockDetail = $this->stockDetail2Model->getStockListDetailNew(
                    $stockId, 
                );

                if ($stockDetail) {

                    // Validasi stok
                    if (floatval($stockDetail['stok_total']) < floatval($b->qty)) {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => "Stok tidak mencukupi untuk dokumen {$b->stock_dokumen}. Sisa: {$stockDetail['stok_total']}, diminta: {$b->qty}"
                        ]);
                    }

                    $stockRebus = $this->prosesRebusModel->where('no_rebus', $stockDetail['no_dokumen_1'])->first();
                    $this->jasaVendorOutDetailModel->insert([
                        'proses_rebus_id'   => $stockRebus == null ? null : $stockRebus['id'],
                        'jasa_vendor_out_id'=> $id,
                        'stock_out_id'      => $stockId,
                        'bc_out_id'         => $b->bc_id,
                        'no_aju_out'        => $b->no_aju,
                        'stock_dokumen'     => $b->stock_dokumen,
                        'qty'               => $b->qty,
                    ]);
                }

            } else {
                // STOK JASA VENDOR
                // $stockDetail = $this->stockDetail2Model->getStockListDetailNew(
                //     $stockId
                // );
                $stockData = $this->stockDetail2Model->find($stockId);
                $stockDetail = $this->stockDetail2Model->getStockListDetailNew(
                     $stockId
                );

                if ($stockDetail) {

                    // Validasi stok
                    if (floatval($stockDetail['stok_total']) < floatval($b->qty)) {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => "Stok tidak mencukupi untuk dokumen {$b->stock_dokumen}. Sisa: {$stockDetail['stok_total']}, diminta: {$b->qty}"
                        ]);
                    }

                    $stockRebus = $this->prosesRebusModel
                        ->where('no_rebus', $stockDetail['no_dokumen_1'])
                        ->first();

                    $this->jasaVendorOutDetailModel->insert([
                        'proses_rebus_id'   => $stockRebus == null ? null : $stockRebus['id'],
                        'jasa_vendor_out_id'=> $id,
                        'stock_out_id'      => $stockId,
                        'bc_out_id'     => !empty($b->bc_id) ? $b->bc_id : 0,
                        'no_aju_out'    => !empty($b->no_aju) ? $b->no_aju : '-',
                        'stock_dokumen' => !empty($b->stock_dokumen) ? $b->stock_dokumen : '-',
                        'qty'               => $b->qty
                    ]);
                }
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
        $id = decrypt($this->request->getVar('id'));

        $this->jasaVendorOutModel->update($id, [
            'vendor_id' => $this->request->getVar('vendor_id'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'no_kontainer' => $this->request->getVar('no_kontainer'),
            'tipe_pengambilan_stock' => $this->request->getVar('type_pengambilan_stock'),
            'keterangan' => $this->request->getVar('keterangan'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
        ]);

        $barang = json_decode($this->request->getVar('listBarang'));
        // Hapus dahulu detail nya
        $this->jasaVendorOutDetailModel->where('jasa_vendor_out_id', $id)->delete();

        foreach ($barang as $b) {

            // CEK STOK DARI PROSES REBUS
            $stockId = decrypt($b->id);
            $stockIdArr = json_decode($stockId);

            if (is_array($stockIdArr)) {
                // STOK DARI SUPPLIER
                $qtyDiambil = 0;

                foreach ($stockIdArr as $s) {
                    $stockDetail = $this->stockDetail2Model->getStockListDetail($s, $b->bc_id, $b->no_aju, $b->stock_dokumen);

                    if ($stockDetail) {
                        $qtyYangTersedia = $stockDetail['stok_total'];
                        $qtyYangDiperlukan = $b->qty - $qtyDiambil;
                        $qtyDiambilSekarang = min($qtyYangTersedia, $qtyYangDiperlukan);
                        $qtyDiambil += $qtyDiambilSekarang;

                        $stockRebus = $this->prosesRebusModel->where('no_rebus', $stockDetail['no_dokumen_1'])->first();
                        $this->jasaVendorOutDetailModel->insert([
                            'proses_rebus_id' => $stockRebus == null ? null : $stockRebus['id'],
                            'jasa_vendor_out_id' => $id,
                            'stock_out_id' => $stockDetail['stock_id'],
                            'bc_out_id' => $b->bc_id,
                            'no_aju_out' => $b->no_aju,
                            'stock_dokumen' => $b->stock_dokumen,
                            'qty' => $qtyDiambilSekarang,
                        ]);

                        if ($qtyDiambil >= $b->qty) {
                            break;
                        }
                    }
                }
            } else {
                // STOK JASA VENDOR
                $stockDetail = $this->stockDetail2Model->getStockListDetail(
                    $stockId,
                    $b->bc_id,
                    $b->no_aju,
                    $b->stock_dokumen
                );
                $stockRebus = $this->prosesRebusModel->where('no_rebus', $stockDetail['no_dokumen_1'])->first();
                $this->jasaVendorOutDetailModel->insert([
                    'proses_rebus_id' => $stockRebus == null ? null : $stockRebus['id'],
                    'jasa_vendor_out_id' => $id,
                    'stock_out_id' => $stockId,
                    'bc_out_id' => $b->bc_id,
                    'no_aju_out' => $b->no_aju,
                    'stock_dokumen' => $b->stock_dokumen,
                    'qty' => $b->qty
                ]);
            }
        }

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil diupdate",
            'token' => csrf_hash(),
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
        $id = decrypt($this->request->getVar('id'));

        // BARANG OUT KE VENDOR
        // Insert To Inventori (-)
        $jasaVendorOut = $this->jasaVendorOutModel->find($id);
        $jasaVendorOutDetail = $this->jasaVendorOutDetailModel->where('jasa_vendor_out_id', $id)->where('deletedAt', null)->findAll();


        // // var_dump($jasaVendorOut);
        // var_dump($jasaVendorOutDetail);
        // die;

        foreach ($jasaVendorOutDetail as $j) {
            $stockDetail2 = $this->stockDetail2Model->find($j['stock_out_id']);
            $stock = $this->stockModel->find($stockDetail2['stock_id']);
            $qty = $j['qty'];

            if ($stock) {
                if ($stock['tipe_barang'] == "kemasan") {
                    $barang2_id = $stock['kemasan_id'];
                } else {
                    $barang2_id = $stock['barang2_id'];
                }
            }

            // BARANG LAMA
            // $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
            //     $j['stock_out_id'],
            //     $j['bc_out_id'],
            //     $j['no_aju_out'],
            //     $j['stock_dokumen']
            // );

            $stockOldDetail = $this->stockDetail2Model->getStockListDetailNew(
                $j['stock_out_id'],
            );

            $stok = $this->stockModel->insertStok(
                $jasaVendorOut['company_id'],
                $jasaVendorOut['warehouse_id'],
                $jasaVendorOut['divisi_id'],
                $stock['tipe_barang'],
                $stock['barang1_id'],
                $barang2_id,
                ($qty * -1)
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                "Out",
                $jasaVendorOut['tanggal'],
                $this->this_user_id,
                "JASA VENDOR",
                "-",
                $jasaVendorOut['keterangan']
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $j['bc_out_id'],
                $stok,
                $stokDetail,
                $qty,
                $j['no_aju_out'],
                $jasaVendorOut['no_surat_jalan'],
                $j['stock_dokumen'],
                $stockOldDetail['supplier_id'],
                $stockOldDetail['harga_umum'],
                $stockOldDetail['harga_harian'],
                $stockOldDetail['harga_bulanan'],
                $stockOldDetail['no_po']
            );
        }

        $this->jasaVendorOutModel->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Jasa vendor pengeluaran barang berhasil diposting",
            'token' => csrf_hash(),
        ]);
    }

    public function unPosting()
    {
        $id = decrypt($this->request->getVar('id'));

        $result =  $this->stockModel->unPostingStockJasaVendorOut($id);
        if ($result) {
            $this->jasaVendorOutModel->update($id, ['status_posting' => '0']);
            return response()->setJSON([
                'status' => true,
                'message' => "Jasa vendor pengeluaran barang berhasil di Unposting",
                'token' => csrf_hash(),
            ]);
        }
        return response()->setJSON([
            'status' => false,
            'message' => "terjadi kesalahan saat unposting stok",
            'token' => csrf_hash(),
        ]);
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

        $jasaVendorOutDetail = $this->jasaVendorOutDetailModel->select($selectQryJasaVendorDetail)
            ->join('stock', 'stock.id = jasa_vendor_out_detail.stock_out_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
            ->where('jasa_vendor_out_id', $jasaVendorOut['id'])
            ->groupBy('jasa_vendor_out_detail.stock_out_id')
            ->findAll();

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
        $asalBarang = $this->request->getVar('asal_barang');

        if ($asalBarang == "SUPPLIER") {
            // Stok Dengan Master Barang Saja
            $data = $this->barangMasterModel->getListBarangmaster(
                $this->request->getVar('type_barang')
            );
        } else {
            // Stok Dengan Master Barang & Spesifikasi
            $data = $this->stockModel->getBarangRebusAndStock(
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

        if (!empty($this->request->getVar('stock_id')) && (!empty($supplierId) || !empty($vendorId))) {

            if (!empty($supplierId)) {
                // Untuk Dari Po & Supplier
                $condition = [
                    'stock_details2.supplier_id' => $this->request->getVar('supplier_id'),
                ];
            } else {
                // Untuk Dari Jasa Vendor
                $condition = [
                    'stock_details.sumber' => "JASA VENDOR",
                ];
            }

            $dataResult = $this->stockDetail2Model->getStockListWithAddConditionNew(
                $this->request->getVar('stock_id'),
                $this->request->getVar('spesifikasi_id'),
                $condition
            );


            $stock = $this->stockModel->find($this->request->getVar('stock_id'));
            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $this->barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $this->satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
            } else {
                $kemasan = $this->kemasanModel->find($stock['kemasan_id']);
                $satuan = $this->satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
            }
            $resultArr = array();

            if (!empty($supplierId)) {
                // Khsus Dari Supplier
                for ($i = 0; $i < count($dataResult); $i++) {
                    $bcType = $this->metaDataModel->find($dataResult[$i]['bc_id']);

                    $rmPurchaseOrder = $this->rmPurchaseOrderModel->where('po_no', $dataResult[$i]['stock_dokumen'])
                        ->where('company_id', $stock['company_id'])
                        ->first();

                    $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                    $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                    $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                    $dataResult[$i]['satuan'] = $satuan['kode_satuan'];
                    $dataResult[$i]['barang'] = strtoupper($barangName);
                    $dataResult[$i]['stock_date'] = $rmPurchaseOrder == null ? "-" : date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['type_barang'] = $stock['tipe_barang'];
                    $dataResult[$i]['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                    $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                    $dataResult[$i]['total_penerimaan'] = floatval($dataResult[$i]['total_penerimaan']);

                    if ($dataResult[$i]['stok_total'] > 0) {
                        array_push($resultArr, $dataResult[$i]);
                    }
                }
            } else {

                // Khsus Dari Vendor
                for ($i = 0; $i < count($dataResult); $i++) {
                    $result = strstr($dataResult[$i]['stock_dokumen'], '(', true);
                    $noJasaVendorIn = trim($result);
                    $supplierName = $dataResult[$i]['supplier_name'];

                    $jasaVendorIn = $this->jasaVendorInModel
                        ->select('jasa_vendor_in.*,vendors.name as nama_vendor')
                        ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                        ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                        ->where('jasa_vendor_in.company_id', $this->this_company_id)
                        ->where('vendor_id', $vendorId)
                        ->first();

                    $bcType = $this->metaDataModel->find($dataResult[$i]['bc_id']);

                    $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                    $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                    $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                    $dataResult[$i]['satuan'] = $satuan['kode_satuan'];
                    $dataResult[$i]['barang'] = strtoupper($barangName);
                    $dataResult[$i]['stock_date'] = $jasaVendorIn == null ? "-" : date('d/m/Y', strtotime($jasaVendorIn['tanggal']));
                    $dataResult[$i]['stock_id'] = $dataResult[$i]['stock_id'];
                    $dataResult[$i]['type_barang'] = $stock['tipe_barang'];
                    $dataResult[$i]['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                    $dataResult[$i]['stok_total'] = floatval($dataResult[$i]['stok_total']);
                    $dataResult[$i]['supplier_name'] = $jasaVendorIn == null ? "-" : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'];

                    if ($jasaVendorIn != null && $dataResult[$i]['stok_total'] > 0) {
                        array_push($resultArr, $dataResult[$i]);
                    }
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
        $stockId = $this->request->getVar('stock_id');

        // if ((!empty($this->request->getVar('stock_id')) || !empty($barangMasterId)) && 
        //     (!empty($supplierId) || !empty($vendorId))) {

            if (!empty($supplierId) && !empty($barangMasterId)) {
                // Untuk Dari Po & Supplier
                $condition = [
                    'stock_details2.supplier_id' => $supplierId,
                    'stock.barang1_id' => $barangMasterId,
                ];
                $dataResult = $this->stockDetail2Model->getStockListJasaVendorOutNew($condition);
                
                $resultArr = [];
                foreach ($dataResult as $item) {
                    if (floatval($item['stok_total']) <= 0) continue;
                    
                    $bcType = $this->metaDataModel->find($item['bc_id']);
                    $stock = $this->stockModel->find($item['stock_id']);
                    
                    $resultArr[] = [
                        'id' => $item['id'],
                        'sumber' => $item['sumber'],
                        'bc_id' => $item['bc_id'],
                        'no_aju' => $item['no_aju'],
                        'supplier_name' => $item['supplier_name'],
                        'bc_type' => $bcType ? $bcType['value'] : 'NON PABEAN',
                        'stock_dokumen' => $item['stock_dokumen'] ?? '-',
                        'stock_date' => date('d/m/Y', strtotime($item['stock_date'])),
                        'barang' => $item['barang_name'],
                        'satuan' => $item['kode_satuan'],
                        'stok_total' => floatval($item['stok_total'])
                    ];
                }
            } else {
                // Untuk Dari Jasa Vendor
                $condition = [
                    'stock_details.sumber' => "JASA VENDOR",
                    // 'stock.barang1_id' => $barangMasterId,
                ];

                $dataResult = $this->stockDetail2Model->getStockListWithAddCondition(
                    $stockId, 
                    $condition
                );

                $resultArr = [];
                foreach ($dataResult as $item) {
                    if (floatval($item['stok_total']) <= 0) continue;
                    
                    $stock = $this->stockModel->find($item['stock_id']);
                    $bcType = $this->metaDataModel->find($item['bc_id']);
                    $noJasaVendorIn = trim(strstr($item['stock_dokumen'], '(', true));
                   
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
                        'sumber' => $item['sumber'],
                        'supplier_name' => $item['supplier_name'] . ' / ' . $jasaVendorIn['nama_vendor'],
                        'bc_type' => $bcType ? $bcType['value'] : 'NON PABEAN',
                        'stock_dokumen' => $item['stock_dokumen'] ?? '-',
                        'stock_date' => date('d/m/Y', strtotime($jasaVendorIn['tanggal'])),
                        'barang' => $item['barang'],
                        'satuan' => $item['kode_satuan'],
                        'stok_total' => floatval($item['stok_total'])
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
