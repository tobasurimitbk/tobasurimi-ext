<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("shift"); ?>">
                Kembali
            </a>

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">


            <form class="create-form form-add-shift" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control nama_shift" id="nama_shift" name="nama_shift" <?= !empty($data) ? ($data->nama_shift === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->nama_shift : ""; ?>" placeholder="Shift Name">
                            <label for="floatingInput">Shift Name</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="time" class="form-control SOT" id="SOT" name="SOT" <?= !empty($data) ? ($data->SOT === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->SOT : ""; ?>" placeholder="Start of time">
                            <label for="floatingInput">Jam Masuk</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="time" class="form-control EOT" id="EOT" name="EOT" <?= !empty($data) ? ($data->EOT === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->EOT : ""; ?>" placeholder="End of time">
                            <label for="floatingInput">Jam Pulang</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="time" class="form-control BSOT" id="BSOT" name="BSOT" <?= !empty($data) ? ($data->BSOT === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->BSOT : ""; ?>" placeholder="Break start of time">
                            <label for="floatingInput">Jam Istirahat</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="time" class="form-control BEOT" id="BEOT" name="BEOT" <?= !empty($data) ? ($data->BEOT === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->BEOT : ""; ?>" placeholder="Break end of time">
                            <label for="floatingInput">Jam Selesai Istirahat</label>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    var validator = $(".create-form").validate({
        rules: {
            nama_shift: {
                required: true
            },
            SOT: {
                required: true
            },
            EOT: {
                required: true
            },
            BSOT: {
                required: true
            },
            BEOT: {
                required: true
            },
        },
        messages: {
            nama_shift: {
                required: "Nama shift wajib diisi"
            },
            SOT: {
                required: "Jam masuk wajib diisi"
            },
            EOT: {
                required: "Jam pulang wajib diisi"
            },
            BSOT: {
                required: "Jam istirahat wajib diisi"
            },
            BEOT: {
                required: "Jam selesai istirahat wajib diisi"
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


    $(".btn-submit").click(function() {
        if ($(".create-form").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    setLoading()
                    let data = new FormData(document.querySelector(".create-form"));

                    let id = $(".id").val();

                    // UPDATE
                    if (id) {
                        $.ajax({
                            url: "<?= base_url("shift/update"); ?>",
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
                                    stopLoading()
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            window.location.href = "<?= base_url("shift"); ?>";
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
                    // CREATE
                    else {
                        $.ajax({
                            url: "<?= base_url("shift/save"); ?>",
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
                                    stopLoading()
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            window.location.href = "<?= base_url("shift"); ?>";
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
    })
</script>

<?= $this->endSection(); ?>