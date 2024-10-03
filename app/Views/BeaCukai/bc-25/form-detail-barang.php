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
            BC 2.5 - PEMBERITAHUAN IMPOR BARANG DARI TEMPAT PENIMBUNAN BERIKAT
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
                    <a class="btn btn-danger float-right" href="<?= base_url('bea-cukai-bc-25/id/barang/' . encrypt($bc25['id'])) ?>" style="margin-right: 8px;">
                        Batal
                    </a>
                </div>

                <table width="100%" class="mb-3">
                    <tbody>
                        <tr style="color: black;">
                            <td width="150px"><b>Kode Barang</b></td>
                            <td width="10px">:</td>
                            <td><?= $barang['barangDetail']['kode_barang'] ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Nama Barang</b></td>
                            <td width="10px">:</td>
                            <td><?= $barang['barangDetail']['barang_master_name'] ?></td>
                        </tr>
                    </tbody>
                </table>
                <hr style="color: black;">

                <form id="form-barang-detail">
                    <div class="row">
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mb-3">
                                Jenis
                            </label>
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
                                            <option <?= $barang['bcDetail']->posTarif == $k['code'] ? 'selected' : '' ?> value="<?= $k['code'] ?>">
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
                                <div class="form-floating mb-3">
                                    <textarea id="uraian" name="uraian" type="text" class="form-control uraian" placeholder="" style="height: 100px;"><?= $barang['bcDetail']->uraian ?></textarea>
                                    <label>Uraian</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="merk" name="merk" type="text" class="form-control merk" value="<?= $barang['bcDetail']->merk == ""  ? '-' : $barang['bcDetail']->merk ?>" placeholder="">
                                    <label>Merk Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="tipe" name="tipe" type="text" class="form-control tipe" value="<?= $barang['bcDetail']->tipe == ""  ? '-' : $barang['bcDetail']->tipe ?>" placeholder="">
                                    <label>Tipe Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="spesifikasiLain" name="spesifikasiLain" type="text" class="form-control spesifikasiLain" value="<?= $barang['bcDetail']->spesifikasiLain == ""  ? '-' : $barang['bcDetail']->spesifikasiLain ?>" placeholder="">
                                    <label>Spesifikasi Lain</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mb-3">
                                Keterangan Lainnya
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeGunaBarang" id="kodeGunaBarang" name="kodeGunaBarang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeGunaBarang as $k) : ?>
                                            <option <?= $barang['bcDetail']->kodeGunaBarang == $k['value'] ? 'selected' : '' ?> value="<?= $k['value'] ?>">
                                                <?= strtoupper($k['value']) . " - " . $k['description'] . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Penggunaan</label>
                                </div>
                            </div>

                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeKategoriBarang" id="kodeKategoriBarang" name="kodeKategoriBarang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKategoriBarang as $k) : ?>
                                            <option <?= $barang['bcDetail']->kodeKategoriBarang == json_decode($k['description'])[1] ? 'selected' : '' ?> value="<?= json_decode($k['description'])[1] ?>">
                                                <?= json_decode($k['description'])[1] . " - " . $k['value'] . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kategori Barang</label>
                                </div>
                            </div>

                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodeKondisiBarang" id="kodeKondisiBarang" name="kodeKondisiBarang" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeKondisiBarang as $k) : ?>
                                            <option <?= $barang['bcDetail']->kodeKondisiBarang == $k['value'] ? 'selected' : '' ?> value="<?= $k['value'] ?>">
                                                <?= $k['value'] . " - " . $k['description'] . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Kondisi Barang</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <label class="form-label font-weight-bold lable-title mb-2">Jangka Waktu</label>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" <?= $barang['bcDetail']->flag4tahun == "Y" ? 'checked' : '' ?> name="flag4tahun" value="Y" type="checkbox" id="flag4tahun">
                                        <label class="form-check-label" for="flag4tahun">
                                            > 4 Tahun
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select kodePerhitungan" id="kodePerhitungan" name="kodePerhitungan" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodePerhitungan as $k) : ?>
                                            <option <?= $barang['bcDetail']->kodePerhitungan == $k['value'] ? 'selected' : '' ?> value="<?= $k['value'] ?>">
                                                <?= $k['value'] . " - " . $k['description'] . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Cara Perhitungan</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-1">
                            <label class="form-label font-weight-bold lable-title mb-3">
                                Jumlah & Berat
                            </label>
                            <div class="mt-1">
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-floating mb-3">
                                            <input id="jumlahSatuan" name="jumlahSatuan" type="text" class="form-control jumlahSatuan" value="<?= $barang['bcDetail']->jumlahSatuan ?>" readonly placeholder="">
                                            <label>Jumlah Satuan</label>
                                        </div>
                                    </div>
                                    <div class="col-sm">
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
                                    <div class="col-sm">
                                        <div class="form-floating mb-3">
                                            <input id="jumlahKemasan" name="jumlahKemasan" type="number" class="form-control jumlahKemasan" value="<?= $barang['bcDetail']->jumlahKemasan ?>" placeholder="">
                                            <label>Jumlah Kemasan</label>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <select class="form-select kodeJenisKemasan" id="kodeJenisKemasan" name="kodeJenisKemasan" aria-label="Floating label select example">
                                                <option value=""></option>
                                                <?php foreach ($kodeJenisKemasan as $k) : ?>
                                                    <option <?= $barang['bcDetail']->kodeJenisKemasan == $k['description'] ? 'selected' : '' ?> value="<?= $k['description'] ?>">
                                                        <?= $k['description'] . " - " . strtoupper($k['value']) . "" ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label style="z-index: 1;">Kode Jenis Kemasan</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="netto" name="netto" type="text" class="form-control netto" value="<?= $barang['bcDetail']->netto ?>" placeholder="">
                                    <label>Berat Bersih</label>
                                </div>
                            </div>
                            <label class="form-label font-weight-bold lable-title mb-3">
                                Harga
                            </label>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input readonly id="cif" name="cif" type="text" class="form-control cif" value="<?= formatRupiah($barang['bcDetail']->cif) ?>" placeholder="">
                                    <label>Nilai CIF</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input readonly id="hargaEkspor" name="hargaEkspor" type="text" class="form-control hargaEkspor" value="<?= formatRupiah($barang['bcDetail']->cif ?? 0 * $barang['bcDetail']->ndpbm ?? 0) ?>" placeholder="">
                                    <label>Nilai Pabean</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input id="hargaPenyerahan" name="hargaPenyerahan" onchange="this.value = formatRupiah(this.value)" type="text" class="form-control hargaPenyerahan" value="<?= formatRupiah($barang['bcDetail']->hargaPenyerahan ?? 0) ?>" placeholder="">
                                    <label>Harga Penyerahan / Harga Jual</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Dokumen
                        </label>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" width="100%" cellspacing="0">
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
                                    <?php if (count($dokumen) == 0) : ?>
                                        <tr style="color: white; text-align:center;">
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
                    </div>
                    <div class="col-sm-6 mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3">
                            Pungutan
                        </label>
                        <div class="alert alert-secondary alert-dismissible fade show mt-3 text-black" role="alert">
                            Tarif Seri Pertama Wajib <b>BM (Bea Masuk)</b>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="form-pungutan">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select kodeJenisPungutan" id="kodeJenisPungutan" name="kodeJenisPungutan" aria-label="Floating label select example">
                                            <option value=""></option>
                                            <?php foreach ($kodeJenisPungutan as $k) : ?>
                                                <option value="<?= $k['value'] ?>">
                                                    <?= $k['value'] . " - " . strtoupper($k['description']) . "" ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label style="z-index: 1;">Kode Jenis Pungutan</label>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select kodeJenisTarif" id="kodeJenisTarif" name="kodeJenisTarif" aria-label="Floating label select example">
                                            <option value=""></option>
                                            <?php foreach ($kodeJenisTarif as $k) : ?>
                                                <option value="<?= $k['value'] ?>">
                                                    <?= $k['value'] . " - " . $k['description'] . "" ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label style="z-index: 1;">Kode Jenis Tarif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="form-floating mb-3">
                                        <input id="tarif" name="tarif" type="number" class="form-control tarif" value="" placeholder="">
                                        <label>Tarif (%)</label>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select kodeFasilitasTarif" id="kodeFasilitasTarif" name="kodeFasilitasTarif" aria-label="Floating label select example">
                                            <option value=""></option>
                                            <?php foreach ($kodeFasilitasTarif as $k) : ?>
                                                <option value="<?= $k['value'] ?>">
                                                    <?= $k['value'] . " - " . $k['description'] . "" ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label style="z-index: 1;">Kode Fasilitas Tarif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="form-floating mb-3">
                                        <input id="tarifFasilitas" name="tarifFasilitas" type="number" class="form-control tarifFasilitas" value="" placeholder="">
                                        <label>Tarif Fasilitas (%)</label>
                                    </div>
                                </div>
                                <div class="col-sm"></div>

                            </div>

                        </form>
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="row" style="float: right; margin-bottom:5px;">
                                    <div class="col-sm" style="margin-right: -20px;">
                                        <button type="button" class="btn btn-add btn-block float-right btn-submit-pungutan" id="btn-submit-pungutan" style="float: right;">
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
                                    <?php if (count($pungutan) == 0) : ?>
                                        <tr style="color: white; text-align:center;">
                                            <td colspan="7">Tidak ada pungutan</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($pungutan as $i => $b) : ?>
                                            <tr style="color: white; text-align:center;">
                                                <td><?= $no++; ?></td>
                                                <td><?= $b['kodeJenisPungutanText']; ?></td>
                                                <td><?= $b['kodeJenisTarifText']; ?></td>
                                                <td><?= $b['tarif']; ?></td>
                                                <td><?= $b['kodeFasilitasTarifText']; ?></td>
                                                <td><?= $b['tarifFasilitas']; ?></td>
                                                <td>
                                                    <?php if (count($barang['bcDetail']->barangTarif) - 1 == $i) : ?>
                                                        <button type="button" class="btn btn-danger" onclick="removePungutan('<?= $b['kodeJenisPungutan'] ?>')"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3 mt-3">
                            Bahan Baku Impor
                        </label>

                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="row" style="float: right; margin-bottom:5px;">
                                    <div class="col-sm" style="margin-right: -20px;">
                                        <button type="button" class="btn btn-add btn-block float-right btn-open-modal" data-tipe="IMPORT" style="float: right;">
                                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pungutan" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:10px;">Seri</th>
                                    <th style="text-align: center;">Uraian</th>
                                    <th style="text-align: center;">Nilai Barang</th>
                                    <th style="text-align: center;">Kode Satuan</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tbody>
                                <?php if (count($bahanBakuImpor) == 0) : ?>
                                    <tr style="color: white; text-align:center;">
                                        <td colspan="5">Tidak ada bahan baku impor</td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($bahanBakuImpor as $b) : ?>
                                        <tr style="color: white; text-align:center;">
                                            <td><?= $no++; ?></td>
                                            <td><?= $b['uraianBarang']; ?></td>
                                            <td><?= number_format($b['hargaPenyerahan'], 2); ?></td>
                                            <td><?= $b['kodeSatuanBarang']; ?></td>
                                            <td>
                                                <?php if (count($bahanBakuImpor) - 1 == $i) : ?>
                                                    <button type="button" class="btn btn-danger" onclick="removeBahanBaku('<?= $b['indexDelete'] ?>')"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm mt-1">
                        <label class="form-label font-weight-bold lable-title mb-3 mt-3">
                            Bahan Baku Lokal
                        </label>

                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="row" style="float: right; margin-bottom:5px;">
                                    <div class="col-sm" style="margin-right: -20px;">
                                        <button type="button" class="btn btn-add btn-block float-right btn-open-modal" data-tipe="LOKAL" style="float: right;">
                                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pungutan" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center; width:10px;">Seri</th>
                                    <th style="text-align: center;">HS</th>
                                    <th style="text-align: center;">Uraian</th>
                                    <th style="text-align: center;">Nilai Barang</th>
                                    <th style="text-align: center;">Kode Satuan</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($bahanBakuLokal) == 0) : ?>
                                    <tr style="color: white; text-align:center;">
                                        <td colspan="5">Tidak ada bahan baku lokal</td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($bahanBakuLokal as $b) : ?>
                                        <tr style="color: white; text-align:center;">
                                            <td><?= $no++; ?></td>
                                            <td><?= $b['posTarif']; ?></td>
                                            <td><?= $b['uraianBarang']; ?></td>
                                            <td><?= number_format($b['hargaPenyerahan'], 2); ?></td>
                                            <td><?= $b['kodeSatuanBarang']; ?></td>
                                            <td>
                                                <?php if (count($bahanBakuLokal) - 1 == $i) : ?>
                                                    <button type="button" class="btn btn-danger" onclick="removeBahanBaku('<?= $b['indexDelete'] ?>')"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                                                <?php endif; ?>
                                            </td>
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
</section>
<div class="modal fade" id="modalBahanBaku" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
            </div>
            <form id="form-create-bahan-baku">
                <input type="hidden" name="tipe" id="tipe" class="tipe">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 mt-1">
                            <div class="form-floating mb-2" style="height: 50px;">
                                <select class="form-select search_no_aju_daftar" id="search_no_aju_daftar" name="search_no_aju_daftar" aria-label="Floating label select example">
                                    <option value=""></option>

                                </select>
                                <label style="z-index: 1;">Cari Berdasarkan Nomor Aju / Nomor Daftar</label>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-informasi-pungutan" id="dataTable1" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px;">#</th>
                                <th style="text-align: center; width:10px;">Seri</th>
                                <th style="text-align: center;">HS</th>
                                <th style="text-align: center;">Uraian</th>
                                <th style="text-align: center;">Harga</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: center;">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" style="text-align:center;">Tidak ada barang</td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-discard mr-3 btn-discard-modal" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-submit-form" id="btn-simpan-bahan-baku">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>


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
            kodeGunaBarang: {
                required: true
            },
            kodeKategoriBarang: {
                required: true
            },
            kodeKondisiBarang: {
                required: true
            },
            kodePerhitungan: {
                required: true
            },
            kodeSatuanBarang: {
                required: true
            },
            jumlahKemasan: {
                required: true
            },
            kodeJenisKemasan: {
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
            kodeGunaBarang: {
                required: "Kode penggunaan barang wajib diisi"
            },
            kodeKategoriBarang: {
                required: "Kode kategori barang wajib diisi"
            },
            kodeKondisiBarang: {
                required: "Kode kondisi barang wajib diisi"
            },
            kodePerhitungan: {
                required: "Kode perhitungan wajib diisi"
            },
            kodeSatuanBarang: {
                required: "Kode satuan barang wajib diisi"
            },
            jumlahKemasan: {
                required: "Jumlah kemasan wajib diisi"
            },
            kodeJenisKemasan: {
                required: "Kode jenis kemasan wajib diisi"
            },
            netto: {
                required: "Netto wajib diisi"
            },
            cif: {
                required: "Cif wajib diisi"
            },
            hargaEkspor: {
                required: "Nilai pabean wajib diisi"
            },
            hargaPenyerahan: {
                required: "Nilai penyerahan wajib diisi"
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

    $('#btn-simpan-bahan-baku').click(function() {
        if ($('#form-create-bahan-baku').valid()) {

            var checkedCheckboxes = $(".child:checked");
            var dataSeriBarang = checkedCheckboxes.map(function() {
                return $(this).data("seri_barang");
            }).get();
            var indexSplice = []; // YANG DIHAPUS

            if (dataSeriBarang.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Checklist minimal satu barang !",
                    confirmButtonColor: '#4e73df',
                    confirmButtonText: 'Ok'
                });
            } else {
                $.each(bahanBakuBCList.barang, function(i, v) {
                    if ($.inArray(v.seriBarang, dataSeriBarang) === 1) {
                        indexSplice.push(i); // INDEX YANG DI SPLICE
                    }
                });

                $.each(indexSplice, function(i, v) {
                    // SPLICE BARANG
                    bahanBakuBCList.barang.splice(v);
                });

                // SUBMIT KE SERVER
                Swal.fire({
                    icon: 'question',
                    title: "Simpan asal bahan baku ?",
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = new FormData(document.querySelector("#form-create-bahan-baku"));
                        formData.append("id", "<?= encrypt($bc25['id']) ?>");
                        formData.append("seriBarang", "<?= $barang['bcDetail']->seriBarang ?>");
                        formData.append("bcPurchaseOrderId", $('#search_no_aju_daftar option:selected').val());
                        formData.append("bahanBakuBCList", JSON.stringify(bahanBakuBCList));

                        $.ajax({
                            url: "<?= base_url("bea-cukai-bc-25/id/barang/bahan-baku-create"); ?>",
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
                })
            }

        }
    });

    $('#btn-submit-pungutan').click(function() {
        if ($('#form-pungutan').valid()) {
            Swal.fire({
                icon: 'question',
                title: "Simpan pungutan ?",
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData(document.querySelector("#form-pungutan"));
                    formData.append("id", "<?= encrypt($bc25['id']) ?>");
                    formData.append("seriBarang", "<?= $barang['bcDetail']->seriBarang ?>");

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-25/id/barang/pungutan-create"); ?>",
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
            })
        }
    })

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
                    formData.append("id", "<?= encrypt($bc25['id']) ?>");

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-25/id/barang"); ?>",
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
                    formData.append("id", "<?= encrypt($bc25['id']) ?>");
                    formData.append("seriBarang", "<?= $barang['bcDetail']->seriBarang ?>");
                    formData.append("seriDokumen", seriDokumen);

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-25/id/barang/dokumen-create"); ?>",
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
                    formData.append("id", "<?= encrypt($bc25['id']) ?>");
                    formData.append("seriBarang", "<?= $barang['bcDetail']->seriBarang ?>");
                    formData.append("seriDokumen", seriDokumen);

                    $.ajax({
                        url: "<?= base_url("bea-cukai-bc-25/id/barang/dokumen-delete"); ?>",
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

    function removePungutan(kodeJenisPungutan) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Pungutan ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai-bc-25/id/barang/pungutan-delete"); ?>",
                    data: {
                        id: "<?= encrypt($bc25['id']) ?>",
                        seriBarang: "<?= $barang['bcDetail']->seriBarang ?>",
                        kodeJenisPungutan: kodeJenisPungutan
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
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    },
                });
            }
        })
    }

    function removeBahanBaku(indexDelete) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Bahan Baku ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("bea-cukai-bc-25/id/barang/bahan-baku-delete"); ?>",
                    data: {
                        id: "<?= encrypt($bc25['id']) ?>",
                        seriBarang: "<?= $barang['bcDetail']->seriBarang ?>",
                        indexDelete: indexDelete
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
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    },
                });
            }
        })

    }

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function drawTable(bahanBakuBCList) {
        const table = $('#dataTable1');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (bahanBakuBCList.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="7" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
            if (bahanBakuBCList.barang.length == 0) {
                var newRow = $('<tr>');
                newRow.append($('<td colspan="7" style="text-align:center">Tidak Ada Barang</td>'));
                table.find('tfoot').append(newRow);
            } else {
                var no = 1;
                $.each(bahanBakuBCList.barang, function(i, v) {
                    var newRow = $('<tr style="color:whitesmoke;">');
                    newRow.append($('<td style="text-align:center;">').html(`
                     <input name="seri_barang" data-seri_barang="${v.seriBarang}" class="child seri_barang" type="checkbox" value="${v.seriBarang}">
                `));

                    newRow.append($('<td style="text-align:center;">').text(v.seriBarang));
                    newRow.append($('<td style="text-align:center;">').text(v.posTarif));
                    newRow.append($('<td style="text-align:center;">').text(v.uraian));
                    newRow.append($('<td style="text-align:center;">').text(formatRupiah(v.hargaPenyerahan)));
                    newRow.append($('<td style="text-align:center;">').text(formatRupiah(v.jumlahSatuan)));
                    newRow.append($('<td style="text-align:center;">').text(v.kodeSatuanBarang));

                    table.find('tbody').append(newRow);
                });
            }

        }
    }

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