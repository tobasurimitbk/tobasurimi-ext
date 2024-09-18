<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Mutasi Barang Jadi</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: -1px;">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-mutasi/print"); ?>')">PDF</button></li>
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-mutasi/excel"); ?>')">Excel</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-bea-cukai"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row ">
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select kategori_barang" name="kategori_barang" id="kategori_barang">
                            <option value="ALL" selected>SEMUA BARANG</option>
                            <?php foreach ($tipeBarang as $t): ?>
                                <option value="<?= $t['description'] ?>"><?= strtoupper($t['value']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Kategori Barang</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group mb-3">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" value="<?= date('d/m/Y', strtotime(date('Y-m-01'))) ?>" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group mb-3">
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
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select divisi_id" name="divisi_id" id="divisi_id" onchange="getListWarehouse()">
                            <option disabled selected value=""></option>
                            <?php foreach ($dataDivisi as $d) : ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= $d['divisi']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Pilih Departemen</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select warehouse_id" name="warehouse_id" id="warehouse_id">
                            <option disabled selected value=""></option>

                        </select>
                        <label style="z-index: 1;">Pilih Warehouse</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <input type="text" name="search" id="search" class="form-control search" placeholder="Kode / Nama Barang">
                        <label style="z-index: 1;">Kode / Nama Barang</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('satuan')">Satuan</th>
                                <th onclick="changeSort('kategori_barang')">Kategori Barang</th>
                                <th onclick="changeSort('divisi')">Departemen</th>
                                <th onclick="changeSort('warehouse')">Warehouse</th>
                                <th>Stok Awal</th>
                                <th>Pemasukan</th>
                                <th>Pengeluaran</th>
                                <th>Penyesuaian</th>
                                <th>Stok Akhir</th>
                                <th>Dead Stok</th>
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
    let sort = "stock.createdAt";
    let sortType = "desc";
    let kategori_barang = ["bahan_jadi"];

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
            url: "<?= base_url("laporan-bea-cukai/all-mutasi-barang"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.kategori_barang = JSON.stringify(kategori_barang);
                data.date_start = $(".dateStart").val();
                data.date_end = $(".dateEnd").val();
                data.divisi_id = $('.divisi_id').val();
                data.warehouse_id = $('.warehouse_id').val();
                data.search = $('.search').val();
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
                data: "kode_barang",
                className: "text-center",

            },
            {
                data: "nama_barang",
                className: "text-center",

            },
            {
                data: "satuan",
                className: "text-center",
            },
            {
                data: "kategori_barang",
                className: "text-center",
            },
            {
                data: "divisi",
                className: "text-center",
            },
            {
                data: "warehouse",
                className: "text-center",
            },
            {
                data: "stok_awal",
                className: "text-center",
                sortable: false
            },
            {
                data: "stok_pemasukan",
                className: "text-center",
                sortable: false
            },
            {
                data: "stok_pengeluaran",
                className: "text-center",
                sortable: false
            },
            {
                data: "stok_penyesuaian",
                className: "text-center",
                sortable: false
            },
            {
                data: "stok_akhir",
                className: "text-center",
                sortable: false
            },
            {
                data: "stok_dead",
                className: "text-center",
                sortable: false
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
    $('#kategori_barang').select2({
        placeholder: "Pilih Kategori Barang",
        theme: "bootstrap-5",
        allowClear: false
    });
    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('#warehouse_id').select2({
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

    $('.divisi_id, .dateStart, .dateEnd, .warehouse_id, .kategori_barang').change(function() {
        // kategori change
        var selectedKategori = $('#kategori_barang option:selected').val();
        if (selectedKategori == "ALL") {
            kategori_barang = ['bahan_jadi'];
        } else {
            kategori_barang = ['bahan_jadi'];
        }
        table.ajax.reload();
    });

    $('.search').keyup(function() {
        table.ajax.reload();
    });

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $("#kategori_barang,#divisi_id,#warehouse_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const pdfExcel = function(url) {
        let date_start = $(".dateStart").val();
        let date_end = $(".dateEnd").val();
        let divisi_id = $(".divisi_id").val();
        let warehouse_id = $('.warehouse_id').val();
        let search = $(".search").val();
        // let sort = "stock_details2.createdAt";
        // let sortType = "desc";
        window.open(url + `?kategori_barang=${JSON.stringify(kategori_barang)}&date_start=${date_start}&date_end=${date_end}&divisi_id=${divisi_id}&bsearch=${search}&warehouse_id=${warehouse_id}&sort=${sort}&sortType=${sortType}`, "_blank");
    }

    function getListWarehouse() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('proses-rebus/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $("#divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $("#warehouse_id").empty()
                $("#warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $("#warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $("#warehouse_id").val();
            }
        });
    }
</script>

<?= $this->endSection(); ?>