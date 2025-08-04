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
            <div class="card-header" style="font-weight: bold;">
                BC 3.0 - PEMBERITAHUAN EKSPOR BARANG
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-header">
                    <input type="hidden" name="id" id="id" class="id" value="<?= encrypt($bc30['id']) ?>">
                    <div class="row">
                        <div class="col-sm-12 mt-1">
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input disabled id="nomorAju" value="<?= $noAju ?>" name="nomorAju" type="text" readonly class="nomorAju form-control" placeholder="">
                                    <label>Nomor Pengajuan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeKantorMuat" id="kodeKantorMuat" name="kodeKantorMuat" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option <?= $payload->kodeKantorMuat == "" ? (encrypt($selectedKantor) == encrypt($k['kode']) ? 'selected' : '') : '' ?> <?= $payload->kodeKantorMuat != "" ? ($payload->kodeKantorMuat == $k['kode'] ? 'selected' : '') : "" ?> value="<?= $k['kode'] ?>">
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kantor Pabean Muatan Asal</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodePelabuhan" id="kodePelabuhan" name="kodePelabuhan" aria-label="Floating label select example">
                                        <option value="<?= $payload->kodePelEkspor ?>"><?= $payload->kodePelEkspor ?></option>
                                    </select>
                                    <label style="z-index: 1;">Pelabuhan Muat Ekspor</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select disabled class="form-select kodeKantorEkspor" id="kodeKantorEkspor" name="kodeKantorEkspor" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option <?= $payload->kodeKantorEkspor == "" ? (encrypt($selectedKantor) == encrypt($k['kode']) ? 'selected' : '') : '' ?> <?= $payload->kodeKantorEkspor != "" ? ($payload->kodeKantorEkspor == $k['kode'] ? 'selected' : '') : "" ?> value="<?= $k['kode'] ?>">
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kantor Pabean Muatan Ekspor Asal</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select jenisEkspor" id="jenisEkspor" name="jenisEkspor" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1" <?= $payload->kodeJenisEkspor != "" ?  ($payload->kodeJenisEkspor == "1" ? "selected" : "") : ""; ?>>1 - EKSPOR BIASA</option>
                                        <option value="2" <?= $payload->kodeJenisEkspor != "" ?  ($payload->kodeJenisEkspor == "2" ? "selected" : "") : ""; ?>>2 - EKSPOR AKAN DIIMPOR KEMBALI</option>
                                        <option value="3" <?= $payload->kodeJenisEkspor != "" ?  ($payload->kodeJenisEkspor == "3" ? "selected" : "") : ""; ?>>3 - EKSPOR REEKSPOR LAINNYA</option>
                                        <option value="4" <?= $payload->kodeJenisEkspor != "" ?  ($payload->kodeJenisEkspor == "4" ? "selected" : "") : ""; ?>>4 - EKSPOR REEKSPOR EX IMPOR SEMENTARA</option>
                                    </select>
                                    <label style="z-index: 1;">Jenis Ekspor</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kategoriEkspor" id="kategoriEkspor" name="kategoriEkspor" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="10" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "10" ? "selected" : "") : ""; ?>>10 - UMUM</option>
                                        <option value="21" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "21" ? "selected" : "") : ""; ?>>21 - YANG PADA SAAT IMPOR MENDAPAT FASILITAS PEMBEBASAN BM (NIPER DGB PEMBEBASAN)</option>
                                        <option value="22" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "22" ? "selected" : "") : ""; ?>>22 - YANG PADA SAAT IMPOR MENDAPAT FASILITAS PENGEMBALIAN BM (NIPER DGN PENGEMBALIAN)</option>
                                        <option value="23" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "23" ? "selected" : "") : ""; ?>>23 - KITE DENGAN PEMBEBASAN DAN PENGEMBALIAN</option>
                                        <option value="31" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "31" ? "selected" : "") : ""; ?>>31 - KHUSUS BARANG PERWAKILAN NEGARA ASING</option>
                                        <option value="32" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "32" ? "selected" : "") : ""; ?>>32 - KHUSUS BARANG BADAN INTERNASIONAL</option>
                                        <option value="33" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "33" ? "selected" : "") : ""; ?>>33 - KHUSUS BARANG KIRIMAN (POS ATAU JASA TITIPAN)</option>
                                        <option value="34" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "34" ? "selected" : "") : ""; ?>>34 - KHUSUS BARANG PINDAHAN</option>
                                        <option value="35" <?= $payload->kodeKategoriEkspor != "" ?  ($payload->kodeKategoriEkspor == "35" ? "selected" : "") : ""; ?>>35 - KHUSUS BARANG KEPERLUAN IBADAH UNTUK UMUM, SOSIAL, PENDIDIKAN, KEBUDAYAAN / OLAHRAGA DAN BENCANA ALAM</option>
                                    </select>
                                    <label style="z-index: 1;">Kategori Ekspor</label>

                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select caraDagang" id="caraDagang" name="caraDagang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1" <?= $payload->kodeCaraDagang != "" ?  ($payload->kodeCaraDagang == "1" ? "selected" : "") : ""; ?>>1 - BIASA</option>
                                        <option value="15" <?= $payload->kodeCaraDagang != "" ?  ($payload->kodeCaraDagang == "15" ? "selected" : "") : ""; ?>>15 - LAINNYA</option>
                                        <option value="2" <?= $payload->kodeCaraDagang != "" ?  ($payload->kodeCaraDagang == "2" ? "selected" : "") : ""; ?>>2 - IMB</option>
                                    </select>
                                    <label style="z-index: 1;">Cara Dagang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select caraBayar" id="caraBayar" name="caraBayar" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1" <?= $payload->kodeCaraBayar != "" ?  ($payload->kodeCaraBayar == "1" ? "selected" : "") : ""; ?>>1 - BIASA / TUNAI</option>
                                        <option value="2" <?= $payload->kodeCaraBayar != "" ?  ($payload->kodeCaraBayar == "2" ? "selected" : "") : ""; ?>>2 - BERKALA</option>
                                        <option value="3" <?= $payload->kodeCaraBayar != "" ?  ($payload->kodeCaraBayar == "3" ? "selected" : "") : ""; ?>>3 - DENGAN JAMINAN</option>
                                        <option value="9" <?= $payload->kodeCaraBayar != "" ?  ($payload->kodeCaraBayar == "9" ? "selected" : "") : ""; ?>>9 - GABUNGAN / LAINNYA</option>
                                    </select>
                                    <label style="z-index: 1;">Cara Bayar</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select komoditi" id="komoditi" name="komoditi" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1" <?= $payload->flagMigas != "" ?  ($payload->flagMigas == "1" ? "selected" : "") : ""; ?>>1 - MIGAS</option>
                                        <option value="2" <?= $payload->flagMigas != "" ?  ($payload->flagMigas == "2" ? "selected" : "") : ""; ?>>2 - NON MIGAS</option>
                                    </select>
                                    <label style="z-index: 1;">Komoditi</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select curah" id="curah" name="curah" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1" <?= $payload->flagCurah != "" ?  ($payload->flagCurah     == "1" ? "selected" : "") : ""; ?>>1 - CURAH </option>
                                        <option value="2" <?= $payload->flagCurah != "" ?  ($payload->flagCurah     == "2" ? "selected" : "") : ""; ?>>2 - NON CURAH</option>
                                    </select>
                                    <label style="z-index: 1;">Curah</label>
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

    $('#kodeKantorMuat').select2({
        placeholder: "Pilih Kode Kantor Asal",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        $.ajax({
            url: `<?= base_url("bea-cukai-bc-23/api/get-pelabuhan"); ?>`,
            method: "GET",
            data: {
                header_kantor_pabean_bongkar: $('#kodeKantorMuat').val()
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
        var kodeKantorMuat = $('#kodeKantorMuat').val()
        console.log(kodeKantorMuat);
        $('#kodeKantorEkspor').val(kodeKantorMuat);
    });

    $('#jenisEkspor').select2({
        placeholder: "Pilih Kode Jenis Ekspor",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kategoriEkspor').select2({
        placeholder: "Pilih Kode Kategori Ekspor",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#caraDagang').select2({
        placeholder: "Pilih Kode Cara Dagang",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#caraBayar').select2({
        placeholder: "Pilih Kode Cara Bayar",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#komoditi').select2({
        placeholder: "Pilih Kode Migas",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#curah').select2({
        placeholder: "Pilih Kode Curah",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#kodePelabuhan').select2({
        placeholder: "Pilih Kode Pelabuhan Ekspor",
        theme: "bootstrap-5",
        allowClear: true
    });


    var validatorHeader = $("#form-header").validate({
        rules: {
            nomorAju: {
                required: true
            },
            kodeKantor: {
                required: true
            },

        },
        messages: {
            nomorAju: {
                required: "Nomor pengajuan wajib diisi"
            },
            kodeKantor: {
                required: "Kode kantor wajib diisi"
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
                    formData.append("nomorAju", $('#nomorAju').val());
                    formData.append("kodeKantorEkspor", $('#kodeKantorEkspor').val());
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/header"); ?>",
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