<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-add-spp .form-floating .form-floating-custom .select2 .selection .select2-selection {
        height: 150px !important;
    }

    .form-add-spp .form-floating .form-floating-custom .select2 .selection .select2-selection__rendered {
        height: 120px !important;
    }
</style>
<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Karyawan</h1>
        <?php if (can('Personalia', 'Karyawan', 'c')): ?>
            <button class="btn btn-sync btn-add" float-right style="right:110px;">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Sync Karyawan ke Fingerprint
            </button>
            <a class="btn btn btn-show-form btn-save float-right" href="<?= base_url('employee/create') ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 mt-1">
                    <div class="form-floating" style="height: 50px;">
                        <select name="division_id" id="division_id" class="form-control form-select division_id">
                            <option value="">Pilih Departemen</option>
                            <?php foreach ($divisi as $d) : ?>
                                <option value="<?= encrypt($d['id']) ?>">
                                    <?= strtoupper($d['divisi']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput" style="z-index: 1;">Pilih Departemen</label>
                    </div>
                </div>
                <div class="col-md-3 mt-1">
                    <div class="form-floating" style="height: 50px;">
                        <select class="form-select bagian_id" name="bagian_id" id="bagian_id" aria-label="Floating label select example">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput" style="z-index: 1;">Bagian </label>
                    </div>
                </div>
                <div class="col-md-3 mt-1">
                    <div class="form-floating" style="height: 50px;">
                        <select class="form-select tipe" name="tipe" id="tipe" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($tipeEmployee as $t) : ?>
                                <option value="<?= $t['golongan_name'] ?>">
                                    <?= strtoupper($t['golongan_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput" style="z-index: 1;">Tipe / Golongan </label>
                    </div>
                </div>
                <div class="col-md-3 mt-1">
                    <div class="form-floating" style="height: 50px;">
                        <input autocomplete="one-time-code" type="text" class="form-control search" id="search" name="search" placeholder="">
                        <label for="floatingInput">Cari Berdasarkan NIP/Nama </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableKaryawan" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('employees.nip')" class="sort">NIP</th>
                                <th onclick="changeSort('employees.id')" class="sort">ID Finger</th>
                                <th onclick="changeSort('employees.name')" class="sort">Nama Lengkap</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Departemen</th>
                                <th onclick="changeSort('employees.bagian_id')" class="sort">Bagian</th>
                                <th onclick="changeSort('employees.tipe')" class="sort">Tipe/Gol</th>
                                <th onclick="changeSort('employees.dob')" class="sort">Tgl Lahir</th>
                                <th onclick="changeSort('employees.gender')" class="sort">Jenis Kelamin</th>
                                <th onclick="changeSort('employees.attendance_sync')" class="sort">Finger</th>
                                <th onclick="changeSort('employees.status')" class="sort">Status</th>
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

<div class="modal sync-fingerprint-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sinkronisasi Karyawan yang Belum Terdaftar di Fingerprint</h5>
            </div>
            <div class="modal-body">
                <form class="create-form form-add-spp form-sinkronisasi" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating" style="height: 50px;">
                                <select name="attendances_unit_id" id="attendances_unit_id" class="form-control form-select attendances_unit_id">
                                    <option value="">Pilih Mesin Finger</option>
                                    <?php foreach ($dataAttendanceUnit as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Pilih Mesin Finger</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-show-detail btn-add btn-block float-right" id="btnPilihSemua" type="button" style="width: 90% !important;">
                                        <i class="fa-solid fa-users"></i> Pilih Semua
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="form-floating mb-3" style="height: 150px;">
                                <div class="form-floating-custom">
                                    <select multiple class="form-select employee_id" name="employee_id[]" id="employee_id[]">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-copy btn-discard mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form" id="btnSinkronisasi">Sinkronisasi</button>
            </div>
        </div>
    </div>
</div>

<div class="modal list-fingerprint-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">List Fingerprint</h5>
            </div>
            <div class="modal-body">
                <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <div class="text-center">
                                <img class="preview_photo" width="130" id="preview_photo" src="" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control nama_karyawan" id="nama_karyawan" name="nama_karyawan" placeholder="Nama Karyawan">
                                <label for="floatingInput">Nama Karyawan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table nowrap table-hover-tobasurimi dataTable" id="listFingerTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th class="sort">Nama Finger</th>
                                        <th class="sort">IP Unit Finger</th>
                                        <th class="sort" style="width: 10px;text-align:center;">Status</th>
                                        <th style="width: 80px;text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table-list-finger" id="body-table-list-finger">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-copy btn-discard mr-2" id="btnHideListFingerprint">Kembali</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "nip";
    let sortType = "asc";
    var employeeIdArr = [];
    var employeeIdSelectedFinger = [];

    var row = 0;

    const table = $('#dataTableKaryawan').DataTable({

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
            url: "<?= base_url("employee/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.tipe = $('.tipe').val();
                data.bagian_id = $('.bagian_id').val();
                data.division_id = $('.division_id').val();
                data.sort = sort;
                data.sortType = sortType;
            },

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
        }, {
            data: "nip",
            className: "text-left"
        }, {
            data: "id_text",
            className: "text-left"
        }, {
            data: "name",
            className: "text-left"
        }, {
            data: "divisionName",
            className: "text-left"
        }, {
            data: "bagianName",
            className: "text-left"
        }, {
            data: "tipe",
            className: "text-left"
        }, {
            data: "dob",
            className: "text-left"
        }, {
            data: "gender",
            className: "text-left"
        }, {
            data: "attendance_sync",
            className: "text-center",
            render: function(data, type, row) {
                let attendance_sync = row.attendance_sync;
                let htmlRes = '';

                if (row.attendance_sync == "1") {
                    htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                } else {
                    htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                }

                return htmlRes;

            }
        }, {
            data: "status",
            className: "text-center",
            render: function(data, type, row) {
                let status = row.status;
                let htmlRes = '';

                if (status == "AKTIF") {
                    htmlRes += `
                            <div class="text-success">
                               <i class="fa-solid fa-check"></i>
                            </div>`
                } else {
                    htmlRes += `
                            <div class="text-danger">
                               <i class="fa-solid fa-x"></i>
                            </div>`
                }

                return htmlRes;
            }
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            width: "10%",
            render: function(data, type, row) {
                let id = row.id;
                let res = '';

                res += `
                  <?php if (can('Personalia', 'Karyawan', 'u')): ?>
                        <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    <?php endif ?>
                  <?php if (can('Personalia', 'Karyawan', 'd')): ?>
                        <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif ?>
                    <button data-toggle="tooltip" title="Atur Jam Kerja" onclick="jamKerjaAction('${row.id}')" class="btn btn-success">
                        <i class="fas fa-user-clock"></i>
                    </button>
                    <button data-toggle="tooltip" title="List Finger" onclick="listFingerAction('${row.id}')" class="btn btn-info">
                        <i class="fa-solid fa-fingerprint"></i>
                    </button>
                `;

                return res;
            }
        }],
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
            emptyTable: "Tidak Ada Data Karyawan",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $('#division_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        let csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append('divisionID', $(this).val());
        $.ajax({
            url: `<?= base_url("employee/get-bagian"); ?>`,
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
                $("select[name='bagian_id']").empty()
                $("select[name='bagian_id']").append(`<option value=""></option>`)
                result.data.forEach(function(item) {
                    $("select[name='bagian_id']").append(`<option value="${item.id}">${item.kode_bagian.toUpperCase()} - ${item.nama_bagian.toUpperCase()}</option>`)
                });

            }
        });
        table.ajax.reload();
    });

    $('#bagian_id').select2({
        placeholder: "Pilih Bagian",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('#tipe').select2({
        placeholder: "Pilih Tipe / Golongan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px').css('height', ' calc(3.5rem + 2px)');


    $('.search').keyup(function() {
        table.ajax.reload();
    });

    $('#attendances_unit_id').select2({
        placeholder: "Pilih Mesin Finger",
        theme: "bootstrap-5",
        dropdownParent: $('.sync-fingerprint-modal')
    });

    $('.employee_id').select2({
        placeholder: "Pilih Employee",
        theme: "bootstrap-5",
        allowClear: false,
    });

    $('#attendances_unit_id').change(function(e) {
        e.preventDefault();
        dropdownEmployeeSyncFinger();
    });

    $('.employee_id').change(function(e) {
        e.preventDefault();
        let arr = $(this).val();
        employeeIdSelectedFinger = [];
        employeeIdSelectedFinger = arr;
    });

    var validatorSinkronasi = $(".form-sinkronisasi").validate({
        rules: {
            attendances_unit_id: {
                required: true
            },
        },
        messages: {
            attendances_unit_id: {
                required: "Pilih mesin finger"
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


    $('#btnPilihSemua').click(function(e) {
        e.preventDefault();
        if (employeeIdArr.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Data karyawan kosong',
                confirmButtonColor: '#4e73df',
            });
            return '';
        } else {
            var employeeSelect = $("select[name='employee_id[]']");
            employeeSelect.empty();

            var emptyOption = $("<option></option>")
                .attr("value", "")
                .text("Pilih Karyawan");
            employeeSelect.append(emptyOption);
            $.each(employeeIdArr, function(index, data) {
                employeeIdSelectedFinger.push(data.id);
                var option = $("<option selected></option>")
                    .attr("value", data.id)
                    .text(data.name);
                employeeSelect.append(option);
            });
        }
    })

    // $('#btn-reset-filter').click(function() {
    //     $("select[name='bagian_id']").empty()
    //     $('#division_id').val(null).change();
    //     $('#bagian_id').val(null).change();
    //     $('#tipe').val(null).change();
    //     $('#search').val('');
    // });

    $('.btn-sync').click(function(e) {
        e.preventDefault();
        $('#attendances_unit_id').val(null).change();
        employeeIdArr = [];
        employeeIdSelectedFinger = [];
        drawDropdownEmployee(employeeIdArr);
        $('.sync-fingerprint-modal').modal('show');
    });

    $('.btn-discard').click(function() {
        $('.sync-fingerprint-modal').modal('hide');
    });

    $('#btnSinkronisasi').click(function(e) {
        e.preventDefault();
        if (employeeIdSelectedFinger.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Data karyawan dipilih kosong',
                confirmButtonColor: '#4e73df',
            });
            return false;
        }

        if ($('.form-sinkronisasi').valid()) {
            const csrf = $(`[name="${csrfToken}"]`);
            let formData = new FormData();
            formData.set('attendances_unit_id', $('#attendances_unit_id').val());
            formData.set('employee_id', JSON.stringify(employeeIdSelectedFinger));

            $.ajax({
                url: "<?= base_url('employee/sync-employee-finger'); ?>",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    setLoading();
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                complete: function() {
                    stopLoading();
                },
                success: function(response) {
                    csrf.val(response.token);
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        }).then(() => {
                            table.ajax.reload();
                            $(".sync-fingerprint-modal").modal("hide");
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        });
                    }
                }
            });
        }
    });

    $('#btnHideListFingerprint').click(function(e) {
        e.preventDefault();
        $('.list-fingerprint-modal').modal('hide');
    });


    function resetForm() {
        $('#attendances_unit_id').val(null).change();
        employeeIdArr = [];
    }

    function dropdownEmployeeSyncFinger() {
        var attendanceUnitId = $('#attendances_unit_id option:selected').val();
        if (attendanceUnitId == '') {
            return '';
        }

        $.ajax({
            url: `<?= base_url("employee/get-employee-not-sync-finger"); ?>`,
            data: {
                attendance_unit_id: attendanceUnitId,
            },
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "GET",
            success: function(result) {
                employeeIdArr = result.data;
                drawDropdownEmployee(employeeIdArr);
            }
        });
    }

    function drawDropdownEmployee(employeIdArr) {
        var employeeSelect = $("select[name='employee_id[]']");
        employeeSelect.empty();

        var emptyOption = $("<option></option>")
            .attr("value", "")
            .text("Pilih Karyawan");
        employeeSelect.append(emptyOption);
        $.each(employeIdArr, function(index, data) {
            var option = $("<option></option>")
                .attr("value", data.id)
                .text(data.name);
            employeeSelect.append(option);
        });
    }

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function jamKerjaAction(id) {
        window.location.href = "<?= base_url('employee/jam-kerja/') ?>" + id;

    }

    function edit(id) {
        location.replace(`<?= base_url("employee/id"); ?>/${id}`);
    }

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Karyawan?',
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
                    url: "<?= base_url("employee/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading()
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
                                })
                        }
                    },

                });
            }
        });

    }

    function listFingerAction(employeeId) {
        $.ajax({
            url: `<?= base_url("employee/get-employee-sync-finger"); ?>`,
            data: {
                employee_id: employeeId
            },
            method: "GET",
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(result) {
                if (result.status) {
                    var data = result.data;
                    $('#nama_karyawan').val(data.employee.name);

                    var tableBody = $('#listFingerTable tbody');
                    tableBody.empty(); // hapus isi sebelumnya

                    $.each(data.finger, function(i, v) {
                        let actionBtn = '';
                        let statusBadge = v.status ?
                            `<div class="text-success text-center">
                               <i class="fa-solid fa-check"></i>
                            </div>` :
                            `
                             <div class="text-danger text-center">
                               <i class="fa-solid fa-x"></i>
                            </div>
                            `;

                        if (v.status == true) {
                            actionBtn = `
                                <center>
                                    <button data-toggle="tooltip" title="Hapus Finger" onclick="deleteFinger('${v.id}', '${v.employee_id}')" class="btn btn-danger" type="button">
                                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                    </button>
                                </center>
                            `;
                        }

                        let row = `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${v.name}</td>
                            <td>${v.ip}</td>
                            <td>${statusBadge}</td>
                            <td>${actionBtn}</td>
                        </tr>
                    `;
                        tableBody.append(row);
                    });

                    $('#preview_photo').attr('src', data.employee.employee_img);
                    $('.list-fingerprint-modal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: result.message,
                        confirmButtonColor: '#4e73df',
                    });
                }
            }
        });
    }

    function deleteFinger(attendanceUnitId, employeeId) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Karyawan di mesin finger ?',
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
                    url: "<?= base_url("employee/delete-employee-in-finger"); ?>",
                    data: {
                        attendance_unit_id: attendanceUnitId,
                        employee_id: employeeId
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading()
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
                                    table.ajax.reload();
                                    $('.list-fingerprint-modal').modal('hide');
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            });
                        }
                    },

                });
            }
        });
    }
</script>

<?= $this->endSection(); ?>