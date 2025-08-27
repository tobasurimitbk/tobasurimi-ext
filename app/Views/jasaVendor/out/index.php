<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Jasa Vendor Barang Keluar</h1>
        <?php if (can("Jasa Vendor", "Barang Keluar", "c")) : ?>
            <a href="<?= base_url('jasa-vendor-out/create') ?>" type="button" class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <div class="row mb-4">
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($dataDivisi as $divisi) : ?>
                                <option value="<?= $divisi["id"]; ?>"><?= strtoupper($divisi["divisi"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Departemen</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                            <option value=""></option>
                        </select>
                        <label style="z-index: 1;">Warehouse</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select status" id="status" name="status" aria-label="Floating label select example">
                            <option value="">SEMUA</option>
                            <option value="1">POSTED</option>
                            <option value="0">WAITING</option>
                        </select>
                        <label style="z-index: 1;">Status Posting</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" class="form-control input-picker start_date" id="start_date" name="start_date" placeholder="Tanggal Dibuat" />
                                <label for="floatingInput">Tanggal Mulai</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" class="form-control input-picker end_date" id="end_date" name="end_date" placeholder="Tanggal Selesai" />
                                <label for="floatingInput">Tanggal Selesai</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Surat Jalan </label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th onclick="changeSort('no_surat_jalan')">No Surat Jalan</th>
                            <th onclick="changeSort('jasa_vendor_out.createdAt')">Tanggal</th>
                            <th onclick="changeSort('divisi_id')">Departemen</th>
                            <th onclick="changeSort('warehouse_id')">Warehouse</th>
                            <th>Barang</th>
                            <th>Total Item</th>
                            <th onclick="changeSort('vendor_id')">Vendor</th>
                            <th onclick="changeSort('status_closed')">Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table" style="cursor: pointer;">
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
            url: "<?= base_url("jasa-vendor-out/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.divisi_id = $(".divisi_id").val();
                data.warehouse_id = $(".warehouse_id").val();
                data.status = $(".status").val();
                data.start_date = $(".start_date").val();
                data.end_date = $(".end_date").val();
                data.no_surat_jalan = $(".no_surat_jalan").val();
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
                data: "no_surat_jalan",
                className: "text-center",

            },
            {
                data: "tanggal",
                className: "text-center"
            },
            {
                data: "divisi",
                className: "text-center",
            },
            {
                data: "warehouse_name",
                className: "text-center"
            },
            {
                data: "barang_name",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "total_item",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "vendor_name",
                className: "text-center"
            }, {
                data: "status_closed",
                className: "text-center"
            }, {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let status = row.status_posting
                    let status_closed = row.status_closed;
                    let un_posting = row.un_posting;

                    if (status === "0") {
                        return `
                        <div class="mt-0">
                        <?php if (can('Jasa Vendor', 'Barang Keluar', 'a')) : ?>
                            <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Jasa Vendor', 'Barang Keluar', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("jasa-vendor-out/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        <?php if (can('Jasa Vendor', 'Barang Keluar', 'd')) : ?>
                            <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        </div>
                    `
                    } else {
                        var res = '';

                        if (status_closed != "CLOSED") {
                            res += `
                                <button data-toggle="tooltip" title="Close" onclick="closed('${id}')" class="btn btn-danger posting-spp">
                                     <i class="fa fa-xmark fa-sm" aria-hidden="true"></i> 
                                </button>
                            `;

                            if (un_posting) {
                                res += `
                                    <button data-toggle="tooltip" title="Un Posting" onclick="unPosting('${id}')" class="btn btn-danger posting-spp">
                                        <i class="fa-solid fa-ban"></i>    
                                    </button>
                                `;

                            }

                        }

                        <?php if (can('Jasa Vendor', 'Barang Keluar', 'p')) : ?>
                            res += `
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("jasa-vendor-out/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            `;

                        <?php endif; ?>


                        return res;

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

    $(".start_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".end_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });
        table.ajax.reload();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('#status').select2({
        placeholder: "Pilih Status",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $("#divisi_id,#warehouse_id,#status")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    $(".no_surat_jalan").keyup(function() {
        table.ajax.reload();
    })

    $('.start_date,.end_date').change(function() {
        table.ajax.reload();
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("jasa-vendor-out/id"); ?>/${data.id}`);
    });


    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Jasa Vendor Barang Keluar ?',
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
                    url: "<?= base_url("jasa-vendor-out/posting"); ?>",
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

    const unPosting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Unposting Jasa Vendor Barang Keluar ?',
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
                    url: "<?= base_url("jasa-vendor-out/unposting"); ?>",
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
            title: 'Hapus Jasa Vendori Barang Keluar ?',
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
                    url: "<?= base_url("jasa-vendor-out/delete"); ?>",
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


    const closed = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Close Jasa Vendori Barang Keluar ?',
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
                    url: "<?= base_url("jasa-vendor-out/close"); ?>",
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