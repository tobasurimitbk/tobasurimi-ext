<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Mutasi Lokal (Pengeluaran Barang)</h1>
        <?php if (can("Inventori", "Mutasi", "c")) : ?>
            <a href="<?= base_url('mutasi/create-lokal') ?>" type="button" class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Mutasi Lokal (Pengeluaran Barang)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('mutasi') ?>">Mutasi PPBKB</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('mutasi/global') ?>">Mutasi BC 2.7</a>
                </li>
            </ul>

            <?= csrf_field() ?>
            <div class="row mb-4 mt-3">
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
                            <th onclick="changeSort('tanggal')">Tanggal</th>
                            <th onclick="changeSort('no_mutasi')">No Mutasi</th>
                            <th onclick="changeSort('divisi_asal_id')">Dept Asal</th>
                            <th onclick="changeSort('warehouse_asal_id')">Warehouse Asal</th>
                            <th onclick="changeSort('divisi_tujuan_id')">Dept Tujuan</th>
                            <th onclick="changeSort('warehouse_tujuan_id')">Warehouse Tujuan</th>
                            <th onclick="changeSort('status_posting')">Posting</th>
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
    let sort = "no_mutasi";
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
            url: "<?= base_url("mutasi/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $("#search").val();
                data.dateStart = $('#dateStart').val();
                data.dateEnd = $('#dateEnd').val();
                data.tipe_mutasi = "LOKAL";
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
                orderable: false
            },
            {
                data: "tanggal",
            },
            {
                data: "no_mutasi",
            },
            {
                data: "divisi_asal",
            },
            {
                data: "warehouse_asal",
            },
            {
                data: "divisi_tujuan",
            },
            {
                data: "warehouse_tujuan",
            },
            {
                data: "status_posting",
                searchable: false,
                width: "5%",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_posting == 1) {
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
                render: function(data, type, row) {
                    let id = row.id;
                    let status = row.status_posting

                    if (status == 0) {
                        return `
                        <div class="mt-0">
                          <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php if (can('Inventori', 'Mutasi', 'a')) : ?>
                            <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Inventori', 'Mutasi', 'd')) : ?>
                            <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                           <?php if (can('Inventori', 'Mutasi', 'p')) : ?>
                                <a data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" href="<?= base_url("mutasi/print-lokal/"); ?>${id}" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    `
                    } else {
                        if (row.no_ppbkb == null) {
                            return `
                                <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (can('Inventori', 'Mutasi', 'ua')) : ?>
                                    <button data-toggle="tooltip" title="Un-Posting" onclick="unposting('${id}')" class="btn btn-danger posting-spp">
                                        <i class="fa-solid fa-ban"></i>    
                                    </button>
                                <?php endif; ?>
                                   <?php if (can('Inventori', 'Mutasi', 'p')) : ?>
                                <a data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" href="<?= base_url("mutasi/print-lokal/"); ?>${id}" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                            `;
                        } else {
                            return `
                              <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php if (can('Inventori', 'Mutasi', 'p')) : ?>
                                <a data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" href="<?= base_url("mutasi/print-lokal/"); ?>${id}" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                            `;
                        }
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
    });

    $(".dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#dateStart,#dateEnd').change(function() {
        table.ajax.reload();
    });

    $(".search").keyup(function() {
        table.ajax.reload();
    });

    function edit(id) {
        window.location.href = "<?= base_url('mutasi/id-lokal') ?>" + '/' + id
    }

    function posting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Mutasi ?',
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
                    url: "<?= base_url("mutasi/posting"); ?>",
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

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Mutasi ?',
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
                    url: "<?= base_url("mutasi/delete"); ?>",
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
        })
    }

    function unposting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Un Posting Mutasi ?',
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
                    url: "<?= base_url("mutasi/un-posting"); ?>",
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