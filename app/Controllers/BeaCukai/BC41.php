<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Helpers\BeaCukaiApi;
use App\Models\BC41Model;
use App\Models\CeisaSettingModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\SalesOrderLainModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 4.1 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54

class BC41 extends BaseController
{

    protected $this_company_id;
    protected $this_user_id;
    protected $akunCeisa;
    protected $ceisaSettingModel;
    protected $bc41Model;
    protected $metaDataModel;
    protected $salesOrderLainModel;
    protected $salesOrderLainDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;

    public function __construct()
    {
        $this->ceisaSettingModel = new CeisaSettingModel();
        $this->bc41Model = new BC41Model();
        $this->metaDataModel = new MetadataModel();
        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();

        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->akunCeisa = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
    }

    public function index()
    {
        $data = [
            'akunCeisa' => $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first(),
        ];

        return view('BeaCukai/BC-41/index', $data);
    }

    public function online()
    {
        $data = [
            'baseUrl' => $this->metaDataModel->where('name', "Base Url BC")->first()['value']
        ];

        return view('BeaCukai/BC-41/online', $data);
    }

    public function allOnline()
    {
        $username = ($this->akunCeisa == null ? "" : $this->akunCeisa['username']);
        $password = ($this->akunCeisa == null ? "" : $this->akunCeisa['password']);

        $beacukaiApi = new BeaCukaiApi($username, $password);
        $dataOnline = $beacukaiApi->getListStatusResponseAll();

        $newDataResult = [];
        foreach ($dataOnline->dataRespon as $d) {
            if ($d->kodeDokumen == "41") {
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
            "type"          => "BC 4.1"
        ];

        $condition = [
            "bc_41.company_id"  => $this->this_company_id,
            "bc_41.deletedAt" => null,
        ];

        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "statusPosting" => $this->request->getGet("statusPosting"),
            "mulaiTanggalBC41" => $this->request->getGet("mulaiTanggalBC41"),
            "selesaiTanggalBC41" => $this->request->getGet('selesaiTanggalBC41'),
            "noAju" => $this->request->getGet('noAju'),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiData = $this->bc41Model->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "divisi"                => $data->divisi,
                "warehouse_name"        => $data->warehouse_name,
                "no_sales_order"        => $data->no_sales_order,
                "customer_name"         => $data->customer_name,
                "no_aju"                => $data->no_aju . " / " . $data->no_daftar,
                "tanggal_bc_41"         => $data->createdAt == null ? '-' : date('d/m/Y', strtotime($data->createdAt)),
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
            'noAju' => $this->generateNomorAju(),
            'salesOrderLain' => $this->bc41Model->getListSalesOrderLain()
        ];

        return view('BeaCukai/BC-41/form', $data);
    }

    public function createAction()
    {
        $this->bc41Model->insert([
            'company_id' => $this->this_company_id,
            'sales_order_lain_id' => $this->request->getVar('sales_order_lain_id'),
            'no_aju' => $this->request->getVar('no_aju'),
            'no_daftar' => $this->request->getVar('no_daftar'),
            'status_posting' => '0',
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Disimpan"
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->bc41Model->update($id, [
            'company_id' => $this->this_company_id,
            'sales_order_lain_id' => $this->request->getVar('sales_order_lain_id'),
            'no_aju' => $this->request->getVar('no_aju'),
            'no_daftar' => $this->request->getVar('no_daftar'),
            'status_posting' => '0',
        ]);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Diupdate"
        ]);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $bc41 = $this->bc41Model->detail($id);

        if ($bc41 == null) {
            return redirect()->to('bea-cukai-bc-41');
        }

        $data = [
            'noAju' => $this->generateNomorAju(),
            'salesOrderLain' => $this->bc41Model->getListSalesOrderLain($bc41['sales_order_lain_id']),
            'bc41' => $bc41
        ];

        return view('BeaCukai/BC-41/form', $data);
    }

    public function checkNoAju()
    {
        $id = decrypt($this->request->getVar('id'));
        $noAju = $this->request->getVar('no_aju');
        $isUsed = true;

        if (!empty($this->request->getVar('id'))) {
            // UPDATE
            $first = $this->bc41Model
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
            $first = $this->bc41Model
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
        $this->bc41Model->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Dihapus"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $bc41 = $this->bc41Model->find($id);

        // KURANGIN STOK NYA
        $salesOrderLain = $this->salesOrderLainModel->find($bc41['sales_order_lain_id']);
        $salesOrderLainList = $this->salesOrderLainDetailModel->where('sales_order_lain_id', $bc41['sales_order_lain_id'])->findAll();

        foreach ($salesOrderLainList as $s) {
            $stock = $this->stockModel->find($s['stock_id']);
            $qty = $s['qty_konversi'];

            if ($stock['tipe_barang'] == "kemasan") {
                $barang2_id = $stock['kemasan_id'];
            } else {
                $barang2_id = $stock['barang2_id'];
            }

            // BARANG LAMA
            $stockOldDetail = $this->stockDetail2Model->getStockListDetail(
                $s['stock_id'],
                $s['bc_id'],
                $s['no_aju'],
                $s['stock_dokumen']
            );

            $stok = $this->stockModel->insertStok(
                $salesOrderLain['company_id'],
                $salesOrderLain['warehouse_id'],
                $salesOrderLain['divisi_id'],
                $stock['tipe_barang'],
                $stock['barang1_id'],
                $barang2_id,
                ($qty * -1),
            );

            // DETAIL
            $stokDetail = $this->stockDetailModel->insertStokDetail(
                $stok,
                $qty,
                "Out",
                date('Y-m-d'),
                $this->this_user_id,
                "PENJUALAN",
                $salesOrderLain['no_sales_order'],
                $salesOrderLain['keterangan'],
            );

            // SUB DETAIL
            $this->stockDetail2Model->insertStokDetail2(
                $s['bc_id'],
                $stok,
                $stokDetail,
                $qty,
                $s['no_aju'],
                $salesOrderLain['no_sales_order'],
                $s['stock_dokumen'],
                $stockOldDetail['supplier_id'],
                $s['total_harga'],
                null,
                null,
                $stockOldDetail['no_po']
            );
        }

        $this->bc41Model->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'message' => "Dokumen BC 4.1 Berhasil Diposting"
        ]);
    }


    public function generateNomorAju()
    {
        $ceisaSetting = $this->ceisaSettingModel->where('company_id', $this->this_company_id)->first();
        $kodeDokumenBC25Static = $this->metaDataModel->where('name', "Kode BC41 Static")->first();

        $kodeKantorStatic = $ceisaSetting['kode_kantor_pabean'];
        $tanggalAju = date('Ymd');
        $sequenceNoUrutPengajuan = "";

        $bc41Last = $this->bc41Model->orderBy('createdAt', "DESC")->limit(1)->first();

        if ($bc41Last == null) {
            $sequenceNoUrutPengajuan = "000001";
        } else {
            if ($bc41Last['no_aju'] == null) {
                $sequenceNoUrutPengajuan = "000001";
            } else {
                // Buatkan auto increment
                $arrNo = explode('-', $bc41Last['no_aju']);
                $lastNomor = $arrNo[3];
                // lakukan increment
                $nextNomor = str_pad((int)$lastNomor + 1, strlen($lastNomor), '0', STR_PAD_LEFT);
                $sequenceNoUrutPengajuan = $nextNomor;
            }
        }

        return $kodeDokumenBC25Static['value'] . '-' . $kodeKantorStatic . '-' . $tanggalAju . '-' . $sequenceNoUrutPengajuan;
    }
}
