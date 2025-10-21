<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Duplikasi Proforma Invoice</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("proforma-invoice/detail/" . encrypt($dataSalesOrderExport->id)); ?>">
                Kembali
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Duplikasi
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data" id="form-parent">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Detail Invoice</label>
                    </div>
                </div>
                <input autocomplete="one-time-code" value="<?= !empty($dataPI) ? encrypt($dataPI['id']) : '' ?>" type="hidden" class="id" name="id" id="id" />
                <input type="hidden" name="sales_contract_id" id="sales_contract_id" value="<?= $dataSalesOrderExport->id ?>">
                <?= csrf_field() ?>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= $dataSalesOrderExport->sales_contract_no ?>" autocomplete="one-time-code" disabled type="text" class="form-control">
                            <label for="floatingInput">SC</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= strip_tags($dataSalesOrderExport->payment_term) ?>" autocomplete="one-time-code" disabled type="text" class="form-control no_invoice">
                            <label for="floatingInput">Payment Term (Dari Contract)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataPI) ? $dataPI['nama_customer'] :  $dataSalesOrderExport->customer_name ?>" autocomplete="one-time-code" type="text" class="form-control nama_customer" id="nama_customer" name="nama_customer">
                            <label for="floatingInput">Buyer / Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control alamat_customer" id="alamat_customer" name="alamat_customer" placeholder="Alamat Customer"><?= !empty($dataPI) ? $dataPI['alamat_customer'] :  $dataSalesOrderExport->address ?></textarea>
                            <label for="floatingInput">Alamat Customer</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Proforma Invoice</label>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" value="AUTO GENERATE" <?= !empty($dataPI) ? ($dataPI['status_posting'] == 1 ? 'readonly' : '') : 'readonly' ?> class="form-control no_invoice_pi" id="no_invoice_pi" name="no_invoice_pi" placeholder="No Invoice PI" required>
                                    <label for="floatingInput">No PI</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal_pi" id="tanggal_pi" name="tanggal_pi" placeholder="Tanggal PI" value="<?= !empty($dataPI) ? date('d/m/Y', strtotime($dataPI['tanggal_pi'])) : date('d/m/Y')  ?>">
                                    <label for="floatingInput">Tanggal PI</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select valas_id" id="valas_id" name="valas_id">
                                <option value=""></option>
                                <?php foreach ($dataValuta as $d) : ?>
                                    <option <?= !empty($dataPI) ? ($dataPI['valas_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d["id"]; ?>"><?= $d["value"]  ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput">Pilih Valas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control payment_term_parent" id="payment_term_parent" name="payment_term_parent" placeholder="Payment Term"><?= !empty($dataPI) ? $dataPI['payment_term'] : strip_tags($dataSalesOrderExport->payment_term); ?></textarea>
                            <label for="floatingInput">Term Of Payment</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control payment_instruction" id="payment_instruction" name="payment_instruction" placeholder="Payment Instruction"><?= !empty($dataPI) ? $dataPI['payment_instruction'] : '' ?></textarea>
                            <label for="floatingInput">Payment Instruction</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bank_id" id="bank_id" name="bank_id">
                                <option value=""></option>
                                <?php foreach ($dataBank as $d) : ?>
                                    <option <?= !empty($dataPI) ? ($dataPI['bank_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d["id"]; ?>"><?= $d["name"] . " - " . $d['atas_nama'] . " - " . $d['no_rekening']; ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput">Pilih Bank</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- <div class="form-floating mb-3">
                            <input value=""  autocomplete="one-time-code" type="text" class="form-control packing" id="packing" name="packing">
                            <label for="floatingInput">Packing</label>
                        </div> -->
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control packing" id="packing" name="packing" placeholder="Keterangan / Packing"><?= !empty($dataPI) ? $dataPI['packing'] : '' ?></textarea>
                            <label for="floatingInput">Keterangan / Packing</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataPI) ? $dataPI['penanda_tangan'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control penanda_tangan" id="penanda_tangan" name="penanda_tangan" placeholder="Penanda Tangan">
                            <label for="floatingInput">Penanda Tangan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control total_pi" id="total_pi" name="total_pi">
                            <label for="floatingInput">Total Proforma Invoice</label>
                        </div>
                    </div>
                </div>

                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold lable-title">List Barang Ekspor</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAddBarang" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="barangTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>Barang</th>
                                    <th>Keterangan</th>
                                    <th style="width: 100px; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-barang" id="body-barang">

                            </tbody>
                            <tfoot class="foot-barang" id="foot-barang">
                                <tr>
                                    <td colspan="4">List Barang Kosong</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="col-subtitle-modal mt-5">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">List Biaya Tambahan</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnBiayaTambahan" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="biayaTambahanTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>Biaya Tambahan</th>
                                    <th style="width: 120px;">Total Biaya</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-biaya-tambahan" id="body-biaya-tambahan">

                            </tbody>
                            <tfoot class="foot-biaya-tambahan" id="foot-biaya-tambahan">
                                <tr>
                                    <td colspan="4">List Biaya Tambahan Kosong</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="col-subtitle-modal mt-5">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold lable-title">List Payment Term</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAddPaymentTerm" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="paymentTermTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>Keterangan Payment Term</th>
                                    <th style="width: 120px; text-align:right;">Nilai</th>
                                    <th style="width: 100px; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-payment-term" id="body-payment-term">

                            </tbody>
                            <tfoot class="foot-payment-term" id="foot-payment-term">
                                <tr>
                                    <td colspan="4">List Payment Term Kosong</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </form>

        </div>
    </div>
</section>

<div class="modal detail-modal" id="barangModal" tabindex="1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-modal-barang"></label> Barang</h5>
            </div>
            <form id="form-barang" role="form" method="POST">
                <input type="hidden" name="id_barang" id="id_barang">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea autocomplete="one-time-code" class="full-textarea form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan"></textarea>
                                <label for="floatingInput">Keterangan (Opsional)</label>
                            </div>
                        </div>

                        <div class="col-subtitle-modal">
                            <div class="row mt-3 justify-content-end">
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold modal-sub-title"></label>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success btn-block float-right" type="button" id="btnAddSizeBreakdownModal">
                                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i> Tambah Size / Grade
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="productSizeBreakdown" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Size</th>
                                            <th>Grade</th>
                                            <th>Packing</th>
                                            <th>Satuan</th>
                                            <th>Qty</th>
                                            <th>Harga Satuan</th>
                                            <th>Total Harga</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-barang-size-breakdown">
                                        <!-- isi data -->
                                    </tbody>
                                    <tfoot class="tfoot-detail-table-size-breakdown">
                                        <tr>
                                            <td colspan="4"></td>
                                            <td><b>TOTAL</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b></b></td>
                                            <td><b>0.00</b></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideBarang">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitBarang">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal add-modal" id="addSizeBreakdownModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title "><label class="title-size-breakdown"></label> Size & Grade</h5>
            </div>
            <form class="create-form-size-breakdown" role="form" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_detail_breakdown" id="id_detail_breakdown">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control grade" name="grade" id="grade" placeholder="Grade (Opsional)">
                                <label for="floatingInput">Grade</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control size" name="size" id="size" placeholder="Size (Opsional)">
                                <label for="floatingInput">Size (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control packing_size" name="packing_size" id="packing_size" placeholder="Packing (Opsional)">
                                <label for="floatingInput">Packing (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col mb-3">
                            <h6 class="<?= session()->get('theme') == "dark" ? "text-white" : "text-dark" ?>">Detail Harga</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_size_id" name="satuan_size_id" id="satuan_size_id">
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d) : ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" onkeyup="this.value = greatFormatRupiah(this.value)" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideSizeBreakdownModal">Kembali</button>
                    <button type="submit" class="btn btn-submit-form" id="btnSubmitSizeBreakDown">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="paymentTermModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-payment-term"></label> Payment Term</h5>
            </div>
            <form id="form-payment-term" role="form" method="POST">
                <input type="hidden" name="id_payment_term" id="id_payment_term">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 100px;">
                                <textarea name="payment_term" id="payment_term" class="form-control full-textarea payment_term" placeholder="Payment Term"></textarea>
                                <label for="floatingInput">Keterangan Payment Term</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="presentase" name="presentase" placeholder="Nilai Presentase" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Nilai Presentase</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control" id="nilai_payment_term" name="nilai_payment_term" placeholder="Nilai Payment Term" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Nilai Payment Term</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form">
                                <label class="mt-2 text-dark">
                                    <b>Masukkan ke Penagihan Proforma Invoice,</b> (Jika Aktif Maka Akan Dijadikan Penagihan Proforma Invoice Ini)
                                </label>
                                <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input" type="checkbox" value="1" name="is_penagihan" id="is_penagihan">
                                        <label class="form-check-label"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHidePaymentTerm">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitPaymentTerm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="biayaTambahanModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-detail-biaya-tambahan"></label> Biaya Tambahan</h5>
            </div>
            <form class="create-form-biaya-tambahan" role="form" method="POST">
                <input type="hidden" name="id_biaya_tambahan" id="id_biaya_tambahan">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control biaya_tambahan" id="biaya_tambahan" name="biaya_tambahan" placeholder="Deskripsi Biaya Tambahan">
                                <label for="floatingInput">Deskripsi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select name="tipe_biaya_tambahan" id="tipe_biaya_tambahan" class="form-control tipe_biaya_tambahan">
                                        <option value="PLUS">PLUS (+)</option>
                                        <option value="MINUS">MINUS (-)</option>
                                    </select>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control nilai_biaya_tambahan" id="nilai_biaya_tambahan" name="nilai_biaya_tambahan" placeholder="Nilai Biaya Tambahan">
                                    <label for="floatingInput">Nilai</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideBiayaTambahan">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitBiayaTambahan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listBarang = [];
    var listPaymentTerm = [];
    var listBiayaTambahan = [];
    var listSizeBreakdown = [];

    $(document).ready(function() {
        <?php if (!empty($dataPI)) { ?>
            listBarang = <?= json_encode($dataPIBarang) ?>;

            <?php foreach ($dataPIPaymentTerm as $d): ?>
                listPaymentTerm.push({
                    id_payment_term: "<?= $d['id'] ?>",
                    payment_term: "<?= $d['payment_term'] ?>",
                    nilai_payment_term: <?= floatval($d['nilai_payment_term']) ?>,
                    is_penagihan: <?= $d['is_penagihan'] ?>,
                    presentase: <?= $d['presentase'] ?>
                });
            <?php endforeach ?>

            <?php foreach ($dataPIBiaya as $d): ?>
                listBiayaTambahan.push({
                    id_biaya_tambahan: "<?= $d['id'] ?>",
                    biaya_tambahan: "<?= $d['biaya_tambahan'] ?>",
                    tipe_biaya_tambahan: "<?= $d['tipe_biaya_tambahan'] ?>",
                    nilai_biaya_tambahan: <?= floatval($d['nilai_biaya_tambahan']) ?>
                });
            <?php endforeach; ?>
            drawTableBarang(listBarang);
            drawTablePaymentTerm(listPaymentTerm);
            drawTableBiayaTambahan(listBiayaTambahan);

        <?php } else { ?>

        <?php } ?>


        $("#tanggal_pi,#keberangkatan_kapal,#tanggal_surat_jalan,#tanggal_faktur_pajak").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('.valas_id').select2({
            placeholder: "Pilih Valas",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.bank_id').select2({
            placeholder: "Pilih Bank",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.satuan_id').select2({
            placeholder: "Pilih Satuan",
            theme: "bootstrap-5",
            dropdownParent: $('#barangModal')
        }).change(function() {});

        $('.company_id').select2({
            placeholder: "Pilih Kop Surat",
            theme: "bootstrap-5",
            dropdownParent: $('.kopsurat-modal')
        }).change(function() {});

        $('#btn-hide-kopsurat').click(function(e) {
            e.preventDefault();
            $('.kopsurat-modal').modal('hide');
        });

        $('#satuan_size_id').select2({
            placeholder: "Pilih Satuan",
            theme: "bootstrap-5",
            dropdownParent: $('#addSizeBreakdownModal')
        });

        $('#btnAddSizeBreakdownModal').click(function() {
            clearFormSizeBreakdown();
            $('.title-size-breakdown').text("Tambah ");
            $('#addSizeBreakdownModal').modal('show');
        });

        $('#btnHideSizeBreakdownModal').click(function() {
            $('#addSizeBreakdownModal').modal('hide');
        });

        // QTY KEYUP
        $('#qty,#harga').keyup(function(e) {
            e.preventDefault();
            var qty = parseFloat($('.qty').val()) || 0;
            var harga = destroyFormatRupiah($('.harga').val()) || 0;
            var total = qty * harga;
            $('#total').val(greatFormatRupiah(total.toFixed(2)));
        });

        $('#total').keyup(function(e) {
            e.preventDefault();
            var qty = parseFloat($('#qty').val()) || 1; // menghindari dibagi 0
            var total = destroyFormatRupiah($('#total').val());
            var harga = parseFloat(total / qty).toFixed(2);
            $('#harga').val(greatFormatRupiah(harga));
        })

        //CSS SELECT2 FLOATING LABEL
        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_id,.company_id,.satuan_size_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_id,.company_id,.satuan_size_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_id,.company_id,.satuan_size_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $("#form-parent").validate({
            rules: {
                nama_customer: {
                    required: true
                },
                alamat_customer: {
                    required: true
                },
                no_invoice_pi: {
                    required: true
                },
                tanggal_pi: {
                    required: true
                },
                valas_id: {
                    required: true
                },
                payment_term_parent: {
                    required: true
                },
                payment_instruction: {
                    required: true
                },
                bank_id: {
                    required: true
                },
                packing: {
                    required: true
                },
                penanda_tangan: {
                    required: true
                },
            },
            messages: {
                nama_customer: {
                    required: "Nama customer wajib diisi"
                },
                alamat_customer: {
                    required: "Alamat customer wajib diisi"
                },
                no_invoice_pi: {
                    required: "No Invoice PI wajib diisi"
                },
                tanggal_pi: {
                    required: "Tanggal PI wajib diisi"
                },
                valas_id: {
                    required: "Valas wajib diisi"
                },
                payment_term_parent: {
                    required: "Payment term wajib diisi"
                },
                payment_instruction: {
                    required: "Payment instruction wajib diisi"
                },
                bank_id: {
                    required: "Pilih bank"
                },
                packing: {
                    required: "Packing wajib diisi"
                },
                penanda_tangan: {
                    required: "Penanda tangan wajib diisi"
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

        var validatorBarang = $("#form-barang").validate({
            rules: {
                nama_barang: {
                    required: true
                },
            },
            messages: {
                nama_barang: {
                    required: "barang wajib diisi"
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

        var validatorPaymentTerm = $("#form-payment-term").validate({
            rules: {
                payment_term: {
                    required: true
                },
                presentase: {
                    required: true
                },
                nilai_payment_term: {
                    required: true
                },
            },
            messages: {
                payment_term: {
                    required: "Payment term wajib diisi"
                },
                presentase: {
                    required: "Presentase wajib diisi"
                },
                nilai_payment_term: {
                    required: "Nilai payment term wajib diisi"
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

        var validatorBiayaTambahan = $(".create-form-biaya-tambahan").validate({
            rules: {
                biaya_tambahan: {
                    required: true
                },
                nilai_biaya_tambahan: {
                    required: true
                },
            },
            messages: {
                biaya_tambahan: {
                    required: "Deskripsi wajib diisi"
                },
                nilai_biaya_tambahan: {
                    required: "Biaya tambahan wajib diisi"
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

        var validatorSizeBreakdown = $('.create-form-size-breakdown').validate({
            rules: {
                grade: {
                    required: true
                },
                qty: {
                    required: true
                },
                harga: {
                    required: true
                },
                satuan_size_id: {
                    required: true
                }
            },
            messages: {
                grade: {
                    required: "Grade required"
                },
                qty: {
                    required: "Qty required"
                },
                harga: {
                    required: "Price required"
                },
                satuan_size_id: {
                    required: "Unit required"
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


        $('#btnSubmitSizeBreakDown').click(function(e) {
            e.preventDefault();
            if ($('.create-form-size-breakdown').valid()) {
                var id_detail_breakdown = $('#id_detail_breakdown').val();
                var size = $('#size').val();
                var grade = $('#grade').val();
                var packing_size = $('#packing_size').val();
                var qty = $('#qty').val();
                var harga = parseFloat(destroyFormatRupiah($('#harga').val())).toFixed(2);
                var total = parseFloat(destroyFormatRupiah($('#total').val())).toFixed(2);
                var satuan_size_id = $('#satuan_size_id option:selected').val();
                var satuan_size_code = $('#satuan_size_id option:selected').text();

                var result = {
                    id_detail_breakdown: id_detail_breakdown,
                    size: size,
                    grade: grade,
                    packing_size: packing_size,
                    qty: qty,
                    harga: harga,
                    total: total,
                    satuan_size_id: satuan_size_id,
                    satuan_size_code: satuan_size_code,
                }

                if (id_detail_breakdown == '') {
                    // Create
                    result.id_detail_breakdown = getID();
                    listSizeBreakdown.push(result);
                } else {
                    // Update
                    var index = null;
                    for (var i = 0; i < listSizeBreakdown.length; i++) {
                        if (listSizeBreakdown[i].id_detail_breakdown == id_detail_breakdown) {
                            index = i;
                            break;
                        }
                    }

                    listSizeBreakdown[index].size = result.size;
                    listSizeBreakdown[index].grade = result.grade;
                    listSizeBreakdown[index].packing_size = result.packing_size;
                    listSizeBreakdown[index].qty = result.qty;
                    listSizeBreakdown[index].harga = result.harga;
                    listSizeBreakdown[index].total = result.total;
                    listSizeBreakdown[index].satuan_size_id = result.satuan_size_id;
                    listSizeBreakdown[index].satuan_size_code = result.satuan_size_code;
                }

                drawTableListSizeBreakDown(listSizeBreakdown);
                $('#addSizeBreakdownModal').modal('hide');

            }
        });


        $('#btnAddPaymentTerm').click(function(e) {
            e.preventDefault();
            if (listBarang.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan list barang terlebih dahulu !',
                    confirmButtonColor: '#4e73df',
                });
            } else {
                $("#label-payment-term").text("Tambah ");
                $('#paymentTermModal').modal('show');
                resetFormPaymentTerm();
            }
        });

        $('#btnBiayaTambahan').click(function() {
            resetFormBiayaTambahan();
            $('#label-detail-biaya-tambahan').text('Tambah ');
            $('#biayaTambahanModal').modal('show');
        });

        $('#btnSubmitBiayaTambahan').click(function(e) {
            e.preventDefault();
            if ($('.create-form-biaya-tambahan').valid()) {
                var idBiayaTambahan = $('#id_biaya_tambahan').val();
                var biayaTambahan = $('#biaya_tambahan').val();
                var tipeBiayaTambahan = $('#tipe_biaya_tambahan').val();
                var nilaiBiayaTambahan = destroyFormatRupiah($('#nilai_biaya_tambahan').val());

                if (idBiayaTambahan) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listBiayaTambahan.length; i++) {
                        if (listBiayaTambahan[i].id_biaya_tambahan == idBiayaTambahan) {
                            index = i;
                            break;
                        }
                    }

                    listBiayaTambahan[index].biaya_tambahan = biayaTambahan;
                    listBiayaTambahan[index].tipe_biaya_tambahan = tipeBiayaTambahan;
                    listBiayaTambahan[index].nilai_biaya_tambahan = nilaiBiayaTambahan;
                } else {
                    // CREATE
                    idBiayaTambahan = getID();
                    listBiayaTambahan.push({
                        id_biaya_tambahan: idBiayaTambahan,
                        biaya_tambahan: biayaTambahan,
                        tipe_biaya_tambahan: tipeBiayaTambahan,
                        nilai_biaya_tambahan: nilaiBiayaTambahan
                    });
                }

                $('#biayaTambahanModal').modal('hide');
                drawTableBiayaTambahan(listBiayaTambahan);
            }
        });

        $('#btnAddBarang').click(function(e) {
            e.preventDefault();
            $('#label-modal-barang').text("Tambah ");
            $('#barangModal').modal('show');
            listSizeBreakdown = []; // reset size breakdown
            drawTableListSizeBreakDown(listSizeBreakdown);
            resetFormBarang();
        });

        $('#btnHideBarang').click(function(e) {
            e.preventDefault();
            $('#barangModal').modal('hide');
        });

        $('#qty_barang,#harga_satuan').keyup(function(e) {
            e.preventDefault();
            var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
            var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
            var totalHarga = qtyBarang * hargaSatuan;

            $('#total_harga').val(greatFormatRupiah(totalHarga.toFixed(2)));
        });

        $('#btnSubmitBarang').click(function(e) {
            e.preventDefault();
            if (listSizeBreakdown.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan list size / packing dahulu !',
                    confirmButtonColor: '#4e73df',
                });
                return;
            } else {
                if ($('#form-barang').valid()) {
                    var idBarang = $('#id_barang').val();
                    var namaBarang = $('#nama_barang').val();
                    var keterangan = $('#keterangan').val();

                    if (idBarang) {
                        // UPDATE
                        var index = null;
                        for (var i = 0; i < listBarang.length; i++) {
                            if (listBarang[i].id_barang == idBarang) {
                                index = i;
                                break;
                            }
                        }

                        listBarang[index].nama_barang = namaBarang;
                        listBarang[index].keterangan = keterangan;
                        listBarang[index].size_breakdown = listSizeBreakdown;

                    } else {
                        // CREATE
                        listBarang.push({
                            id_barang: getID(),
                            nama_barang: namaBarang,
                            keterangan: keterangan,
                            size_breakdown: listSizeBreakdown
                        });
                    }

                    $('#barangModal').modal('hide');
                    drawTableBarang(listBarang);
                }
            }

        });

        $('#btnHideBiayaTambahan').click(function() {
            $('#biayaTambahanModal').modal('hide');
        });

        $('#btnHidePaymentTerm').click(function(e) {
            e.preventDefault();
            $('#paymentTermModal').modal('hide');
        });

        $('#qty_barang,#harga_satuan').keyup(function(e) {
            e.preventDefault();
            var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
            var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
            var totalHarga = parseFloat(qtyBarang) * parseFloat(hargaSatuan);
            $('#total_harga').val(greatFormatRupiah(totalHarga));
        });

        $('#presentase').keyup(function(e) {
            e.preventDefault();
            var presentase = destroyFormatRupiah($('#presentase').val());
            var totalNilaiBarang = 0;
            $.each(listBarang, function(i, v) {
                $.each(v.size_breakdown, function(j, v2) {
                    totalNilaiBarang += v2.total;
                });
            });

            var presentaseDecimal = presentase / 100;
            var nilaiPaymentTerm = presentaseDecimal * totalNilaiBarang;
            $('#nilai_payment_term').val(greatFormatRupiah(nilaiPaymentTerm.toFixed()));
        });

        $('#btnSubmitPaymentTerm').click(function(e) {
            e.preventDefault();
            if ($('#form-payment-term').valid()) {
                var idPaymentTerm = $('#id_payment_term').val();
                var paymentTerm = $('#payment_term').val();
                var nilaiPaymentTerm = destroyFormatRupiah($('#nilai_payment_term').val());
                var presentase = destroyFormatRupiah($('#presentase').val());
                var isPenagihan = $('#is_penagihan').is(':checked');

                if (idPaymentTerm) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listPaymentTerm.length; i++) {
                        if (listPaymentTerm[i].id_payment_term == idPaymentTerm) {
                            index = i;
                            break;
                        }
                    }

                    listPaymentTerm[index].presentase = presentase;
                    listPaymentTerm[index].payment_term = paymentTerm;
                    listPaymentTerm[index].nilai_payment_term = parseFloat(nilaiPaymentTerm);
                    listPaymentTerm[index].is_penagihan = isPenagihan;

                } else {
                    // Create
                    idPaymentTerm = getID();
                    listPaymentTerm.push({
                        id_payment_term: idPaymentTerm,
                        payment_term: paymentTerm,
                        nilai_payment_term: parseFloat(nilaiPaymentTerm),
                        is_penagihan: isPenagihan,
                        presentase: presentase
                    });
                }

                drawTablePaymentTerm(listPaymentTerm);
                $('#paymentTermModal').modal('hide');

            }
        });

        $(".btn-submit-parent").click(function() {
            var totalPi = destroyFormatRupiah($('#total_pi').val());
            if (listBarang.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan list barang ekspor !',
                    confirmButtonColor: '#4e73df',
                })
            } else if (listPaymentTerm.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan payment term yang akan dibayar !',
                    confirmButtonColor: '#4e73df',
                })
            } else if (totalPi == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Total proforma invoice tidak boleh kosong !',
                    confirmButtonColor: '#4e73df',
                })
            } else {
                var id = $('#id').val();
                if ($("#form-parent").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Duplikasi Data ?',
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
                            let url = "<?= base_url('proforma-invoice/create') ?>";
                            let data = new FormData(document.querySelector("#form-parent"));
                            let totalPI = destroyFormatRupiah($('#total_pi').val());

                            data.append("total_pi", totalPI);
                            data.append("listBarang", JSON.stringify(listBarang));
                            data.append("listPaymentTerm", JSON.stringify(listPaymentTerm));
                            data.append("listBiayaTambahan", JSON.stringify(listBiayaTambahan));

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
                                                window.location.href = "<?= base_url("proforma-invoice/detail/" . encrypt($dataSalesOrderExport->id)) ?>";
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

    function resetFormPaymentTerm() {
        $('#id_payment_term').val(null);
        $('#payment_term').val(null);
        $('#nilai_payment_term').val(null);
        $('#presentase').val(null);
        $('#is_penagihan').prop('checked', false);
    }

    function resetFormBarang() {
        $('#id_barang').val(null);
        $('#nama_barang').val(null);
        $('#keterangan').val(null).change();

    }

    function detailRowSizeBreakdown(id) {
        var item = null;
        for (var i = 0; i < listSizeBreakdown.length; i++) {
            if (listSizeBreakdown[i].id_detail_breakdown == id) {
                item = listSizeBreakdown[i];
                break;
            }
        }

        $('#id_detail_breakdown').val(item.id_detail_breakdown);
        $('#size').val(item.size);
        $('#grade').val(item.grade);
        $('#packing_size').val(item.packing_size);
        $('#qty').val(item.qty);
        $('#harga').val(greatFormatRupiah(item.harga));
        $('#total').val(greatFormatRupiah(item.total));
        $('#satuan_size_id').val(item.satuan_size_id).change();

        $('.title-size-breakdown').text("Update ");
        $('#addSizeBreakdownModal').modal('show');
    }

    function deleteRowSizeBreakdown(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listSizeBreakdown.length; i++) {
            if (listSizeBreakdown[i].id_detail_breakdown == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listSizeBreakdown.splice(indexToRemove, 1);
        }
        drawTableListSizeBreakDown(listSizeBreakdown);
    }


    function drawTableBarang(listBarang) {
        const table = $('#barangTable');
        const tbody = table.find('#body-barang');
        const tfoot = table.find('#foot-barang');

        tbody.empty();
        tfoot.empty();

        if (listBarang.length === 0) {
            tfoot.append(`
            <tr>
                <td colspan="7" >List Barang Kosong</td>
            </tr>
        `);
        } else {
            let no = 1;
            let totalQty = 0;
            let totalTotal = 0;

            listBarang.forEach(item => {
                const newRow = $(`
                    <tr style="color:whitesmoke;">
                        <td class="text-center">${no++}</td>
                        <td>${item.nama_barang}</td>
                        <td>${item.keterangan}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBarang('${item.id_barang}')">
                                <i class="fa fa-pencil fa-sm"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRowBarang('${item.id_barang}')">
                                <i class="fa fa-trash fa-sm"></i>
                            </button>
                        </td>
                    </tr>
                `);

                tbody.append(newRow);

                // === Row Kedua: Breakdown Table ===
                const detailRow = $('<tr style="color:whitesmoke;">');
                const innerTable = $(`
                        <table class="table table-sm table-bordered mb-2 w-100">
                            <thead class="bg-warning text-dark">
                                <tr>
                                    <th>Size</th>
                                    <th>Grade</th>
                                    <th>Packing</th>
                                    <th>Satuan</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><b>TOTAL</b></td>
                                    <td class="total-qty"><b>0.00</b></td>
                                    <td class="total-price"></td>
                                    <td class="total-amount"><b>0.00</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    `);

                const breakdownBody = innerTable.find('tbody');
                let totalQtySize = 0;
                let totalAmountSize = 0;

                item.size_breakdown.forEach(size => {
                    let qty = parseFloat(size.qty) || 0;
                    let total = parseFloat(size.total) || 0;
                    let harga = parseFloat(size.harga) || 0;

                    totalQtySize += qty;
                    totalAmountSize += total;

                    const row = `
                            <tr>
                                <td>${size.size || ''}</td>
                                <td>${size.grade || ''}</td>
                                <td>${size.packing_size || ''}</td>
                                <td>${size.satuan_size_code || ''}</td>
                                <td>${greatFormatRupiah(qty.toFixed(2))}</td>
                                <td>${greatFormatRupiah(harga.toFixed(2))}</td>
                                <td>${greatFormatRupiah(total.toFixed(2))}</td>
                            </tr>
                        `;
                    breakdownBody.append(row);

                });

                innerTable.find('.total-qty').html(`<b>${greatFormatRupiah(totalQtySize.toFixed(2))}</b>`);
                innerTable.find('.total-amount').html(`<b>${greatFormatRupiah(totalAmountSize.toFixed(2))}</b>`);

                detailRow.append(`<td colspan="4"><b>SIZE & BREAKDOWN</b><br>${innerTable.prop('outerHTML')}</td>`);
                tbody.append(detailRow);

                totalQty += totalQtySize;
                totalTotal += totalAmountSize;
            });

            // === Footer Total ===
            const totalRow = $(`
                <tr class="bg-light">
                    <td class="text-end"><b>TOTAL</b></td>
                    <td style="text-align:right;"><b>${greatFormatRupiah(totalQty)}</b></td>
                    <td style="text-align:right;"><b>${greatFormatRupiah(totalTotal.toFixed(2))}</b></td>
                    <td></td>
                </tr>
            `);
            tfoot.append(totalRow);
        }
    }


    function drawTableListSizeBreakDown(listSizeBreakdown) {
        $('.body-barang-size-breakdown').empty();
        $('.tfoot-detail-table-size-breakdown').empty();
        var row = '';
        var no = 1;
        const table = $('#productSizeBreakdown');
        if (listSizeBreakdown.length === 0) {
            row += `
                    <tr>
                        <td colspan="4"></td>
                        <td><b>TOTAL</b></td>
                        <td><b>0.00</b></td>
                        <td><b></b></td>
                        <td><b>0.00</b></td>
                        <td></td>
                    </tr>
                `;
            $('.tfoot-detail-table-size-breakdown').append(row);
        } else {
            var totalQty = 0;
            var totalHarga = 0;
            var totalTotalHarga = 0;

            listSizeBreakdown.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.size));
                newRow.append($('<td>').text(item.grade));
                newRow.append($('<td>').text(item.packing_size));
                newRow.append($('<td>').text(item.satuan_size_code));
                newRow.append($('<td>').text(greatFormatRupiah(item.qty)));
                newRow.append($('<td>').text(greatFormatRupiah(item.harga)));
                newRow.append($('<td>').text(greatFormatRupiah(item.total)));
                newRow.append($('<td>').html(
                    `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowSizeBreakdown('${item.id_detail_breakdown}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowSizeBreakdown('${item.id_detail_breakdown}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                ));

                totalQty += parseFloat(item.qty);
                totalHarga += destroyFormatRupiah(item.harga);
                totalTotalHarga += destroyFormatRupiah(item.total);

                table.find('tbody').append(newRow);
            });
            $('#body-barang-size-breakdown').append(row);
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td colspan="4"></td>'));
            newRow.append($('<td><b>TOTAL</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalQty) + '</b></td>'));
            newRow.append($('<td><b></b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalTotalHarga.toFixed(2)) + '</b></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);
        }
    }

    function drawTablePaymentTerm(listPaymentTerm) {
        $('#body-payment-term').empty();
        $('#foot-payment-term').empty();
        var row = '';
        var no = 1;
        const table = $('#paymentTermTable');
        if (listPaymentTerm.length === 0) {
            row += `
                    <tr>
                        <td colspan="4">List Payment Term Kosong</td>
                    </tr>
                `;
            $('#foot-payment-term').append(row);
        } else {
            listPaymentTerm.map(item => {
                var bgcolor = 'whitesmoke';
                <?php if (session()->get('theme') == 'dark'): ?>
                    if (item.is_penagihan) {
                        bgcolor = '#d76a6aff';
                    } else {
                        bgcolor = '#343A40';

                    }
                <?php else: ?>
                    if (item.is_penagihan) {
                        bgcolor = '#ebe520ff';
                    }
                <?php endif; ?>

                var newRow = $('<tr style="background-color:' + bgcolor + ';color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.payment_term));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.nilai_payment_term)));
                newRow.append($('<td class="text-right">').html(
                    `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPaymentTerm('${item.id_payment_term}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowPaymentTerm('${item.id_payment_term}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                ));

                table.find('tbody').append(newRow);
            });
        }
        recalculatePIPayed(listPaymentTerm);
    }

    function drawTableBiayaTambahan(listBiayaTambahan) {
        $('#body-biaya-tambahan').empty();
        $('#foot-biaya-tambahan').empty();
        var row = '';
        var no = 1;
        const table = $('#biayaTambahanTable');
        if (listBiayaTambahan.length === 0) {
            row += `
                    <tr>
                        <td colspan="4">List Additional Empty</td>
                    </tr>
                `;
            $('#foot-biaya-tambahan').append(row);
        } else {
            listBiayaTambahan.map(item => {
                var iconOperator = item.tipe_biaya_tambahan == "PLUS" ? "(+)" : "(-)";
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.biaya_tambahan));
                newRow.append($('<td>').text(iconOperator + " " + greatFormatRupiah(item.nilai_biaya_tambahan)));
                newRow.append($('<td>').html(
                    `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBiayaTambahan('${item.id_biaya_tambahan}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowBiayaTambahan('${item.id_biaya_tambahan}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                ));

                table.find('tbody').append(newRow);
            });
        }

    }


    function detailRowBiayaTambahan(id_biaya_tambahan) {
        var item = null;
        for (var i = 0; i < listBiayaTambahan.length; i++) {
            if (listBiayaTambahan[i].id_biaya_tambahan == id_biaya_tambahan) {
                item = listBiayaTambahan[i];
                break;
            }
        }

        $('#id_biaya_tambahan').val(item.id_biaya_tambahan);
        $('#biaya_tambahan').val(item.biaya_tambahan);
        $('#tipe_biaya_tambahan').val(item.tipe_biaya_tambahan);
        $('#nilai_biaya_tambahan').val(greatFormatRupiah(item.nilai_biaya_tambahan));

        $('#label-detail-biaya-tambahan').text("Update ");
        $('#biayaTambahanModal').modal('show');
    }

    function deleteRowBiayaTambahan(id_biaya_tambahan) {
        var indexToRemove = -1;
        for (var i = 0; i < listBiayaTambahan.length; i++) {
            if (listBiayaTambahan[i].id_biaya_tambahan == id_biaya_tambahan) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listBiayaTambahan.splice(indexToRemove, 1);
        }
        drawTableBiayaTambahan(listBiayaTambahan);
    }


    function detailRowBarang(id_barang) {
        var item = null;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].id_barang == id_barang) {
                item = listBarang[i];
                break;
            }
        }

        $('#id_barang').val(item.id_barang);
        $('#nama_barang').val(item.nama_barang);
        $('#keterangan').val(item.keterangan);

        // show detail
        listSizeBreakdown = item.size_breakdown;
        drawTableListSizeBreakDown(listSizeBreakdown);

        $('#label-modal-barang').text("Update ");
        $('#barangModal').modal('show');
    }

    function detailRowPaymentTerm(id_payment_term) {
        var item = null;
        for (var i = 0; i < listPaymentTerm.length; i++) {
            if (listPaymentTerm[i].id_payment_term == id_payment_term) {
                item = listPaymentTerm[i];
                break;
            }
        }

        $('#id_payment_term').val(item.id_payment_term);
        $('#payment_term').val(item.payment_term);
        $('#presentase').val(greatFormatRupiah(item.presentase));
        $('#nilai_payment_term').val(greatFormatRupiah(item.nilai_payment_term));
        $('#is_penagihan').prop('checked', item.is_penagihan).change();

        $('#label-payment-term').text("Update ");
        $('#paymentTermModal').modal('show');
    }

    function deleteRowPaymentTerm(id_payment_term) {
        var indexToRemove = -1;
        for (var i = 0; i < listPaymentTerm.length; i++) {
            if (listPaymentTerm[i].id_payment_term == id_payment_term) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listPaymentTerm.splice(indexToRemove, 1);
        }
        drawTablePaymentTerm(listPaymentTerm);
    }

    function deleteRowBarang(id_barang) {
        var indexToRemove = -1;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].id_barang == id_barang) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listBarang.splice(indexToRemove, 1);
        }
        drawTableBarang(listBarang);
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
            $('#no_invoice_pi').val("AUTO GENERATE");
            $('#no_invoice_pi').attr('readonly', true);
        } else {
            // SET NOMOR AUTO GENERATE FALSE
            $('#no_invoice_pi').val(null);
            $('#no_invoice_pi').attr('readonly', false);
        }

    }

    function recalculatePIPayed(listPaymentTerm) {
        var totalPaymentTerm = 0;
        $.each(listPaymentTerm, function(i, v) {
            if (v.is_penagihan) {
                totalPaymentTerm = totalPaymentTerm + v.nilai_payment_term;
            }
        });

        $('#total_pi').val(greatFormatRupiah(totalPaymentTerm));
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
                    url: "<?= base_url("proforma-invoice/posting"); ?>",
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
                                    var salesOrderExportId = "<?= !empty($dataPI) ? encrypt($dataPI['sales_contract_id']) : '' ?>";
                                    location.href = "<?= base_url('proforma-invoice/detail/') ?>" + salesOrderExportId;
                                })
                        }
                    },
                });
            }
        })

    }

    function clearFormSizeBreakdown() {
        $('#id_detail_breakdown').val(null);
        $('#size').val(null);
        $('#grade').val(null);
        $('#packing_size').val(null);
        $('#can').val(null);
        $('#case').val(null);
        $('#kg').val(null);
        $('#lb').val(null);
        $('#inner_box').val(null);
        $('#pc').val(null);
        $('#bag').val(null);
        $('#persen').val(null);
        $('#qty').val(null);
        $('#harga').val(null);
        $('#total').val(null);
        $('#satuan_size_id').val(null).change();
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
                    url: "<?= base_url("proforma-invoice/unposting"); ?>",
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


    function print(id) {
        $('#id').val(id);
        <?php if (in_array(session()->get('login')->this_company_id, [1, 2])): ?>
            $('.kopsurat-modal').modal('show');
        <?php else: ?>
            var companyId = "<?= session()->get('login')->this_company_id; ?>";
            var url = "<?= base_url('proforma-invoice/print/') ?>" + id + '?company_id=' + companyId;
            window.open(url, "_blank");
        <?php endif; ?>
    }

    function print2() {
        var id = $('#id').val();
        var companyId = $('#company_id').val();
        if (companyId == '') {
            Swal.fire({
                icon: 'error',
                title: "Pilih kop surat perusahaan",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            var url = "<?= base_url('proforma-invoice/print/') ?>" + id + '?company_id=' + companyId;
            window.open(url, "_blank");
        }
    }

    function resetFormBiayaTambahan() {
        $('#id_biaya_tambahan').val(null);
        $('#biaya_tambahan').val(null);
        $('#tipe_biaya_tambahan').val("PLUS").change();
        $('#nilai_biaya_tambahan').val(null);
    }
</script>
<?= $this->endSection(); ?>