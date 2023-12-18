<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\BeaCukaiModel;
use App\Models\CountryModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\MetadataModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PenerimaanBarangModel;

// META DATA -> jenis_dok_aju
// BC 2.3 -> 48
// BC 2.5 -> 49
// BC 2.6.1 -> 50
// BC 2.6.2 -> 51
// BC 2.7 -> 52
// BC 4.0 -> 53
// BC 4.1 -> 54
class BC23 extends BaseController
{
    protected $this_company_id;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function index()
    {
        return \view('BeaCukai/bc-23/index');
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
            "type"          => "BC 2.3"
        ];

        $condition = [
            "penerimaan_barang.company_id"  => $this->this_company_id,
            "penerimaan_barang.deletedAt" => null,
            "penerimaan_barang.bc_type" => 48 // bc 23
        ];
        $addCondition = [
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType"),
            "noRegistrasi" => $this->request->getGet("noRegistrasi"),
            "dateStart" => $this->request->getGet("dateStart"),
            "dateFinish" => $this->request->getGet("dateFinish"),
            "status" => $this->request->getGet('status')
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");

        $beaCukaiModel = new BeaCukaiModel();

        $beaCukaiData = $beaCukaiModel->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            $status = $data->status_posting == null ? "BELUM DIBUAT" : strtoupper($data->status_post);
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => encrypt($data->id),
                "lpb_id"                => encrypt($data->lpb_id),
                "lpb_no"                => $data->no_penerimaan_barang,
                "lpb_date"              => date('d/m/Y', strtotime($data->lpb_date)),
                "po_no"                 => implode(', ', str_replace(['[', ']', '"'], '', json_decode(json_decode($data->multiple_po_no, true)))),
                "jenis_po"              => $data->status_penerimaan . " " . ($data->tipe_bahan == "PENOLONG" ? "BP" : "BB"),
                "warehouse_name"        => strtoupper($data->warehouse_name),
                "aju_no"                => $data->aju_no ?? "-",
                "no_registration"       => $data->no_registration ?? "-",
                "validation_date"       => $data->validation_date ?? "-" ? "-" : date('d/M/Y', strtotime($data->validation_date)),
                "status"                => $status
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

    public function create($id)
    {
        $penerimaanBarangModel = new PenerimaanBarangModel();
        $penerimaanBarangDetailModel = new PenerimaanBarangDetailModel();
        $metaDataModel = new MetadataModel();
        $countryModel = new CountryModel();
        $kantorBeaCukaiModel = new KantorBeaCukaiModel();

        $id = decrypt($id);

        $lpb = $penerimaanBarangModel->getById($id);

        if ($lpb == null) {
            return redirect()->to('bea-cukai-bc-23');
        }


        $data = [
            // data helper passing in form select
            'kodeFasilitasTarif' => $metaDataModel->where('name', "Kode Fasilitas Tarif BC")->findAll(),
            'kodeJenisTarif' => $metaDataModel->where('name', "Kode Jenis Tarif BC")->findAll(),
            'kodeAsalBahanBaku' => $metaDataModel->where('name', "Kode Asal Bahan Baku BC")->findAll(),
            'kodeSatuanBarang' => $metaDataModel->where('name', "Kode Satuan BC")->orderBy('value', "ASC")->findAll(),
            'kodePerhitungan' => $metaDataModel->where('name', 'Kode Perhitungan BC')->orderBy('value', "ASC")->findAll(),
            'kodeNegaraAsal' => $countryModel->findAll(),
            'kodeJenisKemasan' => $metaDataModel->where('name', 'Jenis Kemasan')->orderBy('description', "ASC")->findAll(),
            'kodeKategoriBarang' => $metaDataModel->where('name', 'Kategori Barang BC')->orderBy('description', "ASC")->findAll(),
            'kodeDokumen' => $metaDataModel->where('name', "Dokumen")->orderBy('description', "ASC")->findAll(),
            'kodeAsuransi' => $metaDataModel->where('name', "Kode Asuransi BC")->findAll(),
            'kodeIncoterm' => $metaDataModel->where('name', "Kode Incoterm BC")->findAll(),
            'kodeKantor' => $kantorBeaCukaiModel->findAll(),
            'kodeTujunTpb' => $metaDataModel->where('name', "Jenis TPB")->findAll(),
            'kodeTutupPu' => $metaDataModel->where('name', "Kode Tutup Pu BC")->findAll(),
            'kodeValuta' => $metaDataModel->where('name', "Valuta")->findAll(),
            'kodeJenisAPI' => $metaDataModel->where('name', "Jenis API")->findAll(),
            'kodeKenaPajak' => $metaDataModel->where('name', "Kode Kena Pajak BC")->findAll(),
            'kodeJenisIdentas' => $metaDataModel->where('name', "Jenis Identitas")->findAll(),
            'kodeTipeKontainer' => $metaDataModel->where('name', "Kode Tipe Kontainer BC")->findAll(),
            'kodeUkuranKontainer' => $metaDataModel->where('name', "Kode Ukuran Kontainer BC")->findAll(),
            'kodeJenisKontainer' => $metaDataModel->where('name', "Jenis Kontainer")->findAll(),

            // data detail
            'lpb' => $penerimaanBarangModel->getById($id),
            'lpbDetail' => $penerimaanBarangDetailModel->getPenerimaanBarangDetailByPenerimaanBarangId($id, $lpb->tipe_bahan, $lpb->status_penerimaan)
        ];


        // dd($data['lpbDetail']);

        return view('BeaCukai/bc-23/form', $data);
    }

    public function getKodeSatuanBarang()
    {
        $metaDataModel = new MetadataModel();
        $page = $this->request->getVar('page');
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $data = $metaDataModel->getKodeSatuanBarang($limit, $offset);

        $response = [
            'results' => $data,
            'pagination' => [
                'more' => count($data) == $limit
            ]
        ];

        return $this->response->setJSON($response);
    }
}
