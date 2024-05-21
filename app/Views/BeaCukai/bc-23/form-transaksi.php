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
            BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
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
                                        <option <?= $bc23 != null ? ($bc23['kode_valuta'] == $k['value'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['value']) ?>">
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
                                <input id="harga_ndpbm" value="<?= $bc23 != null ? ($bc23['ndpbm'] != null ? $bc23['ndpbm'] : "0,00") : "0,00" ?>" name="harga_ndpbm" type="text" class="form-control harga_ndpbm" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>NDPBM</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select harga_kode_harga_barang" id="harga_kode_harga_barang" name="harga_kode_harga_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeIncoterm as $k) : ?>
                                        <option <?= $bc23 != null ? ($bc23['kode_incoterm'] == $k['value'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Harga Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga_nilai_barang" value="<?= $bc23 != null ? ($bc23['nilai_barang'] != null ? $bc23['nilai_barang'] : "0,00") : '0,00' ?>" name="harga_nilai_barang" type="text" class="harga_nilai_barang form-control" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>Harga Barang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="harga_cif" name="harga_cif" value="<?= $bc23 != null ? ($bc23['cif'] != null ? $bc23['cif'] : '0,00') : '0,00' ?>" type="text" class="harga_cif form-control" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>Harga Cif</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_nilai_pabean" readonly value="<?= $bc23 != null ? ($bc23['harga_penyerahan'] != null ? $bc23['harga_penyerahan'] : '0,00') : '0,00' ?>" name="harga_nilai_pabean" type="text" class="form-control harga_nilai_pabean" placeholder="">
                                <label>Harga Barang Pabean</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Harga Lainnya
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_lainnya_biaya_penambah" value="<?= $bc23 != null ? ($bc23['biaya_tambahan'] != null ? $bc23['biaya_tambahan'] : '0,00') : '0,00' ?>" maxlength="24" name="harga_lainnya_biaya_penambah" type="text" class="form-control harga_lainnya_biaya_penambah" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>Biaya Penambah</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_lainnya_biaya_pengurang" value="<?= $bc23 != null ? ($bc23['biaya_pengurang'] != null ? $bc23['biaya_pengurang'] : '0,00') : '0,00' ?>" maxlength="24" name="harga_lainnya_biaya_pengurang" type="text" class="form-control harga_lainnya_biaya_pengurang" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>Biaya Pengurang</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_lainnya_free_on_board" value="<?= $bc23 ? ($bc23['fob'] != null ? $bc23['fob'] : '0,00') : '0,00' ?>" maxlength="24" name="harga_lainnya_free_on_board" type="text" class="form-control harga_lainnya_free_on_board" placeholder="">
                                <label>FOB (Free on Board)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_lainnya_freight" maxlength="24" value="<?= $bc23 != null ? ($bc23['freight'] != null ? $bc23['freight'] : '0,00') : '0,00' ?>" name="harga_lainnya_freight" type="text" class="form-control harga_lainnya_freight" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>Freight</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select harga_lainnya_kode_asuransi" id="harga_lainnya_kode_asuransi" name="harga_lainnya_kode_asuransi" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeAsuransi as $k) : ?>
                                        <option <?= $bc23 != null ? ($bc23['kode_asuransi'] == $k['value'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Asuransi</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="harga_lainnya_nilai_asuransi" value="<?= $bc23 != null ? ($bc23['asuransi'] != null ? $bc23['asuransi'] : '0,00') : '0,00' ?>" name="harga_lainnya_nilai_asuransi" type="text" class="form-control harga_lainnya_nilai_asuransi" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>Nilai Asuransi</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Berat
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="berat_bruto" name="berat_bruto" value="<?= $bc23 != null ? ($bc23['bruto'] != null ? $bc23['bruto'] : '0,00') : '0,00' ?>" type="text" class="form-control berat_bruto" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>Berat Bersih/Bruto (KGM)</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="berat_netto" value="<?= $bc23 != null ? ($bc23['netto'] != null ? $bc23['netto'] : '0,00') : '0,00' ?>" name="berat_netto" type="text" class="form-control berat_netto" placeholder="" onchange="this.value = formatRupiah(this.value)">
                                <label>Berat Kotor/Netto (KGM)</label>
                            </div>
                        </div>
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Keterangan Pajak
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select pajak_jasa_kena_pajak" id="pajak_jasa_kena_pajak" name="pajak_jasa_kena_pajak" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKenaPajak as $k) : ?>
                                        <option <?= $bc23 != null ? ($bc23['kode_kena_pajak'] == $k['value'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['value']) ?>">
                                            <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Jasa Kena Pajak</label>
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
                    harga_kode_valuta: $('#harga_kode_valuta').val()
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

                            var ndpbm = convertRupiahToNumber($("#harga_ndpbm").val()) || 0;
                            var hargaBarang = convertRupiahToNumber($("#harga_nilai_barang").val()) || 0;
                            var hargaPabean = (Number(ndpbm) * Number(hargaBarang));

                            $('#harga_nilai_pabean').val(formatRupiah(hargaPabean))
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
        var hargaBarang = convertRupiahToNumber($("#harga_nilai_barang").val()) || 0;
        var hargaPabean = (Number(ndpbm) * Number(hargaBarang));

        $('#harga_cif').val(formatRupiah(ndpbm));
        $('#harga_nilai_pabean').val(formatRupiah(hargaPabean))
    });

    $('#harga_nilai_barang').keyup(function() {
        $('#harga_lainnya_free_on_board').val(formatRupiah($(this).val()));

        var ndpbm = convertRupiahToNumber($("#harga_ndpbm").val()) || 0;
        var hargaBarang = convertRupiahToNumber($(this).val()) || 0;
        var hargaPabean = (Number(ndpbm) * Number(hargaBarang));

        $('#harga_nilai_pabean').val(formatRupiah(hargaPabean))
    });

    $('#pajak_jasa_kena_pajak').select2({
        placeholder: "Pilih Jasa Kena Pajak",
        theme: "bootstrap-5",
    });

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

    var validatorTransaksi = $("#form-transaksi").validate({
        rules: {
            harga_kode_valuta: {
                required: true
            },
            harga_ndpbm: {
                required: true
            },
            harga_kode_harga_barang: {
                required: true
            },
            harga_nilai_barang: {
                required: true
            },
            harga_cif: {
                required: true
            },
            harga_nilai_pabean: {
                required: true
            },
            harga_lainnya_biaya_penambah: {
                required: true
            },
            harga_lainnya_biaya_pengurang: {
                required: true
            },
            harga_lainnya_free_on_board: {
                required: true
            },
            harga_lainnya_freight: {
                required: true
            },
            harga_lainnya_kode_asuransi: {
                required: true
            },
            harga_lainnya_nilai_asuransi: {
                required: true
            },
            berat_bruto: {
                required: true
            },
            berat_netto: {
                required: true
            },
            pajak_jasa_kena_pajak: {
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
            harga_kode_harga_barang: {
                required: "Pilih kode harga barang"
            },
            harga_nilai_barang: {
                required: "Nilai barang wajib diisi"
            },
            harga_cif: {
                required: "CIF wajib diisi"
            },
            harga_nilai_pabean: {
                required: "Harga barang pabean wajib diisi"
            },
            harga_lainnya_biaya_penambah: {
                required: "Biaya penambah wajib diisi"
            },
            harga_lainnya_biaya_pengurang: {
                required: "Biaya pengurang wajib diisi"
            },
            harga_lainnya_free_on_board: {
                required: "FOB wajib diisi"
            },
            harga_lainnya_freight: {
                required: "Freight wajib diisi"
            },
            harga_lainnya_kode_asuransi: {
                required: "Pilih asuransi"
            },
            harga_lainnya_nilai_asuransi: {
                required: "Nilai asuransi wajib diisi"
            },
            berat_bruto: {
                required: "Bruto wajib diisi"
            },
            berat_netto: {
                required: "Netto wajib diisi"
            },
            pajak_jasa_kena_pajak: {
                required: "Pilih jasa kena pajak"
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
                    formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-23/id/transaksi"); ?>",
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
            return "0,00";
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