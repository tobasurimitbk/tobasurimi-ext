<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include_once('header.php') ?>
    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold;">
                BC 2.5 - PEMBERITAHUAN IMPOR BARANG DARI TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-header">
                    <input type="hidden" name="id" id="id" class="id" value="<?= encrypt($bc25['id']) ?>">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pengajuan
                            </label>
                            <div class="mt-1">
                                <div class="input-group">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input disabled id="header_no_pengajuan" value="<?= $noAju ?>" name="header_no_pengajuan" type="text" readonly class="header_no_pengajuan form-control" placeholder="">
                                        <label>Nomor Pengajuan</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-success" type="button" id="btnUpdateNoAjuModal">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                            <div class="input-group">
                                <div class="form-floating mb-3">
                                    <input autocomplete="one-time-code" value="<?= $bc25['no_daftar'] ?>" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar (Opsional)">
                                    <label for="floatingInput">Nomor Daftar (Opsional)</label>
                                </div>
                                <?php if (!empty($bc25)): ?>
                                    <?php if ($bc25['status_dokumen'] == "Sudah Kirim"): ?>
                                        <div class="input-group-append" style="height:50px;">
                                            <button class="btn btn-success" data-toggle="modal" type="button" onclick="ambilNoDaftar()">
                                                <i class="fa-solid fa-download"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" type="text" class="form-control tanggal" id="tanggal" name="tanggal" placeholder="Tanggal" value="<?= date('d/m/Y', strtotime($bc25['tanggal'])) ?>">
                                <label for="floatingInput">Tanggal Dokumen</label>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Kantor Pabean
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select header_kantor_pabean" disabled id="header_kantor_pabean" name="header_kantor_pabean" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option <?= !empty($selectedKantor) ? (encrypt($selectedKantor) == encrypt($k['kode']) ? 'selected' : '') : '' ?> value="<?= ($k['kode']) ?>">
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kantor Pabean</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Keterangan Lain
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select header_kode_jenis_tpb" id="header_kode_jenis_tpb" name="header_kode_jenis_tpb" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeTujuanTpb as $k) : ?>
                                            <option <?= !empty($payload) ? ($payload->kodeJenisTpb == $k['description'] ? 'selected' : '') : '' ?> value="<?= $k['description'] ?>">
                                                <?= strtoupper($k['description']) . " - " . strtoupper($k['value']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Jenis TPB</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select header_kode_tujuan_pengiriman" id="header_kode_tujuan_pengiriman" name="header_kode_tujuan_pengiriman" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeTujuanPengiriman as $k) : ?>
                                            <option <?= !empty($payload) ? ($payload->kodeTujuanPengiriman == json_decode($k['value'])[1] ? 'selected' : '') : '' ?> value="<?= json_decode($k['value'])[1] ?>">
                                                <?= json_decode($k['value'])[1] . " - " . strtoupper($k['description']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Tujuan Pengiriman</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select header_kode_cara_bayar" id="header_kode_cara_bayar" name="header_kode_cara_bayar" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeCaraBayar as $k) : ?>
                                            <option <?= !empty($payload) ? ($payload->kodeCaraBayar == $k['value'] ? 'selected' : '') : '' ?> value="<?= $k['value'] ?>">
                                                <?= $k['value'] . " - " . $k['description'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Cara Bayar</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select header_kode_lokasi_bayar" id="header_kode_lokasi_bayar" name="header_kode_lokasi_bayar" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeLokasiBayar as $k) : ?>
                                            <option <?= !empty($payload) ? ($payload->kodeLokasiBayar == $k['value'] ? 'selected' : '') : '' ?> value="<?= $k['value'] ?>">
                                                <?= $k['value'] . " - " . $k['description'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Lokasi Bayar</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary" id="btn-simpan-perubahan" style="float: right;">
                        Simpan Perubahan
                    </a>
                    <button class="btn btn-primary" type="button" disabled id="btn-loading" style="float: right;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalUpdateNoAju" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Nomor Pengajuan</h5>
            </div>
            <form id="form-update-noaju">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="tanggal_pengajuan" value="" name="tanggal_pengajuan" type="text" class="tanggal_pengajuan form-control" placeholder="">
                                    <label>Tanggal</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_urut_dokumen" name="no_urut_dokumen" type="number" class="no_urut_dokumen form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Nomor Urut</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="kode_kantor" name="kode_kantor" type="number" class="kode_kantor form-control" placeholder="" oninput="event.target.value = /^\d{0,6}$/.test(event.target.value) ? event.target.value : ''">
                                <label>Kode Kantor</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="no_pengajuan" value="<?= $noAju ?>" name="no_pengajuan" type="text" readonly class="no_pengajuan form-control" placeholder="">
                                <label>Preview Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" id="btnHideModalNoAju">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitUpdateNoAju">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    // init loading
    $('#btn-loading').hide();

    $('#header_kode_tujuan_pengiriman').select2({
        placeholder: "Pilih Tujuan Pengiriman",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#header_kode_jenis_tpb').select2({
        placeholder: "Pilih Jenis TPB",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#header_kode_cara_bayar').select2({
        placeholder: "Pilih Kode Cara Bayar",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#header_kode_lokasi_bayar').select2({
        placeholder: "Pilih Kode Lokasi Bayar",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("#tanggal_penerimaan_barang").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    var validatorHeader = $("#form-header").validate({
        rules: {
            header_no_pengajuan: {
                required: true
            },
            tanggal: {
                required: true
            },
            header_kantor_pabean: {
                required: true
            },
            header_kode_jenis_tpb: {
                required: true
            },
            header_kode_tujuan_pengiriman: {
                required: true
            },
            header_kode_cara_bayar: {
                required: true
            },
        },
        messages: {
            header_no_pengajuan: {
                required: "Nomor pengajuan wajib diisi"
            },
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            header_kantor_pabean: {
                required: "Kantor pabean wajib diisi"
            },
            header_kode_jenis_tpb: {
                required: "Pilih jenis TPB"
            },
            header_kode_tujuan_pengiriman: {
                required: "Pilih tujuan pengiriman"
            },
            header_kode_cara_bayar: {
                required: "Pilih cara bayar"
            },
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

    var validatorNoAju = $(".form-update-noaju").validate({
        rules: {
            tanggal_pengajuan: {
                required: true
            },
            no_urut_dokumen: {
                required: true
            },
            kode_kantor: {
                required: true
            },
            no_pengajuan: {
                required: true
            },
        },
        messages: {
            tanggal_pengajuan: {
                required: "Tanggal pengajuan wajib diisi"
            },
            no_urut_dokumen: {
                required: "No urut wajib diisi"
            },
            kode_kantor: {
                required: "Kode kantor wajib diisi"
            },
            no_pengajuan: {
                required: "No pengajuan wajib diisi"
            },
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

    $('#btn-simpan-perubahan').click(function() {
        if ($('#form-header').valid()) {
            var formData = new FormData(document.querySelector("#form-header"));
            formData.append("tanggal", $('#tanggal').val());
            formData.append("header_kantor_pabean", $('#header_kantor_pabean').val());
            formData.append("header_no_pengajuan", $('#header_no_pengajuan').val());

            $.ajax({
                url: "<?= base_url("bea-cukai-bc-25/id/header"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    $('#btn-loading').show();
                    $('#btn-simpan-perubahan').hide();
                },
                complete: function() {
                    $('#btn-loading').hide();
                    $('#btn-simpan-perubahan').show();
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        location.reload();
                    }
                },
            });
        }
    });

    $('#btnSubmitUpdateNoAju').click(function(e) {
        e.preventDefault();
        if ($('#form-update-noaju').valid()) {
            let data = new FormData(document.querySelector("#form-update-noaju"));
            data.append("id", "<?= encrypt($bc25['id']) ?>");
            $.ajax({
                url: "<?= base_url("bea-cukai-bc-25/update-no-aju"); ?>",
                data: data,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading()
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        location.reload();
                    }
                },
            });

        }
    });

    $("#tanggal_pengajuan,#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#btnHideModalNoAju').click(function(e) {
        e.preventDefault();
        $('#modalUpdateNoAju').modal('hide');
    });

    $('#btnUpdateNoAjuModal').click(function(e) {
        e.preventDefault();
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");

        var year = splitValues[2].substring(0, 4);
        var month = splitValues[2].substring(4, 6);
        var day = splitValues[2].substring(6, 8);

        var formattedDate = day + '/' + month + '/' + year;

        $('#tanggal_pengajuan').val(formattedDate);
        $('#no_pengajuan').val(noAju);
        $('#no_urut_dokumen').val(splitValues[3]);
        $('#kode_kantor').val(splitValues[1]);

        $('#modalUpdateNoAju').modal('show');
    });

    // NO AJU ACTION
    $('#no_urut_dokumen').keyup(function() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");
        splitValues[3] = $(this).val();
        $('#no_pengajuan').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });
    $('#kode_kantor').keyup(function() {
        var noAju = $('#no_pengajuan').val();
        var splitValues = noAju.split("-");
        splitValues[1] = $(this).val();
        $('#no_pengajuan').val(splitValues[0] + '-' + splitValues[1] + '-' + splitValues[2] + '-' + splitValues[3]);
    });

    $("#tanggal_pengajuan").change(function() {
        var tanggalPengajuan = $(this).val();
        var noAju = $('#no_pengajuan').val();
        var tanggalPengajuanSplit = tanggalPengajuan.split("/");
        var noPengajuanSplit = noAju.split("-");
        $('#no_pengajuan').val(noPengajuanSplit[0] + '-' + noPengajuanSplit[1] + '-' + tanggalPengajuanSplit[2] + '' + tanggalPengajuanSplit[1] + '' + tanggalPengajuanSplit[0] + '-' + noPengajuanSplit[3]);
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
</script>


<?= $this->endSection(); ?>