<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section list">
<div class="section-header">
    <h1>Surat Permintaan Pembelian</h1>
    <button class="btn btn-show-form btn-add float-right">
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </button>
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

<section class="section add" style="display: none;">
<div class="section-header">
    <h1>Tambah</h1>
    <button class="btn btn-hide-form btn-add">
        Batal
    </button>
</div>
<div class="card">
    <div class="card-body">

    </div>
</div>
</section>

<script>
    $(document).ready(function() {
        $(".btn-show-form").click(function() {
            $(".list").css("display", "none");
            $(".add").css("display", "");
        })

        $(".btn-hide-form").click(function() {
            $(".list").css("display", "");
            $(".add").css("display", "none");
        })
    })
</script>
<?= $this->endSection(); ?>