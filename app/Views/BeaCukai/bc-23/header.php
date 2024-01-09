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
            <?php
            $isFinished = session()->getFlashdata('isCompleteFormHeader') && session()->getFlashdata('isCompleteFormEntitas') && session()->getFlashdata('isCompleteFormDokumen') && session()->getFlashdata('isCompleteFormPengangkut') && session()->getFlashdata('isCompleteFormPetiKemas') && session()->getFlashdata('isCompleteFormTransaksi') && session()->getFlashdata('isCompleteFormBarang') && session()->getFlashdata('isCompleteForPungutan')  && session()->getFlashdata('isCompleteFormPernyataan');
            ?>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view" <?= ($isFinished == true) ? '' : 'disabled' ?>>
                Kirim ke Ceisa 4.0 <?= $isFinished; ?>
            </button>
        </div>
    <?php endif; ?>
</div>