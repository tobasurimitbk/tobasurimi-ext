<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Bea Cukai</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("bea-cukai"); ?>">
                Batal
            </a>
            <?php if(!empty($dataBeaCukai)){ 
            if($dataBeaCukai->status_post === "WAITING"){ 
            ?> 
            <button class="btn btn-hapus delete-parent float-right">
                Hapus
            </button>
            <button class="btn btn-success posting-spp float-right">
                Posting
            </button>
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
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($dataBeaCukai) ? $dataBeaCukai->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataBeaCukai) ? $dataBeaCukai->no_bea_cukai : "AUTO GENERATE"; ?>" readonly="true" autocomplete="one-time-code" type="text" class="form-control bc_no" id="bc_no" name="bc_no" placeholder="No. Dokumen">
                            <label for="floatingInput">No. Dokumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select po_type" id="po_type" name="po_type" aria-label="Floating label select example" onchange="changeTipeBahan()">
                                <option value=""></option>
                                <option <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "LOKAL BAKU" ? "selected" : "") : ""; ?> value="LOKAL BAKU">PO Lokal Bahan Baku</option>
                                <option <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "LOKAL PENOLONG" ? "selected" : "") : ""; ?> value="LOKAL PENOLONG">PO Lokal Bahan Penolong</option>
                                <option <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "IMPORT BAKU" ? "selected" : "") : ""; ?> value="IMPORT BAKU">PO Import Bahan Baku</option>
                                <option <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "IMPORT PENOLONG" ? "selected" : "") : ""; ?> value="IMPORT PENOLONG">PO Import Bahan Penolong</option>
                            </select>
                            <label for="floatingInput">Tipe PO</label>
                        </div>
                    </div>
                    <div class="col-md-4 single-po" style="<?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "LOKAL BAKU" || $dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "IMPORT BAKU" ? "display: none;" : "") : "display: none;"; ?>">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select po_id" id="po_id" name="po_id" onchange="changePO()" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                    if (!empty($dropdownPO)) {
                                        foreach ($dropdownPO as $no) {
                                    ?>
                                            <option <?= (!empty($dataBeaCukai) ? ($dataBeaCukai->po_id === $no["id"] ? "selected" : "") : ""); ?> value="<?= $no["id"]; ?>"><?= $no["po_no"]; ?></option>
                                    <?php
                                        }
                                    }
                                ?>
                            </select>
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                    <div class="col-md-4 multiple-po" style="<?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "LOKAL PENOLONG" || $dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "IMPORT PENOLONG" ? "display: none;" : "") : "display: none;"; ?>">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> multiple class="form-select multiple_po_id" id="multiple_po_id" name="multiple_po_id" onchange="changeMultiPO()" aria-label="Floating label select example">
                            <?php
                                if (!empty($dropdownPO)) {
                                    foreach ($dropdownPO as $no) {
                                ?>
                                        <option <?= (!empty($dataBeaCukai) ? (in_array($no["id"], ($dataBeaCukai->multiple_po_id ? json_decode($dataBeaCukai->multiple_po_id) : [])) ? "selected" : "") : ""); ?> value="<?= $no["id"]; ?>"><?= $no["po_no"]; ?></option>
                                <?php
                                    }
                                }
                            ?>
                            </select>
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                </div>
                <div class="row sj" style="<?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "LOKAL BAKU" || $dataBeaCukai->status_po . " " . $dataBeaCukai->tipe_bahan === "IMPORT BAKU" ? "display: none;" : "") : "display: none;"; ?>">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataBeaCukai) ? $dataBeaCukai->invoice_no : ""; ?>" autocomplete="one-time-code" type="text" class="form-control invoice_no" id="invoice_no" name="invoice_no" placeholder="No. Invoice">
                            <label for="floatingInput">No. Surat Jalan</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Data Dokumen</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataAJU)) {
                                    foreach ($dataAJU as $aju) {
                                ?>
                                        <option <?= !empty($dataBeaCukai) ? ($dataBeaCukai->aju_document_type === $aju["id"] ? "selected" : "") : ""; ?> value="<?= $aju["id"]; ?>"><?= $aju["value"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Jenis Dokumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataBeaCukai) ? $dataBeaCukai->aju_no : ""; ?>" autocomplete="one-time-code" type="text" class="form-control aju_no" name="aju_no" id="aju_no" placeholder="No. Invoice">
                            <label for="floatingInput">No. AJU</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataBeaCukai) ? ($dataBeaCukai->validation_date ? date("d/m/Y", strtotime($dataBeaCukai->validation_date)) : "") : ""; ?>" autocomplete="one-time-code" type="text" class="form-control input-picker validation_date" id="validation_date" name="validation_date" placeholder="Tanggal Pendaftaran">
                                <label for="floatingInput">Tanggal Pendaftaran</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataBeaCukai) ? number_format(formatter($dataBeaCukai->shipping_cost, "STR_TO_INT")) : ""; ?>" autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control shipping_cost" name="shipping_cost" id="shipping_cost" placeholder="Biaya Ongkos Kirim (Opsional)">
                            <label for="floatingInput">Biaya Ongkos Kirim (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataBeaCukai) ? number_format(formatter($dataBeaCukai->bea_masuk, "STR_TO_INT")) : ""; ?>" autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control bea_masuk" name="bea_masuk" id="bea_masuk" placeholder="Bea Masuk">
                                <label for="floatingInput">Bea Masuk</label>
                            </div>
                        </div>
                    <div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-4">
                        <label class="form-label font-weight-bold">Tax</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataBeaCukai) ? formatter($dataBeaCukai->ppn, "STR_TO_FLOAT") : ""; ?>" autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control ppn" name="ppn" id="ppn" placeholder="PPN % (Opsional)">
                            <label for="floatingInput">PPN % (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataBeaCukai) ? ($dataBeaCukai->status_post === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataBeaCukai) ? formatter($dataBeaCukai->pph, "STR_TO_FLOAT") : ""; ?>" autocomplete="one-time-code" type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control pph" name="pph" id="pph" placeholder="PPH % (Opsional)">
                            <label for="floatingInput">PPH % (Opsional)</label>
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
                                <th>No. PO</th>
                                <th>Berat</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Disc %</th>
                                <th>Biaya Tambahan</th>
                                <th>Total Harga</th>
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
                            $total_berat = 0;
                            if (!empty($dataBeaCukaiDetail)) {
                                foreach ($dataBeaCukaiDetail as $details) {
                                    $total_harga_barang = $total_harga_barang + ($details["price"] ? formatter($details["price"], "STR_TO_INT") : 0);
                                    $total_qty = $total_qty + ($details["qty"] ? formatter($details["qty"], "STR_TO_FLOAT") : 0);
                                    $total_berat = $total_berat + ($details["berat"] ? formatter($details["berat"], "STR_TO_FLOAT") : 0);
                                    $total_harga = $total_harga + ($details["price"] && $details["qty"] ? (formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")) : 0);
                            ?>
                                    <tr>
                                        <?php if ($dataBeaCukai->status_post === "WAITING") { ?>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $no; ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["kode_barang"]; ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["nama_barang"]; ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["spec"]; ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["po_no"]; ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= formatter($details["berat"], "STR_TO_FLOAT"); ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= formatter($details["qty"], "STR_TO_FLOAT"); ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["nama_satuan"]; ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= number_format(formatter($details["price"], "STR_TO_INT")); ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= formatter($details["disc"], "STR_TO_INT"); ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?></td>
                                            <td class="edit-table-detail" data-berat="<?= formatter($details["berat"], "STR_TO_FLOAT"); ?>" data-nama_satuan="<?= $details["nama_satuan"]; ?>" data-total="<?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?>" data-additional_cost="<?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?>" data-disc="<?= formatter($details["disc"], "STR_TO_INT"); ?>" data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-spesifikasi="<?= $details["spec"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_FLOAT"); ?>" data-keterangan="<?= $details["note"]; ?>" data-id="<?= formatter($details["id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["note"]; ?></td>
                                            <td><button type='button' onclick='deleteRow(<?= $no; ?>)'>X</button></td>
                                        <?php } else { ?>
                                            <td><?= $no; ?></td>
                                            <td><?= $details["kode_barang"]; ?></td>
                                            <td><?= $details["nama_barang"]; ?></td>
                                            <td><?= $details["spec"]; ?></td>
                                            <td><?= $details["po_no"]; ?></td>
                                            <td><?= formatter($details["berat"], "STR_TO_FLOAT"); ?></td>
                                            <td><?= formatter($details["qty"], "STR_TO_FLOAT"); ?></td>
                                            <td><?= $details["nama_satuan"]; ?></td>
                                            <td><?= number_format(formatter($details["price"], "STR_TO_INT")); ?></td>
                                            <td><?= formatter($details["disc"], "STR_TO_INT"); ?></td>
                                            <td><?= number_format(formatter($details["additional_cost"], "STR_TO_INT")); ?></td>
                                            <td><?= number_format(formatter($details["price"], "STR_TO_INT") * formatter($details["qty"], "STR_TO_FLOAT")); ?></td>
                                            <td><?= $details["note"]; ?></td>
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
                                <td><b>GRAND TOTAL</b></td>
                                <td><b><?= $total_berat; ?></b></td>
                                <td><b><?= $total_qty; ?></b></td>
                                <td></td>
                                <td><b><?= number_format($total_harga_barang); ?></b></td>
                                <td></td>
                                <td></td>
                                <td><b><?= number_format($total_harga); ?></b></td>
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
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" type="text" class="form-control spesifikasi" id="spesifikasi" name="spesifikasi" placeholder="Spesifikasi">
                                <label for="floatingInput">Spesifikasi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea autocomplete="one-time-code" readonly="true" class="form-control keterangan text-area-all" name="keterangan" id="keterangan" placeholder="Keterangan"></textarea>
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
                                <input autocomplete="one-time-code" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control berat" name="berat" id="berat" placeholder="Berat">
                                <label for="floatingInput">Berat</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" type="text" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control disc" name="disc" id="disc" placeholder="Diskon %">
                                <label for="floatingInput">Diskon %</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" onkeyup="formatNumber(this)" type="text" class="form-control additional_cost" name="additional_cost" id="additional_cost" placeholder="Biaya Tambahan">
                                <label for="floatingInput">Biaya Tambahan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-detail delete-form">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let list_delete = [];
    var total_harga_barang = 0;
    var total_qty = 0;
    var total_berat = 0;
    var total_harga = 0;
    var priceEdit = 0;
    var totalPriceEdit = 0;
    var row = 0;

    <?php if (!empty($dataBeaCukaiDetail)) {
        foreach ($dataBeaCukaiDetail as $details) {
    ?>

            priceEdit = Number('<?= $details["price"]; ?>');
            totalPriceEdit = Number('<?= $details["total_price"]; ?>');
            row = row + 1;

            total_harga_barang = total_harga_barang + priceEdit;
            total_qty = total_qty + Number('<?= $details["qty"]; ?>');
            total_berat = total_berat + Number('<?= $details["berat"]; ?>');
            total_harga = total_harga + totalPriceEdit;

            list_items.push({
                id: <?= $details["id"]; ?>,
                row: row,
                barang_id: '<?= $details["barang_id"]; ?>',
                kode_barang: '<?= $details["kode_barang"]; ?>',
                nama_barang: '<?= $details["nama_barang"]; ?>',
                nama_satuan: '<?= $details["nama_satuan"]; ?>',
                po_no: '<?= $details["po_no"]; ?>',
                unit: <?= $details["unit"]; ?>,
                spec: '<?= $details["spec"]; ?>',
                harga: Number('<?= $details["price"]; ?>').toLocaleString(),
                qty: Number('<?= $details["qty"]; ?>'),
                berat: Number('<?= $details["berat"]; ?>'),
                sub_total: (Number('<?= $details["total_price"]; ?>')).toLocaleString(),
                note: '<?= $details["note"]; ?>',
                additional_cost: Number('<?= $details["additional_cost"]; ?>').toLocaleString(),
                disc: Number('<?= $details["disc"]; ?>'),
            })
        <?php
        }
        ?>
    <?php
    } ?>

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                po_type: {
                    required: true
                },
                "multiple_po_id[]": {
                    required: true
                },
                po_id: {
                    required: true
                },
                aju_document_type: {
                    required: true
                },
                aju_no: {
                    required: true,
                },
                validation_date: {
                    required: true,
                },
                invoice_no: {
                    required: true,
                },
                total_weight: {
                    required: true,
                },
                biaya_masuk: {
                    required: true,
                }
            },
            messages: {
                po_type: {
                    required: "Tipe PO wajib diisi"
                },
                "multiple_po_id[]": {
                    required: "No. PO wajib diisi"
                },
                po_id: {
                    required: "No. PO wajib diisi"
                },
                aju_document_type: {
                    required: "Jenis Dokumen wajib diisi"
                },
                aju_no: {
                    required: "No. AJU wajib diisi"
                },
                validation_date: {
                    required: "Tanggal wajib diisi"
                },
                invoice_no: {
                    required: "No. Invoice wajib diisi"
                },
                total_weight: {
                    required: "Weight wajib diisi"
                },
                biaya_masuk: {
                    required: "Bea Masuk wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                console.log(elem);
                if (elem.hasClass("multiple_po_id")) {
                    element = $(".select2-selection--multiple").parent();
                    error.insertAfter(element);
                } else if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent(); 
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.col-md-6').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.col-md-6').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $('.multiple_po_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.multiple_po_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.multiple_po_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.multiple_po_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.po_id').select2({
            placeholder: "",
            allowClear: true,
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.po_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.po_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.po_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.po_type').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.po_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.po_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.po_type')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".validation_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // AJU DOCUMENT TYPE
        $('.aju_document_type').select2({
            placeholder: "",
            theme: "bootstrap-5",
        })

        //CSS SELECT2 FLOATING LABEL
        $('.aju_document_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.aju_document_type')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.aju_document_type')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".ppn").keyup(function() {
            let ppn_sementara = $(".ppn").val() ? $(".ppn").val() : 0;

            if ($(".ppn").val()) {
                if ($(".ppn").val() > 100) {
                    $(".ppn").val(100)
                }
                if ($(".ppn").val() < 0) {
                    $(".ppn").val();
                }
            } else {
                $(".ppn").val();
            }
        })

        $(".pph").keyup(function() {
            let pph_sementara = $(".pph").val() ? $(".pph").val() : 0;
            
            if ($(".pph").val()) {
                if ($(".pph").val() > 100) {
                    $(".pph").val(100)
                }
                if ($(".pph").val() < 0) {
                    $(".pph").val();
                }
            } else {
                $(".pph").val();
            }
        })

        $(".harga, .qty").keyup(function() {
            let qty = $(".qty").val() !== null ? Number($(".qty").val()) : 0;
            let harga = $(".harga").val() !== null ? Number($(".harga").val().replaceAll(",", "")) : 0;
            $(".total").val((qty * harga).toLocaleString())
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
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
                        url: "<?= base_url("bea-cukai/delete"); ?>",
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
                                        window.location.href = "<?= base_url("bea-cukai"); ?>"
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
            let harga = $(".harga").val()
            let berat = $(".berat").val()
            let qty = $(".qty").val()
            let total = $(".total").val()

            // update detail
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
                    total_harga_barang = 0;
                    total_qty = 0;
                    total_harga = 0;
                    total_berat = 0;

                    row = 0;

                    $(".body-detail-table").empty()

                    list_items.map(item => {
                        if (item.row == row_detail) {
                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.kode_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.spec;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.po_no;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += berat;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.disc;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.additional_cost;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += total;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${total}" data-spesifikasi="${item.spec}" data-berat="${berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${harga}" data-qty="${qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.note;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `<button type='button' onclick='deleteRow(${row + 1})'>X</button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                            total_qty = total_qty + Number(qty);
                            total_harga = total_harga + Number(total.replaceAll(",", ""));
                            total_berat = total_berat + Number(berat);

                            new_list_items.push({
                                ...item,
                                qty: qty,
                                harga: harga,
                                berat: berat,
                                sub_total: total
                            });

                            console.log(new_list_items)

                            row = row + 1;
                        } else {
                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.kode_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.spec;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.po_no;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.berat;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.disc;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.additional_cost;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.sub_total;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.note;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `<button type='button' onclick='deleteRow(${row + 1})'>X</button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                            total_qty = total_qty + Number(item.qty);
                            total_harga = total_harga + Number(item.sub_total.replaceAll(",", ""));
                            total_berat = total_berat + Number(item.berat);

                            new_list_items.push(item);

                            row = row + 1;
                        }
                    })

                    list_items = [];

                    list_items = new_list_items;

                    $(".body-detail-table").append(tag_html)

                    tag_total = "";
                    tag_total += `<tr>`;
                    tag_total += `<td colspan="4">`;
                    tag_total += "</td>";
                    tag_total += `<td>`;
                    tag_total += `GRAND TOTAL`;
                    tag_total += "</td>";
                    tag_total += `<td>`;
                    tag_total += `<b>${total_berat}</b>`;
                    tag_total += "</td>";
                    tag_total += `<td>`;
                    tag_total += `<b>${total_qty}</b>`;
                    tag_total += "</td>";
                    tag_total += `<td>`;
                    tag_total += "</td>";
                    tag_total += `<td>`;
                    tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                    tag_total += "</td>";
                    tag_total += `<td colspan="2">`;
                    tag_total += "</td>";
                    tag_total += `<td>`;
                    tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                    tag_total += "</td>";
                    tag_total += `<td colspan="2">`;
                    tag_total += "</td>";
                    tag_total += "</tr>";
                    
                    $(".foot-detail-table").empty();
                    $(".foot-detail-table").append(tag_total);

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
                        url: "<?= base_url("bea-cukai/update-status"); ?>",
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
                                        window.location.href = "<?= base_url("bea-cukai"); ?>";
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

                            let id = $(".id").val();
                            data.append("po_no", $(".po_id option:selected").text());
                            let update_list_items = [];

                            if($(".po_type option:selected").val() === "LOKAL BAKU" || $(".po_type option:selected").val() === "IMPORT BAKU"){
                                data.append("multiple_po_id", JSON.stringify($('.multiple_po_id').val()));
                                var arr_no = $('.multiple_po_id').select2('data').map(function(elem){ 
                                    return elem.text 
                                });
                                data.append("multiple_po_no", JSON.stringify(arr_no));
                            }

                            if (list_delete.length !== 0) {
                                list_delete.map(obj => {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        po_no: obj.po_no,
                                        spec: obj.spec,
                                        note: obj.note,
                                        unit: obj.unit ? Number(obj.unit) : 0,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        disc: obj.disc ? Number(obj.disc) : 0,
                                        additional_cost: obj.additional_cost ? Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                        berat: obj.berat ? Number(obj.berat) : 0,
                                        total_price: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                        isDeleted: true
                                    })
                                })
                            }

                            list_items.map(obj => {
                                if (obj.id) {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        po_no: obj.po_no,
                                        spec: obj.spec,
                                        note: obj.note,
                                        unit: obj.unit ? Number(obj.unit) : 0,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        disc: obj.disc ? Number(obj.disc) : 0,
                                        additional_cost: obj.additional_cost ? Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                        berat: obj.berat ? Number(obj.berat) : 0,
                                        total_price: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                        isDeleted: false
                                    })
                                }
                                else
                                {
                                    update_list_items.push(
                                        {
                                            id: "",
                                            barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                            po_no: obj.po_no,
                                            spec: obj.spec,
                                            note: obj.note,
                                            unit: obj.unit ? Number(obj.unit) : 0,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            disc: obj.disc ? Number(obj.disc) : 0,
                                            additional_cost: obj.additional_cost ? Number(obj.additional_cost.replaceAll(",", "")) : 0,
                                            berat: obj.berat ? Number(obj.berat) : 0,
                                            total_price: obj.sub_total ? Number(obj.sub_total.replaceAll(",", "")) : 0,
                                            isDeleted: false
                                        }
                                    )
                                }
                            })

                            data.append("items", JSON.stringify(update_list_items))

                            // UPDATE
                            if(id)
                            {
                                $.ajax({
                                    url: "<?= base_url("bea-cukai/update"); ?>",
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
                                                    window.location.href = "<?= base_url("bea-cukai"); ?>";
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
                            else
                            {
                                $.ajax({
                                    url: "<?= base_url("bea-cukai/save"); ?>",
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
                                                    window.location.href = "<?= base_url("bea-cukai"); ?>";
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

    const changeTipeBahan = function()
    {
        let tag_total = "";

        list_items.forEach((item) => {
            if(item.id)
            {
                list_delete.push(item)
            }
        })
        
        list_items = [];
        total_harga_barang = 0;
        total_qty = 0;
        total_berat = 0;
        total_harga = 0;
        row = 0;

        $(".multiple_po_id").val([]).change()
        $(".po_id").val('').change()
        $(".invoice_no").val('')
        $(".multiple_po_id").empty()
        $(".po_id").empty()
        $(".po_id").append(`<option value=""></option>`)

        $(".body-detail-table").empty();
        tag_total += `<tr>`;
        tag_total += `<td colspan="4">`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `GRAND TOTAL`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td colspan="4">`;
        tag_total += "</td>";
        tag_total += "</tr>";
        
        $(".foot-detail-table").empty();
        $(".foot-detail-table").append(tag_total);

        if($(".po_type").val() === "LOKAL BAKU")
        {
            $(".single-po").css("display", "none")
            $(".multiple-po").css("display", "")
            $(".sj").css("display", "none")

            $.ajax({
                url: `<?= base_url("po-bea-cukai-lokal-baku/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".po_id").empty()
                    $(".po_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".multiple_po_id").append(`<option value="${item.id}">${item.po_no}</option>`)
                    })
                }
            })
        }
        
        if($(".po_type").val() === "LOKAL PENOLONG")
        {
            $(".single-po").css("display", "")
            $(".multiple-po").css("display", "none")
            $(".sj").css("display", "")
            $.ajax({
                url: `<?= base_url("po-bea-cukai-lokal-penolong/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".po_id").empty()
                    $(".po_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".po_id").append(`<option value="${item.id}">${item.po_no}</option>`)
                    })
                }
            })
        }

        if($(".po_type").val() === "IMPORT BAKU")
        {
            $(".single-po").css("display", "none")
            $(".multiple-po").css("display", "")
            $(".sj").css("display", "none")

            $.ajax({
                url: `<?= base_url("po-bea-cukai-import-baku/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".po_id").empty()
                    $(".po_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".multiple_po_id").append(`<option value="${item.id}">${item.po_no}</option>`)
                    })
                }
            })
        }

        if($(".po_type").val() === "IMPORT PENOLONG")
        {
            $(".single-po").css("display", "")
            $(".multiple-po").css("display", "none")
            $(".sj").css("display", "")
            $.ajax({
                url: `<?= base_url("po-bea-cukai-import-penolong/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".po_id").empty()
                    $(".po_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".po_id").append(`<option value="${item.id}">${item.po_no}</option>`)
                    })
                }
            })
        }
    }

    const changeMultiPO = function()
    {
        let tag_html = "";
        let tag_total = "";

        list_items.forEach((item) => {
            if(item.id)
            {
                list_delete.push(item)
            }
        })

        list_items = [];
        total_harga_barang = 0;
        total_qty = 0;
        total_berat = 0;
        total_harga = 0;
        row = 0;

        $(".body-detail-table").empty();
        tag_total += `<tr>`;
        tag_total += `<td colspan="4">`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `GRAND TOTAL`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td colspan="4">`;
        tag_total += "</td>";
        tag_total += "</tr>";
        
        $(".foot-detail-table").empty();
        $(".foot-detail-table").append(tag_total);

        let arr = $(".multiple_po_id").val();

        if($(".po_type").val() === "LOKAL BAKU")
        {
            arr?.forEach((items) => {
                $.ajax({
                    url: `<?= base_url("po-lokal-bahan-baku/multi/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        id: items
                    },
                    dataType: "json",
                    success: function(res) {
                        console.log(res)
                        
                        res.data.forEach(function(item) {
                                list_items.push({
                                    id: '',
                                    row: row + 1,
                                    barang_id: item.barang_id,
                                    unit: item.id_satuan,
                                    kode_barang: item.kode_barang,
                                    nama_barang: item.nama_barang,
                                    qty: Number(item.qty),
                                    nama_satuan: item.nama_satuan,
                                    harga: Number(item.general_price).toLocaleString(),
                                    sub_total: (Number(item.qty) * Number(item.general_price)).toLocaleString(),
                                    note: item.note,
                                    po_no: item.po_no,
                                    disc: 0,
                                    additional_cost: 0,
                                    berat: 0,
                                    spec: item.spec
                                })

                                total_harga_barang = total_harga_barang + Number(item.general_price);
                                total_qty = total_qty + Number(item.qty);
                                total_harga = total_harga + (Number(item.general_price) * Number(item.qty));

                                tag_html += `<tr>`;
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.kode_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.nama_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.spec;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.po_no;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += Number(item.qty);
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.nama_satuan;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += Number(item.general_price).toLocaleString();
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += (Number(item.general_price) * Number(item.qty)).toLocaleString();
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.general_price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="0" data-disc="0" data-barang_id="${item.barang_id}" data-unit="${item.id_satuan}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.general_price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.note;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `<button type='button' onclick='deleteRow(${row + 1})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                row = row + 1;

                                $(".body-detail-table").empty();
                                $(".body-detail-table").append(tag_html)

                                tag_total = "";
                                tag_total += `<tr>`;
                                tag_total += `<td colspan="4">`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `GRAND TOTAL`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `<b>0</b>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `<b>${total_qty}</b>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += `<td colspan="2">`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += "</td>";
                                tag_total += "</tr>";
                                
                                $(".foot-detail-table").empty();
                                $(".foot-detail-table").append(tag_total);
                            
                        })
                    }
                })
            })
        }

        if($(".po_type").val() === "IMPORT BAKU")
        {
            arr?.forEach((items) => {
                $.ajax({
                    url: `<?= base_url("po-import-bahan-baku/multi/dropdown"); ?>`,
                    method: "GET",
                    data: {
                        id: items
                    },
                    dataType: "json",
                    success: function(res) {
                        console.log(res)
                        
                        res.data.forEach(function(item) {
                                list_items.push({
                                    id: '',
                                    row: row + 1,
                                    barang_id: item.barang_id,
                                    unit: item.unit,
                                    kode_barang: item.kode_barang,
                                    nama_barang: item.nama_barang,
                                    qty: Number(item.qty),
                                    nama_satuan: item.nama_satuan,
                                    harga: Number(item.price).toLocaleString(),
                                    sub_total: (Number(item.qty) * Number(item.price)).toLocaleString(),
                                    note: item.note,
                                    po_no: item.po_no,
                                    disc: Number(item.disc),
                                    additional_cost: Number(item.additional_cost).toLocaleString(),
                                    berat: 0,
                                    spec: item.spec
                                })

                                total_harga_barang = total_harga_barang + Number(item.price);
                                total_qty = total_qty + Number(item.qty);
                                total_harga = total_harga + (Number(item.price) * Number(item.qty));

                                tag_html += `<tr>`;
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.kode_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.nama_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.spec;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.po_no;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += 0;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += Number(item.qty);
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.nama_satuan;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += Number(item.price).toLocaleString();
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += Number(item.disc);
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += Number(item.additional_cost).toLocaleString();
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += (Number(item.price) * Number(item.qty)).toLocaleString();
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-total="${(Number(item.price) * Number(item.qty)).toLocaleString()}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${Number(item.additional_cost).toLocaleString()}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${Number(item.totalPrice).toLocaleString()}" data-harga="${Number(item.price).toLocaleString()}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                tag_html += item.note;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `<button type='button' onclick='deleteRow(${row + 1})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                row = row + 1;

                                $(".body-detail-table").empty();
                                $(".body-detail-table").append(tag_html)

                                tag_total = "";
                                tag_total += `<tr>`;
                                tag_total += `<td colspan="4">`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `GRAND TOTAL`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `<b>0</b>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `<b>${total_qty}</b>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += `<td colspan="2">`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += `<td>`;
                                tag_total += `</td>`;
                                tag_total += `<td>`;
                                tag_total += "</td>";
                                tag_total += "</tr>";
                                
                                $(".foot-detail-table").empty();
                                $(".foot-detail-table").append(tag_total);
                            
                        })
                    }
                })
            })
        }
    }

    const changePO = function() 
    {
        let tag_html = "";
        let tag_total = "";

        list_items.forEach((item) => {
            if(item.id)
            {
                list_delete.push(item)
            }
        })

        list_items = [];
        total_harga_barang = 0;
        total_qty = 0;
        total_berat = 0;
        total_harga = 0;
        row = 0;
        let arr = [$(".po_id option:selected").val()];

        $(".body-detail-table").empty();
        tag_total += `<tr>`;
        tag_total += `<td colspan="4">`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `GRAND TOTAL`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td>`;
        tag_total += `<b>0</b>`;
        tag_total += "</td>";
        tag_total += `<td colspan="4">`;
        tag_total += "</td>";
        tag_total += "</tr>";
        
        $(".foot-detail-table").empty();
        $(".foot-detail-table").append(tag_total);


        if ($(".po_id option:selected").val()) {
            if($(".po_type").val() === "LOKAL PENOLONG")
            {
                arr?.forEach((items) => {
                    $.ajax({
                        url: `<?= base_url("po-lokal-bahan-penolong/multi/dropdown"); ?>`,
                        method: "GET",
                        data: {
                            id: items
                        },
                        dataType: "json",
                        success: function(res) {
                            console.log(res)
                            
                            res.data.forEach(function(item) {
                                    list_items.push({
                                        id: '',
                                        row: row + 1,
                                        barang_id: item.barang_id,
                                        unit: item.unit,
                                        kode_barang: item.kode_barang,
                                        nama_barang: item.nama_barang,
                                        qty: Number(item.qty),
                                        nama_satuan: item.nama_satuan,
                                        harga: item.price,
                                        sub_total: item.totalPrice,
                                        note: item.note,
                                        po_no: item.po_no,
                                        disc: Number(item.disc),
                                        additional_cost: item.additional_cost,
                                        berat: 0,
                                        spec: item.spec
                                    })

                                    total_harga_barang = total_harga_barang + Number(item.price.replaceAll(",", ""));
                                    total_qty = total_qty + Number(item.qty);
                                    total_harga = total_harga + + Number(item.totalPrice.replaceAll(",", ""));

                                    tag_html += `<tr>`;
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.kode_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.nama_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.spec;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.po_no;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += 0;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += Number(item.qty);
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.price;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += Number(item.disc);
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.additional_cost;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.totalPrice;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.note;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `<button type='button' onclick='deleteRow(${row + 1})'>X</button>`;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    row = row + 1;

                                    $(".body-detail-table").empty();
                                    $(".body-detail-table").append(tag_html)

                                    tag_total = "";
                                    tag_total += `<tr>`;
                                    tag_total += `<td colspan="4">`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `GRAND TOTAL`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `<b>0</b>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `<b>${total_qty}</b>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                                    tag_total += "</td>";
                                    tag_total += `<td colspan="2">`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += "</tr>";
                                    
                                    $(".foot-detail-table").empty();
                                    $(".foot-detail-table").append(tag_total);
                                
                            })
                        }
                    })
                })
            }

            if($(".po_type").val() === "IMPORT PENOLONG")
            {
                arr?.forEach((items) => {
                    $.ajax({
                        url: `<?= base_url("po-import-bahan-penolong/multi/dropdown"); ?>`,
                        method: "GET",
                        data: {
                            id: items
                        },
                        dataType: "json",
                        success: function(res) {
                            console.log(res)
                            
                            res.data.forEach(function(item) {
                                    list_items.push({
                                        id: '',
                                        row: row + 1,
                                        barang_id: item.barang_id,
                                        unit: item.unit,
                                        kode_barang: item.kode_barang,
                                        nama_barang: item.nama_barang,
                                        qty: Number(item.qty),
                                        nama_satuan: item.nama_satuan,
                                        harga: item.price,
                                        sub_total: item.totalPrice,
                                        note: item.note,
                                        po_no: item.po_no,
                                        disc: Number(item.disc),
                                        additional_cost: item.additional_cost,
                                        berat: 0,
                                        spec: item.spec
                                    })

                                    total_harga_barang = total_harga_barang + Number(item.price.replaceAll(",", ""));
                                    total_qty = total_qty + Number(item.qty);
                                    total_harga = total_harga + + Number(item.totalPrice.replaceAll(",", ""));

                                    tag_html += `<tr>`;
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.kode_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.nama_barang;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.spec;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.po_no;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += 0;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += Number(item.qty);
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.nama_satuan;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.price;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += Number(item.disc);
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.additional_cost;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.totalPrice;
                                    tag_html += "</td>";
                                    tag_html += `<td class="edit-table-detail" data-total="${item.totalPrice}" data-spesifikasi="${item.spec}" data-berat="0" data-additional_cost="${item.additional_cost}" data-disc="${Number(item.disc)}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.totalPrice}" data-harga="${item.price}" data-qty="${Number(item.qty)}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-purchase_order_details_id="${item.id}" data-id="" data-row="${row + 1}">`;
                                    tag_html += item.note;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `<button type='button' onclick='deleteRow(${row + 1})'>X</button>`;
                                    tag_html += "</td>";
                                    tag_html += "</tr>";

                                    row = row + 1;

                                    $(".body-detail-table").empty();
                                    $(".body-detail-table").append(tag_html)

                                    tag_total = "";
                                    tag_total += `<tr>`;
                                    tag_total += `<td colspan="4">`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `GRAND TOTAL`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `<b>0</b>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `<b>${total_qty}</b>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                                    tag_total += "</td>";
                                    tag_total += `<td colspan="2">`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += `<td>`;
                                    tag_total += "</td>";
                                    tag_total += "</tr>";
                                    
                                    $(".foot-detail-table").empty();
                                    $(".foot-detail-table").append(tag_total);
                                
                            })
                        }
                    })
                })
            }
        } 
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
                total_berat = 0;

                row = 0;

                console.log(list_items)

                list_items.map(item => {
                    if (item.row != id) {
                        tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.kode_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.spec;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.po_no;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.berat;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.disc;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.additional_cost;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.sub_total;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.note;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `<button type='button' onclick='deleteRow(${row + 1})'>X</button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                            total_qty = total_qty + Number(item.qty);
                            total_harga = total_harga + Number(item.sub_total.replaceAll(",", ""));
                            total_berat = total_berat + Number(item.berat);

                        new_list_items.push({
                            ...item,
                            row: row + 1
                        });

                        row = row + 1;
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

                tag_total = "";
                tag_total += `<tr>`;
                tag_total += `<td colspan="4">`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `GRAND TOTAL`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `<b>${total_berat}</b>`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += `<td colspan="2">`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += `<td colspan="2">`;
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
        let additional_cost = $(this).data('additional_cost')
        let total = $(this).data('total')
        let disc = $(this).data('disc')
        let berat = $(this).data('berat')

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
        $(".berat").val(berat)

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

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;
                total_berat = 0;

                row = 0;

                console.log(list_items)

                list_items.map(item => {
                    if (item.row != id) {
                        tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += row + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.kode_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_barang;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.spec;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.po_no;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.berat;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.qty;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.nama_satuan;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.harga;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.disc;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.additional_cost;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.sub_total;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-total="${item.total}" data-spesifikasi="${item.spec}" data-berat="${item.berat}" data-additional_cost="${item.additional_cost}" data-disc="${item.disc}" data-barang_id="${item.barang_id}" data-unit="${item.unit}" data-keterangan="${item.note}" data-sub_total="${item.sub_total}" data-harga="${item.harga}" data-qty="${item.qty}" data-nama_satuan="${item.nama_satuan}" data-nama_barang="${item.nama_barang}" data-kode_barang="${item.kode_barang}" data-id="${item.id}" data-row="${row + 1}">`;
                            tag_html += item.note;
                            tag_html += "</td>";
                            tag_html += "<td>";
                            tag_html += `<button type='button' onclick='deleteRow(${row + 1})'>X</button>`;
                            tag_html += "</td>";
                            tag_html += "</tr>";

                            total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                            total_qty = total_qty + Number(item.qty);
                            total_harga = total_harga + Number(item.sub_total.replaceAll(",", ""));
                            total_berat = total_berat + Number(item.berat);

                        new_list_items.push({
                            ...item,
                            row: row + 1
                        });

                        row = row + 1;
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

                tag_total = "";
                tag_total += `<tr>`;
                tag_total += `<td colspan="4">`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `GRAND TOTAL`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `<b>${total_berat}</b>`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += `<td colspan="2">`;
                tag_total += "</td>";
                tag_total += `<td>`;
                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += `<td colspan="2">`;
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);
            }
        })
    }
</script>
<?= $this->endSection(); ?>