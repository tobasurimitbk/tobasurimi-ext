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
            BC 2.7 - PEMBERITAHUAN PENGELUARAN UNTUK DIANGKUT DARI TEMPAT PENIMBUNAN BERIKAT KE TEMPAT PENIMBUNAN BERIKAT LAINNYA
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <form id="form-transaksi">
                <div class="row mt-1">
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <select class="form-select harga_kode_valuta" id="harga_kode_valuta" name="harga_kode_valuta" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeValuta as $k) : ?>
                                        <option <?= $payload->kodeValuta == $k['value'] ? 'selected' : '' ?> data-id_encrypt="<?= encrypt($k['value']) ?>" value="<?= $k['value'] ?>">
                                            <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Valuta</label>
                            </div>
                        </div>

                        <a href="#" id="btn-sesuai-valuta-terbaru" class="btn btn-warning" style="float: right;">
                            Sesuai Valuta Terbaru
                        </a>

                        <button class="btn btn-warning" type="button" disabled id="btn-sesuai-valuta-terbaru-loading" style="float: right;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading
                        </button>

                        <br><br>

                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_ndpbm" value="<?= $payload->ndpbm == "" ? "0" : formatRupiah($payload->ndpbm) ?>" name="harga_ndpbm" type="text" class="form-control harga_ndpbm" onchange="this.value = formatRupiah(this.value)">
                                <label>NDPBM</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga_cif" name="harga_cif" value="<?= $payload->cif  == "" ? "0" : formatRupiah($payload->cif)  ?>" type="text" class="harga_cif form-control" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Cif</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_nilai_pabean" readonly value="<?= $payload->hargaPenyerahan  == "" ? "0" : formatRupiah($payload->hargaPenyerahan * $payload->ndpbm)  ?>" name="harga_nilai_pabean" type="text" class="form-control harga_nilai_pabean">
                                <label>Nilai Pabean</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly id="harga_nilai_penyerahan" value="<?= $payload->hargaPenyerahan == "" ? "0" : formatRupiah($payload->hargaPenyerahan) ?>" name="harga_nilai_penyerahan" type="text" class="harga_nilai_penyerahan form-control" onchange="this.value = formatRupiah(this.value)">
                                <label>Harga Penyerahan/Harga Jual/Harga Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga Lainnya
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="nilai_jasa" value=" <?= $payload->nilaiJasa  == "" ? "0" : formatRupiah($payload->nilaiJasa)  ?>" name="nilai_jasa" type="text" class="form-control nilai_jasa" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Jasa</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="nilai_muka" value=" <?= $payload->uangMuka  == "" ? "0" : formatRupiah($payload->uangMuka)  ?>" name="nilai_muka" type="text" class="form-control nilai_muka" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Muka</label>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Berat
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="volume" value="" name="volume" type="text" class="form-control volume" onchange="this.value = formatRupiah(this.value)">
                                <label>Volume(M3)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input readonly id="berat_netto" value="<?= $payload->netto == 0 ? "0" : $payload->netto ?>" name="berat_netto" type="text" class="form-control berat_netto">
                                <label>Berat Bersih/Netto (KGM)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="berat_bruto" name="berat_bruto" value="<?= $payload->bruto == "" ? "0" : $payload->bruto ?>" type="text" class="form-control berat_bruto">
                                <label>Berat Kotor/Bruto (KGM)</label>
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

    $('#btn-sesuai-valuta-terbaru-loading').hide();

    $('#btn-sesuai-valuta-terbaru').click(function(e) {
        e.preventDefault();
        if ($('#harga_kode_valuta').val()) {
            $.ajax({
                url: `<?= base_url("bea-cukai-bc-23/api/valuta"); ?>`,
                method: "GET",
                data: {
                    harga_kode_valuta: $('#harga_kode_valuta option:selected').data('id_encrypt')
                },
                beforeSend: function() {
                    $('#btn-sesuai-valuta-terbaru-loading').show();
                    $('#btn-sesuai-valuta-terbaru').hide();
                },
                complete: function() {
                    $('#btn-sesuai-valuta-terbaru').show();
                    $('#btn-sesuai-valuta-terbaru-loading').hide();
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        csrf.val(res.token);
                        if (res.data.status) {
                            $("#harga_ndpbm").val((formatRupiah(res.data.data)));
                            $("#harga_cif").val((formatRupiah(res.data.data)));
                        }
                    }

                    if (res.data.status === false) {
                        Swal.fire({
                            icon: 'error',
                            title: res.data.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: "Pilih valuta dahulu",
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        }
    });

    $('#harga_ndpbm').keyup(function() {
        var ndpbm = convertRupiahToNumber($(this).val()) || 0;
        var hargaBarang = convertRupiahToNumber($("#harga_nilai_penyerahan").val()) || 0;
        var cif = convertRupiahToNumber($("#harga_cif").val()) || 0;
        var hargaPabean = (Number(ndpbm) * Number(cif));
        $('#harga_cif').val(formatRupiah(ndpbm));
        $('#harga_nilai_pabean').val(formatRupiah(hargaPabean));
    });


    $('#harga_nilai_penyerahan').keyup(function() {
        var ndpbm = convertRupiahToNumber($("#harga_ndpbm").val()) || 0;
        var hargaBarang = convertRupiahToNumber($(this).val()) || 0;
        var hargaPabean = (Number(ndpbm) * Number(hargaBarang));

        $('#harga_nilai_pabean').val(formatRupiah(hargaPabean))
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
            harga_kode_valuta: {
                required: true
            },
            harga_ndpbm: {
                required: true
            },
            harga_cif: {
                required: true
            },
            harga_nilai_pabean: {
                required: true
            },
            harga_nilai_penyerahan: {
                required: true
            },
            nilai_jasa: {
                required: true
            },
            nilai_muka: {
                required: true
            },
            pajak_ppn_pajak: {
                required: true
            },
            pajak_tarif_ppn_pajak: {
                required: true
            },
            pajak_ppnbm_pajak: {
                required: true
            },
            pajak_tarif_ppnbm_pajak: {
                required: true
            },
            berat_bruto: {
                required: true
            },
            berat_netto: {
                required: true
            },
        },
        messages: {
            harga_kode_valuta: {
                required: "Pilih kode valuta"
            },
            harga_ndpbm: {
                required: "NDPBM wajib diisi"
            },
            harga_cif: {
                required: "CIF wajib diisi"
            },
            harga_nilai_pabean: {
                required: true
            },
            harga_nilai_penyerahan: {
                required: true
            },
            nilai_jasa: {
                required: true
            },
            nilai_muka: {
                required: true
            },
            pajak_ppn_pajak: {
                required: "PPN wajib diisi"
            },
            pajak_tarif_ppn_pajak: {
                required: true
            },
            pajak_ppnbm_pajak: {
                required: "PPnBM wajib diisi"
            },
            pajak_tarif_ppnbm_pajak: {
                required: true
            },
            berat_bruto: {
                required: "Bruto wajib diisi"
            },
            berat_netto: {
                required: true
            },
            volume: {
                required: true
            }
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
                    formData.append("id", "<?= encrypt($bc27['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-27/id/transaksi"); ?>",
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