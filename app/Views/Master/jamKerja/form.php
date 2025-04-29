<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($jamKerja) ? "Update" : "Tambah"; ?> Jam Kerja</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jam-kerja"); ?>">
                Kembali
            </a>
            <?php if (empty($jamKerja)) : ?>
                <?php if (can('Master Data', 'Jam Kerja', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Master Data', 'Jam Kerja', 'd')) : ?>
                    <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                        Hapus
                    </button>
                <?php endif; ?>
                <?php if (can('Master Data', 'Jam Kerja', 'u')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <label class="form-label font-weight-bold lable-title mt-2">
                Informasi Jam Kerja
            </label>
            <form action="#" method="post" id="formPost" class="mt-4 detail-form">
                <?= csrf_field() ?>
                <input type="hidden" value="<?= ($jamKerja != null) ? encrypt($jamKerja['id']) : null ?>" id="jamKerjaID" class="jamKerjaID" name="jamKerjaID">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisiId" id="divisiId" name="divisiId">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= $jamKerja != null ? ($jamKerja['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" name="jenis" required value="<?= (!empty($jamKerja)) ? $jamKerja['jenis'] : '' ?> " placeholder="Cth : Jam Kerja Satpam">
                            <label for="floatingInput">Nama (Cth : Jam Kerja Satpam)</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select shift" id="shift" name="shift">
                                <option value=""></option>
                                <?php foreach ($shift as $s): ?>
                                    <option <?= $jamKerja != null ? ($jamKerja['shift'] == $s ? 'selected' : '') : '' ?> value="<?= $s ?>"><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Shift (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" name="jamTerlambat" required value="<?= (!empty($jamKerja)) ? $jamKerja['jam_terlambat'] : '09:00' ?> " placeholder="Batas Jam Keterlambatan">
                            <label for="floatingInput">Batas Jam Keterlambatan Absen Masuk</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form">
                            <label class="mt-2 text-dark">
                                <b>Jam Kerja Default,</b> (Jika Aktif Maka Akan Menjadi Jam Kerja Default di Departemen yang Sudah Dipilih)
                            </label>
                            <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;">
                                <div class="form-check form-switch form-switch-lg">
                                    <input <?= isset($jamKerjaDefault) ? ($jamKerjaDefault != null ? 'checked' : '')  : '' ?> class="form-check-input" type="checkbox" value="1" name="jamKerjaDefault" id="jamKerjaDefault">
                                    <label class="form-check-label"></label>
                                </div>
                            </div>
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
                                <!-- <th colspan="2">Jam Istirahat</th> -->
                                <th colspan="1">Jam Pulang</th>
                            </tr>
                        </thead>
                        <tbody class="text-center text-bold">
                            <?php $nomor = 1; ?>
                            <?php foreach ($hari as $i => $v) : ?>
                                <?php
                                $jamKerjaDetailModel = new \App\Models\JamKerjaDetailModel();
                                $jamKerjaDetail = $jamKerjaDetailModel->where('hari', $v['value'])
                                    ->where('jam_kerja_id', ($jamKerja != null) ? ($jamKerja['id']) : null)
                                    ->first();
                                ?>
                                <tr>
                                    <td><?= $nomor++ ?></td>
                                    <td><b><?= $v['value'] ?></b></td>
                                    <td style="height: 70px;">
                                        <div class="form-floating" style="height: 50px;width:auto;">
                                            <input type="text" value="<?= ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_masuk'] : '' ?>" class="form-control time" name="<?= $v['value'] ?>_mulaiMasuk" maxlength="30">
                                            <label for="mulaiMasuk">Mulai Masuk</label>
                                        </div>
                                    </td>
                                    <!-- <td>
                                        <div class="form-floating" style="height: 50px;">
                                            <input type="text" value="<?= ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_istirahat_mulai'] : '' ?>" class="form-control time" name="<?= $v['value'] ?>_mulaiIstirahat" maxlength="30">
                                            <label for="checkout">Mulai Istirahat</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating" style="height: 50px;">
                                            <input type="text" class="form-control time" value="<?= ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_istirahat_selesai'] : '' ?>" name="<?= $v['value'] ?>_selesaiIstirahat" maxlength="30">
                                            <label for="checkout">Selesai Istirahat</label>
                                        </div>
                                    </td> -->
                                    <td>
                                        <div class="form-floating" style="height: 50px;">
                                            <input type="text" class="form-control time" value="<?= ($jamKerjaDetail != null) ? $jamKerjaDetail['jam_pulang'] : '' ?>" name="<?= $v['value'] ?>_mulaiPulang" maxlength="30">
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
            divisiId: {
                required: true
            },
            jenis: {
                required: true
            },
            jamTerlambat: {
                required: true
            },
            SENIN_mulaiMasuk: {
                required: true
            },
            SENIN_mulaiPulang: {
                required: true
            },
            SELASA_mulaiMasuk: {
                required: true
            },
            SELASA_mulaiPulang: {
                required: true
            },
            RABU_mulaiMasuk: {
                required: true
            },
            RABU_mulaiPulang: {
                required: true
            },
            KAMIS_mulaiMasuk: {
                required: true
            },
            KAMIS_mulaiPulang: {
                required: true
            },
            JUMAT_mulaiMasuk: {
                required: true
            },
            JUMAT_mulaiPulang: {
                required: true
            },
            SABTU_mulaiMasuk: {
                required: true
            },
            SABTU_mulaiPulang: {
                required: true
            },
        },
        messages: {
            divisiId: {
                required: "Pilih Departemen"
            },
            jenis: {
                required: "Jenis Penilaian Wajib Diisi"
            },
            jamTerlambat: {
                required: "Jam Terlambat Wajib Diisi"
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

    $('#divisiId').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#shift').select2({
        placeholder: "Pilih Shift (Opsional)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $("#divisiId,#shift")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    $("input[name='jamTerlambat']").datetimepicker({
        format: 'HH:mm',
        icons: {
            up: 'fas fa-chevron-up',
            down: 'fas fa-chevron-down'
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
                cancelButtonText: 'Kembali',
            }).then((result) => {

                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let data = new FormData(document.querySelector("#formPost"));
                    let id = $("input[name='jamKerjaID']").val();

                    if (id) {
                        //UPDATE
                        $.ajax({
                            url: "<?= base_url("jam-kerja/update"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading()
                            },
                            complete: function() {
                                stopLoading();
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
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                }

                            },

                        });
                    } else {
                        // INSERT
                        $.ajax({
                            url: "<?= base_url("jam-kerja/create"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading()
                            },
                            complete: function() {
                                stopLoading();
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
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                }

                            },

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

    function deleteAction() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Jam Kerja ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                var id = $('#jamKerjaID').val();
                var formData = new FormData();
                formData.append("jamKerjaID", id);
                $.ajax({
                    url: `<?= base_url("jam-kerja/delete"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: res.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url("jam-kerja"); ?>";
                                });
                        }
                    }
                })
            }
        })
    }
</script>

<?= $this->endSection(); ?>