<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Penerimaan Mutasi PPBKB</h1>
        <?php if (can("Inventori", "Penerimaan Mutasi", "c")) : ?>
            <a href="<?= base_url('penerimaan-mutasi/create') ?>" type="button" class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('penerimaan-mutasi/lokal') ?>">Penerimaan Mutasi Lokal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Penerimaan Mutasi PPBKB</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('penerimaan-mutasi/global') ?>">Penerimaan Mutasi BC 2.7 (BC 2.7 IN)</a>
                </li>
            </ul>
            <?= csrf_field() ?>
            <div class="row mb-4">
                <div class="col-md-4 mt-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" value="01/<?= date('m/Y') ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data </label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th onclick="changeSort('penerimaan_mutasi.penerimaan_mutasi_no')">No Penerimaan</th>
                            <th onclick="changeSort('penerimaan_mutasi.divisi_id')">Dept</th>
                            <th onclick="changeSort('penerimaan_mutasi.tanggal')">Tanggal</th>
                            <th onclick="changeSort('penerimaan_mutasi.multiple_no_mutasi')">No Mutasi</th>
                            <th>No Ppbkb</th>
                            <th onclick="changeSort('penerimaan_mutasi.status_posting')">Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>



<script>
    let sort = "createdAt";
    let sortType = "desc";
    const csrfToken = '<?= csrf_token() ?>';

    const table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("penerimaan-mutasi/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $("#search").val();
                data.dateStart = $('#dateStart').val();
                data.dateEnd = $('#dateEnd').val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                orderable: false
            },
            {
                data: "penerimaan_mutasi_no",
            },
            {
                data: "divisi",
            },
            {
                data: "tanggal",
            },
            {
                data: "multiple_no_mutasi",
            },
            {
                data: "no_ppbkb",
                className: "text-center",
                orderable: false
            },
            {
                data: "status_posting",
                className: "text-center",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting == "1") {
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
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let status = row.status_posting

                    if (status === "0") {
                        return `
                        <div class="mt-0">
                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'u')): ?>
                            <a href="<?= base_url("penerimaan-mutasi/id"); ?>/${id}" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php endif ?>
                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'a')) : ?>
                            <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("penerimaan-mutasi/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'd')) : ?>
                            <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        </div>
                    `
                    } else {
                        return `
                        <?php if (can('Warehouse', 'Penerimaan Mutasi', 'u')): ?>
                            <a href="<?= base_url("penerimaan-mutasi/id"); ?>/${id}" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php endif ?>
                          <?php if (can('Inventori', 'Penerimaan Mutasi', 'ua')): ?>
                           <button data-toggle="tooltip" title="Unpost" class="btn btn-danger btn-print" onclick="unposting('${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-ban fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif ?>

                        <?php if (can('Inventori', 'Penerimaan Mutasi', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("penerimaan-mutasi/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>

                    `
                    }

                }
            }
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        },
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });


    $(".dateStart").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('#search').keyup(function() {
        table.ajax.reload();
    })

    $('#dateStart,#dateEnd').change(function() {
        table.ajax.reload();
    });


    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Penerimaan Mutasi ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-mutasi/posting"); ?>",
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
                            }).then((result) => {
                                table.ajax.reload()
                            });
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

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Penerimaan Mutasi ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("penerimaan-mutasi/delete"); ?>",
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
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                table.ajax.reload()
                            });
                        }
                    },
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>