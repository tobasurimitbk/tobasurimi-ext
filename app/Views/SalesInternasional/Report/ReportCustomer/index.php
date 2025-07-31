<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Report Ekspor By Customer</h1>

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
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" value="01/<?= date("m/Y") ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Strat Actualy Shipment Date</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" value="<?= date('t/m/Y') ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">End Actualy Shipment Date</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select customer_id" name="customer_id" id="customer_id">
                            <option value="" data-code=""></option>
                            <?php foreach ($dataCustomer as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Select Customer</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th style="text-align:left; width:10px;" onclick="changeSort('sales_order_export.sales_order_export_id')">No</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.user_id')">Acc Holder</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.sales_order_export_no')">Order Form No</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.customer_id')">Customer</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.container')">Container</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.actualy_shipment_date')">Actualy Shipment Date</th>
                                <th style="text-align:left;">Qty (Kg)</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.valas_id')">Valas</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.shipment_value')">Shipment Value</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.shipment_value_net')">Shipment Value Net</th>
                                <th style="text-align:left;">Order Form</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">GRAND TOTAL</th>
                                <th class="text-left totalQtyConvertion"></th>
                                <th></th>
                                <th class="text-left totalShipmentValue"></th>
                                <th class="text-left totalShipmentValueNet"></th>
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
    let sort = "sales_order_export.sales_order_export_id";
    let sortType = "desc";

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('report-ekspor/customer-all') ?>",
            type: 'GET',
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $('.dateStart').val();
                data.dateEnd = $('.dateEnd').val();
                data.search = $('.search').val();
                data.customer_id = $('.customer_id').val();
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
                data: 'sales_order_export_no'
            },
            {
                data: 'customer_name'
            },
            {
                data: 'container'
            },
            {
                data: 'actualy_shipment_date'
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
                data: 'valas'
            },
            {
                data: 'shipment_value',
                render: function(data) {
                    return greatFormatRupiah(data)
                }
            },
            {
                data: 'shipment_value_net',
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
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
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
                $('.totalShipmentValue').html(greatFormatRupiah(total.totalShipmentValue));
                $('.totalShipmentValueNet').html(greatFormatRupiah(total.totalShipmentValueNet));
            }
        },
    });

    $('#customer_id').select2({
        placeholder: "Select Customer",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#customer_id,#dateStart,#dateEnd').change(function() {
        table.ajax.reload();
    });

    $('#search').keyup(function() {
        table.ajax.reload();
    })

    $("#customer_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $(".dateStart,.dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    function exportExcel() {
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var customerId = $('#customer_id option:selected').val();

        if (dateStart == '' || dateEnd == '') {
            alert('Tanggal mulai & Tanggal Akhir wajib diisi');
        } else {
            window.open('<?= base_url('report-ekspor/customer-export') ?>?dateStart=' + dateStart + '?dateEnd=' + dateEnd + '&customer_id=' + customerId);
        }

    }

    const print = function(id) {
        var url = "/order-form-internasional/print/" + id + '?display_price=true';
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