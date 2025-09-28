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
            // Enkripsi ID + amanin untuk URL
                $encrypted = encrypt($spesifikasiId);

                // Generate QR Code
                $qrCode = new QrCode(base_url("barang/detail/" . $encrypted));
                $qrCode->setSize(350); // lebih besar
                $qrCode->setMargin(10);
                $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));

                // Convert ke base64 (Data URI)
                $dataUri = $qrCode->writeDataUri();

                // Append ke HTML
                $html .= "
                    <div class='col-md-12 mb-4 text-center'>
                        <a href='{$dataUri}' download='qr-{$encrypted}.png'>
                            <img src='{$dataUri}' alt='QR Code' class='img-fluid'>
                        </a><br>
                        <small><strong>ID:</strong> {$encrypted}</small>
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

    public function getBarangByIdQr($spesifikasiId)
    {
        try {
            $spesifikasiModel = new BarangMasterSpesifikasiModel();

            $data = $spesifikasiModel
                ->select("CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS nama_barang")
                ->join('barang_master', 'barang_master.id = barang_master_spesifikasi.barang_master_id')
                ->where('barang_master_spesifikasi.id', decrypt($spesifikasiId))
                ->first(); 

            return $this->response
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->setJSON([
                    'status' => 'ok',
                    'nama_barang'   => $data['nama_barang'] ?? null
                ]);
        } catch (Exception $e) {
            return $this->response
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
        }
    }

}
