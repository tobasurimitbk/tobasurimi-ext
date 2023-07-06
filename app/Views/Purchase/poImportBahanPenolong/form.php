<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">

    <?php if(!empty($dataPOImport)){ ?> 

    <?php if($dataPOImport->is_posted === false){ ?> 
    <button class="btn btn-hapus delete-parent float-right">
        Hapus
    </button>
    <?php } ?> 

    <button class="btn btn-warning btn-print float-right" onclick="print('<?= getenv('apiURL'); ?>/auxiliaryMaterialPO/import/print/<?= $dataPOImport->id ?>')">
        Print
    </button>

    <?php if($dataPOImport->is_posted === false){ ?> 
    <button class="btn btn-success posting-spp float-right">
        Posting
    </button>
    <?php } ?> 

    <?php } ?> 

    <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-import-bahan-penolong"); ?>">
        Batal
    </a>

    <?php if(!empty($dataPOImport)){ 
        if($dataPOImport->is_posted === false){ ?> 
    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
        Simpan
    </button>
    <?php }
    } else { ?> 
    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
        Simpan
    </button>
    <?php } ?> 
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mb-3">
                <label class="form-label font-weight-bold lable-title">Data PO</label>
            </div>
        </div>
        <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" class="id" name="id" id="id" value="<?= !empty($dataPOImport) ? $dataPOImport->id : ""; ?>" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? 'readonly=true' : '') : ''; ?> class="form-control input-picker po_date" id="po_date" name="po_date" placeholder="Tanggal Dibuat" value="<?= !empty($dataPOImport) ? $dataPOImport->po_date : ""; ?>">
                                <label for="floatingInput">Tanggal Dibuat</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                <?php if(!empty($dataPOImport)){ ?>
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= !empty($dataPOImport) ? $dataPOImport->spp_no : ""; ?>" readonly="true" class="form-control" placeholder="No. SPP (Opsional)">
                        <label for="floatingInput">No. SPP (Opsional)</label>
                    </div>
                    <?php } else { ?>
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select purchase_request_id" id="purchase_request_id" name="purchase_request_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSPP)) {
                                    foreach ($dataSPP as $spp) {
                                ?>
                                        <option value="<?= $spp->id; ?>"><?= $spp->spp_no; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">No. SPP</label>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? 'readonly=true' : '') : ''; ?> type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="No. PO" value="<?= !empty($dataPOImport) ? $dataPOImport->po_no : ""; ?>">
                                <label for="floatingInput">No. PO</label>
                            </div>
                            <div style="<?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? "display: none" : "") : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="hidden" value="<?= !empty($dataPOImport) ? $dataPOImport->warehouse_id : ""; ?>" class="form-control warehouse_id" id="warehouse_id" name="warehouse_id">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= !empty($dataPOImport) ? $dataPOImport->warehouseName : ""; ?>" readonly="true" class="form-control warehouse" id="warehouse" name="warehouse" placeholder="Gudang">
                        <label for="floatingInput">Gudang</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" readonly="true" class="form-control" placeholder="Order Oleh" value="<?= !empty($dataPOImport) ? $dataPOImport->createdBy : session()->get("login")->name; ?>">
                        <label for="floatingInput">Order Oleh</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Supplier</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataSupplier)) {
                                foreach ($dataSupplier as $supplier) {
                            ?>
                                    <option <?= !empty($dataPOImport) ? ($dataPOImport->supplier_id === $supplier->id ? "selected" : "") : ""; ?> value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>"><?= $supplier->kode; ?> - <?= $supplier->name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Kode Supplier</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= !empty($dataPOImport) ? $dataPOImport->supplierName : ""; ?>" readonly="true" class="form-control supplier" id="supplier" name="supplier" placeholder="Nama Supplier">
                        <label for="floatingInput">Nama Supplier</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pembayaran</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? 'disabled=true' : '') : ''; ?> class="form-select currency" id="currency" name="currency" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                            if (!empty($dataValuta)) {
                                foreach ($dataValuta as $valuta) {
                            ?>
                                    <option <?= !empty($dataPOImport) ? ($dataPOImport->currency === $valuta->value ? "selected" : "") : ""; ?> value="<?= $valuta->id; ?>"><?= $valuta->value; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Valas</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? 'disabled=true' : '') : ''; ?>  type="text" value="<?= !empty($dataPOImport) ? $dataPOImport->payment_term : ""; ?>" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control payment_term" name="payment_term" id="payment_term" placeholder="Termin Pembayaran / Bulan">
                        <label for="floatingInput">Termin Pembayaran / Bulan</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? 'disabled=true' : '') : ''; ?>  value="<?= !empty($dataPOImport) ? $dataPOImport->payment_date : ""; ?>" class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Pembayaran">
                                <label for="floatingInput">Tanggal Pembayaran</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-payment-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? 'disabled=true' : '') : ''; ?>  value="<?= !empty($dataPOImport) ? $dataPOImport->dpp : ""; ?>" onkeyup="formatNumber(this)" type="text" class="form-control dpp" id="dpp" name="dpp" placeholder="DPP">
                        <label for="floatingInput">DPP</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === true ? 'disabled=true' : '') : ''; ?>  value="<?= !empty($dataPOImport) ? $dataPOImport->note : ""; ?>" type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                        <label for="floatingInput">Catatan (Opsional)</label>
                    </div>
                </div>
            </div>
        </form>
        <div class="col-subtitle-modal">
            <div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                </div>
                <div class="col-md-6">
                    <?php if(!empty($dataPOImport)){ 
                        if($dataPOImport->is_posted === false){ ?> 
                    <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                    </button>
                    <?php }
                    } else { ?> 
                    <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                    </button>
                    <?php } ?> 
                </div>
            </div>
        </div>
        <div class="row">
        <div class="table-responsive">
            <table class="table nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Spesifikasi</th>
                        <th>Satuan</th>
                        <th>Harga Barang</th>
                        <th>Qty</th>
                        <th>Total Harga</th>
                        <th>Disc %</th>
                        <th>Biaya Tambahan</th>
                        <th>Keterangan</th>
                        <th>Hapus</th>
                    </tr>
                </thead>
                <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                <?php 
                    $no = 1;
                    $total_harga_barang = 0;
                    $total_qty = 0;
                    $total_harga = 0;
                    if(!empty($dataPOImport)){ 
                    foreach($dataPOImport->am_purchase_order_details as $details){  
                        $total_harga_barang = $total_harga_barang + formatter(str_replace(",", "", $details->price), "STR_TO_INT");
                        $total_qty = $total_qty + $details->qty;
                        $total_harga = $total_harga + formatter(str_replace(",", "", $details->totalPrice), "STR_TO_INT");
                ?> 

<tr>
                            <?php if($dataPOImport->is_posted === false){ ?> 
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $no; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->kodeBarang; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->barangName; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->spec; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->satuanName; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->price; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->qty; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->totalPrice; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->disc; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->additional_cost; ?></td>
                                <td class="edit-table-detail"  data-total="<?= $details->totalPrice; ?>" data-additional_cost="<?= $details->additional_cost; ?>" data-disc="<?= $details->disc; ?>"  data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-satuan="<?= $details->unit; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->note; ?></td>
                                <td><button onclick='deleteRow("<?= $no; ?>")'>X</button></td>

                            <?php } else { ?>

                                    <td><?= $no; ?></td>
                                    <td><?= $details->kodeBarang; ?></td>
                                    <td><?= $details->barangName; ?></td>
                                    <td><?= $details->satuanName; ?></td>
                                    <td><?= $details->spec; ?></td>
                                    <td><?= $details->price; ?></td>
                                    <td><?= $details->qty; ?></td>
                                    <td><?= $details->totalPrice; ?></td>
                                    <td><?= $details->disc; ?></td>
                                    <td><?= $details->additional_cost; ?></td>
                                    <td><?= $details->note; ?></td>
                                    <td></td> 

                            <?php } ?>
                        
                        </tr>
                <?php 
                    $no++;
                    }
                } ?> 
                </tbody>
                <tfoot class="foot-detail-table" id="foot-detail-table">
                    <tr>
                        <td colspan="4"></td>
                        <td><b>TOTAL</b></td>
                        <td><b><?= number_format($total_harga_barang); ?></b></td>
                        <td><b><?= $total_qty; ?></b></td>
                        <td><b><?= number_format($total_harga); ?></b></td>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        </div>
    </div>
