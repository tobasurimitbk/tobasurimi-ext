<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Detail Stok</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-list"); ?>">
                Batal
            </a>

        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Barang</label>
                </div>
            </div>
            <form class="create-form">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled autocomplete="one-time-code" value="" type="text" class="form-control bg-white" id="" name="" placeholder="">
                            <label for="floatingInput">Tipe Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled autocomplete="one-time-code" value="" type="text" class="form-control bg-white" id="" name="" placeholder="">
                            <label for="floatingInput">Kategori Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled autocomplete="one-time-code" value="" type="text" class="form-control bg-white" id="" name="" placeholder="">
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled autocomplete="one-time-code" value="" type="text" class="form-control bg-white" id="" name="" placeholder="">
                            <label for="floatingInput">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled autocomplete="one-time-code" value="" type="text" class="form-control bg-white" id="" name="" placeholder="">
                            <label for="floatingInput">Kode / Nama Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled autocomplete="one-time-code" value="" type="text" class="form-control bg-white" id="" name="" placeholder="">
                            <label for="floatingInput">Satuan</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Stok Per Dokumen</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select bc_id" id="bc_id_stok_per_dokumen" name="bc_id_stok_per_dokumen" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($jenisDokAju as $j) : ?>
                                <option value="<?= $j->id ?>">
                                    <?= $j->value ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="0">NON PABEAN</option>

                        </select>
                        <label for="floatingInput" style="z-index: 1;">Dokumen Pabean</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search_stok_per_dokumen" id="search_stok_per_dokumen" name="search_stok_per_dokumen" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Aju </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Dokumen Pabean</th>
                                <th>No Aju</th>
                                <th>Stok Satuan 1</th>
                                <th>Stok Satuan 2</th>
                                <th>Stok Satuan 3</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Inisiasi Stok</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pemasukkan Barang</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Dari Adjusment</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pemasukkan Barang Dari Produksi</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pengeluaran Barang Ke Produksi</label>
                </div>
            </div>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pengeluaran Barang</label>
                </div>
            </div>


        </div>
    </div>
</section>

<script>
    $('#bc_id_stok_per_dokumen').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $("#bc_id_stok_per_dokumen")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
</script>



<?= $this->endSection(); ?>