<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInKepitingKukusDetailModel;
use App\Models\JasaVendorInKepitingKukusModel;
use App\Models\JasaVendorInModel;
use App\Models\JasaVendorOutKepitingKukusDetailModel;
use App\Models\JasaVendorOutKepitingKukusModel;
use App\Models\MetadataModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use Exception;

class JasaVendorInKepitingKukus extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $metaDataModel;
    protected $divisiModel;
    protected $vendorModel;
    protected $jasaVendorInKepitingKukusModel;
    protected $jasaVendorInKepitingKukusDetailModel;
    protected $jasaVendorOutKepitingKukusModel;
    protected $jasaVendorOutKepitingKukusDetailModel;
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
        $this->jasaVendorInKepitingKukusModel = new JasaVendorInKepitingKukusModel();
        $this->jasaVendorInKepitingKukusDetailModel = new JasaVendorInKepitingKukusDetailModel();
        $this->jasaVendorOutKepitingKukusModel = new JasaVendorOutKepitingKukusModel();
        $this->jasaVendorOutKepitingKukusDetailModel = new JasaVendorOutKepitingKukusDetailModel();
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

        return view('jasaVendor/inKepitingKukus/index', $data);
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
            'jasa_vendor_in_kepiting_kukus.company_id' => $this->this_company_id,
            'jasa_vendor_in_kepiting_kukus.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->jasaVendorInKepitingKukusModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        // var_dump($dataQry);
        // die;
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $jasaVendorInKepitingKukusDetail = $this->jasaVendorInKepitingKukusDetailModel
                ->where('jasa_vendor_in_kepiting_kukus_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_penerimaan_surat_jalan"        => $data->no_penerimaan_surat_jalan,
                "no_surat_jalan"        => str_replace(['"', ']', '['], "",  $data->multiple_jasa_vendor_out_no),
                "no_surat_jalan_vendor" => $data->no_surat_jalan_vendor == "" ? "-" : $data->no_surat_jalan_vendor,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                // "barang"                => $barangName ?? 'Tidak ada barang',
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($jasaVendorInKepitingKukusDetail),
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
        return view('jasaVendor/inKepitingKukus/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $jasaVendorIn = $this->jasaVendorInKepitingKukusModel->find($id);
        if ($jasaVendorIn == null) {
            return redirect()->to('jasa-vendor-in-kepiting-kukus');
        }

        $data = [
            'jasaVendorIn' => $jasaVendorIn,
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
            'divisi' => $this->divisiModel->where('id', $jasaVendorIn['divisi_id'])->findAll(),
            'warehouse' => $this->warehouseModel->where('id', $jasaVendorIn['warehouse_id'])->findAll()
        ];

        return view('jasaVendor/inKepitingKukus/form', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $jasaVendorIn = $this->jasaVendorInKepitingKukusModel->find($id);
        if ($jasaVendorIn == null) {
            return redirect()->to('jasa-vendor-in-kepiting-kukus');
        }

        // ubah string JSON ke array
        $ids = json_decode($jasaVendorIn['multiple_jasa_vendor_out_id'], true);

        // fallback kalau NULL atau bukan array
        if (!is_array($ids)) {
            $ids = [];
        }

        $jasaVendorInKepitingKukusDetail = $this->jasaVendorInKepitingKukusModel->listBarang(
            $ids,
            $id
        );




        $data = [
            'jasaVendorIn' => $jasaVendorIn,
            'vendor' => $this->vendorModel->find($jasaVendorIn['vendor_id']),
            'dataDetail' => $jasaVendorInKepitingKukusDetail
        ];

        $this->dompdf->loadHtml(view('jasaVendor/inKepitingKukus/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Jasa Vendor Barang Masuk", array("Attachment" => false));
    }

    public function printFilter()
    {
        // Ambil parameter filter
        $divisiId = $this->request->getGet('divisi_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        $status = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $noPenerimaan = $this->request->getGet('no_penerimaan_surat_jalan');
        $sort = $this->request->getGet('sort') ?? 'createdAt';
        $sortType = $this->request->getGet('sortType') ?? 'desc';

        // Konfigurasi filter
        $addCondition = [
            "sort" => $sort,
            "sortType" => $sortType,
            "divisi_id" => $divisiId,
            "warehouse_id" => $warehouseId,
            "status" => $status,
            "start_date" => $startDate,
            "end_date" => $endDate,
            "no_penerimaan_surat_jalan" => $noPenerimaan,
        ];

        $divisiArr = [];
        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        // Ambil data berdasarkan filter
        $dataQry = $this->jasaVendorInKepitingKukusModel->getList([
            'jasa_vendor_in_kepiting_kukus.company_id' => $this->this_company_id,
            'jasa_vendor_in_kepiting_kukus.deletedAt' => null,
        ], $divisiArr, $addCondition, 0, 0); // 0,0 untuk ambil semua data

        // Format data untuk print
        $dataResult = [];
        $no = 1;

        foreach ($dataQry['data'] as $data) {
            $jasaVendorInKepitingKukusDetail = $this->jasaVendorInKepitingKukusDetailModel
                ->where('jasa_vendor_in_kepiting_kukus_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            $dataResult[] = [
                "no" => $no++,
                "no_penerimaan_surat_jalan" => $data->no_penerimaan_surat_jalan,
                "tanggal" => date('d/m/Y', strtotime($data->tanggal)),
                "divisi" => $data->divisi,
                "warehouse_name" => $data->warehouse_name,
                "no_surat_jalan" => str_replace(['"', ']', '['], "", $data->multiple_jasa_vendor_out_no),
                "vendor_name" => $data->vendor_name,
                "status_posting" => $data->status_posting == 1 ? 'POSTED' : 'WAITING',
                "total_item" => count($jasaVendorInKepitingKukusDetail),
                "no_surat_jalan_vendor" => $data->no_surat_jalan_vendor ?: '-',
            ];
        }

        // Data untuk view print
        $printData = [
            'title' => 'Laporan Jasa Vendor Barang Masuk',
            'data' => $dataResult,
            'filter' => [
                'divisi' => $divisiId ? $this->divisiModel->find($divisiId)->divisi ?? '-' : 'SEMUA',
                'warehouse' => $warehouseId ? $this->warehouseModel->find($warehouseId)->warehouse_name ?? '-' : 'SEMUA',
                'status' => $status === '1' ? 'POSTED' : ($status === '0' ? 'WAITING' : 'SEMUA'),
                'periode' => $startDate && $endDate ? "$startDate s/d $endDate" : 'SEMUA',
                'no_penerimaan' => $noPenerimaan ?: '-',
                'total_record' => count($dataResult),
                'printed_at' => date('d/m/Y H:i:s'),
            ]
        ];

        // Load view print
        return view('jasaVendor/inKepitingKukus/print_filter', $printData);
    }

    public function createAction()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInKepitingKukusModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $check = $this->jasaVendorInKepitingKukusModel->where('company_id', $this->this_company_id)->where('no_penerimaan_surat_jalan', $this->request->getVar('no_penerimaan_surat_jalan'))->first();

        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor penerimaan surat jalan sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $id = $this->jasaVendorInKepitingKukusModel->insert([
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
            $this->jasaVendorOutKepitingKukusModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        foreach ($barangs as $b) {
            // LIST BARANG MASUK
            foreach ($b->list_barang_masuk as $c) {
                if ($c->qty_kotor != 0) {
                    $this->jasaVendorInKepitingKukusDetailModel->insert([
                        'jasa_vendor_in_kepiting_kukus_id' => $id,
                        'jasa_vendor_out_kepiting_kukus_id' => $b->jasa_vendor_out_kepiting_kukus_id,
                        'jasa_vendor_out_kepiting_kukus_detail_id' => $b->jasa_vendor_out_kepiting_kukus_detail_id,
                        'spesifikasi_in_id' => $c->spesifikasi_in_id,
                        'qty_kotor' => $c->qty_kotor
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
        $jasaVendorOutNo = $this->jasaVendorInKepitingKukusModel->getJasaVendorOutNo(
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

        $this->jasaVendorInKepitingKukusModel->update($id, [
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
            $this->jasaVendorOutKepitingKukusModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        // Delete first and insert again
        $this->jasaVendorInKepitingKukusDetailModel->where('jasa_vendor_in_kepiting_kukus_id', $id)->delete();

        foreach ($barangs as $b) {
            // LIST BARANG MASUK
            foreach ($b->list_barang_masuk as $c) {
                if ($c->qty_kotor != 0) {
                    $this->jasaVendorInKepitingKukusDetailModel->insert([
                        'jasa_vendor_in_kepiting_kukus_id' => $id,
                        'jasa_vendor_out_kepiting_kukus_id' => $b->jasa_vendor_out_kepiting_kukus_id,
                        'jasa_vendor_out_kepiting_kukus_detail_id' => $b->jasa_vendor_out_kepiting_kukus_detail_id,
                        'spesifikasi_in_id' => $c->spesifikasi_in_id,
                        'qty_kotor' => $c->qty_kotor
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



    public function createActionNew()
    {
        $barangs = json_decode($_POST['listBarang']);
        $jasaVendorOutNo = $this->jasaVendorInKepitingKukusModel->getJasaVendorOutNo(
            $this->request->getVar('multiple_jasa_vendor_out_id')
        );

        if (count($barangs) == 0) {
            return response()->setJSON([
                'status' => false,
                'message' => "Barang tidak boleh kosong",
                'token' => csrf_hash()
            ]);
        }

        $check = $this->jasaVendorInKepitingKukusModel
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
        $id = $this->jasaVendorInKepitingKukusModel->insert([
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
            $this->jasaVendorOutKepitingKukusModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        // Step 1: Gabungkan semua list_barang_masuk (global grouping)
        $groupedBarang = [];

        foreach ($barangs as $b) {
            foreach ($b->list_barang_masuk as $c) {
               
                    // Group berdasarkan spesifikasi + dokumen
                    $key = $c->supplier_id . '_' . $b->keterangan;

                    if (!isset($groupedBarang[$key])) {
                        $groupedBarang[$key] = [
                            'jasa_vendor_out_id' => $b->jasa_vendor_out_id,
                            'jasa_vendor_out_detail_id' => $b->jasa_vendor_out_detail_id, // ambil dari data pertama yang ketemu
                            'spesifikasi_in_id' => $c->spesifikasi_in_id,
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

            $this->jasaVendorInKepitingKukusDetailModel->insert([
                'jasa_vendor_in_id' => $id,
                'jasa_vendor_out_id' => $gb['jasa_vendor_out_id'],
                'jasa_vendor_out_detail_id' => $gb['jasa_vendor_out_detail_id'],
                'spesifikasi_in_id' => $gb['spesifikasi_in_id'], 
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
        $jasaVendorOutNo = $this->jasaVendorInKepitingKukusModel->getJasaVendorOutNo(
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
        $this->jasaVendorInKepitingKukusModel->update($id, [
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
            $this->jasaVendorOutKepitingKukusModel->update($j, [
                'status_closed' => $statusClosedJasaVendorOut
            ]);
        }

        // Delete detail lama
        $this->jasaVendorInKepitingKukusDetailModel->where('jasa_vendor_in_id', $id)->delete();

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
            $stockInId = $this->stockModel->initStockBarang(
                $this->this_company_id,
                $this->request->getVar('divisi_id'),
                $this->request->getVar('warehouse_id'),
                "bahan_baku",
                $gb['spesifikasi_in_id']
            );

            $this->jasaVendorInKepitingKukusDetailModel->insert([
                'jasa_vendor_in_id' => $id,
                'jasa_vendor_out_id' => $gb['jasa_vendor_out_id'],
                'jasa_vendor_out_detail_id' => $gb['jasa_vendor_out_detail_id'],
                'spesifikasi_in_id' => $gb['spesifikasi_in_id'],
                'stock_in_id' => $stockInId,
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
        $jasaVendorIn = $this->jasaVendorInKepitingKukusModel->find($id);
        $jasaVendorOutIdArr = \json_decode($jasaVendorIn['multiple_jasa_vendor_out_id']);

        if ($jasaVendorIn['status_closed_jasa_vendor_out'] == "1") {
            foreach ($jasaVendorOutIdArr as $j) {
                $this->jasaVendorOutKepitingKukusModel->update($j, [
                    'status_closed' => "0"
                ]);
            }
        }

        $this->jasaVendorInKepitingKukusModel->delete($id);
        $this->jasaVendorInKepitingKukusDetailModel->where('jasa_vendor_in_kepiting_kukus_id', $id)->delete();

        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        // BARANG IN KE INVENTORI DARI VENDOR
        // // INSERT INVENTORI (+)
        // $jasaVendorIn = $this->jasaVendorInKepitingKukusModel->find($id);
        // $jasaVendorInKepitingKukusDetail = $this->jasaVendorInKepitingKukusDetailModel->where('jasa_vendor_in_id', $id)->findAll();
        $this->jasaVendorInKepitingKukusModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'message' => "Jasa Vendor Barang Masuk berhasil diposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function unPosting()
    {
        try {
            $id = decrypt($this->request->getVar('id'));

            // $this->stockModel->unPostingStockJasaVendorIn($id);

            $this->jasaVendorInKepitingKukusModel->update($id, [
                'status_posting' => '0'
            ]);

            return response()->setJSON([
                'status' => true,
                'message' => "Barang Masuk Berhasil Di Unposting",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => "Gagal Posting : Terjadi kesalahan saat unposting barang masuk",
                'error' => $e->getTrace(),
                'token' => csrf_hash()
            ]);
        }
    }

    public function postingBayar()
    {
        $id = decrypt($this->request->getVar('id'));

        // Ambil data parent
        $jasaVendorIn = $this->jasaVendorInKepitingKukusModel->find($id);
        if (!$jasaVendorIn) {
            return response()->setJSON([
                'message' => "Data Jasa Vendor Barang Masuk tidak ditemukan",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Ambil data detail
        $jasaVendorInKepitingKukusDetail = $this->jasaVendorInKepitingKukusDetailModel
            ->where('jasa_vendor_in_id', $id)
            ->findAll();

        if (empty($jasaVendorInKepitingKukusDetail)) {
            return response()->setJSON([
                'message' => "Detail Jasa Vendor Barang Masuk tidak ditemukan",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        // Validasi qty_kotor
        foreach ($jasaVendorInKepitingKukusDetail as $j) {
            if (empty($j['qty_kotor']) || floatval($j['qty_kotor']) <= 0) {
                return response()->setJSON([
                    'message' => "Gagal posting! Ada detail dengan qty kotor kosong / 0",
                    'status' => false,
                    'token' => csrf_hash()
                ]);
            }
        }

        // Kalau lolos validasi → update status
        $this->jasaVendorInKepitingKukusModel->update($id, ['status_bayar' => '1']);

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
            $data = $this->jasaVendorInKepitingKukusModel->listBarang(
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

        $result = $this->jasaVendorOutKepitingKukusModel
            ->select('DISTINCT(divisis.id), divisis.divisi')
            ->join('divisis', 'divisis.id = jasa_vendor_out_kepiting_kukus.divisi_id', 'left')
            ->whereIn('jasa_vendor_out_kepiting_kukus.divisi_id', $divisiResult)
            ->where('vendor_id', $vendorID)
            ->where('status_posting', '1')
            ->where('status_closed', '0')
            ->where('jasa_vendor_out_kepiting_kukus.deletedAt', null)
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

        $result = $this->jasaVendorOutKepitingKukusModel
            ->select('DISTINCT(warehouses.id), warehouses.warehouse_name')
            ->join('warehouses', 'warehouses.id = jasa_vendor_out_kepiting_kukus.warehouse_id', 'left')
            ->where('jasa_vendor_out_kepiting_kukus.divisi_id', $divisiID)
            ->where('vendor_id', $vendorID)
            ->where('status_posting', '1')
            ->where('status_closed', '0')
            ->where('jasa_vendor_out_kepiting_kukus.deletedAt', null)
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

        $result = $this->jasaVendorOutKepitingKukusModel
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
            $no = $this->jasaVendorInKepitingKukusModel->get_no(
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
            $no = $this->jasaVendorInKepitingKukusModel->get_no(
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