</div>
</section>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <input type="hidden" class="barang_id" name="barang_id" id="barang_id" />
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Barang</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="hidden" class="kode" name="kode" id="kode" />
                                <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                    <option data-barang_id= "" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select spesifikasi" name="spesifikasi" id="spesifikasi" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Spesitifikasi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Harga</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan" name="satuan" id="satuan" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control disc" name="disc" id="disc" placeholder="Diskon %">
                                <label for="floatingInput">Diskon %</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control additional_cost" name="additional_cost" id="additional_cost" placeholder="Biaya Tambahan">
                                <label for="floatingInput">Biaya Tambahan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <button type="button" class="btn btn-discard delete-detail">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let list_delete = [];
    var row = 0;
    var total_harga_barang = 0;
    var total_qty = 0;
    var total_harga = 0;
    var priceEdit = 0;
    var totalPriceEdit = 0;

    <?php if(!empty($dataPOImport)){ 
        foreach($dataPOImport->am_purchase_order_details as $details){  
    ?>

    priceEdit = Number('<?= $details->price; ?>'.replaceAll(",", ""));
    totalPriceEdit = Number('<?= $details->totalPrice; ?>'.replaceAll(",", ""));
    row = row + 1;

    total_harga_barang = total_harga_barang + priceEdit;
    total_qty = total_qty + <?= $details->qty; ?>;
    total_harga = total_harga + totalPriceEdit;

    list_items.push({
        id: <?= $details->id; ?>,
        row: row,
        barang_id: '<?= $details->barang_id; ?>',
        kode_barang: '<?= $details->kodeBarang; ?>',
        nama_barang: '<?= $details->barangName; ?>',
        nama_satuan: '<?= $details->satuanName; ?>',
        satuan: <?= $details->unit; ?>,
        spesifikasi: '<?= $details->spec; ?>',
        harga: '<?= $details->price; ?>',
        qty: <?= $details->qty; ?>,
        total: '<?= $details->totalPrice; ?>',
        keterangan: '<?= $details->note; ?>',
        additional_cost: '<?= $details->additional_cost; ?>',
        disc: '<?= $details->disc; ?>',
    })
    <?php 
        }
    ?>
    <?php
    } ?>

    var validator_detail = $(".detail-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            nama_barang: {
                required: true
            },
            qty: {
                required: true
            },
            satuan: {
                required: true
            },
            harga: {
                required: true
            }
        },
        messages: {
            kode_barang: {
                required: "Kode wajib diisi"
            },
            nama_barang: {
                required: "Nama wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            satuan: {
                required: "Satuan wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    $(document).ready(function() {
        $(".po_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".payment_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // PURCHASE REQUEST ID
        $('.purchase_request_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.purchase_request_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.purchase_request_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.purchase_request_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SPESIFIKASI
        $('.spesifikasi').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.spesifikasi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.spesifikasi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.spesifikasi')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SUPPLIER
        $('.supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.supplier_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.supplier_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // FOREIGN EXHANGE
        $('.currency').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.currency')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.currency')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.currency')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KODE BARANG
        $('.kode_barang').select2({
            placeholder: "Pilih Kode Barang / Buat Baru",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            tags: true,
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SATUAN
        $('.satuan').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.satuan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.icon-po-date').click(function() {
            $(".po_date").focus();
        });

        $('.icon-payment-date').click(function() {
            $(".payment_date").focus();
        });

        var validator = $(".create-form").validate({
            rules: {
                purchase_request_id: {
                    required: true
                },
                po_date: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                payment_term: {
                    required: true,
                },
                currency: {
                    required: true,
                },
                payment_date: {
                    required: true,
                },
                dpp: {
                    required: true,
                }
            },
            messages: {
                purchase_request_id: {
                    required: "No. SPP wajib diisi"
                },
                po_date: {
                    required: "Tanggal Dibuat wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                payment_term: {
                    required: "Termin Pembayaran / Bulan wajib diisi"
                },
                currency: {
                    required: "Valas wajib diisi"
                },
                payment_date: {
                    required: "Tanggal Pembayaran wajib diisi"
                },
                dpp: {
                    required: "DPP wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent(); 
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');                      

            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');   
            },
        });

        $(".btn-show-detail").click(function() {
            $(".delete-detail").css('display', 'none');

            $(".title-detail-name").text("Tambah");
            $(".id_detail").val('');
            
            $(".kode").val('')
            $(".nama_barang").val('')
            $(".qty").val('')
            $(".satuan").val('')
            $(".spesifikasi").val('').change()
            $(".harga").val('')
            $(".total").val('')
            $(".keterangan").val('')

            $(".disc").val('')
            $(".additional_cost").val('')

            validator_detail.resetForm();
            validator_detail.reset();

            $.ajax({
                url: `<?= base_url("barang/dropdown/kategori"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    kategori: "bahan-penolong"
                },
                success: function(res) {
                    $(".kode_barang").empty();

                    $(".kode_barang").append(`<option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".kode_barang").append(`<option data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    })

                    $(".kode_barang").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("satuan/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".satuan").empty();

                    $(".satuan").append(`<option value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".satuan").append(`<option value="${item.id}">${item.nama_satuan}</option>`);
                    })

                    $(".satuan").val("").change();
                    $(".detail-modal").modal("show");
                }
            })
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".disc").keyup(function() {
            if($(".disc").val())
            {
                if($(".disc").val() > 100)
                {
                    $(".disc").val(100)
                }
                if($(".disc").val() < 0)
                {
                    $(".disc").val();
                }
            }
            else
            {
                $(".disc").val();
            }
        })

        $(".supplier_id").change(function() {
            if($(".supplier_id option:selected").val())
            {
                let name = $(".supplier_id option:selected").data("name") ? $(".supplier_id option:selected").data("name") : "";
                $(".supplier").val(name);
            }
            else
            {
                $(".supplier").val("");
            }
        })

        $(".purchase_request_id").change(function() {
            if($(".purchase_request_id option:selected").val())
            {
                $.ajax({
                    url: `<?= base_url("spp/ajax"); ?>`,
                    method: "GET",
                    data: {
                        id: $(".purchase_request_id option:selected").val()
                    },
                    dataType: "json",
                    success: function(res) {
                        if(res.status)
                        {
                            $(".warehouse_id").val(res?.data?.warehouse_id)
                            $(".warehouse").val(res?.data?.warehouseName)

                            let new_list_items = []
                            let tag_html = "";
                            let tag_total = "";

                            row = 0;

                            $(".body-detail-table").empty()

                            res?.data?.purchase_request_details.map(item => {
                                tag_html += `<tr>`;
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.kodeBarang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.barangName;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.spec;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.satuanName;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.price;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.qty;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.totalPrice;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan"${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.note;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                list_items.push({
                                    id: "",
                                    row: row + 1,
                                    barang_id: item.barang_id,
                                    kode_barang: item.kodeBarang,
                                    nama_barang: item.barangName,
                                    nama_satuan: item.satuanName,
                                    satuan: item.satuan_id,
                                    spesifikasi: item.spec,
                                    harga: item.price,
                                    disc: "",
                                    additional_cost: "",
                                    qty: item.qty,
                                    total: item.totalPrice,
                                    keterangan: item.note
                                });

                                row = row + 1;

                                total_harga_barang = total_harga_barang + Number(item.price.replaceAll(",", ""));
                                total_qty = total_qty + Number(item.qty);
                                total_harga = total_harga + Number(item.totalPrice.replaceAll(",", ""));
                            })

                            $(".body-detail-table").append(tag_html)

                            $(".foot-detail-table").empty()

                            tag_total += `<tr>`;
                            tag_total += "<td colspan='4'>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += "<b>TOTAL</b>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_qty}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='6'>";
                            tag_total += "</td>";
                            tag_total += "</tr>";

                            $(".foot-detail-table").append(tag_total);
                        }
                        else
                        {
                            $(".warehouse_id").val()
                            $(".warehouse").val()

                            list_items = []

                            row = 0;

                            let tag_total = "";

                            $(".body-detail-table").empty()

                            $(".foot-detail-table").empty()

                            tag_total += `<tr>`;
                            tag_total += "<td colspan='4'>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += "<b>TOTAL</b>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>0</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>0</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>0</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='6'>";
                            tag_total += "</td>";
                            tag_total += "</tr>";

                            $(".foot-detail-table").append(tag_total);

                            Swal.fire({
                                icon: 'error',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    }
                })
            }
            else
            {

            }
        })

        $(".kode_barang").change(function() {
            if($(".kode_barang option:selected").val())
            {
                let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
                let satuan = $(".kode_barang option:selected").data("satuan") ? $(".kode_barang option:selected").data("satuan") : "";
                let stok = $(".kode_barang option:selected").data("stok") ? $(".kode_barang option:selected").data("stok") : "";
                let harga = $(".kode_barang option:selected").data("harga") ? $(".kode_barang option:selected").data("harga") : "";
                let barang_id = $(".kode_barang option:selected").data("barang_id") ? $(".kode_barang option:selected").data("barang_id") : "";

                $(".nama_barang").attr("readonly", nama ? true : false);

                $.ajax({
                    url: "<?= base_url("barang/id"); ?>" + "/" + barang_id,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        $(".spesifikasi").empty()
                        $(".spesifikasi").append(`<option value=""></option>`)
                        res.data.spek.forEach(function(item) {
                            $(".spesifikasi").append(`<option value="${item}">${item}</option>`)
                        })

                        $(".spesifikasi").val("").change();
                    }
                })

                $(".kode").val($(".kode_barang option:selected").val());
                $(".nama_barang").val(nama);
                $(".barang_id").val(barang_id);
                $(".satuan").val(satuan).change();
                $(".qty").val(stok);
                $(".harga").val(harga ? harga.toLocaleString() : "");
                $(".total").val(harga || stok ? (Number(harga.replaceAll(",", "")) * stok).toLocaleString() : "");
            }
            else
            {
                $(".spesifikasi").empty()
                $(".spesifikasi").val("").change()
                $(".nama_barang").attr("readonly", false)
                $(".kode").val("");
                $(".nama_barang").val("");
                $(".barang_id").val("");
                $(".satuan").val("").change();
                $(".qty").val("");
                $(".harga").val("");
                $(".total").val("");
            }
        })

        // delete
        $(".delete-parent").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("po-import-bahan-penolong/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        window.location.href = "<?= base_url("po-import-bahan-penolong"); ?>"
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val() ? Number($(".id_detail").val()) : 0;
            let barang_id = $(".barang_id").val()
            let kode_barang = $(".kode").val()
            let nama_barang = $(".nama_barang").val()
            let nama_satuan = $(".satuan option:selected").text()
            let satuan = $(".satuan option:selected").val()
            let spesifikasi = $(".spesifikasi option:selected").val() ? $(".spesifikasi option:selected").val() : ""
            let keterangan = $(".keterangan").val()
            let harga = $(".harga").val()
            let qty = $(".qty").val()
            let additional_cost = $(".additional_cost").val()
            let disc = $(".disc").val()
            let total = $(".total").val()

            let validate_same = false;

            list_items.map(item => {
                if(barang_id !== '')
                {
                    if(item.barang_id == barang_id)
                    {
                        // kalau edit barang, barang tidak ganti tidak kena validasi
                        if(row_detail === item.row)
                        {
                            validate_same = false;
                        }
                        else
                        {
                            validate_same = true;
                        }
                    }
                }
            })

            if(validate_same)
            {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Sudah Ada",
                    confirmButtonColor: '#4e73df',
                })
            }
            else
            {
                // update detail
            if(row_detail)
            {
                if ($(".detail-form").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            console.log(id)
                            let new_list_items = []
                            let tag_html = "";
                            let tag_total = "";

                            row = 0;

                            $(".body-detail-table").empty()

                            total_harga_barang = 0;
                            total_qty = 0;
                            total_harga = 0;

                            list_items.map(item => {
                                if(item.row == row_detail)
                                {
                                    tag_html += `<tr>`;
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += kode_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += nama_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += spesifikasi;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += harga;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += qty;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += total;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += disc ? disc : 0;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += additional_cost ? additional_cost : 0;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += keterangan;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    new_list_items.push({
                                        id: item.id,
                                        row: row + 1,
                                        barang_id: barang_id,
                                        kode_barang: kode_barang,
                                        nama_barang: nama_barang,
                                        nama_satuan: nama_satuan,
                                        satuan: satuan,
                                        spesifikasi: spesifikasi,
                                        harga: harga,
                                        qty: qty,
                                        total: total,
                                        additional_cost: additional_cost,
                                        disc: disc,
                                        keterangan: keterangan
                                    });

                                    row = row + 1;

                                    total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                    total_qty = total_qty + Number(qty.replaceAll(",", ""));
                                    total_harga = total_harga + Number(total.replaceAll(",", ""));
                                }
                                else
                                {
                                    tag_html += `<tr>`;
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.kode_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.nama_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.spesifikasi;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.harga;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.qty;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.total;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.disc ? item.disc : 0;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.additional_cost ? item.additional_cost : 0;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                    tag_html += item.keterangan;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    new_list_items.push(item);

                                    row = row + 1;

                                    total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                                    total_qty = total_qty + Number(item.qty);
                                    total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                                }
                            })

                            list_items = [];

                            list_items = new_list_items;

                            $(".body-detail-table").append(tag_html)

                            $(".foot-detail-table").empty()

                            tag_total += `<tr>`;
                            tag_total += "<td colspan='4'>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += "<b>TOTAL</b>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_qty}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='6'>";
                            tag_total += "</td>";
                            tag_total += "</tr>";

                            $(".foot-detail-table").append(tag_total);

                            $(".detail-modal").modal("hide")
                        }
                    })
                }
            }
            // create detail
            else
            {
                if ($(".detail-form").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            list_items.push({
                                id: '',
                                row: row + 1,
                                barang_id: barang_id,
                                kode_barang: kode_barang,
                                nama_barang: nama_barang,
                                nama_satuan: nama_satuan,
                                satuan: satuan,
                                spesifikasi: spesifikasi,
                                harga: harga,
                                qty: qty,
                                disc: disc,
                                additional_cost: additional_cost,
                                total: total,
                                keterangan: keterangan
                            })

                            total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                            total_qty = total_qty + Number(qty.replaceAll(",", ""));
                            total_harga = total_harga + Number(total.replaceAll(",", ""));

                            let tag_html = "";
                            let tag_total = "";

                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += kode_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += spesifikasi;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += total;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += disc ? disc : 0;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += additional_cost ? additional_cost : 0;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail"  data-total="${total}" data-additional_cost="${additional_cost}" data-disc="${disc}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan"${keterangan}" data-id="" data-row="${row + 1}">`;
                            tag_html += keterangan;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";
                            $(".body-detail-table").append(tag_html)

                            $(".foot-detail-table").empty()

                            tag_total += `<tr>`;
                            tag_total += "<td colspan='4'>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += "<b>TOTAL</b>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_qty}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='6'>";
                            tag_total += "</td>";
                            tag_total += "</tr>";

                            $(".foot-detail-table").append(tag_total);

                            $(".detail-modal").modal("hide")
                            row = row + 1;
                        }
                    })
                }
            }   
            }
        })

        $(".harga, .qty").keyup(function () {
            let harga = $(".harga").val() ? $(".harga").val().replaceAll(",", "") : 0;
            let qty = $(".qty").val() ? parseInt($(".qty").val()) : 0;

            let total = (harga * qty).toLocaleString();
            $(".total").val(total);
        })

        $(".posting-spp").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di Posting?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Posting',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("po-import-bahan-penolong/update-status"); ?>",
                        data: {
                            id: $(".id").val()
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url("po-import-bahan-penolong"); ?>" + "/id/" + $(".id").val()
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })

        })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG
            if(list_items.length === 0)
            {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            }
            else
            {
                if ($(".create-form").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));

                            let update_list_items = [];

                            if(list_delete.length !== 0)
                            {
                                list_delete.map(obj => {
                                    update_list_items.push(
                                        {
                                            id: obj.id ? Number(obj.id) : 0,
                                            item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            item_code: obj.kode_barang,
                                            item_name: obj.nama_barang,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            unit: obj.satuan ? Number(obj.satuan) : 0,
                                            price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            note: obj.keterangan,
                                            disc: obj.disc ? Number(obj.disc) : 0,
                                            additional_cost: obj.additional_cost ?  Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                            spec: obj.spesifikasi,
                                            isDeleted: true
                                        }
                                    )
                                })
                            }
                            
                            list_items.map(obj => {
                                if (obj.id) {
                                    update_list_items.push(
                                        {
                                            id: obj.id ? Number(obj.id) : 0,
                                            item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            item_code: obj.kode_barang,
                                            item_name: obj.nama_barang,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            unit: obj.satuan ? Number(obj.satuan) : 0,
                                            price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            note: obj.keterangan,
                                            disc: obj.disc ? Number(obj.disc) : 0,
                                            additional_cost: obj.additional_cost ?  Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                            spec: obj.spesifikasi
                                        }
                                    )
                                }
                                else
                                {
                                    update_list_items.push(
                                        {
                                            item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            item_code: obj.kode_barang,
                                            item_name: obj.nama_barang,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            unit: obj.satuan ? Number(obj.satuan) : 0,
                                            price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            note: obj.keterangan,
                                            disc: obj.disc ? Number(obj.disc) : 0,
                                            additional_cost: obj.additional_cost ?  Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                            spec: obj.spesifikasi
                                        }
                                    )
                                }
                            })

                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if(id)
                            {
                                $.ajax({
                                    url: "<?= base_url("po-import-bahan-penolong/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            stopLoading()
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                window.location.href = "<?= base_url("po-import-bahan-penolong"); ?>" + "/id/" + id;
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                            // CREATE
                            else
                            {
                                $.ajax({
                                    url: "<?= base_url("po-import-bahan-penolong/save"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            stopLoading()
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                window.location.href = "<?= base_url("po-import-bahan-penolong"); ?>" + "/id/" + + response.id;
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                        }
                    })
                }
            }
        })
    })

    const deleteRow = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                row = 0;

                console.log(list_items)

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;

                list_items.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.nama_satuan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.total;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.disc ? item.disc : 0;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.additional_cost ? item.additional_cost : 0;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({...item, row: row + 1});

                        row = row + 1;

                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                    }
                    else
                    {
                        // sent parameter isDelete if have customer id and id
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".foot-detail-table").empty()

                tag_total += `<tr>`;
                tag_total += "<td colspan='4'>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += "<b>TOTAL</b>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td colspan='6'>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    }

    $(document).on('click', '.edit-table-detail', function(evt) {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');
        let additional_cost = $(this).data('additional_cost')
        let total = $(this).data('total')
        let disc = $(this).data('disc')

        let barang_id = $(this).data('barang_id')
        let kode_barang = $(this).data('kode_barang')
        let nama_barang = $(this).data('nama_barang')
        let satuan = $(this).data('satuan')
        let spesifikasi = $(this).data('spesifikasi')
        let harga = $(this).data('harga')
        let qty = $(this).data('qty')
        let keterangan = $(this).data('keterangan')
        let rowid = $(this).data('row')
        let id = $(this).data('id')

        validator_detail.resetForm();
        validator_detail.reset();

        $(".id_detail").val(rowid)
        $(".kode").val(kode_barang)
        $(".keterangan").val(keterangan)

        $.ajax({
            url: "<?= base_url("barang/id"); ?>" + "/" + barang_id,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".spesifikasi").empty()
                $(".spesifikasi").append(`<option value=""></option>`)
                res.data.spek.forEach(function(item) {
                    $(".spesifikasi").append(`<option value="${item}">${item}</option>`)
                })

                $(".spesifikasi").val(spesifikasi).change();
            }
        })

        $.ajax({
            url: `<?= base_url("barang/dropdown/kategori"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                kategori: "bahan-penolong"
            },
            success: function(res) {
                $(".kode_barang").empty();

                $(".kode_barang").append(`<option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>`);

                if(barang_id === "")
                {
                    $(".kode_barang").append(`<option selected data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value="${kode_barang}">${kode_barang}</option>`);
                }

                res.data.forEach(function(item) {
                    if(kode_barang === item.kode_barang)
                    {
                        $(".kode_barang").append(`<option selected data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    }   
                    else
                    {
                        $(".kode_barang").append(`<option data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    }
                })
            }
        })

        $(".barang_id").val(barang_id)
        $(".nama_barang").val(nama_barang)

        if(barang_id === "")
        {
            $(".nama_barang").attr("readonly", false);
        }
        else
        {
            $(".nama_barang").attr("readonly", true);
        }

        $.ajax({
            url: `<?= base_url("satuan/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".satuan").empty();

                $(".satuan").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".satuan").append(`<option value="${item.id}">${item.nama_satuan}</option>`);
                })

                $(".satuan").val(satuan).change();
                $(".harga").val(harga)
                $(".qty").val(qty)
                $(".total").val(total)

                $(".additional_cost").val(additional_cost)
                $(".disc").val(disc)

                $(".detail-modal").modal("show");
            }
        })
    })

    $(document).on('click', '.delete-detail', function() {
        let id = $(".id_detail").val()
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;

                row = 0;

                console.log(list_items)

                list_items.map(item => {
                    if(item.row != id)
                    {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.nama_satuan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.total;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.disc ? item.disc : 0;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.additional_cost ? item.additional_cost : 0;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan"${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({...item, row: row + 1});

                        row = row + 1;

                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                    }
                    else
                    {
                        // sent parameter isDelete if have customer id and id
                        if(item.id)
                        {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".foot-detail-table").empty()

                tag_total += `<tr>`;
                tag_total += "<td colspan='4'>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += "<b>TOTAL</b>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td colspan='6'>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    })

    const print = function(url) 
    {
        window.open(url, "_blank");
    }

    const changeStatus = function()
    {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if(value)
        {
            $(".po_no").attr("readonly", true);
            $(".po_no").val("AUTO GENERATE");
        }
        else
        {
            $(".po_no").attr("readonly", false);
            $(".po_no").val("");
        }
    }
</script>
<?= $this->endSection(); ?>