<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List Proforma Invoice</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("proforma-invoice"); ?>">
                Kembali
            </a>
            <?php if (can('Invoice Exim', 'Proforma Invoice', 'c')) : ?>
                <a class="btn btn-show-form btn-success float-right" href="<?= base_url("proforma-invoice/create/" . encrypt($dataSalesOrderExport->sales_order_export_id)); ?>">
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
                <tr>
                    <td>Nilai PEB</td>
                    <td>:</td>
                    <td>
                        <?= "(" . $dataSalesOrderExport->mata_uang . ") " . number_format($dataSalesOrderExport->shipment_value, 2) ?>
                    </td>
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
                                <th onclick="changeSort('proforma_invoice.id')" style="width: 10px;">No</th>
                                <th onclick="changeSort('proforma_invoice.no_pi')" class="sort">No PI</th>
                                <th onclick="changeSort('proforma_invoice.tanggal_pi')" class="sort">Tgl PI</th>
                                <th class="sort">Term Of Payment</th>
                                <th onclick="changeSort('proforma_invoice.total_pi')" class="sort">Total PI</th>
                                <th onclick="changeSort('proforma_invoice.status_posting')" class="sort" style="width: 10px;">Exim</th>
                                <th onclick="changeSort('proforma_invoice.status_bayar')" class="sort" style="width: 10px;">Kasir</th>
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
<?php if (in_array(session()->get('login')->this_company_id, [1, 2])): ?>
    <div class="modal kopsurat-modal" tabindex="1">
        <div class="modal-dialog" style="min-width: 900px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title title-secondary">Pilih Kop Surat</h5>
                </div>
                <form class="form-kop-surat">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select
                                        class="form-select company_id"
                                        aria-label="Floating label select example"
                                        name="company_id"
                                        id="company_id">
                                        <option value=""></option>
                                        <?php foreach ($dataCompany as $d) : ?>
                                            <option value="<?= $d['id'] ?>" <?= $d['id'] == session()->get('login')->this_company_id ? 'selected' : '' ?>>
                                                <?= $d['company'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Pilih Kop Surat Printout</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-hide-detail btn-discard mr-3" id="btn-hide-kopsurat">Back</button>
                        <button type="button" onclick="print2()" class="btn btn-submit-form">Print</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

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
            url: "<?= base_url("proforma-invoice/all-pi"); ?>",
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
                data: "no_pi",
                className: "text-left"
            }, {
                data: "tanggal_pi",
                className: "text-left"
            }, {
                data: "payment_term",
                className: "text-left",
            }, {
                data: "total_pi",
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
                            <?php if (can('Invoice Exim', 'Proforma Invoice', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Invoice Exim', 'Proforma Invoice', 'a')) : ?>
                                <button data-toggle="tooltip" title="Posting" onclick="posting('${id}')" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Invoice Exim', 'Proforma Invoice', 'd')) : ?>
                                <button data-toggle="tooltip" title="Delete" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                                <?php if (can('Invoice Exim', 'Proforma Invoice', 'c')) : ?>
                                    <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                                        <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>
                        `;
                    }

                    if (status_posting == "1") {
                        res += `
                        <?php if (can('Invoice Exim', 'Proforma Invoice', 'p')) : ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                        `;

                        res += `
                            <?php if (can('Invoice Exim', 'Proforma Invoice', 'ua')) : ?>
                                <button data-toggle="tooltip" title="Un-Posting" onclick="unposting('${id}')" class="btn btn-danger posting-spp">
                                    <i class="fa-solid fa-ban"></i>    
                                </button>
                            <?php endif; ?>
                            `;

                        <?php if (can('Invoice Exim', 'Proforma Invoice', 'c')) : ?>
                            res += `
                                <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                                    <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                                </button>
                            `;
                        <?php endif; ?>
                    }

                    return `
                        <div class="mt-0">
                         <?php if (can('Invoice Exim', 'Proforma Invoice', 'u')) : ?>
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
    });

    $('#btn-hide-kopsurat').click(function(e) {
        e.preventDefault();
        $('.kopsurat-modal').modal('hide');
    });

    $(".search").keyup(function() {
        table.ajax.reload();
    });

    $('.company_id').select2({
        placeholder: "Pilih Kop Surat",
        theme: "bootstrap-5",
        dropdownParent: $('.kopsurat-modal')
    }).change(function() {});

    $('.company_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.company_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.company_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    function edit(id) {
        window.location.href = "<?= base_url('proforma-invoice/id') ?>" + '/' + id
    }

    function duplicate(id) {
        window.location.href = "<?= base_url('proforma-invoice/duplicate') ?>" + '/' + id
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
                    url: "<?= base_url("proforma-invoice/posting"); ?>",
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
                                    table.ajax.reload()
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
                    url: "<?= base_url("proforma-invoice/unposting"); ?>",
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
                                    table.ajax.reload()
                                })
                        }
                    },
                });
            }
        })

    }

    function print(id) {
        $('#id').val(id);
        <?php if (in_array(session()->get('login')->this_company_id, [1, 2])): ?>
            $('.kopsurat-modal').modal('show');
        <?php else: ?>
            var companyId = "<?= session()->get('login')->this_company_id; ?>";
            var url = "<?= base_url('proforma-invoice/print/') ?>" + id + '?company_id=' + companyId;
            window.open(url, "_blank");
        <?php endif; ?>
    }

    function print2() {
        var id = $('#id').val();
        var companyId = $('#company_id').val();
        if (companyId == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih kop surat perusahaan",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            var url = "<?= base_url('proforma-invoice/print/') ?>" + id + '?company_id=' + companyId;
            window.open(url, "_blank");
        }
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