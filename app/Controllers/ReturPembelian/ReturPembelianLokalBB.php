<?php

namespace App\Controllers\ReturPembelian;

use App\Controllers\BaseController;
use App\Models\BC25Model;
use App\Models\BC41Model;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;
use App\Models\PengembalianBarangDetailModel;
use App\Models\PengembalianBarangModel;
use App\Models\StockDetail2Model;
use App\Models\StockDetailModel;
use App\Models\StockModel;
use App\Models\SupplierModel;
use Dompdf\Dompdf;
use Exception;

class ReturPembelianLokalBB extends BaseController
{
    protected $this_company_id;
    protected $this_user_id;
    protected $this_divisi_access;
    protected $divisiModel;
    protected $supplierModel;
    protected $pengembalianBarangModel;
    protected $pengembalianBarangDetailModel;
    protected $penerimaanBarangModel;
    protected $metaDataModel;
    protected $bc41Model;
    protected $penerimaanBarangDetailModel;
    protected $stockModel;
    protected $stockDetailModel;
    protected $stockDetail2Model;
    protected $bc25Model;
    protected $dompdf;

    public function __construct()
    {
        $this->this_user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->this_divisi_access = session()->get('login')->this_access_divisi_id;
        $this->divisiModel = new DivisisModel();
        $this->supplierModel = new SupplierModel();
        $this->pengembalianBarangModel = new PengembalianBarangModel();
        $this->pengembalianBarangDetailModel = new PengembalianBarangDetailModel();
        $this->penerimaanBarangModel = new PenerimaanBarangModel();
        $this->penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $this->metaDataModel = new MetadataModel();
        $this->bc41Model = new BC41Model();
        $this->bc25Model = new BC25Model();
        $this->stockModel = new StockModel();
        $this->stockDetailModel = new StockDetailModel();
        $this->stockDetail2Model = new StockDetail2Model();
        $this->dompdf = new Dompdf();
    }

