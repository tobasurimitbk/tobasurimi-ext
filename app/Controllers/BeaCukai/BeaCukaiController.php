<?php

namespace App\Controllers\BeaCukai;

use App\Controllers\BaseController;
use App\Models\BeaCukaiModel;
use App\Models\BeaCukaiBarangModel;
use App\Models\BeaCukaiDokumenModel;
use App\Models\BeaCukaiKemasanModel;
use App\Models\BeaCukaiKontainerModel;
use App\Models\CountryModel;
use App\Models\KantorBeaCukaiModel;
use App\Models\SupplierModel;
use App\Models\MetadataModel;
use App\Models\SalesOrderInvoiceModel;

use App\Models\RMPurchaseOrderModel;
use App\Models\RMImportPOModel;
use App\Models\AMPurchaseOrderModel;

class BeaCukaiController extends BaseController
{

    private $modelRMPurchaseOrder, $modelRMImportPO, $modelAMPurchaseOrder, $modelBeaCukai, $modelBeaCukaiBarang, $modelBeaCukaiDokumen, $modelBeaCukaiKemasan, $modelBeaCukaiKontainer, $modelKantorBeaCukai, $modelSupplier, $modelCountry, $modelMetadata, $modelSalesOrderInvoice, $this_company_id;

    public function __construct()
    {
        $this->modelRMPurchaseOrder = new RMPurchaseOrderModel();
        $this->modelRMImportPO = new RMImportPOModel();
        $this->modelAMPurchaseOrder = new AMPurchaseOrderModel();
        $this->modelBeaCukai = new BeaCukaiModel();
        $this->modelBeaCukaiBarang = new BeaCukaiBarangModel();
        $this->modelBeaCukaiDokumen = new BeaCukaiDokumenModel();
        $this->modelBeaCukaiKemasan = new BeaCukaiKemasanModel();
        $this->modelBeaCukaiKontainer = new BeaCukaiKontainerModel();
        $this->modelKantorBeaCukai = new KantorBeaCukaiModel();
        $this->modelSupplier = new SupplierModel();
        $this->modelCountry = new CountryModel();
        $this->modelMetadata = new MetadataModel();
        $this->modelSalesOrderInvoice = new SalesOrderInvoiceModel();
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function bcDelete()
    {
        try {
            $id = $this->request->getPost("id");

            if (empty($id)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $this->modelBeaCukai->delete($id);
            $data = [
                "status"    => true,
                "message"   => "Data Berhasil dihapus",
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function dropdownBeaCukaiPO()
    {
        $dropdownDokumen = [];

        if($this->request->getGet("jenis_dokumen") === "No PO")
        {
             //Get PO Bahan Baku BC
            $bbLokal = $this->modelRMPurchaseOrder->getNoPOBeaCukai($this->this_company_id);
            foreach($bbLokal as $x)
            {
                array_push($dropdownDokumen, [
                    "id" => $x["id"],
                    "po_no" => $x["po_no"],
                    "type" => "BAHAN BAKU LOKAL"
                ]);
            }

            $bbImport = $this->modelRMImportPO->getNoPOBeaCukai($this->this_company_id);

            foreach($bbImport as $x)
            {
                array_push($dropdownDokumen, [
                    "id" => $x["id"],
                    "po_no" => $x["po_no"],
                    "type" => "BAHAN BAKU IMPORT"
                ]);
            }
        }
        else
        {
            //Get PO Bahan Penolong BC
            $bpLokal = $this->modelAMPurchaseOrder->getNoPOBeaCukai('Lokal', $this->this_company_id);
            foreach($bpLokal as $x)
            {
                array_push($dropdownDokumen, [
                    "id" => $x["id"],
                    "po_no" => $x["po_no"],
                    "type" => "BAHAN PENOLONG LOKAL"
                ]);
            }

            $bpImport = $this->modelAMPurchaseOrder->getNoPOBeaCukai('Import', $this->this_company_id);
            foreach($bpImport as $x)
            {
                array_push($dropdownDokumen, [
                    "id" => $x["id"],
                    "po_no" => $x["po_no"],
                    "type" => "BAHAN PENOLONG IMPORT"
                ]);
            }
        }

        $data = [
            "data" => $dropdownDokumen
        ];

        echo json_encode($data);
        return;
    }

    public function bc23View()
    {

        return view('BeaCukai/bc-23/index');
    }

    public function bc23CreateFormView()
    {
        //Get Jenis Kontainer
        $jenisKontainer = $this->modelMetadata->get_by_name_bc('Jenis Kontainer');

        //Get Ukuran Kontainer
        $ukuranKontainer = $this->modelMetadata->get_by_name_bc('Ukuran Kontainer');

        //Get Tipe Kontainer
        $tipeKontainer = $this->modelMetadata->get_by_name_bc('Tipe Kontainer');

        //Get Jenis Dokumen
        $jenisDokumen = $this->modelMetadata->get_by_name_bc('Dokumen');

        //Get Jenis Kemasan
        $jenisKemasan = $this->modelMetadata->get_by_name_bc('Jenis Kemasan');

        //Get Jenis Identitas
        $jenisIdentitas = $this->modelMetadata->get_by_name_bc('Jenis Identitas');

        //Get Jenis API
        $jenisAPI = $this->modelMetadata->get_by_name_bc('Jenis API');

        //Get Valuta
        $valuta = $this->modelMetadata->get_by_name('Valuta');

        //Get Jenis TPB
        $jenisTPB = $this->modelMetadata->get_by_name_bc('Jenis TPB');

        //Get Pengangkutan
        $pengangkutan = $this->modelMetadata->get_by_name_bc('Pengangkutan');

        //Get Kategori Barang
        $kategoriBarang = $this->modelMetadata->get_by_name_bc('Kategori Barang BC');

        $data = [
            'kategoriBarang' => $kategoriBarang,
            'dropdownDokumen' => [],
            'jenisKontainer' => $jenisKontainer,
            'ukuranKontainer' => $ukuranKontainer,
            'tipeKontainer' => $tipeKontainer,
            'jenisKemasan' => $jenisKemasan,
            'jenisDokumen' => $jenisDokumen,
            'jenisIdentitas' => $jenisIdentitas,
            'jenisAPI' => $jenisAPI,
            'valuta' => $valuta,
            'dokumen' => $this->modelSalesOrderInvoice->asObject()->findAll(),
            'jenisTPB' => $jenisTPB,
            'pengangkutan' => $pengangkutan,
            'kantorBeaCukai' => $this->modelKantorBeaCukai->asObject()->findAll(),
            'supplier' => $this->modelSupplier->asObject()->findAll(),
            'country' => $this->modelCountry->asObject()->findAll()
        ];
        return view('BeaCukai/bc-23/create', $data);
    }

    public function bc23GetByIdFormView($id)
    {
        //Get Jenis Kontainer
        $jenisKontainer = $this->modelMetadata->get_by_name_bc('Jenis Kontainer');

        //Get Ukuran Kontainer
        $ukuranKontainer = $this->modelMetadata->get_by_name_bc('Ukuran Kontainer');

        //Get Tipe Kontainer
        $tipeKontainer = $this->modelMetadata->get_by_name_bc('Tipe Kontainer');

        //Get Jenis Dokumen
        $jenisDokumen = $this->modelMetadata->get_by_name_bc('Dokumen');

        //Get Jenis Kemasan
        $jenisKemasan = $this->modelMetadata->get_by_name_bc('Jenis Kemasan');

        //Get Jenis Identitas
        $jenisIdentitas = $this->modelMetadata->get_by_name_bc('Jenis Identitas');

        //Get Jenis API
        $jenisAPI = $this->modelMetadata->get_by_name_bc('Jenis API');

        //Get Valuta
        $valuta = $this->modelMetadata->get_by_name('Valuta');

        //Get Jenis TPB
        $jenisTPB = $this->modelMetadata->get_by_name_bc('Jenis TPB');

        //Get Pengangkutan
        $pengangkutan = $this->modelMetadata->get_by_name_bc('Pengangkutan');

        //Get Kategori Barang
        $kategoriBarang = $this->modelMetadata->get_by_name_bc('Kategori Barang BC');

        $data = [
            'kategoriBarang' => $kategoriBarang,
            'jenisKontainer' => $jenisKontainer,
            'ukuranKontainer' => $ukuranKontainer,
            'tipeKontainer' => $tipeKontainer,
            'jenisDokumen' => $jenisDokumen,
            'jenisKemasan' => $jenisKemasan,
            'jenisIdentitas' => $jenisIdentitas,
            'jenisAPI' => $jenisAPI,
            'valuta' => $valuta,
            'dokumen' => $this->modelSalesOrderInvoice->asObject()->findAll(),
            'jenisTPB' => $jenisTPB,
            'pengangkutan' => $pengangkutan,
            'kantorBeaCukai' => $this->modelKantorBeaCukai->asObject()->findAll(),
            'supplier' => $this->modelSupplier->asObject()->findAll(),
            'country' => $this->modelCountry->asObject()->findAll()
        ];

        if (!empty($id)) {
            $dataBC = $this->modelBeaCukai->getById($id);
            $data["dataBC"] = $dataBC;

            $dataBCBarangDetail = $this->modelBeaCukaiBarang->getByBeaCukaiId($id);

            if($dataBCBarangDetail)
            {
                $data["dataBCBarangDetail"] = $dataBCBarangDetail;
            }

            $dataBCDokumenDetail = $this->modelBeaCukaiDokumen->getByBeaCukaiId($id);

            if($dataBCDokumenDetail)
            {
                $data["dataBCDokumenDetail"] = $dataBCDokumenDetail;
            }

            $dataBCKemasanDetail = $this->modelBeaCukaiKemasan->getByBeaCukaiId($id);

            if($dataBCKemasanDetail)
            {
                $data["dataBCKemasanDetail"] = $dataBCKemasanDetail;
            }

            $dataBCKontainerDetail = $this->modelBeaCukaiKontainer->getByBeaCukaiId($id);

            if($dataBCKontainerDetail)
            {
                $data["dataBCKontainerDetail"] = $dataBCKontainerDetail;
            }

            // var_dump($dataBCBarangDetail);
            // die;

            $dropdownDokumen = [];
            if($dataBC->jenis_dokumen === "No PO")
            {
                //Get PO Bahan Baku BC
                $bbLokal = $this->modelRMPurchaseOrder->getNoPOBeaCukai($this->this_company_id);
                foreach($bbLokal as $x)
                {
                    array_push($dropdownDokumen, [
                        "id" => $x["id"],
                        "po_no" => $x["po_no"],
                        "type" => "BAHAN BAKU LOKAL"
                    ]);
                }

                $bbImport = $this->modelRMImportPO->getNoPOBeaCukai($this->this_company_id);

                foreach($bbImport as $x)
                {
                    array_push($dropdownDokumen, [
                        "id" => $x["id"],
                        "po_no" => $x["po_no"],
                        "type" => "BAHAN BAKU IMPORT"
                    ]);
                }
            }

            if($dataBC->jenis_dokumen === "No SJ")
            {
                //Get PO Bahan Penolong BC
                $bpLokal = $this->modelAMPurchaseOrder->getNoPOBeaCukai('Lokal', $this->this_company_id);
                foreach($bpLokal as $x)
                {
                    array_push($dropdownDokumen, [
                        "id" => $x["id"],
                        "po_no" => $x["po_no"],
                        "type" => "BAHAN PENOLONG LOKAL"
                    ]);
                }

                $bpImport = $this->modelAMPurchaseOrder->getNoPOBeaCukai('Import', $this->this_company_id);
                foreach($bpImport as $x)
                {
                    array_push($dropdownDokumen, [
                        "id" => $x["id"],
                        "po_no" => $x["po_no"],
                        "type" => "BAHAN PENOLONG IMPORT"
                    ]);
                }
            }

            $data["dropdownDokumen"] = $dropdownDokumen;

            if($dataBC->type !== "BC 2.3")
            {
                return view('BeaCukai/bc-23/index');
            }
        }
        
        return view('BeaCukai/bc-23/create', $data);
    }

    public function bc23SaveForm()
    {
        try {
            $rules = [
                "jenisDokumenBC" => [
                    "rules" => "required"
                ],
                "noDokumenBC" => [
                    "rules" => "required"
                ],
                "kppbcBongkar" => [
                    "rules" => "required"
                ],
                "kppbcPengawas" => [
                    "rules" => "required"
                ],
                "kodeTujuanTpb" => [
                    "rules" => "required"
                ],
                "namaSupplier" => [
                    "rules" => "required"
                ],
                "namaImportir" => [
                    "rules" => "required"
                ],
                "noIzinTPBImportir" => [
                    "rules" => "required"
                ],
                "APIImportir" => [
                    "rules" => "required"
                ],
                "alamatImportir" => [
                    "rules" => "required"
                ],
                "caraPengangkutan" => [
                    "rules" => "required"
                ],
                "namaSaranaPengangkut" => [
                    "rules" => "required"
                ],
                "noVoyFlight" => [
                    "rules" => "required"
                ],
                "pengangkutanNegara" => [
                    "rules" => "required"
                ],
                "pelabuhanMuat" => [
                    "rules" => "required"
                ],
                "pelabuhanTransit" => [
                    "rules" => "required"
                ],
                "pelabuhanBongkar" => [
                    "rules" => "required"
                ],
                "noInvoice" => [
                    "rules" => "required"
                ],
                "noBc" => [
                    "rules" => "required"
                ],
                "tanggalBc" => [
                    "rules" => "required"
                ],
                "kodePos" => [
                    "rules" => "required"
                ],
                "tempatPenimbunan" => [
                    "rules" => "required"
                ],
                "valuta" => [
                    "rules" => "required"
                ],
                "npdpbm" => [
                    "rules" => "required"
                ],
                "fob" => [
                    "rules" => "required"
                ],
                "freight" => [
                    "rules" => "required"
                ],
                "tipeAsuransi" => [
                    "rules" => "required"
                ],
                "nilaiCif" => [
                    "rules" => "required"
                ],
                "nilaiCifRupiah" => [
                    "rules" => "required"
                ],
                "bruto" => [
                    "rules" => "required"
                ],
                "netto" => [
                    "rules" => "required"
                ],
                "jumlahBarang" => [
                    "rules" => "required"
                ],
                "tempat" => [
                    "rules" => "required"
                ],
                "tanggal" => [
                    "rules" => "required"
                ],
                "pemberitahu" => [
                    "rules" => "required"
                ],
                "jabatan" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $payload = [
                "company_id"            => $this->this_company_id,
                "status"                => "-",
                "status_perbaikan"      => "-",
                "aju_no"                => "-",
                "registration_no"       => "-",
                "registration_date"     => "-",
                "type"                  => "BC 2.3",
                "status_posting"        => "Belum Posting",

                "jenis_dokumen"         => $this->request->getPost("jenisDokumenBC"),
                "no_dokumen"            => $this->request->getPost("noDokumenBC"),
                "dokumen_type"          => $this->request->getPost("dokumenType"),

                "kppbc_bongkar"         => $this->request->getPost("kppbcBongkar"),
                "kppbc_pengawas"        => $this->request->getPost("kppbcPengawas"),
                "tujuan_tpb"            => $this->request->getPost("kodeTujuanTpb"),

                "supplier_id"           => $this->request->getPost("namaSupplier"),

                "importir_jenis_identitas"   => $this->request->getPost("jenisIdentitasImportir"),
                "importir_identitas"   => $this->request->getPost("identitasImportir"),
                "importir_jenis_api"   => $this->request->getPost("jenisAPIImportir"),
                "importir_name"         => $this->request->getPost("namaImportir"),
                "tpb_no"                => $this->request->getPost("noIzinTPBImportir"),
                "importir_api"          => $this->request->getPost("APIImportir"),
                "importir_address"      => $this->request->getPost("alamatImportir"),

                "pemilik_barang"        => $this->request->getPost("switchPemilikBarang") ? 1 : 0,
                "pemilik_barang_jenis_identitas"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("jenisIdentitasImportir") : $this->request->getPost("jenisIdentitasPemilikBarang"),
                "pemilik_barang_identitas"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("identitasImportir") : $this->request->getPost("identitasPemilikBarang"),
                "pemilik_barang_name"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("namaImportir") : $this->request->getPost("namaPemilikBarang"),
                "pemilik_barang_address"=> $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("alamatImportir") : $this->request->getPost("alamatPemilikBarang"),
                "pemilik_barang_api"    => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("APIImportir") : $this->request->getPost("APIPemilikBarang"),

                "ppjk_npwp"             => $this->request->getPost("PpjkNpwp"),
                "ppjk_name"             => $this->request->getPost("PpjkNama"),
                "ppjk_date"             => $this->request->getPost("PpjkTanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("PpjkTanggal")))) : "",
                "ppjk_no"               => $this->request->getPost("PpjkNo"),
                "ppjk_address"          => $this->request->getPost("PpjkAlamat"),

                "pengangkutan"          => $this->request->getPost("caraPengangkutan"),
                "pengangkutan_sarana"   => $this->request->getPost("namaSaranaPengangkut"),
                "voy_no"                => $this->request->getPost("noVoyFlight"),
                "pengangkutan_country"  => $this->request->getPost("pengangkutanNegara"),
                "kode_pelabuhan_muat"   => $this->request->getPost("pelabuhanMuat"),
                "kode_pelabuhan_transit"=> $this->request->getPost("pelabuhanTransit"),
                "kode_pelabuhan_bongkar"=> $this->request->getPost("pelabuhanBongkar"),

                "invoice_id"            => $this->request->getPost("noInvoice"),
                "invoice_date"          => $this->request->getPost("tanggalInvoice") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalInvoice")))) : "",
                "fasilitas_import_no"   => $this->request->getPost("noFasilitasImport"),
                "fasilitas_import_date" => $this->request->getPost("tanggalFasilitasImport") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalFasilitasImport")))) : "",
                "fasilitas_import_code" => $this->request->getPost("kodeFasilitasImport"),
                "lc_no"                 => $this->request->getPost("noLc"),
                "lc_date"               => $this->request->getPost("tanggalLc") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalLc")))) : "",
                "bl_no"                 => $this->request->getPost("noBl"),
                "bl_date"               => $this->request->getPost("tanggalBl") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalBl")))) : "",
                "bc_11_no"              => $this->request->getPost("noBc"),
                "bc_11_date"            => $this->request->getPost("tanggalBc") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalBc")))) : "",
                "bc_11_zip"             => $this->request->getPost("kodePos"),

                "penimbunan"            => $this->request->getPost("tempatPenimbunan"),

                "valuta"                => $this->request->getPost("valuta"),
                "ndpbm"                 => $this->request->getPost("npdpbm"),
                "fob"                   => $this->request->getPost("fob"),
                "freight"               => $this->request->getPost("freight"),
                "asuransi_type"         => $this->request->getPost("tipeAsuransi"),
                "cif_value"             => $this->request->getPost("nilaiCif"),
                "cif_price"             => $this->request->getPost("nilaiCifRupiah"),
                
                "bruto"                 => $this->request->getPost("bruto"),
                "netto"                 => $this->request->getPost("netto"),
                "item_count"            => $this->request->getPost("jumlahBarang"),

                "tempat"                => $this->request->getPost("tempat"),
                "tanggal"               => $this->request->getPost("tanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggal")))) : "",
                "pemberitahu"           => $this->request->getPost("pemberitahu"),
                "jabatan"               => $this->request->getPost("jabatan")
            ];
            
            $insert =  $this->modelBeaCukai->insert($payload);
            
            $data_barang = json_decode($this->request->getPost("data_barang"));
            $data_kemasan = json_decode($this->request->getPost("data_kemasan"));
            $data_kontainer = json_decode($this->request->getPost("data_kontainer"));
            $data_dokumen = json_decode($this->request->getPost("data_dokumen"));

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            foreach($data_barang as $data)
            {
                $detailPayload = [];

                $detailPayload = [
                    'bea_cukai_id' => $insert,
                    'barang_id' => $data->barang_id,
                    'kategori_id' => $data->kategori_id,
                    'merk' => $data->merk,
                    'tipe' => $data->tipe
                ];

                $responseDetail = $this->modelBeaCukaiBarang->insert($detailPayload);

                if(!$responseDetail) {
                    $message =  'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }
            }

            foreach($data_kemasan as $data)
            {
                $detailPayload = [];

                $detailPayload = [
                    'bea_cukai_id' => $insert,
                    'jumlah' => $data->jumlah,
                    'kemasan_id' => $data->kemasan_id,
                    'uraian' => $data->uraian,
                    'merk' => $data->merk
                ];

                $responseDetail = $this->modelBeaCukaiKemasan->insert($detailPayload);

                if(!$responseDetail) {
                    $message =  'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }
            }

            foreach($data_kontainer as $data)
            {
                $detailPayload = [];

                $detailPayload = [
                    'bea_cukai_id' => $insert,
                    'jenis_id' => $data->jenis_id,
                    'ukuran_id' => $data->ukuran_id,
                    'tipe_id' => $data->tipe_id,
                    'keterangan' => $data->keterangan
                ];

                $responseDetail = $this->modelBeaCukaiKontainer->insert($detailPayload);

                if(!$responseDetail) {
                    $message =  'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }
            }

            foreach($data_dokumen as $data)
            {
                $detailPayload = [];

                $detailPayload = [
                    'bea_cukai_id' => $insert,
                    'dokumen_id' => $data->dokumen_id,
                    'no_dokumen' => $data->no_dokumen,
                    'date' => $data->date ? date("Y/m/d", strtotime(str_replace("/", "-", $data->date))) : "",
                    'keterangan' => $data->keterangan
                ];

                $responseDetail = $this->modelBeaCukaiDokumen->insert($detailPayload);

                if(!$responseDetail) {
                    $message =  'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message,
                        "payload"   => $payload,
                        'token' => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }
            }

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => json_encode($payload),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function bc23UpdateForm()
    {
        try {
            $rules = [
                "jenisDokumenBC" => [
                    "rules" => "required"
                ],
                "noDokumenBC" => [
                    "rules" => "required"
                ],
                "kppbcBongkar" => [
                    "rules" => "required"
                ],
                "kppbcPengawas" => [
                    "rules" => "required"
                ],
                "kodeTujuanTpb" => [
                    "rules" => "required"
                ],
                "namaSupplier" => [
                    "rules" => "required"
                ],
                "namaImportir" => [
                    "rules" => "required"
                ],
                "noIzinTPBImportir" => [
                    "rules" => "required"
                ],
                "APIImportir" => [
                    "rules" => "required"
                ],
                "alamatImportir" => [
                    "rules" => "required"
                ],
                "caraPengangkutan" => [
                    "rules" => "required"
                ],
                "namaSaranaPengangkut" => [
                    "rules" => "required"
                ],
                "noVoyFlight" => [
                    "rules" => "required"
                ],
                "pengangkutanNegara" => [
                    "rules" => "required"
                ],
                "pelabuhanMuat" => [
                    "rules" => "required"
                ],
                "pelabuhanTransit" => [
                    "rules" => "required"
                ],
                "pelabuhanBongkar" => [
                    "rules" => "required"
                ],
                "noInvoice" => [
                    "rules" => "required"
                ],
                "noBc" => [
                    "rules" => "required"
                ],
                "tanggalBc" => [
                    "rules" => "required"
                ],
                "kodePos" => [
                    "rules" => "required"
                ],
                "tempatPenimbunan" => [
                    "rules" => "required"
                ],
                "valuta" => [
                    "rules" => "required"
                ],
                "npdpbm" => [
                    "rules" => "required"
                ],
                "fob" => [
                    "rules" => "required"
                ],
                "freight" => [
                    "rules" => "required"
                ],
                "tipeAsuransi" => [
                    "rules" => "required"
                ],
                "nilaiCif" => [
                    "rules" => "required"
                ],
                "nilaiCifRupiah" => [
                    "rules" => "required"
                ],
                "bruto" => [
                    "rules" => "required"
                ],
                "netto" => [
                    "rules" => "required"
                ],
                "jumlahBarang" => [
                    "rules" => "required"
                ],
                "tempat" => [
                    "rules" => "required"
                ],
                "tanggal" => [
                    "rules" => "required"
                ],
                "pemberitahu" => [
                    "rules" => "required"
                ],
                "jabatan" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $id = $this->request->getPost("id");

            $payload = [
                "jenis_dokumen"         => $this->request->getPost("jenisDokumenBC"),
                "no_dokumen"            => $this->request->getPost("noDokumenBC"),
                "dokumen_type"          => $this->request->getPost("dokumenType"),

                "kppbc_bongkar"         => $this->request->getPost("kppbcBongkar"),
                "kppbc_pengawas"        => $this->request->getPost("kppbcPengawas"),
                "tujuan_tpb"            => $this->request->getPost("kodeTujuanTpb"),

                "supplier_id"           => $this->request->getPost("namaSupplier"),

                "importir_jenis_identitas"   => $this->request->getPost("jenisIdentitasImportir"),
                "importir_identitas"   => $this->request->getPost("identitasImportir"),
                "importir_jenis_api"   => $this->request->getPost("jenisAPIImportir"),
                "importir_name"         => $this->request->getPost("namaImportir"),
                "tpb_no"                => $this->request->getPost("noIzinTPBImportir"),
                "importir_api"          => $this->request->getPost("APIImportir"),
                "importir_address"      => $this->request->getPost("alamatImportir"),

                "pemilik_barang"        => $this->request->getPost("switchPemilikBarang") ? 1 : 0,
                "pemilik_barang_jenis_identitas"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("jenisIdentitasImportir") : $this->request->getPost("jenisIdentitasPemilikBarang"),
                "pemilik_barang_identitas"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("identitasImportir") : $this->request->getPost("identitasPemilikBarang"),
                "pemilik_barang_jenis_api"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("jenisAPIImportir") : $this->request->getPost("jenisAPIPemilikBarang"),
                "pemilik_barang_name"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("namaImportir") : $this->request->getPost("namaPemilikBarang"),
                "pemilik_barang_address"=> $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("alamatImportir") : $this->request->getPost("alamatPemilikBarang"),
                "pemilik_barang_api"    => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("APIImportir") : $this->request->getPost("APIPemilikBarang"),

                "ppjk_npwp"             => $this->request->getPost("PpjkNpwp"),
                "ppjk_name"             => $this->request->getPost("PpjkNama"),
                "ppjk_date"             => $this->request->getPost("PpjkTanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("PpjkTanggal")))) : "",
                "ppjk_no"               => $this->request->getPost("PpjkNo"),
                "ppjk_address"          => $this->request->getPost("PpjkAlamat"),

                "pengangkutan"          => $this->request->getPost("caraPengangkutan"),
                "pengangkutan_sarana"   => $this->request->getPost("namaSaranaPengangkut"),
                "voy_no"                => $this->request->getPost("noVoyFlight"),
                "pengangkutan_country"  => $this->request->getPost("pengangkutanNegara"),
                "kode_pelabuhan_muat"   => $this->request->getPost("pelabuhanMuat"),
                "kode_pelabuhan_transit"=> $this->request->getPost("pelabuhanTransit"),
                "kode_pelabuhan_bongkar"=> $this->request->getPost("pelabuhanBongkar"),

                "invoice_id"            => $this->request->getPost("noInvoice"),
                "invoice_date"          => $this->request->getPost("tanggalInvoice") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalInvoice")))) : "",
                "fasilitas_import_no"   => $this->request->getPost("noFasilitasImport"),
                "fasilitas_import_date" => $this->request->getPost("tanggalFasilitasImport") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalFasilitasImport")))) : "",
                "fasilitas_import_code" => $this->request->getPost("kodeFasilitasImport"),
                "lc_no"                 => $this->request->getPost("noLc"),
                "lc_date"               => $this->request->getPost("tanggalLc") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalLc")))) : "",
                "bl_no"                 => $this->request->getPost("noBl"),
                "bl_date"               => $this->request->getPost("tanggalBl") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalBl")))) : "",
                "bc_11_no"              => $this->request->getPost("noBc"),
                "bc_11_date"            => $this->request->getPost("tanggalBc") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalBc")))) : "",
                "bc_11_zip"             => $this->request->getPost("kodePos"),

                "penimbunan"            => $this->request->getPost("tempatPenimbunan"),

                "valuta"                => $this->request->getPost("valuta"),
                "ndpbm"                 => $this->request->getPost("npdpbm"),
                "fob"                   => $this->request->getPost("fob"),
                "freight"               => $this->request->getPost("freight"),
                "asuransi_type"         => $this->request->getPost("tipeAsuransi"),
                "cif_value"             => $this->request->getPost("nilaiCif"),
                "cif_price"             => $this->request->getPost("nilaiCifRupiah"),
                
                "bruto"                 => $this->request->getPost("bruto"),
                "netto"                 => $this->request->getPost("netto"),
                "item_count"            => $this->request->getPost("jumlahBarang"),

                "tempat"                => $this->request->getPost("tempat"),
                "tanggal"               => $this->request->getPost("tanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggal")))) : "",
                "pemberitahu"           => $this->request->getPost("pemberitahu"),
                "jabatan"               => $this->request->getPost("jabatan")
            ];
            
            $insert =  $this->modelBeaCukai->where(['id' => $id])->set($payload)->update();

            $data_barang = json_decode($this->request->getPost("data_barang"));
            $data_kemasan = json_decode($this->request->getPost("data_kemasan"));
            $data_kontainer = json_decode($this->request->getPost("data_kontainer"));
            $data_dokumen = json_decode($this->request->getPost("data_dokumen"));

            if ($insert) {
                foreach($data_barang as $data)
                {
                    $detailPayload = [];

                    $detailPayload = [
                        'bea_cukai_id' => $id,
                        'barang_id' => $data->barang_id,
                        'kategori_id' => $data->kategori_id,
                        'merk' => $data->merk,
                        'tipe' => $data->tipe
                    ];

                    // kalau hapus dan ada id
                    if($data->isDeleted === true)
                    {
                        if($data->id)
                        {
                            $responseDetail = $this->modelBeaCukaiBarang->delete($data->id);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Dihapus';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                    else
                    {
                         // kalau update
                        if($data->id)
                        {
                            $conditionDetail = [
                                'id' => $data->id
                            ];

                            $responseDetail = $this->modelBeaCukaiBarang->where($conditionDetail)->set($detailPayload)->update();

                            if(!$responseDetail) {
                                $message =  'Data Gagal Disimpan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }

                        // kalau create
                        else
                        {
                            $responseDetail = $this->modelBeaCukaiBarang->insert($detailPayload);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Diubah';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }

                foreach($data_kemasan as $data)
                {
                    $detailPayload = [];

                    $detailPayload = [
                        'bea_cukai_id' => $id,
                        'jumlah' => $data->jumlah,
                        'kemasan_id' => $data->kemasan_id,
                        'uraian' => $data->uraian,
                        'merk' => $data->merk
                    ];

                    // kalau hapus dan ada id
                    if($data->isDeleted === true)
                    {
                        if($data->id)
                        {
                            $responseDetail = $this->modelBeaCukaiKemasan->delete($data->id);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Dihapus';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                    else
                    {
                         // kalau update
                        if($data->id)
                        {
                            $conditionDetail = [
                                'id' => $data->id
                            ];

                            $responseDetail = $this->modelBeaCukaiKemasan->where($conditionDetail)->set($detailPayload)->update();

                            if(!$responseDetail) {
                                $message =  'Data Gagal Disimpan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }

                        // kalau create
                        else
                        {
                            $responseDetail = $this->modelBeaCukaiKemasan->insert($detailPayload);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Diubah';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }

                foreach($data_kontainer as $data)
                {
                    $detailPayload = [];

                    $detailPayload = [
                        'bea_cukai_id' => $id,
                        'jenis_id' => $data->jenis_id,
                        'ukuran_id' => $data->ukuran_id,
                        'tipe_id' => $data->tipe_id,
                        'keterangan' => $data->keterangan
                    ];

                    // kalau hapus dan ada id
                    if($data->isDeleted === true)
                    {
                        if($data->id)
                        {
                            $responseDetail = $this->modelBeaCukaiKontainer->delete($data->id);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Dihapus';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                    else
                    {
                         // kalau update
                        if($data->id)
                        {
                            $conditionDetail = [
                                'id' => $data->id
                            ];

                            $responseDetail = $this->modelBeaCukaiKontainer->where($conditionDetail)->set($detailPayload)->update();

                            if(!$responseDetail) {
                                $message =  'Data Gagal Disimpan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }

                        // kalau create
                        else
                        {
                            $responseDetail = $this->modelBeaCukaiKontainer->insert($detailPayload);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Diubah';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }

                foreach($data_dokumen as $data)
                {
                    $detailPayload = [];

                    $detailPayload = [
                        'bea_cukai_id' => $id,
                        'dokumen_id' => $data->dokumen_id,
                        'no_dokumen' => $data->no_dokumen,
                        'date' => $data->date ? date("Y/m/d", strtotime(str_replace("/", "-", $data->date))) : "",
                        'keterangan' => $data->keterangan
                    ];

                    // kalau hapus dan ada id
                    if($data->isDeleted === true)
                    {
                        if($data->id)
                        {
                            $responseDetail = $this->modelBeaCukaiDokumen->delete($data->id);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Dihapus';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                    else
                    {
                         // kalau update
                        if($data->id)
                        {
                            $conditionDetail = [
                                'id' => $data->id
                            ];

                            $responseDetail = $this->modelBeaCukaiDokumen->where($conditionDetail)->set($detailPayload)->update();

                            if(!$responseDetail) {
                                $message =  'Data Gagal Disimpan';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }

                        // kalau create
                        else
                        {
                            $responseDetail = $this->modelBeaCukaiDokumen->insert($detailPayload);

                            if(!$responseDetail) {
                                $message =  'Data Gagal Diubah';
                                $data = [
                                    "status"            => false,
                                    "message"    => $message,
                                    "payload"   => $payload,
                                    'token' => csrf_hash()
                                ];
                                echo json_encode($data);
                                return;
                            }
                        }
                    }
                }

                $data = [
                    "status"    => true,
                    "message"   => "Data Berhasil disimpan",
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
            else
            {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function bc23All()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "type"          => "BC 2.3"
        ];

        $condition = [
            "bea_cukai.company_id"  => $this->this_company_id,
            "type"                  => "BC 2.3"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $beaCukaiData = $this->modelBeaCukai->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "aju_no"                => $data->aju_no,
                "registration_no"       => $data->registration_no,
                "registration_date"     => $data->registration_date,
                "tujuan_tpb_name"       => $data->tujuan_tpb_name,
                "status_posting"        => $data->status_posting
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function bc25View()
    {
        return view('BeaCukai/bc-25/index');
    }

    public function bc25CreateFormView()
    {
        //Get Jenis Dokumen
        $jenisDokumen = $this->modelMetadata->get_by_name_bc('Dokumen');

        //Get Jenis Identitas
        $jenisIdentitas = $this->modelMetadata->get_by_name_bc('Jenis Identitas');

        //Get Jenis API
        $jenisAPI = $this->modelMetadata->get_by_name_bc('Jenis API');

        //Get Valuta
        $valuta = $this->modelMetadata->get_by_name_bc('Valuta');

        //Get Jenis TPB
        $jenisTPB = $this->modelMetadata->get_by_name_bc('Jenis TPB');

        //Get Pengangkutan
        $pengangkutan = $this->modelMetadata->get_by_name_bc('Pengangkutan');

        //Get Referensi Lokasi Bayar
        $referensiLokasiBayar = $this->modelMetadata->get_by_name_bc('Lokasi Bayar');

        //Get Wajib Bayar
        $wajibBayar = $this->modelMetadata->get_by_name_bc('Entitas');

        $data = [
            'jenisDokumen' => $jenisDokumen,
            'jenisIdentitas' => $jenisIdentitas,
            'jenisAPI' => $jenisAPI,
            'wajibBayar' => $wajibBayar,
            'referensiLokasiBayar' => $referensiLokasiBayar,
            'valuta' => $valuta,
            'dokumen' => $this->modelSalesOrderInvoice->asObject()->findAll(),
            'jenisTPB' => $jenisTPB,
            'pengangkutan' => $pengangkutan,
            'kantorBeaCukai' => $this->modelKantorBeaCukai->asObject()->findAll()
        ];
        return view('BeaCukai/bc-25/create', $data);
    }

    public function bc25GetByIdFormView($id)
    {
        //Get Jenis Dokumen
        $jenisDokumen = $this->modelMetadata->get_by_name_bc('Dokumen');

        //Get Jenis Identitas
        $jenisIdentitas = $this->modelMetadata->get_by_name_bc('Jenis Identitas');

        //Get Jenis API
        $jenisAPI = $this->modelMetadata->get_by_name_bc('Jenis API');

        //Get Valuta
        $valuta = $this->modelMetadata->get_by_name_bc('Valuta');

        //Get Jenis TPB
        $jenisTPB = $this->modelMetadata->get_by_name_bc('Jenis TPB');

        //Get Pengangkutan
        $pengangkutan = $this->modelMetadata->get_by_name_bc('Pengangkutan');

        //Get Referensi Lokasi Bayar
        $referensiLokasiBayar = $this->modelMetadata->get_by_name_bc('Lokasi Bayar');

        //Get Wajib Bayar
        $wajibBayar = $this->modelMetadata->get_by_name_bc('Entitas');

        $data = [
            'jenisDokumen' => $jenisDokumen,
            'jenisIdentitas' => $jenisIdentitas,
            'jenisAPI' => $jenisAPI,
            'wajibBayar' => $wajibBayar,
            'referensiLokasiBayar' => $referensiLokasiBayar,
            'valuta' => $valuta,
            'dokumen' => $this->modelSalesOrderInvoice->asObject()->findAll(),
            'jenisTPB' => $jenisTPB,
            'pengangkutan' => $pengangkutan,
            'kantorBeaCukai' => $this->modelKantorBeaCukai->asObject()->findAll()
        ];

        if (!empty($id)) {
            $dataBC = $this->modelBeaCukai->getById($id);
            $data["dataBC"] = $dataBC;

            if($dataBC->type !== "BC 2.5")
            {
                return view('BeaCukai/bc-25/index');
            }
        }
        
        return view('BeaCukai/bc-25/create', $data);
    }

    public function bc25SaveForm()
    {
        try {
            $rules = [
                "kantorPabean" => [
                    "rules" => "required"
                ],
                "kodeTujuanTpb" => [
                    "rules" => "required"
                ],
                "namaImportir" => [
                    "rules" => "required"
                ],
                "noIzinTPBImportir" => [
                    "rules" => "required"
                ],
                "APIImportir" => [
                    "rules" => "required"
                ],
                "alamatImportir" => [
                    "rules" => "required"
                ],
                "npwpPenerimaBarang" => [
                    "rules" => "required"
                ],
                "namaPenerimaBarang" => [
                    "rules" => "required"
                ],
                "APIPenerimaBarang" => [
                    "rules" => "required"
                ],
                "niperPenerimaBarang" => [
                    "rules" => "required"
                ],
                "alamatPenerimaBarang" => [
                    "rules" => "required"
                ],
                "noInvoice" => [
                    "rules" => "required"
                ],
                "noPackingList" => [
                    "rules" => "required"
                ],
                "tanggalPackingList" => [
                    "rules" => "required"
                ],
                "noKontrak" => [
                    "rules" => "required"
                ],
                "tanggalKontrak" => [
                    "rules" => "required"
                ],
                "valuta" => [
                    "rules" => "required"
                ],
                "npdpbm" => [
                    "rules" => "required"
                ],
                "nilaiCif" => [
                    "rules" => "required"
                ],
                "hargaPenyerahan" => [
                    "rules" => "required"
                ],
                "caraPengangkutan" => [
                    "rules" => "required"
                ],
                "bruto" => [
                    "rules" => "required"
                ],
                "netto" => [
                    "rules" => "required"
                ],
                "jumlahBarang" => [
                    "rules" => "required"
                ],
                "pembayaran" => [
                    "rules" => "required"
                ],
                "wajibBayar" => [
                    "rules" => "required"
                ],
                "tempat" => [
                    "rules" => "required"
                ],
                "tanggal" => [
                    "rules" => "required"
                ],
                "pemberitahu" => [
                    "rules" => "required"
                ],
                "jabatan" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $payload = [
                "company_id"            => $this->this_company_id,
                "status"                => "-",
                "status_perbaikan"      => "-",
                "aju_no"                => "-",
                "registration_no"       => "-",
                "registration_date"     => "-",
                "type"                  => "BC 2.5",
                "status_posting"        => "Belum Posting",

                "jenis_dokumen"         => $this->request->getPost("jenisDokumenBC"),
                "no_dokumen"            => $this->request->getPost("noDokumen"),

                "kantor_pabean"         => $this->request->getPost("kantorPabean"),
                "tujuan_tpb"            => $this->request->getPost("kodeTujuanTpb"),

                "importir_jenis_identitas"   => $this->request->getPost("jenisIdentitasImportir"),
                "importir_identitas"   => $this->request->getPost("identitasImportir"),
                "importir_jenis_api"   => $this->request->getPost("jenisAPIImportir"),
                "importir_name"         => $this->request->getPost("namaImportir"),
                "tpb_no"                => $this->request->getPost("noIzinTPBImportir"),
                "importir_api"          => $this->request->getPost("APIImportir"),
                "importir_address"      => $this->request->getPost("alamatImportir"),

                "pemilik_barang"        => $this->request->getPost("switchPemilikBarang") ? 1 : 0,
                "pemilik_barang_jenis_identitas"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("jenisIdentitasImportir") : $this->request->getPost("jenisIdentitasPemilikBarang"),
                "pemilik_barang_identitas"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("identitasImportir") : $this->request->getPost("identitasPemilikBarang"),
                "pemilik_barang_name"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("namaImportir") : $this->request->getPost("namaPemilikBarang"),
                "pemilik_barang_address"=> $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("alamatImportir") : $this->request->getPost("alamatPemilikBarang"),
                "pemilik_barang_api"    => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("APIImportir") : $this->request->getPost("APIPemilikBarang"),

                "penerima_barang_npwp"  => $this->request->getPost("npwpPenerimaBarang"),
                "penerima_barang_name"  => $this->request->getPost("namaPenerimaBarang"),
                "penerima_barang_api"   => $this->request->getPost("APIPenerimaBarang"),
                "penerima_barang_niper" => $this->request->getPost("niperPenerimaBarang"),
                "penerima_barang_address" => $this->request->getPost("alamatPenerimaBarang"),

                "invoice_id"            => $this->request->getPost("noInvoice"),
                "invoice_date"          => $this->request->getPost("tanggalInvoice") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalInvoice")))) : "",
                "no_packing_list"       => $this->request->getPost("noPackingList"),
                "tanggal_packing_list"  => $this->request->getPost("tanggalPackingList") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalPackingList")))) : "",
                "no_kontrak"            => $this->request->getPost("noKontrak"),
                "tanggal_kontrak"       => $this->request->getPost("tanggalKontrak") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalKontrak")))) : "",  
                "fasilitas_import_no"   => $this->request->getPost("noFasilitasImport"),
                "fasilitas_import_date" => $this->request->getPost("tanggalFasilitasImport") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalFasilitasImport")))) : "",  
                "fasilitas_import_code" => $this->request->getPost("kodeFasilitasImport"),

                "valuta"                => $this->request->getPost("valuta"),
                "ndpbm"                 => $this->request->getPost("npdpbm"),
                "cif_value"             => $this->request->getPost("nilaiCif"),
                "harga_penyerahan"      => $this->request->getPost("hargaPenyerahan"),
                "pengangkutan"          => $this->request->getPost("caraPengangkutan"),
                
                "bruto"                 => $this->request->getPost("bruto"),
                "netto"                 => $this->request->getPost("netto"),
                "item_count"            => $this->request->getPost("jumlahBarang"),

                "pembayaran"            => $this->request->getPost("pembayaran"),
                "wajib_bayar"           => $this->request->getPost("wajibBayar"),

                "tempat"                => $this->request->getPost("tempat"),
                "tanggal"               => $this->request->getPost("tanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggal")))) : "",
                "pemberitahu"           => $this->request->getPost("pemberitahu"),
                "jabatan"               => $this->request->getPost("jabatan"),

                "data_dokumen"          => $this->request->getPost("data_dokumen"),
                "data_kontainer"        => $this->request->getPost("data_kontainer"),
                "data_kemasan"          => $this->request->getPost("data_kemasan")
            ];
            
            $insert =  $this->modelBeaCukai->insert($payload);

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => json_encode($payload),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function bc25UpdateForm()
    {
        try {
            $rules = [
                "kantorPabean" => [
                    "rules" => "required"
                ],
                "kodeTujuanTpb" => [
                    "rules" => "required"
                ],
                "namaImportir" => [
                    "rules" => "required"
                ],
                "noIzinTPBImportir" => [
                    "rules" => "required"
                ],
                "APIImportir" => [
                    "rules" => "required"
                ],
                "alamatImportir" => [
                    "rules" => "required"
                ],
                "npwpPenerimaBarang" => [
                    "rules" => "required"
                ],
                "namaPenerimaBarang" => [
                    "rules" => "required"
                ],
                "APIPenerimaBarang" => [
                    "rules" => "required"
                ],
                "niperPenerimaBarang" => [
                    "rules" => "required"
                ],
                "alamatPenerimaBarang" => [
                    "rules" => "required"
                ],
                "noInvoice" => [
                    "rules" => "required"
                ],
                "noPackingList" => [
                    "rules" => "required"
                ],
                "tanggalPackingList" => [
                    "rules" => "required"
                ],
                "noKontrak" => [
                    "rules" => "required"
                ],
                "tanggalKontrak" => [
                    "rules" => "required"
                ],
                "valuta" => [
                    "rules" => "required"
                ],
                "npdpbm" => [
                    "rules" => "required"
                ],
                "nilaiCif" => [
                    "rules" => "required"
                ],
                "hargaPenyerahan" => [
                    "rules" => "required"
                ],
                "caraPengangkutan" => [
                    "rules" => "required"
                ],
                "bruto" => [
                    "rules" => "required"
                ],
                "netto" => [
                    "rules" => "required"
                ],
                "jumlahBarang" => [
                    "rules" => "required"
                ],
                "pembayaran" => [
                    "rules" => "required"
                ],
                "wajibBayar" => [
                    "rules" => "required"
                ],
                "tempat" => [
                    "rules" => "required"
                ],
                "tanggal" => [
                    "rules" => "required"
                ],
                "pemberitahu" => [
                    "rules" => "required"
                ],
                "jabatan" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $id = $this->request->getPost("id");

            $payload = [
                "company_id"            => $this->this_company_id,
                "status"                => "-",
                "status_perbaikan"      => "-",
                "aju_no"                => "-",
                "registration_no"       => "-",
                "registration_date"     => "-",
                "type"                  => "BC 2.5",
                "status_posting"        => "Belum Posting",

                "jenis_dokumen"         => $this->request->getPost("jenisDokumenBC"),
                "no_dokumen"            => $this->request->getPost("noDokumen"),

                "kantor_pabean"         => $this->request->getPost("kantorPabean"),
                "tujuan_tpb"            => $this->request->getPost("kodeTujuanTpb"),

                "importir_jenis_identitas"   => $this->request->getPost("jenisIdentitasImportir"),
                "importir_identitas"   => $this->request->getPost("identitasImportir"),
                "importir_jenis_api"   => $this->request->getPost("jenisAPIImportir"),
                "importir_name"         => $this->request->getPost("namaImportir"),
                "tpb_no"                => $this->request->getPost("noIzinTPBImportir"),
                "importir_api"          => $this->request->getPost("APIImportir"),
                "importir_address"      => $this->request->getPost("alamatImportir"),

                "pemilik_barang"        => $this->request->getPost("switchPemilikBarang") ? 1 : 0,
                "pemilik_barang_jenis_identitas"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("jenisIdentitasImportir") : $this->request->getPost("jenisIdentitasPemilikBarang"),
                "pemilik_barang_identitas"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("identitasImportir") : $this->request->getPost("identitasPemilikBarang"),
                "pemilik_barang_name"   => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("namaImportir") : $this->request->getPost("namaPemilikBarang"),
                "pemilik_barang_address"=> $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("alamatImportir") : $this->request->getPost("alamatPemilikBarang"),
                "pemilik_barang_api"    => $this->request->getPost("switchPemilikBarang") ? $this->request->getPost("APIImportir") : $this->request->getPost("APIPemilikBarang"),

                "penerima_barang_npwp"  => $this->request->getPost("npwpPenerimaBarang"),
                "penerima_barang_name"  => $this->request->getPost("namaPenerimaBarang"),
                "penerima_barang_api"   => $this->request->getPost("APIPenerimaBarang"),
                "penerima_barang_niper" => $this->request->getPost("niperPenerimaBarang"),
                "penerima_barang_address" => $this->request->getPost("alamatPenerimaBarang"),

                "invoice_id"            => $this->request->getPost("noInvoice"),
                "invoice_date"          => $this->request->getPost("tanggalInvoice") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalInvoice")))) : "",
                "no_packing_list"       => $this->request->getPost("noPackingList"),
                "tanggal_packing_list"  => $this->request->getPost("tanggalPackingList") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalPackingList")))) : "",
                "no_kontrak"            => $this->request->getPost("noKontrak"),
                "tanggal_kontrak"       => $this->request->getPost("tanggalKontrak") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalKontrak")))) : "",  
                "fasilitas_import_no"   => $this->request->getPost("noFasilitasImport"),
                "fasilitas_import_date" => $this->request->getPost("tanggalFasilitasImport") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalFasilitasImport")))) : "",  
                "fasilitas_import_code" => $this->request->getPost("kodeFasilitasImport"),

                "valuta"                => $this->request->getPost("valuta"),
                "ndpbm"                 => $this->request->getPost("npdpbm"),
                "cif_value"             => $this->request->getPost("nilaiCif"),
                "harga_penyerahan"      => $this->request->getPost("hargaPenyerahan"),
                "pengangkutan"          => $this->request->getPost("caraPengangkutan"),
                
                "bruto"                 => $this->request->getPost("bruto"),
                "netto"                 => $this->request->getPost("netto"),
                "item_count"            => $this->request->getPost("jumlahBarang"),

                "pembayaran"            => $this->request->getPost("pembayaran"),
                "wajib_bayar"           => $this->request->getPost("wajibBayar"),

                "tempat"                => $this->request->getPost("tempat"),
                "tanggal"               => $this->request->getPost("tanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggal")))) : "",
                "pemberitahu"           => $this->request->getPost("pemberitahu"),
                "jabatan"               => $this->request->getPost("jabatan"),

                "data_dokumen"          => $this->request->getPost("data_dokumen"),
                "data_kontainer"        => $this->request->getPost("data_kontainer"),
                "data_kemasan"          => $this->request->getPost("data_kemasan")
            ];
            
            $insert =  $this->modelBeaCukai->where(['id' => $id])->set($payload)->update();

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => json_encode($payload),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function bc25All()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "type"          => "BC 2.5"
        ];

        $condition = [
            "bea_cukai.company_id"  => $this->this_company_id,
            "type"                  => "BC 2.5"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $beaCukaiData = $this->modelBeaCukai->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "aju_no"                => $data->aju_no,
                "registration_no"       => $data->registration_no,
                "registration_date"     => $data->registration_date,
                "tujuan_tpb_name"       => $data->tujuan_tpb_name,
                "status_posting"        => $data->status_posting
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function bc261View()
    {
        return view('BeaCukai/bc-261/index');
    }

    public function bc261CreateFormView()
    {
        //Get Valuta
        $valuta = $this->modelMetadata->get_by_name_bc('Valuta');

        //Get Jenis TPB
        $jenisTPB = $this->modelMetadata->get_by_name_bc('Jenis TPB');

        //Get Pengangkutan
        $pengangkutan = $this->modelMetadata->get_by_name_bc('Pengangkutan');

        //Get Referensi Lokasi Bayar
        $referensiLokasiBayar = $this->modelMetadata->get_by_name_bc('Lokasi Bayar');

        //Get Wajib Bayar
        $wajibBayar = $this->modelMetadata->get_by_name_bc('Entitas');

        $data = [
            'wajibBayar' => $wajibBayar,
            'referensiLokasiBayar' => $referensiLokasiBayar,
            'valuta' => $valuta,
            'dokumen' => $this->modelSalesOrderInvoice->asObject()->findAll(),
            'jenisTPB' => $jenisTPB,
            'pengangkutan' => $pengangkutan,
            'kantorBeaCukai' => $this->modelKantorBeaCukai->asObject()->findAll()
        ];
        return view('BeaCukai/bc-261/create', $data);
    }

    public function bc261GetByIdFormView($id)
    {
        //Get Valuta
        $valuta = $this->modelMetadata->get_by_name_bc('Valuta');

        //Get Jenis TPB
        $jenisTPB = $this->modelMetadata->get_by_name_bc('Jenis TPB');

        //Get Pengangkutan
        $pengangkutan = $this->modelMetadata->get_by_name_bc('Pengangkutan');

        //Get Referensi Lokasi Bayar
        $referensiLokasiBayar = $this->modelMetadata->get_by_name_bc('Lokasi Bayar');

        //Get Wajib Bayar
        $wajibBayar = $this->modelMetadata->get_by_name_bc('Entitas');

        $data = [
            'wajibBayar' => $wajibBayar,
            'referensiLokasiBayar' => $referensiLokasiBayar,
            'valuta' => $valuta,
            'dokumen' => $this->modelSalesOrderInvoice->asObject()->findAll(),
            'jenisTPB' => $jenisTPB,
            'pengangkutan' => $pengangkutan,
            'kantorBeaCukai' => $this->modelKantorBeaCukai->asObject()->findAll()
        ];

        if (!empty($id)) {
            $dataBC = $this->modelBeaCukai->getById($id);
            $data["dataBC"] = $dataBC;

            if($dataBC->type !== "BC 2.6.1")
            {
                return view('BeaCukai/bc-261/index');
            }
        }
        
        return view('BeaCukai/bc-261/create', $data);
    }

    public function bc261SaveForm()
    {
        try {
            $rules = [
                "kantorPabean" => [
                    "rules" => "required"
                ],
                "kodeTujuanTpb" => [
                    "rules" => "required"
                ],
                "npwpImportir" => [
                    "rules" => "required"
                ],
                "namaImportir" => [
                    "rules" => "required"
                ],
                "noIzinTPBImportir" => [
                    "rules" => "required"
                ],
                "tanggalIzinTPB" => [
                    "rules" => "required"
                ],
                "APIImportir" => [
                    "rules" => "required"
                ],
                "alamatImportir" => [
                    "rules" => "required"
                ],
                "npwpPenerimaBarang" => [
                    "rules" => "required"
                ],
                "namaPenerimaBarang" => [
                    "rules" => "required"
                ],
                "alamatPenerimaBarang" => [
                    "rules" => "required"
                ],
                "noPackingList" => [
                    "rules" => "required"
                ],
                "tanggalPackingList" => [
                    "rules" => "required"
                ],
                "valuta" => [
                    "rules" => "required"
                ],
                "npdpbm" => [
                    "rules" => "required"
                ],
                "nilaiCif" => [
                    "rules" => "required"
                ],
                "nilaiCifRupiah" => [
                    "rules" => "required"
                ],
                "caraPengangkutan" => [
                    "rules" => "required"
                ],
                "bruto" => [
                    "rules" => "required"
                ],
                "netto" => [
                    "rules" => "required"
                ],
                "jumlahBarang" => [
                    "rules" => "required"
                ],
                "tempat" => [
                    "rules" => "required"
                ],
                "tanggal" => [
                    "rules" => "required"
                ],
                "pemberitahu" => [
                    "rules" => "required"
                ],
                "jabatan" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $payload = [
                "company_id"            => $this->this_company_id,
                "status"                => "-",
                "status_perbaikan"      => "-",
                "aju_no"                => "-",
                "registration_no"       => "-",
                "registration_date"     => "-",
                "type"                  => "BC 2.6.1",
                "status_posting"        => "Belum Posting",

                "kantor_pabean"         => $this->request->getPost("kantorPabean"),
                "tujuan_tpb"            => $this->request->getPost("kodeTujuanTpb"),

                "importir_npwp"         => $this->request->getPost("npwpImportir"),
                "importir_name"         => $this->request->getPost("namaImportir"),
                "tpb_no"                => $this->request->getPost("noIzinTPBImportir"),
                "tpb_date"              => $this->request->getPost("tanggalIzinTPB") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalIzinTPB")))) : "",
                "importir_api"          => $this->request->getPost("APIImportir"),
                "importir_address"      => $this->request->getPost("alamatImportir"),

                "penerima_barang_npwp"  => $this->request->getPost("npwpPenerimaBarang"),
                "penerima_barang_name"  => $this->request->getPost("namaPenerimaBarang"),
                "penerima_barang_address" => $this->request->getPost("alamatPenerimaBarang"),

                "no_packing_list"       => $this->request->getPost("noPackingList"),
                "tanggal_packing_list"  => $this->request->getPost("tanggalPackingList") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalPackingList")))) : "",
                "fasilitas_import_no"   => $this->request->getPost("noFasilitasImport"),
                "fasilitas_import_date" => $this->request->getPost("tanggalFasilitasImport") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalFasilitasImport")))) : "",  
                "fasilitas_import_code" => $this->request->getPost("kodeFasilitasImport"),

                "valuta"                => $this->request->getPost("valuta"),
                "ndpbm"                 => $this->request->getPost("npdpbm"),
                "cif_value"             => $this->request->getPost("nilaiCif"),
                "cif_price"             => $this->request->getPost("nilaiCifRupiah"),

                "pengangkutan"          => $this->request->getPost("caraPengangkutan"),
                
                "bruto"                 => $this->request->getPost("bruto"),
                "netto"                 => $this->request->getPost("netto"),
                "item_count"            => $this->request->getPost("jumlahBarang"),

                "tempat"                => $this->request->getPost("tempat"),
                "tanggal"               => $this->request->getPost("tanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggal")))) : "",
                "pemberitahu"           => $this->request->getPost("pemberitahu"),
                "jabatan"               => $this->request->getPost("jabatan"),

                "data_dokumen"          => $this->request->getPost("data_dokumen"),
                "data_kontainer"        => $this->request->getPost("data_kontainer"),
                "data_kemasan"          => $this->request->getPost("data_kemasan"),
                "data_jaminan"          => $this->request->getPost("data_jaminan")
            ];
            
            $insert =  $this->modelBeaCukai->insert($payload);

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => json_encode($payload),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function bc261UpdateForm()
    {
        try {
            $rules = [
                "kantorPabean" => [
                    "rules" => "required"
                ],
                "kodeTujuanTpb" => [
                    "rules" => "required"
                ],
                "npwpImportir" => [
                    "rules" => "required"
                ],
                "namaImportir" => [
                    "rules" => "required"
                ],
                "noIzinTPBImportir" => [
                    "rules" => "required"
                ],
                "tanggalIzinTPB" => [
                    "rules" => "required"
                ],
                "APIImportir" => [
                    "rules" => "required"
                ],
                "alamatImportir" => [
                    "rules" => "required"
                ],
                "npwpPenerimaBarang" => [
                    "rules" => "required"
                ],
                "namaPenerimaBarang" => [
                    "rules" => "required"
                ],
                "alamatPenerimaBarang" => [
                    "rules" => "required"
                ],
                "noPackingList" => [
                    "rules" => "required"
                ],
                "tanggalPackingList" => [
                    "rules" => "required"
                ],
                "valuta" => [
                    "rules" => "required"
                ],
                "npdpbm" => [
                    "rules" => "required"
                ],
                "nilaiCif" => [
                    "rules" => "required"
                ],
                "nilaiCifRupiah" => [
                    "rules" => "required"
                ],
                "caraPengangkutan" => [
                    "rules" => "required"
                ],
                "bruto" => [
                    "rules" => "required"
                ],
                "netto" => [
                    "rules" => "required"
                ],
                "jumlahBarang" => [
                    "rules" => "required"
                ],
                "tempat" => [
                    "rules" => "required"
                ],
                "tanggal" => [
                    "rules" => "required"
                ],
                "pemberitahu" => [
                    "rules" => "required"
                ],
                "jabatan" => [
                    "rules" => "required"
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $id = $this->request->getPost("id");

            $payload = [
                "company_id"            => $this->this_company_id,
                "status"                => "-",
                "status_perbaikan"      => "-",
                "aju_no"                => "-",
                "registration_no"       => "-",
                "registration_date"     => "-",
                "type"                  => "BC 2.6.1",
                "status_posting"        => "Belum Posting",

                "kantor_pabean"         => $this->request->getPost("kantorPabean"),
                "tujuan_tpb"            => $this->request->getPost("kodeTujuanTpb"),

                "importir_npwp"         => $this->request->getPost("npwpImportir"),
                "importir_name"         => $this->request->getPost("namaImportir"),
                "tpb_no"                => $this->request->getPost("noIzinTPBImportir"),
                "tpb_date"              => $this->request->getPost("tanggalIzinTPB") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalIzinTPB")))) : "",
                "importir_api"          => $this->request->getPost("APIImportir"),
                "importir_address"      => $this->request->getPost("alamatImportir"),

                "penerima_barang_npwp"  => $this->request->getPost("npwpPenerimaBarang"),
                "penerima_barang_name"  => $this->request->getPost("namaPenerimaBarang"),
                "penerima_barang_address" => $this->request->getPost("alamatPenerimaBarang"),

                "no_packing_list"       => $this->request->getPost("noPackingList"),
                "tanggal_packing_list"  => $this->request->getPost("tanggalPackingList") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalPackingList")))) : "",
                "fasilitas_import_no"   => $this->request->getPost("noFasilitasImport"),
                "fasilitas_import_date" => $this->request->getPost("tanggalFasilitasImport") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggalFasilitasImport")))) : "",  
                "fasilitas_import_code" => $this->request->getPost("kodeFasilitasImport"),

                "valuta"                => $this->request->getPost("valuta"),
                "ndpbm"                 => $this->request->getPost("npdpbm"),
                "cif_value"             => $this->request->getPost("nilaiCif"),
                "cif_price"             => $this->request->getPost("nilaiCifRupiah"),

                "pengangkutan"          => $this->request->getPost("caraPengangkutan"),
                
                "bruto"                 => $this->request->getPost("bruto"),
                "netto"                 => $this->request->getPost("netto"),
                "item_count"            => $this->request->getPost("jumlahBarang"),

                "tempat"                => $this->request->getPost("tempat"),
                "tanggal"               => $this->request->getPost("tanggal") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getPost("tanggal")))) : "",
                "pemberitahu"           => $this->request->getPost("pemberitahu"),
                "jabatan"               => $this->request->getPost("jabatan"),

                "data_dokumen"          => $this->request->getPost("data_dokumen"),
                "data_kontainer"        => $this->request->getPost("data_kontainer"),
                "data_kemasan"          => $this->request->getPost("data_kemasan"),
                "data_jaminan"          => $this->request->getPost("data_jaminan")
            ];
            
            $insert =  $this->modelBeaCukai->where(['id' => $id])->set($payload)->update();

            if (!$insert) {
                $data = [
                    "status"    => false,
                    "message"   => 'Data Gagal Disimpan!',
                    "payload"   => json_encode($payload),
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $data = [
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                "payload"   => json_encode($payload),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }

    public function bc261All()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "type"          => "BC 2.6.1"
        ];

        $condition = [
            "bea_cukai.company_id"  => $this->this_company_id,
            "type"                  => "BC 2.6.1"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $beaCukaiData = $this->modelBeaCukai->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "aju_no"                => $data->aju_no,
                "registration_no"       => $data->registration_no,
                "registration_date"     => $data->registration_date,
                "tujuan_tpb_name"       => $data->tujuan_tpb_name,
                "status_posting"        => $data->status_posting
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function bc262View()
    {
        return view('BeaCukai/bc-262/index');
    }

    public function bc262All()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "type"          => "BC 2.6.2"
        ];

        $condition = [
            "bea_cukai.company_id"  => $this->this_company_id,
            "type"                  => "BC 2.6.2"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $beaCukaiData = $this->modelBeaCukai->getList($condition, $addCondition, $limit, $offset);

        $dataBeaCukai = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($beaCukaiData['data'] as $data) {
            array_push($dataBeaCukai, [
                "no"                    => $no++,
                "id"                    => $data->id,
                "aju_no"                => $data->aju_no,
                "registration_no"       => $data->registration_no,
                "registration_date"     => $data->registration_date,
                "tujuan_tpb_name"       => $data->tujuan_tpb_name,
                "status_posting"        => $data->status_posting
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $beaCukaiData['totalData'],
            "recordsFiltered"   => $beaCukaiData['totalFilteredData'],
            "data"              => $dataBeaCukai,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function bc27View()
    {
        return view('BeaCukai/bc-27/index');
    }

    public function bc30View()
    {
        return view('BeaCukai/bc-30/index');
    }

    public function bc40View()
    {
        return view('BeaCukai/bc-40/index');
    }

    public function bc41View()
    {
        return view('BeaCukai/bc-41/index');
    }
}
