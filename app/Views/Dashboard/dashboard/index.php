<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">

    <div class="section-header">
        <h1>Dashboard</h1>
    </div>
    <div class="card">
        <div class="card-header text-black text-bold">
            <b>MENU CEPAT</b>
        </div>
        <div class="card-body">
            <div class="row" style="margin-top: -20px;">

                <div class="col-sm-1 mr-4 ml-4 mt-3">
                    <div class="btn-group dropend">
                        <button class="btn dropdown-toggle p-3" style="background-color: #E02B35; color: white; width:160px; " type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <button class="btn dropdown-toggle p-3" style="background-color: #E02B35; color: white; width:160px; " type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <div class="btn-group">
                            <a href="<?= base_url("laporan-warehouse"); ?>" class="btn p-3" style="background-color: #E02B35; color: white; width:160px">
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
                        <div class="btn-group">
                            <a href="<?= base_url("work-order"); ?>" class="btn p-3" style="background-color: #E02B35; color: white; width:160px">
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
                        <button class="btn dropdown-toggle p-3" style="background-color: #E02B35; color: white; width:160px; " type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <div class="btn-group">
                            <a href="<?= base_url("kurs"); ?>" class="btn p-3" style="background-color: #E02B35; color: white; width:160px">
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
                        <div class="btn-group">
                            <a href="<?= base_url("user");  ?>" class="btn p-3" style="background-color: #E02B35; color: white; width:160px">
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
                        <button class="btn dropdown-toggle p-3" style="background-color: #E02B35; color: white; width:160px; " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-barcode"></i> Products
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                99
                            </span>
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
                                <li><a class="dropdown-item" href="<?= base_url("kemasan");  ?>">KEMANASAN</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <?php if (can('Master Data', 'Customer Global', 'r')) : ?>
                    <div class="col-sm-1 mr-4 ml-4 mt-3">
                        <div class="btn-group">
                            <a href="<?= base_url("customer");  ?>" class="btn p-3" style="background-color: #E02B35; color: white; width:160px">
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
    </div>




    <!-- <div class="col-12" style="overflow-x: auto; white-space: nowrap; padding: 10px;">
            <a href="http://202.162.198.46:7089/rak/tf" class="btn bg-orange d-lg-none" target="_blank">
                <i class="fa fa-share"></i> Transfer Antar Rak
            </a>
            <a href="http://202.162.198.46:7089/pdf_pending_dokumen" class="btn bg-navy" target="_blank">
                <span class="badge bg-info">4</span>
                <i class="fas fa-file"></i> Pending Doc.
            </a>

            <a href="http://202.162.198.46:7089/tp/purchase" class="btn bg-secondary">
                <span class="badge bg-teal">271</span>
                <i class="fas fa-store"></i> Purchase
            </a>
            <a href="http://202.162.198.46:7089/ts/sales" class="btn bg-teal">
                <span class="badge bg-purple">15</span>
                <i class="fas fa-barcode"></i> Sales
            </a>
            <a href="http://202.162.198.46:7089/internal_report/inventory_report/request" class="btn bg-success">
                <span class="badge bg-danger">50</span>
                <i class="fas fa-truck"></i> Material Req.
            </a>
            <a href="http://202.162.198.46:7089/ppic/work-order" class="btn bg-info">
                <span class="badge bg-warning">226</span>
                <i class="fas fa-play"></i> Production
            </a>
            <a href="http://202.162.198.46:7089/internal_report/inventory_report/surat-jalan" class="btn bg-dark">
                <span class="badge bg-success">0</span>
                <i class="fas fa-truck"></i> Delivery
            </a>
            <a href="http://202.162.198.46:7089/setting/kurs" class="btn ">
                <span class="badge  bg-teal "> 16,412 </span>
                <i class="fas fa-bar-chart"><b>$</b></i> Kurs
            </a>
            <a href="http://202.162.198.46:7089/setting/periode" class="btn ">
                <span class="badge  bg-teal "> PR-2024-01 </span>
                <i class="fas fa-calendar-alt"></i> Periode
            </a>
            <a href="http://202.162.198.46:7089/setting/user" class="btn">
                <span class="badge bg-info">42</span>
                <i class="fas fa-users"></i> Users
            </a>
            <a href="/warehouse/barang" class="btn">
                <span class="badge bg-success">1,121</span>
                <i class="fas fa-barcode"></i> Products
            </a>
            <a href="http://202.162.198.46:7089/ts/buyer" class="btn">
                <span class="badge bg-purple">111</span>
                <i class="fas fa-address-card"></i> Customer
            </a>
            <a href="http://202.162.198.46:7089/tp/supplier" class="btn">
                <span class="badge bg-warning">1,284</span>
                <i class="fas fa-address-book"></i> Supplier
            </a>

            <a href="http://202.162.198.46:7089/alur-proses" class="btn">
                <span class="badge bg-danger"></span>
                <i class="fas fa-info"></i> Info
            </a>
        </div> -->


    <!-- <div class="row mt-4">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-3">
                                <span class="m-0" style="vertical-align: middle; font-size: 25px; "><i class="fas fa-th text-sm mr-2" style="font-size: 25px;"></i> Work In Progress </span>
                            </div>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control p-4" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-light">
                                                    <i class=" far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control p-4" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <span class="input-group-text bg-light">
                                                    <i class="fas fa-search "></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-lg bg-warning dropdown-toggle float-right ml-2 p-3" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-download mr-1"></i> Export
                                        </button>
                                        <ul class="dropdown-menu text-xs" style="">
                                            <li class="dropdown-item" id="excel" onclick="actionExport('Excel')">Excel</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pr-2 pb-0 pl-2 pt-2 row">
                        <div class="col-sm-12 table-responsive">
                            <div id="mytable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0" style="border-color: #f7f6f5;">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th style="text-align: center;">Production Date </th>
                                                        <th style="text-align: center;">Production Code</th>
                                                        <th style="text-align: center;">Qty Order</th>
                                                        <th style="text-align: center;">Finish Good</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body-table" style="text-align: center;">
                                                    <tr style="color: whitesmoke;">
                                                        <td colspan="4">No data</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-md-3">
                <div class="card">

                    <div class="card-header bg-light">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend d-flex" style="align-items: center;">
                                <span class="input-group-text bg-light border-0">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                                <input type="month" class="form-control text-xs border-0" id="s_date">
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-1 m-0 row">
                        <ul class="nav flex-column col-6">
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('in', 'BC 2.3')" class="nav-link text-info text-sm ">
                                    BC 2.3 <span id="bc23" class="float-right">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('in', 'BC 2.3 PJT')" class="nav-link text-info text-sm">
                                    BC 2.3 PJT <span id="bc23pjt" class="float-right">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('in', 'BC 2.6.2')" class="nav-link text-info text-sm">
                                    BC 2.6.2 <span id="bc262" class="float-right">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('in', 'BC 2.7')" class="nav-link text-info text-sm">
                                    BC 2.7 <span id="bc272" class="float-right">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('in', 'BC 4.0')" class="nav-link text-info text-sm">
                                    BC 4.0 <span id="bc40" class="float-right">151</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav flex-column col-6">
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('out', 'BC 2.5')" class="nav-link text-info text-sm">
                                    BC 2.5 <span id="bc25" class="float-right">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('out', 'BC 2.6.1')" class="nav-link text-info text-sm">
                                    BC 2.6.1 <span id="bc261" class="float-right">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('out', 'BC 2.7')" class="nav-link text-info text-sm">
                                    BC 2.7 <span id="bc271" class="float-right">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('out', 'BC 3.0')" class="nav-link text-info text-sm">
                                    BC 3.0 <span id="bc30" class="float-right">3</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('out', 'BC 3.3')" class="nav-link text-info text-sm">
                                    BC 3.3 <span id="bc33" class="float-right">0</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#" onclick="prev_doc('out', 'BC 4.1')" class="nav-link text-info text-sm">
                                    BC 4.1 <span id="bc41" class="float-right">1</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

        </div> -->
    <!-- <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-user"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Admin</h4>
                        </div>
                        <div class="card-body">
                            10
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-newspaper"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>News</h4>
                        </div>
                        <div class="card-body">
                            42
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Reports</h4>
                        </div>
                        <div class="card-body">
                            1,201
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Online Users</h4>
                        </div>
                        <div class="card-body">
                            47
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
</section>


<?= $this->endSection(); ?>