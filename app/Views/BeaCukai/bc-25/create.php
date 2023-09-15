<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah Dokumen BC 2.5</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("bea-cukai-bc-25"); ?>">
                Batal
            </a>
            <?php if(!empty($dataBC)){ 
                if($dataBC->status_posting === "Belum Posting"){ 
            ?> 
            <button class="btn btn-hapus delete-parent float-right">
                Hapus
            </button>
            <button class="btn btn-success posting-spp float-right">
                Posting
            </button>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan 
            </button>
            <?php }
            } else { ?> 
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan 
            </button>
            <?php } ?> 
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-bc" role="form" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input value="<?= (!empty($dataBC)) ? $dataBC->id : '' ?>" autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                <table width="100%" class="mb-3">
                    <tbody>
                        <tr style="color: black;">
                            <td width="150px"><b>Status</b></td>
                            <td width="10px">:</td>
                            <td><?= (!empty($dataBC)) ? $dataBC->status : '-' ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Status Perbaikan</b></td>
                            <td width="30px">:</td>
                            <td><?= (!empty($dataBC)) ? $dataBC->status_perbaikan : '-' ?></td>
                        </tr>
                    </tbody>
                </table>

                <label class="form-label font-weight-bold lable-title mt-3">
                    Informasi Dokumen
                </label>
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($dataBC)) ? $dataBC->aju_no : '-' ?>" readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                            <label for="floatingInput">Nomor AJU</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($dataBC)) ? $dataBC->registration_no : '-' ?>" readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                            <label for="floatingInput">Nomor Daftar</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= (!empty($dataBC)) ? $dataBC->registration_date : '-' ?>" readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker" value="-">
                            <label for="floatingInput">Tanggal Daftar</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Informasi Tempat
                </label>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select kantorPabean" name="kantorPabean" id="kantorPabean" aria-label="Floating label select example">
                                <option value="">
                                    - Kantor Pabean -
                                </option>
                                <?php foreach ($kantorBeaCukai as $k) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->kantor_pabean === $k->id ? 'selected' : '') : '' ?> value="<?= $k->id ?>">
                                        - (<?= $k->kode ?>) <?= $k->kantor_name ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Kantor Pabean</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select kodeTujuanTpb" name="kodeTujuanTpb" id="kodeTujuanTpb" aria-label="Floating label select example">
                                <option value="">
                                    - PILIH JENIS TPB -
                                </option>
                                <?php foreach ($jenisTPB as $d) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->tujuan_tpb === $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        - <?= $d['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis TPB</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Pengusaha TPB
                </label>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_npwp : '' ?>" autocomplete="one-time-code" name="npwpImportir" type="text" placeholder="Identitas (NPWP)" class="form-control target input-picker">
                            <label for="floatingInput">Identitas (NPWP)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_name : '' ?>" autocomplete="one-time-code" name="namaImportir" type="text" placeholder="Nama Pengusaha" class="form-control target input-picker">
                            <label for="floatingInput">Nama Pengusaha</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->tpb_no : '' ?>" autocomplete="one-time-code" name="noIzinTPBImportir" type="text" placeholder="No Izin TPB" class="form-control target input-picker">
                            <label for="floatingInput">No Izin TPB</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_api : '' ?>" autocomplete="one-time-code" name="APIImportir" type="text" placeholder="API" class="form-control target input-picker">
                            <label for="floatingInput">API</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> name="alamatImportir" class="form-control text-area-all"><?= (!empty($dataBC)) ? $dataBC->importir_address : '' ?></textarea>
                            <label for="floatingInput">Alamat</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Pemilik Barang
                </label><br>
                <label class="mt-2">
                    Sama dengan data pengusaha
                </label>
                <div class="form-control border-0 custom-toggle-switch">
                    <div class="form-check form-switch form-switch-lg">
                        <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang ? 'checked' : '') : '' ?> class="form-check-input" type="checkbox" name="switchPemilikBarang" id="switchPemilikBarang">
                        <label class="form-check-label" for="switchPemilikBarang"></label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang || $dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->pemilik_barang_npwp : '' ?>" autocomplete="one-time-code" name="npwpPemilikBarang" type="text" placeholder="Identitas (NPWP)" class="npwpPemilikBarang form-control target input-picker">
                            <label for="floatingInput">Identitas (NPWP)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang || $dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->pemilik_barang_name : '' ?>" autocomplete="one-time-code" name="namaPemilikBarang" type="text" placeholder="Nama Importir" class="namaPemilikBarang form-control target input-picker">
                            <label for="floatingInput">Nama Pemilik Barang</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang || $dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> name="alamatPemilikBarang" class="alamatPemilikBarang form-control text-area-all"><?= (!empty($dataBC)) ? $dataBC->pemilik_barang_address : '' ?></textarea>
                            <label for="floatingInput">Alamat</label>
                        </div>

                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang || $dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->pemilik_barang_api : '' ?>" autocomplete="one-time-code" name="APIPemilikBarang" type="text" placeholder="APIPemilikBarang" class="APIPemilikBarang form-control target input-picker">
                            <label for="floatingInput">API</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Penerima Barang
                </label>
                
            </form>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>