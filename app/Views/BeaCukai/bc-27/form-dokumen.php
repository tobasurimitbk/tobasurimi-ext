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
        <?= csrf_field() ?>
        <form id="form-dokumen">
            <input type="hidden" name="id" id="id" class="id" value="<?= encrypt($bc27['id']) ?>">
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <div class="alert alert-secondary alert-dismissible fade show mt-3 text-black" role="alert">
                    Urutan penginputan dokumen adalah <br>
                    <b>Seri Dokumen 1 : Invoice (380)</b> <br>
                    <b>Seri Dokumen Selanjutnya : Dokumen Pelengkap</b> <br>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select dokumen_jenis_dokumen" id="dokumen_jenis_dokumen" name="dokumen_jenis_dokumen" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeDokumen as $k) : ?>
                                    <option value="<?= $k['description'] ?>">
                                        <?= strtoupper($k['description']) . " - " . strtoupper($k['value']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Jenis Dokumen</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3">
                            <input id="dokumen_nomor_dokumen" name="dokumen_nomor_dokumen" type="text" class="form-control dokumen_nomor_dokumen" placeholder="">
                            <label>Nomor Dokumen</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" name="dokumen_tanggal" type="text" placeholder="" class="form-control dokumen_tanggal" id="dokumen_tanggal">
                                <label>Tanggal Dokumen</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="row" style="float: right; margin-bottom:5px;">
                            <div class="col-sm" style="margin-right: -20px;">
                                <button type="button" class="btn btn-add btn-block float-right btn-submit-dokumen" style="float: right;">
                                    <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-dokumen" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">Seri Dokumen</th>
                                <th style="text-align: center;">Jenis Dokumen</th>
                                <th style="text-align: center;">Nomor Dokumen</th>
                                <th style="text-align: center;">Tanggal</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($dokumen) == 0) : ?>
                                <tr style="color: white; text-align:center;">
                                    <td colspan="5">Tidak ada dokumen</td>
                                </tr>
                            <?php else : ?>
                                <?php $length = count($dokumen); ?>
                                <?php foreach ($dokumen as $i => $d) : ?>
                                    <tr style="color: white; text-align:center;">
                                        <td><?= $d['seriDokumen'] ?></td>
                                        <td><?= $d['kodeDokumen'] ?></td>
                                        <td><?= $d['nomorDokumen'] ?></td>
                                        <td><?= $d['tanggalDokumen'] ?></td>
                                        <td>
                                            <?php if ($i == $length - 1) : ?>
                                                <button type="button" class="btn btn-danger" onclick="removeDokumen(<?= $i ?>)"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                                            <?php else : ?>

                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

    </div>

</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('#dokumen_jenis_dokumen').select2({
        placeholder: "Pilih Jenis Dokumen",
        theme: "bootstrap-5",
    });

    $("#dokumen_tanggal").datepicker({
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

    // TABEL LIST DOKUMEN
    var validatorDokumen = $("#form-dokumen").validate({
        rules: {
            dokumen_jenis_dokumen: {
                required: true
            },
            dokumen_nomor_dokumen: {
                required: true
            },
            dokumen_tanggal: {
                required: true
            },
        },
        messages: {
            dokumen_jenis_dokumen: {
                required: "Pilih jenis dokumen"
            },
            dokumen_nomor_dokumen: {
                required: "Nomor dokumen wajib diisi"
            },
            dokumen_tanggal: {
                required: "Tanggal dokumen wajib diisi"
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

    $('.btn-submit-dokumen').click(function() {
        if ($('#form-dokumen').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Dokumen ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-dokumen"));
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-27/id/dokumen"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
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
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    confirmButtonText: 'Ok'
                                })
                            }
                        },
                    });
                }
            })
        }
    });

    function removeDokumen(index_delete) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai-bc-27/id/dokumen/delete"); ?>",
                    data: {
                        id: "<?= encrypt($bc27['id']) ?>",
                        index_delete: index_delete
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    },
                });
            }
        })

    }
</script>


<?= $this->endSection(); ?>