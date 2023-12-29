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
    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <div class="row">
                    <div class="col-sm-4">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Importir/Pengusaha TPB
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_npwp_importir" value="<?= $npwpDefault['value'] ?>" name="entitas_npwp_importir" type="text" class="form-control entitas_npwp_importir" placeholder="">
                                <label>NPWP</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_nama_importir" value="<?= $namaImportirDefault['value'] ?>" name="entitas_nama_importir" type="text" class="form-control entitas_nama_importir" placeholder="">
                                <label>Nama</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <textarea name="entitas_alamat_importir" id="entitas_alamat_importir" class="form-control entitas_alamat_importir" style="height: 100px;"><?= "\n\n" . $alamatImportirDefault['value'] ?></textarea>
                                <label>Alamat</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_nomor_ijin_tpb" name="entitas_nomor_ijin_tpb" type="text" class="form-control entitas_nomor_ijin_tpb" placeholder="">
                                <label>Nomor Ijin TPB</label>
                            </div>
                        </div>
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" name="entitas_tanggal_skep_tpb" type="text" placeholder="" class="form-control entitas_tanggal_skep_tpb" id="entitas_tanggal_skep_tpb">
                                <label>Tanggal Skep TPB</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input id="entitas_nib" value="<?= $nibDefault['value'] ?>" name="entitas_nib" type="text" class="form-control entitas_nib" placeholder="">
                            <label>NIB</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Pemasok
                        </label>
                        <div class="mt-1">
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <input id="entitas_nama_pemasok" value="" name="entitas_nama_pemasok" type="text" class="form-control entitas_nama_pemasok" placeholder="">
                                    <label>Nama</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3">
                                    <textarea name="entitas_alamat_pemasok" id="entitas_alamat_pemasok" class="form-control entitas_alamat_pemasok" style="height: 100px;"></textarea>
                                    <label>Alamat</label>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select entitas_negara" id="entitas_negara" name="entitas_negara" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <?php foreach ($kodeNegaraAsal as $k) : ?>
                                            <option value="<?= encrypt($k['code']) ?>">
                                                <?= $k['code'] . " - " . strtoupper($k['country_name']) . "" ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label style="z-index: 1;">Negara</label>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="col-sm-4">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Pemilik Barang
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_npwp_pemilik_barang" value="" name="entitas_npwp_pemilik_barang" type="text" class="form-control entitas_npwp_pemilik_barang" placeholder="">
                                <label>NPWP</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <input id="entitas_nama_pemilik_barang" value="" name="entitas_nama_pemilik_barang" type="text" class="form-control entitas_nama_pemilik_barang" placeholder="">
                                <label>Nama</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3">
                                <textarea name="entitas_alamat_pemilik_barang" id="entitas_alamat_pemilik_barang" class="form-control entitas_alamat_pemilik_barang" style="height: 100px;"></textarea>
                                <label>Alamat</label>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn btn-primary" style="float: right;">
                    Simpan Perubahan
                </a>
            </div>
        </div>
    </div>

</section>

<script>
    $('#entitas_negara').select2({
        placeholder: "Pilih Negara",
        theme: "bootstrap-5",
    });

    $("#entitas_tanggal_skep_tpb").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
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