<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <div class="section-header">
        <h1 class="title-name">Dokumen BC 2.3</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                Simpan
            </button>
        </div>
    </div>

    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <?= csrf_field() ?>
                <div class="row mt-1">
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga
                        </label>
                        <div class="mt-1">

                        </div>

                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga Lainnya
                        </label>

                    </div>
                </div>
            </div>
        </div>

</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);


    $('#harga_lainnya_kode_asuransi').select2({
        placeholder: "Pilih Asuransi",
        theme: "bootstrap-5",
    });

    $('#harga_kode_harga_barang').select2({
        placeholder: "Pilih Kode Harga",
        theme: "bootstrap-5",
    });

    $('#kontainer_jenis').select2({
        placeholder: "Pilih Jenis Peti Kemas",
        theme: "bootstrap-5",
    });

    $('#kontainer_tipe').select2({
        placeholder: "Pilih Tipe Peti Kemas",
        theme: "bootstrap-5",
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
</script>


<?= $this->endSection(); ?>