<div class="section-header">
    <h1 class="title-name">Dokumen BC 2.3</h1>
    <?php if (request()->uri->getSegment(5) == null) : ?>
        <?php
        $isFinished = session()->getFlashdata('isCompleteFormHeader') && session()->getFlashdata('isCompleteFormEntitas') && session()->getFlashdata('isCompleteFormDokumen') && session()->getFlashdata('isCompleteFormPengangkut') && session()->getFlashdata('isCompleteFormPetiKemas') && session()->getFlashdata('isCompleteFormTransaksi') && session()->getFlashdata('isCompleteFormBarang') && session()->getFlashdata('isCompleteFormPernyataan');
        $bc23Model = new \App\Models\BC23Model();
        $bc23 = $bc23Model->get(decrypt(request()->uri->getSegment(4)));
        ?>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-23/po/" . encrypt($bcPo['id'])); ?>">
                Batal
            </a>
            <?php if ($bc23 != null) : ?>
                <button <?= $bc23['status_dokumen'] == 'Sudah Kirim' ? 'disabled' : '' ?> class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view" <?= ($isFinished == true) ? '' : 'disabled' ?> onclick="submitDokumen()">
                    Kirim ke Ceisa 4.0
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<script>
    function submitDokumen() {
        Swal.fire({
            icon: 'question',
            title: 'Posting BC 2.3 ke aplikasi Ceisa Bea Cukai ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('bea-cukai-bc-23/api/kirim-dokumen/' . request()->uri->getSegment(4)) ?>`,
                    method: "GET",
                    beforeSend: function(xhr) {
                        setLoading();
                    },
                    complete: function() {
                        stopLoading();
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                location.replace("<?= base_url('bea-cukai-bc-23') ?>")
                            });
                        }
                    }
                })
            }
        })
    }
</script>