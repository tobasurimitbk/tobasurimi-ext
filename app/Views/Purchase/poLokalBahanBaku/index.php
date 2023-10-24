<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>PO Lokal Bahan Baku</h1>
    <a class="btn btn-show-form btn-add float-right" href="<?= base_url("po-lokal-bahan-baku/create"); ?>">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </a>
</div>
<div class="card">
    <?= csrf_field() ?>
    <div class="card-body">
        <div class="row justify-content-end row-col-spp">
            <div class="col mb-3">
                <div class="input-group input-group-password">
                    <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="input-group input-group-password">
                    <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Ketik No PO" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th onclick="changeSort('poDate')" class="sort">Tanggal Dibuat</th>
                            <th onclick="changeSort('companyName')" class="sort">Company</th>
                            <th onclick="changeSort('poNo')" class="sort">No. PO</th>
                            <th onclick="changeSort('supplier')" class="sort">Supplier</th>
                            <th onclick="changeSort('total')" class="sort">Total</th>
                            <th>Order</th>
                            <th onclick="changeSort('statusPenerimaan')" class="sort">Status</th>
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
    let sort = "poDate";
    let sortType = "desc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[1, 'desc']],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("po-lokal-bahan-baku/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.status = $(".status").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function (settings, json) {    
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
        },
        {
            data: "po_date",
            className: "text-center"
        },
        {
            data: "companyName",
            className: "text-center"
        },
        {
            data: "po_no",
            className: "text-center"
        },
        {
            data: "supplierName",
            className: "text-center"
        },
        {
            data: "total",
            className: "text-center"
        },
        {
            data: "itemCount",
            className: "text-center",
            searchable: false,
            sortable: false,
        },
        {
            data: "status_penerimaan",
            className: "text-center"
        },
        {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row?.id;
                let status = row?.is_posted
                let status_penerimaan = row?.status_penerimaan
                let purchase_request_id = row?.purchase_request_id

                // jika belum posting
                if (status !== "1") {
                    return `
                        <div class="mt-0">
                        <button class="btn btn-warning btn-print" onclick="print('<?= base_url("po-lokal-bahan-baku/print/"); ?>${id}')" style="box-shadow: none !important;">
                            <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                        </button>
                        <button onclick="posting(${id}, ${purchase_request_id})" class="btn btn-success posting-spp">
                            <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
                        </button>
                        <button onclick="remove(${id})" class="btn btn-danger delete-parent">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                        </div>
                    `
                } else {
                    // jika belum close po
                    if (status_penerimaan !== "CLOSED") {
                        return `
                            <div class="mt-0">
                            <button class="btn btn-warning btn-print" onclick="print('<?= base_url("po-lokal-bahan-baku/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            <button onclick="closePO(${id})" class="btn btn-danger delete-parent">
                                <i class="fa fa-xmark fa-sm" aria-hidden="true"></i>
                            </button>
                            </div>
                        `
                    } else {
                        return `
                            <div class="mt-0">
                            <button class="btn btn-warning btn-print" onclick="print('<?= base_url("po-lokal-bahan-baku/print/"); ?>${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                            </div>
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

        $(".search").keyup(function () {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd").change(function () {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("po-lokal-bahan-baku/id"); ?>/${data.id}`);
        })
    })

    const posting = function(id, purchase_request_id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan di Posting?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("po-lokal-bahan-baku/update-status"); ?>",
                    data: {
                        id: id,
                        spp: purchase_request_id
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Disimpan, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }

    const closePO = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Yakin akan Close PO?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Close',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("po-lokal-bahan-baku/close-po"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Disimpan, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }

    const remove = function(id) {
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
                    url: "<?= base_url("po-lokal-bahan-baku/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                    }
                });
            }
        })
    }

    const print = function(url) 
    {
        window.open(url, "_blank");
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