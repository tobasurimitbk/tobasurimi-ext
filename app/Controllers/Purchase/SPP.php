<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Models\AMPurchaseOrderModel;
use App\Models\SppModel;
use App\Models\SppDetailModel;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\CompaniesModel;
use App\Models\ParentBarangModel;
use App\Models\RMImportPOModel;
use App\Models\RMPurchaseOrderModel;
use App\Models\SatuansModel;
use Dompdf\Dompdf;

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

    protected $this_company_id;
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
        $this->RmPurchaseOrderModel = new RMPurchaseOrderModel();
        $this->RmImportPoModel = new RMImportPOModel();

        $this->this_company_id = session()->get("login")->this_company_id;

        $this->dompdf = new Dompdf();
    }

    public function spp()
    {
        $dataSppType =  $this->MetadataModel->get_by_name("Tipe SPP");
        return view('Purchase/spp/index', ['dataSppType' => $dataSppType]);
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
            'satuanBarang' => $satuanModel->where('deletedAt', null)->findAll()
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

        $condition = [
            "purchase_request_details.deletedAt" => null,
            "purchase_requests.deletedAt" => null,
            "purchase_requests.company_id" => $this->this_company_id
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "spp_type"      => $this->request->getVar("spp_type"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $sppData = $this->SppModel->getSppList($condition, $addCondition, $limit, $offset);

        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($sppData['data'] as $data) {
            if ($data->spp_type == "Lokal BB") {
                $status = $this->RmPurchaseOrderModel->where('purchase_request_id', $data->id)->first();
            } elseif ($data->spp_type == "Import BB") {
                $status = $this->RmImportPoModel->where('purchase_request_id', $data->id)->first();
            } else {
                $status = $this->AmPurchaseOrderModel->where('purchase_request_id', $data->id)->first();
            }

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
                "status" => $status == null ? "OPEN" : "CLOSED"
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
        $id = $this->SppModel->insert([
            'company_id' => $this->this_company_id,
            "request_date" => $this->request->getVar("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("request_date")))) : "",
            'spp_no' => $this->request->getVar('spp_no'),
            'spp_type' => trim($this->request->getVar('spp_type')),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'note' => $this->request->getVar('note'),
            'createdBy' =>  session()->get("login")->user_id,
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
                'note' => $s->keterangan,
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

        $this->SppModel->update($id, [
            "request_date" => $this->request->getVar("request_date") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("request_date")))) : "",
            'spp_no' => $this->request->getVar('spp_no'),
            'divisi_id' => $this->request->getVar('divisi_id'),
            'spp_type' => trim($this->request->getVar('spp_type')),
            'note' => $this->request->getVar('note'),
        ]);

        // delete first in detail
        $this->SppDetailModel->where('purchase_request_id', $id)->delete();
        $spp_detail = json_decode($this->request->getVar("items"));

        foreach ($spp_detail as $s) {
            $this->SppDetailModel->insert([
                'purchase_request_id' => $id,
                'barang1_id' => decrypt($s->barang_id),
                'barang2_id' => decrypt($s->barang_spesifikasi_id),
                'nama_barang' => $s->nama_barang,
                'qty' => $s->qty,
                'unit' => $s->satuan_id,
                'note' => $s->keterangan,
            ]);
        }

        return response()->setJSON([
            "status" => true,
            "message" => "Data Berhasil Diupdate",
            "token" => csrf_hash()
        ]);
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

    public function generateSPP()
    {
        $divisi_name = $this->request->getVar("divisi_name");
        $response = $this->SppModel->generateNoSpp($divisi_name);
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

    public function printTable()
    {
        $filename = "Data SPP";

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "spp_type"      => $this->request->getVar("spp_type"),
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
            $this->dompdf->setPaper('A4', 'landscape');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }
}
