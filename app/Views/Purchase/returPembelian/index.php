<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Retur Pembelian</h1>
    <a class="btn btn-show-form btn-add float-right" href="<?= base_url("retur-pembelian/create"); ?>">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </a>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end row-col-spp">
            <div class="col-md-3 mb-3">
                <input class="form-control search form-out-search" placeholder="Search" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>No. Terima Faktur</th>
                            <th>Supplier</th>
                            <th>Nominal Faktur</th>
                            <th>Tanggal Penerimaan</th>
                            <th>Penerima</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table" style="cursor: pointer;">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>

<?= $this->endSection(); ?>