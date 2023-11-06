<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Pinjaman Karyawan</h1>
        <div class="col-button-tambah-spp">
            <?php if (count($pinjamanCheck) == 0) : ?>
                <a class="btn btn-hide-form btn-discard float-right" data-bs-toggle="modal" data-bs-target="#generateModal" href="#">
                    Generate
                </a>
            <?php endif; ?>
            <?php if (count($pinjamanCheck) != 0) : ?>
                <button onclick="printPinjaman('<?= base_url('pinjaman-karyawan/print/' . $year . '-' . $month) ?>')" class="btn btn-warning btn-print float-right">
                    Print
                </button>
                <!-- <button class="btn btn-show-form btn-save float-right btn-submit">
                    Simpan
                </button> -->
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-page-list-attendance mb-3">
                <div class="col-6 mb-0">
                    <form action="<?= base_url('pinjaman-karyawan') ?>" class="kt-form kt-form--fit kt-margin-b-20" method="GET">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                                <!-- <th scope="col" style="width: 10px;"><input type="checkbox" id="parent"></th> -->
                                <th>No</th>
                                <th onclick="changeSort('employees.name')" class="sort">Nama Karyawan</th>
                                <th onclick="changeSort('employees.tipe')" class="sort">Tipe/Gol</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Departemen</th>
                                <th onclick="changeSort('pinjaman_karyawan.start_date')">Mulai Absen</th>
                                <th onclick="changeSort('pinjaman_karyawan.end_date')">Selesai Absen</th>
                                <th onclick="changeSort('pinjaman_karyawan.hadir')">Hadir</th>
                                <th onclick="changeSort('pinjaman_karyawan.tidak_hadir')">Tidak Hadir</th>
                                <th>Nominal</th>
                                <th>Status Pinjaman</th>
                                <!-- <th>Action</th> -->
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Generate Pinjaman Karyawan</h5>
            </div>
            <form id="formGeneratePinjaman" class="create-form" role="form" method="POST">
                <div class="modal-body">
                    <div class="alert bg-info text-white" style="font-weight: bold; margin-top:-10px;">
                        Pinjaman digenerate tanggal 12 setiap bulan
                    </div>
                    <?= csrf_field() ?>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="form-floating mt-1">
                                <input value="<?= $year . '-' . $month ?>" autocomplete="one-time-code" readonly name="monthYear" type="month" required class="form-control target input-picker">
                                <label>Periode Pinjaman</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mt-3">
                                <input value="<?= "01/$month/$year" ?>" readonly autocomplete="one-time-code" name="startDate" type="text" required class="form-control target input-picker">
                                <label for="floatingInput">Tanggal Mulai</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mt-3">
                                <input value="<?= "12/$month/$year" ?>" readonly autocomplete="one-time-code" name="finishDate" type="text" required class="form-control target input-picker">
                                <label for="floatingInput">Tanggal Selesai</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-submit-form" id="generateGlobal">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="generateSingleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Generate Ulang Pinjaman</h5>
            </div>
            <form id="formGeneratePinjamanSingle" role="form" method="POST">
                <div class="modal-body">
                    <div class="alert bg-info text-white" style="font-weight: bold; margin-top:-10px;">
                        Jika ada update data pada log absensi, anda dapat melakukan generate ulang pinjaman per pegawai
                    </div>
                    <?= csrf_field() ?>
                    <input type="hidden" name="employeeID" id="employeeID">
                    <input type="hidden" name="id" id="id">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-floating mt-1">
                                <input value="" autocomplete="one-time-code" name="monthYear" id="employeeName" readonly type="text" required class="form-control target input-picker">
                                <label>Periode Pinjaman</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mt-1">
                                <input value="" readonly autocomplete="one-time-code" id="tipeGol" type="text" required class="form-control target input-picker">
                                <label>Tipe/Gol</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mt-3">
                                <input value="" autocomplete="one-time-code" readonly name="monthYear" id="monthYear" type="month" required class="form-control target input-picker">
                                <label>Periode Pinjaman</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mt-3">
                                <input value="" readonly autocomplete="one-time-code" name="startDate" type="text" id="startDate" required class="form-control target input-picker">
                                <label for="floatingInput">Tanggal Mulai</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mt-3">
                                <input value="" readonly autocomplete="one-time-code" name="finishDate" type="text" id="finishDate" required class="form-control target input-picker">
                                <label for="floatingInput">Tanggal Selesai</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-submit-form" id="generateUlang">Generate Ulang</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="updateStatusPinjamanModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Pinjaman</h5>
            </div>
            <form role="form" id="formChangeStatusPinjaman" method="POST">
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12" style="margin-top: -20px;">
                            <div class="alert bg-info">
                                Status pinjaman yang <b>Diambil</b>, akan masuk kedalam komponen potongan di payroll. <br>
                                <small id="totalDataSelected" class="card-text mt-2" style="font-size: 13px; font-weight:bold;">
                                </small>
                            </div>
                            <div class="form-floating" style="margin-top: -5px;">
                                <select required class="form-select" name="statusPinjaman" aria-label="Floating label select example">
                                    <option value="">
                                        Pilih Status Pinjaman
                                    </option>
                                    <option value="1">Sudah Diambil</option>
                                    <option value="0">Tidak Diambil</option>
                                </select>
                                <label for="floatingInput">Pilih Status Pinjaman</label>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-submit-form" id="updateStatusPinjaman">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let sort = "nomor";
    let sortType = "desc";
    const csrfToken = '<?= csrf_token() ?>';
    $('#loadingSpinner').hide();
    // $("input[name='startDate'], input[name='finishDate']").datepicker({
    //     todayHighlight: true,
    //     format: "dd/mm/yyyy",
    //     orientation: "bottom auto",
    //     autoclose: true
    // });
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
            url: "<?= base_url("pinjaman-karyawan/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.divisi_id = $("select[name='filterDivisiID']").val();
                data.employee_id = $("select[name='filterEmployeeID']").val();
                data.tipe = $("select[name='filterGolongan']").val();
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
        columns: [
            // {
            //     data: "id",
            //     className: "text-center",
            //     sortable: false,
            //     width: "5%",
            //     searchable: false,
            //     render: function(data, type, row) {
            //         let id = row?.id;
            //         let is_boleh_minjam = row?.isBolehMinjam;
            //         let employeeName = row?.name;
            //         let status_pinjaman = row?.statusPinjaman;
            //         if (status_pinjaman == 1) {
            //             return '-';
            //         } else {
            //             return `
            //             <input name="id_pinjaman[]" data-employee_name="${employeeName}" class="child id_pinjaman" type="checkbox" value="${id}" ${is_boleh_minjam == 0 ? 'disabled' : ''}>
            //             `
            //         }

            //     }
            // },

            {
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
                data: "tipeGol",
                className: "text-center",
                width: "10%"
            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "mulaiAbsen",
                className: "text-center"
            },
            {
                data: "selesaiAbsen",
                className: "text-center"
            },
            {
                data: "hadir",
                className: "text-center"
            },
            {
                data: "tidakHadir",
                className: "text-center"
            },
            {
                data: "nominalPinjaman",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let nominalPinjaman = row?.nominalPinjaman;
                    let is_boleh_minjam = row?.isBolehMinjam;
                    let status_pinjaman = row?.statusPinjaman;
                    let id = row?.id;
                    return formatRupiah(nominalPinjaman);
                }
            },

            {
                data: "statusPinjaman",
                className: "text-center",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let is_boleh_minjam = row?.isBolehMinjam;
                    let status_pinjaman = row?.statusPinjaman;
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

            //     {
            //         data: "id",
            //         className: "text-center actions",
            //         searchable: false,
            //         sortable: false,
            //         render: function(data, type, row) {
            //             let employeeID = row?.employeeID;
            //             let employeeName = row?.name;
            //             let yearMonth = row?.monthYear;
            //             let startDate = row?.mulaiAbsen;
            //             let finishDate = row?.selesaiAbsen;
            //             let tipeGol = row?.tipeGol;
            //             let status_pinjaman = row?.statusPinjaman;
            //             let id = row?.id;

            //             if (status_pinjaman == 1) {
            //                 return '-';
            //             } else {
            //                 return `
            //     <div class="mt-0">
            //         <button onclick="generateSingle(${employeeID}, '${employeeName}', '${yearMonth}', '${startDate}', '${finishDate}', '${tipeGol}', ${id})" class="btn btn-success posting-spp">
            //             <i class="fa-solid fa-sm fa-repeat"></i>
            //         </button>
            //     </div>
            // `
            //             }

            //         }
            //     }


        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Data pinjaman bulan <?= $month ?> tahun <?= $year ?> belum digenerate", // Change this line
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
        // generate pinjaman action
        $('#generateGlobal').click(function(e) {
            e.preventDefault();
            if ($("#formGeneratePinjaman").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Generate Pinjaman Karyawan?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.getElementById("formGeneratePinjaman"));
                        $.ajax({
                            url: "<?= base_url("pinjaman-karyawan/generate-all"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                $('#loadingSpinner').show();
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
                                        icon: 'warning',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {});
                                }
                                $('#loadingSpinner').hide();
                                $('#formGeneratePinjaman')[0].reset();
                                $("#generateModal").modal("hide");
                                location.reload();
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                });
                                $('#loadingSpinner').hide();
                                $('#formGeneratePinjaman')[0].reset();
                            }
                        });
                    }
                });
            }
        });
        // generate single pinjaman
        $('#generateUlang').click(function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'Generate Ulang Pinjaman Karyawan?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    let csrf = $(`[name="${csrfToken}"]`);
                    let data = new FormData(document.getElementById("formGeneratePinjamanSingle"));
                    $.ajax({
                        url: "<?= base_url("pinjaman-karyawan/generate-single"); ?>",
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            $('#loadingSpinner').show();
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
                                    icon: 'warning',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                }).then(() => {});
                            }
                            $('#loadingSpinner').hide();

                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            });
                            $('#loadingSpinner').hide();
                        }
                    });

                }
            })
        });

        $('#parent').click(function() {
            $('.child:not(:disabled)').prop('checked', this.checked);
        });

        $('.child').click(function() {
            if ($('.child:checked').length == $('.child').length) {
                $('#parent').prop('checked', true);
            } else {
                $('#parent').prop('checked', false);
            }
        });

        let checkedValues = [];
        $('.btn-submit').on('click', function() {
            $('input[name="id_pinjaman[]"]:checked').each(function() {
                checkedValues.push($(this).val());
            });
            if (checkedValues.length == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: "Checklist minimal satu data karyawan",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else {
                $('#totalDataSelected').text("Total data selected : " + checkedValues.length + " karyawan");

                $('#updateStatusPinjamanModal').modal('show');
            }

        });

        $('#updateStatusPinjaman').click(function(e) {
            e.preventDefault();
            var statusPinjaman = $("select[name='statusPinjaman']").val();

            if (statusPinjaman == '') {
                Swal.fire({
                    icon: 'warning',
                    title: "Pilih Status Pinjaman Dahulu",
                    confirmButtonColor: '#4e73df',
                }).then(() => {});
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Ubah status pinjaman karyawan ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let csrf = $(`[name="${csrfToken}"]`);
                        let formData = new FormData();
                        formData.append('pinjamanID', checkedValues);
                        formData.append('statusPinjaman', statusPinjaman);

                        $.ajax({
                            url: "<?= base_url("pinjaman-karyawan/change-status"); ?>",
                            data: formData,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                $('#loadingSpinner').show();
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
                                            $("#updateStatusPinjamanModal").modal("hide");
                                        });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {});
                                }
                                $('#loadingSpinner').hide();
                                $('#formChangeStatusPinjaman')[0].reset();
                                $("#updateStatusPinjamanModal").modal("hide");
                                checkedValues = [];
                                $('input[name="id_pinjaman[]"]:checked').prop('checked', false);

                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                });
                                $('#loadingSpinner').hide();
                                $('#formChangeStatusPinjaman')[0].reset();
                                checkedValues = [];
                                $('input[name="id_pinjaman[]"]:checked').prop('checked', false);
                            }
                        });
                    }
                });
            }
        });
    });

    const changeNominalPinjaman = function(element) {
        let nominal = $(element).val();
        let id = $(element).data('id');
        let csrf = $(`[name="${csrfToken}"]`);

        var formData = new FormData();
        formData.append('id', id);
        formData.append('nominal', nominal);

        $.ajax({
            url: "<?= base_url("pinjaman-karyawan/update-nominal"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                $('#loadingSpinner').show();
            },
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loadingSpinner').hide();
                csrf.val(response.token)
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    confirmButtonColor: '#4e73df',
                });

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
    $("select[name='filterEmployeeID'], select[name='filterGolongan'], select[name='filterDivisiID']").change(function() {
        table.ajax.reload();
    });
</script>
<script>
    const printPinjaman = function(url) {
        var divisionID = $("select[name='filterDivisiID']").val();
        if (divisionID == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Departemen',
                confirmButtonColor: '#4e73df',
            });
        } else {
            window.open(url + '/' + divisionID, "_blank");
        }
    }

    function formatRupiah(angka) {
        if (angka === null) {
            angka = 0;
        }

        angka = angka.toString();
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return 'Rp. ' + ribuanFormatted + ',' + desimal;
    }
</script>

<?= $this->endSection(); ?>