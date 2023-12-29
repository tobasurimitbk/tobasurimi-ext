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
                                    <option value="<?= encrypt($k['value']) ?>">
                                        <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Valuta</label>
                        </div>
                    </div>

                    <a href="#" id="btn-sesuai-valuta-terbaru" class="btn btn-primary" style="float: right;">
                        Sesuai Valuta Terbaru
                    </a>

                    <button class="btn btn-primary" type="button" disabled id="btn-sesuai-valuta-terbaru-loading" style="float: right;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading
                    </button>

                    <br><br>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="harga_ndpbm" value="0" name="harga_ndpbm" type="text" class="form-control harga_ndpbm" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                            <label>NDPBM</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select harga_kode_harga_barang" id="harga_kode_harga_barang" name="harga_kode_harga_barang" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeIncoterm as $k) : ?>
                                    <option value="<?= encrypt($k['value']) ?>">
                                        <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Kode Harga Barang</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input id="harga_nilai_barang" name="harga_nilai_barang" type="text" class="harga_nilai_barang form-control" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                            <label>Harga Barang</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input id="harga_cif" name="harga_cif" readonly type="text" class="harga_cif form-control" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                            <label>Harga Cif</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="harga_nilai_pabean" readonly name="harga_nilai_pabean" type="text" class="form-control harga_nilai_pabean" placeholder="">
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
                            <input id="harga_lainnya_biaya_penambah" maxlength="24" name="harga_lainnya_biaya_penambah" type="text" class="form-control harga_lainnya_biaya_penambah" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                            <label>Biaya Penambah</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="harga_lainnya_biaya_pengurang" maxlength="24" name="harga_lainnya_biaya_pengurang" type="text" class="form-control harga_lainnya_biaya_pengurang" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                            <label>Biaya Pengurang</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="harga_lainnya_free_on_board" maxlength="24" name="harga_lainnya_free_on_board" type="text" class="form-control harga_lainnya_free_on_board" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                            <label>FOB (Free on Board)</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="harga_lainnya_freight" maxlength="24" name="harga_lainnya_freight" type="text" class="form-control harga_lainnya_freight" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                            <label>Freight</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select harga_lainnya_kode_asuransi" id="harga_lainnya_kode_asuransi" name="harga_lainnya_kode_asuransi" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($kodeAsuransi as $k) : ?>
                                    <option value="<?= encrypt($k['value']) ?>">
                                        <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Asuransi</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="harga_lainnya_nilai_asuransi" name="harga_lainnya_nilai_asuransi" type="text" class="form-control harga_lainnya_nilai_asuransi" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
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
                            <input id="berat_bruto" name="berat_bruto" type="text" class="form-control berat_bruto" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
                            <label>Berat Bersih/Bruto (KGM)</label>
                        </div>
                    </div>
                    <div class="mt-1">
                        <div class="form-floating mb-3">
                            <input id="berat_netto" name="berat_netto" type="text" class="form-control berat_netto" placeholder="" oninput="this.value = this.value.replace(/[^\d,]/g, '')">
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
                                    <option value="<?= encrypt($k['value']) ?>">
                                        <?= strtoupper($k['value']) . " - " . strtoupper($k['description']) . "" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label style="z-index: 1;">Jasa Kena Pajak</label>
                        </div>
                    </div>
                </div>
            </div>

            <a href="#" class="btn btn-primary mt-4" style="float: right;">
                Simpan Perubahan
            </a>
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
                            $("#harga_ndpbm").val((res?.data?.data));
                            $("#harga_cif").val((res?.data?.data));
                        }
                    }

                    if (res.data.status === false) {
                        Swal.fire({
                            icon: 'warning',
                            title: res.data.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    }
                }
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: "Pilih valuta dahulu",
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        }
    });

    $('#harga_nilai_barang').keyup(function() {
        var ndpbm = $("#harga_ndpbm").val() || 0;
        var hargaBarang = $(this).val() || 0;
        $('#harga_nilai_pabean').val((Number(ndpbm) * Number(hargaBarang)))
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
</script>


<?= $this->endSection(); ?>