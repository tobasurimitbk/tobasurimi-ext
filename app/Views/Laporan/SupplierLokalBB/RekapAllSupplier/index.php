<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Pendapatan All Supplier</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <!-- <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-supplier-lokal-bb/rekap-all-supplier/print"); ?>')">PDF</button></li> -->
            <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-supplier-lokal-bb/rekap-all-supplier/print-excel"); ?>')">Excel</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-supplier-lokal-bb"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" value="01/<?= date("m/Y") ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
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
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
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
                        <select class="form-select filter_supplier" name="filter_supplier" id="filter_supplier">
                            <option value="" data-code=""></option>

                            <?php foreach ($getSupplier as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= $row['name'] ?></option>
                            <?php endforeach; ?>


                        </select>
                        <label for="floatingInput">Filter Suplier</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_barang" name="filter_barang" id="filter_barang">
                            <option value="" data-code=""></option>

                            <?php foreach ($getBarang as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= strtoupper($row["barang_name"]); ?></option>
                            <?php endforeach; ?>


                        </select>
                        <label for="floatingInput">Filter Barang</label>
                    </div>
                </div>

                <!-- <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_divisi" name="filter_divisi" id="filter_divisi">
                            <option value="" data-code=""></option>

                            <?php foreach ($getDivisi as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= strtoupper($row["divisi"]); ?></option>
                            <?php endforeach; ?>

                        </select>
                        <label for="floatingInput">Filter Department</label>
                    </div>
                </div> -->
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th onclick="changeSort('supplierName')" class="sort" rowspan="2">Supplier</th>
                                <th onclick="changeSort('barangName')" class="sort" rowspan="2">Bahan Baku</th>
                                <th onclick="changeSort('barangName')" class="sort" rowspan="2">Satuan</th>
                                <th onclick="changeSort('barangName')" class="sort" rowspan="2">Qty</th>
                                <th colspan="3">Harian</th>
                                <th colspan="3">Tambahan Harian</th>
                                <th colspan="3">Tambahan Bulanan</th>
                                <th colspan="3">Subsidi</th>
                                <th rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                                <th>DPP</th>
                                <th>PPh</th>
                                <th>Dibayarkan</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">TOTAL</th>
                                <th class="text-center">-</th> <!-- Satuan -->
                                <th class="text-center">0</th> <!-- DPP Harian -->
                                <th class="text-center">0</th> <!-- PPh Harian -->
                                <th class="text-center">0</th> <!-- Dibayarkan Harian -->
                                <th class="text-center">0</th> <!-- DPP Tambahan Harian -->
                                <th class="text-center">0</th> <!-- PPh Tambahan Harian -->
                                <th class="text-center">0</th> <!-- Dibayarkan Tambahan Harian -->
                                <th class="text-center">0</th> <!-- DPP Tambahan Bulanan -->
                                <th class="text-center">0</th> <!-- PPh Tambahan Bulanan -->
                                <th class="text-center">0</th> <!-- Dibayarkan Tambahan Bulanan -->
                                <th class="text-center">0</th> <!-- DPP Subsidi -->
                                <th class="text-center">0</th> <!-- PPh Subsidi -->
                                <th class="text-center">0</th> <!-- Dibayarkan Subsidi -->
                                <th class="text-center">0</th> <!-- Grand Total -->
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "supplierName";
    let sortType = "desc";
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
            url: "<?= base_url("/laporan-supplier-lokal-bb/rekap-all-supplier/all-rekap-all-supplier"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
                data.filter_supplier = $(".filter_supplier").val();
                data.filter_barang = $(".filter_barang").val();
                data.filter_divisi = $(".filter_divisi").val();
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
            },
            {
                data: "supplierName",
                className: "text-center",

            },

            {
                data: "barangName",
                className: "text-center",
            },
            {
                data: "satuanName",
                className: "text-center",
            },
            {
                data: "qtyPO",
                className: "text-center",
            },
            {
                data: "dppUmum",
                className: "text-center",
            },
            {
                data: "pphUmum",
                className: "text-center",
            },
            {
                data: "totalUmum",
                className: "text-center",
            },
            {
                data: "dppHarian",
                className: "text-center",
            },
            {
                data: "pphHarian",
                className: "text-center",
            },
            {
                data: "totalHarian",
                className: "text-center",
            },
            {
                data: "dppBulanan",
                className: "text-center",
            },
            {
                data: "pphBulanan",
                className: "text-center",
            },
            {
                data: "totalBulanan",
                className: "text-center",
            },
            {
                data: "subsidi",
                className: "text-center",
            },
            {
                data: "pphSubsidi",
                className: "text-center",

            },
            {
                data: "totalSubsidi",
                className: "text-center",
            },
            {
                data: "totalRow",
                className: "text-center",
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();

            // Kolom-kolom yang ingin di-total (indeks dimulai dari 0)
            var columnsToSum = [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17];

            columnsToSum.forEach(function(col) {
                var total = api
                    .column(col, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return destroyFormatRupiah(a) + destroyFormatRupiah(b);
                    }, 0);

                // Update footer
                $(api.column(col).footer()).html(greatFormatRupiah(total.toFixed(2)));
            });
        },
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

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

    $(".dateStart, .dateEnd, .filter_supplier, .filter_barang, .filter_divisi").change(function() {
        table.ajax.reload();
    });

    $('.filter_supplier').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        allowClear: true,
    })

    $('.filter_divisi').select2({
        placeholder: "Pilih Department",
        theme: "bootstrap-5",
        allowClear: true,
    })

    $('.filter_barang').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        allowClear: true,
    })


    $('.filter_supplier, .filter_barang, .filter_divisi')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_supplier, .filter_barang, .filter_divisi')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_supplier, .filter_barang, .filter_divisi')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    const pdf = function(url) {

        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();
        let filter_supplier = $(".filter_supplier").val();

        let filter_barang = $(".filter_barang").val();

        window.open(url + `?filter_supplier=${filter_supplier}&filter_barang=${filter_barang}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>