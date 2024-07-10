<?php

namespace App\Controllers\PenjualanLain;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use App\Models\BC25Model;
use App\Models\BC41Model;
use App\Models\CompaniesModel;
use App\Models\CustomerModel;
use App\Models\DivisisModel;
use App\Models\KemasanModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderLainDetailModel;
use App\Models\SalesOrderLainModel;
use App\Models\SatuansModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use Dompdf\Dompdf;

class SalesOrderLain extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $salesOrderLainModel;
    protected $salesOrderLainDetailModel;
    protected $metaDataModel;
    protected $divisiModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $barangMasterModel;
    protected $barangMasterSpesifikasiModel;
    protected $kemasanModel;
    protected $satuanModel;
    protected $customerModel;
    protected $companyModel;
    protected $bc25Model;
    protected $bc41Model;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->salesOrderLainModel = new SalesOrderLainModel();
        $this->salesOrderLainDetailModel = new SalesOrderLainDetailModel();
        $this->metaDataModel = new MetadataModel();
        $this->divisiModel = new DivisisModel();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->barangMasterModel = new BarangMasterModel();
        $this->barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $this->kemasanModel = new KemasanModel();
        $this->satuanModel = new SatuansModel();
        $this->customerModel = new CustomerModel();
        $this->companyModel = new CompaniesModel();
        $this->bc25Model = new BC25Model();
        $this->bc41Model = new BC41Model();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        return view('SalesOrderLain/index');
    }

    public function all()
    {
        $payload = [
            "pageSize"      => $this->request->getVar("length"),
            "currentPage"   => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "search" => $this->request->getVar("search"),
            "sort" => $this->request->getVar("sort"),
            "sorttype" => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "sort"   => $this->request->getVar("sort"),
            "sortType"  => $this->request->getVar("sortType"),
            "search" => $this->request->getVar("search"),
            "status_posting" => $this->request->getVar("status_posting"),
            "no_sales_order" => $this->request->getVar("no_sales_order"),
            "mulai_tanggal"  => $this->request->getVar("mulai_tanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("mulai_tanggal")))) : "",
            "selesai_tanggal" => $this->request->getVar("selesai_tanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("selesai_tanggal")))) : "",
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $divisiArr = array();
        $dataResult = array();

        $condition = [
            'sales_order_lain.company_id' => $this->this_company_id,
            'sales_order_lain.deletedAt' => null
        ];

        foreach ($this->divisiModel->getDivisiAccess() as $d) {
            array_push($divisiArr, $d['id']);
        }

        $dataQry = $this->salesOrderLainModel->getList($condition, $divisiArr,  $addCondition, $limit, $offset);
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($dataQry['data'] as $data) {
            $listItem = $this->salesOrderLainDetailModel->where('sales_order_lain_id', $data->id)->findAll();
            $totalItem = count($listItem);

            if ($data->bc_id === "0") {
                $data->dokumen_pengeluaran = "NON PABEAN";
            } else {
                $bc = $this->metaDataModel->find($data->bc_id);
                $data->dokumen_pengeluaran = $bc['value'];
            }

            $bc25 = $this->bc25Model->where('sales_order_lain_id', $data->id)->first();
            $bc41 = $this->bc41Model->where('sales_order_lain_id', $data->id)->first();

            array_push($dataResult, [
                "no" => $no++,
                "id" => encrypt($data->id),
                "tanggal"  => date('d/m/Y', strtotime($data->tanggal)),
                "divisi" => $data->divisi,
                "no_sales_order" => $data->no_sales_order,
                "tipe_customer" => $data->tipe_customer,
                "customer_name" => $data->customer_name,
                "dokumen_pengeluaran" => $data->dokumen_pengeluaran,
                "keterangan" => $data->keterangan == "" || $data->keterangan == null ? "-" : $data->keterangan,
                "total_barang" => $totalItem,
                "total_harga" => number_format($data->total_harga, 2),
                "status_posting" => $data->status_posting,
                "status_used" => (($data->bc_id == "0" && $data->status_posting === "1") ? false : (($bc25 == null && $bc41 == null))) ? false : true
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $dataQry['totalData'],
            "recordsFiltered"   => $dataQry['totalFilteredData'],
            "data"              => $dataResult,
            "payload"           => $payload
        ];

        return response()->setJSON($data);
    }


    public function create()
    {
        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'dokumenPabean' => $this->salesOrderLainModel->getDokumenPabean(),
            'tanggal' => date('Y-m-d'),
            'divisi' => $this->divisiModel->getDivisiAccess()
        ];
        return view('SalesOrderLain/form', $data);
    }

    public function detail($id)
    {
        $id = decrypt($id);
        $salesOrderLain = $this->salesOrderLainModel->detail($id);

        if ($salesOrderLain == null) {
            return redirect()->to('order-form-lain');
        }

        $data = [
            'tipeBarang' => $this->metaDataModel->where('deletedAt', null)->where('name', "Kategori Barang")->findAll(),
            'dokumenPabean' => $this->salesOrderLainModel->getDokumenPabean(),
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'salesOrderLain' => $salesOrderLain,
        ];

        return view('SalesOrderLain/form', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);
        $salesOrderLain = $this->salesOrderLainModel->detail($id);

        if ($salesOrderLain == null) {
            return redirect()->to('order-form-lain');
        }

        $salesOrderLainDetail = $this->salesOrderLainDetailModel->detail($id);
        $data = [
            'company' => $this->companyModel->find($salesOrderLain['company_id']),
            'salesOrderLain' => $salesOrderLain,
            'salesOrderLainDetail' => $salesOrderLainDetail
        ];
        $this->dompdf->loadHtml(view('SalesOrderLain/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Sales Order Penjualan Lain", array("Attachment" => false));
    }

    public function createAction()
    {

        $check = $this->salesOrderLainModel
            ->where('company_id', $this->this_company_id)
            ->where('no_sales_order', $this->request->getVar('no_sales_order'))
            ->first();

        if ($check != null) {
            return response()->setJSON([
                'status' => false,
                'message' => "Nomor sales order sudah ada",
                'token' => csrf_hash()
            ]);
        }

        $id = $this->salesOrderLainModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'customer_id' => $this->request->getVar('customer_id'),
            'bc_id' => $this->request->getVar('bc_id'),
            'no_sales_order' => $this->request->getVar('no_sales_order'),
            'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
            'status_posting' => '0',
            'keterangan' => strtoupper($this->request->getVar('keterangan'))
        ]);

        foreach (json_decode($_POST['listBarang']) as $l) {
            $this->salesOrderLainDetailModel->insert([
                'sales_order_lain_id' => $id,
                'stock_id' => $l->stock_id,
                'stock_dokumen' => $l->stock_dokumen,
                'no_aju' => $l->no_aju,
                'bc_id' => $l->bc_id,
                'satuan_order_id' => $l->satuan_order_id,
                'qty_order' => $l->qty_order,
                'qty_konversi' => $l->qty_konversi,
                'harga_satuan' => $l->harga_satuan,
                'potongan_harga' => $l->potongan_harga,
                'biaya_tambahan' => $l->biaya_tambahan,
                'total_harga' => $l->total_harga
            ]);
        }

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Order form berhasil disimpan"
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));

        $this->salesOrderLainModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $this->request->getVar('divisi_id'),
            'warehouse_id' => $this->request->getVar('warehouse_id'),
            'customer_id' => $this->request->getVar('customer_id'),
            'bc_id' => $this->request->getVar('bc_id'),
            'tanggal' =>  $this->request->getPost("tanggal") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal")))) : "",
            'status_posting' => '0',
            'keterangan' => strtoupper($this->request->getVar('keterangan'))
        ]);

        // get all id detail
        $id_detail_all = [];
        foreach (json_decode($_POST['listBarang']) as $l) {
            $check = $this->salesOrderLainDetailModel
                ->where('sales_order_lain_id', $id)
                ->where('stock_id', $l->stock_id)
                ->where('stock_dokumen', $l->stock_dokumen)
                ->where('no_aju', $l->no_aju)
                ->where('bc_id', $l->bc_id)
                ->first();

            if ($check == null) {
                $id_detail_new = $this->salesOrderLainDetailModel->insert([
                    'sales_order_lain_id' => $id,
                    'stock_id' => $l->stock_id,
                    'stock_dokumen' => $l->stock_dokumen,
                    'no_aju' => $l->no_aju,
                    'bc_id' => $l->bc_id,
                    'satuan_order_id' => $l->satuan_order_id,
                    'qty_order' => $l->qty_order,
                    'qty_konversi' => $l->qty_konversi,
                    'harga_satuan' => $l->harga_satuan,
                    'potongan_harga' => $l->potongan_harga,
                    'biaya_tambahan' => $l->biaya_tambahan,
                    'total_harga' => $l->total_harga
                ]);

                array_push($id_detail_all, $id_detail_new);
            } else {
                // ADA
                $this->salesOrderLainDetailModel->update($check['id'], [
                    'sales_order_lain_id' => $id,
                    'stock_id' => $l->stock_id,
                    'stock_dokumen' => $l->stock_dokumen,
                    'no_aju' => $l->no_aju,
                    'bc_id' => $l->bc_id,
                    'satuan_order_id' => $l->satuan_order_id,
                    'qty_order' => $l->qty_order,
                    'qty_konversi' => $l->qty_konversi,
                    'harga_satuan' => $l->harga_satuan,
                    'potongan_harga' => $l->potongan_harga,
                    'biaya_tambahan' => $l->biaya_tambahan,
                    'total_harga' => $l->total_harga
                ]);
                array_push($id_detail_all, $check['id']);
            }
        }

        // HAPUS WHERE NOT IN
        $this->salesOrderLainDetailModel->where('sales_order_lain_id', $id)->whereNotIn('id', $id_detail_all)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Order form berhasil diupdate",
            'token' => csrf_hash(),
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $salesOrderLain = $this->salesOrderLainModel->find($id);
        $salesOrderLainList = $this->salesOrderLainDetailModel->where('sales_order_lain_id', $id)->findAll();

        if ($salesOrderLain['bc_id'] === "0") {
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
        }

        $this->salesOrderLainModel->update($id, ['status_posting' => '1']);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Order form berhasil diposting"
        ]);
    }

    public function unPosting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->salesOrderLainModel->update($id, ['status_posting' => '0']);

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Order form berhasil diunposting"
        ]);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->salesOrderLainModel->delete($id);
        $this->salesOrderLainDetailModel->where('sales_order_lain_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Order form berhasil dihapus",
            'token' => csrf_hash(),
        ]);
    }

    public function getListStockByStockID()
    {
        $bcId = $this->request->getVar('bc_id');
        $bcIdSearch = 0;

        if (!empty($bcId)) {
            if ($bcId != 0) {
                $bc = $this->metaDataModel->find($bcId);
                if ($bc['value'] == "BC 2.5") {
                    // ASAL BARANG 2.3
                    $bc23 = $this->metaDataModel->getBCFirst("BC 2.3");
                    $bcIdSearch = $bc23['id'];
                } else {
                    // ASAL BARANG 4.0
                    $bc40 = $this->metaDataModel->getBCFirst("BC 4.0");
                    $bcIdSearch = $bc40['id'];
                }
            }
        }


        if (!empty($this->request->getVar('stock_id'))) {
            $condition = [
                'stock_details2.bc_id' => $bcIdSearch,
                // 'stock_details.sumber' => "LPB"
            ];

            $dataResult = $this->stockDetail2Model->getStockListWithAddCondition(
                $this->request->getVar('stock_id'),
                $condition
            );
            $stock = $this->stockModel->find($this->request->getVar('stock_id'));
            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $this->barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $this->satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $satuanId = $barangMasterSpesifikasi['satuan_1'];
                $barangName = $barangMaster['barang_name'] . "-" . $barangMasterSpesifikasi['spesifikasi'];
            } else {
                $kemasan = $this->kemasanModel->find($stock['kemasan_id']);
                $satuan = $this->satuanModel->find($kemasan['satuan_id']);
                $satuanId = $kemasan['satuan_id'];
                $barangName = $kemasan['name'];
            }
            $resultArr = array();

            for ($i = 0; $i < count($dataResult); $i++) {
                $bcType = $this->metaDataModel->find($dataResult[$i]['bc_id']);

                $dataResult[$i]['stock_dokumen'] = $dataResult[$i]['stock_dokumen'] == null ? "-" : $dataResult[$i]['stock_dokumen'];
                $dataResult[$i]['no_aju'] =  $dataResult[$i]['no_aju'] == "-" ? "-" : $dataResult[$i]['no_aju'];
                $dataResult[$i]['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
                $dataResult[$i]['satuan'] = $satuan['kode_satuan'];
                $dataResult[$i]['barang'] = strtoupper($barangName);
                $dataResult[$i]['stock_date'] = date('d/m/Y', strtotime($dataResult[$i]['stock_date']));
                $dataResult[$i]['type_barang'] = $stock['tipe_barang'];
                $dataResult[$i]['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
                $dataResult[$i]['stok_total'] = ($dataResult[$i]['stok_total']);
                $dataResult[$i]['satuan_id'] = $satuanId;
                // TAMBAHAN
                $dataResult[$i]['satuan_order_id'] = 0;
                $dataResult[$i]['satuan_order_text'] = "";
                $dataResult[$i]['qty_order'] = 0;
                $dataResult[$i]['qty_konversi'] = 0;
                $dataResult[$i]['harga_satuan'] = 0;
                $dataResult[$i]['potongan_harga'] = 0;
                $dataResult[$i]['biaya_tambahan'] = 0;
                $dataResult[$i]['total_harga'] = 0;

                if ($dataResult[$i]['stok_total'] > 0) {
                    array_push($resultArr, $dataResult[$i]);
                }
            }
            return response()->setJSON([
                'data' => $resultArr,
                'token' => csrf_hash(),
                'status' => true
            ]);
        }
    }

    public function dropdownSatuanOrder()
    {
        $stock = $this->stockModel->find($this->request->getVar('stock_id'));
        if ($stock['kemasan_id'] == 0) {
            $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stock['barang2_id']);
            $satuanIdArr = [$barangMasterSpesifikasi['satuan_1'], $barangMasterSpesifikasi['satuan_2'], $barangMasterSpesifikasi['satuan_3']];
        } else {
            $kemasan = $this->kemasanModel->find($stock['kemasan_id']);
            $satuanIdArr = [$kemasan['satuan_id']];
        }

        $resultSatuan = $this->satuanModel->whereIn('id', $satuanIdArr)->orderBy('kode_satuan', "ASC")->findAll();

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'data' => $resultSatuan
        ]);
    }

    public function hitungKonversi()
    {
        $stock = $this->stockModel->find($this->request->getVar('stock_id'));
        $qtyStock = $this->request->getVar('qty_stock');
        $qtyOrder = $this->request->getVar('qty_order');
        $satuanOrderId = $this->request->getVar('satuan_order_id');
        $hasilKonversi = 0;

        if ($stock['kemasan_id'] == 0) {
            // INI BARANG
            $barangMasterSpesifikasi = $this->barangMasterSpesifikasiModel->find($stock['barang2_id']);
            if ($barangMasterSpesifikasi['satuan_1'] == $satuanOrderId) {
                $hasilKonversi = $qtyOrder * 1;
            } elseif ($barangMasterSpesifikasi['satuan_2'] == $satuanOrderId) {
                $hasilKonversi = $qtyOrder * $barangMasterSpesifikasi['konversi_satuan_2'];
            } elseif ($barangMasterSpesifikasi['satuan_3'] == $satuanOrderId) {
                $hasilKonversi = $qtyOrder * $barangMasterSpesifikasi['konversi_satuan_3'];
            }
        } else {
            // INI KEMASAN
            $hasilKonversi = $qtyOrder / 1;
        }

        if ($qtyStock < $hasilKonversi) {
            return response()->setJSON([
                'status' => false,
                'token' => csrf_hash(),
                'message' => "Stok digudang tidak cukup !"
            ]);
        } else {
            return response()->setJSON([
                'status' => true,
                'token' => csrf_hash(),
                'qty_konversi' => $hasilKonversi
            ]);
        }
    }

    public function dropdownListBarang()
    {
        $data = $this->stockModel->getBarangAndStock(
            $this->request->getVar('type_barang'),
            $this->request->getVar('divisi_id'),
            $this->request->getVar('warehouse_id')
        );
        return response()->setJSON([
            'data' => $data,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function getSalesOrderLainNo()
    {
        $last_day = date("Y-m-t", strtotime(date('Y') . "-" . date('m') . "-" . date('d')));
        $divisiID = $this->request->getVar('divisi_id');

        if (empty($divisiID)) {
            $no = $this->salesOrderLainModel->get_no(date('m'), date('Y'), $last_day, "", $divisiID);
        } else {
            $divisi = $this->divisiModel->where('id', $divisiID)->first();
            $no = $this->salesOrderLainModel->get_no(date('m'), date('Y'), $last_day, strtoupper($divisi['divisi']), $divisiID);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no,
            'token' => csrf_hash()
        ]);
    }

    public function getListSalesOrderDetail()
    {
        $id = decrypt($this->request->getVar('sales_order_lain_id'));
        $salesOrderDetail = $this->salesOrderLainDetailModel->detail($id);

        return response()->setJSON([
            'data' => $salesOrderDetail,
            'token' => csrf_hash(),
            'status' => true
        ]);
    }

    public function dropdownListCustomer()
    {
        $tipeCustomer = $this->request->getVar('tipe_customer');
        $dataResult = $this->customerModel->where([
            'deletedAt' => null,
            'tipe_customer' => $tipeCustomer,
            'company_id' => $this->this_company_id
        ])
            ->findAll();

        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'data' => $dataResult
        ]);
    }
}
