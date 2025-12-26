<?php

namespace App\Controllers\SalesInternasional;

use App\Controllers\BaseController;
use App\Models\BarangMasterSalesModel;
use App\Models\CompaniesModel;
use App\Models\CustomerModel;
use App\Models\DivisisModel;
use App\Models\SalesOrderExportModel;
use App\Models\SampleAdditionalModel;
use App\Models\SampleDetailModel;
use App\Models\SampleModel;
use App\Models\SatuansModel;
use Exception;
use Dompdf\Dompdf;

class Sample extends BaseController
{
    protected $this_user_id;
    protected $is_admin;
    protected $this_company_id;
    protected $satuanModel;
    protected $divisiModel;
    protected $barangMasterSalesModel;
    protected $sampleModel;
    protected $sampleDetailModel;
    protected $companyModel;
    protected $sampleAdditionalModel;
    protected $customerModel;
    protected $salesOrderExportModel;
    protected $dompdf;

    public function __construct()
    {
        $this->is_admin = session()->get("login")->is_admin;
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->satuanModel = new SatuansModel();
        $this->divisiModel = new DivisisModel();
        $this->barangMasterSalesModel = new BarangMasterSalesModel();
        $this->sampleModel = new SampleModel();
        $this->sampleDetailModel = new SampleDetailModel();
        $this->companyModel = new CompaniesModel();
        $this->sampleAdditionalModel = new SampleAdditionalModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->customerModel = new CustomerModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('SalesInternasional/Sample/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];
        if ($this->is_admin == '1') {

            $condition = [
                "sample.company_id"    => $this->this_company_id,
                "sample.deletedAt" => null,
            ];
        } else {
            $condition = [
                "sample.company_id"    => $this->this_company_id,
                "sample.deletedAt" => null,
                'sample.user_id' => $this->this_user_id
            ];
        }

        $addCondition = [
            "search"        => trim($this->request->getGet("search")),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "status"      => $this->request->getGet("status"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $salesData = $this->sampleModel->getList($condition, $addCondition, $limit, $offset);

        $dataSales = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($salesData['data'] as $data) {
            array_push($dataSales, [
                "no"                        => $no++,
                "id"                        => encrypt($data->id),
                "no_sample"                 => $data->no_sample,
                "no_invoice"                => $data->no_invoice,
                "customer_name"             => $data->customer_name,
                "tanggal_invoice"           => date('d/m/Y', strtotime($data->tanggal_invoice)),
                "tanggal"                   => date('d/m/Y', strtotime($data->tanggal)),
                "total_berat_bersih"        => (float)$data->total_berat_bersih,
                "total_berat_kotor"         => (float)$data->total_berat_kotor,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $salesData['totalData'],
            "recordsFiltered"   => $salesData['totalFilteredData'],
            "data"              => $dataSales,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function createView()
    {
        $dataSatuan = $this->satuanModel->findAll();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataBarang = $this->barangMasterSalesModel
            ->where('company_id', $this->this_company_id)
            ->where('type_barang_sales', "EKSPOR")
            ->where('deletedAt', null)
            ->orderBy('barang_name', "asc")
            ->findAll();
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );

        $data = [
            'dataSatuan' => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            'dataBarang' => $dataBarang,
            "dataCustomer" => $dataCustomer
        ];

        return view('SalesInternasional/Sample/form', $data);
    }

    public function updateView($id)
    {
        $id = decrypt($id);
        $dataSample = $this->sampleModel->where('id', $id)->first();
        if ($dataSample == null) {
            return redirect()->to('sample-ekspor');
        }

        $dataSatuan = $this->satuanModel->findAll();
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataBarang = $this->barangMasterSalesModel
            ->where('company_id', $this->this_company_id)
            ->where('type_barang_sales', "EKSPOR")
            ->where('deletedAt', null)
            ->orderBy('barang_name', "asc")
            ->findAll();
        $dataBarangList = $this->sampleDetailModel->getSampleDetail($id);
        $dataCustomer = $this->customerModel->getCustomerEkspor(
            $this->this_user_id,
            $this->is_admin
        );

        $pickupDate = $via = $an = "";
        foreach ($dataBarangList as $d) {
            $pickupDate = $d['pickup_date'];
            $via = $d['via'];
            $an = $d['an'];
        }

        $data = [
            'dataSample' => $dataSample,
            'dataBarangList' => $dataBarangList,
            'dataSatuan' => $dataSatuan,
            "dataDivisi" => $dataDivisi,
            'dataBarang' => $dataBarang,
            "pickupDate" => $pickupDate,
            "via" => $via,
            "an" => $an,
            "dataCustomer" => $dataCustomer
        ];

        return view('SalesInternasional/Sample/form', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $dataSample = $this->sampleModel
            ->select('sample.*,customers.name AS customer_name')
            ->join('customers', 'customers.id = sample.customer_id', 'left')
            ->where('sample.id', $id)
            ->first();

        if ($dataSample == null) {
            return redirect()->to('sample-ekspor');
        }

        $dataBarangList = $this->sampleDetailModel->getSampleDetail($id);
        // $dataAdditionalItem = $this->sampleAdditionalModel
        //     ->select('
        //         sample_additional.additional_item, 
        //         SUM(sample_additional.qty_additional) AS total_qty_additional, 
        //         satuans.kode_satuan')
        //     ->join('satuans', 'satuans.id = sample_additional.satuan_additional', 'left')
        //     ->where('sample_additional.sample_id', $id)
        //     ->where('sample_additional.deletedAt', null)
        //     ->groupBy('sample_additional.additional_item')
        //     ->findAll();

        $company = $this->companyModel->where('id', $this->this_company_id)->first();

        $note = [
            'totalCols' => 10,
            'totalColsBeratBersih' => 7,
            'note' => false
        ];

        foreach ($dataBarangList as $d) {
            if ($d['note'] != null || !empty($d['note'])) {
                $note = [
                    'totalCols' => 10,
                    'totalColsBeratBersih' => 8,
                    'isNote' => true
                ];
                break;
            }
        }

        $data = [
            'dataSample' => $dataSample,
            'dataBarangList' => $dataBarangList,
            "company" => $company,
            'note' => $note
        ];

        $this->dompdf->loadHtml(view('SalesInternasional/Sample/print', $data));
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();

        $filename = $dataSample['no_sample'];
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // return response()->setJSON([
            //     '$_POST' => $_POST,
            //     'listBarang' => json_decode($_POST['listBarang'])
            // ]);

            $noSample = $this->request->getVar('no_sample');
            $tanggal = $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "";

            if ($noSample == "AUTO GENERATE") {
                $noSample = $this->getNo($tanggal);
            }

            if (!$this->checkNum($noSample, null)) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Number Sample Already Exists"
                ]);
            }

            $noInvoice = $this->salesOrderExportModel->generateCodePI($this->this_company_id);

            $id = $this->sampleModel->insert([
                'user_id' => $this->this_user_id,
                'company_id' => $this->this_company_id,
                'no_sample' => $noSample,
                "tanggal" => $tanggal,
                "tanggal_invoice" => $tanggal,
                "no_invoice" => $noInvoice, // auto generate
                "customer_id" => $this->request->getVar('customer_id'),
                'divisi_id' => $this->request->getVar('divisi_id'),
                'delivery' => trim($this->request->getVar('delivery')),
                'delivery_address' => trim($this->request->getVar('delivery_address')),
                'attn_no' => $this->request->getVar('attn_no'),
                'approved_by' => $this->request->getVar('approved_by'),
                'payment_term' => $this->request->getVar('payment_term'),
                'nb' =>  trim($this->request->getVar('nb')),
                'description_notes' => $this->request->getVar('description_notes'),
                'total_berat_bersih' => $this->request->getVar('total_berat_bersih'),
                'total_berat_kotor' => $this->request->getVar('total_berat_kotor'),
                'total_qty' => $this->request->getVar('total_qty')
            ]);

            $an = $this->request->getVar('an');
            $pickupDate = $this->request->getVar('pickup_date');
            $via = $this->request->getVar('via');

            foreach (json_decode($_POST['listBarang']) as $l) {
                $sample_detail_id = $this->sampleDetailModel->insert([
                    'sample_id' => $id,
                    'barang_master_sales_id' => $l->barang_master_sales_id,
                    'satuan_id' => $l->satuan_id,
                    'grade' => $l->grade,
                    'an' => $an,
                    'pickup_date' => $pickupDate,
                    'via' => $via,
                    'qty' => $l->qty,
                    'berat_kotor' => $l->berat_kotor,
                    'berat_bersih' => $l->berat_bersih,
                    'note' => trim($l->note),
                    'divisi_barang_id' => $l->divisi_barang_id
                ]);

                foreach ($l->list_additional as $la) {
                    $this->sampleAdditionalModel->insert([
                        'sample_id' => $id,
                        'sample_detail_id' => $sample_detail_id,
                        'additional_item' => $la->additional_item,
                        'qty_additional' => (float)$la->qty_additional,
                        'satuan_additional' => $la->satuan_additional
                    ]);
                }
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Data Created"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_token()
            ]);
        }
    }

    public function update()
    {
        $db = \Config\Database::connect();
        try {
            $id = decrypt($this->request->getVar('id'));

            $db->transBegin();
            $noSample = $this->request->getVar('no_sample');
            $tanggal = $this->request->getVar("tanggal") ? date_format(date_create_from_format("d/m/Y", $this->request->getVar("tanggal")), "Y-m-d") : "";

            if (!$this->checkNum($noSample, $id)) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Number Sample Already Exists"
                ]);
            }

            $this->sampleModel->update($id, [
                'company_id' => $this->this_company_id,
                'no_sample' => $noSample,
                "tanggal" => $tanggal,
                "customer_id" => $this->request->getVar('customer_id'),
                'divisi_id' => $this->request->getVar('divisi_id'),
                'delivery' => trim($this->request->getVar('delivery')),
                'delivery_address' => trim($this->request->getVar('delivery_address')),
                'attn_no' => $this->request->getVar('attn_no'),
                'approved_by' => $this->request->getVar('approved_by'),
                'payment_term' => $this->request->getVar('payment_term'),
                'nb' =>  trim($this->request->getVar('nb')),
                'description_notes' => $this->request->getVar('description_notes'),
                'total_berat_bersih' => $this->request->getVar('total_berat_bersih'),
                'total_berat_kotor' => $this->request->getVar('total_berat_kotor'),
                'total_qty' => $this->request->getVar('total_qty')
            ]);

            $an = $this->request->getVar('an');
            $pickupDate = $this->request->getVar('pickup_date');
            $via = $this->request->getVar('via');

            $this->sampleDetailModel->where('sample_id', $id)->delete(null, true);
            $this->sampleAdditionalModel->where('sample_id', $id)->delete(null, true);
            foreach (json_decode($_POST['listBarang']) as $l) {
                $sample_detail_id =  $this->sampleDetailModel->insert([
                    'sample_id' => $id,
                    'barang_master_sales_id' => $l->barang_master_sales_id,
                    'satuan_id' => $l->satuan_id,
                    'grade' => $l->grade,
                    'an' => $an,
                    'pickup_date' => $pickupDate,
                    'via' => $via,
                    'qty' => $l->qty,
                    'berat_kotor' => $l->berat_kotor,
                    'berat_bersih' => $l->berat_bersih,
                    'note' => trim($l->note),
                    'divisi_barang_id' => $l->divisi_barang_id
                ]);

                foreach ($l->list_additional as $la) {
                    $this->sampleAdditionalModel->insert([
                        'sample_id' => $id,
                        'sample_detail_id' => $sample_detail_id,
                        'additional_item' => $la->additional_item,
                        'qty_additional' => (float)$la->qty_additional,
                        'satuan_additional' => $la->satuan_additional
                    ]);
                }
            }

            $db->transCommit();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_token(),
                'message' => "Data Updated"
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'message' => $e->getMessage(),
                'status' => false,
                'token' => csrf_token()
            ]);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->sampleModel->delete($id);
        $this->sampleDetailModel->where('sample_id', $id)->delete(null, false);
        $this->sampleAdditionalModel->where('sample_id', $id)->delete(null, true);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_token(),
            'message' => "Data Deleted"
        ]);
    }

    private function checkNum($noSample, $id = null): bool
    {
        $builder = $this->sampleDetailModel
            ->where('no_sample', $noSample)
            ->where('company_id', $this->this_company_id);

        if (!empty($id)) {
            $builder->where('id !=', $id);
        }

        return $builder->countAllResults() === 0;
    }


    private function getNo($tanggal)
    {
        $tanggalArr = explode('-', $tanggal);
        $bulanF = $tanggalArr[1];
        $tahunF = date('y', strtotime($tanggal));
        $lastDayOfMonth = date('Y-m-t', strtotime($tanggal));
        $startDayOfMonth = date('Y-m') . "-01";

        $lastSample = $this->sampleModel
            ->where('tanggal >=', $startDayOfMonth)
            ->where('tanggal <=', $lastDayOfMonth)
            ->where('company_id', $this->this_company_id)
            // ->where('deletedAt', null)
            ->orderBy('createdAt', 'DESC')
            ->findAll();

        $nextSequence = count($lastSample) + 1;
        $formattedNumber = sprintf("%04d", $nextSequence);
        $templeate = "SAM/" . $bulanF . "/" . $tahunF . "/" . $formattedNumber;

        return $templeate;
    }
}
