<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Retur Purchase Lokal Bahan Penolong</h1>
        <?php if (can("Retur Pembelian", "Retur Lokal BP", "c")) : ?>
            <a href="<?= base_url('retur-po-lokal-bp/create') ?>" type="button" class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <div class="row mb-4">
                <div class="col-sm-4 mt-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control start_date" id="start_date" name="start_date" value="01/<?= date('m/Y') ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control end_date" id="end_date" name="end_date" />
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
                            <th onclick="changeSort('supplier_id')">Supplier</th>
                            <th onclick="changeSort('tanggal_surat_jalan')">Tgl Retur</th>
                            <th onclick="changeSort('no_surat_jalan')">No Surat Jalan</th>
                            <th onclick="changeSort('multiple_spp_id')">No Spp</th>
                            <th onclick="changeSort('multiple_lpb_id')">No Lpb</th>
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
    let sort = "tanggal_surat_jalan";
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
            url: "<?= base_url("retur-po-lokal-bp/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $("#search").val();
                data.start_date = $('#start_date').val();
                data.end_date = $('#end_date').val();
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
                className: "text-left",
                orderable: false
            },
            {
                data: "supplier_name",
                className: "text-left"
            },
            {
                data: "tanggal_surat_jalan",
                className: "text-left"
            },

            {
                data: "no_surat_jalan",
                className: "text-left",
            },
            {
                data: "multiple_spp_no",
                className: "text-left"
            },
            {
                data: "multiple_lpb_no",
                className: "text-left",
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let status_post = row.status_post
                    let status_bc = row.status_bc;

                    if (status_post == "WAITING") {
                        return `
                        <div class="mt-0">
                            <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'u')): ?>
                                <a href="<?= base_url("retur-po-lokal-bp/id"); ?>/${id}" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif ?>
                            <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("retur-po-lokal-bp/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'a')) : ?>
                                <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'd')) : ?>
                                <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    `
                    } else {
                        var string = '';
                        string = `
                           <?php if (can('Retur Pembelian', 'P. Lokal BP', 'u')): ?>
                                <a href="<?= base_url("retur-po-lokal-bp/id"); ?>/${id}" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif ?>
                            <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("retur-po-lokal-bp/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                           `;

                        if (status_bc === 0) {
                            string += `
                            <?php if (can('Retur Pembelian', 'Retur Lokal BP', 'ua')) : ?>
                                <button data-toggle="tooltip" title="Unposting" class="btn btn-danger btn-print" onclick="unposting('${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-ban fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                           `;
                        }

                        return `
                            <div class="mt-0">
                                ${string}
                            </div>
                        `;
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

    $("#start_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $("#end_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('#start_date,#end_date').change(function() {
        // RELOAD
        table.ajax.reload();
    });

    $('#search').keyup(function(e) {
        e.preventDefault();
        table.ajax.reload();
    })

    function posting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Retur Pembelian ?',
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
                    url: "<?= base_url("retur-po-lokal-bp/posting"); ?>",
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

    function unposting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Unposting Retur Pembelian ?',
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
                    url: "<?= base_url("retur-po-lokal-bp/unposting"); ?>",
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
            title: 'Hapus Retur Pembelian ?',
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
                    url: "<?= base_url("retur-po-lokal-bp/delete"); ?>",
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

    const print = function(url) {
        window.open(url, "_blank");
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