<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Biaya Impor</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right" href="#" onclick="exportExcel()">
                <i class="fa fa-download"></i> Export
            </a>

            <?php if (can('Biaya Exim', 'Biaya Impor', 'c')) : ?>
                <a class="btn btn-show-form btn-success float-right" href="<?= base_url("biaya-impor/create"); ?>">
                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                </a>
            <?php endif; ?>
        </div>
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
                    <select class="form-select status_posting" name="status_posting" id="status_posting" aria-label="Floating label select example">
                        <option value="ALL" selected>STATUS : ALL</option>
                        <option value="SUDAH POSTING">STATUS : POSTED AUDIT</option>
                        <option value="BELUM POSTING">STATUS : NOT POSTED AUDIT</option>
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
                                <th onclick="changeSort('biaya_ekspor.id')">No</th>
                                <th onclick="changeSort('biaya_ekspor.divisi_id')" class="sort">Departemen</th>
                                <th onclick="changeSort('biaya_ekspor.tanggal_invoice')" class="sort">Tanggal</th>
                                <th onclick="changeSort('biaya_ekspor.no_invoice')" class="sort">Invoice</th>
                                <th onclick="changeSort('biaya_ekspor.no_container')" class="sort">Supplier</th>
                                <th onclick="changeSort('customers.name')" class="sort">Port Of Origin</th>
                                <th onclick="changeSort('sales_contract.dicharge_port')" class="sort">Port Of Destination</th>
                                <th onclick="changeSort('vendor_pelayaran.nama_vendor')" class="sort">Vendor</th>
                                <th onclick="changeSort('biaya_ekspor.total_faktur')" class="sort">Total</th>
                                <th onclick="changeSort('biaya_ekspor.status_posting')" class="sort">Audit</th>
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



