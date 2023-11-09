<?php

namespace App\Controllers\Supplier;

use App\Controllers\BaseController;
use App\Models\CompaniesModel;
use App\Models\ProvincesModel;
use App\Models\SupplierModel;
use Dompdf\Dompdf;

class KwitansiTb extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
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

        return view('SalesLokal/KwitansiTb/index', [
            'year' => $year,
            'month' => $month,
            'data' => $res
        ]);
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

        $dompdf->loadHtml(view('SalesLokal/KwitansiTb/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Cetak Kwitansi TB ", array("Attachment" => false));

        exit(0);
    }
}
