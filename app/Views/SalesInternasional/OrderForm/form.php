<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Order Form Internasional</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("order-form-internasional"); ?>">
                Batal
            </a>
            <?php if (!empty($dataSO)) { ?>
                <?php if ($dataSO->status === "NEW") { ?>
                    <button class="btn btn-success posting-spp posting-so float-right">
                        Posting
                    </button>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php } ?>
                <?php if ($dataSO->status === "POSTED") { ?>
                    <button class="btn btn-success posting-spp unposting-so float-right">
                        Unposting
                    </button>
                <?php } ?>
                <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("order-form-internasional/print/"); ?><?= $dataSO->sales_order_export_id; ?>')">
                    Print
                </button>
            <?php } else { ?>

            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" class="id" name="id" id="id" value="<?= !empty($dataSO) ? $dataSO->sales_order_export_id : ""; ?>" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly="true" value="<?= !empty($dataSO) ? $dataSO->sales_order_export_no : ""; ?>" type="text" class="form-control sales_order_export_no" id="sales_order_export_no" name="sales_order_export_no" placeholder="No. SO Export">
                                <label for="floatingInput">No. SO Export</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select disabled="true" class="form-select customer_id" id="customer_id" name="customer_id" aria-label="Floating label select example">
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
                            <input readonly="true" value="<?= !empty($dataSO) ? $dataSO->customer_po_no : ""; ?>" type="text" class="form-control customer_po_no" id="customer_po_no" name="customer_po_no" placeholder="No. PO">
                            <label for="floatingInput">No. PO</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly="true" value="<?= !empty($dataSO) ? $dataSO->loading_port : ""; ?>" type="text" class="form-control loading_port" id="loading_port" name="loading_port" placeholder="Loading Port">
                            <label for="floatingInput">Loading Port</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly="true" value="<?= !empty($dataSO) ? $dataSO->dicharge_port : ""; ?>" type="text" class="form-control dicharge_port" id="dicharge_port" name="dicharge_port" placeholder="Dicharge Port">
                            <label for="floatingInput">Dicharge Port</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input disabled="true" value="<?= !empty($dataSO) ? ($dataSO->due_date ? date("d/m/Y", strtotime($dataSO->due_date)) : "") : ""; ?>" class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Due Date">
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
                            <input readonly="true" value="<?= !empty($dataSO) ? $dataSO->payment_term : ""; ?>" type="text" class="form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term (Opsional)">
                            <label for="floatingInput">Payment Term (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly="true" value="<?= !empty($dataSO) ? $dataSO->tolerance : ""; ?>"  type="text" class="form-control tolerance" id="tolerance" name="tolerance" placeholder="Tolerance">
                            <label for="floatingInput">Tolerance</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly="true" value="<?= !empty($dataSO) ? ($dataSO->shipment_date ? date("d/m/Y", strtotime($dataSO->shipment_date)) : "") : ""; ?>" class="form-control input-picker shipment_date" id="shipment_date" name="shipment_date" placeholder="Shipment Date">
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
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea readonly="true" class="full-textarea form-control documents_required" id="documents_required" name="documents_required" placeholder="Document Required"><?= !empty($dataSO) ? $dataSO->documents_required : ""; ?></textarea>
                            <label for="floatingInput">Document Required</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea readonly="true" class="full-textarea form-control special_instructions" id="special_instructions" name="special_instructions" placeholder="Special Instructions"><?= !empty($dataSO) ? $dataSO->special_instructions : ""; ?></textarea>
                            <label for="floatingInput">Special Instructions</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataSO) ? ($dataSO->status === "POSTED" ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSO) ? $dataSO->director_name : ""; ?>" type="text" class="form-control director_name" id="director_name" name="director_name" placeholder="Nama Direktur">
                            <label for="floatingInput">Nama Direktur</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataSO) ? ($dataSO->status === "POSTED" ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSO) ? $dataSO->marketing_name : ""; ?>" type="text" class="form-control marketing_name" id="marketing_name" name="marketing_name" placeholder="Nama Marketing">
                            <label for="floatingInput">Nama Marketing</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataSO) ? ($dataSO->status === "POSTED" ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSO) ? $dataSO->exim_name : ""; ?>" type="text" class="form-control exim_name" id="exim_name" name="exim_name" placeholder="Nama Exim">
                            <label for="floatingInput">Nama Exim</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataSO) ? ($dataSO->status === "POSTED" ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSO) ? $dataSO->procurement_name : ""; ?>" type="text" class="form-control procurement_name" id="procurement_name" name="procurement_name" placeholder="Nama Procure">
                            <label for="floatingInput">Nama Procure</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataSO) ? ($dataSO->status === "POSTED" ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSO) ? $dataSO->production_name : ""; ?>" type="text" class="form-control production_name" id="production_name" name="production_name" placeholder="Nama Production">
                            <label for="floatingInput">Nama Production</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($dataSO) ? ($dataSO->status === "POSTED" ? 'readonly=true' : '') : ''; ?> value="<?= !empty($dataSO) ? $dataSO->qc_name : ""; ?>" type="text" class="form-control qc_name" id="qc_name" name="qc_name" placeholder="Nama QC">
                            <label for="floatingInput">Nama QC</label>
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
                                    <td><?= $no; ?></td>
                                    <td><?= $details["kode_barang"]; ?></td>
                                    <td><?= $details["nama_barang"]; ?></td>
                                    <td><?= $details["nama_satuan"]; ?></td>
                                    <td><?= $details["remark"]; ?></td>
                                    <td><?= formatter($details["qty"], "STR_TO_INT"); ?></td>
                                    <td><?= number_format(formatter($details["price"], "STR_TO_INT")); ?></td>
                                    <td><?= number_format(formatter($details["total_price"], "STR_TO_INT")); ?></td>
                                </tr>
                            <?php } 
                                    $no++;
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    $(document).ready(function() {
        $(".posting-so").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan di Posting?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Posting',
                cancelButtonText: 'Batal',
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
                                        window.location.href = "<?= base_url("order-form-internasional"); ?>" + "/id/" + $(".id").val()
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
                cancelButtonText: 'Batal',
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
                                        window.location.href = "<?= base_url("order-form-internasional"); ?>" + "/id/" + $(".id").val()
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
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        let data = new FormData(document.querySelector(".create-form"));

                        let id = $(".id").val();
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
                                                window.location.href = "<?= base_url("order-form-internasional"); ?>" + "/id/" + id;
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
        })
    })

    const print = function(url) {
        window.open(url, "_blank");
    }
</script>
<?= $this->endSection(); ?>