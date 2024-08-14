<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include_once('header.php') ?>
    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 3.0 - PEMBERITAHUAN EKSPOR BARANG
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-kesiapan-barang">
                    <input type="hidden" name="id" id="id" class="id" value="<?= encrypt($bc30['id']) ?>">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select jenisBarang" id="jenisBarang" name="jenisBarang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1" <?= empty($payload->kesiapanBarang[0]->kodeJenisBarang) ? " " : ($payload->kesiapanBarang[0]->kodeJenisBarang == 1 ? "selected" : "")  ?>>1 - BARANG ESKPOR GABUNGAN</option>
                                        <option value="2" <?= empty($payload->kesiapanBarang[0]->kodeJenisBarang) ? " " : ($payload->kesiapanBarang[0]->kodeJenisBarang == 2 ? "selected" : "")  ?>>2 - BAHAN/BARANG ASAL IMPOR FASILITAS</option>
                                    </select>
                                    <label style="z-index: 1;">Jenis Ekspor</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select jenisGudang" id="jenisGudang" name="jenisGudang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1" <?= empty($payload->kesiapanBarang[0]->kodeJenisGudang) ? " " : ($payload->kesiapanBarang[0]->kodeJenisGudang == 1 ? "selected" : "")  ?>>1 - GUDANG VEEM</option>
                                        <option value="2" <?= empty($payload->kesiapanBarang[0]->kodeJenisGudang) ? " " : ($payload->kesiapanBarang[0]->kodeJenisGudang == 2 ? "selected" : "")  ?>>2 - GUDANG PABRIK</option>
                                        <option value="3" <?= empty($payload->kesiapanBarang[0]->kodeJenisGudang) ? " " : ($payload->kesiapanBarang[0]->kodeJenisGudang == 3 ? "selected" : "")  ?>>3 - GUDANG KONSOLIDASI</option>
                                        <option value="4" <?= empty($payload->kesiapanBarang[0]->kodeJenisGudang) ? " " : ($payload->kesiapanBarang[0]->kodeJenisGudang == 4 ? "selected" : "")  ?>>4 - LAINNYA</option>
                                    </select>
                                    <label style="z-index: 1;">Jenis Gudang </label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="namaPic" value="<?= $payload->kesiapanBarang[0]->namaPic != "" ? $payload->kesiapanBarang[0]->namaPic : ""  ?>" name="namaPic" type="text" class=" form-control" placeholder="">
                                    <label>Nama PIC</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="alamat" value="<?= $payload->kesiapanBarang[0]->alamat != "" ? $payload->kesiapanBarang[0]->alamat : ""  ?>" name="alamat" type="text" class=" form-control" placeholder="">
                                    <label>Alamat</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="nomorTelponPic" value="<?= $payload->kesiapanBarang[0]->nomorTelpPic != "" ? $payload->kesiapanBarang[0]->nomorTelpPic : "" ?>" name="nomorTelponPic" type="text" class=" form-control" placeholder="">
                                    <label>Nomor Telpon Pic</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="jumlahContainer20" value="<?= $payload->kesiapanBarang[0]->jumlahContainer20 != "" ? $payload->kesiapanBarang[0]->jumlahContainer20 : ""  ?>" name="jumlahContainer20" type="text" class=" form-control" placeholder="">
                                    <label>Jumlah Container 20</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="jumlahContainer40" value="<?= $payload->kesiapanBarang[0]->jumlahContainer40 != "" ? $payload->kesiapanBarang[0]->jumlahContainer40 : ""  ?>" name="jumlahContainer40" type="text" class=" form-control" placeholder="">
                                    <label>Jumlah Container 40</label>
                                </div>
                            </div>

                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input id="lokasiSiapPeriksa" value="<?= $payload->kesiapanBarang[0]->lokasiSiapPeriksa != "" ? $payload->kesiapanBarang[0]->lokasiSiapPeriksa : ""  ?>" name="lokasiSiapPeriksa" type="text" class=" form-control" placeholder="">
                                    <label>Lokasi Siap Periksa</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select caraStuffing" id="caraStuffing" name="caraStuffing" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="4" <?= empty($payload->kesiapanBarang[0]->kodeCaraStuffing) ? " " : ($payload->kesiapanBarang[0]->kodeCaraStuffing == 4 ? "selected" : "")  ?>>4 - EMPTY</option>
                                        <option value="7" <?= empty($payload->kesiapanBarang[0]->kodeCaraStuffing) ? " " : ($payload->kesiapanBarang[0]->kodeCaraStuffing == 7 ? "selected" : "")  ?>>7 - LCL</option>
                                        <option value="8" <?= empty($payload->kesiapanBarang[0]->kodeCaraStuffing) ? " " : ($payload->kesiapanBarang[0]->kodeCaraStuffing == 8 ? "selected" : "")  ?>>8 - FCL</option>

                                    </select>
                                    <label style="z-index: 1;">Cara Stuffing</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select jenisPartOf" id="jenisPartOf" name="jenisPartOf" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1" <?= empty($payload->kesiapanBarang[0]->kodeJenisPartOf) ? " " : ($payload->kesiapanBarang[0]->kodeJenisPartOf == 1 ? "selected" : "")  ?>>1 - GABUNGAN KEMUDAHAN EKSPOR</option>
                                        <option value="2" <?= empty($payload->kesiapanBarang[0]->kodeJenisPartOf) ? " " : ($payload->kesiapanBarang[0]->kodeJenisPartOf == 2 ? "selected" : "")  ?>>2 - GABUNGAN KE/NON KE</option>
                                        <option value="NULL" <?= empty($payload->kesiapanBarang[0]->kodeJenisPartOf) ? " " : ($payload->kesiapanBarang[0]->kodeJenisPartOf == "NULL" ? "selected" : "")  ?>>TIDAK ADA</option>

                                    </select>
                                    <label style="z-index: 1;">Jenis Part Of</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" class="form-control input-picker tanggalPkb" id="tanggalPkb" name="tanggalPkb" placeholder="Tanggal Dibuat" value="<?= $payload->kesiapanBarang[0]->tanggalPkb != "" ? date('d/m/Y', strtotime($payload->kesiapanBarang[0]->tanggalPkb)) : ""   ?>">
                                            <label for="floatingInput">Tanggal PKB</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <div class="input-group input-group-password">
                                                <div class="form-floating mb-3" style="height: 50px;">
                                                    <input autocomplete="one-time-code" class="form-control input-picker tanggalSiapPeriksa" id="tanggalSiapPeriksa" name="tanggalSiapPeriksa" placeholder="Tanggal Dibuat" value="<?= $tanggalSiapPeriksa != ""  ? $tanggalSiapPeriksa : ""; ?>">
                                                    <label for="floatingInput">Tanggal Siap Periksa</label>
                                                </div>
                                                <div class="input-group-prepend group-prepend-password align-items-center">
                                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input id="waktuSiapPeriksa" value="<?= $waktuPeriksa != ""  ? $waktuPeriksa : ""; ?>" name="waktuSiapPeriksa" type="time" class=" form-control" placeholder="">
                                            <input type="hidden" id="timezoneOffset" name="timezoneOffset">
                                            <label>Waktu Siap Periksa</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary" id="btn-simpan-perubahan" style="float: right;">
                        Simpan Perubahan
                    </a>
            </div>
        </div>
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
    $('#kodeKantor').select2({
        placeholder: "Pilih Kode Kantor Asal",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        $.ajax({
            url: `<?= base_url("bea-cukai-bc-23/api/get-pelabuhan"); ?>`,
            method: "GET",
            data: {
                header_kantor_pabean_bongkar: $('#kodeKantor').val()
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
                } else {
                    $('#kodePelabuhan').empty();
                    $.each(res.data.data, function(i, v) {
                        var option = $('<option>').val(v.kodePelabuhan).text(v.kodePelabuhan);
                        $('#kodePelabuhan').append(option);
                    });
                }
            }
        });
    });

    $('#jenisEkspor').select2({
        placeholder: "Pilih Kode Jenis Ekspor",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#jenisGudang').select2({
        placeholder: "Pilih Kode Jenis Gudang",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#caraStuffing').select2({
        placeholder: "Pilih Kode Cara Stuffing",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#jenisPartOf').select2({
        placeholder: "Pilih Jenis Part Of",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#jenisPartOf').select2({
        placeholder: "Pilih Jenis Part Of",
        theme: "bootstrap-5",
        allowClear: true
    });
    $("#tanggalPkb").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
    $("#tanggalSiapPeriksa").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
    $("#waktuSiapPeriksa").change(function() {
        const timezoneOffset = new Date().getTimezoneOffset();
        $("#timezoneOffset").val(timezoneOffset);
    });



    var validatorHeader = $("#form-kesiapan-barang").validate({
        rules: {
            jenisGudang: {
                required: true
            },
            namaPic: {
                required: true
            },
            alamat: {
                required: true
            },
            nomorTelpPic: {
                required: true
            },
            lokasiSiapPeriksa: {
                required: true
            },
            tanggalPkb: {
                required: true
            },
            tanggalSiapPeriksa: {
                required: true
            },
            waktuSiapPeriksa: {
                required: true
            },

        },
        messages: {
            jenisGudang: {
                required: "Kode Jenis Gudang Wajib Diisi"
            },
            namaPic: {
                required: "Nama person in charge wajib diisi"
            },
            alamat: {
                required: "Alamat wajib diisi"
            },
            nomorTelpPic: {
                required: "Nomor telpin person in charge wajib diisi"
            },
            lokasiSiapPeriksa: {
                required: "Lokasi Siap Periksa wajib diisi"
            },
            tanggalPkb: {
                required: "Tanggal PKB wajib diisi"
            },
            tanggalSiapPeriksa: {
                required: "Tanggal Siap Periksa wajib diisi"
            },
            waktuSiapPeriksa: {
                required: "Waktu Siap Periksa wajib diisi"
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
        if ($('#form-kesiapan-barang').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Kesiapan Barang ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-kesiapan-barang"));
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/kesiapan-barang"); ?>",
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