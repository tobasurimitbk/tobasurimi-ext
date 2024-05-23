<?php

namespace App\Helpers;

use App\Models\BCBarangDokumenModel;
use App\Models\BCBarangTarifModel;
use App\Models\CeisaSettingModel;
use App\Models\MetadataModel;
use Exception;

class BeaCukaiApi
{
    protected $npwpPerusahaan;
    protected $baseUrl, $baseUrlDev, $username, $password;
    protected $metaDataModel;

    public function __construct($username, $password)
    {
        $this->metaDataModel = new MetadataModel();
        $this->baseUrl = $this->metaDataModel->where('name', "Base Url BC")->first()['value'];
        $this->baseUrlDev = $this->metaDataModel->where('name', "Base Url BC")->first()['description'];
        $this->npwpPerusahaan =  $this->metaDataModel->where('name', "NPWP Importir Default BC")->first()['value'];
        $this->username = $username;
        $this->password = $password;
    }

    // API GET
    public function getNilaiValuta($kodeValuta)
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrl . "/openapi/kurs/" . $kodeValuta;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'message' => curl_error($ch),
                'status' => false
            ];
        } else {
            $responseData = json_decode($response);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                if (count($responseData->data) == 0) {
                    return [
                        'data' => 0,
                        'status' => true
                    ];
                } else {
                    return [
                        'data' => $responseData->data[0]->nilaiKurs,
                        'status' => true
                    ];
                }
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }
        }

        curl_close($ch);
    }


    public function getListKodePelabuhan($kodeKantor)
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrl . "/openapi/pelabuhan/kodeKantor/" . $kodeKantor;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'message' => curl_error($ch),
                'status' => false
            ];
        } else {
            $responseData = json_decode($response);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                return [
                    'data' => $responseData->data,
                    'status' => true
                ];
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }
        }
    }

    public function getManifest($noHostBL, $tglHostBL, $kodeKantor, $namaImportir)
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrl . "/openapi/manifes-bc11?noHostBl=" . urlencode($noHostBL) . "&tglHostBl=" . urlencode($tglHostBL) . "&kodeKantor=" . urlencode($kodeKantor) . "&nama=" . urlencode($namaImportir);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            return [
                'message' => curl_error($ch) . ". Code " . $httpCode,
                'status' => false
            ];
        } else {
            $responseData = json_decode($response);

            if ($httpCode == 200) {
                $responseData->tglBc11 = $responseData->tglBc11 == null ? date('d/m/Y', strtotime(date('Y-m-d'))) : date('d/m/Y', strtotime($responseData->tglBc11));
                $responseData->caraPengangkutan = encrypt($responseData->caraPengangkutan);
                $responseData->bendera = encrypt($responseData->bendera);

                return [
                    'data' => $responseData,
                    'status' => true
                ];
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }
        }
    }

    public function kirimDokumenBC($payload, $isFinal = false)
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrlDev . "/openapi/document?isFinal" . urlencode($isFinal);

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'message' => curl_error($ch),
                'status' => false
            ];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            $responseData = json_decode($response);
            return [
                'data' => $responseData,
                'status' => true
            ];
        } else {
            return [
                'message' => $response,
                'status' => false
            ];
        }
    }

    public function getListStatusResponseAll()
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrl . "/openapi/status?idPerusahaan=" . $this->npwpPerusahaan;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            return [
                'message' => curl_error($ch) . ". Code " . $httpCode,
                'status' => false
            ];
        } else {
            $responseData = json_decode($response);

            if ($httpCode == 200) {
                return $responseData;
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }
        }
    }

    public function getTokenApi()
    {
        try {
            $endPoint = $this->baseUrl . "/nle-oauth/v1/user/login";
            $headers = array(
                'Content-Type: application/json',
            );

            $postData = array(
                'username' => $this->username,
                'password' => $this->password,
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endPoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return [
                    'message' => curl_error($ch),
                    'status' => false
                ];
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                $responseData = json_decode($response);
                return [
                    'token' => $responseData->item->access_token,
                    'status' => true
                ];
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }

            curl_close($ch);
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'status' => false
            ];
        }
    }

    // PAYLOAD
    public function payloadTempleateKirimBC23($bcData, $bcKontainer, $bcBarang, $bcEntitas, $bcKemasan, $bcDokumen, $bcPengangkut)
    {
        $BCBarangDokumenModel = new BCBarangDokumenModel();
        $BCBarangTarifModel = new BCBarangTarifModel();

        $payload = [
            'asalData' => 'S',
            'asuransi' => (float)$bcData['asuransi'],
            'bruto' => (float)$bcData['bruto'],
            'cif' => (float)$bcData['cif'],
            'fob' => (float)$bcData['fob'],
            'freight' =>  (float)$bcData['freight'],
            'hargaPenyerahan' =>  (float)$bcData['harga_penyerahan'],
            'jabatanTtd' => $bcData['jabatan_pengusaha_ttd'],
            'jumlahKontainer' => count($bcKontainer),
            'kodeAsuransi' => $bcData['kode_asuransi'],
            'kodeDokumen' => $bcData['kode_dokumen'],
            'kodeIncoterm' => $bcData['kode_incoterm'],
            'kodeKantor' => $bcData['kode_kantor'],
            'kodeKantorBongkar' => $bcData['kode_kantor_bongkar'],
            'kodePelBongkar' => $bcData['kode_pelabuhan_bongkar'],
            'kodePelMuat' => $bcData['kode_pelabuhan_muat'],
            'kodePelTransit' => $bcData['kode_pelabuhan_transit'],
            'kodeTps' => $bcData['kode_tps'],
            'kodeTujuanTpb' => $bcData['kode_tujuan_tpb'],
            'kodeTutupPu' => '11', // referensi bc 1.1 
            'kodeValuta' => $bcData['kode_valuta'],
            'kotaTtd' => $bcData['kota_ttd'],
            'namaTtd' => $bcData['nama_ttd'],
            'ndpbm' =>  (float)$bcData['ndpbm'],
            'netto' =>  (float)$bcData['netto'],
            'nik' => '-', // ?
            'nilaiBarang' => (float)$bcData['nilai_barang'],
            'nomorAju' => str_replace('-', '', $bcData['no_aju']),
            'nomorBc11' => $bcData['no_bc_11'],
            'posBc11' => $bcData['pos_bc_11'],
            'seri' => (int)$bcData['seri'],
            'subposBc11' => $bcData['sub_pos_bc_11'],
            'tanggalBc11' => $bcData['tanggal_bc_11'],
            'tanggalTiba' =>  $bcData['tanggal_bc_11'], // ?
            'tanggalTtd' => $bcData['tanggal_ttd'],
            'biayaTambahan' => (float)$bcData['biaya_tambahan'],
            'biayaPengurang' => (float)$bcData['biaya_pengurang'],
            'kodeKenaPajak' => $bcData['kode_kena_pajak'],
            'barang' => [],
            'entitas' => [],
            'kemasan' => [],
            'kontainer' => [],
            'dokumen' => [],
            'pengangkut' => []
        ];
        $barangArr = [];
        $entitasArr = [];
        $kemasanArr = [];
        $kontainerArr = [];
        $dokumenArr = [];
        $pengangkutArr = [];

        foreach ($bcBarang as $b) {
            $barang = [
                'idBarang' => $b['id'],
                'asuransi' => (float)$b['asuransi'],
                'cif' => (float)$bcData['cif'],
                'diskon' => (float)$b['diskon'], // ?
                'fob' => (float)$b['fob'],
                'freight' => (float)$b['freight'],
                'hargaEkspor' => (float)$b['harga_ekspor'],
                'hargaPenyerahan' => (float)$b['harga_penyerahan_barang'], // ?
                'hargaSatuan' => (float)$b['harga_satuan_barang'],
                'isiPerKemasan' => (int)$b['jumlah_satuan'], // ?
                'jumlahKemasan' => (float)$b['jumlah_kemasan'],
                'jumlahSatuan' => (float)$b['jumlah_satuan'],
                'kodeBarang' => $b['kode_barang'],
                'kodeDokumen' => $b['kode_dokumen'],
                'kodeKategoriBarang' => $b['kode_kategori_barang'],
                'kodeJenisKemasan' => $b['kode_jenis_kemasan'],
                'kodeNegaraAsal' => $b['kode_negara_asal'],
                'kodePerhitungan' => $b['kode_perhitungan'],
                'kodeSatuanBarang' => $b['kode_satuan_barang'],
                'merk' => $b['merk_barang'],
                'netto' => (float)$b['netto'],
                'nilaiBarang' => (float)$b['nilai_barang'], // ?
                'nilaiTambah' => (float)$b['nilai_tambah'],
                'posTarif' => $b['pos_tarif'],
                'seriBarang' => (int)$b['seri_barang'],
                'spesifikasiLain' => $b['spesifikasi_lain'],
                'tipe' => $b['tipe_barang'],
                'ukuran' => $b['ukuran_barang'], // ?
                'uraian' => $b['uraian'],
                'ndpbm' => (float) $bcData['ndpbm'], // ?
                'cifRupiah' => (float) $b['cif_rupiah'],
                'hargaPerolehan' => (float)$b['harga_perolehan_barang'], // ?
                'kodeAsalBahanBaku' => '0', //kode asal bahan baku: [0] Impor atau [1] Lokal
                'barangTarif' => [],
                'barangDokumen' => []
            ];

            $barangTarifData = $BCBarangTarifModel
                ->where('bc_purchase_order_id', $b['bc_purchase_order_id'])
                ->where('penerimaan_barang_id', $b['penerimaan_barang_id'])
                ->where('deletedAt', null)
                ->findAll();

            $barangTarifArr = [];
            foreach ($barangTarifData as $bt) {
                $barangTarifArr[] = [
                    'kodeJenisTarif' => $bt['kode_jenis_tarif'],
                    'jumlahSatuan' => 0, // ?
                    'kodeFasilitasTarif' => $bt['kode_fasilitas_tarif'],
                    'kodeSatuanBarang' => $bt['kode_satuan_barang'],
                    'kodeJenisPungutan' => $bt['kode_jenis_pungutan'],
                    'nilaiBayar' => roundNumber($bt['nilai_bayar'], 0.01),
                    'nilaiFasilitas' => 0, // ?
                    'nilaiSudahDilunasi' => 0, // ?
                    'seriBarang' => (int)$bt['seri_barang'],
                    'tarif' => (float)$bt['tarif_bea_masuk'],
                    'tarifFasilitas' => (float)$bt['tarif_fasilitas']
                ];
            }
            $barang['barangTarif'] = $barangTarifArr;

            $barangDokumenData = $BCBarangDokumenModel
                ->where('bc_purchase_order_id', $b['bc_purchase_order_id'])
                ->where('penerimaan_barang_id', $b['penerimaan_barang_id'])
                ->where('deletedAt', null)
                ->findAll();

            $barangDokumenArr = [];
            foreach ($barangDokumenData as $bd) {
                $barangDokumenArr[] = [
                    'seriDokumen' => $bd['seri_dokumen']
                ];
            }
            $barang['barangDokumen'] = $barangDokumenArr;

            $barangArr[] = $barang;
        }

        // ENTITAS
        foreach ($bcEntitas as $b) {
            // IMPORTIR ATAU PENGUSAHA TPB
            $entitasArr[] = [
                'alamatEntitas' => $b['alamat_entitas'],
                'kodeEntitas' => $b['kode_entitas'],
                'kodeJenisIdentitas' => $b['kode_jenis_entitas'],
                'namaEntitas' => $b['nama_entitas'],
                'nibEntitas' => $b['nib_entitas'],
                'nomorIdentitas' => $b['nomor_identitas'],
                'nomorIjinEntitas' => $b['nomor_ijin_entitas'],
                'tanggalIjinEntitas' => $b['tanggal_ijin_entitas'],
                'seriEntitas' => (int)$b['seri_entitas']
            ];
            // PEMASOK
            $entitasArr[] = [
                'alamatEntitas' => $b['alamat_pemasok'],
                'kodeEntitas' => '5',
                'kodeJenisIdentitas' => $b['kode_jenis_entitas'],
                'namaEntitas' => $b['nama_pemasok'],
                'kodeNegara' => $b['kode_negara_pemasok'],
                'seriEntitas' => 2
            ];
            // PEMILIK BARANG
            $entitasArr[] = [
                'alamatEntitas' => $b['alamat_pemilik_barang'],
                'kodeEntitas' => '7',
                'kodeJenisIdentitas' => $b['kode_jenis_entitas'],
                'namaEntitas' => $b['nama_pemilik_barang'],
                'nibEntitas' => $b['nib_entitas'],
                'nomorIdentitas' => $b['npwp_pemilik_barang'],
                'nomorIjinEntitas' => $b['nomor_ijin_entitas'],
                'tanggalIjinEntitas' => $b['tanggal_ijin_entitas'],
                'kodeStatus' => '3', // KODE STATUS PENGUSAHA
                'nomorIjinEntitas' => $b['nomor_ijin_entitas'],
                'tanggalIjinEntitas' => $b['tanggal_ijin_entitas'],
                'kodeJenisApi' => '1',
                'seriEntitas' => 3
            ];
        }


        foreach ($bcKemasan as $b) {
            $kemasanArr[] = [
                'jumlahKemasan' => (int)$b['jumlah_kemasan'],
                'kodeJenisKemasan' => $b['kode_jenis_kemasan'],
                'seriKemasan' => (int)$b['seri_kemasan'],
                'merkKemasan' => $b['merk_kemasan']
            ];
        }

        foreach ($bcKontainer as $b) {
            $kontainerArr[] = [
                'kodeTipeKontainer' => $b['kode_tipe_kontainer'],
                'kodeUkuranKontainer' => $b['kode_ukuran_kontainer'],
                'nomorKontainer' => $b['nomor_kontainer'],
                'seriKontainer' => (int)$b['seri_kontainer'],
                'kodeJenisKontainer' => $b['kode_jenis_kontainer']
            ];
        }

        foreach ($bcDokumen as $b) {
            $dokumenArr[] = [
                'kodeDokumen' => $b['kode_dokumen'],
                'nomorDokumen' => $b['nomor_dokumen'],
                'seriDokumen' => (int)$b['seri_dokumen'],
                'tanggalDokumen' => $b['tanggal_dokumen'],
                'idDokumen' => $b['id_dokumen']
            ];
        }

        foreach ($bcPengangkut as $b) {
            $pengangkutArr[] = [
                'kodeBendera' => $b['kode_bendera'],
                'namaPengangkut' => $b['nama_sarana_pengangkut'],
                'nomorPengangkut' => $b['nomor_pengangkut'],
                'kodeCaraAngkut' => $b['kode_cara_angkut'],
                'seriPengangkut' => (int)$b['seri_pengangkut']
            ];
        }

        $payload['barang'] = $barangArr;
        $payload['entitas'] = $entitasArr;
        $payload['kemasan'] = $kemasanArr;
        $payload['kontainer'] = $kontainerArr;
        $payload['dokumen'] = $dokumenArr;
        $payload['pengangkut'] = $pengangkutArr;

        return $payload;
    }

    public function payloadTempleateKirimBC40($bcData, $bcKontainer, $bcBarang, $bcEntitas, $bcKemasan, $bcDokumen, $bcPengangkut, $bcBarangTarif)
    {
        $BCBarangTarifModel = new BCBarangTarifModel();

        $payload = [
            'asalData' => 'S',
            'asuransi' => (float)$bcData['asuransi'],
            'bruto' => (float)$bcData['bruto'],
            'cif' => (float)$bcData['cif'],
            'kodeJenisTpb' => (string)$bcData['kode_jenis_tpb'],
            'freight' => (float)$bcData['freight'],
            'hargaPenyerahan' => (float)$bcData['harga_penyerahan'],
            'idPengguna' => $bcData['id_pengguna'],
            'jabatanTtd' => $bcData['jabatan_ttd'],
            'jumlahKontainer' => (int)count($bcKontainer),
            'kodeDokumen' => $bcData['kode_dokumen'],
            'kodeKantor' => $bcData['kode_kantor'],
            'kodeTujuanPengiriman' => $bcData['kode_tujuan_pengiriman'],
            'kotaTtd' => $bcData['kota_ttd'],
            'namaTtd' => $bcData['nama_ttd'],
            'netto' => (int)$bcData['netto'],
            'nik' => $bcData['nik'] == null ? "-" : $bcData['nik'],
            'nomorAju' => str_replace('-', '', $bcData['no_aju']),
            'seri' => (int)$bcData['seri'],
            'tanggalAju' => date('Y-m-d'),
            'tanggalTtd' => $bcData['tanggal_ttd'],
            'userPortal' => '-',
            'volume' => (float)$bcData['volume'],
            'biayaTambahan' => 0,
            'biayaPengurang' => 0,
            'vd' => 0, // NTR
            'uangMuka' => (float)$bcData['uang_muka'],
            'nilaiJasa' => (float)$bcData['nilai_jasa'],
            'entitas' => [],
            'dokumen' => [],
            'pengangkut' => [],
            'kontainer' => [],
            'kemasan' => [],
            'pungutan' => [],
            'barang' => [],
        ];
        $entitasArr = [];
        $dokumenArr = [];
        $pengangkutArr = [];
        $kontainerArr = [];
        $kemasanArr = [];
        $barangTarifArr = [];
        $barangArr = [];

        foreach ($bcEntitas as $b) {
            $entitasArr[] = [
                'alamatEntitas' => $b['alamat_entitas'],
                'kodeEntitas' => $b['kode_entitas'],
                'kodeJenisEntitas' => $b['kode_jenis_entitas'],
                'namaEntitas' => $b['nama_entitas'],
                'nibEntitas' => $b['nib_entitas'],
                'nomorIdentitas' => $b['nomor_identitas'],
                'nomorIjinEntitas' => $b['nomor_ijin_entitas'],
                'tanggalIjinEntitas' => $b['tanggal_ijin_entitas'],
                'seriEntitas' => (int)$b['seri_entitas'],
                'kodeJenisIdentitas' => '5'
            ];

            $entitasArr[] = [
                'alamatEntitas' => $b['alamat_entitas'],
                'kodeEntitas' => '7',
                'kodeJenisApi' => '2',
                'kodeJenisEntitas' => $b['kode_jenis_entitas'],
                'namaEntitas' => $b['alamat_pemasok'],
                'nibEntitas' => $b['nib_entitas'],
                'nomorIdentitas' => $b['npwp_pemasok'],
                'nomorIjinEntitas' => $b['nomor_ijin_entitas'],
                'tanggalIjinEntitas' => $b['tanggal_ijin_entitas'],
                'seriEntitas' => (int)$b['seri_entitas'],
                'kodeJenisIdentitas' => '5'
            ];

            $entitasArr[] = [
                'alamatEntitas' => $b['alamat_pemilik_barang'],
                'kodeEntitas' => '9',
                'kodeJenisApi' => '2',
                'kodeJenisEntitas' => $b['kode_jenis_entitas'],
                'namaEntitas' => $b['nama_pemilik_barang'],
                'nibEntitas' => $b['nib_entitas'],
                'nomorIdentitas' => $b['npwp_pemilik_barang'],
                'nomorIjinEntitas' => $b['nomor_ijin_entitas'],
                'tanggalIjinEntitas' => $b['tanggal_ijin_entitas'],
                'seriEntitas' => (int)$b['seri_entitas'],
                'kodeJenisIdentitas' => '5'
            ];
        }

        foreach ($bcDokumen as $b) {
            $dokumenArr[] = [
                'kodeDokumen' => $b['kode_dokumen'],
                'nomorDokumen' => $b['nomor_dokumen'],
                'seriDokumen' => (int)$b['seri_dokumen'],
                'tanggalDokumen' => $b['tanggal_dokumen'],
            ];
        }

        foreach ($bcPengangkut as $b) {
            $pengangkutArr[] = [
                'namaPengangkut' => $b['nama_sarana_pengangkut'],
                'nomorPengangkut' => $b['nomor_pengangkut'],
                'seriPengangkut' => (int)$b['seri_pengangkut']
            ];
        }

        foreach ($bcKontainer as $b) {
            $kontainerArr[] = [
                'kodeJenisKontainer' => $b['kode_jenis_kontainer'],
                'kodeTipeKontainer' => $b['kode_tipe_kontainer'],
                'kodeUkuranKontainer' => $b['kode_ukuran_kontainer'],
                'nomorKontainer' => $b['nomor_kontainer'],
                'seriKontainer' => (int)$b['seri_kontainer'],
            ];
        }

        foreach ($bcKemasan as $b) {
            $kemasanArr[] = [
                'jumlahKemasan' => (int)$b['jumlah_kemasan'],
                'kodeJenisKemasan' => $b['kode_jenis_kemasan'],
                'merkKemasan' => $b['merk_kemasan'],
                'seriKemasan' => (int)$b['seri_kemasan'],
            ];
        }

        foreach ($bcBarangTarif as $b) {
            $barangTarifArr[] = [
                'kodeFasilitasTarif' => $b['kode_fasilitas_tarif'],
                'kodeJenisPungutan' => $b['kode_jenis_pungutan'],
                'nilaiPungutan' => (float)$b['nilai_bayar'] == null ? 0 : (float)$b['nilai_bayar'],
            ];
        }

        foreach ($bcBarang as $b) {
            $barang = [
                'asuransi' => 0,
                'bruto' => 0,
                'cif' => 0,
                'diskon' => (float)$b['diskon'],
                'hargaEkspor' => (float)$b['harga_ekspor'],
                'hargaPenyerahan' => (float)$b['harga_ekspor'],
                'hargaSatuan' => (float)$b['harga_ekspor'] / (float)$b['jumlah_satuan'],
                'isiPerKemasan' => (int)$b['jumlah_satuan'],
                'jumlahRealisasi' => 0,
                'jumlahSatuan' => (float)$b['jumlah_satuan'],
                'kodeBarang' => $b['kode_barang'],
                'kodeDokumen' => $b['kode_dokumen'],
                'kodeJenisKemasan' => $b['kode_jenis_kemasan'],
                'kodeSatuanBarang' => $b['kode_satuan_barang'],
                'merk' => $b['merk_barang'],
                'netto' => (float)$b['netto'],
                'nilaiBarang' => (float)$b['nilai_barang'],
                'posTarif' => $b['pos_tarif'],
                'seriBarang' => (int)$b['seri_barang'],
                'spesifikasiLain' => $b['spesifikasi_lain'],
                'tipe' => $b['tipe_barang'],
                'ukuran' => $b['ukuran_barang'],
                'uraian' => $b['uraian'],
                'volume' => (float)$bcData['volume'],
                'jumlahKemasan' => count($bcKemasan),
                'cifRupiah' => 0,
                'hargaPerolehan' => 0,
                'kodeAsalBahanBaku' => '1',
                'ndpbm' => 1,
                'uangMuka' => 0,
                'nilaiJasa' => 0,
                'barangTarif' => [],
            ];

            $barangTarifData = $BCBarangTarifModel->where('penerimaan_barang_id', $b['penerimaan_barang_id'])
                ->where('bc_purchase_order_id', $b['bc_purchase_order_id'])
                ->where('barang1_id', $b['barang1_id'])
                ->where('deletedAt', null)
                ->findAll();

            $barangTarifArr = [];
            foreach ($barangTarifData as $bt) {
                $barangTarifArr[] = [
                    'kodeJenisTarif' => $bt['kode_jenis_tarif'],
                    'jumlahSatuan' => 0,
                    'kodeFasilitasTarif' => $bt['kode_fasilitas_tarif'],
                    'kodeSatuanBarang' => $bt['kode_satuan_barang'],
                    'nilaiBayar' => (float)$bt['nilai_bayar'],
                    'nilaiFasilitas' => 0,
                    'nilaiSudahDilunasi' => 0,
                    'seriBarang' => (int)$bt['seri_barang'],
                    'tarif' => (float) $bt['tarif_bea_masuk'],
                    'tarifFasilitas' => (float) $bt['tarif_fasilitas'],
                    'kodeJenisPungutan' => $bt['kode_jenis_pungutan'],
                    'nilaiPungutan' => 0
                ];
            }
            $barang['barangTarif'] = $barangTarifArr;
            $barangArr[] = $barang;
        }

        $payload['barang'] = $barangArr;
        $payload['entitas'] = $entitasArr;
        $payload['kemasan'] = $kemasanArr;
        $payload['kontainer'] = $kontainerArr;
        $payload['dokumen'] = $dokumenArr;
        $payload['pengangkut'] = $pengangkutArr;
        $payload['pungutan'] = $barangTarifArr;

        return $payload;
    }
}
