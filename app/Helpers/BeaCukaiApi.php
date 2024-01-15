<?php

namespace App\Helpers;

use App\Models\BCBarangDokumenModel;
use App\Models\BCBarangTarifModel;
use App\Models\MetadataModel;
use Exception;

class BeaCukaiApi
{

    protected $baseUrl, $username, $password;
    protected $metaDataModel;

    public function __construct()
    {
        $this->metaDataModel = new MetadataModel();
        $this->baseUrl = $this->metaDataModel->where('name', "Base Url BC")->first()['value'];
        $this->username = $this->metaDataModel->where('name', "Username BC")->first()['value'];
        $this->password = $this->metaDataModel->where('name', "Password BC")->first()['value'];
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

    public function kirimDokumenBC23($payload, $isFinal = false)
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrl . "/openapi/document?isFinal" . urlencode($isFinal);

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
                'message' => "Server Ceisa Error : " . $httpCode,
                'status' => false
            ];
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
    public function payloadTempleateKirimBC23($bc23Data, $bc23Kontainer, $bc23Barang, $bc23Entitas, $bc23Kemasan, $bc23Dokumen, $bc23Pengangkut)
    {
        $BCBarangDokumenModel = new BCBarangDokumenModel();
        $BCBarangTarifModel = new BCBarangTarifModel();

        $payload = [
            'asalData' => 'S',
            'asuransi' => $bc23Data['asuransi'],
            'bruto' => $bc23Data['bruto'],
            'cif' => $bc23Data['cif'],
            'fob' => $bc23Data['fob'],
            'freight' => $bc23Data['freight'],
            'hargaPenyerahan' => $bc23Data['harga_penyerahan'],
            'jabatanTtd' => $bc23Data['jabatan_pengusaha_ttd'],
            'jumlahKontainer' => count($bc23Kontainer),
            'kodeAsuransi' => $bc23Data['kode_asuransi'],
            'kodeDokumen' => $bc23Data['kode_dokumen'],
            'kodeIncoterm' => $bc23Data['kode_incoterm'],
            'kodeKantor' => $bc23Data['kode_kantor'],
            'kodeKantorBongkar' => $bc23Data['kode_kantor_bongkar'],
            'kodePelBongkar' => $bc23Data['kode_pelabuhan_bongkar'],
            'kodePelMuat' => $bc23Data['kode_pelabuhan_muat'],
            'kodePelTransit' => $bc23Data['kode_pelabuhan_transit'],
            'kodeTps' => $bc23Data['kode_tps'],
            'kodeTujuanTpb' => $bc23Data['kode_tujuan_tpb'],
            'kodeTutupPu' => '11', // referensi bc 1.1 
            'kodeValuta' => $bc23Data['kode_valuta'],
            'kotaTtd' => $bc23Data['kota_ttd'],
            'namaTtd' => $bc23Data['nama_ttd'],
            'ndpbm' => $bc23Data['ndpbm'],
            'netto' => $bc23Data['netto'],
            'nik' => '-', // ?
            'nilaiBarang' => (float)$bc23Data['nilai_barang'],
            'nomorAju' => $bc23Data['no_aju'],
            'nomorBc11' => $bc23Data['no_bc_11'],
            'posBc11' => $bc23Data['pos_bc_11'],
            'seri' => $bc23Data['seri'],
            'subposBc11' => $bc23Data['sub_pos_bc_11'],
            'tanggalBc11' => $bc23Data['tanggal_bc_11'],
            'tanggalTiba' =>  $bc23Data['tanggal_bc_11'], // ?
            'tanggalTtd' => $bc23Data['tanggal_ttd'],
            'biayaTambahan' => (float)$bc23Data['biaya_tambahan'],
            'biayaPengurang' => (float)$bc23Data['biaya_pengurang'],
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

        foreach ($bc23Barang as $b) {
            $barang = [
                'idBarang' => $b['id'],
                'asuransi' => (float)$b['asuransi'],
                'cif' => (float)$bc23Data['cif'],
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
                'seriBarang' => $b['seri_barang'],
                'spesifikasiLain' => $b['spesifikasi_lain'],
                'tipe' => $b['tipe_barang'],
                'ukuran' => $b['ukuran_barang'], // ?
                'uraian' => $b['uraian'],
                'ndpbm' => (float) $bc23Data['ndpbm'], // ?
                'cifRupiah' => (float) $b['cif_rupiah'],
                'hargaPerolehan' => (float)$b['harga_perolehan_barang'], // ?
                'kodeAsalBahanBaku' => 0, //kode asal bahan baku: [0] Impor atau [1] Lokal
                'barangTarif' => [],
                'barangDokumen' => []
            ];

            $barangTarifData = $BCBarangTarifModel->where('penerimaan_barang_id', $b['penerimaan_barang_id'])
                ->where('penerimaan_barang_detail_id', $b['penerimaan_barang_detail_id'])
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
                    'nilaiBayar' => (float)$bt['nilai_bayar'],
                    'nilaiFasilitas' => 0, // ?
                    'nilaiSudahDilunasi' => 0, // ?
                    'seriBarang' => $bt['seri_barang'],
                    'tarif' => (float) $bt['tarif_bea_masuk'],
                    'tarifFasilitas' => (float) $bt['tarif_fasilitas']
                ];
            }
            $barang['barangTarif'] = $barangTarifArr;

            $barangDokumenData = $BCBarangDokumenModel->where('penerimaan_barang_id', $b['penerimaan_barang_id'])
                ->where('penerimaan_barang_detail_id', $b['penerimaan_barang_detail_id'])
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

        foreach ($bc23Entitas as $b) {
            $entitasArr[] = [
                'alamatEntitas' => $b['alamat_entitas'],
                'kodeEntitas' => $b['kode_entitas'],
                'kodeJenisEntitas' => $b['kode_jenis_entitas'],
                'namaEntitas' => $b['nama_entitas'],
                'nibEntitas' => $b['nib_entitas'],
                'nomorIdentitas' => $b['nomor_identitas'],
                'nomorIjinEntitas' => $b['nomor_ijin_entitas'],
                'tanggalIjinEntitas' => $b['tanggal_ijin_entitas'],
                'seriEntitas' => $b['seri_entitas']
            ];
        }

        foreach ($bc23Kemasan as $b) {
            $kemasanArr[] = [
                'jumlahKemasan' => (int)$b['jumlah_kemasan'],
                'kodeJenisKemasan' => $b['kode_jenis_kemasan'],
                'seriKemasan' => $b['seri_kemasan'],
                'merkKemasan' => $b['merk_kemasan']
            ];
        }

        foreach ($bc23Kontainer as $b) {
            $kontainerArr[] = [
                'kodeTipeKontainer' => $b['kode_tipe_kontainer'],
                'kodeUkuranKontainer' => $b['kode_ukuran_kontainer'],
                'nomorKontainer' => $b['nomor_kontainer'],
                'seriKontainer' => (int)$b['seri_kontainer'],
                'kodeJenisKontainer' => $b['kode_jenis_kontainer']
            ];
        }

        foreach ($bc23Dokumen as $b) {
            $dokumenArr[] = [
                'kodeDokumen' => $b['kode_dokumen'],
                'nomorDokumen' => $b['nomor_dokumen'],
                'seriDokumen' => (int)$b['seri_dokumen'],
                'tanggalDokumen' => $b['tanggal_dokumen'],
                'idDokumen' => $b['id_dokumen']
            ];
        }

        foreach ($bc23Pengangkut as $b) {
            $pengangkutArr[] = [
                'kodeBendera' => $b['kode_bendera'],
                'namaPengangkut' => $b['nama_sarana_pengangkut'],
                'nomorPengangkut' => $b['nomor_pengangkut'],
                'kodeCaraAngkut' => $b['kode_cara_angkut'],
                'seriPengangkut' => $b['seri_pengangkut']
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
}
