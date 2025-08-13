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
                <?php if (can('Penjualan Ekspor', 'Order Form', 'p')): ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= encrypt($dataBiayaEskpor->sales_order_export_id) ?>')">
                        Print
                    </button>
                <?php endif; ?>
                <?php if ($dataBiayaEskpor->status === "NEW") { ?>
                    <?php if (can('Penjualan Ekspor', 'Order Form', 'a')): ?>
                        <button class="btn btn-success posting-spp posting-so float-right">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Penjualan Ekspor', 'Order Form', 'u')): ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php } ?>
                <?php if ($dataBiayaEskpor->status === "POSTED") { ?>
                    <?php if (can('Penjualan Ekspor', 'Order Form', 'ua')): ?>
                        <button class="btn btn-success posting-spp unposting-so float-right">
                            Unposting
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
                <input autocomplete="one-time-code" value="<?= $id ?? "" ?>" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->sales_order_export_no : 'AUTO GENERATE' ?>" <?= !empty($dataBiayaEskpor) ? ($dataBiayaEskpor->status == "POSTED" ? 'readonly' : '') : 'readonly' ?> class="form-control no_invoice" id="no_invoice" name="no_invoice" placeholder="No Invoice" required>
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
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal_invoice" id="tanggal_invoice" name="tanggal_invoice" placeholder="Tanggal Invoice" value="<?= !empty($dataBiayaEskpor) ? date('d/m/Y', strtotime($dataBiayaEskpor->tanggal)) : date('d/m/Y')  ?>">
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
                            <select class="form-select divisi_id" name="divisi_id" id="divisi_id">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d): ?>
                                    <option value=<?= $d['id'] ?>""><?= $d['divisi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select sales_order_export_id" name="sales_order_export_id" id="sales_order_export_id">
                                <option value=""></option>
                                <?php foreach ($dataSalesOrderExport as $d): ?>
                                    <option value="<?= $d['sales_order_export_id'] ?>"><?= $d['sales_order_export_no'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Sales Order Ekspor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input readonly autocomplete="one-time-code" value="" type="text" class="form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term (Optional)">
                            <label for="floatingInput">Payment Term</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control destination" id="destination" name="destination" placeholder="Destination" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->deadline : '' ?>">
                            <label for="floatingInput">Destination</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control no_container_order_form" id="no_container_order_form" name="no_container_order_form" placeholder="No Container" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->deadline : '' ?>">
                            <label for="floatingInput">No Container</label>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">List Barang Eskpor</label>
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
                                    <th style="text-align: right;">Qty Order Form</th>
                                    <th style="text-align: right;">Harga Satuan</th>
                                    <th style="text-align: right;">Total Harga <span id="txt_valas" class="mr-1"></span> <span id="txt_tipe_harga"></span></th>
                                </tr>
                            </thead>
                            <tbody id="body-detail-table-barang">

                            </tbody>
                            <tfoot id="foot-detail-table-barang">
                                <tr>
                                    <td colspan="6">List Barang Eskpor Tidak Ada</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Detail Biaya</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control no_container" id="no_container" name="no_container" placeholder="No Container" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->deadline : '' ?>">
                            <label for="floatingInput">No Container</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control no_seal" id="no_seal" name="no_seal" placeholder="No Seal" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->deadline : '' ?>">
                            <label for="floatingInput">No Seal</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control nama_kapal" id="nama_kapal" name="nama_kapal" placeholder="Nama Kapal" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->deadline : '' ?>">
                            <label for="floatingInput">Nama Kapal</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker keberangkatan_kapal" id="keberangkatan_kapal" name="keberangkatan_kapal" placeholder="Keberangkatan Kapal" value="<?= !empty($dataBiayaEskpor) ? date('d/m/Y', strtotime($dataBiayaEskpor->tanggal)) : ''  ?>">
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
                            <input autocomplete="one-time-code" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="No Surat Jalan" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->deadline : '' ?>">
                            <label for="floatingInput">No Surat Jalan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal_surat_jalan" id="tanggal_surat_jalan" name="tanggal_surat_jalan" placeholder="Sales Order Date" value="<?= !empty($dataBiayaEskpor) ? date('d/m/Y', strtotime($dataBiayaEskpor->tanggal)) : ''  ?>">
                                    <label for="floatingInput">Tanggal Surat Jalan</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control no_kendaraan" id="no_kendaraan" name="no_kendaraan" placeholder="No Kendaraan" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->deadline : '' ?>">
                            <label for="floatingInput">No Kendaraan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control detail_kendaraan" id="detail_kendaraan" name="detail_kendaraan" placeholder="Detail Kendaraan" value="<?= !empty($dataBiayaEskpor) ? $dataBiayaEskpor->deadline : '' ?>">
                            <label for="floatingInput">Detail Kendaraan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select vendor_pelayaran_id" name="vendor_pelayaran_id" id="vendor_pelayaran_id">
                                <option value=""></option>
                                <?php foreach ($dataVendorPelayaran as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= $d['nama_vendor'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Vendor / Pelayaran (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control total_biaya_prev" id="total_biaya_prev" name="total_biaya_prev">
                            <label for="floatingInput">Total Biaya Ekspor</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control total_ppn_11_prev" id="total_ppn_11_prev" name="total_ppn_11_prev">
                            <label for="floatingInput">Total PPN Masukan 11%</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control total_pph_21_prev" id="total_pph_21_prev" name="total_pph_21_prev">
                            <label for="floatingInput">Total Potongan PPH Pasal 21</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control total_faktur_prev" id="total_faktur_prev" name="total_faktur_prev">
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
                                    <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAddBiaya" type="button" style="width: 90% !important;">
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
                                    <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAddPajakModal" type="button" style="width: 90% !important;">
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
                                                <th style="text-align: center;">Tgl Faktur Pajak</th>
                                                <th style="text-align: center;">No Faktur Pajak</th>
                                                <th style="text-align: center;">Pajak</th>
                                                <th style="text-align: center;">Jumlah</th>
                                                <th style="text-align: center;">Status</th>
                                                <th style="text-align: center;">Keterangan</th>
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
                                        <option value="<?= $d['id'] ?>"><?= trim($d['name']) ?></option>
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

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listDataSalesKontrak = [];
    var listBiayaEkspor = [];
    var listAdditional = [];
    var listPajak = [];
    // HIDE DETAIL SPECS LIST
    // $('#component-detail-specs-list').hide();


    $(document).ready(function() {
        <?php if (!empty($dataBiayaEskpor)) { ?>
            listDataSalesKontrak = <?= json_encode($dataBiayaEskporDetail) ?>;
            listBiayaEkspor = <?= json_encode($dataBiayaEskporSpecs) ?>;

            <?php foreach ($dataBiayaEskporAdditional as $s): ?>
                listAdditional.push({
                    id_detail_additional: "<?= $s['id'] ?>",
                    additional_detail: "<?= $s['additional_detail'] ?>",
                    additional_detail_type: "<?= $s['additional_detail_type'] ?>",
                    additional_detail_price: "<?= $s['additional_detail_price'] ?>",
                });
            <?php endforeach ?>

            drawTable(listDataSalesKontrak);
            drawTableBiayaEkspor(listBiayaEkspor);
            drawTableAdditionalList(listAdditional);
        <?php } else { ?>

        <?php } ?>


        <?php if (!empty($dataBiayaEskpor)): ?>
            <?php if ($dataBiayaEskpor->divisi == "PTS"): ?>
                $('#component-detail-specs-list').show();
            <?php endif; ?>
        <?php endif; ?>

        $("#tanggal_invoice,#keberangkatan_kapal,#tanggal_surat_jalan,#tanggal_faktur_pajak").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('.divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.sales_order_export_id').select2({
            placeholder: "Pilih Order Form Ekspor",
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
        $('.sales_order_export_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.sales_order_export_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.sales_order_export_id, .divisi_id, .vendor_pelayaran_id, .tax_id, .tax_status')
            .parent('div')
            .find('label')
            .css('z-index', '1');


        $(".posting-so").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di Posting?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Posting',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("order-form-internasional/update-status"); ?>",
                        data: {
                            id: $(".id").val(),
                            status: "POSTED"
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        window.location.href = "<?= base_url("order-form-internasional"); ?>"
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Diubah, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })

        $(".unposting-so").click(function() {

            $(".unpost-modal").modal("show");

        });



        var validator = $("#form-parent").validate({
            rules: {
                sales_order_export_no: {
                    required: true
                },
                // divisi_id: {
                //     required: true
                // },
                sales_contract_id: {
                    required: true
                },
                tanggal: {
                    required: true
                },
                payment_term: {
                    required: true
                },
                deadline: {
                    required: true
                },
                // container: {
                //     required: true
                // },
                document_required: {
                    required: true
                }
            },
            messages: {
                sales_order_export_no: {
                    required: "Sales order no required"
                },
                // divisi_id: {
                //     required: "Departemen required"
                // },
                sales_contract_id: {
                    required: "Select sales contract"
                },
                tanggal: {
                    required: "Sales order date required"
                },
                payment_term: {
                    required: "Payment term required"
                },
                deadline: {
                    required: "Deadline required"
                },
                // container: {
                //     required: "Container required"
                // },
                document_required: {
                    required: "Document required"
                }
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
                var idBiayaEksporPajak = $('#id_biaya_ekspor_pajak').val();
                var tanggalFakturPajak = $('#tanggal_faktur_pajak').val();
                var noFakturPajak = $('#no_faktur_pajak').val();
                var taxId = $('#tax_id option:selected').val();
                var taxName = $('#tax_id option:selected').text();
                var nilaiPajak = destroyFormatRupiah($('#nilai_pajak').val());
                var taxStatus = $('#tax_status option:selected').val();
                var keteranganPajak = $('#keterangan_pajak').val();

                if (idBiayaEksporPajak) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listPajak.length; i++) {
                        if (listPajak[i].id_biaya_ekspor_pajak == idBiayaEksporPajak) {
                            index = i;
                            break;
                        }
                    }

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
                        keterangan_pajak: keteranganPajak
                    });
                }

                drawTablePengenaanPajak(listPajak);
                $('#detailPengenaanPajakModal').modal('hide');
                console.log(listPajak);
            }
        });

        $(".btn-submit-parent").click(function() {
            tinymce.triggerSave();

            var id = $('#id').val();
            var document_required = $('#document_required').val();
            var payment_term = $('#payment_term').val();

            console.log(listDataSalesKontrak);

            if (listDataSalesKontrak.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Please Select Sales Contract',
                    confirmButtonColor: '#4e73df',
                })
            } else if (document_required == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Document required, is required',
                    confirmButtonColor: '#4e73df',
                })
            } else if (payment_term == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Payment term, is required',
                    confirmButtonColor: '#4e73df',
                })
            } else {

                // Validasi Departemen
                var itemFailed = null;
                var sizeFailed = null;
                $.each(listDataSalesKontrak.salesContractDetailList, function(i, v) {
                    if (v.divisi_id == null || v.divisi_id == "") {
                        itemFailed = v;
                    }

                    $.each(v.size_breakdown, function(j, s) {
                        if ((s.satuan_convertion_id == null || s.satuan_convertion_id == "") && s.qty != 0) {
                            sizeFailed = s;
                        }
                    });
                });

                if (itemFailed != null) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Department for item ' + itemFailed.barang_name + ' required !',
                        confirmButtonColor: '#4e73df',
                    })
                } else if (sizeFailed != null) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fill in the Qty Order Form in Kg (red), if the goods are not included in the order form, fill in the qty with 0!',
                        confirmButtonColor: '#4e73df',
                    })
                } else {

                    if ($("#form-parent").valid()) {
                        Swal.fire({
                            icon: 'question',
                            title: id ? 'Update Data ?' : 'Create Data ?',
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: 'Save',
                            cancelButtonText: 'Back',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Init tiny ke textarea
                                tinymce.triggerSave();

                                const csrf = $(`[name="${csrfToken}"]`);
                                let data = new FormData(document.querySelector("#form-parent"));
                                let royaltyPrice = destroyFormatRupiah($('#royalty_price').val());
                                let rebatePrice = destroyFormatRupiah($('#rebate_price').val());
                                let canDeductionPrice = destroyFormatRupiah($('#can_deduction_price').val());
                                let estimatedFreightPrice = destroyFormatRupiah($('#estimated_freight_price').val());
                                let paletFumigationPrice = destroyFormatRupiah($('#palet_fumigation_price').val());
                                // let additionalDetailPrice = destroyFormatRupiah($('#additional_detail_price').val());
                                let othersPrice = destroyFormatRupiah($('#others_price').val());

                                data.set('royalty_price', royaltyPrice);
                                data.set('rebate_price', rebatePrice);
                                data.set('can_deduction_price', canDeductionPrice);
                                data.set('estimated_freight_price', estimatedFreightPrice);
                                data.set('palet_fumigation_price', paletFumigationPrice);
                                // data.set('additional_detail_price', additionalDetailPrice);
                                data.set('others_price', othersPrice);

                                data.append("listBiayaEkspor", JSON.stringify(listBiayaEkspor));
                                data.append("listDataSalesKontrak", JSON.stringify(listDataSalesKontrak));
                                data.append("listAdditional", JSON.stringify(listAdditional));

                                // UPDATE
                                if (id) {
                                    $.ajax({
                                        url: "<?= base_url("order-form-internasional/update"); ?>",
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
                                                        window.location.href = "<?= base_url("order-form-internasional") ?>";
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
                                } else {
                                    $.ajax({
                                        url: "<?= base_url("order-form-internasional/save"); ?>",
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
                                                        window.location.href = "<?= base_url("order-form-internasional") ?>";
                                                    })
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: response.message,
                                                    confirmButtonColor: '#4e73df',
                                                })
                                            }
                                        },

                                    });
                                }
                            }
                        })
                    }

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
                    var dataSalesOrderExport = res.dataSalesOrderExport;
                    var dataSalesContract = res.dataSalesExportDetail.salesContract;

                    $('#payment_term').val(dataSalesContract.payment_term);
                    $('#destination').val(dataSalesOrderExport.dicharge_port);
                    $('#no_container_order_form').val(dataSalesOrderExport.container);

                    $('#txt_valas').text("(" + dataSalesOrderExport.mata_uang + ")");
                    $('#txt_tipe_harga').text("(" + dataSalesOrderExport.tipe_harga + ")");

                    // DrawTable Detail Barang
                    drawTableBarangEkspor(res);

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


    function resetFormBiayaEkspor() {
        $('#id_biaya_ekspor_detail').val(null);
        $('#uraian_biaya').val(null);
        $('#nilai_biaya').val(null);

        drawTableBiayaEkspor(listBiayaEkspor);
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
                        <?php if (!empty($dataBiayaEskpor) && $dataBiayaEskpor->status == "POSTED") : ?>
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
                    <?php if (!empty($dataBiayaEskpor)) : ?> <?php if ($dataBiayaEskpor->status == "POSTED") : ?> `-`
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

        $('#label-biaya-eskpor').text("Update ");
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
    }

    function drawTableBarangEkspor(res) {
        var listBarang = res.dataSalesExportDetail.salesContractDetailList;
        var salesExport = res.dataSalesExportDetail;

        $('#body-detail-table-barang').empty();
        $('#foot-detail-table-barang').empty();
        var row = '';
        var no = 1;
        const table = $('#dataTableBarang');
        const tfoot = table.find('#foot-detail-table-barang');

        if (listBarang.length === 0) {
            row += `
                    <tr>
                        <td colspan="8">List Barang Eskpor Tidak Ada</td>
                    </tr>
                `;
            $('#foot-detail-table-barang').append(row);
        } else {
            var totalTotalHarga = 0;
            listBarang.map(item => {
                totalTotalHarga += item.total_input;

                var satuanOrderForm = "";
                item.size_breakdown.forEach(sizeBreakdown => {
                    satuanOrderForm = sizeBreakdown.satuan_size_code;
                });

                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.kode_barang));
                newRow.append($('<td>').text(item.barang_name));
                newRow.append($('<td class="text-right">').text(item.qty + " " + satuanOrderForm));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.harga)));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.total_input)));
                table.find('tbody').append(newRow);
            });

            console.log(salesExport);

            // Tfoot rapi dan sesuai jumlah kolom
            if (salesExport.royaltyPriceFinal > 0) {
                totalTotalHarga -= salesExport.royaltyPriceFinal;

                tfoot.append(`
                    <tr>
                        <th colspan="5" class="text-right">ROYALTY</th>
                        <th class="text-right">${greatFormatRupiah(salesExport.royaltyPriceFinal)}</th>
                    </tr>
                `);
            }

            if (salesExport.rebatePriceFinal > 0) {
                totalTotalHarga -= salesExport.rebatePriceFinal;

                tfoot.append(`
                    <tr>
                        <th colspan="5" class="text-right">REBATE</th>
                        <th class="text-right">${greatFormatRupiah(salesExport.rebatePriceFinal)}</th>
                    </tr>
                `);
            }


            if (salesExport.canDeductionPriceFinal > 0) {
                totalTotalHarga -= salesExport.canDeductionPriceFinal;

                tfoot.append(`
                    <tr>
                        <th colspan="5" class="text-right">CAN DEDUCTION</th>
                        <th class="text-right">${greatFormatRupiah(salesExport.canDeductionPriceFinal)}</th>
                    </tr>
                `);
            }

            if (salesExport.estimatedFreightPriceFinal > 0) {
                totalTotalHarga += salesExport.estimatedFreightPriceFinal;

                tfoot.append(`
                    <tr>
                        <th colspan="5" class="text-right">ESTIMATED FREIGHT</th>
                        <th class="text-right">${greatFormatRupiah(salesExport.estimatedFreightPriceFinal)}</th>
                    </tr>
                `);
            }

            if (salesExport.othersPriceFinal > 0) {
                if (salesExport.othersTypeFinal == "PLUS") {
                    totalTotalHarga += salesExport.othersPriceFinal;
                } else {
                    totalTotalHarga -= salesExport.othersPriceFinal;
                }

                tfoot.append(`
                    <tr>
                        <th colspan="5" class="text-right">OTHER PRICE</th>
                        <th class="text-right">${greatFormatRupiah(salesExport.othersPriceFinal)}</th>
                    </tr>
                `);
            }

            tfoot.append(`
                    <tr>
                        <th colspan="5" class="text-right">GRAND TOTAL</th>
                        <th class="text-right">${greatFormatRupiah(totalTotalHarga)}</th>
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

    const print = function(id) {
        $('.id').val(id);
        $('.print-modal').modal('show');
    }

    $('.btn-hide-print').click(function() {
        $('.print-modal').modal('hide');
    });

    function unPosting() {
        var csrf = $(`[name="${csrfToken}"]`);
        var date_revision = $('#date_revision').val();
        var keterangan = $('#keterangan_unpost').val();
        var state = true;

        // MAU UNPOSTING
        if (date_revision == "") {
            state = false;
            Swal.fire({
                icon: 'error',
                title: "Form Date Revision Required",
                confirmButtonColor: '#4e73df',
            })
        } else {
            $.ajax({
                url: "<?= base_url("order-form-internasional/update-status"); ?>",
                data: {
                    id: $(".id").val(),
                    status: "0",
                    date_revision: date_revision,
                    keterangan: keterangan,
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    stopLoading()
                },
                complete: function() {
                    stopLoading();
                },
                method: "POST",
                dataType: "json",
                success: function(response) {
                    csrf.val(response.token);
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


    }

    const printAction = function() {
        var id = $('.id').val();
        var display_price = $('.display_price').is(':checked');
        <?php if (session()->get('login')->this_company_id == 1): ?>
            var company_id = $('#company_id option:selected').val();
        <?php else: ?>
            var company_id = "<?= session()->get('login')->this_company_id ?>";
        <?php endif; ?>

        if (id == "") {
            alert("Failed Print : Order form not found");
        } else if (company_id == "") {
            alert("Please select company head")
        } else {
            var url = "/order-form-internasional/print/" + id + '?display_price=' + display_price + '&company_id=' + company_id
            window.open(url, "_blank");
        }
    }
</script>
<?= $this->endSection(); ?>