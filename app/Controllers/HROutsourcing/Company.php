<?php

namespace App\Controllers\HROutsourcing;

use App\Controllers\BaseController;
use App\Models\AttendancesUnitOutsourceModel;
use App\Models\DivisisModel;
use App\Models\HROutsourcingCompanyModel;
use App\Models\HROutsourcingEmployeeModel;
use App\Models\MetadataModel;

class Company extends BaseController
{
    protected $this_company_id;
    protected $divisiModel;
    protected $hrOutsourcingCompanyModel;
    protected $hrOutsourcingEmployeeModel;
    protected $hrOutsourcingAttendanceModel;
    protected $metaDataModel;

    public function __construct()
    {
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->divisiModel = new DivisisModel();
        $this->hrOutsourcingCompanyModel = new HROutsourcingCompanyModel();
        $this->hrOutsourcingEmployeeModel = new HROutsourcingEmployeeModel();
        $this->hrOutsourcingAttendanceModel = new AttendancesUnitOutsourceModel();
        $this->metaDataModel = new MetaDataModel();
    } 


    public function allTipeKaryawan()
    {
        try {
            $search = $this->request->getGet('search');
            $limit = $this->request->getGet('limit') ?? 25;
            $offset = $this->request->getGet('offset') ?? 0;
            
            // Query untuk data tipe karyawan outsource
            $builder = $this->metaDataModel
                ->where('name', 'tipe_karyawan_outsource')
                ->orderBy('id', 'DESC');
            
            // Jika ada search
            if ($search) {
                $builder->groupStart()
                    ->like('value', $search)
                    ->orLike('description', $search)
                    ->groupEnd();
            }
            
            // Hitung total
            $total = $builder->countAllResults(false);
            
            // Ambil data dengan pagination
            $data = $builder->findAll($limit, $offset);
            
            // Format response sesuai FE
            $formattedData = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'nama_tipe' => $item['value'],
                    'keterangan' => $item['description'],
                    'created_at' => $item['createdAt'],
                    'updated_at' => $item['updatedAt']
                ];
            }, $data);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formattedData,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * STORE TIPE KARYAWAN (CREATE)
     */
    public function storeTipeKaryawan()
    {
        try {
            // Validasi input
            $rules = [
                'nama_tipe' => 'required|max_length[100]',
                'keterangan' => 'max_length[255]'
            ];
            
            $messages = [
                'nama_tipe' => [
                    'required' => 'Nama tipe wajib diisi',
                    'max_length' => 'Nama tipe maksimal 100 karakter'
                ],
                'keterangan' => [
                    'max_length' => 'Keterangan maksimal 255 karakter'
                ]
            ];
            
            if (!$this->validate($rules, $messages)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(422);
            }
            
            // Cek duplikasi nama tipe
            $namaTipe = $this->request->getPost('nama_tipe');
            $exist = $this->metaDataModel
                ->where('name', 'tipe_karyawan_outsource')
                ->where('value', $namaTipe)
                ->first();
                
            if ($exist) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Nama tipe sudah ada dalam database'
                ])->setStatusCode(400);
            }
            
            // Data untuk disimpan
            $data = [
                'name' => 'tipe_karyawan_outsource',
                'value' => $namaTipe,
                'description' => $this->request->getPost('keterangan') ?? '',
                'createdAt' => date('Y-m-d H:i:s'),
                'updatedAt' => date('Y-m-d H:i:s')
            ];
            
            // Simpan ke database
            if ($this->metaDataModel->insert($data)) {
                $id = $this->metaDataModel->getInsertID();
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data tipe karyawan berhasil ditambahkan',
                    'data' => [
                        'id' => $id,
                        'nama_tipe' => $data['value'],
                        'keterangan' => $data['description']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data'
                ])->setStatusCode(500);
            }
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * UPDATE TIPE KARYAWAN
     */
    public function updateTipeKaryawan()
    {
        try {
            // Validasi input
            $rules = [
                'id' => 'required|integer',
                'nama_tipe' => 'required|max_length[100]',
                'keterangan' => 'max_length[255]'
            ];
            
            $messages = [
                'id' => [
                    'required' => 'ID wajib diisi',
                    'integer' => 'ID harus berupa angka'
                ],
                'nama_tipe' => [
                    'required' => 'Nama tipe wajib diisi',
                    'max_length' => 'Nama tipe maksimal 100 karakter'
                ],
                'keterangan' => [
                    'max_length' => 'Keterangan maksimal 255 karakter'
                ]
            ];
            
            if (!$this->validate($rules, $messages)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(422);
            }
            
            $id = $this->request->getPost('id');
            $namaTipe = $this->request->getPost('nama_tipe');
            
            // Cek data exist
            $dataExist = $this->metaDataModel->find($id);
            if (!$dataExist) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }
            
            // Cek duplikasi nama tipe (kecuali untuk data ini sendiri)
            $exist = $this->metaDataModel
                ->where('name', 'tipe_karyawan_outsource')
                ->where('value', $namaTipe)
                ->where('id !=', $id)
                ->first();
                
            if ($exist) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Nama tipe sudah ada dalam database'
                ])->setStatusCode(400);
            }
            
            // Data untuk diupdate
            $data = [
                'value' => $namaTipe,
                'description' => $this->request->getPost('keterangan') ?? '',
                'updatedAt' => date('Y-m-d H:i:s')
            ];
            
            // Update data
            if ($this->metaDataModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data tipe karyawan berhasil diperbarui',
                    'data' => [
                        'id' => $id,
                        'nama_tipe' => $data['value'],
                        'keterangan' => $data['description']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui data'
                ])->setStatusCode(500);
            }
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * DELETE TIPE KARYAWAN
     */
    public function destroyTipeKaryawan()
    {
        try {
            // Validasi input
            $rules = [
                'id' => 'required|integer'
            ];
            
            $messages = [
                'id' => [
                    'required' => 'ID wajib diisi',
                    'integer' => 'ID harus berupa angka'
                ]
            ];
            
            if (!$this->validate($rules, $messages)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors()
                ])->setStatusCode(422);
            }
            
            $id = $this->request->getPost('id');
            
            // Cek data exist
            $dataExist = $this->metaDataModel->find($id);
            if (!$dataExist) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }
            
            // Hapus data
            if ($this->metaDataModel->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data tipe karyawan berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data'
                ])->setStatusCode(500);
            }
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * GET SINGLE TIPE KARYAWAN (jika diperlukan untuk edit)
     */
    public function getTipeKaryawan($id)
    {
        try {
            $data = $this->metaDataModel->find($id);
            
            if (!$data) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }
            
            // Format response
            $formattedData = [
                'id' => $data['id'],
                'nama_tipe' => $data['value'],
                'keterangan' => $data['description']
            ];
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $formattedData
            ]);
            
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function index()
    {
        $data = [
            'divisi' => $this->divisiModel->getDivisiAccess(),
            'dataAttendanceUnit' => $this->hrOutsourcingAttendanceModel->findAll(),
        ];

        return view('HROutsourcing/company/index', $data);
    }


    public function all()
    {
        $payload = [
            "pageSize"         => $this->request->getVar("length"),
            "currentPage"      => ($this->request->getVar("start") / $this->request->getVar("length")) + 1,
            "sort"             => $this->request->getVar("sort"),
            "sortType"         => $this->request->getVar("sortType"),
        ];

        $addCondition = [
            "divisi_id"     => $this->request->getVar('divisi_id'),
            "search"        => $this->request->getVar("search"),
            "sort"          => $this->request->getVar("sort"),
            "sortType"      => $this->request->getVar("sortType"),
        ];

        $condition = [
            'hr_outsourcing_company.company_id' => $this->this_company_id
        ];

        $limit = $this->request->getVar("length");
        $offset = $this->request->getVar("start");
        $sppData = $this->hrOutsourcingCompanyModel->getList($condition, $addCondition, $limit, $offset);

        $dataSPP = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($sppData['data'] as $data) {
            $totalEmployee = count($this->hrOutsourcingEmployeeModel->where('company_id', $data->id)->findAll());
            array_push($dataSPP, [
                "no"            => $no++,
                "id"            => encrypt($data->id),
                "divisi"      => $data->divisi,
                "name" => $data->name,
                "total" => $totalEmployee,
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getVar("draw")),
            "recordsTotal"      => $sppData['totalData'],
            "recordsFiltered"   => $sppData['totalFilteredData'],
            "data"              => $dataSPP,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }

    public function store()
    {
        $name = $this->request->getVar('name');
        $divisiId = $this->request->getVar('divisi_id');
        $address = $this->request->getVar('address');
        $ip_finger = $this->request->getVar('ip_finger');

        $this->hrOutsourcingCompanyModel->insert([
            'company_id' => $this->this_company_id,
            'divisi_id' => $divisiId,
            'name' => $name,
            'address' => $address,
            'ip_finger' => $ip_finger
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Company Outsourcing Berhasil Disimpan",
            'token' => csrf_hash()
        ]);
    }

    public function update()
    {
        $id = decrypt($this->request->getVar('id'));
        $name = $this->request->getVar('name');
        $divisiId = $this->request->getVar('divisi_id');
        $address = $this->request->getVar('address');
        $ip_finger = $this->request->getVar('ip_finger');

        $this->hrOutsourcingCompanyModel->update($id, [
            'company_id' => $this->this_company_id,
            'divisi_id' => $divisiId,
            'name' => $name,
            'address' => $address,
            'ip_finger' => $ip_finger
        ]);

        return response()->setJSON([
            'status' => true,
            'message' => "Company Outsourcing Berhasil Diupdate",
            'token' => csrf_hash()
        ]);
    }

    public function destroy()
    {
        $id = decrypt($this->request->getVar('id'));
        $this->hrOutsourcingCompanyModel->delete($id);
        return response()->setJSON([
            'status' => true,
            'message' => "Company Outsourcing Berhasil Dihapus",
            'token' => csrf_hash()
        ]);
    }

    public function get($id)
    {
        $id = decrypt($id);
        $hrOutsourcingCompany = $this->hrOutsourcingCompanyModel->find($id);
        return response()->setJSON([
            'status' => true,
            'data' => $hrOutsourcingCompany,
            'token' => csrf_hash()
        ]);
    }
}
