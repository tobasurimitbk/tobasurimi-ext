<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal" id="detailPayroll" tabindex="1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Komponen Gaji</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input type="text" value="<?= date('M-Y', strtotime($year . "-" . $month)) ?>" readonly class="form-control" disabled>
                            <label for="bulan">Bulan</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input type="text" id="divisiName" readonly class="form-control" disabled>
                            <label>Divisi</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input id="employeesName" type="text" class="form-control" disabled>
                            <label>Employee Name</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input id="totalHadir" type="text" class="form-control" disabled>
                            <label>Total Hadir</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input id="totalIjin" type="text" class="form-control" disabled>
                            <label>Total Ijin</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input id="totalAlpha" type="text" class="form-control" disabled>
                            <label>Total Alpha</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input id="totalCuti" type="text" class="form-control" disabled>
                            <label>Total Cuti</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input id="totalSakit" type="text" class="form-control" disabled>
                            <label>Total Sakit</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-2" style="height: 50px;">
                            <input id="totalLibur" type="text" class="form-control" disabled>
                            <label>Total Libur</label>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2 mb-3">
                    <table class="table-inside table-borderd nowrap table-hover-tobasurimi" width="100%" cellspacing="0" id="tabelGaji">
                        <thead class="thead-dark">
                            <tr>
                                <th>Komponen Gaji</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-gaji" style="cursor: pointer;">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-2">Close</button>
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
            <?php if (!$isGenerate) : ?>
                <a id="generate" class="btn btn-hide-form btn-discard float-right" data-bs-toggle="modal" data-bs-target="#generateModal" href="#">
                    Generate
                </a>
            <?php else : ?>
                <button class="btn btn-warning btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-print"></i> Export
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><button class="dropdown-item" onclick="printPerDivisi('<?= base_url('payroll/print/division/' . $year . '-' . $month) ?>')">Daftar Upah</button></li>
                    <li><button class="dropdown-item" onclick="detailPerDivisi('<?= base_url('payroll/print/detail/' . $year . '-' . $month) ?>')">Slip Gaji</button></li>
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
                    <div class="form-floating">
                        <select class="form-select" name="filterDivisiID" aria-label="Floating label select example">
                            <option value="">
                                Cari Berdasarkan Divisi
                            </option>
                            <?php foreach ($divisi as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['divisi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Cari Berdasarkan Divisi</label>
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
                                <th>No</th>
                                <th onclick="changeSort('nip')" class="sort">NIP</th>
                                <th onclick="changeSort('name')" class="sort">Nama Lengkap</th>
                                <th onclick="changeSort('divisi')" class="sort">Divisi</th>
                                <th>Hari Kerja</th>
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
    let sortType = "asc";
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
                data.divisi_id = $("select[name='filterDivisiID']").val();
                data.employee_id = $("select[name='filterEmployeeID']").val();
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
            }, {
                data: "nip",
                className: "text-center",
                width: "10%"
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
                data: "hariKerja",
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
                    let employee_id = row?.employee_id;
                    let id = row?.id;
                    return `
                        <div class="mt-0">
                            <button class="btn btn-warning btn-print" onclick="print('<?= base_url("payroll/print/single/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <button onclick="generateUlang(${employee_id})" class="btn btn-success posting-spp">
                                <i class="fa-solid fa-sm fa-repeat"></i>
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

    $("select[name='filterDivisiID']").change(function() {
        table.ajax.reload();
    });
    $("select[name='filterEmployeeID']").change(function() {
        table.ajax.reload();
    });

    // generate ulang per employee
    const generateUlang = function(employeeID) {
        Swal.fire({
            icon: 'question',
            title: 'Generate Ulang Payroll ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loadingSpinner').show();
                const csrf = $(`[name="${csrfToken}"]`);
                var formData = new FormData();
                var month = "<?= $month ?>";
                var year = "<?= $year; ?>";

                formData.append('month', month);
                formData.append('year', year);
                formData.append('employeeID', employeeID);

                $.ajax({
                    url: "<?= base_url("payroll/generate-single"); ?>",
                    data: formData,
                    method: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            });

                        }
                        table.ajax.reload();
                        $('#loadingSpinner').hide();
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

    // Generate
    $('#generate').click(function(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'question',
            title: 'Generate Payroll Bulan <?= $year . " - " . $month ?> ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {

            if (result.isConfirmed) {
                $('#loadingSpinner').show();
                // set variable
                const csrf = $(`[name="${csrfToken}"]`);
                var month = "<?= $month ?>";
                var year = "<?= $year; ?>";
                // append to form
                var formData = new FormData();
                formData.append('month', month);
                formData.append('year', year);
                // generate action
                $.ajax({
                    url: "<?= base_url("payroll/generate"); ?>",
                    data: formData,
                    method: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            });
                            // update table
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            });
                        }
                        $('#loadingSpinner').hide();
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

    });

    // Get and Show
    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("payroll/id"); ?>/${data.id}`);
    });
    // hide modal
    $('.btn-hide-detail').click(function() {
        $('#detailPayroll').modal('hide');

    });
    // function helper
    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join('.').split('').reverse().join('');
        return 'Rp. ' + formatted;
    }

    // select2 divisi
    $("select[name='filterDivisiID']").select2({
        placeholder: "Cari Berdasarkan Divisi",
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

    const print = function(url) {
        window.open(url, "_blank");
    }

    const printPerDivisi = function(url) {
        var divisionID = $("select[name='filterDivisiID']").val();
        if (divisionID == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Divisi Dahulu',
                confirmButtonColor: '#4e73df',
            });
        } else {
            window.open(url + '/' + divisionID, "_blank");
        }
    }

    const detailPerDivisi = function(url) {
        var divisionID = $("select[name='filterDivisiID']").val();
        if (divisionID == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Divisi Dahulu',
                confirmButtonColor: '#4e73df',
            });
        } else {
            window.open(url + '/' + divisionID, "_blank");
        }
    }
</script>

<?= $this->endSection(); ?>