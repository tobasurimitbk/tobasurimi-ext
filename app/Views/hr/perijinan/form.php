<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("form-perijinan"); ?>">
                Batal
            </a>

            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-pinjaman-karyawan" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select employee_id" name="employee_id" id="employee_id" <?= !empty($data) ? ($data->is_posted === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($dataEmployee)) {
                                    foreach ($dataEmployee as $employee) {
                                ?>
                                        <option value="<?= $employee->id; ?>" <?= !empty($data) ? ($data->employee_id === $employee->id ? "selected" : "") : ""; ?>><?= $employee->nip; ?> - <?= $employee->name; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Nama Karyawan</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="date" class="form-control start_date" id="start_date" name="start_date" <?= !empty($data) ? ($data->start_date === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->start_date : ""; ?>" placeholder="Tanggal mulai">
                            <label for="floatingInput">Tanggal mulai</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="date" class="form-control end_date" id="end_date" name="end_date" <?= !empty($data) ? ($data->end_date === true ? 'disabled=true' : '') : ''; ?> value="<?= !empty($data) ? $data->end_date : ""; ?>" placeholder="Tanggal akhir">
                            <label for="floatingInput">Tanggal akhir</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select status" name="status" id="status" <?= !empty($data) ? ($data->status === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php
                                if (!empty($status)) {
                                    foreach ($status as $s) {
                                ?>
                                        <option value="<?= $s; ?>" <?= !empty($data) ? ($data->status === $s ? "selected" : "") : ""; ?>><?= $s; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Status</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea class="form-control reason" id="reason" name="reason" <?= !empty($data) ? ($data->reason === true ? 'disabled=true' : '') : ''; ?> placeholder="Keterangan"><?= !empty($data) ? $data->reason : ""; ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3" style="height: 50px;">
                            <label for="floatingInput">Posting</label>
                            <div class="switch-form-form-perijinan">
                                <label class="switch">
                                    <input class="is_posted" <?= !empty($data) ? ($data->is_posted === true ? 'disabled=true' : '') : ''; ?> name="is_posted" id="is_posted" type="checkbox" <?= !empty($data) ? ($data->is_posted === true ? 'checked' : '') : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
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

    $(document).ready(function() {
        // EMPLOYEE
        $('.employee_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.employee_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.employee_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.employee_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // STATUS
        $('.status').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.status')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.status')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.status')
            .parent('div')
            .find('label')
            .css('z-index', '1');
    })

    var validator = $(".create-form").validate({
        rules: {
            employee_id: {
                required: true
            },
            start_date: {
                required: true
            },
            end_date: {
                required: true
            },
            status: {
                required: true
            },
        },
        messages: {
            employee_id: {
                required: "Nama Karyawan wajib diisi"
            },
            start_date: {
                required: "Tanggal mulai wajib diisi"
            },
            end_date: {
                required: "Tanggal akhir wajib diisi"
            },
            status: {
                required: "Status wajib diisi"
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
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    setLoading()
                    let data = new FormData(document.querySelector(".create-form"));

                    let id = $(".id").val();

                    // UPDATE
                    if (id) {
                        $.ajax({
                            url: "<?= base_url("form-perijinan/update"); ?>",
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
                                            window.location.href = "<?= base_url("pinjaman-karyawan"); ?>";
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
                            url: "<?= base_url("form-perijinan/save"); ?>",
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
                                            window.location.href = "<?= base_url("pinjaman-karyawan"); ?>";
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