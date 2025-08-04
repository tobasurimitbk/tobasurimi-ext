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
            BC 2.7 - PEMBERITAHUAN PENGELUARAN UNTUK DIANGKUT DARI TEMPAT PENIMBUNAN BERIKAT KE TEMPAT PENIMBUNAN BERIKAT LAINNYA
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                Pernyataan
            </label>
            <div class="alert alert-secondary text-dark">
                Dengan ini saya menyatakan bertanggung jawab atas kebenaran hal-hal yang diberitahukan dalam dokumen ini dan keabsahan dokumen pelengkap pabean yang menjadi dasar pembuatan dokumen ini
            </div>
            <form id="form-pernyataan">
                <input type="hidden" name="id" value="<?= encrypt($bc27['id']) ?>">
                <div class="row">
                    <div class="col-sm mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Tempat & Tanggal
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= $payload->kotaTtd != "" ? $payload->kotaTtd : $ceisaSetting['tempat'] ?>" id="kotaTtd" name="kotaTtd" type="text" class="form-control kotaTtd" placeholder="">
                                <label>Tempat</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= $payload->tanggalTtd != "" ? date('d/m/Y', strtotime($payload->tanggalTtd)) : date('d/m/Y', strtotime(date('Y-m-d'))) ?>" id="tanggalTtd" name="tanggalTtd" type="text" class="form-control tanggalTtd" placeholder="">
                                <label>Tanggal</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Nama
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= $payload->namaTtd != "" ? $payload->namaTtd : $ceisaSetting['nama']  ?>" id="namaTtd" name="namaTtd" type="text" class="form-control namaTtd" placeholder="">
                                <label>Nama</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= $payload->jabatanTtd != "" ? $payload->jabatanTtd : $ceisaSetting['jabatan']  ?>" id="jabatanTtd" name="jabatanTtd" type="text" class="form-control jabatanTtd" placeholder="">
                                <label>Jabatan</label>
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
</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    // init loading
    $('#btn-loading').hide();

    $("#tanggalTtd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    var validatorPernyataan = $("#form-pernyataan").validate({
        rules: {
            kotaTtd: {
                required: true
            },
            tanggalTtd: {
                required: true
            },
            namaTtd: {
                required: true
            },
            jabatanTtd: {
                required: true
            },
        },
        messages: {
            kotaTtd: {
                required: "Tempat wajib diisi"
            },
            tanggalTtd: {
                required: "Tanggal wajib diisi"
            },
            namaTtd: {
                required: "Nama wajib diisi"
            },
            jabatanTtd: {
                required: "Jabatan wajib diisi"
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
        if ($('#form-pernyataan').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Pernyataan ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-pernyataan"));
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-27/id/pernyataan"); ?>",
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