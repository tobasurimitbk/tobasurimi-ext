<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Sales Order Internasional</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("order-form-internasional/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-3">
                    <select class="form-select status" name="status" id="status" aria-label="Floating label select example">
                        <option value="NEW">NEW</option>
                        <option value="POSTED">POSTED</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari No. Sales Order Form" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('sales_order_export_no')" class="sort">No. Sales Order Form</th>
                                <th onclick="changeSort('customer_po_no')" class="sort">No. PO</th>
                                <th onclick="changeSort('customer_name')" class="sort">Buyer</th>
                                <th onclick="changeSort('dicharge_port')" class="sort">Tujuan Pengiriman</th>
                                <th onclick="changeSort('shipment_date')" class="sort">Shipment Date</th>
                                <th onclick="changeSort('createdAt')" class="sort">Tanggal Pembuatan</th>
                                <th onclick="changeSort('status')" class="sort">Status</th>
                                <th class="sort">Keterangan Unpost</th>
                                <th class="sort">Jumlah Unpost</th>
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
                <h5 class="modal-title title-secondary">Unposting Sales Order</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_sales_order" class="id_sales_order" id="id_sales_order">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control keterangan_unpost" name="keterangan_unpost" id="keterangan_unpost" placeholder="Keterangan Unpost">
                            <label for="floatingInput">Keterangan Unpost</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="button" onclick="updateStatus('NEW', 'NEW')" class="btn btn-submit-form btn-submit-detail">Un Posting</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "sales_order_export_no";
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
            url: "<?= base_url("order-form-internasional/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.status = $(".status").val();
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
            data: "sales_order_export_no",
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
            data: "status",
            className: "text-center"
        }, {
            data: "keterangan_unpost",
            className: "text-center"
        }, {
            data: "jumlah_unpost",
            className: "text-center"
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
                            <button data-toggle="tooltip" title="Posting" onclick="updateStatus('${id}', 'POSTED')" class="btn btn-success posting-spp">
                                <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" onclick="handleDelete('${id}')" class="btn btn-discard delete-btn btn-trash">
                                <i class="fa fa-trash"></i>
                            </button>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("order-form-lokal/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        `
                    } else {
                        return `
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("order-form-lokal/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        `
                    }
                } else {
                    if (used == "NOT USED") {
                        return `
                            <button data-toggle="tooltip" title="Un Posting" onclick="updateStatus('${id}', 'UNPOST')" type="button" class="btn btn-danger" >
                                <i class="fa fa-ban" aria-hidden="true"></i>
                            </button>
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("order-form-lokal/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        `
                    } else {
                        return `
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("order-form-lokal/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        `
                    }
                }
            }
        }],
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

        $(".status").change(function() {
            table.ajax.reload();
        })

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("order-form-internasional/id/"); ?>${data.id}`);
        })

        $(".btn-hide-detail").click(function() {
            $(".id_sales_order").val("");
            $(".keterangan_unpost").val("");
            $(".unpost-modal").modal("hide");
        })
    })

    const updateStatus = function(id, status) {
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
            Swal.fire({
                icon: 'question',
                title: status == 'POSTED' ? 'Yakin akan diposting ?' : 'Batalkan Posting ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    $(".id_sales_order").val("");
                    $(".keterangan_unpost").val("");
                    $(".unpost-modal").modal("hide");
                    $.ajax({
                        url: "<?= base_url("order-form-internasional/update-status"); ?>",
                        data: {
                            id: id,
                            status: status,
                            keterangan: ket,
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
</script>

<?= $this->endSection(); ?>