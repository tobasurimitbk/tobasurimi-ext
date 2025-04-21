<?= $this->include('layouts/template_pop_up.php') ?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DETAIL BARANG DOKUMEN</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $bcPo['no_daftar'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">No Daftar</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $noAju ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">No Aju</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= date('d/m/Y', strtotime($bcPo['createdAt']))  ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Tanggal Dokumen</label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $bcPo['supplier_name'] ?>" type="text" class="form-control " id="" name="" placeholder="">
                        <label for="floatingInput">Supplier</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable " id="dataTable_supplier" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:5px;">No</th>
                                <th style="text-align: center;">Tgl PO</th>
                                <th style="text-align: center;">Tgl LPB</th>
                                <th style="text-align: center;">No LPB</th>
                                <th style="text-align: center;">No PO</th>
                                <th style="text-align: center;">No SPP</th>
                                <th style="text-align: center;">Kode</th>
                                <th style="text-align: center;">Barang</th>
                                <th style="text-align: center;">Qty PO</th>
                                <th style="text-align: center;">Qty Diterima</th>
                                <th style="text-align: center;">Qty Diterima (Konversi)</th>
                                <th style="text-align: center;">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                            <?php
                            $no = 1;
                            $totalQtyPo = 0;
                            $totalQtyDiterima = 0;
                            $totalQtyDiterimaKonversi = 0;
                            $totalHargaNumber = 0;
                            ?>
                            <?php foreach ($daftarPoUsed as  $d) : ?>
                                <?php
                                $totalQtyPo += $d['qty_po'];
                                $totalQtyDiterima += $d['qty_lpb'];
                                $totalQtyDiterimaKonversi += $d['qty_lpb_konversi'];
                                $totalHargaNumber += $d['harga'];
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $d['po_date'] ?></td>
                                    <td><?= $d['lpb_date'] ?></td>
                                    <td><?= $d['lpb_no'] ?></td>
                                    <td><?= $d['po_no'] ?></td>
                                    <td><?= $d['spp_no'] ?></td>
                                    <td><?= $d['kode_barang'] ?></td>
                                    <td><?= $d['barang_name'] ?></td>
                                    <td><?= floatval($d['qty_po']) ?></td>
                                    <td><?= floatval($d['qty_lpb']) ?></td>
                                    <td><?= floatval($d['qty_lpb_konversi']) ?></td>
                                    <td><?= number_format($d['harga'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="color:whitesmoke; background-color:#f2c996; color:black;">
                                <td style="text-align: right;" colspan="8">
                                    <b>
                                        GRAND TOTAL
                                    </b>
                                </td>
                                <td>
                                    <?= floatval($totalQtyPo) ?>
                                </td>
                                <td>
                                    <?= floatval($totalQtyDiterima) ?>
                                </td>
                                <td>
                                    <?= floatval($totalQtyDiterimaKonversi) ?>
                                </td>
                                <td>
                                    <?= number_format($totalHargaNumber, 2) ?>
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>

        </div>
    </div>
</section>