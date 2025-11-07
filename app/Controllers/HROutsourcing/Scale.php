<?php namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSpesifikasiModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Config\Services;
use Exception;

class Scale extends BaseController
{
    public function generateQrBarangView()
    {
        return view('HROutsourcing/scale/index');
    }

    public function generateQrBarang()
    {
        $spesifikasiId = $this->request->getPost('spesifikasi_id');
        $html = "";

        $spesifikasiModel = new BarangMasterSpesifikasiModel();

        try {
            // Ambil data barang dan spesifikasi
            $spesifikasi = $spesifikasiModel
                ->select('barang_master_spesifikasi.*, barang_master.barang_name')
                ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id', 'left')
                ->where('barang_master_spesifikasi.id', $spesifikasiId)
                ->get()
                ->getRow();

            if (!$spesifikasi) {
                throw new Exception("Data spesifikasi tidak ditemukan.");
            }

            $barangName = strtoupper($spesifikasi->barang_name);
            $spesifikasiName = strtoupper($spesifikasi->spesifikasi ?? '-');

            // Format dan encrypt ID
            $type = 'BRG';
            $encryptedId = encrypt("{$type}-{$spesifikasiId}");

            // QR text langsung isi type + value
            $qrText = "{$type}-{$encryptedId}";

            // Generate QR Code
            $qrCode = new QrCode($qrText);
            $qrCode->setSize(380);
            $qrCode->setMargin(12);
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));

            $dataUri = $qrCode->writeDataUri();

            // ==== HTML tampilannya ====
            $html .= "
                <div style='
                    width:100%;
                    text-align:center;
                    font-family:Arial, Helvetica, sans-serif;
                    padding:30px 10px;
                '>
                    <div style='
                        font-size:26px;
                        font-weight:900;
                        letter-spacing:1px;
                        text-transform:uppercase;
                        margin-bottom:5px;
                    '>{$barangName}</div>

                    <div style='
                        font-size:23px;
                        font-weight:600;
                        color:#555;
                        margin-bottom:20px;
                        text-transform:uppercase;
                    '>{$spesifikasiName}</div>

                    <img src='{$dataUri}' alt='QR Code' 
                        style='width:350px;height:350px;display:block;margin:0 auto;border:5px solid #000;border-radius:8px;'>

                    <div style='
                        margin-top:15px;
                        font-size:14px;
                        color:#222;
                        font-weight:500;
                    '>
                    </div>
                </div>

                <style>
                    @media print {
                        body {
                            margin:0;
                            padding:0;
                            text-align:center;
                            background:#fff;
                        }
                        img {
                            page-break-inside: avoid;
                        }
                        div {
                            page-break-inside: avoid;
                        }
                    }
                </style>
            ";

            return $this->response->setJSON([
                'status' => 'ok',
                'html'   => "<div class='row justify-content-center'>{$html}</div>"
            ]);

        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function getBarangByIdQr($encryptedId)
    {
        // Siapkan CORS (buat scanner / mobile app)
        $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        try {
            // decrypt isi QR (hasilnya misal "BRG-123")
            $decoded = decrypt($encryptedId);

            // pecah tipe dan id (misal "BRG-12")
            $parts = explode('-', $decoded);
            if (count($parts) < 2) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Format QR tidak valid.'
                ]);
            }

            [$type, $id] = $parts;
            $type = strtoupper($type);

            // Validasi type
            if ($type !== 'BRG') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Tipe QR '{$type}' tidak cocok untuk barang."
                ]);
            }

            // Ambil data barang
            $spesifikasiModel = new BarangMasterSpesifikasiModel();

            $data = $spesifikasiModel
                ->select("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS nama_barang")
                ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
                ->where('barang_master_spesifikasi.id', $id)
                ->first();

            if (!$data) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data barang tidak ditemukan.'
                ]);
            }

            // ✅ Biar Golang lo bisa langsung ambil "nama_barang"
            return $this->response->setJSON([
                'status' => 'ok',
                'nama_barang' => $data['nama_barang'],
            ]);

        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal membaca QR: ' . $e->getMessage()
            ]);
        }
    }

    public function searchMasterBarang()
    {
        $barangMasterModel = new BarangMasterModel();

        $term = $this->request->getGet('q');

        if (strlen($term) < 3) {
            return $this->response->setJSON([
                'data' => [],
                'status' => false,
                'message' => 'Minimal 3 karakter'
            ]);
        }
        

        $builder = $barangMasterModel
            ->select('barang_master.barang_name as master_barang, barang_master.id as id')
            ->where('barang_master.deletedAt', null)
            ->where('barang_master.company_id', session()->get("login")->this_company_id)
            ->where('barang_master.type_barang', 'bahan_baku')
            ->groupStart()
                ->like('barang_master.barang_name', "%{$term}%")
            ->groupEnd();

        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'data'   => $data,
            'status' => true,
            'token'  => csrf_hash()
        ]);
    }

    public function searchBarang()
    {
        $term = $this->request->getGet('q');
        $barang = $this->request->getGet('barang_id');

        $barangSpesifikasiModel = new BarangMasterSpesifikasiModel();

        $builder = $barangSpesifikasiModel
            ->select('barang_master_spesifikasi.id as spesifikasi_id, barang_master.barang_name as master_barang, barang_master_spesifikasi.spesifikasi as spesifikasi, satuans.kode_satuan')
            ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
            ->join('satuans', 'satuans.id = barang_master_spesifikasi.satuan_1')
            ->where('barang_master_spesifikasi.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            ->where('barang_master.id', $barang)
            ->where('barang_master.company_id', session()->get("login")->this_company_id)
            ->where('barang_master.type_barang', 'bahan_baku')
            ->groupStart()
                ->like('barang_master_spesifikasi.spesifikasi', "%{$term}%")
            ->groupEnd();

        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'data'   => $data,
            'status' => true,
            'token'  => csrf_hash()
        ]);
    }


}
