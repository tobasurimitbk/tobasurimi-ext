<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Sales Kontrak</h1>
        <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("sales-kontrak/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Create New
            </a>
        <?php endif; ?>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp mb-3">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Start Date" value="01<?= date('/m/Y') ?>">
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
                        <option value="BELUM POSTING">STATUS : NOT POSTED</option>
                        <option value="SUDAH POSTING">STATUS : POSTED</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search Data" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('sales_contract_no')" class="sort">No. SC</th>
                                <th onclick="changeSort('customer_name')" class="sort">Buyer</th>
                                <th onclick="changeSort('dicharge_port')" class="sort">Dicharge Port</th>
                                <th onclick="changeSort('shipment_date')" class="sort">Shipment Date</th>
                                <th onclick="changeSort('createdAt')" class="sort">Creation Date</th>
                                <th class="sort">Unpost Description</th>
                                <th class="sort">Number of Unposts</th>
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

<div class="modal unpost-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Unposting Sales Kontrak</h5>
            </div>
            <form class="form-unposting">
                <div class="modal-body">
                    <input type="hidden" name="id_sales_order" class="id_sales_order" id="id_sales_order">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control date_revision" name="date_revision" id="date_revision" placeholder="Date Revision">
                                <label for="floatingInput">Date Revision</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keterangan_unpost" name="keterangan_unpost" id="keterangan_unpost" placeholder="Keterangan Unpost (Opsional)">
                                <label for="floatingInput">Note Unposting (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3">Back</button>
                    <button type="button" onclick="updateStatus('NEW', '0')" class="btn btn-submit-form btn-submit-detail">Un Posting</button>
                </div>
            </form>


        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "createdAt";
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
            url: "<?= base_url("sales-kontrak/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.status_posting = $(".status_posting").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
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
                orderable: false
            }, {
                data: "sales_contract_no",
                className: "text-left"
            }, {
                data: "customer_name",
                className: "text-left",
            }, {
                data: "dicharge_port",
                className: "text-left",
            }, {
                data: "shipment_date",
                className: "text-left",
            }, {
                data: "createdAt",
                className: "text-left",
            }, {
                data: "keterangan_unpost",
                className: "text-left",
            }, {
                data: "jumlah_unpost",
                className: "text-left",
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let status_posting = row.status_posting;
                    let status_closed = row.status_closed;

                    let res = '';

                    if (status_posting === "0") {
                        res += `
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
                                <button data-toggle="tooltip" title="Delete" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                                <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'c')) : ?>
                                    <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                                        <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        `;
                    }

                    if (status_posting === "1") {
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
                                <button data-toggle="tooltip" title="Un-Posting" onclick="updateStatus('${id}', 'UNPOST')" class="btn btn-danger posting-spp">
                                    <i class="fa-solid fa-ban"></i>    
                                </button>
                            <?php endif; ?>
                            `;
                        }
                        <?php if (can('Penjualan Ekspor', 'Sales Kontrak', 'c')) : ?>
                            res += `
                                <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                                    <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                    }

                    return `
                    <div class="mt-0">
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
            emptyTable: "Data Empty",
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

    $(".date_revision").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(document).on('shown.bs.modal', '.unpost-modal', function() {
        if (!$(this).data('datepicker-initialized')) {
            $(this).find(".date_revision").datepicker({
                todayHighlight: true,
                format: "dd/mm/yyyy",
                orientation: "bottom auto",
                autoclose: true
            });
            $(this).data('datepicker-initialized', true);
        }
    });


    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    const updateStatus = function(id, status) {
        document.activeElement.blur(); // cegah auto focus trigger datepicker

        if (status == "UNPOST") {
            $(".id_sales_order").val(id);
            $(".unpost-modal").modal("show");
        } else {
            if (id == 'NEW') {
                id = $(".id_sales_order").val();
                ket = $(".keterangan_unpost").val();
            } else {
                id = id;
                ket = "-";
            }

            var state = true;
            var date_revision = $('#date_revision').val();
            if (status == 0) {
                // MAU UNPOSTING
                if (date_revision == "") {
                    state = false;
                    Swal.fire({
                        icon: 'error',
                        title: "Form Date Revision Required",
                        confirmButtonColor: '#4e73df',
                    })
                }
            }

            if (state) {
                Swal.fire({
                    icon: 'question',
                    title: status == '1' ? 'Posted ?' : 'Unposted ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Back',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        $(".id_sales_order").val("");
                        $(".keterangan_unpost").val("");
                        $(".unpost-modal").modal("hide");
                        $.ajax({
                            url: "<?= base_url("sales-kontrak/update-status"); ?>",
                            data: {
                                id: id,
                                status: status,
                                keterangan: ket,
                                date_revision: date_revision
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
        }
    }

    const remove = function(id, tipe) {
        Swal.fire({
            icon: 'question',
            title: 'Delete Sales Kontrak ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Back',
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

    function duplicate(id) {
        Swal.fire({
            title: 'Duplicate Sales Kontrak ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, duplicate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke route yang ditentukan
                window.location.href = `/sales-kontrak/duplicate/${id}`;
            }
        });
    }


    const print = function(url) {
        window.open(url, "_blank");
    }

    $(".dataTable_info").addClass("pt-0");

    $(".status_posting, .dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $(".btn-hide-detail").click(function() {
        $(".id_sales_order").val("");
        $(".keterangan_unpost").val("");
        $(".unpost-modal").modal("hide");
    })

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        location.replace(`<?= base_url("sales-kontrak/id/"); ?>${data.id}`);
    })

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