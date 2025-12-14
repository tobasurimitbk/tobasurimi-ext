<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List Pinjaman Karyawan</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" data-bs-toggle="modal" id="generateModalBtn" data-bs-target="#generateModal" href="#">
                <i class="fa-solid fa-clock-rotate-left"></i> Generate
            </a>
            <?php if (can('Personalia', 'Pinjaman Karyawan', 'p')): ?>
                <button class="btn btn-warning btn-print float-right" onclick="exportPinjaman()">
                    <i class="fa fa-download"></i> Export
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-start mb-3">
                <div class="col-sm-2">
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
                    <div class="form-floating">
                        <select class="form-select" name="filterDivisiID">
                            <option value="" selected></option>
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
                    <div class="form-floating">
                        <select class="form-select filterBagianID" name="filterBagianID" id="filterBagianID">
                            <option value="" selected></option>
                        </select>
                        <label for="floatingInput">Cari Bagian</label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-floating">
                        <select class="form-select" name="filterStatusPinjaman" id="filterStatusPinjaman">
                            <option value="ALL" selected>ALL</option>
                            <option value="AMBIL">AMBIL</option>
                            <option value="TIDAK AMBIL">TIDAK AMBIL</option>
                        </select>
                        <label for="floatingInput">Status Pinjaman</label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-floating">
                        <select class="form-select" name="filterGolongan">
                            <option value="" selected></option>
                            <?php foreach ($golongan as $g) : ?>
                                <option value="<?= $g['golongan_name'] ?>">
                                    <?= $g['golongan_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Tipe / Golongan</label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-floating">
                        <select class="form-select" name="filterEmployeeID">
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
                                <th style="width: 10px;">No</th>
                                <th onclick="changeSort('employees.nip')" class="sort">Nip</th>
                                <th onclick="changeSort('employees.name')" class="sort">Karyawan</th>
                                <th onclick="changeSort('employees.tipe')" class="sort">Tipe/Gol</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Dept</th>
                                <th onclick="changeSort('pinjaman_karyawan.tanggal_ambil')" class="sort">Tgl Ambil</th>
                                <th onclick="changeSort('pinjaman_karyawan.start_date')">Range Absen</th>
                                <th onclick="changeSort('pinjaman_karyawan.hadir')">Hadir</th>
                                <th onclick="changeSort('pinjaman_karyawan.tidak_hadir')">Tidak Hadir</th>
                                <th>Nominal</th>
                                <th>Status Pinjaman</th>
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
<div class="modal fade" id="generateModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Generate Pinjaman Karyawan</h5>
            </div>
            <form id="formGeneratePinjaman" class="create-form" role="form" method="POST">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <input name="monthYear" id="monthYear" type="text" required class="form-control target input-picker" placeholder="Periode Pinjaman">
                                    <label>Periode Pinjaman</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group">
                                <div class="form-floating">
                                    <input name="tanggalAmbil_Global" id="tanggalAmbil_Global" type="text" required class="form-control target input-picker" placeholder="Tanggal Ambil Pinjaman">
                                    <label for="floatingInput">Tanggal Ambil Pinjaman</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <select class="form-select" name="divisiId_generate" id="divisiId_generate">
                                    <option value="" selected></option>
                                    <?php foreach ($divisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['divisi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Departemen</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <select class="form-select bagianId_generate" name="bagianId_generate" id="bagianId_generate">
                                    <option value="" selected></option>
                                </select>
                                <label for="floatingInput">Pilih Bagian</label>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="form-floating">
                                <select class="form-select" name="golongan_generate" id="golongan_generate">
                                    <option value="" selected></option>
                                    <?php foreach ($golongan as $g) : ?>
                                        <option value="<?= $g['golongan_name'] ?>">
                                            <?= $g['golongan_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Cari Tipe / Golongan</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group">
                                <div class="form-floating">
                                    <input name="startDate" id="startDate" type="text" required class="form-control target input-picker" placeholder="Tanggal Mulai Absensi">
                                    <label for="floatingInput">Tanggal Mulai Absensi</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group">
                                <div class="form-floating">
                                    <input name="finishDate" id="finishDate" type="text" required class="form-control target input-picker" placeholder="Tanggal Selesai Absensi">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-submit-form" id="generateGlobal">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="generateSingleModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Generate Ulang Pinjaman Per Karyawan</h5>
            </div>
            <form id="formGeneratePinjamanSingle" role="form" method="POST">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="employeeID" id="employeeID">
                    <input type="hidden" name="id" id="id">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input readonly id="employeeName" type="text" required class="form-control target input-picker">
                                <label>Karyawan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating ">
                                <input readonly id="tipeGol" type="text" required class="form-control target input-picker">
                                <label>Tipe/Gol</label>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <input name="monthYearSingle" id="monthYearSingle" type="text" required class="form-control target input-picker" placeholder="Periode Pinjaman">
                                    <label>Periode Pinjaman</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <input name="tanggalAmbil_Personal" id="tanggalAmbil_Personal" type="text" required class="form-control target input-picker" placeholder="Tanggal Ambil Pinjaman">
                                    <label for="floatingInput">Tanggal Ambil Pinjaman</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <input name="startDate" type="text" id="startDate" required class="form-control target input-picker" placeholder="Tanggal Mulai Log Absensi">
                                    <label for="floatingInput">Tanggal Mulai Log Absensi</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <input name="finishDate" type="text" id="finishDate" required class="form-control target input-picker" placeholder="Tanggal Selesai Log Absensi">
                                    <label for="floatingInput">Tanggal Selesai Log Absensi</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-submit-form" id="generateUlang">Generate Ulang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updatePinjamanModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Pinjaman</h5>
            </div>
            <form id="formUpdatePinjaman" role="form" method="POST">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input readonly id="employeeName_statusPinjaman" type="text" required class="form-control target input-picker">
                                <label>Karyawan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating ">
                                <input readonly id="tipeGol_statusPinjaman" type="text" required class="form-control target input-picker">
                                <label>Tipe/Gol</label>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="input-group">
                                <div class="form-floating">
                                    <input readonly name="monthYear_StatusPinjaman" id="monthYear_StatusPinjaman" type="text" required class="form-control target input-picker" placeholder="Periode Pinjaman">
                                    <label>Periode Pinjaman</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="form-floating">
                                <select class="form-select" name="statusPinjaman" id="statusPinjaman">
                                    <option value="" selected></option>
                                    <option value="1">DIAMBIL</option>
                                    <option value="0">TIDAK DIAMBIL</option>
                                </select>
                                <label for="floatingInput">Status Pinjaman</label>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="form-floating">
                                <input id="nominal" type="text" class="form-control nominal" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label>Nominal Pinjaman</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" id="btnHidePinjaman" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-submit-form" id="btnUpdatePinjaman">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let sort = "employees.nip";
    let sortType = "asc";
    const csrfToken = '<?= csrf_token() ?>';

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
            url: "<?= base_url("pinjaman-karyawan/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.divisi_id = $("select[name='filterDivisiID']").val();
                data.employee_id = $("select[name='filterEmployeeID']").val();
                data.tipe = $("select[name='filterGolongan']").val();
                data.bagian_id = $("select[name='filterBagianID']").val();
                data.status_pinjaman = $("select[name='filterStatusPinjaman']").val();
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
        columns: [

            {
                data: "no",
                className: "text-left",
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
                data: "tipeGol",
                className: "text-left",
                width: "10%"
            },
            {
                data: "divisi",
                className: "text-left"
            },
            {
                data: "tanggal_ambil",
                className: "text-left"
            },
            {
                data: "mulaiAbsen",
                className: "text-left",
                className: "text-left",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let startDate = row.mulaiAbsen;
                    let endDate = row.selesaiAbsen;
                    return startDate + ' - ' + endDate;
                }
            },
            {
                data: "hadir",
                className: "text-left"
            },
            {
                data: "tidakHadir",
                className: "text-left"
            },
            {
                data: "nominalPinjaman",
                className: "text-left",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let nominalPinjaman = row.nominalPinjaman;
                    return greatFormatRupiah(nominalPinjaman);
                }
            },

            {
                data: "statusPinjaman",
                className: "text-left",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let is_boleh_minjam = row.isBolehMinjam;
                    let is_ambil = row.isAmbil;
                    let htmlRes = '';

                    if (is_boleh_minjam == 0) {
                        htmlRes += `
                            <div class="text-danger">
                                Tidak Diizinkan
                            </div>`
                    } else {
                        if (is_ambil == 0) {
                            htmlRes += `
                            <div class="text-warning">
                                Tidak Diambil
                            </div>`
                        } else {
                            htmlRes += `
                            <div class="text-success">
                                Diambil
                            </div>`
                        }
                    }

                    return htmlRes;
                }
            },

            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                orderable: false, // di DataTables namanya 'orderable', bukan 'sortable'
                render: function(data, type, row) {
                    let employeeID = row.employeeID;
                    let employeeName = row.name;
                    let yearMonth = row.monthYear;
                    let startDate = row.mulaiAbsen;
                    let finishDate = row.selesaiAbsen;
                    let tipeGol = row.tipeGol;
                    let is_boleh_minjam = row.isBolehMinjam;
                    let id = row.id;
                    let nominal = row.nominalPinjaman;
                    let statusPinjaman = row.statusPinjaman;

                    // escape nama biar aman saat ada tanda kutip
                    let safeEmployeeName = employeeName.replace(/'/g, "\\'");

                    let generateBtn = `
                        <button data-toggle="tooltip" title="Generate Ulang"
                            onclick="generateSingle(${employeeID}, '${safeEmployeeName}', '${yearMonth}', '${startDate}', '${finishDate}', '${tipeGol}', ${data})"
                            class="btn btn-success posting-spp">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>
                    `;

                    let updateBtn = `
                        <button data-toggle="tooltip" title="Update"
                            onclick="updateStatusPinjaman('${safeEmployeeName}', '${yearMonth}', '${tipeGol}', '${nominal}', '${statusPinjaman}', '${id}')"
                            class="btn btn-primary posting-spp">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    `;

                    let deleteBtn = `
                        <button data-toggle="tooltip" title="Hapus" onclick="destroy('${id}')" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `;

                    if (is_boleh_minjam == 1) {
                        return `<div class="mt-0">${generateBtn} ${updateBtn} ${deleteBtn}</div>`;
                    } else {
                        return `<div class="mt-0">${generateBtn} ${deleteBtn}</div>`;
                    }
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
            emptyTable: "Tidak ada data pinjaman", // Change this line
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });
    // select2 divisi
    $("select[name='filterDivisiID']").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    }).change(function(e) {
        e.preventDefault();
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', $(this).val());
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
                $("#filterBagianID").empty()
                $("#filterBagianID").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("#filterBagianID").append(`<option value="${item.id}">${item.kode_bagian.toUpperCase()} - ${item.nama_bagian.toUpperCase()}</option>`)
                });
            }
        });
    });

    $("select[name='filterEmployeeID']").select2({
        placeholder: "Cari Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "<?= base_url('pinjaman-karyawan/like-employees') ?>",
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

    $("select[name='filterGolongan']").select2({
        placeholder: "Cari Tipe/Golongan",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("select[name='filterStatusPinjaman']").select2({
        placeholder: "Cari Status Pinjaman",
        theme: "bootstrap-5",
        allowClear: false,
    });

    $("#statusPinjaman").select2({
        placeholder: "Pilih Status Pinjaman",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#updatePinjamanModal')
    });

    $("#divisiId_generate").select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#generateModal')
    }).change(function(e) {
        e.preventDefault();
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', $(this).val());
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
                $("#bagianId_generate").empty()
                $("#bagianId_generate").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("#bagianId_generate").append(`<option value="${item.id}">${item.kode_bagian.toUpperCase()} - ${item.nama_bagian.toUpperCase()}</option>`)
                });
            }
        });

    });

    $("#bagianId_generate").select2({
        placeholder: "Pilih Bagian",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#generateModal')
    });

    $("#golongan_generate").select2({
        placeholder: "Pilih Golongan (Opsional)",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#generateModal')
    });

    $("#filterBagianID").select2({
        placeholder: "Pilih Bagian",
        theme: "bootstrap-5",
        allowClear: true,
    }).change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $("#startDate,#finishDate,#tanggalAmbil_Global,#tanggalAmbil_Personal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("#monthYear,#month,#monthYearSingle").datepicker({
        format: "yyyy-mm",
        startView: "months", // langsung tampilin bulan
        minViewMode: "months", // cuma bisa pilih bulan
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
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

    const generateSingle = function(employeeID, employeeName, yearMonth, startDate, finishDate, tipeGol, id) {
        $('#tipeGol').val(tipeGol);
        $('#employeeName').val(employeeName);
        $('#monthYear').val(yearMonth);
        $('#startDate').val(startDate);
        $('#finishDate').val(finishDate);
        $('#employeeID').val(employeeID);
        $('#id').val(id);
        $('#generateSingleModal').modal('show');
    }

    const updateStatusPinjaman = function(employeeName, yearMonth, tipeGol, nominal, statusPinjaman, id) {
        $('#tipeGol_statusPinjaman').val(tipeGol);
        $('#employeeName_statusPinjaman').val(employeeName);
        $('#monthYear_StatusPinjaman').val(yearMonth);
        $('#id').val(id);
        $('#nominal').val(greatFormatRupiah(nominal));
        $('#statusPinjaman').val(statusPinjaman).change();
        $('#updatePinjamanModal').modal('show');
    }


    $(document).ready(function() {
        var validatorGeneratePinjaman = $(".create-form").validate({
            rules: {
                monthYear: {
                    required: true
                },
                startDate: {
                    required: true
                },
                finishDate: {
                    required: true
                },
                tanggalAmbil_Global: {
                    required: true
                },
                divisiId_generate: {
                    required: true
                },
                bagianId_generate: {
                    required: true
                },
            },
            messages: {
                monthYear: {
                    required: "Pilih periode pinjaman"
                },
                startDate: {
                    required: "Tanggal Mulai Wajib Diisi"
                },
                finishDate: {
                    required: "Tanggal Selesai Wajib Diisi"
                },
                tanggalAmbil_Global: {
                    required: "Tanggal Ambil Wajib Diisi"
                },
                divisiId_generate: {
                    required: "Departemen Wajib Diisi"
                },
                bagianId_generate: {
                    required: "Bagian Wajib Diisi"
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
        var validatorGenerateSingle = $("#formGeneratePinjamanSingle").validate({
            rules: {
                monthYearSingle: {
                    required: true
                },
                startDate: {
                    required: true
                },
                finishDate: {
                    required: true
                },
                tanggalAmbil_Personal: {
                    required: true
                }
            },
            messages: {
                monthYearSingle: {
                    required: "Pilih periode pinjaman"
                },
                startDate: {
                    required: "Tanggal Mulai Wajib Diisi"
                },
                finishDate: {
                    required: "Tanggal Selesai Wajib Diisi"
                },
                tanggalAmbil_Personal: {
                    required: "Tanggal Ambil Wajib Diisi"
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

        var validatorUpdateStatusPinjaman = $("#formUpdatePinjaman").validate({
            rules: {
                statusPinjaman: {
                    required: true
                },
                nominal: {
                    required: true
                },
            },
            messages: {
                statusPinjaman: {
                    required: "Pilih Status Pinjaman"
                },
                nominal: {
                    required: "Nominal wajib diisi"
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

        $('#btnHidePinjaman').click(function(e) {
            e.preventDefault();
            $('#updatePinjamanModal').modal('hide');
        });

        $('#generateModalBtn').click(function(e) {
            e.preventDefault();
            $('#divisiId_generate').val(null).change();
            $('#golongan_generate').val(null).change();
            $('#bagianId_generate').val(null).change();
        })

        // generate pinjaman action
        $('#generateGlobal').click(function(e) {
            e.preventDefault();
            if ($("#formGeneratePinjaman").valid()) {
                let csrf = $(`[name="${csrfToken}"]`);
                let data = new FormData(document.getElementById("formGeneratePinjaman"));
                $.ajax({
                    url: "<?= base_url("pinjaman-karyawan/generate-all"); ?>",
                    data: data,
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
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload();
                                    $('#formGeneratePinjaman')[0].reset();
                                    $("#generateModal").modal("hide");
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then(() => {});
                        }
                    },

                });
            }
        });
        // generate single pinjaman
        $('#generateUlang').click(function(e) {
            e.preventDefault();
            if ($('#formGeneratePinjamanSingle').valid()) {
                let csrf = $(`[name="${csrfToken}"]`);
                let data = new FormData(document.getElementById("formGeneratePinjamanSingle"));
                $.ajax({
                    url: "<?= base_url("pinjaman-karyawan/generate-single"); ?>",
                    data: data,
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
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload();
                                    $("#generateSingleModal").modal("hide");
                                });
                            $('#formGeneratePinjamanSingle')[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then(() => {});
                        }
                    },

                });
            }

        });

    });


    $('#btnUpdatePinjaman').click(function(e) {
        e.preventDefault();
        if ($('#formUpdatePinjaman').valid()) {
            var csrf = $(`[name="${csrfToken}"]`);
            var formData = new FormData();
            var id = $('#id').val();
            var statusPinjaman = $('#statusPinjaman option:selected').val();
            var nominal = destroyFormatRupiah($('#nominal').val());
            formData.append("id", id);
            formData.append("status_pinjaman", statusPinjaman);
            formData.append("nominal", nominal);
            $.ajax({
                url: "<?= base_url("pinjaman-karyawan/update"); ?>",
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
                    if (response.status) {
                        Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            .then(() => {
                                table.ajax.reload();
                                $("#updatePinjamanModal").modal("hide");
                            });
                        $('#formUpdatePinjaman')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        }).then(() => {});
                    }
                },

            });
        }
    })

    $("select[name='filterEmployeeID'], select[name='filterGolongan'], select[name='filterDivisiID'],#month,select[name='filterStatusPinjaman']").change(function() {
        table.ajax.reload();
    });

    function exportPinjaman() {
        var divisionID = $("select[name='filterDivisiID']").val();
        var yearMonth = $('#month').val();
        var bagianID = $('#filterBagianID').val();
        if (divisionID == "") {
            Swal.fire({
                icon: 'error',
                title: 'Pilih Departemen',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else if (yearMonth == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih Periode',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else if (bagianID == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih bagian',
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            var url = "<?= base_url('pinjaman-karyawan/print') ?>" + "?divisi_id=" + divisionID + '&year_month=' + yearMonth + '&bagian_id=' + bagianID;
            window.open(url, "_blank");
        }
    }

    function destroy(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("pinjaman-karyawan/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    table.ajax.reload()
                                    $(".add-modal").modal("hide")
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                });
            }
        })
    }


    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>