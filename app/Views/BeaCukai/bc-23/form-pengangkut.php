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
                <form id="form-pengangkut">
                    <?= csrf_field() ?>
                    <div class="alert alert-info alert-dismissible fade show mt-3 text-white" role="alert">
                        <strong id="text-respon-ceisa"></strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                BC 1.1
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="bc_11_no_bc_11" value="<?= $bc23 != null ? $bc23['no_bc_11'] : ''  ?>" name="bc_11_no_bc_11" type="text" maxlength="6" class="form-control bc_11_no_bc_11" placeholder="">
                                    <label>Nomor BC 1.1</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input value="<?= $bc23 != null ? ($bc23['tanggal_bc_11'] != null ? date('d/m/Y', strtotime($bc23['tanggal_bc_11']))  : '') : '' ?>" autocomplete="one-time-code" name="bc_11_tanggal_bc_11" type="text" placeholder="" class="form-control bc_11_tanggal_bc_11" id="bc_11_tanggal_bc_11">
                                        <label>Tanggal BC 1.1</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $bc23 != null ? $bc23['pos_bc_11'] : '' ?>" id="bc_11_pos_bc_11" name="bc_11_pos_bc_11" type="text" maxlength="4" class="form-control bc_11_pos_bc_11" placeholder="">
                                    <label>Nomor Pos BC 1.1</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $bc23 != null ? $bc23['sub_pos_bc_11']  : '' ?>" id="bc_11_sub_pos_bc_11" name="bc_11_sub_pos_bc_11" type="text" maxlength="8" class="form-control bc_11_sub_pos_bc_11" placeholder="">
                                    <label>Nomor Sub Pos BC 1.1</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $bc23 != null ? $bc23['sub_pos_pos_bc_11'] : '' ?>" id="bc_11_sub_sub_pos_bc_11" name="bc_11_sub_sub_pos_bc_11" type="text" class="form-control bc_11_sub_sub_pos_bc_11" placeholder="">
                                    <label>Nomor Sub Sub Pos BC 1.1</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pengangkutan
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengangkutan_cara_pengangkutan" id="pengangkutan_cara_pengangkutan" name="pengangkutan_cara_pengangkutan" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodePengangkutan as $k) : ?>
                                            <option <?= $bc23Pengangkut != null ?  ($bc23Pengangkut['kode_cara_angkut'] == $k['description'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['description']) ?>">
                                                <?= $k['description'] . " - " . strtoupper($k['value']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Cara Pengangkutan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $bc23Pengangkut != null ? $bc23Pengangkut['nama_sarana_pengangkut'] : '' ?>" id="pengangkutan_nama_sarana_pengangkut" name="pengangkutan_nama_sarana_pengangkut" type="text" class="form-control pengangkutan_nama_sarana_pengangkut" placeholder="">
                                    <label>Nama Sarana Pengangkut</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $bc23Pengangkut != null ? $bc23Pengangkut['nomor_pengangkut'] : '' ?>" id="pengangkutan_nomor_pengangkut" name="pengangkutan_nomor_pengangkut" type="text" class="form-control pengangkutan_nomor_pengangkut" placeholder="">
                                    <label>Nomor Voy/Flight/No.Pol</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengangkutan_kode_bendera" id="pengangkutan_kode_bendera" name="pengangkutan_kode_bendera" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeBendera as $k) : ?>
                                            <option <?= $bc23Pengangkut != null ? ($bc23Pengangkut['kode_bendera'] == $k['code'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['code']) ?>">
                                                <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Bendera</label>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pelabuhan & Tempat Penimbunan
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $bc23 != null ?  $bc23['kode_pelabuhan_muat'] : '' ?>" id="pengangkutan_pelabuhan_muat" name="pengangkutan_pelabuhan_muat" type="text" class="form-control pengangkutan_pelabuhan_muat" placeholder="">
                                    <label>Kode Pelabuhan Muat</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $bc23 != null ? $bc23['kode_pelabuhan_transit'] : '' ?>" id="pengangkutan_pelabuhan_transit" name="pengangkutan_pelabuhan_transit" type="text" class="form-control pengangkutan_pelabuhan_transit" placeholder="">
                                    <label>Kode Pelabuhan Transit</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="pengangkutan_pelabuhan_bongkar" value="<?= $bc23 != null ? $bc23['kode_pelabuhan_bongkar'] : '' ?>" readonly name="pengangkutan_pelabuhan_bongkar" type="text" class="form-control pengangkutan_pelabuhan_bongkar" placeholder="">
                                    <label>Kode Pelabuhan Bongkar</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-2">
                                    <input id="pengangkutan_tempat_penimbunan" value="<?= $bc23 != null ? $bc23['kode_tps'] : '' ?>" name="pengangkutan_tempat_penimbunan" type="text" class="form-control pengangkutan_tempat_penimbunan" placeholder="">
                                    <label>Kode Tempat Penimbunan</label>
                                </div>
                            </div>
                            <div class="mt-0">
                                <a href="#" <?= $bc23DokumenBL == null ? 'disabled' : '' ?> class="btn btn-warning btn-block" id="btn-ambil-manifest" style="float: right;">
                                    <?= $bc23DokumenBL == null ? "Dokumen B/L atau AWB belum diisi" : "Ambil Data Manifest Dokumen B/L" ?>
                                </a>
                                <button class="btn btn-warning btn-block" type="button" disabled id="btn-loading-manifest" style="float: right;">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Loading
                                </button>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary mt-4" id="btn-simpan-perubahan" style="float: right;">
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

    $('.alert-dismissible').hide();

    $('#pengangkutan_cara_pengangkutan').select2({
        placeholder: "Pilih Cara Pengangkutan",
        theme: "bootstrap-5",
    });

    $('#pengangkutan_kode_bendera').select2({
        placeholder: "Pilih Bendera",
        theme: "bootstrap-5",
    });

    $("#bc_11_tanggal_bc_11").datepicker({
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

    var validatorPengangkut = $("#form-pengangkut").validate({
        rules: {
            bc_11_no_bc_11: {
                required: true,
            },
            bc_11_tanggal_bc_11: {
                required: true,
            },
            bc_11_pos_bc_11: {
                required: true,
            },
            bc_11_sub_pos_bc_11: {
                required: true,
            },
            bc_11_sub_sub_pos_bc_11: {
                required: true
            },
            pengangkutan_cara_pengangkutan: {
                required: true
            },
            pengangkutan_nama_sarana_pengangkut: {
                required: true
            },
            pengangkutan_nomor_pengangkut: {
                required: true
            },
            pengangkutan_kode_bendera: {
                required: true
            },
            pengangkutan_pelabuhan_muat: {
                required: true
            },
            pengangkutan_pelabuhan_transit: {
                required: true
            },
            pengangkutan_pelabuhan_bongkar: {
                required: true
            },
            pengangkutan_tempat_penimbunan: {
                required: true
            }
        },
        messages: {
            bc_11_no_bc_11: {
                required: "Nomor BC 1.1 wajib diisi",
            },
            bc_11_tanggal_bc_11: {
                required: "Tanggal BC 1.1 wajib diisi"
            },
            bc_11_pos_bc_11: {
                required: "Nomor Pos BC 1.1 wajib diisi",
            },
            bc_11_sub_pos_bc_11: {
                required: "Nomor Sub Pos BC 1.1 wajib diisi",
            },
            bc_11_sub_sub_pos_bc_11: {
                required: "Nomor Sub Pos Sub Pos BC 1.1"
            },
            pengangkutan_cara_pengangkutan: {
                required: "Pilih cara pengangkutan"
            },
            pengangkutan_nama_sarana_pengangkut: {
                required: "Nama sarana pengangkut wajib diisi"
            },
            pengangkutan_nomor_pengangkut: {
                required: "Nomor pengangkutan wajib diisi"
            },
            pengangkutan_kode_bendera: {
                required: "Pilih kode bendera"
            },
            pengangkutan_pelabuhan_muat: {
                required: "Kode pelabuhan muat wajib diisi"
            },
            pengangkutan_pelabuhan_transit: {
                required: "Kode pelabuhan transit wajib diisi"
            },
            pengangkutan_pelabuhan_bongkar: {
                required: "Kode pelabuhan bongkar wajib diisi"
            },
            pengangkutan_tempat_penimbunan: {
                required: "Kode tempat penimbunan wajib diisi"
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
    $('#btn-loading-manifest').hide();

    $('#btn-ambil-manifest').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: `<?= base_url("bea-cukai-bc-23/api/get-manifest"); ?>`,
            method: "GET",
            data: {
                penerimaan_barang_id: "<?= encrypt($lpb->id) ?>"
            },
            beforeSend: function() {
                $('#btn-loading-manifest').show();
                $('#btn-ambil-manifest').hide();
            },
            complete: function() {
                $('#btn-loading-manifest').hide();
                $('#btn-ambil-manifest').show();
            },
            dataType: "json",
            success: function(res) {
                csrf.val(res.token);
                if (res.status) {
                    if (res.data.status) {
                        var manifestData = res.data.data;
                        // alert
                        $('.alert-dismissible').show();
                        $('#text-respon-ceisa').text(manifestData.respon);
                        // append
                        $('#bc_11_no_bc_11').val(manifestData.noBc11);
                        $('#bc_11_tanggal_bc_11').val(manifestData.tglBc11);
                        $('#bc_11_pos_bc_11').val(manifestData.noPos);
                        $('#bc_11_sub_pos_bc_11').val(manifestData.noPos);
                        $('#bc_11_sub_sub_pos_bc_11').val(manifestData.noPos);
                        $('#pengangkutan_cara_pengangkutan').val(manifestData.caraPengangkutan).change();
                        $('#pengangkutan_nama_sarana_pengangkut').val(manifestData.namaSaranaPengangkut);
                        $('#pengangkutan_nomor_pengangkut').val(manifestData.noVoyage);
                        $('#pengangkutan_kode_bendera').val(manifestData.bendera).change();
                        $('#pengangkutan_pelabuhan_muat').val(manifestData.pelAsal);
                        $('#pengangkutan_pelabuhan_transit').val(manifestData.pelTransit);
                        $('#pengangkutan_tempat_penimbunan').val(manifestData.kodeGudang);

                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                }
            }
        });
    });

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
                    formData.append("penerimaan_barang_id", "<?= encrypt($lpb->id) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-23/id/pengangkut"); ?>",
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