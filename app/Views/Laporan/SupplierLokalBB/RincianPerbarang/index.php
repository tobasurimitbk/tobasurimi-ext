<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Pendapatan Perbarang</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-supplier-lokal-bb/rincian-perbarang/print"); ?>')">PDF</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-supplier-lokal-bb"); ?>">
                Batal
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-2">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_warehouse" name="filter_warehouse" id="filter_warehouse">
                            <option value="" data-code=""></option>

                            <?php foreach ($getWarehouse as $row) : ?>
                                <option value="<?= $row['id']; ?>" data-code=""><?= $row['warehouse_name'] ?></option>
                            <?php endforeach; ?>


                        </select>
                        <label for="floatingInput">Filter Warehouse</label>
                    </div>
                </div>
                <div class="col-md-2">
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

                <div class="col-md-2">
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
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th onclick="changeSort('supplierName')" class="sort" rowspan="2">Supplier</th>
                                <th onclick="changeSort('supplierNpwp')" class="sort" rowspan="2">NPWP</th>
                                <th onclick="changeSort('barangName')" class="sort" rowspan="2">Jenis</th>
                                <th onclick="changeSort('spekName')" class="sort" rowspan="2">Spesifikasi</th>
                                <th onclick="changeSort('warehouseName')" class="sort" rowspan="2">Divisi</th>
                                <th onclick="changeSort('poNum')" class="sort" rowspan="2">No PO</th>
                                <th onclick="changeSort('poDate')" class="sort" rowspan="2">Tgl PO</th>
                                <th rowspan="2">Qty</th>
                                <th rowspan="2">Satuan</th>
                                <th colspan="3">Harian</th>
                                <th colspan="3">Tambahan Harian</th>
                            </tr>
                            <tr>
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
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "poNum";
    let sortType = "desc";
    var row = 0;

    var table = $('.dataTable').DataTable({
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
            url: "<?= base_url("/laporan-supplier-lokal-bb/rincian-perbarang/all-rincian-perbarang"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
                data.filter_supplier = $(".filter_supplier").val();
                data.filter_warehouse = $(".filter_warehouse").val();
                data.filter_barang = $(".filter_barang").val();
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
                data: "supplierNpwp",
                className: "text-center",
            },
            {
                data: "barangName",
                className: "text-center",
            },
            {
                data: "spekName",
                className: "text-center",
            },
            {
                data: "warehouseName",
                className: "text-center",
            },
            {
                data: "poNum",
                className: "text-center",
            },
            {
                data: "poDate",
                className: "text-center",
            },
            {
                data: "qtyPO",
                className: "text-center",
            },
            {
                data: "satuanName",
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

    $(".dateStart, .dateEnd, .filter_supplier, .filter_warehouse, .filter_barang").change(function() {
        table.ajax.reload();
    });

    $('.filter_supplier, .filter_warehouse, .filter_barang').select2({
        placeholder: "",
        theme: "bootstrap-5",
        allowClear: true,
    })

    $('.filter_supplier, .filter_warehouse, .filter_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_supplier, .filter_warehouse, .filter_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_supplier, .filter_warehouse, .filter_barang')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    const pdf = function(url) {

        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();
        let filter_supplier = $(".filter_supplier").val();
        let filter_warehouse = $(".filter_warehouse").val();
        let filter_barang = $(".filter_barang").val();

        window.open(url + `?filter_supplier=${filter_supplier}&filter_warehouse=${filter_warehouse}&filter_barang=${filter_barang}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>