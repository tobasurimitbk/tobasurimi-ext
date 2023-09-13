<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($jamKerja) ? "Update" : "Tambah"; ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jam-kerja"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit">
                <?= !empty($jamKerja) ? "Update" : "Tambah"; ?>
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <label class="form-label font-weight-bold lable-title mt-2">
                Informasi Jam Kerja
            </label>
            <form action="#" method="post" id="formPost" class="mt-4">
                <?= csrf_field() ?>
                <input type="hidden" value="<?= ($jamKerja != null) ? $jamKerja['id'] : null ?>" name="jamKerjaID">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($jamKerja)) ? $jamKerja['jenis'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control" name="jenisJamKerja" placeholder="Jenis Jam Kerja">
                            <label for="floatingInput">Jenis Jam Kerja</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" name="status" readonly value="<?= (!empty($jamKerja)) ? 'Jam Kerja Sudah Diatur' : 'Jam Kerja Belum Diatur' ?> " placeholder="Status">
                            <label for="floatingInput">Status</label>
                        </div>
                    </div>
                </div>
                <div class="overflow-auto">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 10px;">No</th>
                                <th rowspan="2" style="vertical-align : middle;text-align:center;">Hari</th>
                                <th colspan="1">Jam Masuk</th>
                                <th colspan="2">Jam Istirahat</th>
                                <th colspan="1">Jam Pulang</th>
                            </tr>
                        </thead>
                        <tbody class="text-center text-bold">
                            <?php $nomor = 1; ?>
                            <?php foreach ($hari as $i => $v) : ?>
                                <?php
                                $jamKerjaDetailModel = new \App\Models\JamKerjaDetailModel();
                                $jamKerjaDetail = $jamKerjaDetailModel->where('hari', $v['value'])
                                    ->where('jam_kerja_id', ($jamKerja != null) ? $jamKerja['id'] : null)
                                    ->first();
                                ?>
                                <tr>
                                    <td><?= $nomor++ ?></td>
                                    <td><b><?= $v['value'] ?></b></td>
                                    <td style="height: 100px;">
                                        <div class="form-floating" style="height: 50px;width:auto;">
                                            <input type="text" <?= $i == 6 ? 'disabled' : '' ?> value="<?= ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_masuk'] : '' ?>" class="form-control time" name="<?= $v['value'] ?>_mulaiMasuk" maxlength="30">
                                            <label for="mulaiMasuk">Mulai Masuk</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating" style="height: 50px;">
                                            <input type="text" <?= $i == 6 ? 'disabled' : '' ?> value="<?= ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_istirahat_mulai'] : '' ?>" class="form-control time" name="<?= $v['value'] ?>_mulaiIstirahat" maxlength="30">
                                            <label for="checkout">Mulai Istirahat</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating" style="height: 50px;">
                                            <input type="text" <?= $i == 6 ? 'disabled' : '' ?> class="form-control time" value="<?= ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_istirahat_selesai'] : '' ?>" name="<?= $v['value'] ?>_selesaiIstirahat" maxlength="30">
                                            <label for="checkout">Selesai Istirahat</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating" style="height: 50px;">
                                            <input type="text" <?= $i == 6 ? 'disabled' : '' ?> class="form-control time" value="<?= ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_pulang'] : '' ?>" name="<?= $v['value'] ?>_mulaiPulang" maxlength="30">
                                            <label for="checkout">Mulai Pulang</label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </form>
        </div>
    </div>
    </div>
</section>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    var validator = $("#formPost").validate({
        ignore: ":disabled", // Mengabaikan input yang disabled
        rules: {
            jenisJamKerja: {
                required: true
            },
            SENIN_mulaiMasuk: {
                required: true
            },
            SENIN_mulaiIstirahat: {
                required: true
            },
            SENIN_selesaiIstirahat: {
                required: true
            },
            SENIN_mulaiPulang: {
                required: true
            },
            SELASA_mulaiMasuk: {
                required: true
            },
            SELASA_mulaiIstirahat: {
                required: true
            },
            SELASA_selesaiIstirahat: {
                required: true
            },
            SELASA_mulaiPulang: {
                required: true
            },
            RABU_mulaiMasuk: {
                required: true
            },
            RABU_mulaiIstirahat: {
                required: true
            },
            RABU_selesaiIstirahat: {
                required: true
            },
            RABU_mulaiPulang: {
                required: true
            },
            KAMIS_mulaiMasuk: {
                required: true
            },
            KAMIS_mulaiIstirahat: {
                required: true
            },
            KAMIS_selesaiIstirahat: {
                required: true
            },
            KAMIS_mulaiPulang: {
                required: true
            },
            JUMAT_mulaiMasuk: {
                required: true
            },
            JUMAT_mulaiIstirahat: {
                required: true
            },
            JUMAT_selesaiIstirahat: {
                required: true
            },
            JUMAT_mulaiPulang: {
                required: true
            },
            SABTU_mulaiMasuk: {
                required: true
            },
            SABTU_mulaiIstirahat: {
                required: true
            },
            SABTU_selesaiIstirahat: {
                required: true
            },
            SABTU_mulaiPulang: {
                required: true
            },
        },
        messages: {
            jenisJamKerja: {
                required: "Jenis jam kerja wajib diisi"
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
        if ($("#formPost").valid()) {
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
                    let data = new FormData(document.querySelector("#formPost"));
                    let id = $("input[name='jamKerjaID']").val();

                    if (id) {
                        //UPDATE
                        $.ajax({
                            url: "<?= base_url("jam-kerja/update"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                        })
                                        .then(() => {
                                            location.reload();
                                        });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                }

                            },
                            onError: function(response) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        });
                    } else {
                        // INSERT
                        $.ajax({
                            url: "<?= base_url("jam-kerja/create"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                        })
                                        .then(() => {
                                            window.location.href = "<?= base_url("jam-kerja"); ?>";
                                        });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                }

                            },
                            onError: function(response) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        });
                    }


                }
            })
            stopLoading();
        } else {
            stopLoading();
        }
    });

    $(function() {
        $('.time').datetimepicker({
            format: 'HH:mm',
            icons: {
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down'
            },
        });

    });
</script>

<?= $this->endSection(); ?>