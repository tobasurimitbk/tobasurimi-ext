<?php

namespace App\Controllers\Pembayaran;

use App\Controllers\BaseController;
use App\Models\PanjarSupplierModel;
use App\Models\SupplierModel;
use App\Models\PinjamanSupplierModel;
use App\Models\PanjarPinjamanTransactionModel;
use App\Models\LocalPOPaymentPanjarModel;
use App\Models\Sub_AkunsModel;
use App\Controllers\Accounting\JurnalUmum\JurnalUmum;

class PanjarSupplier extends BaseController
{

    protected $token;
    protected $this_company_id;

    protected $panjarSupplierModel;
    protected $pinjamanSupplierModel;
    protected $panjarPinjamanTransactionModel;
    protected $jurnalController;
    protected $supplierModel;
    protected $sub_AkunsModel;

    protected $localPOPaymentPanjarModel;
    public function __construct()
    {
        $this->token = session()->get("login")->token;
        $this->this_company_id = session()->get("login")->this_company_id;
        $this->panjarSupplierModel = new PanjarSupplierModel();
        $this->panjarPinjamanTransactionModel = new PanjarPinjamanTransactionModel();
        $this->pinjamanSupplierModel = new PinjamanSupplierModel();
        $this->supplierModel = new SupplierModel();
        $this->jurnalController = new JurnalUmum();
        $this->sub_AkunsModel = new Sub_AkunsModel();
        $this->localPOPaymentPanjarModel = new LocalPOPaymentPanjarModel();
    }

    public function index()
    {

        $data = [
            // 'noPanjar' => $this->panjarSupplierModel->getNumber($this->this_company_id)
        ];

        return view('Pembayaran/pembayaranPanjarSupplier/index', $data);
    }

    public function dropdownSupplierByType()
    {
        $typeSupplier = $this->request->getVar('type_supplier');
        $result = $this->supplierModel->getSupplierByType($typeSupplier);

        return response()->setJSON([
            'data' => $result,
            'status' => true
        ]);
    }

