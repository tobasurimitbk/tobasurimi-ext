<?php

namespace App\Controllers\Purchase;

use App\Controllers\BaseController;

use App\Models\TandaTerimaFakturModel;
use App\Models\TandaTerimaFakturDetailModel;
use App\Models\PenerimaanBarangDetailModel;
use App\Models\PajakTandaTerimaFakturModel;
use App\Models\SupplierModel;

use Dompdf\Dompdf;

class TerimaFakturImport extends BaseController
{
    private $dompdf;

    private $token;
    private $user_id;
    private $this_company_id;

    public function __construct()
    {
        $this->dompdf = new Dompdf();

        $this->token = session()->get("login")->token;
        $this->user_id = session()->get("login")->user_id;
        $this->this_company_id = session()->get("login")->this_company_id;
    }

    public function terimaFakturImport()
    {
        return view('Purchase/terimaFakturImport/index');
    }

    public function createTerimaFakturImport()
    {   
        $supplierModel = new SupplierModel();

        //Get Supplier
        $supplierCond = [
            'company_id'        => $this->this_company_id,
            'supplier_buyer'    => 'SUPPLIER',
            'kategori'          => 'IMPORT'
        ];
        $supplierList = $supplierModel->asObject()
            ->where($supplierCond)
            ->findAll();

        $data = [
            // "dataSupplier" => $supplierList
        ];

        return view('Purchase/terimaFakturImport/form', $data);
    }

    public function getByIdTerimaFakturImport($id)
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $supplierModel = new SupplierModel();
        $tandaTerimaFakturDetModel = new TandaTerimaFakturDetailModel();
        $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();

        $selectQry = "tanda_terima_faktur.*, 
                      DATE_FORMAT(invoice_date, '%d/%m/%Y') AS invoice_date,
                      DATE_FORMAT(receive_date, '%d/%m/%Y') AS receive_date";
        $data = $tandaTerimaFakturModel->asObject()
            ->select($selectQry)
            ->find($id);

        if (empty($data)) {
            return view('errors/html/error_404', ['message' => 'Not Found!']);
        }

        $supplierCond = [
            'company_id'        => $this->this_company_id,
            'type'              => ($data->tipe_bahan === 'BAKU') ? 'BAHAN BAKU' : 'BAHAN PENOLONG',
            'supplier_buyer'    => 'SUPPLIER',
            'kategori'          => 'IMPORT'
        ];
        $supplierList = $supplierModel->asObject()
            ->where($supplierCond)
            ->findAll();

        $detSelectQry = "penerimaan_barang_detail_id AS id,
                         'no_po' AS no_po, 
                         DATE_FORMAT(lpb_date, '%d/%m/%Y') AS lpb_date, 
                         lpb_no AS no_lpb,
                         item_name,
                         qty,
                         unit,
                         (qty * price) AS total";
        $detData = $tandaTerimaFakturDetModel->asObject()
            ->select($detSelectQry)
            ->where('tanda_terima_faktur_id', $id)
            ->findAll();

        $taxSelectQry = "DATE_FORMAT(tax_inv_date, '%d/%m/%Y') AS taxInvDate, 
                         tax_inv_no AS taxInvNo, 
                         tax_type AS taxType, 
                         tax_amt AS taxAmt, 
                         tax_status AS taxStatus, 
                         tax_note AS taxNote";
        $taxData = $pajakTandaTerimaFakturModel->asObject()
            ->select($taxSelectQry)
            ->where('tanda_terima_faktur_id', $id)
            ->findAll();

        $data->item_total = $data->nominal_faktur + $data->potongan - $data->tambahan;

        $data = [
            "dataTerimaFaktur"  => $data,
            "dataSupplier"      => $supplierList,
            "selectedItems"     => $detData,
            "taxData"           => $taxData
        ];

