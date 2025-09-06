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
        <h1 class="title-name">Duplikat Commercial Invoice Customer</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("invoice-packing-customer/detail/" . encrypt($dataSalesOrderExport->sales_order_export_id)); ?>">
                Kembali
            </a>

            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Duplikat
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
                            <input value="<?= date('d/m/Y', strtotime($dataSalesOrderExport->tanggal_invoice))  ?>" autocomplete="one-time-code" disabled type="text" class="form-control">
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
                            <input value="<?= $dataSalesOrderExport->customer_name ?>" autocomplete="one-time-code" disabled type="text" class="form-control no_invoice">
                            <label for="floatingInput">Buyer / Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= strip_tags($dataSalesOrderExport->loading_port) ?>" autocomplete="one-time-code" disabled type="text" class="form-control no_invoice">
                            <label for="floatingInput">Port Of Loading</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= strip_tags($dataSalesOrderExport->dicharge_port) ?>" autocomplete="one-time-code" disabled type="text" class="form-control no_invoice">
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
                            <select <?= !empty($dataInvoice) ? ($dataInvoice['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="form-select valas_id" name="valas_id" id="valas_id">
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
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['phone'] : '' ?>" autocomplete=" one-time-code" type="text" class="form-control phone" placeholder="Phone (Opsional)" id="phone" name="phone">
                            <label for="floatingInput">Phone (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['attn'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control attn" placeholder="Attn (Opsional)" id="attn" name="attn">
                            <label for="floatingInput">Attn (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataInvoice) ? $dataInvoice['email'] : '' ?>" autocomplete=" one-time-code" type="text" placeholder="Email (Opsional)" class="form-control email" id="email" name="email">
                            <label for="floatingInput">Email (Optional)</label>
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
                            <input autocomplete="one-time-code" readonly type="text" class="form-control" id="total_carton" name="total_carton">
                            <label for="floatingInput">Total Carton</label>
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
                            <button <?= !empty($dataInvoice) ? ($dataInvoice['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnAddBarang" type="button" style="width: 90% !important;">
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
                            <button <?= !empty($dataInvoice) ? ($dataInvoice['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnBiayaTambahan" type="button" style="width: 90% !important;">
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
                            <button <?= !empty($dataInvoice) ? ($dataInvoice['status_posting'] == 1 ? 'disabled' : '') : '' ?> class="btn btn-show-detail btn-add btn-block float-right" id="btnAddPacking" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
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
                                    <th>Keterangan</th>
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
                                            <th>Packing</th>
                                            <th>Can</th>
                                            <th>Case</th>
                                            <th>Kg</th>
                                            <th>LB</th>
                                            <th>Inner Box</th>
                                            <th>PC</th>
                                            <th>Bag</th>
                                            <th>Cup</th>
                                            <th>Pallet</th>
                                            <th>%</th>
                                            <th>Remarks</th>
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
                                            <td colspan="15"></td>
                                            <td><b>TOTAL</b></td>
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
                                <label for="floatingInput">Grade (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control size" name="size" id="size" placeholder="Size (Opsional)">
                                <label for="floatingInput">Size (Opsional)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control packing_size" name="packing_size" id="packing_size" placeholder="Packing (Opsional)">
                                <label for="floatingInput">Packing (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control can" name="can" id="can" placeholder="Can (Opsional)">
                                <label for="floatingInput">Can (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control case" name="case" id="case" placeholder="Case (Opsional)">
                                <label for="floatingInput">Case (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control kg" name="kg" id="kg" placeholder="Kg (Opsional)">
                                <label for="floatingInput">Kg (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control lb" name="lb" id="lb" placeholder="Lb (Opsional)">
                                <label for="floatingInput">Lb (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control inner_box" name="inner_box" id="inner_box" placeholder="Inner Box (Opsional)">
                                <label for="floatingInput">Inner Box (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control pc" name="pc" id="pc" placeholder="Pc (Opsional)">
                                <label for="floatingInput">PC (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control bag" name="bag" id="bag" placeholder="Bag (Opsional)">
                                <label for="floatingInput">Bag (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control persen" name="persen" id="persen" placeholder="Persen (Opsional)" oninput="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Percentage % (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control cup" name="cup" id="cup" placeholder="Cup (Opsional)">
                                <label for="floatingInput">Cup (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control palet" name="palet" id="palet" placeholder="Cup (Opsional)">
                                <label for="floatingInput">Pallet (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
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
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" type="text" class="form-control remark" id="remark" name="remark" placeholder="Remark">
                                <label for="floatingInput">Remark (Opsional)</label>
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

                        <div class="col-subtitle-modal">
                            <div class="row mt-3 justify-content-end">
                                <div class="col-md-6">
                                    <label class="form-label font-weight-bold modal-sub-title"></label>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success btn-block float-right" type="button" id="btnAddPackingBreakdown">
                                        <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i> Tambah Packing
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="packingBreakdown" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Packing</th>
                                            <th>Brand</th>
                                            <th>Can Dimension</th>
                                            <th>Eu Approval Number</th>
                                            <th>Qty Karton</th>
                                            <th>Qty Cans</th>
                                            <th>Berat Bersih</th>
                                            <th>Berat Kotor</th>
                                            <th>VGM</th>
                                            <th>Drammed</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-packing-breakdown">
                                        <!-- isi data -->
                                    </tbody>
                                    <tfoot class="tfoot-packing-breakdown">
                                        <tr>
                                            <td colspan="4"></td>
                                            <td><b>TOTAL</b></td>
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
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control packing" name="packing" id="packing" placeholder="Nama Packing">
                                <label for="floatingInput">Nama Packing</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control qty_carton" name="qty_carton" id="qty_carton" oninput="this.value = greatFormatRupiah(this.value)" placeholder="Qty Carton (Opsional)">
                                <label for="floatingInput">Qty Karton</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control berat_bersih" oninput="this.value = greatFormatRupiah(this.value)" name="berat_bersih" id="berat_bersih" placeholder="Berat Bersih (Opsional)">
                                <label for="floatingInput">Berat Bersih (Kg)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control berat_kotor" oninput="this.value = greatFormatRupiah(this.value)" name="berat_kotor" id="berat_kotor" placeholder="Berat Kotor (Opsional)">
                                <label for="floatingInput">Berat Kotor (Kg)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control can_dimension" name="can_dimension" id="can_dimension" placeholder="Can Dimension (Opsional)">
                                <label for="floatingInput">Can Dimension (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control brand_packing" name="brand_packing" id="brand_packing" placeholder="Brand Packing (Opsional)">
                                <label for="floatingInput">Brand Packing (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control eu_approval_number" name="eu_approval_number" id="eu_approval_number" placeholder="Eu Approval Number (Opsional)">
                                <label for="floatingInput">Eu Approval Number (Opsional)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control qty_cans" name="qty_cans" id="qty_cans" placeholder="Qty Cans (Opsional)">
                                <label for="floatingInput">Qty Cans (Opsional)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control vgm" oninput="this.value = greatFormatRupiah(this.value)" name="vgm" id="vgm" placeholder="VGM (Opsional)">
                                <label for="floatingInput">VGM (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control drammed" oninput="this.value = greatFormatRupiah(this.value)" name="drammed" id="drammed" placeholder="Drammed (Opsional)">
                                <label for="floatingInput">Drammed (Opsional)</label>
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


        $("#departure_date,#keberangkatan_kapal,#tanggal_surat_jalan,#tanggal_faktur_pajak").datepicker({
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

        // VALIDATOR PARENT BARANG  
        var validator = $("#form-parent").validate({
            rules: {
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
                    required: "Packing wajib diisi"
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


        $('#btnSubmitSizeBreakDown').click(function(e) {
            e.preventDefault();
            if ($('.create-form-size-breakdown').valid()) {
                var id_detail_breakdown = $('#id_detail_breakdown').val();
                var size = $('#size').val();
                var grade = $('#grade').val();
                var packing_size = $('#packing_size').val();
                var can = $('#can').val();
                var cased = destroyFormatRupiah($('#case').val());
                var kg = $('#kg').val();
                var lb = $('#lb').val();
                var inner_box = $('#inner_box').val();
                var pc = $('#pc').val();
                var bag = $('#bag').val();
                var palet = $('#palet').val();
                var persen = destroyFormatRupiah($('#persen').val() || 0);
                var qty = $('#qty').val();
                var harga = destroyFormatRupiah($('#harga').val());
                var total = destroyFormatRupiah($('#total').val());
                var remark = $('#remark').val();
                var satuan_size_id = $('#satuan_size_id option:selected').val();
                var satuan_size_code = $('#satuan_size_id option:selected').text();

                var result = {
                    id_detail_breakdown: id_detail_breakdown,
                    size: size,
                    grade: grade,
                    packing: packing_size,
                    can: can,
                    cased: cased,
                    kg: kg,
                    lb: lb,
                    inner_box: inner_box,
                    pc: pc,
                    bag: bag,
                    palet: palet,
                    persen: persen,
                    qty: qty,
                    harga: harga,
                    total: harga * qty,
                    remark: remark,
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

                    listSizeBreakdown[index].palet = palet;
                    listSizeBreakdown[index].size = result.size;
                    listSizeBreakdown[index].grade = result.grade;
                    listSizeBreakdown[index].packing = result.packing;
                    listSizeBreakdown[index].can = result.can;
                    listSizeBreakdown[index].cased = result.cased;
                    listSizeBreakdown[index].kg = result.kg;
                    listSizeBreakdown[index].lb = result.lb;
                    listSizeBreakdown[index].inner_box = result.inner_box;
                    listSizeBreakdown[index].pc = result.pc;
                    listSizeBreakdown[index].bag = result.bag;
                    listSizeBreakdown[index].persen = result.persen;
                    listSizeBreakdown[index].qty = result.qty;
                    listSizeBreakdown[index].harga = result.harga;
                    listSizeBreakdown[index].total = result.total;
                    listSizeBreakdown[index].remark = result.remark;
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
                var packing = $('#packing').val();
                var canDimension = $('#can_dimension').val();
                var brandPacking = $('#brand_packing').val();
                var euApprovalNumber = $('#eu_approval_number').val();
                var qtyCarton = destroyFormatRupiah($('#qty_carton').val());
                var qtyCans = destroyFormatRupiah($('#qty_cans').val());
                var beratBersih = destroyFormatRupiah($('#berat_bersih').val());
                var beratKotor = destroyFormatRupiah($('#berat_kotor').val());
                var vgm = destroyFormatRupiah($('#vgm').val());
                var drammed = destroyFormatRupiah($('#drammed').val());

                var result = {
                    id_detail_breakdown_packing: idDetailBreakdownPacking,
                    packing: packing,
                    can_dimension: canDimension,
                    brand_packing: brandPacking,
                    eu_approval_number: euApprovalNumber,
                    qty_carton: qtyCarton,
                    qty_cans: qtyCans,
                    berat_bersih: beratBersih,
                    berat_kotor: beratKotor,
                    vgm: vgm,
                    drammed: drammed
                };

                if (idDetailBreakdownPacking == '' || idDetailBreakdownPacking == null) {
                    // Create
                    result.id_detail_breakdown_packing = getID();
                    listPackingSizeBreakdown.push(result);
                } else {
                    // Update
                    var index = null;
                    for (var i = 0; i < listPackingSizeBreakdown.length; i++) {
                        if (listPackingSizeBreakdown[i].id_detail_breakdown_packing == idDetailBreakdownPacking) {
                            index = i;
                            break;
                        }
                    }

                    listPackingSizeBreakdown[index].packing = packing;
                    listPackingSizeBreakdown[index].can_dimension = result.can_dimension;
                    listPackingSizeBreakdown[index].brand_packing = result.brand_packing;
                    listPackingSizeBreakdown[index].eu_approval_number = result.eu_approval_number;
                    listPackingSizeBreakdown[index].qty_carton = result.qty_carton;
                    listPackingSizeBreakdown[index].qty_cans = result.qty_cans;
                    listPackingSizeBreakdown[index].berat_bersih = result.berat_bersih;
                    listPackingSizeBreakdown[index].berat_kotor = result.berat_kotor;
                    listPackingSizeBreakdown[index].vgm = result.vgm;
                    listPackingSizeBreakdown[index].drammed = result.drammed;
                }

                drawTableListPackingSizeBreakDown(listPackingSizeBreakdown);
                $('#addPackingBreakdownModal').modal('hide');

            }
        })

        $('#btnAddPacking').click(function(e) {
            e.preventDefault();
            $('#packingModal').modal('show');
            $('#label-modal-packing').text("Tambah ");
            listPackingSizeBreakdown = [];
            drawTableListPackingSizeBreakDown(listPackingSizeBreakdown);
            resetFormPacking();

        });

        $('#btnAddPackingBreakdown').click(function(e) {
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
        })

        $('#qty_barang,#harga_satuan').keyup(function(e) {
            e.preventDefault();
            var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
            var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
            var totalHarga = qtyBarang * hargaSatuan;

            $('#total_harga').val(greatFormatRupiah(totalHarga.toFixed(2)));
        });

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
                if ($('#form-barang').valid()) {
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
                            let totalCarton = destroyFormatRupiah($('#total_carton').val());
                            let totalBeratBersih = destroyFormatRupiah($('#total_berat_bersih').val());
                            let totalBeratKotor = destroyFormatRupiah($('#total_berat_kotor').val());

                            data.set("total_nilai_invoice", totalNilaiInvoice);
                            data.set("total_carton", totalCarton);
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
        console.log(listSizeBreakdown);
        $('.body-barang-size-breakdown').empty();
        $('.tfoot-detail-table-size-breakdown').empty();
        var row = '';
        var no = 1;
        const table = $('#productSizeBreakdown');
        if (listSizeBreakdown.length === 0) {
            row += `
                    <tr>
                        <td colspan="15"></td>
                        <td><b>TOTAL</b></td>
                        <td><b>0.00</b></td>
                        <td><b>0.00</b></td>
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
                newRow.append($('<td>').text(item.packing));
                newRow.append($('<td>').text(item.can));
                newRow.append($('<td>').text(greatFormatRupiah(item.cased)));
                newRow.append($('<td>').text(item.kg));
                newRow.append($('<td>').text(item.lb));
                newRow.append($('<td>').text(item.inner_box));
                newRow.append($('<td>').text(item.pc));
                newRow.append($('<td>').text(item.bag));
                newRow.append($('<td>').text(item.cup));
                newRow.append($('<td>').text(item.palet));
                newRow.append($('<td>').text(item.persen));
                newRow.append($('<td>').text(item.remark));
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
            newRow.append($('<td colspan="15"></td>'));
            newRow.append($('<td><b>TOTAL</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalQty) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalHarga.toFixed(2)) + '</b></td>'));
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
                        <td colspan="4"></td>
                        <td><b>TOTAL</b></td>
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
            var totalQtyCarton = 0;
            var totalQtyCans = 0;
            var totalBeratBersih = 0;
            var totalBeratKotor = 0;
            var totalVgm = 0;
            var totalDrammed = 0;

            listPackingSizeBreakdown.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.packing));
                newRow.append($('<td>').text(item.brand_packing));
                newRow.append($('<td>').text(item.can_dimension));
                newRow.append($('<td>').text(item.eu_approval_number));
                newRow.append($('<td>').text(greatFormatRupiah(item.qty_carton)));
                newRow.append($('<td>').text(greatFormatRupiah(item.qty_cans)));
                newRow.append($('<td>').text(greatFormatRupiah(item.berat_bersih)));
                newRow.append($('<td>').text(greatFormatRupiah(item.berat_kotor)));
                newRow.append($('<td>').text(greatFormatRupiah(item.vgm)));
                newRow.append($('<td>').text(greatFormatRupiah(item.drammed)));
                newRow.append($('<td>').html(
                    `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPackingBreakdown('${item.id_detail_breakdown_packing}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowPackingBreakdown('${item.id_detail_breakdown_packing}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                ));

                totalQtyCarton += parseFloat(item.qty_carton);
                totalQtyCans += destroyFormatRupiah(item.qty_cans);
                totalBeratBersih += destroyFormatRupiah(item.berat_bersih);
                totalBeratKotor += destroyFormatRupiah(item.berat_kotor);
                totalVgm += destroyFormatRupiah(item.vgm);
                totalDrammed += destroyFormatRupiah(item.drammed);

                table.find('tbody').append(newRow);
            });
            $('#body-packing-breakdown').append(row);
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td colspan="4"></td>'));
            newRow.append($('<td><b>TOTAL</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalQtyCarton.toFixed(2)) + '</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalQtyCans.toFixed(2)) + '</b></td>'));
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
        $('#case').val(greatFormatRupiah(item.cased));
        $('#kg').val(item.kg);
        $('#lb').val(item.lb);
        $('#inner_box').val(item.inner_box);
        $('#pc').val(item.pc);
        $('#bag').val(item.bag);
        $('#persen').val(item.persen);
        $('#qty').val(item.qty);
        $('#harga').val(greatFormatRupiah(item.harga));
        $('#total').val(greatFormatRupiah(item.total));
        $('#remark').val(item.remark);
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
        $('.title-breakdown-packing').text('Update ');
        $('#id_detail_breakdown_packing').val(item.id_detail_breakdown_packing);
        $('#packing').val(item.packing);
        $('#can_dimension').val(item.can_dimension);
        $('#brand_packing').val(item.brand_packing);
        $('#eu_approval_number').val(item.eu_approval_number);
        $('#qty_carton').val(greatFormatRupiah(item.qty_carton));
        $('#qty_cans').val(greatFormatRupiah(item.qty_cans));
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
            <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPacking('${item.id_packing}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteRowPacking('${item.id_packing}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>
              
        `;

            newRow.append(`<td>${actionButton}</td>`);
            tbody.append(newRow);

            // === Row Kedua: Breakdown Table ===
            const detailRow = $('<tr  style="color:whitesmoke;">');
            const innerTable = $(`
            <table class="table table-sm table-bordered mb-2 w-100">
                <thead class="bg-warning text-dark">
                    <tr>
                        <th>Packing</th>
                        <th>Brand</th>
                        <th>Can Dimension</th>
                        <th>Eu Approval Number</th>
                        <th>Qty Karton</th>
                        <th>Qty Cans</th>
                        <th>Berat Bersih</th>
                        <th>Berat Kotor</th>
                        <th>VGM</th>
                        <th>Drammed</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><b>TOTAL</b></td>
                        <td class="total-carton"><b>0.00</b></td>
                        <td class="total-cans"><b>0.00</b></td>
                        <td class="total-berat-bersih"><b>0.00</b></td>
                        <td class="total-berat-kotor"><b>0.00</b></td>
                        <td class="total-vgm"><b>0.00</b></td>
                        <td class="total-drammed"><b>0.00</b></td>
                    </tr>
                </tfoot>
            </table>
        `);

            const breakdownBody = innerTable.find('tbody');
            let totalCarton = 0;
            let totalCans = 0;
            let totalBeratBersih = 0;
            let totalBeratKotor = 0;
            let totalVgm = 0;
            let totalDrammed = 0;

            item.size_breakdown.forEach(size => {
                let qty_carton = parseFloat(size.qty_carton) || 0;
                let qty_cans = parseFloat(size.qty_cans) || 0;
                let berat_bersih = parseFloat(size.berat_bersih) || 0;
                let berat_kotor = parseFloat(size.berat_kotor) || 0;
                let vgm = parseFloat(size.vgm) || 0;
                let drammed = parseFloat(size.drammed) || 0;

                totalCarton += qty_carton;
                totalCans += qty_cans;
                totalBeratBersih += berat_bersih;
                totalBeratKotor += berat_kotor;
                totalVgm += vgm;
                totalDrammed += drammed;

                const row = `
                <tr>
                    <td>${size.packing || ''}</td>
                    <td>${size.brand_packing || ''}</td>
                    <td>${size.can_dimension || ''}</td>
                    <td>${size.eu_approval_number || ''}</td>
                    <td>${greatFormatRupiah(qty_carton.toFixed(2))}</td>
                    <td>${greatFormatRupiah(qty_cans.toFixed(2))}</td>
                    <td>${greatFormatRupiah(berat_bersih.toFixed(2))}</td>
                    <td>${greatFormatRupiah(berat_kotor.toFixed(2))}</td>
                    <td>${greatFormatRupiah(vgm.toFixed(2))}</td>
                    <td>${greatFormatRupiah(drammed.toFixed(2))}</td>
                </tr>
            `;
                breakdownBody.append(row);
            });

            innerTable.find('.total-carton').html(`<b>${greatFormatRupiah(totalCarton.toFixed(2))}</b>`);
            innerTable.find('.total-cans').html(`<b>${greatFormatRupiah(totalCans.toFixed(2))}</b>`);
            innerTable.find('.total-berat-bersih').html(`<b>${greatFormatRupiah(totalBeratBersih.toFixed(2))}</b>`);
            innerTable.find('.total-berat-kotor').html(`<b>${greatFormatRupiah(totalBeratKotor.toFixed(2))}</b>`);
            innerTable.find('.total-vgm').html(`<b>${greatFormatRupiah(totalVgm.toFixed(2))}</b>`);
            innerTable.find('.total-drammed').html(`<b>${greatFormatRupiah(totalDrammed.toFixed(2))}</b>`);

            detailRow.append(`<td colspan="10"><b>DETAIL PACKING</b><br>${innerTable.prop('outerHTML')}</td>`);
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
                        <th>Packing</th>
                        <th>Can</th>
                        <th>Case</th>
                        <th>Kg</th>
                        <th>LB</th>
                        <th>Inner Box</th>
                        <th>PC</th>
                        <th>Bag</th>
                        <th>Unit</th>
                        <th>Remarks</th>
                        <th>Pallet</th>
                        <th>%</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="13" class="text-end"><b>TOTAL</b></td>
                        <td class="total-persen"><b>0.00</b></td>
                        <td class="total-qty"><b>0.00</b></td>
                        <td class="total-price"><b>0.00</b></td>
                        <td class="total-amount"><b>0.00</b></td>
                    </tr>
                </tfoot>
            </table>
        `);

            const breakdownBody = innerTable.find('tbody');
            let totalQtySize = 0;
            let totalHargaSize = 0;
            let totalAmountSize = 0;
            let totalPersenSize = 0;

            item.size_breakdown.forEach(size => {
                let qty = parseFloat(size.qty) || 0;
                let harga = parseFloat(size.harga) || 0;
                let total = parseFloat(size.total) || 0;
                let persen = parseFloat(size.persen) || 0;

                totalQtySize += qty;
                totalHargaSize += harga;
                totalAmountSize += total;
                totalPersenSize += persen;

                const row = `
                <tr>
                    <td>${size.size || ''}</td>
                    <td>${size.grade || ''}</td>
                    <td>${size.packing || ''}</td>
                    <td>${size.can || ''}</td>
                    <td>${size.cased || ''}</td>
                    <td>${size.kg || ''}</td>
                    <td>${size.lb || ''}</td>
                    <td>${size.inner_box || ''}</td>
                    <td>${size.pc || ''}</td>
                    <td>${size.bag || ''}</td>
                    <td>${size.satuan_size_code || ''}</td>
                    <td>${size.remark || ''}</td>
                    <td>${size.palet || ''}</td>
                    <td>${size.persen || ''}</td>
                    <td>${greatFormatRupiah(qty.toFixed(2))}</td>
                    <td>${greatFormatRupiah(harga.toFixed(2))}</td>
                    <td>${greatFormatRupiah(total.toFixed(2))}</td>
                </tr>
            `;
                breakdownBody.append(row);
            });

            innerTable.find('.total-persen').html(`<b>${totalPersenSize == 0 ? "" : greatFormatRupiah(totalPersenSize.toFixed(2))}</b>`);
            innerTable.find('.total-qty').html(`<b>${greatFormatRupiah(totalQtySize.toFixed(2))}</b>`);
            innerTable.find('.total-price').html(`<b>${greatFormatRupiah(totalHargaSize.toFixed(2))}</b>`);
            innerTable.find('.total-amount').html(`<b>${greatFormatRupiah(totalAmountSize.toFixed(2))}</b>`);

            detailRow.append(`<td colspan="13"><b>SIZE & BREAKDOWN</b><br>${innerTable.prop('outerHTML')}</td>`);
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
        var totalCarton = 0;
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
                totalCarton += parseFloat(s.qty_carton);
                totalBeratBersih += parseFloat(s.berat_bersih);
                totalBeratKotor += parseFloat(s.berat_kotor);
            });
        });
        console.log(listPacking);
        console.log(totalCarton, totalBeratBersih, totalBeratKotor);
        var totalInvoiceFinal = totalInvoiceBarang + totalBiayaTambahan;
        $('#total_nilai_invoice').val(greatFormatRupiah(totalInvoiceFinal));
        $('#total_carton').val(greatFormatRupiah(totalCarton));
        $('#total_berat_bersih').val(greatFormatRupiah(totalBeratBersih));
        $('#total_berat_kotor').val(greatFormatRupiah(totalBeratKotor));

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
        $('#remark').val(null);
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
</script>
<?= $this->endSection(); ?>