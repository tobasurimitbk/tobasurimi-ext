<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\OtherPaymentModel;
use App\Models\OtherPaymentDetailModel;
use App\Models\Sub_AkunsModel;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;
use App\Models\BanksModel;

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
        $this->banksModel = new BanksModel();
    }

    public function index()
    {
        $data = [
            'bankList' => $this->banksModel->where('company_id', $this->this_company_id)
                            ->orderBy('name', "ASC")
                            ->findAll(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'dataValuta' => $this->metaDataModel->get_by_name('Valuta'),
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
                "status_posting"    => $data['status_posting'],
                "nominal"           => number_format($data['nominal_all'], 2),
                "bayar_ke"          => $data['bayar_ke'],

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
        $payload = $this->request->getJSON(true);
        
        // Validasi no_pembayaran
        $existing = $this->otherPaymentModel->where('no_pembayaran', $payload['no_pembayaran'])->first();
        if ($existing) {
            return $this->response->setJSON([
                'status' => false,
                'message' => "No pembayaran sudah ada",
                'token' => csrf_hash()
            ]);
        }

        // Sederhanakan parent data
        $parentData = [
            'company_id' => $this->this_company_id,
            'divisi_id' => $payload['divisi_id'],
            'bank_id' => $payload['bank_id'],
            'no_pembayaran' => $payload['no_pembayaran'],
            'bayar_ke' => $payload['bayar_ke'],
            'jenis_pembayaran' => $payload['jenis_pembayaran'],
            'metode_pembayaran' => $payload['metode_pembayaran'],
            'keterangan' => $payload['keterangan_parent'],
            'nominal' => $payload['total_all_amount'],
            // SELALU simpan akun_selisih dan akun_kas sesuai jenis
            'akun_selisih' => $payload['jenis_pembayaran'] === 'PUTIH' ? $payload['akun_selisih'] : null,
            'akun_kas' => $payload['jenis_pembayaran'] === 'MERAH' ? $payload['akun_selisih'] : null,
        ];

        $parentId = $this->otherPaymentModel->insert($parentData);

        // Proses details
        $latestDate = null;
        foreach ($payload['details'] as $detail) {
            $detailData = [
                'other_payment_id' => $parentId,
                'tanggal_pembayaran' => date("Y-m-d", strtotime(str_replace("/", "-", $detail['tanggal']))),
                'nominal' => $detail['jumlah_idr'],
                'pembayaran_oleh' => $detail['pembayaran_oleh'],
                'valas_id' => $detail['valas_id'],
                'kurs' => $detail['kurs'],
                'jumlah' => $detail['jumlah'],
                'jumlah_idr' => $detail['jumlah_idr'],
                'keterangan' => $detail['keterangan'] ?? null,
                // Konsisten: akun_kas selalu debit, akun_selisih selalu kredit
                'akun_kas' => $detail['akun_kas'],
                'akun_selisih' => $payload['jenis_pembayaran'] === 'PUTIH' 
                    ? $payload['akun_selisih'] 
                    : $detail['akun_kas']
            ];

            $this->otherPaymentDetailModel->insert($detailData);
            $latestDate = $detailData['tanggal_pembayaran'];
        }

        if ($latestDate) {
            $this->otherPaymentModel->update($parentId, ['tanggal' => $latestDate]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => "Data berhasil disimpan",
            'token' => csrf_hash()
        ]);
    }


    public function updateAction()
    {
        $payload = $this->request->getJSON(true);
        $parentId = decrypt($payload['id']) ?? null;

        if (!$parentId) {
            return $this->response->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => 'ID tidak ditemukan'
            ]);
        }

        // 1. Update Parent Data - Konsisten dengan createAction
        $parentData = [
            'divisi_id' => $payload['divisi_id'],
            'bank_id' => $payload['bank_id'],
            'no_pembayaran' => $payload['no_pembayaran'],
            'bayar_ke' => $payload['bayar_ke'],
            'jenis_pembayaran' => $payload['jenis_pembayaran'],
            'metode_pembayaran' => $payload['metode_pembayaran'],
            'keterangan' => $payload['keterangan_parent'],
            'nominal' => $payload['total_all_amount'],
            // Tetap konsisten dengan logika create
            'akun_selisih' => $payload['jenis_pembayaran'] === 'PUTIH' ? $payload['akun_selisih'] : null,
            'akun_kas' => $payload['jenis_pembayaran'] === 'MERAH' ? $payload['akun_selisih'] : null,
        ];
        $this->otherPaymentModel->update($parentId, $parentData);

        // 2. Handle Details
        $existingDetails = $this->otherPaymentDetailModel->where('other_payment_id', $parentId)->findAll();
        $existingDetailIds = array_column($existingDetails, 'id');
        $submittedDetailIds = [];
        $latestDate = null;

        foreach ($payload['details'] as $detail) {
            $detailId = !empty($detail['id']) ? decrypt($detail['id']) : null;
            
            $detailData = [
                'other_payment_id' => $parentId,
                'tanggal_pembayaran' => date("Y-m-d", strtotime(str_replace("/", "-", $detail['tanggal']))),
                'nominal' => $detail['jumlah_idr'],
                'pembayaran_oleh' => $detail['pembayaran_oleh'],
                'valas_id' => $detail['valas_id'],
                'kurs' => $detail['kurs'],
                'jumlah' => $detail['jumlah'],
                'jumlah_idr' => $detail['jumlah_idr'],
                'keterangan' => $detail['keterangan'] ?? null,
                // Logika konsisten dengan create:
                'akun_kas' => $detail['akun_kas'], // Selalu debit dari detail
                'akun_selisih' => $payload['jenis_pembayaran'] === 'PUTIH' 
                    ? $payload['akun_selisih'] 
                    : $detail['akun_kas'] // Untuk MERAH, akun_selisih di child = akun_kas
            ];

            if ($detailId && in_array($detailId, $existingDetailIds)) {
                // Update existing detail
                $this->otherPaymentDetailModel->update($detailId, $detailData);
                $submittedDetailIds[] = $detailId;
            } else {
                // Insert new detail
                $this->otherPaymentDetailModel->insert($detailData);
            }

            $latestDate = $detailData['tanggal_pembayaran'];
        }

        // 3. Delete removed details
        $detailsToDelete = array_diff($existingDetailIds, $submittedDetailIds);
        if (!empty($detailsToDelete)) {
            $this->otherPaymentDetailModel->whereIn('id', $detailsToDelete)->delete();
        }

        // Update tanggal terakhir
        if ($latestDate) {
            $this->otherPaymentModel->update($parentId, ['tanggal' => $latestDate]);
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
        $details = $this->otherPaymentDetailModel
            ->select('other_payment_detail.*, 
                            kas.nama_sub as akun_kas_name, 
                            selisih.nama_sub as akun_selisih_name,
                            kas.no_sub as akun_kas_no,
                            selisih.no_sub as akun_selisih_no,
                            metadata.value as valas')
            ->join('sub_akuns as kas', 'kas.id = other_payment_detail.akun_kas', 'left')
            ->join('sub_akuns as selisih', 'selisih.id = other_payment_detail.akun_selisih', 'left')
            ->join('metadata', 'metadata.id = other_payment_detail.valas_id', 'left')
            ->where('other_payment_detail.other_payment_id', $id)
            ->findAll();

        // Format details data
        $formattedDetails = [];
        foreach ($details as $detail) {
            $formattedDetails[] = [
                'id' => encrypt($detail['id']),
                'tanggal' => date('d/m/Y', strtotime($detail['tanggal_pembayaran'])),
                'nominal_pembayaran' => number_format($detail['nominal'], 0, ',', '.'),
                'pembayaran_oleh' => $detail['pembayaran_oleh'],
                'akun_kas' => $detail['akun_kas'],
                'valas' => $detail['valas'],
                'valas_id' => $detail['valas_id'],
                'jumlah' => $detail['jumlah'],
                'jumlah_idr' => $detail['jumlah_idr'],
                'kurs' => $detail['kurs'],
                'akun_selisih' => $detail['akun_selisih'],
                'akun_kas_name' => $detail['akun_kas_no'] . ' ' . $detail['akun_kas_name'],
                'akun_selisih_name' => $detail['akun_selisih_no'] . ' ' . $detail['akun_selisih_name'],
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
                    'metode_pembayaran' => $parentData['metode_pembayaran'],
                    'divisi_id' => $parentData['divisi_id'],
                    'bank_id' => $parentData['bank_id'],
                    'bayar_ke' => $parentData['bayar_ke'],
                    'akun_selisih' => $parentData['akun_selisih'],
                    'akun_kas' => $parentData['akun_kas'],
                    'jenis_pembayaran' => $parentData['jenis_pembayaran'],
                    'total_all_amount' => $parentData['nominal'],
                    'keterangan_parent' => $parentData['keterangan'],
                    'status_posting' => $parentData['status_posting'] ?? '0'
                ],
                'details' => $formattedDetails
            ],
            'token' => csrf_hash()
        ];

        return $this->response->setJSON($response);
    }


    public function generatePayment()
    {
        $otherPaymentModel = new otherPaymentModel();
        $jenis = $this->request->getGet('jenisPembayaran');
        $divisi = str_replace(' ', '', trim($this->request->getGet('divisiId')));
        $bank = str_replace(' ', '', trim($this->request->getGet('bankId')));
        $metodePembayaran = $this->request->getGet('metodePembayaran');

        $paymentNo = $otherPaymentModel->get_new_no(
            $jenis,
            $divisi,
            $metodePembayaran,
            $bank,
            date('m'),
            date('Y'),
            getLastDay(),
            $this->this_company_id
        );

        return response()->setJSON([
            'paymentNo' => $paymentNo,
            'token' => csrf_hash(),
            'success' => true,
        ]);
    }

    public function posting()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $currentStatus = $this->request->getVar('status') ?? 1; // Default to posting if not specified

            // Update posting status
            $this->otherPaymentModel->update($id, ['status_posting' => $currentStatus]);

            if ($currentStatus == 1) {
                // POSTING LOGIC
                $result = $this->jurnalController->insertDataPembayaran($id, "LAIN-LAIN", null);
                $message = "Pembayaran berhasil diposting";
            } else {
                // UNPOSTING LOGIC
                $result = $this->jurnalController->unpostDataPembayaran($id, "LAIN-LAIN", null);
                $message = "Pembayaran berhasil diunpost";
            }

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => $message,
                'new_status' => $currentStatus
            ]);

        } catch (\Exception $e) {
            // Rollback status update if error occurs
            $this->otherPaymentModel->update($id, ['status_posting' => $currentStatus ? 0 : 1]);

            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Gagal memproses: " . $e->getMessage()
            ]);
        }
    }
}
