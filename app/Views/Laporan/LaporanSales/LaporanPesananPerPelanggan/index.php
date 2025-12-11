<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .txt-bold {
        font-weight: 700 !important;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Pesanan Per Pelanggan</h1>

        <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li>
                <button class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-sales/pesanan-sales-per-pelanggan/printPDF"); ?>')">PDF</button>
            </li>
            <li>
                <button class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-sales/pesanan-sales-per-pelanggan/printExcel"); ?>')">EXCEL</button>
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
                        <div class="col-md-3 mb-3">
                            <div class="input-group" style="height: 50px;">
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateStartShip" id="dateStartShip" name="dateStartShip" placeholder="Mulai Tanggal Pengiriman">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStartShip"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="input-group" style="height: 50px;">
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateEndShip" id="dateEndShip" name="dateEndShip" placeholder="Selesai Tanggal Pengiriman">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEndShip"></i>
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
                        <div class="col-md-3">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select filter_status" name="filter_status" id="filter_status">
                                    <option value="" disabled selected></option>
                                    <option value="belum">Belum Proses</option>
                                    <option value="selesai">Selesai</option>

                                </select>
                                <label for="floatingInput">Status</label>
                            </div>
                        </div>
                        <div class="col-md-3" style="height: 50px;">
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
                                <th>No. Pesanan</th>
                                <th>Tanggal Pesan</th>
                                <th>Ship Date</th>
                                <th>Jumlah</th>
                                <th>Status</th>
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
                url: "<?= base_url("laporan-sales/pesanan-sales-per-pelanggan/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.filter = $(".filter_customer").val();
                    data.filter_status = $(".filter_status").val();
                    data.dateStart = $(".dateStart").val();
                    data.dateEnd = $(".dateEnd").val();
                    data.dateStartShip = $(".dateStartShip").val();
                    data.dateEndShip = $(".dateEndShip").val();
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
            createdRow: function(row, data, dataIndex) {
                if (data.is_customer) {
                    $(row).addClass('customer-row')
                        .find('td')
                        .attr('colspan', 8)
                        .removeClass('text-center')
                        .addClass('text-left txt-bold')
                        .css('cssText', 'font-weight:700 !important;');

                    $(row).find('td:not(:first)').remove(); // Remove other cells
                } else if (data.is_total) {
                    $(row).addClass('total-row');

                    // Buat ulang isi row total
                    $(row).html(`
                        <td colspan="3" class="text-left txt-bold" style="font-weight:700!important">Total Pesanan : </td>
                        <td class="text-center txt-bold" style="font-weight:700!important">${data.no_sales_order}</td>
                        <td></td>
                    `);
                }
            },
            columns: [{
                data: "no_sales_order",
                className: "text-center",
            }, {
                data: "order_date",
                className: "text-center",
            }, {
                data: "shipping_date",
                className: "text-center",
            }, {
                data: "sum_amount",
                className: "text-center",
            }, {
                data: "status",
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
        $('.filter_customer').select2({
            placeholder: "Filter Pelanggan",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.filter_status').select2({
            placeholder: "Filter Status",
            theme: "bootstrap-5",
            allowClear: true
        });
        $('.filter_customer, .filter_status')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.filter_customer, .filter_status')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.filter_customer, .filter_status')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".dateStart, .dateStartShip").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".dateEnd, .dateEndShip").datepicker({
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

        $('.icon-dateStartShip').click(function() {
            $(".dateStartShip").focus();
        });

        $('.icon-dateEndShip').click(function() {
            $(".dateEndShip").focus();
        });

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dateStart, .dateEnd, .dateStartShip, .dateEndShip, .filter_customer, .filter_status").change(function() {
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
        var filter_status = $(".filter_status").val() ? $(".filter_status").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + filter_status + "/" + search;
        // console.log(url2);
        window.open(url2, "_blank");
    }
    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var search = $(".search").val() ? $(".search").val() : "all";
        var filter = $(".filter_customer").val() ? $(".filter_customer").val() : "all";
        var filter_status = $(".filter_status").val() ? $(".filter_status").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + filter_status + "/" + search;
        window.open(url2, "_blank");
    }
</script>
<?= $this->endSection(); ?>