<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("order-form-internasional"); ?>">
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
                <input type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input type="text" class="form-control sales_order_id" id="sales_order_id" name="sales_order_id" placeholder="No. SO">
                                    <label for="floatingInput">No. Penawaran</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select sales_contract_id" id="sales_contract_id" name="sales_contract_id" aria-label="Floating label select example">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput">No. SC</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="hidden" class="form-control customer_id" id="customer_id" name="customer_id">
                            <input readonly type="text" class="form-control customer" id="customer" name="customer" placeholder="Buyer">
                            <label for="floatingInput">Buyer</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control final_destination" id="final_destination" name="final_destination" placeholder="Final Destination">
                            <label for="floatingInput">Final Destination</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control contract" id="contract" name="contract" placeholder="No. Kontrak">
                            <label for="floatingInput">No. Kontrak</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input class="form-control input-picker expired_date" id="expired_date" name="expired_date" placeholder="Batas Waktu">
                                    <label for="floatingInput">Batas Waktu</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-expired-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select currency" id="currency" name="currency" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php
                                if (!empty($dataValuta)) {
                                    foreach ($dataValuta as $valuta) {
                                ?>
                                        <option value="<?= $valuta["id"]; ?>"><?= $valuta["value"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <label for="floatingInput">Valas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input type="text" class="form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term (Opsional)">
                            <label for="floatingInput">Payment Term (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input readonly value="<?= !empty($dataSO) ? ($dataSO->total_amount ? number_format($dataSO->total_amount) : 0) : ""; ?>" type="text" value="0" class="form-control total_amount" id="total_amount" name="total_amount" placeholder="Grand Total" />
                            <label for="floatingInput">Grand Total</label>
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
            </form>
            <div class="col-subtitle-modal mt-5">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
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
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="2"></td>
                                <td><b>TOTAL</b></td>
                                <td colspan="2"></td>
                                <td><b>0</b></td>
                                <td><b>0</b></td>
                                <td><b>0</b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $(".expired_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        })

        $('.icon-expired-date').click(function() {
            $(".expired_date").focus();
        });

        // CURRENCY
        $('.currency').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.currency')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.currency')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.currency')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // SALES CONTRACT ID
        $('.sales_contract_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.sales_contract_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.sales_contract_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.sales_contract_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');
    })
    
    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".sales_order_id").attr("readonly", true);
            $(".sales_order_id").val("AUTO GENERATE");
        } else {
            $(".sales_order_id").attr("readonly", false);
            $(".sales_order_id").val("");
        }
    }
</script>
<?= $this->endSection(); ?>