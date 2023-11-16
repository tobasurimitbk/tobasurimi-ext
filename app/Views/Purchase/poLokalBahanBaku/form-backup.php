<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah</h1>
        <div class="col-button-tambah-spp">

            <?php if (!empty($dataPOLokal)) { ?>

                <?php if ($dataPOLokal->is_posted === "0") { ?>
                    <button class="btn btn-hapus delete-parent float-right">
                        Hapus
                    </button>
                <?php } ?>

                <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("po-lokal-bahan-baku/print/"); ?><?= $dataPOLokal->id ?>')">
                    Print
                </button>

                <?php if ($dataPOLokal->is_posted === "0") { ?>
                    <button class="btn btn-success posting-spp float-right">
                        Posting
                    </button>
                <?php } ?>

            <?php } ?>

            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-lokal-bahan-baku"); ?>">
                Batal
            </a>

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
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" value="<?= !empty($dataPOLokal) ? $dataPOLokal->po_no : "AUTO GENERATE"; ?>" readonly="true" class="form-control" placeholder="No. PO">
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-control input-picker po_date" id="po_date" name="po_date" placeholder="Tanggal Dibuat" value="<?= !empty($dataPOLokal) ? ($dataPOLokal->po_date ? date("d/m/Y", strtotime($dataPOLokal->po_date)) : "") : ""; ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSupplier)) {
                                    foreach ($dataSupplier as $supplier) {
                                ?>
                                        <option <?= !empty($dataPOLokal) ? ($dataPOLokal->supplier_id === $supplier->id ? "selected" : "") : ""; ?> value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>"><?= $supplier->kode; ?> - <?= $supplier->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select pph" id="pph" name="pph" aria-label="Floating label select example">
                                <option <?= !empty($dataPOLokal) ? ($dataPOLokal->pph === "None" ? "selected" : "") : ""; ?> value="None">Pph tidak ditanggung</option>
                                <option <?= !empty($dataPOLokal) ? ($dataPOLokal->pph === "Supplier" ? "selected" : "") : ""; ?> value="Supplier">Pph ditanggung supplier</option>
                                <option <?= !empty($dataPOLokal) ? ($dataPOLokal->pph === "Company" ? "selected" : "") : ""; ?> value="Company">Pph ditanggung perusahaan</option>
                            </select>
                            <label for="floatingInput">PPH</label>
                        </div>
                    </div>
                    <div class="col md-4">
                        <div class="ffloat mb-3" style="height: 50px;">
                            <label for="floatingInput">Potong KG</label>
                            <div>
                                <input autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> <?= !empty($dataPOLokal) ? ($dataPOLokal->potong_kg ? "checked" : "") : ""; ?> class="potong_kg" name="potong_kg" id="potong_kg" type="checkbox">
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" readonly="true" class="form-control" placeholder="Order Oleh" value="<?= !empty($dataPOLokal) ? $dataPOLokal->createdBy : session()->get("login")->name; ?>">
                            <label for="floatingInput">Order Oleh</label>
                        </div>
                    </div> -->
                </div>
                <div class="row">
                    <div class="col md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->cong_sebenarnya : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control cong_sebenarnya" name="cong_sebenarnya" id="cong_sebenarnya" placeholder="Cong Sebenarnya">
                            <label for="floatingInput">Cong Sebenarnya</label>
                        </div>
                    </div>
                    <div class="col md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->cong_batasan : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control cong_batasan" name="cong_batasan" id="cong_batasan" placeholder="Cong Batasan">
                            <label for="floatingInput">Cong Batasan</label>
                        </div>
                    </div>
                    <div class="col md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->subsidi_langsung : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control subsidi_langsung" name="subsidi_langsung" id="subsidi_langsung" placeholder="Subsidi Langsung">
                            <label for="floatingInput">Subsidi Langsung</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($dataPOLokal)) {
                            if ($dataPOLokal->is_posted === "0") { ?>
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
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Spesifikasi</th>
                                <th>Harga Umum</th>
                                <th>Qty</th>
                                <!-- <th>Total Harga</th> -->
                                <th>Bagian</th>
                                <th>Peti</th>
                                <th>Kualitas</th>
                                <th>Harga Harian</th>
                                <th>Harga Bulanan</th>
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
                            if (!empty($dataPOLokal)) {
                                foreach ($dataPOLokal->rm_purchase_order_details as $details) {
                                    $total_harga_barang = $total_harga_barang + ($details->general_price ? formatter(str_replace(",", "", $details->general_price), "STR_TO_INT") : 0);
                                    $total_qty = $total_qty + $details->qty;
                                    $total_harga = $total_harga + ($details->general_price ? formatter(str_replace(",", "", $details->general_price), "STR_TO_INT") : 0) * $details->qty;
                            ?>

                                    <tr>
                                        <?php if ($dataPOLokal->is_posted === "0") { ?>

                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $no; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->kodeBarang; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->barangName; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->spec; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->general_price; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->qty; ?></td>
                                            <!-- <td class="edit-table-detail"  data-total="<?php // number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); 
                                                                                            ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>"   data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?></td> -->
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->warehouseName; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->peti; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->quality; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->daily_price; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->monthly_price; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>" data-monthly_price="<?= $details->monthly_price; ?>" data-daily_price="<?= $details->daily_price; ?>" data-quality="<?= $details->quality; ?>" data-peti="<?= $details->peti; ?>" data-bagian="<?= $details->bagian; ?>" data-barang_id="<?= $details->barang_id; ?>" data-kode_barang="<?= $details->kodeBarang; ?>" data-nama_barang="<?= $details->barangName; ?>" data-spesifikasi="<?= $details->spec; ?>" data-harga="<?= $details->general_price; ?>" data-qty="<?= $details->qty; ?>" data-keterangan="<?= $details->note; ?>" data-id="<?= $details->id; ?>" data-row="<?= $no; ?>"><?= $details->note; ?></td>
                                            <td><button class="btn-trash" onclick='deleteRow("<?= $no; ?>")'>X</button></td>

                                        <?php } else { ?>

                                            <td><?= $no; ?></td>
                                            <td><?= $details->kodeBarang; ?></td>
                                            <td><?= $details->barangName; ?></td>
                                            <td><?= $details->spec; ?></td>
                                            <td><?= $details->general_price; ?></td>
                                            <td><?= $details->qty; ?></td>
                                            <td><?= $details->warehouseName; ?></td>
                                            <td><?= $details->peti; ?></td>
                                            <td><?= $details->quality; ?></td>
                                            <td><?= $details->daily_price; ?></td>
                                            <td><?= $details->monthly_price; ?></td>
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
                                <td colspan="3"></td>
                                <td><b>TOTAL</b></td>
                                <td><b><?= number_format($total_harga_barang); ?></b></td>
                                <td><b><?= $total_qty; ?></b></td>
                                <!-- <td><b><?php // number_format($total_harga); 
                                            ?></b></td> -->
                                <td colspan="11"></td>
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
                                <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" />
                                <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                    <option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control spesifikasi" id="spesifikasi" name="spesifikasi" placeholder="Spesifikasi">
                                <label for="floatingInput">Spesifikasi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea autocomplete="one-time-code" class="form-control keterangan text-area-all" name="keterangan" id="keterangan" placeholder="Keterangan"></textarea>
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
                                <input autocomplete="one-time-code" type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Umum">
                                <label for="floatingInput">Harga Umum</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control daily_price" name="daily_price" id="daily_price" placeholder="Harga Harian">
                                <label for="floatingInput">Harga Harian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control monthly_price" name="monthly_price" id="monthly_price" placeholder="Harga Bulanan">
                                <label for="floatingInput">Harga Bulanan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select bagian" name="bagian" id="bagian" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Bagian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control peti" name="peti" id="peti" placeholder="Peti">
                                <label for="floatingInput">Peti/Tong</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="display: none;">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select quality" name="quality" id="quality" aria-label="Floating label select example">
                                    <option value="Baik">Baik</option>
                                    <option value="Jelek">Jelek</option>
                                </select>
                                <label for="floatingInput">Kualitas</label>
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

    <?php if (!empty($dataPOLokal)) {
        foreach ($dataPOLokal->rm_purchase_order_details as $details) {
    ?>

            priceEdit = Number('<?= $details->general_price; ?>'.replaceAll(",", ""));
            totalPriceEdit = Number('<?= $details->general_price; ?>'.replaceAll(",", "")) * <?= $details->qty; ?>;
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
                spesifikasi: '<?= $details->spec; ?>',
                harga: '<?= $details->general_price; ?>',
                qty: <?= $details->qty; ?>,
                total: '<?= number_format(formatter($details->general_price, "CURR_TO_INT") * $details->qty); ?>',
                keterangan: '<?= $details->note; ?>',
                bagian: '<?= $details->bagian; ?>',
                bagianName: '<?= $details->warehouseName; ?>',
                peti: '<?= $details->peti; ?>',
                quality: '<?= $details->quality; ?>',
                daily_price: '<?= $details->daily_price; ?>',
                monthly_price: '<?= $details->monthly_price; ?>',
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
            harga: {
                required: true
            },
            peti: {
                required: true
            },
            quality: {
                required: true
            },
            daily_price: {
                required: true
            },
            monthly_price: {
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
            harga: {
                required: "Harga wajib diisi"
            },
            peti: {
                required: "Peti/Tong wajib diisi"
            },
            quality: {
                required: "Kualitas wajib diisi"
            },
            daily_price: {
                required: "Harga Harian wajib diisi"
            },
            monthly_price: {
                required: "Harga Bulanan wajib diisi"
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

        // KODE BARANG
        $('.kode_barang').select2({
            placeholder: "Pilih Kode Barang",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            tags: false,
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

        // SPESIFIKASI
        // $('.spesifikasi').select2({
        //     placeholder: "",
        //     theme: "bootstrap-5",
        //     dropdownParent: $(".detail-modal .modal-content")
        // })

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

        // BAGIAN
        $('.bagian').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.bagian')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.bagian')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.bagian')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.icon-po-date').click(function() {
            $(".po_date").focus();
        });

        var validator = $(".create-form").validate({
            rules: {
                po_date: {
                    required: true
                },
                supplier_id: {
                    required: true
                }
            },
            messages: {
                po_date: {
                    required: "Tanggal Dibuat wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
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

        $(".btn-show-detail").click(function() {
            $(".delete-detail").css('display', 'none');

            $(".title-detail-name").text("Tambah");
            $(".id_detail").val('');

            $(".kode").val('')
            $(".nama_barang").val('')
            $(".qty").val('')
            $(".spesifikasi").val('')
            $(".harga").val('')
            $(".total").val('')
            $(".keterangan").val('')

            $(".bagian").val('')
            $(".peti").val('')
            $(".quality").val('')
            $(".daily_price").val('')
            $(".monthly_price").val('')

            validator_detail.resetForm();
            validator_detail.reset();

            $.ajax({
                url: `<?= base_url("warehouse/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".bagian").empty()
                    $(".bagian").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".bagian").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                    })

                    $(".bagian").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("barang/dropdown/type"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    type: "Bahan Baku Lokal"
                },
                success: function(res) {
                    $(".kode_barang").empty();

                    $(".kode_barang").append(`<option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".kode_barang").append(`<option data-barang_id="${item.id}" data-nama="${item.nama_barang}"  data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    })

                    $(".kode_barang").val("").change();
                    $(".detail-modal").modal("show");
                }
            })
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
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

                            row = 0;
                            list_items = [];
                            total_harga_barang = 0;
                            total_qty = 0;
                            total_harga = 0;

                            $(".body-detail-table").empty()

                            res?.detail.map(item => {
                                tag_html += `<tr>`;
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.kodeBarang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.barangName;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.spec;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.satuanName;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.price;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.qty;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.totalPrice;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-ppn="" data-pph="" data-total="${item.totalPrice}" data-additional_cost="" data-disc=""  data-barang_id="${item.barang_id}" data-kode_barang="${item.kodeBarang}" data-nama_barang="${item.barangName}" data-satuan="${item.unit}" data-spesifikasi="${item.spec}" data-harga="${item.price}" data-qty="${item.qty}" data-keterangan="${item.note}" data-id="" data-row="${row + 1}">`;
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
                                    ppn: "",
                                    nilai_ppn: "",
                                    pph: "",
                                    nilai_pph: "",
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
            } else {

            }
        })

        $(".kode_barang").change(function() {
            if ($(".kode_barang option:selected").val()) {
                let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
                let stok = $(".kode_barang option:selected").data("stok") ? $(".kode_barang option:selected").data("stok") : "";
                let harga = $(".kode_barang option:selected").data("harga") ? $(".kode_barang option:selected").data("harga") : "";
                let barang_id = $(".kode_barang option:selected").data("barang_id") ? $(".kode_barang option:selected").data("barang_id") : "";

                $(".nama_barang").attr("readonly", nama ? true : false);

                console.log(harga)

                $.ajax({
                    url: "<?= base_url("barang/id"); ?>" + "/" + barang_id,
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        let spek = res?.data?.spek;
                        $(".spesifikasi").val(spek);
                    }
                })

                $(".kode").val($(".kode_barang option:selected").val());
                $(".nama_barang").val(nama);
                $(".barang_id").val(barang_id);
                $(".qty").val(stok);
                $(".harga").val(harga ? Number(harga).toLocaleString() : "");
                $(".total").val(harga || stok ? (Number(harga.replaceAll(",", "")) * stok).toLocaleString() : "");
            } else {
                $(".spesifikasi").val("")
                $(".nama_barang").attr("readonly", false)
                $(".kode").val("");
                $(".nama_barang").val("");
                $(".barang_id").val("");
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
                        url: "<?= base_url("po-lokal-bahan-baku/delete"); ?>",
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
                                        window.location.href = "<?= base_url("po-lokal-bahan-baku"); ?>"
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
            let barang_id = $(".barang_id").val()
            let kode_barang = $(".kode").val()
            let nama_barang = $(".nama_barang").val()
            let spesifikasi = $(".spesifikasi").val()
            let keterangan = $(".keterangan").val()
            let harga = $(".harga").val()
            let qty = $(".qty").val()
            let total = $(".total").val()

            let bagian = $(".bagian option:selected").val()
            let bagianName = $(".bagian option:selected").text()
            let peti = $(".peti").val()
            let quality = $(".quality").val()
            let daily_price = $(".daily_price").val()
            let monthly_price = $(".monthly_price").val()

            let validate_same = false;

            list_items.map(item => {
                if (barang_id !== '') {
                    // if(item.barang_id == barang_id)
                    // {
                    //     // kalau edit barang, barang tidak ganti tidak kena validasi
                    //     if(row_detail === item.row)
                    //     {
                    //         validate_same = false;
                    //     }
                    //     else
                    //     {
                    //         validate_same = true;
                    //     }
                    // }
                    if (item.barang_id !== barang_id) {
                        validate_same = true;
                    }
                }
            })

            if (validate_same) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Berbeda",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                // update detail
                if (row_detail) {
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
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += row + 1;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += kode_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += nama_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += spesifikasi;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += harga;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += qty;
                                        tag_html += "</td>";
                                        // tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        // tag_html += total;
                                        // tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price"=${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += bagianName;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += peti;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += quality;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += daily_price;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += monthly_price;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
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
                                            spesifikasi: spesifikasi,
                                            harga: harga,
                                            qty: qty,
                                            total: total,
                                            keterangan: keterangan,

                                            bagian: bagian,
                                            bagianName: bagianName,
                                            peti: peti,
                                            quality: quality,
                                            daily_price: daily_price,
                                            monthly_price: monthly_price
                                        });

                                        row = row + 1;

                                        total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                        total_qty = total_qty + Number(qty.replaceAll(",", ""));
                                        total_harga = total_harga + Number(total.replaceAll(",", ""));
                                    } else {
                                        tag_html += `<tr>`;
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += row + 1;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.kode_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.nama_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.spesifikasi;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.harga;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.qty;
                                        tag_html += "</td>";
                                        // tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        // tag_html += item.total;
                                        // tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.bagianName;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.peti;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.quality;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.daily_price;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.monthly_price;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
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
                                tag_total += "<td colspan='3'>";
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
                                // tag_total += "<td>";
                                // tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                // tag_total += "</td>";
                                tag_total += "<td colspan='11'>";
                                tag_total += "</td>";
                                tag_total += "</tr>";

                                $(".foot-detail-table").append(tag_total);

                                $(".detail-modal").modal("hide")
                            }
                        })
                    }
                }
                // create detail
                else {
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
                                    spesifikasi: spesifikasi,
                                    harga: harga,
                                    qty: qty,
                                    total: total,
                                    keterangan: keterangan,

                                    bagian: bagian,
                                    bagianName: bagianName,
                                    peti: peti,
                                    quality: quality,
                                    daily_price: daily_price,
                                    monthly_price: monthly_price
                                })

                                total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                total_qty = total_qty + Number(qty.replaceAll(",", ""));
                                total_harga = total_harga + Number(total.replaceAll(",", ""));

                                let tag_html = "";
                                let tag_total = "";

                                tag_html += `<tr>`;
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += kode_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += nama_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += spesifikasi;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += harga;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += qty;
                                tag_html += "</td>";
                                // tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                // tag_html += total;
                                // tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += bagianName;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += peti;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += quality;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += daily_price;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += monthly_price;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail"  data-daily_price="${daily_price}" data-monthly_price="${monthly_price}"  data-quality="${quality}" data-peti="${peti}" data-bagian="${bagian}"  data-total="${total}"  data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}"  data-spesifikasi="${spesifikasi}" data-harga="${harga}" data-qty="${qty}" data-keterangan="${keterangan}" data-id="" data-row="${row + 1}">`;
                                tag_html += keterangan;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";
                                $(".body-detail-table").append(tag_html)

                                $(".foot-detail-table").empty()

                                tag_total += `<tr>`;
                                tag_total += "<td colspan='3'>";
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
                                // tag_total += "<td>";
                                // tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                // tag_total += "</td>";
                                tag_total += "<td colspan='11'>";
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

        $(".harga, .qty").keyup(function() {
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
                        url: "<?= base_url("po-lokal-bahan-baku/update-status"); ?>",
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
                                        window.location.href = "<?= base_url("po-lokal-bahan-baku"); ?>" + "/id/" + $(".id").val()
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

                            if (list_delete.length !== 0) {
                                list_delete.map(obj => {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        item_code: obj.kode_barang,
                                        item_name: obj.nama_barang,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        general_price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        note: obj.keterangan,
                                        spec: obj.spesifikasi,

                                        bagian: obj.bagian ? Number(obj.bagian) : 0,
                                        peti: obj.peti,
                                        quality: obj.quality,
                                        daily_price: obj.daily_price ? Number(obj.daily_price.replaceAll(",", "")) : 0,
                                        monthly_price: obj.monthly_price ? Number(obj.monthly_price.replaceAll(",", "")) : 0,

                                        isDeleted: true
                                    })
                                })
                            }

                            list_items.map(obj => {
                                if (obj.id) {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        item_code: obj.kode_barang,
                                        item_name: obj.nama_barang,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        general_price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        note: obj.keterangan,
                                        spec: obj.spesifikasi,

                                        bagian: obj.bagian ? Number(obj.bagian) : 0,
                                        peti: obj.peti,
                                        quality: obj.quality,
                                        daily_price: obj.daily_price ? Number(obj.daily_price.replaceAll(",", "")) : 0,
                                        monthly_price: obj.monthly_price ? Number(obj.monthly_price.replaceAll(",", "")) : 0
                                    })
                                } else {
                                    update_list_items.push({
                                        item_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        item_code: obj.kode_barang,
                                        item_name: obj.nama_barang,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        general_price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        note: obj.keterangan,
                                        spec: obj.spesifikasi,

                                        bagian: obj.bagian ? Number(obj.bagian) : 0,
                                        peti: obj.peti,
                                        quality: obj.quality,
                                        daily_price: obj.daily_price ? Number(obj.daily_price.replaceAll(",", "")) : 0,
                                        monthly_price: obj.monthly_price ? Number(obj.monthly_price.replaceAll(",", "")) : 0
                                    })
                                }
                            })

                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if (id) {
                                $.ajax({
                                    url: "<?= base_url("po-lokal-bahan-baku/update"); ?>",
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
                                                    window.location.href = "<?= base_url("po-lokal-bahan-baku"); ?>" + "/id/" + id;
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
                                    url: "<?= base_url("po-lokal-bahan-baku/save"); ?>",
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
                                                    window.location.href = "<?= base_url("po-lokal-bahan-baku"); ?>" + "/id/" + +response.id;
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
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                row = 0;

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;

                list_items.map(item => {
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        // tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        // tag_html += item.total;
                        // tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.bagianName;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.peti;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.quality;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.daily_price;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.monthly_price;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            ...item,
                            row: row + 1
                        });

                        row = row + 1;

                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                    } else {
                        // sent parameter isDelete if have customer id and id
                        if (item.id) {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".foot-detail-table").empty()

                tag_total += `<tr>`;
                tag_total += "<td colspan='3'>";
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
                // tag_total += "<td>";
                // tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                // tag_total += "</td>";
                tag_total += "<td colspan='11'>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    }

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
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;

                row = 0;

                list_items.map(item => {
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.spesifikasi;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        // tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="" data-row="${row + 1}">`;
                        // tag_html += item.total;
                        // tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.bagianName;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.peti;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.quality;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.daily_price;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail"  data-daily_price="${item.daily_price}" data-monthly_price="${item.monthly_price}" data-quality="${item.quality}" data-peti="${item.peti}" data-bagian="${item.bagian}"  data-total="${item.total}"  data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}"  data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" data-qty="${item.qty}" data-keterangan="${item.keterangan}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.monthly_price;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            ...item,
                            row: row + 1
                        });

                        row = row + 1;

                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                    } else {
                        // sent parameter isDelete if have customer id and id
                        if (item.id) {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".foot-detail-table").empty()

                tag_total += `<tr>`;
                tag_total += "<td colspan='3'>";
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
                // tag_total += "<td>";
                // tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                // tag_total += "</td>";
                tag_total += "<td colspan='11'>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    })


    $(document).on('click', '.edit-table-detail', function(evt) {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');

        let barang_id = $(this).data('barang_id')
        let kode_barang = $(this).data('kode_barang')
        let nama_barang = $(this).data('nama_barang')
        let spesifikasi = $(this).data('spesifikasi')
        let harga = $(this).data('harga')
        let qty = $(this).data('qty')
        let keterangan = $(this).data('keterangan')
        let rowid = $(this).data('row')
        let id = $(this).data('id')
        let total = $(this).data('total')

        let bagian = $(this).data('bagian')
        let peti = $(this).data('peti')
        let quality = $(this).data('quality')
        let daily_price = $(this).data('daily_price')
        let monthly_price = $(this).data('monthly_price')

        validator_detail.resetForm();
        validator_detail.reset();

        $(".id_detail").val(rowid)
        $(".kode").val(kode_barang)
        $(".keterangan").val(keterangan)

        $(".spesifikasi").val(spesifikasi);

        $.ajax({
            url: `<?= base_url("barang/dropdown/type"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                type: "Bahan Baku Lokal"
            },
            success: function(res) {
                $(".kode_barang").empty();

                $(".kode_barang").append(`<option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>`);

                if (barang_id === "") {
                    $(".kode_barang").append(`<option selected data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value="${kode_barang}">${kode_barang}</option>`);
                }

                res.data.forEach(function(item) {
                    if (kode_barang == item.kode_barang) {
                        $(".kode_barang").append(`<option selected data-barang_id="${item.id}" data-nama="${item.nama_barang}"  data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    } else {
                        $(".kode_barang").append(`<option data-barang_id="${item.id}" data-nama="${item.nama_barang}"  data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    }
                })
            }
        })

        $(".barang_id").val(barang_id)
        $(".nama_barang").val(nama_barang)

        if (barang_id === "") {
            $(".nama_barang").attr("readonly", false);
        } else {
            $(".nama_barang").attr("readonly", true);
        }

        $(".harga").val(harga)
        $(".qty").val(qty)
        $(".total").val(total)

        $(".peti").val(peti)
        $(".quality").val(quality)
        $(".daily_price").val(daily_price)
        $(".monthly_price").val(monthly_price)

        $.ajax({
            url: `<?= base_url("warehouse/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".bagian").empty()
                $(".bagian").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".bagian").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })

                $(".bagian").val(bagian).change()
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
</script>
<?= $this->endSection(); ?>