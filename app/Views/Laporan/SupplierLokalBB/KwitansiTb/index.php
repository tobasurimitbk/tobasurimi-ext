<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Kwitansi Bulanan PO Bahan Baku</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-supplier-lokal-bb"); ?>">
                Kembali
            </a>
            <?php if (can('Laporan', 'Supplier Lokal BB', 'p')) : ?>
                <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #FFA426 !important;color: white !important;border: 0px solid !important;">
                    Print All
                </button>
                <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
                    <li><button class="dropdown-item" id="btn-print-f4">Print TB F4</button></li>
                    <li><button class="dropdown-item" id="btn-print-continous">Print TB Continous</button></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-page-list-attendance">
                <div class="col-6 mb-2">
                    <form id="search_form" action="#" name="search_form" class="kt-form kt-form--fit kt-margin-b-20">
                        <select required name="month" class="month" id="month">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                $temp = (strlen($i) == 1) ? ("0" . $i) : $i;
                                $checked = ($month == $temp) ? "selected" : "";
                            ?>
                                <option value="<?php echo $temp; ?>" <?php echo $checked; ?>><?php echo $temp; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <select required name="year" class="year" id="year">
                            <?php
                            for ($i = date("Y") - 2; $i <= date("Y") + 2; $i++) {
                                $checked = ($year == $i) ? "selected" : "";
                            ?>
                                <option value="<?php echo $i; ?>" <?php echo $checked; ?>><?php echo $i; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-brand--icon filterBulan" id="">
                            <span>
                                <i class="la la-print"></i>
                                <span>Cari</span>
                            </span>
                        </button>

                    </form>
                </div>
                <div class="col-6 mb-2">
                    <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <?= csrf_field() ?>
                <div class="row justify-content-end row-col-spp mb-3">
                    <div class="col-md-3">
                        <select class="form-select tb_search" name="tb_search" id="tb_search" aria-label="Floating label select example">
                            <option value="" selected>SEMUA SUPPLIER</option>
                            <option value="PUNYA TB">SUPPLIER PUNYA NILAI TB</option>
                            <option value="TIDAK PUNYA TB">SUPPLIER TIDAK PUNYA NILAI TB</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Data" value="" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th onclick="changeSort('name')">Supplier</th>
                                <th>Total</th>
                                <th>No Kwitansi</th>
                                <th>Tanggal</th>
                                <th style="width: 50px;">Print</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "name";
    let sortType = "asc";
    var row = 0;

    var table = $('.dataTable').DataTable({
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
            url: "<?= base_url("laporan-supplier-lokal-bb/kwitansi-tb/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.year = $(".year").val();
                data.month = $(".month").val();
                data.tb_search = $(".tb_search").val();
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            },
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
            sortable: false
        }, {
            data: "name",
            className: "text-left",
        }, {
            data: "total",
            className: "text-left",
            searchable: false,
            sortable: false,
        }, {
            data: "no_kwitansi",
            searchable: false,
            sortable: false,
            className: "text-left"
        }, {
            data: "tanggal",
            searchable: false,
            sortable: false,
            className: "text-left",
            render: function(data, type, row) {
                if (row.is_print == "0") {
                    return "-";
                } else {
                    let inputId = "tanggal_" + row.id;
                    return `
                        <div class="mt-0">
                            <input id="${inputId}" class="tanggal form-control search form-out-search" data-id="${row.id}" type="date" value="${row.tanggal}">
                        </div>
                    `;
                }

            }
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row.id;
                let no_kwitansi_hash = row.no_kwitansi_hash;
                let tanggal = row.tanggal;
                let year = $(".year").val();
                let month = $(".month").val();


                if (row.is_print == "0") {
                    return '-';
                } else {

                    return `
                            <button data-toggle="tooltip" title="Print" class="btn btn-warning btn-print" onclick="print('laporan-supplier-lokal-bb/kwitansi-tb/print/${id}/${year}-${month}/${no_kwitansi_hash}', '${id}')" style="box-shadow: none !important;">
                                <i class="fa fa-print fa-sm" aria-hidden="true"></i>
                            </button>
                        `;
                }

            }
        }],
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
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    })

    $(".filterBulan").click(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('.tb_search').change(function() {
        table.ajax.reload();
    });

    $(".search").change(function() {
        table.ajax.reload();
    })

    const print = function(url, id) {
        let inputId = "tanggal_" + id;
        let element = $('#' + inputId);
        let splitData = url.split("/");
        let res = splitData[0] + '/' + splitData[1] + '/' + splitData[2] + '/' + splitData[3] + '/' + splitData[4] + '/' + element.val() + '/' + splitData[5];

        window.open("<?= base_url('/') ?>" + res, "_blank");
    }

    $('#btn-print-f4').on('click', function(e) {
        e.preventDefault();
        var month = $('#month').val();
        var year = $('#year').val();
        if (month == "") {
            alert("Pilih bulan");
            return;
        } else if (year == "") {
            alert("Pilih tahun");
            return;
        } else {
            var url = "<?= base_url('laporan-supplier-lokal-bb/print-all-kwitansi-tb') ?>?month=" + month + "&year=" + year + "&kertas=f4";
            window.open(url);
        }
    });

    $('#btn-print-continous').on('click', function(e) {
        e.preventDefault();
        var month = $('#month').val();
        var year = $('#year').val();
        if (month == "") {
            alert("Pilih bulan");
            return;
        } else if (year == "") {
            alert("Pilih tahun");
            return;
        } else {
            var url = "<?= base_url('laporan-supplier-lokal-bb/print-all-kwitansi-tb') ?>?month=" + month + "&year=" + year + "&kertas=continous";
            window.open(url);
        }
    });

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