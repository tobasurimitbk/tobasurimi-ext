<?php

namespace App\Controllers\JasaVendor;

use App\Controllers\BaseController;
use App\Models\BiayaUdangModel;
use App\Models\DivisisModel;
use App\Models\JasaVendorInModel;
use Dompdf\Dompdf;

class BiayaUdang extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $divisiModel;
    protected $biayaUdangModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->biayaUdangModel = new BiayaUdangModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess()
        ];

        return view('jasaVendor/biayaUdang/index', $data);
    }

    public function create()
    {
        $data = [
            'tanggal' => date('Y-m-d'),
            'jasaVendorIn' => $this->biayaUdangModel->dropdownPenerimaanSuratJalan()
        ];

        return view('jasaVendor/biayaUdang/form', $data);
    }

    public function createAction()
    {
        $listBarang = json_decode($_POST['listBarang']);
        return response()->setJSON([
            'listBarang' => $listBarang,
            'all' => $_POST
        ]);
    }

    public function dropdownBarang()
    {
        $jasaVendorInID = $this->request->getVar('jasa_vendor_in_id');
        $id = $this->request->getVar('id');
        if (empty($id)) {
            $data = $this->biayaUdangModel->dropdownBarang($jasaVendorInID);
            return response()->setJSON([
                'data' => $data,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }
}
