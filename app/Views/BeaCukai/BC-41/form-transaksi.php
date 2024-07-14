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
        <div class="card-header" style="font-weight: bold; color:black;">
            BC 4.1 - PEMBERITAHUAN PENGELUARAN KEMBALI BARANG ASAL TEMPAT LAIN DALAM DAERAH PABEAN DARI TEMPAT PENIMBUNAN BERIKAT
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
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly id="hargaPenyerahan" value="<?= $payload->hargaPenyerahan == "" ? "0" : formatRupiah($payload->hargaPenyerahan) ?>" name="hargaPenyerahan" type="text" class="hargaPenyerahan form-control" onchange="this.value = formatRupiah(this.value)">
                                <label>Harga Penyerahan</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="nilaiJasa" value="<?= formatRupiah($nilaiJasaTotal) ?>" name="nilaiJasa" type="text" class="form-control nilaiJasa" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Jasa</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="uangMuka" value="<?= formatRupiah($payload->uangMuka) ?>" name="uangMuka" type="text" class="form-control uangMuka" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Uang Muka</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="hargaPerolehan" value="<?= formatRupiah($hargaPerolehanTotal) ?>" name="hargaPerolehan" type="text" class="form-control hargaPerolehan" onchange="this.value = formatRupiah(this.value)">
                                <label>Harga Perolehan</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Berat
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="volume" value="<?= $payload->volume == "" ? "0" : formatRupiah($payload->volume) ?>" name="volume" type="text" class="form-control volume">
                                <label>Volume (M3)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="bruto" name="bruto" value="<?= $payload->bruto == "" ? "0" : formatRupiah($payload->bruto) ?>" type="text" class="form-control bruto" onchange="this.value = formatRupiah(this.value)">
                                <label>Berat Kotor/Bruto (KGM)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="netto" value="<?= $payload->netto == "" ? "0" : formatRupiah($payload->netto) ?>" name="netto" type="text" class="form-control netto">
                                <label>Berat Bersih/Netto (KGM)</label>
                            </div>
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
    $('#harga_kode_valuta').select2({
        placeholder: "Pilih Valuta",
        theme: "bootstrap-5",
    });


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var validatorTransaksi = $("#form-transaksi").validate({
        rules: {
            hargaPenyerahan: {
                required: true
            },
            nilaiJasa: {
                required: true
            },
            uangMuka: {
                required: true
            },
            hargaPerolehan: {
                required: true
            },
            volume: {
                required: true
            },
            bruto: {
                required: true
            },
            netto: {
                required: true
            },
        },
        messages: {
            hargaPenyerahan: {
                required: "Harga penyerahan wajib diisi"
            },
            nilaiJasa: {
                required: "Nilai jasa wajib diisi"
            },
            uangMuka: {
                required: "Uang muka wajib diisi"
            },
            hargaPerolehan: {
                required: "Harga perloehan wajib diisi"
            },
            volume: {
                required: "Volume wajib diisi"
            },
            bruto: {
                required: "Bruto wajib diisi"
            },
            netto: {
                required: "Netto wajib diisi"
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
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-transaksi"));
                    formData.append("id", "<?= encrypt($bc41['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-41/id/transaksi"); ?>",
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
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    confirmButtonText: 'Ok'
                                })
                            }
                        },
                    });
                }
            })
        }
    });

    function formatRupiah(angka) {
        var formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });
        var parsedNumber = parseFloat(angka);
        if (isNaN(parsedNumber)) {
            return "0";
        }
        return formatter.format(parsedNumber).replace('Rp', '').trim();
    }

    function convertRupiahToNumber(rupiah) {
        var withoutDot = rupiah.replace(/\./g, '');
        var numberWithDot = withoutDot.replace(',', '.');
        return parseFloat(numberWithDot);
    }
</script>


<?= $this->endSection(); ?>