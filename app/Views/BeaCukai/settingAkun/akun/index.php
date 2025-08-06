<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Integrasi CEISA 4.0</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("setting-akun-bc"); ?>">
                Kembali
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 mt-1">
            <div class="card">
                <form class="create-form" method="post">
                    <div class="card-header" style="font-weight: bold;">
                        AKUN CEISA PERUSAHAAN
                    </div>
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="text-center">
                            <img src="<?= base_url('assets/img/bc.png') ?>" width="130" alt="">
                        </div>

                        <?php if (empty($akunCeisa)) : ?>
                            <div class="alert alert-danger mt-3 mb-3" role="alert">
                                AKUN CEISA BEA CUKAI BELUM TERHUBUNG DENGAN APP INI
                            </div>
                        <?php else : ?>
                            <?php if ($akunCeisa['status_integrasi']) : ?>
                                <div class="alert alert-success mt-3 mb-3" role="alert">
                                    AKUN CEISA BEA CUKAI SUDAH TERHUBUNG DENGAN APP INI
                                </div>
                            <?php else : ?>
                                <div class="alert alert-danger mt-3 mb-3" role="alert">
                                    AKUN CEISA BEA CUKAI BELUM TERHUBUNG DENGAN APP INI
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="row ">
                            <div class="col-sm-3">
                                <div class="form-floating mb-3 mt-1" style="height: 50px;">
                                    <select class="form-select kode_kantor_pabean" id="kode_kantor_pabean" name="kode_kantor_pabean" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option <?= !empty($akunCeisa) ? ($akunCeisa['kode_kantor_pabean'] == $k['kode'] ? 'selected' : '') : '' ?> value="<?= $k['kode'] ?>">
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kantor Pabean</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="npwp_perusahaan" value="<?= !empty($akunCeisa) ? $akunCeisa['npwp_perusahaan'] : '' ?>" type="number" class="form-control npwp_perusahaan" name="npwp_perusahaan" placeholder="">
                                        <label>NPWP Perusahaan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mt-1">
                                    <div class="form-floating">
                                        <input id="kode_unik" value="<?= !empty($akunCeisa) ? $akunCeisa['kode_unik'] : '' ?>" type="text" class="form-control kode_unik" name="kode_unik" placeholder="">
                                        <label>Kode Unik Perusahaan (Untuk No Aju)</label>
                                    </div>

                                </div>
                                <label class="mb-3">
                                    <i>
                                        000040-<span class="text-danger">017189</span>-20250806-006655
                                    </i>
                                </label>
                            </div>
                            <div class="col-sm-3">
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="username" value="" type="text" class="form-control username" name="username" placeholder="">
                                        <label>Username</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="password" value="" type="text" class="form-control password" name="password" placeholder="">
                                        <label>Password</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header" style="font-weight: bold; color:black; margin-bottom:-20px;">
                        PERNYATAAN PEMBUATAN DOKUMEN
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4 mt-1">
                                <div class="form-floating mb-3">
                                    <input id="tempat" value="<?= !empty($akunCeisa) ? $akunCeisa['tempat'] : '' ?>" type="text" class="form-control tempat" name="tempat" placeholder="">
                                    <label>Tempat</label>
                                </div>
                            </div>
                            <div class="col-sm-4 mt-1">
                                <div class="form-floating mb-3">
                                    <input id="nama" value="<?= !empty($akunCeisa) ? $akunCeisa['nama'] : '' ?>" type="text" class="form-control nama" name="nama" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="col-sm-4 mt-1">
                                <div class="form-floating mb-3">
                                    <input id="jabatan" value="<?= !empty($akunCeisa) ? $akunCeisa['jabatan'] : '' ?>" type="text" class="form-control jabatan" name="jabatan" placeholder="">
                                    <label>Jabatan</label>
                                </div>
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
    const csrf = $(`[name="${csrfToken}"]`);

    $('#kode_kantor_pabean').select2({
        placeholder: "Pilih Kode Kantor Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#npwp_perusahaan').mask('0000000000000000');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            kode_kantor_pabean: {
                required: true
            },
            username: {
                required: true
            },
            password: {
                required: true
            },
            tempat: {
                required: true
            },
            nama: {
                required: true
            },
            jabatan: {
                required: true
            },
            kode_unik: {
                required: true,
                digits: true,
                minlength: 6,
                maxlength: 6
            }
        },
        messages: {
            kode_kantor_pabean: {
                required: "Pilih kode kantor pabean"
            },
            username: {
                required: "Username wajib diisi"
            },
            password: {
                required: "Password wajib diisi"
            },
            tempat: {
                required: "Tempat wajib diisi"
            },
            nama: {
                required: "Nama wajib diisi"
            },
            jabatan: {
                required: "Jabatan wajib diisi"
            },
            kode_unik: {
                required: "Kode unik wajib diisi (untuk no aju)",
                digits: "Kode unik harus berupa angka",
                minlength: "Kode unik harus 6 digit",
                maxlength: "Kode unik harus 6 digit"
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

    $('.btn-submit-parent').click(function() {
        if ($('.create-form').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = new FormData(document.querySelector(".create-form"));
                    $.ajax({
                        url: "<?= base_url("setting-akun-bc/akun/create-update"); ?>",
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading()
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status == false) {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then((result) => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    location.reload();
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