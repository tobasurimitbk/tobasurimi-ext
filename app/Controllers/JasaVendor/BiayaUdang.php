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
use Exception;

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
                "multiple_jasa_vendor_in_no"        => str_replace(['"', ']', '['], "",  $data->multiple_jasa_vendor_in_no),
                "tanggal"               => date('d/m/Y', strtotime($data->tanggal)),
                "divisi"                => $data->divisi,
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
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
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
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'vendor' => $this->vendorModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('name', "ASC")->findAll(),
        ];

        return view('jasaVendor/biayaUdang/form', $data);
    }

    public function createAction()
    {
        $listBarang = json_decode($this->request->getPost('listBarang'), true);
        
        $jasaVendorInNo = $this->biayaUdangModel->getJasaVendorInNo(
            $this->request->getPost('multiple_jasa_vendor_in_id')
        );

        if (empty($listBarang)) {
            return $this->response->setJSON([
                'message' => "Barang tidak boleh kosong",
                'status' => false,
                'token' => csrf_hash()
            ]);
        }

        $check = $this->biayaUdangModel
            ->where('company_id', $this->this_company_id)
            ->where('no_pembayaran', $this->request->getPost('no_pembayaran'))
            ->first();

        if ($check != null) {
            return $this->response->setJSON([
                'status' => false,
                'message' => "Nomor pembayaran sudah ada",
                'token' => csrf_hash()
            ]);
        }

        try {
            $id = $this->biayaUdangModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getPost('divisi_id'),
                'multiple_jasa_vendor_in_id' => json_encode($this->request->getPost('multiple_jasa_vendor_in_id')),
                'multiple_jasa_vendor_in_no' => json_encode($jasaVendorInNo),
                'vendor_id' => $this->request->getPost('vendor_id'),
                'no_pembayaran' => $this->request->getPost('no_pembayaran'),
                "tanggal" => $this->request->getPost("tanggal") ? 
                    date("Y-m-d", strtotime(str_replace('/', '-', $this->request->getPost("tanggal")))) : 
                    date("Y-m-d"),
                'keterangan' => $this->request->getPost('keterangan'),
                'status_posting' => '0'
            ]);

            if (!$id) {
                throw new Exception('Gagal menyimpan data biaya udang');
            }

            foreach ($listBarang as $b) {
                $parentData = $b['parent'];
                
                // Hitung total harga berdasarkan harga_per_kilo dan sum_bersih
                $total_harga = ($parentData['sum_bersih'] ?? 0) * ($parentData['harga_per_kilo'] ?? 0);
                
                $this->biayaUdangDetailModel->insert([
                    'biaya_udang_id' => $id,
                    'jasa_vendor_in_id' => $parentData['id'],
                    'tanggal_po' => $parentData['tanggal_po'],
                    'kg_cn' => $parentData['sum_bersih'] ?? 0,
                    'kg_daging' => $parentData['sum_bersih'] ?? 0,
                    'harga_per_kilo' => $parentData['harga_per_kilo'] ?? 0, // Simpan harga_per_kilo
                    'total_harga' => $total_harga // Simpan total_harga yang dihitung
                ]);
            }

            return $this->response->setJSON([
                'message' => "Biaya udang berhasil disimpan",
                'token' => csrf_hash(),
                'id' => encrypt($id),
                'status' => true
            ]);

        } catch (Exception $e) {
            return $this->response->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_hash()
            ]);
        }
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
                    'tanggal_po' => $b->tanggal_po,
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
                    'tanggal_po' => $b->tanggal_po,
                    'kg_fauzy' => $b->kg_fauzy,
                    'kg_cn' => $b->kg_cn,
                    'kg_daging' => $b->kg_daging,
                    'tb_harga' => $b->tb_harga
                ]);
                array_push($id_detail_all,  $id_detail_new);
            }
        }

        $this->biayaUdangDetailModel->where('biaya_udang_id', $id)->whereNotIn('id', $id_detail_all)->delete();

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

        // Get data untuk print
        $biayaUdangDetail = $this->biayaUdangModel->dropdownBarangPrint(json_decode($biayaUdang['multiple_jasa_vendor_in_id']), $id);
        $biayaUdangTotal = $this->biayaUdangModel->getBarangDetail(json_decode($biayaUdang['multiple_jasa_vendor_in_id']), $id);

        // Update parent data dengan data dari biayaUdangTotal
        foreach ($biayaUdangDetail as &$parentBlock) {
            $parentId = $parentBlock['parent']['jasa_vendor_in_id'];
            if (isset($biayaUdangTotal[$parentId])) {
                $parentBlock['parent']['harga_per_kilo'] = $biayaUdangTotal[$parentId]['harga_per_kilo'];
                $parentBlock['parent']['total_harga'] = $biayaUdangTotal[$parentId]['total_harga'];
                $parentBlock['parent']['tanggal_po'] = $biayaUdangTotal[$parentId]['tanggal_po'];
            }
        }

        $data = [
            'tanggal' => date('Y-m-d'),
            'biayaUdang' => $biayaUdang,
            'biayaUdangDetail' => $biayaUdangDetail,
            'divisi' => $this->divisiModel->find($biayaUdang['divisi_id']),
            'vendor' => $this->vendorModel->find($biayaUdang['vendor_id']),
            'biayaUdangTotal' => $biayaUdangTotal
        ];

        $this->dompdf->loadHtml(view('jasaVendor/biayaUdang/print', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream("Biaya Udang", array("Attachment" => false));
    }

    public function getNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisiID = $this->request->getVar('divisi_id');

        if (empty($divisiID)) {
            $no = $this->biayaUdangModel->get_no(date('m'), date('Y'), $last_day, "", $divisiID);
        } else {
            $divisi = $this->divisiModel->where('id', $divisiID)->first();
            $no = $this->biayaUdangModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisiID);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownBarang()
    {
        $jasaVendorInArrID = $this->request->getVar('multiple_jasa_vendor_in_id');
        $id = $this->request->getVar('id');
        
        // Decode JSON jika diperlukan
        if (is_string($jasaVendorInArrID)) {
            $jasaVendorInArrID = json_decode($jasaVendorInArrID, true);
        }
        
        if (empty($jasaVendorInArrID)) {
            return response()->setJSON([
                'data' => [],
                'dataTotal' => [],
                'token' => csrf_hash(),
                'status' => false,
                'message' => 'Jasa Vendor ID tidak boleh kosong'
            ]);
        }

        if (empty($id)) {
            $data = $this->biayaUdangModel->dropdownBarang($jasaVendorInArrID);
            $dataTotal = [];
        } else {
            $decryptedId = decrypt($id);
            $data = $this->biayaUdangModel->dropdownBarang($jasaVendorInArrID, $decryptedId);
            $dataTotal = $this->biayaUdangModel->getBarangDetail($jasaVendorInArrID, $decryptedId);
            
            // Update data dengan nilai dari dataTotal jika ada
            if (!empty($dataTotal)) {
                foreach ($data as $key => $item) {
                    $jasaVendorInId = $item['parent']['jasa_vendor_in_id'];
                    if (isset($dataTotal[$jasaVendorInId])) {
                        // Hanya update harga_per_kilo dan total_harga (tanpa tb_harga)
                        $data[$key]['parent']['harga_per_kilo'] = $dataTotal[$jasaVendorInId]['harga_per_kilo'];
                        $data[$key]['parent']['total_harga'] = $dataTotal[$jasaVendorInId]['total_harga'];
                        $data[$key]['parent']['tanggal_po'] = $dataTotal[$jasaVendorInId]['tanggal_po'];
                    }
                }
            }
        }
        
        return response()->setJSON([
            'data' => $data,
            'dataTotal' => $dataTotal,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownJasaVendorIn()
    {
        $divisiID = $this->request->getVar('divisi_id');
        $vendorID = $this->request->getVar('vendor_id');

        if (empty($divisiID)) {
            $data = [];
        } else {
            $data = $this->biayaUdangModel->dropdownJasaVendorIn($divisiID, $vendorID);
        }

        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownDivisi()
    {
        $vendorID = $this->request->getVar('vendor_id');

        if (empty($vendorID)) {
            $data = [];
        } else {
            $data = $this->biayaUdangModel->dropdownDivisi($vendorID);
        }

        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function autoComplete()
    {
        $listBarang = json_decode($_POST['listBarang']);
        $listBarangTotal = $this->biayaUdangModel->getDataTotalAutoComplete($listBarang);
        return response()->setJSON([
            'data' => $listBarang,
            'dataTotal' => $listBarangTotal,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }
}
