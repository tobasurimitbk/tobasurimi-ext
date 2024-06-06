<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= (!empty($lemburDetail)) ? "Detail" : "Simpan" ?> Lembur</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("lembur"); ?>">
                Batal
            </a>
            <?php if (!empty($lemburDetail)) : ?>
                <?php if (can('Personalia', 'Form Lembur', 'd')) : ?>
                    <a href="#" class="btn btn-hapus delete-parent float-right delete-lembur" data-id="<?= encrypt($lemburDetail['id']) ?>">
                        Hapus
                    </a>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Personalia', 'Form Lembur', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif ?>
        </div>
    </div>
    <form action="#" id="formLembur" method="post">
        <?= csrf_field() ?>

        <input type="hidden" name="totalUangLembur">
        <input type="hidden" name="totalJamLembur">
        <input type="hidden" name="gajiPokokPerHari">

        <div class="card">
            <div class="card-body">
                <label class="form-label font-weight-bold lable-title mt-2">
                    Informasi Karyawan
                </label>
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($lemburDetail)) ? "disabled" : "" ?> class="form-select" name="divisionID" id="divisionID">
                                <option value=""> Pilih Nama Karyawan</option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= (!empty($lemburDetail)) ?  ($lemburDetail['division_id'] == $d['id'] ? "selected" : "") : ""  ?> value="<?= $d['id'] ?>">
                                        <?= strtoupper($d['divisi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Departemen</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select" name="employeeID" id="employeeID" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Nama Karyawan -
                                </option>
                                <?php if (!empty($lemburDetail)) : ?>
                                    <?php foreach ($employees as $e) : ?>
                                        <option <?= $lemburDetail['employee_id'] == $e['id'] ? 'selected' : '' ?> data-nip="<?= $e['nip'] ?>" value="<?= $e['id'] ?>">
                                            - <?= $e['name'] ?> -
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Pilih Nama Karyawan</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" id="nip" placeholder="" class="form-control target input-picker" value="-">
                            <label for="floatingInput">NIP</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Informasi Waktu
                </label>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($lemburDetail)) ? date('d/m/Y', strtotime($lemburDetail['periode']))  : "" ?>" autocomplete="one-time-code" name="tanggalLembur" type="text" required class="form-control target input-picker">
                            <label for="floatingInput">Tanggal Lembur</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" required name="jamKerjaMasuk" id="jamKerjaMasuk" class="form-control target input-picker" value="-">
                            <label for="floatingInput">Jam Kerja Masuk</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" required name="jamKerjaKeluar" id="jamKerjaKeluar" class="form-control target input-picker" value="-">
                            <label for="floatingInput">Jam Kerja Keluar</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 mt-1"></div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" required name="jamMulaiLembur" id="jamMulaiLembur" class="form-control target input-picker" value="-">
                            <label for="floatingInput">Jam Mulai Lembur</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" required name="jamSelesaiLembur" id="jamSelesaiLembur" class="form-control target input-picker" value="<?= !empty($lemburDetail) ? $lemburDetail['jam_selesai_lembur'] : "" ?>">
                            <label for="floatingInput">Jam Selesai Lembur</label>
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select" name="kurangiJamIstirahat" id="kurangiJamIstirahat" aria-label="Floating label select example">
                                <?php if (!empty($lemburDetail)) : ?>
                                    <option selected value="">- <?= ($lemburDetail['kurangi_jam_istirahat']) ? 'YA' : 'TIDAK' ?> -</option>
                                <?php else : ?>
                                    <option value="1">- YA -</option>
                                    <option selected value="0">- TIDAK -</option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Kurangi Jam Istirahat</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" required name="jumlahJamIstirahat" id="jumlahJamIstirahat" class="form-control target input-picker" value="-">
                            <label for="floatingInput">Jumlah Jam Istirahat</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" required name="jumlahJamKerjaBersih" id="jumlahJamKerjaBersih" class="form-control target input-picker" value="-">
                            <label for="floatingInput">Jumlah Jam Kerja Bersih</label>
                        </div>
                    </div>
                </div> -->

                <!-- Komponen Gaji -->
                <div id="rincanLembur">
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="tabelGaji" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;" class="sort">No</th>
                                    <th onclick="" class="sort">Jenis Komponen Gaji</th>
                                    <th onclick="" class="sort">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="body-table" id="body-table">
                                <tr>
                                    <td colspan="3" class="text-center">
                                        Pilih karyawan dan tanggal lembur dulu yha
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <label class="form-label font-weight-bold lable-title mt-2">
                        Rincan Perhitungan Uang Lembur
                    </label>
                    <table class="table mt-3 p-3" width="100%" cellspacing="0">
                        <tbody class="body-table" id="rincanBiayaLembur">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>

    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    $('#rincanLembur').hide();

    $('#employeeID').change(function(e) {
        e.preventDefault();
        var selectedOption = $(this).find(":selected");
        var nip = selectedOption.data("nip");
        $('#nip').val(nip);
    });

    $("select[name='employeeID']").select2({
        placeholder: "Pilih Nama Karyawan",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("select[name='divisionID']").select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    });

    $("input[name='tanggalLembur']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("input[name='jamSelesaiLembur']").datetimepicker({
        format: 'HH:mm',
        icons: {
            up: 'fas fa-chevron-up',
            down: 'fas fa-chevron-down'
        },
    }).on('dp.change', function(e) {
        generateLembur();
    });

    $("input[name='tanggalLembur'], select[name='kurangiJamIstirahat'], select[name='employeeID']").change(function() {
        generateLembur();
    });

    $("input[name='jamSelesaiLembur']").on('change', function() {
        generateLembur();
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    function generateLembur() {

        // set variable
        const csrf = $(`[name="${csrfToken}"]`);
        var employeeID = $("select[name='employeeID']").val();
        var tanggalLembur = $("input[name='tanggalLembur']").val();
        var kurangiJamIstirahat = $("select[name='kurangiJamIstirahat']").val();
        var jamSelesaiLembur = $("input[name='jamSelesaiLembur']").val();
        var gajiPokokPerHari = "<?= (!empty($lemburDetail)) ?  $lemburDetail['gaji_pokok_per_hari'] : "-" ?>";

        // append to form
        var formData = new FormData();
        formData.append('employeeID', employeeID);
        formData.append('tanggalLembur', tanggalLembur);
        formData.append('kurangiJamIstirahat', kurangiJamIstirahat);
        formData.append('jamSelesaiLembur', jamSelesaiLembur);
        formData.append('gajiPokokPerHari', gajiPokokPerHari);

        // generate action
        $.ajax({
            url: "<?= base_url("lembur/generate-pay"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                // setLoading();
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            complete: function() {
                // stopLoading();
            },
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) {
                    var table = $('#tabelGaji');
                    table.find('tbody').empty();

                    var jamKerja = response.jamKerja;
                    var lemburJamPertama = response.lembur.lemburJamPertama;
                    var lemburJamBerikutnya = response.lembur.lemburJamBerikutnya;

                    $.each(response.komponenGaji, function(index, data) {
                        var newRow = $('<tr style="color:whitesmoke; font-weight:bold;">');
                        var indexNumber = index + 1;
                        var nominal = "<?= !empty($lemburDetail) ? $lemburDetail['gaji_pokok_per_hari'] : "-" ?>"
                        newRow.append($('<td>').text(indexNumber));
                        newRow.append($('<td>').text(data.name));
                        newRow.append($('<td>').text(data.nominal != null ? formatRupiah(nominal == "-" ? data.nominal : nominal) : 0));
                        table.append(newRow);
                    });

                    $('input[name="jamKerjaMasuk"]').val(jamKerja.jamKerjaMasuk);
                    $('input[name="jamKerjaKeluar"]').val(jamKerja.jamKerjaKeluar);
                    $('input[name="jamMulaiLembur"]').val(jamKerja.jamMulaiLembur);
                    $('input[name="jamSelesaiLembur"]').val(jamKerja.jamSelesaiLembur);
                    $('input[name="jumlahJamIstirahat"]').val(jamKerja.jumlahJamIstirahat);
                    $('input[name="jumlahJamKerjaBersih"]').val(jamKerja.jumlahJamKerjaBersih);

                    var tbody = $("#rincanBiayaLembur");
                    tbody.empty();

                    var dataToAdd = [{
                            column1: "1.",
                            column2: "1/173 x 25 x 1.5",
                            column3: "x",
                            column4: lemburJamPertama.totalLemburJamPertama + " x " + formatRupiah(response.upah),
                            column5: formatRupiah(lemburJamPertama.bayaran),
                        },
                        {
                            column1: "2.",
                            column2: "1/173 x 25 x 2",
                            column3: "x",
                            column4: lemburJamBerikutnya.totalLemburJamKedua + " x " + formatRupiah(response.upah),
                            column5: formatRupiah(lemburJamBerikutnya.bayaran),
                        },
                        {
                            column1: "",
                            column2: "",
                            column3: "",
                            column4: "",
                            column5: formatRupiah(response.lembur.totalBayaran)
                        }
                    ];

                    $('input[name="totalUangLembur"]').val(response.lembur.totalBayaran);
                    $('input[name="totalJamLembur"]').val(response.lembur.totalJamLembur);
                    $('input[name="gajiPokokPerHari"]').val(response.upah);

                    $.each(dataToAdd, function(index, data) {
                        var newRow = $("<tr class='text-dark font-weight-bold' style='color:whitesmoke;'>");
                        newRow.append($("<td style='width: 10px;'>").text(data.column1));
                        newRow.append($("<td>").text(data.column2));
                        newRow.append($("<td>").text(data.column3));
                        newRow.append($("<td>").text(data.column4));
                        newRow.append($("<td>").text(data.column5));
                        tbody.append(newRow);
                    });

                    showRincianUangLembur();


                } else if (!response.status && response.code == 400) {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    });
                    var table = $('#tabelGaji');
                    table.find('tbody').empty();
                    hideRincianUangLembur();

                    $('input[name="jamKerjaMasuk"]').val(null);
                    $('input[name="jamKerjaKeluar"]').val(null);
                    $('input[name="jamMulaiLembur"]').val(null);
                    $('input[name="jamSelesaiLembur"]').val(null);
                    $('input[name="jumlahJamIstirahat"]').val(null);
                    $('input[name="jumlahJamKerjaBersih"]').val(null);
                    $('input[name="totalUangLembur"]').val(null);
                    $('input[name="gajiPokokPerHari"]').val(null);
                }

            },
            onError: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan pada sistem',
                    confirmButtonColor: '#4e73df',
                });
            }
        });

    }

    var validator = $("#formLembur").validate({
        rules: {
            employeeID: {
                required: true
            },
            tanggalLembur: {
                required: true
            },
            jamKerjaMasuk: {
                required: true
            },
            jamKerjaKeluar: {
                required: true
            },
            jamMulaiLembur: {
                required: true
            },
            jamSelesaiLembur: {
                required: true
            },
            jumlahJamIstirahat: {
                required: true
            },
            jumlahJamKerjaBersih: {
                required: true
            }
        },
        messages: {
            employeeID: {
                required: "Pilih pegawai terlebih dahulu"
            },
            tanggalLembur: {
                required: " Tanggal Lembur wajib diisi"
            },
            jamKerjaMasuk: {
                required: " Jam Kerja Masuk wajib diisi"
            },
            jamKerjaKeluar: {
                required: " Jam Kerja Keluar wajib diisi"
            },
            jamMulaiLembur: {
                required: " Jam Mulai Lembur wajib diisi"
            },
            jamSelesaiLembur: {
                required: " Jam Selesai Lembur wajib diisi"
            },
            jumlahJamIstirahat: {
                required: " Jumlah Jam Istirahat wajib diisi"
            },
            jumlahJamKerjaBersih: {
                required: " Jumlah Jam Kerja Bersih wajib diisi"
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

    $('.btn-submit').click(function() {
        if ($("#formLembur").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);

                    var employeeID = $('select[name="employeeID"]').val();
                    var tanggalLembur = $('input[name="tanggalLembur"]').val();
                    var totalJamLembur = $('input[name="totalJamLembur"]').val();
                    var totalUangLembur = $('input[name="totalUangLembur"]').val();
                    var kurangiJamIstirahat = $('select[name="kurangiJamIstirahat"]').val();
                    var jamMulaiLembur = $('input[name="jamMulaiLembur"]').val();
                    var jamSelesaiLembur = $('input[name="jamSelesaiLembur"]').val();
                    var gajiPokokPerHari = $('input[name="gajiPokokPerHari"]').val();

                    if (totalUangLembur != 0) {
                        setLoading()
                        // append to form
                        var formData = new FormData();
                        formData.append('employeeID', employeeID);
                        formData.append('tanggalLembur', tanggalLembur);
                        formData.append('totalJamLembur', totalJamLembur);
                        formData.append('totalUangLembur', totalUangLembur);
                        formData.append('kurangiJamIstirahat', kurangiJamIstirahat);
                        formData.append('jamMulaiLembur', jamMulaiLembur);
                        formData.append('jamSelesaiLembur', jamSelesaiLembur);
                        formData.append('gajiPokokPerHari', gajiPokokPerHari);

                        // update dan delete
                        $.ajax({
                            url: "<?= base_url("lembur/create"); ?>",
                            data: formData,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.status) {
                                    stopLoading()
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            window.location.href = "<?= base_url("lembur"); ?>";
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Nominal uang lembur yang diterima tidak boleh kosong',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        }
    });

    $("select[name='divisionID']").on('change', function(e) {
        e.preventDefault();
        const csrf = $(`[name="${csrfToken}"]`);
        var divisionID = $(this).val();
        var formData = new FormData();
        formData.append('divisionID', divisionID);

        $.ajax({
            url: "<?= base_url("lembur/employees"); ?>",
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                var employeeSelect = $("select[name='employeeID']");
                employeeSelect.empty();

                var emptyOption = $("<option></option>")
                    .attr("value", "")
                    .text("Pilih Nama Karyawan");

                employeeSelect.append(emptyOption);

                $.each(response.data, function(index, data) {
                    var option = $("<option data-nip=" + data.nip + "></option>")
                        .attr("value", data.id)
                        .text(data.name);
                    employeeSelect.append(option);
                });

            },
            onError: function(response) {
                csrf.val(response.token);

            }
        });

    });

    // function helper
    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join('.').split('').reverse().join('');
        return 'Rp. ' + formatted;
    }
</script>
<?php if (!empty($lemburDetail)) : ?>
    <script>
        var selectedOption = $('select[name="employeeID"]').find(":selected");
        var divisi = selectedOption.data("divisi");
        var nip = selectedOption.data("nip");
        $('#nip').val(nip);
        $('#divisi').val(divisi);

        $('select[name="employeeID"]').prop('disabled', true);
        $('input[name="tanggalLembur"]').prop('disabled', true);
        $('select[name="kurangiJamIstirahat"]').prop('disabled', true);
        $('input[name="jamSelesaiLembur"]').prop('disabled', true);

        generateLembur();


        $('.delete-lembur').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Lembur ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                setLoading()
                // csrf
                const csrfToken = '<?= csrf_token() ?>';
                const csrf = $(`[name="${csrfToken}"]`);
                var lemburID = $(this).data('id');

                var formData = new FormData();
                formData.append('id', lemburID);

                $.ajax({
                    url: "<?= base_url("lembur/delete"); ?>",
                    data: formData,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            stopLoading()
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url("lembur"); ?>";
                                });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lembur gagal dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                        stopLoading()
                    }
                });

            });
        });
    </script>
<?php endif; ?>

<script>
    function hideRincianUangLembur() {
        $('#rincanLembur').hide();
    }

    function showRincianUangLembur() {
        $('#rincanLembur').show();
    }
</script>

<?= $this->endSection(); ?>