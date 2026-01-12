<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\AMPurchaseOrderModel;
use App\Models\BarangMasterModel;
use App\Models\SppModel;
use App\Models\SppDetailModel;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\CompaniesModel;
use App\Models\ParentBarangModel;
use App\Models\PenerimaanBarangModel;
use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SatuansModel;
use Dompdf\Dompdf;
use Exception;

class SPP extends BaseController
{
    protected $token;
    protected $role_id;
    protected $SppModel;
    protected $SppDetailModel;
    protected $MetadataModel;
    protected $DivisisModel;
    protected $CompaniesModel;
    protected $AmPurchaseOrderModel;
    protected $RmPurchaseOrderModel;
    protected $RmImportPoModel;
    protected $barangMasterModel;
    protected $amPurchaseOrderDetailModel;
    protected $penerimaanBarangModel;
    protected $this_company_id;
    protected $this_user_id;
    protected $is_admin;
    protected $dompdf;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->role_id = session()->get("login")->this_role_id;
        $this->SppModel = new SppModel();
        $this->SppDetailModel = new SppDetailModel();
        $this->MetadataModel = new MetadataModel();
        $this->DivisisModel = new DivisisModel();
        $this->CompaniesModel = new CompaniesModel();
        $this->AmPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->amPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->RmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RmImportPoModel = new RMImportPOModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get("login")->user_id;
        $this->is_admin = session()->get("login")->is_admin;

