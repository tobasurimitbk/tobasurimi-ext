<?php

namespace App\Controllers\BiayaExim\BiayaImpor;

use App\Controllers\BaseController;
use App\Models\BiayaImporModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\SupplierModel;
use App\Models\TaxModel;
use App\Models\VendorPelayaranModel;
use Dompdf\Dompdf;
use Exception;

class BiayaImpor extends BaseController
{

    protected $this_company_id;
    protected $divisiModel;
    protected $taxesModel;
    protected $vendorPelayaranModel;
    protected $dompdf;
    protected $supplierModel;
    protected $metadataModel;
    protected $biayaImporModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->taxesModel = new TaxModel();
        $this->vendorPelayaranModel = new VendorPelayaranModel();
        $this->supplierModel = new SupplierModel();
        $this->dompdf = new Dompdf();
        $this->metadataModel = new MetadataModel();
        $this->biayaImporModel = new BiayaImporModel();
    }


    public function index()
    {
        return view('BiayaExim/BiayaImpor/index');
    }

    public function create()
    {
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataPajak = $this->taxesModel
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%', 'PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('type', "asc")
            ->findAll();
        $dataVendorPelayaran = $this->vendorPelayaranModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('nama_vendor', "asc")->findAll();
        $dataSupplier = $this->supplierModel->getSupplierByType("INTERNASIONAL");
        $dataValas = $this->metadataModel->where('name', "Valuta")->where('deletedAt', null)->orderBy('value', "ASC")->findAll();

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataSupplier" => $dataSupplier,
            "dataValas" => $dataValas
        ];


        return view('BiayaExim/BiayaImpor/form', $data);
    }

    public function dropdownPo()
    {
        $tipePo = trim($this->request->getVar('tipe_po'));
        $supplierId = $this->request->getVar('supplier_id');

        $resultArray = $this->biayaImporModel->getDropdownPo(
            $supplierId,
            $tipePo
        );

        return response()->setJSON([
            'data' => $resultArray,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getDetailBarangPo()
    {
        try {
            $tipePo = trim($this->request->getVar('tipe_po'));
            $poId = $this->request->getVar('po_id');

            if (!empty($tipePo) && !empty($poId)) {
                $resultArray = $this->biayaImporModel->getDetailBarangPo(
                    $poId,
                    $tipePo
                );

                return response()->setJSON([
                    'data' => $resultArray,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            } else {
                return response()->setJSON([
                    'data' => null,
                    'token' => csrf_hash(),
                    'status' => true
                ]);
            }
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'message' => $e->getMessage() . " at " . $e->getFile() . " line " . $e->getLine()
            ]);
        }
    }
}
