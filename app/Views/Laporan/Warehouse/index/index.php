<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Laporan Warehouse</h1>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('/laporan-warehouse/sales-order') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Laporan Sales Order</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('/laporan-warehouse/sales-order-ekspor') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Laporan Sales Order Ekspor</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('/laporan-warehouse/purchase-order') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Laporan Purchase Order</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('/laporan-warehouse/penerimaan-barang') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Laporan Penerimaan Barang</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>


    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('/laporan-warehouse/sales-order') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Laporan Material Request </h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('/laporan-warehouse/sales-order') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Laporan Hasil Produksi </h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

</section>


<?= $this->endSection(); ?>