<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Karyawan</h1>
        <button class="btn btn-sync btn-add" float-right style="right:130px;">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Sync Karyawan ke Fingerprint
        </button>
        <a class="btn btn btn-show-form btn-save float-right" href="<?= base_url('employee/create') ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
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
                <div class="col-md-3 mt-2">
                    <button class="btn btn-primary btn-lg mt-1" id="btn-reset-filter">
                        <i class="fa-solid fa-rotate-right mr-1"></i>
                        Reset Filter
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('employees.nip')" class="sort">NIP</th>
                                <th onclick="changeSort('employees.name')" class="sort">Nama Lengkap</th>
                                <th onclick="changeSort('employees.division_id')" class="sort">Departemen</th>
                                <th onclick="changeSort('employees.bagian_id')" class="sort">Bagian</th>
                                <th onclick="changeSort('employees.tipe')" class="sort">Tipe/Gol</th>
                                <th onclick="changeSort('employees.dob')" class="sort">Tanggal Lahir</th>
                                <th onclick="changeSort('employees.gender')" class="sort">Jenis Kelamin</th>
                                <th onclick="changeSort('employees.attendance_sync')" class="sort">Fingerprint</th>
                                <th onclick="changeSort('employees.status')" class="sort">Status</th>
                                <th style="width: 10px;">Jam Kerja</th>
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
<div class="modal sync-fingerprint" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sinkronisasi Karyawan ke Fingerprint Master</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            Apakah anda yakin untuk Sinkronisasi Karyawan ke Fingerprint Master?
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-copy btn-discard mr-2">Kembali</button>
                <button type="button" class="btn btn-submit-form btn-process-form">Process</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "createdAt";
    let sortType = "desc";

    var row = 0;

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
            className: "text-center"
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "divisionName",
            className: "text-center"
        }, {
            data: "bagianName",
            className: "text-center"
        }, {
            data: "tipe",
            className: "text-center"
        }, {
            data: "dob",
            className: "text-center"
        }, {
            data: "gender",
            className: "text-center"
        }, {
            data: "attendance_sync",
            className: "text-center",
            render: function(data, type, row) {
                let attendance_sync = row.attendance_sync;
                if (attendance_sync == 1) {
                    return `
                        <span class="badge badge-success">SINKRON</span>
                    `
                } else {
                    return `
                        <span class="badge badge-danger">BELUM SINKRON</span>
                    `
                }

            }
        }, {
            data: "status",
            className: "text-center"
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            width: "10%",
            render: function(data, type, row) {
                let id = row.id;
                return `
                    <button data-toggle="tooltip" title="Atur Jam Kerja" onclick="jamKerjaAction('${row.id}')" class="btn btn-success">
                    <i class="fas fa-user-clock"></i>
                    </button>
                    `
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

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("employee/id"); ?>/${data.id}`);
    });

    $('#division_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
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
    }).change(function() {
        table.ajax.reload();
    });

    $('#tipe').select2({
        placeholder: "Pilih Tipe / Golongan",
        theme: "bootstrap-5",
    }).change(function() {
        table.ajax.reload();
    });

    $('.search').keyup(function() {
        table.ajax.reload();
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px').css('height', ' calc(3.5rem + 2px)');

    $('#btn-reset-filter').click(function() {
        $("select[name='bagian_id']").empty()
        $('#division_id').val(null).change();
        $('#bagian_id').val(null).change();
        $('#tipe').val(null).change();
        $('#search').val('');
    });

    $('.btn-sync').click(function(e) {
        e.preventDefault();
        $('.sync-fingerprint').modal('show');
    });

    $('.btn-discard').click(function() {
        $('.sync-fingerprint').modal('hide');

    })

    $(".btn-process-form").click(function() {
        const csrf = $(`[name="${csrfToken}"]`);
        $.ajax({
            url: "<?= base_url("api/employees-sync-attendances"); ?>",
            beforeSend: function(xhr) {
                setLoading();
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            complete: function() {
                stopLoading();
            },
            method: "GET",
            success: function(response) {
                csrf.val(response.token);
                if (response.status) {
                    Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                        .then(() => {
                            $(".sync-fingerprint").modal("hide")
                        })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    })
                }
            },
            onError: function(response) {
                csrf.val(response.token);
                Swal.fire({
                    icon: 'error',
                    title: 'Data Gagal Disimpan, coba Lagi',
                    confirmButtonColor: '#4e73df',
                })
            }
        });
    });


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
</script>

<?= $this->endSection(); ?>