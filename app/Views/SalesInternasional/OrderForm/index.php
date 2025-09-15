<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>
<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Order Form Ekspor</h1>
        <?php if (can('Penjualan Ekspor', 'Order Form', 'c')): ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("order-form-internasional/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add New
            </a>
        <?php endif; ?>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
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
                    <select class="form-select status" name="status" id="status" aria-label="Floating label select example">
                        <option value="ALL" selected>STATUS : ALL</option>
                        <option value="SUDAH POSTING">STATUS : POSTED</option>
                        <option value="BELUM POSTING">STATUS : NOT POSTED</option>
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
                                <th onclick="changeSort('tanggal')" class="sort">Date</th>
                                <!-- <th onclick="changeSort('divisi_id')" class="sort">Department</th> -->
                                <th onclick="changeSort('sales_order_export_no')" class="sort">Order Form No</th>
                                <th onclick="changeSort('customer_po_no')" class="sort">PO No</th>
                                <th onclick="changeSort('customer_name')" class="sort">Buyer</th>
                                <th onclick="changeSort('dicharge_port')" class="sort">Destination</th>
                                <th onclick="changeSort('shipment_date')" class="sort">Shipment Date</th>
                                <th class="sort">Number Unpost</th>
                                <th class="sort">Action</th>
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
                <h5 class="modal-title title-secondary">Unposting Order Form</h5>
            </div>
            <form class="form-unposting">
                <div class="modal-body">
                    <input type="hidden" name="id" class="id" id="id">
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
                    <button type="button" onclick="updateStatus('NEW', '-')" class="btn btn-submit-form btn-submit-detail">Un Posting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal print-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Print Configuration</h5>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form">
                                <label class="<?= session()->get('theme') == 'dark' ? 'text-white' : 'text-dark' ?>">
                                    Display Price in Printout ? (If Active, Price Show in Printout OrderForm)
                                </label>
                                <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;<?= session()->get('theme') == 'dark' ? 'background-color:#474D54' : '' ?>">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input display_price" type="checkbox" value="1" name="display_price" id="display_price">
                                        <label class="form-check-label"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (session()->get('login')->this_company_id == 1 || session()->get('login')->this_company_id == 2): ?>
                            <!-- <div class="col-md-12 mt-3">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select
                                        class="form-select company_id"
                                        aria-label="Floating label select example"
                                        name="company_id"
                                        id="company_id">
                                        <option value=""></option>
                                        <?php foreach ($dataCompany as $d) : ?>
                                            <option value="<?= $d['id'] ?>" <?= $d['id'] == 1 ? 'selected' : '' ?>>
                                                <?= $d['company'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Select Company Head In Printout</label>
                                </div>
                            </div> -->
                        <?php endif; ?>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard btn-hide-print mr-3">Back</button>
                    <button type="button" class="btn btn-submit-form" onclick="printAction()">Print Order Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "tanggal";
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
            url: "<?= base_url("order-form-internasional/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.status = $(".status").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
            orderable: false
        }, {
            data: "tanggal",
            className: "text-left"
        }, {
            data: "sales_order_export_no",
            className: "text-left"
        }, {
            data: "customer_po_no",
            className: "text-left"
        }, {
            data: "customer_name",
            className: "text-left"
        }, {
            data: "dicharge_port",
            className: "text-left"
        }, {
            data: "shipment_date",
            className: "text-left"
        }, {
            data: "jumlah_unpost",
            className: "text-left",
            orderable: false
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                let status = row.status;
                let used = row.used;
                if (status == "NEW") {
                    if (used == "NOT USED") {
                        return `
                        <?php if (can('Penjualan Ekspor', 'Order Form', 'a')): ?>
                            <button data-toggle="tooltip" title="Posting" onclick="updateStatus('${id}', '1')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                   
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'p')): ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'd')): ?>
                            <button data-toggle="tooltip" title="Hapus" type="button" onclick="handleDelete('${id}')" class="btn btn-discard delete-btn btn-trash">
                                <i class="fa fa-trash"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'c')) : ?>
                            <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                                <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                        `
                    } else {
                        return `
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'p')): ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'c')) : ?>
                            <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                                <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                        `
                    }
                } else {
                    if (used == "NOT USED") {
                        return `
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'ua')): ?>
                            <button data-toggle="tooltip" title="Un Posting" onclick="updateStatus('${id}', '0')" type="button" class="btn btn-danger" >
                                <i class="fa fa-ban" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'ua')): ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'c')) : ?>
                            <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                            <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                        `
                    } else {
                        return `
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'p')): ?>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'c')) : ?>
                            <button data-toggle="tooltip" title="Duplicate" onclick="duplicate('${id}')" class="btn duplicate-btn text-white" style="background-color:#B8522A">
                            <i class="fa fa-copy fa-sm" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                        `
                    }
                }
            }
        }],
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

    $(document).ready(function() {
        $(".dataTable_info").addClass("pt-0");

        $(".status,.dateStart,.dateEnd").change(function() {
            table.ajax.reload();
        })

        $(".search").keyup(function() {
            table.ajax.reload();
        })


        $(".date_revision").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

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

        $('#company_id').select2({
            placeholder: "Select Company Head In Printout",
            theme: "bootstrap-5",
            dropdownParent: $('.print-modal')
        });

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
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.company_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $('.icon-dateStart').click(function() {
            $(".dateStart").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".dateEnd").focus();
        });

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("order-form-internasional/id/"); ?>${data.id}`);
        })

        $(".btn-hide-detail").click(function() {
            $(".id").val("");
            $(".keterangan_unpost").val("");
            $(".unpost-modal").modal("hide");
        });

        $('.btn-hide-print').click(function() {
            $('.print-modal').modal('hide');
        });
    })

    const print = function(id) {
        $('.id').val(id);
        $('.print-modal').modal('show');
    }

    const printAction = function() {
        var id = $('.id').val();
        var display_price = $('.display_price').is(':checked');
        <?php if (session()->get('login')->this_company_id == 1 || session()->get('login')->this_company_id == 2): ?>
            var company_id = $('#company_id option:selected').val();
        <?php else: ?>
            var company_id = "<?= session()->get('login')->this_company_id ?>";
        <?php endif; ?>

        if (id == "") {
            alert("Failed Print : Order form not found");
        } else if (company_id == "") {
            alert("Please select company head")
        } else {
            var url = "/order-form-internasional/print/" + id + '?display_price=' + display_price + '&company_id=' + company_id
            window.open(url, "_blank");
        }
    }

    const handleDelete = function(id, tipe) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di hapus?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("order-form-internasional/delete"); ?>",
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

    const updateStatus = function(id, status) {
        if (status == 0) {
            $(".id").val(id);
            $(".unpost-modal").modal("show");
        } else {
            if (id == 'NEW') {
                id = $(".id").val();
                ket = $(".keterangan_unpost").val();
                status = 0;
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
                    title: status == 0 ? 'Unpost ?' : 'Post ?',
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
                            url: "<?= base_url("order-form-internasional/update-status"); ?>",
                            data: {
                                id: id,
                                status: status,
                                date_revision: date_revision,
                                keterangan: ket,
                            },
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                setLoading();
                            },
                            complete: function() {
                                stopLoading();
                                $(".id").val("");
                                $(".keterangan_unpost").val("");
                                $(".unpost-modal").modal("hide");
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


        }
    }

    function duplicate(id) {
        Swal.fire({
            title: 'Duplicate Order Form ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, duplicate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke route yang ditentukan
                window.location.href = `/order-form-internasional/duplicate/${id}`;
            }
        });
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