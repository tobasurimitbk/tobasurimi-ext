<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Stock Adjusment</h1>
        <?php if (can("Inventori", "Stok Adjusment", "c")) : ?>
            <a href="<?= base_url('stock-adjusment/create') ?>" type="button" class="btn btn-show-form btn-add float-right">
                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php foreach ($dataDivisi as $divisi) : ?>
                                <option value="<?= $divisi["id"]; ?>" <?= !empty($dataPenerimaanBarang) ? ($dataPenerimaanBarang['divisi_id'] === $divisi["id"] ? "selected" : "") : ""; ?>><?= strtoupper($divisi["divisi"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Departemen</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">

                        </select>
                        <label style="z-index: 1;">Warehouse</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select status_stok" id="status_stok" name="status_stok" aria-label="Floating label select example">
                            <option value="ALL">SEMUA</option>
                            <option value="1">POSTED</option>
                            <option value="0">WAITING</option>
                        </select>
                        <label style="z-index: 1;">Status Adjusment</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Nomor Adjusment </label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th onclick="changeSort('parent_barang.parent_type')">No Adjusment</th>
                            <th onclick="changeSort('parent_barang.parent_name')">Tanggal</th>
                            <th onclick="changeSort('parent_barang.parent_name')">Departemen</th>
                            <th onclick="changeSort('parent_barang.parent_name')">Warehouse</th>
                            <th onclick="changeSort('barang_master.barang_name')">Keterangan</th>
                            <th onclick="changeSort('barang_master.barang_name')">Status</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>



<script>
    let sort = "createdAt";
    let sortType = "desc";
</script>

<?= $this->endSection(); ?>