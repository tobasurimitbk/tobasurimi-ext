<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah Dokumen BC 2.3</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table width="100%" class="mb-3">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px"><b>Status</b></td>
                        <td width="10px">:</td>
                        <td>-</td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px"><b>Status Perbaikan</b></td>
                        <td width="30px">:</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>

            <label class="form-label font-weight-bold lable-title mt-3">
                Informasi Dokumen
            </label>
            <div class="row mt-2">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                        <label for="floatingInput">Nomor AJU</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                        <label for="floatingInput">Nomor Daftar</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                        <label for="floatingInput">Tanggal Daftar</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Informasi Tempat
            </label>
            <div class="row mt-2">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select kppbcBongkar" name="kppbcBongkar" id="kppbcBongkar" aria-label="Floating label select example">
                            <option value="">
                                - Kantor KPPBC Bongkar -
                            </option>
                            <?php foreach ($kantorBeaCukai as $k) : ?>
                                <option value="<?= $k->id ?>">
                                    - (<?= $k->kode ?>) <?= $k->kantor_name ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">KPPBC Bongkar</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select kppbcPengawas" name="kppbcPengawas" id="kppbcPengawas" aria-label="Floating label select example">
                            <option value="">
                                - Kantor KPPBC Pengawas -
                            </option>
                            <?php foreach ($kantorBeaCukai as $k) : ?>
                                <option value="<?= $k->id ?>">
                                    - (<?= $k->kode ?>) <?= $k->kantor_name ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">KPPBC Pengawas</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">

                        <select class="form-select kodeTujuanTpb" name="kodeTujuanTpb" id="kodeTujuanTpb" aria-label="Floating label select example">
                            <option value="">
                                - PILIH JENIS TPB -
                            </option>
                            <?php foreach (\App\Constant\BeaCukai::DokumenTPB as $d) : ?>
                                <option value="<?= $d['kodeJenisTPB'] ?>">
                                    - <?= $d['namaJenisTPB'] ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Pilih Jenis TPB</label>
                    </div>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Supplier
            </label>
            <div class="row mt-2">
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select namaSupplier" name="namaSupplier" id="namaSupplier" aria-label="Floating label select example">
                            <option value="">
                                - Pilih Supplier -
                            </option>
                            <?php foreach ($supplier as $s) : ?>
                                <option value="<?= $s->id ?>">
                                    - (<?= $s->kode ?>) <?= $k->kantor_name ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">KPPBC Bongkar</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select kppbcPengawas" name="kppbcPengawas" id="kppbcPengawas" aria-label="Floating label select example">
                            <option value="">
                                - Kantor KPPBC Pengawas -
                            </option>
                            <?php foreach ($kantorBeaCukai as $k) : ?>
                                <option value="<?= $k->id ?>">
                                    - (<?= $k->kode ?>) <?= $k->kantor_name ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">KPPBC Pengawas</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-1">
                    <div class="form-floating mb-3" style="height: 50px;">

                        <select class="form-select kodeTujuanTpb" name="kodeTujuanTpb" id="kodeTujuanTpb" aria-label="Floating label select example">
                            <option value="">
                                - PILIH JENIS TPB -
                            </option>
                            <?php foreach (\App\Constant\BeaCukai::DokumenTPB as $d) : ?>
                                <option value="<?= $d['kodeJenisTPB'] ?>">
                                    - <?= $d['namaJenisTPB'] ?> -
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Pilih Jenis TPB</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $('#kppbcBongkar').select2({
        placeholder: "Pilih Kantor Bongkar",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kppbcPengawas').select2({
        placeholder: "Pilih Kantor Pengawas",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#kodeTujuanTpb').select2({
        placeholder: "Pilih Kode Tujuan TPB",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1')
</script>
<?= $this->endSection(); ?>