<?= $this->extend('layouts/template-new-window'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>DETAIL BY ACCOUNT HOLDERS</h1>
        <button onclick="exportExcel()" style="right: 10px;" class="btn btn-discard btn-dropdown-export float-right" type="button" aria-expanded="false">
            <i class="fa-solid fa-print"></i> Export
        </button>
    </div>
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $user['name']  ?>" type="text" class="form-control ">
                        <label for="floatingInput">Account Holder</label>
                    </div>
                </div>

            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Start Actualy Shipment Date</label>
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
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
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
                    <div class="form-floating mb-3">
                        <select class="form-select barang_master_sales_id" name="barang_master_sales_id" id="barang_master_sales_id">
                            <option value="" data-code=""></option>
                            <?php foreach ($dataBarangSales as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['barang_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Select Items</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select user_id" name="user_id" id="user_id">
                            <option value="" data-code=""></option>
                            <?php foreach ($dataAccHolder as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Select Acc Holder</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select company_id" name="company_id" id="company_id">
                            <option value="" data-code=""></option>
                            <?php foreach ($dataCompany as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['company'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Select Plant</label>
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
                                <!-- <th style="text-align:left;" onclick="changeSort('sales_order_export.user_id')">Acc Holder</th> -->
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.sales_order_export_no')">Order Form No</th>
                                <th style="text-align:left;" onclick="changeSort('sales_contract.customer_id')">Customer</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.container')">Container</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.actualy_shipment_date')">Actualy Shipment Date</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.deadline')">Deadline</th>
                                <th style="text-align:left;" onclick="changeSort('sales_contract.dicharge_port')">Destination</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.company_id')">Plant</th>
                                <th style="text-align:left;" onclick="changeSort('sales_contract_detail.barang_master_sales_id')">Product</th>
                                <th style="text-align:left;">Qty Order Form</th>
                                <th style="text-align:left;">Qty (Kg)</th>
                                <th style="text-align:left;" onclick="changeSort('sales_order_export.valas_id')">Valas</th>
                                <th style="text-align:left;">Amount</th>
                                <th style="text-align:left;" onclick="changeSort('sales_contract.tipe_harga')">Price Type</th>
                                <th style="text-align:left;">OF</th>
                                <th style="text-align:left;">SC</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="10" class="text-right">GRAND TOTAL</th>
                                <th class="text-left totalQtyConvertion"></th>
                                <th></th>
                                <th class="text-left amountValue"></th>
                                <th></th>
                                <th></th>
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
            url: "<?= base_url('report-ekspor/items-all') ?>",
            type: 'GET',
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $('.dateStart').val();
                data.dateEnd = $('.dateEnd').val();
                data.search = $('.search').val();
                data.customer_id = $('.customer_id').val();
                data.barang_master_sales_id = $('.barang_master_sales_id').val();
                data.user_id = "<?= $user['id'] ?>";
                data.company_id = $('.company_id').val();
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
            // {
            //     data: 'acc_holder'
            // },
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
                data: 'deadline',
            },
            {
                data: 'dicharge_port',
            },
            {
                data: 'company',
            },
            {
                data: 'barang_name',
            },
            {
                data: 'total_qty',
                searchable: false,
                sortable: false,
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
                data: 'amount_value',
                render: function(data) {
                    return greatFormatRupiah(data)
                }
            },
            {
                data: 'price_type',
            },
            {
                data: 'id',
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.id;
                    return `
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="printOF('${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        `
                }
            },
            {
                data: 'id',
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    let id = row.sales_contract_id;
                    return `
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="printSC('${id}')" style="box-shadow: none !important;">
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
                $('.amountValue').html(greatFormatRupiah(total.amountValue));
            }
        },
    });

    $('#customer_id').select2({
        placeholder: "Select Customer",
        theme: "bootstrap-5",
        allowClear: true
    }).on('change select2:clear', function() {
        table.ajax.reload();
    });

    $('#user_id').select2({
        placeholder: "Select Acc Holder",
        theme: "bootstrap-5",
        allowClear: true
    }).on('change select2:clear', function() {
        table.ajax.reload();
    });

    $('#barang_master_sales_id').select2({
        placeholder: "Select Items",
        theme: "bootstrap-5",
        allowClear: true
    }).on('change select2:clear', function() {
        table.ajax.reload();
    });

    $('#company_id').select2({
        placeholder: "Select Plant",
        theme: "bootstrap-5",
        allowClear: true
    }).on('change select2:clear', function() {
        table.ajax.reload();
    });

    $('#customer_id,#dateStart,#dateEnd,#barang_master_sales_id,#user_id,#company_id').change(function() {
        table.ajax.reload();
    });

    $('#search').keyup(function() {
        table.ajax.reload();
    })

    $("#customer_id,#barang_master_sales_id,#user_id,#company_id")
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
        var barangMasterSalesId = $('#barang_master_sales_id option:selected').val();
        var userId = "<?= $user['id'] ?>";
        var companyId = $('#company_id option:selected').val();
        var search = $('#search').val();

        window.open('<?= base_url('report-ekspor/items-export') ?>?dateStart=' +
            dateStart + '&dateEnd=' + dateEnd +
            '&customer_id=' + customerId +
            '&barang_master_sales_id=' + barangMasterSalesId +
            '&user_id=' + userId +
            '&company_id=' + companyId +
            '&search=' + search
        );
    }

    const printOF = function(id) {
        var url = "/order-form-internasional/print/" + id + '?display_price=true';
        window.open(url, "_blank");
    }

    const printSC = function(id) {
        var url = "/sales-kontrak/print/" + id;
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