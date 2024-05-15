<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Setting Akun Bea Cukai</h1>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('setting-akun-bc/akun') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Integrasi Ceisa</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="<?= base_url('setting-akun-bc/pengusaha-tpb') ?>">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="far fa-file"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pegusaha TPB</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

</section>


<?= $this->endSection(); ?>