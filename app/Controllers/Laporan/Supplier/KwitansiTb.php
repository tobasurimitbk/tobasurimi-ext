<?php

namespace App\Controllers\Laporan\Supplier;

use App\Controllers\BaseController;
use App\Models\CompaniesModel;
use App\Models\NomorKwitansiBulananModel;
use App\Models\ProvincesModel;
use App\Models\SupplierModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;

class KwitansiTb extends BaseController
{
    protected $this_company_id;
    protected $supplierModel;
    protected $nomorKwitansuBulananModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->supplierModel = new SupplierModel();
        $this->nomorKwitansuBulananModel = new NomorKwitansiBulananModel();
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
            "idCompany"     => $this->this_company_id,
            "status"      => $this->request->getGet("status")
        ];

        $condition = [
            "suppliers.deletedAt"    => null,
            "rm_purchase_orders.deletedAt" => null,
            "suppliers.type" => "BAHAN BAKU",
            "suppliers.company_id" => $this->this_company_id
        ];

        $year = $this->request->getVar('year');
        $month = $this->request->getVar('month');

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "year_month"    => "$year . '-' . $month",
            "tb_search"     => $this->request->getVar('tb_search')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $invData = $this->supplierModel->getSupplierListKwitansiBulanan(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $dataInv = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($invData['data'] as $data) {
            $noKwitansi = '';
            $tanggal = '';

            if ($data['total_bulanan'] != 0 && $data['total_bulanan'] != null) {
                $noKwitansiStatis = $this->nomorKwitansuBulananModel
                    ->where('year_month', $year . "-" . $month)
                    ->where('supplier_id', $data['id'])
                    ->first();
                $noKwitansi = $noKwitansiStatis == null ? "" : $noKwitansiStatis['no_kwitansi'];
                $tanggal = $noKwitansiStatis == null ? "" : $noKwitansiStatis['tanggal'];
            }

            array_push($dataInv, [
                "no"                => $no++,
                "id"                => encrypt($data['id']),
                "name"              => $data['name'],
                "no_kwitansi_hash"  => encrypt($noKwitansi),
                "total"             => number_format($data['total_bulanan'], 2),
                "no_kwitansi"       => $noKwitansi,
                "tanggal"           => $tanggal,
            ]);
        }


        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $invData['totalData'],
            "recordsFiltered"   => $invData['totalFilteredData'],
            "data"              => $dataInv,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function allBackup()
    {
        $limit = intval($this->request->getGet("length"));
        $offset = intval($this->request->getGet("start"));
        $currentPage = ($offset / $limit) + 1;

        $payload = [
            "pageSize"      => $limit,
            "currentPage"   => $currentPage,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "type"          => "BAHAN BAKU"
        ];

        // $condition = [
        //     "suppliers.type"        => "BAHAN BAKU",
        //     "suppliers.company_id"  => $this->this_company_id
        // ];

        $month = $this->request->getVar('month');
        $year = $this->request->getVar('year');

        $addCondition = [
            "search"    => $this->request->getGet("supplier_search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType"),
            "tb_search" => $this->request->getGet("tb_search")
        ];

        // Ambil semua data supplier tanpa pagination
        $supplierData = $this->supplierModel->getSupplierByType("BAHAN BAKU"); // Hapus $limit & $offset
        $allSuppliers = $supplierData;

        $filteredData = [];
        $noKwitansi = '';

        foreach ($allSuppliers as $i => $data) {
            $kwitansiTB = $this->supplierModel->getKwitansiTBBySupplier(
                $data['id'],
                $year,
                $month
            );

            $totalTB = $kwitansiTB['total'];
            $masuk = false;

            // Filter tb_search manual
            if ($addCondition['tb_search'] === "1" && $totalTB != 0) {
                $masuk = true;
            } elseif ($addCondition['tb_search'] === "0" && $totalTB == 0) {
                $masuk = true;
            } elseif (!isset($addCondition['tb_search']) || $addCondition['tb_search'] === '') {
                $masuk = true;
            }

            if ($masuk) {
                // Generate nomor kwitansi
                if ($totalTB != 0) {
                    $noKwitansi = ($noKwitansi == '') ? "001/KTB/$month/$year" : generateNoKwitansiTB($noKwitansi, $month, $year);
                }

                $filteredData[] = [
                    "id"                => encrypt($data['id']),
                    "name"              => $data['name'],
                    "no_kwitansi_hash"  => encrypt($noKwitansi),
                    "total"             => $totalTB == 0 ? '-' : number_format($totalTB, 2),
                    "no_kwitansi"       => $totalTB == 0 ? '-' : $noKwitansi,
                    "tanggal"           => $totalTB == 0 ? '-' : $year . '-' . $month . '-' . date("t", strtotime("$year-$month-01")),
                    "is_print"          => $totalTB == 0 ? '0' : '1'
                ];
            }
        }

        // Pagination manual setelah filter
        $paginatedData = array_slice($filteredData, $offset, $limit);
        $no = $offset + 1;

        foreach ($paginatedData as &$row) {
            $row['no'] = $no++;
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => count($filteredData), // semua data setelah filter
            "recordsFiltered"   => count($filteredData),
            "data"              => $paginatedData,
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

    public function printAllKwitansiTB()
    {
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');
        $kertas = $this->request->getGet('kertas');

        $dataResult = $this->getDataKwintansiTb(
            $month,
            $year
        );

        if (empty($dataResult)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk dicetak.');
        }

        if ($kertas == "kasir") {
            $urlView = "Laporan/SupplierLokalBB/KwitansiTb/print-all-kasir";
        } else {
            $urlView = "Laporan/SupplierLokalBB/KwitansiTb/print-all-continous";
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view($urlView, ['dataResult' => $dataResult]));
        $dompdf->setPaper('F4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Kwitansi_TB_Bulanan_$month-$year.pdf", ["Attachment" => false]);
        exit;
    }

    public function getListGenerateNoKwintansiTb()
    {
        try {
            $yearMonth = $this->request->getVar('year_month');
            $yearMonthSplit = explode('-', $yearMonth);
            $year = $yearMonthSplit[0];
            $month = $yearMonthSplit[1];

            $dataResult = $this->getDataKwintansiTbGenerate(
                $month,
                $year
            );

            $dataResultFinal = [];

            foreach ($dataResult as $d) {
                $supplierId = $d['kwintansi']['supplier']['id'];
                $supplierName = $d['kwintansi']['supplier']['name'];
                $noKwitansi = $d['noKwitansi'];
                $yearMonth = $d['year'] . "-" . $d['month'];
                $total = (float)$d['kwintansi']['harga_bulanan_pph'];
                $tanggal = $d['tanggal'];

                $dataResultFinal[] = [
                    'supplier_id'   => $supplierId,
                    'supplier_name' => $supplierName,
                    'no_kwitansi'   => $noKwitansi,
                    'year_month'    => $yearMonth,
                    'total'         => $total,
                    'tanggal'       => date('d/m/Y', strtotime($tanggal)),
                    'company_id' => $this->this_company_id
                ];
            }

            usort($dataResultFinal, function ($a, $b) {
                return strcmp($a['no_kwitansi'], $b['no_kwitansi']);
            });

            return response()->setJSON([
                'data' => $dataResultFinal,
                'status' => true,
                'token' => csrf_hash()
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'message' => $e->getMessage(),
                'token' => csrf_hash(),
                'status' => false
            ]);
        }
    }

    public function generateNoKwitansiAction()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $listData = json_decode($this->request->getPost('listData'), true);

            if (empty($listData) || !is_array($listData)) {
                throw new \Exception('Data tidak valid atau kosong.');
            }

            // 🔍 Cek duplikat No Kwitansi
            $noKwitansiArr = array_column($listData, 'no_kwitansi');
            $duplicates = array_unique(array_diff_assoc($noKwitansiArr, array_unique($noKwitansiArr)));

            if (!empty($duplicates)) {
                return $this->response->setJSON([
                    'token' => csrf_hash(),
                    'status' => false,
                    'message' => 'Terdapat No Kwitansi yang duplikat yakni: ' . implode(', ', $duplicates),
                ]);
            }

            foreach ($listData as $l) {
                if (is_object($l)) {
                    $l = (array) $l;
                }

                unset($l['supplier_name']);
                unset($l['total']);

                if (empty($l['supplier_id']) || empty($l['year_month'])) {
                    continue; // skip data tidak lengkap
                }

                // 🔹 Hapus data lama
                $this->nomorKwitansuBulananModel
                    ->where('supplier_id', $l['supplier_id'])
                    ->where('year_month', $l['year_month'])
                    ->delete(null, true);

                // 🔹 Format tanggal ke YYYY-MM-DD
                if (!empty($l['tanggal'])) {
                    $l['tanggal'] = date("Y-m-d", strtotime(str_replace("/", "-", $l['tanggal'])));
                } else {
                    $l['tanggal'] = null;
                }

                // 🔹 Insert data baru
                $this->nomorKwitansuBulananModel->insert($l);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => 'No Kwitansi berhasil digenerate.'
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    private function getDataKwintansiTb($month, $year)
    {
        $condition = [
            'MONTH(rm_purchase_orders.po_date)' => $month,
            'YEAR(rm_purchase_orders.po_date)' => $year,
            'rm_purchase_orders.deletedAt' => null,
            'suppliers.deletedAt' => null,
            'suppliers.company_id' => $this->this_company_id
        ];

        $allSuppliers = $this->supplierModel->select('suppliers.*,SUM(dpp_bulanan)')
            ->join('rm_purchase_orders', 'rm_purchase_orders.supplier_id = suppliers.id', 'left')
            ->where($condition)
            ->orderBy('suppliers.name', 'asc')
            ->groupBy('suppliers.id')
            ->findAll();

        $dataResult = [];

        $company = (new CompaniesModel())->where('id', $this->this_company_id)->where('deletedAt', null)->first();
        $provinsi = (new ProvincesModel())->where('id', $company['province_id'])->first();

        foreach ($allSuppliers as $data) {
            $kwitansiTB = $this->supplierModel->getKwitansiTBBySupplier(
                $data['id'],
                $year,
                $month
            );

            $totalTB = $kwitansiTB['total'];

            if ($totalTB != 0) {
                // Get No Kwitansi First
                $noKwitansiStatis = $this->nomorKwitansuBulananModel
                    ->where('year_month', $year . "-" . $month)
                    ->where('supplier_id', $data['id'])
                    ->first();

                if ($noKwitansiStatis != null) {
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

                    $kwitansiTBFinal = [
                        'nama_barang' => $namaBarang,
                        'kode_satuan' => $kodeSatuan,
                        'qty' => $qtyTotal,
                        'harga_bulanan' => $hargaBulananTotal,
                        'pph' => $pphTotal,
                        'harga_bulanan_pph' => $hargaBulananPphTotal,
                        'supplier' => $kwitansiTB['supplier'],
                        'total' => $kwitansiTB['total']
                    ];

                    $data = [
                        'year' => $year,
                        'month' => $month,
                        'tanggal' => $noKwitansiStatis['tanggal'],
                        'noKwitansi' => $noKwitansiStatis['no_kwitansi'],
                        'company' => $company,
                        'kwintansi' => $kwitansiTBFinal,
                        'provinsi' => $provinsi
                    ];

                    array_push($dataResult, $data);
                }
            }
        }

        return $dataResult;
    }

    private function getDataKwintansiTbGenerate($month, $year)
    {
        $condition = [
            'MONTH(rm_purchase_orders.po_date)' => $month,
            'YEAR(rm_purchase_orders.po_date)' => $year,
            'rm_purchase_orders.deletedAt' => null,
            'suppliers.deletedAt' => null,
            'suppliers.company_id' => $this->this_company_id
        ];

        $allSuppliers = $this->supplierModel->select('suppliers.*,SUM(dpp_bulanan)')
            ->join('rm_purchase_orders', 'rm_purchase_orders.supplier_id = suppliers.id', 'left')
            ->where($condition)
            ->orderBy('suppliers.name', 'asc')
            ->groupBy('suppliers.id')
            ->findAll();

        $dataResult = [];

        $company = (new CompaniesModel())->where('id', $this->this_company_id)->where('deletedAt', null)->first();
        $provinsi = (new ProvincesModel())->where('id', $company['province_id'])->first();
        $noKwitansi = '';

        foreach ($allSuppliers as $data) {
            $kwitansiTB = $this->supplierModel->getKwitansiTBBySupplier(
                $data['id'],
                $year,
                $month
            );

            $totalTB = $kwitansiTB['total'];

            if ($totalTB != 0) {
                // Get No Kwitansi First
                $noKwitansiStatis = $this->nomorKwitansuBulananModel
                    ->where('year_month', $year . "-" . $month)
                    ->where('supplier_id', $data['id'])
                    ->first();

                if ($noKwitansiStatis == null) {
                    $noKwitansiMax = $this->nomorKwitansuBulananModel
                        ->where('year_month', $year . "-" . $month)
                        ->where('company_id', $this->this_company_id)
                        ->orderBy('no_kwitansi', "desc")
                        ->first();

                    if ($noKwitansiMax != null) {
                        $noKwitansi = generateNoKwitansiTB($noKwitansiMax['no_kwitansi'], $month, $year);
                    } else {
                        $noKwitansi = ($noKwitansi == '') ? "001/KTB/$month/$year" : generateNoKwitansiTB($noKwitansi, $month, $year);
                    }

                    $tanggal = date("Y-m-t", strtotime("$year-$month-01"));
                } else {
                    $noKwitansi = $noKwitansiStatis['no_kwitansi'];
                    $tanggal = $noKwitansiStatis['tanggal'];
                }

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

                $kwitansiTBFinal = [
                    'nama_barang' => $namaBarang,
                    'kode_satuan' => $kodeSatuan,
                    'qty' => $qtyTotal,
                    'harga_bulanan' => $hargaBulananTotal,
                    'pph' => $pphTotal,
                    'harga_bulanan_pph' => $hargaBulananPphTotal,
                    'supplier' => $kwitansiTB['supplier'],
                    'total' => $kwitansiTB['total']
                ];

                $data = [
                    'year' => $year,
                    'month' => $month,
                    'tanggal' => $tanggal,
                    'noKwitansi' => $noKwitansi,
                    'company' => $company,
                    'kwintansi' => $kwitansiTBFinal,
                    'provinsi' => $provinsi
                ];

                array_push($dataResult, $data);
            }
        }

        return $dataResult;
    }
}
