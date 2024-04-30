<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Generate Payroll</h5>
            </div>
            <div class="modal-body">
                <?= csrf_field() ?>
                <ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-top: -20px;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#global" type="button" role="tab" aria-controls="home" aria-selected="true">Global (Seluruh Karyawan)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab" aria-controls="profile" aria-selected="false">Personal (Per Karyawan)</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="global" role="tabpanel" aria-labelledby="home-tab">
                        <form id="formGenerateGlobal" role="form" method="POST">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="form-floating mt-1">
                                        <input value="<?= $year . '-' . $month ?>" readonly autocomplete="one-time-code" name="monthYearGlobal" type="month" required class="form-control target">
                                        <label>Periode Absensi</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating mt-3">
                                        <select class="form-select" id="divisionGlobalID" name="divisionGlobalID" aria-label="Floating label select example">
                                            <option value="">
                                                Cari Departemen
                                            </option>
                                            <option value="ALL">
                                                Semua Departemen
                                            </option>
                                            <?php foreach ($divisi as $d) : ?>
                                                <option value="<?= $d['id'] ?>">
                                                    <?= $d['divisi']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">Cari Departemen</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="<?= $startDate ?>" autocomplete="one-time-code" name="startDateGlobal" type="text" required class="form-control target input-picker startDate">
                                        <label for="floatingInput">Mulai</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="<?= $endDate ?>" autocomplete="one-time-code" name="finishDateGlobal" type="text" required class="form-control target input-picker endDate">
                                        <label for="floatingInput">Selesai </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-submit-form" id="globalGenerateBtn">Generate</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="single" role="tabpanel" aria-labelledby="profile-tab">
                        <form id="formGeneratePersonal">
                            <div class="row mb-2">
                                <div class="col-md-12 mt-3">
                                    <div class="form-floating mt-1">
                                        <input value="<?= $year . '-' . $month ?>" readonly autocomplete="one-time-code" name="monthYearPersonal" type="month" required class="form-control target">
                                        <label>Periode Absensi</label>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="divisionID" name="filterDivisiID" aria-label="Floating label select example">
                                            <option value="">
                                                Cari Departemen
                                            </option>
                                            <?php foreach ($divisi as $d) : ?>
                                                <option value="<?= $d['id'] ?>">
                                                    <?= $d['divisi']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput">Cari Departemen</label>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="employeeID" name="filterEmployeeID" aria-label="Floating label select example">
                                            <option value="">
                                                Cari Berdasarkan Nama Karyawan
                                            </option>
                                        </select>
                                        <label for="floatingInput">Cari Berdasarkan Nama Karyawan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="<?= $startDate ?>" autocomplete="one-time-code" name="startDatePersonal" type="text" required class="form-control target input-picker startDate">
                                        <label for="floatingInput">Mulai</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mt-3">
                                        <input value="<?= $endDate ?>" autocomplete="one-time-code" name="finishDatePersonal" type="text" required class="form-control target input-picker endDate">
                                        <label for="floatingInput">Selesai</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-submit-form" id="singleGenerateBtn">Generate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Payroll</h1>
        <div class="col-button-tambah-spp">
            <?= csrf_field() ?>
            <a id="generate" class="btn btn-hide-form btn-discard float-right" data-bs-toggle="modal" data-bs-target="#generateModal" href="#" style="margin-right: 10px;">
                Generate
            </a>
            <?php if ($isGenerate) : ?>
                <button class="btn btn-warning btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-print"></i> Print
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><button class="dropdown-item" onclick="printWithDivision('<?= base_url('payroll/print/division/' . $year . '-' . $month) ?>')">Daftar Upah</button></li>
                    <li><button class="dropdown-item" onclick="printWithDivision('<?= base_url('payroll/print/detail/' . $year . '-' . $month) ?>')">Slip Gaji</button></li>
                    <li><button class="dropdown-item" onclick="printWithDivision('<?= base_url('payroll/print/summary/' . $year . '-' . $month) ?>')">Summary</button></li>
                    <li><button class="dropdown-item" onclick="printWithDivision('<?= base_url('payroll/print/potongan/' . $year . '-' . $month) ?>')">Daftar Potongan</button></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-0">
                    <form action="<?= base_url('payroll') ?>" class="kt-form kt-form--fit kt-margin-b-20" method="GET">
                        <select name="month" required id="month">
                            <?php for ($i = 1; $i <= 12; $i++) : ?>
                                <?php
                                $temp = (strlen($i) == 1) ? ("0" . $i) : $i;
                                $checked = ($month == $temp) ? "selected" : "";
                                ?>
                                <option value="<?= $temp; ?>" <?= $checked; ?>>
                                    <?= $temp; ?>
                                </option>
                            <?php endfor ?>
                        </select>
                        <select name="year" required id="year">
                            <?php
                            for ($i = date("Y") - 2; $i <= date("Y") + 2; $i++) :
                                $checked = ($year == $i) ? "selected" : "";
                            ?>
                                <option value="<?= $i; ?>" <?= $checked; ?>><?= $i; ?></option>
                            <?php endfor ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-brand--icon" id="kt_search" onclick="printReport();">
                            <span>
                                <i class="la la-print"></i>
                                <span>Cari</span>
                            </span>
                        </button>
                    </form>
                </div>
                <div class="col-6 mb-0">
                    <div class="clearfix" id="loadingSpinner">
                        <div class="spinner-border text-primary float-right" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-start mb-3">
                <div class="col-md-4">
                    <div class="form-floating mt-1">
                        <select class="form-select" name="filterDivisiID" id="filterDivisiID" aria-label="Floating label select example">
                            <option value="">
                                Cari Departemen
                            </option>
                            <?php foreach ($divisi as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['divisi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Departemen</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-floating">
                        <select class="form-select" name="filterGolongan" aria-label="Floating label select example">
                            <option value="">
                                Cari Tipe / Golongan
                            </option>
                            <?php foreach ($golongan as $g) : ?>
                                <option <?= @$_GET['golongan'] == $g['golongan_name'] ? "selected" : "" ?> value="<?= $g['golongan_name'] ?>">
                                    <?= $g['golongan_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Tipe / Golongan</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mt-1">
                        <select class="form-select" name="filterEmployeeID" id="filterEmployeeID" aria-label="Floating label select example">
                            <option value="">
                                Cari Berdasarkan Nama Karyawan
                            </option>
                        </select>
                        <label for="floatingInput">Cari Berdasarkan Nama Karyawan</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('employees.name')" class="sort">Nama Lengkap</th>
                                <th onclick="changeSort('divisis.divisi')" class="sort">Departemen</th>
                                <th onclick="changeSort('employees.nip')" class="sort">Bagian</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Hari Kerja</th>
                                <th>Gaji Bersih</th>
                                <th>Total Gaji & Lembur</th>
                                <th>Total Pengurangan Gaji</th>
                                <th>Gaji Diterima</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "nomor";
    let sortType = "desc";
    $('#loadingSpinner').hide();

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("payroll/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.divisi_id = $("#filterDivisiID").val();
                data.employee_id = $("#filterEmployeeID").val();
                data.golongan = $("select[name='filterGolongan']").val();
                data.year = "<?= $year ?>";
                data.month = "<?= $month; ?>";
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            },
            {
                data: "name",
                className: "text-center"
            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "namaBagian",
                className: "text-center",
                width: "10%"
            },
            {
                data: "startDate",
                className: "text-center"
            },
            {
                data: "endDate",
                className: "text-center"
            },
            {
                data: "hariKerja",
                className: "text-center"
            },
            {
                data: "upahBersih",
                className: "text-center"
            },
            {
                data: "totalGajiLembur",
                className: "text-center"
            },
            {
                data: "totalPenguranganGaji",
                className: "text-center"
            },
            {
                data: "sisaGaji",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let employee_id = row.employee_id;
                    let id = row.id;
                    return `
                        <div class="mt-0">
                            <button class="btn btn-warning btn-print" onclick="print('<?= base_url("payroll/print/single/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                    `
                }
            }
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Data payroll bulan ini belum digenerate", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $("#filterDivisiID").change(function() {
        table.ajax.reload();
    });
    $("#filterEmployeeID").change(function() {
        table.ajax.reload();
    });

    // Generate Modal Show
    $('#generate').click(function(e) {
        e.preventDefault();
        $('#generateModal').modal('show');
    });

    $("select[name='filterGolongan']").select2({
        placeholder: "Cari Tipe/Golongan Pegawai",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $("select[name='filterGolongan']").change(function() {
        table.ajax.reload();
    });

    // if on change divisi
    $('#employeeID').attr('disabled', true);
    $("#divisionID").on('change', function() {
        $("#employeeID").empty();
        if ($(this).val() == "") {
            $('#employeeID').attr('disabled', true);
        } else {
            $('#employeeID').attr('disabled', false);
        }
    });

    // Generate Global
    $('#globalGenerateBtn').click(function(e) {
        e.preventDefault();
        // set variable
        const csrf = $(`[name="${csrfToken}"]`);
        var monthYearGlobal = $("input[name='monthYearGlobal']").val();
        var startDateGlobal = $("input[name='startDateGlobal']").val();
        var finishDateGlobal = $("input[name='finishDateGlobal']").val();
        var divisionGlobalID = $("select[name='divisionGlobalID']").val();

        if (monthYearGlobal == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih periode payroll",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else if (startDateGlobal == '') {
            Swal.fire({
                icon: 'error',
                title: "Tanggal mulai tidak boleh kosong",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else if (finishDateGlobal == '') {
            Swal.fire({
                icon: 'error',
                title: "Tanggal selesai tidak boleh kosong",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else if (divisionGlobalID == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih departemen dahulu",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else {
            Swal.fire({
                icon: 'question',
                title: 'Generate Global Payroll (Data payroll pegawai periode ini akan di reset) ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append('yearMonth', monthYearGlobal);
                    formData.append('startDate', startDateGlobal);
                    formData.append('finishDate', finishDateGlobal);
                    formData.append('divisionGlobalID', divisionGlobalID);

                    $.ajax({
                        url: "<?= base_url("payroll/generate-global"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            // show loading
                            $('#loadingSpinner').show();
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then((result) => {
                                    // update table
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                            $('#loadingSpinner').hide();
                            $('#generateModal').modal('hide');
                            location.reload();
                        },
                        onError: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan pada sistem',
                                confirmButtonColor: '#4e73df',
                            });
                            $('#loadingSpinner').hide();
                        }
                    });
                }

            });
        }
    });

    // Generate Single
    $('#singleGenerateBtn').click(function(e) {
        e.preventDefault();
        // set variable
        const csrf = $(`[name="${csrfToken}"]`);
        var monthYearPersonal = $("input[name='monthYearPersonal']").val();
        var startDatePersonal = $("input[name='startDatePersonal']").val();
        var finishDatePersonal = $("input[name='finishDatePersonal']").val();
        var employeeID = $('#employeeID').val();
        var divisionID = $('#divisionID').val();
        if (monthYearPersonal == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih periode absensi",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else if (divisionID == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih departemen",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else if (employeeID == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih karyawan",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else if (startDatePersonal == '') {
            Swal.fire({
                icon: 'error',
                title: "Tanggal mulai tidak boleh kosong",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else if (finishDatePersonal == '') {
            Swal.fire({
                icon: 'error',
                title: "Tanggal selesai tidak boleh kosong",
                confirmButtonColor: '#4e73df',
            }).then(() => {});
        } else {
            Swal.fire({
                icon: 'question',
                title: 'Generate Personal Payroll (Data payroll pegawai ini akan direset) ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                // append to form
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append('yearMonth', monthYearPersonal);
                    formData.append('startDate', startDatePersonal);
                    formData.append('finishDate', finishDatePersonal);
                    formData.append('employeeID', employeeID);

                    $.ajax({
                        url: "<?= base_url("payroll/generate-single"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            // show loading
                            $('#loadingSpinner').show();
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then((result) => {
                                    // update table
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                            $('#loadingSpinner').hide();
                            $('#generateModal').modal('hide');
                        },
                        onError: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan pada sistem',
                                confirmButtonColor: '#4e73df',
                            });
                            $('#loadingSpinner').hide();
                        }
                    });
                }

            });
        }
    });

    // Get and Show
    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("payroll/id"); ?>/${data.id}`);
    });
    // function helper
    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join('.').split('').reverse().join('');
        return 'Rp. ' + formatted;
    }

    // select2 divisi
    $("select[name='filterDivisiID'], #divisionGlobalID").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("select[name='filterEmployeeID']").select2({
        placeholder: "Cari Berdasarkan Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("select[name='filterDivisiID']").on('change', function(e) {
        e.preventDefault();
        const csrf = $(`[name="${csrfToken}"]`);
        var divisionID = $(this).val();

        var formData = new FormData();
        formData.append('divisionID', divisionID);

        $.ajax({
            url: "<?= base_url("payroll/employees"); ?>",
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
                var employeeSelect = $("select[name='filterEmployeeID']");
                employeeSelect.empty();
                employeeSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Silahkan pilih karyawan dahulu"));
                $.each(response.data, function(index, data) {
                    var option = $("<option></option>")
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

    $("input[name='startDatePersonal'], input[name='startDateGlobal']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });


    $("input[name='finishDatePersonal'], input[name='finishDateGlobal']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });


    const printWithDivision = function(url) {
        var divisionID = $("#filterDivisiID").val();
        if (divisionID == "") {
            Swal.fire({
                icon: 'error',
                title: 'Pilih Departemen',
                confirmButtonColor: '#4e73df',
            });
        } else {
            window.open(url + '/' + divisionID, "_blank");
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }
</script>

<?= $this->endSection(); ?>