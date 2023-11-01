<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah PO Lokal Bahan Penolong</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-lokal-bahan-penolong"); ?>">
                Batal
            </a>
            <?php if (!empty($dataPOLokal)) { ?>

                <?php if ($dataPOLokal->is_posted === "0") { ?>
                    <button class="btn btn-hapus delete-parent float-right">
                        Hapus
                    </button>
                <?php } ?>

                <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("po-lokal-bahan-penolong/print/"); ?><?= $dataPOLokal->id ?>')">
                    Print
                </button>

                <?php if ($dataPOLokal->is_posted === "0") { ?>
                    <button class="btn btn-success posting-spp float-right posting-po">
                        Posting
                    </button>
                <?php } ?>

                <?php if ($dataPOLokal->is_posted === "1") { 
                    if ($dataPOLokal->status_penerimaan === "0") { ?>
                    <button class="btn btn-hapus close-parent float-right">
                        Close PO
                    </button>
                <?php } 
                } ?>

            <?php } ?>

            <?php if (!empty($dataPOLokal)) {
                if ($dataPOLokal->is_posted === "0") { ?>
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
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($dataPOLokal) ? $dataPOLokal->id : ""; ?>" />
                <input autocomplete="one-time-code" type="hidden" class="spp" name="spp" id="spp" value="<?= !empty($dataPOLokal) ? $dataPOLokal->purchase_request_id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-control input-picker po_date" id="po_date" name="po_date" placeholder="Tanggal Dibuat" value="<?= !empty($dataPOLokal) ? ($dataPOLokal->po_date ? date("d/m/Y", strtotime($dataPOLokal->po_date)) : "") : $today; ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <?php if (!empty($dataPOLokal)) { ?>
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" value="<?= !empty($dataPOLokal) ? $dataPOLokal->spp_no : ""; ?>" readonly="true" class="form-control" placeholder="No. SPP">
                                <label for="floatingInput">No. SPP</label>
                            </div>
                        <?php } else { ?>
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select purchase_request_id" id="purchase_request_id" name="purchase_request_id" aria-label="Floating label select example">
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
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="No. PO" value="<?= !empty($dataPOLokal) ? $dataPOLokal->po_no : ""; ?>">
                                    <label for="floatingInput">No. PO</label>
                                </div>
                                <div style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none" : "") : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" readonly type="text" value="<?= !empty($dataPOLokal) ? $dataPOLokal->companyName : ""; ?>" class="form-control company" placeholder="Company">
                            <label for="floatingInput">Company</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input autocomplete="one-time-code" type="hidden" value="<?= !empty($dataPOLokal) ? $dataPOLokal->divisi_id : ""; ?>" class="form-control divisi_id" id="divisi_id" name="divisi_id">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" readonly type="text" value="<?= !empty($dataPOLokal) ? $dataPOLokal->divisiName : ""; ?>" class="form-control divisi" id="divisi" name="divisi" placeholder="Divisi">
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSupplier)) {
                                    foreach ($dataSupplier as $supplier) {
                                ?>
                                        <option <?= !empty($dataPOLokal) ? ($dataPOLokal->supplier_id === $supplier->id ? "selected" : "") : ""; ?> value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>"><?= $supplier->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" readonly="true" class="form-control" placeholder="Order Oleh" value="<?= !empty($dataPOLokal) ? $dataPOLokal->createdByName : session()->get("login")->name; ?>">
                            <label for="floatingInput">Order Oleh</label>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select bc_type" id="bc_type" name="bc_type" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataBCType)) {
                                    foreach ($dataBCType as $bc) {
                                ?>
                                        <option <?= !empty($dataPOLokal) ? ($dataPOLokal->bc_type === $bc["id"] ? "selected" : "") : ""; ?> value="<?= $bc["id"]; ?>"><?= $bc["value"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Jenis Dokumen (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOLokal) ? ($dataPOLokal->po_date ? date("d/m/Y", strtotime($dataPOLokal->payment_date)) : $today) : $today; ?>" class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Pembayaran">
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-payment-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataPOLokal) ? $dataPOLokal->note : ""; ?>" type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
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
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <!-- <th>Kode</th> -->
                                <th>Nama</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>QTY</th>
                                <!-- <th>Sisa Penerimaan</th>
                                <th>Jumlah Diterima</th> -->
                                <th>Total</th>
                                <th>Disc (%)</th>
                                <th>Tambahan</th>
                                <th>Keterangan</th>
                                <!-- <th>PPN</th>
                                <th>PPH</th> -->
                                <th>Action</th>
                                <!-- <th>Keterangan</th> -->
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
                            
                            if (!empty($dataPOLokal)) {
                                foreach ($dataPOLokal->am_purchase_order_details as $details) {
                                    $total_harga_barang = $total_harga_barang + ($details->price ? formatter(str_replace(",", "", $details->price), "STR_TO_FLOAT") : 0);
                                    $total_qty = $total_qty + $details->qty;
                                    $total_qty_diterima = $total_qty_diterima + ($details->qty_diterima ? formatter($details->qty_diterima, "STR_TO_FLOAT") : 0);
                                    $total_remaining_qty = $total_remaining_qty + ($details->remaining_qty ? formatter($details->remaining_qty, "STR_TO_FLOAT") : 0);
                                    $total_harga = $total_harga + ($details->totalPriceWithoutAdditional ? formatter(str_replace(",", "", $details->totalPriceWithoutAdditional), "STR_TO_FLOAT") : 0);
                            ?>

                                    <tr>

                                        <?php if ($dataPOLokal->is_posted === "0") { ?>
                                            <td><?= $no; ?></td>
                                            <!-- <td><?= $details->kode_barang; ?></td> -->
                                            <td><?= $details->nama_barang; ?></td>
                                            <td><?= $details->nama_satuan; ?></td>
                                            <td><?= "Rp " . number_format(formatter($details->price, "STR_TO_FLOAT"), 2, '.', ','); ?></td>
                                            <td><?= formatter($details->qty, "STR_TO_FLOAT"); ?></td>
                                            <!-- <td><?= formatter($details->remaining_qty, "STR_TO_FLOAT"); ?></td>
                                            <td><?= formatter($details->qty_diterima, "STR_TO_FLOAT"); ?></td> -->
                                            <td><?= "Rp " . number_format(formatter($details->totalPriceWithoutAdditional, "STR_TO_FLOAT"), 2, '.', ','); ?></td>
                                            <td><?= formatter($details->disc, "STR_TO_INT"); ?></td>
                                            <td><?= "Rp " . number_format(formatter($details->additional_cost, "STR_TO_FLOAT"), 2, '.', ','); ?></td>
                                            <!-- <td><?= $details->ppn; ?></td>
                                            <td><?= $details->pph; ?></td> -->
                                            <td><?= $details->note; ?></td>
                                            <td>
                                                <button data-nama_satuan="<?= $details->nama_satuan; ?>" data-ppn="<?= $details->ppn; ?>" data-pph="<?= $details->pph; ?>" data-total="<?= number_format(formatter($details->totalPriceWithoutAdditional, "STR_TO_FLOAT"), 2, '.', ','); ?>" data-additional_cost="<?= number_format(formatter($details->additional_cost, "STR_TO_FLOAT"), 2, '.', ','); ?>" data-disc="<?= formatter($details->disc, "STR_TO_INT"); ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kode_barang; ?>" data-nama_barang="<?= $details->nama_barang; ?>" data-satuan="<?= $details->unit; ?>" data-harga="<?= number_format(formatter($details->price, "STR_TO_FLOAT"), 2, '.', ','); ?>" data-qty="<?= formatter($details->qty, "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>" class="edit-table-detail btn btn-warning posting-spp">
                                                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                                </button>
                                            </td>

                                        <?php } else { ?>
                                            <td><?= $no; ?></td>
                                            <!-- <td><?= $details->kode_barang; ?></td> -->
                                            <td><?= $details->nama_barang; ?></td>
                                            <td><?= $details->nama_satuan; ?></td>
                                            <td><?= "Rp " . number_format(formatter($details->price, "STR_TO_FLOAT"), 2, '.', ','); ?></td>
                                            <td><?= formatter($details->qty, "STR_TO_FLOAT"); ?></td>
                                            <!-- <td><?= formatter($details->remaining_qty, "STR_TO_FLOAT"); ?></td>
                                            <td><?= formatter($details->qty_diterima, "STR_TO_FLOAT"); ?></td> -->
                                            <td><?= "Rp " . number_format(formatter($details->totalPriceWithoutAdditional, "STR_TO_FLOAT"), 2, '.', ','); ?></td>
                                            <td><?= formatter($details->disc, "STR_TO_INT"); ?></td>
                                            <td><?= "Rp " . number_format(formatter($details->additional_cost, "STR_TO_FLOAT"), 2, '.', ','); ?></td>
                                            <!-- <td><?= $details->ppn; ?></td>
                                            <td><?= $details->pph; ?></td> -->
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
                                <td colspan="2"></td>
                                <td><b>TOTAL</b></td>
                                <td><b><?= "Rp " . number_format(formatter($total_harga_barang, "STR_TO_FLOAT"), 2, '.', ','); ?></b></td>
                                <td><b><?= $total_qty; ?></b></td>
                                <!-- <td><b><?= $total_remaining_qty; ?></b></td>
                                <td><b><?= $total_qty_diterima; ?></b></td> -->
                                <td><b><?= "Rp " . number_format(formatter($total_harga, "STR_TO_FLOAT"), 2, '.', ','); ?></b></td>
                                <td colspan="4"></td>
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
                <!-- <button type="button" onclick="addBarang('<?= base_url("barang"); ?>')" class="btn btn-add-barang mr-3"><i class="fa fa-plus mr-3"></i>Barang</button> -->
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <input autocomplete="one-time-code" type="hidden" class="barang_id" name="barang_id" id="barang_id" />
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Barang</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" type="text" class="form-control kode" name="kode" id="kode" placeholder="Kode Barang" />
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <textarea autocomplete="one-time-code" readonly class="form-control keterangan text-area-all" name="keterangan" id="keterangan" placeholder="Keterangan (Opsional)"></textarea>
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
                                <input autocomplete="one-time-code" readonly="true" type="text" class="form-control nama_satuan" name="nama_satuan" id="nama_satuan" placeholder="Satuan">
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">QTY</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" type="number" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control disc" name="disc" id="disc" placeholder="Diskon (%) (Opsional)">
                                <label for="floatingInput">Discount (%) (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control additional_cost" name="additional_cost" id="additional_cost" placeholder="Biaya Tambahan">
                                <label for="floatingInput">Biaya Tambahan (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Tax</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select ppn" name="ppn" id="ppn" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">PPN (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pph" name="pph" id="pph" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">PPH (Opsional)</label>
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
    var row = 0;
    var total_harga_barang = 0;
    var total_qty = 0;
    var total_remaining_qty = 0;
    var total_qty_diterima = 0;
    var total_harga = 0;
    var priceEdit = 0;
    var totalPriceEdit = 0;

    <?php if (!empty($dataPOLokal)) {
        foreach ($dataPOLokal->am_purchase_order_details as $details) {
    ?>

            priceEdit = Number('<?= $details->price; ?>'.replaceAll(",", ""));
            totalPriceEdit = Number('<?= $details->totalPriceWithoutAdditional; ?>'.replaceAll(",", ""));
            row = row + 1;

            total_harga_barang = total_harga_barang + priceEdit;
            total_qty = total_qty + <?= $details->qty; ?>;
            total_remaining_qty = total_remaining_qty + Number('<?= $details->remaining_qty; ?>');
            total_qty_diterima = total_qty_diterima + Number('<?= $details->qty_diterima; ?>');
            total_harga = total_harga + totalPriceEdit;

            list_items.push({
                id: <?= $details->id; ?>,
                purchase_request_detail_id: <?= $details->purchase_request_detail_id; ?>,
                row: row,
                barang_id: '<?= $details->barang_id; ?>',
                kode_barang: '<?= $details->kode_barang; ?>',
                nama_barang: '<?= $details->nama_barang; ?>',
                nama_satuan: '<?= $details->nama_satuan; ?>',
                satuan: <?= $details->unit; ?>,
                harga: '<?= number_format(formatter($details->price, "STR_TO_FLOAT"), 2, '.', ','); ?>',
                qty: Number('<?= $details->qty; ?>'),
                total: '<?= number_format(formatter($details->totalPriceWithoutAdditional, "STR_TO_FLOAT"), 2, '.', ','); ?>',
                keterangan: '<?= $details->note; ?>',
                additional_cost: '<?= number_format(formatter($details->additional_cost, "STR_TO_FLOAT"), 2, '.', ','); ?>',
                disc: Number('<?= $details->disc; ?>'),
                ppn: '<?= $details->ppn; ?>',
                pph: '<?= $details->pph; ?>',
                remaining_qty: Number('<?= $details->remaining_qty; ?>'),
                qty_diterima: Number('<?= $details->qty_diterima; ?>'),
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

        // SUPPLIER
        $('.supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        // BC
        $('.bc_type').select2({
            placeholder: "",
            theme: "bootstrap-5",
            allowClear: true
        })

        // FOREIGN EXHANGE
        $('.currency').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        // PPN
        $('.ppn').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        // PPH
        $('.pph').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.form-select')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.form-select')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.form-select')
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
                currency: {
                    required: true,
                },
                payment_date: {
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
                payment_date: {
                    required: "Tanggal Pembayaran wajib diisi"
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

        // $(".supplier_id").change(function() {
        //     if ($(".supplier_id option:selected").val()) {
        //         let name = $(".supplier_id option:selected").data("name") ? $(".supplier_id option:selected").data("name") : "";
        //         $(".supplier").val(name);
        //     } else {
        //         $(".supplier").val("");
        //     }
        // })

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
                            $(".divisi_id").val(res?.data?.divisi_id)
                            // $(".note").val(res?.data?.note)
                            $(".divisi").val(res?.data?.divisiName)
                            $(".company").val(res?.data?.companyName)

                            let new_list_items = []
                            let tag_html = "";
                            let tag_total = "";

                            row = 0;
                            list_items = [];
                            total_harga_barang = 0;
                            total_qty = 0;
                            total_harga = 0;

                            $(".body-detail-table").empty()

                            res?.detail.map(item => {
                                tag_html += `<tr>`;
                                tag_html += `<td>`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                // tag_html += `<td>`;
                                // tag_html += item.kodeBarang;
                                // tag_html += "</td>";
                                tag_html += `<td>`;
                                tag_html += item.barangName;
                                tag_html += "</td>";
                                tag_html += `<td>`;
                                tag_html += item.satuanName;
                                tag_html += "</td>";
                                tag_html += `<td>Rp `;
                                tag_html += Number(item.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                tag_html += "</td>";
                                tag_html += `<td>`;
                                tag_html += Number(item.qty);
                                tag_html += "</td>";
                                // tag_html += `<td>`;
                                // tag_html += 0;
                                // tag_html += "</td>";
                                // tag_html += `<td>`;
                                // tag_html += 0;
                                // tag_html += "</td>";
                                tag_html += `<td>Rp `;
                                tag_html += Number(item.totalPrice).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                tag_html += "</td>";
                                tag_html += `<td>`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td>`;
                                tag_html += "Rp 0.00";
                                tag_html += "</td>";
                                // tag_html += `<td>`;
                                // tag_html += 0;
                                // tag_html += "</td>";
                                // tag_html += `<td>`;
                                // tag_html += 0;
                                // tag_html += "</td>";
                                tag_html += `<td>`;
                                tag_html += item.note;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `
                                <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-nama_satuan="${item.satuanName}" data-ppn="" data-pph="" data-total="${Number(item.totalPrice).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-harga="${Number(item.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-qty="${Number(item.qty)}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">
                                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                </button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                list_items.push({
                                    id: "",
                                    purchase_request_detail_id: item.id ? Number(item.id) : 0,
                                    row: row + 1,
                                    barang_id: item.barang_id,
                                    kode_barang: item.kodeBarang,
                                    nama_barang: item.barangName,
                                    nama_satuan: item.satuanName,
                                    satuan: item.unit,
                                    harga: Number(item.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                    disc: "",
                                    additional_cost: "",
                                    qty: Number(item.qty),
                                    total: Number(item.totalPrice).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                    ppn: "",
                                    nilai_ppn: "",
                                    pph: "",
                                    nilai_pph: "",
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
                            tag_total += "<td colspan='2'>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += "<b>TOTAL</b>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>Rp ${total_harga_barang.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>${total_qty}</b>`;
                            tag_total += "</td>";
                            // tag_total += "<td>";
                            // tag_total += `<b>${total_remaining_qty}</b>`;
                            // tag_total += "</td>";
                            // tag_total += "<td>";
                            // tag_total += `<b>${total_qty_diterima}</b>`;
                            // tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>Rp ${total_harga.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='4'>";
                            tag_total += "</td>";
                            tag_total += "</tr>";

                            $(".foot-detail-table").append(tag_total);
                        } else {
                            $(".divisi_id").val('')
                            $(".divisi").val('')
                            $(".company").val('')
                            // $(".note").val('')

                            list_items = []

                            row = 0;

                            let tag_total = "";

                            $(".body-detail-table").empty()

                            $(".foot-detail-table").empty()

                            tag_total += `<tr>`;
                            tag_total += "<td colspan='2'>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += "<b>TOTAL</b>";
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>Rp 0.00</b>`;
                            tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>0</b>`;
                            tag_total += "</td>";
                            // tag_total += "<td>";
                            // tag_total += `<b>0</b>`;
                            // tag_total += "</td>";
                            // tag_total += "<td>";
                            // tag_total += `<b>0</b>`;
                            // tag_total += "</td>";
                            tag_total += "<td>";
                            tag_total += `<b>Rp 0.00</b>`;
                            tag_total += "</td>";
                            tag_total += "<td colspan='4'>";
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
                        url: "<?= base_url("po-lokal-bahan-penolong/close-po"); ?>",
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
                                        window.location.href = "<?= base_url("po-lokal-bahan-penolong"); ?>";
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
                        url: "<?= base_url("po-lokal-bahan-penolong/delete"); ?>",
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
                                        window.location.href = "<?= base_url("po-lokal-bahan-penolong"); ?>"
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
            let ppn = $(".ppn option:selected").val()
            let pph = $(".pph option:selected").val()
            let nilai_ppn = $(".ppn option:selected").text()
            let nilai_pph = $(".pph option:selected").text()
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
                            tag_html += `<td>`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += item.kode_barang;
                            // tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += "Rp" + item.harga;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.qty;
                            tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += item.remaining_qty;
                            // tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += item.qty_diterima;
                            // tag_html += "</td>";
                            tag_html += `<td>Rp `;
                            tag_html += item.total;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += disc ? disc : 0;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += additional_cost ? ("Rp " + Number(additional_cost).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })) : "Rp 0.00";
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.keterangan;
                            tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += nilai_ppn ? nilai_ppn : 0;
                            // tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += nilai_pph ? nilai_pph : 0;
                            // tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `
                            <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-nama_satuan="${item.nama_satuan}" data-ppn="${ppn}" data-pph="${pph}" data-total="${item.total}" data-additional_cost="${Number(additional_cost).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-disc="${disc}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${Number(item.qty)}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            new_list_items.push({
                                ...item,
                                additional_cost: Number(additional_cost).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                disc: disc,
                                ppn: ppn,
                                pph: pph,
                                nilai_ppn: nilai_ppn,
                                nilai_pph: nilai_pph,
                            });

                            row = row + 1;
                        } else {
                            tag_html += `<tr>`;
                            tag_html += `<td>`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += item.kode_barang;
                            // tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += "Rp" + item.harga;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.qty;
                            tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += item.remaining_qty;
                            // tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += item.qty_diterima;
                            // tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += "Rp" + item.total;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.disc ? item.disc : 0;
                            tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.additional_cost ? ("Rp " + item.additional_cost) : "Rp 0.00";
                            tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += item.nilai_ppn ? item.nilai_ppn : 0;
                            // tag_html += "</td>";
                            // tag_html += `<td>`;
                            // tag_html += item.nilai_pph ? item.nilai_pph : 0;
                            // tag_html += "</td>";
                            tag_html += `<td>`;
                            tag_html += item.keterangan;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `
                            <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-nama_satuan="${item.nama_satuan} data-ppn="${item.ppn}" data-pph="${item.pph}" data-total="${item.total}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${Number(item.qty)}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>`;
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

        $(".posting-po").click(function() {
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
                    let spp = $(".spp").val();
                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-penolong/update-status"); ?>",
                        data: {
                            id: $(".id").val(),
                            spp: spp
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
                                        window.location.href = "<?= base_url("po-lokal-bahan-penolong"); ?>";
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

                            let update_list_items = [];

                            list_items.map(obj => {
                                if (obj.id) {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        purchase_request_detail_id: obj.purchase_request_detail_id ? Number(obj.purchase_request_detail_id) : 0,
                                        item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        item_code: obj.kode_barang,
                                        item_name: obj.nama_barang,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        unit: obj.satuan ? Number(obj.satuan) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        note: obj.keterangan,
                                        disc: obj.disc ? Number(obj.disc) : 0,
                                        additional_cost: obj.additional_cost ? Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                        ppn: obj.ppn ? Number(obj.ppn) : 0,
                                        pph: obj.pph ? Number(obj.pph) : 0
                                    })
                                } else {
                                    update_list_items.push({
                                        item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        purchase_request_detail_id: obj.purchase_request_detail_id ? Number(obj.purchase_request_detail_id) : 0,
                                        item_code: obj.kode_barang,
                                        item_name: obj.nama_barang,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        unit: obj.satuan ? Number(obj.satuan) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        note: obj.keterangan,
                                        disc: obj.disc ? Number(obj.disc) : 0,
                                        additional_cost: obj.additional_cost ? Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                        ppn: obj.ppn ? Number(obj.ppn) : 0,
                                        pph: obj.pph ? Number(obj.pph) : 0
                                    })
                                }
                            })

                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if (id) {
                                $.ajax({
                                    url: "<?= base_url("po-lokal-bahan-penolong/update"); ?>",
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
                                                    window.location.href = "<?= base_url("po-lokal-bahan-penolong"); ?>";
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
                                    url: "<?= base_url("po-lokal-bahan-penolong/save"); ?>",
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
                                                    window.location.href = "<?= base_url("po-lokal-bahan-penolong"); ?>";
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
        let ppn = $(this).data('ppn')
        let pph = $(this).data('pph')
        let additional_cost = $(this).data('additional_cost')
        let total = $(this).data('total')
        let disc = $(this).data('disc')

        let barang_id = $(this).data('barang_id')
        let kode_barang = $(this).data('kode_barang')
        let nama_barang = $(this).data('nama_barang')
        let nama_satuan = $(this).data('nama_satuan')
        let satuan = $(this).data('satuan')
        let harga = $(this).data('harga')
        let qty = $(this).data('qty')
        let keterangan = $(this).data('keterangan')
        // let keterangan = $(this).data('keterangan')
        let rowid = $(this).data('row')
        let id = $(this).data('id')

        $(".id_detail").val(rowid)
        $(".kode").val(kode_barang)
        $(".keterangan").val(keterangan)
        // $(".keterangan").val(keterangan)

        $.ajax({
            url: `<?= base_url("tax/dropdown"); ?>`,
            method: "GET",
            data: {
                type: 'ppn'
            },
            dataType: "json",
            success: function(res) {
                $(".ppn").empty()
                $(".ppn").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".ppn").append(`<option value="${item.id}">${item.tax_value}</option>`)
                })

                $(".ppn").val(ppn).change();
            }
        })

        $.ajax({
            url: `<?= base_url("tax/dropdown"); ?>`,
            method: "GET",
            data: {
                type: 'pph'
            },
            dataType: "json",
            success: function(res) {
                $(".pph").empty()
                $(".pph").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".pph").append(`<option value="${item.id}">${item.tax_value}</option>`)
                })

                $(".pph").val(pph).change();
                $(".barang_id").val(barang_id)
                $(".nama_barang").val(nama_barang)
                $(".nama_satuan").val(nama_satuan)

                $(".harga").val(harga.replaceAll(",", ""))
                $(".qty").val(qty)
                $(".total").val(total.replaceAll(",", ""))

                $(".ppn").val(ppn)
                $(".pph").val(pph)
                $(".additional_cost").val(additional_cost.replaceAll(",", ""))
                $(".disc").val(disc)

                $(".detail-modal").modal("show");
            }
        })
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