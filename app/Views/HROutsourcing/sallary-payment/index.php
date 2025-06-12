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
            <a class="btn btn-success float-right" href="<?= base_url("hr-outsourcing-sallary-payment/create"); ?>">
                Tambah
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
              
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th  class="sort">Jenis Doc</th>
                                <th class="sort">Tanggal Doc</th>
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