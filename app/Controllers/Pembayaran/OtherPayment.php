<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\OtherPaymentModel;
use App\Models\OtherPaymentDetailModel;
use App\Models\Sub_AkunsModel;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;

class OtherPayment extends BaseController
{
    protected $divisiModel;
    protected $this_company_id;
    protected $otherPaymentModel;
    protected $otherPaymentDetailModel;
    protected $metaDataModel;
    protected $subAkunsModel;
    protected $jurnalController;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->otherPaymentModel = new OtherPaymentModel();
        $this->otherPaymentDetailModel = new OtherPaymentDetailModel();
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
                "no_pembayaran"     => $data['no_pembayaran'],
                "valas"             => $data['valas_name'],
                "status_posting"    => $data['status_posting'],
                "nominal"           => number_format($data['nominal_all'], 2)
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
        $payload = $this->request->getJSON(true); // Ambil body JSON sebagai array

        $noPembayaran = $payload['no_pembayaran'];
        $existing = $this->otherPaymentModel->where('no_pembayaran', $noPembayaran)->first();

        if ($existing !== null) {
            return $this->response->setJSON([
                'status' => false,
                'message' => "No pembayaran " . $noPembayaran . " sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $parentData = [
            'company_id'     => $this->this_company_id,
            'divisi_id'      => $payload['divisi_id'],
            'no_pembayaran'  => $payload['no_pembayaran'],
            'bayar_ke'       => $payload['bayar_ke'],
            'valas'          => $payload['valas'],
        ];

        $parentId = $this->otherPaymentModel->insert($parentData);

        foreach ($payload['details'] as $detail) {
            $detailData = [
                'other_payment_id'     => $parentId,
                'tanggal_pembayaran'   => date("Y-m-d", strtotime(str_replace("/", "-", $detail['tanggal']))),
                'metode_pembayaran'    => $detail['metode_pembayaran'],
                'nominal'              => str_replace(['.', ','], '', $detail['nominal_pembayaran']),
                'pembayaran_oleh'      => $detail['pembayaran_oleh'],
                'akun_kas'             => $detail['akun_kas'],
                'akun_selisih'         => $detail['akun_selisih'],
                'keterangan'           => $detail['keterangan'] ?? null
            ];

            $this->otherPaymentDetailModel->insert($detailData);
        }

        return $this->response->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Pembayaran lain-lain berhasil disimpan",
        ]);
    }


    public function updateAction()
    {
        // Ambil data JSON dari body
        $payload = $this->request->getJSON(true);
    
        $parentId = decrypt($payload['id']) ?? null;
    
        if (!$parentId) {
            return $this->response->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => 'ID tidak ditemukan'
            ]);
        }
    
        // 1. Update Parent Data
        $parentData = [
            'divisi_id'     => $payload['divisi_id'],
            'no_pembayaran' => $payload['no_pembayaran'],
            'bayar_ke'      => $payload['bayar_ke'],
            'valas'         => $payload['valas'],
        ];
        $this->otherPaymentModel->update($parentId, $parentData);
    
        // 2. Handle Details
        $existingDetails   = $this->otherPaymentDetailModel->where('other_payment_id', $parentId)->findAll();
        $existingDetailIds = array_column($existingDetails, 'id');
        $submittedDetailIds = [];
    
        foreach ($payload['details'] as $detail) {
            $detailData = [
                'other_payment_id'     => $parentId,
                'tanggal_pembayaran'   => date("Y-m-d", strtotime(str_replace("/", "-", $detail['tanggal']))),
                'metode_pembayaran'    => $detail['metode_pembayaran'],
                'nominal'              => str_replace(['.', ','], '', $detail['nominal_pembayaran']),
                'pembayaran_oleh'      => $detail['pembayaran_oleh'],
                'akun_kas'             => $detail['akun_kas'],
                'akun_selisih'         => $detail['akun_selisih'],
                'keterangan'           => $detail['keterangan'] ?? null
            ];
    
            if (!empty(decrypt($detail['id']))) {
                // Update
                $this->otherPaymentDetailModel->update(decrypt($detail['id']), $detailData);
                $submittedDetailIds[] = decrypt($detail['id']);
            } else {
                // Insert
                $this->otherPaymentDetailModel->insert($detailData);
            }
        }
    
        // 3. Delete removed details
        $detailsToDelete = array_diff($existingDetailIds, $submittedDetailIds);
        if (!empty($detailsToDelete)) {
            $this->otherPaymentDetailModel->whereIn('id', $detailsToDelete)->delete();
        }
    
        return $this->response->setJSON([
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
        
        // Get parent data
        $parentData = $this->otherPaymentModel->find($id);
        if (!$parentData) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data not found',
                'token' => csrf_hash()
            ]);
        }

        // Get all child details
        $details = $this->otherPaymentDetailModel->where('other_payment_id', $id)->findAll();
        
        // Format details data
        $formattedDetails = [];
        foreach ($details as $detail) {
            $formattedDetails[] = [
                'id' => encrypt($detail['id']),
                'tanggal' => date('d/m/Y', strtotime($detail['tanggal_pembayaran'])),
                'metode_pembayaran' => $detail['metode_pembayaran'],
                'nominal_pembayaran' => number_format($detail['nominal'], 0, ',', '.'),
                'pembayaran_oleh' => $detail['pembayaran_oleh'],
                'akun_kas' => $detail['akun_kas'],
                'akun_selisih' => $detail['akun_selisih'],
                'keterangan' => $detail['keterangan']
            ];
        }

        // Prepare response
        $response = [
            'status' => true,
            'data' => [
                'parent' => [
                    'id' => encrypt($parentData['id']),
                    'no_pembayaran' => $parentData['no_pembayaran'],
                    'divisi_id' => $parentData['divisi_id'],
                    'bayar_ke' => $parentData['bayar_ke'],
                    'valas' => $parentData['valas'],
                    'status_posting' => $parentData['status_posting'] ?? '0'
                ],
                'details' => $formattedDetails
            ],
            'token' => csrf_hash()
        ];

        return $this->response->setJSON($response);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->jurnalController->insertDataPembayaran($id, "LAIN-LAIN", null);
        // exit;
        $this->otherPaymentModel->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Pembayaran berhasil diposting"
        ]);
    }
}
