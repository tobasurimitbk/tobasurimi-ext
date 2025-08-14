<?php

namespace App\Controllers\BiayaExim\BiayaEskpor;

use App\Controllers\BaseController;
use App\Models\BiayaEksporDetailModel;
use App\Models\BiayaEksporModel;
use App\Models\BiayaEksporPajakModel;
use App\Models\DivisisModel;
use App\Models\SalesOrderExportModel;
use App\Models\TaxModel;
use App\Models\VendorPelayaranModel;
use Dompdf\Dompdf;
use Exception;

class BiayaEskpor extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $taxesModel;
    protected $vendorPelayaranModel;
    protected $salesOrderExportModel;
    protected $biayaEksporModel;
    protected $biayaEksporDetailModel;
    protected $biayaEksporPajakModel;
    protected $dompdf;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->taxesModel = new TaxModel();
        $this->vendorPelayaranModel = new VendorPelayaranModel();
        $this->salesOrderExportModel = new SalesOrderExportModel();
        $this->biayaEksporModel = new BiayaEksporModel();
        $this->biayaEksporDetailModel = new BiayaEksporDetailModel();
        $this->biayaEksporPajakModel = new BiayaEksporPajakModel();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('BiayaExim/BiayaEskpor/index');
    }

    public function all()
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
        ];

        $condition = [
            "biaya_ekspor.company_id"  => $this->this_company_id,
            "biaya_ekspor.deletedAt" => NULL,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
            "status_posting" => $this->request->getGet("status_posting"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $dataResult = $this->biayaEksporModel->getList(
            $condition,
            $addCondition,
            $limit,
            $offset
        );

        $vendorResult = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataResult['data'] as $data) {
            array_push($vendorResult, [
                "no"                => $no++,
                "id"                => \encrypt($data['id']),
                "divisi"        => $data['divisi'],
                "tanggal_invoice"   => \date('d/m/Y', \strtotime($data['tanggal_invoice'])),
                "no_invoice"        => $data['no_invoice'],
                "no_container"        => $data['no_container'],
                "customer_name"        => $data['customer_name'],
                "dicharge_port"        => $data['dicharge_port'],
                "nama_vendor"        => $data['nama_vendor'],
                "total_faktur"        => (float)$data['total_faktur'],
                "status_posting"        => $data['status_posting'],

            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $dataResult['totalData'],
            "recordsFiltered"   => $dataResult['totalFilteredData'],
            "data"              => $vendorResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
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
        $dataSalesOrderExport = $this->salesOrderExportModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('sales_order_export_id', "desc")->findAll();

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataSalesOrderExport" => $dataSalesOrderExport
        ];

        return view('BiayaExim/BiayaEskpor/form', $data);
    }

    public function edit($id)
    {
        $id = \decrypt($id);
        $dataBiayaEskpor = $this->biayaEksporModel->getById($id);
        if ($dataBiayaEskpor == null) {
            return \redirect()->to('biaya-ekspor');
        }
        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataPajak = $this->taxesModel
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%', 'PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('type', "asc")
            ->findAll();
        $dataVendorPelayaran = $this->vendorPelayaranModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('nama_vendor', "asc")->findAll();
        $dataSalesOrderExport = $this->salesOrderExportModel->where('company_id', $this->this_company_id)->where('deletedAt', null)->orderBy('sales_order_export_id', "desc")->findAll();
        $dataBiayaEksporPajak = $this->biayaEksporPajakModel
            ->select('biaya_ekspor_pajak.*,taxes.type as type_tax, taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_ekspor_pajak.tax_id', 'left')
            ->where('biaya_ekspor_pajak.biaya_ekspor_id', $id)
            ->where('biaya_ekspor_pajak.deletedAt', null)
            ->findAll();
        $dataBiayaEksporDetail = $this->biayaEksporDetailModel
            ->where('biaya_ekspor_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataSalesOrderExport" => $dataSalesOrderExport,
            "dataBiayaEskpor" => $dataBiayaEskpor,
            "dataBiayaEksporPajak" => $dataBiayaEksporPajak,
            "dataBiayaEksporDetail" => $dataBiayaEksporDetail
        ];

        return view('BiayaExim/BiayaEskpor/form', $data);
    }

    public function print($id)
    {

        $id = decrypt($id);
        $dataBiayaEkspor = $this->biayaEksporModel->where('id', $id)->first();
        if ($dataBiayaEkspor == null) {
            return redirect()->to('biaya-eskpor');
        }

        $filename = $dataBiayaEkspor['no_invoice'];

        $data = [];
        $detailBiayaList = [];
        $taxList = [];
        $taxReturnList = [];
        $biayaTotal = 0;
        $taxTotal = 0;
        $taxReturnTotal = 0;

        $dataBiayaEkspor = $this->biayaEksporModel
            ->select("biaya_ekspor.*, vendor_pelayaran.nama_vendor")
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_ekspor.vendor_pelayaran_id', 'left')
            ->where('biaya_ekspor.id', $id)
            ->first();

        $dataBiayaEksporDetail = $this->biayaEksporDetailModel
            ->where('biaya_ekspor_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        // Pajak Bukti Pengeluaran (Bon Putih)
        $taxData = $this->biayaEksporPajakModel
            ->select('biaya_ekspor_pajak.*,taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_ekspor_pajak.tax_id', 'left')
            ->where('biaya_ekspor_id', $id)
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%'])
            ->where('biaya_ekspor_pajak.deletedAt', null)
            ->findAll();

        // Pajak Bukti Pemasukkan (Bon Merah)
        $taxReturnData = $this->biayaEksporPajakModel
            ->select('biaya_ekspor_pajak.*,taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_ekspor_pajak.tax_id', 'left')
            ->where('biaya_ekspor_id', $id)
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('biaya_ekspor_pajak.deletedAt', null)
            ->findAll();

        foreach ($dataBiayaEksporDetail as $det) {
            $detailBiayaList[] = $det['uraian_biaya'];
            $biayaTotal += $det['nilai_biaya'];
        }

        foreach ($taxData as $tax) {
            $taxList[] = "{$tax['tax_name']}: {$tax['no_faktur_pajak']}";
            $taxTotal += $tax['nilai_pajak'];
        }

        foreach ($taxReturnData as $tax) {
            $taxReturnList[] = "{$tax['tax_name']}: {$tax['no_faktur_pajak']}";
            $taxReturnTotal += $tax['nilai_pajak'];
        }

        $total = $biayaTotal  + $taxReturnTotal;

        $data["uraian"] = implode(", ", $detailBiayaList);
        $data["biayaTotal"] = $biayaTotal;
        $data["dataBiayaEkspor"] = $dataBiayaEkspor;
        $data['taxList'] = implode(', ', $taxList);
        $data['taxReturnList'] = implode(', ', $taxReturnList);
        $data['total'] = $total;
        $data['taxTotal'] = $taxTotal;
        $data['taxReturnTotal'] = $taxReturnTotal;
        $data['terbilang'] = penyebut($total < 0 ? $total * -1 : $total);
        $data['taxReturnTerbilang'] = $taxReturnTotal > 0 ? penyebut($taxReturnTotal) : 'nol';
        $data['taxReturnData'] = $taxReturnData;
        $data['taxData'] = $taxData;

        // Bon Merah Bukti Penerimaan
        $totalDikembalikan = 0;
        foreach ($taxReturnData as $t) :
            $totalDikembalikan += $t['nilai_pajak'];
        endforeach;
        $data['totalDikembalikan'] = $totalDikembalikan;

        $this->dompdf->loadHtml(view('BiayaExim/BiayaEskpor/print', $data));
        $this->dompdf->setPaper('A5', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function getDetailOrderForm()
    {
        try {
            $salesOrderExportId = $this->request->getVar('sales_order_export_id');
            $salesOrderExport = $this->salesOrderExportModel->getById($salesOrderExportId);

            $dataSalesExportDetail =  $this->salesOrderExportModel
                ->getDetailSalesKontrakInOrderForm(
                    $salesOrderExport->sales_contract_id,
                    $salesOrderExportId
                );

            return response()->setJSON([
                'dataSalesExportDetail' => $dataSalesExportDetail,
                'dataSalesOrderExport' => $salesOrderExport,
                'status' => true
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function store()
    {
        // return response()->setJSON([
        //     'POST' => $_POST,
        //     'listPajak' => \json_decode($_POST['listPajak']),
        //     'listBiayaEkspor' => \json_decode($_POST['listBiayaEkspor'])
        // ]);

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $noInvoice = $this->request->getVar('no_invoice');
            $tanggalInvoice = $this->request->getVar('tanggal_invoice');
            if ($noInvoice == "AUTO GENERATE") {
                $noInvoice = $this->biayaEksporModel->generateNumber(
                    $tanggalInvoice
                );
            }

            if ($this->checkNumber($noInvoice, null) == false) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "No invoice sudah digunakan"
                ]);
            }

            $biayaEksporId = $this->biayaEksporModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'vendor_pelayaran_id' => $this->request->getVar('vendor_pelayaran_id'),
                'sales_order_export_id' => $this->request->getVar('sales_order_export_id'),
                'no_invoice' => $noInvoice,
                'tanggal_invoice' =>  $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : null,
                'no_container' => $this->request->getVar('no_container'),
                'no_seal' => $this->request->getVar('no_seal'),
                'nama_kapal' => $this->request->getVar('nama_kapal'),
                'keberangkatan_kapal' =>  $this->request->getVar("keberangkatan_kapal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("keberangkatan_kapal")))) : null,
                'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
                'tanggal_surat_jalan' =>  $this->request->getVar("tanggal_surat_jalan") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_surat_jalan")))) : null,
                'no_kendaraan' => $this->request->getVar('no_kendaraan'),
                'detail_kendaraan' => $this->request->getVar('detail_kendaraan'),
                'total_faktur_before_tax' => $this->request->getVar('total_faktur_before_tax'),
                'total_faktur' => $this->request->getVar('total_faktur'),
                'status_posting' => 0
            ]);

            foreach (\json_decode($_POST['listBiayaEkspor']) as $l) {
                $this->biayaEksporDetailModel->insert([
                    'biaya_ekspor_id' => $biayaEksporId,
                    'nilai_biaya' => $l->nilai_biaya,
                    'uraian_biaya' => $l->uraian_biaya
                ]);
            }

            foreach (\json_decode($_POST['listPajak']) as $l) {
                $this->biayaEksporPajakModel->insert([
                    'biaya_ekspor_id' => $biayaEksporId,
                    'tax_id' => $l->tax_id,
                    'no_faktur_pajak' => $l->no_faktur_pajak,
                    'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                    'status_pajak' => $l->tax_status,
                    'nilai_pajak' => $l->nilai_pajak,
                    'keterangan_pajak' => $l->keterangan_pajak
                ]);
            }

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data berhasil disimpan",
                'token' => csrf_hash(),
                'status' => true,
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return \response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage() . " " . $e->getFile() . " at " . $e->getLine()
            ]);
        }
    }

    public function update()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $id = \decrypt($this->request->getVar('id'));
            $noInvoice = $this->request->getVar('no_invoice');

            if ($this->checkNumber($noInvoice, $id) == false) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "No invoice sudah digunakan"
                ]);
            }

            $this->biayaEksporModel->update($id, [
                'divisi_id' => $this->request->getVar('divisi_id'),
                'vendor_pelayaran_id' => $this->request->getVar('vendor_pelayaran_id'),
                'sales_order_export_id' => $this->request->getVar('sales_order_export_id'),
                'no_invoice' => $noInvoice,
                'tanggal_invoice' =>  $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : null,
                'no_container' => $this->request->getVar('no_container'),
                'no_seal' => $this->request->getVar('no_seal'),
                'nama_kapal' => $this->request->getVar('nama_kapal'),
                'keberangkatan_kapal' =>  $this->request->getVar("keberangkatan_kapal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("keberangkatan_kapal")))) : null,
                'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
                'tanggal_surat_jalan' =>  $this->request->getVar("tanggal_surat_jalan") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_surat_jalan")))) : null,
                'no_kendaraan' => $this->request->getVar('no_kendaraan'),
                'detail_kendaraan' => $this->request->getVar('detail_kendaraan'),
                'total_faktur_before_tax' => $this->request->getVar('total_faktur_before_tax'),
                'total_faktur' => $this->request->getVar('total_faktur'),
            ]);

            $idBiayaEksporDetailUsedArr = array();
            $idBiayaEksporPajakUsedArr = array();
            foreach (\json_decode($_POST['listBiayaEkspor']) as $l) {
                $check = $this->biayaEksporDetailModel
                    ->where('id', $l->id_biaya_ekspor_detail)
                    ->first();

                if ($check != null) {
                    // Update
                    $this->biayaEksporDetailModel->update($check['id'], [
                        'nilai_biaya' => $l->nilai_biaya,
                        'uraian_biaya' => $l->uraian_biaya
                    ]);
                    \array_push($idBiayaEksporDetailUsedArr, $check['id']);
                } else {
                    // Create
                    $biayaEksporDetailId =   $this->biayaEksporDetailModel->insert([
                        'biaya_ekspor_id' => $id,
                        'nilai_biaya' => $l->nilai_biaya,
                        'uraian_biaya' => $l->uraian_biaya
                    ]);
                    \array_push($idBiayaEksporDetailUsedArr, $biayaEksporDetailId);
                }
            }

            foreach (\json_decode($_POST['listPajak']) as $l) {
                $check = $this->biayaEksporPajakModel
                    ->where('id', $l->id_biaya_ekspor_pajak)
                    ->first();


                if ($check != null) {
                    // Update
                    $this->biayaEksporPajakModel->update($check['id'], [
                        'tax_id' => $l->tax_id,
                        'no_faktur_pajak' => $l->no_faktur_pajak,
                        'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                        'status_pajak' => $l->tax_status,
                        'nilai_pajak' => $l->nilai_pajak,
                        'keterangan_pajak' => $l->keterangan_pajak
                    ]);
                    \array_push($idBiayaEksporPajakUsedArr, $check['id']);
                } else {
                    // Create
                    $biayaEksporPajakId =  $this->biayaEksporPajakModel->insert([
                        'biaya_ekspor_id' => $id,
                        'tax_id' => $l->tax_id,
                        'no_faktur_pajak' => $l->no_faktur_pajak,
                        'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                        'status_pajak' => $l->tax_status,
                        'nilai_pajak' => $l->nilai_pajak,
                        'keterangan_pajak' => $l->keterangan_pajak
                    ]);
                    \array_push($idBiayaEksporPajakUsedArr, $biayaEksporPajakId);
                }
            }

            $this->biayaEksporDetailModel->whereNotIn('id', $idBiayaEksporDetailUsedArr)
                ->where('biaya_ekspor_id', $id)
                ->delete();

            $this->biayaEksporPajakModel->whereNotIn('id', $idBiayaEksporPajakUsedArr)
                ->where('biaya_ekspor_id', $id)
                ->delete();

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data berhasil diupdate",
                'token' => csrf_hash(),
                'status' => true,
            ]);
        } catch (Exception $e) {
            $db->transRollback();
            return \response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy()
    {
        try {
            $id = \decrypt($this->request->getVar('id'));
            $this->biayaEksporModel->delete($id);
            $this->biayaEksporDetailModel->where('biaya_ekspor_id', $id)->delete();
            $this->biayaEksporPajakModel->where('biaya_ekspor_id', $id)->delete();

            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'message' => "Data berhasil dihapus"
            ]);
        } catch (Exception $e) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function posting()
    {
        $id = \decrypt($this->request->getVar('id'));
        $this->biayaEksporModel->update($id, [
            'status_posting' => 1
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Data berhasil diposting"
        ]);
    }

    public function unposting()
    {
        $id = \decrypt($this->request->getVar('id'));
        $this->biayaEksporModel->update($id, [
            'status_posting' => 0
        ]);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Data berhasil diunposting"
        ]);
    }

    private function checkNumber($noInvoice, $id)
    {
        $checkQry = $this->biayaEksporModel
            ->where('company_id', $this->this_company_id)
            ->where('no_invoice', $noInvoice)
            ->where('deletedAt', null);
        if ($id != null) {
            $checkQry->where('id !=', $id);
        }

        $resultCheck = $checkQry->first();
        if ($resultCheck == null) {
            return true;
        } else {
            return false;
        }
    }
}
