<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataBiayaImpor) ? "Update Biaya Impor" : "Tambah Biaya Impor" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("biaya-impor"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataBiayaImpor)) { ?>
                <?php if (can('Biaya Exim', 'Biaya Impor', 'p')): ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= encrypt($dataBiayaImpor['id']) ?>')">
                        Print
                    </button>
                <?php endif; ?>
                <?php if ($dataBiayaImpor['status_posting'] == 0) { ?>
                    <?php if (can('Biaya Exim', 'Biaya Impor', 'a')): ?>
                        <button class="btn btn-success posting-spp posting-so float-right" onclick="posting()">
                            Posting Audit
                        </button>
                    <?php endif; ?>
                    <?php if (can('Biaya Exim', 'Biaya Impor', 'u')): ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Update
                        </button>
                    <?php endif; ?>
                <?php } ?>

                <?php if ($dataBiayaImpor['status_posting'] == 1) { ?>
                    <?php if (can('Biaya Exim', 'Biaya Impor', 'ua')): ?>
                        <button class="btn btn-success posting-spp unposting-so float-right" onclick="unposting()">
                            Unposting Audit
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
                <input autocomplete="one-time-code" value="<?= !empty($dataBiayaImpor) ? encrypt($dataBiayaImpor['id']) : '' ?>" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" value="<?= !empty($dataBiayaImpor) ? $dataBiayaImpor['no_invoice'] : 'AUTO GENERATE' ?>" <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'readonly' : '') : 'readonly' ?> class="form-control no_invoice" id="no_invoice" name="no_invoice" placeholder="No Invoice" required>
                                    <label for="floatingInput">No Invoice</label>
                                </div>
                                <div style="<?= !empty($dataBiayaImpor) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal_invoice" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal Invoice" value="<?= !empty($dataBiayaImpor) ? date('d/m/Y', strtotime($dataBiayaImpor['tanggal_invoice'])) : date('d/m/Y')  ?>">
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
                            <select <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="form-select divisi_id" name="divisi_id" id="divisi_id">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d): ?>
                                    <option <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="form-select tipe_po" name="tipe_po" id="tipe_po">
                                <option value=""></option>
                                <option <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['tipe_po'] == "IMPORT BAKU" ? 'selected' : '') : '' ?> value="IMPORT BAKU">IMPORT BAHAN BAKU</option>
                                <option <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['tipe_po'] == "IMPORT PENOLONG" ? 'selected' : '') : '' ?> value="IMPORT PENOLONG">IMPORT BAHAN PENOLONG</option>
                            </select>
                            <label for="floatingInput">Pilih Tipe PO</label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="form-select supplier_id" name="supplier_id" id="supplier_id">
                                <option value=""></option>
                                <?php foreach ($dataSupplier as $d): ?>
                                    <option <?= !empty($dataBiayaImpor) ? ($d['id'] == $dataBiayaImpor['supplier_id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="form-select po_id" name="po_id" id="po_id">
                                <option value=""></option>
                                <?php if (!empty($dataDetailPo)): ?>
                                    <option selected value="<?= $dataDetailPo['id'] ?>"><?= $dataDetailPo['po_no'] ?></option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput">Pilih Nomor PO</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control no_bl" id="no_bl" name="no_bl" placeholder="No B/L" value="<?= !empty($dataBiayaImpor) ? $dataBiayaImpor['no_bl'] : '' ?>">
                            <label for="floatingInput">No B/L</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="form-select vendor_pelayaran_id" name="vendor_pelayaran_id" id="vendor_pelayaran_id">
                                <option value=""></option>
                                <?php foreach ($dataVendorPelayaran as $d): ?>
                                    <option <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['vendor_pelayaran_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>"><?= $d['nama_vendor'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Vendor / Pelayaran (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control shipper_prev" id="shipper_prev" name="shipper_prev">
                            <label for="floatingInput">Shipper</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control consigne_prev" id="consigne_prev" name="consigne_prev">
                            <label for="floatingInput">Consigne</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control port_of_origin_prev" id="port_of_origin_prev" name="port_of_origin_prev">
                            <label for="floatingInput">Port Of Origin</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control port_of_destination_prev" id="port_of_destination_prev" name="port_of_destination_prev">
                            <label for="floatingInput">Port Of Destination</label>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">List Barang Impor</label>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableBarang" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Barang</th>
                                    <th style="text-align: right;">Qty PO</th>
                                    <th style="text-align: right;">Harga Satuan</th>
                                    <th style="text-align: right;">Total Harga <span id="txt_valas" class="mr-0"></span></th>
                                </tr>
                            </thead>
                            <tbody id="body-detail-table-barang">

                            </tbody>
                            <tfoot id="foot-detail-table-barang">
                                <tr>
                                    <td colspan="6">List Barang Impor Tidak Ada</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row">
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
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab1" data-toggle="tab" href="#profile1" role="tab" aria-controls="profile1" aria-selected="false">List Container</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="col-subtitle-modal">
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <button <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnAddBiaya" type="button" style="width: 90% !important;">
                                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="biayaImporDetailTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 10px;">No</th>
                                            <th>Detail Biaya</th>
                                            <th style="width: 120px; text-align:right;">Currency</th>
                                            <th style="width: 120px; text-align:right;">Nilai</th>
                                            <th style="width: 120px; text-align:right;">Exchange Rate</th>
                                            <th style="width: 120px; text-align:right;">Nilai (IDR)</th>
                                            <th style="width: 100px; text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-biaya-ekspor-detail" id="body-biaya-ekspor-detail">

                                    </tbody>
                                    <tfoot class="foot-biaya-ekspor-detail" id="foot-biaya-ekspor-detail">
                                        <tr>
                                            <td colspan="7">List Biaya Kosong</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="col-subtitle-modal">
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <button <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnAddPajakModal" type="button" style="width: 90% !important;">
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

                    <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile-tab1">
                        <div class="col-subtitle-modal">
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <button <?= !empty($dataBiayaImpor) ? ($dataBiayaImpor['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnAddContainer" type="button" style="width: 90% !important;">
                                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="detailContainerTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 10px;">No</th>
                                            <th>Nomor Container</th>
                                            <th>Detail Container</th>
                                            <th style="width: 100px; text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-container" id="body-container">

                                    </tbody>
                                    <tfoot class="foot-container" id="foot-container">
                                        <tr>
                                            <td colspan="4">List Container Kosong</td>
                                        </tr>
                                    </tfoot>
                                </table>
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
                <h5 class="modal-title title-secondary"><label id="label-biaya-impor"></label> Biaya Impor</h5>
            </div>
            <form id="form-biaya-impor" role="form" method="POST">
                <input type="hidden" name="id_biaya_impor_detail" id="id_biaya_impor_detail">
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
                                <select class="form-select valas_id" name="valas_id" id="valas_id">
                                    <option value=""></option>
                                    <?php foreach ($dataValas as $d): ?>
                                        <option data-valas_name="<?= $d['value'] ?>" value="<?= $d['id'] ?>"><?= trim($d['value'] . " - " . $d['description']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput">Pilih Currency</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" value="1" class="form-control nilai_biaya" id="nilai_biaya" name="nilai_biaya" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Nilai Biaya">
                                <label for="floatingInput">Nilai Biaya</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control exchange_rate" id="exchange_rate" name="exchange_rate" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Nilai Exchange Rate">
                                <label for="floatingInput">Nilai Exchange Rate</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control nilai_biaya_idr" id="nilai_biaya_idr" name="nilai_biaya_idr" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Nilai Biaya IDR">
                                <label for="floatingInput">Nilai Biaya (IDR)</label>
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
                <input type="hidden" name="id_biaya_impor_pajak" id="id_biaya_impor_pajak">
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

<div class="modal detail-modal" id="detailContainerModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-container"></label> Container</h5>
            </div>
            <form id="form-detail-container" role="form" method="POST">
                <input type="hidden" name="id_container" id="id_container">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_container" id="no_container" name="no_container" placeholder="No Container">
                                <label for="floatingInput">Nomor Container</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-5" style="height: 50px;">
                                <textarea name="detail_container" id="detail_container" class="form-control full-textarea" placeholder="Detail Container"></textarea>
                                <label for="floatingInput">Detail Container (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideDetailContainer">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitDetailContainer">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listBiayaImpor = [];
    var listPajak = [];
    var listContainer = [];


    $(document).ready(function() {
        <?php if (!empty($dataBiayaImpor)) { ?>
            <?php foreach ($dataBiayaImporDetail as $d): ?>
                listBiayaImpor.push({
                    id_biaya_impor_detail: "<?= $d['id'] ?>",
                    uraian_biaya: "<?= $d['uraian_biaya'] ?>",
                    valas_id: "<?= $d['valas_id'] ?>",
                    valas_name: "<?= $d['valas_name'] ?>",
                    nilai_biaya: <?= floatval($d['nilai_biaya']) ?>,
                    nilai_exchange_rate: <?= floatval($d['nilai_exchange_rate']) ?>,
                    nilai_biaya_idr: <?= floatval($d['nilai_biaya_idr']) ?>
                });
            <?php endforeach ?>

            <?php foreach ($dataBiayaImporPajak as $d): ?>
                listPajak.push({
                    id_biaya_impor_pajak: "<?= $d['id'] ?>",
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

            <?php foreach ($dataContainer as $d): ?>
                listContainer.push({
                    id_container: "<?= $d['id'] ?>",
                    no_container: "<?= $d['no_container'] ?>",
                    detail_container: "<?= $d['detail_container'] ?>"
                })
            <?php endforeach; ?>

            getPoDetail("<?= $dataBiayaImpor['tipe_po'] ?>", "<?= $dataBiayaImpor['po_id'] ?>");
            drawTableBiayaEkspor(listBiayaImpor);
            drawTablePengenaanPajak(listPajak);
            drawTableDetailContainer(listContainer);
            calculateTotalBiayaAndTax();
        <?php } else { ?>

        <?php } ?>


        $("#tanggal_invoice,#keberangkatan_kapal,#tanggal_surat_jalan,#tanggal_faktur_pajak").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('.supplier_id').select2({
            placeholder: "Pilih Supplier",
            theme: "bootstrap-5",
        }).change(function() {
            var supplierId = $('#supplier_id option:selected').val();
            var tipePo = $('#tipe_po option:selected').val();
            dropdownPo(tipePo, supplierId);
        });


        $('.tipe_po').select2({
            placeholder: "Pilih Tipe PO",
            theme: "bootstrap-5",
        }).change(function() {
            $('#supplier_id').val(null).change();
        });

        $('.divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.po_id').select2({
            placeholder: "Pilih Nomor PO",
            theme: "bootstrap-5",
        }).change(function() {
            // Get Order Form Ekspor
            var tipePo = $('#tipe_po option:selected').val();
            var poId = $('#po_id option:selected').val();
            getPoDetail(tipePo, poId);
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

        $('.valas_id').select2({
            placeholder: "Pilih Currency",
            theme: "bootstrap-5",
            dropdownParent: $('#detailBiayaModal')
        }).change(function() {});

        //CSS SELECT2 FLOATING LABEL
        $('.po_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status, .supplier_id, .tipe_po, .valas_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.po_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status, .supplier_id, .tipe_po, .valas_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.po_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status, .supplier_id, .tipe_po, .valas_id')
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
                tipe_po: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                po_id: {
                    required: true
                },
                no_bl: {
                    required: true
                },
                vendor_pelayaran_id: {
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
                tipe_po: {
                    required: "Tipe PO wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                po_id: {
                    required: "Nomor po wajib diisi"
                },
                no_bl: {
                    required: "Nomor b/l wajib diisi"
                },
                vendor_pelayaran_id: {
                    required: "Vendor pelayaran wajib diisi"
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

        var validatorBiayaEkspor = $("#form-biaya-impor").validate({
            rules: {
                uraian_biaya: {
                    required: true
                },
                valas_id: {
                    required: true
                },
                nilai_biaya: {
                    required: true
                },
                exchange_rate: {
                    required: true
                },
                nilai_biaya_idr: {
                    required: true
                },
            },
            messages: {
                uraian_biaya: {
                    required: "Uraian biaya wajib diisi"
                },
                valas_id: {
                    required: "Pilih Currency"
                },
                nilai_biaya: {
                    required: "Nilai biaya wajib diisi"
                },
                exchange_rate: {
                    required: "Nilai exchange rate wajib diisi"
                },
                nilai_biaya_idr: {
                    required: "Nilai biaya IDR wajib diisi"
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

        var validatorDetailContainer = $("#form-detail-container").validate({
            rules: {
                no_container: {
                    required: true
                },
            },
            messages: {
                no_container: {
                    required: "No container wajib diisi"
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
            $("#label-biaya-impor").text("Tambah ");
            $('#detailBiayaModal').modal('show');
            resetFormBiayaImpor();
        });

        $('#btnHideDetailBiaya').click(function(e) {
            e.preventDefault();
            $('#detailBiayaModal').modal('hide');
        });

        $('#nilai_biaya,#exchange_rate').keyup(function(e) {
            e.preventDefault();
            var nilaiBiaya = destroyFormatRupiah($('#nilai_biaya').val());
            var exchangeRate = destroyFormatRupiah($('#exchange_rate').val());
            var nilaiIdr = nilaiBiaya * exchangeRate;

            $('#nilai_biaya_idr').val(greatFormatRupiah(nilaiIdr.toFixed(2)));
        })

        $('#btnSubmitDetailBiaya').click(function(e) {
            e.preventDefault();
            if ($('#form-biaya-impor').valid()) {
                var idBiayaEksporDetail = $('#id_biaya_impor_detail').val();
                var uraianBiaya = $('#uraian_biaya').val();
                var valasId = $('#valas_id option:selected').val();
                var valasName = $('#valas_id option:selected').data('valas_name');
                var nilaiBiaya = destroyFormatRupiah($('#nilai_biaya').val());
                var nilaiExchangeRate = destroyFormatRupiah($('#exchange_rate').val());
                var nilaiBiayaIdr = destroyFormatRupiah($('#nilai_biaya_idr').val());

                if (idBiayaEksporDetail) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listBiayaImpor.length; i++) {
                        if (listBiayaImpor[i].id_biaya_impor_detail == idBiayaEksporDetail) {
                            index = i;
                            break;
                        }
                    }

                    listBiayaImpor[index].uraian_biaya = uraianBiaya;
                    listBiayaImpor[index].valas_id = valasId;
                    listBiayaImpor[index].valas_name = valasName;
                    listBiayaImpor[index].nilai_biaya = parseFloat(nilaiBiaya);
                    listBiayaImpor[index].nilai_exchange_rate = parseFloat(nilaiExchangeRate);
                    listBiayaImpor[index].nilai_biaya_idr = parseFloat(nilaiBiayaIdr);

                } else {
                    // CREATE
                    listBiayaImpor.push({
                        id_biaya_impor_detail: getID(),
                        uraian_biaya: uraianBiaya,
                        valas_id: valasId,
                        valas_name: valasName,
                        nilai_biaya: parseFloat(nilaiBiaya),
                        nilai_exchange_rate: parseFloat(nilaiExchangeRate),
                        nilai_biaya_idr: parseFloat(nilaiBiayaIdr)
                    });
                }

                $('#detailBiayaModal').modal('hide');
                drawTableBiayaEkspor(listBiayaImpor);
                calculateTotalBiayaAndTax();
            }
        });

        $('#btnSubmitDetailContainer').click(function(e) {
            e.preventDefault();
            if ($('#form-detail-container').valid()) {
                var idContainer = $('#id_container').val();
                var noContainer = $('#no_container').val();
                var detailContainer = $('#detail_container').val();

                if (idContainer) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listContainer.length; i++) {
                        if (listContainer[i].id_container == idContainer) {
                            index = i;
                            break;
                        }
                    }

                    listContainer[index].no_container = noContainer;
                    listContainer[index].detail_container = detailContainer;

                } else {
                    // CREATE
                    listContainer.push({
                        id_container: getID(),
                        no_container: noContainer,
                        detail_container: detailContainer,
                    });
                }

                $('#detailContainerModal').modal('hide');
                drawTableDetailContainer(listContainer);
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

        $('#btnSubmitPengenaanPajak').click(function(e) {
            e.preventDefault();
            if ($('#form-pengenaan-pajak').valid()) {
                var idBiayaEksporPajak = $('#id_biaya_impor_pajak').val();
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
                        if (listPajak[i].id_biaya_impor_pajak == idBiayaEksporPajak) {
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
                        id_biaya_impor_pajak: idBiayaEksporPajak,
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
            if (listBiayaImpor.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan biaya impor !',
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
                            let url = id == '' ? "<?= base_url('biaya-impor/create') ?>" : "<?= base_url('biaya-impor/update') ?>";
                            let data = new FormData(document.querySelector("#form-parent"));
                            let totalFaktur = destroyFormatRupiah($('#total_faktur_prev').val());
                            let totalFakturBeforeTax = destroyFormatRupiah($('#total_biaya_prev').val());

                            data.append("total_faktur_before_tax", totalFakturBeforeTax);
                            data.append("total_faktur", totalFaktur);
                            data.append("listBiayaImpor", JSON.stringify(listBiayaImpor));
                            data.append("listPajak", JSON.stringify(listPajak));
                            data.append("listContainer", JSON.stringify(listContainer));

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
                                                window.location.href = "<?= base_url("biaya-impor") ?>";
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

    $('#btnAddContainer').click(function(e) {
        e.preventDefault();
        $('#label-container').text("Tambah ");
        $('#detailContainerModal').modal('show');
        resetFormDetailContainer();
    });

    $('#btnHideDetailContainer').click(function(e) {
        e.preventDefault();
        $('#detailContainerModal').modal('hide');
    })

    function getPoDetail(tipePo, poId) {
        $.ajax({
            url: `<?= base_url("biaya-impor/get-detail-barang-po"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                tipe_po: tipePo,
                po_id: poId
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    if (res.data != null) {
                        var dataPo = res.data.po_detail;
                        var dataPoDetail = res.data.po_barang;

                        $('#shipper_prev').val(dataPo.shipper);
                        $('#consigne_prev').val(dataPo.consigne);
                        $('#port_of_origin_prev').val(dataPo.port_origin);
                        $('#port_of_destination_prev').val(dataPo.port_destination);

                        $('#txt_valas').text("(" + dataPo.valas_name + ")");

                        // DrawTable Detail Barang
                        drawTablePoDetail(dataPoDetail);
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

    function dropdownPo(tipePo, supplierId) {
        $.ajax({
            url: `<?= base_url("biaya-impor/dropdown-po"); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                supplier_id: supplierId,
                tipe_po: tipePo
            },
            dataType: "json",
            success: function(res) {
                $(".po_id").empty();
                $(".po_id").append(`<option value=""></option>`);
                res.data.forEach(function(item) {
                    $(".po_id").append(`<option value="${item.id}">${item.po_no}</option>`);
                })
                $(".po_id").val("").change();
            }
        })
    }


    function resetFormBiayaImpor() {
        $('#id_biaya_impor_detail').val(null);
        $('#uraian_biaya').val(null);
        $('#nilai_biaya').val(null);
        $('#valas_id').val(null).change();
        $('#exchange_rate').val(1).change();
        $('#nilai_biaya_idr').val(null).change();

        drawTableBiayaEkspor(listBiayaImpor);
    }

    function resetFormPengenaanPajak() {
        $('#id_biaya_impor_pajak').val(null);
        $('#tanggal_faktur_pajak').val(null);
        $('#no_faktur_pajak').val(null);
        $('#tax_id').val(null).change();
        $('#nilai_pajak').val(null);
        $('#tax_status').val(null).change();
        $('#keterangan_pajak').val(null);
    }

    function resetFormDetailContainer() {
        $('#id_container').val(null);
        $('#no_container').val(null);
        $('#detail_container').val(null);
    }

    function drawTableBiayaEkspor(listBiayaImpor) {
        const table = $('#biayaImporDetailTable');
        const tbody = table.find('#body-biaya-ekspor-detail');
        const tfoot = table.find('#foot-biaya-ekspor-detail');

        tbody.empty();
        tfoot.empty();

        if (listBiayaImpor.length === 0) {
            tfoot.append(`
            <tr>
                <td colspan="7">List Biaya Kosong</td>
            </tr>
        `);
        } else {
            let nilaiTotal = 0;
            let no = 1;

            listBiayaImpor.forEach(item => {
                nilaiTotal += item.nilai_biaya_idr;
                const newRow = $(`
                <tr style="color:whitesmoke;">
                    <td class="text-center">${no++}</td>
                    <td>${item.uraian_biaya}</td>
                    <td class="text-right">${item.valas_name}</td>
                    <td class="text-right">${greatFormatRupiah(item.nilai_biaya)}</td>
                    <td class="text-right">${greatFormatRupiah(item.nilai_exchange_rate)}</td>
                    <td class="text-right">${greatFormatRupiah(item.nilai_biaya_idr)}</td>
                    <td class="text-center">
                        <?php if (!empty($dataBiayaImpor) && $dataBiayaImpor['status_posting'] == 1) : ?>
                            -
                        <?php else : ?>
                            <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBiayaEkspor('${item.id_biaya_impor_detail}')">
                                <i class="fa fa-pencil fa-sm"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRowBiayaEkspor('${item.id_biaya_impor_detail}')">
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
                    <th colspan="5" class="text-right">TOTAL</th>
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
                    <?php if (!empty($dataBiayaImpor)) : ?> <?php if ($dataBiayaImpor['status_posting'] == 1) : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPajak('${item.id_biaya_impor_pajak}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRowPajak('${item.id_biaya_impor_pajak}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPajak('${item.id_biaya_impor_pajak}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowPajak('${item.id_biaya_impor_pajak}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                table.find('tbody').append(newRow);
            });
        }
    }

    function drawTableDetailContainer(listContainer) {
        $('#body-container').empty();
        $('#foot-container').empty();
        var row = '';
        var no = 1;
        const table = $('#detailContainerTable');
        if (listContainer.length === 0) {
            row += `
                    <tr>
                        <td colspan="4">List Container Kosong</td>
                    </tr>
                `;
            $('#foot-container').append(row);
        } else {
            listContainer.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.no_container));
                newRow.append($('<td>').text(item.detail_container));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataBiayaImpor)) : ?> <?php if ($dataBiayaImpor['status_posting'] == 1) : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowContainer('${item.id_container}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRowContainer('${item.id_container}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowContainer('${item.id_container}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowContainer('${item.id_container}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                table.find('tbody').append(newRow);
            });
        }
    }

    function detailRowBiayaEkspor(id_biaya_impor_detail) {
        var item = null;
        for (var i = 0; i < listBiayaImpor.length; i++) {
            if (listBiayaImpor[i].id_biaya_impor_detail == id_biaya_impor_detail) {
                item = listBiayaImpor[i];
                break;
            }
        }

        $('#id_biaya_impor_detail').val(item.id_biaya_impor_detail);
        $('#uraian_biaya').val(item.uraian_biaya);
        $('#valas_id').val(item.valas_id).change();
        $('#nilai_biaya').val(greatFormatRupiah(item.nilai_biaya));
        $('#exchange_rate').val(greatFormatRupiah(item.nilai_exchange_rate));
        $('#nilai_biaya_idr').val(greatFormatRupiah(item.nilai_biaya_idr));

        $('#label-biaya-impor').text("Update ");
        $('#detailBiayaModal').modal('show');
    }

    function detailRowPajak(id_biaya_impor_pajak) {
        var item = null;
        for (var i = 0; i < listPajak.length; i++) {
            if (listPajak[i].id_biaya_impor_pajak == id_biaya_impor_pajak) {
                item = listPajak[i];
                break;
            }
        }

        $('#id_biaya_impor_pajak').val(item.id_biaya_impor_pajak);
        $('#tanggal_faktur_pajak').val(item.tanggal_faktur_pajak);
        $('#no_faktur_pajak').val(item.no_faktur_pajak);
        $('#tax_id').val(item.tax_id).change();
        $('#nilai_pajak').val(greatFormatRupiah(item.nilai_pajak));
        $('#tax_status').val(item.tax_status).change();
        $('#keterangan_pajak').val(item.keterangan_pajak);

        $('#label-pengenaan-pajak').text("Update ");
        $('#detailPengenaanPajakModal').modal('show');
    }

    function detailRowContainer(id_container) {
        var item = null;
        for (var i = 0; i < listContainer.length; i++) {
            if (listContainer[i].id_container == id_container) {
                item = listContainer[i];
                break;
            }
        }

        $('#id_container').val(item.id_container);
        $('#no_container').val(item.no_container);
        $('#detail_container').val(item.detail_container);

        $('#label-container').text("Update ");
        $('#detailContainerModal').modal('show');
    }

    function deleteRowContainer(id_container) {
        var indexToRemove = -1;
        for (var i = 0; i < listContainer.length; i++) {
            if (listContainer[i].id_container == id_container) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listContainer.splice(indexToRemove, 1);
        }
        drawTableDetailContainer(listContainer);
    }

    function deleteRowPajak(id_biaya_impor_pajak) {
        var indexToRemove = -1;
        for (var i = 0; i < listPajak.length; i++) {
            if (listPajak[i].id_biaya_impor_pajak == id_biaya_impor_pajak) {
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

    function deleteRowBiayaEkspor(id_biaya_impor_detail) {
        var indexToRemove = -1;
        for (var i = 0; i < listBiayaImpor.length; i++) {
            if (listBiayaImpor[i].id_biaya_impor_detail == id_biaya_impor_detail) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listBiayaImpor.splice(indexToRemove, 1);
        }
        drawTableBiayaEkspor(listBiayaImpor);
        calculateTotalBiayaAndTax();
    }

    function drawTablePoDetail(dataPoDetail) {
        $('#body-detail-table-barang').empty();
        $('#foot-detail-table-barang').empty();
        var row = '';
        var no = 1;
        const table = $('#dataTableBarang');
        const tfoot = table.find('#foot-detail-table-barang');

        if (dataPoDetail.length === 0) {
            row += `
                    <tr>
                        <td colspan="8">List Barang Impor Tidak Ada</td>
                    </tr>
                `;
            $('#foot-detail-table-barang').append(row);
        } else {
            dataPoDetail.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.kode_barang));
                newRow.append($('<td>').text(item.barang_name + ' - ' + item.spesifikasi));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.qty.toFixed(2)) + " " + item.kode_satuan));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.harga_satuan.toFixed(2))));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.total_harga.toFixed(2))));
                table.find('tbody').append(newRow);
            });
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

        $.each(listBiayaImpor, function(i, v) {
            totalBiaya += v.nilai_biaya_idr;
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
        window.open("<?= base_url('biaya-impor/print') ?>" + '/' + id, "_blank");
    }

    function posting() {
        Swal.fire({
            icon: 'question',
            title: "Posting Data ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Back',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("biaya-impor/posting"); ?>",
                    data: {
                        id: $("#id").val(),
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading()
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    location.href = "<?= base_url('biaya-impor') ?>";
                                })
                        }
                    },
                });
            }
        })

    }


    function unposting() {
        Swal.fire({
            icon: 'question',
            title: "Unposting Data ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Back',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("biaya-impor/unposting"); ?>",
                    data: {
                        id: $("#id").val(),
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading()
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    location.reload();
                                })
                        }
                    },
                });
            }
        })

    }
</script>
<?= $this->endSection(); ?>