<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section section-form">

    <div class="section-header">
        <h1 class="title-name"><?= !empty($bc30) ? "Update Dokumen BC 3.0" : "Tambah Dokumen BC 3.0" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right root-form-view" href="<?= base_url("bea-cukai-bc-30"); ?>">
                Batal
            </a>
            <?php if (!empty($bc30)) : ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="deleteAction()">
                            Hapus
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="postingAction()">
                            Posting
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bc30['status_posting'] == "0") : ?>
                    <?php if (can('Bea Cukai', 'BC 3.0', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Bea Cukai', 'BC 3.0', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            DATA BARANG UNTUK PEMBUATAN DOKUMEN BEA CUKAI 3.0
        </div>
        <div class="card-body">
            <form class="create-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id" class="id" value="<?= !empty($bc30) ? encrypt($bc30['id']) : '' ?>">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select company_tujuan_id" id="company_tujuan_id" name="company_tujuan_id" aria-label="Floating label select example">
                                <option value=""></option>

                            </select>
                            </select>
                            <label style="z-index: 1;">Pilih Customer</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3 mt-1" style="height: 50px;">
                            <select <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="form-select mutasi_global_id" id="mutasi_global_id" name="mutasi_global_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($bc30)) : ?>
                                    <option selected value="<?= $bc30['mutasi_global_id'] ?>">
                                        <?= $bc30['no_mutasi'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label style="z-index: 1;">Pilih Nomor Mutasi</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc30) ? $bc30['divisi'] : '' ?>" class="form-control divisi_asal_name" id="divisi_asal_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Departemen Asal</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled placeholder="Company Asal" value="<?= !empty($bc30) ? $bc30['warehouse_name'] : '' ?>" class="form-control warehouse_asal_name" id="warehouse_asal_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Warehouse Asal</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <div class="form-floating mb-3">
                                <input value="<?= !empty($bc30) ? $bc30['no_aju'] : "" ?>" name="no_aju" readonly type="text" id="no_aju" class="form-control no_aju" placeholder="">
                                <label>Nomor Pengajuan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button" onclick="noAjuShowModal()">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($bc30) ? ($bc30['status_posting'] == "1" ? "disabled" : "") : '' ?> value="<?= !empty($bc30) ? $bc30['no_daftar'] : "" ?>" autocomplete="one-time-code" type="number" class="form-control no_daftar" id="no_daftar" name="no_daftar" placeholder="No Daftar">
                            <label for="floatingInput">Nomor Daftar</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mt-2">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dipindahkan</label>
                </div>
                <div class="col-md-12 col-table-button-tts">


                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Tipe Barang</th>
                                    <th style="text-align: center;">Asal Barang</th>
                                    <th style="text-align: center;">No Dokumen</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">Tgl Penerimaan</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Qty Mutasi</th>
                                    <th style="text-align: center;">Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="10" style="text-align: center;">
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    var listData = [];
</script>

<?= $this->endSection(); ?>