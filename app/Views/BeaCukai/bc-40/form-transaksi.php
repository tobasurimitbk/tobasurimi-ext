<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="card">
        <div class="card-header" style="font-weight: bold;">
            BC 4.0 - PEMBERITAHUAN PEMASUKAN BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN KE TEMPAT PENIMBUNAN BERIKAT
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <form id="form-transaksi">
                <div class="row mt-1">
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga
                        </label>
                        <div class="mt-1">
                            <div class="form-floating">
                                <input id="harga_penyerahan" readonly value="<?= $bc40 == null ? "0" : ($bc40['harga_penyerahan'] == null ? "0" : number_format($bc40['harga_penyerahan'], 2)) ?>" name="harga_penyerahan" type="text" class="form-control harga_penyerahan" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label>Harga Penyerahan</label>
                            </div>
                            <span class="text-small mb-3"><i>Harga Penyerahan akan terakumulasi secara otomatis setelah mengisi data barang</i></span>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="nilai_jasa" value="<?= $bc40 == null ? "0" : ($bc40['nilai_jasa'] == null ? "0" : number_format($bc40['nilai_jasa'], 2)) ?>" name="nilai_jasa" type="text" class="form-control nilai_jasa" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label>Nilai Jasa</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="nilai_uang_muka" value="<?= $bc40 == null ? "0" : ($bc40['uang_muka'] == null ? "0" : number_format($bc40['uang_muka'], 2)) ?>" name="nilai_uang_muka" type="text" class="form-control nilai_uang_muka" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label>Nilai Uang Muka</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_perolehan" value="<?= $bc40 == null ? "0" : ($bc40['harga_perolehan'] == null ? "0" : number_format($bc40['harga_perolehan'], 2)) ?>" name="harga_perolehan" type="text" class="form-control harga_perolehan" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label>Harga Perolehan</label>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga Lainnya
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="volume" value="<?= $bc40 == null ? "0" : ($bc40['volume'] == null ? "0" : number_format($bc40['volume'], 2)) ?>" name="volume" type="text" class="form-control volume" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label>Volume (M3)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="berat_kotor" value="<?= $bc40 == null ? "0" : ($bc40['bruto'] == null ? "0" : number_format($bc40['bruto'], 2)) ?>" name="berat_kotor" type="text" class="form-control berat_kotor" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label>Berat Kotor (KGM)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating">
                                <input id="berat_bersih" readonly value="<?= $bc40 == null ? "0" : ($bc40['netto'] == null ? "0" : number_format($bc40['netto'], 2)) ?>" name="berat_bersih" type="text" class="form-control berat_bersih" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label>Berat Bersih (KGM)</label>
                            </div>
                            <span class="text-small"><i>Berat bersih nilainya akan diakumulasikan dari total berat bersih pada tab Barang</i></span>
                        </div>
                    </div>
                </div>
            </form>
            <a href="#" class="btn btn-primary mt-4" id="btn-simpan-perubahan" style="float: right;">
                Simpan Perubahan
            </a>
            <button class="btn btn-primary" type="button" disabled id="btn-loading" style="float: right;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading
            </button>
        </div>
    </div>

</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');


    var validatorTransaksi = $("#form-transaksi").validate({
        rules: {
            harga_penyerahan: {
                required: true
            },
            nilai_jasa: {
                required: true
            },
            nilai_uang_muka: {
                required: true
            },
            harga_perolehan: {
                required: true
            },
            volume: {
                required: true
            },
            berat_kotor: {
                required: true,
            },
            berat_bersih: {
                required: true
            },
        },
        messages: {
            harga_penyerahan: {
                required: "Harga penyerahan wajib diisi"
            },
            nilai_jasa: {
                required: "Nilai jasa wajib diisi"
            },
            nilai_uang_muka: {
                required: "Uang muka wajib diisi"
            },
            harga_perolehan: {
                required: "Harga perolehan wajib diisi"
            },
            volume: {
                required: "Volume wajib diisi"
            },
            berat_kotor: {
                required: "Berat kotor wajib diisi",
            },
            berat_bersih: {
                required: "Berat bersih wajib diisi"
            },
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });


    // init loading
    $('#btn-loading').hide();

    $('#btn-simpan-perubahan').click(function() {
        if ($('#form-transaksi').valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Transaksi ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-transaksi"));
                    var hargaPenyerahan = destroyFormatRupiah($('#harga_penyerahan').val());
                    var nilaiJasa = destroyFormatRupiah($('#nilai_jasa').val());
                    var nilaiUangMuka = destroyFormatRupiah($('#nilai_uang_muka').val());
                    var hargaPerolehan = destroyFormatRupiah($('#harga_perolehan').val());
                    var volume = destroyFormatRupiah($('#volume').val());
                    var beratKotor = destroyFormatRupiah($('#berat_kotor').val());
                    var beratBersih = destroyFormatRupiah($('#berat_bersih').val());

                    formData.set('harga_penyerahan', hargaPenyerahan);
                    formData.set('nilai_jasa', nilaiJasa);
                    formData.set('nilai_uang_muka', nilaiUangMuka);
                    formData.set('harga_perolehan', hargaPerolehan);
                    formData.set('volume', volume);
                    formData.set('berat_kotor', beratKotor);
                    formData.set('berat_bersih', beratBersih);

                    formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-40/id/transaksi"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            $('#btn-loading').show();
                            $('#btn-simpan-perubahan').hide();
                        },
                        complete: function() {
                            $('#btn-loading').hide();
                            $('#btn-simpan-perubahan').show();
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    confirmButtonText: 'Ok'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            }
                        },
                    });
                }
            })
        }
    });
</script>


<?= $this->endSection(); ?>