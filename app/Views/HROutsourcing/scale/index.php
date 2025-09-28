<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<style>
    #qrModal .modal-body {
        background: #fff !important;
        z-index: 1055 !important;
        position: relative;
    }

    #qrResult img {
        width: 100% !important;
        max-width: 350px; /* biar ga terlalu gede */
        height: auto;
        cursor: pointer;
    }


</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Generate QR CODE Spesifikasi</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-add-spp row justify-content-start mb-3">
                    <?= csrf_field() ?>
                    <!-- MASTER BARANG -->
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select barang_id" id="barang_id" name="barang_id">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Barang</label>
                        </div>
                    </div>

                    <!-- SPESIFIKASI BARANG -->
                    <div class="col-md-4">
                        <div class="form-spp form-floating mb-3">
                                <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id">
                                </select>
                                <label for="spesifikasi_id">Pilih Spesifikasi Barang</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <button type="button" id="btn-generate" class="btn btn-primary">
                            Generate QR Code
                        </button>
                    </div>
            </div>
        </div>
    </div>
</section>

 <div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="qrResult">
                <!-- QR Code muncul disini -->
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-print" class="btn btn-success">Print</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>

<script>

    let abortController = null;
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $("#barang_id").select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        minimumInputLength: 3,
        width: '100%',
        ajax: {
            delay: 300,
            transport: function(params, success, failure) {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch("<?= base_url('jasa-vendor-out-kepiting-kukus/search-master-barang'); ?>?" + new URLSearchParams({
                    q: params.data.term
                }), {
                    signal: abortController.signal
                })
                .then(res => res.json())
                .then(success)
                .catch(err => {
                    if (err.name !== "AbortError") failure(err);
                });
            },
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.id,
                        text: `${item.master_barang}`
                    }))
                };
            }
        }
    });

    $("#spesifikasi_id").select2({
        placeholder: "Pilih Spesifikasi Barang",
        theme: "bootstrap-5",
        ajax: {
            delay: 300,
            transport: function(params, success, failure) {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                fetch("<?= base_url('jasa-vendor-out-kepiting-kukus/search-barang'); ?>?" + new URLSearchParams({
                    barang_id: $('#barang_id option:selected').val(),
                    q: params.data.term
                }), {
                    signal: abortController.signal
                })
                .then(res => res.json())
                .then(success)
                .catch(err => {
                    if (err.name !== "AbortError") failure(err);
                });
            },
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.spesifikasi_id,
                        text: `${item.master_barang} - ${item.spesifikasi}`,
                        master_barang: item.master_barang,
                        spesifikasi: item.spesifikasi,
                        satuan: item.kode_satuan,
                    }))
                };
            }
        }
    })

    $("#btn-generate").on("click", function() {
        const csrf = $(`[name="${csrfToken}"]`);
        console.log(csrf)
        let spesifikasi_id = $("#spesifikasi_id option:selected").val();

        $.ajax({
            url: "<?= base_url('/hr-outsourcing-scale'); ?>",
            method: "POST",
            data: { spesifikasi_id: spesifikasi_id },
             beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
            complete: function() {
                                        stopLoading()
                                    },
            success: function(res) {
                if (res.status === "ok") {
                    $("#qrResult").html(res.html);
                    $("#qrModal").modal("show");
                } else {
                    alert("Gagal generate QR Code");
                }
            }
        });
    });

    // Print isi modal body
    $("#btn-print").on("click", function() {
        let printContents = document.getElementById("qrResult").innerHTML;
        let w = window.open();
        w.document.write(printContents);
        w.document.close();
        w.print();
    });
</script>


<?= $this->endSection(); ?>