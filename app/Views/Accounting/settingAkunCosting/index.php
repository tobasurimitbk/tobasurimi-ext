<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Setting Akun Costing</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mt-3">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Department" value="" />
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th colspan="2">Keterangan</th>
                                <th>Akun COA</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                            <tr>
                                <td>Keterangan</td>
                                <td>Akun COA</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" placeholder="Nama Department" id="parentName" name="parentName">
                                <label for="floatingInput">Nama Department</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select akun_ap_id" name="akun_ap_id" id="akun_ap_id">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($subAkuns)) {
                                                foreach ($subAkuns as $sub) {
                                            ?>
                                                    <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">AP</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select akun_ar_id" name="akun_ar_id" id="akun_ar_id">
                                            <option value="" data-code=""></option>
                                            <?php
                                            if (!empty($subAkuns)) {
                                                foreach ($subAkuns as $sub_ar) {
                                            ?>
                                                    <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="floatingInput">AR</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="button" class="btn btn-submit-form">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>