    public function index()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataSupplier' => $this->supplierModel->getSupplierByType('BAHAN BAKU')
        ];

        return view('Warehouse/returnBarang/indexLokalBB', $data);
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

        $condition = [
            "pengembalian_barang.company_id" => $this->this_company_id,
            "pengembalian_barang.deletedAt" => null,
            "penerimaan_barang.status_penerimaan" => "LOKAL",
            "penerimaan_barang.tipe_bahan" => "BAKU",
        ];

        $addCondition = [
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
            "search" => $this->request->getVar('search'),
            "status" => $this->request->getVar("status"),
            "divisi_id" => $this->request->getVar('divisi_id'),
            "warehouse_id" => $this->request->getVar('warehouse_id'),
            "supplier_id" => $this->request->getVar("supplier_id"),
            "status_post" => $this->request->getVar('status_post'),
            "start_date" => $this->request->getVar("start_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("start_date")))) : "",
            "end_date" => $this->request->getVar("end_date") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("end_date")))) : "",
            "divisi_access_id" => $this->this_divisi_access,
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $penerimaanBarangData = $this->pengembalianBarangModel->getPengembalianBarangList($condition, $addCondition, $limit, $offset);

        $dataPenerimaanBarang = [];
        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($penerimaanBarangData['data'] as $data) {
            $status_bc = 0;
            $dokumen_pengeluaran = $this->metaDataModel->where('id', $data->bc_pengeluaran_id)->first();

            if ($data->bc_pengeluaran_id == 0) {
                // TIDAK ADA
                if ($data->status_post == "FINISH") {
                    $status_bc = 1;
                } else {
                    $status_bc = 0;
                }
            } else {
                // ADA DOKUMEN BC   
                // 54 -> 4.1
                // 49 -> 2.5
                $bc25 = $this->bc25Model->where('pengembalian_barang_id', $data->id)->first();
                $bc41 = $this->bc41Model->where('pengembalian_barang_id', $data->id)->first();

                if ($bc25 != null || $bc41 != null) {
                    $status_bc = 1;
                } else {
                    $status_bc = 0;
                }
            }

            array_push($dataPenerimaanBarang, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "no_surat_jalan"        => $data->no_surat_jalan,
                "no_penerimaan_barang"  => $data->no_penerimaan_barang,
                "supplier_name"         => $data->supplier_name,
                "tanggal_surat_jalan"   => $data->tanggal_surat_jalan ? date("d/m/Y", strtotime($data->tanggal_surat_jalan)) : "",
                "divisi_name"           => $data->divisi_name,
                "warehouse_name"        => $data->warehouse_name,
                "status_post"           => $data->status_post,
                "status_bc"             => $status_bc,
                "dokumen_pengeluaran"   => $dokumen_pengeluaran == null ? "NON PABEAN" : $dokumen_pengeluaran['value'],
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $penerimaanBarangData['totalData'],
            "recordsFiltered"   => $penerimaanBarangData['totalFilteredData'],
            "data"              => $dataPenerimaanBarang,
            "payload"           => $payload,
        ];

        echo json_encode($data);
        return;
    }

    public function create()
    {
        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataDokumenPabean' => $this->metaDataModel->where('name', "jenis_dok_aju")->where('value', "BC 4.1")->findAll()
        ];

        return view('Warehouse/returnBarang/formLokalBB', $data);
    }

    public function createAction()
    {
        $first = $this->pengembalianBarangModel->where('company_id', $this->this_company_id)->where('no_surat_jalan', $this->request->getVar('no_surat_jalan'))->first();
        $detailBarang = json_decode($this->request->getVar('listBarang'));

        if ($first != null) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "No Surat Jalan Sudah Ada",
                'status' => false
            ]);
        }

        if (count($detailBarang) == 0) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Barang yang diretur tidak ada",
                'status' => false
            ]);
        }

        // Cek Kekosongan
        $qtyRetur = 0;
        foreach ($detailBarang as $d) {
            $qtyRetur += $d->jml_retur;
        }

        if ($qtyRetur == 0) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Isikan minimal satu item barang yang akan direturn",
                'status' => false
            ]);
        }

        $id = $this->pengembalianBarangModel->insert([
            'company_id' => $this->this_company_id,
            'bc_pengeluaran_id' => $this->request->getVar('bc_pengeluaran_id'),
            'penerimaan_barang_id' => $this->request->getVar('penerimaan_barang_id'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'status_post' => "WAITING",
            'tanggal_surat_jalan' => $this->request->getVar('tanggal_retur_barang') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('tanggal_retur_barang')), "Y-m-d") : "",
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        foreach ($detailBarang as $d) {
            if ($d->jml_retur != 0) {
                $this->pengembalianBarangDetailModel->insert([
                    'pengembalian_barang_id' => $id,
                    'penerimaan_barang_detail_id' => $d->id,
                    'jumlah_return' => $d->jml_retur,
                    'keterangan_return' => $d->ket_retur
                ]);
            }
        }

        return response()->setJSON([
            'message' => "Retur Barang berhasil disimpan",
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function updateAction()
    {
        $id = decrypt($this->request->getVar('id'));
        $detailBarang = json_decode($this->request->getVar('listBarang'));

        if (count($detailBarang) == 0) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Barang yang diretur tidak ada",
                'status' => false
            ]);
        }

        // Cek Kekosongan
        $qtyRetur = 0;
        foreach ($detailBarang as $d) {
            $qtyRetur += $d->jml_retur;
        }

        if ($qtyRetur == 0) {
            return response()->setJSON([
                'token' => csrf_hash(),
                'message' => "Isikan minimal satu item barang yang akan direturn",
                'status' => false
            ]);
        }

        $this->pengembalianBarangModel->update($id, [
            'company_id' => $this->this_company_id,
            'bc_pengeluaran_id' => $this->request->getVar('bc_pengeluaran_id'),
            'penerimaan_barang_id' => $this->request->getVar('penerimaan_barang_id'),
            'no_surat_jalan' => $this->request->getVar('no_surat_jalan'),
            'tanggal_surat_jalan' => $this->request->getVar('tanggal_retur_barang') ? date_format(date_create_from_format("d/m/Y", $this->request->getVar('tanggal_retur_barang')), "Y-m-d") : "",
            'keterangan' => $this->request->getVar('keterangan')
        ]);

        // DELETE FIRST
        $this->pengembalianBarangDetailModel->where('pengembalian_barang_id', $id)->delete();

        foreach ($detailBarang as $d) {
            if ($d->jml_retur != 0) {
                $this->pengembalianBarangDetailModel->insert([
                    'pengembalian_barang_id' => $id,
                    'penerimaan_barang_detail_id' => $d->id,
                    'jumlah_return' => $d->jml_retur,
                    'keterangan_return' => $d->ket_retur
                ]);
            }
        }

        return response()->setJSON([
            'message' => "Retur Barang berhasil disimpan",
            'token' => csrf_hash(),
            'status' => true,
        ]);
    }

    public function update($id)
    {
        $id = decrypt($id);

        if ($this->pengembalianBarangModel->find($id) == null) {
            return redirect()->to('retur-po-lokal-bb');
        }

        $dataPengembalianBarang = $this->pengembalianBarangModel->find($id);
        $dataPengembalianBarangDetail = $this->pengembalianBarangModel->getReturDetail(
            $id,
            $dataPengembalianBarang['penerimaan_barang_id']
        );
        $dataPenerimaanBarang = $this->penerimaanBarangModel
            ->select('penerimaan_barang.*,warehouses.warehouse_name,suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->where('penerimaan_barang.id', $dataPengembalianBarang['penerimaan_barang_id'])
            ->findAll();

        $resultPengembalianDetail = array();
        foreach ($dataPengembalianBarangDetail as $d) {
            if ($d['jml_retur'] != 0) {
                array_push($resultPengembalianDetail, $d);
            }
        }

        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataDokumenPabean' => $this->metaDataModel->where('name', "jenis_dok_aju")->where('value', "BC 4.1")->findAll(),
            'dataPengembalianBarang' => $dataPengembalianBarang,
            'dataPengembalianBarangDetail' => $resultPengembalianDetail,
            'dataPenerimaanBarang' => $dataPenerimaanBarang
        ];

        return view('Warehouse/returnBarang/formLokalBB', $data);
    }

    public function print($id)
    {
        $id = decrypt($id);

        if ($this->pengembalianBarangModel->find($id) == null) {
            return redirect()->to('retur-po-lokal-bb');
        }

        $dataPengembalianBarang = $this->pengembalianBarangModel->find($id);
        $dataPengembalianBarangDetail = $this->pengembalianBarangModel->getReturDetail(
            $id,
            $dataPengembalianBarang['penerimaan_barang_id']
        );
        $dataPenerimaanBarang = $this->penerimaanBarangModel
            ->select('penerimaan_barang.*,warehouses.warehouse_name,suppliers.name as supplier_name,divisis.divisi')
            ->join('suppliers', 'suppliers.id = penerimaan_barang.supplier_id', 'left')
            ->join('warehouses', 'warehouses.id = penerimaan_barang.warehouse_id', 'left')
            ->join('divisis', 'divisis.id = penerimaan_barang.divisi_id', 'left')
            ->where('penerimaan_barang.id', $dataPengembalianBarang['penerimaan_barang_id'])
            ->findAll();

        $resultPengembalianDetail = array();
        foreach ($dataPengembalianBarangDetail as $d) {
            if ($d['jml_retur'] != 0) {
                array_push($resultPengembalianDetail, $d);
            }
        }


        $data = [
            'dataDivisi' => $this->divisiModel->getDivisiAccess(),
            'dataDokumenPabean' => $this->metaDataModel->where('name', "jenis_dok_aju")->where('value', "BC 4.1")->findAll(),
            'dataPengembalianBarang' => $dataPengembalianBarang,
            'dataPengembalianBarangDetail' => $resultPengembalianDetail,
            'dataPenerimaanBarang' => $dataPenerimaanBarang,
            'title' => "Retur Pembelian Lokal Bahan Baku"
        ];

        $this->dompdf->loadHtml(view('Warehouse/returnBarang/print', $data));
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("Retur Barang", array("Attachment" => false));
        exit(0);
    }

    public function delete()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->pengembalianBarangModel->delete($id);
        $this->pengembalianBarangDetailModel->where('pengembalian_barang_id', $id)->delete();

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil Hapus Retur Pembelian"
        ]);
    }

    public function posting()
    {
        $id = decrypt($this->request->getVar('id'));
        $pengembalianBarang = $this->pengembalianBarangModel->where('id', $id)->first();

        if ($pengembalianBarang == null) {
            return response()->setJSON([
                'status' => true,
                'message' => "Gagal posting, data retur tidak ditemukan"
            ]);
        }

        if ($pengembalianBarang['bc_pengeluaran_id'] == 0) {
            // NON PABEAN LANGSNG POTONG STOK
            try {
                $penerimaanBarang = $this->penerimaanBarangModel
                    ->join('pengembalian_barang', 'pengembalian_barang.penerimaan_barang_id = penerimaan_barang.id')
                    ->where('penerimaan_barang.id', $pengembalianBarang['penerimaan_barang_id'])
                    ->where('pengembalian_barang.id', $id)
                    ->first();
                $penerimaanBarangList = $this->penerimaanBarangDetailModel
                    ->join('pengembalian_barang_detail', 'pengembalian_barang_detail.penerimaan_barang_detail_id = penerimaan_barang_detail.id')
                    ->where('pengembalian_barang_id', $id)
                    ->where('pengembalian_barang_detail.deletedAt', null)
                    ->where('penerimaan_barang_detail.deletedAt', null)
                    ->findAll();

                // STOK BARANG DIINPUT
                foreach ($penerimaanBarangList as $p) {
                    $stok = $this->stockModel->insertStok(
                        $penerimaanBarang['company_id'],
                        $penerimaanBarang['warehouse_id'],
                        $penerimaanBarang['divisi_id'],
                        $penerimaanBarang['tipe_bahan'] == "BAKU" ? "bahan_baku" : "bahan_penolong",
                        $p['barang_id'],
                        $p['spesifikasi_id'],
                        ($p['jumlah_return'] * -1)
                    );

                    // DETAIL
                    $stokDetail = $this->stockDetailModel->insertStokDetail(
                        $stok,
                        $p['jumlah_return'],
                        "Out",
                        date('Y-m-d'),
                        $this->this_user_id,
                        "RETUR",
                        $penerimaanBarang['no_surat_jalan'],
                        $p['keterangan_return'] ? $p['keterangan_return'] : "-"
                    );

                    // SUB DETAIL
                    $this->stockDetail2Model->insertStokDetail2(
                        $penerimaanBarang['bc_type'],
                        $stok,
                        $stokDetail,
                        $p['jumlah_return'],
                        "-",
                        $penerimaanBarang['no_surat_jalan'],
                        $penerimaanBarang['no_penerimaan_barang'],
                        $penerimaanBarang['supplier_id'],
                        $p['harga'],
                        $p['harga_harian'],
                        $p['harga_bulanan'],
                    );
                }
            } catch (Exception $e) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "Gagal Posting : Terjadi kesalahan saat mengeluarkan stok",
                    'error' => $e->getTrace(),
                    'token' => csrf_hash()
                ]);
            }
        }


        $this->pengembalianBarangModel->update($id, [
            'status_post' => "FINISH"
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil Posting Retur Pembelian"
        ]);
    }


    public function unposting()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->pengembalianBarangModel->update($id, [
            'status_post' => "WAITING"
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Berhasil Unposting Retur Pembelian"
        ]);
    }

    public function dropdownPenerimaanBarang()
    {
        $divisiId = $this->request->getVar('divisi_id');
        $dataList = $this->pengembalianBarangModel->dropdownPenerimaanBarang(
            $this->this_company_id,
            $divisiId,
            "BAKU",
            "LOKAL"
        );

        return response()->setJSON([
            'data' => $dataList,
            'status' => true
        ]);
    }

    public function detailBarang()
    {
        $id = $this->request->getVar('id');
        $penerimaanBarangId = $this->request->getVar('penerimaan_barang_id');

        $id = $id == "" ? null : $id;
        $dataList = $this->pengembalianBarangModel->getReturDetail(
            $id,
            $penerimaanBarangId
        );

        return response()->setJSON([
            'data' => $dataList,
            'status' => true
        ]);
    }

    public function generateNumber()
    {
        $divisiId = $this->request->getVar('divisi_id');
        if (empty($divisiId)) {
            $no = $this->pengembalianBarangModel->get_no(date('m'), date('Y'), "", $divisiId);
        } else {
            $divisi = $this->divisiModel->where('id', $divisiId)->first();
            $no = $this->pengembalianBarangModel->get_no(date('m'), date('Y'), $divisi['divisi'], $divisiId);
        }
        return response()->setJSON([
            'status' => true,
            'data' => $no
        ]);
    }
}
