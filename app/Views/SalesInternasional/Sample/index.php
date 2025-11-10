<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Sample</h1>
        <?php if (can('Penjualan Ekspor', 'Sample', 'c')) : ?>
            <a class="btn btn-show-form btn-add float-right" href="<?= base_url("sample-ekspor/create"); ?>">
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
                                <!-- <th onclick="changeSort('divisi_id')" class="sort">Department</th> -->
                                <th onclick="changeSort('tanggal')" class="sort">Sample Date</th>
                                <th onclick="changeSort('delivery')" class="sort">Delivery</th>
                                <th onclick="changeSort('attn_no')" class="sort">Attn</th>
                                <th onclick="changeSort('total_berat_kotor')" class="sort">Gross Weight (Kg)</th>
                                <th onclick="changeSort('total_berat_bersih')" class="sort">Net Weight (Kg)</th>
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
            url: "<?= base_url("sample-ekspor/all"); ?>",
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
                data: "tanggal",
                className: "text-left",
            }, {
                data: "delivery",
                className: "text-left",
            }, {
                data: "attn_no",
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

                    let res = '';

                    res += `
                        <div class="mt-0">
                          <?php if (can('Penjualan Ekspor', 'Sample', 'u')) : ?>
                            <a href="javascript:void(0)" onclick="edit('${id}')" data-toggle="tooltip" title="Edit" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Sample', 'p')) : ?>
                                <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                    <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Sample', 'd')) : ?>
                                <button data-toggle="tooltip" title="Delete" onclick="remove('${id}')" class="btn btn-danger delete-parent">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
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

    function edit(id) {
        var base_url = "<?= base_url() ?>";
        window.location.href = base_url + `sample-ekspor/id/${id}`;
    }

    function print(id) {
        var base_url = "<?= base_url() ?>";
        window.open(base_url + `sample-ekspor/print/${id}`, '_blank');
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Delete Sample ?',
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
                    url: "<?= base_url("sample-ekspor/delete"); ?>",
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