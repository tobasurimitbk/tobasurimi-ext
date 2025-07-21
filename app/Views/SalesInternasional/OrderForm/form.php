<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<style>
    .table-sm th,
    .table-sm td {
        padding: 0.35rem !important;
        vertical-align: middle;
    }
</style>

<?php if (session()->get('theme') == "dark"): ?>
    <style>
        .bg-light {
            background-color: #343A40 !important;
        }

        .bg-warning {
            background-color: #343A40 !important;
        }
    </style>
<?php else: ?>
    <style>
        .bg-light {
            background-color: #f9f9f9 !important;
        }

        .bg-warning {
            background-color: #fff3cd !important;
        }
    </style>
<?php endif; ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataSalesExport) ? "Update Order Form" : "Create Order Form" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("order-form-internasional"); ?>">
                Back
            </a>
            <?php if (!empty($dataSalesExport)) { ?>
                <?php if (can('Penjualan Ekspor', 'Order Form', 'p')): ?>
                    <button class="btn btn-warning btn-print float-right">
                        Print
                    </button>
                <?php endif; ?>
                <?php if ($dataSalesExport->status === "NEW") { ?>
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
                <?php if ($dataSalesExport->status === "POSTED") { ?>
                    <?php if (can('Penjualan Ekspor', 'Order Form', 'ua')): ?>
                        <button class="btn btn-success posting-spp unposting-so float-right">
                            Unposting
                        </button>
                    <?php endif; ?>
                <?php } ?>

            <?php } else { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Save
                </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <!-- Otomatis Terisi BC 3.0 -->
                <input type="hidden" name="aju_document_type" id="aju_document_type" value="<?= $dataAJU[0]['id'] ?>">
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
                                    <input autocomplete="one-time-code" type="text" value="<?= !empty($dataSalesExport) ? $dataSalesExport->sales_order_export_no : 'AUTO GENERATE' ?>" <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : 'readonly' ?> class="form-control sales_order_export_no" id="sales_order_export_no" name="sales_order_export_no" placeholder="Sales Order No" required>
                                    <label for="floatingInput">Sales Order No</label>
                                </div>
                                <div style="<?= !empty($dataSalesExport) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select
                                class="form-select divisi_id"
                                aria-label="Floating label select example"
                                name="divisi_id"
                                id="divisi_id">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>">
                                        <?= $d['divisi'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Department</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <?php if (!empty($dataSalesExport)) { ?>
                                <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->sales_contract_id ?>" type="hidden" class="form-control sales_kontrak_id" id="sales_kontrak_id" name="sales_kontrak_id" placeholder="Sales Kontrak">
                                <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->sales_contract_no ?>" type="text" class="form-control sales_kontrak_no" id="sales_kontrak_no" name="sales_kontrak_no" placeholder="Sales Kontrak">
                            <?php } ?>
                            <?php if (empty($dataSalesExport)) { ?>
                                <select class="form-select sales_contract_id" name="sales_contract_id" id="sales_contract_id">
                                    <option value=""></option>
                                </select>
                            <?php } ?>
                            <label for="floatingInput">Sales Kontrak</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Sensitech Information</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Sales Order Date" value="<?= date('d/m/Y')  ?>">
                                    <label for="floatingInput">Sales Order Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control tax_id" id="tax_id" name="tax_id" placeholder="Tax Id (Optional)">
                            <label for="floatingInput">Tax ID (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control consigne" id="consigne" name="consigne" placeholder="Consigne / Buyer" readonly>
                            <label for="floatingInput">Consigne / Buyer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control destination" id="destination" name="destination" placeholder="Destination" readonly>
                            <label for="floatingInput">Destination</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control deadline" id="deadline" name="deadline" placeholder="Deadline" readonly>
                            <label for="floatingInput">Deadline</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="PO Number" readonly>
                            <label for="floatingInput">PO No</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Item List</label>
                    </div>
                </div>

                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTableBarang" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Item Code</th>
                                    <th>Goods</th>
                                    <th>Species</th>
                                    <th>Brand</th>
                                    <th>Packing</th>
                                    <th>Specs</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody id="body-detail-table-barang">

                            </tbody>
                            <tfoot id="foot-detail-table-barang">
                                <tr>
                                    <td colspan="6"></td>
                                    <td><b>TOTAL</b></td>
                                    <td><b>0,00</b></td>
                                    <td><b>0,00</b></td>
                                    <td><b>0,00</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control commision" id="commision" name="commision" placeholder="Commision (Optional)">
                            <label for="floatingInput">Commision (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control palet_fumigation" id="palet_fumigation" name="palet_fumigation" placeholder="Pallet & Fumigation (Optional)">
                            <label for="floatingInput">Pallet & Fumigation (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control palet_fumigation_number" id="palet_fumigation_number" name="palet_fumigation_number" placeholder="Pallet & Fumigation Number (Optional)" oninput="this.value = greatFormatRupiah(this.value)">
                            <label for="floatingInput">Pallet & Fumigation Number (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="" type="text" class="form-control additional_detail" id="additional_detail" name="additional_detail" placeholder="Additional Details (Optional)">
                            <label for="floatingInput">Additional Details (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select name="additional_detail_type" id="additional_detail_type" class="form-control additional_detail_type">
                                    <option value="PLUS">PLUS (+)</option>
                                    <option value="MINUS">MINUS (-)</option>
                                </select>
                            </div>
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control additional_detail_price" id="additional_detail_price" name="additional_detail_price" placeholder="Additional Detail Price (Optional)">
                                <label for="floatingInput">Additional Details Number (Optional)</label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Docs & Certificate Required</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term">
                            <label for="floatingInput">Payment Term</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control shipment_an" id="shipment_an" name="shipment_an" placeholder="Shipment A/N (Optional)">
                            <label for="floatingInput">Shipment A/N (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control consigne" id="consigne" name="consigne" placeholder="Consigne (Optional)">
                            <label for="floatingInput">Consigne (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control notify_party" id="notify_party" name="notify_party" placeholder="Notify Party (Optional)">
                            <label for="floatingInput">Notify Party (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control additional_details" id="additional_details" name="additional_details" placeholder="Additional Details (Optional)">
                            <label for="floatingInput">Additional Details (Optional)</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Special Instruction</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 mt-2 mb-3">
                        <label class="text-dark">Product Specs (Optional)</label>
                        <textarea class="form-control tiny product_specs" id="product_specs"></textarea>
                    </div>

                    <div class="col-sm-6 mt-2 mb-3">
                        <label class="text-dark">Processing Method (Optional)</label>
                        <textarea class="form-control tiny processing_method" id="processing_method"></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label class="text-dark">Packaging (Optional)</label>
                        <textarea class="form-control tiny packaging" id="packaging"></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label class="text-dark">Code Stamping (Optional)</label>
                        <textarea class="form-control tiny code_stamping" id="code_stamping"></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label class="text-dark">Loading (Optional)</label>
                        <textarea class="form-control tiny loading" id="loading"></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label class="text-dark">Foto Loading (Optional)</label>
                        <textarea class="form-control tiny foto_loading" id="foto_loading"></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label class="text-dark">Stuffing (Optional)</label>
                        <textarea class="form-control tiny stuffing" id="stuffing"></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label class="text-dark">Additional Details (Optional)</label>
                        <textarea class="form-control tiny additional_detail" id="additional_detail"></textarea>
                    </div>
                </div>

                <div id="component-detail-specs-list">
                    <div class="col-subtitle-modal mt-5">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title">Detail Specs List (Only Departement PTS)</label>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAddDetailSpecs" type="button">
                                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add Specs
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable2" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Grade</th>
                                        <th>Specification</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-detail-specs" id="body-detail-specs">

                                </tbody>
                                <tfoot class="foot-detail-specs" id="foot-detail-specs">
                                    <tr>
                                        <td colspan="4">List Detail Specs Empty</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

<div class="modal detail-modal" id="updateQtyOrderFormModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Update Qty Order Form</h5>
            </div>
            <form class="create-form-size-breakdown" role="form" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_detail_breakdown" id="id_detail_breakdown">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control size" name="size" id="size" placeholder="Size (Optional)">
                                <label for="floatingInput">Size (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control grade" name="grade" id="grade" placeholder="Grade (Optional)">
                                <label for="floatingInput">Grade (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control packing_size" name="packing_size" id="packing_size" placeholder="Packing (Optional)">
                                <label for="floatingInput">Packing (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control can" name="can" id="can" placeholder="Can (Optional)">
                                <label for="floatingInput">Can (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control case" name="case" id="case" placeholder="Case (Optional)">
                                <label for="floatingInput">Case (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control kg" name="kg" id="kg" placeholder="Kg (Optional)">
                                <label for="floatingInput">Kg (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control lb" name="lb" id="lb" placeholder="Lb (Optional)">
                                <label for="floatingInput">Lb (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control inner_box" name="inner_box" id="inner_box" placeholder="Inner Box (Optional)">
                                <label for="floatingInput">Inner Box (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control pc" name="pc" id="pc" placeholder="Pc (Optional)">
                                <label for="floatingInput">PC (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control bag" name="bag" id="bag" placeholder="Bag (Optional)">
                                <label for="floatingInput">Bag (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control persen" name="persen" id="persen" placeholder="Persen (Optional)" oninput="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Percentage % (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control cup" name="cup" id="cup" placeholder="Cup (Optional)">
                                <label for="floatingInput">Cup (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control palet" name="palet" id="palet" placeholder="Cup (Optional)">
                                <label for="floatingInput">Pallet (Optional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col mb-3">
                            <h6 class="<?= session()->get('theme') == "dark" ? "text-white" : "text-dark" ?>">Price Data</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty Order Form</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select disabled class="form-select satuan_size_id" name="satuan_size_id" id="satuan_size_id">
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d) : ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Unit</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
                                <label for="floatingInput">Unit Price</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" type="text" class="form-control remark" id="remark" name="remark" placeholder="Remark">
                                <label for="floatingInput">Remark (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total Price</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideSizeBreakdownModal">Back</button>
                    <button type="submit" class="btn btn-submit-form" id="btnSubmitSizeBreakDown">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="detailSpecsModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Create Detail Specs List</h5>
            </div>
            <form class="create-form-detail-specs-list" role="form" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control grade" name="grade" id="grade" placeholder="Grade">
                                <label for="floatingInput">Grade</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control specification" name="specification" id="specification" placeholder="Specification">
                                <label for="floatingInput">Specification</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnDetailSpecsHide">Back</button>
                    <button type="submit" class="btn btn-submit-form" id="btnSubmitSpecsDetail">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listDataSalesKontrak = [];
    var listDetailSpecs = [];
    var totalAmount = 0;
    // HIDE DETAIL SPECS LIST
    $('#component-detail-specs-list').hide();

    $(document).ready(function() {
        <?php if (!empty($dataSalesExport)) { ?>
            <?php if (!empty($dataSalesExportDetail)) { ?>
                <?php foreach ($dataSalesExportDetail as $sales) { ?>
                    listBarang.push({
                        id: "<?= $sales->barang_id ?>",
                        barang_name: "<?= $sales->barang_name ?>",
                        harga: "<?= $sales->hargaContract ?>",
                        id_detail_sales_order: "<?= $sales->sales_order_export_detail_id ?>",
                        kemasan: "<?= $sales->kemasan ?>",
                        kode_barang: "<?= $sales->barang_kode ?>",
                        qty: <?= floatval($sales->qtyContract)  ?>,
                        remark: "<?= $sales->remark ?>",
                        satuan_order_id: "<?= $sales->satuan_id ?>",
                        satuan_order_name: "<?= $sales->kode_satuan ?>",
                        total: <?= floatval($sales->totalHargaContract) ?>,
                        qtyOrder: <?= floatval($sales->qty) ?>,
                        hargaOrder: <?= floatval($sales->harga_barang) ?>,
                        totalHargaOrder: <?= floatval($sales->total_harga_barang) ?>,
                    });
                <?php } ?>
                drawTable();
            <?php } ?>
        <?php } else { ?>

        <?php } ?>

        // Sales Kontrak
        $('.sales_contract_id').select2({
            placeholder: "Select Sales Kontrak",
            theme: "bootstrap-5"
        }).change(function() {
            listBarang = [];
            getDetailSalesKontrak();
        });

        $("#tanggal").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('.divisi_id').select2({
            placeholder: "Select Department",
            theme: "bootstrap-5"
        }).change(function() {
            dropdownSalesKontrak();
        });


        //CSS SELECT2 FLOATING LABEL
        $('.sales_contract_id, .divisi_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.sales_contract_id, .divisi_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.sales_contract_id, .divisi_id')
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
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di Unposting?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Unposting',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    $.ajax({
                        url: "<?= base_url("order-form-internasional/update-status"); ?>",
                        data: {
                            id: $(".id").val(),
                            status: "NEW"
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

        var validator = $(".create-form").validate({
            rules: {
                no_sales_order: {
                    required: true
                },
                sales_contract_id: {
                    required: true
                },
                documents_required: {
                    required: true
                },
                special_instructions: {
                    required: true
                }
            },
            messages: {
                no_sales_order: {
                    required: "No sales order wajib diisi"
                },
                sales_contract_id: {
                    required: "Sales kontrak wajib diisi"
                },
                documents_required: {
                    required: "Dokumen Required Wajib Diisi"
                },
                special_instructions: {
                    required: "Special Instructions Wajib Diisi"
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

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")

            if ($(".create-form").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        let data = new FormData(document.querySelector(".create-form"));

                        let id = $(".id").val();
                        data.append("items", JSON.stringify(listBarang));
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("order-form-internasional/update"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                var id = response.id; // Menyimpan nilai response.id ke dalam variabel id
                                                window.location.href = "<?= base_url("order-form-internasional") ?>"; // Mengarahkan ke URL dengan menambahkan id ke belakangnya
                                            })
                                        stopLoading();
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
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        } else {
                            $.ajax({
                                url: "<?= base_url("order-form-internasional/save"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                method: "POST",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    csrf.val(response.token);
                                    if (response.status) {
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                var id = response.id; // Menyimpan nilai response.id ke dalam variabel id
                                                window.location.href = "<?= base_url("order-form-internasional") ?>"; // Mengarahkan ke URL dengan menambahkan id ke belakangnya
                                            })
                                        stopLoading();
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
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                    }
                })
            }
        })
    })

    function getSalesKontrak() {
        setLoading()
        $.ajax({
            url: `<?= base_url("order-form-internasional/get/sales-kontrak"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".sales_kontrak").empty();

                $(".sales_kontrak").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".sales_kontrak").append(`<option value="${item.id}">${item.sales_contract_no}</option>`);
                })

                $(".sales_kontrak").val("").change();
                stopLoading();
            }
        })
    }

    function detailRow(id, id_detail_breakdown) {
        var itemSalesContractDetail = null;
        var itemSizeBreakdown = null;
        var listSalesContractDetail = listDataSalesKontrak.salesContractDetailList;
        for (var i = 0; i < listSalesContractDetail.length; i++) {
            if (listSalesContractDetail[i].id == id) {
                itemSalesContractDetail = listSalesContractDetail[i];
                break;
            }
        }

        for (var i = 0; i < itemSalesContractDetail.size_breakdown.length; i++) {
            if (itemSalesContractDetail.size_breakdown[i].id_detail_breakdown == id_detail_breakdown) {
                itemSizeBreakdown = itemSalesContractDetail.size_breakdown[i];
                break;
            }
        }

        $('#id_detail_breakdown').val(itemSizeBreakdown.id_detail_breakdown);
        $('#size').val(itemSizeBreakdown.size).attr('readonly', true);
        $('#grade').val(itemSizeBreakdown.grade).attr('readonly', true);
        $('#packing_size').val(itemSizeBreakdown.packing_size).attr('readonly', true);
        $('#can').val(itemSizeBreakdown.can).attr('readonly', true);
        $('#case').val(itemSizeBreakdown.cased).attr('readonly', true);
        $('#kg').val(itemSizeBreakdown.kg).attr('readonly', true);
        $('#lb').val(itemSizeBreakdown.lb).attr('readonly', true);
        $('#inner_box').val(itemSizeBreakdown.inner_box).attr('readonly', true);
        $('#pc').val(itemSizeBreakdown.pc).attr('readonly', true);
        $('#bag').val(itemSizeBreakdown.bag).attr('readonly', true);
        $('#persen').val(itemSizeBreakdown.persen).attr('readonly', true);
        $('#cup').val(itemSizeBreakdown.cup).attr('readonly', true);
        $('#palet').val(itemSizeBreakdown.palet).attr('readonly', true);
        $('#remark').val(itemSizeBreakdown.remark).attr('readonly', true);

        $('#qty').val(itemSizeBreakdown.qty_sisa);
        $('#harga').val(greatFormatRupiah(itemSizeBreakdown.harga)).attr('readonly', true);
        $('#total').val(greatFormatRupiah(itemSizeBreakdown.total_sisa)).attr('readonly', true);
        $('#satuan_size_id').val(itemSizeBreakdown.satuan_size_id).change();

        $('#updateQtyOrderFormModal').modal('show');

    }

    $('#btnHideSizeBreakdownModal').click(function() {
        $('#updateQtyOrderFormModal').modal('hide');
    });

    $('#btnAddDetailSpecs').click(function() {
        $('#detailSpecsModal').modal('show');
    });

    $('#btnDetailSpecsHide').click(function() {
        $('#detailSpecsModal').modal('hide');

    });

    $('.btn-submit-detail').click(function() {
        var id_detail = $('#id_detail').val();
        var remark = $('#remark').val();

        if (remark == '') {
            Swal.fire({
                icon: 'error',
                title: "Remark Wajib Diisi",
                confirmButtonColor: '#4e73df',
            })
        } else {
            var index = null;
            for (var i = 0; i < listBarang.length; i++) {
                if (listBarang[i].id == id_detail) {
                    index = i;
                    break;
                }
            }

            listBarang[i].remark = remark;
            drawTable();
            $('.detail-modal').modal('hide');
        }
    });

    $('.btn-hide-detail').click(function() {
        $('.detail-modal').modal('hide');
    });

    function getDetailSalesKontrak() {
        var salesContractId = $('#sales_contract_id option:selected').val();

        $.ajax({
            url: `<?= base_url("order-form-internasional/get/detail-sales-kontrak"); ?>`,
            method: "GET",
            dataType: "json",
            beforeSend: () => {
                setLoading()
            },
            complete: () => {
                stopLoading();
            },
            data: {
                sales_contract_id: salesContractId
            }, // Memasukkan id ke dalam data yang dikirim
            success: function(res) {
                listDataSalesKontrak = res.data;
                drawTable(listDataSalesKontrak);
                // DRAW SENSITECH INFORMATION
                $('#consigne').val(listDataSalesKontrak.salesContract.customer_name);
                $('#destination').val(listDataSalesKontrak.salesContract.dicharge_port);
                $('#deadline').val(listDataSalesKontrak.salesContract.shipment_date);
                $('#po_no').val(listDataSalesKontrak.salesContract.po_no);

            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function drawTable(listDataSalesKontrak) {
        const table = $('#dataTableBarang');
        const tbody = table.find('#body-detail-table-barang'); // tbody utama
        const tfoot = table.find('#foot-detail-table-barang');

        tbody.empty();
        tfoot.empty();

        var row = '';
        var no = 1;
        var totalQty = 0;
        var totalHarga = 0;
        var totalTotalHarga = 0;

        if (listDataSalesKontrak.length === 0) {
            row += `
            <tr>
                <td colspan="6"></td>
                <td><b>TOTAL</b></td>
                <td><b>0,00</b></td>
                <td><b>0,00</b></td>
                <td><b>0,00</b></td>
            </tr>
        `;
            $('#foot-detail-table-barang').append(row);
        } else {

            listDataSalesKontrak.salesContractDetailList.map((item, index) => {
                const newRow = $('<tr style="color:whitesmoke;">');
                newRow.append(`<td style="text-align:center;">${no++}</td>`);
                newRow.append(`<td>${item.kode_barang}</td>`);
                newRow.append(`<td>${item.barang_name}</td>`);
                newRow.append(`<td>${item.species}</td>`);
                newRow.append(`<td>${item.brand}</td>`);
                newRow.append(`<td>${item.packing}</td>`);
                newRow.append(`<td>${item.specs}</td>`);
                newRow.append(`<td>${greatFormatRupiah(item.qty_sisa)}</td>`);
                newRow.append(`<td>${greatFormatRupiah(item.harga)}</td>`);
                newRow.append(`<td>${greatFormatRupiah(item.total_sisa)}</td>`);
                tbody.append(newRow);

                // === Row Kedua: Breakdown Table ===
                const detailRow = $('<tr class="bg-light">');
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
                        <th>Qty Order Form</th>
                        <th>Unit Price</th>
                        <th>Total Amount</th>
                        <th>Action</th>
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
                        <td></td>
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
                    let qty = parseFloat(size.qty_sisa) || 0;
                    let harga = parseFloat(size.harga) || 0;
                    let total = parseFloat(size.total_sisa) || 0;
                    let persen = parseFloat(size.persen) || 0;

                    totalQtySize += qty;
                    totalHargaSize += harga;
                    totalAmountSize += total;
                    totalPersenSize += persen;

                    const actionButton = `
                    <?php if (!empty($dataSalesExport)) : ?>
                        <?php if ($dataSalesExport['status'] == "POSTED") : ?>
                            -
                        <?php else : ?>
                            <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRow('${item.id}', '${size.id_detail_breakdown}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRow('${item.id}', '${size.id_detail_breakdown}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    <?php else : ?>
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRow('${item.id}', '${size.id_detail_breakdown}')">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteRow('${item.id}', '${size.id_detail_breakdown}')">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif; ?>
                `;

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
                    <td>${actionButton}</td>
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

                totalQty += parseFloat(item.qty_sisa);
                totalHarga += destroyFormatRupiah(item.harga);
                totalTotalHarga += destroyFormatRupiah(item.total_sisa);

            });

            var additionalPrice = '';

            if (listDataSalesKontrak.royaltyPriceFinal > 0) {
                totalTotalHarga -= listDataSalesKontrak.royaltyPriceFinal;
                additionalPrice += `
                    <tr class="bg-light">
                        <td colspan="6" class="text-end"><b>ROYALTY</b></td>
                        <td><b></b></td>
                        <td><b></b></td>
                        <td>
                            <input type="text" 
                                id="royalty"
                                name="royalty"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value)"
                                data-max="${listDataSalesKontrak.royaltyPriceFinal}"
                                value="${greatFormatRupiah(listDataSalesKontrak.royaltyPriceFinal)}" 
                        </td>
                        <td></td>
                    </tr>
                `;
            }


            if (listDataSalesKontrak.rebatePriceFinal > 0) {
                totalTotalHarga -= listDataSalesKontrak.rebatePriceFinal;
                additionalPrice += `
                    <tr class="bg-light">
                        <td colspan="6" class="text-end"><b>REBATE</b></td>
                        <td><b></b></td>
                        <td><b></b></td>
                        <td>
                            <input type="text" 
                                id="rebate"
                                name="rebate"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value)"
                                data-max="${listDataSalesKontrak.rebatePriceFinal}"
                                value="${greatFormatRupiah(listDataSalesKontrak.rebatePriceFinal)}" 
                        </td>
                        <td></td>
                    </tr>
                `;
            }

            if (listDataSalesKontrak.canDeductionPriceFinal > 0) {
                totalTotalHarga -= listDataSalesKontrak.canDeductionPriceFinal;
                additionalPrice += `
                    <tr class="bg-light">
                        <td colspan="6" class="text-end"><b>CAN DEDUCTION</b></td>
                        <td><b></b></td>
                        <td><b></b></td>
                        <td>
                            <input type="text" 
                                id="can_deduction_price"
                                name="can_deduction_price"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value)"
                                data-max="${listDataSalesKontrak.canDeductionPriceFinal}"
                                value="${greatFormatRupiah(listDataSalesKontrak.canDeductionPriceFinal)}" 
                        </td>
                        <td></td>
                    </tr>
                `;
            }

            if (listDataSalesKontrak.estimatedFreightPriceFinal > 0) {
                totalTotalHarga += listDataSalesKontrak.estimatedFreightPriceFinal;
                additionalPrice += `
                    <tr class="bg-light">
                        <td colspan="6" class="text-end"><b>ESTIMATED FREIGHT</b></td>
                        <td><b></b></td>
                        <td><b></b></td>
                        <td>
                            <input type="text" 
                                id="estimated_freight_price"
                                name="estimated_freight_price"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value)"
                                data-max="${listDataSalesKontrak.estimatedFreightPriceFinal}"
                                value="${greatFormatRupiah(listDataSalesKontrak.estimatedFreightPriceFinal)}" 
                        </td>
                        <td></td>
                    </tr>
                `;
            }

            if (listDataSalesKontrak.othersPriceFinal > 0) {

                if (listDataSalesKontrak.othersTypeFinal == "PLUS") {
                    totalTotalHarga += listDataSalesKontrak.othersPriceFinal;
                } else {
                    totalTotalHarga -= listDataSalesKontrak.othersPriceFinal;
                }

                additionalPrice += `
                    <input type="hidden" value="${listDataSalesKontrak.othersTypeFinal}" name="others_type" id="others_type"/>
                    <tr class="bg-light">
                        <td colspan="6" class="text-end"><b>OTHER PRICE</b></td>
                        <td><b></b></td>
                        <td><b></b></td>
                        <td>
                            <input type="text" 
                                id="others_price"
                                name="others_price"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value)"
                                data-max="${listDataSalesKontrak.othersPriceFinal}"
                                value="${greatFormatRupiah(listDataSalesKontrak.othersPriceFinal)}" 
                        </td>
                        <td></td>
                    </tr>
                `;
            }

            // <td><b>${greatFormatRupiah(totalQty)}</b></td>
            // <td><b>${greatFormatRupiah(totalHarga.toFixed(2))}</b></td>
            // === Footer Total ===
            const totalRow = $(`
                ${additionalPrice}
                <tr class="bg-light">
                    <td colspan="6" class="text-end"><b>TOTAL</b></td>
                    <td><b></b></td>
                    <td><b></b></td>
                    <td><b>${greatFormatRupiah(totalTotalHarga.toFixed(2))}</b></td>
                    <td></td>
                </tr>
            `);
            tfoot.append(totalRow);
        }
    }

    function updateRemark(input) {
        const csrf = $(` [name = "${csrfToken}"] `);
        const remarkId = $(input).attr("data-id") // Ambil ID input
        const remarkValue = $(input).val(); // Ambil nilai remark

        $.ajax({
            url: "<?= base_url("order-form-internasional/update-remark"); ?>",
            data: {
                id: remarkId,
                remark: remarkValue, // Kirim nilai remark
                status: "POSTED"
            },
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        })
                        .then(() => {
                            stopLoading()
                        })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        confirmButtonColor: '#4e73df',
                    })
                    stopLoading()
                }
            }
        });
    }

    function updateOrder(input) {
        var index = input.data('index');
        var qtyInput = input.val() == "" ? 0.0 : parseFloat(input.val()); // Ambil nilai qty yang diinputkan
        var item = listBarang[index];
        var maxQty = parseFloat(item.qty);
        var qty = Math.min(maxQty, qtyInput); // Batasi nilai qty sesuai dengan nilai qty pada listBarang
        var hargaOrder = parseFloat(item.harga);
        var totalHargaOrder = hargaOrder * qty;

        // Update nilai qty di listBarang
        listBarang[index].qtyOrder = qty;
        listBarang[index].hargaOrder = hargaOrder;
        listBarang[index].totalHargaOrder = totalHargaOrder;

        // Update nilai input dengan nilai qty yang sudah dibatasi
        input.val(qty);

        // Update Harga Order dan Total Harga Order
        $('.dataTable tbody tr:eq(' + index + ') td:eq(10)').text(greatFormatRupiah(hargaOrder));
        $('.dataTable tbody tr:eq(' + index + ') td:eq(11)').text(greatFormatRupiah(totalHargaOrder));

        // Recalculate Total
        var totalQty = 0;
        var totalHarga = 0;
        var totalTotalHarga = 0;

        $('.dataTable tbody tr').each(function() {
            var currentQty = parseFloat($(this).find('td:eq(9) input').val());
            var currentHargaOrder = parseFloat($(this).find('td:eq(10)').text().replace('.', ''));
            var currentTotalHargaOrder = parseFloat($(this).find('td:eq(11)').text().replace('.', ''));

            totalQty += currentQty;
            totalHarga += currentHargaOrder;
            totalTotalHarga += currentTotalHargaOrder;
        });

        $('.foot-detail-table td:eq(5)').text(totalQty);
        $('.foot-detail-table td:eq(6)').text(greatFormatRupiah(totalHarga));
        $('.foot-detail-table td:eq(7)').text(greatFormatRupiah(totalTotalHarga));
    }

    function dropdownSalesKontrak() {
        var divisiId = $('#divisi_id option:selected').val();
        var divisiIdText = $('#divisi_id option:selected').text().trim();

        // AJAX DROPDOWN GET SALES CONTRACT
        $.ajax({
            url: "<?= base_url("order-form-internasional/get/sales-kontrak"); ?>",
            data: {
                divisi_id: divisiId,
            },
            method: "GET",
            beforeSend: function(xhr) {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            success: function(res) {
                if (res.status) {
                    $("#sales_contract_id").empty()
                    $("#sales_contract_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $("#sales_contract_id").append(`<option value="${item.id}">${item.sales_contract_no}</option>`)
                    })
                    $("#sales_contract_id").val();
                }
            }
        });

        if (divisiIdText == "PTS") {
            $('#component-detail-specs-list').show();
        } else {
            // HIDE DETAIL SPECS LIST COMPONENT
            // CLEAR DETAIL SPECS LIST
            listDetailSpecs = [];
            // DRAW (SUPAYA KE RESET TABEL NYA)
            $('#component-detail-specs-list').hide();
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
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
            $('#sales_order_export_no').val("AUTO GENERATE");
            $('#sales_order_export_no').attr('readonly', true);
        } else {
            // SET NOMOR AUTO GENERATE FALSE
            $('#sales_order_export_no').val(null);
            $('#sales_order_export_no').attr('readonly', false);
        }

    }
</script>
<?= $this->endSection(); ?>