    public function savePanjarSupplier()
    {
        try {
            $rules = [
                "no_transaksi" => [
                    "rules" => "required"
                ],
                "jenis" => [
                    "rules" => "required"
                ],
                "tipe_supplier" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "details" => [
                    "rules" => "required"
                ],
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status" => false,
                    "message" => $errorList[array_keys($errorList)[0]],
                    'token' => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            // Check if transaction number already exists
            $check = $this->panjarPinjamanTransactionModel
                ->where('company_id', $this->this_company_id)
                ->where('no_transaction', $this->request->getPost("no_transaksi"))
                ->first();

            if ($check != null) {
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => "No Transaksi sudah digunakan",
                    'status' => false
                ]);
            }

            // Prepare parent transaction data
            $parentData = [
                "company_id" => $this->this_company_id,
                "supplier_id" => $this->request->getVar('supplier_id'),
                "type" => $this->request->getVar('jenis'),
                "no_transaction" => $this->request->getPost("no_transaksi"),
                "keterangan" => $this->request->getPost("keterangan"),
            ];

            // Start transaction
            $this->panjarPinjamanTransactionModel->db->transBegin();

            // Insert parent transaction
            $parentId = $this->panjarPinjamanTransactionModel->insert($parentData, true);

            if (!$parentId) {
                $this->panjarPinjamanTransactionModel->db->transRollback();
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => 'Gagal menyimpan data transaksi utama',
                    'status' => false
                ]);
            }

            // Process details
            $details = $this->request->getVar('details');
            $success = true;

            foreach ($details as $detail) {


                if ($detail['jenis_transaksi'] == 'PINJAMAN') {

                    $detailData = [
                        "transaction_id" => $parentId,
                        "company_id" => $this->this_company_id,
                        "supplier_id" => $this->request->getVar('supplier_id'),
                        "payment_date" => date('Y-m-d', strtotime(str_replace('/', '-', $detail['tanggal']))),
                        "jenis_transaksi" => $detail['jenis_transaksi'],
                        "total_pinjaman" => repairDouble($detail['nominal_pembayaran']),
                        "akun_kas" => $detail['akun_kas'],
                        "akun_selisih" => $detail['akun_selisih'],
                    ];

                    $insert = $this->pinjamanSupplierModel->insert($detailData);
                } else {

                    $detailData = [
                        "transaction_id" => $parentId,
                        "company_id" => $this->this_company_id,
                        "supplier_id" => $this->request->getVar('supplier_id'),
                        "payment_date" => date('Y-m-d', strtotime(str_replace('/', '-', $detail['tanggal']))),
                        "jenis_panjar" => $detail['jenis_transaksi'],
                        "total_panjar" => repairDouble($detail['nominal_pembayaran']),
                        "akun_kas" => $detail['akun_kas'],
                        "akun_selisih" => $detail['akun_selisih'],
                    ];

                    $insert = $this->panjarSupplierModel->insert($detailData);
                }

                if (!$insert) {
                    $success = false;
                    break;
                }
            }

            if (!$success) {
                $this->panjarPinjamanTransactionModel->db->transRollback();
                return response()->setJSON([
                    'token' => csrf_hash(),
                    'message' => 'Gagal menyimpan detail transaksi',
                    'status' => false
                ]);
            }

            // Commit transaction if all successful
            $this->panjarPinjamanTransactionModel->db->transCommit();

            $data = [
                "status" => true,
                "message" => "Data Berhasil disimpan",
                "payload" => json_encode(['parent' => $parentData, 'details' => $details]),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        } catch (\Exception $e) {
            if (isset($this->panjarPinjamanTransactionModel->db) && $this->panjarPinjamanTransactionModel->db->transStatus() !== false) {
                $this->panjarPinjamanTransactionModel->db->transRollback();
            }

            $data = [
                "status" => false,
                "message" => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
            return;
        }
    }



    public function updatePanjarSupplier()
    {
        try {
            $rules = [
                "id" => [
                    "rules" => "required"
                ],
                "no_transaksi" => [
                    "rules" => "required"
                ],
                "jenis" => [
                    "rules" => "required"
                ],
                "tipe_supplier" => [
                    "rules" => "required"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "details" => [
                    "rules" => "required"
                ],
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status" => false,
                    "message" => $errorList[array_keys($errorList)[0]],
                    'token' => csrf_hash()
                ];
                return $this->response->setJSON($data);
            }

            // Get the transaction ID to update
            $transactionId = decrypt($this->request->getVar('id'));


            // Check if transaction exists
            $existingTransaction = $this->panjarPinjamanTransactionModel
                ->where('company_id', $this->this_company_id)
                ->where('id', $transactionId)
                ->first();

            if (!$existingTransaction) {
                return $this->response->setJSON([
                    'token' => csrf_hash(),
                    'message' => "Transaksi tidak ditemukan",
                    'status' => false
                ]);
            }

            // Check if transaction number is changed and already exists
            if ($existingTransaction['no_transaction'] != $this->request->getPost("no_transaksi")) {
                $check = $this->panjarPinjamanTransactionModel
                    ->where('company_id', $this->this_company_id)
                    ->where('no_transaction', $this->request->getPost("no_transaksi"))
                    ->first();

                if ($check != null) {
                    return $this->response->setJSON([
                        'token' => csrf_hash(),
                        'message' => "No Transaksi sudah digunakan",
                        'status' => false
                    ]);
                }
            }

            // Prepare parent transaction data
            $parentData = [
                "supplier_id" => $this->request->getVar('supplier_id'),
                "type" => $this->request->getVar('jenis'),
                "no_transaction" => $this->request->getPost("no_transaksi"),
                "updated_at" => date('Y-m-d H:i:s'),
                "keterangan" => $this->request->getPost("keterangan"),
            ];

            // Start transaction
            $this->panjarPinjamanTransactionModel->db->transBegin();

            // Update parent transaction
            $updateParent = $this->panjarPinjamanTransactionModel
                ->where('id', $transactionId)
                ->set($parentData)
                ->update();

            if (!$updateParent) {
                $this->panjarPinjamanTransactionModel->db->transRollback();
                return $this->response->setJSON([
                    'token' => csrf_hash(),
                    'message' => 'Gagal mengupdate data transaksi utama',
                    'status' => false
                ]);
            }

            // Process details - first delete existing details
            $this->panjarSupplierModel->where('transaction_id', $transactionId)->delete();
            $this->pinjamanSupplierModel->where('transaction_id', $transactionId)->delete();

            // Then insert new details
            $details = $this->request->getVar('details');
            $success = true;

            foreach ($details as $detail) {
                if ($detail['jenis_transaksi'] == 'PINJAMAN') {

                    $detailData = [
                        "transaction_id" => $transactionId,
                        "company_id" => $this->this_company_id,
                        "supplier_id" => $this->request->getVar('supplier_id'),
                        "payment_date" => date('Y-m-d', strtotime(str_replace('/', '-', $detail['tanggal']))),
                        "jenis_transaksi" => $detail['jenis_transaksi'],
                        "total_pinjaman" => repairDouble($detail['nominal_pembayaran']),
                        "akun_kas" => $detail['akun_kas'],
                        "akun_selisih" => $detail['akun_selisih'],
                    ];

                    $insert = $this->pinjamanSupplierModel->insert($detailData);
                } else {

                    $detailData = [
                        "transaction_id" => $transactionId,
                        "company_id" => $this->this_company_id,
                        "supplier_id" => $this->request->getVar('supplier_id'),
                        "payment_date" => date('Y-m-d', strtotime(str_replace('/', '-', $detail['tanggal']))),
                        "jenis_panjar" => $detail['jenis_transaksi'],
                        "total_panjar" => repairDouble($detail['nominal_pembayaran']),
                        "akun_kas" => $detail['akun_kas'],
                        "akun_selisih" => $detail['akun_selisih'],
                    ];

                    $insert = $this->panjarSupplierModel->insert($detailData);
                }

                if (!$insert) {
                    $success = false;
                    break;
                }
            }

            if (!$success) {
                $this->panjarPinjamanTransactionModel->db->transRollback();
                return $this->response->setJSON([
                    'token' => csrf_hash(),
                    'message' => 'Gagal menyimpan detail transaksi',
                    'status' => false
                ]);
            }

            // Commit transaction if all successful
            $this->panjarPinjamanTransactionModel->db->transCommit();

            $data = [
                "status" => true,
                "message" => "Data Berhasil diupdate",
                "payload" => json_encode(['parent' => $parentData, 'details' => $details]),
                'token' => csrf_hash()
            ];
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            if (isset($this->panjarPinjamanTransactionModel->db) && $this->panjarPinjamanTransactionModel->db->transStatus() !== false) {
                $this->panjarPinjamanTransactionModel->db->transRollback();
            }

            $data = [
                "status" => false,
                "message" => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            return $this->response->setJSON($data);
        }
    }

    public function updateStatusPanjarSupplier()
    {
        $id = decrypt($this->request->getVar('id'));
        $status = $this->request->getVar('status');

        $this->panjarPinjamanTransactionModel->update($id, [
            'is_posted' => $status
        ]);

        $this->jurnalController->insertDataPanjarPinjamanTransaction($id);

        return response()->setJSON([
            "status" => true,
            "message" => "Status Posting Berhasil Diudpdate",
            "token" => csrf_hash()
        ]);
    }


    public function deletePanjarSupplier()
    {
        try {
            if (is_numeric($this->request->getPost('id'))) {
                $id = $this->request->getPost("id");
            } else {
                $id = decrypt($this->request->getPost("id"));
            }

            if (empty($id)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Gagal Dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $this->panjarSupplierModel->delete($id);
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

    public function allPanjarSupplier()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search")['value'] ?? $this->request->getGet("search"),
            "panjar_status" => $this->request->getVar("panjar_status"),
            "sort"          => $this->request->getGet("order")[0]['column'] ?? $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("order")[0]['dir'] ?? $this->request->getGet("sortType"),
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : "",
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : "",
        ];

        $addCondition = [
            "search"        => $payload['search'],
            "sort"          => $payload['sort'],
            "sortType"      => $payload['sortType'],
            "panjar_status" => $payload['panjar_status'],
            "dateStart"     => $payload['dateStart'],
            "dateEnd"       => $payload['dateEnd'],
        ];

        $conditionPanjar = [
            'panjar_supplier.company_id' => $this->this_company_id,
            'panjar_supplier.deletedAt' => null
        ];

        $conditionPinjaman = [
            'pinjaman_supplier.company_id' => $this->this_company_id,
            'pinjaman_supplier.deletedAt' => null
        ];

        $limit = $payload["pageSize"];
        $offset = $this->request->getGet("start");

        // Ambil data dari kedua model
        $pinjamanData = $this->pinjamanSupplierModel->getPinjamanSupplierList($addCondition, $conditionPinjaman, $limit, $offset);
        $panjarData = $this->panjarSupplierModel->getPanjarSupplierList($addCondition, $conditionPanjar, $limit, $offset);

        $dataSupplier = [];

        // Proses data panjar
        foreach ($panjarData['data'] as $data) {
            $bayar_panjar = $this->localPOPaymentPanjarModel
                ->where('panjar_id', $data->id)
                ->findAll();

            $total_bayar_panjar = 0;
            foreach ($bayar_panjar as $b) {
                $total_bayar_panjar += $b['bayar_panjar'];
            }

            $dataSupplier[] = [
                "id"            => encrypt($data->id),
                "no_panjar"     => $data->no_panjar,
                "jenis_panjar"  => str_replace('_', ' ', $data->jenis_panjar ?? 'PANJAR'),
                "supplier"      => $data->name,
                "akun_kas"      => $data->akun_kas,
                "akun_selisih"  => $data->akun_selisih,
                "akun_selisih_nama" => $data->akun_selisih_name,
                "akun_kas_nama" => $data->akun_kas_name,
                "payment_date"  => date('d/m/Y', strtotime($data->payment_date)),
                "total_panjar"  => number_format($data->total_panjar, 2),
                "sisa_panjar"   => number_format(($data->total_panjar) - $total_bayar_panjar, 2),
                "is_posted"     => $data->is_posted,
                "keterangan"     => $data->keterangan,
                "type"          => 'panjar' // Tambahkan identifier
            ];
        }

        // Proses data pinjaman (disesuaikan dengan struktur panjar)
        foreach ($pinjamanData['data'] as $data) {
            // Hitung total bayar pinjaman jika ada model pembayaran pinjaman
            $total_bayar_pinjaman = 0; // Anda perlu menyesuaikan ini dengan model pembayaran pinjaman

            $dataSupplier[] = [
                "id"            => encrypt($data->id),
                "no_panjar"     => $data->no_pinjaman ?? '-',
                "jenis_panjar"  => 'PINJAMAN',
                "supplier"      => $data->name,
                "akun_kas"      => $data->akun_kas ?? null,
                "akun_selisih"  => $data->akun_selisih ?? null,
                "akun_selisih_nama" => $data->akun_selisih_name ?? '-',
                "akun_kas_nama" => $data->akun_kas_name ?? '-',
                "payment_date"  => date('d/m/Y', strtotime($data->payment_date)),
                "total_panjar"  => number_format($data->total_pinjaman ?? 0, 2),
                "sisa_panjar"   => number_format(($data->total_pinjaman ?? 0) - $total_bayar_pinjaman, 2),
                "is_posted"     => $data->is_posted,
                "keterangan"     => $data->keterangan,
                "type"          => 'pinjaman' // Tambahkan identifier
            ];
        }

        // Gabungkan total data
        $totalData = $pinjamanData['totalData'] + $panjarData['totalData'];
        $totalFilteredData = $pinjamanData['totalFilteredData'] + $panjarData['totalFilteredData'];

        // Urutkan data gabungan berdasarkan payment_date DESC (default)
        usort($dataSupplier, function ($a, $b) {
            $dateA = strtotime(str_replace('/', '-', $a['payment_date']));
            $dateB = strtotime(str_replace('/', '-', $b['payment_date']));
            return $dateB - $dateA;
        });

        // Potong data sesuai pagination
        $paginatedData = array_slice($dataSupplier, $offset, $limit);

        // TAMBAHKAN NOMOR URUT SETELAH SORTING DAN PAGINATION
        $startNumber = $offset + 1;
        foreach ($paginatedData as $key => &$item) {
            $item['no'] = $startNumber + $key;
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $totalData,
            "recordsFiltered"   => $totalFilteredData,
            "data"              => $paginatedData,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }


    public function allPanjarPinjamanSupplier()
    {

        $payload = [
            "pageSize"      => $this->request->getGet("length") ?? 10,
            "currentPage"   => ($this->request->getGet("start") / ($this->request->getGet("length") ?? 10)) + 1,
            "search"        => $this->request->getGet("search")['value'] ?? $this->request->getGet("search") ?? '',
            "sort"          => $this->request->getGet("order")[0]['column'] ?? $this->request->getGet("sort") ?? 'payment_date',
            "sortType"      => $this->request->getGet("order")[0]['dir'] ?? $this->request->getGet("sortType") ?? 'DESC',
            "dateStart"     => $this->request->getVar("dateStart") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateStart")))) : null,
            "dateEnd"       => $this->request->getVar("dateEnd") ? date("Y-m-d", strtotime(str_replace("/", "-", $this->request->getVar("dateEnd")))) : null,
        ];

        $addCondition = [
            "search"        => $payload['search'],
            "sort"          => $payload['sort'],
            "sortType"      => $payload['sortType'],
            "dateStart"     => $payload['dateStart'],
            "dateEnd"       => $payload['dateEnd'],
        ];

        $conditionPanjarPinjaman = [
            'ppt.company_id'        => $this->this_company_id,
            'ppt.deletedAt'         => null,
        ];


        $limit = $payload["pageSize"];
        $offset = $this->request->getGet("start") ?? 0;

        // Get data from the transaction model
        $panjarPinjamanData = $this->panjarPinjamanTransactionModel
            ->getPanjarPinjamanSupplierList($addCondition, $conditionPanjarPinjaman, $limit, $offset);

        $dataPanjarPinjamanTransaction = [];

        // Process each transaction
        foreach ($panjarPinjamanData['data'] as $data) {

            $dataPanjarPinjamanTransaction[] = [
                "id"            => encrypt($data->id),
                "no_transaction" => $data->no_transaction,
                "type"          => $data->type,
                "is_posted"          => $data->is_posted,
                "supplier"      => $data->supplier_name,
                "nominal" => $data->total_pinjaman + $data->total_panjar,
                "createdAt"    => date('d/m/Y H:i', strtotime($data->createdAt)),
            ];
        }

        // Paginate the sorted data
        $paginatedData = array_slice($dataPanjarPinjamanTransaction, $offset, $limit);

        // Add row numbers
        foreach ($paginatedData as $key => &$item) {
            $item['no'] = $offset + $key + 1;
        }

        $response = [
            "draw"              => intval($this->request->getGet("draw") ?? 1),
            "recordsTotal"      => $panjarPinjamanData['totalData'] ?? 0,
            "recordsFiltered"   => $panjarPinjamanData['totalFilteredData'] ?? 0,
            "data"              => $paginatedData,
            "payload"           => $payload
        ];

        return $this->response->setJSON($response);
    }


    public function getByIdPanjarSupplier($id)
    {
        if (is_numeric($id)) {
            $id = $id;
        } else {
            $id = decrypt($id);
        }
        $panjarSupplierData = $this->panjarSupplierModel->getPanjarSupplierbyID($id);
        if (!$panjarSupplierData) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }

        $data = [
            "status"    => true,
            "supplier" => $this->supplierModel->getSupplierByType($panjarSupplierData->type), // GET SUPPLIER DETAIL
            "data"      => $panjarSupplierData,
        ];
        echo json_encode($data);

        return;
    }

    public function getByIdPanjarPinjamanSupplier($id)
    {
        // Decrypt ID if needed
        $id = is_numeric($id) ? $id : decrypt($id);

        // Get main transaction data
        $transaction = $this->panjarPinjamanTransactionModel->getPanjarPinjamanSupplierbyID($id);

        if (!$transaction) {
            return $this->response->setJSON([
                "status" => false,
                "message" => 'Data transaksi tidak ditemukan'
            ])->setStatusCode(404);
        }

        // Get all panjar details for this transaction
        $panjarDetails = $this->panjarSupplierModel
            ->where('transaction_id', $id)
            ->findAll();

        // Get all pinjaman details for this transaction
        $pinjamanDetails = $this->pinjamanSupplierModel
            ->where('transaction_id', $id)
            ->findAll();

        // Get account information from sub_akuns table
        $accountIds = [];
        foreach ($panjarDetails as $detail) {
            $accountIds[] = $detail['akun_kas'];
            $accountIds[] = $detail['akun_selisih'];
        }
        foreach ($pinjamanDetails as $detail) {
            $accountIds[] = $detail['akun_kas'];
            $accountIds[] = $detail['akun_selisih'];
        }

        $uniqueAccountIds = array_unique(array_filter($accountIds));
        $accounts = [];
        if (!empty($uniqueAccountIds)) {
            $accounts = $this->sub_AkunsModel
                ->whereIn('id', $uniqueAccountIds)
                ->findAll();
            $accounts = array_combine(
                array_column($accounts, 'id'),
                $accounts
            );
        }

        // Combine all details into one array with type indicator
        $combinedDetails = [];

        // Add panjar details
        foreach ($panjarDetails as $detail) {
            $combinedDetails[] = [
                "id" => $detail['id'],
                "jenis_transaksi" => $detail['jenis_panjar'],
                "payment_date" => $detail['payment_date'],
                "nominal_pembayaran" => $detail['total_panjar'],
                "akun_kas" => [
                    "id" => $detail['akun_kas'],
                    "name" => $accounts[$detail['akun_kas']]['nama_sub'] ?? null
                ],
                "akun_selisih" => [
                    "id" => $detail['akun_selisih'],
                    "name" => $accounts[$detail['akun_selisih']]['nama_sub'] ?? null
                ],
                "keterangan" => $detail['keterangan'],
                "createdAt" => $detail['createdAt']
            ];
        }

        // Add pinjaman details
        foreach ($pinjamanDetails as $detail) {
            $combinedDetails[] = [
                "id" => $detail['id'],
                "jenis_transaksi" => "PINJAMAN",
                "payment_date" => $detail['payment_date'],
                "nominal_pembayaran" => $detail['total_pinjaman'],
                "akun_kas" => [
                    "id" => $detail['akun_kas'],
                    "name" => $accounts[$detail['akun_kas']]['nama_sub']
                ],
                "akun_selisih" => [
                    "id" => $detail['akun_selisih'],
                    "name" => $accounts[$detail['akun_selisih']]['nama_sub']
                ],
                "keterangan" => $detail['keterangan'],
                "createdAt" => $detail['createdAt']
            ];
        }

        // Calculate totals
        $totalPanjar = array_reduce($panjarDetails, function ($carry, $item) {
            return $carry + ($item['total_panjar'] ?? 0);
        }, 0);

        $totalPinjaman = array_reduce($pinjamanDetails, function ($carry, $item) {
            return $carry + ($item['total_pinjaman'] ?? 0);
        }, 0);

        // Get supplier data
        $supplier = $this->supplierModel->find($transaction->supplier_id);

        // Prepare response data
        $response = [
            "status" => true,
            "data" => [
                "transaction" => [
                    "id" => encrypt($transaction->id),
                    "no_transaction" => $transaction->no_transaction,
                    "type" => $transaction->type,
                    "createdAt" => $transaction->createdAt,
                    "is_posted" => $transaction->is_posted ?? 0,
                    "total_panjar" => $totalPanjar,
                    "total_pinjaman" => $totalPinjaman,
                    "grand_total" => $totalPanjar + $totalPinjaman
                ],
                "supplier" => $supplier ? [
                    "id" => $supplier['id'],
                    "name" => $supplier['name'],
                    "type" => $supplier['type']
                ] : null,
                "details" => $combinedDetails
            ]
        ];

        return $this->response->setJSON($response);
    }

    public function dropDownHistoryPembayaranPanjar()
    {
        $id = decrypt($this->request->getVar('id'));

        $historyPembayaranPanjarData = $this->localPOPaymentPanjarModel->getPembayaranPanjarDetailsbyPanjarId($id);
        $panjarDetail = $this->panjarSupplierModel->getPanjarSupplierbyID($id);

        if (!$panjarDetail) {
            $data = [
                "status"    => false,
                "message"   => 'Not Found!'
            ];
            echo json_encode($data);
            return;
        }
        $data = [
            "status"    => true,
            "data"      => $historyPembayaranPanjarData,
            'panjar_detail' => $panjarDetail

        ];


        return response()->setJSON($data);
    }
    public function generateNoPanjar()
    {
        $noPanjar = $this->panjarPinjamanTransactionModel->getNumber($this->this_company_id);
        return json_encode($noPanjar);
    }

    public function getSubAkun()
    {
        $Sub_AkunsModel = new Sub_AkunsModel();
        $search = trim($this->request->getGet('search')); // Ambil & bersihkan input pencarian

        $subAkun = $Sub_AkunsModel
            ->select('id, nama_sub')
            ->where('company_id', $this->this_company_id)
            ->where('deletedAt', null)
            ->like('nama_sub', $search)
            ->findAll(10); // Batasi hasil max 10 biar efisien

        return $this->response->setJSON($subAkun);
    }
}
