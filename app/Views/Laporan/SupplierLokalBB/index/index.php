<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Laporan Pembelian Supplier Bahan Baku</h1>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('laporan-supplier-lokal-bb/kwitansi-tb') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Kwitansi TB</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('laporan-supplier-lokal-bb/pendapatan-supplier') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pendapatan Supplier</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('laporan-supplier-lokal-bb/rekap-all-supplier') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Rekap All Supplier</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('laporan-supplier-lokal-bb/rekap-all-barang') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Rekap All Barang (Summary)</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('laporan-supplier-lokal-bb/rincian-perbarang') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Rincan Per Barang</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div> -->

    </div>
    <div class="row">
        <!-- <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('laporan-supplier-lokal-bb/rekap-persupplier') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Rekap Per Supplier</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div> -->

        <!-- <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('laporan-supplier-lokal-bb/rekap-perbarang') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Rekap Per Barang</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div> -->

    </div>
</section>


<?= $this->endSection(); ?>