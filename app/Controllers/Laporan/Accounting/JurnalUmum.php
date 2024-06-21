<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use App\Models\JurnalUmumModel;
use App\Models\TransaksiJurnalModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JurnalUmum extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $MetadataModel;
    protected $transaksiJurnalModel;
    protected $encrypter;

    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->Sub_AkunsModel = new Sub_AkunsModel();
        $this->KategoriAkunsModel = new KategoriAkunsModel();
        $this->HeaderAkunsModel = new HeaderAkunsModel();
        $this->MetadataModel = new MetadataModel();
        $this->jurnalUmumModel = new JurnalUmumModel();
        $this->transaksiJurnalModel = new TransaksiJurnalModel();
        $this->encrypter = \Config\Services::encrypter();
    }
    public function index()
    {
        $dateStart = $this->request->getPost('dateStart');
        $dateEnd = $this->request->getPost('dateEnd');

        if ($dateEnd) {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
            $condition2 = [
                'tanggal_transaksi >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_transaksi <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
        } else {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
            $condition2 = [
                'tanggal_transaksi >=' => date('Y-m-01'),
                'tanggal_transaksi <=' => date('Y-m-d')
            ];
        }

        $dataMetadata = $this->MetadataModel
            ->asObject()
            ->where('name', 'Kelompok Akun')
            ->groupStart()
            ->like('value', 'Aktiva / Harta')
            ->orLike('value', 'Kewajiban / Hutang')
            ->orLike('value', 'Modal')
            ->groupEnd()
            ->findAll();
        $dataKategoriAkun = $this->KategoriAkunsModel->getAPAR($this->this_company_id);
        $dataHeaderAkun = $this->HeaderAkunsModel->getAPAR($this->this_company_id);
        $dataSubAkun = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $dataMetadataTipeTransaksi = $this->MetadataModel
            ->asObject()
            ->where('name', 'tipe_transaksi')
            ->findAll();
        foreach ($dataMetadataTipeTransaksi as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }
        $dataTransaksiJurnal = $this->transaksiJurnalModel
            ->asObject()
            ->where($condition2)
            ->findAll();
        foreach ($dataTransaksiJurnal as $val) {
            $val->tipe_transaksi_hex = bin2hex($this->encrypter->encrypt($val->type_transaksi));
            $val->id_transaksi_hex = bin2hex($this->encrypter->encrypt($val->id));
        }
        $dataJurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition)
            ->findAll();
        $dataJurnalUmumWithGroup = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header, jurnal_umum.id_transaksi as trans_id, transaksi_jurnal.valas as valas, transaksi_jurnal.exchange_rate as exchange_rate')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition)
            ->groupBy('trans_id')
            ->findAll();
        // var_dump($dataJurnalUmumWithGroup);
        // var_dump($dataJurnalUmum);

        $data = [
            "dataMetadata" => $dataMetadata,
            "dataKategoriAkun" => $dataKategoriAkun,
            "dataHeaderAkun" => $dataHeaderAkun,
            "dataSubAkuns" => $dataSubAkun,
            "dataTransaksiJurnal" => $dataTransaksiJurnal,
            "dataJurnalUmum" => $dataJurnalUmum,
            "dataJurnalUmumWithGroup" => $dataJurnalUmumWithGroup,
            "dataMetadataTipeTransaksi" => $dataMetadataTipeTransaksi,
            "dateStart" => $dateStart ? $dateStart : date('01/m/Y'),
            "dateEnd" => $dateEnd ? $dateEnd : date('d/m/Y'),
        ];
        return view('Laporan/LaporanJurnalUmum/index', $data);
    }

    public function exportPDF($tglAwal, $tglAkhir, $filter)
    {
        $dompdf = new Dompdf();
        $dateStart = $tglAwal;
        $dateEnd = $tglAkhir;
        $Filter = $filter != "all" ? $this->encrypter->decrypt(hex2bin($filter)) : "";

        if ($dateStart != "" && $dateEnd != "") {
            $condition = [
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
            $condition2 = [
                'tanggal_transaksi >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_transaksi <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
            $condition3 = [
                'name' => 'tipe_transaksi',
            ];
        } else {
            $condition = [
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
            $condition2 = [
                'tanggal_transaksi >=' => date('Y-m-01'),
                'tanggal_transaksi <=' => date('Y-m-d')
            ];
            $condition3 = [
                'name' => 'tipe_transaksi',
            ];
        }
        if ($Filter != "") {
            $condition = [
                'transaksi_jurnal.type_transaksi' => $Filter,
            ];
            $condition2 = [
                'type_transaksi' => $Filter,
            ];
            $condition3 = [
                'name' => 'tipe_transaksi',
                'id' => $Filter,
            ];
        } else {
            $condition3 = [
                'name' => 'tipe_transaksi',
            ];
        }

        // var_dump($condition);
        // var_dump($condition2);
        // var_dump($condition3);
        // exit;

        $dataMetadataTipeTransaksi = $this->MetadataModel
            ->asObject()
            ->where($condition3)
            ->findAll();
        foreach ($dataMetadataTipeTransaksi as $val) {
            $val->hexid = bin2hex($this->encrypter->encrypt($val->id));
        }
        $dataTransaksiJurnal = $this->transaksiJurnalModel
            ->asObject()
            ->where($condition2)
            ->findAll();
        foreach ($dataTransaksiJurnal as $val) {
            $val->tipe_transaksi_hex = bin2hex($this->encrypter->encrypt($val->type_transaksi));
        }
        $dataJurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition)
            ->findAll();
        $dataJurnalUmumWithGroup = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header, jurnal_umum.id_transaksi as trans_id, transaksi_jurnal.valas as valas, transaksi_jurnal.exchange_rate as exchange_rate')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition)
            ->groupBy('trans_id')
            ->findAll();
        // var_dump($dataJurnalUmumWithGroup);

        $data = [
            "dataTransaksiJurnal" => $dataTransaksiJurnal,
            "dataJurnalUmum" => $dataJurnalUmum,
            "dataJurnalUmumWithGroup" => $dataJurnalUmumWithGroup,
            "dataMetadataTipeTransaksi" => $dataMetadataTipeTransaksi,
            "dateStart" => $dateStart ? date("d/m/Y", strtotime($dateStart)) : date('d/m/Y'),
            "dateEnd" => $dateEnd ? date("d/m/Y", strtotime($dateEnd)) : date('d/m/Y'),
        ];
        $dompdf->loadHtml(view('Laporan/LaporanJurnalUmum/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Jurnal Umum ", array("Attachment" => false));

        exit(0);
    }

    public function exportExcel($tglAwal, $tglAkhir, $filter)
    {

        $spreadsheet = new Spreadsheet();
        $dateStart = $tglAwal;
        $dateEnd = $tglAkhir;
        $Filter = $filter != "all" ? $this->encrypter->decrypt(hex2bin($filter)) : "";

        if ($dateStart != "" && $dateEnd != "") {
            $condition = [
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
            $condition2 = [
                'tanggal_transaksi >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_transaksi <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
            $condition3 = [
                'name' => 'tipe_transaksi',
            ];
        } else {
            $condition = [
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
            $condition2 = [
                'tanggal_transaksi >=' => date('Y-m-01'),
                'tanggal_transaksi <=' => date('Y-m-d')
            ];
            $condition3 = [
                'name' => 'tipe_transaksi',
            ];
        }
        if ($Filter != "") {
            $condition = [
                'transaksi_jurnal.type_transaksi' => $Filter,
            ];
            $condition2 = [
                'type_transaksi' => $Filter,
            ];
            $condition3 = [
                'name' => 'tipe_transaksi',
                'id' => $Filter,
            ];
        } else {
            $condition3 = [
                'name' => 'tipe_transaksi',
            ];
        }

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Tanggal')
            ->setCellValue('C1', 'Description')
            ->setCellValue('E1', 'Debit')
            ->setCellValue('F1', 'Kredit');
        $spreadsheet->getActiveSheet()->mergeCells('A1:B1');
        $spreadsheet->getActiveSheet()->mergeCells('C1:D1');

        $dataMetadataTipeTransaksi = $this->MetadataModel
            ->asObject()
            ->where($condition3)
            ->findAll();
        $dataTransaksiJurnal = $this->transaksiJurnalModel
            ->asObject()
            ->where($condition2)
            ->findAll();
        $dataJurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition)
            ->findAll();
        $dataJurnalUmumWithGroup = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header, jurnal_umum.id_transaksi as trans_id, transaksi_jurnal.valas as valas, transaksi_jurnal.exchange_rate as exchange_rate')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition)
            ->groupBy('trans_id')
            ->findAll();

        function format_ribuan($nilai)
        {
            $nilaiFloat = floatval($nilai);
            return "Rp " . number_format($nilaiFloat, 2, ',', '.');
        }
        $flag = 0;
        $column = 2;
        foreach ($dataMetadataTipeTransaksi as $Tipe) :
            foreach ($dataTransaksiJurnal as $transaksiJurnalData) :
                foreach ($dataJurnalUmumWithGroup as $jurnalUmumWithGroupData) :
                    if ($jurnalUmumWithGroupData->id_transaksi == $transaksiJurnalData->id && $transaksiJurnalData->type_transaksi === $Tipe->id) :
                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('A' . $column, date('d-m-Y', strtotime($jurnalUmumWithGroupData->tanggal_jurnal)))
                            ->setCellValue('C' . $column, $jurnalUmumWithGroupData->no_transaksi . " " . $jurnalUmumWithGroupData->value);
                        $spreadsheet->getActiveSheet()->mergeCells('A' . $column . ':B' . $column);
                        $spreadsheet->getActiveSheet()->mergeCells('C' . $column . ':F' . $column);
                        $column++;
                    endif;
                endforeach;
                // kategori
                $total_debit  = 0;
                $total_kredit = 0;
                foreach ($dataJurnalUmum as $jurnalUmumData) :
                    $flag = 1;
                    $total_debit  += $jurnalUmumData->debit;
                    $total_kredit += $jurnalUmumData->kredit;
                    if ($jurnalUmumData->id_transaksi == $transaksiJurnalData->id && $transaksiJurnalData->type_transaksi === $Tipe->id) :
                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('A' . $column, '')
                            ->setCellValue('C' . $column, $jurnalUmumData->no_sub . " - " . $jurnalUmumData->nama_sub)
                            ->setCellValue('E' . $column, format_ribuan($jurnalUmumData->debit))
                            ->setCellValue('F' . $column, format_ribuan($jurnalUmumData->kredit));
                        $spreadsheet->getActiveSheet()->mergeCells('A' . $column . ':B' . $column);
                        $spreadsheet->getActiveSheet()->mergeCells('C' . $column . ':D' . $column);
                        $column++;
                    endif;
                endforeach;
            endforeach;
        endforeach;

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan-Jurnal';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        die;
    }
}
