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
            <?php if (!$isGenerate && !$isPosted) : ?>
                <a id="generate" class="btn btn-hide-form btn-discard float-right" data-bs-toggle="modal" data-bs-target="#generateModal" href="#">
                    Generate
                </a>
            <?php else : ?>
                <?php if (!$isPosted) : ?>
                    <a id="posting" class="btn btn-show-form btn-save float-right btn-submit">
                        Posting
                    </a>
                <?php else : ?>
                    <button disabled class="btn btn-show-form btn-save float-right btn-submit">
                        <i class="fa-solid fa-check mr-1 fa-lg"></i> Sudah Posting
                    </button>
                <?php endif ?>
            <?php endif ?>
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
                </div>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-start mb-3">
                <div class="col-md-4">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" id="filterEmployeeName" placeholder="Ketik nama Employee" />
                </div>
                <div class="col-md-4">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" id="filterDivisiName" placeholder="Ketik Divisi" />
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
                                <th onclick="changeSort('hadir')" class="sort">Hadir</th>
                                <th onclick="changeSort('ijin')" class="sort">Ijin</th>
                                <th onclick="changeSort('alpha')" class="sort">Alpha</th>
                                <th onclick="changeSort('cuti')" class="sort">Cuti</th>
                                <th onclick="changeSort('sakit')" class="sort">Sakit</th>
                                <th onclick="changeSort('libur')" class="sort">Libur</th>
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
                data.name = $('#filterEmployeeName').val();
                data.divisi = $('#filterDivisiName').val();
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
                data: "hadir",
                className: "text-center"
            },
            {
                data: "ijin",
                className: "text-center"
            },
            {
                data: "alpha",
                className: "text-center"
            },
            {
                data: "cuti",
                className: "text-center"
            },
            {
                data: "sakit",
                className: "text-center"
            },
            {
                data: "libur",
                className: "text-center"
            },

        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Data payroll belum digenerate", // Change this line
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

    $("#filterEmployeeName").keyup(function() {
        table.ajax.reload();
    });
    $("#filterDivisiName").keyup(function() {
        table.ajax.reload();
    });

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
                setLoading();
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
                        stopLoading()
                    },
                    onError: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan pada sistem',
                            confirmButtonColor: '#4e73df',
                        });
                        stopLoading()
                    }
                });
            }
            stopLoading();
        });

    });

    // psoting
    $('#posting').click(function(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'question',
            title: 'Posting Payroll (Setelah posting maka data payroll dibawah tidak dapat diposting ulang) ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {

            if (result.isConfirmed) {
                setLoading();
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
                    url: "<?= base_url("payroll/posting"); ?>",
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
                        }
                        stopLoading()
                    },
                    onError: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan pada sistem',
                            confirmButtonColor: '#4e73df',
                        });
                        stopLoading()
                    }
                });
            }
            stopLoading();
        });
    });

    // Get and Show
    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        let id = data.id;
        let name = data.name;
        let divisi = data.divisi;
        let hadir = data.hadir;
        let sakit = data.sakit;

        $('#divisiName').val(data.divisi);
        $('#employeesName').val(data.name);
        $('#totalHadir').val((data.hadir == null) ? '-' : data.hadir + ' Kali');
        $('#totalIjin').val((data.ijin == null) ? '-' : data.ijin + ' Kali');
        $('#totalAlpha').val((data.alpha == null) ? '-' : data.alpha + ' Kali');
        $('#totalCuti').val((data.cuti == null) ? '-' : data.cuti + ' Kali');
        $('#totalLibur').val((data.libur == null) ? '-' : data.libur + ' Kali');
        $('#totalSakit').val((data.sakit == null) ? '-' : data.sakit + ' Kali');

        const csrf = $(`[name="${csrfToken}"]`);
        // append to form
        var formData = new FormData();
        formData.append('payrollID', data.id);
        // generate action
        $.ajax({
            url: "<?= base_url("payroll/detail"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {
                var table = $('#tabelGaji');
                table.find('tbody').empty();

                $.each(response.data.gaji, function(index, data) {
                    var newRow = $('<tr>');
                    newRow.append($('<td>').text(data.name)); // Ganti 'field1' dengan nama kolom yang sesuai
                    newRow.append($('<td>').text(data.nominal != null ? formatRupiah(data.nominal) : 0)); // Ganti 'field2' dengan nama kolom yang sesuai
                    table.append(newRow);
                });
            },
            onError: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan pada sistem',
                    confirmButtonColor: '#4e73df',
                });
            }
        });

        $('#detailPayroll').modal('show');
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
</script>

<?= $this->endSection(); ?>