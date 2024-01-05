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
                            <td width="150px"><b>Nomor LPB</b></td>
                            <td width="10px">:</td>
                            <td><?= $lpb->no_penerimaan_barang ?></td>
                        </tr>
                        <tr style="color: black; height: 20px;">
                            <td colspan="3"></td>
                        </tr>
                        <tr style="color: black;">
                            <td width="150px"><b>Tanggal Diterima</b></td>
                            <td width="30px">:</td>
                            <td><?= date('d/m/Y', strtotime($lpb->tanggal)) ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-list-lpb" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center;width:10px;">No</th>
                                <th style="text-align: center;">No PO</th>
                                <th style="text-align: center;">Kode HS</th>
                                <th style="text-align: center;">Kode Barang</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Jmlh Diterima (LPB)</th>
                                <th style="text-align: center;">Total Harga (LPB)</th>
                                <th style="text-align: center;">Jmlh Order (PO)</th>
                                <th style="text-align: center;">Total Harga (PO)</th>
                                <th style="text-align: center;">Status Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php
                            $jmlDiterimaLPB = 0;
                            $totalHargaLPB = 0;
                            $jmlOrderPO = 0;
                            $totalHargaPO = 0;
                            ?>
                            <?php foreach ($lpbDetail as $l) : ?>
                                <?php $sudahDiisi = false; ?>
                                <?php
                                $jmlDiterimaLPB += $l['jml_masuk'];
                                $totalHargaLPB += $l['sub_total'];
                                $jmlOrderPO +=  $l['qty'];
                                $totalHargaPO += $l['qty'] * ($l['harga'] + $l['harga_harian'] + $l['harga_bulanan']);
                                ?>
                                <tr style="cursor: pointer;" data-penerimaan_barang_id="<?= encrypt($lpb->id) ?>" data-penerimaan_barang_detail_id="<?= encrypt($l['penerimaan_barang_detail_id']) ?>">
                                    <td style="text-align: center;"><?= $no++; ?></td>
                                    <td style="text-align: center;"><?= $l['po_no'] ?></td>
                                    <td style="text-align: center;">-</td>
                                    <td style="text-align: center;"><?= $l['kode_barang'] ?></td>
                                    <td style="text-align: center;"><?= $l['nama_barang_dok'] ?></td>
                                    <td style="text-align: center;"><?= $l['jml_masuk'] ?></td>
                                    <td style="text-align: center;"><?= str_replace('Rp', '', toRupiah($l['sub_total'])) ?></td>
                                    <td style="text-align: center;"><?= $l['qty'] ?></td>
                                    <td style="text-align: center;"><?= str_replace('Rp', '', toRupiah($l['qty'] * ($l['harga'] + $l['harga_harian'] + $l['harga_bulanan']))) ?></td>
                                    <td style="text-align: center;" class="body-table-info-status-barang-root-view" data-id="<?= encrypt($l['id']) ?>">
                                        <?php if (!empty($bc23Detail)) : ?>
                                            <?php foreach ($bc23Json['detailBarangDok'] as $bj) : ?>
                                                <?php if ($bj['penerimaan_barang_detail_id'] == encrypt($l['id'])) : ?>
                                                    <span class="badge badge-success">
                                                        SUDAH DIISI
                                                    </span>
                                                    <?php $sudahDiisi = true; ?>
                                                    <?php break; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php if (!$sudahDiisi) : ?>
                                                <span class="badge badge-danger">
                                                    BELUM DIISI
                                                </span>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span class="badge badge-danger">
                                                BELUM DIISI
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
                                <td style="text-align: center;"><b>Total</b></td>
                                <td style="text-align: center;"><b><?= $jmlDiterimaLPB ?></b></td>
                                <td style="text-align: center;"><b><?= str_replace('Rp', '', toRupiah($totalHargaLPB)) ?></b></td>
                                <td style="text-align: center;"><b><?= $jmlOrderPO ?></b></td>
                                <td style="text-align: center;"><b><?= str_replace('Rp', '', toRupiah($totalHargaPO)) ?></b></td>
                                <td style="text-align: center;"></td>
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
        var penerimaanBarangId = $(this).data('penerimaan_barang_id');
        var penerimaanBarangDetailId = $(this).data('penerimaan_barang_detail_id');

        window.location.replace("<?= base_url('bea-cukai-bc-23/id/barang/') ?>" + penerimaanBarangId + '/' + penerimaanBarangDetailId);
    });
</script>


<?= $this->endSection(); ?>