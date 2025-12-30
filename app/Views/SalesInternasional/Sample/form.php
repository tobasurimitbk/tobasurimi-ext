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

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($dataSample) ? "Update Sample" : "Create Sample" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("sample-ekspor"); ?>">
                Back
            </a>
            <?php if (!empty($dataSample)) { ?>
                <?php if (can('Penjualan Ekspor', 'Sample', 'p')): ?>
                    <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url('sample-ekspor/print/') . encrypt($dataSample['id']) ?>')">
                        Print
                    </button>
                <?php endif; ?>
                <?php if (can('Penjualan Ekspor', 'Sample', 'u')): ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Update
                    </button>
                <?php endif; ?>

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
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Header</label>
                    </div>
                </div>
                <input autocomplete="one-time-code" value="<?= !empty($dataSample) ? encrypt($dataSample['id']) : '' ?>" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($dataSample) ? '' : 'readonly' ?> autocomplete="one-time-code" type="text" value="<?= !empty($dataSample) ? $dataSample['no_sample'] : 'AUTO GENERATE' ?>" class="form-control no_sample" id="no_sample" name="no_sample" placeholder="Sales Order No" required>
                                    <label for="floatingInput">Sample No</label>
                                </div>
                                <div style="<?= !empty($dataSample) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Sample Date" value="<?= !empty($dataSample) ? date('d/m/Y', strtotime($dataSample['tanggal'])) : date('d/m/Y')  ?>">
                                    <label for="floatingInput">Sample Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select
                                    class="form-select customer_id"
                                    aria-label="Floating label select example"
                                    name="customer_id"
                                    id="customer_id">
                                    <option value=""></option>
                                    <?php foreach ($dataCustomer as $d) : ?>
                                        <option value="<?= $d['id'] ?>"
                                            <?= !empty($dataSample['customer_id']) && $dataSample['customer_id'] == $d['id'] ? 'selected' : '' ?>>
                                            <?= "(" . $d['kode'] . ") " . $d['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Delivery To</label>
                            </div>
                            <?php if (can('Penjualan Ekspor', 'Customer', 'c')) : ?>
                                <div class="input-group-append" style="height:50px;">
                                    <?php if (!empty($dataSample)) : ?>

                                    <?php else : ?>
                                        <button class="btn btn-success btn-customer-add" id="btn-customer-add" data-toggle="modal" type="button">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" value="<?= !empty($dataSample) ? $dataSample['delivery'] : '' ?>" type="text" class="form-control delivery" id="delivery" name="delivery" placeholder="Delivery">
                            <label for="floatingInput">Delivery</label>
                        </div>
                    </div> -->
                    <!-- <div class="col-sm-4">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" value="<?= !empty($dataSample) ? $dataSample['attn_no'] : '' ?>" type="text" class="form-control attn_no" id="attn_no" name="attn_no" placeholder="Attn No">
                            <label for="floatingInput">Attn To</label>
                        </div>
                    </div> -->
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control recipient_details" id="recipient_details" name="recipient_details" placeholder="Recipient Details" value="<?= !empty($dataSample['recipient_details']) ? $dataSample['recipient_details'] : "" ?>">
                            <label for="floatingInput">Recipient Details</label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" value="<?= !empty($dataSample) ? $dataSample['approved_by'] : '' ?>" type="text" class="form-control approved_by" id="approved_by" name="approved_by" placeholder="Approved By">
                            <label for="floatingInput">Approved By</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control an" id="an" name="an" placeholder="AN" value="<?= !empty($an) ? $an : "" ?>">
                            <label for="floatingInput">AN</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <input autocomplete="one-time-code" type="text" class="form-control pickup_date" id="pickup_date" name="pickup_date" placeholder="Pickup Date (Optional)" value="<?= !empty($pickupDate) ? $pickupDate : "" ?>">
                            <label for="floatingInput">Pickup Date (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control" id="via" name="via" placeholder="Via (Optional)"><?= !empty($via) ? $via : ""; ?></textarea>
                            <label for="floatingInput">Via (Optional)</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control delivery_address" id="delivery_address" name="delivery_address" placeholder="Delivery Address"><?= !empty($dataSample) ? $dataSample['delivery_address'] : ""; ?></textarea>
                            <label for="floatingInput">Delivery Address (Optional)</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control nb" id="nb" name="nb" placeholder="NB (Optional)"><?= !empty($dataSample) ? $dataSample['nb'] : ""; ?></textarea>
                            <label for="floatingInput">NB (Optional)</label>
                        </div>
                    </div>

                </div>

                <div class="col-subtitle-modal mt-5">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">List Items</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAddBarang" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Add Items
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
                                    <th>Dept</th>
                                    <th>Product</th>
                                    <th>Grade / Spesification</th>
                                    <th>Note</th>
                                    <!-- <th>AN</th>
                                    <th>Pickup Date</th>
                                    <th>Via</th> -->
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Gross Weight (Kg)</th>
                                    <th>Net Weight (Kg)</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-barang" id="body-barang">

                            </tbody>
                            <tfoot class="foot-barang" id="foot-barang">
                                <tr>
                                    <td colspan="10">List Items Empty</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Description Of Notes</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <textarea placeholder="Description Of Notes" class="form-control tiny description_notes" id="description_notes" name="description_notes"><?= !empty($dataSample) ? $dataSample['description_notes'] : '' ?></textarea>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</section>



<div class="modal detail-modal" id="barangModal" tabindex="1">
    <div class="modal-dialog modal-lg" style="min-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-barang"></label> Items</h5>
            </div>
            <form class="create-form-barang" role="form" method="POST">
                <input type="hidden" name="id_barang" id="id_barang">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select
                                    class="form-select barang_master_sales_id"
                                    aria-label="Floating label select example"
                                    name="barang_master_sales_id"
                                    id="barang_master_sales_id">
                                    <option value=""></option>
                                    <?php foreach ($dataBarang as $d) : ?>
                                        <option value="<?= $d['id'] ?>">
                                            <?= $d['barang_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Select Items</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea class="full-textarea form-control grade" id="grade" name="grade" placeholder="Grade / Specification"></textarea>
                                <label for="floatingInput">Grade / Specification</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty" id="qty" name="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_id" name="satuan_id" id="satuan_id">
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d) : ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Select Unit</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select
                                    class="form-select divisi_barang_id"
                                    aria-label="Floating label select example"
                                    name="divisi_barang_id"
                                    id="divisi_barang_id">
                                    <option value=""></option>
                                    <?php foreach ($dataDivisi as $d) : ?>
                                        <option value="<?= $d['id'] ?>"
                                            <?= !empty($dataSample['divisi_id']) && $dataSample['divisi_id'] == $d['id'] ? 'selected' : '' ?>>
                                            <?= $d['divisi'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Select Department</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control berat_kotor" id="berat_kotor" name="berat_kotor" placeholder="Gross Weight (Optional)">
                                <label for="floatingInput">Gross Weight (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control berat_bersih" id="berat_bersih" name="berat_bersih" placeholder="Net Weight (Optional)">
                                <label for="floatingInput">Net Weight (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea class="full-textarea form-control note" id="note" name="note" placeholder="Note Items (Optional)"></textarea>
                                <label for="floatingInput">Note (Optional)</label>
                            </div>
                        </div>

                    </div>

                    <!-- <div class="col-subtitle-modal">
                        <div class="row mt-3 justify-content-end">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold modal-sub-title"></label>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success btn-block float-right" type="button" id="btnAddAditional">
                                    <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i> Add Additional
                                </button>
                            </div>
                        </div>
                    </div> -->

                    <!-- <div class="row">
                        <div style="max-height: 400px; overflow-y: auto;">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="additionalTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 10px;">No</th>
                                            <th>Additional Item</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th style="width: 100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-additional" id="body-additional">
                                    </tbody>
                                    <tfoot class="tfoot-additional" id="tfoot-additional">
                                        <tr>
                                            <td colspan="1"></td>
                                            <td><b style="float: right;">TOTAL</b></td>
                                            <td><b>0.00</b></td>
                                            <td colspan="2"><b></b></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div> -->

                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideBarang">Back</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitBarang">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="additionalItemModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-additional-item"></label> Additional Item</h5>
            </div>
            <form class="create-form-additional-item" role="form" method="POST">
                <input type="hidden" name="id_additional_item" id="id_additional_item">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control additional_item" id="additional_item" name="additional_item" placeholder="Additional Item">
                                <label for="floatingInput">Additional Item (Example: Ice Gel Pack / Dry Ice)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_additional" id="qty_additional" name="qty_additional" placeholder="Qty Additional">
                                <label for="floatingInput">Qty Additional</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_additional" name="satuan_additional" id="satuan_additional">
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d) : ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Select Unit</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideAdditionalItem">Back</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitAdditionalItem">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal addCustomerModal" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Data</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-customer" id="create-form-customer" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe_customer" class="tipe_customer" id="tipe_customer" value="INTERNASIONAL">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Pic Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select country_id" name="country_id" id="country_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($dataCountry)) {
                                        foreach ($dataCountry as $dc) {
                                    ?>
                                            <option value="<?= $dc["id"]; ?>">(<?= $dc["code"]; ?>) <?= $dc['country_name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Country</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <textarea autocomplete="one-time-code" class="form-control address" id="address" name="address"></textarea>
                                <label for="floatingInput">Address (Optional)</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2 btn-discard-customer" id="btn-discard-customer">Back</button>
                <button type="submit" class="btn btn-submit-form btn-submit-customer">Create</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listBarang = [];
    var listAdditional = [];
    var totalBeratBersih = 0;
    var totalBeratKotor = 0;
    var totalQty = 0;


    $(document).ready(function() {
        <?php if (!empty($dataSample)) { ?>
            listBarang = <?= json_encode($dataBarangList) ?>;
            drawTableBarang(listBarang);
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

        $('.customer_id').select2({
            placeholder: "Delivery  ",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.country_id').select2({
            placeholder: "Select Country",
            theme: "bootstrap-5",
            dropdownParent: $('#addCustomerModal')
        }).change(function() {});

        $('.barang_master_sales_id').select2({
            placeholder: "Select Items",
            theme: "bootstrap-5",
            dropdownParent: $('#barangModal')
        }).change(function() {});

        $('.satuan_id').select2({
            placeholder: "Select Unit",
            theme: "bootstrap-5",
            dropdownParent: $('#barangModal')
        }).change(function() {});

        $('.satuan_additional').select2({
            placeholder: "Select Unit",
            theme: "bootstrap-5",
            dropdownParent: $('#additionalItemModal')
        }).change(function() {});

        $('.divisi_barang_id').select2({
            placeholder: "Select Department",
            theme: "bootstrap-5",
            dropdownParent: $('#barangModal')
        }).change(function() {});

        //CSS SELECT2 FLOATING LABEL
        $('.sales_contract_id, .customer_id,.barang_master_sales_id,.satuan_id,.satuan_additional,.divisi_barang_id,.country_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.sales_contract_id, .customer_id,.barang_master_sales_id,.satuan_id,.satuan_additional,.divisi_barang_id,.country_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.sales_contract_id, .customer_id,.barang_master_sales_id,.satuan_id,.satuan_additional,.divisi_barang_id,.country_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');


        var validator = $("#form-parent").validate({
            rules: {
                no_sample: {
                    required: true
                },
                tanggal: {
                    required: true
                },
                customer_id: {
                    required: true
                },
                recipient_details: {
                    required: true
                },
                // attn_no: {
                //     required: true
                // },
                approved_by: {
                    required: true
                },
                delivery_address: {
                    required: true
                },
            },
            messages: {
                no_sample: {
                    required: "No sample required"
                },
                tanggal: {
                    required: "Tanggal required"
                },
                customer_id: {
                    required: "Delivery to required"
                },
                recipient_details: {
                    required: "Recipient details required"
                },
                // attn_no: {
                //     required: "Attn no required"
                // },
                approved_by: {
                    required: "Approved by required"
                },
                delivery_address: {
                    required: "Delivery required"
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

        var validatorAdditionalDetail = $(".create-form-barang").validate({
            rules: {
                barang_master_sales_id: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                grade: {
                    required: true
                },
                // an: {
                //     required: true
                // },
                // pickup_date: {
                //     required: true
                // },
                // via: {
                //     required: true
                // },
                qty: {
                    required: true
                },
                divisi_barang_id: {
                    required: true
                },
                // berat_bersih: {
                //     required: true
                // },
            },
            messages: {
                barang_master_sales_id: {
                    required: "Select items"
                },
                satuan_id: {
                    required: "Unit required"
                },
                grade: {
                    required: "Grade required"
                },
                // an: {
                //     required: "An required"
                // },
                // pickup_date: {
                //     required: "Pickup date required"
                // },
                // via: {
                //     required: "Via required"
                // },
                qty: {
                    required: "Qty required"
                },
                divisi_barang_id: {
                    required: "Department required"
                },
                // berat_kotor: {
                //     required: "Gross weight required"
                // },
                // berat_bersih: {
                //     required: "Net Weight required"
                // },
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

        var validatorCustomer = $("#create-form-customer").validate({
            rules: {
                name: {
                    required: true
                },
                country_id: {
                    required: true
                },
            },
            messages: {
                name: {
                    required: "name required"
                },
                country_id: {
                    required: "country required"
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


        var validatorAdditionalItem = $(".create-form-additional-item").validate({
            rules: {
                additional_item: {
                    required: true
                },
                qty_additional: {
                    required: true
                },
                satuan_additional: {
                    required: true
                },
            },
            messages: {
                additional_item: {
                    required: "Additional item required"
                },
                qty_additional: {
                    required: "Qty additional required"
                },
                satuan_additional: {
                    required: "Unit additional required"
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

        $('#btnSubmitBarang').click(function(e) {
            e.preventDefault();
            if ($('.create-form-barang').valid()) {
                var idBarang = $('#id_barang').val();
                var barangMasterSalesId = $('#barang_master_sales_id option:selected').val();
                var barangMasterSales = $('#barang_master_sales_id option:selected').text();
                var grade = $('#grade').val();
                var an = $('#an').val();
                var pickUpDate = $('#pickup_date').val();
                var via = $('#via').val();
                var qty = destroyFormatRupiah($('#qty').val());
                var kodeSatuan = $('#satuan_id option:selected').text();
                var satuanId = $('#satuan_id option:selected').val();
                var beratKotor = destroyFormatRupiah($('#berat_kotor').val());
                var beratBersih = destroyFormatRupiah($('#berat_bersih').val());
                var note = $('#note').val();
                var divisiBarangId = $('#divisi_barang_id option:selected').val();
                var divisiBarangText = $('#divisi_barang_id option:selected').text();

                if (idBarang) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listBarang.length; i++) {
                        if (listBarang[i].id_barang == idBarang) {
                            index = i;
                            break;
                        }
                    }

                    listBarang[index].id_barang = idBarang;
                    listBarang[index].barang_master_sales_id = barangMasterSalesId;
                    listBarang[index].barang = barangMasterSales;
                    listBarang[index].grade = grade;
                    listBarang[index].an = an;
                    listBarang[index].pickup_date = pickUpDate;
                    listBarang[index].via = via;
                    listBarang[index].qty = qty;
                    listBarang[index].note = note;
                    listBarang[index].kode_satuan = kodeSatuan;
                    listBarang[index].satuan_id = satuanId;
                    listBarang[index].berat_kotor = beratKotor;
                    listBarang[index].berat_bersih = beratBersih;
                    listBarang[index].list_additional = listAdditional;
                    listBarang[index].divisi_barang_id = divisiBarangId;
                    listBarang[index].divisi_barang_text = divisiBarangText;


                } else {
                    // CREATE
                    idBarang = getID();
                    listBarang.push({
                        id_barang: idBarang,
                        barang_master_sales_id: barangMasterSalesId,
                        barang: barangMasterSales,
                        grade: grade,
                        an: an,
                        pickup_date: pickUpDate,
                        via: via,
                        qty: qty,
                        kode_satuan: kodeSatuan,
                        satuan_id: satuanId,
                        berat_kotor: beratKotor,
                        berat_bersih: beratBersih,
                        note: note,
                        list_additional: listAdditional,
                        divisi_barang_id: divisiBarangId,
                        divisi_barang_text: divisiBarangText
                    });
                }

                $('#barangModal').modal('hide');
                drawTableBarang(listBarang);
            }
        });

        $(".btn-submit-parent").click(function() {
            tinymce.triggerSave();

            var id = $('#id').val();
            var description_notes = $('#description_notes').val();

            if (listBarang.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'List items required',
                    confirmButtonColor: '#4e73df',
                })
            } else if (description_notes == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Description notes required',
                    confirmButtonColor: '#4e73df',
                })
            } else {
                let id = $('#id').val();
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
                            let url = id == '' ? "<?= base_url('sample-ekspor/create') ?>" : "<?= base_url('sample-ekspor/update') ?>";
                            data.append("total_berat_bersih", totalBeratBersih);
                            data.append("total_berat_kotor", totalBeratKotor);
                            data.append("total_qty", totalQty);
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
                                                window.location.href = "<?= base_url("sample-ekspor") ?>";
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
    });

    $('#btnHideBarang').click(function() {
        $('#barangModal').modal('hide');
    });

    $('#btnAddBarang').click(function() {
        resetFormBarang();
        $('#label-barang').text('Create ');
        $('#barangModal').modal('show');
        listAdditional = [];
        drawTableAdditionalItem(listAdditional);
    });

    $('#btnAddAditional').click(function(e) {
        e.preventDefault();
        resetFormAdditional();
        $('.label-additional-item').text('Create ');
        $('#additionalItemModal').modal('show');
    });

    $('#btnHideAdditionalItem').click(function(e) {
        e.preventDefault();
        $('#additionalItemModal').modal('hide');
    });

    $('#btnSubmitAdditionalItem').click(function(e) {
        e.preventDefault();
        if ($('.create-form-additional-item').valid()) {
            var id_additional_item = $('#id_additional_item').val();
            var additional_item = $('#additional_item').val();
            var qty_additional = destroyFormatRupiah($('#qty_additional').val());
            var satuan_additional = $('#satuan_additional option:selected').val();
            var satuan_additional_kode = $('#satuan_additional option:selected').text();

            if (id_additional_item) {
                // edit
                var index = null;
                for (var i = 0; i < listAdditional.length; i++) {
                    if (listAdditional[i].id_additional_item == id_additional_item) {
                        index = i;
                        break;
                    }
                }

                listAdditional[index].id_additional_item = id_additional_item;
                listAdditional[index].additional_item = additional_item;
                listAdditional[index].qty_additional = qty_additional;
                listAdditional[index].satuan_additional = satuan_additional;
                listAdditional[index].satuan_additional_kode = satuan_additional_kode;
            } else {
                id_additional_item = getID();
                listAdditional.push({
                    id_additional_item: id_additional_item,
                    additional_item: additional_item,
                    qty_additional: qty_additional,
                    satuan_additional: satuan_additional,
                    satuan_additional_kode: satuan_additional_kode
                });

            }
            $('#additionalItemModal').modal('hide');
            drawTableAdditionalItem(listAdditional);

        }
    });

    $('#btn-customer-add').click(function(e) {
        e.preventDefault();
        resetFormCustomer();
        $('#addCustomerModal').modal('show');
    });

    $('#btn-discard-customer').click(function(e) {
        e.preventDefault();
        $('#addCustomerModal').modal('hide');
    });

    $('.btn-submit-customer').click(function() {
        if ($('.create-form-customer').valid()) {
            const csrf = $(`[name="${csrfToken}"]`);
            const data = new FormData(document.querySelector(".create-form-customer"));
            Swal.fire({
                icon: 'question',
                title: 'Create PIC?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Create',
                cancelButtonText: 'Back',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url("customer-ekspor/save"); ?>",
                        data: data,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            setLoading();
                        },
                        complete: function() {
                            stopLoading();
                        },
                        method: "POST",
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            csrf.val(response.token);
                            $("#addCustomerModal").modal("hide");
                            if (response.status) {
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        $('#addCustomerModal').modal('hide');

                                    })
                                getListCustomer();
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
            })

        }
    });

    function getListCustomer() {
        $.ajax({
            url: `<?= base_url('sample-ekspor/customer'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {},
            dataType: "json",
            success: function(res) {
                $(".customer_id").empty()
                $(".customer_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".customer_id").append(`<option value="${item.id}">(${item.kode}) ${item.name}</option>`)
                })
                $(".customer_id").val();
            }
        });
    }

    function resetFormCustomer() {
        $('#name').val(null);
        $('#country_id').val(null).change();
        $('#address').val(null);
    }

    function resetFormBarang() {
        $('#id_barang').val(null);
        $('#barang_master_sales_id').val(null).change();
        $('#grade').val(null);
        $('#qty').val(null);
        $('#satuan_id').val(null).change();
        $('#berat_kotor').val(null);
        $('#berat_bersih').val(null);
        $('#note').val(null);
        $('#divisi_barang_id').val(null).change();
    }

    function resetFormAdditional() {
        $('#id_additional_item').val(null);
        $('#additional_item').val(null);
        $('#qty_additional').val(null);
        $('#satuan_additional').val(null).change();
    }

    function drawTableBarang(listBarang) {
        const table = $('#barangTable');
        const tbody = table.find('#body-barang');
        const tfoot = table.find('#foot-barang');

        tbody.empty();
        tfoot.empty();

        if (!Array.isArray(listBarang) || listBarang.length === 0) {
            tbody.html(`
            <tr>
                <td colspan="10" class="text-center text-muted">List Items Empty</td>
            </tr>
        `);
            return;
        }

        let htmlRows = [];
        let kode_satuan = '';
        let no = 1;

        for (const item of listBarang) {
            const id_barang = item.id_barang || '';
            const barang = item.barang || '';
            const grade = item.grade || '';
            const note = item.note || '';
            // const an = item.an || '';
            // const pickup_date = item.pickup_date || '';
            // const via = item.via || '';
            const qty = parseFloat(item.qty || 0);
            const berat_kotor = parseFloat(item.berat_kotor || 0);
            const berat_bersih = parseFloat(item.berat_bersih || 0);
            const divisi_barang_text = item.divisi_barang_text;
            kode_satuan = item.kode_satuan || '';

            totalBeratKotor += berat_kotor;
            totalBeratBersih += berat_bersih;

            // Row utama
            htmlRows.push(`
            <tr style="color:whitesmoke;">
                <td style="text-align:center;">${no++}</td>
                <td>${divisi_barang_text}</td>
                <td>${barang}</td>
                <td>${grade}</td>
                <td>${note}</td>
                <td>${greatFormatRupiah(qty)}</td>
                <td>${kode_satuan}</td>
                <td>${greatFormatRupiah(berat_kotor)}</td>
                <td>${greatFormatRupiah(berat_bersih)}</td>
                <td>
                    <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBarang('${id_barang}')">
                        <i class="fa fa-pencil fa-sm"></i>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteRowBarang('${id_barang}')">
                        <i class="fa fa-trash fa-sm"></i>
                    </button>
                </td>
            </tr>
        `);

            // Tambah inner table jika ada list_additional
            if (Array.isArray(item.list_additional) && item.list_additional.length > 0) {
                let additionalRows = item.list_additional.map(add => `
                <tr>
                    <td>${add.additional_item || ''}</td>
                    <td>${greatFormatRupiah((add.qty_additional || 0).toFixed(2))}</td>
                    <td>${add.satuan_additional_kode || ''}</td>
                </tr>
            `).join('');

                htmlRows.push(`
                <tr style="color:whitesmoke;">
                    <td colspan="17">
                        <b>ADDITIONAL ITEM</b><br>
                        <table class="table table-sm table-bordered mb-2 w-100">
                            <thead class="bg-warning text-dark">
                                <tr>
                                    <th>Additional Item</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>${additionalRows}</tbody>
                        </table>
                    </td>
                </tr>
            `);
            }
        }

        // Masukkan semua row sekaligus
        tbody.html(htmlRows.join(''));

        // Footer total
        const totalRow = `
        <tr>
            <td colspan="6"></td>
            <td><b>TOTAL</b></td>
            <td><b>${greatFormatRupiah(totalBeratKotor.toFixed(2))} (Gross Weight)</b></td>
            <td><b>${greatFormatRupiah(totalBeratBersih.toFixed(2))} (Net Weight)</b></td>
            <td></td>
        </tr>
    `;
        tfoot.html(totalRow);
    }



    function drawTableAdditionalItem(listAdditional) {
        const table = $('#additionalTable');
        $('#body-additional').empty();
        $('#tfoot-additional').empty();
        var row = '';
        var no = 1;
        if (listAdditional.length === 0) {
            row += `
                <tr>
                    <td colspan="1"></td>
                    <td><b style="float: right;">TOTAL</b></td>
                    <td><b>0.00</b></td>
                    <td colspan="2"><b></b></td>
                </tr>
                `;
            $('#tfoot-additional').append(row);
        } else {
            var totalQtyAdditional = 0;

            listAdditional.map(item => {
                totalQtyAdditional += parseFloat(item.qty_additional);

                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.additional_item));
                newRow.append($('<td>').text(greatFormatRupiah(item.qty_additional)));
                newRow.append($('<td>').text(item.satuan_additional_kode));
                newRow.append($('<td>').html(`
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowAdditional('${item.id_additional_item}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowAdditional('${item.id_additional_item}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `));

                table.find('tbody').append(newRow);

            });


            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td></td>'));
            newRow.append($('<td><b style="float: right;">TOTAL</b></td>'));
            newRow.append($('<td><b>' + greatFormatRupiah(totalQtyAdditional) + '</b></td>'));
            newRow.append($('<td colspan="2"><b></b></td>'));
            table.find('tfoot').append(newRow);
        }
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
        $('#barang_master_sales_id').val(item.barang_master_sales_id).change();
        $('#grade').val(item.grade);
        $('#an').val(item.an);
        $('#pickup_date').val(item.pickup_date);
        $('#via').val(item.via);
        $('#qty').val(greatFormatRupiah(item.qty));
        $('#satuan_id').val(item.satuan_id).change();
        $('#berat_kotor').val(greatFormatRupiah(item.berat_kotor));
        $('#berat_bersih').val(greatFormatRupiah(item.berat_bersih));
        $('#note').val(item.note);
        $('#divisi_barang_id').val(item.divisi_barang_id).change();

        listAdditional = item.list_additional;
        drawTableAdditionalItem(listAdditional);

        $('#label-barang').text("Update ");
        $('#barangModal').modal('show');
    }

    function detailRowAdditional(id_additional_item) {
        var item = null;
        for (var i = 0; i < listAdditional.length; i++) {
            if (listAdditional[i].id_additional_item == id_additional_item) {
                item = listAdditional[i];
                break;
            }
        }

        $('#id_additional_item').val(item.id_additional_item);
        $('#additional_item').val(item.additional_item);
        $('#qty_additional').val(greatFormatRupiah(item.qty_additional));
        $('#satuan_additional').val(item.satuan_additional).change();

        $('#label-additional-item').text("Update ");
        $('#additionalItemModal').modal('show');
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

    function deleteRowAdditional(id_additional_item) {
        var indexToRemove = -1;
        for (var i = 0; i < listAdditional.length; i++) {
            if (listAdditional[i].id_additional_item == id_additional_item) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listAdditional.splice(indexToRemove, 1);
        }
        drawTableAdditionalItem(listAdditional);
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
            $('#no_sample').val("AUTO GENERATE");
            $('#no_sample').attr('readonly', true);
        } else {
            // SET NOMOR AUTO GENERATE FALSE
            $('#no_sample').val(null);
            $('#no_sample').attr('readonly', false);
        }
    }

    //handle print
    function print(url) {
        window.open(url, "_blank");
    }
</script>
<?= $this->endSection(); ?>