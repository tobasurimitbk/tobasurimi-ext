<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\OtherPaymentModel;
use App\Models\Sub_AkunsModel;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;

class OtherPayment extends BaseController
{
    protected $divisiModel;
    protected $this_company_id;
    protected $otherPaymentModel;
    protected $metaDataModel;
    protected $subAkunsModel;
    protected $jurnalController;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->otherPaymentModel = new OtherPaymentModel();
        $this->jurnalController = new JurnalUmum();
        $this->metaDataModel = new MetadataModel();
        $this->subAkunsModel = new Sub_AkunsModel();
    }

    public function index()
    {

        $data = [
            'divisi' => $this->divisiModel->getDivisiAccess(),
            "dataValuta" => $this->metaDataModel->get_by_name('Valuta'),
            'subsAkuns' =>  $this->subAkunsModel->asObject()
                ->where('deletedAt', null)
                ->where('company_id', $this->this_company_id)
                ->findAll()
        ];

        return view('Pembayaran/pembayaranLain/index', $data);
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "startdate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastdate" => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $responseData = [];

        $condition = [
            'other_payment.company_id' => $this->this_company_id,
        ];

        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "startDate" => $this->request->getGet("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "lastDate"  => $this->request->getGet("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "status_posting" => $this->request->getGet('status_posting'),
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $paymentList = $this->otherPaymentModel->getList($condition, $addCondition, $limit, $offset);

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($paymentList['data'] as $data) {
            array_push($responseData, [
                "no"                => $no++,
                "id"                => encrypt($data['id']),
                "divisi"            => $data['divisi'],
                "tanggal"           => date('d/m/Y', strtotime($data['tanggal'])),
                "no_pembayaran"     => $data['no_pembayaran'],
                "metode_pembayaran" => strtoupper($data['metode_pembayaran']),
                "valas"             => $data['valas_name'],
                "status_posting"    => $data['status_posting'],
                "nominal"           => number_format($data['nominal'], 2)
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $paymentList['totalData'],
            "recordsFiltered"   => $paymentList['totalFilteredData'],
            "data"              => $responseData,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function createAction()
    {
        $noPembayaran = $this->request->getVar('no_pembayaran');
        $otherPaymentFirst = $this->otherPaymentModel->where('no_pembayaran', $noPembayaran)->first();

        if ($otherPaymentFirst != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "No pembayaran " . $noPembayaran . " sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $id = $this->otherPaymentModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
            'no_pembayaran' => $this->request->getVar('no_pembayaran'),
            'bayar_ke' => $this->request->getVar('bayar_ke'),
            'valas' => $this->request->getVar('valas'),
            'metode_pembayaran' => $this->request->getVar('metode_pembayaran'),
            'nominal' => $this->request->getVar('nominal_pembayaran'),
            'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
            'akun_kas' => $this->request->getVar('akun_kas'),
            'akun_selisih' => $this->request->getVar('akun_selisih'),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Pembayaran lain-lain berhasil disimpan",
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->otherPaymentModel->update($id, [
            'divisi_id' => $this->request->getVar('divisi_id'),
            'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
            'bayar_ke' => $this->request->getVar('bayar_ke'),
            'valas' => $this->request->getVar('valas'),
            'metode_pembayaran' => $this->request->getVar('metode_pembayaran'),
            'nominal' => $this->request->getVar('nominal_pembayaran'),
            'pembayaran_oleh' => $this->request->getVar('pembayaran_oleh'),
            'akun_kas' => $this->request->getVar('akun_kas'),
            'akun_selisih' => $this->request->getVar('akun_selisih'),
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Pembayaran lain-lain berhasil diupdate",
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->otherPaymentModel->delete($id);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Pembayaran lain-lain berhasil dihapus",
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $data = $this->otherPaymentModel->find($id);
        $data['tanggal'] = date('d/m/Y', strtotime($data['tanggal']));
        $data['id'] = encrypt($data['id']);
        return response()->setJSON([
            'status' => true,
            'data' => $data,
            'token' => csrf_hash()
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->jurnalController->insertDataPembayaran($id, "LAIN-LAIN");
        // exit;
        $this->otherPaymentModel->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pembayaran berhasil diposting"
        ]);
    }
}
