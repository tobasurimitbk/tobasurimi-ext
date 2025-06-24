<?= $this->extend('layouts/template-new-window'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Detail Hutang</h1>
        <?php if (can('Laporan', 'Accounting', 'p')) : ?>
            <button style="right: 10px;" class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                <!-- <li><button class="dropdown-item" onclick="exportToPDF()">PDF</button></li> -->
                <li><button class="dropdown-item" onclick="exportToExcel()">EXCEL</button></li>
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
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Mulai Tanggal Transaksi" value="<?php $tanggalAwal == "" ? "" : date('d/m/Y', strtotime($tanggalAwal)); ?>" />
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group" style="height: 50px;">
                                <input style="height: auto;" autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Selesai Tanggal Transaksi" value="<?php $tanggalAkhir == "" ? "" : date('d/m/Y', strtotime($tanggalAkhir)); ?>">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" style="height: 50px;">
                            <input style="height: auto;" autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="<?php $search == "all" ? "" : $search; ?>" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>No. Invoice</th>
                                <th>No. LPB</th>
                                <th>Divisi</th>
                                <th>Amount(IDR)</th>
                                <th>Remaining(IDR)</th>
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
                url: "<?= base_url("laporan-accounting/hutang/details/invoice/" . $id); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.dateStart = $(".dateStart").val();
                    data.dateEnd = $(".dateEnd").val();
                    data.filter = <?php echo json_encode($filter); ?>;
                    data.filter_divisi = <?php echo json_encode($filterDivisi); ?>;
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
                data: "tanggal_invoice",
                className: "text-center",
            }, {
                data: "no_invoice",
                className: "text-center",
            }, {
                data: "no_penerimaan_barang",
                className: "text-center",
            }, {
                data: "divisi_invoice",
                className: "text-center",
            }, {
                data: "nominal_invoice",
                className: "text-center",
                render: function(data) {
                    return greatFormatRupiahPayment(data);
                }
            }, {
                data: "remaining_invoice",
                className: "text-center",
                render: function(data) {
                    return greatFormatRupiahPayment(data);
                }
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

        $(".dateStart, .dateEnd").change(function() {
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

    function getFilterQuery() {
        return {
            search: $(".search").val(),
            filter: <?php echo json_encode($filter); ?>,
            filter_divisi: <?php echo json_encode($filterDivisi); ?>,
            supplierId: <?= json_encode($id) ?>,
            dateStart: $(".dateStart").val(),
            dateEnd: $(".dateEnd").val(),
            sort: sort,
            sortType: sortType
        };
    }

    function exportToPDF() {
        const params = new URLSearchParams(getFilterQuery()).toString();
        window.open(`<?= base_url("laporan-accounting/hutang/detail/print") ?>?${params}`, "_blank");
    }

    function exportToExcel() {
        const params = new URLSearchParams(getFilterQuery()).toString();
        window.open(`<?= base_url("laporan-accounting/hutang/detail/export-excel") ?>?${params}`, "_blank");
    }
</script>
<?= $this->endSection(); ?>