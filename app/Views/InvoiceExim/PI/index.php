<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>List Order Form Ekspor</h1>
    </div>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp mb-3">
                <div class="col mb-3">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Start Date" value="01/<?= date('m/Y') ?>">
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
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search Data" id="search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('sales_contract.id')" style="width: 10px;">No</th>
                                <th onclick="changeSort('createdAt')" class="sort">Tgl Dibuat</th>
                                <th onclick="changeSort('customer_name')" class="sort">Customer</th>
                                <th onclick="changeSort('sales_contract_no')" class="sort">No SC</th>
                                <th onclick="changeSort('sales_contract.dicharge_port')" class="sort">Destination</th>
                                <th>Total Inv PI</th>
                                <th style="width: 120px;">Action</th>
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
    var csrf = $(`[name="${csrfToken}"]`);
    let sort = "sales_contract.id";
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
            url: "<?= base_url("proforma-invoice/all-contract"); ?>",
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
            }, {
                data: "createdAt",
                className: "text-left",
            }, {
                data: "customer_name",
                className: "text-left",
            }, {
                data: "sales_contract_no",
                className: "text-left",
            },
            {
                data: "dicharge_port",
                className: "text-left",
            },
            {
                data: "total_inv_pi",
                className: "text-left",
                searchable: false,
                sortable: false,
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;

                    return `
                        <div class="mt-0">
                            <a href="<?= base_url("proforma-invoice/detail"); ?>/${id}"  data-toggle="tooltip" title="List PI" class="btn btn-danger">
                               <i class="fa-solid fa-file-lines"></i>
                            </a>
                        </div>
                    `
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

    $(".status_invoice, .dateStart, .dateEnd").change(function() {
        table.ajax.reload();
    })

    $(".search").keyup(function() {
        table.ajax.reload();
    })

    function edit(id) {
        window.location.href = "<?= base_url('proforma-invoice/detail') ?>" + '/' + id
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