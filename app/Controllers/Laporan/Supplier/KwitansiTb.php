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

        return view('Laporan/SupplierLokalBB/KwitansiTb/index', [
            'year' => $year,
            'month' => $month,
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

        $month = $this->request->getVar('month');
        $year = $this->request->getVar('year');

        $addCondition = [
            "search"    => $this->request->getGet("supplier_search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "tb_search" => $this->request->getGet("tb_search")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $this->supplierModel->getSupplierList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        $noKwitansi = '';
        foreach ($supplierData['data'] as $i => $data) {
            // GET KWITANSI TB
            $kwitansiTB = $this->supplierModel->getKwitansiTBBySupplier(
                $data->id,
                $this->request->getVar('year'),
                $this->request->getVar('month')
            );

            if ($kwitansiTB['total'] != 0) {
                if ($i == 0) {
                    $noKwitansi = "001/KTB/$month/$year";
                } else {
                    $noKwitansi = generateNoKwitansiTB($noKwitansi, $month, $year);
                }
            }

            if ($addCondition['tb_search'] == "1") {
                if ($kwitansiTB['total'] != 0) {
                    array_push($dataSupplier, [
                        "no"            => $no++,
                        "id"            => encrypt($data->id),
                        "name"          => $data->name,
                        "no_kwitansi_hash" => encrypt($noKwitansi),
                        "total"         => $kwitansiTB['total'] == 0 ? '-' : number_format($kwitansiTB['total'], 2),
                        "no_kwitansi"   => $kwitansiTB['total'] == 0 ? '-' : $noKwitansi,
                        "tanggal"       => $kwitansiTB['total'] == 0 ? '-' : $year . '-' . $month . '-' . date("t", strtotime("$year-$month-01")),
                        "is_print"      => $kwitansiTB['total'] == 0 ? '0' : '1',
                    ]);
                }
            } elseif ($addCondition['tb_search'] == "0") {
                if ($kwitansiTB['total'] == 0) {
                    array_push($dataSupplier, [
                        "no"            => $no++,
                        "id"            => encrypt($data->id),
                        "name"          => $data->name,
                        "no_kwitansi_hash" => encrypt($noKwitansi),
                        "total"         => $kwitansiTB['total'] == 0 ? '-' : number_format($kwitansiTB['total'], 2),
                        "no_kwitansi"   => $kwitansiTB['total'] == 0 ? '-' : $noKwitansi,
                        "tanggal"       => $kwitansiTB['total'] == 0 ? '-' : $year . '-' . $month . '-' . date("t", strtotime("$year-$month-01")),
                        "is_print"      => $kwitansiTB['total'] == 0 ? '0' : '1',
                    ]);
                }
            } else {
                array_push($dataSupplier, [
                    "no"            => $no++,
                    "id"            => encrypt($data->id),
                    "name"          => $data->name,
                    "no_kwitansi_hash" => encrypt($noKwitansi),
                    "total"         => $kwitansiTB['total'] == 0 ? '-' : number_format($kwitansiTB['total'], 2),
                    "no_kwitansi"   => $kwitansiTB['total'] == 0 ? '-' : $noKwitansi,
                    "tanggal"       => $kwitansiTB['total'] == 0 ? '-' : $year . '-' . $month . '-' . date("t", strtotime("$year-$month-01")),
                    "is_print"      => $kwitansiTB['total'] == 0 ? '0' : '1',
                ]);
            }
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

        $supplierID = decrypt($supplierID);
        $noKwitansi = decrypt($noKwitansi);

        $yearMonthSplit = explode('-', $yearMonth);
        $year = $yearMonthSplit[0];
        $month = $yearMonthSplit[1];
        $noKwitansi = \str_replace('-', '/', $noKwitansi);

        $kwitansiTB = $supplierModel->getKwitansiTBBySupplier(
            $supplierID,
            $year,
            $month
        );

        $kwitansiTBMerged = array();
        $namaBarang = "";
        $kodeSatuan = "";
        $qtyTotal = 0;
        $hargaBulananTotal = 0;
        $pphTotal = 0;
        $hargaBulananPphTotal = 0;

        foreach ($kwitansiTB['all'] as $k) {
            $namaBarang = $k['nama_barang'];
            $kodeSatuan = $k['kode_satuan'];

            $qtyTotal += $k['qty'];
            $hargaBulananTotal += $k['harga_bulanan'];
            $pphTotal += $k['pph'];
            $hargaBulananPphTotal += $k['harga_bulanan_pph'];
        }

        $kwitansiTBMerged[] = [
            'nama_barang' => $namaBarang,
            'kode_satuan' => $kodeSatuan,
            'qty'          => $qtyTotal,
            'harga_bulanan' =>  $hargaBulananTotal,
            'pph'   => $pphTotal,
            'harga_bulanan_pph' => $hargaBulananPphTotal
        ];

        $kwitansiTBFinal = [
            'all' => $kwitansiTBMerged,
            'supplier' => $kwitansiTB['supplier'],
            'total' => $kwitansiTB['total']
        ];

        // dd($kwitansiTB);

        $company = $companyModel->where('id', $this->this_company_id)->where('deletedAt', null)->first();

        $data = [
            'year' => $year,
            'month' => $month,
            'tanggal' => $tanggal,
            'noKwitansi' => $noKwitansi,
            'company' => $company,
            'kwitansis' => $kwitansiTBFinal,
            'provinsi' => $provinsiModel->where('id', $company['province_id'])->first()
        ];

        $dompdf->loadHtml(view('Laporan/SupplierLokalBB/KwitansiTb/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Cetak Kwitansi TB ", array("Attachment" => false));

        exit(0);
    }
}
