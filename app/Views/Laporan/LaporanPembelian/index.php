<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Pembelian</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="input-group input-group-password">
                                <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Mulai Tanggal Transaksi">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="input-group input-group-password">
                                <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Selesai Tanggal Transaksi">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select list_supplier" name="list_supplier" id="list_supplier">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($suppliers)) {
                                        foreach ($suppliers as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Supplier</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select dokumen" name="dokumen" id="dokumen">
                                    <option value="all" data-code="">ALL</option>n>
                                    <option value="pabean" data-code="">Dokumen Pabean</option>
                                    <option value="non" data-code="">Non Pabean</option>
                                </select>
                                <label for="floatingInput">Dokumen</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Transaction Date</th>
                                <th>Document</th>
                                <th>Evidance Num</th>
                                <th>Invoice</th>
                                <th>Invoice Date</th>
                                <th>Tax Invoice</th>
                                <th>PO Num</th>
                                <th>Supplier</th>
                                <th>Valas</th>
                                <th>Exchange Rate</th>
                                <th>Nominal Value</th>
                                <th>Nominal Value(IDR)</th>
                                <th>Paid Value(IDR)</th>
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
    let sort = "createdAt";
    let sortType = "asc";
    $(document).ready(function() {
        const csrfToken = '<?= csrf_token() ?>';
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
                url: "<?= base_url("laporan-accounting/pembelian/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.filter = $(".list_supplier").val();
                    data.dateStart = $(".dateStart").val();
                    data.dateEnd = $(".dateEnd").val();
                    data.sort = sort;
                    data.sortType = sortType;
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: false,
            columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            }, {
                data: "po_date",
                className: "text-center",
            }, {
                data: "dokumen_num",
                className: "text-center",
            }, {
                data: "evidance_num",
                className: "text-center",
            }, {
                data: "invoice_num",
                className: "text-center",
            }, {
                data: "invoice_date",
                className: "text-center",
            }, {
                data: "tax_invoice",
                className: "text-center",
            }, {
                data: "po_num",
                className: "text-center",
            }, {
                data: "supplier_name",
                className: "text-center",
            }, {
                data: "valas",
                className: "text-center",
            }, {
                data: "exchange",
                className: "text-center",
            }, {
                data: "nominal",
                className: "text-center",
            }, {
                data: "nominal_idr",
                className: "text-center",
            }, {
                data: "paid_idr",
                className: "text-center",
            }, ],
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
        //CSS SELECT2 FLOATING LABEL
        $('.list_supplier').select2({
            placeholder: "Filter Supplier",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.list_supplier, .dokumen')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.list_supplier, .dokumen')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.list_supplier, .dokumen')
            .parent('div')
            .find('label')
            .css('z-index', '1');

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

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd, .list_supplier").change(function() {
            table.ajax.reload();
        })
    });
</script>
<?= $this->endSection(); ?>