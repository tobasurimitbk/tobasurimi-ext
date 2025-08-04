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
                BC 2.7 - PEMBERITAHUAN PENGELUARAN UNTUK DIANGKUT DARI TEMPAT PENIMBUNAN BERIKAT KE TEMPAT PENIMBUNAN BERIKAT LAINNYA
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-header">
                    <input type="hidden" name="id" id="id" class="id" value="<?= encrypt($bc27['id']) ?>">
                    <div class="row">
                        <div class="col-sm-6 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pengajuan Asal
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input disabled id="nomorAju" value="<?= $noAju ?>" name="nomorAju" type="text" readonly class="nomorAju form-control" placeholder="">
                                    <label>Nomor Pengajuan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeKantor" id="kodeKantor" name="kodeKantor" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option <?= $payload->kodeKantor == "" ? (encrypt($selectedKantor) == encrypt($k['kode']) ? 'selected' : '') : '' ?> <?= $payload->kodeKantor != "" ? ($payload->kodeKantor == $k['kode'] ? 'selected' : '') : "" ?> value="<?= ($k['kode']) ?>">
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kantor Pabean Asal</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeJenisTpb" id="kodeJenisTpb" name="kodeJenisTpb" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeTujuanTpb as $k) : ?>
                                            <option <?= !empty($payload) ? ($payload->kodeJenisTpb == $k['description'] ? 'selected' : '') : '' ?> value="<?= $k['description'] ?>">
                                                <?= strtoupper($k['description']) . " - " . strtoupper($k['value']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Jenis TPB Asal</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pengajuan Tujuan
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeKantorTujuan" id="kodeKantorTujuan" name="kodeKantorTujuan" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option <?= $payload->kodeKantorTujuan != "" ? ($payload->kodeKantorTujuan == $k['kode'] ? 'selected' : '') : "" ?> value="<?= ($k['kode']) ?>">
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kantor Pabean Tujuan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeTujuanTpb" id="kodeTujuanTpb" name="kodeTujuanTpb" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeTujuanTpb as $k) : ?>
                                            <option <?= !empty($payload) ? ($payload->kodeTujuanTpb == $k['description'] ? 'selected' : '') : '' ?> value="<?= $k['description'] ?>">
                                                <?= strtoupper($k['description']) . " - " . strtoupper($k['value']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Jenis TPB Tujuan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeTujuanPengiriman" id="kodeTujuanPengiriman" name="kodeTujuanPengiriman" aria-label="Floating label select example">
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

    $('#kodeTujuanPengiriman').select2({
        placeholder: "Pilih Tujuan Pengiriman",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kodeJenisTpb').select2({
        placeholder: "Pilih Jenis TPB Asal",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kodeKantor').select2({
        placeholder: "Pilih Kode Kantor Asal",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kodeTujuanTpb').select2({
        placeholder: "Pilih Jenis TPB Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kodeKantorTujuan').select2({
        placeholder: "Pilih Kode Kantor Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    });

    var validatorHeader = $("#form-header").validate({
        rules: {
            nomorAju: {
                required: true
            },
            kodeKantor: {
                required: true
            },
            kodeJenisTpb: {
                required: true
            },
            kodeKantorTujuan: {
                required: true
            },
            kodeTujuanTpb: {
                required: true
            },
            kodeTujuanPengiriman: {
                required: true
            },
        },
        messages: {
            nomorAju: {
                required: "Nomor pengajuan wajib diisi"
            },
            kodeKantor: {
                required: "Kode kantor wajib diisi"
            },
            kodeJenisTpb: {
                required: "Pilih jenis TPB"
            },
            kodeKantorTujuan: {
                required: "Pilih kantor tujuan"
            },
            kodeTujuanTpb: {
                required: "Pilih tujuan TPB"
            },
            kodeTujuanPengiriman: {
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
                    formData.append("nomorAju", $('#nomorAju').val());

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-27/id/header"); ?>",
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