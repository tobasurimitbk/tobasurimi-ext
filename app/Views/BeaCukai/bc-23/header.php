<div class="section-header">
    <h1 class="title-name">Dokumen BC 2.3</h1>
    <?php if (request()->uri->getSegment(5) == null) : ?>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>
            <button class="btn btn-warning btn-print float-right text-white root-form-view" disabled>
                Print
            </button>
            <button class="btn btn-show-form btn-success float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                Validasi
            </button>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view" disabled>
                Kirim ke Ceisa 4.0
            </button>
        </div>
    <?php endif; ?>
</div>