        return view('Purchase/terimaFakturImport/form', $data);
    }

    public function getBySupplierId($supplierId = null)
    {
        //Get Faktur
        $responseFaktur = curl_request("GET", "/tandaTerimaFaktur/getBySupplier/$supplierId", $this->token);

        $dataFaktur = [];
        if ($responseFaktur["code"] === 200) {
            $dataFaktur = json_decode($responseFaktur["body"])->data;
        }

        $data = [
            "data" => $dataFaktur
        ];

        echo json_encode($data);
        return;
    }

    public function allTerimaFakturImport()
    {
        $payload = [
            "pageSize"      => $this->request->getGet("length"),
            "currentPage"   => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType"),
            "idCompany"     => $this->this_company_id,
            "kategori"      => "IMPORT",
            "type"          => "BAHAN BAKU"
        ];

        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $condition = [
            // "suppliers.company_id"  => $this->this_company_id,
            "faktur_type"           => "IMPORT"
        ];
        $addCondition = [
            "search"    => $this->request->getGet("search"),
            "sort"      => $this->request->getGet("sort"),
            "sortType"  => $this->request->getGet("sortType")
        ];
        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $supplierData = $tandaTerimaFakturModel->getInvoiceList($condition, $addCondition, $limit, $offset);

        $dataSupplier = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($supplierData['data'] as $data) {
            array_push($dataSupplier, [
                "no"            => $no++,
                "id"            => $data->id,
                "faktur_no"     => $data->faktur_no,
                "supplier_name" => $data->supplierName,
                "nominal_faktur"=> $data->nominal_faktur,
                "invoice_date"  => $data->invoice_date,
                "receive_date"  => $data->receive_date,
                "recipient"     => $data->userName
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $supplierData['totalData'],
            "recordsFiltered"   => $supplierData['totalFilteredData'],
            "data"              => $dataSupplier,
            // "response" => $response,
            "payload"           => $payload
        ];

        echo json_encode($data);
        return;
    }
    
    public function saveTerimaFakturImport()
    {
        try{
            $postData = $this->request->getPost();
            $postData["penerimaan_barang"] = json_decode($postData["penerimaan_barang"], true);
            $postData["pengenaan_pajak"] = json_decode($postData["pengenaan_pajak"], true);
            
            $tandaTerimaFakturModel = new TandaTerimaFakturModel();
            $tandaTerimaFakturDetModel = new TandaTerimaFakturDetailModel();
            $penerimaanBarangDetModel = new PenerimaanBarangDetailModel();
            $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();

            $rules = [
                "tipe_bahan" => [
                    "rules" => "required|in_list[BAKU,PENOLONG]"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "invoice_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "receive_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "potongan" => [
                    "rules" => "permit_empty|is_natural"
                ],
                "tambahan" => [
                    "rules" => "permit_empty|is_natural"
                ],
                "penerimaan_barang.*.id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "penerimaan_barang.*.no_po" => [
                    "rules" => "required"
                ],
                "penerimaan_barang.*.lpb_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "penerimaan_barang.*.no_lpb" => [
                    "rules" => "required"
                ],
                "penerimaan_barang.*.item_name" => [
                    "rules" => "required"
                ],
                "penerimaan_barang.*.qty" => [
                    "rules" => "required|numeric"
                ],
                "penerimaan_barang.*.unit" => [
                    "rules" => "required"
                ],
                "pengenaan_pajak.*.taxInvDate" => [
                    "rules" => "permit_empty|valid_date[d/m/Y]"
                ],
                "pengenaan_pajak.*.taxInvNo" => [
                    "rules" => "permit_empty"
                ],
                "pengenaan_pajak.*.taxType" => [
                    "rules" => "permit_empty"
                ],
                "pengenaan_pajak.*.taxAmt" => [
                    "rules" => "permit_empty|numeric"
                ],
                "pengenaan_pajak.*.taxStatus" => [
                    "rules" => "permit_empty"
                ],
                "pengenaan_pajak.*.taxNote" => [
                    "rules" => "permit_empty"
                ]
            ];
    
            if (!$this->validateData($postData, $rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $updateData = [];
            $detData = [];
            $taxData = [];
            $invAmt = 0;

            $penerimaanDetIds = array_column($postData['penerimaan_barang'], 'id');
            /* $penerimaanDetData = $penerimaanBarangDetModel->asObject()
                ->whereIn('id', $penerimaanDetIds)
                ->findAll();

            if (count($penerimaanDetData) != count($penerimaanDetIds)) {
                throw new Exception('Error Bro!');
            } */

            foreach ($postData['penerimaan_barang'] as $data) {
                
                $penDetData = $penerimaanBarangDetModel->asObject()
                    ->where('summarized_qty <', 'qty', false)
                    ->find($data['id']);

                if (empty($penDetData)) {
                    $data = [
                        "status"    => false,
                        "message"   => 'Penerimaan tidak ditemukan!',
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }

                $invAmt += $penDetData->harga * $data['qty'];
                $detData[] = [
                    'penerimaan_barang_detail_id'   => $data['id'],
                    'po_no'                         => $data['no_po'],
                    'lpb_date'                      => date("Y/m/d", strtotime(str_replace("/", "-", $data['lpb_date']))),
                    'lpb_no'                        => $data['no_lpb'],
                    'item_name'                     => $data['item_name'],
                    'qty'                           => $data['qty'],
                    'unit'                          => $data['unit'],
                    'price'                         => $penDetData->harga
                ];
                $updateData[] = [
                    'id'                => $data['id'],
                    'summarized_qty'    => $penDetData->summarized_qty + $data['qty']
                    // 'summarized_qty'    => $data['qty']
                ];
            }

            foreach ($postData['pengenaan_pajak'] as $data) {
                $taxData[] = [
                    'tax_inv_date'  => date("Y/m/d", strtotime(str_replace("/", "-", $data['taxInvDate']))),
                    'tax_inv_no'    => $data['taxInvNo'],
                    'tax_type'      => $data['taxType'],
                    'tax_amt'       => $data['taxAmt'],
                    'tax_status'    => $data['taxStatus'],
                    'tax_note'      => $data['taxNote']
                ];
            }

            // generate new inv number here
            $fakturNo = $this->generateInvNumber();
            
            // save here
            $tandaTerimaFakturModel->db->transException(true)->transStart();
            $tandaTerimaData = [
                'supplier_id'       => $postData['supplier_id'],
                'faktur_no'         => $fakturNo,
                'invoice_date'      => date("Y/m/d", strtotime(str_replace("/", "-", $postData['invoice_date']))),
                'receive_date'      => date("Y/m/d", strtotime(str_replace("/", "-", $postData['receive_date']))),
                'potongan'          => $postData['potongan'] ?: 0,
                'tambahan'          => $postData['tambahan'] ?: 0,
                // 'recipient'         => $postData['recipient'],
                // 'sender'            => $postData['sender'],
                'faktur_type'       => 'IMPORT',
                'information'       => $postData['information'],
                'tipe_bahan'        => $postData['tipe_bahan'],
                'user_id'           => $this->user_id
            ];
            $tandaTerimaData['nominal_faktur'] = $invAmt + $tandaTerimaData['tambahan'] - $tandaTerimaData['potongan'];
            
            $insertedId = $tandaTerimaFakturModel->insert($tandaTerimaData);

            foreach ($detData as &$data) {
                $data['tanda_terima_faktur_id'] = $insertedId;
            }

            $tandaTerimaFakturDetModel->insertBatch($detData);
            /* $penerimaanBarangDetModel->whereIn('id', $penerimaanDetIds)
                ->set('summarized_qty', "summarized_qty+{$}", false); */

            $tas = $penerimaanBarangDetModel->updateBatch($updateData, 'id');

            if (count($taxData)) {

                foreach ($taxData as &$data) {
                    $data['tanda_terima_faktur_id'] = $insertedId;
                }
    
                // insert tax here
                $pajakTandaTerimaFakturModel->insertBatch($taxData);
            }

            $tandaTerimaFakturModel->db->transComplete();

            $data = [
                "id"        => $insertedId,
                "status"    => true,
                "message"   => "Data Berhasil disimpan",
                // "payload"   => $payload,
                'token'     => csrf_hash(),
                'code'      => 200
            ];
            echo json_encode($data);
            return;
        }
        catch(\Exception $e)
        {
            $data = [
                "status"    => false,
                "message"   => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token'     => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function updateTerimaFakturImport()
    {
        try{
            $postData = $this->request->getPost();
            $postData["penerimaan_barang"] = json_decode($postData["penerimaan_barang"], true);
            $postData["pengenaan_pajak"] = json_decode($postData["pengenaan_pajak"], true);

            $tandaTerimaFakturModel = new TandaTerimaFakturModel();
            $tandaTerimaFakturDetModel = new TandaTerimaFakturDetailModel();
            $penerimaanBarangDetModel = new PenerimaanBarangDetailModel();
            $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();

            $rules = [
                "tipe_bahan" => [
                    "rules" => "required|in_list[BAKU,PENOLONG]"
                ],
                "supplier_id" => [
                    "rules" => "required"
                ],
                "invoice_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "receive_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "potongan" => [
                    "rules" => "permit_empty|is_natural"
                ],
                "tambahan" => [
                    "rules" => "permit_empty|is_natural"
                ],
                "penerimaan_barang.*.id" => [
                    "rules" => "required|is_natural_no_zero"
                ],
                "penerimaan_barang.*.no_po" => [
                    "rules" => "required"
                ],
                "penerimaan_barang.*.lpb_date" => [
                    "rules" => "required|valid_date[d/m/Y]"
                ],
                "penerimaan_barang.*.no_lpb" => [
                    "rules" => "required"
                ],
                "penerimaan_barang.*.item_name" => [
                    "rules" => "required"
                ],
                "penerimaan_barang.*.qty" => [
                    "rules" => "required|numeric"
                ],
                "penerimaan_barang.*.unit" => [
                    "rules" => "required"
                ],
                "pengenaan_pajak.*.taxInvDate" => [
                    "rules" => "permit_empty|valid_date[d/m/Y]"
                ],
                "pengenaan_pajak.*.taxInvNo" => [
                    "rules" => "permit_empty"
                ],
                "pengenaan_pajak.*.taxType" => [
                    "rules" => "permit_empty"
                ],
                "pengenaan_pajak.*.taxAmt" => [
                    "rules" => "permit_empty|numeric"
                ],
                "pengenaan_pajak.*.taxStatus" => [
                    "rules" => "permit_empty"
                ],
                "pengenaan_pajak.*.taxNote" => [
                    "rules" => "permit_empty"
                ]
            ];
    
            if (!$this->validateData($postData, $rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            $id = $postData["id"];
            $detData = [];
            $taxData = [];
            $invAmt = 0;

            $tandaTerimaFakturData = $tandaTerimaFakturModel->asObject()->find($id);

            if (empty($tandaTerimaFakturData)) {
                $data = [
                    "status"    => false,
                    "message"   => "Data Tidak Ditemukan",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }

            foreach ($postData['penerimaan_barang'] as $data) {

                $penDetData = $penerimaanBarangDetModel->asObject()
                    ->find($data['penerimaan_barang_detail_id']);
                $invAmt += $penDetData->harga * $data['qty'];

                $detData[] = [
                    'tanda_terima_faktur_id'        => $id,
                    'penerimaan_barang_detail_id'   => $data['penerimaan_barang_detail_id'],
                    'po_no'                         => $data['no_po'],
                    'lpb_date'                      => date("Y/m/d", strtotime(str_replace("/", "-", $data['lpb_date']))),
                    'lpb_no'                        => $data['no_lpb'],
                    'item_name'                     => $data['item_name'],
                    'qty'                           => $data['qty'],
                    'unit'                          => $data['unit'],
                    'price'                         => $penDetData->harga
                ];
            }

            foreach ($postData['pengenaan_pajak'] as $data) {
                $taxData[] = [
                    'tax_inv_date'  => date("Y/m/d", strtotime(str_replace("/", "-", $data['taxInvDate']))),
                    'tax_inv_no'    => $data['taxInvNo'],
                    'tax_type'      => $data['taxType'],
                    'tax_amt'       => $data['taxAmt'],
                    'tax_status'    => $data['taxStatus'],
                    'tax_note'      => $data['taxNote']
                ];
            }
            
            $tandaTerimaFakturModel->db->transStart();
            $tandaTerimaData = [
                'supplier_id'       => $postData['supplier_id'],
                'invoice_date'      => date("Y/m/d", strtotime(str_replace("/", "-", $postData['invoice_date']))),
                'receive_date'      => date("Y/m/d", strtotime(str_replace("/", "-", $postData['receive_date']))),
                'potongan'          => $postData['potongan'] ?? 0,
                'tambahan'          => $postData['tambahan'] ?? 0,
                'faktur_type'       => 'IMPORT',
                'information'       => $postData['information'],
                'tipe_bahan'        => $postData['tipe_bahan']
            ];
            $tandaTerimaData['nominal_faktur'] = $invAmt + $tandaTerimaData['tambahan'] - $tandaTerimaData['potongan'];
            $tandaTerimaFakturModel->update($id, $tandaTerimaData);

            // delete all detail and insert the new one
            $tandaTerimaFakturDetModel->where(['tanda_terima_faktur_id' => $id])->delete();

            $tandaTerimaFakturDetModel->insertBatch($detData);

            // delete all tax and insert the new one
            $pajakTandaTerimaFakturModel->where(['tanda_terima_faktur_id' => $id])->delete();

            if (count($taxData)) {

                foreach ($taxData as &$data) {
                    $data['tanda_terima_faktur_id'] = $id;
                }
    
                // insert tax here
                $pajakTandaTerimaFakturModel->insertBatch($taxData);
            }

            $tandaTerimaFakturModel->db->transComplete();
            
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function deleteTerimafakturImport()
    {
        try{
            $id = $this->request->getPost("id");

            if (!empty($id)) {
                $tandaTerimaFakturModel = new TandaTerimaFakturModel();

                $checkExist = $tandaTerimaFakturModel->find($id);

                if (empty($checkExist)) {
                    $data = [
                        "status"    => false,
                        "message"   => 'Data not Found',
                        'token'     => csrf_hash()
                    ];
                    echo json_encode($data);
                    return;
                }

                $tandaTerimaFakturModel->delete($id);

                $data = [
                    "status"    => true,
                    "message"   => "Data Berhasil dihapus",
                    'token'     => csrf_hash()
                ];
                echo json_encode($data);
                return;
            }
        }
        catch(\Exception $e)
        {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
                'token' => csrf_hash()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function print($id) 
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        $tandaTerimaFakturDetModel = new TandaTerimaFakturDetailModel();
        $pajakTandaTerimaFakturModel = new PajakTandaTerimaFakturModel();

        $filename = "PO Import Bahan Baku";

        $data = [];
        $itemsList = [];
        $taxList = [];
        $itemTotal = 0;
        $taxTotal = 0;
        $dataInv = $tandaTerimaFakturModel->asObject()
            ->select("tanda_terima_faktur.*, DATE_FORMAT(tanda_terima_faktur.invoice_date, '%d/%m/%Y') AS invoice_date, suppliers.name AS supplier_name")
            ->join('suppliers', 'suppliers.id = tanda_terima_faktur.supplier_id')
            ->find($id);

        $dataDet = $tandaTerimaFakturDetModel->asObject()
            ->where('tanda_terima_faktur_id', $id)
            ->findAll();

        $taxData = $pajakTandaTerimaFakturModel->asObject()
            ->where('tanda_terima_faktur_id', $id)
            ->findAll();

        foreach ($dataDet as $det) {
            $itemsList[] = "$det->qty $det->unit $det->item_name";
            $itemTotal += $det->qty * $det->price;
        }

        foreach ($taxData as $tax) {
            $taxList[] = "{$tax->tax_type}: {$tax->tax_inv_no}";
            $taxTotal += $tax->tax_amt;
        }

        $total = $itemTotal + $dataInv->tambahan + $taxTotal - $dataInv->potongan;

        $data["data"] = $dataInv;
        $data['invNo'] = $dataInv->faktur_no;
        $data["itemName"] = implode(', ', $itemsList);
        $data['taxList'] = implode(', ', $taxList);
        $data["itemTotal"] = $itemTotal;
        $data["potongan"] = $dataInv->potongan;
        $data["tambahan"] = $dataInv->tambahan;
        $data['total'] = $total;
        $data['taxTotal'] = $taxTotal;
        $data['terbilang'] = penyebut($total);

        // var_dump($dataPODetail);
        // die;
        // return view('Purchase/terimaFakturImport/print', $data);

        // load HTML content
        $this->dompdf->loadHtml(view('Purchase/terimaFakturImport/print', $data));

        // (optional) setup the paper size and orientation
        $this->dompdf->setPaper('A5', 'landscape');

        // render html as PDF
        $this->dompdf->render();

        // output the generated pdf
        $this->dompdf->stream($filename, array("Attachment" => false));

        exit(0);
    }

    private function generateInvNumber(): string
    {
        $tandaTerimaFakturModel = new TandaTerimaFakturModel();
        
        $month = idate('m');
        $year = date('y');
        $romanMonth = romanMonthNumber($month);
        $numberTemplate = "/TT/$romanMonth/$year";
        
        $lastData = $tandaTerimaFakturModel->asObject()
            ->like('faktur_no', $numberTemplate, 'before')
            ->orderBy('createdAt', 'DESC')
            ->first();

        $invNumber = '001' . $numberTemplate;

        if (!empty($lastData)) {
            $asd = explode('/', $lastData->faktur_no);
            $lastIncrement = intval($asd[0]) + 1;
            $paddedNumber = str_pad($lastIncrement, 3, 0, STR_PAD_LEFT);

            $invNumber = $paddedNumber . $numberTemplate;
        }

        return $invNumber;
    }
}
?>