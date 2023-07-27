<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("sales-kontrak"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input value="<?= !empty($dataSO) ? $dataSO->sales_contract_no : ""; ?>" type="text" class="form-control sales_contract_no" id="sales_contract_no" name="sales_contract_no" placeholder="No. Sales Contract">
                                    <label for="floatingInput">No. SC</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select customer_id" id="customer_id" name="customer_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataCustomer)) {
                                    foreach ($dataCustomer as $customer) {
                                ?>
                                        <option <?= !empty($dataSO) ? ($dataSO->customer_id === $customer["id"] ? "selected" : "") : ""; ?> value="<?= $customer["id"]; ?>"><?= $customer["name"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Buyer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataSO) ? $dataSO->customer_po_no : ""; ?>" type="text" class="form-control customer_po_no" id="customer_po_no" name="customer_po_no" placeholder="No. PO">
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataSO) ? $dataSO->loading_port : ""; ?>" type="text" class="form-control loading_port" id="loading_port" name="loading_port" placeholder="Loading Port">
                            <label for="floatingInput">Loading Port</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataSO) ? $dataSO->dicharge_port : ""; ?>" type="text" class="form-control dicharge_port" id="dicharge_port" name="dicharge_port" placeholder="Dicharge Port">
                            <label for="floatingInput">Dicharge Port</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input value="<?= !empty($dataSO) ? ($dataSO->due_date ? date("d/m/Y", strtotime($dataSO->due_date)) : "") : ""; ?>" class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Due Date">
                                    <label for="floatingInput">Due Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-due-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataSO) ? $dataSO->payment_term : ""; ?>" type="text" class="form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term (Opsional)">
                            <label for="floatingInput">Payment Term (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="<?= !empty($dataSO) ? $dataSO->tolerance : ""; ?>"  type="text" class="form-control tolerance" id="tolerance" name="tolerance" placeholder="Tolerance">
                            <label for="floatingInput">Tolerance</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input value="<?= !empty($dataSO) ? ($dataSO->shipment_date ? date("d/m/Y", strtotime($dataSO->shipment_date)) : "") : ""; ?>" class="form-control input-picker shipment_date" id="shipment_date" name="shipment_date" placeholder="Shipment Date">
                                    <label for="floatingInput">Shipment Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-shipment-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea class="full-textarea form-control documents_required" id="documents_required" name="documents_required" placeholder="Document Required"><?= !empty($dataSO) ? $dataSO->documents_required : ""; ?></textarea>
                            <label for="floatingInput">Document Required</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea class="full-textarea form-control special_instructions" id="special_instructions" name="special_instructions" placeholder="Special Instructions"><?= !empty($dataSO) ? $dataSO->special_instructions : ""; ?></textarea>
                            <label for="floatingInput">Special Instructions</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input readonly value="<?= !empty($dataSO) ? ($dataSO->total_amount ? number_format($dataSO->total_amount) : 0) : ""; ?>" type="text" value="0" class="form-control total_amount" id="total_amount" name="total_amount" placeholder="Grand Total" />
                            <label for="floatingInput">Grand Total</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal mt-5">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-detail btn-add btn-block float-right" data-btn="detail-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable table-tambah-spp" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Nama Satuan</th>
                                <th>Remark</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Total Harga</th>
                                <th>Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                            <?php
                            $no = 1;
                            $total_harga_barang = 0;
                            $total_qty = 0;
                            $total_harga = 0;
                            
                            if (!empty($dataSO)) {
                                foreach ($dataSODetail as $details) {
                                    $total_harga_barang = $total_harga_barang + ($details["price"] ? formatter($details["price"], "STR_TO_INT") : 0);
                                    $total_qty = $total_qty + ($details["qty"] ? formatter($details["qty"], "STR_TO_INT") : 0);
                                    $total_harga = $total_harga + ($details["total_price"] ? (formatter($details["total_price"], "STR_TO_INT")) : 0);
                            ?>
                                    <tr>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?>"  data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-remark="<?= $details["remark"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_INT"); ?>" data-id="<?= formatter($details["sales_contract_detail_id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $no; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?>"  data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-remark="<?= $details["remark"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_INT"); ?>" data-id="<?= formatter($details["sales_contract_detail_id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["kode_barang"]; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?>"  data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-remark="<?= $details["remark"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_INT"); ?>" data-id="<?= formatter($details["sales_contract_detail_id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["nama_barang"]; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?>"  data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-remark="<?= $details["remark"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_INT"); ?>" data-id="<?= formatter($details["sales_contract_detail_id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["nama_satuan"]; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?>"  data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-remark="<?= $details["remark"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_INT"); ?>" data-id="<?= formatter($details["sales_contract_detail_id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= $details["remark"]; ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?>"  data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-remark="<?= $details["remark"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_INT"); ?>" data-id="<?= formatter($details["sales_contract_detail_id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= number_format(formatter($details["price"], "STR_TO_INT")); ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?>"  data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-remark="<?= $details["remark"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_INT"); ?>" data-id="<?= formatter($details["sales_contract_detail_id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= formatter($details["qty"], "STR_TO_INT"); ?></td>
                                            <td class="edit-table-detail" data-total="<?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?>"  data-barang_id="<?= formatter($details["barang_id"], "STR_TO_INT"); ?>" data-kode_barang="<?= $details["kode_barang"]; ?>" data-nama_barang="<?= $details["nama_barang"]; ?>" data-satuan="<?= formatter($details["unit"], "STR_TO_INT"); ?>" data-remark="<?= $details["remark"]; ?>" data-harga="<?= number_format(formatter($details["price"], "STR_TO_INT")); ?>" data-qty="<?= formatter($details["qty"], "STR_TO_INT"); ?>" data-id="<?= formatter($details["sales_contract_detail_id"], "STR_TO_INT"); ?>" data-row="<?= $no; ?>"><?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?></td>
                                            <td><button onclick='deleteRow("<?= $no; ?>")'>X</button></td>
                                    </tr>


                            <?php
                                    $no++;
                                }
                            } ?>
                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="2"></td>
                                <td><b>TOTAL</b></td>
                                <td colspan="2"></td>
                                <td><b><?= $total_qty; ?></b></td>
                                <td><b><?= number_format($total_harga_barang); ?></b></td>
                                <td><b><?= number_format($total_harga); ?></b></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal detail-modal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label> Barang</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <input type="hidden" class="barang_id" name="barang_id" id="barang_id" />
                    <div class="row">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Barang</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="hidden" class="kode" name="kode" id="kode" />
                                <select class="form-select kode_barang" name="kode_barang" id="kode_barang" aria-label="Floating label select example">
                                    <option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>
                                </select>
                                <label for="floatingInput">Kode Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col mb-3">
                            <h5 class="title-tambah-barang">Data Harga</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan" name="satuan" id="satuan" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input onkeyup="formatNumber(this)" type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" readonly="true" class="form-control total" name="total" id="total" placeholder="Total">
                                <label for="floatingInput">Total</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <textarea class="full-textarea form-control remark" id="remark" name="remark" placeholder="Remark"></textarea>
                                <label for="floatingInput">Remark</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-detail btn-discard mr-3">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <button type="button" class="btn btn-discard delete-detail">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let list_items = [];
    let list_delete = [];
    var row = 0;
    var total_harga_barang = 0;
    var total_qty = 0;
    var total_harga = 0;
    var priceEdit = 0;
    var totalPriceEdit = 0;

    var validator_detail = $(".detail-form").validate({
        rules: {
            kode_barang: {
                required: true
            },
            nama_barang: {
                required: true
            },
            qty: {
                required: true
            },
            satuan: {
                required: true
            },
            harga: {
                required: true
            }
        },
        messages: {
            kode_barang: {
                required: "Kode wajib diisi"
            },
            nama_barang: {
                required: "Nama wajib diisi"
            },
            qty: {
                required: "Qty wajib diisi"
            },
            satuan: {
                required: "Satuan wajib diisi"
            },
            harga: {
                required: "Harga wajib diisi"
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

    $(document).ready(function() {
        $(".due_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $(".shipment_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $('.icon-due-date').click(function() {
            $(".due_date").focus();
        });

        $('.icon-shipment-date').click(function() {
            $(".shipment_date").focus();
        });

        // CUSTOMER
        $('.customer_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.customer_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.customer_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.customer_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // KODE BARANG
        $('.kode_barang').select2({
            placeholder: "Pilih Kode Barang",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content"),
            tags: false,
            allowClear: true
        })

        //CSS SELECT2 FLOATING LABEL
        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.kode_barang')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.kode_barang')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SATUAN
        $('.satuan').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.satuan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.satuan')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.satuan')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $(".create-form").validate({
            rules: {
                sales_contract_no: {
                    required: true
                },
                customer_id: {
                    required: true
                },
                customer_po_no: {
                    required: true
                },
                loading_port: {
                    required: true
                },
                dicharge_port: {
                    required: true,
                },
                due_date: {
                    required: true,
                },
                tolerance: {
                    required: true,
                },
                shipment_date: {
                    required: true,
                },
                documents_required: {
                    required: true,
                },
                special_instructions: {
                    required: true,
                }
            },
            messages: {
                sales_contract_no: {
                    required: "No. SC wajib diisi"
                },
                customer_id: {
                    required: "Buyer wajib diisi"
                },
                customer_po_no: {
                    required: "No. PO wajib diisi"
                },
                loading_port: {
                    required: "Loading Port wajib diisi"
                },
                dicharge_port: {
                    required: "Dicharge Port wajib diisi"
                },
                due_date: {
                    required: "Due Date wajib diisi"
                },
                tolerance: {
                    required: "Tolerance wajib diisi"
                },
                shipment_date: {
                    required: "Shipment Date wajib diisi"
                },
                documents_required: {
                    required: "Dokumen Required wajib diisi"
                },
                special_instructions: {
                    required: "Special Instructions wajib diisi"
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

        $(".btn-submit-detail").click(function() {
            let row_detail = $(".id_detail").val() ? Number($(".id_detail").val()) : 0;
            let barang_id = $(".barang_id").val()
            let kode_barang = $(".kode").val()
            let nama_barang = $(".nama_barang").val()
            let remark = $(".remark").val()
            let nama_satuan = $(".satuan option:selected").text()
            let satuan = $(".satuan option:selected").val()
            let harga = $(".harga").val()
            let qty = $(".qty").val()
            let total = $(".total").val()

            let validate_same = false;

            if (validate_same) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Sudah Ada",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                // update detail
                if (row_detail) {
                    if ($(".detail-form").valid()) {
                        Swal.fire({
                            icon: 'question',
                            title: 'Simpan Data?',
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: 'Simpan',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let new_list_items = []
                                let tag_html = "";
                                let tag_total = "";

                                row = 0;

                                $(".body-detail-table").empty()

                                total_harga_barang = 0;
                                total_qty = 0;
                                total_harga = 0;

                                list_items.map(item => {
                                    if (item.row == row_detail) {
                                        tag_html += `<tr>`;
                                        tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += row + 1;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += kode_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += nama_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += nama_satuan;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += remark;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += qty;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += harga;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += total;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_list_items.push({
                                            id: item.id,
                                            row: row + 1,
                                            barang_id: barang_id,
                                            kode_barang: kode_barang,
                                            nama_barang: nama_barang,
                                            nama_satuan: nama_satuan,
                                            satuan: satuan,
                                            remark: remark,
                                            harga: harga,
                                            qty: qty,
                                            total: total
                                        });

                                        row = row + 1;

                                        total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                        total_qty = total_qty + Number(qty.replaceAll(",", ""));
                                        total_harga = total_harga + Number(total.replaceAll(",", ""));
                                    } else {
                                        tag_html += `<tr>`;
                                        tag_html += `<td class="edit-table-detail"  data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += row + 1;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.kode_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.nama_barang;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.nama_satuan;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.remark;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.qty;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.harga;
                                        tag_html += "</td>";
                                        tag_html += `<td class="edit-table-detail"  data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                                        tag_html += item.total;
                                        tag_html += "</td>";
                                        tag_html += "<td>";
                                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                        tag_html += "</td>";
                                        tag_html += "</tr>";

                                        new_list_items.push(item);

                                        row = row + 1;

                                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                                        total_qty = total_qty + Number(item.qty);
                                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                                    }
                                })

                                list_items = [];

                                list_items = new_list_items;

                                $(".body-detail-table").append(tag_html)

                                $(".foot-detail-table").empty()

                                tag_total += `<tr>`;
                                tag_total += "<td colspan='2'>";
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += "<b>TOTAL</b>";
                                tag_total += "</td>";
                                tag_total += "<td colspan='2'>";
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_qty}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td colspan=''>";
                                tag_total += "</td>";
                                tag_total += "</tr>";

                                $(".total_amount").val(total_harga.toLocaleString());

                                $(".foot-detail-table").append(tag_total);

                                $(".detail-modal").modal("hide")
                            }
                        })
                    }
                }
                // create detail
                else {
                    if ($(".detail-form").valid()) {
                        Swal.fire({
                            icon: 'question',
                            title: 'Simpan Data?',
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: 'Simpan',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                list_items.push({
                                    id: '',
                                    row: row + 1,
                                    barang_id: barang_id,
                                    kode_barang: kode_barang,
                                    nama_barang: nama_barang,
                                    nama_satuan: nama_satuan,
                                    satuan: satuan,
                                    remark: remark,
                                    harga: harga,
                                    qty: qty,
                                    total: total,
                                })

                                total_harga_barang = total_harga_barang + Number(harga.replaceAll(",", ""));
                                total_qty = total_qty + Number(qty.replaceAll(",", ""));
                                total_harga = total_harga + Number(total.replaceAll(",", ""));

                                let tag_html = "";
                                let tag_total = "";

                                tag_html += `<tr>`;
                                tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="" data-row="${row + 1}">`;
                                tag_html += row + 1;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="" data-row="${row + 1}">`;
                                tag_html += kode_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="" data-row="${row + 1}">`;
                                tag_html += nama_barang;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id=" data-row="${row + 1}">`;
                                tag_html += nama_satuan;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="" data-row="${row + 1}">`;
                                tag_html += remark;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="" data-row="${row + 1}">`;
                                tag_html += qty;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="" data-row="${row + 1}">`;
                                tag_html += harga;
                                tag_html += "</td>";
                                tag_html += `<td class="edit-table-detail" data-remark="${remark}" data-total="${total}" data-barang_id="${barang_id}" data-kode_barang="${kode_barang}" data-nama_barang="${nama_barang}" data-satuan="${satuan}" data-harga="${harga}" data-qty="${qty}" data-id="" data-row="${row + 1}">`;
                                tag_html += total;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";
                                $(".body-detail-table").append(tag_html)

                                $(".foot-detail-table").empty()

                                tag_total += `<tr>`;
                                tag_total += "<td colspan='2'>";
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += "<b>TOTAL</b>";
                                tag_total += "</td>";
                                tag_total += "<td colspan='2'>";
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_qty}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td>";
                                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                                tag_total += "</td>";
                                tag_total += "<td colspan=''>";
                                tag_total += "</td>";
                                tag_total += "</tr>";

                                $(".total_amount").val(total_harga.toLocaleString());

                                $(".foot-detail-table").append(tag_total);

                                $(".detail-modal").modal("hide")
                                row = row + 1;
                            }
                        })
                    }
                }
            }
        })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")

            // CHECK IF NO BARANG
            if (list_items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang Tidak Boleh Kosong",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                if ($(".create-form").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            setLoading()
                            let data = new FormData(document.querySelector(".create-form"));
                            var total = 0;

                            let update_list_items = [];

                            if (list_delete.length !== 0) {
                                list_delete.map(obj => {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        unit: obj.satuan ? Number(obj.satuan) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        remark: obj.remark,
                                        total_price: obj.total ? Number(obj.total.replaceAll(",", "")) : 0,
                                        isDeleted: true
                                    })
                                })
                            }

                            list_items.map(obj => {
                                if (obj.id) {
                                    update_list_items.push({
                                        id: obj.id ? Number(obj.id) : 0,
                                        barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        unit: obj.satuan ? Number(obj.satuan) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        remark: obj.remark,
                                        total_price: obj.total ? Number(obj.total.replaceAll(",", "")) : 0,
                                        isDeleted: false
                                    })
                                } else {
                                    update_list_items.push({
                                        id: "",
                                        barang_id: obj.barang_id ? Number(obj.barang_id) : 0,
                                        qty: obj.qty ? Number(obj.qty) : 0,
                                        unit: obj.satuan ? Number(obj.satuan) : 0,
                                        price: obj.harga ? Number(obj.harga.replaceAll(",", "")) : 0,
                                        remark: obj.remark,
                                        total_price: obj.total ? Number(obj.total.replaceAll(",", "")) : 0,
                                        isDeleted: false
                                    })
                                }
                            })

                            data.append("items", JSON.stringify(update_list_items))

                            let id = $(".id").val();
                            // UPDATE
                            if (id) {
                                $.ajax({
                                    url: "<?= base_url("sales-kontrak/update"); ?>",
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
                                                    window.location.href = "<?= base_url("sales-kontrak"); ?>" + "/id/" + id;
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
                                            title: 'Data Gagal Disimpan, coba Lagi',
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                });
                            }
                            // CREATE
                            else {
                                $.ajax({
                                    url: "<?= base_url("sales-kontrak/save"); ?>",
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
                                                    window.location.href = "<?= base_url("sales-kontrak"); ?>" + "/id/" + +response.id;
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
            }
        })

        $(".btn-show-detail").click(function() {
            $(".delete-detail").css('display', 'none');

            $(".title-detail-name").text("Tambah");
            $(".id_detail").val('');

            $(".kode").val('')
            $(".nama_barang").val('')
            $(".qty").val('')
            $(".satuan").val('')
            $(".remark").val('')
            $(".harga").val('')
            $(".total").val('')
            $(".keterangan").val('')

            $.ajax({
                url: `<?= base_url("barang/dropdown/kategori"); ?>`,
                method: "GET",
                dataType: "json",
                data: {
                    kategori: "Barang Jadi"
                },
                success: function(res) {
                    $(".kode_barang").empty();

                    $(".kode_barang").append(`<option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".kode_barang").append(`<option data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    })

                    $(".kode_barang").val("").change();
                }
            })

            $.ajax({
                url: `<?= base_url("satuan/dropdown"); ?>`,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    $(".satuan").empty();

                    $(".satuan").append(`<option value=""></option>`);

                    res.data.forEach(function(item) {
                        $(".satuan").append(`<option value="${item.id}">${item.nama_satuan}</option>`);
                    })

                    $(".satuan").val("").change();
                    $(".detail-modal").modal("show");
                }
            })
        })

        $(".harga, .qty").keyup(function() {
            let harga = $(".harga").val() ? $(".harga").val().replaceAll(",", "") : 0;
            let qty = $(".qty").val() ? parseInt($(".qty").val()) : 0;

            let total = (harga * qty).toLocaleString();
            $(".total").val(total);
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".kode_barang").change(function() {
            if ($(".kode_barang option:selected").val()) {
                let nama = $(".kode_barang option:selected").data("nama") ? $(".kode_barang option:selected").data("nama") : "";
                let satuan = $(".kode_barang option:selected").data("satuan") ? $(".kode_barang option:selected").data("satuan") : "";
                let stok = $(".kode_barang option:selected").data("stok") ? $(".kode_barang option:selected").data("stok") : "";
                let harga = $(".kode_barang option:selected").data("harga") ? $(".kode_barang option:selected").data("harga") : "";
                let barang_id = $(".kode_barang option:selected").data("barang_id") ? $(".kode_barang option:selected").data("barang_id") : "";

                $(".nama_barang").attr("readonly", nama ? true : false);

                $(".kode").val($(".kode_barang option:selected").val());
                $(".nama_barang").val(nama);
                $(".barang_id").val(barang_id);
                $(".satuan").val(satuan).change();
                $(".qty").val(stok);
                $(".harga").val(harga ? Number(harga).toLocaleString() : "");
                $(".total").val(harga || stok ? (Number(harga.replaceAll(",", "")) * stok).toLocaleString() : "");
            } else {
                $(".nama_barang").attr("readonly", false)
                $(".kode").val("");
                $(".nama_barang").val("");
                $(".barang_id").val("");
                $(".satuan").val("").change();
                $(".qty").val("");
                $(".harga").val("");
                $(".total").val("");
            }
        })
    })

    const deleteRow = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                row = 0;

                console.log(list_items)

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;

                list_items.map(item => {
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.nama_satuan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.remark;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.total;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            ...item,
                            row: row + 1
                        });

                        row = row + 1;

                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                    } else {
                        // sent parameter isDelete if have customer id and id
                        if (item.id) {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".foot-detail-table").empty()

                tag_total += `<tr>`;
                tag_total += "<td colspan='2'>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += "<b>TOTAL</b>";
                tag_total += "</td>";
                tag_total += "<td colspan='2'>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    }

    $(document).on('click', '.delete-detail', function() {
        let id = $(".id_detail").val()
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                let new_list_items = []
                let tag_html = "";
                let tag_total = "";

                $(".body-detail-table").empty()

                total_harga_barang = 0;
                total_qty = 0;
                total_harga = 0;

                row = 0;

                console.log(list_items)

                list_items.map(item => {
                    if (item.row != id) {
                        tag_html += `<tr>`;
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += row + 1;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.kode_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.nama_barang;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.nama_satuan;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.remark;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.qty;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.harga;
                        tag_html += "</td>";
                        tag_html += `<td class="edit-table-detail" data-remark="${item.remark}" data-total="${item.total}" data-barang_id="${item.barang_id}" data-kode_barang="${item.kode_barang}" data-nama_barang="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga}" data-qty="${item.qty}" data-id="${item.id}" data-row="${row + 1}">`;
                        tag_html += item.total;
                        tag_html += "</td>";
                        tag_html += "<td>";
                        tag_html += `<button onclick='deleteRow(${row + 1})'>X</button>`;
                        tag_html += "</td>";
                        tag_html += "</tr>";

                        new_list_items.push({
                            ...item,
                            row: row + 1
                        });

                        row = row + 1;

                        total_harga_barang = total_harga_barang + Number(item.harga.replaceAll(",", ""));
                        total_qty = total_qty + Number(item.qty);
                        total_harga = total_harga + Number(item.total.replaceAll(",", ""));
                    } else {
                        // sent parameter isDelete if have customer id and id
                        if (item.id) {
                            list_delete.push(item)
                        }
                    }
                })

                list_items = [];

                list_items = new_list_items;

                $(".body-detail-table").append(tag_html)

                $(".foot-detail-table").empty()

                tag_total += `<tr>`;
                tag_total += "<td colspan='2'>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += "<b>TOTAL</b>";
                tag_total += "</td>";
                tag_total += "<td colspan='2'>";
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_qty}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga_barang.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += `<b>${total_harga.toLocaleString()}</b>`;
                tag_total += "</td>";
                tag_total += "<td>";
                tag_total += "</td>";
                tag_total += "</tr>";

                $(".total_amount").val(total_harga.toLocaleString());

                $(".foot-detail-table").append(tag_total);

                $(".detail-modal").modal("hide")
            }
        })
    })

    $(document).on('click', '.edit-table-detail', function(evt) {
        $(".title-detail-name").text("Update")
        $(".delete-detail").css('display', '');
        let total = $(this).data('total')

        let barang_id = $(this).data('barang_id')
        let kode_barang = $(this).data('kode_barang')
        let nama_barang = $(this).data('nama_barang')
        let satuan = $(this).data('satuan')
        let remark = $(this).data('remark')
        let harga = $(this).data('harga')
        let qty = $(this).data('qty')
        let keterangan = $(this).data('keterangan')
        let rowid = $(this).data('row')
        let id = $(this).data('id')

        validator_detail.resetForm();
        validator_detail.reset();

        $(".id_detail").val(rowid)
        $(".kode").val(kode_barang)
        $(".keterangan").val(keterangan)

        $(".remark").val(remark);

        $.ajax({
            url: `<?= base_url("barang/dropdown/kategori"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                kategori: "Barang Jadi"
            },
            success: function(res) {
                $(".kode_barang").empty();

                $(".kode_barang").append(`<option data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value=""></option>`);

                if (barang_id === "") {
                    $(".kode_barang").append(`<option selected data-barang_id="" data-nama="" data-satuan="" data-stok="" data-harga="" value="${kode_barang}">${kode_barang}</option>`);
                }

                res.data.forEach(function(item) {
                    if (kode_barang === item.kode_barang) {
                        $(".kode_barang").append(`<option selected data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    } else {
                        $(".kode_barang").append(`<option data-barang_id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan_id}" data-stok="${item.stok}" data-harga="${item.harga_barang}" value="${item.kode_barang}">${item.kode_barang} - ${item.nama_barang}</option>`);
                    }
                })
            }
        })

        $(".barang_id").val(barang_id)
        $(".nama_barang").val(nama_barang)

        if (barang_id === "") {
            $(".nama_barang").attr("readonly", false);
        } else {
            $(".nama_barang").attr("readonly", true);
        }

        $.ajax({
            url: `<?= base_url("satuan/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".satuan").empty();

                $(".satuan").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".satuan").append(`<option value="${item.id}">${item.nama_satuan}</option>`);
                })

                $(".satuan").val(satuan).change();
                $(".harga").val(harga)
                $(".qty").val(qty)
                $(".total").val(total)

                $(".detail-modal").modal("show");
            }
        })
    })

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".sales_contract_no").attr("readonly", true);
            $(".sales_contract_no").val("AUTO GENERATE");
        } else {
            $(".sales_contract_no").attr("readonly", false);
            $(".sales_contract_no").val("");
        }
    }
</script>
<?= $this->endSection(); ?>