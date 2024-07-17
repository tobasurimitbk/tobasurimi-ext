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
            return redirect()->to('bea-cukai-bc-27');
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
}
