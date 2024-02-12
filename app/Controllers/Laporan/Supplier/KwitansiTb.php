<?php

namespace App\Controllers\Laporan\Supplier;

use App\Controllers\BaseController;
use App\Models\CompaniesModel;
use App\Models\ProvincesModel;
use App\Models\SupplierModel;
use Dompdf\Dompdf;

class KwitansiTb extends BaseController
{
    protected $this_company_id;
    protected $supplierModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        // get data $_GET
        $year = ($this->request->getVar("year") == "") ? date("Y") : $this->request->getGet("year");
        $month = ($this->request->getVar("month") == "") ? date("m") : $this->request->getGet("month");

        $supplierModel = new SupplierModel();

        $res = [];
        $supplierModel = new SupplierModel();
        $supplier = $supplierModel->getSupplierByType('BAHAN BAKU');

        $noKwitansi = '';
        foreach ($supplier as $i => $s) {
            $kwitansiTB = $supplierModel->getKwitansiTB($s['id'], $year, $month);

            if ($kwitansiTB['hargaBulananWithQtyPphTotal'] != 0) {
                if ($i == 0) {
                    $noKwitansi = "001/KTB/$month/$year";
                } else {
                    $noKwitansi = generateNoKwitansiTB($noKwitansi, $month, $year);
                }
                $noKwitansi = sprintf($noKwitansi);
                $res[] = [
                    'id' => $s['id'],
                    'supplier' => $s['name'],
                    'total' => $kwitansiTB['hargaBulananWithQtyPphTotal'],
                    'noKwitansi' => $noKwitansi,
                    "tanggal" => $year . '-' . $month . '-' . date("t", strtotime("$year-$month-01")),
                ];
            }
        }



        return view('Laporan/SupplierLokalBB/KwitansiTb/index', [
            'year' => $year,
            'month' => $month,
            'data' => $res
        ]);
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "type"          => "BAHAN BAKU"
        ];

        $condition = [
            "suppliers.type"        => "BAHAN BAKU",
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->supplierModel->getSupplierList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            array_push($dataSupplier, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "name"          => $data->name,
                "total"         => "",
                "no_kwitansi"   => "",
                "tanggal"       => "",
                "is_print"      => ""
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $supplierData['totalData'],
            "recordsFiltered"   => $supplierData['totalFilteredData'],
            "data"              => $dataSupplier,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function exportPDFKwitansiTB($supplierID, $yearMonth, $tanggal, $noKwitansi)
    {
        $dompdf = new Dompdf();
        $companyModel = new CompaniesModel();
        $supplierModel = new SupplierModel();
        $provinsiModel = new ProvincesModel();

        $yearMonthSplit = explode('-', $yearMonth);
        $year = $yearMonthSplit[0];
        $month = $yearMonthSplit[1];
        $noKwitansi = \str_replace('-', '/', $noKwitansi);

        $kwitansiTB = $supplierModel->getKwitansiTB($supplierID, $year, $month);
        $company = $companyModel->where('id', $this->this_company_id)->where('deletedAt', null)->first();

        $data = [
            'year' => $year,
            'month' => $month,
            'tanggal' => $tanggal,
            'noKwitansi' => $noKwitansi,
            'company' => $company,
            'kwitansi' => $kwitansiTB,
            'provinsi' => $provinsiModel->where('id', $company['province_id'])->first()
        ];

        $dompdf->loadHtml(view('Laporan/SupplierLokalBB/KwitansiTb/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Cetak Kwitansi TB ", array("Attachment" => false));

        exit(0);
    }
}
