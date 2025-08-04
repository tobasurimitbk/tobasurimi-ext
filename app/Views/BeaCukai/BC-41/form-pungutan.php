<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="card">
        <div class="card-header" style="font-weight: bold;">
            BC 4.1 - PEMBERITAHUAN PENGELUARAN KEMBALI BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN DARI TEMPAT PENIMBUNAN BERIKAT
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <form id="form-pungutan">
                <div class="row mt-1">
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Lokasi Bayar
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kodeLokasiBayar" id="kodeLokasiBayar" name="kodeLokasiBayar" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeLokasiBayar as $k) : ?>
                                        <option <?= !empty($payload) ? ($payload->kodeLokasiBayar == $k['value'] ? 'selected' : '') : '' ?> value="<?= $k['value'] ?>">
                                            <?= $k['value'] . " - " . $k['description'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Lokasi Bayar</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Wajib Bayar
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kodePembayar" id="kodePembayar" name="kodePembayar" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodePembayar as $k) : ?>
                                        <option <?= !empty($payload) ? ($payload->kodePembayar == $k['description'] ? 'selected' : '') : '' ?> value="<?= $k['description'] ?>">
                                            <?= $k['description'] . " - " . $k['value'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Kode Entitas</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Lokasi Bayar
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="nomorBuktiBayar" value="<?= $payload->nomorBuktiBayar ?>" name="nomorBuktiBayar" type="text" class="nomorBuktiBayar form-control">
                                <label>Nomor Bukti Bayar</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input value="<?= $payload->tanggalBuktiBayar != "" ? date('d/m/Y', strtotime($payload->tanggalBuktiBayar)) : "" ?>" autocomplete="one-time-code" name="tanggalBuktiBayar" type="text" placeholder="" class="form-control tanggalBuktiBayar" id="tanggalBuktiBayar">
                                    <label>Tanggal Bukti Bayar</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <a href="#" class="btn btn-primary mt-4" id="btn-simpan-perubahan" style="float: right;">
                Simpan Perubahan
            </a>
            <button class="btn btn-primary" type="button" disabled id="btn-loading" style="float: right;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading
            </button>
        </div>
    </div>

</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('#kodeLokasiBayar').select2({
        placeholder: "Pilih Kode Lokasi Bayar",
        theme: "bootstrap-5",
    });

    $('#kodePembayar').select2({
        placeholder: "Pilih Kode Entitas Yang Membayar",
        theme: "bootstrap-5",
    });

    $("#tanggalBuktiBayar").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var validatorPungutan = $("#form-pungutan").validate({
        rules: {
            kodeLokasiBayar: {
                required: true
            },
            kodePembayar: {
                required: true
            },
            nomorBuktiBayar: {
                required: true
            },
            tanggalBuktiBayar: {
                required: true
            },
        },
        messages: {
            kodeLokasiBayar: {
                required: "Pilih kode lokasi bayar"
            },
            kodePembayar: {
                required: "Pilih entitas"
            },
            nomorBuktiBayar: {
                required: "Nomor bukti bayar wajib diisi"
            },
            tanggalBuktiBayar: {
                required: "Tanggal bukti bayar wajib diisi"
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

    // init loading
    $('#btn-loading').hide();

    $('#btn-simpan-perubahan').click(function() {
        if ($('#form-pungutan').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Pungutan ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-pungutan"));
                    formData.append("id", "<?= encrypt($bc41['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-41/id/pungutan"); ?>",
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
</script>


<?= $this->endSection(); ?>