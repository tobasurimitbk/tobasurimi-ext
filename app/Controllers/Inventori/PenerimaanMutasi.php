<?php

namespace App\Controllers\Inventori;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\MutasiDetailModel;
use App\Models\MutasiModel;
use App\Models\PenerimaanMutasiDetailModel;
use App\Models\PenerimaanMutasiModel;
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
            'warehouses.deletedAt' => null,
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

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_mutasi_no"  => $data->penerimaan_mutasi_no,
                "multiple_no_mutasi"    => str_replace(['"', ']', '['], "", $data->multiple_no_mutasi),
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "warehouse_tujuan"      => $data->divisi_tujuan . ' - ' . $data->warehouse_tujuan,
                "jenis_mutasi"          => $data->jenis_mutasi,
                "bc_no"                 => $data->bc_no == "" ? "-" : $data->bc_no,
                "total_item"            => $totalItem,
                "state"                 => '0',
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
            'jenisMutasi' => $this->metaDataModel->where('name', "Jenis Mutasi")->findAll(),
            'warehouse' => $this->warehouseModel->select('warehouses.*, divisis.divisi')
                ->join('divisis', 'divisis.id = warehouses.divisi_id')
                ->where('warehouses.id', $penerimaanMutasi['warehouse_id'])
                ->first(),
        ];

        return view('Warehouse/penerimaanMutasi/form', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $selectQry = "
            penerimaan_mutasi.*,
            divisis.divisi,
            warehouses.warehouse_name
        ";

        $penerimaanMutasi = $this->penerimaanMutasiModel
            ->asObject()
            ->select($selectQry)
            ->join('divisis', 'divisis.id = penerimaan_mutasi.divisi_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_mutasi.warehouse_id', 'left')
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
        $warehouse = $this->warehouseModel->find($this->request->getVar('warehouse_id'));
        $jenisMutasi = $this->request->getVar('jenis_mutasi');
        $mutasiNo = $this->penerimaanMutasiModel->getMutasiNo($this->request->getVar('multiple_mutasi_id'));
        $bc = $this->metaDataModel->where('name', "jenis_dok_aju")->where('value', $jenisMutasi)->first();
        $barang = json_decode($_POST['listBarang']);
        $qty_diterima_current = 0;

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
            'divisi_id' => $warehouse['divisi_id'],
            'warehouse_id' => $warehouse['id'],
            'bc_id' => $bc['id'],
            'bc_no' => "",
            'penerimaan_mutasi_no' => $this->request->getVar('penerimaan_mutasi_no'),
            'jenis_mutasi' => $jenisMutasi,
            'multiple_mutasi_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_mutasi_id'))),
            'multiple_no_mutasi' => str_replace(['\\"', '\\'], '', json_encode($mutasiNo)),
            'tanggal' => date('Y-m-d'),
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
                    'stock_id' => $b->stock_id,
                    'bc_id' => $b->bc_id,
                    'no_aju' => $b->no_aju,
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
        $warehouse = $this->warehouseModel->find($this->request->getVar('warehouse_id'));
        $jenisMutasi = $this->request->getVar('jenis_mutasi');
        $mutasiNo = $this->penerimaanMutasiModel->getMutasiNo($this->request->getVar('multiple_po_id'));
        $bc = $this->metaDataModel->where('name', "jenis_dok_aju")->where('value', $jenisMutasi)->first();
        $barang = json_decode($_POST['listBarang']);
        $qty_diterima_current = 0;

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

        $this->penerimaanMutasiModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $warehouse['divisi_id'],
            'warehouse_id' => $warehouse['id'],
            'bc_id' => $bc['id'],
            'bc_no' => "",
            'jenis_mutasi' => $jenisMutasi,
            'multiple_mutasi_id' => str_replace(['\\"', '\\', '"'], '', json_encode($this->request->getVar('multiple_po_id'))),
            'multiple_no_mutasi' => str_replace(['\\"', '\\'], '', json_encode($mutasiNo)),
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0',
            'createdBy' => $this->this_user_id
        ]);

        foreach ($barang as $b) {
            if ($b->qty_diterima_current != 0) {
                // update or insert
                $check = $this->penerimaanMutasiDetailModel
                    ->where('penerimaan_mutasi_id', $id)
                    ->where('mutasi_id', $b->id)
                    ->where('mutasi_detail_id', $b->mutasi_detail_id)
                    ->first();

                if ($check == null) {
                    // insert
                    $this->penerimaanMutasiDetailModel->insert([
                        'penerimaan_mutasi_id' => $id,
                        'mutasi_id' => $b->mutasi_id,
                        'mutasi_detail_id' => $b->mutasi_detail_id,
                        'stock_id' => $b->stock_id,
                        'bc_id' => $b->bc_id,
                        'no_aju' => $b->no_aju,
                        'qty' => $b->qty_diterima_current
                    ]);
                } else {
                    // update
                    $this->penerimaanMutasiDetailModel->update($check['id'], [
                        'penerimaan_mutasi_id' => $id,
                        'mutasi_id' => $b->mutasi_id,
                        'mutasi_detail_id' => $b->mutasi_detail_id,
                        'stock_id' => $b->stock_id,
                        'bc_id' => $b->bc_id,
                        'no_aju' => $b->no_aju,
                        'qty' => $b->qty_diterima_current
                    ]);
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
            $stock = $this->stockModel->find($p['stock_id']);
            $qty = $p['qty'];

            if ($stock['tipe_barang'] == "kemasan") {
                $barang2_id = $stock['kemasan_id'];
            } else {
                $barang2_id = $stock['barang2_id'];
            }

            $stok = $this->stockModel->insertStok(
                $mutasi['company_id'],
                $mutasi['warehouse_asal_id'],
                $mutasi['divisi_asal_id'],
                $stock['tipe_barang'],
                $stock['barang1_id'],
                $barang2_id,
                ($qty * -1),
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                "Out",
                date('Y-m-d'),
                $this->this_user_id,
                "MUTASI",
                $penerimaanMutasi['penerimaan_mutasi_no'],
                $penerimaanMutasi['keterangan'],
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $p['bc_id'],
                $stok,
                $stokDetail,
                $qty,
                $p['no_aju'],
                $mutasi['no_mutasi']
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
