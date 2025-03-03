<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<section class="section section-form">
    <?php include('header.php') ?>
    <div class="card">
        <div class="card-header" style="font-weight: bold; color:black;">
            BC 2.5 - PEMBERITAHUAN IMPOR BARANG DARI TEMPAT PENIMBUNAN BERIKAT
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-sm">
                    <label class="form-label font-weight-bold lable-title mt-4 mb-2">
                        Data Pungutan
                    </label>
                </div>
                <!-- <div class="col-sm">
                    <a href="#" type="button" class="btn btn-primary mt-4 mb-2" style="float: right;">
                        Generate Pungutan
                    </a>
                </div> -->
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-pungutan" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="text-align: center;width:10px;">No</th>
                            <th style="text-align: center;">Pungutan</th>
                            <th style="text-align: center;">Dibayar</th>
                            <th style="text-align: center;">Dibebaskan</th>
                            <th style="text-align: center;">Ditanggung Pemerintah</th>
                            <th style="text-align: center;">Sudah Dilunasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($payload->barang) != 0) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($pungutanList as $p) : ?>
                                <tr>
                                    <td style="text-align: center;"><?= $no++ ?></td>
                                    <td style="text-align: center;"><?= ($p['pungutan']) ?></td>
                                    <td style="text-align: center;"><?= number_format($p['dibayar'], 2) ?></td>
                                    <td style="text-align: center;"><?= number_format($p['dibebaskan'], 2) ?></td>
                                    <td style="text-align: center;"><?= number_format($p['ditanggung'], 2) ?></td>
                                    <td style="text-align: center;"><?= number_format($p['sudahDilunasi'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</section>

<script>
    // CSRF
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var tableListInformasiPungutan = $('.table-list-pungutan').DataTable({

        lengthChange: true,
        info: false,
        paging: false,
        searching: false,
        ordering: false,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });
</script>


<?= $this->endSection(); ?>