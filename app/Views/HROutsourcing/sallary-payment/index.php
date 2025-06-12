<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>HR Outsourcing Sallary Payment</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("hr-outsourcing-sallary-payment/create"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <!-- <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_bc_type" name="filter_bc_type" id="filter_bc_type">
                            <option selected value="all" data-code="">All</option>
                            <option value="BC 2.3" data-code="">BC 2.3</option>
                            <option value="BC 2.7" data-code="">BC 2.7</option>
                            <option value="BC 4.0" data-code="">BC 4.0</option>
                            <option value="PPB KB" data-code="">PPB KB</option>
                            <option value="Non Pabean" data-code="">Non Pabean</option>
                        </select>
                        <label for="floatingInput">Filter Tipe BC</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input autocomplete="one-time-code" class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input autocomplete="one-time-code" class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                        </div>
                    </div>
                </div> -->
            </div>
            <!-- <div class="row justify-content-end row-col-spp">
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_divisi" name="filter_divisi" id="filter_divisi">
                            <option selected value="">Semua Divisi</option>
                            <?php foreach ($divisis as $divisi) : ?>
                                <option value="<?= $divisi['id']; ?>"><?= $divisi['divisi']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Filter Divisi</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_supplier" name="filter_supplier" id="filter_supplier">
                            <option selected value="">Semua Supplier</option>
                            <?php foreach ($suppliers as $supplier) : ?>
                                <option value="<?= $supplier['id']; ?>"><?= $supplier['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Filter Supplier</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_barang" name="filter_barang" id="filter_barang">
                            <option selected value="">Semua Barang</option>
                            <?php foreach ($barangs as $barang) : ?>
                                <option value="<?= $barang['barang_master_spesifikasi_id']; ?>"><?= $barang['barang_name_master'] . ' ' . $barang['spesifikasi']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Filter Barang</label>
                    </div>
                </div>
            </div> -->
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('bc_type')" class="sort">Jenis Doc</th>
                                <th onclick="changeSort('tanggal_bc')" class="sort">Tanggal Doc</th>
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

<!-- <script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "tanggal_lpb";
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
            url: "<?= base_url("/laporan-warehouse/penerimaan-barang/all-penerimaan-barang"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.filter_bc_type = $(".filter_bc_type").val();
                data.sort = sort;
                data.sortType = sortType;
                data.filter_divisi = $(".filter_divisi").val();
                data.filter_supplier = $(".filter_supplier").val();
                data.filter_barang = $(".filter_barang").val();
                console.log(data);
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
                data: "bc_type",
                className: "text-center",

            },
            {
                data: "tanggal_bc",
                className: "text-center",
            },
            {
                data: "no_daftar",
                className: "text-center",
            },
            {
                data: "no_aju",
                className: "text-center",
            },
            {
                data: "no_penerimaan_barang",
                className: "text-center",
            },
            {
                data: "tanggal_lpb",
                className: "text-center",
            },
            {
                data: "po_no",
                className: "text-center",
            },
            {
                data: "po_date",
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
                data: "nama_barang_dok",
                className: "text-center",
            },
            {
                data: "kode_satuan",
                className: "text-center",
            },
            {
                data: "qty",
                className: "text-center",
            },
            {
                data: "jml_masuk",
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

    $(".dateStart, .dateEnd, .filter_bc_type, .filter_divisi, .filter_supplier, .filter_barang").change(function() {
        table.ajax.reload();
    });

    $('.filter_divisi, .filter_supplier, .filter_barang').select2({
        theme: "bootstrap-5",
        allowClear: true
    })

    $('.filter_bc_type').select2({
        theme: "bootstrap-5",
        allowClear: false
    })

    $('.filter_bc_type, .filter_divisi, .filter_supplier, .filter_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_bc_type, .filter_divisi, .filter_supplier, .filter_barang')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_bc_type, .filter_divisi, .filter_supplier, .filter_barang')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    const pdf = function(url) {

        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();
        let filter_bc_type = $(".filter_bc_type").val();
        let filter_divisi = $(".filter_divisi").val();
        let filter_supplier = $(".filter_supplier").val();
        let filter_barang = $(".filter_barang").val();

        window.open(url + `?filter_bc_type=${filter_bc_type}&filter_divisi=${filter_divisi}&filter_supplier=${filter_supplier}&filter_barang=${filter_barang}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script> -->

<?= $this->endSection(); ?>