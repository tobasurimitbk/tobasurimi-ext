<?php namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Models\BarangMasterModel;
use App\Models\BarangMasterSortirModel;
use App\Models\BarangMasterSpesifikasiModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Config\Services;
use Exception;

class Scale extends BaseController
{
    protected $this_company_id;
    protected $barangModel;
    private $nampanFile;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->barangModel = new BarangMasterSortirModel();
        $this->nampanFile = WRITEPATH . 'nampan.json';
    }

    public function generateQrBarangView()
    {
        return view('HROutsourcing/scale/index');
    }

    public function getData()
    {
        $barang = $this->barangModel
            ->asObject()
            ->where('company_id', $this->this_company_id)
            ->findAll();
        
        $data = [];
        foreach ($barang as $item) {
            $data[] = [
                'id' => $item->id,
                'name' => $item->name,
                'harga' => number_format($item->harga, 0, ',', '.'), // Format harga
                'createdAt' => $item->createdAt,
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function store()
    {
        if (!$this->validate([
            'name' => 'required',
            'harga' => 'required|numeric'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $this->barangModel->save([
                'name' => $this->request->getPost('name'),
                'harga' => $this->request->getPost('harga'),
                'company_id' => $this->this_company_id,
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Barang berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan barang: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $barang = $this->barangModel->find($id);

        if (!$barang) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $barang
        ]);
    }

    public function update($id)
    {
        if (!$this->validate([
            'name' => 'required',
            'harga' => 'required|numeric'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $this->barangModel->update($id, [
                'name' => $this->request->getPost('name'),
                'harga' => $this->request->getPost('harga'),
                'company_id' => $this->this_company_id,
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Barang berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengupdate barang: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $this->barangModel->delete($id);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus barang: ' . $e->getMessage()
            ]);
        }
    }


    public function generateQrBarang()
    {
        $spesifikasiId = $this->request->getPost('spesifikasi_id');
        $html = "";

        try {
            // Ambil data barang dan spesifikasi
            $spesifikasi = $this->barangModel
                ->where('id', $spesifikasiId)
                ->get()
                ->getRow();

            if (!$spesifikasi) {
                throw new Exception("Data spesifikasi tidak ditemukan.");
            }

            $barangName = strtoupper($spesifikasi->name);

            // Format dan encrypt ID
            $type = 'BRG';
            $encryptedId = weakEncrypt("{$type}-{$spesifikasiId}");

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
                        margin-bottom:10px;
                    '>{$barangName}</div>

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
            $spesifikasiModel = new BarangMasterSortirModel();;

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

        $barangSpesifikasiModel = new BarangMasterSortirModel();;

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


    // Get all nampan data
    public function getNampan()
    {
        try {
            $data = [];
            
            if (file_exists($this->nampanFile)) {
                $jsonContent = file_get_contents($this->nampanFile);
                $data = json_decode($jsonContent, true) ?? [];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ]);
        }
    }

    // Get single nampan by ID
    public function getNampanById($id)
    {
        try {
            $data = [];
            
            if (file_exists($this->nampanFile)) {
                $jsonContent = file_get_contents($this->nampanFile);
                $data = json_decode($jsonContent, true) ?? [];
            }

            $nampan = array_filter($data, function($item) use ($id) {
                return $item['id'] == $id;
            });

            if (empty($nampan)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data nampan tidak ditemukan'
                ]);
            }

            $nampan = array_values($nampan)[0];

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $nampan
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ]);
        }
    }

    // Save nampan (create/update)
    public function saveNampan()
    {
        if (!$this->validate([
            'nama' => 'required',
            'berat' => 'required|numeric'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $id = $this->request->getPost('id');
            $nama = $this->request->getPost('nama');
            $berat = $this->request->getPost('berat');

            // Load existing data
            $data = [];
            if (file_exists($this->nampanFile)) {
                $jsonContent = file_get_contents($this->nampanFile);
                $data = json_decode($jsonContent, true) ?? [];
            }

            if ($id) {
                // Update existing
                foreach ($data as &$item) {
                    if ($item['id'] == $id) {
                        $item['nama'] = $nama;
                        $item['berat'] = $berat;
                        $item['updated_at'] = date('Y-m-d H:i:s');
                        break;
                    }
                }
                $message = 'Nampan berhasil diupdate';
            } else {
                // Create new
                $newId = empty($data) ? 1 : (max(array_column($data, 'id')) + 1);
                $data[] = [
                    'id' => $newId,
                    'nama' => $nama,
                    'berat' => $berat,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $message = 'Nampan berhasil ditambahkan';
            }

            // Save to JSON file
            file_put_contents($this->nampanFile, json_encode($data, JSON_PRETTY_PRINT));

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    // Delete nampan
    public function deleteNampan($id)
    {
        try {
            $data = [];
            
            if (file_exists($this->nampanFile)) {
                $jsonContent = file_get_contents($this->nampanFile);
                $data = json_decode($jsonContent, true) ?? [];
            }

            // Filter out the item to delete
            $data = array_filter($data, function($item) use ($id) {
                return $item['id'] != $id;
            });

            // Reindex array
            $data = array_values($data);

            // Save back to JSON file
            file_put_contents($this->nampanFile, json_encode($data, JSON_PRETTY_PRINT));

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Nampan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    // Generate QR Code for nampan
    public function generateNampanQr($id)
    {
        try {
            // Load nampan data
            $data = [];
            if (file_exists($this->nampanFile)) {
                $jsonContent = file_get_contents($this->nampanFile);
                $data = json_decode($jsonContent, true) ?? [];
            }

            $nampan = array_filter($data, function($item) use ($id) {
                return $item['id'] == $id;
            });

            if (empty($nampan)) {
                throw new Exception("Data nampan tidak ditemukan.");
            }

            $nampan = array_values($nampan)[0];

            // Generate QR Code - YANG DIUBAH: Encrypt berat saja
            $type = 'NAMPAN';
            $encryptedBerat = weakEncrypt($nampan['berat']); // Encrypt berat saja
            $qrText = "{$type}-{$encryptedBerat}"; // Format: NAMPAN-encrypted_berat

            $qrCode = new QrCode($qrText);
            $qrCode->setSize(350);
            $qrCode->setMargin(10);
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));

            $dataUri = $qrCode->writeDataUri();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id' => $id,
                    'nama' => $nampan['nama'],
                    'berat' => $nampan['berat'],
                    'qr_image' => $dataUri
                ]
            ]);

        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

}
