<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Pembelian</h1>
        <?php if (can('Laporan', 'Accounting', 'p')) : ?>
            <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <li><button class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-accounting/pembelian/printPDF"); ?>')">PDF</button></li>
                <li><button class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-accounting/pembelian/printExcel"); ?>')">EXCEL</button></li>
            </ul>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group" style="height: 50px;">
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Mulai Tanggal">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group" style="height: 50px;">
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Selesai Tanggal">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                                <label for="floatingInput">List Supplier</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select class="form-select list_type_barang" name="list_type_barang" id="list_type_barang">
                                    <option selected value="BAHAN BAKU LOKAL">BAHAN BAKU LOKAL</option>
                                    <option value="BAHAN PENOLONG LOKAL">BAHAN PENOLONG LOKAL</option>
                                    <option value="BAHAN BAKU INTERNASIONAL">BAHAN BAKU INTERNASIONAL</option>
                                    <option value="BAHAN PENOLONG INTERNASIONAL">BAHAN PENOLONG INTERNASIONAL</option>
                                </select>
                                <label for="floatingInput">List Type Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select class="form-select divisi_id" name="divisi_id" id="divisi_id">
                                    <option value=""></option>
                                    <?php foreach ($divisis as $divisi) : ?>
                                        <option value="<?= $divisi['id']; ?>"><?= $divisi['divisi']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Filter Departemen</label>
                            </div>
                        </div>
                        <div class="col-md-4" style="height: 50px;">
                            <input style="height: auto;" autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
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
                        <tfoot class="thead">
                            <tr>
                                <th colspan="11" class="text-right">Grand Total:</th>
                                <th class="text-right" id="gt-nominal">-</th>
                                <th class="text-right" id="gt-nominal-idr">-</th>
                                <th class="text-right" id="gt-paid-idr">-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let sort = "createdAt";
    let sortType = "desc";

    // Set default tanggal: awal bulan - hari ini
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    function formatDate(date) {
        // Format dd/mm/yyyy
        let dd = String(date.getDate()).padStart(2, '0');
        let mm = String(date.getMonth() + 1).padStart(2, '0'); // Januari = 0
        let yyyy = date.getFullYear();
        return dd + '/' + mm + '/' + yyyy;
    }

    $(".dateStart").val(formatDate(firstDay));
    $(".dateEnd").val(formatDate(today));

    $(document).ready(function() {
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
                url: "<?= base_url("laporan-accounting/pembelian/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.filter = $(".list_supplier option:selected").val();
                    data.filter_divisi = $(".divisi_id option:selected").val();
                    data.filter_type_barang = $(".list_type_barang option:selected").val();
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
        // Tambahkan handler xhr
        table.on('xhr.dt', function(e, settings, json, xhr) {
            if (json.grandTotal) {
                $('#gt-nominal').text((json.grandTotal.nominal));
                $('#gt-nominal-idr').text((json.grandTotal.nominal_idr));
                $('#gt-paid-idr').text((json.grandTotal.paid_idr));
            }
        });
        //CSS SELECT2 FLOATING LABEL
        $('.list_supplier').select2({
            placeholder: "Filter Supplier",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.list_type_barang').select2({
            placeholder: "Filter Type Barang",
            theme: "bootstrap-5"
        });
        $('.divisi_id').select2({
            placeholder: "Filter Department",
            theme: "bootstrap-5"
        });
        $('.list_supplier, .dokumen, .list_type_barang, .divisi_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.list_supplier, .dokumen, .list_type_barang, .divisi_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.list_supplier, .dokumen, .list_type_barang, .divisi_id')
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

        $(".dateStart, .dateEnd, .list_supplier, .list_type_barang, .divisi_id").change(function() {
            if ($(".dateStart").val() != "" && $(".dateEnd").val() != "") {
                table.ajax.reload();
            }
        })

    });
    const convertDateFormat = function(dateString) {
        // Memisahkan tanggal, bulan, dan tahun dari string
        var dateParts = dateString.split("/");

        // Membalikkan urutan elemen array untuk membuat format "YYYY-MM-DD"
        var formattedDate = dateParts[2] + "-" + dateParts[1] + "-" + dateParts[0];

        return formattedDate;
    }
    const printPDF = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "";
        var search = $(".search").val() ? $(".search").val() : "all";
        var filter = $(".list_supplier").val() ? $(".list_supplier").val() : "all";
        var filter_divisi = $(".divisi_id option:selected").val() ? $(".divisi_id option:selected").val() : "all";
        var filter_type_barang = $(".list_type_barang option:selected").val() ? $(".list_type_barang option:selected").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + search + "/" + filter_divisi + "/" + filter_type_barang;
        // console.log(url2);
        if (tanggal_awal == "" || tanggal_akhir == "") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tanggal tidak boleh kosong!',
            });
        } else {
            window.open(url2, "_blank");
        }
    }
    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "";
        var search = $(".search").val() ? $(".search").val() : "all";
        var filter = $(".list_supplier").val() ? $(".list_supplier").val() : "all";
        var filter_divisi = $(".divisi_id option:selected").val() ? $(".divisi_id option:selected").val() : "all";
        var filter_type_barang = $(".list_type_barang option:selected").val() ? $(".list_type_barang option:selected").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + search + "/" + filter_divisi + "/" + filter_type_barang;
        // console.log(url2);
        if (tanggal_awal == "" || tanggal_akhir == "") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Tanggal tidak boleh kosong!',
            });
        } else {
            window.open(url2, "_blank");
        }
    }
</script>
<?= $this->endSection(); ?>