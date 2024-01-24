<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-po-import"); ?>">
                Batal
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-form">
                Simpan
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <label class="form-label font-weight-bold lable-title mt-2 mb-3">
                Detail Pembayaran
            </label>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" value="<?= $paymentData->payment_no ?? '' ?>" placeholder="No. Pembayaran" disabled>
                                    <label for="floatingInput">No. Pembayaran (Auto Generate)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? "disabled" : "" ?> class="form-select" name="payment_type" id="payment_type">
                                <option value="" selected> Pilih Payment Type </option>
                                <option value="DP" <?= (!empty($paymentData) && $paymentData->payment_type == 'DP') ? 'selected' : '' ?>>DP</option>
                                <option value="Pelunasan" <?= (!empty($paymentData) && $paymentData->payment_type == 'Pelunasan') ? 'selected' : '' ?>>Pelunasan</option>
                            </select>
                            <label for="floatingInput">Payment Type</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? "disabled" : "" ?> class="form-select " name="po_type" id="po_type">
                                <option disabled selected value=""></option>
                                <option value="BAKU" <?= (!empty($paymentData) && $paymentData->po_type == 'BAKU') ? 'selected' : '' ?>>Baku</option>
                                <option value="PENOLONG" <?= (!empty($paymentData) && $paymentData->po_type == 'PENOLONG') ? 'selected' : '' ?>>Penolong</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">PO Type</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? "disabled" : "" ?> class="form-select " name="supplier_id" id="supplier">
                                <option disabled selected value=""></option>
                                <?php foreach ($supplierList ?? [] as $supplier) : ?>
                                    <option value="<?= $supplier->id ?>" <?= (!empty($paymentData) && $paymentData->supplier_id == $supplier->id) ? 'selected' : '' ?>><?= $supplier->name ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <?php if (!empty($poList)) : ?>
                                <input type="hidden" name="import_po" id="import_po" value="<?= $paymentData->po_id ?>">
                            <?php endif; ?>
                            <select class="form-select " name="import_po" id="import_po" <?= empty($poList) ?: "disabled" ?>>
                                <option selected value="">Pilih No PO Import</option>
                                <?php foreach ($poList ?? [] as $po) : ?>
                                    <option value="<?= $po->id ?>" <?= (!empty($paymentData) && $paymentData->po_id == $po->id) ? 'selected' : '' ?>><?= $po->po_no ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">PO Import</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                    <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                        <select class="form-select " name="import_lpb" id="import_lpb" disabled>
                            <option disabled selected value=""></option>
                            <?php foreach ($lpbList ?? [] as $lpb) : ?>
                            <option value="<?= $lpb->id ?>" <?= (!empty($paymentData) && $paymentData->penerimaan_barang_id == $lpb->id) ? 'selected' : '' ?>><?= $lpb->lpb_no ?></option>
                            <?php endforeach ?>
                        </select>
                        <label for="floatingInput" style="z-index: 1;">LPB Import</label>
                    </div>
                </div> -->
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="currency" value="<?= $paymentData->currency ?? '' ?>" placeholder="Currency" disabled>
                                    <label for="floatingInput">Currency</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" id="po_amt" name="po_amt" value="<?= number_format($mustPay ?? 0, 2, ',', '.')  ?>" disabled>
                            <label for="floatingInput">PO Amount (Sisa Pembayaran)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="payment_amt" <?= !empty($paymentData) ? "readonly" : "" ?> name="payment_amt" value="<?= "" . number_format($paymentData->payment_amt ?? 0, 2, ',', '.')  ?>" onchange="this.value = formatRupiah(this.value);">
                                    <label for="floatingInput">Payment Amount</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input onkeyup="this.value = this.value.replace(/[^0-9,]/g, '');" autocomplete="one-time-code" type="text" class="form-control" id="current_exchange_rate" <?= !empty($paymentData) ? "readonly" : "" ?> name="current_exchange_rate" value="<?= "" . number_format($paymentData->current_exchange_rate ??  0, 2, ',', '.')  ?>" onchange="this.value = formatRupiah(this.value);">
                            <label for="floatingInput">Kurs saat ini</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control" id="payment_date" <?= !empty($paymentData) ? "readonly" : "" ?> name="payment_date" value="<?= $paymentData->payment_date ?? '' ?>" placeholder="Payment Date">
                                    <label for="floatingInput">Payment Date</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control" name="termin" <?= !empty($paymentData) ? "readonly" : "" ?> id="termin" value="<?= $paymentData->termin ?? '' ?>" placeholder="Termin">
                                    <label for="floatingInput">Termin</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select " <?= !empty($paymentData) ? "disabled" : "" ?> name="payment_method" id="payment_method">
                                <option selected value="">Pilih Payment Method</option>
                                <option value="CASH" <?= (!empty($paymentData) && $paymentData->payment_method == 'CASH') ? 'selected' : '' ?>>Cash</option>
                                <option value="TRANSFER" <?= (!empty($paymentData) && $paymentData->payment_method == 'TRANSFER') ? 'selected' : '' ?>>Transfer</option>
                                <option value="LC" <?= (!empty($paymentData) && $paymentData->payment_method == 'LC') ? 'selected' : '' ?>>LC</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Payment Method</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control" id="voucher_no" <?= !empty($paymentData) ? "readonly" : "" ?> name="voucher_no" value="<?= $paymentData->voucher_no ?? '' ?>" placeholder="No. Voucher">
                                    <label for="floatingInput">No. Voucher</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <textarea autocomplete="one-time-code" name="note" class="form-control information text-area-all" <?= !empty($paymentData) ? "readonly" : "" ?>><?= $paymentData->note ?? '' ?></textarea>
                            <label for="floatingInput">Note</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= session()->get("login")->name; ?>" type="text" readonly="true" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="detail-pembayaran">
                <label class="form-label font-weight-bold lable-title mt-2 mb-3">
                    Detail Barang
                </label><br>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi" id="detailBarang" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga Barang</th>
                                <th>Jumlah Diterima</th>
                                <th>Total Harga</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table">
                        </tbody>
                    </table>
                </div>
                <label class="form-label font-weight-bold lable-title mt-2 mb-3">
                    Riwayat Pembayaran
                </label>
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi" id="riwayatBayar" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th>No.</th>
                                <th>No Pembayaran</th>
                                <th>Tanggal Bayar</th>
                                <th>Termin</th>
                                <th>Payment Method</th>
                                <th>Payment Type</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);

        $('.detail-pembayaran').hide();

        var validator = $(".create-form").validate({
            rules: {
                no_bukti_pembayaran: {
                    required: true
                },
                "multiple_faktur_id[]": {
                    required: true
                },
                nominal_faktur: {
                    required: true
                },
                due_date: {
                    required: "Tanggal Pembayaran wajib diisi"
                }
            },
            messages: {
                no_bukti_pembayaran: {
                    required: "No. Pembayaran wajib diisi"
                },
                "multiple_faktur_id[]": {
                    required: "No. Terima Faktur wajib diisi"
                },
                nominal_faktur: {
                    required: "Nominal Faktur wajib diisi"
                },
                due_date: {
                    required: "Tanggal Pembayaran wajib diisi"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
                var elem = $(element);
                console.log(elem);
                if (elem.hasClass("multiple_po_id")) {
                    element = $(".select2-selection--multiple").parent();
                    error.insertAfter(element);
                } else if (elem.hasClass("select2-hidden-accessible")) {
                    element = $("#select2-" + elem.attr("id") + "-container").parent();
                    error.insertAfter(element);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).closest('.col-md-6').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.col-md-6').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $("#payment_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('#po_type').select2({
            placeholder: "Pilih PO Type",
            theme: "bootstrap-5"
        }).change(function(e) {});

        $('#supplier').select2({
            placeholder: "Pilih Nama Supplier",
            theme: "bootstrap-5"
        }).change(function(e) {
            const supplierId = $(this).val();
            const paymentType = $('#payment_type').val();
            console.log("Haha");
            if (paymentType == 'DP') {
                gokilDP(supplierId);
            } else {
                console.log('hahaha')
                gokilPelunasan(supplierId);
            }
        });

        function gokilDP(supplierId) {
            const url = ($('#po_type').val() == 'BAKU') ? '<?= base_url('/po-import-bahan-baku/payment-dropdown/'); ?>' : '<?= base_url('/po-import-bahan-penolong/payment-dropdown/'); ?>';

            $("#import_po").empty();
            $("#import_po").select2({
                placeholder: "Pilih Nomor PO",
                theme: "bootstrap-5",
                ajax: {
                    url: `${url}${supplierId}`,
                    dataType: 'json',
                    processResults: function(res) {
                        return {
                            results: $.map(res.data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.po_no,
                                    amount: +item.total,
                                    currency: item.currency
                                }
                            })
                        };
                    }
                },
                templateSelection: function(container) {
                    $(container.element).attr("data-amount", container.amount);
                    $(container.element).attr("data-currency", container.currency);
                    return container.text;
                }
            });
        }

        function gokilPelunasan(supplierId) {
            $("#import_lpb").empty();
            $("#import_lpb").select2({
                // placeholder: "Pilih Bro",
                theme: "bootstrap-5",
                ajax: {
                    url: `<?= base_url('/penerimaan-barang-import/receivedItemsBySupplier/') ?>${supplierId}`,
                    dataType: 'json',
                    processResults: function(res) {
                        console.log('whyyyyyyyyyyyyyyyyyyyy')
                        return {
                            results: $.map(res.data, function(item) {
                                console.log('heheheheheheheh')
                                return {
                                    id: item.id,
                                    text: item.lpb_no,
                                    amount: +item.total,
                                    currency: item.currency
                                }
                            })
                        };
                    }
                },
                templateSelection: function(container) {
                    $(container.element).attr("data-amount", container.amount);
                    $(container.element).attr("data-currency", container.currency);
                    return container.text;
                }
            });
        }

        $('#import_po, #import_lpb').select2({
            placeholder: "Pilih Nomor PO Import",
            theme: "bootstrap-5"
        }).change(function() {
            const attr = $(this).select2('data');
            const amount = attr[0]?.amount;
            const currency = attr[0]?.currency;

            $('#po_amt').val(formatRupiah(amount || 0));
            $('#currency').val(currency);
        });

        $(".btn-submit-form").click(function() {
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
                        const paymentAmt = $('#payment_amt').val().replace(/\,/g, '');
                        const currentExchangeRate = $('#current_exchange_rate').val().replace(/\,/g, '');
                        const data = $(".create-form").serializeArray();
                        data.push({
                            name: 'payment_amt',
                            value: paymentAmt
                        });
                        data.push({
                            name: 'current_exchange_rate',
                            value: currentExchangeRate
                        });

                        $.ajax({
                            url: "<?= base_url("pembayaran-po-import/create"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            method: "POST",
                            dataType: "json",
                            success: function(response) {
                                csrf.val(response.token);
                                if (response.status) {
                                    stopLoading()
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            window.location.href = `<?= base_url("pembayaran-po-import"); ?>/${response.id}`;
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
                })
            }
        })
    })

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".no_bukti_pembayaran").attr("readonly", true);
            $(".no_bukti_pembayaran").val("AUTO GENERATE");
        } else {
            $(".no_bukti_pembayaran").attr("readonly", false);
            $(".no_bukti_pembayaran").val("");
        }
    }

    $('#import_po').on('change', function() {
        var idPO = $(this).val();
        generateBarangByPo(idPO);
    });

    generateBarangByPo($('#import_po').val());



    function generateBarangByPo(id) {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        console.log(id);
        var formData = new FormData();
        formData.append("id", id);
        $.ajax({
            url: "<?= base_url("pembayaran-po-import/get-po"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                if (response.status) {
                    console.log(response);
                    $('.detail-pembayaran').show();
                    $('#detailBarang').find('tbody').empty();
                    var no = 1;
                    var totalHarga = 0;
                    if (response.dataPOImportDetail.length == 0) {
                        var newRow = $('<tr>');
                        newRow.append($('<td colspan="8" align="center" style="font-weight:normal;">').text("Detail barang tidak ditemukan"));
                        $('#detailBarang').append(newRow);
                    } else {
                        $.each(response.dataPOImportDetail, function(i, v) {
                            var newRow = $('<tr align="center">');
                            newRow.append($('<td>').text(no++));
                            newRow.append($('<td>').text(v.kode_barang));
                            newRow.append($('<td>').text(v.nama_barang));
                            newRow.append($('<td>').text(formatRupiah(v.price)));
                            newRow.append($('<td>').text(v.qty_diterima));
                            newRow.append($('<td>').text(formatRupiah(v.totalPriceWithoutAdditional)));
                            $('#detailBarang').append(newRow);
                        });
                        var newRow = $('<tr>');
                        newRow.append($('<td colspan="5" align="right" style="font-weight:bold;">').text("Total Harga Perlu Dibayar"));
                        newRow.append($('<td colspan="1" align="center" style="font-weight:bold;">').text(formatRupiah(response.dataPOImportHargaFinal)));
                        $('#detailBarang').append(newRow);
                    }

                    var no = 1;
                    var totalbayar = 0;
                    if (response.importPoPaymentRiwayat.length == 0) {
                        var newRow = $('<tr>');
                        newRow.append($('<td colspan="8" align="center" style="font-weight:normal;">').text("Riwayat Pembayaran Tidak Ada"));
                        $('#riwayatBayar').append(newRow);
                    } else {
                        $.each(response.importPoPaymentRiwayat, function(i, v) {
                            var newRow = $('<tr align="center">');
                            var splitDate = v.payment_date.split('-');
                            var dateFormated = splitDate[2] + '/' + splitDate[1] + '/' + splitDate[0];

                            newRow.append($('<td>').text(no++));
                            newRow.append($('<td>').text(v.payment_no));
                            newRow.append($('<td>').text(dateFormated));
                            newRow.append($('<td>').text(v.termin));
                            newRow.append($('<td>').text(v.payment_method));
                            newRow.append($('<td>').text(v.payment_type));
                            newRow.append($('<td>').text(formatRupiah(v.payment_amt)));

                            $('#riwayatBayar').append(newRow);
                        });
                        var newRow = $('<tr>');
                        newRow.append($('<td colspan="6" align="right" style="font-weight:bold;">').text("Total Sudah Dibayar"));
                        newRow.append($('<td colspan="1" align="center" style="font-weight:bold;">').text(formatRupiah(response.totalPay)));
                        $('#riwayatBayar').append(newRow);
                    }
                }
            },
            onError: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan pada sistem',
                    confirmButtonColor: '#4e73df',
                });
            }
        });
    }
</script>
<script>
    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join('.').split('').reverse().join('');
        return '' + formatted;
    }
</script>

<?= $this->endSection(); ?>