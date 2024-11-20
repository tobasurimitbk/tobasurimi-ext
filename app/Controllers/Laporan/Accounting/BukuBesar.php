<?php

namespace App\Controllers\Laporan\Accounting;

use App\Controllers\BaseController;
use App\Models\Sub_AkunsModel;
use App\Models\KategoriAkunsModel;
use App\Models\HeaderAkunsModel;
use App\Models\MetadataModel;
use App\Models\JurnalUmumModel;
use Dompdf\Dompdf;

class BukuBesar extends BaseController
{
    protected $token;
    protected $this_company_id;
    protected $Sub_AkunsModel;
    protected $jurnalUmumModel;
    protected $KategoriAkunsModel;
    protected $HeaderAkunsModel;
    protected $MetadataModel;
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
        $this->encrypter = \Config\Services::encrypter();
    }
    public function index()
    {
        $dateStart = $this->request->getPost('dateStart');
        $dateEnd = $this->request->getPost('dateEnd');

        if ($dateStart != "" && $dateEnd != "") {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
        } else {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
        }

        $dataMetadata = $this->MetadataModel
            ->asObject()
            ->where('name', 'Kelompok Akun')
            ->groupStart()
            ->like('value', 'Pendapatan')
            ->orLike('value', 'Beban')
            ->groupEnd()
            ->findAll();
        $dataKategoriAkun = $this->KategoriAkunsModel->getAPAR($this->this_company_id);
        $dataHeaderAkun = $this->HeaderAkunsModel->getAPAR($this->this_company_id);
        $dataSubAkun = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $dataJurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition);

        if (isset($_POST['id_sub_akun'])) {
            foreach ($_POST['id_sub_akun'] as $i) {
                $dataJurnalUmum->where('id_coa', decrypt($i));
            }
        }

        if (isset($_POST['id_header']) && @$_POST['id_header'] != "") {
            $dataJurnalUmum->where('sub_akuns.header_id', decrypt($_POST['id_header']));
        }

        $dataJurnalUmumResult =  $dataJurnalUmum->findAll();

        $dataJurnalUmumWithGroup = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition);

        if (isset($_POST['id_sub_akun'])) {
            foreach ($_POST['id_sub_akun'] as $i) {
                $dataJurnalUmumWithGroup->where('id_coa', decrypt($i));
            }
        }

        if (isset($_POST['id_header']) && @$_POST['id_header'] != "") {
            $dataJurnalUmumWithGroup->where('sub_akuns.header_id', decrypt($_POST['id_header']));
        }

        $dataJurnalUmumWithGroupResult = $dataJurnalUmumWithGroup->groupBy('id_header')->findAll();

        $data = [
            "dataMetadata" => $dataMetadata,
            "dataKategoriAkun" => $dataKategoriAkun,
            "dataHeaderAkun" => $dataHeaderAkun,
            "dataSubAkuns" => $dataSubAkun,
            "dataJurnalUmum" => $dataJurnalUmumResult,
            "dataJurnalUmumWithGroup" => $dataJurnalUmumWithGroupResult,
            "dateEnd" => $dateEnd ? $dateEnd : date('d/m/Y'),
        ];
        return view('Laporan/LaporanBukuBesar/index', $data);
    }

    public function exportPDF()
    {
        $dompdf = new Dompdf();
        $dateStart = $this->request->getPost('dateStart');
        $dateEnd = $this->request->getPost('dateEnd');

        if ($dateStart != "" && $dateEnd != "") {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateStart))),
                'tanggal_jurnal <=' => date('Y-m-d', strtotime(str_replace('/', '-', $dateEnd))),
            ];
        } else {
            $condition = [
                'jurnal_umum.company_id' => $this->this_company_id,
                'tanggal_jurnal >=' => date('Y-m-01'),
                'tanggal_jurnal <=' => date('Y-m-d')
            ];
        }

        $dataMetadata = $this->MetadataModel
            ->asObject()
            ->where('name', 'Kelompok Akun')
            ->groupStart()
            ->like('value', 'Pendapatan')
            ->orLike('value', 'Beban')
            ->groupEnd()
            ->findAll();
        $dataKategoriAkun = $this->KategoriAkunsModel->getAPAR($this->this_company_id);
        $dataHeaderAkun = $this->HeaderAkunsModel->getAPAR($this->this_company_id);
        $dataSubAkun = $this->Sub_AkunsModel->getAPAR($this->this_company_id);

        $dataJurnalUmum = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition);

        if (isset($_POST['id_sub_akun'])) {
            foreach ($_POST['id_sub_akun'] as $i) {
                $dataJurnalUmum->where('id_coa', decrypt($i));
            }
        }

        if (isset($_POST['id_header']) && @$_POST['id_header'] != "") {
            $dataJurnalUmum->where('sub_akuns.header_id', decrypt($_POST['id_header']));
        }

        $dataJurnalUmumResult =  $dataJurnalUmum->findAll();

        $dataJurnalUmumWithGroup = $this->jurnalUmumModel
            ->asObject()
            ->select('*, sub_akuns.header_id as id_header')
            ->join('sub_akuns', 'jurnal_umum.id_coa = sub_akuns.id', 'left')
            ->join('transaksi_jurnal', 'jurnal_umum.id_transaksi = transaksi_jurnal.id', 'left')
            ->join('metadata', 'transaksi_jurnal.type_transaksi = metadata.id', 'left')
            ->where($condition);

        if (isset($_POST['id_sub_akun'])) {
            foreach ($_POST['id_sub_akun'] as $i) {
                $dataJurnalUmumWithGroup->where('id_coa', decrypt($i));
            }
        }

        if (isset($_POST['id_header']) && @$_POST['id_header'] != "") {
            $dataJurnalUmumWithGroup->where('sub_akuns.header_id', decrypt($_POST['id_header']));
        }

        $dataJurnalUmumWithGroupResult = $dataJurnalUmumWithGroup->groupBy('id_header')->findAll();

        $data = [
            "dataMetadata" => $dataMetadata,
            "dataKategoriAkun" => $dataKategoriAkun,
            "dataHeaderAkun" => $dataHeaderAkun,
            "dataSubAkuns" => $dataSubAkun,
            "dataJurnalUmum" => $dataJurnalUmumResult,
            "dataJurnalUmumWithGroup" => $dataJurnalUmumWithGroupResult,
            "dateStart" => $dateStart ? date("d/m/Y", strtotime($dateStart)) : date('d/m/Y'),
            "dateEnd" => $dateEnd ? date("d/m/Y", strtotime($dateEnd)) : date('d/m/Y'),
        ];
        $dompdf->loadHtml(view('Laporan/LaporanBukuBesar/print', $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Laporan Jurnal Umum ", array("Attachment" => false));

        exit(0);
    }
}
