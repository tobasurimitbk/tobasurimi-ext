<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1>Tanda Terima Faktur Import</h1>
    <a class="btn btn-show-form btn-add float-right" href="<?= base_url("terima-faktur-import/create"); ?>"
        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
    </a>
</div>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end row-col-spp">
            <div class="col mb-3">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateStart" id="dateStart" name="dateStart" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateStart"></i>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="input-group input-group-password">
                    <input class="form-control input-picker dateEnd" id="dateEnd" name="dateEnd" placeholder="Tanggal">
                    <div class="input-group-prepend group-prepend-password align-items-center">
                        <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-dateEnd"></i>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
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
                            <th>Dari</th>
                            <th>Nominal Faktur</th>
                            <th>Tanggal Jatuh Tempo</th>
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

<script>
$(document).ready(function() {
    $(".dateStart").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });
})
</script>

<?= $this->endSection(); ?>