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
            BC 2.7 - PEMBERITAHUAN PENGELUARAN UNTUK DIANGKUT DARI TEMPAT PENIMBUNAN BERIKAT KE TEMPAT PENIMBUNAN BERIKAT LAINNYA
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <!-- ROOT FORM -->
            <div class="root-form-view mt-3">

                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-lpb" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:5px;">No</th>
                                <th style="text-align: center;">Kode HS</th>
                                <th style="text-align: center;">Kode</th>
                                <th style="text-align: center;">Barang</th>
                                <th style="text-align: center;">Harga</th>
                                <th style="text-align: center;">Qty Keluar</th>
                                <th style="text-align: center;">Satuan Inventori</th>
                                <th style="text-align: center;">Satuan Bea Cukai</th>
                                <th style="text-align: center;">Status</th>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            $totalHarga = 0;
                            $bc27Model = new App\Models\BC27Model();
                            ?>
                            <?php foreach ($barang as $b) : ?>
                                <?php $detailBarang = $bc27Model->detailBarang($bc27['id'], $b['kode_barang'], $bc27['mutasi_global_id']); ?>
                                <tr style="text-align: center; cursor:pointer;" data-kode_barang="<?= encrypt($b['kode_barang']) ?>" data-id="<?= encrypt($bc27['id']) ?>">
                                    <td><?= $i++; ?></td>
                                    <td>
                                        <?php if ($detailBarang['bcDetail'] == null) : ?>
                                            -
                                        <?php else : ?>
                                            <?= $detailBarang['bcDetail']->posTarif ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $b['kode_barang'] ?></td>
                                    <td><?= $b['barang'] ?></td>
                                    <td><?= number_format($b['total_harga'], 2) ?></td>
                                    <td><?= $b['qty'] ?></td>
                                    <td><?= $b['satuan']  ?></td>
                                    <td>
                                        <?php if ($detailBarang['bcDetail'] == null) : ?>
                                            -
                                        <?php else : ?>
                                            <?= $detailBarang['bcDetail']->kodeSatuanBarang ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($detailBarang['bcDetail'] == null) : ?>
                                            <span class="badge badge-danger">
                                                BELUM DIISI
                                            </span>
                                        <?php else : ?>
                                            <?php if (count($detailBarang['bcDetail']->bahanBaku) == 0) : ?>
                                                <span class="badge badge-danger">
                                                    BELUM DIISI
                                                </span>
                                            <?php else : ?>
                                                <span class="badge badge-success">
                                                    SUDAH DIISI
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="action">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">Total</td>
                                <td style="text-align: center;"><?= number_format($totalHarga, 2) ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>


</section>

<script>
    var table = $('.table-list-lpb').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        paging: false,
        searching: false,
        ordering: false,
        info: false,
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });


    $('.table-list-lpb tbody').on('click', 'tr', function() {
        if ($(this).hasClass('action')) {
            return;
        }
        var bc27Id = $(this).data('id');
        var kodeBarang = $(this).data('kode_barang');
        window.location.replace("<?= base_url('bea-cukai-bc-27/id/barang/') ?>" + bc27Id + '/' + kodeBarang);
    });
</script>


<?= $this->endSection(); ?>