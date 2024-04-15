<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($biayaKepiting) ? "Tambah Biaya Kepiting" : "Update Biaya Kepiting" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("biaya-udang"); ?>">
                Batal
            </a>
            <?php if (!empty($biayaKepiting)) : ?>
                <?php if ($biayaKepiting['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($biayaKepiting['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($biayaKepiting['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("biaya-udang/print/"); ?><?= encrypt($biayaKepiting['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Jasa Vendor', 'Biaya Kepiting', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("biaya-udang/print/"); ?><?= encrypt($biayaKepiting['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
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
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Barang Masuk Vendor</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($biayaKepiting) ? encrypt($biayaKepiting['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($biayaKepiting) ? 'disabled=true' : ''; ?> value="<?= !empty($biayaKepiting) ? $biayaKepiting['no_pembayaran'] : "PAY-UDG/" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_pembayaran" id="no_pembayaran" name="no_pembayaran" placeholder="No. Rebus">
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                                <div style="<?= !empty($biayaKepiting) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($biayaKepiting) ? 'disabled' : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($biayaKepiting) ? $biayaKepiting['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($biayaKepiting) ? 'disabled' : '' ?> class="form-select jasa_vendor_in_id" id="jasa_vendor_in_id" name="jasa_vendor_in_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($jasaVendorIn)) : ?>
                                    <?php foreach ($jasaVendorIn as $j) : ?>
                                        <option data-warehouse_id="<?= $j['warehouse_id'] ?>" data-vendor="<?= strtoupper($j['name']) ?>" data-divisi="<?= strtoupper($j['divisi']) ?>" value="<?= $j['id'] ?>">
                                            <?= $j['no_penerimaan_surat_jalan'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else : ?>

                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih No Surat Jalan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($biayaKepiting) ? $jasaVendorInDetail['name'] : '' ?>" autocomplete="one-time-code" disabled type="text" class="form-control vendor" id="vendor" name="vendor" placeholder="Vendor">
                            <label for="floatingInput">Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($biayaKepiting) ? $jasaVendorInDetail['divisi'] : '' ?>" autocomplete="one-time-code" disabled type="text" class="form-control divisi" id="divisi" name="divisi" placeholder="Departemen">
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($biayaKepiting) ? ($biayaKepiting['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($biayaKepiting) ? $biayaKepiting['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>


            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dibayar</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="4"></th>
                                    <th style="text-align: center;" colspan="2">Kg BB</th>
                                    <th style="text-align: center;" colspan="4">Upah Kopek Yang Dibayar</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tanggal Masuk</th>
                                    <th style="text-align: center;">Tanggal Keluar</th>
                                    <th style="text-align: center;">Barang</th>


                                    <th style="text-align: center;">Me</th>

                                    <th style="text-align: center;">KG REBUS</th>
                                    <th style="text-align: center;">KG DAGING FAUZY</th>
                                    <th style="text-align: center;">KG DAGING CN</th>

                                    <th style="text-align: center;">Kg Daging</th>
                                    <th style="text-align: center;">Ratio</th>
                                    <th style="text-align: center;">TB Harga</th>
                                    <th style="text-align: center;">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="12" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>



<?= $this->endSection(); ?>