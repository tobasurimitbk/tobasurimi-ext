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
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("bea-cukai-bc-23"); ?>">
                Batal
            </a>
            <?php if (!empty($dataBC)) : ?>
                <?php if ($dataBC->status_posting === "Belum Posting") : ?>
                    <button class="btn btn-hapus delete-parent float-right">
                        Hapus
                    </button>
                    <button class="btn btn-success posting-spp float-right">
                        Posting
                    </button>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-bc" role="form" method="POST" enctype="multipart/form-data">
                <label class="form-label font-weight-bold lable-title mt-3">
                    Informasi Barang
                </label>
                <?= csrf_field() ?>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= $lpb->no_penerimaan_barang ?>" readonly autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker">
                            <label for="floatingInput">Nomor LPB</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= $lpb->tanggal ?>" autocomplete="one-time-code" type="text" placeholder="" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal Penerimaan</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;width:10px;">No</th>
                                        <th style="text-align: center;">No PO</th>
                                        <th style="text-align: center;">Kode Barang</th>
                                        <th style="text-align: center;">Nama Barang</th>
                                        <th style="text-align: center;">Jmlh Order</th>
                                        <th style="text-align: center;">Jmlh Diterima</th>
                                        <th style="text-align: center;">Sisa</th>
                                        <th style="text-align: center;">Sub Total</th>
                                        <th style="text-align: center;">Action</th>

                                    </tr>
                                </thead>
                                <tbody class="body-dokumen-table" id="body-dokumen-table">
                                    <?php $no = 1; ?>
                                    <?php foreach ($lpbDetail as $l) : ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select kppbcBongkar" name="kppbcBongkar" id="kppbcBongkar" aria-label="Floating label select example">
                                <option value="">
                                    - Kantor KPPBC Bongkar -
                                </option>

                            </select>
                            <label for="floatingInput">KPPBC Bongkar</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select kppbcPengawas" name="kppbcPengawas" id="kppbcPengawas" aria-label="Floating label select example">
                                <option value="">
                                    - Kantor KPPBC Pengawas -
                                </option>

                            </select>
                            <label for="floatingInput">KPPBC Pengawas</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select kodeTujuanTpb" name="kodeTujuanTpb" id="kodeTujuanTpb" aria-label="Floating label select example">
                                <option value="">
                                    - PILIH TUJUAN -
                                </option>

                            </select>
                            <label for="floatingInput">Pilih Tujuan</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Supplier
                </label>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select namaSupplier" name="namaSupplier" id="namaSupplier" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Nama Supplier -
                                </option>

                            </select>
                            <label for="floatingInput">Nama Supplier</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->country_name . ' (' . $dataBC->country_code . ')' : '' ?>" autocomplete="one-time-code" readonly name="negara" type="text" placeholder="Negara Supplier" class="form-control negara target input-picker">
                            <label for="floatingInput">Negara</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3">
                            <textarea <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> name="alamat" class="form-control alamat text-area-all" readonly><?= (!empty($dataBC)) ? $dataBC->supplier_address : '' ?></textarea>
                            <label for="floatingInput">Alamat</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Importir
                </label>
                <div class="row mt-2">
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select jenisIdentitasImportir" id="jenisIdentitasImportir" name="jenisIdentitasImportir" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Jenis Identitas -
                                </option>

                            </select>
                            <label for="floatingInput">Jenis Identitas</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_identitas : '' ?>" autocomplete="one-time-code" name="identitasImportir" type="text" placeholder="Identitas" class="form-control target input-picker">
                            <label for="floatingInput">Identitas</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_name : '' ?>" autocomplete="one-time-code" name="namaImportir" type="text" placeholder="Nama Importir" class="form-control target input-picker">
                            <label for="floatingInput">Nama Importir</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->tpb_no : '' ?>" autocomplete="one-time-code" name="noIzinTPBImportir" type="text" placeholder="No Izin TPB" class="form-control target input-picker">
                            <label for="floatingInput">No Izin TPB</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select jenisAPIImportir" id="jenisAPIImportir" name="jenisAPIImportir" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Jenis API -
                                </option>
                            </select>
                            <label for="floatingInput">Jenis API</label>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->importir_api : '' ?>" autocomplete="one-time-code" name="APIImportir" type="text" placeholder="APIImportir" class="form-control target input-picker">
                            <label for="floatingInput">API</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> name="alamatImportir" class="form-control text-area-all"><?= (!empty($dataBC)) ? $dataBC->importir_address : '' ?></textarea>
                            <label for="floatingInput">Alamat</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Pemilik Barang
                </label><br>
                <label class="mt-2">
                    Sama dengan data importir
                </label>
                <div class="form-control border-0 custom-toggle-switch">
                    <div class="form-check form-switch form-switch-lg">
                        <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> <?= (!empty($dataBC)) ? ($dataBC->pemilik_barang ? 'checked' : '') : '' ?> class="form-check-input" type="checkbox" name="switchPemilikBarang" id="switchPemilikBarang">
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
                    PPJK
                </label>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->ppjk_npwp : '' ?>" autocomplete="one-time-code" name="PpjkNpwp" type="text" placeholder="Identitas (NPWP)" class="form-control target input-picker">
                            <label for="floatingInput">NPWP (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->ppjk_name : '' ?>" autocomplete="one-time-code" name="PpjkNama" type="text" placeholder="Nama (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Nama (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->ppjk_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->ppjk_date)) : "") : '' ?>" autocomplete="one-time-code" name="PpjkTanggal" type="text" placeholder="Tanggal PPJK (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal PPJK (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->ppjk_no : '' ?>" autocomplete="one-time-code" name="PpjkNo" type="text" placeholder="No PPJK (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">No PPJK (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3">
                            <textarea <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> name="PpjkAlamat" class="form-control text-area-all"><?= (!empty($dataBC)) ? $dataBC->ppjk_address : '' ?></textarea>
                            <label for="floatingInput">Alamat (Opsional)</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Pengangkutan
                </label>
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select caraPengangkutan" name="caraPengangkutan" id="caraPengangkutan" aria-label="Floating label select example">
                                <option value="">
                                    - Cara Pengangkutan -
                                </option>
                            </select>
                            <label for="floatingInput">Cara Pengangkutan</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->pengangkutan_sarana : '' ?>" autocomplete="one-time-code" name="namaSaranaPengangkut" type="text" placeholder="Nama Sarana Pengangkut" class="form-control target input-picker">
                            <label for="floatingInput">Nama Sarana Pengangkut</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="row">
                            <div class="col-sm">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->voy_no : '' ?>" autocomplete="one-time-code" name="noVoyFlight" type="text" placeholder="No Voy/Flight" class="form-control target input-picker">
                                    <label for="floatingInput">No Voy / Flight</label>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select pengangkutanNegara" id="pengangkutanNegara" name="pengangkutanNegara" aria-label="Floating label select example">
                                        <option value="">
                                            - Pilih Negara -
                                        </option>
                                    </select>
                                    <label for="floatingInput">Negara</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->kode_pelabuhan_muat : '' ?>" autocomplete="one-time-code" name="pelabuhanMuat" type="text" placeholder="Pelabuhan Muat" class="form-control target input-picker">
                            <label for="floatingInput">Pelabuhan Muat</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->kode_pelabuhan_transit : '' ?>" autocomplete="one-time-code" name="pelabuhanTransit" type="text" placeholder="Pelabuhan Transit" class="form-control target input-picker">
                            <label for="floatingInput">Pelabuhan Transit</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->kode_pelabuhan_bongkar : '' ?>" autocomplete="one-time-code" name="pelabuhanBongkar" type="text" placeholder="Pelabuhan Bongkar" class="form-control target input-picker">
                            <label for="floatingInput">Pelabuhan Bongkar</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Dokumen
                </label>
                <div class="row mt-2">
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select noInvoice" id="noInvoice" name="noInvoice" aria-label="Floating label select example">
                                <option value="" data-date="">
                                    - Pilih Nomor Invoice -
                                </option>
                            </select>
                            <label for="floatingInput">No Invoice</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->tanggal_invoice !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->tanggal_invoice)) : "") : '' ?>" readonly autocomplete="one-time-code" name="tanggalInvoice" type="text" placeholder="Tanggal Invoice" class="tanggalInvoice form-control target input-picker">
                            <label for="floatingInput">Tanggal Invoice</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->fasilitas_import_no : '' ?>" autocomplete="one-time-code" name="noFasilitasImport" type="text" placeholder="Nomor Fasilitas Import (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Nomor Fasilitas Import (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->fasilitas_import_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->fasilitas_import_date)) : "") : '' ?>" autocomplete="one-time-code" name="tanggalFasilitasImport" type="text" placeholder="Tanggal Fasilitas Import (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal Fasilitas Import (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->fasilitas_import_code : '' ?>" autocomplete="one-time-code" name="kodeFasilitasImport" type="text" placeholder="Kode Fasilitas Import (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Kode Fasilitas Import (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->lc_no : '' ?>" autocomplete="one-time-code" name="noLc" type="text" placeholder="No LC (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">No LC (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->lc_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->lc_date)) : "") : '' ?>" autocomplete="one-time-code" name="tanggalLc" type="text" placeholder="Tanggal LC (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal LC (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->bl_no : '' ?>" autocomplete="one-time-code" name="noBl" type="text" placeholder="No B/L (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">No B/L (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->bl_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->bl_date)) : "") : '' ?>" autocomplete="one-time-code" name="tanggalBl" type="text" placeholder="Tanggal B/L (Opsional)" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal B/L (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->bc_11_no : '' ?>" autocomplete="one-time-code" name="noBc" type="text" placeholder="No B.C 1.1" class="form-control target input-picker">
                            <label for="floatingInput">No B.C 1.1</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? ($dataBC->bc_11_date !== "0000-00-00" ? date("d/m/Y", strtotime($dataBC->bc_11_date)) : "") : '' ?>" autocomplete="one-time-code" name="tanggalBc" type="text" placeholder="Tanggal B.C 1.1" class="form-control target input-picker">
                            <label for="floatingInput">Tanggal B.C 1.1</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->bc_11_zip : '' ?>" autocomplete="one-time-code" name="kodePos" type="text" placeholder="Kode Pos" class="form-control target input-picker">
                            <label for="floatingInput">Kode Pos</label>
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

                        </tbody>
                    </table>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Penimbunan
            </label>
            <form class="create-form form-add-second" role="form" method="POST" enctype="multipart/form-data">
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? $dataBC->penimbunan : '' ?>" autocomplete="one-time-code" name="tempatPenimbunan" type="text" placeholder="Tempat Penimbunan" class="tempatPenimbunan form-control target input-picker">
                            <label for="floatingInput">Tempat Penimbunan</label>
                        </div>
                    </div>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2">
                    Harga
                </label>
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> class="form-select valuta" id="valuta" name="valuta" aria-label="Floating label select example">
                                <option value="">
                                    - Pilih Valuta -
                                </option>

                            </select>
                            <label for="floatingInput">Valuta</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->ndpbm, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="npdpbm" type="number" placeholder="NDPBM" class="npdpbm form-control target input-picker">
                            <label for="floatingInput">NDPBM</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->fob, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="fob" type="number" placeholder="FOB" class="fob form-control target input-picker">
                            <label for="floatingInput">FOB</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->freight, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="freight" type="number" placeholder="Freight" class="freight form-control target input-picker">
                            <label for="floatingInput">Freight</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->asuransi_type, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="tipeAsuransi" type="number" placeholder="Asuransi Luar Negeri / Dalam Negeri" class="tipeAsuransi form-control target input-picker">
                            <label for="floatingInput">Asuransi Luar Negeri / Dalam Negeri</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->cif_value, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="nilaiCif" type="number" placeholder="Nilai CIF" class="nilaiCif form-control target input-picker">
                            <label for="floatingInput">Nilai CIF</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= (!empty($dataBC)) ? ($dataBC->status_posting === 'Belum Posting' ? '' : 'disabled') : '' ?> value="<?= (!empty($dataBC)) ? formatter($dataBC->cif_price, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="nilaiCifRupiah" type="number" placeholder="Nilai CIF Rupiah" class="nilaiCifRupiah form-control target input-picker">
                            <label for="floatingInput">Nilai CIF Rupiah</label>
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

                        </tbody>
                    </table>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Barang
            </label>
            <form class="create-form form-add-third" role="form" method="POST" enctype="multipart/form-data">
                <div class="row mt-2">
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" name="bruto" type="number" placeholder="Bruto (Kg)" class="bruto form-control target input-picker">
                            <label for="floatingInput">Bruto (Kg)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" name="netto" type="number" placeholder="Netto (Kg)" class="netto form-control target input-picker">
                            <label for="floatingInput">Netto (Kg)</label>
                        </div>
                    </div>
                    <div class="col-sm-4 mt-1">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly value="<?= (!empty($dataBC)) ? formatter($dataBC->item_count, "STR_TO_FLOAT") : '' ?>" autocomplete="one-time-code" name="jumlahBarang" type="number" placeholder="Jumlah Barang" class="jumlahBarang form-control target input-picker">
                            <label for="floatingInput">Jumlah Barang</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th style="text-align: center;">kode Barang</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Pos Tarif / HS</th>
                                <th style="text-align: center;">Kategori</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-barang-table" id="body-barang-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-2">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;">No.</th>
                                <th style="text-align: center;">Jenis Pungutan</th>
                                <th style="text-align: center;">Ditangguhkan (Rp)</th>
                                <th style="text-align: center;">Dibebaskan (Rp)</th>
                                <th style="text-align: center;">Tidak Dipungut (Rp)</th>
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <label class="form-label font-weight-bold lable-title mt-2">
                Pengesahan
            </label>
            <br />
            <label class="form-label mt-2">
                Dengan ini saya menyatakan bertanggung jawab atas kebenaran hal-hal yang diberitahukan dalam pemberitahuan pabean ini.
            </label>
        </div>

    </div>
</section>


<script>

</script>
<?= $this->endSection(); ?>