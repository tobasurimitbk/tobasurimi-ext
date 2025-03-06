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
                                <th style="text-align: center;">Kode</th>
                                <th style="text-align: center;">Barang</th>
                                <th style="text-align: center;">Qty PO</th>
                                <th style="text-align: center;">Qty Diterima</th>
                                <th style="text-align: center;">Qty Diterima (Konversi)</th>
                                <th style="text-align: center;">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                            <?php $no = 1; ?>
                            <?php foreach ($daftarPoUsed as  $d) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $d['po_date'] ?></td>
                                    <td><?= $d['lpb_date'] ?></td>
                                    <td><?= $d['lpb_no'] ?></td>
                                    <td><?= $d['po_no'] ?></td>
                                    <td><?= $d['kode_barang'] ?></td>
                                    <td><?= $d['barang_name'] ?></td>
                                    <td><?= number_format($d['qty_po']) ?></td>
                                    <td><?= number_format($d['qty_lpb']) ?></td>
                                    <td><?= number_format($d['qty_lpb_konversi']) ?></td>
                                    <td><?= number_format($d['harga'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
</section>