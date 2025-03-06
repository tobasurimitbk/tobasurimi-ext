<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Kartu Stock</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-warehouse/stock-kartu/print"); ?>')">Excel</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-warehouse"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row ">
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_tipe_barang" name="filter_tipe_barang" id="filter_tipe_barang">
                            <option disabled selected value=""></option>
                            <?php foreach ($tipe_barang as $td) : ?>
                                <option value="<?= $td ?>"><?= strtoupper(str_replace('_', " ", $td)) ?></option>
                            <?php endforeach ?>
                        </select>
                        <label style="z-index: 1;">Filter Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_tipe_divisi" name="filter_tipe_divisi" id="filter_tipe_divisi">
                            <option disabled selected value=""></option>
                            <?php foreach ($divisi as $d) : ?>
                                <option value="<?= $d ?>"><?= $d ?></option>
                            <?php endforeach ?>
                        </select>
                        <label style="z-index: 1;">Department</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select filter_warehouse" name="filter_warehouse" id="filter_warehouse">
                            <option disabled selected value=""></option>
                            <?php foreach ($warehouse as $w) : ?>
                                <option value="<?= $w ?>"><?= $w ?></option>
                            <?php endforeach ?>
                        </select>
                        <label style="z-index: 1;">Warehouse</label>

                    </div>

                </div>
                <div class="col mb-4">
                    <div class="form-floating">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;">Cari Kode / Nama Barang </label>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('tipe_barang')" class="sort">Tipe Barang</th>
                                <th onclick="changeSort('no aju')" class="sort">Sumber Barang (BC/No Aju/Sumber)</th>
                                <th onclick="changeSort('supplier')" class="sort">Supplier</th>
                                <th onclick="changeSort('department')" class="sort">Department </th>
                                <th onclick="changeSort('warehouse')" class="sort">Warehouse</th>
                                <th onclick="changeSort('tanggal_penerimaan')" class="sort">Tanggal Penerimaan</th>
                                <th onclick="changeSort('no_dok')" class="sort">Nomor</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang </th>
                                <th onclick="changeSort('nama_baramg')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('spesifikasi')" class="sort">Spesifikasi</th>
                                <th onclick="changeSort('satuan')" class="sort">Satuan</th>
                                <th onclick="changeSort('qty')" class="sort">Qty</th>
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
    let sort = "tanggal_penerimaan";
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
            url: "<?= base_url("laporan-warehouse/stock-kartu/all-stock-kartu"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $('.search').val();
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.tipeBarang = $('#filter_tipe_barang').val();
                data.divisi = $('#filter_tipe_divisi').val();
                data.warehouse = $('#filter_warehouse').val();
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
            },
            {
                data: "tipe_barang",
                className: "text-center",

            },
            {
                data: "sumber_barang",
                className: "text-center",
            },
            {
                data: "supplier",
                className: "text-center",
            },
            {
                data: "department",
                className: "text-center",
            },
            {
                data: "warehouse_name",
                className: "text-center",
            },
            {
                data: "tanggal_penerimaan",
                className: "text-center",
            },
            {
                data: "nomor",
                className: "text-center",
            },
            {
                data: "kode_barang",
                className: "text-center",
            },
            {
                data: "nama_barang",
                className: "text-center",
            },
            {
                data: "spesifikasi",
                className: "text-center",
            },
            {
                data: "satuan",
                className: "text-center",
            },
            {
                data: "qty",
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
    $('#filter_tipe_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#filter_tipe_divisi').select2({
        placeholder: "Pilih Department",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#filter_warehouse').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $(".dateStart, .dateEnd, .filter_tipe_barang, .filter_tipe_divisi, .filter_warehouse ").change(function() {
        table.ajax.reload();
    });
    $(".search").keyup(function() {
        table.ajax.reload();
    })
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const pdf = function(url) {


        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();
        let filter_tipe_barang = $(".filter_tipe_barang").val();
        let divisi = $(".filter_tipe_divisi").val();
        let warehouse = $(".filter_warehouse").val();
        let search = $('.search').val();
        let length = 100;

        window.open(url + `?tipeBarang=${filter_tipe_barang}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}&length=${length}&divisi=${divisi}&search=${search}&warehouse=${warehouse}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>