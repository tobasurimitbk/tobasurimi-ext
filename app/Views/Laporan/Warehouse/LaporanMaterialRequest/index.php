<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Materal Request</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-warehouse/material-request/print"); ?>')">PDF</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-warehouse"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_tipe_barang" name="filter_tipe_barang" id="filter_tipe_barang">
                            <option selected value="bahan_baku" data-code="">Bahan Baku</option>
                            <option value="bahan_penolong" data-code="">Bahan Penolong</option>
                            <option value="bahan_jadi" data-code="">Bahan Jadi</option>
                            <option value="bahan_scrap" data-code="">Bahan Scrap</option>
                            <option value="bahan_setengah_jadi" data-code="">Bahan Setengah Jadi</option>
                        </select>
                        <label for="floatingInput">Filter Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-password">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div>



            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('wo_no')" class="sort">Kode Produksi</th>
                                <th onclick="changeSort('req_no')" class="sort">No Request</th>
                                <th onclick="changeSort('request_date')" class="sort">Tanggal Request</th>
                                <th onclick="changeSort('divisi')" class="sort">Departemen</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('qty2')" class="sort">Jumlah</th>
                                <th onclick="changeSort('satuan')" class="sort">Satuan</th>
                                <th onclick="changeSort('ref_no')" class="sort">Refrensi</th>

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
    let sort = "request_date";
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
            url: "<?= base_url("laporan-warehouse/material-request/all-material-request"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.filter_tipe_barang = $(".filter_tipe_barang").val();
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
                data: "wo_no",
                className: "text-center",

            },
            {
                data: "req_no",
                className: "text-center",
            },
            {
                data: "request_date",
                className: "text-center",
            },
            {
                data: "divisi",
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
                data: "qty2",
                className: "text-center",
            },
            {
                data: "satuan",
                className: "text-center",
            },
            {
                data: "ref_no",
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

    $(".dateStart, .dateEnd, .filter_tipe_barang").change(function() {
        table.ajax.reload();
    });

    $('.filter_tipe_barang').select2({
        placeholder: "",
        theme: "bootstrap-5"
    })

    $('.filter_tipe_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_tipe_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_tipe_barang')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    const pdf = function(url) {

        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();
        let filter_tipe_barang = $(".filter_tipe_barang").val();


        window.open(url + `?filter_tipe_barang=${filter_tipe_barang}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>