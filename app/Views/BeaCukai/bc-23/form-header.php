<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <div class="section-header">
        <h1 class="title-name">Dokumen BC 2.3</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>


            <button class="btn btn-show-form btn-save float-right btn-submit-parent root-form-view btn-submit-root-form-view">
                Simpan
            </button>

        </div>
    </div>

    <div class="root-form-view">
        <div class="card">
            <div class="card-header" style="font-weight: bold; color:black;">
                BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
            </div>
            <div class="card-body">
                <?php include_once('nav.php') ?>
                <div class="row">
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Pengajuan
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled id="header_no_pengajuan" value="<?= $noAju ?>" name="header_no_pengajuan" type="text" readonly class="header_no_pengajuan form-control" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Kantor Pabean
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input id="header_pelabuhan_bongkar" name="header_pelabuhan_bongkar" type="text" class="header_pelabuhan_bongkar form-control" placeholder="">
                                <label>Kode Pelabuhan Bongkar</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select header_kantor_pabean_bongkar" id="header_kantor_pabean_bongkar" name="header_kantor_pabean_bongkar" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKantor as $k) : ?>
                                        <option <?= !empty($bc23Detail) ? (encrypt($bc23Detail['kode_kantor_bongkar']) == encrypt($k['kode']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['kode']) ?>">
                                            <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Kantor Bongkar</label>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select header_kantor_pabean_pengawas" disabled id="header_kantor_pabean_pengawas" name="header_kantor_pabean_pengawas" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeKantor as $k) : ?>
                                        <option <?= !empty($selectedKantor) ? (encrypt($selectedKantor['value']) == encrypt($k['kode']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['kode']) ?>">
                                            <?= strtoupper($k['kode']) . " - " . strtoupper($k['kantor_name']) . " " ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kantor Pabean Pengawas</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                            Keterangan Lain
                        </label>
                        <div class="mt-1">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select <?= !empty($bc23Detail) ? ($bc23Detail['status_posting'] == "Sudah Posting" ? 'disabled' : '') : '' ?> class="form-select header_kode_tujuan_tpb" id="header_kode_tujuan_tpb" name="header_kode_tujuan_tpb" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($kodeTujunTpb as $k) : ?>
                                        <option <?= !empty($bc23Detail) ? (encrypt($bc23Detail['kode_tujuan_tpb']) == encrypt($k['description']) ? 'selected' : '') : '' ?> value="<?= encrypt($k['description']) ?>">
                                            <?= strtoupper($k['description']) . " - " . strtoupper($k['value']) . " " ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label style="z-index: 1;">Pilih Kode Tujuan TPB</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<script>
    $('#header_kantor_pabean_bongkar').select2({
        placeholder: "Pilih Kode Kantor Bongkar",
        theme: "bootstrap-5",
    });

    $('#header_kode_tujuan_tpb').select2({
        placeholder: "Pilih Kode Tujuan TPB",
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