<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
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
                                    <div class="input-group mb-3 mt-3">
                                        <div class="form-floating">
                                            <input value="" placeholder="Pilih Periode Absensi" name="monthYearGlobal" id="monthYearGlobal" type="text" required class="form-control target">
                                            <label>Periode Absensi</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <select class="form-select" id="divisionGlobalID" name="divisionGlobalID">
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
                                    <div class="input-group mb-3 mt-3">
                                        <div class="form-floating">
                                            <input name="startDateGlobal" type="text" required class="form-control target input-picker startDate" placeholder="Tanggal Mulai Absensi">
                                            <label for="floatingInput">Tanggal Mulai Absensi</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-3 mt-3">
                                        <div class="form-floating">
                                            <input name="finishDateGlobal" type="text" required class="form-control target input-picker endDate" placeholder="Tanggal Selesai Absensi">
                                            <label for="floatingInput">Tanggal Selesai Absensi</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
                                <button type="submit" class="btn btn-submit-form" id="globalGenerateBtn">Generate</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="single" role="tabpanel" aria-labelledby="profile-tab">
                        <form id="formGeneratePersonal">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="input-group mb-2 mt-2">
                                        <div class="form-floating">
                                            <input placeholder="Periode Absensi" value="" name="monthYearPersonal" id="monthYearPersonal" type="text" required class="form-control target">
                                            <label>Periode Absensi</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating mb-2 mt-2">
                                        <select class="form-select" id="divisionID" name="divisionID">
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
                                <div class="col-md-12">
                                    <div class="form-floating mb-2 mt-2">
                                        <select class="form-select" id="bagianSingleID" name="bagianSingleID">
                                            <option value="">
                                                Cari Bagian
                                            </option>
                                        </select>
                                        <label for="floatingInput">Cari Bagian</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating mb-2 mt-2">
                                        <select class="form-select" id="employeeID">
                                            <option value="">
                                                Cari Karyawan
                                            </option>
                                        </select>
                                        <label for="floatingInput">Cari Karyawan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-2 mt-2">
                                        <div class="form-floating">
                                            <input name="startDatePersonal" type="text" required class="form-control target input-picker startDate" placeholder="Tanggal Mulai Absensi">
                                            <label for="floatingInput">Tanggal Mulai Absensi</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-2 mt-2">
                                        <div class="form-floating">
                                            <input name="finishDatePersonal" type="text" required class="form-control target input-picker endDate" placeholder="Tanggal Selesai Absensi">
                                            <label for="floatingInput">Tanggal Selesai Absensi</label>
                                        </div>
                                        <div class="input-group-append" style="height:50px;">
                                            <button disabled class="btn btn-secondary" type="button">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
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
        <h1>List Payroll</h1>
        <div class="col-button-tambah-spp">
            <?= csrf_field() ?>
            <?php if (can('Personalia', 'Payroll', 'c')): ?>
                <a id="generate" class="btn btn-hide-form btn-discard float-right" data-bs-toggle="modal" data-bs-target="#generateModal" href="#" style="margin-right: 10px;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Generate
                </a>
            <?php endif; ?>
            <?php if (can('Personalia', 'Payroll', 'p')): ?>
                <button class="btn btn-warning btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-download"></i> Export
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><button class="dropdown-item" onclick="printWithDivision('<?= base_url('payroll/print/division') ?>')">Daftar Upah</button></li>
                    <li><button class="dropdown-item" onclick="printWithDivision('<?= base_url('payroll/print/detail') ?>')">Slip Gaji</button></li>
                    <li><button class="dropdown-item" onclick="printWithDivision('<?= base_url('payroll/print/summary') ?>')">Summary</button></li>
                    <li><button class="dropdown-item" onclick="printWithDivision('<?= base_url('payroll/print/potongan') ?>')">Daftar Potongan</button></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-start mb-3">
                <div class="col-sm-2 mt-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" value="<?= date('Y-m') ?>" class="form-control month" id="month" name="month" />
                            <label style="z-index: 1;" style="z-index: 1;">Pilih Bulan</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-floating mt-3">
                        <select class="form-select" name="filterDivisiID" id="filterDivisiID">
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
                <div class="col-sm-2">
                    <div class="form-floating mt-3">
                        <select class="form-select" name="filterBagianID" id="filterBagianID">
                            <option value="">
                                Cari Bagian
                            </option>
                        </select>
                        <label for="floatingInput">Cari Bagian</label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-floating mt-3">
                        <select class="form-select" name="filterGolongan">
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
                <div class="col-sm-3">
                    <div class="form-floating mt-3">
                        <select class="form-select" name="filterEmployeeID" id="filterEmployeeID">
                            <option value="">
                                Cari Karyawan
                            </option>
                        </select>
                        <label for="floatingInput">Cari Karyawan</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('employees.nip')" class="sort">Nip</th>
                                <th onclick="changeSort('employees.name')" class="sort">Karyawan</th>
                                <th onclick="changeSort('divisis.divisi')" class="sort">Dept</th>
                                <th onclick="changeSort('employees.bagian_id')" class="sort">Bagian</th>
                                <th onclick="changeSort('payrolls.start_date')">Mulai</th>
                                <th onclick="changeSort('payrolls.end_date')">Selesai</th>
                                <th onclick="changeSort('payrolls.hadir_final')">Hari Kerja</th>
                                <th onclick="changeSort('payrolls.nominal_uang_gaji')">Gaji Bersih</th>
                                <th onclick="changeSort('payrolls.nominal_uang_lembur')">Total Lembur</th>
                                <th onclick="changeSort('payrolls.nominal_pengurangan_gaji')">Total Pengurangan Gaji</th>
                                <th onclick="changeSort('payrolls.nominal_gaji_diterima')">Gaji Diterima (THP)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "employees.nip";
    let sortType = "asc";

    const table = $('.dataTable').DataTable({

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
                data.bagian_id = $("#filterBagianID").val();
                data.golongan = $("select[name='filterGolongan']").val();
                data.month = $('#month').val();
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
                width: "3%"
            },
            {
                data: "nip",
                className: "text-left"
            },
            {
                data: "name",
                className: "text-left"
            },
            {
                data: "divisi",
                className: "text-left"
            },
            {
                data: "namaBagian",
                className: "text-left",
                width: "10%"
            },
            {
                data: "startDate",
                className: "text-left"
            },
            {
                data: "endDate",
                className: "text-left"
            },
            {
                data: "hariKerja",
                className: "text-left"
            },
            {
                data: "upahBersih",
                className: "text-left",
                render: function(data, type, row) {
                    return greatFormatRupiah(data); // Format kolom upahBersih
                }
            },
            {
                data: "totalLembur",
                className: "text-left",
                render: function(data, type, row) {
                    return greatFormatRupiah(data); // Format kolom totalGajiLembur
                }
            },
            {
                data: "totalPenguranganGaji",
                className: "text-left",
                render: function(data, type, row) {
                    return greatFormatRupiah(data); // Format kolom totalPenguranganGaji
                }
            },
            {
                data: "sisaGaji",
                className: "text-left",
                render: function(data, type, row) {
                    return greatFormatRupiah(data); // Format kolom sisaGaji
                }
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let employee_id = row.employee_id;
                    let id = row.id;
                    let res = '';

                    res += `
                        <?php if (can('Personalia', 'Payroll', 'u')): ?>
                            <a href='<?= base_url("payroll/id") ?>/${id}' data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php endif ?>
                        <?php if (can('Personalia', 'Payroll', 'p')): ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("payroll/print/single/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif ?>
                    `;

                    return res;
                }
            }
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
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

    $("#filterDivisiID,#filterEmployeeID,#filterBagianID,#month").change(function() {
        table.ajax.reload();
    });

    // Generate Modal Show
    $('#generate').click(function(e) {
        e.preventDefault();
        $('#generateModal').modal('show');
    });

    $('#divisionID').change(function(e) {
        e.preventDefault();
        dropdownBagianSingle();
    });

    $('#bagianSingleID').change(function(e) {
        e.preventDefault();
        dropdownKaryawanSinglePayroll();
    });

    $("#month,#monthYearGlobal,#monthYearPersonal").datepicker({
        format: "yyyy-mm",
        startView: "months", // langsung tampilin bulan
        minViewMode: "months", // cuma bisa pilih bulan
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    });

    $("select[name='filterGolongan']").select2({
        placeholder: "Cari Tipe/Golongan Pegawai",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("#employeeID").select2({
        placeholder: "Cari Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#generateModal')
    });
    $("#divisionID").select2({
        placeholder: "Cari Berdasarkan Departemen",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#generateModal')
    });

    $("#bagianGlobalID").select2({
        placeholder: "Cari Bagian",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#generateModal')
    });

    $("#bagianSingleID").select2({
        placeholder: "Cari Bagian",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#generateModal')
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
                cancelButtonText: 'Kembali',
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
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
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
                            table.ajax.reload();
                        },

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
                cancelButtonText: 'Kembali',
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
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
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

                    });
                }

            });
        }
    });

    function print(url) {
        window.open(url, "_blank");
    }

    // select2 divisi
    $("#divisionGlobalID").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#generateModal')
    });

    $("#filterDivisiID").select2({
        placeholder: "Cari Department",
        theme: "bootstrap-5",
        allowClear: true,
    })
    $("#filterBagianID").select2({
        placeholder: "Cari Bagian",
        theme: "bootstrap-5",
        allowClear: true,
    })

    $("#filterEmployeeID").select2({
        placeholder: "Cari Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "<?= base_url('payroll/like-employees') ?>",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    employeesName: params.term,
                };
            },
            processResults: function(data) {
                var options = [];
                $.each(data.data, function(index, employee) {
                    options.push({
                        id: employee.id,
                        text: employee.name
                    });
                });
                return {
                    results: options
                };
            },
            cache: true
        }
    });

    function dropdownKaryawanSinglePayroll() {
        const csrf = $(`[name="${csrfToken}"]`);
        var bagianId = $('#bagianSingleID').val();

        var formData = new FormData();
        formData.append('bagianId', bagianId);

        $.ajax({
            url: "<?= base_url("payroll/employees-by-bagian"); ?>",
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
                var employeeSelect = $("#employeeID");
                employeeSelect.empty();
                employeeSelect.append($("<option></option>")
                    .attr("value", "")
                    .text("Silahkan pilih karyawan dahulu"));
                $.each(response.data, function(index, data) {
                    var option = $("<option></option>")
                        .attr("value", data.id)
                        .text("(" + data.nip + ") " + data.name);
                    employeeSelect.append(option);
                });

            },
            onError: function(response) {
                csrf.val(response.token);

            }
        });
    }

    $('#filterDivisiID').change(function() {
        var divisi = $('#filterDivisiID option:selected').val();
        $.ajax({
            url: `<?= base_url('/payroll/getBagian'); ?>`,
            method: "GET",
            data: {
                divisi: divisi,
            },
            dataType: "json",
            success: function(res) {
                // APPEND TO DROPDOWN
                console.log(res);
                appendDropdownBagian(res.data);
            }
        });
    });

    function appendDropdownBagian(data) {
        $("#filterBagianID").empty()
        $("#filterBagianID").append(`<option value=""></option>`)
        data.forEach(function(item) {
            $("#filterBagianID").append(`<option value="${item.id}">${item.nama_bagian}</option>`)
        })
    }

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

    function dropdownBagianGlobal() {
        var divisiGlobalId = $('#divisionGlobalID').val();
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', divisiGlobalId);
        $.ajax({
            url: `<?= base_url("list-attendance/get-bagian"); ?>`,
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(result) {
                csrf.val(result.token);
                $("#bagianGlobalID").empty()
                $("#bagianGlobalID").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("#bagianGlobalID").append(`<option value="${item.id}">${item.kode_bagian.toUpperCase()} - ${item.nama_bagian.toUpperCase()}</option>`)
                });
            }
        });

    }

    function dropdownBagianSingle() {
        var divisionID = $('#divisionID').val();
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', divisionID);
        $.ajax({
            url: `<?= base_url("list-attendance/get-bagian"); ?>`,
            data: formData,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            method: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(result) {
                csrf.val(result.token);
                $("#bagianSingleID").empty()
                $("#bagianSingleID").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("#bagianSingleID").append(`<option value="${item.id}">${item.kode_bagian.toUpperCase()} - ${item.nama_bagian.toUpperCase()}</option>`)
                });
            }
        });

    }

    const printWithDivision = function(url) {
        var divisionID = $("#filterDivisiID").val();
        var month = $('#month').val();
        var bagianId = $('#filterBagianID').val();
        if (month == "") {
            Swal.fire({
                icon: 'error',
                title: 'Pilih Bulan',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else if (divisionID == "") {
            Swal.fire({
                icon: 'error',
                title: 'Pilih Departemen',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            var newUrl = url + '?divisi_id=' + divisionID + '&month=' + month + '&bagian_id=' + bagianId;
            window.open(newUrl, "_blank");
        }
    }
</script>

<?= $this->endSection(); ?>