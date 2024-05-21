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
    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 4.0 - PEMBERITAHUAN PEMASUKAN BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN KE TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-pengangkut">
                    <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                        Pengangkutan
                    </label>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="jenis_sarana_pengangkut" value="<?= $bcPengangkut != null ? ($bcPengangkut['nama_sarana_pengangkut'] != null ? $bcPengangkut['nama_sarana_pengangkut'] : '') : '' ?>" name="jenis_sarana_pengangkut" type="text" class="form-control jenis_sarana_pengangkut" placeholder="">
                                    <label>Jenis Sarana Pengangkut</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="nomor_sarana_pengangkut" value="<?= $bcPengangkut != null ? ($bcPengangkut['nomor_pengangkut'] != null ? $bcPengangkut['nomor_pengangkut'] : '') : '' ?>" name="nomor_sarana_pengangkut" type="text" class="form-control nomor_sarana_pengangkut" placeholder="">
                                    <label>Nomor Sarana Pengangkut</label>
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
    </div>

</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var validatorPengangkut = $("#form-pengangkut").validate({
        rules: {
            jenis_sarana_pengangkut: {
                required: true
            },
            nomor_sarana_pengangkut: {
                required: true
            },
        },
        messages: {
            jenis_sarana_pengangkut: {
                required: "Jenis sarana pengangkut wajib diisi"
            },
            nomor_sarana_pengangkut: {
                required: "Nomor sarana pengangkut wajib diisi"
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

    $('#btn-loading').hide();

    $('#btn-simpan-perubahan').click(function() {
        if ($('#form-pengangkut').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Pengangkut ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-pengangkut"));
                    formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-40/id/pengangkut"); ?>",
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