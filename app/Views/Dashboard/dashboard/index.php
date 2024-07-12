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
            <div class="row justify-content-center" style="margin-top: -20px;">

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
        <div class="row">
            <div class="col-sm-8">
                <div class="card-header text-black text-bold">
                    <b>POSISI BARANG WORK IN PROGRESS (WIP)</b>
                </div>
                <div class="card-body">

                </div>
            </div>
            <div class="col-sm-4">
                <div class="card-header text-black text-bold">
                    <b>DATA DOKUMEN BEA CUKAI</b>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>

    </div>



</section>


<?= $this->endSection(); ?>