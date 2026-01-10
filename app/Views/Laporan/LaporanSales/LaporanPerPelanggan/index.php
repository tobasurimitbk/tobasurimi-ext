<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Penjualan Per Pelanggan</h1>

        <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li>
                <button class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-sales/sales-per-pelanggan/printPDF"); ?>')">PDF Per Pelanggan</button>
            </li>
            <li>
                <button class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-sales/sales-per-pelanggan/printExcel"); ?>')">EXCEL Per Pelanggan</button>
            </li>
            <li>
                <button class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-sales/sales-per-pelanggan-per-penjual/printPDF"); ?>')">PDF Per Penjual</button>
            </li>
            <li>
                <button class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-sales/sales-per-pelanggan-per-penjual/printExcel"); ?>')">EXCEL Per Penjual</button>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="input-group" style="height: 50px;">
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Mulai Tanggal Transaksi">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="input-group" style="height: 50px;">
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Selesai Tanggal Transaksi">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select filter_customer" name="filter_customer" id="filter_customer">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($customer)) {
                                        foreach ($customer as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->kode; ?> <?= $sub->name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Pelanggan</label>
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 50px;">
                            <input style="height: auto;" autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 mb-3">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div> -->
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">No. Pelanggan</th>
                                <th class="text-center">Nama Pelanggan</th>
                                <th class="text-center">Nama Penjual</th>
                                <th class="text-center">Jumlah Data</th>
                                <th class="text-center">Jumlah Dengan Pajak</th>
                                <th class="text-center">Jumlah Tanpa Pajak</th>
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
            fixedHeader: true,
            lengthMenu: [
                [25],
                [25],
            ],
            pageLength: 25,
            ajax: {
                url: "<?= base_url("laporan-sales/sales-per-pelanggan/all"); ?>",
                dataSrc: "data",
                data: function(d) {
                    d.search = $(".search").val();
                    d.filter = $(".filter_customer").val();
                    d.dateStart = $(".dateStart").val();
                    d.dateEnd = $(".dateEnd").val();

                    // mapping order dari datatables
                    if (d.order && d.order.length > 0) {
                        let orderColIdx = d.order[0].column;
                        let orderDir = d.order[0].dir;

                        // mapping ke nama field backend
                        let colName = d.columns[orderColIdx].data;
                        d.sort = colName;
                        d.sortType = orderDir;
                    }
                }
            },
            display: "stripe",
            searching: false,
            columns: [{
                    data: "no",
                    className: "text-center",
                    sortable: false,
                    width: "5%"
                },
                {
                    data: "kode_pelanggan",
                    className: "text-center"
                },
                {
                    data: "nama_pelanggan",
                    className: "text-left"
                },
                {
                    data: "nama_penjual",
                    className: "text-left"
                },
                {
                    data: "count_invoice",
                    className: "text-center"
                },
                {
                    data: "total_invoice",
                    className: "text-center"
                },
                {
                    data: "total_invoice_before_ppn",
                    className: "text-center"
                },
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
        //CSS SELECT2 FLOATING LABEL
        $('.filter_customer').select2({
            placeholder: "Filter Pelanggan",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.filter_customer')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.filter_customer')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.filter_customer')
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

        $(".dateStart, .dateEnd, .filter_customer").change(function() {
            table.ajax.reload();
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
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var search = $(".search").val() ? $(".search").val() : "all";
        var filter = $(".filter_customer").val() ? $(".filter_customer").val() : "all";

        // Encode parameters for URL
        tanggal_awal = encodeURIComponent(tanggal_awal);
        tanggal_akhir = encodeURIComponent(tanggal_akhir);
        search = encodeURIComponent(search);
        filter = encodeURIComponent(filter);

        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + search;
        window.open(url2, "_blank");
    }

    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var search = $(".search").val() ? $(".search").val() : "all";
        var filter = $(".filter_customer").val() ? $(".filter_customer").val() : "all";

        // Encode parameters for URL
        tanggal_awal = encodeURIComponent(tanggal_awal);
        tanggal_akhir = encodeURIComponent(tanggal_akhir);
        search = encodeURIComponent(search);
        filter = encodeURIComponent(filter);

        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + search;
        window.open(url2, "_blank");
    }
</script>
<?= $this->endSection(); ?>