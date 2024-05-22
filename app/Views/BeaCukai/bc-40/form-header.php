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
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 4.0 - PEMBERITAHUAN PEMASUKAN BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN KE TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-header">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pengajuan
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input disabled id="header_no_pengajuan" value="<?= $noAju ?>" name="header_no_pengajuan" type="text" readonly class="header_no_pengajuan form-control" placeholder="">
                                    <label>Nomor Pengajuan</label>
                                </div>
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
                                            <option <?= !empty($selectedKantor) ? (encrypt($selectedKantor) == encrypt($k['kode']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['kode']) ?>">
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
                                            <option <?= !empty($bc40) ? (encrypt($bc40['kode_jenis_tpb']) == encrypt($k['description']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['description']) ?>">
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
                                            <option <?= !empty($bc40) ? (encrypt($bc40['kode_tujuan_pengiriman']) == encrypt(json_decode($k['value'])[1]) ? 'selected' : '') : '' ?> value="<?= encrypt(json_decode($k['value'])[1]) ?>">
                                                <?= json_decode($k['value'])[1] . " - " . strtoupper($k['description']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Tujuan Pengiriman</label>
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
            tanggal_penerimaan_barang: {
                required: true
            },
            header_kode_jenis_tpb: {
                required: true
            },
            header_kode_tujuan_pengiriman: {
                required: true
            },
        },
        messages: {
            header_no_pengajuan: {
                required: "Nomor pengajuan wajib diisi"
            },
            tanggal_penerimaan_barang: {
                required: "Tanggal pengiriman barang wajib diisi"
            },
            header_kode_jenis_tpb: {
                required: "Pilih jenis TPB"
            },
            header_kode_tujuan_pengiriman: {
                required: "Pilih tujuan pengiriman"
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
            Swal.fire({
                icon: 'question',
                title: 'Simpan Header ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-header"));
                    formData.append("header_kantor_pabean", $('#header_kantor_pabean').val());
                    formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-40/id/header"); ?>",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            }
                        },
                    });
                }
            })
        }
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