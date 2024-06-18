<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\BC27Model;
use App\Models\CompaniesModel;
use App\Models\DivisisModel;
use App\Models\MutasiGlobalDetailModel;
use App\Models\MutasiGlobalModel;
use App\Models\PenerimaanMutasiGlobalDetailModel;
use App\Models\PenerimaanMutasiGlobalModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class PenerimaanMutasiGlobal extends BaseController
{
    protected $this_user_id;
    protected $this_company_id;
    protected $companyModel;
    protected $divisiModel;
    protected $penerimaanMutasiGlobalModel;
    protected $penerimaanMutasiGlobalDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $mutasiGlobalModel;
    protected $mutasiGlobalDetailModel;
    protected $warehouseModel;
    protected $bc27Model;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->companyModel = new CompaniesModel();
        $this->divisiModel = new DivisisModel();
        $this->penerimaanMutasiGlobalModel = new PenerimaanMutasiGlobalModel();
        $this->penerimaanMutasiGlobalDetailModel = new PenerimaanMutasiGlobalDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->mutasiGlobalModel = new MutasiGlobalModel();
        $this->mutasiGlobalDetailModel = new MutasiGlobalDetailModel();
        $this->bc27Model = new BC27Model();
        $this->warehouseModel = new WarehousesModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('Warehouse/penerimaanMutasi/index_global');
    }

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Warehouse/penerimaanMutasi/form_global', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $penerimaanMutasiGlobal = $this->penerimaanMutasiGlobalModel->find($id);

        if ($penerimaanMutasiGlobal == null) {
            return redirect()->to('penerimaan-mutasi/global');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'warehouse' => $this->warehouseModel->where('divisi_id', $penerimaanMutasiGlobal['divisi_penerima_id'])->where('deletedAt', null)->findAll(),
            'penerimaanMutasiGlobal' => $penerimaanMutasiGlobal,
            'dropdownCompanyExcept' => $this->companyModel->getCompaniesExcepct($this->this_company_id),
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Warehouse/penerimaanMutasi/form_global', $data);
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
            "search" => $this->request->getVar("search"),
            "divisi_penerima_id" => $this->request->getVar("divisi_penerima_id"),
            "status" => $this->request->getVar("status"),
            "penerimaan_mutasi_no" => $this->request->getVar("penerimaan_mutasi_no"),
            "multiple_mutasi_no" => $this->request->getVar("multiple_mutasi_no"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'penerimaan_mutasi_global.company_penerima_id' => $this->this_company_id,
            'penerimaan_mutasi_global.deletedAt' => null
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->penerimaanMutasiGlobalModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            $listItem = $this->penerimaanMutasiGlobalDetailModel->where('penerimaan_mutasi_global_id', $data->id)->findAll();
            $totalItem = count($listItem);

            // CARI DIVISI PENGIRIM
            $divisiPengirimArr = $this->mutasiGlobalModel
                ->select('divisis.divisi')
                ->join('divisis', 'divisis.id = mutasi_global.divisi_asal_id', 'left')
                ->whereIn('mutasi_global.id', json_decode($data->multiple_mutasi_id))
                ->findAll();

            $divisiPengirimResultArr = [];

            foreach ($divisiPengirimArr as $d) {
                array_push($divisiPengirimResultArr, $d['divisi']);
            }
            $divisiPengirimResultArr = array_unique($divisiPengirimResultArr);

            // CARI DOKUMEN MUTASI (BC 2.7)
            $mutasiNameArr = [];
            foreach ($listItem as $l) {
                $bc27 = $this->bc27Model->where('mutasi_global_id', $l['mutasi_global_id'])->first();
                array_push($mutasiNameArr, $bc27['no_aju']);
            }

            $mutasiNameArr = array_unique($mutasiNameArr);

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_mutasi_no"  => $data->penerimaan_mutasi_no,
                "multiple_no_mutasi"    => str_replace(['"', ']', '['], " ", $data->multiple_no_mutasi),
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi_penerima"       => $data->divisi_penerima,
                "warehouse_penerima"    => $data->warehouse_penerima,
                "company_pengirim"      => $data->company_pengirim,
                "divisi_pengirim"       => implode(', ', $divisiPengirimResultArr),
                "dokumen_mutasi_barang" => "BC 2.7 (" . implode(', ', $mutasiNameArr) . ")",
                "total_item"            => $totalItem,
                "status_posting"        => $data->status_posting
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


    public function createAction()
    {
        $mutasiNo = $this->penerimaanMutasiGlobalModel->getMutasiNo($this->request->getVar('multiple_mutasi_id'));
        $barang = json_decode($_POST['listBarang']);
        $qty_diterima_current = 0;

        $first = $this->penerimaanMutasiGlobalModel
            ->where('company_penerima_id', $this->this_company_id)
            ->where('penerimaan_mutasi_no', $this->request->getVar('penerimaan_mutasi_no'))
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Nomor penerimaan mutasi sudah ada"
            ]);
        }


        foreach ($barang as $b) {
            $qty_diterima_current += $b->qty_diterima_current;
        }

        if ($qty_diterima_current == 0) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Isikan minimal satu item barang yang akan diterima",
                'status' => false
            ]);
        }

        $id = $this->penerimaanMutasiGlobalModel->insert([
            'company_penerima_id' => $this->this_company_id,
            'company_pengirim_id' => $this->request->getVar('company_pengirim_id'),
            'divisi_penerima_id' => $this->request->getVar('divisi_penerima_id'),
            'warehouse_penerima_id' => $this->request->getVar('warehouse_penerima_id'),
            'penerimaan_mutasi_no' => $this->request->getVar('penerimaan_mutasi_no'),
            'multiple_mutasi_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_mutasi_id'))),
            'multiple_no_mutasi' => str_replace(['\\"', '\\'], '', json_encode($mutasiNo)),
            'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0',
            'createdBy' => $this->this_user_id
        ]);

        foreach ($barang as $b) {
            if ($b->qty_diterima_current != 0) {
                $this->penerimaanMutasiGlobalDetailModel->insert([
                    'penerimaan_mutasi_global_id' => $id,
                    'mutasi_global_id' => $b->mutasi_global_id,
                    'mutasi_global_detail_id' => $b->mutasi_global_detail_id,
                    'stock_mutasi_id' => $b->stock_mutasi_id,
                    'bc_mutasi_id' => $b->bc_mutasi_id,
                    'no_aju_mutasi' => $b->no_aju_mutasi,
                    'stock_dokumen_asal' => $b->stock_dokumen_asal,
                    'bc_mutasi_id' => $b->bc_mutasi_id,
                    'no_aju_mutasi' => $b->no_aju_mutasi,
                    'stock_dokumen_asal' => $b->stock_dokumen_asal,
                    'qty' => $b->qty_diterima_current
                ]);
            }
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Penerimaan mutasi berhasil disimpan"
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $barang = json_decode($_POST['listBarang']);
        $qty_diterima_current = 0;

        $first = $this->penerimaanMutasiGlobalModel
            ->where('company_penerima_id', $this->this_company_id)
            ->where('penerimaan_mutasi_no', $this->request->getVar('penerimaan_mutasi_no'))
            ->where('id !=', $id)
            ->first();

        if ($first != null) {
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Nomor penerimaan mutasi sudah ada"
            ]);
        }

        foreach ($barang as $b) {
            $qty_diterima_current += $b->qty_diterima_current;
        }

        if ($qty_diterima_current == 0) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Isikan minimal satu item barang yang akan diterima",
                'status' => false
            ]);
        }

        $this->penerimaanMutasiGlobalModel->update($id, [
            'divisi_penerima_id' => $this->request->getVar('divisi_penerima_id'),
            'warehouse_penerima_id' => $this->request->getVar('warehouse_penerima_id'),
            'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0',
            'createdBy' => $this->this_user_id
        ]);

        $id_not_delete = [];
        foreach ($barang as $b) {
            if ($b->qty_diterima_current != 0) {
                // update or insert
                $check = $this->penerimaanMutasiGlobalDetailModel
                    ->where('penerimaan_mutasi_global_id', $id)
                    ->where('mutasi_global_id', $b->mutasi_global_id)
                    ->where('mutasi_global_detail_id', $b->mutasi_global_detail_id)
                    ->first();

                if ($check == null) {
                    // insert
                    $id =  $this->penerimaanMutasiGlobalDetailModel->insert([
                        'penerimaan_mutasi_global_id' => $id,
                        'mutasi_global_id' => $b->mutasi_global_id,
                        'mutasi_global_detail_id' => $b->mutasi_global_detail_id,
                        'stock_mutasi_id' => $b->stock_mutasi_id,
                        'bc_mutasi_id' => $b->bc_mutasi_id,
                        'no_aju_mutasi' => $b->no_aju_mutasi,
                        'stock_dokumen_asal' => $b->stock_dokumen_asal,
                        'bc_mutasi_id' => $b->bc_mutasi_id,
                        'no_aju_mutasi' => $b->no_aju_mutasi,
                        'stock_dokumen_asal' => $b->stock_dokumen_asal,
                        'qty' => $b->qty_diterima_current
                    ]);
                    array_push($id_not_delete, $id);
                } else {
                    // update
                    $this->penerimaanMutasiGlobalDetailModel->update($check['id'], [
                        'penerimaan_mutasi_global_id' => $id,
                        'mutasi_global_id' => $b->mutasi_global_id,
                        'mutasi_global_detail_id' => $b->mutasi_global_detail_id,
                        'stock_mutasi_id' => $b->stock_mutasi_id,
                        'bc_mutasi_id' => $b->bc_mutasi_id,
                        'no_aju_mutasi' => $b->no_aju_mutasi,
                        'stock_dokumen_asal' => $b->stock_dokumen_asal,
                        'bc_mutasi_id' => $b->bc_mutasi_id,
                        'no_aju_mutasi' => $b->no_aju_mutasi,
                        'stock_dokumen_asal' => $b->stock_dokumen_asal,
                        'qty' => $b->qty_diterima_current
                    ]);
                    array_push($id_not_delete, $check['id']);
                }
            } else {
                $this->penerimaanMutasiGlobalDetailModel
                    ->where('penerimaan_mutasi_global_id', $id)
                    ->where('mutasi_global_id', $b->mutasi_global_id)
                    ->where('mutasi_global_detail_id', $b->mutasi_global_detail_id)
                    ->delete();
            }
        }

        $this->penerimaanMutasiGlobalDetailModel->whereNotIn('id', $id_not_delete)->where('penerimaan_mutasi_global_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Penerimaan mutasi berhasil diupdate"
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->penerimaanMutasiGlobalModel->delete($id);
        $this->penerimaanMutasiGlobalDetailModel->where('penerimaan_mutasi_global_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Penerimaan mutasi berhasil dihapus"
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $selectQry = "penerimaan_mutasi_global.*, divisis.divisi AS divisi_penerima, warehouses.warehouse_name AS warehouse_penerima, companies.company AS company_pengirim";

        $penerimaanMutasiGlobal = $this->penerimaanMutasiGlobalModel->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi_global.divisi_penerima_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi_global.warehouse_penerima_id', 'left')
            ->join('companies', 'companies.id = penerimaan_mutasi_global.company_pengirim_id', 'left')
            ->where('penerimaan_mutasi_global.id', $id)
            ->first();

        if ($penerimaanMutasiGlobal == null) {
            return redirect()->to('penerimaan-mutasi/global');
        }

        $data = [
            'penerimaanMutasiGlobal' => $penerimaanMutasiGlobal,
            'penerimaanMutasiDetailGlobal' => $this->penerimaanMutasiGlobalModel->getListBarangMutasi(
                json_decode($penerimaanMutasiGlobal->multiple_mutasi_id),
                $id
            )
        ];

        $this->dompdf->loadHtml(view('Warehouse/penerimaanMutasi/print_global', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Penerimaan Mutasi BC 2.7", array("Attachment" => false));
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        // Insert To Inventori (-)
        $penerimaanMutasiGlobal = $this->penerimaanMutasiGlobalModel->find($id);
        $penerimaanMutasiGlobalList = $this->penerimaanMutasiGlobalDetailModel->where('penerimaan_mutasi_global_id', $penerimaanMutasiGlobal['id'])->where('deletedAt', null)->findAll();
        // Inventori Stok Minus
        foreach ($penerimaanMutasiGlobalList as $p) {
            $mutasiGlobal = $this->mutasiGlobalModel->where('id', $p['mutasi_global_id'])->first();
            $stockMutasi = $this->stockModel->find($p['stock_mutasi_id']);

            if ($stockMutasi['tipe_barang'] == "kemasan") {
                $barang2Id = $stockMutasi['kemasan_id'];
            } else {
                $barang2Id = $stockMutasi['barang2_id'];
            }

            // INIT STOK NYA (KARENA BARANG NYA BISA AJA TIDAK ADA DI INVENTORI)
            $stok = $this->stockModel->getStokMaster(
                $this->this_company_id,
                $penerimaanMutasiGlobal['warehouse_penerima_id'],
                $penerimaanMutasiGlobal['divisi_penerima_id'],
                $stockMutasi['tipe_barang'],
                $stockMutasi['barang1_id'],
                $barang2Id
            );

            if ($stok == null) {
                $stok = $this->stockModel->insertStok(
                    $this->this_company_id,
                    $penerimaanMutasiGlobal['warehouse_penerima_id'],
                    $penerimaanMutasiGlobal['divisi_penerima_id'],
                    $stockMutasi['tipe_barang'],
                    $stockMutasi['barang1_id'],
                    $barang2Id,
                    0
                );
            }

            // INSERT LEVEL 1
            $stok = $this->stockModel->insertStok(
                $this->this_company_id,
                $penerimaanMutasiGlobal['warehouse_penerima_id'],
                $penerimaanMutasiGlobal['divisi_penerima_id'],
                $stockMutasi['tipe_barang'],
                $stockMutasi['barang1_id'],
                $barang2Id,
                $p['qty']
            );

            // INSERT LEVEL 2
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $p['qty'],
                'In',
                date('Y-m-d'),
                $this->this_user_id,
                "MUTASI",
                $penerimaanMutasiGlobal['penerimaan_mutasi_no'],
                "-",
            );

            // STOK OLD 
            $mutasiGlobalDetail =  $this->mutasiGlobalDetailModel
                ->where('mutasi_global_id', $p['mutasi_global_id'])
                ->where('id', $p['mutasi_global_detail_id'])
                ->first();

            $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
                $mutasiGlobalDetail['stock_id'],
                $mutasiGlobalDetail['bc_id'],
                $mutasiGlobalDetail['no_aju'],
                $mutasiGlobalDetail['stock_dokumen']
            );

            // INSERT LEVEL 3 
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_mutasi_id'],
                $stok,
                $stokDetail,
                $p['qty'],
                $p['no_aju_mutasi'],
                $mutasiGlobal['no_mutasi'],
                $mutasiGlobal['no_mutasi'] . " (" . $stockOldDetail['no_po'] . ") ",
                $stockOldDetail['supplier_id'],
                $stockOldDetail['harga_umum'],
                $stockOldDetail['harga_harian'],
                $stockOldDetail['harga_bulanan'],
                $stockOldDetail['no_po']
            );
        }

        $this->penerimaanMutasiGlobalModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'message' => "Penerimaan Mutasi berhasil diposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownListNomorMutasi()
    {
        $companyPengirimId = $this->request->getVar('company_pengirim_id');

        if (empty($companyPengirimId)) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => [],
                'status' => true
            ]);
        }

        $data = $this->penerimaanMutasiGlobalModel->getListNomorMutasi(
            $companyPengirimId
        );

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $data,
            'status' => true
        ]);
    }


    public function dropdownListBarang()
    {
        $mutasiGlobalID = json_decode($this->request->getVar('mutasi_global_id'));
        $penerimaanMutasiGlobalID = decrypt($this->request->getVar('penerimaan_mutasi_global_id'));

        if (empty($mutasiGlobalID)) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => [],
                'status' => true
            ]);
        } else {

            $data = $this->penerimaanMutasiGlobalModel->getListBarangMutasi(
                $mutasiGlobalID,
                $penerimaanMutasiGlobalID
            );

            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => $data,
                'status' => true
            ]);
        }
    }

    public function dropdownListBarangMasuk()
    {
        $data = $this->stockModel->getBarangAndStock(
            $this->request->getVar('tipe_barang'),
            $this->request->getVar('divisi_id'),
            $this->request->getVar('warehouse_id')
        );

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $data
        ]);
    }

    public function getPenerimaanMutasiNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisiID = $this->request->getVar('divisi_penerima_id');

        if (empty($divisiID)) {
            $no = $this->penerimaanMutasiGlobalModel->get_no(date('m'), date('Y'), $last_day, "", $divisiID);
        } else {
            $divisi = $this->divisiModel->where('id', $divisiID)->first();
            $no = $this->penerimaanMutasiGlobalModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisiID);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
