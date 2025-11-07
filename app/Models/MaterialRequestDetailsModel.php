<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialRequestDetailsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'material_request_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getMaterialRequestDetailByMaterialRequestID($mrID)
    {
        $selectQry = '
            material_request_details.*,        
            material_request_details.qty_now,        
            barang_master.kode_barang,        
            barang_master.barang_name,        
            barang_master.type_barang,    
            satuans.id AS satuan_id,
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = material_request_details.barang1_id', 'left')
            ->join('satuans', 'satuans.kode_satuan = material_request_details.satuan', 'left')
            ->whereIn('material_request_details.material_request_id', $mrID)
            ->where('material_request_details.qty_now >', 0)
            ->where('material_request_details.deletedAt', null)
            ->where('barang_master.deletedAt', null)
            ->where('satuans.deletedAt', null)
            // ->groupBy('material_request_details.barang1_id, material_request_details.barang2_id')
            ->findAll();

        return $dataQry;
    }

    public function getListBarangWorkInProgres($condition, $addCondition, $limit = 10, $offset = 0)
    {
        $availableSort = [
            'material_request_details.barang_type' => 'material_request_details.barang_type',
            'barang_master.kode_barang' => 'barang_master.kode_barang',
            'barang_master.barang_name' => 'barang_master.barang_name',
            'material_request_details.satuan' => 'material_request_details.satuan',
            'material_request_details.qty' => 'material_request_details.qty',
            'material_request_details.note' => 'material_request_details.note',
        ];

        $availableSortType = ['asc' => 'ASC', 'desc' => 'DESC'];

        $sort = $availableSort[$addCondition['sort'] ?? 'createdAt'] ?? 'material_request_details.createdAt';
        $sortType = $availableSortType[$addCondition['sortType'] ?? 'desc'] ?? 'DESC';

        $selectQry = '
            barang_master.kode_barang,
            barang_master.barang_name,
            material_request_details.*,
            SUM(qty_now) as total_qty_now
        ';

        $dataQry = $this->asObject()
            ->select($selectQry)
            ->join('barang_master', 'barang_master.id = material_request_details.barang1_id', 'left')
            ->join('material_requests', 'material_requests.id = material_request_details.material_request_id', 'left')
            ->join('work_orders', 'work_orders.id = material_requests.work_order_id', 'left')
            ->join('production_results', 'production_results.work_order_id = work_orders.id', 'left')
            ->where($condition)
            ->groupBy('material_request_details.barang1_id')
            ->groupBy('work_orders.divisi_id')
            ->orderBy($sort, $sortType);

        $totalData = $dataQry->countAllResults(false);

        if ($addCondition['status_produksi'] != "ALL" ||  $addCondition['date_start'] != "" || $addCondition['date_end'] != "" || $addCondition['divisi_id'] || $addCondition['nama_barang'] != "" || $addCondition['kode_produksi'] != "") {
            $dataQry->groupStart();
        }

        if (isset($addCondition['date_start']) && $addCondition['date_start'] !== "") {
            $dataQry->where('production_results.receive_date >=', $addCondition['date_start']);
        }

        if (isset($addCondition['date_end']) && $addCondition['date_end'] !== "") {
            $dataQry->where('production_results.receive_date <=', $addCondition['date_end']);
        }

        if (isset($addCondition['divisi_id']) && $addCondition['divisi_id'] !== "") {
            $dataQry->where('work_orders.divisi_id', $addCondition['divisi_id']);
        }

        if (isset($addCondition['kode_produksi']) && $addCondition['kode_produksi'] !== "") {
            $dataQry->where('production_results.pr_no', $addCondition['kode_produksi']);
        }

        if (isset($addCondition['status_produksi']) && $addCondition['status_produksi'] != "ALL") {
            // MASIH AKTIF = BELUM DIPOSTING
            $dataQry->where('production_results.is_posted', 0);
        }

        if (isset($addCondition['nama_barang']) && $addCondition['nama_barang'] !== "") {
            $dataQry->like('barang_name', $addCondition['nama_barang'])->orLike('kode_barang', $addCondition['nama_barang']);
        }

        if ($addCondition['status_produksi'] != "ALL" ||  $addCondition['date_start'] != "" || $addCondition['date_end'] != "" || $addCondition['divisi_id'] || $addCondition['nama_barang'] != "" || $addCondition['kode_produksi'] != "") {
            $dataQry->groupEnd();
        }


        $totalFilteredData = $dataQry->countAllResults(false);
        $data = $dataQry->findAll($limit, $offset);

        return [
            'data'              => $data,
            'totalData'         => $totalData,
            'totalFilteredData' => $totalFilteredData
        ];
    }

    public function getMaterialRequestForRasio($where)
    {
        $where['deletedAt'] = null;
        $selectQryJadi = '
            material_requests.*,
            material_request_details.*,
        ';

        $dataQry = $this->asArray()
            ->select($selectQryJadi)
            ->join('material_requests', 'material_requests.id = material_request_details.material_request_id', 'left')
            ->join('account_barang', 'account_barang.barang_master_id = material_request_details.barang1_id', 'left')
            ->where('material_requests.request_date >=', $where['tanggal_awal'])
            ->where('material_requests.request_date <=', $where['tanggal_akhir'])
            ->where('material_requests.company_id', $where['company_id'])
            ->where('account_barang.company_id', $where['company_id'])
            ->where('material_request_details.divisi_tujuan_id', $where['divisi_id'])
            ->where('account_barang.divisi_id', $where['divisi_id'])
            ->where('account_barang.kategori_id', $where['kategori_id'])
            ->where('material_request_details.deletedAt', $where['deletedAt'])
            ->where('material_requests.deletedAt', $where['deletedAt'])
            ->groupBy('material_request_details.barang1_id, material_request_details.barang2_id')
            ->findAll();

        return $dataQry;
    }


    public function getMaterialRequestBahanBakuDetail($materialRequestId)
    {
        $stockDetail2Model = new StockDetail2Model();
        $stockModel = new StockModel();
        $barangMasterModel = new BarangMasterModel();
        $barangMasterSpesifikasiModel = new BarangMasterSpesifikasiModel();
        $satuanModel = new SatuansModel();
        $kemasanModel = new KemasanModel();
        $metaDataModel = new MetadataModel();
        $rmPurchaseOrderModel = new RMPurchaseOrderModel();
        $jasaVendorInModel = new JasaVendorInModel();

        $jasaVendorIn = null;
        $result = array();
        $materialRequestDetail = $this->asArray()
            ->select(
                '
                        material_request_details.*, 
                        warehouse_asal.warehouse_name as warehouse_asal_text, 
                        divisi_asal.divisi as divisi_asal_text,
                        warehouse_tujuan.warehouse_name as warehouse_tujuan_text,
                        divisi_tujuan.divisi as divisi_tujuan_text
                    '
            )
            ->join('divisis as divisi_asal', 'divisi_asal.id = material_request_details.divisi_id', 'left')
            ->join('divisis as divisi_tujuan', 'divisi_tujuan.id = material_request_details.divisi_tujuan_id', 'left')
            ->join('warehouses as warehouse_asal', 'warehouse_asal.id = material_request_details.warehouse_id', 'left')
            ->join('warehouses as warehouse_tujuan', 'warehouse_tujuan.id = material_request_details.warehouse_tujuan_id', 'left')
            ->where('barang_type', "bahan_baku")
            ->where('material_request_id', $materialRequestId)
            ->findAll();

        foreach ($materialRequestDetail as $m) {
            $stockList = $stockDetail2Model->getStockListDetail(
                $m['stock_id'],
                $m['bc_id'],
                $m['no_aju'],
                $m['stock_dokumen']
            );
            $stock = $stockModel->find($m['stock_id']);


            if ($stock['kemasan_id'] == 0) {
                $barangMaster = $barangMasterModel->find($stock['barang1_id']);
                $barangMasterSpesifikasi = $barangMasterSpesifikasiModel->find($stock['barang2_id']);
                $satuan = $barangMasterSpesifikasi == null ? null : $satuanModel->find($barangMasterSpesifikasi['satuan_1']);
                $barangName =  $barangMaster['barang_name'];
                $spesifikasi = $barangMasterSpesifikasi == null ? null : $barangMasterSpesifikasi['spesifikasi'];
            } else {
                $kemasan = $kemasanModel->find($stock['kemasan_id']);
                $satuan = $satuanModel->find($kemasan['satuan_id']);
                $barangName = $kemasan['name'];
                $spesifikasi = "";
            }

            $rmPurchaseOrder = $rmPurchaseOrderModel->where('po_no', $m['stock_dokumen'])->where('company_id', $stockList['company_id'])->first();

            $resultNoJasaVendorIn = strstr($m['stock_dokumen'], '(', true);
            $noJasaVendorIn = trim($resultNoJasaVendorIn);
            $supplierName = $stockList['supplier_name'];
            $stockDate = $rmPurchaseOrder == null ? "" :  date('d/m/Y', strtotime($rmPurchaseOrder['po_date']));

            $jasaVendorIn = $jasaVendorInModel
                ->select('jasa_vendor_in.*,vendors.name as nama_vendor')
                ->join('vendors', 'vendors.id = jasa_vendor_in.vendor_id', 'left')
                ->where('no_penerimaan_surat_jalan', $noJasaVendorIn)
                ->where('jasa_vendor_in.company_id',  session()->get("login")->this_company_id)
                ->first();


            $noDaftar = $stockDetail2Model->getNomorDaftar(
                $m['no_aju'],
                $m['bc_id']
            );

            $stockList['qty'] = $m['qty'];
            $bcType = $metaDataModel->find($stockList['bc_id']);
            $stockList['no_aju'] =  $stockList['no_aju'] == "-" ? "-" : $stockList['no_aju'];
            $stockList['bc_type'] = $bcType == null ? "NON PABEAN" : $bcType['value'];
            $stockList['satuan'] = $satuan == null ? '' : $satuan['kode_satuan'];
            $stockList['barang'] = $barangName;
            $stockList['stock_id'] = $stockList['stock_id'];
            $stockList['type_barang'] = $stock['tipe_barang'];
            $stockList['type_barang_text'] = strtoupper(str_replace('_', ' ', $stock['tipe_barang']));
            $stockList['stok_total'] = ($stockList['stok_total']);
            $stockList['stock_date'] = $jasaVendorIn == null ? $stockDate :  date('d/m/Y', strtotime($jasaVendorIn['tanggal']));
            $stockList['supplier_name'] = $jasaVendorIn == null ? $supplierName : $supplierName . ' / ' . $jasaVendorIn['nama_vendor'];

            // Tambahan
            $stockList['divisi_id'] = $m['divisi_id'];
            $stockList['divisi_tujuan_id'] = $m['divisi_tujuan_id'];
            $stockList['divisi_asal_text'] = $m['divisi_asal_text'];
            $stockList['divisi_tujuan_text'] = $m['divisi_tujuan_text'];
            $stockList['qty2'] = $m['qty2'];
            $stockList['qty_isi'] = $m['qty_isi'];
            $stockList['kode_satuan'] = $m['satuan'];
            $stockList['warehouse_id'] = $m['warehouse_id'];
            $stockList['warehouse_asal_text'] = $m['warehouse_asal_text'];
            $stockList['warehouse_tujuan_text'] = $m['warehouse_tujuan_text'];
            $stockList['warehouse_tujuan_id'] = $m['warehouse_tujuan_id'];
            $stockList['no_daftar'] = $noDaftar;
            $stockList['harga_umum'] = $m['harga_umum'];
            $stockList['harga_harian'] = $m['harga_harian'];
            $stockList['harga_bulanan'] = $m['harga_bulanan'];
            $stockList['spesifikasi'] = $spesifikasi;

            array_push($result, $stockList);
        }

        if ($jasaVendorIn == null) {
            // Untuk Stok Supplier
            $grouped = [];

            foreach ($result as $item) {
                $key = $item['stock_dokumen'] . '|' . $item['stock_date'];
                $spec = $item['spesifikasi'] ?? $item['sepsifikasi'] ?? '';

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'id' => [],
                        'bc_id' => $item['bc_id'],
                        'stock_dokumen' => $item['stock_dokumen'],
                        'sumber' => $item['sumber'],
                        'supplier_name' => $item['supplier_name'],
                        'stock_detail_id' => $item['stock_detail_id'],
                        'no_aju' => $item['no_aju'],
                        'stock_id' => $item['stock_id'],
                        'stok_total' => 0, // inisialisasi stok_total
                        'bc_type' => $item['bc_type'],
                        'satuan' => $item['satuan'],
                        'type_barang' => $item['type_barang'],
                        'type_barang_text' => $item['type_barang_text'],
                        'stock_date' => $item['stock_date'],
                        'divisi_id' => $item['divisi_id'],
                        'divisi_asal_text' => $item['divisi_asal_text'],
                        'divisi_tujuan_id' => $item['divisi_tujuan_id'],
                        'divisi_tujuan_text' => $item['divisi_tujuan_text'],
                        'qty2' => 0,
                        'qty_isi' => 0,
                        'kode_satuan' => $item['kode_satuan'],
                        'warehouse_id' => $item['warehouse_id'],
                        'warehouse_asal_text' => $item['warehouse_asal_text'],
                        'warehouse_tujuan_text' => $item['warehouse_tujuan_text'],
                        'warehouse_tujuan_id' => $item['warehouse_tujuan_id'],
                        'no_daftar' => $item['no_daftar'],
                        'supplier_name' => $item['supplier_name'],
                        'supplier_id' => $item['supplier_id'],
                        'harga_umum' => $item['harga_umum'],
                        'harga_harian' => $item['harga_harian'],
                        'harga_bulanan' => $item['harga_bulanan'],

                        'qty' => 0, // inisialisasi qty,
                        'barang' => $item['barang'],
                        'spesifikasi_list' => [],
                    ];
                }

                $grouped[$key]['qty'] += floatval($item['qty']);
                $grouped[$key]['qty2'] += floatval($item['qty2']);
                $grouped[$key]['qty_isi'] += floatval($item['qty_isi']);

                $grouped[$key]['stok_total'] += floatval($item['stok_total']);
                $grouped[$key]['id'][] = $item['stock_id'];
                $grouped[$key]['spesifikasi_list'][] = $spec;
            }

            $result = [];
            foreach ($grouped as  &$group) {
                $group['spesifikasi_list'] = array_unique($group['spesifikasi_list']);
                $group['barang'] = trim($group['barang'] . ' ' . implode(', ', $group['spesifikasi_list']));

                $group['id'] = encrypt2(json_encode($group['id']));
                $result[] = $group;
            }
        } else {
            // Untuk Stok Jasa Vendor
            $grouped = [];


            foreach ($result as $item) {
                $key = $item['stock_dokumen'] . '|' . $item['stock_date'];
                $spec = $item['spesifikasi'] ?? $item['sepsifikasi'] ?? '';

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'id' => [],
                        'bc_id' => $item['bc_id'],
                        'stock_dokumen' => $item['stock_dokumen'],
                        'sumber' => $item['sumber'],
                        'supplier_name' => $item['supplier_name'],
                        'stock_detail_id' => $item['stock_detail_id'],
                        'no_aju' => $item['no_aju'],
                        'stock_id' => $item['stock_id'],
                        'stok_total' => 0, // inisialisasi stok_total
                        'bc_type' => $item['bc_type'],
                        'satuan' => $item['satuan'],
                        'type_barang' => $item['type_barang'],
                        'type_barang_text' => $item['type_barang_text'],
                        'stock_date' => $item['stock_date'],
                        'divisi_id' => $item['divisi_id'],
                        'divisi_asal_text' => $item['divisi_asal_text'],
                        'divisi_tujuan_id' => $item['divisi_tujuan_id'],
                        'divisi_tujuan_text' => $item['divisi_tujuan_text'],
                        'qty2' => 0,
                        'qty_isi' => 0,
                        'kode_satuan' => $item['kode_satuan'],
                        'warehouse_id' => $item['warehouse_id'],
                        'warehouse_asal_text' => $item['warehouse_asal_text'],
                        'warehouse_tujuan_text' => $item['warehouse_tujuan_text'],
                        'warehouse_tujuan_id' => $item['warehouse_tujuan_id'],
                        'no_daftar' => $item['no_daftar'],
                        'supplier_name' => $item['supplier_name'],
                        'supplier_id' => $item['supplier_id'],
                        'harga_umum' => $item['harga_umum'],
                        'harga_harian' => $item['harga_harian'],
                        'harga_bulanan' => $item['harga_bulanan'],

                        'qty' => 0, // inisialisasi qty,
                        'barang' => $item['barang'],
                        'spesifikasi_list' => [],
                    ];
                }

                $grouped[$key]['qty'] += floatval($item['qty']);
                $grouped[$key]['qty2'] += floatval($item['qty2']);
                $grouped[$key]['qty_isi'] += floatval($item['qty_isi']);

                $grouped[$key]['stok_total'] += floatval($item['stok_total']);
                $grouped[$key]['id'][] = $item['stock_id'];
                $grouped[$key]['spesifikasi_list'][] = $spec;
            }


            $result = [];
            foreach ($grouped as  &$group) {
                $group['spesifikasi_list'] = array_unique($group['spesifikasi_list']);
                $group['barang'] = trim($group['barang'] . ' ' . implode(', ', $group['spesifikasi_list']));

                $group['id'] = encrypt2(json_encode($group['id']));
                $result[] = $group;
            }
        }


        return $result;
    }

    public function getMaterialRequestBahanBakuDetailNew($materialRequestId)
    {
        // Inisialisasi model
        $result = array();
        $materialRequestDetail = $this->asArray()
            ->select("
                    material_request_details.id, 
                    material_request_details.id AS id_material_request_detail, 
                    material_request_details.stock_id, 
                    material_request_details.stock_detail_id, 
                    suppliers.name as supplier_name,
                    material_request_details.bc_id, 
                    material_request_details.no_aju, 
                    material_request_details.stock_dokumen, 
                    penerimaan_barang.supplier_id,
                    material_request_details.harga_umum, 
                    material_request_details.harga_harian,
                    material_request_details.harga_bulanan,
                    material_request_details.stock_date,
                    material_request_details.qty, 
                    material_request_details.qty2,
                    material_request_details.qty_isi,
                    material_request_details.qty_now,
                    material_request_details.satuan,
                    material_request_details.divisi_id, 
                    material_request_details.divisi_tujuan_id,
                    material_request_details.warehouse_id,
                    material_request_details.warehouse_tujuan_id,
                    material_request_details.keterangan,
                    rm_purchase_orders.po_no,
                    barang_master.barang_name,
                    barang_master_spesifikasi.spesifikasi,
                    CONCAT(barang_master.barang_name, ' ', barang_master_spesifikasi.spesifikasi) AS barang,
                    warehouse_asal.warehouse_name as warehouse_asal_text, 
                    divisi_asal.divisi as divisi_asal_text,
                    warehouse_tujuan.warehouse_name as warehouse_tujuan_text,
                    divisi_tujuan.divisi as divisi_tujuan_text,
                ")
            ->join('barang_master', 'barang_master.id = material_request_details.barang1_id', 'left')
            ->join('barang_master_spesifikasi', 'barang_master_spesifikasi.id = material_request_details.barang2_id', 'left')
            ->join('divisis as divisi_asal', 'divisi_asal.id = material_request_details.divisi_id', 'left')
            ->join('divisis as divisi_tujuan', 'divisi_tujuan.id = material_request_details.divisi_tujuan_id', 'left')
            ->join('warehouses as warehouse_asal', 'warehouse_asal.id = material_request_details.warehouse_id', 'left')
            ->join('warehouses as warehouse_tujuan', 'warehouse_tujuan.id = material_request_details.warehouse_tujuan_id', 'left')
            ->join('stock_revamp_detail', 'stock_revamp_detail.id = material_request_details.stock_detail_id', 'left')
            ->join('penerimaan_barang', "penerimaan_barang.id = stock_revamp_detail.reference_id AND stock_revamp_detail.reference_type = 'LPB'", 'left')
            ->join('suppliers', "suppliers.id = penerimaan_barang.supplier_id AND stock_revamp_detail.reference_type = 'LPB'", 'left')
            ->join('rm_purchase_orders', "rm_purchase_orders.id = stock_revamp_detail.po_id AND stock_revamp_detail.reference_type = 'LPB'", 'left')
            ->where('barang_type', "bahan_baku")
            ->where('material_request_id', $materialRequestId)
            ->findAll();

        foreach ($materialRequestDetail as $m) {
            $stockList['stock_id'] = $m['stock_id'];
            $stockList['stock_detail_id'] = $m['stock_detail_id'];
            $stockList['id_material_request_detail'] = $m['id_material_request_detail'];
            $stockList['supplier_name'] = empty($m['supplier_name']) ? "-" : $m['supplier_name'];
            $stockList['id'] = encrypt($m['stock_detail_id']) . '-' . encrypt($m['id']);
            $stockList['bc_id'] =  empty($m['bc_id']) ? "-" : $m['bc_id'];
            $stockList['no_aju'] =  empty($m['no_aju']) ? "-" : $m['no_aju'];
            $stockList['stock_dokumen'] = empty($m['stock_dokumen']) ? '-' : $m['stock_dokumen'];
            $stockList['no_dokumen_2'] = empty($m['stock_dokumen']) ? '-' : $m['stock_dokumen'];
            $stockList['supplier_id'] = empty($m['supplier_id']) ? null : $m['supplier_id'];
            $stockList['harga_umum'] = empty($m['harga_umum']) ? "0" : $m['harga_umum'];
            $stockList['harga_harian'] = empty($m['harga_harian']) ? "0" : $m['harga_harian'];
            $stockList['harga_bulanan'] = empty($m['harga_bulanan']) ? "0" : $m['harga_bulanan'];
            $stockList['no_po'] = empty($m['po_no']) ? "-" : $m['po_no'];
            $stockList['barang_name'] = empty($m['barang_name']) ? "-" : $m['barang_name'];
            $stockList['spesifikasi'] = empty($m['spesifikasi']) ? "-" : $m['spesifikasi'];
            $stockList['kode_satuan'] = empty($m['satuan']) ? "-" : $m['satuan'];
            $stockList['stock_date'] = date('d/m/Y', strtotime($m['stock_date']));
            $stockList['bc_type'] = empty($m['type_bc']) ? "NON PABEAN" : $m['type_bc'];
            $stockList['no_daftar'] = empty($m['no_daftar']) ? "-" : $m['no_daftar'];
            $stockList['stok_total'] = floatval($m['qty']);
            $stockList['satuan'] = $m['satuan'];
            $stockList['barang'] = $m['barang'];
            $stockList['sepsifikasi'] = $m['spesifikasi'];
            $stockList['divisi_id'] = $m['divisi_id'];
            $stockList['divisi_asal_text'] = $m['divisi_asal_text'];
            $stockList['warehouse_id'] = $m['warehouse_id'];
            $stockList['warehouse_asal_text'] = $m['warehouse_asal_text'];
            $stockList['divisi_tujuan_id'] = $m['divisi_tujuan_id'];
            $stockList['divisi_tujuan_text'] = $m['divisi_tujuan_text'];
            $stockList['warehouse_tujuan_id'] = $m['warehouse_tujuan_id'];
            $stockList['warehouse_tujuan_text'] = $m['warehouse_tujuan_text'];
            $stockList['qty'] = $m['qty'];
            $stockList['qty2'] = $m['qty2'];
            $stockList['qty_isi'] = $m['qty_isi'];
            $stockList['qty_now'] = $m['qty_now'];
            $stockList['keterangan'] = $m['keterangan'];
            $stockList['type_barang'] = "bahan_baku";
            $stockList['type_barang_text'] = "BAHAN BAKU";
            $stockList['supplier_id'] = $m['supplier_id'];
            $stockList['stock_detail_id'] = $m['stock_detail_id'];
            $stockList['sumber'] = "LPB";

            $result[] = $stockList;
        }

        return $result;
    }

    public function getMaterialRequestNotApprove($stockId, $stockDetailId)
    {
        $selectQry = '
            SUM(material_request_details.qty) as qty
        ';

        $dataQry = $this->asArray()
            ->select($selectQry)
            ->join('material_requests', 'material_requests.id = material_request_details.material_request_id', 'left')
            ->where('material_request_details.stock_id', $stockId)
            ->where('material_request_details.stock_detail_id', $stockDetailId)
            ->where('material_request_details.deletedAt', null)
            ->where('material_requests.deletedAt', null)
            ->where('material_requests.is_approve', null)
            ->first();

        return $dataQry;
    }
}
