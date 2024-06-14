<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\PenerimaanMutasiDetailModel;
use App\Models\PenerimaanMutasiModel;
use App\Models\PPBKBModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class PenerimaanMutasi extends BaseController
{
    protected $divisiModel;
    protected $metaDataModel;
    protected $penerimaanMutasiModel;
    protected $penerimaanMutasiDetailModel;
    protected $warehouseModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $mutasiModel;
    protected $mutasiDetailModel;
    protected $ppbkbModel;
    protected $dompdf;
    protected $this_user_id;
    protected $this_company_id;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->metaDataModel = new MetadataModel();
        $this->penerimaanMutasiModel = new PenerimaanMutasiModel();
        $this->penerimaanMutasiDetailModel = new PenerimaanMutasiDetailModel();
        $this->warehouseModel = new WarehousesModel();
        $this->mutasiModel = new MutasiModel();
        $this->mutasiDetailModel = new MutasiDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->ppbkbModel = new PPBKBModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('Warehouse/penerimaanMutasi/index', $data);
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
            "divisi_id" => $this->request->getVar("divisi_id"),
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
            'penerimaan_mutasi.company_id' => $this->this_company_id,
            'divisis.deletedAt' => null,
            'penerimaan_mutasi.deletedAt' => null
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->penerimaanMutasiModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            $listItem = $this->penerimaanMutasiDetailModel->where('penerimaan_mutasi_id', $data->id)->findAll();
            $totalItem = count($listItem);

            // CARI DIVISI PENGIRIM
            $divisiPengirimArr = $this->mutasiModel
                ->select('divisis.divisi')
                ->join('divisis', 'divisis.id = mutasi.divisi_asal_id', 'left')
                ->whereIn('mutasi.id', json_decode($data->multiple_mutasi_id))
                ->findAll();

            $divisiPengirimResultArr = [];

            foreach ($divisiPengirimArr as $d) {
                array_push($divisiPengirimResultArr, $d['divisi']);
            }
            $divisiPengirimResultArr = array_unique($divisiPengirimResultArr);

            // CARI DOKUMEN PPBKB
            $mutasiNameArr = [];
            foreach ($listItem as $l) {
                $ppbkb = $this->ppbkbModel->where('mutasi_id', $l['mutasi_id'])->first();
                array_push($mutasiNameArr, $ppbkb['no_ppbkb']);
            }
            $mutasiNameArr = array_unique($mutasiNameArr);

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_mutasi_no"  => $data->penerimaan_mutasi_no,
                "multiple_no_mutasi"    => str_replace(['"', ']', '['], " ", $data->multiple_no_mutasi),
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi_penerima"       => $data->divisi_penerima,
                "divisi_pengirim"       => implode(', ', $divisiPengirimResultArr),
                "dokumen_mutasi_barang" => "PPBKB (" . implode(', ', $mutasiNameArr) . ")",
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

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('Warehouse/penerimaanMutasi/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $penerimaanMutasi = $this->penerimaanMutasiModel->find($id);

        if ($penerimaanMutasi == null) {
            return redirect()->to('penerimaan-mutasi');
        }

        $data = [
            'penerimaanMutasi' => $penerimaanMutasi,
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('Warehouse/penerimaanMutasi/form', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $selectQry = "
            penerimaan_mutasi.*,
            divisis.divisi,
        ";

        $penerimaanMutasi = $this->penerimaanMutasiModel
            ->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->where('penerimaan_mutasi.id', $id)
            ->first();

        if ($penerimaanMutasi == null) {
            return redirect()->to('penerimaan-mutasi');
        }

        $data = [
            'penerimaanMutasi' => $penerimaanMutasi,
            'penerimaanMutasiDetail' => $this->penerimaanMutasiModel->getListBarangMutasi(
                json_decode($penerimaanMutasi->multiple_mutasi_id),
                $id
            )
        ];

        $this->dompdf->loadHtml(view('Warehouse/penerimaanMutasi/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Penerimaan Mutasi", array("Attachment" => false));
    }

    public function createAction()
    {
        $mutasiNo = $this->penerimaanMutasiModel->getMutasiNo($this->request->getVar('multiple_mutasi_id'));
        $barang = json_decode($_POST['listBarang']);
        $qty_diterima_current = 0;

        $first = $this->penerimaanMutasiModel
            ->where('company_id', $this->this_company_id)
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

        $id = $this->penerimaanMutasiModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
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
                $this->penerimaanMutasiDetailModel->insert([
                    'penerimaan_mutasi_id' => $id,
                    'mutasi_id' => $b->mutasi_id,
                    'mutasi_detail_id' => $b->mutasi_detail_id,
                    'stock_asal_id' => $b->stock_asal_id,
                    'bc_asal_id' => $b->bc_asal_id,
                    'no_aju_asal' => $b->no_aju_asal,
                    'stock_dokumen_asal' => $b->stock_dokumen_asal,
                    'bc_mutasi_id' => $b->bc_mutasi_id,
                    'no_aju_mutasi' => $b->no_aju_mutasi,
                    'qty' => $b->qty_diterima_current
                ]);
            }
        }

        return response()->setJSON([
            'message' => "Penerimaan mutasi berhasil disimpan",
            'id' => encrypt($id),
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $barang = json_decode($_POST['listBarang']);
        $qty_diterima_current = 0;

        $first = $this->penerimaanMutasiModel
            ->where('company_id', $this->this_company_id)
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

        // PMU/PTS/01/VI/2024

        $this->penerimaanMutasiModel->update($id, [
            'company_id' => $this->this_company_id,
            // 'divisi_id' => $this->request->getVar('divisi_id'),
            // 'penerimaan_mutasi_no' => $this->request->getVar('penerimaan_mutasi_no'),
            // 'multiple_mutasi_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_mutasi_id'))),
            // 'multiple_no_mutasi' => str_replace(['\\"', '\\'], '', json_encode($mutasiNo)),
            'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0',
            'createdBy' => $this->this_user_id
        ]);

        $id_not_delete = [];
        foreach ($barang as $b) {
            if ($b->qty_diterima_current != 0) {
                // update or insert
                $check = $this->penerimaanMutasiDetailModel
                    ->where('penerimaan_mutasi_id', $id)
                    ->where('mutasi_id', $b->mutasi_id)
                    ->where('mutasi_detail_id', $b->mutasi_detail_id)
                    ->first();

                if ($check == null) {
                    // insert
                    $id = $this->penerimaanMutasiDetailModel->insert([
                        'penerimaan_mutasi_id' => $id,
                        'mutasi_id' => $b->mutasi_id,
                        'mutasi_detail_id' => $b->mutasi_detail_id,
                        'stock_asal_id' => $b->stock_asal_id,
                        'bc_asal_id' => $b->bc_asal_id,
                        'no_aju_asal' => $b->no_aju_asal,
                        'stock_dokumen_asal' => $b->stock_dokumen_asal,
                        'bc_mutasi_id' => $b->bc_mutasi_id,
                        'no_aju_mutasi' => $b->no_aju_mutasi,
                        'qty' => $b->qty_diterima_current
                    ]);
                    array_push($id_not_delete, $id);
                } else {
                    // update
                    $this->penerimaanMutasiDetailModel->update($check['id'], [
                        'penerimaan_mutasi_id' => $id,
                        'mutasi_id' => $b->mutasi_id,
                        'mutasi_detail_id' => $b->mutasi_detail_id,
                        'stock_asal_id' => $b->stock_asal_id,
                        'stock_dokumen_asal' => $b->stock_dokumen_asal,
                        'bc_asal_id' => $b->bc_asal_id,
                        'no_aju_asal' => $b->no_aju_asal,
                        'bc_mutasi_id' => $b->bc_mutasi_id,
                        'no_aju_mutasi' => $b->no_aju_mutasi,
                        'qty' => $b->qty_diterima_current
                    ]);
                    array_push($id_not_delete, $check['id']);
                }
            } else {
                // delete
                $this->penerimaanMutasiDetailModel
                    ->where('penerimaan_mutasi_id', $id)
                    ->where('mutasi_id', $b->id)
                    ->where('mutasi_detail_id', $b->mutasi_detail_id)
                    ->delete();
            }
        }

        $this->penerimaanMutasiDetailModel->whereNotIn('id', $id_not_delete)->where('penerimaan_mutasi_id', $id)->delete();

        return response()->setJSON([
            'message' => "Penerimaan mutasi berhasil diupdate",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->penerimaanMutasiModel->delete($id);
        $this->penerimaanMutasiDetailModel->where('penerimaan_mutasi_id', $id)->delete();

        return response()->setJSON([
            'message' => "Penerimaan mutasi berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        // Insert To Inventori (-)
        $penerimaanMutasi = $this->penerimaanMutasiModel->find($id);
        $penerimaanMutasiList = $this->penerimaanMutasiDetailModel->where('penerimaan_mutasi_id', $penerimaanMutasi['id'])->where('deletedAt', null)->findAll();
        // Inventori Stok Minus
        foreach ($penerimaanMutasiList as $p) {
            $mutasi = $this->mutasiModel->find($p['mutasi_id']);
            $stockMutasiAsal = $this->stockModel->find($p['stock_asal_id']);

            if ($stockMutasiAsal['tipe_barang'] == "kemasan") {
                $barang2Id = $stockMutasiAsal['kemasan_id'];
            } else {
                $barang2Id = $stockMutasiAsal['barang2_id'];
            }

            // INIT STOK NYA (KARENA BARANG NYA BISA AJA TIDAK ADA DI INVENTORI)
            $stok = $this->stockModel->getStokMaster(
                $this->this_company_id,
                $mutasi['warehouse_tujuan_id'],
                $mutasi['divisi_tujuan_id'],
                $stockMutasiAsal['tipe_barang'],
                $stockMutasiAsal['barang1_id'],
                $barang2Id
            );

            if ($stok == null) {
                $stok = $this->stockModel->insertStok(
                    $this->this_company_id,
                    $mutasi['warehouse_tujuan_id'],
                    $mutasi['divisi_tujuan_id'],
                    $stockMutasiAsal['tipe_barang'],
                    $stockMutasiAsal['barang1_id'],
                    $barang2Id,
                    0
                );
            }

            // INSERT LEVEL 1
            $stok = $this->stockModel->insertStok(
                $this->this_company_id,
                $mutasi['warehouse_tujuan_id'],
                $mutasi['divisi_tujuan_id'],
                $stockMutasiAsal['tipe_barang'],
                $stockMutasiAsal['barang1_id'],
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
                $penerimaanMutasi['penerimaan_mutasi_no'],
                "-",
            );

            // STOK OLD 
            $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
                $p['stock_asal_id'],
                $p['bc_asal_id'],
                $p['no_aju_asal'],
                $p['stock_dokumen_asal']
            );

            // INSERT LEVEL 3 
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_mutasi_id'],
                $stok,
                $stokDetail,
                $p['qty'],
                $p['no_aju_mutasi'],
                $mutasi['no_mutasi'],
                $mutasi['no_mutasi'] . " (" . $stockOldDetail['no_po'] . ") ",
                $stockOldDetail['supplier_id'],
                $stockOldDetail['harga_umum'],
                $stockOldDetail['harga_harian'],
                $stockOldDetail['harga_bulanan'],
                $stockOldDetail['no_po']
            );
        }

        $this->penerimaanMutasiModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'message' => "Penerimaan Mutasi berhasil diposting",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownListDivisi()
    {
        $jenisMutasi = $this->request->getVar('jenis_mutasi');
        $bcID = $this->metaDataModel->where('name', "jenis_dok_aju")->where('value', $jenisMutasi)->first();
        $divisi = $this->divisiModel->getDivisiAccess();

        $divisiArr = array();
        foreach ($divisi as $d) {
            array_push($divisiArr, $d['id']);
        }

        $data = $this->penerimaanMutasiModel->getListWarehouse(
            $bcID['id'],
            $divisiArr
        );

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $data,
            'status' => true
        ]);
    }

    public function dropdownListNomorMutasi()
    {
        $divisiId = $this->request->getVar('divisi_id');
        $data = $this->penerimaanMutasiModel->getListNomorMutasi(
            $divisiId
        );

        return response()->setJSON([
            'token' => csrf_hash(),
            'data' => $data,
            'status' => true
        ]);
    }

    public function dropdownListBarang()
    {
        $mutasiID = json_decode($this->request->getVar('mutasi_id'));
        $penerimaanMutasiID = decrypt($this->request->getVar('penerimaan_mutasi_id'));

        if (empty($mutasiID)) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => [],
                'status' => true
            ]);
        } else {

            $data = $this->penerimaanMutasiModel->getListBarangMutasi(
                $mutasiID,
                $penerimaanMutasiID
            );

            return response()->setJSON([
                'token' => csrf_hash(),
                'data' => $data,
                'status' => true
            ]);
        }
    }

    public function getPenerimaanMutasiNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisiID = $this->request->getVar('divisi_id');

        if (empty($divisiID)) {
            $no = $this->penerimaanMutasiModel->get_no(date('m'), date('Y'), $last_day, "", $divisiID);
        } else {
            $divisi = $this->divisiModel->where('id', $divisiID)->first();
            $no = $this->penerimaanMutasiModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisiID);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }
}
