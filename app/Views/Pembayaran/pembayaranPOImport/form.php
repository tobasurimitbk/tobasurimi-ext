<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

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
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" <?= !empty($paymentData) ? 'readonly' : '' ?> class="form-control no_pembayaran" id="no_pembayaran" name="no_pembayaran" placeholder="No. Pembayaran" required <?= !empty($paymentData) ? 'disabled value="' . $paymentData['payment_no'] . '"' : '' ?>>
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 25px; margin-left: -30px; <?= !empty($paymentData) ? 'display:none;' : '' ?>" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? "disabled" : "" ?> class="form-select tipe_pembayaran" name="tipe_pembayaran" id="tipe_pembayaran">
                                <option disabled selected value=""></option>
                                <option value="DP" <?= (!empty($paymentData) && $paymentData['payment_type'] == 'DP') ? 'selected' : '' ?>>DP</option>
                                <option value="PELUNASAN" <?= (!empty($paymentData) && $paymentData['payment_type'] == 'PELUNASAN') ? 'selected' : '' ?>>PELUNASAN</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? "disabled" : "" ?> class="form-select " name="po_type" id="po_type">
                                <option disabled selected value=""></option>
                                <option value="BAHAN BAKU" <?= (!empty($paymentData) && $paymentData['po_type'] == 'BAHAN BAKU') ? 'selected' : '' ?>>BAHAN BAKU</option>
                                <option value="BAHAN PENOLONG" <?= (!empty($paymentData) && $paymentData['po_type'] == 'BAHAN PENOLONG') ? 'selected' : '' ?>>BAHAN PENOLONG</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Purchase Order</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? "disabled" : "" ?> class="form-select " name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($supplierList ?? [] as $supplier) : ?>
                                    <option value="<?= $supplier->id ?>" <?= (!empty($paymentData) && $paymentData['supplier_id'] == $supplier->id) ? 'selected' : '' ?>><?= $supplier->name ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <?php if (!empty($paymentData)) : ?>
                                <select class="form-select " name="import_po" id="import_po" <?= empty($paymentData) ?: "disabled" ?>>
                                    <option selected value="<?= encrypt($poDetail->id) ?>"><?= $poDetail->po_no ?></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">PO Import</label>
                            <?php else : ?>
                                <select class="form-select " name="import_po" id="import_po" <?= empty($paymentData) ?: "disabled" ?>>
                                    <option selected value="">Pilih No PO Import</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">PO Import</label>
                            <?php endif;  ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" name="currency" readonly class="form-control" id="currency" value="<?= $paymentData['currency'] ?? '' ?>" placeholder="Currency">
                                    <label for="floatingInput">Valas</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control" id="payment_amt" <?= !empty($paymentData) ? "readonly" : "" ?> name="payment_amt" value="<?= "" . number_format($paymentData['payment_amt'] ?? 0, 2, ',', '.')  ?>">
                                    <label for="floatingInput">Total Bayar</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control" id="sisa_bayar" name="sisa_bayar" value="<?= number_format($sisaBayar ?? 0, 2, ',', '.')  ?>" disabled>
                            <label for="floatingInput">Sisa Bayar</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control" id="current_exchange_rate" <?= !empty($paymentData) ? "readonly" : "" ?> name="current_exchange_rate" value="<?= "" . number_format($paymentData['current_exchange_rate'] ??  0, 2, ',', '.')  ?>">
                            <label for="floatingInput">Kurs saat ini</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" name="payment_date" type="text" <?= !empty($paymentData) ? 'readonly' : '' ?> value="<?= !empty($paymentData) ? date('d/m/Y', strtotime($paymentData['payment_date'])) : '' ?>" class="form-control payment_date" id="payment_date">
                                <label>Tanggal Pembayaran</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control" name="termin" <?= !empty($paymentData) ? "readonly" : "" ?> id="termin" value="<?= $paymentData->termin ?? '-' ?>" placeholder="Termin">
                                    <label for="floatingInput">Termin Pembayaran</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select " <?= !empty($paymentData) ? "disabled" : "" ?> name="payment_method" id="payment_method">
                                <option selected value="">Pilih Payment Method</option>
                                <option value="CASH" <?= (!empty($paymentData) && $paymentData['payment_method'] == 'CASH') ? 'selected' : '' ?>>Cash</option>
                                <option value="TRANSFER" <?= (!empty($paymentData) && $paymentData['payment_method'] == 'TRANSFER') ? 'selected' : '' ?>>Transfer</option>
                                <option value="LC" <?= (!empty($paymentData) && $paymentData['payment_method'] == 'LC') ? 'selected' : '' ?>>LC</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="akun_kas" id="akun_kas">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_kas'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="akun_selisih" id="akun_selisih">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_selisih'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit (Opsional)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control" id="voucher_no" <?= !empty($paymentData) ? "readonly" : "" ?> name="voucher_no" value="<?= $paymentData->voucher_no ?? '-' ?>" placeholder="No. Voucher">
                                    <label for="floatingInput">No. Voucher</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" value="<?= session()->get("login")->name; ?>" type="text" readonly name="pembayaran_oleh" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea autocomplete="one-time-code" name="note" class="form-control information text-area-all" <?= !empty($paymentData) ? "readonly" : "" ?>><?= $paymentData->note ?? '-' ?></textarea>
                            <label for="floatingInput">Note</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-sm mt-1">
                    <label class="form-label font-weight-bold lable-title mt-2 mb-3">
                        Purchase Order List
                    </label>
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi" id="detailBarang" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr style="text-align: center;">
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Qty Order</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm mt-1">
                    <label class="form-label font-weight-bold lable-title mt-2 mb-3">
                        Riwayat Pembayaran
                    </label>
                    <table class="table table-bordered nowrap table-hover-tobasurimi" id="riwayatBayar" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr style="text-align: center;">
                                <th>No Pembayaran</th>
                                <th>Tanggal Bayar</th>
                                <th>Payment Method</th>
                                <th>Total Bayar</th>
                                <th>Sisa Bayar</th>
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
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $("#payment_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#import_po').select2({
        placeholder: "Pilih Nomor PO Import",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selectedOptionData = $(this).find(':selected');
        var totalBayar = selectedOptionData.data('total_bayar');
        var sisaBayar = selectedOptionData.data('sisa_bayar');
        var valas = selectedOptionData.data('valas');
        var kurs = selectedOptionData.data('kurs');

        $('#currency').val(valas);
        $('#payment_amt').val(sisaBayar);
        $('#sisa_bayar').val(sisaBayar);
        $('#current_exchange_rate').val(kurs);
        detailBarang.ajax.reload();
        riwayatBayar.ajax.reload();
        console.log('Test');
    });

    $('#tipe_pembayaran').select2({
        placeholder: "Pilih Tipe Pembayaran",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#payment_method').select2({
        placeholder: "Pilih Metode Pembayaran",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#po_type').select2({
        placeholder: "Pilih Tipe Purchase Order",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Nama Supplier",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#akun_kas, #akun_selisih').select2({
        placeholder: "",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#supplier_id, #po_type').change(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("po_type", $('#po_type').val());
        formData.append("supplier_id", $('#supplier_id').val());

        $.ajax({
            url: "<?= base_url("pembayaran-po-import/po-belum-lunas"); ?>",
            data: formData,
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
                $('#import_po').empty();
                var option = $('<option>', {
                    value: "",
                    text: ""
                });
                $('#import_po').append(option);
                $.each(response.data, function(i, v) {
                    var option = $('<option>', {
                        value: v.id,
                        text: v.po_no
                    });
                    option.data('total_bayar', v.total_bayar);
                    option.data('sisa_bayar', v.sisa_bayar);
                    option.data('valas', v.valas);
                    option.data('kurs', v.kurs);
                    $('#import_po').append(option);
                });
            },

        });
    });


    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-6px');


    const detailBarang = $('#detailBarang').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: false,
        order: [
            [1, 'asc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("pembayaran-po-import/all-po"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.po_id = $("#import_po").val();
                data.po_type = $("#po_type").val();
                data.sort = "id";
                data.sortType = "DESC";
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "kode_barang",
                className: "text-center"
            },
            {
                data: "nama_barang",
                className: "text-center"
            },
            {
                data: "qty_order",
                className: "text-center"
            },
            {
                data: "total_harga",
                className: "text-center"
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    const riwayatBayar = $('#riwayatBayar').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: false,
        order: [
            [1, 'asc']
        ],

        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("pembayaran-po-import/all-riwayat-pembayaran"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.po_id = $("#import_po").val();
                data.po_type = $("#po_type").val();
                data.sort = "id";
                data.sortType = "DESC";
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no_pembayaran",
                className: "text-center"
            },
            {
                data: "tanggal_bayar",
                className: "text-center"
            },
            {
                data: "payment_method",
                className: "text-center"
            },
            {
                data: "total_bayar",
                className: "text-center"
            },
            {
                data: "sisa_bayar",
                className: "text-center"
            },
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    var validator = $(".create-form").validate({
        rules: {
            no_pembayaran: {
                required: true
            },
            tipe_pembayaran: {
                required: true
            },
            po_type: {
                required: true
            },
            supplier_id: {
                required: true
            },
            import_po: {
                required: true
            },
            currency: {
                required: true
            },
            payment_amt: {
                required: true
            },
            sisa_bayar: {
                required: true
            },
            current_exchange_rate: {
                required: true
            },
            payment_date: {
                required: true
            },
            termin: {
                required: true
            },
            payment_method: {
                required: true
            },
            voucher_no: {
                required: true
            },
            pembayaran_oleh: {
                required: true
            },
            note: {
                required: true
            },
        },
        messages: {
            no_pembayaran: {
                required: "No pembayaran wajib diisi"
            },
            tipe_pembayaran: {
                required: "Pilih tipe pembayaran"
            },
            po_type: {
                required: "Pilih tipe purchase order"
            },
            supplier_id: {
                required: "Pilih supplier"
            },
            import_po: {
                required: "Pilih nomor purchase order"
            },
            currency: {
                required: "Valas wajib diisi"
            },
            payment_amt: {
                required: "Total bayar wajib diisi"
            },
            sisa_bayar: {
                required: "Sisa bayar wajib diisi"
            },
            current_exchange_rate: {
                required: "Kurs saat ini wajib diisi"
            },
            payment_date: {
                required: "Tanggal pembayaran wajib diisi"
            },
            termin: {
                required: "Termin wajib diisi"
            },
            payment_method: {
                required: "Pilih metode pembayaran"
            },
            voucher_no: {
                required: "Nomor voucer wajib diisi"
            },
            pembayaran_oleh: {
                required: "Nama kasir wajib diisi"
            },
            note: {
                required: "Note wajib diisi"
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
                    const data = $(".create-form").serializeArray();
                    $.ajax({
                        url: "<?= base_url("pembayaran-po-import/create"); ?>",
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
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        window.location.href = `<?= base_url("pembayaran-po-import"); ?>/id/${response.id}`;
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
            })
        }
    })

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        if (value) {
            $.ajax({
                url: "<?= base_url("pembayaran-po-import/generate-no-pembayaran"); ?>",
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
                    $(".no_pembayaran").val(response.data);
                },

            });
            $(".no_pembayaran").attr("readonly", true);
        } else {
            $(".no_pembayaran").attr("readonly", false);
            $(".no_pembayaran").val("");
        }
    }
</script>

<?= $this->endSection(); ?>