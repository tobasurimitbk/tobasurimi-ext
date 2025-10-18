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
            <div class="card-header" style="font-weight: bold;">
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
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                                Kantor Pabean
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select header_pelabuhan_bongkar" id="header_pelabuhan_bongkar" name="header_pelabuhan_bongkar" aria-label="Floating label select example">
                                        <?php if (!empty($bc23)) : ?>
                                            <option value="<?= $bc23['kode_pelabuhan_bongkar'] ?>"><?= $bc23['kode_pelabuhan_bongkar'] ?></option>
                                        <?php else : ?>
                                            <option value=""></option>
                                        <?php endif; ?>
                                    </select>
                                    <label style="z-index: 1;">Ketik Kode Pelabuhan Bongkar</label>
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
                                    <label style="z-index: 1;">Pilih Kode Kantor Pabean Bongkar</label>
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
                                    <select class="form-select header_kode_tujuan_tpb" id="header_kode_tujuan_tpb" name="header_kode_tujuan_tpb" aria-label="Floating label select example">
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
        placeholder: "Pilih Kode Kantor Pabean Bongkar",
        theme: "bootstrap-5",
    }).change(function() {
        $.ajax({
            url: `<?= base_url("bea-cukai-bc-23/api/get-pelabuhan"); ?>`,
            method: "GET",
            data: {
                header_kantor_pabean_bongkar: $('#header_kantor_pabean_bongkar').val()
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                if (res.data.status === false) {
                    Swal.fire({
                        icon: 'error',
                        title: res.data.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                }
            }
        });
    });

    $('#header_pelabuhan_bongkar').select2({
        placeholder: "Pilih Kode Pelabuhan Bongkar",
        theme: "bootstrap-5",
        tags: true, // Allow manual input even when no data
        minimumInputLength: 1,
        delay: 250,
        ajax: { // Ganti $.ajax dengan ajax
            url: `<?= base_url("bea-cukai-bc-23/api/get-pelabuhan"); ?>`,
            method: "GET",
            data: function(params) {
                return {
                    header_kantor_pabean_bongkar: params.term // Mengambil input pengguna
                };
            },
            processResults: function(data) {
                if (data.data.status === false) {
                    Swal.fire({
                        icon: 'error',
                        title: data.data.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                    return {
                        results: []
                    }; // Mengembalikan hasil kosong jika ada error
                } else {
                    let results = $.map(data.data.data, function(v) {
                        return {
                            id: v.kodePelabuhan,
                            text: v.kodePelabuhan + ' - ' + v.namaPelabuhan
                        };
                    });
                    return {
                        results: results
                    }; // Mengembalikan hasil yang diproses
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
                return {
                    results: []
                }; // Mengembalikan hasil kosong saat error
            }
        }
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
            var formData = new FormData(document.querySelector("#form-header"));
            formData.append("header_kantor_pabean_pengawas", $('#header_kantor_pabean_pengawas').val());
            formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");

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
                        location.reload();
                    }
                },
            });

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