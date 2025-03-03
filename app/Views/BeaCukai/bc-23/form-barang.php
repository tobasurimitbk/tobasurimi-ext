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
            BC 2.3 - PEMBERITAHUAN IMPOR BARANG UNTUK DITIMBUN DI TEMPAT PENIMBUNAN BERIKAT
        </div>
        <div class="card-body">
            <?php include_once('nav.php') ?>
            <?= csrf_field() ?>
            <!-- ROOT FORM -->
            <div class="root-form-view mt-3">
                <table width="100%" class="mb-3">
                    <tbody>
                        <tr style="color: black;">
                            <td width="150px"><b>Nama Supplier</b></td>
                            <td width="10px">:</td>
                            <td><?= $bcPo['supplier_name'] ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-lpb" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:5px;">No</th>
                                <th style="text-align: center;">Kode HS</th>
                                <th style="text-align: center;">Tgl PO</th>
                                <th style="text-align: center;">Tgl LPB</th>
                                <th style="text-align: center;">No LPB</th>
                                <th style="text-align: center;">No PO</th>
                                <th style="text-align: center;">Kode</th>
                                <th style="text-align: center;">Barang</th>
                                <th style="text-align: center;">Qty PO</th>
                                <th style="text-align: center;">Qty Diterima</th>
                                <th style="text-align: center;">Harga</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $qtyPoTotal = 0;
                            $qtyLpbTotal = 0;
                            $hargaTotal = 0;
                            $bcBarangModel = new App\Models\BCBarangModel();
                            ?>
                            <?php foreach ($lpbDetail as $l) : ?>
                                <?php
                                $qtyPoTotal += $l['qty_po'];
                                $qtyLpbTotal += $l['qty_lpb'];
                                $hargaTotal += $l['harga'];
                                ?>
                                <?php $bcDokumenBarang =  $bcBarangModel->where('penerimaan_barang_id', $l['penerimaan_barang_id'])->where('barang1_id', $l['barang1_id'])->first(); ?>
                                <tr style="cursor: pointer;" data-bc_purchase_order_id="<?= encrypt($bcPo['id']) ?>" data-penerimaan_barang_id="<?= encrypt($l['penerimaan_barang_id']) ?>" data-barang1_id="<?= encrypt($l['barang1_id']) ?>">
                                    <td style="text-align: center;"><?= $no++; ?></td>
                                    <td style="text-align: center;"><?= $bcDokumenBarang == null ? "-" : $bcDokumenBarang['pos_tarif'] ?></td>
                                    <td style="text-align: center;"><?= $l['po_date'] ?></td>
                                    <td style="text-align: center;"><?= $l['lpb_date'] ?></td>
                                    <td style="text-align: center;"><?= $l['lpb_no'] ?></td>
                                    <td style="text-align: center;"><?= $l['po_no'] ?></td>
                                    <td style="text-align: center;"><?= $l['kode_barang'] ?></td>
                                    <td style="text-align: center;"><?= $l['barang_name'] ?></td>
                                    <td style="text-align: center;"><?= number_format($l['qty_po']) ?></td>
                                    <td style="text-align: center;"><?= number_format($l['qty_lpb']) ?></td>
                                    <td style="text-align: center;"><?= number_format($l['harga'], 2) ?></td>
                                    <td style="text-align: center;" class="body-table-info-status-barang-root-view" data-id="<?= encrypt($l['penerimaan_barang_id']) ?>">
                                        <?php if ($bcDokumenBarang == null) : ?>
                                            <span class="badge badge-danger">
                                                BELUM DIISI
                                            </span>
                                        <?php else : ?>
                                            <span class="badge badge-success">
                                                SUDAH DIISI
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="action">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right;">Total</td>
                                <td style="text-align: center;"><?= $qtyPoTotal ?></td>
                                <td style="text-align: center;"><?= $qtyLpbTotal ?></td>
                                <td style="text-align: center;"><?= number_format($hargaTotal, 2) ?></td>
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
        var bcPoId = $(this).data('bc_purchase_order_id');
        var penerimaanBarangId = $(this).data('penerimaan_barang_id');
        var barang1Id = $(this).data('barang1_id');

        window.location.replace("<?= base_url('bea-cukai-bc-23/id/barang/') ?>" + bcPoId + '/' + penerimaanBarangId + '/' + barang1Id);
    });
</script>


<?= $this->endSection(); ?>