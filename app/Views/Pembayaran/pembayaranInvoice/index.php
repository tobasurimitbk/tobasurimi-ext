<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<style>
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Pembayaran Invoice</h1>
        <?php if (can('Transaksi Internasional', 'Pembayaran Invoice', 'c')) : ?>
            <button class="btn btn-discard float-right" 
                    type="button" 
                    id="dropdownMenuButtonExport"
                    aria-expanded="false" 
                    style="margin-right:20px;"
                    onclick="showInvoice()">
                <i class="fa fa-eye fa-sm mr-2" aria-hidden="true"></i>Invoice Belum Lunas
                <span id="invoiceBadge"
                    style="display:none;
                            position:absolute;
                            top:-6px;
                            left:-15px;
                            background-color:#dc3545;
                            color:#fff;
                            border-radius:50%;
                            padding:5px 8px;
                            font-size:11px;
                            font-weight:bold;
                            box-shadow:0 0 8px rgba(220,53,69,0.6);
                            animation:blink 3s infinite;">
                    0
                </span>
            </button>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("pembayaran-invoice/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Mulai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Selesai Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <select class="form-select status_posting" name="status_posting" id="status_posting" aria-label="Floating label select example">
                        <option value="ALL">STATUS : SEMUA</option>
                        <option value="SUDAH POSTING">STATUS : SUDAH POSTING</option>
                        <option value="BELUM POSTING">STATUS : BELUM POSTING</option>
                    </select>
                </div>
                <div class="col mb-3">
                    <select class="form-select type_invoice" name="type_invoice" id="type_invoice" aria-label="Floating label select example">
                        <option value="ALL">Tipe : SEMUA</option>
                        <option value="LOKAL">Tipe : Lokal</option>
                        <option value="EKSPOR">Tipe : Ekspor</option>
                        <option value="LAIN-LAIN">Tipe : Lain-Lain</option>
                    </select>
                </div>
                <div class="col mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('')">No. Pembayaran</th>
                                <th onclick="changeSort('')">Payment Date</th>
                                <th onclick="changeSort('')">Tipe Sales</th>
                                <th onclick="changeSort('')">Customer</th>
                                <th onclick="changeSort('')">Amount</th>
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

<div class="modal add-modal" id="showInvoiceModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 1400px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Data Invoice Belum Lunas</h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-bordered nowrap table-hover-tobasurimi" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Customer / No. Faktur</th>
                                <th width="15%">Termin</th>
                                <th width="15%">Tgl Faktur</th>
                                <th width="15%">Jatuh Tempo</th>
                                <th width="20%">Nilai Faktur</th>
                                <th width="20%">Status</th>
                            </tr>
                        </thead>
                        <tbody class="body-table-invoice" id="body-table-invoice" style="cursor: pointer;">
                            <!-- Data will be inserted here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<script>
    let sort = "pembayaran_invoice.id";
    let sortType = "desc";
    const csrfToken = '<?= csrf_token() ?>';

    const table = $('.dataTable').DataTable({

        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'desc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("pembayaran-invoice/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status_posting = $(".status_posting").val();
                data.type_invoice = $(".type_invoice").val();
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
                orderable: false,
                width: "5%"
            },
            {
                data: "no_pembayaran",
                className: "text-center"
            },
            {
                data: "payment_date",
                className: "text-center"
            },
            {
                data: "tipe_invoice",
                className: "text-center"
            },
            {
                data: "customer_name",
                className: "text-center",
                render: function(data, type, row) {
                    if (data === null || data === undefined || data === "") {
                        return 'DATA IMPORT';
                    }
                    return data;
                }
            },
            {
                data: "amount",
                className: "text-center"
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    let form = '';
                    let status_posting = row.status_posting;


                    form += ` <div class="mt-0">`;
                    if (status_posting == '0') {
                        form += `
                            <?php if (can('Transaksi Internasional', 'Pembayaran Invoice', 'd')) : ?>
                                <button data-toggle="tooltip" title="Hapus" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        `;

                        form += `
                            <?php if (can('Transaksi Internasional', 'Pembayaran Invoice', 'a')) : ?>
                                <button data-toggle="tooltip" title="Posting" onclick="posting('${id}', 1)" class="btn btn-success posting-spp">
                                    <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                        `;
                    } else {
                        form += `
                            <?php if (can('Transaksi Internasional', 'Pembayaran Invoice', 'a')) : ?>
                                <button data-toggle="tooltip" title="Unposting" onclick="unposting('${id}', 1)" class="btn btn-warning unposting-spp">
                                    <i class="fa fa-undo fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>

                        `;
                    }

                    form += ` </div>`;

                    return form;
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

    $(document).ready(function() {

        checkUnpaidInvoice();

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

        $('.icon-dateStart').click(function() {
            $(".dateStart").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".dateEnd").focus();
        });

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd").change(function() {
            table.ajax.reload();
        })

        $(".status_posting").change(function() {
            table.ajax.reload();
        });
        $(".type_invoice").change(function() {
            table.ajax.reload();
        });

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("pembayaran-invoice/id/"); ?>${data.id}`);
        })
    });


    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Pembayaran ?',
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
                    url: "<?= base_url("pembayaran-invoice/posting"); ?>",
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

    const unposting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Batalkan Posting Pembayaran ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ok',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("pembayaran-invoice/unposting"); ?>",
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

    const remove = function(id) {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        Swal.fire({
            icon: 'question',
            title: 'Hapus Pembayaran Ini ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: "<?= base_url("pembayaran-invoice/delete"); ?>",
                    data: formData,
                    method: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                // update table
                                table.ajax.reload();
                            });
                        }
                    },
                });
            }
        });
    }

    function showInvoice() {
        $("#showInvoiceModal").modal('show');

        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        $.ajax({
            method: "GET",
            url: "pembayaran-invoice/all-invoice",
            dataType: "json",
            beforeSend: function(xhr) {
                setLoading();
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            complete: function() {
                stopLoading();
            },
            success: function(response) {
                let html = '';
                let currentCustomer = '';
                let customerRowCount = 0;

                // Group invoices by customer first
                const customers = {};
                response.data.forEach(item => {
                    if (!customers[item.nama_pelanggan]) {
                        customers[item.nama_pelanggan] = {
                            total: 0,
                            invoices: []
                        };
                    }

                    // Convert string to number (remove commas and parse)
                    const amount = parseFloat(item.total_invoice.replace(/,/g, ''));
                    customers[item.nama_pelanggan].total += amount;
                    customers[item.nama_pelanggan].invoices.push(item);
                });

                // Generate table rows
                Object.keys(customers).forEach((customerName, index) => {
                    const customerData = customers[customerName];
                    customerRowCount++;

                    // Format total with thousand separators
                    const formattedTotal = customerData.total.toLocaleString('id-ID');

                    // Add customer summary row
                    html += `
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td>${customerRowCount}</td>
                        <td colspan="2">${customerName}</td>
                        <td></td>
                        <td class="text-right">${formattedTotal}</td>
                        <td></td>
                    </tr>`;

                    // Add invoice detail rows
                    customerData.invoices.forEach(invoice => {
                        html += `
                        <tr>
                            <td></td>
                            <td style="padding-left: 30px;">${invoice.no_faktur}</td>
                            <td>${invoice.terms}</td>
                            <td>${invoice.tanggal_faktur}</td>
                            <td>${invoice.tanggal_jatuh_tempo}</td>
                            <td class="text-right">${invoice.total_invoice}</td>
                            <td>${invoice.status_pembayaran}</td>
                        </tr>`;
                    });
                });

                $('#body-table-invoice').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Gagal memuat data invoice');
            }
        });
    }

    function checkUnpaidInvoice() {
        $.ajax({
            url: "pembayaran-invoice/check-unpaid",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const count = response.total_unpaid || 0;
                const badge = document.getElementById('invoiceBadge');

                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            },
            error: function(xhr, status, error) {
                console.error('Gagal ambil data invoice belum lunas:', error);
            }
        });
    }

</script>
<?= $this->endSection(); ?>