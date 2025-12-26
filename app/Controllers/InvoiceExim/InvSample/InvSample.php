<?php

namespace App\Controllers\InvoiceExim\InvSample;

use App\Controllers\BaseController;
use App\Models\SampleModel;
use Exception;

class InvSample extends BaseController
{
    protected $this_company_id;
    protected $sampleModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->sampleModel = new SampleModel();
    }

    public function index()
    {
        return view('InvoiceExim/InvSample/index');
    }

    public function updateNoInvoice()
    {
        try {
            $id = decrypt($this->request->getVar('id'));
            $noInvoice = $this->request->getVar('no_invoice');
            $tanggalInvoice = formatDMYtoYMD($this->request->getVar('tanggal_invoice'));
            $this->sampleModel->update($id, [
                'no_invoice' => $noInvoice,
                'tanggal_invoice' => $tanggalInvoice
            ]);
            return response()->setJSON([
                'status' => true,
                'message' => "no invoice berhasil diupdate",
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
