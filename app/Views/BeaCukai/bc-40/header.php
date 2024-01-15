<div class="section-header">
    <h1 class="title-name">Dokumen BC 4.0</h1>
    <?php if (request()->uri->getSegment(5) == null) : ?>
        <?php
        $isFinished = session()->getFlashdata('isCompleteFormHeader') && session()->getFlashdata('isCompleteFormEntitas') && session()->getFlashdata('isCompleteFormDokumen') && session()->getFlashdata('isCompleteFormPengangkut') && session()->getFlashdata('isCompleteFormPetiKemas') && session()->getFlashdata('isCompleteFormTransaksi') && session()->getFlashdata('isCompleteFormBarang') && session()->getFlashdata('isCompleteFormPernyataan');
        $bc23Model = new \App\Models\BC23Model();
        $bc23 = $bc23Model->get(decrypt(request()->uri->getSegment(4)));
        ?>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-40"); ?>">
                Batal
            </a>
            <?php if ($bc23 != null) : ?>
                <button <?= $bc23['status_dokumen'] == 'Sudah Kirim' ? 'disabled' : '' ?> class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                    Hapus
                </button>
            <?php endif; ?>
            <button class="btn btn-warning btn-print float-right text-white root-form-view" disabled>
                Print
            </button>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view" <?= ($isFinished == true) ? '' : 'disabled' ?> onclick="submitDokumen()">
                Kirim ke Ceisa 4.0
            </button>
        </div>
    <?php endif; ?>
</div>
<script>
    function submitDokumen() {
        window.location.replace("<?= base_url('bea-cukai-bc-23/api/kirim-dokumen/' . request()->uri->getSegment(4)) ?>");
    }

    function deleteAction() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Dokumen BC 2.3 ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append("penerimaan_barang_id", "<?= request()->uri->getSegment(4) ?>");
                $.ajax({
                    url: `<?= base_url("bea-cukai-bc-23/id/delete"); ?>`,
                    method: "POST",
                    data: formData,
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        csrf.val(res.token);
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