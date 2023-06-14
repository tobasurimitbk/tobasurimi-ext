<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Surat Permintaan Pembelian</h1>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end mb-3">
            <div class="col-md-2">
                <input class="form-control search" placeholder="Search" value="" />
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Tipe SPP</th>
                            <th>No. SPP</th> 
                            <th>Departemen</th> 
                            <th>Jenis Order</th> 
                            <th>Total Harga</th> 
                            <th>Tanggal Order</th> 
                            <th>Status</th> 
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