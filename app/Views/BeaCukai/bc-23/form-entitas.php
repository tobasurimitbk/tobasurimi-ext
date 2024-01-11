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
                BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-entitas">
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Importir/Pengusaha TPB
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_npwp_importir" value="<?= $bc23Entitas == null ? $npwpDefault['value'] : $bc23Entitas['nomor_identitas'] ?>" name="entitas_npwp_importir" type="text" class="form-control entitas_npwp_importir" placeholder="">
                                    <label>NPWP</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_nama_importir" value="<?= $bc23Entitas == null ? $namaImportirDefault['value'] : $bc23Entitas['nama_entitas'] ?>" name="entitas_nama_importir" type="text" class="form-control entitas_nama_importir" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="entitas_alamat_importir" id="entitas_alamat_importir" class="form-control entitas_alamat_importir" style="height: 100px;"><?= "\n" . ($bc23Entitas == null) ? $alamatImportirDefault['value'] : $bc23Entitas['alamat_entitas'] ?></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select entitas_nomor_ijin_tpb" id="entitas_nomor_ijin_tpb" name="entitas_nomor_ijin_tpb" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($nomorIjinTPB as $k) : ?>
                                            <option data-tanggal_skep_tpb="<?= date('d/m/Y', strtotime($k['tanggal_skep_tpb'])) ?>" <?= !empty($bc23Entitas) ? ($bc23Entitas['nomor_ijin_entitas'] == $k['no_izin_tpb'] ? 'selected' : '') : '' ?> value="<?= $k['no_izin_tpb'] ?>">
                                                <?= $k['no_izin_tpb'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Nomor Izin TPB</label>
                                </div>
                            </div>
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly value="<?= $bc23Entitas == null ? '' : date('d/m/Y', strtotime($bc23Entitas['tanggal_ijin_entitas']))  ?>" autocomplete="one-time-code" name="entitas_tanggal_skep_tpb" type="text" placeholder="" class="form-control entitas_tanggal_skep_tpb" id="entitas_tanggal_skep_tpb">
                                    <label>Tanggal Skep TPB</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input id="entitas_nib" value="<?= $bc23Entitas == null ? $nibDefault['value'] : $bc23Entitas['nib_entitas'] ?>" name="entitas_nib" type="text" class="form-control entitas_nib" placeholder="">
                                <label>NIB</label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pemasok
                            </label>
                            <div class="mt-1">
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <input id="entitas_nama_pemasok" value="<?= $bc23Entitas != null ? $bc23Entitas['nama_pemasok'] : '' ?>" name="entitas_nama_pemasok" type="text" class="form-control entitas_nama_pemasok" placeholder="">
                                        <label>Nama</label>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <div class="form-floating mb-3">
                                        <textarea name="entitas_alamat_pemasok" id="entitas_alamat_pemasok" class="form-control entitas_alamat_pemasok" style="height: 100px;"><?= ($bc23Entitas != null) ? $bc23Entitas['alamat_pemasok'] : '' ?></textarea>
                                        <label>Alamat</label>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select entitas_negara" id="entitas_negara" name="entitas_negara" aria-label="Floating label select example">
                                            <option value=""></option>
                                            <?php foreach ($kodeNegaraAsal as $k) : ?>
                                                <option <?= $bc23Entitas != null ? ($bc23Entitas['kode_negara_pemasok'] == $k['code'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['code']) ?>">
                                                    <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label style="z-index: 1;">Negara</label>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-sm-4">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pemilik Barang
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_npwp_pemilik_barang" value="<?= $bc23Entitas != null ? $bc23Entitas['npwp_pemilik_barang'] : $npwpDefault['value'] ?>" name="entitas_npwp_pemilik_barang" type="text" class="form-control entitas_npwp_pemilik_barang" placeholder="">
                                    <label>NPWP</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_nama_pemilik_barang" value="<?= $bc23Entitas != null ? $bc23Entitas['nama_pemilik_barang'] : $namaImportirDefault['value'] ?>" name="entitas_nama_pemilik_barang" type="text" class="form-control entitas_nama_pemilik_barang" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="entitas_alamat_pemilik_barang" id="entitas_alamat_pemilik_barang" class="form-control entitas_alamat_pemilik_barang" style="height: 100px;"><?= $bc23Entitas != null ? $bc23Entitas['alamat_pemilik_barang'] : $alamatImportirDefault['value'] ?></textarea>
                                    <label>Alamat</label>
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

    $('#entitas_negara').select2({
        placeholder: "Pilih Negara",
        theme: "bootstrap-5",
    });

    $('#entitas_nomor_ijin_tpb').select2({
        placeholder: "Pilih No Ijin TPB",
        theme: "bootstrap-5",
    }).change(function() {
        var selected = $(this).find('option:selected');
        $('#entitas_tanggal_skep_tpb').val(selected.data('tanggal_skep_tpb'));
    });

    $("#entitas_tanggal_skep_tpb").datepicker({
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

    var validatorEntitas = $("#form-entitas").validate({
        rules: {
            entitas_npwp_importir: {
                required: true
            },
            entitas_nama_importir: {
                required: true
            },
            entitas_alamat_importir: {
                required: true
            },
            entitas_nomor_ijin_tpb: {
                required: true
            },
            entitas_tanggal_skep_tpb: {
                required: true
            },
            entitas_nib: {
                required: true
            },
            entitas_nama_pemasok: {
                required: true
            },
            entitas_alamat_pemasok: {
                required: true
            },
            entitas_negara: {
                required: true
            },
            entitas_npwp_pemilik_barang: {
                required: true
            },
            entitas_nama_pemilik_barang: {
                required: true
            },
            entitas_alamat_pemilik_barang: {
                required: true
            }
        },
        messages: {
            entitas_npwp_importir: {
                required: "Npwp importir wajib diisi"
            },
            entitas_nama_importir: {
                required: "Nama importir wajib diisi"
            },
            entitas_alamat_importir: {
                required: "Alamat wajib diisi"
            },
            entitas_nomor_ijin_tpb: {
                required: "Nomor ijin TPB wajib diisi"
            },
            entitas_tanggal_skep_tpb: {
                required: "Tanggal skep TPB wajib diisi"
            },
            entitas_nib: {
                required: "NIB wajib diisi"
            },
            entitas_nama_pemasok: {
                required: "Nama pemasok wajib diisi"
            },
            entitas_alamat_pemasok: {
                required: "Alamat pemasok wajib diisi"
            },
            entitas_negara: {
                required: "Negara pemasok wajib diisi"
            },
            entitas_npwp_pemilik_barang: {
                required: "Npwp pemilik barang wajib diisi"
            },
            entitas_nama_pemilik_barang: {
                required: "Nama pemilik barang wajib diisi"
            },
            entitas_alamat_pemilik_barang: {
                required: "Alamat pemilik barang wajib diisi"
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

    $('#btn-loading').hide();

    $('#btn-simpan-perubahan').click(function() {
        if ($('#form-entitas').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Entitas ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-entitas"));
                    formData.append("penerimaan_barang_id", "<?= encrypt($lpb->id) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-23/id/entitas"); ?>",
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