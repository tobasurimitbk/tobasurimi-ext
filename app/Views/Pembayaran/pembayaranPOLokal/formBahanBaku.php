<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($detail) ? "Update Pembayaran PO Lokal Bahan Baku" : "Tambah Pembayaran PO Lokal Bahan Baku" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-po-lokal-bb"); ?>">
                Batal
            </a>
            <?php if (!empty($detail)) : ?>
                <?php if ($detail['pembayaranDetail']['status_posting'] == "0") : ?>
                    <?php if (can('Pembayaran', 'Lokal BB', 'd')) : ?>
                        <button onclick="remove('<?= encrypt($detail['pembayaranDetail']['id']) ?>')" class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Lokal BB', 'a')) : ?>
                        <button onclick="posting('<?= encrypt($detail['pembayaranDetail']['id']) ?>')" class="btn btn-success posting-spp float-right posting">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (can('Pembayaran', 'Lokal BB', 'p')) : ?>
                    <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-lokal-bb/print/' . encrypt($detail['pembayaranDetail']['id']) ?? '') ?>">
                        <i class="fa-solid fa-print"></i> Print
                    </a>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-form">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pembayaran</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" disabled type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran" required <?= !empty($detail) ? 'disabled value="' . $detail['pembayaranDetail']['payment_no'] . '"' : '' ?>>
                            <label for="floatingInput">No. Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="bank_id" id="bank_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($bankList as $b) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['bank_id'] == $b->id ? 'selected' : '') : '' ?> value="<?= $b->id ?>"><?= strtoupper($b->kode_bank) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kode Bank</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? 'disabled value="' . date('d/m/Y', strtotime($detail['pembayaranDetail']['payment_date']))  . '"' : '' ?>>
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating  form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? 'disabled' : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['supplier_id'] == $supplier->id ? 'selected' : '') : '' ?> value="<?= $supplier->id ?>"><?= strtoupper($supplier->name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div <?= !empty($detail) ? 'disabled' : '' ?> class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" name="tipe_pembayaran" id="tipe_pembayaran">
                                <option disabled selected value=""></option>
                                <option value="BULANAN">BULANAN</option>
                                <option value="HARIAN">HARIAN</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Bayar</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <input type="text" name="jenis_dokumen" class="form-control" id="jenis_dokumen" readonly>
                            <label for="floatingInput" style="z-index: 1;">Jenis Dokumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bulanan-form">
                            <?php if (!empty($detail)) : ?>
                                <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                    <input <?= !empty($detail) ? 'disabled' : '' ?> value="<?= !empty($detail) ? $detail['pembayaranDetail']['month'] : '' ?>" type="text" class="form-control">
                                    <label for="floatingInput" style="z-index: 1;">Pilih Bulan</label>
                                </div>
                            <?php else : ?>
                                <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                    <input <?= !empty($detail) ? 'disabled' : '' ?> value="<?= !empty($detail) ? $detail['pembayaranDetail']['month'] : '' ?>" type="month" name="bulan" id="bulan" class="form-control">
                                    <label for="floatingInput" style="z-index: 1;">Pilih Bulan</label>
                                </div>
                            <?php endif; ?>

                        </div>
                        <div class="harian-form">
                            <?php if (!empty($detail)) : ?>
                                <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                    <select class="form-select" disabled>
                                        <?php foreach ($penerimaanData as $value) : ?>
                                            <option <?= $detail['pembayaranDetail']['lpb_no'] == $value->id ? 'selected' : '' ?> value=""><?= !empty($detail) ? $value->no_penerimaan_barang : '' ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">No Dokumen LPB</label>
                                </div>
                            <?php else : ?>
                                <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                    <select class="form-select" name="lpb" id="lpb">
                                        <option disabled selected value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">No Dokumen LPB</label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($detail) ? 'disabled' : '' ?> autocomplete="one-time-code" class="form-control input-picker jatuh_tempo" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? 'value="' . date('d/m/Y', strtotime($detail['pembayaranDetail']['due_date']))  . '"' : '' ?>>
                                <label for="floatingInput">Tanggal Jatuh Tempo</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? 'disabled' : '' ?> class="form-select " name="payment_method" id="payment_method">
                                <option disabled selected value="">Pilih Metode Pembayaran</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Cash" ? 'selected' : '') : '' ?> value="Cash">Cash</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Debit" ? 'selected' : '') : '' ?> value="Debit">Debit</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? 'disabled' : '' ?> name="pembayaran_oleh" id="pembayaran_oleh" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['pembayaranDetail']['pembayaran_oleh'] : session()->get("login")->name; ?>" type="text" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="akun_kas" id="akun_kas">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_kas'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="akun_selisih" id="akun_selisih">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_selisih'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit (Opsional)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Nominal & Status Pembayaran</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? 'disabled' : '' ?> oninput="preventNegativeInput(this)" name="nominal_pembayaran" id="nominal_pembayaran" autocomplete="one-time-code" value="<?= !empty($detail) ? number_format($detail['pembayaranDetail']['harga_sebelum_diskon'], 2) : '' ?>" type="text" class="form-control" placeholder="Nominal Pembayaran">
                            <label for="floatingInput">Total Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? 'disabled' : '' ?> oninput="preventNegativeInput(this)" name="potongan" id="potongan" autocomplete="one-time-code" value="<?= !empty($detail) ? number_format($detail['pembayaranDetail']['potongan_harga'], 2) : '' ?>" type="text" class="form-control" placeholder="Nominal Pembayaran">
                            <label for="floatingInput">Potongan / Diskon</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="status_lunas" id="status_lunas">
                                <option selected value="">Pilih Status Pelunasan</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['status_lunas'] == '1' ? 'selected' : '') : '' ?> value="1">LUNAS</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['status_lunas'] == '0' ? 'selected' : '') : '' ?> value="0">BELUM LUNAS</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Status Pelunasan</label>
                        </div>
                    </div>
                </div>
                <div class="col-subtitle-modal mt-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">Data pembelian yang sudah diterima</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0" style="border-color: #f7f6f5;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">No</th>
                                        <th style="text-align: center;">Tanggal LPB</th>
                                        <th style="text-align: center;">No LPB</th>
                                        <th style="text-align: center;">Tanggal PO</th>
                                        <th style="text-align: center;">No PO</th>
                                        <th style="text-align: center;">Barang</th>
                                        <th style="text-align: center;">Total Order</th>
                                        <th style="text-align: center;">Total Diterima</th>
                                        <th style="text-align: center;">Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody id="body-table" style="text-align: center;">
                                    <?php if (!empty($detail)) : ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($detail['itemList']['detail'] as $d) : ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= $d['tanggalLpb'] ?></td>
                                                <td><?= $d['lpbNo'] ?></td>
                                                <td><?= $d['tanggalPo'] ?></td>
                                                <td><?= $d['poNo'] ?></td>
                                                <td><?= $d['barang'] ?></td>
                                                <td><?= $d['totalOrder'] ?></td>
                                                <td><?= $d['totalDiterima'] ?></td>
                                                <td><?= $d['totalHarga'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                Total
                                            </td>
                                            <td style="text-align: center;">
                                                <b><?= $detail['itemList']['totalOrder'] ?></b>
                                            </td>
                                            <td style="text-align: center;">
                                                <b><?= $detail['itemList']['totalDiterima'] ?></b>
                                            </td>
                                            <td style="text-align: center;">
                                                <b><?= $detail['itemList']['totalHarga'] ?></b>
                                            </td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                <b> GRAND TOTAL</b>
                                            </td>
                                            <td style="text-align: center;">
                                                <b>0</b>
                                            </td>
                                            <td style="text-align: center;">
                                                <b>0</b>
                                            </td>
                                            <td style="text-align: center;">
                                                <b>Rp 0.0</b>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

<?php if (!empty($detail)) : ?>
    <?php if ($detail['pembayaranDetail']['type_bayar'] == "Bulanan") : ?>
        <script>
            $('.bulanan-form').show();
            $('.harian-form').hide();
            $('#bulan').val('');
            $('#jenis_dokumen').val("KWITANSI TB");
        </script>
    <?php else : ?>
        <script>
            $('.harian-form').show();
            $('.bulanan-form').hide();
            $('#jenis_dokumen').val("LPB");
        </script>
    <?php endif; ?>
    <script>
        $('#tipe_pembayaran').val("<?= strtoupper($detail['pembayaranDetail']['type_bayar']) ?>");
        $('#tipe_pembayaran').attr('disabled', true);
    </script>
<?php else : ?>
    <script>
        $('.bulanan-form,.harian-form').hide();
    </script>
<?php endif; ?>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const table = $('#dataTable');
    var listPoNo = [];
    var listPoID = [];

    $(document).ready(function() {


        var validator = $(".create-form").validate({
            rules: {
                no_bukti_pembayaran: {
                    required: true
                },
                bank_id: {
                    required: true
                },
                divisi_id: {
                    required: true
                },
                pembayaran_oleh: {
                    required: true
                },
                status_lunas: {
                    required: true
                },
                payment_date: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                tipe_pembayaran: {
                    required: true
                },
                jenis_dokumen: {
                    required: true
                },
                nominal_pembayaran: {
                    required: true
                },
                jatuh_tempo: {
                    required: true
                },
                payment_method: {
                    required: true
                },
                akun_kas: {
                    required: true
                }
            },
            messages: {
                no_bukti_pembayaran: {
                    required: "No. Pembayaran wajib diisi"
                },
                bank_id: {
                    required: "Kode bank wajib diisi"
                },
                divisi_id: {
                    required: "Departemen wajib diisi"
                },
                pembayaran_oleh: {
                    required: "Pembayaran oleh wajib diisi"
                },
                status_lunas: {
                    required: "Status pelunasan wajib diisi"
                },
                payment_date: {
                    required: "Tanggal pembayaran wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                jenis_dokumen: {
                    required: "Jenis dokumen wajib diisi"
                },
                nominal_pembayaran: {
                    required: "Nominal pembayaran wajib diisi"
                },
                jatuh_tempo: {
                    required: "Tanggal jatuh tempo wajib diisi"
                },
                payment_method: {
                    required: "Metode pembayaran wajib diisi"
                },
                akun_kas: {
                    required: "Akun kas wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
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

        $("#payment_date,#jatuh_tempo").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('#payment_date').change(function() {
            changeStatus();
        });

        $('#bank_id').select2({
            placeholder: "Pilih kode bank",
            theme: "bootstrap-5"
        }).change(function() {
            changeStatus();
        });

        $('#lpb').select2({
            placeholder: "Pilih No Penerimaan Barang",
            theme: "bootstrap-5"
        });

        $('#akun_kas').select2({
            placeholder: "Pilih akun debit",
            theme: "bootstrap-5"
        });

        $('#akun_selisih').select2({
            placeholder: "Pilih akun kredit",
            theme: "bootstrap-5"
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        }).change(function() {
            generateLPBNo();
            resetTable();
        });

        $('#bulan').change(function() {
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);

            var lpbID = $(this).val();
            var supplierID = $('#supplier_id').val();
            var formData = new FormData();
            formData.append("supplierID", $('#supplier_id').val());
            formData.append("bulan", $(this).val());
            formData.append("tipeBayar", $('#tipe_pembayaran').val());

            $.ajax({
                url: "<?= base_url("pembayaran-po-lokal-bb/get-list-po-no-paid"); ?>",
                data: formData,
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    if (response.data.detail == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: "List PO yang sudah diterima di warehouse tidak ditemukan",
                            confirmButtonColor: '#4e73df',
                        })
                    }

                    drawTable(response.data);
                    $('#nominal_pembayaran').val(response.data.sisaNumber);
                }
            });
        });

        $('#lpb').change(function() {
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);

            var lpbID = $(this).val();
            var supplierID = $('#supplier_id').val();
            var formData = new FormData();
            formData.append("supplierID", $('#supplier_id').val());
            formData.append("lpbID", $(this).val());
            formData.append("tipeBayar", $('#tipe_pembayaran').val());

            $.ajax({
                url: "<?= base_url("pembayaran-po-lokal-bb/get-list-po-no-paid"); ?>",
                data: formData,
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    drawTable(response.data);
                    $('#nominal_pembayaran').val(response.data.sisaNumber);
                }
            });
        });

        $(".btn-submit-form").click(function() {
            var id = $('.id').val();
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);
            if (id) {
                // UPDATE

            } else {
                // CREATE
                // VALIDASI BARANG LIST
                if (listPoID.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'PO List Masih kosong',
                        confirmButtonColor: '#4e73df',
                    });
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
                                let formData = new FormData(document.querySelector(".create-form"));
                                formData.append("no_bukti_pembayaran", $('#no_bukti_pembayaran').val());
                                formData.append("poIDList", JSON.stringify(listPoID));
                                formData.append("poNoList", JSON.stringify(listPoNo));

                                $.ajax({
                                    url: "<?= base_url("pembayaran-po-lokal-bb/create"); ?>",
                                    data: formData,
                                    beforeSend: function(xhr) {
                                        setLoading();
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    },
                                    complete: function() {
                                        stopLoading();
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
                                                    window.location.href = `<?= base_url("pembayaran-po-lokal-bb/id/"); ?>` + response.id;
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            });
                                        }
                                    },
                                });
                            }
                        })
                    }
                }
            }
        })

        function drawTable(data) {
            var no = 1;
            const table = $('#dataTable');
            table.find('tbody').empty();
            // clear res
            listPoID.length = 0;
            listPoNo.length = 0;
            $.each(data.detail, function(i, v) {
                // push po id
                listPoID.push({
                    poID: v.poID
                });
                // push po no
                listPoNo.push({
                    poNo: v.poNo
                });
                var newRow = $('<tr>');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td style="text-align:center;">').text(v.tanggalLpb));
                newRow.append($('<td style="text-align:center;">').text(v.lpbNo));
                newRow.append($('<td style="text-align:center;">').text(v.tanggalPo));
                newRow.append($('<td style="text-align:center;">').text(v.poNo));
                newRow.append($('<td style="text-align:center;">').text(v.barang));
                newRow.append($('<td style="text-align:center;">').text(v.totalOrder));
                newRow.append($('<td style="text-align:center;">').text(v.totalDiterima));
                newRow.append($('<td style="text-align:center;">').text(v.totalHarga));
                table.find('tbody').append(newRow);
            });
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:right;" colspan="6"><b>GRAND TOTAL</b></td>'));
            newRow.append($('<td style="text-align:center;"><b>' + data.totalOrder + '</b></td>'));
            newRow.append($('<td style="text-align:center;"><b>' + data.totalDiterima + '</b></td>'));
            newRow.append($('<td style="text-align:center;"><b>' + data.totalHarga + '</b></td>'));
            table.find('tbody').append(newRow);
        }

        $('#tipe_pembayaran').select2({
            placeholder: "Pilih Tipe Bayar",
            theme: "bootstrap-5"
        }).change(function() {
            var tipeBayar = $(this).val();
            if (tipeBayar == "BULANAN") {
                $('.bulanan-form').show();
                $('.harian-form').hide();
                $('#bulan').val('');
                $('#jenis_dokumen').val("KWITANSI TB");
            } else {
                $('.harian-form').show();
                $('.bulanan-form').hide();
                $('#jenis_dokumen').val("LPB");
                generateLPBNo();
            }
            // clear res
            resetTable();
        });

        $('#supplier_id').select2({
            placeholder: "Pilih Supplier",
            theme: "bootstrap-5"
        }).change(function(e) {
            if ($('#tipe_pembayaran').val() == "HARIAN") {
                generateLPBNo();
            } else {
                $('#bulan').val("");
            }
            resetTable();
        });
    });

    function resetTable() {
        // clear res
        listPoID.length = 0;
        listPoNo.length = 0;
        const table = $('#dataTable');
        table.find('tbody').empty();
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>Total</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>0</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>0</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>0.0</b></td>'));
        table.find('tbody').append(newRow);
    }

    function remove(id) {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        Swal.fire({
            icon: 'question',
            title: 'Hapus Pembayaran Ini ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: "<?= base_url("pembayaran-po-lokal-bb/delete"); ?>",
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
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        }).then((result) => {
                            location.reload();
                        });

                    },
                });
            }
        });
    }

    function posting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Pembayaran ?',
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
                    url: "<?= base_url("pembayaran-po-lokal-bb/posting"); ?>",
                    data: {
                        id: id,
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
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
                                    location.reload()
                                })
                        }
                    },

                });
            }
        })
    }

    function generateLPBNo() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("supplierID", $('#supplier_id').val());
        formData.append("divisiID", $('#divisi_id').val());

        $.ajax({
            url: "<?= base_url("pembayaran-po-lokal-bb/get-lpb-not-paid"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                $("#lpb").empty();
                $("#lpb").append(`<option value=""></option>`);
                response.data.forEach(function(item) {
                    $("#lpb").append(`<option  value="${item.lpbID}">${item.lpbNO}</option>`);
                });

            }
        });
    }

    function changeStatus() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var bank_id = $('#bank_id').val();
        var payment_date = $('#payment_date').val();
        var formData = new FormData();
        formData.append("type", "Bahan Baku");
        formData.append("bank_id", bank_id);
        formData.append("payment_date", payment_date)

        $.ajax({
            url: "<?= base_url("pembayaran-po-lokal-bb/generate-no-pembayaran"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                $(".no_bukti_pembayaran").val(response.paymentNo);
            },
            onError: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan pada sistem',
                    confirmButtonColor: '#4e73df',
                });
            }
        });
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }

    function formatRupiah(angka) {
        if (angka === null) {
            angka = 0;
        }

        angka = angka.toString();
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return "Rp. " + ribuanFormatted + ',' + desimal;
    }
</script>

<?= $this->endSection(); ?>