<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .txt-bold {
        font-weight: 700 !important;
    }
    /* Agar header dan isi tabel tidak wrap */
    #pivotTable th,
    #pivotTable td {
        white-space: nowrap;
        text-align: right; /* atau left untuk nama customer */
    }

    /* Nama customer tetap rata kiri */
    #pivotTable th:first-child,
    #pivotTable td:first-child {
        text-align: left;
    }

    /* Tambahkan scroll horizontal jika tabel terlalu panjang */
    .table-responsive {
        overflow-x: auto;
    }
</style>

<section class="section">
    <div class="section-header">
        <h1>Laporan Items Sales By Customers</h1>

        <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" data-bs-toggle="dropdown">
            Export
        </button>
        <ul class="dropdown-menu">
            <li><button class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-sales/items-sales-by-customers/printPDF"); ?>')">PDF</button></li>
            <li><button class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-sales/items-sales-by-customers/printExcel"); ?>')">EXCEL</button></li>
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
                                <select class="form-select filter_barang" name="filter_barang" id="filter_barang">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($barang)) {
                                        foreach ($barang as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->kode_barang; ?> <?= $sub->barang_name; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Barang</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="pivotTable" class="table table-bordered table-hover-tobasurimi">
                    <thead><tr></tr></thead>
                    <tfoot><tr></tr></tfoot>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    let table;

    // =========================
    // DEFAULT DATE
    // =========================
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    function formatDate(date) {
        return String(date.getDate()).padStart(2, '0') + '/' +
            String(date.getMonth() + 1).padStart(2, '0') + '/' +
            date.getFullYear();
    }

    $('.dateStart').val(formatDate(firstDay));
    $('.dateEnd').val(formatDate(today));

    // =========================
    // DEBOUNCE
    // =========================
    function debounce(fn, delay = 500) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    const reloadPivot = debounce(loadPivot);

    // =========================
    // LOAD PIVOT
    // =========================
    function loadPivot() {

        if (!$('.dateStart').val() || !$('.dateEnd').val()) return;

        setLoading();

        // Destroy lama
        if ($.fn.DataTable.isDataTable('#pivotTable')) {
            table.destroy();
            $('#pivotTable thead tr').empty();
            $('#pivotTable tfoot tr').empty();
            $('#pivotTable tbody').empty();
        }

        // =========================
        // AMBIL HEADER DULU
        // =========================
        $.ajax({
            url: "<?= base_url('laporan-sales/items-sales-by-customers/all') ?>",
            dataType: "json",
            data: {
                dateStart: $('.dateStart').val(),
                dateEnd: $('.dateEnd').val(),
                filter: $('.filter_barang').val(),
                headerOnly: true
            },
            success: function (res) {

                // =========================
                // BUILD HEADER & FOOTER
                // =========================
                let columns = [
                    { data: 'nama_barang', title: 'Barang', className: 'text-left' }
                ];

                let footerHtml = '<th>Total</th>';

                res.header.forEach(h => {

                    columns.push({
                        data: h.customer_id,
                        title: h.customer_name,
                        className: 'text-right',
                        render: d => greatFormatRupiah(d ?? 0)
                    });

                    footerHtml += `
                        <th class="text-right">
                            ${greatFormatRupiah(res.footer.per_customer[h.customer_id] ?? 0)}
                        </th>
                    `;
                });

                columns.push({
                    data: 'total',
                    title: 'Total',
                    className: 'text-right',
                    render: d => greatFormatRupiah(d ?? 0)
                });

                footerHtml += `
                    <th class="text-right">
                        ${greatFormatRupiah(res.footer.grand_total)}
                    </th>
                `;

                $('#pivotTable tfoot tr').html(footerHtml);

                // =========================
                // INIT DATATABLE
                // =========================
                table = $('#pivotTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    searching: false,
                    pageLength: 25,
                    ajax: {
                        url: "<?= base_url('laporan-sales/items-sales-by-customers/all') ?>",
                        data: d => {
                            d.dateStart = $('.dateStart').val();
                            d.dateEnd   = $('.dateEnd').val();
                            d.filter    = $('.filter_barang').val();
                        }
                    },
                    columns: columns,
                    initComplete: function() {
                        stopLoading(); // <--- SELESAI LOADING
                    }
                });
            },
            error: function() {
                stopLoading(); // <--- JIKA AJAX HEADER GAGAL, STOP LOADING
            }
        });
    }

    // =========================
    // READY
    // =========================
    $(document).ready(function () {

        loadPivot();

        $('.filter_barang').select2({
            placeholder: "Filter Barang",
            theme: "bootstrap-5",
            allowClear: true
        });

        $(".dateStart, .dateEnd").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            autoclose: true
        });

        $('.dateStart, .dateEnd, .filter_barang').on('change', reloadPivot);
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
        var filter = $(".filter_barang").val() ? $(".filter_barang").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter;
        // console.log(url2);
        window.open(url2, "_blank");
    }
    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var filter = $(".filter_barang").val() ? $(".filter_barang").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter;
        window.open(url2, "_blank");
    }
</script>
<?= $this->endSection(); ?>
