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
use App\Models\StockRevampDetailModel;
use App\Models\StockRevampModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;
use Exception;

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
    protected $stockRevampModel;
    protected $dompdf;
    protected $this_user_id;
    protected $this_company_id;
    protected $stockRevampDetailModel;

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
        $this->stockRevampModel = new StockRevampModel();
        $this->dompdf = new Dompdf();
        $this->stockRevampDetailModel = new StockRevampDetailModel();
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
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");

        $condition = [
            'penerimaan_mutasi.company_id' => $this->this_company_id,
            'penerimaan_mutasi.tipe_mutasi' => "PPBKB",
            'penerimaan_mutasi.deletedAt' => null
        ];

        $dataQry = $this->penerimaanMutasiModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        $dataResult = array();

        // Get all data ppbkb
        $ppbkbAll = $this->ppbkbModel->where('company_id', $this->this_company_id)->findAll();
        // Buat map mutasi_id => no_ppbkb
        $ppbkbMap = [];
        foreach ($ppbkbAll as $ppbkb) {
            $ppbkbMap[$ppbkb['mutasi_id']][] = $ppbkb['no_ppbkb'];
        }

        foreach ($dataQry['data'] as $data) {

            // decode JSON array dari multiple_mutasi_id
            $multipleMutasiIdArr = json_decode($data->multiple_mutasi_id);

            $noPpbkbArr = [];
            if (is_array($multipleMutasiIdArr)) {
                foreach ($multipleMutasiIdArr as $mutasiId) {
                    if (isset($ppbkbMap[$mutasiId])) {
                        // ambil semua no_ppbkb yang terkait mutasi_id ini
                        $noPpbkbArr = array_merge($noPpbkbArr, $ppbkbMap[$mutasiId]);
                    }
                }
            }

            // gabungkan menjadi string, pisah koma
            $noPpbkb = implode(', ', $noPpbkbArr);

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "penerimaan_mutasi_no"  => $data->penerimaan_mutasi_no,
                "divisi"       => $data->divisi,
                "multiple_no_mutasi"    => str_replace(['"', ']', '[', "'"], " ", $data->multiple_no_mutasi),
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "status_posting"        => $data->status_posting,
                "no_ppbkb"              => $noPpbkb
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

        $penerimaanMutasiDetail = $this->penerimaanMutasiModel->getListBarangMutasi(
            json_decode($penerimaanMutasi['multiple_mutasi_id']),
            $id,
            true
        );


        $data = [
            'penerimaanMutasiDetail' => $penerimaanMutasiDetail,
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
                $id,
                true
            )
        ];

        $this->dompdf->loadHtml(view('Warehouse/penerimaanMutasi/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Penerimaan Mutasi", array("Attachment" => false));
    }

    public function createAction()
    {
        // return response()->setJSON([
        //     '$_POST' => $_POST,
        //     'status' => false,
        //     'listBarang' => json_decode($_POST['listBarang'])
        // ]);

        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $multipleMutasiId = $this->request->getVar('multiple_mutasi_id');
            $penerimaanMutasiNo = $this->request->getVar('penerimaan_mutasi_no');
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $tipeMutasi = $this->request->getVar('tipe_mutasi');

            if (count($multipleMutasiId) == 0) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "data list nomor mutasi tidak ditemukan"
                ]);
            }

            $firstNumber = $this->penerimaanMutasiModel
                ->where('company_id', $this->this_company_id)
                ->where('penerimaan_mutasi_no', $penerimaanMutasiNo)
                ->where('deletedAt', null)
                ->first();

            if ($firstNumber != null) {
                $penerimaanMutasiNo = $this->get_no_str(
                    $tanggal,
                    $tipeMutasi
                );
            }

            $multipleMutasiNo = array();
            foreach (json_decode($_POST['listBarang']) as $l) {
                array_push($multipleMutasiNo, $l->no_mutasi);
            }

            $multipleMutasiId = array_values(array_unique($multipleMutasiId));
            $multipleMutasiNo = array_values(array_unique($multipleMutasiNo));

            $multipleMutasiIdStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleMutasiId));
            $multipleMutasiNoStr = json_encode($multipleMutasiNo, JSON_UNESCAPED_SLASHES);

            $id = $this->penerimaanMutasiModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'tipe_mutasi' => $tipeMutasi,
                'penerimaan_mutasi_no' => $penerimaanMutasiNo,
                'multiple_mutasi_id' => $multipleMutasiIdStr,
                'multiple_no_mutasi' => $multipleMutasiNoStr,
                'tanggal' =>  $tanggal,
                'keterangan' => $this->request->getVar('keterangan'),
                'status_posting' => '0',
                'createdBy' => $this->this_user_id
            ]);

            foreach (json_decode($_POST['listBarang']) as $l) {
                $this->penerimaanMutasiDetailModel->insert([
                    'penerimaan_mutasi_id' => $id,
                    'mutasi_id' => $l->mutasi_id,
                    'mutasi_detail_id' => $l->mutasi_detail_id,
                    'stock_detail_id' => null,
                    'qty' => $l->qty_diterima_sekarang
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Data tersimpan"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateAction()
    {
        $db = \Config\Database::connect();
        try {
            $db->transBegin();
            $id = decrypt($this->request->getVar('id'));
            $multipleMutasiId = $this->request->getVar('multiple_mutasi_id');
            $penerimaanMutasiNo = $this->request->getVar('penerimaan_mutasi_no');
            $tanggal = formatDMYtoYMD($this->request->getVar('tanggal'));
            $tipeMutasi = $this->request->getVar('tipe_mutasi');

            if (count($multipleMutasiId) == 0) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "data list nomor mutasi tidak ditemukan"
                ]);
            }

            $firstNumber = $this->penerimaanMutasiModel
                ->where('company_id', $this->this_company_id)
                ->where('penerimaan_mutasi_no', $penerimaanMutasiNo)
                ->where('id !=', $id)
                ->where('deletedAt', null)
                ->first();

            if ($firstNumber != null) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "nomor penerimaan sudah ada"
                ]);
            }

            $multipleMutasiNo = array();
            foreach (json_decode($_POST['listBarang']) as $l) {
                array_push($multipleMutasiNo, $l->no_mutasi);
            }

            $multipleMutasiId = array_values(array_unique($multipleMutasiId));
            $multipleMutasiNo = array_values(array_unique($multipleMutasiNo));

            $multipleMutasiIdStr = str_replace(['\\"', '\\', '"'], '', json_encode($multipleMutasiId));
            $multipleMutasiNoStr = json_encode($multipleMutasiNo, JSON_UNESCAPED_SLASHES);

            $this->penerimaanMutasiModel->update($id, [
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'tipe_mutasi' => $tipeMutasi,
                'penerimaan_mutasi_no' => $penerimaanMutasiNo,
                'multiple_mutasi_id' => $multipleMutasiIdStr,
                'multiple_no_mutasi' => $multipleMutasiNoStr,
                'tanggal' =>  $tanggal,
                'keterangan' => $this->request->getVar('keterangan'),
                'status_posting' => '0',
            ]);

            $this->penerimaanMutasiDetailModel->where('penerimaan_mutasi_id', $id)->delete();

            foreach (json_decode($_POST['listBarang']) as $l) {
                $this->penerimaanMutasiDetailModel->insert([
                    'penerimaan_mutasi_id' => $id,
                    'mutasi_id' => $l->mutasi_id,
                    'mutasi_detail_id' => $l->mutasi_detail_id,
                    'stock_detail_id' => null,
                    'qty' => $l->qty_diterima_sekarang
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Data terupdate"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
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
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $this->penerimaanMutasiModel->posting($id, $db);
            $this->penerimaanMutasiModel->update($id, ['status_posting' => '1']);
            $db->transCommit();
            return response()->setJSON([
                'message' => "Penerimaan Mutasi berhasil diposting",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unposting()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $id = decrypt($this->request->getVar('id'));
            $status = $this->penerimaanMutasiModel->unposting($id);
            if (!$status) {
                $db->transRollback();
                return response()->setJSON([
                    'message' => "Gagal unposting : stock sudah digunakan",
                    'status' => true,
                    'token' => csrf_hash()
                ]);
            }
            $this->penerimaanMutasiModel->update($id, ['status_posting' => '0']);
            $db->transCommit();
            return response()->setJSON([
                'message' => "Penerimaan Mutasi berhasil diunposting",
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
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
        $tipeMutasi = $this->request->getVar('tipe_mutasi');

        $data = $this->penerimaanMutasiModel->getListNomorMutasi(
            $divisiId,
            $tipeMutasi
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
        $tanggal = $this->request->getVar('tanggal');
        $tipe_mutasi = $this->request->getVar('tipe_mutasi');

        if (empty($tanggal)) {
            return response()->setJSON([
                'status' => true,
                'data' => '',
                'token' => csrf_hash()
            ]);
        }

        $tanggal = formatDMYtoYMD($tanggal); // 2025-09-21
        $tanggalParts = explode('-', $tanggal);
        if (count($tanggalParts) !== 3) {
            return $this->response->setJSON([
                'data' => '',
                'status' => false,
                'message' => 'Format tanggal tidak valid. Gunakan dd/mm/yyyy.'
            ]);
        }

        $no = $this->get_no_str($tanggal, $tipe_mutasi);
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    public function get_no_str($tanggal, $tipe_mutasi)
    {
        $tanggalParts = explode('-', $tanggal);
        $month = $tanggalParts[1];
        $year = $tanggalParts[0];

        $no = $this->penerimaanMutasiModel->get_no(
            $month,
            $year,
            $this->this_company_id,
            $tipe_mutasi
        );

        return $no;
    }
}
