<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\JasaVendorInModel;
use App\Models\JasaVendorOutDetailModel;
use App\Models\JasaVendorOutModel;
use App\Models\MetadataModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use Exception;

class JasaVendorIn extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $vendorModel;
    protected $jasaVendorInModel;
    protected $jasaVendorInDetailModel;
    protected $jasaVendorOutModel;
    protected $jasaVendorOutDetailModel;
    protected $warehouseModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->vendorModel = new VendorModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $this->jasaVendorOutModel = new JasaVendorOutModel();
        $this->jasaVendorOutDetailModel = new JasaVendorOutDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('jasaVendor/in/index', $data);
    }


    public function indexKepiting()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('jasaVendor/in/index_kepiting', $data);
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
            "no_penerimaan_surat_jalan" => $this->request->getVar("no_penerimaan_surat_jalan"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'jasa_vendor_in.company_id' => $this->this_company_id,
            'jasa_vendor_in.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->jasaVendorInModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $jasaVendorInDetail = $this->jasaVendorInDetailModel
                ->where('jasa_vendor_in_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();


            $barangMasterModel = new BarangMasterModel();
            $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
            
            $barangName = null;
            $processedMasterIds = []; // Array untuk melacak master_id yang sudah diproses

            foreach($jasaVendorInDetail as $jvi) {
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($jvi['spesifikasi_in_id']);
                $currentMasterId = $barangMasterSpesifikasi['barang_master_id'];
                
                // Cek jika master_id belum diproses
                if (!in_array($currentMasterId, $processedMasterIds)) {
                    $barangMaster = $barangMasterModel->find($currentMasterId);
                    
                    if ($barangName === null) {
                        $barangName = $barangMaster['barang_name'];
                    } else {
                        $barangName .= ', ' . $barangMaster['barang_name'];
                    }
                    
                    $processedMasterIds[] = $currentMasterId; // Tandai sebagai sudah diproses
                }
            }

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_surat_jalan"        => $data->no_penerimaan_surat_jalan,
                "no_surat_jalan"        => str_replace(['"', ']', '['], "",  $data->multiple_jasa_vendor_out_no),
                "no_surat_jalan_vendor" => $data->no_surat_jalan_vendor == "" ? "-" : $data->no_surat_jalan_vendor,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "barang"                => $barangName ?? 'Tidak ada barang',
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($jasaVendorInDetail),
                "vendor_name"           => $data->vendor_name,
                "status_posting"        => $data->status_posting,
                "status_bayar"          => $data->status_bayar
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


    public function allKepiting()
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
            // "divisi_id" => $this->request->getVar("divisi_id"),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
            "status" => $this->request->getVar("status"),
            "start_date" => $this->request->getVar('start_date'),
            "end_date" => $this->request->getVar('end_date'),
            "no_penerimaan_surat_jalan" => $this->request->getVar("no_penerimaan_surat_jalan"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'jasa_vendor_in.company_id' => $this->this_company_id,
            'jasa_vendor_in.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->jasaVendorInModel->getListKepiting($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $jasaVendorInDetail = $this->jasaVendorInDetailModel
                ->where('jasa_vendor_in_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            $barangMasterModel = new BarangMasterModel();
            $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
            
            $barangName = null;
            $processedMasterIds = []; // Array untuk melacak master_id yang sudah diproses

            foreach($jasaVendorInDetail as $jvi) {
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($jvi['spesifikasi_in_id']);
                $currentMasterId = $barangMasterSpesifikasi['barang_master_id'];
                
                // Cek jika master_id belum diproses
                if (!in_array($currentMasterId, $processedMasterIds)) {
                    $barangMaster = $barangMasterModel->find($currentMasterId);
                    
                    if ($barangName === null) {
                        $barangName = $barangMaster['barang_name'];
                    } else {
                        $barangName .= ', ' . $barangMaster['barang_name'];
                    }
                    
                    $processedMasterIds[] = $currentMasterId; // Tandai sebagai sudah diproses
                }
            }

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_surat_jalan"        => $data->no_penerimaan_surat_jalan,
                "no_surat_jalan"        => str_replace(['"', ']', '['], "",  $data->multiple_jasa_vendor_out_no),
                "no_surat_jalan_vendor" => $data->no_surat_jalan_vendor == "" ? "-" : $data->no_surat_jalan_vendor,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "barang"                => $barangName ?? 'Tidak ada barang',
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($jasaVendorInDetail),
                "vendor_name"           => $data->vendor_name,
                "status_posting"        => $data->status_posting,
                "status_bayar"          => $data->status_bayar
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
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
        ];
        return view('jasaVendor/in/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $jasaVendorIn = $this->jasaVendorInModel->find($id);
        if ($jasaVendorIn == null) {
            return redirect()->to('jasa-vendor-in');
        }

        $data = [
            'jasaVendorIn' => $jasaVendorIn,
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'divisi' => $this->divisiModel->where('id', $jasaVendorIn['divisi_id'])->findAll(),
            'warehouse' => $this->warehouseModel->where('id', $jasaVendorIn['warehouse_id'])->findAll()
        ];

        return view('jasaVendor/in/form', $data);
    }


    public function createKepiting()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
        ];
        return view('jasaVendor/in/form_kepiting', $data);
    }

    public function detailKepiting($id)
    {
        $id = decrypt($id);
        $jasaVendorIn = $this->jasaVendorInModel->find($id);
        if ($jasaVendorIn == null) {
            return redirect()->to('jasa-vendor-in');
        }

        $data = [
            'jasaVendorIn' => $jasaVendorIn,
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'divisi' => $this->divisiModel->where('id', $jasaVendorIn['divisi_id'])->findAll(),
            'warehouse' => $this->warehouseModel->where('id', $jasaVendorIn['warehouse_id'])->findAll()
        ];

        return view('jasaVendor/in/form_kepiting', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $jasaVendorIn = $this->jasaVendorInModel->find($id);
        if ($jasaVendorIn == null) {
            return redirect()->to('jasa-vendor-in');
        }

        $selectQryJasaVendorDetail = "
            SUM(jasa_vendor_in_detail.qty_bersih) as qty_bersih,
            SUM(jasa_vendor_in_detail.qty_kotor) as qty_kotor,
            satuans.kode_satuan,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi
        ";

        $jasaVendorInDetail = $this->jasaVendorInDetailModel->select($selectQryJasaVendorDetail)
            ->join('stock', 'stock.id = jasa_vendor_in_detail.stock_in_id')
            ->join('barang_master', 'barang_master.id = stock.barang1_id')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = stock.barang2_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
            ->where('jasa_vendor_in_id', $id)
            ->groupBy('jasa_vendor_in_detail.stock_in_id')
            ->findAll();

        $data = [
            'jasaVendorIn' => $jasaVendorIn,
            'vendor' => $this->vendorModel->find($jasaVendorIn['vendor_id']),
            'jasaVendorInDetail' => $jasaVendorInDetail
        ];

        $this->dompdf->loadHtml(view('jasaVendor/in/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Jasa Vendor Barang Masuk", array("Attachment" => false));
    }

    public function createAction()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );
        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $check = $this->jasaVendorInModel->where('company_id', $this->this_company_id)->where('no_penerimaan_surat_jalan', $this->request->getVar('no_penerimaan_surat_jalan'))->first();

        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor penerimaan surat jalan sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $id = $this->jasaVendorInModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'vendor_id' => $this->request->getVar('vendor_id'),
            "tanggal" => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
            "no_surat_jalan_vendor" => $this->request->getVar('no_surat_jalan_vendor'),
            'status_closed_jasa_vendor_out' => $this->request->getVar('status_closed_jasa_vendor_out'),
            'no_penerimaan_surat_jalan' => $this->request->getVar('no_penerimaan_surat_jalan'),
            'multiple_jasa_vendor_out_id' =>  str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_jasa_vendor_out_id'))),
            'multiple_jasa_vendor_out_no' =>  str_replace(['\\"', '\\'], '', json_encode($jasaVendorOutNo)),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        $statusClosedJasaVendorOut = $this->request->getVar('status_closed_jasa_vendor_out');
        $jasaVendorOutIdArr = $this->request->getVar('multiple_jasa_vendor_out_id');

        foreach ($jasaVendorOutIdArr as $j) {
            $this->jasaVendorOutModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        foreach ($barangs as $b) {
            // LIST BARANG MASUK
            foreach ($b->list_barang_masuk as $c) {
                if ($c->qty_bersih != 0) {
                    $stockInId = $this->stockModel->initStockBarang(
                        $this->this_company_id,
                        $this->request->getVar('divisi_id'),
                        $this->request->getVar('warehouse_id'),
                        "bahan_baku",
                        $c->spesifikasi_in_id
                    );

                    $this->jasaVendorInDetailModel->insert([
                        'jasa_vendor_in_id' => $id,
                        'jasa_vendor_out_id' => $b->jasa_vendor_out_id,
                        'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id,
                        'spesifikasi_in_id' => $c->spesifikasi_in_id,
                        'stock_in_id' => $stockInId,
                        'bc_in_id' => $b->bc_id,
                        'no_aju_in' => $b->no_aju,
                        'stock_dokumen' => $b->stock_dokumen,
                        'qty_kotor' => $c->qty_kotor,
                        'qty_bersih' => $c->qty_bersih
                    ]);
                }
            }
        }

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($id)
        ]);
    }
    
    public function updateAction()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );
        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $id = decrypt($this->request->getVar('id'));

        $this->jasaVendorInModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'status_closed_jasa_vendor_out' => $this->request->getVar('status_closed_jasa_vendor_out'),
            "no_surat_jalan_vendor" => $this->request->getVar('no_surat_jalan_vendor'),
            'multiple_jasa_vendor_out_id' =>  str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_jasa_vendor_out_id'))),
            'multiple_jasa_vendor_out_no' =>  str_replace(['\\"', '\\'], '', json_encode($jasaVendorOutNo)),
            'keterangan' => $this->request->getVar('keterangan')
        ]);


        $statusClosedJasaVendorOut = $this->request->getVar('status_closed_jasa_vendor_out');
        $jasaVendorOutIdArr = $this->request->getVar('multiple_jasa_vendor_out_id');

        foreach ($jasaVendorOutIdArr as $j) {
            $this->jasaVendorOutModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        // Delete first and insert again
        $this->jasaVendorInDetailModel->where('jasa_vendor_in_id', $id)->delete();

        foreach ($barangs as $b) {
            // LIST BARANG MASUK
            foreach ($b->list_barang_masuk as $c) {
                if ($c->qty_bersih != 0) {
                    // INIT STOCK BARANG
                    $stockInId = $this->stockModel->initStockBarang(
                        $this->this_company_id,
                        $this->request->getVar('divisi_id'),
                        $this->request->getVar('warehouse_id'),
                        "bahan_baku",
                        $c->spesifikasi_in_id
                    );

                    $this->jasaVendorInDetailModel->insert([
                        'jasa_vendor_in_id' => $id,
                        'jasa_vendor_out_id' => $b->jasa_vendor_out_id,
                        'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id,
                        'spesifikasi_in_id' => $c->spesifikasi_in_id,
                        'stock_in_id' => $stockInId,
                        'bc_in_id' => $b->bc_id,
                        'no_aju_in' => $b->no_aju,
                        'stock_dokumen' => $b->stock_dokumen,
                        'qty_kotor' => $c->qty_kotor,
                        'qty_bersih' => $c->qty_bersih
                    ]);
                }
            }
        }

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Diupdate",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function createKepitingAction()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $check = $this->jasaVendorInModel
            ->where('company_id', $this->this_company_id)
            ->where('no_penerimaan_surat_jalan', $this->request->getVar('no_penerimaan_surat_jalan'))
            ->first();

        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor penerimaan surat jalan sudah ada",
                'token' => csrf_hash()
            ]);
        }

        // Insert data utama jasa_vendor_in
        $id = $this->jasaVendorInModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'vendor_id' => $this->request->getVar('vendor_id'),
            'one_raw' => $this->request->getVar('one_raw'),
            "tanggal" => $this->request->getVar("tanggal")
                ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d")
                : "",
            "no_surat_jalan_vendor" => $this->request->getVar('no_surat_jalan_vendor'),
            'status_closed_jasa_vendor_out' => $this->request->getVar('status_closed_jasa_vendor_out'),
            'no_penerimaan_surat_jalan' => $this->request->getVar('no_penerimaan_surat_jalan'),
            'multiple_jasa_vendor_out_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_jasa_vendor_out_id'))),
            'multiple_jasa_vendor_out_no' => str_replace(['\\"', '\\'], '', json_encode($jasaVendorOutNo)),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        // Update status jasa_vendor_out
        $statusClosedJasaVendorOut = $this->request->getVar('status_closed_jasa_vendor_out');
        $jasaVendorOutIdArr = $this->request->getVar('multiple_jasa_vendor_out_id');

        foreach ($jasaVendorOutIdArr as $j) {
            $this->jasaVendorOutModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        // Step 1: Gabungkan semua list_barang_masuk (global grouping)
        $groupedBarang = [];

        foreach ($barangs as $b) {
            foreach ($b->list_barang_masuk as $c) {
               
                    // Group berdasarkan spesifikasi + dokumen
                    $key = $c->spesifikasi_in_id . '_' . $b->stock_dokumen;

                    if (!isset($groupedBarang[$key])) {
                        $groupedBarang[$key] = [
                            'jasa_vendor_out_id' => $b->jasa_vendor_out_id ?? null,
                            'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id ?? null, // ambil dari data pertama yang ketemu
                            'spesifikasi_in_id' => $c->spesifikasi_in_id,
                            'bc_in_id' => $b->bc_id ?? null,
                            'no_aju_in' => $b->no_aju ?? null,
                            'stock_dokumen' => $b->stock_dokumen,
                            'qty_kotor' => 0,
                            'qty_bersih' => 0
                        ];
                    }

                    $groupedBarang[$key]['qty_kotor'] += $c->qty_kotor;
                    $groupedBarang[$key]['qty_bersih'] += $c->qty_bersih;

            }
        }

        // Step 2: Insert hasil grouping
        foreach ($groupedBarang as $gb) {

            $this->jasaVendorInDetailModel->insert([
                'jasa_vendor_in_id' => $id,
                'jasa_vendor_out_id' => $gb['jasa_vendor_out_id'],
                'jasa_vendor_out_detail_id' => $gb['jasa_vendor_out_detail_id'],
                'spesifikasi_in_id' => $gb['spesifikasi_in_id'],
                'bc_in_id' => $gb['bc_in_id'],
                'no_aju_in' => $gb['no_aju_in'],
                'stock_dokumen' => $gb['stock_dokumen'],
                'qty_kotor' => $gb['qty_kotor'],
                'qty_bersih' => $gb['qty_bersih']
            ]);
        }

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($id)
        ]);
    }
    
    public function updateKepitingAction()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $id = decrypt($this->request->getVar('id'));

        // Update data utama jasa_vendor_in
        $this->jasaVendorInModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'vendor_id' => $this->request->getVar('vendor_id'),
            "tanggal" => $this->request->getVar("tanggal")
                ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d")
                : "",
            "no_surat_jalan_vendor" => $this->request->getVar('no_surat_jalan_vendor'),
            'status_closed_jasa_vendor_out' => $this->request->getVar('status_closed_jasa_vendor_out'),
            'multiple_jasa_vendor_out_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_jasa_vendor_out_id'))),
            'multiple_jasa_vendor_out_no' => str_replace(['\\"', '\\'], '', json_encode($jasaVendorOutNo)),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        // Update status jasa_vendor_out
        $statusClosedJasaVendorOut = $this->request->getVar('status_closed_jasa_vendor_out');
        $jasaVendorOutIdArr = $this->request->getVar('multiple_jasa_vendor_out_id');

        foreach ($jasaVendorOutIdArr as $j) {
            $this->jasaVendorOutModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        // Delete detail lama
        $this->jasaVendorInDetailModel->where('jasa_vendor_in_id', $id)->delete();

        // Step 1: Gabungkan semua list_barang_masuk (global grouping)
        $groupedBarang = [];

        foreach ($barangs as $b) {
            foreach ($b->list_barang_masuk as $c) {
               
                    // Group berdasarkan spesifikasi + dokumen
                    $key = $c->spesifikasi_in_id . '_' . $b->stock_dokumen;
                    
                    if (!isset($groupedBarang[$key])) {
                        $groupedBarang[$key] = [
                            'jasa_vendor_out_id' => $b->jasa_vendor_out_id ?? null,
                            'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id ?? null, // ambil dari data pertama yang ketemu
                            'spesifikasi_in_id' => $c->spesifikasi_in_id,
                            'bc_in_id' => $b->bc_id ?? null,
                            'no_aju_in' => $b->no_aju ?? null,
                            'stock_dokumen' => $b->stock_dokumen,
                            'qty_kotor' => 0,
                            'qty_bersih' => 0
                        ];
                    }

                    $groupedBarang[$key]['qty_kotor'] += $c->qty_kotor;
                    $groupedBarang[$key]['qty_bersih'] += $c->qty_bersih;
                
            }
        }

        // Step 2: Insert hasil grouping
        foreach ($groupedBarang as $gb) {
            $this->jasaVendorInDetailModel->insert([
                'jasa_vendor_in_id' => $id,
                'jasa_vendor_out_id' => $gb['jasa_vendor_out_id'],
                'jasa_vendor_out_detail_id' => $gb['jasa_vendor_out_detail_id'],
                'spesifikasi_in_id' => $gb['spesifikasi_in_id'],
                'bc_in_id' => $gb['bc_in_id'],
                'no_aju_in' => $gb['no_aju_in'],
                'stock_dokumen' => $gb['stock_dokumen'],
                'qty_kotor' => $gb['qty_kotor'],
                'qty_bersih' => $gb['qty_bersih']
            ]);
        }

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Diupdate",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function createActionNew()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $check = $this->jasaVendorInModel
            ->where('company_id', $this->this_company_id)
            ->where('no_penerimaan_surat_jalan', $this->request->getVar('no_penerimaan_surat_jalan'))
            ->first();

        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor penerimaan surat jalan sudah ada",
                'token' => csrf_hash()
            ]);
        }

        // Insert data utama jasa_vendor_in
        $id = $this->jasaVendorInModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'vendor_id' => $this->request->getVar('vendor_id'),
            "tanggal" => $this->request->getVar("tanggal")
                ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d")
                : "",
            "no_surat_jalan_vendor" => $this->request->getVar('no_surat_jalan_vendor'),
            'status_closed_jasa_vendor_out' => $this->request->getVar('status_closed_jasa_vendor_out'),
            'no_penerimaan_surat_jalan' => $this->request->getVar('no_penerimaan_surat_jalan'),
            'multiple_jasa_vendor_out_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_jasa_vendor_out_id'))),
            'multiple_jasa_vendor_out_no' => str_replace(['\\"', '\\'], '', json_encode($jasaVendorOutNo)),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        // Update status jasa_vendor_out
        $statusClosedJasaVendorOut = $this->request->getVar('status_closed_jasa_vendor_out');
        $jasaVendorOutIdArr = $this->request->getVar('multiple_jasa_vendor_out_id');

        foreach ($jasaVendorOutIdArr as $j) {
            $this->jasaVendorOutModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        // Step 1: Gabungkan semua list_barang_masuk (global grouping)
        $groupedBarang = [];

        foreach ($barangs as $b) {
            foreach ($b->list_barang_masuk as $c) {
               
                    // Group berdasarkan spesifikasi + dokumen
                    $key = $c->spesifikasi_in_id . '_' . $b->stock_dokumen;

                    if (!isset($groupedBarang[$key])) {
                        $groupedBarang[$key] = [
                            'jasa_vendor_out_id' => $b->jasa_vendor_out_id,
                            'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id, // ambil dari data pertama yang ketemu
                            'spesifikasi_in_id' => $c->spesifikasi_in_id,
                            'bc_in_id' => $b->bc_id,
                            'no_aju_in' => $b->no_aju,
                            'stock_dokumen' => $b->stock_dokumen,
                            'qty_kotor' => 0,
                            'qty_bersih' => 0
                        ];
                    }

                    $groupedBarang[$key]['qty_kotor'] += $c->qty_kotor;
                    $groupedBarang[$key]['qty_bersih'] += $c->qty_bersih;

            }
        }

        // Step 2: Insert hasil grouping
        foreach ($groupedBarang as $gb) {

            $this->jasaVendorInDetailModel->insert([
                'jasa_vendor_in_id' => $id,
                'jasa_vendor_out_id' => $gb['jasa_vendor_out_id'],
                'jasa_vendor_out_detail_id' => $gb['jasa_vendor_out_detail_id'],
                'spesifikasi_in_id' => $gb['spesifikasi_in_id'],
                'bc_in_id' => $gb['bc_in_id'],
                'no_aju_in' => $gb['no_aju_in'],
                'stock_dokumen' => $gb['stock_dokumen'],
                'qty_kotor' => $gb['qty_kotor'],
                'qty_bersih' => $gb['qty_bersih']
            ]);
        }

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Disimpan",
            'token' => csrf_hash(),
            'status' => true,
            'id' => encrypt($id)
        ]);
    }


    public function updateActionNew()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $id = decrypt($this->request->getVar('id'));

        // Update data utama jasa_vendor_in
        $this->jasaVendorInModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'vendor_id' => $this->request->getVar('vendor_id'),
            "tanggal" => $this->request->getVar("tanggal")
                ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d")
                : "",
            "no_surat_jalan_vendor" => $this->request->getVar('no_surat_jalan_vendor'),
            'status_closed_jasa_vendor_out' => $this->request->getVar('status_closed_jasa_vendor_out'),
            'multiple_jasa_vendor_out_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_jasa_vendor_out_id'))),
            'multiple_jasa_vendor_out_no' => str_replace(['\\"', '\\'], '', json_encode($jasaVendorOutNo)),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        // Update status jasa_vendor_out
        $statusClosedJasaVendorOut = $this->request->getVar('status_closed_jasa_vendor_out');
        $jasaVendorOutIdArr = $this->request->getVar('multiple_jasa_vendor_out_id');

        foreach ($jasaVendorOutIdArr as $j) {
            $this->jasaVendorOutModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        // Delete detail lama
        $this->jasaVendorInDetailModel->where('jasa_vendor_in_id', $id)->delete();

        // Step 1: Gabungkan semua list_barang_masuk (global grouping)
        $groupedBarang = [];

        foreach ($barangs as $b) {
            foreach ($b->list_barang_masuk as $c) {
               
                    // Group berdasarkan spesifikasi + dokumen
                    $key = $c->spesifikasi_in_id . '_' . $b->stock_dokumen;

                    if (!isset($groupedBarang[$key])) {
                        $groupedBarang[$key] = [
                            'jasa_vendor_out_id' => $b->jasa_vendor_out_id,
                            'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id,
                            'spesifikasi_in_id' => $c->spesifikasi_in_id,
                            'bc_in_id' => $b->bc_id,
                            'no_aju_in' => $b->no_aju,
                            'stock_dokumen' => $b->stock_dokumen,
                            'qty_kotor' => 0,
                            'qty_bersih' => 0
                        ];
                    }

                    $groupedBarang[$key]['qty_kotor'] += $c->qty_kotor;
                    $groupedBarang[$key]['qty_bersih'] += $c->qty_bersih;
                
            }
        }

        // Step 2: Insert hasil grouping
        foreach ($groupedBarang as $gb) {
            $this->jasaVendorInDetailModel->insert([
                'jasa_vendor_in_id' => $id,
                'jasa_vendor_out_id' => $gb['jasa_vendor_out_id'],
                'jasa_vendor_out_detail_id' => $gb['jasa_vendor_out_detail_id'],
                'spesifikasi_in_id' => $gb['spesifikasi_in_id'],
                'bc_in_id' => $gb['bc_in_id'],
                'no_aju_in' => $gb['no_aju_in'],
                'stock_dokumen' => $gb['stock_dokumen'],
                'qty_kotor' => $gb['qty_kotor'],
                'qty_bersih' => $gb['qty_bersih']
            ]);
        }

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Diupdate",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $jasaVendorIn = $this->jasaVendorInModel->find($id);
        $jasaVendorOutIdArr = \json_decode($jasaVendorIn['multiple_jasa_vendor_out_id']);

        if ($jasaVendorIn['status_closed_jasa_vendor_out'] == "1") {
            foreach ($jasaVendorOutIdArr as $j) {
                $this->jasaVendorOutModel->update($j, [
                    'status_closed' => "0"
                ]);
            }
        }

        $this->jasaVendorInModel->delete($id);
        $this->jasaVendorInDetailModel->where('jasa_vendor_in_id', $id)->delete();

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function posting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        $stockRevampModel = new StockRevampModel();
        $stockRevampDetailModel = new StockRevampDetailModel();

        try {
            $id = decrypt($this->request->getVar('id'));

            // BARANG IN KE INVENTORI DARI VENDOR
            $jasaVendorIn = $this->jasaVendorInModel->find($id);
            $jasaVendorInDetail = $this->jasaVendorInDetailModel->where('deletedAt', null)->where('jasa_vendor_in_id', $id)->findAll();

            $ids = $jasaVendorIn['multiple_jasa_vendor_out_id'];

            // cek dulu kalau ternyata string JSON
            if (is_string($ids)) {
                $ids = json_decode($ids, true); 
            }

            // pastikan hasil decode array
            if (!is_array($ids)) {
                $ids = [$ids]; // fallback: bikin array tunggal
            }

            $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();

            foreach ($jasaVendorInDetail as $p) {
                // ambil data spesifikasi & type bc
                $spesifikasiData = $barangMasterSpesifikasiModel
                    ->where('id', $p['spesifikasi_in_id'])
                    ->where('deletedAt', NULL)
                    ->first();

                $typeBc = $this->metaDataModel
                    ->where('deletedAt', NULL)
                    ->where('id', $p['bc_in_id'])
                    ->first();

                // hitung total masuk (qty diterima di vendor in)
                $totalMasuk = $p['qty_bersih']; // atau qty_bersih tergantung definisi


                // buat record stock in
                $data = [
                    "company_id"       => $this->this_company_id,
                    "spesifikasi_id"   => $spesifikasiData["id"],
                    "barang_master_id" => $spesifikasiData["barang_master_id"],
                    "unit_id"          => $spesifikasiData["satuan_1"],
                    "divisi_id"        => $jasaVendorIn["divisi_id"],
                    "warehouse_id"     => $jasaVendorIn["warehouse_id"],
                    "no_dokumen"       => $jasaVendorIn["no_penerimaan_surat_jalan"],
                    "bc_id"            => $p['bc_in_id'],
                    "type_bc"          => $typeBc == null ? "NON PABEAN" : $typeBc['value'],
                    "qty_diterima"     => $totalMasuk,
                    "qty_bersih"       => $p["qty_bersih"],
                    "reference_id"     => $id,
                    "po_type"          => "LOKAL BAKU",
                    "reference_type"   => "JASA VENDOR",
                    "status"           => "IN"
                ];

                $stockDetailId = $stockRevampModel->insertStockRevampJasaVendorIn($db, $data);

                // 🔹 Update kolom stock_detail_in_id di jasa_vendor_in_detail
                $this->jasaVendorInDetailModel
                    ->where('id', $p['id'])
                    ->where('deletedAt', null)
                    ->set('stock_detail_in_id', $stockDetailId)
                    ->update();

                // ambil semua jasa vendor out terkait
                $jasaVendorOut = $this->jasaVendorOutModel
                    ->where('deletedAt', null)
                    ->whereIn('id', $ids)
                    ->findAll();

                foreach ($jasaVendorOut as $j) {
                    $details = $this->jasaVendorOutDetailModel
                        ->select('id, stock_out_detail_id, qty')
                        ->where('jasa_vendor_out_id', $j['id'])
                        ->where('deletedAt', null)
                        ->findAll();

                     

                    foreach ($details as $d) {
                        // 🔹 Hitung qty_masuk_i sesuai rumus:
                        // qty_masuk_i = qty_keluar_i * (total_masuk / total_keluar)
                        $qtyMasukI = 0;
                        if ($d['qty'] > 0) {
                            $qtyMasukI = $d['qty'] * ($totalMasuk / $d['qty']);
                        }

                        $db->table('stock_revamp_history')->insert([
                            'stock_detail_asal'    => $d['stock_out_detail_id'],
                            'stock_detail_akhir'   => $stockDetailId,
                            'qty_bersih_asal'      => $d['qty'],
                            'qty_diterima_asal'    => $d['qty'],
                            'qty_bersih_akhir'     => $qtyMasukI, // hasil rumus
                            'qty_diterima_akhir'   => $qtyMasukI, // bisa disamakan kalau proporsional
                            'createdAt'            => date('Y-m-d H:i:s'),
                            'updatedAt'            => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }



            // update status Jasa Vendor
            $this->jasaVendorInModel->update($id, [
                'status_posting' => '1'
            ]);

            // commit transaksi
            $db->transCommit();

            return $this->response->setJSON([
                'message' => "Jasa Vendor berhasil diposting",
                'status'  => true,
                'token'   => csrf_hash()
            ]);
        } catch (\Throwable $th) {
            $db->transRollback();

            return $this->response->setJSON([
                'message' => "Gagal posting Jasa Vendor: " . $th->getMessage(),
                'status'  => false,
                'token'   => csrf_hash()
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
            
            $jasaVendorIn = $this->jasaVendorInModel->find($id);
            $jasaVendorInDetail = $this->jasaVendorInDetailModel->where('deletedAt', null)->where('jasa_vendor_in_id', $id)->findAll();


            if (empty($jasaVendorInDetail)) {
                throw new \Exception("Detail Jasa Vendor In tidak ditemukan");
            }

            foreach ($jasaVendorInDetail as $j) {
                $data = [
                    "stock_detail_akhir"     => $j["stock_detail_in_id"],    
                    "qty_diterima_akhir"     => $j["qty_bersih"],
                    "no_dokumen"            => $jasaVendorIn["no_penerimaan_surat_jalan"],
                    "keterangan"            => "UNPOST JASA VENDOR MASUK"
                ];

                // Panggil model - jika gagal akan throw exception
                $stockRevampModel->unpostStockMasuk($db, $data);
            }

            // update status Jasa Vendor In
            $this->jasaVendorInModel->update($id, [
                'status_posting' => '0'
            ]);

            // commit transaksi
            $db->transCommit();

            return $this->response->setJSON([
                'message' => "Jasa Vendor In berhasil di-unpost",
                'status'  => true,
                'token'   => csrf_hash()
            ]);

        } catch (\Exception $e) {
            $db->transRollback();

            return $this->response->setJSON([
                'message' => "Gagal unpost Jasa Vendor In: " . $e->getMessage(),
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

    public function postingBayar()
    {
        $id = decrypt($this->request->getVar('id'));

        // Ambil data parent
        $jasaVendorIn = $this->jasaVendorInModel->find($id);
        if (!$jasaVendorIn) {
            return response()->setJSON([
                'message' => "Data Jasa Vendor Barang Masuk tidak ditemukan",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Ambil data detail
        $jasaVendorInDetail = $this->jasaVendorInDetailModel
            ->where('jasa_vendor_in_id', $id)
            ->findAll();

        if (empty($jasaVendorInDetail)) {
            return response()->setJSON([
                'message' => "Detail Jasa Vendor Barang Masuk tidak ditemukan",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Validasi qty_kotor
        foreach ($jasaVendorInDetail as $j) {
            if (empty($j['qty_kotor']) || floatval($j['qty_kotor']) <= 0) {
                return response()->setJSON([
                    'message' => "Gagal posting! Ada detail dengan qty kotor kosong / 0",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }
        }

        // Kalau lolos validasi → update status
        $this->jasaVendorInModel->update($id, ['status_bayar' => '1']);

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk berhasil Di Proses Pembayaran",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }



    public function dropdownListBarangKeluar()
    {
        $id = decrypt($this->request->getVar('id'));
        $jasaVendorOutID = json_decode($this->request->getVar('multiple_jasa_vendor_out_id'));
        if (count($jasaVendorOutID) == 0) {
            return response()->setJSON([
                'data' => [
                    'dataDetail' => [],
                    'dataGroup' => []
                ],
                'status' => true,
                'token' => csrf_hash()
            ]);
        } else {
            $id = ($id == false) ? null : $id;
            $data = $this->jasaVendorInModel->listBarang(
                $jasaVendorOutID,
                $id
            );
            return response()->setJSON([
                'data' => $data,
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    public function dropdownListBarangKeluarKepiting()
    {
        $id = decrypt($this->request->getVar('id'));
        $jasaVendorOutID = json_decode($this->request->getVar('multiple_jasa_vendor_out_id'));
        if (count($jasaVendorOutID) == 0) {
            return response()->setJSON([
                'data' => [
                    'dataDetail' => [],
                    'dataGroup' => []
                ],
                'status' => true,
                'token' => csrf_hash()
            ]);
        } else {
            $id = ($id == false) ? null : $id;
            $data = $this->jasaVendorInModel->listBarangKepiting(
                $jasaVendorOutID,
                $id
            );
            return response()->setJSON([
                'data' => $data,
                'status' => true,
                'token' => csrf_hash()
            ]);
        }
    }

    public function dropdownListBarangMasuk()
    {
        $typeBarang = $this->request->getVar('type_barang');
        if (!empty($typeBarang)) {
            $data =  $this->stockModel->getListMasterBarang(
                $this->this_company_id,
                $typeBarang
            );
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'data' => $data
            ]);
        } else {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'data' => []
            ]);
        }
    }

    public function dropdownDivisi()
    {
        $vendorID = $this->request->getVar('vendor_id');
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $divisiResult = [];

        foreach ($dataDivisi as $d) {
            array_push($divisiResult, $d['id']);
        }

        $result = $this->jasaVendorOutModel
            ->select('DISTINCT(divisis.id), divisis.divisi')
            ->join('divisis', 'divisis.id = jasa_vendor_out.divisi_id', 'left')
            ->whereIn('jasa_vendor_out.divisi_id', $divisiResult)
            ->where('vendor_id', $vendorID)
            ->where('status_posting', '1')
            ->where('status_closed', '0')
            ->where('jasa_vendor_out.deletedAt', null)
            ->where('divisis.deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownWarehouse()
    {
        $vendorID = $this->request->getVar('vendor_id');
        $divisiID = $this->request->getVar('divisi_id');

        $result = $this->jasaVendorOutModel
            ->select('DISTINCT(warehouses.id), warehouses.warehouse_name')
            ->join('warehouses', 'warehouses.id = jasa_vendor_out.warehouse_id', 'left')
            ->where('jasa_vendor_out.divisi_id', $divisiID)
            ->where('vendor_id', $vendorID)
            ->where('status_posting', '1')
            ->where('status_closed', '0')
            ->where('jasa_vendor_out.deletedAt', null)
            ->where('warehouses.deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownNoJasaVendorOut()
    {
        $vendorID = $this->request->getVar('vendor_id');
        $divisiID = $this->request->getVar('divisi_id');
        $warehouseID = $this->request->getVar('warehouse_id');

        $result = $this->jasaVendorOutModel
            ->where('divisi_id', $divisiID)
            ->where('warehouse_id', $warehouseID)
            ->where('vendor_id', $vendorID)
            ->where('status_posting', '1')
            ->where('status_closed', '0')
            ->where('deletedAt', null)
            ->findAll();

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getJasaVendorInNo()
    {
        $tanggal = date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))));
        $warehouse_id = $this->request->getVar('warehouse_id');

        if (empty($tanggal) || empty($warehouse_id)) {
            $no = $this->jasaVendorInModel->get_no(
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
            $no = $this->jasaVendorInModel->get_no(
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
