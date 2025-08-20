<?php

namespace App\Controllers\BiayaExim\BiayaLokal;

use App\Controllers\BaseController;
use App\Models\BiayaLokalDetailModel;
use App\Models\BiayaLokalModel;
use App\Models\BiayaLokalPajakModel;
use App\Models\DivisisModel;
use App\Models\MetadataModel;
use App\Models\TaxModel;
use App\Models\VendorPelayaranModel;
use Dompdf\Dompdf;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BiayaLokal extends BaseController
{
    protected $this_company_id;
    protected $metadataModel;
    protected $dompdf;
    protected $divisiModel;
    protected $taxesModel;
    protected $vendorPelayaranModel;
    protected $biayaLokalModel;
    protected $biayaLokalPajakModel;
    protected $biayaLokalDetailModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->metadataModel = new MetadataModel();
        $this->dompdf = new Dompdf();
        $this->divisiModel = new DivisisModel();
        $this->taxesModel = new TaxModel();
        $this->vendorPelayaranModel = new VendorPelayaranModel();
        $this->biayaLokalModel = new BiayaLokalModel();
        $this->biayaLokalPajakModel = new BiayaLokalPajakModel();
        $this->biayaLokalDetailModel = new BiayaLokalDetailModel();
    }

    public function index()
    {
        return view('BiayaExim/BiayaLokal/index');
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
            "biaya_lokal.company_id"  => $this->this_company_id,
            "biaya_lokal.deletedAt" => NULL,
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

        $dataResult = $this->biayaLokalModel->getList(
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
        $dataValuta = $this->metadataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataValuta" => $dataValuta,
        ];

        return view('BiayaExim/BiayaLokal/form', $data);
    }

    public function edit($id)
    {

        $id = \decrypt($id);
        $dataBiayaLokal = $this->biayaLokalModel->where('id', $id)->first();

        if ($dataBiayaLokal == null) {
            return \redirect()->to('biaya-lokal');
        }

        $dataDivisi = $this->divisiModel->getDivisiAccess();
        $dataPajak = $this->taxesModel
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%', 'PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->orderBy('type', "asc")
            ->findAll();
        $dataVendorPelayaran = $this->vendorPelayaranModel->where('deletedAt', null)->where('company_id', $this->this_company_id)->orderBy('nama_vendor', "asc")->findAll();
        $dataValuta = $this->metadataModel->where('name', "Valuta")->orderBy('value', "asc")->findAll();
        $dataBiayaLokalDetail = $this->biayaLokalDetailModel
            ->select('biaya_lokal_detail.*,metadata.value as valas_name')
            ->join('metadata', 'metadata.id = biaya_lokal_detail.valas_id', 'left')
            ->where('biaya_lokal_id', $id)
            ->where('biaya_lokal_detail.deletedAt', null)
            ->findAll();
        $dataBiayaLokalPajak = $this->biayaLokalPajakModel
            ->select(
                '
                biaya_lokal_pajak.*,
                taxes.type as type_tax, 
                taxes.name as tax_name
            '
            )
            ->join('taxes', 'taxes.id = biaya_lokal_pajak.tax_id', 'left')
            ->where('biaya_lokal_id', $id)
            ->where('biaya_lokal_pajak.deletedAt', null)
            ->findAll();

        $data = [
            "dataDivisi" => $dataDivisi,
            "dataPajak" => $dataPajak,
            "dataVendorPelayaran" => $dataVendorPelayaran,
            "dataValuta" => $dataValuta,
            "dataBiayaLokal" => $dataBiayaLokal,
            "dataBiayaLokalDetail" => $dataBiayaLokalDetail,
            "dataBiayaLokalPajak" => $dataBiayaLokalPajak
        ];

        return view('BiayaExim/BiayaLokal/form', $data);
    }

    public function print($id)
    {

        $id = decrypt($id);
        $dataBiayaLokal = $this->biayaLokalModel->where('id', $id)->first();
        if ($dataBiayaLokal == null) {
            return redirect()->to('biaya-impor');
        }

        $filename = $dataBiayaLokal['no_invoice'];

        $data = [];
        $detailBiayaList = [];
        $taxList = [];
        $taxReturnList = [];
        $biayaTotal = 0;
        $taxTotal = 0;
        $taxReturnTotal = 0;

        $dataBiayaLokal = $this->biayaLokalModel
            ->select("biaya_lokal.*, vendor_pelayaran.nama_vendor")
            ->join('vendor_pelayaran', 'vendor_pelayaran.id = biaya_lokal.vendor_pelayaran_id', 'left')
            ->where('biaya_lokal.id', $id)
            ->first();

        $dataBiayaLokalDetail = $this->biayaLokalDetailModel
            ->where('biaya_lokal_id', $id)
            ->where('deletedAt', null)
            ->findAll();

        // Pajak Bukti Pengeluaran (Bon Putih)
        $taxData = $this->biayaLokalPajakModel
            ->select('biaya_lokal_pajak.*,taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_lokal_pajak.tax_id', 'left')
            ->where('biaya_lokal_id', $id)
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPN Masukan 11%'])
            ->where('biaya_lokal_pajak.deletedAt', null)
            ->findAll();

        // Pajak Bukti Pemasukkan (Bon Merah)
        $taxReturnData = $this->biayaLokalPajakModel
            ->select('biaya_lokal_pajak.*,taxes.name as tax_name')
            ->join('taxes', 'taxes.id = biaya_lokal_pajak.tax_id', 'left')
            ->where('biaya_lokal_id', $id)
            ->whereIn('taxes.name', ['PPN Masukan 0%', 'PPh Pasal 21', 'PPh Pasal 23', 'PPh Pasal 4 (2)'])
            ->where('biaya_lokal_pajak.deletedAt', null)
            ->findAll();

        foreach ($dataBiayaLokalDetail as $det) {
            $detailBiayaList[] = $det['uraian_biaya'];
            $biayaTotal += $det['nilai_biaya_idr'];
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
        $data["dataBiayaLokal"] = $dataBiayaLokal;
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

        $this->dompdf->loadHtml(view('BiayaExim/BiayaLokal/print', $data));
        $this->dompdf->setPaper('A5', 'landscape');
        $this->dompdf->render();
        $this->dompdf->stream($filename, array("Attachment" => false));
        exit(0);
    }

    public function store()
    {
        // return response()->setJSON([
        //     'POST' => $_POST,
        //     'listPajak' => \json_decode($_POST['listPajak']),
        //     'listBiayaLokal' => \json_decode($_POST['listBiayaLokal']),
        //     // 'listContainer' => \json_decode($_POST['listContainer']),
        //     // 'listBarang' => \json_decode($_POST['listBarang'])
        // ]);

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $noInvoice = $this->request->getVar('no_invoice');
            $tanggalInvoice = $this->request->getVar('tanggal_invoice');
            if ($noInvoice == "AUTO GENERATE") {
                $noInvoice = $this->biayaLokalModel->generateNumber(
                    $tanggalInvoice
                );
            }

            if ($this->checkNumber($noInvoice, null) == false) {
                return response()->setJSON([
                    'status' => false,
                    'message' => "No invoice sudah digunakan"
                ]);
            }
            $biayaLokalId = $this->biayaLokalModel->insert([
                'company_id' => $this->this_company_id,
                'divisi_id' => $this->request->getVar('divisi_id'),
                'vendor_pelayaran_id' => $this->request->getVar('vendor_pelayaran_id'),
                'tanggal_invoice' =>  $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : null,
                'no_invoice' => $noInvoice,
                'total_faktur_before_tax' => $this->request->getVar('total_faktur_before_tax'),
                'total_faktur' => $this->request->getVar('total_faktur'),
                'status_posting' => 0
            ]);

            foreach (\json_decode($_POST['listBiayaLokal']) as $l) {
                $this->biayaLokalDetailModel->insert([
                    'biaya_lokal_id' => $biayaLokalId,
                    'valas_id' => $l->valas_id,
                    'uraian_biaya' => $l->uraian_biaya,
                    'nilai_biaya' => $l->nilai_biaya,
                    'nilai_exchange_rate' => $l->nilai_exchange_rate,
                    'nilai_biaya_idr' => $l->nilai_biaya_idr
                ]);
            }

            foreach (\json_decode($_POST['listPajak']) as $l) {
                $this->biayaLokalPajakModel->insert([
                    'biaya_lokal_id' => $biayaLokalId,
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
            return \response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        // return response()->setJSON([
        //     'POST' => $_POST,
        //     'listPajak' => \json_decode($_POST['listPajak']),
        //     'listBiayaLokal' => \json_decode($_POST['listBiayaLokal']),
        //     // 'listContainer' => \json_decode($_POST['listContainer']),
        //     // 'listBarang' => \json_decode($_POST['listBarang'])
        // ]);

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

            $this->biayaLokalModel->update($id, [
                'divisi_id' => $this->request->getVar('divisi_id'),
                'vendor_pelayaran_id' => $this->request->getVar('vendor_pelayaran_id'),
                'tanggal_invoice' =>  $this->request->getVar("tanggal_invoice") ? date("Y/m/d", strtotime(str_replace("/", "-", $this->request->getVar("tanggal_invoice")))) : null,
                'no_invoice' => $noInvoice,
                'total_faktur_before_tax' => $this->request->getVar('total_faktur_before_tax'),
                'total_faktur' => $this->request->getVar('total_faktur'),
                'status_posting' => 0
            ]);

            $idBiayaLokalDetailUsed = array();
            $idBiayaLokalPajakUsed = array();

            foreach (\json_decode($_POST['listBiayaLokal']) as $l) {
                $check = $this->biayaLokalDetailModel
                    ->where('id', $l->id_biaya_lokal_detail)
                    ->first();
                if ($check != null) {
                    $this->biayaLokalDetailModel->update($check['id'], [
                        'valas_id' => $l->valas_id,
                        'uraian_biaya' => $l->uraian_biaya,
                        'nilai_biaya' => $l->nilai_biaya,
                        'nilai_exchange_rate' => $l->nilai_exchange_rate,
                        'nilai_biaya_idr' => $l->nilai_biaya_idr
                    ]);

                    array_push($idBiayaLokalDetailUsed, $check['id']);
                } else {
                    $biayaLokalDetailId =  $this->biayaLokalDetailModel->insert([
                        'biaya_lokal_id' => $id,
                        'valas_id' => $l->valas_id,
                        'uraian_biaya' => $l->uraian_biaya,
                        'nilai_biaya' => $l->nilai_biaya,
                        'nilai_exchange_rate' => $l->nilai_exchange_rate,
                        'nilai_biaya_idr' => $l->nilai_biaya_idr
                    ]);

                    array_push($idBiayaLokalDetailUsed, $biayaLokalDetailId);
                }
            }

            foreach (\json_decode($_POST['listPajak']) as $l) {
                $check = $this->biayaLokalPajakModel
                    ->where('id', $l->id_biaya_lokal_pajak)
                    ->first();

                if ($check != null) {
                    $this->biayaLokalPajakModel->update($check['id'], [
                        'tax_id' => $l->tax_id,
                        'no_faktur_pajak' => $l->no_faktur_pajak,
                        'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                        'status_pajak' => $l->tax_status,
                        'nilai_pajak' => $l->nilai_pajak,
                        'keterangan_pajak' => $l->keterangan_pajak
                    ]);

                    array_push($idBiayaLokalPajakUsed, $check['id']);
                } else {
                    $biayaLokalPajakId = $this->biayaLokalPajakModel->insert([
                        'biaya_lokal_id' => $id,
                        'tax_id' => $l->tax_id,
                        'no_faktur_pajak' => $l->no_faktur_pajak,
                        'tanggal_faktur_pajak' => $l->tanggal_faktur_pajak ? date("Y/m/d", strtotime(str_replace("/", "-", $l->tanggal_faktur_pajak))) : null,
                        'status_pajak' => $l->tax_status,
                        'nilai_pajak' => $l->nilai_pajak,
                        'keterangan_pajak' => $l->keterangan_pajak
                    ]);
                    array_push($idBiayaLokalPajakUsed, $biayaLokalPajakId);
                }
            }

            $this->biayaLokalDetailModel->whereNotIn('id', $idBiayaLokalDetailUsed)->where('biaya_lokal_id', $id)->delete();
            $this->biayaLokalPajakModel->whereNotIn('id', $idBiayaLokalPajakUsed)->where('biaya_lokal_id', $id)->delete();

            $db->transCommit();

            return response()->setJSON([
                'message' => "Data berhasil diupdate",
                'token' => csrf_hash(),
                'status' => true,
            ]);
        } catch (Exception $e) {
            return \response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy()
    {
        try {
            $id = \decrypt($this->request->getVar('id'));
            $this->biayaLokalModel->delete($id);
            $this->biayaLokalDetailModel->where('biaya_lokal_id', $id)->delete();
            $this->biayaLokalPajakModel->where('biaya_lokal_id', $id)->delete();

            return \response()->setJSON([
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
        $this->biayaLokalModel->update($id, ['status_posting' => 1]);
        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Data berhasil diposting"
        ]);
    }


    public function unposting()
    {
        $id = \decrypt($this->request->getVar('id'));
        $this->biayaLokalModel->update($id, ['status_posting' => 0]);
        return response()->setJSON([
            'status' => true,
            'token' => csrf_hash(),
            'message' => "Data berhasil diunposting"
        ]);
    }

    public function exportExcel()
    {
        $condition = [
            "biaya_lokal.company_id" => $this->this_company_id,
            "biaya_lokal.deletedAt"  => null,
        ];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "dateStart"     => $this->request->getVar("dateStart")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart"))))
                : "",
            "dateEnd"       => $this->request->getVar("dateEnd")
                ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd"))))
                : "",
            "status_posting" => $this->request->getGet("status_posting"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
        ];

        $biayaLokal = $this->biayaLokalModel->getList($condition, $addCondition, 100000000, 0);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        /**
         * ========================
         * Header Utama
         * ========================
         */
        $headers = [
            'NO',
            'NO INVOICE',
            'TANGGAL INVOICE',
            'DEPARTEMEN',
            'VENDOR',
            'TOTAL INVOICE (IDR)',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', strtoupper($header));
            $col++;
        }

        // Styling header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFDCE6F1'); // biru lembut
        $sheet->getStyle('A1:F1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        /**
         * ========================
         * Isi Data
         * ========================
         */
        $row = 2;
        $no  = 1;

        foreach ($biayaLokal['data'] as $data) {
            // Data utama
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $data['no_invoice']);
            $sheet->setCellValue("C{$row}", $data['tanggal_invoice']);
            $sheet->setCellValue("D{$row}", $data['divisi']);
            $sheet->setCellValue("E{$row}", $data['nama_vendor']);
            $sheet->setCellValue("F{$row}", $data['total_faktur']);

            // Format angka rupiah (tanpa Rp)
            $sheet->getStyle("F{$row}")->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $row++;

            /**
             * ========================
             * Detail Pajak
             * ========================
             */
            $pajakHeader = [
                'TGL FAKTUR PAJAK',
                'NO FAKTUR PAJAK',
                'PAJAK',
                'NILAI PAJAK',
                'STATUS',
                'KETERANGAN'
            ];

            $col = 'B';
            foreach ($pajakHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailPajak = $this->biayaLokalPajakModel
                ->select('biaya_lokal_pajak.*, taxes.type as type_tax, taxes.name as tax_name')
                ->join('taxes', 'taxes.id = biaya_lokal_pajak.tax_id', 'left')
                ->where('biaya_lokal_pajak.biaya_lokal_id', $data['id'])
                ->where('biaya_lokal_pajak.deletedAt', null)
                ->findAll();

            if ($detailPajak) {
                foreach ($detailPajak as $pjk) {
                    $sheet->setCellValue("B{$row}", $pjk['tanggal_faktur_pajak']);
                    $sheet->setCellValue("C{$row}", $pjk['no_faktur_pajak']);
                    $sheet->setCellValue("D{$row}", $pjk['tax_name']);
                    $sheet->setCellValue("E{$row}", $pjk['nilai_pajak']);
                    $sheet->getStyle("E{$row}")->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->setCellValue("F{$row}", $pjk['status_pajak']);
                    $sheet->setCellValue("G{$row}", $pjk['keterangan_pajak']);
                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada pajak -");
                $row++;
            }

            /**
             * ========================
             * Detail Biaya
             * ========================
             */
            $biayaHeader = [
                'DETAIL BIAYA',
                'CURRENCY',
                'NILAI',
                'EXCHANGE RATE',
                'NILAI (IDR)'
            ];

            $col = 'B';
            foreach ($biayaHeader as $header) {
                $sheet->setCellValue($col . $row, strtoupper($header));
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF2F2F2');
                $col++;
            }
            $row++;

            $detailBiaya = $this->biayaLokalDetailModel
                ->select('biaya_lokal_detail.*, metadata.value as valas_name')
                ->join('metadata', 'metadata.id = biaya_lokal_detail.valas_id', 'left')
                ->where('biaya_lokal_id', $data['id'])
                ->where('biaya_lokal_detail.deletedAt', null)
                ->findAll();

            if ($detailBiaya) {
                foreach ($detailBiaya as $by) {
                    $sheet->setCellValue("B{$row}", $by['uraian_biaya']);
                    $sheet->setCellValue("C{$row}", $by['valas_name']);
                    $sheet->setCellValue("D{$row}", $by['nilai_biaya']);
                    $sheet->setCellValue("E{$row}", $by['nilai_exchange_rate']);
                    $sheet->setCellValue("F{$row}", $by['nilai_biaya_idr']);

                    // Format angka
                    $sheet->getStyle("D{$row}")->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle("E{$row}")->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle("F{$row}")->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $row++;
                }
            } else {
                $sheet->setCellValue("B{$row}", "- Tidak ada biaya -");
                $row++;
            }

            // Spasi antar invoice
            $row++;
        }

        /**
         * ========================
         * Output
         * ========================
         */
        $fileName = 'Export_Biaya_Lokal_' . time() . '.xlsx';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody((function () use ($spreadsheet) {
                ob_start();
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                return ob_get_clean();
            })());
    }

    private function checkNumber($noInvoice, $id)
    {
        $checkQry = $this->biayaLokalModel
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
