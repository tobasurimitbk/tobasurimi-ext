<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Sales Order</h1>
        <button class="btn btn-discard btn-dropdown-export dropdown-toggle float-right" type="button" id="dropdownMenuButtonExport" data-bs-toggle="dropdown" aria-expanded="false">
            Export
        </button>
        <ul class="dropdown-menu list-dropdown-company" aria-labelledby="dropdownMenuButtonExport">
            <li><button class="dropdown-item pdf" onclick="pdf('<?= base_url("/laporan-warehouse/sales-order/print"); ?>')">PDF</button></li>
        </ul>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-warehouse"); ?>">
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
                        <select class="form-select filter_jenis_dokumen" name="filter_jenis_dokumen" id="filter_jenis_dokumen">
                            <option value="" data-code=""></option>
                            <option value="pengiriman" data-code="">Pengiriman</option>
                            <option value="pesanan" data-code="">Pesanan</option>
                        </select>
                        <label for="floatingInput">Filter Jenis Dokumen</label>
                    </div>
                </div>
                <!-- <div class="col-md-2">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_status" name="filter_status" id="filter_status">
                            <option value="" data-code=""></option>
                            <option value="0" data-code="">Waiting</option>
                            <option value="1" data-code="">Finish</option>
                        </select>
                        <label for="floatingInput">Filter Status</label>
                    </div>
                </div> -->


            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('document_type')" class="sort">Jenis PO</th>
                                <th onclick="changeSort('no_faktur')" class="sort">Nomor</th>
                                <th onclick="changeSort('tanggal_faktur')" class="sort">Tanggal</th>
                                <th onclick="changeSort('nama_pelanggan')" class="sort">Customer</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('nama_barang')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('satuan')" class="sort">Satuan</th>
                                <th onclick="changeSort('document_no')" class="sort">Dokumen No</th>
                                <th>Jumlah Order</th>
                                <th>Jumlah Diterima</th>
                                <th>Total Harga</th>
                                <th>Sisa</th>
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
    let sort = "no_faktur";
    let sortType = "asc";
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
            url: "<?= base_url("/laporan-warehouse/sales-order/all-sales-order"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.sort = sort;
                data.sortType = sortType;
                data.filter_jenis_dokumen = $(".filter_jenis_dokumen").val();
                data.filter_status = $(".filter_status").val();

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
                data: "document_type",
                className: "text-center",

            },
            {
                data: "no_faktur",
                className: "text-center",
            },
            {
                data: "tanggal_faktur",
                className: "text-center",
            },
            {
                data: "nama_pelanggan",
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
                data: "satuan",
                className: "text-center",
            },
            {
                data: "document_no",
                className: "text-center",
            },
            {
                data: "qty",
                className: "text-center",
            },
            {
                data: "qty_invoice",
                className: "text-center",
            },
            {
                data: "amount_invoice",
                className: "text-center",
            },
            {
                data: "qty_sekarang",
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

    $(".dateStart, .dateEnd, .filter_jenis_dokumen, .filter_status").change(function() {
        table.ajax.reload();
    });

    $('.filter_jenis_dokumen, .filter_status').select2({
        placeholder: "",
        theme: "bootstrap-5",
        allowClear: true,
    })

    $('.filter_jenis_dokumen, .filter_status')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_jenis_dokumen, .filter_status')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_jenis_dokumen, .filter_status')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    const pdf = function(url) {

        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();
        let filter_jenis_dokumen = $(".filter_jenis_dokumen").val();
        let filter_status = $(".filter_status").val();


        window.open(url + `?filter_jenis_dokumen=${filter_jenis_dokumen}&filter_status=${filter_status}&dateStart=${dateStart}&dateEnd=${dateEnd}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script>

<?= $this->endSection(); ?>