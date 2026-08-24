<?php

namespace App\Controllers\HR;

use App\Controllers\BaseController;
use App\Models\FormRequestActionModel;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Exception;

class FormRequestAction extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $formRequestActionModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_user_id = session()->get('login')->user_id;
        $this->formRequestActionModel = new FormRequestActionModel();
    }

    public function index()
    {
        return view('hr/requestAction/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $yearMonth = $this->request->getVar('year_month');
        $search = trim($this->request->getVar('search'));

        $condition = [
            'form_request_action.deletedAt' => null,
            'form_request_action.company_id' => $this->this_company_id,
            'form_request_action.year_month' => $yearMonth
        ];

        $addCondition = [
            "search" => $search,
            'sort' => $payload['sort'],
            'sortType' => $payload['sortType'],
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $result = $this->formRequestActionModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataResult = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;
        foreach ($result['data'] as $p) {
            array_push($dataResult, [
                "no" => $no++,
                "id" => encrypt($p['id']),
                "nomor" => $p['nomor'],
                "judul_form" => $p['judul_form'],
                "last_update" => $p['last_update'],
                "status_print" => $p['status_print'],
            ]);
        }
        $data = [
            "draw" => intval($this->request->getGet("draw")),
            "recordsTotal" => $result['totalData'],
            "recordsFiltered" => $result['totalFilteredData'],
            "data" => $dataResult,
            "payload" => $payload
        ];

        return response()->setJSON($data);
    }

    public function save()
    {
        try {
            $nomor = $this->request->getVar('nomor');
            $yearMonth = $this->request->getVar('year_month');
            $judulForm = $this->request->getVar('judul_form');
            $chekNo = $this->checkNo($nomor, $this->this_company_id, null);
            $yearMonthArr = explode('-', $yearMonth);

            if ($chekNo != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false,
                    'message' => 'Nomor sudah dipakek'
                ]);
            }

            $this->formRequestActionModel->insert([
                'nomor' => $nomor,
                'year_month' => $yearMonth,
                'judul_form' => $judulForm,
                'year' => $yearMonthArr[0],
                'last_update_by' => $this->this_user_id,
                'company_id' => $this->this_company_id,
                'status_print' => 'no'
            ]);

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => 'Berhasil simpan'
            ]);

        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $nomor = $this->request->getVar('nomor');
            $judulForm = $this->request->getVar('judul_form');
            $yearMonth = $this->request->getVar('year_month');
            $yearMonthArr = explode('-', $yearMonth);

            $chekNo = $this->checkNo(
                $nomor,
                $this->this_company_id,
                $id
            );

            if ($chekNo != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'status' => false,
                    'message' => 'Nomor sudah dipakek'
                ]);
            }

            $this->formRequestActionModel->update($id, [
                'nomor' => $nomor,
                'year_month' => $yearMonth,
                'judul_form' => $judulForm,
                'year' => $yearMonthArr[0],
                'last_update_by' => $this->this_user_id,
                'company_id' => $this->this_company_id,
                'status_print' => 'no'
            ]);

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'message' => 'Berhasil update'
            ]);

        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->formRequestActionModel->delete($id);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => 'Data terhapus'
        ]);
    }

    public function get()
    {
        $id = decrypt($this->request->getVar('id'));
        $data = $this->formRequestActionModel->where('id', $id)->first();
        $data['id'] = encrypt($data['id']);

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'data' => $data
        ]);

    }

    public function getNo()
    {
        try {
            $yearMonth = $this->request->getVar('year_month');

            $nomor = $this->formRequestActionModel->getNo(
                $this->this_company_id,
                $yearMonth
            );

            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => true,
                'data' => $nomor
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function print()
    {
        try {

            $ids = json_decode($this->request->getVar('id'));
            if (count($ids) == 0) {
                return redirect()->back();
            }

            $idRes = [];
            $dataUpdated = [];
            foreach ($ids as $i) {
                $idDecrypt = decrypt($i);
                $idRes[] = $idDecrypt;
                $dataUpdated[] = [
                    'id' => $idDecrypt,
                    'status_print' => 'yes'
                ];
            }

            $this->formRequestActionModel->updateBatch($dataUpdated, 'id');
            $nomorList = $this->formRequestActionModel->whereIn('id', $idRes)->orderBy('nomor', "asc")->findAll();
            $data = [
                'nomorList' => $nomorList
            ];

            $dompdf = new Dompdf();

            $dompdf->loadHtml(view('hr/requestAction/print', $data));
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('Form Request Action', array("Attachment" => false));
            exit(0);

        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function checkNo(
        $nomor,
        $companyId,
        $id = null
    ) {
        $dataQry = $this->formRequestActionModel->where('nomor', $nomor);
        $dataQry->where('company_id', $companyId);
        $dataQry->where('deletedAt', null);
        if ($id != null) {
            $dataQry->where('id <>', $id);
        }

        return $dataQry->first();
    }
}
