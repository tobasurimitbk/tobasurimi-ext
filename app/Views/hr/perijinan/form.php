<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($data) ? "Ubah" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("form-perijinan"); ?>">
                Batal
            </a>
            <?php if (!empty($formPerijinan)) : ?>
                <a href="#" class="btn btn-hapus delete-parent float-right delete-perizinan" data-kode="<?= $formPerijinan['kode'] ?>">
                    Hapus
                </a>
            <?php endif; ?>
            <button class="btn btn-show-form btn-save float-right btn-submit">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-pinjaman-karyawan" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="kode" name="kode" id="kode" value="<?= !empty($formPerijinan) ? $formPerijinan['kode'] : "" ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($formPerijinan)) ? "disabled" : "" ?> class="form-select division_id" name="division_id" id="division_id">
                                <option value="">
                                    Pilih Divisi Terlebih Dahulu
                                </option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= (!empty($formPerijinan)) ?  ($formPerijinan['division_id'] == $d['id'] ? "selected" : "") : ""  ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Divisi</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($formPerijinan)) ? "disabled" : "" ?> class="form-select employee_id" name="employee_id" id="employee_id">
                                <option value="">
                                    Pilih Nama Karyawan
                                </option>
                                <?php if (!empty($formPerijinan)) : ?>
                                    <option selected value="<?= $formPerijinan['employee_id'] ?>">
                                        <?= $formPerijinan['name']; ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Pilih Nama Karyawan</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($formPerijinan)) ? $mulai : "" ?>" autocomplete="one-time-code" type="date" class="form-control start_date" id="start_date" name="start_date" <?= !empty($data) ? 'disabled=true' :  ''; ?> placeholder="Tanggal mulai">
                            <label for="floatingInput">Tanggal mulai</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($formPerijinan)) ? $selesai : "" ?>" autocomplete="one-time-code" type="date" class="form-control end_date" id="end_date" name="end_date" <?= !empty($data) ? 'disabled=true' : ''; ?> placeholder="Tanggal akhir">
                            <label for="floatingInput">Tanggal akhir</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select status" name="status" id="status" <?= !empty($data) ? ($data->status === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php foreach ($status as $s) : ?>
                                    <option <?= (!empty($formPerijinan)) ?  ($formPerijinan['status'] == $s ? "selected" : "") : ""  ?> value="<?= $s; ?>"><?= $s; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Status</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea autocomplete="one-time-code" class="form-control reason text-area-all" id="reason" name="reason" placeholder="Keterangan"><?= (!empty($formPerijinan) ? $formPerijinan['reason'] : "") ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                    <?php if (!empty($formPerijinan)) : ?>
                        <input type="hidden" name="division_id" value="<?= $formPerijinan['division_id']; ?>">
                        <input type="hidden" name="employee_id" value="<?= $formPerijinan['employee_id'] ?>">
                    <?php endif; ?>
                </div>
                <label class="mt-2">
                    Persetujuan Perizinan
                </label>
                <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;">
                    <div class="form-check form-switch form-switch-lg">
                        <input <?= !empty($formPerijinan) ? ($formPerijinan['is_approval'] == "1" ? "checked" : "") : "" ?> class="form-check-input" type="checkbox" name="is_approval" id="is_approval">
                        <label class="form-check-label" for="is_approval"></label>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';

    $(document).ready(function() {
        // EMPLOYEE
        $('.employee_id').select2({
            placeholder: "Pilih Karyawan",
            theme: "bootstrap-5",
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
            placeholder: "Pilih Status Perizinan",
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

        $('.division_id').select2({
            placeholder: "Pilih Divisi Terlebih Dahulu",
            theme: "bootstrap-5"
        });

        $('.division_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.division_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');
    })

    var validator = $(".create-form").validate({
        rules: {
            division_id: {
                required: true
            },
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
            division_id: {
                required: "Divisi wajib diisi"
            },
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

                    let kode = $(".kode").val();

                    // UPDATE
                    if (kode) {
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
                                            location.replace(`<?= base_url("form-perijinan/id"); ?>/${response.kode}`);
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
                                            window.location.href = "<?= base_url("form-perijinan"); ?>";
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
    });

    $('.delete-perizinan').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Perizinan ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            setLoading()
            // csrf
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);
            var perizinanID = $(this).data('perizinan_id');

            var formData = new FormData();
            formData.append('id', perizinanID);

            $.ajax({
                url: "<?= base_url("form-perijinan/delete"); ?>",
                data: formData,
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
                                window.location.href = "<?= base_url("form-perijinan"); ?>";
                            });
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
                        title: 'Perizinan gagal dihapus, coba Lagi',
                        confirmButtonColor: '#4e73df',
                    })
                    stopLoading()
                }
            });

        });
    });

    $("select[name='division_id']").on('change', function(e) {
        e.preventDefault();
        const csrf = $(`[name="${csrfToken}"]`);
        var divisionID = $(this).val();
        var formData = new FormData();
        formData.append('divisionID', divisionID);

        $.ajax({
            url: "<?= base_url("form-perijinan/employees"); ?>",
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                var employeeSelect = $("select[name='employee_id']");
                employeeSelect.empty();
                $.each(response.data, function(index, data) {
                    var option = $("<option></option>")
                        .attr("value", data.id)
                        .text(data.name);
                    employeeSelect.append(option);
                });

            },
            onError: function(response) {
                csrf.val(response.token);

            }
        });

    });
</script>

<?= $this->endSection(); ?>