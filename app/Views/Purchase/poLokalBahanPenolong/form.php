<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($poDetail) ? "Update PO Lokal Bahan Penolong" : "Tambah PO Lokal Bahan Penolong" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-lokal-bahan-penolong"); ?>">
                Kembali
            </a>
            <?php if (!empty($poDetail)) : ?>
                <?php if (!$poDetail['is_posted']) : ?>
                    <?php if (can('Pembelian', 'PO Lokal BP', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php endif ?>
                <?php if (can('Pembelian', 'PO Lokal BP', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("po-lokal-bahan-penolong/print/"); ?><?= encrypt($poDetail['id']) ?>')">
                        Print
                    </button>
                <?php endif; ?>
                <?php if (!$poDetail['is_posted']) : ?>
                    <?php if (can('Pembelian', 'PO Lokal BP', 'a')) : ?>
                        <button data-status="1" class="btn btn-success posting-spp float-right posting-po">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Pembelian', 'PO Lokal BP', 'ua') && !$poDetail['status_penerimaan'] && !$unPosting) : ?>
                        <button data-status="0" class="btn btn-success posting-spp float-right posting-po">
                            Un Posting
                        </button>
                    <?php endif; ?>
                <?php endif ?>
                <?php if ($poDetail['is_posted']) : ?>
                    <?php if (!$poDetail['status_penerimaan']) : ?>
                        <?php if (can('Pembelian', 'PO Lokal BP', 'a')) : ?>
                            <button class="btn btn-hapus close-parent float-right">
                                Close PO
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif ?>
            <?php if (!empty($poDetail)) : ?>
                <?php if (!$poDetail['is_posted']) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data PO</label>
                </div>
            </div>
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" <?= !empty($poDetail) ? 'value="' . encrypt($poDetail['id']) . '"' : '' ?> />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker po_date" <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'readonly' : '') : ''  ?> id="po_date" name="po_date" placeholder="Tanggal Dibuat" <?= !empty($poDetail) ? 'value="' . formatYMDtoDMY($poDetail['po_date']) . '"' : 'value="' . formatYMDtoDMY($today) . '"' ?>>
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($poDetail) ? ($poDetail['is_posted'] == "1" ? 'disabled=true' : '') : ''; ?> type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="No. PO" <?= !empty($poDetail) ?  ' value="' . $poDetail['po_no'] . '"' : '' ?>>
                                    <label for="floatingInput">No. PO</label>
                                </div>
                                <div <?= !empty($poDetail) ? 'style="display:none;"' : ''; ?> class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 25px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> class="form-select division_id" id="division_id" name="division_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($poDetail) ? ($poDetail['division_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Departemen</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($supplier as $s) : ?>
                                    <option value="<?= $s['id'] ?>">
                                        <?= strtoupper($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> class="form-select spp_id" id="spp_id" name="spp_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($dataListSPP)) : ?>
                                    <?php foreach ($dataListSPP as $d) : ?>
                                        <option <?= !empty($poDetail) ? ($poDetail['purchase_request_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['spp_no'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">SPP</label>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" value="<?= !empty($poDetail) ? formatYMDtoDMY($poDetail['payment_date']) : formatYMDtoDMY($today) ?>" class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Pembayaran">
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-payment-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> class="form-select status_closed_spp" name="status_closed_spp" id="status_closed_spp">
                                <option value=""></option>
                                <option <?= !empty($poDetail) ? ($poDetail['status_closed_spp'] == "0" ? 'selected' : '') : '' ?> value="0">OPEN SPP</option>
                                <option <?= !empty($poDetail) ? ($poDetail['status_closed_spp'] == "1" ? 'selected' : '') : '' ?> value="1">CLOSE SPP</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tutup SPP</label>
                        </div>
                        <small class="mb-3 mt-1"><i>Status Open Berarti SPP Masih Bisa Digunakan Kembali, Status Close Berarti SPP Tidak Dapat Digunakan Kembali</i></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" value="<?= !empty($poDetail) ? $poDetail['note'] : '' ?>" type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                            <label for="floatingInput">Catatan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> class="form-select ppn" name="ppn" id="ppn" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($ppn as $p) : ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= $p['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih PPN (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-1">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                </div>
            </div>
            <form class="detail-form" role="form" method="POST" enctype="multipart/form-data" style="<?= !empty($poDetail) ? ($poDetail['is_posted'] ? "display: none;" : "") : ""; ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select disabled class="form-select barang_id" id="barang_id" name="barang_id" aria-label="Floating label select example">
                                <option data-barang_id="" data-parent_name="" data-spesifikasi_id="" data-spesifikasi_name="" data-satuan_id="" data-nama_barang="" data-kode_barang="" value=""></option>
                                <?php foreach ($barang as $s) : ?>
                                    <option
                                        data-barang_id="<?= $s['id'] ?>"
                                        data-parent_name="<?= $s['parent_name'] ?>"
                                        data-spesifikasi_id="<?= $s['barang_master_spesifikasi_id'] ?>"
                                        data-spesifikasi_name="<?= $s['spesifikasi']  ?>"
                                        data-satuan_id="<?= $s['satuan_1'] ?>"
                                        data-satuan_2="<?= $s['satuan_2'] ?>"
                                        data-satuan_3="<?= $s['satuan_3'] ?>"
                                        data-nama_barang="<?= $s['barang_name_master'] ?>"
                                        data-kode_barang="<?= $s['kode_barang'] ?>"
                                        value="<?= $s['barang_master_spesifikasi_id'] ?>">
                                        <?= $s['kode_barang'] . " { " . $s['barang_name_master'] . " - " . $s['spesifikasi'] . " }" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Kode Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="hidden" name="id_detail" id="id_detail" class="id_detail">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang">
                            <label for="floatingInput">Nama Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control nama_kategori" id="nama_kategori" name="nama_kategori">
                            <label for="floatingInput">Kategori Barang</label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select class="form-select satuan_id" name="satuan_id" id="satuan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($satuan as $s) : ?>
                                    <option
                                        data-nama_satuan="<?= $s['nama_satuan'] ?>"
                                        data-kode_satuan="<?= $s['kode_satuan'] ?>"
                                        value="<?= $s['id'] ?>">
                                        <?= $s['kode_satuan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Satuan</label>
                        </div>
                        <small class="mb-4 mt-1">
                            <i>
                                Jika ingin menggunakan satuan yang lain, pastikan anda sudah mengatur satuannya di menu master barang
                            </i>
                        </small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control harga_satuan" name="harga_satuan" id="harga_satuan" placeholder="Harga Satuan" onkeyup="this.value = greatFormatRupiah(this.value)">
                            <label for="floatingInput">Harga Satuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <input <?= isset($checkLpb) ? ($checkLpb != null ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                            <label for="floatingInput">QTY</label>
                        </div>
                        <?php if (isset($checkLpb)): ?>
                            <?php if ($checkLpb != null) : ?>
                                <small>
                                    <i>
                                        PO Sudah dibuatkan LPB dengan nomor <b><?= $checkLpb['no_penerimaan_barang'] ?></b>, sehingga anda hanya diizinkan update harga saja
                                    </i>
                                </small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" min="0" max="100" type="number" class="form-control diskon" required value="0" name="diskon" id="diskon" placeholder="Discount (%)">
                            <label for="floatingInput">Diskon (%)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select name="additional_cost_type" class="form-select additional_cost_type" id="additional_cost_type">
                                        <option value=""></option>
                                        <option value="PLUS">(+)</option>
                                        <option value="MINUS">(-)</option>
                                    </select>
                                    <label for="floatingInput">Pilih</label>
                                </div>
                            </div>
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control biaya_tambahan" name="biaya_tambahan" id="biaya_tambahan" placeholder="Biaya Tambahan" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Biaya Tambahan / Pengurang (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control total" name="total" id="total" placeholder="Total" onkeyup="this.value = greatFormatRupiah(this.value)">
                            <label for="floatingInput">Total</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                            <label for="floatingInput">Keterangan (Opsional)</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select pph" name="pph" id="pph" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($pph as $p) : ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= $p['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih PPH (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal" style="<?= !empty($poDetail) ? ($poDetail['is_posted'] ? "display: none;" : "") : ""; ?>">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit-detail">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i> Update
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetForm()">
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
                                <th style="text-align: center; width:10px">No.</th>
                                <th style="text-align: center;">Kode</th>
                                <th style="text-align: center;">Barang</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">Harga Satuan</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: center;">Diskon (%)</th>
                                <th style="text-align: center;">Tambahan</th>
                                <th style="text-align: center;">Total</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody style="text-align:center;">
                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td style="text-align: right;" colspan="8">
                                    <b>TOTAL</b>
                                </td>
                                <td style="text-align: center;">
                                    <b>0.00</b>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (empty($poDetail)) : ?>
    <script>
        $(document).ready(function() {
            changeStatus();
        });
    </script>
<?php else: ?>

<?php endif; ?>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    // init barang list
    var listBarang = [];
    var totalHarga = 0;
    // init select

    $('#satuan_id').select2({
        placeholder: "Pilih Satuan",
        theme: "bootstrap-5",
        allowClear: true
    })

    $('#division_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('#status_closed_spp').select2({
        placeholder: "Pilih Status SPP",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('#spp_id').select2({
        placeholder: "Pilih Nomor SPP",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getDetailSPP();
        listBarang = [];
        totalHarga = 0;
        drawTabel(listBarang);
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $('#division_id').change(function() {
        getListSPP();
    });

    $('#barang_id').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selectedNamaBarang = $('#barang_id option:selected').text();
        var selected = $('#barang_id option:selected');
        activeFieldSatuanId(
            selected.data('satuan_id'),
            selected.data('satuan_2'),
            selected.data('satuan_3')
        );

        $('#nama_kategori').val($(this).find("option:selected").data("parent_name"));

        try {
            const match = selectedNamaBarang.match(/\{([^}]*)\}/);
            if (!match) {
                $('#nama_barang').val($(this).find("option:selected").data("nama_barang") + " - " + $(this).find("option:selected").data("spesifikasi_name"));
            }
            $('#nama_barang').val(match[1]);
        } catch (error) {
            $('#nama_barang').val($(this).find("option:selected").data("nama_barang") + " - " + $(this).find("option:selected").data("spesifikasi_name"));
        }
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $("#po_date,#payment_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    // HARGA SATUAN DAN QTY CHANE
    $('#harga_satuan,#qty,#biaya_tambahan,#diskon').keyup(function() {
        var hargaSatuan = parseFloat(destroyFormatRupiah($('#harga_satuan').val())) || 0;
        var qty = parseFloat($('#qty').val()) || 1;
        var biayaTambahan = parseFloat(destroyFormatRupiah($('#biaya_tambahan').val())) || 0;
        var diskon = parseFloat($('#diskon').val()) || 0;
        var diskonHarga = (diskon / 100) * (hargaSatuan * qty);
        var additionalCostType = $('#additional_cost_type option:selected').val();

        if (additionalCostType == '') {
            biayaTambahan = 0;
        } else if (additionalCostType == "MINUS") {
            biayaTambahan = biayaTambahan * -1;
        }

        var total = (((hargaSatuan * qty) - diskonHarga) + biayaTambahan);
        $('#total').val(total == 0 ? '' : greatFormatRupiah(total.toFixed(2)));
    });

    $('#additional_cost_type').change(function() {
        var additionalCostType = $('#additional_cost_type option:selected').val();
        var biayaTambahan = parseFloat(destroyFormatRupiah($('#biaya_tambahan').val())) || 0;
        if (additionalCostType == '') {
            $('#biaya_tambahan').val('');
            $('#biaya_tambahan').keyup();
        } else {
            if (additionalCostType == "MINUS") {
                biayaTambahan = biayaTambahan * -1;
            }
            // Hitung Total
            var hargaSatuan = parseFloat(destroyFormatRupiah($('#harga_satuan').val())) || 0;
            var qty = parseFloat($('#qty').val()) || 1;
            var diskon = parseFloat($('#diskon').val()) || 0;
            var diskonHarga = (diskon / 100) * (hargaSatuan * qty);

            var total = (((hargaSatuan * qty) - diskonHarga) + biayaTambahan);
            $('#total').val(total == 0 ? '' : greatFormatRupiah(total.toFixed(2)));
        }
    });

    // CHANGE TOTAL
    $('#total').keyup(function() {
        var total = parseFloat(destroyFormatRupiah($('#total').val())) || 0;
        var qty = parseFloat($('#qty').val()) || 1;
        // var biayaTambahan = parseFloat(destroyFormatRupiah($('#biaya_tambahan').val())) || 0;
        var diskon = parseFloat($('#diskon').val()) || 0;
        var diskonHarga = (diskon / 100) * (hargaSatuan * qty);

        var hargaSatuan = (((total / qty)));
        // $('#additional_cost_type').change();
        $('#harga_satuan').val(hargaSatuan == 0 ? '' : greatFormatRupiah(hargaSatuan.toFixed(2)));
    });

    // PPN CHANGE
    $('#ppn').change(function() {
        // Set Global Ppn
        setGlobalPpn();
    });

    // VALIDATOR DETAIL
    var validatorBarang = $(".detail-form").validate({
        rules: {
            barang_id: {
                required: true
            },
            nama_barang: {
                required: true
            },
            satuan_id: {
                required: true
            },
            harga_satuan: {
                required: true
            },
            qty: {
                required: true,
            },
            diskon: {
                required: true,
            },
            total: {
                required: true,
            }
        },
        messages: {
            barang_id: {
                required: "Pilih Kode Barang"
            },
            nama_barang: {
                required: "Nama barang wajib diisi"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
            },
            harga_satuan: {
                required: "Harga Satuan wajib diisi"
            },
            qty: {
                required: "Kuantitas barang wajib diisi"
            },
            total: {
                required: "Total biaya wajib diisi"
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

    var validatorPO = $(".create-form").validate({
        rules: {
            po_date: {
                required: true
            },
            po_no: {
                required: true
            },
            company_id: {
                required: true
            },
            division_id: {
                required: true
            },
            supplier_id: {
                required: true,
            },
            payment_date: {
                required: true,
            },
            spp_id: {
                required: true,
            },
            status_closed_spp: {
                required: true
            }
        },
        messages: {
            po_date: {
                required: "Tanggal PO Dibuat wajib diisi"
            },
            po_no: {
                required: "Nomor PO wajib diisi"
            },
            company_id: {
                required: "Pilih unit company"
            },
            division_id: {
                required: "Pilih departemen"
            },
            supplier_id: {
                required: "Pilih supplier"
            },
            payment_date: {
                required: "Tanggal pembayaran wajib diisi"
            },
            spp_id: {
                required: "Pilih Nomor SPP",
            },
            status_closed_spp: {
                required: "Status wajib diisi"
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

    $('.btn-submit-detail').click(function() {
        if ($('.detail-form').valid()) {
            var id = $('#id_detail').val();
            var spesifikasiID = $('#barang_id').find("option:selected").data("spesifikasi_id");
            var barang_id = $('#barang_id').find("option:selected").data("barang_id");
            var total = $('#total').val();
            var biayaTambahan = parseFloat(destroyFormatRupiah($('#biaya_tambahan').val())) || 0;
            var additionalCostType = $('#additional_cost_type option:selected').val();

            if (biayaTambahan != 0 && biayaTambahan != isNaN && additionalCostType == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Pilih Tipe Biaya Tambahan (Plus atau Minus) terlebih dahulu',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                });
                return;
            } else {
                if (additionalCostType == "MINUS") {
                    biayaTambahan = destroyFormatRupiah($('#biaya_tambahan').val() || 0) * -1;
                }
            }

            if (parseFloat(total) < 0 || parseFloat(total) == isNaN) {
                Swal.fire({
                    icon: 'error',
                    title: 'Nilai Total Tidak Boleh Negatif',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else {
                if (id != "") {
                    // UPDATE
                    if (spesifikasiID == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Spesifikasi barang tidak ada',
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            reverseButtons: true,
                            confirmButtonText: 'Oke',
                        })
                    } else {
                        var indexToUpdate = -1;
                        for (var i = 0; i < listBarang.length; i++) {
                            if (listBarang[i].id == id) {
                                indexToUpdate = i;
                                break;
                            }
                        }

                        listBarang[indexToUpdate] = {
                            id: id,
                            barang_id: $('#barang_id').find("option:selected").data("barang_id"),
                            spesifikasi_id: $('#barang_id').val(),
                            kode_barang: $('#barang_id').find("option:selected").data("kode_barang"),
                            nama_barang: $('#nama_barang').val(),
                            satuan_id: $('#satuan_id').val(),
                            nama_satuan: $('#satuan_id').find("option:selected").data("kode_satuan"),
                            qty: parseFloat($('#qty').val()),
                            diskon: parseFloat($('#diskon').val()),
                            harga_satuan: destroyFormatRupiah($('#harga_satuan').val() || 0),
                            biaya_tambahan: biayaTambahan,
                            total: destroyFormatRupiah($('#total').val()),
                            keterangan: $('#keterangan').val(),
                            ppn: $('#ppn').val(),
                            pph: $('#pph').val()
                        };
                        drawTabel(listBarang);
                        resetForm();
                    }

                } else {
                    // TAMBAH
                    var isAdd = false;
                    for (var i = 0; i < listBarang.length; i++) {
                        if (listBarang[i].barang_id == barang_id && listBarang[i].spesifikasi_id == spesifikasiID) {
                            indexToRemove = i;
                            isAdd = true;
                            break;
                        }
                    }
                    if (isAdd) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Barang Sudah Ada',
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            reverseButtons: true,
                            confirmButtonText: 'Oke',
                        })
                    } else if (spesifikasiID == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Spesifikasi barang tidak ada',
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            reverseButtons: true,
                            confirmButtonText: 'Oke',
                        })
                    } else {
                        insertList();
                    }

                }
            }
        }
    });

    $('.btn-submit-parent').click(function() {
        if ($('.create-form').valid()) {
            if (listBarang.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Barang masih kosong',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else {
                var id = $('#id').val();
                if (id) {
                    // UPDATE
                    Swal.fire({
                        icon: 'question',
                        title: 'Update Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var id = $('#id').val();
                            var poDate = $('#po_date').val();
                            var poNo = $('#po_no').val();
                            var divisionID = $('#division_id').val();
                            var supplierID = $('#supplier_id').val();
                            var paymentDate = $('#payment_date').val();
                            var sppID = $('#spp_id').val();
                            var note = $('#note').val();
                            var statusClosedSpp = $('#status_closed_spp option:selected').val();
                            // append
                            var formData = new FormData();
                            formData.append("id", id);
                            formData.append("spp_id", sppID);
                            formData.append("poDate", poDate);
                            formData.append("poNo", poNo);
                            formData.append("divisionID", divisionID);
                            formData.append("supplierID", supplierID);
                            formData.append("paymentDate", paymentDate);
                            formData.append("total", totalHarga);
                            formData.append("note", note);
                            formData.append("status_closed_spp", statusClosedSpp);
                            formData.append("listBarang", JSON.stringify(listBarang));

                            $.ajax({
                                url: "<?= base_url("po-lokal-bahan-penolong/update"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        }).then((result) => {
                                            window.location.href = "<?= base_url('po-lokal-bahan-penolong') ?>"
                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            cancelButtonColor: '#d33',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        })
                                    }

                                }
                            });
                        }
                    })
                } else {
                    // CREATE
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var poDate = $('#po_date').val();
                            var poNo = $('#po_no').val();
                            var divisionID = $('#division_id').val();
                            var supplierID = $('#supplier_id').val();
                            var paymentDate = $('#payment_date').val();
                            var sppID = $('#spp_id').val();
                            var note = $('#note').val();
                            var statusClosedSpp = $('#status_closed_spp option:selected').val();
                            // append
                            var formData = new FormData();
                            formData.append("poDate", poDate);
                            formData.append("spp_id", sppID);
                            formData.append("poNo", poNo);
                            formData.append("divisionID", divisionID);
                            formData.append("supplierID", supplierID);
                            formData.append("paymentDate", paymentDate);
                            formData.append("total", totalHarga);
                            formData.append("note", note);
                            formData.append("status_closed_spp", statusClosedSpp);
                            formData.append("listBarang", JSON.stringify(listBarang));

                            $.ajax({
                                url: "<?= base_url("po-lokal-bahan-penolong/save"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    setLoading();
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        }).then((result) => {
                                            window.location.href = "<?= base_url('po-lokal-bahan-penolong') ?>"

                                        })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            cancelButtonColor: '#d33',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        })
                                    }

                                }
                            });
                        }
                    });
                }
            }
        }
    });

    function insertList() {
        listBarang.push({
            id: getID(),
            barang_id: $('#barang_id').find("option:selected").data("barang_id"),
            spesifikasi_id: $('#barang_id').val(),
            kode_barang: $('#barang_id').find("option:selected").data("kode_barang"),
            nama_barang: $('#nama_barang').val(),
            satuan_id: $('#satuan_id').val(),
            nama_satuan: $('#satuan_id').find("option:selected").data("kode_satuan"),
            qty: parseFloat($('#qty').val()),
            diskon: parseFloat($('#diskon').val()),
            harga_satuan: destroyFormatRupiah($('#harga_satuan').val() || 0),
            biaya_tambahan: destroyFormatRupiah($('#biaya_tambahan').val() || 0),
            total: destroyFormatRupiah($('#total').val()),
            additional_cost_type: $('#additional_cost_type option:selected').val(),
            keterangan: $('#keterangan').val(),
            ppn: $('#ppn').val(),
            pph: $('#pph').val()
        });
        $(".detail-form input, .detail-form select").val("");
        $(".barang_id").val("").change();
        $(".diskon").val('0');
        // reset update flag
        $('#barang_update_id').val("");
        $('#satuan_id').val(null).change();
        drawTabel(listBarang);
    }

    function drawTabel(listBarang) {
        const table = $('#dataTable');
        var no = 1;
        table.find('tbody').empty();
        totalHarga = 0;
        $.each(listBarang, function(i, v) {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td style="text-align:center;">').text(no++));
            newRow.append($('<td>').text(v.kode_barang));
            newRow.append($('<td>').text(v.nama_barang));
            newRow.append($('<td>').text(v.nama_satuan));
            newRow.append($('<td>').text(greatFormatRupiah(v.harga_satuan)));
            newRow.append($('<td>').text(parseFloat(v.qty)));
            newRow.append($('<td>').text(v.diskon));
            newRow.append($('<td>').text(greatFormatRupiah(v.biaya_tambahan)));
            newRow.append($('<td>').text(greatFormatRupiah(v.total)));
            <?php if (!empty($poDetail)) : ?>
                <?php if (!$poDetail['is_posted']) : ?>
                    newRow.append($('<td>').html(
                        `
                            <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${v.id}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php if ($checkLpb == null) : ?>
                            <button class="btn btn-danger" onclick="deleteRow('${v.id}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif ?>
                        `
                    ));
                <?php else : ?>
                    newRow.append($('<td>').text('-'));
                <?php endif; ?>
            <?php else : ?>
                newRow.append($('<td>').html(
                    `
                        <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${v.id}')">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button><button class="btn btn-danger" onclick="deleteRow('${v.id}')">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `
                ));
            <?php endif; ?>

            table.find('tbody').append(newRow);
            totalHarga += parseFloat(v.total);
        });
        table.find('tfoot').empty();
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="8"><b>Total</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' + greatFormatRupiah(totalHarga) + '</b></td>'));
        newRow.append($('<td></td>'));
        table.find('tfoot').append(newRow);
    }

    // generate no po
    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $.ajax({
                url: `<?= base_url("/po-lokal-bahan-penolong/generate-po-no"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res) {
                        $(".po_no").val(res);
                        $(".po_no").attr("readonly", true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".po_no").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".po_no").val("");
                    }
                }
            })
        } else {
            $(".po_no").attr("readonly", false);
            $(".po_no").val("");
        }
    }

    function deleteRow(id) {

        var indexToRemove = -1;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listBarang.splice(indexToRemove, 1);
        }
        drawTabel(listBarang);
        resetForm();

    }

    function detailRow(id) {
        var item = null;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].id == id) {
                item = listBarang[i];
                break;
            }
        }
        var additionalCostType = "";
        var biayaTambahan = item.biaya_tambahan;
        if (biayaTambahan != 0 && biayaTambahan != '' && biayaTambahan != NaN) {
            if (biayaTambahan > 0) {
                additionalCostType = 'PLUS';
            } else {
                additionalCostType = 'MINUS';
                item.biaya_tambahan = item.biaya_tambahan * -1;
            }
        }

        $('#id_detail').val(item.id);
        $('#barang_id, #barang_update_id').val(item.spesifikasi_id).change();
        $('#harga_satuan').val(item.harga_satuan == 0 ? '' : greatFormatRupiah(item.harga_satuan));
        $('#qty').val(parseFloat(item.qty));
        $('#diskon').val(parseFloat(item.diskon));
        $('#biaya_tambahan').val((item.biaya_tambahan == 0) ? '' : greatFormatRupiah(item.biaya_tambahan));
        $('#additional_cost_type').val(additionalCostType).change();
        $('#keterangan').val(item.keterangan);
        $('#ppn').val(item.ppn);
        $('#pph').val(item.pph);
        $('#satuan_id').val(item.satuan_id).change();
        // $('#biaya_tambahan').change();
        $('#total').val(item.total == 0 ? '' : greatFormatRupiah(item.total));
        // attr barang_id disabled
        $('#barang_id').attr('disabled', true);
        // setTimeout(function() {
        //     $('#harga_satuan').keyup();
        // }, 1000);
    }

    function resetForm() {
        // $('#barang_id').attr('disabled', false);
        $(".satuan_id").val(null).change();
        $(".detail-form input, .detail-form select").val("");
        $(".barang_id").val("").change();
        $(".diskon").val('0');
        $(".nama_barang").val(null);
    }

    function getListSPP() {
        $.ajax({
            url: "<?= base_url("po-lokal-bahan-penolong/dropdown/get-spp"); ?>",
            data: {
                divisi_id: $('.division_id').val(),
                spp_type: "Lokal BP"
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            method: "GET",
            success: function(response) {
                var sppSelect = $("select[name='spp_id']");
                sppSelect.empty();

                var emptyOption = $("<option></option>")
                    .attr("value", "")
                    .text("Pilih Nomor SPP");
                sppSelect.append(emptyOption);
                $.each(response.data, function(index, data) {
                    var option = $("<option></option>")
                        .attr("value", data.id)
                        .text(data.spp_no.toUpperCase());
                    sppSelect.append(option);
                });

            },
            onError: function(response) {
                alert("ERROR")
            }
        });
    }

    function getListBarang() {
        $.ajax({
            url: "<?= base_url("po-lokal-bahan-penolong/dropdown/get-barang"); ?>",
            data: {
                divisi_id: $('.division_id').val(),
                spp_type: "Lokal BP"
            },
            beforeSend: function() {
                $.LoadingOverlay("show", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            complete: function() {
                $.LoadingOverlay("hide", {
                    image: "",
                    fontawesomeColor: "#222FCC",
                    fontawesome: "fa fa-cog fa-spin"
                });
            },
            method: "GET",
            success: function(response) {
                var barangSelect = $("select[name='barang_id']");
                barangSelect.empty();

                var emptyOption = $("<option></option>")
                    .attr("value", "")
                    .text("Pilih Barang");
                barangSelect.append(emptyOption);
                $.each(response.data, function(index, data) {
                    var option = $("<option></option>")
                        .attr("data-barang_id", data.id)
                        .attr("data-parent_name", data.parent_name)
                        .attr("data-spesifikasi_id", data.spesifikasi_id)
                        .attr("data-spesifikasi_name", data.spesifikasi_name)
                        .attr("data-satuan_id", data.satuan_id)
                        .attr("data-satuan_2", data.satuan_2)
                        .attr("data-satuan_3", data.satuan_3)
                        .attr("data-nama_barang", data.barang_name_master)
                        .attr("data-kode_barang", data.kode_barang)
                        .attr("value", data.barang_master_spesifikasi_id)
                        .text(data.kode_barang + " { " + data.barang_name_master + " - " + data.spesifikasi + " }");
                    barangSelect.append(option);
                });

            },
            onError: function(response) {
                alert("ERROR")
            }
        });
    }

    function getDetailSPP() {
        var spp_id = $('.spp_id').val();
        // Load Ulang Master Barang Ketika SPP Dipilih
        getListBarang();
        if (spp_id !== '') {
            $.ajax({
                url: "<?= base_url("po-lokal-bahan-penolong/dropdown/get-detail-barang-spp"); ?>",
                data: {
                    po_id: $('#id').val(),
                    spp_id: spp_id,
                },
                method: "GET",
                beforeSend: function() {
                    $.LoadingOverlay("show", {
                        image: "",
                        fontawesomeColor: "#222FCC",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                complete: function() {
                    $.LoadingOverlay("hide", {
                        image: "",
                        fontawesomeColor: "#222FCC",
                        fontawesome: "fa fa-cog fa-spin"
                    });
                },
                success: function(response) {
                    $.each(response.data, function(i, v) {
                        listBarang.push({
                            id: getID(),
                            barang_id: v.barang_id,
                            spesifikasi_id: v.spesifikasi_id,
                            kode_barang: v.kode_barang,
                            nama_barang: v.nama_barang,
                            satuan_id: v.satuan_id,
                            nama_satuan: v.nama_satuan,
                            qty: v.qty,
                            diskon: v.diskon,
                            harga_satuan: v.harga_satuan,
                            biaya_tambahan: v.biaya_tambahan,
                            total: v.total,
                            keterangan: v.keterangan,
                            ppn: v.ppn,
                            pph: v.pph
                        });

                        console.log(v.ppn);
                    });
                    console.log(listBarang);
                    drawTabel(listBarang);
                    // Set Global Ppn
                    setGlobalPpn();
                },
                onError: function(response) {
                    alert("ERROR")
                }
            });
        }

    }

    function activeFieldSatuanId(satuan_1, satuan_2, satuan_3) {
        const allowedValues = [String(satuan_1), String(satuan_2), String(satuan_3)];
        $('#satuan_id').on('select2:open', function() {
            $('#satuan_id option').each(function() {
                if (!allowedValues.includes(String($(this).val()))) {
                    $(this).attr('disabled', true).addClass('disabled-option');
                } else {
                    $(this).attr('disabled', false).removeClass('disabled-option');
                }
            });
        });
    }

    function setGlobalPpn() {
        // Set PPN
        // PPN untuk semua list barang hasilnya sama
        var ppn = $('#ppn option:selected').val();
        for (let i = 0; i < listBarang.length; i++) {
            listBarang[i].ppn = ppn == undefined || ppn == '' ? 0 : ppn;
        }
    }

    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };
</script>
<!-- Edit Script -->
<?php if (!empty($poDetail)) : ?>
    <script>
        $('#company_id').change();
        $('#supplier_id').val("<?= $poDetail['supplier_id'] ?>").change();
        <?php foreach ($listBarang as $l) : ?>
            listBarang.push({
                id: getID(),
                barang_id: "<?= $l['barang_id'] ?>",
                spesifikasi_id: "<?= $l['spesifikasi_id'] ?>",
                kode_barang: "<?= $l['kode_barang'] ?>",
                nama_barang: "<?= str_replace('"', '\"', $l['nama_barang']) . " - " . str_replace('"', '\"', $l['spesifikasi_name'])  ?>",
                satuan_id: "<?= $l['satuan_id'] ?>",
                nama_satuan: "<?= $l['nama_satuan'] ?>",
                harga_satuan: "<?= $l['harga_satuan'] ?>",
                qty: "<?= $l['qty'] ?>",
                diskon: "<?= $l['diskon'] ?>",
                biaya_tambahan: "<?= $l['biaya_tambahan'] ?>",
                total: ("<?= (($l['harga_satuan'] * $l['qty']) - (($l['diskon'] / 100) * ($l['harga_satuan'] * $l['qty']))) + $l['biaya_tambahan'] ?>"),
                keterangan: "<?= $l['keterangan'] ?>",
                ppn: "<?= $l['ppn'] ?>",
                pph: "<?= $l['pph'] ?>"
            });
            // Set PPN
            // PPN untuk semua list barang hasilnya sama
            $('#ppn').val("<?= $l['ppn'] ?>")
        <?php endforeach; ?>
        drawTabel(listBarang);

        // HAPUS
        $('.delete-parent').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan hapus PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-penolong/delete"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    window.location.href = "<?= base_url('po-lokal-bahan-penolong') ?>"
                                })
                            }

                        }
                    });
                }
            })
        });

        //POSTING
        $('.posting-po').click(function() {
            var status = $(this).data('status');
            Swal.fire({
                icon: 'question',
                title: status == '1' ? 'Posting PO ?' : 'Unposting PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: status == '0' ? 'Unposting' : 'Posting',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    formData.append("status", status);

                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-penolong/update-status"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            setLoading();
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        complete: function() {
                            stopLoading()
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    window.location.href = "<?= base_url('po-lokal-bahan-penolong') ?>";
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                })
                            }

                        }
                    });
                }
            })
        });

        // CLOSE PO
        $('.close-parent').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan close PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-penolong/close-po"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    window.location.href = "<?= base_url('po-lokal-bahan-penolong') ?>";
                                })
                            }

                        }
                    });
                }
            })
        });
        const print = function(url) {
            window.open(url, "_blank");
        }
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>