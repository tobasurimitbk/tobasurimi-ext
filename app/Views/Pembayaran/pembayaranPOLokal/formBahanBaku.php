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
                    <button class="btn btn-show-form btn-save float-right btn-submit-form">
                        Simpan
                    </button>
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
                <?php  ?>
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($detail) ? encrypt($detail['pembayaranDetail']['id']) : ""; ?>" />
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
                            <select class="form-select" <?= !empty($detail) ? '' : '' ?> name="bank_id" id="bank_id">
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
                                    <input autocomplete="one-time-code" class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? 'value="' . date('d/m/Y', strtotime($detail['pembayaranDetail']['payment_date']))  . '"' : '' ?>>
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
                            <select <?= !empty($detail) ? '' : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
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
                    <div class="col-md-4">
                        <div <?= !empty($detail) ? 'disabled' : '' ?> class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" name="tipe_pembayaran" id="tipe_pembayaran">
                                <option disabled selected value="<?= !empty($detail['pembayaranDetail']['type_bayar']) ? $detail['pembayaranDetail']['type_bayar'] : '';  ?>"></option>
                                <option value="BULANAN">BULANAN</option>
                                <option value="HARIAN">HARIAN</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Bayar</label>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                                    <input <?= !empty($detail) ? 'disabled' : '' ?> value="<?= !empty($detail['pembayaranDetail']['bulan']) ? $detail['pembayaranDetail']['bulan'] : '' ?>" type="text" class="form-control">
                                    <label for="floatingInput" style="z-index: 1;">Pilih Bulan</label>
                                </div>
                            <?php else : ?>
                                <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                    <input <?= !empty($detail) ? 'disabled' : '' ?> value="<?= !empty($detail['pembayaranDetail']['bulan']) ? $detail['pembayaranDetail']['bulan'] : '' ?>" type="month" name="bulan" id="bulan" class="form-control">
                                    <label for="floatingInput" style="z-index: 1;">Pilih Bulan</label>
                                </div>
                            <?php endif; ?>

                        </div>
                        <div class="harian-form">
                            <?php if (!empty($detail)) : ?>
                                <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                    <select class="form-select" name="lpb[]" id="lpb" disabled multiple>


                                        <?php foreach ($detail['pembayaranDetail']['multiple_lpb_no'] as $d) :  ?>
                                            <option selected value="<?= $d; ?>"><?= $d; ?> </option>
                                        <?php endforeach; ?>

                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">No Dokumen LPB</label>
                                </div>
                            <?php else : ?>
                                <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                    <select class="form-select" name="lpb[]" id="lpb">
                                        <option disabled selected value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">No Dokumen LPB</label>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? '' : '' ?> class="form-select " name="payment_method" id="payment_method">
                                <option disabled selected value="">Pilih Metode Pembayaran</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Cash" ? 'selected' : '') : '' ?> value="Cash">Cash</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Debit" ? 'selected' : '') : '' ?> value="Debit">Debit</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? '' : '' ?> name="pembayaran_oleh" id="pembayaran_oleh" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['pembayaranDetail']['pembayaran_oleh'] : session()->get("login")->name; ?>" type="text" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? '' : '' ?> name="akun_kas" id="akun_kas">
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
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Pembayaran</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Potongan Panjar</button>
                        </li>

                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
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
                                                    <th style="text-align: center;">Total Bayar</th>
                                                    <?php if (!empty($detail)) :  ?>
                                                        <th style="text-align: center;">Sisa Bayar</th>
                                                    <?php endif; ?>
                                                    <th style="text-align: center;">Input Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body-table" style="text-align: center;">
                                                <tr style="color: whitesmoke;">
                                                    <td colspan="10">Tidak Ada Pembayaran</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable-panjar" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th onclick="changeSort('no_panjar')">No. Panjar</th>
                                                <th onclick="changeSort('payment_date')">Payment Date</th>
                                                <th onclick="changeSort('payment_amount')">Total Panjar</th>
                                                <th>Sisa Panjar</th>
                                                <th>Bayar Panjar </th>
                                            </tr>

                                        </thead>
                                        <tbody class="body-table" id="body-table-panjar" style="cursor: pointer;">
                                            <tr style="color: whitesmoke;">
                                                <td colspan="7" style="text-align: center;">Tidak ada Panjar</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>



                        </div>

                    </div>

                </div>


                <!-- <div class="col-subtitle-modal mt-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">Data pembelian yang sudah diterima</label>
                        </div>
                    </div>
                </div> -->
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
    const csrf = $(`[name="${csrfToken}"]`);
    const table = $('#dataTable');
    var listPoNo = [];
    var listPoID = [];
    var listPanjar = [];
    var listPembayaran = [];

    <?php if (!empty($detail)) : ?>

        $.ajax({
            url: "<?= base_url("/pembayaran-po-lokal-bb/get-list-po-paid"); ?>",
            data: {
                pembayaran_id: $("#id").val(),
            },
            method: "GET",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            success: function(response) {
                listPembayaran = [];
                listPembayaran = response.data.detail;
                listPanjar = [];
                listPanjar = response.data.panjar

                csrf.val(response.token);
                drawPaidPanjarTable(listPanjar);
                drawPaidTable(response.data);
            }
        });
    <?php endif; ?>


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
            payment_method: {
                required: true
            },
            akun_kas: {
                required: true
            },
            bayar_panjar: {
                digits: true
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
            payment_method: {
                required: "Metode pembayaran wajib diisi"
            },
            akun_kas: {
                required: "Akun kas wajib diisi"
            },
            bayar_panjar: {
                digits: "harus berupa angka"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("multiple_lpb_id")) {
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
        theme: "bootstrap-5",
        multiple: true
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
                listPembayaran = [];
                listPembayaran = response.data;


                csrf.val(response.token);
                drawTable(listPembayaran);
                $('#nominal_pembayaran').val(response.data.sisaNumber);
            }
        });
    });

    $('#lpb').change(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);

        var lpbID = JSON.stringify($(this).val());
        var supplierID = $('#supplier_id').val();
        var formData = new FormData();

        formData.append("supplierID", $('#supplier_id').val());
        formData.append("lpbID", lpbID);
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
                listPembayaran = [];
                listPembayaran = response.data;

                csrf.val(response.token);
                drawTable(listPembayaran);
                $('#nominal_pembayaran').val(response.data.sisaNumber);
            }
        });
    });

    $(".btn-submit-form").click(function() {
        var id = $('.id').val();
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);


        //APPEND BAYAR PANJAR TO listPanjar
        $.each(listPanjar, function(i, v) {
            var element = $('input[data-id="' + v.id + '"].bayar_panjar');
            var input_user = (element.val());
            listPanjar[i].bayar_panjar = input_user;
        });

        //appemd pembayanran tp listPembayaran
        $.each(listPembayaran, function(i, v) {
            var element = $('input[data-id="' + v.penerimaan_barang_detail_id + '"].pembayaran');
            var input_user = (element.val());
            // listPembayaran[i].id = v.id;
            listPembayaran[i].pembayaran_user_input = input_user;

        });


        if (id) {
            // UPDATE
            let formData = new FormData(document.querySelector(".create-form"));

            formData.append("panjarList", JSON.stringify(listPanjar));
            formData.append("pembayaranList", JSON.stringify(listPembayaran));
            $.ajax({

                url: "<?= base_url("pembayaran-po-lokal-bb/update"); ?>",
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
                            formData.append("panjarList", JSON.stringify(listPanjar));
                            formData.append("pembayaranList", JSON.stringify(listPembayaran));

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

    function drawPaidTable(data) {
        var no = 1;
        const table = $('#dataTable');
        table.find('tbody').empty();

        $.each(data.detail, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:center;">').text(no++));
            newRow.append($('<td style="text-align:center;">').text(v.tanggal_LPB));
            newRow.append($('<td style="text-align:center;">').text(v.no_penerimaan_barang));
            newRow.append($('<td style="text-align:center;">').text(v.tanggal_PO));
            newRow.append($('<td style="text-align:center;">').text(v.po_no));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.total_order));
            newRow.append($('<td style="text-align:center;">').text(v.total_diterima));
            newRow.append($('<td style="text-align:center;">').text(v.total_tagihan));
            newRow.append($('<td style="text-align:center;">').text(v.sisa_pembayaran));
            newRow.append($('<td>').html(
                `
                        <input   oninput="limitInputBayar(this, ${v.total_number + v.sisa_pembayaran})" autocomplete="one-time-code" data-id="${v.penerimaan_barang_detail_id}"  class="form-control pembayaran" type="text" value="${v.total_number}" name = "pembayaran" style="height:40px">
                                `
            ));
            table.find('tbody').append(newRow);
        });
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>TOTAL PEMBAYARAN  </b></td>'));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append($('<td style="text-align:right;" ><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-pembayaran trigger-input" type="text" value="' + data.total_pembayaran + '" name = "total_pembayaran"  readonly>' +
            '</b></td>'));
        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>POTONGAN/DISKON</b></td>'));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append($('<td style="text-align:center;"><b>' +
            '  <input <?= !empty($detail) ? 'disabled' : '' ?> oninput="preventNegativeInput(this)" name="potongan" id="potongan" autocomplete="one-time-code" value="<?= !empty($detail) ? number_format($detail['pembayaranDetail']['potongan_harga'], 2) : '0' ?>" type="text" class="form-control trigger-input" placeholder="Nominal Pembayaran">' +
            '</b></td>'));

        table.find('tbody').append(newRow);
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>TOTAL PEMBAYARAN PANJAR</b></td>'));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar trigger-input" type="text" value="' + data.total_bayar_panjar + ' " name = "total_pembayaran_panjar" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>GRAND TOTAL</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' + data.total_order + '</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' + data.total_diterima + '</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' + data.total_tagihan + '</b></td>'));
        newRow.append(($('<td </td>')));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control grand-total" type="text" value="' + data.total_akhir + '" name = "grand_total"  readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

    }

    function drawTable(data) {
        var no = 1;

        var TotalOrder = 0;
        var TotalDiterima = 0;
        var TotalHarga = 0;


        const table = $('#dataTable');
        table.find('tbody').empty();
        // clear res
        listPoID.length = 0;
        listPoNo.length = 0;
        $.each(data, function(i, v) {
            // push po id
            listPoID.push({
                poID: v.penerimaan_barang_id
            });
            // push po no
            listPoNo.push({
                poNo: v.no_penerimaan_barang
            });
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:center;">').text(no++));
            newRow.append($('<td style="text-align:center;">').text(v.tanggal_LPB));
            newRow.append($('<td style="text-align:center;">').text(v.no_penerimaan_barang));
            newRow.append($('<td style="text-align:center;">').text(v.tanggal_PO));
            newRow.append($('<td style="text-align:center;">').text(v.po_no));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.total_order));
            newRow.append($('<td style="text-align:center;">').text(v.total_diterima));
            newRow.append($('<td style="text-align:center;">').text(v.total_tagihan));
            newRow.append($('<td>').html(
                `
                        <input   oninput="limitInputBayar(this, ${v.total_tagihan_number})" autocomplete="one-time-code" data-id="${v.penerimaan_barang_detail_id}"  class="form-control pembayaran" type="text" value="" name = "pembayaran" style="height:40px">
                                `
            ));
            table.find('tbody').append(newRow);

            TotalOrder += parseFloat(v.total_order);
            TotalDiterima += parseFloat(v.total_diterima);
            TotalHarga += parseFloat(v.total_tagihan_number);

        });

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>TOTAL PEMBAYARAN  </b></td>'));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append($('<td style="text-align:right;" ><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-pembayaran trigger-input" type="text" value="" name = "total_pembayaran"  readonly>' +
            '</b></td>'));
        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>POTONGAN/DISKON</b></td>'));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append($('<td style="text-align:center;"><b>' +
            '  <input <?= !empty($detail) ? 'disabled' : '' ?> oninput="preventNegativeInput(this)" name="potongan" id="potongan" autocomplete="one-time-code" value="<?= !empty($detail) ? number_format($detail['pembayaranDetail']['potongan_harga'], 2) : '0' ?>" type="text" class="form-control trigger-input" placeholder="Nominal Pembayaran">' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>TOTAL PEMBAYARAN PANJAR</b></td>'));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append(($('<td </td>')));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar trigger-input" type="text" value="" name = "total_pembayaran_panjar" readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);

        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="6"><b>GRAND TOTAL</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' + TotalOrder + '</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' + TotalDiterima + '</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' + TotalHarga + '</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control grand-total" type="text" value="" name = "grand_total"  readonly>' +
            '</b></td>'));

        table.find('tbody').append(newRow);
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



    //get supplier id for panjar
    $('#supplier_id').change(function() {
        var supplierId = $('#supplier_id option:selected').val();
        $.ajax({
            url: `<?= base_url('/pembayaran-po-lokal/get-panjar'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                supplier_id: supplierId,
            },
            dataType: "json",
            success: function(res) {
                //after getting the data

                listPanjar = [];
                listPanjar = res.data;
                appendPanjarNo(listPanjar);


            }
        });
    });


    function drawPaidPanjarTable(data) {
        const tablePanjar = $('#dataTable-panjar');
        tablePanjar.find('tbody').empty();
        tablePanjar.find('tfoot').empty();

        if (data.length > 0) {
            let no = 1;
            $("#no_panjar").empty();
            tablePanjar.find('tbody').empty();

            $.each(data, function(i, v) {

                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="width: 10px;">').text(no++));
                newRow.append($('<td>').text(v.no_panjar));
                newRow.append($('<td>').text(formatDate(v.payment_date)));
                newRow.append($('<td>').text((v.total_panjar)));
                newRow.append($('<td>').text((v.sisa_panjar)));

                newRow.append($('<td>').html(
                    `
                        <input  class="form-control bayar_panjar" oninput="limitInputBayar(this, ${v.sisa_panjar_number})" autocomplete="one-time-code" data-id="${v.id}"  class="form-control" type="text" value="${v.bayar_panjar}" name = "bayar_panjar" style="height:40px">
                            `
                ));
                tablePanjar.find('tbody').append(newRow);
            })
        } else {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td colspan="8" style="text-align:center;">Tidak Ada Panjar</td>'));
            tablePanjar.find('tbody').append(newRow);
        }

    }

    //append the panjar data
    function appendPanjarNo(data) {
        const tablePanjar = $('#dataTable-panjar');
        tablePanjar.find('tbody').empty();
        tablePanjar.find('tfoot').empty();

        if (data.length > 0) {
            let no = 1;
            let found = false;
            $("#no_panjar").empty();
            tablePanjar.find('tbody').empty();

            $.each(data, function(i, v) {
                if (v.sisa_panjar_number > 0) {
                    found = true;

                    var newRow = $('<tr style="color:whitesmoke;">');
                    newRow.append($('<td style="width: 10px;">').text(no++));
                    newRow.append($('<td>').text(v.no_panjar));
                    newRow.append($('<td>').text(formatDate(v.payment_date)));
                    newRow.append($('<td>').text((v.total_panjar)));
                    newRow.append($('<td>').text((v.sisa_panjar)));

                    newRow.append($('<td>').html(
                        `
                        <input  class="form-control bayar_panjar" oninput="limitInputBayar(this, ${v.sisa_panjar_number})" autocomplete="one-time-code" data-id="${v.id}"  class="form-control" type="text" value="" name = "bayar_panjar" style="height:40px">
                            `
                    ));
                    tablePanjar.find('tbody').append(newRow);
                }

            });
            if (!found) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Panjar</td>'));
                tablePanjar.find('tbody').append(newRow);
            }
        } else {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Panjar</td>'));
            tablePanjar.find('tbody').append(newRow);
        }

    }


    //get PanjarID
    $("#no_panjar").change(function() {
        var panjar_id = [];
        $("select option:selected").each(function() {
            panjar_id.push($(this).val());
        });
        $.ajax({
            url: `<?= base_url('pembayaran-po-lokal/get-panjar-amount'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                panjar_id: panjar_id
            },
            dataType: "json",
            success: function(res) {
                // APPEND TO DROPDOWN
                appendPanjarAmount(res.data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

    function limitInputBayar(input, maxAmount) {
        var inputValue = input.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');

        // If numericValue is not a valid number, set it to '0'
        if (isNaN(parseFloat(numericValue))) {
            input.value = '0';
        } else {
            input.value = numericValue;
        }

        // Convert numericValue to a float for comparison
        if (parseFloat(numericValue) > maxAmount) {
            input.value = maxAmount;
        }
    }



    function formatDate(dateString) {

        let parts = dateString.split("-");
        let reversedParts = parts.reverse();
        let formattedDate = reversedParts.join("/");
        return formattedDate;
    }


    $(document).on("input", ".pembayaran", function() {
        var sum = 0;
        $(".pembayaran").each(function() {
            sum += +$(this).val();
        });
        $(".total-pembayaran").val(sum);
        updateGrandTotal()
    });

    $(document).on("input", ".bayar_panjar", function() {
        var sum = 0;
        $(".bayar_panjar").each(function() {
            sum += +$(this).val();
        });
        $(".total-bayar-panjar").val(sum);
        updateGrandTotal()
    });

    $(document).on("input", "#potongan", function() {
        var totalBayarPanjar = parseFloat($(".total-bayar-panjar").val()) || 0;
        var totalPembayaran = parseFloat($(".total-pembayaran").val()) || 0;
        var maxPotongan = totalPembayaran - totalBayarPanjar;
        limitInputBayar(this, maxPotongan);

        updateGrandTotal();
    });





    function updateGrandTotal(potongan) {
        var totalBayarPanjar = parseFloat($(".total-bayar-panjar").val()) || 0;
        var totalPembayaran = parseFloat($(".total-pembayaran").val()) || 0;
        var potongan = parseFloat($("#potongan").val()) || 0;


        var total = totalPembayaran - totalBayarPanjar - potongan;

        $(".grand-total").val(total); // Setting the total with 2 decimal places
    }
</script>

<?= $this->endSection(); ?>