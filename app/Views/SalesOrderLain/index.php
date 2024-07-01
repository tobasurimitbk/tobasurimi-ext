<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Order Form Penjualan Lain Lain</h1>
        <?php if (can("Penjualan Lain", "Order Form", "c")) : ?>
            <a href="<?= base_url('order-form-lain/create') ?>" type="button" class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row justify-content-start row-col-spp">
                <div class="col-md-3 mb-3">
                    <?= csrf_field() ?>
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker tanggal_mulai" id="tanggal_mulai" name="tanggal_mulai" placeholder="Mulai Tanggal Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-tanggal_mulai"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker tanggal_selesai" id="tanggal_selesai" name="tanggal_selesai" placeholder="Selesai Tanggal Dibuat">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-tanggal_selesai"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <select name="status_posting" class="form-select status_posting" id="status_posting">
                        <option selected value="ALL">STATUS POSTING : SEMUA</option>
                        <option value="SUDAH POSTING">STATUS POSTING : SUDAH POSTING</option>
                        <option value="BELUM POSTING">STATUS POSTING : BELUM POSTING</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input autocomplete="one-time-code" class="form-control no_sales_order search form-out-search" id="no_sales_order" placeholder="Cari Nomor Order Form" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No</th>
                                <th onclick="changeSort('tanggal')" style="text-align: center;">Tanggal Order</th>
                                <th onclick="changeSort('divisi_id')" class="sort" style="text-align: center;">Departemen</th>
                                <th onclick="changeSort('no_sales_order')" class="sort" style="text-align: center;">No Order Form</th>
                                <th onclick="changeSort('tipe_customer')" style="text-align: center;">Tipe Customer</th>
                                <th onclick="changeSort('customer_name')" style="text-align: center;">Customer</th>
                                <th style="text-align: center;">Dokumen Pengeluaran</th>
                                <th onclick="changeSort('keterangan')" style="text-align: center;">Keterangan</th>
                                <th style="text-align: center;">Total Barang</th>
                                <th onclick="changeSort('total_harga')" style="text-align: center;">Total Harga</th>
                                <th style="text-align: center;">Action</th>
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
    let sort = "createdAt";
    let sortType = "desc";
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
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
            url: "<?= base_url("order-form-lain/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.mulai_tanggal = $(".mulai_tanggal").val();
                data.selesai_tanggal = $(".selesai_tanggal").val();
                data.status_posting = $(".status_posting").val();
                data.no_sales_order = $(".no_sales_order").val();
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
                data: "tanggal",
                className: "text-center",

            },
            {
                data: "divisi",
                className: "text-center"
            },
            {
                data: "no_sales_order",
                className: "text-center",
            },
            {
                data: "tipe_customer",
                className: "text-center"
            },
            {
                data: "customer_name",
                className: "text-center"
            },
            {
                data: "dokumen_pengeluaran",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "keterangan",
                className: "text-center"
            },
            {
                data: "total_barang",
                className: "text-center",
                searchable: false,
                sortable: false
            },
            {
                data: "total_harga",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let status_posting = row.status_posting
                    let status_used = row.status_used;

                    if (status_posting === "0") {
                        return `
                        <div class="mt-0">
                            <?php if (can('Penjualan Lain', 'Order Form', 'a')) : ?>
                                <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Lain', 'Order Form', 'd')) : ?>
                                <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Lain', 'Order Form', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("order-form-lain/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    `
                    } else {
                        var res = '';
                        <?php if (can('Penjualan Lain', 'Order Form', 'ua')) : ?>
                            if (status_used == '0') {
                                res += `
                        <?php if (can('Penjualan Lain', 'Order Form', 'ua')) : ?>
                                <button data-toggle="tooltip" title="Un-Posting" onclick="unPosting('${id}')" class="btn btn-danger posting-spp">
                                    <i class="fa-solid fa-ban"></i>    
                                </button>
                            <?php endif; ?>
                        `;
                            }
                        <?php endif; ?>
                        <?php if (can('Penjualan Lain', 'Order Form', 'p')) : ?>
                            res += `
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("order-form-lain/print/"); ?>${id}')" style="box-shadow: none !important;">
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

    $(".tanggal_mulai").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".tanggal_selesai").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $(".tanggal_mulai,.tanggal_selesai,.status_posting").change(function() {
        table.ajax.reload();
    });

    $('.no_sales_order').keyup(function() {
        table.ajax.reload();
    });

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("order-form-lain/id"); ?>/${data.id}`);
    });

    const print = function(url) {
        window.open(url, "_blank");
    }

    const unPosting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Batalkan Posting ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("order-form-lain/unposting"); ?>",
                    data: {
                        id: id,
                        status: status
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading();
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
                                })
                                .then(() => {
                                    table.ajax.reload()
                                })
                        }
                    },
                });
            }
        })
    }


    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Order Form ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("order-form-lain/posting"); ?>",
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
                        }
                    },
                });
            }
        })
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Order Form ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("order-form-lain/delete"); ?>",
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

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
        table.ajax.reload();
    }
</script>

<?= $this->endSection(); ?>