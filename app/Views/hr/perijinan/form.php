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
            <?php if (!empty($data)) : ?>
                <a href="#" class="btn btn-hapus delete-parent float-right delete-perizinan" data-perizinan_id="<?= $data->id ?>">
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
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($data) ? $data->id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select employee_id" name="employee_id" id="employee_id">
                                <option value=""></option>
                                <?php if (!empty($dataEmployee)) : ?>
                                    <?php foreach ($dataEmployee as $employee) : ?>
                                        <option value="<?= $employee->id; ?>" <?= !empty($data) ? ($data->employee_id === $employee->id ? "selected" : "") : ""; ?>><?= $employee->nip; ?> - <?= $employee->name; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Nama Karyawan</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="date" class="form-control start_date" id="start_date" name="start_date" <?= !empty($data) ? 'disabled=true' :  ''; ?> value='<?= !empty($data) ? $data->periode :  ''; ?>' placeholder="Tanggal mulai">
                            <label for="floatingInput">Tanggal mulai</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="date" class="form-control end_date" id="end_date" name="end_date" <?= !empty($data) ? 'disabled=true' : ''; ?> value='<?= !empty($data) ? $data->periode :  ''; ?>' placeholder="Tanggal akhir">
                            <label for="floatingInput">Tanggal akhir</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select status" name="status" id="status" <?= !empty($data) ? ($data->status === true ? 'disabled=true' : '') : ''; ?>>
                                <option value=""></option>
                                <?php if (!empty($status)) : ?>
                                    <?php foreach ($status as $s) : ?>
                                        <option value="<?= $s; ?>" <?= !empty($data) ? ($data->status === $s ? "selected" : "") : ""; ?>><?= $s; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Status</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control jamMulaiLembur" id="jamMulaiLembur" name="jamMulaiLembur" <?= !empty($data) ? 'disabled=true' :  ''; ?> value='<?= !empty($data) ? $data->periode :  ''; ?>' placeholder="Tanggal mulai">
                            <label for="floatingInput">Jam mulai Lembur</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control jamSelesaiLembur" id="jamSelesaiLembur" name="jamSelesaiLembur" <?= !empty($data) ? 'disabled=true' : ''; ?> value='<?= !empty($data) ? $data->periode :  ''; ?>' placeholder="Tanggal akhir">
                            <label for="floatingInput">Jam selesai Lembur</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea autocomplete="one-time-code" class="form-control reason text-area-all" id="reason" name="reason" <?= !empty($data) ? ($data->reason === true ? 'disabled=true' : '') : ''; ?> placeholder="Keterangan"><?= !empty($data) ? $data->reason : ""; ?></textarea>
                            <label for="floatingInput">Keterangan (Opsional)</label>
                        </div>
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
            placeholder: "",
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
                required: "Status perizinan wajib diisi"
            },
            jamMulaiLembur: {
                required: "Jam mulai lembur wajib diisi"
            },
            jamSelesaiLembur: {
                required: "Jam selesai lembur wajib diisi"
            }
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

    $(function() {
        $('#jamMulaiLembur').datetimepicker({
            format: 'HH:mm',
            icons: {
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down'
            },
        });
        $('#jamSelesaiLembur').datetimepicker({
            format: 'HH:mm',
            icons: {
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down'
            },
        });
    });

    $('#status').change(function(e) {
        e.preventDefault();
        var status = $(this).val();
        var tanggalMulai = $('#start_date');
        var tanggalSelesai = $('#end_date');
        var jamMulaiLembur = $('#jamMulaiLembur');
        var jamSelesaiLembur = $('#jamSelesaiLembur');

        if (status === "LEMBUR") {
            jamMulaiLembur.attr('readonly', false);
            jamSelesaiLembur.attr('readonly', false);
            jamMulaiLembur.attr('required', true);
            jamSelesaiLembur.attr('required', true);
            tanggalSelesai.attr('readonly', true);
            tanggalSelesai.val(tanggalMulai.val());
        } else {
            jamMulaiLembur.attr('readonly', true);
            jamSelesaiLembur.attr('readonly', true);
            jamMulaiLembur.attr('required', false);
            jamSelesaiLembur.attr('required', false);
            tanggalSelesai.attr('readonly', false);
            jamMulaiLembur.val(null);
            jamSelesaiLembur.val(null);
        }
    });

    $('#start_date').change(function(e) {
        e.preventDefault();
        if ($('#status').val() == "LEMBUR") {
            var tanggalMulai = $(this).val();
            $('#end_date').val(tanggalMulai);
        }
    });
</script>

<?= $this->endSection(); ?>