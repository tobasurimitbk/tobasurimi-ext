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
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select jenisDokumenBC" name="jenisDokumenBC" id="jenisDokumenBC" aria-label="Floating label select example">
                                <option value="">
                                    - Jenis Dokumen -
                                </option>
                                <?php foreach ($jenisDokumen as $j) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->jenis_dokumen === $j['id'] ? 'selected' : '') : '' ?> value="<?= $j['id'] ?>">
                                        - <?= $j['description'] ?> - <?= $j['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis Dokumen</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->no_dokumen : '' ?>" autocomplete="one-time-code" name="noDokumen" type="text" placeholder="No Dokumen" class="form-control target input-picker">
                            <label for="floatingInput">No Dokumen</label>
                        </div>
                    </div>
                </div>
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
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select jenisIdentitasImportir" id="jenisIdentitasImportir" name="jenisIdentitasImportir" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Jenis Identitas -
                                </option>
                                <?php foreach ($jenisIdentitas as $j) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->importir_jenis_identitas === $j['id'] ? 'selected' : '') : '' ?> value="<?= $j['id'] ?>">
                                        - <?= $j['description'] ?> - <?= $j['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis Identitas</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_identitas : '' ?>" autocomplete="one-time-code" name="identitasImportir" type="text" placeholder="Identitas" class="form-control target input-picker">
                            <label for="floatingInput">Identitas</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_name : '' ?>" autocomplete="one-time-code" name="namaImportir" type="text" placeholder="Nama Importir" class="form-control target input-picker">
                            <label for="floatingInput">Nama Importir</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->tpb_no : '' ?>" autocomplete="one-time-code" name="noIzinTPBImportir" type="text" placeholder="No Izin TPB" class="form-control target input-picker">
                            <label for="floatingInput">No Izin TPB</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select jenisAPIImportir" id="jenisAPIImportir" name="jenisAPIImportir" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Jenis API -
                                </option>
                                <?php foreach ($jenisAPI as $j) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->importir_jenis_api === $j['id'] ? 'selected' : '') : '' ?> value="<?= $j['id'] ?>">
                                        - <?= $j['description'] ?> - <?= $j['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis API</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_api : '' ?>" autocomplete="one-time-code" name="APIImportir" type="text" placeholder="APIImportir" class="form-control target input-picker">
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
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang || $dataBC->status_posting !== 'Belum Posting' ? 'disabled' : '') : '' ?> class="form-select jenisIdentitasPemilikBarang" id="jenisIdentitasPemilikBarang" name="jenisIdentitasPemilikBarang" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Jenis Identitas -
                                </option>
                                <?php foreach ($jenisIdentitas as $j) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang_jenis_identitas === $j['id'] ? 'selected' : '') : '' ?> value="<?= $j['id'] ?>">
                                        - <?= $j['description'] ?> - <?= $j['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis Identitas</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang || $dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->pemilik_barang_identitas : '' ?>" autocomplete="one-time-code" name="identitasPemilikBarang" type="text" placeholder="Identitas" class="identitasPemilikBarang form-control target input-picker">
                            <label for="floatingInput">Identitas</label>
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
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang || $dataBC->status_posting !== 'Belum Posting' ? 'disabled' : '') : '' ?> class="form-select jenisAPIPemilikBarang" id="jenisAPIPemilikBarang" name="jenisAPIPemilikBarang" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Jenis API -
                                </option>
                                <?php foreach ($jenisAPI as $j) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang_jenis_api === $j['id'] ? 'selected' : '') : '' ?> value="<?= $j['id'] ?>">
                                        - <?= $j['description'] ?> - <?= $j['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis API</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang || $dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->pemilik_barang_api : '' ?>" autocomplete="one-time-code" name="APIPemilikBarang" type="text" placeholder="APIPemilikBarang" class="APIPemilikBarang form-control target input-picker">
                            <label for="floatingInput">API</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Penerima Barang
                </label>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->penerima_barang_npwp : '' ?>" autocomplete="one-time-code" name="npwpPenerimaBarang" type="text" placeholder="Identitas (NPWP)" class="form-control target input-picker">
                            <label for="floatingInput">Identitas (NPWP)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->penerima_barang_name : '' ?>" autocomplete="one-time-code" name="namaPenerimaBarang" type="text" placeholder="Nama Penerima Barang" class="form-control target input-picker">
                            <label for="floatingInput">Nama Penerima Barang</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->penerima_barang_api : '' ?>" autocomplete="one-time-code" name="APIPenerimaBarang" type="text" placeholder="API" class="form-control target input-picker">
                            <label for="floatingInput">API</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->penerima_barang_niper : '' ?>" autocomplete="one-time-code" name="niperPenerimaBarang" type="text" placeholder="Niper" class="form-control target input-picker">
                            <label for="floatingInput">Niper</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> name="alamatPenerimaBarang" class="form-control text-area-all"><?= (!empty($dataBC)) ? $dataBC->penerima_barang_address : '' ?></textarea>
                            <label for="floatingInput">Alamat</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Dokumen
                </label>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select noInvoice" id="noInvoice" name="noInvoice" aria-label="Floating label select example">
                                <option value="" data-date="">
                                    - Pilih Nomor Invoice -
                                </option>
                                <?php foreach ($dokumen as $d) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->invoice_id === $d->id ? 'selected' : '') : '' ?> value="<?= $d->id ?>" data-date="<?= date("d/m/Y", strtotime($d->createdAt)) ?>">
                                        - <?= $d->no_faktur ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">No Invoice</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->tanggal_invoice !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->tanggal_invoice)) : "") : '' ?>" readonly autocomplete="one-time-code" name="tanggalInvoice" type="text" placeholder="Tanggal Invoice" class="tanggalInvoice form-control target input-picker">
                            <label for="floatingInput">Tanggal Invoice</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->no_packing_list : '' ?>" autocomplete="one-time-code" name="noPackingList" type="text" placeholder="No Packing List" class="form-control target input-picker">
                            <label for="floatingInput">No Packing List</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->tanggal_packing_list !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->tanggal_packing_list)) : "") : '' ?>" autocomplete="one-time-code" name="tanggalPackingList" type="text" placeholder="Tanggal Packing List" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal Packing List</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->no_kontrak : '' ?>" autocomplete="one-time-code" name="noKontrak" type="text" placeholder="No Kontrak" class="form-control target input-picker">
                            <label for="floatingInput">No Kontrak</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->tanggal_kontrak !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->tanggal_kontrak)) : "") : '' ?>" autocomplete="one-time-code" name="tanggalKontrak" type="text" placeholder="Tanggal Kontrak" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal Kontrak</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->fasilitas_import_no : '' ?>" autocomplete="one-time-code" name="noFasilitasImport" type="text" placeholder="No Fasilitas Import (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">No Fasilitas Import (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->fasilitas_import_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->fasilitas_import_date)) : "") : '' ?>" autocomplete="one-time-code" name="tanggalFasilitasImport" type="text" placeholder="Tanggal Fasiitas Import (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal Fasilitas Import (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting !== 'Belum Posting' ? 'readonly' : '') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->fasilitas_import_code : '' ?>" autocomplete="one-time-code" name="kodeFasilitasImport" type="text" placeholder="Kode Fasilitas Import (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Kode Fasilitas Import (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Dokumen</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-dokumen btn-add btn-block float-right" data-btn="dokumen-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th style="text-align: center;">Kode Dokumen</th>
                                <th style="text-align: center;">Jenis Dokumen</th>
                                <th style="text-align: center;">No Dokumen</th>
                                <th style="text-align: center;">Tanggal</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-dokumen-table" id="body-dokumen-table" style="cursor: pointer;">
                            <?php if(!empty($dataBC)){ 
                                $list = json_decode($dataBC->data_dokumen);
                                $row_dokumen = 0; 
                                foreach($list as $item){    
                                    $row_dokumen = $row_dokumen + 1;
                                    if($dataBC->status_posting === "Belum Posting"){
                                ?>
                                        <tr>
                                        <td style="text-align: center;" class="edit-table-dokumen" data-kode="<?= $item->kode; ?>" data-jenis="<?= $item->jenis; ?>" data-no="<?= $item->no; ?>" data-tanggal="<?= $item->tanggal; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_dokumen; ?>">
                                            <?= $row_dokumen; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-dokumen" data-kode="<?= $item->kode; ?>" data-jenis="<?= $item->jenis; ?>" data-no="<?= $item->no; ?>" data-tanggal="<?= $item->tanggal; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_dokumen; ?>">
                                            <?= $item->kode; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-dokumen" data-kode="<?= $item->kode; ?>" data-jenis="<?= $item->jenis; ?>" data-no="<?= $item->no; ?>" data-tanggal="<?= $item->tanggal; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_dokumen; ?>">
                                            <?= $item->jenis; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-dokumen" data-kode="<?= $item->kode; ?>" data-jenis="<?= $item->jenis; ?>" data-no="<?= $item->no; ?>" data-tanggal="<?= $item->tanggal; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_dokumen; ?>">
                                            <?= $item->no; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-dokumen" data-kode="<?= $item->kode; ?>" data-jenis="<?= $item->jenis; ?>" data-no="<?= $item->no; ?>" data-tanggal="<?= $item->tanggal; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_dokumen; ?>">
                                            <?= $item->tanggal; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <button onclick='deleteRowDokumen(<?= $row_dokumen; ?>)'>X</button>
                                        </td>
                                        </tr>
                                <?php } else {?>
                                        <tr>
                                            <td style="text-align: center;"><?= $row_dokumen; ?></td>
                                            <td style="text-align: center;"><?= $item->kode; ?></td>
                                            <td style="text-align: center;"><?= $item->jenis; ?></td>
                                            <td style="text-align: center;"><?= $item->no; ?></td>
                                            <td style="text-align: center;"><?= $item->tanggal; ?></td>
                                            <td></td>
                                        </tr>
                                <?php }
                                } 
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Harga
            </label>
            <form class="create-form form-add-second" role="form" method="POST" enctype="multipart/form-data">
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select valuta" id="valuta" name="valuta" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Valuta -
                                </option>
                                <?php foreach ($valuta as $v) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->valuta === $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>">
                                        - <?= $v['value'] ?> - <?= $v['description'] ?> - 
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Valuta</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->ndpbm, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="npdpbm" type="number" placeholder="NDPBM" class="npdpbm form-control target input-picker">
                            <label for="floatingInput">NDPBM</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->cif_value, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="nilaiCif" type="number" placeholder="Nilai CIF" class="nilaiCif form-control target input-picker">
                            <label for="floatingInput">Nilai CIF</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->harga_penyerahan, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="hargaPenyerahan" type="number" placeholder="Harga Penyerahan" class="hargaPenyerahan form-control target input-picker">
                            <label for="floatingInput">Harga Penyerahan</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select caraPengangkutan" name="caraPengangkutan" id="caraPengangkutan" aria-label="Floating label select example">
                                <option value="">
                                    - Jenis Sarana Pengangkut -
                                </option>
                                <?php foreach ($pengangkutan as $p) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->pengangkutan === $p['id'] ? 'selected' : '') : '' ?> value="<?= $p['id'] ?>">
                                        - <?= $p['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Jenis Sarana Pengangkut</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Kontainer</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-kontainer btn-add btn-block float-right" data-btn="kontainer-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th style="text-align: center;">No Kontainer</th>
                                <th style="text-align: center;">Ukuran</th>
                                <th style="text-align: center;">Tipe</th>
                                <th style="text-align: center;">Keterangan</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-kontainer-table" id="body-kontainer-table" style="cursor: pointer;">
                            <?php if(!empty($dataBC)){ 
                                $list = json_decode($dataBC->data_kontainer);
                                $row_kontainer = 0; 
                                foreach($list as $item){    
                                    $row_kontainer = $row_kontainer + 1;
                                    if($dataBC->status_posting === "Belum Posting"){
                                ?>
                                        <tr>
                                        <td style="text-align: center;" class="edit-table-kontainer" data-no="<?= $item->no; ?>" data-ukuran="<?= $item->ukuran; ?>" data-tipe="<?= $item->tipe; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kontainer; ?>">
                                            <?= $row_kontainer; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-kontainer" data-no="<?= $item->no; ?>" data-ukuran="<?= $item->ukuran; ?>" data-tipe="<?= $item->tipe; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kontainer; ?>">
                                            <?= $item->no; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-kontainer" data-no="<?= $item->no; ?>" data-ukuran="<?= $item->ukuran; ?>" data-tipe="<?= $item->tipe; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kontainer; ?>">
                                            <?= $item->ukuran; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-kontainer" data-no="<?= $item->no; ?>" data-ukuran="<?= $item->ukuran; ?>" data-tipe="<?= $item->tipe; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kontainer; ?>">
                                            <?= $item->tipe; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-kontainer" data-no="<?= $item->no; ?>" data-ukuran="<?= $item->ukuran; ?>" data-tipe="<?= $item->tipe; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kontainer; ?>">
                                            <?= $item->keterangan; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <button onclick='deleteRowKontainer(<?= $row_kontainer; ?>)'>X</button>
                                        </td>
                                        </tr>
                                <?php } else {?>
                                        <tr>
                                            <td style="text-align: center;"><?= $row_kontainer; ?></td>
                                            <td style="text-align: center;"><?= $item->no; ?></td>
                                            <td style="text-align: center;"><?= $item->ukuran; ?></td>
                                            <td style="text-align: center;"><?= $item->tipe; ?></td>
                                            <td style="text-align: center;"><?= $item->keterangan; ?></td>
                                            <td></td>
                                        </tr>
                                <?php }
                                } 
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Kemasan</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-kemasan btn-add btn-block float-right" data-btn="kemasan-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th style="text-align: center;">Jumlah</th>
                                <th style="text-align: center;">Kode</th>
                                <th style="text-align: center;">Uraian</th>
                                <th style="text-align: center;">Merk Kemasan</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-kemasan-table" id="body-kemasan-table" style="cursor: pointer;">
                            <?php if(!empty($dataBC)){ 
                                $list = json_decode($dataBC->data_kemasan);
                                $row_kemasan = 0; 
                                foreach($list as $item){    
                                    $row_kemasan = $row_kemasan + 1;
                                    if($dataBC->status_posting === "Belum Posting"){
                                ?>
                                        <tr>
                                        <td style="text-align: center;" class="edit-table-kemasan" data-jumlah="<?= $item->jumlah; ?>" data-kode="<?= $item->kode; ?>" data-uraian="<?= $item->uraian; ?>" data-merk="<?= $item->merk; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kemasan; ?>">
                                            <?= $row_kemasan; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-kemasan" data-jumlah="<?= $item->jumlah; ?>" data-kode="<?= $item->kode; ?>" data-uraian="<?= $item->uraian; ?>" data-merk="<?= $item->merk; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kemasan; ?>">
                                            <?= $item->jumlah; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-kemasan" data-jumlah="<?= $item->jumlah; ?>" data-kode="<?= $item->kode; ?>" data-uraian="<?= $item->uraian; ?>" data-merk="<?= $item->merk; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kemasan; ?>">
                                            <?= $item->kode; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-kemasan" data-jumlah="<?= $item->jumlah; ?>" data-kode="<?= $item->kode; ?>" data-uraian="<?= $item->uraian; ?>" data-merk="<?= $item->merk; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kemasan; ?>">
                                            <?= $item->uraian; ?>
                                        </td>
                                        <td style="text-align: center;" class="edit-table-kemasan" data-jumlah="<?= $item->jumlah; ?>" data-kode="<?= $item->kode; ?>" data-uraian="<?= $item->uraian; ?>" data-merk="<?= $item->merk; ?>" data-keterangan="<?= $item->keterangan; ?>"  data-row="<?= $row_kemasan; ?>">
                                            <?= $item->merk; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <button onclick='deleteRowKemasan(<?= $row_kemasan; ?>)'>X</button>
                                        </td>
                                        </tr>
                                <?php } else {?>
                                        <tr>
                                            <td style="text-align: center;"><?= $row_kemasan; ?></td>
                                            <td style="text-align: center;"><?= $item->jumlah; ?></td>
                                            <td style="text-align: center;"><?= $item->kode; ?></td>
                                            <td style="text-align: center;"><?= $item->uraian; ?></td>
                                            <td style="text-align: center;"><?= $item->merk; ?></td>
                                            <td></td>
                                        </tr>
                                <?php }
                                } 
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <form class="create-form form-add-third" role="form" method="POST" enctype="multipart/form-data">
                <label class="form-label font-weight-bold lable-title mt-2">
                    Barang
                </label>
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->bruto, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="bruto" type="number" placeholder="Bruto (Kg)" class="bruto form-control target input-picker">
                            <label for="floatingInput">Bruto (Kg)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->netto, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="netto" type="number" placeholder="Netto (Kg)" class="netto form-control target input-picker">
                            <label for="floatingInput">Netto (Kg)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->item_count, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="jumlahBarang" type="number" placeholder="Jumlah Barang" class="jumlahBarang form-control target input-picker">
                            <label for="floatingInput">Jumlah Barang</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Billing
                </label>
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select pembayaran" name="pembayaran" id="pembayaran" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Pembayaran -
                                </option>
                                <?php foreach ($referensiLokasiBayar as $r) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->pembayaran === $r['id'] ? 'selected' : '') : '' ?> value="<?= $r['id'] ?>">
                                        - <?= $r['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> class="form-select wajibBayar" name="wajibBayar" id="wajibBayar" aria-label="Floating label select example">
                                <option value="">
                                    - Wajib Bayar -
                                </option>
                                <?php foreach ($wajibBayar as $w) : ?>
                                    <option <?= (!empty($dataBC)) ? ($dataBC->wajib_bayar === $w['id'] ? 'selected' : '') : '' ?> value="<?= $w['id'] ?>">
                                        - <?= $w['value'] ?> -
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Wajib Bayar</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row mt-2">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th style="text-align: center;">Jenis Pungutan</th>
                                <th style="text-align: center;">Dibayar (Rp)</th>
                                <th style="text-align: center;">Dibebaskan (Rp)</th>
                                <th style="text-align: center;">Ditangguhkan (Rp)</th>
                                <th style="text-align: center;">Sudah Dilunasi (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="body-pungutan-table" id="body-pungutan-table" style="cursor: pointer;">
                        
                        </tbody>
                        <tfoot class="foot-pungutan-table" id="foot-pungutan-table">
                            <tr>
                                <td></td>
                                <td style="text-align: center;">Total</td>
                                <td style="text-align: center;">0</td>
                                <td style="text-align: center;">0</td>
                                <td style="text-align: center;">0</td>
                                <td style="text-align: center;">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Pengesahan
            </label>
            <br/>
            <label class="form-label mt-2">
                Dengan ini saya menyatakan bertanggung jawab atas kebenaran hal-hal yang diberitahukan dalam pemberitahuan pabean ini.
            </label>
            <form class="create-form form-add-four" role="form" method="POST" enctype="multipart/form-data">
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->tempat : '' ?>" autocomplete="one-time-code" name="tempat" type="text" placeholder="Tempat" class="tempat form-control target input-picker">
                            <label for="floatingInput">Tempat</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->tanggal !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->tanggal)) : "") : '' ?>" autocomplete="one-time-code" name="tanggal" type="text" placeholder="Tanggal" class="tanggal form-control target input-picker">
                            <label for="floatingInput">Tanggal</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->pemberitahu : '' ?>" autocomplete="one-time-code" name="pemberitahu" type="text" placeholder="Pemberitahu" class="pemberitahu form-control target input-picker">
                            <label for="floatingInput">Pemberitahu</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled'): '' ?> value="<?= (!empty($dataBC)) ? $dataBC->jabatan : '' ?>" autocomplete="one-time-code" name="jabatan" type="text" placeholder="Jabatan" class="jabatan form-control target input-picker">
                            <label for="floatingInput">Jabatan</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<div class="modal dokumenModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-dokumen-name"></label> Dokumen</h5>
            </div>
            <div class="modal-body">
                <form class="dokumenForm" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="idDokumen" name="idDokumen" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control kodeDokumen" name="kodeDokumen" placeholder="Kode Dokumen">
                                <label for="floatingInput">Kode Dokumen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control jenisDokumen" name="jenisDokumen" placeholder="Jenis Dokumen">
                                <label for="floatingInput">Jenis Dokumen</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control noDokumen" name="noDokumen" placeholder="No Dokumen">
                                <label for="floatingInput">No Dokumen</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control tanggalDokumen" name="tanggalDokumen" placeholder="Tanggal">
                                <label for="floatingInput">Tanggal</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keteranganDokumen" name="keteranganDokumen" placeholder="Keterangan (Opsional)">
                                <label for="floatingInput">Keterangan (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-dokumen btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-dokumen">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-dokumen delete-form">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal kontainerModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-kontainer-name"></label> Kontainer</h5>
            </div>
            <div class="modal-body">
                <form class="kontainerForm" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="idKontainer" name="idKontainer" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control noKontainer" name="noKontainer" placeholder="No Kontainer">
                                <label for="floatingInput">No Kontainer</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control ukuranKontainer" name="ukuranKontainer" placeholder="Ukuran">
                                <label for="floatingInput">Ukuran</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control tipeKontainer" name="tipeKontainer" placeholder="Tipe">
                                <label for="floatingInput">Tipe</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keteranganKontainer" name="keteranganKontainer" placeholder="Keterangan (Opsional)">
                                <label for="floatingInput">Keterangan (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-kontainer btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-kontainer">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-kontainer delete-form">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal kemasanModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-kemasan-name"></label> Kemasan</h5>
            </div>
            <div class="modal-body">
                <form class="kemasanForm" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="idKemasan" name="idKemasan" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control jumlahKemasan" name="jumlahKemasan" placeholder="Jumlah">
                                <label for="floatingInput">Jumlah</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control kodeKemasan" name="kodeKemasan" placeholder="Kode">
                                <label for="floatingInput">Kode</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control uraianKemasan" name="uraianKemasan" placeholder="Uraian">
                                <label for="floatingInput">Uraian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control merkKemasan" name="merkKemasan" placeholder="Merk Kemasan">
                                <label for="floatingInput">Merk Kemasan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keteranganKemasan" name="keteranganKemasan" placeholder="Keterangan (Opsional)">
                                <label for="floatingInput">Keterangan (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-kemasan btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-kemasan">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-kemasan delete-form">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_dokumen = [];
    let list_kontainer = [];
    let list_kemasan = [];
    let list_pungutan = [];

    let row_dokumen = 0;
    let row_kontainer = 0;
    let row_kemasan = 0;

    <?php if(!empty($dataBC)){
        $list_dokumen = json_decode($dataBC->data_dokumen);
        foreach($list_dokumen as $item){
    ?> 
            row_dokumen = row_dokumen + 1;
            list_dokumen.push({
                "row": row_dokumen,
                "kode": '<?= $item->kode ?>',
                "jenis": '<?= $item->jenis ?>',
                "no": '<?= $item->no ?>',
                "tanggal": '<?= $item->tanggal ?>',
                "keterangan": '<?= $item->keterangan ?>',
            });
    <?php } 
    } ?>

    <?php if(!empty($dataBC)){
        $list_kontainer = json_decode($dataBC->data_kontainer);
        foreach($list_kontainer as $item){
    ?> 
            row_kontainer = row_kontainer + 1;
            list_kontainer.push({
                "row": row_kontainer,
                "no": '<?= $item->no ?>',
                "ukuran": '<?= $item->ukuran ?>',
                "tipe": '<?= $item->tipe ?>',
                "keterangan": '<?= $item->keterangan ?>',
            });
    <?php } 
    } ?>

<?php if(!empty($dataBC)){
        $list_kemasan = json_decode($dataBC->data_kemasan);
        foreach($list_kemasan as $item){
    ?> 
            row_kemasan = row_kemasan + 1;
            list_kemasan.push({
                "row": row_kemasan,
                "jumlah": '<?= $item->jumlah ?>',
                "kode": '<?= $item->kode ?>',
                "uraian": '<?= $item->uraian ?>',
                "merk": '<?= $item->merk ?>',
                "keterangan": '<?= $item->keterangan ?>',
            });
    <?php } 
    } ?>

    var validator = $(".form-add-bc").validate({
        rules: {
            noDokumen: {
                required: true
            },
            jenisDokumenBC: {
                required: true
            },
            kantorPabean: {
                required: true
            },
            kodeTujuanTpb: {
                required: true
            },
            jenisIdentitasImportir: {
                required: true
            },
            identitasImportir: {
                required: true
            },
            jenisAPIImportir: {
                required: true
            },
            namaImportir: {
                required: true
            },
            noIzinTPBImportir: {
                required: true
            },
            APIImportir: {
                required: true
            },
            alamatImportir: {
                required: true
            },
            npwpPenerimaBarang : {
                required: true
            },
            namaPenerimaBarang : {
                required: true
            },
            APIPenerimaBarang : {
                required: true
            },
            niperPenerimaBarang : {
                required: true
            },
            alamatPenerimaBarang : {
                required: true
            },
            noInvoice : {
                required: true
            },
            noPackingList : {
                required: true
            },
            tanggalPackingList : {
                required: true
            },
            noKontrak : {
                required: true
            },
            tanggalKontrak : {
                required: true
            }
        },
        messages: {
            noDokumen: {
                required: "No Dokumen wajib diisi"
            },
            jenisDokumenBC: {
                required: "Jenis Dokumen wajib diisi"
            },
            kantorPabean: {
                required: "Kantor Pabean wajib diisi"
            },
            kodeTujuanTpb: {
                required: "Tujuan wajib diisi"
            },
            jenisIdentitasImportir: {
                required: "Jenis Identitas wajib diisi"
            },
            identitasImportir: {
                required: "identitas wajib diisi"
            },
            jenisAPIImportir: {
                required: "Jenis API wajib diisi"
            },
            namaImportir: {
                required: "Nama Pengusaha wajib diisi"
            },
            noIzinTPBImportir: {
                required: "No Izin TPB wajib diisi"
            },
            APIImportir: {
                required: "API wajib diisi"
            },
            alamatImportir: {
                required: "Alamat wajib diisi"
            },
            npwpPenerimaBarang : {
                required: "Identitas (NPWP) wajib diisi"
            },
            namaPenerimaBarang : {
                required: "Nama Penerima Barang wajib diisi"
            },
            APIPenerimaBarang : {
                required: "API wajib diisi"
            },
            niperPenerimaBarang : {
                required: "Niper wajib diisi"
            },
            alamatPenerimaBarang : {
                required: "Alamat wajib diisi"
            },
            noInvoice : {
                required: "No Invoice wajib diisi"
            },
            noPackingList : {
                required: "No Packing wajib diisi"
            },
            tanggalPackingList : {
                required: "Tanggal Packing wajib diisi"
            },
            noKontrak : {
                required: "No kontrak wajib diisi"
            },
            tanggalKontrak : {
                required: "Tanggal Kontrak wajib diisi"
            },
            jenisIdentitasPemilikBarang: {
                required: "Jenis Identitas wajib diisi"
            },
            identitasPemilikBarang: {
                required: "identitas wajib diisi"
            },
            jenisAPIPemilikBarang: {
                required: "Jenis API wajib diisi"
            },
            namaPemilikBarang: {
                required: "Nama Pemilik Barang wajib diisi"
            },
            alamatPemilikBarang: {
                required: "Alamat wajib diisi"
            },
            APIPemilikBarang: {
                required: "API wajib diisi"
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

    <?php if(!empty($dataBC)){
        if($dataBC->pemilik_barang === 0){
    ?>
            $('.jenisIdentitasPemilikBarang').rules('add', {
                required: true
            });
            $('.identitasPemilikBarang').rules('add', {
                required: true
            });
            $('.jenisAPIPemilikBarang').rules('add', {
                required: true
            });
            $('.namaPemilikBarang').rules('add', {
                required: true
            });
            $('.alamatPemilikBarang').rules('add', {
                required: true
            });
            $('.APIPemilikBarang').rules('add', {
                required: true
            });
    <?php }} else { ?>
        $('.jenisIdentitasPemilikBarang').rules('add', {
            required: true
        });
        $('.identitasPemilikBarang').rules('add', {
            required: true
        });
        $('.jenisAPIPemilikBarang').rules('add', {
            required: true
        });
        $('.namaPemilikBarang').rules('add', {
            required: true
        });
        $('.alamatPemilikBarang').rules('add', {
            required: true
        });
        $('.APIPemilikBarang').rules('add', {
            required: true
        });
    <?php } ?>

    var validator_second = $(".form-add-second").validate({
        rules: {
            valuta: {
                required: true
            },
            npdpbm: {
                required: true
            },
            nilaiCif: {
                required: true
            },
            hargaPenyerahan: {
                required: true
            },
            caraPengangkutan: {
                required: true
            }
        },
        messages: {
            valuta: {
                required: "Valuta wajib diisi"
            },
            npdpbm: {
                required: "NPDPBM wajib diisi"
            },
            nilaiCif: {
                required: "Nilai CIF wajib diisi"
            },
            hargaPenyerahan: {
                required: "Harga Penyerahan wajib diisi"
            },
            caraPengangkutan: {
                required: "Cara Pengangkutan wajib diisi"
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

    var validator_third = $(".form-add-third").validate({
        rules: {
            bruto: {
                required: true
            },
            netto: {
                required: true
            },
            jumlahBarang: {
                required: true
            },
            pembayaran: {
                required: true
            },
            wajibBayar: {
                required: true
            }
        },
        messages: {
            bruto: {
                required: "Bruto wajib diisi"
            },
            netto: {
                required: "Netto wajib diisi"
            },
            jumlahBarang: {
                required: "Jumlah Barang wajib diisi"
            },
            pembayaran: {
                required: "Pembayaran wajib diisi"
            },
            wajibBayar: {
                required: "Wajib Bayar wajib diisi"
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

    var validator_four = $(".form-add-four").validate({
        rules: {
            tempat: {
                required: true
            },
            tanggal: {
                required: true
            },
            pemberitahu: {
                required: true
            },
            jabatan: {
                required: true
            }
        },
        messages: {
            tempat: {
                required: "Tempat wajib diisi"
            },
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            pemberitahu: {
                required: "Pemberitahu wajib diisi"
            },
            jabatan: {
                required: "Jabatan wajib diisi"
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

    var validator_dokumen = $(".dokumenForm").validate({
        rules: {
            kodeDokumen: {
                required: true
            },
            jenisDokumen: {
                required: true
            },
            noDokumen: {
                required: true
            },
            tanggalDokumen: {
                required: true
            }
        },
        messages: {
            kodeDokumen: {
                required: "Kode Dokumen wajib diisi"
            },
            jenisDokumen: {
                required: "Jenis Dokumen wajib diisi"
            },
            noDokumen: {
                required: "No Dokumen wajib diisi"
            },
            tanggalDokumen: {
                required: "Tanggal wajib diisi"
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

    var validator_kontainer = $(".kontainerForm").validate({
        rules: {
            noKontainer: {
                required: true
            },
            ukuranKontainer: {
                required: true
            },
            tipeKontainer: {
                required: true
            }
        },
        messages: {
            noKontainer: {
                required: "No Kontainer wajib diisi"
            },
            ukuranKontainer: {
                required: "Ukuran wajib diisi"
            },
            tipeKontainer: {
                required: "Tipe wajib diisi"
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

    var validator_kemasan = $(".kemasanForm").validate({
        rules: {
            jumlahKemasan: {
                required: true
            },
            kodeKemasan: {
                required: true
            },
            uraianKemasan: {
                required: true
            },
            merkKemasan: {
                required: true
            }
        },
        messages: {
            jumlahKemasan: {
                required: "Jumlah wajib diisi"
            },
            kodeKemasan: {
                required: "Kode wajib diisi"
            },
            uraianKemasan: {
                required: "Uraian wajib diisi"
            },
            merkKemasan: {
                required: "Merk Kemasan wajib diisi"
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

    $('#jenisDokumenBC').select2({
        placeholder: "Pilih Jenis Dokumen",
        theme: "bootstrap-5"
    });

    $('#jenisIdentitasImportir').select2({
        placeholder: "Pilih Jenis Identitas",
        theme: "bootstrap-5"
    });

    $('#jenisAPIImportir').select2({
        placeholder: "Pilih Jenis API",
        theme: "bootstrap-5"
    });

    $('#jenisIdentitasPemilikBarang').select2({
        placeholder: "Pilih Jenis Identitas",
        theme: "bootstrap-5"
    });

    $('#jenisAPIPemilikBarang').select2({
        placeholder: "Pilih Jenis API",
        theme: "bootstrap-5"
    });

    $('#kantorPabean').select2({
        placeholder: "Pilih Kantor Pabean",
        theme: "bootstrap-5"
    });

    $('#kodeTujuanTpb').select2({
        placeholder: "Pilih Jenis TPB",
        theme: "bootstrap-5"
    });

    $("select[name='caraPengangkutan']").select2({
        placeholder: "Pilih Cara Pengangkutan",
        theme: "bootstrap-5"
    });

    $("input[name='tanggalFasilitasImport']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("input[name='tanggal']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("input[name='tanggalPackingList']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("input[name='tanggalKontrak']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("input[name='tanggalFasilitasImport']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("input[name='tanggalDokumen']").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("select[name='noInvoice']").select2({
        placeholder: "Pilih Nomor Invoice",
        theme: "bootstrap-5"
    });

    $("select[name='valuta']").select2({
        placeholder: "Pilih Valuta",
        theme: "bootstrap-5"
    });

    $("select[name='pembayaran']").select2({
        placeholder: "Pilih Pembayaran",
        theme: "bootstrap-5"
    });

    $("select[name='wajibBayar']").select2({
        placeholder: "Pilih Wajib Bayar",
        theme: "bootstrap-5"
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
        .css('z-index', '1');

    // SwitchBox
    $('#switchPemilikBarang').click(function() {
        var statusChecked = $(this).prop('checked');

        var jenisIdentitasImportir = $(".jenisIdentitasImportir option:selected").val();
        var identitasImportir = $("input[name='identitasImportir']").val();
        var namaImportir = $("input[name='namaImportir']").val();
        var jenisAPIImportir = $(".jenisAPIImportir option:selected").val();
        var APIImportir = $("input[name='APIImportir']").val();
        var alamatImportir = $("textarea[name='alamatImportir']").val();

        if (statusChecked) {
            $(".jenisIdentitasPemilikBarang").attr('disabled', true).val(jenisIdentitasImportir).change();
            $("input[name='identitasPemilikBarang']").attr('readonly', true).val(identitasImportir);
            $("input[name='namaPemilikBarang']").attr('readonly', true).val(namaImportir);
            $("textarea[name='alamatPemilikBarang']").attr('readonly', true).val(alamatImportir);
            $(".jenisAPIPemilikBarang").attr('disabled', true).val(jenisAPIImportir).change();
            $("input[name='APIPemilikBarang']").attr('readonly', true).val(APIImportir);

            $('.jenisIdentitasPemilikBarang').rules('remove', 'required');
            $('.identitasPemilikBarang').rules('remove', 'required');
            $('.jenisAPIPemilikBarang').rules('remove', 'required');
            $('.namaPemilikBarang').rules('remove', 'required');
            $('.alamatPemilikBarang').rules('remove', 'required');
            $('.APIPemilikBarang').rules('remove', 'required');
        } else {
            $(".jenisIdentitasPemilikBarang").attr('disabled', false).val('').change();
            $("input[name='identitasPemilikBarang']").attr('readonly', false).val(null);
            $("input[name='namaPemilikBarang']").attr('readonly', false).val(null);
            $("textarea[name='alamatPemilikBarang']").attr('readonly', false).val(null);
            $(".jenisAPIPemilikBarang").attr('disabled', false).val('').change();
            $("input[name='APIPemilikBarang']").attr('readonly', false).val(null);

            $('.jenisIdentitasPemilikBarang').rules('add', {
                required: true
            });
            $('.jenisAPIPemilikBarang').rules('add', {
                required: true
            });
            $('.namaPemilikBarang').rules('add', {
                required: true
            });
            $('.alamatPemilikBarang').rules('add', {
                required: true
            });
            $('.APIPemilikBarang').rules('add', {
                required: true
            });
        }
    });

    // open modal
    $('.btn-show-dokumen').click(function() {
        $(".dokumenForm")[0].reset();
        validator_dokumen.resetForm();
        validator_dokumen.reset();
        $(".title-dokumen-name").text("Create");
        $(".delete-dokumen").css("display", "none");
        $(".dokumenModal").modal("show");
    })

    $('.btn-hide-dokumen').click(function() {
        $(".dokumenModal").modal("hide");
    })

    $('.btn-show-kontainer').click(function() {
        $(".kontainerForm")[0].reset();
        validator_kontainer.resetForm();
        validator_kontainer.reset();
        $(".title-kontainer-name").text("Create");
        $(".delete-kontainer").css("display", "none");
        $(".kontainerModal").modal("show");
    })

    $('.btn-hide-kontainer').click(function() {
        $(".kontainerModal").modal("hide");
    })

    $('.btn-show-kemasan').click(function() {
        $(".kemasanForm")[0].reset();
        validator_kemasan.resetForm();
        validator_kemasan.reset();
        $(".title-kemasan-name").text("Create");
        $(".delete-kemasan").css("display", "none");
        $(".kemasanModal").modal("show");
    })

    $('.btn-hide-kemasan').click(function() {
        $(".kemasanModal").modal("hide");
    })

    // edit
    $(document).on('click', '.edit-table-dokumen', function(evt) {
        $(".title-dokumen-name").text("Update");
        $(".delete-dokumen").css('display', '');
        $(".dokumenForm")[0].reset();
        let kode = $(this).data('kode');
        let jenis = $(this).data('jenis');
        let no = $(this).data('no');
        let tanggal = $(this).data('tanggal');
        let keterangan = $(this).data('keterangan');
        let rowid = $(this).data('row');

        validator_dokumen.resetForm();
        validator_dokumen.reset();

        $(".idDokumen").val(rowid);
        $(".kodeDokumen").val(kode);
        $(".jenisDokumen").val(jenis);
        $(".noDokumen").val(no);
        $(".tanggalDokumen").val(tanggal);
        $(".keteranganDokumen").val(keterangan);

        $(".dokumenModal").modal('show');
    })

    $(document).on('click', '.edit-table-kontainer', function(evt) {
        $(".title-kontainer-name").text("Update");
        $(".delete-kontainer").css('display', '');
        $(".kontainerForm")[0].reset();
        let no = $(this).data('no');
        let ukuran = $(this).data('ukuran');
        let tipe = $(this).data('tipe');
        let keterangan = $(this).data('keterangan');
        let rowid = $(this).data('row');

        validator_kontainer.resetForm();
        validator_kontainer.reset();

        $(".idKontainer").val(rowid);
        $(".noKontainer").val(no);
        $(".ukuranKontainer").val(ukuran);
        $(".tipeKontainer").val(tipe);
        $(".keteranganKontainer").val(keterangan);

        $(".kontainerModal").modal('show');
    })

    $(document).on('click', '.edit-table-kemasan', function(evt) {
        $(".title-kemasan-name").text("Update");
        $(".delete-kemasan").css('display', '');
        $(".kemasanForm")[0].reset();
        let jumlah = $(this).data('jumlah');
        let kode = $(this).data('kode');
        let uraian = $(this).data('uraian');
        let merk = $(this).data('merk');
        let keterangan = $(this).data('keterangan');
        let rowid = $(this).data('row');

        validator_kemasan.resetForm();
        validator_kemasan.reset();

        $(".idKemasan").val(rowid);
        $(".jumlahKemasan").val(jumlah);
        $(".kodeKemasan").val(kode);
        $(".uraianKemasan").val(uraian);
        $(".merkKemasan").val(merk);
        $(".keteranganKemasan").val(keterangan);

        $(".kemasanModal").modal('show');
    })

    // submit
    $('.btn-submit-parent').click(function() {
        if(list_dokumen.length === 0)
        {
            Swal.fire({
                icon: 'error',
                title: "List Dokumen Tidak Boleh Kosong",
                confirmButtonColor: '#4e73df',
            })
        }
        else
        {
            if ($(".form-add-bc").valid() && $(".form-add-second").valid()) {
                if ($(".form-add-third").valid() && $(".form-add-four").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            setLoading()
                            let data = new FormData(document.querySelector(".form-add-bc"));
                            data.append("valuta", $(".valuta").val());
                            data.append("npdpbm", $(".npdpbm").val());
                            data.append("nilaiCif", $(".nilaiCif").val());
                            data.append("hargaPenyerahan", $(".hargaPenyerahan").val());
                            data.append("caraPengangkutan", $(".caraPengangkutan option:selected").val());
                            data.append("bruto", $(".bruto").val());
                            data.append("netto", $(".netto").val());
                            data.append("jumlahBarang", $(".jumlahBarang").val());
                            data.append("pembayaran", $(".pembayaran").val());
                            data.append("wajibBayar", $(".wajibBayar").val());
                            data.append("tempat", $(".tempat").val());
                            data.append("tanggal", $(".tanggal").val());
                            data.append("pemberitahu", $(".pemberitahu").val());
                            data.append("jabatan", $(".jabatan").val());
                            data.append("data_dokumen", JSON.stringify(list_dokumen));
                            data.append("data_kontainer", JSON.stringify(list_kontainer));
                            data.append("data_kemasan", JSON.stringify(list_kemasan));

                            // update
                            if($(".id").val())
                            {
                                $.ajax({
                                    url: "<?= base_url("bea-cukai-bc-25/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            stopLoading()
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("bea-cukai-bc-25"); ?>";
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Diubah, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                            // create
                            else
                            {
                                $.ajax({
                                    url: "<?= base_url("bea-cukai-bc-25/save"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        csrf.val(response.token);
                                        if (response.status) {
                                            stopLoading()
                                            Swal.fire({
                                                    icon: 'success',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                                .then(() => {
                                                    window.location.href = "<?= base_url("bea-cukai-bc-25"); ?>";
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            stopLoading()
                                        }
                                    },
                                    onError: function(response) {
                                        csrf.val(response.token);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Data Gagal Diubah, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                        }
                    })
                }
            }
        }
    })

    $('.btn-submit-dokumen').click(function() {
        let row_detail = $(".idDokumen").val() ? Number($(".idDokumen").val()) : 0;
        let kode = $(".kodeDokumen").val();
        let jenis = $(".jenisDokumen").val();
        let no = $(".noDokumen").val();
        let tanggal = $(".tanggalDokumen").val();
        let keterangan = $(".keteranganDokumen").val();

        if ($(".dokumenForm").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // update detail
                    if(row_detail)
                    {
                        let new_list_items = [];
                        let tag_html = "";
                        row_dokumen = 0;

                        list_dokumen.map(item => {
                            row_dokumen = row_dokumen + 1;
                            if (item.row == row_detail) {
                                tag_html += `<tr>`;
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += row_dokumen;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += kode;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += jenis;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += no;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += tanggal;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;">`;
                                tag_html += `<button onclick='deleteRowDokumen(${row_dokumen})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                new_list_items.push({
                                    "row": row_dokumen,
                                    "kode": kode,
                                    "jenis": jenis,
                                    "no": no,
                                    "tanggal": tanggal,
                                    "keterangan": keterangan
                                })
                            }
                            else
                            {
                                tag_html += `<tr>`;
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += row_dokumen;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += item.kode;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += item.jenis;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += item.no;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                                tag_html += item.tanggal;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;">`;
                                tag_html += `<button onclick='deleteRowDokumen(${row_dokumen})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                new_list_items.push(item);
                            }
                        })

                        list_dokumen = new_list_items;
                        $(".body-dokumen-table").empty();
                        $(".body-dokumen-table").append(tag_html);

                        $(".dokumenModal").modal("hide");
                    }
                    // create detail
                    else
                    {
                        row_dokumen = row_dokumen + 1;

                        list_dokumen.push({
                            "row": row_dokumen,
                            "kode": kode,
                            "jenis": jenis,
                            "no": no,
                            "tanggal": tanggal,
                            "keterangan": keterangan
                        })

                        let tag_html = "";
                        
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += row_dokumen;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += kode;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += jenis;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += no;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${kode}" data-jenis="${jenis}" data-no="${no}" data-tanggal="${tanggal}" data-keterangan="${keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += tanggal;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowDokumen(${row_dokumen})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";
                        $(".body-dokumen-table").append(tag_html);

                        $(".dokumenModal").modal("hide");
                    }
                }
            })
        }
    })

    $('.btn-submit-kontainer').click(function() {
        let row_detail = $(".idKontainer").val() ? Number($(".idKontainer").val()) : 0;
        let no = $(".noKontainer").val();
        let ukuran = $(".ukuranKontainer").val();
        let tipe = $(".tipeKontainer").val();
        let keterangan = $(".keteranganKontainer").val();

        if ($(".kontainerForm").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // update detail
                    if(row_detail)
                    {
                        let new_list_items = [];
                        let tag_html = "";
                        row_kontainer = 0;

                        list_kontainer.map(item => {
                            row_kontainer = row_kontainer + 1;
                            if (item.row == row_detail) {
                                tag_html += `<tr>`;
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                                tag_html += row_kontainer;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                                tag_html += no;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                                tag_html += ukuran;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                                tag_html += tipe;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                                tag_html += keterangan;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;">`;
                                tag_html += `<button onclick='deleteRowKontainer(${row_kontainer})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                new_list_items.push({
                                    "row": row_kontainer,
                                    "no": no,
                                    "ukuran": ukuran,
                                    "tipe": tipe,
                                    "keterangan": keterangan
                                })
                            }
                            else
                            {
                                tag_html += `<tr>`;
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                                tag_html += row_kontainer;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                                tag_html += item.no;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                                tag_html += item.ukuran;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                                tag_html += item.tipe;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                                tag_html += item.keterangan;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;">`;
                                tag_html += `<button onclick='deleteRowKontainer(${row_kontainer})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                new_list_items.push(item);
                            }
                        })

                        list_kontainer = new_list_items;
                        $(".body-kontainer-table").empty();
                        $(".body-kontainer-table").append(tag_html);

                        $(".kontainerModal").modal("hide");
                    }
                    // create detail
                    else
                    {
                        row_kontainer = row_kontainer + 1;

                        list_kontainer.push({
                            "row": row_kontainer,
                            "no": no,
                            "ukuran": ukuran,
                            "tipe": tipe,
                            "keterangan": keterangan
                        })

                        let tag_html = "";
                        
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                        tag_html += row_kontainer;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                        tag_html += no;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                        tag_html += ukuran;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                        tag_html += tipe;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${no}" data-ukuran="${ukuran}" data-tipe="${tipe}" data-keterangan="${keterangan}" data-row="${row_kontainer}">`;
                        tag_html += keterangan;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowKontainer(${row_kontainer})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";
                        $(".body-kontainer-table").append(tag_html);

                        $(".kontainerModal").modal("hide");
                    }
                }
            })
        }
    })

    $('.btn-submit-kemasan').click(function() {
        let row_detail = $(".idKemasan").val() ? Number($(".idKemasan").val()) : 0;
        let jumlah = $(".jumlahKemasan").val();
        let kode = $(".kodeKemasan").val();
        let uraian = $(".uraianKemasan").val();
        let merk = $(".merkKemasan").val();
        let keterangan = $(".keteranganKemasan").val();

        if ($(".kemasanForm").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // update detail
                    if(row_detail)
                    {
                        let new_list_items = [];
                        let tag_html = "";
                        row_kemasan = 0;

                        list_kemasan.map(item => {
                            row_kemasan = row_kemasan + 1;
                            if (item.row == row_detail) {
                                tag_html += `<tr>`;
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                                tag_html += row_kemasan;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                                tag_html += jumlah;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                                tag_html += kode;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                                tag_html += uraian;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                                tag_html += merk;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;">`;
                                tag_html += `<button onclick='deleteRowKemasan(${row_kemasan})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                new_list_items.push({
                                    "row": row_kemasan,
                                    "jumlah": jumlah,
                                    "kode": kode,
                                    "uraian": uraian,
                                    "merk": merk,
                                    "keterangan": keterangan
                                })
                            }
                            else
                            {
                                tag_html += `<tr>`;
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                                tag_html += row_kemasan;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                                tag_html += item.jumlah;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                                tag_html += item.kode;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                                tag_html += item.uraian;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                                tag_html += item.merk;
                                tag_html += "</td>";
                                tag_html += `<td style="text-align: center;">`;
                                tag_html += `<button onclick='deleteRowKemasan(${row_kemasan})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";

                                new_list_items.push(item);
                            }
                        })

                        list_kemasan = new_list_items;
                        $(".body-kemasan-table").empty();
                        $(".body-kemasan-table").append(tag_html);

                        $(".kemasanModal").modal("hide");
                    }
                    // create detail
                    else
                    {
                        row_kemasan = row_kemasan + 1;

                        list_kemasan.push({
                            "row": row_kemasan,
                            "jumlah": jumlah,
                            "kode": kode,
                            "uraian": uraian,
                            "merk": merk,
                            "keterangan": keterangan
                        })

                        let tag_html = "";
                        
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                        tag_html += row_kemasan;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                        tag_html += jumlah;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                        tag_html += kode;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                        tag_html += uraian;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${jumlah}" data-kode="${kode}" data-uraian="${uraian}" data-merk="${merk}" data-keterangan="${keterangan}" data-row="${row_kemasan}">`;
                        tag_html += merk;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowKemasan(${row_kemasan})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";
                        $(".body-kemasan-table").append(tag_html);

                        $(".kemasanModal").modal("hide");
                    }
                }
            })
        }
    })

    // get invoice date by selected no invoice
    $(".noInvoice").change(function() {
        if($(".noInvoice option:selected").val())
        {
            let date =  $(".noInvoice option:selected").attr("data-date");
            $(".tanggalInvoice").val(date);
        }
        else
        {
            $(".tanggalInvoice").val('');
        }
    })

    const deleteRowDokumen = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";

                $(".body-dokumen-table").empty()

                row_dokumen = 0;

                list_dokumen.map(item => {
                    row_dokumen = row_dokumen + 1;
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += row_dokumen;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += item.kode;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += item.jenis;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += item.no;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += item.tanggal;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowDokumen(${row_dokumen})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            "row": row_dokumen,
                            "kode": item.kode,
                            "jenis": item.jenis,
                            "no": item.no,
                            "tanggal": item.tanggal,
                            "keterangan": item.keterangan
                        })
                    }
                })

                list_dokumen = new_list_items;

                $(".body-dokumen-table").append(tag_html);
            }
        })
    }

    const deleteRowKontainer = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";

                $(".body-kontainer-table").empty()

                row_kontainer = 0;

                list_kontainer.map(item => {
                    row_kontainer = row_kontainer + 1;
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += row_kontainer;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += item.no;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += item.ukuran;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += item.tipe;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowKontainer(${row_kontainer})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            "row": row_kontainer,
                            "no": item.no,
                            "ukuran": item.ukuran,
                            "tipe": item.tipe,
                            "keterangan": item.keterangan
                        })
                    }
                })

                list_kontainer = new_list_items;

                $(".body-kontainer-table").append(tag_html);
            }
        })
    }

    const deleteRowKemasan = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";

                $(".body-kemasan-table").empty()

                row_kemasan = 0;

                list_kemasan.map(item => {
                    row_kemasan = row_kemasan + 1;
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += row_kemasan;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += item.jumlah;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += item.kode;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += item.uraian;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += item.merk;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowKemasan(${row_kemasan})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            "row": row_kemasan,
                            "jumlah": item.jumlah,
                            "kode": item.kode,
                            "uraian": item.uraian,
                            "merk": item.merk,
                            "keterangan": item.keterangan
                        })
                    }
                })

                list_kemasan = new_list_items;

                $(".body-kemasan-table").append(tag_html);
            }
        })
    }

    $(document).on('click', '.delete-dokumen', function() {
        let id = $(".idDokumen").val()
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";

                $(".body-dokumen-table").empty()

                row_dokumen = 0;

                list_dokumen.map(item => {
                    row_dokumen = row_dokumen + 1;
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += row_dokumen;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += item.kode;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += item.jenis;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += item.no;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-dokumen" data-kode="${item.kode}" data-jenis="${item.jenis}" data-no="${item.no}" data-tanggal="${item.tanggal}" data-keterangan="${item.keterangan}"  data-row="${row_dokumen}">`;
                        tag_html += item.tanggal;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowDokumen(${row_dokumen})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            "row": row_dokumen,
                            "kode": item.kode,
                            "jenis": item.jenis,
                            "no": item.no,
                            "tanggal": item.tanggal,
                            "keterangan": item.keterangan
                        })
                    }
                })

                list_dokumen = new_list_items;

                $(".body-dokumen-table").append(tag_html);
                $(".dokumenModal").modal("hide");
            }
        })
    })

    $(document).on('click', '.delete-kontainer', function() {
        let id = $(".idKontainer").val()
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";

                $(".body-kontainer-table").empty()

                row_kontainer = 0;

                list_kontainer.map(item => {
                    row_kontainer = row_kontainer + 1;
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += row_kontainer;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += item.no;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += item.ukuran;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += item.tipe;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kontainer" data-no="${item.no}" data-ukuran="${item.ukuran}" data-tipe="${item.tipe}" data-keterangan="${item.keterangan}" data-row="${row_kontainer}">`;
                        tag_html += item.keterangan;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowKontainer(${row_kontainer})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            "row": row_kontainer,
                            "no": item.no,
                            "ukuran": item.ukuran,
                            "tipe": item.tipe,
                            "keterangan": item.keterangan
                        })
                    }
                })

                list_kontainer = new_list_items;

                $(".body-kontainer-table").append(tag_html);
                $(".kontainerModal").modal("hide");
            }
        })
    })

    $(document).on('click', '.delete-kemasan', function() {
        let id = $(".idKemasan").val()
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";

                $(".body-kemasan-table").empty()

                row_kemasan = 0;

                list_kemasan.map(item => {
                    row_kemasan = row_kemasan + 1;
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += row_kemasan;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += item.jumlah;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += item.kode;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += item.uraian;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;" class="edit-table-kemasan" data-jumlah="${item.jumlah}" data-kode="${item.kode}" data-uraian="${item.uraian}" data-merk="${item.merk}" data-keterangan="${item.keterangan}" data-row="${row_kemasan}">`;
                        tag_html += item.merk;
                        tag_html += "</td>";
                        tag_html += `<td style="text-align: center;">`;
                        tag_html += `<button onclick='deleteRowKemasan(${row_kemasan})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            "row": row_kemasan,
                            "jumlah": item.jumlah,
                            "kode": item.kode,
                            "uraian": item.uraian,
                            "merk": item.merk,
                            "keterangan": item.keterangan
                        })
                    }
                })

                list_kemasan = new_list_items;

                $(".body-kemasan-table").append(tag_html);
                $(".kemasanModal").modal("hide");
            }
        })
    })

    // delete
    $(".delete-parent").click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                let id = $(".id").val();
                setLoading()
                $.ajax({
                    url: "<?= base_url("bea-cukai-bc-25/delete"); ?>",
                    data: {
                        id: id
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        csrf.val(response.token);
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    window.location.href = "<?= base_url("bea-cukai-bc-25"); ?>"
                                })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    },
                    onError: function(response) {
                        csrf.val(response.token);
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Gagal Dihapus, coba Lagi',
                            confirmButtonColor: '#4e73df',
                        })
                        stopLoading()
                    }
                });
            }
        })
    })
</script>
<?= $this->endSection(); ?>