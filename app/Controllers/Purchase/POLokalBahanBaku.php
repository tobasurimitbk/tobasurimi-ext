<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Models\AccountBarangModel;
use App\Models\CompaniesModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\RMPurchaseOrderDetailModel;
use App\Models\SupplierModel;
use App\Models\BarangMasterModel;
use App\Models\BagianModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\SatuansModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SppDetailModel;
use App\Models\SppModel;
use App\Models\SupplierHargaModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use Exception;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;

class POLokalBahanBaku extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $RMPurchaseOrderModel;
    protected $RMPurchaseOrderDetailModel;
    protected $CompaniesModel;
    protected $SupplierModel;
    protected $barangModel;
    protected $barangSpesifikasiModel;
    protected $metadataModel;
    protected $BagianModel;
    protected $SatuansModel;
    protected $SupplierHargaModel;
    protected $warehousesModel;
    protected $penerimaanBarangModel;
    protected $dompdf;
    protected $penerimaanBarangDetailModel;
    protected $jurnalController;
    protected $sppModel;
    protected $sppDetailModel;
    protected $divisiModel;
    protected $kemasanModel;
    protected $accountBarangModel;

    protected $this_user_id;
    protected $is_admin;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;
        $this->RMPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RMPurchaseOrderDetailModel = new RMPurchaseOrderDetailModel();
        $this->SupplierModel = new SupplierModel();
        $this->barangModel = new BarangMasterModel();
        $this->barangSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->metadataModel = new MetadataModel();
        $this->BagianModel = new BagianModel();
        $this->CompaniesModel = new CompaniesModel();
        $this->SatuansModel = new SatuansModel();
        $this->SupplierHargaModel = new SupplierHargaModel();
        $this->warehousesModel = new WarehousesModel();
        $this->dompdf = new Dompdf();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->jurnalController = new JurnalUmum();
        $this->sppModel = new SppModel();
        $this->sppDetailModel = new SppDetailModel();
        $this->divisiModel = new DivisisModel();
        $this->sppModel = new SppModel();
        $this->kemasanModel = new KemasanModel();
        $this->accountBarangModel = new AccountBarangModel();
    }

    public function poLokalBahanBaku()
    {
        return view('Purchase/poLokalBahanBaku/index');
    }

    public function createPOLokalBahanBaku()
    {
        $dataBCType = $this->metadataModel->getBCUsed("po_lokal_bb");
        $dataDivisi =  $this->divisiModel->getDivisiAccess();
        $dataSupplier = $this->SupplierModel->getSupplierByType('BAHAN BAKU');
        $dataSatuan = $this->SatuansModel->where('deletedAt', null)->findAll();
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);
        $dataBarang = $this->barangModel->getBarangByType("bahan_baku");
        $dataKemasan = $this->kemasanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', 'asc')->findAll();

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        $data = [
            "dataBCType"    => $dataBCType,
            "today"         => date("d/m/Y"),
            "dataSatuan"    => $dataSatuan,
            "dataSupplier"  => $dataSupplier,
            "dataDivisi"    => $dataDivisi,
            "dataWarehouse" => $dataWarehouse,
            "dataBarang"    => $dataBarang,
            "dataKemasan"   => $dataKemasan

        ];

        return view('Purchase/poLokalBahanBaku/form', $data);
    }

    public function getByIdPOLokalBahanBaku($id = null)
    {
        $id = decrypt($id);
        $dataSupplier = $this->SupplierModel->getSupplierByType('BAHAN BAKU');
        $dataWarehouse = $this->warehousesModel->get_by_company_id($this->this_company_id);
        $dataBCType = $this->metadataModel->getBCUsed("po_lokal_bb");
        $dataDivisi =  $this->divisiModel->getDivisiAccess();
        $dataKemasan = $this->kemasanModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', 'asc')->findAll();
        $dataSatuan = $this->SatuansModel->where('deletedAt', null)->findAll();

        foreach (array_keys($dataSupplier) as $key) {
            $dataSupplier[$key] = (object)$dataSupplier[$key];
        }

        $data = [
            "today" => date("d/m/Y"),
            "dataSupplier" => $dataSupplier,
            "dataWarehouse" => $dataWarehouse,
            "dataBCType"    => $dataBCType,
            "dataDivisi"    => $dataDivisi,
            "dataKemasan"   => $dataKemasan,
            "dataSatuan"    => $dataSatuan,
        ];


        if (!empty($id)) {
            $dataBBLokal = $this->RMPurchaseOrderModel->getPoBBLokalById($id);
            if ($dataBBLokal == null) {
                return redirect()->to('po-lokal-bahan-baku');
            }
            $dataBBLokalDetail = $this->RMPurchaseOrderDetailModel->getPoBBLokalDetailById($id);
            $dataBarang = $this->barangModel->getBySupplier($dataBBLokal->supplier_id);

            $data["dataSpesifikasi"] = $this->SupplierHargaModel->getSupplierHarga($dataBBLokal->supplier_id, $dataBBLokal->barang_id, $dataBBLokal->divisi_id);
            $data["dataSPP"] = $this->sppModel->find($dataBBLokal->purchase_request_id);
            $data["dataListSPP"] = $this->sppModel->where('request_status', "waiting")->where('is_posted', '1')->where('divisi_id', $dataBBLokal->divisi_id)->where('spp_type', "Lokal BB")->where('deletedAt', null)->findAll();
            $data["dataBarang"] = $dataBarang;
            $data["dataPOLokal"] = $dataBBLokal;
            $data["dataPOLokal"]->rm_purchase_order_details = $dataBBLokalDetail;
        }

        return view('Purchase/poLokalBahanBaku/form', $data);
    }

    public function allPOLokalBahanBaku()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        if ($this->is_admin == '1') {
            $condition = [
                'rm_purchase_orders.deletedAt' => null,
                'rm_purchase_orders.company_id' => $this->this_company_id,
                'rm_purchase_order_details.deletedAt' => null,
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                'rm_purchase_orders.deletedAt' => null,
                'rm_purchase_orders.company_id' => $this->this_company_id,
                'rm_purchase_order_details.deletedAt' => null,
                'rm_purchase_orders.createdBy' => $this->this_user_id
            ];
        }

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "is_posted"     => $this->request->getVar("is_posted"),
        ];
        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $poData = $this->RMPurchaseOrderModel->getPoBBList($condition, $addCondition, $limit, $offset);

        $dataPOLokal = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poData['data'] as $data) {
            // $detailPurchase = $this->RMPurchaseOrderDetailModel->where('rm_purchase_order_id', $data->id)->where('deletedAt', null)->findAll();
            $unPostingCheck = $this->penerimaanBarangModel->where('tipe_bahan', "BAKU")->where('status_penerimaan', "LOKAL")->like('multiple_po_id', $data->id)->first();
            // $nilaiPph = !empty($data->supplierNPWP) ? (1.00 - 0.0025) : (1.00 - 0.005);
            // $nilaiPph2 = !empty($data->supplierNPWP) ? 0.0025 : 0.005;

            // // PUNYA NPWP 0.25
            // // GK PUNYA 0.5
            // // 314.54 RUPIAH 
            // // sebelum pph 2,635
            // // 2,642,105.26 SEBELUM PPH
            // // 26,35.500 SESUDAH PPH
            // // 325 KTP 

            // $totalQty = 0;
            // // Tanpa PPH
            // $nilaiTotalBulanan = 0;
            // $nilaiTotalUmum = 0;
            // $nilaiTotalHarian = 0;
            // // Dengan PPH
            // $nilaiTotalBulananWithPPH = 0;
            // $nilaiTotalUmumWithPPH = 0;
            // $nilaiTotalHarianWithPPH = 0;
            // // Total Tambahan
            // $totalTambahan = 0;
            // $totalTambahanWithPPH = 0;

            // // Nilai PPH
            // // $nilaiPPHBulanan = 0;
            // // $nilaiPPHumum = 0;
            // // $nilaiPPHHarian = 0;

            // foreach ($detailPurchase as $d) {

            //     if ($data->pph === "None" || $data->pph === "Supplier") {
            //         $nilaiTotalHarian +=  ($d['daily_price'] * $d['qty']);
            //         $nilaiTotalUmum +=  ($d['general_price'] * $d['qty']);
            //         $nilaiTotalBulanan += ($d['monthly_price'] * $d['qty']);
            //     } else {
            //         // COMPANY
            //         $nilaiTotalHarian +=  (($d['daily_price'] / $nilaiPph) * $d['qty']);
            //         $nilaiTotalUmum +=  (($d['general_price'] / $nilaiPph) * $d['qty']);
            //         $nilaiTotalBulanan += (($d['monthly_price'] / $nilaiPph) * $d['qty']);
            //     }

            //     $totalQty += $d['qty'];
            // }

            // if ($data->pph === "Supplier" || $data->pph === "Company") {
            //     $nilaiTotalBulananWithPPH = $nilaiTotalBulanan - ($nilaiTotalBulanan * $nilaiPph2);
            //     $nilaiTotalUmumWithPPH = $nilaiTotalUmum - ($nilaiTotalUmum * $nilaiPph2);
            //     $nilaiTotalHarianWithPPH = $nilaiTotalHarian - ($nilaiTotalHarian * $nilaiPph2);
            // }

            // if ($data->pph == "Company") {
            //     $selisih = ($data->cong_batasan - $data->cong_sebenarnya + $data->subsidi_langsung) / $nilaiPph;
            //     $totalTambahan = $selisih;
            //     $totalTambahanWithPPH = $totalTambahan - ($totalTambahan * $nilaiPph2);
            // } else {
            //     $selisih =  ($data->cong_batasan - $data->cong_sebenarnya + $data->subsidi_langsung);
            //     $totalTambahan = ($selisih * $totalQty);
            //     $totalTambahanWithPPH = $totalTambahan - ($totalTambahan * $nilaiPph2);
            // }

            // // NILAI SEBELUM PPH
            // $totalBeforePph = $nilaiTotalBulanan + $nilaiTotalHarian + $nilaiTotalUmum +  abs($totalTambahan);
            // $totalAfterPph = $nilaiTotalBulananWithPPH + $nilaiTotalHarianWithPPH + $nilaiTotalUmumWithPPH + abs($totalTambahanWithPPH);

            array_push($dataPOLokal, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "divisi"        => $data->divisi,
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "po_no"         => $data->po_no,
                "companyName"   => $data->companyName,
                "supplierName"  => $data->supplierName,
                "itemCount"     => $data->itemCount,
                "qtyTotal"      => round($data->totalQty, 2),
                "total_after_pph" => number_format($data->total_after_pph, 2),
                "total_before_pph" => number_format($data->total_before_pph, 2),
                "is_posted"     => $data->is_posted,
                "status_penerimaan" => $data->status_penerimaan === "0" ? "OPEN" : "CLOSED",
                "un_posting" => $unPostingCheck == null ? 0 : 1,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $poData['totalData'],
            "recordsFiltered"   => $poData['totalFilteredData'],
            "data"              => $dataPOLokal,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function savePOLokalBahanBaku()
    {

        $first = $this->RMPurchaseOrderModel
            ->where('po_no', $this->request->getVar("po_no"))
            ->where('company_id', $this->this_company_id)
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "No Purchase Order Sudah Ada" . $first['po_no'] . " COMPANY ID" . $this->this_company_id,
                'status' => false
            ]);
        }

        $id = $this->RMPurchaseOrderModel->insert([
            'company_id' => $this->this_company_id,
            "warehouse_id" => $this->request->getVar("warehouse_id"),
            "purchase_request_id" => $this->request->getVar('spp_id'),
            "supplier_id" => $this->request->getVar("supplier_id"),
            "barang_id" => $this->request->getVar("barang_id"),
            "divisi_id" => $this->request->getVar('divisi_id'),
            "bc_type" => $this->request->getVar("bc_type"),
            "kemasan_id" => $this->request->getVar('kemasan_id'),
            "jumlah_kemasan" => $this->request->getVar('jumlah_kemasan'),
            "kemasan_tambahan" => $this->request->getVar('kemasan_tambahan'),
            "po_no" =>  $this->request->getVar("po_no"),
            "po_date" => $this->request->getVar("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("po_date")))) : "",
            "pph" => $this->request->getVar("pph"),
            "cong_sebenarnya" => $this->request->getVar("cong_sebenarnya") ? formatter($this->request->getVar("cong_sebenarnya"), "STR_TO_INT") : 0,
            "cong_batasan" => $this->request->getVar("cong_batasan") ? formatter($this->request->getVar("cong_batasan"), "STR_TO_INT") : 0,
            "subsidi_langsung" => $this->request->getVar("subsidi_langsung") ? formatter($this->request->getVar("subsidi_langsung"), "STR_TO_INT") : 0,
            "total" => $this->request->getVar("total") ? formatter($this->request->getVar("total"), "STR_TO_INT") : 0,
            "is_posted" => false,
            "createdBy" => session()->get("login")->user_id,
        ]);

        $this->sppModel->update($this->request->getVar('spp_id'), [
            'request_status' => 'finished'
        ]);

        foreach (json_decode($this->request->getVar("items")) as $r) {
            $this->RMPurchaseOrderDetailModel->insert([
                'rm_purchase_order_id' => $id,
                'supplier_harga_id' => $r->supplier_harga_id,
                'barang1_id' =>  $this->request->getVar("barang_id"),
                'barang2_id' => $r->spesifikasi_id,
                'satuan_id' => $r->satuan_id,
                'peti' => $r->peti,
                'quality' => $r->quality,
                'note' => $r->keterangan,
                'qty' => $r->qty,
                'qty_diterima' => 0,
                'remaining_qty' => $r->qty,
                'general_price' => $r->harga,
                'daily_price' => $r->daily_price,
                'monthly_price' => $r->monthly_price
            ]);
            $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisi_id'), $this->request->getVar("barang_id"), $r->spesifikasi_id, $r->keterangan);
        }

        $detailPurchase = $this->RMPurchaseOrderDetailModel->where('rm_purchase_order_id', $id)->where('deletedAt', null)->findAll();
        $totalHarga = 0;
        foreach ($detailPurchase as $d) {
            $totalHarga += ($d['general_price'] + $d['daily_price'] + $d['monthly_price']) * $d['qty'];
        }

        $totalFinal = $this->RMPurchaseOrderModel->generateTotalBeforeAndAfterPph($id);

        $this->RMPurchaseOrderModel->update($id, [
            'total' => $totalHarga  + $this->request->getVar("subsidi_langsung"),
            'total_before_pph'  => $totalFinal['total_before_pph'],
            'total_after_pph' => $totalFinal['total_after_pph']
        ]);

        return response()->setJSON([
            'message' => "PO Lokal Bahan Baku Berhasil Disimpan",
            'token' => csrf_hash(),
            'id' => encrypt($id),
            'status' => true
        ]);
    }

    public function updatePOLokalBahanBaku()
    {
        $id = decrypt($this->request->getVar('id'));

        $first = $this->RMPurchaseOrderModel
            ->where('company_id', $this->this_company_id)
            ->where('po_no', $this->request->getVar("po_no"))
            ->where('id !=', $id)
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "No Purchase Order Sudah Ada",
                'status' => false
            ]);
        }

        $firstData = $this->RMPurchaseOrderModel->find($id);

        $this->sppModel->update($firstData['purchase_request_id'], [
            'request_status' => 'waiting'
        ]);

        $detailPurchase = $this->RMPurchaseOrderDetailModel->where('rm_purchase_order_id', $id)->where('deletedAt', null)->findAll();
        $totalHarga = 0;
        foreach ($detailPurchase as $d) {
            $totalHarga += ($d['general_price'] + $d['daily_price'] + $d['monthly_price']) * $d['qty'];
        }

        $this->RMPurchaseOrderModel->update($id, [
            'company_id' => $this->this_company_id,
            "warehouse_id" => $this->request->getVar("warehouse_id"),
            "purchase_request_id" => $this->request->getVar('spp_id') ?  $this->request->getVar('spp_id') :  $firstData['purchase_request_id'],
            "supplier_id" => $this->request->getVar("supplier_id"),
            "barang_id" => $this->request->getVar("barang_id"),
            "divisi_id" => $this->request->getVar('divisi_id'),
            "kemasan_id" => $this->request->getVar('kemasan_id'),
            "jumlah_kemasan" => $this->request->getVar('jumlah_kemasan'),
            "kemasan_tambahan" => $this->request->getVar('kemasan_tambahan'),
            "bc_type" => $this->request->getVar("bc_type"),
            "po_no" =>  $this->request->getVar("po_no"),
            "po_date" => $this->request->getVar("po_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("po_date")))) : "",
            "pph" => $this->request->getVar("pph"),
            "cong_sebenarnya" => $this->request->getVar("cong_sebenarnya") ? formatter($this->request->getVar("cong_sebenarnya"), "STR_TO_INT") : 0,
            "cong_batasan" => $this->request->getVar("cong_batasan") ? formatter($this->request->getVar("cong_batasan"), "STR_TO_INT") : 0,
            "subsidi_langsung" => $this->request->getVar("subsidi_langsung") ? formatter($this->request->getVar("subsidi_langsung"), "STR_TO_INT") : 0,
            "total" => $totalHarga  + $this->request->getVar("subsidi_langsung"),
            "createdBy" => session()->get("login")->user_id,
        ]);

        $this->sppModel->update($this->request->getVar('spp_id'), [
            'request_status' => 'finished'
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach (json_decode($this->request->getVar("items")) as $r) {
            $check = $this->RMPurchaseOrderDetailModel
                ->where('rm_purchase_order_id', $id)
                ->where('supplier_harga_id', $r->supplier_harga_id)
                ->where('barang2_id', $r->spesifikasi_id)
                ->where('barang1_id', $this->request->getVar('barang_id'))
                ->first();

            if ($check != null) {
                // UPDATE
                $this->RMPurchaseOrderDetailModel->update($check['id'], [
                    'rm_purchase_order_id' => $id,
                    'supplier_harga_id' => $r->supplier_harga_id,
                    'barang1_id' =>  $this->request->getVar("barang_id"),
                    'barang2_id' => $r->spesifikasi_id,
                    'satuan_id' => $r->satuan_id,
                    'peti' => $r->peti,
                    'quality' => $r->quality,
                    'note' => $r->keterangan,
                    'qty' => $r->qty,
                    'qty_diterima' => 0,
                    'remaining_qty' => $r->qty,
                    'general_price' => $r->harga,
                    'daily_price' => $r->daily_price,
                    'monthly_price' => $r->monthly_price
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // NEW BARANG
                // DELETE
                $this->RMPurchaseOrderDetailModel
                    ->where('rm_purchase_order_id', $id)
                    ->where('supplier_harga_id', $r->supplier_harga_id)
                    ->where('barang2_id', $r->spesifikasi_id)
                    ->where('barang1_id', $this->request->getVar('barang_id'))
                    ->delete();

                // INSERT NEW
                $id_detail_new = $this->RMPurchaseOrderDetailModel->insert([
                    'rm_purchase_order_id' => $id,
                    'supplier_harga_id' => $r->supplier_harga_id,
                    'barang1_id' =>  $this->request->getVar("barang_id"),
                    'barang2_id' => $r->spesifikasi_id,
                    'satuan_id' => $r->satuan_id,
                    'peti' => $r->peti,
                    'quality' => $r->quality,
                    'note' => $r->keterangan,
                    'qty' => $r->qty,
                    'qty_diterima' => 0,
                    'remaining_qty' => $r->qty,
                    'general_price' => $r->harga,
                    'daily_price' => $r->daily_price,
                    'monthly_price' => $r->monthly_price
                ]);

                array_push($id_detail_all, $id_detail_new);
            }
            $this->accountBarangModel->insertAccountBarang($this->this_company_id, $this->request->getVar('divisi_id'), $this->request->getVar("barang_id"), $r->spesifikasi_id, $r->keterangan ?? null);
        }

        $this->RMPurchaseOrderDetailModel
            ->where('rm_purchase_order_id', $id)
            ->whereNotIn('id', $id_detail_all)
            ->delete();

        $totalFinal = $this->RMPurchaseOrderModel->generateTotalBeforeAndAfterPph($id);

        $this->RMPurchaseOrderModel->update($id, [
            'total_before_pph'  => $totalFinal['total_before_pph'],
            'total_after_pph' => $totalFinal['total_after_pph']
        ]);


        return response()->setJSON([
            'message' => "PO Lokal Bahan Baku Berhasil Diupdate",
            'token' => csrf_hash(),
            'id' => encrypt($id),
            'status' => true
        ]);
    }

    public function closePOLokalBahanBaku()
    {
        try {
            $id = $this->request->getVar('id');
            if (is_numeric($id)) {
                $id = $id;
            } else {
                $id = decrypt($id);
            }

            $payload = [
                "status_penerimaan" => true
            ];

            if (!empty($id)) {
                $this->RMPurchaseOrderModel->update($id, $payload);

                $data = [
                    "status"    => true,
                    "message"   => "PO Berhasil di Close",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "PO Gagal di Close",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateStatusPOLokalBahanBaku()
    {
        try {

            $id = $this->request->getVar('id');
            if (is_numeric($id)) {
                $id = $id;
            } else {
                $id = decrypt($id);
            }

            // po posting
            $payload = [
                "is_posted" => $this->request->getVar('status_posting')
            ];

            if (!empty($id)) {
                $data = [
                    "status"    => true,
                    "message"   => "Status Posting PO Berhasil Diperbaruhi",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                // $result = $this->jurnalController->insertDataPembelian($id, "BAHAN BAKU", "LOKAL", "pembelian");
                // if ($result) {
                //     $responseBody = json_decode($result->getBody(), true);
                //     if ($responseBody && isset($responseBody['status'])) {
                //         $data["status"] =  false;
                //         $data["message"] = $responseBody['message'];
                //         $data["token"] = csrf_hash();
                //     }
                // } else {
                if ($payload['is_posted']) {
                    $detail = $this->RMPurchaseOrderModel->where('id', $id)->first();
                    // cek if warehouse_id != null
                    if ($detail['warehouse_id'] != null && $detail['warehouse_id'] != 0) {
                        $result = $this->jurnalController->insertDataPembelian($id, "BAHAN BAKU", "LOKAL", "pembelian");
                        if ($result) {
                            $responseBody = json_decode($result->getBody(), true);
                            if ($responseBody && isset($responseBody['status'])) {
                                $data = [
                                    "status"    => false,
                                    "message"   => $responseBody['message'],
                                    "payload"   => "",
                                    'token'     => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        } else {
                            $this->penerimaanBarangModel->generateLpbBB($detail['id'], $detail['warehouse_id'], $detail['bc_type'], $detail['po_date']);
                        }
                    }
                }
                $this->RMPurchaseOrderModel->update($id, $payload);
                // }
                echo json_encode($data);
            } else {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Disimpan",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
            }
        } catch (Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deletePOLokalBahanBaku()
    {
        $id = $this->request->getVar('id');
        if (is_numeric($id)) {
            $id = $id;
        } else {
            $id = decrypt($id);
        }

        $firstData = $this->RMPurchaseOrderModel->find($id);

        $this->sppModel->update($firstData['purchase_request_id'], [
            'request_status' => 'waiting'
        ]);

        if (empty($id)) {
            $data = [
                "status"     => false,
                "message"    => "Data Gagal Dihapus",
                'token'      => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }

        $this->RMPurchaseOrderModel->delete($id);
        $this->RMPurchaseOrderDetailModel->where('rm_purchase_order_id', $id)->delete();

        $data = [
            "status"    => true,
            "message"   => "Data Berhasil dihapus",
            'token'     => csrf_hash()
        ];
        echo json_encode($data);
        return;
    }

    public function print($id = null)
    {
        if (is_numeric($id)) {
            $id = $id;
        } else {
            $id = decrypt($id);
        }
        if ($id) {
            $filename = "PO LOKAL Bahan Baku";

            $data = [];
            $dataPO = $this->RMPurchaseOrderModel->getPoBBLokalById($id);
            $dataPO->itemName = $dataPO->barangName;
            $dataPO->lpb = null;
            $dataPO->lpbDetail = null;

            if ($dataPO) {
                $dataPODetail = $this->RMPurchaseOrderDetailModel->getPoBBLokalDetailById($id);

                // dd($dataPODetail);

                $totalPrice = 0;
                $totalDailyPrice = 0;
                $totalQty = 0;
                $pph = 0.00;
                $pphTax = !empty($dataPO->supplierNPWP) ? 0.0025 : 0.005;

                $objPph = [
                    "None" => 0,
                    "Supplier" => 1,
                    "Company" => -1,
                ];

                $pphTax *= $objPph[$dataPO->pph];

                $dataPO->nilai_pph = !empty($dataPO->supplierNPWP) ? (1.00 - 0.0025) : (1.00 - 0.005);
                $dataPO->nilai_pph2 = !empty($dataPO->supplierNPWP) ? 0.0025 : 0.005;

                foreach ($dataPODetail as $value) {
                    $totalPrice += formatter($value->general_price, "CURR_TO_FLOAT") * formatter($value->qty, "CURR_TO_FLOAT");
                    $totalDailyPrice += formatter($value->daily_price, "CURR_TO_FLOAT") * formatter($value->qty, "CURR_TO_FLOAT");
                    $totalQty += formatter($value->qty, "STR_TO_FLOAT");
                }
                $dataPO->totalPrice = number_format($totalPrice, 2, '.', ',');
                $dataPO->totalDailyPrice = number_format($totalDailyPrice, 2, '.', ',');
                $dataPO->totalQty = number_format($totalQty, 2, '.', ',');
                $dataPO->totalPph = number_format($totalPrice * $pphTax, 2, '.', ',');
                $dataPO->totalDailyPph = number_format($totalDailyPrice * $pphTax, 2, '.', ',');
                $dataPO->totalPaid = number_format($totalPrice + $totalPrice * $pphTax, 2, '.', ',');
                $dataPO->totalDailyPaid = number_format(($totalDailyPrice + $totalDailyPrice * $pphTax), 2, '.', ',');
                $dataPO->amount = terbilang($totalPrice);
                $dataPO->amountDaily = terbilang($totalDailyPrice);
                if ($dataPO->pph == "Company") {
                    // Pakai nilai_pph
                    $dataPO->selisih = (($dataPO->cong_batasan ? $dataPO->cong_batasan : 0) - ($dataPO->cong_sebenarnya ? $dataPO->cong_sebenarnya : 0) + $dataPO->subsidi_langsung) / $dataPO->nilai_pph;
                    $dataPO->totalTambahan = $dataPO->selisih;
                } else {
                    // Pakai nilai_pph2
                    $dataPO->selisih = (($dataPO->cong_batasan ? $dataPO->cong_batasan : 0) - ($dataPO->cong_sebenarnya ? $dataPO->cong_sebenarnya : 0) + $dataPO->subsidi_langsung);
                    $dataPO->totalTambahan = ($dataPO->selisih * $totalQty);
                }
                // dd($dataPO->selisih, $dataPO->cong_batasan, $dataPO->cong_sebenarnya);
                // $dataPO->selisih = $dataPO->subsidi_langsung / $dataPO->nilai_pph;
                // dd($dataPO->totalTambahan, $dataPO->selisih, $dataPO->totalQty,  $dataPO->nilai_pph2);
                $dataPO->pphTambahan = $dataPO->totalTambahan;

                if ($dataPODetail) {
                    $data["dataPO"] = $dataPO;
                    $data["dataPODetail"] = $dataPODetail;
                }
            }

            $lpb = $this->penerimaanBarangModel->where('status_penerimaan', "LOKAL")->where('tipe_bahan', "BAKU")->like('multiple_po_id', $id)->first();

            if ($lpb != null) {
                $lpbDetail = $this->penerimaanBarangModel->getById($lpb['id']);
                $dataPenerimaanBarangDetail = $this->penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($lpb['id'], "BAKU", "LOKAL");
                $dataPO->lpb = $lpbDetail;
                $dataPO->lpbDetail = $dataPenerimaanBarangDetail;
            }

            $this->dompdf->loadHtml(view('Purchase/poLokalBahanBaku/print', $data));
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function dropdownHistoriPenerimaanBarang()
    {
        $id = decrypt($this->request->getVar('id'));
        $listBarang = $this->RMPurchaseOrderDetailModel
            ->select('
            rm_purchase_order_details.qty_diterima AS diterima, 
            rm_purchase_order_details.remaining_qty AS sisa, 
            CONCAT(barang_master.barang_name, " - ",barang_master_spesifikasi.spesifikasi) AS nama_barang, 
            barang_master.kode_barang, 
            rm_purchase_order_details.qty')
            ->join('barang_master', 'barang_master.id = rm_purchase_order_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = rm_purchase_order_details.barang2_id', 'left')
            ->where('rm_purchase_order_details.rm_purchase_order_id', $id)
            ->where('rm_purchase_order_details.deletedAt', null)
            ->findAll();

        $poDetail = $this->RMPurchaseOrderModel->select('rm_purchase_orders.po_no, suppliers.name AS supplierName')
            ->join('suppliers', 'suppliers.id = rm_purchase_orders.supplier_id')
            ->where('rm_purchase_orders.id', $id)
            ->where('rm_purchase_orders.deletedAt', null)
            ->first();

        $lpbDetail = $this->penerimaanBarangModel->like('multiple_po_id', $id)->where('deletedAt', null)->findAll();

        $lpbNo = [];
        foreach ($lpbDetail as $l) {
            $lpbNo[] = $l['no_penerimaan_barang'];
        }

        return response()->setJSON([
            'lpb_no' => count($lpbNo) == 0 ? "BELUM ADA LPB" : str_replace(['[', ']', '"', "\\"], '', json_encode($lpbNo)),
            'po_detail' => $poDetail,
            'list_barang' => $listBarang,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownPOLokalBahanBaku()
    {
        $id = formatter($this->request->getVar("id"), "STR_TO_INT");
        $divisi_id = $this->request->getVar('divisi_id');

        $dataPOLokal = $this->RMPurchaseOrderModel->getNoPenerimaanBarang(
            $id,
            $this->this_company_id,
            $divisi_id
        );

        $data = [
            "data" => $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOLokalBahanBaku()
    {
        $id = $this->request->getVar("id");

        $dataPOLokal = $this->RMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }

    public function dropdownWarehouse()
    {
        $id = $this->request->getVar('divisi_id');
        $warehouse_asal_id = $this->request->getVar('warehouse_asal_id');
        if ($warehouse_asal_id == null) {
            $res = $this->warehousesModel->where('deletedAt', null)->where('divisi_id', $id)->findAll();
        } else {
            $res = $this->warehousesModel->where('deletedAt', null)->where('divisi_id', $id)->whereNotIn('id', [$warehouse_asal_id])->findAll();
        }

        return response()->setJSON([
            'data' => $res,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownGetSpp()
    {
        $id = $this->request->getVar('divisi_id');
        $spp_type = $this->request->getVar('spp_type');
        $condition = [
            'purchase_requests.deletedAt' => null,
            'purchase_requests.divisi_id' => $id,
            'purchase_requests.is_posted' => '1',
            'purchase_requests.request_status' => 'waiting',
            // 'purchase_requests.user_id' => $this->this_user_id,
            // 'purchase_requests.spp_type' => $spp_type
        ];
        $data = $this->sppModel->where($condition)->like('purchase_requests.spp_type', $spp_type)->findAll();

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $data,
            'status' => true,
            'spp_type' => $spp_type
        ]);
    }

    public function dropdownGetSppDetail()
    {
        $id = $this->request->getVar('spp_id');
        $supplier_id = $this->request->getVar('supplier_id');

        $condition = [
            'purchase_request_details.purchase_request_id' => $id,
            'purchase_request_details.deletedAt' => null
        ];

        $selectQry = "
            purchase_request_details.*,
            barang_master.barang_name,
            barang_master_spesifikasi.spesifikasi,
            satuans.kode_satuan
        ";

        $spp = $this->sppModel->find($id);

        if ($spp == null) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => 'Spp tidak ada'
            ]);
        }

        $sppDetail = $this->sppDetailModel->select($selectQry)
            ->join('barang_master', 'purchase_request_details.barang1_id = barang_master.id', 'left')
            ->join('barang_master_spesifikasi', 'purchase_request_details.barang2_id=barang_master_spesifikasi.id', 'left')
            ->join('satuans', 'satuans.id = purchase_request_details.unit', 'left')
            ->where($condition)->findAll();

        $result = [];

        foreach ($sppDetail as $s) {
            $supplierHarga = $this->SupplierHargaModel->getDetailSpesifikasi(
                $s['barang1_id'],
                $supplier_id,
                $s['barang2_id'],
                $spp['divisi_id']
            );

            if ($supplierHarga == null) {
                // create new
                $this->SupplierHargaModel->insert([
                    'supplier_id' => $supplier_id,
                    'bahan_baku_id' => $s['barang1_id'],
                    'spesifikasi_id' => $s['barang2_id'],
                    'nama_barang' => $s['barang_name'] . ' ' . $s['spesifikasi'],
                    'spesifikasi' => $s['spesifikasi'],
                    'harga_umum' => 0,
                    'harga_harian' => 0,
                    'harga_bulanan' => 0,
                ]);

                $supplierHarga = $this->SupplierHargaModel->getDetailSpesifikasi(
                    $s['barang1_id'],
                    $supplier_id,
                    $s['barang2_id'],
                    $spp['divisi_id']
                );
            }

            $result[] = [
                'supplier_harga_id' => $supplierHarga['id'],
                'barang1_id' => $s['barang1_id'],
                'barang2_id' => $s['barang2_id'],
                'spesifikasi' => $s['spesifikasi'],
                'satuan_id' => $s['unit'],
                'satuan_name' => $s['kode_satuan'],
                'harga_umum' => $supplierHarga['harga_umum'],
                'harga_harian' => $supplierHarga['harga_harian'],
                'harga_bulanan' => $supplierHarga['harga_bulanan'],
                'keterangan' => $s['note'],
                'qty' => $s['qty'],
                'peti' => '-',
                'kualitas' => 'Baik',
                'total' => ($supplierHarga['harga_umum'] + $supplierHarga['harga_harian'] + $supplierHarga['harga_bulanan']) * $s['qty']
            ];
        }

        return response()->setJSON([
            'barang_master_id' => $sppDetail[0]['barang1_id'],
            'sppDetail' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getBarangAndSupplier()
    {
        $supplier_id = $this->request->getVar('supplier_id');
        $bahan_baku_id = $this->request->getVar('barang_id');
        $divisi_id = $this->request->getVar('divisi_id');

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'data' => $this->SupplierHargaModel->getSupplierHarga(
                $supplier_id,
                $bahan_baku_id,
                $divisi_id,
            )
        ]);
    }

    public function generateNoPO()
    {
        // yyyy-mm-dd
        $tanggal = date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal"))));
        $tanggalExplode = explode('-', $tanggal);
        $year = $tanggalExplode[0];
        $month = $tanggalExplode[1];

        $no = $this->RMPurchaseOrderModel->get_new_no_po(
            $month,
            $year,
            $this->this_company_id
        );
        return json_encode($no);
    }
}