        $this->dompdf = new Dompdf();
    }

    public function spp()
    {
        $dataSppType =  $this->MetadataModel->get_by_name("Tipe SPP");
        $dataDivisi = $this->DivisisModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        return view('Purchase/spp/index', ['dataSppType' => $dataSppType, 'dataDivisi' => $dataDivisi]);
    }

    public function createSPP()
    {
        $dataSppType =  $this->MetadataModel->get_by_name("Tipe SPP");
        $dataDivisi = $this->DivisisModel->getDivisiAccess();
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();

        $data = [
            "today"       => date("d/m/Y"),
            "dataSppType" => $dataSppType,
            "dataDivisi"  => $dataDivisi,
            'kelompokBarang' => $parentBarangModel->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll(),
        ];

        return view('Purchase/spp/form', $data);
    }

    public function getByIdSPP($id = null)
    {
        $id = decrypt($id);

        $dataSppType =  $this->MetadataModel->get_by_name("Tipe SPP");
        $dataDivisi = $this->DivisisModel->getDivisiAccess();
        $parentBarangModel = new ParentBarangModel();
        $satuanModel = new SatuansModel();

        $data = [
            "today"         => date("d/m/Y"),
            "dataSppType" => $dataSppType,
            "dataDivisi" => $dataDivisi,
            'kelompokBarang' => $parentBarangModel->where('deletedAt', null)->findAll(),
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
        ];

        if (!empty($id)) {
            $dataSPP = $this->SppModel->getSppById($id);
            $dataSppDetail = $this->SppDetailModel->getSppDetailById($id);
            $data["dataSPP"] = $dataSPP;
            $data["dataSPPDetail"] = $dataSppDetail;
            $data["kelompokBarang"] = $parentBarangModel->where('deletedAt', null)->findAll();
            $data["satuanBarang"] = $satuanModel->where('deletedAt', null)->findAll();
        }

        if ($data["dataSPP"] == null) {
            return redirect()->to('spp');
        }

        return view('Purchase/spp/form', $data);
    }

    public function allSPP()
    {
        $payload = [
            "pageSize"         => $this->request->getVar("length"),
            "currentPage"      => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search"           => $this->request->getVar("search"),
            "spp_type"         => $this->request->getVar("spp_type"),
            "sort"             => $this->request->getVar("sort"),
            "sortType"         => $this->request->getVar("sortType"),
            "dateStart"        => $this->request->getVar("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"          => $this->request->getVar("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        if ($this->is_admin == '1') {
            $condition = [
                "purchase_request_details.deletedAt" => null,
                "purchase_requests.deletedAt" => null,
                "purchase_requests.company_id" => $this->this_company_id,
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                "purchase_request_details.deletedAt" => null,
                "purchase_requests.deletedAt" => null,
                "purchase_requests.company_id" => $this->this_company_id,
                "purchase_requests.user_id" => $this->this_user_id
            ];
        }


        $addCondition = [
            "divisi_id"     => $this->request->getVar('divisi_id'),
            "is_posted"     => $this->request->getVar('is_posted'),
            "search"        => $this->request->getVar("search"),
            "spp_type"      => $this->request->getVar("spp_type"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        if ($addCondition['is_posted'] == "BELUM POSTING") {
            // Jika Belum Posting Matikan Filter Start Date End Date
            $addCondition['dateStart'] = "";
            $addCondition['dateEnd'] = "";
        }

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $sppData = $this->SppModel->getSppList($condition, $addCondition, $limit, $offset);

        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($sppData['data'] as $data) {
            // if ($data->spp_type == "Lokal BB") {
            //     $status = $this->RmPurchaseOrderModel->where('purchase_request_id', $data->id)->first();
            // } elseif ($data->spp_type == "Import BB") {
            //     $status = $this->RmImportPoModel->where('purchase_request_id', $data->id)->first();
            // } else {
            //     $status = $this->AmPurchaseOrderModel->where('purchase_request_id', $data->id)->first();
            // }

            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "spp_types"      => strtoupper($data->spp_type),
                "spp_no"        => $data->spp_no,
                "companyName"  => $data->companyName,
                "divisiName" => $data->divisiName,
                "spp_type"      => $data->spp_type,
                "request_date"  => date('d/m/Y', strtotime($data->request_date)),
                "is_posted"     => $data->is_posted,
                "itemCount"     => $data->itemCount,
                "createdAt"     => date('d/m/Y', strtotime($data->createdAt)),
                "status" =>  $data->request_status != "finished" ? "OPEN" : "CLOSED",
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $sppData['totalData'],
            "recordsFiltered"   => $sppData['totalFilteredData'],
            "data"              => $dataSPP,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function saveSPP()
    {
        $sppNoFirst = $this->SppModel->where('company_id', $this->this_company_id)
            ->where('spp_no', $this->request->getVar('spp_no'))
            ->where('deletedAt', null)
            ->first();

        // Validasi Nomor SPP
        if ($sppNoFirst != null) {
            return response()->setJSON([
                "status"  => false,
                "message" => "Nomor SPP Sudah Dipakai",
                'token'   => csrf_hash(),
            ]);
        }

        $id = $this->SppModel->insert([
            'company_id' => $this->this_company_id,
            'user_id' => $this->this_user_id,
            "request_date" => $this->request->getVar("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("request_date")))) : "",
            'spp_no' => $this->request->getVar('spp_no'),
            'spp_type' => trim($this->request->getVar('spp_type')),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'note' => $this->request->getVar('note'),
        ]);

        $spp_detail = json_decode($this->request->getVar("items"));

        foreach ($spp_detail as $s) {
            $this->SppDetailModel->insert([
                'purchase_request_id' => $id,
                'barang1_id' => decrypt($s->barang_id),
                'barang2_id' => decrypt($s->barang_spesifikasi_id),
                'nama_barang' => $s->nama_barang,
                'qty' => $s->qty,
                'unit' => $s->satuan_id,
                'note' => trim(str_replace(["\r", "\n"], '', $s->keterangan)),
            ]);
        }

        return response()->setJSON([
            "id"      => encrypt($id),
            "status"  => true,
            "message" => "Data Berhasil disimpan",
            'token'   => csrf_hash(),
        ]);
    }

    public function updateSPP()
    {

        $id = decrypt($this->request->getVar('id'));

        $sppNoFirst = $this->SppModel->where('company_id', $this->this_company_id)
            ->where('spp_no', $this->request->getVar('spp_no'))
            ->where('id <>', $id)
            ->where('deletedAt', null)
            ->first();

        // Validasi Nomor SPP
        if ($sppNoFirst != null) {
            return response()->setJSON([
                "status"  => false,
                "message" => "Nomor SPP Sudah Dipakai",
                'token'   => csrf_hash(),
            ]);
        }

        $this->SppModel->update($id, [
            "request_date" => $this->request->getVar("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("request_date")))) : "",
            'spp_no' => $this->request->getVar('spp_no'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'spp_type' => trim($this->request->getVar('spp_type')),
            'note' => $this->request->getVar('note'),
        ]);

        $spp_detail = json_decode($this->request->getVar("items"));
        $id_detail_all = [];

        foreach ($spp_detail as $s) {
            $check = $this->SppDetailModel->where('id', $s->barang_detail_id)->first();
            if ($check != null) {
                $this->SppDetailModel->update($check['id'], [
                    'purchase_request_id' => $id,
                    'barang1_id' => decrypt($s->barang_id),
                    'barang2_id' => decrypt($s->barang_spesifikasi_id),
                    'nama_barang' => $s->nama_barang,
                    'qty' => $s->qty,
                    'unit' => $s->satuan_id,
                    'note' => trim(str_replace(["\r", "\n"], '', $s->keterangan)),
                ]);

                array_push($id_detail_all, $check['id']);
            } else {
                // NEW BARANG
                $id_detail_new = $this->SppDetailModel->insert([
                    'purchase_request_id' => $id,
                    'barang1_id' => decrypt($s->barang_id),
                    'barang2_id' => decrypt($s->barang_spesifikasi_id),
                    'nama_barang' => $s->nama_barang,
                    'qty' => $s->qty,
                    'unit' => $s->satuan_id,
                    'note' => trim(str_replace(["\r", "\n"], '', $s->keterangan)),
                ]);
                array_push($id_detail_all, $id_detail_new);
            }
        }

        $this->SppDetailModel
            ->where('purchase_request_id', $id)
            ->whereNotIn('id', $id_detail_all)
            ->delete();

        $this->updateKeteranganPo($id);

        return response()->setJSON([
            "status" => true,
            "message" => "Data Berhasil Diupdate",
            "token" => csrf_hash()
        ]);
    }

    public function updateKeteranganPo($sppId)
    {
        // update keterangan di po
        $selectQry = "
            am_purchase_order_details.id,
            am_purchase_order_details.am_purchase_order_id,
            purchase_request_details.note
        ";
        $amPurchaseOrderDetailList = $this->amPurchaseOrderDetailModel
            ->select($selectQry)
            ->join('am_purchase_orders', 'am_purchase_orders.id = am_purchase_order_details.am_purchase_order_id', 'left')
            ->join('purchase_request_details', 'purchase_request_details.id = am_purchase_order_details.purchase_request_detail_id', 'left')
            ->where('am_purchase_orders.purchase_request_id', $sppId)
            ->where('am_purchase_order_details.deletedAt', null)
            ->findAll();

        $poIds = array();
        $dataUpdated = array();
        foreach ($amPurchaseOrderDetailList as $poDetail) {
            array_push($poIds, $poDetail['am_purchase_order_id']);
            array_push($dataUpdated, [
                'id' => $poDetail['id'],
                'note' => trim(str_replace(["\r", "\n"], '', $poDetail['note']))
            ]);
        }

        if (count($dataUpdated) > 0) {
            $this->amPurchaseOrderDetailModel->updateBatch($dataUpdated, 'id');
        }

        if (count($poIds) > 0) {
            $this->penerimaanBarangModel->updateHargalpbByPoIds($poIds, $this->this_company_id);
        }
    }

    public function updateStatusSPP()
    {
        $id = decrypt($this->request->getVar('id'));
        $status = $this->request->getVar('status');

        $this->SppModel->update($id, [
            'is_posted' => $status,
        ]);

        return response()->setJSON([
            "status" => true,
            "message" => "Status Posting Berhasil Diupdate",
            "token" => csrf_hash()
        ]);
    }

    public function closeSPP()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->SppModel->update($id, [
            'request_status' => 'finished',
            'is_posted' => 1,
        ]);

        return response()->setJSON([
            "status" => true,
            "message" => "SPP berhasil Di Close",
            "token" => csrf_hash()
        ]);
    }

    public function generateSPP()
    {
        $divisi_name = $this->request->getVar("divisi_name");
        $request_date =  $this->request->getVar("request_date");
        $spp_type =  $this->request->getVar("spp_type");

        if (empty($request_date) || empty($divisi_name)) {
            $data = [
                "status"  => true,
                "data"  => '',
            ];
            echo json_encode($data);
        }

        $request_date = date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("request_date"))));
        $tanggalExplode = explode('-', $request_date);
        $year = $tanggalExplode[0];
        $month = $tanggalExplode[1];
        $response = $this->SppModel->generateNoSpp($divisi_name, $this->this_company_id, $month, $year, $spp_type);

        if ($response) {
            $data = [
                "status"  => true,
                "data"  => $response,
            ];
            echo json_encode($data);
        } else {
            $message = 'Gagal Auto Generate';
            $data = [
                "status" => false,
                "message"  => $message
            ];
            echo json_encode($data);
        }

        return;
    }

    public function deleteSPP()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->SppModel->delete($id);
        $this->SppDetailModel->where('purchase_request_id', $id)->delete();

        return response()->setJSON([
            'message' => "SPP Berhasil Dihapus",
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function deleteSPPDetail()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            // cari spp detail first
            $sppDetailFirst = $this->SppDetailModel->where('id', $id)->first();
            // hapus detail spp
            $this->SppDetailModel->delete($id);
            // Get ALl Spp setelah dihapus
            $sppDetailAll = $this->SppDetailModel
                ->where('purchase_request_id', $sppDetailFirst['purchase_request_id'])
                ->where('deletedAt', null)
                ->findAll();
            // hitung all spp setelah dihapus jika kosong maka hapus parentnya
            if (count($sppDetailAll) == 0) {
                $this->SppModel->delete($sppDetailFirst['purchase_request_id']);
            }

            return response()->setJSON([
                'message' => "SPP Detail Berhasil Dihapus",
                'token' => csrf_hash(),
                'status' => true
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => $e->getMessage(),
                'status' => csrf_hash()
            ]);
        }
    }

    public function printTable()
    {
        $filename = "Data SPP";

        if ($this->is_admin == '1') {
            $condition = [
                "purchase_request_details.deletedAt" => null,
                "purchase_requests.deletedAt" => null,
                "purchase_requests.company_id" => $this->this_company_id,
            ];
        } elseif ($this->is_admin == '0') {
            $condition = [
                "purchase_request_details.deletedAt" => null,
                "purchase_requests.deletedAt" => null,
                "purchase_requests.company_id" => $this->this_company_id,
                "purchase_requests.user_id" => $this->this_user_id
            ];
        }

        $addCondition = [
            "divisi_id"     => $this->request->getVar('divisi_id'),
            "is_posted"     => $this->request->getVar('is_posted'),
            "search"        => $this->request->getVar("search"),
            "spp_type"      => "",
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $sppData = $this->SppModel->getSppList($condition, $addCondition, 100000000, 0);

        $dataSPP = [];

        $no = 1;

        foreach ($sppData['data'] as $data) {
            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => $data->id,
                "spp_type"      => $data->spp_type,
                "spp_no"        => $data->spp_no,
                "companyName"   => $data->companyName,
                "divisiName"    => $data->divisiName,
                "request_date"  => date('d/m/Y', strtotime($data->request_date)),
                "is_posted"     => $data->is_posted,
                "createdAt"     => date('d/m/Y', strtotime($data->createdAt)),
            ]);
        }

        $data = [
            "dataSPP"  => $dataSPP,
        ];
        $this->dompdf->loadHtml(view('Purchase/spp/print-table', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function print($id = null)
    {
        if ($id) {
            $filename = "SPP";

            if (!empty($id)) {
                $id = decrypt($id);
                $dataSPP = $this->SppModel->getSppById($id);
                $dataSppDetail = $this->SppDetailModel->getSppDetailById($id);

                if ($dataSPP == null) {
                    return redirect()->to('spp');
                }

                $no = 0;
                $totalQty = 0;
                $totalAll = 0;

                foreach ($dataSppDetail as $value) {
                    $no++;
                    $value->no = $no;
                    $totalQty += formatter($value->qty, "STR_TO_FLOAT");
                }
                $dataSPP->totalQty = number_format($totalQty);
                $dataSPP->totalAll = number_format($totalAll);

                $data["dataSPP"] = $dataSPP;
                $data["dataSPP"]->purchase_request_details = $dataSppDetail;

                // dd($dataSppDetail);
            }
            $this->dompdf->loadHtml(view('Purchase/spp/print', $data));
            $this->dompdf->setPaper([0, 0, 595.28, 935.43], 'portrait'); // F4 in points
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function dropdownBarang()
    {
        $search = $this->request->getVar('q');
        $type = $this->request->getVar('type');

        $data = $this->barangMasterModel->dropdownBarangType(
            $type,
            $this->this_company_id,
            $search
        );

        $results = [];
        foreach ($data as $item) {
            $results[] = [
                'id' => $item['id'],
                'text' => $item['kode_barang'] . " - " . $item['barang_name'],
                'satuan_1' => $item['satuan_1'],
                'satuan_2' => $item['satuan_2'],
                'satuan_3' => $item['satuan_3'],
                'barang_name_master' => $item['barang_name_master'],
                'barang_spesifikasi_id' => $item['barang_master_spesifikasi_id'],
                'barang_id' => $item['id'],
                'nama' => $item['barang_name'],
                'satuan_id' => $item['satuan_1'],
                'kode_barang' => $item['kode_barang'],
                'barang_id' => encrypt($item['barang_id']),
                'satuan' => $item['nama_satuan']
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }

    public function dropdownBarangFirst()
    {
        $search = $this->request->getVar('q');
        $type = $this->request->getVar('type');
        $id = decrypt($this->request->getVar('barang_spesifikasi_id'));

        $data = $this->barangMasterModel->dropdownBarangType(
            $type,
            $this->this_company_id,
            $search,
            $id
        );

        $results = [];
        foreach ($data as $item) {
            $results[] = [
                'id' => $item['id'],
                'text' => $item['kode_barang'] . " - " . $item['barang_name'],
                'satuan_1' => $item['satuan_1'],
                'satuan_2' => $item['satuan_2'],
                'satuan_3' => $item['satuan_3'],
                'barang_name_master' => $item['barang_name_master'],
                'barang_spesifikasi_id' => $item['barang_master_spesifikasi_id'],
                'barang_id' => encrypt($item['barang_id']),
                'nama' => $item['barang_name'],
                'satuan_id' => $item['satuan_1'],
                'kode_barang' => $item['kode_barang'],
                'satuan' => $item['nama_satuan']
            ];
        }

        return $this->response->setJSON(['data' => $results]);
    }
}
