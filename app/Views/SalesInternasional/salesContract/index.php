<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Sales Contract</h1>
        <a class="btn btn-show-form btn-add float-right" href="<?= base_url("sales-kontrak/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a>
    </div>
    <div class="card">
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-borderd nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>No. Penawaran</th>
                        <th>Nama Customer</th>
                        <th>Valas</th>
                        <th>Expired Date</th>
                        <th>Tanggal Pembuatan</th>
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
    $(document).ready(function(){
        $('#dataTable').DataTable({
            "processing": true,
            "ordering": true,
            "info": true,
            "serverSide": true,
            "stateSave ": true,
            "scrollX": true,
            "order": [],
            "ajax": {
                "url": "<?=site_url('ajax-list-sales-contract') ;?>",
                "type": "POST",
                error: function(){ 
                    $(".dataTable-error").html("");
                    $("#dataTable").append('<tbody class="dataTable-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>');
                    $("#dataTable_processing").css("display","none");
        
                }
            },
            "columnDefs": [
                { 'targets': '_all', 'orderable': false, },
                { "className": 'dt-center', "targets": "_all" },
                { "className": 'dt-head-center', "targets": "_all" },
                { "width": "3%", "targets": [0,6] },
                { "width": "10%", "targets": [1] },
                { "width": "13%", "targets": [3] },
            ],
        });
    });
</script>

<?= $this->endSection(); ?>