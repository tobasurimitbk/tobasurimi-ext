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
        <h1 class="title-name">Duplikasi CIPL</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("invoice-packing-customer/detail/" . encrypt($dataSalesOrderExport->sales_order_export_id)); ?>">
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
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Detail Invoice</label>
                    </div>
                </div>
                <input autocomplete="one-time-code" value="<?= !empty($dataInvoice) ? encrypt($dataInvoice['id']) : '' ?>" type="hidden" class="id" name="id" id="id" />
                <input type="hidden" name="sales_order_export_id" id="sales_order_export_id" value="<?= $dataSalesOrderExport->sales_order_export_id ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= $dataSalesOrderExport->no_invoice ?>" autocomplete="one-time-code" disabled type="text" class="form-control">
                            <label for="floatingInput">No Invoice</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? date('d/m/Y', strtotime($dataInvoice['tanggal_invoice'])) : date('d/m/Y', strtotime($dataSalesOrderExport->tanggal_invoice))  ?>" autocomplete="one-time-code" name="tanggal_invoice" type="text" class="form-control tanggal_invoice" id="tanggal_invoice">
                            <label for="floatingInput">Tanggal Invoice</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= $dataSalesOrderExport->sales_order_export_no ?>" autocomplete="one-time-code" disabled type="text" class="form-control">
                            <label for="floatingInput">SC</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['nama_customer'] : $dataSalesOrderExport->customer_name ?>" autocomplete="one-time-code" name="nama_customer" type="text" class="form-control nama_customer">
                            <label for="floatingInput">Buyer / Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['loading_port'] : strip_tags($dataSalesOrderExport->loading_port) ?>" autocomplete="one-time-code" name="loading_port" type="text" class="form-control loading_port">
                            <label for="floatingInput">Port Of Loading</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['dicharge_port'] :  strip_tags($dataSalesOrderExport->dicharge_port) ?>" autocomplete="one-time-code" name="dicharge_port" type="text" class="form-control dicharge_port">
                            <label for="floatingInput">Port Of Discharge</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= strip_tags($dataSalesOrderExport->tipe_harga) ?>" autocomplete="one-time-code" disabled type="text" class="form-control no_invoice">
                            <label for="floatingInput">Tipe Harga</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Commercial Invoice & Packing</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker departure_date" id="departure_date" name="departure_date" placeholder="Tanggal PI" value="<?= !empty($dataInvoice) ? date('d/m/Y', strtotime($dataInvoice['departure_date'])) : date('d/m/Y')  ?>">
                                    <label for="floatingInput">Departure Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['vessels_name'] : '' ?>" autocomplete="one-time-code" id="vessels_name" name="vessels_name" placeholder="Vessels Name" type="text" class="form-control">
                            <label for="floatingInput">Vessel's Name</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select valas_id" name="valas_id" id="valas_id">
                                <option value=""></option>
                                <?php foreach ($dataValuta as $d): ?>
                                    <option <?= empty($dataInvoice) ? ($dataSalesOrderExport->valas_id == $d['id'] ? 'selected' : '') : ($dataInvoice['valas_id'] == $d['id'] ? 'selected' : '') ?> value="<?= $d['id'] ?>"><?= $d['value'] . " - " . $d['description'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih Valas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term"><?= !empty($dataInvoice) ? $dataInvoice['payment_term'] : strip_tags($dataSalesOrderExport->payment_term) ?></textarea>
                            <label for="floatingInput">Payment Term</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control alamat" id="alamat" name="alamat" placeholder="Alamat Customer"><?= !empty($dataInvoice) ? $dataInvoice['alamat'] : $dataSalesOrderExport->address ?></textarea>
                            <label for="floatingInput">Alamat Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control notify_party" id="notify_party" name="notify_party" placeholder="Notify Party"><?= !empty($dataInvoice) ? $dataInvoice['notify_party'] : strip_tags($dataSalesOrderExport->notify_party) ?></textarea>
                            <label for="floatingInput">Notify Party</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control notify_party2" id="notify_party2" name="notify_party2" placeholder="Notify Party 2"><?= !empty($dataInvoice) ? $dataInvoice['notify_party2'] : '' ?></textarea>
                            <label for="floatingInput">Notify Party 2 (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control payment_description" id="payment_description" name="payment_description" placeholder="Payment Description (Opsional)"><?= !empty($dataInvoice) ? $dataInvoice['payment_description'] : "PLEASE FILL THE FOLLOWING CODES IN THE FIELD 70 ON SWIFT MT103" ?></textarea>
                            <label for="floatingInput">Payment Description (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['no_container'] : $dataSalesOrderExport->container ?>" autocomplete="one-time-code" type="text" class="form-control no_container" id="no_container" name="no_container" placeholder="No Container">
                            <label for="floatingInput">No Container</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['no_seal'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control no_seal" id="no_seal" name="no_seal" placeholder="No Seal">
                            <label for="floatingInput">No Seal</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['country_of_origin'] : "INDONESIA" ?>" autocomplete="one-time-code" placeholder="Country Of Origin" type="text" class="form-control country_of_origin" id="country_of_origin" name="country_of_origin">
                            <label for="floatingInput">Country Of Origin</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['penanda_tangan'] : '' ?>" autocomplete=" one-time-code" type="text" placeholder="Penanda Tangan" class="form-control penanda_tangan" id="penanda_tangan" name="penanda_tangan">
                            <label for="floatingInput">Penanda Tangan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['measurement'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control measurement" id="measurement" placeholder="Measurement (Opsional)" name="measurement">
                            <label for="floatingInput">Measurement (Opsional)</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control" id="total_nilai_invoice" name="total_nilai_invoice">
                            <label for="floatingInput">Nilai Invoice</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control" id="total_berat_bersih" name="total_berat_bersih">
                            <label for="floatingInput">Total Berat Bersih</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control" id="total_berat_kotor" name="total_berat_kotor">
                            <label for="floatingInput">Total Berat Kotor</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">A) List Commercial Invoice </label>
                    </div>
                </div>
                <div class="col-subtitle-modal">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold lable-title">- List Barang</label>
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
                                    <th>Catatan / Note</th>
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

                <div class="col-subtitle-modal mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">- List Biaya Tambahan</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnBiayaTambahan" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
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

                <div class="row mt-5">
                    <div class="col">
                        <label class="form-label font-weight-bold lable-title">B) List Packing </label>
                    </div>
                </div>

                <div class="col-subtitle-modal">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title"></label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnRefreshPacking" type="button" style="width: 90% !important;">
                                <i class="fa-solid fa-arrows-rotate mr-2"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="packingTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>HS Code</th>
                                    <th>Barang</th>
                                    <th>Catatan / Note</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-packing" id="body-packing">

                            </tbody>
                            <tfoot class="foot-packing" id="foot-packing">
                                <tr>
                                    <td colspan="5">List Packing Kosong</td>
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
                                <textarea autocomplete="one-time-code" <?= !empty($dataInvoice) ? ($dataInvoice['status_posting']  ? 'readonly=true' : '') : ''; ?> class="full-textarea form-control catatan" id="catatan" name="catatan" placeholder="Catatan"></textarea>
                                <label for="floatingInput">Catatan (Opsional)</label>
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
                                            <td colspan="3"></td>
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
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control grade" name="grade" id="grade" placeholder="Grade (Opsional)">
                                <label for="floatingInput">Grade</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control size" name="size" id="size" placeholder="Size (Opsional)">
                                <label for="floatingInput">Size (Opsional)</label>
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
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
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

<div class="modal detail-modal" id="packingModal" tabindex="1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-modal-packing"></label> Packing</h5>
            </div>
            <form id="form-packing" role="form" method="POST">
                <input type="hidden" name="id_packing" id="id_packing">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_barang_packing" id="nama_barang_packing" name="nama_barang_packing" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select hs_code" name="hs_code" id="hs_code">
                                    <option value=""></option>
                                    <?php foreach ($dataHsCode as $d) : ?>
                                        <option data-hs_code_name="<?= $d['code'] . " - " . $d['uraian_barang'] ?>" value="<?= $d['id'] ?>"><?= $d['code'] . " - " . $d['uraian_barang'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Kode HS</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea class="full-textarea form-control keterangan_packing" id="keterangan_packing" name="keterangan_packing" placeholder="Keterangan (Opsional)"></textarea>
                                <label for="floatingInput">Keterangan (Opsional)</label>
                            </div>
                        </div>

                        <!-- <div class="col-subtitle-modal">
                            <div class="row mt-3 justify-content-end">
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold modal-sub-title"></label>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success btn-block float-right" type="button" id="btnRefreshPackingBreakdown">
                                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i> Tambah Packing
                                    </button>
                                </div>
                            </div>
                        </div> -->

                    </div>

                    <div class="row">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="packingBreakdown" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Size</th>
                                            <th>Grade</th>
                                            <th>Can</th>
                                            <th>Case / Carton</th>
                                            <th>Kg</th>
                                            <th>LB</th>
                                            <th>Inner Box</th>
                                            <th>PC</th>
                                            <th>Bag</th>
                                            <th>%</th>
                                            <th>Cup</th>
                                            <th>Pallet</th>
                                            <th>Berat Bersih</th>
                                            <th>Berat Kotor</th>
                                            <th>VGM</th>
                                            <th>Drained</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-packing-breakdown">
                                        <!-- isi data -->
                                    </tbody>
                                    <tfoot class="tfoot-packing-breakdown">
                                        <tr>
                                            <td colspan="2"></td>
                                            <td><b>TOTAL</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
                                            <td><b>0.00</b></td>
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
                    <button type="button" class="btn btn-discard mr-2" id="btnHidePacking">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitPacking">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal add-modal" id="addPackingBreakdownModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title "><label class="title-breakdown-packing"></label> Detail Packing</h5>
            </div>
            <form class="create-form-breakdown-packing" role="form" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_detail_breakdown_packing" id="id_detail_breakdown_packing">
                    <div class="row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control grade_packing" readonly name="grade_packing" id="grade_packing" placeholder="Grade (Opsional)">
                                    <label for="floatingInput">Grade (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control size_packing" readonly name="size_packing" id="size_packing" placeholder="Size (Opsional)">
                                    <label for="floatingInput">Size (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control can_packing" name="can_packing" id="can_packing" placeholder="Can (Opsional)">
                                        <label for="floatingInput">Can (Opsional)</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-primary btn-add-barang" data-toggle="modal" type="button" id="formulaCanBtn">
                                            <i class="fa-solid fa-square-root-variable"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control case_packing" name="case_packing" id="case_packing" placeholder="Case (Opsional)">
                                        <label for="floatingInput">Case / Cartons (Opsional)</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-primary btn-add-barang" data-toggle="modal" type="button" id="formulaCaseBtn">
                                            <i class="fa-solid fa-square-root-variable"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control kg_packing" oninput="this.value = greatFormatRupiah(this.value)" name="kg_packing" id="kg_packing" placeholder="Kg (Opsional)">
                                    <label for="floatingInput">Kg (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control lb_packing" oninput="this.value = greatFormatRupiah(this.value)" name="lb_packing" id="lb_packing" placeholder="Lb (Opsional)">
                                    <label for="floatingInput">Lb (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control inner_box_packing" oninput="this.value = greatFormatRupiah(this.value)" name="inner_box_packing" id="inner_box_packing" placeholder="Inner Box (Opsional)">
                                    <label for="floatingInput">Inner Box (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control pc_packing" oninput="this.value = greatFormatRupiah(this.value)" name="pc_packing" id="pc_packing" placeholder="Pc (Opsional)">
                                    <label for="floatingInput">PC (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control bag_packing" oninput="this.value = greatFormatRupiah(this.value)" name="bag_packing" id="bag_packing" placeholder="Bag (Opsional)">
                                    <label for="floatingInput">Bag (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control persen_packing" name="persen_packing" id="persen_packing" placeholder="Persen (Opsional)" oninput="this.value = greatFormatRupiah(this.value)">
                                    <label for="floatingInput">Percentage % (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control cup_packing" oninput="this.value = greatFormatRupiah(this.value)" name="cup_packing" id="cup_packing" placeholder="Cup (Opsional)">
                                    <label for="floatingInput">Cup (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control palet_packing" oninput="this.value = greatFormatRupiah(this.value)" name="palet_packing" id="palet_packing" placeholder="Cup (Opsional)">
                                    <label for="floatingInput">Pallet (Opsional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control vgm" oninput="this.value = greatFormatRupiah(this.value)" name="vgm" id="vgm" placeholder="VGM (Opsional)">
                                        <label for="floatingInput">VGM (Opsional)</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-primary btn-add-barang" data-toggle="modal" type="button" id="formulaVgmBtn">
                                            <i class="fa-solid fa-square-root-variable"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control packing" name="packing" id="packing" oninput="this.value = greatFormatRupiah(this.value)" placeholder="Packing">
                                    <label for="floatingInput">Total Packing</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control qty_packing" name="qty_packing" id="qty_packing" oninput="this.value = greatFormatRupiah(this.value)" readonly placeholder="Qty Packing (Opsional)">
                                            <label for="floatingInput">Qty</label>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-floating mb-3" style="height: 50px;">
                                            <input autocomplete="one-time-code" type="text" class="form-control satuan_size_code" name="satuan_size_code" id="satuan_size_code" readonly placeholder="Satuan">
                                            <label for="floatingInput">Satuan</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control berat_bersih" oninput="this.value = greatFormatRupiah(this.value)" name="berat_bersih" id="berat_bersih" placeholder="Berat Bersih (Opsional)">
                                    <label for="floatingInput">Berat Bersih (Kg)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-password">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control berat_kotor" oninput="this.value = greatFormatRupiah(this.value)" name="berat_kotor" id="berat_kotor" placeholder="Berat Kotor (Opsional)">
                                        <label for="floatingInput">Berat Kotor (Kg)</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button class="btn btn-primary btn-add-barang" data-toggle="modal" type="button" id="formulaBeratKotorBtn">
                                            <i class="fa-solid fa-square-root-variable"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control drammed" oninput="this.value = greatFormatRupiah(this.value)" name="drammed" id="drammed" placeholder="Drammed (Opsional)">
                                    <label for="floatingInput">Drainned (Opsional)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHidePackingBreakdown">Kembali</button>
                    <button type="submit" class="btn btn-submit-form" id="btnSubmitPackingBreakdown">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="formulaCanModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Formula Hitung Qty Can</h5>
            </div>
            <form class="create-form-formula-can" role="form" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_qty_case_hitung_can" id="formula_qty_case_hitung_can" name="formula_qty_case_hitung_can" placeholder="Qty Case / Carton">
                                    <label for="floatingInput">Qty Case / Carton</label>
                                </div>
                                <div class="input-group-prepend">
                                    <select name="formula_operator_hitung_can" class="form-control" id="formula_operator_hitung_can" style="height: 50px;">
                                        <option value="tambah">+</option>
                                        <option value="kurang">-</option>
                                        <option value="bagi"> / </option>
                                        <option value="kali" selected>*</option>
                                    </select>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_bilangan_case_hitung_can" id="formula_bilangan_case_hitung_can" name="formula_bilangan_case_hitung_can" placeholder="Nilai Bilangan / Kemasan">
                                    <label for="floatingInput">Qty Per Case</label>
                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        =
                                    </span>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control hasil_qty_can_hitung_can" id="hasil_qty_can_hitung_can" name="hasil_qty_can_hitung_can" placeholder="Qty Can">
                                    <label for="floatingInput">Hasil Qty Can</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideFormulaCan">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitFormulaCan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="formulaCtModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Formula Hitung Qty Case / Carton</h5>
            </div>
            <form class="create-form-formula-case" role="form" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_qty_can_hitung_case" id="formula_qty_can_hitung_case" name="formula_qty_can_hitung_case" placeholder="Qty Can">
                                    <label for="floatingInput">Qty Can</label>
                                </div>
                                <div class="input-group-prepend">
                                    <select name="formula_operator_hitung_case" class="form-control" id="formula_operator_hitung_case" style="height: 50px;">
                                        <option value="tambah">+</option>
                                        <option value="kurang">-</option>
                                        <option value="bagi" selected> / </option>
                                        <option value="kali">*</option>
                                    </select>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_bilangan_can_hitung_case" id="formula_bilangan_can_hitung_case" name="formula_bilangan_can_hitung_case" placeholder="Nilai Bilangan / Kemasan">
                                    <label for="floatingInput">Qty / Can</label>
                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        =
                                    </span>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control hasil_qty_case_hitung_case" id="hasil_qty_case_hitung_case" name="hasil_qty_case_hitung_case" placeholder="Qty Case / Carton">
                                    <label for="floatingInput">Hasil Qty Case</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideFormulaCase">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitFormulaCase">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="formulaVgm" tabindex="1">
    <div class="modal-dialog" style="min-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Formula Hitung Qty VGM</h5>
            </div>
            <form class="create-form-formula-vgm" role="form" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_berat_kotor_hitung_vgm" id="formula_berat_kotor_hitung_vgm" name="formula_berat_kotor_hitung_vgm" placeholder="Berat Kotor">
                                    <label for="floatingInput">Qty Berat Kotor</label>
                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        +
                                    </span>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_bilangan_hitung_vgm" id="formula_bilangan_hitung_vgm" name="formula_bilangan_hitung_vgm" placeholder="Nilai Bilangan">
                                    <label for="floatingInput">Qty Empty Container</label>
                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        =
                                    </span>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control hasil_vgm" id="hasil_vgm" name="hasil_vgm" placeholder="Qty VGM">
                                    <label for="floatingInput">Qty Vgm</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideFormulaVgm">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitFormulaVGm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="formulaBeratKotorModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Formula Hitung Berat Kotor</h5>
            </div>
            <form class="create-form-formula-berat-kotor" role="form" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-prepend">
                                        <select name="formula_pilih_jumlah_case_can_hitung_berat_kotor" class="form-control" id="formula_pilih_jumlah_case_can_hitung_berat_kotor" style="height: 50px;">
                                            <option value="">Pilih</option>
                                            <option value="case">CASE</option>
                                            <option value="can">CAN</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_jumlah_case_can_hitung_berat_kotor" id="formula_jumlah_case_can_hitung_berat_kotor" name="formula_jumlah_case_can_hitung_berat_kotor" placeholder="Jumlah Case Can">
                                    <label for="floatingInput">Jumlah Case / Can</label>
                                </div>
                                <div class="input-group-prepend">
                                    <select name="formula_operator1_hitung_berat_kotor" class="form-control" id="formula_operator1_hitung_berat_kotor" style="height: 50px;">
                                        <option value="tambah">+</option>
                                        <option value="kurang">-</option>
                                        <option value="bagi"> / </option>
                                        <option value="kali" selected>*</option>
                                    </select>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_packing_hitung_berat_kotor" id="formula_packing_hitung_berat_kotor" name="formula_packing_hitung_berat_kotor" placeholder="Total Packing">
                                    <label for="floatingInput">Total Packing</label>
                                </div>
                                <div class="input-group-prepend">
                                    <select name="formula_operator2_hitung_berat_kotor" class="form-control" id="formula_operator2_hitung_berat_kotor" style="height: 50px;">
                                        <option value="tambah" selected>+</option>
                                        <option value="kurang">-</option>
                                        <option value="bagi"> / </option>
                                        <option value="kali">*</option>
                                    </select>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_berat_bersih_hitung_berat_kotor" id="formula_berat_bersih_hitung_berat_kotor" name="formula_berat_bersih_hitung_berat_kotor" placeholder="Berat Bersih">
                                    <label for="floatingInput">Berat Bersih</label>
                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        =
                                    </span>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control formula_hasil_berat_kotor" id="formula_hasil_berat_kotor" name="formula_hasil_berat_kotor" placeholder="Qty Berat Kotor">
                                    <label for="floatingInput">Berat Kotor</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideFormulaBeratKotor">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitFormulaBeratKotor">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listBarang = [];
    var listPacking = [];
    var listSizeBreakdown = [];
    var listPackingSizeBreakdown = [];
    var listBiayaTambahan = [];

    $(document).ready(function() {
        <?php if (!empty($dataInvoice)) { ?>
            listBarang = <?= json_encode($dataListBarang) ?>;
            listPacking = <?= json_encode($dataListPacking) ?>;
            listBiayaTambahan = <?= json_encode($dataListBiayaTambahan) ?>

            drawTableBarang(listBarang);
            drawTablePacking(listPacking);
            drawTableBiayaTambahan(listBiayaTambahan);
        <?php } else { ?>

        <?php } ?>


        $("#departure_date,#tanggal_invoice").datepicker({
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

        $('#satuan_size_id').select2({
            placeholder: "Pilih Satuan",
            theme: "bootstrap-5",
            dropdownParent: $('#addSizeBreakdownModal')
        })

        $('#hs_code').select2({
            placeholder: "Pilih HS Code",
            theme: "bootstrap-5",
            dropdownParent: $('#packingModal')
        })

        //CSS SELECT2 FLOATING LABEL
        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_size_id,.hs_code')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_size_id,.hs_code')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_size_id,.hs_code')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // QTY KEYUP
        $('.qty,.harga').keyup(function() {
            var qty = parseFloat($('.qty').val()) || 0;
            var harga = destroyFormatRupiah($('.harga').val()) || 0;
            var total = qty * harga;
            $('.total').val(greatFormatRupiah(total.toFixed(2)));
        });


        $('#btnAddSizeBreakdownModal').click(function() {
            clearFormSizeBreakdown();
            $('.title-size-breakdown').text("Tambah ");
            $('#addSizeBreakdownModal').modal('show');
        });

        $('#btnHideSizeBreakdownModal').click(function() {
            $('#addSizeBreakdownModal').modal('hide');

        });

        $('#btnBiayaTambahan').click(function() {
            resetFormBiayaTambahan();
            $('#label-detail-biaya-tambahan').text('Tambah ');
            $('#biayaTambahanModal').modal('show');
        });

        $('#formulaCanBtn').click(function(e) {
            e.preventDefault();
            resetFormFormulaCan();
            var casePackingVal = $('#case_packing').val();
            if (casePackingVal == "") {
                Swal.fire({
                    icon: 'error',
                    title: "Isikan terlebih dahulu Qty Carton",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                var casePacking = destroyFormatRupiah(casePackingVal);
                $('#formula_qty_case_hitung_can').val(greatFormatRupiah(casePacking));
                $('#formulaCanModal').modal('show');
            }
        });

        $('#formulaVgmBtn').click(function(e) {
            e.preventDefault();
            resetFormFormulaVgm();
            var beratKotor = $('#berat_kotor').val();
            if (beratKotor == "") {
                Swal.fire({
                    icon: 'error',
                    title: "Isikan terlebih dahulu berat kotor",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                var beratKotor = destroyFormatRupiah(beratKotor);
                $('#formula_berat_kotor_hitung_vgm').val(greatFormatRupiah(beratKotor));
                $('#formulaVgm').modal('show');
            }
        });

        $('#formulaCaseBtn').click(function(e) {
            e.preventDefault();
            resetFormFormulaCase();
            var canPacking = destroyFormatRupiah($('#can_packing').val());
            $('#formula_qty_can_hitung_case').val(greatFormatRupiah(canPacking));
            $('#formulaCtModal').modal('show');
        });

        $('#formulaBeratKotorBtn').click(function(e) {
            e.preventDefault();
            resetFormFormulaBeratKotor();
            //--------------------------------------------            $('#formula_jumlah_case_can_hitung_berat_kotor').val();
            var packing = $('#packing').val();
            var beratbersih = $('#berat_bersih').val();

            if (packing == '') {
                Swal.fire({
                    icon: 'error',
                    title: "Isikan packing terlebih dahulu",
                    confirmButtonColor: '#4e73df',
                });
                return;
            } else if (beratbersih == '') {
                Swal.fire({
                    icon: 'error',
                    title: "Isikan berat bersih terlebih dahulu",
                    confirmButtonColor: '#4e73df',
                });
                return;
            } else {
                $('#formula_packing_hitung_berat_kotor').val(greatFormatRupiah(packing));
                $('#formula_berat_bersih_hitung_berat_kotor').val(greatFormatRupiah(beratbersih));
                $('#formulaBeratKotorModal').modal('show');
            }

        });

        $('#btnHideFormulaVgm').click(function(e) {
            e.preventDefault();
            $('#formulaVgm').modal('hide');
        });

        $('#btnHideFormulaCan').click(function(e) {
            e.preventDefault();
            $('#formulaCanModal').modal('hide');
        });

        $('#btnHideFormulaCase').click(function(e) {
            e.preventDefault();
            $('#formulaCtModal').modal('hide');
        });


        $('#formula_bilangan_can_hitung_case').keyup(function(e) {
            e.preventDefault();
            hitungCase();
        });

        $('#formula_operator_hitung_case').change(function(e) {
            e.preventDefault();
            hitungCase();
        });

        function hitungCase() {
            var formulaQtyCan = destroyFormatRupiah($('#formula_qty_can_hitung_case').val());
            var formulaBilanganCan = destroyFormatRupiah($('#formula_bilangan_can_hitung_case').val());
            var formulaOperatorHitungCase = $('#formula_operator_hitung_case').val();
            var hasilQtyCase = 0;
            if (formulaOperatorHitungCase == "tambah") {
                hasilQtyCase = formulaQtyCan + formulaBilanganCan;
            } else if (formulaOperatorHitungCase == "kurang") {
                hasilQtyCase = formulaQtyCan - formulaBilanganCan;
            } else if (formulaOperatorHitungCase == "bagi") {
                hasilQtyCase = formulaQtyCan / formulaBilanganCan;
            } else if (formulaOperatorHitungCase == "kali") {
                hasilQtyCase = formulaQtyCan * formulaBilanganCan;
            }
            $('#hasil_qty_case_hitung_case').val(greatFormatRupiah(hasilQtyCase.toFixed(2)));
        }

        $('#formula_bilangan_case_hitung_can').keyup(function(e) {
            e.preventDefault();
            hitungCan();
        });

        $('#formula_operator_hitung_can').change(function(e) {
            e.preventDefault();
            hitungCan();
        });

        function hitungCan() {
            var formulaQtyCase = destroyFormatRupiah($('#formula_qty_case_hitung_can').val());
            var formulaBilanganCase = destroyFormatRupiah($('#formula_bilangan_case_hitung_can').val());
            var formulaOperatorHitungCan = $('#formula_operator_hitung_can').val();
            var hasilQtyCan = 0;
            if (formulaOperatorHitungCan == 'tambah') {
                hasilQtyCan = formulaQtyCase + formulaBilanganCase;
            } else if (formulaOperatorHitungCan == "kurang") {
                hasilQtyCan = formulaQtyCase - formulaBilanganCase;
            } else if (formulaOperatorHitungCan == "bagi") {
                hasilQtyCan = formulaQtyCase / formulaBilanganCase;
            } else if (formulaOperatorHitungCan == "kali") {
                hasilQtyCan = formulaQtyCase * formulaBilanganCase;
            }
            $('#hasil_qty_can_hitung_can').val(greatFormatRupiah(hasilQtyCan.toFixed(2)));
        }

        $('#formula_bilangan_hitung_vgm').keyup(function(e) {
            e.preventDefault();
            var formulaBilangan = destroyFormatRupiah($('#formula_bilangan_hitung_vgm').val());
            var beratKotor = destroyFormatRupiah($('#formula_berat_kotor_hitung_vgm').val());
            var hasilVgm = formulaBilangan + beratKotor;
            $('#hasil_vgm').val(greatFormatRupiah(hasilVgm));
        })

        // VALIDATOR PARENT BARANG  
        var validator = $("#form-parent").validate({
            rules: {
                tanggal_invoice: {
                    required: true
                },
                customer_name: {
                    required: true
                },
                loading_port: {
                    required: true
                },
                dicharge_port: {
                    required: true
                },
                departure_date: {
                    required: true
                },
                vessels_name: {
                    required: true
                },
                valas_id: {
                    required: true
                },
                payment_term: {
                    required: true
                },
                alamat: {
                    required: true
                },
                notify_party: {
                    required: true
                },
                no_container: {
                    required: true
                },
                no_seal: {
                    required: true
                },
                country_of_origin: {
                    required: true
                },
                penanda_tangan: {
                    required: true
                },
            },
            messages: {
                tanggal_invoice: {
                    required: "Tanggal invoice wajib diisi"
                },
                customer_name: {
                    required: "Customer wajib diisi"
                },
                loading_port: {
                    required: "Loading port wajib diisi"
                },
                dicharge_port: {
                    required: "Dicharge port wajib diisi"
                },
                departure_date: {
                    required: "Departure date wajib diisi"
                },
                vessels_name: {
                    required: "Vessels name wajib diisi"
                },
                valas_id: {
                    required: "Valas wajib diisi"
                },
                payment_term: {
                    required: "Payment term wajib diisi"
                },
                alamat: {
                    required: "Alamat wajib diisi"
                },
                notify_party: {
                    required: "Notify party wajib diisi"
                },
                no_container: {
                    required: "No container wajib diisi"
                },
                no_seal: {
                    required: "No seal wajib diisi"
                },
                country_of_origin: {
                    required: "Country of origin wajib diisi"
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

        // VALIDATOR BARANG
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

        // VALIDATOR SIZE & BREAKDOWN
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

        // VALIDATOR PACKING BREAKDOWN
        var validatorPackingBreakdown = $(".create-form-breakdown-packing").validate({
            rules: {
                packing: {
                    required: true
                },
                berat_bersih: {
                    required: true
                },
                berat_kotor: {
                    required: true
                },
                qty_carton: {
                    required: true
                },
            },
            messages: {
                packing: {
                    required: "Total Packing wajib diisi"
                },
                berat_bersih: {
                    required: "Berat bersih wajib diisi"
                },
                berat_kotor: {
                    required: "Berat kotor wajib diisi"
                },
                qty_carton: {
                    required: "Qrt carton wajib diisi"
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

        // VALIDATOR PACKING 
        var validatorPacking = $(".form-packing").validate({
            rules: {
                nama_barang_packing: {
                    required: true
                },
                hs_code: {
                    required: true
                },
            },
            messages: {
                nama_barang_packing: {
                    required: "NAma barang wajib diisi"
                },
                hs_code: {
                    required: "HS code wajib diisi"
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

        // VALIDATOR FORMULA CAN
        var validatorCan = $(".create-form-formula-can").validate({
            rules: {
                formula_qty_case_hitung_can: {
                    required: true
                },
                formula_bilangan_case_hitung_can: {
                    required: true
                },
                hasil_qty_can_hitung_can: {
                    required: true
                },
            },
            messages: {
                formula_qty_case_hitung_can: {
                    required: "Qty Case Wajib Diisi"
                },
                formula_bilangan_case_hitung_can: {
                    required: "Qty Per Case Wajib Diisi"
                },
                hasil_qty_can_hitung_can: {
                    required: "Hasil Formula Wajib Diisi"
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

        // FORMULA CASE
        var validatorCase = $(".create-form-formula-case").validate({
            rules: {
                formula_qty_can_hitung_case: {
                    required: true
                },
                formula_bilangan_can_hitung_case: {
                    required: true
                },
                hasil_qty_case_hitung_case: {
                    required: true
                },
            },
            messages: {
                formula_qty_can_hitung_case: {
                    required: "Qty Can Wajib DIisi"
                },
                formula_bilangan_can_hitung_case: {
                    required: "Qty Per Can Wajib Diisi"
                },
                hasil_qty_case_hitung_case: {
                    required: "Hasil Formula Case Wajib Diisi"
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

        // FORMULA VGM
        var validatorVgm = $(".create-form-formula-vgm").validate({
            rules: {
                formula_berat_kotor_hitung_vgm: {
                    required: true
                },
                formula_bilangan_hitung_vgm: {
                    required: true
                },
                hasil_vgm: {
                    required: true
                },
            },
            messages: {
                formula_berat_kotor_hitung_vgm: {
                    required: "Berat kotor wajib diisi"
                },
                formula_bilangan_hitung_vgm: {
                    required: "Nilai bilangan wajib diisi"
                },
                hasil_vgm: {
                    required: "Hasil VGM wajib diisi"
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

        // FORMULA BERAT KOTOR
        var validatorBeratKotor = $(".create-form-formula-berat-kotor").validate({
            rules: {
                formula_hasil_berat_kotor: {
                    required: true
                },
            },
            messages: {
                formula_hasil_berat_kotor: {
                    required: "Hasil berat kotor"
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

        $('#btnSubmitFormulaBeratKotor').click(function(e) {
            e.preventDefault();
            if ($('.create-form-formula-berat-kotor').valid()) {
                var hasilBeratKotor = destroyFormatRupiah($('#formula_hasil_berat_kotor').val());
                $('#berat_kotor').val(greatFormatRupiah(hasilBeratKotor));
                $('#formulaBeratKotorModal').modal('hide');
            }
        });

        $('#btnSubmitFormulaVGm').click(function(e) {
            e.preventDefault();
            if ($('.create-form-formula-vgm').valid()) {
                var hasilVgm = destroyFormatRupiah($('#hasil_vgm').val());
                $('#vgm').val(greatFormatRupiah(hasilVgm));
                $('#formulaVgm').modal('hide');
            }
        });


        $('#btnSubmitFormulaCan').click(function(e) {
            e.preventDefault();
            if ($('.create-form-formula-can').valid()) {
                var formulaQtyCan = destroyFormatRupiah($('#hasil_qty_can_hitung_can').val());

                $('#can_packing').val(greatFormatRupiah(formulaQtyCan));
                $('#formulaCanModal').modal('hide');
            }
        });

        $('#btnSubmitFormulaCase').click(function(e) {
            e.preventDefault();
            if ($('.create-form-formula-case').valid()) {
                var formulaQtyCase = destroyFormatRupiah($('#hasil_qty_case_hitung_case').val());

                $('#case_packing').val(greatFormatRupiah(formulaQtyCase));
                $('#formulaCtModal').modal('hide');
            }
        })

        $('#btnSubmitSizeBreakDown').click(function(e) {
            e.preventDefault();
            if ($('.create-form-size-breakdown').valid()) {
                var id_detail_breakdown = $('#id_detail_breakdown').val();
                var size = $('#size').val();
                var grade = $('#grade').val();
                var qty = $('#qty').val();
                var harga = destroyFormatRupiah($('#harga').val());
                var total = destroyFormatRupiah($('#total').val());
                var satuan_size_id = $('#satuan_size_id option:selected').val();
                var satuan_size_code = $('#satuan_size_id option:selected').text();

                var result = {
                    id_detail_breakdown: id_detail_breakdown,
                    size: size,
                    grade: grade,
                    qty: qty,
                    harga: harga,
                    total: harga * qty,
                    satuan_size_id: satuan_size_id,
                    satuan_size_code: satuan_size_code
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

        $('#btnSubmitPackingBreakdown').click(function(e) {
            e.preventDefault();
            if ($('.create-form-breakdown-packing').valid()) {
                var idDetailBreakdownPacking = $('#id_detail_breakdown_packing').val();
                //------------------------
                var canPacking = destroyFormatRupiah($('#can_packing').val());
                var casePacking = destroyFormatRupiah($('#case_packing').val());
                var kgPacking = destroyFormatRupiah($('#kg_packing').val());
                var lbPacking = destroyFormatRupiah($('#lb_packing').val());
                var innerBoxPacking = destroyFormatRupiah($('#inner_box_packing').val());
                var pcPacking = destroyFormatRupiah($('#pc_packing').val());
                var bagPacking = destroyFormatRupiah($('#bag_packing').val());
                var persenPacking = destroyFormatRupiah($('#persen_packing').val());
                var cupPacking = destroyFormatRupiah($('#cup_packing').val());
                var paletPacking = destroyFormatRupiah($('#palet_packing').val());
                //---------------------------------
                var packing = destroyFormatRupiah($('#packing').val());
                var beratBersih = destroyFormatRupiah($('#berat_bersih').val());
                var beratKotor = destroyFormatRupiah($('#berat_kotor').val());
                var vgm = destroyFormatRupiah($('#vgm').val());
                var drammed = destroyFormatRupiah($('#drammed').val());

                var result = {
                    id_detail_breakdown_packing: idDetailBreakdownPacking,
                    can: canPacking,
                    case: casePacking,
                    kg: kgPacking,
                    lb: lbPacking,
                    inner_box: innerBoxPacking,
                    pc: pcPacking,
                    bag: bagPacking,
                    persen: persenPacking,
                    cup: cupPacking,
                    palet: paletPacking,
                    packing: packing,
                    berat_bersih: beratBersih,
                    berat_kotor: beratKotor,
                    vgm: vgm,
                    drammed: drammed
                };

                // Update
                var index = null;
                for (var i = 0; i < listPackingSizeBreakdown.length; i++) {
                    if (listPackingSizeBreakdown[i].id_detail_breakdown_packing == idDetailBreakdownPacking) {
                        index = i;
                        break;
                    }
                }

                listPackingSizeBreakdown[index].can = result.can;
                listPackingSizeBreakdown[index].case = result.case;
                listPackingSizeBreakdown[index].kg = result.kg;
                listPackingSizeBreakdown[index].lb = result.lb;
                listPackingSizeBreakdown[index].inner_box = result.inner_box;
                listPackingSizeBreakdown[index].pc = result.pc;
                listPackingSizeBreakdown[index].bag = result.bag;
                listPackingSizeBreakdown[index].persen = result.persen;
                listPackingSizeBreakdown[index].cup = result.cup;
                listPackingSizeBreakdown[index].palet = result.palet;
                listPackingSizeBreakdown[index].packing = result.packing;
                listPackingSizeBreakdown[index].berat_bersih = result.berat_bersih;
                listPackingSizeBreakdown[index].berat_kotor = result.berat_kotor;
                listPackingSizeBreakdown[index].vgm = result.vgm;
                listPackingSizeBreakdown[index].drammed = result.drammed;

                drawTableListPackingSizeBreakDown(listPackingSizeBreakdown);
                $('#addPackingBreakdownModal').modal('hide');

            }
        })

        $('#btnRefreshPacking').click(function(e) {
            e.preventDefault();
            syncBarangPacking();
        });

        $('#btnRefreshPackingBreakdown').click(function(e) {
            e.preventDefault();
            $('#addPackingBreakdownModal').modal("show");
            $('.title-breakdown-packing').text('Tambah ');
            resetFormBreakdownPacking();
        })

        $('#btnAddBarang').click(function(e) {
            e.preventDefault();
            $('#label-modal-barang').text("Tambah ");
            $('#barangModal').modal('show');
            listSizeBreakdown = [];
            drawTableListSizeBreakDown(listSizeBreakdown);
            resetFormBarang();
        });

        $('#btnHideBarang').click(function(e) {
            e.preventDefault();
            $('#barangModal').modal('hide');
        });

        $('#btnHidePacking').click(function(e) {
            e.preventDefault();
            $('#packingModal').modal('hide');
        });

        $('#btnHidePackingBreakdown').click(function(e) {
            e.preventDefault();
            $('#addPackingBreakdownModal').modal('hide');
        });

        $('#btnHideFormulaBeratKotor').click(function(e) {
            e.preventDefault();
            $('#formulaBeratKotorModal').modal('hide');
        });

        $('#qty_barang,#harga_satuan').keyup(function(e) {
            e.preventDefault();
            var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
            var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
            var totalHarga = qtyBarang * hargaSatuan;

            $('#total_harga').val(greatFormatRupiah(totalHarga.toFixed(2)));
        });

        $('#formula_pilih_jumlah_case_can_hitung_berat_kotor,#formula_operator1_hitung_berat_kotor,#formula_operator2_hitung_berat_kotor').change(function(e) {
            e.preventDefault();
            var pilihJumlahCanCase = $('#formula_pilih_jumlah_case_can_hitung_berat_kotor').val();
            var caseCan = 0;
            if (pilihJumlahCanCase == "can") {
                caseCan = destroyFormatRupiah($('#can_packing').val());
            } else {
                caseCan = destroyFormatRupiah($('#case_packing').val());
            }

            $('#formula_jumlah_case_can_hitung_berat_kotor').val(greatFormatRupiah(caseCan));

            var packing = destroyFormatRupiah($('#formula_packing_hitung_berat_kotor').val());
            var beratBersih = destroyFormatRupiah($('#formula_berat_bersih_hitung_berat_kotor').val());

            var operator1 = $('#formula_operator1_hitung_berat_kotor').val();
            var operator2 = $('#formula_operator2_hitung_berat_kotor').val();

            var casCanPackingHasil = 0;
            if (operator1 == 'tambah') {
                casCanPackingHasil = caseCan + packing;
            } else if (operator1 == 'kurang') {
                casCanPackingHasil = caseCan - packing;
            } else if (operator1 == 'bagi') {
                casCanPackingHasil = caseCan / packing;
            } else if (operator1 == 'kali') {
                casCanPackingHasil = caseCan * packing;
            }

            var beratKotor = 0;
            if (operator2 == 'tambah') {
                beratKotor = casCanPackingHasil + beratBersih;
            } else if (operator2 == 'kurang') {
                beratKotor = casCanPackingHasil - beratBersih
            }

            $('#formula_hasil_berat_kotor').val(greatFormatRupiah(beratKotor));
        });



        // $('#packing,#qty_packing,#berat_bersih').keyup(function(e) {
        //     e.preventDefault();
        //     var packing = destroyFormatRupiah($('#packing').val());
        //     var qtyPacking = destroyFormatRupiah($('#qty_packing').val());
        //     var beratBersih = destroyFormatRupiah($('#berat_bersih').val());
        //     var beratKotor = (packing * qtyPacking) + beratBersih;
        //     $('#berat_kotor').val(greatFormatRupiah(beratKotor));
        // })

        $('#btnHideBiayaTambahan').click(function() {
            $('#biayaTambahanModal').modal('hide');
        });

        $('#btnSubmitBarang').click(function(e) {
            e.preventDefault();
            if (listSizeBreakdown.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Isikan terlebih dahulu detail grade barang",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                if ($('#form-barang').valid()) {
                    var idBarang = $('#id_barang').val();
                    var namaBarang = $('#nama_barang').val();
                    var catatan = $('#catatan').val();

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
                        listBarang[index].catatan = catatan;
                        listBarang[index].size_breakdown = listSizeBreakdown;

                    } else {
                        // CREATE
                        listBarang.push({
                            id_barang: getID(),
                            nama_barang: namaBarang,
                            catatan: catatan,
                            size_breakdown: listSizeBreakdown,
                        });
                    }

                    $('#barangModal').modal('hide');
                    listSizeBreakdown = [];
                    syncBarangPacking();
                    drawTableBarang(listBarang);
                }
            }
        });

        $('#btnSubmitPacking').click(function(e) {
            e.preventDefault();
            if (listPackingSizeBreakdown.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Isikan terlebih dahulu detail packing barang",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                if ($('#form-packing').valid()) {
                    var idPacking = $('#id_packing').val();
                    var namaBarangPacking = $('#nama_barang_packing').val();
                    var hsCode = $('#hs_code option:selected').val();
                    var hasCodeName = $('#hs_code option:selected').data('hs_code_name');
                    var keteranganPacking = $('#keterangan_packing').val();

                    if (idPacking) {
                        // UPDATE
                        var index = null;
                        for (var i = 0; i < listPacking.length; i++) {
                            if (listPacking[i].id_packing == idPacking) {
                                index = i;
                                break;
                            }
                        }

                        listPacking[index].nama_barang_packing = namaBarangPacking;
                        listPacking[index].hs_code = hsCode;
                        listPacking[index].hs_code_name = hasCodeName;
                        listPacking[index].keterangan_packing = keteranganPacking;
                        listPacking[index].size_breakdown = listPackingSizeBreakdown;

                    } else {
                        // CREATE
                        listPacking.push({
                            id_packing: getID(),
                            nama_barang_packing: namaBarangPacking,
                            hs_code: hsCode,
                            hs_code_name: hasCodeName,
                            keterangan_packing: keteranganPacking,
                            size_breakdown: listPackingSizeBreakdown
                        });
                    }

                    listPackingSizeBreakdown = [];
                    $('#packingModal').modal('hide');
                    drawTablePacking(listPacking);
                }
            }
        });
        $('#qty_barang,#harga_satuan').keyup(function(e) {
            e.preventDefault();
            var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
            var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
            var totalHarga = parseFloat(qtyBarang) * parseFloat(hargaSatuan);
            $('#total_harga').val(greatFormatRupiah(totalHarga));
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


        $('#presentase').keyup(function(e) {
            e.preventDefault();
            var presentase = destroyFormatRupiah($('#presentase').val());
            var totalNilaiBarang = 0;
            $.each(listBarang, function(i, v) {
                totalNilaiBarang += v.total_harga;
            });

            var presentaseDecimal = presentase / 100;
            var nilaiPaymentTerm = presentaseDecimal * totalNilaiBarang;
            $('#nilai_payment_term').val(greatFormatRupiah(nilaiPaymentTerm.toFixed()));
        });

        $(".btn-submit-parent").click(function() {
            if (listBarang.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan list barang !',
                    confirmButtonColor: '#4e73df',
                })
            } else if (listPacking.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan packing list !',
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
                            let url = "<?= base_url('invoice-packing-customer/create') ?>";
                            let data = new FormData(document.querySelector("#form-parent"));
                            let totalNilaiInvoice = destroyFormatRupiah($('#total_nilai_invoice').val());
                            let totalBeratBersih = destroyFormatRupiah($('#total_berat_bersih').val());
                            let totalBeratKotor = destroyFormatRupiah($('#total_berat_kotor').val());

                            data.set("total_nilai_invoice", totalNilaiInvoice);
                            data.set("total_berat_bersih", totalBeratBersih);
                            data.set("total_berat_kotor", totalBeratKotor);

                            data.append("listBarang", JSON.stringify(listBarang));
                            data.append("listBiayaTambahan", JSON.stringify(listBiayaTambahan));
                            data.append("listPacking", JSON.stringify(listPacking));

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
                                                window.location.href = "<?= base_url("invoice-packing-customer/detail/" . encrypt($dataSalesOrderExport->sales_order_export_id)) ?>";
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

    function drawTableListSizeBreakDown(listSizeBreakdown) {
        $('.body-barang-size-breakdown').empty();
        $('.tfoot-detail-table-size-breakdown').empty();
        var row = '';
        var no = 1;
        const table = $('#productSizeBreakdown');
        if (listSizeBreakdown.length === 0) {
            row += `
                    <tr>
                        <td colspan="3"></td>
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
                newRow.append($('<td>').text(item.satuan_size_code));
                newRow.append($('<td>').text(greatFormatRupiah(item.qty)));
                newRow.append($('<td>').text(greatFormatRupiah(item.harga)));
                newRow.append($('<td>').text(greatFormatRupiah(item.total)));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataInvoice)) : ?> <?php if ($dataInvoice['status_posting']) : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowSizeBreakdown('${item.id_detail_breakdown}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowSizeBreakdown('${item.id_detail_breakdown}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowSizeBreakdown('${item.id_detail_breakdown}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowSizeBreakdown('${item.id_detail_breakdown}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                totalQty += parseFloat(item.qty);
                totalHarga += destroyFormatRupiah(item.harga);
                totalTotalHarga += destroyFormatRupiah(item.total);

                table.find('tbody').append(newRow);
            });
            $('#body-barang-size-breakdown').append(row);
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td colspan="3"></td>'));
            newRow.append($('<td><b>TOTAL</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalQty) + '</b></td>'));
            newRow.append($('<td><b></b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalTotalHarga.toFixed(2)) + '</b></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);
        }
    }

    function drawTableListPackingSizeBreakDown(listPackingSizeBreakdown) {
        $('.body-packing-breakdown').empty();
        $('.tfoot-packing-breakdown').empty();
        var row = '';
        var no = 1;
        const table = $('#packingBreakdown');
        if (listPackingSizeBreakdown.length === 0) {
            row += `
                    <tr>
                        <td colspan="2></td>
                        <td><b>TOTAL</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
                        <td></td>
                    </tr>
                `;
            $('.tfoot-packing-breakdown').append(row);
        } else {
            var totalCan = 0;
            var totalCased = 0;
            var totalKg = 0;
            var totalLb = 0;
            var totalInnerBox = 0;
            var totalPc = 0;
            var totalBag = 0;
            var totalPersen = 0;
            var totalCup = 0;
            var totalPalet = 0;
            var totalBeratBersih = 0;
            var totalBeratKotor = 0;
            var totalVgm = 0;
            var totalDrammed = 0;

            listPackingSizeBreakdown.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.size));
                newRow.append($('<td>').text(item.grade));
                newRow.append($('<td>').text(greatFormatRupiah(item.can)));
                newRow.append($('<td>').text(greatFormatRupiah(item.case)));
                newRow.append($('<td>').text(greatFormatRupiah(item.kg)));
                newRow.append($('<td>').text(greatFormatRupiah(item.lb)));
                newRow.append($('<td>').text(greatFormatRupiah(item.inner_box)));
                newRow.append($('<td>').text(greatFormatRupiah(item.pc)));
                newRow.append($('<td>').text(greatFormatRupiah(item.bag)));
                newRow.append($('<td>').text(greatFormatRupiah(item.persen)));
                newRow.append($('<td>').text(greatFormatRupiah(item.cup)));
                newRow.append($('<td>').text(greatFormatRupiah(item.palet)));
                newRow.append($('<td>').text(greatFormatRupiah(item.berat_bersih)));
                newRow.append($('<td>').text(greatFormatRupiah(item.berat_kotor)));
                newRow.append($('<td>').text(greatFormatRupiah(item.vgm)));
                newRow.append($('<td>').text(greatFormatRupiah(item.drammed)));

                newRow.append($('<td>').html(
                    <?php if (!empty($dataInvoice)) : ?> <?php if ($dataInvoice['status_posting']) : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPackingBreakdown('${item.id_detail_breakdown_packing}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowPackingBreakdown('${item.id_detail_breakdown_packing}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPackingBreakdown('${item.id_detail_breakdown_packing}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowPackingBreakdown('${item.id_detail_breakdown_packing}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                totalCan += parseFloat(item.can);
                totalCased += destroyFormatRupiah(item.case);
                totalKg += destroyFormatRupiah(item.kg);
                totalLb += destroyFormatRupiah(item.lb);
                totalInnerBox += destroyFormatRupiah(item.inner_box);
                totalPc += destroyFormatRupiah(item.pc);
                totalBag += destroyFormatRupiah(item.bag);
                totalPersen += destroyFormatRupiah(item.persen);
                totalCup += destroyFormatRupiah(item.cup);
                totalPalet += destroyFormatRupiah(item.palet);
                totalBeratBersih += destroyFormatRupiah(item.berat_bersih);
                totalBeratKotor += destroyFormatRupiah(item.berat_kotor);
                totalVgm += destroyFormatRupiah(item.vgm);
                totalDrammed += destroyFormatRupiah(item.drammed);

                table.find('tbody').append(newRow);
            });
            $('#body-packing-breakdown').append(row);
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td colspan="2"></td>'));
            newRow.append($('<td><b>TOTAL</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalCan.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalCased.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalKg.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalLb.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalInnerBox.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalPc.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalBag.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalPersen.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalCup.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalPalet.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalBeratBersih.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalBeratKotor.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalVgm.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalDrammed.toFixed(2)) + '</b></td>'));
            newRow.append($('<td></td>'));
            table.find('tfoot').append(newRow);
        }
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
        $('#can').val(item.can);
        $('#case').val(greatFormatRupiah(item.case));
        $('#kg').val(item.kg);
        $('#lb').val(item.lb);
        $('#inner_box').val(item.inner_box);
        $('#pc').val(item.pc);
        $('#bag').val(item.bag);
        $('#persen').val(item.persen);
        $('#qty').val(item.qty);
        $('#harga').val(greatFormatRupiah(item.harga));
        $('#total').val(greatFormatRupiah(item.total));
        $('#satuan_size_id').val(item.satuan_size_id).change();

        $('.title-size-breakdown').text("Update ");
        $('#addSizeBreakdownModal').modal('show');
    }

    function detailRowPackingBreakdown(id) {
        var item = null;
        for (var i = 0; i < listPackingSizeBreakdown.length; i++) {
            if (listPackingSizeBreakdown[i].id_detail_breakdown_packing == id) {
                item = listPackingSizeBreakdown[i];
                break;
            }
        }
        console.log(item);
        // -----------------------------
        $('#grade_packing').val(item.grade);
        $('#size_packing').val(item.size);
        $('#can_packing').val(greatFormatRupiah(item.can));
        $('#case_packing').val(greatFormatRupiah(item.case));
        $('#kg_packing').val(greatFormatRupiah(item.kg));
        $('#lb_packing').val(greatFormatRupiah(item.lb));
        $('#inner_box_packing').val(greatFormatRupiah(item.inner_box));
        $('#pc_packing').val(greatFormatRupiah(item.pc));
        $('#bag_packing').val(greatFormatRupiah(item.bag));
        $('#persen_packing').val(greatFormatRupiah(item.persen));
        $('#cup_packing').val(greatFormatRupiah(item.cup));
        $('#palet_packing').val(greatFormatRupiah(item.palet));
        // ------------------------------
        $('.title-breakdown-packing').text('Update ');
        $('#id_detail_breakdown_packing').val(item.id_detail_breakdown_packing);
        $('#qty_packing').val(greatFormatRupiah(item.qty));
        $('#satuan_size_code').val(item.satuan_size_code);
        // YANG DI INSERT
        $('#packing').val(greatFormatRupiah(item.packing));
        $('#berat_bersih').val(greatFormatRupiah(item.berat_bersih));
        $('#berat_kotor').val(greatFormatRupiah(item.berat_kotor));
        $('#vgm').val(greatFormatRupiah(item.vgm));
        $('#drammed').val(greatFormatRupiah(item.drammed));

        $('#addPackingBreakdownModal').modal('show');
    }

    function deleteRowPackingBreakdown(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listPackingSizeBreakdown.length; i++) {
            if (listPackingSizeBreakdown[i].id_detail_breakdown_packing == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listPackingSizeBreakdown.splice(indexToRemove, 1);
        }
        drawTableListPackingSizeBreakDown(listPackingSizeBreakdown);
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
        $('#packing').val(null);
        $('#catatan').val(null);
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
                    <?php if (!empty($dataInvoice)) : ?> <?php if ($dataInvoice['status_posting'] == 1) : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBiayaTambahan('${item.id_biaya_tambahan}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowBiayaTambahan('${item.id_biaya_tambahan}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBiayaTambahan('${item.id_biaya_tambahan}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowBiayaTambahan('${item.id_biaya_tambahan}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                table.find('tbody').append(newRow);
            });
        }

        recalculateTotal();
    }

    function drawTablePacking(listPacking) {
        const table = $('#packingTable');
        const tbody = table.find('#body-packing'); // tbody utama
        const tfoot = table.find('tfoot');

        tbody.empty();
        tfoot.empty();

        let no = 1;

        if (listPacking.length === 0) {
            const row = `
            <tr>
                <td colspan="5">List Packing Kosong</td>
            </tr>`;
            tfoot.append(row);
            return;
        }

        listPacking.forEach(item => {
            // === Row Utama Barang ===
            const newRow = $('<tr style="color:whitesmoke;">');
            newRow.append(`<td style="text-align:center;">${no++}</td>`);
            newRow.append(`<td>${item.hs_code_name}</td>`);
            newRow.append(`<td>${item.nama_barang_packing}</td>`);
            newRow.append(`<td>${item.keterangan_packing}</td>`);
            const actionButton = `
            <?php if (!empty($dataInvoice)) : ?>
                <?php if ($dataInvoice['status_posting']) : ?>
                    -
                <?php else : ?>
                    <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPacking('${item.id_packing}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteRowPacking('${item.id_packing}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPacking('${item.id_packing}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteRowPacking('${item.id_packing}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>
            <?php endif; ?>
        `;

            newRow.append(`<td>${actionButton}</td>`);
            tbody.append(newRow);

            // === Row Kedua: Breakdown Table ===
            const detailRow = $('<tr  style="color:whitesmoke;">');
            const innerTable = $(`
            <table class="table table-sm table-bordered mb-2 w-100">
                <thead class="bg-warning text-dark">
                    <tr>
                        <th>Size</th>
                        <th>Grade</th>
                        <th>Can</th>
                        <th>Case / Carton</th>
                        <th>Kg</th>
                        <th>LB</th>
                        <th>Inner Box</th>
                        <th>PC</th>
                        <th>Bag</th>
                        <th>%</th>
                        <th>Cup</th>
                        <th>Pallet</th>
                        <th>Berat Bersih</th>
                        <th>Berat Kotor</th>
                        <th>VGM</th>
                        <th>Drained</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end"><b>TOTAL</b></td>
                        <td class="total-can"><b>0.00</b></td>
                        <td class="total-case"><b>0.00</b></td>
                        <td class="total-kg"><b>0.00</b></td>
                        <td class="total-lb"><b>0.00</b></td>
                        <td class="total-inner-box"><b>0.00</b></td>
                        <td class="total-pc"><b>0.00</b></td>
                        <td class="total-bag"><b>0.00</b></td>
                        <td class="total-persen"><b>0.00</b></td>
                        <td class="total-cup"><b>0.00</b></td>
                        <td class="total-palet"><b>0.00</b></td>
                        <td class="total-berat-bersih"><b>0.00</b></td>
                        <td class="total-berat-kotor"><b>0.00</b></td>
                        <td class="total-vgm"><b>0.00</b></td>
                        <td class="total-drammed"><b>0.00</b></td>
                    </tr>
                </tfoot>
            </table>
        `);

            const breakdownBody = innerTable.find('tbody');
            var totalCan = 0;
            var totalCase = 0;
            var totalKg = 0;
            var totalLb = 0;
            var totalInnerBox = 0;
            var totalPc = 0;
            var totalBag = 0;
            var totalPersen = 0;
            var totalCup = 0;
            var totalPalet = 0;
            var totalBeratBersih = 0;
            var totalBeratKotor = 0;
            var totalVgm = 0;
            var totalDrammed = 0;

            item.size_breakdown.forEach(size => {
                let can = parseFloat(size.can) || 0;
                let cased = parseFloat(size.case) || 0;
                let kg = parseFloat(size.kg) || 0;
                let lb = parseFloat(size.lb) || 0;
                let innerBox = parseFloat(size.inner_box) || 0;
                let pc = parseFloat(size.pc) || 0;
                let bag = parseFloat(size.bag) || 0;
                let persen = parseFloat(size.persen) || 0;
                let cup = parseFloat(size.cup) || 0;
                let palet = parseFloat(size.palet) || 0;
                let beratBersih = parseFloat(size.berat_bersih) || 0;
                let beratKotor = parseFloat(size.berat_kotor) || 0;
                let vgm = parseFloat(size.vgm) || 0;
                let drammed = parseFloat(size.drammed) || 0;

                totalCan += can;
                totalCase += cased;
                totalKg += kg;
                totalLb += lb;
                totalInnerBox += innerBox;
                totalPc += pc;
                totalBag += bag;
                totalPersen += persen;
                totalCup += cup;
                totalPalet += palet;
                totalBeratBersih += beratBersih;
                totalBeratKotor += beratKotor;
                totalVgm += vgm;
                totalDrammed += drammed;

                const row = `
                <tr>
                    <td>${size.size || ''}</td>
                    <td>${size.grade || ''}</td>
                    <td>${greatFormatRupiah(can.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(cased.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(kg.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(lb.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(innerBox.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(pc.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(bag.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(persen.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(cup.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(palet.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(beratBersih.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(beratKotor.toFixed(2)) || ''}</td>
                    <td>${greatFormatRupiah(vgm.toFixed(2))}</td>
                    <td>${greatFormatRupiah(drammed.toFixed(2))}</td>
                </tr>
            `;
                breakdownBody.append(row);
            });

            innerTable.find('.total-can').html(`<b>${greatFormatRupiah(totalCan.toFixed(2))}</b>`);
            innerTable.find('.total-case').html(`<b>${greatFormatRupiah(totalCase.toFixed(2))}</b>`);
            innerTable.find('.total-kg').html(`<b>${greatFormatRupiah(totalKg.toFixed(2))}</b>`);
            innerTable.find('.total-lb').html(`<b>${greatFormatRupiah(totalLb.toFixed(2))}</b>`);
            innerTable.find('.total-inner-box').html(`<b>${greatFormatRupiah(totalInnerBox.toFixed(2))}</b>`);
            innerTable.find('.total-pc').html(`<b>${greatFormatRupiah(totalPc.toFixed(2))}</b>`);
            innerTable.find('.total-bag').html(`<b>${greatFormatRupiah(totalBag.toFixed(2))}</b>`);
            innerTable.find('.total-persen').html(`<b>${greatFormatRupiah(totalPersen.toFixed(2))}</b>`);
            innerTable.find('.total-cup').html(`<b>${greatFormatRupiah(totalCup.toFixed(2))}</b>`);
            innerTable.find('.total-palet').html(`<b>${greatFormatRupiah(totalPalet.toFixed(2))}</b>`);
            innerTable.find('.total-berat-bersih').html(`<b>${greatFormatRupiah(totalBeratBersih.toFixed(2))}</b>`);
            innerTable.find('.total-berat-kotor').html(`<b>${greatFormatRupiah(totalBeratKotor.toFixed(2))}</b>`);
            innerTable.find('.total-vgm').html(`<b>${greatFormatRupiah(totalVgm.toFixed(2))}</b>`);
            innerTable.find('.total-drammed').html(`<b>${greatFormatRupiah(totalDrammed.toFixed(2))}</b>`);

            detailRow.append(`<td colspan="16"><b>DETAIL PACKING</b><br>${innerTable.prop('outerHTML')}</td>`);
            tbody.append(detailRow);
        });

        recalculateTotal();
    }

    function drawTableBarang() {
        const table = $('#barangTable');
        const tbody = table.find('#body-barang'); // tbody utama
        const tfoot = table.find('tfoot');

        tbody.empty();
        tfoot.empty();

        let no = 1;

        if (listBarang.length === 0) {
            const row = `
            <tr>
                <td colspan="4">List Barang Kosong</td>
            </tr>`;
            tfoot.append(row);
            return;
        }

        listBarang.forEach(item => {
            // === Row Utama Barang ===
            const newRow = $('<tr style="color:whitesmoke;">');
            newRow.append(`<td style="text-align:center;">${no++}</td>`);
            newRow.append(`<td>${item.nama_barang}</td>`);
            newRow.append(`<td>${item.catatan}</td>`);

            const actionButton = `
            <?php if (!empty($dataInvoice)) : ?>
                <?php if ($dataInvoice['status_posting']) : ?>
                    -
                <?php else : ?>
                    <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBarang('${item.id_barang}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteRowBarang('${item.id_barang}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBarang('${item.id_barang}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteRowBarang('${item.id_barang}')">
                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                </button>
            <?php endif; ?>
        `;

            newRow.append(`<td>${actionButton}</td>`);
            tbody.append(newRow);

            // === Row Kedua: Breakdown Table ===
            const detailRow = $('<tr  style="color:whitesmoke;">');
            const innerTable = $(`
            <table class="table table-sm table-bordered mb-2 w-100">
                <thead class="bg-warning text-dark">
                    <tr>
                        <th>Size</th>
                        <th>Grade</th>
                        <th>Satuan</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><b>TOTAL</b></td>
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

            detailRow.append(`<td colspan="8"><b>SIZE & BREAKDOWN</b><br>${innerTable.prop('outerHTML')}</td>`);
            tbody.append(detailRow);
        });

        recalculateTotal();
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
        $('#packing').val(item.packing);
        $('#catatan').val(item.catatan);
        listSizeBreakdown = item.size_breakdown;
        drawTableListSizeBreakDown(listSizeBreakdown);
        $('#label-modal-barang').text("Update ");
        $('#barangModal').modal('show');
    }


    function detailRowPacking(id_packing) {
        var item = null;
        for (var i = 0; i < listPacking.length; i++) {
            if (listPacking[i].id_packing == id_packing) {
                item = listPacking[i];
                break;
            }
        }

        $('#id_packing').val(item.id_packing);
        $('#nama_barang_packing').val(item.nama_barang_packing);
        $('#hs_code').val(item.hs_code).change();
        $('#keterangan_packing').val(item.keterangan_packing);
        listPackingSizeBreakdown = item.size_breakdown;
        drawTableListPackingSizeBreakDown(listPackingSizeBreakdown);
        $('#label-modal-packing').text("Update ");
        $('#packingModal').modal('show');
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

    function deleteRowPackingBreakdown(id_barang) {
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


    function deleteRowPacking(id_packing) {
        var indexToRemove = -1;
        for (var i = 0; i < listPacking.length; i++) {
            if (listPacking[i].id_packing == id_packing) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listPacking.splice(indexToRemove, 1);
        }
        drawTablePacking(listPacking);
    }


    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };

    function resetFormBiayaTambahan() {
        $('#id_biaya_tambahan').val(null);
        $('#biaya_tambahan').val(null);
        $('#tipe_biaya_tambahan').val("PLUS").change();
        $('#nilai_biaya_tambahan').val(null);
    }

    function recalculateTotal() {
        var totalInvoiceBarang = 0;
        var totalBiayaTambahan = 0;
        var totalBeratBersih = 0;
        var totalBeratKotor = 0;
        // List Barang
        $.each(listBarang, function(i, v) {
            $.each(v.size_breakdown, function(j, s) {
                totalInvoiceBarang += parseFloat(s.total);
            });
        });
        // List Biaya Tambahan
        $.each(listBiayaTambahan, function(i, v) {
            if (v.tipe_biaya_tambahan == "PLUS") {
                totalBiayaTambahan += parseFloat(v.nilai_biaya_tambahan);
            } else {
                totalBiayaTambahan -= parseFloat(v.nilai_biaya_tambahan);
            }
        });
        // List Packing
        $.each(listPacking, function(i, v) {
            $.each(v.size_breakdown, function(j, s) {
                totalBeratBersih += parseFloat(s.berat_bersih);
                totalBeratKotor += parseFloat(s.berat_kotor);
            });
        });
        var totalInvoiceFinal = totalInvoiceBarang + totalBiayaTambahan;
        $('#total_nilai_invoice').val(greatFormatRupiah(totalInvoiceFinal));
        $('#total_berat_bersih').val(greatFormatRupiah(totalBeratBersih));
        $('#total_berat_kotor').val(greatFormatRupiah(totalBeratKotor));
    }

    const print = function(url) {
        window.open(url, "_blank");
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
                    url: "<?= base_url("invoice-packing-customer/posting"); ?>",
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
                                    var salesOrderExportId = "<?= !empty($dataInvoice) ? encrypt($dataInvoice['sales_order_export_id']) : '' ?>";
                                    location.href = "<?= base_url('invoice-packing-customer/detail/') ?>" + salesOrderExportId;
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
                    url: "<?= base_url("invoice-packing-customer/unposting"); ?>",
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

    function syncBarangPacking() {
        listPacking = [];
        listBarang.forEach(item => {
            var result_breakdown = [];
            $.each(item.size_breakdown, function(j, s) {
                // id 19 = case /carton
                // id 68 = can
                var cased = null;
                var can = null;
                if (s.satuan_size_id == 19) {
                    cased = s.qty;
                } else if (s.satuan_size_id == 68) {
                    can = s.qty;
                }

                result_breakdown.push({
                    id_detail_breakdown_packing: getID(),
                    size: s.size,
                    grade: s.grade,
                    packing: null,
                    can: can,
                    kg: null,
                    lb: null,
                    inner_box: null,
                    pc: null,
                    bag: null,
                    palet: null,
                    persen: null,
                    case: cased,
                    qty: s.qty,
                    harga: s.harga,
                    total: s.total,
                    satuan_size_id: s.satuan_size_id,
                    satuan_size_code: s.satuan_size_code,
                    berat_bersih: null,
                    berat_kotor: null,
                    vgm: null,
                    drammed: null
                });
            })

            var result = {
                id_packing: getID(),
                hs_code: null,
                hs_code_name: '',
                nama_barang_packing: item.nama_barang,
                keterangan_packing: item.catatan,
                size_breakdown: result_breakdown,
            }

            listPacking.push(result);
        });
        drawTablePacking(listPacking);

    }

    function resetFormPacking() {
        $('#id_packing').val(null);
        $('#nama_barang_packing').val(null);
        $('#hs_code').val(null).change();
        $('#keterangan_packing').val(null);
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

    function resetFormBreakdownPacking() {
        $('#id_detail_breakdown_packing').val(null);
        $('#packing').val(null);
        $('#brand_packing').val(null);
        $('#can_dimension').val(null);
        $('#eu_approval_number').val(null);
        $('#qty_carton').val(null);
        $('#qty_cans').val(null);
        $('#berat_bersih').val(null);
        $('#berat_kotor').val(null);
        $('#vgm').val(null);
        $('#drammed').val(null);
    }

    function resetFormFormulaCan() {
        $('#formula_qty_case_hitung_can').val(null);
        $('#formula_bilangan_case_hitung_can').val(null);
        $('#hasil_qty_can_hitung_can').val(null);
    }

    function resetFormFormulaCase() {
        $('#formula_qty_can_hitung_case').val(null);
        $('#formula_bilangan_can_hitung_case').val(null);
        $('#hasil_qty_case_hitung_case').val(null);
    }

    function resetFormFormulaVgm() {
        $('#formula_berat_kotor_hitung_vgm').val(null);
        $('#formula_bilangan_hitung_vgm').val(null);
        $('#hasil_vgm').val(null);
    }

    function resetFormFormulaBeratKotor() {
        $('#formula_jumlah_case_can_hitung_berat_kotor').val(null).change();
        $('#formula_packing_hitung_berat_kotor').val(null);
        $('#formula_berat_bersih_hitung_berat_kotor').val(null);
        $('#formula_hasil_berat_kotor').val(null);
    }
</script>
<?= $this->endSection(); ?>