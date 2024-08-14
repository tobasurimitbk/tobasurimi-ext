<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\BC30Model;
use App\Models\CeisaSettingModel;
use App\Models\MetadataModel;
use App\Models\StockDetail2Model;
use App\Models\StuffingInternasionalModel;
use App\Models\StuffingLokalModel;
use App\Models\SalesOrderModel;
use App\Models\SalesOrderDetailModel;
use App\Models\SalesOrderExportModel;
use App\Models\SalesOrderExportDetailModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\PengusahaTPBModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\CountryModel;
use App\Models\HsCodesModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54

class BC30 extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $bc30Model;
    protected $metaDataModel;
    protected $stuffingLokalModel;
    protected $stuffingInternasionalModel;
    protected $stockDetail2Model;
    protected $salesOrderModel;
    protected $salesOrderDetailModel;
    protected $salesOrderExportModel;
    protected $salesOrderExportDetailModel;
    protected $kantorBeaCukaiModel;
    protected $pengusahaTPBModel;
    protected $barangMasterSpesifikasiModel;
    protected $countryModel;
    protected $hsCodeModel;

    public function __construct()
    {
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->bc30Model = new BC30Model();
        $this->metaDataModel = new MetadataModel();
        $this->stuffingLokalModel = new StuffingLokalModel();
        $this->stuffingInternasionalModel = new StuffingInternasionalModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->salesOrderModel = new SalesOrderModel();
        $this->salesOrderDetailModel = new SalesOrderDetailModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->salesOrderExportDetailModel = new SalesOrderExportDetailModel();
        $this->kantorBeaCukaiModel = new KantorBeaCukaiModel();
        $this->pengusahaTPBModel = new PengusahaTPBModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->countryModel = new CountryModel();
        $this->hsCodeModel = new HsCodesModel();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first(),
        ];

        return view('BeaCukai/bc-30/index', $data);
    }


    public function online()
    {
        $data = [
            'baseUrl' => $this->metaDataModel->where('name', "Base Url BC")->first()['value']
        ];

        return view('BeaCukai/bc-30/online', $data);
    }

    public function allOnline()
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);
        $dataOnline = $beacukaiApi->getListStatusResponseAll();

        $newDataResult = [];
        foreach ($dataOnline->dataRespon as $d) {
            if ($d->kodeDokumen == "30") {
                $newDataResult[] = $d;
            }
        }
        $dataOnline->dataRespon = $newDataResult;

        if ($dataOnline->status == false) {
            return response()->setJSON($dataOnline);
        } else {
            return response()->setJSON([
                'data' => $dataOnline,
                'status' => true
            ]);
        }
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "company_id"    => $this->this_company_id,
            "type"          => "BC 3.0"
        ];

        $condition = [
            "bc_30.company_id"  => $this->this_company_id,
            "bc_30.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC30" => $this->request->getGet("mulaiTanggalBC30"),
            "selesaiTanggalBC30" => $this->request->getGet('selesaiTanggalBC30'),
            "noAju" => $this->request->getGet('noAju'),
            "tipeSalesOrder" => $this->request->getGet('tipeSalesOrder')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc30Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $detail = $this->bc30Model->detail($data->id);
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "tipe_sales_order"      => $data->tipe_sales_order,
                "no_order_form"         => $detail != null ? $detail['no_sales_order'] : "-",
                "no_stuffing"           => $detail != null ? $detail['no_stuffing'] : "-",
                "customer_name"         => $detail != null ? $detail['nama_customer'] : "-",
                "no_aju"                => $data->no_aju . " / " . $data->no_daftar,
                "tanggal_bc_30"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
                "status_posting"        => $data->status_posting,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }

    public function create()
    {
        $data = [
            'noAju' => $this->generateNomorAju()
        ];
        return view('BeaCukai/bc-30/form', $data);
    }

    public function createAction()
    {
        $this->bc30Model->insert([
            'company_id' => $this->this_company_id,
            'sales_order_id' => $this->request->getVar('sales_order_id'),
            'tipe_sales_order' => $this->request->getVar('tipe_sales_order'),
            'no_aju' => $this->request->getVar('no_aju'),
            'no_daftar' => $this->request->getVar('no_daftar'),
            'status_posting' => '0',
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 3.0 Berhasil Disimpan"
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->bc30Model->update($id, [
            'company_id' => $this->this_company_id,
            'sales_order_id' => $this->request->getVar('sales_order_id'),
            'tipe_sales_order' => $this->request->getVar('tipe_sales_order'),
            'no_aju' => $this->request->getVar('no_aju'),
            'no_daftar' => $this->request->getVar('no_daftar'),
            'status_posting' => '0',
        ]);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 3.0 Berhasil Diupdate"
        ]);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->detail($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $data = [
            'noAju' => $this->generateNomorAju(),
            'bc30' => $bc30
        ];

        return view('BeaCukai/bc-30/form', $data);
    }

    public function checkNoAju()
    {
        $id = decrypt($this->request->getVar('id'));
        $noAju = $this->request->getVar('no_aju');
        $isUsed = true;

        if (!empty($this->request->getVar('id'))) {
            // UPDATE
            $first = $this->bc30Model
                ->where('company_id', $this->this_company_id)
                ->where(
                    'no_aju',
                    $noAju
                )
                ->where('id != ', $id)
                ->first();

            if ($first != null) {
                $isUsed = false;
            }
        } else {
            // CREATE
            $first = $this->bc30Model
                ->where('company_id', $this->this_company_id)
                ->where(
                    'no_aju',
                    $noAju
                )
                ->first();

            if ($first != null) {
                $isUsed = false;
            }
        }

        if (!$isUsed) {
            return response()->setJSON([
                'status' => false,
                'message' => "No aju sudah digunakan"
            ]);
        } else {
            return response()->setJSON([
                'status' => true,
                'message' => "No aju tersedia"
            ]);
        }
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->bc30Model->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 3.0 Berhasil Dihapus"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->bc30Model->update($id, ['status_posting' => '1']);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 3.0 Berhasil Diposting"
        ]);
    }

    public function dropdownSalesOrder()
    {
        $tipeSalesOrder = $this->request->getVar('tipe_sales_order');
        $result = $this->bc30Model->getListSalesOrder(
            $this->this_company_id,
            $tipeSalesOrder
        );

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getListBarang()
    {
        $tipeSalesOrder = $this->request->getVar('tipe_sales_order');
        $salesOrderId = $this->request->getVar('sales_order_id');

        if (empty($tipeSalesOrder) || empty($salesOrderId)) {
            return response()->setJSON([
                'data' => [],
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
        $result = $this->bc30Model->getListBarang(
            $salesOrderId,
            $tipeSalesOrder
        );

        return response()->setJSON([
            'data' => $result,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function generateNomorAju()
    {
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $kodeDokumenbc30Static = $this->metaDataModel->where('name', "Kode BC30 Static")->first();

        $kodeKantorStatic = $ceisaSetting['kode_kantor_pabean'];
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = "";

        $bc30Last = $this->bc30Model->orderBy('createdAt', "DESC")->limit(1)->first();

        if ($bc30Last == null) {
            $sequenceNoUrutPengajuan = "000001";
        } else {
            if ($bc30Last['no_aju'] == null) {
                $sequenceNoUrutPengajuan = "000001";
            } else {
                // Buatkan auto increment
                $arrNo = explode('-', $bc30Last['no_aju']);
                $lastNomor = $arrNo[3];
                // lakukan increment
                $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
                $sequenceNoUrutPengajuan = $nextNomor;
            }
        }

        return $kodeDokumenbc30Static['value'] . '-' . $kodeKantorStatic . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan;
    }
    public function viewOutstanding()
    {
        return view('BeaCukai/bc-30/bc30outstanding');
    }

    public function allOutstanding()
    {
        $bc30Data = $this->bc30Model->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();
        $salesOrderData = $this->salesOrderModel->where('id_company', $this->this_company_id)->where('deletedAt', null)->findAll();
        $salesOrderExportData = $this->salesOrderExportModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->findAll();

        $salesOrderIdUse = [];
        $allSalesOrderId = [];
        $salesOrderIdNotUse = [];
        $salesOrderExportIdUse = [];
        $salesOrderExportIdNotUse = [];
        $allSalesOrderExportId = [];

        foreach ($salesOrderData as $s) {
            array_push($allSalesOrderId, $s['id']);
        }

        foreach ($salesOrderExportData as $e) {
            array_push($allSalesOrderExportId, $e['sales_order_export_id']);
        }

        foreach ($bc30Data as $b) {
            if ($b['tipe_sales_order'] == "LOKAL") {
                array_push($salesOrderIdUse, $b['id']);
            } elseif ($b['tipe_sales_order'] == "INTERNASIONAL") {
                array_push($salesOrderExportIdUse, $b['id']);
            }
        }
        $salesOrderIdNotUse = array_diff($allSalesOrderId, $salesOrderIdUse);
        $salesOrderExportIdNotUse = array_diff($allSalesOrderExportId, $salesOrderExportIdUse);

        $list = [];

        foreach ($salesOrderIdNotUse as $idLokal) {
            $so = $this->salesOrderModel
                ->select('
                sales_order.id as sales_order_id,
                no_sales_order,
                no_surat_jalan,
                no_stuffing,
                customers.name,    
                ')
                ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order.surat_jalan_so_id', 'left')
                ->join('stuffing_lokal', 'stuffing_lokal.sales_order_id = sales_order.id', 'left')
                ->join('customers', 'customers.id = sales_order.id_customer', 'left')
                ->where('sales_order.id', $idLokal)
                ->first();

            $sod = $this->salesOrderDetailModel
                ->select('count(*) as jumlah_barang, sum(amount) as total_harga')
                ->where('id_sales_order', $idLokal)
                ->first();

            if ($so != null) {
                array_push($list, [
                    'id' => $so['sales_order_id'],
                    'tipe_sales_order' => 'LOKAL',
                    'no_sales_order' => $so['no_sales_order'],
                    'no_surat_jalan' => empty($so['no_surat_jalan']) ? "-" : "{$so['no_surat_jalan']}",
                    'no_stuffing' => $so['no_stuffing'],
                    'customers' => $so['name'],
                    'jumlah_barang' => $sod['jumlah_barang'],
                    'total_harga' => number_format($sod['total_harga'], 2)
                ]);
            }
        }

        foreach ($salesOrderExportIdNotUse as $idExport) {
            $soe = $this->salesOrderExportModel
                ->select('
                    sales_order_export.sales_order_export_id,
                    sales_order_export_no,
                    no_stuffing,
                    companies.company,    
                    ')
                // ->join('surat_jalan_so', 'surat_jalan_so.id = sales_order.surat_jalan_so_id', 'left')
                ->join('stuffing_internasional', 'stuffing_internasional.sales_order_export_id = sales_order_export.sales_order_export_id', 'left')
                ->join('companies', 'companies.id = sales_order_export.company_id', 'left')
                ->where('sales_order_export.sales_order_export_id', $idExport)
                ->first();
            $soed = $this->salesOrderExportDetailModel
                ->select('count(*) as jumlah_barang, sum(harga_barang) as total_harga')
                ->where('sales_order_export_id', $idExport)
                ->first();
            if ($soe != null) {
                array_push($list, [
                    'tipe_sales_order' => 'INTERNASIONAL',
                    'no_sales_order' => $soe['sales_order_export_no'],
                    'no_surat_jalan' => '-',
                    'no_stuffing' => $soe['no_stuffing'],
                    'customers' => $soe['company'],
                    'jumlah_barang' => $soed['jumlah_barang'],
                    'total_harga' => number_format($soed['total_harga'], 2)
                ]);
            }
        }


        return json_encode($list);
    }


    public function OutstandingSheet()
    {
        $list = json_decode($this->allOutstanding());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'No.')
            ->setCellValue('B1', 'Tipe Sales Order')
            ->setCellValue('C1', 'No Sales Order')
            ->setCellValue('D1', 'No Surat Jalan')
            ->setCellValue('E1', 'No Stuffing')
            ->setCellValue('F1', 'Customer')
            ->setCellValue('G1', 'Jumlah Barang')
            ->setCellValue('H1', 'Nilai Barang');
        $no = 1;
        $column = 2;

        foreach ($list as $l) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $column, $no++)
                ->setCellValue('B' . $column,  $l->tipe_sales_order)
                ->setCellValue('C' . $column,  $l->no_sales_order)
                ->setCellValue('D' . $column,  $l->no_surat_jalan)
                ->setCellValue('E' . $column,  $l->no_stuffing)
                ->setCellValue('F' . $column,  $l->customers)
                ->setCellValue('G' . $column,  $l->jumlah_barang)
                ->setCellValue('H' . $column,  $l->total_harga);
            $column++;
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap BC30';
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Outstanding-BC-3.0';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }

    //kirim ceisa
    public function kirimCeisa($id)
    {
        $id = decrypt($id);
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $beacukaiApi = new BeaCukaiApi($this->akunCeisa['username'], $this->akunCeisa['password']);


        $res = $beacukaiApi->kirimDokumenBC($payload, false);
        if ($res['status'] == false) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'status' => false,
                'message' => "Gagal Kirim Ceisa Karena : " . $res['message'],
            ]);
        }
        // // UPDATE STATUS
        $this->bc30Model->set('status_dokumen', "Sudah Kirim")->where('id', $id)->update();

        return response()->setJSON([
            'token' => csrf_hash(),
            'status' => true,
            'message' => "Dokumen BC 2.7 Berhasil Diposting",
            'res' => $res
        ]);
    }
    private function setFlashDataNavigatorSession($id)
    {
        $isCompleteFormHeader = $this->bc30Model->isCompleteFormHeader($id);
        $isCompleteFormEntitas = $this->bc30Model->isCompleteFormEntitas($id);
        $isCompleteFormDokumen = $this->bc30Model->isCompleteFormDokumen($id);
        $isCompleteFormPengangkut = $this->bc30Model->isCompleteFormPengangkut($id);
        $isCompleteFormPetiKemas = $this->bc30Model->isCompleteFormPetiKemas($id);
        $isCompleteFormTransaksi = $this->bc30Model->isCompleteFormTransaksi($id);
        $isCompleteFormBarang = $this->bc30Model->isCompleteFormBarang($id);
        $isCompleteFormPernyataan = $this->bc30Model->isCompleteFormPernyataan($id);


        session()->setFlashdata('isCompleteFormHeader', $isCompleteFormHeader);
        session()->setFlashdata('isCompleteFormEntitas', $isCompleteFormEntitas);
        session()->setFlashdata('isCompleteFormDokumen', $isCompleteFormDokumen);
        session()->setFlashdata('isCompleteFormPengangkut', $isCompleteFormPengangkut);
        session()->setFlashdata('isCompleteFormPetiKemas', $isCompleteFormPetiKemas);
        session()->setFlashdata('isCompleteFormTransaksi', $isCompleteFormTransaksi);
        session()->setFlashdata('isCompleteFormBarang', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPungutan', $isCompleteFormBarang);
        session()->setFlashdata('isCompleteFormPernyataan', $isCompleteFormPernyataan);

        if (
            $isCompleteFormHeader && $isCompleteFormEntitas &&
            $isCompleteFormEntitas && $isCompleteFormDokumen && $isCompleteFormPengangkut &&
            $isCompleteFormPetiKemas && $isCompleteFormTransaksi
        ) {
            $this->bc30Model->set('status_dokumen', "Siap Kirim")->where('id', $id)->update();
        } else {
            $this->bc30Model->set('status_dokumen', "Belum Lengkap")->where('id', $id)->update();
        }
    }

    //CEISA ROUTER
    public function header($id)
    {
        $id = decrypt($id);

        $bc30 = $this->bc30Model->find($id);
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();


        $this->setFlashDataNavigatorSession($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $data = [
            'bc30' => $bc30,
            'noAju' => $bc30 == null ? $this->generateNomorAju() : $bc30['no_aju'],
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'kodeLokasiBayar' => $this->metaDataModel->where('name', "KODE LOKASI BAYAR")->findAll(),
            'kodeTujuanTpb' => $this->metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTujuanPengiriman' => $this->metaDataModel->where('name', "Kode Tujuan Pengiriman BC")->like('value', 40)->where('deletedAt', null)->findAll(),
            'kodeCaraBayar' => $this->metaDataModel->where('name', "CARA BAYAR")->findAll(),
            'selectedKantor' => $ceisaSetting['kode_kantor_pabean'],
            'payload' => json_decode($bc30['payload'])
        ];


        return view('BeaCukai/bc-30/form-header', $data);
    }

    public function updateHeader()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);

        $payload->asalData = "S";
        $payload->nomorAju =  str_replace('-', '', $this->request->getVar('nomorAju'));
        $payload->tanggalAju = getDateFromNomorAju($this->request->getVar('nomorAju'));
        $payload->kodeKantor = $this->request->getVar('kodeKantor');
        $payload->kodeJenisEkspor = $this->request->getVar('jenisEkspor');
        $payload->kodeKategoriEkspor = $this->request->getVar('kategoriEkspor');
        $payload->kodeKantorEkspor = $this->request->getVar('kodeKantorEkspor');
        $payload->kodeCaraDagang = $this->request->getVar('caraDagang');
        $payload->kodeCaraBayar = $this->request->getVar('caraBayar');
        $payload->flagMigas = $this->request->getVar('komoditi');
        $payload->flagCurah = $this->request->getVar('curah');
        $payload->seri = 1;


        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Header berhasil disimpan"
        ]);
    }

    public function entitas($id)
    {
        $id = decrypt($id);

        $bc30 = $this->bc30Model->find($id);
        $pengusahaTPB = $this->pengusahaTPBModel->where('company_id', $this->this_company_id)->findAll();


        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);

        $payload = json_decode($bc30['payload']);
        // JIKA MASIH KOSONG SET DULU BOSQ
        if (!is_array($payload->entitas)) {
            $payload->entitas = [
                [
                    //eksportir
                    "alamatEntitas" => "",
                    "kodeEntitas" => "2",
                    "nibEntitas" => "",
                    "kodeJenisIdentitas" => "",
                    "namaEntitas" => "",
                    "nomorIdentitas" => "",
                    "seriEntitas" => "1",
                ],
                [
                    //penerima

                    "alamatEntitas" => "",
                    "kodeEntitas" => '8',
                    "kodeNegara" => "",
                    "namaEntitas" => "",
                    "seriEntitas" => '2',

                ],
                [
                    //pembeli
                    "alamatEntitas" => "",
                    "kodeEntitas" => '6',
                    "kodeNegara" => "",
                    "namaEntitas" => "",
                    "seriEntitas" => '3',
                ],
                [
                    //pemilik

                    "alamatEntitas" => "",
                    "kodeEntitas" => "7",
                    "kodeJenisIdentitas" => "",
                    "nibEntitas" => "",
                    "namaEntitas" => "",
                    "nomorIdentitas" => "",
                    "seriEntitas" => "4",


                ],
            ];
            $this->bc30Model->update($id, ['payload' => json_encode($payload)]);
        }
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $pemilikBarang = [];
        $nomor = 1;
        $indexEntitas = count($payload->entitas);
        foreach ($payload->entitas as $i => $e) {
            if ($i > 0 && $i < $indexEntitas - 2) {
                $e->nomor = $nomor++;
                array_push($pemilikBarang, $e);
            }
        }


        $data = [
            'bc30' => $bc30,
            'pengusahaTPB' => $pengusahaTPB,
            'payload' => $payload,
            'kodeNegaraAsal' => $this->countryModel->findAll(),
            'pemilik' => $pemilikBarang,
            'indexEntitas' => $indexEntitas
        ];


        return view('BeaCukai/bc-30/form-entitas', $data);
    }

    public function updateEntitas()
    {
        $id = decrypt($this->request->getVar('id'));

        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);


        $payload->entitas[0] = [
            //eksportir
            "alamatEntitas" => $this->request->getVar('entitas_alamat_eksportir'),
            "kodeEntitas" => "2",
            "kodeJenisIdentitas" => $this->request->getVar('entitas_kode_jenis_identitas_eksportir'),
            "namaEntitas" => $this->request->getVar('entitas_nama_eksportir'),
            "nomorIdentitas" => $this->request->getVar('entitas_nomor_eksportir'),
            "seriEntitas" => 1,
        ];


        $payload->entitas[1] = [
            //penerima

            "alamatEntitas" => $this->request->getVar('entitas_alamat_penerima'),
            "kodeEntitas" => '8',
            "kodeNegara" => decrypt($this->request->getVar('entitas_kode_negara_penerima')),
            "namaEntitas" => $this->request->getVar('entitas_nama_penerima'),
            "seriEntitas" => 2,
        ];

        $payload->entitas[2] = [
            //pembeli
            "alamatEntitas" => $this->request->getVar('entitas_alamat_pembeli'),
            "kodeEntitas" => '6',
            "kodeNegara" => decrypt($this->request->getVar('entitas_kode_negara_pembeli')),
            "namaEntitas" => $this->request->getVar('entitas_nama_pembeli'),
            "seriEntitas" => 3,
        ];




        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);
        return response()->setJSON([
            'status' => true,
            'message' => "Entitas berhasil disimpan"
        ]);
    }

    public function updateEntitasPemilik()
    {
        $id = decrypt($this->request->getVar('id'));

        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);
        $lastIndex = count($payload->entitas) - 2;
        $arrayEntitasPemilik = [
            //pemilik
            "alamatEntitas" => $this->request->getVar('tambah_alamat_pemilik_barang'),
            "kodeEntitas" => "7",
            "kodeJenisIdentitas" => $this->request->getVar('tambah_pemilik_kode_jenis_entitas'),
            "namaEntitas" => $this->request->getVar('tambah_nama_pemilik_barang'),
            "nomorIdentitas" => $this->request->getVar('tambah_nomor_pemilik_barang'),
            "nibEntitas" => $this->request->getVar('tambah_nib_pemilik_barang') || "",
            "seriEntitas" => $lastIndex,
        ];
        array_splice($payload->entitas, $lastIndex, 0, [$arrayEntitasPemilik]);

        // $payload->entitas[$lastIndex] = [
        //     //pemilik
        //     "alamatEntitas" => $this->request->getVar('tambah_alamat_pemilik_barang'),
        //     "kodeEntitas" => "7",
        //     "kodeJenisIdentitas" => $this->request->getVar('tambah_pemilik_kode_jenis_entitas'),
        //     "namaEntitas" => $this->request->getVar('tambah_nama_pemilik_barang'),
        //     "nomorIdentitas" => $this->request->getVar('tambah_nomor_pemilik_barang'),
        //     "nibEntitas" => $this->request->getVar('tambah_nib_pemilik_barang'),
        //     "seriEntitas" => $lastIndex,
        // ];

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Entitas Pemilik berhasil disimpan"
        ]);
    }

    public function deleteEntitasPemilik()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete') + 3;

        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->entitas[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function dokumen($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        if (!is_array($payload->dokumen)) {
            $payload->dokumen = [];
            $this->bc30Model->update($id, ['payload' => json_encode($payload)]);
        }
        $dokumen = [];
        foreach (json_decode($this->bc30Model->find($id)['payload'])->dokumen as $d) {
            $dokumenDetail = $this->metaDataModel->where('name', "Dokumen")->where('description', $d->kodeDokumen)->first();
            $value = ($dokumenDetail == null) ? "" : $dokumenDetail['value'];
            array_push($dokumen, [
                'seriDokumen' => $d->seriDokumen,
                'kodeDokumen' => $d->kodeDokumen . " - " . $value,
                'nomorDokumen' => $d->nomorDokumen,
                'tanggalDokumen' => date('d/m/Y', strtotime($d->tanggalDokumen))
            ]);
        }

        $data = [
            'bc30' => $bc30,
            'kodeDokumen' => $this->metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'dokumen' => $dokumen,
        ];

        return view('BeaCukai/bc-30/form-dokumen', $data);
    }

    public function updateDokumen()
    {
        // INVOICE = 30
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $indexLast = count($payload->dokumen) == 0 ? 0 : count($payload->dokumen) - 1;
        $seriDokumen = count($payload->dokumen) == 0 ? 1 : $payload->dokumen[$indexLast]->seriDokumen + 1;

        $kodeDokumen = $this->request->getVar('dokumen_jenis_dokumen');
        if ($seriDokumen == 1) {
            // HARUS INVOICE
            if ($kodeDokumen != 380) {
                return response()->setJSON([
                    'status' => false,
                    'token' => csrf_hash(),
                    'message' => "Dokumen seri pertama wajib invoice ",
                ]);
            }
        }

        array_push($payload->dokumen, [
            'idDokumen' =>  generateUniqueCode(5),
            'kodeDokumen' => $kodeDokumen,
            'nomorDokumen' => $this->request->getVar('dokumen_nomor_dokumen'),
            'seriDokumen' => $seriDokumen,
            'tanggalDokumen' => date_format(date_create_from_format("d/m/Y", $this->request->getVar('dokumen_tanggal')), "Y-m-d"),
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil disimpan"
        ]);
    }

    public function deleteDokumen()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->dokumen[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }
    public function pengangkut($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);
        $pengangkutData = [];

        foreach (json_decode($this->bc30Model->find($id)['payload'])->pengangkut as $p) {
            $pengangkutDetail = $this->metaDataModel->where('name', "Pengangkutan")->where('description', $p->kodeCaraAngkut)->first();
            $negaraDetail = $this->countryModel->where('code', $p->kodeBendera)->first();
            $valuePengangkut = ($pengangkutDetail == null) ? "" : $pengangkutDetail['value'];
            $countryName = ($negaraDetail == null) ? "" : $negaraDetail['country_name'];
            array_push($pengangkutData, [
                'seriPengangkut' => $p->seriPengangkut,
                'kodeBendera' => $p->kodeBendera . " - " . $countryName,
                'nomorPengangkut' => $p->nomorPengangkut,
                'kodeCaraAngkut' => $p->kodeCaraAngkut . " - " . $valuePengangkut,
                'namaPengangkut' => $p->namaPengangkut
            ]);
        }

        $data = [
            'pengangkut' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'bc30' => $bc30,
            'kodeKantor' => $this->kantorBeaCukaiModel->findAll(),
            'selectedKantor' => $this->metaDataModel->where('name', "Kode Kantor Pabean Pengawas Static")->first(),
            'kodeNegaraAsal' => $this->countryModel->findAll(),
            'kodePengangkutan' => $this->metaDataModel->where('name', "Pengangkutan")->findAll(),
            'payload' => $payload,
            'pengangkutData' => $pengangkutData
        ];

        return view('BeaCukai/bc-30/form-pengangkut', $data);
    }
    //table pengangkutan
    public function createPengangkutanAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $indexLast = count($payload->pengangkut) == 0 ? 0 : count($payload->pengangkut) - 1;
        $seriPengangkut = count($payload->pengangkut) == 0 ? 1 : $payload->pengangkut[$indexLast]->seriPengangkut + 1;
        $kodeNegara = decrypt($this->request->getVar('pengangkutan_negara'));

        array_push($payload->pengangkut, [
            "kodeBendera" => $kodeNegara,
            "namaPengangkut" => $this->request->getVar('nama_sarana_angkut'),
            'nomorPengangkut' => $this->request->getVar('nomor_pengangkutan'),
            'kodeCaraAngkut' => $this->request->getVar('cara_pengangkutan'),
            'seriPengangkut' => $seriPengangkut
        ]);


        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil disimpan"
        ]);
    }

    public function deletePengangkutAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->pengangkut[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pengangkut berhasil dihapus"
        ]);
    }


    public function pengangkutUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);


        $payload->kodeTps = decrypt($this->request->getVar('pengangkut_tempat_penimbuhan'));
        $payload->kodePelMuat = $this->request->getVar('pengangkut_muat_asal');
        $payload->kodePelEkspor = decrypt($this->request->getVar('pengangkut_muat_ekspor'));
        $payload->kodePelBongkar = $this->request->getVar('pengangkut_bongkar');
        $payload->kodePelTujuan = $this->request->getVar('pengangkut_tujuan');
        $payload->kodeNegaraTujuan = decrypt($this->request->getVar('negara_tujuan_ekspor'));
        $payload->tanggalEkspor = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('pengangkutan_tanggal_perkiraan_ekspor'))));
        $payload->kodeLokasi = $this->request->getVar('pengangkutan_lokasi_pemeriksaan');
        $payload->tanggalPeriksa = date('Y-m-d', strtotime(str_replace('/', '-', $this->request->getVar('pengangkutan_tanggal_pemeriksa'))));

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);


        return response()->setJSON([
            'status' => true,
            'message' => "Pengangkut berhasil disimpan"
        ]);
    }

    public function kemasanPetiKemas($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-27');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $indexLastKemasan = count($payload->kemasan) == 0 ? 0 : count($payload->kemasan) - 1;
        $indexLastKontainer = count($payload->kontainer) == 0 ? 0 : count($payload->kontainer) - 1;
        $seriKemasan = count($payload->kemasan) == 0 ? 1 : $payload->kemasan[$indexLastKemasan]->seriKemasan + 1;
        $seriKontainer = count($payload->kontainer) == 0 ? 1 : $payload->kontainer[$indexLastKontainer]->seriKontainer + 1;

        $dataKemasan = [];
        $dataKontainer = [];

        // KEMASAN
        foreach ($payload->kemasan as $k) {
            $jenisKemasanDetail = $this->metaDataModel->where('name', 'Jenis Kemasan')->where('description', $k->kodeJenisKemasan)->first();


            array_push($dataKemasan, [
                'jumlahKemasan' => $k->jumlahKemasan,
                'kodeJenisKemasan' => $k->kodeJenisKemasan . " - " . strtoupper($jenisKemasanDetail['value']),
                'merkKemasan' => $k->merkKemasan,
                'seriKemasan' => $k->seriKemasan,
            ]);
        }

        // KONTAINER
        foreach ($payload->kontainer as $k) {
            $jenisKontainerDetail = $this->metaDataModel->where('name', "Jenis Kontainer")->where('description', $k->kodeJenisKontainer)->first();
            $tipeKontainerDetail = $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->where('value', $k->kodeTipeKontainer)->first();
            $ukuranKontainerDetail = $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->where('value', $k->kodeUkuranKontainer)->first();

            array_push($dataKontainer, [
                'kodeJenisKontainer' => $k->kodeJenisKontainer . " - " .  $jenisKontainerDetail['value'],
                'kodeTipeKontainer' => $k->kodeTipeKontainer . " - " .  $tipeKontainerDetail['description'],
                'kodeUkuranKontainer' => $k->kodeUkuranKontainer . " - " . $ukuranKontainerDetail['description'],
                'nomorKontainer' => $k->nomorKontainer,
                'seriKontainer' => $k->seriKontainer
            ]);
        }

        $data = [
            'bc30' => $bc30,
            'payload' => $payload,
            'seriKemasan' => $seriKemasan,
            'seriKontainer' => $seriKontainer,
            // 'dropdownKemasan' => $this->bc30Model->dropdownKemasan($bc30['sales_order_id']),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeTipeKontainer' => $this->metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $this->metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $this->metaDataModel->where('name', "Jenis Kontainer")->findAll(),
            'dataKemasan' => $dataKemasan,
            'dataKontainer' => $dataKontainer,
        ];

        return view('BeaCukai/bc-30/form-kemasan-peti-kemas', $data);
    }

    public function kemasanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);

        array_push($payload->kemasan, [
            'jumlahKemasan' => (float)$this->request->getVar('kemasan_jumlah_kemasan'),
            'kodeJenisKemasan' => $this->request->getVar('kemasan_jenis_kemasan'),
            'merkKemasan' => $this->request->getVar('kemasan_merk_kemasan'),
            'seriKemasan' => (int)$this->request->getVar('kemasan_seri_kemasan'),
            // 'kemasanInventoriId' => $this->request->getVar('kemasan_inventori_id'),
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kemasan berhasil disimpan"
        ]);
    }

    public function deleteKemasan()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->kemasan[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen berhasil dihapus"
        ]);
    }

    public function kontainerUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);

        array_push($payload->kontainer, [
            'kodeJenisKontainer' => $this->request->getVar('kontainer_jenis'),
            'kodeTipeKontainer' => $this->request->getVar('kontainer_tipe'),
            'kodeUkuranKontainer' => $this->request->getVar('kontainer_ukuran'),
            'nomorKontainer' => $this->request->getVar('kontainer_nomor'),
            'seriKontainer' => (int)$this->request->getVar('kontainer_seri'),
        ]);

        $payload->jumlahKontainer = count($payload->kontainer);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil disimpan"
        ]);
    }

    public function deleteKontainer()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->kontainer[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Kontainer berhasil dihapus"
        ]);
    }

    public function transaksi($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $data = [
            'kodeValuta' => $this->metaDataModel->where('name', "Valuta")->findAll(),
            'kodeAsuransi' => $this->metaDataModel->where('name', "Asuransi")->findAll(),
            'kodeIncoterm' => $this->metaDataModel->where('name', "Kode Incoterm BC")->findAll(),
            'bc30' => $bc30,
            'payload' => $payload,
        ];

        return view('BeaCukai/bc-30/form-transaksi', $data);
    }

    public function transaksiUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);

        $payload->kodeValuta = $this->request->getVar('harga_kode_valuta');
        $payload->ndpbm = (float)($this->request->getVar('harga_ndpbm'));
        $payload->kodeIncoterm = $this->request->getVar('transaksi_kode_incoterm');
        $payload->cif = intval($this->request->getVar('harga_cif'));
        $payload->freight = (float) $this->request->getVar('freight');
        $payload->kodeAsuransi = $this->request->getVar('transaksi_kode_asuransi');
        $payload->asuransi = (float) $this->request->getVar('tambah_nomor_pemilik_barang');
        $payload->bruto = (float)$this->request->getVar('berat_bruto');
        $payload->netto = (float) $this->request->getVar('berat_netto');
        $payload->nilaiMaklon = (float) $this->request->getVar('nilai_maklon');
        $payload->totalDanaSawit = (float)$this->request->getVar('nilai_pungutan_sawit');

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bank Devisa berhasil ditambah"
        ]);
    }

    public function saveBankDevisa()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($bc30['payload']);


        $kodeBank = $this->request->getVar('tambah-kode-bank-devisa');
        $bankData = $this->metaDataModel->where('name', 'Kode Bank')->where('value', $kodeBank)->first();
        $seriBankLast = count($payload->bankDevisa) == 0 ? 0 : count($payload->bankDevisa);

        if ($bankData == null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Tidak ada Bank",
                'status' => false
            ]);
        }

        array_push($payload->bankDevisa, [
            'kodeBank' => $kodeBank,
            'namaBank' => $bankData['description'],
            'seriBank' => $seriBankLast + 1,
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bank Devisa berhasil ditambah"
        ]);
    }

    public function deleteBankDevisa()
    {
        $id = decrypt($this->request->getVar('id'));
        $indexDelete = $this->request->getVar('index_delete');
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        unset($payload->bankDevisa[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Bank Devisa berhasil dihapus"
        ]);
    }


    public function barang($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);



        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $data = [
            'bc30' => $bc30,
            'payload' => $payload,
            'barang' =>  $this->bc30Model->barang(
                $bc30['sales_order_id'],
                $bc30['tipe_sales_order']
            )
        ];
        // var_dump($data['barang']);
        // die;

        return view('BeaCukai/bc-30/form-barang', $data);
    }


    public function barangDetail($id, $kodeBarang)
    {
        $id = decrypt($id);
        $kodeBarang = decrypt($kodeBarang);
        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $detailBarang = $this->bc30Model->detailBarang($bc30['id'], $kodeBarang, $bc30['sales_order_id']);
        $totalBarang = count($this->bc30Model->barang($bc30['sales_order_id'], $bc30['tipe_sales_order']));
        $this->setFlashDataNavigatorSession($id);

        if ($detailBarang['bcDetail'] == null) {
            // MASIH KOSONG
            $indexLast = count($payload->barang) == 0 ? 0 : count($payload->barang) - 1;
            $seriBarang = count($payload->barang) == 0 ? 1 : $payload->barang[$indexLast]->seriBarang + 1;

            // $ndpbm = $payload->ndpbm / $totalBarang;
            // $cif = $payload->cif / $totalBarang;
            // $bruto = $payload->bruto / $totalBarang;

            array_push($payload->barang, [
                'cif' => 0,
                'cifRupiah' => 0,
                'fob' => 0,
                'hargaEkspor' => 0,
                'hargaPatokan' => 0,
                'hargaPerolehan' => 0,
                'hargaSatuan' => 0,
                'jumlahKemasan' => 0,
                'jumlahSatuan' =>  (float)$detailBarang['barangDetail']['qty_keluar'],
                'kodeAsalBahanBaku' => "",
                'kodeBarang' => $kodeBarang,
                'kodeDaerahAsal' => "",
                'kodeDokumen' => "30",
                'kodeJenisKemasan' => "",
                'kodeNegaraAsal' => "",
                'kodeSatuanBarang' => $detailBarang['barangDetail']['kode_satuan_internal'],
                'merk' => "",
                'ndpbm' => 0,
                'netto' => 0,
                'nilaiBarang' => 0,
                'nilaiDanaSawit' => 0,
                'posTarif' => "",
                'seriBarang' => $seriBarang,
                'spesifikasiLain' => "",
                'tipe' => "",
                'ukuran' => "",
                'uraian' => "",
                'volume' => "",
                'barangTarif' => [],
                'barangDokumen' => [],
                'barangPemilik' => []
            ]);


            $this->bc30Model->update($id, ['payload' => json_encode($payload)]);
        }
        $data = [
            'bc30' => $bc30,
            'barang' => $this->bc30Model->detailBarang($bc30['id'], $kodeBarang),
            'kodeHS' => $this->hsCodeModel->findAll(),
            'kodeJenisKemasan' => $this->metaDataModel->where('name', "Jenis Kemasan")->findAll(),
            'kodeFasilitasTarif' => $this->metaDataModel
                ->where('name', "Kode Fasilitas Tarif BC")->whereIn('description', ['TIDAK DIPUNGUT', 'DIBEBASKAN', 'DITANGGUHKAN'])
                ->findAll(),
            'kodeJenisTarif' => $this->metaDataModel
                ->where('name', "Kode Jenis Tarif BC")
                ->findAll(),
            'kodeJenisPungutan' => $this->metaDataModel
                ->where('name', "Kode Jenis Pungutan BC")
                ->whereIn('value', ['BM', 'PPN', 'PPH'])
                ->findAll(),
            'kodeNegaraAsal' => $this->countryModel->findAll(),
            'kodeSatuanBarang' => null,
            'dokumen' => [],
            'dokumenSelected' => [], // SERI DOKUMEN YANG DI CHEKLIST
            'entitas' => [],
            'entitasSelected' => [],
        ];
        $data['kodeSatuanBarang'] = $this->metaDataModel->where('name', "Kode Satuan BC")->where('value', $data['barang']['bcDetail']->kodeSatuanBarang)->findAll();
        // APPEND JENIS DOKUMEN
        foreach ($payload->dokumen as $d) {
            $dokumenDetail = $this->metaDataModel->where('name', "Dokumen")->where('description', $d->kodeDokumen)->first();
            $value = ($dokumenDetail == null) ? "" : $dokumenDetail['value'];
            array_push($data['dokumen'], [
                'seriDokumen' => $d->seriDokumen,
                'kodeDokumen' => $d->kodeDokumen . " - " . $value,
                'nomorDokumen' => $d->nomorDokumen,
                'tanggalDokumen' => date('d/m/Y', strtotime($d->tanggalDokumen))
            ]);
        }
        // APPEND DOKUMEN YANG TER CHECKLIST
        foreach ($data['barang']['bcDetail']->barangDokumen as $b) {
            array_push($data['dokumenSelected'], $b->seriDokumen);
        }
        //APPEND JENIS ENTITAS
        $indexEntitas = count($payload->entitas);
        foreach ($payload->entitas as $i => $e) {
            if ($i > 0 && $i < $indexEntitas - 2) {
                array_push($data['entitas'], [
                    'seriEntitas' => $e->seriEntitas,
                    'noIdentitas' => $e->nomorIdentitas,
                    'namaEntitas' => $e->namaEntitas,
                    'alamatEntitas' => $e->alamatEntitas
                ]);
            }
            $i++;
        }
        foreach ($data['barang']['bcDetail']->barangPemilik as $e) {
            array_push($data['entitasSelected'], $e->seriEntitas);
        }

        return view('BeaCukai/bc-30/form-detail-barang', $data);
    }

    public function barangDetailUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        $seriBarang = $this->request->getVar('seriBarang');

        $nettoTotal = 0;
        $hargaPenyerahanTotal = 0;
        for ($i = 0; $i < count($payload->barang); $i++) {
            if ($payload->barang[$i]->seriBarang == $seriBarang) {
                $payload->barang[$i]->fob = (float) $this->request->getVar('fob');
                //     $payload->barang[$i]->hargaEkspor = $this->request->getVar('kodeBarang');
                //  $payload->barang[$i]->hargaPatokan = $this->request->getVar('uraian');
                //   $payload->barang[$i]->hargaPerolehan = $this->request->getVar('merk');
                //     $payload->barang[$i]->hargaSatuan = $this->request->getVar('tipe');
                $payload->barang[$i]->jumlahKemasan = intval($this->request->getVar('jumlahKemasan'));
                $payload->barang[$i]->jumlahSatuan = intval($this->request->getVar('jumlahSatuan'));
                $payload->barang[$i]->kodeAsalBahanBaku = $this->request->getVar('barang_detail_kode_asal_bahan_baku');
                $payload->barang[$i]->kodeBarang = $this->request->getVar('kodeBarang');
                $payload->barang[$i]->kodeDaerahAsal = $this->request->getVar('daerahAsalBarang');
                //  $payload->barang[$i]->kodeDokumen = $this->request->getVar('kodePerhitungan');
                $payload->barang[$i]->kodeJenisKemasan = $this->request->getVar('kodeJenisKemasan');
                $payload->barang[$i]->kodeNegaraAsal = (string)decrypt($this->request->getVar('negaraAsalBarang'));
                $payload->barang[$i]->kodeSatuanBarang = "" . decrypt($this->request->getVar('kodeSatuanBarang'));
                $payload->barang[$i]->merk = $this->request->getVar('merk');
                //                $payload->barang[$i]->ndpbm = (float)$this->request->getVar('netto');
                $payload->barang[$i]->netto = (float) $this->request->getVar('netto');
                // $payload->barang[$i]->nilaiBarang = (float)convertRupiahToNumber($this->request->getVar('hargaEkspor'));
                // $payload->barang[$i]->nilaiDanaSawit = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
                $payload->barang[$i]->posTarif = $this->request->getVar('posTarif');
                $payload->barang[$i]->seriBarang = intval($this->request->getVar('seriBarang'));
                //$payload->barang[$i]->spesifikasiLain = $this->request->getVar('hargaPenyerahan');
                $payload->barang[$i]->tipe = $this->request->getVar('tipe');
                $payload->barang[$i]->ukuran = $this->request->getVar('ukuran');
                $payload->barang[$i]->uraian = $this->request->getVar('uraian');
                $payload->barang[$i]->volume = (float) $this->request->getVar('volume');
                //below keknya tuk table
                // $payload->barang[$i]->barangTarif = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
                // $payload->barang[$i]->barangDokumen = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
                // $payload->barang[$i]->barangPemilik = (float)convertRupiahToNumber($this->request->getVar('hargaPenyerahan'));
            }
            $nettoTotal += $payload->barang[$i]->netto;
        }
        $payload->netto = $nettoTotal;


        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil update detail barang"
        ]);
    }


    public function createDokumenBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriDokumen = $this->request->getVar('seriDokumen');
        $seriBarang = $this->request->getVar('seriBarang');
        $indexBarang = 0;

        $payload = json_decode($this->bc30Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        array_push($payload->barang[$indexBarang]->barangDokumen, [
            'seriDokumen' => $seriDokumen,
            'seriIjin' => $seriDokumen
        ]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Dokumen berhasil disimpan",
            'status' => true,
        ]);
    }
    public function deleteDokumenBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $seriDokumen = $this->request->getVar('seriDokumen');
        $indexBarang = 0;
        $indexDelete = 0;

        $payload = json_decode($this->bc30Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        foreach ($payload->barang[$indexBarang]->barangDokumen as $i => $b) {
            if ($b->seriDokumen == $seriDokumen) {
                $indexDelete = $i;
            }
        }

        unset($payload->barang[$indexBarang]->barangDokumen[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Dokumen berhasil dihapus",
            'status' => true,
        ]);
    }

    public function createEntitasBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriEntitas = $this->request->getVar('seriEntitas');
        $seriBarang = $this->request->getVar('seriBarang');
        $indexBarang = 0;
        $payload = json_decode($this->bc30Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        array_push($payload->barang[$indexBarang]->barangPemilik, [
            'seriEntitas' => intval($seriEntitas),
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Entitas Pemilik Barang berhasil disimpan",
            'status' => true,
        ]);
    }
    public function deleteEntitasBarangDetail()
    {
        $id = decrypt($this->request->getVar('id'));
        $seriBarang = $this->request->getVar('seriBarang');
        $seriEntitas = $this->request->getVar('seriEntitas');
        $indexBarang = 0;
        $indexDelete = 0;

        $payload = json_decode($this->bc30Model->find($id)['payload']);
        foreach ($payload->barang as $i => $b) {
            if ($b->seriBarang == $seriBarang) {
                $indexBarang = $i;
            }
        }

        foreach ($payload->barang[$indexBarang]->barangDokumen as $i => $b) {
            if ($b->seriEntitas == $seriEntitas) {
                $indexDelete = $i;
            }
        }

        unset($payload->barang[$indexBarang]->barangDokumen[$indexDelete]);
        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'message' => "Entitas Pemilik Barang berhasil dihapus",
            'status' => true,
        ]);
    }
    public function pungutan($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);
        $kodeJenisPungutan = $this->metaDataModel
            ->where('name', "Kode Jenis Pungutan BC")
            ->whereIn('value', ['BM', 'PPN', 'PPH'])
            ->findAll();

        $pungutanList = [];

        $bmDibayar = 0;
        $bmDitanggung = 0;
        $bmDibebaskan = 0;
        $bmSudahDilunasi = 0;

        $pphDibayar = 0;
        $pphDitanggung = 0;
        $pphDibebaskan = 0;
        $pphSudahDilunasi = 0;

        $ppnDibayar = 0;
        $ppnDitanggung = 0;
        $ppnDibebaskan = 0;
        $ppnSudahDilunasi = 0;

        foreach ($payload->barang as $b) {
            foreach ($b->barangTarif as $p) {
                if ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 1) {
                    $bmDibayar += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 2) {
                    $bmDitanggung += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 5) {
                    $bmDibebaskan += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "BM" && $p->kodeFasilitasTarif == 7) {
                    $bmSudahDilunasi += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 1) {
                    $ppnDibayar += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 2) {
                    $ppnDitanggung  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 5) {
                    $ppnDibebaskan  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPN" && $p->kodeFasilitasTarif == 7) {
                    $ppnSudahDilunasi  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 1) {
                    $pphDibayar  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 2) {
                    $pphDitanggung  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 5) {
                    $pphDibebaskan  += $p->nilaiBayar;
                } elseif ($p->kodeJenisPungutan == "PPH" && $p->kodeFasilitasTarif == 7) {
                    $pphSudahDilunasi  += $p->nilaiBayar;
                }
            }
        }
        foreach ($kodeJenisPungutan as $k) {
            if ($k['value'] == "BM") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $bmDibayar,
                    'ditanggung' => $bmDitanggung,
                    'dibebaskan' => $bmDibebaskan,
                    'sudahDilunasi' => $bmSudahDilunasi

                ];
            } elseif ($k['value'] == "PPH") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $pphDibayar,
                    'ditanggung' => $pphDitanggung,
                    'dibebaskan' => $pphDibebaskan,
                    'sudahDilunasi' => $pphSudahDilunasi

                ];
            } elseif ($k['value'] == "PPN") {
                $pungutanList[] = [
                    'pungutan' => $k['value'],
                    'dibayar' => $ppnDibayar,
                    'ditanggung' => $ppnDitanggung,
                    'dibebaskan' => $ppnDibebaskan,
                    'sudahDilunasi' => $ppnSudahDilunasi
                ];
            }
        }


        $data = [
            'bc30' => $bc30,
            'payload' => $payload,
            'pungutanList' => $pungutanList,
        ];

        return view('BeaCukai/bc-30/form-pungutan', $data);
    }

    public function pernyataan($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);

        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();

        $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $data = [
            'bc30' => $bc30,
            'payload' => $payload,
            'ceisaSetting' => $ceisaSetting
        ];

        return view('BeaCukai/bc-30/form-pernyataan', $data);
    }

    public function pernyataanUpdate()
    {
        $id = decrypt($this->request->getVar('id'));
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        $payload->kotaTtd = $this->request->getVar('kotaTtd');
        $payload->tanggalTtd = date('Y-m-d', strtotime($this->request->getVar('tanggalTtd')));
        $payload->namaTtd = $this->request->getVar('namaTtd');
        $payload->jabatanTtd = $this->request->getVar('jabatanTtd');

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "Pernyataan berhasil diupdate"
        ]);
    }

    public function kesiapanBarang($id)
    {
        $id = decrypt($id);
        $bc30 = $this->bc30Model->find($id);



        if ($bc30 == null) {
            return redirect()->to('bea-cukai-bc-30');
        }

        // $this->setFlashDataNavigatorSession($id);
        $payload = json_decode($bc30['payload']);

        $data = [
            'bc30' => $bc30,
            'payload' => $payload,
        ];


        return view('BeaCukai/bc-30/form-kesiapan-barang', $data);
    }

    public function kesiapanBarangUpdate()
    {
        $id = decrypt($this->request->getVar('id'));

        $bc30 = $this->bc30Model->find($id);
        $payload = json_decode($this->bc30Model->find($id)['payload']);

        $payload->kesiapanBarang = [];

        $waktuSiapPeriksa =  date('Y-m-d', strtotime(str_replace('/', '-',  $this->request->getVar('tanggalSiapPeriksa'))))  . "T" .  date('H:i:s', strtotime($this->request->getVar('waktuSiapPeriksa'))) . '' . date('P', strtotime($this->request->getVar('timezoneOffset')));

        array_push($payload->kesiapanBarang, [
            'kodeJenisBarang' => $this->request->getVar('jenisBarang'),
            'kodeJenisGudang' => $this->request->getVar('jenisGudang'),
            'namaPic' => $this->request->getVar('namaPic'),
            'alamat' => $this->request->getVar('alamat'),
            'nomorTelpPic' => $this->request->getVar('nomorTelponPic'),
            'jumlahContainer20' => (float) $this->request->getVar('jumlahContainer20'),
            'jumlahContainer40' => (float)$this->request->getVar('jumlahContainer40'),
            'lokasiSiapPeriksa' => $this->request->getVar('lokasiSiapPeriksa'),
            'kodeCaraStuffing' => $this->request->getVar('caraStuffing'),
            'kodeJenisPartOf' => $this->request->getVar('jenisPartOf'),
            'tanggalPkb' => date("Y-m-d", strtotime(str_replace('/', '-', $this->request->getVar('tanggalPkb')))),
            'waktuSiapPeriksa' => $waktuSiapPeriksa,
        ]);

        $this->bc30Model->update($id, ['payload' => json_encode($payload)]);

        return response()->setJSON([
            'status' => true,
            'message' => "kesiapan Barang berhasil diupdate"
        ]);
    }
}
