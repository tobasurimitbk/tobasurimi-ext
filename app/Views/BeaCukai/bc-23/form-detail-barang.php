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
            BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
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
                    <a class="btn btn-primary float-right btn-simpan-detail-barang-form-view" id="btn-simpan-detail-barang-form-view" href="#">
                        Simpan
                    </a>
                    <a class="btn btn-danger float-right" href="<?= base_url('bea-cukai-bc-23/id/barang/' . encrypt($bcPo['id'])) ?>" style="margin-right: 8px;">
                        Kembali
                    </a>
                </div>

                <table width="100%" class="mb-3">
                    <tbody>
                        <tr style="color: black;">
                            <td width="150px"><b>Tgl PO</b></td>
                            <td width="10px">:</td>
                            <td><?= $barangDetail['po_date'] ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Tgl LPB</b></td>
                            <td width="10px">:</td>
                            <td><?= $barangDetail['lpb_date'] ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Nomor LPB</b></td>
                            <td width="10px">:</td>
                            <td><?= $barangDetail['lpb_no'] ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Nomor PO</b></td>
                            <td width="30px">:</td>
                            <td><?= $barangDetail != null ? $barangDetail['po_no'] : '-' ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Nama Barang</b></td>
                            <td width="10px">:</td>
                            <td><?= $barangDetail['barang_name'] ?></td>
                        </tr>
                    </tbody>
                </table>
                <hr style="color: black;">
                <?php if ($bc23DokumenBarang == null) : ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Dokumen barang ini belum diisi</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form id="form-barang-dokumen">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mb-3">
                                Jenis
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input readonly value="<?= $bc23DokumenBarang == null ? $seriBarang : $bc23DokumenBarang['seri_barang'] ?>" id="barang_detail_seri_barang" name="barang_detail_seri_barang" type="number" class="form-control barang_detail_seri_barang" placeholder="">
                                    <label>Seri Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select barang_detail_kode_hs" id="barang_detail_kode_hs" name="barang_detail_kode_hs" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeHS as $k) : ?>
                                            <option <?= $bc23DokumenBarang != null ? ($bc23DokumenBarang['pos_tarif'] == $k['code'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['code']) ?>">
                                                <?= strtoupper($k['code']) . " - " . strtoupper($k['uraian_barang']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Pilih Kode HS/Pos Tarif</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-2">
                                    <input value="<?= $barangDetail != null ? $barangDetail['kode_barang'] : '-' ?>" id="barang_detail_kode_barang" readonly name="barang_detail_kode_barang" type="text" class="form-control barang_detail_kode_barang" placeholder="">
                                    <label>Kode</label>
                                </div>
                            </div>

                            <a href="#" id="btn-sesuai-kode-hs" class="btn btn-warning" style="float: right;">
                                Sesuai Hs
                            </a> <br><br>

                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea id="barang_detail_uraian" name="barang_detail_uraian" type="text" class="form-control barang_detail_uraian" placeholder="" style="height: 100px;"><?= $bc23DokumenBarang != null ? $bc23DokumenBarang['uraian'] : '' ?></textarea>
                                    <label>Uraian</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_merk_barang" name="barang_detail_merk_barang" type="text" class="form-control barang_detail_merk_barang" value="<?= $bc23DokumenBarang != null ? $bc23DokumenBarang['merk_barang'] : '-' ?>" placeholder="">
                                    <label>Merk Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_tipe_barang" name="barang_detail_tipe_barang" type="text" class="form-control barang_detail_tipe_barang" value="<?= $bc23DokumenBarang != null ? $bc23DokumenBarang['tipe_barang'] : '-' ?>" placeholder="">
                                    <label>Tipe Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_spesifikasi_lain" name="barang_detail_spesifikasi_lain" type="text" class="form-control barang_detail_spesifikasi_lain" value="<?= $bc23DokumenBarang != null ? $bc23DokumenBarang['spesifikasi_lain'] : '-' ?>" placeholder="">
                                    <label>Spesifikasi Lain</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mb-3">
                                Kategori Barang
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select barang_detail_kategori_barang" id="barang_detail_kategori_barang" name="barang_detail_kategori_barang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKategoriBarang as $k) : ?>
                                            <option <?= $bc23DokumenBarang != null ? ($bc23DokumenBarang['kode_kategori_barang'] == str_replace(']', '', explode(',', $k['description'])[1]) ? 'selected' : '') : '' ?> value="<?= encrypt(str_replace(']', '', explode(',', $k['description'])[1])) ?>">
                                                <?= str_replace(']', '', explode(',', $k['description'])[1]) . " - " . strtoupper($k['value']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Pilih Kode Kategori Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select barang_detail_negara" id="barang_detail_negara" name="barang_detail_negara" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeNegaraAsal as $k) : ?>
                                            <option <?= $bc23DokumenBarang != null ? ($bc23DokumenBarang['kode_negara_asal'] == $k['code'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['code']) ?>">
                                                <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Negara</label>
                                </div>
                            </div>
                            <label class="form-label font-weight-bold lable-title mb-3">
                                Harga
                            </label>
                            <div class="mt-1">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <?= $barangDetail['valas'] ?>
                                        </span>
                                    </div>
                                    <div class="form-floating">
                                        <input readonly id="barang_detail_harga" value="<?= number_format($barangDetail['harga'], 2)  ?>" name="barang_detail_harga" type="text" class="form-control barang_detail_harga" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                        <label>Harga</label>
                                        <small><i>Harga total sesuai dengan LPB diterima</i></small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_biaya_tambahan" name="barang_detail_biaya_tambahan" type="text" value="<?= $bc23DokumenBarang == null ? "0,00" : number_format($bc23DokumenBarang['nilai_tambah'], 2) ?>" class="form-control barang_detail_biaya_tambahan" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Biaya Tambahan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_fob" name="barang_detail_fob" type="text" value="<?= $bc23DokumenBarang == null ? "0,00" : number_format($bc23DokumenBarang['fob'], 2) ?>" class="form-control barang_detail_fob" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>FOB</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $barangDetail['qty_lpb'] == 0 ? '0,00' : number_format($barangDetail['harga'] / $barangDetail['qty_lpb'], 2) ?>" id="barang_detail_harga_satuan" name="barang_detail_harga_satuan" type="text" class="form-control barang_detail_harga_satuan" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Harga Satuan</label>
                                    <small><i>Harga satuan diambil dari total harga LPB dibagi jumlah diterima LPB</i></small>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_freight" name="barang_detail_freight" type="text" value="<?= $bc23DokumenBarang == null ? "0,00" : number_format($bc23DokumenBarang['freight'], 2) ?>" class="form-control barang_detail_freight" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Freight</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_asuransi" name="barang_detail_asuransi" type="text" value="<?= $bc23DokumenBarang == null ? "0,00" : number_format($bc23DokumenBarang['asuransi'], 2) ?>" class="form-control barang_detail_asuransi" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Asuransi</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_cif" name="barang_detail_cif" readonly type="text" value="<?= $bc23DokumenBarang == null ? number_format($barangDetail['harga'], 2) : number_format($bc23DokumenBarang['cif_rupiah'], 2) ?>" class="form-control barang_detail_cif" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Nilai CIF</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_nilai_pabean" name="barang_detail_nilai_pabean" readonly type="text" value="<?= $bc23DokumenBarang == null ? number_format($barangDetail['harga'] * $bc23['ndpbm'], 2) : number_format($barangDetail['harga'] * $bc23['ndpbm'], 2) ?>" class="form-control barang_detail_nilai_pabean" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Nilai Pabean</label>
                                    <small><i>Nilai pabean diambil dari nilai CIF dikalikan dengan Harga barang</i></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mb-3">
                                Jumlah & Berat
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= ($barangDetail['qty_lpb']) ?>" readonly id="barang_detail_jumlah_satuan" name="barang_detail_jumlah_satuan" type="text" class="form-control barang_detail_jumlah_satuan" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Jumlah Satuan</label>
                                    <small><i>Jumlah diterima sesuai dengan LPB</i></small>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select barang_detail_kode_satuan_barang" id="barang_detail_kode_satuan_barang" name="barang_detail_kode_satuan_barang" aria-label="Floating label select example">
                                        <?php if ($kodeSatuanBarang != null) : ?>
                                            <option value="<?= encrypt($kodeSatuanBarang['value']) ?>" selected>
                                                <?= $kodeSatuanBarang['value'] ?> - <?= $kodeSatuanBarang['description'] ?>
                                            </option>
                                        <?php else : ?>
                                            <option value=""></option>
                                        <?php endif; ?>
                                    </select>
                                    <label style="z-index: 1;">Kode Satuan Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_jumlah_kemasan" value="<?= $barangDetail['jumlah_kemasan'] ?>" readonly name="barang_detail_jumlah_kemasan" type="text" class="form-control barang_detail_jumlah_kemasan" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Jumlah Kemasan</label>
                                    <small><i>Nama Kemasan(dari LPB) : <?= $barangDetail['kemasan_name'] ?></i></small>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select barang_detail_kode_jenis_kemasan" id="barang_detail_kode_jenis_kemasan" name="barang_detail_kode_jenis_kemasan" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeJenisKemasan as $k) : ?>
                                            <option <?= $bc23DokumenBarang != null ? ($bc23DokumenBarang['kode_jenis_kemasan'] == $k['description'] ? 'selected' : '') : '' ?> value="<?= encrypt($k['description']) ?>">
                                                <?= $k['description'] . " - " . strtoupper($k['value']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Pilih Kode Jenis Kemasan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input value="<?= $bc23DokumenBarang != null ? number_format($bc23DokumenBarang['netto'], 2) : '' ?>" id="barang_detail_berat_bersih" name="barang_detail_berat_bersih" type="text" class="form-control barang_detail_berat_bersih" placeholder="" onkeyup="this.value = greatFormatRupiah(this.value)">
                                    <label>Berat Bersih (Kg)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Dokumen (Checklist yang digunakan)
                        </label>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-dokumen" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center; width:10px;">#</th>
                                        <th style="text-align: center;">Seri Dokumen</th>
                                        <th style="text-align: center;">Nomor Dokumen</th>
                                        <th style="text-align: center;">Jenis Dokumen</th>
                                        <th style="text-align: center;">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Pungutan
                        </label>
                        <div class="alert alert-secondary alert-dismissible fade show mt-3 text-black" role="alert">
                            Urutan penginputan pungutan adalah <br>
                            <b>Seri 1 : Bea Masuk / Bea Masuk KITE</b> <br>
                            <b>Seri 2 : PPH Impor</b> <br>
                            <b>Seri 3 : PPN Impor</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="form-pungutan">
                            <input type="hidden" name="bc_barang_tarif_id" id="bc_barang_tarif_id" class="bc_barang_tarif_id">
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <select class="form-select barang_detail_kode_jenis_pungutan" id="barang_detail_kode_jenis_pungutan" name="barang_detail_kode_jenis_pungutan" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeJenisPungutan as $k) : ?>
                                            <option value="<?= encrypt($k['value']) ?>">
                                                <?= $k['value'] . " - " . strtoupper($k['description']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Pilih Pungutan</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <select class="form-select barang_detail_kode_jenis_tarif" id="barang_detail_kode_jenis_tarif" name="barang_detail_kode_jenis_tarif" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeJenisTarif as $k) : ?>
                                            <option value="<?= encrypt($k['value']) ?>">
                                                <?= $k['value'] . " - " . strtoupper($k['description']) . " " ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Jenis Tarif</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_nilai_tarif" name="barang_detail_nilai_tarif" type="number" class="form-control barang_detail_nilai_tarif" placeholder="">
                                    <label>Nilai Tarif (%)</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select barang_detail_kode_fasilitas_tarif" id="barang_detail_kode_fasilitas_tarif" name="barang_detail_kode_fasilitas_tarif" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeFasilitasTarif as $k) : ?>
                                            <option value="<?= encrypt($k['value']) ?>">
                                                (<?= $k['value'] ?>) <?= $k['description'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Fasilitas Tarif</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="barang_detail_tarif_fasilitas" max="100" value="100" name="barang_detail_tarif_fasilitas" type="number" class="form-control barang_detail_tarif_fasilitas" placeholder="" minlength="1" maxlength="100" oninput="$(this).val(Math.max(0, Math.min(100, $(this).val())))">
                                    <label>Tarif Fasilitas (%)</label>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="row" style="float: right; margin-bottom:5px;">
                                    <div class="col-sm" style="margin-right: -20px;">
                                        <button type="button" class="btn btn-add btn-block float-right btn-submit-pungutan" style="float: right;">
                                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pungutan" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center; width:10px;">No</th>
                                        <th style="text-align: center;">Jenis Pungutan</th>
                                        <th style="text-align: center;">Jenis Tarif</th>
                                        <th style="text-align: center;">Nilai Tarif (%)</th>
                                        <th style="text-align: center;">Fasilitas Tarif</th>
                                        <th style="text-align: center;">Tarif Fasilitas (%)</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
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
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('#btn-loading').hide();

    $('#btn-sesuai-kode-hs').click(function(e) {
        e.preventDefault();
        var str = $('#barang_detail_kode_hs').find('option:selected').text().split('-');
        $('#barang_detail_uraian').val("" + str[1].trim());
    });

    $('#barang_detail_kode_hs').select2({
        placeholder: "Pilih Kode HS/Pos Tarif",
        theme: "bootstrap-5",
    });

    $('#persentase_jenis_negara').select2({
        placeholder: "Pilih Lokasi Negara",
        theme: "bootstrap-5",
    }).change(function() {
        var percentace = Number(destroyFormatRupiah($(this).val())) / 100;
        var freight = Number(destroyFormatRupiah($('#barang_detail_fob').val())) * percentace;
        var ndpbm = "<?= $ndpbm ?>";
        $('#barang_detail_freight').val(greatFormatRupiah(freight));

        var asuransi = (freight + Number(destroyFormatRupiah($('#barang_detail_harga').val()))) * 0.5;
        $('#barang_detail_asuransi').val(greatFormatRupiah(asuransi));

        var cif = ((asuransi) + (freight) + destroyFormatRupiah($('#barang_detail_harga').val()));
        $('#barang_detail_cif').val(greatFormatRupiah(cif));

        var nilaiPabean = ndpbm * cif;
        $('#barang_detail_nilai_pabean').val(greatFormatRupiah(nilaiPabean));

    });

    $('#barang_detail_kategori_barang').select2({
        placeholder: "Pilih Kategori Barang",
        theme: "bootstrap-5",
    });

    $('#barang_detail_negara').select2({
        placeholder: "Pilih Negara",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_jenis_kemasan').select2({
        placeholder: "Pilih Kode Jenis Kemasan",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_jenis_pungutan').select2({
        placeholder: "Pilih Jenis Pungutan",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_jenis_tarif').select2({
        placeholder: "Pilih Jenis Tarif",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_fasilitas_tarif').select2({
        placeholder: "Pilih Fasilitas Tarif",
        theme: "bootstrap-5",
    });

    $('#barang_detail_kode_satuan_barang').select2({
        placeholder: "Pilih Kode Satuan Barang",
        theme: "bootstrap-5",
        ajax: {
            url: '<?= base_url('bea-cukai-bc-23/satuan-barang') ?>',
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

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    var tableListInformasiPungutan = $('.table-list-informasi-pungutan').DataTable({

        processing: true,
        serverSide: true,
        ordering: false,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("bea-cukai-bc-23/id/barang-pungutan-all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
                data.penerimaan_barang_id = "<?= encrypt($barangDetail['penerimaan_barang_id']) ?>";
                data.barang1_id = "<?= encrypt($barangDetail['barang1_id']) ?>";
                data.sort = "bc_23_barang_tarif.createdAt";
                data.sortType = "ASC";
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "kode_jenis_pungutan",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "kode_jenis_tarif",
                searchable: false,
                sortable: false,
                className: "text-center",
            },
            {
                data: "tarif_bea_masuk",
                searchable: false,
                sortable: false,
                className: "text-center"
            },
            {
                data: "kode_fasilitas_tarif",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "tarif_fasilitas",
                className: "text-center",
                searchable: false,
                sortable: false,
            },
            {
                data: "id",
                className: "text-center actions",
                searchable: false,
                sortable: false,
                render: function(data, type, row) {
                    return `
                        <div class="text-center">
                           <button type="button" class="btn btn-warning" onclick="detailPungutan('${row.id}')" >
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="removePungutan('${row.id}')" >
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                        `;
                }
            }

        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada pungutan",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    const tableListInformasiDokumen = $('.table-list-informasi-dokumen').DataTable({

        processing: true,
        serverSide: true,
        ordering: false,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("bea-cukai-bc-23/id/barang-dokumen-all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.bc_purchase_order_id = "<?= encrypt($bcPo['id']) ?>";
                data.penerimaan_barang_id = "<?= encrypt($barangDetail['penerimaan_barang_id']) ?>";
                data.barang1_id = "<?= encrypt($barangDetail['barang1_id']) ?>";
                data.sort = "bc_dokumen.createdAt";
                data.sortType = "DESC";
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "bc_dokumen_id",
                className: "text-center",
                sortable: false,
                width: "5%",
                searchable: false,
                render: function(data, type, row) {
                    var is_used = row.is_used;
                    var seri_dokumen = row.seri_dokumen;
                    var barang_dokumen_id = row.barang_dokumen_id;
                    var bc_dokumen_id = row.bc_dokumen_id;
                    if (is_used) {
                        // delete
                        return `
                            <input name="bc_dokumen_id[]" data-seri_dokumen="${seri_dokumen}" data-bc_dokumen_id="${bc_dokumen_id}" data-barang_dokumen_id="${barang_dokumen_id}" class="child bc_dokumen_id" type="checkbox"  checked>
                        `
                    } else {
                        // create
                        return `
                        <input name="bc_dokumen_id[]" data-seri_dokumen="${seri_dokumen}" data-bc_dokumen_id="${bc_dokumen_id}" data-barang_dokumen_id="${barang_dokumen_id}" class="child bc_dokumen_id" type="checkbox" >
                        `
                    }

                }
            },
            {
                data: "seri_dokumen",
                className: "text-center"
            },
            {
                data: "nomor_dokumen",
                className: "text-center",
            },
            {
                data: "kode_dokumen",
                className: "text-center"
            },
            {
                data: "tanggal_dokumen",
                className: "text-center"
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak ada dokumen",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var validatorPungutan = $("#form-pungutan").validate({
        rules: {
            barang_detail_kode_jenis_pungutan: {
                required: true
            },
            barang_detail_kode_jenis_tarif: {
                required: true
            },
            barang_detail_nilai_tarif: {
                required: true
            },
            barang_detail_kode_fasilitas_tarif: {
                required: true
            },
            barang_detail_tarif_fasilitas: {
                required: true
            },
        },
        messages: {
            barang_detail_kode_jenis_pungutan: {
                required: "Pilih jenis pungutan"
            },
            barang_detail_kode_jenis_tarif: {
                required: "Pilih jenis tarif"
            },
            barang_detail_nilai_tarif: {
                required: "Nilai tarif wajib diisi"
            },
            barang_detail_kode_fasilitas_tarif: {
                required: "Pilih fasilitas tarif"
            },
            barang_detail_tarif_fasilitas: {
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

    var validatorBarangDokumen = $("#form-barang-dokumen").validate({
        rules: {
            barang_detail_seri_barang: {
                required: true
            },
            barang_detail_kode_hs: {
                required: true
            },
            barang_detail_kode_barang: {
                required: true
            },
            barang_detail_uraian: {
                required: true
            },
            barang_detail_merk_barang: {
                required: true
            },
            barang_detail_tipe_barang: {
                required: true
            },
            barang_detail_spesifikasi_lain: {
                required: true
            },
            barang_detail_kategori_barang: {
                required: true
            },
            barang_detail_negara: {
                required: true
            },
            barang_detail_harga: {
                required: true
            },
            barang_detail_biaya_tambahan: {
                required: true
            },
            barang_detail_fob: {
                required: true
            },
            barang_detail_harga_satuan: {
                required: true,
            },
            barang_detail_freight: {
                required: true
            },
            barang_detail_asuransi: {
                required: true
            },
            barang_detail_cif: {
                required: true
            },
            barang_detail_nilai_pabean: {
                required: true
            },
            barang_detail_jumlah_satuan: {
                required: true
            },
            barang_detail_kode_satuan_barang: {
                required: true
            },
            barang_detail_jumlah_kemasan: {
                required: true
            },
            barang_detail_kode_jenis_kemasan: {
                required: true
            },
            barang_detail_berat_bersih: {
                required: true
            },
        },
        messages: {
            barang_detail_seri_barang: {
                required: "Seri barang wajib diisi"
            },
            barang_detail_kode_hs: {
                required: "Pilih kode HS"
            },
            barang_detail_kode_barang: {
                required: "Kode barang wajib diisi"
            },
            barang_detail_uraian: {
                required: "Uraian wajib diisi"
            },
            barang_detail_merk_barang: {
                required: "Merk barang wajib diisi"
            },
            barang_detail_tipe_barang: {
                required: "Tipe Barang wajib diisi"
            },
            barang_detail_spesifikasi_lain: {
                required: "Spesifikasi lain wajib diisi"
            },
            barang_detail_kategori_barang: {
                required: "Pilih kategori barang"
            },
            barang_detail_negara: {
                required: "Pilih negara"
            },
            barang_detail_harga: {
                required: "Harga wajib diisi"
            },
            barang_detail_biaya_tambahan: {
                required: "Biaya tambahan wajib diisi"
            },
            barang_detail_fob: {
                required: "FOB wajib diisi"
            },
            barang_detail_harga_satuan: {
                required: "Harga satuan wajib diisi",
            },
            barang_detail_freight: {
                required: "Nilai Freight wajib diisi"
            },
            barang_detail_asuransi: {
                required: "Nilai Asuransi wajib diisi"
            },
            barang_detail_cif: {
                required: "Nilai CIF wajib diisi"
            },
            barang_detail_nilai_pabean: {
                required: "Nilai pabean wajib diisi"
            },
            barang_detail_jumlah_satuan: {
                required: "Jumlah satuan wajib diisi"
            },
            barang_detail_kode_satuan_barang: {
                required: "Pilih kode satuan barang"
            },
            barang_detail_jumlah_kemasan: {
                required: "Jumlah kemasan wajib diisi"
            },
            barang_detail_kode_jenis_kemasan: {
                required: "Pilih kode jenis kemasan"
            },
            barang_detail_berat_bersih: {
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


    $('.btn-submit-pungutan').click(function() {
        if ($('#form-pungutan').valid()) {
            var formData = new FormData(document.querySelector("#form-pungutan"));
            var id = $('#bc_barang_tarif_id').val();
            var url = '';
            formData.append("penerimaan_barang_id", "<?= encrypt($barangDetail['penerimaan_barang_id']) ?>");
            formData.append("barang1_id", "<?= encrypt($barangDetail['barang1_id']) ?>");
            formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
            if (id) {
                // Update
                url = "<?= base_url("bea-cukai-bc-23/id/barang-pungutan-update"); ?>";
            } else {
                // Create
                url = "<?= base_url("bea-cukai-bc-23/id/barang-pungutan-create"); ?>";

            }
            $.ajax({
                url: url,
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
                    csrf.val(response.token);
                    if (response.status) {
                        tableListInformasiPungutan.ajax.reload();
                        resetFormPungutan();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        });
                    }
                },
            });

        }
    });

    function detailPungutan(id) {
        $.ajax({
            url: "<?= base_url("bea-cukai-bc-23/id/barang-pungutan-detail"); ?>",
            data: {
                id: id,
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "GET",
            success: function(response) {
                if (response.status) {
                    $('#bc_barang_tarif_id').val(id);
                    $('#barang_detail_kode_jenis_pungutan').val(response.data.kode_jenis_pungutan).change();
                    $('#barang_detail_kode_jenis_tarif').val(response.data.kode_jenis_tarif).change();
                    $('#barang_detail_nilai_tarif').val(response.data.nilai_tarif);
                    $('#barang_detail_kode_fasilitas_tarif').val(response.data.kode_fasilitas_tarif).change();
                    $('#barang_detail_tarif_fasilitas').val(response.data.tarif_fasilitas).change();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                }
            },
        });
    }

    function resetFormPungutan() {
        $('#bc_barang_tarif_id').val(null);
        $('#barang_detail_kode_jenis_pungutan').val(null).change();
        $('#barang_detail_kode_jenis_tarif').val(null).change();
        $('#barang_detail_nilai_tarif').val('');
        $('#barang_detail_kode_fasilitas_tarif').val(null).change();
        $('#barang_detail_tarif_fasilitas').val('100');
    }

    $('.btn-simpan-detail-barang-form-view').click(function() {
        if ($('#form-barang-dokumen').valid()) {
            var formData = new FormData(document.querySelector("#form-barang-dokumen"));
            var barangDetailHarga = destroyFormatRupiah($('#barang_detail_harga').val());
            var barangDetailBiayaTambahan = destroyFormatRupiah($('#barang_detail_biaya_tambahan').val());
            var barangDetailFob = destroyFormatRupiah($('#barang_detail_fob').val());
            var barangDetailHargaSatuan = destroyFormatRupiah($('#barang_detail_harga_satuan').val());
            var barangDetailFreight = destroyFormatRupiah($('#barang_detail_freight').val());
            var barangDetailAsuransi = destroyFormatRupiah($('#barang_detail_asuransi').val());
            var barangDetailCif = destroyFormatRupiah($('#barang_detail_cif').val());
            var barangDetailNilaiPabean = destroyFormatRupiah($('#barang_detail_nilai_pabean').val());
            var barangDetailJumlahSatuan = destroyFormatRupiah($('#barang_detail_jumlah_satuan').val());
            var barangDetailJumlahKemasan = destroyFormatRupiah($('#barang_detail_jumlah_kemasan').val());
            var barangDetailBeratBersih = destroyFormatRupiah($('#barang_detail_berat_bersih').val());

            formData.set('barang_detail_harga', barangDetailHarga);
            formData.set('barang_detail_biaya_tambahan', barangDetailBiayaTambahan);
            formData.set('barang_detail_fob', barangDetailFob);
            formData.set('barang_detail_harga_satuan', barangDetailHargaSatuan);
            formData.set('barang_detail_freight', barangDetailFreight);
            formData.set('barang_detail_asuransi', barangDetailAsuransi);
            formData.set('barang_detail_cif', barangDetailCif);
            formData.set('barang_detail_nilai_pabean', barangDetailNilaiPabean);
            formData.set('barang_detail_jumlah_satuan', barangDetailJumlahSatuan);
            formData.set('barang_detail_jumlah_kemasan', barangDetailJumlahKemasan);
            formData.set('barang_detail_berat_bersih', barangDetailBeratBersih);

            formData.append("penerimaan_barang_id", "<?= encrypt($barangDetail['penerimaan_barang_id']) ?>");
            formData.append("barang1_id", "<?= encrypt($barangDetail['barang1_id']) ?>");
            formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");

            $.ajax({
                url: "<?= base_url("bea-cukai-bc-23/id/barang"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    $('#btn-loading').show();
                    $('.btn-simpan-detail-barang-form-view').hide();
                },
                complete: function() {
                    $('#btn-loading').hide();
                    $('.btn-simpan-detail-barang-form-view').show();
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    location.reload();
                },
            });
        }
    })

    $(document).on('click', '.bc_dokumen_id', function() {
        var checkbox = $(this);
        var isChecked = checkbox.prop('checked');
        var seriDokumen = checkbox.data('seri_dokumen');
        var bc23DokumenID = checkbox.data('bc_dokumen_id');
        var barangDokumenID = checkbox.data('barang_dokumen_id');

        if (isChecked) {
            // Tambah
            var formData = new FormData();
            formData.append("penerimaan_barang_id", "<?= encrypt($barangDetail['penerimaan_barang_id']) ?>");
            formData.append("barang1_id", "<?= encrypt($barangDetail['barang1_id']) ?>");
            formData.append("bc_purchase_order_id", "<?= encrypt($bcPo['id']) ?>");
            formData.append("bc_dokumen_id", bc23DokumenID);
            formData.append("seri_dokumen", seriDokumen);
            $.ajax({
                url: "<?= base_url("bea-cukai-bc-23/id/barang-dokumen-create"); ?>",
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
                        csrf.val(response.token);
                        tableListInformasiDokumen.ajax.reload();
                    }
                },
            });

        } else {
            // Hapus
            var formData = new FormData();
            formData.append("id", barangDokumenID);
            $.ajax({
                url: "<?= base_url("bea-cukai-bc-23/id/barang-dokumen-delete"); ?>",
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
                        csrf.val(response.token);
                        tableListInformasiDokumen.ajax.reload();

                    }
                },
            });
        }
    });


    function removePungutan(id) {
        const csrf = $(`[name="${csrfToken}"]`);
        $.ajax({
            url: "<?= base_url("bea-cukai-bc-23/id/barang-pungutan-delete"); ?>",
            data: {
                id: id
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            method: "POST",
            dataType: "json",
            success: function(response) {
                csrf.val(response.token);
                if (response.status) {
                    tableListInformasiPungutan.ajax.reload();
                }
            },
        });
    }
</script>


<?= $this->endSection(); ?>