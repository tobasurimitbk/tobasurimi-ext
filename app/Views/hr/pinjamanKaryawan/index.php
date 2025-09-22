<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List Pinjaman Karyawan</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" data-bs-toggle="modal" data-bs-target="#generateModal" href="#">
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
                        <select class="form-select" name="filterDivisiID" aria-label="Floating label select example">
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
                        <select class="form-select" name="filterGolongan" aria-label="Floating label select example">
                            <option value="">
                                Cari Tipe / Golongan
                            </option>
                            <?php foreach ($golongan as $g) : ?>
                                <option value="<?= $g['golongan_name'] ?>">
                                    <?= $g['golongan_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Tipe / Golongan</label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-floating">
                        <select class="form-select" name="filterEmployeeID" aria-label="Floating label select example">
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
                                <th style="width: 10px;">No</th>
                                <th onclick="changeSort('employees.name')" class="sort">Nama Karyawan</th>
                                <th onclick="changeSort('employees.tipe')" class="sort">Tipe/Gol</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Dept</th>
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
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <input value="" name="monthYear" id="monthYear" type="text" required class="form-control target input-picker" placeholder="Periode Pinjaman">
                                    <label>Periode Pinjaman</label>
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
                                    <input value="" name="startDate" id="startDate" type="text" required class="form-control target input-picker" placeholder="Tanggal Mulai Log Absensi">
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
                                    <input value="" name="finishDate" id="finishDate" type="text" required class="form-control target input-picker" placeholder="Tanggal Selesai Log Absensi">
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
                                <input readonly value="" name="monthYear" id="employeeName" type="text" required class="form-control target input-picker">
                                <label>Karyawan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating ">
                                <input value="" readonly id="tipeGol" type="text" required class="form-control target input-picker">
                                <label>Tipe/Gol</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <input value="" name="monthYear" id="monthYear" type="text" required class="form-control target input-picker" placeholder="Periode Pinjaman">
                                    <label>Periode Pinjaman</label>
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
                                    <input value="" name="startDate" type="text" id="startDate" required class="form-control target input-picker" placeholder="Tanggal Mulai Log Absensi">
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
                                    <input value="" name="finishDate" type="text" id="finishDate" required class="form-control target input-picker" placeholder="Tanggal Selesai Log Absensi">
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
<script>
    let sort = "pinjaman_karyawan.id";
    let sortType = "desc";
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
                    let is_boleh_minjam = row.isBolehMinjam;
                    let status_pinjaman = row.statusPinjaman;
                    let id = row.id;
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
                    let status_pinjaman = row.statusPinjaman;
                    let htmlRes = '';

                    if (is_boleh_minjam == 0) {
                        htmlRes += `
                            <div class="text-danger">
                                Tidak Diizinkan
                            </div>`
                    } else {
                        if (status_pinjaman == 0) {
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
                sortable: false,
                render: function(data, type, row) {
                    let employeeID = row.employeeID;
                    let employeeName = row.name;
                    let yearMonth = row.monthYear;
                    let startDate = row.mulaiAbsen;
                    let finishDate = row.selesaiAbsen;
                    let tipeGol = row.tipeGol;
                    let status_pinjaman = row.statusPinjaman;
                    let id = row.id;

                    if (status_pinjaman == 1) {
                        return '-';
                    } else {
                        return `
                <div class="mt-0">
                    <button data-toggle="tooltip" title="Generate Ulang" onclick="generateSingle(${employeeID}, '${employeeName}', '${yearMonth}', '${startDate}', '${finishDate}', '${tipeGol}', ${id})" class="btn btn-success posting-spp">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            `
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
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    // select2 divisi
    $("select[name='filterDivisiID']").select2({
        placeholder: "Cari Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("select[name='filterEmployeeID']").select2({
        placeholder: "Cari Berdasarkan Karyawan",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("select[name='filterGolongan']").select2({
        placeholder: "Cari Tipe/Golongan Pegawai",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $("#startDate,#finishDate").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("#monthYear,#month").datepicker({
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

    $("select[name='filterDivisiID']").on('change', function(e) {
        e.preventDefault();
        const csrf = $(`[name="${csrfToken}"]`);
        var divisionID = $(this).val();

        var formData = new FormData();
        formData.append('divisionID', divisionID);

        $.ajax({
            url: "<?= base_url("pinjaman-karyawan/employees"); ?>",
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
                }
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
                monthYear: {
                    required: true
                },
                startDate: {
                    required: true
                },
                finishDate: {
                    required: true
                }
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
                                    $("#generateModal").modal("hide");
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then(() => {});
                        }
                        $('#formGeneratePinjaman')[0].reset();
                        $("#generateModal").modal("hide");
                        table.ajax.reload();
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

    $("select[name='filterEmployeeID'], select[name='filterGolongan'], select[name='filterDivisiID'],#month").change(function() {
        table.ajax.reload();
    });

    function exportPinjaman() {
        var divisionID = $("select[name='filterDivisiID']").val();
        var yearMonth = $('#month').val();
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
        } else {
            var url = "<?= base_url('pinjaman-karyawan/print') ?>" + "?divisi_id=" + divisionID + '&year_month=' + yearMonth;
            window.open(url, "_blank");
        }
    }
</script>
<script>

</script>

<?= $this->endSection(); ?>