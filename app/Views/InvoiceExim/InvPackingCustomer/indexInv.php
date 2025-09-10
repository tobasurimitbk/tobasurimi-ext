<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List CIPL</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("invoice-packing-customer"); ?>">
                Kembali
            </a>
            <?php if (can('Invoice Exim', 'CIPL', 'c')) : ?>
                <a class="btn btn-show-form btn-success float-right" href="<?= base_url("invoice-packing-customer/create/" . encrypt($dataSalesOrderExport->sales_order_export_id)); ?>">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?= csrf_field() ?>
    <div class="card">

        <div class="card-header">
            <table class="form-label font-weight-bold lable-title">
                <tr>
                    <td style="width: 150px;">No Invoice</td>
                    <td style="width: 10px;">:</td>
                    <td><?= $dataSalesOrderExport->no_invoice ?></td>
                </tr>
                <tr>
                    <td>SC</td>
                    <td>:</td>
                    <td><?= $dataSalesOrderExport->sales_order_export_no ?></td>
                </tr>
                <tr>
                    <td>Buyer / Customer</td>
                    <td>:</td>
                    <td><?= $dataSalesOrderExport->customer_name ?></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td>:</td>
                    <td><?= $dataSalesOrderExport->address ?></td>
                </tr>
                <tr>
                    <td>Payment Term</td>
                    <td>:</td>
                    <td><?= strip_tags($dataSalesOrderExport->payment_term) ?></td>
                </tr>

            </table>
        </div>
        <div class="card-body">
            <div class="row justify-content-end row-col-spp mb-3">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Start Date" value="">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="End Date">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select status_posting" name="status_posting" id="status_posting" aria-label="Floating label select example">
                        <option value="ALL" selected>STATUS : ALL</option>
                        <option value="SUDAH POSTING">STATUS : POSTED</option>
                        <option value="BELUM POSTING">STATUS : NOT POSTED</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search Data" id="search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('id')" style="width: 10px;">No</th>
                                <th onclick="changeSort('no_container')" class="sort">No Container</th>
                                <th onclick="changeSort('no_seal')" class="sort">No Seal</th>
                                <th onclick="changeSort('vessels_name')" class="sort">Vessel's Name</th>
                                <th onclick="changeSort('departure_date')" class="sort">Departure Date</th>
                                <th onclick="changeSort('total_berat_bersih')" class="sort">Berat Bersih</th>
                                <th onclick="changeSort('total_berat_kotor')" class="sort">Berat Kotor</th>
                                <th onclick="changeSort('total_nilai_invoice')" class="sort">Nilai</th>
                                <th onclick="changeSort('valas_id')" class="sort">Valas</th>
                                <th onclick="changeSort('status_posting')" class="sort" style="width: 10px;">Exim</th>
                                <th onclick="changeSort('status_kasir')" class="sort" style="width: 10px;">Kasir</th>
                                <th style="width: 150px;">Action</th>
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


<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "proforma_invoice.createdAt";
    let sortType = "desc";

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
            url: "<?= base_url("invoice-packing-customer/all-invoice"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.sales_order_export_id = "<?= $dataSalesOrderExport->sales_order_export_id ?>";
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
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
                className: "text-left",
            }, {
                data: "no_container",
                className: "text-left"
            }, {
                data: "no_seal",
                className: "text-left"
            }, {
                data: "vessels_name",
                className: "text-left"
            }, {
                data: "departure_date",
                className: "text-left",
            }, {
                data: "total_berat_bersih",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            }, {
                data: "total_berat_kotor",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            }, {
                data: "total_nilai_invoice",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            },
            {
                data: "valas_name",
                className: "text-left",
            },
            {
                data: "status_posting",
                className: "text-center",
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
            },
            {
                data: "status_bayar",
                className: "text-center",
                searchable: false,
                width: "5%",
                render: function(data, type, row) {
                    let htmlRes = '';

                    if (row.status_bayar == 1) {
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
                    let status_posting = row.status_posting;
                    let res = '';

                    if (status_posting == "0") {
                        res += `
                            <?php if (can('Invoice Exim', 'CIPL', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("invoice-packing-customer/print/"); ?>${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Invoice Exim', 'CIPL', 'a')) : ?>
                                <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Invoice Exim', 'CIPL', 'd')) : ?>
                                <button data-toggle="tooltip" title="Delete" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                                <?php if (can('Invoice Exim', 'CIPL', 'c')) : ?>
                                    <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                                        <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>
                        `;
                    }

                    if (status_posting == "1") {
                        res += `
                        <?php if (can('Invoice Exim', 'CIPL', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("invoice-packing-customer/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        `;

                        res += `
                            <?php if (can('Invoice Exim', 'CIPL', 'ua')) : ?>
                                <button data-toggle="tooltip" title="Un-Posting" onclick="unposting('${id}')" class="btn btn-danger posting-spp">
                                    <i class="fa-solid fa-ban"></i>    
                                </button>
                            <?php endif; ?>
                            `;

                        <?php if (can('Invoice Exim', 'CIPL', 'c')) : ?>
                            res += `
                                <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                                    <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>


                    }

                    return `
                        <div class="mt-0">
                         <?php if (can('Invoice Exim', 'CIPL', 'u')) : ?>
                            <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php endif; ?>
                            ${res}
                        </div>
                    `;
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
            emptyTable: "Tidak ada data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $(".dateStart,.dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })


    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $(".dataTable_info").addClass("pt-0");

    $(".status_posting, .dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    function print(url) {
        window.open(url, '_blank');
    }

    function edit(id) {
        window.location.href = "<?= base_url('invoice-packing-customer/id') ?>" + '/' + id
    }

    function duplicate(id) {
        window.location.href = "<?= base_url('invoice-packing-customer/duplicate') ?>" + '/' + id
    }

    function posting(id) {
        Swal.fire({
            icon: 'question',
            title: "Posting Data ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Back',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("invoice-packing-customer/posting"); ?>",
                    data: {
                        id: id,
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading()
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
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url('invoice-packing-customer/detail/' . encrypt($dataSalesOrderExport->sales_order_export_id)) ?>"
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
            title: "Unposting Data ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Back',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("invoice-packing-customer/unposting"); ?>",
                    data: {
                        id: id,
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading()
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
                                })
                                .then(() => {
                                    location.reload();
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
            title: "Hapus Data ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Back',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("invoice-packing-customer/delete"); ?>",
                    data: {
                        id: id,
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading()
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
                                })
                                .then(() => {
                                    location.reload();
                                })
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