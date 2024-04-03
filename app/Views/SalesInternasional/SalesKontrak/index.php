<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Sales Kontrak</h1>
        <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("sales-kontrak/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp mb-3">
                <div class="col-md-3">
                    <select class="form-select status_posting" name="status_posting" id="status_posting" aria-label="Floating label select example">
                        <option value="0">NEW</option>
                        <option value="1">POSTED</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari No. SC / No. PO / Buyer" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('sales_contract_no')" class="sort">No. SC</th>
                                <th onclick="changeSort('customer_po_no')" class="sort">No. PO</th>
                                <th onclick="changeSort('customer_name')" class="sort">Buyer</th>
                                <th onclick="changeSort('dicharge_port')" class="sort">Tujuan Pengiriman</th>
                                <th onclick="changeSort('shipment_date')" class="sort">Shipment Date</th>
                                <th onclick="changeSort('createdAt')" class="sort">Tanggal Pembuatan</th>
                                <th>Status</th>
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
    let sort = "createdAt";
    let sortType = "desc";

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
            url: "<?= base_url("sales-kontrak/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.status_posting = $(".status_posting").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        initComplete: function(settings, json) {
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
            }, {
                data: "sales_contract_no",
                className: "text-center"
            }, {
                data: "customer_po_no",
                className: "text-center"
            }, {
                data: "customer_name",
                className: "text-center"
            }, {
                data: "dicharge_port",
                className: "text-center"
            }, {
                data: "shipment_date",
                className: "text-center"
            }, {
                data: "createdAt",
                className: "text-center"
            }, {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let state = '0';
                    if (state == '0') {
                        return '<i class="fa-solid fa-square text-danger"></i>';
                    } else {
                        return '<i class="fa-solid fa-square text-success"></i>';
                    }
                },
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row?.id;
                    let status_posting = row?.status_posting;
                    let status_closed = row.status_closed;

                    if (status_posting === "0") {
                        return `
                        <div class="mt-0">
                            <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("sales-kontrak/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'a')) : ?>
                                <button data-toggle="tooltip" title="Posting" onclick="updateStatus('${id}', 1)" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'd')) : ?>
                                <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    `;
                    }
                    if (status_posting === "1") {
                        var res = '';
                        res += `
                        <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("sales-kontrak/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    `;

                        if (status_closed == "0") {
                            res += `
                            <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'ua')) : ?>
                                <button data-toggle="tooltip" title="Un-Posting" onclick="updateStatus('${id}', 0)" class="btn btn-danger posting-spp">
                                    <i class="fa-solid fa-ban"></i>    
                                </button>
                            <?php endif; ?>
                        `;
                        }

                        return `
                        <div class="mt-0">
                            ${res}
                        </div>
                    `;
                    }
                }
            }
        ],
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


    const updateStatus = function(id, status) {
        Swal.fire({
            icon: 'question',
            title: status == '1' ? 'Yakin akan diposting ?' : 'Batalkan Posting ?',
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
                    url: "<?= base_url("sales-kontrak/update-status"); ?>",
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

    const remove = function(id, tipe) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di hapus?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("sales-kontrak/delete"); ?>",
                    data: {
                        id: id,
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


    const print = function(url) {
        window.open(url, "_blank");
    }

    $(".dataTable_info").addClass("pt-0");

    $(".status_posting").change(function() {
        table.ajax.reload();
    })

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("sales-kontrak/id/"); ?>${data.id}`);
    })
</script>

<?= $this->endSection(); ?>