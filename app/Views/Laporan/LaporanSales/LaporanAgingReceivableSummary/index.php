<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .txt-bold {
        font-weight: 700 !important;
    }
    .aging-link {
        color: #0d6efd;
        cursor: pointer;
        text-decoration: underline;
    }
    .aging-link:hover {
        color: #0a58ca;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Aging Receivable Summary</h1>

        <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li>
                <button class="dropdown-item" onclick="printPDF('<?= base_url("/laporan-sales/aging-receivable-summary/printPDF"); ?>')">PDF</button>
            </li>
            <li>
                <button class="dropdown-item" onclick="printExcel('<?= base_url("/laporan-sales/aging-receivable-summary/printExcel"); ?>')">EXCEL</button>
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
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Customer Name</th>
                                <th>Total Invoice</th>
                                <th>Not Yet</th>
                                <th>1-30</th>
                                <th>31-60</th>
                                <th>61-90</th>
                                <th>91-120</th>
                                <th>>120</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL DRILLDOWN -->
    <div class="modal" id="invoiceModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Invoice Aging</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="invoiceTable">
                        <thead>
                            <tr>
                                <th>No Faktur</th>
                                <th>Tgl Faktur</th>
                                <th>Jatuh Tempo</th>
                                <th>Aging (Hari)</th>
                                <th>Sisa Invoice</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    let sort = "tanggal_jatuh_tempo";
    let sortType = "asc";

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
        const table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            searching: false,
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
                url: "<?= base_url('laporan-sales/aging-receivable-summary/all'); ?>",
                data: function(d) {
                    d.search = $('.search').val();
                    d.filter = $('.filter_customer').val();
                    d.dateStart = $('.dateStart').val();
                    d.dateEnd = $('.dateEnd').val();
                }
            },
            columns: [
                { data: 'customer_name' },
                { data: 'total_invoice', className: 'text-end txt-bold' },
                agingColumn('not_yet', 'not_yet'),
                agingColumn('1_30', '1_30'),
                agingColumn('31_60', '31_60'),
                agingColumn('61_90', '61_90'),
                agingColumn('91_120', '91_120'),
                agingColumn('over_120', 'over_120 text-danger')
            ]
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

    function agingColumn(key, cls) {
        return {
            data: key,
            className: 'text-end ' + cls,
            render: function(data, type, row) {
                if (data === '0' || data === '-' || !data) return '-';
                return `
                    <span class="aging-link"
                        onclick="showInvoice('${row.customer_id}','${key}')">
                        ${data}
                    </span>
                `;
            }
        };
    }
    function showInvoice(customerId, aging) {
        $.ajax({
            url: "<?= base_url('laporan-sales/aging-receivable-summary/detail'); ?>",
            type: "GET",
            dataType: "json",
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading()
            },
            data: {
                customer_id: customerId,
                aging: aging
            },
            success: function (res) {

                if (!res || !res.data) {
                    alert('Data invoice tidak ditemukan');
                    return;
                }

                let html = '';
                res.data.forEach(row => {
                    html += `
                        <tr>
                            <td>${row.no_faktur}</td>
                            <td>${row.tanggal_faktur}</td>
                            <td>${row.tanggal_jatuh_tempo}</td>
                            <td class="text-center">${row.aging_hari}</td>
                            <td class="text-end">${greatFormatRupiah(row.sisa_invoice)}</td>
                        </tr>
                    `;
                });

                $('#invoiceTable tbody').html(html);
                $('#invoiceModal').modal('show');
                $('.modal-backdrop').remove();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Gagal mengambil detail invoice');
            }
        });
    }
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
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + search;
        // console.log(url2);
        window.open(url2, "_blank");
    }
    const printExcel = function(url) {
        var tanggal_awal = $(".dateStart").val() ? convertDateFormat($(".dateStart").val()) : "all";
        var tanggal_akhir = $(".dateEnd").val() ? convertDateFormat($(".dateEnd").val()) : "now";
        var search = $(".search").val() ? $(".search").val() : "all";
        var filter = $(".filter_customer").val() ? $(".filter_customer").val() : "all";
        url2 = url + "/" + tanggal_awal + "/" + tanggal_akhir + "/" + filter + "/" + search;
        window.open(url2, "_blank");
    }
</script>
<?= $this->endSection(); ?>