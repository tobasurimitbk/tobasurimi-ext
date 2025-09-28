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
            // Enkripsi ID + amanin untuk URL
            $spesifikasiModel = new BarangMasterSpesifikasiModel();

            $data = $spesifikasiId->select('spesifikasi')->where('id', decrypt($spesifikasiId))->first();            


            return $this->response->setJSON([
                'status' => 'ok',
                'data'   => $data['spesifikasi']
            ]);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
