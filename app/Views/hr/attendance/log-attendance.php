<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    th {
        background-color: white;
    }

    /* Row biasa lebih pendek */
    #attendanceTable,
    #attendanceTotalTable td {
        padding: 4px 8px;
        font-size: 13px;
        line-height: 1.2;
    }

    /* Header lebih tinggi & agak tebal */
    #attendanceTable thead th {
        padding: 8px 8px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        /* biar beda dikit */
    }

    #attendanceTable th,
    #attendanceTable td {
        padding: 4px 8px;
        /* lebih kecil dari default Bootstrap */
        font-size: 13px;
        /* biar lebih rapih */
        line-height: 1.2;
    }

    /* Kolom No */
    #attendanceTable th:nth-child(1),
    #attendanceTable td:nth-child(1) {
        position: sticky;
        left: 0;
        z-index: 3;
        min-width: 45px !important;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
    }

    /* Kolom NIP */
    #attendanceTable th:nth-child(2),
    #attendanceTable td:nth-child(2) {
        position: sticky;
        left: 50px;
        /* geser setelah kolom No */
        z-index: 3;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
        min-width: 120px !important;
    }

    /* Kolom Karyawan */
    #attendanceTable th:nth-child(3),
    #attendanceTable td:nth-child(3) {
        position: sticky;
        left: 170px;
        /* 50 (No) + 120 (NIP) */
        z-index: 3;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
        min-width: 200px !important;
        white-space: nowrap;
    }

    /* Kolom Dept */
    #attendanceTable th:nth-child(4),
    #attendanceTable td:nth-child(4) {
        position: sticky;
        left: 370px;
        /* 50 + 120 + 200 */
        z-index: 3;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
        min-width: 120px !important;
    }

    /* Kolom Bagian */
    #attendanceTable th:nth-child(5),
    #attendanceTable td:nth-child(5) {
        position: sticky;
        left: 490px;
        /* 50 + 120 + 200 + 120 */
        z-index: 3;
        background-color: <?= session()->get('theme') == 'dark' ? '#464D55;' : 'white;' ?>;
        min-width: 150px !important;
    }

    #attendanceTable td:nth-child(n+6):nth-child(-n+70),
    #attendanceTable th:nth-child(n+6):nth-child(-n+70) {
        min-width: 100px !important;
        cursor: pointer;
        max-width: 100px !important;
        width: 100px !important;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Log Absensi (Mesin Finger)</h1>
        <div class="col-button-tambah-spp">
            <?php if (can('Personalia', 'Log Absensi', 'p')): ?>
                <button class="btn btn-warning btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-download"></i> Export
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><a class="dropdown-item" href="#" onclick="exportExcelBulanan()">Lap. Bulanan</a></li>
                    <li><a class="dropdown-item" id="btnShowExportHarianModal" href="#" onclick="exportExcelHarian()">Lap. Harian</a></li>
                </ul>
            <?php endif; ?>
        </div>

    </div>
    <div class="card">
        <div class="card-body">

            <div class="row row-col-page-list-attendance mt-4">
                <form action="#" method="get">

                    <div class="row mb-4">
                        <div class="col-sm-3">
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
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="divisi_id" id="divisi_id">
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
                        <div class="col-sm-3">
                            <div class="form-floating">
                                <select class="form-select" name="tipe" id="tipe">
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
                            <div class="form-floating">
                                <select class="form-select" id="employee_id" name="employee_id">

                                </select>
                                <label for="floatingInput">Cari Karyawan</label>
                            </div>
                        </div>

                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-form-tts" id="attendanceTable">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-5">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="attendanceTotalTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th style="width: 100px;">Nip</th>
                                <th>Karyawan</th>
                                <th style="width: 100px;">Dept</th>
                                <th style="width: 100px;">Bagian</th>
                                <?php foreach ($statusPerizinanAll as $s): ?>
                                    <th width="20" align="center">
                                        <b><?= explode('_', $s['value'])[1] ?></b>
                                    </th>
                                <?php endforeach; ?>
                                <th width="20" align="center">
                                    <b>L</b>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div class="card-text mt-4">
                    <b class="">Keterangan</b>
                </div>

                <div class="row mt-3">
                    <?php foreach ($statusPerizinanAll as $s) : ?>
                        <div class="col-sm-2 col-4 mt-2">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="p-3" style="width: 5px; height:5px; background-color:<?= $s['description'] ?>"></div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="card-text mt-1">
                                        <?= explode("_", $s['value'])[0] ?> (<?= explode("_", $s['value'])[1]; ?>)
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="col-sm-2 col-4 mt-2">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="text-center">
                                    <img src='assets/img/stop.png' width='27' height='27'>
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <div class="card-text mt-1">
                                    LIBUR (L)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Detail Log Attendance</h5>
            </div>
            <form id="updateAttendanceForm" role="form" method="POST">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" class="form-control" id="namaUnit" disabled>
                        <label for="namaUnit">Nama Fingerprint</label>
                    </div>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" class="form-control" id="employeeName" disabled>
                        <label for="employeeName">Employe Name</label>
                    </div>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="tanggal" class="form-control" id="tanggal" disabled>
                        <label for="tanggal">Tanggal</label>
                    </div>
                    <div class="input-group">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input type="text" name="jamKerjaName" class="form-control" id="jamKerjaName" disabled>
                            <label for="tanggal">Jam Kerja</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button class="btn btn-success jamKerjaDetail" id="jamKerjaDetail" type="button">
                                <i class="fas fa-calendar-week"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="statusKehadiran" class="form-control" id="statusKehadiran" disabled>
                        <label for="status">Status Kehadiran</label>
                    </div>

                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="keterangan" class="form-control" id="keterangan" disabled>
                        <label for="status">Keterangan Tambahan</label>
                    </div>

                    <div class="form-floating mb-2" style="height: 50px;">
                        <input type="text" name="jamTerlambat" class="form-control" id="jamTerlambat" disabled>
                        <label for="jamTerlambat">Jam Terlambat</label>
                    </div>

                    <div class="row mb-2" id="formInOut">
                        <div class="col-md-6">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <input type="text" class="form-control" id="checkIn" name="checkIn" disabled maxlength="30">
                                <label for="checkin">CheckIN</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <input type="text" class="form-control" id="checkOut" name="checkOut" disabled maxlength="30">
                                <label for="checkout">CheckOut</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard btn-discard-1 mr-3">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="detail2Modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Jam Kerja</h5>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input type="text" id="jamKerjaNameDetail" class="form-control jamKerjaNameDetail" disabled>
                            <label for="checkin">Jenis Jam Kerja</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input type="text" id="jamTerlambatDetail" class="form-control jamTerlambatDetail" disabled>
                            <label for="checkout">Jam Terlambat</label>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered nowrap table-striped table-hover-tobasurimi dataTable table-form-tts" id="dataTable2" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center; width:10px;">No</th>
                            <th style="text-align: center;">Hari</th>
                            <th style="text-align: center;">Masuk</th>
                            <th style="text-align: center;">Mulai Istirahat</th>
                            <th style="text-align: center;">Selesai Istirahat</th>
                            <th style="text-align: center;">Pulang</th>
                        </tr>
                    </thead>
                    <tbody class="body-table">
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-2 mr-3">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="lapHarianModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Laporan Harian Log Presensi</h5>
            </div>
            <form id="lapLogAbsensiForm" role="form" method="POST">
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-2" style="height: 50px;">
                                    <input type="text" id="start_date" name="start_date" class="form-control start_date" placeholder="Tanggal Mulai Log Absensi">
                                    <label for="start_date">Tanggal Mulai Log Absensi</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button disabled class="btn btn-secondary" type="button">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-2" style="height: 50px;">
                                    <input type="text" id="end_date" name="end_date" class="form-control end_date" placeholder="Tanggal Selesai Log Absensi">
                                    <label for="end_date">Tanggal Selesai Log Absensi</label>
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
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" id="btnHideExportHarianModal">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnExportLapHarian">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let attendanceTable;

    $.ajax({
        url: "<?= base_url('log-attendance/all') ?>",
        type: "POST",
        data: {
            month: $('#month').val(),
            year: $('#year').val(),
            divisi_id: $('#divisi_id').val(),
            tipe: $('#tipe').val(),
            employee_id: $('#employee_id').val()
        },
        success: function(json) {
            // buat header <th> sesuai response columns
            let thead = '<tr>';
            json.columns.forEach(col => thead += `<th>${col.title}</th>`);
            thead += '</tr>';
            $('#attendanceTable thead').html(thead);

            // init DataTable (sekali saja)
            attendanceTable = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: true,
                paging: true,
                autoWidth: true,
                pageLength: 25, // 🔹 default 25 baris per halaman
                ajax: {
                    url: "<?= base_url('log-attendance/all') ?>",
                    type: "POST",
                    data: function(d) {
                        d.month = $('#month').val();
                        d.year = $('#year').val();
                        d.divisi_id = $('#divisi_id').val();
                        d.tipe = $('#tipe').val();
                        d.employee_id = $('#employee_id').val();
                    },
                    dataSrc: 'data' // penting, biar DataTables ngerti
                },
                columnDefs: [{
                    targets: "_all",
                    render: function(data, type, row, meta) {
                        let colName = meta.settings.aoColumns[meta.col].data; // contoh: "day_1_in"
                        let colClass = row[colName + "_class"] || ""; // contoh: row.day_1_in_class

                        if (colClass.includes('bg-libur')) {
                            return `<img src="<?= base_url('assets/img/stop.png') ?>" 
                        width="16" height="16" alt="Stop">`;
                        }


                        if (colClass.includes('bg-alpha')) {
                            return renderCell(data, '#e7323a');
                        }

                        if (colClass.includes('bg-cuti-tahunan')) {
                            return renderCell(data, '#ffc107');
                        }

                        if (colClass.includes('bg-cuti-haid')) {
                            return renderCell(data, '#242120');
                        }

                        if (colClass.includes('bg-cuti-hamil')) {
                            return renderCell(data, '#C34A36');
                        }

                        if (colClass.includes('bg-cuti-melahirkan')) {
                            return renderCell(data, '#4B4453');
                        }

                        if (colClass.includes('bg-ijin')) {
                            return renderCell(data, '#17a2b8');
                        }

                        if (colClass.includes('bg-sakit')) {
                            return renderCell(data, '#28a745');
                        }

                        if (colClass.includes('bg-rl')) {
                            return renderCell(data, '#ff7b00');
                        }

                        if (colClass.includes('bg-hadir')) {
                            return renderCell(data, '#304de2');
                        }


                        function renderCell(data, bgColor) {
                            return `<div style="
                                width:100%; 
                                height:100%; 
                                background-color:${bgColor}; 
                                color:#fff; 
                                display:flex; 
                                align-items:center; 
                                justify-content:center; 
                                font-weight:bold;
                            ">
                                ${data ?? ''}
                            </div>`;
                        }

                        return data ?? '';
                    }
                }],

                columns: json.columns,
            });
        }
    });

    let attendanceTotalTable = $('#attendanceTotalTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: true,
        paging: true,
        autoWidth: true,
        pageLength: 25, // 🔹 default 25 baris per halaman
        ajax: {
            url: "<?= base_url('log-attendance/all-total') ?>",
            type: "POST",
            data: function(d) {
                d.month = $('#month').val();
                d.year = $('#year').val();
                d.divisi_id = $('#divisi_id').val();
                d.tipe = $('#tipe').val();
                d.employee_id = $('#employee_id').val();
            },
            dataSrc: 'data'
        },
        columns: [{
                data: "no",
                className: "text-center",
            }, {
                data: "nip",
                className: "text-left"
            }, {
                data: "name",
                className: "text-left"
            }, {
                data: "divisi",
                className: "text-left",
            }, {
                data: "bagian",
                className: "text-left",
            }, {
                data: "total_cuti_tahunan",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_cuti_haid",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_cuti_hamil",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_cuti_melahirkan",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_ijin",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_sakit",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_rl",
                className: "text-left",
                sortable: false,
            }, {
                data: "total_hadir",
                className: "text-left",
                sortable: false,
            },
            {
                data: "total_alpha",
                className: "text-left",
                sortable: false,
            },
            {
                data: "total_libur",
                className: "text-left",
                sortable: false,
            },

        ],
    });


    $('#attendanceTable tbody').on('click', 'td', function() {
        let cell = attendanceTable.cell(this);
        let colIndex = cell.index().column;

        // hanya mulai dari kolom ke-6
        if (colIndex >= 5) {
            let colName = attendanceTable.settings().init().columns[colIndex].data;
            // contoh: "day_12_in"

            // ambil tanggal dari field "_date"
            let rowData = attendanceTable.row(this.closest('tr')).data();

            // ganti _in/_out/_class jadi _date
            let baseName = colName.replace(/_(in|out|class)$/, '');
            let tanggal = rowData[baseName + '_date'];
            let employeeId = rowData.id;

            $.ajax({
                url: "<?= base_url("log-attendance/detail"); ?>",
                data: {
                    employee_id: employeeId,
                    tanggal: tanggal
                },
                beforeSend: function(xhr) {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                method: "GET",
                success: function(response) {
                    var data = response.data;

                    $('#employeeName').val(data.employee?.name);
                    $('#tanggal').val(data.tanggal);
                    $('#statusKehadiran').val(data.status);
                    $('#keterangan').val(data.keterangan);
                    $('#checkIn').val(data.checkIn);
                    $('#checkOut').val(data.checkOut);
                    $('#jamTerlambat').val(data.jamTerlambat);
                    $('#namaUnit').val(data.namaUnit);

                    // ASSIGN ATTR
                    if (data.jamKerja != null) {
                        $('#jamKerjaName').val(data.jamKerja.jenis);
                        $('#jamKerjaDetail').data('jam_kerja_id', data.jamKerja.id);
                        $('#jamKerjaDetail').data('jenis', data.jamKerja.jenis);
                        $('#jamKerjaDetail').data('jam_terlambat', data.jamKerja.jam_terlambat);
                    }


                    $('#detailModal').modal('show');
                },
            });

        }
    });

    // Search employee
    $("#employee_id").select2({
        placeholder: "Cari Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "<?= base_url('log-attendance/like-employees') ?>",
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

    $("#divisi_id").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $("#tipe").select2({
        placeholder: "Cari Tipe/Golongan Pegawai",
        theme: "bootstrap-5",
        allowClear: true,
    });
    $(".month").datepicker({
        format: "yyyy-mm",
        startView: "months", // langsung tampilin bulan
        minViewMode: "months", // cuma bisa pilih bulan
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    });

    $("#start_date").datepicker({
        placeholder: "Pilih Tanggal Mulai Log Absensi",
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("#end_date").datepicker({
        placeholder: " Tanggal Selesai Log Absensi",
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#month,#divisi_id,#tipe,#employee_id').change(function(e) {
        e.preventDefault();
        if (attendanceTable) {
            attendanceTable.ajax.reload();
            attendanceTotalTable.ajax.reload();
        }
    })

    var validatorLapAbsensi = $("#lapLogAbsensiForm").validate({
        rules: {
            start_date: {
                required: true
            },
            end_date: {
                required: true
            },
        },
        messages: {
            start_date: {
                required: "Tgl selesai wajib diisi"
            },
            end_date: {
                required: "Tgl mulai wajib diisi"
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

    $('.detail').click(function(e) {
        e.preventDefault();

    });
    $('.jamKerjaDetail').click(function() {
        var element = $(this);
        var jamKerjaId = element.data('jam_kerja_id');
        var jenis = element.data('jenis');
        var jamTerlambat = element.data('jam_terlambat');
        // ASSIGN
        $('#jamKerjaNameDetail').val(jenis);
        $('#jamTerlambatDetail').val(jamTerlambat);
        // GET DETAIL JAM KERJA
        getListDetailJamKerja(jamKerjaId);

        $('#detail2Modal').modal('show');

    })

    $('.btn-discard-1').click(function() {
        $('#detailModal').modal('hide');
    });

    $('.btn-discard-2').click(function() {
        $('#detail2Modal').modal('hide');
    });

    $('#btnHideExportHarianModal').click(function(e) {
        e.preventDefault();
        $('#lapHarianModal').hide();
    });

    $('#btnShowExportHarianModal').click(function(e) {
        e.preventDefault();
        $('#start_date,#end_date').val(null);
        $('#lapHarianModal').modal('show');
    });

    $('#btnExportLapHarian').click(function(e) {
        e.preventDefault();
        if ($('#lapLogAbsensiForm').valid()) {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            var divisiId = $('#divisi_id').val();
            var tipe = $('#tipe').val();

            var url = "<?= base_url('log-attendance/export-harian') ?>" + "?start_date=" + startDate + "&end_date=" + endDate + "&divisi_id=" + divisiId + "&tipe=" + tipe;
            window.location.href = url;
        }
    });

    function getListDetailJamKerja(jamKerjaId) {
        $.ajax({
            url: `<?= base_url('employee/get-jam-kerja-detail'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                jam_kerja_id: jamKerjaId,
            },
            dataType: "json",
            success: function(res) {
                var listData = res.data;
                var no = 1;

                const table = $('#dataTable2');
                table.find('tbody').empty();
                table.find('tfoot').empty();

                if (listData.length === 0) {
                    var newRow = $('<tr>');
                    newRow.append($('<td colspan="6" style="text-align:center">Tidak Ada Jam Kerja</td>'));
                    table.find('tfoot').append(newRow);
                } else {
                    $.each(listData, function(i, v) {
                        var newRow = $('<tr style="color:whitesmoke;">');
                        newRow.append($('<td style="text-align: center;">').html(
                            `
                            ${no++} 
                        `
                        ));
                        newRow.append($('<td style="text-align: center;">').text(v.hari));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_masuk));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_istirahat_mulai));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_istirahat_selesai));
                        newRow.append($('<td style="text-align: center;">').text(v.jam_pulang));
                        table.find('tbody').append(newRow);
                    });
                }
            }
        });

    }

    function exportExcelBulanan() {
        var month = $('#month').val();
        var divisiId = $('#divisi_id').val();
        var tipe = $('#tipe').val();

        if (month == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih bulan dahulu',
                confirmButtonColor: '#4e73df',
            });
            return;
        }

        var url = "<?= base_url('log-attendance/export-bulanan') ?>?month=" + month + "&divisi_id=" + divisiId + "&tipe=" + tipe;
        window.location.href = url;
    }
</script>


<?= $this->endSection(); ?>