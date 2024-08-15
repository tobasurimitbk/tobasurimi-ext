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
            BC 3.0 - PEMBERITAHUAN EKSPOR BARANG
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>

            <!-- DETAIL BARANG VIEW -->
            <div class="detail-barang-form-view">
                <div class="mt-3">
                    <button class="btn btn-primary" type="button" disabled id="btn-loading" style="float: right;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading
                    </button>
                    <a class="btn btn-primary float-right btn-simpan-detail-barang" id="btn-simpan-detail-barang" href="#">
                        Simpan
                    </a>
                    <a class="btn btn-danger float-right" href="<?= base_url('bea-cukai-bc-30/id/barang/' . encrypt($bc30['id'])) ?>" style="margin-right: 8px;">
                        Batal
                    </a>
                </div>

                <table width="100%" class="mb-3">
                    <tbody>
                        <tr style="color: black;">
                            <td width="150px"><b>Kode Barang</b></td>
                            <td width="10px">:</td>
                            <td><?= $barang['barangDetail']['kode_barang_internal'] ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Nama Barang</b></td>
                            <td width="10px">:</td>
                            <td><?= $barang['barangDetail']['nama_barang'] ?></td>
                        </tr>
                    </tbody>
                </table>
                <hr style="color: black;">

                <form id="form-barang-detail">
                    <div class="row">
                        <div class="col-sm-6 mt-1">

                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input readonly value="<?= $barang['bcDetail']->seriBarang ?>" id="seriBarang" name="seriBarang" type="number" class="form-control seriBarang" placeholder="">
                                    <label>Seri Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select posTarif" id="posTarif" name="posTarif" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeHS as $k) : ?>
                                            <option value="<?= $k['code'] ?>" <?= $barang['bcDetail']->posTarif == $k['code'] ? 'selected' : '' ?>>
                                                <?= strtoupper($k['code']) . " - " . strtoupper($k['uraian_barang']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Pilih Kode HS/Pos Tarif</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-2">
                                    <input value="<?= $barang['bcDetail']->kodeBarang ?>" id="kodeBarang" readonly name="kodeBarang" type="text" class="form-control kodeBarang" placeholder="">
                                    <label>Kode</label>
                                </div>
                            </div>

                            <a href="#" id="btn-sesuai-kode-hs" class="btn btn-warning" style="float: right;">
                                Sesuai Hs
                            </a> <br><br>
                            <div class="mt-1">
                                <div class="form-floating mb-2">
                                    <label>Lartas</label>
                                </div>
                            </div>

                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea id="uraian" name="uraian" type="text" class="form-control uraian" placeholder="" style="height: 100px;"><?= $barang['bcDetail']->uraian ?></textarea>
                                    <label>Uraian</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="merk" name="merk" type="text" class="form-control merk" value="<?= $barang['bcDetail']->merk ?>" placeholder="">
                                    <label>Merk Barang</label>
                                </div>
                            </div>
                            <div class=" mt-1">
                                <div class="form-floating mb-3">
                                    <input id="tipe" name="tipe" type="text" class="form-control tipe" value="<?= $barang['bcDetail']->tipe ?>" placeholder="">
                                    <label>Tipe Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="ukuran" name="ukuran" type="text" class="form-control ukuran" value="<?= $barang['bcDetail']->ukuran ?>" placeholder="">
                                    <label>Ukuran</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select negaraAsalBarang" id="negaraAsalBarang" name="negaraAsalBarang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeNegaraAsal as $k) : ?>
                                            <option <?= $barang['bcDetail']->kodeNegaraAsal == $k['code'] ? 'selected' : '' ?> value="<?= encrypt($k['code']) ?>">
                                                <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Negara Asal Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select daerahAsalBarang" id="daerahAsalBarang" name="daerahAsalBarang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option <?= $barang['bcDetail']->kodeDaerahAsal == 1 ? 'selected' : '' ?> value="1">1 - SEPENUHNYA DIPEROLEH DAN/ATAU DIPRODUKSI DI LUAR DAERAH PABEAN</option>
                                        <option <?= $barang['bcDetail']->kodeDaerahAsal == 2 ? 'selected' : '' ?> value="2">2 - SEPENUHNYA DIPERLOEH DAN/ATAU DIPRODUKSI DI TEMPAT LAIN DALAM DAERAH PABEAN</option>
                                        <option <?= $barang['bcDetail']->kodeDaerahAsal == 3 ? 'selected' : '' ?> value="3">3 - KAWASAN PABEAN </option>
                                        <option <?= $barang['bcDetail']->kodeDaerahAsal == 4 ? 'selected' : '' ?> value="4">LAINNYA</option>
                                    </select>
                                    <label style="z-index: 1;">Daerah Asal Barang</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 mt-1">

                            <div class="mt-1">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-floating mb-2">
                                            <input value="<?= $barang['bcDetail']->jumlahSatuan ?>" id="jumlahSatuan" name="jumlahSatuan" type="text" class="form-control jumlahSatuan" placeholder="">
                                            <label>Jumlah Satuan</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select kodeSatuanBarang" id="kodeSatuanBarang" name="kodeSatuanBarang" aria-label="Floating label select example">
                                                <option value=""></option>
                                                <?php foreach ($kodeSatuanBarang as $k) : ?>
                                                    <option <?= $barang['bcDetail']->kodeSatuanBarang == $k['value'] ? 'selected' : '' ?> value="<?= $k['value'] ?>">
                                                        <?= $k['value'] . " - " . strtoupper($k['description']) . "" ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label style="z-index: 1;">Kode Satuan</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-floating mb-2">
                                            <input value="<?= $barang['bcDetail']->jumlahKemasan ?>" id="jumlahKemasan" name="jumlahKemasan" type="text" class="form-control jumlahKemasan" placeholder="">
                                            <label>Jumlah Kemasan</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating mb-2" style="height: 50px;">
                                            <select class="form-select kodeJenisKemasan" id="kodeJenisKemasan" name="kodeJenisKemasan" aria-label="Floating label select example">
                                                <option value=""></option>
                                                <?php foreach ($kodeJenisKemasan as $k) :  ?>
                                                    <option value="<?= $k['description'] ?>" <?= $barang['bcDetail']->kodeJenisKemasan == $k['description'] ? 'selected' : '' ?>>
                                                        <?= $k['description'] . " - " . strtoupper($k['value']) . "" ?>
                                                    </option>
                                                <?php endforeach;  ?>
                                            </select>
                                            <label style="z-index: 1;">Kode Jenis Kemasan</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="form-floating mb-3">
                                    <input value="<?= $barang['bcDetail']->fob ?>" id="fob" name="fob" type="text" class="form-control fob" placeholder="">
                                    <label>Harga FOB</label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="form-floating mb-3">
                                    <input value="<?= $barang['bcDetail']->volume ?>" id="volume" name="volume" type="text" class="form-control volume" placeholder="">
                                    <label>Volume</label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="form-floating mb-3">
                                    <input value="<?= $barang['bcDetail']->netto ?>" id="netto" name="netto" type="text" class="form-control netto" placeholder="">
                                    <label>Berat Bersih (Kg)</label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="form-floating mb-3">
                                    <input value="<?= $barang['bcDetail']->fob ?>" id="satuan_fob" name="satuan_fob" type="text" class="form-control satuan_fob" placeholder="">
                                    <label>Harga Satuan FOB</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select barang_detail_kode_asal_bahan_baku" id="barang_detail_kode_asal_bahan_baku" name="barang_detail_kode_asal_bahan_baku" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="1">1 - SEPENUHNYA DIPEROLEH DAN/ATAU DIPRODUKSI DI LUAR DAERAH PABEAN</option>
                                        <option value="2">2 - SEPENUHNYA DIPEROLEH DAN/ATAU DIPRODUKSI DI TEMPAT LAIN DALAM DAERAH PABEAN</option>
                                        <option value="3">3 - KAWASAN PABEAN</option>
                                        <option value="4">4 - LAINNYA</option>
                                    </select>
                                    <label style="z-index: 1;">Referensi Asal Barang</label>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
                <div class="row">
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3 mt-3">
                            Dokumen Fasilitas / Lartas
                        </label>

                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-lartas" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:10px;">#</th>
                                    <th style="text-align: center;">Seri Dokumen</th>
                                    <th style="text-align: center;">Kode Dokumen</th>
                                    <th style="text-align: center;">Nomor Dokumen</th>
                                    <th style="text-align: center;">Tanggal Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($dokumen) == 0) :  ?>
                                    <tr style="color:white; text-align: center;">
                                        <td colspan="5">Tidak ada dokumen</td>

                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($dokumen as $i => $d) : ?>
                                        <tr style="color: white; text-align:center;">
                                            <td>
                                                <input <?= in_array($d['seriDokumen'], $dokumenSelected) ? 'checked' : '' ?> name="dokumen_id" data-seri_dokumen="<?= $d['seriDokumen'] ?>" class="child dokumen_id" type="checkbox">
                                            </td>
                                            <td><?= $d['seriDokumen'] ?></td>
                                            <td><?= $d['kodeDokumen'] ?></td>
                                            <td><?= $d['nomorDokumen'] ?></td>
                                            <td><?= $d['tanggalDokumen'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3 mt-3">
                            Entitas Barang
                        </label>


                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-entitas-barang" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:10px;">#</th>
                                    <th style="text-align: center; width:10px;">Seri</th>
                                    <th style="text-align: center;">No Identitas</th>
                                    <th style="text-align: center;">Nama</th>
                                    <th style="text-align: center;">Alamat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php if (count($entitas) == 0) :  ?>
                                    <tr style="color:white; text-align: center;">
                                        <td colspan="5">Tidak ada Entitas</td>

                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($entitas as $i => $e) : ?>
                                        <tr style="color: white; text-align:center;">
                                            <td>
                                                <input <?= in_array($e['seriEntitas'], $entitasSelected) ? 'checked' : '' ?> name="entitas_id" data-seri_entitas="<?= $e['seriEntitas'] ?>" class="child entitas_id" type="checkbox">
                                            </td>

                                            <td><?= $no++; ?></td>
                                            <td><?= $e['noIdentitas'] ?></td>
                                            <td><?= $e['namaEntitas'] ?></td>
                                            <td><?= $e['alamatEntitas'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>


        </div>
    </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var bahanBakuBCList = [];

    $('#btn-loading').hide();

    $('#btn-sesuai-kode-hs').click(function(e) {
        e.preventDefault();
        var str = $('#posTarif').find('option:selected').text().split('-');
        $('#uraian').val("" + str[1].trim());
    });


    $('#kodeJenisKemasan').select2({
        placeholder: "Pilih Kode Jenis Kemasan",
        theme: "bootstrap-5",
    });
    $('#negaraAsalBarang').select2({
        placeholder: "Pilih Kode Negara Asal Barang",
        theme: "bootstrap-5",
    });
    $('#daerahAsalBarang').select2({
        placeholder: "Pilih Kode Daerah Asal Barang",
        theme: "bootstrap-5",
    });
    $('#barang_detail_kode_asal_bahan_baku').select2({
        placeholder: "Pilih Kode Referensi Asal Barang",
        theme: "bootstrap-5",
    });

    $('#posTarif').select2({
        placeholder: "Pilih Kode HS / Pos Tarif",
        theme: "bootstrap-5",
    });

    $('#kodeGunaBarang').select2({
        placeholder: "Pilih Kode Penggunaan",
        theme: "bootstrap-5",
    });

    $('#kodeKategoriBarang').select2({
        placeholder: "Pilih Kode Kategori Barang",
        theme: "bootstrap-5",
    });

    $('#kodeKondisiBarang').select2({
        placeholder: "Pilih Kode Kondisi Barang",
        theme: "bootstrap-5",
    });

    $('#kodePerhitungan').select2({
        placeholder: "Pilih Kode Perhitungan Barang",
        theme: "bootstrap-5",
    });

    $('#kodeFasilitasTarif').select2({
        placeholder: "Pilih Kode Fasilitas Tarif",
        theme: "bootstrap-5",
    });

    $('#kodeJenisTarif').select2({
        placeholder: "Pilih Kode Jenis Tarif",
        theme: "bootstrap-5",
    });

    $('#kodeJenisPungutan').select2({
        placeholder: "Pilih Kode Jenis Pungutan",
        theme: "bootstrap-5",
    });

    $('#search_no_aju_daftar').select2({
        placeholder: "Cari berdasarkan nomor aju / daftar",
        theme: "bootstrap-5",
        dropdownParent: $('#modalBahanBaku'),
        allowClear: true,
        ajax: {
            url: '<?= base_url('bea-cukai-bc-25/list-bahan-baku-asal') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                    tipe: $('#tipe').val()
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                };
            },
            cache: true
        },
        minimumInputLength: 1,
    }).change(function() {
        $.ajax({
            url: "<?= base_url("bea-cukai-bc-25/list-payload-barang"); ?>",
            data: {
                bc_purchase_order_id: $(this).val(),
                tipe: $('#tipe').val()
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "GET",
            dataType: "json",
            success: function(response) {
                bahanBakuBCList = [];
                bahanBakuBCList = response.data;
                drawTable(bahanBakuBCList)
            },
        });
    });



    $('#kodeSatuanBarang').select2({
        placeholder: "Pilih Kode Satuan Barang",
        theme: "bootstrap-5",
        allowClear: true,
        ajax: {
            url: '<?= base_url('bea-cukai-bc-40/satuan-barang') ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                };
            },
            cache: true
        },
        minimumInputLength: 1,
    });

    var validatorBarangDetail = $("#form-barang-detail").validate({
        rules: {
            seriBarang: {
                required: true
            },
            posTarif: {
                required: true
            },
            kodeBarang: {
                required: true
            },
            uraian: {
                required: true
            },
            merk: {
                required: true
            },
            tipe: {
                required: true
            },
            spesifikasiLain: {
                required: true
            },
            jumlahSatuan: {
                required: true
            },
            kodeSatuanBarang: {
                required: true
            },
            volume: {
                required: true
            },
            netto: {
                required: true
            },
            cif: {
                required: true
            },
            hargaEkspor: {
                required: true
            },
            hargaPenyerahan: {
                required: true
            },
            hargaPerolehan: {
                required: true
            },
            nilaiJasa: {
                required: true
            },
        },
        messages: {
            seriBarang: {
                required: "Seri barang wajib diisi"
            },
            posTarif: {
                required: "Pos tarif wajib diisi"
            },
            kodeBarang: {
                required: "Kode barang wajib diisi"
            },
            uraian: {
                required: "Uraian wajib diisi"
            },
            merk: {
                required: "Merk wajib diisi"
            },
            tipe: {
                required: "Tipe wajib diisi"
            },
            spesifikasiLain: {
                required: "Spesifikasi lain wajib diisi"
            },
            jumlahSatuan: {
                required: "Jumlah Satuan wajib diisi"
            },
            kodeSatuanBarang: {
                required: "Kode Satuan Barang wajib diisi"
            },
            volume: {
                required: "Volume wajib diisi"
            },
            netto: {
                required: "Barang Bersih wajib diisi"
            },
            cif: {
                required: "Cif wajib diisi"
            },
            hargaEkspor: {
                required: "Nilai Pabean wajib diisi"
            },
            hargaPenyerahan: {
                required: "Harga Penyerahan wajib diisi"
            },
            hargaPerolehan: {
                required: "Harga Perolehan wajib diisi"
            },
            nilaiJasa: {
                required: "NIlai Jasa wajib diisi"
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

    var validatorPungutan = $("#form-pungutan").validate({
        rules: {
            kodeJenisPungutan: {
                required: true
            },
            kodeJenisTarif: {
                required: true
            },
            tarif: {
                required: true
            },
            kodeFasilitasTarif: {
                required: true
            },
            tarifFasilitas: {
                required: true
            },
        },
        messages: {
            kodeJenisPungutan: {
                required: "Pilih kode jenis pungutan"
            },
            kodeJenisTarif: {
                required: "Pilih kode jenis tarif"
            },
            tarif: {
                required: "Tarif wajib diisi"
            },
            kodeFasilitasTarif: {
                required: "Pilih kode fasilitas tarif"
            },
            tarifFasilitas: {
                required: "Tarif fasilitas wajib diisi"
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

    var validatorBahanBaku = $("#form-create-bahan-baku").validate({
        rules: {
            search_no_aju_daftar: {
                required: true
            }
        },
        messages: {
            search_no_aju_daftar: {
                required: "No aju / daftar wajib diisi"
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

    $('.btn-open-modal').click(function() {
        var buttonAdd = $(this);
        $('#tipe').val(buttonAdd.data('tipe'));
        $('#modal-title').text('BARANG ASAL ' + buttonAdd.data('tipe'));
        // CLEAR DATA
        $('#search_no_aju_daftar').val(null).change();
        bahanBakuBCList = [];
        drawTable(bahanBakuBCList);

        $('#modalBahanBaku').modal('show');
    });

    $('.btn-discard-modal').click(function() {
        $('#modalBahanBaku').modal('hide');
    });



    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');



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

    $(document).on('click', '.dokumen_id', function() {
        var checkbox = $(this);
        var isChecked = checkbox.prop('checked');
        var seriDokumen = checkbox.data('seri_dokumen');

        if (isChecked) {
            // Tambah
            Swal.fire({
                icon: 'question',
                title: "Simpan dokumen dengan nomor seri " + seriDokumen + " ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    formData.append("seriBarang", "<?= $barang['bcDetail']->seriBarang ?>");
                    formData.append("seriDokumen", seriDokumen);

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/barang/dokumen-create"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
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
                                    location.reload();
                                });
                            }
                        },
                    });
                } else {
                    checkbox.prop('checked', false);
                }
            });
        } else {
            // Hapus
            Swal.fire({
                icon: 'question',
                title: "Hapus dokumen dengan nomor seri " + seriDokumen + " ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    formData.append("seriBarang", "<?= $barang['bcDetail']->seriBarang ?>");
                    formData.append("seriDokumen", seriDokumen);

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/barang/dokumen-delete"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
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
                                    location.reload();
                                });

                            }
                        },
                    });
                } else {
                    checkbox.prop('checked', true);
                }
            });
        }
    });
    $(document).on('click', '.entitas_id', function() {
        var checkbox = $(this);
        var isChecked = checkbox.prop('checked');
        var seriEntitas = checkbox.data('seri_entitas');


        if (isChecked) {
            // Tambah
            Swal.fire({
                icon: 'question',
                title: "Simpan Entitas Barang dengan nomor seri " + seriEntitas + " ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    formData.append("seriBarang", "<?= $barang['bcDetail']->seriBarang ?>");
                    formData.append("seriEntitas", seriEntitas);

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/barang/entitas-create"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
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
                                    location.reload();
                                });
                            }
                        },
                    });
                } else {
                    checkbox.prop('checked', false);
                }
            });
        } else {
            // Hapus
            Swal.fire({
                icon: 'question',
                title: "Hapus entitas dengan nomor seri " + seriEntitas + " ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");
                    formData.append("seriBarang", "<?= $barang['bcDetail']->seriBarang ?>");
                    formData.append("seriEntitas", seriEntitas);

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/barang/entitas-delete"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
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
                                    location.reload();
                                });

                            }
                        },
                    });
                } else {
                    checkbox.prop('checked', true);
                }
            });
        }
    });
    $('#btn-simpan-detail-barang').click(function() {
        if ($('#form-barang-detail').valid()) {
            Swal.fire({
                icon: 'question',
                title: "Update Detail Barang Dokumen ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-barang-detail"));
                    formData.append("id", "<?= encrypt($bc30['id']) ?>");

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-30/id/barang"); ?>",
                        data: formData,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            $('#btn-loading').show();
                            $('#btn-simpan-detail-barang').hide();
                        },
                        complete: function() {
                            $('#btn-loading').hide();
                            $('#btn-simpan-detail-barang').show();
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                location.reload();
                            });
                        },
                    });
                }
            })
        }
    });
</script>


<?= $this->endSection(); ?>