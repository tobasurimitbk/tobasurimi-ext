<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Dokumen BC 2.3</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("bea-cukai-bc-23/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <div class="card">
        <div class="card-body">

        </div>
    </div>
</section>

<?= $this->endSection(); ?>