<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Report By Account Holder</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right text-white" onclick="exportExcel()">
                <i class="fa-solid fa-print"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right " href="<?= base_url("report-ekspor"); ?>">
                Back
            </a>
        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control year" id="year" name="year" aria-label="Floating label select example" value="" />
                            <label style="z-index: 1;" style="z-index: 1;">Select Actualy Shipment Year</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Search</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th style="text-align:left; width:10px;" onclick="changeSort('sales_order_export.user_id')">No</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.user_id')">Acc Holder</th>
                                <th style="text-align:left; width:100px;" onclick="changeSort('sales_order_detail_export.qty_convertion')">Total Qty (Kg)</th>
                                <th style="text-align:left; width:100px;" onclick="changeSort('sales_order_detail_export.total_harga_barang')">Total Amount</th>
                                <th style="text-align:left; width:100px;">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">GRAND TOTAL</th>
                                <th class="text-left totalQtyConvertion"></th>
                                <th class="text-left amountValue"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let sort = "sales_order_export.user_id";
    let sortType = "desc";

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('report-ekspor/account-holder-all') ?>",
            type: 'GET',
            dataSrc: "data",
            data: function(data) {
                data.year = $('.year').val();
                data.search = $('.search').val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        pageLength: 25,
        display: "stripe",
        searching: false,
        columns: [{
                data: 'no',
                width: "2%"
            },
            {
                data: 'acc_holder'
            },
            {
                data: 'total_qty_convertion',
                searchable: false,
                sortable: false,
                render: function(data) {
                    return greatFormatRupiah(data)
                }
            },
            {
                data: 'total_harga_barang',
                render: function(data) {
                    return greatFormatRupiah(data)
                }
            },
            {
                data: 'id',
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    return `
                        <div class="mt-0 actions">
                            <a href="javascript:void(0)" onclick="detail('${id}')" data-toggle="tooltip" title="Detail" class="btn btn-success posting-spp actions">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        `
                }
            },
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
        },
        footerCallback: function(row, data, start, end, display) {
            const api = this.api();

            const total = api.ajax.json().footerTotals;

            if (total) {
                $('.totalQtyConvertion').html(greatFormatRupiah(total.totalQtyConvertion));
                $('.amountValue').html(greatFormatRupiah(total.amountValue));
            }
        },

    });


    $('#year').change(function() {
        table.ajax.reload();
    });

    $('#search').keyup(function() {
        table.ajax.reload();
    })


    $(".year").datepicker({
        format: "yyyy",
        minViewMode: 2, // hanya tampilkan tahun
        autoclose: true,
        todayHighlight: true,
        orientation: "bottom auto"
    });

    const detail = function(id) {
        const width = 800;
        const height = 600;
        const left = window.innerWidth / 2 - width / 2;
        const top = window.innerHeight / 2 - height / 2;

        window.open(
            "<?= base_url('report-ekspor/account-holder/id/') ?>" + id,
            "_blank",
            `width=${width},height=${height},top=${top},left=${left},resizable=yes`
        );

    }

    function exportExcel() {
        var year = $('#year').val();
        var search = $('#search').val();

        window.open('<?= base_url('report-ekspor/account-holder-export') ?>?year=' +
            year + '&search=' + search
        );
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