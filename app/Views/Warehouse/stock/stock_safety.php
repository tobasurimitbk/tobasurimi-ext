<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="section-header">
        <h1>Stock Safety</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Tipe Barang</th>
                                <th>Safety</th>
                                <th>Harus Restock</th>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "parent_barang";
    let sortType = "asc";
    let list_spek = [];
    var row_detail = 0;
    let changeParent = true;
</script>

<?= $this->endSection(); ?>