<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "biaya_ekspor.tanggal_invoice";
    let sortType = "desc";

    // const table = $('.dataTable').DataTable({

    //     processing: true,
    //     serverSide: true,
    //     ordering: true,
    //     order: [
    //         [1, 'asc']
    //     ],
    //     fixedHeader: true,
    //     lengthMenu: [
    //         [25],
    //         [25],
    //     ],
    //     pageLength: 25,
    //     ajax: {
    //         url: "<?= base_url("biaya-eskpor/all"); ?>",
    //         dataSrc: "data",
    //         data: function(data) {
    //             data.search = $(".search").val();
    //             data.status_posting = $(".status_posting").val();
    //             data.dateStart = $(".dateStart").val();
    //             data.dateEnd = $(".dateEnd").val();
    //             data.sort = sort;
    //             data.sortType = sortType;
    //         }
    //     },
    //     initComplete: function(settings, json) {
    //         $('.dataTables_length').empty();
    //         $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
    //         $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
    //     },
    //     display: "stripe",
    //     searching: false,
    //     columns: [{
    //             data: "no",
    //             className: "text-left",
    //         }, {
    //             data: "divisi",
    //             className: "text-left"
    //         }, {
    //             data: "tanggal_invoice",
    //             className: "text-left"
    //         }, {
    //             data: "no_invoice",
    //             className: "text-left",
    //         }, {
    //             data: "no_container",
    //             className: "text-left",
    //         }, {
    //             data: "customer_name",
    //             className: "text-left",
    //         }, {
    //             data: "dicharge_port",
    //             className: "text-left",
    //         }, {
    //             data: "nama_vendor",
    //             className: "text-left",
    //         },
    //         {
    //             data: "total_faktur",
    //             className: "text-left",
    //             render: function(data) {
    //                 return greatFormatRupiah(data);
    //             }
    //         },
    //         {
    //             data: "status_posting",
    //             className: "text-center",
    //             searchable: false,
    //             sortable: false,
    //             width: "5%",
    //             render: function(data, type, row) {
    //                 let htmlRes = '';

    //                 if (row.status_posting == 1) {
    //                     htmlRes += `
    //                         <div class="text-success">
    //                            <i class="fa-solid fa-check"></i>
    //                         </div>`
    //                 } else {
    //                     htmlRes += `
    //                         <div class="text-danger">
    //                            <i class="fa-solid fa-x"></i>
    //                         </div>`
    //                 }

    //                 return htmlRes;
    //             }
    //         },
    //         {
    //             data: "id",
    //             className: "text-center actions",
    //             searchable: false,
    //             sortable: false,
    //             render: function(data, type, row) {
    //                 let id = row.id;
    //                 let status_posting = row.status_posting;

    //                 let res = '';

    //                 if (status_posting === "0") {
    //                     res += `
    //                     <div class="mt-0">
    //                       <?php if (can('Biaya Exim', 'Biaya Ekspor', 'u')) : ?>
    //                         <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
    //                             <i class="fas fa-edit"></i>
    //                         </a>
    //                     <?php endif; ?>
    //                         <?php if (can('Biaya Exim', 'Biaya Ekspor', 'p')) : ?>
    //                             <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("biaya-eskpor/print/"); ?>${id}')" style="box-shadow: none !important;">
    //                                 <i class="fa fa-print fa-sm" aria-hidden="true"></i>
    //                             </button>
    //                         <?php endif; ?>
    //                         <?php if (can('Biaya Exim', 'Biaya Ekspor', 'a')) : ?>
    //                             <button data-toggle="tooltip" title="Posting Audit" onclick="posting('${id}')" class="btn btn-success posting-spp">
    //                                 <i class="fa fa-paper-plane fa-sm" aria-hidden="true"></i>
    //                             </button>
    //                         <?php endif; ?>
    //                         <?php if (can('Biaya Exim', 'Biaya Ekspor', 'd')) : ?>
    //                             <button data-toggle="tooltip" title="Delete" onclick="remove('${id}')" class="btn btn-danger delete-parent">
    //                                 <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
    //                             </button>
    //                         <?php endif; ?>
    //                         </div>
    //                     `;
    //                 }

    //                 if (status_posting === "1") {
    //                     res += `
    //                      <?php if (can('Biaya Exim', 'Biaya Ekspor', 'u')) : ?>
    //                         <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
    //                             <i class="fas fa-edit"></i>
    //                         </a>
    //                     <?php endif; ?>
    //                     <?php if (can('Biaya Exim', 'Biaya Ekspor', 'p')) : ?>
    //                         <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('<?= base_url("biaya-eskpor/print/"); ?>${id}')" style="box-shadow: none !important;">
    //                             <i class="fa fa-print fa-sm" aria-hidden="true"></i>
    //                         </button>

    //                     <?php endif; ?>
    //                       <?php if (can('Biaya Exim', 'Biaya Ekspor', 'ua')) : ?>
    //                             <button data-toggle="tooltip" title="Un-Posting" onclick="unposting('${id}')" class="btn btn-danger posting-spp">
    //                                 <i class="fa-solid fa-ban"></i>    
    //                             </button>
    //                         <?php endif; ?>
    //                     `;
    //                 }

    //                 return `
    //                 <div class="mt-0">
    //                     ${res}
    //                 </div>
    //                 `;
    //             }
    //         }
    //     ],
    //     "drawCallback": function(settings) {
    //         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
    //         var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    //             return new bootstrap.Tooltip(tooltipTriggerEl)
    //         });
    //     },
    //     columnDefs: [{
    //         defaultContent: "-",
    //         targets: "_all"
    //     }],
    //     language: {
    //         emptyTable: "Tidak ada data",
    //         lengthMenu: "Show _MENU_ entries",
    //         paginate: {
    //             previous: '<i class="fa fa-angle-left"></i>',
    //             next: '<i class="fa fa-angle-right"></i>'
    //         }
    //     }
    // });

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


    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    function remove(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data ?',
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
                    url: "<?= base_url("biaya-eskpor/delete"); ?>",
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

    function edit(id) {
        window.location.href = `/biaya-eskpor/id/${id}`;
    }

    function exportExcel() {
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var statusPosting = $('#status_posting').val();
        var search = $('#search').val();

        window.location.href = "<?= base_url('biaya-eskpor/export-excel') ?>" + '?dateStart=' + dateStart + '&dateEnd=' + dateEnd + '&status_posting=' + statusPosting + '&search=' + search;
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    function posting(id) {
        Swal.fire({
            icon: 'question',
            title: "Posting Data ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Posting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("biaya-eskpor/posting"); ?>",
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
                                    table.ajax.reload();
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
            confirmButtonText: 'Unposting',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("biaya-eskpor/unposting"); ?>",
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