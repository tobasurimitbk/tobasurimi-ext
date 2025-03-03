<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Barang Work In Progres</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: -1px;">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-wip/print"); ?>')">PDF</button></li>
            <li><button class="dropdown-item pdf" onclick="pdfExcel('<?= base_url("/laporan-bea-cukai/laporan-wip/excel"); ?>')">Excel</button></li>
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
                        <select class="form-select status_produksi" name="status_produksi" id="status_produksi">
                            <option value="ALL" selected>SEMUA PRODUKSI</option>
                            <option value="ACTIVE">PRODUKSI AKTIF</option>
                        </select>
                        <label style="z-index: 1;">Status Produksi</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal Produksi</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir Produksi</label>
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
                        <select class="form-select divisi_id" name="divisi_id" id="divisi_id">
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
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control nama_barang" placeholder="Kode / Nama Barang">
                        <label style="z-index: 1;">Kode / Nama Barang</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <input type="text" name="kode_produksi" id="kode_produksi" class="form-control kode_produksi" placeholder="Kode Produksi">
                        <label style="z-index: 1;">Kode Produksi</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('material_request_details.barang_type')" class="sort">Tipe Barang</th>
                                <th onclick="changeSort('barang_master.kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('barang_master.barang_name')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('material_request_details.qty')">Qty</th>
                                <th onclick="changeSort('material_request_details.satuan')">Satuan</th>
                                <th onclick="changeSort('material_request_details.note')">Keterangan</th>
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
    let sort = "material_request_details.createdAt";
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
            url: "<?= base_url("laporan-bea-cukai/all-wip"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.status_produksi = $(".status_produksi").val();
                data.date_start = $(".dateStart").val();
                data.date_end = $(".dateEnd").val();
                data.divisi_id = $('.divisi_id').val();
                data.nama_barang = $('.nama_barang').val();
                data.kode_produksi = $('.kode_produksi').val();
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
                data: "tipeBarang",
                className: "text-center",

            },
            {
                data: "kodeBarang",
                className: "text-center",

            },
            {
                data: "namaBarang",
                className: "text-center",
            },
            {
                data: "qty",
                className: "text-center",
            },
            {
                data: "satuan",
                className: "text-center",
            },

            {
                data: "keterangan",
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
    $('#status_produksi').select2({
        placeholder: "Pilih Status Produksi",
        theme: "bootstrap-5",
        allowClear: false
    });
    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $('.divisi_id, .dateStart, .dateEnd, .status_produksi').change(function() {
        table.ajax.reload();
    });

    $('.nama_barang,.kode_produksi').keyup(function() {
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

    $("#status_produksi")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const pdfExcel = function(url) {
        let status_produksi = $(".status_produksi").val();
        let date_start = $(".dateStart").val();
        let date_end = $(".dateEnd").val();
        let divisi_id = $(".divisi_id").val();
        let nama_barang = $(".nama_barang").val();
        let kode_produksi = $(".kode_produksi").val();
        // let sort = "stock_details2.createdAt";
        // let sortType = "desc";
        window.open(url + `?status_produksi=${status_produksi}&date_start=${date_start}&date_end=${date_end}&divisi_id=${divisi_id}&bnama_barang=${nama_barang}&kode_produksi=${kode_produksi}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>