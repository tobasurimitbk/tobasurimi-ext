<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Pendapatan All Supplier (Pembelian)</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-supplier-lokal-bb/rekap-all-supplier-pembelian/print"); ?>')">PDF</button></li>
            <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-supplier-lokal-bb/rekap-all-supplier-pembelian/print-excel"); ?>')">Excel</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-supplier-lokal-bb"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
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

                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_divisi" name="filter_divisi" id="filter_divisi">
                            <option value="" data-code=""></option>

                            <?php foreach ($getDivisi as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= strtoupper($row["divisi"]); ?></option>
                            <?php endforeach; ?>

                        </select>
                        <label for="floatingInput">Filter Department</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th onclick="changeSort('supplierName')" class="sort" rowspan="2">Supplier</th>
                                <th onclick="changeSort('divisiName')" class="sort" rowspan="2">Department</th>
                                <th onclick="changeSort('barangName')" class="sort" rowspan="2">Bahan Baku</th>
                                <th onclick="changeSort('barangName')" class="sort" rowspan="2">Satuan</th>
                                <th onclick="changeSort('barangName')" class="sort" rowspan="2">Qty</th>
                                <th style="text-align: center;">Tambahan Bulanan</th>
                                <th rowspan="2" style="text-align: center;">Total</th>
                            </tr>
                            <tr>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">TOTAL</th>
                                <th id="ft-qtyall" class="text-center">0</th>
                                <th id="ft-totalBulanan" class="text-center">0</th>
                                <th id="ft-totalRow" class="text-center">0</th>
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
            [25]
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
        initComplete: function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false
            },
            {
                data: "supplierName",
                className: "text-center"
            },
            {
                data: "divisiName",
                className: "text-center"
            },
            {
                data: "barangName",
                className: "text-center"
            },
            {
                data: "satuanName",
                className: "text-center"
            },
            {
                data: "qtyPO",
                className: "text-center"
            },
            {
                data: "totalBulanan",
                className: "text-center"
            },
            {
                data: "totalRow",
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
        },
        drawCallback: function(settings) {
            const json = settings.json;
            if (json && json.footerTotals) {
                $('#ft-qtyall').html(json.footerTotals.qtyPO);

                $('#ft-totalBulanan').html(json.footerTotals.totalBulanan);

                $('#ft-totalRow').html(json.footerTotals.totalRow);
            }
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
        let filter_divisi = $(".filter_divisi").val();

        window.open(url + `?filter_supplier=${filter_supplier}&filter_barang=${filter_barang}&filter_divisi=${filter_divisi}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>