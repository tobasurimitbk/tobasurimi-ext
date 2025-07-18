<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Pembayaran PO Lokal Bahan Baku</h1>
        <?php if (can('Pembayaran', 'Lokal BB', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("pembayaran-po-lokal-bb/create"); ?>">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker startDate" id="startDate" name="startDate" placeholder="Tanggal Mulai">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input value="<?= date('d/m/Y') ?>" autocomplete="one-time-code" class="form-control input-picker paymentDate" id="paymentDate" name="paymentDate" placeholder="Tanggal Selesai">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <select class="form-select type_bayar" name="type_bayar" id="type_bayar" aria-label="Floating label select example">
                        <option value="All">TIPE BAYAR : SEMUA</option>
                        <option value="Harian">TIPE BAYAR : HARIAN</option>
                        <option value="Bulanan">TIPE BAYAR : BULANAN</option>
                    </select>
                </div>
                <div class="col mb-3">
                    <select class="form-select status_posting" name="status_posting" id="status_posting" aria-label="Floating label select example">
                        <option value="ALL">STATUS : SEMUA</option>
                        <option value="SUDAH POSTING">STATUS : SUDAH POSTING</option>
                        <option value="BELUM POSTING">STATUS : BELUM POSTING</option>
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
                                <th>No. Pembayaran</th>
                                <th>Tipe Bayar</th>
                                <th>Supplier</th>
                                <th>PO Number</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Metode Pembayaran</th>
                                <th>Total Bayar</th>
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
    let sort = "id";
    let sortType = "desc";
    const csrfToken = '<?= csrf_token() ?>';

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
            url: "<?= base_url("pembayaran-po-lokal/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.startDate = $(".startDate").val();
                data.paymentDate = $(".paymentDate").val();
                // data.type_po = "Bahan Baku";
                data.type_bayar = $(".type_bayar").val();
                data.status_posting = $(".status_posting").val();
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
                data: "payment_no",
                className: "text-center"
            },
            {
                data: "tipe_bayar",
                className: "text-center"
            },
            {
                data: "supplier",
                className: "text-center"
            },
            {
                data: "po_number",
                className: "text-center"
            },
            {
                data: "payment_date",
                className: "text-center"
            },
            {
                data: "payment_method",
                className: "text-center"
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
                    let divisi_id = row.divisi_id;
                    let status_posting = row.status_posting;
                    let buttons = '';
                    
                    buttons += `<div class="btn-group" role="group">`;
                    
                    // Print button (always visible if has permission)
                    <?php if (can('Pembayaran', 'Lokal BB', 'p')) : ?>
                        buttons += `
                            <button class="btn btn-warning btn-print" 
                                    onclick="print('<?= base_url("pembayaran-po-lokal-bb/print/"); ?>${id}')"
                                    data-toggle="tooltip" title="Print">
                                <i class="fa fa-print fa-sm"></i>
                            </button>`;
                    <?php endif; ?>
                    
                    // Conditional buttons based on posting status
                    if (status_posting == '0') {
                        // Unposted state - show delete and post buttons
                        <?php if (can('Pembayaran', 'Lokal BB', 'd')) : ?>
                            buttons += `
                                <button onclick="remove('${id}')" 
                                        class="btn btn-danger"
                                        data-toggle="tooltip" title="Hapus">
                                    <i class="fa fa-trash fa-sm"></i>
                                </button>`;
                        <?php endif; ?>
                        
                        <?php if (can('Pembayaran', 'Lokal BB', 'a')) : ?>
                            buttons += `
                                <button onclick="posting('${id}', '${divisi_id}', '1')" 
                                        class="btn btn-success"
                                        data-toggle="tooltip" title="Posting">
                                    <i class="fa fa-paper-plane fa-sm"></i>
                                </button>`;
                        <?php endif; ?>
                    } else {
                        // Posted state - show unpost button
                        <?php if (can('Pembayaran', 'Lokal BB', 'a')) : ?>
                            buttons += `
                                <button onclick="posting('${id}', '${divisi_id}', '0')" 
                                        class="btn btn-danger"
                                        data-toggle="tooltip" title="Unpost">
                                    <i class="fa fa-undo fa-sm"></i>
                                </button>`;
                        <?php endif; ?>
                    }
                    
                    buttons += `</div>`;
                    return buttons;
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

    $(document).ready(function() {
        $(".startDate").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".paymentDate").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $('.icon-dateStart').click(function() {
            $(".startDate").focus();
        });

        $('.icon-dateEnd').click(function() {
            $(".paymentDate").focus();
        });

        $(".dataTable_info").addClass("pt-0");

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".startDate, .paymentDate, .type_bayar").change(function() {
            table.ajax.reload();
        })

        $(".status_posting").change(function() {
            table.ajax.reload();
        });

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("pembayaran-po-lokal-bb/id/"); ?>${data.id}`);
        });
    });
    const print = function(url) {
        window.open(url, "_blank");
    }


    const posting = function(id, divisi_id, status) {
        const isPosting = status == 1;
        const titleText = isPosting ? 'Posting Pembayaran?' : 'Unposting Pembayaran?';
        const confirmText = isPosting ? 'Posting' : 'Unposting';

        Swal.fire({
            icon: 'question',
            title: titleText,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("pembayaran-po-lokal-bb/posting"); ?>",
                    data: {
                        id: id,
                        divisi_id: divisi_id,
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
                            }).then(() => {
                                table.ajax.reload();
                            });
                        }
                    },
                });
            }
        });
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
                    url: "<?= base_url("pembayaran-po-lokal-bb/delete"); ?>",
                    data: formData,
                    method: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
</script>
<?= $this->endSection(); ?>