<?= $this->include('layouts/template_pop_up.php') ?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="section-header">
                <div class="col-button-tambah-spp">
                    <a class="btn btn-warning btn-print float-right" href="<?= base_url('biaya-eskpor/print/' . encrypt($dataBiayaEskpor['id'])) ?>" target="_blank">
                        <i class="fa fa-download"></i> PRINT
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col ">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DETAIL BIAYA EKSPOR</label>
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
                                    <?= $dataBiayaEskpor['no_invoice'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Tanggal Invoice</td>
                                <td>:</td>
                                <td>
                                    <?= date('d/m/Y', strtotime($dataBiayaEskpor['tanggal_invoice']))  ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Departemen</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaEskpor['divisi'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Customer</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaEskpor['customer_name'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Destination</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaEskpor['destination'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>No Container</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaEskpor['no_container'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>No Seal</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaEskpor['no_seal'] ?>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-sm">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td style="width: 180px;">Nama Kapal</td>
                                <td style="width: 10px;">:</td>
                                <td>
                                    <?= $dataBiayaEskpor['nama_kapal'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Keberangkatan Kapal</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaEskpor['keberangkatan_kapal'] != null ? date('d/m/Y', strtotime($dataBiayaEskpor['keberangkatan_kapal'])) : ''  ?>
                                </td>
                            </tr>
                            <tr>
                                <td>No Surat Jalan</td>
                                <td>:</td>
                                <td><?= $dataBiayaEskpor['no_surat_jalan'] ?></td>
                            </tr>
                            <tr>
                                <td>Tgl Surat Jalan</td>
                                <td>:</td>
                                <td>
                                    <?= $dataBiayaEskpor['tanggal_surat_jalan'] != null ? date('d/m/Y', strtotime($dataBiayaEskpor['tanggal_surat_jalan'])) : ''  ?>
                                </td>
                            </tr>
                            <tr>
                                <td>No Kendaraan</td>
                                <td>:</td>
                                <td><?= $dataBiayaEskpor['no_kendaraan'] ?></td>
                            </tr>
                            <tr>
                                <td>Detail Kendaraan</td>
                                <td>:</td>
                                <td><?= $dataBiayaEskpor['no_kendaraan'] ?></td>
                            </tr>
                            <tr>
                                <td>Vendor/Pelayaran</td>
                                <td>:</td>
                                <td><?= $dataBiayaEskpor['nama_vendor'] ?></td>
                            </tr>
                            <tr>
                                <td>Nilai Faktur</td>
                                <td>:</td>
                                <td><?= number_format($dataBiayaEskpor['total_faktur'], 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col ">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">LIST BIAYA EKSPOR</label>
                    </div>
                </div>
            </div>

            <table class="table table-bordered dataTable nowrap table-hover-tobasurimi" id="taxTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 10px;">No</th>
                        <th scope="col">Detail Biaya</th>
                        <th scope="col">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1 ?>
                    <?php $totalBiaya = 0; ?>
                    <?php foreach ($dataBiayaEksporDetail as $d): ?>
                        <?php $totalBiaya += $d['nilai_biaya'] ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $d['uraian_biaya'] ?></td>
                            <td><?= number_format($d['nilai_biaya'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" style="text-align: right;">TOTAL BIAYA</td>
                        <td><?= number_format($totalBiaya, 2) ?></td>
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
                    <?php foreach ($dataBiayaEksporPajak as $d): ?>
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

            <div class="row">
                <div class="col ">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">LIST BARANG DI EKSPOR</label>
                    </div>
                </div>
            </div>

            <table class="table table-bordered dataTable nowrap table-hover-tobasurimi" id="taxTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 10px;">No</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Barang</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1 ?>
                    <?php foreach ($dataDetailBarang as $d): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $d['kode_barang'] ?></td>
                            <td><?= $d['barang_name'] ?></td>
                            <td><?= number_format($d['qty_barang'], 2) ?></td>
                            <td><?= number_format($d['harga_satuan'], 2) ?></td>
                            <td><?= number_format($d['total_harga'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>