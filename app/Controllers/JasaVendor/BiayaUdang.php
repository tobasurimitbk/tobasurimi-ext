<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BiayaUdangDetailModel;
use App\Models\BiayaUdangModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInDetailModel;
use App\Models\JasaVendorInModel;
use App\Models\VendorModel;
use App\Models\WarehousesModel;
use Dompdf\Dompdf;

class BiayaUdang extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $divisiModel;
    protected $warehouseModel;
    protected $biayaUdangModel;
    protected $biayaUdangDetailModel;
    protected $jasaVendorInModel;
    protected $jasaVendorInDetailModel;
    protected $vendorModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->warehouseModel = new WarehousesModel();
        $this->biayaUdangModel = new BiayaUdangModel();
        $this->biayaUdangDetailModel = new BiayaUdangDetailModel();
        $this->jasaVendorInModel = new JasaVendorInModel();
        $this->jasaVendorInDetailModel = new JasaVendorInDetailModel();
        $this->vendorModel = new VendorModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('jasaVendor/biayaUdang/index', $data);
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
            'biaya_udang.company_id' => $this->this_company_id,
            'biaya_udang.deletedAt' => null,
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->biayaUdangModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {

            $biayaUdangDetailModel = $this->biayaUdangDetailModel
                ->where('biaya_udang_id', $data->id)
                ->where('deletedAt', null)
                ->findAll();

            array_push($dataResult, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_pembayaran"        => $data->no_pembayaran,
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "total_item"            => count($biayaUdangDetailModel),
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
            'jasaVendorIn' => $this->biayaUdangModel->dropdownPenerimaanSuratJalan()
        ];

        return view('jasaVendor/biayaUdang/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $biayaUdang = $this->biayaUdangModel->find($id);

        if ($biayaUdang == null) {
            return redirect()->to('biaya-udang');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'biayaUdang' => $this->biayaUdangModel->find($id),
            'jasaVendorInDetail' => $this->biayaUdangModel->getPenerimaanSuratJalanDetail($id),
        ];

        return view('jasaVendor/biayaUdang/form', $data);
    }

    public function createAction()
    {
        $listBarang = json_decode($_POST['listBarang']);
        $jasaVendorIn = $this->jasaVendorInModel->find($this->request->getVar('jasa_vendor_in_id'));

        if (count($listBarang) == 0) {
            return response()->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $id = $this->biayaUdangModel->insert([
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
            $this->biayaUdangDetailModel->insert([
                'biaya_udang_id' => $id,
                'jasa_vendor_in_id' => $this->request->getVar('jasa_vendor_in_id'),
                'barang_master_id' => $b->barang_master_id,
                'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                'kg_fauzy' => $b->kg_fauzy,
                'kg_cn' => $b->kg_cn,
                'kg_daging' => $b->kg_daging,
                'tb_harga' => $b->tb_harga
            ]);
        }

        return response()->setJSON([
            'message' => "Biaya udang berhasil disimpan",
            'token' => csrf_token(),
            'id' => encrypt($id),
            'status' => true
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $listBarang = json_decode($_POST['listBarang']);

        if (count($listBarang) == 0) {
            return response()->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $this->biayaUdangModel->update($id, [
            'company_id' => $this->this_company_id,
            'keterangan' => $this->request->getVar('keterangan'),
            'status_posting' => '0'
        ]);

        // get all id detail
        $id_detail_all = [];

        foreach ($listBarang as $b) {
            $check = $this->biayaUdangDetailModel
                ->where('biaya_udang_id', $id)
                ->where('barang_master_id', $b->barang_master_id)
                ->where('barang_master_spesifikasi_id', $b->barang_master_spesifikasi_id)
                ->first();

            if ($check != null) {
                $this->biayaUdangDetailModel->update($check['id'], [
                    'biaya_udang_id' => $id,
                    'barang_master_id' => $b->barang_master_id,
                    'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                    'kg_fauzy' => $b->kg_fauzy,
                    'kg_cn' => $b->kg_cn,
                    'kg_daging' => $b->kg_daging,
                    'tb_harga' => $b->tb_harga
                ]);
                array_push($id_detail_all, $check['id']);
            } else {
                // DELETE
                $this->biayaUdangDetailModel
                    ->where('biaya_udang_id', $id)
                    ->where('barang_master_id', $b->barang_master_id)
                    ->where('barang_master_spesifikasi_id', $b->barang_master_spesifikasi_id)
                    ->delete();
                // INSERT
                $id_detail_new =  $this->biayaUdangDetailModel->insert([
                    'biaya_udang_id' => $id,
                    'barang_master_id' => $b->barang_master_id,
                    'barang_master_spesifikasi_id' => $b->barang_master_spesifikasi_id,
                    'kg_fauzy' => $b->kg_fauzy,
                    'kg_cn' => $b->kg_cn,
                    'kg_daging' => $b->kg_daging,
                    'tb_harga' => $b->tb_harga
                ]);
                array_push($id_detail_all,  $id_detail_new);
            }
        }

        return response()->setJSON([
            'message' => "Biaya udang berhasil diupdate",
            'token' => csrf_token(),
            'status' => true
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->biayaUdangModel->delete($id);
        $this->biayaUdangDetailModel->where('biaya_udang_id', $id)->delete();

        return response()->setJSON([
            'token' => csrf_token(),
            'status' => true,
            'message' => "Biaya udang berhasil dihapus"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->biayaUdangModel->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'token' => csrf_token(),
            'status' => true,
            'message' => "Biaya udang berhasil diposting"
        ]);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $biayaUdang = $this->biayaUdangModel->find($id);
        if ($biayaUdang == null) {
            return redirect()->to('biaya-udang');
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'biayaUdang' => $this->biayaUdangModel->find($id),
            'biayaUdangDetail' => $this->biayaUdangModel->dropdownBarang($biayaUdang['jasa_vendor_in_id'], $id)
        ];

        $data['vendor'] = $this->vendorModel->find($biayaUdang['vendor_id']);

        $this->dompdf->loadHtml(view('jasaVendor/biayaUdang/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Jasa Vendor Barang Masuk", array("Attachment" => false));
    }

    public function getNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $warehouseID = $this->request->getVar('warehouse_id');

        if (empty($warehouseID)) {
            $no = $this->biayaUdangModel->get_no(date('m'), date('Y'), $last_day, "", $warehouseID);
        } else {
            $warehouse = $this->warehouseModel->where('id', $warehouseID)->first();
            $no = $this->biayaUdangModel->get_no(date('m'), date('Y'), $last_day, strtoupper($warehouse['code_warehouse']), $warehouseID);
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
            $data = $this->biayaUdangModel->dropdownBarang($jasaVendorInID);
        } else {
            $id = decrypt($id);
            $data = $this->biayaUdangModel->dropdownBarang($jasaVendorInID, $id);
        }
        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
}
