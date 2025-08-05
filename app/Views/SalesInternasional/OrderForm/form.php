<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<style>
    .table-sm th,
    .table-sm td {
        padding: 0.35rem !important;
        vertical-align: middle;
    }

    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
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
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= encrypt($dataSalesExport->sales_order_export_id) ?>')">
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
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data" id="form-parent">
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
                    <div class="col-md-6">
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
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select
                                class="form-select divisi_id"
                                aria-label="Floating label select example"
                                name="divisi_id"
                                <?= !empty($dataSalesExport) ? 'disabled' : '' ?>
                                id="divisi_id">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option <?= !empty($dataSalesExport) ? ($dataSalesExport->divisi_id == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Department</label>
                        </div>
                    </div> -->
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <?php if (!empty($dataSalesExport)) { ?>
                                <select <?= !empty($dataSalesExport) ? 'disabled' : '' ?> class="form-select sales_contract_id" name="sales_contract_id" id="sales_contract_id">
                                    <option selected value="<?= $dataSalesKontrak['id'] ?>"><?= $dataSalesKontrak['sales_contract_no'] ?></option>
                                </select>
                            <?php } ?>
                            <?php if (empty($dataSalesExport)) { ?>
                                <select class="form-select sales_contract_id" name="sales_contract_id" id="sales_contract_id">
                                    <option value=""></option>
                                    <?php foreach ($dataSalesKontrak as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['sales_contract_no'] ?></option>
                                    <?php endforeach; ?>
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
                                    <input <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Sales Order Date" value="<?= !empty($dataSalesExport) ? date('d/m/Y', strtotime($dataSalesExport->tanggal)) : date('d/m/Y')  ?>">
                                    <label for="floatingInput">Sales Order Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker actualy_shipment_date" id="actualy_shipment_date" name="actualy_shipment_date" placeholder="Actually Shipment Date" value="<?= !empty($dataSalesExport) ? ((!empty($dataSalesExport->actualy_shipment_date)) ? date('d/m/Y', strtotime($dataSalesExport->actualy_shipment_date)) : '') : '' ?>">
                                    <label for="floatingInput">Actualy Shipment Date (Optional)</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" value="<?= !empty($dataSalesExport) ? $dataSalesExport->tax_id : '' ?>" type="text" class="form-control tax_id" id="tax_id" name="tax_id" placeholder="Tax Id (Optional)">
                            <label for="floatingInput">Tax ID (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control deadline" id="deadline" name="deadline" placeholder="Deadline" value="<?= !empty($dataSalesExport) ? $dataSalesExport->deadline : '' ?>">
                            <label for="floatingInput">Deadline</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control container" id="container" name="container" placeholder="No. Container (Optional)" value="<?= !empty($dataSalesExport) ? $dataSalesExport->container : '' ?>">
                            <label for="floatingInput">No. Container (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control consigne" id="consigne" name="consigne" placeholder="Consigne / Buyer" readonly value="<?= !empty($dataSalesExport) ? $dataSalesExport->customer_name : '' ?>">
                            <label for="floatingInput">Consigne / Buyer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control destination" id="destination" name="destination" placeholder="Destination" readonly value="<?= !empty($dataSalesExport) ? $dataSalesExport->dicharge_port : '' ?>">
                            <label for="floatingInput">Destination</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="PO No (Opsional)" value="<?= !empty($dataSalesExport) ? $dataSalesExport->po_no : '' ?>">
                            <label for="floatingInput">PO No (Opsional)</label>
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
                                    <th>Department</th>
                                    <th>Species</th>
                                    <th>Brand</th>
                                    <th>Packing</th>
                                    <th>Specs</th>
                                    <th>Action</th>
                                    <!-- <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th> -->
                                </tr>
                            </thead>
                            <tbody id="body-detail-table-barang">

                            </tbody>
                            <tfoot id="foot-detail-table-barang">
                                <tr>
                                    <td colspan="9">Item List Empty</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control commision" id="commision" name="commision" placeholder="Commision (Optional)" value="<?= !empty($dataSalesExport) ? $dataSalesExport->commision : '' ?>">
                            <label for="floatingInput">Commision (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control palet_fumigation" id="palet_fumigation" name="palet_fumigation" placeholder="Pallet & Fumigation (Optional)" value="<?= !empty($dataSalesExport) ? $dataSalesExport->palet_fumigation : '' ?>">
                            <label for="floatingInput">Pallet & Fumigation (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control palet_fumigation_price" id="palet_fumigation_price" name="palet_fumigation_price" value="<?= !empty($dataSalesExport) ? number_format($dataSalesExport->palet_fumigation_price) : '' ?>" placeholder="Pallet & Fumigation Number (Optional)" oninput="this.value = greatFormatRupiah(this.value)">
                            <label for="floatingInput">Pallet & Fumigation Number (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control freight" id="freight" name="freight" placeholder="Freight (Optional)" value="<?= !empty($dataSalesExport) ? $dataSalesExport->freight : '' ?>">
                            <label for="floatingInput">Freight (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control additional" id="additional" name="additional" placeholder="Additional (Optional)" value="<?= !empty($dataSalesExport) ? $dataSalesExport->additional : '' ?>">
                            <label for="floatingInput">Additional 1 (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input <?= !empty($dataSalesExport) ? ($dataSalesExport->status == "POSTED" ? 'readonly' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control additional_2" id="additional_2" name="additional_2" placeholder="Additional 2 (Optional)" value="<?= !empty($dataSalesExport) ? $dataSalesExport->additional_2 : '' ?>">
                            <label for="floatingInput">Additional 2 (Optional)</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= !empty($dataSalesExport) ? $dataSalesExport->additional_detail_docs : '' ?>" type="text" class="form-control additional_detail_docs" id="additional_detail_docs" name="additional_detail_docs" placeholder="Additional Details (Optional)">
                            <label for="floatingInput">Additional Details (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select name="additional_detail_type" id="additional_detail_type" class="form-control additional_detail_type">
                                    <option <?= !empty($dataSalesExport) ? ($dataSalesExport->additional_detail_type == "PLUS" ? 'selected' : '') : '' ?> value="PLUS">PLUS (+)</option>
                                    <option <?= !empty($dataSalesExport) ?  ($dataSalesExport->additional_detail_type == "MINUS" ? 'selected' : '') : '' ?> value="MINUS">MINUS (-)</option>
                                </select>
                            </div>
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="<?= !empty($dataSalesExport) ? number_format($dataSalesExport->additional_detail_price) : '' ?>" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control additional_detail_price" id="additional_detail_price" name="additional_detail_price" placeholder="Additional Detail Price (Optional)">
                                <label for="floatingInput">Additional Details Number (Optional)</label>
                            </div>
                        </div>
                    </div> -->

                </div>


                <div class="col-subtitle-modal mt-5">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">Additional List</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAdditionalList" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add List
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="additionalDetailTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>Additional Details</th>
                                    <th style="width: 120px;">Total Price</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-additional-detail" id="body-additional-detail">

                            </tbody>
                            <tfoot class="foot-additional-detail" id="foot-additional-detail">
                                <tr>
                                    <td colspan="4">List Additional Empty</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>


                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Docs & Certificate Required</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label>Document Required</label>
                        <div class="form-floating mb-3">
                            <textarea placeholder="Document Required" class="form-control tiny document_required" id="document_required" name="document_required"><?= !empty($dataSalesExport) ? $dataSalesExport->document_required : '' ?>"</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Payment Term</label>
                        <div class="form-floating mb-3">
                            <textarea placeholder="Payment Term" class="form-control tiny payment_term" id="payment_term" name="payment_term"><?= !empty($dataSalesExport) ? $dataSalesExport->payment_term : '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Shipment A/N (Optional)</label>
                        <div class="form-floating mb-3">
                            <textarea class="form-control tiny shipment_an" id="shipment_an" name="shipment_an" placeholder="Shipment A/N (Optional)"><?= !empty($dataSalesExport) ? $dataSalesExport->shipment_an : '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Consigne (Optional)</label>
                        <div class="form-floating mb-3">
                            <textarea class="form-control tiny consigne_docs" id="consigne_docs" name="consigne_docs" placeholder="Consign (Optional)"><?= !empty($dataSalesExport) ? $dataSalesExport->consigne_docs : '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Notify Party (Optional)</label>
                        <div class="form-floating mb-3">
                            <textarea class="form-control tiny notify_party" id="notify_party" name="notify_party" placeholder="Notify Party (Optional)"><?= !empty($dataSalesExport) ? $dataSalesExport->notify_party : '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Additional Details (Optional)</label>
                        <div class="form-floating mb-3">
                            <textarea class="form-control tiny additional_detail_docs" id="additional_detail_docs" name="additional_detail_docs" placeholder="Additional Details (Optional)"><?= !empty($dataSalesExport) ? $dataSalesExport->additional_detail_docs : '' ?></textarea>
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
                        <label>Product Specs (Optional)</label>
                        <textarea class="form-control tiny product_specs" placeholder="Product Specs (Optional)" name="product_specs" id="product_specs"><?= !empty($dataSalesExport) ? $dataSalesExport->product_specs : '' ?></textarea>
                    </div>

                    <div class="col-sm-6 mt-2 mb-3">
                        <label>Processing Method (Optional)</label>
                        <textarea class="form-control tiny processing_method" placeholder="Processing Method (Optional)" name="processing_method" id="processing_method"><?= !empty($dataSalesExport) ? $dataSalesExport->processing_method : '' ?></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label>Packaging (Optional)</label>
                        <textarea class="form-control tiny packaging" placeholder="Packaging (Optional)" name="packaging" id="packaging"><?= !empty($dataSalesExport) ? $dataSalesExport->packaging : '' ?></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label>Code Stamping (Optional)</label>
                        <textarea class="form-control tiny code_stamping" placeholder="Code Stamping (Optional)" name="code_stamping" id="code_stamping"><?= !empty($dataSalesExport) ? $dataSalesExport->code_stamping : '' ?></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label>Loading (Optional)</label>
                        <textarea class="form-control tiny loading" placeholder="Loading (Optional)" name="loading" id="loading"><?= !empty($dataSalesExport) ? $dataSalesExport->loading : '' ?></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label>Foto Loading (Optional)</label>
                        <textarea class="form-control tiny foto_loading" placeholder="Foto Loading (Optional)" name="foto_loading" id="foto_loading"><?= !empty($dataSalesExport) ? $dataSalesExport->foto_loading : '' ?></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label>Stuffing (Optional)</label>
                        <textarea class="form-control tiny stuffing" placeholder="Stuffing (Optional)" name="stuffing" id="stuffing"><?= !empty($dataSalesExport) ? $dataSalesExport->stuffing : '' ?></textarea>
                    </div>

                    <div class="col-sm-6 mt-3 mb-3">
                        <label>Additional Details (Optional)</label>
                        <textarea class="form-control tiny additional_detail" placeholder="Additional Details (Optional)" name="additional_detail" id="additional_detail"><?= !empty($dataSalesExport) ? $dataSalesExport->additional_detail : '' ?></textarea>
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
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="detailSpecsListTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 10px;">No</th>
                                        <th>Size / Packing</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-detail-specs" id="body-detail-specs">

                                </tbody>
                                <tfoot class="foot-detail-specs" id="foot-detail-specs">
                                    <tr>
                                        <td colspan="3">List Size / Packing Empty</td>
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
                    <input type="hidden" name="id_detail" id="id_detail">
                    <input type="hidden" name="id_detail_breakdown" id="id_detail_breakdown">
                    <div class="row mt-1">
                        <div class="col mb-3">
                            <h6 class="<?= session()->get('theme') == "dark" ? "text-white" : "text-dark" ?>">
                                Price & Qty Order Form
                            </h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input oninput="preventNegativeInput(this);validateMaxPrice(this);" autocomplete="one-time-code" type="text" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                    <label for="floatingInput">Qty Order Form</label>
                                </div>
                                <div class="input-group-append">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select disabled class="form-select satuan_size_id" name="satuan_size_id" id="satuan_size_id">
                                            <option value=""></option>
                                            <?php foreach ($dataSatuan as $d) : ?>
                                                <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Unit Order Form</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input oninput="preventNegativeInput(this);" autocomplete="one-time-code" type="text" class="form-control qty_convertion" name="qty_convertion" id="qty_convertion" placeholder="Qty Order Form Convertion In Kg">
                                    <label for="floatingInput">Qty Order Form Convertion In Kg</label>
                                </div>
                                <div class="input-group-append">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select disabled class="form-select satuan_convertion_id" name="satuan_convertion_id" id="satuan_convertion_id">
                                            <option value=""></option>
                                            <?php foreach ($dataSatuan as $d) : ?>
                                                <option <?= $d['kode_satuan'] == "KG" ? 'selected' : '' ?> value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="floatingInput" style="z-index: 1;">Unit Convertion</label>
                                    </div>
                                </div>
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

                    <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home1" type="button" role="tab" aria-controls="home" aria-selected="true">
                                Add Note Size & Breakdown
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile1" type="button" role="tab" aria-controls="profile" aria-selected="false">
                                Detail Size & Breakdown
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home1" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_size" name="note_size" id="note_size" placeholder="Note Size (Optional)">
                                        <label for="floatingInput">Note Size (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_grade" name="note_grade" id="note_grade" placeholder="Note Grade (Optional)">
                                        <label for="floatingInput">Note Grade (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_packing_size" name="note_packing_size" id="note_packing_size" placeholder="Note Packing (Optional)">
                                        <label for="floatingInput">Note Packing (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_can" name="note_can" id="note_can" placeholder="Note Can (Optional)">
                                        <label for="floatingInput">Note Can (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_case" name="note_case" id="note_case" placeholder="Note Case (Optional)">
                                        <label for="floatingInput">Note Case (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_kg" name="note_kg" id="note_kg" placeholder="Note Kg (Optional)">
                                        <label for="floatingInput">Note Kg (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_lb" name="note_lb" id="note_lb" placeholder="Note Lb (Optional)">
                                        <label for="floatingInput">Note Lb (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_inner_box" name="note_inner_box" id="note_inner_box" placeholder="Note Inner Box (Optional)">
                                        <label for="floatingInput">Note Inner Box (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_pc" name="note_pc" id="note_pc" placeholder="Note Pc (Optional)">
                                        <label for="floatingInput">Note PC (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_bag" name="note_bag" id="note_bag" placeholder="Note Bag (Optional)">
                                        <label for="floatingInput">Note Bag (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_persen" name="note_persen" id="note_persen" placeholder="Note Persen (Optional)">
                                        <label for="floatingInput">Note Percentage % (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_cup" name="note_cup" id="note_cup" placeholder="Note Cup (Optional)">
                                        <label for="floatingInput">Note Cup (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control note_palet" name="note_palet" id="note_palet" placeholder="Note Pallet (Optional)">
                                        <label for="floatingInput">Note Pallet (Optional)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile-tab">
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
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideSizeBreakdownModal">Back</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitSizeBreakDown">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>




<div class="modal detail-modal" id="detailSpecsModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-detail-specs"></label> Detail Specs List</h5>
            </div>
            <form class="create-form-detail-specs-list" role="form" method="POST">
                <input type="hidden" name="id_detail_specs_list" id="id_detail_specs_list">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control size_packing" name="size_packing" id="size_packing" placeholder="Size / Packing">
                                <label for="floatingInput">Size / Packing</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-subtitle-modal">
                        <div class="row mt-3 justify-content-end">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title"></label>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success btn-block float-right" type="button" id="btnAddGradeSpecsDetail">
                                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add Grade
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top: -20px;">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="detailTableGradeSpecs" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width: 10px;">No</th>
                                        <th>Grade</th>
                                        <th>Specification</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="body-grade-specs" id="body-grade-specs">

                                </tbody>
                                <tfoot class="foot-grade-specs" id="foot-grade-specs">
                                    <tr>
                                        <td colspan="4">List Grade & Specs Empty</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnDetailSpecsHide">Back</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitSpecsDetail">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="gradeSpecsModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-grade-specification"></label> Grade & Specification</h5>
            </div>
            <form class="create-form-grade-specs" role="form" method="POST">
                <input type="hidden" name="id_grade_specs" id="id_grade_specs">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control grade_pts" name="grade_pts" id="grade_pts" placeholder="Grade">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideGradeSpecs">Back</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitGradeSpecs">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal detail-modal" id="additionalDetailModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-detail-additional"></label> Additional List</h5>
            </div>
            <form class="create-form-additional" role="form" method="POST">
                <input type="hidden" name="id_additional_detail" id="id_detail_additional">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control additional_detail_docs2" id="additional_detail_docs2" name="additional_detail_docs2" placeholder="Additional Details">
                                <label for="floatingInput">Additional Details</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select name="additional_detail_type" id="additional_detail_type" class="form-control additional_detail_type">
                                        <option value="PLUS">PLUS (+)</option>
                                        <option value="MINUS">MINUS (-)</option>
                                    </select>
                                </div>
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" oninput="this.value = greatFormatRupiah(this.value)" class="form-control additional_detail_price" id="additional_detail_price" name="additional_detail_price" placeholder="Additional Detail Price">
                                    <label for="floatingInput">Additional Details Number</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideAdditional">Back</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitAdditional">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" tabindex="1" id="productSubtitleModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Update Product Subtitle</h5>
            </div>
            <form class="form-product-subtitle">
                <div class="modal-body">
                    <input type="hidden" name="id_detail_subtitle" class="id_detail_subtitle" id="id_detail_subtitle">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" readonly value="" type="text" class="form-control product_name" id="product_name" name="product_name" placeholder="Product Name">
                                <label for="floatingInput">Product Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="" type="text" class="form-control brand" id="brand" name="brand" placeholder="Brand">
                                <label for="floatingInput">Brand (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="" type="text" class="form-control packing" id="packing" name="packing" placeholder="Packing">
                                <label for="floatingInput">Packing (Opsional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="" type="text" class="form-control species" id="species" name="species" placeholder="Species (Optional)">
                                <label for="floatingInput">Species (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" value="" type="text" class="form-control specs" id="specs" name="specs" placeholder="Specs (Optional)">
                                <label for="floatingInput">Specs (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select divisi_id" name="divisi_id" id="divisi_id">
                                    <option value=""></option>
                                    <?php foreach ($dataDivisi as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['divisi'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Select Department</label>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3" id="btn-hide-subtitle">Back</button>
                    <button type="button" class="btn btn-submit-form" id="btn-submit-subtitle">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal print-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Print Configuration</h5>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form">
                                <label>
                                    Display Price in Printout ? (If Active, Price Show in Printout OrderForm)
                                </label>
                                <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;<?= session()->get('theme') == 'dark' ? 'background-color:#474D54' : '' ?>">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input display_price" type="checkbox" value="1" name="display_price" id="display_price">
                                        <label class="form-check-label"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (session()->get('login')->this_company_id == 1): ?>
                            <div class="col-md-12 mt-3">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select
                                        class="form-select company_id"
                                        aria-label="Floating label select example"
                                        name="company_id"
                                        id="company_id">
                                        <option value=""></option>
                                        <?php foreach ($dataCompany as $d) : ?>
                                            <option value="<?= $d['id'] ?>" <?= $d['id'] == 1 ? 'selected' : '' ?>>
                                                <?= $d['company'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Select Company Head In Printout</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard btn-hide-print mr-3">Back</button>
                    <button type="button" class="btn btn-submit-form" onclick="printAction()">Print Order Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal unpost-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Unposting Order Form</h5>
            </div>
            <form class="form-unposting">
                <div class="modal-body">
                    <input type="hidden" name="id" class="id" id="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control date_revision" name="date_revision" id="date_revision" placeholder="Date Revision">
                                <label for="floatingInput">Date Revision</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control keterangan_unpost" name="keterangan_unpost" id="keterangan_unpost" placeholder="Keterangan Unpost (Opsional)">
                                <label for="floatingInput">Note Unposting (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-3">Back</button>
                    <button type="button" onclick="unPosting()" class="btn btn-submit-form btn-submit-detail">Un Posting</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listDataSalesKontrak = [];
    var listDetailSpecs = [];
    var listAdditional = [];
    var listGradeSpecs = [];
    // HIDE DETAIL SPECS LIST
    // $('#component-detail-specs-list').hide();


    $(document).ready(function() {
        <?php if (!empty($dataSalesExport)) { ?>
            listDataSalesKontrak = <?= json_encode($dataSalesExportDetail) ?>;
            listDetailSpecs = <?= json_encode($dataSalesExportSpecs) ?>;

            <?php foreach ($dataSalesExportAdditional as $s): ?>
                listAdditional.push({
                    id_detail_additional: "<?= $s['id'] ?>",
                    additional_detail: "<?= $s['additional_detail'] ?>",
                    additional_detail_type: "<?= $s['additional_detail_type'] ?>",
                    additional_detail_price: "<?= $s['additional_detail_price'] ?>",
                });
            <?php endforeach ?>

            drawTable(listDataSalesKontrak);
            drawTableDetailSpecs(listDetailSpecs);
            drawTableAdditionalList(listAdditional);
        <?php } else { ?>

        <?php } ?>


        <?php if (!empty($dataSalesExport)): ?>
            <?php if ($dataSalesExport->divisi == "PTS"): ?>
                $('#component-detail-specs-list').show();
            <?php endif; ?>
        <?php endif; ?>

        // Sales Kontrak
        $('.sales_contract_id').select2({
            placeholder: "Select Sales Kontrak",
            theme: "bootstrap-5"
        }).change(function() {
            listBarang = [];
            getDetailSalesKontrak();
        });

        $("#tanggal,#actualy_shipment_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $(".date_revision").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $('.divisi_id').select2({
            placeholder: "Select Department",
            theme: "bootstrap-5",
            dropdownParent: $('#productSubtitleModal')
        }).change(function() {});

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

        $(".btn-hide-detail").click(function() {
            $(".keterangan_unpost").val("");
            $(".unpost-modal").modal("hide");
        });

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

        var validatorDetailSpecs = $(".create-form-detail-specs-list").validate({
            rules: {
                size_packing: {
                    required: true
                },
            },
            messages: {
                size_packing: {
                    required: "Size / Packing required"
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

        var validatorGradeSpecs = $(".create-form-grade-specs").validate({
            rules: {
                grade_pts: {
                    required: true
                },
                specification: {
                    required: true
                },
            },
            messages: {
                grade_pts: {
                    required: "Grade Required"
                },
                specification: {
                    required: "Specification Required"
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

        var validatorQtyOrderForm = $(".create-form-size-breakdown").validate({
            rules: {
                qty: {
                    required: true
                },
                qty_convertion: {
                    required: true
                },
                satuan_convertion_id: {
                    required: true
                },
            },
            messages: {
                qty: {
                    required: "Qty order form required"
                },
                qty_convertion: {
                    required: "Qty convertion in kg required"
                },
                satuan_convertion_id: {
                    required: "Unit convertion must be in kg"
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

        var validatorAdditionalDetail = $(".create-form-additional").validate({
            rules: {
                additional_detail_docs2: {
                    required: true
                },
                additional_detail_price: {
                    required: true
                },
            },
            messages: {
                additional_detail_docs2: {
                    required: "Additional detail required"
                },
                additional_detail_price: {
                    required: "Additional detail price required"
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

        var validatorProductSubtitle = $(".form-product-subtitle").validate({
            rules: {
                divisi_id: {
                    required: true
                },
            },
            messages: {
                divisi_id: {
                    required: "Department for product subtitle required"
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


        $('#btnSubmitSpecsDetail').click(function(e) {
            e.preventDefault();
            if ($('.create-form-detail-specs-list').valid()) {
                var idDetailSpecList = $('#id_detail_specs_list').val();
                var sizePacking = $('#size_packing').val();
                var state = true;

                if (listGradeSpecs.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'List Grade & Specification Required',
                        confirmButtonColor: '#4e73df',
                    });

                    state = false;
                }

                if (state) {
                    if (idDetailSpecList) {
                        // UPDATE
                        var index = null;
                        for (var i = 0; i < listDetailSpecs.length; i++) {
                            if (listDetailSpecs[i].id_detail_specs_list == idDetailSpecList) {
                                index = i;
                                break;
                            }
                        }

                        listDetailSpecs[index].size_packing = sizePacking;
                        listDetailSpecs[index].grade_specs = listGradeSpecs;

                    } else {
                        // CREATE
                        idDetailSpecList = getID();
                        listDetailSpecs.push({
                            id_detail_specs_list: idDetailSpecList,
                            size_packing: sizePacking,
                            grade_specs: listGradeSpecs
                        });
                    }

                    $('#detailSpecsModal').modal('hide');
                    drawTableDetailSpecs(listDetailSpecs);
                }


            }
        });

        $('#btnSubmitGradeSpecs').click(function(e) {
            e.preventDefault();
            if ($('.create-form-grade-specs').valid()) {
                var idGradeSpecs = $('#id_grade_specs').val();
                var grade_pts = $('#grade_pts').val();
                var specification = $('#specification').val();

                if (idGradeSpecs) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listGradeSpecs.length; i++) {
                        if (listGradeSpecs[i].id_grade_specs == idGradeSpecs) {
                            index = i;
                            break;
                        }
                    }

                    listGradeSpecs[index].grade = grade_pts;
                    listGradeSpecs[index].specification = specification;

                } else {
                    // Create
                    idGradeSpecs = getID();
                    listGradeSpecs.push({
                        id_grade_specs: idGradeSpecs,
                        grade: grade_pts,
                        specification: specification
                    });
                }

                drawTableGradeSpecification(listGradeSpecs);
                $('#gradeSpecsModal').modal('hide');
            }
        });

        $('#btnSubmitAdditional').click(function(e) {
            e.preventDefault();
            if ($('.create-form-additional').valid()) {
                var idAdditionalDetail = $('#id_detail_additional').val();
                var additionalDetail = $('#additional_detail_docs2').val();
                var additionalDetailType = $('#additional_detail_type').val();
                var additionalDetailPrice = destroyFormatRupiah($('#additional_detail_price').val());

                if (idAdditionalDetail) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listAdditional.length; i++) {
                        if (listAdditional[i].id_detail_additional == idAdditionalDetail) {
                            index = i;
                            break;
                        }
                    }

                    listAdditional[index].additional_detail = additionalDetail;
                    listAdditional[index].additional_detail_type = additionalDetailType;
                    listAdditional[index].additional_detail_price = additionalDetailPrice;
                } else {
                    // CREATE
                    idAdditionalDetail = getID();
                    listAdditional.push({
                        id_detail_additional: idAdditionalDetail,
                        additional_detail: additionalDetail,
                        additional_detail_type: additionalDetailType,
                        additional_detail_price: additionalDetailPrice
                    });
                }

                $('#additionalDetailModal').modal('hide');
                drawTableAdditionalList(listAdditional);
            }
        });

        $('#btn-submit-subtitle').click(function(e) {
            e.preventDefault();

            if ($('.form-product-subtitle').valid()) {

                var idSubtitle = $('#id_detail_subtitle').val();
                var listSalesContractDetail = listDataSalesKontrak.salesContractDetailList;
                var indexSalesKontrakDetail = 0;
                for (var i = 0; i < listSalesContractDetail.length; i++) {
                    if (listSalesContractDetail[i].id == idSubtitle) {
                        indexSalesKontrakDetail = i;
                        break;
                    }
                }

                var brand = $('#brand').val();
                var packing = $('#packing').val();
                var species = $('#species').val();
                var specs = $('#specs').val();
                var divisiId = $('#divisi_id option:selected').val();
                var divisiName = $('#divisi_id option:selected').text();

                listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].brand = brand;
                listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].packing = packing;
                listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].species = species;
                listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].specs = specs;
                listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].divisi_id = divisiId;
                listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].divisi_name = divisiName;

                drawTable(listDataSalesKontrak);
                $('#productSubtitleModal').modal('hide');
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

                                data.append("listDetailSpecs", JSON.stringify(listDetailSpecs));
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

        // Detail Size
        $('#id_detail').val(itemSalesContractDetail.id);
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
        // Note Size
        $('#note_size').val(itemSizeBreakdown.note_size);
        $('#note_grade').val(itemSizeBreakdown.note_grade);
        $('#note_packing_size').val(itemSizeBreakdown.note_packing_size);
        $('#note_can').val(itemSizeBreakdown.note_can);
        $('#note_case').val(itemSizeBreakdown.note_case);
        $('#note_kg').val(itemSizeBreakdown.note_kg);
        $('#note_lb').val(itemSizeBreakdown.note_lb);
        $('#note_inner_box').val(itemSizeBreakdown.note_inner_box);
        $('#note_pc').val(itemSizeBreakdown.note_pc);
        $('#note_bag').val(itemSizeBreakdown.note_bag);
        $('#note_persen').val(itemSizeBreakdown.note_persen);
        $('#note_cup').val(itemSizeBreakdown.note_cup);
        $('#note_palet').val(itemSizeBreakdown.note_palet);
        // Qty
        $('#qty').data('max', itemSizeBreakdown.qty_sisa);
        $('#qty').val(itemSizeBreakdown.qty_input);
        $('#harga').val(greatFormatRupiah(itemSizeBreakdown.harga)).attr('readonly', true);
        $('#total').val(greatFormatRupiah(itemSizeBreakdown.total_input)).attr('readonly', true);
        $('#satuan_size_id').val(itemSizeBreakdown.satuan_size_id).change();
        if (itemSizeBreakdown.satuan_size_id == 29 && itemSizeBreakdown.qty_convertion == 0) {
            // KALAU SATUAN KG OTOMATIS SET SATUAN KONVERSI KG YHA
            $('#qty_convertion').val(itemSizeBreakdown.qty_input);

        } else {
            $('#qty_convertion').val(itemSizeBreakdown.qty_convertion);

        }
        // $('#satuan_convertion_id').val(itemSizeBreakdown.satuan_convertion_id).change();
        $('#updateQtyOrderFormModal').modal('show');

    }

    $('#btnSubmitSizeBreakDown').click(function() {
        if ($('.create-form-size-breakdown').valid()) {
            var qty_input = $('#qty').val();
            var id_detail = $('#id_detail').val();
            var id_detail_breakdown = $('#id_detail_breakdown').val();
            var total_input = destroyFormatRupiah($('#total').val());
            var qty_convertion = destroyFormatRupiah($('#qty_convertion').val());
            var satuan_convertion_id = $('#satuan_convertion_id option:selected').val();
            var satuan_convertion_kode = $('#satuan_convertion_id option:selected').text();

            var indexSalesKontrakDetail = null;
            var indexSizeBreakDown = null;
            var listSalesContractDetail = listDataSalesKontrak.salesContractDetailList;
            for (var i = 0; i < listSalesContractDetail.length; i++) {
                if (listSalesContractDetail[i].id == id_detail) {
                    indexSalesKontrakDetail = i;
                    break;
                }
            }

            for (var i = 0; i < listSalesContractDetail[indexSalesKontrakDetail].size_breakdown.length; i++) {
                if (listSalesContractDetail[indexSalesKontrakDetail].size_breakdown[i].id_detail_breakdown == id_detail_breakdown) {
                    indexSizeBreakDown = i;
                    break;
                }
            }

            // Ubah Qty Input nya
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].qty_input = qty_input;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].total_input = total_input;
            // Update Qty Convertion nya
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].qty_convertion = qty_convertion;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].satuan_convertion_id = satuan_convertion_id;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].satuan_convertion_kode = satuan_convertion_kode;


            var noteSize = $('#note_size').val();
            var noteGrade = $('#note_grade').val();
            var notePackingSize = $('#note_packing_size').val();
            var noteCan = $('#note_can').val();
            var noteCase = $('#note_case').val();
            var noteKg = $('#note_kg').val();
            var noteLb = $('#note_lb').val();
            var noteInnerBox = $('#note_inner_box').val();
            var notePc = $('#note_pc').val();
            var noteBag = $('#note_bag').val();
            var notePersen = $('#note_persen').val();
            var noteCup = $('#note_cup').val();
            var notePalet = $('#note_palet').val();
            // Update Note Size
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_size = noteSize;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_grade = noteGrade;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_packing_size = notePackingSize;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_can = noteCan;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_case = noteCase;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_kg = noteKg;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_lb = noteLb;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_inner_box = noteInnerBox;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_pc = notePc;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_bag = noteBag;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_persen = notePersen;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_cup = noteCup;
            listDataSalesKontrak.salesContractDetailList[indexSalesKontrakDetail].size_breakdown[indexSizeBreakDown].note_palet = notePalet;
            drawTable(listDataSalesKontrak);
            recalculateGrandTotal();
            $('#updateQtyOrderFormModal').modal('hide');

        }

    });

    $('#btnHideSizeBreakdownModal').click(function() {
        $('#updateQtyOrderFormModal').modal('hide');
    });

    $('#btnAddDetailSpecs').click(function() {
        resetFormDetailSpecs();
        $('#label-detail-specs').text("Create ");
        $('#detailSpecsModal').modal('show');
    });

    $('#btnDetailSpecsHide').click(function() {
        $('#detailSpecsModal').modal('hide');
    });

    $('#btnAddGradeSpecsDetail').click(function() {
        resetFormGradeSpecsModal();
        $('#gradeSpecsModal').modal('show');
    });

    $('#btnHideGradeSpecs').click(function() {
        $('#gradeSpecsModal').modal('hide');
    });

    $('#qty').keyup(function() {
        var harga = destroyFormatRupiah($('#harga').val() || 0);
        var qty = parseFloat($('#qty').val());
        var total = harga * qty;

        $('#total').val(greatFormatRupiah(total));
    });

    $('.btn-hide-detail').click(function() {
        $('.detail-modal').modal('hide');
    });

    $('#btnAdditionalList').click(function() {
        resetFormAdditional();
        $('#label-detail-additional').text('Create ');
        $('#additionalDetailModal').modal('show');
    });

    $('#btnHideAdditional').click(function() {
        $('#additionalDetailModal').modal('hide');
    });

    $('#btn-hide-subtitle').click(function() {
        $('#productSubtitleModal').modal('hide');
    });

    function resetFormDetailSpecs() {
        $('#id_detail_specs_list').val(null);
        $('#size_packing').val(null);
        listGradeSpecs = [];
        drawTableGradeSpecification(listGradeSpecs);
    }

    function resetFormGradeSpecsModal() {
        $('#id_grade_specs').val(null);
        $('#grade_pts').val(null);
        $('#specification').val(null);
    }

    function resetFormAdditional() {
        $('#id_detail_additional').val(null);
        $('#additional_detail_docs2').val(null);
        $('#additional_detail_type').val("PLUS").change();
        $('#additional_detail_price').val(null);
    }

    function getDetailSalesKontrak() {
        var salesContractId = $('#sales_contract_id option:selected').val();
        var id = $('#id').val();

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
                sales_contract_id: salesContractId,
                id: id,
            }, // Memasukkan id ke dalam data yang dikirim
            success: function(res) {
                listDataSalesKontrak = res.data;
                drawTable(listDataSalesKontrak);
                // DRAW SENSITECH INFORMATION
                $('#consigne').val(listDataSalesKontrak.salesContract.customer_name);
                $('#destination').val(listDataSalesKontrak.salesContract.dicharge_port);
                // $('#deadline').val(listDataSalesKontrak.salesContract.shipment_date);
                $('#po_no').val(listDataSalesKontrak.salesContract.po_no);
                $('#document_required').val(listDataSalesKontrak.salesContract.documents_required);
                $('#payment_term').val(listDataSalesKontrak.salesContract.payment_term);
            },
            error: function(xhr, status, error) {}
        });
    }

    function drawTable(listDataSalesKontrak) {
        console.log(listDataSalesKontrak);
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
                <td colspan="9">Item List Empty</td>
            </tr>
        `;
            $('#foot-detail-table-barang').append(row);
        } else {

            listDataSalesKontrak.salesContractDetailList.map((item, index) => {
                const actionButtonSubtitle = `
                    <?php if (!empty($dataSalesExport)) : ?>
                        <?php if ($dataSalesExport->status == "POSTED") : ?>
                            -
                        <?php else : ?>
                            <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowSubtitle('${item.id}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    <?php else : ?>
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowSubtitle('${item.id}')">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif; ?>
                `;


                const newRow = $('<tr style="color:whitesmoke;">');
                newRow.append(`<td style="text-align:center;">${no++}</td>`);
                newRow.append(`<td>${item.kode_barang}</td>`);
                newRow.append(`<td>${item.barang_name}</td>`);
                newRow.append(`<td>${item.divisi_name}</td>`);
                newRow.append(`<td>${item.species}</td>`);
                newRow.append(`<td>${item.brand}</td>`);
                newRow.append(`<td>${item.packing}</td>`);
                newRow.append(`<td>${item.specs}</td>`);
                newRow.append(`<td>${actionButtonSubtitle}</td>`)
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
                        <th>Qty Order Form Convertion (Kg)</th>
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
                        <td class="total-qty-convertion"><b>0.00</b></td>
                        <td class="total-price"><b>0.00</b></td>
                        <td class="total-amount"><b>0.00</b></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        `);

                const breakdownBody = innerTable.find('tbody');
                let totalQtySize = 0;
                let totalQtyConvertion = 0;
                let totalHargaSize = 0;
                let totalAmountSize = 0;
                let totalPersenSize = 0;

                item.size_breakdown.forEach(size => {
                    let qty = parseFloat(size.qty_input) || 0;
                    let qty_convertion = parseFloat(size.qty_convertion) || 0;
                    let harga = parseFloat(size.harga) || 0;
                    let total = parseFloat(size.total_input) || 0;
                    let persen = parseFloat(size.persen) || 0;

                    totalQtySize += qty;
                    totalQtyConvertion += qty_convertion;
                    totalHargaSize += harga;
                    totalAmountSize += total;
                    totalPersenSize += persen;

                    const actionButton = `
                    <?php if (!empty($dataSalesExport)) : ?>
                        <?php if ($dataSalesExport->status == "POSTED") : ?>
                            -
                        <?php else : ?>
                            <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRow('${item.id}', '${size.id_detail_breakdown}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button>
                        <?php endif; ?>
                    <?php else : ?>
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRow('${item.id}', '${size.id_detail_breakdown}')">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button>
                    <?php endif; ?>
                `;

                    var classCss = '';
                    var styleColor = '';

                    if ((size.satuan_convertion_id == null || size.satuan_convertion_id == "") && size.qty != 0) {
                        classCss = 'bg-danger text-white';
                        styleColor = 'color: white !important;font-weight:bold;';
                    }


                    const row = `
                <tr class="${classCss}" style="${styleColor}">
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
                    <td>${greatFormatRupiah(qty.toFixed(2))+" "+size.satuan_size_code}</td>
                    <td>${greatFormatRupiah(qty_convertion.toFixed(2))+" "+size.satuan_convertion_kode}</td>
                    <td>${greatFormatRupiah(harga.toFixed(2))}</td>
                    <td>${greatFormatRupiah(total.toFixed(2))}</td>
                    <td>${actionButton}</td>
                </tr>
            `;
                    breakdownBody.append(row);
                });

                innerTable.find('.total-persen').html(`<b>${totalPersenSize == 0 ? "" : greatFormatRupiah(totalPersenSize.toFixed(2))}</b>`);
                innerTable.find('.total-qty').html(`<b>${greatFormatRupiah(totalQtySize.toFixed(2))}</b>`);
                innerTable.find('.total-qty-convertion').html(`<b>${greatFormatRupiah(totalQtyConvertion.toFixed(2))}</b>`);
                innerTable.find('.total-price').html(`<b>${greatFormatRupiah(totalHargaSize.toFixed(2))}</b>`);
                innerTable.find('.total-amount').html(`<b>${greatFormatRupiah(totalAmountSize.toFixed(2))}</b>`);

                detailRow.append(`<td colspan="9"><b>SIZE & BREAKDOWN</b><br>${innerTable.prop('outerHTML')}</td>`);
                tbody.append(detailRow);

                totalQty += parseFloat(item.qty_input);
                totalHarga += destroyFormatRupiah(item.harga);
                totalTotalHarga += destroyFormatRupiah(item.total_input);

            });


            var additionalPrice = '';

            if (listDataSalesKontrak.royaltyPriceFinal > 0) {
                totalTotalHarga -= listDataSalesKontrak.royaltyPriceFinal;
                additionalPrice += `
                    <tr class="bg-light">
                        <td colspan="6" class="text-end"><b>ROYALTY (${listDataSalesKontrak.salesContract.royalty})</b></td>
                        <td colspan="2">
                            <input type="text" 
                                id="royalty_price"
                                name="royalty_price"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value);recalculateGrandTotal();validateMaxPrice(this)"
                                data-max="${listDataSalesKontrak.royaltyPriceMax}"
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
                        <td colspan="6" class="text-end"><b>REBATE (${listDataSalesKontrak.salesContract.rebate})</b></td>
                        <td colspan="2">
                            <input type="text" 
                                id="rebate_price"
                                name="rebate_price"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value);recalculateGrandTotal();validateMaxPrice(this)"
                                data-max="${listDataSalesKontrak.rebatepriceMax}"
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
                        <td colspan="6" class="text-end"><b>CAN DEDUCTION (${listDataSalesKontrak.salesContract.can_deduction})</b></td>
                        <td colspan="2">
                            <input type="text" 
                                id="can_deduction_price"
                                name="can_deduction_price"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value);recalculateGrandTotal();validateMaxPrice(this)"
                                data-max="${listDataSalesKontrak.canDeductionPriceMax}"
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
                        <td colspan="6" class="text-end"><b>ESTIMATED FREIGHT (${listDataSalesKontrak.salesContract.estimated_freight})</b></td>
                        <td colspan="2">
                            <input type="text" 
                                id="estimated_freight_price"
                                name="estimated_freight_price"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value);recalculateGrandTotal();validateMaxPrice(this)"
                                data-max="${listDataSalesKontrak.estimatedFreightPriceMax}"
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
                        <td colspan="6" class="text-end"><b>OTHER PRICE (${listDataSalesKontrak.salesContract.others})</b></td>
                        <td colspan="2">
                            <input type="text" 
                                id="others_price"
                                name="others_price"
                                class="form-control form-control-sm"
                                oninput="this.value = greatFormatRupiah(this.value);recalculateGrandTotal();validateMaxPrice(this)"
                                data-max="${listDataSalesKontrak.othersPriceMax}"
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
                    <td style="text-align:left;" colspan="2"><b id="total_txt">${greatFormatRupiah(totalTotalHarga)}</b></td>
                    <td></td>
                </tr>
            `);
            tfoot.append(totalRow);
        }
    }

    function drawTableDetailSpecs(listDetailSpecs) {
        var row = '';
        var no = 1;
        const table = $('#detailSpecsListTable');
        const tbody = table.find('#body-detail-specs');
        const tfoot = table.find('#foot-detail-specs');

        tbody.empty();
        tfoot.empty();

        if (listDetailSpecs.length === 0) {
            row += `
                    <tr>
                        <td colspan="4">List Size / Packing Empty</td>
                    </tr>
                `;
            $('#foot-detail-specs').append(row);
        } else {
            listDetailSpecs.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.size_packing));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataSalesExport)) : ?> <?php if ($dataSalesExport->status == "POSTED") : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowSpecs('${item.id_detail_specs_list}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowSpecs('${item.id_detail_specs_list}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowSpecs('${item.id_detail_specs_list}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowSpecs('${item.id_detail_specs_list}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));
                tbody.append(newRow);

                // Baris Kedua
                // === Row Kedua: Breakdown Table ===
                const detailRow = $('<tr class="bg-light">');
                const innerTable = $(`
                    <table class="table table-sm table-bordered mb-2 w-100">
                        <thead class="bg-warning text-dark">
                            <tr>
                                <th>Grade</th>
                                <th>Specification</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                `);

                const breakdownBody = innerTable.find('tbody');
                item.grade_specs.forEach(grade => {
                    const row = `
                        <tr>
                            <td>${grade.grade || ''}</td>
                            <td>${grade.specification || ''}</td>
                        </tr>
                    `;
                    breakdownBody.append(row);
                });

                detailRow.append(`<td colspan="3"><b>GRADE & SPECS</b><br>${innerTable.prop('outerHTML')}</td>`);
                tbody.append(detailRow);
            });
        }
    }


    function drawTableGradeSpecification(listGradeSpecs) {
        $('#body-grade-specs').empty();
        $('#foot-grade-specs').empty();
        var row = '';
        var no = 1;
        const table = $('#detailTableGradeSpecs');
        if (listGradeSpecs.length === 0) {
            row += `
                    <tr>
                        <td colspan="4">List Grade & Specs Empty</td>
                    </tr>
                `;
            $('#foot-grade-specs').append(row);
        } else {
            listGradeSpecs.map(item => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.grade));
                newRow.append($('<td>').text(item.specification));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataSalesExport)) : ?> <?php if ($dataSalesExport->status == "POSTED") : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowGradeSpecs('${item.id_grade_specs}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowGradeSpecs('${item.id_grade_specs}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowGradeSpecs('${item.id_grade_specs}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowGradeSpecs('${item.id_grade_specs}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                table.find('tbody').append(newRow);
            });
        }
    }

    function drawTableAdditionalList(listAdditional) {
        $('#body-additional-detail').empty();
        $('#foot-additional-detail').empty();
        var row = '';
        var no = 1;
        const table = $('#additionalDetailTable');
        if (listAdditional.length === 0) {
            row += `
                    <tr>
                        <td colspan="4">List Additional Empty</td>
                    </tr>
                `;
            $('#foot-additional-detail').append(row);
        } else {
            listAdditional.map(item => {
                var iconOperator = item.additional_detail_type == "PLUS" ? "(+)" : "(-)";
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.additional_detail));
                newRow.append($('<td>').text(iconOperator + " " + greatFormatRupiah(item.additional_detail_price)));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataSalesExport)) : ?> <?php if ($dataSalesExport->status == "POSTED") : ?> `-`
                        <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowAdditional('${item.id_detail_additional}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowAdditional('${item.id_detail_additional}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                        <?php endif; ?>

                    <?php else : ?> `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowAdditional('${item.id_detail_additional}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowAdditional('${item.id_detail_additional}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                    <?php endif; ?>
                ));

                table.find('tbody').append(newRow);
            });
        }
    }

    function detailRowSubtitle(id) {
        var itemSalesContractDetail = null;
        var listSalesContractDetail = listDataSalesKontrak.salesContractDetailList;

        for (var i = 0; i < listSalesContractDetail.length; i++) {
            if (listSalesContractDetail[i].id == id) {
                itemSalesContractDetail = listSalesContractDetail[i];
                break;
            }
        }

        $('#id_detail_subtitle').val(itemSalesContractDetail.id);
        $('#product_name').val(itemSalesContractDetail.barang_name);
        $('#brand').val(itemSalesContractDetail.brand);
        $('#packing').val(itemSalesContractDetail.packing);
        $('#species').val(itemSalesContractDetail.species);
        $('#specs').val(itemSalesContractDetail.specs);
        $('#divisi_id').val(itemSalesContractDetail.divisi_id).change();

        $('#productSubtitleModal').modal('show');

    }

    function detailRowSpecs(id_detail_specs_list) {
        var item = null;
        for (var i = 0; i < listDetailSpecs.length; i++) {
            if (listDetailSpecs[i].id_detail_specs_list == id_detail_specs_list) {
                item = listDetailSpecs[i];
                break;
            }
        }

        $('#id_detail_specs_list').val(item.id_detail_specs_list);
        $('#size_packing').val(item.size_packing);
        listGradeSpecs = item.grade_specs;
        drawTableGradeSpecification(listGradeSpecs);

        $('#label-detail-specs').text("Update ");
        $('#detailSpecsModal').modal('show');
    }

    function detailRowGradeSpecs(id_grade_specs) {
        var item = null;
        for (var i = 0; i < listGradeSpecs.length; i++) {
            if (listGradeSpecs[i].id_grade_specs == id_grade_specs) {
                item = listGradeSpecs[i];
                break;
            }
        }

        $('#id_grade_specs').val(item.id_grade_specs);
        $('#grade_pts').val(item.grade);
        $('#specification').val(item.specification);

        $('#label-grade-specification').text("Update ");
        $('#gradeSpecsModal').modal('show');
    }

    function deleteRowGradeSpecs(id_grade_specs) {
        var indexToRemove = -1;
        for (var i = 0; i < listGradeSpecs.length; i++) {
            if (listGradeSpecs[i].id_grade_specs == id_grade_specs) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listGradeSpecs.splice(indexToRemove, 1);
        }
        drawTableGradeSpecification(listGradeSpecs);
    }

    function deleteRowSpecs(id_detail_specs_list) {
        var indexToRemove = -1;
        for (var i = 0; i < listDetailSpecs.length; i++) {
            if (listDetailSpecs[i].id_detail_specs_list == id_detail_specs_list) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listDetailSpecs.splice(indexToRemove, 1);
        }
        drawTableDetailSpecs(listDetailSpecs);
    }

    function detailRowAdditional(id_detail_additional) {
        var item = null;
        for (var i = 0; i < listAdditional.length; i++) {
            if (listAdditional[i].id_detail_additional == id_detail_additional) {
                item = listAdditional[i];
                break;
            }
        }

        $('#id_detail_additional').val(item.id_detail_additional);
        $('#additional_detail_docs2').val(item.additional_detail);
        $('#additional_detail_type').val(item.additional_detail_type);
        $('#additional_detail_price').val(greatFormatRupiah(item.additional_detail_price));

        $('#label-detail-additional').text("Update ");
        $('#additionalDetailModal').modal('show');
    }

    function deleteRowAdditional(id_detail_additional) {
        var indexToRemove = -1;
        for (var i = 0; i < listAdditional.length; i++) {
            if (listAdditional[i].id_detail_additional == id_detail_additional) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listAdditional.splice(indexToRemove, 1);
        }
        drawTableAdditionalList(listAdditional);
    }


    function recalculateGrandTotal() {
        var total = 0;
        listDataSalesKontrak.salesContractDetailList.map((item, index) => {
            item.size_breakdown.forEach(size => {
                total += parseFloat(size.total_input) || 0;
            })
        });

        var royaltyPrice = destroyFormatRupiah($('#royalty_price').val() || 0);
        var rebatePrice = destroyFormatRupiah($('#rebate_price').val() || 0);
        var canDeductionPrice = destroyFormatRupiah($('#can_deduction_price').val());
        var estimatedFreightPrice = destroyFormatRupiah($('#estimated_freight_price').val());
        var othersPrice = destroyFormatRupiah($('#others_price').val());

        if (royaltyPrice > 0) {
            total -= royaltyPrice;
        }

        if (rebatePrice > 0) {
            total -= rebatePrice;
        }

        if (canDeductionPrice > 0) {
            total -= canDeductionPrice;
        }

        if (estimatedFreightPrice > 0) {
            total += estimatedFreightPrice;
        }

        if (listDataSalesKontrak.othersTypeFinal == "PLUS") {
            total += othersPrice;
        } else {
            total -= othersPrice;
        }


        $('#total_txt').text(greatFormatRupiah(total));
    }

    function validateMaxPrice(elementx) {
        var element = $(elementx);
        var maxPrice = element.data('max');
        var userInput = destroyFormatRupiah(element.val());

        if (userInput > maxPrice) {
            element.val(greatFormatRupiah(maxPrice));
        }
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

    // function dropdownSalesKontrak() {
    //     var divisiId = $('#divisi_id option:selected').val();
    //     var divisiIdText = $('#divisi_id option:selected').text().trim();

    //     // AJAX DROPDOWN GET SALES CONTRACT
    //     $.ajax({
    //         url: "<?= base_url("order-form-internasional/get/sales-kontrak"); ?>",
    //         data: {
    //             divisi_id: divisiId,
    //         },
    //         method: "GET",
    //         beforeSend: function(xhr) {
    //             setLoading();
    //         },
    //         complete: function() {
    //             stopLoading();
    //         },
    //         success: function(res) {
    //             if (res.status) {
    //                 $("#sales_contract_id").empty()
    //                 $("#sales_contract_id").append(`<option value=""></option>`)
    //                 res.data.forEach(function(item) {
    //                     $("#sales_contract_id").append(`<option value="${item.id}">${item.sales_contract_no}</option>`)
    //                 })
    //                 $("#sales_contract_id").val();
    //             }
    //         }
    //     });

    //     // if (divisiIdText == "PTS") {
    //     //     $('#component-detail-specs-list').show();
    //     // } else {
    //     //     // HIDE DETAIL SPECS LIST COMPONENT
    //     //     // CLEAR DETAIL SPECS LIST
    //     //     listDetailSpecs = [];
    //     //     // DRAW (SUPAYA KE RESET TABEL NYA)
    //     //     $('#component-detail-specs-list').hide();
    //     // }
    // }

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