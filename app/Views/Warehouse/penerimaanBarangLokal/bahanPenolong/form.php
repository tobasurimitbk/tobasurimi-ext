<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataPenerimaanBarang) ? "Update Penerimaan Barang Lokal Bahan Penolong" : "Tambah Penerimaan Barang Lokal Bahan Penolong" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("penerimaan-barang-lokal-bp"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataPenerimaanBarang)) : ?>
                <!-- <?php if (can('Warehouse', 'P. Barang Lokal BP', 'p')) : ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("penerimaan-barang-lokal-bp/print/"); ?><?= encrypt($dataPenerimaanBarang['id']); ?>')">
                        Print
                    </button>
                <?php endif; ?> -->
            <?php endif; ?>
            <?php if (!empty($dataPenerimaanBarang)) : ?>
                <?php if ($dataPenerimaanBarang['status_post'] === "WAITING") : ?>
                    <!-- <?php if (can('Warehouse', 'P. Barang Lokal BP', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Warehouse', 'P. Barang Lokal BP', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-lpb">
                            Posting
                        </button>
                    <?php endif; ?> -->
                    <?php if (can('Warehouse', 'P. Barang Lokal BP', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
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
            <?php if (empty($dataPenerimaanBarang)): ?>
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" id="nav_link_single" href="#" onclick="loadComponent('single')">Single Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="nav_link_multiple" href="#" onclick="loadComponent('multiple')">Multiple Order</a>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="nav nav-tabs mb-3">
                    <?php if ($dataPenerimaanBarang['acceptance_type'] == "SINGLE ORDER"): ?>
                        <li class="nav-item">
                            <a class="nav-link active" id="nav_link_single" href="#">Single Order</a>
                        </li> <?php else: ?>

                        <li class="nav-item">
                            <a class="nav-link active" id="nav_link_multiple" href="#">Multiple Order</a>
                        </li>
                    <?php endif; ?>
                </ul>

            <?php endif; ?>
            <div class="spinner-border" id="spinner" role="status">
                <span class="sr-only">Loading...</span>
            </div>

            <div class="content" id="content"></div>
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
                    <input autocomplete="one-time-code" type="hidden" class="am_purchase_order_id" name="am_purchase_order_id" id="am_purchase_order_id" />
                    <input autocomplete="one-time-code" type="hidden" class="am_purchase_order_details_id" name="am_purchase_order_details_id" id="am_purchase_order_details_id" />
                    <input autocomplete="one-time-code" type="hidden" class="jml_diterima_lpb_last" name="jml_diterima_lpb_last" id="jml_diterima_lpb_last" />
                    <input type="hidden" class="sub_total_po" id="sub_total_po" name="sub_total_po">
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Barang</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control po_no" id="po_no" name="po_no" placeholder="Nomor PO">
                                <label for="floatingInput">Nomor PO</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control kode_barang" id="kode_barang" name="kode_barang" placeholder="Kode Barang">
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control satuan_order" id="satuan_order" name="satuan_order" placeholder="Satuan Order">
                                <label for="floatingInput">Satuan Order</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control jml_order" id="jml_order" name="jml_order" placeholder="Jumlah Order">
                                <label for="floatingInput">Jumlah Order</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly="true" class="form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan" />
                                <label for="floatingInput">Keterangan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control jml_diterima_lpb" id="jml_diterima_lpb" name="jml_diterima_lpb" placeholder="Qty Diterima LPB ini">
                                <label for="floatingInput">Qty Diterima Saat ini</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control jml_diterima_total" id="jml_diterima_total" name="jml_diterima_total" placeholder="Qty Diterima Total">
                                <label for="floatingInput">Qty Diterima Total</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly type="text" readonly="true" class="form-control sisa_total" id="sisa_total" name="sisa_total" placeholder="Sisa Total">
                                <label for="floatingInput">Sisa Total</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Detail Barang di Dokumen (Bea Cukai)</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control nama_barang_dokumen" id="nama_barang_dokumen" name="nama_barang_dokumen" placeholder="Nama Barang di dokumen">
                                <label for="floatingInput">Nama Barang di dokumen</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly disabled autocomplete="one-time-code" type="text" class="form-control harga_satuan" name="harga_satuan" id="harga_satuan" placeholder="Harga Barang Satuan">
                                <label for="floatingInput" class="label-input-harga">Harga Barang Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly disabled autocomplete="one-time-code" type="text" class="form-control sub_total" name="sub_total" id="sub_total" placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($dataPenerimaanBarang)): ?>
    <script>
        const csrfToken = '<?= csrf_token() ?>';
        $(document).ready(function() {
            $.ajax({
                url: "<?= base_url('penerimaan-barang-lokal-bp/load-component') ?>",
                type: "GET",
                data: {
                    form: "<?= $dataPenerimaanBarang['acceptance_type'] == "SINGLE ORDER" ? 'single' : "multiple" ?>",
                    id: "<?= $dataPenerimaanBarang['id'] ?>"
                },
                beforeSend: function() {
                    $('#spinner').show();
                    $('#content').hide();
                },
                complete: function() {
                    $('#spinner').hide();
                    $('#content').show();
                },
                success: function(response) {
                    $("#content").html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

        });
    </script>
<?php else: ?>
    <script>
        const csrfToken = '<?= csrf_token() ?>';

        $('#spinner').hide();
        loadComponent('single');

        function loadComponent(form) {
            if (form === 'single') {
                $('#nav_link_single').addClass('active');
                $('#nav_link_multiple').removeClass('active');
            } else {
                $('#nav_link_single').removeClass('active');
                $('#nav_link_multiple').addClass('active');
            }

            $.ajax({
                url: "<?= base_url('penerimaan-barang-lokal-bp/load-component') ?>",
                type: "GET",
                data: {
                    form: form
                },
                beforeSend: function() {
                    $('#spinner').show();
                    $('#content').hide();
                },
                complete: function() {
                    $('#spinner').hide();
                    $('#content').show();
                },
                success: function(response) {
                    $("#content").html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>