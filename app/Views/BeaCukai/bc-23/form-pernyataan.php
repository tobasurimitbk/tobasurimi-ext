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
            BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
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
                <div class="row">
                    <div class="col-sm mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Tempat & Tanggal
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bc23) ? ($bc23['kota_ttd'] == null ? $ceisaSetting['tempat'] : $bc23['kota_ttd']) : $ceisaSetting['tempat'] ?>" id="pernyatan_tempat" name="pernyatan_tempat" type="text" class="form-control pernyatan_tempat" placeholder="">
                                <label>Tempat</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bc23) ? ($bc23['tanggal_ttd'] == null ? date('d/m/Y') : date('d/m/Y', strtotime($bc23['tanggal_ttd']))) : date('d/m/Y', strtotime(date('Y-m-d'))) ?>" id="pernyataan_tanggal" name="pernyataan_tanggal" type="text" class="form-control pernyataan_tanggal" placeholder="">
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
                                <input value="<?= !empty($bc23) ?  ($bc23['nama_ttd'] == null ? $ceisaSetting['nama'] : $bc23['nama_ttd']) : $ceisaSetting['nama']  ?>" id="pernyatan_nama" name="pernyatan_nama" type="text" class="form-control pernyatan_nama" placeholder="">
                                <label>Nama</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bc23) ?  ($bc23['jabatan_pengusaha_ttd'] == null ? $ceisaSetting['jabatan'] : $bc23['jabatan_pengusaha_ttd']) : $ceisaSetting['jabatan']  ?>" id="pernyatan_jabatan" name="pernyatan_jabatan" type="text" class="form-control pernyatan_jabatan" placeholder="">
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

    $("#pernyataan_tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    var validatorPernyataan = $("#form-pernyataan").validate({
        rules: {
            pernyatan_tempat: {
                required: true
            },
            pernyataan_tanggal: {
                required: true
            },
            pernyatan_nama: {
                required: true
            },
            pernyatan_jabatan: {
                required: true
            },
        },
        messages: {
            pernyatan_tempat: {
                required: "Tempat wajib diisi"
            },
            pernyataan_tanggal: {
                required: "Tanggal wajib diisi"
            },
            pernyatan_nama: {
                required: "Nama wajib diisi"
            },
            pernyatan_jabatan: {
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
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-pernyataan"));
                    formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-23/id/pernyataan"); ?>",
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