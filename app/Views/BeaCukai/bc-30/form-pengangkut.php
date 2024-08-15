<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Tambah Pemilik</h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <form id="pengangkut-form-table" class="pengangkut-form-table">
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="nama_sarana_angkut" value="" name="nama_sarana_angkut" type="text" class="nama_sarana_angkut form-control" placeholder="">
                            <label>Nama Sarana Angkut</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select cara_pengangkutan" id="cara_pengangkutan" name="cara_pengangkutan" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($pengangkut as $p) : ?>
                                    <option value="<?= $p['description'] ?>">
                                        <?= $p['description'] . " - " . $p['value'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Cara Pengangkutan</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="nomor_pengangkutan" value="" name="nomor_pengangkutan" type="text" class="nomor_pengangkutan form-control" placeholder="">
                            <label>Nomor Voy/Flight</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select pengangkutan_negara" id="pengangkutan_negara" name="pengangkutan_negara" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeNegaraAsal as $k) : ?>
                                    <option value="<?= encrypt($k['code']) ?>">
                                        <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Bendera</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn-tambah-data-pemilik-barang" class="btn btn-submit-form btn-submit-parent">Tambah</button>
            </div>
        </div>
    </div>
</div>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 3.0 - PEMBERITAHUAN EKSPOR BARANG
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <form id="form-pengangkut">
                    <input type="hidden" name="id" value="<?= encrypt($bc30['id']) ?>" class="id" id="id">
                    <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                        Pengangkutan
                    </label>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengangkut_tempat_penimbuhan" id="pengangkut_tempat_penimbuhan" name="pengangkut_tempat_penimbuhan" aria-label="Floating label select example">
                                        <?php if (!empty($payload)) : ?>
                                            <option value="<?= $payload->kodeTps ?>"><?= $payload->kodeTps ?></option>
                                        <?php else : ?>
                                            <option value=""></option>
                                        <?php endif; ?>
                                    </select>
                                    <label style="z-index: 1;">Tempat Penimbuhan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengangkut_muat_asal" id="pengangkut_muat_asal" name="pengangkut_muat_asal" aria-label="Floating label select example">
                                        <?php if (!empty($payload)) : ?>
                                            <option value="<?= $payload->kodePelMuat ?>"><?= $payload->kodePelMuat ?></option>
                                        <?php else : ?>
                                            <option value=""></option>
                                        <?php endif; ?>
                                    </select>
                                    <label style="z-index: 1;">Pelabuhan Muat Asal </label>
                                </div>
                                <input id="kodeKantorMuat" class="kodeKantorMuat" type="hidden" value="<?= $payload->kodeKantorMuat != "" ? $payload->kodeKantorMuat : "" ?>">
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select disabled class="form-select pengangkut_muat_ekspor" id="pengangkut_muat_ekspor" name="pengangkut_muat_ekspor" aria-label="Floating label select example">
                                        <?php if ($payload->kodePelEkspor != ""):  ?>
                                            <option value="<?= $payload->kodePelEkspor ?>"><?= $payload->kodePelEkspor ?></option>
                                        <?php else: ?>
                                            <option value=""></option>
                                        <?php endif; ?>


                                    </select>
                                    <label style="z-index: 1;">Pelabuhan Muat Ekspor </label>
                                </div>

                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengangkut_bongkar" id="pengangkut_bongkar" name="pengangkut_bongkar" aria-label="Floating label select example">
                                        <?php if (!empty($payload)) : ?>
                                            <option value="<?= $payload->kodePelBongkar ?>"><?= $payload->kodePelBongkar ?></option>
                                        <?php else : ?>
                                            <option value=""></option>
                                        <?php endif; ?>
                                    </select>
                                    <label style="z-index: 1;">Pelabuhan Bongkar </label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengangkut_tujuan" id="pengangkut_tujuan" name="pengangkut_tujuan" aria-label="Floating label select example">
                                        <?php if (!empty($payload)) : ?>
                                            <option <?= $payload->kodePelTujuan ? 'selected' : '' ?> value="<?= $payload->kodePelTujuan ?>"><?= $payload->kodePelTujuan  ?></option>
                                        <?php else : ?>
                                            <option value=""></option>
                                        <?php endif; ?>
                                    </select>
                                    <label style="z-index: 1;">Pelabuhan tujuan </label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select negara_tujuan_ekspor" id="negara_tujuan_ekspor" name="negara_tujuan_ekspor" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeNegaraAsal as $k) : ?>
                                            <option value="<?= encrypt($k['code']) ?>" <?= $payload->kodeNegaraTujuan == $k['code'] ? 'selected' : '' ?>>
                                                <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Negara Tujuan Ekspor</label>
                                </div>
                            </div>

                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" value="<?= empty($payload->tanggalEkspor) ? '' : date('d/m/Y', strtotime($payload->tanggalEkspor))  ?>" class="form-control input-picker pengangkutan_tanggal_perkiraan_ekspor" id="pengangkutan_tanggal_perkiraan_ekspor" name="pengangkutan_tanggal_perkiraan_ekspor" placeholder="Tanggal Perkiraan Ekspor">
                                            <label for="floatingInput">Tanggal Perkiraan Ekspor</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99 ; margin-bottom: 20px ;margin-left: -30px; border: 0px" class="fa fa-calendar icon-form"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengangkutan_lokasi_pemeriksaan" id="pengangkutan_lokasi_pemeriksaan" name="pengangkutan_lokasi_pemeriksaan" aria-label="Floating label select example">
                                        <?php if (!empty($payload->kodeLokasi)) : ?>
                                            <option value=""></option>
                                            <option value="1" <?= $payload->kodeLokasi == 1 ? 'selected' : '' ?>>1 - KP TEMPAT PEMUATAN</option>
                                            <option value="2" <?= $payload->kodeLokasi == 2 ? 'selected' : '' ?>>2 - GUDANG EKSPORTIR</option>
                                            <option value="3" <?= $payload->kodeLokasi == 3 ? 'selected' : '' ?>>3 - TEMPAT LAIN YANG DIIZINKAN</option>
                                            <option value="4" <?= $payload->kodeLokasi == 4 ? 'selected' : '' ?>>4 - TPS</option>
                                            <option value="5" <?= $payload->kodeLokasi == 5 ? 'selected' : '' ?>>5 - TPP</option>
                                            <option value="6" <?= $payload->kodeLokasi == 6 ? 'selected' : '' ?>>6 - TPB</option>
                                        <?php else : ?>
                                            <option value=""></option>
                                            <option value="1">1 - KP TEMPAT PEMUATAN</option>
                                            <option value="2">2 - GUDANG EKSPORTIR</option>
                                            <option value="3">3 - TEMPAT LAIN YANG DIIZINKAN</option>
                                            <option value="4">4 - TPS</option>
                                            <option value="5">5 - TPP</option>
                                            <option value="6">6 - TPB</option>

                                        <?php endif; ?>

                                    </select>
                                    <label style="z-index: 1;">Lokasi Pemeriksaan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <div class="input-group input-group-password">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" value="<?= empty($payload->tanggalPeriksa) ? '' : date('d/m/Y', strtotime($payload->tanggalPeriksa))  ?>" class="form-control input-picker pengangkutan_tanggal_pemeriksa" id="pengangkutan_tanggal_pemeriksa" name="pengangkutan_tanggal_pemeriksa" placeholder="Tanggal Perkiraan Ekspor">
                                            <label for="floatingInput">Tanggal Pemeriksa</label>
                                        </div>
                                        <div class="input-group-prepend group-prepend-password align-items-center">
                                            <i style="cursor: pointer; z-index: 99 ; margin-bottom: 20px ;margin-left: -30px; border: 0px" class="fa fa-calendar icon-form"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select pengangkutan_kantor_pemeriksa" id="pengangkutan_kantor_pemeriksa" name="pengangkutan_kantor_pemeriksa" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKantor as $k) : ?>
                                            <option value="<?= $k['kode'] ?>" <?= $payload->kodeKantorPeriksa == $k['kode'] ? 'selected' : '' ?>>
                                                <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kantor Pemeriksa</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex align-content-center">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold lable-title mt-4 mb-2" style="margin-bottom:5px;">
                                    SARANA ANGKUT
                                </label>
                            </div>
                            <div class=" col-md-6">
                                <div class="row" style="float: right;">
                                    <div class="col-sm" style="margin-right: -20px;">
                                        <button type="button" class="btn btn-add btn-block float-right btn-submit-kemasan" id="btn-display-modal" style="float: right;">
                                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pengangkut" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">Seri Dokumen</th>
                                        <th style="text-align: center;">Nama Sarkut</th>
                                        <th style="text-align: center;">Nomor Pengangkut</th>
                                        <th style="text-align: center;">Cara Pengangkut</th>
                                        <th style="text-align: center;">Bendera</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($pengangkutData) == 0) : ?>
                                        <tr style="color: white; text-align:center;">
                                            <td colspan="5">Tidak ada data pengangkutan</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php $length = count($pengangkutData); ?>
                                        <?php foreach ($pengangkutData as $i => $p) : ?>
                                            <tr style="color: white; text-align:center;">
                                                <td><?= $p['seriPengangkut'] ?></td>
                                                <td><?= $p['namaPengangkut'] ?></td>
                                                <td><?= $p['nomorPengangkut'] ?></td>
                                                <td><?= $p['kodeCaraAngkut'] ?></td>
                                                <td><?= $p['kodeBendera'] ?></td>
                                                <td>
                                                    <?php if ($i == $length - 1) : ?>
                                                        <button type="button" class="btn btn-danger" onclick="removeData(<?= $i ?>)"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
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

    <?php if ($payload->kodeKantor != "") : ?>
        $.ajax({
            url: `<?= base_url("bea-cukai-bc-23/api/get-tps-by-kode-kantor"); ?>`,
            method: "GET",
            data: {
                kodeKantor: <?= $payload->kodeKantor; ?>
            },
            beforeSend: function() {

            },
            complete: function() {

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
                    $('#pengangkut_tempat_penimbuhan').empty();
                    $.each(res.data.data, function(i, v) {
                        var option = $('<option>').val(v.kodeGudang).text(v.kodeGudang + " - " + v.namaGudang);
                        $('#pengangkut_tempat_penimbuhan').append(option);
                    });
                }
            }
        });
    <?php endif; ?>
    <?php if ($payload->kodeKantorMuat != "") :  ?>
        var kodeKantorMuat = $("#kodeKantorMuat").val();
        $.ajax({
            url: `<?= base_url("bea-cukai-bc-23/api/get-pelabuhan"); ?>`,
            method: "GET",
            data: {
                header_kantor_pabean_bongkar: kodeKantorMuat
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
                    $('#pengangkut_muat_asal').empty();
                    $.each(res.data.data, function(i, v) {
                        var option = $('<option>').val(v.kodePelabuhan).text(v.kodePelabuhan);
                        $('#pengangkut_muat_asal').append(option);
                    });
                }
            }
        });
    <?php endif; ?>

    $('#pengangkut_tempat_penimbuhan').select2({
        placeholder: "Pilih Tempat Penimbuhan",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#negara_tujuan_ekspor').select2({
        placeholder: "Pilih Kode Negara Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#pengangkut_muat_asal').select2({
        placeholder: "Pilih Pelabuhan Muat Asal",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#pengangkut_muat_ekspor').select2({
        placeholder: "Pilih Pelabuhan Muat Asal",
        theme: "bootstrap-5",
        allowClear: true
    });



    $('#pengangkut_bongkar').select2({
        placeholder: "Pilih Pelabuhan Bongkar",
        theme: "bootstrap-5",
        allowClear: true,
        ajax: {
            url: '<?= base_url('bea-cukai-bc-23/api/get-pelabuhan-by-kata') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                };
            },
            success: function(res) {
                if (res.data.status === false) {
                    Swal.fire({
                        icon: 'error',
                        title: res.data.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    $('#pengangkut_bongkar').empty();
                    $.each(res.data.data, function(i, v) {
                        var option = $('<option>').val(v.kodePelabuhan).text(v.kodePelabuhan + " - " + v.namaPelabuhan);
                        $('#pengangkut_bongkar').append(option);
                    });
                }
            },
            cache: true
        },
        minimumInputLength: 1,

    });
    $('#pengangkut_tujuan').select2({
        placeholder: "Pilih Pelabuhan Tujuan",
        theme: "bootstrap-5",
        allowClear: true,
        ajax: {
            url: '<?= base_url('bea-cukai-bc-23/api/get-pelabuhan-by-kata') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                };
            },
            success: function(res) {
                if (res.data.status === false) {
                    Swal.fire({
                        icon: 'error',
                        title: res.data.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    $('#pengangkut_tujuan').empty();
                    $.each(res.data.data, function(i, v) {
                        var option = $('<option>').val(v.kodePelabuhan).text(v.kodePelabuhan + " - " + v.namaPelabuhan);
                        $('#pengangkut_tujuan').append(option);
                    });
                }
            },
            cache: true
        },
        minimumInputLength: 1,
    });



    $('#pengangkutan_lokasi_pemeriksaan').select2({
        placeholder: "Pilih Lokasi Pemeriksaan",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#pengangkutan_kantor_pemeriksa').select2({
        placeholder: "Pilih Kantor Pemeriksaan",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#cara_pengangkutan').select2({
        placeholder: "Pilih Cara Pengangkutan",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#pengangkutan_negara').select2({
        placeholder: "Pilih Negara",
        theme: "bootstrap-5",
        allowClear: true
    });

    $(".pengangkutan_tanggal_perkiraan_ekspor").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });
    $(".pengangkutan_tanggal_pemeriksa").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var validatorSaranaPengangkut = $("#pengangkut-form-table").validate({
        rules: {
            nama_sarana_angkut: {
                required: true,
            },
            cara_pengangkutan: {
                required: true,

            },
            nomor_pengangkutan: {
                required: true,
            },
            pengangkutan_negara: {
                required: true,
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
    })

    var validatorPengangkut = $("#form-pengangkut").validate({
        rules: {
            pengangkut_tempat_penimbuhan: {
                required: true
            },
            pengangkut_muat_asal: {
                required: true
            },
            pengangkut_muat_ekspor: {
                required: true
            },
            pengangkut_bongkar: {
                required: true
            },
            pengangkut_tujuan: {
                required: true
            },
            pengangkutan_tanggal_perkiraan_ekspor: {
                required: true
            },
            pengangkutan_lokasi_pemeriksaan: {
                required: true
            },
            pengangkutan_tanggal_pemeriksa: {
                required: true
            },
            pengangkutan_kantor_pemeriksa: {
                required: true
            },

        },
        messages: {
            pengangkut_tempat_penimbuhan: {
                required: "Tempat Penimbuhan wajib diisi"
            },
            pengangkut_muat_asal: {
                required: "Pelabuhan Muat Asal wajib diisi"
            },
            pengangkut_muat_ekspor: {
                required: "Pelabhuan Muat Ekspor wajib diisi"
            },
            pengangkut_bongkar: {
                required: "Pelabuhan Bongkar wajib diisi"
            },
            pengangkut_tujuan: {
                required: "Pelabuhan TUjuan wajib diisi"
            },
            pengangkutan_tanggal_perkiraan_ekspor: {
                required: "Tanggal Perkiraan Ekspor wajib diisi"
            },
            pengangkutan_lokasi_pemeriksaan: {
                required: "Lokasi Pemeriksaan wajib diisi"
            },
            pengangkutan_tanggal_pemeriksa: {
                required: "Tanggal Pemeriksa wajib diisi"
            },
            pengangkutan_kantor_pemeriksa: {
                required: "Kantor Pemeriksa wajib diisi"
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

    $('#btn-display-modal').click(function() {
        $('.add-modal').modal('show');
        validator.resetForm();
        validator.reset();
        $(".pengangkut-form")[0].reset();
    })

    $('#btn-loading').hide();
    $('#btn-tambah-data-pemilik-barang').click(function() {
        if ($('#pengangkut-form-table').valid()) {
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
                    var formData = new FormData(document.querySelector('#pengangkut-form-table'))
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/pengangkut-insert-table"); ?>",
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
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    formData.append("pengangkut_muat_ekspor", $("#pengangkut_muat_ekspor").val());
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/pengangkut"); ?>",
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

    function removeData(index_delete) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Pengangkut ?',
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
                    url: "<?= base_url("bea-cukai-bc-30/id/pengangkut-delete-table"); ?>",
                    data: {
                        id: "<?= encrypt($bc30['id']) ?>",
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