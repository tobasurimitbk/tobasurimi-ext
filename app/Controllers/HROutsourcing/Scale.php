<?php namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
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

        try {
            // Format dan encrypt ID
            $type = 'BRG';
            $encryptedId = encrypt("{$type}-{$spesifikasiId}");

            // QR text langsung isi type + value, bukan URL
            $qrText = "{$type}-{$encryptedId}";

            // Generate QR Code (isi text-nya aja)
            $qrCode = new QrCode($qrText);
            $qrCode->setSize(350);
            $qrCode->setMargin(10);
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));

            // Convert ke data URI
            $dataUri = $qrCode->writeDataUri();

            // Output tampilan
            $html .= "
                <div class='col-md-12 mb-4 text-center'>
                    <a href='{$dataUri}' download='qr-{$type}-{$spesifikasiId}.png'>
                        <img src='{$dataUri}' alt='QR Code' class='img-fluid'>
                    </a><br>
                    <small>
                        <strong>SCAN VALUE:</strong> {$qrText}<br>
                        <strong>ID:</strong> {$spesifikasiId}
                    </small>
                </div>
            ";

            return $this->response->setJSON([
                'status' => 'ok',
                'html'   => "<div class='row'>{$html}</div>"
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


}
