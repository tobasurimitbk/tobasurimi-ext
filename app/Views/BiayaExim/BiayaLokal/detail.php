<?= $this->include('layouts/template_pop_up.php') ?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="section-header">
                <div class="col-button-tambah-spp">
                    <a class="btn btn-warning btn-print float-right" href="<?= base_url('biaya-lokal/print/' . encrypt($dataBiayaLokal['id'])) ?>" target="_blank">
                        <i class="fa fa-download"></i> PRINT
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col ">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DETAIL BIAYA LOKAL</label>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-sm">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td style="width: 180px;">No Invoice</td>
                                <td style="width: 10px;">:</td>
                                <td>
                                    <?= $dataBiayaLokal['no_invoice'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Tanggal Invoice</td>
                                <td>:</td>
                                <td>
                                    <?= date('d/m/Y', strtotime($dataBiayaLokal['tanggal_invoice']))  ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Departemen</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaLokal['divisi'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Vendor / Pelayaran</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaLokal['nama_vendor'] ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col ">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">LIST BIAYA LOKAL</label>
                    </div>
                </div>
            </div>

            <table class="table table-bordered dataTable nowrap table-hover-tobasurimi" id="taxTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 10px;">No</th>
                        <th scope="col">Detail Biaya</th>
                        <th scope="col">Currency</th>
                        <th scope="col">Nilai</th>
                        <th scope="col">Exchange Rate</th>
                        <th scope="col">Nilai (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1 ?>
                    <?php $totalBiayaIdr = 0; ?>
                    <?php foreach ($dataBiayaLokalDetail as $d): ?>
                        <?php $totalBiayaIdr += $d['nilai_biaya_idr'] ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $d['uraian_biaya'] ?></td>
                            <td><?= $d['valas_name'] ?></td>
                            <td><?= number_format($d['nilai_biaya'], 2) ?></td>
                            <td><?= number_format($d['nilai_exchange_rate'], 2) ?></td>
                            <td><?= number_format($d['nilai_biaya_idr'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="5" style="text-align: right;">TOTAL BIAYA</td>
                        <td><?= number_format($totalBiayaIdr, 2) ?></td>
                    </tr>
                </tbody>

            </table>

            <div class="row">
                <div class="col ">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">LIST PENGENAAN PAJAK</label>
                    </div>
                </div>
            </div>

            <table class="table table-bordered dataTable nowrap table-hover-tobasurimi" id="taxTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 10px;">No</th>
                        <th scope="col">Tgl Faktur</th>
                        <th scope="col">No Faktur</th>
                        <th scope="col">Pajak</th>
                        <th scope="col">Nilai Pajak</th>
                        <th scope="col">Status</th>
                        <th scope="col">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1 ?>
                    <?php foreach ($dataBiayaLokalPajak as $d): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $d['tanggal_faktur_pajak'] ?></td>
                            <td><?= $d['no_faktur_pajak'] ?></td>
                            <td><?= $d['tax_name'] ?></td>
                            <td><?= number_format($d['nilai_pajak'], 2) ?></td>
                            <td><?= $d['status_pajak'] ?></td>
                            <td><?= $d['keterangan_pajak'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


        </div>
    </div>
</section>