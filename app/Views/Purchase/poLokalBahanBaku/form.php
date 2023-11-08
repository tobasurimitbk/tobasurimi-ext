<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah PO Lokal Bahan Baku</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-lokal-bahan-baku"); ?>">
                Batal
            </a>
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
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSupplier)) : ?>
                                    <?php foreach ($dataSupplier as $supplier) : ?>
                                        <option <?= !empty($dataPOLokal) ? ($dataPOLokal->supplier_id === $supplier->id ? "selected" : "") : ""; ?> value="<?= $supplier->id; ?>" data-name="<?= $supplier->name; ?>"><?= $supplier->name; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-6">
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
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select barang_id" id="barang_id" name="barang_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataBarang)) {
                                    foreach ($dataBarang as $b) {
                                ?>
                                        <option value="<?= $b["id"]; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->barang_id === $b["id"] ? "selected" : "") : ""; ?>><?= $b["barang_name"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Bahan Baku</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> class="form-select company_id" id="company_id" name="company_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataCompany)) {
                                    foreach ($dataCompany as $company) {
                                ?>
                                        <option value="<?= $company["id"]; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->company_id === $company["id"] ? "selected" : "") : ""; ?>><?= $company["company"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Company</label>
                        </div>
                    </div>
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
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->cong_sebenarnya : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control cong_sebenarnya" name="cong_sebenarnya" id="cong_sebenarnya" placeholder="Cong Sebenarnya (Opsional)">
                            <label for="floatingInput">Cong Sebenarnya (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->cong_batasan : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control cong_batasan" name="cong_batasan" id="cong_batasan" placeholder="Cong Batasan (Opsional)">
                            <label for="floatingInput">Cong Batasan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataPOLokal) ? $dataPOLokal->subsidi_langsung : ""; ?>" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control subsidi_langsung" name="subsidi_langsung" id="subsidi_langsung" placeholder="Subsidi Langsung (Opsional)">
                            <label for="floatingInput">Tambahan Langsung (Opsional)</label>
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
            <form class="detail-form" role="form" method="POST" enctype="multipart/form-data" style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>">
                <input autocomplete="one-time-code" type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" />
                            <select onchange="changeSpesifikasi()" class="form-select spesifikasi" name="spesifikasi" id="spesifikasi" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSpesifikasi)) {
                                    foreach ($dataSpesifikasi as $spesifikasi) {
                                ?>
                                        <option data-satuan="<?= $spesifikasi["id_satuan"]; ?>" data-bagian="<?= $spesifikasi["bagian_id"]; ?>" data-umum="<?= number_format($spesifikasi["harga_umum"], 2, '.', ','); ?>" data-harian="<?= number_format($spesifikasi["harga_harian"], 2, '.', ','); ?>" data-bulanan="<?= number_format($spesifikasi["harga_bulanan"], 2, '.', ','); ?>" data-barang_id="<?= $spesifikasi["bahan_baku_id"]; ?>" data-nama="<?= $spesifikasi["barang_name"]; ?>" value="<?= $spesifikasi["supplier_harga_id"]; ?>"><?= $spesifikasi["spesifikasi"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Spesifikasi</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bagian" name="bagian" id="bagian" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataBagian)) {
                                    foreach ($dataBagian as $bagian) {
                                ?>
                                        <option value="<?= $bagian["id"]; ?>"><?= $bagian["nama_bagian"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Bagian</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select satuan" name="satuan" id="satuan" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataSatuan)) {
                                    foreach ($dataSatuan as $satuan) {
                                ?>
                                        <option value="<?= $satuan["id"]; ?>"><?= $satuan["nama_satuan"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Satuan</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control harga" name="harga" id="harga" placeholder="Harga Umum">
                            <label for="floatingInput">Harga Umum</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control daily_price" name="daily_price" id="daily_price" placeholder="Harga Harian">
                            <label for="floatingInput">Harga Harian</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control monthly_price" name="monthly_price" id="monthly_price" placeholder="Harga Bulanan">
                            <label for="floatingInput">Harga Bulanan</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                            <label for="floatingInput">QTY</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                            <label for="floatingInput">Total Harga</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control peti" name="peti" id="peti" placeholder="Peti">
                            <label for="floatingInput">Peti / Tong</label>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <textarea autocomplete="one-time-code" class="form-control keterangan text-area-all" name="keterangan" id="keterangan" placeholder="Keterangan (Opsional)"></textarea>
                            <label for="floatingInput">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal" style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit-detail">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="setBarang()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Spesifikasi</th>
                                <th>Bagian</th>
                                <th>Satuan</th>
                                <th>Umum</th>
                                <th>Harian</th>
                                <th>Bulanan</th>
                                <th>QTY</th>
                                <th>Peti</th>
                                <th>Kualitas</th>
                                <th style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                            <?php
                            $total_harga = 0;
                            $total_harian = 0;
                            $total_bulanan = 0;
                            $total_qty = 0;
                            $row = 0;

                            if (!empty($dataPOLokal)) {
                                foreach ($dataPOLokal->rm_purchase_order_details as $details) {
                                    $total_harga = $total_harga + ($details->general_price ? formatter(str_replace(",", "", $details->general_price), "STR_TO_FLOAT") : 0);
                                    $total_qty = $total_qty + $details->qty;
                                    $total = (($details->general_price ? formatter(str_replace(",", "", $details->general_price), "STR_TO_FLOAT") : 0) + ($details->daily_price ? formatter(str_replace(",", "", $details->daily_price), "STR_TO_FLOAT") : 0) + ($details->monthly_price ? formatter(str_replace(",", "", $details->monthly_price), "STR_TO_FLOAT") : 0)) * formatter($details->qty, "STR_TO_FLOAT");
                                    $total_harian = $total_harian + ($details->daily_price ? formatter(str_replace(",", "", $details->daily_price), "STR_TO_FLOAT") : 0);
                                    $total_bulanan = $total_bulanan + ($details->monthly_price ? formatter(str_replace(",", "", $details->monthly_price), "STR_TO_FLOAT") : 0);
                            ?>
                                    <tr>
                                        <td>
                                            <?= $row = $row + 1; ?>
                                        </td>
                                        <td>
                                            <?= $details->spesifikasi; ?>
                                        </td>
                                        <td>
                                            <?= $details->nama_bagian; ?>
                                        </td>
                                        <td>
                                            <?= $details->nama_satuan; ?>
                                        </td>
                                        <td>
                                            Rp <?= number_format(formatter($details->general_price, "STR_TO_FLOAT"), 2, '.', ','); ?>
                                        </td>
                                        <td>
                                            Rp <?= number_format(formatter($details->daily_price, "STR_TO_FLOAT"), 2, '.', ','); ?>
                                        </td>
                                        <td>
                                            Rp <?= number_format(formatter($details->monthly_price, "STR_TO_FLOAT"), 2, '.', ','); ?>
                                        </td>
                                        <td>
                                            <?= formatter($details->qty, "STR_TO_FLOAT"); ?>
                                        </td>
                                        <td>
                                            <?= $details->peti; ?>
                                        </td>
                                        <td>
                                            <?= $details->quality; ?>
                                        </td>
                                        <td style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>">
                                            <?php if ($dataPOLokal->is_posted === "0") { ?>
                                                <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-satuan="<?= $details->id_satuan; ?>" data data-spesifikasi="<?= $details->supplier_harga_id; ?>" data-harga="<?= number_format(formatter($details->general_price, "STR_TO_FLOAT"), 2, '.', ','); ?>" data-daily="<?= number_format(formatter($details->daily_price, "STR_TO_FLOAT"), 2, '.', ','); ?>" data-monthly="<?= number_format(formatter($details->monthly_price, "STR_TO_FLOAT"), 2, '.', ','); ?>" data-bagian="<?= $details->bagian; ?>" data-qty="<?= formatter($details->qty, "STR_TO_FLOAT"); ?>" data-total="<?= number_format(formatter($total, "STR_TO_FLOAT"), 2, '.', ',') ?>" data-keterangan="<?= $details->note; ?>" data-peti="<?= $details->peti; ?>" data-quality="<?= $details->quality; ?>" data-id="<?= $details->id; ?>" data-row="<?= $row; ?>">
                                                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                                </button><button class="btn btn-danger" onclick="deleteRow(<?= $row; ?>)">
                                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="3"></td>
                                <td><b>TOTAL</b></td>
                                <td><b><?= "Rp " . number_format(formatter($total_harga, "STR_TO_FLOAT"), 2, '.', ','); ?></b></td>
                                <td><b><?= "Rp " . number_format(formatter($total_harian, "STR_TO_FLOAT"), 2, '.', ','); ?></b></td>
                                <td><b><?= "Rp " . number_format(formatter($total_bulanan, "STR_TO_FLOAT"), 2, '.', ','); ?></b></td>
                                <td><b><?= $total_qty; ?></b></td>
                                <td colspan="2"></td>
                                <td style="<?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? "display: none;" : "") : ""; ?>"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let list_delete = [];
    var row = 0;
    var total_qty = 0;
    var total_harga = 0;
    var total_harian = 0;
    var total_bulanan = 0;
    var priceEdit = 0;
    let trigger = true

    <?php if (!empty($dataPOLokal)) {
        foreach ($dataPOLokal->rm_purchase_order_details as $details) {
    ?>
            priceEdit = Number('<?= $details->general_price; ?>'.replaceAll(",", ""));
            harianEdit = Number('<?= $details->daily_price; ?>'.replaceAll(",", ""));
            bulananEdit = Number('<?= $details->monthly_price; ?>'.replaceAll(",", ""));
            row = row + 1;

            total_qty = total_qty + <?= $details->qty; ?>;
            total_harga = total_harga + priceEdit;
            total_harian = total_harian + harianEdit;
            total_bulanan = total_bulanan + bulananEdit;

            list_items.push({
                id: <?= $details->id; ?>,
                row: row,
                satuan: '<?= $details->id_satuan; ?>',
                satuanName: '<?= $details->nama_satuan; ?>',
                spesifikasi: '<?= $details->supplier_harga_id; ?>',
                spesifikasiName: '<?= $details->spesifikasi; ?>',
                harga: '<?= number_format(formatter($details->general_price, "STR_TO_FLOAT"), 2, '.', ','); ?>',
                qty: Number('<?= $details->qty; ?>'),
                total: '<?= number_format((($details->monthly_price ? formatter(str_replace(",", "", $details->monthly_price), "STR_TO_FLOAT") : 0) + ($details->daily_price ? formatter(str_replace(",", "", $details->daily_price), "STR_TO_FLOAT") : 0) + ($details->general_price ? formatter(str_replace(",", "", $details->general_price), "STR_TO_FLOAT") : 0)) * formatter($details->qty, "STR_TO_FLOAT"), 2, '.', ','); ?>',
                keterangan: '<?= $details->note; ?>',
                bagian_id: '<?= $details->bagian; ?>',
                bagian_name: '<?= $details->nama_bagian; ?>',
                peti: '<?= $details->peti; ?>',
                quality: '<?= $details->quality; ?>',
                daily_price: '<?= number_format(formatter($details->daily_price, "STR_TO_FLOAT"), 2, '.', ','); ?>',
                monthly_price: '<?= number_format(formatter($details->monthly_price, "STR_TO_FLOAT"), 2, '.', ','); ?>',
            })
        <?php
        }
        ?>
    <?php
    } ?>

    var validator_detail = $(".detail-form").validate({
        rules: {
            spesifikasi: {
                required: true
            },
            bagian: {
                required: true
            },
            satuan: {
                required: true
            },
            peti: {
                required: true
            },
            quality: {
                required: true
            },
            qty: {
                required: true
            },
            harga: {
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
            spesifikasi: {
                required: "Spesifikasi wajib diisi"
            },
            bagian: {
                required: "Bagian wajib diisi"
            },
            satuan: {
                required: "Satuan wajib diisi"
            },
            peti: {
                required: "Peti wajib diisi"
            },
            quality: {
                required: "Kualitas wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            harga: {
                required: "Harga Umum wajib diisi"
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

    const changeSpesifikasi = function() {
        if (trigger) {
            if ($(".spesifikasi option:selected").val()) {
                let bagian = $(".spesifikasi option:selected").data("bagian") ? $(".spesifikasi option:selected").data("bagian") : "";
                let umum = $(".spesifikasi option:selected").data("umum") ? $(".spesifikasi option:selected").data("umum") : "";
                let harian = $(".spesifikasi option:selected").data("harian") ? $(".spesifikasi option:selected").data("harian") : "";
                let bulanan = $(".spesifikasi option:selected").data("bulanan") ? $(".spesifikasi option:selected").data("bulanan") : "";
                let satuan = $(".spesifikasi option:selected").data("satuan") ? $(".spesifikasi option:selected").data("satuan") : "";

                $(".bagian").val(Number(bagian)).change();
                $(".harga").val(umum.replaceAll(",", ""));
                $(".daily_price").val(harian.replaceAll(",", ""));
                $(".monthly_price").val(bulanan.replaceAll(",", ""));
                $(".satuan").val(satuan).change();
                $(".qty").val("");
                $(".total").val("");
            } else {
                $(".bagian").val("").change();
                $(".harga").val("");
                $(".daily_price").val("");
                $(".monthly_price").val("");
                $(".satuan").val("").change();
                $(".qty").val("");
                $(".total").val("");
            }
        } else {
            trigger = true
        }
    }

    $(document).ready(function() {
        $(".po_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        // BC Type
        $('.bc_type').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        // COMPANY ID
        $('.company_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        // PURCHASE REQUEST ID
        $('.purchase_request_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        // BARANG ID
        $('.barang_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        // SPESIFIKASI
        $('.spesifikasi').select2({
            placeholder: "Pilih Spesifikasi (Supplier & Bahan Baku required)",
            theme: "bootstrap-5",
            allowClear: true
        })

        // SATUAN
        $('.satuan').select2({
            placeholder: "Pilih Satuan",
            theme: "bootstrap-5"
        })

        // SUPPLIER
        $('.supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        // BAGIAN
        $('.bagian').select2({
            placeholder: "Pilih Bagian",
            theme: "bootstrap-5"
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

        var validator = $(".create-form").validate({
            rules: {
                po_date: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                barang_id: {
                    required: true
                },
                po_no: {
                    required: true
                },
                company_id: {
                    required: true
                },
                pph: {
                    required: true
                },
                subsidi_langsung: {
                    required: true
                },
                cong_sebenarnya: {
                    required: true
                },
                cong_batasan: {
                    required: true
                }
            },
            messages: {
                po_date: {
                    required: "Tanggal Dibuat wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                barang_id: {
                    required: "Barang wajib diisi"
                },
                po_no: {
                    required: "No PO wajib diisi"
                },
                company_id: {
                    required: "Company wajib diisi"
                },
                pph: {
                    required: "PPH wajib diisi"
                },
                subsidi_langsung: {
                    required: "Subsidi Langsung wajib diisi"
                },
                cong_sebenarnya: {
                    required: "Cong Sebenarnya wajib diisi"
                },
                cong_batasan: {
                    required: "Cong Batasan wajib diisi"
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

        $(".barang_id").change(function() {
            $.ajax({
                url: `<?= base_url("supplier-harga/barang-and-supplier"); ?>`,
                method: "GET",
                data: {
                    barang_id: $(".barang_id option:selected").val(),
                    supplier_id: $(".supplier_id option:selected").val()
                },
                dataType: "json",
                success: function(res) {
                    $(".spesifikasi").empty();

                    $(".spesifikasi").append(`<option data-satuan="" data-bagian="" data-umum="" data-harian="" data-bulanan="" data-nama="" value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".spesifikasi").append(`<option data-satuan="${item.id_satuan}" data-bagian="${item.bagian_id}" data-umum="${Number(item.harga_umum).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-harian="${Number(item.harga_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-bulanan="${Number(item.harga_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-barang_id="${item.bahan_baku_id}" data-nama="${item.barang_name}" value="${item.supplier_harga_id}">${item.spesifikasi}</option>`);
                    })

                    $(".spesifikasi").val("").change();
                }
            })

            list_items.map((item) => {
                if (item.id) {
                    list_delete.push(item);
                }
            })

            $(".body-detail-table").empty()

            row = 0;

            total_qty = 0;
            total_harga = 0;
            total_harian = 0;
            total_bulanan = 0;

            list_items = []

            $(".foot-detail-table").empty()

            let tag_total = ""

            tag_total += `<tr>`;
            tag_total += "<td colspan='3'>";
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += "<b>TOTAL</b>";
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += `<b>${"Rp " + Number(total_harga).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += `<b>${"Rp " + Number(total_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += `<b>${"Rp " + Number(total_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += `<b>${total_qty}</b>`;
            tag_total += "</td>";
            tag_total += "<td colspan='3'>";
            tag_total += "</td>";
            tag_total += "</tr>";

            $(".foot-detail-table").append(tag_total);
        })

        $(".supplier_id").change(function() {
            $(".spesifikasi").empty();

            $(".spesifikasi").append(`<option data-bagian="" data-umum="" data-harian="" data-bulanan="" data-nama="" value=""></option>`);

            $(".spesifikasi").val("").change();

            list_items.map((item) => {
                if (item.id) {
                    list_delete.push(item);
                }
            })

            $(".body-detail-table").empty()

            row = 0;

            total_qty = 0;
            total_harga = 0;
            total_harian = 0;
            total_bulanan = 0;

            list_items = []

            $(".foot-detail-table").empty()

            let tag_total = ""

            tag_total += `<tr>`;
            tag_total += "<td colspan='3'>";
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += "<b>TOTAL</b>";
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += `<b>${"Rp " + Number(total_harga).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += `<b>${"Rp " + Number(total_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += `<b>${"Rp " + Number(total_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
            tag_total += "</td>";
            tag_total += "<td>";
            tag_total += `<b>${total_qty}</b>`;
            tag_total += "</td>";
            tag_total += "<td colspan='3'>";
            tag_total += "</td>";
            tag_total += "</tr>";

            $(".foot-detail-table").append(tag_total);

            if ($(".supplier_id option:selected").val()) {
                $.ajax({
                    url: `<?= base_url("barang/supplier"); ?>/` + $(".supplier_id option:selected").val(),
                    method: "GET",
                    dataType: "json",
                    success: function(res) {
                        $(".barang_id").val('').change()
                        $(".barang_id").empty()
                        $(".barang_id").append(`<option value=""></option>`)
                        res?.data?.map((item) => {
                            $(".barang_id").append(`<option value="${item?.id}">${item?.barang_name}</option>`)
                        })
                    }
                })
            } else {
                $(".barang_id").val('').change()
                $(".barang_id").empty()
                $(".barang_id").append(`<option value=""></option>`)
            }
        })

        $(".qty, .harga, .daily_price, .monthly_price").keyup(function() {
            var qty = $(".qty").val() ? Number($(".qty").val()) : 0;

            var gabungan_harga = Number($(".harga").val()) + Number($(".daily_price").val()) + Number($(".monthly_price").val());

            let total = (gabungan_harga * qty);
            $(".total").val(total);
        })

        $(document).on('click', '.edit-table-detail', function(evt) {
            $(".title-detail-name").text("Update")
            $(".delete-detail").css('display', '');
            let harga = $(this).data('harga')
            let daily = $(this).data('daily')
            let monthly = $(this).data('monthly')
            let bagian = $(this).data('bagian')
            let satuan = $(this).data('satuan')
            let qty = $(this).data('qty')
            let total = $(this).data('total')
            let peti = $(this).data('peti')
            let quality = $(this).data('quality')
            let keterangan = $(this).data('keterangan')
            let spesifikasi = $(this).data('spesifikasi')
            let rowid = $(this).data('row')
            let id = $(this).data('id')

            validator_detail.resetForm();
            validator_detail.reset();

            $(".id_detail").val(rowid);

            $(".qty").val(Number(qty))
            $(".harga").val(harga.replaceAll(",", ""))
            $(".daily_price").val(daily.replaceAll(",", ""))
            $(".monthly_price").val(monthly.replaceAll(",", ""))
            $(".quality").val(quality)
            $(".peti").val(peti)
            $(".total").val(Number(total.replaceAll(",", "")))
            $(".keterangan").val(keterangan)
            trigger = false;
            $(".spesifikasi").val(Number(spesifikasi)).change();

            $(".bagian").val(Number(bagian)).change();
            $(".satuan").val(Number(satuan)).change();
            trigger = true;
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
                // validate input
                let validate_item = false;

                if (list_items.length === 0) {
                    validate_item = true;
                }

                if (validate_item) {
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
                                            satuan_id: obj.satuan ? Number(obj.satuan) : 0,
                                            supplier_harga_id: obj.spesifikasi ? Number(obj.spesifikasi) : 0,
                                            bagian: obj.bagian_id ? Number(obj.bagian_id) : 0,
                                            peti: obj.peti,
                                            quality: obj.quality,
                                            note: obj.keterangan,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            daily_price: obj.daily_price ? Number(obj.daily_price.replaceAll(",", "")) : 0,
                                            monthly_price: obj.monthly_price ? Number(obj.monthly_price.replaceAll(",", "")) : 0,
                                            general_price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                            isDeleted: true
                                        })
                                    })
                                }

                                list_items.map(obj => {
                                    if (obj.id) {
                                        update_list_items.push({
                                            id: obj.id ? Number(obj.id) : 0,
                                            satuan_id: obj.satuan ? Number(obj.satuan) : 0,
                                            supplier_harga_id: obj.spesifikasi ? Number(obj.spesifikasi) : 0,
                                            bagian: obj.bagian_id ? Number(obj.bagian_id) : 0,
                                            peti: obj.peti,
                                            quality: obj.quality,
                                            note: obj.keterangan,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            daily_price: obj.daily_price ? Number(obj.daily_price.replaceAll(",", "")) : 0,
                                            monthly_price: obj.monthly_price ? Number(obj.monthly_price.replaceAll(",", "")) : 0,
                                            general_price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0
                                        })
                                    } else {
                                        update_list_items.push({
                                            supplier_harga_id: obj.spesifikasi ? Number(obj.spesifikasi) : 0,
                                            satuan_id: obj.satuan ? Number(obj.satuan) : 0,
                                            bagian: obj.bagian_id ? Number(obj.bagian_id) : 0,
                                            peti: obj.peti,
                                            quality: obj.quality,
                                            note: obj.keterangan,
                                            qty: obj.qty ? Number(obj.qty) : 0,
                                            daily_price: obj.daily_price ? Number(obj.daily_price.replaceAll(",", "")) : 0,
                                            monthly_price: obj.monthly_price ? Number(obj.monthly_price.replaceAll(",", "")) : 0,
                                            general_price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0
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
                                                        window.open('<?= base_url("po-lokal-bahan-baku/print") ?>/' + id, "_blank")
                                                        window.location.href = "<?= base_url("po-lokal-bahan-baku"); ?>";
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
                                                        window.open('<?= base_url("po-lokal-bahan-baku/print") ?>/' + response.id, "_blank")
                                                        window.location.href = "<?= base_url("po-lokal-bahan-baku"); ?>";
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
            }
        })

        $(".posting-po").click(function() {
            // validate input
            let validate_item = false;

            if (list_items.length === 0) {
                validate_item = true;
            }

            if (validate_item) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            } else {
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
                                            window.location.href = "<?= base_url("po-lokal-bahan-baku"); ?>";
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

            let bagian = $(".bagian option:selected").val()
            let bagianName = $(".bagian option:selected").text()
            let spesifikasi = $(".spesifikasi option:selected").val()
            let spesifikasiName = $(".spesifikasi option:selected").text()
            let satuan = $(".satuan option:selected").val()
            let satuanName = $(".satuan option:selected").text()
            let peti = $(".peti").val()
            let quality = $(".quality").val()
            let harga = $(".harga").val()
            let daily_price = $(".daily_price").val()
            let qty = $(".qty").val()
            let total = $(".total").val()
            let monthly_price = $(".monthly_price").val()
            let keterangan = $(".keterangan").val()

            let validate_same = false;

            list_items.map(item => {
                if (spesifikasi !== '') {
                    if (item.spesifikasi == spesifikasi) {
                        // kalau edit barang, barang tidak ganti tidak kena validasi
                        if (row_detail === item.row) {
                            validate_same = false;
                        } else {
                            validate_same = true;
                        }
                    }
                }
            })

            if (validate_same) {
                Swal.fire({
                    icon: 'error',
                    title: "Spesifikasi Sudah Ada",
                    confirmButtonColor: '#4e73df',
                })
            } else {
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
                            // update detail
                            if (row_detail) {
                                let new_list_items = []
                                let tag_html = "";
                                let tag_total = "";

                                row = 0;

                                $(".body-detail-table").empty()

                                total_qty = 0;
                                total_harga = 0;
                                total_harian = 0;
                                total_bulanan = 0;

                                list_items.map(item => {
                                    if (item.row == row_detail) {
                                        tag_html += `<tr>`;
                                        tag_html += `<td>`;
                                        tag_html += row + 1;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += spesifikasiName;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += bagianName;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += satuanName;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += "Rp " + Number(harga).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += "Rp " + Number(daily_price).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += "Rp " + Number(monthly_price).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += qty;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += peti;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += quality;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += `
                                        <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${Number(harga).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" 
                                        data-daily="${Number(daily_price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-monthly="${Number(monthly_price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-bagian="${bagian}" data-qty="${qty}" data-total="${Number(total).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-keterangan="${keterangan}" data-peti="${peti}" data-quality="${quality}" data-id="" data-row="${row + 1}">
                                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                        </button><button class="btn btn-danger" onclick="deleteRow(${row + 1})">
                                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                        </button>`;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_list_items.push({
                                            id: item.id,
                                            row: row + 1,
                                            spesifikasi: spesifikasi,
                                            spesifikasiName: spesifikasiName,
                                            satuan: satuan,
                                            satuanName: satuanName,
                                            bagian_id: bagian,
                                            bagian_name: bagianName,
                                            harga: Number(harga).toLocaleString(undefined, {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }),
                                            daily_price: Number(daily_price).toLocaleString(undefined, {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }),
                                            monthly_price: Number(monthly_price).toLocaleString(undefined, {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }),
                                            qty: qty,
                                            total: Number(total).toLocaleString(undefined, {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }),
                                            peti: peti,
                                            quality: quality,
                                            keterangan: keterangan
                                        });

                                        row = row + 1;

                                        total_qty = total_qty + Number(qty);
                                        total_harga = total_harga + Number(harga);
                                        total_harian = total_harian + Number(daily_price);
                                        total_bulanan = total_bulanan + Number(monthly_price);
                                    } else {
                                        tag_html += `<tr>`;
                                        tag_html += `<td>`;
                                        tag_html += row + 1;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += item.spesifikasiName;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += item.bagian_name;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += item.satuanName;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += "Rp " + item.harga;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += "Rp " + item.daily_price;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += "Rp " + item.monthly_price;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += item.qty;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += item.peti;
                                        tag_html += "</td>";
                                        tag_html += `<td>`;
                                        tag_html += item.quality;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += `
                                        <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" 
                                        data-daily="${item.daily_price}" data-monthly="${item.monthly_price}" data-bagian="${item.bagian_id}" data-qty="${item.qty}" data-total="${item.total}" data-keterangan="${item.keterangan}" data-peti="${item.peti}" data-quality="${item.quality}" data-id="${item.id}" data-row="${row + 1}">
                                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                        </button><button class="btn btn-danger" onclick="deleteRow(${row + 1})">
                                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                        </button>`;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_list_items.push(item);

                                        row = row + 1;

                                        total_qty = total_qty + Number(item.qty);
                                        total_harga = total_harga + Number(item.harga.replaceAll(",", ""));
                                        total_harian = total_harian + Number(item.daily_price.replaceAll(",", ""));
                                        total_bulanan = total_bulanan + Number(item.monthly_price.replaceAll(",", ""));
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
                                tag_total += `<b>${"Rp " + Number(total_harga).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${"Rp " + Number(total_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${"Rp " + Number(total_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_qty}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td colspan='3'>";
                                tag_total += "</td>";
                                tag_total += "</tr>";

                                $(".foot-detail-table").append(tag_total);

                                reset()
                            }
                            // create detail
                            else {
                                if ($(".detail-form").valid()) {
                                    list_items.push({
                                        id: '',
                                        row: row + 1,
                                        spesifikasi: spesifikasi,
                                        spesifikasiName: spesifikasiName,
                                        satuan: satuan,
                                        satuanName: satuanName,
                                        bagian_id: bagian,
                                        bagian_name: bagianName,
                                        harga: Number(harga).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }),
                                        daily_price: Number(daily_price).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }),
                                        monthly_price: Number(monthly_price).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }),
                                        qty: qty,
                                        total: Number(total).toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }),
                                        peti: peti,
                                        quality: quality,
                                        keterangan: keterangan
                                    })

                                    total_qty = total_qty + Number(qty);
                                    total_harga = total_harga + Number(harga);
                                    total_harian = total_harian + Number(daily_price);
                                    total_bulanan = total_bulanan + Number(monthly_price);

                                    let tag_html = "";
                                    let tag_total = "";

                                    tag_html += `<tr>`;
                                    tag_html += `<td>`;
                                    tag_html += row + 1;
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += spesifikasiName;
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += bagianName;
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += satuanName;
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += "Rp " + Number(harga).toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += "Rp " + Number(daily_price).toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += "Rp " + Number(monthly_price).toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += qty;
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += peti;
                                    tag_html += "</td>";
                                    tag_html += `<td>`;
                                    tag_html += quality;
                                    tag_html += "</td>";
                                    tag_html += "<td>";
                                    tag_html += `
                                    <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-satuan="${satuan}" data-spesifikasi="${spesifikasi}" data-harga="${Number(harga).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" 
                                    data-daily="${Number(daily_price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-monthly="${Number(monthly_price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-bagian="${bagian}" data-qty="${qty}" data-total="${Number(total).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}" data-keterangan="${keterangan}" data-peti="${peti}" data-quality="${quality}" data-id="" data-row="${row + 1}">
                                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                    </button><button class="btn btn-danger" onclick="deleteRow(${row + 1})">
                                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                    </button>`;
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
                                    tag_total += `<b>${"Rp " + Number(total_harga).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                                    tag_total += "</td>";
                                    tag_total += "<td>";
                                    tag_total += `<b>${"Rp " + Number(total_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                                    tag_total += "</td>";
                                    tag_total += "<td>";
                                    tag_total += `<b>${"Rp " + Number(total_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                                    tag_total += "</td>";
                                    tag_total += "<td>";
                                    tag_total += `<b>${total_qty}</b>`;
                                    tag_total += "</td>";
                                    tag_total += "<td colspan='3'>";
                                    tag_total += "</td>";
                                    tag_total += "</tr>";

                                    $(".foot-detail-table").append(tag_total);

                                    reset()
                                    row = row + 1;
                                }
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

                total_qty = 0;
                total_harga = 0;
                total_harian = 0;
                total_bulanan = 0;

                list_items.map(item => {
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td>`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += item.spesifikasiName;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += item.bagian_name;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += item.satuanName;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += "Rp " + item.harga;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += "Rp " + item.daily_price;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += "Rp " + item.monthly_price;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += item.peti;
                        tag_html += "</td>";
                        tag_html += `<td>`;
                        tag_html += item.quality;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `
                        <button class="btn btn-warning posting-spp mr-1 edit-table-detail" data-satuan="${item.satuan}" data-spesifikasi="${item.spesifikasi}" data-harga="${item.harga}" 
                        data-daily="${item.daily_price}" data-monthly="${item.monthly_price}" data-bagian="${item.bagian_id}" data-qty="${item.qty}" data-total="${item.total}" data-keterangan="${item.keterangan}" data-peti="${item.peti}" data-quality="${item.quality}" data-id="${item.id}" data-row="${row + 1}">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button><button class="btn btn-danger" onclick="deleteRow(${row + 1})">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            ...item,
                            row: row + 1
                        });

                        row = row + 1;

                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.harga.replaceAll(",", ""));
                        total_harian = total_harian + Number(item.daily_price.replaceAll(",", ""));
                        total_bulanan = total_bulanan + Number(item.monthly_price.replaceAll(",", ""));
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
                tag_total += `<b>${"Rp " + Number(total_harga).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${"Rp " + Number(total_harian).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${"Rp " + Number(total_bulanan).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += "<td colspan='3'>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                reset()
            }
        })
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

    let reset = function() {
        $(".id_detail").val('');

        $(".qty").val('')
        $(".harga").val('')
        $(".daily_price").val('')
        $(".monthly_price").val('')
        $(".quality").val('Baik')
        $(".peti").val('')
        $(".total").val('')
        $(".keterangan").val('')
        $(".satuan").val("").change()
        $(".spesifikasi").val("").change()
        $(".bagian").val('').change()

        validator_detail.resetForm();
        validator_detail.reset();
    }

    const setBarang = function() {
        reset()
    }

    const print = function(url) {
        window.open(url, "_blank");
    }
</script>
<?= $this->endSection(); ?>