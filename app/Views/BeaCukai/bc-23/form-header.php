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
                <form id="form-header">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Pengajuan
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input disabled id="header_no_pengajuan" value="<?= $noAju ?>" name="header_no_pengajuan" type="text" readonly class="header_no_pengajuan form-control" placeholder="">
                                    <label>Nomor Pengajuan</label>
                                </div>
                            </div>
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Tanggal Penerimaan Barang
                            </label>
                            <div class="mt-1">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input id="tanggal_penerimaan_barang" value="<?= date('d/m/Y', strtotime($lpb->tanggal)) ?>" name="tanggal_penerimaan_barang" type="text" class="tanggal_penerimaan_barang form-control" placeholder="">
                                        <label>Tanggal Penerimaan Barang</label>
                                    </div>
                                    <div class="input-group-prepend group-prepend-password align-items-center">
                                        <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Kantor Pabean
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="header_pelabuhan_bongkar" value="<?= !empty($bc23) ? $bc23['kode_pelabuhan_bongkar'] : '' ?>" name="header_pelabuhan_bongkar" type="text" class="header_pelabuhan_bongkar form-control" placeholder="">
                                    <label>Kode Pelabuhan Bongkar</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select header_kantor_pabean_bongkar" id="header_kantor_pabean_bongkar" name="header_kantor_pabean_bongkar" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option <?= !empty($bc23) ? (encrypt($bc23['kode_kantor_bongkar']) == encrypt($k['kode']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['kode']) ?>">
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Pilih Kode Kantor Bongkar</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select header_kantor_pabean_pengawas" disabled id="header_kantor_pabean_pengawas" name="header_kantor_pabean_pengawas" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option <?= !empty($selectedKantor) ? (encrypt($selectedKantor['value']) == encrypt($k['kode']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['kode']) ?>">
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Pilih Kantor Pabean Pengawas</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Keterangan Lain
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= !empty($bc23) ? ($bc23['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> class="form-select header_kode_tujuan_tpb" id="header_kode_tujuan_tpb" name="header_kode_tujuan_tpb" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeTujuanTpb as $k) : ?>
                                            <option <?= !empty($bc23) ? (encrypt($bc23['kode_tujuan_tpb']) == encrypt($k['description']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['description']) ?>">
                                                <?= strtoupper($k['description']) . " - " . strtoupper($k['value']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Pilih Kode Tujuan TPB</label>
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

    // init loading
    $('#btn-loading').hide();

    $('#header_kantor_pabean_bongkar').select2({
        placeholder: "Pilih Kode Kantor Bongkar",
        theme: "bootstrap-5",
    });

    $('#header_kode_tujuan_tpb').select2({
        placeholder: "Pilih Kode Tujuan TPB",
        theme: "bootstrap-5",
    });

    $("#tanggal_penerimaan_barang").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    var validatorHeader = $("#form-header").validate({
        rules: {
            header_pelabuhan_bongkar: {
                required: true
            },
            header_kantor_pabean_bongkar: {
                required: true
            },
            header_kode_tujuan_tpb: {
                required: true
            },
        },
        messages: {
            header_pelabuhan_bongkar: {
                required: "Kode pelabuhan bongkar wajib diisi"
            },
            header_kantor_pabean_bongkar: {
                required: "Pilih kantor bongkar"
            },
            header_kode_tujuan_tpb: {
                required: "Pilih tujuan TPB"
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
        if ($('#form-header').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Header ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-header"));
                    formData.append("header_kantor_pabean_pengawas", $('#header_kantor_pabean_pengawas').val());
                    formData.append("penerimaan_barang_id", "<?= encrypt($lpb->id) ?>");

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-23/id/header"); ?>",
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


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
</script>


<?= $this->endSection(); ?>