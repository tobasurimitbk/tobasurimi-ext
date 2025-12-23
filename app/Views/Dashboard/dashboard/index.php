<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- 
<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table nowrap table-hover-tobasurimi dataTableBC" id="dataTableBC" width="100%" cellspacing="0">
                            <thead class="thead-dark" id="head-table">

                            </thead>
                            <tbody class="body-table" id="body-table" style="cursor: pointer;">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div> -->








<section class="section">

    <div class="section-header">
        <h1>Dashboard</h1>

    </div>
    <!-- <b>
        Dashboard Akan Dibuat Setelah Semua Modul Siap
    </b> -->
    <div class="card">
        <!-- <div class="card-header card-dashboard-text">
            <b>MENU CEPAT</b>
        </div>
        <div class="card-body">
        </div> -->
        <!-- <div class="card-body">
            <div class="row justify-content-center" style="margin-top: -20px;">

                <div class="col-sm-1 mr-4 ml-4 mt-3">
                    <div class="btn-group dropend">
                        <button class="btn dropdown-toggle p-3 card-dashboard" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-store"></i> <span>Purchase</span>
                            <?php if (!empty($jumlah_pembayaran)) : ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                    <?= !empty($jumlah_pembayaran) ? $jumlah_pembayaran : "-" ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (can('Pembelian', 'SPP', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("spp"); ?>">SPP</a></li>
                            <?php endif; ?>
                            <?php if (can('Pembelian', 'PO Lokal BB', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("po-lokal-bahan-baku"); ?>">PO LOKAL BB </a></li>
                            <?php endif; ?>
                            <?php if (can('Pembelian', 'PO Lokal BP', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("po-lokal-bahan-penolong"); ?>">PO LOKAL BP</a></li>
                            <?php endif; ?>
                            <?php if (can('Pembelian', 'PO Import BB', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("po-import-bahan-baku"); ?>">PO IMPORT BB</a></li>
                            <?php endif; ?>
                            <?php if (can('Pembelian', 'PO Import BP', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("po-import-bahan-penolong"); ?>">PO IMPORT BP</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-1 mr-4 ml-4 mt-3">
                    <div class="btn-group dropend">
                        <button class="btn dropdown-toggle p-3 card-dashboard" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-barcode"></i> Sales
                            <?php if (!empty($jumlah_sales_bulan_ini)) : ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                    <?= !empty($jumlah_sales_bulan_ini) ? $jumlah_sales_bulan_ini : "-" ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (can('Penjualan Lokal', 'Order Form', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("order-form-lokal"); ?>">PENJUALAN LOKAL</a></li>
                            <?php endif; ?>
                            <?php if (can('Penjualan Ekspor', 'Order Form', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("order-form-internasional"); ?>">PENJUALAN EKSPOR</a></li>
                            <?php endif; ?>
                            <?php if (can('Penjualan Lain', 'Order Form', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("order-form-lain"); ?>">PENJUALAN LAIN</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <?php if (can('Laporan', 'Warehouse', 'r')) : ?>
                    <div class="col-sm-1 mr-4 ml-4 mt-3">
                        <div class="btn-group card-dashboard">
                            <a href="<?= base_url("laporan-warehouse"); ?>" class="btn p-3 card-dashboard">
                                <i class="fas fa-truck"></i> Material Req.
                                <?php if (!empty($jumlah_warehouse_bulan_ini)) : ?>
                                    <span class=" position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        <?= !empty($jumlah_warehouse_bulan_ini) ? $jumlah_warehouse_bulan_ini : "-" ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (can('Produksi', 'Work Order', 'r')) : ?>
                    <div class="col-sm-1 mr-4 ml-4 mt-3">
                        <div class="btn-group card-dashboard">
                            <a href="<?= base_url("work-order"); ?>" class="btn p-3 card-dashboard">
                                <i class="fas fa-play"></i> Production
                                <?php if (!empty($jumlah_work_orders)) : ?>
                                    <span class=" position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        <?= !empty($jumlah_work_orders) ? $jumlah_work_orders : "-" ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-sm-1 mr-4 ml-4 mt-3">
                    <div class="btn-group dropend">
                        <button class="btn dropdown-toggle p-3 card-dashboard" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-truck"></i> Jasa Vendor

                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">

                            <?php if (can('Jasa Vendor', 'Proses Rebus', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("proses-rebus"); ?>">PROSES REBUS</a></li>
                            <?php endif; ?>

                            <?php if (can('Jasa Vendor', 'Barang Keluar', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("jasa-vendor-out"); ?>">BARANG KELUAR </a></li>
                            <?php endif; ?>

                            <?php if (can('Jasa Vendor', 'Barang Masuk', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("jasa-vendor-in"); ?>">BARANG MASUK</a></li>
                            <?php endif; ?>

                            <?php if (can('Jasa Vendor', 'Biaya Udang', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("biaya-udang"); ?>">BIAYA UDANG</a></li>
                            <?php endif; ?>

                            <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url('biaya-kepiting'); ?>">BIAYA KEPITING</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <?php if (can('Master Data', 'Kurs', 'r')) : ?>
                    <div class="col-sm-1 mr-4 ml-4 mt-3">
                        <div class="btn-group card-dashboard">
                            <a href="<?= base_url("kurs"); ?>" class="btn p-3 card-dashboard">
                                <div class="text-center">
                                    <i class="fa-solid fa-dollar-sign fa-lg"></i>
                                    KURS
                                </div>

                                <?php if (!empty($jumlah_sales_bulan_ini)) : ?>
                                    <span class=" position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        <?= !empty($jumlah_sales_bulan_ini) ? $jumlah_sales_bulan_ini : "-" ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (can('Settings', 'Manajemen User', 'r')) : ?>
                    <div class="col-sm-1 mr-4 ml-4 mt-3">
                        <div class="btn-group card-dashboard">
                            <a href="<?= base_url("user");  ?>" class="btn p-3 card-dashboard">
                                <i class="fas fa-users"></i> Users
                                <?php if (!empty($jumlah_users)) : ?>
                                    <span class=" position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        <?= !empty($jumlah_users) ? $jumlah_users : "-" ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-sm-1 mr-4 ml-4 mt-3">
                    <div class="btn-group dropend">
                        <button class="btn dropdown-toggle p-3 card-dashboard" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-barcode"></i> Products
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (can('Master Barang', 'Bahan Baku', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("barang-bahan-baku");  ?>">BAHAN BAKU</a></li>
                            <?php endif; ?>
                            <?php if (can('Master Barang', 'Bahan Penolong', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("barang-bahan-penolong");  ?>">BAHAN PENOLONG</a></li>
                            <?php endif; ?>
                            <?php if (can('Master Barang', 'Bahan jadi', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("barang-bahan-jadi");  ?>">BAHAN JADI</a></li>
                            <?php endif; ?>
                            <?php if (can('Master Barang', 'Barang Scrap', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("barang-scrap");  ?>">BARANG SCRAP</a></li>
                            <?php endif; ?>
                            <?php if (can('Master Barang', 'Barang Modal', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("barang-modal");  ?>">BARANG MODAL</a></li>
                            <?php endif; ?>
                            <?php if (can('Master Barang', 'Bahan Setengah Jadi', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("barang-setengah-jadi");  ?>">BAHAN SETENGAH JADI</a></li>
                            <?php endif; ?>
                            <?php if (can('Master Barang', 'Kemasan', 'r')) : ?>
                                <li><a class="dropdown-item" href="<?= base_url("kemasan");  ?>">KEMASAN</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <?php if (can('Master Data', 'Customer Global', 'r')) : ?>
                    <div class="col-sm-1 mr-4 ml-4 mt-3">
                        <div class="btn-group card-dashboard">
                            <a href="<?= base_url("customer");  ?>" class="btn p-3 card-dashboard">
                                <i class="fas fa-address-book"></i> Customer
                                <?php if (!empty($jumlah_customer)) : ?>
                                    <span class=" position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        <?= !empty($jumlah_customer) ? $jumlah_customer : "-" ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>



            </div>
        </div>
        <hr>
        <div class="row p-3">
            <div class="col-sm-8">
                <div class="card-header card-dashboard-text text-bold" id="card-dashboard-text">
                    <b>WORK IN PROGRESS</b>
                </div>
                <hr>
                <div class="card-body" style="margin-top: -20px; margin-bottom: -20px;">
                    <br>
                    <div class="row">
                        <div class="col-sm">
                            <div class="input-group input-group-password align-items-center">
                                <input autocomplete="one-time-code" class="form-control input-picker p-4 bulanProduksi" id="bulanProduksi" name="bulanProduksi" placeholder="Pilih Bulan" value="<?= date('m/Y') ?>">
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99;  margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="input-group input-group-password align-items-center" id="sea">
                                <input autocomplete="one-time-code" class="form-control input-picker p-4 kodeProduksi" id="kodeProduksi" name="kodeProduksi" placeholder="Cari Kode Produksi" value="">
                                <div class="input-group-append" style="height:50px;">
                                    <button class="btn btn-success" type="button" onclick="excel('<?= base_url("/dashboard/list-wip/excel"); ?>')">
                                        <i class="fas fa-file-excel fa-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable2" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th onclick="changeSort('createdAt')">Production Date</th>
                                        <th onclick="changeSort('productionCode')">Production Code</th>
                                        <th onclick="changeSort('barangCode')"> Kode Barang</th>
                                        <th onclick="changeSort('barangName')">Nama Barang</th>
                                        <th>Hasil Produksi</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table" id="body-table" style="cursor: pointer;">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card-header card-dashboard-text text-bold" id="card-dashboard-text">
                    <b>DATA DOKUMEN BEA CUKAI</b>
                </div>

                <div class="card-body" style="margin-top: -20px; margin-bottom: -20px;">
                    <br>
                    <div class="input-group input-group-password align-items-center" id="dateBCPicker">
                        <input autocomplete="one-time-code" class="form-control input-picker p-4 dateBC" id="dateBC" name="dateBC" placeholder="Pilih Bulan" value="">
                        <div class="input-group-prepend group-prepend-password align-items-center">
                            <i style="cursor: pointer; z-index: 99;  margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body border" style="margin-top: 20px">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Rekap BC</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Outstanding BC</button>
                        </li>

                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                            <div class="row">
                                <ul class="nav flex-column col-6">
                                    <li class="nav-item">
                                        <a href="#" onclick="showDetails('bc23')" class="nav-link text-info text-sm ">
                                            <b>BC 2.3 <span id="bc23" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" onclick="showDetails('bc25')" class="nav-link text-info text-sm">
                                            <b>BC 2.5 <span id="bc25" class="float-right">0</span></b>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#" onclick="showDetails('bc27')" class="nav-link text-info text-sm">
                                            <b>BC 2.7 <span id="bc27" class="float-right">0</span></b>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="#" onclick="showDetails('bc30')" class="nav-link text-info text-sm">
                                            <b>BC 3.0 <span id="bc30" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" onclick="showDetails('bc40')" class="nav-link text-info text-sm">
                                            <b>BC 4.0 <span id="bc40" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                </ul>

                                <ul class="nav flex-column col-6">
                                    <li class="nav-item">
                                        <a href="#" onclick="showDetails('bc41')" class="nav-link text-info text-sm">
                                            <b>BC 4.1 <span id="bc41" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" onclick="showDetails('ppbkb')" class="nav-link text-info text-sm">
                                            <b>PPBKB <span id="ppbkb" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                            <div class="row">
                                <ul class="nav flex-column col-6">
                                    <li class="nav-item">
                                        <a href="<?= base_url("bea-cukai-bc-23/bc-23-outstanding");  ?>" class="nav-link text-info text-sm ">
                                            <b>BC 2.3 <span id="bc-23-outstanding" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= base_url("bea-cukai-bc-25/bc-25-outstanding");  ?>" class="nav-link text-info text-sm">
                                            <b>BC 2.5 <span id="bc-25-outstanding" class="float-right">0</span></b>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="<?= base_url("bea-cukai-bc-27/bc-27-outstanding");  ?>" class="nav-link text-info text-sm">
                                            <b>BC 2.7 <span id="bc-27-outstanding" class="float-right">0</span></b>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="<?= base_url("bea-cukai-bc-30/bc-30-outstanding");  ?>" class="nav-link text-info text-sm">
                                            <b>BC 3.0 <span id="bc-30-outstanding" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= base_url("bea-cukai-bc-40/bc-40-outstanding");  ?>" class="nav-link text-info text-sm">
                                            <b>BC 4.0 <span id="bc-40-outstanding" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                </ul>

                                <ul class="nav flex-column col-6">
                                    <li class="nav-item">
                                        <a href="<?= base_url("bea-cukai-bc-41/bc-41-outstanding");  ?>" class="nav-link text-info text-sm">
                                            <b>BC 4.1 <span id="bc-41-outstanding" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= base_url('bea-cukai-ppbkb/ppbkb-outstanding') ?>" class="nav-link text-info text-sm">
                                            <b>PPBKB <span id="ppbkb-outstanding" class="float-right">0</span></b>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div> -->

    </div>



</section>
<script>
    var csrfToken = '<?= csrf_token() ?>';
</script>
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.style.zoom = "70%";
    });

    let sort = "production_results.createdAt";
    let sortType = "desc";

    const table = $('#dataTable2').DataTable({

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
            url: "<?= base_url("dashboard/list-wip"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.kodeProduksi = $(".kodeProduksi").val();
                data.month = $(".bulanProduksi").val();
                data.sort = sort;
                data.sortType = sortType;
            }
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
                data: "productionDate",
                className: "text-center",

            },
            {
                data: "productionCode",
                className: "text-center",
            },
            {
                data: "barangCode",
                className: "text-center",
            },
            {
                data: "barangName",
                className: "text-center",
            },

            {
                data: "hasilProduksi",
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

    $('.kodeProduksi').keyup(function() {
        table.ajax.reload();
    });
    $('.bulanProduksi').change(function() {
        table.ajax.reload();
    })

    $(document).ready(function() {
        var currentDate = new Date();
        var formattedDate = (currentDate.getMonth() + 1).toString().padStart(2, '0') + '/' + currentDate.getFullYear();
        $("#dateBC").val(formattedDate);
        $('#bulanProduksi').val(formattedDate);
        $('#profile-tab').click(function() {
            $('#dateBCPicker').hide();
        });
        $('#home-tab').click(function() {
            $('#dateBCPicker').show();
        });

        dataBC();
        var BCarr = ['bc-23', 'bc-25', 'bc-27', 'bc-30', 'bc-40', 'bc-41', 'ppbkb'];
        BCarr.forEach(function(bc) {
            $.ajax({
                url: "<?= base_url(); ?>bea-cukai-" + bc + "/" + bc + "-outstanding-all",
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $('#' + bc + '-outstanding').text(res.length);
                },
            });
        });
    })

    $(".dateStart").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        minViewMode: "months"
    });
    $("#dateBC,#bulanProduksi").datepicker({
        todayHighlight: true,
        format: "mm/yyyy",
        orientation: "bottom auto",
        autoclose: true,
        minViewMode: "months"
    });

    $('#dateBC').change(function() {
        console.log($("#dateBC").val());
        dataBC();
    });

    function dataBC() {

        var formData = new FormData();
        var date_BC = $("#dateBC").val();

        $.ajax({

            url: "<?= base_url('/dashboard/get-number-bc'); ?>",
            data: {
                dateBC: date_BC
            },
            method: "GET",
            dataType: "json",
            success: function(res) {
                $("#bc23").text(res.bc23);
                $("#bc25").text(res.bc25);
                $("#bc27").text(res.bc27);
                $("#bc30").text(res.bc30);
                $("#bc40").text(res.bc40);
                $("#bc41").text(res.bc41);
                $("#ppbkb").text(res.ppbkb);

            },
        });
    }


    function showDetails(bc) {
        var bcName = bc;
        // var date_BC = $("#dateBC").val();
        // var formattedBCDate = date_BC.replace('/', '-');

        location.href = "<?= base_url('dashboard/list-dokumen-') ?>" + bc;

    }

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    function excel(url) {
        let month = $(".bulanProduksi").val();
        let kodeProduksi = $(".kodeProduksi").val();
        window.open(url + `?month=${month}&kodeProduksi=${kodeProduksi}&sort=${sort}&sortType=${sortType}`, "_blank");
    }
</script> -->


<?= $this->endSection(); ?>