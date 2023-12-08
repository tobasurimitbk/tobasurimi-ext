<?php

namespace App\Controllers\Accounting\JurnalUmum;

use App\Controllers\BaseController;
use App\Models\AccountModuleModel;
use App\Models\Sub_AkunsModel;
use App\Models\JurnalUmumModel;

class JurnalUmum extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
    }

    public function index()
    {
        $accountModuleModel = new AccountModuleModel();
        $Sub_AkunsModel = new Sub_AkunsModel();

        $accountModuleData = $accountModuleModel->asObject()->findAll();
        $subAkunsModel = $Sub_AkunsModel->getAPAR("");

        $data = [
            "dataAccountModule" => $accountModuleData,
            "subAkuns" => $subAkunsModel
        ];

        return view('Accounting/jurnalUmum/index', $data);
    }

    public function save()
    {
        try {
            $nm = $this->request->getPost('cari');
            $result = array();
            foreach ($nm as $key => $val) {
                if ($_POST['debet'][$key] == "" || $_POST['debet'][$key] == 0) {
                    $result[] = array(
                        'id_transaksi' => 1,
                        'id_coa' => $_POST['cari'][$key],
                        'tanggal_jurnal' => $this->request->getPost('tgl_transaksi'),
                        'debit' => "0",
                        'kredit' => (float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['kredit'][$key])),
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id
                    );
                } else if ($_POST['kredit'][$key] == "" || $_POST['kredit'][$key] == 0) {
                    $result[] = array(
                        'id_transaksi' => 1,
                        'id_coa' => $_POST['cari'][$key],
                        'tanggal_jurnal' => $this->request->getPost('tgl_transaksi'),
                        'debit' => (float) str_replace(",", ".", str_replace(["Rp. ", "."], "",  $_POST['debet'][$key])),
                        'kredit' => "0",
                        'keterangan' => $_POST['ket'][$key],
                        'id_inputer' => session()->get("login")->user_id
                    );
                }
            }
            $this->jurnalUmumModel->insertJurnalBatch($result);

            session()->setFlashdata('success_message', 'Data Berhasil disimpan');
        } catch (\Exception $e) {
            session()->setFlashdata('error_message', $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
        return redirect()->to('jurnal');
    }

    public function updateAccountModule()
    {
        try {
            $accountModuleModel = new AccountModuleModel();

            $accountModuleData = $accountModuleModel->asObject()->findAll();

            $rules = [
                "name" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $id = $this->request->getPost("id");

                $payload = [
                    "name"              => $this->request->getPost("name"),
                    "type"           => $this->request->getPost("tipe"),
                    "kategori"           => $this->request->getPost("kategori"),
                    "module"           => $this->request->getPost("module"),
                    "ap_id"           => $this->request->getPost("akun_ap_id"),
                    "ar_id"           => $this->request->getPost("akun_ar_id")
                ];
            }

            if ($payload) {
                $accountModuleModel->update($id, $payload);

                $data = [
                    "status"            => true,
                    "message"   => "Data Berhasil diubah",
                    "payload"   => $payload,
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function getByIdAccountModule($id)
    {
        $accountModuleModel = new AccountModuleModel();
        $accountModuleData = $accountModuleModel->getAccountModuleById($id);

        if (!$accountModuleData) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }

        // $response = curl_request("GET", "/barangs/$id?idCompany=$this->this_company_id", $this->token);

        $accountModuleData->list_address = []; // cek nanti
        $data = [
            "status"    => true,
            "data"      => $accountModuleData,
        ];
        echo json_encode($data);

        return;
    }

    public function deleteAccountModule()
    {
        try {
            $accountModuleModel = new AccountModuleModel();
            $id = $this->request->getPost("id");

            if (empty($id)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $accountModuleModel->delete($id);
            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }
}
