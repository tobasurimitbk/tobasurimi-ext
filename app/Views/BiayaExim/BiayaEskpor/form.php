<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataBiayaEskpor) ? "Update Biaya Ekspor" : "Tambah Biaya Ekspor" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("biaya-eskpor"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataBiayaEskpor)) { ?>
                <?php if (can('Biaya Exim', 'Biaya Ekspor', 'p')): ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= encrypt($dataBiayaEskpor['id']) ?>')">
                        Print
                    </button>
                <?php endif; ?>
                <?php if ($dataBiayaEskpor['status_bayar'] == 0) { ?>
                    <?php if (can('Biaya Exim', 'Biaya Ekspor', 'u')): ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Update
                        </button>
                    <?php endif; ?>
                <?php } ?>

            <?php } else { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data" id="form-parent">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Header</label>
                    </div>
                </div>
                <input autocomplete="one-time-code" value="<?= !empty($dataBiayaEskpor) ? encrypt($dataBiayaEskpor['id']) : '' ?>" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['no_invoice'] : 'AUTO GENERATE' ?>" <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : 'readonly' ?> class="form-control no_invoice" id="no_invoice" name="no_invoice" placeholder="No Invoice" required>
                                    <label for="floatingInput">No Invoice</label>
                                </div>
                                <div style="<?= !empty($dataBiayaEskpor) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal_invoice" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal Invoice" value="<?= !empty($dataBiayaEskpor) ? date('d/m/Y', strtotime($dataBiayaEskpor['tanggal_invoice'])) : date('d/m/Y')  ?>">
                                    <label for="floatingInput">Tanggal Invoice</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'disabled' : '') : '' ?> class="form-select divisi_id" name="divisi_id" id="divisi_id">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d): ?>
                                    <option <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'disabled' : '') : '' ?> class="form-select customer_id" name="customer_id" id="customer_id">
                                <option value=""></option>
                                <?php foreach ($dataCustomer as $d): ?>
                                    <option <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['customer_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'disabled' : '') : '' ?> class="form-select sales_order_export_id" name="sales_order_export_id" id="sales_order_export_id">
                                <option value=""></option>
                                <?php if (!empty($dataSalesOrderExport)): ?>
                                    <?php foreach ($dataSalesOrderExport as $d): ?>
                                        <option <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['sales_order_export_id'] == $d['sales_order_export_id'] ? 'selected' : '') : '' ?> value="<?= $d['sales_order_export_id'] ?>"><?= $d['sales_order_export_no'] . " - CONTAINER : " . $d['container'] . " PO : " . $d['po_no'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Pilih Sales Order Ekspor (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['payment_term'] : '' ?>" type="text" class="form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term (Opsional)">
                            <label for="floatingInput">Payment Term (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['destination'] : '' ?>" type="text" class="form-control destination" id="destination" name="destination" placeholder="Destination">
                            <label for="floatingInput">Destination (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['no_container_order_form'] : '' ?>" class="form-control no_container_order_form" id="no_container_order_form" name="no_container_order_form" placeholder="No Container">
                            <label for="floatingInput">No Container Order Form (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['po_no'] : '' ?>" class="form-control po_no" id="po_no" name="po_no" placeholder="No PO">
                            <label for="floatingInput">No PO (Opsional)</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-subtitle-modal">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold lable-title" style="margin-bottom: -30px;">List Barang yang di Ekspor</label>
                            </div>
                            <div class="col-md-6">
                                <button <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnAddBarangDetail" type="button" style="width: 90% !important;">
                                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableBarang" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Barang</th>
                                    <th style="text-align: right;">Qty</th>
                                    <th style="text-align: right;">Harga Satuan</th>
                                    <th style="text-align: right;">Total Harga <span id="txt_valas" class="mr-0"></span> <span id="txt_tipe_harga"></span></th>
                                    <th style="width: 100px; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="body-detail-table-barang">

                            </tbody>
                            <tfoot id="foot-detail-table-barang">
                                <tr>
                                    <td colspan="7">List Barang Eskpor Tidak Ada</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Detail Biaya & Pengenaan Pajak Ekspor</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control no_container" id="no_container" name="no_container" placeholder="No Container" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['no_container'] : '' ?>">
                            <label for="floatingInput">No Container</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control no_seal" id="no_seal" name="no_seal" placeholder="No Seal" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['no_seal']  : '' ?>">
                            <label for="floatingInput">No Seal</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control nama_kapal" id="nama_kapal" name="nama_kapal" placeholder="Nama Kapal" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['nama_kapal']  : '' ?>">
                            <label for="floatingInput">Nama Kapal</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker keberangkatan_kapal" id="keberangkatan_kapal" name="keberangkatan_kapal" placeholder="Keberangkatan Kapal" value="<?= !empty($dataBiayaEskpor) ? date('d/m/Y', strtotime($dataBiayaEskpor['keberangkatan_kapal'])) : ''  ?>">
                                    <label for="floatingInput">Keberangkatan Kapal</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="No Surat Jalan" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['no_surat_jalan']  : '' ?>">
                            <label for="floatingInput">No Surat Jalan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal_surat_jalan" id="tanggal_surat_jalan" name="tanggal_surat_jalan" placeholder="Sales Order Date" value="<?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['tanggal_surat_jalan'] != null ? date('d/m/Y', strtotime($dataBiayaEskpor['tanggal_surat_jalan'])) : '') : ''  ?>">
                                    <label for="floatingInput">Tanggal Surat Jalan (Opsional)</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control no_kendaraan" id="no_kendaraan" name="no_kendaraan" placeholder="No Kendaraan" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['no_kendaraan']  : '' ?>">
                            <label for="floatingInput">No Kendaraan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control detail_kendaraan" id="detail_kendaraan" name="detail_kendaraan" placeholder="Detail Kendaraan" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor['detail_kendaraan']  : '' ?>">
                            <label for="floatingInput">Detail Kendaraan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'disabled' : '') : '' ?> class="form-select vendor_pelayaran_id" name="vendor_pelayaran_id" id="vendor_pelayaran_id">
                                <option value=""></option>
                                <?php foreach ($dataVendorPelayaran as $d): ?>
                                    <option <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['vendor_pelayaran_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['nama_vendor'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Vendor / Pelayaran (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" disabled type="text" class="form-control total_biaya_prev" id="total_biaya_prev" name="total_biaya_prev">
                            <label for="floatingInput">Total Biaya Ekspor</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" disabled type="text" class="form-control total_ppn_prev" id="total_ppn_prev" name="total_ppn_prev">
                            <label for="floatingInput">Total Pajak PPN</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" disabled type="text" class="form-control total_pph_prev" id="total_pph_prev" name="total_pph_prev">
                            <label for="floatingInput">Total Potongan PPH</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" disabled type="text" class="form-control total_faktur_prev" id="total_faktur_prev" name="total_faktur_prev">
                            <label for="floatingInput">Nominal Faktur Final</label>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">List Biaya Ekspor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Pengenaan Pajak</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="col-subtitle-modal">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <button <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnAddBiaya" type="button" style="width: 90% !important;">
                                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="biayaEksporDetailTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 10px;">No</th>
                                            <th>Detail Biaya</th>
                                            <th style="width: 120px; text-align:right;">Nilai</th>
                                            <th style="width: 100px; text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-biaya-ekspor-detail" id="body-biaya-ekspor-detail">

                                    </tbody>
                                    <tfoot class="foot-biaya-ekspor-detail" id="foot-biaya-ekspor-detail">
                                        <tr>
                                            <td colspan="4">List Biaya Kosong</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="col-subtitle-modal">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <button <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor['status_bayar'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnAddPajakModal" type="button" style="width: 90% !important;">
                                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered dataTable nowrap table-hover-tobasurimi" id="taxTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="text-align: center; width:10px;">No</th>
                                                <th style="text-align: left;">Tgl Faktur Pajak</th>
                                                <th style="text-align: left;">No Faktur Pajak</th>
                                                <th style="text-align: left;">Pajak</th>
                                                <th style="text-align: left;">Nilai Pajak</th>
                                                <th style="text-align: left;">Status</th>
                                                <th style="text-align: left;">Keterangan</th>
                                                <th style="text-align: center; width:100px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table-pengenaan-pajak" id="body-table-pengenaan-pajak">
                                        </tbody>
                                        <tfoot class="foot-pengenaan-pajak" id="foot-pengenaan-pajak">
                                            <tr>
                                                <td colspan="8">List Pengenaan Pajak Kosong</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </form>

        </div>
    </div>
</section>

<div class="modal detail-modal" id="detailBiayaModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-biaya-eskpor"></label> Biaya Eskpor</h5>
            </div>
            <form id="form-biaya-ekspor" role="form" method="POST">
                <input type="hidden" name="id_biaya_ekspor_detail" id="id_biaya_ekspor_detail">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-5" style="height: 50px;">
                                <textarea name="uraian_biaya" id="uraian_biaya" class="form-control full-textarea" placeholder="Uraian Biaya"></textarea>
                                <label for="floatingInput">Uraian Biaya</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3 mt-5" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nilai_biaya" id="nilai_biaya" name="nilai_biaya" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Nilai Biaya">
                                <label for="floatingInput">Nilai Biaya</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideDetailBiaya">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitDetailBiaya">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="detailPengenaanPajakModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-pengenaan-pajak"></label> Pengenaan Pajak</h5>
            </div>
            <form id="form-pengenaan-pajak" role="form" method="POST">
                <input type="hidden" name="id_biaya_ekspor_pajak" id="id_biaya_ekspor_pajak">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker datepicker" id="tanggal_faktur_pajak" name="tanggal_faktur_pajak" placeholder="Tanggal Faktur Pajak">
                                    <label for="floatingInput">Tanggal Faktur Pajak</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="no_faktur_pajak" name="no_faktur_pajak" placeholder="No Faktur Pajak (Opsional)">
                                <label for="floatingInput">No Faktur Pajak (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tax_id" name="tax_id" id="tax_id">
                                    <option value=""></option>
                                    <?php foreach ($dataPajak as $d): ?>
                                        <option data-type_tax="<?= $d['type'] ?>" value="<?= $d['id'] ?>"><?= trim($d['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Pajak</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="nilai_pajak" name="nilai_pajak" placeholder="Nilai Pajak" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Nilai Pajak</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select tax_status" name="tax_status" id="tax_status">
                                    <option value=""></option>
                                    <option value="Pajak dipungut oleh negara">Pajak dipungut oleh negara</option>
                                    <option value="Pajak dikembalikan lagi">Pajak dikembalikan lagi</option>
                                </select>
                                <label for="floatingInput">Pilih Status Pajak</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 100px;">
                                <textarea name="keterangan_pajak" id="keterangan_pajak" class="form-control full-textarea keterangan_pajak" placeholder="Keterangan Pajak"></textarea>
                                <label for="floatingInput">Keterangan Pajak (Opsional)</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHidePengenaanPajak">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitPengenaanPajak">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="detailBarang" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-detail-barang"></label> Barang Ekspor</h5>
            </div>
            <form id="form-detail-barang" role="form" method="POST">
                <input type="hidden" name="id_detail_barang" id="id_detail_barang">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select barang_id" name="barang_id" id="barang_id">
                                    <option value=""></option>
                                    <?php foreach ($dataBarang as $d): ?>
                                        <option data-kode_barang="<?= $d['kode_barang'] ?>" data-barang_name="<?= $d['barang_name'] ?>" value="<?= $d['id'] ?>"><?= $d['kode_barang'] . " - " . $d['barang_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Barang</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select valas_id" name="valas_id" id="valas_id">
                                    <option value=""></option>
                                    <?php foreach ($dataValuta as $d): ?>
                                        <option data-valas_name="<?= $d['value'] ?>" value="<?= $d['id'] ?>"><?= $d['value'] . " - " . $d['description'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Currency</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="qty_barang" name="qty_barang" placeholder="Qty Barang" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Qty Barang</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d): ?>
                                        <option data-kode_satuan="<?= $d['kode_satuan'] ?>" value="<?= $d['id'] ?>"><?= $d['kode_satuan']  ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="harga_satuan" name="harga_satuan" placeholder="Qty Barang" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Harga Barang</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control" id="total_harga" name="total_harga" placeholder="Total Harga" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideDetailBarang">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitDetailBarang">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listBiayaEkspor = [];
    var listPajak = [];
    var listBarang = [];


    $(document).ready(function() {
        <?php if (!empty($dataBiayaEskpor)) { ?>

            <?php foreach ($dataDetailBarang as $d): ?>
                listBarang.push({
                    id_detail_barang: "<?= $d['id'] ?>",
                    barang_id: "<?= $d['barang_master_sales_id'] ?>",
                    kode_barang: "<?= $d['kode_barang'] ?>",
                    barang_name: "<?= $d['barang_name'] ?>",
                    valas_id: "<?= $d['valas_id'] ?>",
                    valas_name: "<?= $d['valas_name'] ?>",
                    qty_barang: <?= (float)$d['qty_barang'] ?>,
                    harga_satuan: <?= (float)$d['harga_satuan'] ?>,
                    total_harga: <?= (float)$d['total_harga'] ?>,
                    satuan_id: "<?= $d['satuan_id'] ?>",
                    kode_satuan: "<?= $d['kode_satuan'] ?>"
                });
            <?php endforeach; ?>

            <?php foreach ($dataBiayaEksporDetail as $d): ?>
                listBiayaEkspor.push({
                    id_biaya_ekspor_detail: "<?= $d['id'] ?>",
                    uraian_biaya: "<?= str_replace(array("\r", "\n"), '', trim($d['uraian_biaya'])) ?>",
                    nilai_biaya: <?= floatval($d['nilai_biaya']) ?>,
                });
            <?php endforeach ?>

            <?php foreach ($dataBiayaEksporPajak as $d): ?>
                listPajak.push({
                    id_biaya_ekspor_pajak: "<?= $d['id'] ?>",
                    no_faktur_pajak: "<?= $d['no_faktur_pajak'] ?>",
                    tanggal_faktur_pajak: "<?= date('d/m/Y', strtotime($d['tanggal_faktur_pajak'])) ?>",
                    tax_id: "<?= $d['tax_id'] ?>",
                    tax_name: "<?= trim($d['tax_name']) ?>",
                    nilai_pajak: <?= floatval($d['nilai_pajak']) ?>,
                    tax_status: "<?= trim($d['status_pajak']) ?>",
                    keterangan_pajak: "<?= $d['keterangan_pajak'] ?>",
                    type_tax: "<?= $d['type_tax'] ?>",
                });
            <?php endforeach ?>
            drawTableBarangEkspor(listBarang);
            drawTableBiayaEkspor(listBiayaEkspor);
            drawTablePengenaanPajak(listPajak);
            calculateTotalBiayaAndTax();
        <?php } else { ?>

        <?php } ?>


        $("#tanggal_invoice,#keberangkatan_kapal,#tanggal_surat_jalan,#tanggal_faktur_pajak").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('.customer_id').select2({
            placeholder: "Pilih Customer",
            theme: "bootstrap-5",
        }).change(function() {
            var customerId = $('#customer_id option:selected').val();
            getDropdownSalesOrderExport(customerId);
        });

        $('.divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.barang_id').select2({
            placeholder: "Pilih Barang",
            theme: "bootstrap-5",
            dropdownParent: $('#detailBarang')
        }).change(function() {});

        $('.satuan_id').select2({
            placeholder: "Pilih Satuan",
            theme: "bootstrap-5",
            dropdownParent: $('#detailBarang')
        }).change(function() {});

        $('.valas_id').select2({
            placeholder: "Pilih Currency",
            theme: "bootstrap-5",
            dropdownParent: $('#detailBarang')
        }).change(function() {});

        $('.sales_order_export_id').select2({
            placeholder: "Pilih Order Form Ekspor (Opsional)",
            theme: "bootstrap-5",
        }).change(function() {
            // Get Order Form Ekspor
            var salesOrderExportId = $('#sales_order_export_id option:selected').val();
            getOrderFormEkspor(salesOrderExportId);
        });

        $('.vendor_pelayaran_id').select2({
            placeholder: "Pilih Vendor / Pelayaran (Opsional)",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.tax_id').select2({
            placeholder: "Pilih Pajak",
            theme: "bootstrap-5",
            dropdownParent: $('#detailPengenaanPajakModal')
        }).change(function() {});

        $('.tax_status').select2({
            placeholder: "Pilih Status Pajak",
            theme: "bootstrap-5",
            dropdownParent: $('#detailPengenaanPajakModal')
        }).change(function() {});

        //CSS SELECT2 FLOATING LABEL
        $('.sales_order_export_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status, .customer_id,.barang_id,.valas_id,.satuan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.sales_order_export_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status, .customer_id,.barang_id,.valas_id,.satuan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.sales_order_export_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status, .customer_id,.barang_id,.valas_id,.satuan_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $("#form-parent").validate({
            rules: {
                no_invoice: {
                    required: true
                },
                tanggal_invoice: {
                    required: true
                },
                divisi_id: {
                    required: true
                },
                // sales_order_export_id: {
                //     required: true
                // },
                no_container: {
                    required: true
                },
                no_seal: {
                    required: true
                },
                nama_kapal: {
                    required: true
                },
                keberangkatan_kapal: {
                    required: true
                },
                // no_surat_jalan: {
                //     required: true
                // },
                // tanggal_surat_jalan: {
                //     required: true
                // }
            },
            messages: {
                no_invoice: {
                    required: "No invoice wajib diisi"
                },
                tanggal_invoice: {
                    required: "Tanggal invoice wajib diisi"
                },
                divisi_id: {
                    required: "Departemen wajib diisi"
                },
                // sales_order_export_id: {
                //     required: "Pilih Order form ekspor"
                // },
                no_container: {
                    required: "No container wajib diisi"
                },
                no_seal: {
                    required: "No seal wajib diisi"
                },
                nama_kapal: {
                    required: "Nama kapal wajib diisi"
                },
                keberangkatan_kapal: {
                    required: "Keberangkatan kapal wajib diisi"
                },
                // no_surat_jalan: {
                //     required: "No surat jalan wajib diisi"
                // },
                // tanggal_surat_jalan: {
                //     required: "Tanggal surat jalan wajib diisi"
                // }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        var validatorBiayaEkspor = $("#form-biaya-ekspor").validate({
            rules: {
                uraian_biaya: {
                    required: true
                },
                nilai_biaya: {
                    required: true
                },
            },
            messages: {
                uraian_biaya: {
                    required: "Uraian biaya wajib diisi"
                },
                nilai_biaya: {
                    required: "Nilai biaya wajib diisi"
                },
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        var validatorPengenaanPajak = $("#form-pengenaan-pajak").validate({
            rules: {
                tanggal_faktur_pajak: {
                    required: true
                },
                tax_id: {
                    required: true
                },
                nilai_pajak: {
                    required: true
                },
                tax_status: {
                    required: true
                },
            },
            messages: {
                tanggal_faktur_pajak: {
                    required: "Tanggal faktur pajak wajib diisi"
                },
                tax_id: {
                    required: "Pilih Pajak"
                },
                nilai_pajak: {
                    required: "Nilai pajak wajib diisi"
                },
                tax_status: {
                    required: "Pilih status pajak"
                },
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        var validatorDetailBarang = $("#form-detail-barang").validate({
            rules: {
                barang_id: {
                    required: true
                },
                valas_id: {
                    required: true
                },
                qty_barang: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                harga_satuan: {
                    required: true
                },
                total_harga: {
                    required: true
                },
            },
            messages: {
                barang_id: {
                    required: "Pilih barang"
                },
                valas_id: {
                    required: "Pilih currency"
                },
                qty_barang: {
                    required: "Qty barang wajib diisi"
                },
                satuan_id: {
                    required: "Pilih satuan"
                },
                harga_satuan: {
                    required: "Harga satuan wajib diisi"
                },
                total_harga: {
                    required: "Total harga wajib diisi"
                },
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $('#btnAddBiaya').click(function(e) {
            e.preventDefault();
            $("#label-biaya-eskpor").text("Tambah ");
            $('#detailBiayaModal').modal('show');
            resetFormBiayaEkspor();
        });

        $('#btnHideDetailBiaya').click(function(e) {
            e.preventDefault();
            $('#detailBiayaModal').modal('hide');
        });

        $('#btnAddBarangDetail').click(function(e) {
            e.preventDefault();
            $('#label-detail-barang').text('Tambah ');
            $('#detailBarang').modal('show');
            resetFormDetailBarang();
        });

        $('#btnHideDetailBarang').click(function(e) {
            e.preventDefault();
            $('#detailBarang').modal('hide');
        });

        $('#btnSubmitDetailBarang').click(function(e) {
            e.preventDefault();
            if ($('#form-detail-barang').valid()) {
                var idBarangDetail = $('#id_detail_barang').val();
                var barangId = $('#barang_id option:selected').val();
                var kodeBarang = $('#barang_id option:selected').data('kode_barang');
                var barangName = $('#barang_id option:selected').data('barang_name');
                var valasId = $('#valas_id option:selected').val();
                var valasName = $('#valas_id option:selected').data('valas_name');
                var satuanId = $('#satuan_id option:selected').val();
                var kodeSatuan = $('#satuan_id option:selected').data('kode_satuan');
                var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
                var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
                var totalHarga = destroyFormatRupiah($('#total_harga').val());

                if (idBarangDetail) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listBarang.length; i++) {
                        if (listBarang[i].id_detail_barang == idBarangDetail) {
                            index = i;
                            break;
                        }
                    }

                    listBarang[index].barang_id = barangId;
                    listBarang[index].kode_barang = kodeBarang;
                    listBarang[index].barang_name = barangName;
                    listBarang[index].valas_id = valasId;
                    listBarang[index].valas_name = valasName;
                    listBarang[index].qty_barang = qtyBarang;
                    listBarang[index].harga_satuan = hargaSatuan;
                    listBarang[index].total_harga = totalHarga;
                    listBarang[index].kode_satuan = kodeSatuan;
                    listBarang[index].satuan_id = satuanId;
                } else {
                    // INSERT
                    listBarang.push({
                        id_detail_barang: getID(),
                        barang_id: barangId,
                        kode_barang: kodeBarang,
                        barang_name: barangName,
                        valas_id: valasId,
                        valas_name: valasName,
                        qty_barang: qtyBarang,
                        harga_satuan: hargaSatuan,
                        total_harga: totalHarga,
                        kode_barang: kodeBarang,
                        satuan_id: satuanId,
                        kode_satuan: kodeSatuan
                    });
                }

                console.log(listBarang);
                drawTableBarangEkspor(listBarang);
                $('#detailBarang').modal('hide');
            }
        });

        $('#btnSubmitDetailBiaya').click(function(e) {
            e.preventDefault();
            if ($('#form-biaya-ekspor').valid()) {
                var idBiayaEksporDetail = $('#id_biaya_ekspor_detail').val();
                var uraianBiaya = $('#uraian_biaya').val();
                var nilaiBiaya = destroyFormatRupiah($('#nilai_biaya').val());

                if (idBiayaEksporDetail) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listBiayaEkspor.length; i++) {
                        if (listBiayaEkspor[i].id_biaya_ekspor_detail == idBiayaEksporDetail) {
                            index = i;
                            break;
                        }
                    }

                    listBiayaEkspor[index].uraian_biaya = uraianBiaya;
                    listBiayaEkspor[index].nilai_biaya = parseFloat(nilaiBiaya);

                } else {
                    // CREATE
                    listBiayaEkspor.push({
                        id_biaya_ekspor_detail: getID(),
                        uraian_biaya: uraianBiaya,
                        nilai_biaya: parseFloat(nilaiBiaya)
                    });
                }

                $('#detailBiayaModal').modal('hide');
                drawTableBiayaEkspor(listBiayaEkspor);
                calculateTotalBiayaAndTax();
            }
        });

        $('#btnAddPajakModal').click(function(e) {
            e.preventDefault();
            $('#detailPengenaanPajakModal').modal('show');
            $('#label-pengenaan-pajak').text("Tambah ");
            resetFormPengenaanPajak();
        });

        $('#btnHidePengenaanPajak').click(function(e) {
            e.preventDefault();
            $('#detailPengenaanPajakModal').modal('hide');
        });

        $('#qty_barang,#harga_satuan').keyup(function(e) {
            e.preventDefault();
            var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
            var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
            var totalHarga = parseFloat(qtyBarang) * parseFloat(hargaSatuan);
            $('#total_harga').val(greatFormatRupiah(totalHarga));
        });

        $('#btnSubmitPengenaanPajak').click(function(e) {
            e.preventDefault();
            if ($('#form-pengenaan-pajak').valid()) {
                var idBiayaEksporPajak = $('#id_biaya_ekspor_pajak').val();
                var tanggalFakturPajak = $('#tanggal_faktur_pajak').val();
                var noFakturPajak = $('#no_faktur_pajak').val();
                var taxId = $('#tax_id option:selected').val();
                var taxName = $('#tax_id option:selected').text();
                var nilaiPajak = destroyFormatRupiah($('#nilai_pajak').val());
                var taxStatus = $('#tax_status option:selected').val();
                var keteranganPajak = $('#keterangan_pajak').val();
                var typeTax = $('#tax_id option:selected').data('type_tax');

                if (idBiayaEksporPajak) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listPajak.length; i++) {
                        if (listPajak[i].id_biaya_ekspor_pajak == idBiayaEksporPajak) {
                            index = i;
                            break;
                        }
                    }

                    listPajak[index].type_tax = typeTax;
                    listPajak[index].tanggal_faktur_pajak = tanggalFakturPajak;
                    listPajak[index].no_faktur_pajak = noFakturPajak;
                    listPajak[index].tax_id = taxId;
                    listPajak[index].tax_name = taxName.trim();
                    listPajak[index].nilai_pajak = parseFloat(nilaiPajak);
                    listPajak[index].tax_status = taxStatus;
                    listPajak[index].keterangan_pajak = keteranganPajak;

                } else {
                    // Create
                    idBiayaEksporPajak = getID();
                    listPajak.push({
                        id_biaya_ekspor_pajak: idBiayaEksporPajak,
                        no_faktur_pajak: noFakturPajak,
                        tanggal_faktur_pajak: tanggalFakturPajak,
                        tax_id: taxId,
                        tax_name: taxName.trim(),
                        nilai_pajak: parseFloat(nilaiPajak),
                        tax_status: taxStatus,
                        keterangan_pajak: keteranganPajak,
                        type_tax: typeTax
                    });
                }

                drawTablePengenaanPajak(listPajak);
                $('#detailPengenaanPajakModal').modal('hide');
                calculateTotalBiayaAndTax();
            }
        });

        $(".btn-submit-parent").click(function() {
            if (listBiayaEkspor.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan biaya ekspor !',
                    confirmButtonColor: '#4e73df',
                })
            } else if (listBarang.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan barang yang akan diekspor !',
                    confirmButtonColor: '#4e73df',
                })
            } else {
                var id = $('#id').val();
                if ($("#form-parent").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: id ? 'Update Data ?' : 'Simpan Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Save',
                        cancelButtonText: 'Back',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            let id = $('#id').val();
                            let url = id == '' ? "<?= base_url('biaya-eskpor/create') ?>" : "<?= base_url('biaya-eskpor/update') ?>";
                            let data = new FormData(document.querySelector("#form-parent"));
                            let totalFaktur = destroyFormatRupiah($('#total_faktur_prev').val());
                            let totalFakturBeforeTax = destroyFormatRupiah($('#total_biaya_prev').val());

                            data.append("total_faktur_before_tax", totalFakturBeforeTax);
                            data.append("total_faktur", totalFaktur);
                            data.append("listBiayaEkspor", JSON.stringify(listBiayaEkspor));
                            data.append("listPajak", JSON.stringify(listPajak));
                            data.append("listBarang", JSON.stringify(listBarang));

                            $.ajax({
                                url: url,
                                data: data,
                                beforeSend: function(xhr) {
                                    setLoading();
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                complete: function() {
                                    stopLoading();
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                window.location.href = "<?= base_url("biaya-eskpor") ?>";
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                    }
                                }
                            });
                        }
                    })
                }
            }
        })
    })

    function getOrderFormEkspor(salesOrderExportId) {
        $.ajax({
            url: `<?= base_url("biaya-eskpor/get-order-form"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                sales_order_export_id: salesOrderExportId
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    // Readonly Append
                    if (res.dataSalesOrderExport != null) {
                        var dataSalesOrderExport = res.dataSalesOrderExport;
                        var dataSalesContract = res.dataSalesExportDetail.salesContract;

                        $('#payment_term').val(dataSalesContract.payment_term);
                        $('#destination').val(dataSalesOrderExport.dicharge_port);
                        $('#no_container_order_form').val(dataSalesOrderExport.container);
                        $('#po_no').val(dataSalesOrderExport.po_no);

                        $('#txt_valas').text("(" + dataSalesOrderExport.mata_uang + ")");
                        $('#txt_tipe_harga').text("(" + dataSalesOrderExport.tipe_harga + ")");

                        // Masukan Ke List Barang (Otomatis yah)
                        listBarang = [];
                        $.each(res.dataSalesExportDetail.salesContractDetailList, function(i, v) {
                            var satuanId = "";
                            var satuanName = "";

                            $.each(v.size_breakdown, function(j, k) {
                                satuanId = k.satuan_size_id;
                                satuanName = k.satuan_size_code;
                            });

                            listBarang.push({
                                id_detail_barang: v.id,
                                barang_id: v.barang_id,
                                kode_barang: v.kode_barang,
                                barang_name: v.barang_name,
                                valas_id: dataSalesOrderExport.valas_id,
                                valas_name: dataSalesOrderExport.mata_uang,
                                qty_barang: parseFloat(v.qty),
                                harga_satuan: parseFloat(v.harga),
                                total_harga: parseFloat(v.total_harga),
                                satuan_id: satuanId,
                                kode_satuan: satuanName
                            });
                        });
                        // DrawTable Detail Barang
                        console.log(res);
                        // $.each(res.)
                        console.log(listBarang);
                        drawTableBarangEkspor(listBarang);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
        })
    }

    function getDropdownSalesOrderExport(customerId) {
        $.ajax({
            url: `<?= base_url("biaya-eskpor/get-sales-order-export"); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                customer_id: customerId,
            },
            dataType: "json",
            success: function(res) {
                var poNo = "";
                var container = "";
                var result = "";
                $(".sales_order_export_id").empty();
                $(".sales_order_export_id").append(`<option value=""></option>`);
                res.data.forEach(function(item) {
                    if (item.po_no != null) {
                        poNo = item.po_no;
                    }

                    if (container != null) {
                        container = item.container;
                    }

                    // if (container != "" || poNo != "") {
                    result = " - CONTAINER : " + container + " PO : " + poNo;
                    // }

                    $(".sales_order_export_id").append(`<option value="${item.sales_order_export_id.trim()}">${item.sales_order_export_no+" "+result}</option>`);
                })
                $(".sales_order_export_id").val("").change();
            }
        })
    }


    function resetFormBiayaEkspor() {
        $('#id_biaya_ekspor_detail').val(null);
        $('#uraian_biaya').val(null);
        $('#nilai_biaya').val(null);

        drawTableBiayaEkspor(listBiayaEkspor);
    }

    function resetFormDetailBarang() {
        $('#id_detail_barang').val(null);
        $('#barang_id').val(null).change();
        $('#valas_id').val(30).change();
        $('#satuan_id').val(null).change();
        $('#qty_barang').val(null);
        $('#harga_satuan').val(null);
        $('#total_harga').val(null);
    }

    function resetFormPengenaanPajak() {
        $('#id_biaya_ekspor_pajak').val(null);
        $('#tanggal_faktur_pajak').val(null);
        $('#no_faktur_pajak').val(null);
        $('#tax_id').val(null).change();
        $('#nilai_pajak').val(null);
        $('#tax_status').val(null).change();
        $('#keterangan_pajak').val(null);
    }

    function drawTableBiayaEkspor(listBiayaEkspor) {
        const table = $('#biayaEksporDetailTable');
        const tbody = table.find('#body-biaya-ekspor-detail');
        const tfoot = table.find('#foot-biaya-ekspor-detail');

        tbody.empty();
        tfoot.empty();

        if (listBiayaEkspor.length === 0) {
            tfoot.append(`
            <tr>
                <td colspan="4" >List Biaya Kosong</td>
            </tr>
        `);
        } else {
            let nilaiTotal = 0;
            let no = 1;

            listBiayaEkspor.forEach(item => {
                nilaiTotal += item.nilai_biaya;
                const newRow = $(`
                <tr style="color:whitesmoke;">
                    <td class="text-center">${no++}</td>
                    <td>${item.uraian_biaya}</td>
                    <td class="text-right">${greatFormatRupiah(item.nilai_biaya)}</td>
                    <td class="text-center">
                        <?php if (!empty($dataBiayaEskpor) && $dataBiayaEskpor['status_bayar'] == 1) : ?>
                            -
                        <?php else : ?>
                            <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBiayaEkspor('${item.id_biaya_ekspor_detail}')">
                                <i class="fa fa-pencil fa-sm"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRowBiayaEkspor('${item.id_biaya_ekspor_detail}')">
                                <i class="fa fa-trash fa-sm"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            `);

                tbody.append(newRow);
            });

            // Tfoot rapi dan sesuai jumlah kolom
            tfoot.append(`
                <tr>
                    <th colspan="2" class="text-right">TOTAL</th>
                    <th class="text-right">${greatFormatRupiah(nilaiTotal)}</th>
                    <th></th>
                </tr>
            `);

        }
    }

    function drawTablePengenaanPajak(listPajak) {
        $('#body-table-pengenaan-pajak').empty();
        $('#foot-pengenaan-pajak').empty();
        var row = '';
        var no = 1;
        const table = $('#taxTable');
        if (listPajak.length === 0) {
            row += `
                    <tr>
                        <td colspan="8">List Pengenaan Pajak Kosong</td>
                    </tr>
                `;
            $('#foot-pengenaan-pajak').append(row);
        } else {
            listPajak.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.tanggal_faktur_pajak));
                newRow.append($('<td>').text(item.no_faktur_pajak));
                newRow.append($('<td>').text(item.tax_name));
                newRow.append($('<td>').text(greatFormatRupiah(item.nilai_pajak)));
                newRow.append($('<td>').text(item.tax_status));
                newRow.append($('<td>').text(item.keterangan_pajak));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataBiayaEskpor)) : ?> <?php if ($dataBiayaEskpor['status_bayar'] == 1) : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPajak('${item.id_biaya_ekspor_pajak}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRowPajak('${item.id_biaya_ekspor_pajak}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPajak('${item.id_biaya_ekspor_pajak}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowPajak('${item.id_biaya_ekspor_pajak}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                table.find('tbody').append(newRow);
            });
        }
    }

    function detailRowBarang(id_detail_barang) {
        var item = null;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].id_detail_barang == id_detail_barang) {
                item = listBarang[i];
                break;
            }
        }

        $('#id_detail_barang').val(item.id_detail_barang);
        $('#barang_id').val(item.barang_id).change();
        $('#valas_id').val(item.valas_id).change();
        $('#satuan_id').val(item.satuan_id).change();
        $('#qty_barang').val(greatFormatRupiah(item.qty_barang));
        $('#harga_satuan').val(greatFormatRupiah(item.harga_satuan));
        $('#total_harga').val(greatFormatRupiah(item.total_harga));

        $('#label-detail-barang').text("Update ");
        $('#detailBarang').modal('show');
    }

    function deleteRowBarang(id_detail_barang) {
        var indexToRemove = -1;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].id_detail_barang == id_detail_barang) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listBarang.splice(indexToRemove, 1);
        }
        drawTableBarangEkspor(listBarang);
    }

    function detailRowBiayaEkspor(id_biaya_ekspor_detail) {
        var item = null;
        for (var i = 0; i < listBiayaEkspor.length; i++) {
            if (listBiayaEkspor[i].id_biaya_ekspor_detail == id_biaya_ekspor_detail) {
                item = listBiayaEkspor[i];
                break;
            }
        }

        $('#id_biaya_ekspor_detail').val(item.id_biaya_ekspor_detail);
        $('#uraian_biaya').val(item.uraian_biaya);
        $('#nilai_biaya').val(greatFormatRupiah(item.nilai_biaya));

        $('#label-biaya-eskpor').text("Update ");
        $('#detailBiayaModal').modal('show');
    }

    function detailRowPajak(id_biaya_ekspor_pajak) {
        var item = null;
        for (var i = 0; i < listPajak.length; i++) {
            if (listPajak[i].id_biaya_ekspor_pajak == id_biaya_ekspor_pajak) {
                item = listPajak[i];
                break;
            }
        }

        $('#id_biaya_ekspor_pajak').val(item.id_biaya_ekspor_pajak);
        $('#tanggal_faktur_pajak').val(item.tanggal_faktur_pajak);
        $('#no_faktur_pajak').val(item.no_faktur_pajak);
        $('#tax_id').val(item.tax_id).change();
        $('#nilai_pajak').val(greatFormatRupiah(item.nilai_pajak));
        $('#tax_status').val(item.tax_status).change();
        $('#keterangan_pajak').val(item.keterangan_pajak);

        $('#label-pengenaan-pajak').text("Update ");
        $('#detailPengenaanPajakModal').modal('show');
    }

    function deleteRowPajak(id_biaya_ekspor_pajak) {
        var indexToRemove = -1;
        for (var i = 0; i < listPajak.length; i++) {
            if (listPajak[i].id_biaya_ekspor_pajak == id_biaya_ekspor_pajak) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listPajak.splice(indexToRemove, 1);
        }
        drawTablePengenaanPajak(listPajak);
        calculateTotalBiayaAndTax();
    }

    function deleteRowBiayaEkspor(id_biaya_ekspor_detail) {
        var indexToRemove = -1;
        for (var i = 0; i < listBiayaEkspor.length; i++) {
            if (listBiayaEkspor[i].id_biaya_ekspor_detail == id_biaya_ekspor_detail) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listBiayaEkspor.splice(indexToRemove, 1);
        }
        drawTableBiayaEkspor(listBiayaEkspor);
        calculateTotalBiayaAndTax();
    }

    function drawTableBarangEkspor(listBarang) {

        $('#body-detail-table-barang').empty();
        $('#foot-detail-table-barang').empty();
        var row = '';
        var no = 1;
        const table = $('#dataTableBarang');
        const tfoot = table.find('#foot-detail-table-barang');

        if (listBarang.length === 0) {
            $('#txt_valas').text("");
            row += `
                    <tr>
                        <td colspan="7">List Barang Eskpor Tidak Ada</td>
                    </tr>
                `;
            $('#foot-detail-table-barang').append(row);
        } else {
            var totalTotalHarga = 0;
            listBarang.map(item => {
                totalTotalHarga += item.total_harga;

                // Valas
                $('#txt_valas').text(" (" + item.valas_name + ")");

                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.kode_barang));
                newRow.append($('<td>').text(item.barang_name));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.qty_barang) + " " + item.kode_satuan));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.harga_satuan)));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.total_harga)));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataBiayaEskpor)) : ?> <?php if ($dataBiayaEskpor['status_bayar'] == 1) : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBarang('${item.id_detail_barang}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRowBarang('${item.id_detail_barang}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBarang('${item.id_detail_barang}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowBarang('${item.id_detail_barang}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));
                table.find('tbody').append(newRow);
            });
            tfoot.append(`
                    <tr>
                        <th colspan="5" class="text-right">GRAND TOTAL</th>
                        <th class="text-right">${greatFormatRupiah(totalTotalHarga)}</th>
                        <th class="text-right"></th>
                    </tr>
                `);

        }
    }


    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };

    function changeStatus() {
        let isChecked = document.getElementById('auto_generate').checked;
        if (isChecked) {
            // SET NOMOR AUTO GENERATE
            $('#no_invoice').val("AUTO GENERATE");
            $('#no_invoice').attr('readonly', true);
        } else {
            // SET NOMOR AUTO GENERATE FALSE
            $('#no_invoice').val(null);
            $('#no_invoice').attr('readonly', false);
        }

    }

    function calculateTotalBiayaAndTax() {
        var totalBiaya = 0;
        var totalPajakPpn = 0;
        var totalPajakPph = 0;

        $.each(listBiayaEkspor, function(i, v) {
            totalBiaya += v.nilai_biaya;
        });

        $.each(listPajak, function(i, v) {
            if (v.type_tax == "ppn") {
                totalPajakPpn += v.nilai_pajak;
            } else {
                totalPajakPph += v.nilai_pajak;
            }
        });

        var nominalBiayaFaktur = totalBiaya + totalPajakPpn - totalPajakPph;
        // Append
        $('#total_biaya_prev').val(greatFormatRupiah(totalBiaya));
        $('#total_ppn_prev').val(greatFormatRupiah(totalPajakPpn));
        $('#total_pph_prev').val(greatFormatRupiah(totalPajakPph));
        $('#total_faktur_prev').val(greatFormatRupiah(nominalBiayaFaktur));
    }

    const print = function(id) {
        window.open("<?= base_url('biaya-eskpor/print') ?>" + '/' + id, "_blank");
    }
</script>
<?= $this->endSection(); ?>