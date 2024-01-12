<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;

use App\Models\AMPurchaseOrderModel;
use App\Models\AMPurchaseOrderDetailModel;
use App\Models\BarangMasterModel;
use App\Models\SupplierModel;
use App\Models\SppModel;
use App\Models\MetadataModel;
use App\Models\DivisisModel;
use App\Models\BeaCukaiModel;
use App\Models\CompaniesModel;
use App\Models\PenerimaanBarangModel;
use App\Models\SatuansModel;
use App\Models\TaxModel;
use Dompdf\Dompdf;

class POLokalBahanPenolong extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $aMPurchaseOrderModel;
    protected $aMPurchaseOrderDetailModel;
    protected $MetadataModel;
    protected $SppModel;
    protected $supplierModel;
    protected $divisionModel;
    protected $BeaCukaiModel;
    protected $dompdf;
    protected $companyModel;
    protected $barangMasterModel;
    protected $satuanModel;
    protected $penerimaanBarangModel;
    protected $taxModel;

    protected $jurnalController;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->aMPurchaseOrderModel = new AMPurchaseOrderModel();
        $this->aMPurchaseOrderDetailModel = new AMPurchaseOrderDetailModel();
        $this->MetadataModel = new MetadataModel();
        $this->SppModel = new SppModel();
        $this->supplierModel = new SupplierModel();
        $this->divisionModel = new DivisisModel();
        $this->BeaCukaiModel = new BeaCukaiModel();
        $this->dompdf = new Dompdf();
        $this->companyModel = new CompaniesModel();
        $this->barangMasterModel = new BarangMasterModel();
        $this->satuanModel = new SatuansModel();
        $this->taxModel = new TaxModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->jurnalController = new JurnalUmum();
    }

    public function poLokalBahanPenolong()
    {
        return view('Purchase/poLokalBahanPenolong/index');
    }

    public function createPOLokalBahanPenolong()
    {
        $data = [
            "company" => $this->companyModel->getCompanies(),
            "today" => date('Y-m-d'),
            "supplier" => $this->supplierModel->getSupplierByType("Bahan Penolong"),
            "barang" => $this->barangMasterModel->getBarangByType("bahan_penolong"),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "ppn" => $this->taxModel->getTaxByType("ppn"),
            "pph" => $this->taxModel->getTaxByType("pph")
        ];

        return view('Purchase/poLokalBahanPenolong/form', $data);
    }

    public function getDivisionByCompany()
    {
        $companyID = $this->request->getVar('companyID');
        $res = $this->divisionModel->get_by_company_id($companyID);
        return response()->setJSON([
            'data' => $res,
            'token' => csrf_hash(),
        ]);
    }

    public function savePOLokalBahanPenolong()
    {
        $divisi = $this->divisionModel->get_by_id(
            $this->request->getVar('divisionID')
        );

        if ($this->request->getVar('poNo') != "AUTO GENERATE") {
            $noPoNew = $this->request->getVar('poNo');
        } else {
            $noPoNew =  $this->aMPurchaseOrderModel->get_no(
                date('d'),
                date('m'),
                date('Y'),
                $divisi[0]['divisi'],
                date('y'),
                $this->request->getPost("divisionID"),
                getLastDay()
            );
        }
        $dataAmPurchaseOrderData = [
            'po_no' => $noPoNew,
            'po_date' => formatDMYtoYMD($this->request->getVar('poDate')),
            'payment_date' => formatDMYtoYMD($this->request->getVar('paymentDate')),
            'po_type' => "Lokal",
            'supplier_id' => $this->request->getVar('supplierID'),
            'company_id' => $this->request->getVar('companyID'),
            'division_id' => $this->request->getVar('divisionID'),
            'total' => $this->request->getVar('total'),
            'note' => $this->request->getVar('note'),
            "createdBy" => session()->get("login")->user_id,
        ];


        // insert new po
        $poID = $this->aMPurchaseOrderModel->insert($dataAmPurchaseOrderData);
        $aMPurchaseOrderDetailData = json_decode($this->request->getVar('listBarang'));


        foreach ($aMPurchaseOrderDetailData as $d) {
            $this->aMPurchaseOrderDetailModel->insert([
                'am_purchase_order_id' => $poID,
                'barang_id' => $d->barang_id,
                'note' => $d->keterangan,
                'unit' => $d->satuan_id,
                'qty' => $d->qty,
                'price' => $d->harga_satuan,
                'disc' => $d->diskon,
                'additional_cost' => $d->biaya_tambahan,
                'ppn' => $d->ppn,
                'pph' => $d->pph,
                'total' => repairDouble($d->total),
                'remaining_qty' => $d->qty
            ]);
        }

        // $this->jurnalController->insertDataPembelian($poID);

        return response()->setJSON([
            'message' => "PO Bahan penolong berhasil ditambah",
            'status' => true,
        ]);
    }

    public function allPOLokalBahanPenolong()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $condition = [
            'po_type' => "Lokal",
            'am_purchase_orders.deletedAt' => null,
            'am_purchase_order_details.deletedAt' => null
        ];

        $addCondition = [
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];
        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $poData = $this->aMPurchaseOrderModel->getPOLokalBPList($condition, $addCondition, $limit, $offset);

        $dataPOLokal = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($poData['data'] as $data) {
            array_push($dataPOLokal, [
                "no"            => $no++,
                "id"            => $data->id,
                "po_date"       => $data->po_date ? date("d/m/Y", strtotime($data->po_date)) : "",
                "po_no"         => $data->po_no,
                "companyName"  => $data->companyName,
                "supplierName"  => $data->supplierName,
                "total"         => "Rp " . number_format(formatter($data->total, "STR_TO_FLOAT"), 2, '.', ','),
                "is_posted"     => $data->is_posted,
                "itemCount"     => $data->itemCount,
                "status_penerimaan" => $data->status_penerimaan === "0" ? "OPEN" : "CLOSED"
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $poData['totalData'],
            "recordsFiltered"   => $poData['totalFilteredData'],
            "data"              => $dataPOLokal,
            "payload"           => $payload
        ];


        echo json_encode($data);
        return;
    }

    public function getByIdPOLokalBahanPenolong($id)
    {
        $amPurchaseOrderCondition = [
            'id' => $id,
            'deletedAt' => null
        ];

        $amPurchaseOrderDetailCondition = [
            'am_purchase_order_id' => $id,
            'am_purchase_order_details.deletedAt' => null
        ];

        $selectQryPurchaseOrderDetail = "
            am_purchase_order_details.barang_id,
            am_purchase_order_details.additional_cost AS biaya_tambahan,
            am_purchase_order_details.disc AS diskon,
            am_purchase_order_details.price AS harga_satuan,
            am_purchase_order_details.note AS keterangan,
            barang_master.kode_barang,
            barang_master.barang_name AS nama_barang,
            satuans.nama_satuan,
            am_purchase_order_details.pph,
            am_purchase_order_details.ppn,
            am_purchase_order_details.qty,
            am_purchase_order_details.unit AS satuan_id,

        ";

        $poDetail = $this->aMPurchaseOrderModel->where($amPurchaseOrderCondition)->first();
        $listBarang = $this->aMPurchaseOrderDetailModel
            ->select($selectQryPurchaseOrderDetail)
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id')
            ->join('satuans', 'barang_master.satuan_id = satuans.id')
            ->where($amPurchaseOrderDetailCondition)
            ->findAll();

        if ($poDetail == null) {
            return redirect()->to('po-lokal-bahan-penolong');
        }

        $data = [
            "company" => $this->companyModel->getCompanies(),
            "today" => date('Y-m-d'),
            "supplier" => $this->supplierModel->getSupplierByType("Bahan Penolong"),
            "barang" => $this->barangMasterModel->getBarangByType("bahan_penolong"),
            "satuan" => $this->satuanModel->getSatuanAll(),
            "ppn" => $this->taxModel->getTaxByType("ppn"),
            "pph" => $this->taxModel->getTaxByType("pph"),
            "poDetail" => $poDetail,
            "listBarang" => $listBarang

        ];

        return view('Purchase/poLokalBahanPenolong/form', $data);
    }

    public function updatePOLokalBahanPenolong()
    {
        $id = $this->request->getVar('id');

        $dataAmPurchaseOrderData = [
            'payment_date' => formatDMYtoYMD($this->request->getVar('paymentDate')),
            'po_type' => "Lokal",
            'supplier_id' => $this->request->getVar('supplierID'),
            'company_id' => $this->request->getVar('companyID'),
            'division_id' => $this->request->getVar('divisionID'),
            'total' => $this->request->getVar('total'),
            'note' => $this->request->getVar('note'),
            "createdBy" => session()->get("login")->user_id,
        ];

        $this->aMPurchaseOrderModel->update($id, $dataAmPurchaseOrderData);

        // deleteAllDetail
        $this->aMPurchaseOrderDetailModel->where('am_purchase_order_details.am_purchase_order_id', $id)->delete();
        // insert again
        $aMPurchaseOrderDetailData = json_decode($this->request->getVar('listBarang'));

        foreach ($aMPurchaseOrderDetailData as $d) {
            $this->aMPurchaseOrderDetailModel->insert([
                'am_purchase_order_id' => $id,
                'barang_id' => $d->barang_id,
                'note' => $d->keterangan,
                'unit' => $d->satuan_id,
                'qty' => $d->qty,
                'price' => $d->harga_satuan,
                'disc' => $d->diskon,
                'additional_cost' => $d->biaya_tambahan,
                'ppn' => $d->ppn,
                'pph' => $d->pph,
                'total' => repairDouble($d->total),
                'remaining_qty' => $d->qty
            ]);
        }

        return response()->setJSON([
            'message' => "PO Bahan penolong berhasil diubah",
            'status' => true,
        ]);
    }

    public function updateStatusPOLokalBahanPenolong()
    {
        $id = $this->request->getPost("id");

        $this->jurnalController->insertDataPembelian($id, "BAHAN PENOLONG", "LOKAL", "pembelian");
        // exit;
        $this->aMPurchaseOrderModel->update($id, [
            'is_posted' => true
        ]);


        return response()->setJSON([
            'message' => "PO Berhasil diposting",
            'status' => true,
        ]);
    }

    public function closePOLokalBahanPenolong()
    {
        $id = $this->request->getPost("id");

        $this->aMPurchaseOrderModel->update($id, [
            'status_penerimaan' => true
        ]);

        return response()->setJSON([
            'message' => "Close PO berhasil",
            'status' => true,
        ]);
    }

    public function deletePOLokalBahanPenolong()
    {
        $id = $this->request->getVar("id");

        $this->aMPurchaseOrderModel->delete($id);
        $this->aMPurchaseOrderDetailModel->where('am_purchase_order_id', $id)->delete();

        return response()->setJSON([
            'message' => "PO berhasil dihapus",
            'status' => true,
            'token' => csrf_hash()
        ]);
    }

    public function print($id = null)
    {
        if ($id) {
            $filename = "PO Lokal Bahan Penolong";

            if (!empty($id)) {
                $dataBPLokal = $this->aMPurchaseOrderModel->getPOById($id);
                $dataBPLokalDetail = $this->aMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);
                foreach (array_keys($dataBPLokalDetail) as $key) {
                    $dataBPLokalDetail[$key] = (object)$dataBPLokalDetail[$key];
                }

                $no = 0;
                $totalPrice = 0;
                $totalDisc = 0;
                $totalPpn = 0;
                $totalTambahan = 0;
                $keterangan = [];

                foreach ($dataBPLokalDetail as $value) {
                    $no++;
                    $value->no = $no;
                    $totalan = formatter($value->price, "CURR_TO_INT") * formatter($value->qty, "STR_TO_FLOAT");
                    $value->nilaiPpn = number_format($totalan * (float)$value->ppnValue / 100);
                    $value->nilaiPph = number_format($totalan * (float)$value->pphValue / 100);
                    $totalTambahan += formatter($value->additional_cost, "CURR_TO_INT");
                    $totalPrice += $totalan;
                    $totalDisc += ($totalan) * (float)$value->disc / 100;
                    $totalPpn += $totalan * (float)$value->ppnValue / 100;
                    $keterangan[] = $value->note;
                }

                $dataBPLokal->totalTambahan = number_format(formatter($totalTambahan, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPrice = number_format(formatter($totalPrice, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalDisc = number_format(formatter($totalDisc, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPpn = number_format(formatter($totalPpn, "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->totalPo = number_format(formatter(($totalTambahan + $totalPrice - $totalDisc + $totalPpn), "STR_TO_FLOAT"), 2, '.', ',');
                $dataBPLokal->keterangan = implode(",", array_unique($keterangan));
                $dataBPLokal->jatuhTempoHari = \totalDayInRange($dataBPLokal->po_date, $dataBPLokal->payment_date);
                $data["dataPOLokal"] = $dataBPLokal;
                $data["dataPOLokal"]->am_purchase_order_details = $dataBPLokalDetail;
            }
            $this->dompdf->loadHtml(view('Purchase/poLokalBahanPenolong/print', $data));
            $this->dompdf->setPaper('A4', 'landscape');
            $this->dompdf->render();
            $this->dompdf->stream($filename, array("Attachment" => false));
            exit(0);
        }
    }

    public function getHistoriHarga()
    {
        $amPurchaseOrderModel = new AMPurchaseOrderModel();
        $res = $amPurchaseOrderModel->historiHargaPOBahanPenolongFirst($this->request->getVar('id'), "Lokal", $this->this_company_id);
        return response()->setJSON(['res' => $res]);
    }

    public function dropdownHistoriPenerimaanBarang()
    {
        $id = $this->request->getVar('id');
        $listBarang = $this->aMPurchaseOrderDetailModel
            ->select('am_purchase_order_details.qty_diterima AS diterima, am_purchase_order_details.remaining_qty AS sisa, barang_master.barang_name AS nama_barang, barang_master.kode_barang, am_purchase_order_details.qty')
            ->join('barang_master', 'barang_master.id = am_purchase_order_details.barang_id')
            ->where('am_purchase_order_details.am_purchase_order_id', $id)
            ->where('am_purchase_order_details.deletedAt', null)
            ->findAll();

        $poDetail = $this->aMPurchaseOrderModel->select('am_purchase_orders.po_no, suppliers.name AS supplierName')
            ->join('suppliers', 'suppliers.id = am_purchase_orders.supplier_id')
            ->where('am_purchase_orders.id', $id)
            ->where('am_purchase_orders.deletedAt', null)
            ->first();

        $lpbDetail = $this->penerimaanBarangModel->like('multiple_po_id', $id)->where('deletedAt', null)->findAll();

        $lpbNo = [];
        foreach ($lpbDetail as $l) {
            $lpbNo[] = $l['no_penerimaan_barang'];
        }

        return response()->setJSON([
            'lpb_no' => count($lpbNo) == 0 ? "BELUM ADA LPB" : str_replace(['[', ']', '"', "\\"], '', json_encode($lpbNo)),
            'po_detail' => $poDetail,
            'list_barang' => $listBarang,
            'token' => csrf_hash()
        ]);
    }

    public function dropdownPOLokalBahanPenolong()
    {
        $id = formatter($this->request->getGet("id"), "STR_TO_INT");
        $dataPOLokal = $this->aMPurchaseOrderModel->getNoPenerimaanBarang("LOKAL", $id);
        $data = [
            "data" => $dataPOLokal
        ];
        echo json_encode($data);
        return;
    }

    public function dropdownBarangPOLokalBahanPenolong()
    {
        $id = $this->request->getGet("id");

        $dataPOLokal = $this->aMPurchaseOrderDetailModel->getPurchaseOrderDetailByPurchaseOrderId($id);

        $data = [
            "data" =>  $dataPOLokal
        ];

        echo json_encode($data);
        return;
    }
}
