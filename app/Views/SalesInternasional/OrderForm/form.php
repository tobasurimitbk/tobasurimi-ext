<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Order Form Internasional</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("order-form-internasional"); ?>">
                Kembali
            </a>
            <?php if (!empty($dataSalesExport)) { ?>
                <?php if ($dataSalesExport->status === "NEW") { ?>
                    <button class="btn btn-success posting-spp posting-so float-right">
                        Posting
                    </button>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php } ?>
                <?php if ($dataSalesExport->status === "POSTED") { ?>
                    <button class="btn btn-success posting-spp unposting-so float-right">
                        Unposting
                    </button>
                <?php } ?>
                <button class="btn btn-warning btn-print float-right">
                    Print
                </button>
            <?php } else { ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" value="<?= $id ?? "" ?>" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" <?= !empty($dataSalesExport) ? 'readonly' : '' ?> class="form-control no_sales_order" id="no_sales_order" name="no_sales_order" placeholder="No. Sales Order" required <?= !empty($dataSalesExport) ? 'disabled value="' . $dataSalesExport->sales_order_export_no . '"' : '' ?>>
                                    <label for="floatingInput">No. Order</label>
                                </div>
                                <div style="<?= !empty($dataSalesExport) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <?php if (!empty($dataSalesExport)) { ?>
                                <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->sales_contract_id ?>" type="hidden" class="form-control sales_kontrak_id" id="sales_kontrak_id" name="sales_kontrak_id" placeholder="Sales Kontrak">
                                <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->sales_contract_no ?>" type="text" class="form-control sales_kontrak_no" id="sales_kontrak_no" name="sales_kontrak_no" placeholder="Sales Kontrak">
                            <?php } ?>
                            <?php if (empty($dataSalesExport)) { ?>
                                <select class="form-select sales_kontrak" name="sales_kontrak" id="sales_kontrak">
                                    <option value=""></option>
                                </select>
                            <?php } ?>
                            <label for="floatingInput">Sales Kontrak</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select <?= !empty($dataSalesExport) ? 'disabled' : ''; ?> class="form-select aju_document_type" id="aju_document_type" name="aju_document_type" aria-label="Floating label select example">

                                <?php foreach ($dataAJU as $aju) : ?>
                                    <option selected value="<?= $aju["id"]; ?>"><?= $aju["value"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Dokumen Pabean </label>
                        </div>
                        <!-- <small class="mb-3"><i>Kosongkan jika non pabean</i></small> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->customer_name ?? "" ?>" type="text" class="form-control customer_name" id="customer_name" name="customer_name" placeholder="Customer Name">
                                <label for="floatingInput">Customer</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->customer_po_no ?? "" ?>" type="text" class="form-control customer_po_no" id="customer_po_no" name="customer_po_no" placeholder="No. PO">
                            <label for="floatingInput">No. PO (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->loading_port ?? "" ?>" type="text" class="form-control loading_port" id="loading_port" name="loading_port" placeholder="Loading Port">
                            <label for="floatingInput">Loading Port</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->dicharge_port ?? "" ?>" type="text" class="form-control dicharge_port" id="dicharge_port" name="dicharge_port" placeholder="Dicharge Port">
                            <label for="floatingInput">Dicharge Port</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->due_date ?? "" ?>" class="form-control input-picker due_date" id="due_date" name="due_date" placeholder="Due Date">
                                    <label for="floatingInput">Due Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-due-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->payment_term ?? "" ?>" type="text" class="form-control payment_term" id="payment_term" name="payment_term" placeholder="Payment Term (Opsional)">
                            <label for="floatingInput">Payment Term (Opsional)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->tolerance ?? "" ?>" type="text" class="form-control tolerance" id="tolerance" name="tolerance" placeholder="Tolerance">
                            <label for="floatingInput">Tolerance</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" value="<?= $dataSalesExport->shipment_date ?? "" ?>" class="form-control input-picker shipment_date" id="shipment_date" name="shipment_date" placeholder="Shipment Date">
                                    <label for="floatingInput">Shipment Date</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 10px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-shipment-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly oninput="preventNegativeInput(this)" value="<?= $dataSalesExport->currencyName ?? "" ?>" autocomplete="one-time-code" type="text" class="form-control currency" id="currency" name="currency" placeholder="Currency">
                            <label for="floatingInput">Currency</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly oninput="preventNegativeInput(this)" value="<?= $dataSalesExport->tipe_harga ?? "" ?>" autocomplete="one-time-code" type="text" class="form-control tipe_harga" id="tipe_harga" name="tipe_harga" placeholder="Tipe Harga">
                            <label for="floatingInput">Price Type</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly oninput="preventNegativeInput(this)" value="<?= $dataSalesExport->potongan_harga ?? "" ?>" autocomplete="one-time-code" type="text" class="form-control potongan_harga" id="potongan_harga" name="potongan_harga" placeholder="Potongan Harga (Opsional)">
                            <label for="floatingInput">Potongan Harga (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" value="<?= $dataSalesExport->keterangan ?? "" ?>" class="form-control keterangan" id="keterangan" name="keterangan" placeholder="Keterangan (Opsional)">
                            <label for="floatingInput">Keterangan (Opsional)</label>
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
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th width="1%">No</th>
                                <th width="5%">Kode Barang</th>
                                <th width="5%">Barang</th>
                                <th width="5%">Satuan Order</th>
                                <th width="5%">Kemasan</th>
                                <th width="5%">Remark</th>
                                <th width="5%">Qty</th>
                                <th width="5%">Harga</th>
                                <th width="5%">Total Harga</th>
                                <th width="5%">Qty Order</th>
                                <th width="5%">Harga Order</th>
                                <th width="5%">Total Harga Order</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td colspan="5"></td>
                                <td><b>TOTAL</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
                                <td><b>0.00</b></td>
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
    var listBarang = [];
    var totalAmount = 0;

    $(document).ready(function() {
        <?php if (!empty($dataSalesExport)) { ?>
            <?php if (!empty($dataSalesExportDetail)) { ?>
                <?php foreach ($dataSalesExportDetail as $sales) { ?>
                    listBarang.push({
                        barang_master_sales_id: "<?= $sales->barang_id ?>",
                        barang_name: "<?= $sales->barang_name ?>",
                        harga: "<?= $sales->hargaContract ?>",
                        id_detail_sales_order: "<?= $sales->sales_order_export_detail_id ?>",
                        kemasan: "<?= $sales->kemasan ?>",
                        kode_barang: "<?= $sales->barang_kode ?>",
                        qty: "<?= $sales->qtyContract  ?>",
                        remark: "<?= $sales->remark ?>",
                        satuan_order_id: "<?= $sales->satuan_id ?>",
                        satuan_order_name: "<?= $sales->kode_satuan ?>",
                        total: "<?= $sales->totalHargaContract ?>",
                        qtyOrder: "<?= $sales->qty ?>",
                        hargaOrder: "<?= $sales->harga_barang ?>",
                        totalHargaOrder: "<?= $sales->total_harga_barang ?>",
                    });
                <?php } ?>
                drawTable();
            <?php } ?>
        <?php } else { ?>
            getSalesKontrak();
            changeStatus()
        <?php } ?>

        // Sales Kontrak
        $('.sales_kontrak').select2({
            placeholder: "Pilih Sales Kontrak",
            theme: "bootstrap-5"
        }).change(function() {
            listBarang = [];
            let customer = $(this).find(':selected').data('customer');
            let customerPO = $(this).find(':selected').data('customer-po-no');
            let loadingPort = $(this).find(':selected').data('loading-port');
            let dichargePort = $(this).find(':selected').data('dicharge-port');
            let dueDate = $(this).find(':selected').data('due-date');
            let paymentTerm = $(this).find(':selected').data('payment-term');
            let tolerance = $(this).find(':selected').data('tolerance');
            let shipmentDate = $(this).find(':selected').data('shipment-date');
            let currency = $(this).find(':selected').data('currency');
            let tipeHarga = $(this).find(':selected').data('tipe-harga');
            let potonganHarga = $(this).find(':selected').data('potongan-harga');
            let keterangan = $(this).find(':selected').data('keterangan');

            // console.log(customer);

            $(".customer_name").val(customer);
            $(".customer_po_no").val(customerPO);
            $(".loading_port").val(loadingPort);
            $(".dicharge_port").val(dichargePort);
            $(".due_date").val(dueDate);
            $(".payment_term").val(paymentTerm);
            $(".tolerance").val(tolerance);
            $(".shipment_date").val(shipmentDate);
            $(".currency").val(currency);
            $(".tipe_harga").val(tipeHarga);
            $(".potongan_harga").val(potonganHarga);
            $(".keterangan").val(keterangan);

            getDetailSalesKontrak($(this).val());
        });
        //CSS SELECT2 FLOATING LABEL
        $('.sales_kontrak')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.sales_kontrak')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.sales_kontrak')
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
                sales_kontrak: {
                    required: true
                }
            },
            messages: {
                no_sales_order: {
                    required: "No sales order wajib diisi"
                },
                sales_kontrak: {
                    required: "Sales kontrak wajib diisi"
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
                console.log(listBarang);
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
                                                window.location.href = "<?= base_url("order-form-internasional/id/") ?>" + id; // Mengarahkan ke URL dengan menambahkan id ke belakangnya
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
                                                window.location.href = "<?= base_url("order-form-internasional/id/") ?>" + id; // Mengarahkan ke URL dengan menambahkan id ke belakangnya
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
                console.log(res);
                $(".sales_kontrak").empty();

                $(".sales_kontrak").append(`<option value=""></option>`);

                res.data.forEach(function(item) {
                    $(".sales_kontrak").append(`<option data-customer="${item.customerName}" data-customer-po-no="${item.customer_po_no}" data-loading-port="${item.loading_port}" data-dicharge-port="${item.dicharge_port}" data-due-date="${item.due_date}" data-payment-term="${item.payment_term}" data-tolerance="${item.tolerance}" data-shipment-date="${item.shipment_date}" data-currency="${item.currencyName}" data-tipe-harga="${item.tipe_harga}" data-potongan-harga="${item.potongan_harga}" data-keterangan="${item.keterangan}" value="${item.id}">${item.sales_contract_no}</option>`);
                })

                $(".sales_kontrak").val("").change();
                stopLoading();
            }
        })
    }

    function getDetailSalesKontrak(id) {
        setLoading();
        $.ajax({
            url: `<?= base_url("order-form-internasional/get/detail-sales-kontrak"); ?>`,
            method: "GET",
            dataType: "json",
            data: {
                id: id
            }, // Memasukkan id ke dalam data yang dikirim
            success: function(res) {
                if (res.status) {
                    res.data.forEach(function(item) {
                        console.log(item);
                        listBarang.push({
                            barang_master_sales_id: item.barang_master_sales_id,
                            barang_name: item.barang_name,
                            harga: item.harga,
                            id_detail: item.id_detail,
                            kemasan: item.kemasan,
                            kode_barang: item.kode_barang,
                            qty: item.qty,
                            qty_awal: item.qty_awal,
                            remark: item.remark,
                            satuan_order_id: item.satuan_order_id,
                            satuan_order_name: item.satuan_order_name,
                            total: item.total,
                            qtyOrder: item.qty,
                            hargaOrder: item.harga,
                            totalHargaOrder: item.total,
                        });
                    });
                    stopLoading();
                } else {
                    console.error("Failed to fetch data");
                }
                drawTable();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function drawTable() {
        $('.body-detail-table').empty();
        $('.tfoot').empty();
        var row = '';
        var no = 1;
        const table = $('.dataTable');
        if (listBarang.length === 0) {
            row += `
            <tr>
                <td colspan="5"></td>
                <td><b>TOTAL</b></td>
                <td><b>0.00</b></td>
                <td><b>0.00</b></td>
                <td><b>0.00</b></td>
                <td><b>0.00</b></td>
                <td><b>0.00</b></td>
                <td><b>0.00</b></td>
            </tr>
        `;
            $('.tfoot').append(row);
        } else {
            var totalQty = 0;
            var totalHarga = 0;
            var totalTotalHarga = 0;
            var totalQtyOrder = 0;
            var totalHargaOrder = 0;
            var totalTotalHargaOrder = 0;

            listBarang.map((item, index) => {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.kode_barang));
                newRow.append($('<td>').text(item.barang_name));
                newRow.append($('<td>').text(item.satuan_order_name));
                newRow.append($('<td>').text(item.kemasan));
                newRow.append($('<td>').text(item.remark));
                newRow.append($('<td>').text(item.qty));
                newRow.append($('<td>').text(formatRupiah(item.harga)));
                newRow.append($('<td>').text(formatRupiah(item.total)));
                newRow.append($('<td>').html(`
                <input <?= isset($dataSalesExport) && $dataSalesExport->status === "POSTED" ? "readonly" : "" ?> class="form-control qty-barang-order" oninput="preventNegativeInput(this);updateOrder($(this))" autocomplete="one-time-code" class="form-control" type="text" data-index="${index}" value="${item.qtyOrder}">
            `));
                newRow.append($('<td>').text(formatRupiah(item.hargaOrder)));
                newRow.append($('<td>').text(formatRupiah(item.totalHargaOrder)));

                totalQty += parseFloat(item.qty);
                totalHarga += parseFloat(item.harga);
                totalTotalHarga += parseFloat(item.total);
                totalQtyOrder += parseFloat(item.qtyOrder);
                totalHargaOrder += parseFloat(item.hargaOrder);
                totalTotalHargaOrder += parseFloat(item.totalHargaOrder);

                table.find('tbody').append(newRow);
            });

            $('.body-detail-table').append(row);
            table.find('tfoot').empty();
            var newRow = $('<tr>');
            newRow.append($('<td colspan="5"></td>'));
            newRow.append($('<td><b>TOTAL</b></td>'));
            newRow.append($('<td><b>' + totalQty + '</b></td>'));
            newRow.append($('<td><b>' + formatRupiah(totalHarga.toString()) + '</b></td>'));
            newRow.append($('<td><b>' + formatRupiah(totalTotalHarga.toString()) + '</b></td>'));
            newRow.append($('<td><b>' + totalQtyOrder + '</b></td>'));
            newRow.append($('<td><b>' + formatRupiah(totalHargaOrder.toString()) + '</b></td>'));
            newRow.append($('<td><b>' + formatRupiah(totalTotalHargaOrder.toString()) + '</b></td>'));
            table.find('tfoot').append(newRow);

            totalAmount = totalTotalHarga;
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
        $('.dataTable tbody tr:eq(' + index + ') td:eq(10)').text(formatRupiah(hargaOrder.toString()));
        $('.dataTable tbody tr:eq(' + index + ') td:eq(11)').text(formatRupiah(totalHargaOrder.toString()));

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
        $('.foot-detail-table td:eq(6)').text(formatRupiah(totalHarga.toString()));
        $('.foot-detail-table td:eq(7)').text(formatRupiah(totalTotalHarga.toString()));
    }


    const print = function(url) {
        window.open(url, "_blank");
    }

    function formatRupiah(angka) {
        if (angka != "") {
            angka = angka.replace(/\./g, ',');
            angka = angka.replace(/[^\d,]/g, '');
            var parts = angka.split(',');
            var ribuan = parts[0];
            var desimal = parts[1] || '00';
            var reverse = ribuan.toString().split('').reverse().join('');
            var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
            return '' + ribuanFormatted + ',' + desimal;
        }

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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $.ajax({
                url: "<?= base_url("order-form-internasional/generate-no-order-form"); ?>",
                method: "POST",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    csrf.val(response.token);
                    $(".no_sales_order").val(response.data);
                },

            });
            $(".no_sales_order").attr("readonly", true);
        } else {
            $(".no_sales_order").attr("readonly", false);
            $(".no_sales_order").val("");
        }
    }
</script>
<?= $this->endSection(); ?>