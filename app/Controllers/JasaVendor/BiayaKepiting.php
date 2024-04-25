<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BiayaKepitingDetailModel;
use App\Models\BiayaKepitingGajiModel;
use App\Models\BiayaKepitingModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\JasaVendorInModel;
use App\Models\MetadataModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class BiayaKepiting extends BaseController
{

    protected $this_company_id;
    protected $this_user_id;
    protected $divisiModel;
    protected $warehouseModel;
    protected $biayaKepitingModel;
    protected $biayaKepitingDetailModel;
    protected $biayaKepitingGajiModel;
    protected $jasaVendorInModel;
    protected $jasaVendorInDetailModel;
    protected $vendorModel;
    protected $metaDataModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->warehouseModel = new WarehousesModel();
        $this->biayaKepitingModel = new BiayaKepitingModel();
        $this->biayaKepitingDetailModel = new BiayaKepitingDetailModel();
        $this->biayaKepitingGajiModel = new BiayaKepitingGajiModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $this->vendorModel = new VendorModel();
        $this->metaDataModel = new MetadataModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
        ];

        return view('jasaVendor/biayaKepiting/index', $data);
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
            "no_pembayaran" => $this->request->getVar("no_pembayaran"),
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'biaya_kepiting.company_id' => $this->this_company_id,
            'biaya_kepiting.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->biayaKepitingModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $biayaKepitingDetailModel = $this->biayaKepitingDetailModel
                ->where('biaya_kepiting_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_pembayaran"        => $data->no_pembayaran,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($biayaKepitingDetailModel),
                "vendor_name"           => $data->vendor_name,
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
            'jasaVendorIn' => $this->biayaKepitingModel->dropdownJasaVendorIn(),
        ];

        return view('jasaVendor/biayaKepiting/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $biayaKepiting = $this->biayaKepitingModel->find($id);

        if ($biayaKepiting == null) {
            return redirect()->to('biaya-kepiting');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'biayaKepiting' => $this->biayaKepitingModel->find($id),
            'jasaVendorInDetail' => $this->biayaKepitingModel->getPenerimaanSuratJalanDetail($id),
        ];

        return view('jasaVendor/biayaKepiting/form', $data);
    }


    public function createAction()
    {
        $listBarang = json_decode($_POST['listBarang']);
        $listPerolehanGaji = json_decode($_POST['listPerolehanGaji']);

        $jasaVendorIn = $this->jasaVendorInModel->find($this->request->getVar('jasa_vendor_in_id'));

        if (count($listBarang) == 0) {
            return response()->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $id = $this->biayaKepitingModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $jasaVendorIn['divisi_id'],
            'jasa_vendor_in_id' => $jasaVendorIn['id'],
            'vendor_id' => $jasaVendorIn['vendor_id'],
            'warehouse_id' => $jasaVendorIn['warehouse_id'],
            'no_pembayaran' => $this->request->getVar('no_pembayaran'),
            "tanggal" => $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "",
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0'
        ]);

        foreach ($listBarang as $b) {
            $this->biayaKepitingDetailModel->insert([
                'biaya_kepiting_id' => $id,
                'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
                'barang_master_id' => $b->barang_master_id,
                'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                'jumbo' => $b->jumbo,
                'ex_lump' => $b->ex_lump,
                'lump' => $b->lump,
                'special' => $b->special,
                'claw' => $b->claw,
                'mh' => $b->mh,
                'cf' => $b->cf
            ]);
        }

        foreach ($listPerolehanGaji as $b) {
            $this->biayaKepitingGajiModel->insert([
                'biaya_kepiting_id' => $id,
                'jumbo' => $b->jumbo,
                'ex_lump' => $b->ex_lump,
                'lump' => $b->lump,
                'special' => $b->special,
                'claw' => $b->claw,
                'mh' => $b->mh,
                'cf' => $b->cf,
                'jenis' => $b->description
            ]);
        }

        return response()->setJSON([
            'message' => "Biaya kepiting berhasil disimpan",
            'token' => csrf_token(),
            'id' => encrypt($id),
            'status' => true
        ]);
    }

    public function updateAction()
    {
        $listBarang = json_decode($_POST['listBarang']);
        $listPerolehanGaji = json_decode($_POST['listPerolehanGaji']);
        $id = decrypt($this->request->getVar('id'));

        if (count($listBarang) == 0) {
            return response()->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $this->biayaKepitingModel->update($id, [
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0'
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach ($listBarang as $b) {
            $check = $this->biayaKepitingDetailModel
                ->where('biaya_kepiting_id', $id)
                ->where('barang_master_id', $b->barang_master_id)
                ->where('barang_master_spesifikasi_id', $b->barang_master_spesifikasi_id)
                ->first();

            if ($check != null) {
                $this->biayaKepitingDetailModel->update($check['id'], [
                    'biaya_kepiting_id' => $id,
                    'barang_master_id' => $b->barang_master_id,
                    'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                    'jumbo' => $b->jumbo,
                    'ex_lump' => $b->ex_lump,
                    'lump' => $b->lump,
                    'special' => $b->special,
                    'claw' => $b->claw,
                    'mh' => $b->mh,
                    'cf' => $b->cf
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // DELETE
                $this->biayaKepitingDetailModel
                    ->where('biaya_kepiting_id', $id)
                    ->where('barang_master_id', $b->barang_master_id)
                    ->where('barang_master_spesifikasi_id', $b->barang_master_spesifikasi_id)
                    ->delete();
                // INSERT
                $id_detail_new =  $this->biayaKepitingDetailModel->insert([
                    'biaya_kepiting_id' => $id,
                    'barang_master_id' => $b->barang_master_id,
                    'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                    'jumbo' => $b->jumbo,
                    'ex_lump' => $b->ex_lump,
                    'lump' => $b->lump,
                    'special' => $b->special,
                    'claw' => $b->claw,
                    'mh' => $b->mh,
                    'cf' => $b->cf
                ]);
                array_push($id_detail_all,  $id_detail_new);
            }
        }

        $this->biayaKepitingDetailModel->where('biaya_kepiting_id', $id)->whereNotIn('id', $id_detail_all)->delete();

        foreach ($listPerolehanGaji as $b) {
            $check = $this->biayaKepitingGajiModel->where('biaya_kepiting_id', $id)->where('jenis', $b->description)->first();
            $this->biayaKepitingGajiModel->update($check['id'], [
                'biaya_kepiting_id' => $id,
                'jumbo' => $b->jumbo,
                'ex_lump' => $b->ex_lump,
                'lump' => $b->lump,
                'special' => $b->special,
                'claw' => $b->claw,
                'mh' => $b->mh,
                'cf' => $b->cf,
                'jenis' => $b->description
            ]);
        }

        return response()->setJSON([
            'message' => "Biaya kepiting berhasil diupdate",
            'token' => csrf_token(),
            'id' => encrypt($id),
            'status' => true
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->biayaKepitingModel->delete($id);
        $this->biayaKepitingDetailModel->where('biaya_kepiting_id', $id)->delete();
        $this->biayaKepitingGajiModel->where('biaya_kepiting_id', $id)->delete();

        return response()->setJSON([
            'token' => csrf_token(),
            'status' => true,
            'message' => "Biaya kepiting berhasil dihapus"
        ]);
    }


    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->biayaKepitingModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'token' => csrf_token(),
            'status' => true,
            'message' => "Biaya kepiting berhasil diposting"
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $biayaKepiting = $this->biayaKepitingModel->find($id);
        if ($biayaKepiting == null) {
            return redirect()->to('biaya-kepiting');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'biayaKepiting' => $this->biayaKepitingModel->find($id),
            'biayaKepitingDetail' => $this->biayaKepitingModel->dropdownBarang($biayaKepiting['jasa_vendor_in_id'], $id),
            'dataPerolehanGaji' => $this->biayaKepitingModel->dropdownPerolehanGaji($id)
        ];

        $data['vendor'] = $this->vendorModel->find($biayaKepiting['vendor_id']);
        $data['gajiBiayaKepiting'] = $this->biayaKepitingGajiModel->where('biaya_kepiting_id', $biayaKepiting['id'])->findAll();

        $this->dompdf->loadHtml(view('jasaVendor/biayaKepiting/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Biaya Kepiting", array("Attachment" => false));
    }

    public function getNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouseID = $this->request->getVar('warehouse_id');

        if (empty($warehouseID)) {
            $no = $this->biayaKepitingModel->get_no(date('m'), date('Y'), $last_day, "", $warehouseID);
        } else {
            $warehouse = $this->warehouseModel->where('id', $warehouseID)->first();
            $no = $this->biayaKepitingModel->get_no(date('m'), date('Y'), $last_day, strtoupper($warehouse['code_warehouse']), $warehouseID);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownBarang()
    {
        $jasaVendorInID = $this->request->getVar('jasa_vendor_in_id');
        $id = $this->request->getVar('id');
        if (empty($id)) {
            $data = $this->biayaKepitingModel->dropdownBarang($jasaVendorInID);
            $dataPerolehanGaji = $this->biayaKepitingModel->dropdownPerolehanGaji();
        } else {
            $id = decrypt($id);
            $data = $this->biayaKepitingModel->dropdownBarang($jasaVendorInID, $id);
            $dataPerolehanGaji = $this->biayaKepitingModel->dropdownPerolehanGaji($id);
        }
        return response()->setJSON([
            'data' => $data,
            'dataPerolehanGaji' => $dataPerolehanGaji,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
}
