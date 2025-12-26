<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Invoice Sample</h1>
    </div>
    <?= csrf_field() ?>
    <div class="card">
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
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search Data" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('no_sample')" class="sort">No. Sample</th>
                                <th onclick="changeSort('no_invoice')" class="sort">No. Invoice</th>
                                <th onclick="changeSort('tanggal_invoice')" class="sort">Tgl Invoice</th>
                                <th onclick="changeSort('customer_id')" class="sort">Delivery To</th>
                                <th onclick="changeSort('total_berat_kotor')" class="sort">Gross Weight (Kg)</th>
                                <th onclick="changeSort('total_berat_bersih')" class="sort">Net Weight (Kg)</th>
                                <th>Action</th>
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
<div class="modal noinvoicemodal" id="noinvoicemodal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Update Nomor Invoice</h5>
            </div>
            <form class="form-noinvoice">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input value="" autocomplete="one-time-code" type="text" class="form-control tanggal_invoice" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal Invoice">
                                <label for="floatingInput">Tgl Invoice</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input value="" autocomplete="one-time-code" type="text" class="form-control no_invoice" id="no_invoice" name="no_invoice" placeholder="No Invoice">
                                <label for="floatingInput">No Invoice</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3" id="btn-hide-noinvoice">Back</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitInvoice">Update Nomor Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "tanggal";
    let sortType = "desc";
    var csrf = $(`[name="${csrfToken}"]`);

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
            url: "<?= base_url("invoice-sample/all-sample"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
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
                data: "no_sample",
                className: "text-left"
            }, {
                data: "no_invoice",
                className: "text-left",
            }, {
                data: "tanggal_invoice",
                className: "text-left",
            }, {
                data: "customer_name",
                className: "text-left",
            }, {
                data: "total_berat_kotor",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            }, {
                data: "total_berat_bersih",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                width: "8%",
                render: function(data, type, row) {
                    let id = row.id;
                    let no_invoice = row.no_invoice;
                    let tanggal_invoice = row.tanggal_invoice;

                    let res = '';

                    res += `
                        <div class="mt-0">
                            <?php if (can('Invoice Exim', 'Sample', 'u')): ?>
                                <a href="#" onclick="updateNoInvoiceModal('${id}', '${no_invoice}', '${tanggal_invoice}')" data-toggle="tooltip" title="Update No Invoice" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Sample', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                         </div>
                        `;

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

    $(".tanggal_invoice").datepicker({
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

    var validator = $(".form-noinvoice").validate({
        rules: {
            no_invoice: {
                required: true
            },
            tanggal_invoice: {
                required: true
            },
        },
        messages: {
            no_invoice: {
                required: "No Invoice Wajib Diisi"
            },
            tanggal_invoice: {
                required: "Tanggal Invoice Wajib Diisi"
            },
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    $('#btn-hide-noinvoice').click(function(e) {
        e.preventDefault();
        $('#noinvoicemodal').modal('hide');
    });

    $('#btnSubmitInvoice').click(function(e) {
        e.preventDefault();
        if ($('.form-noinvoice').valid()) {
            var id = $('#id').val();
            var noInvoice = $('#no_invoice').val();
            var tanggalInvoice = $('#tanggal_invoice').val();

            $.ajax({
                url: "<?= base_url("invoice-sample/update-no-invoice"); ?>",
                data: {
                    id: id,
                    no_invoice: noInvoice,
                    tanggal_invoice: tanggalInvoice
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
                    csrf.val(response.token);
                    if (response.status) {
                        Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            .then(() => {
                                $('#noinvoicemodal').modal('hide');
                                table.ajax.reload();
                            })
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
    });

    function updateNoInvoiceModal(id, noInvoice, tanggalInvoice) {
        $('#id').val(id);
        $('#no_invoice').val(noInvoice);
        $('#tanggal_invoice').val(tanggalInvoice);

        $('#noinvoicemodal').modal('show');
    }

    function print(id) {
        var base_url = "<?= base_url() ?>";
        window.open(base_url + `invoice-sample/print/${id}`, '_blank');
    }

    $(".dataTable_info").addClass("pt-0");

    $(".dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $(".search").keyup(function() {
        table.ajax.reload();
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