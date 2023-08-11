<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-import-bahan-penolong"); ?>">
                Batal
            </a>

            <?php if (!empty($dataPOImport)) { ?>

                <?php if ($dataPOImport->is_posted === "0") { ?>
                    <button class="btn btn-hapus delete-parent float-right">
                        Hapus
                    </button>
                <?php } ?>

                <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("po-import-bahan-penolong/print/"); ?><?= $dataPOImport->id; ?>')">
                    Print
                </button>

                <?php if ($dataPOImport->is_posted === "0") { ?>
                    <button class="btn btn-success posting-spp float-right">
                        Posting
                    </button>
                <?php } ?>

                <?php if ($dataPOImport->is_posted === "1") { 
                    if ($dataPOImport->status_penerimaan === "0") { ?>
                    <button class="btn btn-hapus close-parent float-right">
                        Close PO
                    </button>
                <?php } 
                } ?>

            <?php } ?>

            <?php if (!empty($dataPOImport)) {
                if ($dataPOImport->is_posted === "0") { ?>
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
                                    <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-control input-picker po_date" id="po_date" name="po_date" placeholder="Tanggal Dibuat" value="<?= !empty($dataPOImport) ? ($dataPOImport->po_date ? date("d/m/Y", strtotime($dataPOImport->po_date)) : "")  : ""; ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?php if (!empty($dataPOImport)) { ?>
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" value="<?= !empty($dataPOImport) ? $dataPOImport->spp_no : ""; ?>" readonly="true" class="form-control" placeholder="No. SPP">
                                <label for="floatingInput">No. SPP</label>
                            </div>
                        <?php } else { ?>
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select purchase_request_id" id="purchase_request_id" name="purchase_request_id" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataSPP)) {
                                        foreach ($dataSPP as $spp) {
                                    ?>
                                            <option value="<?= $spp["id"]; ?>"><?= $spp["spp_no"]; ?></option>
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
                                    <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'readonly=true' : '') : ''; ?> type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="No. PO" value="<?= !empty($dataPOImport) ? $dataPOImport->po_no : ""; ?>">
                                    <label for="floatingInput">No. PO</label>
                                </div>
                                <div style="<?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? "display: none" : "") : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
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
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Supplier</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSupplier)) {
                                    foreach ($dataSupplier as $supplier) {
                                ?>
                                        <option <?= !empty($dataPOImport) ? ($dataPOImport->supplier_id === $supplier["id"] ? "selected" : "") : ""; ?> value="<?= $supplier["id"]; ?>" data-name="<?= $supplier["name"]; ?>"><?= $supplier["kode"]; ?> - <?= $supplier["name"]; ?></option>
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
                            <select <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select currency" id="currency" name="currency" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataValuta)) {
                                    foreach ($dataValuta as $valuta) {
                                ?>
                                        <option <?= !empty($dataPOImport) ? (($dataPOImport->currency ? formatter($dataPOImport->currency, "STR_TO_INT") : 0) === formatter($valuta["id"], "STR_TO_INT") ? "selected" : "") : ""; ?> value="<?= $valuta["id"]; ?>"><?= $valuta["value"]; ?></option>
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
                            <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="text" value="<?= !empty($dataPOImport) ? $dataPOImport->payment_term : ""; ?>" class="form-control payment_term" name="payment_term" id="payment_term" placeholder="Termin Pembayaran / Bulan">
                            <label for="floatingInput">Termin Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" readonly="true" class="form-control" placeholder="Order Oleh" value="<?= !empty($dataPOImport) ? $dataPOImport->createdByName : session()->get("login")->name; ?>">
                            <label for="floatingInput">Order Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataPOImport) ? ($dataPOImport->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOImport) ? $dataPOImport->note : ""; ?>" type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                            <label for="floatingInput">Catatan (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Spesifikasi</th>
                                <th>Satuan</th>
                                <th>Harga Barang</th>
                                <th>Qty</th>
                                <th>Sisa Penerimaan</th>
                                <th>Jumlah Diterima</th>
                                <th>Total Harga</th>
                                <th>Disc %</th>
                                <th>Biaya Tambahan</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                            <?php
                            $no = 1;
                            $total_harga_barang = 0;
                            $total_qty = 0;
                            $total_harga = 0;
                            $total_qty_diterima = 0;
                            $total_remaining_qty = 0;
                            
                            if (!empty($dataPOImport)) {
                                foreach ($dataPOImportDetail as $details) {
                                    $total_harga_barang = $total_harga_barang + ($details["price"] ? formatter($details["price"], "CURR_TO_INT") : 0);
                                    $total_qty = $total_qty + ($details["qty"] ? formatter($details["qty"], "CURR_TO_INT") : 0);
                                    $total_qty_diterima = $total_qty_diterima + ($details["qty_diterima"] ? formatter($details["qty_diterima"], "STR_TO_INT") : 0);
                                    $total_remaining_qty = $total_remaining_qty + ($details["remaining_qty"] ? formatter($details["remaining_qty"], "STR_TO_INT") : 0);
                                    $total_harga = $total_harga + ($details["price"] && $details["qty"] ? (formatter($details["totalPrice"], "CURR_TO_INT")) : 0);
                            ?>
                                    <tr>
                                        <?php if ($dataPOImport->is_posted === "0") { ?>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $no; ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["kode_barang"]; ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["nama_barang"]; ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["spec"]; ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["nama_satuan"]; ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["price"]; ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= formatter($details["qty"], "CURR_TO_INT"); ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= formatter($details["remaining_qty"], "CURR_TO_INT"); ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= formatter($details["qty_diterima"], "CURR_TO_INT"); ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["totalPrice"]; ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= formatter($details["disc"], "STR_TO_INT"); ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["additional_cost"]; ?></td>
                                            <td class="edit-table-detail" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= $details["totalPrice"]; ?>" data-additional-cost="<?= $details["additional_cost"]; ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= $details["price"]; ?>" data-qty="<?= formatter($details["qty"], "CURR_TO_INT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["note"]; ?></td>
                                        <?php } else { ?>

                                            <td><?= $no; ?></td>
                                            <td><?= $details["kode_barang"]; ?></td>
                                            <td><?= $details["nama_barang"]; ?></td>
                                            <td><?= $details["spec"]; ?></td>
                                            <td><?= $details["nama_satuan"]; ?></td>
                                            <td><?= $details["price"]; ?></td>
                                            <td><?= formatter($details["qty"], "CURR_TO_INT"); ?></td>
                                            <td><?= formatter($details["remaining_qty"], "CURR_TO_INT"); ?></td>
                                            <td><?= formatter($details["qty_diterima"], "CURR_TO_INT"); ?></td>
                                            <td><?= $details["totalPrice"]; ?></td>
                                            <td><?= formatter($details["disc"], "STR_TO_INT"); ?></td>
                                            <td><?= $details["additional_cost"]; ?></td>
                                            <td><?= $details["note"]; ?></td>
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
                                <td><b><?= $total_remaining_qty; ?></b></td>
                                <td><b><?= $total_qty_diterima; ?></b></td>
                                <td><b><?= number_format($total_harga); ?></b></td>
                                <td colspan="3"></td>
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
                <button type="button" onclick="addBarang('<?= base_url("barang"); ?>')" class="btn btn-add-barang mr-3"><i class="fa fa-plus mr-3"></i>Barang</button>
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
                                <input readonly="true" type="text" class="form-control kode" name="kode" id="kode" placeholder="Kode Barang" />
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly="true" type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly="true" type="text" class="form-control spesifikasi" id="spesifikasi" name="spesifikasi" placeholder="Spesifikasi">
                                <label for="floatingInput">Spesifikasi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea readonly="true" class="form-control keterangan text-area-all" name="keterangan" id="keterangan" placeholder="Keterangan"></textarea>
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Harga</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly="true" type="text" class="form-control nama_satuan" name="nama_satuan" id="nama_satuan" placeholder="Satuan">
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly="true" type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly="true" onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
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
    var total_remaining_qty = 0;
    var total_qty_diterima = 0;
    var total_harga = 0;
    var priceEdit = 0;
    var totalPriceEdit = 0;

    <?php if (!empty($dataPOImportDetail)) {
        foreach ($dataPOImportDetail as $details) {
    ?>

            priceEdit = Number('<?= $details["price"] ? $details["price"] : 0; ?>'.replaceAll(",", ""));
            totalPriceEdit = Number('<?= $details["totalPrice"] ? $details["totalPrice"] : 0; ?>'.replaceAll(",", ""));
            row = row + 1;

            total_harga_barang = total_harga_barang + priceEdit;
            total_qty = total_qty + Number('<?= $details["qty"] ? $details["qty"] : 0; ?>'.replaceAll(",", ""));
            total_remaining_qty = total_remaining_qty + Number('<?= $details["remaining_qty"]; ?>');
            total_qty_diterima = total_qty_diterima + Number('<?= $details["qty_diterima"]; ?>');
            total_harga = total_harga + totalPriceEdit;

            list_items.push({
                id: <?= $details["id"]; ?>,
                row: row,
                barang_id: '<?= $details["barang_id"]; ?>',
                kode_barang: '<?= $details["kode_barang"]; ?>',
                nama_barang: '<?= $details["nama_barang"]; ?>',
                nama_satuan: '<?= $details["nama_satuan"]; ?>',
                satuan: <?= $details["id_satuan"]; ?>,
                spesifikasi: '<?= $details["spec"]; ?>',
                harga: '<?= $details["price"]; ?>',
                qty: Number('<?= $details["qty"] ? $details["qty"] : 0; ?>'.replaceAll(",", "")),
                total: '<?= $details["totalPrice"]; ?>',
                keterangan: '<?= $details["note"]; ?>',
                additional_cost: '<?= $details["additional_cost"]; ?>',
                disc: Number('<?= $details["disc"]; ?>'),
                remaining_qty: Number('<?= $details["remaining_qty"]; ?>'),
                qty_diterima: Number('<?= $details["qty_diterima"]; ?>'),
            })
        <?php
        }
        ?>
    <?php
    } ?>

    $(document).ready(function() {
        $(".po_date").datepicker({
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

        $('.icon-po-date').click(function() {
            $(".po_date").focus();
        });

        var validator = $(".create-form").validate({
            rules: {
                po_no: {
                    required: true
                },
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
            },
            messages: {
                po_no: {
                    required: "No. PO wajib diisi"
                },
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
                    required: "Termin Pembayaran wajib diisi"
                },
                currency: {
                    required: "Valas wajib diisi"
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

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".disc").keyup(function() {
            if ($(".disc").val()) {
                if ($(".disc").val() > 100) {
                    $(".disc").val(100)
                }
                if ($(".disc").val() < 0) {
                    $(".disc").val();
                }
            } else {
                $(".disc").val();
            }
        })

        $(".supplier_id").change(function() {
            if ($(".supplier_id option:selected").val()) {
                let name = $(".supplier_id option:selected").data("name") ? $(".supplier_id option:selected").data("name") : "";
                $(".supplier").val(name);
            } else {
                $(".supplier").val("");
            }
        })

        $(".purchase_request_id").change(function() {
            if ($(".purchase_request_id option:selected").val()) {
                $.ajax({
                    url: `<?= base_url("spp/ajax"); ?>`,
                    method: "GET",
                    data: {
                        id: $(".purchase_request_id option:selected").val()
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status) {
                            $(".warehouse_id").val(res?.data?.warehouse_id)
                            $(".warehouse").val(res?.data?.warehouseName)

                            let new_list_items = []
                            let tag_html = "";
                            let tag_total = "";

                            list_items = [];
                            total_harga_barang = 0;
                            total_qty = 0;
                            total_harga = 0;

                            row = 0;

                            $(".body-detail-table").empty()
                            console.log(res.detail)

                            res?.detail.map(item => {
                                tag_html += `<tr>`;
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.kodeBarang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.barangName;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.spec;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.satuanName;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.price;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.qty ? Number(item.qty.replaceAll(",", "")) : 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.totalPrice;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.satuanName}" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${Number(item.barang_id)}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${Number(item.unit)}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${Number(item.qty.replaceAll(",", ""))}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.note;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                list_items.push({
                                    id: "",
                                    row: row + 1,
                                    barang_id: item.barang_id ? Number(item.barang_id) : 0,
                                    kode_barang: item.kodeBarang,
                                    nama_barang: item.barangName,
                                    nama_satuan: item.satuanName,
                                    satuan: item.unit ? Number(item.unit) : 0,
                                    spesifikasi: item.spec,
                                    harga: item.price,
                                    disc: "",
                                    additional_cost: "",
                                    qty: item.qty ? Number(item.qty) : 0,
                                    total: item.totalPrice,
                                    keterangan: item.note,
                                    remaining_qty: 0,
                                    qty_diterima: 0
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
                            tag_total += `<b>${total_remaining_qty}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_qty_diterima}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='3'>";
                            tag_total += "</td>";
                            tag_total += "</tr>";

                            $(".foot-detail-table").append(tag_total);
                        } else {
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
                            tag_total += "<td>";
                            tag_total += `<b>0</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>0</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='3'>";
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
            } else {

            }
        })

        // close
        $(".close-parent").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan Close PO?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Close',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("po-import-bahan-penolong/close-po"); ?>",
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
                                title: 'Data Gagal Dihapus, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
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
                                title: 'Data Gagal Dihapus, coba Lagi',
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
            let additional_cost = $(".additional_cost").val()
            let disc = $(".disc").val()

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
                    let new_list_items = []
                    let tag_html = "";
                    let tag_total = "";

                    row = 0;

                    $(".body-detail-table").empty()

                    total_harga_barang = 0;
                    total_qty = 0;
                    total_harga = 0;

                    list_items.map(item => {
                        if (item.row == row_detail) {
                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.kode_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.spesifikasi;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.remaining_qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.qty_diterima;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.total;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += disc ? disc : 0;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += additional_cost ? additional_cost : 0;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${additional_cost}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.keterangan;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            new_list_items.push({
                                ...item,
                                additional_cost: additional_cost,
                                disc: disc
                            });

                            row = row + 1;
                        } else {
                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.kode_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.spesifikasi;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.remaining_qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.qty_diterima;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.total;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.disc ? item.disc : 0;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.additional_cost ? item.additional_cost : 0;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.keterangan;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            new_list_items.push(item);

                            row = row + 1;
                        }
                    })

                    list_items = [];

                    list_items = new_list_items;

                    $(".body-detail-table").append(tag_html)

                    $(".detail-modal").modal("hide")
                }
            })
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
                                title: 'Data Gagal Diubah, coba Lagi',
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
            if (list_items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            } else {
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
                            var total = 0;

                            let update_list_items = [];

                            if (list_delete.length !== 0) {
                                list_delete.map(obj => {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        item_code: obj.kode_barang,
                                        item_name: obj.nama_barang,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        unit: obj.satuan ? Number(obj.satuan) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        note: obj.keterangan,
                                        disc: obj.disc ? Number(obj.disc) : 0,
                                        additional_cost: obj.additional_cost ? Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                        spec: obj.spesifikasi,
                                        isDeleted: true
                                    })
                                })
                            }

                            list_items.map(obj => {
                                total = total + (obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0) * (obj.qty ? Number(obj.qty) : 0);
                                if (obj.id) {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        item_code: obj.kode_barang,
                                        item_name: obj.nama_barang,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        unit: obj.satuan ? Number(obj.satuan) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        note: obj.keterangan,
                                        disc: obj.disc ? Number(obj.disc) : 0,
                                        additional_cost: obj.additional_cost ? Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                        spec: obj.spesifikasi,
                                        isDeleted: false
                                    })
                                } else {
                                    update_list_items.push({
                                        id: "",
                                        item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        item_code: obj.kode_barang,
                                        item_name: obj.nama_barang,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        unit: obj.satuan ? Number(obj.satuan) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        note: obj.keterangan,
                                        disc: obj.disc ? Number(obj.disc) : 0,
                                        additional_cost: obj.additional_cost ? Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                        spec: obj.spesifikasi,
                                        isDeleted: false
                                    })
                                }
                            })

                            data.append("total", total)

                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if (id) {
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
                                            title: 'Data Gagal Diubah, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                            // CREATE
                            else {
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
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("po-import-bahan-penolong"); ?>" + "/id/" + +response.id;
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

    $(document).on('click', '.edit-table-detail', function(evt) {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');
        let additional_cost = $(this).data('additional_cost')
        let total = $(this).data('total')
        let disc = $(this).data('disc')

        let barang_id = $(this).data('barang_id')
        let kode_barang = $(this).data('kode_barang')
        let nama_barang = $(this).data('nama_barang')
        let nama_satuan = $(this).data('nama_satuan')
        let satuan = $(this).data('satuan')
        let spesifikasi = $(this).data('spesifikasi')
        let harga = $(this).data('harga')
        let qty = $(this).data('qty')
        let keterangan = $(this).data('keterangan')
        let rowid = $(this).data('row')
        let id = $(this).data('id')

        console.log(keterangan)

        $(".id_detail").val(rowid)
        $(".kode").val(kode_barang)
        $(".keterangan").val(keterangan)

        $(".spesifikasi").val(spesifikasi);

        $(".barang_id").val(barang_id)
        $(".nama_barang").val(nama_barang)
        $(".nama_satuan").val(nama_satuan)

        $(".harga").val(harga)
        $(".qty").val(qty)
        $(".total").val(total)

        $(".additional_cost").val(additional_cost)
        $(".disc").val(disc)

        $(".detail-modal").modal("show");
    })

    const addBarang = function(url) {
        window.open(url, "_blank");
    }
    
    const print = function(url) {
        window.open(url, "_blank");
    }

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".po_no").attr("readonly", true);
            $(".po_no").val("AUTO GENERATE");
        } else {
            $(".po_no").attr("readonly", false);
            $(".po_no").val("");
        }
    }
</script>
<?= $this->endSection(); ?>